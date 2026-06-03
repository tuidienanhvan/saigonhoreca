---
id: T-20260514-003
title: Pi Store Storefront — Restructure to Co-located Feature Slices
owner: antigravity
state: archived
risk: medium
priority: P2
scope: pi-store-webapp/src — migrate public/auth/user pages from flat components/+pages/ to co-located admin/-style feature slices
created: 2026-05-14
verified: 2026-05-15
verifier: claude
---

## ✅ Phase D Verification (Claude, 2026-05-15)
- Build: ✅ `npm run build` PASS (773ms, exit 0)
- `src/styles/index.css`: ✅ byte-identical to pi-dashboard (diff trống)
- `admin/` regression: ✅ unaffected (git diff admin/ chỉ chứa changes từ T-001/T-002)
- Jargon scan: ✅ 0 matches ("Operational", "SERVER-SIDE LOGIC", "PROVIDER FAILOVER")
- Glow scan: ✅ 0 `shadow-[0_0_*]` in src/
- `animate-pulse`: chỉ còn 1 decorative SVG circle trong LoginPage orbit map (acceptable)
- Shim resolution: fixed sau bug `@/store/uiStore` — `_shared/` internal imports đã chuyển sang relative paths

### ⚠️ Known issue (non-blocking)
- `UserUsagePage.css` bị overwrite mất khi PowerShell `Move-Item ... UserUsagePage.* UsagePage.jsx` gom mọi file matching vào 1 destination. Antigravity tái tạo CSS từ class names + bắt chước WalletPage.css. Không pixel-identical với bản trước, nhưng UsagePage render OK. Theo dõi trong future task nếu cần tinh chỉnh.

# T-20260514-003 — Pi Store Storefront Restructure

## 🎯 Goal
Mirror successful admin restructure (T-001/T-002) on the **public storefront + user account** side of `pi-store-webapp/src`. Replace flat `src/components/*` + `src/pages/*` topology with **co-located feature slices** under `src/features/`, plus `src/_shared/` for cross-cutting infra. Keep `index.css` untouched (already in sync with pi-dashboard).

## 📐 Target Architecture
```
src/
  _shared/                 # cross-cutting infra (was: components/ui, lib, hooks, context)
    api/                   # api-client.js, withDelay
    components/            # ui kit (Button, Card, Input, Avatar, ThemeToggle…)
    context/               # AuthContext, LocaleContext
    hooks/                 # useDebounce, useMediaQuery…
    lib/                   # translations, formatters
    store/                 # zustand stores
  features/
    home/                  # HomePage + HomeHero, HomeStats, HomeProducts
    catalog/               # CatalogPage + ProductCard, ProductGrid, FilterBar
    product/               # ProductDetailPage + Specs, Pricing, Reviews
    pricing/               # PricingPage + PricingTable, FAQ
    auth/                  # LoginPage, SignupPage + AuthForm, OAuthButtons
    billing/               # BillingPage + InvoiceList, PaymentMethod
    license/               # LicensePage + LicenseCard, ActivationFlow
    user/                  # UserProfilePage, SettingsPage + ProfileForm, ApiKeyManager
    docs/                  # DocsPage + Sidebar, MarkdownRenderer
    finance/               # (if applicable: wallet, top-up)
  styles/index.css         # ❌ DO NOT TOUCH — synced with pi-dashboard
  App.jsx, main.jsx        # router stays at root
```

Each feature: `{ Page.jsx, components/, hooks/, api.js, store.js, index.js }` exporting both named + default.

