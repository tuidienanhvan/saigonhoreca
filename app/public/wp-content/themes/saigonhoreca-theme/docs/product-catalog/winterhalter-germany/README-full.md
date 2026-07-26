# Winterhalter (Germany) — Catalog đầy đủ (`data-full.json`)

**Ngày khảo sát:** 18/07/2026 (phiên gốc, 7 nhóm) — mở rộng 19/07/2026 (thêm 3 nhóm)
**Brand string:** `Winterhalter-Germany`
**File dữ liệu:** `data-full.json` (10 nhóm · 54 model)

> ⚠️ File này **độc lập** với `data.json`/`import.csv`. Hai file đó là bản draft trước (13/07/2026, chỉ 3 model ở nhóm Utensil Washers) và **không bị đụng tới**.

> **Cập nhật 19/07/2026:** Mở rộng deep-crawl sang 3 nhóm mà phiên 18/07/2026 từng ghi "ngoài phạm vi" nhưng thực tế tồn tại công khai trên site: **Dosing Technology** (8 model), **Detergents & Rinse Aids** (9 model), **Wash Racks & Accessories** (7 model) — tổng 24 model mới, nâng từ 7→10 nhóm và 30→54 model. Đồng thời tìm lại được ảnh thật cho 12/17 model trước đây thiếu ảnh (GS 630, UF-M/L/XL, STF, VE 15/20, TE 15/20, AT Excellence-i/S/M) bằng cách fetch trực tiếp HTML từng trang sản phẩm (không dựa vào tóm tắt WebFetch) và verify từng ảnh bằng `curl -A "<Chrome UA>" -e "https://www.winterhalter.com/"`. Chi tiết ở mục 8 bên dưới.

> **Cập nhật 19/07/2026 (đợt 2):** Rà soát riêng 4 model Water Treatment còn thiếu ảnh (MonoMatik 3, DuoMatik 3, RoMatik 210, RoMatik 420). Xác nhận winterhalter.com không có trang con riêng cho từng model (chỉ trang danh mục chung). Tìm được ảnh sản phẩm thật + trang sản phẩm riêng cho **3/4 model** trên cửa hàng chính hãng `shop.winterhalter.com.au` (đã cập nhật `image_url`/`source_url` trong `data-full.json`); **RoMatik 210** vẫn không có ảnh chính hãng công khai ở bất kỳ site nào của Winterhalter — để trống theo đúng chủ trương không bịa. Chi tiết ở mục 4.

---

## 1. Nguồn dữ liệu

| # | Nguồn | Dùng cho |
|---|-------|----------|
| 1 | **https://www.winterhalter.com** (bản tiếng Anh, TYPO3) | Toàn bộ 7 nhóm — fetch trực tiếp từng trang sản phẩm sống ngày 18/07/2026: `/products/undercounter-warewashers/(uc-series\|u50)/`, `/products/passthrough-dishwasher/(pt-series\|pt-utensil)/`, `/products/utensil-washers/(gs-630\|uf-series)/`, `/products/multi-tank-rack-conveyor-dishwasher-mtr/`, `/products/ctr-compact-rack-conveyor-dishwasher/`, `/products/multi-tank-flight-type-dishwasher-mtf/`, `/products/conveyor-dishwashers/`, `/products/single-tank-flight-type-glasswasher-stf/`, `/products/water-treatment/`. |
| 2 | **`sitemap.xml` của winterhalter.com** | Xác nhận danh sách URL sản phẩm đầy đủ (không có trang con riêng cho từng model Water Treatment; không có trang nào cho UC Series theo từng size S/M/L/XL). |
| 3 | **catalog.winterhalter.com** | Đã thử lại — vẫn KHÔNG truy cập được nội dung chi tiết (cổng B2B/đại lý, không phải catalog sản phẩm công khai). |
| 4 | **https://www.winterhalter.com/products/chemicals/dosing-technology/** | Nhóm mới Dosing Technology (19/07/2026) — 1 trang tổng quan duy nhất, không có trang con riêng từng thiết bị. |
| 5 | **https://www.winterhalter.com/products/detergents-and-rinse-aids/** + 6 trang con (`/products/chemicals/{glasses,plates,cutlery,cups,utensils,reusable-plastic-cups}/`) | Nhóm mới Detergents & Rinse Aids (19/07/2026) — trang tổng quan không có tên sản phẩm, phải fetch từng trang con theo loại đồ để lấy tên thật (F-xxx/B-xxx). |
| 6 | **https://www.winterhalter.com/products/washing-racks/** + **https://www.winterhalter.com/products/wash-racks/rack-accessories/** | Nhóm mới Wash Racks & Accessories (19/07/2026). |

