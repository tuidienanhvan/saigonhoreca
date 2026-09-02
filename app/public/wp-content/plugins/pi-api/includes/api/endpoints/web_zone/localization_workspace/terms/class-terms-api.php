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

    public static function save_draft($request) {
        $fields = $request->get_param('fields');
        if (!is_array($fields)) {
            return new WP_Error('bad_request', 'Thiếu fields.', ['status' => 422]);
        }
        $res = Pi_Multilang_Term::save_draft((int) $request->get_param('id'), self::lang($request), $fields);
        if (is_wp_error($res)) return $res;
        return rest_ensure_response(['ok' => true, 'saved' => $res]);
    }

    public static function apply($request) {
        $res = Pi_Multilang_Term::apply((int) $request->get_param('id'), self::lang($request));
        if (is_wp_error($res)) return $res;
        return rest_ensure_response(['ok' => true, 'applied' => $res]);
    }

    public static function undo($request) {
        $res = Pi_Multilang_Term::undo((int) $request->get_param('id'), self::lang($request));
        if (is_wp_error($res)) return $res;
        return rest_ensure_response(['ok' => true, 'restored' => $res]);
    }
}
