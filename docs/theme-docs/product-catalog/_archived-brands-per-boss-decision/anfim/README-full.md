# Anfim (Ý) — Catalog đầy đủ (`data-full.json`)

**Ngày khảo sát:** 19/07/2026 (cập nhật lần 2, mở rộng từ bản 18/07/2026)
**Brand string:** `Anfim`
**File dữ liệu:** `data-full.json` (2 nhóm · 20 model)

> ⚠️ File này **độc lập** với `data.json`. `data.json` là bản đã import làm draft trước đó (research_date 2026-07-14, 14 model) và **không bị đụng tới** trong bất kỳ lần cập nhật nào.

---

## 1. Nguồn dữ liệu

Duy nhất một nguồn: website chính hãng **https://www.anfim-milano.com/en**. Anfim không chặn crawl — dùng WebFetch + `curl` để đọc thẳng HTML, kể cả thẻ `<title>`, `<meta name="description">` và `<img>` gốc, không suy đoán từ trí nhớ.

| Trang | Vai trò |
|---|---|
| `/en/product-list` | Menu chính thức "Products" — nguồn xác nhận 4 model **đang bán**. |
| `/en/taxonomy/term/2` (trang mặc định + `?page=1`) | Danh mục "Professional Grinder" — 2 trang, 17 model (đã cào đủ từ lần trước). |
| `/en/taxonomy/term/1` | Danh mục **"Semi Professional Grinder"** — **danh mục hoàn toàn khác, chưa từng cào ở lần trước**, chỉ 1 trang không phân trang, 3 model. |
| `/en/product/<slug>` (20 trang) | Từng trang model — mô tả thật, bảng thông số, `<title>`, `<meta name="description">`, `<img>` gốc. |

---

## 2. Phát hiện chính so với bản 2026-07-18 (17 model)

Bản trước chỉ cào `/en/taxonomy/term/2` ("Professional Grinder"). Lần này chủ động dò thêm các taxonomy term khác (`term/1`, `term/3`...`term/6`) và phát hiện `term/1` = **"Semi Professional Grinder"**, một danh mục sản phẩm riêng biệt với 3 model ngừng bán chưa từng có trong dữ liệu:

| Model | Slug | Ghi chú |
|---|---|---|
| **SUPER BEST** | `/en/product/super-best` | 54mm hardened steel, doser, 1kg/ngày, static tamper |
| **BEST ON DEMAND EL ST** | `/en/product/best-demand-el-st` | 54mm, doserless/on-demand, hẹn giờ điện tử đơn/đôi |
| **KS / KS-T** | `/en/product/ks-ks-t` | 50mm, on-demand tự động khi lắp portafilter; **1 trang gộp chung cả 2 biến thể KS và KS-T** (site không có URL riêng cho từng biến thể — đã thử `/en/product/ks` và `/en/product/ks-t`, cả 2 đều HTTP 404) |

Cả 3 model đều: HTTP 200, `<title>` thật dạng `"TÊN | Anfim"`, `<meta name="description">` = `"No longer available"`, `<img>` gốc xác nhận HTTP 200 qua `curl`.

**Đã xác nhận KHÔNG tồn tại** các model mà một audit trước nghi ngờ bị thiếu: **CODY II, HAUS, HAUS SELF**. Thử trực tiếp các slug `/en/product/cody-ii`, `/en/product/haus`, `/en/product/haus-self` đều trả về **HTTP 404**, và không xuất hiện ở bất kỳ trang taxonomy nào (đã dò `term/1` đến `term/6`; `term/3`–`term/6` là các trang tin tức/landing không liên quan sản phẩm). Các model BEST, SUPER CAIMANO, CAIMANO, SCODY II đã có sẵn trong dữ liệu từ trước — không phải model mới.

**Không có model mới nào trong nhóm đang bán** — `/en/product-list` tại 19/07/2026 vẫn chỉ hiển thị đúng 4 model (Alba, Luna, Pratica, Solida).

---

## 3. Thống kê

| Chỉ số | Số lượng |
|---|---|
| Nhóm (`groups`) | **2** |
| Model | **20** (tăng từ 17 → 20, +3) |
| Model có `image_url` xác nhận HTTP 200 | **20/20** |
| Model có `specs` thật (≥5 trường) | **20/20** |
| Model có `source_url` xác nhận HTTP 200 | **20/20** |
| Mã model trùng lặp | 0 |

| # | Nhóm (EN) | Nhóm (VI) | Model |
|---|---|---|---|
| 1 | Currently Sold (2026 lineup) | Dòng đang bán chính thức | 4 |
| 2 | Discontinued (page still live, 'No longer available') | Model ngừng bán | 16 |

---

## 4. Mô tả sản phẩm (`description_en`)

3 model mới (SUPER BEST, BEST ON DEMAND EL ST, KS / KS-T) được viết dựa trên đoạn văn bản mô tả THẬT trên trang sản phẩm, đúng phương pháp đã dùng cho 17 model trước — không bịa số liệu, không suy đoán từ trí nhớ.

## 5. SEO fields — nguyên văn từ site, KHÔNG dịch

- **`seo_title`**: nguyên văn thẻ `<title>` thật, dạng `"TÊN MODEL | Anfim"` — xác nhận qua `curl` cho cả 3 trang mới.
- **`seo_description`**: nguyên văn thẻ `<meta name="description">` thật — cả 3 model mới đều ngừng bán nên site đặt sẵn `"No longer available"`, giữ nguyên văn.

## 6. `origin` / `heat_source`

Tất cả 20 model: `origin: "Italy"`, `heat_source: "electric"`.

---

## 7. Những gì KHÔNG có / vấn đề cần review

| Vấn đề | Chi tiết |
|---|---|
| **Giá bán** | Anfim không công bố giá công khai (thông lệ B2B ngành pha chế). Cần liên hệ hãng/đại lý. |
| **Nhà phân phối Việt Nam** | Trang `/en/dealer-search-form` có "Vietnam" trong dropdown nhưng là form tương tác — không cào tĩnh được tên đại lý cụ thể. Chưa xác nhận. |
| **Ảnh sản phẩm** | Có URL `<img>` gốc thật cho cả 20 model (đã xác nhận HTTP 200), nhưng **chưa tải file về máy** — vẫn hotlink thẳng server anfim-milano.com. |
| **16 model ngừng bán** | Không nên đưa lên site như hàng đang bán mới — chỉ tham khảo lịch sử dòng sản phẩm / hỗ trợ khách hàng cũ. |
| **KS / KS-T** | Một entry JSON đại diện cho 2 biến thể (site gộp chung 1 trang) — nếu cần hiển thị tách riêng trên site Saigonhoreca, cần agent người dùng quyết định cách tách vì nguồn không có 2 URL riêng. |
| **CODY II / HAUS / HAUS SELF** | Xác nhận KHÔNG tồn tại trên anfim-milano.com — audit trước nêu các tên này là sai/không có thật. |
| **"Micro"** | Xác nhận lại lần nữa: không tồn tại trong lineup Anfim hiện tại lẫn lịch sử. |
| **Taxonomy term khác** | `term/3` (caffeine), `term/4` (coffeelove), `term/5` (coffeemug), `term/6` (News) — đã kiểm tra, không chứa sản phẩm nào. |

Không có `data.json` hay `import.csv` nào bị chỉnh sửa trong quá trình này.