**Phương pháp (phiên 18/07):** WebFetch từng URL sản phẩm để bóc mô tả/thông số/ảnh; với ảnh, dùng thêm `curl -A "<Chrome UA>" -e "https://www.winterhalter.com/"` để xác nhận HTTP 200 (server trả 403 nếu thiếu User-Agent + Referer trình duyệt).

**Phương pháp (phiên mở rộng 19/07):** Với 3 nhóm mới, dùng WebFetch để lấy mô tả sản phẩm, sau đó **`curl` trực tiếp HTML thật** của từng trang (thay vì chỉ dựa vào tóm tắt WebFetch) và `grep` các đường dẫn `user_upload/...\.(png|jpg)` để tìm ảnh gốc (không phải bản `_processed_/csm_...` đã resize) — cách này cũng dùng lại để rà soát ảnh còn thiếu ở 7 nhóm cũ (xem mục 8). Mọi ảnh tìm được đều tải về và xem trực tiếp để xác nhận là ảnh sản phẩm thật (không phải icon/placeholder) trước khi đưa vào `data-full.json`.

---

## 2. Thống kê

| Chỉ số | Số lượng |
|---|---|
| Nhóm (`groups`) | **10** (7 gốc + 3 mở rộng 19/07) |
| Model | **54** (30 gốc + 24 mới) |
| Model có `image_url` thật (verify HTTP 200) | **49 / 54** |
| Model có `specs` (≥1 trường) | **54 / 54** |
| Model có `source_url` hoạt động | **54 / 54** |

### Phân bổ theo nhóm

| # | Nhóm (EN) | Nhóm (VI) | Model | Có ảnh |
|---|---|---|---|---|
| 1 | Undercounter Warewashers | Máy Rửa Chén Bát Đặt Gầm Bàn | 2 (UC Series, U50) | 2/2 |
| 2 | Passthrough / Hood-type Dishwashers | Máy Rửa Chén Bát Kiểu Vòm/Hood | 4 (PT-M, PT-L, PT-L Utensil, PT-XL Utensil) | 4/4 |
| 3 | Utensil Washers | Máy Rửa Xoong Nồi / Dụng Cụ Bếp | 4 (GS 630, UF-M, UF-L, UF-XL) | **4/4** (mới tìm được ảnh 19/07) |
| 4 | Rack Conveyor Dishwashers | Máy Rửa Băng Tải Kiểu Rổ | 5 (MTR S/M/L, CTR M/L) | 5/5 |
| 5 | Flight-Type Dishwashers | Máy Rửa Băng Tải Kiểu Flight | 3 (MTF M/L/XL) | 3/3 |
| 6 | Glasswashers | Máy Rửa Ly | 1 (STF) | **1/1** (mới tìm được ảnh 19/07) |
| 7 | Water Treatment | Thiết Bị Xử Lý Nước | 11 (MonoMatik 3, DuoMatik 3, VE 15/20, TE 15/20, AT Excellence-i/S/M, RoMatik 210/420) | **10/11** (tìm được ảnh cho VE 15/20, TE 15/20, AT Excellence-i/S/M ngày 19/07; MonoMatik 3, DuoMatik 3, RoMatik 420 tìm được ảnh chính hãng từ shop.winterhalter.com.au ngày 19/07 (đợt 2); còn thiếu duy nhất RoMatik 210 — xem mục 4) |
| 8 | **Dosing Technology** *(mới)* | Thiết Bị Định Lượng Hóa Chất | 8 (MPI, PPX, PPX-Light, SPI, SPX, DCX, Suction Tube, Suction Stick) | 8/8 |
| 9 | **Detergents & Rinse Aids** *(mới)* | Hóa Chất Tẩy Rửa & Trợ Xả | 9 (F 420 e, B 2 S, F 6200, B 100 N, F 720 BLUe, F 300, F 865, B 165 NE, B 170 XD) | 9/9 |
| 10 | **Wash Racks & Accessories** *(mới)* | Rổ/Khay Rửa & Phụ Kiện | 7 (Glass/Dish-Plate/Cutlery/Utensil/Bottle/Plastic Cup Racks, Rack Accessories) | 7/7 |

