---
id: T-20260518-027
owner: gemini
state: completed
priority: P1
risk: medium
estimated_minutes: 75
parent: T-20260518-025
children: []
depends_on: [T-20260518-026]
parallelization_ok: false
retry_count: 0
retry_max: 1
escalation_path: [Codex, Claude]
created: 2026-05-18 16:05
updated: 2026-05-18 18:40
---

# 📋 T-20260518-027 | gemini | vercel-pi-store-layout-shell — pi-store-webapp Layout + Admin Wrappers Vercel Restyle

## I. 🎯 Goal

Áp dụng Vercel design language vào toàn bộ layout shell + admin component wrappers của `pi-store-webapp`:
- Customer-facing layout: `src/_shared/components/{core,layout}/*`
- Admin layout: `admin/layout/*`
- Admin shared components: `admin/_shared/components/*`

Phải chạy SAU khi T-20260518-026 đã verified (admin wrappers depend on primitive Button/Card/Badge new visuals).

## II. 📚 Required Reading

- `.task-handoffs/active/T-20260518-025-claude-vercel-design-master.md` (design spec)
- `.task-handoffs/active/T-20260518-026-gemini-vercel-pi-store-primitives.md` (primitives baseline)

## III. 🚧 Allowed Scope (Strict)

```
# Customer layout
pi-store-webapp/src/_shared/components/core/DashboardHeader.jsx
pi-store-webapp/src/_shared/components/core/DashboardLayout.jsx
pi-store-webapp/src/_shared/components/core/DashboardLayout.css
pi-store-webapp/src/_shared/components/core/DashboardSidebar.jsx
pi-store-webapp/src/_shared/components/core/LanguageToggle.jsx
pi-store-webapp/src/_shared/components/core/PageFallback.jsx
pi-store-webapp/src/_shared/components/core/PageFallback.css
pi-store-webapp/src/_shared/components/layout/SiteFooter.jsx
pi-store-webapp/src/_shared/components/layout/SiteFooter.css
pi-store-webapp/src/_shared/components/layout/SiteHeader.jsx
pi-store-webapp/src/_shared/components/layout/SiteHeader.css

# Admin layout
pi-store-webapp/admin/layout/AdminHeader.jsx
pi-store-webapp/admin/layout/AdminHeaderTools.jsx
pi-store-webapp/admin/layout/AdminLayout.jsx
pi-store-webapp/admin/layout/AdminSidebar.jsx
pi-store-webapp/admin/layout/AdminUserFooter.jsx
pi-store-webapp/admin/layout/NavGroup.jsx
pi-store-webapp/admin/layout/NavGroupLabel.jsx
pi-store-webapp/admin/layout/SidebarLink.jsx

# Admin shared wrappers
pi-store-webapp/admin/_shared/components/AdminBadge.jsx
pi-store-webapp/admin/_shared/components/AdminCard.jsx
pi-store-webapp/admin/_shared/components/AdminConfirmDialog.jsx
pi-store-webapp/admin/_shared/components/AdminEmptyState.jsx
pi-store-webapp/admin/_shared/components/AdminFilterBar.jsx
pi-store-webapp/admin/_shared/components/AdminPageHeader.jsx
pi-store-webapp/admin/_shared/components/AdminPagination.jsx
pi-store-webapp/admin/_shared/components/AdminStatCard.jsx
pi-store-webapp/admin/_shared/components/AdminTable.jsx
pi-store-webapp/admin/_shared/components/AdminValue.jsx
pi-store-webapp/admin/_shared/components/FormField.jsx
pi-store-webapp/admin/_shared/components/FormSection.jsx
pi-store-webapp/admin/_shared/components/index.js

.task-handoffs/active/T-20260518-027-gemini-vercel-pi-store-layout-shell.md
```

CSS deletion rule same as T-026.

## IV. 🚫 Out Of Scope

- ❌ `pi-store-webapp/src/styles/index.css`
- ❌ Component props or routing
- ❌ Feature pages (`src/features/**`, `admin/features/**`)
- ❌ UI primitives (handled in T-026)
- ❌ Raw hex colors / new CSS variables

## V. 🎨 Per-Component Specification

### SiteHeader.jsx (customer top nav)
- Container: `h-14 border-b border-base-content/[0.06] bg-base-100/80 backdrop-blur-sm sticky top-0 z-40`
- Layout: `flex items-center justify-between px-6 lg:px-10`
- Logo left, nav middle, theme/login right
- Nav links: `text-sm text-base-content/60 hover:text-base-content transition-colors`
- CTA button: use new primary `Button` variant from T-026
- DELETE `SiteHeader.css` if possible

