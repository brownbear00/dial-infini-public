import { lines } from './lib.js'

export function calcConfig(cc) {
  const rows = (t) => lines(t).map((l) => l.split('|').map((s) => s.trim()))
  return {
    phones: rows(cc.phones),
    rates: rows(cc.rates),
    fx: { USD: 1, EUR: parseFloat(cc.eur) || 0.92, GBP: parseFloat(cc.gbp) || 0.79 },
    link: cc.cta_link?.startsWith('/') ? '/' + cc.cta_link.slice(1) : (cc.cta_link || '#'),
    pl: cc.phone_link_text,
    rl: cc.rates_link_text,
  }
}

export function initCalc() {
  const root = document.getElementById('calc')
  if (!root || root.dataset.ready) return
  root.dataset.ready = '1'
  const C = JSON.parse(root.dataset.cfg)
  let cur = 'USD', term = 2, pi = 0, ri = 0
  const M = [1, 3, 12], U = ['/month', '/3 months', '/year']
  const sym = { USD: '$', EUR: '€', GBP: '£' }, cu = { USD: '¢', EUR: 'c', GBP: 'p' }
  const $ = (i) => document.getElementById(i)
  const flag = (c) => 'https://flagcdn.com/w40/' + String(c).toLowerCase() + '.png'
  function fill(sel, arr) {
    arr.forEach((r, i) => {
      const o = document.createElement('option')
      o.value = i
      o.textContent = r[1]
      sel.appendChild(o)
    })
  }
  fill($('pSel'), C.phones)
  fill($('rSel'), C.rates)
  const money = (v) => sym[cur] + (v * C.fx[cur]).toFixed(2)
  let shown = 0, raf
  function tween(el, to, fmt) {
    cancelAnimationFrame(raf)
    const from = shown
    let t0 = null
    ;(function s(ts) {
      if (!t0) t0 = ts
      const p = Math.min(1, (ts - t0) / 450), e = 1 - Math.pow(1 - p, 3)
      shown = from + (to - from) * e
      el.textContent = fmt(shown)
      if (p < 1) raf = requestAnimationFrame(s)
    })(performance.now())
  }
  function drawPhone() {
    const r = C.phones[pi]
    if (!r) return
    const p = [+r[2], +r[3], +r[4]], m = M[term], tot = p[term], per = tot / m, base = p[0] * m
    $('pFlag').src = flag(r[0])
    ;[].forEach.call(document.querySelectorAll('#seg button'), (b, i) => {
      const sv = i ? Math.round((1 - p[i] / (p[0] * M[i])) * 100) : 0
      b.querySelector('small').textContent = sv > 0 ? 'Save ' + sv + '%' : ''
      b.querySelector('small').style.visibility = sv > 0 ? 'visible' : 'hidden'
    })
    $('tilde').style.display = m > 1 ? '' : 'none'
    tween($('pPrice'), per * C.fx[cur], (v) => sym[cur] + v.toFixed(2))
    $('pUnit').textContent = '/month'
    const sv = Math.round((1 - tot / base) * 100)
    $('pSub').innerHTML = m > 1 ? ((sv > 0 ? '<s>' + money(base) + '</s> ' : '') + '<span>' + money(tot) + U[term] + '</span>') : '<span>Billed monthly, cancel anytime</span>'
    $('pNote').querySelector('span').textContent = r[5] || ''
    $('pNote').style.display = r[5] ? '' : 'none'
    const l = $('pLink')
    l.href = C.link
    l.innerHTML = C.pl.replace('{country}', r[1]) + ' &rarr;'
  }
  function drawRates() {
    const r = C.rates[ri]
    if (!r) return
    const f = (v) => (+v * C.fx[cur]).toFixed(1).replace(/\.0$/, '')
    $('rFlag').src = flag(r[0])
    $('rM').textContent = f(r[2])
    $('rL').textContent = f(r[3])
    $('rS').textContent = f(r[4])
    ;[].forEach.call(document.querySelectorAll('.ru'), (e) => { e.textContent = cu[cur] + '/Min' })
    document.querySelector('.ru2').textContent = cu[cur] + '/Msg'
    const l = $('rLink')
    l.href = C.link
    l.innerHTML = C.rl.replace('{country}', r[1]) + ' &rarr;'
  }
  function thumb(box, sel, th) {
    const b = box.querySelector(sel)
    if (!b) return
    th.style.width = b.offsetWidth + 'px'
    th.style.transform = 'translateX(' + b.offsetLeft + 'px)'
  }
  const cs = root.querySelector('.cur-switch'), sg = $('seg')
  function place() {
    thumb(cs, 'button.on', cs.querySelector('.cur-thumb'))
    thumb(sg, 'button.on', sg.querySelector('.seg-thumb'))
  }
  cs.addEventListener('click', (e) => {
    const b = e.target.closest('button')
    if (!b) return
    cur = b.dataset.cur
    ;[].forEach.call(cs.querySelectorAll('button'), (x) => x.classList.toggle('on', x === b))
    place(); drawPhone(); drawRates()
  })
  sg.addEventListener('click', (e) => {
    const b = e.target.closest('button')
    if (!b) return
    term = +b.dataset.t
    ;[].forEach.call(sg.querySelectorAll('button'), (x) => x.classList.toggle('on', x === b))
    place(); drawPhone()
  })
  $('pSel').addEventListener('change', function () { pi = +this.value; drawPhone() })
  $('rSel').addEventListener('change', function () { ri = +this.value; drawRates() })
  drawPhone(); drawRates(); place()
  window.addEventListener('resize', place)
  if (document.fonts && document.fonts.ready) document.fonts.ready.then(place)
  setTimeout(place, 300)
}
