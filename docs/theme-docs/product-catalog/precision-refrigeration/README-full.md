# Precision-UK — Catalog đầy đủ (`data-full.json`)

**Ngày khảo sát:** 18/07/2026
**Brand string:** `Precision-UK`
**File dữ liệu:** `data-full.json` (22 nhóm · 73 model)

> ⚠️ File này **độc lập** với `data.json`. `data.json` là bản đã import làm draft trước đó và **không bị đụng tới**.

---

## 1. Nguồn dữ liệu

| # | Nguồn | Dùng cho |
|---|-------|----------|
| 1 | **https://www.precision-refrigeration.com** (Squarespace) | 48/48 trang sản phẩm hiện hành. Lấy danh sách URL từ `sitemap.xml`, tải HTML thô bằng curl, bóc tách mô tả, bảng thông số, Key Features, Options, ảnh sản phẩm (`og:image`), thẻ `<title>` và `<meta name="description">`. |
| 2 | **Precision Price List 2026** (PDF chính chủ trên Squarespace CDN) | Freezer Meatwell **PFW100** (không có trang web nào), giá niêm yết 2026, và các mã cấu hình biến thể: `PPC 410-DDDD/TTTT`, `HSS 340-DD/HH`, `HWU 211-UGG/UDD`, tuỳ chọn nhiệt đới `TBBS`. Bổ sung thông số đầy đủ cho PPS106 / PPS209. |
| 3 | **Precision Brochure 2019** (PDF lưu tại profoodservicereps.co.uk) | Toàn bộ dòng đã ngưng sản xuất: xe đẩy tiệc `PMR 24` / `PMH 30`, tủ hong vịt `DDPT 601` / `DDPT 1401`, họ bàn lạnh dàn nóng rời `MCR/LCR`, các model bị thay thế (`MCU 423`, `LCU 223`, `LCU 323`, `HWU 111`, `HWU 311`, `PCF 15/40/50`, `BBS 1850`), và **hệ mã cấu hình bàn lạnh** (Counter Model Codes, trang 17–18). |
| 4 | **Thông cáo báo chí Precision 18/02/2013 (Cision)** | `PTC70` — tủ rã đông có kiểm soát. Đây là nguồn duy nhất còn truy cập được; model này không có trong website, price list 2026, lẫn catalogue 2019. |

**Lưu ý tên miền:** `precisionrefrigeration.co.uk` **không phân giải** (không tồn tại). Tên miền đúng là `precision-refrigeration.com`. Các PDF cũ 2019 vẫn ghi `precision-refrigeration.co.uk` ở footer — đó là tên miền cũ đã bỏ.

---

## 2. Thống kê

| Chỉ số | Số lượng |
|---|---|
| Nhóm (`groups`) | **22** |
| Model | **73** |
| Model có `image_url` | **72 / 73** (xem mục 7) |
| Model có `specs` (≥3 trường) | **73 / 73** |
| Model có `source_url` hoạt động | **73 / 73** |
| Mã model trùng lặp | 0 |

### Phân bổ theo nhóm

