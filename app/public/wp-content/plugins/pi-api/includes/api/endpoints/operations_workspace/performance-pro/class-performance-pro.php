<?php
/**
 * Pi API Performance PRO Endpoints
 *
 * Advanced performance control-plane used by the Hieu suat PRO React page.
 *
 * Routes (all under /pi/v1/performance-pro):
 *   GET|POST /cdn                           - Cloudflare / BunnyCDN / custom creds
 *   POST     /cdn/purge                     - Purge by URL list (or entire zone)
 *   GET|POST /critical-css                  - Global settings
 *   POST     /critical-css/generate?url=... - Regex-based above-fold CSS extraction
 *   GET      /webp/queue                    - List attachments + conversion status
 *   POST     /webp/convert-all              - Queue all JPG/PNG in batches of 20
 *   POST     /webp/convert/{id}             - Convert single attachment
 *   GET|POST /pwa                           - App manifest editor fields
 *   GET      /pwa/manifest                  - Public manifest.json (__return_true)
 *   GET|POST /nano-cache                    - TTL, exclude paths, bypass cookies
 *   POST     /nano-cache/flush              - Delete all nanocache_* transients
 *   GET      /nano-cache/stats              - hit/miss, total pages, bytes
 *   GET|POST /wp-cleanup                    - Toggle list: emoji, WLW, RSD, etc.
 *
 * Clone target: plugins/pi-performance/ + plugins/pi-performance-v2/
 *
 * NOTE: Critical CSS here uses a regex fallback (no headless browser).
 *       A production deployment should run a Puppeteer / Playwright microservice
 *       and post-process the generated CSS. Document limitation in UI.
 *
 * @package Pi_Api
 */

if (!defined('ABSPATH')) exit;

class Pi_Api_PerformancePro {

    // Option keys (one bucket per feature).
    const OPT_CDN          = 'pi_perf_cdn';
    const OPT_CRITICAL_CSS = 'pi_perf_critical_css';
    const OPT_WEBP         = 'pi_perf_webp';
    const OPT_PWA          = 'pi_perf_pwa';
    const OPT_NANO_CACHE   = 'pi_perf_nano_cache';
    const OPT_WP_CLEANUP   = 'pi_perf_wp_cleanup';

    // Per-URL critical CSS goes into its own option family: pi_perf_ccss_{md5}.
    const CCSS_URL_PREFIX  = 'pi_perf_ccss_url_';

    // Nano cache transient prefix (aligns with v1 SGH_Nano_Cache / v2 NanoCache).
    const NANO_CACHE_PREFIX = 'nanocache_';

    // WebP batch size - tune upward only if server has sufficient memory.
    const WEBP_BATCH_SIZE = 20;

    public static function register_routes($ns, $admin) {
        // CDN
        register_rest_route($ns, '/performance-pro/cdn', [
            ['methods' => 'GET',  'callback' => [self::class, 'get_cdn'],  'permission_callback' => $admin],
            ['methods' => 'POST', 'callback' => [self::class, 'post_cdn'], 'permission_callback' => $admin],
        ]);
        register_rest_route($ns, '/performance-pro/cdn/purge', [
            'methods'  => 'POST',
            'callback' => [self::class, 'cdn_purge'],
            'permission_callback' => $admin,
        ]);

        // Critical CSS
        register_rest_route($ns, '/performance-pro/critical-css', [
            ['methods' => 'GET',  'callback' => [self::class, 'get_critical_css'],  'permission_callback' => $admin],
            ['methods' => 'POST', 'callback' => [self::class, 'post_critical_css'], 'permission_callback' => $admin],
        ]);
        register_rest_route($ns, '/performance-pro/critical-css/generate', [
            'methods'  => 'POST',
            'callback' => [self::class, 'critical_css_generate'],
            'permission_callback' => $admin,
        ]);

        // WebP
        register_rest_route($ns, '/performance-pro/webp', [
            ['methods' => 'GET',  'callback' => [self::class, 'get_webp'],  'permission_callback' => $admin],
            ['methods' => 'POST', 'callback' => [self::class, 'post_webp'], 'permission_callback' => $admin],
        ]);
        register_rest_route($ns, '/performance-pro/webp/queue', [
            'methods'  => 'GET',
            'callback' => [self::class, 'webp_queue'],
            'permission_callback' => $admin,
        ]);
        register_rest_route($ns, '/performance-pro/webp/convert-all', [
            'methods'  => 'POST',
            'callback' => [self::class, 'webp_convert_all'],
            'permission_callback' => $admin,
        ]);
        register_rest_route($ns, '/performance-pro/webp/convert/(?P<id>\d+)', [
            'methods'  => 'POST',
            'callback' => [self::class, 'webp_convert_single'],
            'permission_callback' => $admin,
        ]);

        // PWA - GET|POST settings plus public manifest endpoint
        register_rest_route($ns, '/performance-pro/pwa', [
            ['methods' => 'GET',  'callback' => [self::class, 'get_pwa'],  'permission_callback' => $admin],
            ['methods' => 'POST', 'callback' => [self::class, 'post_pwa'], 'permission_callback' => $admin],
        ]);
        register_rest_route($ns, '/performance-pro/pwa/manifest', [
            'methods'  => 'GET',
            'callback' => [self::class, 'pwa_manifest'],
            'permission_callback' => '__return_true',
        ]);

        // Nano cache
        register_rest_route($ns, '/performance-pro/nano-cache', [
            ['methods' => 'GET',  'callback' => [self::class, 'get_nano_cache'],  'permission_callback' => $admin],
            ['methods' => 'POST', 'callback' => [self::class, 'post_nano_cache'], 'permission_callback' => $admin],
        ]);
        register_rest_route($ns, '/performance-pro/nano-cache/flush', [
            'methods'  => 'POST',
            'callback' => [self::class, 'nano_cache_flush'],
            'permission_callback' => $admin,
        ]);
        register_rest_route($ns, '/performance-pro/nano-cache/stats', [
            'methods'  => 'GET',
            'callback' => [self::class, 'nano_cache_stats'],
            'permission_callback' => $admin,
        ]);

        // WP Cleanup
        register_rest_route($ns, '/performance-pro/wp-cleanup', [
            ['methods' => 'GET',  'callback' => [self::class, 'get_wp_cleanup'],  'permission_callback' => $admin],
            ['methods' => 'POST', 'callback' => [self::class, 'post_wp_cleanup'], 'permission_callback' => $admin],
        ]);
    }

