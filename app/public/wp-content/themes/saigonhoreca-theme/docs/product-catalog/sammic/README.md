# Sammic — Nghiên cứu bổ sung danh mục (merge Sammic + Sammic-Spain)

Nguồn: website chính hãng Sammic (Tây Ban Nha) — bản tiếng Anh `sammic.com/en`. Có đối chiếu với site khu vực Mỹ `sammic.us` (nội dung tương tự, không có giá).
Ngày cào dữ liệu: 2026-07-13 (2 lượt: lượt 1 = 11 nhóm gốc, lượt 2 = bổ sung 3 nhóm + đối chiếu site). Dữ liệu máy đọc song song ở [`data.json`](./data.json).

## Vì sao có tài liệu này

Site có 2 taxonomy brand trùng nhau đang được gộp: **"Sammic"** (17 sản phẩm) và **"Sammic-Spain"** (10 sản phẩm) → gộp thành 1 brand **"Sammic"** duy nhất (27 sản phẩm). Chủ site muốn làm giàu catalog bằng sản phẩm thật từ lineup chính hãng của Sammic.

**Cập nhật lượt 2**: Đã đối chiếu trực tiếp 27 sản phẩm hiện có qua REST API (`/wp-json/wp/v2/product?product_brand=27`) — xem phần "Đối chiếu với site" bên dưới.

## Xác nhận site chính hãng

