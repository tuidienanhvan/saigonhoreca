<?php
/**
 * Static-mirror server.
 *
 * Maps the incoming request URL to a matching file inside
 * `static-mirror/<slug>/index.html` (full snapshot of saigonhoreca.vn
 * scraped via `saigonhoreca-clone/scrape.py`). Rewrites the absolute
 * production URLs to local equivalents so CSS / JS / images / inter-page
 * links all resolve against this site.
 *
 * Hooked at `template_redirect` priority 0 so it runs before the normal
 * WP query → template lookup. Misses fall through to WP's regular flow
 * (e.g. /wp-admin/, /wp-json/, posts that aren't mirrored yet).
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('sgh_mirror_serve')) {
    function sgh_mirror_serve() {
        // Bail on admin, AJAX, REST, CLI — only handle real public requests.
        if (is_admin()) return;
        if (defined('DOING_AJAX') && DOING_AJAX) return;
        if (defined('REST_REQUEST') && REST_REQUEST) return;
        if (defined('DOING_CRON') && DOING_CRON) return;
        if (defined('WP_CLI') && WP_CLI) return;
        if (!isset($_SERVER['REQUEST_METHOD']) || strtoupper($_SERVER['REQUEST_METHOD']) !== 'GET') return;
        // Note: logged-in users ALSO see the mirror — that's the point. The
        // mirror IS the site for now. wp-admin still works (skipped above)
        // so the user can manage content; the public front-end is the
        // scraped HTML until proper templates replace it.

        // Skip mirror for any URL with a query string. Reasons:
        //   - Search (?s=keyword)  — let WP run the real query
        //   - Post-ID URLs (?p=N)  — WP handles canonical redirect
        //   - Plugin/preview args  — pagination, filters, debug flags
        // The scraped HTML never has live query strings, so a query-string
        // request is always "do something dynamic" intent. Mirror only
        // owns canonical permalink URLs.
        if (!empty($_GET)) return;

        $uri = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
        $uri = '/' . trim((string) $uri, '/');
        if ($uri === '/') $uri = ''; // root → /static-mirror/index.html

        // Skip WP paths + asset extensions.
        $skip_prefixes = ['/wp-admin', '/wp-login', '/wp-json', '/wp-content', '/wp-includes', '/xmlrpc.php', '/feed'];
        foreach ($skip_prefixes as $p) {
            if ($uri === $p || strpos($uri . '/', $p . '/') === 0) return;
        }
        if (preg_match('/\\.(?:css|js|jpe?g|png|gif|webp|svg|ico|woff2?|ttf|otf|map|xml|json|txt|pdf)$/i', $uri)) {
            return;
        }

        // Phase 2 gradual fallback: if a NATIVE WP entity (post/page/CPT)
        // already owns this URL, let WP render it instead of the mirror.
        // This lets us migrate content one post at a time — newly imported
        // posts shadow the static HTML automatically.
        // (Defensive: only consult WP if the query has actually resolved.)
        if (function_exists('sgh_mirror_has_native_match') && sgh_mirror_has_native_match($uri)) {
            return;
        }

        // FORCE NATIVE slugs — đã rebuild archive template, không serve mirror
        // dù file `static-mirror/<slug>/index.html` còn tồn tại. Khi user
        // thấy URL này, để WP dùng archive-{cpt}.php từ theme.
        $force_native_slugs = [
            'san-pham',  // → archive-product.php (CPT product archive)
            'du-an',     // → archive-project.php (CPT project archive)
        ];
        $uri_trim = trim((string) $uri, '/');
        if (in_array($uri_trim, $force_native_slugs, true)) {
            return;
        }
        // Cũng force native cho paginated URLs (/san-pham/page/2/, /du-an/page/3/, …)
        // và child taxonomy URLs (/san-pham/danh-muc-san-pham/<slug>/) để mọi
        // page của archive đều dùng archive-{cpt}.php thay vì static HTML cũ.
        foreach ($force_native_slugs as $slug) {
            if (preg_match('#^' . preg_quote($slug, '#') . '(?:/page/\d+)?$#', $uri_trim)) {
                return;
            }
        }
        // Force native cho /danh-muc-san-pham/<slug>/ và /thuong-hieu/<slug>/
        // (+ paginated) — dùng taxonomy-product_*.php native templates.
        if (preg_match('#^(?:danh-muc-san-pham|thuong-hieu)/[a-z0-9-]+(?:/[a-z0-9-]+)*(?:/page/\d+)?$#', $uri_trim)) {
            return;
        }

        $mirror_root = get_template_directory() . '/static-mirror';
        $candidates = [
            $mirror_root . $uri . '/index.html',
            $mirror_root . $uri . '.html',
        ];

        foreach ($candidates as $file) {
            if (is_readable($file)) {
                $html = @file_get_contents($file);
                if ($html === false || $html === '') continue;

                $html = sgh_mirror_rewrite_urls($html);

                // Force 200 status — WP may have set 404 during query
                // resolution (no matching WP post for /du-an/, /san-pham/…
                // until we create custom post types). The mirror file IS
                // the canonical content for these URLs.
                status_header(200);
                nocache_headers(); // disable WP's 404 cache headers
                header('Content-Type: text/html; charset=UTF-8');
                header('X-SGH-Mirror: HIT');
                header('Cache-Control: public, max-age=0, must-revalidate');
                echo $html;
                exit;
            }
        }
        // Miss → let WP handle the URL normally.
    }
}

if (!function_exists('sgh_mirror_rewrite_urls')) {
    /**
     * Rewrite scraped absolute URLs onto this site:
     *   - wp-content/uploads/ -> smart resolution via sgh_img() so they load natively on prod
     *     and use local or fallback-to-prod on local development.
     *   - wp-content (excluding uploads) -> local static-mirror equivalent under this theme.
     *   - wp-includes -> local static-mirror equivalent under this theme.
     *   - Any other saigonhoreca.vn URLs -> local home.
     */
    function sgh_mirror_rewrite_urls($html) {
        $theme_uri = get_template_directory_uri();
        $mirror_uri = $theme_uri . '/static-mirror';

        // 1. Tối ưu hóa tải ảnh uploads: Gọi sgh_img() để phân giải thông minh.
        // Trên Prod: Tải ảnh từ thư mục uploads gốc.
        // Trên Local: Nếu có ảnh cục bộ thì dùng, nếu thiếu thì tự động trỏ về Production.
        $html = preg_replace_callback(
            '#https?://saigonhoreca\\.(?:vn|com)/wp-content/uploads/([^"\'\s>]+)#i',
            function($matches) {
                $path = $matches[1];
                if (function_exists('sgh_img')) {
                    return sgh_img($path);
                }
                return 'https://saigonhoreca.vn/wp-content/uploads/' . $path;
            },
            $html
        );

        // 2. wp-content (loại trừ thư mục uploads vì đã xử lý ở bước 1) -> trỏ vào static-mirror trong theme
        $html = preg_replace(
            '#https?://saigonhoreca\\.(?:vn|com)/wp-content/(?!uploads/)#i',
            $mirror_uri . '/wp-content/',
            $html
        );

        // 3. wp-includes -> trỏ vào static-mirror trong theme
        $html = preg_replace(
            '#https?://saigonhoreca\\.(?:vn|com)/wp-includes/#i',
            $mirror_uri . '/wp-includes/',
            $html
        );

        // 4. Các URL trang nội bộ khác -> trỏ về local home_url()
        $html = preg_replace(
            '#https?://saigonhoreca\\.(?:vn|com)/(?!wp-content/uploads/)#i',
            home_url('/'),
            $html
        );

        // Loại bỏ thẻ AMP discovery vì môi trường local/theme mới không dùng
        $html = preg_replace('#<link[^>]+rel=["\']amphtml["\'][^>]*>\\s*#i', '', $html);

        return $html;
    }
}

