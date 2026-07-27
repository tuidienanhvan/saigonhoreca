# Giorik (Ý) — Nghiên cứu bổ sung danh mục

Nguồn: website chính hãng Giorik S.p.A. (Ý) — `www.giorik.com` (bản tiếng Anh `/en/`), cộng thêm microsite `popspeedoven.giorik.com`.
Ngày cào dữ liệu: 2026-07-13 (cập nhật lần 2 cùng ngày — crawl sâu thêm Ovens + Modular Cooking). Dữ liệu máy đọc song song ở [`data.json`](./data.json).

## Vì sao có tài liệu này

Site saigonhoreca.local hiện chỉ có **3 sản phẩm** dưới brand Giorik. Client muốn mở rộng danh mục bằng sản phẩm THẬT từ lineup chính hãng của Giorik. Giorik.com KHÔNG chặn crawl (WebFetch đọc được bình thường), nên đã cào trực tiếp từ site chính hãng thay vì suy đoán.

**Cập nhật 13/07 (lần 2):** đã crawl sâu thêm 5 dòng Ovens và 12/13 loại thiết bị Modular Cooking còn lại (trước đó chỉ có Bratt Pan + Salamander đầy đủ mã). Tổng cộng thêm **~512 mã model mới có URL nguồn** (ước tính ~139 mã Ovens + ~373 mã Modular Cooking khác ngoài Bratt Pan). Chi tiết ở bảng bên dưới.

## Về Giorik

