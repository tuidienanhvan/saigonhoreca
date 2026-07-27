---
title: Tài liệu API (REST)
category: developer
order: 9
date: 2026-05-04
tags: api, developer, integration, rest
---

# Tích hợp Hệ thống qua Pi REST API

Pi Dashboard cung cấp một hệ thống API mạnh mẽ, cho phép các nhà phát triển bên thứ ba có thể tương tác, trích xuất dữ liệu và điều khiển các tính năng của website một cách lập trình.

---

## 🔐 Xác thực (Authentication)
Mọi yêu cầu API đều phải được xác thực thông qua **JWT (JSON Web Token)** hoặc **Application Password**.

### Base URL
`https://yourdomain.com/wp-json/pi/v1`

### Header mẫu:
```http
Authorization: Bearer <YOUR_JWT_TOKEN>
Content-Type: application/json
X-Pi-Site: your-site-id
```

---

## 📡 Các Endpoints quan trọng

### 1. Quản lý Leads
- `GET /leads`: Lấy danh sách lead với các bộ lọc (status, date, score).
- `POST /leads`: Tạo lead mới từ hệ thống bên ngoài.
- `PATCH /leads/:id`: Cập nhật trạng thái hoặc thông tin lead.

### 2. SEO & Audit
- `GET /seo/audit`: Lấy kết quả audit SEO gần nhất.
- `POST /seo/audit/run`: Kích hoạt một tiến trình quét SEO mới.
- `GET /seo/schema`: Lấy danh sách các schema đang hoạt động.

### 3. Hệ thống & Health
- `GET /system/health`: Kiểm tra trạng thái server (CPU, RAM, Disk, DB).
- `GET /system/logs`: Trích xuất nhật ký lỗi.

---

## 🛠️ Ví dụ tích hợp (JavaScript/Node.js)

```javascript
const fetchLeads = async () => {
  const response = await fetch('https://api.pitechco.com/wp-json/pi/v1/leads', {
    headers: {
      'Authorization': 'Bearer YOUR_TOKEN',
      'X-Pi-Site': 'saigonhouse.vn'
    }
  });
  const data = await response.json();
  console.log(data.items);
};
```

---

## ⚠️ Giới hạn Tốc độ (Rate Limiting)
Để bảo vệ hiệu năng server, chúng tôi áp dụng giới hạn:
- **60 requests / phút** cho mỗi IP/Token.
- Vượt quá giới hạn sẽ nhận lỗi **429 Too Many Requests**.

---

## 🛡️ Webhooks (Push Notification)
Thay vì liên tục gọi API (Polling), bạn nên sử dụng Webhooks để nhận thông báo tức thời khi:
- Có Lead mới (`lead.created`)
- Đơn hàng mới (`order.created`)
- Backup hoàn tất (`backup.success`)

Cấu hình URL nhận Webhook tại: **Hệ thống → Webhooks**.

---

## 🔗 Tài liệu liên quan
- [Cấu hình Webhooks](/help/article/webhooks)
- [Bảo mật API Vault](/help/article/api-vault)
- [Tất cả bài viết](/help)
