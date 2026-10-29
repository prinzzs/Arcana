const assert = require("node:assert/strict");
const fs = require("node:fs");
const path = require("node:path");
const { chromium } = require("playwright");
const root = path.resolve(__dirname, "..");

function htmlFiles(directory) {
    return fs.readdirSync(directory, { withFileTypes: true }).flatMap(entry => {
        const file = path.join(directory, entry.name);
        if (entry.isDirectory()) {
            return ["node_modules", ".git"].includes(entry.name) ? [] : htmlFiles(file);
        }
        return file.endsWith(".html") ? [file] : [];
    });
}

async function run() {
    const browser = await chromium.launch({
        headless: true,
        args: ["--no-sandbox", "--use-gl=angle", "--use-angle=swiftshader"],
        ...(process.env.CHROMIUM_EXECUTABLE_PATH
            ? { executablePath: process.env.CHROMIUM_EXECUTABLE_PATH } : {})
    });
    const context = await browser.newContext();
    const errors = [];
    const origin = "http://arcana.test";
    await context.route("**/*", async route => {
        const url = new URL(route.request().url());
        if (url.origin !== origin) return route.abort();
        const file = path.resolve(root, "." + decodeURIComponent(url.pathname));
        if (!file.startsWith(root + path.sep) || !fs.existsSync(file)) {
            errors.push("Arquivo ausente: " + url.pathname);
            return route.fulfill({ status: 404, body: "Not found" });
        }
        const types = {
            ".html": "text/html", ".js": "text/javascript", ".css": "text/css",
            ".png": "image/png", ".jpg": "image/jpeg", ".otf": "font/otf"
        };
        await route.fulfill({
            contentType: types[path.extname(file)] || "application/octet-stream",
            body: fs.readFileSync(file)
        });
    });
    const page = await context.newPage();
    page.on("pageerror", error => errors.push(error.message));

    for (const file of htmlFiles(root)) {
        const source = fs.readFileSync(file, "utf8");
        assert(!source.includes("<?"), file + " contém código de servidor");
        assert(!source.includes(".php"), file + " contém link antigo");
        await page.goto(origin + "/" + path.relative(root, file), { waitUntil: "networkidle" });
        for (const target of await page.locator("a[href]").evaluateAll(links =>
            links.map(link => link.getAttribute("href")))) {
            if (/^(https?:|mailto:|tel:|#)/.test(target)) continue;
            const destination = path.resolve(path.dirname(file), target.split(/[?#]/)[0]);
            assert(fs.existsSync(destination), "Link quebrado: " + file + " → " + target);
        }
    }

    await page.goto(origin + "/cadastro.html");
    await page.locator('[name="name"]').fill("Mestre");
    await page.locator('[name="email"]').fill("mestre@example.test");
    await page.locator("form button").click();
    await page.waitForURL("**/campanhas.html");
    await page.locator('#create [name="name"]').fill("A torre");
    await page.locator("#create button").click();
    await page.locator("#room").waitFor({ state: "visible" });
    const code = (await page.locator("#invite").textContent()).replace("Código local: ", "");

    await page.locator('[data-tab="shield"]').click();
    await page.locator('#shieldForm textarea').fill("Segredo do mestre");
    await page.locator("#shieldForm button").click();
    await page.reload();
    await page.locator("#campaigns button").click();
    await page.locator('[data-tab="shield"]').click();
    assert.equal(await page.locator("#shieldForm textarea").inputValue(), "Segredo do mestre");

    await page.goto(origin + "/cadastro.html");
    await page.locator('[name="name"]').fill("Jogador");
    await page.locator('[name="email"]').fill("jogador@example.test");
    await page.locator("form button").click();
    await page.waitForURL("**/campanhas.html");
    await page.locator('#join [name="code"]').fill(code);
    await page.locator("#join button").click();
    assert(await page.locator("#shieldTab").isHidden());
    await page.locator('[data-tab="characters"]').click();
    await page.locator('#character [name="name"]').fill("Lyra");
    await page.locator('#character textarea').fill("Espada e cura");
    await page.locator('#character button[type="submit"], #character button:not([type])').click();
    await page.locator("#sheets button").waitFor();
    const master = await page.locator("#activeProfile option").evaluateAll(options =>
        options.find(option => option.textContent === "Mestre").value);
    await page.locator("#activeProfile").selectOption(master);
    await page.locator("#campaigns button").click();
    await page.locator('[data-tab="characters"]').click();
    await page.locator("#sheets button").click();
    await page.locator('#character [name="hp"]').fill("7");
    await page.locator('#character button:not([type])').click();
    assert((await page.locator("#sheets").textContent()).includes("7 PV"));

    await page.goto(origin + "/dado.html");
    for (const sides of [4, 6, 8, 10, 12, 20, 100]) {
        await page.getByRole("button", { name: "D" + sides, exact: true }).click();
        await page.locator(".roll").click();
        await page.waitForFunction(() => !document.querySelector(".roll").disabled);
        const total = Number(await page.locator(".die").textContent());
        assert(total >= 1 && total <= sides);
    }
    await page.setViewportSize({ width: 390, height: 844 });
    await page.goto(origin + "/campanhas.html");
    assert(await page.evaluate(() =>
        document.documentElement.scrollWidth <= window.innerWidth + 1));
    await page.locator("#campaigns button").click();
    await page.locator('[data-tab="characters"]').click();
    assert(await page.evaluate(() =>
        document.documentElement.scrollWidth <= window.innerWidth + 1));
    await page.locator("#lightModeToggle").click();
    const theme = await page.locator("html").getAttribute("data-theme");
    await page.reload();
    assert.equal(await page.locator("html").getAttribute("data-theme"), theme);

    await page.goto(origin + "/conversas.html");
    await page.locator("#options button").first().click();
    await page.locator("#options button").nth(2).click();
    assert((await page.locator("#chat").textContent()).includes("Você rolou"));
    await page.locator("#options button").click();
    assert.equal(await page.locator("#options button").count(), 3);

    await page.goto(origin + "/painelAdm.html");
    await page.locator("#search").fill("jogador");
    assert.equal(await page.locator(".profile-row").count(), 1);
    await page.getByRole("link", { name: "Editar", exact: true }).click();
    await page.locator('[name="name"]').fill("Jogador atualizado");
    await page.locator("form button").click();
    await page.goto(origin + "/painelAdm.html");
    await page.locator("#search").fill("atualizado");
    await page.getByRole("link", { name: "Excluir", exact: true }).click();
    await page.locator("#delete-user").click();
    await page.waitForURL("**/painelAdm.html");
    assert.equal(await page.locator(".profile-row").count(), 1);
    assert.deepEqual(errors, []);
    await browser.close();
    console.log("Páginas, links, perfis, campanhas, fichas, escudo, dados e tela móvel: OK");
}
run().catch(error => {
    console.error(error);
    process.exit(1);
});
