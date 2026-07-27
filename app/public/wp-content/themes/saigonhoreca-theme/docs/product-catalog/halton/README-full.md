# Halton-Finland — Catalog đầy đủ (data-full.json)

**Ngày crawl (đợt 1):** 18/07/2026
**Ngày bổ sung (đợt 2 — hoàn thiện 100%):** 18/07/2026
**Thương hiệu:** Halton-Finland (Halton Group, Phần Lan)
**File dữ liệu:** `data-full.json`
**Tổng cộng:** 38 nhóm sản phẩm — **307 model** — 307/307 model có ảnh thật — **307/307 model có bảng thông số (100%, đã vá xong 10 model rỗng)**

> Lưu ý: file `data.json` (bản cũ, 
> đã import thành draft) KHÔNG bị đụng tới. `data-full.json` là bản crawl sâu, đầy đủ toàn dải sản phẩm.

> **Đợt 2 (18/07/2026):** bổ sung 5 model còn thiếu (sau khi rà soát lại toàn bộ ~34 trang nghi ngờ "chỉ có bản không phải tiếng Anh" — xem mục 5) và vá nốt 10 model bị rỗng `specs` ở đợt 1 (xem mục 6). Xem chi tiết kết quả ở mục 5 và 6 bên dưới trước khi coi đây là số liệu cuối.

---

## 1. Nguồn dữ liệu

Toàn bộ dữ liệu lấy thật từ website chính thức **halton.com**, không có số liệu bịa.

| Bước | Nguồn | Ghi chú |
|---|---|---|
| Lập danh sách sản phẩm | `https://www.halton.com/sitemap_index.xml` → `product-sitemap.xml` + `product-sitemap2.xml` | 1.187 URL sản phẩm mọi ngôn ngữ |
| Lọc bản tiếng Anh | Các URL dạng `https://www.halton.com/products/.../` | Đúng **302** trang — khớp chính xác số model của bản khảo sát trước |
| Lấy nội dung | Tải HTML gốc từng trang (curl/urllib, User-Agent trình duyệt) | 302/302 trang tải thành công, không lỗi |
| Đối chiếu nhóm | Danh sách 38 nhóm của bản khảo sát + `https://www.halton.com/productgroup/...` | Ghép 1-1 hoàn hảo: 302 model ↔ 302 URL, không thừa không thiếu |

**Các trường bóc tách từ mỗi trang:**

- `title` — thẻ `<h1>` của trang sản phẩm
- `description_en` — đoạn mô tả thật ngay dưới H1 + mục *Overview* / *How it works*
- `specs` — các mục thông số theo đúng heading gốc: *How it works, Performance Highlights, Modifications and Options, Dimensions, Material, Function, Installation, Servicing, Specification, Order code, Product models*...
- `image_url` — ảnh sản phẩm thật (thẻ `<img>` đầu tiên trong vùng nội dung, dự phòng `og:image`); **100%** trỏ về `images.halton.com`
- `source_url` — URL trang sản phẩm chính xác (đã kiểm tra mẫu ngẫu nhiên: tất cả trả về HTTP 200)
- `seo_title` — thẻ `<title>` server trả về
- `seo_description` — thẻ `<meta name="description">` server trả về

---

## 2. Bảng nhóm sản phẩm (đã cập nhật sau đợt 2)

