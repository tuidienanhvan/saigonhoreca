# Hoshizaki — Catalog đầy đủ (`data-full.json`)

**Ngày khảo sát:** 18/07/2026 (cập nhật mở rộng: 19/07/2026)
**Brand string:** `Hoshizaki`
**File dữ liệu:** `data-full.json` (8 nhóm · **103 model** có mô tả/specs chi tiết · 438 URL sản phẩm thật xác nhận qua sitemap)

> File này **độc lập** với `data.json`. `data.json` (24 model, đã import draft trước đó) **không bị đụng tới**, cũng không đụng `import.csv`.

> **Cập nhật 19/07/2026:** Bổ sung thêm **30 model Refrigeration** (12→42) và **18 model Ice Maker** (19→37) cào chi tiết từ `full_catalog_urls` đã lưu sẵn, để phủ đủ hơn 2 nhóm cốt lõi của Hoshizaki đang bị đại diện quá ít. Đồng thời đã audit riêng dòng **Blast Chiller/Freezer** trên hoshizaki-sea.com — xem mục 4.7.

---

## 1. Nguồn dữ liệu

| # | Nguồn | Dùng cho |
|---|-------|----------|
| 1 | **https://www.hoshizaki-sea.com** — Hoshizaki South East Asia | Nguồn DUY NHẤT dùng cho file này. Site khu vực Đông Nam Á, gần thị trường Việt Nam nhất (có filter quốc gia Vietnam trên menu). |
| 2 | `product-sitemap1.xml` / `2.xml` / `3.xml` (sitemap chính chủ, Rank Math SEO plugin) | Tải trực tiếp bằng `curl` (không qua model tóm tắt) để lấy **danh sách đầy đủ, thật** của mọi URL sản phẩm đang publish — 466 URL tổng cộng, lastmod 06-07/2026. |

**Vì sao dùng sitemap thay vì crawl menu:** Lần khảo sát trước (13/07/2026) ghi nhận menu category trên site chỉ hiển thị 12 sản phẩm/trang qua giao diện AJAX — đây là hạn chế thật của site, không phải lỗi công cụ. Lần này đã xác nhận lại: `/page/2/`, `/page/5/`, các query `?filter_xxx=` đều trả về **y hệt trang 1** khi fetch tĩnh (site dùng JS client-side để phân trang/lọc thật, không lộ ra qua HTML tĩnh); Browser pane (trình duyệt tương tác) cũng bị site chặn điều hướng trong phiên này. Sitemap XML là nguồn tĩnh, chính chủ, và liệt kê đủ 100% URL sản phẩm đang publish — khắc phục triệt để hạn chế đó.

---

## 2. Thống kê

| Chỉ số | Số lượng |
|---|---|
| Nhóm (`groups`) | **8** |
| Tổng URL sản phẩm thật xác nhận qua sitemap (2 nhóm ưu tiên) | **108 (Ice Maker) + 330 (Refrigeration) = 438** |
| Model có mô tả + thông số kỹ thuật chi tiết đã fetch | **103** |
| Model có `image_url` | **103 / 103** (cập nhật 19/07/2026: bổ sung 12 model còn thiếu bằng fetch trực tiếp từng `source_url`) |
| Model có `source_url` hoạt động | **103 / 103** |
| Model có giá bán | **0** (Hoshizaki không công bố giá trên web) |

### Phân bổ theo nhóm

| # | Nhóm (EN) | Nhóm (VI) | URL thật (sitemap) | Model đã fetch chi tiết |
|---|---|---|---|---|
| 1 | Ice Maker | Máy Làm Đá | **108** | **37** (19 gốc + 18 mới 19/07) |
| 2 | Refrigeration | Tủ Lạnh / Tủ Đông Công Nghiệp | **330** | **42** (12 gốc + 30 mới 19/07) |
| 3 | Dishwasher | Máy Rửa Chén Bát Công Nghiệp | 6 | 6 |
| 4 | Sushi Showcase | Tủ Trưng Bày Sushi | 8 | 8 |
| 5 | Beer & Soda Dispenser | Vòi Bia Tươi / Nước Có Gas | 5 | 5 |
| 6 | Deep Freezer | Tủ Đông Sâu | 3 | 3 |
| 7 | Thawing Cabinet | Tủ Rã Đông | 1 | 1 |
| 8 | Cooking Equipment | Thiết Bị Nấu | 1 | 1 |

