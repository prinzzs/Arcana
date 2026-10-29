const assert = require("node:assert/strict");
const fs = require("node:fs");
const path = require("node:path");
const { chromium } = require("playwright");
const root = path.resolve(__dirname, "..");
const origin = "http://arcana.test";

async function run() {
    const browser = await chromium.launch({
        headless: true,
        args: ["--no-sandbox", "--use-gl=angle", "--use-angle=swiftshader"],
        ...(process.env.CHROMIUM_EXECUTABLE_PATH ? {executablePath:process.env.CHROMIUM_EXECUTABLE_PATH} : {})
    });
    const context = await browser.newContext();
    const errors = [];
    await context.route("**/*", async route => {
        const url = new URL(route.request().url());
        if (url.origin !== origin) return route.abort();
        const file = path.resolve(root, "." + decodeURIComponent(url.pathname));
        if (!file.startsWith(root + path.sep) || !fs.existsSync(file)) {
            errors.push("Recurso ausente: " + url.pathname);
            return route.fulfill({status:404,body:"Not found"});
        }
        const types = {".html":"text/html",".js":"text/javascript",".css":"text/css",".otf":"font/otf",".jpg":"image/jpeg",".png":"image/png"};
        return route.fulfill({contentType:types[path.extname(file)] || "application/octet-stream",body:fs.readFileSync(file)});
    });
    const page = await context.newPage();
    page.on("pageerror", e => errors.push(e.message));
    const pages = fs.readdirSync(root).filter(p=>p.endsWith(".html"))
        .concat(fs.readdirSync(path.join(root,"Entidades")).filter(p=>p.endsWith(".html")).map(p=>"Entidades/"+p));
    for (const file of pages) {
        await page.goto(origin + "/" + file);
        assert.equal(await page.locator("#lightModeToggle").count(),1);
        for (const href of await page.locator("a[href]").evaluateAll(links=>links.map(a=>a.getAttribute("href")))) {
            if (/^(https?:|mailto:|tel:|#)/.test(href)) continue;
            assert(fs.existsSync(path.resolve(path.dirname(path.join(root,file)),href.split(/[?#]/)[0])),file+" → "+href);
        }
    }

    // Confere a migração do vermelho antigo para vermelho escuro.
    await page.evaluate(() => { localStorage.setItem("arcana-theme","red"); localStorage.removeItem("arcana-palette"); });
    await page.reload();
    assert.equal(await page.locator("html").getAttribute("data-theme"),"red");
    for (const width of [390,1440]) {
        await page.setViewportSize({width,height:1000});
        await page.goto(origin+"/index.html");
        await page.locator("#lightModeToggle").click();
        for (const palette of ["purple","red"]) {
            await page.locator('[data-palette-choice="'+palette+'"]').click();
            for (const mode of ["light","dark"]) {
                await page.locator('[data-choice="'+mode+'"]').click();
                assert.equal(await page.locator("html").getAttribute("data-mode"),mode);
                assert.equal(await page.locator("html").getAttribute("data-palette"),palette);
                assert(await page.evaluate(()=>document.documentElement.scrollWidth <= innerWidth+1));
            }
        }
        await page.keyboard.press("Escape");
        assert(await page.locator("#themePanel").isHidden());
        assert.equal(await page.locator(".hero").evaluate(el=>getComputedStyle(el,"::before").borderRadius),"50%");
    }
    await page.reload();
    assert.equal(await page.locator("html").getAttribute("data-theme"),"red");

    // Dois perfis usam a mesma campanha local, com páginas e dados diferentes.
    async function createProfile(name,email) {
        await page.goto(origin+"/cadastro.html");
        await page.locator('[name="name"]').fill(name);
        await page.locator('[name="email"]').fill(email);
        await page.locator("form button").click();
        await page.waitForURL("**/campanhas.html");
    }
    await createProfile("Mestre","mestre@example.test");
    await page.locator('#create [name="name"]').fill("Os ecos do Submundo");
    await page.locator("#create button").click();
    await page.waitForURL("**/mestre.html?*");
    const campaignUrl = page.url();
    const query = new URL(campaignUrl).search;
    const code = await page.locator("#invite").textContent();
    const masterId = await page.evaluate(()=>JSON.parse(localStorage.getItem("arcana-local-v1")).active);
    await page.locator('#scene-form [name="title"]').fill("A porta vermelha");
    await page.locator('#scene-form [name="text"]').fill("Alguém chama do outro lado.");
    await page.locator("#scene-form button").click();
    await page.goto(origin+"/escudo.html"+query);
    await page.locator('[name="shield"]').fill("O guardião está mentindo.");
    await page.locator('[name="encounters"]').fill("Guardião da passagem");
    await page.locator('[name="reminders"]').fill("Perguntar quem carrega a chave.");
    await page.locator("#shield-form button").click();
    await page.reload();
    assert.equal(await page.locator('[name="shield"]').inputValue(),"O guardião está mentindo.");
    await page.goto(origin+"/diario.html"+query);
    await page.locator("textarea").fill("O grupo encontrou a chave.");
    await page.locator("#journal-form button").click();

    await createProfile("Jogador","jogador@example.test");
    const playerId = await page.evaluate(()=>JSON.parse(localStorage.getItem("arcana-local-v1")).active);
    await page.locator('#join [name="code"]').fill(code);
    await page.locator("#join button").click();
    await page.waitForURL("**/jogador.html?*");
    assert.equal(await page.locator("#scene-title").textContent(),"A porta vermelha");
    assert((await page.locator("#journal-preview").textContent()).includes("chave"));
    assert.equal(await page.locator('#world-nav a[href*="escudo"]').count(),0);
    await page.goto(origin+"/escudo.html"+query);
    assert(await page.locator("#world-content").isHidden());
    assert.equal(await page.locator('[name="shield"]').inputValue(),"");
    await page.goto(origin+"/personagens.html"+query);
    await page.locator('[name="name"]').fill("Lyra");
    await page.locator('[name="body"]').fill("Destreza 3");
    await page.locator('[name="inventory"]').fill("Uma chave antiga");
    await page.locator("#character-form button").click();
    assert((await page.locator("#sheets").textContent()).includes("Lyra"));
    await page.goto(origin+"/sessao.html"+query);
    assert(await page.locator("#private-roll-label").isHidden());
    await page.locator(".roll").click();
    await page.waitForFunction(()=>!document.querySelector(".roll").disabled);
    assert((await page.locator("#last-roll").textContent()).includes("Jogador"));

    await page.goto(origin+"/campanhas.html");
    await page.locator("#activeProfile").selectOption(masterId);
    await page.waitForLoadState();
    await page.locator(".campaign-card").click();
    await page.goto(origin+"/personagens.html"+query);
    await page.locator("#sheets button").click();
    await page.locator('[name="hp"]').fill("8");
    await page.locator("#character-form button").click();
    await page.goto(origin+"/sessao.html"+query);
    const publicRoll = await page.locator("#last-roll").textContent();
    await page.locator("#private-roll").check();
    await page.locator(".roll").click();
    await page.waitForFunction(()=>!document.querySelector(".roll").disabled);
    assert.equal(await page.locator("#last-roll").textContent(),publicRoll);

    // Inspeção responsiva das telas preenchidas e capturas para revisão.
    for (const width of [390,1440]) {
        await page.setViewportSize({width,height:1000});
        for (const file of ["campanhas","mestre","escudo","personagens","diario","sessao"]) {
            await page.goto(origin+"/"+file+".html"+query);
            assert(await page.evaluate(()=>document.documentElement.scrollWidth <= innerWidth+1),file+" "+width);
            if (process.env.ARCANA_SCREENSHOTS && width===1440 && ["mestre","escudo","campanhas"].includes(file)) {
                fs.mkdirSync(process.env.ARCANA_SCREENSHOTS,{recursive:true});
                await page.screenshot({path:path.join(process.env.ARCANA_SCREENSHOTS,file+".png"),fullPage:true});
                await page.locator("#lightModeToggle").click();
                await page.locator('[data-choice="light"]').click();
                await page.keyboard.press("Escape");
                await page.screenshot({path:path.join(process.env.ARCANA_SCREENSHOTS,file+"-claro.png"),fullPage:true});
                await page.locator("#lightModeToggle").click();
                await page.locator('[data-choice="dark"]').click();
                await page.keyboard.press("Escape");
            }
        }
    }
    await page.goto(origin+"/campanhas.html");
    await page.locator("#activeProfile").selectOption(playerId);
    await page.waitForLoadState();
    await page.goto(origin+"/personagens.html"+query);
    await page.locator("#sheets button").click();
    assert.equal(await page.locator('[name="hp"]').inputValue(),"8");
    assert.equal(await page.locator('[name="inventory"]').inputValue(),"Uma chave antiga");
    if (process.env.ARCANA_SCREENSHOTS) {
        await page.goto(origin+"/jogador.html"+query);
        await page.screenshot({path:path.join(process.env.ARCANA_SCREENSHOTS,"jogador.png"),fullPage:true});
    }

    await page.goto(origin+"/dado.html");
    for (const sides of [4,6,8,10,12,20,100]) {
        await page.getByRole("button",{name:"D"+sides,exact:true}).click();
        await page.locator(".roll").click();
        await page.waitForFunction(()=>!document.querySelector(".roll").disabled);
        const result = Number(await page.locator(".die").textContent());
        assert(result>=1 && result<=sides);
    }
    assert.deepEqual(errors,[]);
    await browser.close();
    console.log("Páginas, links, identidades, luminosidade, mestre/jogador, fichas, diário, escudo e sessão: OK");
}
run().catch(error=>{console.error(error);process.exit(1)});
