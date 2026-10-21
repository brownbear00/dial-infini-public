import { pages, settings, asset, href, rich, nl, lines } from './lib.js'
import { PageHero, SectionTitle, CtaBand, FeatureRows, IconCards, ContactRows } from './components.jsx'
import { calcConfig } from './calc.js'

export function HomePage() {
  const h = pages.home
  const hero = h.hero
  const stats = h.stats
  const products = h.products
  const mq = [...h.usecases.items.map((i) => i.title), ...products.items.map((i) => i.title)]
  const how = h.how
  const pricing = h.pricing
  const cta = h.cta
  return (
    <>
      <section className="hero">
        <div className="hero-bg"></div>
        <div className="container position-relative">
          <div className="row align-items-center gy-5">
            <div className="col-lg-6">
              {hero.eyebrow ? <span className="eyebrow"><i></i>{hero.eyebrow}</span> : null}
              <h1 className="hero-title" dangerouslySetInnerHTML={{ __html: rich(hero.title) }} />
              <p className="hero-text">{hero.text}</p>
              <div className="d-flex flex-wrap gap-2">
                {hero.cta1_text ? <a className="btn btn-brand btn-lg-pill" href={href(hero.cta1_link)}>{hero.cta1_text}</a> : null}
                {hero.cta2_text ? <a className="btn btn-ghost btn-lg-pill" href={href(hero.cta2_link)}>{hero.cta2_text}</a> : null}
              </div>
              {hero.note ? <p className="hero-note"><i className="bi bi-shield-check"></i> {hero.note}</p> : null}
            </div>
            <div className="col-lg-6">
              <div className="hero-visual">
                <div className="chip chip-call" id="chipCall" aria-hidden="true">
                  <span className="chip-av"><i className="bi bi-telephone-fill"></i><b></b><b></b></span>
                  <span className="chip-tx"><small id="ccState">Incoming call</small><strong>{settings.site_name}</strong></span>
                  <span className="eq" id="ccEq"><i></i><i></i><i></i><i></i><i></i></span>
                </div>
                <div className="chip chip-sms" aria-hidden="true">
                  <span className="chip-av chip-g"><i className="bi bi-chat-dots-fill"></i></span>
                  <span className="chip-tx"><small>New SMS</small><strong>Delivered <i className="bi bi-check2-all"></i></strong></span>
                </div>
                <div className="shot shot-main"><img src={asset(hero.img_main)} alt="Dial Infini messages screen" /></div>
                {hero.img_phone1 ? <div className="phone phone-l"><img src={asset(hero.img_phone1)} alt="Dial pad screen" /></div> : null}
                {hero.img_phone2 ? <div className="phone phone-r"><img src={asset(hero.img_phone2)} alt="Call anyone screen" /></div> : null}
              </div>
            </div>
          </div>
        </div>
      </section>

      <section className="section statement">
        <div className="container">
          {stats.kicker ? <span className="eyebrow reveal"><i></i>{stats.kicker}</span> : null}
          <h2 className="statement-title scrub" aria-label={String(stats.title).replace(/\*/g, '')} dangerouslySetInnerHTML={{ __html: rich(stats.title) }} />
          <div className="stat-row">
            {stats.items.map((it) => (
              <div className="stat-item reveal" key={it.value + it.label}>
                <div className="stat-v">{it.value}</div>
                <p>{it.label}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {mq.length ? (
        <div className="marquee" aria-hidden="true">
          <div className="marquee-track">
            {[0, 1].flatMap((k) => mq.map((w, i) => <span key={`${k}-${i}`}><i className="bi bi-asterisk"></i>{w}</span>))}
          </div>
        </div>
      ) : null}

      <section className="section products-sec" id="products">
        <div className="container">
          <SectionTitle title={products.title} text={products.text} />
          <div className="stack">
            {products.items.map((it, n) => (
              <a className={`scard pc-${n % 4}`} style={{ '--i': n }} href={href(it.link)} key={it.title}>
                <div className="scard-txt">
                  <span className="scard-no">0{n + 1} <em>/ 0{products.items.length}</em></span>
                  <span className="tag">{it.tag}</span>
                  <h3>{it.title}</h3>
                  <p>{it.text}</p>
                  <span className="btn btn-brand btn-sm-pill">{it.link_text || 'Learn more'} <i className="bi bi-arrow-right"></i></span>
                </div>
                <div className="scard-media"><span className="pc-blob"></span><div className="phone phone-sm"><img src={asset(it.image)} alt="" loading="lazy" /></div></div>
              </a>
            ))}
          </div>
        </div>
      </section>

      <FeatureRows items={h.showcase.items} />

      <section className="section bg-soft" id="how">
        <div className="container">
          <SectionTitle title={how.title} text={how.text} eyebrow="How it works" />
          <div className="loop-track" id="loopTrack">
            <span className="lt-bar"><i></i></span>
            {how.items.map((it, i) => <span className="lt-node" key={it.title}><b>{i + 1}</b><em>{it.title}</em></span>)}
          </div>
          <div className="row g-4" id="loopSteps">
            {how.items.map((it, i) => (
              <div className="col-sm-6 col-lg-3 reveal" key={it.title}>
                <div className="step lp">
                  <div className="phone phone-step"><img src={asset(it.image)} alt="" loading="lazy" /></div>
                  <div className="step-n">{i + 1}</div>
                  <h3>{it.title}</h3>
                  <p>{it.text}</p>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      <section className="section">
        <div className="container">
          <SectionTitle title={h.usecases.title} />
          <IconCards items={h.usecases.items} />
        </div>
      </section>

      <section className="section dark-section">
        <div className="container">
          <div className="section-title reveal on-dark">
            <h2 dangerouslySetInnerHTML={{ __html: rich(pricing.title) }} />
            <p>{pricing.text}</p>
          </div>
          <div className="row g-4 justify-content-center">
            {pricing.items.map((it) => (
              <div className="col-md-4 reveal" key={it.title}>
                <div className="rate-card">
                  <div className="rate-v">{it.value}<small>{it.unit}</small></div>
                  <h3>{it.title}</h3>
                  <p>{it.text}</p>
                </div>
              </div>
            ))}
          </div>
          {pricing.cta_text ? <div className="text-center mt-5"><a className="btn btn-brand btn-lg-pill" href={href(pricing.cta_link)}>{pricing.cta_text} <i className="bi bi-arrow-right"></i></a></div> : null}
        </div>
      </section>

      <div className="pt-5"></div>
      <CtaBand title={cta.title} text={cta.text} b1t={cta.cta1_text} b1l={cta.cta1_link} b2t={cta.cta2_text} b2l={cta.cta2_link} img={cta.image} />
    </>
  )
}

export function FeaturesPage() {
  const h = pages.features
  const c = h.cta
  return (
    <>
      <PageHero d={h.hero} />
      <FeatureRows items={h.blocks.items} />
      <section className="section bg-soft">
        <div className="container">
          <SectionTitle title={h.grid.title} />
          <IconCards items={h.grid.items} cols="col-md-6 col-lg-4" />
        </div>
      </section>
      <div className="pt-5"></div>
      <CtaBand title={c.title} text={c.text} b1t={c.cta_text} b1l={c.cta_link} />
    </>
  )
}

export function PricingPage() {
  const h = pages.pricing
  const cc = h.calc
  const included = h.included
  const cr = h.credit
  const f = h.faq
  return (
    <>
      <PageHero d={h.hero} />
      <section className="section calc-sec" id="numbers">
        <div className="container">
          <div className="section-title reveal">
            <h2 dangerouslySetInnerHTML={{ __html: rich(cc.title) }} />
            <p>{cc.text}</p>
          </div>
          <div className="calc" id="calc" data-cfg={JSON.stringify(calcConfig(cc))}>
            <div className="cur-switch reveal" role="tablist" aria-label="Currency"><span className="cur-thumb"></span>
              <button type="button" className="on" data-cur="USD">$ USD</button>
              <button type="button" data-cur="EUR">€ EUR</button>
              <button type="button" data-cur="GBP">£ GBP</button>
            </div>
            <div className="row g-4 reveal">
              <div className="col-lg-6"><div className="cc">
                <div className="cc-head"><span className="cc-ic"><i className="bi bi-telephone"></i></span><div><h3>{cc.phone_title}</h3><p>{cc.phone_sub}</p></div></div>
                <div className="sel"><img className="flag" id="pFlag" alt="" /><select id="pSel" aria-label="Country"></select><i className="bi bi-chevron-down"></i></div>
                <div className="seg" id="seg"><span className="seg-thumb"></span>
                  <button type="button" data-t="0"><b>1 month</b><small></small></button>
                  <button type="button" data-t="1"><b>3 months</b><small></small></button>
                  <button type="button" data-t="2" className="on"><b>12 months</b><small></small></button>
                </div>
                <div className="bigprice"><span className="tilde" id="tilde">~</span><span className="bp-v" id="pPrice">$0.00</span><span className="bp-u" id="pUnit">/month</span></div>
                <div className="bp-sub" id="pSub"></div>
                <div className="cc-pill" id="pNote"><i className="bi bi-check2"></i><span></span></div>
                <a className="cc-link" id="pLink" href="#"></a>
              </div></div>
              <div className="col-lg-6"><div className="cc">
                <div className="cc-head"><span className="cc-ic"><i className="bi bi-currency-dollar"></i></span><div><h3>{cc.rates_title}</h3><p>{cc.rates_sub}</p></div></div>
                <div className="sel"><img className="flag" id="rFlag" alt="" /><select id="rSel" aria-label="Destination"></select><i className="bi bi-chevron-down"></i></div>
                <ul className="rates">
                  <li><span className="ri ri-b"><i className="bi bi-phone"></i></span><span className="rn">Mobile</span><span className="rv"><b id="rM">0</b> <small className="ru">¢/Min</small></span></li>
                  <li><span className="ri ri-o"><i className="bi bi-telephone"></i></span><span className="rn">Landline</span><span className="rv"><b id="rL">0</b> <small className="ru">¢/Min</small></span></li>
                  <li><span className="ri ri-g"><i className="bi bi-chat-square-text"></i></span><span className="rn">SMS</span><span className="rv"><b id="rS">0</b> <small className="ru2">¢/Msg</small></span></li>
                </ul>
                <a className="cc-link" id="rLink" href="#"></a>
              </div></div>
            </div>
            <p className="calc-foot"><i className="bi bi-dot"></i> {cc.footnote}</p>
          </div>
        </div>
      </section>

      <section className="section bg-soft">
        <div className="container">
          <SectionTitle title={included.title} />
          <div className="row g-3">
            {included.items.map((it) => (
              <div className="col-md-6 col-lg-4 reveal" key={it.title}>
                <div className="incl"><i className={`bi ${it.icon}`}></i><div><strong>{it.title}</strong><span>{it.text}</span></div></div>
              </div>
            ))}
          </div>
        </div>
      </section>

      <section className="section">
        <div className="container">
          <div className="row g-5 align-items-center">
            <div className="col-lg-6 text-center reveal"><div className="phone phone-lg"><img src={asset(cr.image)} alt="Credit wallet" loading="lazy" /></div></div>
            <div className="col-lg-6 reveal">
              <h2 className="h1-sm" dangerouslySetInnerHTML={{ __html: rich(cr.title) }} />
              <p className="lead-sm">{cr.text}</p>
              <ul className="check-list">{lines(cr.bullets).map((b) => <li key={b}><i className="bi bi-check-circle-fill"></i>{b}</li>)}</ul>
            </div>
          </div>
        </div>
      </section>

      <section className="section bg-soft">
        <div className="container" style={{ maxWidth: 860 }}>
          <SectionTitle title={f.title} />
          <div className="accordion faq reveal" id="faq">
            {f.items.map((q, n) => (
              <div className="accordion-item" key={q.q}>
                <h3 className="accordion-header">
                  <button className={`accordion-button${n ? ' collapsed' : ''}`} type="button" data-bs-toggle="collapse" data-bs-target={`#q${n}`}>{q.q}</button>
                </h3>
                <div id={`q${n}`} className={`accordion-collapse collapse${n ? '' : ' show'}`} data-bs-parent="#faq">
                  <div className="accordion-body" dangerouslySetInnerHTML={{ __html: nl(q.a) }} />
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>
    </>
  )
}

export function AboutPage() {
  const h = pages.about
  const s = h.story
  const c = h.cta
  return (
    <>
      <PageHero d={h.hero} />
      <section className="section pt-0">
        <div className="container">
          <div className="row g-5 align-items-center">
            <div className="col-lg-6 reveal">
              <h2 className="h1-sm" dangerouslySetInnerHTML={{ __html: rich(s.title) }} />
              {String(s.text).split(/\n\s*\n/).map((para) => (
                <p className="lead-sm" key={para.slice(0, 24)} dangerouslySetInnerHTML={{ __html: nl(para.trim()) }} />
              ))}
            </div>
            <div className="col-lg-6 reveal"><div className="shot"><img src={asset(s.image)} alt="" loading="lazy" /></div></div>
          </div>
        </div>
      </section>
      <section className="section bg-soft">
        <div className="container">
          <SectionTitle title={h.values.title} />
          <IconCards items={h.values.items} />
        </div>
      </section>
      <div className="pt-5"></div>
      <CtaBand title={c.title} text={c.text} b1t={c.cta_text} b1l={c.cta_link} />
    </>
  )
}

export function ContactPage() {
  const h = pages.contact
  const f = h.form
  return (
    <>
      <PageHero d={h.hero} />
      <section className="section pt-0">
        <div className="container">
          <div className="row g-4 g-lg-5">
            <div className="col-lg-7 reveal">
              <div className="form-card">
                <h2 className="h4 mb-4">{f.title}</h2>
                <form id="contactForm" method="post" action="#contact" noValidate>
                  <div className="row g-3">
                    <div className="col-md-6"><label className="form-label">Full name</label><input className="form-control" name="name" required maxLength={120} placeholder="Your name" /></div>
                    <div className="col-md-6"><label className="form-label">Email</label><input className="form-control" type="email" name="email" required maxLength={160} placeholder="you@example.com" /></div>
                    <div className="col-md-6"><label className="form-label">Phone <span className="text-muted">(optional)</span></label><input className="form-control" name="phone" maxLength={40} placeholder="+1 307 000 0000" /></div>
                    <div className="col-md-6">
                      <label className="form-label">Topic</label>
                      <select className="form-select" name="topic">{lines(f.topics).map((t) => <option key={t}>{t}</option>)}</select>
                    </div>
                    <div className="col-12"><label className="form-label">Message</label><textarea className="form-control" name="message" rows={5} required maxLength={4000} placeholder="How can we help?"></textarea></div>
                    <div className="d-none"><input type="text" name="website" tabIndex={-1} autoComplete="off" /></div>
                    <div className="col-12"><button className="btn btn-brand btn-lg-pill w-100" type="submit"><span className="lbl">{f.button}</span></button></div>
                    <div className="col-12"><div id="formMsg" className="form-msg" data-success={f.success} role="status"></div></div>
                  </div>
                </form>
              </div>
            </div>
            <div className="col-lg-5 reveal">
              <div className="side-card">
                <h3>{f.side_title}</h3>
                <p>{f.side_text}</p>
                <ul className="list-unstyled contact-list">
                  <ContactRows ctx="contact" />
                  {settings.hours ? <li><i className="bi bi-clock"></i><div><small>Support hours</small>{settings.hours}</div></li> : null}
                </ul>
              </div>
            </div>
          </div>
        </div>
      </section>
    </>
  )
}

export function LegalPage({ id }) {
  const d = pages[id].content
  return (
    <>
      <section className="page-hero compact"><div className="container text-center"><h1>{d.title}</h1><p className="lead">Last updated: {d.updated}</p></div></section>
      <section className="section pt-0"><div className="container"><article className="legal reveal" dangerouslySetInnerHTML={{ __html: d.body }} /></div></section>
    </>
  )
}

export function NotFoundPage() {
  return (
    <section className="page-hero">
      <div className="container text-center">
        <span className="eyebrow"><i></i>Error 404</span>
        <h1>Page not <span className="hl">found</span></h1>
        <p className="lead">The page you’re looking for doesn’t exist or has moved.</p>
        <a className="btn btn-brand btn-lg-pill" href="/">Back to home</a>
      </div>
    </section>
  )
}
