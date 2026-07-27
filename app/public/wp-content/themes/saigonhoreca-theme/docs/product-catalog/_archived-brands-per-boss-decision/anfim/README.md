# Anfim (Ý) — Nghiên cứu danh mục sản phẩm (fresh crawl)

Nguồn: website chính hãng Anfim S.r.l. (Ý) — `www.anfim-milano.com` (bản tiếng Anh `/en/`).
Ngày cào dữ liệu: 2026-07-14. Dữ liệu máy đọc song song ở [`data.json`](./data.json).

## Vì sao có tài liệu này

Site saigonhoreca.local hiện có brand **Anfim CHỈ dưới dạng logo đối tác** ở trang chủ (mục "Đối tác") — **0 sản phẩm Anfim** trong danh mục thực tế. Đây là nghiên cứu nền tảng (từ đầu, không dùng dữ liệu cũ) để có thể bổ sung sản phẩm thật lên site, theo đúng phong cách tài liệu đã dùng cho Giorik-Italia.

Anfim-milano.com KHÔNG chặn crawl — đã cào trực tiếp qua WebFetch + `curl` (đọc thẳng HTML, kể cả thẻ `<meta>` và `<img>` gốc) thay vì suy đoán từ trí nhớ.

## Về Anfim

**Anfim S.r.l.** (thành viên **Hemro Group** / Hemro AG, Thụy Sĩ — cùng tập đoàn với Mahlkönig, Ditting, Ceado...), thành lập **1932** tại **Milano, Ý**. Nhà máy + showroom gần trung tâm Milano. Tự thiết kế và sản xuất lưỡi nghiền (burr) trong nhà. Năm 2024 Anfim làm mới thương hiệu + website hoàn toàn. ([Nguồn](https://www.anfim-milano.com/en/about-us))

## Lineup hiện tại — chỉ 4 model đang bán chính thức

Trang menu chính thức **Products** (`/en/product-list`) hiển thị **đúng 4 model** đang bán tại thời điểm cào (2026-07-14):

| Model | Vai trò | Lưỡi nghiền | Dosing | Motor | Giá đang bán? |
|---|---|---|---|---|---|
| **ALBA** | Flagship, tốc độ cao | 80mm phẳng, thép Đức | On-demand, màn cảm ứng 3.5", 3 công thức | 700-900W | Không công khai |
| **LUNA** | Best value | 65mm phẳng, thép Đức | On-demand/doserless, màn cảm ứng 3.5", 3 công thức | 440-700W (tùy điện áp) | Không công khai |
| **PRATICA** | Kiểu Ý truyền thống + on-demand | 65mm phẳng, thép tôi | On-demand, 6 công thức lập trình | 440-580W | Không công khai |
| **SOLIDA** | Doser cơ truyền thống | 65mm phẳng, thép tôi | Doser (chia liều cơ) 6-9 g/s | 440-510W | Không công khai |

Chi tiết specs đầy đủ (RPM, hopper, kích thước, cân nặng, điện áp, màu sắc) nằm trong `data.json` → `groups[0].models`.

Nguồn: [Product List](https://www.anfim-milano.com/en/product-list) · [Alba](https://www.anfim-milano.com/en/product/alba) · [Luna](https://www.anfim-milano.com/en/product/luna) · [Pratica](https://www.anfim-milano.com/en/product/pratica) · [Solida](https://www.anfim-milano.com/en/product/solida)

## Model đã ngừng bán (trang vẫn công khai, ghi "No longer available")

Dò qua trang taxonomy `/en/taxonomy/term/2` + thử slug pattern bằng `curl` (HTTP 200 = trang còn tồn tại) phát hiện thêm **10 model ngừng bán** — Anfim giữ các trang này để hỗ trợ dịch vụ/phụ tùng khách hàng cũ qua đại lý, KHÔNG phải hàng đang bán mới:

| Model | Lưỡi nghiền | Dosing | Công suất/ngày | Ghi chú |
|---|---|---|---|---|
| SP II+ | 75mm phủ titan | On-demand (cảm biến tiệm cận) | 5-6 kg | Flagship cũ, 720W, IP55 cooling |
| SP II | 75mm phủ titan | — | 5-6 kg | Tiền nhiệm SP II+ |
| CAIMANO ON DEMAND | 64mm thép tôi phủ titan | Doserless/on-demand | 1-2 kg | Quán café khối lượng thấp |
| CAIMANO | 64mm thép tôi | Doser | 1-2 kg | Quán café/xe cà phê lưu động |
| SUPER CAIMANO | 75mm thép tôi phủ titan | Doser + đếm shot | 2-4 kg | **Xác nhận có thật** (user có nhắc trong trí nhớ) — nhưng đã ngừng bán |
| CAIMANO SPECIAL | 75mm SP thép tôi | Doser | 4-10 kg (~1,400 ly) | "High-volume workhorse" |
| CAIMANO DROGHERIA | 75 SP thép tôi | Xay ra túi bán lẻ | 30 kg/giờ | Máy bán lẻ tạp hóa, không phải grinder pha tại quầy |
| SCODY II | 75mm thép tôi phẳng | On-demand tự động khi lắp portafilter | 2-3 kg | Vòi chống vón cục |
| BEST ON DEMAND EL | 54mm thép | Doserless/on-demand | ~1.5 g/s | Nhỏ gọn, không gian hẹp |
| BEST | 54mm thép tôi | Doser | 1 kg | Nhỏ gọn, không gian hẹp |

Chi tiết specs đầy đủ nằm trong `data.json` → `groups[1].models`.

**Lưu ý quan trọng:** model "Micro" mà có thể được nhớ tới **KHÔNG tồn tại** trong lineup Anfim (hiện tại lẫn lịch sử) — đã dò toàn bộ trang product-list + taxonomy + thử nhiều slug qua `curl`, không ra kết quả. Có thể nhầm với brand khác. "Super Caimano" thì **có thật** nhưng đã ngừng bán.

Nguồn: [Taxonomy — Professional Grinder](https://www.anfim-milano.com/en/taxonomy/term/2) · [SP II+](https://www.anfim-milano.com/en/product/sp-ii-0) · [SP II](https://www.anfim-milano.com/en/product/sp-ii) · [Caimano On Demand](https://www.anfim-milano.com/en/product/caimano-demand) · [Caimano](https://www.anfim-milano.com/en/product/caimano) · [Super Caimano](https://www.anfim-milano.com/en/product/super-caimano) · [Caimano Special](https://www.anfim-milano.com/en/product/caimano-special) · [Caimano Drogheria](https://www.anfim-milano.com/en/product/caimano-drogheria) · [Scody II](https://www.anfim-milano.com/en/product/scody-ii) · [Best On Demand EL](https://www.anfim-milano.com/en/product/best-demand-el) · [Best](https://www.anfim-milano.com/en/product/best)

## Dữ liệu SEO (per-model)

Mỗi model trong `data.json` có object `seo` với `seo_title`, `seo_description`, `image_url`.

- **`seo_title`**: lấy NGUYÊN VĂN từ thẻ `<title>` thật trên trang (xác nhận qua `curl`), dạng `"TÊN MODEL | Anfim"`.
- **`seo_description`**: **CẢNH BÁO QUAN TRỌNG** — kiểm tra trực tiếp HTML `<head>` của toàn bộ 14 trang sản phẩm cho thấy Anfim-milano.com có thẻ `<meta name="description">` **hầu như rỗng** (chỉ khoảng trắng, hoặc với model ngừng bán chỉ ghi "No longer available"). Không có thẻ `og:description`/`og:image` trên các trang này. Vì vậy `seo_description` trong file này **KHÔNG phải trích xuất nguyên văn từ site** — do agent tự viết mới dựa trên nội dung mô tả sản phẩm thật, cần biên tập lại giọng văn SEO tiếng Việt trước khi publish.
- **`image_url`**: URL ảnh sản phẩm **thật** lấy từ thẻ `<img>` gốc trên chính trang model đó (đã verify qua `curl` từng trang, không lấy nhầm ảnh sản phẩm liên quan ở sidebar). KHÔNG phải logo — toàn bộ 14 model đều có ảnh xác nhận.

## Giá bán

Anfim **KHÔNG công bố giá bán công khai** trên website chính hãng (thông lệ ngành thiết bị pha chế B2B, giống Giorik). Cần liên hệ Anfim (qua `/en/form/contact`) hoặc đại lý phân phối để lấy báo giá.

## Thị trường Việt Nam — CHƯA xác minh

Trang `/en/dealer-search-form` **có** "Vietnam" trong danh sách quốc gia thả xuống, Anfim tự nhận "works with direct distributors and service partners in over 70 countries" — nhưng đây là form tương tác (chọn quốc gia → submit), WebFetch/curl tĩnh không thực thi được để lấy tên đại lý cụ thể. **Chưa xác nhận** nhà phân phối chính hãng Anfim tại Việt Nam.

## Việc còn thiếu / gaps

- **Model "Micro"** không tồn tại trong lineup Anfim — cần user xác nhận lại tên nếu vẫn cần tìm (có thể nhầm brand).
- **Nhà phân phối chính hãng tại Việt Nam** — chưa xác nhận (form cần thao tác trình duyệt thật, không cào tĩnh được).
- **Giá bán** — không công khai, cần liên hệ Anfim/đại lý.
- **Ảnh sản phẩm** — mới có URL trỏ thẳng server anfim-milano.com cho cả 14 model, CHƯA tải file về máy. Cần tải + xin phép sử dụng nếu định host lại trên saigonhoreca.local.
- **Meta description SEO** — site gốc để trống gần như hoàn toàn; `seo_description` trong `data.json` là nội dung tự viết mới, cần biên tập trước khi publish.
- **10 model ngừng bán** — không nên đưa lên site như hàng đang bán mới; chỉ giữ tham khảo lịch sử dòng sản phẩm hoặc nếu cần hỗ trợ khách hàng cũ/phụ tùng.
