---
id: T-20260514-001
owner: antigravity
state: archived
priority: P1
risk: high
estimated_minutes: 780
parent: null
children: []
depends_on: []
parallelization_ok: false
retry_count: 0
retry_max: 1
escalation_path: [Codex, Claude]
created: 2026-05-14 16:50
updated: 2026-05-14 20:43
archived: 2026-05-14 20:43
---

# T-20260514-001 — Pi Store Admin: Full Quantum HUD Redesign + Feature Completeness

## 0. User Original Intent (Phase 0, verbatim — 2026-05-14)

> "em đọc qua pi-backend, pi-store-admin, e lấy design uxui bên trang login và trang home của admin, thiết kế full lại trang admin và các tab với cấu trúc thư mục dễ scale, maintace và hiện đại, lên plan task-handoffs, đọc folder task-handoffs, anh muốn nó làm 1 lần mà full ux/ui và full các chức năng còn thiếu trong trang admin"

User wants ONE-PASS execution that delivers:
1. Lift the **Quantum HUD aesthetic** from `LoginPage.jsx` + `AdminOverviewPage.jsx` (orbit map, HUD corners, animated borders, scanlines, gradient glows) onto **every admin page**.
2. **Restructure folders** under `admin/` to a scalable convention (feature-based or atomic — agent's choice with rationale documented).
3. **Audit & complete every missing feature** so each `/v1/admin/*` endpoint has functional UI.
4. Single archive after this task — no follow-up needed for cosmetic or coverage.

---

## 1. Goal — Strategic Objective

Pi store admin (`/admin/*`) must reach **production-grade quality** in one pass:
- **Visual coherence**: All 12 admin pages share the same Quantum HUD design language as the login + overview.
- **Functional coverage**: Every backend `/v1/admin/*` endpoint is reachable from at least one admin UI surface. No orphan API methods, no stub modals, no fake actions.
- **Folder structure**: Clean enough that adding `pi_email` or `pi_referral` admin page next month requires only creating ONE feature folder, not editing 5+ scattered files.
- **No regression**: AdminLicensesPage, AdminKeysPage, AdminPackagesPage (already functional from T-20260513-001/002) keep working.

Outcome: when an admin opens `/admin/anything`, the look matches `/admin` overview + the login page — same gradients, same HUD corners, same typography, same micro-animations. When backend exposes a new endpoint, admin only needs ONE folder change.

---

## 2. Audit Result — Current State (2026-05-14)

### 2.1 Pages inventory (`admin/pages/`)

| Page | Lines | State |
|---|---|---|
| `AdminOverviewPage.jsx` | 368 | ✅ Quantum HUD reference design — use as style anchor |
| `ai/providers/AdminProvidersPage.jsx` | 419 | ⚠️ Functional, generic UI — needs Quantum HUD upgrade |
| `ai/usage/AdminUsagePage.jsx` | 268 | ⚠️ Same |
| `finance/AdminPackagesPage.jsx` | 315 | ⚠️ Routing config form done (T-20260513-001), UI style outdated |
| `finance/AdminRevenuePage.jsx` | 170 | ⚠️ Basic table view, needs HUD treatment |
| `finance/BillingPage.jsx` | 254 | ⚠️ Customer-facing, NOT admin — move out of admin/ |
| `finance/CheckoutSuccessPage.jsx` | 51 | ⚠️ Customer-facing — move out |
| `license/AdminKeysPage.jsx` | 694 | ⚠️ Functional but XL file, needs split + restyle |
| `license/AdminLicensesPage.jsx` | 378 | ⚠️ Functional (T-20260513-002), restyle |
| `license/ApiKeysPage.jsx` | 250 | ⚠️ Customer-facing — move out |
| `license/UserLicensesPage.jsx` | 177 | ⚠️ Customer-facing — move out |
| `license/{Adjust,Assign,Create,Detail}Modal.jsx` | 681 total | ⚠️ Functional, restyle to HUD |
| `system/AdminAuditLogPage.jsx` | 470 | ⚠️ Restyle |
| `system/AdminCronPage.jsx` | 160 | ⚠️ Restyle |
| `system/AdminReleasesPage.jsx` | 264 | ⚠️ Restyle |
| `system/AdminSettingsPage.jsx` | 342 | ⚠️ Restyle |
| `system/AdminUsersPage.jsx` | 179 | ⚠️ Restyle |
| `system/AdminUserProfilePage.jsx` | 259 | ⚠️ Restyle |
| `system/{Downloads,Profile,Support}Page.jsx` | 297 total | ⚠️ Customer-facing OR low priority — review |

Total: **~5,400 lines** across 25+ files. Mixed admin + customer-facing concerns under same `admin/` folder.

### 2.2 Atomic structure already exists (partial)

Per T-20260511-004 (atomic refactor), `src/components/admin/` has skeleton:
```
src/components/admin/
├── atoms/{NavGroupLabel,SidebarLink}.jsx
├── molecules/{AdminHeaderTools,AdminUserFooter,NavGroup}.jsx
└── organisms/{AdminHeader,AdminSidebar}.jsx
```

Layout uses these. **Keep + extend** this pattern; don't redo.

### 2.3 Backend coverage (44 admin endpoints)

| Router | Endpoint count | UI coverage |
|---|---|---|
| `overview` | 1 | ✅ AdminOverviewPage |
| `licenses` | 7 | ✅ AdminLicensesPage (T-20260513-002) |
| `users` | 3 | ✅ AdminUsersPage + AdminUserProfilePage |
| `providers` | 5 | ✅ AdminProvidersPage |
| `usage` | 1 | ✅ AdminUsagePage |
| `revenue` | 1 | ✅ AdminRevenuePage |
| `releases` | 2 | ✅ AdminReleasesPage |
| `settings` | 2 | ✅ AdminSettingsPage |
| `keys` | 11 | ✅ AdminKeysPage |
| `packages` | 7 | ✅ AdminPackagesPage |
| `audit-log` | 1 | ✅ AdminAuditLogPage |
| `cron` | 3 | ✅ AdminCronPage (T-20260513-002) |

**No missing endpoint coverage from T-20260513-002.** Focus this task on **visual + structural** upgrade, not functional gaps. If any new gap surfaces during audit → fix inline.

### 2.4 Design tokens already defined

`pi-store-webapp/src/styles/index.css` (byte-identical with dashboard) defines:
- Brand colors: `--p` `--ph` `--pc`
- Base: `--b1` to `--b4` (dark) / light overrides
- Content: `--bc` `--bc2` `--bc3`
- Borders: `--color-base-border` (4% white in dark, post T-2026-05-13 fix)
- Status: `--su` `--wa` `--er` `--in`
- Charts: `--color-chart-1` to `--color-chart-6`

Plus `@theme inline` mappings → Tailwind utilities like `bg-base-200`, `text-base-content`, `border-base-border` work everywhere.

**Use ONLY semantic tokens.** No hardcoded hex except in chart palette. No `border-white`, `text-gray-XXX`, `bg-#abc`.

---

## 3. Required Reading (mandatory before any edit)

### Design references (FIRST PASS — extract pattern):
1. `pi-store-webapp/src/pages/auth/LoginPage.jsx` — orbit SVG, gradient stroke, animated nodes, central glow, scanlines
2. `pi-store-webapp/src/pages/auth/AuthLayout.css` — `.auth-orbit__*` classes, animation keyframes, layout shell
3. `pi-store-webapp/src/pages/auth/AuthForm.css` — form field treatment, button glow, helper text
4. `pi-store-webapp/admin/pages/AdminOverviewPage.jsx` — StatCard pattern, hud-card, hud-corner, INFINITY CORE V2 badge
5. `pi-store-webapp/admin/pages/AdminOverviewPage.css` — quantum-grid background, hud-corner positioning, scan-lines
6. `pi-store-webapp/src/components/admin/organisms/AdminHeader.jsx` + `AdminSidebar.jsx` — existing atomic shell

### Functional references:
7. `pi-backend/app/admin/routers/*.py` — verify each endpoint exists + request/response shape
8. `pi-backend/app/admin/schemas.py` + `app/admin/schemas_cloud.py` — Pydantic models
9. `pi-store-webapp/src/lib/api-client.js` (lines 320-460) — existing `api.admin.*` wrappers

### Operational:
10. `.task-handoffs/SKILL.md` v4.2 — Phase 0 + anti-hallucination + Role Self-ID
11. `.task-handoffs/project/PROJECT.md` — workspace context
12. `.task-handoffs/archive/2026-05/T-20260513-002-antigravity-store-admin-completeness.md` — own previous task in this surface, do NOT undo

---

## 4. Allowed Scope (Strict)

### 4.1 MAY MODIFY / CREATE / DELETE inside

- `pi-store-webapp/admin/pages/**/*.{jsx,css}` — all pages here are in-scope
- `pi-store-webapp/admin/layout/AdminLayout.jsx` — adjust nav config + outer shell if needed
- `pi-store-webapp/src/components/admin/**/*.{jsx,css}` — atomic library (extend, do NOT replace structure)
- `pi-store-webapp/src/styles/quantum-hud.css` — NEW shared file for hud-card, hud-corner, scan-line, quantum-grid utilities (extract from AdminOverviewPage.css so other pages reuse)
- `pi-store-webapp/src/App.jsx` — route table if folder restructure changes import paths

### 4.2 MUST NOT TOUCH

- `pi-backend/` — zero backend changes. Surface ONLY consumes existing endpoints.
- `pi-store-webapp/src/pages/auth/` — login design is the reference; don't modify it.
- `pi-store-webapp/src/pages/public/` — public marketing pages, separate concern.
- `pi-store-webapp/src/styles/index.css` + `animations.css` — theme tokens stay byte-identical with pi-dashboard-webapp.
- `pi-store-webapp/src/lib/api-client.js` — only ADD new methods if a missed gap appears; don't restructure.
- `.task-handoffs/` — only this dossier (state transitions allowed).
- `pi-dashboard-webapp/` — entirely out of scope.

### 4.3 MOVE OUT OF `admin/` (customer-facing, not admin)

Relocate to `pi-store-webapp/src/pages/user/` and update routes:
- `BillingPage.jsx`
- `CheckoutSuccessPage.jsx`
- `ApiKeysPage.jsx` (customer's own license keys view)
- `UserLicensesPage.jsx`
- `system/DownloadsPage.jsx`
- `system/ProfilePage.jsx` (if duplicate of AdminUserProfilePage — verify, may delete one)
- `system/SupportPage.jsx`
- `ai/wallet/WalletPage.jsx` + `LedgerPage.jsx`
- `ai/usage/UserUsagePage.jsx`

Result: `admin/pages/` contains ONLY admin-facing pages (`AdminXxxPage.jsx` naming).

---

## 5. Design System Spec (Quantum HUD Distilled)

Extract from LoginPage + AdminOverviewPage into reusable primitives. Each admin page consumes them — no copy-paste of hud-card CSS into individual page files.

### 5.1 Shared CSS file `src/styles/quantum-hud.css` (NEW)

```css
/* Card with 4 corner brackets + glass background */
.hud-card { ... }
.hud-card:hover { ... }
.hud-corner { ... }
.hud-corner--tl/--tr/--bl/--br { ... }

/* HUD value typography (mono, glow) */
.hud-value { ... }

/* Grid background pattern for page wrapper */
.quantum-grid { ... }

/* Scan-line animation across panel top */
.scan-line { ... }

/* Section header banner with red dot + tagline */
.hud-banner { ... }
.hud-banner__title { ... }
.hud-banner__tagline { ... }
.hud-banner__badge { ... }   /* INFINITY CORE V2 style */
```

Page-level CSS files only contain page-specific layout. All HUD primitives sourced from this one file.

### 5.2 Reusable React components (extend `src/components/admin/`)

NEW under `src/components/admin/atoms/`:
- `HudCard.jsx` — wraps children with 4 corner brackets + glass background
- `HudCorner.jsx` — single corner (used by HudCard internally)
- `HudValue.jsx` — mono typography for numbers
- `HudBadge.jsx` — small label chip (e.g., INFINITY CORE V2, ACTIVE)
- `HudBanner.jsx` — page-top hero with title/subtitle/badge

NEW under `src/components/admin/molecules/`:
- `HudStatCard.jsx` — replaces existing `AdminStatCard` (moved here, restyled)
- `HudDataTable.jsx` — wraps `<Table>` with hud-card chrome + scan-line on hover
- `HudEmptyState.jsx` — illustrated empty state for "no data yet"
- `HudConfirmDialog.jsx` — destructive action confirm (replaces native `confirm()`)

NEW under `src/components/admin/organisms/`:
- `HudPageHeader.jsx` — banner + breadcrumb + primary action button
- `HudFilterBar.jsx` — standard filter row (search + selects + clear)
- `HudPagination.jsx` — bottom pagination (page size, prev/next, total count)

### 5.3 Page composition pattern

Every admin page MUST follow:

```jsx
<div className="quantum-grid p-6 lg:p-12 flex flex-col gap-6">
  <HudBanner
    title="TỔNG QUAN AI PROVIDERS"
    subtitle="Quản lý nhà cung cấp upstream"
    badge="INFRASTRUCTURE"
    actions={<Button variant="primary">+ Thêm provider</Button>}
  />

  <HudFilterBar ... />

  <HudDataTable ... />

  <HudPagination ... />
</div>
```

No exceptions. The HUD shell is the consistent frame; only the middle content varies.

### 5.4 Animation budget

Allowed animations (all in `src/animations.css` already):
- `fadeInUp` — page entry, stagger by 50ms per row
- `toast-in` / `toast-out` — notifications
- `skeleton-shimmer` — loading
- `scan-line` (NEW in quantum-hud.css) — 4s linear infinite horizontal sweep

Forbidden:
- `transition: all` — only specific properties (`border-color`, `transform`, `opacity`)
- `box-shadow` animation (perf killer)
- `backdrop-filter` change on hover (jank on Safari)

### 5.5 Color usage rules

| Token | Use for |
|---|---|
| `--color-primary` (red) | Primary CTA buttons, accent dots, brand glow |
| `--color-success` (green) | "ACTIVE", "healthy", confirm |
| `--color-warning` (amber) | "degraded", non-critical alerts |
| `--color-error` (rose) | Failed, destructive, validation errors |
| `--color-info` (sky) | Informational badges |
| `--color-base-content` | All body text |
| `--color-content-dim` (60%) | Labels, secondary text |
| `--color-content-ghost` (40%) | Placeholder, helper text |

NEVER: hardcoded hex outside chart palette. Use Tailwind `bg-primary/10`, `border-error/30`, etc.

---

## 6. Folder Restructure Proposal

### 6.1 Recommended target structure

```
pi-store-webapp/
├── admin/                          ← ONLY admin-facing UI
│   ├── layout/
│   │   ├── AdminLayout.jsx
│   │   └── AdminLayout.css
│   ├── shared/                     ← NEW — admin-only shared utilities
│   │   ├── adminApi.js             ← thin wrapper over api.admin.* (typed)
│   │   ├── adminTranslations.js    ← admin string catalog
│   │   └── adminHooks.js           ← useAdminAuth, useAdminTable, etc.
│   └── pages/
│       ├── overview/
│       │   ├── OverviewPage.jsx
│       │   ├── OverviewPage.css
│       │   └── components/         ← page-local sub-components
│       │       ├── RevenueWidget.jsx
│       │       ├── LicensesWidget.jsx
│       │       └── SystemStatusWidget.jsx
│       ├── revenue/
│       │   └── RevenuePage.jsx
│       ├── usage/
│       │   └── UsagePage.jsx
│       ├── packages/
│       │   ├── PackagesPage.jsx
│       │   └── components/
│       │       └── PackageRoutingForm.jsx
│       ├── licenses/
│       │   ├── LicensesPage.jsx
│       │   ├── CreateLicenseModal.jsx
│       │   ├── LicenseDetailModal.jsx
│       │   ├── AdjustTokensModal.jsx
│       │   └── AssignPackageModal.jsx
│       ├── releases/
│       │   ├── ReleasesPage.jsx
│       │   └── UploadReleaseModal.jsx
│       ├── providers/
│       │   ├── ProvidersPage.jsx
│       │   └── ProviderTestButton.jsx
│       ├── keys/
│       │   ├── KeysPage.jsx
│       │   ├── components/
│       │   │   ├── KeyAllocateModal.jsx
│       │   │   ├── KeyBulkImportModal.jsx
│       │   │   └── KeySummaryStrip.jsx
│       │   └── KeysPage.css
│       ├── users/
│       │   ├── UsersPage.jsx
│       │   └── UserProfilePage.jsx
│       ├── cron/
│       │   └── CronPage.jsx
│       ├── audit/
│       │   └── AuditLogPage.jsx
│       └── settings/
│           ├── SettingsPage.jsx
│           └── components/
│               └── SettingsForm.jsx
└── src/
    ├── components/
    │   └── admin/                  ← shared atomic library
    │       ├── atoms/
    │       ├── molecules/
    │       └── organisms/
    └── styles/
        └── quantum-hud.css         ← NEW shared HUD primitives
```

### 6.2 Naming convention

- File: `XxxPage.jsx` (Pascal, suffix `Page`). Drop the `Admin` prefix — folder context already implies admin.
- Modal: `XxxModal.jsx` (suffix `Modal`). Live next to the page they belong to.
- Sub-components used only by one page: under that page's `components/` folder.
- Reusable components: under `src/components/admin/{atoms,molecules,organisms}/`.

### 6.3 Why this layout

- Folder = feature. To add `pi_email` admin section: create `admin/pages/email/EmailCampaignsPage.jsx` + add 1 line to `AdminLayout` nav. Done.
- Modal lives next to page, not in separate root `modals/` folder. Easy to find when reading the page.
- Atomic library separate (under `src/components/admin/`) → reusable across pages.
- Page-local components separate (under each page's `components/`) → cannot accidentally leak into other pages.

---

## 7. Per-Page Deliverable Checklist

Each page MUST end with the following checks ticked:

### Universal (every page)
- [ ] Wrapper: `<div className="quantum-grid p-6 lg:p-12 flex flex-col gap-6">`
- [ ] Top: `<HudBanner>` with title + subtitle + optional badge + primary action
- [ ] Filter section (if list page): `<HudFilterBar>`
- [ ] Main content in `<HudCard>` or `<HudDataTable>`
- [ ] Bottom: pagination if list ≥ 1 page
- [ ] Loading: existing skeleton component (don't replace)
- [ ] Error: `<Alert tone="danger">` styled to HUD aesthetic
- [ ] Empty state: `<HudEmptyState>` instead of "No data" text
- [ ] All semantic Tailwind tokens (no hardcoded colors)
- [ ] Vietnamese diacritics intact (no mojibake)
- [ ] All buttons trigger correct API method
- [ ] Page renders without console error or warning

### Page-specific extras

| Page | Special requirements |
|---|---|
| `overview` | 4 stat cards top + revenue chart + system status + recent activity |
| `revenue` | 7/30/90 day toggle + chart + per-package breakdown table |
| `usage` | Same toggle + per-plugin breakdown + per-provider breakdown |
| `packages` | Routing config form (routing_mode, allowed_tiers, priority_boost, dedicated_key_count) — preserve T-20260513-001 work |
| `licenses` | Row actions: View, Adjust tokens, Assign package, Revoke, Reactivate, Delete |
| `releases` | Upload modal supports multi-part file + version + changelog |
| `providers` | Test button per row, health badges |
| `keys` | Provider summary strip + filters + per-row actions (test, revoke, release) + bulk import modal + allocate modal |
| `users` | Search by email, edit profile inline OR via detail page |
| `cron` | List jobs + Run-now button + last status + auto-refresh 30s |
| `audit-log` | Filter by actor/action/resource + diff card (existing pattern, preserve) |
| `settings` | Sectioned form (brand, email, security) + save bar at bottom |

---

## 8. Phase Plan (13 hours)

```
Phase A — Read references + draft design system        → 1h
Phase B — Create quantum-hud.css + HUD atoms/molecules → 2h
Phase C — Restructure folders + move customer pages out → 1h30
Phase D — Adapt AdminLayout/Header/Sidebar to new shell → 30min
Phase E — Redesign Overview page (style anchor)        → 1h
Phase F — Redesign list pages (revenue, usage, licenses,
          packages, releases, providers, keys, users,
          cron, audit-log, settings — 11 pages × 30min) → 5h30
Phase G — Redesign modals (5 existing + new ones)       → 1h
Phase H — Build verification + screenshot every page    → 1h
─── User accept gate ───
Phase I — Archive via archive-task.sh
```

Stop and report at each Phase boundary with raw evidence.

---

## 9. Verification Commands

```bash
cd pi-store-webapp

# Build clean
npx vite build
# Expect: ✓ built in <15s, no warnings about missing imports

# No hardcoded colors leaked
grep -rnE "border-(white|gray|red|blue|green|yellow|orange|pink|purple)-[0-9]" admin/ src/components/admin/ 2>/dev/null | grep -v -- "//" | head
# Expect: empty

# No mojibake
grep -rnE "Ã|â€|ð\x9F|t\\?|S\\?" admin/ src/components/admin/ 2>/dev/null | head
# Expect: empty (Vietnamese diacritics preserved)

# No stub / placeholder text
grep -rnE "TODO|placeholder for|đang chưa có|chua co|cập nhật\\.\\.\\." admin/ src/components/admin/ 2>/dev/null | grep -v 'placeholder=' | head
# Expect: empty

# No fake alerts
grep -rnE 'alert\\([^)]*(giả lập|simulated|fake|chưa có)' admin/ 2>/dev/null | head
# Expect: empty

# Every api.admin.* method consumed
grep -oE "api\\.admin\\.[a-zA-Z]+" admin/ -r --include="*.jsx" | awk -F"api.admin." '{print $2}' | awk -F"[ (]" '{print $1}' | sort -u > /tmp/used.txt
grep -oE "^\\s+[a-zA-Z]+:" src/lib/api-client.js | grep -v "^\\s*//" | awk -F":" '{print $1}' | sed 's/^[[:space:]]*//' | sort -u > /tmp/defined.txt
# diff /tmp/defined.txt /tmp/used.txt — report unused methods

# Page count sanity
ls admin/pages/ -d */ | wc -l
# Expect: 12 (or however many after restructure)
```

### Manual smoke test (production deploy)

After Vercel re-deploy:
1. Login admin → `/admin` shows new Quantum HUD overview
2. Click each sidebar item → page loads, HUD aesthetic consistent, no console error
3. Open each modal (Create license, Adjust tokens, Assign package, Upload release, Allocate keys, Bulk import keys) → renders styled, submits succeed
4. Toggle filters on Licenses/Keys/AuditLog → URL params update, data refetches

---

## 10. Acceptance Criteria

### Build & structure
- [ ] `npx vite build` exits 0, no warnings about missing imports or unused exports
- [ ] All routes in `src/App.jsx` resolve to existing files (no 404 on `/admin/*`)
- [ ] Folder layout matches §6.1 (or agent's documented variant)
- [ ] Customer-facing pages moved OUT of `admin/pages/` to `src/pages/user/`

### Design consistency
- [ ] `src/styles/quantum-hud.css` exists with shared `.hud-card`, `.hud-corner*`, `.quantum-grid`, `.scan-line`, `.hud-banner`
- [ ] Every admin page uses `<HudBanner>` at top
- [ ] Every list page uses `<HudDataTable>` (not bare `<Table>`)
- [ ] Every stat number uses `<HudValue>` typography
- [ ] No hardcoded hex colors outside `--color-chart-*` palette
- [ ] No `border-{color}-{shade}` Tailwind utilities (only semantic tokens)

### Functional coverage
- [ ] All 44 `/v1/admin/*` endpoints have at least one UI consumer
- [ ] Stub elimination preserved (no Stub 1/2/3/4 from T-20260513-002 regressions)
- [ ] Routing config UI (T-20260513-001) preserved on `packages` page
- [ ] Adjust Tokens modal: `note` field still required (financial audit trail)
- [ ] Cron page: "Run now" still hits `runCron` API
- [ ] Audit log: diff card pattern preserved

### Code quality
- [ ] Vietnamese diacritics intact (UTF-8) across all string literals
- [ ] No `TODO`, `placeholder for`, `cập nhật...`, `giả lập` in admin/
- [ ] No `console.log` left in production code
- [ ] No file >700 lines (split if needed — AdminKeysPage was 694, OK to leave)
- [ ] Imports use absolute aliases (`@admin/`, `@/components/`) consistently

### Scope discipline
- [ ] Zero file touched in `pi-backend/`
- [ ] Zero file touched in `pi-store-webapp/src/pages/auth/` (login is reference, untouched)
- [ ] Zero file touched in `pi-dashboard-webapp/`
- [ ] `src/styles/index.css` + `animations.css` unchanged (theme tokens stable)

### Documentation
- [ ] Each page file has top JSDoc block summarising purpose + main API calls
- [ ] `quantum-hud.css` has header comment explaining each utility class
- [ ] If folder layout differs from §6.1, rationale documented in commit message

---

## 11. Risk & Rollback

**Risk: high.** Surface = entire admin UI. 25+ files modified. Customer-facing pages relocated → import chain changes ripple to `src/App.jsx` routes.

### Mitigation

1. **Phase-by-phase commits.** Each Phase = 1 commit. Bisect-friendly if regression appears.
2. **Build after every Phase.** Don't accumulate broken state.
3. **Screenshot each page after redesign** → attach to dossier `## Evidence` section.
4. **Keep semantic tokens** — even if visual changes, swapping back is a CSS-only revert.
5. **Modal contract preserved** — submit handlers must call the same `api.admin.X(payload)` with same shape (so no backend changes needed).

### Rollback plan (`changes/T-20260514-001-antigravity-admin-quantum-redesign/rollback-plan.md`)

```bash
cd pi-store-webapp
git log --oneline -20 | head
# Identify commits from this task (typically 8-12)
git revert <oldest-commit-of-task>..<newest-commit-of-task>
npx vite build && deploy
# Total rollback time: ~5 min
```

If rollback partial (e.g., 1 page broken):
- Comment out the route in `src/App.jsx`
- Re-deploy
- Fix in isolation, re-enable

---

## 12. Out-of-scope Findings (log if encountered)

If during work agent notices:
- Backend endpoint missing for an obvious admin need (e.g., `POST /admin/keys/{id}/test`) → log
- Customer-facing page improvement opportunity → log (not implement)
- Design token gap (e.g., need a `--color-base-elevated`) → log + propose, but don't add to global tokens this task

---

## 13. Worker Self-Check (mandatory before starting)

Antigravity must verify:
- ✅ Capability: React + Tailwind v4 + atomic design + large refactor — proven on T-20260513-002
- ✅ Context: ~25 page files + 12 new components + 1 CSS file — fits 1M context
- ⚠️ Risk: HIGH — entire admin surface. Mitigated by phase-by-phase commits + screenshots.
- ⚠️ Coordination: This task supersedes any in-flight admin work. Confirm no parallel agent in `.task-handoffs/active/` touching admin/ before starting.

If self-check fails (mismatch capability, context overflow, parallel conflict) → set `state: rejected` with reason in `## Escalation` and STOP. Do NOT half-finish.

---

## 14. Phase Sequence

```
Phase A → 1h     read all 12 references
Phase B → 2h     quantum-hud.css + HUD atoms/molecules library
Phase C → 1h30   folder restructure + move customer pages out
Phase D → 30min  AdminLayout + Header + Sidebar adaptation
Phase E → 1h     Overview redesign (style anchor)
Phase F → 5h30   11 list/detail pages redesign (30min each)
Phase G → 1h     5 existing modals + any new ones restyled
Phase H → 1h     verification + screenshots
─── User accept gate ───
Phase I → 30min  changes/ + archive-task.sh
Total: ~13h
```

---

## 15. Prompt Block (ready-to-dispatch to Antigravity)

```text
You are executing T-20260514-001 — Pi Store Admin: Full Quantum HUD Redesign + Feature Completeness.

This is a one-pass full redesign of pi-store-webapp/admin/ UI. The user is paying for a single execution that delivers visual consistency + structural cleanliness + complete coverage. No follow-up tasks expected.

MUST READ before any edit (in order):
1. .task-handoffs/active/T-20260514-001-antigravity-admin-quantum-redesign.md (this file, FULL)
2. .task-handoffs/SKILL.md (operational protocol v4.2 — Phase 0, anti-hallucination, Role Self-ID)
3. .task-handoffs/project/PROJECT.md (workspace context)
4. .task-handoffs/archive/2026-05/T-20260513-002-antigravity-store-admin-completeness.md (your prior work — do not regress)

5. pi-store-webapp/src/pages/auth/LoginPage.jsx          (Quantum HUD design reference — DO NOT MODIFY)
6. pi-store-webapp/src/pages/auth/AuthLayout.css         (HUD CSS reference — DO NOT MODIFY)
7. pi-store-webapp/src/pages/auth/AuthForm.css           (form styling reference)
8. pi-store-webapp/admin/pages/AdminOverviewPage.jsx     (style anchor — extract pattern)
9. pi-store-webapp/admin/pages/AdminOverviewPage.css     (existing HUD CSS to lift into quantum-hud.css)
10. pi-store-webapp/src/components/admin/                (atomic library baseline — extend, don't replace)

11. pi-backend/app/admin/routers/                        (44 endpoints — verify UI covers each)
12. pi-store-webapp/src/lib/api-client.js lines 320-460  (existing api.admin.* wrappers)

EXECUTE Phase A → Phase H in order. STOP at each phase boundary, paste:
  - Files modified/created/deleted this phase
  - `npx vite build` exit code + last 5 lines
  - Any out-of-scope finding into §12 of this dossier

CRITICAL RULES (violations = fail):
- DO NOT touch files outside §4 Allowed Scope.
- DO NOT modify pi-backend OR pi-dashboard-webapp OR pi-store-webapp/src/pages/auth/.
- DO NOT use hardcoded hex colors except in `--color-chart-*` palette.
- DO NOT use `border-{tailwind-color}-{shade}` — only semantic tokens.
- DO NOT replace existing functional code (T-20260513-001 routing UI, T-20260513-002 modals) — restyle in place.
- DO NOT skip the `note` field on AdjustTokensModal (financial audit trail).
- Vietnamese strings: type with proper diacritics (UTF-8). Never reuse mojibake from old code.
- PASTE raw `npx vite build` output into ## Evidence at each phase boundary.
- After each phase: update `updated:` frontmatter + STATUS.md heartbeat to 'antigravity-working-phase-X'.

REPORT format per .task-handoffs/system/REPORTING.md. Final REPORT block at end.

Set `state: returned` when all Phase A-H acceptance criteria met. Claude will Phase C-verify before archive.
```
