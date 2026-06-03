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
        $url = rtrim((string) $upload['baseurl'], '/');
        
        // Tự động nâng cấp giao thức sang HTTPS để tránh lỗi Mixed Content chặn hiển thị ảnh
        if (is_ssl() || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) {
            $url = str_replace('http://', 'https://', $url);
        }
        return $url;
    }
}

if (!function_exists('sgh_img')) {
    /**
     * Return an image URL for a path relative to /wp-content/uploads/.
     *
     * Priority:
     * 1. Current site's uploads directory.
     * 2. No production fallback (full local).
     */
    function sgh_img($path) {
        $path = ltrim((string) $path, '/');

        $uploads_dir = sgh_uploads_base_dir();
        $uploads_url = sgh_uploads_base_url();
        $local_file = wp_normalize_path($uploads_dir . '/' . $path);

        $webp_swap = preg_match('/\.(jpe?g|png)$/i', $path);

        if ($webp_swap) {
            $webp_path = preg_replace('/\.(jpe?g|png)$/i', '.webp', $path);
            $webp_file = wp_normalize_path($uploads_dir . '/' . $webp_path);
            if ($webp_path && file_exists($webp_file)) {
                return $uploads_url . '/' . $webp_path;
            }
        }

        return $uploads_url . '/' . $path;
    }
}

if (!function_exists('sgh_get_project_thumbnail')) {
    /**
     * Retrieve a custom project thumbnail configured directly in the single-project template header comment.
     *
     * Example header comment:
     *   * Thumbnail: yuzu-omakase/yuzu-omakase-nghe-nhan-sushi-quay-bieu-dien.webp
     *
     * @param string $slug Project CPT post name/slug.
     * @return string Normalized full URL of the image or empty string if not configured.
     */
    function sgh_get_project_thumbnail($slug) {
        $file_path = get_template_directory() . '/single-project/' . $slug . '.php';
        if (file_exists($file_path)) {
            $data = get_file_data($file_path, [
                'thumbnail' => 'Thumbnail',
            ]);
            if (!empty($data['thumbnail'])) {
                return sgh_img(trim($data['thumbnail']));
            }
        }
        return '';
    }
}

if (!function_exists('sgh_get_project_meta')) {
    /**
     * Retrieve custom project metadata (e.g. Address, Title) configured directly in the single-project template header comment.
     *
     * @param string $slug Project CPT post name/slug.
     * @param string $key Header comment key (e.g. 'Address', 'Title').
     * @return string Trimming value or empty string.
     */
    function sgh_get_project_meta($slug, $key) {
        $file_path = get_template_directory() . '/single-project/' . $slug . '.php';
        if (file_exists($file_path)) {
            $data = get_file_data($file_path, [
                'val' => $key,
            ]);
            if (!empty($data['val'])) {
                return trim($data['val']);
            }
        }
        return '';
    }
}
