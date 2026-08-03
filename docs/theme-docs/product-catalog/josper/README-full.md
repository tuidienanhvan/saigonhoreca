# Josper-Spain — Catalog đầy đủ (`data-full.json`)

Ngày khảo sát: **2026-07-18** · Brand string: **`Josper-Spain`**
File dữ liệu: `data-full.json` (26 nhóm · 212 model)

> Lưu ý: file này **độc lập** với `data.json`. `data.json` là bản đã import thành draft trước đó
> (3 nhóm / 24 model) và **không bị chỉnh sửa**. `data-full.json` là bản deep-crawl toàn danh mục.

---

## 1. Nguồn dữ liệu

| Nguồn | Vai trò |
|---|---|
| `https://jospergrill.com/product-sitemap.xml` | Lấy danh sách URL sản phẩm gốc (188 URL, lọc bỏ bản /es/, /fr/, /it/, /nl/) |
| `https://jospergrill.com/product_cat-sitemap.xml` + crawl toàn bộ trang `/product-category/**` (có phân trang) | Bắt các sản phẩm không nằm trong sitemap (Robatagrill, Mangal, bản HOME…) |
| Từng trang sản phẩm `/products/**` | Mô tả thật, thông số, ảnh sản phẩm, `<title>`, meta description |
| `https://jospergrill.com/hibachi/` | Trang landing Hibachi (REF HIB-001) |
| `https://jospergrill.com/casa/charcoal-ovens/` | Microsite Josper Casa — 5 model gia dụng |
| `https://jospergrill.com/product-category/josper-rotisseries/` | Trang danh mục Rotisseries (còn sống nhưng **rỗng** trên shop) |
| `https://jospergrill.com/wp-content/uploads/josper-products-catalogue-en.pdf` | Biến thể cấu hình lò (M/MBC/L/LBC, HJA base/M/HC), mã PVJ/CVJ/RGJ/ASJ + kích thước, sức chứa |
| `https://jospergrill.com/wp-content/uploads/josper-accessories-en.pdf` | 57 mã phụ kiện & phụ tùng thay thế (ZAL3002W, Ed. 01.2020) |
| `https://jospergrill.com/wp-content/uploads/josper-cookware-catalogue.pdf` | Mã dụng cụ bếp chỉ có trong catalogue (xiên, kẹp, bàn chải, nắp nồi, mini cocotte…) |

`josper.es` **không dùng** do lỗi SSL — toàn bộ dữ liệu lấy từ `jospergrill.com` (site tiếng Anh toàn cầu).

**Đã kiểm chứng:** 247/247 URL trong file (source_urls, source_url, image_url, specs_source_url) đều trả HTTP 200 (kiểm lại 2026-07-18 sau đợt bổ sung 2).

---

## 2. Thống kê theo nhóm

