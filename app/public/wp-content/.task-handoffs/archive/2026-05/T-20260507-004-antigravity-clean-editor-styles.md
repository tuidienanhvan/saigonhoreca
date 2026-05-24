---
id: T-20260507-004
owner: antigravity
state: returned
priority: P1
risk: low
estimated_minutes: 30
created: 2026-05-07
updated: 2026-05-07
---

# 🛡️ UNIFIED CLEAN CONTENT STYLES (NO EFFECTS)

Loại bỏ toàn bộ các hiệu ứng CSS trang trí (borders, backgrounds, shadows, opacity) trong hệ thống typography (class .prose) để đảm bảo sự đồng bộ tuyệt đối giữa Editor và Frontend. Chỉ hiển thị các thuộc tính nội dung thực tế (Font size, Bold, Color, Heading).

## User Review Required
> [!IMPORTANT]
> Việc tách biệt style Editor và Frontend sẽ đảm bảo người dùng không bị "đánh lừa" bởi các hiệu ứng trang trí không tồn tại trong data thực tế.

## Allowed Scope
- `wp-content/pi-dashboard-webapp/src/styles/Prose.css`
- `wp-content/pi-dashboard-webapp/src/components/editor/TipTapEditor.jsx`
- `wp-content/pi-dashboard-webapp/src/styles/Editor.css`

## Out Of Scope
- Thay đổi logic Tiptap extensions.
- Thay đổi UI Toolbar.

## Phases
1. **Audit**: Kiểm tra các class đang áp dụng hiệu ứng trong `Prose.css`. (Done)
2. **Implementation**:
    - Tạo class `.pi-editor-content` trong `Editor.css` hoặc `Prose.css`. (Done)
    - Định nghĩa lại style cho H1-H4, Blockquote, Table... mà không có hiệu ứng trang trí. (Done)
    - Cập nhật `TipTapEditor.jsx` để sử dụng class mới. (Done)
3. **Verification**: Kiểm tra hiển thị trong Editor đảm bảo sạch sẽ. (Done)

## Status Sync
- [x] Create dossier
- [x] Update STATUS.md
- [x] Clean up Editor styles
- [x] Verify