    // ------------------------------------------------------------------
    //  CDN
    // ------------------------------------------------------------------

    public static function get_cdn() {
        return new \WP_REST_Response(self::merge_cdn_defaults((array) get_option(self::OPT_CDN, [])), 200);
    }

    public static function post_cdn(\WP_REST_Request $request) {
        $body = (array) $request->get_json_params();
        $provider = in_array(($body['provider'] ?? ''), ['cloudflare', 'bunnycdn', 'custom'], true)
            ? $body['provider'] : 'cloudflare';

        $clean = [
            'provider'        => $provider,
            'api_key'         => sanitize_text_field((string) ($body['api_key']  ?? '')),
            'zone_id'         => sanitize_text_field((string) ($body['zone_id']  ?? '')),
            'hostname'        => sanitize_text_field((string) ($body['hostname'] ?? '')),
            'purge_on_update' => !empty($body['purge_on_update']),
        ];
        update_option(self::OPT_CDN, $clean, false);
        return new \WP_REST_Response(['success' => true, 'settings' => $clean], 200);
    }

    /**
     * Purge CDN cache. Empty urls array -> purge the entire zone.
     */
    public static function cdn_purge(\WP_REST_Request $request) {
        $body = (array) $request->get_json_params();
        $urls = isset($body['urls']) && is_array($body['urls'])
            ? array_values(array_filter(array_map('esc_url_raw', $body['urls'])))
            : [];

        $cfg = self::merge_cdn_defaults((array) get_option(self::OPT_CDN, []));
        if (empty($cfg['api_key'])) {
            return new \WP_REST_Response(['success' => false, 'message' => 'CDN chua cau hinh API key'], 400);
        }

        if ($cfg['provider'] === 'cloudflare') {
            if (empty($cfg['zone_id'])) {
                return new \WP_REST_Response(['success' => false, 'message' => 'Cloudflare can zone_id'], 400);
            }
            $endpoint = 'https://api.cloudflare.com/client/v4/zones/' . rawurlencode($cfg['zone_id']) . '/purge_cache';
            $payload  = empty($urls) ? ['purge_everything' => true] : ['files' => $urls];
            $response = wp_remote_post($endpoint, [
                'timeout' => 15,
                'headers' => [
                    'Authorization' => 'Bearer ' . $cfg['api_key'],
                    'Content-Type'  => 'application/json',
                ],
                'body'    => wp_json_encode($payload),
            ]);
        } elseif ($cfg['provider'] === 'bunnycdn') {
            if (empty($cfg['zone_id'])) {
                return new \WP_REST_Response(['success' => false, 'message' => 'BunnyCDN can zone_id'], 400);
            }
            if (empty($urls)) {
                $endpoint = 'https://api.bunny.net/pullzone/' . rawurlencode($cfg['zone_id']) . '/purgeCache';
                $response = wp_remote_post($endpoint, [
                    'timeout' => 15,
                    'headers' => ['AccessKey' => $cfg['api_key'], 'Content-Type' => 'application/json'],
                ]);
            } else {
                // BunnyCDN purges individual URLs one at a time.
                $ok = true; $last_msg = '';
                foreach ($urls as $url) {
                    $r = wp_remote_post('https://api.bunny.net/purge?url=' . rawurlencode($url), [
                        'timeout' => 15,
                        'headers' => ['AccessKey' => $cfg['api_key']],
                    ]);
                    if (is_wp_error($r)) { $ok = false; $last_msg = $r->get_error_message(); }
                    elseif ((int) wp_remote_retrieve_response_code($r) >= 400) { $ok = false; $last_msg = (string) wp_remote_retrieve_body($r); }
                }
                return new \WP_REST_Response([
                    'success' => $ok,
                    'message' => $ok ? 'BunnyCDN purge OK (' . count($urls) . ' URLs)' : 'BunnyCDN purge loi: ' . $last_msg,
                    'purged'  => count($urls),
                ], $ok ? 200 : 500);
            }
        } else {
            // Custom provider: just return purge intent (caller responsible).
            return new \WP_REST_Response([
                'success' => true,
                'message' => 'Custom provider - please wire up your own integration',
                'purged'  => count($urls),
            ], 200);
        }

        if (is_wp_error($response)) {
            return new \WP_REST_Response(['success' => false, 'message' => $response->get_error_message()], 500);
        }
        $code = (int) wp_remote_retrieve_response_code($response);
        $body_raw = (string) wp_remote_retrieve_body($response);
        $decoded  = json_decode($body_raw, true);
        if ($code >= 400) {
            return new \WP_REST_Response([
                'success' => false,
                'message' => is_array($decoded) ? wp_json_encode($decoded) : $body_raw,
                'status'  => $code,
            ], 500);
        }
        return new \WP_REST_Response([
            'success' => true,
            'message' => empty($urls) ? 'Da purge toan bo zone' : 'Da purge ' . count($urls) . ' URL',
            'purged'  => count($urls),
            'raw'     => $decoded,
        ], 200);
    }

