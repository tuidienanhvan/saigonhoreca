<?php
if (!defined('ABSPATH')) exit;

/**
 * Rà soát và gỡ thẻ ngôn ngữ gắn nhầm (EP-12, EP-13 — UC-13).
 *
 * Bối cảnh: mô hình CŨ dịch bài bằng cách NHÂN BẢN bài sang ngôn ngữ khác, mỗi
 * bản mang meta `pi_language` riêng. Cách đó đã bỏ (đẻ 612 page trắng vì trang
 * dựng bằng template PHP có post_content rỗng) và thay bằng bản dịch nằm TRÊN
 * CÙNG một bản ghi (`_pi_tr_{lang}_{field}`, xem class-content.php). Tàn dư của
 * thời kỳ cũ là những bài còn đeo `pi_language` nhưng không có nội dung thật —
 * chúng vẫn lọt vào query công khai vì Pi_Multilang_Meta::filter_query lọc theo
 * đúng meta đó.
 *
 * Lớp này CHỈ nêu nghi vấn kèm bằng chứng, KHÔNG tự kết luận và KHÔNG tự xoá.
 * Một bài tiếng Nhật viết tay, không dịch từ đâu cả, cũng khớp mọi dấu hiệu —
 * chỉ người mới phân biệt được. Đúng nguyên tắc đã chốt ở hồ sơ khả thi: máy đề
 * xuất, người giữ nút.
 */
class Pi_Multilang_Audit {

    /** Lô gỡ thẻ dùng chung bảng với lô slug, phân biệt bằng cột `kind`. */
    const BATCH_KIND = 'untag';

    /** Bản đồ idempotency-key -> batch_id, để cron/retry không gỡ hai lần. */
    const IDEMPOTENCY_OPTION = 'pi_multilang_untag_idempotency';
    const IDEMPOTENCY_MAX    = 100;

    /**
     * Tìm bài đeo thẻ ngôn ngữ đáng ngờ.
     *
     * Ba dấu hiệu, trả kèm từng bài để người soát tự cân:
     *   - `not_in_config`  : thẻ trỏ ngôn ngữ không còn trong cấu hình — thẻ rác thật.
     *   - `empty_content`  : post_content rỗng — đúng vân tay của 612 page trắng.
     *   - `no_translation` : không có meta `_pi_tr_*` nào, tức không phải bản gốc
     *                        của nhóm dịch nào cả.
     *
     * Bài KHÔNG có `pi_language` là bài ngôn ngữ mặc định — bất biến của
     * class-meta.php, nên không bao giờ lọt vào đây.
     */
    public static function mistagged($limit = 100, $offset = 0) {
        global $wpdb;

        $limit  = max(1, min(500, (int) $limit));
        $offset = max(0, (int) $offset);

        $types = Pi_Multilang_Content::post_types();
        if (empty($types)) return ['items' => [], 'total' => 0, 'counts' => []];

        $ph_types = implode(',', array_fill(0, count($types), '%s'));

        // Ngôn ngữ mặc định không cần thẻ; bài đeo đúng thẻ mặc định cũng là dư
        // thừa nhưng vô hại, nên vẫn liệt kê để người soát thấy toàn cảnh.
        $configured = array_keys((array) Pi_Multilang_Core::get_language_config());

        $sql = $wpdb->prepare(
            "SELECT SQL_CALC_FOUND_ROWS p.ID, p.post_title, p.post_type, p.post_name,
                    p.post_status, p.post_date,
                    m.meta_value AS lang,
                    CHAR_LENGTH(TRIM(p.post_content)) AS content_len,
                    (SELECT COUNT(*) FROM {$wpdb->postmeta} t
                      WHERE t.post_id = p.ID AND t.meta_key LIKE '\\_pi\\_tr%%') AS tr_meta
               FROM {$wpdb->posts} p
               INNER JOIN {$wpdb->postmeta} m ON m.post_id = p.ID AND m.meta_key = 'pi_language'
              WHERE p.post_type IN ({$ph_types})
                AND p.post_status NOT IN ('trash', 'auto-draft')
                AND TRIM(COALESCE(m.meta_value, '')) <> ''
              ORDER BY p.ID DESC
              LIMIT %d OFFSET %d",
            array_merge($types, [$limit, $offset])
        );

        $rows  = $wpdb->get_results($sql) ?: [];
        $total = (int) $wpdb->get_var('SELECT FOUND_ROWS()');

        $counts = ['not_in_config' => 0, 'empty_content' => 0, 'no_translation' => 0];
        $items  = [];

        foreach ($rows as $r) {
            $signals = [];
            if (!in_array($r->lang, $configured, true)) $signals[] = 'not_in_config';
            if ((int) $r->content_len === 0)            $signals[] = 'empty_content';
            if ((int) $r->tr_meta === 0)                $signals[] = 'no_translation';

            foreach ($signals as $s) $counts[$s]++;

            $items[] = [
                'id'        => (int) $r->ID,
                'title'     => $r->post_title,
                'post_type' => $r->post_type,
                'slug'      => $r->post_name,
                'status'    => $r->post_status,
                'lang'      => $r->lang,
                'permalink' => get_permalink($r->ID) ?: '',
                'edit_link' => get_edit_post_link($r->ID, 'raw') ?: '',
                'signals'   => $signals,
                // Ba dấu hiệu cùng lúc thì gần như chắc là tàn dư nhân bản, nhưng
                // vẫn để người bấm — xem ghi chú đầu lớp.
                'confidence' => count($signals) >= 3 ? 'cao' : (count($signals) === 2 ? 'trung bình' : 'thấp'),
            ];
        }

        return [
            'items'      => $items,
            'total'      => $total,
            'limit'      => $limit,
            'offset'     => $offset,
            'counts'     => $counts,
            'configured' => $configured,
        ];
    }

