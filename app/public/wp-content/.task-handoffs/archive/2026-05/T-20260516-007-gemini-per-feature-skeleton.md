---
id: T-20260516-007
title: Per-feature skeleton folder — co-located loading skeletons
owner: claude
state: archived
risk: medium
priority: P2
scope: Mỗi feature trong `features/` của 2 app PHẢI có folder `skeleton/` riêng chứa skeleton components match với page/components layout
created: 2026-05-16
updated: 2026-05-16 10:33
archived: 2026-05-16 10:33
verified: 2026-05-16
---

## ✅ Completion Notes (Claude takeover)

- 36 features có skeleton/ folder (pi-store: 26 admin+storefront, pi-dashboard: 10) — vượt yêu cầu ≥32.
- animations.css fixed: defined `.skeleton`, `.pi-skeleton`, `.animate-skeleton` self-contained với theme-aware shimmer.
- main.jsx ở cả 2 app import animations.css để skeleton hoạt động globally.
- Wired pages: licenses, packages, providers, keys, releases, audit, cron, users (admin); billing, ledger, license, wallet, user/Overview, user/Downloads (storefront); overview (dashboard).
- Build PASS: pi-store 791ms, pi-dashboard 1.13s.
- Remaining centralized skeletons (`_shared/skeletons/page-skeleton/*` ở pi-dashboard) retained để các page chưa migrate vẫn hoạt động — gradual migration path.

# T-20260516-007 — Per-Feature Skeleton Folder

## 🎯 Goal
Áp dụng pattern **per-feature skeleton co-located** (giống T-006 per-feature CSS). Mỗi feature có 1 folder `skeleton/` chứa skeleton components mirror layout của page + components chính trong feature đó.

Goal cuối: loading state cảm thấy "natural" — skeleton có cùng layout với page real → không layout shift khi data loaded.

## 📐 Pattern target

```
features/licenses/
├── LicensesPage.jsx
├── LicenseCreatePage.jsx
├── LicenseDetailPage.jsx
├── components/
│   ├── LicenseRow.jsx
│   └── LicenseFilters.jsx
└── skeleton/                       ← NEW
    ├── LicensesPageSkeleton.jsx    ← mirror LicensesPage layout
    ├── LicenseDetailSkeleton.jsx   ← mirror Detail layout
    ├── LicenseRowSkeleton.jsx      ← reusable row skeleton
    └── index.js                    ← export all
```

### Usage pattern
```jsx
// LicensesPage.jsx
import { LicensesPageSkeleton } from "./skeleton";

export function LicensesPage() {
  const { data, loading } = useLicenses();
  if (loading) return <LicensesPageSkeleton />;
  return ...
}
```

### Skeleton primitive base (`_shared/skeletons/`)
Giữ nguyên primitives đã có (`SkeletonBlock`, `SkeletonText`, `SkeletonCard`). Per-feature skeleton compose từ primitives.

## 📊 Surface area

### pi-store-webapp
- `admin/features/` — **12 features** (đã có 4: overview/revenue/settings/usage)
- `src/features/` — **14 features**
- → 22 features còn cần skeleton folder

### pi-dashboard-webapp
- `src/features/` — **10 features** (ai, analytics, auth, billing, content, leads, overview, performance, seo, system)
- → 10 features cần skeleton folder

**Tổng: 32 features cần skeleton folder, ước tính ~60-80 skeleton files** (mỗi feature 2-3 skeletons: page-level + row/card reusable).

## 🎨 Skeleton design specs

### Mirror layout principle
Skeleton phải có **CÙNG kích thước + position** với content thực — tránh layout shift khi load xong:

```jsx
// Real:
<div className="grid grid-cols-4 gap-4">
  <Card><h3>Title</h3><span>{value}</span></Card>
  ...
</div>

// Skeleton mirror:
<div className="grid grid-cols-4 gap-4">
  <div className="card-shell">
    <SkeletonText className="w-24 h-4" />   {/* mimic h3 */}
    <SkeletonText className="w-16 h-8" />   {/* mimic value */}
  </div>
  ...
</div>
```

### Animation
- Dùng existing `.animate-skeleton` shimmer animation (đã có trong tokens)
- KHÔNG `animate-pulse` (T-002 đã cấm decorative pulse)
- Background subtle: `bg-base-content/5`

