<?php
if (!defined('ABSPATH')) exit;

/**
 * Mặt REST của việc rà soát thẻ ngôn ngữ (EP-12, EP-13 — UC-13).
 *
 * Chỉ là tầng vận chuyển: nhận request, kiểm tham số, gọi Pi_Multilang_Audit,
 * trả JSON. Không có nghiệp vụ nào ở đây — cùng quy ước với class-translations.php.
 */
class Pi_Api_TranslationsAudit {

    public static function register_routes($ns, $admin) {
        // MultilangApp có thể không nạp (plugin pi-multilang độc lập đang bật).
        if (!class_exists('Pi_Multilang_Audit')) return;

        register_rest_route($ns, '/translations/audit/language-tags', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'language_tags'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/translations/audit/untag', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'untag'],
            'permission_callback' => $admin,
        ]);

        // Ngoài hợp đồng gốc: EP-13 hứa "ghi lô hoàn tác được", mà không có
        // đường lùi thì lời hứa đó rỗng. Hai route dưới đây làm nó thành thật.
        register_rest_route($ns, '/translations/audit/batches', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'batches'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/translations/audit/batches/(?P<id>\d+)/undo', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'undo'],
            'permission_callback' => $admin,
        ]);
    }

    public static function language_tags($request) {
        $limit  = $request->get_param('limit');
        $offset = $request->get_param('offset');

        $res = Pi_Multilang_Audit::mistagged(
            $limit === null ? 100 : (int) $limit,
            $offset === null ? 0 : (int) $offset
        );
        return rest_ensure_response(['ok' => true] + $res);
    }

    public static function untag($request) {
        $ids = $request->get_param('ids');
        if (!is_array($ids)) {
            return new WP_Error('bad_request', 'Thiếu danh sách ids.', ['status' => 422]);
        }

        $res = Pi_Multilang_Audit::untag(
            $ids,
            (string) $request->get_param('idempotency_key'),
            (string) $request->get_param('note')
        );
        if (is_wp_error($res)) return $res;
        return rest_ensure_response($res);
    }

    public static function batches($request) {
        $limit = $request->get_param('limit');
        return rest_ensure_response([
            'ok'    => true,
            'items' => Pi_Multilang_Audit::batches($limit === null ? 50 : (int) $limit),
        ]);
    }

    public static function undo($request) {
        $res = Pi_Multilang_Audit::undo_untag((int) $request->get_param('id'));
        if (is_wp_error($res)) return $res;
        return rest_ensure_response($res);
    }
}
