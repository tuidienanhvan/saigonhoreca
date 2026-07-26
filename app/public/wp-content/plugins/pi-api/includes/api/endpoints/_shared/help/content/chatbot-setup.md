---
title: Cấu hình Chatbot AI
category: intelligence
order: 6
date: 2026-05-04
tags: ai, chatbot, intelligence, knowledge-base, customer-service
---

# Triển khai Trợ lý ảo AI thông minh

Pi Chatbot không phải là một chatbot kịch bản thông thường. Đây là một trợ lý ảo dựa trên mô hình ngôn ngữ lớn (LLM), có khả năng học hiểu toàn bộ nội dung website của bạn để hỗ trợ khách hàng 24/7 với độ chính xác kinh ngạc.

---

## 🧠 Cơ chế hoạt động: Knowledge Base (RAG)
Sức mạnh của Pi Chatbot nằm ở công nghệ **Retrieval-Augmented Generation (RAG)**:
- **Học từ Website**: Chatbot tự động quét các bài viết, trang và sản phẩm trên site của bạn.
- **Tài liệu bổ sung**: Bạn có thể upload thêm các file PDF, Docx hoặc dán văn bản hướng dẫn riêng tư để huấn luyện AI.
- **Phản hồi chính xác**: Khi khách hỏi, AI sẽ tìm kiếm trong "kho kiến thức" này trước khi đưa ra câu trả lời, đảm bảo không trả lời sai lệch thông tin doanh nghiệp.

---

## ⚙️ Quy trình thiết lập 3 bước

### Bước 1: Kết nối "Não bộ" (AI Model)
Truy cập **Intelligence → Chatbot Settings** để chọn mô hình:
- **GPT-4o (OpenAI)**: Cực kỳ thông minh, xử lý logic tốt.
- **Claude 3.5 Sonnet (Anthropic)**: Văn phong tự nhiên, thấu cảm cao.
- **Gemini 1.5 Pro (Google)**: Tốc độ nhanh, xử lý được lượng tài liệu khổng lồ.

### Bước 2: Huấn luyện Kiến thức
Vào mục **Knowledge Base**:
1. Nhấn **Sync Website Content** để AI đọc toàn bộ bài viết hiện có.
2. Upload các file quy định bảo hành, bảng giá hoặc FAQ nội bộ.
3. Nhấn **Process Embeddings** để chuyển đổi dữ liệu sang dạng mà AI hiểu được.

### Bước 3: Tùy chỉnh "Tính cách" (System Prompt)
Định nghĩa cách mà Chatbot giao tiếp với khách hàng:
- **Vai trò**: "Bạn là chuyên gia tư vấn bất động sản tại SaigonHouse..."
- **Tông giọng**: "Lễ phép, chuyên nghiệp, sử dụng đại từ 'em' và 'anh/chị'..."
- **Giới hạn**: "Chỉ trả lời về các dự án có trên web, không bàn luận về chính trị..."

---

## 🎨 Tùy chỉnh Giao diện (UI/UX)
- **Branding**: Thay đổi màu sắc chủ đạo, icon chatbot và tên hiển thị (ví dụ: "Pi Assistant").
- **Welcome Message**: Câu chào tự động khi khách vừa mở khung chat.
- **Quick Actions**: Tạo các nút bấm nhanh như "Xem bảng giá", "Liên hệ Sales", "Đặt lịch tư vấn".

---

## 📊 Giám sát & Cải thiện
Hệ thống cung cấp nhật ký hội thoại (Chat Logs) chi tiết:
- **Lịch sử chat**: Xem lại toàn bộ nội dung AI đã trả lời khách hàng.
- **Human Handoff**: Tự động thông báo cho nhân viên qua Telegram/Email khi khách hàng yêu cầu gặp người thật hoặc khi AI gặp câu hỏi quá khó.
- **Feedback Loop**: Bạn có thể "Like/Dislike" câu trả lời của AI để huấn luyện nó tốt hơn trong tương lai.

---

## 💡 Mẹo tối ưu Chatbot
- **Cung cấp dữ liệu thô**: Hãy upload các file PDF mô tả sản phẩm thật chi tiết, AI sẽ tư vấn tốt hơn cả nhân viên mới.
- **Cấu hình "Lead Capture"**: Yêu cầu khách hàng để lại Email/Số điện thoại trước khi bắt đầu tư vấn chuyên sâu để đẩy dữ liệu vào module **Leads**.

---

## 🔗 Tài liệu liên quan
- [Quản lý Leads & CRM](/help/article/leads-management)
- [Cấu hình API Vault](/help/article/api-vault)
- [Khắc phục sự cố AI](/help/article/troubleshooting)
