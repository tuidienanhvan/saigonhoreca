# Kolb — Catalog ĐẦY ĐỦ (deep-crawl toàn dòng sản phẩm) — 19/07/2026 (cập nhật verify-pass #2)

File dữ liệu: `data-full.json` (KHÔNG đụng tới `data.json` cũ — bản cũ khớp với các product
đã import nháp).

## Verify-pass 19/07/2026 (bổ sung) — 4 model ảnh 404, KHÔNG khôi phục được trong phiên này
4 model sau dùng ảnh lấy từ Wayback Machine snapshot của trang nhà máy (xem mục "Nguồn dữ
liệu" #2) — ảnh gốc trên `kolbcn.com` đã bị gỡ (site đã redesign, xoá hẳn thư mục
`/upload_img/...` cũ, trả 404 cả ảnh lẫn trang chi tiết còn sống):
- **K51-6601D, K51-4501DF, K51-5001D** (nhóm Fryer, hợp tác Giles, đã ngừng niêm yết) — dùng
  chung 1 ảnh cũ `20200810_125014_0000021.png` → nay 404.
- **Atollspeed Power (K02-3006TIS)** — ảnh cũ `20210422_105220_0000029.png` → nay 404.

**Đã thử khôi phục nhưng KHÔNG thành công trong phiên này:**
1. Fetch lại trực tiếp 2 URL snapshot Wayback (`id 261` cho Fryer, `id 308` cho Atollspeed
   Power) đã ghi trong `source_url` — **archive.org bị sập toàn cục** tại thời điểm chạy phiên
   (trang chủ + playback đều trả 500/503 "Internet Archive services are temporarily offline",
   xác nhận qua nhiều lần thử lại cách nhau vài chục giây trong hơn 10 phút, kể cả sau khi
   trang chủ archive.org đã hồi phục (200) thì riêng dịch vụ playback `web.archive.org/web/...`
   vẫn tiếp tục 500/503).
2. Fetch trực tiếp `kolbcn.com/html/en/product-detail/2020/07/22/261.html` và
   `.../2020/08/24/308.html` (live, không qua Wayback) — cả 2 đều 404. Trang danh mục hiện tại
   (`products-cid-168` đến `175`, danh sách link trên `atollspeed-solution.html`) xác nhận
   **KHÔNG còn liệt kê Fryer (id 261) hay Atollspeed Power (id 308)** trong menu/điều hướng
   sống của site — khớp với ghi chú "delisted"/"chỉ bán tại Trung Quốc" đã có sẵn trong data.
3. Tra chéo các đại lý khu vực đã dùng làm nguồn cho các model khác trong file
   (alliedmetals.com/product-tag/kolb — 12 sản phẩm Kolb liệt kê, KHÔNG có Fryer/Atollspeed
   Power; alliedfse.com; dksh.com/kolb-atollspeed — xác nhận dòng Atollspeed phân phối khu vực
   "chỉ có 3 size", không gồm Power; examasjaya.com; gastro.co.id) — không đại lý nào bán/có
   ảnh cho 4 model này.
4. Thử catalogue PDF marketing của MCE Marketing (`Kolb-Catalogue.pdf`,
   `mcemarketing.com/product/kolb-equipment/`) — file là ảnh scan (không có text layer, môi
   trường phiên này không có công cụ render PDF sang ảnh để đọc thủ công), không xác minh được
   nội dung có ảnh Fryer/Atollspeed Power hay không.

**Kết luận:** Đã set `image_url = ""` cho cả 4 model thay vì giữ URL cũ đã hỏng (tránh ảnh vỡ
trên site) hoặc bịa ảnh mới. `source_url` (2 URL Wayback) giữ nguyên vì bản thân trang đó vẫn
là nguồn dữ liệu specs/mô tả hợp lệ, chỉ tạm thời không truy cập được do archive.org sập —
**nên thử lại fetch 2 URL Wayback này ở phiên sau** để khôi phục ảnh khi archive.org hoạt động
lại bình thường:
- Fryer: `https://web.archive.org/web/20250809021131/https://www.kolbcn.com/html/en/product-detail/2020/07/22/261.html`
- Atollspeed Power: `https://web.archive.org/web/20250712121748/https://www.kolbcn.com/html/en/product-detail/2020/08/24/308.html`

