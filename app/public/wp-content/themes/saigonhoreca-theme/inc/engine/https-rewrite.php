<?php
/**
 * Force HTTPS on every URL WordPress generates.
 *
 * `siteurl` and `home` options in the DB still hold `http://` because
 * the site was created over plain HTTP. Filtering them in PHP is safer
 * than a DB migration — every call to `home_url()`, `site_url()`,
 * `wp_get_attachment_url()` etc. picks up the HTTPS scheme without
 * needing a one-shot search-replace.
 *
 * T-020: required for Lighthouse Best Practices `is-on-https` audit.
 *
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;

$sgh_force_https_url = static function($url) {
    if (is_string($url) && strpos($url, 'http://') === 0) {
        return 'https://' . substr($url, 7);
    }
    return $url;
};
add_filter('option_siteurl', $sgh_force_https_url);
add_filter('option_home', $sgh_force_https_url);
add_filter('wp_get_attachment_url', $sgh_force_https_url);
add_filter('wp_get_attachment_image_src', static function($image) use ($sgh_force_https_url) {
    if (is_array($image) && !empty($image[0])) {
        $image[0] = $sgh_force_https_url($image[0]);
    }
    return $image;
});
add_filter('wp_calculate_image_srcset', static function($sources) use ($sgh_force_https_url) {
    if (is_array($sources)) {
        foreach ($sources as &$s) {
            if (isset($s['url'])) {
                $s['url'] = $sgh_force_https_url($s['url']);
            }
        }
    }
    return $sources;
});

/**
 * Final-output HTTPS rewrite. Catches any HTTP URLs that slip through
 * the option/attachment filters (menu hrefs, permalinks, post_content
 * with hardcoded URLs, etc.). Runs after the response body is fully
 * rendered but before it leaves the server.
 *
 * Hooked late (priority 999) on `template_redirect` to register an
 * `ob_start` callback that survives until WP closes the buffer.
 */
add_action('template_redirect', static function() {
    $is_https = is_ssl()
        || (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off')
        || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower((string) $_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https');

    if (!$is_https || is_admin() || wp_doing_ajax() || defined('REST_REQUEST')) {
        return;
    }
    ob_start(static function($buffer) {
        return str_replace('http://saigonhoreca.local', 'https://saigonhoreca.local', (string) $buffer);
    });
}, 999);
