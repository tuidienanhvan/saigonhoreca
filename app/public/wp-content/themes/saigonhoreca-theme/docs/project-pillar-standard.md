# Quy Chuẩn Thiết Kế & Cấu Trúc Project Pillar (Dự Án Con)

Tài liệu này định nghĩa cấu trúc thư mục, quy hoạch mã nguồn và thiết kế giao diện cho các dự án con (Project Pillar) trong Theme Saigon Horeca. Đây là chuẩn mực tối thượng để các kỹ sư phát triển nhanh chóng các dự án tiếp theo với chất lượng đồng nhất, chuẩn SEO và tối ưu hiệu năng.

> [!NOTE]
> **Cập nhật cấu trúc (2026-05, T-034):** Section *Related* và *CTA* **không còn tạo riêng cho từng dự án**. Hai khối này nay là **partial dùng chung toàn site** (`_related.php` + `_cta.php`), tự render ở cuối mọi trang dự án. Mỗi dự án con chỉ còn **6 section nội dung riêng** (hero → gallery). Nội dung đúc kết / lời kết đặc thù dự án (nếu có) được gộp vào cuối **Section 6 — Gallery**.

---

## 1. Cấu Trúc Thư Mục Quy Chuẩn

Mỗi dự án con phải có một thư mục riêng biệt đặt tại `template-parts/project-pillar/[slug-du-an]/` (gồm **6 section riêng**) và file CSS gốc tại `single-project/[slug-du-an].css`.

Cấu trúc thư mục của một dự án con hoàn chỉnh:

```text
wp-content/themes/saigonhoreca-theme/
├── single-project/
│   └── [slug-du-an].css                 # File import & style bổ sung cho toàn trang dự án
└── template-parts/
    └── project-pillar/
        ├── _related.php / _related.css   # 🌐 GLOBAL — dùng chung mọi dự án (KHÔNG tạo riêng)
        ├── _cta.php     / _cta.css       # 🌐 GLOBAL — dùng chung mọi dự án (KHÔNG tạo riêng)
        └── [slug-du-an]/
            ├── hero.php / hero.css       # Section 1: Banner chính & Slogan
            ├── intro.php / intro.css     # Section 2: Alternating Storytelling (Chữ & Ảnh đan xen)
            ├── concept.php / concept.css # Section 3: Concept thiết kế & bố cục quầy bar/đầu bếp
            ├── specs.php / specs.css     # Section 4: Bản vẽ kỹ thuật & Thực tế (Collage)
            ├── partnership.php / .css    # Section 5: Đối tác vận hành & Triết lý lắp đặt
            └── gallery.php / gallery.css # Section 6: Thư viện ảnh + (tùy chọn) khối đúc kết/lời kết
```

> [!IMPORTANT]
> **Thứ tự render trong wrapper** `single-project/[slug-du-an].php`:
> ```php
> get_template_part('template-parts/project-pillar/[slug]/hero');
> // ... intro, concept, specs, partnership ...
> get_template_part('template-parts/project-pillar/[slug]/gallery');
> get_template_part('template-parts/project-pillar/_related');  // 🌐 GLOBAL — luôn cuối trang
> get_template_part('template-parts/project-pillar/_cta');      // 🌐 GLOBAL — luôn cuối trang
> ```
> ❌ **KHÔNG** tạo `[slug]/related.php` hay `[slug]/cta.php` per-project nữa — sẽ trùng lặp với 2 partial global.

> [!IMPORTANT]
> Toàn bộ CSS của dự án con phải được khai báo `@import` vào file `assets/css/imports/_imports-project.css` để công cụ Tailwind v4 build-time đóng gói và tối ưu hóa thành một file bundle CSS duy nhất (`theme-project.css`) cho toàn bộ route `single-project`. CSS của 2 partial global (`_related.css`, `_cta.css`) được import **một lần duy nhất** ở đầu file, KHÔNG lặp theo từng dự án.

---

## 2. Quy Chuẩn Layout Cho Từng Section

### Section 1: Hero Banner
- **PHP**: Gọi ảnh đại diện chất lượng cao thông qua `sgh_img('[slug-du-an]/[slug-du-an]-hero.jpg')`. Sử dụng thẻ tiêu đề `<h1>` chứa tên dự án (SEO chuẩn) và một đoạn mô tả súc tích dài tối đa 2-3 câu làm sub-headline.
- **CSS**: Áp dụng hiệu ứng nền tối mờ ảo (overlay gradient), định vị nội dung căn giữa hoặc căn trái phía góc dưới. Sử dụng animation mượt mà cho chữ khi load trang (fade-in-up).