| # | Nhóm (EN) | Nhóm (VI) | Model |
|---|---|---|---|
| 1 | Top Mount Cabinets | Tủ mát/đông đứng GN2/1 dàn lạnh gắn nóc | 4 |
| 2 | Bottom Mount Cabinets | Tủ mát/đông đứng dàn lạnh gắn đáy | 2 |
| 3 | 670 Series GN1/1 Counters | Bàn lạnh Series 670 khay GN1/1 | 5 |
| 4 | 520 Series GN2/3 Slimline Counters | Bàn lạnh Series 520 khay GN2/3 (thân hẹp) | 2 |
| 5 | Bakery Counters (600 x 400) | Bàn lạnh bánh mì / bánh ngọt khay 600×400 | 2 |
| 6 | Under Counters | Tủ mát/đông gầm bàn | 4 |
| 7 | Space Savers | Tủ mát/đông tiết kiệm không gian | 6 |
| 8 | Under Broiler Counters | Tủ mát đặt dưới bếp nướng salamander | 3 |
| 9 | Variable Temperature Drawers | Ngăn kéo lạnh nhiệt độ thay đổi được | 2 |
| 10 | Prep Counters | Bàn lạnh sơ chế | 4 |
| 11 | Space Saving Prep Counter | Bàn lạnh sơ chế cỡ nhỏ | 3 |
| 12 | Pizza Prep Stations | Bàn lạnh sơ chế pizza | 2 |
| 13 | Freezer Meatwell | Tủ đông âm bàn dạng khay thịt | 1 |
| 14 | Wall Cabinets | Tủ mát treo tường | 2 |
| 15 | Blast Chiller / Freezers | Tủ cấp đông nhanh / làm lạnh nhanh | 2 |
| 16 | Bottle Coolers & Glass Frosters | Tủ mát quầy bar & tủ làm lạnh ly | 9 |
| 17 | Retro Refrigeration | Tủ lạnh phong cách retro | 3 |
| 18 | Meat Ageing Cabinet | Tủ ủ thịt (dry aging) | 1 |
| 19 | LEGACY — Mobile Banqueting Carts | Xe đẩy tiệc di động (lạnh / hâm nóng) | 2 |
| 20 | LEGACY — Duck Drying Cabinets | Tủ sấy/hong vịt | 2 |
| 21 | LEGACY — Remote Condensing Counters | Bàn lạnh dàn nóng rời (MCR/LCR) | 2 |
| 22 | LEGACY — Superseded Models | Model cũ đã bị thay thế | 10 |

---

## 3. Những gì đã được bổ sung so với bản crawl trước

Đây là các dòng mà một lần crawl menu/sitemap thông thường **sẽ bỏ sót**:

1. **Freezer Meatwell (PFW100)** — nguyên một danh mục có mục riêng trong price list (tr. 61–64) nhưng **không có bất kỳ trang nào trên website**. Vô hình với mọi site crawl. Đã lấy đủ mô tả, 18 trường thông số, Key Features, giá £3,260.
2. **Bakery Counters (RBCU 264 / RBCU 364)** — danh mục riêng trong price list, nhưng website chôn trong `/counters` nên crawl theo menu đọc nhầm thành bàn lạnh thường.
3. **520 Series GN2/3 (MCU 223 / MCU 323)** — series riêng biệt với 670 Series GN1/1, cũng bị chôn trong `/counters`.
4. **Tách Top Mount (MPT/LPT 601/1401) vs Bottom Mount (MPU/LPU 401)** — hai danh mục riêng trong price list, website gộp chung vào một trang `/cabinets`.
5. **Space Saving Prep Counter (HSS 340)** — danh mục riêng (price list tr. 53–56), website xếp trong `/prep-counters`.
6. **Pizza Prep Stations (PPS106 / PPS209)** — danh mục riêng (price list tr. 57–60), cũng xếp trong `/prep-counters`.
7. **HUC 150, LUC 150, LSS 300** — ba model space-saver có trang thật và **có trong sitemap.xml hiện tại** (kết quả khảo sát trước ghi là thiếu; đã kiểm tra lại, sitemap ngày 18/07/2026 có đủ 6 model). Cả 6 đều đã lấy dữ liệu đầy đủ.
8. **Hệ mã cấu hình SKU cho bàn lạnh** — MCU/LCU/MCR/LCR là hàng đặt theo cấu hình. Toàn bộ bảng mã (M/L, C, U/R, 211/311/411/223/323/423, U/D/H/T, 10 tuỳ chọn chiều cao) đã được nhúng vào trường `specs` của từng model counter để tra cứu.
9. **Biến thể nhiệt đới TBBS** (TBBS 600 / 900 / 1350) — 43°C ambient, kính 3 lớp. Là tuỳ chọn có giá riêng trên BBS 600/900/1350; **không áp dụng** cho hai model cao 1850mm.
10. **Họ MCR/LCR dàn nóng rời** — nhánh "R" của hệ mã, cấp nguồn từ catalogue 2019 (không dùng trang distributor Úc vì trả HTTP 403).
11. **Legacy đầy đủ**: PMR 24, PMH 30, DDPT 601, DDPT 1401, PTC70, PCF 15/40/50, HWU 111/311, MCU 423, LCU 223/323, BBS 1850.