    protected static function merge_cdn_defaults(array $opt): array {
        return array_merge([
            'provider'        => 'cloudflare',
            'api_key'         => '',
            'zone_id'         => '',
            'hostname'        => '',
            'purge_on_update' => false,
        ], $opt);
    }

    // ------------------------------------------------------------------
    //  Critical CSS
    // ------------------------------------------------------------------

    public static function get_critical_css() {
        return new \WP_REST_Response(self::merge_ccss_defaults((array) get_option(self::OPT_CRITICAL_CSS, [])), 200);
    }

    public static function post_critical_css(\WP_REST_Request $request) {
        $body = (array) $request->get_json_params();
        $current = self::merge_ccss_defaults((array) get_option(self::OPT_CRITICAL_CSS, []));
        $clean = [
            'enabled'         => !empty($body['enabled']),
            'per_url_cache'   => !empty($body['per_url_cache']),
            'generated_count' => (int) $current['generated_count'],
            'last_generated'  => (string) $current['last_generated'],
        ];
        update_option(self::OPT_CRITICAL_CSS, $clean, false);
        return new \WP_REST_Response(['success' => true, 'settings' => $clean], 200);
    }

    /**
     * Regex-based above-fold CSS extraction.
     *
     * Fetches the target URL, collects linked stylesheets + inline <style>,
     * then keeps declarations for selectors that look "above the fold":
     * header/nav/hero/logo/menu/banner/cta + universal resets.
     *
     * Limitation: no real layout awareness. A headless browser (Puppeteer)
     * would produce substantially better results. Callable from the UI with
     * a "Re-generate" button and visible size stat.
     */
    public static function critical_css_generate(\WP_REST_Request $request) {
        $url = esc_url_raw((string) ($request->get_param('url') ?: home_url('/')));
        if (empty($url)) {
            return new \WP_REST_Response(['success' => false, 'message' => 'Thieu URL'], 400);
        }

        // wp_safe_remote_get() (not wp_remote_get()) — rejects loopback/RFC1918/
        // link-local hosts (incl. cloud metadata IPs). This endpoint takes an
        // admin-supplied URL and fetches + reflects its content server-side, so
        // without this it's an authenticated SSRF primitive for internal
        // network reconnaissance.
        $response = wp_safe_remote_get($url, ['timeout' => 20, 'redirection' => 3]);
        if (is_wp_error($response)) {
            return new \WP_REST_Response(['success' => false, 'message' => $response->get_error_message()], 500);
        }
        $html = (string) wp_remote_retrieve_body($response);
        if ($html === '') {
            return new \WP_REST_Response(['success' => false, 'message' => 'Trang rong'], 500);
        }

        // Collect CSS sources.
        $css_blob = '';
        // Inline <style> blocks
        if (preg_match_all('#<style[^>]*>(.*?)</style>#is', $html, $m)) {
            $css_blob .= implode("\n", $m[1]);
        }
        // Linked stylesheets (rel=stylesheet)
        if (preg_match_all('#<link[^>]+rel=["\']stylesheet["\'][^>]*>#i', $html, $links)) {
            foreach ($links[0] as $link_tag) {
                if (preg_match('#href=["\']([^"\']+)["\']#i', $link_tag, $hm)) {
                    $css_url = $hm[1];
                    // Make absolute
                    if (strpos($css_url, '//') === 0)       $css_url = 'https:' . $css_url;
                    elseif (strpos($css_url, '/') === 0)    $css_url = home_url($css_url);
                    elseif (!preg_match('#^https?://#i', $css_url)) $css_url = trailingslashit($url) . $css_url;

                    $r = wp_safe_remote_get($css_url, ['timeout' => 10]);
                    if (!is_wp_error($r) && (int) wp_remote_retrieve_response_code($r) < 400) {
                        $css_blob .= "\n" . (string) wp_remote_retrieve_body($r);
                    }
                }
            }
        }
        if ($css_blob === '') {
            return new \WP_REST_Response(['success' => false, 'message' => 'Khong tim thay CSS'], 500);
        }

        // Strip comments then pull rules.
        $css_blob = (string) preg_replace('#/\*.*?\*/#s', '', $css_blob);

        // Above-fold selector patterns.
        $patterns = [
            '\*', 'html', 'body', ':root', '@font-face',
            'header', 'nav', 'menu', 'logo', 'hero', 'banner', 'cta', 'above-fold',
            'site-header', 'site-branding', 'main-navigation', 'topbar',
            'sgh-header', 'pi-header', 'wp-block-navigation',
            'h1', 'h2', 'h3',
        ];
        $pattern_re = '#(?:^|,)\s*([^{}]*(?:' . implode('|', array_map(function ($p) { return preg_quote($p, '#'); }, $patterns)) . ')[^{}]*)\s*\{([^}]+)\}#im';

        $critical = '';
        if (preg_match_all('#([^{}]+)\{([^}]+)\}#m', $css_blob, $rules, PREG_SET_ORDER)) {
            foreach ($rules as $rule) {
                $selector = trim($rule[1]);
                $declarations = trim($rule[2]);
                if ($selector === '' || $declarations === '') continue;
                // Only keep rules whose selector mentions an above-fold pattern.
                foreach ($patterns as $p) {
                    if (stripos($selector, $p) !== false) {
                        $critical .= $selector . '{' . $declarations . '}' . "\n";
                        break;
                    }
                }
            }
        }

        // Always inject basic resets as a safety net.
        if ($critical === '') {
            $critical = '*,*::before,*::after{box-sizing:border-box}body{margin:0;font-family:system-ui,sans-serif}';
        }

        // Minify light.
        $critical = (string) preg_replace('#\s+#', ' ', $critical);
        $critical = (string) preg_replace('#\s*([{}:;,])\s*#', '$1', $critical);

        // Persist per URL.
        $opt_key = self::CCSS_URL_PREFIX . md5($url);
        update_option($opt_key, $critical, false);

        // Update aggregate counters.
        $settings = self::merge_ccss_defaults((array) get_option(self::OPT_CRITICAL_CSS, []));
        $settings['generated_count'] = (int) $settings['generated_count'] + 1;
        $settings['last_generated']  = current_time('mysql');
        update_option(self::OPT_CRITICAL_CSS, $settings, false);

        return new \WP_REST_Response([
            'success' => true,
            'url'     => $url,
            'bytes'   => strlen($critical),
            'css'     => $critical,
            'note'    => 'Trich xuat dua tren regex - dung headless browser de co ket qua chinh xac hon',
        ], 200);
    }

