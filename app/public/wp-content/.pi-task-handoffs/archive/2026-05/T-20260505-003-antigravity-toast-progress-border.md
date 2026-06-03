# 🛡️ Dossier: T-20260505-003-antigravity-toast-progress-border

**Chủ sở hữu**: Antigravity (Senior AI Engineer)
**Ngày khởi tạo**: 2026-05-05
**Mục tiêu**: Nâng cấp UI thông báo (Toaster) với hiệu ứng thanh tiến độ chạy bao quanh viền (Border Progress).

---

## 🔍 Chẩn đoán (Diagnosis)
- Hiện tại `Toaster.jsx` đang dùng một `div` đơn giản ở bottom làm progress bar.
- Hiệu ứng bao quanh viền yêu cầu một kỹ thuật xử lý `stroke` chính xác trên hình chữ nhật bo góc (rounded rectangle).

---

## 🏗️ Kế hoạch thực thi (Implementation Plan)
1. **Step 1: CSS Animation**: 
   - Thêm keyframe `toastBorderProgress` vào `index.css` để xử lý việc giảm `stroke-dashoffset`.
   - Khai báo biến `--toast-duration` đồng bộ.
2. **Step 2: Update Toaster.jsx**:
   - Chèn một SVG tuyệt đối bao phủ `ToastItem`.
   - Sử dụng `pathLength="100"` trên thẻ `rect` để dễ dàng tính toán phần trăm progress (0-100).
   - Loại bỏ thanh progress bottom cũ.

---

## 🧪 Kiểm chứng (Verification)
- [x] Thông báo hiện ra có viền chạy vòng quanh.
- [x] Viền chạy hết 1 vòng đúng 5s (hoặc thời gian đã set).
- [x] Viền đổi màu theo trạng thái (Success, Error, Info, Warning).
