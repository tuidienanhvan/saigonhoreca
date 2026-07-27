# Giorik (Ý) — Catalog đầy đủ (`data-full.json`)

**Ngày khảo sát:** 18-19/07/2026 (bổ sung lần 1 19/07: giải quyết 4 vấn đề để ngỏ từ đợt 18/07; bổ sung lần 2 19/07: audit nhóm còn thiếu Friggitrici/Cuocipasta + kiểm tra Lavaggio/Refrigerazione)
**Brand string:** `Giorik-Italia`
**File dữ liệu:** `data-full.json` (4 nhóm chính · **35 model** đại diện có ảnh + specs + source_url thật, tăng từ 29)

> **Cập nhật 19/07/2026 (lần 2 — audit bổ sung nhóm thiếu):** đã thêm 6 model mới vào nhóm **Modular Cooking**: Friggitrici (Fryers) Unika 900 (`FE9113` điện, `FG9113T` gas) và Cuocipasta (Pasta-Cooker) Unika 900 (`CPE926` điện, `CPG926` gas) + Gio 60 (`LCP6713` điện, `LCG6700` gas). Friggitrici/Cuocipasta Unika 700 đã có sẵn từ trước (`FG7215T`, `CPE726`). **Đã kiểm tra kỹ và XÁC NHẬN 2 nhóm "Lavaggio" và "Refrigerazione" KHÔNG tồn tại trên giorik.com** — trang danh mục chính thức (`giorik.com/it/prodotti.html`, `/en/home.html`) chỉ có đúng 3 nhóm sản phẩm (Ovens, Modular Cooking, Salamanders) + microsite Pop Speed Oven; nhiều truy vấn Google `site:giorik.com` cho "lavaggio"/"refrigerazione" không ra trang sản phẩm nào thuộc 2 nhóm này. Giorik không sản xuất thiết bị lạnh; "lavaggio" (rửa khoang lò) chỉ là TÍNH NĂNG của các dòng lò (Steambox/Kore/Minitouch/Movair — "automatic washing system"), không phải nhóm sản phẩm riêng. KHÔNG thêm 2 nhóm này vào catalog để tránh bịa dữ liệu — xem `gaps_not_resolved` trong `data-full.json` để biết chi tiết.
>
> **Cập nhật 19/07/2026 (lần 1):** đã xử lý xong 4/4 vấn đề tồn đọng ghi ở mục 6 bản trước — xem mục 7 bên dưới để biết chi tiết kết luận từng vấn đề. Đã dùng `curl` tải HTML gốc trực tiếp (không qua lớp tóm tắt AI của WebFetch) để loại trừ khả năng lỗi cache công cụ, và tải thêm 1 file PDF datasheet chính hãng cho Pop Speed Oven.

> File này **độc lập** với `data.json` (bản "mức dòng/series" cào ngày 13/07) và `import.csv` (bản nháp 51 dòng). **Không sửa 2 file đó.**

---

## 1. Nguồn dữ liệu và phương pháp

- Nguồn duy nhất: website chính hãng **www.giorik.com** (bản `/en/`) và microsite **popspeedoven.giorik.com**.
- Điểm khởi đầu: các URL nhóm/dòng (`source_url`) đã liệt kê sẵn trong `data.json` và `README.md` cũ.
- Phương pháp: dùng WebFetch tải HTML thật của từng trang, đi 2-3 lớp sâu:
  1. Trang tổng quan nhóm (vd `/en/products/modular-cooking/gas-ranges/unika700-...html`) → tìm link tới trang model con dạng `/en/product/.../<slug>-<id>.html`.
  2. Fetch trang model con để lấy `<title>`, mô tả, bảng thông số kỹ thuật, và URL ảnh chính (dạng `https://www.giorik.com/imageimp/<hash>/<slug>` — **không phải** placeholder `res/img/en.svg`).
  3. Với vài dòng (MovAir, EasyAir, Kore) phải đi thêm 1 lớp trung gian để tìm đúng model điện/gas đại diện.
