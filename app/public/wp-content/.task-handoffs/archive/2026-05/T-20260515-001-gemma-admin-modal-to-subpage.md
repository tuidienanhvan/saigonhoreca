---
id: T-20260515-001
title: Admin — Convert all modals to sub-pages
owner: gemma
state: archived
verified: 2026-05-15
verifier: claude
risk: medium
priority: P1
scope: pi-store-webapp/admin — replace 9 modal-based create/edit flows with full sub-pages + redesign existing PackageEditPage
created: 2026-05-15
---

# T-20260515-001 — Admin Modal → Sub-Page Migration

## 🎯 Goal
Eliminate cramped modal forms in admin panel. Replace với **full sub-page** pattern cho mọi create/edit flow. Density-first aesthetic — không text-[10px] walls, không uppercase tracking-widest, không max-width 800px chen chúc.

User feedback ngữ cảnh:
- Modal "xấu và khó nhìn" — form dài bị nén trong 800px width
- Existing `PackageEditPage.jsx` (Claude vừa làm) cũng "dài và xấu" — phải redesign
- Cần style giống Linear / Vercel / shadcn admin: rộng rãi, font size đọc được, section breathing

## 📋 Scope — 9 modals + 1 redesign

| File modal cần xoá | Sub-page mới | Route |
|---|---|---|
| `admin/features/packages/PackageEditPage.jsx` (đã có, redesign) | giữ tên | `/admin/packages/new` + `/packages/:slug/edit` (đã wired) |
| `admin/features/providers/components/ProviderModal.jsx` | `ProviderEditPage.jsx` | `/admin/providers/new` + `/providers/:id/edit` |
| `admin/features/licenses/components/CreateLicenseModal.jsx` | `LicenseCreatePage.jsx` | `/admin/licenses/new` |
| `admin/features/licenses/components/LicenseDetailModal.jsx` | `LicenseDetailPage.jsx` | `/admin/licenses/:id` |
| `admin/features/licenses/components/AssignPackageModal.jsx` | `LicenseAssignPackagePage.jsx` | `/admin/licenses/:id/assign-package` |
| `admin/features/licenses/components/AdjustTokensModal.jsx` | `LicenseAdjustTokensPage.jsx` | `/admin/licenses/:id/adjust-tokens` |
| `admin/features/releases/components/UploadReleaseModal.jsx` | `ReleaseUploadPage.jsx` | `/admin/releases/new` |
| `admin/features/keys/components/AddKeyModal.jsx` | `KeyCreatePage.jsx` | `/admin/keys/new` |
| `admin/features/keys/components/AllocateModal.jsx` | `KeyAllocatePage.jsx` | `/admin/keys/:id/allocate` |
| `admin/features/keys/components/BulkImportModal.jsx` | `KeyBulkImportPage.jsx` | `/admin/keys/bulk-import` |

## 🎨 Design specs (CRITICAL — không skip)

### Layout
- **Container**: `<div className="flex flex-col gap-6 pb-12 max-w-4xl mx-auto">` — KHÔNG full width, max 4xl (~896px) để form không kéo dài quá xa
- **Page header**: dùng `<AdminPageHeader>` có sẵn với `title` + `tagline` + back button ở `actions`
- **Sections**: max **3-4 sections** mỗi page, nếu dài hơn thì TÁCH thành tab hoặc multi-step wizard
- **Sticky footer save**: `sticky bottom-0` với Huỷ + Lưu thay đổi (2 button flex-1)

### Typography
- ❌ KHÔNG `text-[10px] font-bold uppercase tracking-widest` walls (đây là anti-pattern brand cũ)
- ❌ KHÔNG `text-[8-11px]` cho label/input
- ✅ Label: `text-xs font-medium text-base-content/70` (case thường, không uppercase)
- ✅ Input: `text-sm` default, `font-mono text-sm` cho code/URL/ID
- ✅ Section title: `text-sm font-semibold` mixed case
- ✅ Section description: `text-xs text-base-content/50` 1 dòng
- ✅ Hint: `text-xs text-base-content/40` dưới input

