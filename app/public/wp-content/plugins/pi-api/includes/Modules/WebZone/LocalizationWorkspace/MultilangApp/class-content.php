<?php
if (!defined('ABSPATH')) exit;

/**
 * Chuyển ngữ NỘI DUNG trên CÙNG MỘT bản ghi.
 *
 * Đây là lựa chọn kiến trúc, không phải tiện tay: bản cũ
 * (class-translation.php, đã gỡ 29/08/2026) gọi wp_insert_post() để đẻ ra một
 * bài riêng cho mỗi ngôn ngữ. Chính cơ chế đó đã sinh 612 page trắng hồi
 * 27/07→01/08/2026, và với 528 product × 3 ngôn ngữ thì nó nhân kho bài lên
 * gấp ba. Ở đây bản dịch là postmeta gắn vào bài gốc: xoá bài là bản dịch đi
 * theo (core tự dọn postmeta), post_count không đổi, và không có khái niệm
 * "bài dịch bị mồ côi".
 *
 * Ba tầng meta cho mỗi (bài, ngôn ngữ):
 *   _pi_trd_{lang}_*  BẢN NHÁP  — AI hoặc người vừa soạn, chưa ai thấy
 *   _pi_tr_{lang}_*   BẢN SỐNG  — cái frontend đang hiển thị
 *   _pi_tro_{lang}_*  BẢN CŨ    — ảnh chụp trước lần áp gần nhất, để hoàn tác
 *
 * Cùng vòng đời draft → apply → undo với lô slug (class-slug-batch.php), để
 * người dùng chỉ phải học một mô hình.
 */
class Pi_Multilang_Content {

    const FIELDS = ['title', 'content', 'excerpt', 'seo_title', 'seo_description', 'image_alt'];

    /**
     * Trường lấy nguồn từ POST META chứ không từ bảng posts.
     *
     * Theme đọc SEO qua sgh_pi_seo_aliases() (inc/site/pi-seo-resolver.php), ưu
     * tiên `_pi_seo_*`. Không dịch hai trường này thì trang `/ja/` dịch đủ nội
     * dung nhưng thẻ <title> và mô tả trên Google vẫn tiếng Việt — tức là phần
     * người dùng nhìn thấy TRƯỚC khi bấm vào vẫn chưa dịch.
     */
    const META_MAP = [
        'seo_title'       => '_pi_seo_title',
        'seo_description' => '_pi_seo_description',
        'image_alt'       => '_wp_attachment_image_alt',
    ];

    /** Khoá cũ theme vẫn còn đọc ở vài chỗ — lọc luôn cho khỏi lệch. */
    const META_ALIAS = [
        '_seo_title'       => 'seo_title',
        '_seo_description' => 'seo_description',
        '_pi_seo_desc'     => 'seo_description',
    ];

    /** Giá trị GỐC của một trường: bảng posts hoặc post meta. */
    public static function source_value($post_id, $field) {
        $post = get_post((int) $post_id);
        if (!$post) return '';
        if ($field === 'title')   return (string) $post->post_title;
        if ($field === 'content') return (string) $post->post_content;
        if ($field === 'excerpt') return (string) $post->post_excerpt;
        if (isset(self::META_MAP[$field])) {
            $v = get_post_meta((int) $post_id, self::META_MAP[$field], true);
            return is_string($v) ? $v : '';
        }
        return '';
    }

    /** Loại bài được phép chuyển ngữ. Lọc được để khỏi sửa file khi thêm CPT. */
    public static function post_types() {
        // nav_menu_item: nhan menu go tay nam o post_title cua chinh no, va
        // Walker_Nav_Menu chay qua filter `the_title` — nen chi can co mat trong
        // danh sach nay la dich duoc, khong phai hook rieng.
        // attachment: chua alt anh (xem META_MAP).
        return apply_filters('pi_multilang_translatable_post_types',
            ['post', 'page', 'project', 'product', 'nav_menu_item', 'attachment']);
    }

    /* ================================================================== *
     * Khoá meta
     * ================================================================== */

    private static function key($stage, $lang, $field) {
        $lang = preg_replace('/[^a-z0-9\-]/', '', strtolower((string) $lang));
        return '_pi_' . $stage . '_' . $lang . '_' . $field;
    }

    public static function live_key($lang, $field)  { return self::key('tr',  $lang, $field); }
    public static function draft_key($lang, $field) { return self::key('trd', $lang, $field); }
    public static function old_key($lang, $field)   { return self::key('tro', $lang, $field); }

