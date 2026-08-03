# Fiorenzato (Ý) — Nghiên cứu danh mục từ đầu (from scratch)

Nguồn: website chính hãng Fiorenzato — `www.fiorenzato.com` (bản tiếng Anh `/en/`), cộng thêm microsite dòng gia đình `www.fiorenzatohome.com`.
Ngày cào dữ liệu: 2026-07-14 (lần cào đầu tiên). Dữ liệu máy đọc song song ở [`data.json`](./data.json).

## Vì sao có tài liệu này

Site saigonhoreca.local hiện chỉ có brand Fiorenzato dưới dạng **logo đối tác** ở section "Đối tác" trang chủ — **0 sản phẩm** trong catalog thật. Khác với các brand đã nghiên cứu trước (Giorik, Sammic, Hoshizaki...) vốn đã có sẵn vài sản phẩm để mở rộng, đây là bản cào **hoàn toàn từ đầu**. Fiorenzato.com không chặn crawl (WebFetch đọc bình thường), nên đã cào trực tiếp từ site chính hãng.

## Về Fiorenzato

Fiorenzato M.C. S.r.l. có lịch sử **hơn 80 năm** trong ngành máy xay và định lượng cà phê tại Ý. Khác với Giorik (lò/bếp công nghiệp), Fiorenzato **chuyên biệt 100% vào máy xay cà phê (coffee grinders) và máy định lượng (dosers)** — không có lò, bếp, hay thiết bị rửa. Liên hệ: info@fiorenzato.it. ([Nguồn](https://www.fiorenzato.com/en))

## Tổng quan danh mục — 6 nhóm, 34 model

| Nhóm | Số model | Phân khúc |
|---|---|---|
| **Grind by Weight (SENSE/ADAPT)** | 8 | Cao cấp, cân điện tử tích hợp, cho quán chuyên espresso |
| **PRO Line Grinders** | 7 | Máy xay-định lượng truyền thống cấp PRO |
| **On Demand** | 9 | Xay theo yêu cầu, không doser, cho quán espresso hiện đại |
| **Grocery** | 4 | Truyền thống, phân khúc tạp hóa/quán nhỏ (ký hiệu "D") |
| **Dosers (Grinder-Dosers "A")** | 4 | Máy xay-định lượng cổ điển phổ biến nhất |
| **Home (AllGround)** | 2 | Dòng gia đình, site riêng fiorenzatohome.com |

Toàn bộ 34 model có URL nguồn (link tới trang sản phẩm riêng, không phải chỉ trang danh mục) trong `data.json`.

## 1. Grind by Weight — Máy Xay Cân Trọng Lượng (SENSE / ADAPT)

Dòng cao cấp nhất: 2 kích cỡ lưỡi dao (64mm / 83mm) × 2 công nghệ (SENSE = cân tích hợp trong fork tới 0.1g; ADAPT = tự động điều chỉnh xay theo độ ẩm/tuổi hạt) × 2 cấp (thường / PRO).

| Model | Lưỡi dao | Công suất | Khả năng xay | Kích thước |
|---|---|---|---|---|
| F64 EVO SENSE | Ø64mm phẳng | 450W | 2.7 g/s (Dark-T®) | 230×620×274mm |
| F64 EVO ADAPT | Ø64mm phẳng | 450W | 2.7 g/s (Dark-T®) | 230×620×274mm |
| F64 EVO PRO SENSE | Ø64mm phẳng | 450W | 3.4 g/s (cứng) / 2.7 g/s (Dark-T®) | 230×620×274mm |
| F64 EVO PRO ADAPT | Ø64mm phẳng | 450W | 3.4 g/s / 2.7 g/s | 230×620×274mm |
| F83 E SENSE | Ø83mm phẳng | 650W | 3.1 g/s (Dark-T®) | 234×680×275mm |
| F83 E ADAPT | Ø83mm phẳng | 650W | 3.1 g/s (Dark-T®) | 234×680×275mm |
| F83 E PRO SENSE | Ø83mm phẳng | 650W | 3.1 g/s (Dark-T®) | 234×680×275mm |
| F83 E PRO ADAPT | Ø83mm phẳng | 650W | 3.1 g/s (Dark-T®) | 234×680×275mm |

Nguồn: [Grind by Weight](https://www.fiorenzato.com/en/grind-by-weight/) — mỗi model có URL trang riêng, xem `data.json` → `groups[0].models`.

## 2. PRO Line Grinders — Máy Xay Truyền Thống Có Định Lượng (PRO)

Có phễu đựng hạt + doser (định lượng cơ khí bằng đòn bẩy). 3 kích cỡ lưỡi dao: 64mm/83mm phẳng và 71mm côn (ký hiệu K).

| Model | Lưỡi dao | Công suất | Trọng lượng | Ghi chú |
|---|---|---|---|---|
| F5 A PRO | Ø64mm phẳng | 350W | 13 kg | Biến thể A/T/M |
| F6 A PRO | Ø83mm phẳng | 650W | 15.5 kg | |
| F71 AK PRO | Ø71mm côn | 850W | 21 kg | Doser 250g, liều 5.5-10g |
| F64 E PRO | Ø64mm phẳng | 350W | 13 kg | Title trang lại ghi "On Demand" — nghi trộn template |
| F64 EVO PRO | Ø64mm phẳng | 450W | 13 kg | Quạt làm mát tự động |
| F83 E PRO | Ø83mm phẳng | 650W | 15 kg | |
| F71 EK PRO | Ø71mm côn | 850W | 19 kg | Title trang ghi "F71 DK PRO" — gắn cờ nghi vấn |

Nguồn: [PRO Line Grinders](https://www.fiorenzato.com/en/pro-line-grinders/) — chi tiết + URL từng model ở `data.json` → `groups[1].models`.

## 3. On Demand — Máy Xay Theo Yêu Cầu

Không doser — xay trực tiếp theo lệnh (nút bấm hoặc cân điện tử). Gồm dòng XGi (điện tử cấp PRO, quạt làm mát) và các model EVO/cơ bản tiêu chuẩn.

| Model | Lưỡi dao | Công suất | Trọng lượng |
|---|---|---|---|
| F64 EVO XGi | Ø64mm phẳng | 600W | 15.3 kg |
| F83 E XGi | Ø83mm phẳng | 850W | 18.6 kg |
| F4 EVO | Ø58mm phẳng | 414W | 9 kg |
| F64 EVO XGi PRO | Ø64mm phẳng | 600W | 15.3 kg |
| F83 E XGi PRO | Ø83mm phẳng | 850W | 18.6 kg |
| F64 E | Ø64mm phẳng | 350W | 13 kg |
| F64 EVO | Ø64mm phẳng | 450W | 13 kg |
| F83 E | Ø83mm phẳng | 650W | 15 kg |
| F71 EK | Ø71mm côn | 850W | 20 kg |

Nguồn: [On Demand](https://www.fiorenzato.com/en/on-demand/) — chi tiết + URL từng model ở `data.json` → `groups[2].models`.

## 4. Grocery — Dòng Cho Tạp Hóa / Quán Nhỏ

Ký hiệu "D" (doser only, không automatic microswitch như dòng "A").

| Model | Lưỡi dao | Công suất | Trọng lượng |
|---|---|---|---|
| F5 D | Ø64mm phẳng | 350W | 13 kg |
| F6 D | Ø83mm phẳng | 650W | 15.5 kg |
| F71 DK | Ø71mm côn | 850W | 19 kg |
| F4 ECO | Ø58mm phẳng | 250W | 8 kg |

Nguồn: [Grocery](https://www.fiorenzato.com/en/grocery/) — chi tiết + URL từng model ở `data.json` → `groups[3].models`.

## 5. Dosers — Máy Xay-Định Lượng Cổ Điển (Grinder-Doser "A")

Dòng phổ biến nhất — ký hiệu "A" (automatic microswitch).

| Model | Lưỡi dao | Công suất | Trọng lượng | Doser |
|---|---|---|---|---|
| F4 A | Ø58mm phẳng | 250W | 10 kg | 200g, liều 5.5-10g |
| F5 | Ø64mm phẳng | 350W | 14 kg | 250g, liều 5.5-10g |
| F6 | Ø83mm phẳng | 650W | 16 kg | 250g, liều 5.5-10g |
| F71 AK | Ø71mm côn | 850W | 21 kg | 250g, liều 5.5-10g |

Nguồn: [Dosers](https://www.fiorenzato.com/en/dosers/) — chi tiết + URL từng model ở `data.json` → `groups[4].models`.

## 6. Home (AllGround) — Dòng Gia Đình, Site Riêng

Microsite riêng biệt `fiorenzatohome.com` (không nằm trong menu chính fiorenzato.com), nhắm thị trường tiêu dùng gia đình nhưng dùng lưỡi dao 64mm + Dark-T® coating của dòng chuyên nghiệp.

| Model | Lưỡi dao | Công suất | Trọng lượng | Đặc điểm |
|---|---|---|---|---|
| AllGround Classic | Ø64mm phẳng, Dark-T® | 250W | 9 kg | Điều chỉnh liên tục bằng vòng xoay |
| AllGround Sense | Ø64mm phẳng, Dark-T® | 250W | 9 kg | Cân tích hợp trong fork, màn cảm ứng, điều chỉnh 10µm/nấc |

Nguồn: [Fiorenzato Home](https://www.fiorenzatohome.com/?language=eng) — chi tiết + URL từng model ở `data.json` → `groups[5].models`.

## SEO — vấn đề cần lưu ý trước khi generate CSV

- **seo_title**: có sẵn cho toàn bộ 34 model, lấy trực tiếp từ thẻ `<title>` mỗi trang.
- **seo_description**: **KHÔNG có sẵn** — toàn bộ 34 trang đều thiếu thẻ `<meta name="description">` (đặc điểm chung toàn site, không phải lỗi cào riêng lẻ). Cần soạn mới thủ công hoặc AI-generate có kiểm duyệt trước khi import.
- **image_url**: 24/34 model đã xác nhận URL ảnh tuyệt đối trực tiếp từ WebFetch. 10 model còn lại (F64 EVO PRO ADAPT, F5 A PRO, F64 EVO XGi PRO, F83 E, F4 A, F5, F71 AK, AllGround Classic, AllGround Sense — xem ghi chú `note` trong từng entry ở `data.json`) chỉ có tên file/đường dẫn tương đối, cần 1 lượt fetch bổ sung để chốt URL tuyệt đối trước khi dùng cho import.

## Giá bán

Fiorenzato **KHÔNG công bố giá bán công khai** trên website chính hãng (thông lệ ngành thiết bị pha chế B2B/wholesale). Cần liên hệ trực tiếp Fiorenzato (info@fiorenzato.it) hoặc đại lý phân phối để lấy báo giá.

## Thị trường Việt Nam — CHƯA xác minh

Không tìm thấy trang "dealers"/"distributors" trong phạm vi các trang đã cào (trang chủ + 6 category page + microsite Home). Site saigonhoreca.local đã có Fiorenzato ở section đối tác — có thể chính site này là đại lý chính thức, nhưng **chưa xác minh chéo** qua danh sách đại lý chính thức của Fiorenzato.com — cần user xác nhận nội bộ.

## Việc còn thiếu / cần theo dõi

- **10/34 model chưa có URL ảnh tuyệt đối xác nhận** — cần 1 lượt WebFetch bổ sung cho từng trang trước khi generate CSV import.
- **seo_description trống toàn bộ** — cần soạn nội dung SEO mới, không tự bịa để tránh sai lệch thông tin kỹ thuật.
- **Nghi vấn trộn title giữa PRO Line và On Demand PRO**: F64 E PRO, F64 EVO PRO, F83 E PRO, F71 EK PRO nằm trong menu "PRO Line Grinders" nhưng `<title>` trang lại ghi "On Demand ... PRO" hoặc (F71 EK PRO) "Traditional ... F71 DK PRO". Đã giữ nguyên dữ liệu thật cào được, chỉ gắn cờ ghi chú — không tự sửa.
- **F64 EVO XGi PRO** (on-demand) có title trùng hệt bản không-PRO — chưa xác minh khác biệt spec thực tế ngoài URL riêng.
- **Chưa cào** trang phụ kiện ([accessories](https://www.fiorenzato.com/en/our-optional-accessories/)) và phụ tùng thay thế ([spare parts](https://www.fiorenzato.com/en/spare-parts/)) — có thể có thêm SKU phụ kiện (phễu, cân, lưỡi dao rời) nhưng không phải máy xay hoàn chỉnh nên chưa ưu tiên ở lần cào đầu.
- **Giá bán VN** — Fiorenzato không công bố giá public, cần liên hệ hãng/đại lý.
- **Quan hệ đại lý chính thức tại VN** — chưa xác nhận qua trang nguồn.
