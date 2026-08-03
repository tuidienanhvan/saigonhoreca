# Hobart — Catalog đầy đủ (`data-full.json`)

**Ngày khảo sát:** 18/07/2026
**Brand string:** `Hobart`
**File dữ liệu:** `data-full.json` (7 nhóm · 94 model)

> ⚠️ File này **độc lập** với `data.json`. `data.json` là bản đã import làm draft trước đó (tất cả nhóm đang "models: 0") và **không bị đụng tới**. `import.csv` cũng không bị đụng tới.

> **Cập nhật 2026-07-19:** Ban đầu file này từng có thêm 2 nhóm "Cooking Equipment" (31 model) và "Refrigeration" (18 model) lấy từ vulcanequipment.com/traulsen.com. Sau khi xác nhận đây là 2 thương hiệu **độc lập** (Vulcan, Traulsen — chỉ cùng thuộc tập đoàn mẹ ITW Food Equipment Group với Hobart, không phải dòng sản phẩm Hobart), theo yêu cầu khách hàng đã **tách 2 nhóm này ra thành catalog riêng**: xem `docs/product-catalog/vulcan/` và `docs/product-catalog/traulsen/`. File này quay lại đúng 94 model gốc của Hobart Corporation.

> **Cập nhật 2026-07-19 (đợt 2 — bổ sung ảnh):** Fetch lại trực tiếp từng trang nguồn (curl với User-Agent trình duyệt, do WebFetch/browser trước đó không lộ hết `<img src>`) cho 10 model bị báo thiếu ảnh hoặc lỗi HTTP khi tải ảnh cũ. Kết quả: **7/10 tìm được ảnh thật** — PD35, PD70 (Power Drive Units — cả hai dùng chung ảnh sản phẩm "PD70" vì trang chỉ có ảnh gắn nhãn PD70 cho cả họ, không có ảnh riêng PD35), LXnR (Undercounter Dishwasher), FT1000i (Conveyorized Washer), NGW/NGW1/NGW2 (Automatic Wrapping System), HWS-4 / W32 / 625A (Hand Wrap Solutions), HTi Self-Service Scale. **3/10 không tìm được** — HSL220, HSL300, HSU440 (Spiral Mixers): trang `spiral-mixers` hiện tại (đã kiểm tra HTML gốc + schema.org JSON-LD trên trang) chỉ còn ảnh sản phẩm cho HSL130 và HSL180; ba mã HSL220/HSL300/HSU440 chỉ xuất hiện trong tên file PDF spec sheet và bảng so sánh thông số, không có `<img>` nào gắn với chúng — các URL ảnh cũ trong file (`Spiral%20Mixer%20HSL220/300...qr%20.png`) trả về **404** khi kiểm tra lại bằng curl, xác nhận ảnh đã bị gỡ khỏi site. `image_url` của 3 model này đã để trống thay vì giữ URL hỏng hoặc bịa ảnh khác.

---

## 1. Nguồn dữ liệu và phương pháp

| Bước | Nội dung |
|---|---|
| 1 | Tải `https://www.hobartcorp.com/sitemap.xml` (curl, 312 URL) — sitemap duy nhất, không cần sitemap index. |
| 2 | Lọc toàn bộ URL nằm dưới `/products/` → 87 URL, bao gồm cả trang danh mục cấp 1 (vd `food-prep/mixers`) lẫn trang dòng sản phẩm cấp 2 (vd `food-prep/mixers/spiral-mixers`). |
| 3 | Loại các trang `*/accessories`, `*mixer-accessories`, `*slicing-accessories`, `*specialty-accessories` — đây là phụ kiện/đính kèm, không phải model độc lập. |
| 4 | Dùng WebFetch tải từng trang dòng sản phẩm còn lại (61 trang leaf), bóc tách: `<title>`, `<meta name="description">`, mô tả sản phẩm, bảng/tóm tắt thông số kỹ thuật hiển thị trên trang, ảnh sản phẩm thật (miền `webdam-assets` trên hobartcorp.com hoặc `cdn2.webdamdb.com`), và URL nguồn. |
| 5 | Ghi kết quả vào `data-full.json` theo đúng schema mẫu của `precision-refrigeration/data-full.json`. |

