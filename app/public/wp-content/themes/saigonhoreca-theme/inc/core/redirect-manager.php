<?php
/**
 * D5: Redirect Manager — Frontend redirect handler
 * Reads sgh_redirects from wp_options and processes on template_redirect.
 */

if (!defined('ABSPATH')) exit;

add_action('template_redirect', function () {
    $redirects = get_option('sgh_redirects', []);
    if (empty($redirects) || !is_array($redirects)) return;

    $current_path = '/' . ltrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

    foreach ($redirects as $r) {
        if (empty($r['from']) || empty($r['to'])) continue;
        $from = '/' . ltrim($r['from'], '/');

        if ($current_path === $from) {
            $to   = $r['to'];
            $code = intval($r['type'] ?? 301);
            // Resolve relative → absolute
            if (strpos($to, 'http') !== 0) {
                $to = home_url(ltrim($to, '/'));
            }
            wp_redirect(esc_url_raw($to), $code);
            exit;
        }
    }
});