add_action('template_redirect', 'sgh_mirror_serve', 0);

if (!function_exists('sgh_mirror_stub_wc_store')) {
    /**
     * Stub for /wp-json/wc/store/v1* — the scraped Astra+Elementor pages
     * load WooCommerce block bundles (attribute-filter, product-list etc.)
     * which fetch this REST endpoint at runtime. Without the WooCommerce
     * plugin installed locally the route 404s and the block JS throws
     * "There is no route for the given namespace (/wc/store/v1)".
     *
     * Return a minimal valid JSON envelope so the block silently no-ops.
     */
    function sgh_mirror_stub_wc_store() {
        $uri = strtok($_SERVER['REQUEST_URI'] ?? '', '?');
        if (strpos($uri, '/wp-json/wc/store/') === false) return;

        status_header(200);
        header('Content-Type: application/json; charset=UTF-8');
        header('X-SGH-Mirror-Stub: wc-store');
        header('Cache-Control: public, max-age=300');
        // Empty WC cart / products shape; blocks treat it as no items.
        echo wp_json_encode(['items' => [], 'totals' => (object) [], 'extensions' => (object) []]);
        exit;
    }
}
add_action('rest_api_init', 'sgh_mirror_stub_wc_store', -100);
add_action('init', 'sgh_mirror_stub_wc_store', 0);

