<?php
/**
 * Router — lưới an toàn CHỈ chạy khi đường nhanh (intent rule-based + known_brands()
 * + Retriever::search()) đã trả rỗng. KHÔNG thay thế đường nhanh — xem
 * docs/plans/pi-chatbot-adaptive-router.md cho toàn bộ lý do thiết kế và căn cứ
 * học thuật (Self-RAG arXiv:2310.11511, "Small Models Big Insights" arXiv:2402.12052,
 * "To Retrieve or Not to Retrieve?" arXiv:2501.09292 — kích hoạt retrieval theo
 * bất định thay vì luôn chạy giảm ~50% lượt gọi mà gần như không mất độ chính xác).
 *
 * Khoảng trống đang vá: khách hỏi tiếng Việt, KHÔNG nhắc brand, từ vựng Việt không
 * trùng tiêu đề Anh (đo thật: "lò than hoa" → 0 kết quả, trong khi
 * "charcoal oven for restaurant" → đúng — hệ thống hiện tại KHÔNG kém tiếng Anh,
 * chỉ thiếu bước dịch Việt→Anh theo ngữ cảnh sản phẩm).
 *
 * @package PiChatbot
 */

if (!defined('ABSPATH')) exit;

class Pi_Chatbot_Router {
    /** Đổi prompt thì bump số này — cache cũ tự vô hiệu, không cần dò xoá thủ công. */
    const PROMPT_VERSION = 1;

    const MODEL_DEFAULT = 'codestral-latest';
    // 1 lần gọi, KHÔNG retry — khác Generator (2 key). Router là lưới an toàn PHỤ,
    // không đáng đánh đổi thêm giây chờ cho 1 lần thử lại (xem plan, mục "Guardrail").
    const TIMEOUT_SECONDS = 8;
    const MAX_KEY_ATTEMPTS = 1;

    const CACHE_TTL_POSITIVE = 30 * DAY_IN_SECONDS; // bản dịch 1 cụm từ không đổi theo catalog
    const CACHE_TTL_NEGATIVE = 10 * MINUTE_IN_SECONDS; // tránh hammering LLM7 khi 1 câu lỗi lặp lại

    // Circuit breaker — cùng nguyên tắc atomic-bucket đã dùng ở
    // Pi_Chatbot_Rest::check_rate_limit() (bài học: race condition đã xảy ra thật
    // với get_transient()/set_transient() đọc-sửa-ghi không nguyên tử).
    const BREAKER_WINDOW_SECONDS = 300; // 5 phút/bucket
    const BREAKER_FAIL_THRESHOLD = 5;

    /**
     * @param string $question Câu hỏi GỐC (chưa bóc intent) — router cần ngữ cảnh đầy đủ.
     * @return array{keywords_en:string[],keywords_vi:string[],brand:?string}|null
     *   null = tắt/breaker mở/lỗi/không phải câu hỏi sản phẩm — gọi nơi dùng PHẢI coi
     *   null như "không enrich được", giữ nguyên hành vi rỗng như khi chưa có router.
     */
    public static function enrich($question) {
        $settings = pi_chatbot_settings();
        if (empty($settings['router_enabled'])) return null;

        $normalized = trim(preg_replace('/\s+/u', ' ', mb_strtolower((string) $question)));
        if ($normalized === '') return null;

        $cache_key = 'pi_chatbot_rtr_' . self::PROMPT_VERSION . '_' . md5($normalized);
        $cached = get_transient($cache_key);
        if ($cached !== false) {
            return $cached === 'NEGATIVE' ? null : $cached;
        }

        if (self::breaker_open()) return null;

        $keys = !empty($settings['router_keys']) ? $settings['router_keys'] : $settings['llm7_keys'];
        if (empty($keys)) return null;

        $model = !empty($settings['router_model']) ? $settings['router_model'] : self::MODEL_DEFAULT;
        $body = [
            'model'       => $model,
            'messages'    => self::build_messages($question),
            'temperature' => 0,
            'max_tokens'  => 300,
        ];

        $response = Pi_Chatbot_LLM_Client::call(
            $body, $keys, self::TIMEOUT_SECONDS, self::MAX_KEY_ATTEMPTS, 'pi-chatbot-router'
        );

        if (is_wp_error($response)) {
            self::bump_breaker_failure();
            set_transient($cache_key, 'NEGATIVE', self::CACHE_TTL_NEGATIVE);
            return null;
        }

        $parsed = self::parse_response(Pi_Chatbot_LLM_Client::content($response));
        if ($parsed === null) {
            // Model trả về nhưng không phải JSON dùng được — KHÔNG tính là lỗi hạ
            // tầng (breaker không cần mở), nhưng vẫn cache âm để khỏi hỏi lại ngay.
            set_transient($cache_key, 'NEGATIVE', self::CACHE_TTL_NEGATIVE);
            return null;
        }

        $validated = self::validate($parsed);
        set_transient($cache_key, $validated ?: 'NEGATIVE', $validated ? self::CACHE_TTL_POSITIVE : self::CACHE_TTL_NEGATIVE);
        return $validated;
    }

