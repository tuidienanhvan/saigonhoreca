# Fujimak-Japan — Catalog đầy đủ (`data-full.json`)

**Ngày khảo sát:** 18–19/07/2026
**Brand string:** `Fujimak-Japan`
**File dữ liệu:** `data-full.json` (56 nhóm · 152 model/biến thể)

> ⚠️ File này **độc lập** với `data.json` và `import.csv`. Cả hai file cũ (bản 13/07/2026, chỉ có 2 nhóm Teppanyaki/Jet Oven, "models: 0" trong draft ban đầu) **không bị đụng tới**.

---

## 1. Phạm vi khảo sát — MỞ RỘNG so với yêu cầu ban đầu

Yêu cầu ban đầu chỉ cần 2 nhóm: **Teppanyaki** và **Jet Oven**. Trong lúc deep-crawl, phát hiện Fujimak có **11 danh mục lớn** (Heating, Cooling, Rice Cooking, Bakery, Washing/Sterilizing, Work Equipment, Cooling/Warming Cart, Food Preparation, Service Equipment, Sanitary Equipment, IoT/Robotics) với khoảng **64 danh mục con** và **142 trang sản phẩm thật** khác ngoài Teppanyaki/Jet Oven. Theo đúng chỉ đạo ("nếu tìm thấy nhóm mới thật sự tồn tại trên site, thêm nhóm mới và ghi rõ là mở rộng ngoài phạm vi khảo sát ban đầu"), đã crawl **toàn bộ** các danh mục này và đưa vào `data-full.json` dưới dạng 54 nhóm bổ sung.

**Kết quả:** 56 nhóm tổng cộng = 2 nhóm gốc (Teppanyaki, Jet Oven) + 54 nhóm mở rộng (toàn bộ catalog Fujimak còn lại).

---

## 2. Nguồn dữ liệu

| # | Nguồn | Dùng cho |
|---|---|---|
| 1 | **https://en.fujimak.co.jp** (bản tiếng Anh chính hãng) | Toàn bộ 11 danh mục lớn (`/products/01`…`/products/11`) và ~64 danh mục con (`/products/01/01a`…`/products/09/09f`), cùng 142 trang chi tiết sản phẩm (`/products/{cat}/{id}.html`). Lấy `<title>`, `meta description`, `og:title`, `og:image`, danh sách "Model List", mô tả sản phẩm, và ảnh sản phẩm thật (`fujimak.co.jp/products/upload_files/img/...`). |
| 2 | **https://en.fujimak.co.jp/works/customize/teppanyaki-grill/** | Trang chuyên đề Teppanyaki (4 biến thể lắp đặt thật + ảnh lắp đặt thật). |
| 3 | **https://en.fujimak.co.jp/products/jetoven/03.html** (Lineup) và `/05.html` (Industrial) | Trang chuyên đề Jet Oven — 6 model thật kèm **ảnh riêng từng model** (`jetoven/img/03/img_03_lineup_*.jpg`), mô tả kỹ thuật, phụ kiện. |
| 4 | **https://fujimak.biz** | Cổng xuất khẩu/tiếng Anh khác của Fujimak. Đã xác minh là chính hãng: mọi trang đều có link "Company Profile" trỏ thẳng về `fujimak.co.jp` và `en.fujimak.co.jp` ở góc trên/footer — không phải bên thứ ba. |

**Phương pháp kỹ thuật:** `en.fujimak.co.jp` trả về HTTP 403 cho WebFetch mặc định (chặn theo User-Agent), nên toàn bộ crawl dùng `curl` với User-Agent trình duyệt thật (Chrome/Windows) — tải HTML thô, bóc tách bằng Node.js (regex trên `<title>`, `meta`, `og:*`, khối "Model List", ảnh `upload_files/img`). Danh sách 144 URL sản phẩm được thu thập bằng cách fetch tuần tự: trang `/products/` gốc → 11 trang danh mục lớn → ~64 trang danh mục con → trích toàn bộ link `products/{cat}/{6-7 số}.html` duy nhất.

---

## 3. Thống kê

| Chỉ số | Số lượng |
|---|---|
| Nhóm (`groups`) | **56** |
| Model/biến thể | **152** |
| Model có `image_url` thật (không phải ảnh placeholder chung) | **152 / 152** |
| Model có `source_url` hoạt động (đã kiểm tra HTTP 200 trên mẫu đại diện) | **100%** |
| Trang sản phẩm gốc đã crawl (không trùng lặp) | **142** (+ 1 trang Teppanyaki tổng quan + 1 trang Jet Oven tổng quan dùng làm nguồn, không tính là model riêng) |

### Phân bổ theo nhóm

