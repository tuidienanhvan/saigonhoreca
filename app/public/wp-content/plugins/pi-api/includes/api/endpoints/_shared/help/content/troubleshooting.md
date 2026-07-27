---
title: Khắc phục Sự cố
category: system
order: 8
date: 2026-05-04
tags: troubleshooting, debug, errors, logs, fix
---

# Trung tâm Xử lý & Khắc phục Sự cố

Dù Pi Dashboard được thiết kế để hoạt động ổn định nhất có thể, đôi khi các yếu tố ngoại vi (server lag, conflict plugin bên thứ 3) có thể gây ra lỗi. Tài liệu này giúp bạn tự chẩn đoán và xử lý nhanh các tình huống thường gặp.

---

## 🚨 Các lỗi Đăng nhập & Xác thực

### 1. Thông báo: "Phiên đã hết hạn" hoặc "Unauthorized"
- **Nguyên nhân**: Token JWT bị lỗi hoặc Application Password trong WordPress đã bị thu hồi.
- **Giải pháp**:
    1. Kiểm tra lại Application Password trong **Users → Profile**.
    2. Nếu đang dùng chế độ Iframe, hãy thử Logout và Login lại từ Portal chính.
    3. Đảm bảo thời gian (Time) trên máy tính của bạn trùng khớp với thời gian thực tế (sai lệch quá 5 phút sẽ lỗi JWT).

### 2. Lỗi "CORS Policy"
- **Giải pháp**: Truy cập mục **Hệ thống → Security**, thêm domain của bạn vào danh sách *Allowed Origins*.

---

## 🛠️ Lỗi liên quan đến Nội dung (Editor)

### 1. Editor không load hoặc bị trắng trang
- **Nguyên nhân**: Thường do conflict với các plugin tối ưu hóa JS (như Rocket, Autoptimize).
- **Giải pháp**: Loại trừ (Exclude) các script của Pi Dashboard khỏi quá trình nén JS hoặc thử tắt tạm các plugin cache để kiểm tra.

### 2. Không upload được ảnh
- **Kiểm tra**:
    - Dung lượng file có vượt quá `upload_max_filesize` của PHP không?
    - Thư mục `wp-content/uploads` có quyền ghi (Permission 755) không?
    - Server có còn trống dung lượng đĩa không?

---

## 🤖 Sự cố AI & Chatbot

### 1. AI trả lời: "I cannot fulfill this request" hoặc báo lỗi 429
- **Nguyên nhân**: Hết hạn mức (Quota) của API Key hoặc ví (Wallet) tại Pi Ecosystem bị cạn kiệt.
- **Giải pháp**: Kiểm tra số dư tại *Hệ thống → API Vault* hoặc nạp thêm token.

### 2. Chatbot trả lời sai kiến thức doanh nghiệp
- **Giải pháp**: Kiểm tra lại mục **Knowledge Base**. Nhấn **Re-sync** để đảm bảo AI đã cập nhật dữ liệu mới nhất từ website.

---

## 📊 Công cụ Chẩn đoán Nâng cao (Dành cho Dev)

### 1. Xem nhật ký lỗi (Logs Viewer)
Truy cập **Hệ thống → Log Viewer** để xem:
- **PHP Error Logs**: Các lỗi phát sinh từ phía WordPress.
- **API Logs**: Lịch sử các request lỗi giữa Dashboard và Server.
- **Audit Log**: Ai đã làm gì và làm lỗi ở đâu.

### 2. Chế độ Debug
Nếu lỗi vẫn chưa rõ ràng, hãy bật `WP_DEBUG` trong file `wp-config.php` để xem thông báo lỗi chi tiết thay vì lỗi 500 chung chung.

---

## 🆘 Yêu cầu Hỗ trợ Kỹ thuật
Nếu các bước trên không giải quyết được vấn đề, vui lòng:
1. Chụp ảnh màn hình lỗi (kèm cả tab Network trong Inspect Element nếu có thể).
2. Mô tả các bước dẫn đến lỗi.
3. Mở ticket tại [pi.tech/support](https://pi.tech/support) hoặc liên hệ qua kênh Telegram hỗ trợ riêng cho Agency.

---

## 🔗 Tài liệu liên quan
- [Sao lưu & Khôi phục dữ liệu](/help/article/backup-restore)
- [Cấu hình Hệ thống](/help/article/system-settings)
- [Tất cả bài viết](/help)