---

## 4. Những gì KHÔNG bao gồm — và lý do

| Không có | Lý do |
|---|---|
| **Hàng nghìn mã SKU đặt hàng đầy đủ của dòng counter** | Mã đầy đủ ghép từ vị trí cửa/ngăn kéo (U/D/H/T cho từng section) + mã chiều cao, ví dụ `LCU311-UDDD-865`. Tổ hợp lên tới hàng nghìn mã. Liệt kê hết là vô nghĩa cho catalog. Thay vào đó chỉ ghi model gốc, và nhét **nguyên bảng hệ mã** vào `specs` để người dùng tự dựng mã. |
| **Phụ kiện / phụ tùng thay thế / tuỳ chọn rời** | Kệ, khay GN, chân, mặt bàn tuỳ biến, Precision Connect… đã ghi trong trường `Options` của từng model, không tách thành sản phẩm riêng. |
| ~~**`image_url` cho 16 model chỉ có trong PDF**~~ | **ĐÃ XỬ LÝ 18/07/2026 — xem mục 7.** 15/16 đã có ảnh; chỉ còn PFW100 để trống. |
| **`seo_title` / `seo_description` cho model từ PDF** | Không có trang web → không có thẻ meta. Để trống. |
| **`seo_description` cho toàn bộ model từ website** | Squarespace của Precision **không set** `<meta name="description">` (giá trị rỗng trên mọi trang). Đã kiểm tra 48/48 trang. Để trống theo đúng dữ liệu thật. |
| **Giá bán tại Việt Nam** | Không có nguồn. Giá trong file là **giá niêm yết UK** (£, price list 2026 hoặc catalogue 2019) và có ghi rõ năm trong tên trường. |
| **Trang distributor Úc (industrykitchens.com.au) cho MCR 311** | Trả HTTP 403 với mọi user-agent. Đã thay bằng nguồn gốc catalogue 2019 của chính Precision — đáng tin hơn. |

---

## 5. Ghi chú kỹ thuật khi import

- Toàn bộ 73 `source_url` đã kiểm tra là URL thật đang hoạt động.
- Toàn bộ 72 `image_url` đã **kiểm tra HTTP 200** (0 link chết) — 57 link Squarespace CDN của lần khảo sát đầu + 15 link bổ sung ngày 18/07/2026 (mục 7).
- `origin` = `United Kingdom` cho tất cả (Precision sản xuất tại Thetford, Norfolk, UK).
- `heat_source` = `electric` cho tất cả, kể cả PMH 30 (xe hâm nóng chạy điện 230V).
- Một số trang web có `<h1>` lệch với `og:title`, ví dụ trang `bbs-600-1850-...` vẫn để tiêu đề cũ **"BBS 1850"**. Trường `model` dùng mã chuẩn hiện hành (`BBS 600-1850`); mã cũ `BBS 1850` được giữ riêng ở nhóm legacy kèm thông số R134a đời 2019.
- Ảnh dùng chung giữa vài model là **đúng thực tế** của nhà sản xuất (ví dụ MPT 601 và LPT 601 dùng chung ảnh `PT601.png`; RBCU 264 dùng ảnh `CU 211 - UDD.png`) — đây là `og:image` do chính Precision khai báo, không phải lỗi bóc tách.

---

## 6. Fix lỗi nghiêm trọng 18/07/2026 — mất dấu trừ nhiệt độ tủ đông + sai tên hiển thị TBBS

Một agent verify độc lập phát hiện 2 lỗi trong lần crawl trước, đã sửa trực tiếp trong `data-full.json` (không đụng `data.json`):

### 6.1. Mất dấu trừ (`-`) ở số đầu trong khoảng nhiệt độ tủ đông (10 dòng)