    /**
     * Few-shot song ngữ THẬT từ chính DB thay vì để model dịch mù (theo
     * arXiv:2409.12880 — ví dụ song ngữ thật cải thiện +15,3% so với zero-shot cho
     * cặp ngôn ngữ model yếu). Cache 1 ngày — không cần tính lại mỗi lần router chạy,
     * catalog không đổi nhanh tới mức cần tươi hơn thế.
     */
    private static function few_shot_examples() {
        $cache_key = 'pi_chatbot_rtr_fewshot';
        $cached = get_transient($cache_key);
        if (is_array($cached)) return $cached;

        global $wpdb;
        $table = Pi_Chatbot_Indexer::table_name();
        $rows = $wpdb->get_results(
            "SELECT DISTINCT category, title FROM {$table}
             WHERE post_type = 'product' AND category != '' AND title != ''
             ORDER BY RAND() LIMIT 5"
        );
        $examples = [];
        foreach ($rows as $r) {
            $examples[] = ['vi' => $r->category, 'en_context' => $r->title];
        }
        set_transient($cache_key, $examples, DAY_IN_SECONDS);
        return $examples;
    }

    private static function build_messages($question) {
        $examples = self::few_shot_examples();
        $example_lines = array_map(function ($e) {
            return sprintf('- "%s" xuất hiện trong sản phẩm có tiêu đề tiếng Anh: "%s"', $e['vi'], $e['en_context']);
        }, $examples);

        $known_brands = array_values(array_unique(array_values(Pi_Chatbot_Entities::known_brands())));

        $system = "Bạn là bộ phân loại + dịch thuật cho chatbot bán thiết bị bếp công nghiệp "
            . "(catalog tiêu đề TIẾNG ANH, khách hỏi có thể bằng tiếng Việt/Anh/Nhật/khác).\n"
            . "Ví dụ thuật ngữ ngành THẬT từ catalog (dùng để dịch đúng, không đoán chung chung):\n"
            . implode("\n", $example_lines) . "\n\n"
            . "Danh sách brand THẬT đã có (chỉ chọn nguyên văn từ đây, không bịa brand khác): "
            . implode(', ', $known_brands) . "\n\n"
            . "Trả lời DUY NHẤT bằng JSON, không thêm chữ nào khác:\n"
            . "{\"is_product_question\":true/false,\"keywords_en\":[\"...\"],\"keywords_vi\":[\"...\"],\"brand\":\"...\"or null}\n"
            . "is_product_question=false nếu câu hỏi không phải về sản phẩm/thiết bị bếp cụ thể "
            . "(chào hỏi, tán gẫu, ngoài phạm vi) — khi đó các field khác để rỗng.\n"
            . "keywords_en: 2-5 từ khoá tiếng Anh mô tả ĐÚNG loại thiết bị (dùng thuật ngữ ngành "
            . "bếp công nghiệp thật, không dịch từng chữ máy móc).\n"
            . "keywords_vi: từ khoá tiếng Việt gốc còn lại sau khi bỏ các từ chào hỏi/đệm.";

        return [
            ['role' => 'system', 'content' => $system],
            ['role' => 'user', 'content' => (string) $question],
        ];
    }

    /**
     * Parse phòng thủ — model hay bọc JSON trong ```json fence hoặc thêm chữ thừa
     * trước/sau (đã quan sát thật ở Generator/analyzer trước). Bóc fence rồi tìm
     * khối {...} cân bằng ngoặc đầu tiên thay vì json_decode() thẳng chuỗi thô.
     */
    private static function parse_response($text) {
        if ($text === '') return null;
        $text = preg_replace('/^```(?:json)?\s*/i', '', trim($text));
        $text = preg_replace('/\s*```$/', '', $text);

        $start = strpos($text, '{');
        if ($start === false) return null;
        $depth = 0;
        $end = null;
        for ($i = $start; $i < strlen($text); $i++) {
            if ($text[$i] === '{') $depth++;
            elseif ($text[$i] === '}') {
                $depth--;
                if ($depth === 0) { $end = $i; break; }
            }
        }
        if ($end === null) return null;

        $json = substr($text, $start, $end - $start + 1);
        $data = json_decode($json, true);
        return is_array($data) ? $data : null;
    }

