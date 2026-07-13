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

## project-render.py — KHÔNG khớp quy ước hiện tại (quan trọng)

`scripts/project-render.py` (generator cũ) sinh tên file theo kiểu tự phân loại
Elementor section: `01-hero.php`, `02-text.php`, `03-split.php`... Nhưng **34 dự án đã
publish dùng tên section CỐ ĐỊNH**: `hero.php`, `intro.php`, `concept.php`,
`partnership.php`, `specs.php`, `gallery.php` (đúng theo
`docs/project-pillar-standard.md`).

→ Generator **không phải** nguồn tạo ra cấu trúc hiện tại như-đang-thấy, hoặc đã bị hand-edit
nặng sau khi sinh, hoặc dùng phiên bản tooling khác trước đó đã thất lạc. **Không coi
project-render.py là pipeline đáng tin để tự động tái tạo 34 dự án hiện có** — chạy nó sẽ
ra cấu trúc KHÁC với convention thật.

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
3. Tạo 6 file `.php` + 6 file `.css` trong `template-parts/project-pillar/<slug>/` theo
   đúng tên section chuẩn (xem `docs/project-pillar-standard.md`) — có thể dùng
   `project-render.py <slug>` làm điểm khởi đầu rồi **đổi tên file thủ công** về đúng quy
   ước (01-hero.php → hero.php...), hoặc copy cấu trúc từ 1 dự án tương tự làm khung.
4. Tạo `single-project/<slug>.php` + `.css` (wrapper: get_header → 6 get_template_part →
   `_related` + `_cta` → get_footer — copy 1 wrapper có sẵn làm mẫu, ~24 dòng).
5. Nếu slug post WordPress khác tên thư mục pillar: thêm alias vào
   `sgh_project_slug_aliases()` trong `inc/config/routes.php`.
6. `npm run build` (tự phát hiện entrypoint mới — không cần sửa `package.json`).
7. `npm run perf:all -- --filter <slug>` — xác nhận Lighthouse trước khi publish.

## Việc CHƯA làm (để lại có chủ đích, không phải bỏ sót)

- Chuẩn hoá `project-render.py` để tên file output khớp quy ước — cần hiểu rõ logic
  phân loại `render_hero/render_split/render_gallery/...` và ánh xạ đúng sang 6 tên cố
  định, rủi ro làm sai nếu không có test coverage; để dành cho phiên làm việc riêng.
