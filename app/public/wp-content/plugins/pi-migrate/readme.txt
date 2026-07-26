=== Pi Migrate ===
Requires at least: 5.8
Tested up to: 6.6
Requires PHP: 7.4

Full-site migration (DB export/import, serialize-safe search-replace, uploads sync/zip)
plus a generalized product/catalog importer, shared across saigonhoreca and saigonhouse.

== Usage ==

WP-CLI:
	wp pi-migrate db-export --file=backup.sql
	wp pi-migrate db-import backup.sql
	wp pi-migrate search-replace https://old.com https://new.com --dry-run
	wp pi-migrate files-sync "C:\path\to\dest\uploads"
	wp pi-migrate catalog-import docs/product-catalog --publish
	wp pi-migrate catalog-import docs/product-catalog --brand=rational,vulcan
	wp pi-migrate catalog-export product --file=products.json
	wp pi-migrate site-export --output-dir=exports/2026-07-25
	wp pi-migrate site-import exports/2026-07-25 --new-url=https://saigonhouse.com

Admin UI: Tools -> Pi Migrate (same actions via REST, for when shell/WP-CLI access isn't available).

SEO fields round-trip automatically — no separate SEO import/export path exists because
none is needed: every `meta:*` CSV column (e.g. meta:_seo_title, meta:_seo_description,
meta:_og_image, meta:_source_url) is imported as post meta as-is, and catalog-export dumps
the full get_post_meta() for every post, so SEO meta is captured on export the same way.

Catalog import accepts:
  - a directory of brand subfolders each containing data.json (the saigonhoreca layout under
    docs/product-catalog/{brand}/data.json)
  - a single .json file (bare array, or wrapped in {"items":[...]} / {"products":[...]})
  - a single .csv file (header row + rows)

When the pi-api plugin is active, catalog import delegates to
Pi_Api_ImportExport::import_post_list() (slug-matched, idempotent update) — the same engine
saigonhoreca-theme's docs/product-catalog/import_via_pi.php already uses. Without pi-api, a
minimal built-in importer is used instead (no image sideload / advanced taxonomy shapes).

== Changelog ==

= 1.0.0 =
Initial release: db export/import, search-replace, uploads sync/zip, site package export/import,
generalized catalog importer, WP-CLI commands, admin UI + REST routes.
