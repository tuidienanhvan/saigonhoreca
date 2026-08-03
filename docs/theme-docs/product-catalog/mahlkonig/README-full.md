# Mahlkönig — Catalog đầy đủ (`data-full.json`)

**Ngày khảo sát:** 18-19/07/2026
**Brand string:** `Mahlkönig`
**File dữ liệu:** `data-full.json` (5 nhóm · 34 model)

> File này **độc lập** với `data.json`/`import.csv`. `data.json` là bản đã import làm draft trước đó (13 model, 4 nhóm) và **không bị đụng tới**.

---

## 1. Nguồn dữ liệu và phương pháp

| Bước | Cách làm |
|---|---|
| 1. Liệt kê toàn bộ URL sản phẩm | Đọc `sitemap_products_1.xml` (~351 URL `/products/`) + `/collections`, `/collections/all`, `/collections/grinders` — không chỉ dựa vào menu điều hướng chính, vì nhiều model (đặc biệt Legacy) bị ẩn khỏi nav nhưng trang vẫn sống. |
| 2. Lấy mô tả + thông số | WebFetch từng trang sản phẩm để đọc mô tả, bảng thông số kỹ thuật, trạng thái tồn kho/ngừng bán. |
| 3. Lấy chính xác thẻ SEO + giá | **curl trực tiếp HTML thô** (khắc phục lỗi của lần cào 2026-07-14: công cụ WebFetch tự convert HTML→markdown làm RỚT MẤT thẻ `<head>`) — trích đúng `<title>`, `<meta name="description">`, và giá JSON-LD `"price":"..."`. |

**Phát hiện quan trọng nhất:** lần cào trước (14/07) kết luận sai rằng site "không đặt meta description". Thực tế site **CÓ ĐẦY ĐỦ** — lỗi nằm ở công cụ cào (WebFetch bỏ `<head>` khi convert sang markdown), không phải ở site. Đã xác minh lại bằng `curl -A "Mozilla/5.0"` cho toàn bộ 34 model.

---

## 2. Thống kê

| Chỉ số | Số lượng |
|---|---|
| Nhóm (`groups`) | **5** |
| Model | **34** |
| Model có `image_url` | **34 / 34** |
| Model có `specs` (≥3 trường) | **28 / 34** (6 model Legacy chỉ có specs rời rạc — xem mục 5) |
| Model có `seo_title` + `seo_description` thật (không phải `null`) | **34 / 34** |
| Model có giá công khai | **9 / 34** |

### Phân bổ theo nhóm

| # | Nhóm (EN) | Nhóm (VI) | Model | Ghi chú |
|---|---|---|---|---|
| 1 | Commercial Espresso Grinders (Grind-by-Sync) | Máy Xay Espresso Thương Mại (dòng Grind-by-Sync) | 6 | E65W/E65T/E80W/E80T GbS, E80S, E65S |
| 2 | Industrial Grinders | Máy Xay Công Nghiệp | **2 (MỚI)** | DK15, DK27 — chưa từng biết trước lần cào này |
| 3 | Allround / Shop Grinders | Máy Xay Đa Năng / Cửa Hàng | 4 | EK43, EK43 S, EK Omnia, Guatemala |
| 4 | Home Grinders | Máy Xay Gia Dụng | 3 | E64 WS, X64 SD, X54 |
| 5 | Legacy / Discontinued Models | Model Cũ Đã Ngừng Bán | **19 (MỚI, trước là 0)** | Xem mục 4 |

---

## 3. Nhóm mới phát hiện — Industrial Grinders (DK15, DK27)

Đây là nhóm **hoàn toàn không có** trong `data.json` cũ (13 model / 4 nhóm) lẫn trong 4 nhóm đã biết trước khi giao việc. Xác nhận là **model HIỆN HÀNH** (không phải legacy) vì xuất hiện trong danh mục "Current Professional Grinders" trên trang `/collections`, cùng nhóm với E65W/E80W GbS, EK43, Guatemala, E80S, E65S — không lẫn với danh mục "Legacy Professional Grinders".

