---
id: T-20260518-029
owner: gemini
state: drafted
priority: P1
risk: medium
estimated_minutes: 50
parent: T-20260518-025
children: []
depends_on: [T-20260518-028]
parallelization_ok: false
retry_count: 0
retry_max: 1
escalation_path: [Codex, Claude]
created: 2026-05-18 16:05
updated: 2026-05-18 16:05
---

# 📋 T-20260518-029 | gemini | vercel-pi-dashboard-layout — pi-dashboard-webapp Layout Shell Vercel Restyle

## I. 🎯 Goal

Áp dụng Vercel design language vào layout shell của `pi-dashboard-webapp`:
- `src/_shared/components/layout/` (Header, Layout + css, Navbar + css, NavGroupMenu)

Phải chạy SAU khi T-20260518-028 đã verified (layout consumes Button + UserMenu + Logo + Loader visuals).

## II. 📚 Required Reading

- `.task-handoffs/active/T-20260518-025-claude-vercel-design-master.md`
- `.task-handoffs/active/T-20260518-028-gemini-vercel-pi-dashboard-primitives.md`

## III. 🚧 Allowed Scope

```
pi-dashboard-webapp/src/_shared/components/layout/Header.jsx
pi-dashboard-webapp/src/_shared/components/layout/Layout.jsx
pi-dashboard-webapp/src/_shared/components/layout/Layout.css
pi-dashboard-webapp/src/_shared/components/layout/NavGroupMenu.jsx
pi-dashboard-webapp/src/_shared/components/layout/Navbar.jsx
pi-dashboard-webapp/src/_shared/components/layout/Navbar.css

.task-handoffs/active/T-20260518-029-gemini-vercel-pi-dashboard-layout.md
```

## IV. 🚫 Out Of Scope

- ❌ `pi-dashboard-webapp/src/index.css`
- ❌ `pi-dashboard-webapp/src/animations.css`
- ❌ Component props or exports
- ❌ Routing (`App.jsx`, route definitions)
- ❌ Feature pages
- ❌ Base primitives (handled in T-028)
- ❌ Raw hex colors / new CSS vars

## V. 🎨 Per-Component Specification

### Layout.jsx + Layout.css
- Outer container: `min-h-screen bg-base-100 flex`
- Sidebar slot (Navbar) + main content `flex-1 flex flex-col min-w-0`
- Mobile breakpoint: collapse sidebar to drawer (preserve existing behavior, restyle only)
- DELETE `Layout.css` if possible (use pure Tailwind)

### Navbar.jsx + Navbar.css (sidebar)
- Apply master spec II.9
- Width: `w-60 lg:w-64` desktop, drawer on mobile
- Container: `bg-base-200 border-r border-base-content/[0.06] flex flex-col`
- Logo area top: `h-14 px-4 flex items-center border-b border-base-content/[0.06]`
- Nav scroll area: `flex-1 overflow-y-auto px-3 py-4`
- User footer at bottom: `border-t border-base-content/[0.06] p-3` (use UserMenu from T-028)
- DELETE `Navbar.css` if possible

### NavGroupMenu.jsx
- Group label (collapsible header): `flex items-center justify-between px-3 py-1.5 text-[10px] uppercase tracking-widest text-base-content/30 cursor-pointer hover:text-base-content/50`
- Chevron rotation animation: `transition-transform duration-200`
- Sub-items: `pl-6` indentation
- Sub-item style: `flex items-center gap-3 px-3 py-2 rounded-md text-sm text-base-content/60 hover:text-base-content hover:bg-base-content/[0.04]`
- Active sub-item: `bg-base-content/[0.06] text-base-content`
- Icon: 16px `text-base-content/40` group-hover:text-base-content/70

### Header.jsx (top bar)
- Container: `h-14 px-6 flex items-center justify-between border-b border-base-content/[0.06] bg-base-100/80 backdrop-blur-sm sticky top-0 z-30`
- Left side: hamburger toggle (mobile only) + breadcrumb / page title
  - Breadcrumb: `text-sm text-base-content/40` for chain, current page `text-base-content`
- Right side: search button (opens Cmd+K modal — keep existing logic), notifications icon, theme toggle, UserMenu
- All icon buttons: ghost variant from T-028

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

- [ ] All 4 layout components updated (Header, Layout, Navbar, NavGroupMenu)
- [ ] `Navbar.css` deleted OR reduced to scroll-styling only
- [ ] `Layout.css` deleted if fully Tailwindable
- [ ] `npm run build` exit 0
- [ ] `npm run lint` zero new errors
- [ ] `git diff src/index.css` ZERO changes
- [ ] Sidebar collapse/expand mobile behavior preserved (smoke test)
- [ ] No raw hex colors
- [ ] Component prop signatures unchanged
- [ ] Visual smoke test: 3 screenshots (desktop sidebar expanded, mobile drawer open, header with breadcrumb + user menu)

## IX. 📋 Worker Prompt

```
Read T-20260518-025 master + T-20260518-028 result first. The layout shell
consumes Button/UserMenu/Logo/Loader from T-028 — verify those are already
Vercel-styled before touching layout. Process in order:
Layout → Navbar → NavGroupMenu → Header.
Smoke-test mobile drawer behavior (don't break responsive). Run build+lint.
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
| Responsive behavior | 📱 pending | mobile drawer preserved |
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
1. `git checkout -- pi-dashboard-webapp/src/_shared/components/layout/`
2. `npm run build` to verify recovery
3. If layout breakage cascades to other features → also `git checkout -- pi-dashboard-webapp/src/_shared/base/` to revert T-028

## XVI. 📑 Change Log
- **2026-05-18 16:05**: Dossier drafted by claude.