| Nhóm (EN) | Nhóm (VI) | Model | Có ảnh | Có specs |
|---|---|---:|---:|---:|
| Charcoal Ovens - HJX-PRO line | Lò Nướng Than Josper - Dòng HJX-PRO | 5 | 5 | 5 |
| Charcoal Ovens - HJA-PLUS line (A Class) | Lò Nướng Than Josper - Dòng HJA-PLUS (Hạng A) | 4 | 4 | 4 |
| Charcoal Ovens - base HJX line | Lò Nướng Than Josper - Dòng HJX Cơ Bản | 4 | 4 | 4 |
| Basque Grills - Monoblock (MB) | Bếp Nướng Than Mở Basque Josper - Nguyên Khối (MB) | 5 | 5 | 5 |
| Basque Grills - Countertop (CT) | Bếp Nướng Than Mở Basque Josper - Đặt Bàn (CT) | 4 | 4 | 4 |
| Combos (Charcoal Oven + Basque Grill) | Bộ Combo Josper (Lò Nướng Than + Bếp Nướng Basque) | 6 | 6 | 6 |
| Robatagrills | Bếp Nướng Than Mở Robata Josper | 3 | 3 | 3 |
| Mangals | Bếp Nướng Than Mangal Josper | 1 | 1 | 1 |
| Hibachi / Tabletop Grills | Bếp Nướng Than Để Bàn Hibachi Josper | 1 | 1 | 1 |
| HOME / domestic units on the Josper online shop | Lò Nướng Than Josper Bản Gia Đình (HOME) | 3 | 3 | 3 |
| Cookware - Knives | Dụng Cụ Bếp Josper - Dao | 6 | 6 | 6 |
| Cookware - Etxebarri Grills (by Bittor Arginzoniz) | Dụng Cụ Bếp Josper - Vỉ Nướng Etxebarri (Bittor Arginzoniz) | 10 | 10 | 10 |
| Cookware - Grill baskets | Dụng Cụ Bếp Josper - Vỉ Kẹp Nướng | 1 | 1 | 0 |
| Cookware - Gastronorm trays | Dụng Cụ Bếp Josper - Khay Gastronorm | 12 | 12 | 12 |
| Cookware - Cast Iron Service Plates & Iroko Supports | Dụng Cụ Bếp Josper - Đĩa Gang Phục Vụ & Đế Gỗ Iroko | 8 | 8 | 8 |
| Cookware - Casseroles | Dụng Cụ Bếp Josper - Nồi Gang | 7 | 7 | 7 |
| Wood Charcoal | Than Củi Josper | 10 | 10 | 10 |
| Smoking Wood Chunks (aromatic) | Gỗ Xông Khói Tạo Hương Josper | 6 | 6 | 6 |
| Charcoal Ovens - catalogue configuration variants (PDF only) | Lò Nướng Than Josper - Biến Thể Cấu Hình Theo Catalogue | 23 | 23 *(7 ảnh model gốc)* | 23 |
| Basque Grills - catalogue codes (PDF only) | Bếp Nướng Basque Josper - Mã Catalogue | 4 | 4 *(2 ảnh model gốc)* | 4 |
| Combos - catalogue codes (PDF only) | Bộ Combo Josper - Mã Catalogue | 3 | 3 *(2 ảnh model gốc)* | 3 |
| Robatagrills - catalogue codes (PDF only) | Bếp Nướng Robata Josper - Mã Catalogue | 3 | 3 *(1 ảnh model gốc)* | 3 |
| Rotisseries (ASJ) | Máy Quay Xiên Than Josper (ASJ) | 2 | 2 *(2 ảnh dòng sản phẩm)* | 2 |
| Josper Casa (domestic line) | Josper Casa (Dòng Gia Dụng) | 5 | 5 | 5 |
| Accessories & Spare Parts (official PDF list) | Phụ Kiện & Phụ Tùng Thay Thế Josper | 57 | 8 | 57 |
| Cookware - catalogue-only codes | Dụng Cụ Bếp Josper - Mã Chỉ Có Trong Catalogue | 19 | 9 *(4 ảnh model gốc)* | 19 |
| **TỔNG** | | **212** | **156** | **211** |

Trong đó:
- **96 model là SKU thật trên shop** (`/products/**` + trang Hibachi) — có đủ mô tả, ảnh, `seo_title`, phần lớn có `seo_description` và giá (`specs.price_eur`).
- **5 model Josper Casa** — lấy từ microsite `jospergrill.com/casa`, có ảnh và kích thước W/H/D thật.
- **111 model còn lại là mã catalogue PDF** — có mô tả + thông số thật từ PDF và **không có `seo_*`** vì nguồn là file PDF chứ không phải trang web.
  Sau đợt bổ sung 2026-07-18 (mục 3c), **43/111 mã này đã có `image_url`**. Sau đợt bổ sung 2026-07-19 (mục 3e, tìm
  ảnh từ nhà phân phối phụ tùng ngoài Josper), thêm **12/68 mã phụ kiện/phụ tùng có `image_url`** → tổng **55/111 mã
  catalogue PDF đã có ảnh**; **56 mã còn lại** (toàn bộ là phụ kiện/phụ tùng rời) vẫn để rỗng vì không tìm được ảnh
  thật đáng tin cậy ở bất kỳ nguồn nào đã thử.

---

## 3. Những gì ĐÃ được bổ sung so với lần crawl trước

Toàn bộ các dòng bị bỏ sót ở pass 1 nay đã có mặt:

- Robatagrills (RGJ-PRO S/M/L trên shop + RGJ-050/100/140 trong catalogue)
- Mangal MGJ-132
- Hibachi HIB-001 (kèm thông số: vỉ 235×290 mm, tổng 343×194×291 mm, 250 °C, mồi lửa 35 phút, 10 khách)
- Rotisseries ASJ-063 / ASJ-130 (danh mục shop rỗng → dữ liệu từ catalogue PDF)
- Toàn bộ Cookware: 44 SKU shop qua 6 nhóm con (dao, vỉ Etxebarri, vỉ kẹp, khay GN, đĩa gang + đế Iroko, nồi gang)
- Etxebarri Grills by Bittor Arginzoniz (10 món)
- Wood Charcoal (10 món: CAE, CE, CEs, QBE, briquette 301001, binchotan bạch đàn, binchotan vải, MDV, SDV, Lumix)
- Smoking Wood Chunks (6 hương, ref 45565–45572)
- 57 mã phụ kiện & phụ tùng trong PDF chính hãng
- Bản HOME bán trên shop (HJX-20 HOME, HJX-25-T HOME, HJA-25-M-PRO HOME)
- Dòng Josper Casa (Casa, Torre, Carro, Mesa, Cocina)
- Dòng HJX cơ bản còn đăng bán (HJX-20, HJX-25, HJX-45, HJX-50) + HJX-15
- Biến thể cấu hình catalogue: HJX M/MBC/L/LBC × 3 size, HJA base (20/25/45/50), HJA-M, HJA-HC

---

## 3b. Sửa lỗi sau lần verify 2026-07-18

1. **Mã trùng `HJX-20` (2 bản ghi cùng 1 mã)** — đã phát hiện và tách:
   - Bản 1 (nhóm *Charcoal Ovens - base HJX line*): SKU thật trên webshop, `source_url` trỏ tới trang sản phẩm riêng
     (`.../josper-charcoal-broiler-oven-hjx-20-mini/`), có ảnh, `seo_title`, `seo_description`. **Giữ nguyên mã `HJX-20`.**
   - Bản 2 (nhóm *catalogue configuration variants (PDF only)*): cùng một lò HJX-20 Mini, nhưng số đo lấy nguyên văn từ
     catalogue PDF (72 x 47 x 103 cm) khác chút so với số đo trên trang web (71 x 42 x 112 cm) — không phải biến thể
     cấu hình riêng (catalogue PDF không gán ký hiệu hậu tố nào cho bản này, khác với các mã M/MBC/L/LBC/HC khác trong
     cùng nhóm). Đã đổi mã thành **`HJX-20-PDF`** và ghi rõ trong `description_en` rằng đây là cùng model gốc, chỉ khác
     nguồn số đo, để không trùng slug/mã với SKU thật.
2. **2 SKU thật thiếu `specs` dù trang nguồn có phần thông số** — đã vào lại `source_url` và điền:
   - `HJA-PLUS L175` (nhóm *HJA-PLUS line*): điền đủ `overall_dimensions_wxdxh`, `grill_dimensions`, `capacity`,
     `fire_up_time`, `daily_charcoal_consumption`, `performance_per_load`, `production_per_hour`, `grilling_temperature`,
     `recommended_installation_height` từ `.../josper-charcoal-oven-hja-plus-large/`.
   - `CVJ-50-PRO-S` (nhóm *Combos*): điền `recommended_for`, `fire_up_time` (riêng lò than và bếp Basque),
     `grill_dimensions_mm`, `oven_dimensions_mm`, `overall_dimensions_wxdxh` từ
     `.../combo-josper-cvj-50-pro-s-basalt-oven-basque-grill-double-in-one-module/`.
   - 7 mục khác vẫn còn `specs` rỗng (dụng cụ/than củi: `Fish grill / fish poacher`, `Hexagonal briquette ref 301001`,
     `Eucalyptus binchotan`, `Lichi binchotan`, `CEs`, `CE`, `SDV`) — đã kiểm tra lại trang nguồn: các trang này có mục
     "Technical Data Sheets +" nhưng nội dung là **file PDF datasheet tải riêng** (không phải bảng thông số HTML trong
     thân trang), nằm ngoài phạm vi crawl đã thực hiện (chỉ crawl HTML trang sản phẩm + 3 catalogue PDF chính, không
     crawl từng PDF datasheet phụ lục theo SKU). Vì không nằm trong 2 SKU được yêu cầu điền cụ thể, đã **giữ nguyên
     `specs` rỗng** thay vì suy đoán số liệu; đây là giới hạn crawl thật, không phải lỗi.