    protected static function merge_ccss_defaults(array $opt): array {
        return array_merge([
            'enabled'         => false,
            'per_url_cache'   => true,
            'generated_count' => 0,
            'last_generated'  => '',
        ], $opt);
    }

    // ------------------------------------------------------------------
    //  WebP
    // ------------------------------------------------------------------

    public static function get_webp() {
        $opt = self::merge_webp_defaults((array) get_option(self::OPT_WEBP, []));
        $opt['can_convert']    = self::can_convert_webp();
        $opt['total_pending']  = self::count_pending_webp();
        return new \WP_REST_Response($opt, 200);
    }

    public static function post_webp(\WP_REST_Request $request) {
        $body = (array) $request->get_json_params();
        $current = self::merge_webp_defaults((array) get_option(self::OPT_WEBP, []));
        $quality = max(70, min(90, (int) ($body['quality'] ?? $current['quality'])));
        $clean = [
            'enabled'          => !empty($body['enabled']),
            'quality'          => $quality,
            'queue_status'     => (string) $current['queue_status'],
            'total_converted'  => (int) $current['total_converted'],
            'total_pending'    => self::count_pending_webp(),
        ];
        update_option(self::OPT_WEBP, $clean, false);
        return new \WP_REST_Response(['success' => true, 'settings' => $clean], 200);
    }