| Nhóm (EN) | Nhóm (VI) — dùng làm product_category | Số model | Có specs | Danh sách model |
|---|---|---|---|---|
| Kitchen Exhaust Hoods (Foodservice) - CE/European variants | Chụp Hút Khói Bếp Công Nghiệp (Chuẩn CE/Châu Âu) | 26 | 26 | KVI, KVF, KWI, KWF, UVI, UVF, UWI, UWF, KSR-S, KSR-F, KSR-M, USR-S, USR-F, USR-M, CMW-IMOD, CMW-FMOD, KCH-I, KCH-F, UCH-I, UCH-F, KBO, KVD, KVV, CJB, CJP, CCW |
| Kitchen Exhaust Hoods (Foodservice) - North America ETL/UL variants | Chụp Hút Khói Bếp Công Nghiệp (Chuẩn ETL/UL Bắc Mỹ) | 23 | 23 | KEX, KSK, MDH, P2, EO, CH, KCH, KVE, KVE-CM, KVE-UV, KVE-WW, KVE-WW-UV, KVC, KVC-UV, KVC-WW, KVC-WW-UV, KVL, KVL-UV, KVM-A, KVO, KVR, KVW, KVW-UV |
| Ventilated Ceilings | Trần Thông Gió Bếp Công Nghiệp | 10 | 10 | KCJ, KCJ-UV, KCW, KCW-UV, KCV, KCV-WW, KCP, CCL, HSC, VCS |
| Kitchen Diffusers (make-up air) | Miệng Gió Cấp Khí Bù Cho Bếp | 5 | 5 | KCD (CE), KCD, LFU, LVU, TCG |
| Galley Hoods (marine/cruise) | Chụp Hút Bếp Tàu Biển (Marine/Du Thuyền) | 7 | 7 | KFM, KWH, KWT, KW3, KVM, WR, CCW-M |
| Display Cooking / Mobile Cooking Ventilation | Thiết Bị Thông Gió Bếp Mở Và Bếp Di Động | 8 | 8 | KMC, KMC-A, KMC-NA, MMC, JES (CE), JES (ETL), JES-RD, CBR |
| Ductless Hoods / Recirculation Systems | Chụp Hút Không Ống Gió Và Hệ Thống Tuần Hoàn Khí | 4 | 4 | RAH (CE), RAH (UL Recognized), KVS, KVH |
| Pollution Control Units (PST / UV / ESP / odour) | Thiết Bị Xử Lý Khí Thải Và Khử Mùi Bếp | 4 | 4 | PST, PST-A, PEU, RCT |
| Make-up Air Units / kitchen air handling | Thiết Bị Cấp Khí Tươi Và Xử Lý Không Khí Cho Bếp | 7 | 7 | MUA-DG, MUA-IF, AEU, ARL, EXT, XEU, HRU |
| Exhaust Fans (kitchen roof-top) | Quạt Hút Khí Thải Bếp | 2 | 2 | GEF, KEFB |
| Grease Ducts | Ống Gió Dẫn Khí Nhiễm Dầu Mỡ | 4 | 4 | HSWCK, HDWCK+1, HDWCK-ZC, HDS |
| Kitchen Fire Safety | Hệ Thống An Toàn Phòng Cháy Cho Bếp | 5 | 5 | FSS, KGS (CE), KGS (ETL), HFW, Halton SafeGuard |
| Energy and Controls (M.A.R.V.E.L. DCKV + kitchen airflow control) | Hệ Thống Điều Khiển Và Tiết Kiệm Năng Lượng (M.A.R.V.E.L. DCKV) | 11 | 11 | MRV, MRV DCKV, MRVPLUS, HES, AirWatch, ABD (CE), ABD (ETL), MBD, KBD, KVV-R, KVV-S |
| Utility Distribution Systems | Hệ Thống Phân Phối Tiện Ích Cho Bếp | 4 | 4 | UTL, UPT, KDS-W, KDS-I-CB |
| Kitchen Lighting | Đèn Chiếu Sáng Bếp Chuyên Dụng | 1 | 1 | HCL |
| Diffusers and Plenums (HVAC) | Miệng Gió Và Hộp Phân Phối Gió (HVAC) | **43** | 43 | APL, APS, BCF, CAR, DAC, DCS, DFA, DFB, DFP, DRV, IAO, JCC, JDA, JDB, JDS, JLN, JMC, JRC, JRP, JSC, JTH, JWC, PDA, PDF, PDI, PLD, PLL, PLM, PLN, SLL, SLM, SLN, TCM, TCV, THL, TLB, TLD, TRB, TRS, TSA, TSR, **TDMS**, **TDM** |
| Grilles and Plenums | Cửa Gió Nan Và Hộp Đấu Nối | 19 | 19 | AGC, AHD, ALE, ALU, ASC, AWE, AWU, BDR, BOS, EVA, FLE, FLU, GDD, GSP, HDF, PRL, VSC, VSG, WDD |
| Chilled Beams | Dầm Lạnh (Chilled Beam) | 16 | 16 | RE6, R6W, REE, REP, REW, RSI, RXP, CaBeam (exposed wall), CaBeam (integrated), CaBeam (recessed), CaBeam REC, CSW, CPA, CHB, CBH, VPR |
| Airflow Management Dampers and Controllers | Van Điều Tiết Và Bộ Điều Khiển Lưu Lượng Gió | 22 | 22 | BOX, MDC, MLC, MOC, MOS, MSB, MUC, PRA, PTS, RDR, RMC, RMK, SA, UKV, UTK, UTT, UTP, UTA, UTG, CID-01, VLR, VLS |
| Fire Dampers and Valves | Van Chặn Lửa Và Van Phòng Cháy | 22 | 22 | CFD-01, CFD-02TM, EDC, EFC, ELC, ELR, ESC, ESR, ETC, ETR, FCE, FDA, FDB2, FDH, FDI, FDK, FDL, FDO, FDT, FDV, FD-CON-A, FD-CON-2 |
| Fire Control Systems | Hệ Thống Điều Khiển Phòng Cháy | 6 | 6 | HSD, HSL, HSP, HSR, SM2, MSH |
| Smoke Control Dampers | Van Điều Khiển Khói | 6 | 6 | SCS, SFC, SFR, SMR, SSC, SSR |
| Blast Dampers and Blast Valves | Van Chống Sóng Nổ | 5 | 5 | BDH, BLD-01, BLD-02, PVKKS, PVKKSX |
| Non-Return and Pressure Relief Dampers | Van Một Chiều Và Van Xả Áp | 4 | 4 | BLD, BRD, ITC, ITR |
| Droplet Separators | Bộ Tách Giọt Ẩm | 3 | 3 | DSA, DSH, WTH |
| Measurement Units | Thiết Bị Đo Lưu Lượng Gió | 3 | 3 | MSA, MSD, MSS |
| Outdoor Louvres | Cửa Chớp Lấy Gió Ngoài Trời | 3 | 3 | USL, USM, USS |
| Transfer Grilles | Cửa Gió Chuyển Tiếp | 3 | 3 | TVA, TVB, TVC |
| Valves | Van Gió Tròn | 2 | 2 | ULA, URH |
| Displacement Ventilation | Thông Gió Kiểu Đẩy (Displacement) | 4 | 4 | JRF, ZRE, ZRW, ZSC |
| Room and Cabin Units | Dàn Lạnh Phòng Và Cabin Tàu | 4 | 4 | HME, HMF, HMM, TP1 |
| Room Automation / Workplace | Tự Động Hóa Phòng Và Văn Phòng | 5 | 5 | HTP, VRA, VSP, WRA, WSO |
| HEPA Terminal Units | Bộ Lọc HEPA Đầu Cuối | 5 | 5 | VHH, VHT, VHR, VSN, VSR |
| Disinfection / Recirculation and Air Curtain Units | Thiết Bị Khử Khuẩn, Lọc Tuần Hoàn Và Màn Khí | **5** | 5 | VDU, VCR, DWE, DWI, **UVGI-SA-W** |
| Medical Gas and Smoke Extraction | Hút Khí Và Khói Y Tế | 1 | 1 | VEX |
| Radiant Panels | Tấm Bức Xạ Nhiệt | 1 | 1 | AIN |
| Other (non-HORECA) | Sản Phẩm Khác (Ngoài Ngành F&B) | **3** | 3 | PSH, **ZPC**, **ZPR** |
| Hygienic Cooling and Heating Units | Thiết Bị Làm Mát Và Sưởi Vô Trùng Y Tế | 1 | 1 | VPA |

