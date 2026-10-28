/* Testes de apresentação. PHP é substituído por dados de exemplo, não executado.
   Os fluxos de autenticação/banco precisam ser validados em PHP + MySQL. */
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const { chromium } = require('playwright');
const root = path.resolve(__dirname, '..');
const origin = 'http://arcana.test';
const pages = [
    'index.html', 'login.php', 'cadastro.php', 'loginAdm.php', 'cadastroAdm.php',
    'editarUsuario.php', 'edit.php', 'painelAdm.php', 'fichas.html', 'mecanica.html',
    'dado.html', 'conversas.html', 'paginaInicial.html', 'paginaInicial2.html',
    'Entidades/fichaNoxlith.html', 'Entidades/fichaRatos.html', 'Entidades/fichaSerafim.html'
];

function fixture(source) {
    return source
        .replace(/<\?=\s*htmlspecialchars\(\$(?:user|usuario)\['nome'\][\s\S]*?\?>/g, 'Jogadora de exemplo')
        .replace(/<\?=\s*htmlspecialchars\(\$(?:user|usuario)\['email'\][\s\S]*?\?>/g, 'jogadora@example.test')
        .replace(/<\?=\s*\$nome\s*\?>/g, 'Jogadora de exemplo')
        .replace(/<\?(?:php|=)[\s\S]*?\?>/g, '')
        .replace(/<div class="message\s*">\s*<\/div>/g, '')
        .replace(/<div class="message">\s*<p><\/p>\s*<\/div>/g, '')
        .replace(/<img src=""[^>]*>/g, '')
        .replace('<p>Nenhum usuário encontrado.</p>', '');
}

async function serve(context) {
    await context.route('**/*', async route => {
        const url = new URL(route.request().url());
        if (url.origin !== origin) return route.abort();
        if (url.pathname === '/no-controls.html') {
            return route.fulfill({ contentType: 'text/html', body: '<html><head><script src="/js/lightmode.js"></script></head><body>Sem controles</body></html>' });
        }
        const file = path.resolve(root, '.' + decodeURIComponent(url.pathname));
        if (!file.startsWith(root + path.sep) || !fs.existsSync(file)) {
            return route.fulfill({ status: 404, body: 'Not found' });
        }
        const ext = path.extname(file);
        const types = { '.php': 'text/html', '.js': 'text/javascript', '.css': 'text/css', '.jpg': 'image/jpeg', '.png': 'image/png', '.otf': 'font/otf' };
        return route.fulfill({ contentType: types[ext] || 'text/plain', body: ext === '.php' ? fixture(fs.readFileSync(file, 'utf8')) : fs.readFileSync(file) });
    });
}

async function checkTheme(page, expected) {
    assert.equal(await page.locator('html').getAttribute('data-theme'), expected);
    assert.equal(await page.locator('#lightModeToggle').getAttribute('aria-label'), expected === 'dark' ? 'Ativar modo claro' : 'Ativar modo escuro');
}

function contrast(a, b) {
    const luminance = hex => {
        const values = hex.replace('#', '').match(/../g).map(x => parseInt(x, 16) / 255).map(x => x <= .04045 ? x / 12.92 : ((x + .055) / 1.055) ** 2.4);
        return values[0] * .2126 + values[1] * .7152 + values[2] * .0722;
    };
    const values = [luminance(a), luminance(b)].sort((a, b) => b - a);
    return (values[0] + .05) / (values[1] + .05);
}

(async () => {
    const browser = await chromium.launch({ headless: true, args: ['--no-sandbox'], ...(process.env.CHROMIUM_EXECUTABLE_PATH ? { executablePath: process.env.CHROMIUM_EXECUTABLE_PATH } : {}) });
    try {
        let layouts = 0;
        for (const theme of ['light', 'dark']) {
            const context = await browser.newContext({ colorScheme: theme, reducedMotion: 'reduce' });
            await serve(context);
            const page = await context.newPage();
            const errors = [];
            page.on('pageerror', e => errors.push(e.message));
            page.on('response', r => { if (r.status() >= 400) errors.push(r.status() + ' ' + r.url()); });
            for (const width of [320, 390, 1440]) {
                await page.setViewportSize({ width, height: 900 });
                for (const name of pages) {
                    await page.goto(origin + '/' + name);
                    await page.evaluate(() => document.fonts.ready);
                    await checkTheme(page, theme);
                    assert.equal(await page.locator('#lightModeToggle').count(), 1, name);
                    assert.equal(await page.locator('html').getAttribute('data-theme-preference'), 'system');
                    const layout = await page.evaluate(() => ({ doc: document.documentElement.scrollWidth, viewport: window.innerWidth }));
                    assert.ok(layout.doc <= layout.viewport + 1, `${name} ${theme} ${width}: horizontal overflow ${layout.doc}`);
                    const controls = await page.locator('input').evaluateAll(inputs => inputs.map(input => ({ id: input.id, label: input.labels.length || input.getAttribute('aria-label') })));
                    assert.ok(controls.every(input => input.label), `${name}: unlabeled input`);
                    await page.locator('#lightModeToggle').click();
                    await checkTheme(page, theme === 'dark' ? 'light' : 'dark');
                    // In the story page the appearance button must not trigger navigation.
                    if (name === 'paginaInicial.html') {
                        await page.waitForTimeout(650);
                        assert.equal(new URL(page.url()).pathname, '/paginaInicial.html');
                    }
                    await page.locator('[data-theme-system]').click();
                    await checkTheme(page, theme);
                    if (process.env.ARCANA_SCREENSHOTS && ['index.html', 'login.php', 'fichas.html', 'Entidades/fichaSerafim.html'].includes(name) && width !== 320) {
                        fs.mkdirSync(process.env.ARCANA_SCREENSHOTS, { recursive: true });
                        await page.screenshot({ path: path.join(process.env.ARCANA_SCREENSHOTS, name.replaceAll('/', '-').replace('.php', '') + `-${theme}-${width}.png`), fullPage: true });
                    }
                    layouts++;
                }
            }
            assert.deepEqual(errors, [], `${theme}: page/resource errors`);
            const tokens = await page.evaluate(() => {
                const css = getComputedStyle(document.documentElement);
                return Object.fromEntries(['text', 'text-muted', 'bg', 'surface', 'surface-raised', 'surface-soft', 'accent', 'accent-soft', 'button', 'button-hover', 'on-button', 'success', 'success-bg', 'danger', 'danger-bg', 'input-border'].map(k => [k, css.getPropertyValue('--' + k).trim()]));
            });
            for (const surface of ['bg', 'surface', 'surface-raised', 'surface-soft']) {
                for (const foreground of ['text', 'text-muted', 'accent']) {
                    assert.ok(contrast(tokens[foreground], tokens[surface]) >= 4.5, `${theme}: ${foreground}/${surface} contrast`);
                }
            }
            for (const [a, b] of [['accent', 'accent-soft'], ['on-button', 'button'], ['on-button', 'button-hover'], ['success', 'success-bg'], ['danger', 'danger-bg']]) {
                assert.ok(contrast(tokens[a], tokens[b]) >= 4.5, `${theme}: ${a}/${b} contrast`);
            }
            assert.ok(contrast(tokens['input-border'], tokens.surface) >= 3, `${theme}: input boundary contrast`);
            await context.close();
        }

        const context = await browser.newContext({ colorScheme: 'light', reducedMotion: 'reduce' });
        await serve(context);
        const page = await context.newPage();
        await page.goto(origin + '/index.html');
        await page.locator('#lightModeToggle').focus();
        assert.equal(await page.locator('#lightModeToggle').evaluate(el => getComputedStyle(el).outlineStyle), 'solid');
        await page.keyboard.press('Enter');
        await checkTheme(page, 'dark');
        await page.reload();
        await checkTheme(page, 'dark');
        await page.goto(origin + '/Entidades/fichaRatos.html');
        await checkTheme(page, 'dark');
        await page.emulateMedia({ colorScheme: 'dark' });
        await page.locator('#lightModeToggle').click();
        await checkTheme(page, 'light');
        await page.emulateMedia({ colorScheme: 'light' });
        await page.emulateMedia({ colorScheme: 'dark' });
        await checkTheme(page, 'light'); // Explicit choice overrides OS.
        await page.locator('[data-theme-system]').click();
        await checkTheme(page, 'dark');
        await page.emulateMedia({ colorScheme: 'light' });
        await page.waitForFunction(() => document.documentElement.dataset.theme === 'light');
        await checkTheme(page, 'light');
        const second = await context.newPage();
        await second.goto(origin + '/login.php');
        await second.locator('#lightModeToggle').click();
        await page.waitForFunction(() => document.documentElement.dataset.theme === 'dark');
        await checkTheme(page, 'dark'); // Shared choice across tabs.
        await second.evaluate(() => localStorage.clear());
        await page.waitForFunction(() => document.documentElement.dataset.themePreference === 'system');
        await checkTheme(page, 'light');
        await second.close();
        await page.evaluate(() => { localStorage.clear(); localStorage.setItem('lightMode', 'enabled'); });
        await page.reload();
        await checkTheme(page, 'dark');
        assert.equal(await page.evaluate(() => localStorage.getItem('arcana-theme')), 'dark');
        await page.evaluate(() => { localStorage.setItem('arcana-theme', 'invalid'); localStorage.removeItem('lightMode'); });
        await page.reload();
        await checkTheme(page, 'light');

        await page.goto(origin + '/fichas.html');
        await page.locator('[data-filter="Medo"]').click();
        await page.locator('#search').fill('Ratos');
        assert.equal(await page.locator('.card:visible').count(), 0);
        assert.ok(await page.locator('#empty-state').isVisible());
        await page.locator('[data-filter="all"]').click();
        assert.equal(await page.locator('.card:visible').count(), 1);
        await page.locator('.card:visible .button').click();
        await page.getByRole('button', { name: 'Descrição', exact: true }).click();
        assert.ok(await page.locator('#descricao').isVisible());
        assert.ok(!(await page.locator('#status').isVisible()));
        await page.goto(origin + '/index.html');
        await page.locator('[aria-controls="loreContent"]').click();
        assert.ok(await page.locator('#loreContent').isVisible());
        assert.equal(await page.locator('[aria-controls="loreContent"]').getAttribute('aria-expanded'), 'true');
        await page.goto(origin + '/dado.html');
        await page.getByRole('button', { name: 'Rolar d20', exact: true }).click();
        const result = await page.locator('#resultado span').first().textContent();
        assert.ok(+result >= 1 && +result <= 20);
        await page.goto(origin + '/no-controls.html');
        await page.waitForLoadState();
        assert.equal(await page.locator('html').getAttribute('data-theme'), 'light');
        await context.close();

        const blocked = await browser.newContext({ colorScheme: 'dark' });
        await serve(blocked);
        await blocked.addInitScript(() => {
            Object.defineProperty(window, 'localStorage', { get() { throw new DOMException('Storage blocked', 'SecurityError'); } });
        });
        const blockedPage = await blocked.newPage();
        const blockedErrors = [];
        blockedPage.on('pageerror', error => blockedErrors.push(error.message));
        await blockedPage.goto(origin + '/index.html');
        await checkTheme(blockedPage, 'dark');
        await blockedPage.locator('#lightModeToggle').click();
        await checkTheme(blockedPage, 'light');
        assert.deepEqual(blockedErrors, []);
        await blocked.close();

        const noJS = await browser.newContext({ colorScheme: 'dark', javaScriptEnabled: false });
        await serve(noJS);
        const noJSPage = await noJS.newPage();
        await noJSPage.goto(origin + '/login.php');
        assert.equal(await noJSPage.evaluate(() => getComputedStyle(document.body).backgroundColor), 'rgb(16, 13, 22)');
        await noJS.close();
        console.log(`PASS: ${layouts} page/theme/viewport combinations, palette contrast, keyboard, persistence, OS changes, tab sync, legacy migration, blocked storage, no-control page, no-JS fallback, filters, entity sections, lore and dice.`);
    } finally {
        await browser.close();
    }
})().catch(error => { console.error(error); process.exitCode = 1; });
