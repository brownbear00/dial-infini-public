import { initCalc } from './calc.js'

function load(src) {
  return new Promise((resolve, reject) => {
    const s = document.createElement('script')
    s.src = src
    s.onload = () => resolve()
    s.onerror = () => reject(new Error(src))
    document.body.appendChild(s)
  })
}

export async function boot() {
  if (document.getElementById('calc')) initCalc()
  await load('/assets/vendor/bootstrap/bootstrap.bundle.min.js')
  await load('/assets/js/site.js?v=1.1')
  await load('/assets/js/premium.js?v=2.0')
}