**Tổng: 38 nhóm / 307 model** (302 gốc đợt 1 + 5 model mới đợt 2, in đậm ở trên). 307/307 model có `specs` không rỗng.

---

## 3. Phạm vi bao phủ

### Đã bao phủ đầy đủ (so với các dòng bị thiếu ở lần khảo sát trước — nay đã có đủ)

- **Chụp hút bếp bản CE/Châu Âu (26 model):** KVI, KVF, KWI, KWF, UVI, UVF, UWI, UWF, KSR-S/F/M, USR-S/F/M, CMW-IMOD, CMW-FMOD, KCH-I, KCH-F, UCH-I, UCH-F, KBO, KVD, KVV, CJB, CJP, CCW
- **Chụp hút bếp bản Bắc Mỹ ETL/UL (23 model):** KEX, KSK, MDH ModularChef, P2 Pizza Oven Hood, EO, CH, KCH, KVE và toàn bộ biến thể water-wash/UV (KVE-CM, KVE-UV, KVE-WW, KVE-WW-UV, KVC, KVC-UV, KVC-WW, KVC-WW-UV, KVL, KVL-UV, KVM-A, KVO, KVR, KVW, KVW-UV)
- **Dòng MobiChef:** KMC (CE), KMC-A, KMC-NA, MMC (Marine MobiChef)
- **Reco-Air & kiosk:** RAH (CE), RAH (UL Recognized), KVS, KVH
- **Chụp hút bếp tàu biển:** KFM, KWH, KWT, KW3, KVM, WR, CCW-M + miệng gió galley TCG + cửa chớp hàng hải USM
- **Cấp khí tươi:** MUA-DG, MUA-IF, AEU, ARL, EXT, XEU, HRU
- **Quạt hút:** GEF, KEFB
- **An toàn cháy bếp:** FSS (ANSUL R-102), KGS (CE), KGS (ETL), HFW FireWatch, Halton SafeGuard
- **Miệng gió bếp:** KCD (CE), KCD, LFU, LVU, TCG
- **Ống gió dầu mỡ:** HSWCK, HDWCK+1, HDWCK-ZC, HDS
- **Xử lý khí thải:** PST, PST-A, PEU PolluStop, RCT ReactAway UV-C
- **Trần thông gió:** KCJ, KCJ-UV, KCW, KCW-UV, KCV, KCV-WW, KCP, CCL Cyclocell, HSC, VCS
- **M.A.R.V.E.L. và điều khiển:** MRV (CE), MRV DCKV (ETL), MRVPLUS, HES/AirWatch, ABD (CE), ABD (ETL), MBD, KBD, KVV-R, KVV-S
- **Phân phối tiện ích:** UTL Utiline, UPT Unipoint, KDS-W, KDS-I-CB
- **Đèn bếp:** HCL Halton Skyline
- **Toàn bộ dải HVAC ngoài bếp:** miệng gió & hộp gió (41), cửa gió nan (19), dầm lạnh (16), van điều tiết (22), van chặn lửa (22), van khói (6), van chống nổ (5), HEPA (5), tự động hóa phòng (5), y tế/khử khuẩn...

