# Halton (Phần Lan) — Danh mục sản phẩm Chụp Hút Khói / Thông Gió Bếp Công Nghiệp

- **Nguồn chính thức:** https://www.halton.com (dải sản phẩm **Foodservice** — thông gió & xử lý khí thải bếp công nghiệp).
- **Ngày crawl:** 2026-07-18
- **Thương hiệu:** Halton-Finland (hãng Phần Lan, chuyên thông gió bếp công nghiệp & giải pháp không khí trong nhà).
- **Phân loại của user:** "Chụp hút khói".

## Tổng quan số liệu
- **8 nhóm (categories)** — **17 model/hệ thống** thật, mỗi cái có `source_url` hoạt động:
  1. **Exhaust Hoods (Capture Jet)** — 6 model: KVE, KVC, KVL, KVO, KVE-CM, EO
  2. **Condensate Hoods** — 2 model: KCH (Type I), CH (Type II)
  3. **UV Hoods (Capture Ray)** — 2 model: KVE-UV, KVC-UV
  4. **Water Wash Hoods** — 3 model: KVE-WW, KVC-WW, KVE-WW-UV
  5. **Ductless / Kiosk Ventilation** — 1: KVS
  6. **Ventilated Ceilings** — 1: VCS
  7. **Pollution Control Units** — 1: PST (PolluStop)
  8. **Demand Control & Energy** — 1: M.A.R.V.E.L. (MRV) DCKV

## Lưu ý quan trọng (caveats)
- **Halton bán HỆ THỐNG / CÔNG NGHỆ, không phải SKU đóng hộp.** Mỗi chụp hút được thiết kế và cắt theo kích thước từng dự án; "model code" (KVE, KVC…) là dòng sản phẩm/công nghệ chứ không phải mã hàng cố định. Đây là bản chất sản phẩm, không phải thiếu dữ liệu.
- **Dùng phiên bản Bắc Mỹ (na-en)** vì trang sản phẩm chi tiết & đầy đủ specs nhất. Halton còn có phiên bản CE (châu Âu) với mã tương đương (VD KWI/KWF water wash, KVF supply-air, KCH-I/UCH-I condensate, RAH recirculation) — chưa đưa vào để tránh trùng công nghệ.
- **Xuất xứ:** để "Finland" (trụ sở hãng). Halton có nhà máy nhiều nước; trang sản phẩm không ghi nơi lắp ráp cụ thể nên không bịa.
- **`seo_description` để trống:** các trang render động (JS), server không trả về thẻ meta description qua fetch. `seo_title` (thẻ `<title>`) lấy được đầy đủ.
- **`image_url`:** lấy ảnh sản phẩm thật từ images.halton.com (link trực tiếp trên trang). Đã kiểm cấu trúc, hợp lệ.

## Còn thiếu / có thể bổ sung sau (gaps)
- **KO ductless hood** (na-en): slug `ko-ductless-kitchen-hood-na-en` render ra trang products chung, không xác nhận được trang riêng → **bỏ qua**. Dòng ductless/recirculation của Halton (Reco-Air RAH, MobiChef) là bản CE.
- **Marine/Galley water wash** (KW3, KWT, KWH) — dành cho tàu biển/offshore, ngoài phạm vi bếp HORECA trên bờ → không đưa vào.
- **Make-up Air Units, Exhaust Fans, Kitchen Fire Safety, Diffusers (KCD)** — thuộc dải Foodservice nhưng là phụ kiện/thiết bị bổ trợ, không phải "chụp hút khói" cốt lõi → chưa crawl chi tiết. Có thể bổ sung nếu cần đầy đủ hệ thống.
- **Các công nghệ nền** (Capture Jet, Capture Ray UV, Water Wash, KSA filter) là công nghệ chung dùng chéo nhiều model — đã mô tả trong specs từng sản phẩm, không tách thành "sản phẩm" riêng.

Tất cả dữ liệu là THẬT, không bịa. Mỗi entry có URL nguồn kiểm chứng được.
