---
id: T-20260518-028
owner: gemini
state: drafted
priority: P1
risk: medium
estimated_minutes: 60
parent: T-20260518-025
children: []
depends_on: [T-20260518-027]
parallelization_ok: false
retry_count: 0
retry_max: 1
escalation_path: [Codex, Claude]
created: 2026-05-18 16:05
updated: 2026-05-18 16:05
---

# 📋 T-20260518-028 | gemini | vercel-pi-dashboard-primitives — pi-dashboard-webapp Base Primitives Vercel Restyle

## I. 🎯 Goal

Áp dụng Vercel design language vào tất cả base primitives của `pi-dashboard-webapp`:
- `src/_shared/base/*` (Button, Checkbox, CountUp, PageLoader, PageTitle, PiLogo, Select, StatusBadge, UserMenu)
- `src/_shared/components/ui/*` (Loader)

Note: `pi-dashboard-webapp` chưa có UI primitives đầy đủ như `pi-store-webapp`. Worker chỉ refactor visual treatment các file đã tồn tại — **KHÔNG được tạo file mới** (Alert.jsx, Modal.jsx, etc.) trong scope này.

## II. 📚 Required Reading

- `.task-handoffs/active/T-20260518-025-claude-vercel-design-master.md`
- `.task-handoffs/active/T-20260518-026-gemini-vercel-pi-store-primitives.md` (reference visual treatment)

## III. 🚧 Allowed Scope

```
pi-dashboard-webapp/src/_shared/base/Button.jsx
pi-dashboard-webapp/src/_shared/base/Checkbox.jsx
pi-dashboard-webapp/src/_shared/base/CountUp.jsx
pi-dashboard-webapp/src/_shared/base/PageLoader.jsx
pi-dashboard-webapp/src/_shared/base/PageTitle.jsx
pi-dashboard-webapp/src/_shared/base/PiLogo.jsx
pi-dashboard-webapp/src/_shared/base/PiLogo.css
pi-dashboard-webapp/src/_shared/base/Select.jsx
pi-dashboard-webapp/src/_shared/base/StatusBadge.jsx
pi-dashboard-webapp/src/_shared/base/UserMenu.jsx
pi-dashboard-webapp/src/_shared/base/UserMenu.css
pi-dashboard-webapp/src/_shared/components/ui/Loader.jsx

.task-handoffs/active/T-20260518-028-gemini-vercel-pi-dashboard-primitives.md
```

## IV. 🚫 Out Of Scope

- ❌ `pi-dashboard-webapp/src/index.css` (theme palette)
- ❌ `pi-dashboard-webapp/src/animations.css`
- ❌ Component props or exports
- ❌ Feature pages (`src/features/**`)
- ❌ Layout shell (handled in T-029)
- ❌ Creating NEW primitive files
- ❌ Raw hex colors / new CSS vars
- ❌ Adding/removing npm packages
- ❌ Touching React Query / store / context

## V. 🎨 Per-Component Specification

### Button.jsx
- Apply master spec II.4 — 5 variants (primary/secondary/ghost/danger/brand)
- Remove `shadow-primary-glow` and all glow effects
- Typography: `font-medium tracking-tight` (NOT uppercase by default — only if `uppercase` prop passed)
- Sizes: sm/md/lg matching pi-store Button

### Checkbox.jsx
- Replace `duration-500 scale-110` animation with `duration-150` color shift
- Match pi-store Checkbox structure: 16px square, hairline border, checked = `bg-base-content text-base-100`
- Label class: `text-xs font-semibold tracking-wide text-base-content/60 uppercase` (per existing UX intent in pi-dashboard)

### CountUp.jsx
- Numeric display: `font-mono tracking-tight text-base-content` (per master II.3)
- Preserve animation logic (intersection observer) — only restyle text

### PageLoader.jsx
- Apply master spec II.6 — hairline spinner: `border border-base-content/10 border-t-primary rounded-full animate-spin`
- Caption (if any): `text-[10px] uppercase tracking-widest text-base-content/40`

### PageTitle.jsx
- `text-2xl font-semibold tracking-tight text-base-content`
- Subtitle slot: `text-sm text-base-content/40 mt-1.5`
- No gradient text effects

