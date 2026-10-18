// Pure calculations: no network, storage, DOM or price feed.
export function number(value, label, {min=0,max=1e18,positive=false,integer=false}={}) {
  if (String(value ?? '').trim()==='') throw Error(`Enter ${label.toLowerCase()}.`);
  const n=Number(value);
  if (!Number.isFinite(n) || n<min || n>max || (positive && n<=0) || (integer && !Number.isInteger(n))) throw Error(`${label} must be ${integer?'a whole number ':''}${positive?'greater than zero':`at least ${min}`} and no more than ${max}.`);
  return n;
}
const pct=(v,label,max=100)=>number(v,label,{max});
function finish(metrics,headers=[],rows=[],bars=[]) {
  for(const [,v] of metrics) if(typeof v==='number'&&!Number.isFinite(v)) throw Error('These inputs produce a result too large to calculate. Reduce the values.');
  for(const row of rows) for(const v of row) if(typeof v==='number'&&!Number.isFinite(v)) throw Error('These inputs produce a result too large to calculate.');
  return {metrics,headers,rows,bars};
}
export function convertExact(amount,unit,powers) {
  if (!(unit in powers)) throw Error('Choose a valid unit.');
  const raw=String(amount).trim();
  if(raw.length>100||!/^\d+(?:\.\d+)?$/.test(raw)) throw Error('Enter a non-negative plain decimal, such as 0.001. Scientific notation is not supported.');
  const [whole,part='']=raw.split('.'), power=powers[unit];
  if(part.slice(power).replace(/0/g,'')!=='') throw Error('This amount is smaller than the smallest supported unit. Use a whole number of satoshis or wei.');
  const atoms=BigInt(whole)*10n**BigInt(power)+BigInt((part.slice(0,power).padEnd(power,'0'))||'0');
  return Object.entries(powers).map(([name,p])=>{
    if(!p)return [name,atoms.toString()];
    const text=atoms.toString().padStart(p+1,'0'), tail=text.slice(-p).replace(/0+$/,'');
    return [name,text.slice(0,-p)+(tail?'.'+tail:'')];
  });
}
export function utcDate(value) {
  if(!/^\d{4}-\d{2}-\d{2}$/.test(value))throw Error('Enter a valid date.');
  const date=new Date(value+'T00:00:00Z');
  if(!Number.isFinite(+date)||date.toISOString().slice(0,10)!==value||date.getUTCFullYear()<1900||date.getUTCFullYear()>9990)throw Error('Use a valid date between 1900 and 9990.');
  return date;
}
export function addMonths(date,months) {
  const target=new Date(Date.UTC(date.getUTCFullYear(),date.getUTCMonth()+months,1));
  const end=new Date(Date.UTC(target.getUTCFullYear(),target.getUTCMonth()+1,0)).getUTCDate();
  target.setUTCDate(Math.min(date.getUTCDate(),end));
  if(target.getUTCFullYear()>9999)throw Error('The vesting end date is too far in the future.');
  return target;
}
export function calculate(slug,v,items=[]) {
  const n=(key,label=key,options={})=>number(v[key],label,options);
  switch(slug) {
    case 'profit-loss': {
      const buy=n('buy','Buy price',{positive:true}), sell=n('sell','Sell price'), q=n('quantity','Token quantity',{positive:true}), bf=pct(v.buyFee,'Buy fee',99.999),sf=pct(v.sellFee,'Sell fee',99.999);
      const cost=buy*q*(1+bf/100),proceeds=sell*q*(1-sf/100),profit=proceeds-cost;
      return finish([['Net profit / loss',profit],['Return on cost (%)',profit/cost*100],['Total purchase cost',cost],['Net sale proceeds',proceeds],['Total fees',buy*q*bf/100+sell*q*sf/100],['Break-even sell price',cost/(q*(1-sf/100))]]);
    }
    case 'average-buy-price': {
      requireRows(items);let quantity=0,cost=0,fees=0;
      items.forEach((r,i)=>{const q=number(r.quantity,`Row ${i+1} quantity`,{positive:true}),p=number(r.price,`Row ${i+1} price`,{positive:true}),f=number(r.fee,`Row ${i+1} fee`);quantity+=q;cost+=q*p+f;fees+=f;});
      return finish([['Average cost per token',cost/quantity],['Total tokens',quantity],['Total purchase cost',cost],['Fees included',fees]]);
    }
    case 'position-size': {
      const balance=n('balance','Balance',{positive:true}),risk=pct(v.risk,'Risk per trade'),entry=n('entry','Entry price',{positive:true}),stop=n('stop','Stop price',{positive:true});
      if(!['long','short'].includes(v.side))throw Error('Choose a valid direction.');
      if((v.side==='long'&&stop>=entry)||(v.side==='short'&&stop<=entry))throw Error(`For a ${v.side} position, the stop must be ${v.side==='long'?'below':'above'} the entry price.`);
      const budget=balance*risk/100,units=budget/Math.abs(entry-stop);
      return finish([['Position size (tokens)',units],['Planned loss at stop',budget],['Position notional',units*entry],['Notional / account balance',units*entry/balance],['Stop distance (%)',Math.abs(entry-stop)/entry*100]]);
    }
    case 'market-cap-fdv': {
      const price=n('price','Price'),supply=n('circulating','Circulating supply'),total=n('total','Diluted supply',{positive:true});
      if(supply>total)throw Error('Circulating supply cannot exceed your fully diluted supply assumption.');
      return finish([['Market capitalization',price*supply],['Fully diluted valuation',price*total],['Circulating share (%)',supply/total*100],['Supply outside circulation',total-supply]]);
    }
    case 'bitcoin-unit-converter': return finish(convertExact(v.amount,v.unit,{BTC:8,mBTC:5,sats:0}));
    case 'ethereum-unit-converter': return finish(convertExact(v.amount,v.unit,{ETH:18,gwei:9,wei:0}));
    case 'gas-fee': {
      const gas=n('gas','Gas used',{positive:true,integer:true}),base=n('base','Base fee'),tip=n('tip','Priority fee'),price=n('ethPrice','ETH price');
      const eth=gas*(base+tip)/1e9,metrics=[['Estimated fee (ETH)',eth],['Total gas price (gwei)',base+tip],['Base fee component (ETH)',gas*base/1e9],['Priority component (ETH)',gas*tip/1e9]];
      if(price>0)metrics.splice(1,0,['Estimated fee (USD)',eth*price]);
      return finish(metrics);
    }
    case 'staking-rewards': {
      const principal=n('principal','Principal',{positive:true}),rate=pct(v.apr,'APR',10000)/100,days=n('days','Days',{max:36500}),freq=v.frequency;
      if(!['none','365','12','1'].includes(freq))throw Error('Choose a valid compounding frequency.');
      const balance=d=>freq==='none'?principal*(1+rate*d/365):principal*Math.pow(1+rate/Number(freq),Number(freq)*d/365);
      const end=balance(days), rows=Array.from({length:5},(_,i)=>[Math.round(days*i/4*100)/100,balance(days*i/4),balance(days*i/4)-principal]);
      return finish([['Projected total tokens',end],['Hypothetical reward tokens',end-principal],['Return over duration (%)',(end/principal-1)*100]],['Day','Total tokens','Rewards'],rows);
    }
    case 'portfolio-allocation': {
      requireRows(items); const holdings=items.map((r,i)=>({asset:String(r.asset??'').trim().slice(0,80),value:number(r.value,`Row ${i+1} value`),target:pct(r.target,`Row ${i+1} target`)}));
      if(holdings.some(r=>!r.asset))throw Error('Name each holding.');
      const total=holdings.reduce((sum,r)=>sum+r.value,0),targets=holdings.reduce((sum,r)=>sum+r.target,0);
      if(total<=0)throw Error('Enter a positive total portfolio value.');
      if(Math.abs(targets-100)>1e-7)throw Error(`Targets must total 100%. They currently total ${targets.toFixed(2)}%.`);
      const rows=holdings.map(r=>[r.asset,r.value/total*100,r.target,total*r.target/100,total*r.target/100-r.value]);
      return finish([['Total portfolio value',total],['Total target (%)',targets]],['Asset','Current %','Target %','Target value','Buy (+) / sell (−)'],rows,holdings.map(r=>[r.asset,r.value/total*100]));
    }
    case 'token-vesting': {
      const allocation=n('allocation','Allocation',{positive:true}),start=utcDate(v.start),asof=utcDate(v.asof),cliff=n('cliff','Cliff',{integer:true,max:1200}),duration=n('duration','Duration',{positive:true,integer:true,max:1200}),initial=pct(v.initial,'Initial unlock');
      if(cliff>=duration)throw Error('The cliff must be shorter than the total duration.');
      const cliffDate=addMonths(start,cliff),end=addMonths(start,duration),initialTokens=allocation*initial/100;
      const unlocked=date=>+date<+start?0:initialTokens+(allocation-initialTokens)*Math.min(1,Math.max(0,(date-cliffDate)/(end-cliffDate)));
      const amount=unlocked(asof),indexes=[...new Set([0,cliff,...Array.from({length:Math.min(duration,12)},(_,i)=>Math.ceil(duration*(i+1)/Math.min(duration,12)))])].sort((a,b)=>a-b);
      return finish([['Unlocked tokens',amount],['Locked tokens',Math.max(0,allocation-amount)],['Unlocked (%)',amount/allocation*100],['Cliff ends',cliffDate.toISOString().slice(0,10)],['Fully vested',end.toISOString().slice(0,10)]],['Date','Unlocked tokens','Locked tokens'],indexes.map(m=>{const date=addMonths(start,m),a=unlocked(date);return [date.toISOString().slice(0,10),a,Math.max(0,allocation-a)];}));
    }
    case 'creator-campaign-planner': {
      requireRows(items);if(!['USD','INR','EUR','GBP'].includes(v.currency))throw Error('Choose a valid currency.');
      const subtotal=items.reduce((sum,r,i)=>{if(!String(r.item??'').trim())throw Error('Name each deliverable.');return sum+number(r.quantity,`Row ${i+1} quantity`,{positive:true,integer:true,max:100000})*number(r.rate,`Row ${i+1} rate`);},0);
      const uplift=subtotal*pct(v.rights,'Usage rights',10000)/100,exclusive=n('exclusivity','Exclusivity'),expenses=n('expenses','Expenses'),fees=subtotal+uplift+exclusive,commission=fees*pct(v.commission,'Commission')/100;
      return finish([[`Brand quote (${v.currency})`,fees+expenses],[`Estimated creator payout (${v.currency})`,fees-commission],['Deliverables subtotal',subtotal],['Usage rights uplift',uplift],['Exclusivity fee',exclusive],['Reimbursed production expenses',expenses],['Agency commission',commission]]);
    }
    default: throw Error('Unknown calculator.');
  }
}
function requireRows(rows){if(!rows.length||rows.length>50)throw Error('Use between 1 and 50 rows.');}
export const quizQuestions=[
  ['What does a crypto wallet primarily manage?',['Private keys used to authorize transactions','Physical coins','Guaranteed investment returns'],0,'Wallets manage keys and help sign transactions. Assets are recorded on the blockchain.'],
  ['Who should you share your seed phrase with?',['A support agent','Nobody','A verified influencer'],1,'A seed phrase can restore access to your wallet. Anyone with it may control your funds.'],
  ['How many satoshis are in one bitcoin?',['1,000','1,000,000','100,000,000'],2,'One bitcoin equals 100 million satoshis.'],
  ['What is circulating market capitalization?',['Price multiplied by circulating supply','Total trading fees','The amount held on one exchange'],0,'Market cap uses current price and circulating supply. It is not the amount of money invested.'],
  ['What does a higher FDV than market cap usually indicate?',['Guaranteed price growth','Some assumed diluted supply is not circulating','The token has no fees'],1,'FDV uses a larger supply assumption at the same price. Future supply and price can differ.'],
  ['What pays for execution work on Ethereum?',['A seed phrase','Gas fees','Bitcoin halving'],1,'Gas measures computational work. Execution fees depend on gas used and the effective price per gas.'],
  ['What is a stablecoin depeg?',['A departure from its intended reference value','A guaranteed buying opportunity','A wallet password change'],0,'Stablecoins can trade away from their target. Their design does not guarantee stable value.'],
  ['Does high trading volume always mean deep liquidity?',['Yes, in every market','Only for new tokens','No, they describe different things'],2,'Volume measures traded amounts over a period. Liquidity concerns the ability to trade with limited price impact.'],
  ['Are staking rewards guaranteed fiat profit?',['Yes','No','Only when APR is above 10%'],1,'Rewards may vary, and token prices, fees, slashing or lockups can change the outcome.'],
  ['What does a token vesting schedule describe?',['When tokens become unlocked under specified rules','A guaranteed price path','The number of daily traders'],0,'Vesting defines unlock timing. Actual contracts may use cliffs, initial unlocks and discrete or continuous releases.']
];
