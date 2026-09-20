<?php
if (!defined('ABSPATH')) exit;

/**
 * Chuyển ngữ TÊN và MÔ TẢ chuyên mục (taxonomy term).
 *
 * Vì sao cần lớp riêng: bản dịch bài viết (class-content.php) chỉ phủ post. Tên
 * chuyên mục nằm ở bảng terms, mà nó xuất hiện khắp nơi người dùng đi qua —
 * menu, breadcrumb, bộ lọc, tiêu đề trang lưu trữ. Thiếu nó thì trang `/en/` đã
 * dịch hết bài vẫn còn 80 chuyên mục sản phẩm tiếng Việt nằm giữa giao diện Anh.
 *
 * Mô hình giống hệt bài viết, cố ý: bản dịch là termmeta trên CHÍNH term gốc,
 * không đẻ term mới. Đẻ term mới sẽ làm hỏng quan hệ bài↔chuyên mục và nhân đôi
 * cây phân cấp. Vòng đời nháp → áp → hoàn tác cũng giống, để người dùng chỉ phải
 * học một mô hình cho cả bài lẫn chuyên mục.
 */
class Pi_Multilang_Term {

    const FIELDS = ['name', 'description'];

    /** Taxonomy được phép chuyển ngữ. Lọc được để khỏi sửa file khi thêm mới. */
    public static function taxonomies() {
        return apply_filters('pi_multilang_translatable_taxonomies', [
            'product_category', 'product_brand', 'project_category', 'category', 'post_tag',
        ]);
    }

    private static function key($stage, $lang, $field) {
        $lang = preg_replace('/[^a-z0-9\-]/', '', strtolower((string) $lang));
        return '_pi_' . $stage . '_' . $lang . '_' . $field;
    }

    public static function live_key($lang, $field)  { return self::key('tr',  $lang, $field); }
    public static function draft_key($lang, $field) { return self::key('trd', $lang, $field); }
    public static function old_key($lang, $field)   { return self::key('tro', $lang, $field); }

    public static function default_lang() {
        return Pi_Multilang_Content::default_lang();
    }

    private static function lang_ok($lang) {
        $config = Pi_Multilang_Core::get_language_config();
        return $lang !== '' && isset($config[$lang]);
    }

    public static function live($term_id, $lang, $field) {
        $v = get_term_meta((int) $term_id, self::live_key($lang, $field), true);
        return is_string($v) ? $v : '';
    }

    public static function draft($term_id, $lang, $field) {
        $v = get_term_meta((int) $term_id, self::draft_key($lang, $field), true);
        return is_string($v) ? $v : '';
    }

    /* ================================================================== *
     * Ghi
     * ================================================================== */

    public static function save_draft($term_id, $lang, array $fields) {
        $term_id = (int) $term_id;
        if (!get_term($term_id)) return new WP_Error('not_found', 'Không tìm thấy chuyên mục.', ['status' => 404]);
        if (!self::lang_ok($lang)) return new WP_Error('bad_lang', 'Ngôn ngữ không có trong cấu hình.', ['status' => 400]);

        $saved = [];
        foreach (self::FIELDS as $f) {
            if (!array_key_exists($f, $fields)) continue;
            $val = (string) $fields[$f];
            // Tên chuyên mục đi vào <title>, breadcrumb và thẻ option — không
            // được mang HTML. Mô tả thì cho phép đúng bộ thẻ của post content.
            $val = ($f === 'name') ? sanitize_text_field($val) : wp_kses_post($val);
            update_term_meta($term_id, self::draft_key($lang, $f), wp_slash($val));
            $saved[] = $f;
        }
        return $saved;
    }

    /** Nháp -> sống, chụp bản sống hiện tại sang tầng "cũ" trước khi ghi đè. */
    public static function apply($term_id, $lang) {
        $term_id = (int) $term_id;
        if (!get_term($term_id)) return new WP_Error('not_found', 'Không tìm thấy chuyên mục.', ['status' => 404]);
        if (!self::lang_ok($lang)) return new WP_Error('bad_lang', 'Ngôn ngữ không có trong cấu hình.', ['status' => 400]);

        $applied = [];
        foreach (self::FIELDS as $f) {
            $d = self::draft($term_id, $lang, $f);
            if ($d === '') continue;
            // Chụp CẢ KHI bản sống đang rỗng — nếu không, hoàn tác một bản dịch
            // mới tinh sẽ không có gì để khôi phục. Giống hệt class-content.php.
            update_term_meta($term_id, self::old_key($lang, $f), wp_slash(self::live($term_id, $lang, $f)));
            update_term_meta($term_id, self::live_key($lang, $f), wp_slash($d));
            delete_term_meta($term_id, self::draft_key($lang, $f));
            $applied[] = $f;
        }
        if (!$applied) return new WP_Error('empty_draft', 'Không có bản nháp nào để áp.', ['status' => 400]);

        // Ảnh chụp phải mô tả ĐÚNG lần áp này (xem ghi chú cùng chỗ ở class-content).
        foreach (self::FIELDS as $f) {
            if (in_array($f, $applied, true)) continue;
            delete_term_meta($term_id, self::old_key($lang, $f));
        }

        self::after_write($term_id);
        return $applied;
    }

