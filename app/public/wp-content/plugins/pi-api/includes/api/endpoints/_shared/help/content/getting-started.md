---
title: Bắt đầu với Pi Dashboard
category: getting-started
order: 1
date: 2026-05-04
tags: onboarding, basics, setup
---

# Chào mừng bạn đến với Hệ sinh thái Pi Dashboard

**Pi Dashboard** không chỉ là một giao diện quản trị WordPress thông thường. Đây là một trung tâm điều hành tập trung (Command Center) được thiết kế tối ưu cho các Senior Developer và Agency, giúp bạn kiểm soát hoàn toàn hệ thống website WordPress với hiệu năng vượt trội và trí tuệ nhân tạo tích hợp sẵn.

---

## Triết lý Thiết kế
Chúng tôi xây dựng Pi Dashboard dựa trên ba trụ cột cốt lõi:
1. **Speed First**: Tối ưu hóa từng miligiây phản hồi. Giao diện Single Page Application (SPA) giúp bạn thao tác không cần chờ đợi load trang.
2. **AI-Driven**: Tích hợp sâu các mô hình ngôn ngữ lớn (LLM) vào quy trình viết bài, SEO và phân tích dữ liệu.
3. **Data Centralization**: Toàn bộ dữ liệu về Lead, Traffic, SEO Health đều được hiển thị trực quan ngay trên Dashboard.

---

## Bước 1: Thiết lập Kết nối (Application Password)
Để đảm bảo tính bảo mật tuyệt đối mà không cần chia sẻ mật khẩu chính, Pi Dashboard sử dụng cơ chế **WordPress Application Password**.

### Quy trình thực hiện:
1. Đăng nhập vào WordPress Admin truyền thống của bạn.
2. Truy cập **Users (Thành viên) → Profile (Hồ sơ)**.
3. Cuộn xuống phần **Application Passwords**.
4. Nhập tên định danh (ví dụ: `PiDashboard-Main`) và nhấn **Add New**.
5. Copy đoạn mã 24 ký tự hiện ra. Đây là mật khẩu duy nhất bạn cần để đăng nhập vào Pi Dashboard.

> [!IMPORTANT]
> Lưu giữ mật khẩu này ở nơi an toàn. Pi Dashboard sẽ không lưu trữ mật khẩu thuần (plain text) của bạn để bảo vệ tối đa quyền riêng tư.

---

## Bước 2: Làm quen với Giao diện Điều hành
Hệ thống được chia thành các Domain (Miền) nghiệp vụ rõ ràng:

### 1. Miền Nội Dung (Content Domain)
Nơi bạn quản trị Bài viết, Trang và Media với trình soạn thảo **TipTap PRO** thế hệ mới. Hỗ trợ Realtime Collaboration và AI Assistant hỗ trợ viết bài ngay trong editor.

### 2. Miền Marketing & SEO (Growth Domain)
Tích hợp các công cụ SEO Audit chuyên sâu, Quản lý Leads (Pipeline CRM) và các chiến dịch Email Marketing tự động hóa.

### 3. Miền Hệ Thống (System Domain)
Trung tâm kiểm soát sức khỏe server, quản lý Backup/Restore, và cấu hình các khóa API (OpenAI, Anthropic, Google Search Console).

---

## ⌨️ Bước 3: Làm chủ Tốc độ với Phím tắt
Pi Dashboard được thiết kế để bạn làm việc mà không cần rời tay khỏi bàn phím:

- `Cmd + K` (hoặc `Ctrl + K`): Mở **Command Palette**. Tìm kiếm nhanh bất kỳ bài viết, menu hay hành động nào.
- `Cmd + /`: Ẩn/Hiện Sidebar để tập trung vào nội dung.
- `Cmd + S`: Lưu nhanh bài viết đang soạn thảo.
- `Esc`: Đóng ngay lập tức các Modal hoặc Drawer.

---

## 💡 Chuyên gia khuyên dùng (Best Practices)
- **Cấu hình AI sớm**: Truy cập *Hệ thống → API Vault* để thiết lập OpenAI Key. Điều này sẽ kích hoạt tính năng tự động tạo Meta Description và gợi ý Outline bài viết.
- **Sử dụng Dashboard Widgets**: Tùy chỉnh các widget tại trang Tổng quan để theo dõi các chỉ số quan trọng nhất (ví dụ: Số Lead mới trong ngày).
- **Audit SEO định kỳ**: Sử dụng công cụ SEO Audit ít nhất 1 lần mỗi tuần để phát hiện sớm các lỗi kỹ thuật ảnh hưởng đến thứ hạng Google.

---

## 🔗 Tài liệu liên quan
- [Hướng dẫn Quản lý Bài viết & Trang](/help/article/posts-pages)
- [Cấu hình SEO Tổng quan](/help/article/seo-overview)
- [Quản lý Leads & Pipeline CRM](/help/article/leads-management)