    /**
     * Validate + chống hallucination:
     * - brand PHẢI khớp nguyên văn 1 brand thật trong known_brands() — không khớp thì
     *   bỏ field (degrade từng phần, đúng nguyên tắc detect_brand_from_title() dùng),
     *   KHÔNG vứt cả kết quả chỉ vì brand sai.
     * - keywords_en/keywords_vi giới hạn tối đa 8 phần tử, mỗi phần tử ≤ 40 ký tự —
     *   chặn model xả dài. Chuỗi rỗng/không phải string bị loại.
     * - is_product_question === false (RÕ RÀNG, không phải "field vắng mặt") → trả
     *   null. Đo thật bắt được bug: model hay BỎ QUA field này dù vẫn trích đúng
     *   keywords (model tuân JSON schema không ổn định, đặc điểm chung của model
     *   turbo/rẻ) — dùng empty() coi field thiếu như false sẽ loại oan kết quả tốt.
     *   Tín hiệu đáng tin hơn: có keywords hay không (model không tự sinh keyword
     *   cho câu nó thấy không liên quan sản phẩm).
     */
    private static function validate($parsed) {
        if (array_key_exists('is_product_question', $parsed)) {
            $flag = $parsed['is_product_question'];
            // Chấp nhận cả boolean false lẫn chuỗi "false" (model JSON hoá không
            // nhất quán) — nhưng KHÔNG dùng empty() vì 0/"" cũng empty, dễ hiểu sai.
            if ($flag === false || $flag === 'false') return null;
        }

        $clean_list = function ($v) {
            if (!is_array($v)) return [];
            $out = [];
            foreach ($v as $item) {
                if (!is_string($item)) continue;
                $item = trim($item);
                if ($item === '' || mb_strlen($item) > 40) continue;
                $out[] = $item;
                if (count($out) >= 8) break;
            }
            return $out;
        };

        $keywords_en = $clean_list($parsed['keywords_en'] ?? []);
        $keywords_vi = $clean_list($parsed['keywords_vi'] ?? []);
        if (!$keywords_en && !$keywords_vi) return null;

        $brand = null;
        if (!empty($parsed['brand']) && is_string($parsed['brand'])) {
            $claimed = trim($parsed['brand']);
            foreach (Pi_Chatbot_Entities::known_brands() as $full) {
                if (strcasecmp($full, $claimed) === 0) { $brand = $full; break; }
            }
            // Không khớp → $brand giữ null, KHÔNG dùng chuỗi model tự bịa.
        }

        return ['keywords_en' => $keywords_en, 'keywords_vi' => $keywords_vi, 'brand' => $brand];
    }

    private static function breaker_bucket() {
        return (int) floor(time() / self::BREAKER_WINDOW_SECONDS);
    }

    private static function breaker_open() {
        global $wpdb;
        $key = '_transient_pi_chatbot_rtr_fail_' . self::breaker_bucket();
        $count = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT option_value FROM {$wpdb->options} WHERE option_name = %s", $key
        ));
        return $count >= self::BREAKER_FAIL_THRESHOLD;
    }

    /** Atomic increment — cùng pattern INSERT...ON DUPLICATE KEY UPDATE đã dùng ở
     *  check_rate_limit(), không phải get_transient()/set_transient() đọc-sửa-ghi. */
    private static function bump_breaker_failure() {
        global $wpdb;
        $key = '_transient_pi_chatbot_rtr_fail_' . self::breaker_bucket();
        $wpdb->query($wpdb->prepare(
            "INSERT INTO {$wpdb->options} (option_name, option_value, autoload)
             VALUES (%s, '1', 'no')
             ON DUPLICATE KEY UPDATE option_value = option_value + 1",
            $key
        ));
        add_option('_transient_timeout_pi_chatbot_rtr_fail_' . self::breaker_bucket(), time() + self::BREAKER_WINDOW_SECONDS, '', false);
    }
}
