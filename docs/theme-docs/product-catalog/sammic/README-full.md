# Sammic — Catalog đầy đủ (`data-full.json`)

Ngày cào dữ liệu: **2026-07-19** (cập nhật bổ sung cùng ngày) · Brand string: **`Sammic`**
File dữ liệu: `data-full.json` (15 nhóm · 186 model)

> Lưu ý: file này **độc lập** với `data.json`. `data.json` là bản đã import làm draft trước đó
> (27 sản phẩm, gộp 2 taxonomy "Sammic" + "Sammic-Spain") và **không bị chỉnh sửa**.
> `data-full.json` là bản deep-crawl toàn danh mục sammic.com/en.

---

## 1. Nguồn dữ liệu

Toàn bộ dữ liệu lấy thật từ website chính hãng **sammic.com/en** (Tây Ban Nha). Mỗi trang sản phẩm
được tải trực tiếp và bóc tách: `<title>`, mô tả thật, bảng thông số kỹ thuật thật, ảnh sản phẩm
thật (CDN `storage.googleapis.com/sammic-media-prod`), `source_url`. `seo_title`/`seo_description`
ở bản này là **nguyên văn tiếng Anh lấy từ site** — CHƯA Việt hóa (bước Việt hóa SEO sẽ chạy sau
bằng `gen_seo.py` theo đúng pipeline đã dùng cho 5 hãng Tier 1).

Do khối lượng lớn, việc cào được chia thành 8 lượt song song theo nhóm sản phẩm, sau đó tổng hợp
thủ công vào 1 file duy nhất (route sub-agent → sub-agent bị lỗi không gửi được kết quả nội bộ nên
main agent trực tiếp thu thập và ghép file).

---

## 2. Bảng thống kê theo nhóm

| Nhóm (EN) | Nhóm (VI) | Model |
|---|---|---:|
| Commercial Vegetable Preparation Machines (Food Processors) | Máy Chế Biến Rau Củ Đa Năng | 8 |
| Cutter-Mixers & Emulsifiers | Máy Xay Trộn Cắt Đa Năng (Cutter-Mixer) | 6 |
| Combi Veg Prep / Food Processor (CK Series) | Máy Chế Biến Rau Củ Kết Hợp (dòng CK) | 6 |
| Commercial Vacuum Packing Machines | Máy Hút Chân Không Thương Mại | 40 |
| Blast Chillers / Freezers | Tủ Cấp Đông/Làm Lạnh Nhanh | 10 |
| SmartVide — Sous-Vide Immersion Circulators | Máy Nấu Sous-Vide Ngâm Nước Chính Xác | 4 |
| Planetary Mixers | Máy Trộn Bột Đa Năng (Planetary Mixer) | 13 |
| Commercial Slicers | Máy Thái Lát Thực Phẩm Thương Mại | 15 |
| Commercial Potato Peelers | Máy Gọt Vỏ Khoai Tây Thương Mại | 9 |
| Commercial Salad Spinners | Máy Vắt Rau Ráo Nước Thương Mại | 2 |
| Commercial Meat Mincers | Máy Xay Thịt Thương Mại | 5 |
| Commercial Hand Blenders (Immersion Blenders) | Máy Xay Cầm Tay Công Nghiệp | 17 |
| Hand Operated Potato Chipping Machine (French Fry Cutter) | Máy Cắt Khoai Tây Chiên Cầm Tay | 1 |
| Accessories | Phụ Kiện | 2 |
| Ware-Washing (Commercial Dishwashers & Glasswashers) | Máy Rửa Chén & Máy Rửa Ly Thương Mại | 48 |
| **TỔNG** | | **186** |

---

## 3. Ghi chú quan trọng

