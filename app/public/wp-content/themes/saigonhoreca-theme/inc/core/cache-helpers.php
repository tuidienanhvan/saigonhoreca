<?php
/**
 * Centralized Cache Management
 *
 * Single place to flush all theme transients and caches.
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
 * Flush all theme-specific transients and WP object cache.
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
}

add_action('save_post', 'sgh_invalidate_content_cache');
add_action('delete_post', 'sgh_invalidate_content_cache');
add_action('wp_update_nav_menu', function () {
    sgh_invalidate_content_cache(0);
});