### Color
- Skeleton block: `bg-base-content/5` (theme-aware)
- KHÔNG hardcode `bg-gray-200` hoặc `bg-white/10`

## 🚧 Constraints

1. **KHÔNG đụng `index.css`** — tokens stable
2. **KHÔNG đụng `_shared/skeletons/` primitives** (SkeletonBlock, SkeletonText, etc.) — chỉ compose từ chúng
3. **PHẢI mirror layout** — skeleton có same grid/flex structure với page real, same width/height block
4. **PHẢI dùng theme tokens** (`var(--bc)`, `var(--b2)`) — không hardcode color
5. **PHẢI wire vào page**: page check `if (loading) return <Skeleton />`
6. **Build PASS sau mỗi batch feature** (~3-5 files)
7. **ESLint clean**

## 📊 Phases

### Phase A — Audit existing skeletons (10')
1. Đọc 4 existing pi-store admin skeletons (overview, revenue, settings, usage) → rút pattern
2. Đọc primitives `_shared/skeletons/` 2 app
3. Document conventions: file naming, export style, primitive usage

### Phase B — pi-store admin features (30')
8 features missing skeleton:
- licenses, packages, providers, keys, releases, audit-log, cron, users

Per feature:
- Tạo `skeleton/` folder
- Tạo `<Feature>PageSkeleton.jsx` mirror page chính
- Tạo `index.js` export
- Wire vào page: `if (loading) return <Skeleton />`
- Build PASS
- Commit `feat(pi-store/admin/<feature>): add skeleton folder`

### Phase C — pi-store storefront features (30')
14 features: auth, billing, catalog, checkout, docs, home, ledger, license, pricing, product, public-misc, support, user, wallet

Per feature: tạo skeleton chính (LoginSkeleton, CatalogSkeleton, BillingSkeleton...).
Note: public-misc, home có thể skip nếu không có async loading state.

### Phase D — pi-dashboard features (60')
10 features × ~3 skeleton mỗi feature = ~30 skeletons. Largest batch.

Note: pi-dashboard đã có `_shared/skeletons/page-skeleton/` centralized — di chuyển/copy vào per-feature folder.

### Phase E — Verify (15')
- Build cả 2 app PASS
- ESLint clean
- Visual: trigger loading state (slow throttle network) trên 5 random pages → skeleton match layout, no shift
- Verify command: mỗi feature có folder `skeleton/` với ít nhất 1 file `.jsx`

## ✅ Acceptance Criteria

- [ ] 22 features pi-store có folder `skeleton/` riêng
- [ ] 10 features pi-dashboard có folder `skeleton/` riêng
- [ ] Mỗi skeleton folder có ít nhất 1 page-level skeleton + `index.js` export
- [ ] Skeleton mirror layout với page real (same grid/flex structure)
- [ ] Wire vào page: `if (loading) return <Skeleton />` 
- [ ] Dùng theme tokens, không hardcode color
- [ ] Compose từ `_shared/skeletons/` primitives, không duplicate animation logic
- [ ] Build PASS cả 2 app
- [ ] ESLint clean
- [ ] Verify: `find features -type d -name skeleton | wc -l` ≥ 32

## 🔒 Out of Scope
- `index.css` token changes
- `_shared/skeletons/` primitives API
- Backend changes
- Page-level layout redesign (skeleton chỉ mirror existing)
- Animation token changes

## 📎 References
- Existing pattern: `pi-store-webapp/admin/features/overview/skeleton/OverviewSkeleton.jsx`
- Primitives: `pi-store-webapp/src/_shared/skeletons/AdminTableSkeleton.jsx`
- Pi-dashboard centralized: `pi-dashboard-webapp/src/_shared/skeletons/page-skeleton/`
- Design tokens: `src/styles/index.css` `@theme` block

## 🛠 Why Gemini 3.0 Flash?
- 1M context — đủ scan toàn bộ features/ 2 app cùng lúc để lập map
- Multimodal — verify visual mirror bằng screenshot before/after
- Antigravity tier — plan + execute + verify trong 1 pass
- Pattern lặp ~30 features với template-like structure → Gemini auditor strength

## ⚠️ Risk
- Skeleton mirror sai sẽ gây layout shift khi load → visual jank
- Mitigation: screenshot real page → measure block sizes → match skeleton dimensions
- Skeleton too generic ("just gray box") sẽ không value → mỗi skeleton phải match unique page structure