    /* ================================================================== *
     * Ngôn ngữ mặc định
     * ================================================================== */

    /**
     * Ngôn ngữ mặc định của host đang chạy = ngôn ngữ có tiền tố RỖNG.
     *
     * KHÔNG dùng cờ is_default riêng: production có hai mặc định cùng lúc
     * (vi trên .vn, en trên .com), một cờ boolean không diễn tả nổi. Tiền tố
     * rỗng là định nghĩa duy nhất tự nhất quán.
     */
    public static function default_lang() {
        $config = Pi_Multilang_Core::get_language_config();
        foreach ($config as $code => $c) {
            if (empty($c['path_prefix'])) return $code;
        }
        $codes = array_keys($config);
        return $codes ? $codes[0] : '';
    }

    /** Bài gốc viết bằng ngôn ngữ nào — thẻ pi_language, không có thì lấy mặc định. */
    public static function source_lang($post_id) {
        $own = get_post_meta($post_id, 'pi_language', true);
        if (is_string($own) && $own !== '') return $own;
        return self::default_lang();
    }

    /* ================================================================== *
     * Đọc / ghi
     * ================================================================== */

    /** Bản dịch SỐNG của một trường. '' nghĩa là chưa có. */
    public static function live($post_id, $lang, $field) {
        $v = get_post_meta((int) $post_id, self::live_key($lang, $field), true);
        return is_string($v) ? $v : '';
    }

    public static function draft($post_id, $lang, $field) {
        $v = get_post_meta((int) $post_id, self::draft_key($lang, $field), true);
        return is_string($v) ? $v : '';
    }

    /**
     * Ghi bản nháp. Trường không truyền thì giữ nguyên; truyền chuỗi rỗng là
     * XOÁ nháp của trường đó (khác nhau, nên phải dùng array_key_exists).
     */
    public static function save_draft($post_id, $lang, array $fields) {
        $post_id = (int) $post_id;
        if (!get_post($post_id)) return new WP_Error('not_found', 'Không tìm thấy bài.', ['status' => 404]);
        if (!self::lang_ok($lang))  return new WP_Error('bad_lang', 'Ngôn ngữ không có trong cấu hình.', ['status' => 400]);
        if ($lang === self::source_lang($post_id)) {
            return new WP_Error('same_lang', 'Không dịch bài sang chính ngôn ngữ gốc của nó.', ['status' => 400]);
        }

        foreach (self::FIELDS as $f) {
            if (!array_key_exists($f, $fields)) continue;
            $val = (string) $fields[$f];
            $key = self::draft_key($lang, $f);
            if ($val === '') { delete_post_meta($post_id, $key); continue; }
            update_post_meta($post_id, $key, wp_slash(self::sanitize_field($f, $val)));
        }
        return true;
    }

    /**
     * Nội dung bài đi qua wp_kses_post giống hệt đường lưu bài thường — bản
     * dịch KHÔNG được là lối đi vòng để nhét thẻ script vào bài.
     */
    private static function sanitize_field($field, $val) {
        // SEO title/description đi thẳng vào thẻ <title> và <meta> — không được
        // mang HTML, khác với content.
        if (isset(self::META_MAP[$field])) return sanitize_text_field((string) $val);

        if ($field === 'content') return wp_kses_post($val);
        return sanitize_text_field(wp_strip_all_tags($val));
    }

    private static function lang_ok($lang) {
        $config = Pi_Multilang_Core::get_language_config();
        return $lang !== '' && isset($config[$lang]);
    }

    /* ================================================================== *
     * Vòng đời: áp / hoàn tác
     * ================================================================== */

