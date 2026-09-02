<?php
/**
 * CacheManager — Theme-agnostic edge HTML page cache for any WordPress site
 * running the Pi API plugin.
 *
 * DESIGN GOALS (why this lives in pi-api, not in a theme):
 *   1. ONE plugin, MANY themes. Any theme opts in via the `pi_api_cache_enabled`
 *      filter — no theme hardcodes cache logic. Replaces per-theme
 *      `sgh_static_cache_*` implementations (which drifted + caused stale bugs).
 *   2. VERSION-KEYED invalidation. The cache path embeds a theme-supplied
 *      version token (`pi_api_cache_version` filter). Bump the version when a
 *      template changes → old cache files are bypassed instantly (different
 *      path) WITHOUT a manual flush. This fixes the class of "edited a
 *      template file but the page still serves the old HTML" bugs, because
 *      `save_post` hooks never fire on a raw template-file edit.
 *   3. MULTI-SITE / MULTI-THEME buckets. Cache path is namespaced by host +
 *      version so two sites (or a theme + version bump) never collide.
 *   4. PI ECOSYSTEM SYNC. On flush, optionally tells pi-backend to purge the
 *      CDN edge (Cloudflare) so the local file cache and the CDN stay
 *      consistent. The pi-dashboard "Clear Cache" button calls the REST purge
 *      route which ends up here.
 *
 * LAYER NOTE: This is an *origin* page cache (serves before template render).
 * It is NOT a replacement for a CDN — it complements one. The fastest serve
 * path is still the optional `.htaccess` rule (Apache serves the file before
 * PHP boots); without it, this class serves the file at `plugins_loaded`
 * priority, skipping the ~200ms template render while still paying WP core
 * boot (~40ms). Both are far faster than a full uncached render.
 *
 * @package Pi\Api
 */

namespace PiApi\Core;

if (!defined('ABSPATH')) {
    exit;
}

class CacheManager {

    /** Option key: master on/off toggle (admin/dashboard controllable). */
    private const OPT_ENABLED = 'pi_api_cache_enabled';

    /** Option key: stored cache version (auto-bumped on flush events). */
    private const OPT_VERSION = 'pi_api_cache_version';

    /** Option key: running total of cached bytes on disk (approximate, incremental). */
    private const OPT_SIZE_BYTES = 'pi_api_cache_size_bytes';

    /** Default cap on total cache size — no TTL/eviction existed before, so this grew unbounded against the site's URL space. */
    private const DEFAULT_MAX_BYTES = 200 * 1024 * 1024; // 200MB

    /** Buffer must exceed this size to be cached (skip error/empty pages). */
    private const MIN_BYTES = 1024;

    /** Marker injected into cached HTML so we can confirm a cache hit. */
    private const SIGNATURE = '<!-- pi-api-cache -->';

    public static function init(): void {
        // Writer: capture rendered HTML at the end of template_redirect.
        add_action('template_redirect', [self::class, 'maybeStartBuffer'], 0);

        // Auto-flush triggers — content/structure changes invalidate cache.
        // 'comment_post' is handled separately below: it fires for EVERY
        // submitted comment, including ones held for moderation or marked
        // spam, so hooking flush() directly here let any anonymous visitor
        // (or a comment-spam bot) trigger a full, synchronous, site-wide
        // cache-tree delete + optional CDN purge on demand — an
        // unauthenticated DoS amplifier. Only an actually-approved comment
        // changes what a cached page looks like, so only that should flush.
        $flush_hooks = [
            'save_post', 'deleted_post', 'trashed_post',
            'switch_theme', 'wp_update_nav_menu', 'customize_save_after',
        ];
        foreach ($flush_hooks as $hook) {
            add_action($hook, [self::class, 'flush'], 10);
        }
        add_action('comment_post', [self::class, 'maybeFlushOnComment'], 10, 2);

        // Option changes that affect every URL → full flush.
        add_action('updated_option', [self::class, 'onOptionUpdated'], 10, 1);
    }

