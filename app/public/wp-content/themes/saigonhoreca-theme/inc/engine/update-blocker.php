<?php
/**
 * Disable WP update checks (core/plugins/themes) — không chặn Plugin Install / Search API.
 *
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;

// 1. Return empty data for update transients (prevents stale check trigger)
add_filter('pre_site_transient_update_plugins', function() { return (object) ['last_checked' => time(), 'response' => [], 'translations' => [], 'no_update' => []]; });
add_filter('pre_site_transient_update_themes',  function() { return (object) ['last_checked' => time(), 'response' => [], 'translations' => [], 'no_update' => []]; });
add_filter('pre_site_transient_update_core',    function() { return (object) ['last_checked' => time(), 'version_checked' => get_bloginfo('version'), 'updates' => []]; });
// 2. Block CHỈ các endpoint update-check (không chặn plugin install / browse / download)
add_filter('pre_http_request', function($pre, $args, $url) {
    $block_patterns = [
        '/core/version-check/',      // Core update check
        '/plugins/update-check/',    // Plugin update check
        '/themes/update-check/',     // Theme update check
        '/core/credits/',            // Credits ping (not essential)
    ];
    foreach ($block_patterns as $needle) {
        if (stripos($url, $needle) !== false) {
            return ['response' => ['code' => 200], 'body' => ''];
        }
    }
    return $pre;
}, 1, 3);
// 3. Remove scheduled cron events at earliest hook
add_action('init', function() {
    remove_action('wp_version_check', 'wp_version_check');
    remove_action('wp_update_plugins', 'wp_update_plugins');
    remove_action('wp_update_themes', 'wp_update_themes');
    remove_action('wp_maybe_auto_update', 'wp_maybe_auto_update');
}, 1);
