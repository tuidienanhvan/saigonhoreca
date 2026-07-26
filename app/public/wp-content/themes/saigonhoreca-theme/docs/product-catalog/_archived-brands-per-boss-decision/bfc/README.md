# BFC (Ý) — Nghiên cứu danh mục sản phẩm (fresh crawl)

Nguồn: website chính hãng BFC srl (Ý) — `www.bfcsrl.it` (bản tiếng Anh `/en/`).
Ngày cào dữ liệu: 2026-07-14 (lần cào đầu tiên — trước đó BFC chưa từng được nghiên cứu). Dữ liệu máy đọc song song ở [`data.json`](./data.json).

## Vì sao có tài liệu này

Site saigonhoreca.local hiện brand **BFC chỉ tồn tại dưới dạng logo đối tác** ở mục "Đối tác" trang chủ — **0 sản phẩm** trong catalog. Client muốn nghiên cứu xem BFC có phải nhà sản xuất thật, tìm website chính hãng, và cào danh mục sản phẩm thật để chuẩn bị đưa lên site.

## Xác minh thương hiệu (quan trọng — "BFC" dễ nhầm)

"BFC" một mình rất mơ hồ (không phải tên hãng cà phê phổ biến, có thể trùng công ty ngành khác). Đã tìm kiếm nhiều truy vấn khác nhau ("BFC coffee machines Italy espresso", "BFC espresso machine manufacturer Italy") và xác nhận:

- **Tên đầy đủ:** BFC srl, viết tắt của **Brutti Fullin Costruzioni**
- **Website chính hãng:** https://www.bfcsrl.it/en/ (KHÔNG phải la-bfc.it hay bfc-espresso.com — 2 domain gợi ý ban đầu không tồn tại/không phải trang chính hãng)
- **Trụ sở & nhà máy:** Via Martiri Delle Foibe 15, 31015 Scomigo di Conegliano (Treviso), Ý — nhà máy 5.000 m², tự sản xuất nồi hơi đồng, ống nối, vỏ thép in-house
- **Quy mô:** 75 nhân viên, xuất khẩu 80% sản lượng. Theo Global Coffee Report (nguồn thứ cấp, phỏng vấn Marco Brutti, không phải nguồn chính hãng): năm 2017 sản xuất >15,000 máy/năm, top 5 nhà sản xuất máy espresso của Ý, xuất khẩu sang Mỹ/Úc/Bắc Phi/Á/Âu.
- **Năm thành lập:** nguồn thứ cấp ghi "thập niên 1950" nhưng trang About Us chính hãng KHÔNG nêu rõ — CHƯA xác minh 100%, gắn cờ trong data.json.

