<?php
if (!defined('ABSPATH')) exit;

/**
 * Slug theo ngôn ngữ — ALIAS trên MỘT bản ghi gốc.
 *
 * Vì sao alias chứ không nhân bản bài: nhiều trang của theme dựng bằng template
 * PHP với post_content RỖNG (page-lien-he.php, page-gioi-thieu.php). Nhân bản
 * chúng ra bản ngôn ngữ khác cho ra trang trắng, và đã xảy ra thật — 612 page
 * "Contact" cùng 148 page "About" bản nháp rỗng, tích tụ 27/07→01/08/2026.
 * Một bài gốc, N alias: thêm ngôn ngữ không nhân số bài lên.
 *
 * BA BẤT BIẾN, đọc trước khi sửa:
 *
 * 1. `alias` là MỘT ĐOẠN, không phải path. Phân cấp trang cha/con được ghép lại
 *    lúc phân giải. Lưu path đầy đủ thì đổi slug trang cha làm hỏng toàn bộ
 *    alias con — hỏng âm thầm, không báo lỗi.
 *
 * 2. `status` nằm TRONG unique key. Một alias có đúng một bản `active` nhưng
 *    được phép nhiều bản `retired` (lịch sử slug). Bỏ `status` khỏi key là không
 *    lưu được lịch sử, mà lịch sử chính là nguồn của 301.
 *
 * 3. Bảng là NGUỒN CHÂN LÝ, option `pi_multilang_slug_index` chỉ là bản dựng lại
 *    để đọc nhanh ở frontend. Sửa bảng thì PHẢI xoá option. Không bao giờ ghi
 *    thẳng vào option.
 *
 * `ensure_table()` chỉ được gọi từ REST/admin, TUYỆT ĐỐI không từ đường frontend
 * — `dbDelta()` nạp wp-admin/includes/upgrade.php và chạy hàng chục DESCRIBE.
 */
class Pi_Multilang_Slugs {

    const TABLE          = 'pi_multilang_slugs';
    const SCHEMA_OPTION  = 'pi_multilang_slugs_schema';
    const SCHEMA_VERSION = 1;
    const INDEX_OPTION   = 'pi_multilang_slug_index';
    const INDEX_VERSION  = 1;
    const FLAGS_OPTION   = 'pi_multilang_flags';

    /** Loại nội dung nạp sẵn vào chỉ mục. `product` cố ý ĐỨNG NGOÀI — xem get_index(). */
    const HOT_POST_TYPES = ['page', 'post', 'project'];

    /* ================================================================== *
     * Cờ tính năng
     * ================================================================== */

    /**
     * Mỗi bước triển khai một cờ, mặc định TẮT. Một lần đọc option phục vụ mọi
     * cờ, nên autoload = yes (mảng vài trăm byte).
     */
    public static function flag($name) {
        static $flags = null;
        if ($flags === null) $flags = (array) get_option(self::FLAGS_OPTION, []);
        return !empty($flags[$name]);
    }

    public static function set_flag($name, $on) {
        $flags = (array) get_option(self::FLAGS_OPTION, []);
        $flags[$name] = (bool) $on;
        update_option(self::FLAGS_OPTION, $flags, true);
    }

    /* ================================================================== *
     * Bảng
     * ================================================================== */

    public static function table() {
        global $wpdb;
        return $wpdb->prefix . self::TABLE;
    }

    /**
     * Migration lười, đúng pattern của pi-api (không register_activation_hook —
     * xem class-forms.php:41,119 và class-analytics.php:112).
     */
    public static function ensure_table() {
        if ((int) get_option(self::SCHEMA_OPTION, 0) >= self::SCHEMA_VERSION) return;

        global $wpdb;
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $table   = self::table();
        $collate = $wpdb->get_charset_collate();

        // alias VARCHAR(200) chứ không 255: index (lang, object_type, alias,
        // status) với utf8mb4 phải nằm dưới 3072 byte của InnoDB, và 200 cũng
        // khớp giới hạn thực tế của post_name.
        $sql = "CREATE TABLE {$table} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            object_type VARCHAR(20) NOT NULL DEFAULT 'post',
            object_id BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
            route_key VARCHAR(64) NOT NULL DEFAULT '',
            lang VARCHAR(12) NOT NULL DEFAULT '',
            alias VARCHAR(200) NOT NULL DEFAULT '',
            status VARCHAR(12) NOT NULL DEFAULT 'active',
            source VARCHAR(12) NOT NULL DEFAULT 'manual',
            batch_id BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            retired_at DATETIME NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY uniq_alias (lang, object_type, alias, status),
            KEY obj (object_type, object_id, lang),
            KEY route (route_key, lang),
            KEY batch (batch_id)
        ) {$collate};";