    public static function webp_queue(\WP_REST_Request $request) {
        $limit = max(1, min(200, (int) $request->get_param('limit') ?: 50));
        $offset = max(0, (int) $request->get_param('offset'));

        $query = new \WP_Query([
            'post_type'      => 'attachment',
            'post_status'    => 'inherit',
            'post_mime_type' => ['image/jpeg', 'image/png'],
            'posts_per_page' => $limit,
            'offset'         => $offset,
            'no_found_rows'  => false,
            'fields'         => 'ids',
        ]);

        $items = [];
        foreach ($query->posts as $id) {
            $file = get_attached_file($id);
            $webp = $file ? preg_replace('/\.(jpe?g|png)$/i', '.webp', $file) : '';
            $has_webp = $webp && file_exists($webp);
            $items[] = [
                'id'        => $id,
                'title'     => get_the_title($id),
                'mime'      => get_post_mime_type($id),
                'size'      => $file && file_exists($file) ? (int) filesize($file) : 0,
                'webp_size' => $has_webp ? (int) filesize($webp) : 0,
                'status'    => $has_webp ? 'converted' : 'pending',
                'url'       => wp_get_attachment_url($id),
            ];
        }

        return new \WP_REST_Response([
            'items'  => $items,
            'total'  => (int) $query->found_posts,
            'limit'  => $limit,
            'offset' => $offset,
            'can_convert' => self::can_convert_webp(),
        ], 200);
    }

    /**
     * Queue all JPG/PNG for conversion. Runs one batch synchronously then
     * schedules the rest via wp_schedule_single_event every 60s until done.
     */
    public static function webp_convert_all(\WP_REST_Request $request) {
        if (!self::can_convert_webp()) {
            return new \WP_REST_Response([
                'success' => false,
                'message' => 'Server thieu GD/imagewebp - can ImageMagick hoac GD co ho tro WebP',
            ], 500);
        }

        $opt = self::merge_webp_defaults((array) get_option(self::OPT_WEBP, []));
        $quality = (int) $opt['quality'];

        // Collect next batch of pending IDs.
        $pending = self::get_pending_webp_ids(self::WEBP_BATCH_SIZE);
        $result = ['ok' => 0, 'skipped' => 0, 'failed' => 0, 'processed' => []];
        foreach ($pending as $id) {
            $status = self::convert_attachment_to_webp((int) $id, $quality);
            $result[$status] = ($result[$status] ?? 0) + 1;
            $result['processed'][] = ['id' => (int) $id, 'status' => $status];
        }

        $opt['total_converted'] = (int) $opt['total_converted'] + $result['ok'];
        $opt['total_pending']   = self::count_pending_webp();
        $opt['queue_status']    = $opt['total_pending'] > 0 ? 'running' : 'done';
        update_option(self::OPT_WEBP, $opt, false);

        // If there is still work, schedule next batch.
        if ($opt['total_pending'] > 0 && !wp_next_scheduled('pi_perf_webp_batch_tick')) {
            wp_schedule_single_event(time() + 60, 'pi_perf_webp_batch_tick');
        }

        return new \WP_REST_Response([
            'success'   => true,
            'processed' => $result,
            'pending'   => (int) $opt['total_pending'],
            'status'    => $opt['queue_status'],
        ], 200);
    }

    public static function webp_convert_single(\WP_REST_Request $request) {
        if (!self::can_convert_webp()) {
            return new \WP_REST_Response([
                'success' => false,
                'message' => 'Server thieu GD/imagewebp',
            ], 500);
        }
        $id = (int) $request['id'];
        $opt = self::merge_webp_defaults((array) get_option(self::OPT_WEBP, []));
        $status = self::convert_attachment_to_webp($id, (int) $opt['quality']);

        if ($status === 'ok') {
            $opt['total_converted'] = (int) $opt['total_converted'] + 1;
            $opt['total_pending']   = self::count_pending_webp();
            update_option(self::OPT_WEBP, $opt, false);
        }

        return new \WP_REST_Response([
            'success' => $status === 'ok',
            'status'  => $status,
            'id'      => $id,
        ], $status === 'failed' ? 500 : 200);
    }

    protected static function merge_webp_defaults(array $opt): array {
        return array_merge([
            'enabled'          => false,
            'quality'          => 82,
            'queue_status'     => 'idle',
            'total_converted'  => 0,
            'total_pending'    => 0,
        ], $opt);
    }

    protected static function can_convert_webp(): bool {
        return function_exists('imagewebp')
            && function_exists('imagecreatefromjpeg')
            && function_exists('imagecreatefrompng');
    }

