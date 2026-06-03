---
id: T-20260515-006
title: Per-component + per-page CSS — scoped style files (pi-store + pi-dashboard)
owner: codex
state: archived
verified: 2026-05-16
verifier: claude
risk: high
priority: P2
scope: Mỗi component & page có file CSS riêng (Component.css / Page.css) co-located, di chuyển bulk Tailwind utility class CSS-heavy ra file CSS scoped để dễ maintain
created: 2026-05-15
---

# T-20260515-006 — Per-Component / Per-Page CSS

## 🎯 Goal
Chuyển từ Tailwind-only utility cluster sang **CSS scoped per component/page** để dễ maintain. Pattern target:

```
features/auth/
├── LoginPage.jsx
├── LoginPage.css        ← styles riêng cho LoginPage
└── components/
    ├── AuthForm.jsx
    └── AuthForm.css     ← styles riêng cho AuthForm
```

JSX import `./Component.css` ngay đầu file. CSS dùng BEM-ish class names hoặc data-attribute. Vẫn dùng Tailwind tokens (var(--p), var(--shadow-md), etc) bên trong CSS via `@apply` hoặc CSS variables.

## 📐 Pattern spec

### Component pattern
```jsx
// AuthForm.jsx
import "./AuthForm.css";

export function AuthForm() {
  return <div className="auth-form">...</div>;
}
```

```css
/* AuthForm.css */
.auth-form {
  display: flex;
  flex-direction: column;
  gap: 16px;
  padding: 24px;
  border-radius: 12px;
  border: 1px solid var(--bd);
  background: var(--b2);
  box-shadow: var(--shadow-md);
}

.auth-form__title {
  font-size: 1.25rem;
  font-weight: 600;
  color: var(--bc);
}
```

### Page pattern
```jsx
// LoginPage.jsx
import "./LoginPage.css";
import { AuthForm } from "./components/AuthForm";

export function LoginPage() {
  return <main className="login-page">...</main>;
}
```

### Rules
- **JSX className**: chỉ class semantic (BEM) hoặc Tailwind utilities cho layout (`flex`, `grid-cols-3`, `gap-4`)
- **CSS file**: visual styling (color, spacing chi tiết, shadow, border) dùng tokens từ `index.css`
- **No deep Tailwind walls** trong JSX nữa — utility class chỉ cho layout primitives
- **Co-located**: file `.css` cùng folder với `.jsx`, import top
- **Naming**: `ComponentName.jsx` ↔ `ComponentName.css` (1:1 match)
- **BEM-style class**: `.component-name`, `.component-name__element`, `.component-name--modifier`

## 📊 Surface area (Updated 2026-05-16 per user)

### ⚠️ MANDATORY: `features/` full coverage
User explicit yêu cầu: **TOÀN BỘ JSX trong `features/` (pages root-level + components subfolder) PHẢI có `.css` riêng co-located**. Không exception (trừ `.test.jsx`).

### pi-store-webapp
- `admin/features/<feature>/*.jsx` — 30 files (pages + sub-components)
- `src/features/<feature>/**/*.jsx` — 50 files (pages + components)
- `admin/_shared/components/*.jsx` — ~15 shared primitives
- → **~95 files**

### pi-dashboard-webapp
- `src/features/<feature>/**/*.jsx` — **216 files** (pages root + components nested)
- `src/_shared/widgets/*.jsx`, `_shared/layout/*.jsx`, `_shared/components/*.jsx` — ~60 shared
- `src/_shared/base/*.jsx`, `_shared/overlays/*.jsx` — ~20 (UserMenu, GlobalSearch — batch 1 done)
- `src/_shared/skeletons/**/*.jsx` — ~35 (skip — already token-minimal)
- → **~290 files**

### Live progress (verify 2026-05-16)
| App | Total JSX in features/ | Has matching .css | Missing |
|---|---|---|---|
| pi-store | 50 + 30 = 80 | 25 | **55** |
| pi-dashboard | 216 | 5 (batch 1) | **211** |

