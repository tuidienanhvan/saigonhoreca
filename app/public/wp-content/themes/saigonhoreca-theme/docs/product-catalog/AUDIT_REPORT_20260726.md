# Audit Report — Saigonhoreca Product Catalog (2026-07-26)

Thay thế `AUDIT_REPORT_20260725.md` (đã xóa — liệt kê 20 hãng, trong đó có 5 hãng
đã bị loại khỏi danh mục đối tác theo quyết định của sếp, xem
`../../../../../../../.claude/projects/C--/memory/feedback_boss_brand_partner_list.md`).

## Danh mục đối tác chính thức: 15 hãng

| Hãng | Sản phẩm | Ảnh | Category | Xuất xứ |
|---|---:|---:|---:|---|
| fujimak-japan | 537 | 537 | 54 | Japan |
| giorik-italia | 555 | 555 | 16 | Italy |
| halton | 104 | 104 | 14 | Finland |
| hoshizaki | 462 | 462 | 8 | (nhiều) |
| jemi | 594 | 594 | 86 | Spain |
| josper | 76 | 76 | 16 | Spain |
| kolb | 97 | 97 | 21 | (nhiều) |
| mahlkonig | 37 | 37 | 5 | Germany |
| precision-refrigeration | 83 | 83 | 22 | United Kingdom |
| rational | 28 | 28 | 6 | Germany |
| sammic | 291 | 291 | 42 | Spain |
| toprinse-sunmixer | 38 | 38 | 6 | China |
| traulsen | 67 | 67 | 13 | United States |
| vulcan | 308 | 308 | 12 | (nhiều) |
| winterhalter-germany | 28 | 28 | 7 | (nhiều) |
| **TỔNG** | **3305** | **3305 (100%)** | **328** | |

## Đã loại khỏi danh mục (theo quyết định sếp — archived, không xóa)

Hobart, Berjaya-Malaysia, BFC, Fiorenzato, Anfim — 790 model, nằm tại
`_archived-brands-per-boss-decision/`.

## Kiểm tra chất lượng đã thực hiện

- `scripts/generic_local_audit.py` — 0 lỗi trên toàn bộ 15 hãng: seo_title,
  seo_description, description_en, source_url đầy đủ 100%; 0 ký tự tiếng Việt
  rò rỉ vào trường EN; 0 file ảnh tham chiếu bị thiếu trên đĩa.
- `scripts/deep_image_audit.py` — 0 file ảnh hỏng, 0 ảnh trắng/đơn sắc, **0 trùng
  ảnh chéo hãng** (trùng nội bộ cùng hãng là hợp lệ — biến thể cùng dòng dùng
  chung ảnh nhà sản xuất).
- Đối chiếu sitemap sống cho 13 hãng có cấu hình sẵn (`sitemap_completeness.py`)
  + kiểm tra thủ công giorik-italia/traulsen (không có sitemap.xml) — **0 sản
  phẩm thật bị bỏ sót**, mọi URL "thiếu" đều là phụ kiện đã xóa, trang danh mục,
  hoặc URL trùng ngôn ngữ khác.
- Đồng bộ ảnh: số file trên đĩa == số file tại
  `D:\Administrator\Downloads\Saigonhoreca_Product_Catalog\images` cho từng
  hãng, 1:1 — 0 ảnh mồ côi, 0 model thiếu ảnh.

## Việc đã sửa trong đợt audit cuối (26/07/2026)

1. **Fujimak Teppanyaki/Jet Oven** — theo yêu cầu trực tiếp của sếp bổ sung 2
   nhóm này: cả 2 đã tồn tại nhưng có 5 model xếp nhầm nhóm (4 model Jet Oven
   nằm trong Teppanyaki, 1 model bakery nằm trong Teppanyaki) — đã chuyển đúng.
2. **31 phụ kiện lọt** — xóa nhóm "Work Equipment (Tables, Sinks, Shelving)" của
   fujimak (14 model bàn/bồn/kệ inox) + 12 model đơn lẻ (xe đẩy khay, kệ nhiệt,
   giá đỡ...) + 2 model vulcan (griddle accessory) + 2 model winterhalter (lõi
   lọc tiêu hao).
3. **39 model mã SKU/specs lỗi**: traulsen (9/13 bổ sung specs thật qua đại lý
   Mỹ, 4/13 là dòng sản phẩm nhiều biến thể ghi chú rõ chưa tách được từng SKU),
   fujimak (13 model IoT/dịch vụ đặt riêng xác nhận thật sự không có SKU catalog
   độc lập — không bịa), jemi (11 "lỗi" hóa ra là false positive — mã số trần
   625/631/741... là quy ước thật của Jemi cho dòng bếp gas, đã có specs đầy
   đủ), sammic (1 model bổ sung specs thật từ trang nguồn).
4. Xóa 2090 file ảnh mồ côi (2 nơi) — ảnh đã tải nhưng không model nào còn
   tham chiếu, chủ yếu là ảnh của 565+31 phụ kiện đã xóa qua các đợt.
5. Dọn 6 script chết/trùng lặp tham chiếu 5 hãng đã loại.