    /** Lùi đúng MỘT bước về bản sống ngay trước lần áp gần nhất. */
    public static function undo($term_id, $lang) {
        $term_id = (int) $term_id;
        $restored = [];
        foreach (self::FIELDS as $f) {
            $key = self::old_key($lang, $f);
            if (!metadata_exists('term', $term_id, $key)) continue;
            $old = get_term_meta($term_id, $key, true);

            // Giữ lại công sức dịch: nháp trống thì đẩy bản vừa bị thay về nháp.
            if (self::draft($term_id, $lang, $f) === '') {
                $cur = self::live($term_id, $lang, $f);
                if ($cur !== '') update_term_meta($term_id, self::draft_key($lang, $f), wp_slash($cur));
            }

            if ($old === '') delete_term_meta($term_id, self::live_key($lang, $f));
            else update_term_meta($term_id, self::live_key($lang, $f), wp_slash($old));

            delete_term_meta($term_id, $key);
            $restored[] = $f;
        }
        if (!$restored) return new WP_Error('nothing_to_undo', 'Chưa có lần áp nào để hoàn tác.', ['status' => 400]);

        self::after_write($term_id);
        return $restored;
    }

    private static function after_write($term_id) {
        clean_term_cache((int) $term_id);
        if (class_exists('Pi_Multilang_Slug_Batch')) Pi_Multilang_Slug_Batch::flush_site_caches();
    }

    /* ================================================================== *
     * Đọc cho dashboard
     * ================================================================== */

    public static function listing($args = []) {
        $lang     = isset($args['lang']) ? (string) $args['lang'] : '';
        $taxonomy = isset($args['taxonomy']) ? (string) $args['taxonomy'] : '';
        $state    = isset($args['state']) ? (string) $args['state'] : '';
        $search   = isset($args['search']) ? (string) $args['search'] : '';
        $page     = max(1, (int) ($args['page'] ?? 1));
        $per_page = min(200, max(1, (int) ($args['per_page'] ?? 50)));

        $taxes = self::taxonomies();
        if ($taxonomy !== '' && in_array($taxonomy, $taxes, true)) $taxes = [$taxonomy];

        $terms = get_terms([
            'taxonomy'   => $taxes,
            'hide_empty' => false,
            'search'     => $search,
        ]);
        if (is_wp_error($terms)) return $terms;

        $items = [];
        foreach ($terms as $t) {
            $live  = self::live($t->term_id, $lang, 'name');
            $draft = self::draft($t->term_id, $lang, 'name');
            $st = $live !== '' ? 'live' : ($draft !== '' ? 'draft' : 'missing');
            if ($state !== '' && $st !== $state) continue;

            $parent_id   = (int) $t->parent;
            $ancestors   = $parent_id > 0 ? get_ancestors($t->term_id, $t->taxonomy, 'taxonomy') : [];
            $parent_name = $parent_id > 0 ? get_term_field('name', $parent_id, $t->taxonomy) : '';

            $items[] = [
                'id'          => (int) $t->term_id,
                'taxonomy'    => $t->taxonomy,
                'name'        => $t->name,
                'slug'        => $t->slug,
                'count'       => (int) $t->count,
                'state'       => $st,
                'translated'  => $live !== '' ? $live : $draft,
                'parent_id'   => $parent_id,
                'parent_name' => is_string($parent_name) ? $parent_name : '',
                'depth'       => count($ancestors),
                'ancestors'   => $ancestors,
            ];
        }

        $total  = count($items);
        $offset = ($page - 1) * $per_page;

        return [
            'ok'       => true,
            'items'    => array_slice($items, $offset, $per_page),
            'total'    => $total,
            'page'     => $page,
            'pages'    => max(1, (int) ceil($total / $per_page)),
            'per_page' => $per_page,
        ];
    }

    public static function detail($term_id, $lang) {
        $t = get_term((int) $term_id);
        if (!$t || is_wp_error($t)) {
            return new WP_Error('not_found', 'Không tìm thấy chuyên mục.', ['status' => 404]);
        }
        $fields = [];
        foreach (self::FIELDS as $f) {
            $fields[$f] = [
                'source'   => $f === 'name' ? $t->name : $t->description,
                'draft'    => self::draft($t->term_id, $lang, $f) ?: null,
                'live'     => self::live($t->term_id, $lang, $f) ?: null,
                'undoable' => metadata_exists('term', (int) $t->term_id, self::old_key($lang, $f)),
            ];
        }
        return [
            'ok'        => true,
            'id'        => (int) $t->term_id,
            'taxonomy'  => $t->taxonomy,
            'slug'      => $t->slug,
            'lang'      => $lang,
            'permalink' => get_term_link($t) instanceof WP_Error ? '' : get_term_link($t),
            'fields'    => $fields,
        ];
    }