- **DK15**: đĩa nghiền 130mm, động cơ 2200W 3 pha, công suất 105-200 kg/giờ, dùng cho xưởng rang cỡ nhỏ.
- **DK27**: đĩa nghiền 180mm, động cơ 5500W 3 pha, công suất 230-300 kg/giờ, dùng cho dây chuyền sản xuất tự động liên tục.

Cả hai đều **không công bố giá** (JSON-LD `price = 0.00`) — đúng với đặc điểm sản phẩm B2B công nghiệp, cần liên hệ đại lý.

---

## 4. Nhóm Legacy — từ 0 lên 19 model đã xác minh

Yêu cầu ban đầu ghi "Legacy / Discontinued Models — 0 model (CẦN cào nếu site còn liệt kê)". Lần cào 2026-07-14 trước đó chỉ liệt kê 6 tên model qua **WebSearch** (không WebFetch trực tiếp), và gắn cờ "VTA 6S", "Tanzania" là **CHƯA XÁC MINH**.

Lần deep-crawl này đã **WebFetch trực tiếp toàn bộ 19 trang sản phẩm Legacy còn sống** trên `mahlkoenig.us` — mỗi trang có banner rõ ràng dạng:

> *"The [Tên model] grinder model is no longer available. Image is for reference only."*

### Danh sách đầy đủ 19 model Legacy

| # | Model | Đĩa nghiền | Có specs đầy đủ? |
|---|---|---|---|
| 1 | PEAK | 80mm cast steel | Có |
| 2 | K30 2.0 | 65mm | Có |
| 3 | K30 VARIO Air | 65mm | Có |
| 4 | K30 TWIN | 2x65mm | Có |
| 5 | K30 TWIN 2.0 | 2x65mm | Có |
| 6 | K30 TWIN 2.0 Hybrid | 2x65mm | Có |
| 7 | K30 ES / VARIO | 65mm | Có |
| 8 | K60 ES | — | **Không** (chỉ mô tả tính năng) |
| 9 | EKK43 | 2x98mm cast steel | Có |
| 10 | VTA 6S | 120mm special steel | Có — **đã xác minh độc lập, trước đây chỉ là nghi vấn** |
| 11 | Tanzania | 80mm special steel | Có — **đã xác minh độc lập, trước đây chỉ là nghi vấn** |
| 12 | E65S GbW | 65mm | Có (tiền nhiệm của "E65S" hiện hành, khác slug) |
| 13 | E80S GbW | 80mm | Có (tiền nhiệm của "E80S" hiện hành, khác slug) |
| 14 | K501 | — | **Không** |
| 15 | FCG 2.0 | — | **Không** (chỉ có dung tích lập trình 2.2L) |
| 16 | FCG 6.0 | — | **Không** (chỉ có dung tích lập trình 6.0L) |
| 17 | ProM / ProM Espresso | 65mm | Một phần (burr + hopper, thiếu motor/RPM) |
| 18 | Vario Home | 54mm ceramic | Có |
| 19 | Kenia / Guatemala | — | **Không** (tiền nhiệm của "Guatemala" hiện hành) |

**6 model không có bảng thông số công khai trên trang** (K60 ES, K501, FCG 2.0, FCG 6.0, ProM/ProM Espresso, Kenia/Guatemala) — các trang này chỉ mô tả tính năng bằng lời, số liệu kỹ thuật chi tiết nằm trong PDF product sheet tải về mà lần cào này chưa trích xuất được nội dung PDF.

### Lưu ý quan trọng — dễ nhầm lẫn tên

- **E65S GbW** (Legacy, slug `/products/e65s-gbw`) ≠ **E65S** hiện hành (nhóm 1, slug `/products/e65s`). Hai sản phẩm khác nhau, chỉ trùng một phần tên.
- **E80S GbW** (Legacy, slug `/products/e80s-gbw-grinder`) ≠ **E80S** hiện hành (nhóm 1, slug `/products/e80s-grinder`).
- **Kenia / Guatemala** (Legacy) tự nhận trên trang là "*previous version of the Guatemala*" — tức là tiền nhiệm trực tiếp của model **Guatemala** hiện hành (nhóm 3, slug `guatemala-2-0`). Trang cũng nhắc tới cả model "K32" nhưng không có trang sản phẩm riêng — chưa xác minh độc lập.

