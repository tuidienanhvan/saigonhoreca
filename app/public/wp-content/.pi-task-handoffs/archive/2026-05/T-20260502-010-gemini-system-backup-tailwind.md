# T-20260502-010-gemini-system-backup-tailwind - Backup & Restore Migration

## Task Metadata / Thông Tin Task

- ID: `T-20260502-010`
- Owner: `Gemini`
- State: `active`
- Created: `2026-05-02`
- Workspace: `C:\Users\Administrator\Local Sites\saigonhouse\app\public\wp-content`
- Source of truth: `.task-handoffs/project/PROJECT.md`

## Goal / Mục Tiêu

Migrate `BackupRestore.jsx` and its sub-components to pure Tailwind v4, deleting `BackupRestore.css`. This is the final major system module with legacy CSS.

## Allowed Scope / Scope Được Sửa

- `pi-dashboard-webapp/src/pages/system/BackupRestore.jsx`
- `pi-dashboard-webapp/src/pages/system/BackupRestore.css`
- `pi-dashboard-webapp/src/components/backup/*.jsx`
- `.task-handoffs/active/T-20260502-010-gemini-system-backup-tailwind.md`

## Phases / Các Phase

1. Audit `BackupRestore.jsx` and sub-components.
2. Rewrite JSX with Tailwind v4.
3. Delete `BackupRestore.css`.
4. Run ESLint and Build.
5. Update Agent Result.

## Acceptance Criteria / Tiêu Chí Nhận

- [x] `BackupRestore.jsx` imports zero `.css` files.
- [x] All `pi-bk` classes replaced with Tailwind v4.
- [x] Build and Lint pass.
- [x] `BackupRestore.css` deleted.

## Agent Result / Kết Quả Agent

Status: `completed`

### Summary / Tóm Tắt

Batch 3 migration completed. `BackupRestore` and its sub-components are now using pure Tailwind v4. Legacy CSS files have been removed. Build is green.

### Files Modified / File Đã Sửa

- `src/pages/system/BackupRestore.jsx`
- `src/components/backup/BackupCard.jsx`
- `src/components/backup/ScheduleCard.jsx`
- `src/components/backup/CreateBackupModal.jsx`
- `src/components/backup/RestoreModal.jsx`
- `src/components/backup/BackupTargetConfig.jsx`

### Verification / Kiểm Tra

- ESLint: Pass
- Build: Pass (822ms)