    // ───────────────────────────────────────────────────────────────
    // Configuration (all theme-overridable via filters)
    // ───────────────────────────────────────────────────────────────

    /**
     * Master switch. A theme enables caching with:
     *   add_filter('pi_api_cache_enabled', '__return_true');
     * Admin/dashboard can force-disable via the option (kill switch wins).
     */
    public static function isEnabled(): bool {
        // Kill switch: if option explicitly '0', stay off regardless of filter.
        if (get_option(self::OPT_ENABLED, '1') === '0') {
            return false;
        }
        /** @var bool $enabled */
        $enabled = apply_filters('pi_api_cache_enabled', false);
        return (bool) $enabled;
    }

    /**
     * Cache version token. Theme bumps this when templates change:
     *   add_filter('pi_api_cache_version', fn() => '2026.05.29');
     * Combined with the stored auto-bump counter so both manual (theme) and
     * automatic (flush event) invalidation work. Changing either → new path.
     */
    public static function version(): string {
        $theme_version = (string) apply_filters('pi_api_cache_version', '');
        $auto_version  = (string) get_option(self::OPT_VERSION, '1');
        $combined = $auto_version . ($theme_version !== '' ? '-' . $theme_version : '');
        // Keep it filesystem-safe.
        return preg_replace('#[^a-zA-Z0-9._-]#', '', $combined) ?: '1';
    }

    /** Root cache directory (plugin-owned, NOT theme-owned). */
    public static function cacheRoot(): string {
        /** @var string $root */
        $root = apply_filters('pi_api_cache_root', WP_CONTENT_DIR . '/cache/pi-api-html');
        return rtrim($root, '/');
    }

    /** Current site host, sanitized — namespaces the cache per site. */
    private static function hostBucket(): string {
        $host = (string) (wp_parse_url(home_url('/'), PHP_URL_HOST) ?: 'site');
        return preg_replace('#[^a-zA-Z0-9._-]#', '_', $host) ?: 'site';
    }

    /**
     * Map a request URI → absolute cache file path.
     * Path shape: <root>/<host>/<version>/<uri>/index.html
     * Returns '' if the URI is unsafe (cache-poisoning guard).
     */
    public static function pathForUri(string $uri): string {
        $uri = strtok($uri, '?');
        if ($uri === false) {
            $uri = '/';
        }
        $uri = trim($uri, '/');
        // Whitelist guard — REQUEST_URI is user-controlled.
        if ($uri !== '' && !preg_match('#^[a-zA-Z0-9/_-]+$#', $uri)) {
            return '';
        }
        $base = self::cacheRoot() . '/' . self::hostBucket() . '/' . self::version();
        return $base . ($uri === '' ? '/index.html' : '/' . $uri . '/index.html');
    }

    // ───────────────────────────────────────────────────────────────
    // Cacheability decision
    // ───────────────────────────────────────────────────────────────

    public static function isCacheable(): bool {
        if (!self::isEnabled()) {
            return false;
        }
        if ((defined('DOING_AJAX') && DOING_AJAX)
            || (defined('DOING_CRON') && DOING_CRON)
            || (defined('REST_REQUEST') && REST_REQUEST)
            || (defined('WP_CLI') && WP_CLI)
            || is_admin()) {
            return false;
        }
        if (!isset($_SERVER['REQUEST_METHOD']) || strtoupper((string) $_SERVER['REQUEST_METHOD']) !== 'GET') {
            return false;
        }
        if (!empty($_GET)) {
            return false; // dynamic intent (search, preview, pagination args)
        }
        if (is_user_logged_in()) {
            return false;
        }
        if (function_exists('is_preview') && is_preview()) {
            return false;
        }
        if (function_exists('is_customize_preview') && is_customize_preview()) {
            return false;
        }
        if (is_404() || is_search()) {
            return false;
        }
        // Sensitive cookies → personalized response, never cache.
        if (!empty($_SERVER['HTTP_COOKIE'])) {
            $cookie = (string) $_SERVER['HTTP_COOKIE'];
            foreach (['wordpress_logged_in_', 'wp-postpass_', 'comment_author_', 'woocommerce_items_in_cart', 'edd_items_in_cart'] as $needle) {
                if (stripos($cookie, $needle) !== false) {
                    return false;
                }
            }
        }
        // Theme/site can exclude specific URIs.
        $uri = isset($_SERVER['REQUEST_URI']) ? strtok((string) $_SERVER['REQUEST_URI'], '?') : '/';
        /** @var string[] $excluded */
        $excluded = (array) apply_filters('pi_api_cache_exclude', [], $uri);
        foreach ($excluded as $pattern) {
            if ($pattern !== '' && stripos($uri, (string) $pattern) !== false) {
                return false;
            }
        }
        /** @var bool $cacheable */
        $cacheable = apply_filters('pi_api_cache_is_cacheable', true, $uri);
        return (bool) $cacheable;
    }

