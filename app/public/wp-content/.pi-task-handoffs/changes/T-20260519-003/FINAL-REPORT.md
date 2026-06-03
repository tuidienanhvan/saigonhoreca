# T-20260519-003 — Final Report

**Task**: saigonhoreca-data-migration (Phase 2: HTML scrape → WP posts/CPTs)
**Owner**: claude
**Period**: 2026-05-19 01:00 → 2026-05-19 02:35 (~95 min)
**State**: completed → ready for archive

---

## Acceptance criteria result

| # | Criterion | Result |
|---|---|---|
| 1 | Custom Post Types registered (`project`, `product`) | ✅ inc/core/cpt-registration.php |
| 2 | Taxonomies registered (project_category, product_category, product_brand) | ✅ same file |
| 3 | Scraped HTML parsed into structured JSON | ✅ 248 entities (128 product, 15 project, 63 post, 2 page, 40 term seeds) |
| 4 | Image files imported into wp_uploads | ✅ 1225 files (548 canonical + 677 size variants) |
| 5 | wp_posts (attachment) rows created with srcset metadata | ✅ 548/548 created |
| 6 | wp_posts (entity) rows created via wp_insert_post | ✅ 208/208 created (0 failures) |
| 7 | Featured images bound via _thumbnail_id | ✅ via attachment-map lookup |
| 8 | Categories + brands bound via wp_set_object_terms | ✅ 31 product_category terms + 9 product_brand terms auto-created |
| 9 | Gradual fallback: native posts shadow mirror automatically | ✅ verified — /san-pham/<sku>/ resolves native (X-SGH-Mirror absent), /san-pham/ archive still serves mirror |
| 10 | Front page rendered natively | ✅ page_on_front=761 ("home" page), show_on_front=page |
| 11 | Single-product + single-project templates render | ✅ HTTP 200, displays SKU/brand/categories |
| 12 | Sample URLs respond HTTP 200 | ✅ 16/16 sampled URLs pass |

---

## Phase-by-phase deliverables

### Phase A — CPTs + gradual fallback (25 min)

Files:
- `inc/core/cpt-registration.php` (NEW, 150 lines) — `project`+`product` CPTs, 3 taxonomies, rewrite slugs matching scraped URLs (`du-an`, `san-pham`, `danh-muc-du-an`, `danh-muc-san-pham`, `thuong-hieu`)
- `inc/core/static-mirror.php` (MODIFIED) — `sgh_mirror_has_native_match()` helper, yields to native WP when a published post exists for the URI
- `functions.php` (1 line add) — requires cpt-registration after static-mirror

### Phase B — HTML parser (30 min)

Scripts:
- `changes/T-20260519-003/scripts/parse-scrape.py` (270 lines) — walks 391 index.html files, classifies by path pattern, extracts title/excerpt/content/featured_image/categories/brand/SKU using BeautifulSoup4+lxml selectors discovered during structure survey

Output:
- `out/products.json` (128 entities)
- `out/projects.json` (15)
- `out/pages.json` (2)
- `out/posts.json` (63)
- `out/terms.json` (40 term seeds)
- `out/skippeds.json` (143 with reasons: amp-variant, paginated-archive, cpt-archive, skip-prefix)

Coverage: 128/129 products with categories (99%), 121/129 with brand (94%), 124/129 with SKU (96%), 128/129 with featured image (99%).

### Phase C — Image import (20 min)

Scripts:
- `scripts/import-images.py` — collects URLs from JSON entities, resolves canonical + size variants, copies files
- `scripts/import-attachments.php` — wp_insert_post for type=attachment, populates `_wp_attached_file` + `_wp_attachment_metadata`

Output:
- 1225 image files copied to `wp-content/uploads/<year>/<month>/`
- 548 wp_posts attachment rows
- 29 image URLs flagged as missing (not in scrape; will fall back gracefully)
- `out/image-manifest.json` + `out/attachment-map.json`

### Phase D — Post import (15 min)

Scripts:
- `scripts/import-posts.php` — wp_insert_post for products/projects/posts/pages, rewrites scraped URLs (`saigonhoreca.vn/wp-content/uploads/*` → local), sets featured image via attachment-map, creates taxonomy terms on-demand via wp_set_object_terms

Result:
- 208 wp_posts created
- 31 product_category terms auto-created
- 9 product_brand terms auto-created
- 0 failures

Permalink wiring:
- `permalink_structure` changed from `/%year%/%monthnum%/%day%/%postname%/` (saigonhouse style) to `/%postname%/` (saigonhoreca.vn style) — matches production canonical URLs
- `show_on_front=page` + `page_on_front=761` (home page)
- `page_for_posts=762` (tin-tuc page)
- Rewrites flushed

### Phase E — Templates (10 min)

Files:
- `single-product.php` (NEW) — SKU/brand/categories meta panel, schema.org/Product, related products query
- `single-project.php` (NEW) — case-study layout, no author box, random related projects