1. **Nhóm "French Fry Cutter" đã được bổ sung** (lượt crawl thứ 2, cùng ngày 2026-07-19), khắc phục
   thiếu sót của đợt deep-crawl trước (bị bỏ sót khi chia 8 lượt crawl song song, để lại 0 model).
   Đã fetch trực tiếp `https://sammic.com/en/products/french-fry-cutter` — trang danh mục chỉ có
   đúng **1 model duy nhất: `CF-5`** (Potato chipping machine CF-5), khớp với README.md cũ
   (2026-07-13). Đã lấy đầy đủ mô tả, bảng thông số, ảnh sản phẩm thật và `source_url` từ
   `https://sammic.com/en/product/cf-5`. Sammic hiện vẫn bán sản phẩm này — không có ghi nhận nào
   cho thấy site đã ngừng bán.
2. **2 sản phẩm bị xếp nhầm nhóm ở lượt cào đầu, đã sửa lại đúng vị trí:**
   - `CR-143` — ban đầu lẫn vào "Commercial Slicers", thực chất là phụ kiện đĩa/lưới cắt gắn vào
     máy trộn bột (BP-10C/20C/30C/40C) hoặc máy gọt khoai PPC — đã chuyển sang nhóm **Accessories**.
   - `HM-71` — ban đầu lẫn vào "Commercial Hand Blenders", thực chất là phụ kiện xay thịt gắn vào
     máy trộn bột/máy gọt khoai — đã chuyển sang nhóm **Accessories**.
3. **Ware-Washing** là nhóm hoàn toàn mới so với `data.json` cũ (trước đó "models: 0"). Gồm 5
   category: glasswasher (5), glass_pro (3), dishwasher_undercounter (8), dishwasher_passthrough
   (19), utensil_washer (6), cộng thêm 10 model chỉ có specs ở mức trang danh mục (7 tunnel
   dishwasher SRC-series + 3 cutlery dryer/polisher SAM/SAS-series — chưa có trang sản phẩm riêng
   trên site nên thông số sơ sài hơn, đã đánh dấu `note` trong JSON).
4. **Nghi vấn cần kiểm tra lại thủ công:**
   - `SU-806CC` — **ĐÃ XÁC MINH (2026-07-19)**: fetch lại trực tiếp trang
     `https://sammic.com/en/product/su-806-cc` (2 lần độc lập, kết quả nhất quán) xác nhận trang
     chính hãng thực sự hiển thị **"Net weight: 511 kg"** — KHÔNG phải lỗi parse bảng của lượt cào
     trước, số liệu đã được cào đúng như site hiển thị. Tuy nhiên số liệu này có **mâu thuẫn nội
     tại**: "Gross weight (crated)" của chính SU-806CC chỉ là 273 kg — thấp hơn "Net weight" 511 kg,
     điều này phi lý về vật lý (gross/crated luôn phải ≥ net). Nhiều khả năng đây là **lỗi nhập liệu
     từ chính Sammic** trên website của họ (có thể con số đúng phải gần với SU-806/SU-806LL là 232
     kg), không phải lỗi crawl. Đã giữ nguyên 511 kg trong `data-full.json` (không tự bịa/sửa số
     liệu) nhưng đã ghi chú rõ nghi vấn này trong field `note` của model — cân nhắc liên hệ Sammic
     xác nhận hoặc ẩn field "Net weight" của riêng model này khi hiển thị lên site nếu cần độ chính
     xác cao.
   - `AT-7 1/1 PW` — URL slug ghi "at-8p-1-1" nhưng nội dung trang (title/H1) là AT-7, không có
     model "AT-8" độc lập.
   - Nhiều URL `ux-120-xl*` là slug cũ đã được site đổi hướng sang dòng model UX-130BC — đã ghi
     chú trong từng entry liên quan.
   - `MB-51` — trang gốc có 2 nhãn i18n chưa dịch (`vel_tri_min/max`, `vel_bat_min/max`) thay vì
     "Motor speed" — giữ nguyên như site, không tự sửa.
5. **`seo_description` rỗng cho phần lớn model** — xác nhận site sammic.com/en không đặt
   `<meta name="description">` trên đa số trang sản phẩm (chỉ nhóm Hand Blenders có đầy đủ).
6. Không có giá bán công khai trên bất kỳ trang nào — nhất quán với ghi nhận ở `README.md` cũ.

`data.json` và `import.csv` không bị đụng tới.
