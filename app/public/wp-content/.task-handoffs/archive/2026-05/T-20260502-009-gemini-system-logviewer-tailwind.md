# T-20260502-009-gemini-system-logviewer-tailwind - LogViewer, Webhooks, ApiKeyVault Migration

## Task Metadata / Thông Tin Task

- ID: `T-20260502-009`
- Owner: `Gemini`
- State: `active`
- Created: `2026-05-02`
- Workspace: `C:\Users\Administrator\Local Sites\saigonhouse\app\public\wp-content`
- Source of truth: `.task-handoffs/project/PROJECT.md`

## Goal / Mục Tiêu

Migrate `LogViewer.jsx`, `Webhooks.jsx`, and `ApiKeyVault.jsx` to pure Tailwind v4, deleting their legacy CSS files. These pages still import `.css` files and use BEM/legacy classes.

## Allowed Scope / Scope Được Sửa

- `pi-dashboard-webapp/src/pages/system/LogViewer.jsx`
- `pi-dashboard-webapp/src/pages/system/LogViewer.css`
- `pi-dashboard-webapp/src/pages/system/Webhooks.jsx`
- `pi-dashboard-webapp/src/pages/system/Webhooks.css`
- `pi-dashboard-webapp/src/pages/system/ApiKeyVault.jsx`
- `pi-dashboard-webapp/src/pages/system/ApiKeyVault.css`
- `.task-handoffs/active/T-20260502-009-gemini-system-logviewer-tailwind.md`

## Out Of Scope / Scope Cấm

- Do not change backend API routes or logic.
- Do not modify other system pages.

## Phases / Các Phase

1. Audit each JSX to understand CSS class usage.
2. Rewrite JSX inline with Tailwind v4 classes.
3. Delete the corresponding `.css` files.
4. Run `npx eslint` on the 3 files.
5. Run `npm run build`.
6. Update Agent Result.

## Acceptance Criteria / Tiêu Chí Nhận

- [x] `LogViewer.jsx`, `Webhooks.jsx`, `ApiKeyVault.jsx` import zero `.css` files.
- [x] All BEM/legacy classes replaced with Tailwind v4 equivalents.
- [x] Build and Lint pass.
- [x] Legacy CSS files deleted.

## Agent Result / Kết Quả Agent

Status: `completed`

### Summary / Tóm Tắt

Batch 2 migration completed. All target files are now using pure Tailwind v4. Legacy CSS files have been removed. Build is green.

### Files Modified / File Đã Sửa

- `src/pages/system/LogViewer.jsx`
- `src/components/log-viewer/LogRow.jsx`
- `src/components/log-viewer/LogFilters.jsx`
- `src/components/log-viewer/LogDetailPanel.jsx`
- `src/pages/system/Webhooks.jsx`
- `src/pages/system/ApiKeyVault.jsx`
- `src/components/api-keys/ServiceIcon.jsx`
- `src/components/api-keys/RotationReminder.jsx`
- `src/components/api-keys/KeyTester.jsx`
- `src/components/api-keys/KeyRow.jsx`
- `src/components/api-keys/AddKeyModal.jsx`

### Verification / Kiểm Tra

- ESLint: Pass (with 1 minor pre-existing warning in AddKeyModal)
- Build: Pass (863ms)
