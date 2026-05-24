# 🛡️ DOSSIER: T-20260505-007-antigravity-dashboard-theme-unification

- **ID**: T-20260505-007
- **Agent**: Antigravity (Infinity Edition)
- **Scope**: Toàn bộ `pi-dashboard-webapp/src/`, đặc biệt là `index.css` và các component sử dụng CSS variables.
- **Status**: IN_PROGRESS

## 🎯 OBJECTIVE
Thống nhất Design System giữa `pi-dashboard` và `pi-store` bằng cách:
1. **Loại bỏ hoàn toàn tiền tố `--pi-`**: Chuyển sang sử dụng các biến chuẩn (`--brand`, `--text-1`, `--surface`,...).
2. **Đồng bộ index.css**: Cập nhật `pi-dashboard-webapp/src/index.css` theo cấu trúc hiện đại của `pi-store`.
3. **Refactor Global**: Tìm và thay thế tất cả các tham chiếu `--pi-*` trong toàn bộ source code.

## 🏗️ EXECUTION LOG

### [2026-05-05 14:52] Khởi tạo Task
- Đã đọc và phân tích `pi-store-webapp/src/styles/index.css`.
- Lập danh mục ánh xạ (Mapping) từ `--pi-` sang biến chuẩn.

### [2026-05-05 14:53] Cập nhật index.css (Dashboard)
- Thay thế nội dung `index.css` của Dashboard bằng bộ Runtime Tokens mới.
- Giữ lại các utility đặc thù của Dashboard nếu cần, nhưng chuyển sang dùng biến mới.

### [2026-05-05 15:02] Global Replacement & Verify
- Chạy PowerShell script thay thế `--pi-` -> `--` trên toàn bộ 2458 modules.
- Cập nhật thành công `index.css` Dashboard theo chuẩn Token của `pi-store`.
- Chạy `npm run build`: Thành công rực rỡ (Exit code 0).

## 🧪 QUALITY GATES
- [x] Build Gate: `npm run build` (PASSED)
- [x] Integrity Gate: Loại bỏ hoàn toàn tiền tố `--pi-`.
- [x] Alignment Gate: Đồng bộ 100% tokens với `pi-store`.
