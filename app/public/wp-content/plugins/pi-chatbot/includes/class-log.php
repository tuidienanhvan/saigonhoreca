<?php
/**
 * Log hội thoại — ghi lại mỗi câu hỏi THẬT (không phải cache hit) cùng việc có tìm
 * thấy context hay không, để soi ra khoảng trống dữ liệu bằng SQL thuần thay vì
 * phán đoán.
 *
 * Cảm hứng từ pattern MemoryProvider.sync_turn() của Hermes Agent (NousResearch) —
 * ghi lại mỗi lượt hội thoại để tầng "curator" sau này gom thành insight — nhưng
 * đơn giản hoá triệt để cho đúng quy mô pi-chatbot: Hermes chạy 1 process dài hạn
 * nên sync_turn() phải né executor riêng + timeout để không treo turn đang chạy.
 * pi-chatbot là request/response 1 lần của PHP-FPM, 1 câu INSERT tốn <5ms — không
 * cần thread nền, không cần circuit breaker. "Curator" ở đây cũng không phải LLM
 * tóm tắt hội thoại (tốn tiền, tốn 8s/lần) mà là 1 câu GROUP BY tìm câu hỏi lặp lại
 * nhiều lần mà 0 kết quả — cùng mục đích (biến log thô thành tín hiệu học được),
 * khác công cụ.
 *
 * @package PiChatbot
 */

if (!defined('ABSPATH')) exit;

class Pi_Chatbot_Log {

    const DB_VERSION = 1;

    /** Giữ log tối đa bao lâu — bảng tự bounded, không cần cron riêng. */
    const RETENTION_DAYS = 180;

    public static function table_name() {
        global $wpdb;
        return $wpdb->prefix . 'pi_chatbot_log';
    }

    /** Cùng pattern maybe_upgrade() của Indexer — so DB_VERSION, dbDelta nếu lệch. */
    public static function maybe_upgrade() {
        if ((int) get_option('pi_chatbot_log_db_version') === self::DB_VERSION) return;
        self::create_table();
        update_option('pi_chatbot_log_db_version', self::DB_VERSION, false);
    }

    public static function on_activate() {
        self::create_table();
        update_option('pi_chatbot_log_db_version', self::DB_VERSION, false);
    }

    public static function create_table() {
        global $wpdb;
        $table = self::table_name();
        $charset_collate = $wpdb->get_charset_collate();

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        // FULLTEXT trên question_norm để sau này có thể tự dò lại log (kiểu FTS5 của
        // Hermes) — chưa có UI dùng tới, nhưng thêm ngay bây giờ rẻ hơn ALTER TABLE
        // sau này trên bảng đã có dữ liệu.
        $sql = "CREATE TABLE {$table} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            question_norm VARCHAR(500) NOT NULL,
            had_context TINYINT(1) NOT NULL DEFAULT 0,
            source_count TINYINT UNSIGNED NOT NULL DEFAULT 0,
            router_used TINYINT(1) NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL,
            PRIMARY KEY (id),
            KEY had_context_created (had_context, created_at),
            FULLTEXT KEY question_ft (question_norm)
        ) {$charset_collate} ENGINE=InnoDB;";
        dbDelta($sql);
    }

    /**
     * Ghi 1 lượt hỏi. KHÔNG BAO GIỜ được làm hỏng đường trả lời khách — thất bại thì
     * lặng lẽ bỏ qua (đúng nguyên tắc "provider failure never blocks the turn" của
     * MemoryManager bên Hermes, chỉ khác là ở đây không cần thread nền vì bản thân
     * request đã đồng bộ và insert rẻ).
     */
    public static function record($question_norm, $had_context, $source_count, $router_used) {
        global $wpdb;
        $question_norm = trim((string) $question_norm);
        if ($question_norm === '') return;

        $wpdb->insert(
            self::table_name(),
            [
                'question_norm' => mb_substr($question_norm, 0, 500),
                'had_context'   => $had_context ? 1 : 0,
                'source_count'  => min(255, max(0, (int) $source_count)),
                'router_used'   => $router_used ? 1 : 0,
                'created_at'    => current_time('mysql'),
            ],
            ['%s', '%d', '%d', '%d', '%s']
        );

        // Dọn log cũ cơ hội chủ nghĩa (giống cách WP core dọn transient hết hạn) —
        // 1/500 lượt ghi thay vì cron riêng, đủ để bảng không phình vô hạn ở quy mô
        // vài trăm câu hỏi/ngày mà không cần thêm hạ tầng.
        if (wp_rand(1, 500) === 1) {
            self::prune();
        }
    }

    private static function prune() {
        global $wpdb;
        $wpdb->query($wpdb->prepare(
            "DELETE FROM " . self::table_name() . " WHERE created_at < %s",
            gmdate('Y-m-d H:i:s', time() - self::RETENTION_DAYS * DAY_IN_SECONDS)
        ));
    }

    /**
     * Câu hỏi lặp lại nhiều lần nhất mà KHÔNG tìm thấy context — tín hiệu khoảng
     * trống dữ liệu/từ khoá thật, đo được thay vì đoán. Đây là "curator" của
     * pi-chatbot: không cần LLM tóm tắt, GROUP BY trên dữ liệu thô là đủ.
     */
    public static function top_unanswered($limit = 15, $days = 30) {
        global $wpdb;
        $table = self::table_name();
        return $wpdb->get_results($wpdb->prepare(
            "SELECT question_norm, COUNT(*) AS cnt, MAX(created_at) AS last_seen
             FROM {$table}
             WHERE had_context = 0 AND created_at >= %s
             GROUP BY question_norm
             ORDER BY cnt DESC, last_seen DESC
             LIMIT %d",
            gmdate('Y-m-d H:i:s', time() - max(1, (int) $days) * DAY_IN_SECONDS),
            max(1, (int) $limit)
        ));
    }

    /** Số liệu tổng quan cho trang admin. */
    public static function stats() {
        global $wpdb;
        $table = self::table_name();
        $day_ago = gmdate('Y-m-d H:i:s', time() - DAY_IN_SECONDS);

        return [
            'total'               => (int) $wpdb->get_var("SELECT COUNT(*) FROM {$table}"),
            'total_unanswered'    => (int) $wpdb->get_var("SELECT COUNT(*) FROM {$table} WHERE had_context = 0"),
            'distinct_unanswered' => (int) $wpdb->get_var("SELECT COUNT(DISTINCT question_norm) FROM {$table} WHERE had_context = 0"),
            'last_24h'            => (int) $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$table} WHERE created_at >= %s", $day_ago
            )),
        ];
    }
}