### Section 2: Intro (Alternating Storytelling)
- **Bố cục**: Phá vỡ lưới 2 cột truyền thống bằng cấu trúc so le không đối xứng (Asymmetric Alternating). Ví dụ: Cột chữ chiếm 38% / Cột ảnh chiếm 62% hoặc ngược lại.
- **SVG Trang Trí**: Sử dụng các nét vẽ SVG mờ ảo chạy dưới nền (như lưới tọa độ mờ, vòng tròn xoay nhẹ) tăng tính công nghệ và kỹ thuật.
- **PHP**: Tích hợp các đoạn trích dẫn nổi bật (Highlight Quote) và chữ cái trang trí viết lớn đầu đoạn (Dropcap).

### Section 3: Concept Thiết Kế
- **Bố cục**: Layout tạp chí (Editorial Layout) với hình ảnh chiếm không gian lớn bên trái, khối văn bản được đóng gói trong thẻ nền kính mờ (Glassmorphism card) đè chéo lên mép ảnh (margin âm `-3rem` đến `-5rem` trên màn hình lớn) tạo chiều sâu 3D.
- **PHP**: Tích hợp các thẻ phân loại nhỏ (`.pp-concept-card-tag`) phía trên tiêu đề chính để tăng tính điều hướng thị giác.

### Section 4: Specs (Quy Chuẩn Kỹ Thuật)
- **Bố cục**: Cụm collage gồm 2 ảnh xếp chồng lệch góc nghệ thuật (thường là 1 ảnh Bản vẽ CAD / Sơ đồ nguyên lý nằm dưới, và 1 ảnh Thiết bị hoàn thiện thực tế nằm đè lên góc trên).
- **PHP**: Đi kèm cột mô tả thông số kỹ thuật dạng danh sách tính năng, tích hợp icon SVG sắc nét cho mỗi hạng mục. Bổ sung các nhãn tọa độ kỹ thuật giả lập (SYS_COORD, SCALE, CAD_v2.0) chạy mờ bốn góc để nhấn mạnh tính chất thiết kế cơ điện (M&E).

### Section 5: Partnership & Equipment
- **Bố cục**: Cấu trúc so sánh song song hoặc chia mảng lưới giới thiệu các dòng thiết bị nhập khẩu (ATA, Berjaya, Hoshizaki, Winterhalter).
- **CSS**: Sử dụng các card bo góc mềm mịn (`var(--radius-lg)`) với hiệu ứng đường viền sáng nhẹ (`border: 1px solid var(--bd)`) và chuyển màu hover êm ái.

### Section 6: Gallery (+ khối đúc kết tùy chọn)
- **Bố cục**: Mảng lưới gạch gốm (Masonry hoặc Metro Grid) gồm các ảnh kích thước khác nhau đan xen, cho phép nhấp vào để phóng to hoặc hiển thị slide trượt mượt mà.
- **PHP**: Tất cả các ảnh trong Gallery bắt buộc phải đi kèm chú thích (caption) tiếng Việt nghệ thuật, đặt dưới thẻ `<div class="pp-image-caption-shared">`.
- **Khối đúc kết (tùy chọn)**: Nếu dự án có đoạn lời kết / triết lý / nhấn mạnh đặc thù (trước đây nằm ở section *related/cta* riêng), đặt nó vào **cuối `gallery.php`** như một block khép lại câu chuyện — KHÔNG tạo file section riêng cho nó.

---

## 🌐 Section Dùng Chung (Global — KHÔNG tạo per-project)

Hai section cuối trang là partial dùng chung toàn site, render tự động sau Section 6. Chỉ chỉnh sửa ở **một nơi duy nhất**, áp dụng cho mọi dự án.

### `_related.php` — Dự án liên quan
- **Vai trò**: Tự động dựng lưới giới thiệu các dự án khác cùng hệ thống (đọc từ `scripts/project-data/*.json`, loại trừ dự án hiện tại, ưu tiên ảnh thật tồn tại trên đĩa).
- **Lưu ý**: Vì là điều hướng chung, layout thống nhất trên toàn site là **đúng** (không cần "độc bản" theo từng dự án). Nâng cấp hiệu ứng/hover ở `_related.css` sẽ áp dụng đồng loạt.

