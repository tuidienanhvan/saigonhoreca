<?php
if (!defined('ABSPATH')) exit;

/**
 * Mặt REST cho chuyển ngữ chuyên mục. Tầng vận chuyển thuần — nghiệp vụ nằm ở
 * Pi_Multilang_Term, cùng quy ước với class-translations.php.
 */
class Pi_Api_TranslationsTerms {

    public static function register_routes($ns, $admin) {
        if (!class_exists('Pi_Multilang_Term')) return;

        register_rest_route($ns, '/translations/terms', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'listing'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/translations/terms/coverage', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'coverage'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/translations/terms/(?P<id>\d+)', [
            ['methods' => 'GET', 'callback' => [self::class, 'detail'],     'permission_callback' => $admin],
            ['methods' => 'PUT', 'callback' => [self::class, 'save_draft'], 'permission_callback' => $admin],
        ]);

        register_rest_route($ns, '/translations/terms/(?P<id>\d+)/apply', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'apply'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/translations/terms/(?P<id>\d+)/undo', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'undo'],
            'permission_callback' => $admin,
        ]);
    }

    private static function lang($request) {
        $lang = (string) $request->get_param('lang');
        return $lang !== '' ? $lang : 'en';
    }

    public static function listing($request) {
        $res = Pi_Multilang_Term::listing([
            'lang'     => self::lang($request),
            'taxonomy' => (string) $request->get_param('taxonomy'),
            'state'    => (string) $request->get_param('state'),
            'search'   => (string) $request->get_param('search'),
            'page'     => (int) ($request->get_param('page') ?: 1),
            'per_page' => (int) ($request->get_param('per_page') ?: 50),
        ]);
        if (is_wp_error($res)) return $res;
        return rest_ensure_response($res);
    }

    public static function coverage($request) {
        return rest_ensure_response(Pi_Multilang_Term::coverage());
    }

    public static function detail($request) {
        $res = Pi_Multilang_Term::detail((int) $request->get_param('id'), self::lang($request));
        if (is_wp_error($res)) return $res;
        return rest_ensure_response($res);
    }

    private static function check_term_capability($term_id) {
        $term = get_term($term_id);
        if (!$term || is_wp_error($term)) {
            return new WP_Error('not_found', 'Không tìm thấy chuyên mục.', ['status' => 404]);
        }
        if (!current_user_can('edit_term', $term_id)) {
            return new WP_Error('forbidden', 'Không có quyền chỉnh sửa chuyên mục này.', ['status' => 403]);
        }
        return true;
    }

    public static function save_draft($request) {
        $id = (int) $request->get_param('id');
        $perm = self::check_term_capability($id);
        if (is_wp_error($perm)) return $perm;

        $fields = $request->get_param('fields');
        if (!is_array($fields)) {
            return new WP_Error('bad_request', 'Thiếu fields.', ['status' => 422]);
        }
        $res = Pi_Multilang_Term::save_draft($id, self::lang($request), $fields);
        if (is_wp_error($res)) return $res;
        return rest_ensure_response(['ok' => true, 'saved' => $res]);
    }

    public static function apply($request) {
        $id = (int) $request->get_param('id');
        $perm = self::check_term_capability($id);
        if (is_wp_error($perm)) return $perm;

        $res = Pi_Multilang_Term::apply($id, self::lang($request));
        if (is_wp_error($res)) return $res;
        return rest_ensure_response(['ok' => true, 'applied' => $res]);
    }

    public static function undo($request) {
        $id = (int) $request->get_param('id');
        $perm = self::check_term_capability($id);
        if (is_wp_error($perm)) return $perm;

        $res = Pi_Multilang_Term::undo($id, self::lang($request));
        if (is_wp_error($res)) return $res;
        return rest_ensure_response(['ok' => true, 'restored' => $res]);
    }
}