    protected static function get_pending_webp_ids(int $limit): array {
        $query = new \WP_Query([
            'post_type'      => 'attachment',
            'post_status'    => 'inherit',
            'post_mime_type' => ['image/jpeg', 'image/png'],
            'posts_per_page' => $limit,
            'fields'         => 'ids',
            'no_found_rows'  => true,
        ]);
        $pending = [];
        foreach ($query->posts as $id) {
            $file = get_attached_file($id);
            if (!$file) continue;
            $webp = preg_replace('/\.(jpe?g|png)$/i', '.webp', $file);
            if ($webp && !file_exists($webp)) {
                $pending[] = (int) $id;
                if (count($pending) >= $limit) break;
            }
        }
        return $pending;
    }

    protected static function count_pending_webp(): int {
        $query = new \WP_Query([
            'post_type'      => 'attachment',
            'post_status'    => 'inherit',
            'post_mime_type' => ['image/jpeg', 'image/png'],
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'no_found_rows'  => true,
        ]);
        $count = 0;
        foreach ($query->posts as $id) {
            $file = get_attached_file($id);
            if (!$file) continue;
            $webp = preg_replace('/\.(jpe?g|png)$/i', '.webp', $file);
            if ($webp && !file_exists($webp)) $count++;
        }
        return $count;
    }

    protected static function convert_attachment_to_webp(int $attachment_id, int $quality): string {
        $file = get_attached_file($attachment_id);
        if (!$file || !file_exists($file)) return 'failed';
        $mime = (string) get_post_mime_type($attachment_id);
        if (!in_array($mime, ['image/jpeg', 'image/png'], true)) return 'skipped';
        $webp = (string) preg_replace('/\.(jpe?g|png)$/i', '.webp', $file);
        if ($webp === '' || $webp === $file) return 'failed';
        if (file_exists($webp)) return 'skipped';

        // Prefer v2 helper, fall back to v1 helper, else run inline GD pipeline.
        if (is_callable(['PiPerformanceV2\Modules\WebPConvert', 'convertAttachment'])) {
            return \PiPerformanceV2\Modules\WebPConvert::convertAttachment($attachment_id);
        }
        if (function_exists('sgh_convert_single_attachment_webp')) {
            return \sgh_convert_single_attachment_webp($attachment_id);
        }

        $img = null;
        if ($mime === 'image/jpeg') {
            $img = @imagecreatefromjpeg($file);
        } elseif ($mime === 'image/png') {
            $img = @imagecreatefrompng($file);
            if ($img) {
                imagepalettetotruecolor($img);
                imagealphablending($img, true);
                imagesavealpha($img, true);
            }
        }
        if (!$img) return 'failed';
        $ok = @imagewebp($img, $webp, max(1, min(100, $quality)));
        @imagedestroy($img);
        return ($ok && file_exists($webp)) ? 'ok' : 'failed';
    }

    // ------------------------------------------------------------------
    //  PWA
    // ------------------------------------------------------------------

    public static function get_pwa() {
        return new \WP_REST_Response(self::merge_pwa_defaults((array) get_option(self::OPT_PWA, [])), 200);
    }

    public static function post_pwa(\WP_REST_Request $request) {
        $body = (array) $request->get_json_params();

        // Icons array sanitisation.
        $icons = [];
        if (isset($body['icons']) && is_array($body['icons'])) {
            foreach ($body['icons'] as $icon) {
                if (!is_array($icon) || empty($icon['src'])) continue;
                $icons[] = [
                    'src'   => esc_url_raw((string) $icon['src']),
                    'sizes' => sanitize_text_field((string) ($icon['sizes'] ?? '192x192')),
                    'type'  => sanitize_text_field((string) ($icon['type']  ?? 'image/png')),
                ];
            }
        }

        $clean = [
            'enabled'              => !empty($body['enabled']),
            'app_name'             => sanitize_text_field((string) ($body['app_name']   ?? get_bloginfo('name'))),
            'short_name'           => sanitize_text_field((string) ($body['short_name'] ?? '')),
            'theme_color'          => sanitize_hex_color((string) ($body['theme_color'] ?? '#007d3d')) ?: '#007d3d',
            'offline_fallback_url' => esc_url_raw((string) ($body['offline_fallback_url'] ?? '')),
            'icons'                => $icons,
        ];
        update_option(self::OPT_PWA, $clean, false);
        return new \WP_REST_Response(['success' => true, 'settings' => $clean], 200);
    }