        dbDelta($sql);
        update_option(self::SCHEMA_OPTION, self::SCHEMA_VERSION, false);
    }

    /* ================================================================== *
     * Ghi
     * ================================================================== */

    /**
     * Đặt alias active cho một đối tượng ở một ngôn ngữ. Alias cũ (nếu có và
     * khác) chuyển sang `retired` — đó chính là lịch sử để 301.
     *
     * @return true|WP_Error
     */
    public static function set_alias(array $args) {
        global $wpdb;
        self::ensure_table();

        $type  = isset($args['object_type']) ? sanitize_key($args['object_type']) : 'post';
        $oid   = isset($args['object_id']) ? (int) $args['object_id'] : 0;
        $rkey  = isset($args['route_key']) ? sanitize_key($args['route_key']) : '';
        $lang  = isset($args['lang']) ? strtolower(trim((string) $args['lang'])) : '';
        $alias = self::normalize_alias(isset($args['alias']) ? $args['alias'] : '');
        $src   = isset($args['source']) ? sanitize_key($args['source']) : 'manual';
        $batch = isset($args['batch_id']) ? (int) $args['batch_id'] : 0;

        if ($lang === '' || $alias === '') {
            return new WP_Error('bad_alias', 'Thiếu ngôn ngữ hoặc alias.');
        }
        if ($type === 'route' ? $rkey === '' : $oid <= 0) {
            return new WP_Error('bad_target', 'Thiếu đối tượng đích.');
        }

        $err = self::validate_alias($alias, $lang, $type, $oid, $rkey);
        if (is_wp_error($err)) return $err;

        $table = self::table();
        $where = $type === 'route'
            ? $wpdb->prepare("object_type='route' AND route_key=%s AND lang=%s", $rkey, $lang)
            : $wpdb->prepare("object_type=%s AND object_id=%d AND lang=%s", $type, $oid, $lang);

        $current = $wpdb->get_row("SELECT * FROM {$table} WHERE {$where} AND status='active' LIMIT 1");

        if ($current && $current->alias === $alias) return true;   // không đổi

        if ($current) {
            // Cho về hưu, KHÔNG xoá: dòng này là nguồn của 301.
            $wpdb->update($table,
                ['status' => 'retired', 'retired_at' => current_time('mysql', true)],
                ['id' => (int) $current->id]
            );
        }

        // Nếu chính alias này từng bị cho về hưu thì gỡ bản retired đi, nếu không
        // sẽ có 301 trỏ về chính nó — vòng lặp.
        $wpdb->query($wpdb->prepare(
            "DELETE FROM {$table} WHERE lang=%s AND object_type=%s AND alias=%s AND status='retired'",
            $lang, $type, $alias
        ));

        $ok = $wpdb->insert($table, [
            'object_type' => $type,
            'object_id'   => $oid,
            'route_key'   => $rkey,
            'lang'        => $lang,
            'alias'       => $alias,
            'status'      => 'active',
            'source'      => $src,
            'batch_id'    => $batch,
            'created_at'  => current_time('mysql', true),
        ]);

        if ($ok === false) return new WP_Error('insert_failed', 'Không ghi được alias: ' . $wpdb->last_error);

        self::flush_index();
        return true;
    }

    /** Chuẩn hoá về dạng an toàn cho URL. Rỗng nghĩa là không dùng được. */
    public static function normalize_alias($raw) {
        $s = (string) $raw;
        $s = strtok($s, '?');
        $s = trim((string) $s, "/ \t\n\r\0\x0B");
        if ($s === '') return '';
        // sanitize_title xử lý chuyển tự có dấu -> ASCII và ký tự lạ; giữ
        // nguyên ký tự phi Latin nếu locale cho phép, đúng như WP làm với slug.
        $s = sanitize_title($s);
        if (strpos($s, '/') !== false) return '';   // alias phải là MỘT đoạn
        return function_exists('mb_substr') ? mb_substr($s, 0, 200, 'UTF-8') : substr($s, 0, 200);
    }

    /**
     * Alias có dùng được không. Ba lớp chặn, theo thứ tự rẻ dần:
     * đụng tiền tố ngôn ngữ, đụng đoạn URL dành riêng, trùng alias khác.
     *
     * @return true|WP_Error
     */
    public static function validate_alias($alias, $lang, $type, $oid = 0, $rkey = '') {
        global $wpdb;

        if ($alias === '') return new WP_Error('empty_alias', 'Alias rỗng.');

        // 1. Trùng tiền tố của bất kỳ ngôn ngữ nào -> URL sẽ tranh nhau với định tuyến.
        foreach (Pi_Multilang_Core::get_language_config() as $code => $c) {
            $prefix = trim((string) $c['path_prefix'], '/');
            if ($prefix !== '' && $alias === $prefix) {
                return new WP_Error('alias_is_prefix', "Alias \"{$alias}\" trùng tiền tố ngôn ngữ.");
            }
            if ($alias === $code) {
                return new WP_Error('alias_is_lang_code', "Alias \"{$alias}\" trùng mã ngôn ngữ.");
            }
        }

        // 2. Đoạn URL dành riêng của WordPress + slug cấu trúc của theme.
        if (in_array($alias, self::reserved_segments(), true)) {
            return new WP_Error('alias_reserved', "Alias \"{$alias}\" trùng đoạn URL dành riêng.");
        }

        // 3. Trùng một alias active khác trong cùng (ngôn ngữ, loại).
        $table = self::table();
        $row = $wpdb->get_row($wpdb->prepare(
            "SELECT object_id, route_key FROM {$table}
             WHERE lang=%s AND object_type=%s AND alias=%s AND status='active' LIMIT 1",
            $lang, $type, $alias
        ));
        if ($row) {
            $same = ($type === 'route') ? ($row->route_key === $rkey) : ((int) $row->object_id === (int) $oid);
            if (!$same) {
                return new WP_Error('alias_taken', "Alias \"{$alias}\" đã thuộc về đối tượng khác.");
            }
        }

        return true;
    }

    /**
     * Đoạn URL không được dùng làm alias. Lấy động từ chính rewrite slug đang
     * đăng ký, KHÔNG chép tay — theme đổi slug CPT thì danh sách này đổi theo.
     */
    public static function reserved_segments() {
        $out = [
            'wp-admin', 'wp-content', 'wp-includes', 'wp-json', 'wp-login', 'wp-cron',
            'xmlrpc', 'feed', 'page', 'comments', 'embed', 'author', 'category', 'tag',
            'search', 'sitemap', 'robots', 'favicon', 'index',
        ];
        foreach (get_post_types(['public' => true], 'objects') as $pt) {
            if (!empty($pt->rewrite['slug'])) $out[] = trim($pt->rewrite['slug'], '/');
            if (is_string($pt->has_archive) && $pt->has_archive !== '') $out[] = trim($pt->has_archive, '/');
        }
        foreach (get_taxonomies([], 'objects') as $tx) {
            if (!empty($tx->rewrite['slug'])) $out[] = trim($tx->rewrite['slug'], '/');
        }
        return array_values(array_unique(array_filter($out)));
    }

    /* ================================================================== *
     * Nạp sẵn các đoạn cấu trúc
     * ================================================================== */

    /**
     * Đưa 9 mục của `sgh_bilingual_slug_map()` vào bảng dưới dạng hàng `route`.
     *
     * Đây là cách thoát khỏi mảng cặp [vi, en] chỉ có 2 cột: mỗi (route_key,
     * lang) là một dòng, thêm tiếng Nhật là thêm dòng, không đụng cấu trúc.
     *
     * Cột [0] (tiếng Việt) KHÔNG được ghi vào bảng — nó là slug đăng ký của
     * rewrite rule, bất biến, và luôn đóng vai "slug gốc" trong chỉ mục.
     */
    public static function seed_routes() {
        if (!function_exists('sgh_bilingual_slug_map')) {
            return new WP_Error('no_map', 'Theme chưa nạp sgh_bilingual_slug_map().');
        }
        self::ensure_table();

        $map    = sgh_bilingual_slug_map();
        $config = Pi_Multilang_Core::get_language_config();
        $seeded = 0;
        $bo_qua = [];

        foreach ($map as $key => $pair) {
            if (!is_array($pair) || !isset($pair[0])) continue;
            foreach ($config as $lang => $_c) {
                // Ngôn ngữ gốc không cần alias — nó CHÍNH là slug gốc
                if ($lang === 'vi') continue;

                $raw_alias = '';
                if ($lang === 'en' && isset($pair[1])) {
                    $raw_alias = $pair[1];
                } elseif (isset($pair[$lang])) {
                    $raw_alias = $pair[$lang];
                }

                $alias = self::normalize_alias($raw_alias);
                if ($alias === '' || $alias === self::normalize_alias($pair[0])) continue;
                $r = self::set_alias([
                    'object_type' => 'route',
                    'route_key'   => $key,
                    'lang'        => $lang,
                    'alias'       => $alias,
                    'source'      => 'import',
                ]);
                if ($r === true) $seeded++;
            }
        }

        self::flush_index();
        return [
            'seeded'  => $seeded,
            'skipped' => $bo_qua,
        ];
    }

    /* ================================================================== *
     * Chỉ mục đọc
     * ================================================================== */

    public static function flush_index() {
        delete_option(self::INDEX_OPTION);
        wp_cache_delete(self::INDEX_OPTION, 'pi_multilang');
    }

    /**
     * Chỉ mục phẳng, khoá đã tiền-nối để tra bằng isset() — O(1), không loop.
     *
     * Chỉ nạp route + page + post + project + term. 586 product CỐ Ý đứng ngoài:
     * nhét vào làm option phồng gấp ba trong khi trang product ngôn ngữ phụ là
     * thiểu số. Chúng được tra thẳng CSDL khi cần (xem resolve_alias_db).
     */
    public static function get_index() {
        static $memo = null;
        if ($memo !== null) return $memo;

        $cached = wp_cache_get(self::INDEX_OPTION, 'pi_multilang');
        if (is_array($cached) && isset($cached['v']) && $cached['v'] === self::INDEX_VERSION) {
            return $memo = $cached;
        }

        $idx = get_option(self::INDEX_OPTION, null);
        if (!is_array($idx) || !isset($idx['v']) || $idx['v'] !== self::INDEX_VERSION) {
            $idx = self::build_index();
            update_option(self::INDEX_OPTION, $idx, false);   // autoload = no
        }

        wp_cache_set(self::INDEX_OPTION, $idx, 'pi_multilang', 300);
        return $memo = $idx;
    }

    /**
     * Dựng lại chỉ mục từ bảng.
     *
     * Hai luật quan trọng:
     *  - Chỉ nhận đối tượng `publish` và có `pi_language` RỖNG hoặc BẰNG ngôn ngữ
     *    của alias. Nếu không, phân giải sẽ đúng nhưng `pre_get_posts` lọc theo
     *    pi_language (class-frontend.php:34) loại mất bài → 404 câm.
     *  - `old` được GIẢI CHUỖI SẴN: a→b→c thì old[a] lưu thẳng c. Nhờ vậy không
     *    bao giờ có 301 nối 301, theo cấu trúc chứ không theo kỷ luật.
     */
    public static function build_index() {
        global $wpdb;

        // `canon`: alias -> slug gốc, gộp mọi ngôn ngữ. Đây là bảng tra cho
        // to_canonical_segment(), tách riêng để không phải quét `in`.
        $idx = ['v' => self::INDEX_VERSION, 'built' => time(),
                'in' => [], 'out' => [], 'old' => [], 'canon' => []];

        if ((int) get_option(self::SCHEMA_OPTION, 0) < self::SCHEMA_VERSION) return $idx;

        $table = self::table();
        $rows  = $wpdb->get_results("SELECT * FROM {$table} ORDER BY id ASC");
        if (!$rows) return $idx;

        $map = function_exists('sgh_bilingual_slug_map') ? sgh_bilingual_slug_map() : [];

        // slug gốc theo từng đối tượng, và bộ lọc ngôn ngữ hợp lệ
        $canonical = [];   // "type|id" hoặc "route|key" => slug gốc
        $allowed   = [];   // "type|id" => [lang hợp lệ] hoặc true nếu dùng chung

        foreach ($rows as $r) {
            if ($r->object_type === 'route') {
                $k = 'route|' . $r->route_key;
                if (!isset($canonical[$k])) {
                    $canonical[$k] = isset($map[$r->route_key][0])
                        ? self::normalize_alias($map[$r->route_key][0]) : '';
                }
            } elseif ($r->object_type === 'term') {
                $k = 'term|' . (int) $r->object_id;
                if (!array_key_exists($k, $canonical)) {
                    $term = get_term((int) $r->object_id);
                    if (!$term || is_wp_error($term)) {
                        $canonical[$k] = '';
                        $allowed[$k]   = [];
                    } else {
                        $canonical[$k] = $term->slug;
                        $allowed[$k]   = true;
                    }
                }
            } else {
                $k = $r->object_type . '|' . (int) $r->object_id;
                if (!array_key_exists($k, $canonical)) {
                    $post = get_post((int) $r->object_id);
                    if (!$post || $post->post_status !== 'publish') {
                        $canonical[$k] = '';          // mồ côi: bỏ khỏi mọi chiều
                        $allowed[$k]   = [];
                    } else {
                        $canonical[$k] = $post->post_name;
                        $lang_meta = (string) get_post_meta($post->ID, 'pi_language', true);
                        $has_tr = get_post_meta($post->ID, '_pi_tr_en_title', true) || get_post_meta($post->ID, '_pi_tr_ja_title', true);
                        $allowed[$k] = ($has_tr || $lang_meta === '') ? true : [$lang_meta];
                    }
                }
            }
        }

        foreach ($rows as $r) {
            $isRoute = ($r->object_type === 'route');
            $isTerm  = ($r->object_type === 'term');
            $k = $isRoute ? 'route|' . $r->route_key : ($isTerm ? 'term|' . (int) $r->object_id : $r->object_type . '|' . (int) $r->object_id);
            $canon = isset($canonical[$k]) ? $canonical[$k] : '';
            if ($canon === '' || $r->alias === '' || $r->alias === $canon) continue;

            // Bài gắn ngôn ngữ khác thì alias vô nghĩa — pre_get_posts sẽ loại nó.
            if (!$isRoute && !$isTerm && isset($allowed[$k]) && $allowed[$k] !== true
                && !in_array($r->lang, (array) $allowed[$k], true)) {
                continue;
            }

            $type = $isRoute ? 'route' : ($isTerm ? 'term' : 'post');
            $inK  = $r->lang . '|' . $type . '|' . $r->alias;

            // Alias nào cũng quy về được slug gốc, kể cả alias đã về hưu — nhờ
            // vậy dịch một path đã dịch rồi vẫn ra đúng kết quả.
            // Route và Term được ưu tiên tuyệt đối; Post/Product không được đè lên Route/Term
            // và không được chiếm một slug vốn đang là canonical của đối tượng khác.
            if ($isRoute || $isTerm) {
                $idx['canon'][$r->alias] = $canon;
            } elseif (!isset($idx['canon'][$r->alias]) && !in_array($r->alias, $canonical, true)) {
                $idx['canon'][$r->alias] = $canon;
            }

            if ($r->status === 'active') {
                $idx['in'][$inK]  = ($isRoute || $isTerm) ? $canon : (int) $r->object_id;
                $idx['out'][$r->lang . '|' . $type . '|' . $canon] = $r->alias;
            } else {
                // Điền sau vòng này, khi đã biết alias active cuối cùng.
                $idx['old'][$inK] = ['k' => $k, 'lang' => $r->lang, 'type' => $type];
            }
        }

        // Giải chuỗi `old` -> alias ACTIVE hiện tại, một bước.
        foreach ($idx['old'] as $key => $meta) {
            $canon = isset($canonical[$meta['k']]) ? $canonical[$meta['k']] : '';
            if ($canon === '') { unset($idx['old'][$key]); continue; }
            $outK = $meta['lang'] . '|' . $meta['type'] . '|' . $canon;
            $to   = isset($idx['out'][$outK]) ? $idx['out'][$outK] : $canon;
            // Alias cũ nay lại là alias active của đối tượng khác -> `in` thắng.
            if (isset($idx['in'][$key])) { unset($idx['old'][$key]); continue; }
            $idx['old'][$key] = ['to' => $to, 'canon' => $canon];
        }

        return $idx;
    }

    /* ================================================================== *
     * Tra cứu
     * ================================================================== */

    /** Alias -> slug gốc (hoặc ID với bài). null nếu không có. */
    public static function resolve_in($alias, $lang, $type = 'post') {
        $idx = self::get_index();
        $k = $lang . '|' . $type . '|' . $alias;
        return isset($idx['in'][$k]) ? $idx['in'][$k] : null;
    }

    /** Slug gốc -> alias của ngôn ngữ. Trả chính slug gốc nếu chưa có alias. */
    public static function resolve_out($canonical, $lang, $type = 'post') {
        $idx = self::get_index();
        $k = $lang . '|' . $type . '|' . $canonical;
        return isset($idx['out'][$k]) ? $idx['out'][$k] : $canonical;
    }

    /** Alias đã về hưu -> alias hiện hành. null nếu không phải slug cũ. */
    public static function resolve_old($alias, $lang, $type = 'post') {
        $idx = self::get_index();
        $k = $lang . '|' . $type . '|' . $alias;
        return isset($idx['old'][$k]) ? $idx['old'][$k] : null;
    }

    /**
     * Tầng đuôi dài: product không nằm trong chỉ mục, tra thẳng CSDL.
     * Một SELECT có index, CHỈ gọi khi ngữ cảnh đã cho biết là product.
     */
    public static function resolve_alias_db($alias, $lang, $post_type) {
        global $wpdb;
        if ((int) get_option(self::SCHEMA_OPTION, 0) < self::SCHEMA_VERSION) return null;

        $table = self::table();
        $id = $wpdb->get_var($wpdb->prepare(
            "SELECT object_id FROM {$table}
             WHERE lang=%s AND object_type=%s AND alias=%s AND status='active' LIMIT 1",
            $lang, $post_type, $alias
        ));
        return $id ? (int) $id : null;
    }

    /* ================================================================== *
     * Dịch đường dẫn
     * ================================================================== */

    /**
     * Dịch mọi đoạn của một path sang alias của $lang.
     *
     * IDEMPOTENT THEO CẤU TRÚC: luôn quy đoạn về slug GỐC trước rồi mới ra alias
     * đích, nên f(f(x)) === f(x) với mọi x. Đây là điều kiện bắt buộc, không phải
     * tối ưu: seo-schema.php và breadcrumbs.php gọi home_url($_SERVER['REQUEST_URI'])
     * tức là hàm này bị gọi trên URL vốn đã qua xử lý (xem class-core.php:355).
     */
    public static function translate_path_to_lang($path, $lang) {
        $path = (string) $path;
        if ($path === '' || $path === '/') return $path;

        $lead  = (substr($path, 0, 1) === '/');
        $trail = (substr($path, -1) === '/');
        $segs  = array_values(array_filter(explode('/', trim($path, '/')), 'strlen'));
        if (!$segs) return $path;

        $idx = self::get_index();
        $out = [];

        foreach ($segs as $seg) {
            $canon = self::to_canonical_segment($seg, $idx);
            $done  = false;
            foreach (['route', 'post', 'term'] as $type) {
                $k = $lang . '|' . $type . '|' . $canon;
                if (isset($idx['out'][$k])) { $out[] = $idx['out'][$k]; $done = true; break; }
            }
            if (!$done) $out[] = $canon;
        }

        $res = implode('/', $out);
        return ($lead ? '/' : '') . $res . ($trail ? '/' : '');
    }

    /**
     * Đưa một đoạn về slug gốc, bất kể nó đang là alias của ngôn ngữ nào.
     * Đây là nửa đầu làm nên tính idempotent.
     *
     * Tra bảng `canon` dựng sẵn chứ KHÔNG quét `in`: hàm này chạy cho từng đoạn
     * của từng liên kết trên trang (một trang danh sách có hàng chục liên kết),
     * nên quét tuyến tính 840 mục sẽ thành hàng trăm nghìn phép so mỗi request.
     */
    private static function to_canonical_segment($seg, $idx) {
        return isset($idx['canon'][$seg]) ? $idx['canon'][$seg] : $seg;
    }
}
