<?php
/**
 * LLM Client — gọi LLM7 API dùng chung cho Generator (soạn câu trả lời) và Router
 * (quyết định có cần enrich retrieval không). Tách khỏi Generator để mỗi bên tự
 * cấu hình timeout/số lần thử riêng — Router cần 1 lần gọi, timeout ngắn (lưới an
 * toàn phụ, không đáng đánh đổi thêm giây chờ cho retry); Generator cần 2 lần thử,
 * timeout dài hơn (đường chính, đáng đánh đổi để tăng tỉ lệ thành công).
 *
 * @package PiChatbot
 */

if (!defined('ABSPATH')) exit;

class Pi_Chatbot_LLM_Client {
    const API_URL = 'https://api.llm7.io/v1/chat/completions';

    /**
     * Round-robin qua tối đa $max_attempts key LLM7 — key nào lỗi (hết quota/timeout)
     * thử key kế tiếp. Nguyên văn logic đã có ở Generator::call_llm7() trước đây,
     * chỉ tham số hoá timeout/max_attempts thay vì hằng số cứng của riêng Generator.
     *
     * @param array    $body         Payload gửi tới LLM7 (model, messages, ...).
     * @param string[] $keys         Danh sách key, thử theo thứ tự.
     * @param int      $timeout      Giây, áp dụng cho MỖI lần gọi.
     * @param int      $max_attempts Số key tối đa được thử.
     * @param string   $log_prefix   Tiền tố log để phân biệt Generator/Router trong error_log.
     * @return array|WP_Error
     */
    public static function call($body, $keys, $timeout, $max_attempts, $log_prefix = 'pi-chatbot') {
        $last_error = null;
        $attempts = 0;
        foreach ($keys as $key) {
            if (!$key) continue;
            if ($attempts >= $max_attempts) break;
            $attempts++;

            $res = wp_remote_post(self::API_URL, [
                'timeout' => $timeout,
                'headers' => [
                    'Content-Type'  => 'application/json',
                    'Authorization' => 'Bearer ' . $key,
                ],
                'body' => wp_json_encode($body),
            ]);

            if (is_wp_error($res)) {
                $last_error = $res;
                error_log("[{$log_prefix}] LLM7 request failed (key {$attempts}/{$max_attempts}): " . $res->get_error_message());
                continue;
            }

            $code = wp_remote_retrieve_response_code($res);
            $raw  = wp_remote_retrieve_body($res);
            $data = json_decode($raw, true);

            if ($code >= 200 && $code < 300 && is_array($data) && !empty($data['choices'])) {
                return $data;
            }

            error_log("[{$log_prefix}] LLM7 trả lỗi HTTP {$code} (key {$attempts}/{$max_attempts}): " . substr((string) $raw, 0, 500));
            $last_error = new WP_Error('pi_chatbot_llm7_error', 'LLM7 trả lỗi (HTTP ' . $code . ').', $raw);
        }

        return $last_error ?: new WP_Error('pi_chatbot_no_key', 'Chưa cấu hình API key LLM7.');
    }

    /** Rút content text từ response thô — dùng chung, tránh lặp lại ?? chuỗi ở nhiều nơi. */
    public static function content($response) {
        return trim((string) ($response['choices'][0]['message']['content'] ?? ''));
    }
}