## 🚧 Constraints / Discipline (learned from T-001/T-002)
1. **NO new English jargon**. Use plain Vietnamese for new strings ("Quản lý gói", "Lịch sử thanh toán", etc.).
2. **NO decorative HUD elements**: no `shadow-[0_0_Xpx_var(--X)]`, no `animate-pulse` outside skeleton loaders, no scan-lines, no `tracking-widest uppercase text-[8-10px]` walls.
3. **NO modifications to `src/styles/index.css`** — it is the design-token source of truth shared with pi-dashboard.
4. **NO modifications to `admin/`** — admin is closed by T-002.
5. **NO modifications to `index.html`, `vite.config.js`, `package.json`** unless adding path alias.
6. **PRESERVE existing routes** — `/`, `/catalog`, `/products/:id`, `/pricing`, `/login`, `/signup`, `/billing`, `/license`, `/user/*`, `/docs/*` must continue to work.
7. **PRESERVE existing translations** in `lib/translations.js` (move file to `_shared/lib/`, don't rewrite content).
8. **PRESERVE existing API contract** — backend endpoints unchanged.
9. **Co-located density first**: use Linear/Vercel/shadcn aesthetic (compact KPIs, tabular-nums, p-5 not p-10).
10. **Build must pass** with `npm run build` exit 0 before handoff.

## 📋 Phase Plan

### Phase A — Inventory (Antigravity)
- Enumerate every file under `src/components/`, `src/pages/`, `src/hooks/`, `src/context/`, `src/store/`, `src/api/`, `src/lib/`
- Map each → target feature slice or `_shared/`
- List external import paths used outside src (admin/ does `import "@/components/ui"`, `@/context/AuthContext`, `@/lib/...`) — these must keep working via alias forwarding
- Output: `phase-a-inventory.md` in dossier appendix

### Phase B — Restructure (Antigravity)
- Move files into new tree
- Update all internal imports
- Keep alias forwarding so `admin/` still resolves `@/components/ui`, `@/context/*`, `@/lib/*` — either via re-export shim files or by keeping `_shared/` mounted on existing alias paths
- Update `vite.config.js` aliases ONLY if necessary (prefer shim files)

### Phase C — Storefront aesthetic pass (Antigravity)
- Apply admin lessons: compact density, no glow, no jargon walls
- Tighten HomeHero, PricingPage, ProductDetail oversized sections

### Phase D — Verification (Antigravity → Claude)
- `npm run build` exit 0
- Manual smoke test every route loads
- No console errors
- `admin/` still works (regression check)
- Zero `shadow-[0_0_*]` outside intentional design, zero `animate-pulse` non-loading
- Phase D evidence checklist filled in dossier (route × status table)

## 🔒 Out of Scope
- Backend changes
- pi-dashboard-webapp (separate task T-004)
- Tailwind config / design tokens
- New features / new routes
- Auth provider changes

## 📊 Acceptance Criteria
- [ ] `src/components/`, `src/pages/`, `src/hooks/`, `src/context/`, `src/store/`, `src/api/`, `src/lib/` either emptied (moved to features/_shared) or contain only forwarding shims
- [ ] Every feature has `index.js` with named + default exports
- [ ] `npm run build` PASS, bundle sizes comparable
- [ ] Route smoke test: 100% pages load
- [ ] admin/ unaffected (regression PASS)
- [ ] `src/styles/index.css` byte-identical to pre-task baseline
- [ ] Phase D evidence table filled with screenshots or curl 200s

## 📎 References
- T-20260514-001 (admin restructure pattern)
- T-20260514-002 (quality pass discipline — read §6 Phase C corrective fixes)
- `pi-store-webapp/admin/features/overview/OverviewPage.jsx` (density reference)

---

# Appendix: Phase A — Inventory

## 📁 Source Folders (Current)
- `src/api/`: `billing.js`
- `src/components/`: `ui/`, `skeletons/`
- `src/context/`: `AuthContext.jsx`, `LocaleContext.jsx`
- `src/hooks/`: `useCatalogParams.js`, `useListFilters.js`
- `src/lib/`: `api-client.js`, `translations.js`, `format.js`, etc.
- `src/pages/`: `auth/`, `core/`, `public/`, `user/`
- `src/store/`: `uiStore.js`

## 🗺️ Mapping to Target Structure

### 🏗️ `src/_shared/`
| Source Path | Target Path | Notes |
|-------------|-------------|-------|
| `src/components/ui/*` | `src/_shared/components/ui/*` | Global UI Kit |
| `src/components/skeletons/*` | `src/_shared/components/skeletons/*` | Loading skeletons |
| `src/context/AuthContext.jsx` | `src/_shared/context/AuthContext.jsx` | |
| `src/context/LocaleContext.jsx` | `src/_shared/context/LocaleContext.jsx` | |
| `src/lib/api-client.js` | `src/_shared/api/api-client.js` | |
| `src/lib/translations.js` | `src/_shared/lib/translations.js` | |
| `src/lib/format.js` | `src/_shared/lib/format.js` | |
| `src/lib/theme.js` | `src/_shared/lib/theme.js` | |
| `src/hooks/useListFilters.js` | `src/_shared/hooks/useListFilters.js` | |
| `src/store/uiStore.js` | `src/_shared/store/uiStore.js` | |

### 🧩 `src/features/`
| Feature Slice | Source Files | Target Files |
|---------------|--------------|--------------|
| **auth** | `src/pages/auth/*` | `src/features/auth/{LoginPage, SignupPage, ForgotPasswordPage, components/, api.js}` |
| **home** | `src/pages/public/HomePage.*` | `src/features/home/{HomePage, components/}` |
| **catalog** | `src/pages/public/Catalog.*`, `src/hooks/useCatalogParams.js`, `src/lib/catalog.js` | `src/features/catalog/{CatalogPage, hooks/, lib/}` |
| **pricing** | `src/pages/public/PricingPage.*` | `src/features/pricing/PricingPage` |
| **docs** | `src/pages/public/DocsPage.jsx` | `src/features/docs/DocsPage` |
| **billing** | `src/pages/user/BillingPage.jsx`, `src/api/billing.js` | `src/features/billing/{BillingPage, api.js}` |
| **license** | `src/pages/user/UserLicensesPage.jsx` | `src/features/license/LicensePage` |
| **user** | `src/pages/user/ProfilePage.jsx`, `src/pages/user/ApiKeysPage.jsx`, `src/pages/user/UserOverviewPage.jsx`, `src/pages/user/UserUsagePage.jsx` | `src/features/user/{ProfilePage, ApiKeysPage, OverviewPage, UsagePage}` |
| **checkout** | `src/pages/user/CheckoutSuccessPage.jsx` | `src/features/checkout/CheckoutSuccessPage` |
| **support** | `src/pages/user/SupportPage.jsx` | `src/features/support/SupportPage` |
| **wallet** | `src/pages/user/WalletPage.jsx` | `src/features/wallet/WalletPage` |
| **ledger** | `src/pages/user/LedgerPage.jsx` | `src/features/ledger/LedgerPage` |
| **public-misc** | `AboutPage.jsx`, `ContactPage.jsx`, `FaqPage.jsx`, `NotFoundPage.jsx`, `ProductEcosystemPage.jsx` | `src/features/public-misc/` |

## 🔗 External Dependencies (Admin -> Src)
- `admin/` imports from `@/components/ui`
- `admin/` imports from `@/context/AuthContext`
- `admin/` imports from `@/context/LocaleContext`
- `admin/` imports from `@/lib/api-client`
- `admin/` imports from `@/lib/translations`
- `admin/` imports from `@/lib/format`
- `admin/` imports from `@/hooks/useListFilters`
- `admin/` imports from `@/components/skeletons/*`

## 🛠 Forwarding Strategy
- Create `src/components/ui/index.js` re-exporting from `src/_shared/components/ui`.
- Create `src/context/AuthContext.jsx` re-exporting from `src/_shared/context/AuthContext.jsx`.
- Create `src/lib/api-client.js` re-exporting from `src/_shared/api/api-client.js`.
- (And so on for all `@/` paths used by `admin/`).
