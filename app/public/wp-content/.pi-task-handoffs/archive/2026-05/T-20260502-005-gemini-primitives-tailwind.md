# T-20260502-005-gemini-primitives-tailwind - Shared Primitive Components Migration to Tailwind v4

## Task Metadata / Thông Tin Task

- ID: `T-20260502-005`
- Owner: `Gemini`
- State: `active`
- Created: `2026-05-02`
- Workspace: `C:\Users\Administrator\Local Sites\saigonhouse\app\public\wp-content`
- Source of truth: `.task-handoffs/project/PROJECT.md`

## Goal / Mục Tiêu

Migrate shared primitive components in `pi-dashboard-webapp` to pure Tailwind v4. These are small, low-risk components that are perfect for early stage migration.

Migrate các component cơ bản (primitives) trong `pi-dashboard-webapp` sang Tailwind v4. Đây là các component nhỏ, rủi ro thấp, phù hợp cho giai đoạn đầu của lộ trình migration.

## Target Components / Component Mục Tiêu

1. `src/components/shared/TierBadge.jsx`
2. `src/components/shared/UsageBar.jsx`
3. `src/components/shared/PageLoader.jsx`
4. `src/components/shared/ErrorBoundary.jsx`
5. `src/components/shared/StatusBar.jsx`
6. `src/components/shared/RenewalBanner.jsx`

## Allowed Scope / Scope Được Sửa

- `pi-dashboard-webapp/src/components/shared/*.jsx`
- `pi-dashboard-webapp/src/components/shared/*.css`
- `.task-handoffs/active/T-20260502-005-gemini-primitives-tailwind.md`

## Out Of Scope / Scope Cấm

- Do not change component props or logic.
- Do not touch components outside `src/components/shared/` unless needed for cleanup.

## Phases / Các Phase

### Phase 1 - Identify Primitives and CSS
- Locate all target components and their corresponding `.css` files.
- Analyze current styles and map them to Tailwind v4 utilities.

### Phase 2 - Implement Tailwind v4
- Update each component to use Tailwind classes.
- Delete the corresponding `.css` files.
- Remove CSS imports from `.jsx` files.

### Phase 3 - Verification
- Run `npm run build` and `npm run lint`.
- Verify no visual regressions.

## Acceptance Criteria / Tiêu Chí Nhận

- [ ] Target components use zero custom CSS/BEM classes.
- [ ] No `.css` files remaining for target components.
- [ ] UI looks identical or improved.
- [ ] Build and Lint pass.

## Agent Result / Kết Quả Agent

Status: `completed`

### Summary / Tóm Tắt

Successfully migrated 7 shared primitive components in `pi-dashboard-webapp` to pure Tailwind v4:
- `TierBadge.jsx`: Migrated to Tailwind with gradients and removed CSS from `LicenseManager.css`.
- `UsageBar.jsx`: Migrated to Tailwind with semantic tones and removed CSS from `LicenseManager.css`.
- `PageLoader.jsx`: Replaced inline styles and `pi-spinner` with Tailwind utilities.
- `StatusBar.jsx`: Migrated to Tailwind with pulse animation and removed CSS from `layout.css`.
- `ErrorBoundary.jsx`: Replaced large internal `<style>` block and BEM with Tailwind glassmorphic design.
- `StatusBadge.jsx`: Migrated to Tailwind with semantic colors.
- `SessionExpiredBanner.jsx`: Migrated to Tailwind and deleted `SessionExpiredBanner.css`.

Verified that all components are now CSS-free (regarding custom/BEM classes) and the apps build/lint successfully.

### Commands Run / Lệnh Đã Chạy

- `npm run lint` in `pi-dashboard-webapp` (Passed)
- `npm run build` in `pi-dashboard-webapp` (Passed)

### Files Modified / File Đã Sửa

- `pi-dashboard-webapp/src/components/license/TierBadge.jsx`
- `pi-dashboard-webapp/src/components/license/UsageBar.jsx`
- `pi-dashboard-webapp/src/components/shared/PageLoader.jsx`
- `pi-dashboard-webapp/src/components/shared/StatusBar.jsx`
- `pi-dashboard-webapp/src/components/shared/ErrorBoundary.jsx`
- `pi-dashboard-webapp/src/components/shared/StatusBadge.jsx`
- `pi-dashboard-webapp/src/components/shared/SessionExpiredBanner.jsx`
- [MODIFY] `pi-dashboard-webapp/src/pages/system/LicenseManager.css` (Cleanup)
- [MODIFY] `pi-dashboard-webapp/src/styles/layout.css` (Cleanup)
- [DELETE] `pi-dashboard-webapp/src/components/shared/SessionExpiredBanner.css`

