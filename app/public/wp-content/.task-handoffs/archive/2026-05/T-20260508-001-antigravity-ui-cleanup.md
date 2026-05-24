# 🛡️ PI ECOSYSTEM | DOSSIER T-20260508-001

## 📋 Metadata
- **ID**: T-20260508-001
- **Owner**: Antigravity
- **Scope**: `pi-store-webapp/src/styles/index.css`, `pi-store-webapp/src/components/layout/SiteHeader.css`, `pi-store-webapp/src/components/layout/SiteHeader.jsx`
- **Priority**: P0
- **Risk**: Medium (UI impact)
- **Status**: Dispatched

## 🎯 Goal
Đồng bộ hóa hệ thống CSS của Pi Store theo chuẩn Pi Dashboard. Fix lỗi hiển thị Header bị lệch và không đồng nhất.

## 🏗️ Phases

### 📂 Phase 1: Theme Synchronization (index.css)
- [x] Đọc mẫu từ Dashboard.
- [x] Refactor `index.css` của Store chỉ còn Theme Tokens & Global Base.
- [x] Giữ lại các utility chuẩn như `glass`, `bg-grid`.

### 🏗️ Phase 2: Header UI Refactoring
- [x] Chuyển các style của Header từ `index.css` vào `SiteHeader.css`.
- [x] Fix lỗi layout Header (Flex alignment, height, spacing).
- [x] Đảm bảo hiệu ứng Glassmorphism hoạt động chuẩn.

### 🧪 Phase 3: Quality Gate
- [x] `npm run build` pass (built in 857ms).
- [x] `npm run lint` pass.
- [x] Verify giao diện bằng browser.

## 📝 Evidence
- **Build Success**: `vite build` completed successfully.
- **Dossier Finalized**: All UI/UX requirements met.

