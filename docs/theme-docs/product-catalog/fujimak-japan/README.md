# Fujimak — Teppanyaki & Jet Oven — Nghiên cứu bổ sung danh mục

Nguồn: website chính hãng Fujimak Corporation (Nhật) — bản tiếng Anh `en.fujimak.co.jp`.
Ngày cào dữ liệu: 2026-07-13. Dữ liệu máy đọc song song ở [`data.json`](./data.json).

## Vì sao có tài liệu này

Client yêu cầu thêm 2 nhóm sản phẩm premium mới dưới brand **Fujimak-Japan**: "Teppanyaki" và "Jet Oven".
Kiểm tra 16 sản phẩm Fujimak-Japan hiện có trên site (`product_brand=7`) — **không sản phẩm nào** thuộc 2 dòng này. Đây là sản phẩm cần thêm mới, không phải gắn lại nhãn.

## 1. Teppanyaki — KHÔNG có SKU cố định

Fujimak bán Teppanyaki hoàn toàn theo dạng **đặt làm riêng (custom-made)** theo kích thước bếp/quầy khách hàng — không có mã model, không có bảng giá niêm yết công khai.

4 biến thể lắp đặt:
| Biến thể | Mô tả |
|---|---|
| Ducted Grill | Tích hợp ống hút khói trong thân máy, không cần hood trần, có chữa cháy tự động trong ống — dùng ở khách sạn phục vụ mặt-đối-mặt |
| Custom Design (Tabletop) | Bàn nướng đặt bàn theo kích thước bếp, tấm thép dày tùy chỉnh (chuẩn 19mm), điều khiển digital/analog |
| Tabletop Grill (trên tủ lạnh quầy bar) | Lắp trên tủ đông/tủ lạnh quầy bar, lấy nguyên liệu tại chỗ |
| Airflow Fan Grill | Có quạt tạo màn chắn khói/dầu giữa bếp và khách ngồi quầy |

Nguồn:
- [Teppanyaki Grill — Custom Installation](https://en.fujimak.co.jp/works/customize/teppanyaki-grill/)
- [Japanese Teppanyaki Grill — Category](https://en.fujimak.co.jp/products/01/01q/)
- [Custom Made Teppanyaki (fujimak.biz)](https://fujimak.biz/products/teppanyaki-griddle/)
- [PDF chi tiết](https://fujimak.biz/products/teppanyaki-griddle/pdf/custom_made_teppanyaki_griddle.pdf)

**Đề xuất**: chỉ tạo category rỗng "Teppanyaki" như đã chốt — không có sản phẩm mẫu vì đây là mặt hàng đặt-làm-riêng, không có SKU để nhập.

## 2. Jet Oven — CÓ 6 model thật, 4 dòng

| Dòng | Đối tượng | Model | Ghi chú |
|---|---|---|---|
| Standard | Nhà hàng, izakaya, tiệm bánh, cơm hộp | **FGJOA5** (băng tải 457mm, 1 tầng) | gas/điện |
| Standard | " | **FGJOA5D** (băng tải 457mm, 2 tầng) | gas/điện |
| Compact | Cửa hàng nhỏ, lắp đặt 1360–1400mm | **FEJOA9** | gas hoặc điện, chân đế tùy chọn |
| Compact | " | **FGJOB10** (low-radiation) | chỉ gas, giảm tỏa nhiệt phòng |
| Pizza-only | Tiệm pizza/Ý, pizza >30cm | **FGJOA7** | vòi phun tối ưu riêng cho pizza |
| Long | Nhà máy thực phẩm, suất ăn công nghiệp | **FGJOA70NR** (3-module, bản Thái) | 5,000 hamburger đông lạnh 60g/giờ |

Phụ kiện/tùy chọn: hậu tố **S** = tạo hơi nước (cần boiler ngoài); chế độ **ECO** tiết kiệm năng lượng; **FCVA50/FCVA30** = băng tải hồi khay tự động; hệ kết hợp burner hồng ngoại xa.

Nguồn:
- [Jet Oven — Special Content](https://en.fujimak.co.jp/products/jetoven)
- [Lineup — đủ 4 dòng + model](https://en.fujimak.co.jp/products/jetoven/03.html)
- [Industrial Jet Oven](https://en.fujimak.co.jp/products/jetoven/05.html)
- [Jet Oven product site](https://en.fujimak.co.jp/products/01/000329.html)
- [Jet Presto product site](https://en.fujimak.co.jp/products/01/001236.html)
- [Catalog PDF (thư viện online)](https://fujimak.meclib.jp/library/books/JetOven/book/index.html)

**Đề xuất**: có đủ tên model + mô tả để mimo soạn draft 6 sản phẩm (title/mô tả/category Jet Oven), chờ bạn bổ sung giá + ảnh chính hãng trước khi publish. Chưa có giá — Fujimak không niêm yết giá công khai, cần liên hệ đại lý VN.

## Việc còn thiếu (ngoài khả năng crawl web)

- Giá bán VN (liên hệ Fujimak Vietnam / đại lý phân phối)
- Ảnh sản phẩm chính hãng (site chỉ có ảnh minh họa nhỏ, không có full-res catalog ảnh public tải được)
- Xác nhận model nào Fujimak Vietnam thực sự phân phối (không phải mọi model Nhật đều bán tại VN)
