<?php
require_once __DIR__ . '/includes/layout.php';
require_once __DIR__ . '/includes/blocks.php';
$P = 'pricing'; $h = page_content($P);
site_head($P, 'page-inner');
page_hero($h['hero']);
$cc = $h['calc'];
$rows = function ($t) { return array_map(function ($l) { return array_map('trim', explode('|', $l)); }, lines($t)); };
$cfg = ['phones' => $rows($cc['phones']), 'rates' => $rows($cc['rates']), 'fx' => ['USD' => 1, 'EUR' => (float)$cc['eur'] ?: 0.92, 'GBP' => (float)$cc['gbp'] ?: 0.79], 'link' => href($cc['cta_link']), 'pl' => $cc['phone_link_text'], 'rl' => $cc['rates_link_text']]; ?>
<section class="section calc-sec" id="numbers">
  <div class="container">
    <div class="section-title reveal"><h2><?= rich($cc['title']) ?></h2><p><?= e($cc['text']) ?></p></div>
    <div class="calc" id="calc" data-cfg="<?= e(json_encode($cfg, JSON_UNESCAPED_UNICODE)) ?>">
      <div class="cur-switch reveal" role="tablist" aria-label="Currency"><span class="cur-thumb"></span>
        <button type="button" class="on" data-cur="USD">$ USD</button><button type="button" data-cur="EUR">€ EUR</button><button type="button" data-cur="GBP">£ GBP</button>
      </div>
      <div class="row g-4 reveal">
        <div class="col-lg-6"><div class="cc">
          <div class="cc-head"><span class="cc-ic"><i class="bi bi-telephone"></i></span><div><h3><?= e($cc['phone_title']) ?></h3><p><?= e($cc['phone_sub']) ?></p></div></div>
          <div class="sel"><img class="flag" id="pFlag" alt=""><select id="pSel" aria-label="Country"></select><i class="bi bi-chevron-down"></i></div>
          <div class="seg" id="seg"><span class="seg-thumb"></span>
            <button type="button" data-t="0"><b>1 month</b><small></small></button><button type="button" data-t="1"><b>3 months</b><small></small></button><button type="button" data-t="2" class="on"><b>12 months</b><small></small></button>
          </div>
          <div class="bigprice"><span class="tilde" id="tilde">~</span><span class="bp-v" id="pPrice">$0.00</span><span class="bp-u" id="pUnit">/month</span></div>
          <div class="bp-sub" id="pSub"></div>
          <div class="cc-pill" id="pNote"><i class="bi bi-check2"></i><span></span></div>
          <a class="cc-link" id="pLink" href="#"></a>
        </div></div>
        <div class="col-lg-6"><div class="cc">
          <div class="cc-head"><span class="cc-ic"><i class="bi bi-currency-dollar"></i></span><div><h3><?= e($cc['rates_title']) ?></h3><p><?= e($cc['rates_sub']) ?></p></div></div>
          <div class="sel"><img class="flag" id="rFlag" alt=""><select id="rSel" aria-label="Destination"></select><i class="bi bi-chevron-down"></i></div>
          <ul class="rates">
            <li><span class="ri ri-b"><i class="bi bi-phone"></i></span><span class="rn">Mobile</span><span class="rv"><b id="rM">0</b> <small class="ru">¢/Min</small></span></li>
            <li><span class="ri ri-o"><i class="bi bi-telephone"></i></span><span class="rn">Landline</span><span class="rv"><b id="rL">0</b> <small class="ru">¢/Min</small></span></li>
            <li><span class="ri ri-g"><i class="bi bi-chat-square-text"></i></span><span class="rn">SMS</span><span class="rv"><b id="rS">0</b> <small class="ru2">¢/Msg</small></span></li>
          </ul>
          <a class="cc-link" id="rLink" href="#"></a>
        </div></div>
      </div>
      <p class="calc-foot"><i class="bi bi-dot"></i> <?= e($cc['footnote']) ?></p>
    </div>
  </div>