## Verify-pass 19/07/2026 — audit Google lần 2, đảo ngược 1 kết luận của lần 1
Audit lần 2 tra lại 3 model mà lần 1 (18/07/2026) đã kết luận "không xác nhận được" hoặc để
sẵn trong data:
1. **Atollspeed 400T — ĐẢO NGƯỢC kết luận lần 1 (KHÔNG bịa số liệu).** Lần 1 chỉ tra
   examasjaya.com và 1 PDF specsheet lỗi ghép nhãn nên kết luận "không đủ căn cứ độc lập". Lần
   2 tìm thêm được trang sản phẩm CÓ bảng thông số riêng của **TTG Engineering (Việt Nam)**
   tại `https://ttgen.vn/products/microwave-and-convection-oven-atollspeed-300t-400t` — trang
   này liệt kê rõ mã máy `K02-4001T1S` cho bản 400T, kích thước ngoài 700x730x445mm, khoang
   nướng 450x350x200mm, điện áp 380-400V/3P/50Hz, công suất 4.9kW, trọng lượng 94kg — số liệu
   TÁCH BIỆT với 300T (không trùng với K02-3001TIS/K02-3003TIS như PDF lỗi ở lần 1). Đã thêm
   model mới **"Atollspeed 400T (K02-4001T1S)"** vào nhóm "Atollspeed — Speed Ovens", nguồn
   `source_url` = trang TTG Engineering, ảnh lấy từ chính trang đó
   (`product.hstatic.net/1000157133/product/400t_large.png`). Model cũng được bán qua Examas
   Jaya (Malaysia), DKSH (Thái Lan), Femac (Singapore) theo tìm kiếm Google — ghi chú thêm
   trong field `ghi_chu` của model, không đưa các nguồn này làm `source_url` chính vì không có
   bảng thông số riêng độc lập bằng TTG.
2. **Atoll 8008/8008T — ĐÃ CÓ SẴN trong data từ trước (không phải bổ sung mới).** Audit lần 1
   (18/07/2026, xem log bên dưới) đã xác nhận và thêm model "Atoll 8008 / 8008T
   (K03-86X8D00RK / E00RK)" vào nhóm Convection Oven Atoll, nguồn Wayback snapshot của
   `kolbcn.com/html/en/product-detail/2020/07/21/236.html` (id 236). Audit lần 2 fetch lại
   chính URL 236.html trực tiếp (không qua Wayback) để đối chiếu — trang sống trả về lỗi 404 ở
   thời điểm 19/07/2026 (trang đã bị gỡ khỏi site, đúng như lần 1 ghi nhận), xác nhận bản
   Wayback đang lưu trong data vẫn là nguồn hợp lệ duy nhất. Không có thay đổi số liệu.
3. **Atoll 900 — ĐÃ CÓ SẴN trong data từ trước (không phải bổ sung mới).** Audit lần 1 đã thêm
   model "Atoll 900 / 900s / 900L" vào nhóm Convection Oven Atoll, nguồn
   `kolbcn.com/html/en/product-detail/2024/03/14/320.html`. Audit lần 2 fetch lại chính URL này
   — trang còn sống, xác nhận lại đúng thông số đã ghi (Atoll900s: 5 khay 600x400mm,
   930x1026x700mm, 8.1kW, 12.5A/380V; Atoll900L: 10 khay, 930x1026x1100mm, 16.2kW, 25A/380V).
   Không có thay đổi số liệu.