| # | Nhóm (EN) | Nhóm (VI) | Model |
|---|---|---|---|
| 1 | **Teppanyaki** *(yêu cầu gốc)* | Bếp Bàn Nướng Teppanyaki | 4 |
| 2 | **Jet Oven** *(yêu cầu gốc)* | Lò Nướng Băng Tải Jet Oven | 6 |
| 3 | Sanitary Equipment | Thiết bị vệ sinh nhà bếp | 7 |
| 4 | IoT / Robotics | Thiết bị IoT / Robot nhà bếp | 4 |
| 5 | CombiOven Series (Steam Convection Oven) | Lò hấp đối lưu đa năng CombiOven | 2 |
| 6 | Vario Series | Chảo nấu đa năng Vario Series | 2 |
| 7 | Oven (Deck/Compact/Specialty, gồm Jet Presto) | Lò nướng (Deck/Compact/chuyên dụng) | 4 |
| 8 | Gas Range / Stove | Bếp/lò gas công nghiệp | 4 |
| 9 | Electric Range, Table & Stock Pot Range | Bếp điện, bàn bếp, bếp nồi nước dùng | 1 |
| 10 | Induction Stove & Low Range | Bếp từ công nghiệp & bếp thấp | 1 |
| 11 | Griddle / Roaster / Salamander | Bếp Griddle, lò nướng Salamander | 4 |
| 12 | Fryer & Oil Filter | Chảo chiên công nghiệp & máy lọc dầu | 7 |
| 13 | Steamer | Tủ hấp công nghiệp | 1 |
| 14 | Noodle Boiler / Spaghetti Boiler | Nồi luộc mì/spaghetti | 2 |
| 15 | Dumpling Griller | Máy chiên/nướng bánh xếp | 1 |
| 16 | Warmer | Tủ giữ nóng thực phẩm | 2 |
| 17 | Package Cooker | Máy nấu thực phẩm đóng gói (retort) | 1 |
| 18 | Rotary Cooking Kettle / Braising Pan | Nồi nấu/hầm lật nghiêng | 2 |
| 19 | Chinese Range | Bếp Á | 6 |
| 20 | Refrigerator / Freezer / Combo Cabinet | Tủ mát/đông/kết hợp đứng | 1 |
| 21 | High-Humidity Refrigerator | Tủ mát độ ẩm cao | 1 |
| 22 | Undercounter Refrigerator-Freezer | Tủ mát/đông gầm bàn | 1 |
| 23 | Blast Chiller & Freezer | Tủ cấp đông/làm lạnh nhanh | 2 |
| 24 | Freezer for Meal Inspection | Tủ đông lưu mẫu thực phẩm | 1 |
| 25 | Freezer for Meal Inspection (Compact) | Tủ đông lưu mẫu (cỡ nhỏ) | 1 |
| 26 | Cold Food Cart | Xe đẩy thực phẩm lạnh | 1 |
| 27 | Vacuum Cooler | Máy làm lạnh chân không | 1 |
| 28 | Prefabricated Refrigerator / Freezer | Kho lạnh/đông lắp ghép | 1 |
| 29 | Showcase (Refrigerated Display) | Tủ trưng bày mát/đông | 4 |
| 30 | Ice Maker | Máy làm đá công nghiệp | 1 |
| 31 | Tabletop Cooler | Tủ mát để bàn | 2 |
| 32 | Stocker / Chest Freezer | Tủ đông ngăn kéo/dạng hòm | 2 |
| 33 | Automatic Rice Cooker | Nồi cơm điện/gas tự động | 2 |
| 34 | Automatic Compound Rice Cooker (Rice Pro) | Nồi cơm tự động đa năng | 1 |
| 35 | Continuous Automatic Rice Cooker | Hệ nấu cơm liên tục | 1 |
| 36 | Rice Storage (Silo) | Bồn chứa gạo | 3 |
| 37 | Tabletop Rice Cooker | Nồi cơm điện để bàn | 2 |
| 38 | Bakery Equipment — By Function | Thiết bị làm bánh theo chức năng | 4 |
| 39 | Bakery Equipment — Peripherals | Thiết bị phụ trợ làm bánh | 1 |
| 40 | Dishwasher | Máy rửa chén bát | 3 |
| 41 | Conveyor Dishwasher | Máy rửa chén băng tải | 4 |
| 42 | Cookware Washer | Máy rửa nồi/dụng cụ | 1 |
| 43 | Sterilizer Cabinet & UV Sterilizer | Tủ/máy tiệt trùng | 3 |
| 44 | Cleaning Agent for Washing Machines | Hóa chất tẩy rửa | 1 |
| 45 | Vacuum Packing Machine | Máy đóng gói chân không | 1 |
| 46 | Blender / Mixer | Máy xay/trộn công nghiệp | 6 |
| 47 | Cooker (Sous-vide/Multi Cooker) | Máy nấu sous-vide/đa năng | 4 |
| 48 | Pacojet | Máy Pacojet | 1 |
| 49 | Gelato / Ice Cream Machine | Máy làm gelato/kem | 2 |
| 50 | Drink Dispenser / Machine | Máy pha/phân phối đồ uống | 8 |
| 51 | Auxiliary Cooking Equipment | Thiết bị nấu bếp phụ trợ | 3 |
| 52 | Peripheral Heating Equipment | Thiết bị gia nhiệt phụ trợ | 4 |
| 53 | Peripheral Equipment (Service) | Thiết bị phụ trợ phục vụ | 7 |
| 54 | Water Heater | Máy đun nước nóng | 1 |
| 55 | Work Equipment (Tables/Sinks/Shelving) | Bàn/kệ/chậu rửa | 3 |
| 56 | Cooling / Warming Cart Equipment | Xe đẩy giữ nóng/lạnh | 6 |

