import { createRoot } from 'react-dom/client'
import { flushSync } from 'react-dom'
import App from './App.jsx'
import { boot } from './boot.js'

const root = createRoot(document.getElementById('root'))
flushSync(() => {
  root.render(<App />)
})
boot()
