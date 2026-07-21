<?php
/**
 * Generator — build prompt tiếng Việt từ context retrieval, gọi thẳng LLM7 API
 * (https://api.llm7.io) sinh câu trả lời. KHÔNG qua Luna-Proxy — theo yêu cầu user
 * (Luna-Proxy ép model tuỳ ý + là điểm phụ thuộc phụ không cần thiết).
 *
 * @package PiChatbot
 */

if (!defined('ABSPATH')) exit;

class Pi_Chatbot_Generator {

    const SYSTEM_PROMPT = "Bạn là trợ lý tư vấn của Saigon Horeca — chuyên cung cấp thiết bị bếp công nghiệp, nhà hàng, khách sạn (bếp Âu/Á, lò nướng, tủ lạnh công nghiệp, thiết bị rửa...).\n"
        . "Chỉ trả lời dựa trên THÔNG TIN THAM KHẢO được cung cấp bên dưới — không bịa thêm sản phẩm, giá, thông số không có trong đó.\n"
        . "Nếu khách hỏi CHUNG CHUNG kiểu \"bên mình có gì\", \"bán những gì\" và KHÔNG có thông tin tham khảo cụ thể: được phép giới thiệu NGẮN các NHÓM sản phẩm chính theo mô tả công ty ở trên (bếp Âu/Á, lò nướng, tủ lạnh công nghiệp, thiết bị rửa...) rồi hỏi lại khách muốn xem loại nào — đây là mô tả chung về công ty, không phải bịa sản phẩm cụ thể.\n"
        . "Nếu khách hỏi CỤ THỂ (tên sản phẩm/thương hiệu/mã hàng) mà thông tin tham khảo không đủ: nói rõ chưa có thông tin và đề nghị liên hệ hotline, không đoán mò.\n"
        . "Nếu THÔNG TIN THAM KHẢO có mục \"Chính sách báo giá\": relay đúng nội dung đó (giá tuỳ cấu hình, mời liên hệ hotline) — TUYỆT ĐỐI không tự ước tính hay bịa ra một con số giá cụ thể nào, kể cả khi khách hỏi dồn hoặc nói \"áng chừng thôi cũng được\".\n"
        . "Trả lời ngắn gọn, tiếng Việt, giọng tư vấn thân thiện, chuyên nghiệp. Không dùng bảng Markdown, ký hiệu |, #, ** — chỉ văn xuôi hoặc gạch đầu dòng - đơn giản, vì giao diện chat không dựng Markdown.";

