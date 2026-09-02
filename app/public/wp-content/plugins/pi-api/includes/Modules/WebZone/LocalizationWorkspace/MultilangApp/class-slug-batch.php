<?php
if (!defined('ABSPATH')) exit;

/**
 * Lô thay đổi URL: xem trước -> áp -> hoàn tác.
 *
 * Vì sao phải có lô thay vì ghi thẳng: đổi slug là đổi URL công khai, và đội vận
 * hành đã có ba tiền sử hỏng dữ liệu âm thầm (metabox gắn nhầm pi_language, vòng
 * dịch đẻ 612 bản nháp, theme tự bật lại plugin). Máy sinh đề xuất, nhưng người
 * giữ nút áp và nút lùi — đó là điều kiện đã chốt trong hồ sơ khả thi (trục O).
 *
 * `nguyen_trang` lưu JSON trạng thái TRƯỚC khi áp, đủ để dựng lại nguyên vẹn.
 * Không suy ngược từ lịch sử slug: suy ngược là đoán, còn ảnh chụp là sự thật.
 */
class Pi_Multilang_Slug_Batch {

    const TABLE          = 'pi_multilang_slug_batches';
    const SCHEMA_OPTION  = 'pi_multilang_batches_schema';
    const SCHEMA_VERSION = 1;

    public static function table() {
        global $wpdb;
        return $wpdb->prefix . self::TABLE;
    }

    public static function ensure_table() {
        if ((int) get_option(self::SCHEMA_OPTION, 0) >= self::SCHEMA_VERSION) return;

        global $wpdb;
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        $table   = self::table();
        $collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE {$table} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            kind VARCHAR(20) NOT NULL DEFAULT 'slug',
            status VARCHAR(20) NOT NULL DEFAULT 'draft',
            lang VARCHAR(12) NOT NULL DEFAULT '',
            created_by BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            applied_at DATETIME NULL,
            undone_at DATETIME NULL,
            item_count INT NOT NULL DEFAULT 0,
            items LONGTEXT NULL,
            snapshot LONGTEXT NULL,
            note TEXT NULL,
            PRIMARY KEY  (id),
            KEY status (status),
            KEY lang (lang)
        ) {$collate};";