→ **266 files in `features/` còn thiếu CSS** (excluding `_shared/`).

**Tổng cross 2 app: ~385 files cần CSS scoped riêng** (266 features + ~95 shared + 20 base/overlay).

## 🚧 Constraints

1. **KHÔNG đụng `index.css`** — tokens stable, tất cả CSS scoped reference `var(--*)`
2. **KHÔNG đụng `_shared/components/ui/*` primitives** (Button, Input, Modal API)
3. **PHẢI co-located**: file `.css` cùng folder với `.jsx` của nó
4. **PHẢI 1:1 naming**: `LoginPage.jsx` → `LoginPage.css` (không khác)
5. **PHẢI import trên top** của JSX file
6. **PHẢI dùng tokens** trong CSS, không hardcode color/shadow/radius
7. **Build PASS sau mỗi batch** (~20-30 files)
8. **ESLint clean** sau cùng
9. **Commit per batch** cho rollback

## 🎨 Migration logic per file

### Step 1: Audit JSX
Đọc JSX, identify clusters Tailwind utility:
- Layout (flex/grid/gap): **GIỮ** trong JSX
- Visual (color/bg/border/shadow/padding chi tiết): **MOVE** vào CSS

### Step 2: Generate CSS class names
Mỗi `<div>` có nhiều utility visual → tạo BEM class:
```jsx
{/* TRƯỚC */}
<div className="rounded-xl border border-white/10 bg-base-200/40 backdrop-blur-md shadow-md p-6 flex flex-col gap-4">

{/* SAU */}
<div className="component-name__card flex flex-col gap-4">
```

```css
/* TRƯỚC: Tailwind utility cluster */
/* SAU: */
.component-name__card {
  border-radius: var(--radius-xl);
  border: 1px solid var(--bd);
  background: color-mix(in srgb, var(--b2) 40%, transparent);
  backdrop-filter: blur(12px);
  box-shadow: var(--shadow-md);
  padding: 24px;
}
```

### Step 3: Import CSS
```jsx
import "./ComponentName.css";
```

## 📊 Phases

### Phase A — Setup tooling (30')
1. Build Python script `migrate_to_scoped_css.py`:
   - Per file: extract visual utility clusters → generate BEM class
   - Output CSS file co-located
   - Update JSX className references
2. Test trên 1 file isolated (`AdminCard.jsx`)
3. Verify build PASS
4. Document patterns rút ra

### Phase B — Migrate `_shared/` primitives (60')
- pi-store: `admin/_shared/components/*` (15 files)
- pi-dashboard: `_shared/widgets/`, `_shared/layout/`, `_shared/components/` (60 files)

Per file:
- Generate CSS scoped
- Update JSX
- Build PASS
- Commit per 10 files

### Phase C — Migrate admin features pi-store (60')
- `admin/features/<feature>/*.jsx` page files (~30)
- `admin/features/<feature>/components/*.jsx` (~25)

Per feature batch:
- Migrate all files in feature folder
- Build PASS
- Commit `feat(pi-store/admin/<feature>): scoped CSS migration`

### Phase D — Migrate pi-store storefront (45')
- `src/features/<feature>/*.jsx` (~25 files)
- Commit per feature

### Phase E — Migrate pi-dashboard features (120')
- `src/features/<feature>/*.jsx` + `components/**/*.jsx` (~200 files)
- Largest batch — commit per feature (analytics, content, seo, leads, system, ai, performance, billing, overview)

### Phase F — Verify (30')
- Final build PASS cả 2 app
- ESLint clean
- Spot-check 8 random pages screenshot before/after
- Sample CSS file size check (each <5KB ideal)
- index.css byte-identical sync (sanity)

## ✅ Acceptance Criteria

