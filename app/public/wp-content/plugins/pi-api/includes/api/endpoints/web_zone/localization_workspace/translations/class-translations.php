<?php
/**
 * Pi API Translations — Zone: WebZone, Workspace: LocalizationWorkspace.
 *
 * Mặt REST của MultilangApp. Lớp này CHỈ là tầng vận chuyển: nhận request,
 * kiểm quyền, gọi thẳng vào Pi_Multilang_* rồi trả JSON. Mọi luật nghiệp vụ
 * (chuẩn hoá config, validate tiền tố, phân loại trạng thái chuỗi, ghi .po,
 * biên dịch .mo, tạo bản dịch) đều nằm ở MultilangApp — không được chép sang
 * đây, vì hai bản sao luật sẽ lệch nhau đúng như class-core.php từng dính.
 *
 * Ba nhóm route, khớp ba màn hình cũ trong wp-admin:
 *   /translations/languages  — trang "Ngôn ngữ & URL"
 *   /translations/strings    — trang "Chuỗi giao diện" (.po/.mo)
 *   /translations/drafts     — trang "Duyệt bản dịch"
 *
 * @package Pi_Api
 */

if (!defined('ABSPATH')) exit;

class Pi_Api_Translations {

    public static function register_routes($ns, $admin) {
        // MultilangApp có thể không nạp (plugin pi-multilang độc lập còn bật —
        // xem guard trong pi-api.php). Không có class thì không đăng ký route,
        // còn hơn trả 500 ở mọi lần gọi.
        if (!class_exists('Pi_Multilang_Core')) return;

        register_rest_route($ns, '/translations/languages', [
            ['methods' => 'GET', 'callback' => [self::class, 'get_languages'], 'permission_callback' => $admin],
            ['methods' => 'PUT', 'callback' => [self::class, 'put_languages'], 'permission_callback' => $admin],
        ]);

        register_rest_route($ns, '/translations/languages/reset', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'reset_languages'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/translations/locales', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'get_locales'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/translations/strings', [
            ['methods' => 'GET', 'callback' => [self::class, 'get_strings'], 'permission_callback' => $admin],
            ['methods' => 'PUT', 'callback' => [self::class, 'put_strings'], 'permission_callback' => $admin],
        ]);

        register_rest_route($ns, '/translations/strings/regen-pot', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'regen_pot'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/translations/strings/compile-mo', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'compile_mo'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/translations/drafts', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'get_drafts'],
            'permission_callback' => $admin,
        ]);

        // Chuyển ngữ nội dung — bản dịch nằm trên CÙNG bản ghi, xem class-content.php.
        register_rest_route($ns, '/translations/content', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'list_content'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/translations/content/coverage', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'content_coverage'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/translations/content/(?P<id>\d+)', [
            ['methods' => 'GET', 'callback' => [self::class, 'get_content'], 'permission_callback' => $admin],
            ['methods' => 'PUT', 'callback' => [self::class, 'put_content'], 'permission_callback' => $admin],
        ]);

        register_rest_route($ns, '/translations/content/(?P<id>\d+)/apply', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'apply_content'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/translations/content/(?P<id>\d+)/undo', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'undo_content'],
            'permission_callback' => $admin,
        ]);
    }

    /* ================================================================== *
     * Ngôn ngữ & URL
     * ================================================================== */

    private static function current_host() {
        $host = strtolower((string) wp_parse_url(home_url(), PHP_URL_HOST));
        return preg_replace('/:\d+$/', '', $host);
    }

    public static function get_languages() {
        $host   = self::current_host();
        $config = Pi_Multilang_Core::get_language_config();
        $stored = get_option(Pi_Multilang_Core::OPTION_KEY, null);

        $languages = [];
        foreach ($config as $code => $c) {
            $languages[] = array_merge(['code' => $code], $c, [
                // Mặc định KHÔNG phải cờ lưu trong DB — nó là hệ quả của
                // path_prefix rỗng. Tính ra ở đây để UI khỏi tự suy luận.
                'is_default' => (trim($c['path_prefix'], '/') === ''),
            ]);
        }

        return rest_ensure_response([
            'host'            => $host,
            'languages'       => $languages,
            'has_custom'      => is_array($stored),
            // Config đã lưu nhưng không nêu host này thì Core bỏ qua toàn bộ —
            // UI phải cảnh báo, nếu không người dùng tưởng đã lưu thành công.
            'stored_applies'  => is_array($stored) ? Pi_Multilang_Core::config_has_host($stored, $host) : true,
            'uses_prefixes'   => Pi_Multilang_Core::host_uses_prefixes(),
            'available_locales' => self::locale_choices(),
        ]);
    }

    public static function put_languages($request) {
        $rows    = $request->get_param('languages');
        $default = (string) $request->get_param('default_lang');

        if (!is_array($rows)) {
            return new WP_Error('bad_request', 'Thiếu danh sách ngôn ngữ.', ['status' => 400]);
        }

        // Nhận cả dạng mảng-có-khoá lẫn danh sách có trường `code`, để phía
        // React gửi array thẳng từ state cũng được.
        $raw = [];
        foreach ($rows as $code => $r) {
            if (!is_array($r)) continue;
            if (isset($r['code'])) $code = $r['code'];
            $code = strtolower(trim((string) $code));
            if ($code === '') continue;
            $raw[$code] = [
                'label'       => isset($r['label'])       ? (string) $r['label']       : '',
                'host'        => isset($r['host'])        ? (string) $r['host']        : '',
                'path_prefix' => isset($r['path_prefix']) ? (string) $r['path_prefix'] : '',
                'wp_locale'   => isset($r['wp_locale'])   ? (string) $r['wp_locale']   : '',
                'hreflang'    => isset($r['hreflang'])    ? (string) $r['hreflang']    : '',
            ];
        }

        $config = Pi_Multilang_Core::normalize_config($raw);
        if (empty($config)) {
            return new WP_Error('invalid_config', 'Cần ít nhất một ngôn ngữ hợp lệ.', ['status' => 422]);
        }

        $config = Pi_Multilang_Languages_Admin::apply_default_language($config, $default);

        $err = Pi_Multilang_Languages_Admin::validate($config);
        if ($err !== null) {
            return new WP_Error('invalid_config', $err, ['status' => 422]);
        }

        update_option(Pi_Multilang_Core::OPTION_KEY, $config);
        Pi_Multilang_Languages_Admin::after_write();

        return self::get_languages();
    }

    public static function reset_languages() {
        delete_option(Pi_Multilang_Core::OPTION_KEY);
        Pi_Multilang_Languages_Admin::after_write();
        return self::get_languages();
    }

    /** Danh sách locale để chọn trong ô "Locale WP". */
    private static function locale_choices() {
        $out = array_values(array_unique(array_merge(
            ['vi', 'en_US', 'ja'],
            (array) get_available_languages(),
            class_exists('Pi_Multilang_UI_Strings') ? (array) Pi_Multilang_UI_Strings::available_locales() : []
        )));
        sort($out);
        return $out;
    }

    /* ================================================================== *
     * Chuỗi giao diện (.po / .mo)
     * ================================================================== */

    private static function resolve_locale($request) {
        $locales = Pi_Multilang_UI_Strings::available_locales();
        $locale  = (string) $request->get_param('locale');
        return in_array($locale, $locales, true) ? $locale : $locales[0];
    }

    public static function get_locales() {
        return rest_ensure_response([
            'locales' => Pi_Multilang_UI_Strings::available_locales(),
            'default' => Pi_Multilang_UI_Strings::DEFAULT_LOCALE,
        ]);
    }

    public static function get_strings($request) {
        $locale = self::resolve_locale($request);

        $po = Pi_Multilang_UI_Strings::load_po($locale);
        if (!$po) {
            return new WP_Error(
                'po_unreadable',
                'Không đọc được file .po: ' . Pi_Multilang_UI_Strings::po_path($locale),
                ['status' => 500]
            );
        }

        $filter = (string) $request->get_param('filter');
        if (!in_array($filter, ['all', 'untranslated', 'leaking', 'empty', 'translated'], true)) {
            $filter = 'all';
        }

        $q = Pi_Multilang_UI_Strings::query($po, [
            'search' => (string) $request->get_param('search'),
            'filter' => $filter,
            'page'   => max(1, (int) $request->get_param('page')),
        ]);

        $mo_path = Pi_Multilang_UI_Strings::mo_path($locale);

        return rest_ensure_response([
            'locale'   => $locale,
            'filter'   => $filter,
            'items'    => $q['items'],
            'total'    => $q['total'],
            'total_all'=> $q['total_all'],
            'counts'   => $q['counts'],
            'untranslated' => $q['untranslated'],
            'page'     => $q['page'],
            'pages'    => $q['pages'],
            'per_page' => Pi_Multilang_UI_Strings::PER_PAGE,
            'files'    => [
                'po'          => Pi_Multilang_UI_Strings::po_path($locale),
                'mo'          => $mo_path,
                'pot'         => Pi_Multilang_UI_Strings::pot_path(),
                'po_writable' => is_writable(Pi_Multilang_UI_Strings::po_path($locale)),
                'mo_mtime'    => file_exists($mo_path) ? filemtime($mo_path) : null,
            ],
        ]);
    }

    public static function put_strings($request) {
        $locale = self::resolve_locale($request);
        $raw    = $request->get_param('strings');
        if (!is_array($raw)) {
            return new WP_Error('bad_request', 'Thiếu danh sách chuỗi.', ['status' => 400]);
        }

        $pairs = [];
        foreach ($raw as $key => $value) {
            // Khoá là md5 do chính MultilangApp sinh ra — lọc để không dùng
            // input thô làm khoá tra cứu.
            if (!preg_match('/^[a-f0-9]{32}$/', (string) $key)) continue;
            // KHÔNG sanitize nội dung: bản dịch là văn bản thuần, sanitize sẽ
            // ăn mất dấu nháy, dấu & và ký tự có dấu hợp lệ.
            $pairs[$key] = (string) $value;
        }

        $result = Pi_Multilang_UI_Strings::save_msgstrs($pairs, $locale);
        if (is_wp_error($result)) {
            return new WP_Error($result->get_error_code(), $result->get_error_message(), ['status' => 500]);
        }

        return rest_ensure_response([
            'changed' => (int) $result['changed'],
            'mo'      => (bool) $result['mo'],
        ]);
    }

    public static function regen_pot() {
        $result = Pi_Multilang_UI_Strings::regen_pot();
        if (is_wp_error($result)) {
            return new WP_Error($result->get_error_code(), $result->get_error_message(), ['status' => 500]);
        }
        return rest_ensure_response($result);
    }

    public static function compile_mo($request) {
        $locale = self::resolve_locale($request);
        $result = Pi_Multilang_UI_Strings::compile_mo($locale);
        if (is_wp_error($result)) {
            return new WP_Error($result->get_error_code(), $result->get_error_message(), ['status' => 500]);
        }
        return rest_ensure_response(['locale' => $locale, 'compiled' => true]);
    }

    /* ================================================================== *
     * Bản dịch nội dung
     * ================================================================== */

    public static function get_drafts($request) {
        $lang = (string) $request->get_param('language');
        if ($lang === '') $lang = 'en';

        $drafts = get_posts([
            'post_type'      => 'any',
            'post_status'    => 'draft',
            'posts_per_page' => 200,
            'meta_query'     => [
                'relation' => 'AND',
                ['key' => 'pi_translation_group', 'compare' => 'EXISTS'],
                ['key' => 'pi_language', 'value' => $lang],
            ],
        ]);

        $items = [];
        foreach ($drafts as $d) {
            $items[] = [
                'id'        => $d->ID,
                'title'     => $d->post_title,
                'type'      => $d->post_type,
                'language'  => $lang,
                'group'     => get_post_meta($d->ID, 'pi_translation_group', true),
                'modified'  => $d->post_modified_gmt,
                'edit_link' => get_edit_post_link($d->ID, 'raw'),
            ];
        }

        return rest_ensure_response(['language' => $lang, 'items' => $items]);
    }

    /* ================================================================== *
     * Chuyển ngữ nội dung
     * ================================================================== */

    /**
     * Quyền sửa bài là quyền THEO BÀI, không phải theo site. manage_options ở
     * permission_callback chưa đủ — giữ đúng chốt của nút trong wp-admin.
     */
    private static function guard_post($post_id) {
        if (!get_post($post_id)) {
            return new WP_Error('not_found', 'Không tìm thấy bài.', ['status' => 404]);
        }
        if (!current_user_can('edit_post', $post_id)) {
            return new WP_Error('forbidden', 'Không có quyền sửa bài này.', ['status' => 403]);
        }
        return true;
    }

    private static function guard_lang($lang) {
        $config = Pi_Multilang_Core::get_language_config();
        if ($lang === '' || !isset($config[$lang])) {
            return new WP_Error('bad_lang', 'Thiếu tham số lang hợp lệ.', ['status' => 400]);
        }
        return true;
    }

    public static function content_coverage($request) {
        return rest_ensure_response([
            'coverage'     => Pi_Multilang_Content::coverage(),
            'post_types'   => Pi_Multilang_Content::post_types(),
            'default_lang' => Pi_Multilang_Content::default_lang(),
            'languages'    => Pi_Multilang_Core::get_language_config(),
        ]);
    }

    /**
     * Danh sách bài kèm trạng thái dịch cho MỘT ngôn ngữ.
     *
     * Trạng thái tính từ trường `title` — nó là trường duy nhất mọi loại bài
     * chắc chắn có. Lấy `content` làm mốc thì 528 product (mô tả nằm ở meta,
     * post_content rỗng) sẽ vĩnh viễn hiện "chưa dịch" dù tiêu đề đã dịch xong.
     */
    public static function list_content($request) {
        $lang = (string) $request->get_param('lang');
        $ok   = self::guard_lang($lang);
        if (is_wp_error($ok)) return $ok;

        $types = Pi_Multilang_Content::post_types();
        $type  = (string) $request->get_param('post_type');
        if ($type !== '' && !in_array($type, $types, true)) {
            return new WP_Error('bad_type', 'Loại bài không nằm trong danh sách dịch được.', ['status' => 400]);
        }

        $per   = min(100, max(1, (int) ($request->get_param('per_page') ?: 20)));
        $page  = max(1, (int) ($request->get_param('page') ?: 1));
        $state = (string) $request->get_param('state');   // '' | missing | draft | live

        $args = [
            'post_type'      => $type !== '' ? $type : $types,
            'post_status'    => 'publish',
            'posts_per_page' => $per,
            'paged'          => $page,
            'orderby'        => 'modified',
            'order'          => 'DESC',
        ];
        $search = (string) $request->get_param('search');
        if ($search !== '') $args['s'] = $search;

        if ($state === 'live' || $state === 'draft') {
            $key = $state === 'live'
                ? Pi_Multilang_Content::live_key($lang, 'title')
                : Pi_Multilang_Content::draft_key($lang, 'title');
            $args['meta_query'] = [['key' => $key, 'compare' => 'EXISTS']];
        } elseif ($state === 'missing') {
            $args['meta_query'] = [
                'relation' => 'AND',
                ['key' => Pi_Multilang_Content::live_key($lang, 'title'),  'compare' => 'NOT EXISTS'],
                ['key' => Pi_Multilang_Content::draft_key($lang, 'title'), 'compare' => 'NOT EXISTS'],
            ];
        }

        $q     = new WP_Query($args);
        $items = [];
        foreach ($q->posts as $post) $items[] = self::content_row($post, $lang);

        return rest_ensure_response([
            'items' => $items,
            'total' => (int) $q->found_posts,
            'pages' => (int) $q->max_num_pages,
            'page'  => $page,
        ]);
    }

    private static function content_row($post, $lang) {
        $live  = Pi_Multilang_Content::live($post->ID, $lang, 'title');
        $draft = Pi_Multilang_Content::draft($post->ID, $lang, 'title');
        // resolve_out() trả về CHÍNH slug gốc khi chưa có alias — đúng cho việc
        // dựng URL, nhưng cột này hỏi "đã có alias riêng chưa". Không so lại thì
        // mọi bài đều hiện như đã đặt slug tiếng Anh, kể cả bài chưa đụng tới.
        $alias = class_exists('Pi_Multilang_Slugs')
            ? (string) Pi_Multilang_Slugs::resolve_out($post->post_name, $lang, 'post')
            : '';
        if ($alias === $post->post_name) $alias = '';

        return [
            'id'          => (int) $post->ID,
            'post_type'   => $post->post_type,
            'title'       => $post->post_title,
            'slug'        => $post->post_name,
            'source_lang' => Pi_Multilang_Content::source_lang($post->ID),
            'alias'       => is_string($alias) ? $alias : '',
            'state'       => $draft !== '' ? 'draft' : ($live !== '' ? 'live' : 'missing'),
            'permalink'   => get_permalink($post),
            'edit_link'   => get_edit_post_link($post->ID, 'raw'),
        ];
    }

    /** Bài gốc + nháp + bản sống, đủ cho màn hình soạn dịch. */
    public static function get_content($request) {
        $id = (int) $request->get_param('id');
        $ok = self::guard_post($id);
        if (is_wp_error($ok)) return $ok;

        $lang = (string) $request->get_param('lang');
        $ok   = self::guard_lang($lang);
        if (is_wp_error($ok)) return $ok;

        $post   = get_post($id);
        $fields = [];
        // Lay thang tu FIELDS de them truong moi (seo_title, seo_description...)
        // chi phai sua MOT cho o class-content, khong phai nho sua ca day.
        foreach (Pi_Multilang_Content::FIELDS as $f) {
            $source = Pi_Multilang_Content::source_value($id, $f);

            $fields[$f] = [
                'source'   => $source,
                'draft'    => Pi_Multilang_Content::draft($id, $lang, $f),
                'live'     => Pi_Multilang_Content::live($id, $lang, $f),
                // Có ảnh chụp nghĩa là có cái để hoàn tác — nút Undo bật theo cờ này.
                'undoable' => metadata_exists('post', $id, Pi_Multilang_Content::old_key($lang, $f)),
            ];
        }

        return rest_ensure_response([
            'id'          => $id,
            'post_type'   => $post->post_type,
            'slug'        => $post->post_name,
            'source_lang' => Pi_Multilang_Content::source_lang($id),
            'lang'        => $lang,
            'fields'      => $fields,
            'permalink'   => get_permalink($post),
        ]);
    }

    /** Lưu bản NHÁP. Không đụng gì tới cái người dùng cuối đang thấy. */
    public static function put_content($request) {
        $id = (int) $request->get_param('id');
        $ok = self::guard_post($id);
        if (is_wp_error($ok)) return $ok;

        $fields = $request->get_param('fields');
        if (!is_array($fields)) {
            return new WP_Error('bad_fields', 'Thiếu trường fields.', ['status' => 400]);
        }

        $res = Pi_Multilang_Content::save_draft($id, (string) $request->get_param('lang'), $fields);
        if (is_wp_error($res)) return $res;

        return rest_ensure_response(['saved' => true]);
    }

    /**
     * Áp bản nháp. Kèm luôn alias slug nếu người dùng gửi lên — hai thứ này
     * luôn đi cùng nhau trong thực tế (dịch tiêu đề xong thì đổi URL theo),
     * tách làm hai lần bấm chỉ tạo ra trạng thái nửa vời.
     */
    public static function apply_content($request) {
        $id = (int) $request->get_param('id');
        $ok = self::guard_post($id);
        if (is_wp_error($ok)) return $ok;

        $lang  = (string) $request->get_param('lang');
        $alias = (string) $request->get_param('alias');

        $applied = Pi_Multilang_Content::apply($id, $lang);
        if (is_wp_error($applied)) return $applied;

        $alias_result = null;
        if ($alias !== '' && class_exists('Pi_Multilang_Slugs')) {
            $r = Pi_Multilang_Slugs::set_alias([
                'object_type' => 'post',
                'object_id'   => $id,
                'lang'        => $lang,
                'alias'       => $alias,
                'source'      => 'ai',
            ]);
            // Alias hỏng KHÔNG được huỷ phần nội dung đã áp — báo lỗi kèm theo
            // để người dùng sửa riêng, còn hơn nuốt mất bản dịch vừa duyệt.
            $alias_result = is_wp_error($r)
                ? ['ok' => false, 'message' => $r->get_error_message()]
                : ['ok' => true, 'alias' => $alias];
        }

        return rest_ensure_response(['applied' => $applied, 'alias' => $alias_result]);
    }

    public static function undo_content($request) {
        $id = (int) $request->get_param('id');
        $ok = self::guard_post($id);
        if (is_wp_error($ok)) return $ok;

        $res = Pi_Multilang_Content::undo($id, (string) $request->get_param('lang'));
        if (is_wp_error($res)) return $res;

        return rest_ensure_response(['restored' => $res]);
    }
}