### KHÔNG bao gồm — và lý do

| Không có | Lý do |
|---|---|
| Trang bản dịch tiếng Phần Lan / Pháp / Đức / Thụy Điển / Trung Quốc (≈885 URL còn lại trong sitemap) | Trùng nội dung, chỉ khác ngôn ngữ. Bản tiếng Anh có thông số đầy đủ nhất |
| Giá bán | Halton không niêm yết giá. Đây là hệ thống thiết kế riêng theo từng dự án, báo giá qua form *Request a Quote* |
| Bảng kích thước / chọn model chi tiết theo đơn hàng | Nằm trong file PDF datasheet và phần mềm chọn model (MagiCAD/Revit), không phải nội dung HTML |
| Trang *solution*, *technology*, *reference* (dự án tham chiếu) | Không phải sản phẩm — là giải pháp, công nghệ và case study |
| `heat_source` (để trống ở hầu hết model) | Halton sản xuất **thiết bị thông gió**, không phải thiết bị nấu. Chỉ MUA-DG và MUA-IF ghi `gas` vì trang gốc nêu rõ là bộ cấp khí gia nhiệt đốt gas |
| `origin` ghi đồng nhất "Finland" | Theo xuất xứ thương hiệu (Halton Group, Phần Lan). Website không công bố nhà máy sản xuất theo từng model nên không suy đoán |

### Điểm còn thiếu nhỏ (đã kiểm đếm, không phải lỗi)

- ~~10/302 model không có bảng `specs`~~ → **đã vá xong ở đợt 2** (xem mục 6). Còn lại: **26/307 model không có `seo_description`** vì trang gốc không khai báo thẻ `<meta name="description">`. Đã để trống thay vì bịa.
- **`title` và `seo_title` khác nhau** ở nhiều model — đúng như website (H1 khác thẻ title SEO), không phải lỗi.

---

## 5. Đợt 2 — Rà soát lại ~34 trang "chỉ có bản không phải tiếng Anh" (18/07/2026)

**Phương pháp:** tải lại `product-sitemap.xml` + `product-sitemap2.xml` (1.187 URL tất cả ngôn ngữ), lọc ra 126 trang `/cn/products/...` (bản tiếng Trung — nhóm ngôn ngữ lớn nhất ngoài tiếng Anh, các ngôn ngữ khác như cz/dk/es/hu/it/jp/nl/no/pl/pt/ru/tr mỗi ngôn ngữ chỉ có 1 URL là trang index `/products/`, không phải trang sản phẩm). Với mỗi trang `/cn/`, đọc thẻ `<link rel="alternate" hreflang="en" href="...">` trong `<head>` để xác định **chính xác** trang đó có bản tiếng Anh tương ứng hay không (thay vì đoán theo tên slug — nhiều slug CN rút gọn như `kvi`, `marvel`, `abd` thực ra trỏ tới đúng model đã crawl ở đợt 1 dưới slug tiếng Anh dài hơn, ví dụ `kvi-exhaust-hood-capture-jet-en`).

