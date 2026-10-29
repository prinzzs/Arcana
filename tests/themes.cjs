const { chromium } = require('playwright');
const fs = require('node:fs');
const path = require('node:path');
const assert = require('node:assert/strict');
(async () => {
 const browser = await chromium.launch({executablePath:process.env.CHROMIUM_EXECUTABLE_PATH, args:['--no-sandbox','--use-gl=angle','--use-angle=swiftshader']});
 const page = await browser.newPage();
 const root = path.resolve(__dirname,'..');
 const pages = fs.readdirSync(root).filter(p=>p.endsWith('.html')).concat(fs.readdirSync(path.join(root,'Entidades')).filter(p=>p.endsWith('.html')).map(p=>'Entidades/'+p));
 for (const width of [390,1440]) {
  await page.setViewportSize({width,height:1000});
  for (const file of pages) {
   await page.goto('file://'+path.join(root,file));
   assert.equal(await page.locator('#lightModeToggle').count(),1);
   for (const theme of ['light','dark','red']) {
    await page.locator('#lightModeToggle').click();
    await page.locator('[data-choice="'+theme+'"]').click();
    assert.equal(await page.locator('html').getAttribute('data-theme'),theme);
    assert(await page.evaluate(()=>document.documentElement.scrollWidth <= innerWidth+1),file+' overflow '+width);
   }
  }
 }
 await page.goto('file://'+path.join(root,'dado.html'));
 await page.screenshot({path:'/workspace/scratch/875cb5af8411/dados-visual.png',fullPage:true});
 await browser.close();
 console.log('All pages: three themes and two widths OK');
})().catch(e=>{console.error(e);process.exit(1)});
