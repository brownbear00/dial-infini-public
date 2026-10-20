(function () {
  var csrf = document.body.dataset.csrf, base = document.body.dataset.base || '';
  var uid = 0;
  function $(s, r) { return (r || document).querySelector(s); }
  function $$(s, r) { return [].slice.call((r || document).querySelectorAll(s)); }

  /* list fields */
  document.addEventListener('click', function (e) {
    var t = e.target.closest('button'); if (!t) return;
    var lf = t.closest('.listfield');
    if (t.hasAttribute('data-add') && lf) {
      var tpl = $('template', lf), rows = $('.list-rows', lf);
      var html = tpl.innerHTML.replace(/__IDX__/g, 'n' + Date.now() + (uid++));
      var d = document.createElement('div'); d.innerHTML = html; var row = d.firstElementChild; rows.appendChild(row);
      row.scrollIntoView({ behavior: 'smooth', block: 'center' }); var f = $('input,textarea', row); if (f) f.focus();
    }
    var row = t.closest('.list-row');
    if (row && t.hasAttribute('data-del')) { if (confirm('Delete this item?')) row.remove(); }
    if (row && t.hasAttribute('data-up') && row.previousElementSibling) row.parentNode.insertBefore(row, row.previousElementSibling);
    if (row && t.hasAttribute('data-down') && row.nextElementSibling) row.parentNode.insertBefore(row.nextElementSibling, row);
    if (t.hasAttribute('data-copy')) { navigator.clipboard && navigator.clipboard.writeText(t.dataset.copy); var o = t.innerHTML; t.innerHTML = '<i class="bi bi-check2"></i> Copied'; setTimeout(function () { t.innerHTML = o; }, 1200); }
  });
  document.addEventListener('input', function (e) {
    var row = e.target.closest('.list-row'); if (!row) return;
    var first = $('input[type=text],input:not([type])', row); if (first && e.target === first) $('.lr-title', row).textContent = first.value || 'New item';
  });

  /* image picker */
  var picking = null, modalEl = $('#mediaModal'), modal = modalEl && new bootstrap.Modal(modalEl);
  function loadMedia() {
    fetch(base + '/admin/media.php?action=list').then(function (r) { return r.json(); }).then(function (j) {
      var g = $('#pickGrid'); g.innerHTML = '';
      j.files.forEach(function (f) {
        var d = document.createElement('div'); d.className = 'media-item pick';
        d.innerHTML = '<div class="mi-img"><img loading="lazy"></div><div class="mi-meta"><div class="mi-name"></div></div>';
        $('img', d).src = f.url; $('.mi-name', d).textContent = f.name;
        d.addEventListener('click', function () { setImg(picking, f.path, f.url); modal.hide(); });
        g.appendChild(d);
      });
    });
  }
  function setImg(wrap, path, url) {
    if (!wrap) return; $('input', wrap).value = path;
    var th = $('.thumb', wrap); th.innerHTML = path ? '<img src="' + (url || (base + '/' + path)) + '" alt="">' : '<i class="bi bi-image"></i>';
  }
  document.addEventListener('click', function (e) {
    var b = e.target.closest('[data-pick]'), c = e.target.closest('[data-clear]');
    if (b) { picking = b.closest('.imgfield'); loadMedia(); modal.show(); }
    if (c) setImg(c.closest('.imgfield'), '', '');
  });
  document.addEventListener('change', function (e) {
    if (e.target.id === 'pickUpload') {
      var fd = new FormData(); fd.append('action', 'upload');
      [].forEach.call(e.target.files, function (f) { fd.append('files[]', f); });
      fetch(base + '/admin/media.php', { method: 'POST', body: fd, headers: { 'X-CSRF': csrf } }).then(function (r) { return r.json(); }).then(function (j) {
        if (j.errors && j.errors.length) alert(j.errors.join('\n'));
        if (j.saved && j.saved.length) { loadMedia(); }
      });
      e.target.value = '';
    }
    if (e.target.matches('.imgfield input.form-control')) setImg(e.target.closest('.imgfield'), e.target.value, '');
  });

  /* remember open section / tab */
  $$('[data-key]').forEach(function (b) { b.addEventListener('click', function () { var o = $('#openSec'); if (o) o.value = b.dataset.key; }); });
  var tabs = $$('[data-tab]');
  if (tabs.length) {
    var h = location.hash.replace('#', '');
    tabs.forEach(function (t) {
      if (t.dataset.tab === h) new bootstrap.Tab(t).show();
      t.addEventListener('shown.bs.tab', function () { var k = $('#tabKey'); if (k) k.value = t.dataset.tab; history.replaceState(null, '', '#' + t.dataset.tab); });
    });
  }
  if (location.hash && $('.sec-acc') && !tabs.length) {
    var s = $(location.hash.replace('#', '#sec-')); // content page: #hero => #sec-hero
    if (s) { var c = $('.accordion-collapse', s); new bootstrap.Collapse(c, { toggle: false }).show(); setTimeout(function () { s.scrollIntoView({ behavior: 'smooth' }); }, 200); }
  }

  /* SEO counters + SERP preview */
  $$('.seo-block').forEach(function (blk) {
    var ti = $('input[name$="[title]"]', blk), de = $('textarea[name$="[description]"]', blk), st = $('[data-serp-title]', blk), sd = $('[data-serp-desc]', blk);
    function mk(inp, max) { var s = document.createElement('div'); s.className = 'form-text counter'; inp.parentNode.appendChild(s); return s; }
    var ct = mk(ti), cd = mk(de);
    function up() {
      ct.textContent = ti.value.length + ' characters' + (ti.value.length > 60 ? ' — a bit long' : ''); ct.className = 'form-text counter' + (ti.value.length > 60 ? ' warn' : '');
      cd.textContent = de.value.length + ' characters' + (de.value.length > 160 ? ' — a bit long' : ''); cd.className = 'form-text counter' + (de.value.length > 160 ? ' warn' : '');
      st.textContent = ti.value || '(page title)'; sd.textContent = de.value || '(meta description)';
    }
    ti.addEventListener('input', up); de.addEventListener('input', up); up();
  });
})();