    /** Đếm phủ sóng theo taxonomy × ngôn ngữ, tính chính xác số lượng term không bị nhân bản. */
    public static function coverage() {
        global $wpdb;
        $taxes = self::taxonomies();
        if (empty($taxes)) return ['ok' => true, 'coverage' => []];

        $ph = implode(',', array_fill(0, count($taxes), '%s'));

        // 1. Đếm tổng số term phân biệt cho từng taxonomy
        $total_rows = $wpdb->get_results($wpdb->prepare(
            "SELECT taxonomy, COUNT(DISTINCT term_id) AS total
               FROM {$wpdb->term_taxonomy}
              WHERE taxonomy IN ({$ph})
              GROUP BY taxonomy",
            $taxes
        ));

        $out = [];
        foreach ($taxes as $tx) {
            $out[$tx] = ['total' => 0, 'langs' => []];
        }
        foreach ($total_rows as $tr) {
            if (isset($out[$tr->taxonomy])) {
                $out[$tr->taxonomy]['total'] = (int) $tr->total;
            }
        }

        // 2. Đếm số term đã có bản dịch theo từng ngôn ngữ và trạng thái (live/draft)
        $meta_rows = $wpdb->get_results($wpdb->prepare(
            "SELECT tt.taxonomy, tm.meta_key, COUNT(DISTINCT tt.term_id) c
               FROM {$wpdb->term_taxonomy} tt
               INNER JOIN {$wpdb->termmeta} tm
                       ON tm.term_id = tt.term_id
                      AND tm.meta_key LIKE '\\_pi\\_tr%%\\_name'
                      AND TRIM(COALESCE(tm.meta_value, '')) <> ''
              WHERE tt.taxonomy IN ({$ph})
              GROUP BY tt.taxonomy, tm.meta_key",
            $taxes
        ));

        foreach ($meta_rows as $r) {
            $tx = $r->taxonomy;
            if (!isset($out[$tx])) continue;
            if (preg_match('/^_pi_(tr|trd)_([a-z0-9\-]+)_name$/', $r->meta_key, $m)) {
                $stage = $m[1] === 'tr' ? 'live' : 'draft';
                $lang  = $m[2];
                if (!isset($out[$tx]['langs'][$lang])) {
                    $out[$tx]['langs'][$lang] = ['live' => 0, 'draft' => 0];
                }
                $out[$tx]['langs'][$lang][$stage] = (int) $r->c;
            }
        }
        return ['ok' => true, 'taxonomies' => $taxes, 'coverage' => $out];
    }

    /* ================================================================== *
     * Hiển thị
     * ================================================================== */

    public static function init() {
        add_filter('get_term',           [__CLASS__, 'filter_term'], 10, 2);
        add_filter('get_terms',          [__CLASS__, 'filter_terms'], 10, 3);
        add_filter('single_term_title',  [__CLASS__, 'filter_single_title']);
        add_filter('term_description',   [__CLASS__, 'filter_description'], 10, 2);
    }

    /**
     * Ngôn ngữ đang hiển thị. Chốt is_admin/REST giống class-content.php: trang
     * quản trị phải thấy tên GỐC, nếu không người biên tập sẽ sửa đè lên bản
     * tiếng Việt bằng chữ tiếng Anh.
     */
    private static function display_lang() {
        if (is_admin() || wp_doing_ajax() || wp_doing_cron()) return '';
        if (defined('REST_REQUEST') && REST_REQUEST) return '';
        if (!defined('PI_CURRENT_LANGUAGE')) return '';
        $lang = (string) PI_CURRENT_LANGUAGE;
        return ($lang === self::default_lang()) ? '' : $lang;
    }

    private static function value_for($term_id, $field) {
        $lang = self::display_lang();
        if ($lang === '' || !$term_id) return null;
        $v = self::live($term_id, $lang, $field);
        return $v === '' ? null : $v;
    }

    public static function filter_term($term, $taxonomy = '') {
        if (!($term instanceof WP_Term)) return $term;
        if (!in_array($term->taxonomy, self::taxonomies(), true)) return $term;

        $name = self::value_for($term->term_id, 'name');
        if ($name !== null) $term->name = $name;
        $desc = self::value_for($term->term_id, 'description');
        if ($desc !== null) $term->description = $desc;
        return $term;
    }

    /**
     * get_terms trả mảng — có thể là WP_Term, có thể chỉ là id hoặc slug tuỳ
     * tham số `fields`. Chỉ đụng phần tử thật sự là WP_Term.
     */
    public static function filter_terms($terms, $taxonomies = [], $args = []) {
        if (!is_array($terms)) return $terms;
        foreach ($terms as $k => $t) {
            if ($t instanceof WP_Term) $terms[$k] = self::filter_term($t, $t->taxonomy);
        }
        return $terms;
    }

    public static function filter_single_title($title) {
        $obj = get_queried_object();
        if (!($obj instanceof WP_Term)) return $title;
        if (!in_array($obj->taxonomy, self::taxonomies(), true)) return $title;
        $v = self::value_for($obj->term_id, 'name');
        return $v === null ? $title : $v;
    }

    public static function filter_description($desc, $term_id = 0) {
        $id = (int) $term_id ?: get_queried_object_id();
        $v = self::value_for($id, 'description');
        return $v === null ? $desc : $v;
    }
}
