import {test} from 'node:test';
import assert from 'node:assert/strict';
import {calculate as calc,convertExact,utcDate,addMonths,quizQuestions} from '../public/assets/tools-core.mjs';
const metrics=result=>Object.fromEntries(result.metrics);
const close=(actual,expected)=>assert.ok(Math.abs(actual-expected)<1e-8,`${actual} ≈ ${expected}`);
test('trade fees and break-even include both legs',()=>{
 const m=metrics(calc('profit-loss',{buy:100,sell:125,quantity:10,buyFee:0.1,sellFee:0.1}));close(m['Net profit / loss'],247.75);close(m['Break-even sell price'],100.1/.999);
 const loss=metrics(calc('profit-loss',{buy:100,sell:0,quantity:1,buyFee:0,sellFee:0}));assert.equal(loss['Return on cost (%)'],-100);
 assert.throws(()=>calc('profit-loss',{buy:0,sell:10,quantity:1,buyFee:0,sellFee:0}));
});
test('average cost is quantity weighted including fixed fees',()=>{const m=metrics(calc('average-buy-price',{},[{quantity:2,price:100,fee:3},{quantity:1,price:160,fee:0}]));assert.equal(m['Average cost per token'],121);assert.throws(()=>calc('average-buy-price',{},[]));});
test('long and short stops use the correct direction',()=>{
 const values={balance:10000,risk:1,entry:100,stop:95,side:'long'};assert.equal(metrics(calc('position-size',values))['Position size (tokens)'],20);
 assert.equal(metrics(calc('position-size',{...values,side:'short',stop:105}))['Position size (tokens)'],20);
 assert.throws(()=>calc('position-size',{...values,side:'short'}));assert.throws(()=>calc('position-size',{...values,stop:100}));
});
test('supply assumptions cannot contradict circulating supply',()=>{const v={price:2,circulating:1e7,total:1e8};assert.equal(metrics(calc('market-cap-fdv',v))['Fully diluted valuation'],2e8);assert.throws(()=>calc('market-cap-fdv',{...v,total:100}));});
test('converters preserve atomic units beyond Number precision',()=>{
 assert.deepEqual(convertExact('0.00000001','BTC',{BTC:8,sats:0}),[['BTC','0.00000001'],['sats','1']]);
 const m=metrics(calc('ethereum-unit-converter',{amount:'123456789.123456789123456789',unit:'ETH'}));assert.equal(m.wei,'123456789123456789123456789');
 assert.equal(metrics(calc('bitcoin-unit-converter',{amount:'0.0000000100',unit:'BTC'})).sats,'1');
 for(const amount of ['0.000000001','-1','1e8','','Infinity'])assert.throws(()=>calc('bitcoin-unit-converter',{amount,unit:'BTC'}));
 assert.throws(()=>calc('ethereum-unit-converter',{amount:'1.1',unit:'wei'}));
});
test('gas components and optional fiat conversion',()=>{const v={gas:21000,base:20,tip:2,ethPrice:3000},m=metrics(calc('gas-fee',v));close(m['Estimated fee (ETH)'],.000462);close(m['Estimated fee (USD)'],1.386);assert.equal(metrics(calc('gas-fee',{...v,ethPrice:0}))['Estimated fee (USD)'],undefined);});
test('staking duration and compounding are hypothetical and bounded',()=>{
 const v={principal:1000,apr:5,days:365,frequency:'none'};assert.equal(metrics(calc('staking-rewards',v))['Hypothetical reward tokens'],50);
 const daily=metrics(calc('staking-rewards',{...v,frequency:'365'}));assert.ok(daily['Hypothetical reward tokens']>50);
 assert.equal(metrics(calc('staking-rewards',{...v,days:0}))['Hypothetical reward tokens'],0);
 assert.throws(()=>calc('staking-rewards',{...v,apr:10000,days:36500,frequency:'365'}));
});
test('portfolio balances and escapes through data rather than HTML',()=>{const rows=[{asset:'BTC',value:700,target:60},{asset:'ETH',value:300,target:40}],r=calc('portfolio-allocation',{},rows);assert.equal(r.rows[0][4],-100);assert.equal(r.rows[1][4],100);assert.throws(()=>calc('portfolio-allocation',{},[{...rows[0],target:40},rows[1]]));});
test('vesting handles pre-start, cliff, end, leap years and month clamping',()=>{
 const v={allocation:10000,start:'2026-01-01',cliff:3,duration:12,initial:10,asof:'2026-04-01'};
 assert.equal(metrics(calc('token-vesting',v))['Unlocked tokens'],1000);
 assert.equal(metrics(calc('token-vesting',{...v,asof:'2025-12-31'}))['Unlocked tokens'],0);
 assert.equal(metrics(calc('token-vesting',{...v,asof:'2027-01-01'}))['Unlocked tokens'],10000);
 assert.equal(addMonths(utcDate('2024-01-31'),1).toISOString().slice(0,10),'2024-02-29');
 assert.throws(()=>utcDate('2026-02-29'));assert.throws(()=>calc('token-vesting',{...v,cliff:12}));
});
test('creator commission excludes reimbursed expenses',()=>{const m=metrics(calc('creator-campaign-planner',{currency:'USD',rights:20,exclusivity:0,expenses:100,commission:10},[{item:'Posts',quantity:2,rate:250}]));assert.equal(m['Brand quote (USD)'],700);assert.equal(m['Estimated creator payout (USD)'],540);assert.equal(m['Agency commission'],60);});
test('all quiz questions have one valid answer and an explanation',()=>{assert.equal(quizQuestions.length,10);for(const [q,answers,index,why] of quizQuestions){assert.ok(q&&why&&answers[index]);assert.equal(new Set(answers).size,answers.length);}});
