---
id: T-20260508-002
owner: antigravity
state: archived
priority: P1
risk: high
estimated_minutes: 60
created: 2026-05-08T16:22:00+07:00
updated: 2026-05-08T16:22:00+07:00
---

# T-20260508-002: Pi Store Admin Premium Refactor

## 0. User Original Intent
> "lên plan refactor toàn diện page admin đi ... quá xấu, nhớ dùng các biến màu theme [pi-store-webapp/src/styles/index.css], KO HARDCODE MÀU SẮC, có thể tạo components, css bằng tên compoent/page.css. lưu ý, là trong pi-store nha, ko làm trong pi-dashboard, pi-store-admin"

## Scope
- `pi-store-webapp/src/components/core/DashboardLayout.jsx`
- `pi-store-webapp/src/components/core/DashboardLayout.css`
- `pi-store-webapp/src/pages/core/AdminOverviewPage.jsx`
- `pi-store-webapp/src/pages/core/AdminOverviewPage.css`
- `pi-store-webapp/src/pages/license/AdminLicensesPage.jsx`
- `pi-store-webapp/src/pages/license/AdminLicensesPage.css`

## Checklist
- `[ ]` Research and identify all hardcoded colors in Admin pages.
- `[ ]` Refactor `DashboardLayout` for better glassmorphism.
- `[ ]` Redesign `AdminOverviewPage` KPI cards and layout.
- `[ ]` Redesign `AdminLicensesPage` filters and table.
- `[ ]` Ensure full compliance with `index.css` theme variables.
- `[ ]` Verify transitions and responsive states.

## Evidence
(To be filled during execution)
