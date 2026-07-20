<?php
/**
 * Entities — nhận diện thương hiệu (brand) THẬT trong câu hỏi bằng cách so khớp
 * với danh sách brand THẬT đã index, thay vì để full-text tự đoán qua token.
 *
 * Lý do (đúc kết sau nhiều vòng vá bug retrieval): full-text bag-of-words coi
 * "Hoshizaki" ngang hàng với mọi từ khác trong câu — nếu câu có thêm từ phổ biến
 * (chào/bên/liên hệ...) thì các từ đó có thể lấn át, hoặc brand bị pha loãng
 * trong 1 điều kiện AND lớn. Nhận diện brand THẬT trước rồi lọc CỨNG theo cột
 * `brand` (WHERE brand = X) đảm bảo 100% kết quả đúng thương hiệu khách hỏi,
 * full-text chỉ dùng để XẾP HẠNG trong phạm vi đã lọc — đúng pattern "entity-first
 * structured filtering" khuyến nghị cho RAG quy mô nhỏ không dùng vector DB.
 *
 * @package PiChatbot
 */

if (!defined('ABSPATH')) exit;

class Pi_Chatbot_Entities {
    const CACHE_KEY = 'pi_chatbot_known_brands';
    const TAXONOMY_CACHE_KEY = 'pi_chatbot_taxonomy_brands';

    /**
     * Dựng map [tên_thường_dùng => tên_đầy_đủ_chuẩn] từ 1 danh sách tên brand đầy đủ.
     * Dùng chung cho cả known_brands() (nguồn: bảng index) lẫn taxonomy_brands()
     * (nguồn: taxonomy) — 2 nguồn khác nhau nhưng luật so khớp phải giống hệt nhau,
     * nếu tách ra viết 2 lần sẽ lệch nhau lúc sửa.
     */
    private static function build_brand_map($full_names) {
        $map = [];
        // Gom base -> [full,...] trước, KHÔNG ghi thẳng vào $map — ghi đè vô điều
        // kiện (bản cũ) khiến 2 brand cùng tiền tố trước dấu gạch (vd tương lai
        // "Giorik-Italia" + "Giorik-Spain") gộp làm 1 base "giorik", brand thua bị
        // MySQL trả về sau cùng sẽ thắng và brand kia biến mất vĩnh viễn khỏi
        // đường lọc cứng WHERE brand = X — không lỗi, không cảnh báo, chỉ lặng lẽ
        // không bao giờ tìm thấy được.
        $bases = [];
        foreach ($full_names as $full) {
            $full = trim((string) $full);
            if ($full === '') continue;
            $map[mb_strtolower($full)] = $full; // tên đầy đủ luôn khớp được, không nhập nhằng

            // "Giorik-Italia" -> base "Giorik" (phần trước dấu gạch ngang đầu tiên) —
            // khách thường gõ tên ngắn, không kèm hậu tố xuất xứ (-Italia/-Germany...).
            $base = trim(preg_replace('/[\-–—].*$/u', '', $full));
            if ($base === '') continue;
            $lb = mb_strtolower($base);
            if (!isset($bases[$lb])) $bases[$lb] = [];
            if (!in_array($full, $bases[$lb], true)) $bases[$lb][] = $full;
        }
        foreach ($bases as $lb => $fulls) {
            // Base nhập nhằng (≥2 brand cùng tên gốc) HOẶC trùng luôn 1 tên đầy đủ
            // brand khác -> KHÔNG tạo lối tắt. Cả 2 brand vẫn khớp được bằng tên
            // đầy đủ, chỉ mất mỗi lối tắt gõ tên ngắn — cùng nguyên tắc với
            // detect_brand_from_title() trả null khi nhập nhằng thay vì đoán bừa.
            if (count($fulls) === 1 && !isset($map[$lb])) $map[$lb] = $fulls[0];
        }
        // So từ dài nhất trước — tránh brand ngắn (vd "Sammic") khớp nhầm vào giữa
        // 1 tên brand dài khác vô tình chứa chuỗi con trùng. Sort 1 lần rồi cache.
        uksort($map, function ($a, $b) { return mb_strlen($b) - mb_strlen($a); });
        return $map;
    }