- Tổng cộng đã thực hiện **~50 lượt WebFetch thật** trong phiên này (không tính lần crawl 13/07 trước đó).
- `research_date` = `2026-07-18`. `origin` = `"Italy"` cho toàn bộ. `heat_source` chỉ ghi `"electric"`/`"gas"` khi trang nguồn xác nhận rõ; không đoán.

---

## 2. Thống kê

| Nhóm | Số model đại diện có ảnh+specs+source_url thật | Số mã anh em liệt kê ở `model_variants` (ước tính, lấy từ data.json) |
|---|---|---|
| Ovens (Steam/Combi/Convection) | 10 (mỗi sub-line 1 model, riêng Kore B nay có 2 để phân biệt điện/gas: Steambox H, Steambox Master, Kore B điện, Kore B gas, Kore I, Minitouch, MovAir điện, MovAir gas, EasyAir ECE, EasyAir EME) | ~130 mã |
| Modular Cooking | **21** (19 loại thiết bị, riêng Bratt Pans và nay thêm Fryers/Pasta-Cooker Unika 900 + Gio 60 có 2 để phân biệt điện/gas) | ~360 mã |
| Salamanders | 3 (Hi Lite, Hi Plus, Hi Touch) | 4 mã (SH20/40, SHP40, ST40) |
| Pop Speed Oven | 1 (model "POP" — xác nhận là tên sản phẩm chính thức qua datasheet PDF, không phải mã alphanumeric riêng) | 0 |
| **TỔNG** | **35 model** (tăng từ 29) | **~500 mã** |

**35/35 model đều có:** `image_url` thật (URL `imageimp` từ giorik.com, không phải placeholder), `specs` (ít nhất 2-3 trường số liệu thật), `source_url` là trang model con đã fetch thành công, `origin="Italy"`, `seo_title` lấy nguyên văn `<title>` tiếng Anh.

**`seo_description` để trống (`""`) cho toàn bộ 35 model** — đã kiểm tra nhiều trang khác nhau trên giorik.com, **không trang nào set `<meta name="description">`** (giống tình trạng Precision-UK). Đây là dữ liệu thật của trang, không phải thiếu sót khi cào.

### 2.1. Chi tiết 6 model mới bổ sung (lần 2, 19/07/2026)

| Model | Nhóm/dòng | Nguồn nhiệt | Ghi chú |
|---|---|---|---|
| `FE9113` | Friggitrici — Unika 900, đơn bể 13L | electric | Danh mục đầy đủ Unika 900 Fryers có 12 mã điện + 20 mã gas (xem `model_variants`) |
| `FG9113T` | Friggitrici — Unika 900, đơn bể 13L | gas | Ảnh trùng slug `fg7113t` (tên nội bộ tương ứng bên Unika 700) — thực trạng ảnh của site |
| `CPE926` | Cuocipasta — Unika 900, đơn bể 40L | electric | |
| `CPG926` | Cuocipasta — Unika 900, đơn bể 40L | gas | Ảnh trùng slug `cpg726` (tên nội bộ tương ứng bên Unika 700) — thực trạng ảnh của site |
| `LCP6713` | Cuocipasta — Gio 60, đơn bể 26L | electric | Mã này đã từng được nhắc trong `model_variants` của `CPE726` (Unika 700) bản trước — nay tách entry riêng đầy đủ |
| `LCG6700` | Cuocipasta — Gio 60, đơn bể 26L | gas | Cùng lý do trên |

**Friggitrici Gio 60: xác nhận LẠI (19/07, lần 2) vẫn KHÔNG có model nào published** — trang `fryers/gio-60-9851820.html` chỉ chứa đúng 1 link, chính là URL của trang category, không có model con nào bên dưới (khớp với kết luận mục 7.4 bản trước).

---

## 3. So sánh với `import.csv` cũ — phát hiện quan trọng

**`import.csv` cũ KHÔNG hoàn toàn dùng ảnh placeholder như giả định ban đầu.** Kiểm tra lại 51 dòng cho thấy:

- Nhiều dòng **đã có ảnh thật** dạng `https://www.giorik.com/imageimp/<hash>/<slug>png` (ví dụ toàn bộ 12 mã Steambox Evolution SNHE0xx/SERE0xx/SERG0xx, và các mã EasyAir ECG5x/ECG7x/ECG10x, EMG5x/EMG7x/EMG10x).
- Một số dòng khác **vẫn dùng ảnh placeholder** `https://www.giorik.com/res/img/en.svg` — chủ yếu là các mã chỉ được liệt kê từ trang tổng quan (Ovens overview, Salamanders overview) mà chưa fetch trang model con riêng: toàn bộ 8 mã gas Steambox H (SNHG0xx), 3 mã Minitouch (KNM...), phần lớn EasyAir điện dòng ECE/EME cơ bản (không có S/X hậu tố), và toàn bộ 7 mã Salamander (SH20/30/40, SHP30/40, ST30/40).
- Kết luận: `import.csv` là **kết quả trộn giữa 2 cấp độ crawl** (một số dòng đã lấy ảnh trang model con thật, một số dòng khác chỉ dừng ở trang overview) — không đồng nhất. `data-full.json` đã sửa việc này bằng cách **luôn dùng ảnh trang model con thật** cho tất cả 28 entry, kể cả 3 dòng Salamander mà `import.csv` cũ vẫn còn placeholder (SH30, SHP30, ST30 nay đã có ảnh thật `imagephp` links).

### Sai lệch cụ thể phát hiện được so với `data.json`/`README.md` cũ

| Model | Dữ liệu cũ (data.json/README) | Dữ liệu mới verify bằng fetch thật | Ghi chú |
|---|---|---|---|
| Fryer đại diện Unika 700 gas | Dự đoán mã `FG7113T` (13L đơn) theo pattern | Trang thật trả về **FG7215T** (13+13L, 2 bể) tại đúng URL `gas-fryer-13-13-l` đã có sẵn trong data.json | Không phải lỗi nghiêm trọng — chỉ là link cũ trỏ đúng trang nhưng người crawl trước đoán sai mã từ tên slug "13-13-l"; đã sửa đúng trong data-full.json |
| KB101W (Kore B) | Ghi model KB101W, không rõ nguồn gas/điện | **ĐÃ GIẢI QUYẾT 19/07** — xem mục 7.2: đây là trang dạng tab, KB101W=tab điện (đúng), đã thêm entry riêng KBG101W=tab gas | Không phải mâu thuẫn thật, chỉ là cách đặt tên slug gây hiểu lầm |
| CM720G (Bratt Pan) | Phân loại là gas (theo mã ký hiệu G) | **ĐÃ SỬA 19/07** — xem mục 7.3: trang nguồn chỉ có 1 tab "Electric", bảng specs ghi rõ Power supply: Electric | Đã sửa `heat_source` thành electric trong `data-full.json`; nghi CM920G/CM940G cũng sai tương tự, chưa verify |
| Ảnh NMTG10 | — | **ĐÃ XÁC MINH 19/07** — xem mục 7.1: curl trực tiếp xác nhận cả 2 trang nguồn (NMTG10 và NMTE10_R) đều chứa cùng 1 `<img src>` — là thực trạng site, không phải cache | Đã cập nhật `model` thành NMTG10_R (theo bảng specs trang nguồn), giữ ảnh trùng đúng thực trạng |

---

## 4. Những gì KHÔNG cào được — và lý do

