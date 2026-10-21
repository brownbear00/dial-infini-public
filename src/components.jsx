import { useLayoutEffect } from 'react'
import {
  asset, href, rich, nl, lines, settings, seo, hexOk, shade, fullTitle,
  contactEntries, iconLink,
} from './lib.js'

export function StoreButtons({ className = '' }) {
  const a = settings.appstore_url
  const g = settings.playstore_url
  if (!a && !g) return null
  return (
    <div className={`store-btns ${className}`.trim()}>
      {a ? <a href={a} target="_blank" rel="noopener"><i className="bi bi-apple"></i><span><small>Download on the</small>App Store</span></a> : null}
      {g ? <a href={g} target="_blank" rel="noopener"><i className="bi bi-google-play"></i><span><small>Get it on</small>Google Play</span></a> : null}
    </div>
  )
}

export function SectionTitle({ title, text = '', eyebrow = '' }) {
  return (
    <div className="section-title reveal">
      {eyebrow ? <span className="eyebrow"><i></i>{eyebrow}</span> : null}
      <h2 dangerouslySetInnerHTML={{ __html: rich(title) }} />
      {text ? <p>{text}</p> : null}
    </div>
  )
}

export function PageHero({ d }) {
  return (
    <section className="page-hero">
      <div className="container reveal">
        <div className="page-hero-copy">
          {d.eyebrow ? <span className="eyebrow"><i></i>{d.eyebrow}</span> : null}
          <h1 dangerouslySetInnerHTML={{ __html: rich(d.title) }} />
          <p className="lead">{d.text}</p>
        </div>
      </div>
    </section>
  )
}

export function CtaBand({ title, text, b1t, b1l, b2t = '', b2l = '', img = '' }) {
  return (
    <section className="section pt-0">
      <div className="container">
        <div className="cta-band reveal">
          <div className="row align-items-center g-4">
            <div className={`${img ? 'col-lg-7' : 'col-lg-12 text-center'}`}>
              <h2 dangerouslySetInnerHTML={{ __html: rich(title) }} />
              <p>{text}</p>
              <div className={`d-flex flex-wrap gap-3 ${img ? '' : 'justify-content-center'}`}>
                {b1t ? <a className="btn btn-white btn-lg-pill" href={href(b1l)}>{b1t}</a> : null}
                {b2t ? <a className="btn btn-outline-white btn-lg-pill" href={href(b2l)}>{b2t}</a> : null}
              </div>
              <StoreButtons className="mt-4" />
            </div>
            {img ? <div className="col-lg-5"><img className="cta-img" src={asset(img)} alt="" loading="lazy" /></div> : null}
          </div>
        </div>
      </div>
    </section>
  )
}

export function FeatureRows({ items }) {
  return items.map((it, i) => (
    <section className="section feature-row" key={i}>
      <div className="container">
        <div className={`row align-items-center g-5 ${i % 2 === 1 ? 'flex-lg-row-reverse' : ''}`}>
          <div className="col-lg-5 reveal">
            {it.tag ? <span className="eyebrow"><i></i>{it.tag}</span> : null}
            <h2 className="h1-sm" dangerouslySetInnerHTML={{ __html: rich(it.title) }} />
            <p className="lead-sm">{it.text}</p>
            {it.bullets ? (
              <ul className="check-list">
                {lines(it.bullets).map((b) => <li key={b}><i className="bi bi-check-circle-fill"></i>{b}</li>)}
              </ul>
            ) : null}
          </div>
          <div className="col-lg-7 reveal">
            <div className={`shot${String(it.image || '').includes('crop') ? ' narrow' : ''}`}>
              <img src={asset(it.image)} alt={it.title || ''} loading="lazy" />
            </div>
          </div>
        </div>
      </div>
    </section>
  ))
}

export function IconCards({ items, cols = 'col-md-6 col-lg-3' }) {
  return (
    <div className="row g-4">
      {items.map((it) => (
        <div className={`${cols} reveal`} key={it.title}>
          <div className="icon-card">
            <div className="ic"><i className={`bi ${it.icon || 'bi-stars'}`}></i></div>
            <h3>{it.title}</h3>
            <p>{it.text}</p>
          </div>
        </div>
      ))}
    </div>
  )
}