    /**
     * Danh sách brand thật ĐÃ INDEX, dạng [base_name_thường_dùng => tên_đầy_đủ_trong_DB]
     * — ĐÃ SẮP XẾP sẵn theo độ dài tên giảm dần (dài trước) trước khi cache, thay vì
     * uksort() lại mỗi lần gọi extract_brand()/extract_brands() (audit flag: sort lại
     * toàn bộ map mỗi request dù chỉ 6 brand hiện tại — rẻ nhưng vô ích khi kết quả
     * sort không đổi giữa các lần cache còn hiệu lực trong ngày).
     *
     * Dùng cho phía CÂU HỎI: chỉ nhận diện brand thực sự có sản phẩm trong index,
     * tránh lọc cứng theo 1 brand không có bản ghi nào rồi trả rỗng oan.
     */
    public static function known_brands() {
        $cached = get_transient(self::CACHE_KEY);
        if (is_array($cached)) return $cached;

        global $wpdb;
        $table = Pi_Chatbot_Indexer::table_name();
        $full_names = $wpdb->get_col("SELECT DISTINCT brand FROM {$table} WHERE brand != ''");

        $map = self::build_brand_map($full_names);
        set_transient(self::CACHE_KEY, $map, DAY_IN_SECONDS);
        return $map;
    }

    /**
     * Danh sách brand từ TAXONOMY `product_brand` — nguồn chuẩn, đầy đủ (12 term),
     * KHÔNG phụ thuộc bảng index.
     *
     * BẮT BUỘC tách riêng khỏi known_brands(): known_brands() đọc chính bảng index,
     * mà detect_brand_from_title() lại được gọi TRONG lúc đang dựng bảng shadow —
     * dùng known_brands() ở đó sẽ thành lỗi bootstrap gà-và-trứng (đọc bảng chưa
     * dựng xong, hoặc tệ hơn là đọc bảng cũ rồi tự khoá mình ở 6 brand đang có
     * thay vì thấy đủ 12 brand thật trong taxonomy).
     */
    public static function taxonomy_brands() {
        $cached = get_transient(self::TAXONOMY_CACHE_KEY);
        if (is_array($cached)) return $cached;

        if (!taxonomy_exists('product_brand')) return [];
        $names = get_terms([
            'taxonomy'   => 'product_brand',
            'hide_empty' => false,
            'fields'     => 'names',
        ]);
        if (is_wp_error($names) || !is_array($names)) return [];

        $map = self::build_brand_map($names);
        set_transient(self::TAXONOMY_CACHE_KEY, $map, DAY_IN_SECONDS);
        return $map;
    }

    /**
     * Bóc brand từ TIÊU ĐỀ sản phẩm khi taxonomy để trống.
     *
     * So khớp với danh sách 12 brand THẬT trong taxonomy rồi trả về TÊN ĐẦY ĐỦ
     * CHUẨN — KHÔNG cắt chuỗi theo dấu gạch ngang. Cách cắt theo gạch ngang cuối
     * (`/-\s*([^-]+)$/`) là bug thật đã đo được: 8/12 brand có dạng "Brand-QuốcGia"
     * (Fujimak-Japan, Berjaya-Malaysia, Winterhalter-Germany...) nên nó lấy ra
     * "Japan"/"Malaysia"/"Germany" — sai 96/271 bản ghi, và sai kiểu tệ hơn để
     * trống vì giá trị rác đó lọt vào cả cột brand lẫn FULLTEXT index.
     *
     * Trả null khi không khớp brand nào HOẶC khớp từ 2 brand trở lên (nhập nhằng —
     * thà để trống rồi rơi về full-text còn hơn gán sai một cách im lặng).
     */
    public static function detect_brand_from_title($title) {
        $haystack = mb_strtolower((string) $title);
        if ($haystack === '') return null;

        $found = [];
        foreach (self::taxonomy_brands() as $needle => $full) {
            if (mb_strlen($needle) < 3) continue;
            if (mb_strpos($haystack, $needle) === false) continue;
            if (!in_array($full, $found, true)) $found[] = $full;
        }
        return count($found) === 1 ? $found[0] : null;
    }

    public static function flush_cache() {
        delete_transient(self::CACHE_KEY);
        delete_transient(self::TAXONOMY_CACHE_KEY);
    }

    /** Trả tên brand THẬT đầu tiên khớp (đúng như trong DB) nếu câu hỏi nhắc tới, ngược lại null. */
    public static function extract_brand($question) {
        $brands = self::extract_brands($question);
        return $brands ? $brands[0] : null;
    }

    /**
     * Trả TẤT CẢ brand thật khớp trong câu hỏi (không chỉ 1) — cho phép so sánh
     * cross-brand kiểu "Hoshizaki với Giorik cái nào tốt hơn" (trước đây chỉ nhận
     * diện được 1 brand đầu tiên, brand thứ 2 bị bỏ qua hoàn toàn — audit flag).
     */
    public static function extract_brands($question) {
        $q = mb_strtolower((string) $question);
        $brands = self::known_brands();
        $found = [];
        foreach ($brands as $needle => $full) {
            if (mb_strlen($needle) < 3) continue;
            if (mb_strpos($q, $needle) === false) continue;
            if (!in_array($full, $found, true)) $found[] = $full;
        }
        return $found;
    }
}
