---
id: T-20260518-001
owner: claude
state: waiting-for-review
priority: P1
risk: medium
estimated_minutes: 240
actual_minutes: 60
parent: null
children:
  - T-20260518-002
  - T-20260518-003
  - T-20260518-004
  - T-20260518-005
  - T-20260518-006
depends_on: []
parallelization_ok: false
retry_count: 0
retry_max: 1
escalation_path: [Codex]
created: 2026-05-18 23:30
updated: 2026-05-19 00:30
completed: 2026-05-19 00:30
acceptance: passed
final_report: changes/T-20260518-001/FINAL-REPORT.md
project: saigonhoreca-theme
scope:
  allowed:
    - "Local Sites/saigonhoreca/app/public/wp-content/themes/saigonhoreca-theme/**"
    - "Local Sites/saigonhoreca/app/public/wp-content/.task-handoffs/**"
  blocked:
    - "Local Sites/saigonhoreca/app/public/wp-config.php (DB creds)"
    - "Local Sites/saigonhouse/** (different project)"
    - "saigonhoreca.vn production (read-only via scrape)"
---

# 📋 T-20260518-001 | claude | saigonhoreca-mirror-full-coverage

## 0. 📥 User Original Intent (Phase 0 — Verbatim)

> "lên saigonhoreca.com.vn đi, còn nhớ project này ko?"
> "clone về 1 theme r set up giống saigonhouse đang làm"
> "bạn tự clone, trên đó dùng element pro nên file theme ko có gì cả, bạn dùng tool web,… cào full cho tôi, dùng python hay gì đó"
> "Khác với mình. bạn clone đúng k?"
> "bạn làm gì v? sao ko crawl html từ bên kia qua?"
> "Tương tự, full cho các page và template, part khác, làm chi tiết và nghiêm túc, lên dossier taskhandoffs rõ ràng"

**Pivot point**: User pushed me away from rebuilding templates section-by-section toward
serving the scraped HTML directly. The static-mirror approach was right; this dossier
takes that MVP to production-grade coverage across every page type, asset path, and
edge case the live saigonhoreca.vn site exposes.

---

## I. 📊 Frontmatter Snapshot

| Field | Value |
|---|---|
| `id` | T-20260518-001 |
| `owner` | claude (self-implement, orchestrator-direct) |
| `state` | in-progress |
| `priority` | P1 — user blocker (visible site mismatch) |
| `risk` | medium — touches theme load path + WP routing |
| `estimated_minutes` | 240 (4h focused) |
| `escalation_path` | [Codex] |
| `project` | saigonhoreca-theme |

---

## II. 🎯 Mục tiêu / Goal

Tất cả **314 pages + 75 AMP variants** scraped từ `saigonhoreca.vn` phải serve qua
`saigonhoreca.local` với:

1. **HTTP 200** trên mọi URL có trong scrape (no 404, no WP fallback)
2. **Visual parity 100%** với production saigonhoreca.vn (same CSS / JS / images /
   layout — anonymous browser test side-by-side)
3. **All assets load 200** — CSS, JS, fonts, images, AMP-CSS từ
   `static-mirror/wp-content/`
4. **Internal links work** — click `/du-an/`, `/san-pham/`, `/lien-he/`, blog posts,
   product detail → tất cả navigate trong mirrored site, no leak ra live origin
5. **Logged-in admin can still edit** — wp-admin route NOT swallowed by mirror
6. **No saigonhouse leakage** — `grep "Saigon House"` trên rendered HTML phải = 0
7. **Performance** — TTFB <100ms warm, Lighthouse Perf ≥85 trên mobile (parity-floor;
   production target sẽ là Phase 2 với critical CSS + image optim)

---

## III. 🧩 Deliverables (Sub-tasks)

### T-20260518-002 — Mirror coverage audit
Sweep tất cả 314 URLs trong `summary.json` qua mirror, log HTTP status + size mỗi
URL. Output `coverage-report.json` với:
- pages_200 (served)
- pages_404 (mirror miss)
- pages_redirect
- pages_partial (HTML <10KB suggests error page)

### T-20260518-003 — Asset path verification
Sample 50 random assets từ rendered homepage (CSS, JS, img). Verify HTTP 200 + size
matches scraped file. Identify broken paths.

### T-20260518-004 — Mirror edge cases
- AMP routes: `/<slug>/amp/` → serve `static-mirror/<slug>/amp/index.html`
- Trailing-slash redirects (WP-style canonical)
- Query-string URLs (`?p=123` was source of homepage corruption in original scrape)
- 404 fallback: scraped HTML for `404.html` if exists, else WP default
- Search/category/tag archives (some scraped, some not)

