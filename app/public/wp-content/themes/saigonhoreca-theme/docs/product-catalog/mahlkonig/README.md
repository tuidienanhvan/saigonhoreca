# Mahlkönig (Đức) — Nghiên cứu danh mục sản phẩm

Nguồn: website chính hãng Mahlkönig — bản US Shopify store `www.mahlkoenig.us` (có bán trực tiếp, giá công khai), đối chiếu thêm với `www.mahlkoenig.com` (bản Đức/EU, chỉ ship nội địa Đức, tự động điều hướng khách Mỹ sang `.us`).
Ngày cào dữ liệu: 2026-07-14 (crawl từ đầu — site chưa từng được nghiên cứu trước đó). Dữ liệu máy đọc song song ở [`data.json`](./data.json).

## Vì sao có tài liệu này

Site saigonhoreca.local hiện có **0 sản phẩm** dưới brand Mahlkönig — brand chỉ tồn tại dưới dạng **logo đối tác** ở mục "Đối tác" trang chủ, chưa có bất kỳ model nào trong catalog thực tế. Đây là khoảng trống 100%, cần xây dựng danh mục từ đầu bằng dữ liệu thật cào từ site chính hãng (mahlkoenig.us không chặn crawl — WebFetch đọc được bình thường).

## Về Mahlkönig