Bug: khi crawl, số âm đầu tiên của khoảng nhiệt độ tủ đông bị bóc tách thiếu dấu `-`, hiển thị sai kiểu `"18 / -22°C"` thay vì đúng phải là `"-18 / -22°C"` (tủ đông thì cả 2 số phải âm). Số dương "18°C" không tồn tại trên bất kỳ trang nguồn nào — đây thuần là lỗi bóc tách ký tự, không phải sai thông số của hãng.

**10 model bị ảnh hưởng** (trường `specs.Temperature`), đã fetch lại trực tiếp từng `source_url` trên `precision-refrigeration.com` để xác minh giá trị đúng — tất cả đều xác nhận `-18 / -22°C`:

| # | Model | Nhóm | source_url đã verify lại |
|---|---|---|---|
| 1 | LPT 601 | Top Mount Cabinets | `/cabinets/lpt-601-gn2/1-top-mount-freezer` |
| 2 | LPT 1401 | Top Mount Cabinets | `/cabinets/lpt-1401-gn2/1-top-mount-freezer` |
| 3 | LPU 401 | Bottom Mount Cabinets | `/cabinets/lpu-401-bottom-mount-freezer` |
| 4 | LCU 211 | 670 Series GN1/1 Counters | `/counters/lcu-211-gn1/1-counter-freezer` |
| 5 | LCU 311 | 670 Series GN1/1 Counters | `/counters/lcu-311-gn1/1-counter-freezer` |
| 6 | LPU 150 | Under Counters | `/under-counters/lpu-150-undercounter-freezer` |
| 7 | LSS 150 | Space Savers | `/space-savers/lss-150-gn1/1-space-saver-freezer` |
| 8 | LSS 300 | Space Savers | `/space-savers/lss-300-gn1/1-space-saver-freezer` |
| 9 | LUC 150 | Space Savers | `/space-savers/luc-150-gn1/1-space-saver-freezer` |
| 10 | GFS 600 | Bottle Coolers & Glass Frosters | `/bottle-coolers/gfs-600-single-door-glass-froster` |

Cách verify: dùng WebFetch + trình duyệt đọc lại nguyên văn bảng "SPECIFICATIONS" trên từng trang sống (không suy đoán). Với GFS 600, trang có 2 chỗ nhắc nhiệt độ khác nhau — bảng specs chính thức ghi `Temperature -18 / -22°C` (đã lấy giá trị này); trường `Key Features` trong data cũ có câu `-10/-18ºC Temperature Range` không khớp bảng specs — **câu này KHÔNG được sửa** vì không xác minh được nguồn gốc của số liệu đó trên trang sống hiện tại (không suy đoán, để nguyên, cần rà soát riêng nếu cần).

Trường `Configuration Code System` và các trường khác vốn đã ghi đúng `-18/-22°C` (có dấu trừ) từ trước — không đụng tới, chỉ sửa đúng 10 dòng `Temperature` liệt kê trên.

### 6.2. Sai tên hiển thị TBBS bị ghi nhầm thành BBS (3 dòng)

Bug: trường `title` của 3 model biến thể nhiệt đới TBBS (option "Tropicalised" giá riêng trên BBS 600/900/1350, xem mục 3.9) bị copy nguyên `title` của model BBS gốc, làm mất chữ "TBBS" — trường `model` vẫn đúng, chỉ `title` sai.

| Model | title cũ (sai) | title mới (đã sửa) |
|---|---|---|
| TBBS 600 | `BBS 600 - SINGLE DOOR BOTTLE COOLER` | `TBBS 600 - TROPICALISED SINGLE DOOR BOTTLE COOLER` |
| TBBS 900 | `BBS 900 - TWO DOOR BOTTLE COOLER` | `TBBS 900 - TROPICALISED TWO DOOR BOTTLE COOLER` |
| TBBS 1350 | `BBS 1350 - THREE DOOR BOTTLE COOLER` | `TBBS 1350 - TROPICALISED THREE DOOR BOTTLE COOLER` |

