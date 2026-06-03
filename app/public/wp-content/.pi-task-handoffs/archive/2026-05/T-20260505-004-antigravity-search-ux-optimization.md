# 🛡️ Dossier: T-20260505-004-antigravity-search-ux-optimization

**Chủ sở hữu**: Antigravity (Senior AI Engineer)
**Ngày khởi tạo**: 2026-05-05
**Mục tiêu**: Tối ưu trải nghiệm tìm kiếm, loại bỏ hiện tượng giật skeleton khi gõ phím.

---

## 🔍 Chẩn đoán (Diagnosis)
- **Immediate Fetch**: `searchTerm` đang trigger re-fetch ngay lập tức trên mỗi sự kiện `onChange`.
- **Skeleton Trigger**: Logic `if (loading && !data)` trong `ContentList.jsx` kích hoạt skeleton vì React Query coi mỗi từ khóa mới là một Query Key mới (dẫn đến `data` bị null tạm thời).

---

## 🏗️ Kế hoạch thực thi (Implementation Plan)
1. **Step 1: Use Debounce Hook**: 
   - Tạo/Sử dụng cơ chế debounce cho `searchTerm`.
   - `inputSearch` (local UI state) -> 500ms -> `debouncedSearch` (API state).
2. **Step 2: Smooth Loading UI**:
   - Thay đổi logic render skeleton: Chỉ hiện skeleton ở lần load ĐẦU TIÊN (khi chưa có bất kỳ dữ liệu nào).
   - Khi đang tìm kiếm bài viết mới, giữ nguyên danh sách cũ và thêm hiệu ứng `opacity-50` để người dùng biết đang load.
3. **Step 3: Cleanup Search Input**: Đảm bảo input vẫn mượt mà, không bị delay cảm giác gõ (input lag).

---

## 🧪 Kiểm chứng (Verification)
- [ ] Gõ phím liên tục trong ô search không hiện skeleton.
- [ ] Sau khi ngừng gõ 500ms, danh sách bài viết tự động cập nhật.
- [ ] Trong lúc load, danh sách cũ mờ đi thay vì biến mất.