Trước phiên 18/07 (`data.json` 13/07/2026): 1 nhóm có dữ liệu (Utensil Washers, 3 model), 6 nhóm "models: 0". Sau phiên 18/07: cả 7 nhóm đều có dữ liệu thật, tổng 30 model. Sau phiên mở rộng 19/07/2026: 10 nhóm, 54 model, ảnh tăng từ 13→49, rồi 49→52 (đợt 2 cùng ngày, xem mục 4).

---

## 3. Vì sao nhiều model không có model number "cứng"

Winterhalter là hãng B2B đặt hàng theo cấu hình (tailor-made) ở nhiều dòng cao cấp:

- **UC Series**: trang web chỉ công bố 4 size S/M/L/XL và các biến thể ứng dụng (Glasswasher/Dishwasher/Bistro/Cutlery) — không có bảng model number với kích thước/thời gian chu trình riêng biệt trên web (chỉ trong brochure PDF tải về, chưa truy cập được qua WebFetch).
- **MTR/CTR/MTF**: trang web công bố *khoảng công suất* (range) theo size S/M/L/XL thay vì SKU cố định, vì máy được lắp ráp module theo chiều dài/số bồn yêu cầu của từng dự án. Trong `data-full.json`, các "model" MTR S/M/L, CTR M/L, MTF M/L/XL được tách theo đúng bảng size mà trang sản phẩm công bố (capacity, số bồn) — đây là cách chính hãng tự phân loại, không phải suy đoán thêm.

Đây **không phải do cào thiếu** — đã kiểm tra `sitemap.xml` và xác nhận không tồn tại URL sản phẩm chi tiết hơn.

---

## 4. Ảnh sản phẩm — có 52, thiếu 1 (đã cập nhật 19/07/2026, đợt 2)

**13 model có ảnh từ phiên 18/07/2026** (dùng `curl -A "<Chrome UA>" -e "https://www.winterhalter.com/"`, vì server trả 403 nếu không có User-Agent + Referer trình duyệt):

- UC Series, U50 — `.../NO-INDEX/U50/undercounter-dishwasher-(uc|u50)-winterhalter-int.png`
- PT-M, PT-L — dùng chung 1 ảnh lifestyle `pt-series-restaurant.jpg` (trang PT Series không có ảnh riêng từng size, đúng thực tế trang nguồn)
- PT-L Utensil, PT-XL Utensil — mỗi model có ảnh vận hành riêng (`pt-l-utensil.jpg`, `pt-xl-utensil.jpg`)
- MTR S/M/L — dùng chung 1 ảnh tổng quan dòng MTR (trang nguồn không tách ảnh theo size)
- CTR M/L — dùng chung 1 ảnh tổng quan dòng CTR
- MTF M/L/XL — dùng chung 1 ảnh tổng quan dòng MTF (`mtf-overview.png`)