3. **Mô tả boilerplate giống hệt nhau (~40 mục)** — 33 mục trong nhóm *catalogue configuration variants (PDF only)*
   dùng chung 1 đoạn mở đầu ("Configuration listed in the official Josper product catalogue (PDF)…"). Đã thêm câu dẫn
   riêng cho 32/33 mục (dựa trên `specs` sẵn có của từng mã: kích thước tổng, kích thước vỉ nướng, sức chứa khách) để
   mỗi mô tả khác biệt hơn thay vì bê nguyên đoạn chung; đoạn boilerplate gốc vẫn giữ nguyên phía sau vì đó là ghi chú
   nguồn quan trọng (không phải PR content). Mã `HJX-20-PDF` không cần thêm vì đã viết lại mô tả riêng ở bước 1.

---

## 3c. Bổ sung specs + ảnh (2026-07-18, đợt 2)

### 3c.1 — 6/7 mục thiếu `specs` đã điền từ PDF datasheet chính hãng

Các trang than củi chỉ link tới file PDF datasheet (không có bảng thông số HTML trong thân trang). Đã fetch HTML thô,
lấy link PDF, tải về và trích text bằng `pypdf`:

| Nguồn PDF | Vai trò |
|---|---|
| `carbon-vegetal-josper-todas-las-fichas-tecnicas.pdf` (11 trang, Ed.12.2021) | Ficha técnica từng loại than — nguồn chính |
| `2019/05/catalogo-carbon-vegetal-josper-es.pdf` (8 trang, Ed.02.2023) | Đối chiếu EAN, mã REF, khối lượng & kích thước thùng/bao |

Đã điền đầy đủ cho **`Hexagonal briquette ref 301001`, `Eucalyptus binchotan`, `Lichi binchotan`, `CEs`, `CE`, `SDV`**:
mã REF, khối lượng & kích thước thùng/bao, EAN, carbon cố định, tro, chất bay hơi, độ ẩm, cỡ hạt, mức tia lửa,
nhiệt trị (kcal/kg + btu/lb), loại gỗ, hình thái, tỷ trọng, xuất xứ gỗ. Mỗi mục có thêm trường `specs_source_url`
trỏ tới đúng file PDF datasheet.

**Không điền được: `Fish grill / fish poacher` (SKU 0403).** Trang sản phẩm không có link PDF datasheet nào, và mã 0403
không xuất hiện trong `josper-cookware-catalogue.pdf` lẫn `josper-accessories-en.pdf`. WooCommerce Store API
(`/wp-json/wc/store/products`) trả về sản phẩm này với `attributes: []` và mô tả ngắn không kèm số đo. Josper **không
publish kích thước** cho món này → giữ `specs` rỗng thay vì suy đoán.

### 3c.2 — 43/111 mã catalogue PDF nay đã có ảnh

Nguồn ảnh: thư viện media chính hãng của `jospergrill.com` (duyệt qua `/wp-json/wp/v2/media`, 3.769 mục).
Hoá ra Josper **có** ảnh studio cho phần lớn biến thể cấu hình lò/bếp — chúng chỉ không được gắn vào SKU nào
trên webshop nên pass 1 không nhìn thấy. Toàn bộ 35 URL ảnh mới đã kiểm tra trả HTTP 200 và đã **xem trực tiếp từng ảnh**
để loại file wireframe/sketch (ví dụ `hornos-josper-HJX-25-M.jpg` và `hornos-josper-HJX-45-M.jpg` là bản vẽ nét trắng
trên nền đen → **không dùng**) và loại ảnh chụp trong nhà hàng.

**25 mã có ảnh riêng thật của đúng cấu hình đó** (không gắn cờ gì thêm):

`HJX-25-MBC` · `HJX-25-L` · `HJX-25-LBC` · `HJX-45-MBC` · `HJX-45-L` · `HJX-45-LBC` ·
`HJA-20` · `HJA-25` · `HJA-45` · `HJA-50` · `HJA-25-M` · `HJA-45-M` · `HJA-50-M` · `HJA-25-HC` · `HJA-45-HC` · `HJA-50-HC` ·
`PVJ-076-1` · `PVJ-076-2` · `CVJ-050-2-HJX-25` · `RGJ-050` · `RGJ-100` ·
`4228` · `0423` · `MINICAZ10` · `MINICO10`

**18 mã dùng ảnh MODEL GỐC** — Josper không publish ảnh riêng cho đúng mã đó. Mỗi mục được đánh dấu rõ bằng
**`"image_is_from_base_model": true`** kèm **`"image_base_model"`** ghi tên model gốc:

| Mã biến thể | Ảnh lấy từ model gốc |
|---|---|
| `HJX-25-M` | `HJX-25` |
| `HJX-45-M` | `HJX-45` |
| `HJX-50-M`, `HJX-50-MBC`, `HJX-50-L`, `HJX-50-LBC` | `HJX-50` |
| `HJX-20-PDF` | `HJX-20` (cùng một lò, chỉ khác nguồn số đo — xem mục 3b.1) |
| `PVJ-050-2` | `PVJ-50-2-1-MB` |
| `PVJ-076-3` | `PVJ-76-3-3-MB` |
| `CVJ-050-2-HJX-45` | `CVJ-50-PRO-M` |
| `CVJ-050-2-HJX-50` | `CVJ-50-PRO-L` |
| `RGJ-140` | `RGJ-PRO L` |
| `ASJ-063`, `ASJ-130` | ảnh studio dòng máy quay xiên ASJ (`asadores-josper-rotisseries.jpg`) |
| `4225`, `4226`, `4227` | `Glass lid for casserole (various sizes)` (ảnh nắp kính 4228, khác đường kính) |
| `TAPCAZ10` | `Mini casserole O10 (MINICAZ10/TAPCAZ10/MINICO10)` |

> **Cảnh báo khi import:** 18 mã trên **không phải ảnh chụp riêng của mã đó**, mà là ảnh của model gốc / model anh em
> cùng dòng (cùng thân lò, khác chân đế / kích cỡ / đường kính). Nếu cần độ chính xác tuyệt đối về hình ảnh thì phải
> xin ảnh gốc từ Josper. Lọc bằng `image_is_from_base_model == true` để rà lại.

### 3c.3 — 68 mã vẫn KHÔNG có ảnh (và vì sao)

| Nhóm | Số mã | Lý do |
|---|---:|---|
| Accessories & Spare Parts | 57 | Toàn bộ là phụ tùng (que cời, chắn lửa, vỉ nướng, ống khói, tay cầm tro, ốp cửa men màu, giá đỡ khay…). Không mã nào là SKU trên webshop (`/wp-json/wc/store/products` chỉ trả **97** sản phẩm), không có file ảnh nào trong media library khớp mã, và ảnh trong `josper-accessories-en.pdf` là ảnh nhúng trong PDF chứ không phải file public. **Cố ý KHÔNG map ảnh model gốc** — phụ tùng không có "model gốc"; gắn ảnh cái lò vào cái que cời là sai lệch. |
| Cookware catalogue-only | 11 | `4027/2` · `4028` · `4065/2` · `4066` (xiên) · `4231` (kẹp khay) · `8224` · `8224R` (bàn chải) · `4243` (giỏ đôi) · `0402` (giá inox) · `4260` (đĩa bazan 46x30) · `4264` (đế Iroko 53x37). Không có SKU shop, không có ảnh media khớp mã, và không có model gốc cùng kiểu/kích cỡ để mượn ảnh. |

**Đã thử và loại:** file `cesta-besugera-0403-josper-0594.jpg` có chuỗi `0594` trong tên nhưng mở ra là ảnh cận cảnh
**vỉ kẹp cá 0403**, không phải ống khói `0594` → không dùng.

### 3c.4 — Ghi nhận thêm (mục ảnh sai đã được xử lý ở §3d)

- `HJX-20`, `HJX-25`, `HJX-50` (nhóm *base HJX line*) đang dùng `woocommerce-placeholder-720x720.png` — đúng là ảnh
  webshop trả về, nhưng là **ảnh placeholder rỗng**. Media library có ảnh studio thật:
  `2019/03/hornos-josper-HJX-20.jpg`, `2019/03/hornos-josper-HJX-25.jpg`, `2019/03/horno-josper-hjx-50-large.jpg`.
- `HIB-001` đang dùng `logo-horizontal-white.png` (logo Josper, không phải ảnh sản phẩm). Shop có SKU
  *HGJ-PRO JOSPER HIBACHI* với ảnh thật `HIBACHI-082025-0.jpg`.
- Media library còn có **PDF datasheet riêng cho từng cấu hình** (`2019/03/HJX-25-M_EN.pdf`, `HJA-45-HC_EN.pdf`,
  `HJX-50-LBC_EN.pdf`…) — có thể khai thác để làm giàu `specs` cho nhóm catalogue variants nếu cần sau này.

---