    // ───────────────────────────────────────────────────────────────
    // Writer — capture rendered HTML and persist
    // ───────────────────────────────────────────────────────────────

    /** Max total cache size in bytes — theme-overridable via `pi_api_cache_max_bytes`. */
    public static function maxBytes(): int {
        return (int) apply_filters('pi_api_cache_max_bytes', self::DEFAULT_MAX_BYTES);
    }

    public static function maybeStartBuffer(): void {
        if (!self::isCacheable()) {
            return;
        }
        $uri  = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '/';
        $path = self::pathForUri($uri);
        if ($path === '') {
            return;
        }
        ob_start(static function ($buffer) use ($path) {
            // Only persist real, complete 200 responses.
            if (function_exists('http_response_code') && http_response_code() !== 200) {
                return $buffer;
            }
            if (strlen($buffer) < self::MIN_BYTES) {
                return $buffer;
            }
            if (stripos($buffer, '<title>Error') !== false) {
                return $buffer;
            }
            // Skip if a later filter already disabled caching for this request.
            if (!apply_filters('pi_api_cache_persist', true, $path)) {
                return $buffer;
            }
            // No TTL/eviction previously existed — the cache directory grew
            // unbounded against the site's real URL space. Stop writing new
            // entries once the (approximate, incrementally-tracked) total
            // exceeds the cap; existing entries are still served until the
            // next flush() resets the counter.
            if ((int) get_option(self::OPT_SIZE_BYTES, 0) >= self::maxBytes()) {
                return $buffer;
            }
            $dir = dirname($path);
            if (!is_dir($dir)) {
                @mkdir($dir, 0755, true);
            }
            $stamped = $buffer . "\n" . self::SIGNATURE . ' ' . gmdate('c') . " -->";
            $written = @file_put_contents($path, $stamped, LOCK_EX);
            if ($written !== false) {
                $running = (int) get_option(self::OPT_SIZE_BYTES, 0);
                update_option(self::OPT_SIZE_BYTES, $running + (int) $written, false);
            }
            return $buffer;
        });
    }

    // ───────────────────────────────────────────────────────────────
    // Early serve helper (used by advanced-cache.php drop-in OR a manual
    // plugins_loaded hook). Returns true if it served + exited.
    // ───────────────────────────────────────────────────────────────

    /**
     * Attempt to serve a cached file for the current request as early as
     * possible. Safe to call before full WP is loaded IF given a precomputed
     * path; otherwise relies on WP helpers.
     */
    public static function maybeServe(): bool {
        if (!self::isCacheable()) {
            return false;
        }
        $uri  = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '/';
        $path = self::pathForUri($uri);
        if ($path === '' || !is_readable($path)) {
            return false;
        }
        // Respect a TTL if the theme set one (0 = no expiry, flush-driven).
        $ttl = (int) apply_filters('pi_api_cache_ttl', 0, $uri);
        if ($ttl > 0 && (time() - (int) @filemtime($path)) > $ttl) {
            return false; // stale by TTL → let WP regenerate
        }
        $html = @file_get_contents($path);
        if ($html === false || $html === '') {
            return false;
        }
        header('Content-Type: text/html; charset=UTF-8');
        header('X-Pi-Cache: HIT');
        header('Cache-Control: public, max-age=0, must-revalidate');
        echo $html;
        return true;
    }