### `_cta.php` — Kêu gọi hành động & Liên hệ
- **Vai trò**: Khối liên hệ tư vấn dùng chung (hotline, địa chỉ, bản đồ) lấy từ `saigonhouse_get_contact_info()`. Thống nhất thông tin liên hệ Saigon Horeca trên mọi trang dự án.
- **Lưu ý**: Mọi thay đổi nội dung/thiết kế CTA chỉ sửa tại `_cta.php` + `_cta.css`. Nếu một dự án cần điểm nhấn riêng để dẫn dắt hành động, thể hiện nó trong nội dung Section 1-6 (vd lời kết ở cuối gallery), KHÔNG tạo `cta.php` riêng.

---

## 3. Quy Tắc Viết Code PHP & CSS

### Quy Tắc Viết PHP:
1. **Security**: Luôn thêm `if (!defined('ABSPATH')) exit;` ở đầu mỗi file PHP.
2. **Clean HTML**: Sử dụng thẻ ngữ nghĩa HTML5 (`<section>`, `<article>`, `<header>`).
3. **i18n**: Luôn bọc toàn bộ chuỗi tĩnh trong hàm dịch của WordPress (ví dụ: `<?php echo esc_html__('Text ở đây', 'saigonhoreca'); ?>`).
4. **Local Images Only**: Tuyệt đối không dùng đường dẫn tuyệt đối hay link production cũ. Mọi hình ảnh phải gọi qua hàm trợ giúp `sgh_img('[slug-du-an]/[ten-file-anh].jpg')`.

### Quy Tắc Viết CSS:
1. **Namespace**: Toàn bộ class phải được bọc trong tiền tố viết tắt của dự án để tránh xung đột class toàn cục (Ví dụ: `.pp-hero-tct`, `.pp-intro-cm`...). Riêng 2 partial global dùng tiền tố trung lập (`.pp-related-*`, `.pp-cta-*`) — không gắn slug dự án.
2. **Responsive**: Sử dụng các mốc breakpoint chuẩn của Tailwind CSS (`@media (min-width: 768px)`, `@media (min-width: 992px)`, `@media (min-width: 1200px)`).
3. **Typography**: Sử dụng các biến font chữ toàn cục:
   - Font sans-serif: `var(--font-sans)` (Be Vietnam Pro) dùng cho văn bản thường.
   - Font trưng bày: `var(--font-display)` (Lexend) dùng cho các tiêu đề chính.
   - Font serif: `var(--font-serif)` (Lora) dùng cho các trích dẫn nghệ thuật.

---

## 4. Thư Viện Pillar Mẫu (Reference Gallery — Pillar Đã Hoàn Thành)

> [!IMPORTANT]
> **Quy trình**: Mỗi khi một project pillar được **verify + archive** (đạt quality-bar: 0 hex brand, ≥6/9 techniques, shared partials, static-mirror đúng chỗ `themes/saigonhoreca-theme/static-mirror/`, route 200) → **thêm 1 dòng vào bảng dưới** làm mẫu tham khảo. Pillar mới chỉ cần **copy pattern** từ pillar gần nhất cùng loại concept — không dựng từ đầu.

### 4.1 Mẫu Gold Chuẩn (đọc TRƯỚC khi làm pillar mới)
Ba pillar canonical — pattern tối ưu nhất, học theo:

| Slug | abbr | Pattern tiêu biểu (tham khảo cho) |
|---|---|---|
| `roka-fella-tinh-hoa-am-thuc-nhat-ban` | `rkf` | **Gold #1** — Ken-Burns hero, glassmorphism card, corner ornaments, ambient glow. Mẫu chuẩn nhất cho concept fine-dining/Nhật. |
| `casa-maria` | `cm` | Split 55/45 reverse, staggered entry rise, backdrop pill badge. Mẫu cho intro/concept so le. |
| `bambino-saigonhoreca` | `bam` | Video bg, SVG geometric panels. Mẫu cho hero động + trang trí hình học. |

### 4.2 Registry — Tất cả pillar đã hoàn thành

