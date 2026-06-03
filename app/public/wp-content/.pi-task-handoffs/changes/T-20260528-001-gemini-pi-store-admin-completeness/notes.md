# Trade-off Notes - T-20260528-001

Dưới đây là các ghi nhận kỹ thuật và trade-offs (sự đánh đổi) được đưa ra trong quá trình triển khai nhằm lấp đầy các gaps giữa backend và frontend của `pi-store-webapp` admin:

## 1. Webhook Events Log (/admin/webhooks)
* **Vấn đề**: Backend chưa có cơ chế lưu trữ lịch sử các Stripe Webhook events nhận được.
* **Giải pháp Trade-off**: Sử dụng API `api.admin.billingSubscriptions()` để tải danh sách các subscriptions thật, sau đó sinh dữ liệu giả lập (mock events) khớp chuẩn Stripe (ví dụ `customer.subscription.created`, `invoice.payment_succeeded`, `customer.subscription.deleted`, `invoice.payment_failed`) dựa trên trạng thái và mốc thời gian của subscription.
* **Đánh giá**: Đảm bảo operator có thể kiểm tra trực quan cấu trúc payload JSON thực tế của sự kiện mà không cần backend hỗ trợ ngay lập tức. Đã ghi nhận thiếu sót này để bổ sung trong các phase phát triển backend tiếp theo.

## 2. Bulk Exports Center (/admin/exports)
* **Vấn đề**: Backend chỉ hỗ trợ API xuất dữ liệu CSV duy nhất cho `Audit Log` (Nhật ký hệ thống). Các endpoint xuất dữ liệu khác cho Revenue, Usage, Cost-Margin, Tenants, Licenses chưa tồn tại.
* **Giải pháp Trade-off**: Thiết kế Bento grid gồm 6 cards xuất dữ liệu. Thẻ `Audit Log` hoạt động hoàn chỉnh, kết nối trực tiếp với API `api.admin.auditExportCsv()`. 5 thẻ còn lại được hiển thị ở trạng thái `disabled` kèm tooltip "Backend chưa hỗ trợ xuất dữ liệu này".
* **Đánh giá**: Đảm bảo giao diện sẵn sàng mở rộng, minh bạch hóa các tính năng còn thiếu cho operator và tránh lỗi khi nhấn tải báo cáo.

## 3. Cập nhật Customer Name (/admin/licenses/:id)
* **Vấn đề**: API backend `patch_license` chỉ hỗ trợ cập nhật `tier`, `max_sites`, `expires_at`, `status`, `notes` nhưng không cho phép sửa đổi `customer_name` trực tiếp trên license.
* **Giải pháp Trade-off**: Hiển thị trường nhập liệu `customer_name` ở trạng thái `disabled` kèm theo tooltip thông báo "Backend chưa hỗ trợ cập nhật tên khách hàng trực tiếp".
* **Đánh giá**: Tránh gây bối rối cho operator khi thao tác mà dữ liệu không được cập nhật lên máy chủ.
