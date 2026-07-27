## Báo cáo Audit Sơ Bộ - 14 hãng (TASK-20260721-037 → 050)

### Tóm tắt Trạng thái

| Brand | Models | No Image | Dup URLs | Local Files | Notes |
|-------|--------|----------|----------|-------------|-------|
| Josper | 212 | 52 | 11 | 191 | 52 model thiếu image_url, 11 ảnh trùng |
| Halton | 314 | 0 | 5 | ? | Dữ liệu hoàn chỉnh |
| Jemi | 693 | 11 | 139 | ? | 11 model thiếu image_url, 139 ảnh trùng |
| Kolb | 97 | 4 | 16 | ? | 4 model thiếu image_url, 16 ảnh trùng |
| Precision Refrigeration | 83 | 12 | 9 | ? | 12 model thiếu image_url |
| Sammic | 190 | 0 | 39 | ? | 39 ảnh trùng |
| Hobart | 112 | 3 | 17 | ? | 3 model thiếu image_url, 17 ảnh trùng |
| BFC | 40 | 0 | 0 | ? | Hoàn chỉnh, không ảnh trùng |
| Fiorenzato | 34 | 0 | 0 | ? | Hoàn chỉnh, không ảnh trùng |
| Winterhalter | 57 | 1 | 6 | ? | 1 model thiếu image_url |
| Anfim | 21 | 0 | 1 | ? | Hoàn chỉnh |
| Mahlkönig | 37 | 0 | 3 | ? | 3 ảnh trùng |
| Hoshizaki | 462 | 0 | 104 | ? | Nhiều ảnh trùng (104) |
| Rational | 113 | 2 | 3 | ? | 2 model thiếu image_url |

### Các bước cần thực hiện tiếp theo

**1. Kiểm tra source_url trên HTML**
- Theme đã có code hiển thị source_url tại `single.php:d97`
- Cần xác minh mỗi model có source_url không rỗng trong JSON/CSV

**2. Tải ảnh thiếu**
- Josper: 52 model thiếu image_url - cần crawl từ trang sản phẩm
- Jemi: 11 model thiếu image_url
- Kolb: 4 model thiếu image_url
- Precision Refrigeration: 12 model thiếu image_url
- Hobart: 3 model thiếu image_url
- Winterhalter: 1 model thiếu image_url
- Rational: 2 model thiếu image_url

**3. Xác minh ảnh trùng**
- Josper: 11 cặp ảnh trùng - phân tích namespace để xác định ảnh đại diện
- Jemi: 139 ảnh trùng - kiểm tra xem có phải ảnh đại diện hay không
- Kolb: 16 ảnh trùng
- Sammic: 39 ảnh trùng
- Hobart: 17 ảnh trùng
- Hoshizaki: 104 ảnh trùng
- Các hãng khác có ít ảnh trùng hơn

**4. Kiểm tra file ảnh cục bộ**
- Cần sync ảnh từ `docs/product-catalog/{brand}/images/` đến `wp-content/uploads/saigonhoreca/product-catalog/{brand}/`

### Agent Manager Status

14 agent đã được spawn với các task:
- TASK-20260721-037: Josper Audit
- TASK-20260721-038: Halton Audit  
- TASK-20260721-039: Jemi Audit
- TASK-20260721-040: Kolb Audit
- TASK-20260721-041: Precision Refrigeration Audit
- TASK-20260721-042: Sammic Audit
- TASK-20260721-043: Hobart Audit
- TASK-20260721-044: BFC Audit
- TASK-20260721-045: Fiorenzato Audit
- TASK-20260721-046: Winterhalter Audit
- TASK-20260721-047: Anfim Audit
- TASK-20260721-048: Mahlkönig Audit
- TASK-20260721-049: Hoshizaki Audit
- TASK-20260721-050: Rational Audit

### File tham khảo
- BRAND_AUDIT_PROMPT.md - Hướng dẫn chi tiết audit từng hãng
- single.php - Theme đã hiển thị source_url cho product CPT