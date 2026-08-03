# PILLAR-STATUS — Tình trạng thật của hệ 34 dự án pillar (07/2026)

## Tóm tắt

- **32 file** `scripts/project-data/*.json`.
- **31 dự án** có đủ `template-parts/project-pillar/<slug>/` (6 section chuẩn:
  hero/intro/concept/partnership/specs/gallery) + wrapper `single-project/<slug>.php`
  + CSS entrypoint riêng.
- **`pho-24`**: JSON tồn tại nhưng **không có pillar dir, không có post WordPress sống**
  (`/du-an/pho-24/` → 404). Mồ côi — không sinh bundle, không cần xử lý trừ khi có
  yêu cầu publish dự án này.
- **`grand-marble` vs `grand-marble-thuong-hieu-banh-cao-cap-nhat-ban`**: post WordPress
  thật có slug **`grand-marble`**, nhưng thư mục pillar/CSS lại tên
  `grand-marble-thuong-hieu-banh-cao-cap-nhat-ban` → trước đây route resolver không khớp,
  trang này ăn fallback `theme-project.css` (1.13MB) thay vì bundle riêng (139KB).
  **Đã sửa** (07/2026): thêm alias trong `inc/config/routes.php`
  (`sgh_project_slug_aliases()`) — không đổi tên 12 file của dự án, chỉ dạy route resolver
  biết 2 tên này là một. Verify: `/du-an/grand-marble/` giờ load đúng
  `theme-project-grand-marble-thuong-hieu-banh-cao-cap-nhat-ban.css`.

## project-render.py — ĐÃ SỬA (Bước 5, 07/2026): tên file semantic theo vị trí

**Trước đây**: generator tự phân loại Elementor section theo cấu trúc → tên file
`01-hero.php`, `02-text.php`, `03-split.php`... KHÔNG khớp quy ước 34 dự án đã publish
(`hero.php`, `intro.php`, `concept.php`, `partnership.php`, `specs.php`, `gallery.php`).

**Nguyên nhân gốc không phải chỉ là tên gọi khác nhau**: `intro`/`concept`/`partnership`/
`specs` là phân loại theo Ý NGHĨA NỘI DUNG (đoạn này nói về gì), trong khi generator chỉ
phân loại được theo CẤU TRÚC HTML (mấy cột, có ảnh không) — 4 loại trên có cấu trúc HTML
giống hệt nhau (1 khối heading+text, đôi khi kèm ảnh), không có cách nào suy luận ngữ
nghĩa từ cấu trúc. Kiểm tra trực tiếp nội dung 34 dự án thật còn cho thấy mỗi dự án có
BEM prefix riêng (`pp-cm-` casa-maria, `pp-hero-bam` bambino...) + trang trí SVG/CSS riêng
— rõ ràng là thiết kế TAY sau khi có khung, không phải output thuần tự động của bất kỳ
generator nào.

**Đã sửa** (theo quyết định: chỉ sửa tên file + thứ tự cố định, KHÔNG cố suy luận ngữ
nghĩa): `semantic_name_for_index()` gán tên theo VỊ TRÍ section — section 1→`hero.php`,
2→`intro.php`, 3→`concept.php`, 4→`partnership.php`, 5→`specs.php`, 6+→`gallery.php`
(section 7 trở đi trùng tên → `gallery-2.php`, `gallery-3.php`...). Renderer chọn LAYOUT
(hero/split/gallery/text...) vẫn dựa theo `classify()` cấu trúc như cũ — chỉ tên file đổi.
Đã test bằng JSON tổng hợp 6 và 7 section (không đụng project-data thật), verify đúng tên
+ đúng xử lý trùng `gallery-2.php`.

**Giới hạn còn lại (đã ghi rõ trong code comment)**: output vẫn là **bản nháp generic**
(class dùng chung `pp-hero__title`, `pp-text__title`...) — người dùng vẫn phải tự thêm
BEM prefix + trang trí riêng cho từng dự án như 34 dự án hiện có, generator không thay
thế bước thiết kế tay đó, chỉ tạo khung khởi đầu đúng tên file.

`scripts/project-extract.py` (bước tạo JSON từ HTML scrape) vẫn phụ thuộc
`static-mirror/du-an/<slug>/index.html` — thư mục này đã bị dọn khỏi theme (chuyển vào
`_sgh-cleanup-backup` theo `DEPLOY-NOTES.txt` đợt 06/2026) → cần khôi phục mirror hoặc
viết JSON tay trước khi chạy render.

`scripts/project-extract.py` cũng phụ thuộc `static-mirror/du-an/<slug>/index.html` —
thư mục này đã bị dọn khỏi theme (chuyển vào `_sgh-cleanup-backup` theo `DEPLOY-NOTES.txt`
đợt 06/2026) → extract cũng không chạy được ngay mà không khôi phục mirror trước.

**Bằng chứng củng cố** (git status, 07/2026): `wp-content/.theme-orphan-backup/` — nằm
NGOÀI theme, đang bị staged xoá bởi ai đó trước phiên này (không đụng, không phải việc
của phiên nâng cấp này) — chứa ĐÚNG output kiểu `01-hero.php`, `02-with_gallery.php`,
`03-split.php` của `project-render.py`. Xác nhận: generator này TỪNG được dùng, output
của nó bị bỏ/thay bằng cấu trúc semantic hiện tại, rồi orphan-backup đó đang được dọn dẹp
riêng (không phải việc của Claude phiên nâng cấp perf này).

## Quy trình thêm dự án mới (thực tế, không giả vờ full-auto)

1. `python scripts/new-project.py <slug>` — tự động tạo CSS entrypoint +
   imports manifest (an toàn, boilerplate cố định). In checklist phần còn thiếu.
2. Tạo `scripts/project-data/<slug>.json` (viết tay hoặc restore static-mirror rồi chạy
   `project-extract.py`).
3. Tạo 6 file `.php` + 6 file `.css` trong `template-parts/project-pillar/<slug>/` —
   `project-render.py <slug>` giờ SINH ĐÚNG tên (`hero.php`, `intro.php`, `concept.php`,
   `partnership.php`, `specs.php`, `gallery.php`), không cần đổi tên tay nữa. Output vẫn
   là khung generic — thêm BEM prefix + trang trí riêng theo phong cách dự án tương tự.
4. Tạo `single-project/<slug>.php` + `.css` (wrapper: get_header → 6 get_template_part →
   `_related` + `_cta` → get_footer — copy 1 wrapper có sẵn làm mẫu, ~24 dòng).
5. Nếu slug post WordPress khác tên thư mục pillar: thêm alias vào
   `sgh_project_slug_aliases()` trong `inc/config/routes.php`.
6. `npm run build` (tự phát hiện entrypoint mới — không cần sửa `package.json`).
7. `npm run perf:all -- --filter <slug>` — xác nhận Lighthouse trước khi publish.

## Việc CHƯA làm (để lại có chủ đích, không phải bỏ sót)

- Auto-phân loại NGỮ NGHĨA nội dung (biết section nào thật sự "nói về concept" khác
  "nói về partnership") — không khả thi đáng tin bằng heuristic cấu trúc, cần AI đọc
  hiểu nội dung thật hoặc người xác nhận tay. Vị trí cố định (Bước 5) là giải pháp thực
  tế, chấp nhận được vì 34 dự án cũng đi theo đúng thứ tự này.
- `project-extract.py` cần `static-mirror/` được khôi phục để chạy — chưa làm vì không có
  yêu cầu cụ thể; JSON có thể viết tay cho dự án mới trong lúc chờ.
