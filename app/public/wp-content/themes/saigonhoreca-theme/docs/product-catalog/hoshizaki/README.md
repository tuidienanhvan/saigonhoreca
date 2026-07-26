# Hoshizaki — Nghiên cứu bổ sung danh mục

Nguồn: 2 site chính hãng thuộc Hoshizaki Group — `hoshizaki.com/en` (tập đoàn, Nhật Bản) và `hoshizaki-sea.com` (Hoshizaki South East Asia, dùng làm nguồn chính vì gần thị trường Việt Nam nhất).
Ngày cào dữ liệu: 2026-07-13. Dữ liệu máy đọc song song ở [`data.json`](./data.json).

## Vì sao có tài liệu này

Site hiện có **52 sản phẩm** brand Hoshizaki, chủ yếu là máy làm đá (ice machine) và tủ lạnh/tủ đông công nghiệp. Client muốn làm giàu danh mục bằng các dòng sản phẩm thật từ lineup toàn cầu của Hoshizaki — kể cả những nhóm site có thể chưa bán.

## 1. Site chính hãng đã xác minh

| Site | URL | Ghi chú |
|---|---|---|
| Hoshizaki Global | https://www.hoshizaki.com/en | Trang tập đoàn — chỉ liệt kê nhóm sản phẩm cấp cao (Ice Makers/Refrigeration/Dishwashers/Dispensers/Cooking Equipment). Trang con `/solutions-innovation/products/ice-makers` trả lỗi **403 Forbidden** khi fetch tự động — không đọc được nội dung chi tiết qua tool, chỉ biết cấu trúc qua kết quả tìm kiếm web. |
| Hoshizaki South East Asia (nguồn chính) | https://www.hoshizaki-sea.com/products/ | Fetch được đầy đủ. Catalog 462 SKU, có bộ lọc theo quốc gia (Vietnam, Thailand, Malaysia, Singapore, Indonesia, Philippines) — site khu vực sát thị trường VN nhất. Mỗi model có trang riêng: mô tả + nút "GET QUOTE" + link "Spec Sheet"/"Dimensional Drawing" PDF (không public giá). |
| Hoshizaki America | https://www.hoshizakiamerica.com | `/products/` trả lỗi 404 — chưa dò được đường dẫn category đúng, không khảo sát thêm trong lượt này. |

**Không có giá bán public** trên bất kỳ site nào — mọi trang model chỉ có nút "Get Quote"/"Request Quote". Cần liên hệ Hoshizaki Vietnam/đại lý phân phối chính thức.

## 2. Ice Maker — 7 phân nhóm, ~109 model trên site SEA

| Phân nhóm | Số model (site SEA) |
|---|---|
| Cube (đá viên vuông) | 42 |
| Crescent (lưỡi liềm) | 19 |
| Dice (đá viên nhỏ) | 14 |
| Flaker & Nugget (đá vảy/mềm) | 21 |
| Special Ice | 5 |
| Dispenser | 5 |
| Ice Storage Bin | 3 |

3 kiểu cấu hình: **Self Contained** (47, máy+khoang chứa liền khối), **Modular** (44, đầu máy lắp rời trên bin), **Stackable** (8, xếp chồng lên bin có sẵn).

Model mẫu đã lấy được (mô tả + URL, xem đầy đủ trong `data.json.groups[0].sample_models`): `DCM-120KE` (ice/water dispenser), `IM-480DNS-STCRN` (cube & crushed, modular), `FM-1000AKS-N-SB-B-501SA` (nugget, modular), `FM-1000AKS-SB-B-501SA` (flaker, modular), `SRM-650AB-B-801SA` / `SRM-650AB-14-B-801SA` (dice/half-dice).