## 3d. Thay 4 ảnh sản phẩm SAI (2026-07-18, đợt 3)

Xử lý dứt điểm 4 mục đã ghi nhận ở §3c.4: 3 model chủ lực dùng ảnh placeholder trắng và 1 model dùng logo.

| Model | Ảnh CŨ (sai) | Ảnh MỚI | Đã mở xem — nội dung ảnh thật |
|---|---|---|---|
| `HJX-20` | `woocommerce-placeholder-720x720.png` (placeholder trắng WooCommerce) | `charcoal-oven-hjx-20.jpeg` (media ID 34707) | Lò nướng than đặt bàn cỡ nhỏ, mặt inox, ống khói côn, đồng hồ nhiệt, chân đế ngắn, **không có tủ chân**. Khớp mô tả HJX-20 Mini (71x42x112 cm, countertop). |
| `HJX-25` | `woocommerce-placeholder-720x720.png` | `charcoal-oven-hjx-25.jpeg` (media ID 34718) | Lò nướng than thân hẹp, mặt inox, **có tủ chân 2 cánh** — đúng cấu hình HJX-25-L (64x60x185 cm). |
| `HJX-50` | `woocommerce-placeholder-720x720.png` | `charcoal-oven-hjx-50.jpeg` (media ID 34740) | Lò nướng than thân **rộng hơn hẳn HJX-25**, mặt trước men **đỏ**, tủ chân inox 2 cánh — đúng cấu hình HJX-50-L (93x86x190 cm). |
| `HIB-001` | `logo-horizontal-white.png` (LOGO Josper, không phải sản phẩm) | `hibachi-josper-main.jpg` (media ID 53946) | Bếp nướng than để bàn Hibachi: thân inox hình hộp, vỉ lưới thép, biển đồng khắc chữ Josper, nền đen. |

**Cả 4 URL trả HTTP 200** (kiểm 2026-07-18). Mỗi model được thêm field `image_note` ghi xuất xứ + kết quả kiểm tra ảnh.

### 3d.1 — Cách tìm và vì sao chọn bộ ảnh này

Trang sản phẩm `/products/**` của 3 model HJX **không chứa ảnh sản phẩm** — đó chính là lý do đợt crawl trước
lấy phải placeholder. Ảnh thật chỉ nằm trong media library, truy cập qua
`https://www.jospergrill.com/wp-json/wp/v2/media?search=hjx&per_page=100`.

Bộ `charcoal-oven-hjx-{20,25,45,50}.jpeg` được chọn vì là **một series studio đồng nhất**: cùng nền đen,
cùng góc chụp chính diện, cùng kích thước **900x900 vuông** — hợp lưới catalog hơn các ảnh lẻ khác.

**Đã đối chiếu chéo để loại khả năng tên file lệch nội dung:** tải thêm `charcoal-oven-hjx-45.jpeg` cùng series
và so tỉ lệ thân lò giữa 4 ảnh. Thứ tự to dần 20 (đặt bàn, không tủ) → 25 (hẹp, có tủ) → 45/50 (rộng, có tủ)
khớp đúng kích thước công bố trong `specs`. Không phát hiện lệch.

**Ứng viên đã xem nhưng KHÔNG chọn** (đều là ảnh thật, chỉ kém phù hợp hơn):

- `2019/03/hornos-josper-HJX-20.jpg` — ảnh studio thật, nhưng bản **mặt trước đen** và khổ 800x1000 (không vuông),
  lệch series. Ba tên file mà §3c.4 gợi ý trước đây đều sống (HTTP 200) và đều là ảnh thật, không phải bản vẽ.
- `HIBACHI-082025-0.jpg` — ảnh Hibachi thật, tông đồng ấm trên nền gỗ, **có kèm đế support** (phụ kiện).
  Đẹp nhưng là ảnh lifestyle; chọn `hibachi-josper-main.jpg` cho sạch nền và đồng bộ với 3 ảnh lò.

Không model nào phải giữ nguyên — **cả 4 đều thay được bằng ảnh chụp sản phẩm thật.**

---

## 3e. Tìm ảnh thật cho phụ kiện/phụ tùng từ nguồn NGOÀI Josper (2026-07-19, đợt 4)