Both lint clean. Rendering verified via curl: SKU=FGTC30-45, brand-link, category-link all visible on imported product page.

### Phase F — Verification (10 min)

Sample-based smoke (5 random products + 5 random projects + 5 random posts + tin-tuc):
- 16/16 HTTP 200
- All native (no X-SGH-Mirror header)
- Mirror still serves: `/du-an/` archive, `/san-pham/` archive, `/danh-muc-san-pham/<slug>/` taxonomy archives (gradual fallback design)

---

## Migration state matrix

| URL pattern | Before T-003 | After T-003 |
|---|---|---|
| `/` (front page) | mirror HIT | native WP (home page) |
| `/du-an/` (CPT archive) | mirror HIT | mirror HIT (no archive-project.php yet) |
| `/du-an/<slug>/` | mirror HIT | **native WP** (CPT project) |
| `/san-pham/` (CPT archive) | mirror HIT | mirror HIT |
| `/san-pham/<sku>/` | mirror HIT | **native WP** (CPT product) |
| `/danh-muc-san-pham/<cat>/` | mirror HIT | mirror HIT |
| `/<post-slug>/` (blog posts) | mirror HIT | **native WP** (post) |
| `/tin-tuc/` | mirror HIT | **native WP** (page-for-posts) |
| `/wp-admin/` | WP admin | WP admin (now has 128 products + 15 projects + 63 posts editable) |

---

## Counts

WP-CLI verified:
```
$ wp post list --post_type=product --post_status=publish --format=count   →  128
$ wp post list --post_type=project --post_status=publish --format=count   →   15
$ wp post list --post_type=post    --post_status=publish --format=count   →   64
$ wp post list --post_type=attachment                    --format=count   →  548
$ wp term list product_category                          --format=count   →   31
$ wp term list product_brand                             --format=count   →    9
```

Total WP entities created in this task: **776** (208 content + 548 attachments + 40 taxonomy terms — minus overlaps for default WP "Hello world" sample post).

---

## Out-of-scope (logged for follow-up tasks)

- ❌ `archive-product.php` / `archive-project.php` / `taxonomy-product_category.php` — archive pages still mirror-served. Gradual fallback handles this; building templates is a UI/UX polish task, not a data-correctness blocker.
- ❌ 29 image URLs missing from scrape — listed in import-images output, gracefully ignored by attachment importer; affects content body image refs only (featured images all imported successfully).
- ❌ Blog post `bep-an-truong-mam-non-tu-thuc-trinh-vuong` and similar — these were classified as posts but production used them as project-like case studies. Manual reclassification possible in wp-admin.
- ❌ Author + user accounts — all imports run as user ID 1 (admin). Multi-author migration is a separate task.
- ❌ Comments — not migrated.
- ❌ Sitemap / SEO meta — relies on pi-seo plugin (saigonhouse uses it; not installed on saigonhoreca yet).
- ❌ Mirror retirement — keep static-mirror folder on disk for now; serves as fallback safety net + asset library until archive templates are built. Delete in a future task once user confirms native rendering is sufficient.

---

## Re-runnable architecture

Every phase script is idempotent:
- **Phase B** parser overwrites `out/*.json` on each run; no DB side-effects
- **Phase C** image copy skips already-present files, attachment importer reuses existing rows by `_wp_attached_file` lookup
- **Phase D** post importer uses `get_posts(name=slug)` to detect existing entities and falls back to `wp_update_post` instead of `wp_insert_post`

Run order (clean machine):
```
python scripts/parse-scrape.py                                            # Phase B
python scripts/import-images.py                                           # Phase C-1
wp eval-file scripts/import-attachments.php                               # Phase C-2
wp eval-file scripts/import-posts.php                                     # Phase D
wp option update permalink_structure '/%postname%/'                       # Phase D
wp option update show_on_front page; wp option update page_on_front <ID>  # Phase D
wp rewrite flush                                                          # Phase D
```

---

## Evidence trail

- `out/products.json`, `out/projects.json`, etc. — full JSON entities
- `out/image-manifest.json` — 548 entries with size_variants
- `out/attachment-map.json` — src_url → wp_posts.ID lookup
- `out/post-map.json` — post_type/slug → wp_posts.ID lookup
- `PHASE-A-REPORT.md` — Phase A detail with WP-CLI output
- Live verification:
  ```
  https://saigonhoreca.local/san-pham/bep-gas-khong-chan-fgtc30-45-fujimak-japan/
    → HTTP 200, no X-SGH-Mirror header, shows "SKU: FGTC30-45 | Thương hiệu: Fujimak-Japan"
  https://saigonhoreca.local/du-an/yuzu-omakase/
    → HTTP 200, no X-SGH-Mirror header, renders via single-project.php
  https://saigonhoreca.local/danh-muc-san-pham/thiet-bi-bep-cong-nghiep-sgh/
    → HTTP 200, X-SGH-Mirror: HIT (archive — gradual fallback by design)
  ```
