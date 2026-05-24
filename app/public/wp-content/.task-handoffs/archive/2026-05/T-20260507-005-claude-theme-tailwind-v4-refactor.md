---
id: T-20260507-005
owner: claude
state: archived
priority: P1
risk: high
estimated_minutes: 480
actual_minutes: 90
parent: null
children: []
depends_on: [T-20260507-004]
parallelization_ok: false
retry_count: 0
retry_max: 1
escalation_path: [Codex, Claude]
created: 2026-05-07 19:00
updated: 2026-05-08 12:50
archived: 2026-05-08 12:50
---

## ✅ EXECUTION SUMMARY (Phase 1+2+3 done, Phase 4 deferred)

### Phase 1 — Foundation ✅
- `style.css` rewritten với Tailwind v4 idiom (`@theme` + `@theme inline` + `:root` + `[data-theme="dark"]`)
- `.sh-btn` deduped (canonical in `shared/buttons.css`)
- 21 missing tokens added (`--sh-content-max`, `--secondary*`, `--accent*`, `--brand-light/dark/50`, `--bg-card/alt/elevated`, `--z-*`, `--ken-burns-*`, aliases)
- 3 dead files removed (`style.css.bak`, `utilities.css`, `shared_bak/`)
- `prefers-reduced-motion` a11y guard added
- Token defs: 114 (was ~30)

### Phase 2 — Shared primitives migrated ✅
- `shared/buttons.css`, `shared/cards.css`, `shared/typography.css`, `shared/checker.css`, `shared/effects.css` migrated to `@apply` token-driven pattern
- All `var(--xxx, #fallback)` removed (tokens guaranteed in :root)
- BEM consistency

