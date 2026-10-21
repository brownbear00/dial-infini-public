import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import fs from 'node:fs'
import path from 'node:path'

const root = process.cwd()
const mime = {
  '.css': 'text/css; charset=utf-8',
  '.js': 'text/javascript; charset=utf-8',
  '.webp': 'image/webp',
  '.png': 'image/png',
  '.jpg': 'image/jpeg',
  '.jpeg': 'image/jpeg',
  '.svg': 'image/svg+xml',
  '.woff': 'font/woff',
  '.woff2': 'font/woff2',
  '.ico': 'image/x-icon',
  '.html': 'text/html; charset=utf-8',
}

function serveRootStatic(req, res, next) {
  const url = decodeURIComponent((req.url || '').split('?')[0])
  if (!url.startsWith('/assets/') && !url.startsWith('/uploads/')) return next()
  const file = path.normalize(path.join(root, url))
  if (!file.startsWith(root) || !fs.existsSync(file) || !fs.statSync(file).isFile()) return next()
  res.setHeader('Content-Type', mime[path.extname(file).toLowerCase()] || 'application/octet-stream')
  fs.createReadStream(file).pipe(res)
}

function legacyStatic() {
  return {
    name: 'legacy-static',
    configureServer(server) {
      server.middlewares.use(serveRootStatic)
    },
    configurePreviewServer(server) {
      server.middlewares.use(serveRootStatic)
    },
    closeBundle() {
      fs.cpSync(path.join(root, 'assets'), path.join(root, 'dist', 'assets'), { recursive: true })
      fs.cpSync(path.join(root, 'uploads'), path.join(root, 'dist', 'uploads'), { recursive: true })
    },
  }
}

export default defineConfig({
  plugins: [react(), legacyStatic()],
  appType: 'spa',
})
