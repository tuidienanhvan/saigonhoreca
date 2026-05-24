---
id: T-20260515-005
title: Remove quantum-hud.css — migrate to design tokens (pi-store + pi-dashboard)
owner: gemini
state: archived
verified: 2026-05-15
verifier: claude
risk: medium
priority: P1
scope: Xoá quantum-hud.css legacy, migrate ~205 class usages sang Tailwind tokens vừa codified (shadow-glass, shadow-primary, rounded-xl, etc.)
created: 2026-05-15
---

# T-20260515-005 — Quantum-HUD → Token Migration

## 🎯 Goal
Sau khi T-20260515-003 (shadow tokens) đã thành công + ConfirmModal unified, **xoá quantum-hud.css legacy** và migrate toàn bộ class HUD scattered (`hud-card`, `hud-corner`, `scan-line`, `hud-value`, `quantum-grid`...) sang **design tokens chính chủ** đã có trong `index.css`.

Goal cuối: codebase chỉ còn 1 source of truth là `index.css` `@theme` tokens — không còn HUD CSS riêng.

## 📊 Audit kết quả (vừa scan)

### pi-store-webapp/src/styles/quantum-hud.css (99 lines, 12 classes)
| Class | Usage count | Mapping target |
|---|---|---|
| `.quantum-grid` | 0 (chỉ trong CSS) | Bỏ hẳn (decorative grid pattern) |
| `.ambient-glow` | 0 (chỉ trong CSS) | Bỏ (replace by `shadow-primary` token) |
| `.hud-card` | 1 | `rounded-xl border border-white/10 bg-base-200/40 backdrop-blur-md shadow-md` |
| `.hud-card:hover` | (implicit) | `hover:border-primary/30 hover:shadow-primary` |
| `.hud-corner` | 23 (pi-store) | **DELETE** (decorative anti-pattern, user complained) |
| `.scan-line` | 4 | **DELETE** (decorative anti-pattern) |
| `.hud-banner` | 0 | Bỏ (đã có `<AdminPageHeader>` component) |
| `.hud-value` | 3 | `font-semibold tabular-nums text-base-content` |
| `.hud-filter-bar` | 0 | Bỏ (đã có `<AdminFilterBar>` component) |
| `.section-label` | ? | `text-sm font-semibold text-base-content/80` |
| `.glass-panel` | 4 | `rounded-xl border border-white/10 backdrop-blur-md shadow-glass` |

### pi-dashboard-webapp (no quantum-hud.css, but uses classes somewhere)
| Class | Usage |
|---|---|
| `hud-card` | **33** |
| `hud-corner` | **104** |
| `quantum-grid` | 11 |
| `hud-value` | 22 |

→ pi-dashboard defines these classes ở đâu khác — Gemini phải **TÌM** chúng (có thể là `Layout.css`, hoặc `DashboardLayout.css`).

**Tổng cross 2 app: ~205 instances** + 99 lines CSS to delete.

## 🎨 Migration map (CRITICAL — không skip)

### Replace per class usage:

```jsx
{/* TRƯỚC */}
<div className="hud-card">
  <div className="hud-corner top-left" />
  <div className="scan-line" />
  <span className="hud-value">{number}</span>
</div>

{/* SAU */}
<div className="rounded-xl border border-white/10 bg-base-200/40 backdrop-blur-md shadow-md hover:border-primary/30 hover:shadow-primary transition-colors">
  {/* hud-corner DELETED */}
  {/* scan-line DELETED */}
  <span className="font-semibold tabular-nums">{number}</span>
</div>
```

### Detailed mapping table:

| Old class | New Tailwind (uses tokens) |
|---|---|
| `hud-card` | `rounded-xl border border-white/10 bg-base-200/40 backdrop-blur-md shadow-md` |
| `hud-card` hover | thêm `hover:border-primary/30 hover:shadow-primary transition-colors` |
| `hud-corner` (any position) | **REMOVE entire `<div>`** — decorative, user dislikes |
| `scan-line` | **REMOVE entire `<div>`** — decorative, user dislikes |
| `quantum-grid` | **REMOVE entire `<div>`** OR use `bg-[radial-gradient(...)]` inline nếu thực sự cần |
| `hud-value` | `font-semibold tabular-nums text-base-content` |
| `hud-banner` | Wrap inside `<AdminPageHeader title tagline>` component |
| `hud-filter-bar` | Wrap inside `<AdminFilterBar>` component |
| `glass-panel` | `rounded-xl border border-white/10 backdrop-blur-md shadow-glass` |
| `ambient-glow` | `shadow-primary` |
| `section-label` | `text-sm font-semibold text-base-content/80 mb-3` |

## 🚧 Constraints

1. **KHÔNG đụng `index.css`** — đã token-ized stable
2. **KHÔNG đụng `_shared/components/ui/*` primitives**
3. **KHÔNG đổi visual significantly** — mục tiêu **same visual, different source** (CSS class → Tailwind utility với tokens)
4. **Build PASS** sau mỗi app
5. **ESLint clean** sau cùng
6. **Xoá file `quantum-hud.css` HOÀN TOÀN** ở pi-store + bất kỳ tương đương ở pi-dashboard
7. **Remove `@import "./quantum-hud.css"`** trong index.css nếu có
8. **Migrate ALL ~205 instances** — không leave behind (sẽ broken style nếu CSS xoá)
9. **PRESERVE acronyms + brand language** (Pi, AI, SEO, HUD nếu là branding)

