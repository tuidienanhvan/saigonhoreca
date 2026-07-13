# docs/ — Bản đồ tài liệu saigonhoreca-theme

Đọc file nào tuỳ việc đang làm:

| Đang làm gì? | Đọc file này |
|---|---|
| Sắp sửa cơ chế nạp CSS/JS/font/ảnh (enqueue, preload, lazy-load...) | **[PERF-DECISIONS.md](PERF-DECISIONS.md)** — BẮT BUỘC đọc trước, có các quyết định đã đo thực nghiệm, cấm phá |
| Cần hiểu cấu trúc `inc/` (engine/config/site), route→bundle, di chuyển file | **[ARCHITECTURE.md](ARCHITECTURE.md)** |
| Thêm dự án pillar mới, hoặc cần biết dự án nào đang lệch/thiếu gì | **[PILLAR-STATUS.md](PILLAR-STATUS.md)** |
| Viết PHP/CSS cho 1 project pillar (6 section, class BEM...) | **[project-pillar-standard.md](project-pillar-standard.md)** |
| Cần biết token màu/spacing, cách dùng Tailwind v4 trong dự án con | **[styling-and-tokens.md](styling-and-tokens.md)** |
| Upload/xử lý ảnh (WebP, kích thước, nơi lưu) | **[image-standard.md](image-standard.md)** |
| Xem điểm Lighthouse trước/sau đợt nâng cấp 07/2026 | **[perf-baselines/](perf-baselines/)** — chỉ giữ `scoreboard-baseline` (trước) + `scoreboard-final` (sau); các lần đo debug dở dang đã dọn |

## Quy tắc thêm tài liệu mới vào đây

- Chỉ đặt tài liệu **về chính codebase saigonhoreca-theme này** (kiến trúc, chuẩn code,
  quyết định kỹ thuật). Tài liệu marketing/branding/nội dung cho dự án khác (kể cả cùng
  khách hàng) KHÔNG để ở đây — đã từng có 3 file lạc từ dự án OKU-SEKAI (Đà Lạt/Phan
  Thiết) bị đặt nhầm vào đây, đã chuyển đi 07/2026.
- Không tạo báo cáo tổng hợp trùng lặp nội dung 3 file trên — nếu cần báo cáo tổng quan,
  cập nhật TRỰC TIẾP vào ARCHITECTURE.md/PERF-DECISIONS.md/PILLAR-STATUS.md, đừng tạo
  file mới đóng băng tại 1 thời điểm rồi lệch dần với 3 file nguồn.
- `perf-baselines/` chỉ giữ mốc có giá trị tham chiếu lâu dài (đo TOÀN site, trước/sau
  1 đợt nâng cấp lớn) — không lưu kết quả đo lẻ từng trang khi debug, số liệu quan trọng
  đã được rút vào PERF-DECISIONS.md dạng tóm tắt.