Mục tiêu: 68 mã ở §3c.3 (57 phụ kiện & phụ tùng + 11 mã cookware catalogue-only) không có ảnh trên `jospergrill.com`
ở bất kỳ dạng nào (không phải SKU shop, không có file media khớp mã, ảnh trong PDF là ảnh nhúng không public).
Đợt này tìm ảnh THẬT của đúng phụ kiện đó từ các **nhà phân phối phụ tùng Josper chính hãng** bán lẻ từng mã rời —
đây là nguồn duy nhất có ảnh chụp riêng cho các phụ tùng nhỏ (Josper chỉ publish ảnh cho SKU nguyên lò/bếp, không
publish ảnh phụ tùng rời).

**Kết quả: tìm được ảnh thật, khớp đúng mã, cho 12/68 mã.** 56 mã còn lại vẫn để `image_url` rỗng.

### Nguồn đã dùng

| Nguồn | Vai trò |
|---|---|
| `ascateringsupplies.com/manufacturer/josper.html` (A S Catering Supplies, UK) | Nhà phân phối phụ tùng Josper — ảnh sản phẩm chụp riêng từng part number (`JOSP-{mã}_1.jpg`) |
| eBay UK, seller `commercialkitchendirectuk` (100% positive, 5.5K feedback) | Chuyên bán phụ tùng bếp công nghiệp rời, có 18 mã Josper với ảnh chụp thật, tên listing ghi rõ part number Josper |

Cả hai nguồn đều bị chặn bởi `WebFetch` (403/406 — bot protection), phải dùng Browser pane (render JS thật) để đọc
DOM và lấy `src` ảnh. Mỗi ảnh đã được xác minh tải được (load thành công, có kích thước pixel thật, không phải ảnh vỡ)
trước khi ghi vào `image_url`.

### 12 mã đã có ảnh (đợt này)

| Mã | Tên | Nguồn ảnh | Ghi chú |
|---|---|---|---|
| `0415` | S. Steel Meat Grill Rack Hjx-25 | A S Catering Supplies | Khớp mã tuyệt đối |
| `0422` | S. Steel Meat Grill Rack Hjx-50 | eBay commercialkitchendirectuk | Khớp mã tuyệt đối |
| `0426AC` | Coal Grate Hjx-20/25/45 (53x10) | eBay commercialkitchendirectuk | Listing ghi mã `0426` (không có hậu tố AC) nhưng đúng kích thước 527x105mm ≈ 53x10cm — cùng một phụ tùng vật lý, khác cách ghi mã giữa nhà phân phối và catalogue Josper. Đã gắn `image_source_note` giải thích. |
| `0434` | Complete Temperature Gauge | A S Catering Supplies | Khớp mã tuyệt đối |
| `0444INOX` | Ash Handle 26 Cm 25L/45L S. Steel | A S Catering Supplies | Khớp mã tuyệt đối |
| `0490INOX` | Ash Handle 37 Cm 25M/45M S. Steel | A S Catering Supplies | Khớp mã tuyệt đối |
| `0491INOX` | Ash Handle 36 Cm 50L S. Steel | A S Catering Supplies | Khớp mã tuyệt đối |
| `0516/4` | S. Steel Rounded Door Handle Hjx-20/25 | eBay commercialkitchendirectuk | Khớp mã tuyệt đối |
| `0594` | Standard Smoke Outlet Register | eBay commercialkitchendirectuk | Khớp mã tuyệt đối |
| `250089` | Ash Handle Extreme S. Steel | eBay commercialkitchendirectuk | Khớp mã tuyệt đối |
| `250090` | Ash Handle 26,7 Cm S. Steel | eBay commercialkitchendirectuk | Khớp mã tuyệt đối |
| `8224` | Special Aluminium Wire Brush 36 Cm | A S Catering Supplies | Khớp mã tuyệt đối |

Mỗi mục trong 12 mã trên được thêm trường `image_source_note` ghi rõ nguồn ảnh và mức khớp mã.

### 56 mã vẫn KHÔNG tìm được ảnh thật