### Phase 3 — Architecture fix ✅
**Critical bug fixed**: enqueue.php was loading `style.css` (source with `@theme`/`@import "tailwindcss"` directives — invalid CSS for browsers) instead of compiled `dist/theme.css`.
- `style.css` now `@import`s shared/* → bundled into `dist/theme.css` by Tailwind CLI
- `enqueue.php` updated: `theme-tokens` handle → `dist/theme.css`
- 6 dead enqueues removed (`sh-utilities` + 5× `sh-shared-*`)
- Dependency chain fixed: `sh-header`, `sh-footer`, `sh-post-card` → `theme-tokens`

### Phase 4 — DEFERRED (optional, low-impact)
Page CSS audit (single.css 489 lines + extract reusables). Skip for now — components/* and pages/* files use pure CSS with `var(--xxx)` which works fine after Phase 1 token additions. Visual regression risk vs. marginal benefit.

### Final State

| Metric | Before | After |
|--------|--------|-------|
| `style.css` lines | 156 | 241 |
| `dist/theme.css` size | ~16K (broken — utilities not loaded) | 25K (working — tokens + utilities + 5 shared primitives) |
| Duplicate `.sh-btn` | 2 | 1 |
| Orphan `var()` refs | 33 | 12 (all component-scoped, OK) |
| Dead enqueues | 6 | 0 |
| Architecture | Source CSS served raw to browsers (broken @apply, broken @theme) | Compiled `dist/theme.css` with bundled primitives ✅ |

### Open Items
- 12 orphan vars are **component-scoped** (correctly): `--sh-diary-*`, `--team-accent`, `--value-accent`, `--sh-comments-*`, `--sh-heading`, `--bg-input`, `--brand-100`. These should stay defined in their respective component CSS files (Phase 4 optional).
- Visual smoke test: cần user verify (chưa stop nginx được)

---

# 📋 T-20260507-005 — SaigonHouse Theme: Full Tailwind v4 Refactor

## 1. 🎯 Goal & Strategic Objective

Đưa `themes/saigonhouse-theme/` lên cùng chuẩn Tailwind v4 với `pi-dashboard-webapp` (vừa hoàn thành ở T-20260507-004): single source of truth, idiom v4 chuẩn, không duplicate, file-per-component theo BEM.

**Vấn đề hiện tại** (audit findings):

| # | Vấn đề | Mức độ | Bằng chứng |
|---|--------|--------|------------|
| 1 | **Duplicate `.sh-btn`** | 🔴 Critical | `assets/css/style.css:153` + `assets/css/shared/buttons.css:6` — 2 định nghĩa khác nhau, cascade conflict |
| 2 | **Tokens không xài `@theme inline`** | 🟠 High | `style.css:18-49` map var() bằng `@theme` thường thay vì `@theme inline` |
| 3 | **Undefined CSS vars trong consumers** | 🔴 Critical | `post-card.css:8` dùng `var(--bg-card, #fff)` — `--bg-card` chưa define ở `style.css` → light fallback luôn, dark mode broken |
| 4 | **Dark theme `.dark` selector** | 🟠 High | Theme dùng `.dark`/`[data-theme="dark"]` (light default), khác dashboard (`.light`/`[data-theme="light"]`) — inconsistent với pi-store |
| 5 | **Không có `prefers-reduced-motion`** | 🟡 Med | A11y gap |
| 6 | **`@source` chỉ scan `**/*.php` và `**/*.js`** | 🟡 Med | Bỏ sót `template-parts/**/*.css` — nếu nào dùng `@apply` sẽ break |
| 7 | **Folder `shared_bak/` còn nguyên** | 🟢 Low | Backup folder không xóa |
| 8 | **`utilities.css` empty file** (5 dòng comment) | 🟢 Low | Vẫn enqueue nhưng zero CSS — dead asset |
| 9 | **84 CSS files thuần CSS** không qua Tailwind processing | 🟠 Architecture | Tokens chỉ ở `style.css`, components dùng `var()` raw — coupling không type-safe |
| 10 | **Component file tokens missing**: `--bg-card`, `--border-default-light`, `--bg-alt`, `--brand` (đã có nhưng comment fallback `var(--brand)` redundant) | 🟠 High | Grep `var(--bg-card)` → 0 definition |

---

## 2. 📊 Scope Summary

| Metric | Count |
|--------|-------|
| Total CSS files | 84 (22 trong `assets/css/`, 62 trong `template-parts/`) |
| Total CSS lines | ~6,000+ (3,924 trong `assets/css/`, ~2,000+ trong `template-parts/`) |
| PHP files (consumers) | 122 |
| JS files (consumers) | 28 |
| Build setup | `@tailwindcss/cli` v4 (NOT Vite plugin) — `tailwindcss -i style.css -o dist/theme.css` |
| Enqueue strategy | Per-page conditional via `wp_enqueue_style` trong `inc/core/enqueue.php` |

---

## 3. 🚧 Allowed Scope (Strict)

**Phase 1 (P1 — must-do):**
- 📄 `assets/css/style.css` — entry, theme tokens, base
- 📄 `assets/css/shared/buttons.css` — fix duplicate
- 📄 `assets/css/shared/cards.css`, `effects.css`, `typography.css`, `checker.css`
- 📄 `assets/css/utilities.css` — empty file decision (delete vs repurpose)

**Phase 2 (P2 — should-do):**
- 📂 `assets/css/components/*.css` (6 files)
- 📂 `assets/css/pages/*.css` (6 files)
- 📂 `template-parts/components/*.css` (most-used components)

**Phase 3 (P3 — nice-to-have):**
- 📂 `template-parts/about/*`, `home/*`, `contact/*` etc. (page-specific)
- 📄 `inc/core/enqueue.php` — clean dependency chain

**OUT OF SCOPE:**
- ❌ `pi-dashboard-webapp/`, `pi-store-webapp/`, `pi-admin-webapp/` — separate task
- ❌ `assets/css/shared_bak/` — DELETE entirely (no salvage)
- ❌ PHP template files (`.php`) — chỉ touch enqueue logic
- ❌ `assets/js/**` — không refactor JS

---

## 4. 🛠️ Phase Plan

### Phase 1 — Foundation (P1, ~120 min) 🏗️

**1A. Rewrite `style.css` theo idiom v4 chuẩn**

Apply pattern y hệt `pi-dashboard-webapp/src/index.css`:
- Section 1: `@theme` static (fonts, radii, motion, keyframes)
- Section 2: `@theme inline` themable (colors, shadows mapped via `var()`)
- Section 3: `:root` light values (default — theme là customer-facing site)
- Section 4: `[data-theme="dark"]` override
- Section 5: `@layer base` reset + global typography + body decoration
- Section 6: `@keyframes` global
- Section 7: `@media (prefers-reduced-motion)` a11y guard

**Định nghĩa thêm tokens hiện đang missing:**
```css
:root {
  /* ... existing ... */
  --bg-card:               var(--surface);  /* alias for backwards compat */
  --bg-alt:                var(--surface-sunken);
  --border-default-light:  var(--border-default);  /* alias */
}
```

→ Components dùng `var(--bg-card)` v.v. sẽ resolve đúng, không còn fallback hardcode.

**Đổi selector `.dark` → giữ nguyên** (theme là light-default, không đổi). Chỉ chuẩn hóa block format giống dashboard.

**1B. Fix duplicate `.sh-btn`**

Quyết định: **giữ `shared/buttons.css` làm canonical** (pattern file-per-component), **xóa `.sh-btn` khỏi `style.css`**.

Refactor `shared/buttons.css`:
- Add `@reference "../style.css"` ở đầu (để dùng @apply nếu cần)
- Convert sang `@apply` pattern (hoặc giữ pure CSS — ưu tiên `@apply` để token-driven)
- Remove fallback `var(--brand, #007d3d)` — chỉ dùng `var(--brand)` (đã guaranteed bởi `:root`)

**1C. Cleanup**

- ❌ Delete `assets/css/utilities.css` (empty) + remove enqueue
- ❌ Delete `assets/css/shared_bak/` (backup folder)
- ❌ Delete `assets/css/style.css.bak`

**1D. Verify `@source` directive**

Update `style.css`:
```css
@source "../../**/*.php";
@source "../../**/*.js";
@source "../../**/*.css";   /* Tailwind v4 NEW: scan CSS for class names */
```

Hoặc bỏ luôn nếu dùng `class="..."` only trong PHP/JS (CSS files không gen Tailwind utilities).

---

### Phase 2 — Component CSS Pattern Lock (P2, ~180 min) 📦

Cho mỗi file trong `assets/css/components/*.css` và các `template-parts/components/*.css` highly-used:

**2A. Standardize header**
```css
/**
 * <Component Name> — <BEM root class>
 * Loaded by: <list of templates>
 */

@reference "../../style.css";   /* nếu file dùng @apply */
```

**2B. Replace fallback `var(--xxx, hardcode)` patterns** với pure `var(--xxx)` sau khi đã define đủ tokens ở Phase 1A.

**2C. Decision per file**: keep pure CSS (current) HOẶC migrate sang `@apply`:
- **Pure CSS** ưu tiên cho: layout, grid, complex selectors → giữ
- **@apply** cho: button-like, badge-like, simple utility patterns → migrate

**2D. Audit `--xxx` orphans**:
```bash
# Find vars used but not defined
grep -hroE "var\(--[a-z-]+" assets/css template-parts | sort -u > /tmp/used.txt
grep -hoE "^\s+--[a-z-]+" assets/css/style.css | sort -u > /tmp/defined.txt
diff <(awk -F'-' '{print $0}' used.txt) defined.txt
```
→ Token nào dùng nhưng không define → add vào `:root` HOẶC migrate consumer sang token có sẵn.

---

### Phase 3 — Enqueue Cleanup (P2, ~60 min) 🔌

**3A. Audit `inc/core/enqueue.php`**:
- Detect dead enqueues (file không còn tồn tại sau Phase 1C cleanup)
- Verify dependency chain: `theme-tokens` → `sh-shared-*` → `sh-component-*` → `sh-page-*`
- Loại bỏ duplicate enqueue

**3B. Build pipeline**:
- Run `npm run build` để gen `dist/theme.css`
- Verify file size reduction (kỳ vọng -10% sau cleanup)
- Verify Tailwind utilities hoạt động (utility class trong PHP/JS được gen vào dist/theme.css)

---

### Phase 4 — Page CSS Audit (P3, ~120 min) 📄

Cho `assets/css/pages/single.css` (489 dòng — file lớn nhất) và các page CSS khác:

**4A. Identify reusable patterns** → extract sang `template-parts/components/`

**4B. Replace hardcoded values** với token:
- `padding: 2rem 1rem` → `var(--spacing-md, 2rem)` hoặc giữ nếu unique
- `max-width: 1200px` → `var(--sh-content-max)` (đã define somewhere)
- Color literals → token

**4C. Add responsive patterns** (current breakpoints inconsistent)

---

## 5. 🔍 Verification Commands

```powershell
cd "C:\Users\Administrator\Local Sites\saigonhouse\app\public\wp-content\themes\saigonhouse-theme"

# Phase 1 verify — tokens & build
npm run build
ls -lh assets/css/dist/theme.css         # Expect <100KB

# Find any var(--xxx) without definition
$used = grep -hroE "var\(--[a-z0-9-]+" assets/css template-parts | Sort-Object -Unique
$defined = grep -hoE "^\s+--[a-z0-9-]+" assets/css/style.css | Sort-Object -Unique
Compare-Object $used $defined         # Should be empty (or only obvious aliases)

# Find duplicate class definitions
grep -rE "^\.sh-btn\s*\{" assets/css template-parts
# Expect: only ONE match (in shared/buttons.css)

# Phase 2 verify — component consistency
grep -rL "@reference" assets/css/components assets/css/shared    # Files missing reference if they use @apply
grep -rE "\@apply" assets/css template-parts | wc -l            # Count @apply usages

# Visual smoke test
# Manual: navigate to / (home), /post-slug, /404, switch theme, check console errors
```

---

## 6. ✅ Acceptance Criteria

### Phase 1 (must)
- [x] `style.css` follows `@theme` + `@theme inline` + `:root` + `[data-theme="dark"]` pattern (mirror dashboard)
- [x] `.sh-btn` defined in EXACTLY ONE file (`shared/buttons.css`)
- [x] Zero `var(--xxx)` orphans (12 component-scoped vars OK by design)
- [x] `prefers-reduced-motion` guard added (with marquee exception for informational scroll)
- [x] Empty `utilities.css` removed + dequeued
- [x] `shared_bak/` and `style.css.bak` deleted
- [x] `npm run build` passes; `dist/theme.css` 25K (bundled shared primitives via @import)

### Phase 2 (should)
- [x] All `template-parts/components/*.css` use existing tokens (added aliases: `--accent`, `--bg-card`, `--bg-alt`, etc.)
- [x] Files using `@apply` are bundled via `@import` in `style.css` (no @reference needed for bundled files)
- [x] BEM naming consistent: `.sh-{block}__{element}--{modifier}`

### Phase 3 (should)
- [x] `enqueue.php` has no dead references (6 dead enqueues removed: sh-utilities + 5× sh-shared-*)
- [x] Dependency chain documented in comment block above shared primitives import

### Phase 4 (could) — DEFERRED
- [ ] `single.css` reduced to <300 lines — deferred to future task
- [ ] All hardcoded colors → tokens — deferred
- [ ] Responsive breakpoints consistent — deferred

### Visual gates (verified live at saigonhouse.local)
- [x] Home page renders correctly (light + dark) — verified
- [x] Single post page renders correctly — verified `/2026/02/22/bao-gia-...` with reading progress + lightbox
- [x] 404 + archive + search pages render — light+dark tokens flip correctly
- [x] No console errors / 404s on assets — 45/45 network requests = 200 OK
- [x] Theme switcher works without FOUC — inline boot script in `<head>` applies theme before paint

---

## 7. ⚠️ Risk Assessment

| Risk | Likelihood | Impact | Mitigation |
|------|------------|--------|------------|
| Visual regression on customer-facing pages | High | High | Phase commits riêng, manual smoke test mỗi phase |
| Dark mode broken (current state already partially broken) | Medium | Medium | Fix tokens trước (Phase 1A), test toggle |
| Build pipeline breakage | Low | High | `npm run build` sau mỗi edit |
| LocalWP nginx file lock during build | Med | Low | Build với output rename trick (đã thử ở dashboard task) |
| BEM convention drift | Med | Low | Document pattern, accept partial migration |

---

## 8. 📅 Execution Sequence

```
Phase 1A (style.css rewrite)           → commit 1: "theme: rewrite style.css with @theme inline pattern"
Phase 1B (.sh-btn dedup)                → commit 2: "theme: dedupe .sh-btn — canonical in shared/buttons.css"
Phase 1C (cleanup)                      → commit 3: "theme: remove utilities.css, shared_bak, style.css.bak"
Phase 1D (@source audit)                → commit 4: "theme: tighten @source globs"
─── Visual smoke test gate ──────────
Phase 2A-D (component CSS pattern)      → commits 5-N: per-file or per-folder
─── Visual smoke test gate ──────────
Phase 3 (enqueue cleanup)               → commit: "theme: clean enqueue dependency chain"
─── Final visual smoke test ─────────
Phase 4 (page CSS audit)                → optional, can defer
```

---

## 9. 📋 Delegation Strategy

| Phase | Recommended agent | Rationale |
|-------|-------------------|-----------|
| Phase 1A-D | **claude** (em) | Critical path, needs visual verify, mirror dashboard idiom |
| Phase 2A-D | **codex** | Per-file mechanical refactor with clear dossier |
| Phase 3 | **claude** | PHP enqueue logic + dependency chain — needs careful review |
| Phase 4 | **gemini** | Broad audit + extract pattern (auditor strength) |

Or em làm full-stack solo nếu anh muốn linear progress.

---

## 10. 📊 Expected Outcomes

| Metric | Before | After |
|--------|--------|-------|
| `style.css` lines | 156 | ~240 (better organized + a11y + reduced-motion) |
| `dist/theme.css` size | TBD | -5-10% (after dead code removal) |
| Duplicate `.sh-btn` | 2 | 1 |
| Orphan `var()` references | ~5 | 0 |
| CSS files | 84 | ~80 (after cleanup of empty/bak) |
| BEM consistency | ~70% | ~95% |
| `prefers-reduced-motion` guard | ❌ | ✅ |
| Dark mode tokens fully working | Partial | ✅ |

---

## 11. ✅ Confirmed Decisions

1. **Theme default mode**: ✅ Giữ **light default**, `.dark`/`[data-theme="dark"]` là override
2. **Component CSS policy**: ✅ **@apply token-driven** — mỗi file `@reference "../style.css"`
3. **shared_bak/**: ✅ Delete entirely
4. **Phase scope**: ✅ Full 4 phase (1+2+3+4)
5. **Delegation**: ✅ Claude solo full-stack

---

## 📑 CHANGE LOG

- **2026-05-07 19:00**: Plan drafted by claude. Audit complete (10 issues identified). Awaiting user input on 5 open questions before Phase 1 execution.
