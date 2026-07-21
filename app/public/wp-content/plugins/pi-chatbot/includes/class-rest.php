<?php
/**
 * REST — pi/v1/chatbot/ask, public (rate-limited theo IP qua transient).
 *
 * @package PiChatbot
 */

if (!defined('ABSPATH')) exit;

class Pi_Chatbot_Rest {
    const MAX_MESSAGE_LENGTH = 500;

    private static $instance = null;

    public static function instance() {
        if (self::$instance === null) self::$instance = new self();
        return self::$instance;
    }

    private function __construct() {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function register_routes() {
        register_rest_route('pi/v1', '/chatbot/ask', [
            'methods'             => 'POST',
            'callback'            => [$this, 'ask'],
            'permission_callback' => '__return_true',
            'args'                => [
                'message' => [
                    'required'          => true,
                    'type'              => 'string',
                    'sanitize_callback' => 'sanitize_text_field',
                ],
            ],
        ]);
    }

    public function ask($req) {
        $message = trim((string) $req->get_param('message'));
        if ($message === '') {
            return new WP_Error('pi_chatbot_empty_message', 'Thiếu nội dung câu hỏi.', ['status' => 400]);
        }
        if (mb_strlen($message) > self::MAX_MESSAGE_LENGTH) {
            return new WP_Error('pi_chatbot_too_long', sprintf('Câu hỏi quá dài (tối đa %d ký tự).', self::MAX_MESSAGE_LENGTH), ['status' => 400]);
        }

        // Cache câu trả lời theo hash(câu hỏi chuẩn hoá + version index) — câu hỏi
        // lặp lại (chào hỏi, brand phổ biến, 4 nút quick-reply) trả ngay không tốn
        // retrieval + LLM call. Chuẩn hoá khoảng trắng thêm vào (trước đây "tủ  đông"
        // và "tủ đông" là 2 entry cache khác nhau — 2 lần gọi LLM 8,7s cho cùng 1 câu
        // hỏi). pi_chatbot_index_version tăng ở CẢ reindex lẫn sửa/xoá 1 bài lẻ (xem
        // class-indexer.php) — trước đây chỉ reindex mới bump last_reindex nên sửa
        // giá/thông số 1 sản phẩm xong khách vẫn nhận câu trả lời + nguồn cũ tới 1 giờ.
        $normalized = trim(preg_replace('/\s+/u', ' ', mb_strtolower($message)));
        $cache_key = 'pi_chatbot_ans_' . md5($normalized . '|' . (int) get_option('pi_chatbot_index_version', 0));
        $cached = get_transient($cache_key);
        if ($cached !== false) {
            return rest_ensure_response($cached);
        }

        // Rate limit sau cache lookup: câu hỏi đã cache là 0 chi phí (không retrieval,
        // không LLM) nên không nên trừ vào quota — trước đây kiểm tra trước, phục vụ
        // cache miễn phí vẫn tính vào giới hạn 10 câu/phút.
        $rate_check = $this->check_rate_limit();
        if (is_wp_error($rate_check)) return $rate_check;

        $settings = pi_chatbot_settings();

        // Lớp ý định: liên hệ/giá/dịch vụ trả thẳng dữ liệu thật của theme, không
        // qua full-text (trang tương ứng nội dung gần rỗng hoặc không tồn tại — vd
        // 0/381 sản phẩm có giá, full-text sẽ đính bừa sản phẩm ngẫu nhiên làm nguồn
        // nếu không chặn ở đây). strip_intent_phrases() bóc cụm từ đã kích hoạt intent
        // TRƯỚC khi đưa phần còn lại vào search() — không bóc thì "báo giá" sau lọc
        // stopword chỉ còn "báo" (vì "giá" đã là stopword), vẫn khớp bừa sản phẩm.
        $context = [];
        $is_contact = Pi_Chatbot_Retriever::is_contact_intent($message);
        $is_price   = Pi_Chatbot_Retriever::is_price_intent($message);
        $is_service = Pi_Chatbot_Retriever::is_service_intent($message);
        $is_project = Pi_Chatbot_Retriever::is_project_intent($message);

        if ($is_contact) $context = array_merge($context, Pi_Chatbot_Retriever::contact_context());
        if ($is_price)   $context = array_merge($context, Pi_Chatbot_Retriever::price_context());
        if ($is_service) $context = array_merge($context, Pi_Chatbot_Retriever::service_context());
        if ($is_project) {
            $project_context = Pi_Chatbot_Retriever::search_projects($message, (int) $settings['top_k']);
            if ($project_context) $context = array_merge($context, $project_context);
        }

        // Phần dư SAU khi bóc cụm từ intent — proxy tốt hơn "có nhắc brand hay không"
        // (guard cũ) cho "khách có mô tả sản phẩm": "báo giá tủ đông Hoshizaki" không
        // brand nhưng vẫn phải ra đúng sản phẩm; "báo giá" thuần thì phần dư rỗng,
        // không chạy full-text nên không đính nguồn giả.
        $router_used = false;
        $router_keywords = [];
        $router_brand = null;
        $residual = ($is_contact || $is_price || $is_service || $is_project)
            ? Pi_Chatbot_Retriever::strip_intent_phrases($message)
            : $message;
        if (trim($residual) !== '') {
            $product_context = Pi_Chatbot_Retriever::search($residual, (int) $settings['top_k']);
            if ($product_context) {
                $context = array_merge($context, $product_context);
            } elseif (!$is_contact && !$is_price && !$is_service && !$is_project) {
                // Đường nhanh (rule-based + known_brands() + search()) đã thất bại VÀ
                // đây không phải câu đã được intent khác xử lý — đúng khe hở đã đo:
                // "lò than hoa" không nhắc brand, tiêu đề sản phẩm tiếng Anh. Router
                // CHỈ chạy tới đây, không bao giờ chạy khi đường nhanh đã có kết quả
                // (xem docs/plans/pi-chatbot-adaptive-router.md) — mặc định TẮT
                // (router_enabled=false), bật tay sau khi review.
                $enrichment = Pi_Chatbot_Router::enrich($message);
                if ($enrichment) {
                    $router_used = true;
                    $router_keywords = array_merge($enrichment['keywords_vi'], $enrichment['keywords_en']);
                    $router_brand = $enrichment['brand'] ?: null;
                    $enriched_query = trim(implode(' ', array_merge(
                        $enrichment['keywords_en'],
                        $enrichment['keywords_vi'],
                        $enrichment['brand'] ? [$enrichment['brand']] : []
                    )));
                    if ($enriched_query !== '') {
                        $enriched_context = Pi_Chatbot_Retriever::search($enriched_query, (int) $settings['top_k']);
                        if ($enriched_context) {
                            $context = array_merge($context, $enriched_context);
                        }
                    }
                }
            }
        }

        // Log CHỈ trên đường tươi (không phải cache hit — câu đã cache là lặp lại
        // của 1 câu đã log trước đó, log lại sẽ thổi phồng đếm mà không thêm tín hiệu
        // mới). Ghi trước khi gọi Generator để vẫn có dữ liệu ngay cả khi LLM lỗi —
        // "có tìm thấy context hay không" là tín hiệu độc lập với việc sinh câu trả lời.
        Pi_Chatbot_Log::record($normalized, !empty($context), count($context), $router_used);

        $result = Pi_Chatbot_Generator::answer($message, $context);

        if (is_wp_error($result)) {
            return new WP_Error('pi_chatbot_generation_failed', 'Trợ lý đang bận, vui lòng thử lại sau ít phút hoặc gọi hotline.', ['status' => 502]);
        }

        // "trace" — dữ liệu THẬT đã xảy ra trong request này (không phải chain-of-
        // thought của model sinh câu trả lời — LLM7 không trả riêng field đó cho
        // gpt-oss:20b). Frontend dùng để dựng 2 khối "Đã tìm kiếm"/"Đã suy nghĩ"
        // kiểu Claude. router_keywords/router_brand CHỈ có giá trị khi router thật
        // sự chạy — rỗng nghĩa là không có bước suy luận nào xảy ra, không bịa ra
        // nội dung giả cho khối "suy nghĩ".
        $result['trace'] = [
            'intent'          => $is_price ? 'price' : ($is_contact ? 'contact' : ($is_service ? 'service' : ($is_project ? 'project' : null))),
            'search_query'    => trim($residual),
            'found_count'     => count($context),
            'router_used'     => $router_used,
            'router_keywords' => $router_keywords,
            'router_brand'    => $router_brand,
        ];

        set_transient($cache_key, $result, HOUR_IN_SECONDS);
        return rest_ensure_response($result);
    }

    /**
     * Giới hạn theo IP + option 'rate_limit' (mặc định 10 câu/phút) — chống spam đơn giản.
     *
     * Khoá theo BUCKET PHÚT (`floor(time()/60)`) thay vì 1 khoá cố định + reset thủ
     * công. Bản cũ reset cửa sổ bằng get→delete→update — đọc-sửa-ghi 3 bước KHÔNG
     * nguyên tử, ngay cạnh dòng tăng đếm được cố tình làm atomic — nên 2 request đến
     * đúng lúc cửa sổ hết hạn có thể cùng thấy "cửa sổ cũ đã hết", 1 request delete()
     * xong sau khi request kia vừa INSERT xong, xoá mất lượt tăng vừa ghi. Mỗi phút
     * một khoá mới thì KHÔNG CÒN bước reset nào để race — request đầu tiên trong
     * phút mới tự nhiên bắt đầu từ 0 vì khoá đó chưa từng tồn tại.
     *
     * CHỈ TĂNG ĐẾM KHI CÒN DƯỚI HẠN MỨC: trước đây tăng trước rồi mới so sánh, nên
     * user bị chặn 429 mà bấm gửi lại sẽ tự đẩy đếm của MÌNH cao hơn nữa, kéo dài
     * thời điểm được mở lại — đúng ngược với "chờ 1 phút" mà thông báo hứa hẹn.
     */
    private function check_rate_limit() {
        global $wpdb;
        $settings = pi_chatbot_settings();
        $limit = max(1, (int) $settings['rate_limit']);
        $bucket = (int) floor(time() / MINUTE_IN_SECONDS);
        $key = 'pi_chatbot_rl_' . md5($this->get_client_ip()) . '_' . $bucket;
        $value_option = '_transient_' . $key;
        $timeout_option = '_transient_timeout_' . $key;

        $count = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT option_value FROM {$wpdb->options} WHERE option_name = %s", $value_option
        ));