    /**
     * Public manifest.json builder.
     */
    public static function pwa_manifest() {
        $cfg = self::merge_pwa_defaults((array) get_option(self::OPT_PWA, []));
        $manifest = [
            'name'             => $cfg['app_name'] ?: get_bloginfo('name'),
            'short_name'       => $cfg['short_name'] ?: wp_trim_words((string) get_bloginfo('name'), 2, ''),
            'start_url'        => home_url('/'),
            'display'          => 'standalone',
            'background_color' => '#ffffff',
            'theme_color'      => $cfg['theme_color'] ?: '#007d3d',
            'icons'            => !empty($cfg['icons']) ? $cfg['icons'] : [
                ['src' => home_url('/wp-content/uploads/site-icon-192.png'), 'sizes' => '192x192', 'type' => 'image/png'],
                ['src' => home_url('/wp-content/uploads/site-icon-512.png'), 'sizes' => '512x512', 'type' => 'image/png'],
            ],
        ];
        if (!empty($cfg['offline_fallback_url'])) {
            $manifest['offline_fallback_url'] = $cfg['offline_fallback_url'];
        }
        $response = new \WP_REST_Response($manifest, 200);
        $response->header('Content-Type', 'application/manifest+json');
        return $response;
    }

    protected static function merge_pwa_defaults(array $opt): array {
        return array_merge([
            'enabled'              => false,
            'app_name'             => (string) get_bloginfo('name'),
            'short_name'           => '',
            'theme_color'          => '#007d3d',
            'offline_fallback_url' => '',
            'icons'                => [],
        ], $opt);
    }

    // ------------------------------------------------------------------
    //  Nano Cache
    // ------------------------------------------------------------------

    public static function get_nano_cache() {
        return new \WP_REST_Response(self::merge_nano_defaults((array) get_option(self::OPT_NANO_CACHE, [])), 200);
    }

    public static function post_nano_cache(\WP_REST_Request $request) {
        $body = (array) $request->get_json_params();
        $current = self::merge_nano_defaults((array) get_option(self::OPT_NANO_CACHE, []));

        $exclude = isset($body['exclude_paths']) && is_array($body['exclude_paths'])
            ? array_values(array_filter(array_map('sanitize_text_field', $body['exclude_paths'])))
            : $current['exclude_paths'];
        $bypass = isset($body['bypass_cookies']) && is_array($body['bypass_cookies'])
            ? array_values(array_filter(array_map('sanitize_key', $body['bypass_cookies'])))
            : $current['bypass_cookies'];

        $clean = [
            'enabled'        => !empty($body['enabled']),
            'ttl_seconds'    => max(60, min(604800, (int) ($body['ttl_seconds'] ?? $current['ttl_seconds']))),
            'exclude_paths'  => $exclude,
            'bypass_cookies' => $bypass,
            'hit_count'      => (int) $current['hit_count'],
            'miss_count'     => (int) $current['miss_count'],
        ];
        update_option(self::OPT_NANO_CACHE, $clean, false);
        return new \WP_REST_Response(['success' => true, 'settings' => $clean], 200);
    }

    public static function nano_cache_flush() {
        global $wpdb;
        // Cover all known nanocache naming schemes (theme's SGH, plugin v2, and our new prefix).
        $prefixes = [
            '_transient_' . self::NANO_CACHE_PREFIX,
            '_transient_timeout_' . self::NANO_CACHE_PREFIX,
            '_transient_sgh_html_',
            '_transient_timeout_sgh_html_',
            '_transient_pi_perf_',
            '_transient_timeout_pi_perf_',
        ];
        $total = 0;
        foreach ($prefixes as $prefix) {
            $total += (int) $wpdb->query(
                $wpdb->prepare(
                    "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
                    $prefix . '%'
                )
            );
        }
        if (function_exists('wp_cache_flush')) wp_cache_flush();
        // Also call class flushers if present (keeps counters consistent).
        if (class_exists('SGH_Nano_Cache')) {
            try { (new \SGH_Nano_Cache())->flush_all_nano_cache(); } catch (\Throwable $e) {}
        }
        return new \WP_REST_Response([
            'success' => true,
            'message' => 'Da xoa ' . $total . ' ban ghi nanocache',
            'removed' => $total,
        ], 200);
    }

    public static function nano_cache_stats() {
        global $wpdb;
        $opt = self::merge_nano_defaults((array) get_option(self::OPT_NANO_CACHE, []));
        $row = $wpdb->get_row(
            "SELECT COUNT(*) AS cnt, SUM(LENGTH(option_value)) AS bytes
             FROM {$wpdb->options}
             WHERE option_name LIKE '\\_transient\\_nanocache\\_%'
                OR option_name LIKE '\\_transient\\_sgh\\_html\\_%'
                OR option_name LIKE '\\_transient\\_pi\\_perf\\_%'",
            ARRAY_A
        );
        return new \WP_REST_Response([
            'hit_count'          => (int) $opt['hit_count'],
            'miss_count'         => (int) $opt['miss_count'],
            'total_cached_pages' => $row ? (int) $row['cnt']   : 0,
            'cache_size_bytes'   => $row ? (int) $row['bytes'] : 0,
        ], 200);
    }

