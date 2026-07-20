<?php
/**
 * Indexer — bảng FULLTEXT nội bộ cho retrieval (không embedding/vector DB).
 *
 * @package PiChatbot
 */

if (!defined('ABSPATH')) exit;

class Pi_Chatbot_Indexer {
    private static $instance = null;

    public static function instance() {
        if (self::$instance === null) self::$instance = new self();
        return self::$instance;
    }

    /**
     * Phiên bản schema — tăng số này khi đổi cấu trúc bảng để maybe_upgrade() chạy
     * dbDelta trên site đang cài sẵn. Trước đây create_table() CHỈ chạy lúc activate,
     * nên mọi thay đổi schema đều đòi deactivate/activate thủ công (chính là lý do
     * gốc khiến bảng entities của SAG không bao giờ được tạo, kéo theo RENAME TABLE
     * fail nguyên khối và reindex hỏng im lặng).
     */
    const DB_VERSION = 2;

    /** Ngưỡng độ dài tối thiểu để index — chỉ áp cho post_type khác 'product'. */
    const MIN_INDEXABLE_LENGTH = 100;

    /** Post types được index — khớp CPT thật của theme (product/project) + page thường. */
    public static function indexable_post_types() {
        return ['product', 'project', 'page'];
    }

    /**
     * Hook 'plugins_loaded' — đồng bộ schema khi option version lệch DB_VERSION.
     * CỐ Ý không tự gọi reindex_all() ở đây: reindex là việc nặng, phải do người dùng
     * chủ động bấm nút trong wp-admin (bài học từ SAG — nhét job nặng vào hook tự chạy
     * là cách nhanh nhất để treo cả site).
     */
    public static function maybe_upgrade() {
        if ((int) get_option('pi_chatbot_db_version') === self::DB_VERSION) return;
        self::create_table();
        update_option('pi_chatbot_db_version', self::DB_VERSION, false);
    }

    public static function table_name() {
        global $wpdb;
        return $wpdb->prefix . PI_CHATBOT_TABLE;
    }

    public static function on_activate() {
        self::create_table();
        update_option('pi_chatbot_db_version', self::DB_VERSION, false);
        $result = self::reindex_all();
        // Activation không hard-fail nếu reindex lỗi (site vẫn nên kích hoạt được,
        // admin có thể bấm Reindex thủ công sau) — nhưng phải ghi log, không được
        // im lặng như cách reindex_all() từng bị (xem docblock ở đó).
        if (is_wp_error($result)) {
            error_log('[pi-chatbot] on_activate reindex thất bại: ' . $result->get_error_message());
        }
    }

    public static function create_table() {
        self::create_table_at(self::table_name());
    }

