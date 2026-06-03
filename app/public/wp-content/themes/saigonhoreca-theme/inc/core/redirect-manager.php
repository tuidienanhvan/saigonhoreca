<?php
/**
 * D5: Redirect Manager — Frontend redirect handler
 * Reads sgh_redirects from wp_options and processes on template_redirect.
 */

if (!defined('ABSPATH')) exit;

add_action('template_redirect', function () {
    // 1. Force HTTPS redirect (for Lighthouse Best Practices)
    if (
        !is_admin()
        && !wp_doing_ajax()
        && !defined('REST_REQUEST')
    ) {
        $is_https = is_ssl()
            || (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off')
            || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower((string) $_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https');

        if (!$is_https && !headers_sent()) {
            $uri = isset($_SERVER['REQUEST_URI']) ? wp_unslash($_SERVER['REQUEST_URI']) : '/';
            $host = isset($_SERVER['HTTP_HOST']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_HOST'])) : '';
            wp_safe_redirect('https://' . $host . $uri, 301);
            exit;
        }
    }

    // 2. Static path redirects (/saigon-admin, /mau-nha/)
    $req_uri = isset($_SERVER['REQUEST_URI']) ? sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])) : '';
    if (strpos($req_uri, '/saigon-admin') !== false) {
        wp_redirect(admin_url('admin.php?page=sgh-dashboard'));
        exit;
    }

    if (is_page('mau-nha')) {
        $term = get_term_by('slug', 'thiet-ke', 'category');
        if ($term) {
            wp_redirect(get_term_link($term), 301);
            exit;
        }
    }

    // 3. Smart Author Redirect (Redirect /author or /author/ to main author archive if 404)
    if (is_404()) {
        $request_uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
        $path = trim(parse_url($request_uri, PHP_URL_PATH), '/');
        if ($path === 'author') {
            $author_id = 1;
            $author_url = get_author_posts_url($author_id);
            if ($author_url) {
                wp_redirect($author_url, 301);
                exit;
            }
        }
    }

    // 4. Dynamic redirects configured from Dashboard
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
