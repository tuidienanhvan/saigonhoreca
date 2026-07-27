# Kolb — Ghi chú nghiên cứu catalog (18/07/2026)

## Kolb nào? (ĐÃ SỬA — đây mới là hãng đúng)
**KOLB — thiết bị làm bánh chuyên nghiệp (bakery/pizza equipment), gốc Thụy Sĩ.**
- Do **Ernst Kolb**, một thợ bánh bậc thầy người Thụy Sĩ, sáng lập tại Thụy Sĩ năm **1935**.
- Mở rộng sang châu Á: Kolb Hong Kong (1985), rồi **Kolb Huizhou** (2005) — trung tâm R&D và sản xuất chính cho Nam Trung Quốc.
- Năm 2017 gia nhập gia đình **Wiesheu** (đối tác công nghệ lò nướng Đức).
- Website chính hãng: **https://www.kolbcn.com/html/en/index.html**.
- Chuyên: lò nướng tầng (Laguna), lò đối lưu (Atoll), lò xoay (Tornado), lò pizza, tủ ủ bột, tủ ủ trữ lạnh, máy trộn, và thiết bị xử lý bột.

## ⚠️ Đã THAY THẾ dữ liệu SAI trước đó
Bản catalog cũ tại file này nhầm sang **KOLB GesmbH "Back & Gastrotechnik" — Meiningen, Áo (www.kolb.at)**, một hãng máy chiên ngập dầu (Fettbackgeräte) / gastro hoàn toàn khác. Đó là **kết quả sai** và toàn bộ `data.json` + `README.md` đã bị **ghi đè** bằng dữ liệu Kolb làm bánh đúng. Bỏ qua hoàn toàn kolb.at.

## Nguồn dữ liệu (thật)
- **Chính (danh mục model):** `https://www.gastro.co.id/brand/kolb` — nhà phân phối Đông Nam Á, khớp với thứ khách hàng Việt đang bán. Đây là danh sách model chuẩn.
- **Đối chiếu (mô tả/thông số/hình):** trang từng sản phẩm trên gastro.co.id (`/product/kolb-...`) + nhà sản xuất chính hãng `kolbcn.com`.
- Mọi model đều có `source_url` thật.

## Số liệu
- **Nhóm (categories): 11** — Lò Pizza/Nhiều Tầng · Lò Xoay · Tủ Ủ Bột · Tủ Ủ Trữ Lạnh (Retarder) · Máy Trộn Hành Tinh · Máy Trộn Xoắn · Máy Chia Bột · Máy Cán Bột · Máy Tạo Hình · Máy Cắt Lát · Tùy Chỉnh & Trọn Bộ.
- **Models: 28** (26 model có mã + 1 mục Custom theo yêu cầu + 1 mục Bundle thiết bị chuẩn bị bột) — khớp danh mục ~29 trên gastro.co.id (đủ toàn bộ các model có mã đang niêm yết).
- Phân bố: Pizza/Deck 3 · Rotary 2 · Proofer 4 · Retarder Proofer 4 · Planetary Mixer 3 · Spiral Mixer 3 · Divider 3 · Sheeter 2 · Moulder 1 · Slicer 1 · Custom/Bundle 2.

## Logo
- Kolb brand badge (chevron đỏ/cam/vàng + chữ "Kolb"): **https://www.gastro.co.id/assets/brands/kolb.png**

## Nguồn gốc sản xuất
- Lò & tủ ủ (K04/K05/K11/K12): thương hiệu Thụy Sĩ, sản xuất tại **Trung Quốc** (Kolb Huizhou) → `Switzerland/China`.
- Máy trộn & thiết bị xử lý bột (K31/K32/K41-K45): thương hiệu Thụy Sĩ, ghi sản xuất **Đài Loan** trên gastro → `Switzerland/Taiwan`.

## Thiếu / hạn chế (độ tin cậy: KHÁ, danh mục model CHẮC CHẮN)
- Trang chi tiết gastro.co.id **không có meta description** và không có `og:image` → `seo_description` để trống; `image_url` lấy từ ảnh sản phẩm thật `/assets/products/kolb-<model>.jpg`.
- `seo_title` lấy được dạng "Jual Kolb <model> Komersial · GASTRO" cho phần lớn model; hai mục Custom/Bundle không có title rõ nên để trống.
- Nhiều trang chỉ công bố **kích thước (mm)**; công suất kW / điện áp / nhiệt độ / dung tích chi tiết phần lớn nằm trong PDF catalog (không nhúng), nên các trường đó để trống thay vì bịa. Các model đã fetch trực tiếp có số thật: K05-0604T1H (34.60 kW, 985 kg, 1280×1173×2466), K31-0401AB4 (40 L, 1.13 kW, 185 kg), K32-0201AB4 (20 kg bột), cùng kích thước của nhiều tủ ủ/divider/sheeter/moulder/slicer.
- Mô tả cùng họ sản phẩm dùng chung template mô tả của gastro (đúng như trang gốc), đã dịch sang tiếng Anh cho `description_en`.
- examasjaya.com/kolb (Malaysia) không cần thiết để bổ sung — gastro.co.id đã đủ danh mục ~29 model.