**Không bịa số liệu:** phần lớn thông số chi tiết (kích thước mm, điện áp chính xác từng cấu hình, bản vẽ) chỉ có trong file PDF "Spec Sheet" tải riêng từng model — WebFetch không trích xuất được nội dung PDF nên các trường đó để trống thay vì đoán. 6 model không tìm được ảnh trực tiếp trên trang (PD35, PD70, LXnR, FT1000i, HTi Self-Service Scale, RFID GT Scales) đã để `image_url` rỗng.

**Hai thực thể Hobart toàn cầu:** (1) Hobart Corporation (Mỹ, ITW Food Equipment Group, `hobartcorp.com`) — dùng làm nguồn chính; (2) Hobart GmbH (Đức, `international.hobart-export.com`) phục vụ châu Âu/quốc tế nhưng không có danh mục sản phẩm tiếng Anh độc lập để đối chiếu — không dùng.

---

## 2. Thống kê

| Chỉ số | Số lượng |
|---|---|
| Nhóm (`groups`) | **7** |
| Model | **94** |
| Model có `image_url` | **91 / 94** (cập nhật 2026-07-19 đợt 2; 3 model HSL220/HSL300/HSU440 không còn ảnh public trên site nguồn) |
| Model có `specs` (≥1 trường) | **90 / 94** |
| Model có `source_url` hoạt động | **94 / 94** |

### Phân bổ theo nhóm

| # | Nhóm (EN) | Nhóm (VI) | Model | Nguồn (trang leaf) |
|---|---|---|---|---|
| 1 | Mixers | Máy Trộn Bột / Trộn Thực Phẩm | 18 | Legacy+ countertop (2), Legacy+ floor (5), HL662 pizza (1), Centerline (3), Spiral (5), N50 (1), Cutter Mixer HCM450 (1) |
| 2 | Slicers | Máy Thái Lát | 11 | HS series (4), Portion Scale (2), Centerline EDGE (5) |
| 3 | Food Processors | Máy Xay/Cắt Thực Phẩm Đa Năng | 11 | Floor continuous-feed FP300i/FP400i (2), Countertop continuous-feed FP100-350 (4), Bowl-style FP41/HCM61/HCM62 (3), Centerline CCH32/34 (2) |
| 4 | Meat Room | Thiết Bị Sơ Chế Thịt | 10 | Meat saws (2), Mixer-grinders (3), Choppers/grinders (4), Tenderizer 403 (1) |
| 5 | Specialty Food Prep Products | Thiết Bị Sơ Chế Chuyên Dụng | 10 | Salad dryers (2), Peelers (4), Food cutters (2), Power drive units (2) |
| 6 | Commercial Dishwashers | Máy Rửa Chén Công Nghiệp | 20 | Conveyor (CL-ADV/VL/BAS), Door (AMTL, AM16 x4 biến thể), Undercounter (LXnR, LXn Base), Pot/pan/utensil (Turbowash II, PWn-ADV, PWn-BAS), Industrial (FT1000i, CL64T), Waste (WastePro, FD4), Flight (FT2000-ADV, FT2000-BAS), Glasswasher (LXGnR/PR) |
| 7 | Weighing & Wrapping Equipment | Thiết Bị Cân & Đóng Gói | 14 | Wrapping (NGW/1/2, Hand Wrap, CLAS, NGW2P), Scales (HT Series, Wrap Station, HTi Self-Service, PS40, HBR302, FreshServe, GT Series, RFID GT), Software (HTe, FreshXpress) |

*Nhóm 7 và một phần nhóm 6 thay thế cho placeholder cũ `PLACEHOLDER_DISHWASHERS_WEIGHWRAP` trong `data.json` — đã tách thành 2 nhóm rõ ràng: "Commercial Dishwashers" và "Weighing & Wrapping Equipment" vì đây là hai mảng sản phẩm hoàn toàn khác nhau trên chính website Hobart (menu `/products/commercial-dishwashers` và `/products/weigh-wrap` tách biệt).*

---

## 3. Cách xử lý các "họ sản phẩm" (product families)

Nhiều trang trên hobartcorp.com trình bày **một trang = một họ sản phẩm** với nhiều mã cấu hình con (kích thước, điện/gas/hơi, tuỳ chọn ventless/base/advansys...), tương tự cách website liệt kê biến thể. Theo đúng tinh thần đã áp dụng cho Precision-UK (nhúng hệ mã cấu hình vào `specs` thay vì tách dòng), các trường hợp sau được giữ là **một model** với toàn bộ mã con liệt kê trong specs:

- **CL-ADV, CL-VL, CL-BAS** (conveyor dishwasher) — mỗi họ có 2–9 mã kích thước/điện-hơi.
- **AM16VL/VLT, AM16 ASR, AM16 Base, AM16SCB** — 4 biến thể chính của dòng cửa AM16, mỗi biến thể lại có mã Standard/Tall/Ventless riêng.
- **FT2000-ADV, FT2000-BAS** — mỗi họ có 8 mã điện/hơi/blower.
- **PWn-ADV, PWn-BAS** — theo dung tích 10/12/20 sheet pan.
- **WastePro** — gộp WPR-1260 (remote), WPS-1260 (close-coupled), WP-6/WP-9 Waterpress.
- **FD4** — 7 mã dung lượng (FD4/50 → FD4/500) gộp thành 1 model "FD4" vì trang liệt kê chung, không mô tả riêng từng mã.
- **NGW/NGW1/NGW2, HTs/HTi, GTs/GTi** — các cặp/bộ ba biến thể cùng trang, cùng mô tả chính, khác nhau ở cấu hình phần cứng/phần mềm.

Việc này giúp bám sát cấu trúc thật của website (không tự ý tách 1 trang thành hàng chục "model" giả) trong khi vẫn giữ đủ thông tin để tra cứu qua trường `specs`.

---

## 4. Không bao gồm / Cần người xem lại

1. **PDF Spec Sheet** — hầu hết model có link tải "Spec Sheet" (PDF) chứa thông số chi tiết (kích thước mm, điện áp, trọng lượng chính xác). WebFetch không đọc được nội dung PDF nên các trường này để trống. Nếu cần đầy đủ, phải tải PDF thủ công.
2. **3 model còn thiếu ảnh** (`image_url` rỗng, đã xác nhận lại 2026-07-19): HSL220, HSL300, HSU440 (Spiral Mixers) — ảnh sản phẩm cũ trả về 404, trang nguồn hiện tại không còn ảnh public cho 3 mã này (chỉ HSL130/HSL180 còn ảnh). RFID Enabled GT Scales cũng chưa có ảnh (chưa kiểm tra lại trong đợt cập nhật này). Các model PD35, PD70, LXnR, FT1000i, NGW/NGW1/NGW2, HWS-4/W32/625A, HTi Self-Service Scale trước đây thiếu ảnh đã được bổ sung — xem ghi chú "Cập nhật 2026-07-19 (đợt 2)" ở đầu file.
3. **Trang phụ kiện đã loại bỏ khỏi model:** `food-prep-equipment/accessories`, `mixers/mixer-accessories`, `slicers/slicing-accessories`, `specialty-products/specialty-accessories`, `weigh-wrap/accessories/wrapping-equipment-accessories`, `commercial-dishwashers` không có accessories riêng. Đây là phụ kiện/đính kèm cho model chính, không phải sản phẩm độc lập.
4. **Trang không phải sản phẩm vật lý:** `food-prep/whats-new` (trang tin tức), `products/financing-hobart`, `products/find-dealer-or-sales-representative`, `products/buy-online`, `absorption-ratio-capacity-calculator` (công cụ tính toán) — không đưa vào `data-full.json`.
5. **Phần mềm (HTe, FreshXpress)** — đưa vào nhóm Weighing & Wrapping vì nằm trong menu `/products/weigh-wrap/software` của hãng, nhưng đây là phần mềm chứ không phải thiết bị vật lý; trường `heat_source` để rỗng cho 2 mục này.
6. **RFID Enabled GT Scales** — trang không nêu mã model cụ thể (chỉ tham chiếu chung "GT Series"), đã ghi rõ trong `description_en` thay vì bịa mã.
7. **Giá bán** — hobartcorp.com không công khai giá trên trang sản phẩm (khác với Precision-UK có price list PDF); không có trường giá trong file này.
8. **Cooking Equipment và Refrigeration** — hobartcorp.com không tự bán 2 mảng này (trang `/products/cooking/*` và `/products/refrigeration/*` trả 403; trang duy nhất còn sống `/convection-ovens` chỉ điều hướng sang các thương hiệu chị em). Dữ liệu đã cào từ vulcanequipment.com (Vulcan) và traulsen.com (Traulsen) — xem `docs/product-catalog/vulcan/` và `docs/product-catalog/traulsen/`.