    // ───────────────────────────────────────────────────────────────
    // Flush / invalidation
    // ───────────────────────────────────────────────────────────────

    /**
     * comment_post fires for every submission regardless of moderation
     * status ($comment_approved: 1 = approved, 0 = held, 'spam' = spam).
     * Only flush when a comment actually became visible on the cached page.
     */
    public static function maybeFlushOnComment($comment_id, $comment_approved): void {
        if ($comment_approved === 1 || $comment_approved === '1') {
            self::flush();
        }
    }

    public static function onOptionUpdated($option): void {
        $structural = [
            'siteurl', 'home', 'blogname', 'blogdescription',
            'permalink_structure', 'page_on_front', 'page_for_posts',
            'template', 'stylesheet',
        ];
        if (in_array($option, $structural, true)) {
            self::flush();
        }
    }

    /**
     * Flush ALL cached HTML. Implementation = bump the auto-version (so every
     * cache path changes → instant logical invalidation) + best-effort delete
     * of the on-disk files. Bumping first means even if the rm fails, stale
     * files are never served again.
     */
    public static function flush(): void {
        // 1. Logical invalidation — bump version (cheap, atomic, always works).
        $current = (int) get_option(self::OPT_VERSION, '1');
        update_option(self::OPT_VERSION, (string) ($current + 1), false);

        // 2. Physical cleanup — delete old files (best-effort, non-fatal).
        self::deleteDir(self::cacheRoot());

        // 2b. Reset the running size counter — every on-disk file above is gone.
        update_option(self::OPT_SIZE_BYTES, 0, false);

        // 3. Refresh the sidecar meta so the advanced-cache.php drop-in reads
        //    the NEW version on the very next request (otherwise it would serve
        //    files under the old version path until WP next writes meta).
        self::writeMeta();

        // 4. Pi Ecosystem sync — purge CDN edge so it doesn't serve stale HTML.
        self::purgeRemoteCdn();

        do_action('pi_api_cache_flushed');
    }

    // ───────────────────────────────────────────────────────────────
    // Drop-in management (advanced-cache.php early-serve)
    // ───────────────────────────────────────────────────────────────

    /**
     * Write the sidecar meta file the advanced-cache.php drop-in reads BEFORE
     * WP is loaded. Keeps version/host/ttl in sync so the early-serve path
     * always points at the current cache bucket.
     */
    public static function writeMeta(): void {
        $dir = self::cacheRoot();
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        $meta = [
            'version' => self::version(),
            'host'    => self::hostBucket(),
            'ttl'     => (int) apply_filters('pi_api_cache_ttl', 0, '/'),
        ];
        $php = "<?php\nreturn " . var_export($meta, true) . ";\n";
        @file_put_contents($dir . '/.meta.php', $php, LOCK_EX);
    }