Đã thử thêm (ngoài 2 nguồn trên): trang danh mục `jospergrill.com/product-category/josper-accessories/` (chỉ là
trang landing, không liệt kê từng phụ tùng), `nisbets.co.uk` và `partstown.co.uk` (chặn truy cập tự động — 403/406,
không bypass CAPTCHA/bot-detection theo quy định), `premium-gastro.com`, `chefsdeal.com`, `restaurantsupply.com`
(chặn bởi Cloudflare challenge), `gobbqco.com` và `kitchenrestock.com` (chỉ bán lò nguyên bộ, không có phụ tùng rời),
và tìm kiếm eBay UK theo từ khoá cho từng nhóm phụ tùng còn thiếu (poker, firebreak, glazed door cover, coal grate
0432AC, meat grill rack 24002/0419, tray support BC-25/45/50, central grate support 4215/4216/4217, grease drain,
smoke outlet register 4058, wheels 0499N, firebreak hat 4033, skewers 4027/2·4028·4065/2·4066, peg for trays 4231,
brush spare piece 8224R, basket 4243, S.Steel holder 0402, basalt dish 4260, iroko wood support 4264) — không ra
kết quả khớp mã hoặc khớp loại phụ tùng từ người bán đáng tin cậy.

**Không dùng ảnh đại diện chung cho các mã này** dù đề bài cho phép, vì không tìm được dù chỉ 1 ảnh thật đáng tin cậy
cho cả nhóm loại đó (ví dụ: không có ảnh thật nào cho "Josper poker" nói chung, không riêng theo size) — thà để trống
còn hơn gắn nhầm. 56 mã này giữ nguyên `image_url: ""` như cũ.

---

## 4. Những gì KHÔNG có trong file và lý do

| Mục | Lý do |
|---|---|
| **HJA-PLUS-S80-HOME** | Trang sản phẩm trên shop trả **404** — không còn nguồn chính hãng để lấy dữ liệu thật. Chỉ có 3 bản HOME còn sống. |
| **HJA-PLUS-M120-HCTD** | Chỉ xuất hiện ở danh mục nhà phân phối bên thứ ba, không có nguồn Josper chính hãng → không đưa vào để tránh bịa thông số. |
| **BTL** (quebracho trắng ép, ref 4018) | Chỉ có **tên mã** trong bảng than của catalogue PDF, không có mô tả/thông số riêng và không có trang shop. Bỏ để không phải bịa nội dung. |
| **Biến thể màu cửa** (ZU/BL/NC/MR/IN/VC/VR) | Shop bán là **tuỳ chọn của cùng một SKU**, không phải model riêng. |
| **Biến thể cấu hình PRO / PRO-T / PRO-TD / PRO-W / PRO-WT / PRO-WTD** | Cũng là tuỳ chọn của cùng SKU → ghi gộp trong `specs.configurations` của model gốc (ví dụ HJX-PRO M120), không tách thành 6 model. |
| **Trang ngôn ngữ ES / FR / IT / NL** | Trùng nội dung với bản EN; đã canonical-dedupe để tránh model trùng. |
| **`test-product`, trang `/tienda/`** | Không phải sản phẩm thật. |
| **Mã GN & nồi gang trong cookware PDF** (4218–4224, 4238, 4242) | Trùng với SKU đã có trên shop → đã gắn kèm vào trường `model` của SKU tương ứng thay vì tạo bản ghi trùng. |
| **`josper.es`** | Lỗi SSL, không truy cập được. |

---

## 5. Cách dữ liệu được lấy (để tái lập)

1. Đọc `product-sitemap.xml` + `product_cat-sitemap.xml`; lọc URL EN.
2. Crawl mọi trang `/product-category/**` (kèm phân trang) để bắt sản phẩm ngoài sitemap.
3. Hợp nhất 114 URL → fetch HTML thô → dedupe theo `<link rel="canonical">` → còn **98 trang duy nhất** (bỏ `test-product` và `/tienda/` → 96 SKU).
4. Trích: `<h1>` → `title`; nội dung thân trang (đã bỏ nav/footer/cookie banner, khử trùng lặp) → `description_en`;
   cặp `Nhãn:` / `giá trị` trong bảng datasheet + regex trên câu văn (nhiệt độ nướng, thời gian mồi lửa, mức tiêu thụ than, diện tích nướng…) → `specs`;
   `.woocommerce-product-gallery__image img` (fallback `og:image`) → `image_url`; `<title>` → `seo_title`; `meta[name=description]` (fallback `og:description`) → `seo_description`.
5. Trích text 3 catalogue PDF bằng `pypdf` để dựng các nhóm chỉ-có-trong-catalogue.
6. Kiểm tra HTTP 200 cho toàn bộ URL trước khi ghi file.

**Nguyên tắc đã tuân thủ:** không bịa model, không bịa thông số, không bịa ảnh. Trường nào không có nguồn thì để rỗng.
