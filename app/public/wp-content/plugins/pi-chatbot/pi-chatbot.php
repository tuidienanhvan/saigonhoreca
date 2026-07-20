<?php
/**
 * Plugin Name: Pi Chatbot
 * Description: Trợ lý AI tư vấn sản phẩm cho Saigon Horeca — retrieval full-text nội bộ (không embedding/vector DB, quy mô dữ liệu chưa cần) + generation gọi thẳng LLM7 API (không qua proxy trung gian). Gắn vào nút #sh-toggle theme đã có sẵn.
 * Version: 1.0.0
 * Requires at least: 5.9
 * Requires PHP: 7.4
 * Author: Saigon Horeca
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: pi-chatbot
 *
 * @package PiChatbot
 */

if (!defined('ABSPATH')) exit;

define('PI_CHATBOT_VERSION', '1.0.0');
define('PI_CHATBOT_DIR', plugin_dir_path(__FILE__));
define('PI_CHATBOT_URL', plugin_dir_url(__FILE__));
define('PI_CHATBOT_TABLE', 'pi_chatbot_index');
define('PI_CHATBOT_OPTION', 'pi_chatbot_settings');

require_once PI_CHATBOT_DIR . 'includes/class-indexer.php';
require_once PI_CHATBOT_DIR . 'includes/class-entities.php';
require_once PI_CHATBOT_DIR . 'includes/class-retriever.php';
require_once PI_CHATBOT_DIR . 'includes/class-generator.php';
require_once PI_CHATBOT_DIR . 'includes/class-rest.php';
require_once PI_CHATBOT_DIR . 'includes/class-admin.php';

register_activation_hook(__FILE__, ['Pi_Chatbot_Indexer', 'on_activate']);

add_action('plugins_loaded', 'pi_chatbot_bootstrap');
function pi_chatbot_bootstrap() {
    load_plugin_textdomain('pi-chatbot', false, dirname(plugin_basename(__FILE__)) . '/languages');
    Pi_Chatbot_Indexer::maybe_upgrade();
    Pi_Chatbot_Indexer::instance();
    Pi_Chatbot_Rest::instance();
    Pi_Chatbot_Admin::instance();
}

/**
 * Cấu hình hiệu lực = default merge với option đã lưu (đổi qua trang admin).
 * llm7_keys: key LLM7 (round-robin nếu thêm nhiều key qua trang admin) — gọi THẲNG
 * https://api.llm7.io, KHÔNG qua Luna-Proxy/Qwen.
 *
 * Không hardcode key mặc định ở đây — secret sống trong source không rotate được
 * nếu không sửa code. Key sống hoàn toàn trong option DB, nhập/đổi qua trang admin
 * Pi Chatbot (migration 1 lần từ bản cũ đã hoàn tất, hàm migration đã xoá khỏi
 * source — xem lịch sử sửa đổi nếu cần biết chi tiết).
 *
 * Ép kiểu llm7_keys về mảng NGAY TẠI ĐÂY — 1 chỗ duy nhất, đóng cùng lúc 3 đường
 * fatal PHP 8 nếu option này từng bị lưu sai kiểu (scalar thay vì mảng):
 * implode() lúc render trang admin, preg_split() lúc lưu settings, và
 * foreach ($keys as $key) trong Generator::call_llm7() ở ngay request đang phục
 * vụ khách — fatal ở đây là fatal trên đường trả lời chat, nặng hơn nhiều so với
 * fatal ở trang admin.
 */
function pi_chatbot_settings() {
    $defaults = [
        'llm7_keys'  => [],
        'model'      => 'gpt-oss:20b',
        'top_k'      => 6,
        'rate_limit' => 10, // số câu hỏi / phút / IP
    ];
    $saved = get_option(PI_CHATBOT_OPTION, []);
    if (!is_array($saved)) $saved = [];
    $settings = array_merge($defaults, $saved);
    if (!is_array($settings['llm7_keys'])) {
        $settings['llm7_keys'] = array_filter([(string) $settings['llm7_keys']]);
    }
    return $settings;
}

add_action('save_post', ['Pi_Chatbot_Indexer', 'on_save_post'], 20, 3);
add_action('before_delete_post', ['Pi_Chatbot_Indexer', 'on_delete_post']);

add_action('wp_enqueue_scripts', 'pi_chatbot_enqueue_frontend');
function pi_chatbot_enqueue_frontend() {
    if (!function_exists('is_plugin_active')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    // Theme chỉ render nút #sh-toggle khi plugin này active — an toàn khi tự kiểm tra lại ở đây.
    if (!is_plugin_active('pi-chatbot/pi-chatbot.php')) return;

    // Bust cache theo mtime file thay vì version tĩnh — sửa CSS/JS là trình duyệt tải
    // bản mới ngay, không cần bump version tay hay xoá cache thủ công.
    $css_file = PI_CHATBOT_DIR . 'assets/chatbot.css';
    $js_file  = PI_CHATBOT_DIR . 'assets/chatbot.js';
    wp_enqueue_style('pi-chatbot', PI_CHATBOT_URL . 'assets/chatbot.css', [], file_exists($css_file) ? filemtime($css_file) : PI_CHATBOT_VERSION);
    wp_enqueue_script('pi-chatbot', PI_CHATBOT_URL . 'assets/chatbot.js', [], file_exists($js_file) ? filemtime($js_file) : PI_CHATBOT_VERSION, true);
    wp_localize_script('pi-chatbot', 'PiChatbotConfig', [
        'restUrl'   => esc_url_raw(rest_url('pi/v1/chatbot/ask')),
        'nonce'     => wp_create_nonce('wp_rest'),
        // Cùng nguồn với Pi_Chatbot_Rest::MAX_MESSAGE_LENGTH — trước đây "500" là số
        // cứng riêng ở cả PHP lẫn thuộc tính maxlength trong chatbot.js, dễ lệch nếu
        // sau này đổi 1 chỗ mà quên chỗ kia.
        'maxLength' => Pi_Chatbot_Rest::MAX_MESSAGE_LENGTH,
    ]);
}