function ContactValue({ type, value }) {
  if (type === 'email') return <a href={`mailto:${value}`}>{value}</a>
  if (type === 'phone') return <a href={`tel:${value.replace(/[^+\d]/g, '')}`}>{value}</a>
  return <span dangerouslySetInnerHTML={{ __html: nl(value) }} />
}

export function ContactRows({ ctx = 'footer' }) {
  const map = [['email', 'bi-envelope', 'Email'], ['phone', 'bi-telephone', 'Phone'], ['address', 'bi-geo-alt', 'Address']]
  const rows = []
  for (const [type, ic, def] of map) {
    for (const [label, v] of contactEntries(type)) {
      rows.push(
        ctx === 'contact' ? (
          <li key={type + v}>
            <i className={`bi ${ic}`}></i>
            <div><small>{label || def}</small><ContactValue type={type} value={v} /></div>
          </li>
        ) : (
          <li key={type + v}>
            <i className={`bi ${ic}`}></i>
            <span>{label ? <small className="fc-label">{label}</small> : null}<ContactValue type={type} value={v} /></span>
          </li>
        )
      )
    }
  }
  return rows
}

function currentPath(page) {
  return page === 'home' ? '/' : `/${page}`
}

export function Layout({ page, children }) {
  const name = settings.site_name || ''
  const logo = asset(settings.logo || 'assets/img/logo.svg')
  const footerLogo = String(settings.footer_logo || '').trim()
  const fh = Math.max(24, Math.min(120, parseInt(settings.footer_logo_height, 10) || 56))
  const nav = settings.nav || []

  useLayoutEffect(() => {
    document.body.className = page === 'home' ? 'page-home' : 'page-inner'
    document.body.dataset.base = ''
    const key = page === '404' ? 'home' : page
    document.title = fullTitle(key)
    const desc = seo[key]?.description || ''
    let md = document.querySelector('meta[name="description"]')
    if (!md) {
      md = document.createElement('meta')
      md.setAttribute('name', 'description')
      document.head.appendChild(md)
    }
    md.setAttribute('content', desc)
    let robots = document.querySelector('meta[name="robots"]')
    if (!robots) {
      robots = document.createElement('meta')
      robots.setAttribute('name', 'robots')
      document.head.appendChild(robots)
    }
    robots.setAttribute('content', page === '404' ? 'noindex,follow' : (seo[key]?.robots || 'index,follow'))
    const p = hexOk(settings.color_primary, '#0287BF')
    const bg = hexOk(settings.color_bg, '#F5F7FB')
    const dk = hexOk(settings.color_dark, '#101828')
    let style = document.getElementById('theme-vars')
    if (!style) {
      style = document.createElement('style')
      style.id = 'theme-vars'
      document.head.appendChild(style)
    }
    style.textContent = `:root{--brand:${p};--brand-dark:${shade(p, -0.18)};--brand-light:${shade(p, 0.35)};--brand-soft:${shade(p, 0.9)};--bg:${bg};--ink:${dk};}${settings.custom_css || ''}`
    const theme = document.querySelector('meta[name="theme-color"]')
    if (theme) theme.setAttribute('content', p)
  }, [page])

  return (
    <>
      <div className="loader" id="loader" aria-hidden="true">
        <div className="ld-in">
          <img src={logo} alt="" height="44" />
          <span>{name}</span>
          <i className="ld-bar"></i>
        </div>
      </div>
      {settings.topbar_on && settings.topbar_text ? (
        <div className="topbar">
          <div className="container">
            <span>{settings.topbar_text}</span>
            {settings.topbar_link_text ? <a href={href(settings.topbar_link)}>{settings.topbar_link_text} <i className="bi bi-arrow-right"></i></a> : null}
          </div>
        </div>
      ) : null}
      <header className="site-header" id="siteHeader">
        <nav className="navbar navbar-expand-lg">
          <div className="container">
            <a className="brand" href="/" aria-label={name}><img src={logo} alt="" height="30" /><span>{name}</span></a>
            <button className="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileNav" aria-label="Menu"><i className="bi bi-list"></i></button>
            <div className="collapse navbar-collapse">
              <ul className="navbar-nav mx-auto">
                {nav.map((n) => {
                  const h = href(n.link)
                  const active = h === currentPath(page === '404' ? 'home' : page) || (page === 'home' && h === '/')
                  return <li className="nav-item" key={n.label}><a className={`nav-link${active ? ' active' : ''}`} href={h}>{n.label}</a></li>
                })}
              </ul>
              <div className="nav-actions">
                {settings.header_link_text && settings.header_link ? (
                  <a className="nav-alt" href={href(settings.header_link)} {...(/^https?:\/\//.test(href(settings.header_link)) ? { target: '_blank', rel: 'noopener' } : {})}>
                    {settings.header_link_text}{/^https?:\/\//.test(href(settings.header_link)) ? <> <i className="bi bi-arrow-up-right"></i></> : null}
                  </a>
                ) : null}
                {settings.header_cta_text ? <a className="btn btn-brand btn-sm-pill nav-cta" href={href(settings.header_cta_link)}>{settings.header_cta_text} <i className="bi bi-arrow-right"></i></a> : null}
              </div>
            </div>
          </div>
        </nav>
      </header>
      <div className="offcanvas offcanvas-end" tabIndex="-1" id="mobileNav">
        <div className="offcanvas-header">
          <a className="brand" href="/"><img src={logo} alt="" height="26" /><span>{name}</span></a>
          <button type="button" className="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div className="offcanvas-body">
          <ul className="list-unstyled mobile-links">
            {nav.map((n) => <li key={n.label}><a href={href(n.link)}>{n.label}</a></li>)}
          </ul>
          {settings.header_cta_text ? <a className="btn btn-brand w-100 mt-3" href={href(settings.header_cta_link)}>{settings.header_cta_text}</a> : null}
        </div>
      </div>
      <main>{children}</main>
      <footer className="site-footer">
        <div className="container">
          <div className="row g-5">
            <div className="col-lg-3">
              <a className={`brand brand-light foot-logo${footerLogo ? '' : ' foot-logo-badge'}`} href="/" aria-label={name || 'Home'}>
                <img src={asset(footerLogo || settings.logo || 'assets/img/logo.svg')} alt="" style={{ height: `${footerLogo ? fh : Math.min(fh, 40)}px` }} />
                {name ? <span>{name}</span> : null}
              </a>
              <p className="mt-3 foot-about">{settings.footer_about}</p>
              <div className="socials">
                {['facebook', 'instagram', 'x_twitter', 'linkedin', 'youtube'].filter((s) => settings[s]).map((s) => (
                  <a key={s} href={settings[s]} target="_blank" rel="noopener" aria-label={s}><i className={`bi ${iconLink(s)}`}></i></a>
                ))}
              </div>
              <StoreButtons className="mt-3" />
            </div>
            {[1, 2, 3].map((i) => {
              const links = settings[`footer_links_${i}`]
              if (!links?.length) return null
              return (
                <div className="col-6 col-lg-2" key={i}>
                  <h6>{settings[`footer_links_${i}_title`]}</h6>
                  <ul className="list-unstyled">
                    {links.map((l) => <li key={l.label}><a href={href(l.link)}>{l.label}</a></li>)}
                  </ul>
                </div>
              )
            })}
            <div className="col-12 col-lg-3">
              <h6>Contact</h6>
              <ul className="list-unstyled foot-contact">
                <ContactRows ctx="footer" />
              </ul>
            </div>
          </div>
          <div className="foot-mega" aria-hidden="true">{name}</div>
          <div className="foot-bottom"><span>{String(settings.copyright || '').replace('{year}', String(new Date().getFullYear()))}</span></div>
        </div>
      </footer>
    </>
  )
}