| Không có | Lý do |
|---|---|
| **Nhóm "Lavaggio" (Washing) và "Refrigerazione" (Refrigeration)** | **Không tồn tại trên giorik.com** — đã kiểm tra kỹ trang danh mục chính thức và nhiều truy vấn Google `site:giorik.com`, không có category/sản phẩm nào thuộc 2 nhóm này. Giorik chỉ có 3 nhóm sản phẩm: Ovens, Modular Cooking, Salamanders (+ Pop Speed Oven microsite). "Lavaggio" chỉ là tính năng rửa tự động của các lò, không phải nhóm sản phẩm. Xem `gaps_not_resolved` trong `data-full.json`. |
| **Mã model cụ thể cho Gio 60 Fryer** | **Xác nhận LẠI 19/07 bằng curl HTML gốc**: khung "Models" trên trang depth-line hoàn toàn rỗng (không như Unika 700/900 cùng cấu trúc nhưng có tab model thật) — Giorik chưa publish model cụ thể nào cho dòng này, không phải lỗi cào. Xem mục 7.4. |
| **Mã SKU/model cho Pop Speed Oven** | Trang chủ và Shop vẫn không có SKU/giá, nhưng **19/07 tìm được datasheet PDF chính hãng** (`POP_EN.pdf`, cập nhật 11/2025) xác nhận đây là sản phẩm thật với tên model chính thức "POP" và specs đầy đủ. Xem mục 7.4 và entry đã cập nhật trong `data-full.json`. |
| **Toàn bộ ~360 mã biến thể Modular Cooking và ~130 mã biến thể Ovens dưới dạng entry riêng** | Theo đúng chỉ đạo của nhiệm vụ: đây là các biến thể tùy chọn (finish/hinge/tank size/độ sâu) của cùng một kiểu máy, không phải sản phẩm khác nhau về công dụng. Đã chọn 1 model đại diện có ảnh+specs+source_url thật cho MỖI dòng/loại thiết bị, và liệt kê đầy đủ mã anh em trong trường `model_variants` của từng entry (dữ liệu lấy nguyên từ `data.json`, không bịa thêm). |
| **`seo_description` cho toàn bộ 29 model** | Giorik.com không set `<meta name="description">` trên bất kỳ trang model nào đã kiểm tra — để trống đúng thực tế, không suy đoán nội dung. |
| **Giá bán** | Giorik không công bố giá công khai (thông lệ ngành B2B thiết bị bếp công nghiệp). Cần liên hệ info@giorik.com hoặc đại lý phân phối. |
| **File ảnh độ phân giải cao tải về máy** | Chỉ lấy được URL ảnh thật (`imageimp`), chưa tải file vật lý về — nếu cần archive ảnh offline phải tải riêng từng URL. |
| **EasyAir "New Generation" (NECE/NECG/NEME/NEMG) và biến thể MovAir gas `NMTG10W_R`** | Các mã này xuất hiện trong danh sách liệt kê trên trang tổng quan nhưng chưa fetch trang model con riêng để xác minh độc lập — giữ nguyên trạng thái "chưa xác minh" như bản cũ, không cào thêm trong lần này (ưu tiên phủ đủ 1 model/dòng trước). |

---

## 5. Ghi chú kỹ thuật khi import

- Toàn bộ 29 `source_url` là trang model con thật đã fetch thành công (HTTP 200, có nội dung sản phẩm cụ thể, không phải trang lỗi/404) — Pop Speed Oven nay trỏ tới file PDF datasheet chính hãng (nguồn xác thực nhất tìm được).
- Toàn bộ 29 `image_url` lấy từ nội dung trang đã fetch (không đoán, không dùng placeholder). NMTG10_R vẫn dùng chung ảnh với NMTE10_R và KBG101W dùng chung ảnh với KB101W — đã xác minh (mục 7.1, 7.2) đây là thực trạng thật của site (Giorik dùng chung 1 ảnh cho bản điện/gas của cùng dòng máy), không phải lỗi cào.
- `heat_source`: đã sửa CM720G thành `electric` (trước ghi nhầm `gas`, xem mục 7.3). Không còn trường hợp nào để `null`/mâu thuẫn chưa giải quyết trong 29 model đã chọn.
- Model `Pop Speed Oven` có `"model": "POP"` — xác nhận qua datasheet PDF chính hãng rằng "POP" chính là tên sản phẩm/model chính thức (brand không có mã alphanumeric riêng như các dòng oven khác), không còn để `null` nữa.
- Trường `model_variants` là mảng string tự do (không phải object), lấy nguyên từ danh sách mã đã cào trong `data.json` (13/07) cho từng dòng/loại thiết bị tương ứng — không tạo thêm mã mới, không bịa.
- Trường `note` xuất hiện trên các model có phát hiện/đã giải quyết mâu thuẫn hoặc cần theo dõi thêm (KB101W, KBG101W, CM720G, NMTG10_R, Pop Speed Oven, FG7215T, LPV4721 — về ceramic hobs Unika 700/900 không có mã).