- **Global**: [sammic.com/en](https://sammic.com/en) — công ty Tây Ban Nha, thành lập 1961, khởi đầu với máy gọt khoai tây thương mại.
- **US**: [sammic.us](https://sammic.us) — site khu vực Mỹ, nội dung tương tự, không có bảng giá public.
- Sammic **không public giá** trên bất kỳ site khu vực nào đã kiểm tra — phải liên hệ đại lý/nhà phân phối.

## 14 dòng sản phẩm đã cào được (model thật, có nguồn) — 149 model

| Dòng sản phẩm | Số model | Ghi chú |
|---|---|---|
| [Vegetable Preparation Machines (Food Processors)](https://sammic.com/en/products/commercial-food-processor) | 8 | ACTIVE (CA-21/31/41/42/62) + ULTRA (CA-2V/3V/4V) |
| [Cutter-Mixers & Emulsifiers](https://sammic.com/en/products/cutter-emulsifyer-vertical-range) | 6 | ACTIVE (K-41/52/82) + ULTRA (KE-4V/5V/8V) |
| [Commercial Vacuum Packing Machines](https://sammic.com/en/products/commercial-vacuum-packing-machine) | 40 | SE-series (21) + SU-series (19) — dòng lớn nhất |
| [Blast Chillers / Freezers](https://sammic.com/en/products/blast-chillers) | 10 | AT-series, có core probe toàn dải |
| [SmartVide — Sous-Vide Circulators](https://sammic.com/en/product/smartvide5) | 4 | SmartVide 5/7/9/XL, 5-95°C |
| [Planetary Mixers](https://sammic.com/en/products/planetary-mixers) | 6 | BM-5E/5 (bàn) + BP-10/20/30/40 (sàn) |
| [Commercial Slicers](https://sammic.com/en/products/commercial-slicer) | 16 | GSS/GSM/GSL/GGS/GAE (belt/gear-driven) + VML/VSS/VSL/VS (dòng đứng, thịt nguội) |
| [Commercial Potato Peelers](https://sammic.com/en/products/potato-peelers) | 9 | PP/PPC/M/PI/PES — sản phẩm gốc của hãng |
| [Commercial Salad Spinners](https://sammic.com/en/products/salad-spinner) | 3 | ES-100/200 + PES-20 (kết hợp) |
| [Commercial Meat Mincers](https://sammic.com/en/products/meat-mincers) | 5 | PS-12/22/32 + bản lạnh PS-22R/32R |
| [Commercial Hand Blenders](https://sammic.com/en/products/commercial-hand-blender) | 16 | XM/MB/B-series, 240-750W |
| **[Combi Veg Prep / Food Processor (CK Series)](https://sammic.com/en/products/combi-veg-prep-food-processor)** — MỚI lượt 2 | 6 | CK-241 (ACTIVE) + CK-24V/35V/38V/45V/48V (ULTRA) |
| **[French Fry Cutter](https://sammic.com/en/products/french-fry-cutter)** — MỚI lượt 2 | 1 | CF-5, máy cắt khoai tây chiên cầm tay (thủ công) |
| **[Ware-Washing (Dishwashers & Glasswashers)](https://sammic.com/en/products/ware-washing)** — MỚI lượt 2 | 19 | AX/UX-series undercounter + Glass-Pro + pass-through |

**Tổng: 14 nhóm sản phẩm, 149 model thật có thông số kỹ thuật + URL nguồn.**

## ĐÍNH CHÍNH quan trọng: Sammic CÓ sản xuất dishwasher/glasswasher

Lượt nghiên cứu trước kết luận **sai** rằng Sammic không làm dishwasher (do chưa duyệt hết menu sản phẩm). Lượt 2 đã kiểm tra `sitemap.xml` và trang `ware-washing` — xác nhận Sammic có đầy đủ:
- **Undercounter Dishwashers**: AX-50 (ACTIVE), UX-50/UX-50 LITE/UX-50C/UX-50 ISO/UX-50 ISO C (ULTRA)
- **Undercounter Glasswashers**: AX-40 (ACTIVE), UX-40/UX-40C DD/UX-40 ISO/UX-40C ISO DD (ULTRA)
- **Glass-Pro Line**: GP-35/GP-40/GP-50
- **Pass-Through Dishwashers**: AX-100 (ACTIVE), UX-100/UX-120 series/UX-130BC series/UX-150SBC series (ULTRA)
- Ngoài ra còn **Rack Conveyor Dishwashers** và **Auxiliary washing equipment** (pot washers, cutlery dryers) — 2 nhánh này CHƯA fetch chi tiết model trong lượt 2, cần làm thêm nếu cần đầy đủ.

## Đối chiếu với site (`saigonhoreca.local`, brand Sammic id=27, 27 sản phẩm)

Đã fetch trực tiếp qua `curl https://saigonhoreca.local/wp-json/wp/v2/product?product_brand=27&per_page=100&_fields=id,title,slug` (2026-07-13).

- **19/149 model nghiên cứu ĐÃ có trên site** (đánh dấu `already_on_site: true` trong từng model ở `data.json`): SE-204, SE-208, SE-310, SE-520L, SE-604, SE-604CC, SE-606, SE-806, SU-310, SU-520CC, AT-3 2/3 PW, AT-3 1/1 PW, AT-5 1/1 PW, SmartVide 5, SmartVide 7, SmartVide 9, SmartVide XL, PS-22, UX-100.
- **130/149 model là MỚI hoàn toàn**, chưa có trên site — chi tiết xem `already_on_site: false` trong từng model, và `recommended_action` mỗi group để biết model nào nên ưu tiên.
- **8/27 sản phẩm hiện có trên site KHÔNG khớp** với bất kỳ model Sammic chính hãng nào đã cào: GCP-300, GCP-350 (Máy Cắt Thịt — mã "GCP" không có trong danh mục Sammic đã duyệt), LI-240 (Máy Ép — chưa khảo sát category này), MO-1000 (Lò Vi Sóng — ngoài phạm vi food-prep/sous-vide/vacuum/blast-chilling/ware-washing của Sammic), và 2 sản phẩm "Lò Combi SEHE061W/SERE061W" bị gắn nhầm — tiêu đề ghi rõ **"Giorik – Italy"**, không phải Sammic.
- **UX-50B** trên site gần giống nhưng KHÔNG khớp chính xác model nào trong dòng UX-50 vừa cào (UX-50/UX-50C/UX-50 LITE/UX-50 ISO/UX-50 ISO C) — cần xác minh thủ công.
- SmartVide XL và AT-3 1/1 PW mỗi cái xuất hiện 2 lần trên site (2 slug khác nhau) — nghi có trùng lặp còn sót lại sau merge taxonomy, nên rà soát riêng.

Chi tiết đầy đủ nằm trong `data.json` → khoá `site_comparison`.

## Chi tiết theo dòng (11 nhóm gốc — xem data.json để biết thêm 3 nhóm mới)

### 1. Vegetable Preparation Machines — 8 model
ACTIVE (CA-21, CA-31, CA-41, CA-42, CA-62) tốc độ cố định; ULTRA (CA-2V, CA-3V, CA-4V) tốc độ biến thiên động cơ brushless 300-1000rpm. Công suất 50-1000 kg/h tùy model. Hơn 35-70 kiểu đĩa cắt, có kiểu cắt độc quyền Déli-cut.

### 2. Cutter-Mixers & Emulsifiers — 6 model
K-41/52/82 (2 tốc độ) và KE-4V/5V/8V (biến thiên 300-3000rpm), bowl 4.4-8L.

### 3. Vacuum Packing Machines — 40 model (dòng lớn nhất)
SE (Sensor) 21 model + SU (Sensor Ultra) 19 model, buồng hút 288mm đến 864mm, bơm 4-155 m³/h. **Khuyến nghị chỉ chọn 3-6 model tiêu biểu** (vd SE-308, SE-410, SU-420) để giữ catalog gọn thay vì nhập cả 40.

### 4. Blast Chillers/Freezers — 10 model
Dòng AT-...PW, từ AT-3 (3xGN2/3, 500W) đến AT-20 W (xe đẩy hỗn hợp, 6000W). Chilling +3°C/90 phút, freezing -18°C lõi/240 phút, core probe toàn dải.

### 5. SmartVide (Sous-Vide) — 4 model
SmartVide 5 (30L, 1600W) < 7 (56L, 2000W) ≈ 9 (56L, 2000W) < XL (120L, 2600W). Cùng dải nhiệt 5-95°C, độ chính xác 0.01°C, Bluetooth/WiFi, HACCP-ready. Đây là dòng chủ lực sous-vide — khuyến nghị ưu tiên cả 4 model.

### 6. Planetary Mixers — 6 model
BM-5E/5 (bàn, 250-300W) + BP-10/20/30/40 (sàn, 550-1400W, bowl 10-40L).

### 7. Commercial Slicers — 16 model
GSS/GSM (belt-driven), GSL (belt-driven cỡ lớn), GGS (bánh răng thủ công + đếm lát), GAE (bánh răng tự động), VML/VSS/VSL/VS (dòng đứng cho thịt nguội). Blade 250-350mm.

### 8. Potato Peelers — 9 model
PP/PPC (6-12kg, nhôm/inox), M-5 (5kg), PI-10/20/30 (10-30kg), PES-20 (2-trong-1 gọt + sấy). Sản phẩm gốc từ 1961.

### 9. Salad Spinners — 3 model
ES-100 (6kg/chu kỳ), ES-200 (12kg/chu kỳ, tối đa 720kg/h), PES-20 (2-trong-1, trùng với dòng Potato Peelers).

### 10. Meat Mincers — 5 model
PS-12/22/32 (100-425 kg/h) + bản lạnh PS-22R/32R (an toàn thực phẩm cao hơn). **PS-22 đã có trên site.**

### 11. Hand Blenders — 16 model
3 dòng thân máy XM/MB/B, công suất 240-750W, trục 223-630mm, xử lý 10-250L.

### 12. Combi Veg Prep / Food Processor (CK Series) — 6 model (MỚI)
Máy 2-trong-1 (đầu Veg Prep + đầu Cutter dùng chung thân máy): CK-241 (ACTIVE, 800W) + CK-24V/35V/38V/45V/48V (ULTRA, 1100-1500W, 10 vị trí tốc độ biến thiên). Gần với dòng CA/CA-V đã có nhưng mã model khác — cần Sammic xác nhận đây là dòng riêng hay đổi tên.

### 13. French Fry Cutter — 1 model (MỚI)
CF-5: máy cắt khoai tây chiên thủ công (không động cơ điện), 150kg/h, khối dao đổi được 8/10/12mm, NSF listed.

### 14. Ware-Washing (Dishwashers & Glasswashers) — 19 model (MỚI)
Xem phần "ĐÍNH CHÍNH" ở trên. **UX-100 đã có trên site** (dòng pass-through).

## Việc còn thiếu (ngoài khả năng crawl web trong 2 lượt này)

- **Giá bán** — Sammic không public giá ở bất kỳ site khu vực nào (cả `.com` và `.us`), cần liên hệ Sammic Việt Nam hoặc nhà phân phối.
- **Ảnh sản phẩm độ phân giải cao** — chưa tải, chỉ có URL trang sản phẩm.
- **Rack Conveyor Dishwashers** và **Auxiliary washing equipment** (pot washers, cutlery dryers) trong group Ware-Washing — chưa fetch chi tiết model.
- **8 sản phẩm brand Sammic hiện có trên site không khớp model nghiên cứu** (GCP-300/350, LI-240, MO-1000, 2x Combi Giorik) — cần rà soát nguồn gốc/brand trước khi coi là "đã trùng" hay "cần sửa brand".
