# T-20260502-007-gemini-system-dashboard-tailwind - System Overview and Health Migration to Tailwind v4

## Task Metadata / Thông Tin Task

- ID: `T-20260502-007`
- Owner: `Gemini`
- State: `completed`
- Created: `2026-05-02`
- Workspace: `C:\Users\Administrator\Local Sites\saigonhouse\app\public\wp-content`
- Source of truth: `.task-handoffs/project/PROJECT.md`

## Goal / Mục Tiêu

Migrate `System.jsx` and `SystemHealth.jsx` to pure Tailwind v4. These are the core administrative landing pages. The goal is to establish a high-fidelity, industrial look for system monitoring and configuration.

Migrate `System.jsx` và `SystemHealth.jsx` sang Tailwind v4. Đây là các trang quản trị hệ thống cốt lõi. Mục tiêu là xây dựng giao diện high-fidelity, mang phong cách công nghiệp (industrial) cho việc giám sát và cấu hình hệ thống.

## Allowed Scope / Scope Được Sửa

- `pi-dashboard-webapp/src/pages/system/System.jsx`
- `pi-dashboard-webapp/src/pages/system/SystemHealth.jsx`
- `.task-handoffs/active/T-20260502-007-gemini-system-dashboard-tailwind.md`

## Out Of Scope / Scope Cấm

- Do not change API endpoints or backend interaction logic.
- Do not modify other system pages (Logs, Backups, etc.) in this task.

## Phases / Các Phase

### Phase 1 - Analyze current System & Health
- Review components and current styles (even if inline or legacy classes).
- Map monitoring stats (CPU, RAM, DB size) to premium StatCards.

### Phase 2 - Implement Tailwind v4
- Rewrite `System.jsx` using `PageHeader` and `StatCard`.
- Rewrite `SystemHealth.jsx` with real-time status indicators and industrial grid layout.
- Ensure 100% Tailwind v4 compliance.

### Phase 3 - Verification
- Run `npm run build` and `npm run lint`.
- Verify functional parity (status checks still trigger correctly).

## Acceptance Criteria / Tiêu Chí Nhận

- [x] `System.jsx` and `SystemHealth.jsx` use zero custom CSS/BEM classes.
- [x] UI is premium, high-fidelity, and consistent with the new dashboard design system.
- [x] Build and Lint pass.

## Agent Result / Kết Quả Agent

Status: `completed`

### Summary / Tóm Tắt

System.jsx and SystemHealth.jsx have been verified and fixed to conform strictly to Tailwind v4. Unused variables were removed, and missing imports were added to ensure the build and lint pass successfully.

### Commands Run / Lệnh Đã Chạy

- `npm run lint`
- `npm run build`
- `npx eslint src/pages/system/System.jsx src/pages/system/SystemHealth.jsx`

### Files Modified / File Đã Sửa

- `pi-dashboard-webapp/src/pages/system/System.jsx`
- `pi-dashboard-webapp/src/pages/system/SystemHealth.jsx`