    /** Nháp -> sống. Chụp bản sống hiện tại sang tầng "cũ" trước khi ghi đè. */
    public static function apply($post_id, $lang) {
        $post_id = (int) $post_id;
        if (!get_post($post_id)) return new WP_Error('not_found', 'Không tìm thấy bài.', ['status' => 404]);
        if (!self::lang_ok($lang)) return new WP_Error('bad_lang', 'Ngôn ngữ không có trong cấu hình.', ['status' => 400]);

        $applied = [];
        foreach (self::FIELDS as $f) {
            $d = self::draft($post_id, $lang, $f);
            if ($d === '') continue;

            // Ảnh chụp phải ghi CẢ KHI bản sống đang rỗng — nếu không, hoàn tác
            // một bản dịch mới tinh sẽ không có gì để khôi phục và giá trị mới
            // ở lại vĩnh viễn. Chuỗi rỗng vẫn là một trạng thái cần nhớ.
            update_post_meta($post_id, self::old_key($lang, $f), wp_slash(self::live($post_id, $lang, $f)));
            update_post_meta($post_id, self::live_key($lang, $f), wp_slash($d));
            delete_post_meta($post_id, self::draft_key($lang, $f));
            $applied[] = $f;
        }
        if (!$applied) return new WP_Error('empty_draft', 'Không có bản nháp nào để áp.', ['status' => 400]);

        // Ảnh chụp phải mô tả ĐÚNG lần áp này, không phải lần nào đó trước đây.
        // Bỏ bước dọn này thì: áp lần 1 (tiêu đề + nội dung), áp lần 2 (chỉ tiêu
        // đề), rồi hoàn tác — nội dung cũng bị kéo về trạng thái trước lần 1,
        // dù lần 2 không hề đụng tới nó. Đã đo đúng ca này.
        foreach (self::FIELDS as $f) {
            if (in_array($f, $applied, true)) continue;
            delete_post_meta($post_id, self::old_key($lang, $f));
        }

        self::after_write($post_id);
        return $applied;
    }

    /** Trả về bản sống ngay trước lần áp gần nhất. Chỉ lùi được MỘT bước. */
    public static function undo($post_id, $lang) {
        $post_id = (int) $post_id;
        $restored = [];
        foreach (self::FIELDS as $f) {
            $key = self::old_key($lang, $f);
            // metadata_exists phân biệt "chưa từng áp" với "trước đó rỗng".
            if (!metadata_exists('post', $post_id, $key)) continue;
            $old = get_post_meta($post_id, $key, true);

            // Nháp đang trống thì trả giá trị vừa bị thay về làm nháp, để công
            // sức dịch không bốc hơi chỉ vì bấm nhầm nút hoàn tác.
            if (self::draft($post_id, $lang, $f) === '') {
                $cur = self::live($post_id, $lang, $f);
                if ($cur !== '') update_post_meta($post_id, self::draft_key($lang, $f), wp_slash($cur));
            }

            if ($old === '') delete_post_meta($post_id, self::live_key($lang, $f));
            else update_post_meta($post_id, self::live_key($lang, $f), wp_slash($old));

            delete_post_meta($post_id, $key);
            $restored[] = $f;
        }
        if (!$restored) return new WP_Error('nothing_to_undo', 'Chưa có lần áp nào để hoàn tác.', ['status' => 400]);

        self::after_write($post_id);
        return $restored;
    }

    private static function after_write($post_id) {
        clean_post_cache($post_id);
        if (class_exists('Pi_Multilang_Slug_Batch')) Pi_Multilang_Slug_Batch::flush_site_caches();
    }

    /* ================================================================== *
     * Thống kê phủ sóng
     * ================================================================== */

    /**
     * Đếm bằng MỘT câu lệnh cho tất cả ngôn ngữ, thay vì lặp ngôn ngữ × loại
     * bài. Với 728 bài publish thì cách lặp tốn 24 truy vấn cho một màn hình.
     */
    public static function coverage() {
        global $wpdb;
        $types = self::post_types();
        if (!$types) return [];

        $ph = implode(',', array_fill(0, count($types), '%s'));
        $rows = $wpdb->get_results($wpdb->prepare(
            "SELECT p.post_type, m.meta_key, COUNT(DISTINCT p.ID) c
               FROM {$wpdb->posts} p
               JOIN {$wpdb->postmeta} m ON m.post_id = p.ID
              WHERE p.post_status = 'publish'
                AND p.post_type IN ({$ph})
                AND m.meta_key LIKE '\\_pi\\_tr%\\_title'
                AND m.meta_value <> ''
              GROUP BY p.post_type, m.meta_key",
            $types
        ));

        $totals = [];
        foreach ($wpdb->get_results($wpdb->prepare(
            "SELECT post_type, COUNT(*) c FROM {$wpdb->posts}
              WHERE post_status='publish' AND post_type IN ({$ph}) GROUP BY post_type", $types
        )) as $r) $totals[$r->post_type] = (int) $r->c;

        $out = [];
        foreach ($types as $t) {
            $out[$t] = ['total' => isset($totals[$t]) ? $totals[$t] : 0, 'langs' => []];
        }
        foreach ($rows as $r) {
            if (!preg_match('/^_pi_(tr|trd)_([a-z0-9\-]+)_title$/', $r->meta_key, $m)) continue;
            $stage = $m[1] === 'tr' ? 'live' : 'draft';
            $lang  = $m[2];
            if (!isset($out[$r->post_type])) continue;
            if (!isset($out[$r->post_type]['langs'][$lang])) {
                $out[$r->post_type]['langs'][$lang] = ['live' => 0, 'draft' => 0];
            }
            $out[$r->post_type]['langs'][$lang][$stage] = (int) $r->c;
        }
        return $out;
    }