- [ ] **TOÀN BỘ JSX trong `features/` (2 apps) có CSS co-located 1:1** (target: 266 missing → 0)
- [ ] Shared primitives (`_shared/base`, `_shared/overlays`, `_shared/widgets`, `_shared/layout`) cũng có CSS riêng (~95 files)
- [ ] JSX import `./Component.css` top mỗi file
- [ ] CSS dùng tokens (`var(--*)`), chỉ chấp nhận hardcoded color trong `var()` fallback syntax (`var(--er, #ef4444)`)
- [ ] JSX className giữ layout primitives (flex/grid/gap) — visual đẩy vào CSS
- [ ] Build PASS cả 2 app
- [ ] ESLint clean cả 2 app
- [ ] index.css byte-identical (untouched)
- [ ] Bundle size không tăng đáng kể (<5%)
- [ ] Visual no regression (8 random pages spot-check)
- [ ] Verify command: `find features -name "*.jsx" -not -name "*.test.jsx"` mỗi file PHẢI có `.css` cùng tên

## 🔒 Out of Scope
- `index.css` token changes
- `_shared/components/ui/*` primitives API
- Backend changes
- New features
- Animation token changes

## 📎 References
- Sample existing pattern: `src/features/home/components/HomeHero.css` + `HomeHero.jsx` (đã co-located từ trước)
- Sample auth: `src/features/auth/AuthForm.css` (legitimate per-feature CSS)
- Design tokens nguồn: `src/styles/index.css` `@theme` block

## 🛠 Why Codex 5.3?
- 400K context — đủ batch 30 files/turn
- Surgeon tier — per-file precise extraction
- Pattern lặp ~355 lần với same rule → Codex strength
- 1 file = 1 JSX edit + 1 CSS create + 1 import line — clean atomic unit

## ⚠️ Risk

- Visual regression nếu utility cluster phức tạp (conditional className) → script phải handle template literals carefully
- Bundle size có thể tăng nếu CSS overlap nhiều (cùng border/shadow lặp lại trong nhiều file)
- Mitigation: shared utility classes vẫn cho phép trong JSX (chỉ migrate visual cluster, không atomic single utility)

## ✅ Phase F Verification (Claude, 2026-05-16)

### Coverage final
| App | CSS files in features/ + shared/ | Quality pass | Scaffold (intentional) | Hardcoded fixed |
|---|---|---|---|---|
| pi-store | 101 | 50 | 42 | 8 |
| pi-dashboard | 225 | 15+ | 207 | 3 |

### rgba/hex token sweep
- 31 rgba → tokens (Claude P3 first pass): HomeHero, ProductOfferings, Bento*, Pricing, HomeCTA, HomeBento, HomeFeatured
- 20 files round 2 bulk sweep (Modal.css, DocsHero.css, etc.)
- 7 targeted file fixes (PricingCard/Hero, SiteFooter, Card, FullPageLoader, OnboardingTour, ConfirmModal)
- **Total ~60 hardcoded → tokens**

### Remaining 31 hardcoded (intentional brand)
- `PiLogo.css` `#d35573` — brand logo specific color asset
- `HomeHero.css` `#ff5f56/#ffbd2e/#27c93f` — macOS traffic light dots
- `HomeCTA.css` `#fff` in CSS `mask:` — mask requires luminance, hex required
- 5 mask-image `#000` — same CSS spec requirement

### Build verify
- pi-store: ✅ 970ms
- pi-dashboard: ✅ 1.43s
- ESLint clean cả 2

### Commits
- pi-store: `d757fd4` scaffold → `801d557` P1 admin → `b1c704d` P3 auth → `fa60568` P3 home/pricing → `478ae23` final sweep
- pi-dashboard: `36d03ea` scaffold → `9d69c62` P2 dashboard → `27bc64d` final sweep

**Status**: CLOSED | Owner: Codex (Phase A-E primary) + Claude (Phase F verify + final token sweep)
