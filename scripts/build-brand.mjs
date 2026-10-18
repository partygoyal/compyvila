// Derive every logo/icon from the outlined SVG master. Requires installed Chrome and PHP GD for ICO.
import {spawn,execFileSync} from 'node:child_process';
import {readFile,writeFile,mkdtemp,rm} from 'node:fs/promises';
import {tmpdir} from 'node:os';
import {join} from 'node:path';
const root=new URL('../public/',import.meta.url);
const master=await readFile(new URL('assets/brand/compyvila-master.svg',root),'utf8');
const symbol=master.match(/<g id="brand-symbol">([\s\S]*?)<\/g>/)[1];
const icon=`<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 64 64">${symbol}</svg>`;
const light=master.replace('id="brand-wordmark" fill="#20231f"','id="brand-wordmark" fill="#f8f7f2"');
for(const name of ['assets/compyvila-logo-v3.svg','assets/compyvila-logo.svg'])await writeFile(new URL(name,root),master);
await writeFile(new URL('assets/compyvila-logo-light-v3.svg',root),light);
for(const name of ['assets/favicon-v3.svg','assets/favicon-v2.svg','assets/favicon.svg'])await writeFile(new URL(name,root),icon);
const profile=await mkdtemp(join(tmpdir(),'compyvila-brand-'));
const chrome=spawn('/Applications/Google Chrome.app/Contents/MacOS/Google Chrome',['--headless','--disable-gpu','--no-first-run','--no-default-browser-check','--remote-debugging-port=0',`--user-data-dir=${profile}`,'about:blank'],{stdio:['ignore','ignore','pipe']});
let ws;
try {
 const address=await new Promise((resolve,reject)=>{let output='';const timer=setTimeout(()=>reject(new Error('Chrome timeout')),15000);chrome.once('error',reject);chrome.stderr.on('data',data=>{output+=data;const m=output.match(/DevTools listening on (ws:\/\/\S+)/);if(m){clearTimeout(timer);resolve(m[1]);}});});
 ws=new WebSocket(address);await new Promise(resolve=>ws.addEventListener('open',resolve,{once:true}));
 let sequence=0;const pending=new Map();ws.addEventListener('message',({data})=>{const m=JSON.parse(data);if(pending.has(m.id)){const p=pending.get(m.id);pending.delete(m.id);m.error?p.reject(m.error):p.resolve(m.result);}});
 const send=(method,params={},sessionId)=>new Promise((resolve,reject)=>{const id=++sequence;pending.set(id,{resolve,reject});ws.send(JSON.stringify({id,method,params,...(sessionId?{sessionId}:{})}));});
 const {targetId}=await send('Target.createTarget',{url:'about:blank'});const {sessionId}=await send('Target.attachToTarget',{targetId,flatten:true});
 const render=async(svg,width,height)=>{
  const expression=`(async()=>{const img=new Image();img.src='data:image/svg+xml;base64,${Buffer.from(svg).toString('base64')}';await img.decode();const big=document.createElement('canvas');big.width=${width}*4;big.height=${height}*4;big.getContext('2d').drawImage(img,0,0,big.width,big.height);const c=document.createElement('canvas');c.width=${width};c.height=${height};const ctx=c.getContext('2d');ctx.imageSmoothingQuality='high';ctx.drawImage(big,0,0,c.width,c.height);return c.toDataURL('image/png').split(',')[1]})()`;
  const r=await send('Runtime.evaluate',{expression,returnByValue:true,awaitPromise:true},sessionId);if(r.exceptionDetails)throw new Error(r.exceptionDetails.text);return Buffer.from(r.result.value,'base64');
 };
 for(const size of [16,32,48,180,192,512]){
  const bytes=await render(icon,size,size);await writeFile(new URL(`assets/brand/icon-${size}.png`,root),bytes);
  const alias={32:'assets/favicon-32.png',180:'apple-touch-icon.png',192:'assets/icon-192.png',512:'assets/icon-512.png'}[size];if(alias)await writeFile(new URL(alias,root),bytes);
 }
 const ratio=Number(master.match(/width="([\d.]+)"/)[1])/64;
 const logo=await render(master,Math.round(128*ratio),128);
 await writeFile(new URL('assets/compyvila-logo-v3.png',root),logo);await writeFile(new URL('assets/compyvila-logo.png',root),logo);
 await writeFile(new URL('assets/compyvila-logo-light-v3.png',root),await render(light,Math.round(128*ratio),128));
 const inner=master.replace(/^<svg[^>]*>/,'').replace(/<\/svg>\s*$/,'');
 const og=`<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="630" viewBox="0 0 1200 630"><rect width="1200" height="630" fill="#f8f7f2"/><g transform="translate(64 48)">${inner}</g><circle cx="965" cy="312" r="218" fill="#f15a24"/><g transform="translate(804 152) scale(5)">${symbol}</g><text x="64" y="287" font-family="Arial,Helvetica,sans-serif" font-weight="700" font-size="72" letter-spacing="-2" fill="#20231f">Crypto, with</text><text x="64" y="371" font-family="Arial,Helvetica,sans-serif" font-weight="700" font-size="72" letter-spacing="-2" fill="#20231f">perspective.</text><rect x="64" y="480" width="80" height="5" fill="#f15a24"/><text x="64" y="542" font-family="monospace" font-size="17" letter-spacing="2" fill="#20231f">MARKETS / ANALYSIS / UPDATES</text><text x="64" y="584" font-family="Arial,Helvetica,sans-serif" font-size="16" fill="#72776b">compyvila.com</text></svg>`;
 await writeFile(new URL('assets/brand/social-default-v4.svg',root),og);
 const defaultPng=await render(og,1200,630);
 await writeFile(new URL('assets/brand/social-default-v4.png',root),defaultPng);
 await writeFile(new URL('assets/compyvila-og.png',root),defaultPng);
 execFileSync('php',['-r',`$im=imagecreatefrompng('public/assets/brand/social-default-v4.png');imagejpeg($im,'public/assets/social/compyvila-v4.jpg',88);`]);
 execFileSync('php',['-r',`$sizes=[16,32,48];$entries='';$payload='';$offset=6+16*3;foreach($sizes as $s){$png=file_get_contents('public/assets/brand/icon-'.$s.'.png');$entries.=pack('CCCCvvVV',$s,$s,0,0,1,32,strlen($png),$offset);$payload.=$png;$offset+=strlen($png);}file_put_contents('public/favicon.ico',pack('vvv',0,1,3).$entries.$payload);`]);
 console.log('Built consistent SVG, PNG, ICO, Apple, and app icons from the vector master.');
}finally{if(ws)ws.close();chrome.kill('SIGTERM');await new Promise(r=>setTimeout(r,300));await rm(profile,{recursive:true,force:true});}