Trước lần khảo sát 18/07, `data.json` ghi nhận Ice Maker và Refrigeration là **"0 model"** — đây là hai dòng sản phẩm cốt lõi của Hoshizaki nhưng chưa được đưa vào catalog. Lần khảo sát 18/07 xác nhận đủ **108 + 330 = 438 SKU thật** đang publish trên site nguồn cho 2 nhóm này và fetch chi tiết 31 model đại diện. **Lần mở rộng 19/07/2026** cào thêm 48 model nữa (18 Ice Maker + 30 Refrigeration) từ danh sách `full_catalog_urls` đã lưu sẵn, nâng tổng số model có mô tả/specs chi tiết của 2 nhóm này lên **37 + 42 = 79 model** (từ 108+330=438 URL đã biết, còn lại 359 URL chưa cào chi tiết, vẫn lưu nguyên trong `full_catalog_urls` để mở rộng sau).

---

## 3. Vì sao chỉ 79/438 model Ice Maker + Refrigeration có mô tả/specs chi tiết, và cách bù đắp

466 URL sản phẩm (438 riêng cho Ice Maker + Refrigeration) là con số quá lớn để fetch chi tiết từng trang trong một phiên làm việc. Chiến lược áp dụng:

- **6 nhóm nhỏ đã biết** (Dishwasher, Sushi Showcase, Beer & Soda Dispenser, Deep Freezer, Thawing Cabinet, Cooking Equipment — tổng 24 model theo `data.json` cũ): fetch **100%**, không bỏ sót model nào.
- **2 nhóm lớn (Ice Maker, Refrigeration)**: chọn tập mẫu đại diện **phủ đủ mọi phân nhóm, dòng (series), và kiểu cấu hình** thay vì cố lấy hết, qua 2 đợt cào (18/07 và 19/07/2026):
  - **Ice Maker (37 model, từ 19 → 37)**: Cube Self-Contained (nhiều cỡ: IM-45CA/IM-65NE/IM-65NE-Q/IM-100A/IM-130A/IM-240NE/IM-35M-2-LM/IM-55M-2-LM Big Cube mini), Cube Modular (IM-220AA/IM-240DNE/IM-480DNS + crushed-ice IM-240DNS-STCR), Crescent Self-Contained (thường KM-60C/KM-130C + UV-LED KM-130C-U + tự làm sạch KM-40C-W/KM-80C-W), Crescent Modular (KM-470AJ/KM-1180DJ, KMD-270AB), Dice Self-Contained (SRM-45A/SRM-105A) + Modular&Bin (SRM-200AB thường/half-dice, SRM-650AB), Flaker/Nugget Self-Contained (FM-120KE/FM-120KS-50-HCN) + Modular (FM-1000AKS), Dispenser (DCM-80LS-HC/DCM-120KE/DCM-120LS-HC), Cubelet (CM-110KE-50), Ice Storage Bin rời (B-301SA/B-501SA).
  - **Refrigeration (42 model, từ 12 → 42)**: Reach-in/Upright (Premier HR-126MC-S/HR-188MC-SLG, A-FIT HR-78MA-S-JB, A1-FIT HRWH127LS4/HRWH147LS4/HFWH127LS4/HFWH147LS4, PRO HRV-78A/HRV-128A/HFV-128A, kính dài/kính nhiệt/pillar-less), Undercounter (RT-/FT- nhiều cấu hình: thường, swing glass, sliding glass, bakery), Prep Table (RT-xxx-PR, RTWH-GNT), Drawer/Gold Line (RTC-/FTC- nhiều cỡ ngăn kéo GN), Low-Drawer (RTL-/FTL-), Dual-Temp (HRF-/RFT-), Bakery/Blast Chiller Cabinet (BAKER GA/BAKER SF — xem mục 4.7).
- **Toàn bộ URL còn lại** (71 Ice Maker + 288 Refrigeration = 359 URL) tiếp tục được lưu nguyên vẹn trong trường `full_catalog_urls` của từng nhóm trong `data-full.json` — không mất dấu vết, sẵn sàng để crawl tiếp nếu cần mở rộng catalog đầy đủ.

---

## 4. Phát hiện đáng chú ý

