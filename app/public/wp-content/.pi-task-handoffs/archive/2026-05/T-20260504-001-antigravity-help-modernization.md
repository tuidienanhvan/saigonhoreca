# 🛡️ DOSSIER: T-20260504-001 | FIX TYPOGRAPHY & CONTENT MODERNIZATION (HELP CENTER)

- **Agent**: Antigravity (Senior AI Engineer)
- **Scope**: `src/pages/system/HelpCenter.jsx`, `pi-api/includes/help-content/*.md`, `src/components/help/*`
- **Status**: ✅ COMPLETED
- **Objective**: Sửa lỗi chữ bị dính (typography spacing) và thay thế nội dung "vô nghĩa" bằng tài liệu hướng dẫn Pi Dashboard chuyên nghiệp.

---

## 🔍 PHÂN TÍCH HIỆN TRẠNG (DIAGNOSIS)
1. **Lỗi Typography**: Các đoạn văn (`<p>`, `<li>`) dùng line-height thấp, khiến chữ bị "dính" vào nhau.
2. **Lỗi Nội dung**: Dữ liệu help content cũ sơ sài, mang tính chất placeholder.
3. **Môi trường**: Backend đang build dở dang nên hệ thống dùng dữ liệu từ plugin `pi-api` trong LocalWP.

---

## 🛠️ KẾ HOẠCH THỰC THI (PLAN) - MAXIMUM EFFORT
1. **Giai đoạn 1: Content Expansion (Tối đa nội dung)**: 
   - Viết lại 10 file Markdown với chiều sâu kỹ thuật cao (Getting Started, SEO, Leads, AI Chatbot...).
   - Đảm bảo chuẩn UTF-8 Tiếng Việt có dấu.
2. **Giai đoạn 2: Typography Engineering (Sửa chữ bị dính)**:
   - Tinh chỉnh `prose` container trong `HelpCenter.jsx`.
   - Tăng `line-height` (leading-relaxed) và `margin-bottom` cho các thẻ p/h2/h3.
3. **Giai đoạn 3: Final Quality Gate**:
   - Kiểm tra hiển thị qua Browser subagent.

---

## 📝 NHẬT KÝ THỰC THI (EXECUTION LOG)
- [x] Tạo Dossier (Re-created in central wp-content location).
- [x] Đọc và Audit toàn bộ 10 file Markdown trong plugin `pi-api`.
- [x] Thực hiện Content Expansion (Viết lại 10 bài hướng dẫn chuyên sâu).
- [x] Fix typography (Cập nhật Tailwind classes trong HelpCenter.jsx).
- [x] Verify qua Browser (Kết quả: Hoàn hảo).
- [x] Đại tu nhan sắc WhatsNew (Glass Cards, Glow Markers, Premium Header).
- [x] Di chuyển Dossier về thư mục chuẩn `wp-content/.task-handoffs/`.

---
**Mantra**: *"Code in English, Think in Logic, Record in Dossier, Speak in Vietnamese."*