### PiLogo.jsx + PiLogo.css
- Remove "breathing" glow animation (the heavy GPU-eating one)
- Hover: `transition-transform duration-200 hover:scale-105 active:scale-95 hover:rotate-3`
- Keep PiLogo.css ONLY for keyframes that can't be expressed in Tailwind utilities — otherwise DELETE and remove import

### Select.jsx
- Apply master spec II.5 (bg-base-content/[0.02] + hairline border + focus opacity shift)
- Chevron icon `text-base-content/30` absolute right
- Native `<select>` styling: `appearance-none` + custom chevron

### StatusBadge.jsx
- Apply master spec II.7 — Pill shape `rounded-full`, hairline tinted border
- Auto-tone mapping: `publish` → success, `pending` → warning, `spam`/`error` → danger, `draft` → neutral, `info` → info
- Status dot prepended: `<span className="w-1.5 h-1.5 rounded-full bg-{tone}"/>`

### UserMenu.jsx + UserMenu.css
- Rewrite to 100% Tailwind. The current BEM + heavy shadow looks dated
- Trigger pill: `flex items-center gap-2 rounded-full bg-base-content/[0.04] border border-base-content/10 hover:border-base-content/20 px-3 py-1.5`
- Dropdown: `bg-base-200 border border-base-content/[0.06] rounded-lg shadow-xl py-1`
- Menu item: `px-3 py-2 text-sm text-base-content/60 hover:text-base-content hover:bg-base-content/[0.04]`
- Icon: `text-base-content/40` 16px
- Logout item: `text-error hover:bg-error/10`
- DELETE `UserMenu.css` after migration (use pure Tailwind)

### Loader.jsx
- Same as PageLoader spinner but smaller (16-20px default)
- Plain hairline spinner

## VI. 🛠️ Phases — same as T-026

## VII. 🔍 Mandatory Verification

```powershell
cd "C:\Users\Administrator\Local Sites\saigonhouse\app\public\wp-content\pi-dashboard-webapp"
npm run lint
npm run build
git diff --name-only src/index.css   # MUST output NOTHING
git diff --name-only src/animations.css   # MUST output NOTHING
git status --short
```

## VIII. ✅ Acceptance Criteria

- [ ] All 10 component files updated
- [ ] `UserMenu.css` deleted (full Tailwind migration achievable)
- [ ] `PiLogo.css` deleted OR reduced to keyframes-only with semantic tokens
- [ ] `npm run build` exit 0
- [ ] `npm run lint` zero new errors
- [ ] `git diff src/index.css` ZERO changes
- [ ] `git diff src/animations.css` ZERO changes
- [ ] No raw hex colors anywhere in modified files
- [ ] Component prop signatures + exports unchanged
- [ ] Visual smoke test: 3 screenshots (login page using Button+Input, dashboard root using PageTitle+StatusBadge, user menu open showing dropdown)

## IX. 📋 Worker Prompt

```
Read T-20260518-025 master + T-20260518-026 result first. The pi-dashboard
primitive set is SMALLER than pi-store — only refactor the 10 files listed.
DO NOT create new files. Process in order: Button → Select → Checkbox →
StatusBadge → PageLoader → Loader → PageTitle → CountUp → PiLogo → UserMenu.
Run build+lint after UserMenu (highest risk).
```

## X. 📥 Result
Status: `not-started`

## XI. 📊 Quality Gates
| Gate | Status | Evidence |
|---|---|---|
| Build | 🏗️ pending | |
| Lint | 🧹 pending | |
| Scope | 📂 pending | |
| Theme preservation | 🎨 pending | |
| CSS deletion target | 🧽 pending | UserMenu.css must go |
| Visual smoke | 👁️ pending | |

## XII. 📁 Evidence
```text
$ (pending)
```

## XIII. 📉 Diff Summary
(pending)

## XIV. 🛡️ Orchestrator Decision
Status: `pending`

## XV. 🆘 Rollback
1. `git checkout -- pi-dashboard-webapp/src/_shared/base/`
2. `git checkout -- pi-dashboard-webapp/src/_shared/components/ui/Loader.jsx`
3. `npm run build` to verify recovery

## XVI. 📑 Change Log
- **2026-05-18 16:05**: Dossier drafted by claude.