1. **Site menu category chỉ lộ 12 SKU/trang qua HTML tĩnh** — đã xác minh lại bằng nhiều cách (query filter, `/page/N/`, Browser pane) và xác nhận đây là giới hạn thật của kiến trúc site (JS client-side rendering), không phải giới hạn công cụ. Sitemap XML là cách duy nhất lấy được danh sách đầy đủ.
2. **Hoshizaki KHÔNG công bố xuất xứ sản xuất (Japan/Thailand) trên bất kỳ trang sản phẩm nào** đã fetch. Một số trang (đặc biệt Sushi Showcase) liệt kê "thị trường phân phối" (Indonesia/Malaysia/Singapore/Thailand/Vietnam) — dễ nhầm với xuất xứ sản xuất nhưng **không phải cùng một thứ**. Theo đúng yêu cầu "ghi đúng theo trang, không đoán", trường `origin` để trống cho **toàn bộ 55 model** trong file này.
3. **2 model dòng dishwasher — OBY-50T và OBM-1080T — ghi brand "Ozti" ngay trên trang sản phẩm**, khác các model HW-320B/HW-600B cùng nhóm. Đây nhiều khả năng là OEM/liên doanh phân phối qua kênh Hoshizaki SEA, không phải sản xuất bởi Hoshizaki Corporation Nhật Bản — đã ghi chú riêng trong `description_en` của 2 model này, cần Hoshizaki Vietnam xác nhận trước khi công bố.
4. **Model "HR-78MA-S-JD" (tủ rã đông, đã có trong `data.json` cũ) có URL slug thực tế là `/product/hr-78ma-s-jb/`** — chênh lệch B/D so với `<title>` hiển thị trên trang ("HR-78MA-S-JD"). Khả năng là lỗi đánh máy trên chính site nguồn. Đã dùng tên model theo `<title>`, `source_url` theo slug thật.
5. **Dòng Refrigeration có 5 series** rõ ràng qua tên URL: **Premier** (cao cấp, R290, kính dài/bakery), **A-FIT** (phổ thông, đủ mọi cấu hình upright/undercounter/dual-temp/low-drawer), **A1-FIT** (mã "WH", bản nâng cấp A-FIT với evaporator lớn hơn), **Gold Line** (chuyên ngăn kéo GN), **PRO** (dòng giá tốt, ký hiệu đuôi "V"). Cộng thêm **BAKER** — dòng tủ chuyên dụng bánh/bột đa chức năng (đông/mát/ủ bột) tách biệt hoàn toàn khỏi 5 series trên, có vẻ là một sub-brand/nhà máy riêng trong tập đoàn Hoshizaki.
6. **Ice Maker có phân nhóm Crescent (KM-/KMD-) bị bỏ sót ở lần lọc regex đầu tiên** — 22 URL (bao gồm 3 biến thể mỗi cỡ: thường / có UV-LED khử trùng / có tự làm sạch bằng hoá chất máy rửa chén) đã được rà soát lại và bổ sung đủ vào `full_catalog_urls`, nâng tổng Ice Maker từ 86 lên 108 URL.
7. **Blast Chiller / Freezer trên hoshizaki-sea.com — đã audit, kết luận: KHÔNG có dòng sản phẩm riêng, khác với hoshizaki-europe.com.** Trang category `/product-category/refrigeration/` có bộ lọc liệt kê nhãn **"Blast Chiller / Freezer (0)"** — tức taxonomy tồn tại trên site nhưng **hiện có 0 sản phẩm** được gán vào category này. URL trực tiếp `hoshizaki-sea.com/product-category/blast-chiller/` trả về **404 Not Found**. Rà toàn bộ 330 URL Refrigeration đã biết: không URL nào chứa "blast" hoặc "shock" trong slug. Tuy nhiên, một model thuộc dòng Bakery Cabinet — **BAKER SF 550 L DR** (mới cào 19/07) — thực chất là sản phẩm dạng Quick Chiller/Freezer (dải nhiệt -30°C đến +10°C, có chế độ "hard/soft chill"), được xếp chung vào nhóm Bakery/Refrigeration tổng quát thay vì có category riêng. **Kết luận cho theme**: hoshizaki-sea.com có năng lực làm lạnh nhanh (qua dòng BAKER) nhưng **không merchandise Blast Chiller như một dòng sản phẩm độc lập** như bên châu Âu — nên KHÔNG tạo nhóm "Blast Chiller" riêng trong catalog theme, giữ BAKER SF trong nhóm Refrigeration/Bakery Cabinet như hiện tại.

---

## 5. Những gì KHÔNG có — và lý do