## Verify-pass 18/07/2026 — sửa theo yêu cầu audit
1. **3 model nghi thiếu — kết quả tra lại tận nguồn:**
   - **K12-RO68D4** — XÁC NHẬN THẬT, đã thêm vào nhóm "Retarder Proofer" (Roll-in Retarder
     Proofer 4T). Nguồn: `https://www.gastro.co.id/product/kolb-k12-ro68d4` (kiểm tra trực
     tiếp `<title>`/`og:image`/meta description, không phải AI tóm tắt). Trang nhà máy
     kolbcn.com (`product-detail/2020/07/21/237.html`) hiện chỉ liệt kê D1-1 và D2-2, KHÔNG
     có D4 → distributor SKU riêng, specs chi tiết (kích thước/công suất) KHÔNG được Gastro
     công bố công khai nên để nguyên "không bịa" như style Gastro khác trong file.
   - **Atollspeed 400T** — (KẾT LUẬN NÀY ĐÃ BỊ ĐẢO NGƯỢC ở audit 19/07/2026, xem mục
     "Verify-pass 19/07/2026" ở trên — đã tìm thấy nguồn TTG Engineering VN có bảng thông số
     riêng và model đã được thêm vào data). Ghi chú gốc của lần 1: KHÔNG xác nhận được. Trang
     nhà máy kolbcn.com/products-cid-168.html
     chỉ có 300H, 400H, Easy, 300T (KHÔNG có 400T). Nhà phân phối Examas Jaya (Malaysia) có
     trang marketing nhắc tên "300T/400T" và 1 PDF specsheet
     (`kolb-300t_400t...pdf`, lưu Wayback `20260422020543`) nhưng bảng thông số trong PDF đó
     CHỈ có 1 model code `K02-3003TIS` (chính là mã của Atollspeed 300H đã có trong data, khác
     `K02-3001TIS` là mã thật của 300T) — tức tờ rơi ghép nhãn "300T/400T" nhưng dùng nhầm
     bảng specs của 300H, không có số liệu riêng cho 400T. Không đủ căn cứ độc lập →
     KHÔNG thêm vào data để tránh bịa.
   - **Atoll 8010** — KHÔNG tìm thấy ở bất kỳ nguồn nào (kolbcn.com chỉ có Atoll 600/600T,
     800/800T, 8008/8008T; catalogue PDF cũ kolb-hk.com/.../Convection_Oven.pdf — đọc được
     text, không phải ảnh scan như từng ghi nhầm trước đó — chỉ có Atoll 300H/300T/600/800;
     gastro.co.id không có sản phẩm Atoll nào dưới brand Kolb; Wayback CDX search cho
     `kolbcn.com/*8010*` = 0 kết quả). Có thể là số hiệu không tồn tại hoặc nhầm với
     "Atoll 8008" → KHÔNG thêm vào data.
2. **Chuỗi rác placeholder:** tìm thấy đúng 2 chỗ — `seo_description` của "Atollspeed 300H
   (K02-3003TIS)" (`"tset-Kolb Huizhou Ltd."`) và của gói "KSS-010G"
   (`"asfs-Kolb Huizhou Ltd."`). Đối chiếu lại `source_url` của cả hai (trang nhà máy
   kolbcn.com) bằng curl trực tiếp: cả hai trang đều KHÔNG có thẻ `<meta name="description">`
   → sửa về `""` cho đúng quy ước đã ghi ở mục "Ghi chú kỹ thuật khi parse" (seo_description
   trống ở nguồn thì để trống, không bịa).
