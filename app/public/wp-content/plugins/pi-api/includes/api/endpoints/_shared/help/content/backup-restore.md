---
title: Sao lưu & Khôi phục
category: system
order: 7
date: 2026-05-04
tags: backup, restore, security, database, storage
---

# Bảo mật Dữ liệu & Quản trị rủi ro

Dữ liệu là tài sản quý giá nhất của website. Pi Dashboard cung cấp giải pháp sao lưu đa tầng, giúp bạn yên tâm tuyệt đối trước các sự cố về server, lỗi cập nhật plugin hoặc các cuộc tấn công mạng.

---

## 🛡️ Chiến lược Sao lưu Đa tầng

### 1. Sao lưu Tự động (Scheduled Backups)
Hệ thống tự động thực hiện sao lưu toàn bộ mã nguồn và database theo lịch trình:
- **Hàng ngày (Daily)**: Lưu giữ 7 bản gần nhất.
- **Hàng tuần (Weekly)**: Lưu giữ 4 bản gần nhất.
- **Hàng tháng (Monthly)**: Lưu giữ 3 bản gần nhất.

### 2. Sao lưu Tức thời (Instant Snapshot)
Trước khi thực hiện một thay đổi lớn (như cập nhật Core WordPress hoặc cài đặt plugin mới), hãy sử dụng tính năng **Snapshot** để tạo điểm phục hồi ngay lập tức.

---

## ☁️ Lưu trữ Đám mây (Offsite Storage)
Đừng bao giờ để bản sao lưu trên cùng một server với website chính. Pi Dashboard hỗ trợ đẩy dữ liệu lên các nền tảng đám mây an toàn:
- **Google Drive / Dropbox**: Phù hợp cho cá nhân và website nhỏ.
- **Amazon S3 / DigitalOcean Spaces**: Chuẩn doanh nghiệp với độ bền dữ liệu 99.999999999%.
- **FTP/SFTP**: Dành cho các server lưu trữ riêng biệt.

---

## 🔄 Quy trình Khôi phục (Recovery)

### Khôi phục từng phần (Granular Restore)
Bạn không cần phải khôi phục lại toàn bộ website nếu chỉ làm hỏng một phần dữ liệu:
- **Chỉ Database**: Khôi phục lại các bài viết và cài đặt.
- **Chỉ Media**: Lấy lại các hình ảnh đã bị xóa nhầm.
- **Chỉ Plugin/Theme**: Quay lại phiên bản code trước khi bị lỗi.

### Khôi phục thảm họa (Full Disaster Recovery)
Trong trường hợp server bị sập hoàn toàn, bạn có thể cài đặt một bản WordPress mới, kết nối Pi Dashboard và thực hiện **Full Restore** để đưa website trở lại trạng thái hoạt động chỉ trong vài phút.

---

## 🔐 Bảo mật bản sao lưu
- **Mã hóa (Encryption)**: Toàn bộ bản sao lưu được mã hóa bằng chuẩn AES-256 trước khi gửi đi.
- **Checksum Verification**: Tự động kiểm tra tính toàn vẹn của file sau khi upload để đảm bảo bản sao lưu không bị lỗi.

---

## 💡 Lời khuyên từ Chuyên gia bảo mật
- **Kiểm tra định kỳ**: Đừng chỉ sao lưu rồi để đó. Hãy thử khôi phục lên một site test (Staging) 1 lần mỗi tháng để đảm bảo quy trình luôn sẵn sàng.
- **Nguyên tắc 3-2-1**: Giữ ít nhất 3 bản sao, trên 2 loại phương tiện lưu trữ khác nhau, và có 1 bản nằm ở vị trí địa lý khác (Offsite).

---

## 🔗 Tài liệu liên quan
- [Quản lý Hệ thống & Hiệu suất](/help/article/system-settings)
- [Khắc phục sự cố Server](/help/article/troubleshooting)
- [Tất cả bài viết](/help)
