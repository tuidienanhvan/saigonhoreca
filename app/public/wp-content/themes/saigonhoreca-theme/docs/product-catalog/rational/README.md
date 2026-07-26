# RATIONAL (Đức) — Nghiên cứu danh mục sản phẩm

Nguồn: website chính hãng RATIONAL AG (Đức) — `www.rational-online.com` (bản quốc tế `/en_xx/`, đối chiếu thêm `/en_us/`).
Ngày cào dữ liệu: 2026-07-14. Dữ liệu máy đọc song song ở [`data.json`](./data.json).

## Vì sao có tài liệu này

Site saigonhoreca.local hiện có **0 sản phẩm** dưới brand RATIONAL — brand chỉ tồn tại dạng logo đối tác ở mục "Đối tác" trang chủ. RATIONAL là hãng lò hấp nướng combi-steamer lớn nhất/nổi tiếng nhất thế giới, tài liệu rất đầy đủ trên site chính hãng.

**Lưu ý kỹ thuật cào dữ liệu:** rational-online.com dựng bằng web components (custom elements `<cms-*>` + shadow DOM), nên công cụ WebFetch (dùng model tóm tắt) không đọc được nội dung đầy đủ — chỉ thấy DOM rỗng hoặc bị cắt. Đã chuyển sang dùng trình duyệt thật (Claude Browser) + script JS đệ quy xuyên shadow-root để trích xuất chính xác text/ảnh/meta tag. Thông số kỹ thuật chi tiết (kW, kích thước, trọng lượng) của từng model được đối chiếu qua WebSearch — nguồn gốc số liệu vẫn là rational-online.com nhưng được các nhà phân phối/spec-sheet bên thứ ba trích dẫn lại, CHƯA tự tay đọc datasheet PDF gốc cho từng model.

## Về RATIONAL