Nguồn: [Home](https://www.bfcsrl.it/en/) · [About Us](https://www.bfcsrl.it/en/about-us/) · [Global Coffee Report interview](https://www.gcrmag.com/speaking-with-marco-brutti-of-brutti-fullin-costruzioni-bfc-espresso-machin/) (thứ cấp)

## Tổng quan danh mục — 40 sản phẩm, 5 nhóm

| Nhóm | Số model | Mô tả ngắn |
|---|---|---|
| **Traditional Machines** | 19 | Máy quầy bar 1-4 group, nồi hơi đồng, điện tử/bán tự động nút/cần gạt |
| **Machine Lever** | 2 | Classica Leva 2gr/3gr — bán tự động cần gạt cổ điển |
| **Home/Office Range** | 9 | Máy 1 group compact cho gia đình/văn phòng, nồi hơi 0.8-2L |
| **Coffee Grinder Dosers** | 5 | Máy xay định lượng đĩa phẳng Ø58-83mm, hậu tố A=tự động, E=điện tử theo yêu cầu |
| **Accessories** | 5 | Cleaner/Cleaner Plus (vệ sinh tay cầm tự động), khay hứng sâu, ngăn bã, hâm ly |

**Tổng: 40 sản phẩm có URL nguồn rõ ràng** (35 nếu không tính 5 phụ kiện).

## 1. Traditional Machines (19 model)

Tất cả dùng nồi hơi đồng (copper boiler) + mạch nhiệt (thermostatic circuit); nhóm group truyền thống nặng 4.4kg hoặc kiểu ring nhẹ 1.5kg (tùy dòng). Biến thể điều khiển: điện tử (bàn phím lập trình/màn cảm ứng), bán tự động nút, hoặc bán tự động cần gạt.

| Model | Số group | Nồi hơi (L) | Điều khiển | Màu |
|---|---|---|---|---|
| Aviator | 2-3 | 14-21 | Điện tử, touch screen; tùy chọn T.C.I. + P.P.I. (5 profile áp suất) | Steel, White, Black |
| Aviator 1 group | 1 | 5 | Điện tử, touch screen; T.C.I. + P.P.I. | Steel |
| Aviator 5 Keys | 2-3 | 14-21 | Điện tử, touch screen | Steel, White, Black |
| Antelao | 2-3 | 14-21 | Điện tử, touch screen | — |
| Galileo | 2-3-4 | 11-14-21-27 | Điện tử/nút/cần gạt; tùy chọn T.C.I. + Turbo steamer | Steel, Black, White |
| Lira | 1-2SB-2-3-4 | 4-27 (tùy config) | Điện tử/nút/cần gạt; tùy chọn T.C.I. + Turbo steamer | Red, White, Black, Grey, Aluminum, Black&Wood |
| Lira S | 1-2-2SB-3-4 | 4-27 (tùy config) | Điện tử/touch/cần gạt | Red, White, Black |
| Deep Black RS | 2-3 | 14-21 | Điện tử | — (ảnh nguồn ghi "Lira-Deep-Black-RS", nghi là biến thể Lira) |
| Monza K | 2-3-4 | 14-21-27 | Điện tử/nút; tùy chọn T.C.I. | Steel, Black, Blue, White |
| Nuova Monza | 2-3 | 14-21 | Điện tử | — |
| Monza-Imola-Maranello | 2/3/4 | 14-21-27 | Điện tử/nút; tùy chọn T.C.I. | Steel, Blue |
| Classica | 1-2SB-2-3-4 | 4-27 (tùy config) | Điện tử/nút/cần gạt | Steel, Black |
| Classica Eva | 1-2-3-4 | 4-27 (tùy config) | Điện tử/nút/cần gạt | Steel |
| Classica Eva Plus | 2-3 | 14-21 | Điện tử | — |
| Chiara GiDi | 2 | 11 | Điện tử/touch | White |
| De Lux | 1-2-3-4 | 4-27 (tùy config) | Điện tử/nút/cần gạt; tùy chọn T.C.I. | Red, Black |
| De Lux RS | 2-3 | 14-21 | Điện tử/nút | Orange, Grey, White, Black |
| Opera 5 | 2-3 | 5-11-14 | Điện tử+display/nút | Black, White, Light Blue |
| Gran Doge | 2-3 (GD-2GR/GD-3GR) | 11-14-21 | Điện tử/nút | Black, Red |

Ghi chú: **Monza-Imola-Maranello** thực chất 1 dòng máy, 3 tên gọi theo số group (Monza=2gr, Imola=3gr, Maranello=4gr) — không phải 3 sản phẩm khác biệt.

Chi tiết đầy đủ + URL từng model nằm ở `data.json` → `groups[0]`.

## 2. Machine Lever (2 model)

| Model | Số group | Nồi hơi (L) |
|---|---|---|
| Classica Leva 2 groups | 2 | 14 |
| Classica Leva 3 groups | 3 | 21 |

Cả hai đều bán tự động cần gạt (semiautomatic lever), màu Steel.

## 3. Home/Office Range (9 model) — máy 1 group compact

| Model | Nồi hơi (L) | Điều khiển | Ghi chú |
|---|---|---|---|
| Experta | 1.3 | Cần gạt | Nhiều màu (Steel/White/Black kết hợp) |
| Junior | 1.3 | Điện tử/nút | Steel |
| Junior ELA | 1.3 | Nút | Steel |
| Junior Plus | 1.3 | Nút/cần gạt | Steel |
| Junior Elite | 1.3 | Cần gạt | Bơm motor điều chỉnh ngoài, chuyển nước máy/bình |
| Junior Extra | 2 | Cần gạt | Steel |
| Junior Extra TCI | 1.3 & 0.8 (2 nồi hơi riêng) | Cần gạt | T.C.I. — kiểm soát nhiệt riêng biệt |
| Junior Extra TCI MP | 1.3 & 0.8 | Cần gạt | T.C.I. + bơm motor, chuyển nước máy/bình |
| Perfetta | 0.8 | Cần gạt | Steel |

## 4. Coffee Grinder Dosers (5 model) — máy xay định lượng

| Model | Đĩa xay (mm) | Công suất | Phễu | Loại định lượng | Kích thước (cm) | Trọng lượng |
|---|---|---|---|---|---|---|
| BFC 58E | 58 (phẳng) | 300W | 500g | Điện tử, đơn/đôi liều theo nút | 18×23×41 | 8 kg |
| BFC 64A | 64 (phẳng) | 350W | 1.2 kg | Tự động, nghỉ mỗi 6 ly, chỉnh 5-9.5g | 21×38.5×59 | 12.5 kg |
| BFC 64E | 64 (phẳng) | 400W | 1.2 kg | Điện tử, đơn/đôi liều theo nút | 20×27×51 | 12.5 kg |
| BFC 83A | 83 (phẳng) | 650W | 1.2 kg | Tự động, nghỉ mỗi 6 ly, chỉnh 5-9.5g | 37×23×62 | 19 kg |
| BFC 83E | 83 (phẳng) | 650W | 1.2 kg | Điện tử, đơn/đôi liều theo nút | 23×30×62 | 19 kg |

Quy ước hậu tố: **A** = Automatic (tự động, dừng khi doser đầy), **E** = Electronic (theo yêu cầu, nút bấm đơn/đôi liều).

## 5. Accessories (5 model)

| Sản phẩm | Mô tả |
|---|---|
| Cleaner | Vệ sinh tay cầm (portafilter) tự động để bàn, cảm biến quang, 36W, 200g/min, 3kg |
| Cleaner Plus | Bản để sàn/ngoài máy, ống chứa bã ~3kg, 36W, 200g/min, 4kg |
| Deep drip tray | Khay hứng sâu, trượt, chứa ly cao tới 130mm |
| Waste drawer | Ngăn chứa bã cà phê inox, lắp dưới group |
| Cup warmer | Bộ hâm ly bằng thép |

## SEO — gap quan trọng (đã xác minh, không phải do bỏ sót)

Đã kiểm tra thô bằng `curl` (không qua AI-summarize) trên nhiều trang sản phẩm và cả trang chủ: **toàn bộ website bfcsrl.it KHÔNG có thẻ `<meta name="description">`** — chỉ có `<title>` dạng `"{Tên sản phẩm} | BFC srl"`. Vì vậy trong `data.json`, mọi model đều có `seo.seo_description: null` kèm `seo_description_gap` ghi rõ "MISSING trên nguồn" — **không tự bịa mô tả**. Nếu publish lên site thật, cần tự viết meta description mới cho từng sản phẩm.

`seo.image_url` đã lấy được cho **toàn bộ 40/40 sản phẩm** — đều là ảnh sản phẩm thật (không phải logo), lấy trực tiếp từ `wp-content/uploads/` của bfcsrl.it, độ phân giải web (665-1024px chiều rộng).

## Giá bán

BFC **KHÔNG công bố giá bán công khai** trên website chính hãng (thông lệ ngành thiết bị pha chế B2B). Cần liên hệ trực tiếp BFC srl (+39 0438 394979) hoặc đại lý phân phối để lấy báo giá.

## Thị trường Việt Nam — chưa nghiên cứu

Việc xác nhận nhà phân phối chính hãng của BFC tại Việt Nam nằm **ngoài phạm vi** lần cào này — client hiện chỉ dùng BFC làm logo đối tác trên site, quan hệ phân phối thực tế giữa saigonhoreca và BFC srl **chưa được xác minh** qua nguồn công khai.

## Việc còn thiếu / gaps

- **Meta description:** thiếu hoàn toàn trên toàn bộ 40 trang sản phẩm (xác nhận qua curl) — cần viết mới nếu publish.
- **Kích thước/trọng lượng máy:** đa số model chỉ có "boiler capacity" + "group weight" dạng text; kích thước tổng thể (WxDxH) và trọng lượng máy nằm trong PDF/ảnh kỹ thuật (technical drawing), không trích xuất được thành text qua WebFetch — cần tải PDF brochure riêng từng model nếu cần số liệu chính xác.
- **Giấy phép sử dụng ảnh:** ảnh lấy trực tiếp từ site chính hãng, chưa xin phép dùng thương mại — cần liên hệ BFC nếu dùng để bán hàng thật.
- **Năm thành lập chính xác:** chỉ có nguồn thứ cấp (thập niên 1950), chưa xác minh từ nguồn chính hãng.
- **Deep Black RS:** nghi là biến thể màu/finish của dòng Lira (tên file ảnh "Lira-Deep-Black-RS") chứ không phải model độc lập — cần xác nhận thêm nếu cần phân loại chính xác.
- **Nhà phân phối VN:** chưa nghiên cứu, cần làm riêng nếu cần.
- **Giá bán:** không công bố public, cần liên hệ hãng/đại lý.