    /* ================================================================== *
     * Hiển thị
     * ================================================================== */

    public static function init() {
        add_filter('the_title',         [__CLASS__, 'filter_title'], 10, 2);
        add_filter('single_post_title', [__CLASS__, 'filter_single_title'], 10, 2);
        add_filter('the_content',       [__CLASS__, 'filter_content'], 9);
        add_filter('get_the_excerpt',   [__CLASS__, 'filter_excerpt'], 10, 2);
        // Lọc ở tầng đọc meta chứ không hook riêng từng chỗ theme dùng: theme
        // đọc SEO qua nhiều hàm khác nhau, chặn ở đây thì chỗ nào cũng đúng.
        add_filter('get_post_metadata', [__CLASS__, 'filter_seo_meta'], 10, 4);
    }

    /**
     * Ngôn ngữ đang xem có phải ngôn ngữ CẦN dịch không.
     *
     * Chốt is_admin() nằm ở đây chứ không ở init(): wp-admin và REST vẫn phải
     * thấy đúng nội dung GỐC, nếu không thì người biên tập mở bài ra sẽ thấy
     * bản tiếng Anh nằm trong ô soạn thảo tiếng Việt và lưu đè lên bài gốc.
     */
    private static function display_lang() {
        if (is_admin() || wp_doing_ajax() || wp_doing_cron()) return '';
        if (defined('REST_REQUEST') && REST_REQUEST) return '';
        if (!defined('PI_CURRENT_LANGUAGE')) return '';
        return (string) PI_CURRENT_LANGUAGE;
    }

    private static function value_for($post_id, $field) {
        $lang = self::display_lang();
        if ($lang === '' || !$post_id) return null;
        if ($lang === self::source_lang($post_id)) return null;
        $v = self::live($post_id, $lang, $field);
        return $v === '' ? null : $v;
    }

    public static function filter_title($title, $post_id = 0) {
        $v = self::value_for((int) $post_id, 'title');
        return $v === null ? $title : $v;
    }

    public static function filter_single_title($title, $post = null) {
        $id = ($post instanceof WP_Post) ? $post->ID : get_queried_object_id();
        $v = self::value_for((int) $id, 'title');
        return $v === null ? $title : $v;
    }

    /**
     * prio 9 — TRƯỚC wpautop (10) và shortcode (11), đúng chỗ mà nội dung gốc
     * được đưa vào, nên bản dịch đi qua y hệt chuỗi xử lý của bản gốc.
     */
    public static function filter_content($content) {
        $v = self::value_for((int) get_the_ID(), 'content');
        if ($v !== null && (str_contains($v, '[div') || str_contains($v, '[/div]'))) {
            $v = preg_replace('/\[div(\s+[^\]]*)?\]/i', '<div$1>', $v);
            $v = str_ireplace('[/div]', '</div>', $v);
        }
        return $v === null ? $content : $v;
    }

    public static function filter_excerpt($excerpt, $post = null) {
        $id = ($post instanceof WP_Post) ? $post->ID : get_the_ID();
        $v = self::value_for((int) $id, 'excerpt');
        return $v === null ? $excerpt : $v;
    }

    /**
     * Trả bản dịch cho meta SEO khi đang xem ngôn ngữ khác.
     *
     * Cờ $dang_doc chặn đệ quy: self::live() cũng gọi get_post_meta, tức là
     * quay lại chính bộ lọc này. Không có cờ thì tràn stack ngay lần đọc đầu.
     */
    public static function filter_seo_meta($value, $object_id, $meta_key, $single) {
        static $dang_doc = false;
        if ($dang_doc || $value !== null) return $value;

        $field = array_search($meta_key, self::META_MAP, true);
        if ($field === false) $field = self::META_ALIAS[$meta_key] ?? false;
        if ($field === false) return $value;

        $dang_doc = true;
        $v = self::value_for((int) $object_id, $field);
        $dang_doc = false;

        if ($v === null || $v === '') return $value;
        return $single ? $v : [$v];
    }
}