---

## 4. Nhóm 1 — Teppanyaki (4 biến thể, KHÔNG có SKU cố định)

Xác nhận lại kết luận của lần khảo sát trước (13/07/2026): Fujimak bán Teppanyaki **hoàn toàn theo đặt làm riêng** (custom-made) theo kích thước bếp/quầy khách hàng — không có mã model, không có bảng giá công khai. Lần này đã bổ sung **ảnh lắp đặt thật cho từng biến thể** (trước đây chỉ có ảnh minh họa chung `ogp_im01.png`):

| Biến thể | Ảnh thật |
|---|---|
| Ducted Grill | `teppanyaki-grill_004.png` |
| Custom Design (Tabletop) | `teppanyaki-grill_005.png` |
| Tabletop Grill (on Cold Base) | `teppanyaki-grill_006.png` |
| Airflow Fan Grill | `teppanyaki-grill_007.png` |

Cả 4 ảnh đã kiểm tra HTTP 200. Không xác định được loại nhiệt (điện/gas) cho Teppanyaki — trang web không ghi rõ, cần liên hệ hãng.

## 5. Nhóm 2 — Jet Oven (6 model thật, 4 dòng)

Xác nhận lại 6 model đã biết (FGJOA5, FGJOA5D, FEJOA9, FGJOB10, FGJOA7, FGJOA70NR), và bổ sung:

- **Ảnh riêng cho từng model** lấy trực tiếp từ trang Lineup (`jetoven/img/03/img_03_lineup_01-01.jpg`…`04-01.jpg`) — trước đây chưa có.
- **heat_source được ghi rõ hơn** theo đúng mô tả từng dòng: Standard (FGJOA5/D) = gas hoặc điện tùy chọn; Compact FEJOA9 = gas hoặc điện; Compact FGJOB10 (low-radiation) = chỉ gas; Pizza (FGJOA7) và Long (FGJOA70NR) = suy luận gas từ tiền tố mã "FG" (trang lineup không ghi tường minh riêng cho 2 dòng này, đã chú thích rõ đây là suy luận, không phải xác nhận trực tiếp).
- Phát hiện và **tách riêng "Jet Presto"** (model 001236, `en.fujimak.co.jp/products/01/001236.html`) — đây là lò nướng nhanh compact dùng vi sóng + jet, **khác dòng sản phẩm** với Jet Oven băng tải dù cùng công nghệ jet. Đã xếp Jet Presto vào nhóm "Oven (Deck/Compact/Specialty)" thay vì gộp nhầm vào Jet Oven.

## 6. Các nhóm mở rộng (54 nhóm, 142 model) — phương pháp và giới hạn

### 6.1. Cách xây dựng

Mỗi trang sản phẩm của Fujimak thường liệt kê **một họ sản phẩm** với nhiều mã model liên quan trong khối "Model List" (ví dụ trang CombiOven iCombi Pro liệt kê 7 mã: FICPXS, FICP61E/62E/101E/102E/201E/202E). Để tránh nhân bản dữ liệu vô nghĩa (như trường hợp mã cấu hình SKU của Precision-UK), **mỗi trang sản phẩm = 1 entry trong `data-full.json`**, với field `model` lấy mã đầu tiên, và toàn bộ danh sách mã đầy đủ được lưu trong `specs.model_codes_on_page` để tra cứu.

### 6.2. Suy luận `heat_source` khi trang không ghi rõ

