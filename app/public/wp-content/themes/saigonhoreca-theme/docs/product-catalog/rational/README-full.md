# RATIONAL (Đức) — Catalog đầy đủ (`data-full.json`)

**Ngày khảo sát:** 19/07/2026 (crawl lần 1) + 19/07/2026 bổ sung lần 2 + 19/07/2026 bổ sung lần 3 (cùng ngày)
**Brand string:** `RATIONAL`
**File dữ liệu:** `data-full.json` (8 nhóm · **113 mục** sản phẩm/phụ kiện — tăng từ 26 → 105 → **113** qua 3 lần bổ sung)

> ⚠️ File này **độc lập** với `data.json`/`import.csv`. Hai file đó là bản đã import làm draft từ trước (nghiên cứu 14/07/2026) và **không bị đụng tới** trong phiên này.

## 0.0 Bổ sung lần 4 (19/07/2026) — rà soát ảnh cho 3 model báo thiếu

Theo yêu cầu review, đã kiểm tra lại 3 mục bị đánh dấu thiếu ảnh:

1. **CombiMaster Plus 202 (20-2/1)** — ĐÃ TÌM ĐƯỢC ẢNH THẬT. `source_url` cũ (manualslib.com) chỉ xác nhận sự tồn tại của model, không phải trang chính hãng. Đã tìm đại lý ủy quyền RATIONAL khác — **grydle-sync.com** (SKU `CMP 202 E`, mã hãng `B229106.12.202`) — có ảnh sản phẩm thật (`https://grydle-sync.com/wp-content/uploads/2019/11/1456607.jpg`, đã kiểm tra HTTP 200/image-jpeg) và specs khớp: 300-500 suất ăn/ngày, nạp khay 2/1 & 1/1 GN (đúng size 20-2/1). Đã cập nhật `image_url` + `source_url` + `seo_description` trong `data-full.json`.
2. **Flue gas collector** (`abgassammler.php`) và **WiFi-Stick 5 GHz** (`wifi-stick-5-ghz.php`) — ĐÃ FETCH LẠI trực tiếp cả trang chi tiết (curl với User-Agent trình duyệt thật) lẫn biến JS `accessory.allData` trên trang `/accessories/` — xác nhận KHÔNG có bất kỳ ảnh sản phẩm nào (chỉ có `og:image` là logo RATIONAL chung, không phải ảnh sản phẩm thật). Giữ nguyên `image_url` rỗng, không bịa ảnh — đúng như ghi chú `specs.Ảnh` đã có sẵn từ lần 2.

## 0.1 Bổ sung lần 3 (cùng ngày 19/07/2026) — 2 dòng bị audit Google phát hiện thiếu

Theo yêu cầu điều tra, đã thêm 2 nhóm mới còn thiếu trong catalog:

1. **iHexagon (1 mục)** — danh mục sản phẩm hoàn toàn mới của RATIONAL, kết hợp hơi nước + đối lưu (hot air) + vi sóng trên cả 6 khay cùng lúc (khoang 6-1/1). Chỉ có **1 size duy nhất** (không có XS/10/20 như iCombi) — RATIONAL không có trang `/models/` riêng cho dòng này. Model: `iHexagon 6-Half`, điện 20.4kW, 208V/3 pha (thị trường Mỹ), mã hãng `LM400BE`. Nguồn: `https://www.rational-online.com/en_us/ihexagon/` (đọc trực tiếp qua browser, xuyên shadow-root) + đại lý ủy quyền Mỹ cho phần thông số điện.
2. **CombiMaster Plus (7 mục)** — dòng combi entry-level, điều khiển **nút xoay** (không cảm ứng như iCombi): XS 6-2/3, 61 (6-1/1), 62 (6-2/1), 101 (10-1/1), 102 (10-2/1), 201 (20-1/1), 202 (20-2/1). **PHÁT HIỆN QUAN TRỌNG:** rational-online.com hiện **không còn microsite CombiMaster Plus riêng** — `/en_us/combimaster-plus` và `/en_gb/combimaster-plus/` đều redirect thẳng về `/icombi-classic/`, menu điều hướng chính không còn liệt kê CombiMaster Plus. Dòng sản phẩm **vẫn đang bán thật** qua đại lý ủy quyền RATIONAL (webstaurantstore.com, katom.com, catering-appliance.com, Parts Town...) dưới tên gộp "iCombi Classic CombiMaster"/"iCombi Classic Plus XS" (SKU 645ICCXSH...). Vì vậy 7 model trong nhóm này lấy specs từ **nhiều đại lý ủy quyền** thay vì trang chính hãng trực tiếp (đã bị gộp) — mỗi model có ghi chú nguồn + độ tin cậy riêng; model 202 chưa tìm được bảng thông số đầy đủ nên để trống thay vì suy đoán từ model 201.

