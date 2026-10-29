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
    const context = await browser.newContext({locale:"pt-BR",reducedMotion:"reduce"});
    const errors = [];
    await context.route("**/*", async route => {
        const url = new URL(route.request().url());
        if (url.origin !== origin) return route.abort();
        const file = path.resolve(root, "." + decodeURIComponent(url.pathname));
        if (!file.startsWith(root + path.sep) || !fs.existsSync(file)) {
            errors.push("Recurso ausente: " + url.pathname);
            return route.fulfill({status:404,body:"Not found"});
        }
        const types = {".html":"text/html",".js":"text/javascript",".css":"text/css",".otf":"font/otf",".jpg":"image/jpeg",".png":"image/png",".svg":"image/svg+xml"};
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
    // Um visitante conhece Arcana, cria o perfil e retorna à campanha-modelo.
    await page.goto(origin+"/arcana.html");
    await page.getByRole("link",{name:"Criar campanha de Arcana ↗",exact:true}).click();
    assert(await page.locator("#profile-gate").isVisible());
    assert(await page.locator("#save-campaign").isDisabled());
    await page.locator("#create-profile").click();
    await page.locator('[name="name"]').fill("Mestre");
    await page.locator('[name="email"]').fill("mestre@example.test");
    await page.locator("form button").click();
    await page.waitForURL("**/criar-campanha.html?modelo=arcana");
    assert.equal(await page.locator('[name="system"]').inputValue(),"Arcana");
    assert((await page.locator('[name="synopsis"]').inputValue()).includes("Blecaute"));
    await page.locator('[name="name"]').fill("Os ecos do Submundo");
    await page.locator("#save-campaign").click();
    await page.waitForURL("**/preparar-campanha.html?*");
    const campaignId = new URL(page.url()).searchParams.get("campanha");
    const query = "?campanha=" + encodeURIComponent(campaignId);
    await page.locator('[name="objective"]').fill("Descobrir a origem do sinal.");
    await page.locator('[name="rules"]').fill("Combine os testes com o mestre.\nToda pista tem um custo.");
    await page.locator('[name="safety"]').fill("Pausas a qualquer momento.");
    await page.locator('[name="nextSession"]').fill("2026-11-07T19:30");
    await page.locator('[name="location"]').fill("Discord · sala Arcana");
    await page.locator('[name="startingHp"]').fill("32");
    await page.locator('[name="startingLevel"]').fill("3");
    await page.locator('[name="startingInventory"]').fill("Lanterna e comunicador");
    await page.locator('[name="maxPlayers"]').fill("1");
    await page.locator("#campaign-form button").click();
    assert((await page.locator("#form-feedback").textContent()).includes("salva"));
    await page.reload();
    assert.equal(await page.locator('[name="startingHp"]').inputValue(),"32");
    assert.equal(await page.locator('[name="nextSession"]').inputValue(),"2026-11-07T19:30");
    await page.goto(origin+"/mestre.html"+query);
    const code = await page.locator("#invite").textContent();
    const masterId = await page.evaluate(()=>JSON.parse(localStorage.getItem("arcana-local-v1")).active);
    await page.locator('#scene-form [name="title"]').fill("A porta vermelha");
    await page.locator('#scene-form [name="text"]').fill("Alguém chama do outro lado.");
    await page.locator("#scene-form button").click();
    assert.equal(await page.locator("#scene-state").textContent(),"Pronta");
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
    assert.equal(await page.locator('#world-nav a[href*="preparar"]').count(),0);
    assert((await page.locator("#campaign-meta").textContent()).includes("07/11/2026 às 19:30"));
    await page.goto(origin+"/guia-campanha.html"+query);
    assert.equal(await page.locator("#guide-objective").textContent(),"Descobrir a origem do sinal.");
    assert((await page.locator("#guide-rules").textContent()).includes("Toda pista tem um custo."));
    assert((await page.locator("#guide-starting").textContent()).includes("32 pontos de vida"));
    assert(await page.locator("#edit-preparation").isHidden());
    assert(!(await page.locator("body").textContent()).includes("O guardião está mentindo."));
    await page.goto(origin+"/preparar-campanha.html"+query);
    assert(await page.locator("#world-content").isHidden());
    assert.equal(await page.locator('[name="name"]').inputValue(),"");
    await page.goto(origin+"/escudo.html"+query);
    assert(await page.locator("#world-content").isHidden());
    assert.equal(await page.locator('[name="shield"]').inputValue(),"");
    await page.goto(origin+"/personagens.html"+query);
    assert.equal(await page.locator('[name="hp"]').inputValue(),"32");
    assert.equal(await page.locator('[name="level"]').inputValue(),"3");
    assert.equal(await page.locator('[name="inventory"]').inputValue(),"Lanterna e comunicador");
    assert((await page.locator('[name="body"]').inputValue()).includes("Percepção:"));
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

    // O limite impede um novo jogador, sem bloquear quem já está na mesa.
    await createProfile("Visitante","visitante@example.test");
    await page.locator('#join [name="code"]').fill(code);
    await page.locator("#join button").click();
    assert((await page.locator("#status").textContent()).includes("limite de jogadores"));
    await page.goto(origin+"/guia-campanha.html"+query);
    assert(await page.locator("#world-content").isHidden());

    await page.goto(origin+"/campanhas.html");
    await page.locator("#activeProfile").selectOption(masterId);
    await page.waitForLoadState();
    await page.locator(".campaign-card").click();
    await page.goto(origin+"/preparar-campanha.html"+query);
    await page.locator('[name="startingHp"]').fill("40");
    await page.locator("#campaign-form button").click();
    await page.goto(origin+"/personagens.html"+query);
    await page.locator("#sheets button").click();
    assert.equal(await page.locator('[name="hp"]').inputValue(),"32");
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
        for (const file of ["index","arcana","criar-campanha","preparar-campanha","guia-campanha","campanhas","mestre","escudo","personagens","diario","sessao"]) {
            await page.goto(origin+"/"+file+".html"+query);
            await page.locator("#lightModeToggle").click();
            for (const palette of ["purple","red"]) {
                await page.locator('[data-palette-choice="'+palette+'"]').click();
                for (const mode of ["light","dark"]) {
                    await page.locator('[data-choice="'+mode+'"]').click();
                    assert(await page.evaluate(()=>document.documentElement.scrollWidth <= innerWidth+1),file+" "+width+" "+palette+" "+mode);
                }
            }
            await page.keyboard.press("Escape");
            assert((await page.locator("h1").first().evaluate(el=>getComputedStyle(el).fontFamily)).includes("Nevan"));
            if (process.env.ARCANA_SCREENSHOTS && ["index","arcana","criar-campanha","preparar-campanha","guia-campanha","mestre","campanhas"].includes(file)) {
                fs.mkdirSync(process.env.ARCANA_SCREENSHOTS,{recursive:true});
                await page.screenshot({path:path.join(process.env.ARCANA_SCREENSHOTS,file+"-"+width+".png"),fullPage:true});
                await page.locator("#lightModeToggle").click();
                await page.locator('[data-choice="light"]').click();
                await page.keyboard.press("Escape");
                await page.screenshot({path:path.join(process.env.ARCANA_SCREENSHOTS,file+"-claro-"+width+".png"),fullPage:true});
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
    await page.locator("#new-character").click();
    assert.equal(await page.locator('[name="hp"]').inputValue(),"40");
    if (process.env.ARCANA_SCREENSHOTS) {
        await page.goto(origin+"/jogador.html"+query);
        await page.screenshot({path:path.join(process.env.ARCANA_SCREENSHOTS,"jogador.png"),fullPage:true});
    }

    // Campanhas de versões anteriores continuam acessíveis, sem reescrever fichas.
    await page.evaluate(() => {
        const data = JSON.parse(localStorage.getItem("arcana-local-v1"));
        data.campaigns.push({id:"legacy", name:"Campanha antiga", owner:data.active, members:[data.active],
            code:"LEGACY", characters:[{id:"old-sheet",owner:data.active,name:"Veterano",hp:7,level:2,body:"Ficha antiga"}],journal:"Diário antigo",shield:"Segredo antigo"});
        localStorage.setItem("arcana-local-v1",JSON.stringify(data));
    });
    await page.goto(origin+"/guia-campanha.html?campanha=legacy");
    assert.equal(await page.locator("#campaign-title").textContent(),"Campanha antiga");
    await page.goto(origin+"/personagens.html?campanha=legacy&ficha=old-sheet");
    assert.equal(await page.locator('[name="hp"]').inputValue(),"7");
    assert.equal(await page.locator('[name="body"]').inputValue(),"Ficha antiga");
    await page.goto(origin+"/preparar-campanha.html?campanha=legacy");
    await page.locator('[name="synopsis"]').fill('<img src=x onerror="alert(1)">');
    await page.locator("#campaign-form button").click();
    await page.goto(origin+"/guia-campanha.html?campanha=legacy");
    assert.equal(await page.locator("#guide-synopsis img").count(),0);
    assert((await page.locator("#guide-synopsis").textContent()).includes("<img"));
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
    console.log("Páginas, links, quatro temas, fontes, criação, preparação, guia, dados nas fichas, convites, legado, mestre/jogador e sessão: OK");
}
run().catch(error=>{console.error(error);process.exit(1)});