</section>
<script>
(function(){
  var root=document.getElementById('calc'); if(!root) return;
  var C=JSON.parse(root.dataset.cfg), cur='USD', term=2, pi=0, ri=0, M=[1,3,12], U=['/month','/3 months','/year'];
  var sym={USD:'$',EUR:'€',GBP:'£'}, cu={USD:'¢',EUR:'c',GBP:'p'};
  var $=function(i){return document.getElementById(i)};
  var flag=function(c){return 'https://flagcdn.com/w40/'+String(c).toLowerCase()+'.png'};
  function fill(sel,arr){arr.forEach(function(r,i){var o=document.createElement('option');o.value=i;o.textContent=r[1];sel.appendChild(o)})}
  fill($('pSel'),C.phones); fill($('rSel'),C.rates);
  function money(v){return sym[cur]+(v*C.fx[cur]).toFixed(2)}
  var shown=0,raf;
  function tween(el,to,fmt){cancelAnimationFrame(raf);var from=shown,t0=null;(function s(ts){if(!t0)t0=ts;var p=Math.min(1,(ts-t0)/450),e=1-Math.pow(1-p,3);shown=from+(to-from)*e;el.textContent=fmt(shown);if(p<1)raf=requestAnimationFrame(s)})(performance.now())}
  function drawPhone(){
    var r=C.phones[pi]; if(!r) return; var p=[+r[2],+r[3],+r[4]], m=M[term], tot=p[term], per=tot/m, base=p[0]*m;
    $('pFlag').src=flag(r[0]);
    [].forEach.call(document.querySelectorAll('#seg button'),function(b,i){var sv=i?Math.round((1-p[i]/(p[0]*M[i]))*100):0;b.querySelector('small').textContent=sv>0?'Save '+sv+'%':'';b.querySelector('small').style.visibility=sv>0?'visible':'hidden'});
    $('tilde').style.display=m>1?'':'none';
    tween($('pPrice'),per*C.fx[cur],function(v){return sym[cur]+v.toFixed(2)});
    $('pUnit').textContent='/month';
    var sv=Math.round((1-tot/base)*100);
    $('pSub').innerHTML=m>1?((sv>0?'<s>'+money(base)+'</s> ':'')+'<span>'+money(tot)+U[term]+'</span>'):'<span>Billed monthly, cancel anytime</span>';
    $('pNote').querySelector('span').textContent=r[5]||''; $('pNote').style.display=r[5]?'':'none';
    var l=$('pLink'); l.href=C.link; l.innerHTML=C.pl.replace('{country}',r[1])+' &rarr;';
  }
  function drawRates(){
    var r=C.rates[ri]; if(!r) return; var f=function(v){return (+v*C.fx[cur]).toFixed(1).replace(/\.0$/,'')};
    $('rFlag').src=flag(r[0]); $('rM').textContent=f(r[2]); $('rL').textContent=f(r[3]); $('rS').textContent=f(r[4]);
    [].forEach.call(document.querySelectorAll('.ru'),function(e){e.textContent=cu[cur]+'/Min'}); document.querySelector('.ru2').textContent=cu[cur]+'/Msg';
    var l=$('rLink'); l.href=C.link; l.innerHTML=C.rl.replace('{country}',r[1])+' &rarr;';
  }
  function thumb(box,sel,th){var b=box.querySelector(sel); if(!b) return; th.style.width=b.offsetWidth+'px'; th.style.transform='translateX('+b.offsetLeft+'px)'}
  var cs=root.querySelector('.cur-switch'), sg=$('seg');
  function place(){thumb(cs,'button.on',cs.querySelector('.cur-thumb'));thumb(sg,'button.on',sg.querySelector('.seg-thumb'))}
  cs.addEventListener('click',function(e){var b=e.target.closest('button'); if(!b) return; cur=b.dataset.cur; [].forEach.call(cs.querySelectorAll('button'),function(x){x.classList.toggle('on',x===b)}); place(); drawPhone(); drawRates()});
  sg.addEventListener('click',function(e){var b=e.target.closest('button'); if(!b) return; term=+b.dataset.t; [].forEach.call(sg.querySelectorAll('button'),function(x){x.classList.toggle('on',x===b)}); place(); drawPhone()});
  $('pSel').addEventListener('change',function(){pi=+this.value;drawPhone()}); $('rSel').addEventListener('change',function(){ri=+this.value;drawRates()});
  drawPhone(); drawRates(); place(); window.addEventListener('resize',place); if(document.fonts&&document.fonts.ready) document.fonts.ready.then(place); setTimeout(place,300);
})();
</script>

<?php $i = $h['included']; ?>
<section class="section bg-soft">
  <div class="container"><?php section_title($i['title']); ?>
    <div class="row g-3">
      <?php foreach ($i['items'] as $it): ?>
      <div class="col-md-6 col-lg-4 reveal"><div class="incl"><i class="bi <?= e($it['icon']) ?>"></i><div><strong><?= e($it['title']) ?></strong><span><?= e($it['text']) ?></span></div></div></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php $cr = $h['credit']; ?>
<section class="section">
  <div class="container">
    <div class="row g-5 align-items-center">
      <div class="col-lg-6 text-center reveal"><div class="phone phone-lg"><img src="<?= e(asset($cr['image'])) ?>" alt="Credit wallet" loading="lazy"></div></div>
      <div class="col-lg-6 reveal">
        <h2 class="h1-sm"><?= rich($cr['title']) ?></h2>
        <p class="lead-sm"><?= e($cr['text']) ?></p>
        <ul class="check-list"><?php foreach (lines($cr['bullets']) as $b): ?><li><i class="bi bi-check-circle-fill"></i><?= e($b) ?></li><?php endforeach; ?></ul>
      </div>
    </div>
  </div>
</section>

<?php $f = $h['faq']; ?>
<section class="section bg-soft">
  <div class="container" style="max-width:860px">
    <?php section_title($f['title']); ?>
    <div class="accordion faq reveal" id="faq">
      <?php foreach ($f['items'] as $n => $q): ?>
      <div class="accordion-item">
        <h3 class="accordion-header"><button class="accordion-button <?= $n ? 'collapsed' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#q<?= $n ?>"><?= e($q['q']) ?></button></h3>
        <div id="q<?= $n ?>" class="accordion-collapse collapse <?= $n ? '' : 'show' ?>" data-bs-parent="#faq"><div class="accordion-body"><?= nl($q['a']) ?></div></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php site_foot();