## 0. Bổ sung lần 2 (cùng ngày 19/07/2026) — tóm tắt nhanh

1. **Accessories: 8 → 84 mục.** Thay vì chỉ đọc 8 phụ kiện hiển thị sẵn trên trang `/accessories/`, đã đọc trực tiếp biến JavaScript nội bộ `accessory.allData` mà chính trang RATIONAL dùng để render + lọc — biến này chứa toàn bộ **87 phụ kiện thật** (tên, mô tả ngắn, category, ảnh, link trang chi tiết), kể cả các mục ẩn sau nút "More accessories" và các bộ lọc Category/Deviceline/Appliance size. Đã thêm 76 phụ kiện mới thuộc các nhóm: Gastronorm & Baker's standard cookware/trays, Cleaning and care products, Exhaust hood & installation solutions, Oven stand & cabinets, Banquet and mobile solutions, iVario accessories, Hanging rack, Mobile oven rack, Set-up variations.
2. **3 model thiếu specs đã tìm lại được:** iCombi Pro 10-1/1, iCombi Classic 10-2/1, iCombi Classic 20-2/1 — nay đã có đầy đủ kW/kích thước/trọng lượng thật, lấy trực tiếp từ tính năng "Compare selection" của site (khác lần 1 bị treo trang khi tick 6-7 model cùng lúc — lần này tick từng 1-2 model một nên chạy được).
3. **Combi-Duo: xác nhận lại là phụ kiện, có SKU thật.** Không phải oven model riêng, nhưng có 3 phụ kiện lắp đặt thật với mã số (N°) cụ thể: "Combi-Duo kit" (bracket xếp chồng), "Setup variations" (chân đế/levelling kit), "Integrated grease drain kit" (thoát mỡ tích hợp) — đã tách 3 mục này vào nhóm Combi-Duo (trước đây rỗng).

---

## 1. Nguồn dữ liệu và phương pháp

RATIONAL dựng website bằng web components (custom element `<cms-*>` + shadow DOM), nên `WebFetch` (bản tóm tắt HTML tĩnh) không đọc được nội dung thật — trang trả về DOM gần như rỗng hoặc bị cắt cụt. Đã chuyển hẳn sang **trình duyệt thật (Claude Browser) + JavaScript đệ quy xuyên shadow-root** để trích xuất chính xác.

| # | Nguồn | Dùng cho |
|---|-------|----------|
| 1 | `https://www.rational-online.com/en_xx/icombi-pro/models/`, `/icombi-classic/models/`, `/ivario-pro/models/` | **Ảnh sản phẩm thật của từng size** — lấy trực tiếp từ CSS `background-image` của các ô `.img-container` trong bảng so sánh model (checkbox "Compare product"). Đây là ảnh chính hãng dùng riêng để phân biệt từng size (thư mục `comparison/` và `configurator/`), khác hẳn ảnh hero chung của trang. |
| 2 | Thẻ `<title>` + `<meta name="description">` thật của 5 trang: `/icombi-pro/`, `/icombi-classic/`, `/ivario-pro/`, `/icombi-pro/models/`, `/icombi-classic/models/`, `/accessories/`, `/connectedcooking/` | seo_title / seo_description — lấy nguyên văn qua `document.title` và `meta[name="description"]`, KHÔNG dịch, KHÔNG viết lại. |
| 3 | `https://www.rational-online.com/en_xx/accessories/` | 8 phụ kiện chính hãng có tên + ảnh thật hiển thị ngay trên trang (Granite-enamelled container, VarioSmoker, Perforated container, Container, Grid, Chicken and duck Superspike, CombiFry, Ribs rack). |
| 4 | WebSearch trích dẫn datasheet RATIONAL do các đại lý lưu lại (katom.com, kentcateringequipment.co.uk, caterkwik.co.uk, buzzcateringsupplies.com, catering-appliance.com) | Bổ sung/đối chiếu thông số kW, kích thước, trọng lượng cho từng size — dùng vì trang `funktionen/technical-details-equipment/` của RATIONAL chỉ có icon tính năng, KHÔNG có bảng số liệu dạng text đọc được. |
| 5 | `data.json` (nghiên cứu 14/07/2026, cùng brand) | Thông số iVario Pro (4 model) và một số specs iCombi Pro/Classic đã verify trước đó — kế thừa lại, có ghi chú nguồn gốc trong từng trường `specs."Nguồn số liệu"`. |

