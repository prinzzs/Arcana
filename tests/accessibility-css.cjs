const { chromium } = require('playwright');
const fs = require('node:fs');
const path = require('node:path');
const assert = require('node:assert/strict');
(async () => {
 const browser = await chromium.launch({headless:true, ...(process.env.CHROMIUM_EXECUTABLE_PATH ? {executablePath:process.env.CHROMIUM_EXECUTABLE_PATH} : {})});
 const context = await browser.newContext({javaScriptEnabled:false});
 await context.route('**/*', async route => {
  const url = new URL(route.request().url());
  if(url.origin!=='http://arcana.test') return route.abort();
  const file=path.join(process.cwd(),decodeURIComponent(url.pathname));
  const types={'.html':'text/html','.css':'text/css','.js':'text/javascript','.otf':'font/otf','.png':'image/png','.jpg':'image/jpeg'};
  return route.fulfill({contentType:types[path.extname(file)]||'application/octet-stream',body:fs.readFileSync(file)});
 });
 const page=await context.newPage();
 for(const width of [320,390,1440]) {
  await page.setViewportSize({width,height:900});
  for(const file of ['index.html','criar-campanha.html','personagens.html']) {
   await page.goto('http://arcana.test/'+file);
   await page.locator('.accessibility-panel summary').click();
   await page.locator('#accessible-text').check();
   await page.locator('#accessible-contrast').check();
   await page.locator('#accessible-links').check();
   assert.equal(await page.evaluate(()=>getComputedStyle(document.documentElement).fontSize),'20px');
   assert.equal(await page.evaluate(()=>getComputedStyle(document.body).backgroundColor),'rgb(0, 0, 0)');
   assert(await page.evaluate(()=>document.documentElement.scrollWidth <= innerWidth+1),file+' '+width);
   assert(await page.locator('.accessibility-options').isVisible());
   await page.locator('.accessibility-panel summary').click();
   if(file==='personagens.html') {
    await page.getByRole('link',{name:'Inventário',exact:true}).click();
    assert(page.url().endsWith('#character-inventory'));
    await page.screenshot({path:'artifacts/revisao-visual/fichas-sem-js-'+width+'.png',fullPage:true});
   }
  }
 }
 await browser.close();
 console.log('HTML/CSS sem JavaScript: painel, texto, contraste, links e atalhos em 320, 390 e 1440 px: OK');
})().catch(e=>{console.error(e);process.exit(1)});
