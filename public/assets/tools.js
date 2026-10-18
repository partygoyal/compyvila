import {calculate,quizQuestions} from './tools-core.mjs';
const $=(s,root=document)=>root.querySelector(s);
const el=(tag,text,className)=>{const node=document.createElement(tag);if(text!==undefined)node.textContent=text;if(className)node.className=className;return node;};
const format=v=>typeof v==='number'?new Intl.NumberFormat('en-US',{maximumSignificantDigits:12}).format(Object.is(v,-0)?0:v):String(v);
let filter='';
const search=$('#tool-search');
function filterTools(){let count=0;document.querySelectorAll('[data-tool-card]').forEach(card=>{const show=(!filter||card.dataset.group===filter)&&card.textContent.toLowerCase().includes(search.value.trim().toLowerCase());card.hidden=!show;if(show)count++;});$('#tool-empty').hidden=count>0;}
if(search){search.addEventListener('input',filterTools);document.querySelectorAll('[data-tool-filter]').forEach(button=>button.addEventListener('click',()=>{filter=button.dataset.toolFilter;document.querySelectorAll('[data-tool-filter]').forEach(b=>{b.classList.toggle('selected',b===button);b.setAttribute('aria-pressed',String(b===button));});filterTools();}));}
const workspace=$('[data-calculator]');
if(workspace){
  const slug=workspace.dataset.calculator,form=$('#calculator'),output=$('#tool-output');
  if(form){
    const originalRows=$('#tool-rows')?.innerHTML;
    const values=root=>Object.fromEntries([...root.querySelectorAll('input,select')].filter(input=>!input.closest('.tool-row')||root.classList.contains('tool-row')).map(input=>[input.name,input.value]));
    function render(){
      output.replaceChildren();
      try{
        const result=calculate(slug,values(form),[...form.querySelectorAll('.tool-row')].map(values));
        for(const [label,value] of result.metrics){const metric=el('div',undefined,'tool-metric');metric.append(el('span',label),el('strong',format(value)));output.append(metric);}
        if(result.bars.length){const chart=el('div');chart.setAttribute('aria-label','Current portfolio weights');for(const [label,weight] of result.bars){const line=el('div',undefined,'tool-chart-label');line.append(el('span',label),el('span',format(weight)+'%'));const bar=el('div',undefined,'tool-bar'),svg=document.createElementNS('http://www.w3.org/2000/svg','svg'),rect=document.createElementNS(svg.namespaceURI,'rect');svg.setAttribute('viewBox','0 0 100 8');svg.setAttribute('preserveAspectRatio','none');svg.setAttribute('aria-hidden','true');rect.setAttribute('width',String(weight));rect.setAttribute('height','8');rect.setAttribute('fill','#6a8d48');svg.append(rect);bar.append(svg);chart.append(line,bar);}output.append(chart);}
        if(result.rows.length){const wrap=el('div',undefined,'tool-table-wrap'),table=el('table',undefined,'tool-table'),head=el('thead'),tr=el('tr');for(const h of result.headers){const th=el('th',h);th.scope='col';tr.append(th);}head.append(tr);table.append(head);const body=el('tbody');for(const row of result.rows){const tr=el('tr');for(const cell of row)tr.append(el('td',format(cell)));body.append(tr);}table.append(body);wrap.append(table);output.append(wrap);}
      }catch(error){const notice=el('p',error.message,'tool-error');notice.setAttribute('role','alert');output.append(notice);}
    }
    form.addEventListener('submit',event=>{event.preventDefault();render();});
    form.addEventListener('reset',()=>{if(originalRows!==undefined)$('#tool-rows').innerHTML=originalRows;setTimeout(render,0);});
    form.addEventListener('click',event=>{const button=event.target.closest('.tool-remove');if(button){const rows=form.querySelectorAll('.tool-row');if(rows.length>1){button.closest('.tool-row').remove();render();}else{output.replaceChildren(el('p','Keep at least one row.','tool-error'));}}});
    $('#tool-add')?.addEventListener('click',()=>{if(form.querySelectorAll('.tool-row').length>=50){output.replaceChildren(el('p','You can add up to 50 rows.','tool-error'));return;}const row=$('#tool-row-template').content.cloneNode(true);$('#tool-rows').append(row);$('#tool-rows').lastElementChild.querySelector('input').focus();});
    $('#calculator-fields').disabled=false;
    render();
  }else if(slug==='crypto-knowledge-quiz'){
    const root=$('#quiz');let index=0,score=0;
    function question(){root.replaceChildren();if(index===quizQuestions.length){root.append(el('p','QUIZ COMPLETE','quiz-progress'),el('div',`${score} / ${quizQuestions.length}`,'quiz-score'),el('h2',score>=8?'You know your fundamentals.':'Keep building your knowledge.'),el('p','Review our guides, then try again. This score is a learning check, not a qualification.','quiz-feedback'));const restart=el('button','Try again ↺','button');restart.type='button';restart.addEventListener('click',()=>{index=0;score=0;question();});root.append(restart);restart.focus();return;}
      const [text,answers,correct,explanation]=quizQuestions[index];root.append(el('p',`QUESTION ${index+1} OF ${quizQuestions.length} · SCORE ${score}`,'quiz-progress'));const heading=el('h2',text,'quiz-question');heading.tabIndex=-1;root.append(heading);const options=el('div',undefined,'quiz-options');answers.forEach((answer,i)=>{const button=el('button',answer,'quiz-option');button.type='button';button.addEventListener('click',()=>{const right=i===correct;if(right)score++;[...options.children].forEach((b,j)=>{b.disabled=true;if(j===correct)b.classList.add('correct');if(j===i&&!right)b.classList.add('incorrect');});const feedback=el('p',(right?'Correct. ':'Not quite. Correct answer: '+answers[correct]+'. ')+explanation,'quiz-feedback');feedback.setAttribute('role','status');const next=el('button',index===quizQuestions.length-1?'See my score ↗':'Next question →','button');next.type='button';next.addEventListener('click',()=>{index++;question();});root.append(feedback,next);next.focus();});options.append(button);});root.append(options);if(index>0)heading.focus();
    }question();
  }
}
