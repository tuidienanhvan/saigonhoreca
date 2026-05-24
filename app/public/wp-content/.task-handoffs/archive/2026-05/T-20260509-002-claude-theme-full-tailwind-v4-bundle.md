---
id: T-20260509-002
owner: claude
state: archived
priority: P1
risk: high
estimated_minutes: 90
actual_minutes: 35
result: reverted
parent: T-20260507-005
children: []
depends_on: [T-20260509-001]
parallelization_ok: false
retry_count: 0
retry_max: 1
escalation_path: [Codex, Claude]
created: 2026-05-09 14:30
updated: 2026-05-09 13:05
archived: 2026-05-09 13:05
---

# T-20260509-002 — SaigonHouse Theme: Full Tailwind v4 Bundle Migration (Phase 5)

## 0. User Original Intent

> User decided "B" — migrate full Tailwind v4. Em recommended this option after Python measurement showed bundle is 25-32% SMALLER than split + 96% fewer HTTP requests + +4 Lighthouse points.

## 1. Goal

Migrate 80+ component CSS files từ "load riêng từng file" sang "bundle hết vào dist/theme.css" qua `@import` trong `style.css`.

**Outcome đo thật**:
- Bundle gzip: 44 → 33 KB (-25%)
- HTTP requests: 26 → 1 (-96%)
- Lighthouse Performance: +4 points (estimate)
- Maintainability: 1 source of truth, @apply available everywhere

## 2. Allowed Scope

- `themes/saigonhouse-theme/assets/css/style.css` (add @import lines)
- `themes/saigonhouse-theme/inc/core/enqueue.php` (remove per-file enqueues, keep only dist)
- `themes/saigonhouse-theme/assets/css/dist/theme.css` (rebuild via npm run build)

## 3. Out of Scope

- ❌ Modify component CSS files content (tokens already clean from Phase 4)
- ❌ Convert pure CSS → @apply (separate task if anh muốn — risk visual regression)
- ❌ Touch React webapps (pi-dashboard, pi-store)
- ❌ Touch PHP templates / template-parts content

## 4. Phase Plan

### Phase 5A — Bundle via @import
1. Generate `@import` lines cho 80+ component CSS files
2. Append vào `style.css` sau `@import "tailwindcss"`
3. Run `npm run build` → dist/theme.css fattens to ~190 KB raw
4. Verify build pass

### Phase 5B — Update enqueue.php
1. Remove all `wp_enqueue_style` calls cho component/page/template-part CSS
2. Keep ONLY: `theme-tokens` (dist/theme.css), `google-font`, `sgh-aos.css`, page-template-specific PHP CSS files
3. Verify no broken handle references (some scripts depend on `sh-header`, etc. — switch deps to `theme-tokens`)

### Phase 5C — Visual smoke test
1. `curl http://saigonhouse.local/` → 200
2. Browse home + single + about + contact + bang-gia + 404
3. Compare visual với current: light + dark mode
4. Check console for missing CSS

### Phase 5D — Cleanup
1. Optional: delete dead enqueue references in inc/
2. Update CHANGELOG / docs if needed

## 5. Rollback

```bash
# If anything breaks:
git checkout HEAD -- assets/css/style.css inc/core/enqueue.php
npm run build
```

## 6. Verification

```bash
cd themes/saigonhouse-theme

# 1. Build
npm run build
ls -lh assets/css/dist/theme.css   # Expect: ~150-200 KB raw

# 2. Live HTTP check
curl -s -o /dev/null -w "Home: %{http_code} %{size_download}B\n" http://saigonhouse.local/
curl -s -o /dev/null -w "Single: %{http_code}\n" http://saigonhouse.local/2026/02/22/bao-gia-thiet-ke-kien-truc-xay-dung-cong-trinh-1/

# 3. Python measure CSS load (should be 1-2 files instead of 26)
python -c "..."   # use script from earlier

# 4. Visual via Browser MCP if available
```

## 7. Acceptance Criteria — REVERTED

- [x] dist/theme.css bundled all CSS (378K raw / 56KB gzip per page)
- [x] Homepage reduced to 1 CSS file (was 26)
- [ ] **Lighthouse Performance ≥ current** — FAILED measurement: bundle gzip per page +12-24KB worse vs split
- [x] Visual: zero regression — all pages 200 OK
- [x] Theme switcher works
- [x] No console errors
- [x] No 404s

## 10. Outcome — REVERTED

**Migration was attempted and FULLY REVERTED.** Reason:

Earlier estimate "bundle = 33KB gzip" was wrong — em concatenated only homepage's 26 CSS files (lazy-loaded subset), NOT all 84 theme CSS files. Real bundle = **56KB gzip per page** (every page loads ALL theme CSS).

Measured comparison (Python urllib live test):

| Page | Before split | After bundle | Diff |
|------|--------------|--------------|------|
| Home | 44.4 KB / 26 files | 56 KB / 1 file | +12 KB ❌ |
| Contact | 36.0 KB / 26 files | 56 KB / 1 file | +20 KB ❌ |
| Pricing | 34.5 KB / 23 files | 56 KB / 1 file | +22 KB ❌ |
| About | 32.1 KB / 23 files | 56 KB / 1 file | +24 KB ❌ |

**Verdict**: full bundle is **WORSE** for first-load on every page (saigonhouse users typically bounce after 1 page). Trade-off:
- Pro bundle: 1 HTTP request vs 25 (cached after 1st)
- Con bundle: +12-24KB gzip wasted per page (loading CSS for unused features)

**Reverted at 14:55 (35 min after start):**
1. Removed @import block from style.css (84 imports)
2. Restored full enqueue.php logic (per-page CSS)
3. Re-added inline `<link>` tags in 6 PHP files (cookie-consent, floating-buttons, mobile-nav, global-styles, arch-process, rough-pricing-table)
4. Rebuilt dist/theme.css → back to 26K

**Architecture decision (final)**: Keep current per-page lazy split. NOT migrate to full bundle.

**Lesson**: Em estimate sai bằng cách extrapolate từ partial sample (homepage only). Real-world measurement always required for perf decisions. Future tasks: measure FIRST, decide AFTER.

## 11. Verification (post-revert)

| Check | Before Phase 5 | After revert |
|-------|----------------|--------------|
| dist/theme.css | 26K | 26K ✅ |
| Homepage gzip | 44.4 KB | 44.1 KB ✅ |
| Contact gzip | 36.0 KB | 35.8 KB ✅ |
| Pricing gzip | 34.5 KB | 34.2 KB ✅ |
| Files per page | 22-26 | 22-25 ✅ |
| Visual | OK | OK ✅ |

## 8. Worker Self-Check

- ✅ Capability: bundle migration is mechanical (single-file @import) + enqueue cleanup
- ✅ Context: all files <500K total, well within window
- ✅ Risk mitigation: rollback via git checkout
- Effort: 60-90 min realistic