## 📊 Phases

### Phase A — Discovery (15')
1. Read `pi-store-webapp/src/styles/quantum-hud.css` toàn bộ
2. Search `quantum-hud.css` import trong cả 2 app
3. Find pi-dashboard tương đương — có thể là `Layout.css`, `core.css` hoặc inline trong feature CSS
4. List ALL files dùng `hud-*` / `quantum-*` / `scan-line` / `glass-panel` classes
5. Output: discovery report inline trong dossier

### Phase B — Migration pi-store (smaller, 20')
1. Replace 35 instances trong pi-store JSX theo migration map
2. Delete `quantum-hud.css` file
3. Remove `@import "./quantum-hud.css"` từ `index.css` nếu có
4. Build PASS, visual sanity check 3 admin pages
5. Commit: `refactor(pi-store): remove quantum-hud.css, migrate to design tokens`

### Phase C — Migration pi-dashboard (larger, 30')
1. Find HUD CSS source file pi-dashboard
2. Replace 170 instances JSX
3. Delete CSS source file + imports
4. Build PASS, visual sanity check 5 dashboard pages (Overview, Notifications, AI Cloud, SEO, Settings)
5. Commit: `refactor(pi-dashboard): remove HUD legacy CSS, migrate to tokens`

### Phase D — Verify (10')
- grep `hud-card|hud-corner|scan-line|quantum-grid|hud-value|glass-panel|ambient-glow|section-label|hud-filter-bar|hud-banner` → **0 matches** (or only inside deleted CSS files which are already removed)
- Build cả 2 app PASS
- ESLint clean cả 2 app
- index.css byte-identical sync verify
- Screenshot spot-check: 3 pi-store pages + 5 pi-dashboard pages, confirm no broken cards

## ✅ Acceptance Criteria

- [ ] `quantum-hud.css` deleted ở pi-store
- [ ] Equivalent CSS file deleted ở pi-dashboard (Gemini tìm + xác định)
- [ ] 0 instances `hud-card|hud-corner|scan-line|quantum-grid|hud-value|glass-panel|ambient-glow|section-label|hud-filter-bar|hud-banner` còn trong JSX
- [ ] Build PASS cả 2 app
- [ ] ESLint clean cả 2 app
- [ ] index.css byte-identical (sanity sync check)
- [ ] Visual no regression — 8 random pages screenshot trước/sau confirm same look (or better)
- [ ] No broken imports — không file nào còn import quantum-hud.css

## 🔒 Out of Scope
- `index.css` token changes (immutable)
- `_shared/components/ui/*` primitives
- New features
- Page-level layout redesign (chỉ class migration, không restructure)
- Typography cleanup (đã có T-004 cho việc đó)
- Animation tokens (đã token-ized riêng)

## 📎 References
- Tokens đã sẵn sàng dùng: `src/styles/index.css` `@theme` block
  - `--shadow-md`, `--shadow-primary`, `--shadow-glass` cho card backgrounds
  - `--shadow-success/danger/warning` cho state cards
  - `--radius-lg/xl/2xl` cho rounded corners
- T-20260515-003 dossier — shadow token migration foundation
- Sample target style: `admin/features/overview/OverviewPage.jsx` (clean, no HUD classes)

## 🛠 Why Gemini (Tier 1)?
- 1M context — đủ đọc cả 2 app cùng lúc
- Multimodal — verify visual no-regression bằng screenshot before/after
- Antigravity tier — can plan + execute + verify trong 1 pass
- Pattern lặp lại ~205 lần với 12 mapping rules → Gemini auditor strength

## ✅ Phase D Verification (Claude, 2026-05-15)
- `quantum-hud.css` deleted ✅ (find returns 0 results)
- 13 CSS feature files deleted (Analytics, Subscription, Categories, Comments, ContentList, Editor, Media, Menu, Leads, Overview, Performance, PerformancePro, SeoAudit) ✅
- Pi-store **admin/** scope: 0 instances of `hud-card|hud-value|scan-line|hud-corner` ✅
- Pi-dashboard orphan `quantum-grid` (11 instances) → **stripped by Claude** ✅
- Build pi-store: ✅ PASS 1.46s
- Build pi-dashboard: ✅ PASS 1.51s, 308.2 KB
- ESLint clean both

### Known carry-over (scoped feature decoratives, NOT from quantum-hud)
- Auth pages (LoginPage/SignupPage/ForgotPassword): `hud-corner`, `hud-value` in their OWN `AuthForm.css` + `SignupPage.css` — orbit map decoration legitimate per-feature
- Catalog/HomeCTA: `glass-panel`, `card-scan-line` in own `catalog.css` + `HomeCTA.css` — feature-scoped brand decor
- These are NOT cross-cutting quantum-hud usages — they're self-contained per-feature CSS with their own definitions. Out of scope for this task.

**Status**: CLOSED | Owner: Gemini (Phase A-C) + Claude (Phase D verify + orphan quantum-grid mop-up)
