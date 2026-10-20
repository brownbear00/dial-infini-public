/* Dial Infini — premium motion layer */
(function () {
  var reduce = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;
  var fine = window.matchMedia && matchMedia('(hover:hover) and (pointer:fine)').matches;
  var $ = function (s, c) { return [].slice.call((c || document).querySelectorAll(s)); };
  var clamp = function (v, a, b) { return Math.max(a, Math.min(b, v)); };

  /* scroll progress bar */
  var bar = document.createElement('div'); bar.className = 'progress'; document.body.appendChild(bar);

  /* count-up numbers */
  function countUp(el) {
    var t = el.firstChild; while (t && t.nodeType !== 3) t = t.nextSibling;
    if (!t) return;
    var m = t.textContent.match(/(\d[\d,]*\.?\d*)/); if (!m) return;
    var raw = m[1], hasComma = raw.indexOf(',') > -1, num = parseFloat(raw.replace(/,/g, ''));
    var dec = (raw.split('.')[1] || '').length, pre = t.textContent.slice(0, m.index), post = t.textContent.slice(m.index + raw.length);
    var dur = 1600, t0 = null;
    function fmt(v) { var s = v.toFixed(dec); if (hasComma) s = Number(s).toLocaleString('en-US', { minimumFractionDigits: dec, maximumFractionDigits: dec }); return pre + s + post; }
    t.textContent = fmt(0);
    function step(ts) {
      if (!t0) t0 = ts; var p = clamp((ts - t0) / dur, 0, 1), e = 1 - Math.pow(1 - p, 4);
      t.textContent = fmt(num * e); if (p < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
  }
  var nums = $('.stat-v,.rate-v');
  if (!reduce && 'IntersectionObserver' in window) {
    var cio = new IntersectionObserver(function (en) { en.forEach(function (x) { if (x.isIntersecting) { countUp(x.target); cio.unobserve(x.target); } }); }, { threshold: .6 });
    nums.forEach(function (n) { cio.observe(n); });
  }

  /* cursor spotlight on cards (+ 3D tilt on big cards) */
  if (fine && !reduce) {
    $('.pcard,.stat-card,.icon-card,.rate-card,.plan,.incl,.form-card').forEach(function (c) {
      var tilt = c.matches('.pcard,.stat-card');
      c.classList.add('spot'); if (tilt) c.classList.add('tilt');
      c.addEventListener('mousemove', function (e) {
        var r = c.getBoundingClientRect(), x = e.clientX - r.left, y = e.clientY - r.top;
        c.style.setProperty('--mx', x + 'px'); c.style.setProperty('--my', y + 'px');
        if (tilt) { c.style.setProperty('--ry', ((x / r.width - .5) * 10).toFixed(2) + 'deg'); c.style.setProperty('--rx', ((.5 - y / r.height) * 10).toFixed(2) + 'deg'); }
      });
      c.addEventListener('mouseleave', function () { c.style.setProperty('--rx', '0deg'); c.style.setProperty('--ry', '0deg'); });
    });

    /* magnetic buttons */
    $('.btn-lg-pill,.btn-sm-pill,.store-btns a').forEach(function (b) {
      b.addEventListener('mousemove', function (e) {
        var r = b.getBoundingClientRect();
        b.style.translate = ((e.clientX - r.left - r.width / 2) * .18).toFixed(1) + 'px ' + ((e.clientY - r.top - r.height / 2) * .28).toFixed(1) + 'px';
      });
      b.addEventListener('mouseleave', function () { b.style.translate = ''; });
    });

    /* hero: cursor glow + parallax on phones */
    var hero = document.querySelector('.hero');
    if (hero) hero.addEventListener('mousemove', function (e) {
      var r = hero.getBoundingClientRect(), nx = (e.clientX - r.left) / r.width - .5, ny = (e.clientY - r.top) / r.height - .5;
      hero.style.setProperty('--hx', (e.clientX - r.left) + 'px'); hero.style.setProperty('--hy', (e.clientY - r.top) + 'px');
      $('.phone-l', hero).forEach(function (p) { p.style.translate = (nx * -34).toFixed(1) + 'px ' + (ny * -22).toFixed(1) + 'px'; });
      $('.phone-r', hero).forEach(function (p) { p.style.translate = (nx * 34).toFixed(1) + 'px ' + (ny * -26).toFixed(1) + 'px'; });
    });
  }

  /* scroll-driven: hero shot flattens, feature shots drift, header hides, progress bar */
  var hs = document.querySelector('.hero-visual .shot-main'), fs = $('.feature-row .shot'), hd = document.getElementById('siteHeader'), last = 0, tick = false;
  function frame() {
    tick = false;
    var y = window.scrollY, vh = window.innerHeight, h = document.documentElement.scrollHeight - vh;
    bar.style.transform = 'scaleX(' + (h > 0 ? y / h : 0) + ')';
    if (!reduce) {
      if (hs) { var p = clamp(y / (vh * .55), 0, 1); hs.style.transform = 'perspective(1500px) rotateX(' + ((1 - p) * 11).toFixed(2) + 'deg) scale(' + (.93 + .07 * p).toFixed(3) + ')'; }
      fs.forEach(function (s) { var r = s.getBoundingClientRect(); if (r.bottom < -100 || r.top > vh + 100) return; s.style.transform = 'translateY(' + (((r.top + r.height / 2) - vh / 2) * -.07).toFixed(1) + 'px)'; });
    }
    if (hd) { hd.classList.toggle('hide', y > 420 && y > last + 4 && !document.querySelector('.offcanvas.show')); if (y < last - 4 || y <= 420) hd.classList.remove('hide'); }
    last = y;
  }
  window.addEventListener('scroll', function () { if (!tick) { tick = true; requestAnimationFrame(frame); } }, { passive: true });
  window.addEventListener('resize', frame); frame();
})();

/* sticky stacking cards + scroll-driven loop */
(function () {
  var reduce = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;
  var cards = [].slice.call(document.querySelectorAll('.scard')), track = document.getElementById('loopTrack'), steps = [].slice.call(document.querySelectorAll('#loopSteps .lp'));
  var nodes = track ? [].slice.call(track.querySelectorAll('.lt-node')) : [], fill = track ? track.querySelector('.lt-bar i') : null, tick = false;
  function run() {
    tick = false; var vh = window.innerHeight;
    if (!reduce) cards.forEach(function (c, i) {
      var nx = cards[i + 1]; if (!nx) return;
      var top = parseFloat(getComputedStyle(c).top) || 96, d = nx.getBoundingClientRect().top - top, p = Math.max(0, Math.min(1, 1 - d / c.offsetHeight));
      c.style.setProperty('--sc', (1 - .06 * p).toFixed(4)); c.style.setProperty('--dim', (p * .9).toFixed(3));
    });
    if (track && steps.length) {
      var r = document.getElementById('loopSteps').getBoundingClientRect(), p = Math.max(0, Math.min(1, (vh * .8 - r.top) / (r.height + vh * .3)));
      fill.style.transform = 'scaleX(' + p.toFixed(3) + ')';
      nodes.forEach(function (n, i) { var on = p >= (i + .35) / nodes.length - .02; n.classList.toggle('on', on); if (steps[i]) steps[i].classList.toggle('on', on); });
    }
  }
  if (cards.length || track) { window.addEventListener('scroll', function () { if (!tick) { tick = true; requestAnimationFrame(run); } }, { passive: true }); window.addEventListener('resize', run); run(); }
})();

/* ===== FULL MOTION PACK ===== */
(function () {
  var reduce = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;
  var fine = window.matchMedia && matchMedia('(hover:hover) and (pointer:fine)').matches;
  var $ = function (s, c) { return [].slice.call((c || document).querySelectorAll(s)); };
  var clamp = function (v, a, b) { return Math.max(a, Math.min(b, v)); };

  /* 1) loader */
  var L = document.getElementById('loader');
  if (L) {
    var gone = false, hide = function () {
      if (gone) return; gone = true; L.classList.add('done');
      try { sessionStorage.setItem('idl', '1'); } catch (e) {}
      setTimeout(function () { if (L.parentNode) L.parentNode.removeChild(L); document.documentElement.classList.add('ready'); }, 900);
    };
    if (document.documentElement.classList.contains('no-loader')) { L.parentNode.removeChild(L); document.documentElement.classList.add('ready'); }
    else { var t0 = Date.now(); var go = function () { setTimeout(hide, Math.max(0, 900 - (Date.now() - t0))); }; if (document.readyState === 'complete') go(); else window.addEventListener('load', go); setTimeout(hide, 3000); }
  } else document.documentElement.classList.add('ready');

  /* 2) page transitions */
  if (!reduce) {
    document.addEventListener('click', function (e) {
      var a = e.target.closest && e.target.closest('a'); if (!a || e.defaultPrevented || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey || e.button) return;
      var h = a.getAttribute('href'); if (!h || h.charAt(0) === '#' || a.target || a.hasAttribute('download') || /^(mailto:|tel:|javascript:)/.test(h)) return;
      if (a.hostname !== location.hostname) return;
      if (a.pathname === location.pathname && a.search === location.search) return;
      e.preventDefault(); document.body.classList.add('leaving'); setTimeout(function () { location.href = a.href; }, 380);
    });
    window.addEventListener('pageshow', function (e) { if (e.persisted) document.body.classList.remove('leaving'); });
  }

  /* 3) button ripple */
  $('.btn').forEach(function (b) {
    b.addEventListener('pointerdown', function (e) {
      var r = b.getBoundingClientRect(), s = document.createElement('span'), d = Math.max(r.width, r.height) * 1.6;
      s.className = 'ripple'; s.style.cssText = 'width:' + d + 'px;height:' + d + 'px;left:' + (e.clientX - r.left - d / 2) + 'px;top:' + (e.clientY - r.top - d / 2) + 'px';
      b.appendChild(s); setTimeout(function () { s.remove(); }, 700);
    });
  });

  /* 4) back-to-top ring */
  var up = document.createElement('button'); up.className = 'to-top'; up.setAttribute('aria-label', 'Back to top');
  up.innerHTML = '<svg viewBox="0 0 44 44"><circle class="tt-bg" cx="22" cy="22" r="20"/><circle class="tt-fg" cx="22" cy="22" r="20"/></svg><i class="bi bi-arrow-up"></i>';
  document.body.appendChild(up); var fg = up.querySelector('.tt-fg'), C = 2 * Math.PI * 20; fg.style.strokeDasharray = C;
  up.addEventListener('click', function () { window.scrollTo({ top: 0, behavior: 'smooth' }); });

  /* 5) scroll velocity: marquee speed + ring progress */
  var mq = document.querySelector('.marquee-track'), anim = null, lastY = window.scrollY, vel = 0;
  function loop() {
    var y = window.scrollY, dy = y - lastY; lastY = y; vel += (Math.abs(dy) - vel) * .12;
    var h = document.documentElement.scrollHeight - window.innerHeight;
    fg.style.strokeDashoffset = C * (1 - (h > 0 ? y / h : 0)); up.classList.toggle('show', y > 600);
    if (mq && !reduce) { if (!anim && mq.getAnimations) anim = mq.getAnimations()[0]; if (anim) anim.playbackRate = 1 + Math.min(vel * .6, 7); mq.style.transform = ''; mq.parentNode.style.setProperty('--skew', (Math.max(-6, Math.min(6, dy * .12))).toFixed(2) + 'deg'); }
    requestAnimationFrame(loop);
  }
  requestAnimationFrame(loop);

  /* 6) custom cursor ring */
  if (fine && !reduce) {
    var ring = document.createElement('div'), dot = document.createElement('div'); ring.className = 'cur-ring'; dot.className = 'cur-dot';
    document.body.appendChild(ring); document.body.appendChild(dot);
    var mx = -100, my = -100, rx = -100, ry = -100;
    window.addEventListener('mousemove', function (e) { mx = e.clientX; my = e.clientY; dot.style.transform = 'translate(' + mx + 'px,' + my + 'px)'; document.body.classList.add('cur-on'); }, { passive: true });
    document.addEventListener('mouseleave', function () { document.body.classList.remove('cur-on'); });
    (function f() { rx += (mx - rx) * .16; ry += (my - ry) * .16; ring.style.transform = 'translate(' + rx.toFixed(1) + 'px,' + ry.toFixed(1) + 'px)'; requestAnimationFrame(f); })();
    document.addEventListener('mouseover', function (e) {
      var t = e.target.closest && e.target.closest('a,button,select,input,textarea,.spot'); ring.classList.toggle('big', !!t);
    });
    document.addEventListener('mousedown', function () { ring.classList.add('press'); }); document.addEventListener('mouseup', function () { ring.classList.remove('press'); });
  }

  /* 7) hero particle network */
  var hb = document.querySelector('.hero-bg');
  if (hb && !reduce) {
    var cv = document.createElement('canvas'); cv.className = 'hero-cv'; hb.appendChild(cv); var cx = cv.getContext('2d'), W, H, pts = [], mp = { x: -999, y: -999 }, vis = true;
    var N = window.innerWidth < 768 ? 28 : 64;
    function size() { var r = hb.getBoundingClientRect(), d = Math.min(window.devicePixelRatio || 1, 2); W = r.width; H = r.height; cv.width = W * d; cv.height = H * d; cx.setTransform(d, 0, 0, d, 0, 0); }
    size(); window.addEventListener('resize', size);
    for (var i = 0; i < N; i++) pts.push({ x: Math.random() * 1400, y: Math.random() * 700, vx: (Math.random() - .5) * .35, vy: (Math.random() - .5) * .35, r: Math.random() * 1.6 + .6 });
    hb.parentNode.addEventListener('mousemove', function (e) { var r = hb.getBoundingClientRect(); mp.x = e.clientX - r.left; mp.y = e.clientY - r.top; });
    if ('IntersectionObserver' in window) new IntersectionObserver(function (en) { vis = en[0].isIntersecting; }).observe(hb);
    var col = getComputedStyle(document.documentElement).getPropertyValue('--brand').trim() || '#0287BF';
    (function draw() {
      requestAnimationFrame(draw); if (!vis) return; cx.clearRect(0, 0, W, H);
      for (var i = 0; i < pts.length; i++) {
        var p = pts[i]; p.x += p.vx; p.y += p.vy; if (p.x < 0 || p.x > W) p.vx *= -1; if (p.y < 0 || p.y > H * .8) p.vy *= -1;
        var dx = p.x - mp.x, dy = p.y - mp.y, d2 = dx * dx + dy * dy; if (d2 < 14000) { var f = (1 - d2 / 14000) * .9; p.x += dx / Math.sqrt(d2 + 1) * f; p.y += dy / Math.sqrt(d2 + 1) * f; }
        cx.beginPath(); cx.arc(p.x, p.y, p.r, 0, 6.283); cx.fillStyle = col; cx.globalAlpha = .45; cx.fill();
        for (var j = i + 1; j < pts.length; j++) { var q = pts[j], ax = p.x - q.x, ay = p.y - q.y, dd = ax * ax + ay * ay; if (dd < 11000) { cx.globalAlpha = (1 - dd / 11000) * .22; cx.strokeStyle = col; cx.lineWidth = 1; cx.beginPath(); cx.moveTo(p.x, p.y); cx.lineTo(q.x, q.y); cx.stroke(); } }
      }
      cx.globalAlpha = 1;
    })();
  }

  /* 8) live call chip: ringing -> connected timer -> ringing */
  var chip = document.getElementById('chipCall');
  if (chip) {
    var st = document.getElementById('ccState'), sec = 0, ringing = true, n = 0;
    setInterval(function () {
      n++;
      if (ringing && n >= 4) { ringing = false; n = 0; sec = 0; chip.classList.add('live'); }
      else if (!ringing && n >= 9) { ringing = true; n = 0; chip.classList.remove('live'); }
      if (ringing) st.textContent = 'Incoming call'; else { sec++; st.textContent = 'Connected · 00:0' + sec; }
    }, 1000);
  }
})();
