(function () {
  var reduce = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* wrap every word of an element (keeps inner <span class="hl"> etc.) */
  function wrapWords(el, mk) {
    var n = 0;
    (function walk(node) {
      [].slice.call(node.childNodes).forEach(function (c) {
        if (c.nodeType === 3) {
          var parts = c.textContent.split(/(\s+)/), frag = document.createDocumentFragment();
          parts.forEach(function (p) {
            if (!p) return;
            if (/^\s+$/.test(p)) frag.appendChild(document.createTextNode(' '));
            else frag.appendChild(mk(p, n++));
          });
          node.replaceChild(frag, c);
        } else if (c.nodeType === 1) walk(c);
      });
    })(el);
    return n;
  }

  /* 1) headings: words slide up from a mask when they enter the screen */
  var splitSel = '.hero-title,.page-hero h1,.section-title h2,.h1-sm,.cta-band h2,.statement-title:not(.scrub),.legal h2';
  var splits = [].slice.call(document.querySelectorAll(splitSel));
  splits.forEach(function (h) {
    if (!h.getAttribute('aria-label')) h.setAttribute('aria-label', h.textContent.replace(/\s+/g, ' ').trim());
    if (reduce) return;
    h.classList.add('split');
    wrapWords(h, function (w, i) {
      var o = document.createElement('span'); o.className = 'sw'; o.setAttribute('aria-hidden', 'true');
      var s = document.createElement('i'); s.textContent = w; s.style.setProperty('--i', i); o.appendChild(s); return o;
    });
  });
  if (!reduce) {
    var hio = 'IntersectionObserver' in window ? new IntersectionObserver(function (en) {
      en.forEach(function (x) { if (x.isIntersecting) { x.target.classList.add('in'); hio.unobserve(x.target); } });
    }, { threshold: .25 }) : null;
    splits.forEach(function (h) {
      if (h.classList.contains('hero-title')) setTimeout(function () { h.classList.add('in'); }, document.documentElement.classList.contains('no-loader') ? 150 : 1150);
      else if (hio) hio.observe(h); else h.classList.add('in');
    });
  }

  /* 2) scroll-scrub: words change colour as you scroll past the heading */
  var scrubs = [].slice.call(document.querySelectorAll('.scrub')).map(function (el) {
    var words = [];
    wrapWords(el, function (w) { var s = document.createElement('span'); s.className = 'sc'; s.textContent = w; words.push(s); return s; });
    return { el: el, words: words };
  });
  function scrubTick() {
    var vh = window.innerHeight;
    scrubs.forEach(function (o) {
      var r = o.el.getBoundingClientRect();
      var p = (vh * .88 - r.top) / (vh * .55 + r.height * .6);
      p = Math.max(0, Math.min(1, p));
      var on = Math.round(p * o.words.length);
      o.words.forEach(function (w, i) { w.classList.toggle('on', i < on); });
    });
  }
  if (scrubs.length && !reduce) { scrubTick(); window.addEventListener('scroll', function () { requestAnimationFrame(scrubTick); }, { passive: true }); window.addEventListener('resize', scrubTick); }
  else scrubs.forEach(function (o) { o.words.forEach(function (w) { w.classList.add('on'); }); });

  var h = document.getElementById('siteHeader');
  function sc() { if (h) h.classList.toggle('scrolled', window.scrollY > 8); }
  sc(); window.addEventListener('scroll', sc, { passive: true });

  var els = document.querySelectorAll('.reveal');
  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (en) {
      en.forEach(function (x) { if (x.isIntersecting) { x.target.classList.add('in'); io.unobserve(x.target); } });
    }, { threshold: .12, rootMargin: '0px 0px -40px 0px' });
    els.forEach(function (el) { io.observe(el); });
  } else els.forEach(function (el) { el.classList.add('in'); });

  var form = document.getElementById('contactForm');
  if (form) form.addEventListener('submit', function (e) {
    e.preventDefault();
    var msg = document.getElementById('formMsg'), btn = form.querySelector('button[type=submit]'), lbl = btn.querySelector('.lbl'), old = lbl.textContent;
    msg.className = 'form-msg';
    var n = form.name.value.trim(), em = form.email.value.trim(), m = form.message.value.trim();
    if (!n || !/^\S+@\S+\.\S+$/.test(em) || !m) { msg.textContent = 'Please fill in your name, a valid email and a message.'; msg.className = 'form-msg err'; return; }
    btn.disabled = true; lbl.textContent = 'Sending…';
    fetch(form.action, { method: 'POST', body: new FormData(form), headers: { 'X-Requested-With': 'fetch' } })
      .then(function (r) { return r.json(); })
      .then(function (j) {
        if (j.ok) { msg.textContent = msg.dataset.success; msg.className = 'form-msg ok'; form.reset(); }
        else { msg.textContent = j.error || 'Something went wrong. Please try again.'; msg.className = 'form-msg err'; }
      })
      .catch(function () { msg.textContent = 'Network error. Please try again.'; msg.className = 'form-msg err'; })
      .finally(function () { btn.disabled = false; lbl.textContent = old; });
  });
})();