**Vì sao không có `title`/`seo_title` riêng cho từng model:** RATIONAL KHÔNG có trang chi tiết riêng cho mỗi size — cả 7 size iCombi Pro (hay 6 size iCombi Classic, 4 size iVario Pro) đều nằm chung **một trang duy nhất** dạng bảng so sánh (`/models/`). Vì vậy `seo_title`/`seo_description` của các model trong cùng 1 nhóm dùng lại đúng giá trị `<title>`/`<meta description>` thật của trang `/models/` đó — đã ghi rõ trong trường `seo_title_note` ở đầu mỗi nhóm để không gây hiểu nhầm là lỗi trùng lặp.

**Nỗ lực lấy specs qua tính năng "Compare selection":** đã thử tick chọn cả 7 checkbox và bấm nút so sánh trên trang `/icombi-pro/models/` — thao tác này làm trang treo/không phản hồi ổn định trong phiên trình duyệt, nên KHÔNG lấy được bảng specs chi tiết qua đường này. Đã chuyển sang WebSearch đối chiếu datasheet của đại lý như nguồn #4 ở trên.

---

## 2. Thống kê theo nhóm

| # | Nhóm (EN) | Nhóm (VI) | Số mục | Có ảnh thật | Có specs |
|---|---|---|---|---|---|
| 1 | iCombi Pro | Lò Hấp Nướng Đa Năng iCombi Pro | 7 | 7/7 | **7/7 đầy đủ** — 10-1/1 đã bổ sung kW/dims thật ở lần crawl 2 |
| 2 | iCombi Classic | Lò Hấp Nướng Đa Năng iCombi Classic | 6 | 6/6 | **6/6 đầy đủ** — 10-2/1 và 20-2/1 đã bổ sung kW/dims thật ở lần crawl 2 |
| 3 | iVario Pro | Chảo Nghiêng Đa Năng iVario Pro | 4 | 4/4 | 4/4 |
| 4 | Combi-Duo | Bộ Xếp Chồng Combi-Duo | **3** (phụ kiện lắp đặt có SKU thật, không phải oven riêng) | 3/3 | 3/3 (mã số N° thật cho từng cấu hình) |
| 5 | Accessories | Phụ Kiện Chính Hãng | **84** (tăng từ 8, xem mục 0) | 84/84 (2 mục không có ảnh trên site, đã ghi chú rõ) | 84/84 (tên+category thật; phần lớn chưa mở bảng SKU/kích thước chi tiết của từng trang con) |
| 6 | ConnectedCooking | Nền Tảng Quản Lý Bếp Số | 1 | 1/1 | mô tả (phần mềm, không có thông số vật lý) |
| 7 | iHexagon | Lò Đa Năng iHexagon | **1** (mới, lần 3) | 1/1 | 1/1 (thiếu kích thước tổng thể/trọng lượng — chưa xác minh được) |
| 8 | CombiMaster Plus | Lò Hấp Nướng Đa Năng CombiMaster Plus | **7** (mới, lần 3) | **7/7** (model 202 đã bổ sung ảnh ở lần 4) | 6/7 đầy đủ, model 202 chỉ xác nhận tồn tại chưa có bảng số liệu |
| **Tổng** | | | **113 mục** | **111/113** | |

---

## 3. Vấn đề cần review trước khi import

