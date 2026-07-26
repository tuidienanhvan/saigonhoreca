# Fiorenzato (Ý) — Catalog đầy đủ (`data-full.json`)

**Ngày khảo sát:** 18/07/2026
**Brand string:** `Fiorenzato`
**File dữ liệu:** `data-full.json` (6 nhóm · 34 model)

> ⚠️ File này **độc lập** với `data.json`/`import.csv`. Các file đó là bản đã import làm draft trước đó (cào 14/07/2026) và **không bị đụng tới** trong lần này.

---

## 1. Nguồn dữ liệu

| # | Nguồn | Dùng cho |
|---|-------|----------|
| 1 | **https://www.fiorenzato.com** (bản tiếng Anh `/en/`, WordPress) | Toàn bộ 32 trang sản phẩm hiện hành thuộc 5 nhóm B2B (Grind by Weight, PRO Line, On Demand, Grocery, Dosers). Danh sách URL lấy trực tiếp từ `/product-sitemap.xml` (sub-sitemap của `/wp-sitemap.xml`), sau đó WebFetch lại từng trang riêng để lấy `<title>` gốc, đoạn mô tả marketing thật, bảng thông số, và toàn bộ URL ảnh tuyệt đối trong carousel (`wp-content/uploads/...`). |
| 2 | **https://www.fiorenzatohome.com** (microsite riêng, HTML thuần — không phải WordPress, không có `sitemap.xml`/`robots.txt` Sitemap, đã kiểm tra trả về 404) | 2 model dòng gia đình AllGround (Classic, Sense) — nhắm thị trường tiêu dùng, tách biệt hoàn toàn khỏi menu chính fiorenzato.com. |
| 3 | **https://www.fiorenzato.com/en/our-optional-accessories/** | Đối chiếu — trang này chỉ liệt kê tamper, coffee press, phễu rời (không phải máy xay hoàn chỉnh) nên **không** đưa vào catalog model. |

**Xác nhận không sót model:** `/product-sitemap.xml` liệt kê đúng 32 URL `/en/...` sản phẩm (7 pro-line-grinders + 4 grinder-dosers + 4 grocery + 9 on-demand + 8 grind-by-weight = 32), khớp 100% với 32 model B2B đã có trong `data.json` từ lần cào trước. Không phát hiện model hay biến thể màu/phễu nào bị bỏ sót ngoài 34 model đã biết.

---

## 2. Thống kê

| Chỉ số | Số lượng |
|---|---|
| Nhóm (`groups`) | **6** |
| Model | **34** |
| Model có `image_url` tuyệt đối xác nhận | **34 / 34** (100% — xem mục 4) |
| Model có `description_en` thật từ trang nguồn | **34 / 34** |
| Model có `specs` (≥3 trường) | **34 / 34** |
| Model có `source_url` hoạt động | **34 / 34** |
| Model có `seo_description` gốc | **0 / 34** (site không set thẻ meta — xem mục 5) |
| Mã model trùng lặp | 0 |

### Phân bổ theo nhóm

| # | Nhóm (EN) | Nhóm (VI) | Model |
|---|---|---|---|
| 1 | Grind by Weight (SENSE / ADAPT) | Máy Xay Cân Trọng Lượng (SENSE / ADAPT) | 8 |
| 2 | PRO Line Grinders (Traditional, dosed) | Dòng PRO (Máy Xay Truyền Thống Có Định Lượng) | 7 |
| 3 | On Demand | Máy Xay Theo Yêu Cầu (On Demand) | 9 |
| 4 | Grocery (Traditional, dosed — no A prefix) | Dòng Grocery (Máy Xay Truyền Thống Cho Tạp Hóa/Quán Nhỏ) | 4 |
| 5 | Dosers (Grinder-Dosers, 'A' automatic) | Dòng Máy Xay-Định Lượng Cổ Điển (Grinder-Doser) | 4 |
| 6 | Home (AllGround) — consumer line, separate microsite | Dòng Gia Đình AllGround (Site Riêng fiorenzatohome.com) | 2 |

