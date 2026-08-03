# Josper (Tây Ban Nha) — Catalog sản phẩm

**Ngày crawl:** 2026-07-18
**Thương hiệu:** Josper S.A. — nhà sản xuất lò nướng than (charcoal oven) & bếp nướng than mở (Basque grill / BBQ) của Tây Ban Nha.
**Phân loại của SaigonHoreca:** BBQ grill.

## Nguồn chính thức đã dùng

- **jospergrill.com** — trang sản phẩm tiếng Anh chính thức toàn cầu của Josper (dùng làm nguồn chính).
- Trang gốc **josper.es / josper.es/en** hiện **lỗi chứng chỉ SSL** (`esm26.siteground.biz` không khớp altnames) nên WebFetch không tải được. Đã chuyển sang jospergrill.com — cùng hệ dữ liệu sản phẩm, ảnh và mã model trùng khớp.

## Số lượng thu được

**24 model thật, chia 3 nhóm:**

1. **Lò Nướng Than Josper (Charcoal Ovens) — 9 model**
   - Dòng HJX-PRO: HJX-15, HJX-PRO Mini, S80 Small, M120 Medium, L175 Large
   - Dòng HJA-PLUS (Class A nâng cấp): Mini (HJA-20), S80 Small (HJA-25), M120 Medium (HJA-45), L175 Large (HJA-50)
2. **Bếp Nướng Than Mở Basque (Basque Grills) — 9 model**
   - Countertop: PVJ-50-1-1-CT, PVJ-50-2-1-CT, PVJ-76-1-1-CT, PVJ-76-2-1-CT
   - Monoblock: PVJ-50-1-1-MB, PVJ-50-2-1-MB, PVJ-76-1-1-MB, PVJ-76-2-2-MB, PVJ-76-3-3-MB
3. **Combo (Lò + Bếp Basque) — 6 model**
   - CVJ-50-PRO-S/M/L và CVJ-76-PRO-S/M/L

Mỗi model đều có `source_url` thật, ảnh sản phẩm thật (img src, thư mục `/wp-content/uploads/`), `seo_title` lấy từ thẻ `<title>`, và mô tả + thông số kỹ thuật rút trực tiếp từ trang sản phẩm.

## Thiếu / chưa chắc chắn (data-quality caveats)

- **Giá:** Josper KHÔNG công khai giá trên website → để trống, cần liên hệ báo giá. (Có price-list PDF 2022 của đại lý bên thứ ba nhưng không dùng vì không phải nguồn chính thức hiện hành.)
- **Meta description:** Các trang sản phẩm jospergrill.com hầu như không có thẻ meta description → ghi "not published — inquire". `seo_title` thì có đầy đủ.
- **Phụ kiện (Accessories):** Trang `/product-category/josper-accessories/` chỉ có 1 file PDF tổng hợp (`josper-accessories-en.pdf`), KHÔNG có trang sản phẩm riêng cho từng phụ kiện → không đưa vào JSON để tránh bịa. Nếu cần, tải PDF để bóc tách thủ công.
- **Dòng/nhóm chưa crawl** (tồn tại trên site nhưng ngoài phạm vi yêu cầu core BBQ/oven): Robatagrills RGJ PRO, Mangals, Hibachi, Cookware, Wood Charcoal, Etxebarri Grills. Có thể bổ sung sau nếu muốn mở rộng.
- Một vài trang combo có thẻ `<title>` bị lệch tên (ví dụ CVJ-76-PRO-S hiển thị title "CVJ-50-PRO-S", CVJ-50-PRO-L hiển thị title cũ "CVJ-050-2-HJX-50") — đây là lỗi SEO của chính website Josper; đã giữ nguyên đúng như trang gốc, model code lấy theo nội dung thân trang (chính xác).
- Đơn vị kích thước không đồng nhất giữa các trang: dòng HJX-PRO & Basque & Combo dùng **mm**, dòng HJA-PLUS dùng **cm** — đã giữ nguyên đơn vị gốc và đặt tên key rõ (`_mm` / `_cm`).