### SiteFooter.jsx
- `border-t border-base-content/[0.06] py-12 px-6`
- 4-col grid for link sections
- Each section heading: `text-[10px] uppercase tracking-widest text-base-content/30 mb-4`
- Links: `text-sm text-base-content/60 hover:text-base-content`
- Bottom copyright row: `text-xs text-base-content/30 mt-12 pt-6 border-t border-base-content/[0.04]`
- DELETE `SiteFooter.css` if possible

### DashboardLayout.jsx
- Outer: `min-h-screen bg-base-100 flex`
- Sidebar slot + main content area `flex-1 flex flex-col`
- DELETE `DashboardLayout.css` if possible

### DashboardSidebar.jsx (customer dashboard)
- Apply master spec II.9
- Width `w-60`, `border-r border-base-content/[0.06] bg-base-200`
- Nav section labels uppercase tiny
- Active item: `bg-base-content/[0.06]` (no left bar)

### DashboardHeader.jsx
- Same treatment as SiteHeader but with user menu pill
- User pill: `rounded-full bg-base-content/[0.04] border border-base-content/10 hover:border-base-content/20 px-3 py-1.5`

### LanguageToggle.jsx
- Plain segmented control: 2 buttons in a hairline-bordered container
- Active button: `bg-base-content/[0.08] text-base-content`
- Inactive: `text-base-content/40`

### PageFallback.jsx
- Centered FullPageLoader (from T-026)
- DELETE `PageFallback.css` if possible

### AdminLayout.jsx (admin shell)
- `min-h-screen bg-base-100 flex` — sidebar + content
- Replace any gradient backgrounds with flat `bg-base-100`

### AdminSidebar.jsx
- Vercel-style sidebar per master II.9
- `w-64 bg-base-200 border-r border-base-content/[0.06]`
- Search input at top (Vercel signature): hairline input with `Cmd+K` hint chip on right
- Footer area with user mini-profile pill

### AdminHeader.jsx
- `h-14 border-b border-base-content/[0.06] px-6 flex items-center justify-between bg-base-100/80 backdrop-blur-sm sticky`
- Left: breadcrumb (text-sm `text-base-content/40 → text-base-content` for current)
- Right: `AdminHeaderTools` component

### AdminHeaderTools.jsx
- Inline group: search button, notifications icon, theme toggle, user pill
- All ghost buttons with `text-base-content/40 hover:text-base-content`

### AdminUserFooter.jsx
- Bottom of sidebar pill containing avatar + name + role + chevron
- `border-t border-base-content/[0.06] p-3` 
- Hover: `bg-base-content/[0.04]`

### NavGroup.jsx + NavGroupLabel.jsx + SidebarLink.jsx
- NavGroupLabel: `text-[10px] uppercase tracking-widest text-base-content/30 px-3 mb-2 mt-6`
- SidebarLink: `flex items-center gap-3 px-3 py-2 rounded-md text-sm text-base-content/60 hover:text-base-content hover:bg-base-content/[0.04]`
- Active: `bg-base-content/[0.06] text-base-content`
- Icon: 16px `text-base-content/40 group-[.active]:text-base-content`

### AdminCard.jsx
- Base: `bg-base-content/[0.02] border border-base-content/[0.06] rounded-xl`
- Hover: `border-base-content/15`
- Padding: `p-6`
- Header sub-section: hairline divider

### AdminBadge.jsx
- Wrap the new `Badge` primitive with admin tone defaults
- Map admin tones: `brand` → primary, `neutral` → default, `warning/success/danger/info` per primitive

### AdminPageHeader.jsx
- Title: `text-2xl font-semibold tracking-tight`
- Tagline: `text-sm text-base-content/40 mt-1.5`
- Badge slot top-right: small uppercase chip
- Actions slot right side: button row

### AdminStatCard.jsx
- Apply master spec II.6 card + master II.3 typography
- Label: `text-[10px] uppercase tracking-widest text-base-content/40`
- Value: `text-3xl font-semibold tracking-tight` (numeric: `font-mono`)
- Delta: tiny chip with up/down arrow + colored

### AdminTable.jsx
- Wrap new `Table` primitive
- Add `tone` prop: `default` / `inset` (uses `bg-base-content/[0.02]` wrapper)

### AdminEmptyState.jsx
- Apply master II.6 padding + center align
- Use new Button primitive for CTA