        if ($count >= $limit) {
            return new WP_Error('pi_chatbot_rate_limited', 'Bạn gửi câu hỏi hơi nhanh, vui lòng chờ 1 phút.', ['status' => 429]);
        }

        $wpdb->query($wpdb->prepare(
            "INSERT INTO {$wpdb->options} (option_name, option_value, autoload)
             VALUES (%s, '1', 'no')
             ON DUPLICATE KEY UPDATE option_value = option_value + 1",
            $value_option
        ));
        // add_option() là no-op nếu row đã tồn tại — không cần bước reset, WP tự dọn
        // theo timeout khi transient hết hạn (uninstall.php cũng quét theo pattern).
        add_option($timeout_option, time() + (2 * MINUTE_IN_SECONDS), '', false);

        return true;
    }

    /**
     * REMOTE_ADDR làm khoá rate-limit — KHÔNG tin X-Forwarded-For/X-Client-IP vì đó
     * là header client-controlled hoàn toàn, gửi mỗi request 1 giá trị ngẫu nhiên là
     * vô hiệu hoá rate-limit (bug thật đã tìm thấy khi audit). Chỉ tin XFF khi site
     * thật sự đứng sau reverse proxy tin cậy (định nghĩa hằng số PI_CHATBOT_TRUST_PROXY
     * trong wp-config.php để bật, mặc định TẮT — LocalWP/hosting đơn giản không cần).
     */
    private function get_client_ip() {
        if (defined('PI_CHATBOT_TRUST_PROXY') && PI_CHATBOT_TRUST_PROXY && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
            return trim($ip);
        }
        return !empty($_SERVER['REMOTE_ADDR']) ? trim($_SERVER['REMOTE_ADDR']) : '0.0.0.0';
    }
}