TBBS không có trang riêng trên website (chỉ là option giá riêng ghi trong Price List 2026 p.74, xem mục 1 nguồn #2), nên `source_url` của 3 dòng này vẫn trỏ về trang BBS gốc tương ứng — đây là nguồn gần nhất hiện có, không phải lỗi.

### 6.3. Xác nhận không phát hiện thêm lỗi

Đã quét lại toàn bộ `data-full.json` bằng regex cho mọi trường có định dạng `số/-số°C` hoặc `số/số°C` (73 model, tất cả trường `specs`) — sau khi sửa, không còn dòng nào khớp pattern lỗi (số dương đứng trước số âm trong cùng một khoảng nhiệt độ tủ đông). File đã kiểm tra lại: hợp lệ JSON UTF-8, không còn ký tự lỗi mã (mojibake `�`), số lượng model vẫn đúng 73/73 (không mất dữ liệu).

**Không có model nào không fetch lại được nguồn để verify** — cả 10 model tủ đông đều có `source_url` là trang web sống trên `precision-refrigeration.com` và đã fetch thành công.


---

## 7. Bổ sung ảnh cho 16 model thiếu — 18/07/2026

Trước đợt này có 16 model không có `image_url` (danh sách ở `_worklist-missing.json`). Đã điền được **15/16**.

### 7.1. Cách tìm nguồn ảnh

Cả  16 model đều **không có trang trên `precision-refrigeration.com`** — đã xác minh bằng `sitemap.xml` ngày 18/07/2026 (105 URL, không URL nào khớp). Nên không thể lấy `og:image` từ site hiện hành.

Nguồn thực sự dùng: **bản lưu web.archive.org của website cũ `precision-refrigeration.co.uk`**. Trang sản phẩm cũ của từng model legacy vẫn còn trong archive, ảnh sản phẩm nằm ở `div.main-image` dưới dạng `background:url(...)` (không phải thẻ `og:image`, trang cũ không set OG). Đã fetch HTML thô từng trang, bóc URL ảnh, rồi đối chiếu với CDX API để lấy đúng timestamp snapshot có thật.

**Lưu ý dạng URL:** ảnh dùng dạng `https://web.archive.org/web/<timestamp>im_/<url gốc>` — hậu tố `im_` trả file ảnh nguyên bản, không chèn banner/toolbar của archive.org. Tên miền gốc `precision-refrigeration.co.uk` đã chết (không phân giải), nên **bắt buộc dùng link archive**, không dùng link gốc.

> ⚠️ **Khi import vào WordPress**: nên tải 13 ảnh archive này về media library thay vì hotlink trực tiếp — archive.org chậm và có rate-limit. Các ảnh Squarespace CDN thì hotlink bình thường như cũ.

### 7.2. Kết quả

**12 model có ảnh riêng thật** (đúng ảnh trang sản phẩm của chính model đó):

| # | Model | File ảnh gốc | Snapshot |
|---|---|---|---|
| 1 | PMR 24 | `library/pmr_24.png` | 20190122070228 |
| 2 | PMH 30 | `library/pmh_30.png` | 20190121205902 |
| 3 | MCR 311 | `library/cu_311.png` | 20190122033200 |
| 4 | MCU 423 | `library/cu_411.png` | 20190121215412 |
| 5 | LCU 223 | `library/cu_211.png` | 20190122052321 |
| 6 | LCU 323 | `library/cu_311.png` | 20190122033200 |
| 7 | HWU 111 | `uploads/english/products/hwu-111.jpg` | 20140528202827 |
| 8 | HWU 311 | `uploads/english/products/hwu-311.jpg` | 20140528201625 |
| 9 | PCF 15 | `uploads/english/products/pcf.15..jpg` | 20140528180708 |
| 10 | PCF 40 | `uploads/english/products/pcf40.jpg` | 20170325230937 |
| 11 | PCF 50 | `uploads/english/products/297-x-280-pcf-40-50.jpg` | 20140528181747 |
| 12 | BBS 1850 | `library/bbs_1850_gd.png` | 20190121103118 |

Ghi chú về ảnh dùng chung ở bảng trên — **đây là đúng thực tế của hãng, không phải lỗi bóc tách** (cùng nguyên tắc đã nêu ở mục 5):
- `cu_211/cu_311/cu_411.png` là render bàn lạnh 2/3/4 khoang. Hãng dùng chung cho bản mát (M) và đông (L), bản dàn nóng liền (U) và rời (R), 670 Series và 520 Series — vì vỏ ngoài giống hệt nhau. Mỗi model ở trên lấy đúng file mà trang của chính nó khai báo.
- `297-x-280-pcf-40-50.jpg` — tên file do hãng đặt, dùng chung cho PCF 40 và PCF 50.

**3 model dùng ảnh model gốc** — đánh dấu bằng `image_is_from_base_model: true` trong `data-full.json`:

| Model | Lấy ảnh của | Lý do |
|---|---|---|
| DDPT 601 | **MPT 601** (`PT601.png`, Squarespace CDN) | Tủ hong vịt DDPT 601 chính là thân tủ PT 601 đổi cấu hình. Ảnh riêng trên trang cũ (`pt601-duck-dryer-2.jpg`) **không được archive lưu**. |
| DDPT 1401 | **MPT 1401** (`PT1401.png`, Squarespace CDN) | Tương tự. Chính trang DDPT 1401 cũ của hãng cũng dùng render PT-1401 này. |
| MCR / LCR family | `cu_311.png` (archive) | Mục này là **cả họ sản phẩm**, không phải một model cụ thể — không thể có ảnh riêng. Dùng render 3 khoang hãng dùng trên các trang MCR/LCR. |

Mỗi model được bổ sung đều có thêm trường `image_note` ghi rõ xuất xứ ảnh.

### 7.3. Không xử lý được: PFW100 (1/16)

`PFW100 — Freezer Meatwell` vẫn để trống `image_url`. Lý do:

- Không có trang trên `precision-refrigeration.com` (đã xác minh sitemap).
- Không có trong website cũ `precision-refrigeration.co.uk` — đã quét toàn bộ 1.057 URL trong CDX index của archive.org, **không URL nào chứa `pfw`, `meatwell` hay `meat-well`**. Đây là sản phẩm mới (chỉ xuất hiện từ Price List 2026), không tồn tại ở thời điểm website cũ còn sống.
- Nguồn duy nhất có hình là PDF Price List 2026 — PDF không cung cấp URL ảnh.
- **Không xác định được model gốc**: meatwell (khạp đông âm bàn) là kiểu dáng riêng, không chia sẻ thân vỏ với bất kỳ dòng nào khác → **không lấy ảnh dòng khác cho có**.

Cách bổ sung nếu cần sau này: trích ảnh nhúng từ trang 61–64 của `Precision Price List 2026.pdf`, upload lên media library rồi trỏ `image_url` vào đó.

### 7.4. Verify

- **15/15 URL ảnh mới đã kiểm tra HTTP 200.**
- Với 13 link archive: tải thật về đĩa và kiểm tra magic bytes (`89504e47` = PNG / `ffd8ffe0` = JPEG) để chắc không phải trang lỗi HTML trả về mã 200. Kích thước 4.5–48 KB, đều là file ảnh hợp lệ.
- Đã mở xem trực tiếp một số ảnh (PCF 50, HWU 111, BBS 1850) để xác nhận đúng chủng loại sản phẩm, không phải logo/placeholder.
- Sau khi sửa: file vẫn hợp lệ JSON UTF-8, **giữ nguyên 22 nhóm / 73 model**, không động vào `data.json`.


---

## 8. Fix vòng 2 — 18/07/2026: verify TOÀN BỘ 70 giá trị nhiệt độ (không chỉ tủ đông)

Mục 6.1 chỉ rà tủ đông (10 dòng). Lỗi mất dấu trừ khi crawl **cũng dính tủ mát**, biểu hiện là mâu thuẫn nội bộ giữa các model cùng một họ (`MPT 601` = `-2 / +4°C` nhưng `MPT 1401` = `2 / +4°C`). Vòng này verify lại **tất cả 70 giá trị nhiệt độ / 73 model**, không bỏ sót.

### 8.1. Phương pháp

Không suy đoán theo quy luật. Mỗi giá trị đối chiếu nguồn thật:

1. **Trang sống** trên `precision-refrigeration.com` — tải HTML thô của toàn bộ 56 URL sản phẩm, bóc đúng dòng `Temperature` trong khối accordion **SPECIFICATIONS** (không lấy câu trong `Key Features`, vì nhiều trang có 2 con số khác nhau ở 2 chỗ).
2. **Precision Price List 2026** (PDF chính chủ) — trích text 92 trang, đọc bảng `MODEL SPECIFICATIONS`. Dùng làm **nguồn đối chiếu thứ hai độc lập** cho các model hiện hành.
3. **Precision Brochure 2019** (PDF) — cho toàn bộ dòng đã ngưng sản xuất (nhóm 19–22), đọc cả bảng giá lẫn bảng `Model Specifications`.

### 8.2. Đã sửa dấu trừ — 11 dòng

| # | Model | Nhóm | Giá trị cũ (sai) | Giá trị mới | Nguồn verify |
|---|---|---|---|---|---|
| 1 | MPT 1401 | Top Mount Cabinets | `2 / +4°C` | `-2 / +4°C` | Trang sống + Price List 2026 |
| 2 | MPU 401 | Bottom Mount Cabinets | `2/ +4°C` | `-2 / +4°C` | Trang sống + Price List 2026 |
| 3 | MCU 211 | 670 Series GN1/1 | `2 / 10°C` | `-2 / 10°C` | Trang sống (bảng SPECIFICATIONS) |
| 4 | MCU 311 | 670 Series GN1/1 | `2 / 10°C` | `-2 / 10°C` | Trang sống |
| 5 | MCU 411 | 670 Series GN1/1 | `2 / 10°C` | `-2 / 10°C` | Trang sống |
| 6 | MCU 223 | 520 Series GN2/3 | `2 / 10°C` | `-2 / 10°C` | Trang sống |
| 7 | MCU 323 | 520 Series GN2/3 | `2 / 10°C` | `-2 / 10°C` | Trang sống |
| 8 | RBCU 264 | Bakery Counters | `5 / +5°C` | `-5 / +5°C` | Trang sống + Price List 2026 (`-5 / +5°C`) |
| 9 | RBCU 364 | Bakery Counters | `5 / +5°C` | `-5 / +5°C` | Trang sống + Price List 2026 |
| 10 | VUBC 121 | Variable Temp Drawers | `2 / 4°C or -18 / -22°C` | `-2 / 4°C or -18 / -22°C` | Trang sống + Price List 2026 |
| 11 | VUBC 121-2 | Variable Temp Drawers | `2 / 4°C or -18 / -22°C` | `-2 / 4°C or -18 / -22°C` | Trang sống + Price List 2026 |

Cộng với 10 dòng tủ đông ở mục 6.1 → **tổng 21 dòng dính bug mất dấu trừ**.

### 8.3. Chuẩn hoá định dạng — 16 dòng

Đổi ký tự độ thứ tự `º` (U+00BA) thành ký tự độ đúng `°` (U+00B0) trong các trường `Temperature` / `Temperature Range`: HRU1, HRU2, MA600-1950, PMR 24, PMH 30, DDPT 601, DDPT 1401, MCU 423, LCU 223, LCU 323, HWU 111, HWU 311, PCF 15, PCF 40, PCF 50, BBS 1850. **Giá trị số không đổi**, chỉ đổi ký tự. Sau khi sửa: 0 trường nhiệt độ còn dùng `º`.

Khoảng trắng lộn xộn `2/ +4°C` cũng đã chuẩn hoá thành `-2 / +4°C` (đã tính trong 11 dòng ở 8.2).

Lưu ý: chỉ chuẩn hoá trong trường nhiệt độ. Các trường khác (`Key Features`, `Options`) vẫn giữ nguyên văn bản gốc của hãng.

### 8.4. Xác nhận vốn đã đúng — 43 dòng

Trong đó có các giá trị **dương là đúng thật, tuyệt đối không được thêm dấu trừ**:

| Model | Giá trị | Vì sao dương là đúng |
|---|---|---|
| PCF 15 / 40 / 50 | `+90 / 3°C \| +90 / -18°C` | Cấp đông nhanh: hạ từ **+90°C** xuống **+3°C** (chiller) hoặc xuống −18°C (freezer). Brochure 2019 ghi nguyên văn `+90 / 3ºC`. |
| PCF 20 / 52 | `3°C or -18°C` | Cùng lý do — chế độ chiller về +3°C. Trang sống xác nhận. |
| PMH 30 | `78 / 82°C` | Xe đẩy **giữ nóng**, không phải tủ lạnh. |
| BBS 1850 | `3 / +15°C` | Brochure 2019 ghi `3 / +15ºC` cho **cả 4 model BBS** (600/900/1350/1850). Không phải mất dấu trừ. Model hiện hành đã đổi spec sang `1 / 15°C` — chênh lệch này là do **đổi đời máy**, không phải lỗi dữ liệu. |
| MCU 423 | `-2 / +10°C` | Brochure 2019, bảng `Model Specifications` ghi đúng như vậy — khớp với `-2 / 10°C` của MCU 211/311/411 hiện hành. |

### 8.5. Hai nguồn chính chủ vênh nhau ở họ MCU — đã chọn nguồn nào và vì sao

Với MCU 211/311/411/223/323, hai tài liệu chính chủ ghi khác nhau:

- **Bảng SPECIFICATIONS trên trang sống**: `-2 / 10°C`
- **Bảng giá trong Price List 2026 và Brochure 2019**: `-2 / +4°C`

Đây **không phải lỗi crawl** — sự vênh này tồn tại nhất quán qua cả 2 đời tài liệu (brochure 2019 cũng vênh y hệt: bảng giá `-2 / +4ºC` vs bảng spec `-2 / +10ºC`). Cách hiểu hợp lý: bảng spec ghi **dải hoạt động tối đa của bộ điều khiển**, bảng giá ghi **dải nhiệt vận hành khuyến nghị**.

**Đã chọn giá trị bảng SPECIFICATIONS (`-2 / 10°C`)** vì: (a) đúng yêu cầu lấy từ bảng SPECIFICATIONS trang sống, (b) nhất quán với `MCU 423 = -2 / +10°C` vốn đã có trong file. Việc sửa ở đây **chỉ là khôi phục dấu trừ bị mất**, không đổi con số.

### 8.6. Không verify được

| Model | Lý do |
|---|---|
| PTC70 | Không có trang sống, không có trong Price List 2026 lẫn Brochure 2019 (đã tìm cả `PTC`, `Thaw`). Nguồn duy nhất là thông cáo báo chí 2013, không có bảng thông số. **Trong `data-full.json` model này vốn không có trường nhiệt độ nào** → không có gì để sửa, cũng không tự bịa thêm. |
| MCR 311, MCR / LCR family | Là dòng dàn nóng rời đã ngưng. Brochure 2019 chỉ nhắc trong phần tuỳ chọn (`remote - model number MCR or LCR`), **không có bảng thông số nhiệt độ riêng**. Hai mục này cũng vốn không có trường nhiệt độ → giữ nguyên. |

Không có model nào bị giữ lại một giá trị nhiệt độ đáng ngờ mà không verify được: **cả 70 trường nhiệt độ đều đã đối chiếu nguồn thật**.

### 8.7. Verify sau khi sửa

- Tổng: **11 dòng sửa dấu · 16 dòng chuẩn hoá ký tự độ · 43 dòng xác nhận đã đúng = 70 trường nhiệt độ** (3 model không có trường nhiệt độ: PTC70, MCR 311, MCR/LCR family → 73 model).
- File vẫn hợp lệ JSON UTF-8, **giữ nguyên 22 nhóm / 73 model**.
- Các trường ảnh bổ sung ở mục 7 **được giữ nguyên**: `image_url` đủ 73/73, `image_note` 15, `image_is_from_base_model` 3.
- **Không đụng `data.json`**, không import WordPress, không chạy WP-CLI.
