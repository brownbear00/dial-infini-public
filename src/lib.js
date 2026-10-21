import site from './data/site.json'

export const settings = site.settings
export const pages = site.pages
export const seo = site.seo
export const meta = site.meta

export function asset(p) {
  if (!p) return ''
  const s = String(p)
  if (/^(https?:)?\/\//.test(s) || s[0] === '#' || s.startsWith('data:')) return s
  return '/' + s.replace(/^\/+/, '').split('/').map((part) => encodeURIComponent(part)).join('/')
}

export function href(l) {
  const s = String(l ?? '').trim()
  if (!s) return '#'
  if (s[0] === '/' && s.slice(0, 2) !== '//') return '/' + s.slice(1).replace(/^\/+|\/+$/g, '')
  return s
}

function esc(s) {
  return String(s ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
}

export function rich(s) {
  return esc(s).replace(/\*(.+?)\*/gs, '<span class="hl">$1</span>')
}

export function nl(s) {
  return esc(s).replace(/\r\n|\n|\r/g, '<br />\n')
}

export function lines(s) {
  return String(s ?? '').split(/\r?\n/).map((t) => t.trim()).filter(Boolean)
}

export function hexOk(c, d) {
  return /^#[0-9a-fA-F]{6}$/.test(String(c)) ? c : d
}

export function shade(hex, pct) {
  const h = String(hex).replace('#', '')
  let o = '#'
  for (const i of [0, 2, 4]) {
    let v = parseInt(h.substr(i, 2), 16)
    v = Math.max(0, Math.min(255, Math.round(pct >= 0 ? v + (255 - v) * pct : v * (1 + pct))))
    o += v.toString(16).padStart(2, '0')
  }
  return o
}

export function fullTitle(pageKey) {
  const name = settings.site_name || ''
  const title = seo[pageKey]?.title || meta[pageKey]?.label || name
  if (name === '' || title.toLowerCase().includes(name.toLowerCase())) return title
  return title + (settings.title_suffix || '')
}

export function contactEntries(type) {
  const out = []
  const main = String(settings[type] || '').trim()
  if (main) out.push(['', main])
  const key = 'more_' + type + (type === 'address' ? 'es' : 's')
  for (const r of settings[key] || []) {
    const v = String(r?.value || '').trim()
    if (v) out.push([String(r?.label || '').trim(), v])
  }
  return out
}

const ICONS = { facebook: 'bi-facebook', instagram: 'bi-instagram', x_twitter: 'bi-twitter-x', linkedin: 'bi-linkedin', youtube: 'bi-youtube' }
export function iconLink(name) {
  return ICONS[name] || 'bi-link'
}
