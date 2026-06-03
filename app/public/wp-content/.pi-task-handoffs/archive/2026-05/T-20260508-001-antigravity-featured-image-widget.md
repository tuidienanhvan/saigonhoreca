# 🛡️ PI ECOSYSTEM | TASK DOSSIER: T-20260508-001-antigravity-featured-image-widget

- **ID**: T-20260508-001
- **Owner**: Antigravity (Gemini)
- **State**: dispatched
- **Priority**: P1
- **Risk**: medium
- **Scope**: 
    - `pi-dashboard-webapp/src/pages/editor/PostEditor.jsx`
    - `pi-dashboard-webapp/src/components/editor/widgets/FeaturedImageWidget.jsx` [NEW]
    - `plugins/pi-api/includes/endpoints/post-controller.php`
- **Created**: 2026-05-08

## 🎯 Goal
Xây dựng Widget quản lý ảnh đại diện (Featured Image) cho bài viết trong Dashboard, đồng bộ với Backend (pi-api).

## 🚀 Phases

### 1. Audit (Khảo sát)
- [x] Xác định file UI bên phải trong Editor bài viết. (`EditorSidebar.jsx`)
- [x] Kiểm tra API update bài viết hiện tại trong `pi-api`. (`class-content.php`)
- [x] Kiểm tra Media Library component có sẵn để tái sử dụng không. (Đã tạo `MediaPickerModal.jsx`)

### 2. Implementation (Thực thi)
- [x] **Backend (pi-api)**: Thêm/Cập nhật logic xử lý `featured_media` (thumbnail_id) trong Post Controller.
- [x] **Frontend (Widget)**: Tạo `FeaturedImageWidget.jsx` (đã đặt tên là `ThumbnailWidget.jsx`) với giao diện premium.
- [x] **Frontend (Integration)**: Chèn widget vào Sidebar của Editor.

### 3. Verification (Kiểm chứng)
- [ ] Test hiển thị ảnh đại diện cũ.
- [ ] Test thay đổi ảnh qua Media Library.
- [ ] Verify database (wp_postmeta `_thumbnail_id`) sau khi lưu.

## 📁 Evidence (TBD)
(Sẽ dán Terminal log và screenshot kết quả tại đây)