if (!function_exists('sgh_mirror_has_native_match')) {
    /**
     * Returns true if the given URI maps to an existing native WP entity
     * (post, page, or CPT) that the mirror should yield to.
     *
     * Strategy: look up the slug at the end of the URI in each known post
     * type. We check `post_status = 'publish'` so unpublished drafts don't
     * shadow the mirror snapshot (the snapshot is always production-published).
     *
     * URIs we recognize for now:
     *   /<slug>/                  → post or page
     *   /du-an/<slug>/            → CPT `project`
     *   /san-pham/<slug>/         → CPT `product`
     *
     * Anything else (category archives, author archives, paginated) is left
     * for the mirror to handle so we don't accidentally hide content during
     * the gradual migration window.
     *
     * Cheap lookup: single `get_posts()` call with name= filter, scoped to
     * relevant post types. Result is cached for the request lifetime.
     */
    function sgh_mirror_has_native_match($uri) {
        static $cache = [];
        if (isset($cache[$uri])) return $cache[$uri];

        $uri_trim = trim((string) $uri, '/');

        // ╔══════════════════════════════════════════════════════════════╗
        // ║  Approach B — section-by-section rebuild                     ║
        // ║                                                              ║
        // ║  Each route gets opted out of the mirror as soon as its      ║
        // ║  template-parts get rewritten in BEM .sh-* classes with      ║
        // ║  matching CSS pairs (saigonhouse pattern). The mirror serves ║
        // ║  the remaining un-rebuilt routes for production parity.      ║
        // ╚══════════════════════════════════════════════════════════════╝

        // Front page (/) — REBUILT: front-page.php orchestrates clean
        // template-parts/home/* with BEM classes + theme-home.css bundle.
        if ($uri_trim === '') {
            return $cache[$uri] = true;
        }

        // Pages + archives we have NOT yet rebuilt in pure Tailwind v4.
        // Add a slug here whenever a section is still Elementor-heavy and
        // needs the production CSS bundle to render correctly.
        $force_mirror_slugs = [
            'du-an/the-brix',
            // 've-saigon-horeca' — REBUILT
            // 'lien-he'          — REBUILT
            // 'du-an'            — REBUILT
            // 'san-pham'         — REBUILT
            // 'tin-tuc'          — REDIRECTED → /category/tin-tuc/ (xem dưới)
        ];
        if (in_array($uri_trim, $force_mirror_slugs, true)) {
            return $cache[$uri] = false;
        }

        // Force mirror for any URI under /danh-muc-du-an/ — visual layout
        // depends on Elementor CSS chưa rebuild.
        // /danh-muc-san-pham/ và /thuong-hieu/ ĐÃ rebuild → dùng native template
        // (taxonomy-product_category.php / taxonomy-product_brand.php).
        if (strpos($uri_trim, 'danh-muc-du-an/') === 0
            || strpos($uri_trim, 'tu-khoa-san-pham/') === 0
            || strpos($uri_trim, 'category/') === 0
            || strpos($uri_trim, 'tac-gia/') === 0
            || strpos($uri_trim, 'tag/') === 0) {
            return $cache[$uri] = false;
        }

        $segments = explode('/', $uri_trim);
        $post_types = [];
        $slug = '';

        if (count($segments) === 1) {
            // /<slug>/ → could be post or page
            $slug = $segments[0];
            $post_types = ['post', 'page'];
        } elseif (count($segments) === 2 && $segments[0] === 'du-an') {
            $slug = $segments[1];
            $post_types = ['project', 'page'];
        } elseif (count($segments) === 2 && $segments[0] === 'san-pham') {
            $slug = $segments[1];
            $post_types = ['product'];
        } else {
            return $cache[$uri] = false;
        }

        if ($slug === '') return $cache[$uri] = false;

        $found = get_posts([
            'name'           => $slug,
            'post_type'      => $post_types,
            'post_status'    => 'publish',
            'numberposts'    => 1,
            'fields'         => 'ids',
            'no_found_rows'  => true,
            'suppress_filters' => true,
        ]);

        return $cache[$uri] = !empty($found);
    }
}
