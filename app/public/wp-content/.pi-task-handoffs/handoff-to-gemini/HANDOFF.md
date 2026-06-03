# 🤝 Handoff to Gemini — saigonhoreca-theme

**Project**: Clone `saigonhoreca.vn` → `saigonhoreca.local` WordPress theme
**Architecture**: Forked from `saigonhouse-theme`, same patterns (Tailwind v4, per-route bundles, static-mirror layer)
**Handoff date**: 2026-05-19
**Previous assistant**: Claude (Opus 4.7)
**Working directory**: `C:\Users\Administrator\Local Sites\saigonhoreca\app\public\wp-content\`

---

## 1. 🎯 User's goal (verbatim)

The user wants `saigonhoreca.local` to:

1. **Clone đúng giống `saigonhoreca.vn`** — pixel-perfect visual match với production. User chỉ trực tiếp:
   > "có giống đâu" (after comparing screenshots: my local vs saigonhoreca.vn header — clearly different)

2. **Cấu trúc thư mục giống `saigonhouse-theme`** — same directories, same template-parts pattern:
   > "cấu trúc thư mục phải giống saigonhouse-theme nha"
   > "các page khác cũng tương tự, nhớ nha, cấu trúc giống"

3. **Tailwind v4 cũng giống saigonhouse-theme** — same build pipeline:
   > "tailwind v4 cũng giống"

4. **PageSpeed 100/100 toàn site** — Lighthouse Performance 100 cho mọi route:
   > "để page rank 100% hết hiểu chưa"

5. **No Elementor / no Astra / zero-plugin** — tuyệt đối không xài Elementor markup, chỉ Tailwind utility classes:
   > "ko nhé, ko dùng plugin nào hết, như saigonhouse"

6. **Đọc HTML scraped để rebuild Tailwind** — không copy lại Elementor markup:
   > "bạn xem kĩ trên web đi, mình clone lại mà"
   > "đã lên web là có html, sao ko đọc được?"

7. **Mỗi page tách thành template-parts hợp lý** — kiểu saigonhouse front-page.php → loads 8 template-parts/home/*:
   > "clone từ saigonhoreca về, đọc rồi sau đó chia ra template part hợp lý"

8. **Data crawl sau** — categories/posts data sẽ làm sau, focus structure trước:
   > "dữ liệu thì sau tôi sẽ crawl về sau"

9. **Xóa hết nội dung tào lao saigonhouse** — không để lại bất kỳ string/template/asset nào của saigonhouse:
   > "xóa hết nội dung tào lao của bên saigonhouse"

---

## 2. ✅ What's been done (completed work)

### 2.1 Cấu trúc thư mục — đầy đủ song song saigonhouse-theme

```
saigonhoreca-theme/
├── 404.php · archive.php · category.php · comments.php · footer.php
├── front-page.php · functions.php · header.php · index.php · page.php
├── search.php · sidebar.php · single.php · tag.php
├── single-product.php · single-project.php          ← CPT templates (NEW)
├── archive-product.php · archive-project.php         ← CPT archives (NEW)
├── category-video-du-an.php                          ← inherited (dead, can delete)
├── inc/
│   ├── admin/ · config/ · core/ · customizer/        ← same as saigonhouse
│   ├── core/cpt-registration.php (NEW)               ← register `project` + `product` CPTs
│   └── core/static-mirror.php (NEW)                  ← serve scraped HTML for un-rebuilt routes
├── template-parts/
│   ├── home/      (13 sections, all Tailwind v4)    ← REBUILT
│   ├── about/     (6 sections, raw Elementor)       ← scaffolded, NOT rebuilt
│   ├── contact/   (3 sections, raw Elementor)       ← scaffolded, NOT rebuilt
│   ├── archive-product/   (4 parts, Tailwind)       ← Tailwind built
│   ├── archive-project/   (3 sections, raw Elem)    ← scaffolded, NOT rebuilt
│   ├── components/        ← post-card, share-buttons, faq, comments, wave-divider
│   ├── header/            ← logo, navigation, top-bar, mobile-menu
│   ├── footer/            ← company-info, contact-info, cookie, copyright, etc.
│   └── global-styles.{php,css}
├── page-templates/
│   ├── page-gioi-thieu.php  (orchestrate about/*)
│   └── page-lien-he.php     (orchestrate contact/*)
├── assets/
│   ├── css/
│   │   ├── style-{theme,home,about,contact,single,product,project,archive,archive-product,archive-project}.css   ← 10 entry points
│   │   ├── _imports-{home,about,contact,product,project,archive-product,archive-project,route-core,single,archive}.css
│   │   ├── critical.css     (~5 KB above-fold)
│   │   ├── critical-routes.css  (intentionally empty for now)
│   │   └── dist/theme-*.css   (10 compiled bundles, 95-188 KB each)
│   ├── images/
│   │   ├── logo.{webp,png} · logo-125x58.png
│   │   ├── favicon.ico · favicon-{16,32}x{16,32}.png
│   │   ├── android-chrome-{192,512}x{192,512}.png
│   │   ├── apple-touch-icon.png
│   │   ├── site.webmanifest (Saigon Horeca, theme #c1272d)
│   │   ├── facebook-icon.webp · youtube-icon.webp · zalo-icon.webp
│   │   └── placeholder.svg
│   ├── fonts/ (BeVietnamPro Bold/Medium/Regular .ttf)
│   └── js/ (calculator, contact-form, gallery, hero-carousel, main-modern, etc.)
└── static-mirror/   (225 MB — full saigonhoreca.vn production scrape)
    ├── 391 HTML pages   (every URL scraped)
    ├── 182 CSS files     (Elementor, Astra, Woo, plugin CSS)
    ├── 134 JS files
    ├── 1,694 images (JPG/PNG/WebP)
    └── 14 webfonts
```

### 2.2 Database state (WordPress imports)

Phase 2 data migration ran via 4 scripts (`.task-handoffs/changes/T-20260519-003/scripts/`):

| Entity | Count | Source |
|---|---|---|
| `post_type=product` (CPT) | 128 | `/san-pham/<sku>/` pages parsed |
| `post_type=project` (CPT) | 15 | `/du-an/<slug>/` pages parsed |
| `post_type=post` (blog) | 63+1 | `/<slug>/` blog articles |
| `post_type=page` | 2 | home, tin-tuc |
| `post_type=attachment` | 548 | All image files in production scrape |
| `product_category` terms | 31 | Extracted from product_cat-* body classes |
| `product_brand` terms | 9 | Extracted from product attributes table |
| `category` (blog) | 1 (Uncategorized) | **Parser didn't extract per-post categories** |

**Real saigonhoreca.vn has 6 blog categories** (found by parser, not assigned):
- `saigon-horeca` — Saigon Horeca
- `thiet-bi-bep-cong-nghiep` — Thiết bị Bếp Công Nghiệp
- `thiet-bi-bar-cafe` — Thiết bị Bar & Cafe
- `inox-kitchen-equipment` — Thiết bị Bếp Inox
- `thiet-bi-bep-theo-mo-hinh-kinh-doanh` — Thiết bị Bếp theo mô hình kinh doanh
- `tin-tuc` — Tin Tức

User explicitly said data crawl is for later — current Uncategorized state is OK.

### 2.3 Saigon House cleanup

| Removed/changed | Action |
|---|---|
| All saigonhouse-themed `template-parts/home/*` (villa-designs, work-process construction, etc.) | DELETED, replaced with 13 Tailwind sections |
| `template-parts/about/*` saigonhouse story | Replaced with scraped saigonhoreca content (raw Elementor) |
| `template-parts/pricing/*` (46 files for construction pricing) | DELETED entirely |
| `inc/chatbot-content/*` (saigonhouse construction skills) | DELETED entirely |
| `page-templates/page-don-gia-*` (saigonhouse pricing pages) | DELETED |
| `category-video-du-an.php` saigonhouse YouTube channel | Still on disk (DEAD code, can delete) |
| 145 `lh-*.report.{html,json,png}` Lighthouse files in wp-content/ | DELETED |
| 3 `sgh-perf-T020c-*.json` files | DELETED |
| Saigonhouse logo green gradient `#007d3d` in critical.css | Replaced with red `#c1272d` brand |
| All `'saigonhouse'` text domain → `'saigonhoreca'` | REPLACED 57+ occurrences |
| `inc/config/{constants,contact-info,site-structure}.php` | Updated with saigonhoreca data: phone `0901 304 365`, email `contact@saigonhoreca.com`, address `Số 40 Đường Số 6, Melosa Khang Điền` |
| `template-parts/global-styles.css` | Trimmed saigonhouse-only classes (`.sh-townhouse-card`, `.sh-diary-*`, `.sh-grid-1-1`, `.team-header-grid`, `.interior-hero-grid`) + replaced print footer text from "SAIGON HOUSE \| 0961 868 968" to "SAIGON HORECA \| 0901 304 365" |
| Favicons (6 files) | Regenerated from `static-mirror/wp-content/uploads/2023/11/SGH-logo-site-identity.png` (142×142 master) — different md5 from saigonhouse, verified |

### 2.4 Static-mirror infrastructure

`inc/core/static-mirror.php` — WP `template_redirect` priority 0 hook:
1. Reads `static-mirror/<slug>/index.html` if exists
2. Rewrites `https://saigonhoreca.vn/wp-content/*` → `https://saigonhoreca.local/wp-content/themes/saigonhoreca-theme/static-mirror/wp-content/*`
3. Echoes HTML + sets `X-SGH-Mirror: HIT` header
4. **Gradual fallback**: `sgh_mirror_has_native_match($uri)` — if WP has a published native post/page for that URL, yield to native render

Currently force-mirror these URLs (until rebuilt in Tailwind):
```php
$force_mirror_slugs = ['ve-saigon-horeca', 'lien-he', 'du-an', 'san-pham'];
```
Front page `/` — NATIVE (rebuilt in Tailwind).

### 2.5 Tailwind v4 build pipeline

10 per-route entry points (`package.json` build:* scripts):

| Bundle | Size | Used by route |
|---|---|---|
| `theme-home.css` | 114 KB | `/` (front-page.php) |
| `theme-about.css` | 109 KB | `/ve-saigon-horeca/` (page-gioi-thieu.php) |
| `theme-archive-project.css` | 117 KB | `/du-an/` archive |
| `theme-archive-product.css` | 123 KB | `/san-pham/`, taxonomy archives |
| `theme-contact.css` | 124 KB | `/lien-he/` |
| `theme-archive.css` | 130 KB | category/tag/search/404 |
| `theme-product.css` | 135 KB | `/san-pham/<sku>/` |
| `theme-project.css` | 135 KB | `/du-an/<slug>/` |
| `theme-single.css` | 140 KB | blog posts |
| `theme.css` | 188 KB | fallback (full bundle) |

Tailwind config: `@import "tailwindcss" source(none)` in `style.css` → explicit `_imports.css` controls which files get scanned. Per-route `_imports-{route}.css` files import only relevant component CSS.

Build commands:
```bash
cd saigonhoreca-theme/
npm run build           # builds all 10 bundles
npm run build:home      # individual route rebuild
```

### 2.6 Image-LCP architecture

All extracted hero sections have:
```html
<img fetchpriority="high" decoding="sync" loading="eager" ...>
```
All other images: `loading="lazy" decoding="async"`. Smush lazy-load pattern (`data-src` placeholder) was stripped at extract time so images load eagerly when above-fold.

---

## 3. 🚨 Current state — what's BROKEN / different from production

### 3.1 Header layout — DOES NOT MATCH PRODUCTION

**Production saigonhoreca.vn**:
- Logo + "SAIGON HORECA" wordmark visible top-left
- Full 5-item horizontal nav: `Về Saigon Horeca ▾ | Dự Án | Sản Phẩm | Tin Tức | Liên hệ`
- "English" pill button with UK flag top-right
- White/light background
- Floating red phone button bottom-right

**Local saigonhoreca.local**:
- Only "HORECA" text visible (tiny, top-left) — looks like logo image isn't loading OR alt text fallback
- NO nav menu visible
- "English" button visible top-right
- See screenshot user provided

**Likely cause**: 
- `template-parts/header/logo.php` uses `assets/images/logo.webp` — file exists but may be wrong size/format
- `template-parts/header/navigation.php` PHP is correct (5-item fallback nav) but `.sh-nav { display: none }` until `min-width: 1024px` in `critical.css`. Either CSS isn't loading or viewport detection issue.

### 3.2 Hero design — DOESN'T MATCH PRODUCTION

**Production**: Carousel of real kitchen photos (hood + stove), simple white text "TƯ VẤN THIẾT KẾ THI CÔNG", prev/next arrows on sides.

**Local**: Static image (`SGH-new-banner.jpg`) with heavy black gradient overlay + "THIẾT BỊ BẾP CÔNG NGHIỆP CAO CẤP CHO F&B" with "CAO CẤP" in red.

User feedback: "có giống đâu" — wants production-matching design.

### 3.3 Pages still on mirror (NOT rebuilt in Tailwind yet)

Listed in `static-mirror.php` `$force_mirror_slugs`:
- `/ve-saigon-horeca/` — mirror serves 323 KB + 40 stylesheets
- `/lien-he/` — mirror serves 276 KB + 36 stylesheets
- `/du-an/` — mirror serves 351 KB + 37 stylesheets
- `/san-pham/` — mirror serves 443 KB + 33 stylesheets

These work visually (pixel-perfect saigonhoreca.vn) but won't hit Lighthouse 100 due to stylesheet count.

### 3.4 Template-parts scaffolded but NOT Tailwind-rebuilt

These directories contain raw Elementor markup extracted from scrape — they NEED Tailwind rebuild like `/home/`:

```
template-parts/about/{hero, introduction, partners, values, testimonials, cta}.php
template-parts/contact/{hero, form, map}.php
template-parts/archive-project/{hero, trust, projects-grid}.php
template-parts/archive-product/*  ← already Tailwind, no rebuild needed
```

Currently `force_mirror_slugs` shields users from seeing these broken renders.

---

## 4. 📋 Pending tasks (what Gemini should do next)

### Priority 1 — Fix visible bugs

1. **Header logo + nav** rendering. Check:
   - Does `assets/images/logo.webp` open in browser? Should show SGH-2024-Logo wordmark
   - Does `theme-home.css` actually load? Check Network tab
   - Is `.sh-nav { display: flex }` applying at desktop width?
   - Critical CSS coverage — currently `critical.css` only has saigonhouse-themed `.sh-header__arch` etc.
2. **Hero redesign** — match production:
   - Build carousel with 3 slides (use scraped Elementor slide images from `static-mirror/wp-content/uploads/2025/05/SGH-new-banner.jpg` + 2 others)
   - Simple white text overlay, not heavy black gradient
   - Production text: "TƯ VẤN THIẾT KẾ THI CÔNG" — bigger, centered

### Priority 2 — Rebuild remaining pages in Tailwind v4

Apply the same pattern used for `template-parts/home/*` (13 sections converted from Elementor to Tailwind utilities) to:

1. **`/ve-saigon-horeca/`** → `template-parts/about/*`
   - 6 sections already scaffolded
   - Read `static-mirror/ve-saigon-horeca/index.html`, identify visual design, rewrite each section in Tailwind utility classes
2. **`/lien-he/`** → `template-parts/contact/*`
   - 3 sections scaffolded (hero, form, map)
   - Form needs working contact form (use WP nonce + AJAX to `/wp-admin/admin-ajax.php` action `sh_contact_form`)
   - Map: embed Google Maps iframe for address `Số 40 Đường Số 6, Melosa Khang Điền, Quận 9 (TP. Thủ Đức), HCM`
3. **`/du-an/`** → `archive-project.php` + `template-parts/archive-project/*`
   - 3 sections scaffolded (hero, trust, projects-grid)
   - Replace `projects-grid.php` with WP_Query over CPT `project` (15 imported posts) rendering Tailwind project cards
4. **`/san-pham/`** → `archive-product.php` already done in Tailwind ✅
   - But still in force_mirror because mirror's richer Elementor layout was preferred
   - Test native render, decide whether to keep mirror or flip to native

After each rebuild, remove the slug from `$force_mirror_slugs` in `inc/core/static-mirror.php`.

### Priority 3 — Lighthouse 100/100 verification

After all routes are native:
- Run Lighthouse audit for each route at LocalWP `saigonhoreca.local`
- Target: Performance 100, Accessibility 100, Best Practices 100, SEO 100
- If shortfalls: check critical CSS coverage, image LCP attrs, JS deferred status

### Priority 4 — Optional cleanup

- Delete `static-mirror/` (225 MB) once all routes are native
- Delete `template-parts/components/{before-after,faq-premium}.css` — saigonhouse-construction-only
- Delete `category-video-du-an.php` — saigonhouse YouTube channel template

---

## 5. 🏗️ Architecture rules (must follow)

### 5.1 Tailwind v4 pattern (saigonhouse-theme parity)

```css
/* style.css (full bundle entry) */
@import "tailwindcss" source(none);
@import "./_imports.css";

/* style-home.css (per-route entry) */
@import "./style-route-base.css";
@import "./_imports-home.css";

/* _imports-home.css */
@import "./_imports-route-core.css";   /* shared header/footer/nav CSS */
/* + home-specific component imports if any */
```

### 5.2 Template-part rebuild pattern

For each section extracted from production:
1. Read scraped HTML at `static-mirror/<slug>/index.html`
2. Identify visual design (layout, colors, typography, images)
3. Write `template-parts/<route>/<section>.php` with **pure Tailwind utility classes** — NO `.elementor-element-*`, NO `e-con`, NO `e-flex`
4. Reference images via `<?php echo get_template_directory_uri(); ?>/static-mirror/wp-content/uploads/...`
5. First image of page = `fetchpriority="high" decoding="sync" loading="eager"`. All other images = `loading="lazy" decoding="async"`
6. Dynamic content (CPT loops) when possible: featured-projects.php uses `WP_Query(['post_type' => 'project'])` with fallback to hardcoded production images
7. Lint via `php -l <file>`
8. Run `npm run build:<route>` to compile Tailwind bundle

### 5.3 Brand colors

```
Primary red:    #c1272d  (use Tailwind `red-600`, `red-700`)
Black:          slate-900
White:          white
Text body:      slate-600
Text headline:  slate-900
Background alt: slate-50
```

### 5.4 Static-mirror gradual fallback

Mirror handler yields to WP native when:
- `/` and `show_on_front === 'page'` (current state: yields → native homepage)
- URI matches a published WP post/page/CPT slug
- URI NOT in `$force_mirror_slugs` array

To "ship" a rebuilt route: remove its slug from `$force_mirror_slugs`.

---

## 6. 📁 Key files to read first

| File | Why |
|---|---|
| `wp-content/.task-handoffs/STATUS.md` | Project task board |
| `wp-content/.task-handoffs/archive/2026-05/T-20260519-003-*.md` | Phase 2 data-migration dossier |
| `wp-content/.task-handoffs/changes/T-20260519-003/FINAL-REPORT.md` | Data migration acceptance criteria + evidence |
| `themes/saigonhoreca-theme/functions.php` | Main loader, hooks, optimizations |
| `themes/saigonhoreca-theme/inc/core/static-mirror.php` | Mirror serving logic + gradual fallback |
| `themes/saigonhoreca-theme/inc/core/cpt-registration.php` | CPT + taxonomy registration |
| `themes/saigonhoreca-theme/inc/core/enqueue.php` | Per-route CSS bundle detection |
| `themes/saigonhoreca-theme/front-page.php` | Homepage orchestrator (loads 13 template-parts/home/*) |
| `themes/saigonhoreca-theme/template-parts/home/hero.php` | Reference Tailwind v4 pattern for sections |
| `themes/saigonhoreca-theme/static-mirror/index.html` | Production saigonhoreca.vn homepage scraped (398 KB, source of truth for design) |

---

## 7. 🛠️ How to run / test

### LocalWP setup
- Site name: `saigonhoreca.local`
- Path: `C:\Users\Administrator\Local Sites\saigonhoreca\app\public`
- DB host: `127.0.0.1:10011` (LocalWP-assigned, see runtime my.cnf)
- DB user/password: `root`/`root`
- Webserver: Apache (port 10010) behind nginx router

### WP-CLI (needs custom php.ini)
```powershell
$php = "C:\Users\Administrator\AppData\Local\Programs\Local\resources\extraResources\lightning-services\php-8.2.29+0\bin\win64\php.exe"
$wpcli = "C:\Users\Administrator\AppData\Local\Programs\Local\resources\extraResources\bin\wp-cli\wp-cli.phar"
$ini = "C:\Users\Administrator\AppData\Local\Temp\sgh-wpcli.ini"
$bootstrap = "C:\Users\Administrator\AppData\Local\Temp\sgh-dbhost.php"  # defines DB_HOST=127.0.0.1:10011
& $php -c $ini $wpcli --path="C:\...\saigonhoreca\app\public" --require=$bootstrap <command>
```

### Tailwind build
```bash
cd "C:\Users\Administrator\Local Sites\saigonhoreca\app\public\wp-content\themes\saigonhoreca-theme"
npm run build           # 10 bundles
# or per-route: npm run build:home, build:about, build:contact, etc.
```

### Clear static cache (after template change)
```bash
rm -rf "C:\Users\Administrator\Local Sites\saigonhoreca\app\public\wp-content\cache\sgh"
rm -rf "C:\Users\Administrator\Local Sites\saigonhoreca\app\public\wp-content\cache\sgh-static"
```

### Probe a URL
```bash
curl -k -s -m 15 -D - -o /tmp/page.html "https://saigonhoreca.local/"
# X-SGH-Mirror: HIT  →  mirror is serving
# (no header)        →  native WP render
```

---

## 8. ⚠️ Known traps

1. **`sgh_cache_static`** writes `wp-content/cache/sgh-static/<url>/index.html`. After template change, clear cache OR the browser sees stale HTML.
2. **`saigonhouse_auto_setup_front_page`** in `inc/core/auto-setup.php` was DISABLED (no-op stub) to prevent it from re-setting `show_on_front=page` + `page_on_front=761` on every admin_init. If you re-enable it, set the right values manually via wp-cli.
3. **Smush lazy-load pattern**: scraped HTML uses `data-src` + base64 SVG placeholder for `src`. Extraction scripts in `.task-handoffs/changes/T-20260519-003/scripts/` already strip this and restore real URLs. When extracting new sections, use the same `fix_lazyload_img()` function.
4. **PHP `wp eval-file`** wraps the script in `eval()` — `declare(strict_types=1)` will fatal. Use plain `<?php` opening only.
5. **Python on Windows console**: `cp1252` codec chokes on Vietnamese. Scripts must `sys.stdout.reconfigure(encoding='utf-8')` or pipe through `python -X utf8`.
6. **HTML files have CRLF on Windows**: If extracting URLs/slugs from Python output for curl loops, `tr -d '\r'` first.
7. **Mirror handler bails on `$_GET`**: `?nocache=X` query strings make the mirror yield to WP. Useful for forcing native render but means cache-busting via query string skips the mirror.

---

## 9. 📝 Final notes from previous assistant

- The hardest decision was **mirror vs native rebuild**. Mirror gives pixel-perfect saigonhoreca.vn but ~50/100 Lighthouse. Native rebuild gives 95-100 Lighthouse but takes design rebuild time per route.
- Homepage was rebuilt as proof-of-concept — 79 KB HTML + 1 stylesheet (down from 355 KB + 46 stylesheets via mirror).
- User was clear: data crawl is later, **structure + design fidelity is priority now**.
- The handed-off state has the homepage working but user explicitly said "có giống đâu" (it's not the same) — so the design needs another pass to truly match production, especially the **header** (logo + 5-item nav) and **hero** (carousel of kitchen photos, simpler typography).
- Lots of scaffolding exists (template-parts/about/, contact/, archive-project/ with raw Elementor) — Gemini should rebuild these in Tailwind v4 next, then remove from `$force_mirror_slugs`.

Good luck! 🚀
