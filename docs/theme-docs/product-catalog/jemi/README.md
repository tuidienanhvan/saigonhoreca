# Jemi (Tây Ban Nha) - Catalog sản phẩm

**Nguồn chính hãng:** https://jemi.es (bản tiếng Anh: https://jemi.es/en/)
**Slogan:** "Simply built to last" - hãng chế tạo máy rửa chén/ly công nghiệp từ 1959.
**Ngày crawl:** 18/07/2026

## Tổng quan đã thu thập

- **6 nhóm sản phẩm** với tổng **28 model thật**, mỗi model đều có `source_url` đang hoạt động:
  1. Máy rửa ly/chén gầm bàn - Tech Line (GS 35, GS 40S, GS 40, GS 50, GS 50 3P, GS 60) - 6 model
  2. Máy rửa ly/chén gầm bàn - Pro Line cao cấp (KX 40, KX 50, KX 50 3P) - 3 model
  3. Máy rửa chén nắp chụp / pass-through (GS 83 E, GS 83 M, GS 85, GS 102 ML, KX 83 E) - 5 model
  4. Máy rửa chén băng chuyền kéo rổ / rack conveyor (AT 1280, AT 1580 T, IN 1520, AT 1820 T, AT 1800, AT 2100 T, AT 2140, A2440-T, AT 2700 T) - 9 model
  5. Máy rửa chén băng chuyền xích / flight-type (C 7070 TT ECO, C 5830 T, C 4650 T) - 3 model
  6. Máy rửa nồi / dụng cụ bếp (GS 60 PLUS, GSP 44, GSP 45, GSP 46) - 4 model

- Thông số kỹ thuật (năng suất, kích thước rổ, công suất kW, kích thước máy) lấy từ **trang danh mục** của jemi.es (undercounter/dome/tunnel/utensil), khá đầy đủ cho từng model.

## Thiếu / cần lưu ý (caveats)

- **`image_url` để TRỐNG cho toàn bộ model.** Trang chi tiết từng sản phẩm của jemi.es render bằng JavaScript; qua công cụ fetch chỉ đọc được menu/footer, không lấy được `<img>` src cũng như `og:image`. Cần bổ sung URL ảnh thủ công (mở từng trang product trên trình duyệt, lấy ảnh trong `/wp-content/uploads/`).
- **`seo_description` để trống** vì lý do tương tự (meta description nằm trong head bị strip). `seo_title` suy ra theo mẫu chuẩn "<Model> - Jemi" của site.
- Một số model tunnel (AT/IN) thiếu số liệu tiêu thụ nước và kích thước rổ chi tiết trên trang danh mục - đã ghi ước lượng/để dạng "Length ... (module)"; nên đối chiếu datasheet PDF chính hãng khi báo giá.
- **Reverse Osmosis Glasswasher (RO/OSM):** jemi.es có trang giới thiệu giải pháp thẩm thấu ngược cho dòng rửa ly (undercounter 400x400 & 500x500 và dòng Hood). Đây là **giải pháp/tuỳ chọn**, không phải model riêng nên KHÔNG đưa vào `models`. Trang: https://jemi.es/en/products/glasswasher-with-reverse-osmosis-new-solution-specialized-in-a-perfect-and-brilliant-glass-finish/
- Jemi cũng có thiết bị nấu (bếp, grill, bratt pan) nhưng ngoài phạm vi warewashing nên không crawl.
- `heat_source` đặt "Electric" - toàn bộ dòng warewasher Jemi dùng điện (chưa thấy phiên bản gas).

## Đề xuất bước tiếp theo

1. Bổ sung `image_url` bằng cách mở từng trang product qua trình duyệt (Chrome MCP) để lấy ảnh thật.
2. Tải datasheet PDF chính hãng để điền tiêu thụ nước/điện áp còn thiếu cho dòng tunnel.