    /**
     * Gỡ thẻ `pi_language` cho các bài được chọn, ghi lô hoàn tác được.
     *
     * KHÔNG xoá bài, KHÔNG đụng nội dung: chỉ bỏ thẻ để bài rơi về ngôn ngữ mặc
     * định. Sai thì hoàn tác lại đúng giá trị cũ nhờ ảnh chụp trong lô.
     */
    public static function untag(array $ids, $idempotency_key = '', $note = '') {
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));
        if (empty($ids)) {
            return new WP_Error('empty_selection', 'Chưa chọn bài nào để gỡ thẻ.', ['status' => 422]);
        }

        $key = trim((string) $idempotency_key);
        if ($key === '') {
            return new WP_Error('missing_idempotency_key', 'Thiếu idempotency_key.', ['status' => 422]);
        }

        // Cùng khoá thì trả lại kết quả cũ. Cron và nút bấm hai lần đều đi qua
        // đây, mà gỡ lần hai sẽ chụp nguyên trạng RỖNG và giết luôn đường lùi.
        $seen = (array) get_option(self::IDEMPOTENCY_OPTION, []);
        if (isset($seen[$key])) {
            $batch = Pi_Multilang_Slug_Batch::get($seen[$key]);
            if ($batch) {
                return [
                    'ok' => true, 'batch_id' => (int) $batch->id,
                    'untagged' => (int) $batch->item_count, 'replayed' => true,
                ];
            }
        }

        Pi_Multilang_Slug_Batch::ensure_table();
        global $wpdb;

        $snapshot = [];
        foreach ($ids as $id) {
            $lang = get_post_meta($id, 'pi_language', true);
            if ($lang === '' || $lang === false) continue;   // đã sạch, bỏ qua
            $snapshot[] = ['id' => $id, 'lang' => (string) $lang];
        }

        if (empty($snapshot)) {
            return ['ok' => true, 'batch_id' => 0, 'untagged' => 0, 'replayed' => false];
        }

        $ok = $wpdb->insert(Pi_Multilang_Slug_Batch::table(), [
            'kind'       => self::BATCH_KIND,
            'status'     => 'applied',
            'lang'       => '',
            'created_by' => get_current_user_id(),
            'created_at' => current_time('mysql', true),
            'applied_at' => current_time('mysql', true),
            'item_count' => count($snapshot),
            'items'      => wp_json_encode($snapshot),
            'snapshot'   => wp_json_encode($snapshot),
            'note'       => (string) $note,
        ]);
        if ($ok === false) {
            return new WP_Error('batch_insert_failed', $wpdb->last_error, ['status' => 500]);
        }
        $batch_id = (int) $wpdb->insert_id;

        // Ghi lô TRƯỚC khi xoá: đứt điện giữa chừng thì còn ảnh chụp để lùi,
        // còn nếu xoá trước thì mất sạch đường về.
        $done = 0;
        foreach ($snapshot as $row) {
            if (delete_post_meta($row['id'], 'pi_language')) $done++;
        }

        self::remember_key($key, $batch_id);
        Pi_Multilang_Slug_Batch::flush_site_caches();

        return ['ok' => true, 'batch_id' => $batch_id, 'untagged' => $done, 'replayed' => false];
    }

    /** Trả thẻ về đúng giá trị đã chụp trước khi gỡ. */
    public static function undo_untag($batch_id) {
        $batch = Pi_Multilang_Slug_Batch::get($batch_id);
        if (!$batch) {
            return new WP_Error('not_found', 'Không tìm thấy lô.', ['status' => 404]);
        }
        if ($batch->kind !== self::BATCH_KIND) {
            return new WP_Error('wrong_kind', 'Lô này không phải lô gỡ thẻ ngôn ngữ.', ['status' => 409]);
        }
        if ($batch->status === 'undone') {
            return new WP_Error('already_undone', 'Lô này đã hoàn tác.', ['status' => 409]);
        }

        $restored = 0;
        foreach ((array) $batch->snapshot as $row) {
            if (empty($row['id']) || empty($row['lang'])) continue;
            update_post_meta((int) $row['id'], 'pi_language', (string) $row['lang']);
            $restored++;
        }

        global $wpdb;
        $wpdb->update(
            Pi_Multilang_Slug_Batch::table(),
            ['status' => 'undone', 'undone_at' => current_time('mysql', true)],
            ['id' => (int) $batch->id]
        );
        Pi_Multilang_Slug_Batch::flush_site_caches();

        return ['ok' => true, 'batch_id' => (int) $batch->id, 'restored' => $restored];
    }

    /** Lịch sử lô gỡ thẻ (tách khỏi lô slug bằng cột kind). */
    public static function batches($limit = 50, $offset = 0) {
        Pi_Multilang_Slug_Batch::ensure_table();
        global $wpdb;
        $rows = $wpdb->get_results($wpdb->prepare(
            "SELECT id, kind, status, created_by, created_at, applied_at, undone_at, item_count, note
               FROM " . Pi_Multilang_Slug_Batch::table() . "
              WHERE kind = %s ORDER BY id DESC LIMIT %d OFFSET %d",
            self::BATCH_KIND, (int) $limit, (int) $offset
        ));
        return $rows ?: [];
    }

    private static function remember_key($key, $batch_id) {
        $seen = (array) get_option(self::IDEMPOTENCY_OPTION, []);
        $seen[$key] = (int) $batch_id;
        if (count($seen) > self::IDEMPOTENCY_MAX) {
            $seen = array_slice($seen, -self::IDEMPOTENCY_MAX, null, true);
        }
        update_option(self::IDEMPOTENCY_OPTION, $seen, false);
    }
}