3. **Laguna (base line) — specs bị lệch/cắt:** đối chiếu lại HTML gốc
   `product-detail/2020/07/15/190.html` (và Laguna II qua Wayback snapshot cùng bảng y hệt).
   Lỗi cũ: dòng đầu bảng kích thước "600 x 400" bị gộp nhầm vào key tiêu đề cột, và thiếu hẳn
   dòng chiều cao khoang nướng thứ 2 (site có 2 tuỳ chọn "170/325" VÀ "220/365", bản cũ chỉ
   giữ 1). Đã sửa lại đúng theo bảng `product_techinfo_table_cenglu_img` gốc: 6 kích thước
   khoang (600x400 → 1200x1200) mỗi cái tách key riêng với công suất có-hơi/không-hơi, và 2
   tuỳ chọn chiều cao khoang nướng. Mô tả sản phẩm (`description_en`) đối chiếu HTML gốc thấy
   đúng là site viết thiếu chữ "Laguna" ở đầu câu ("...`<div class='Title'>Deck Oven
   Laguna</div><div class='Description'>from Kolb is one of...`") — đây là lỗi/quirk của
   chính trang nhà máy, KHÔNG phải lỗi crawl, nên giữ nguyên. "Sliver，Black" (thay vì
   "Silver") cũng là chính tả gốc trên site, giữ nguyên không tự ý sửa.

## Kolb là hãng nào
**KOLB — thiết bị làm bánh/pizza chuyên nghiệp, gốc Thụy Sĩ (1935).**
- Do Ernst Kolb (thợ bánh bậc thầy Thụy Sĩ) sáng lập năm 1935; mở rộng sang châu Á (Kolb Hong
  Kong 1985, **Kolb Huizhou** 2005 — trung tâm R&D + nhà máy chính ở Quảng Đông, Trung Quốc);
  năm 2017 gia nhập gia đình **Wiesheu** (công nghệ lò nướng Đức).
- Website nhà sản xuất: https://www.kolbcn.com/html/en/index.html
- Xuất xứ ghi trong data: "China (Kolb Huizhou; Swiss design)".

## Nguồn dữ liệu (theo thứ tự ưu tiên)
1. **kolbcn.com — nhà sản xuất (CHÍNH).** Toàn bộ mô tả, thông số kỹ thuật (bảng Technical
   Data render sẵn trong HTML), ảnh sản phẩm (`ProductImageShow` / `og:image`), `seo_title`
   (`<title>`) và `seo_description` (`meta description`) lấy trực tiếp từ trang từng sản phẩm.
   Danh sách 17 gói KBS lấy qua chính endpoint AJAX của website
   (`Ajax.GetCustomSolutionList.php`, quét mọi tổ hợp thẻ s1e–s4e).
2. **Wayback Machine (archive.org).** 5 trang đã bị gỡ khỏi menu nhưng vẫn còn bản lưu, được
   phục hồi và ghi rõ URL snapshot trong `source_url`:
   - Atoll 800/800T (id 234) — K03-86X4D00RK / E00RK / D00PK
   - Atoll 8008/8008T (id 236) — K03-86X8D00RK / E00RK
   - Fryer (id 261, hợp tác Giles USA) — K51-6601D / K51-4501DF / K51-5001D
   - Laguna II (id 306)
   - Atollspeed Power (id 308) — K02-3006TIS  *(bonus, ngoài worklist)*
3. **Nhà phân phối** cho các cấu hình/SKU không có trang riêng trên site nhà máy:
   alliedmetals.com & alliedfse.com (Singapore), gastro.co.id (Indonesia),
   binhminhgroups.com.vn (Việt Nam). Ví dụ: K01-0604D2P0K, K01-0604D3F0K, K03-86X4EPHRK,
   K130PG-0604110 (bake-off), K45-0121AS4 (máy cắt), K42-0221AA5, các SKU K04/K05 của Gastro,
   size mixer K31-0801AB4 / K32-0201AB4.

## Số liệu thực đã ghi
- **Nhóm (categories): 21**
- **Models: 92** (90 gốc + K12-RO68D4 bổ sung 18/07/2026 + Atollspeed 400T bổ sung
  19/07/2026) — tất cả đều có `source_url` chạy được, `image_url` là ảnh thật, và `specs`
  không rỗng.
- **Ảnh: 64 URL ảnh riêng biệt / 92 model** (~70%; giảm từ 65 sau khi 4 model — K51-6601D,
  K51-4501DF, K51-5001D, Atollspeed Power — bị set về rỗng do ảnh nguồn 404 vĩnh viễn, xem mục
  "Verify-pass 19/07/2026 (bổ sung)" ở trên), KHÔNG phải 100% như từng ghi nhầm. Nhiều dòng
  dùng chung 1 ảnh vì bản thân trang nhà máy dùng chung ảnh minh hoạ cho các biến thể cùng dòng
  (VD các cấu hình cùng 1 sản phẩm chỉ khác kích thước/option) — đây là thực tế của nguồn,
  không phải lỗi crawl, nên không đi tìm ảnh riêng giả cho từng SKU.

| Nhóm (VI) | Số model |
|---|---|
| Lò tầng (Deck Oven) Laguna | 5 |
| Lò đối lưu (Convection Oven) Atoll | 5 |
| Lò tốc độ Atollspeed (Speed Oven) | 6 |
| Lò xoay giá (Rotary Oven) Tornado | 4 |
| Lò nướng Pizza | 6 |
| Tủ ủ bột (Proofer) | 6 |
| Tủ ủ hoãn (Retarder Proofer) | 7 |
| Máy trộn bột xoắn (Spiral Mixer) | 5 |
| Máy trộn bột hành tinh (Planetary Mixer) | 6 |
| Máy cán bột (Dough Sheeter) dòng Kolb | 2 |
| Máy cán bột dòng 'R' (hợp tác RONDO) | 2 |
| Máy làm lạnh nước (Water Cooler) | 3 |
| Máy tạo hình bánh dài (Long Moulder) | 1 |
| Máy chia bột (Dough Divider) | 1 |
| Máy chia bánh nhỏ (Bun Divider) | 3 |
| Máy giữ ấm/ủ sô-cô-la (Chocolate Warmer) | 5 |
| Bếp chiên (Fryer) — hợp tác Giles (đã ngừng niêm yết) | 3 |
| Máy cắt bánh mì (Bread Slicer) | 1 |
| Lò kết hợp / bake-off (dòng Wiesheu) | 1 |
| Giải pháp Bếp Mở KOLB (gói KBS) | 17 |
| Giải pháp Atollspeed (gói KSS) | 3 |

## Đã bao phủ (so với các dòng "trước đây bỏ sót")
- TOÀN BỘ dòng Deck Oven Laguna (Laguna base + Easy + Laguna II + 2 cấu hình D2P0K/D3F0K).
- TOÀN BỘ dòng Convection Oven Atoll: 900/900s/900L, 600/600T, 800/800T, 8008/8008T,
  cùng Atoll 800 + proofer (K03-86X4EPHRK).
- TOÀN BỘ sub-brand Atollspeed: 300H / 400H / 300T / 400T / Easy (+ Power bonus).
- Tornado biến thể "-S" từ nhà máy (K05-0608T1HS / K05-0604T1HS) + SKU non-S của Gastro.
- Pizza factory (K04-9879DS-1 / D1-1 / 9779D2-1) + SKU K04-9879D1/D2/D3 của Gastro.
- Climate Unit K11-CU01D, Roll-in Proofer tới D6, Retarder Proofer đầy đủ (gồm -18°C).
- Dòng 'R' RONDO, Water Cooler, Chocolate Warmer, Bun Divider (thêm K42-0221AA5),
  Bread Slicer, Fryer (Giles), Bake-off K130PG-0604110.
- CẢ 17 gói KBS và CẢ 3 gói KSS.

## KHÔNG bao gồm & lý do
- **Giá bán**: hãng/nhà phân phối không công bố công khai.
- **PDF datasheet đầy đủ**: nằm trong "Download Center" của kolbcn.com, yêu cầu đăng nhập
  hội viên → không tải/nhúng.
- **Các mã cấu hình Laguna chỉ xuất hiện trong tên combo của đại lý** (K01-0604D1FH,
  K01-0806D3FH/D10H, K01-1208D3FHK/D4FHK, K01-0604D10H…): đây là mã HOÁ cấu hình của cùng
  dòng Deck Oven Laguna (số tầng + kích thước khoang + kiểu điều khiển/steam). Thông số của
  chúng nằm trong ma trận kích thước–công suất của trang Laguna gốc, KHÔNG có trang sản phẩm
  độc lập → không tách thành model riêng để tránh bịa thông số. Hai cấu hình có trang thật của
  Allied Metals (D2P0K, D3F0K) thì ĐÃ đưa vào.
- **Atoll 300H (catalogue HK cũ)** và **các SKU Hong Kong GC-1022T / GC-1179T / GC-1034
  (K45-0120AS4)**: trang thiết bị kolb-hk.com trả 404, file PDF là ảnh scan (không trích được
  text) → không có nguồn HTML/spec đáng tin để ghi, bỏ qua thay vì bịa.
- **examasjaya.com/kolb (Malaysia)**: chỉ là trang thương hiệu một trang, không có trang sản
  phẩm riêng lẻ → không bổ sung được model nào.

## Ghi chú kỹ thuật khi parse
- Nhiều trang nhà máy gộp 2–4 model trong một bảng Technical Data (cột theo mã model). Bộ
  parser tách đúng cột theo hàng "Product Model"; các hàng dùng `colspan` (áp dụng cho mọi
  model) được giữ nguyên cho tất cả biến thể.
- `seo_description` một số trang nhà máy để trống ở thẻ meta → khi đó dùng chính mô tả sản
  phẩm trên trang; không bịa.
