# Precision Refrigeration (UK) — Catalog dữ liệu thật

**Ngày crawl:** 2026-07-18
**Thương hiệu:** Precision Refrigeration — nhà sản xuất thiết bị lạnh công nghiệp tại Anh (thành lập 2008).

## Nguồn chính thức
- Website chính hãng thực tế: **https://www.precision-refrigeration.com**
- Lưu ý quan trọng: tên miền `precisionrefrigeration.co.uk` ghi trong yêu cầu **KHÔNG phân giải được** (lỗi DNS ENOTFOUND) — không tồn tại/không hoạt động. Toàn bộ dữ liệu được lấy từ tên miền `.com` đang chạy chính thức. Đây là site Squarespace, ảnh sản phẩm host trên `images.squarespace-cdn.com`.

## Phạm vi đã thu thập
Tổng cộng **12 nhóm sản phẩm / 45 model**, mỗi model đều có `source_url` thật và `image_url` thật:

| Nhóm | Số model |
|------|----------|
| Gastronorm Cabinets (tủ đứng mát/đông) | 6 |
| Refrigerated Counters (bàn mát/đông) | 9 |
| Under Counters (tủ dưới bàn) | 4 |
| Space Savers | 6 |
| Under Broiler Counters | 3 |
| Variable Temperature Drawers | 2 |
| Wall Cabinets (treo tường) | 1 |
| Prep Counters (bàn sơ chế / pizza) | 5 |
| Blast Chillers / Freezers (cấp đông nhanh) | 2 |
| Bottle Coolers & Glass Frosters | 6 |
| Meat Ageing Cabinets (ủ thịt) | 1 |
| Retro Refrigeration | 3 |

## Thiếu / Hạn chế (caveat)
- **`capacity_litres`**: hầu hết trang sản phẩm Precision **không công bố dung tích lít**, nên để trống thay vì bịa. Riêng blast chiller ghi năng suất theo kg/mẻ (đã đưa vào trường này).
- **`seo_description` (meta description)**: site Squarespace không xuất meta description ở các trang này, nên để trống toàn bộ. `seo_title` (thẻ `<title>`) thì đầy đủ.
- **`heat_source`**: để trống — không áp dụng cho thiết bị lạnh (giữ đúng schema).
- Một vài ảnh nhóm counter/bakery dùng chung ảnh render series (Precision tái sử dụng ảnh trên chính trang gốc) — đã giữ nguyên đúng `img src` của từng trang.
- Số cửa (`doors`) ở một số model được suy ra từ cấu hình series/hậu tố mã (VD -DDD = 3 cửa); nhiệt độ và kích thước lấy nguyên văn từ trang.
- Có thể còn model biến thể (tùy chọn cửa kính/khoá, màu RAL cho dòng Retro) không tách thành trang riêng nên không liệt kê tách.

## Cách dùng
`data.json` theo đúng schema yêu cầu (research_date, source_note, brand, groups[].models[]). Có thể import trực tiếp vào module catalog của theme saigonhoreca.
