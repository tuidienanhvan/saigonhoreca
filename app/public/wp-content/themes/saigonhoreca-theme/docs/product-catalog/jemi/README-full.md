# Jemi-Spain — Catalog đầy đủ (data-full.json)

**Ngày thu thập:** 2026-07-18
**Thương hiệu (brand):** `Jemi-Spain`
**Nguồn:** [jemi.es](https://jemi.es) — website chính hãng Jemi (Tây Ban Nha), cả bản tiếng Tây Ban Nha và bản `/en/` tiếng Anh.

> File này KHÔNG thay thế `data.json` hiện có (bản đó khớp với các sản phẩm đã import làm draft). `data-full.json` là bản deep-crawl toàn bộ catalog để đối chiếu/bổ sung.

---

## 1. Cách lấy dữ liệu (phương pháp)

Trang jemi.es chạy WordPress + Elementor + JetEngine, các trang sản phẩm render bằng JS. Dữ liệu được lấy bằng 3 tầng, **toàn bộ là số liệu thật trên site, không suy đoán**:

1. **WordPress REST API** (`/wp-json/wp/v2/productos`) — nguồn chuẩn cho danh sách 660 sản phẩm: mã model, `source_url` (link thật), tiêu đề, `tipo` (taxonomy), và khối Yoast SEO (`seo_title`, `seo_description`, `og:image`).
2. **Trang danh mục render sẵn** (Elementor/JetEngine) — bảng thông số kỹ thuật (Dimensiones, Potencia, Voltaje, Rendimiento/hora…) được nhúng server-side ở trang danh mục (không phải trang sản phẩm).
3. **AJAX `jet_smart_filters`** (`admin-ajax.php`) — dùng để lấy thông số các mục bị phân trang (infinite-scroll) mà trang tĩnh không hiển thị hết: Planchas Fry-Top, Coup de feu, Muebles neutros, Túnel de lavado, v.v. Truy vấn theo từng taxonomy term với `posts_per_page` lớn để render đủ 1 trang.

`specs` ưu tiên **bản tiếng Anh** (trang `/en/`); thiếu thì dùng **bản tiếng Tây Ban Nha** — cả hai đều là số thật trên site. `image_url` ưu tiên `og:image` riêng của từng sản phẩm, dự phòng ảnh nền (`background-image`) trong trang danh mục. Mọi URL ảnh chuẩn hóa về `https://`.

`description_en` là mô tả tính năng thật ở **cấp dòng/danh mục** — trang sản phẩm của Jemi không có mô tả riêng từng model, phần mô tả được Jemi viết chung cho cả dòng (ví dụ đặc điểm inox AISI 304, hệ thống rửa, v.v.).

---

## 2. Số lượng đã ghi

- **91 nhóm** (`groups`) — đúng theo worklist khảo sát.
- **693 mục model** (`models`) = **660 sản phẩm REST duy nhất** + **33 mục phụ kiện dùng chung nhiều series** (một phụ kiện lắp cho nhiều series được liệt kê ở từng nhóm series tương ứng, giống cách hãng phân loại).
- **660/660 sản phẩm** đều có `source_url` thật (link REST đã kiểm tra HTTP 200).
- **689/693 mục có thông số kỹ thuật** (sau đợt bổ sung 18/07/2026 — trước đó là ~591). 4 mục còn trống: GS 17 3P, GS 18, GS 19 3P, KX 40 S (xem mục 7.4).
- **691/693 mục có `description_en`**. 2 mục còn trống: GS 17 3P, KX 40 S.
- **682/693 mục có `image_url`** thật.

### Phân chia theo bộ phận (sản phẩm duy nhất)

| Bộ phận | Nhóm | SP duy nhất |
|---|---|---|
| Rửa chén / rửa ly / hầm rửa (Lavavajillas + Lavavasos + Túnel) | 9 | 41 |
| Bếp chiên công nghiệp (Serie J 22 + Serie N 12) | 2 | 34 |
| Cocinas Serie 600 | 15 | 103 |
| Cocinas Serie 750 | 22 | 153 |
| Cocinas Serie 900 | 22 | 152 |
| Cocinas Serie 1000 | 15 | 110 |
| Phụ kiện (Accesorios) | 5 | 68 |
| MOREI (đặt làm theo yêu cầu) | 1 | 1 |

---

## 3. Đã bao phủ

- **Toàn bộ bộ phận Cocinas** (Serie 600/750/900/1000): bếp gas, bếp gas CHEF, EFFICIENT LINE, bếp điện, bếp từ (inducción), bếp hồng ngoại (vitrocerámica), Planchas Fry-Top (acero/cromo duro × lisas/ranuradas/mixtas + biến thể CHEF), Coup de feu, Parrillas agua/lava, Cuecepastas, Baño María, Marmitas, Sartenes basculantes, Paelleros, Hornillo 160HGR, Wok, Hornos, Muebles neutros, Contenedores, Bases soporte — kèm thông số thật.
- **Bếp chiên công nghiệp** Serie J (sobremesa/mueble/gigante, kể cả biến thể GP và CH) và Serie N.
- **Rửa công nghiệp**: bajo mostrador (Tech/Pro Line + GS 16, GS 5AF ngoài dòng), cúpula, lavaperolas (GS 60 PLUS, GSP 44/45/46), túnel arrastre (14 model, gồm LB-2830-TCV+R, A2440-T, các bản ECO) và túnel cadena (C 4650 T, C 5830 T, C 7070 TT ECO).
- **Lavavasos/Lavacopas** (GS 17 3P, GS 18, GS 19 3P, KX 40 S) — 4 model không gắn taxonomy trên site, chỉ tìm được qua sitemap/REST; đã lấy qua post ID.
- **Phụ kiện** (Comunes + từng series 600/750/900/1000) — có tên + ảnh + link.
- **MOREI** — dòng bếp đặt làm theo yêu cầu, một mục thông tin.

---

## 4. KHÔNG bao gồm (và lý do)

- **Giá bán** — không công khai trên website.
- **File ficha técnica (PDF)** — có nút tải nhưng không nhúng vào dữ liệu; nằm ngoài phạm vi.
- ~~**Thông số của nhóm Phụ kiện / ~102 mục thiếu specs**~~ — **ĐÃ LÀM XONG 18/07/2026 (đợt 2), xem mục 7.** Ghi chú cũ ("nằm trong 618 file PDF datasheet riêng lẻ") là **sai**: kiểm tra lại cho thấy jemi.es chỉ có 469 file PDF ở `/documentacion/` và **không có file nào là datasheet riêng cho phụ kiện**. Dữ liệu thật của phụ kiện nằm ở listing `/accesorios-para-cocinas-jemi/` (JetEngine), lấy qua AJAX.
- **Thông số của 4 máy Lavavasos** (GS 17 3P, GS 18, GS 19 3P, KX 40 S) — **vẫn để trống sau đợt 2**. Xem mục 7 để biết lý do chi tiết (server trả placeholder rác, không có datasheet PDF). `image_url` của GS 18 và GS 19 3P cũng trống vì site không có `og:image` cho 2 model này.
- **Nhóm MOREI** — bếp bespoke, không có mã model cố định.

---

## 6. Nhật ký sửa 18/07/2026

- **`source_url` của MOREI đã được kiểm tra lại**: `https://jemi.es/cocinas-a-medida-morei/` trả về **HTTP 200** (đã curl trực tiếp, không redirect), `og:image` và `seo_title` trong response khớp đúng với `image_url`/`seo_title` đã ghi trong `data-full.json`. URL này **đang đúng**, không cần sửa (không phát hiện lỗi 404 tại thời điểm kiểm tra).
- **Dịch nhãn thông số kỹ thuật còn sót tiếng Tây Ban Nha sang tiếng Việt**: 20 nhãn Spanish (Dimensiones, Peso, Potencia, Area útil, Velocidades, Voltaje, Ciclo de lavado, Placas, Quemadores, Rendimiento/hora, Altura útil, Platos, Cajón, Purificador, Litros, Dimensiones cesta, Aceite, Potencia nominal, Elevador, Agua) đã được dịch sang tiếng Việt (ví dụ: Dimensiones → Kích thước, Peso → Trọng lượng, Potencia → Công suất), áp dụng nhất quán trên toàn bộ 693 model — tổng cộng 519 lượt nhãn tại 147 model được đổi. Nhãn tiếng Anh (Dimensions, Weight, Power…) giữ nguyên, không dịch. Giá trị số/đơn vị kỹ thuật của mọi spec không bị thay đổi.
- **Lưu ý chất lượng dữ liệu phát hiện (chưa sửa, ngoài phạm vi 2 việc trên)**: model `Cajón` trong nhóm `Accessories – Common (all series)` có `specs` bị lẫn dữ liệu rác từ menu điều hướng của site (key/value như `"Who we are": "Dishwasher"`, `"Hotel": "Foodservice chains"`…) — rõ ràng là lỗi crawl ghép sai cột, không phải thông số thật. Nhãn `Cajón` trong entry này vẫn được dịch thành `Ngăn kéo` theo đúng phạm vi việc 2, nhưng giá trị rác bên trong chưa được dọn — cần một tác vụ riêng để rà soát và làm sạch.

---

## 7. Nhật ký 18/07/2026 — đợt 2: bổ sung specs + description cho 104 mục còn thiếu

Khách yêu cầu làm đầy đủ thay vì bỏ qua như quyết định trước đó. Kết quả:

| Hạng mục | Kết quả |
|---|---|
| Specs điền được | **100 / 104** |
| Description điền được | **102 / 104** (103 mục thiếu ban đầu → còn 2) |
| Rác `Cajón` | **đã dọn sạch** |

### 7.1. Nguồn dữ liệu thật đã tìm ra (khác hẳn giả định cũ)

Giả định cũ "thông số nằm trong PDF datasheet riêng từng phụ kiện" là **sai**. Thực tế:

- Trang sản phẩm phụ kiện (`jemi.es/productos/<slug>/`) **hoàn toàn rỗng** — chỉ có menu + footer, không có nội dung, không có link PDF, không có `og:description`. Đây chính là nguồn gốc của rác `Cajón`: crawler cũ đã bóc menu điều hướng thành bảng specs.
- WP REST API (`/wp-json/wp/v2/productos`) **không expose meta/ACF**, nên không lấy được specs qua REST.
- **Nguồn thật:** listing JetEngine ở trang `/accesorios-para-cocinas-jemi/` (listing ID `10654`), gọi qua
  `POST /wp-admin/admin-ajax.php` với `action=jet_smart_filters`, `provider=jet-engine/default`,
  `query[_tax_query_tipo]=<term>`, `settings[lisitng_id]=10654`, `settings[posts_num]=200`.
  Taxonomy `tipo`: `463` Serie 600 (17) · `464` Serie 750 (15) · `465` Serie 900 (24) · `466` Serie 1000 (12) · `467` Comunes (31) — **tổng 99, khớp đúng 99 phụ kiện trong worklist.**
  Mỗi card cho ra **tên + mã tham chiếu `CINAC…`** hoặc **câu mô tả gốc tiếng Tây Ban Nha** (ví dụ `Cajón en módulo soporte (1 de 400 mm; 450 mm S1000)`).
  *Lưu ý kỹ thuật:* phân trang của listing này **bị lỗi trả trùng/thiếu** — phải đặt `posts_num` lớn để lấy hết trong 1 lần, đừng lặp `paged`.
- **Kích thước** của phụ kiện lấy từ chính **tên sản phẩm** trên jemi.es (hãng nhúng sẵn số đo trong tên, ví dụ `Bandeja horno 530 x 420 x 30 mm`) — là số thật của hãng, không suy đoán.
- **MOREI**: lấy từ nội dung render sẵn của `jemi.es/cocinas-a-medida-morei/` (3 cấu hình Compact / Modular sobre Único / Monoblock, độ dày mặt bếp 2 mm và 3 mm, series áp dụng, tùy chọn sơn RAL, thiết kế 3D).
- Đã tải và bóc text 18 file PDF từ `/documentacion/` (`Cocinas-es.pdf`, `JEMI_TECH_LINE__PRO_LINE_ES.pdf`, `ft_KX40_PRO-LINE_ES-1.pdf`, các sơ đồ điện GS…) bằng `pypdf` — **tất cả đều bóc được text, không có bản scan ảnh**.

### 7.2. Nhãn specs dùng cho phần bổ sung

Ưu tiên tái dùng nhãn tiếng Việt đã có trong file: `Kích thước`, `Trọng lượng`, `Công suất`, `Điện áp`, `Chu trình rửa`, `Hiệu suất/giờ`, `Chiều cao hữu ích`, `Kích thước giỏ`, `Lít`.
Phụ kiện phát sinh thêm một số nhãn mới (không có nhãn tương đương sẵn):
`Mã tham chiếu`, `Chất liệu`, `Dùng cho`, `Lắp cho`, `Quy cách`, `Đơn vị tính`, `Kiểu dáng`, `Loại`, `Công dụng`, `Vị trí lắp`, `Trang bị`, `Cấu hình`, `Chuẩn khay`, `Chi tiết`, `Ghi chú`.
`Mã tham chiếu` luôn được xếp cuối dict specs.

> **Lưu ý:** file này **chưa dịch xong nhãn** như từng nêu — hiện vẫn còn lẫn nhãn tiếng Anh (`Dimensions` 463 lượt, `Weight` 397, `Power` 370, `Useful area` 106, `Plates` 97, `Burners` 86…) bên cạnh nhãn tiếng Việt (`Kích thước` 133, `Trọng lượng` 111, `Công suất` 98…). Việc thống nhất toàn bộ nhãn sang tiếng Việt là **một tác vụ riêng chưa làm**.

### 7.3. Đã dọn rác

- **`Cajón`** (nhóm `Accessories – Common (all series)`): xoá **31 cặp key/value rác** lấy nhầm từ menu điều hướng (`"Who we are": "Dishwasher"`, `"Hotel": "Foodservice chains"`, `"Industries": "Sports institutions"`…). Thay bằng specs thật lấy từ listing: `Quy cách` = *1 ngăn kéo cho module 400 mm (450 mm ở Series 1000)*, `Lắp cho` = *Module có chân đỡ*. Đã kiểm tra lại toàn file: **không còn nhãn rác nào**.
- **`MOREI (a medida)`**: gỡ đoạn `Lorem ipsum…` trong `description_en` — đây là placeholder chưa viết xong trên chính website jemi.es, bị crawl vào.

### 7.4. 4 mục KHÔNG làm được (để trống theo nguyên tắc thà thiếu còn hơn bịa)

Nhóm `Glasswashers / Cup washers` (`Máy rửa ly / cốc`):

| Model | Specs | Description | Lý do |
|---|---|---|---|
| **GS 17 3P** | trống | trống | Không tồn tại trong listing 660 sản phẩm của `/localiza-tu-producto`; không có trang danh mục, không có datasheet PDF, không có sơ đồ điện. Trang sản phẩm rỗng hoàn toàn. |
| **GS 18** | trống | **đã điền** | Không có datasheet. Mô tả viết dựa trên sơ đồ điện chính hãng `JEMI-ESQUEMA-ELECTRICO-GS-6AF-18-EC-26-04-24-ES.pdf`. |
| **GS 19 3P** | trống | **đã điền** | Không có datasheet. Mô tả viết dựa trên sơ đồ điện chính hãng `JEMI-ESQUEMA-ELECTRICO-GS-19-EC-26-04-24-ES.pdf`. |
| **KX 40 S** | trống | trống | Chỉ có datasheet cho **KX 40** (`ft_KX40_PRO-LINE_ES-1.pdf`), **không phải** biến thể "S" — không được phép gán số của KX 40 sang KX 40 S. |

**Vì sao không lấy specs từ site cho 3 model GS 18 / GS 19 3P / KX 40 S:** listing `/localiza-tu-producto` *có* trả specs cho 3 model này, nhưng là **giá trị placeholder rác**:
`"Dimensiones": "x  x  mm"`, `"Potencia": "kw /"`, `"Peso": "45 Kg"`, `"Litros": "20"`.
Bộ giá trị `Peso 45 Kg / Litros 20` này **dùng chung y hệt cho 14 sản phẩm không liên quan**, kể cả các hầm rửa `A 1280`, `A 2100 T`, `A 2440-T` (máy dài gần 3 m, nặng hàng trăm kg) → chắc chắn là giá trị mặc định của template, **không phải số thật**. Đã bỏ.

Cả 4 model này đều **không xuất hiện** trên trang danh mục hiện hành (`/lavavajillas/bajo-mostrador/tech-line/` chỉ có GS 35/40S/40/50/50 3P/60; `/pro-line/` chỉ có KX 40/50/50 3P) — nhiều khả năng là **model cũ đã ngưng**, hãng đã gỡ thông số khỏi web.

---

## 8. Nhật ký 19/07/2026 — thử fetch lại ảnh cho 11 model thiếu `image_url`

11 model sau đây thiếu `image_url` dù có `source_url` thật: GS 18, GS 19 3P, SPLG100/2, SPLG100, Pasamanos, Zócalo embellecedor, Encendido piezoeléctrico, Armario neutro, Termostato electrónico, Cocina mixta, Ruedas para cocinas.

Đã mở từng trang bằng trình duyệt thật (JS render đầy đủ, không phải fetch tĩnh) và kiểm tra:
- `meta[property="og:image"]` trong `<head>` — **không có** trên cả 11 trang.
- Toàn bộ `<img>` và mọi URL `wp-content/uploads/...` xuất hiện trong DOM đã render — chỉ thấy asset dùng chung của theme (favicon `cropped-logo-favicon-jemi-*.png`, logo header `Capa_1.png`, icon `Group-5.svg`); **không có ảnh sản phẩm nào** trên cả 11 trang.

**Kết quả: 0/11 tìm được ảnh thật.** Các trang sản phẩm này (`jemi.es/productos/<slug>/`) thực sự không nhúng ảnh sản phẩm — giống hiện tượng đã ghi nhận ở mục 7.1 (trang sản phẩm phụ kiện "hoàn toàn rỗng", chỉ có menu + footer). `image_url` của 11 model này **giữ nguyên rỗng** (`""`) trong `data-full.json`, không sửa gì thêm — theo đúng nguyên tắc thà thiếu còn hơn bịa/đoán URL ảnh.

---

## 5. Ghi chú kỹ thuật cho importer

- `specs` là dict `label → value`; nhãn có thể là tiếng Anh (ưu tiên) hoặc tiếng Tây Ban Nha.
- `origin` = `"Spain"` cho mọi sản phẩm (Jemi sản xuất tại Tây Ban Nha).
- `heat_source` suy ra từ loại thiết bị của nhóm (`gas` / `electric` / `induction`); để trống khi trung tính hoặc không xác định rõ (Fry-Top, tủ, giá đỡ…).
- `group_name_vi` là term Tiếng Việt dùng làm `product_category`.
- Một số phụ kiện/bệ đỡ (ví dụ `BS91`, `Embellecedor lateral trasero`, `Plancha hierro…`) xuất hiện ở nhiều nhóm series vì dùng chung — đây là chủ ý, không phải trùng lặp lỗi.