    private static function create_table_at($table) {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        $sql = "CREATE TABLE {$table} (
            post_id BIGINT UNSIGNED NOT NULL,
            post_type VARCHAR(20) NOT NULL,
            title TEXT NOT NULL,
            brand VARCHAR(190) DEFAULT '',
            category VARCHAR(190) DEFAULT '',
            url VARCHAR(500) NOT NULL,
            content_text LONGTEXT NOT NULL,
            updated_at DATETIME NOT NULL,
            PRIMARY KEY (post_id),
            FULLTEXT KEY content_ft (title, brand, category, content_text)
        ) {$charset_collate} ENGINE=InnoDB;";
        dbDelta($sql);
    }

    /**
     * Quét lại toàn bộ nội dung publish. Trả về số bản ghi đã index.
     *
     * Nạp vào bảng SHADOW rồi RENAME TABLE atomic thay vì TRUNCATE bảng đang phục
     * vụ rồi nạp lại dần (bug thật đã tìm thấy khi audit: TRUNCATE mở ra 1 khoảng
     * thời gian bảng HOÀN TOÀN RỖNG suốt quá trình nạp lại ~419 bản ghi — khách
     * chat đúng lúc đó nhận context rỗng dù dữ liệu thật vẫn còn nguyên, chỉ là
     * đang được nạp lại). RENAME TABLE nhiều bảng trong 1 câu lệnh là atomic ở
     * MySQL — bảng chính chỉ "biến mất" trong khoảnh khắc đổi tên metadata, không
     * có khoảng trống rỗng nào khách có thể chạm phải.
     */
    /**
     * @return int|WP_Error Số bản ghi đã index, hoặc WP_Error nếu swap thất bại.
     *
     * Trước đây KHÔNG có hàm nào ở đây kiểm tra lỗi — nếu RENAME TABLE fail (đúng
     * kiểu lỗi đã làm hỏng plugin lần trước: rename tham chiếu bảng không tồn tại,
     * atomic fail nguyên khối), code vẫn chạy tiếp, ghi last_reindex/index_count
     * mới rồi báo "reindex thành công" trong khi thực chất KHÔNG có gì được swap.
     * Giờ mọi query thô đều kiểm tra, cộng 2 cổng chặn:
     *   - Cổng 1: xác nhận bảng shadow THẬT SỰ tồn tại sau create_table_at().
     *   - Cổng 2: $count === 0 thì HUỶ swap, giữ nguyên index cũ — 419+ bài publish
     *     đang có mà quét ra 0 chỉ có thể là lỗi upstream, không phải kết quả hợp lệ.
     * Cố ý KHÔNG thêm cổng "giảm quá X%": bỏ publish hàng loạt là thao tác hợp lệ,
     * cổng nào bắn nhầm vào thao tác hợp lệ sẽ bị admin tắt ngay từ lần đầu.
     */
    public static function reindex_all() {
        global $wpdb;
        $table = self::table_name();
        $shadow = $table . '_shadow';
        $old = $table . '_old';

        if ($wpdb->query("DROP TABLE IF EXISTS {$shadow}") === false) {
            return new WP_Error('pi_chatbot_reindex_failed', 'Không xoá được bảng shadow cũ: ' . $wpdb->last_error);
        }
        self::create_table_at($shadow);
        if ((string) $wpdb->get_var("SHOW TABLES LIKE '{$shadow}'") !== $shadow) {
            return new WP_Error('pi_chatbot_reindex_failed', 'Bảng shadow không được tạo thành công.');
        }

        $count = 0;
        foreach (self::indexable_post_types() as $post_type) {
            $ids = get_posts([
                'post_type'      => $post_type,
                'post_status'    => 'publish',
                'posts_per_page' => -1,
                'fields'         => 'ids',
            ]);
            foreach ($ids as $post_id) {
                if (self::index_post_into($shadow, $post_id)) $count++;
            }
        }

        if ($count === 0) {
            $wpdb->query("DROP TABLE IF EXISTS {$shadow}");
            return new WP_Error('pi_chatbot_reindex_empty', 'Reindex dựng được 0 bản ghi — huỷ swap, giữ nguyên index cũ.');
        }

        $wpdb->query("DROP TABLE IF EXISTS {$old}");
        if ($wpdb->query("RENAME TABLE {$table} TO {$old}, {$shadow} TO {$table}") === false) {
            $err = $wpdb->last_error;
            $wpdb->query("DROP TABLE IF EXISTS {$shadow}");
            return new WP_Error('pi_chatbot_reindex_failed', 'RENAME TABLE thất bại: ' . $err);
        }
        $wpdb->query("DROP TABLE IF EXISTS {$old}");

        // Chỉ ghi bookkeeping SAU KHI swap đã xác nhận thành công.
        update_option('pi_chatbot_last_reindex', current_time('mysql'));
        update_option('pi_chatbot_index_count', $count);
        self::bump_index_version();
        // Pi_Chatbot_Entities luôn được require_once trước khi hook nào chạy (load
        // order cố định ở pi-chatbot.php) — không cần class_exists() phòng thủ.
        Pi_Chatbot_Entities::flush_cache();
        return $count;
    }

    /**
     * Bộ đếm toàn cục dùng làm 1 phần khoá cache câu trả lời (xem class-rest.php).
     * Trước đây cache chỉ hết hạn khi reindex_all() chạy — save_post (sửa 1 bài lẻ)
     * không hề bump last_reindex, nên sửa giá/thông số 1 sản phẩm xong khách vẫn
     * nhận câu trả lời + nguồn cũ tới 1 giờ. Autoload=true vì option này đọc mỗi
     * request chat (ngược với các option chứa API key — cố ý autoload=false).
     */
    private static function bump_index_version() {
        $v = (int) get_option('pi_chatbot_index_version', 0);
        update_option('pi_chatbot_index_version', $v + 1, true);
    }

    /** Index/refresh 1 bài vào bảng CHÍNH — dùng cho hook save_post (cập nhật tăng dần). */
    public static function index_post($post_id) {
        return self::index_post_into(self::table_name(), $post_id);
    }

    /** Dựng dữ liệu 1 bài và ghi (REPLACE) vào $table chỉ định — dùng chung cho
     *  cập nhật tăng dần (index_post, bảng chính) và reindex toàn bộ (bảng shadow). */
    private static function index_post_into($table, $post_id) {
        $post = get_post($post_id);
        if (!$post || $post->post_status !== 'publish') return false;
        if (!in_array($post->post_type, self::indexable_post_types(), true)) return false;

        $brand = '';
        $category = '';
        if ($post->post_type === 'product') {
            $brand    = self::term_names($post_id, 'product_brand');
            $category = self::term_names($post_id, 'product_category');
        } elseif ($post->post_type === 'project') {
            $category = self::term_names($post_id, 'project_category');
        }

        $body = wp_strip_all_tags(strip_shortcodes($post->post_content));
        $excerpt = wp_strip_all_tags($post->post_excerpt);
        // title/brand/category KHÔNG nối vào content_text nữa (audit 2026-07-18 flag:
        // 3 cột đó đã có riêng và cùng nằm trong FULLTEXT KEY content_ft — nối thêm
        // vào content_text khiến token của chúng bị đếm 2 lần, lệch điểm relevance
        // nhẹ so với các trường chỉ xuất hiện 1 lần).
        $content_text = trim($excerpt . ' ' . $body);
        // FULLTEXT MySQL bỏ qua từ ngắn hơn min_word_length mặc định (4) — không cắt bớt
        // ở đây, để nguyên toàn văn cho retriever tự MATCH theo natural language mode.

        // Trang/dự án gần như rỗng chỉ làm nhiễu FULLTEXT — không mang thông tin gì
        // nhưng vẫn khớp token trong title và chiếm slot top-K. Đo trên dữ liệu
        // thật: 3/8 trang page = 0 ký tự, 3/8 trang khác (giỏ hàng) = 79 ký tự —
        // ngưỡng 100 cắt đúng 6 trang vô dụng, giữ 2 trang có nội dung thật.
        // KHÔNG áp cho product: spec ngắn (vd 89 ký tự: kích thước/khối lượng/sức
        // chứa) vẫn là câu trả lời hợp lệ, không phải rác.
        if ($post->post_type !== 'product' && mb_strlen(trim($content_text)) < self::MIN_INDEXABLE_LENGTH) {
            return false;
        }

        // Taxonomy để trống thì bóc brand từ tiêu đề (vd "Bàn Đông FT-158MA-S - Hoshizaki").
        // CHỈ lấp chỗ trống — taxonomy luôn thắng, nên bước này không bao giờ ghi đè dữ
        // liệu người dùng đã gắn tay. Đo trên dữ liệu thật: 271/280 sản phẩm đang trống
        // brand có tên brand nằm sẵn trong tiêu đề, và cả 271 đều khớp đúng 1 brand duy
        // nhất (không nhập nhằng) — độ phủ brand đi từ 26% lên 98% mà không tốn LLM nào.
        if ($brand === '' && $post->post_type === 'product') {
            $detected = Pi_Chatbot_Entities::detect_brand_from_title($post->post_title);
            if ($detected) $brand = $detected;
        }

        global $wpdb;
        $wpdb->replace($table, [
            'post_id'      => $post_id,
            'post_type'    => $post->post_type,
            'title'        => $post->post_title,
            'brand'        => $brand,
            'category'     => $category,
            'url'          => get_permalink($post_id),
            'content_text' => $content_text,
            'updated_at'   => current_time('mysql'),
        ], ['%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s']);

        // Chỉ bump version khi ghi vào bảng CHÍNH (save_post tăng dần) — reindex_all()
        // tự bump 1 lần sau khi swap xong, không cần bump lặp lại 419 lần trong lúc
        // build bảng shadow.
        if ($table === self::table_name()) self::bump_index_version();

        return true;
    }

    public static function remove_post($post_id) {
        global $wpdb;
        $wpdb->delete(self::table_name(), ['post_id' => $post_id], ['%d']);
        self::bump_index_version();
    }

    private static function term_names($post_id, $taxonomy) {
        if (!taxonomy_exists($taxonomy)) return '';
        $terms = get_the_terms($post_id, $taxonomy);
        if (!$terms || is_wp_error($terms)) return '';
        return implode(', ', wp_list_pluck($terms, 'name'));
    }

    /** Hook save_post — cập nhật đúng 1 dòng thay vì reindex toàn bộ. */
    public static function on_save_post($post_id, $post, $update) {
        if (wp_is_post_autosave($post_id) || wp_is_post_revision($post_id)) return;
        if (!in_array($post->post_type, self::indexable_post_types(), true)) return;

        if ($post->post_status === 'publish') {
            self::index_post($post_id);
        } else {
            self::remove_post($post_id);
        }
        Pi_Chatbot_Entities::flush_cache();
    }

    public static function on_delete_post($post_id) {
        self::remove_post($post_id);
    }
}