**Kết quả xác minh lại — con số thật khác với ước tính ban đầu (~34):**

| Loại | Số lượng | Ghi chú |
|---|---|---|
| Trang `/cn/` có `hreflang="en"` trỏ tới trang tiếng Anh **đã có sẵn** trong data-full.json | 117 | Không cần crawl lại — đã nằm trong 302 model đợt 1, chỉ khác slug |
| Trang `/cn/` **thực sự không có bản tiếng Anh nào** (không có thẻ `hreflang="en"`) | **9** | Đây mới là con số chính xác của "~34" ước tính ban đầu — ước tính cũ dựa trên so khớp tên slug thô, không kiểm tra hreflang nên bị thổi phồng |

**9 trang không có bản tiếng Anh, đã xử lý từng trang:**

| Model | Trang gốc | Quyết định | Lý do |
|---|---|---|---|
| **UVGI-SA-W** | `/cn/products/uvgi-sa-w-halton-sentinel-wall-filtration-unit/` | ✅ **Đã thêm** (ưu tiên theo yêu cầu) | Nội dung trang thực chất **100% tiếng Anh** dù nằm dưới path `/cn/` (không cần dịch) — máy lọc khí UVGI di động cho nhà hàng/bếp công nghiệp, liên quan trực tiếp HORECA |
| **TDMS** | `/cn/products/tdms-zh_cn/` | ✅ **Đã thêm** (dịch từ tiếng Trung) | Miệng gió trần tròn **inox chuyên bếp**, đạt chuẩn USPHS — rõ ràng liên quan bếp công nghiệp |
| **TDM** | `/cn/products/tdm-zh_cn/` | ✅ **Đã thêm** (dịch từ tiếng Trung) | Cùng họ sản phẩm với TDMS (bản thép thường, không inox) — giữ lại cho đủ bộ, không phải ưu tiên cao vì không đặc thù bếp |
| **ZPC** | `/cn/products/halton-zpc-圆形消声器/` | ✅ **Đã thêm** (dịch từ tiếng Trung) | Bộ tiêu âm đường ống tròn — phụ kiện chung cho hệ thống gió, có thể dùng cho đường ống hút khói bếp |
| **ZPR** | `/cn/products/halton-zpr-矩形片式消声器/` | ✅ **Đã thêm** (dịch từ tiếng Trung) | Bộ tiêu âm đường ống chữ nhật — cùng lý do với ZPC |
| **HEW / AirWatch** | *(không có trang riêng)* | ⏭️ Không cần thêm — **đã có sẵn** | "HEW" chỉ là mã tên file ảnh nội bộ (`HEW-Product-Main.png`) trên trang **AirWatch** (`https://www.halton.com/products/halton-airwatch-indoor-environmental-quality-sensor-eu/`) đã crawl từ đợt 1, KHÔNG phải sản phẩm riêng biệt — đã kiểm tra sitemap không có URL nào chứa "hew" |
| **anti-virus-film** | `/cn/products/anti-virus-film/` | ❌ **Bỏ qua** | Màng dán kháng khuẩn cho màn hình cảm ứng/bộ điều nhiệt phòng — phụ kiện linh tinh, không phải thiết bị bếp/HVAC, không đáng đưa vào catalog sản phẩm |
| **Halton HFB** | `/cn/products/halton-hfb-圆形变风量阀/` | ❌ **Bỏ qua** | Van biến lưu lượng gió tròn (VAV) cho HVAC dân dụng chung — trang gốc ghi rõ **"đã ngừng sản xuất từ 01/09/2023, thay thế bằng Halton Max MOC"** → sản phẩm hết vòng đời, không có ý nghĩa đưa vào catalog thương mại đang bán |
| **VARIO** | `/cn/products/vario-zh_cn/` | ❌ **Bỏ qua** | Ghi rõ trong tiêu đề trang: *"Halton Vario System for High Class Offices"* — hệ thống điều hòa/phân phối khí cho **văn phòng cao cấp**, hoàn toàn không liên quan ngành bếp/nhà hàng, đúng loại "HVAC dân dụng châu Âu" mà yêu cầu cho phép bỏ qua |

**Kết luận:** Tổng cộng đã thêm **5/9** trang, bỏ qua có lý do rõ ràng **4/9** (3 trang không liên quan bếp + 1 model đã có sẵn dưới tên khác). Con số "~34" ban đầu là ước tính thô dựa trên so khớp slug chưa kiểm chứng qua hreflang thật; sau khi kiểm chứng lại bằng dữ liệu gốc từ server, số trang thực sự "chỉ có bản không tiếng Anh và chưa được crawl" chỉ là 9, không phải 34.

