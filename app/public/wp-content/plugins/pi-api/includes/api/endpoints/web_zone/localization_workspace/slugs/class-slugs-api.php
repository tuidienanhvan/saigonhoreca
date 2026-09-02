<?php
/**
 * Pi API Slugs — Zone: WebZone, Workspace: LocalizationWorkspace.
 *
 * Mặt REST của lớp slug đa ngôn ngữ. Chỉ là tầng vận chuyển: kiểm quyền, gọi
 * Pi_Multilang_Slugs, trả JSON. Mọi luật (chuẩn hoá alias, kiểm va chạm, cho
 * alias cũ về hưu, dựng chỉ mục) nằm trong MultilangApp — không chép sang đây.
 *
 * @package Pi_Api
 */

if (!defined('ABSPATH')) exit;

class Pi_Api_Slugs {

    public static function register_routes($ns, $admin) {
        if (!class_exists('Pi_Multilang_Slugs')) return;

        register_rest_route($ns, '/translations/slugs', [
            ['methods' => 'GET', 'callback' => [self::class, 'list_slugs'], 'permission_callback' => $admin],
            ['methods' => 'PUT', 'callback' => [self::class, 'put_slug'],   'permission_callback' => $admin],
        ]);

        register_rest_route($ns, '/translations/slugs/seed-routes', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'seed_routes'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/translations/slugs/flags', [
            ['methods' => 'GET',  'callback' => [self::class, 'get_flags'], 'permission_callback' => $admin],
            ['methods' => 'POST', 'callback' => [self::class, 'set_flags'], 'permission_callback' => $admin],
        ]);

        register_rest_route($ns, '/translations/slugs/batches', [
            ['methods' => 'GET',  'callback' => [self::class, 'list_batches'],  'permission_callback' => $admin],
            ['methods' => 'POST', 'callback' => [self::class, 'create_batch'],  'permission_callback' => $admin],
        ]);

