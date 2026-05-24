<?php
/**
 * Upload image helper for theme templates.
 *
 * This helper no longer serves theme static-mirror assets. Images should come
 * from the current site's real uploads directory, with a production fallback
 * only when a local file is missing.
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) exit;

if (!defined('SGH_PROD_UPLOADS_BASE')) {
    define('SGH_PROD_UPLOADS_BASE', 'https://saigonhoreca.vn/wp-content/uploads');
}

if (!function_exists('sgh_is_local_static_mirror_enabled')) {
    function sgh_is_local_static_mirror_enabled() {
        if (defined('SGH_ENABLE_STATIC_MIRROR') && SGH_ENABLE_STATIC_MIRROR) {
            return true;
        }

        $host = $_SERVER['HTTP_HOST'] ?? '';
        return strpos($host, '.local') !== false
            || strpos($host, 'localhost') !== false
            || strpos($host, '127.0.0.1') !== false;
    }
}

if (!function_exists('sgh_uploads_base_dir')) {
    function sgh_uploads_base_dir() {
        $upload = wp_get_upload_dir();
        return wp_normalize_path((string) $upload['basedir']);
    }
}

if (!function_exists('sgh_uploads_base_url')) {
    function sgh_uploads_base_url() {
        $upload = wp_get_upload_dir();
        return rtrim((string) $upload['baseurl'], '/');
    }
}

if (!function_exists('sgh_img')) {
    /**
     * Return an image URL for a path relative to /wp-content/uploads/.
     *
     * Priority:
     * 1. Current site's uploads directory.
     * 2. Production uploads fallback.
     */
    function sgh_img($path) {
        $path = ltrim((string) $path, '/');

        $uploads_dir = sgh_uploads_base_dir();
        $uploads_url = sgh_uploads_base_url();
        $local_file = wp_normalize_path($uploads_dir . '/' . $path);

        $webp_swap = !empty($_SERVER['HTTP_ACCEPT'])
            && strpos((string) $_SERVER['HTTP_ACCEPT'], 'image/webp') !== false
            && preg_match('/\.(jpe?g|png)$/i', $path);

        if (file_exists($local_file)) {
            if ($webp_swap) {
                $webp_path = preg_replace('/\.(jpe?g|png)$/i', '.webp', $path);
                $webp_file = wp_normalize_path($uploads_dir . '/' . $webp_path);
                if ($webp_path && file_exists($webp_file)) {
                    return $uploads_url . '/' . $webp_path;
                }
            }

            return $uploads_url . '/' . $path;
        }

        // Tự động chuyển hướng về tên miền cũ saigonhoreca.com nếu là các tệp tin di sản từ 2020 hoặc 2021
        if (preg_match('#^(2020|2021)/#', $path)) {
            return 'https://saigonhoreca.com/wp-content/uploads/' . $path;
        }

        return SGH_PROD_UPLOADS_BASE . '/' . $path;
    }
}