    /**
     * Install the advanced-cache.php drop-in + ensure WP_CACHE is set.
     * Called when caching is enabled (admin toggle / dashboard).
     * Returns a status array for the caller to surface to the user.
     *
     * @return array{ok:bool,steps:string[],warnings:string[]}
     */
    public static function installDropin(): array {
        $steps = [];
        $warnings = [];

        $src = PI_API_DIR . 'includes/cache/advanced-cache.php';
        $dst = WP_CONTENT_DIR . '/advanced-cache.php';

        // 1. Copy drop-in (only overwrite if it's ours or absent).
        if (is_readable($src)) {
            $existing = is_readable($dst) ? (string) @file_get_contents($dst) : '';
            if ($existing === '' || strpos($existing, 'Pi API — advanced-cache.php') !== false) {
                if (@copy($src, $dst)) {
                    $steps[] = 'Copied advanced-cache.php drop-in.';
                } else {
                    $warnings[] = 'Could not write advanced-cache.php (permissions?). Early-serve disabled; falling back to plugins_loaded serve.';
                }
            } else {
                $warnings[] = 'A non-Pi advanced-cache.php already exists — left untouched. Another cache plugin may be active.';
            }
        }

        // 2. Seed meta sidecar.
        self::writeMeta();
        $steps[] = 'Wrote cache meta sidecar.';

        // 3. WP_CACHE constant — needed for WP to load advanced-cache.php.
        if (!defined('WP_CACHE') || !WP_CACHE) {
            $warnings[] = 'WP_CACHE is not enabled. Add `define(\'WP_CACHE\', true);` to wp-config.php for fastest early-serve. (Plugin still serves at plugins_loaded without it.)';
        }

        return ['ok' => empty($warnings), 'steps' => $steps, 'warnings' => $warnings];
    }

    /** Remove the drop-in (only if it's ours). Called when caching disabled. */
    public static function uninstallDropin(): void {
        $dst = WP_CONTENT_DIR . '/advanced-cache.php';
        if (is_readable($dst)) {
            $content = (string) @file_get_contents($dst);
            if (strpos($content, 'Pi API — advanced-cache.php') !== false) {
                @unlink($dst);
            }
        }
    }

    /** Recursively delete cache files under a directory (keeps the root dir). */
    private static function deleteDir(string $dir): void {
        if (!is_dir($dir)) {
            return;
        }
        try {
            $it = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ($it as $f) {
                if ($f->isFile() || $f->isLink()) {
                    @unlink($f->getPathname());
                } elseif ($f->isDir()) {
                    @rmdir($f->getPathname());
                }
            }
        } catch (\Throwable $e) {
            // Non-fatal: logical version bump already invalidated the cache.
            error_log('[pi-api-cache] deleteDir failed: ' . $e->getMessage());
        }
    }

    /**
     * Tell pi-backend to purge the CDN edge for this site. Fire-and-forget —
     * never blocks the WP request. No-op if no license/JWT (BackendClient
     * handles auth). Wires the local cache to the Pi Ecosystem CDN layer
     * (pi_performance /cdn/purge → Cloudflare).
     */
    private static function purgeRemoteCdn(): void {
        if (!apply_filters('pi_api_cache_cdn_purge_enabled', false)) {
            return; // opt-in — off until a CDN is actually configured
        }
        if (!class_exists(BackendClient::class)) {
            return;
        }
        $jwt = '';
        if (class_exists(Settings::class) && method_exists(Settings::class, 'getJwt')) {
            $jwt = (string) Settings::getJwt();
        }
        // Purge the whole zone for this host. BackendClient must implement
        // cdnPurge(); guarded so this is a no-op until that ships.
        if (method_exists(BackendClient::class, 'cdnPurge')) {
            BackendClient::cdnPurge([home_url('/')], $jwt);
        }
    }

    // ───────────────────────────────────────────────────────────────
    // Introspection (for dashboard / REST status)
    // ───────────────────────────────────────────────────────────────

    /** @return array{enabled:bool,version:string,host:string,files:int,bytes:int,root:string} */
    public static function stats(): array {
        $root  = self::cacheRoot();
        $files = 0;
        $bytes = 0;
        if (is_dir($root)) {
            try {
                $it = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS)
                );
                foreach ($it as $f) {
                    if ($f->isFile()) {
                        $files++;
                        $bytes += (int) $f->getSize();
                    }
                }
            } catch (\Throwable $e) {
                // ignore — stats are best-effort
            }
        }
        return [
            'enabled' => self::isEnabled(),
            'version' => self::version(),
            'host'    => self::hostBucket(),
            'files'   => $files,
            'bytes'   => $bytes,
            'root'    => $root,
        ];
    }
}