    /**
     * @param string $question
     * @param array  $context Kết quả từ Pi_Chatbot_Retriever::search()
     * @return array{answer:string, sources:array}|WP_Error
     */
    public static function answer($question, $context) {
        $settings = pi_chatbot_settings();

        if (empty($context)) {
            $context_text = "(Không tìm thấy sản phẩm/trang nào khớp trong dữ liệu nội bộ.)";
        } else {
            $lines = [];
            foreach ($context as $item) {
                $lines[] = sprintf(
                    "- %s%s%s — %s\n  %s",
                    $item['title'],
                    $item['brand'] ? " (Thương hiệu: {$item['brand']})" : '',
                    $item['category'] ? " [{$item['category']}]" : '',
                    $item['url'],
                    $item['excerpt']
                );
            }
            $context_text = implode("\n", $lines);
        }

        $user_prompt = "THÔNG TIN THAM KHẢO:\n{$context_text}\n\nCÂU HỎI KHÁCH: {$question}";

        $body = [
            'model'    => $settings['model'],
            'messages' => [
                ['role' => 'system', 'content' => self::SYSTEM_PROMPT],
                ['role' => 'user', 'content' => $user_prompt],
            ],
            'temperature' => 0.3,
            // max_tokens chặn lặp vô hạn (repetition loop đã gặp thật với gpt-oss:20b:
            // trả "bếp hơi lò nướng" lặp hàng trăm lần) — nếu không cap thì vừa tốn thời
            // gian (rủi ro timeout) vừa bị cache lại nguyên văn qua REST layer.
            //
            // 2000 chứ KHÔNG phải 700: gpt-oss:20b là model REASONING — nó sinh field
            // `reasoning` (~700+ ký tự) TRƯỚC rồi mới tới `content`, và cả hai cùng ăn
            // vào max_tokens. Cap 700 khiến phần suy luận nuốt gần hết budget, thỉnh
            // thoảng content trả về RỖNG → REST trả 502 ngắt quãng (bug thật đã bắt
            // được khi verify: cùng 1 câu hỏi lúc được lúc 502). 2000 cho đủ chỗ cho
            // reasoning + câu trả lời; chống lặp vô hạn giờ do looks_degenerate() lo.
            'max_tokens' => 2000,
        ];

        $response = Pi_Chatbot_LLM_Client::call(
            $body, $settings['llm7_keys'], self::TIMEOUT_SECONDS, self::MAX_KEY_ATTEMPTS, 'pi-chatbot-generator'
        );
        if (is_wp_error($response)) return $response;

        $answer = Pi_Chatbot_LLM_Client::content($response);
        if ($answer === '') {
            return new WP_Error('pi_chatbot_empty', 'Không nhận được câu trả lời từ mô hình.');
        }

        // Phòng thủ thêm: model free-tier đôi khi lặp vô hạn dù đã cap max_tokens (lặp
        // trong PHẦN token được cấp) — phát hiện bằng cách so sánh độ dài văn bản với
        // độ dài sau khi khử trùng lặp cụm từ liên tiếp; lệch quá nhiều nghĩa là suy
        // biến. KHÔNG trả về câu trả lời suy biến (và KHÔNG cache nó — xem class-rest.php).
        if (self::looks_degenerate($answer)) {
            return new WP_Error('pi_chatbot_degenerate', 'Mô hình trả lời không hợp lệ (lặp lại bất thường).');
        }

        $answer = self::strip_markdown($answer);

        return [
            'answer'  => $answer,
            'sources' => array_map(function ($item) {
                return ['title' => $item['title'], 'url' => $item['url']];
            }, $context),
        ];
    }

    /**
     * Phát hiện câu trả lời suy biến kiểu lặp 1 cụm từ liên tục ("bếp hơi lò nướng
     * bếp hơi lò nướng...") — lỗi thật gặp khi test với model free-tier gpt-oss:20b.
     * Heuristic đơn giản: 1 từ chiếm quá 25% tổng số từ trong câu trả lời đủ dài thì
     * gần như chắc chắn là lặp bất thường, không phải văn phong tự nhiên.
     */
    /**
     * Lưới an toàn thứ 2 cho lệnh cấm Markdown trong SYSTEM_PROMPT — prompt là hàng
     * rào MỀM (model vẫn có thể lệch dù temperature thấp), nên strip cú pháp thô
     * thay vì dựng Markdown renderer ở client (client dùng textContent thuần, không
     * innerHTML — cố tình giữ nguyên bất biến bảo mật đó, không đánh đổi lấy 1 tính
     * năng hiển thị). Chỉ xử lý cú pháp INLINE đơn giản; bảng | vẫn có thể sót nếu
     * model cố tình phá — nhưng prompt đã cấm rõ và đây chỉ là lưới dự phòng.
     */
    private static function strip_markdown($text) {
        $text = preg_replace('/\*\*(.+?)\*\*/u', '$1', $text);   // **bold** -> bold
        $text = preg_replace('/^#{1,6}\s*/mu', '', $text);        // # Heading -> Heading
        return $text;
    }

    private static function looks_degenerate($text) {
        $words = preg_split('/\s+/u', trim($text));
        $total = count($words);
        if ($total < 20) return false; // câu ngắn không đủ mẫu để đánh giá, tránh false-positive
        $counts = array_count_values(array_map('mb_strtolower', $words));
        arsort($counts);
        $top_count = reset($counts);
        return ($top_count / $total) > 0.25;
    }

    /** Timeout ngắn + số key thử tối đa mỗi request — chặn worker PHP-FPM không quá
     * MAX_KEY_ATTEMPTS × TIMEOUT giây (trước đây 30s × N key không giới hạn, có thể
     * giữ 1 worker rất lâu nếu admin cấu hình nhiều key và tất cả đều chậm/lỗi). */
    const TIMEOUT_SECONDS = 20;
    const MAX_KEY_ATTEMPTS = 2;
}
