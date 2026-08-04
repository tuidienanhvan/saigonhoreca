<?php
/**
 * Pi API — Template Routes
 *
 * Phơi bản khai `sgh_get_template_routes()` của theme cho dashboard.
 *
 * VÌ SAO CẦN: dashboard liệt kê trang bằng WP_Query(post_type=page) nên chỉ thấy
 * bài CÓ THẬT trong wp_posts — 3 page. Các route do template dựng (/du-an/,
 * /san-pham/, /category/tin-tuc/, taxonomy...) không có bản ghi nào nên không
 * bao giờ hiện; người quản trị không biết chúng tồn tại.
 *
 * Endpoint này KHÔNG tự khai gì — chỉ đọc bản khai của theme. Theme nào không
 * cung cấp sgh_get_template_routes() thì trả mảng rỗng, không lỗi.
 *
 * @package PiApi
 */

if (!defined('ABSPATH')) exit;

class Pi_Api_Template_Routes {

    public static function register_routes($ns, $admin) {
        register_rest_route($ns, '/template-routes', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'get_all'],
            'permission_callback' => $admin,
        ]);
    }

    public static function get_all() {
        if (!function_exists('sgh_get_template_routes')) {
            return new \WP_REST_Response([
                'routes'  => [],
                'message' => 'Theme không cung cấp sgh_get_template_routes().',
            ], 200);
        }

        $theme_dir = get_template_directory();
        $out = [];

        foreach (sgh_get_template_routes() as $r) {
            $url = '';
            if (!empty($r['url']) && is_callable($r['url'])) {
                $url = (string) ($r['url'])();
            }

            $tpl      = (string) ($r['template'] ?? '');
            $tpl_path = $tpl ? $theme_dir . '/' . $tpl : '';
            // Báo rõ file template có thật không — bản khai trỏ file đã xoá là
            // lỗi âm thầm, dashboard nên thấy được ngay.
            $tpl_ok   = $tpl_path && file_exists($tpl_path);

            $store = (string) ($r['store'] ?? 'none');

            // Đếm số từ ở đúng chỗ chứa của từng loại route.
            $words = 0;
            $has_content = false;
            if ($store === 'post' && !empty($r['ref'])) {
                $p = get_page_by_path((string) $r['ref']);
                if ($p) {
                    $words = str_word_count(wp_strip_all_tags((string) $p->post_content));
                    $has_content = trim((string) $p->post_content) !== '';
                }
            } elseif ($store === 'term' && is_array($r['ref'] ?? null)) {
                [$tax, $slug] = $r['ref'];
                $t = get_term_by('slug', $slug, $tax);
                if ($t && !is_wp_error($t)) {
                    $words = str_word_count(wp_strip_all_tags((string) $t->description));
                    $has_content = trim((string) $t->description) !== '';
                }
            } elseif ($store === 'option' && !empty($r['ref']) && function_exists('sgh_archive_content_option')) {
                $v = (string) get_option(sgh_archive_content_option((string) $r['ref']), '');
                $words = str_word_count(wp_strip_all_tags($v));
                $has_content = trim($v) !== '';
            }

            $out[] = [
                'key'          => (string) ($r['key'] ?? ''),
                'label'        => (string) ($r['label'] ?? ''),
                'url'          => $url,
                'template'     => $tpl,
                'template_ok'  => $tpl_ok,
                'store'        => $store,
                'editable'     => $store === 'post',   // chỉ page mới mở được trong editor
                'has_content'  => $has_content,
                'words'        => (int) $words,
                'description'  => (string) ($r['desc'] ?? ''),
            ];
        }

        return new \WP_REST_Response(['routes' => $out, 'total' => count($out)], 200);
    }
}
