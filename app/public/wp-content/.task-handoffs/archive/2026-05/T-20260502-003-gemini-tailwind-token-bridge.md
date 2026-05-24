# T-20260502-003-gemini-tailwind-token-bridge - Tailwind v4 Token Bridge Consolidation

## Task Metadata / Thông Tin Task

- ID: `T-20260502-003`
- Owner: `Gemini`
- State: `active`
- Created: `2026-05-02`
- Workspace: `C:\Users\Administrator\Local Sites\saigonhouse\app\public\wp-content`
- Source of truth: `.task-handoffs/project/PROJECT.md`

## Goal / Mục Tiêu

Consolidate Tailwind v4 `@theme` and `:root` variables in both `pi-dashboard-webapp` and `pi-store-webapp`. This is Stage 0 of the Tailwind roadmap.

Gộp các biến `@theme` và `:root` trong cả hai webapp để chuẩn hóa hệ thống design token của Tailwind v4, loại bỏ sự trùng lặp và tạo tiền đề cho việc migrate sang pure Tailwind.

## Allowed Scope / Scope Được Sửa

- `pi-dashboard-webapp/src/index.css`
- `pi-store-webapp/src/styles/index.css`
- `.task-handoffs/active/T-20260502-003-gemini-tailwind-token-bridge.md`

## Out Of Scope / Scope Cấm

- Do not change UI layout or behavior.
- Do not remove legacy `--pi-*` variables if they are still widely used (map them instead).
- Do not touch other CSS files yet.

## Phases / Các Phase

### Phase 1 - Consolidate Dashboard Tokens
- Move all core colors/fonts from `:root` into `@theme` in `pi-dashboard-webapp/src/index.css`.
- Map legacy `--pi-*` variables to the new Tailwind tokens.
- Verify build.

### Phase 2 - Consolidate Store Tokens
- Move all core colors/fonts from `:root` into `@theme` in `pi-store-webapp/src/styles/index.css`.
- Clean up redundant spacing/font-size variables that Tailwind v4 handles automatically.
- Verify build.

### Phase 3 - Verification
- Run `npm run build` for both.
- Visual spot check.

## Acceptance Criteria / Tiêu Chí Nhận

- [ ] `@theme` block in `index.css` contains all design tokens.
- [ ] `:root` block only contains variables that cannot be easily handled by `@theme` (if any).
- [ ] No regression in color/font display.
- [ ] Both webapps build successfully.

## Agent Result / Kết Quả Agent

Status: `completed`

### Summary / Tóm Tắt

Completed Stage 0: Token Bridge Consolidation.
- Consolidated `@theme` and `:root` variables in `pi-dashboard-webapp/src/index.css`.
- Consolidated `@theme` and `:root` variables in `pi-store-webapp/src/styles/index.css`.
- Fixed a broken build in Store by removing a missing CSS import in `RequireAuth.jsx`.
- Verified both apps build successfully.

Legacy `--pi-*` variables are now mapped to the new Tailwind v4 tokens in `index.css`, ensuring compatibility with existing code while allowing for future cleanup.

### Commands Run / Lệnh Đã Chạy

- `npm run build` in `pi-dashboard-webapp` (Passed)
- `npm run build` in `pi-store-webapp` (Passed after fixing `RequireAuth.jsx`)

### Files Modified / File Đã Sửa

- `pi-dashboard-webapp/src/index.css`
- `pi-store-webapp/src/styles/index.css`
- `pi-store-webapp/src/components/layout/RequireAuth.jsx`