    protected static function merge_nano_defaults(array $opt): array {
        return array_merge([
            'enabled'        => false,
            'ttl_seconds'    => 7200,
            'exclude_paths'  => ['/cart', '/checkout', '/my-account'],
            'bypass_cookies' => ['wordpress_logged_in', 'woocommerce_cart_hash'],
            'hit_count'      => 0,
            'miss_count'     => 0,
        ], $opt);
    }

    // ------------------------------------------------------------------
    //  WP Cleanup
    // ------------------------------------------------------------------

    public static function get_wp_cleanup() {
        return new \WP_REST_Response(self::merge_cleanup_defaults((array) get_option(self::OPT_WP_CLEANUP, [])), 200);
    }

    public static function post_wp_cleanup(\WP_REST_Request $request) {
        $body = (array) $request->get_json_params();
        $current = self::merge_cleanup_defaults((array) get_option(self::OPT_WP_CLEANUP, []));
        $clean = [
            'remove_emoji'        => !empty($body['remove_emoji']),
            'remove_wlw'          => !empty($body['remove_wlw']),
            'remove_rsd'          => !empty($body['remove_rsd']),
            'remove_shortlinks'   => !empty($body['remove_shortlinks']),
            'limit_revisions'     => max(0, min(50, (int) ($body['limit_revisions'] ?? $current['limit_revisions']))),
            'autosave_interval'   => max(10, min(3600, (int) ($body['autosave_interval'] ?? $current['autosave_interval']))),
            'disable_embeds'      => !empty($body['disable_embeds']),
        ];
        update_option(self::OPT_WP_CLEANUP, $clean, false);
        return new \WP_REST_Response(['success' => true, 'settings' => $clean], 200);
    }

    protected static function merge_cleanup_defaults(array $opt): array {
        return array_merge([
            'remove_emoji'      => true,
            'remove_wlw'        => true,
            'remove_rsd'        => true,
            'remove_shortlinks' => true,
            'limit_revisions'   => 5,
            'autosave_interval' => 120,
            'disable_embeds'    => true,
        ], $opt);
    }

    // ------------------------------------------------------------------
    //  Runtime hooks - applied on `init`
    // ------------------------------------------------------------------

    /**
     * Apply the WP Cleanup toggles stored in OPT_WP_CLEANUP.
     * This lives as a static method and is wired once from the API controller.
     */
    public static function apply_wp_cleanup_hooks() {
        $cfg = self::merge_cleanup_defaults((array) get_option(self::OPT_WP_CLEANUP, []));

        if (!empty($cfg['remove_emoji'])) {
            remove_action('wp_head',            'print_emoji_detection_script', 7);
            remove_action('admin_print_scripts','print_emoji_detection_script');
            remove_action('wp_print_styles',    'print_emoji_styles');
            remove_action('admin_print_styles', 'print_emoji_styles');
            remove_filter('the_content_feed',   'wp_staticize_emoji');
            remove_filter('comment_text_rss',   'wp_staticize_emoji');
            remove_filter('wp_mail',            'wp_staticize_emoji_for_email');
        }
        if (!empty($cfg['remove_wlw'])) {
            remove_action('wp_head', 'wlwmanifest_link');
        }
        if (!empty($cfg['remove_rsd'])) {
            remove_action('wp_head', 'rsd_link');
        }
        if (!empty($cfg['remove_shortlinks'])) {
            remove_action('wp_head',           'wp_shortlink_wp_head');
            remove_action('template_redirect', 'wp_shortlink_header', 11);
        }
        if (!empty($cfg['disable_embeds'])) {
            remove_action('wp_head',            'wp_oembed_add_discovery_links');
            remove_action('wp_head',            'wp_oembed_add_host_js');
            remove_action('rest_api_init',      'wp_oembed_register_route');
            add_action('wp_footer', function () { wp_deregister_script('wp-embed'); });
        }

        // Revisions + autosave tuning (cant be reconfigured once set per request, but still respect options).
        if (!defined('WP_POST_REVISIONS') && isset($cfg['limit_revisions'])) {
            define('WP_POST_REVISIONS', (int) $cfg['limit_revisions']);
        }
        if (!defined('AUTOSAVE_INTERVAL') && isset($cfg['autosave_interval'])) {
            define('AUTOSAVE_INTERVAL', (int) $cfg['autosave_interval']);
        }
    }
}

// Wire the WP Cleanup toggles as early as possible.
add_action('init', ['Pi_Api_PerformancePro', 'apply_wp_cleanup_hooks'], 0);

// Background WebP batch tick - processes the next batch whenever the scheduler fires.
add_action('pi_perf_webp_batch_tick', function () {
    if (!class_exists('Pi_Api_PerformancePro')) return;
    $request = new \WP_REST_Request('POST', '/pi/v1/performance-pro/webp/convert-all');
    \Pi_Api_PerformancePro::webp_convert_all($request);
});