        dbDelta($sql);
        update_option(self::SCHEMA_OPTION, self::SCHEMA_VERSION, false);
    }

    /**
     * Tạo lô nháp từ danh sách đề xuất.
     *
     * @param array $items mỗi phần tử: object_type, object_id|route_key, lang,
     *                     alias, title, current_alias, risk[]
     */
    public static function create(array $items, $lang, $note = '') {
        self::ensure_table();
        global $wpdb;

        $ok = $wpdb->insert(self::table(), [
            'kind'       => 'slug',
            'status'     => 'draft',
            'lang'       => $lang,
            'created_by' => get_current_user_id(),
            'created_at' => current_time('mysql', true),
            'item_count' => count($items),
            'items'      => wp_json_encode(array_values($items)),
            'note'       => $note,
        ]);
        if ($ok === false) return new WP_Error('batch_insert_failed', $wpdb->last_error);
        return (int) $wpdb->insert_id;
    }

    public static function get($id) {
        self::ensure_table();
        global $wpdb;
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . self::table() . " WHERE id = %d", (int) $id));
        if (!$row) return null;
        $row->items    = $row->items ? json_decode($row->items, true) : [];
        $row->snapshot = $row->snapshot ? json_decode($row->snapshot, true) : [];
        return $row;
    }

    public static function listing($limit = 50, $offset = 0) {
        self::ensure_table();
        global $wpdb;
        $rows = $wpdb->get_results($wpdb->prepare(
            "SELECT id, kind, status, lang, created_by, created_at, applied_at, undone_at, item_count, note
             FROM " . self::table() . " WHERE kind = 'slug' ORDER BY id DESC LIMIT %d OFFSET %d",
            (int) $limit, (int) $offset
        ));
        return $rows ?: [];
    }

    /**
     * Áp lô. Chụp nguyên trạng TRƯỚC khi ghi dòng đầu tiên.
     *
     * Lỗi giữa chừng thì hoàn tác NGAY toàn lô — không bao giờ để lô áp một nửa,
     * vì nửa vời nghĩa là một phần URL đã đổi mà bảng 301 chưa đủ.
     */
    public static function apply($id) {
        $batch = self::get($id);
        if (!$batch) return new WP_Error('not_found', 'Không tìm thấy lô.', ['status' => 404]);
        // Bang nay dung chung voi lo go the ngon ngu (kind='untag', xem
        // class-audit.php). Ap nham se doc `items` sai dinh dang va ghi bay ba
        // vao bang alias, nen chan thang o day.
        if ($batch->kind !== 'slug') {
            return new WP_Error('wrong_kind', 'Lô này không phải lô slug.', ['status' => 409]);
        }
        if ($batch->status === 'applied') return new WP_Error('already_applied', 'Lô này đã áp rồi.', ['status' => 409]);
        if ($batch->status === 'undone')  return new WP_Error('already_undone', 'Lô này đã hoàn tác.', ['status' => 409]);

        global $wpdb;
        $snapshot = [];
        $applied  = 0;
        $errors   = [];

        foreach ((array) $batch->items as $it) {
            $snapshot[] = self::snapshot_of($it);
        }

        foreach ((array) $batch->items as $i => $it) {
            $res = Pi_Multilang_Slugs::set_alias([
                'object_type' => $it['object_type'] ?? 'post',
                'object_id'   => (int) ($it['object_id'] ?? 0),
                'route_key'   => (string) ($it['route_key'] ?? ''),
                'lang'        => (string) ($it['lang'] ?? $batch->lang),
                'alias'       => (string) ($it['alias'] ?? ''),
                'source'      => (string) ($it['source'] ?? 'ai'),
                'batch_id'    => (int) $id,
            ]);
            if (is_wp_error($res)) {
                $errors[] = ['index' => $i, 'alias' => $it['alias'] ?? '', 'error' => $res->get_error_message()];
                continue;
            }
            $applied++;
        }

        // Lỗi ở BẤT KỲ dòng nào -> lùi cả lô. Thà không áp gì còn hơn áp một nửa.
        if ($errors) {
            self::restore($snapshot);
            Pi_Multilang_Slugs::flush_index();
            return new WP_Error('apply_failed',
                'Áp lô thất bại ở ' . count($errors) . ' dòng, đã hoàn tác toàn bộ.',
                ['status' => 500, 'errors' => $errors]);
        }

        $wpdb->update(self::table(), [
            'status'     => 'applied',
            'applied_at' => current_time('mysql', true),
            'snapshot'   => wp_json_encode($snapshot),
        ], ['id' => (int) $id]);

        Pi_Multilang_Slugs::flush_index();
        self::flush_site_caches();

        return ['applied' => $applied, 'batch_id' => (int) $id];
    }

    public static function undo($id) {
        $batch = self::get($id);
        if (!$batch) return new WP_Error('not_found', 'Không tìm thấy lô.', ['status' => 404]);
        if ($batch->kind !== 'slug') {
            return new WP_Error('wrong_kind', 'Lô này không phải lô slug.', ['status' => 409]);
        }
        if ($batch->status !== 'applied') {
            return new WP_Error('not_applied', 'Chỉ hoàn tác được lô đã áp.', ['status' => 409]);
        }

        global $wpdb;
        self::restore((array) $batch->snapshot);

        // Dọn mọi dòng do chính lô này sinh ra, kể cả bản đã về hưu — nếu không
        // sẽ còn 301 trỏ tới alias vừa bị lùi.
        $wpdb->query($wpdb->prepare(
            "DELETE FROM " . Pi_Multilang_Slugs::table() . " WHERE batch_id = %d", (int) $id
        ));

        $wpdb->update(self::table(), [
            'status'    => 'undone',
            'undone_at' => current_time('mysql', true),
        ], ['id' => (int) $id]);

        Pi_Multilang_Slugs::flush_index();
        self::flush_site_caches();
        return ['undone' => true, 'batch_id' => (int) $id];
    }

    /** Ảnh chụp alias hiện tại của một mục. */
    private static function snapshot_of($it) {
        global $wpdb;
        $table = Pi_Multilang_Slugs::table();
        $type  = $it['object_type'] ?? 'post';
        $lang  = $it['lang'] ?? '';

        $where = ($type === 'route')
            ? $wpdb->prepare("object_type='route' AND route_key=%s AND lang=%s", (string) ($it['route_key'] ?? ''), $lang)
            : $wpdb->prepare("object_type=%s AND object_id=%d AND lang=%s", $type, (int) ($it['object_id'] ?? 0), $lang);

        $row = $wpdb->get_row("SELECT * FROM {$table} WHERE {$where} AND status='active' LIMIT 1", ARRAY_A);
        return [
            'object_type' => $type,
            'object_id'   => (int) ($it['object_id'] ?? 0),
            'route_key'   => (string) ($it['route_key'] ?? ''),
            'lang'        => $lang,
            'alias'       => $row ? $row['alias'] : null,   // null = trước đó KHÔNG có alias
            'source'      => $row ? $row['source'] : null,
        ];
    }

    /** Dựng lại đúng nguyên trạng đã chụp. */
    private static function restore(array $snapshot) {
        global $wpdb;
        $table = Pi_Multilang_Slugs::table();

        foreach ($snapshot as $s) {
            $where = ($s['object_type'] === 'route')
                ? $wpdb->prepare("object_type='route' AND route_key=%s AND lang=%s", $s['route_key'], $s['lang'])
                : $wpdb->prepare("object_type=%s AND object_id=%d AND lang=%s", $s['object_type'], (int) $s['object_id'], $s['lang']);

            $wpdb->query("DELETE FROM {$table} WHERE {$where}");

            if (!empty($s['alias'])) {
                $wpdb->insert($table, [
                    'object_type' => $s['object_type'],
                    'object_id'   => (int) $s['object_id'],
                    'route_key'   => (string) $s['route_key'],
                    'lang'        => $s['lang'],
                    'alias'       => $s['alias'],
                    'status'      => 'active',
                    'source'      => $s['source'] ?: 'manual',
                    'created_at'  => current_time('mysql', true),
                ]);
            }
        }
    }

    public static function flush_site_caches() {
        if (function_exists('sgh_static_cache_flush')) sgh_static_cache_flush();
        if (class_exists('Pi_Multilang_Legacy_Bridge')) Pi_Multilang_Legacy_Bridge::flush_home_caches();
        do_action('sgh_after_flush_cache');
    }
}
