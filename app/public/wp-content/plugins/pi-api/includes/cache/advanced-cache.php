<?php
/**
 * Pi API — advanced-cache.php drop-in (TEMPLATE).
 *
 * This file is COPIED to wp-content/advanced-cache.php by
 * CacheManager::installDropin() when caching is enabled. WordPress loads
 * advanced-cache.php very early (in wp-settings.php, before most of core and
 * before any plugin) IF `define('WP_CACHE', true)` is in wp-config.php.
 *
 * It serves a cached HTML file at the earliest possible point — skipping the
 * full WP bootstrap + template render — giving near-static TTFB (~5-15ms)
 * without an Apache/.htaccess rule.
 *
 * Self-contained on purpose: at this stage NONE of WP's functions exist, so we
 * recompute the cache path with raw PHP using the same scheme as
 * CacheManager::pathForUri(). The version + host are read from a tiny sidecar
 * meta file the writer keeps in sync, so we never need WP loaded here.
 *
 * @package Pi\Api
 */

if (defined('ABSPATH')) {
    // Loaded too late / wrong context — bail to normal WP.
    return;
}

(static function (): void {
    // Only GET, no query string, no auth cookies — mirror isCacheable() basics.
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
        return;
    }
    if (!empty($_GET) || !empty($_SERVER['QUERY_STRING'])) {
        return;
    }
    if (!empty($_SERVER['HTTP_COOKIE'])) {
        $cookie = (string) $_SERVER['HTTP_COOKIE'];
        foreach (['wordpress_logged_in_', 'wp-postpass_', 'comment_author_', 'woocommerce_items_in_cart', 'edd_items_in_cart'] as $needle) {
            if (stripos($cookie, $needle) !== false) {
                return;
            }
        }
    }

    // WP_CONTENT_DIR isn't defined yet; derive from this file's location.
    // This file lives at: <wp-content>/advanced-cache.php
    $contentDir = dirname(__FILE__);
    $metaFile   = $contentDir . '/cache/pi-api-html/.meta.php';
    if (!is_readable($metaFile)) {
        return;
    }
    // Meta returns ['version' => '...', 'host' => '...', 'ttl' => 0].
    $meta = include $metaFile;
    if (!is_array($meta) || empty($meta['version']) || empty($meta['host'])) {
        return;
    }

    $uri = strtok((string) ($_SERVER['REQUEST_URI'] ?? '/'), '?');
    if ($uri === false) {
        $uri = '/';
    }
    $uri = trim($uri, '/');
    if ($uri !== '' && !preg_match('#^[a-zA-Z0-9/_-]+$#', $uri)) {
        return; // cache-poisoning guard
    }

    $base = $contentDir . '/cache/pi-api-html/' . $meta['host'] . '/' . $meta['version'];
    $path = $base . ($uri === '' ? '/index.html' : '/' . $uri . '/index.html');

    if (!is_readable($path)) {
        return;
    }

    // Optional TTL check.
    $ttl = (int) ($meta['ttl'] ?? 0);
    if ($ttl > 0 && (time() - (int) @filemtime($path)) > $ttl) {
        return;
    }

    $html = @file_get_contents($path);
    if ($html === false || $html === '') {
        return;
    }

    header('Content-Type: text/html; charset=UTF-8');
    header('X-Pi-Cache: HIT-EARLY');
    header('Cache-Control: public, max-age=0, must-revalidate');
    echo $html;
    exit;
})();
