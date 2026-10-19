// Native SVG layouts with exact typography, rendered to compact JPEGs by local Chrome.
// No external image service, fonts or runtime API required.
import {spawn} from 'node:child_process';
import {readFile,writeFile,mkdtemp,rm,mkdir} from 'node:fs/promises';
import {tmpdir} from 'node:os';
import {join} from 'node:path';
const root=new URL('../',import.meta.url);
const brand=(await readFile(new URL('public/assets/compyvila-logo-v3.svg',root),'utf8')).replace(/^<svg[^>]*>/,'').replace(/<\/svg>\s*$/,'');
const specs=[
 ['profit-loss',['Crypto Profit','& Loss Calculator'],['See your net return after fees.','Includes ROI and break-even price.'],'Trading','#f15a24','#fff0e5','trade','Net profit','+247.75','Example trade · fees included'],
 ['average-buy-price',['Average Buy','Price Calculator'],['Combine purchases and fees.','Find your weighted cost per token.'],'Trading','#7653cc','#eee8fa','average','Average cost','120','3 tokens · total cost 360'],
 ['position-size',['Crypto Position','Size Calculator'],['Set a risk budget and stop price.','Calculate your planned position size.'],'Trading','#327c64','#e2f1e7','position','Position size','20 tokens','100 risk / 5 stop distance'],
 ['market-cap-fdv',['Market Cap','& FDV Calculator'],['Compare circulating market value','with a diluted supply assumption.'],'Token research','#4668bd','#e6edff','cap','Market cap / FDV','20M / 200M','Example · same token price'],
 ['bitcoin-unit-converter',['BTC to Satoshi','Converter'],['Convert BTC, mBTC and sats.','Exact units. No rounding surprises.'],'Bitcoin units','#c17022','#fff0d5','bitcoin','1 BTC equals','100M sats','1 sat = 0.00000001 BTC'],
 ['ethereum-unit-converter',['ETH, Gwei & Wei','Converter'],['Convert Ethereum units exactly.','Keep every wei with full precision.'],'Ethereum units','#6e56b7','#eee8ff','ethereum','1 ETH equals','1 billion gwei','1 ETH = 10^18 wei'],
 ['gas-fee',['Ethereum Gas','Fee Calculator'],['Estimate execution fees in ETH.','Add your ETH price for a USD estimate.'],'Ethereum fees','#2c7b96','#dff2f6','gas','Estimated fee','0.000462 ETH','21,000 gas × 22 gwei'],
 ['staking-rewards',['Staking Rewards','Simulator'],['Explore hypothetical token rewards.','Compare APR and compounding.'],'Planning','#5f7f32','#edf4de','staking','Example · 5% APR','+50 tokens','1,000 tokens · 365 days · simple'],
 ['portfolio-allocation',['Crypto Portfolio','Allocation Calculator'],['Compare current weights with targets.','Plan your buy and sell adjustments.'],'Portfolio planning','#b4517e','#fae5ee','portfolio','Your target mix','60% / 40%','Example allocation · your inputs'],
 ['token-vesting',['Token Vesting','Calculator'],['Model cliffs and linear unlocks.','See locked and unlocked tokens.'],'Token planning','#44787d','#e0f0ed','vesting','Example schedule','3 → 12 months','Cliff → fully vested'],
 ['creator-campaign-planner',['Creator Campaign','Pricing Planner'],['Build a quote from your own rates.','Include rights, expenses and fees.'],'For creators','#c1682f','#ffecdf','creator','Example brand quote','700 USD','Estimated creator payout: 540 USD'],
 ['directory',['Free Crypto','Tools & Calculators'],['11 tools for trades, tokens and planning.','Free to use. No account needed.'],'The Compyvila toolbox','#f15a24','#e9e6fa','directory','Calculate. Convert.','11 free tools','Your inputs · clearer results'],
 ['quiz',['Crypto Quiz'],['10 questions. How much do you know?','Learn with explanations after each answer.'],'Test your knowledge','#7653cc','#ebe4ff','quiz','Your next challenge','10 questions','No timer · no account needed']
];
const escape=s=>String(s).replaceAll('&','&amp;').replaceAll('<','&lt;').replaceAll('>','&gt;').replaceAll('"','&quot;');
const text=(x,y,size,value,fill='#20231f',weight=400,extra='')=>`<text x="${x}" y="${y}" font-size="${size}" font-weight="${weight}" fill="${fill}" ${extra}>${escape(value)}</text>`;
function diagram(type,a,p){
 const line=(x1,y1,x2,y2)=>`<path d="M${x1} ${y1}L${x2} ${y2}" fill="none" stroke="${a}" stroke-width="7" stroke-linecap="round"/>`;
 const bar=(x,w,h,fill=a)=>`<rect x="${x}" y="${145-h}" width="${w}" height="${h}" rx="7" fill="${fill}"/>`;
 switch(type){
  case 'trade':return line(20,130,80,96)+line(80,96,122,113)+line(122,113,205,35)+`<path d="M174 35H205V66" fill="none" stroke="${a}" stroke-width="7" stroke-linecap="round"/>`;
  case 'average':return bar(15,40,60)+bar(75,40,105)+bar(135,40,78)+`<path d="M5 65H220" stroke="#20231f" stroke-width="3" stroke-dasharray="7 7"/>`+text(180,48,20,'avg',a,700);
  case 'position':return `<path d="M108 10L194 40V85C194 129 150 157 108 175C66 157 22 129 22 85V40Z" fill="${p}" stroke="${a}" stroke-width="4"/>`+text(108,106,41,'1%',a,700,'text-anchor="middle"')+text(108,137,18,'risk budget',a,400,'text-anchor="middle"');
  case 'cap':return bar(30,65,40)+bar(125,65,140)+text(62,177,19,'Cap',a,700,'text-anchor="middle"')+text(157,177,19,'FDV',a,700,'text-anchor="middle"');
  case 'bitcoin':return `<circle cx="112" cy="80" r="72" fill="${a}"/>`+text(112,104,69,'₿','#fff',700,'text-anchor="middle"')+text(112,185,21,'BTC → mBTC → sats',a,700,'text-anchor="middle"');
  case 'ethereum':return `<path d="M112 0L56 83L112 116L168 83Z" fill="${a}"/><path d="M56 97L112 177L168 97L112 129Z" fill="${a}" opacity=".65"/>`;
  case 'gas':return `<path d="M129 5L60 103H112L93 175L174 75H125Z" fill="${a}"/>`+text(18,194,20,'gas × price per gas',a,700);
  case 'staking':return bar(15,42,54)+bar(76,42,91)+bar(137,42,140)+`<path d="M20 65L165 12M141 12H165V36" fill="none" stroke="#20231f" stroke-width="4" stroke-linecap="round"/>`;
  case 'portfolio':return `<circle cx="108" cy="85" r="67" fill="none" stroke="${p}" stroke-width="34"/><circle cx="108" cy="85" r="67" fill="none" stroke="${a}" stroke-width="34" stroke-dasharray="252.58 421" transform="rotate(-90 108 85)"/>`+text(108,94,27,'60 / 40',a,700,'text-anchor="middle"');
  case 'vesting':return `<path d="M10 145H55V125H93V99H132V71H172V40H210" fill="none" stroke="${a}" stroke-width="7" stroke-linecap="round" stroke-linejoin="round"/>`+text(10,184,19,'Cliff',a,700)+text(138,184,19,'Unlocks',a,700);
  case 'creator':return `<rect x="27" y="10" width="166" height="158" rx="13" fill="${p}" stroke="${a}" stroke-width="3"/>`+line(53,48,145,48)+line(53,78,165,78)+line(53,108,125,108)+text(155,151,30,'+',a,700);
  case 'directory':return [0,1,2].flatMap(r=>[0,1,2].map(c=>`<rect x="${28+c*62}" y="${10+r*56}" width="46" height="41" rx="9" fill="${(r+c)%2?a:p}" stroke="${a}" stroke-width="2"/>`)).join('');
  case 'quiz':return `<rect x="22" y="6" width="177" height="163" rx="20" fill="${p}"/>`+text(110,106,93,'?',a,700,'text-anchor="middle"')+`<path d="M66 139L83 154L116 124" fill="none" stroke="${a}" stroke-width="7" stroke-linecap="round" stroke-linejoin="round"/>`;
 }
}
function svg(s){
 const [key,lines,sub,group,accent,pale,type,label,value,foot]=s;
 const titleSize=lines.some(l=>l.length>23)?54:62;
 const titleY=lines.length===1?292:248;
 const fontValue=value.length>16?25:31;
 return `<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="630" viewBox="0 0 1200 630"><rect width="1200" height="630" fill="#f8f7f2"/><rect x="785" width="415" height="630" fill="${pale}"/><circle cx="1160" cy="35" r="127" fill="${accent}" opacity=".14"/><circle cx="1202" cy="610" r="136" fill="${accent}" opacity=".12"/><g font-family="Arial,Helvetica,sans-serif"><g transform="translate(64 43) scale(.65)">${brand}</g>${text(64,157,19,group.toUpperCase(),accent,700,'letter-spacing="1.5"')}${lines.map((line,i)=>text(60,titleY+i*73,titleSize,line,'#20231f',700,'letter-spacing="-2"')).join('')}${sub.map((line,i)=>text(64,392+i*35,25,line,'#5b6256')).join('')}<rect x="64" y="481" width="${key==='quiz'?202:168}" height="38" rx="19" fill="${accent}"/>${text(key==='quiz'?165:148,507,17,key==='quiz'?'FREE CRYPTO QUIZ':'FREE TO USE','#fff',700,'text-anchor="middle"')}${text(key==='quiz'?280:247,507,18,key==='quiz'?'No wallet needed':'No account needed','#5b6256')}<rect x="64" y="565" width="65" height="4" fill="${accent}"/>${text(146,575,22,'compyvila.com','#20231f',700)}<rect x="818" y="133" width="330" height="373" rx="22" fill="#fffefb"/><g transform="translate(870 155)">${diagram(type,accent,pale)}</g>${text(983,384,17,label,'#5b6256',400,'text-anchor="middle"')}${text(983,427,fontValue,value,accent,700,'text-anchor="middle"')}${text(983,467,foot.length>34?13:15,foot,'#5b6256',400,'text-anchor="middle"')}${text(983,568,16,key==='quiz'?'BITCOIN / ETHEREUM / WALLETS':'ILLUSTRATIVE EXAMPLES / MANUAL INPUTS',accent,700,'text-anchor="middle" letter-spacing=".5"')}</g></svg>`;
}
const profile=await mkdtemp(join(tmpdir(),'compyvila-tool-social-'));
const chrome=spawn('/Applications/Google Chrome.app/Contents/MacOS/Google Chrome',['--headless','--disable-gpu','--no-first-run','--no-default-browser-check','--disable-background-networking','--remote-debugging-port=0',`--user-data-dir=${profile}`,'about:blank'],{stdio:['ignore','ignore','pipe']});
let ws;
try{
 const address=await new Promise((resolve,reject)=>{let output='';const timer=setTimeout(()=>reject(new Error('Chrome timeout')),15000);chrome.once('error',reject);chrome.stderr.on('data',data=>{output+=data;const m=output.match(/DevTools listening on (ws:\/\/\S+)/);if(m){clearTimeout(timer);resolve(m[1]);}});});
 ws=new WebSocket(address);await new Promise(resolve=>ws.addEventListener('open',resolve,{once:true}));
 let sequence=0;const pending=new Map();ws.addEventListener('message',({data})=>{const m=JSON.parse(data);if(pending.has(m.id)){const p=pending.get(m.id);pending.delete(m.id);m.error?p.reject(m.error):p.resolve(m.result);}});
 const send=(method,params={},sessionId)=>new Promise((resolve,reject)=>{const id=++sequence;pending.set(id,{resolve,reject});ws.send(JSON.stringify({id,method,params,...(sessionId?{sessionId}:{})}));});
 const {targetId}=await send('Target.createTarget',{url:'about:blank'});const {sessionId}=await send('Target.attachToTarget',{targetId,flatten:true});
 await mkdir(new URL('resources/design/tool-social/',root),{recursive:true});await mkdir(new URL('public/assets/social/tools/',root),{recursive:true});
 const manifest={};
 for(const spec of specs){
  const source=svg(spec);await writeFile(new URL(`resources/design/tool-social/${spec[0]}.svg`,root),source+'\n');
  const expression=`(async()=>{const img=new Image();img.src='data:image/svg+xml;base64,${Buffer.from(source).toString('base64')}';await img.decode();const c=document.createElement('canvas');c.width=1200;c.height=630;c.getContext('2d').drawImage(img,0,0);let quality=.9,encoded;do{encoded=c.toDataURL('image/jpeg',quality).split(',')[1];quality-=.04;}while(encoded.length*3/4>100000&&quality>.65);return encoded;})()`;
  const rendered=await send('Runtime.evaluate',{expression,returnByValue:true,awaitPromise:true},sessionId);if(rendered.exceptionDetails)throw new Error(rendered.exceptionDetails.text);
  const bytes=Buffer.from(rendered.result.value,'base64');if(bytes.length>100000)throw new Error(`${spec[0]} exceeds 100 KB`);
  const name=`${spec[0]}-v1.jpg`;await writeFile(new URL(`public/assets/social/tools/${name}`,root),bytes);
  manifest[spec[0]]={path:`/assets/social/tools/${name}`,alt:`${spec[1].join(' ')} — ${spec[2].join(' ')} Compyvila.`,width:1200,height:630,bytes:bytes.length};
  console.log(`${name}: ${(bytes.length/1000).toFixed(1)} KB`);
 }
 await writeFile(new URL('resources/data/tool-social.json',root),JSON.stringify(manifest,null,2)+'\n');
}finally{ws?.close();chrome.kill('SIGTERM');await new Promise(r=>setTimeout(r,300));await rm(profile,{recursive:true,force:true});}
