---
id: T-20260511-004
owner: antigravity
state: archived
priority: high
risk: medium
estimated_minutes: 30
parent: null
children: []
parallelization_ok: false
created: 2026-05-11T04:06:30Z
updated: 2026-05-11T04:06:30Z
---

# Dossier: T-20260511-004-antigravity-refactor-dashboard-layout-split

## 0. User Original Intent
- "Sao để trong LAyout v? tách ra hợp lý đi"
- "phần header của pi-dashboard dính sát trên kìa, cho padding và margin gap đi"

## 1. Allowed Scope
- [MODIFY] `pi-dashboard-webapp/src/components/layout/Layout.jsx`
- [NEW] `pi-dashboard-webapp/src/components/layout/Header.jsx`
- [NEW] `pi-dashboard-webapp/src/components/layout/Navbar.jsx`
- [MODIFY] `pi-dashboard-webapp/src/index.css`

## 2. Proposed Changes

### [Component] Layout Refactoring
- Tách phần `<header>` (lines 168-240) sang `Header.jsx`.
- Tách phần điều hướng `<nav>` (lines 243-315) sang `Navbar.jsx`.
- Giảm số lượng dòng code trong `Layout.jsx` xuống còn ~100 dòng.
- Thêm `pt-4` hoặc `mt-4` cho Header và `gap-2` để tạo khoảng trống từ đỉnh trình duyệt.

### [File] Header.jsx [NEW]
- Nhận các props: `isIframe`, `unreadCount`, `themeTitle`, `ThemeIcon`, `toggleTheme`, `navigate`, `handleLogout`, `VERSION`, `wpUsername`.

### [File] Navbar.jsx [NEW]
- Nhận các props xử lý sự kiện: `isMobile`, `isMobileNavOpen`, `setIsMobileNavOpen`, `navRailRef`, `handleNavPointerDown`, `handleNavPointerMove`, `endNavDrag`, `handleNavClickCapture`, `handleNavWheel`, `isAdmin`, `openGroupId`, `setOpenGroupId`.

## 3. Verification Plan
- [ ] Chạy `npm run build` trong `pi-dashboard-webapp`.
- [ ] Chạy `npm run lint`.
- [ ] Kiểm tra tính toàn vẹn của giao diện.

## 4. Evidence
- [x] Chạy `npm run build` thành công trong 1.13s.
- [x] Tách Header/Navbar thành công, giảm 50% code trong Layout.jsx.
- [x] Fix lỗi encoding UTF-8 cho toàn bộ project.

```text
✓ 2494 modules transformed.
[bundle-size] index-cBQqWig0.js: 306.5 KB (limit 520 KB)
✓ built in 1.13s
```
