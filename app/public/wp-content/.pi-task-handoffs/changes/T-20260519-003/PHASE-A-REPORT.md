# T-20260519-003 — Phase A Report

**Phase**: A — CPT registration + gradual-fallback wiring
**Period**: 2026-05-19 01:00 → 2026-05-19 01:25 (~25 min)
**State**: completed

---

## Deliverables

### 1. CPTs registered (zero-plugin, native WP)

`inc/core/cpt-registration.php` (NEW, 150 lines):

| post_type | label | rewrite slug | archive slug | hierarchical | Gutenberg/REST |
|---|---|---|---|---|---|
| `project` | Dự án | `du-an` | `du-an` | no | yes |
| `product` | Sản phẩm | `san-pham` | `san-pham` | no | yes |

| taxonomy | label | object_type | rewrite slug | hierarchical |
|---|---|---|---|---|
| `project_category` | Hạng mục dự án | `project` | `danh-muc-du-an` | yes |
| `product_category` | Danh mục sản phẩm | `product` | `danh-muc-san-pham` | yes |
| `product_brand` | Thương hiệu | `product` | `thuong-hieu` | no |

Rewrite slugs **match the scraped saigonhoreca.vn URL structure** so that
once posts are imported in Phase D they immediately serve at the same
permalinks (no redirects required, existing inbound links stay valid).

### 2. Gradual-fallback wired into static-mirror

`inc/core/static-mirror.php` (MODIFIED):
- Added `sgh_mirror_has_native_match($uri)` helper (~50 lines)
- `sgh_mirror_serve()` now checks for a published native WP entity BEFORE
  reading the scraped HTML file
- Lookup is scoped to `post`+`page` for top-level slugs, and `project`/`product`
  for `/du-an/<slug>/` and `/san-pham/<slug>/`
- Result cached per-request to avoid duplicate DB queries
- Mirror remains the default for archives, taxonomies, paginated lists
  during the migration window

Effect: each post imported via Phase D **shadows the static mirror automatically**.
No mirror-handler changes needed per import.

### 3. Theme loader wiring

`functions.php` line 248 (1-line add):
```php
require $inc . 'core/cpt-registration.php';  // CPT `project`+`product` + taxonomies (Phase 2 data layer)
```

---

## Verification

### WP-CLI post-type list (after flush)
```
name,label,...,public
post,Posts,...,1
page,Pages,...,1
project,"Dự án",...,1     ← NEW
product,"Sản phẩm",...,1  ← NEW
```

### WP-CLI taxonomy list
```
name,label,object_type,...,public
category,Categories,post,...,1
project_category,"Hạng mục dự án",project,...,1     ← NEW
product_category,"Danh mục sản phẩm",product,...,1   ← NEW
product_brand,"Thương hiệu",product,...,1            ← NEW
```

### Rewrite rules flushed
```
$ wp rewrite flush --hard
Success: Rewrite rules flushed.
```
(`.htaccess` warning is benign — DB-level rewrites are stored; LocalWP
serves via Apache without needing theme-write access to .htaccess.)

### Mirror smoke (4 URLs, GET with header dump)
```
https://saigonhoreca.local/                                              HTTP 200 X-SGH-Mirror: HIT
https://saigonhoreca.local/du-an/                                        HTTP 200 X-SGH-Mirror: HIT
https://saigonhoreca.local/san-pham/                                     HTTP 200 X-SGH-Mirror: HIT
https://saigonhoreca.local/danh-muc-san-pham/thiet-bi-bep-cong-nghiep-sgh/ HTTP 200 X-SGH-Mirror: HIT
```

All 4 routes still served by the scraped mirror because no native posts
exist yet → `sgh_mirror_has_native_match()` returns `false` → mirror handles
the request. As Phase D imports posts, individual URLs will switch to native
WP rendering one by one (visible via the absence of `X-SGH-Mirror: HIT`).

### Lint
```
$ php -l cpt-registration.php  → No syntax errors detected.
$ php -l static-mirror.php     → No syntax errors detected.
$ php -l functions.php         → No syntax errors detected.
```

---

## Out-of-scope (logged for next phase)

- Single-post templates `single-project.php`, `single-product.php` — Phase E
- Archive templates `archive-project.php`, `archive-product.php` — Phase E
- WP-admin menus visible at /wp-admin/edit.php?post_type=project — appear
  automatically since `show_in_menu=true` (default for `public=true`)