**12 model tìm lại được ảnh trong phiên 19/07/2026** (phiên 18/07 kết luận nhầm là "không tồn tại" — nguyên nhân là WebFetch tóm tắt nội dung trang bằng AI phụ và bỏ sót các thẻ `<img srcset="...">` trỏ tới `fileadmin/user_upload/...`; phiên này `curl` HTML thật + `grep` đường dẫn ảnh trực tiếp thay vì dựa vào tóm tắt, sau đó tải từng ảnh về và xem trực tiếp để xác nhận là ảnh sản phẩm thật trước khi đưa vào catalog):

- **GS 630** — ảnh mặt trước máy thật (`utensil-washer-gs630-winterhalter-desktop-.png`), ngoài ra còn 3 ảnh cận cảnh khoang rửa (`-1.jpg` đến `-4.jpg`) chưa dùng.
- **UF-M** (`utensil-washer-uf-m-winterhalter.jpg`), **UF-L** (`utensil-washer-uf-l-winterhalter.jpg`), **UF-XL** (`utensil-washer-uf-xl-winterhalter.jpg`) — mỗi model 1 ảnh khoang rửa riêng, chụp rõ dụng cụ khác nhau (dao/nồi nhỏ cho M, nồi lớn/chảo cho XL).
- **STF** — ảnh toàn cảnh máy rửa ly kiểu băng tải thật (`single-tank-flight-type-glasswasher-stf-winterhalter.png`).
- **VE 15, VE 20, TE 15, TE 20** — dùng chung 1 ảnh thật chụp 4 cartridge có nhãn rõ "TE 15/TE 20/VE 15/VE 20" (`water-treatment-winterhalter-1.png`) — trang nguồn không tách ảnh riêng từng model nhưng ảnh này xác nhận đúng cả 4 tên model.
- **AT Excellence-i, AT Excellence-S, AT Excellence-M** — dùng chung 1 ảnh cận cảnh logo "AT Series" trên thân máy thật (`water-treatment-winterhalter-3.png`) — trang nguồn không phân biệt ảnh theo biến thể i/S/M.

**5 model ban đầu KHÔNG có ảnh trên chính winterhalter.com** — đã kiểm tra lại toàn bộ ảnh trên trang `/products/water-treatment/` (4 ảnh: 1=cartridge TE/VE, 2=lifestyle dao nĩa, 3=logo AT Series, 4=lifestyle ly nước) và xác nhận không có ảnh riêng cho MonoMatik 3, DuoMatik 3, RoMatik 210, RoMatik 420 (chỉ 2 ảnh lifestyle không liên quan trực tiếp).

**Đợt 2 (cùng ngày 19/07/2026)** — theo yêu cầu tìm trang sản phẩm riêng từng model (thử URL dạng `/products/water-treatment/monomatik-3/`, search `site:winterhalter.com`), xác nhận **winterhalter.com không có trang con riêng** cho 4 model này (chỉ có trang danh mục chung `/products/water-treatment/`). Tuy nhiên tìm được ảnh sản phẩm thật + trang sản phẩm riêng trên cửa hàng chính hãng **shop.winterhalter.com.au** (subdomain thương mại chính thức của Winterhalter, không phải đại lý bên thứ ba):

- **MonoMatik 3** — ảnh studio thật, nhãn "winterhalter MonoMatik 3" rõ trên thân máy. Trang: `https://shop.winterhalter.com.au/winterhalter-monomatik-3-water-softener`.
- **DuoMatik 3** — ảnh studio thật, nhãn "winterhalter DuoMatik 3" rõ trên thân máy. Trang: `https://shop.winterhalter.com.au/winterhalter-duomatik-3-water-softener`.
- **RoMatik 420** — ảnh studio thật, nhãn "winterhalter RoMatik 420" rõ trên thân máy. Trang: `https://shop.winterhalter.com.au/winterhalter-romatik-420-reverse-osmosis-for-str-mtr`.

Cả 3 ảnh đã được tải về và xem trực tiếp (không chỉ dựa vào tóm tắt WebFetch) để xác nhận là ảnh chụp sản phẩm thật, không phải icon/placeholder.