        register_rest_route($ns, '/translations/slugs/batches/(?P<id>\d+)', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'get_batch'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/translations/slugs/batches/(?P<id>\d+)/apply', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'apply_batch'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/translations/slugs/batches/(?P<id>\d+)/undo', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'undo_batch'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/translations/slugs/index', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'get_index'],
            'permission_callback' => $admin,
        ]);
    }

    public static function list_slugs($request) {
        global $wpdb;
        Pi_Multilang_Slugs::ensure_table();

        $table  = Pi_Multilang_Slugs::table();
        $lang   = (string) $request->get_param('lang');
        $status = (string) $request->get_param('status');
        $limit  = min(500, max(1, (int) ($request->get_param('limit') ?: 200)));
        $offset = max(0, (int) $request->get_param('offset'));

        $where = ['1=1'];
        $args  = [];
        if ($lang !== '')   { $where[] = 'lang = %s';   $args[] = $lang; }
        if ($status !== '') { $where[] = 'status = %s'; $args[] = $status; }
        $sql = "SELECT * FROM {$table} WHERE " . implode(' AND ', $where) . ' ORDER BY id DESC LIMIT %d OFFSET %d';
        $args[] = $limit; $args[] = $offset;

        $rows  = $wpdb->get_results($wpdb->prepare($sql, $args));
        $total = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$table}");

        return rest_ensure_response([
            'items' => $rows ?: [],
            'total' => $total,
            'limit' => $limit,
            'offset'=> $offset,
        ]);
    }

    /**
     * Đặt alias. Quyền: cấu hình URL công khai là việc của quản trị viên
     * (permission_callback = manage_options), NHƯNG sửa alias của một bài cụ thể
     * còn phải có quyền sửa CHÍNH bài đó.
     */
    public static function put_slug($request) {
        $type = (string) ($request->get_param('object_type') ?: 'post');
        $oid  = (int) $request->get_param('object_id');

        if ($type !== 'route' && $oid > 0 && !current_user_can('edit_post', $oid)) {
            return new WP_Error('forbidden', 'Không có quyền sửa bài này.', ['status' => 403]);
        }

        $res = Pi_Multilang_Slugs::set_alias([
            'object_type' => $type,
            'object_id'   => $oid,
            'route_key'   => (string) $request->get_param('route_key'),
            'lang'        => (string) $request->get_param('lang'),
            'alias'       => (string) $request->get_param('alias'),
            'source'      => (string) ($request->get_param('source') ?: 'manual'),
        ]);

        if (is_wp_error($res)) {
            return new WP_Error($res->get_error_code(), $res->get_error_message(), ['status' => 422]);
        }

        self::flush_site_caches();
        return rest_ensure_response(['saved' => true]);
    }

    public static function seed_routes() {
        $res = Pi_Multilang_Slugs::seed_routes();
        if (is_wp_error($res)) {
            return new WP_Error($res->get_error_code(), $res->get_error_message(), ['status' => 500]);
        }
        self::flush_site_caches();
        return rest_ensure_response($res);
    }

    public static function get_flags() {
        return rest_ensure_response([
            'flags' => (array) get_option(Pi_Multilang_Slugs::FLAGS_OPTION, []),
            'known' => ['slug_aliases', 'in_resolve_route', 'legacy_301'],
        ]);
    }

    public static function set_flags($request) {
        $flags = $request->get_param('flags');
        if (!is_array($flags)) {
            return new WP_Error('bad_request', 'Thiếu danh sách cờ.', ['status' => 400]);
        }
        foreach ($flags as $name => $on) {
            Pi_Multilang_Slugs::set_flag(sanitize_key($name), (bool) $on);
        }
        Pi_Multilang_Slugs::flush_index();
        self::flush_site_caches();
        return self::get_flags();
    }

    /* ================================================================== *
     * Lô thay đổi URL
     * ================================================================== */

    public static function list_batches($request) {
        $limit  = min(200, max(1, (int) ($request->get_param('limit') ?: 50)));
        $offset = max(0, (int) $request->get_param('offset'));
        return rest_ensure_response(['items' => Pi_Multilang_Slug_Batch::listing($limit, $offset)]);
    }

    /**
     * Tạo lô NHÁP kèm bảng xem trước URL cũ -> mới.
     *
     * Đề xuất đến từ đâu không quan trọng (người nhập tay hay AI Copilot sinh ở
     * phía dashboard) — lô chỉ nhận danh sách đã có alias. Máy đề xuất, người
     * giữ nút áp: KHÔNG có đường nào ghi thẳng vào bảng alias mà bỏ qua bước này.
     */
    public static function create_batch($request) {
        $items = $request->get_param('items');
        $lang  = (string) $request->get_param('lang');
        if (!is_array($items) || !$items) {
            return new WP_Error('bad_request', 'Danh sách rỗng.', ['status' => 400]);
        }

        $prepared = [];
        foreach ($items as $it) {
            if (!is_array($it)) continue;
            $type  = (string) ($it['object_type'] ?? 'post');
            $alias = Pi_Multilang_Slugs::normalize_alias($it['alias'] ?? '');
            $lg    = (string) ($it['lang'] ?? $lang);
            if ($alias === '' || $lg === '') continue;

            $oid  = (int) ($it['object_id'] ?? 0);
            $rkey = (string) ($it['route_key'] ?? '');

            // Cảnh báo trước, KHÔNG chặn: người xem bảng mới quyết định. Chặn ở
            // đây là giấu mất lý do dòng đó biến mất khỏi lô.
            $check = Pi_Multilang_Slugs::validate_alias($alias, $lg, $type, $oid, $rkey);

            $prepared[] = [
                'object_type' => $type,
                'object_id'   => $oid,
                'route_key'   => $rkey,
                'lang'        => $lg,
                'alias'       => $alias,
                'source'      => (string) ($it['source'] ?? 'ai'),
                'title'       => (string) ($it['title'] ?? ($oid ? get_the_title($oid) : $rkey)),
                'url_before'  => (string) ($it['url_before'] ?? ''),
                'url_after'   => (string) ($it['url_after'] ?? ''),
                'risk'        => is_wp_error($check) ? $check->get_error_message() : null,
            ];
        }

        if (!$prepared) {
            return new WP_Error('bad_request', 'Không có dòng nào hợp lệ.', ['status' => 422]);
        }

        $id = Pi_Multilang_Slug_Batch::create($prepared, $lang, (string) $request->get_param('note'));
        if (is_wp_error($id)) {
            return new WP_Error($id->get_error_code(), $id->get_error_message(), ['status' => 500]);
        }
        return rest_ensure_response(Pi_Multilang_Slug_Batch::get($id));
    }

    public static function get_batch($request) {
        $batch = Pi_Multilang_Slug_Batch::get((int) $request->get_param('id'));
        if (!$batch) return new WP_Error('not_found', 'Không tìm thấy lô.', ['status' => 404]);
        return rest_ensure_response($batch);
    }

    public static function apply_batch($request) {
        $res = Pi_Multilang_Slug_Batch::apply((int) $request->get_param('id'));
        if (is_wp_error($res)) {
            $status = $res->get_error_data()['status'] ?? 500;
            return new WP_Error($res->get_error_code(), $res->get_error_message(), ['status' => $status]);
        }
        return rest_ensure_response($res);
    }

    public static function undo_batch($request) {
        $res = Pi_Multilang_Slug_Batch::undo((int) $request->get_param('id'));
        if (is_wp_error($res)) {
            $status = $res->get_error_data()['status'] ?? 500;
            return new WP_Error($res->get_error_code(), $res->get_error_message(), ['status' => $status]);
        }
        return rest_ensure_response($res);
    }

    /** Dùng để soi khi gỡ lỗi: chỉ mục đang có gì. */
    public static function get_index() {
        $idx = Pi_Multilang_Slugs::build_index();
        return rest_ensure_response([
            'counts' => [
                'in'    => count($idx['in']),
                'out'   => count($idx['out']),
                'old'   => count($idx['old']),
                'canon' => count($idx['canon']),
            ],
            'index' => $idx,
        ]);
    }

    /**
     * Đổi alias là đổi URL công khai — cache tĩnh còn giữ bản cũ sẽ phục vụ
     * TRƯỚC cả khi PHP chạy, nên 301 không nổ. Phải xoá ngay tại chỗ ghi.
     */
    private static function flush_site_caches() {
        if (function_exists('sgh_static_cache_flush')) sgh_static_cache_flush();
        if (class_exists('Pi_Multilang_Legacy_Bridge')) Pi_Multilang_Legacy_Bridge::flush_home_caches();
        do_action('sgh_after_flush_cache');
    }
}