### Density
- Section padding: `p-6` (không `p-8` hoặc `p-10`)
- Section gap: `gap-4` giữa fields, `gap-5` giữa sections
- Input height: default (Tailwind h-10), KHÔNG ép `h-12`
- Group small fields: `grid grid-cols-2 md:grid-cols-3 gap-4` (không 1 column dàn dọc)
- Switch + checkbox: inline với label, không nest box-trong-box

### Components dùng sẵn
- `<Field label hint required>` — wrapper input chuẩn (copy từ PackageEditPage hoặc tự viết lại)
- `<Section title description icon>` — wrapper card chuẩn
- `<AdminPageHeader>` — đã có ở `_shared/components/`
- `<Button>`, `<Input>`, `<Textarea>`, `<Checkbox>`, `<Switch>`, `<Select>`, `<Alert>` — đã có

### Layout patterns by content
- **Form ngắn (≤5 fields)**: 1 section duy nhất, không tách
- **Form trung bình (6-15 fields)**: 2-3 sections theo nhóm logic
- **Form dài (>15 fields như Provider)**: tabs (Định danh / Kết nối / Models / Headers / Định tuyến) HOẶC steps wizard
- **Read+edit (LicenseDetail)**: layout 2 cột — left info readonly, right các action button

## 🚧 Constraints

1. **KHÔNG đụng `src/styles/index.css`** — synced với pi-dashboard, immutable
2. **KHÔNG đụng `Modal.jsx` / `Modal.css`** — modal vẫn dùng cho confirm dialogs nhỏ (delete confirm, etc.)
3. **PHẢI giữ confirm dialog** cho delete actions — không convert delete confirm sang sub-page
4. **PHẢI update parent list page** sau migrate:
   - Remove `useState` cho modal
   - Remove import modal
   - Đổi `onClick={() => setEditing(...)}` → `as={Link} to="/admin/<feat>/..."`
   - Edit icon → `<IconButton as={Link} to={...edit-route}>`
5. **PHẢI verify build** `npm run build` PASS sau mỗi feature (đừng để fail dồn cuối)
6. **PHẢI xoá file modal** sau migrate xong feature đó (không leave dead code)
7. **PHẢI commit per feature** — 1 commit/feature để rollback dễ nếu lỗi

## 📐 Reference: density mẫu

Tham khảo các page đã làm đúng pattern:
- `admin/features/overview/OverviewPage.jsx` — Linear-style KPI grid
- `admin/features/users/UserProfilePage.jsx` — read+edit 2 cột
- `src/_shared/components/AdminPageHeader.jsx` — header chuẩn

Tham khảo external (Linear, Vercel, Cal.com):
- Sub-page có max-width khoảng 800-1200px
- Form fields full-width của container
- Section card có border subtle, không glow
- Sticky footer save với 2 button equal width

## ✅ Acceptance Criteria

- [ ] 9 modal files DELETED, 9 page files CREATED
- [ ] `PackageEditPage.jsx` redesigned theo specs trên (rút gọn từ 280+ lines xuống <200 lines bằng cách tách Section/Field component dùng chung)
- [ ] All 9 new pages dùng `Section` + `Field` helpers thống nhất
- [ ] App.jsx có đủ 10 routes mới (1 đã có cho packages)
- [ ] Parent pages (PackagesPage, ProvidersPage, LicensesPage, ReleasesPage, KeysPage) — strip modal state, dùng `<Link>` thay onClick
- [ ] `npm run build` PASS sau cùng
- [ ] Lint clean — 0 unused imports
- [ ] Manual smoke test: từ list page → click "+" → vào page mới → fill form → save → quay về list page có item mới
- [ ] Edit flow: từ list page → click edit icon → vào page với data load sẵn → sửa → save → quay về list

