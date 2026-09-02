<?php
if (!defined('ABSPATH')) exit;

/**
 * Mặt REST của lô việc nền đa ngôn ngữ (EP-16 — UC-14).
 *
 * Tầng vận chuyển thuần; nghiệp vụ nằm ở Pi_Multilang_Jobs.
 */
class Pi_Api_TranslationsJobs {

    public static function register_routes($ns, $admin) {
        if (!class_exists('Pi_Multilang_Jobs')) return;

        register_rest_route($ns, '/translations/jobs/run', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'run'],
            'permission_callback' => $admin,
        ]);

        // Trả 202 kèm mã việc thì phải có chỗ tra lại mã đó, nếu không mã việc
        // là số vô nghĩa.
        register_rest_route($ns, '/translations/jobs', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'listing'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/translations/jobs/(?P<id>[A-Za-z0-9_]+)', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'get_one'],
            'permission_callback' => $admin,
        ]);
    }

    public static function run($request) {
        $args = $request->get_param('args');

        $res = Pi_Multilang_Jobs::run(
            (string) $request->get_param('kind'),
            is_array($args) ? $args : [],
            (string) $request->get_param('idempotency_key')
        );
        if (is_wp_error($res)) return $res;

        // 202: hợp đồng API khai việc là bất đồng bộ. Bản cài đặt này chạy xong
        // ngay trong request (xem ghi chú đầu class-jobs.php) nên `status` đã là
        // done/failed — giao diện cứ đọc `status`, đừng suy từ mã HTTP.
        return new WP_REST_Response($res, 202);
    }

    public static function listing($request) {
        $limit = $request->get_param('limit');
        return rest_ensure_response([
            'ok'    => true,
            'kinds' => Pi_Multilang_Jobs::kinds(),
            'items' => Pi_Multilang_Jobs::listing($limit === null ? 20 : (int) $limit),
        ]);
    }

    public static function get_one($request) {
        $job = Pi_Multilang_Jobs::get((string) $request->get_param('id'));
        if (!$job) return new WP_Error('not_found', 'Không tìm thấy việc.', ['status' => 404]);
        return rest_ensure_response($job);
    }
}