Mahlkönig GmbH & Co. KG thành lập **1924** tại **Hamburg, Đức**. Tự nhận là "The King of Coffee Grinders" — chuyên sản xuất đĩa nghiền (burr), là nhà sản xuất đĩa nghiền phẳng (flat burr) hàng đầu thế giới. Thuộc **Hemro Group**, cùng tập đoàn với các thương hiệu máy xay khác: Anfim (Ý), HeyCafé, Ditting (Thụy Sĩ) — các brand này xuất hiện chung trên site mahlkoenig.us dưới mục "Brand Portfolio" nhưng **không phải sản phẩm Mahlkönig thật**, đã loại khỏi danh sách bên dưới. ([Nguồn](https://www.mahlkoenig.us/pages/team-about))

**Khác biệt lớn so với Giorik:** Mahlkönig **bán trực tiếp online** (Shopify store, có giá công khai bằng USD, nút mua ngay, hiển thị tồn kho) — không phải mô hình B2B kín giá như Giorik.

## 1. Commercial Espresso Grinders — Grind-by-Sync (6 model)

Dòng máy xay espresso thương mại, phân biệt theo đường kính đĩa nghiền (65mm/80mm) và kiểu định lượng: **W** = Grind-by-Weight (cân điện tử), **T** = Timed (định lượng theo thời gian), **S** = Stepless cơ bản (không kết nối app).

| Model | Đĩa nghiền | Công suất | Hopper | Giá (USD) | Ghi chú |
|---|---|---|---|---|---|
| **E65W GbS** | 65mm | 580W (110-127V) | ~1200g | $3,099 | Grind-by-Weight |
| **E65T GbS** | 65mm | 580W (110-127V) | ~1200g | $2,599 | Định lượng theo thời gian |
| **E80W GbS** | 80mm | 845W (110-127V) | ~1800g | $4,199 | Grind-by-Weight, lưu lượng cao |
| **E80T GbS** | 80mm | 845W (110-127V) | ~1800g | $3,399 | Định lượng theo thời gian, lưu lượng cao |
| **E80S** | 80mm | 845W (110-127V) | ~1800g | $2,439 | Không menu chính; đang **hết hàng** tại thời điểm cào |
| **E65S** | 65mm | 580W (110-127V) | ~1200g | *Không công khai* | Không menu chính; lưu 6 công thức xay |

Nguồn: [Grinders collection](https://www.mahlkoenig.us/collections/grinders) · [Grind-by-Sync overview](https://www.mahlkoenig.us/pages/grind-by-sync-espresso-grinders) · từng trang model trong `data.json` → `groups[0]`.

Lưu ý: **E80S và E65S không xuất hiện** trong menu điều hướng chính (`/collections/all`) dù trang sản phẩm vẫn truy cập được — chưa rõ đây là model đang rút khỏi lineup, sắp tái ra mắt, hay lỗi liên kết menu. Gắn cờ chưa giải quyết.

## 2. Allround / Shop Grinders (4 model)

Dòng máy xay đa năng cho phin/filter/cold brew/espresso, đĩa nghiền lớn (71-98mm), công suất cao — dùng cho quán cà phê rang xay bán lẻ, quầy pha chế lưu lượng lớn.

| Model | Đĩa nghiền | Công suất | Giá (USD) | Ghi chú |
|---|---|---|---|---|
| **EK43** | 98mm cast steel, gắn dọc | 1300W | $3,899 | Model biểu tượng — "the undisputed queen of coffee grinders" |
| **EK43 S** | 98mm cast steel | 1300W | $3,899 | Thân máy nhỏ gọn hơn (compact), cùng lõi xay |
| **EK Omnia** | 98mm cast steel (SX Đức) | 750W BLDC | *Không công khai* | Flagship mới nhất, đạt iF Design Award 2024 + UX Design Award Gold; màn cảm ứng, Library Mode 10 công thức |
| **Guatemala** (2.0) | 71mm flat, gắn dọc | 1100W | $2,399 | 20 nấc cố định, espresso tới cold brew |

Nguồn: [Grinders collection](https://www.mahlkoenig.us/collections/grinders) · từng trang model trong `data.json` → `groups[1]`.

## 3. Home Grinders (3 model)

Dòng máy xay gia dụng, thiết kế countertop nhỏ gọn cho barista tại nhà — **duy nhất nhóm này** có mặt song song trên cả site `.us` và `.com` (Đức).

| Model | Đĩa nghiền | Công suất | Giá (USD) | Đánh giá | Ghi chú |
|---|---|---|---|---|---|
| **E64 WS** | 64mm | 200W | $1,199 | — | Grind-by-Weight (chính xác 0.1g) + Grind-by-Sync |
| **X64 SD** | 64mm | 200W | $599 | 4.45/5.0 (59 reviews) | Single-dose, zero retention |
| **X54** | 54mm | 120W | $599 - $649 | 4.68/5.0 (112 reviews) | Allround, 3 màu (Black/White/Black Chrome) |

Nguồn: [Home Grinders collection](https://www.mahlkoenig.us/collections/home-grinder) · [mahlkoenig.com](https://www.mahlkoenig.com/) · từng trang model trong `data.json` → `groups[2]`.

## 4. Model cũ đã ngừng bán (chỉ còn phụ tùng) — KHÔNG đưa vào catalog mới

Xác nhận qua WebSearch (chưa WebFetch riêng từng trang collection phụ tùng):

- **K30 2.0**, **K30 ES / VARIO**, **K30 VARIO Air**, **K30 TWIN**, **PEAK**, **EKK43** — tất cả không còn bán, chỉ còn trang phụ tùng thay thế.
- Nhắc tới thêm "VTA 6S" và "Tanzania" trong tóm tắt trang spare-parts nhưng **chưa xác minh độc lập**.

Ghi chú này chỉ để tránh nhầm lẫn nếu khách hàng cũ hỏi phụ tùng — không phải sản phẩm cần lên site mới.

## Thương hiệu "chị em" cùng Hemro Group — đã loại khỏi danh sách

mahlkoenig.us có mục "Hemro Group Brand Portfolio" bán các thương hiệu cùng tập đoàn nhưng KHÔNG mang tên Mahlkönig: **Anfim** (Luna, Alba), **HeyCafé** (Buddy, Jack, HC-880 LAB, HC-880 LAB S), **Ditting** (807, 807 Filter, 1203, 1827 Industrial). Đã loại khỏi `data.json` vì không phải sản phẩm Mahlkönig thật — nhưng ghi chú lại vì site saigonhoreca.local đã có thể có (hoặc sẽ cân nhắc) các brand đối tác này riêng.

Nguồn: [Hemro Group Brand Portfolio](https://www.mahlkoenig.us/collections/hemro-group-brand-portfolio)

## Giá bán

**Khác Giorik** — Mahlkönig công bố giá bán lẻ công khai bằng USD trên mahlkoenig.us cho hầu hết model (bán trực tiếp qua Shopify, có nút mua, hiển thị tồn kho). Ngoại lệ: **E65S** và **EK Omnia** không hiển thị giá — cần liên hệ đại lý. Giá là giá bán lẻ tại Mỹ, **không phải giá thị trường Việt Nam** — cần quy đổi tỷ giá + cộng thuế nhập khẩu + phí vận chuyển + biên lợi nhuận trước khi đăng giá VNĐ lên site.

## Thị trường Việt Nam — CHƯA xác minh

Không tìm thấy site khu vực Việt Nam/Đông Nam Á hoặc trang phân phối chính hãng của Mahlkönig cho Việt Nam. Site `.com` (Đức) chỉ ship nội địa Đức; site `.us` (Mỹ) không đề cập vận chuyển quốc tế tới VN. Trang nav "Distributors" (trên bản `.com`) chưa được fetch riêng trong lần cào này.

## SEO — Ghi chú quan trọng

Đã cố gắng trích **meta description** cho cả 11 model hiện hành nhưng **KHÔNG tìm thấy thẻ `<meta name="description">` hoặc `og:description`** trong nội dung trả về qua WebFetch cho bất kỳ trang nào (đã thử fetch trực tiếp yêu cầu raw HTML source cho model EK43 để xác nhận — công cụ báo không tìm thấy thẻ này trong nội dung nhận được). Có 2 khả năng: (1) trang thực sự không đặt thẻ meta description riêng (dựa vào Shopify auto-generate hoặc để trống), hoặc (2) công cụ WebFetch converts HTML→markdown làm mất thẻ `<head>`. **Cần kiểm tra lại bằng view-source thủ công** (hoặc trình duyệt thật) trước khi kết luận — hiện tại field `seo_description` trong `data.json` để `null` kèm ghi chú gap, KHÔNG tự bịa nội dung.

`seo_title` (thẻ `<title>`) đã lấy được đầy đủ cho tất cả 11 model — theo đúng format `{Tên model} | Mahlkönig USA`.

Ảnh sản phẩm (`image_url`) lấy từ CDN Shopify chính hãng (`mahlkoenig.us/cdn/shop/files/...`) — là ảnh sản phẩm thật (không phải logo), nhưng **chưa xác nhận quyền sử dụng thương mại** trên site reseller bên thứ ba — cần xin phép hoặc bộ ảnh catalog chính thức từ Mahlkönig/Hemro Group trước khi dùng.

## Việc còn thiếu

- Meta description SEO cho toàn bộ 11 model — không tìm thấy qua crawl, cần kiểm tra thủ công hoặc tự soạn khi đăng sản phẩm.
- E80S/E65S không rõ tình trạng lineup (không có trong menu chính); E80S đang hết hàng.
- Giá E65S và EK Omnia không công khai — cần liên hệ Mahlkönig/đại lý.
- Chưa xác nhận nhà phân phối chính hãng Mahlkönig tại Việt Nam.
- "VTA 6S" và "Tanzania" (tên model cũ) chưa xác minh độc lập.
- Chưa có công thức quy đổi giá USD → VNĐ (thuế nhập khẩu, vận chuyển, biên lợi nhuận) để đăng giá lên site.
- Chưa xác nhận quyền sử dụng ảnh sản phẩm chính hãng cho mục đích thương mại trên site reseller.
