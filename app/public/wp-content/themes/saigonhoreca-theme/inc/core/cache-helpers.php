<?php
/**
 * Centralized Cache Management
 *
 * Single place to flush all theme transients and caches (including Transient Cache and Static HTML Cache).
 * Replaces scattered wp_cache_flush() and delete_transient() calls.
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) exit;

/**
 * All transient keys used by the theme.
 *
 * @return string[] Transient keys (some use patterns with post IDs).
 */
function sgh_get_theme_transient_keys() {
    return [
        'sgh_dashboard_stats',
        'sgh_hero_image',
        'sgh_hero_image_url',
        'sh_youtube_videos_v2',
        'sh_sitemap_xml',
        'sh_sitemap_xml_gz',
    ];
}

/**
 * Flush all theme-specific transients, WP object cache, and Static HTML Cache.
 *
 * Call this instead of wp_cache_flush() + individual delete_transient() calls.
 *
 * @param bool $flush_wp_cache Also call wp_cache_flush() (default true).
 * @return int Number of transients actually deleted (0 if nothing was stored).
 */
function sgh_flush_theme_caches($flush_wp_cache = true) {
    $deleted = 0;
    foreach (sgh_get_theme_transient_keys() as $key) {
        // delete_transient returns true only if the transient existed + was deleted
        if (delete_transient($key)) {
            $deleted++;
        }
    }

    if ($flush_wp_cache) {
        wp_cache_flush();
    }

    // Flush Static HTML Cache
    sgh_static_cache_flush();

    return $deleted;
}

/**
 * Invalidate caches on content changes.
 * Hooked to save_post, delete_post, wp_update_nav_menu.
 */
function sgh_invalidate_content_cache($post_id = 0) {
    // Skip auto-saves and revisions
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if ($post_id && wp_is_post_revision($post_id)) return;

    delete_transient('sgh_dashboard_stats');
    delete_transient('sh_sitemap_xml');
    delete_transient('sh_sitemap_xml_gz');

    // Flush Static HTML Cache on any content change
    sgh_static_cache_flush();
}

add_action('save_post', 'sgh_invalidate_content_cache');
add_action('delete_post', 'sgh_invalidate_content_cache');
add_action('wp_update_nav_menu', function () {
    sgh_invalidate_content_cache(0);
});

/* ============================================================================
 * STATIC HTML CACHE SYSTEM
 * ============================================================================ */

if (!function_exists('sgh_static_cache_dir')) {
    /**
     * Get the directory path for static HTML cache.
     *
     * @return string
     */
    function sgh_static_cache_dir(): string {
        return WP_CONTENT_DIR . '/cache/sgh-static';
    }
}

if (!function_exists('sgh_static_cache_path_for_uri')) {
    /**
     * Get the physical file path for a given URI.
     *
     * @param string $uri
     * @return string
     */
    function sgh_static_cache_path_for_uri(string $uri): string {
        $uri = strtok($uri, '?');
        if ($uri === false) $uri = '/';
        $uri = trim($uri, '/');
        // Sanitize: only allow [a-zA-Z0-9/_-], reject everything else
        // (cache poisoning guard — REQUEST_URI is user-controlled).
        if ($uri !== '' && !preg_match('#^[a-zA-Z0-9/_-]+$#', $uri)) return '';
        return sgh_static_cache_dir() . ($uri === '' ? '/index.html' : '/' . $uri . '/index.html');
    }
}

if (!function_exists('sgh_static_cache_is_cacheable')) {
    /**
     * Check if the current request is cacheable.
     *
     * @return bool
     */
    function sgh_static_cache_is_cacheable(): bool {
        if (defined('DOING_AJAX') && DOING_AJAX) return false;
        if (defined('DOING_CRON') && DOING_CRON) return false;
        if (defined('REST_REQUEST') && REST_REQUEST) return false;
        if (defined('WP_CLI') && WP_CLI) return false;
        if (is_admin()) return false;
        if (!isset($_SERVER['REQUEST_METHOD']) || strtoupper((string) $_SERVER['REQUEST_METHOD']) !== 'GET') return false;
        if (!empty($_GET)) return false;
        if (is_user_logged_in()) return false;
        if (is_preview() || (function_exists('is_customize_preview') && is_customize_preview())) return false;
        if (!empty($_SERVER['HTTP_COOKIE'])) {
            $cookie = (string) $_SERVER['HTTP_COOKIE'];
            if (stripos($cookie, 'wordpress_logged_in_') !== false) return false;
            if (stripos($cookie, 'wp-postpass_') !== false) return false;
            if (stripos($cookie, 'comment_author_') !== false) return false;
        }
        return true;
    }
}

if (!function_exists('sgh_static_cache_flush')) {
    /**
     * Flush all static HTML cache files and directories.
     */
    function sgh_static_cache_flush(): void {
        $dir = sgh_static_cache_dir();
        if (!is_dir($dir)) return;
        try {
            $it = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ($it as $f) {
                if ($f->isFile()) {
                    @unlink($f->getPathname());
                } elseif ($f->isDir()) {
                    @rmdir($f->getPathname());
                }
            }
        } catch (\Throwable $e) {
            error_log('[SaigonHoreca] sgh_static_cache_flush failed: ' . $e->getMessage());
        }
    }
}

// Hook template_redirect to start output buffering for cache generation
add_action('template_redirect', static function() {
    if (!sgh_static_cache_is_cacheable()) return;
    $uri = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '/';
    $path = sgh_static_cache_path_for_uri($uri);
    if ($path === '') return;
    ob_start(static function($buffer) use ($path) {
        if (http_response_code() !== 200) return $buffer;
        if (strlen($buffer) < 1024) return $buffer;
        if (strpos($buffer, '<title>Error') !== false) return $buffer;
        $dir = dirname($path);
        if (!is_dir($dir)) @mkdir($dir, 0755, true);
        @file_put_contents($path, $buffer, LOCK_EX);
        return $buffer;
    });
}, 2);

// Hook additional actions to flush static cache
add_action('comment_post', 'sgh_static_cache_flush');
add_action('switch_theme', 'sgh_static_cache_flush');
add_action('updated_option', static function($option) {
    $watched_options = [
        'siteurl', 
        'home', 
        'blogname', 
        'blogdescription', 
        'permalink_structure', 
        'page_on_front', 
        'page_for_posts'
    ];
    if (in_array($option, $watched_options, true)) {
        sgh_static_cache_flush();
    }
});