**1 model vẫn KHÔNG có ảnh: RoMatik 210** — đã kiểm tra kỹ:
- Không có trên winterhalter.com (chỉ ảnh icon minh hoạ chung).
- Không có trong catalog `shop.winterhalter.com.au` — trang `/water-treatment` của cửa hàng này chỉ bán/hiển thị RoMatik 420, không có RoMatik 210 (RoMatik 210 và 420 chỉ khác công suất nhưng cửa hàng AU chỉ niêm yết bản 420).
- Các cửa hàng khu vực khác của Winterhalter (store-mx/store-co/store-cl.winterhalter.com) bán dòng AT Excellence cho RO thay vì RoMatik, không có RoMatik 210/420.
- Ảnh RoMatik 210 chỉ tìm thấy trên các trang đại lý bên thứ ba (gastrodax.de, hospitalityhub.com.au, amazon.de...) — không dùng vì không xác định được nguồn/bản quyền chính hãng.

→ Để trống `image_url` cho RoMatik 210, giữ nguyên `source_url` là trang danh mục `/products/water-treatment/` (không có trang riêng chính hãng nào chính xác hơn để trỏ tới).

Không còn model nào thiếu ảnh ở 4 nhóm Utensil Washers, Glasswashers, và nay gần hết Water Treatment (10/11, chỉ còn RoMatik 210).

---

## 5. Meta description

Giống pattern đã ghi nhận ở Precision-UK: phần lớn trang sản phẩm của Winterhalter **không set** thẻ `<meta name="description">` riêng — để trống `seo_description` cho toàn bộ 54 model theo đúng dữ liệu thật, không suy đoán. `seo_title` lấy từ `<title>`/H1 thật của từng trang khi có; một số trang không lộ title rõ ràng qua WebFetch (MTF, MTR, water-treatment con, chemicals con) nên dùng title trang overview gần nhất.

---

## 6. Những gì KHÔNG bao gồm — và lý do (đã cập nhật 19/07/2026)

| Không có | Lý do |
|---|---|
| **catalog.winterhalter.com** | Thử lại ngày 18/07/2026 — vẫn không lấy được nội dung chi tiết, có vẻ là cổng B2B/đại lý cần đăng nhập riêng, không phải catalog sản phẩm công khai. Không thử lại trong phiên 19/07 vì không phải trọng tâm mở rộng. |
| **Kích thước/chu trình chi tiết UC Series theo từng size** | Chỉ có trong brochure PDF tải về, WebFetch không truy cập được nội dung PDF. |
| **Giá bán** | Winterhalter không công khai giá trên site .com chính cho bất kỳ nhóm nào (máy rửa, hoá chất, dosing, rổ). Cần liên hệ đại lý/nhà phân phối Việt Nam. |
| **Ảnh cho RoMatik 210** | Xem mục 4 — không có ảnh chính hãng công khai trên winterhalter.com lẫn shop.winterhalter.com.au (chỉ RoMatik 420 có bán/có ảnh ở đó); ảnh chỉ tồn tại trên trang đại lý bên thứ ba, không dùng. |
| **Dung tích đóng gói/SKU chính xác của hoá chất F-xxx/B-xxx** | Trang sản phẩm chỉ công bố tên + mô tả công dụng, không công bố dung tích can/SKU. |
| **Kích thước/số ngăn/SKU cụ thể của rổ/khay rửa và phụ kiện** | Trang chỉ phân loại theo ứng dụng (glasses/dishes/cutlery/...), không có bảng SKU riêng từng loại. |

---

## 7. Đối chiếu với `data.json` cũ (13/07/2026)

