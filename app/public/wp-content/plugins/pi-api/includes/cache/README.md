# Pi API — Edge HTML Cache (theme-agnostic)

**One plugin, many themes.** Page-cache logic lives here in `pi-api`, not in any
theme. Replaces per-theme `sgh_static_cache_*` implementations (which drifted
and caused stale-page bugs like the Casa Maria incident).

## Architecture

```
Browser
   │
   ├─(fastest) advanced-cache.php drop-in ── serves cached HTML before WP boot (~5-15ms)
   │            requires define('WP_CACHE', true) in wp-config.php
   │
   └─(normal)  WP boot → template_redirect ── CacheManager writes HTML to disk
                                               on first render, serves on next

Cache path:  wp-content/cache/pi-api-html/<host>/<version>/<uri>/index.html
Sync to CDN: flush → pi-backend /cdn/purge → Cloudflare (opt-in)
Dashboard:   pi-dashboard → REST /pi/v1/cache/{status,purge,enable,disable}
```

## How a theme opts in

Add to the theme's `functions.php` (or any plugin):

```php
// 1. Enable caching for this site.
add_filter('pi_api_cache_enabled', '__return_true');

// 2. (CRITICAL) Bump version whenever templates change → instant invalidation
//    WITHOUT a manual flush. Use the theme version, a build hash, or a
//    deploy timestamp. This is what prevents stale-template bugs.
add_filter('pi_api_cache_version', fn() => wp_get_theme()->get('Version'));
```

That's the minimum. Everything else has sane defaults.

## Optional filters (all theme-overridable)

| Filter | Default | Purpose |
|---|---|---|
| `pi_api_cache_enabled` | `false` | Master opt-in |
| `pi_api_cache_version` | `''` | Theme version token → path namespace (bump = invalidate) |
| `pi_api_cache_ttl` | `0` (no expiry, flush-driven) | Seconds before a file is treated stale |
| `pi_api_cache_exclude` | `[]` | Array of URI substrings to never cache |
| `pi_api_cache_is_cacheable` | `true` | Final per-request veto (`($bool, $uri)`) |
| `pi_api_cache_root` | `wp-content/cache/pi-api-html` | Cache directory |
| `pi_api_cache_persist` | `true` | Last-chance veto inside the output buffer |
| `pi_api_cache_cdn_purge_enabled` | `false` | Call pi-backend to purge CDN on flush |

## Auto-invalidation triggers

Cache flushes (version bump + file delete) on: `save_post`, `deleted_post`,
`trashed_post`, `comment_post`, `switch_theme`, `wp_update_nav_menu`,
`customize_save_after`, and structural option changes (permalinks, home, etc).

**For template-file edits** (which fire none of those hooks), bump
`pi_api_cache_version` — that's the whole point of version-keying.

## Cacheability rules (a request is cached only if ALL true)

- Caching enabled (filter + not killed by option)
- `GET`, no query string
- Not admin / AJAX / REST / CRON / WP-CLI
- Not logged in, no preview, not 404/search
- No auth/commerce cookies (`wordpress_logged_in_`, `wp-postpass_`,
  `comment_author_`, `woocommerce_items_in_cart`, `edd_items_in_cart`)
- Not in `pi_api_cache_exclude`
- 200 response, ≥1KB, not an error page

## REST control (pi-dashboard)

| Method | Route | Action |
|---|---|---|
| GET | `/pi/v1/cache/status` | `{enabled, version, host, files, bytes, root}` |
| POST | `/pi/v1/cache/purge` | Flush all (bump version + delete + CDN purge) |
| POST | `/pi/v1/cache/enable` | Turn on + install advanced-cache.php drop-in |
| POST | `/pi/v1/cache/disable` | Turn off + remove drop-in + clear files |

All require admin permission (`Pi_Api_Controller::is_admin`).

## Fastest serve (optional, per-site)

For pre-WP-boot serving, add to `wp-config.php`:

```php
define('WP_CACHE', true);
```

Then enable via dashboard (or `POST /pi/v1/cache/enable`) — it copies
`advanced-cache.php` into `wp-content/` and keeps a `.meta.php` sidecar in sync.
Without `WP_CACHE`, caching still works (serve happens after WP core boot via
the writer's file), just ~30ms slower TTFB.

## Migrating a theme off its own cache (e.g. saigonhoreca-theme)

1. Remove the theme's `sgh_static_cache_*` functions + `template_redirect`
   writer + flush hooks from `functions.php`.
2. Remove the static-cache `RewriteRule` block from the theme/site `.htaccess`.
3. Install + activate `pi-api` on that site.
4. Add the two opt-in filters above to the theme.
5. Delete the old `wp-content/cache/sgh-static/` directory.

Result: identical behavior, centrally managed, version-keyed (no more stale
template bugs), CDN-purge-ready.