---

## 6. Việc cần người review kiểm tra thêm (ưu tiên cao → thấp)

1. ~~**Ảnh NMTG10** nghi trùng cache với NMTE10_R~~ — **ĐÃ GIẢI QUYẾT**, xem mục 7.1.
2. ~~**KB101W vs KBG101W**~~ — **ĐÃ GIẢI QUYẾT**, xem mục 7.2.
3. ~~**CM720G**~~ — **ĐÃ GIẢI QUYẾT (đã sửa dữ liệu)**, xem mục 7.3.
4. ~~Gio 60 Fryer và Pop Speed Oven~~ — **ĐÃ XÁC MINH KỸ HƠN** (Gio 60 Fryer xác nhận chưa có model, Pop Speed Oven tìm được datasheet PDF xác nhận là sản phẩm thật), xem mục 7.4.
5. **MỚI phát hiện khi xử lý #3**: CM920G, CM940G (Bratt Pan Unika 900) nghi cũng bị phân loại sai gas/điện tương tự CM720G — chưa verify, cần kiểm tra thêm ở phiên sau.
6. Giá bán và nhà phân phối chính hãng tại Việt Nam — ngoài phạm vi crawl từ giorik.com, cần liên hệ trực tiếp.

---

## 7. Kết luận chi tiết 4 vấn đề đã xử lý (19/07/2026)

Phương pháp lần này: dùng `curl` tải HTML gốc trực tiếp (bỏ qua lớp tóm tắt AI của WebFetch) để loại trừ khả năng lỗi nằm ở công cụ crawl, rồi đọc thẳng đoạn HTML chứa bảng specs/ảnh sản phẩm.

### 7.1. Ảnh NMTG10 nghi trùng cache với NMTE10_R

**Kết luận: đây là lỗi/thực trạng dữ liệu trên chính website Giorik, KHÔNG phải lỗi cache của công cụ fetch.** Đã `curl` trực tiếp cả 2 trang (`nmtg10-...-gas-30250106.html` và `nmte10_r-...-30250033.html`) và xác nhận HTML gốc của cả 2 trang đều chứa cùng một `<img src="https://www.giorik.com/imageimp/8aefa4f788e159e164a10ba03a1549d6eb118c01/nmte10_rpng">`. Bảng specs (kích thước, trọng lượng, công suất) của 2 trang vẫn khác nhau đúng như dữ liệu cũ (907x789x1079mm/148kg gas vs 907x752x1023mm/100kg điện) — chỉ ảnh minh họa bị Giorik dùng chung. Phát hiện thêm: bảng specs trang NMTG10 tự ghi mã "**NMTG10_R**" (có hậu tố `_R`), không phải "NMTG10" trơn như trên `<title>` — đã cập nhật field `model` thành `NMTG10_R` cho khớp nguồn. Đã giữ nguyên ảnh trùng (đúng thực trạng site) và cập nhật `note` để không còn ghi "nghi ngờ" nữa.

### 7.2. KB101W vs KBG101W tự mâu thuẫn

**Kết luận: KHÔNG có mâu thuẫn thật — đây là 1 trang sản phẩm dạng tab (Electric/Gas).** URL cũ trong catalog (`.../kbg101w-touch-screen-...-4674804.html`) là trang có tab "Electric" **đang active mặc định**, và bảng specs trong tab đó ghi rõ `Code: KB101W`, `Power supply: Electric` — tức dữ liệu cũ trong `data-full.json` (model=KB101W, heat_source=electric) **hoàn toàn chính xác**, không cần sửa. "KBG101W" chỉ là tên slug chung Giorik dùng cho cả 2 URL (cả bản điện lẫn bản gas), gây hiểu lầm là mâu thuẫn. Đã `curl` thêm trang tab gas (`.../kbg101w-gas-...-4674805.html`) và xác nhận bảng specs ở đó ghi `Code: KBG101W`, `Power supply: Gas/Electric`, kích thước/công suất khác (519x838x1010mm, 11kW gas + 1.5kW điện phụ trợ) — dùng chung ảnh `kb101wjpg` với bản điện. Đã **thêm entry mới KBG101W** (bản gas) vào `data-full.json` với source_url riêng, tổng số model tăng từ 28 lên **29**.

