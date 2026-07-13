# ARCHITECTURE — Kiến trúc 3 tầng saigonhoreca-theme

> Trạng thái (07/2026): `inc/core/` đã xoá — 22 file engine chuyển sang `inc/engine/`,
> 5 file site-specific (auto-setup, project-cover, section-images, pi-seo-resolver,
> temp-migration) chuyển sang `inc/site/`. Verify: 0 lỗi PHP + HTML-diff 652 URL toàn site
> (chênh lệch còn lại 100% là widget "dự án/sản phẩm liên quan" random mỗi lần tải).
> `inc/admin/`, `inc/customizer/`, `inc/tools/` CHƯA di chuyển vào `inc/site/` — để ở top-level
> hiện tại vì có require_once nội bộ tương đối (dashboard-class.php → sync/*) rủi ro cao hơn
> khi verify (không có HTML frontend để diff cho phần admin-only); follow-up sau nếu cần.
> functions.php CHƯA được tách nốt phần logic inline (static-html-cache, security-headers,
> https-rewrite, query-mods, update-blocker) — vẫn nằm trong functions.php, việc tách đó
> chỉ có giá trị tổ chức code chứ không ảnh hưởng hiệu năng, đã ưu tiên lùi sau Phase 6/7.
> Đọc kèm: `docs/PERF-DECISIONS.md` (bắt buộc trước khi đụng cơ chế nạp tài nguyên).

## Tầng 1 — ENGINE (`inc/engine/` — tái sử dụng được cho mọi project)

Máy móc thuần túy, **CẤM chứa**: tên brand, domain, slug tiếng Việt, ID GTM/FB, copy marketing.
Lint tự động: `node scripts/build.mjs --lint-only` (grep brand string trong engine phải = 0).

Thành phần: enqueue (route→bundle walker), critical-css, static-html-cache, security-headers,
https-rewrite, webp-conversion, bot-optimize, perf-config, cache-helpers, seo-meta/robots/schema,
slug-map (song ngữ), pillar-routes (virtual routing), redirect-manager, media-routing, img-helper,
walkers, svg-icons, breadcrumbs, query-mods, update-blocker.

## Tầng 2 — CONFIG (`inc/config/` — TẤT CẢ những gì đổi giữa project)

| File | Chứa |
|---|---|
| `brand.php` | tên site, legal name, city, SEO snippets, GTM/FB ID, social |
| `domains.php` | prod/alt/local hostnames |
| `routes.php` | map route→CSS bundle, route→hero preload image, slug CPT/page |
| `fonts.php` | Google Fonts URL / fonts.mode (google\|self), font tokens, color palette |
| `contact-info.php` | thông tin liên hệ (đã có sẵn) |
| `site-structure.php` | nav, pages, Pi integration (đã có sẵn) |
| `section-registry.php` | field editor cho Pi Dashboard (đã có sẵn) |
| `constants.php` | legacy constants (đang rút dần về các file trên) |
| `loader.php` | *(Phase 4)* `sgh_config($key, $default)` — accessor chung |

**Trạng thái migrate (07/2026):** `routes.php` (CSS bundle ladder + hero preload map) và
`fonts.php` (font family + palette) đã kết nối và verify byte-identical qua HTML-diff
652 URL. `domains.php` mới tạo constants, **CHƯA rewire** 53 chỗ literal
`saigonhoreca.vn/.com/.local` trong slug-map.php/functions.php/seo-schema.php — theo dõi
làm follow-up từng consumer một (rủi ro cao nếu sửa hàng loạt cùng lúc không verify được
từng bước). Chỉ literal city 'Hồ Chí Minh' trong seo-schema.php đã rewire về
`contact-info.php['city']`.

Accessor: `sgh_config($key, $default)` trong `loader.php`.

## Tầng 3 — PRESENTATION + SITE

- `template-parts/`, `single-project/`, `page-templates/`: template. **34 project pillar giữ
  thiết kế riêng từng dự án** (chủ đích) — sinh bằng pipeline, không viết tay:
  thêm dự án mới = 1 JSON trong `scripts/project-data/` + `python scripts/new-project.py <slug>` + `npm run build`.
- `inc/site/`: PHP chỉ dành riêng site này (admin, tools importer, customizer, migration một lần).

## Quy ước naming (legacy-frozen)

3 prefix cùng tồn tại — KHÔNG mass-rename (churn risk trên site sống):
- `sgh_` — canonical, dùng cho MỌI code mới.
- `sh_` — legacy public API (sh_get_asset_version...), giữ nguyên.
- `saigonhouse_` — legacy setup (saigonhouse_get_contact_info được schema/leads/chatbot gọi), KHÔNG đổi tên.

## Thư viện & third-party

- **jQuery: KHÔNG load ở front-end** (đã xác nhận 07/2026 — mọi enqueue deps=[]; comment-reply
  của core không kéo jQuery). Đừng thêm dependency jQuery mới.
- GTM: lazy qua `gtm-lazy.js` (consent + interaction + timeout 3.5s) — chuẩn, giữ.
- YouTube/iframe third-party: bắt buộc qua cơ chế `data-lazy-src` (main-modern.js, IO 350px).
- JS mới: vanilla ES6, minify qua esbuild vào `assets/js/dist/` (`npm run build`).

## Build & kiểm tra

| Lệnh | Việc |
|---|---|
| `npm run build` | build CSS (song song, incremental) + JS minify + lint |
| `npm run build -- --force` | build lại tất cả |
| `npm run perf` | LHCI gate 3 route chính (fail nếu perf < 0.99) |
| `npm run perf:all -- --sample` | Lighthouse sweep mẫu đại diện + đủ pillar → scoreboard |
| `node scripts/html-snapshot.mjs --label X --diff Y` | chụp + diff HTML mọi URL (refactor phải identical) |

## Scale sang project mới

1. Copy theme, đổi tên folder + `style.css` header.
2. Thay toàn bộ `inc/config/*` (brand, domains, routes, fonts, contact-info, site-structure).
3. Thay `assets/images/`, `manifest.json`, `scripts/project-data/*.json`.
4. `npm install && npm run build && npm run perf`.
Engine + máy móc perf + cổng Lighthouse đi theo miễn phí.
