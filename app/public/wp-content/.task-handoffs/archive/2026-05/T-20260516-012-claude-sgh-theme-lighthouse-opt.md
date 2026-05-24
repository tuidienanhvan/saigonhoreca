---
id: T-20260516-012
owner: claude
state: archived
priority: P2
risk: medium
estimated_minutes: 90
parent: null
children: []
depends_on: []
parallelization_ok: false
retry_count: 0
retry_max: 1
escalation_path: [Codex, Gemini]
created: 2026-05-16
updated: 2026-05-16 18:01
archived: 2026-05-16 18:01
verified: 2026-05-16 18:00
---

## ✅ Completion Notes

**🎉 Phát hiện lớn lúc baseline**: `theme.css` thật sự chỉ **15KB raw / 4.0KB gzipped** — file 513KB ban đầu là stale build từ 6 ngày trước. Sau khi rebuild lại, đã ở dưới Netflix benchmark 6.5KB.

→ User chọn strategy aggressive nhất: **inline TOÀN BỘ CSS** thay vì defer/critical extract.

### Changes applied

| File | Change |
|---|---|
| `assets/css/style.css` | `@source "../../**/*.{php,js}"` → 5 patterns scoped to theme dir |
| `inc/core/critical-css.php` | **NEW** helper `sgh_inline_theme_css()` + auto-dequeue `theme-tokens` |
| `functions.php` | Require new critical-css.php |
| `header.php` | Call `sgh_inline_theme_css()` before `wp_head()` |
| `inc/core/enqueue.php` | Unchanged — dequeue handled in critical-css.php at priority 100 |

### Results

| Metric | Before | After |
|---|---|---|
| CSS file (stale) | 513 KB raw | — |
| CSS rebuild | 16 KB raw / 4.3 KB gzip | **15 KB raw / 4.0 KB gzip** |
| Build time | 539ms | **141ms** *(4× faster)* |
| @source patterns | 2 wildcard (scan all wp-content) | 5 scoped to theme dir |
| Render-blocking CSS | 1 external `<link>` | **0 — fully inlined** |
| Selectors | 183 | 183 *(no class lost)* |

### Expected Lighthouse impact

- **FCP**: 200-400ms faster (no external CSS fetch)
- **LCP**: 100-300ms faster
- **TBT**: 50-150ms lower (no main thread block from CSS parse race)
- **Performance score**: estimated +8-15 points
- **Note**: 14KB rule satisfied → inline CSS comfortable below threshold

### Verification done
- ✅ Build success: `npm run build` → 141ms, no errors
- ✅ PHP files syntax inspected manually (PHP CLI not available)
- ✅ `@source` count: 5 patterns (was 2 wildcard)
- ✅ Defensive fallback: if `dist/theme.css` missing, re-enqueue external
- ✅ Auto-dequeue priority 100 runs AFTER enqueue.php priority 10
- ⚠️ Lighthouse measurement NOT performed (would require browser + LocalWP site running)

### Manual smoke test recommended
1. Mở `http://saigonhouse.local/` (LocalWP)
2. View source → kiểm tra:
   - Có `<style id="sgh-theme-inline">` trong `<head>`
   - KHÔNG có `<link rel="stylesheet" id="theme-tokens-css">` external
3. Visual: trang vẫn hiển thị đúng (no FOUC, header/hero/typo intact)
4. (Optional) Run Lighthouse CLI: `npx lighthouse http://saigonhouse.local/ --only-categories=performance`

### No git commit
Theme dir không phải git repo riêng (chỉ pi-backend/pi-store/pi-dashboard có .git). Thay đổi trên disk. Nếu cần version control sau: `cd themes/saigonhouse-theme && git init`.

## 0. 📥 User Original Intent (Phase 0 — Verbatim)

> "oke lên task dossier r làm — saigonhouse-theme Lighthouse optimization"

Context: research consensus là Tailwind v4 KHÔNG hurt Lighthouse khi config đúng. Bottleneck thực sự là:
1. `@source` quá rộng → scan cả `wp-content/**`
2. CSS render-blocking → hurt FCP/LCP
3. Bundle 513KB raw / 62KB gzipped (hơi to so với average 30-80KB)
4. Last build 6 ngày trước, có thể stale

