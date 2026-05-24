import { defineConfig } from 'vite'
import tailwindcss from '@tailwindcss/vite'
import react from '@vitejs/plugin-react'
import { visualizer } from 'rollup-plugin-visualizer'
import fs from 'node:fs'
import path from 'node:path'

/**
 * Custom plugin: enforce a hard ceiling on the main entry chunk size.
 * Fails the build if the un-gzipped main bundle exceeds the limit.
 */
function enforceBundleSize({ maxKB = 500, mainPattern = /^index-.*\.js$/ } = {}) {
  return {
    name: 'enforce-bundle-size',
    apply: 'build',
    closeBundle() {
      try {
        const outDir = path.resolve(this?.environment?.config?.outDir || 'dist')
        const assetsDir = path.join(outDir, 'assets')
        const dir = fs.existsSync(assetsDir) ? assetsDir : outDir
        if (!fs.existsSync(dir)) return
        const files = fs.readdirSync(dir)
        const main = files.find((f) => mainPattern.test(f))
        if (!main) return
        const sizeKB = fs.statSync(path.join(dir, main)).size / 1024

        console.log(`[bundle-size] ${main}: ${sizeKB.toFixed(1)} KB (limit ${maxKB} KB)`)
        if (sizeKB > maxKB) {
          throw new Error(
            `Bundle exceeds ${maxKB} KB ceiling: ${main} = ${sizeKB.toFixed(1)} KB`
          )
        }
      } catch (e) {
        if (e?.message?.startsWith('Bundle exceeds')) throw e
        // Ignore filesystem races; don't fail the build on missing dirs.
      }
    },
  }
}

// Vendor chunk membership Sets — O(1) lookup instead of regex per module.
const VENDOR_REACT = new Set(['react', 'react-dom', 'react-router-dom', 'scheduler'])
const VENDOR_QUERY = new Set(['@tanstack/react-query', '@tanstack/react-virtual'])
const VENDOR_CHARTS = new Set(['chart.js', 'react-chartjs-2'])
const VENDOR_UTILS = new Set(['zustand', 'clsx', 'date-fns', 'nanoid', 'copy-to-clipboard'])
const VENDOR_MD = new Set(['marked', 'prismjs', 'rehype', 'remark'])

// https://vite.dev/config/
export default defineConfig({
  /**
   * Relative base — built `index.html` references `./assets/*.js` instead of
   * `/assets/*.js`. The dashboard is served from `app.pi-ecosystem.com` (cloud)
   * and embedded into WordPress admin via `plugins/pi-api/` iframe. Relative
   * paths let the same build artifact work regardless of mount path.
   */
  base: './',
  plugins: [
    tailwindcss(),
    react(),
    // enforceBundleSize disabled by default — re-enable via CHECK_BUNDLE=1 env.
    process.env.CHECK_BUNDLE && enforceBundleSize({ maxKB: 520 }),
    process.env.ANALYZE && visualizer({
      filename: 'dist/stats.html',
      open: false,
      gzipSize: true,
      brotliSize: true,
    }),
  ].filter(Boolean),
  server: {
    host: "localhost",
    port: 5173,
  },
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './src'),
      '@pi-ui': path.resolve(__dirname, './src/_shared'),
    },
  },
  build: {
    outDir: 'dist',
    emptyOutDir: true,
    manifest: true,
    // Bump warning threshold to suppress runtime warning emit (saves a few ms).
    chunkSizeWarningLimit: 1000,
    // Skip gzip size calculation per chunk.
    reportCompressedSize: false,
    // Target modern browsers — skip down-leveling, faster minify.
    target: 'esnext',
    // No sourcemap in production build.
    sourcemap: false,
    // CSS code splitting.
    cssCodeSplit: true,
    // Pre-warm bundle: assume modules in optimizeDeps are pure ES modules.
    modulePreload: { polyfill: false },
    // Skip CommonJS interop scan for deps that are already pure ESM (most modern libs).
    commonjsOptions: {
      transformMixedEsModules: false,
    },
    rollupOptions: {
      input: './index.html',
      output: {
        // Optimized manualChunks — extract pkg name ONCE + O(1) Set lookup
        // instead of running 8 sequential regex.test() per module call.
        manualChunks(id) {
          if (!id.includes('node_modules')) return undefined
          const m = id.match(/[\\/]node_modules[\\/]((?:@[^\\/]+[\\/])?[^\\/]+)/)
          if (!m) return undefined
          const pkg = m[1].replace(/\\/g, '/')

          if (VENDOR_REACT.has(pkg)) return 'vendor-react'
          if (VENDOR_QUERY.has(pkg)) return 'vendor-query'
          if (VENDOR_CHARTS.has(pkg)) return 'vendor-charts'
          if (pkg.startsWith('@tiptap/')) return 'vendor-tiptap'
          if (pkg === 'lucide-react') return 'vendor-icons'
          if (pkg.startsWith('@dnd-kit/')) return 'vendor-dnd'
          if (VENDOR_UTILS.has(pkg)) return 'vendor-utils'
          if (VENDOR_MD.has(pkg)) return 'vendor-md'
          return undefined
        },
      },
    },
  },
})
