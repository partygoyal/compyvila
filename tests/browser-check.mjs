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
  for (const width of [1440, 390, 768]) {
    await call('Emulation.setDeviceMetricsOverride', { width, height: 1100, deviceScaleFactor: 1, mobile: width === 390 });
    await navigate('/');
    if (await evaluate(`document.querySelectorAll('[data-hero-slide]').length > 1`)) {
      await evaluate(`document.querySelector('[data-hero-play]').click()`);
      assert(await evaluate(`document.querySelector('[data-hero-play]').getAttribute('aria-label') === 'Play slideshow'`), `Hero autoplay can pause at ${width}px`);
      await evaluate(`document.querySelector('[data-hero-next]').click()`);
      assert(await evaluate(`!document.querySelectorAll('[data-hero-slide]')[1].hidden && document.querySelectorAll('[data-hero-slide]')[0].hidden`), `Hero next post at ${width}px`);
      await evaluate(`document.querySelector('[data-hero-prev]').click()`);
      assert(await evaluate(`!document.querySelectorAll('[data-hero-slide]')[0].hidden`), `Hero previous post at ${width}px`);
      await evaluate(`document.querySelectorAll('[data-hero-dot]')[document.querySelectorAll('[data-hero-dot]').length - 1].click()`);
      assert(await evaluate(`!Array.from(document.querySelectorAll('[data-hero-slide]')).at(-1).hidden`), `Hero pagination selects final post at ${width}px`);
      await evaluate(`document.querySelector('[data-hero-next]').click()`);
      assert(await evaluate(`!document.querySelectorAll('[data-hero-slide]')[0].hidden && document.querySelector('[data-hero-counter]').textContent.trim().startsWith('01')`), `Hero wraps to first post at ${width}px`);
    }
    const bannerSlides = await evaluate(`Array.from(document.querySelectorAll('[data-hero-slide]')).flatMap((slide,i)=>slide.querySelector('.hero-story-art--image img') ? [i] : [])`);
    for (const index of bannerSlides) {
      await evaluate(`document.querySelectorAll('[data-hero-dot]')[${index}]?.click()`);
      const banner = await evaluate(`(()=>{const img=document.querySelector('.hero-slide:not([hidden]) .hero-story-art--image img');const frame=img.parentElement.getBoundingClientRect();return {fit:getComputedStyle(img).objectFit,ratio:frame.width/frame.height};})()`);
      assert(banner.fit === 'contain' && Math.abs(banner.ratio-1.9)<0.03, `Homepage shows complete banner ${index+1} at ${width}px`);
    }
    if (bannerSlides.length) await evaluate(`document.querySelector('[data-hero-dot]')?.click()`);
    const metrics = await evaluate(`({ viewport: innerWidth, page: document.documentElement.scrollWidth, overflowing: Array.from(document.querySelectorAll('body *')).filter(e => e.getBoundingClientRect().right > innerWidth + 2 && getComputedStyle(e).position !== 'absolute' && getComputedStyle(e).position !== 'fixed').slice(0,8).map(e=>e.tagName+'.'+e.className) })`);
    assert(metrics.page <= metrics.viewport, `Homepage fits ${width}px viewport: ${JSON.stringify(metrics)}`);
    if (width !== 768) {
      const screenshot = await call('Page.captureScreenshot', { format: 'png', captureBeyondViewport: true });
      await writeFile(`storage/qa-${width === 390 ? 'mobile' : 'desktop'}.png`, Buffer.from(screenshot.data, 'base64'));
    }
    if (width === 390) {
      const swipeY = await evaluate(`document.querySelector('.hero-story-art').getBoundingClientRect().top + 100`);
      await call('Input.dispatchTouchEvent', { type: 'touchStart', touchPoints: [{ x: 310, y: swipeY }] });
      await call('Input.dispatchTouchEvent', { type: 'touchMove', touchPoints: [{ x: 80, y: swipeY }] });
      await call('Input.dispatchTouchEvent', { type: 'touchEnd', touchPoints: [] });
      assert(await evaluate(`!document.querySelectorAll('[data-hero-slide]')[1].hidden`), 'Touch swipe advances the hero');
      await evaluate(`document.querySelector('[data-blog-slider]').dispatchEvent(new KeyboardEvent('keydown', { key: 'ArrowLeft', bubbles: true }))`);
      assert(await evaluate(`!document.querySelectorAll('[data-hero-slide]')[0].hidden`), 'Keyboard arrows navigate the hero');
      await evaluate(`document.querySelector('.mobile-toggle').click()`);
      assert(await evaluate(`document.querySelector('.mobile-toggle').getAttribute('aria-expanded') === 'true' && getComputedStyle(document.querySelector('#navigation')).display !== 'none'`), 'Mobile navigation opens');
      await evaluate(`document.querySelector('.mobile-toggle').click()`);
      assert(await evaluate(`document.querySelector('.mobile-toggle').getAttribute('aria-expanded') === 'false'`), 'Mobile navigation closes');
    }
  }
  await evaluate(`document.querySelector('[data-hero-play]').click(); new Promise(resolve => setTimeout(resolve, 7300))`);
  assert(await evaluate(`!document.querySelectorAll('[data-hero-slide]')[1].hidden`), 'Hero advances automatically');
  await call('Emulation.setEmulatedMedia', { features: [{ name: 'prefers-reduced-motion', value: 'reduce' }] });
  await navigate('/');
  assert(await evaluate(`document.querySelector('[data-hero-play]').getAttribute('aria-label') === 'Play slideshow'`), 'Reduced motion starts with autoplay paused');
  await call('Emulation.setEmulatedMedia', { features: [] });
  await evaluate(`document.querySelector('[data-save]').click()`);
  assert(await evaluate(`document.querySelector('[data-save]').getAttribute('aria-pressed') === 'true'`), 'Save button updates');
  await navigate('/watchlist');
  assert(await evaluate(`document.querySelector('.saved-stories').children.length === 1 && document.querySelector('.saved-empty').hidden`), 'Reading list survives navigation');
  await evaluate(`document.querySelector('.saved-stories [data-save]').click()`);
  assert(await evaluate(`document.querySelector('.saved-stories').children.length === 0 && !document.querySelector('.saved-empty').hidden`), 'Saved story can be removed');
  for (const width of [1440, 390]) {
    await call('Emulation.setDeviceMetricsOverride', { width, height: 1100, deviceScaleFactor: 1, mobile: width === 390 });
    await navigate('/media-kit');
    assert(await evaluate(`document.documentElement.scrollWidth <= innerWidth`), `Media kit fits ${width}px viewport`);
    if (width === 1440) {
      const shot = await call('Page.captureScreenshot', { format: 'png', captureBeyondViewport: true });
      await writeFile('storage/qa-media-kit.png', Buffer.from(shot.data, 'base64'));
    }
  }
  if (process.argv.includes('--ai-series')) {
    for (const width of [1440,390]) {
      await call('Emulation.setDeviceMetricsOverride', {width,height:1100,deviceScaleFactor:1,mobile:width<600});
      for (const slug of ['ai-agents-crypto-wallets','bittensor-decentralized-ai','akash-ai-gpu-compute']) {
        await navigate('/read/'+slug);
        assert(await evaluate(`document.querySelector('.article-banner img')?.naturalWidth > 1000`), `Creative banner loads for ${slug}`);
        assert(await evaluate(`document.documentElement.scrollWidth <= innerWidth`), `${slug} fits ${width}px`);
        assert(await evaluate(`document.querySelector('meta[property="og:image"]').content.includes('/assets/social/article-') && document.querySelector('meta[property="og:image"]').content.endsWith('.jpg')`), 'Social preview uses the article banner');
        if (slug === 'ai-agents-crypto-wallets') {
          const shot=await call('Page.captureScreenshot',{format:'png',captureBeyondViewport:true});
          await writeFile(`storage/qa-ai-article-${width}.png`,Buffer.from(shot.data,'base64'));
        }
      }
    }
  }
  if (process.argv.includes('--guides')) {
    for (const width of [1440, 390]) {
      await call('Emulation.setDeviceMetricsOverride', { width, height: 1100, deviceScaleFactor: 1, mobile: width === 390 });
      await navigate('/read/market-cap-vs-fdv');
      assert(await evaluate(`document.querySelectorAll('.article-body table').length === 1`), 'Guide comparison table renders');
      assert(await evaluate(`document.documentElement.scrollWidth <= innerWidth`), `Guide fits ${width}px viewport`);
      assert(await evaluate(`JSON.parse(document.querySelector('script[type="application/ld+json"]').textContent)['@graph'][0]['@type'] === 'Article'`), 'Article structured data parses in browser');
      const shot = await call('Page.captureScreenshot', { format: 'png', captureBeyondViewport: true });
      await writeFile(`storage/qa-guide-${width}.png`, Buffer.from(shot.data, 'base64'));
    }
  }
  if (process.argv.includes('--projects')) {
    for (const width of [1440, 1024, 768, 390, 320]) {
      await call('Emulation.setDeviceMetricsOverride', { width, height: 1100, deviceScaleFactor: 1, mobile: width < 900 });
      await navigate('/projects');
      assert(await evaluate(`document.querySelector('.project-filters') !== null`), 'Project directory filters render');
      assert(await evaluate(`document.documentElement.scrollWidth <= innerWidth`), `Project directory fits ${width}px viewport`);
      if (width === 1440 || width === 390) {
        const shot = await call('Page.captureScreenshot', { format: 'png', captureBeyondViewport: true });
        await writeFile(`storage/qa-projects-${width}.png`, Buffer.from(shot.data, 'base64'));
      }
      const profileUrl = await evaluate(`document.querySelector('.project-card h3 a')?.getAttribute('href')`);
      if (profileUrl) {
        await navigate(new URL(profileUrl, origin).pathname);
        assert(await evaluate(`document.querySelector('.project-facts') !== null && document.querySelector('.sources a') !== null`), 'Project milestone facts and primary sources render');
        assert(await evaluate(`document.documentElement.scrollWidth <= innerWidth`), `Project profile fits ${width}px viewport`);
        assert(await evaluate(`JSON.parse(document.querySelector('script[type="application/ld+json"]').textContent)['@graph'][0]['@type'] === 'WebPage'`), 'Project profile schema parses');
        if (width === 390) {
          const shot = await call('Page.captureScreenshot', { format: 'png', captureBeyondViewport: true });
          await writeFile('storage/qa-project-profile-mobile.png', Buffer.from(shot.data, 'base64'));
        }
      } else assert(await evaluate(`document.querySelector('.project-empty') !== null`), 'Empty project directory provides a useful next step');
    }
    await navigate('/projects?year=2025');
    assert(await evaluate(`document.querySelector('[name="year"]').value === '2025' && Array.from(document.querySelectorAll('.project-card .story-meta')).every(e=>e.textContent.includes('2025'))`), 'Launch year filter applies');
    await navigate('/projects?q=does-not-match-any-project-fixture');
    assert(await evaluate(`document.querySelector('.project-empty') !== null`), 'Unmatched project search has an empty state');
  }
  if (process.argv.includes('--admin')) {
    const credentials = await readFile('storage/admin-access.txt', 'utf8');
    const email = credentials.match(/Admin created: (\S+)/)?.[1];
    const password = credentials.match(/Password: (\S+)/)?.[1];
    if (!email || !password) throw new Error('Local bootstrap credentials are unavailable');
    await navigate('/admin/login');
    const loggedIn = new Promise(resolve => events.set('Page.loadEventFired', resolve));
    await evaluate(`document.querySelector('[name="email"]').value = ${JSON.stringify(email)}; document.querySelector('[name="password"]').value = ${JSON.stringify(password)}; document.querySelector('form').requestSubmit();`);
    await loggedIn;
    assert(await evaluate(`document.querySelector('h1')?.textContent.includes('Welcome to the desk.')`), 'Existing admin login works after migration');
    for (const url of ['/admin?tab=articles', '/admin?tab=market', '/admin?tab=projects', '/admin?tab=events', '/admin?tab=settings', '/admin/article', '/admin/article?id=1', '/admin/project', '/admin/event', '/admin/snapshot']) {
      await navigate(url);
      assert(await evaluate(`document.querySelector('.admin-tabs') !== null && document.documentElement.scrollWidth <= innerWidth`), `Admin page renders and fits mobile: ${url}`);
    }
    const signedOut = new Promise(resolve => events.set('Page.loadEventFired', resolve));
    await evaluate(`document.querySelector('form[action="/admin/logout"]').requestSubmit()`);
    await signedOut;
    assert(await evaluate(`document.querySelector('h1')?.textContent.includes('The editorial desk.')`), 'Admin signs out in browser');
  }
  assert(errors.length === 0, `No browser JavaScript exceptions (${errors.length})`);
} finally {
  ws?.close(); chrome.kill('SIGTERM');
  await new Promise(resolve => { if (chrome.exitCode !== null) resolve(); else chrome.once('exit', resolve); });
  await rm(profile, { recursive: true, force: true, maxRetries: 8, retryDelay: 200 });
}