### 7.3. CM720G — gas hay điện?

**Kết luận: model này là ĐIỆN, không phải gas — đã SỬA dữ liệu.** Trang nguồn CHỈ có 1 tab duy nhất "Versione electric" (không có tab gas nào cho model này), và bảng specs ghi rõ `Power supply: Electric`. Phân loại "gas" trong `data.json`/README cũ dựa theo quy ước "hậu tố G trong mã = gas" là **sai cho riêng model CM720G** — quy ước đó không áp dụng nhất quán trong dòng Bratt Pan "CM" (khác với dòng Gas Ranges CG7xxG hay Bratt Pan BE/BG74XM, nơi ký hiệu E/G đúng là điện/gas). Đã sửa `heat_source` từ `"gas"` thành `"electric"` trong `data-full.json`, khớp với mô tả "heating elements" nêu trong task. Đã ghi chú cảnh báo: CM920G, CM940G (cùng dòng, Unika 900) nhiều khả năng cũng bị phân loại sai tương tự nhưng **chưa verify riêng** trong lần này (ngoài phạm vi 4 vấn đề được giao).

### 7.4. Gio 60 Fryer và Pop Speed Oven

- **Gio 60 Fryer: xác nhận CHƯA có model/SKU nào được publish — không phải lỗi cào.** Trang depth-line (`fryers/gio-60-9851820.html`) có khung `<div id="models"><div class="product-tabs">...</div></div>` y hệt cấu trúc của Unika 700/900, nhưng khung tab **hoàn toàn rỗng** — không có bất kỳ nút tab Electric/Gas hay model con nào được server render. So sánh trực tiếp với trang Unika 700 tương ứng (`fryers/unika-700-9851796.html`) cho thấy trang đó CÓ đầy đủ 2 tab Electric/Gas với model thật bên trong — chứng tỏ sự khác biệt là do Giorik chưa/không công bố model Gio 60 Fryer cụ thể nào, không phải do công cụ crawl bỏ sót nội dung JS. Không tìm thấy sitemap.xml hợp lệ (URL `/sitemap.xml` trả về trang chủ, không phải sitemap thật) hay catalogue PDF nào riêng cho dòng này để tìm thêm.
- **Pop Speed Oven: xác nhận ĐÂY LÀ sản phẩm thật đang bán, tên model chính thức là "POP".** Trang chủ và `/shop/` vẫn không có SKU/giá như 2 lần crawl trước, nhưng lần này tìm thấy link tải **datasheet kỹ thuật PDF chính hãng** trên trang chủ: `https://popspeedoven.giorik.com/wp-content/uploads/2025/11/POP_EN.pdf` (bản cập nhật 11/2025, 2 trang, có sơ đồ lắp đặt và bảng thông số điện/nước công nghiệp đầy đủ). PDF xác nhận: (1) sản phẩm có tên chính thức là "**POP**" (dòng "PLUG & COOK SPEED OVEN"), không có mã alphanumeric riêng kiểu NMTG10 — "POP" chính là tên/mã sản phẩm; (2) công nghệ air impingement + steam đang trong tình trạng **patent pending**; (3) thông số đầy đủ: 1x GN2/3, 431x637x510mm ngoài / 360x445x255mm khoang nấu, 220/240V-1N 50/60Hz, 3.3kW buồng nấu / 3kW tổng, 45kg, bình nước 3L (~400 chu kỳ). Đã cập nhật toàn bộ specs này vào `data-full.json` (trước đây ghi "không công bố"), và đổi `source_url` sang chính file PDF (nguồn xác thực nhất). Vẫn không có giá bán.
