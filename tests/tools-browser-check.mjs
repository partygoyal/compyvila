// Optional visual and interaction QA using an installed standalone Chrome.
import { spawn } from 'node:child_process';
import { mkdtemp, readFile, writeFile, rm, mkdir } from 'node:fs/promises';
import { tmpdir } from 'node:os';
import { join } from 'node:path';
const origin = process.env.PREVIEW_URL || 'http://127.0.0.1:8080';
const profile = await mkdtemp(join(tmpdir(), 'compyvila-browser-'));
const chrome = spawn(process.env.CHROME_BIN || '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome', ['--headless', '--disable-gpu', '--no-first-run', '--no-default-browser-check', '--disable-background-networking', '--remote-debugging-port=0', `--user-data-dir=${profile}`, 'about:blank'], { stdio: ['ignore', 'ignore', 'pipe'] });
let ws;
try {
  const address = await new Promise((resolve, reject) => {
    let output = '';
    const timeout = setTimeout(() => reject(new Error('Chrome did not start')), 15000);
    chrome.once('error', reject);
    chrome.stderr.on('data', data => {
      output += data;
      const match = output.match(/DevTools listening on (ws:\/\/\S+)/);
      if (match) { clearTimeout(timeout); resolve(match[1]); }
    });
  });
  ws = new WebSocket(address);
  await new Promise((resolve, reject) => { ws.addEventListener('open', resolve, { once: true }); ws.addEventListener('error', reject, { once: true }); });
  let sequence = 0;
  const pending = new Map();
  const events = new Map();
  const errors = [];
  ws.addEventListener('message', ({ data }) => {
    const message = JSON.parse(data);
    if (message.id && pending.has(message.id)) {
      const { resolve, reject, timer } = pending.get(message.id);
      pending.delete(message.id); clearTimeout(timer);
      message.error ? reject(new Error(message.error.message)) : resolve(message.result);
    }
    if (message.method === 'Runtime.exceptionThrown') errors.push(message.params.exceptionDetails.text);
    if (events.has(message.method)) { events.get(message.method)(); events.delete(message.method); }
  });
  const send = (method, params = {}, sessionId) => new Promise((resolve, reject) => {
    const id = ++sequence;
    const timer = setTimeout(() => { pending.delete(id); reject(new Error(`Timed out: ${method}`)); }, 10000);
    pending.set(id, { resolve, reject, timer }); ws.send(JSON.stringify({ id, method, params, ...(sessionId ? { sessionId } : {}) }));
  });
  const { targetId } = await send('Target.createTarget', { url: 'about:blank' });
  const { sessionId } = await send('Target.attachToTarget', { targetId, flatten: true });
  const call = (method, params = {}) => send(method, params, sessionId);
  await call('Page.enable'); await call('Runtime.enable');
  const evaluate = async expression => {
    const result = await call('Runtime.evaluate', { expression, returnByValue: true, awaitPromise: true });
    if (result.exceptionDetails) throw new Error(result.exceptionDetails.text);
    return result.result.value;
  };
  const navigate = async path => {
    const loaded = new Promise(resolve => events.set('Page.loadEventFired', resolve));
    await call('Page.navigate', { url: origin + path }); await loaded;
  };
  const assert = (value, name) => { if (!value) throw new Error(name); console.log(`PASS: ${name}`); };
  await mkdir('storage', { recursive: true });

  const catalog=JSON.parse(await readFile('resources/data/tools.json','utf8'));
  for(const width of [1440,1280,1200,1024,768,390,320]){
    await call('Emulation.setDeviceMetricsOverride',{width,height:1100,deviceScaleFactor:1,mobile:width<700});
    for(const path of ['/tools',...catalog.map(t=>'/tools/'+t.slug),'/quiz']){
      await navigate(path);
      assert(await evaluate('document.documentElement.scrollWidth <= innerWidth'),`${path} fits ${width}px`);
      if(path!='/tools')assert(await evaluate(`!document.querySelector('.tool-error') && (!!document.querySelector('.tool-metric') || !!document.querySelector('.quiz-option'))`),`${path} initializes at ${width}px`);
      if(width===1440&&path==='/tools')await writeFile('storage/qa-tools-directory.png',Buffer.from((await call('Page.captureScreenshot',{format:'png',captureBeyondViewport:true})).data,'base64'));
      if(width===390&&path==='/tools/portfolio-allocation')await writeFile('storage/qa-tools-mobile.png',Buffer.from((await call('Page.captureScreenshot',{format:'png',captureBeyondViewport:true})).data,'base64'));
    }
  }
  await navigate('/tools');
  assert(await evaluate(`document.querySelectorAll('[data-tool-card]').length===11 && !document.querySelector('[data-tool-card][href*="quiz"]')`),'Quiz is separate from the eleven tools');
  assert(await evaluate(`document.querySelector('#navigation [href="/tools"]').textContent==='Free Crypto Tools' && document.querySelector('#navigation [href="/quiz"]').classList.contains('nav-quiz')`),'Renamed tools and highlighted quiz links');
  await evaluate(`document.querySelector('#tool-search').value='gas';document.querySelector('#tool-search').dispatchEvent(new Event('input'))`);
  assert(await evaluate(`document.querySelectorAll('[data-tool-card]:not([hidden])').length===1`),'Directory search works');
  await evaluate(`document.querySelector('[data-tool-filter="Creators"]').click()`);
  assert(await evaluate(`!document.querySelector('#tool-empty').hidden`),'Category + search show useful empty state');
  await navigate('/tools/profit-loss');
  await evaluate(`document.querySelector('[name="sell"]').value=100;document.querySelector('#calculator').requestSubmit()`);
  assert(await evaluate(`document.querySelector('#tool-output').textContent.includes('-2')`),'Changing inputs recalculates trade loss');
  await evaluate(`document.querySelector('[type="reset"]').click();new Promise(r=>setTimeout(r,50))`);
  assert(await evaluate(`document.querySelector('#tool-output').textContent.includes('247.75')`),'Reset restores the example');
  await navigate('/tools/average-buy-price');
  await evaluate(`document.querySelector('#tool-add').click();const row=document.querySelector('#tool-rows').lastElementChild;row.querySelector('[name="quantity"]').value=1;row.querySelector('[name="price"]').value=120;document.querySelector('#calculator').requestSubmit()`);
  assert(await evaluate(`document.querySelectorAll('.tool-row').length===3 && !document.querySelector('.tool-error')`),'Add purchase row and recalculate');
  await evaluate(`document.querySelector('#tool-rows').lastElementChild.querySelector('.tool-remove').click()`);
  assert(await evaluate(`document.querySelectorAll('.tool-row').length===2`),'Remove purchase row');
  await navigate('/tools/portfolio-allocation');
  await evaluate(`document.querySelector('[name="target"]').value=30;document.querySelector('#calculator').requestSubmit()`);
  assert(await evaluate(`document.querySelector('.tool-error').textContent.includes('100%')`),'Reject invalid portfolio target total');
  await navigate('/tools/ethereum-unit-converter');
  await evaluate(`document.querySelector('[name="amount"]').value='0.000000000000000001';document.querySelector('#calculator').requestSubmit()`);
  assert(await evaluate(`Array.from(document.querySelectorAll('.tool-metric')).at(-1).textContent==='wei1'`),'Exact one-wei conversion in browser');
  await navigate('/quiz');
  assert(await evaluate(`document.querySelector('link[rel="canonical"]').href.endsWith('/quiz') && document.querySelector('#navigation [href="/quiz"]').classList.contains('active')`),'Standalone quiz canonical and navigation');
  const correct=[0,1,2,0,1,1,0,2,1,0];
  for(const answer of correct){await evaluate(`document.querySelectorAll('.quiz-option')[${answer}].click();document.querySelector('#quiz > button').click()`);}
  assert(await evaluate(`document.querySelector('.quiz-score').textContent==='10 / 10'`),'Quiz scores all ten answers');
  await evaluate(`document.querySelector('#quiz > button').click()`);
  assert(await evaluate(`document.querySelector('.quiz-progress').textContent.includes('SCORE 0')`),'Quiz restarts cleanly');
  assert(errors.length===0,`No browser script exceptions: ${errors.join(', ')}`);
} finally {
  ws?.close(); chrome.kill('SIGTERM');
  await new Promise(resolve => { if (chrome.exitCode !== null) resolve(); else chrome.once('exit', resolve); });
  await rm(profile, { recursive: true, force: true, maxRetries: 8, retryDelay: 200 });
}