RATIONAL AG thành lập **1973**, trụ sở tại **Landsberg am Lech, Đức**. Phát triển ra combi-steamer đầu tiên trên thế giới năm **1976** (kết hợp hơi nước + đối lưu nhiệt trong 1 thiết bị). Là công ty đại chúng niêm yết tại Đức, dẫn đầu thị phần toàn cầu về combi-steamer thương mại. ([Nguồn: company history](https://www.rational-online.com/en_xx/company/about-us/history/) — trang này chỉ lấy được tiêu đề qua WebFetch, nội dung chi tiết mốc 1973/1976 xác nhận qua WebSearch trích dẫn rational-online.com, CHƯA tự đọc lại toàn văn qua browser.)

## Website chính hãng & thị trường Việt Nam

- **Domain chính:** `https://www.rational-online.com/`
- **Cấu trúc locale:** `en_xx` (quốc tế, không giá), `en_us` (Mỹ), `en_sg` (Singapore/Đông Nam Á), `zh_cn`, `ja_jp`, `es_xx`, `fr_xx`...
- **KHÔNG có subsite tiếng Việt.** Trong bộ chọn quốc gia (mục Asia) ở trang chủ, "Vietnam" trỏ thẳng sang `https://www.rational-online.com/en_sg/home/` — RATIONAL phục vụ thị trường Việt Nam qua chi nhánh khu vực Singapore, hoàn toàn bằng tiếng Anh, không có bản địa hoá hay catalog/giá riêng cho VN.
- **Đại lý VN:** CHƯA xác nhận nhà phân phối chính hãng tại Việt Nam — trang [dealer locator](https://www.rational-online.com/en_xx/customercare/rational-dealer/) chưa được tra cứu trực tiếp cho địa chỉ VN trong phiên này.

## 1. iCombi Pro — Lò Hấp Nướng Đa Năng (dòng cao cấp nhất, hàng chủ lực)

Combi-oven thông minh: nướng, nấu, hấp, chiên, quay nhiệt độ thấp trong 1 thiết bị. Công nghệ: iCookingSuite (tự nấu thông minh), iProductionManager, iCareSystem (tự làm sạch/tẩy cặn), iDensityControl, WiFi tích hợp sẵn, đầu dò lõi 6 điểm đo, nhiệt độ khoang nấu tới 300°C, cửa kính 3 lớp.

| Model | Dung tích | Suất ăn/ngày | Công suất | Ghi chú |
|---|---|---|---|---|
| **XS 6-2/3** | 6 khay GN2/3 (hoặc 6 GN1/1) | 20-80 | 5.7kW / 400V 3NAC | Nhỏ nhất, dạng compact. Dims 657×556/623×567/594mm, 64kg |
| **6-1/1** | 6 khay GN1/1 | 30-100 | 10.8kW | Size phổ thông nhất |
| **6-2/1** | 6 khay GN2/1 (12 GN1/1) | 60-160 | 28kW (gas) | Dims 1072×975/1042×754/804mm, 155kg. Gas 3/4"IG |
| **10-1/1** | 10 khay GN1/1 | 80-150 | — | Size trung-lớn phổ biến |
| **10-2/1** | 10 khay GN2/1 | 150-300 | — | Dims 1072×1042×1014mm, 192kg |
| **20-1/1** | 20 khay GN1/1 | 150-300 | 37.2kW | — |
| **20-2/1** | 20 khay GN2/1 (40 GN1/1) | 300-500 | 67.9kW / 400V 3 pha | Lớn nhất dòng, cho bếp trung tâm |

Mỗi size có bản **điện (E)** và **gas (G)** — tổng SKU đặt hàng thực tế ~14 (7 size x 2 nguồn nhiệt), chưa kể option kính/tay nắm. Đây là biến thể công suất/nguồn nhiệt của CÙNG 1 dòng máy, không phải sản phẩm khác công dụng.

Ảnh model riêng từng size (thật, không phải logo): `product-overview-xs/-61-1/-62-1/-101/-102/-201/-202-w800.png` tại `rational-online.com/media/images/product/icombipro/2023-ic/`.

Nguồn: [iCombi Pro](https://www.rational-online.com/en_xx/icombi-pro/) · [Models](https://www.rational-online.com/en_xx/icombi-pro/models/) · [Technical details](https://www.rational-online.com/en_xx/icombi-pro/funktionen/technical-details-equipment/)

## 2. iCombi Classic — Lò Hấp Nướng Vận Hành Đơn Giản (núm xoay)

"Productive. Robust. Reliable." — điều khiển núm xoay + màn hình màu, tới 100 chương trình x 12 bước, ClimaPlus (kiểm soát độ ẩm), đèn LED, tự làm sạch bằng viên tẩy không phốt-phát, chứng nhận ENERGY STAR. Định vị là lựa chọn vận hành thủ công cho đầu bếp có kinh nghiệm, thay vì tự động hoàn toàn như iCombi Pro.

| Model | Dung tích | Công suất | Ghi chú |
|---|---|---|---|
| 6-1/1 | 6 khay GN1/1 | 10.8kW | 30-100 suất/ngày |
| 6-2/1 | 6 khay GN2/1 | — | Chưa cào riêng kW |
| 10-1/1 | 10 khay GN1/1 | 18.9kW | — |
| 10-2/1 | 10 khay GN2/1 | — | Chưa cào riêng kW |
| 20-1/1 | 20 khay GN1/1 | — | Size lớn nhất mô tả chung "tới 500 suất/ngày" |
| 20-2/1 | 20 khay GN2/1 | — | Lớn nhất dòng Classic |

6 size (không có bản XS như iCombi Pro), mỗi size cũng có bản điện/gas. Chưa tìm được bộ ảnh riêng từng size như iCombi Pro — chỉ có 1 ảnh hero dùng chung (tên file thực tế là model 10-1/1).

Nguồn: [iCombi Classic](https://www.rational-online.com/en_xx/icombi-classic/) · [Models](https://www.rational-online.com/en_xx/icombi-classic/models/)

## 3. iVario Pro — Chảo Nghiêng Đa Năng

Thay thế bếp, nồi, chảo, nồi chiên, nồi áp suất, bàn giữ nóng cách thủy trong 1 thiết bị — tiết kiệm tới 40% năng lượng. Công nghệ: iVarioBoost (làm nóng nhanh), iCookingSuite, iZoneControl (vùng gia nhiệt độc lập). Ứng dụng: nấu/luộc, chiên, chiên ngập dầu, nấu nhiệt độ thấp, nấu áp suất, sous-vide.

| Model | Dung tích | Công suất |
|---|---|---|
| **2-XS** | 2×17 lít (GN1/1) | 15kW |
| **Pro 2-S** | 2×25 lít (GN1/1) | 21kW / 400V 3PH+N |
| **Pro L** | 100 lít (GN2/1) | 27kW / 400V 3PH+N |
| **Pro XL** | 150 lít (GN3/1) | 41kW / 400V 3PH+N |

4 model hiện hành chính thức. Lưu ý: trang Accessories còn liệt kê "Typ 112 (2×14L)" trong bộ lọc kích cỡ — nhiều khả năng là model iVario (không phải "Pro") thế hệ cũ vẫn còn phụ tùng thay thế, KHÔNG nằm trong 4 model bán chính thức hiện nay — đã gắn cờ riêng trong `data.json`, không đưa vào danh sách sản phẩm chính.

Nguồn: [iVario Pro](https://www.rational-online.com/en_xx/ivario-pro/) · [Models](https://www.rational-online.com/en_xx/ivario-pro/models/)

## 4. Combi-Duo — Bộ xếp chồng 2 lò (KHÔNG phải dòng sản phẩm riêng)

Không phải một dòng lò — là bộ giá đỡ (Stand I/II) + kit xếp chồng để đặt 2 lò iCombi (Pro hoặc Classic) chồng lên nhau, tiết kiệm diện tích sàn, chạy 2 chế độ nấu độc lập cùng lúc. Ghi chú riêng để không nhầm là 1 model lò khi lên danh mục web. Chưa cào chi tiết bảng mã kit/stand cụ thể trong phiên này.

## 5. Accessories — Phụ kiện chính hãng

Danh mục phụ kiện: Combi-Duo, iVario (phụ kiện riêng), Banquet & mobile solutions, Exhaust hood & installation, Oven stand & cabinets, Cleaning and care, Hanging rack, Mobile oven rack, Set-up variations. Sản phẩm ví dụ nêu tên trên trang: khay tráng men granite, VarioSmoker, khay đục lỗ/inox, vỉ inox, Superspike gà/vịt, CombiFry, giá sườn (ribs rack).

Đây là phụ kiện đi kèm — nếu đưa lên site nên gắn làm phụ kiện của sản phẩm chính, không tạo nhóm sản phẩm độc lập ngang hàng iCombi/iVario.

Nguồn: [Accessories](https://www.rational-online.com/en_xx/accessories/)

## 6. ConnectedCooking — Nền tảng số (không phải thiết bị vật lý)

Dịch vụ quản lý bếp trên nền tảng đám mây — tạo công thức từ xa, giám sát thiết bị, ghi log HACCP. Hoạt động với các lò/chảo có WiFi tích hợp (iCombi Pro/Classic, iVario Pro). Không nên tạo thành 1 "sản phẩm" riêng trong catalog trừ khi site có mục dịch vụ/subscription.

Nguồn: [ConnectedCooking](https://www.rational-online.com/en_xx/connectedcooking/)

## Giá bán

RATIONAL **KHÔNG công bố giá bán công khai** trên website chính hãng (thông lệ ngành B2B, giống Giorik). Giá lẻ tham khảo xuất hiện ở một số nhà phân phối bên thứ ba tại Mỹ/Anh (buzzcateringsupplies.com, webstaurantstore.com...) nhưng KHÔNG phải giá chính hãng cho thị trường Việt Nam — cần liên hệ RATIONAL hoặc đại lý phân phối khu vực để lấy báo giá VN.

## Việc còn thiếu / cần làm tiếp

- **Đại lý chính hãng tại Việt Nam** — chưa tra dealer locator hoặc liên hệ trực tiếp hãng để xác nhận.
- **Thông số kỹ thuật đầy đủ** (kW/kích thước/trọng lượng) — mới verify được phần lớn cho iCombi Pro (7/7 model có ít nhất 1 chỉ số), nhưng iCombi Classic chỉ có 2/6 model (6-1/1, 10-1/1) có kW; 4 model còn lại (6-2/1, 10-2/1, 20-1/1, 20-2/1) mới có model code + capacity.
- **Ảnh riêng từng size iCombi Classic** — chỉ có 1 ảnh hero dùng chung cho cả dòng (tên file thực chất là model 10-1/1); không tìm được bộ ảnh theo từng size như iCombi Pro có (`product-overview-*`).
- **Mã đặt hàng (order code) cụ thể** cho từng biến thể điện/gas + option kính/tay nắm/kệ — hiện mới có model_code cấp "kích cỡ" (ví dụ "6-1/1"), chưa phân rã tới SKU cuối như "LM100BE" (mã đăng ký ETL Anh, tìm qua bên thứ ba, chưa xác minh trực tiếp trên rational-online.com).
- **Mã kit/stand Combi-Duo cụ thể** — chỉ có mô tả tổng quát.
- **Giá bán VN** — RATIONAL không công bố giá public.
- **Nội dung đầy đủ trang lịch sử công ty** — mốc 1973/1976 xác nhận gián tiếp qua WebSearch, chưa tự đọc lại toàn văn trang `company/about-us/history/` qua browser.

## Ghi chú quan trọng — áp dụng bài học từ Giorik (biến thể vs. SKU thật)

RATIONAL có **ít dòng sản phẩm hơn Giorik nhiều** — chỉ 2 dòng lò chính (iCombi Pro, iCombi Classic) + 1 dòng chảo (iVario Pro), tổng **17 "model" cấp kích cỡ** (7 + 6 + 4). Nhưng mỗi model (ví dụ "10-1/1") thực chất là **1 kích cỡ**, không phải 1 SKU đặt hàng hoàn chỉnh — cần nhân với biến thể điện/gas (và option kính/tay nắm với một số dòng) để ra mã đặt hàng thật, tương tự cách Giorik có nhiều biến thể finish/hinge trên cùng 1 kiểu máy.

**Khuyến nghị khi lên site:** tạo sản phẩm ở cấp độ `model_code` (17 sản phẩm chính: 7 iCombi Pro + 6 iCombi Classic + 4 iVario Pro), dùng **thuộc tính biến thể (product variation)** cho điện/gas thay vì tạo sản phẩm riêng cho mỗi tổ hợp — tránh nhân bản catalog không cần thiết như đã cảnh báo ở Giorik.