| # | Slug | abbr | Nhận diện / Concept | Route |
|---|---|---|---|---|
| 1 | roka-fella-tinh-hoa-am-thuc-nhat-ban | `rkf` | Nhật fine-dining, accent vàng `--p` (GOLD #1) | `/du-an/roka-fella-tinh-hoa-am-thuc-nhat-ban/` |
| 2 | casa-maria | `cm` | Mediterranean, split reverse (GOLD #2) | `/du-an/casa-maria/` |
| 3 | bambino-saigonhoreca | `bam` | Video bg + SVG panel (GOLD #3) | `/du-an/bambino-saigonhoreca/` |
| 4 | amdang-typhoon | `adt` | Asian luxe restaurant | `/du-an/amdang-typhoon/` |
| 5 | bling-bling-club | `bbc` | Club/bar về đêm | `/du-an/bling-bling-club/` |
| 6 | g-cup-coffee-bistro | `gcb` | Coffee bistro, ambient glow (8/9) | `/du-an/g-cup-coffee-bistro/` |
| 7 | the-brix | `brix` | All-day dining | `/du-an/the-brix/` |
| 8 | the-royal-all-day-dining | `trd` | Fine all-day dining, chandelier | `/du-an/the-royal-all-day-dining/` |
| 9 | little-bear-thao-dien | `lb` | Bake & brunch Thảo Điền | `/du-an/little-bear-thao-dien/` |
| 10 | godmother-friendship | `gmf` | Bake & brunch (7/9) | `/du-an/godmother-friendship/` |
| 11 | skyloft-by-glow | `sky` | Neon rooftop bar, tokens `--sky-purple/--sky-cyan` (7/9) | `/du-an/skyloft-by-glow/` |
| 12 | the-cheezy-time | `tct` | Casual cheese eatery | `/du-an/the-cheezy-time/` |
| 13 | heiwa-sushi-omakase | `heiwa` | Japanese omakase | `/du-an/heiwa-sushi-omakase/` · `.com/heiwa-sushi-omakase/` |
| 14 | grand-marble-thuong-hieu-banh-cao-cap-nhat-ban | `gmarb` | Thương hiệu bánh cao cấp Nhật, accent caramel | `/du-an/grand-marble-thuong-hieu-banh-cao-cap-nhat-ban/` · `.com/grand-marble/` |
| 15 | mam-mam-eatery-lounge-nang-tam-mam-com-viet | `mam` | Eatery & lounge, cơm Việt | `/du-an/mam-mam-eatery-lounge-nang-tam-mam-com-viet/` · `.com/mam-mam-elevating-vietnamese-dining-experience/` |
| 16 | ganh-hao-noi-hon-bien-trong-tung-net-kien-truc | `ganh` | Coastal seafood, accent teal/sand | `/du-an/ganh-hao-noi-hon-bien-trong-tung-net-kien-truc/` · `.com/ganh-hao-where-spirit-of-the-sea-comes-together/` |
| 17 | sol-kitchen-bar | `sol` | Kitchen & bar, accent amber/ember | `/du-an/sol-kitchen-bar/` · `.com/sol-kitchen-bar/` |
| 18 | sol-kitchen-bar-saigon-horeca | `solshr` | Kitchen & bar (vn-only), accent sun/clay | `/du-an/sol-kitchen-bar-saigon-horeca/` |
| 19 | renovate-sol-kitchen-bar-quan-7 | `solq7` | Cải tạo kitchen & bar Q7, accent amber/ember | `/du-an/renovate-sol-kitchen-bar-quan-7/` · `.com/renovate-sol-kitchen-bar-district-7/` |
| 20 | mua-craft-sake-lam-ruou-sake-dau-tien-tai-viet-nam | `mua` | Craft sake brewery đầu tiên VN | `/du-an/mua-craft-sake-lam-ruou-sake-dau-tien-tai-viet-nam/` · `.com/mua-craft-sake-vietnams-first-craft-sake-brewery/` |
| 21 | du-an-bep-cang-tin-cong-ty-nhat-nichiyo | `nichiyo` | Bếp căng tin công nghiệp Nichiyo | `/du-an/du-an-bep-cang-tin-cong-ty-nhat-nichiyo/` · `.com/industrial-kitchen-canteen-kitchen-of-nichiyo/` |

> **Đang chờ làm (10)**: yuzu-omakase, elephant(kdl-rung-thong), moa-moa, du-nam-an-an, tales-by-chapter, hemma-desserts, du-an-vinh-hiep, bep-an-truong-mam-non, sheh-fung, spice-world-hotpot. → Mỗi cái xong thì bổ sung vào bảng 4.2 + xóa khỏi danh sách này.

### 4.3 Checklist trước khi thêm vào registry
- [ ] 0 hex màu brand hardcode (chỉ `var(--*)` / `color-mix`)
- [ ] ≥6/9 CSS techniques (ken-burns, @keyframes, backdrop-filter, clamp, radial-gradient, scroll-reveal, prefers-reduced-motion…)
- [ ] `pp-container-shared` + `pp-image-caption-shared` áp dụng
- [ ] Class prefix `pp-<section>-<abbr>` + BEM
- [ ] static-mirror `.vn` + `.com` (nếu có .com) tại `themes/saigonhoreca-theme/static-mirror/` — KHÔNG ở Local-root/wp-content
- [ ] Ảnh tại `uploads/<slug>/` — load 200
- [ ] Build pass + route 200
- [ ] Task verified + archived