| Không có | Lý do |
|---|---|
| **Thông số kỹ thuật chi tiết cho 359/438 URL Ice Maker + Refrigeration còn lại** | Quy mô quá lớn (466 URL tổng) để fetch từng trang trong 1 phiên. Đã ưu tiên phủ đủ MỌI phân nhóm/dòng bằng 79 model đại diện (qua 2 đợt cào 18/07 + 19/07), và giữ nguyên toàn bộ URL còn lại trong `full_catalog_urls` để tiếp tục sau. |
| **Xuất xứ sản xuất (origin)** | Không trang sản phẩm nào trên hoshizaki-sea.com công bố rõ ràng — để trống cho toàn bộ 103 model, không đoán. |
| **Giá bán** | Hoshizaki không công bố giá trên bất kỳ site nào (chỉ có nút "Get Quote"). |
| ~~Ảnh cho 12/103 model~~ | **Đã bổ sung 19/07/2026** — fetch trực tiếp từng `source_url` (WebFetch) và lấy ảnh sản phẩm chính hiển thị trên trang (gallery/hero image, không phải ảnh sản phẩm liên quan). Ghi chú: `SRM-650AB-B-801SA` và `SRM-650AB-14-B-801SA` dùng chung 1 ảnh gốc (cùng đầu máy modular, khác cấu hình bin) — đúng theo ảnh site hiển thị. `FM-1000AKS-SB-B-501SA` và `FM-1000AKS-N-SB-B-501SA` cũng dùng chung 1 file ảnh trên site nguồn (`7374_FM-1000AKE-N-B-501SA...`, lưu ý site nguồn gõ "AKE" thay vì "AKS" trong tên file — giữ nguyên theo site, không sửa). `SRM-105A` dùng ảnh gallery có tên file `01_SRM-45A...` trên chính trang sản phẩm SRM-105A (không phải từ khối sản phẩm liên quan) — có thể là ảnh dùng chung giữa 2 dòng máy cùng kiểu dáng, giữ nguyên theo site vì đây là ảnh gallery chính thức của trang, không suy đoán. `DCM-120KE`: `source_url` gốc trong data-full.json (`/product/dcm-120ke/`) trả về 404 khi fetch trực tiếp lần cào 19/07 — đã thử lại và trang tải thành công, ảnh lấy được từ đúng URL này. |
| **Dòng "Blast Chiller" riêng biệt** | Đã audit (mục 4.7): category tồn tại trên site nhưng có 0 sản phẩm gán vào, URL category trả 404. Không tạo nhóm riêng trong catalog theme. |
| **Phân trang/filter qua giao diện web** | Site dùng AJAX/JS client-side cho filter quốc gia, loại đá, cấu hình — không lộ qua fetch tĩnh lẫn Browser pane (bị site chặn điều hướng trong phiên này). Sitemap XML là giải pháp thay thế đã dùng. |

---

## 6. Ghi chú kỹ thuật khi import

- Toàn bộ 103 `source_url` đã fetch thành công (HTTP 200, nội dung hợp lệ).
- `heat_source` = `electric` cho toàn bộ 103 model, kể cả máy rửa chén (gia nhiệt nước bằng điện) và fryer FL-20B-SEA (chiên bằng điện trở, không phải gas).
- `origin` để trống (`""`) cho toàn bộ model — xem mục 4.2 và mục 5.
- Trường `specs` dùng tiếng Việt cho nhóm Refrigeration/Dishwasher/Sushi Showcase/Beer&Soda/Deep Freezer/Thawing/Cooking (đã dịch số liệu, giữ nguyên đơn vị gốc), và giữ tiếng Anh cho một số trường Ice Maker mô tả cấu hình kỹ thuật đặc thù (Ice Type, Configuration) — nhất quán với format `data.json`/`import.csv` cũ.
- `seo_title`/`seo_description`: `seo_title` lấy nguyên văn thẻ `<title>` gốc (chưa dịch) theo đúng yêu cầu; `seo_description` để trống cho toàn bộ 103 model vì **không trang nào trong số đã fetch có thẻ `<meta name="description">`** — đã kiểm tra qua nội dung trích xuất, không phải bỏ sót.
- File JSON hợp lệ UTF-8, đã kiểm tra bằng `python -c "json.load(...)"` — 8 nhóm, 103 model chi tiết, không lỗi cú pháp.
- **Không đụng `data.json`**, không đụng `import.csv`, không import WordPress, không chạy WP-CLI.
- Lần mở rộng 19/07/2026 dùng 2 subagent chạy song song, mỗi agent chỉ sửa đúng nhóm được giao ("Ice Maker" hoặc "Refrigeration") trong `models` array để tránh xung đột ghi file; đã validate lại JSON sau khi cả hai hoàn tất.