---

## 5. Những gì KHÔNG bao gồm — và lý do

| Không có | Lý do |
|---|---|
| **~300 trang phụ tùng thay thế** (burr set, hopper, drip tray, power board, display unit, spout...) | Sitemap có ~351 URL `/products/`, đại đa số là phụ tùng rời, không phải máy xay hoàn chỉnh. Không đưa vào catalog 34 model — chỉ dùng để xác nhận chéo tên model tồn tại (ví dụ phụ tùng ghi "VTA 6S", "Tanzania" xác nhận 2 model này có thật). |
| **Merchandise** (mũ, áo, cốc "Prime Edit", "diner mug") | Không phải sản phẩm máy xay cà phê. |
| **Thông số kỹ thuật đầy đủ cho 6 model Legacy** | K60 ES, K501, FCG 2.0, FCG 6.0, ProM/ProM Espresso, Kenia/Guatemala — trang sản phẩm không công bố bảng thông số; chỉ có trong PDF chưa trích xuất. |
| **Giá bán tại Việt Nam** | Không có nguồn. Giá trong file (khi có) là giá niêm yết USD tại Mỹ. |
| **Thương hiệu "chị em" Hemro Group** | Anfim, HeyCafé, Ditting — cùng tập đoàn nhưng không mang tên Mahlkönig, đã loại khỏi danh sách (ghi chú ở cuối `data-full.json`). |
| **Trang phân phối Việt Nam** | Chưa fetch riêng trang "Distributors" trong lần cào này. |

---

## 6. Ghi chú kỹ thuật khi import

- `origin` = `Germany` cho tất cả 34 model (Mahlkönig sản xuất/thiết kế tại Đức, thuộc Hemro Group).
- `heat_source` = `electric` cho tất cả (máy xay cà phê chạy điện).
- `image_url` lấy từ CDN Shopify chính hãng (`mahlkoenig.us/cdn/shop/files/...`) cho toàn bộ 34/34 model — kể cả 19 model Legacy (ảnh sản phẩm thật do hãng lưu trữ để tham khảo lịch sử/phụ tùng, không phải placeholder).
- `seo_title`/`seo_description` là **nguyên văn thẻ `<title>`/`<meta name="description">` thật** lấy qua curl — không dịch, không bịa. Với 19 model Legacy, mô tả SEO có dạng chuẩn hoá "*[Tên model] grinder, legacy coffee grinder from Mahlkoenig*" — đây là nội dung thật trên trang, không phải do thiếu sót của công cụ cào.
- Giá (khi công khai) lấy từ JSON-LD `"price":"..."` trong HTML — đã xác nhận qua curl cho 15 model hiện hành: 9 model có giá > 0, 4 model (E65S, EK Omnia, DK15, DK27) trả về giá 0.00 = không công khai.
- Chưa xác nhận quyền sử dụng thương mại của ảnh sản phẩm trên site reseller bên thứ ba — cần xin phép hoặc bộ ảnh catalog chính thức từ Mahlkönig/Hemro Group trước khi dùng.

---

## 7. Việc còn thiếu (đưa vào `open_questions_for_user` trong `data-full.json`)

- Thông số kỹ thuật đầy đủ cho 6 model Legacy (K60 ES, K501, FCG 2.0, FCG 6.0, ProM/ProM Espresso, Kenia/Guatemala) — cần trích PDF product sheet riêng.
- Model "K32" nhắc tới trong mô tả Kenia/Guatemala — chưa có trang riêng để xác minh độc lập.
- Nhà phân phối chính hãng Mahlkönig tại Việt Nam — chưa xác nhận.
- Công thức quy đổi giá USD → VNĐ (thuế nhập khẩu, vận chuyển, biên lợi nhuận) — chưa có.
- Quyền sử dụng ảnh sản phẩm chính hãng cho mục đích thương mại trên site reseller — chưa xác nhận.
