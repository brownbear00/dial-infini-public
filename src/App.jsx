import { Layout } from './components.jsx'
import { HomePage, FeaturesPage, PricingPage, AboutPage, ContactPage, LegalPage, NotFoundPage } from './pages.jsx'

const routes = {
  '/': ['home', <HomePage key="home" />],
  '/features': ['features', <FeaturesPage key="features" />],
  '/pricing': ['pricing', <PricingPage key="pricing" />],
  '/about': ['about', <AboutPage key="about" />],
  '/contact': ['contact', <ContactPage key="contact" />],
  '/privacy': ['privacy', <LegalPage id="privacy" key="privacy" />],
  '/terms': ['terms', <LegalPage id="terms" key="terms" />],
}

export default function App() {
  const path = (window.location.pathname || '/').replace(/\/+$/, '') || '/'
  const hit = routes[path]
  if (!hit) return <Layout page="404"><NotFoundPage /></Layout>
  return <Layout page={hit[0]}>{hit[1]}</Layout>
}