---

# 📋 T-20260516-012 | saigonhouse-theme Lighthouse Optimization

## II. 🎯 Goal

Lighthouse Performance saigonhouse-theme tăng lên **90+** bằng cách:
1. Giảm CSS bundle 30-50% (62KB gzip → 30-40KB)
2. Loại bỏ render-blocking CSS (preload + onload swap)
3. Critical CSS inline above-the-fold
4. Rebuild với @source scope chính xác

## IV. 🚧 Allowed Scope

- `themes/saigonhouse-theme/assets/css/style.css`
- `themes/saigonhouse-theme/assets/css/critical.css` (new)
- `themes/saigonhouse-theme/inc/core/enqueue.php`
- `themes/saigonhouse-theme/inc/core/critical-css.php` (new)
- `themes/saigonhouse-theme/header.php`
- `themes/saigonhouse-theme/assets/css/dist/theme.css` (build output)

## V. 🚫 Out of Scope
- ❌ Bỏ Tailwind
- ❌ Pi-store / pi-dashboard
- ❌ UI redesign
- ❌ Add new npm dep ngoài Penthouse (nếu chọn auto-extract)
- ❌ PHP refactor không liên quan

## VI. 🛠️ Phases

### Phase A — Baseline measure (10')
- Build hiện tại + đo size raw/gzipped
- Count CSS selectors
- Note @source patterns hiện tại

### Phase B — Tighten @source (15')
Thay 2 wildcard rộng:
```css
@source "../../**/*.php";  /* TOO WIDE: scan cả wp-content */
@source "../../**/*.js";
```

Bằng 5 patterns scoped:
```css
@source "../../*.php";                       /* root template files */
@source "../../inc/**/*.php";
@source "../../page-templates/**/*.php";
@source "../../template-parts/**/*.php";
@source "../../assets/js/*.js";              /* exclude dist/ */
```

Rebuild + đo lại size.

### Phase C — Defer non-critical CSS (15')
Edit `inc/core/enqueue.php` để dùng filter `style_loader_tag`:
```php
// preload + onload swap = non-blocking
add_filter('style_loader_tag', ...);
```

### Phase D — Critical CSS inline (30')
Approach manual (no extra dep):
1. Create `assets/css/critical.css` chứa: tokens, reset, typography base, header, primary CTA — tổng ~5-10KB
2. Create `inc/core/critical-css.php` helper read + minify-inline file
3. Edit `header.php` → call helper trước `wp_head()`

### Phase E — Verify + measure (20')
1. Rebuild → check final size
2. Manual smoke test (open homepage, no FOUC)
3. Commit + archive

## VII. Verification Commands
```bash
cd themes/saigonhouse-theme

# Build
npm run build

# Size
ls -la assets/css/dist/theme.css
cat assets/css/dist/theme.css | gzip -c | wc -c

# Selector count
grep -oE '[.a-zA-Z0-9_-]+\s*\{' assets/css/dist/theme.css | wc -l

# Verify @source
grep "@source" assets/css/style.css

# Verify enqueue
grep -A 3 "style_loader_tag" inc/core/enqueue.php

# Verify critical inline
grep "critical-css" header.php
```

## VIII. Acceptance Criteria
- [ ] @source scoped (5 patterns thay vì 2 wildcard)
- [ ] Gzipped < 50KB target (từ 62KB)
- [ ] CSS load strategy = preload async swap
- [ ] Critical CSS inline ~5-10KB
- [ ] No FOUC khi load homepage
- [ ] Build success, no console error
- [ ] Commit với before/after numbers
- [ ] Archive dossier

## IX. Implementation
Self-implement (Claude). TodoWrite track 5 phases.

## XV. Rollback
- FOUC: revert header.php + enqueue.php
- Build fail: `git checkout assets/css/style.css`
- Worst case: `git checkout themes/saigonhouse-theme/`

## XVI. CHANGE LOG
- 2026-05-16: Dossier created + 5-phase plan filled.
- **2026-05-16 17:53**: State drafted → dispatched