1. ~~Thông số kỹ thuật chưa đầy đủ 100%~~ **ĐÃ GIẢI QUYẾT (lần crawl 2)** — iCombi Pro 10-1/1, iCombi Classic 10-2/1, iCombi Classic 20-2/1 nay đã có kW/kích thước/trọng lượng thật lấy trực tiếp từ tính năng "Compare selection" của site (không còn phải WebSearch/đại lý cho 3 model này). Bí quyết: tick từng 1-2 checkbox một lượt thay vì cả 6-7 model cùng lúc như lần 1 (nguyên nhân treo trang trước đó).
2. **Mâu thuẫn tiềm ẩn giữa nguồn dữ liệu (các model còn lại)** — với các model có 2 nguồn (ví dụ iCombi Classic 6-1/1 công suất 10.8kW xuất hiện ở cả buzzcateringsupplies.com và trùng khớp iCombi Pro 6-1/1), số liệu nhất quán; nhưng chưa tự tải lại PDF gốc trực tiếp từ rational-online.com để xác nhận 100% — nguồn hiện tại là bên thứ ba trích dẫn lại. (Không áp dụng cho 3 model đã sửa ở mục 1 — 3 model đó giờ lấy trực tiếp từ site, không qua bên thứ ba.)
3. **`ConnectedCooking` có og:title/`<title>` lệch chủ đề** — trang `/en_xx/connectedcooking/` trả về `<title>` thật là "Fast Combi Ovens for School and Education Catering" dù nội dung trang (H1, meta description) đúng chủ đề ConnectedCooking. Đây là hiện trạng thật trên site chính hãng tại thời điểm cào (khả năng lỗi template/CMS phía RATIONAL), đã giữ nguyên trong `seo_title` kèm ghi chú rõ trong `specs.Ghi chú` — KHÔNG tự sửa vì đây là dữ liệu thật quan sát được, không phải lỗi cào.
4. ~~Accessories mới lấy được 8/nhiều mục~~ **ĐÃ GIẢI QUYẾT (lần crawl 2)** — đã đọc trực tiếp biến JS nội bộ `accessory.allData` của trang (87 phụ kiện thật, dùng để render+lọc chính trang), bổ sung 76 phụ kiện mới → Accessories hiện có 84 mục. Vẫn còn giới hạn: 76 mục mới chỉ có tên+mô tả ngắn (headline/subline)+ảnh+category, CHƯA mở từng trang con để lấy bảng mã SKU/kích thước chi tiết như đã làm mẫu với "Granite-enamelled containers" (trang con này có bảng 17 dòng SKU theo size/độ sâu) — việc mở hết 76 trang con để lấy SKU chi tiết cần thêm 1 phiên riêng.
5. **Combi-Duo: ĐÃ XÁC NHẬN LẠI — có SKU thật, không phải oven riêng.** Lần crawl 2 tìm thấy 3 phụ kiện Combi-Duo thật trong dữ liệu JS (category id=4): "Combi-Duo kit" (mã ví dụ 60.73.768, 60.73.991, 60.74.725...), "Setup variations" (chân đế/levelling kit, mã 60.74.795, 60.31.622...), "Integrated grease drain kit" (mã 60.75.873, 60.75.406...). Đã thêm 3 mục này vào nhóm Combi-Duo (trước đây rỗng) — đúng như dự đoán ban đầu, đây LÀ phụ kiện lắp đặt cho 2 lò mua riêng, KHÔNG phải model lò độc lập.
6. **iVario Pro trang `/models/` không có `<title>` riêng** (tab hiển thị generic "RATIONAL AG", `meta description` rỗng — đã xác nhận trực tiếp) — đã dùng seo_title/seo_description của trang landing `/ivario-pro/` thay thế, ghi rõ trong `seo_title_note`.
7. **Giá bán VN** — vẫn không có, đúng như `data.json` cũ đã ghi (RATIONAL không công bố giá public, cần liên hệ hãng/đại lý).
8. **Đại lý chính hãng tại Việt Nam** — vẫn CHƯA xác nhận, chưa tra dealer locator trong phiên này (kế thừa vấn đề mở từ `data.json` cũ).

---

## 4. Ghi chú kỹ thuật khi import

- `origin` = `"Germany"` cho toàn bộ model (RATIONAL AG sản xuất tại Landsberg am Lech, Đức) — trừ nhóm Accessories/ConnectedCooking (Germany, nhưng ConnectedCooking là phần mềm không có "xuất xứ" vật lý thật sự, giữ `Germany` vì là hãng phát hành).
- `heat_source`: `electric` cho iVario Pro (chỉ có bản điện); `electric/gas (có cả 2 biến thể cùng size)` cho các model iCombi Pro/Classic có xác nhận cả 2 tuỳ chọn nguồn nhiệt cùng size (không tạo sản phẩm riêng cho từng biến thể, theo đúng khuyến nghị đã ghi trong `data.json` cũ về tránh nhân bản SKU không cần thiết); `n/a` cho phụ kiện và phần mềm (không phải thiết bị gia nhiệt).
- Toàn bộ `image_url` là ảnh PNG/JPG thật lấy trực tiếp từ CSS background của bảng so sánh model hoặc trang phụ kiện trên `rational-online.com` — không dùng ảnh logo/placeholder.
- File hợp lệ JSON UTF-8, đã kiểm tra bằng Python (`json.load` thành công), 6 nhóm / **105 mục**, không mất dữ liệu.
- 2 phụ kiện mới (`abgassammler` / Flue gas collector, `wifi_stick_5_ghz` / WiFi-Stick 5 GHz) không có ảnh trong dữ liệu JS của trang — đã ghi rõ trong `specs.Ảnh` thay vì bịa `image_url`.
- **Không đụng `data.json`/`import.csv`**, không import WordPress, không chạy WP-CLI trong phiên này.