Giorik S.p.A. thành lập **1963**, trụ sở tại **Sedico, Bắc Ý**. Ban đầu chuyên sản xuất nồi chiên (fryer) chuyên nghiệp, đầu thập niên 1980 mở rộng sang salamander, lò đối lưu và lò hấp-nướng kết hợp. Nhà máy rộng 12,000 m², trong đó 600 m² là phòng R&D + bếp trình diễn huấn luyện đầu bếp. ([Nguồn](https://www.giorik.com/en/home.html))

## 1. Ovens — Lò Hấp / Nướng / Đối Lưu (5 dòng, ~139 mã model)

| Dòng | Biến thể | Số mã model xác nhận | Ví dụ mã |
|---|---|---|---|
| **Steambox Evolution** (cao cấp) | Boiler – Steambox H (SNHE/SNHG) / Direct Injection – Steambox Master (SERE/SERG) | 24 | SNHE101W (10 GN1/1, 15kW, 863×796×1115mm, 160kg), SERG101W |
| **Kore** (hiệu suất cao, diện tích nhỏ) | Kore B — Boiler (KB/KBG) / Kore I — Direct Injection (KI/KIG) | 8 | KB101W (10 GN1/1, 13.2kW, 519×806×1010mm, 98kg) — chỉ có bản 6 và 10 GN1/1 |
| **Minitouch** (entry-level, compact) | Direct Injection Minitouch M (KNM), chỉ điện | 3 | KNM101W (10 GN1/1, 13.2kW, cùng khung 519×806×1010mm, 98kg như Kore) |
| **MovAir** (bánh/deli) | Direct Injection (NMTE điện / NMTG gas), mỗi mã có biến thể bản lề trái/phải (_L/_R) + finish (W/X/XW) | ~44 (ước tính, nhiều biến thể tùy chọn của cùng kiểu máy) | NMTE10_R (10 GN1/1, 12kW, 907×752×1023mm, 100kg). 2 mã phụ kiện tủ ủ bột: CLP8, CLP12 (không phải oven) |
| **EasyAir** (điều khiển cơ-điện, bánh/deli) | ECE/ECG (Direct Injection) + NECE/NECG (New Gen) + EME/EMG (Humidifier 2-speed) + NEME/NEMG | ~60 (ước tính, gồm nhiều biến thể S/X/XS) | ECE102 (10 GN1/1, 12kW, 870×749×1023mm, 100kg) |

Bộ lọc so sánh (`compare-ovens`) xác nhận có các tùy chọn: capacity GN 2/3–GN 2/1–GN 1/1 hoặc 5–20 khay, gas/điện, khổ compact/standard, công nghệ Symbiotic System/Meteo System/Steam Tuner/SuperSteam/RecipeTuner/EasyService/ONE TOUCH/Giorik Remote Analysis/Giorik Smart Recipes — nhưng trang này không có sản phẩm chọn sẵn nên không cào được bảng dữ liệu cụ thể.

Lưu ý: số mã lớn (đặc biệt MovAir, EasyAir) chủ yếu vì mỗi máy có nhiều **biến thể tùy chọn** (bản lề trái/phải, finish kính, S/X/XS) chứ không phải sản phẩm khác nhau về công dụng — khi đưa lên site nên gom theo "model chính" + option, không tạo 44-60 sản phẩm riêng biệt.

Toàn bộ danh sách mã model + URL nguồn từng dòng nằm trong `data.json` → `groups[0].series`.

Nguồn: [Ovens](https://www.giorik.com/en/products/ovens-4971222.html) · [Compare Ovens](https://www.giorik.com/en/compare-ovens) · [Steambox Evolution](https://www.giorik.com/en/products/ovens/steambox-evolution-4971490.html) · [Kore](https://www.giorik.com/en/products/ovens/kore-4971493.html) · [Minitouch](https://www.giorik.com/en/products/ovens/minitouch-4971494.html) · [MovAir](https://www.giorik.com/en/products/ovens/movair-4971495.html) · [EasyAir](https://www.giorik.com/en/products/ovens/easyair-4971491.html)

## 2. Modular Cooking — Bếp Module Unika 700 / Unika 900 / Gio 60

Hệ bếp module theo 3 độ sâu tủ: **Unika 700** (sâu 700mm), **Unika 900** (sâu 900mm, ghép nhiều thiết bị hơn), **Gio 60** (sâu 600mm, dạng để bàn). Mỗi độ sâu có 13 loại thiết bị.

| Loại thiết bị | Có ở dòng | Số mã model | Ghi chú |
|---|---|---|---|
| **Gas Ranges** | 700 (+ Basic Line)/900 (+ Basic Line)/Gio 60 | 71 | CG7xx/CG9xx (chuẩn), ECG7xx/ECG9xx (Basic Line, economy), LPGxxx (Gio 60) |
| **Solid Top** | 700/900 (không có Gio 60) | 21 | TE7xx/TE9xx (điện), TG7xx/TG9xx (gas) |
| **Electric Cookers** | 700/900/Gio 60 | 29 | CE7xQ/CE9xQ (tròn), CE7xT (vuông), LPE47/67 (Gio 60) |
| **Ceramic Glass Hobs** | 700/900/Gio 60 | 3 (chỉ Gio 60) | LPV4721/6741/6743 — bản 700/900 chỉ có mô tả chung, chưa lộ mã con |
| **Induction Cookers** | 700/900 (không có Gio 60) | 15 | CI7xx/CI9xx, CW7xxG/CW9xxG (bếp wok) |
| **Fry Top** | 700/900/Gio 60 | 118 | FLE/FRE/FME (điện), FLG/FRG/FMG (gas), hậu tố CRX = gas hiệu suất cao; LGE/LGG (Gio 60) |
| **Aquagrill** (nướng bằng nước) | 700/900 (không có Gio 60) | 20 | GL7xGE/GL9xGE (điện), GL7xGV/GL9xGV (gas) |
| **Volcanic Rock Grills** | 700/900/Gio 60 | 10 | GL7x0GC/GL9x0GC, LTG6700/8700 (Gio 60), toàn bộ gas |
| **Bain-Marie** | 700/900/Gio 60 | 10 | BM7xxE/BM9xxE (điện, 0-90°C), LBE4711/6711 (Gio 60) |
| **Pasta-Cooker** | 700/900/Gio 60 | 11 | CPE7xx/CPE9xx (điện), CPG7xx/CPG9xx (gas), LCP/LCG (Gio 60) |
| **Fryers (chiên nhúng)** | 700/900 đầy đủ mã; Gio 60 chưa lộ mã | 54 | FE7xxx/FE9xxx (điện), FG7xxxT/FG9xxxT (gas thường), FG74xxP/FG98xxP (gas clean-tank) |
| **Bratt Pans (chảo nghiêng)** | 700/900 | 12 | **Đầy đủ mã model** — xem bảng dưới |
| **Boiling Pans (nồi nấu)** | 700/900 (không có Gio 60, xác nhận trên trang nguồn) | 9 | PEI/PGD/PGI + số dung tích (50/100/150L) |

Toàn bộ danh sách mã model + URL nguồn từng loại nằm trong `data.json` → `groups[1].series`.

### Bratt Pans — bảng model đầy đủ nhất cào được

**Unika 700:**
| Model | Dung tích | Kiểu nghiêng | Nguồn nhiệt |
|---|---|---|---|
| CM720G | 15 L | Cố định | Gas |
| CM740G | 30 L | Cố định | Gas |
| BE74XM | 50 L | Nghiêng tay quay | Điện |
| BG74XM | 50 L | Nghiêng tay quay | Gas |

**Unika 900:**
| Model | Dung tích | Kiểu nghiêng | Nguồn nhiệt |
|---|---|---|---|
| CM920G | — | Cố định | Gas |
| CM940G | — | Cố định | Gas |
| BE96XA | 120 L | Tự động | Điện |
| BE94XA | 80 L | Tự động | Điện |
| BE94XM | 80 L | Tay quay | Điện |
| BG96XA | 120 L | Tự động | Gas |
| BG94XA | 80 L | Tự động | Gas |
| BG94XM | 80 L | Tay quay | Gas |

Đáy chảo dòng B**XM làm bằng thép duplex 2304. Chảo có tráng chống dính (không cần tra dầu/bơ), đổ đầy qua vòi trên ống khói.

Nguồn: [Modular Cooking](https://www.giorik.com/en/products/modular-cooking-9851681.html) · [Bratt Pans overview](https://www.giorik.com/en/products/modular-cooking/bratt-pans-9851742.html) · [Bratt Pans Unika 700](https://www.giorik.com/en/products/modular-cooking/bratt-pans/unika-700-9851789.html) · [Bratt Pans Unika 900](https://www.giorik.com/en/products/modular-cooking/bratt-pans/unika-900-9851803.html) · [Gas Ranges](https://www.giorik.com/en/products/modular-cooking/gas-ranges-9851737.html) · [Fryers](https://www.giorik.com/en/products/modular-cooking/fryers-9851749.html) · [Fry Top](https://www.giorik.com/en/products/modular-cooking/fry-top-9851744.html)

## 3. Salamanders — Bếp Nướng Salamander Tốc Độ (Rapid Salamander)

3 dòng, tất cả dùng công nghệ tấm gốm phản xạ nhiệt (đạt nhiệt độ nấu chỉ trong 8 giây):

| Dòng | Model | Ghi chú |
|---|---|---|
| **Hi Lite** | SH20, SH30, SH40 | Cơ bản, tiết kiệm năng lượng tới 79% |
| **Hi Plus** | SHP30, SHP40 (Hi-Lite Plus Rapid Electric Salamander) | Có chức năng Dish Presence (nhận biết có khay) + Hold (giữ nhiệt), phần tử gia nhiệt kích hoạt độc lập |
| **Hi Touch** | ST30, ST40 (Hi-Touch Rapid Electric Salamander) | Điều khiển cảm ứng, kế thừa công nghệ Hi-Lite |

Nguồn: [Rapid Salamanders](https://www.giorik.com/en/products/salamanders/rapid-salamanders-4971484.html) · [Hi Lite](https://www.giorik.com/en/products/salamanders/rapid-salamanders/hi-lite-13318515.html) · [Hi Plus](https://www.giorik.com/en/products/salamanders/rapid-salamanders/hi-plus-13318516.html) · [Hi Touch](https://www.giorik.com/en/products/salamanders/rapid-salamanders/hi-touch-13318517.html)

## 4. Pop Speed Oven — Lò Nướng Tốc Độ

Sản phẩm chuyên biệt có microsite riêng (`popspeedoven.giorik.com`), tách khỏi menu Ovens chính. Lò "Plug & Cook" nướng panini/sandwich/pizza/wrap tối đa 4 phần/chu kỳ, dưới 2.5 phút. Dùng hơi nước điều khiển + "air impingement technology" (đang xin bằng sáng chế). Lõi đạt 65°C, vỏ ngoài vẫn giòn (tránh vị "cao su" như lò vi sóng). Màn cảm ứng 7", khay GN 2/3 chống dính tháo rời được. Bình nước tích hợp 3 lít (~400 chu kỳ nấu), không cần đấu nước máy — chỉ cần ổ điện.

**Chưa tìm thấy mã model/SKU cụ thể**, chưa rõ site này đã bán chính thức hay còn ở giai đoạn "Coming soon".

Nguồn: [Pop Speed Oven](https://popspeedoven.giorik.com/) · [Shop](https://popspeedoven.giorik.com/shop/)

## Giá bán

Giorik **KHÔNG công bố giá bán công khai** trên website chính hãng (thông lệ ngành thiết bị bếp công nghiệp B2B). Cần liên hệ trực tiếp Giorik (info@giorik.com / +39.0437.807200) hoặc đại lý phân phối để lấy báo giá.

## Thị trường Việt Nam — CHƯA xác minh

Tìm thấy các đơn vị trong nước **tự nhận** bán/nhập hàng Giorik: AntMart, Tân Lộc Phát, Venus Corp (Thiết Bị Venus), Phương Bình Group (nhắc tới model combi oven KB061W/KBO-61W). Đây là kết quả tìm kiếm thô từ bên thứ ba, **chưa đối chiếu** với danh sách đại lý chính thức từ giorik.com — không dùng làm căn cứ xác nhận nhà phân phối chính hãng.

## Việc còn thiếu (sau lần crawl bổ sung 13/07)

**Đã giải quyết:** Ovens (5 dòng) và 12/13 loại thiết bị Modular Cooking nay đã có mã model đầy đủ với URL nguồn (xem bảng trên + `data.json`).

**Vẫn còn thiếu:**
- Gio 60 Fryer — trang depth-line chỉ mô tả chung ("modularity of 40 and 60cm"), chưa lộ mã model cụ thể, cần fetch sâu thêm.
- Ceramic Glass Hobs bản Unika 700/900 — chỉ Gio 60 có mã; 2 trang 700/900 mô tả sản phẩm chung không có mã con.
- EasyAir "New Generation" (NECE/NECG) và 1 mã MovAir nghi ngờ ("MTG10W_R") — mới thấy trên trang liệt kê, chưa fetch trang model con riêng để xác minh độc lập.
- Thông số kỹ thuật đầy đủ (kW/kích thước/trọng lượng) mới verify được cho 1 model đại diện mỗi dòng oven — các mã còn lại cùng dòng theo pattern tương tự nhưng chưa fetch riêng từng trang.
- Pop Speed Oven — vẫn chưa có mã model/SKU, chưa rõ đã bán chính thức hay còn "coming soon" (không crawl lại lần này).
- Giá bán VN (liên hệ Giorik hoặc đại lý phân phối) — Giorik không công bố giá public.
- Ảnh sản phẩm chính hãng độ phân giải cao (trang có ảnh nhưng cần tải riêng từng ảnh hoặc xin bộ catalog ảnh).
- Xác nhận nhà phân phối chính hãng tại Việt Nam (các đơn vị liệt kê ở trên chưa xác minh; lưu ý mã KB061W mà Phương Bình Group nhắc tới trùng với mã Kore Boiler B tìm được lần này — xác nhận gián tiếp mã có thật, nhưng chưa xác minh đại lý).
