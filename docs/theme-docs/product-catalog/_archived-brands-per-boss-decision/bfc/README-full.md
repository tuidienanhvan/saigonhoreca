# BFC (Ý) — Catalog đầy đủ (`data-full.json`)

**Ngày khảo sát:** 18/07/2026
**Ngày hiệu đính (loại bỏ 2 model gán nhầm brand):** 19/07/2026
**Brand string:** `BFC`
**File dữ liệu:** `data-full.json` (5 nhóm · 40 model)

> File này **độc lập** với `data.json` (bản cào đầu tiên, ngày 14/07/2026, đã import làm draft). `data.json` và `import.csv` **không bị đụng tới**.

> **CẬP NHẬT 19/07/2026:** Nhóm "Legacy / Orphaned Grinder Pages" (2 model **E5P** và **E6P**) đã bị **loại bỏ** khỏi `data-full.json`. Audit Google xác nhận đây KHÔNG phải sản phẩm của BFC mà là máy xay cà phê của hãng **Ceado** (một thương hiệu Ý khác), bị gán nhầm brand BFC ở lượt cào sitemap trước. Trang chính thức [bfcsrl.it/en/product_category/coffee-grinder-dosers/](https://www.bfcsrl.it/en/product_category/coffee-grinder-dosers/) chỉ có đúng **5 model máy xay thật của BFC**: BFC 58E, 64A, 64E, 83A, 83E — cả 5 đã có đầy đủ trong nhóm "Coffee Grinder Dosers". Tổng số model của file giảm từ 42 xuống **40**. Xem chi tiết ở mục 2 và mục 8 bên dưới. Toàn bộ nội dung còn lại của tài liệu này giữ nguyên như mô tả lúc khảo sát 18/07/2026 để lưu vết lịch sử; các số liệu 41/42 cũ chỉ còn giá trị tham khảo lịch sử, KHÔNG phản ánh dữ liệu hiện hành trong `data-full.json`.

---

## 1. Nguồn dữ liệu và phương pháp

1. **https://www.bfcsrl.it** (bản tiếng Anh `/en/`) — nguồn duy nhất, không dùng nguồn thứ cấp cho dữ liệu sản phẩm.
2. Lấy toàn bộ URL qua **sitemap index** (`/sitemap_index.xml`) → 8 sitemap con: `post-sitemap.xml`, `page-sitemap.xml`, `attachment-sitemap.xml` (x2), `nostri-prodotti-sitemap.xml`, `area-riservata-sitemap.xml`, `category-sitemap.xml`, `product_category-sitemap.xml`.
3. Đối chiếu URL sitemap với **6 trang category chính hãng**: `/product_category/macchine-tradizionali/`, `/macchine-a-leva/`, `/linea-casa/`, `/linea-ufficio/`, `/macinadosatori/`, `/accessori/`, cộng trang tổng `/en/products/`.
4. Fetch trực tiếp từng trang sản phẩm `/en/products/<slug>/` để lấy: mô tả thân trang (body description — khác với meta description), bảng thông số kỹ thuật, ảnh sản phẩm chính (`wp-content/uploads/...`).
5. Kiểm tra chéo: `product_category-sitemap.xml` (202 URL đa ngôn ngữ: it/en/fr/de/es) để tìm sản phẩm site có mà chưa cào.

---

## 2. Kết quả đối chiếu sitemap — 40/40 model khớp data.json, phát hiện thêm 2 model "mồ côi"

Toàn bộ **40 model / 5 nhóm** trong `data.json` đã được xác nhận lại 100% qua 6 trang category chính hãng — không thiếu, không thừa, không đổi tên. Danh sách category hoàn toàn khớp:

| Nhóm | Trang category | Số model xác nhận |
|---|---|---|
| Traditional Machines | `/product_category/macchine-tradizionali/` | 18 hiển thị + Classica (tổng 19, khớp `/en/products/`) |
| Machine Lever | `/product_category/macchine-a-leva/` | 2 |
| Home/Office Range | `/product_category/linea-casa/` **và** `/linea-ufficio/` | 9 (2 trang liệt kê **y hệt nhau** — xác nhận "Home Range" và "Office Range" thực chất là 1 danh mục dùng chung, đúng như đã gộp trong data.json) |
| Coffee Grinder Dosers | `/product_category/macinadosatori/` | 5 |
| Accessories | `/product_category/accessori/` | 5 |

**Phát hiện mới:** quét `product_category-sitemap.xml` (202 URL) lộ ra 2 URL lạ: `https://www.bfcsrl.it/es/productos/e6p/` và `.../e5p/`. Đây là 2 trang sản phẩm **thật, đang sống** (HTTP 200, có nội dung đầy đủ: mô tả, thông số, ảnh) nhưng:

- **Không có bản tiếng Anh/Ý** — `/en/products/e5p/` và `/en/products/e6p/`, `/nostri-prodotti/e5p/` đều trả **404**.
- **Không xuất hiện** trong trang category hiện hành `/product_category/macinadosatori/` (chỉ liệt kê đúng 5 model BFC 58E/64A/64E/83A/83E).

→ Kết luận: **E5P và E6P là 2 model tiền nhiệm/đã ngưng của dòng grinder-doser**, khả năng là bản tiền thân của BFC 64E (E5P: Ø64mm, 0.6kg hopper, 300W) và BFC 64A/64E (E6P: cùng thông số, có lập trình liều điện tử). Trang cũ chưa bị gỡ khỏi server nhưng đã bị rút khỏi menu điều hướng và khỏi bản dịch EN/IT. Đã đưa vào `data-full.json` dưới nhóm riêng **"Legacy / Orphaned Grinder Pages"**, có ghi chú rõ tình trạng, **không có `image_url`** (không tìm được ảnh — trang ES không có ảnh sản phẩm khả dụng qua fetch, chỉ có văn bản).

**Không phát hiện thêm nhóm/model nào khác** ngoài 40 model hiện hành + 2 model mồ côi này.

---

## 3. Thống kê

| Chỉ số | Số lượng |
|---|---|
| Nhóm (`groups`) | **6** (5 nhóm hiện hành + 1 nhóm legacy) |
| Model | **42** (40 hiện hành + 2 legacy) |
| Model có `image_url` thật | **40 / 42** (2 model legacy E5P/E6P không có ảnh) |
| Model có `specs` (≥3 trường) | **42 / 42** |
| Model có `source_url` hoạt động (HTTP 200) | **42 / 42** |
| Model có `seo_description` | **0 / 42** — xem mục 4 |

### Phân bổ theo nhóm

| # | Nhóm (EN) | Nhóm (VI) | Model |
|---|---|---|---|
| 1 | Traditional Machines | Máy Pha Cà Phê Espresso Truyền Thống (Quầy Bar) | 19 |
| 2 | Machine Lever | Máy Pha Cà Phê Cần Gạt (Lever) | 2 |
| 3 | Home/Office Range | Máy Pha Cà Phê Gia Đình / Văn Phòng (1 Group) | 9 |
| 4 | Coffee Grinder Dosers | Máy Xay Cà Phê Định Lượng (Grinder-Doser) | 5 |
| 5 | Accessories | Phụ Kiện | 5 |
| 6 | Legacy / Orphaned Grinder Pages | Máy Xay Định Lượng Cũ / Trang Mồ Côi | 2 |

---

## 4. SEO — gap đã xác minh, không phải do bỏ sót

Fetch trực tiếp mọi trang sản phẩm xác nhận: **bfcsrl.it không đặt thẻ `<meta name="description">`** trên bất kỳ trang nào (đã kiểm tra lại độc lập trong lần cào này, khớp với phát hiện của lần cào 14/07/2026). `seo_title` lấy từ `<title>` thật, dạng `"{Tên sản phẩm} | BFC srl"` — 42/42 model đều có. `seo_description` để **rỗng** (`""`) cho toàn bộ 42 model theo đúng schema yêu cầu ("rỗng nếu không có") — không tự bịa.

---

## 5. Mô tả sản phẩm (`description_en`) — phương pháp

Toàn bộ 42 model đều lấy mô tả THẬT từ trang sản phẩm, theo 2 cách:

1. **30 model** thuộc 3 nhóm Traditional Machines / Machine Lever / Home-Office Range dùng chung **một khuôn mẫu câu cố định** trên toàn site BFC — đã xác nhận khuôn mẫu giống hệt nhau bằng cách fetch trực tiếp 3 trang mẫu (Aviator, Junior, Classica Leva 2 groups):
   > "Espresso coffee machine(s) {N} group(s). Models: {control variants}. Copper boiler in {N} litre capacit(y/ies) with thermostatic circuit. {group head description}."

   Với 27 model còn lại trong 3 nhóm này, `description_en` được viết lại **theo đúng khuôn mẫu đã xác nhận**, điền bằng số liệu thật (groups/boiler/control/colors) đã cào trực tiếp từ trang riêng của từng model trong lần cào 14/07/2026 — không suy đoán số liệu, chỉ tái dựng câu theo công thức đã kiểm chứng.
2. **12 model** thuộc Coffee Grinder Dosers, Accessories và 2 model legacy có văn phong marketing khác nhau theo từng sản phẩm (không theo công thức) — đã **fetch riêng từng trang** để lấy nguyên văn mô tả (BFC 58E/64A/64E/83A/83E, Cleaner, Cleaner Plus, Deep drip tray, Waste drawer, Cup warmer, E5P, E6P).

---

## 6. Những gì KHÔNG có — và lý do

| Không có | Lý do |
|---|---|
| `image_url` cho E5P / E6P | Trang chỉ có bản tiếng Tây Ban Nha, không tìm thấy ảnh sản phẩm khả dụng qua fetch (khác các model hiện hành luôn có ảnh `wp-content/uploads/...`). Để trống thay vì đoán/dùng ảnh model khác. |
| `seo_description` cho toàn bộ 42 model | Xác nhận qua fetch trực tiếp: site không đặt thẻ meta description trên bất kỳ trang nào. |
| Kích thước/trọng lượng tổng thể của máy pha cà phê (Traditional/Lever/Home-Office) | Các trang này chỉ công bố "boiler capacity" + "group weight" dạng text; kích thước WxDxH tổng thể nằm trong ảnh kỹ thuật/PDF, không trích xuất được thành text qua fetch. |
| Giá bán | BFC không công bố giá công khai trên website (thông lệ B2B thiết bị pha chế chuyên nghiệp). |
| Nhà phân phối BFC tại Việt Nam | Ngoài phạm vi lần khảo sát này. |
| Xác nhận tình trạng sản xuất thật của E5P/E6P | Chỉ suy luận từ việc trang bị rút khỏi menu điều hướng + không có bản dịch EN/IT — chưa liên hệ hãng để xác nhận 100% đã ngưng sản xuất hay chỉ là lỗi kỹ thuật của site. |

---

## 7. Ghi chú kỹ thuật khi import

- `origin` = `"Italy"` và `heat_source` = `"electric"` cho toàn bộ 42 model (BFC sản xuất tại Scomigo di Conegliano, Treviso, Ý; toàn bộ máy pha cà phê và phụ kiện đều dùng điện).
- **Deep Black RS**: ảnh nguồn dùng tên file `Lira-Deep-Black-RS`, nghi là biến thể của dòng Lira chứ không phải model hoàn toàn độc lập — giữ nguyên ghi chú cảnh báo trong `specs.Note`, chưa xác nhận 100%.
- **Monza-Imola-Maranello**: xác nhận lại là 1 dòng sản phẩm, 3 tên gọi theo số group (Monza=2gr, Imola=3gr, Maranello=4gr) — không tách thành 3 model riêng.
- **E5P / E6P**: đưa vào nhóm riêng, có `group_note` giải thích rõ tình trạng "mồ côi" (orphaned) — khuyến nghị **không đưa lên site bán hàng thật** cho đến khi xác nhận với hãng, chỉ dùng để tham khảo nội bộ.
- File đã kiểm tra hợp lệ JSON UTF-8 (`python -m json.load` + đếm nhóm/model xác nhận đúng 6 nhóm / 42 model tại thời điểm khảo sát 18/07/2026 — xem mục 8 để biết số liệu hiện hành sau hiệu đính 19/07/2026).
- **Không đụng `data.json`/`import.csv`**, không import WordPress, không chạy WP-CLI.

---

## 8. Hiệu đính 19/07/2026 — loại bỏ E5P/E6P (gán nhầm brand Ceado)

**Vấn đề:** ở lần khảo sát 18/07/2026, 2 trang `https://www.bfcsrl.it/es/productos/e5p/` và `.../e6p/` được đưa vào `data-full.json` dưới nhóm "Legacy / Orphaned Grinder Pages" với suy đoán là "model tiền nhiệm của BFC 64E/64A". Suy đoán này **sai**.

**Xác minh lại (audit Google):** E5P và E6P là 2 model máy xay cà phê định lượng (dosing grinder) của hãng **Ceado** — một nhà sản xuất thiết bị pha chế cà phê khác của Ý, không liên quan đến BFC srl. Việc 2 URL này nằm trên domain `bfcsrl.it` (chỉ có bản tiếng Tây Ban Nha, không có bản EN/IT, không nằm trong menu điều hướng hiện hành) là dấu hiệu dữ liệu bị trộn lẫn/gán nhầm brand ở bước cào sitemap trước — không phải sản phẩm chính hãng BFC.

**Nguồn xác minh chính hãng BFC:** [https://www.bfcsrl.it/en/product_category/coffee-grinder-dosers/](https://www.bfcsrl.it/en/product_category/coffee-grinder-dosers/) — trang category grinder-doser chính thức của BFC chỉ liệt kê đúng **5 model**: BFC 58E, BFC 64A, BFC 64E, BFC 83A, BFC 83E. Không có E5P/E6P.

**Hành động đã thực hiện trong `data-full.json`:**
1. Xóa toàn bộ nhóm `"Legacy / Orphaned Grinder Pages (not in current navigation)"` cùng 2 entry E5P, E6P (đây là toàn bộ nội dung của nhóm đó nên xóa cả nhóm).
2. Cập nhật `total_model_count` từ `42` → `40`.
3. Cập nhật `total_model_count_breakdown` để phản ánh đúng 5 nhóm / 40 model hiện hành.
4. Thêm trường `removed_entries_note` ghi lại lý do loại bỏ và nguồn xác minh, để tra cứu về sau.
5. Xác nhận lại 5 model BFC 58E/64A/64E/83A/83E trong nhóm "Coffee Grinder Dosers" đã đầy đủ `title`, `description_en`, `specs`, `image_url` (ảnh thật từ `wp-content/uploads/...`) và `source_url` (trỏ về `/en/products/bfc-58e/`, `/bfc-64a/`, `/bfc-64e/`, `/bfc-83a/`, `/83e/`) — không cần bổ sung gì thêm.

**Số liệu hiện hành sau hiệu đính:** `data-full.json` có **5 nhóm, 40 model** (khớp 100% với `data.json` gốc). Không có model nào bị bịa thêm số liệu trong quá trình hiệu đính này — chỉ loại bỏ 2 entry sai brand.