---

## 6. Đợt 2 — Vá 10 model bị rỗng `specs` (18/07/2026)

Cả 10 trang đều được tải lại trực tiếp từ `halton.com` (không phải cache), bóc tách đúng các mục *Overview* / *Specification* hiển thị trên trang (một số trang gốc chỉ có tab *Overview* mà không có tab *Specification* riêng — đã dùng nội dung *Overview* làm `specs` để nhất quán với cách các model khác trong file lưu trữ theo heading gốc).

| Model | Trang gốc | Trạng thái |
|---|---|---|
| **EO** | `commercial-kitchen-exhaust-hood-eo-exhaust-only-na-en` | ✅ Đã điền đủ 2 mục *Overview* + **Specification** (mục Specification thật có trên trang, đúng như verify agent xác nhận — bị bỏ sót ở đợt 1 do lỗi bóc tách, nay đã lấy đủ) |
| CJB | `cjb-en_gb` | ✅ Đã điền *Overview* + *Specification* |
| CH | `ch-condensate-hood-na-en` | ✅ Đã điền *Overview* + *Specification* (NSF certified) |
| MRV | `mrv-energy-saving-technology-en` | ✅ Đã điền *Overview* (mô tả kỹ thuật M.A.R.V.E.L. đầy đủ) + *MaaS* + *M.A.R.V.E.L.-Ready option* — trang gốc thật sự không có mục "Specification" riêng (chỉ có tab Overview), không phải lỗi crawl |
| ABD (CE) | `abd-automated-balancing-damper-for-kitchen-ventilation-ce` | ✅ Đã điền *Overview* — mục *Dimensions* trên trang gốc chỉ là 1 ảnh kỹ thuật (không có text để bóc) |
| KVV-S | `kvv-s-square-vav-box-kitchen-ventilation-na-en` | ✅ Đã điền *Overview* |
| KDS-W | `kds-w-kitchen-utility-distribution-system-na-en` | ✅ Đã điền *Overview* + *Specification* |
| FDL | `fdl-a0a60-fire-damper` | ✅ Đã điền *Overview* + ghi chú quan trọng: **sản phẩm đang bị ngừng sản xuất dần** (theo thông báo chính thức trên trang) |
| PVKKS | `pvkks-blast-valve` | ✅ Đã điền *Overview* |
| PVKKSX | `pvkksx-blast-valve` | ✅ Đã điền *Overview* |

Sau đợt 2: **307/307 model có `specs` không rỗng (100%)**.

---

## 7. Kiểm chứng chất lượng (cập nhật sau đợt 2)

- 307/307 model có `source_url` riêng, không trùng lặp (302 gốc đợt 1 + 5 mới đợt 2)
- 307/307 model có `image_url` thật (302 host `images.halton.com`/`halton.com` từ đợt 1 + 5 model mới cũng lấy ảnh thật từ trang gốc, gồm 2 model dùng host `images.halton.com` và 3 model dùng `www.halton.com`)
- 307/307 model có `title` và `description_en` không rỗng
- 307/307 model có `specs` không rỗng (100%, sau khi vá 10 model đợt 1 — xem mục 6)
- Kiểm tra ngẫu nhiên 25 `source_url` + 25 `image_url` (đợt 1): **tất cả trả về HTTP 200**
- 307 mã model không trùng nhau; các mã trùng gốc được phân biệt bằng hậu tố lấy từ chính tiêu đề trang: `KCD (CE)`, `JES (CE)` / `JES (ETL)`, `RAH (CE)` / `RAH (UL Recognized)`, `KGS (CE)` / `KGS (ETL)`, `ABD (CE)` / `ABD (ETL)`, `CaBeam (exposed wall)` / `(integrated)` / `(recessed)`
- Ghi chú: slug của model **HSWCK** trên website Halton bị gõ nhầm thành `hswgd-...`; đã lấy mã đúng **HSWCK** theo tiêu đề H1 của trang, `source_url` giữ nguyên đường dẫn thật đang chạy
- 5 model mới đợt 2 có thêm trường `crawl_note` (không có ở 302 model gốc) ghi rõ lý do/nguồn gốc đặc biệt (trang chỉ có bản tiếng Trung, không có hreflang tiếng Anh) — giữ minh bạch khi import WordPress, có thể loại bỏ trường này lúc import nếu không cần hiển thị