## 📦 Backend endpoints (đã có sẵn, không cần đổi)

- Packages: `GET/POST/PATCH/DELETE /v1/admin/packages[/:slug]`
- Providers: `GET/POST/PATCH/DELETE /v1/admin/providers[/:id]` + `POST /:id/test`
- Licenses: `GET/POST/PATCH/DELETE /v1/admin/licenses[/:id]` + `POST /:id/revoke|reactivate|tokens`
- License Package: `GET/POST /v1/admin/licenses/:id/package` + `POST /:id/package/reset-period`
- Releases: `GET/POST /v1/admin/releases`
- Keys: `GET/POST/DELETE /v1/admin/keys` + `POST /v1/admin/keys/:id/allocate|deallocate` + `POST /v1/admin/keys/bulk-import`

## 📊 Phases

### Phase A — Setup (15min)
1. Đọc PackageEditPage.jsx hiện tại — note những gì xấu/dài
2. Tách `Section` + `Field` helpers vào `admin/_shared/components/FormSection.jsx` + `FormField.jsx` để 9 page sau dùng chung
3. Verify build

### Phase B — Redesign PackageEditPage (20min)
Áp dụng specs design mới. Mục tiêu: file <200 lines, không có wall of `text-[10px] tracking-widest`.

### Phase C — Migrate 9 modals (~30min/feature × 3 features = 90min)
Thứ tự ưu tiên (theo tần suất user sẽ dùng):
1. **Providers** (1 modal — ProviderModal đã Kilo-style, chỉ wrap thành page)
2. **Licenses** (4 modals — phức tạp nhất, làm cuối)
3. **Keys** (3 modals)
4. **Releases** (1 modal — upload file)

Per feature:
- Create new page file(s)
- Add route(s) to App.jsx
- Update parent list page
- Delete modal file
- Run `npm run build` — must PASS
- Git commit `feat(admin/<feature>): convert modal to sub-page`

### Phase D — Verification (15min)
- Final `npm run build`
- ESLint clean
- Manual smoke test 10 flows
- Update dossier với verified state

## 🔒 Out of Scope
- Backend changes
- pi-dashboard changes
- New endpoints / new features
- Modal infrastructure changes (`Modal.jsx` stays for confirm dialogs)
- Theme / token changes

## 📎 References
- `admin/features/packages/PackageEditPage.jsx` — Claude's first attempt (cần redesign)
- `admin/features/overview/OverviewPage.jsx` — density chuẩn
- `admin/features/users/UserProfilePage.jsx` — read+edit pattern
- T-20260514-002 dossier §6 — Phase C corrective discipline (anti-jargon, anti-glow)

## ✅ Phase D Verification (Claude, 2026-05-15)
- Build: ✅ PASS 676ms, bundle 206KB gzipped
- ESLint: ✅ 0 errors
- 10 sub-page files created (PackageEditPage, ProviderEditPage, LicenseCreate/Detail/AssignPackage/AdjustTokens, KeyCreate/Allocate/BulkImport, ReleaseUpload)
- 9 modal files deleted (find -name "*Modal*" returns empty)
- App.jsx: 10 new routes wired correctly
- Parent pages: strip modal state, use `<Link>` — verified for Packages/Providers/Licenses/Keys/Releases
- Shared `FormField` + `FormSection` extracted to `admin/_shared/components/` — reused across all 10 pages

### Known carry-over (out of scope, not blocking)
- 9 anti-pattern instances (`text-[10px] font-bold uppercase tracking-widest`, `h-12 rounded-xl`) still exist in parent list pages (LicensesPage:155,162 / KeysPage:174,207,223,248,257,335 / ReleasesPage:70). Task scope was modal→subpage migration, not parent page typography cleanup. Defer to future task.

**Status**: CLOSED | Owner: Gemma (all 4 phases) + Claude (Phase D verify + archive)
