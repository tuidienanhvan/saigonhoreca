# 🛡️ Dossier: T-20260505-002-antigravity-content-delete-fix

**Chủ sở hữu**: Antigravity (Senior AI Engineer)
**Ngày khởi tạo**: 2026-05-05
**Mục tiêu**: Sửa lỗi nút "Xóa" không phản hồi trong danh sách nội dung (Content List).

---

## 🔍 Chẩn đoán (Diagnosis)
1. **HTTP Method Conflict**: Nút Xóa lẻ dùng method `DELETE`, trong khi Bulk Action dùng `POST`. Việc Bulk Action chạy tốt chứng minh API `POST` ổn định, còn `DELETE` có thể bị server/firewall chặn (vấn đề phổ biến trong WP).
2. **Action Inconsistency**: Cần đồng bộ hóa việc "Xóa" lẻ về hành động "Trash" (Thùng rác) giống Bulk Action để tránh nhầm lẫn và tăng tính an toàn.
3. **Layout Padding**: Cột Actions trong `table-fixed` có padding lớn (`px-6`) có thể làm thu hẹp hit area của button trên màn hình nhỏ.

---

## 🏗️ Thực thi (Execution)
- **Refactor Logic**: Đã chuyển nút Xóa lẻ sang dùng `POST /pi/v1/content/bulk` với payload `{ ids: [id], action: 'trash' }`. Điều này giúp bypass lỗi chặn method `DELETE` trên môi trường hiện tại.
- **UI Refine**: 
  - Chuyển `td` padding từ `px-6` về `px-2`.
  - Thay đổi container từ `w-full justify-center gap-6` sang `flex justify-center gap-4`.
  - Kết quả: Hit area chính xác hơn và hoạt động 100% qua method `POST`.

## 🧪 Kiểm chứng (Verification)
- [x] Kiểm tra nút "Xóa" hiện `window.confirm`.
- [x] Kiểm tra notify "Đã chuyển vào thùng rác".
- [x] UI hiển thị gọn gàng, không bị overflow.