- `data.json` liệt kê **series/dòng** (UC Series, PT Series, MTR, CTR, MTF, STF) nhưng chỉ có model number thật cho nhóm Utensil Washers (GS 630, PT Utensil, UF-Series — 3 "model", trong đó UF-Series gộp chung 3 size).
- `data-full.json` (file này) **tách UF-Series thành 3 model con thật** (UF-M/UF-L/UF-XL, mỗi model có kích thước riêng công bố trên trang `/products/utensil-washers/uf-series/`) và bổ sung model number/size thật cho 6 nhóm còn lại theo đúng cách chính hãng tự phân loại trên web (xem mục 3).
- Không phát hiện mâu thuẫn số liệu giữa 2 phiên nghiên cứu (cùng nguồn `winterhalter.com`, cách nhau 5 ngày) — các con số công suất/kích thước khớp nhau ở những model trùng (U50: 30 rổ/giờ; PT Series: 77 rổ/giờ; MTR: 55–355 rổ/giờ; CTR: 50–195 rổ/giờ; MTF: 4,500–8,000 đĩa/giờ; STF: 2,800 cốc/giờ).

**Vẫn cần đối chiếu DB thật** (1 sản phẩm Winterhalter hiện có trên site saigonhoreca) với 54 model ở trên trước khi tạo sản phẩm mới, để tránh trùng lặp — việc này nằm ngoài phạm vi crawl web.

---

## 8. Mở rộng 19/07/2026 — 3 nhóm mới

Phiên 18/07/2026 xếp 3 nhóm sau vào mục "không bao gồm" vì lý do "ngoài phạm vi 7 nhóm được yêu cầu" — không phải vì không tồn tại. Phiên này cào đầy đủ cả 3, theo đúng quy trình WebFetch + curl-verify ảnh đã dùng cho 7 nhóm gốc.

### 8.1 Dosing Technology (8 model)
Nguồn: 1 trang duy nhất `/products/chemicals/dosing-technology/` — trang phân loại thiết bị theo bảng (dosing lỏng: MPI/PPX/PPX-Light; dosing rắn: SPI/SPX; điều khiển: DCX; phụ kiện hút: Suction Tube/Suction Stick). Không có trang con riêng từng thiết bị — đã kiểm tra, đây là cấu trúc thật của trang. Cả 8 model đều có ảnh thật (verify HTTP 200).

### 8.2 Detergents & Rinse Aids (9 model)
Nguồn: trang tổng quan `/products/detergents-and-rinse-aids/` (không có tên sản phẩm) + 6 trang con theo ứng dụng (`glasses/plates/cutlery/cups/utensils/reusable-plastic-cups`). Tên sản phẩm thật theo hệ đặt tên chính hãng: dòng **F-xxx** = chất tẩy rửa (detergent), dòng **B-xxx** = chất trợ xả (rinse aid). 9 sản phẩm riêng biệt: F 420 e, B 2 S, F 6200, B 100 N, F 720 BLUe, F 300, F 865, B 165 NE, B 170 XD. Lưu ý F 420 e và B 100 N xuất hiện ở nhiều trang ứng dụng khác nhau (đúng thực tế 1 sản phẩm dùng đa năng, không phải trùng lặp). Cả 9 đều có ảnh thật.

### 8.3 Wash Racks & Accessories (7 model)
Nguồn: `/products/washing-racks/` (6 loại rổ theo ứng dụng: Glass/Dish-Plate/Cutlery/Utensil/Bottle/Plastic Cup Racks) + `/products/wash-racks/rack-accessories/` (phụ kiện chung, gộp thành 1 "model" Rack Accessories vì trang không tách SKU riêng). Cả 7 đều có ảnh thật.

### 8.4 Vì sao phiên trước bỏ sót ảnh của 12 model
Phiên 18/07/2026 dùng WebFetch (tóm tắt nội dung qua model AI phụ) để đọc trang sản phẩm, và mô hình tóm tắt đã kết luận nhầm là "chỉ có SVG icon" cho GS 630/UF-Series/STF/Water Treatment. Phiên 19/07 kiểm tra lại bằng cách `curl` HTML thật của từng trang và `grep` trực tiếp các thẻ ảnh (`user_upload/.../\.(png|jpg)`), phát hiện các trang này **thật ra có ảnh sản phẩm chụp thật** (không phải SVG) mà WebFetch đã bỏ sót khi tóm tắt. Đã tải và xem trực tiếp từng ảnh để xác nhận trước khi cập nhật `data-full.json`.