### T-20260518-005 — wp-admin + auth preservation
Verify mirror DOES NOT swallow:
- `/wp-admin/*`
- `/wp-login.php`
- `/wp-json/*`
- `/wp-cron.php`, `/xmlrpc.php`
Verify logged-in user CAN reach wp-admin. Mirror still serves public URLs for them
(by design — they shouldn't get a different broken WP frontend).

### T-20260518-006 — Visual parity validation
Run Puppeteer screenshot comparison: production saigonhoreca.vn vs local
saigonhoreca.local for 5 key pages (home, ve-saigon-horeca, du-an, san-pham, lien-he).
Acceptance: pixel diff <5% (some allowance for dynamic timestamps / view counters /
3rd-party widget render order).

---

## IV. 🛠️ Implementation Plan

### Phase A — Audit (60 min)
1. Write `audit-mirror-coverage.py` — fetches every URL from `summary.json` via
   `https://saigonhoreca.local/`, logs status + size
2. Generate `coverage-report.json` + summary stats
3. Identify gaps (404s + partials)

### Phase B — Fix mirror gaps (90 min)
1. Patch `inc/core/static-mirror.php`:
   - AMP path lookup (`static-mirror/<slug>/amp/index.html`)
   - 404 fallback (`static-mirror/404.html` if present)
   - WP auth path skip-list complete
   - Query-string handling (strip + lookup canonical)
2. Re-run audit, verify 100% 200 on scraped URLs

### Phase C — Asset path verification (30 min)
1. Sample 50 assets, curl, verify 200 + content match
2. Fix any rewrite gaps in `sgh_mirror_rewrite_urls()`

### Phase D — Visual parity (45 min)
1. Puppeteer script: screenshot 5 pages each from production + local
2. Diff via pixelmatch
3. Document findings

### Phase E — Documentation (15 min)
1. Update STATUS.md
2. Move dossier active → waiting-for-review
3. Write archive note

---

## V. ✅ Acceptance Criteria

- [ ] All 314 scraped URLs return HTTP 200 from saigonhoreca.local
- [ ] All 75 AMP URLs return HTTP 200
- [ ] No URL leaks to `saigonhoreca.vn` in rendered HTML (grep = 0 except canonical/og)
- [ ] No "Saigon House" / "saigonhouse" leakage in rendered HTML
- [ ] wp-admin still accessible (logged-in user)
- [ ] Visual parity <5% pixel diff on 5 key pages
- [ ] Coverage report committed to `wp-content/.task-handoffs/changes/T-20260518-001/`
- [ ] Dossier moved to archive after Codex review

---

## VI. 🚨 Risks & Mitigations

| Risk | Mitigation |
|---|---|
| Mirror serves stale content (production updated saigonhoreca.vn after scrape) | Re-run scrape, diff outputs. User chấp nhận snapshot 18/05/2026 |
| Asset path rewrites break on edge-case URLs (CDN, srcset, JSON-LD) | Test each pattern; document any failing pattern in Out-of-scope |
| WP query interferes (sets 404 before mirror, sets canonical wrong) | `status_header(200)` + `nocache_headers()` already applied. Audit confirms |
| Logged-in user sees both WP admin bar + mirror content (weird overlap) | Acceptable for editor UX; mirror is read-only static frontend |
| 222 MB mirror folder bloats backups | Add `static-mirror/` to `.gitignore` if repo committed; document size |

---

## VII. 📋 Out-of-scope (logged for later)

- ❌ Convert mirror HTML → proper WP posts/CPTs (Phase 2 work, T-???)
- ❌ Edit content via wp-admin (mirror is static; admin edits don't reflect)
- ❌ Server-side rendering optimization (Phase 3 — critical CSS, image-LCP, perf)
- ❌ AMP-specific URL rewriting beyond simple lookup
- ❌ WPML / Polylang multi-language (mirror only has VI snapshot)
- ❌ WooCommerce cart/checkout (static mirror; no real ecommerce)

---

## VIII. 📊 Progress Log

| Time | Phase | Action | Evidence |
|---|---|---|---|
| 23:30 | A | Dossier drafted | this file |
| | | | |

(Populated as task progresses)

---

## IX. 🔗 Dependencies

- Scrape complete: `Local Sites/saigonhoreca-clone/site/` (222 MB, 298 pages OK,
  2600 assets, 18/05/2026 22:17 snapshot)
- Theme exists: `wp-content/themes/saigonhoreca-theme/` (forked saigonhouse-theme)
- Mirror handler installed: `inc/core/static-mirror.php` (initial version, this task
  extends + hardens)
