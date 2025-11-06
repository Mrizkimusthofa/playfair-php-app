// script.js - animate processLog lines with highlight per step (1s interval)
(function(){
  const playBtn = document.getElementById('playStepsBtn');
  const stopBtn = document.getElementById('stopStepsBtn');
  const pre = document.getElementById('processLog');
  if(!pre) return;
  let lines = pre.innerText.split('\n').filter(l=>l.trim().length>0);
  // create span lines
  pre.innerHTML = '';
  lines.forEach((ln, idx)=>{
    const sp = document.createElement('div');
    sp.className = 'process-line';
    sp.dataset.index = idx;
    sp.textContent = ln;
    pre.appendChild(sp);
  });
  let timer = null;
  let current = 0;
  function highlightNext(){
    // remove previous highlights
    const all = pre.querySelectorAll('.process-line');
    all.forEach(el=> el.classList.remove('highlight'));
    if(current >= all.length){
      clearInterval(timer);
      timer = null;
      stopBtn.style.display = 'none';
      playBtn.style.display = 'inline-block';
      return;
    }
    const el = pre.querySelector('.process-line[data-index="'+current+'"]');
    if(el){
      el.classList.add('highlight');
      // scroll into view
      el.scrollIntoView({behavior:'smooth', block:'center'});
    }
    current++;
  }
  playBtn.addEventListener('click', ()=>{
    if(timer) return;
    current = 0;
    highlightNext();
    timer = setInterval(highlightNext, 1000); // 1 second interval
    playBtn.style.display = 'none';
    stopBtn.style.display = 'inline-block';
  });
  stopBtn.addEventListener('click', ()=>{
    if(timer) clearInterval(timer);
    timer = null;
    stopBtn.style.display = 'none';
    playBtn.style.display = 'inline-block';
  });
})();