---

## 3. Những gì đã bổ sung/cải thiện so với bản cào trước (14/07/2026)

1. **Xác nhận qua sitemap chính thức** — bản trước cào theo 6 trang danh mục (menu-based); lần này đối chiếu chéo với `/product-sitemap.xml` (nguồn CMS thật) và xác nhận đúng 32/32 URL sản phẩm B2B, không thiếu model nào.
2. **9 model từng chỉ có tên file ảnh tương đối/suy đoán** — đã WebFetch lại từng trang và xác nhận URL tuyệt đối thật:
   - F64 EVO PRO ADAPT → `.../2026/05/F64-EVO-PRO-ADAPT-nero-opaco-1.png`
   - F5 A PRO → `.../2022/05/F5-A-PRO-4-1.png`
   - F64 EVO XGi PRO → `.../2022/05/F64-EVO-XGI-PRO-4-1.png`
   - F83 E (on-demand) → `.../2025/05/F83-E-nero-opaco-1.png`
   - F4 A → `.../2020/10/F4-A-nero-opaco_520x1000.png`
   - F5 (doser) → `.../2020/10/F5-A-4-1.png`
   - F71 AK (doser) → `.../2020/10/F71-AK-4-1.png`
   - AllGround Classic → `https://www.fiorenzatohome.com/img/allground_basic01.png`
   - AllGround Sense → `https://www.fiorenzatohome.com/img/AllGround-Sense_Deep-black_Matte.png`
3. **`description_en` thật cho toàn bộ 34 model** — đoạn mô tả marketing nguyên văn lấy từ mỗi trang sản phẩm (không có ở `data.json` cũ, vốn chỉ có specs).
4. **Đối chiếu trang phụ kiện** (`/en/our-optional-accessories/`) — xác nhận chỉ có tamper, coffee press, phễu rời, không phải máy xay hoàn chỉnh nên không tính vào model count.

---

## 4. Ảnh sản phẩm

Toàn bộ 34/34 `image_url` đã xác nhận là URL tuyệt đối thật, lấy từ ảnh đầu tiên trong carousel mỗi trang sản phẩm (thường hậu tố `-4-1.png` hoặc biến thể màu mặc định đầu tiên). Không dùng ảnh placeholder hay ảnh suy đoán.

- fiorenzato.com dùng thư mục `wp-content/uploads/{năm}/{tháng}/` theo ngày đăng bài — nên các đợt cập nhật sản phẩm khác nhau (2020, 2022, 2023, 2025, 2026) tạo ra định dạng tên file hơi khác nhau giữa các model, đây là đặc điểm CMS thật, không phải lỗi crawl.
- fiorenzatohome.com dùng thư mục `/img/` phẳng, không theo cấu trúc ngày tháng.

---

## 5. SEO — vấn đề cần lưu ý trước khi import

- **`seo_title`**: có sẵn cho toàn bộ 34 model, lấy nguyên văn từ thẻ `<title>` mỗi trang (giữ nguyên bản gốc EN, kể cả các trường hợp có vẻ "trộn tên" — xem mục 6).
- **`seo_description`**: **để trống cho toàn bộ 34 model** — đã kiểm tra lại 18/07/2026, không trang nào trên fiorenzato.com/fiorenzatohome.com có thẻ `<meta name="description">`. Đây là đặc điểm chung toàn site, không phải lỗi cào riêng lẻ. Cần soạn mới thủ công (hoặc AI-generate có kiểm duyệt kỹ thuật) trước khi lên site — schema yêu cầu giữ seo fields là bản gốc EN chưa dịch nên các mô tả SEO tiếng Việt cho site saigonhoreca cần soạn riêng ở tầng khác (không ghi vào `seo_description` của file này).

---

## 6. Nghi vấn trộn title — đã re-verify, giữ nguyên

4 model trong nhóm "PRO Line Grinders" (theo URL/slug và vị trí trong sitemap) nhưng thẻ `<title>` trả về lại thuộc nhóm khác:

| Model | URL thuộc nhóm | `<title>` thực tế trả về |
|---|---|---|
| F64 E PRO | pro-line-grinders/f64-e-pro/ | "On Demand F64 E PRO coffee grinder" |
| F64 EVO PRO | pro-line-grinders/f64-evo-pro/ | "On Demand F64 EVO coffee grinder" |
| F83 E PRO | pro-line-grinders/f83-e-pro/ | "Coffee grinder On Demand F83 E PRO" |
| F71 EK PRO | pro-line-grinders/f71-ek-pro/ | "Traditional coffee grinder F71 DK PRO" (ký hiệu D trùng với Grocery F71 DK) |

Đã fetch lại độc lập trong lần cào 18/07/2026 — kết quả giống hệt lần cào 14/07. Đây nhiều khả năng là lỗi gắn nhầm template/nội dung phía CMS của chính Fiorenzato, **không tự sửa lại** vì đây là dữ liệu thật cào được từ trang nguồn. Đã gắn `note` trong từng entry của `data-full.json` và liệt kê lại ở `open_questions_for_user`.

Ngoài ra, **F64 EVO XGi PRO** (nhóm On Demand) có `<title>` trùng hệt bản không-PRO — khác biệt xác nhận được chỉ là: ảnh riêng (`F64-EVO-XGI-PRO-4-1.png`) và 1 feature_note về quạt làm mát điện tử + fork có giá đỡ.

---

## 7. Những gì KHÔNG bao gồm — và lý do

| Không có | Lý do |
|---|---|
| **`seo_description`** cho toàn bộ 34 model | Site không set thẻ meta description ở bất kỳ trang nào (đã kiểm tra 2 lần, 2 đợt cào cách nhau 4 ngày, cùng kết quả). |
| **Phụ kiện (tamper, coffee press, phễu rời)** | Đã kiểm tra `/en/our-optional-accessories/` — không phải máy xay hoàn chỉnh, không tính vào model count. Có thể làm danh mục phụ kiện riêng sau này nếu cần. |
| **Phụ tùng thay thế** (`/en/spare-parts/`) | Chưa cào nội dung chi tiết — trang liệt kê linh kiện rời (burr, gasket...), không phải sản phẩm hoàn chỉnh. |
| **Giá bán tại Việt Nam / giá bán công khai** | Fiorenzato không công bố giá trên website (đúng thông lệ ngành pha chế B2B/wholesale). Cần liên hệ info@fiorenzato.it hoặc đại lý phân phối. |
| **Quan hệ đại lý chính thức tại Việt Nam** | Không tìm thấy trang "dealers"/"distributors" trên fiorenzato.com. Site saigonhoreca.local có Fiorenzato ở section đối tác trang chủ nhưng chưa cross-check với danh sách đại lý chính thức của hãng — cần user xác nhận nội bộ. |

---

## 8. Ghi chú kỹ thuật khi import

- `origin` = `"Italy"` cho tất cả 34 model (Fiorenzato M.C. S.r.l. sản xuất tại Ý).
- `heat_source` = `"electric"` cho tất cả (toàn bộ máy xay cà phê chạy điện, không có biến thể gas/hơi).
- `seo_title`/`seo_description` trong file này là **bản gốc tiếng Anh chưa dịch**, đúng theo yêu cầu schema — KHÔNG dùng trực tiếp làm nội dung SEO tiếng Việt cho site saigonhoreca mà cần một bước soạn thảo/dịch riêng.
- Ảnh dùng chung một số trường hợp (ví dụ các biến thể PRO/không-PRO của XGi có ảnh khác file nhưng cùng phong cách sản phẩm) là đúng thực tế nhà sản xuất, không phải lỗi bóc tách.
- Toàn bộ 34 `source_url` đã kiểm tra là URL thật đang hoạt động (32 trên fiorenzato.com/en/, 2 trên fiorenzatohome.com).
