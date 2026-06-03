---
id: T-20260518-002
owner: claude
state: waiting-for-review
priority: P1
risk: low
estimated_minutes: 60
actual_minutes: 20
parent: T-20260518-001
children: []
depends_on: [T-20260518-001]
parallelization_ok: false
retry_count: 0
retry_max: 1
escalation_path: [Codex]
created: 2026-05-19 00:35
updated: 2026-05-19 00:55
completed: 2026-05-19 00:55
acceptance: passed
final_report: changes/T-20260518-002/FINAL-REPORT.md
project: saigonhoreca-theme
scope:
  allowed:
    - "Local Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme/**"
    - "Local Sites/saigonhoreca/app/public/wp-content/.task-handoffs/**"
  blocked:
    - "Local Sites/saigonhouse/** (different project)"
---

# 📋 T-20260518-002 | claude | saigonhoreca-mirror-csp-asset-fixes

## 0. 📥 User Original Intent (Phase 0 — Verbatim)

User dán console log từ Chrome devtools sau khi T-20260518-001 đóng. Lỗi runtime:
- 9× CSP font-src violations
- 3× CSP script-src/connect-src violations (Google Analytics + WooCommerce chunks)
- 4× HTTP 404 trên `fa-solid-900.{woff2,woff,ttf}` (FontAwesome font files thiếu)
- 1× ChunkLoadError WooCommerce attribute-filter
- 2× `/wp-json/wc/store/v1` returns 403/404

Mirror coverage đã xong nhưng runtime bị block. Need separate dossier để fix.

---

## I. 📊 Frontmatter Snapshot

| Field | Value |
|---|---|
| `id` | T-20260518-002 |
| `owner` | claude (orchestrator-direct) |
| `state` | in-progress |
| `priority` | P1 — runtime breakage blocks usable mirror |
| `risk` | low — only touches CSP + asset paths |
| `estimated_minutes` | 60 |
| `parent` | T-20260518-001 |

---

## II. 🎯 Mục tiêu / Goal

Eliminate all browser console errors on saigonhoreca.local public pages:

1. **CSP relax** — allow font-src, script-src, connect-src for saigonhoreca's
   Elementor/Astra/WooCommerce/Analytics needs (or remove CSP entirely — saigonhouse
   CSP was tuned for saigonhouse plugin set, not saigonhoreca)
2. **FontAwesome fonts** — copy missing fa-solid-900.{woff2,woff,ttf} into mirror
3. **Dynamic JS chunks** — patch URL rewriter so webpack-loaded chunks resolve to
   local paths (not saigonhoreca.vn production)
4. **WC store API** — return synthetic empty JSON instead of 404 so WooCommerce
   blocks don't error out

---

## III. 🧩 Sub-tasks

### Phase A — CSP audit
- Find current CSP source (functions.php inherited from saigonhouse)
- Decide: relax existing CSP OR remove CSP for saigonhoreca entirely
- Document trade-offs

### Phase B — CSP fix
- Patch (or remove) CSP header
- Verify console clean for fonts/scripts/analytics

### Phase C — FontAwesome assets
- Locate fa-solid-900.{woff2,woff,ttf} in mirror or scraped folders
- If missing, fetch from production saigonhoreca.vn
- Place at expected paths

### Phase D — Dynamic chunk URL rewrite
- Inspect `attribute-filter-wrapper-frontend.js` — find where `saigonhoreca.vn` URL
  is embedded
- Option 1: search-replace inside scraped JS files
- Option 2: inject a `<script>` early that overrides webpack publicPath at runtime

### Phase E — WC store API stub
- Add route handler returning `{}` for `/wp-json/wc/store/v1*`

### Phase F — Validate via Lighthouse / browser console
- Reload homepage + category page
- 0 console errors
- 0 CSP violations
- 0 failed font/JS requests

---

## IV. ✅ Acceptance Criteria

- [ ] 0 CSP violations on browser console (homepage + 1 category page + 1 product page)
- [ ] FontAwesome icons render (visual check)
- [ ] WooCommerce filter widgets don't throw ChunkLoadError
- [ ] No 404 on font files
- [ ] Final report committed to changes/T-20260518-002/

---

## V. 🚨 Risks

| Risk | Mitigation |
|---|---|
| Loosening CSP introduces XSS surface | Acceptable for local dev mirror; production would set CSP per real plugin set |
| Removing CSP entirely is too permissive | Add saigonhoreca-specific minimal CSP instead |
| Webpack chunk URLs deeply embedded in minified JS — search-replace fragile | Use runtime publicPath override script instead |

---

## VI. 📋 Out-of-scope

- ❌ Full WooCommerce plugin install (not needed for static mirror)
- ❌ Google Analytics ID swap (let it 404 silently — mirror is dev-only)