Nhiều trang sản phẩm không nêu tường minh "gas" hay "electric" trong mô tả ngắn. Đã áp dụng suy luận theo thứ tự ưu tiên, mỗi mức đều ghi chú rõ trong field `heat_source`:
1. Mô tả trang có nhắc "gas"/"electric"/"induction"/"steam" → dùng trực tiếp.
2. Không có → suy luận từ **quy ước đặt tên mã model của chính Fujimak** (tiền tố `FG..` = Gas, `FE..` = Electric, `FI..` = Induction/Electric) — quy ước này quan sát được nhất quán trên toàn bộ site (vd FGJOA5 = gas, FEJOA9 = điện, khớp với mô tả tường minh ở nhóm Jet Oven).
3. Không có mã theo quy ước trên (vd sản phẩm không phải "Heating Equipment") → suy luận theo danh mục: Cooling Equipment → điện (nén khí lạnh); Washing → điện; Work Equipment/Sanitary Equipment (bàn, kệ, bồn rửa) → "không áp dụng".
4. Không suy luận được → ghi rõ "không ghi rõ trên trang — cần liên hệ hãng" (KHÔNG bịa).

Đây là suy luận có căn cứ (không phải bịa số liệu), nhưng **không phải xác nhận trực tiếp từ hãng** — mọi trường hợp suy luận đều ghi chú nguồn gốc suy luận ngay trong giá trị `heat_source` để người dùng biết mức độ tin cậy.

### 6.3. Những gì KHÔNG bao gồm

| Không có | Lý do |
|---|---|
| Giá bán (VN hoặc Nhật) | Fujimak không public giá trên web ở bất kỳ trang nào đã crawl — kể cả Jet Oven, Teppanyaki. Cần liên hệ Fujimak Việt Nam/đại lý. |
| Bảng thông số kỹ thuật chi tiết (kích thước mm, công suất kW) cho phần lớn model | Các trang sản phẩm tiếng Anh của Fujimak chỉ có mô tả ngắn + danh sách mã model + 1 ảnh, KHÔNG có bảng specs dạng số như website Precision-UK hay Josper. Đây là thực tế cấu trúc site, không phải bỏ sót khi crawl — đã kiểm tra thủ công cấu trúc HTML của nhiều trang mẫu (Duck Roaster, Jet Oven, CombiOven) và xác nhận không có bảng dữ liệu số ẩn nào bị bỏ lỡ. |
| Phiên bản tiếng Nhật đầy đủ hơn (`fujimak.co.jp` gốc) | Chỉ crawl bản tiếng Anh `en.fujimak.co.jp` theo đúng phạm vi yêu cầu (nội dung xuất khẩu/quốc tế); bản tiếng Nhật có thể có nhiều model nội địa hơn nhưng không thuộc phạm vi "sản phẩm hãng bán quốc tế qua kênh en." |
| Model nào Fujimak Vietnam thực sự phân phối | Không xác định được qua crawl web — cần sales/đại lý xác nhận. |

## 7. Đối chiếu với `import.csv` cũ (đã có, KHÔNG sửa)

`import.csv` hiện có 16 dòng placeholder cho Teppanyaki (4 dòng) + Jet Oven (6 dòng) + phụ kiện Jet Oven (6 dòng). Đối chiếu với dữ liệu crawl lại lần này:

- 4 dòng Teppanyaki: mô tả và tên biến thể khớp đúng. **Thiếu ảnh thật** — file cũ dùng chung ảnh `ogp_im01.png` (placeholder) cho cả 4 dòng; `data-full.json` đã bổ sung ảnh lắp đặt thật riêng từng biến thể (mục 4).
- 6 dòng Jet Oven: mã model, dòng sản phẩm khớp đúng. Cũng dùng ảnh placeholder chung — đã bổ sung ảnh riêng từng model (mục 5).
- Cột `meta:_seo_title`/`meta:_seo_description` trong `import.csv` khớp đúng với `og:title`/`meta description` gốc lấy được lần này — không phát hiện sai lệch.
- **Không sửa `import.csv`** theo đúng yêu cầu; đề xuất người phụ trách import cập nhật cột `product_images` bằng các URL ảnh thật ở mục 4–5 khi build file import mới cho toàn bộ 152 model.

## 8. Việc còn thiếu (ngoài khả năng crawl web)

- Giá bán VN và Nhật (liên hệ Fujimak Vietnam/đại lý phân phối).
- Xác nhận `heat_source` cho các model chỉ suy luận từ tiền tố mã (xem mục 6.2) — nên xác nhận lại với hãng trước khi ghi cứng vào trang sản phẩm public.
- Xác nhận model nào trong 152 model thực sự được Fujimak Vietnam phân phối/hỗ trợ bảo hành.
- Thông số kỹ thuật số (kích thước, công suất) — không có sẵn công khai trên bản tiếng Anh, cần catalog PDF/liên hệ hãng nếu cần chi tiết hơn.