Nguồn: [Ice Maker category — SEA](https://www.hoshizaki-sea.com/product-category/ice-maker/), [Ice Makers — Global](https://www.hoshizaki.com/en/solutions-innovation/products/ice-makers)

**Thiếu**: thông số kỹ thuật chi tiết (công suất kg/24h, kích thước, điện áp) nằm trong PDF "Spec Sheet"/"Dimensional Drawing" riêng từng model — chưa trích xuất được nội dung PDF qua tool.

## 3. Refrigeration — 3 phân nhóm theo dạng tủ, 6 dòng (series) đặt tên

| Phân nhóm | Số model |
|---|---|
| Upright (tủ đứng) | 110 |
| Undercounter (tủ dưới quầy) | 206 |
| Low Undercounter (tủ thấp) | 12 |

Dòng (series) theo tên thương mại: **Premier** (120 SKU — cao cấp, refrigerant tự nhiên, hiệu suất năng lượng cao; có Upright Long Glass Door + Undercounter Swing Glass Door + Bakery Cabinet), **A-Fit** (123), **A1-FIT** (30), **Gold Line** (17), **PRO** (18), **Bakery Cabinet** (4). Riêng **Signature** và **Blast Chiller/Freezer** xuất hiện như filter nhưng hiện 0 sản phẩm gắn trên site SEA — có thể chỉ bán ở thị trường khác hoặc dòng mới chưa cập nhật.

Model mẫu dòng Premier: `FT-98MC-S(P)-ST` (Bakery Freezer), `RT-98MC-(S)P-ST` (Bakery Refrigerator), `HR-188MC-SLG`/`HR-148MC-SLG` (Upright Long Glass Door), dải `RT-188MC` đến `RT-96MC` với hậu tố `-S(P)G` (Undercounter Swing Glass Door).

Facet loại: Refrigerator (186) / Freezer (104) / Dual Temp (18). Facet cửa: Solid Door (186) / Glass Door (60) / Drawer (32).

Nguồn: [Refrigeration category — SEA](https://www.hoshizaki-sea.com/product-category/refrigeration/), [Refrigeration — Global](https://www.hoshizaki.com/en/solutions-innovation/products/refrigeration)

## 4. Nhóm sản phẩm MỚI — chưa có trong 52 SKU hiện tại

| Nhóm | Model thật đã xác minh | Nguồn |
|---|---|---|
| **Dishwasher** (máy rửa chén) | `OBY 50T`, `HW-320B-S`, `HW-320B` (undercounter); `OBM 1080T`, `HW-600B`, `HW-600B3` (hood-type) | [Dishwasher category](https://www.hoshizaki-sea.com/product-category/dishwasher/) |
| **Sushi Showcase** (tủ trưng bày sushi) | `HNC-210BE`, `HNC-180BE`, `HNC-150BE`, `HNC-120BE` — mỗi cỡ có bản trái (`-L-B`) và phải (`-R-B`) = 8 SKU | [Sushi Showcase category](https://www.hoshizaki-sea.com/product-category/sushi-showcase/) |
| **Beer & Soda Dispenser** | `DS-25WLD` (soda), `DBF-40WAC`, `DBF-40SAC`, `DBF-25SAC`, `DBF-AS65WE-T` (beer, có bản tự động) | [Beer & Soda Dispenser category](https://www.hoshizaki-sea.com/product-category/beer-soda-dispenser/) |
| **Deep Freezer** (tủ đông sâu, chest-type) | `HDF-400A-SEA`, `HDF-300A-SEA`, `HDF-140A-SEA` | [Deep Freezer category](https://www.hoshizaki-sea.com/product-category/deep-freezer/) |
| **Thawing Cabinet** (tủ rã đông) | `HR-78MA-S-JD` (chỉ 1 model công bố) | [Thawing Cabinet category](https://www.hoshizaki-sea.com/product-category/thawing-cabinet/) |
| **Cooking Equipment** | `FL-20B-SEA` (electric fryer — mảng rất hẹp, chỉ 1 model) | [Cooking Equipment category](https://www.hoshizaki-sea.com/product-category/cooking-equipment/) |

**Đề xuất ưu tiên**: Sushi Showcase và Dishwasher là 2 nhóm có nhiều SKU thật nhất và phù hợp nhất với khách hàng nhà hàng Nhật/HORECA tại VN — nên ưu tiên tạo draft trước. Beer & Soda Dispenser phù hợp phân khúc bar/pub. Deep Freezer/Thawing Cabinet/Cooking Equipment là mảng ngách (1-3 SKU), cân nhắc theo nhu cầu thực tế.

## Việc còn thiếu (ngoài khả năng crawl web)

- **Giá bán VN** — Hoshizaki không niêm yết giá công khai ở bất kỳ site nào (global, SEA, America); mọi trang chỉ có nút Get Quote/Request Quote. Cần liên hệ Hoshizaki Vietnam/đại lý phân phối.
- **Thông số kỹ thuật chi tiết** (công suất kg/24h, kích thước, điện áp, công suất điện) nằm trong PDF "Spec Sheet"/"Dimensional Drawing" riêng từng model trên site SEA — tool chưa trích xuất được nội dung PDF, cần tải thủ công nếu muốn nhập specs đầy đủ vào WooCommerce.
- **Lọc theo quốc gia Vietnam** — site SEA có filter quốc gia (Vietnam/Thailand/Malaysia/...) nhưng agent chưa bắt được checkbox filter qua fetch tự động (cần tương tác JS/click thật). Nếu cần danh sách CHÍNH XÁC chỉ những model Hoshizaki phân phối tại VN, cần user tự vào site tick filter Vietnam.
- **Đối chiếu 52 SKU hiện có** trên `saigonhoreca.local` (brand Hoshizaki) với danh sách dòng/model ở trên — chưa làm vì agent không có quyền truy vấn DB/WP-admin trong phiên này; cần dev/admin đối chiếu thủ công trước khi thêm sản phẩm mới để tránh trùng lặp.
- **Ảnh sản phẩm chính hãng độ phân giải cao** — chưa tải, chỉ có URL trang sản phẩm.
- **Hoshizaki America** (`hoshizakiamerica.com`) — chưa dò ra đường dẫn category đúng (trang `/products/` trả 404); có thể khảo sát thêm ở lượt sau nếu cần so sánh thị trường Mỹ.