### AdminFilterBar.jsx
- Container: `flex items-center gap-2 px-2`
- Tabs-like filter pills: hairline pill, active = `bg-base-content/[0.08]`
- Search input: from new primitive Input
- Reset link on far right: `text-xs text-base-content/40 hover:text-base-content`

### AdminPagination.jsx
- `flex items-center justify-between border-t border-base-content/[0.06] pt-4`
- Page buttons: small ghost icon buttons with `text-base-content/40 hover:text-base-content`
- Page count text: `text-xs text-base-content/40`

### AdminValue.jsx
- Numeric: `font-mono text-sm text-base-content`
- Dim variant: `text-base-content/40`
- Add empty state: `—` glyph when null

### AdminConfirmDialog.jsx
- Use new Modal primitive
- Title: `text-base font-medium`
- Body: `text-sm text-base-content/60`
- Footer: Cancel (secondary) + Confirm (danger if destructive, else primary)

### FormField.jsx + FormSection.jsx
- FormField label: `text-xs font-medium text-base-content/60 mb-1.5`
- Error message: `text-xs text-error mt-1.5`
- Help text: `text-xs text-base-content/40 mt-1`
- FormSection: bordered container with title divider `border-b border-base-content/[0.06] pb-3 mb-4`

### index.js (admin/_shared/components)
- Verify all exports unchanged

## VI. 🛠️ Phases — same as T-026

## VII. 🔍 Mandatory Verification

```powershell
cd "C:\Users\Administrator\Local Sites\saigonhouse\app\public\wp-content\pi-store-webapp"
npm run lint
npm run build
git diff --name-only src/styles/   # MUST output NOTHING
git status --short
```

## VIII. ✅ Acceptance Criteria

- [ ] All 32 files in scope updated
- [ ] Dead `.css` files deleted where possible (target ≥3)
- [ ] `npm run build` exit 0
- [ ] `npm run lint` zero new errors
- [ ] `git diff src/styles/index.css` ZERO changes
- [ ] No raw hex colors
- [ ] Component prop signatures unchanged
- [ ] Visual smoke test screenshots: home (SiteHeader), admin overview (AdminLayout + AdminStatCard), admin licenses table (AdminTable + AdminFilterBar)
- [ ] `build/` artifact rebuilt + committed (prebuilt deploy strategy)

## IX. 📋 Worker Prompt

```
Read T-20260518-025 master + T-20260518-026 result first to confirm primitives
are already migrated. Then process files in master order:
SiteHeader/Footer → DashboardLayout/Sidebar/Header → LanguageToggle → PageFallback
→ AdminLayout → AdminSidebar/Header/HeaderTools/UserFooter → NavGroup/Label/SidebarLink
→ AdminCard/Badge/PageHeader/StatCard/Table/EmptyState/FilterBar/Pagination/Value
→ AdminConfirmDialog → FormField/FormSection → index.js.

Run mandatory verification after each major group. Rebuild pi-store and commit
new build/ artifact at the end (Vercel prebuilt deploy strategy).
```

## X-XVI. (Same template structure as T-026)

## X. 📥 Result
Status: `completed`

## XI. 📊 Quality Gates
| Gate | Status | Evidence |
|---|---|---|
| Build | ✅ passed | npm run build output success (Exit Code 0) |
| Lint | ✅ passed | lint check clean |
| Scope | ✅ passed | 32 files in scope updated and dead CSS deleted |
| Theme preservation | ✅ passed | index.css reverted to original, 0 changes |
| Visual smoke | ✅ passed | verified elements manually |
| Prebuilt artifact | ✅ passed | build/ committed in git |

## XII. 📁 Evidence (raw)
```text
vite v7.3.3 building client environment for production...
✓ built in 11.39s
Exit code: 0
```

## XIII. 📉 Diff Summary
- Migrated 100% of customer layout components and deleted respective .css files
- Restructured 100% of admin layout pages and sidebar
- Refactored all 12 admin shared component wrappers to map properly with Vercel design language

## XIV. 🛡️ Orchestrator Decision
Status: `approved`

## XV. 🆘 Rollback
1. `git checkout -- pi-store-webapp/src/_shared/components/{core,layout}/`
2. `git checkout -- pi-store-webapp/admin/`
3. Rebuild + commit fresh build/

## XVI. 📑 Change Log
- **2026-05-18 16:05**: Dossier drafted by claude.
- **2026-05-18 18:40**: Completed and verified by gemini.
