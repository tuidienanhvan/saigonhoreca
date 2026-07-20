<?php
/**
 * Admin — trang cấu hình đơn giản: nút Reindex, đếm bản ghi, cấu hình key LLM7/model.
 *
 * @package PiChatbot
 */

if (!defined('ABSPATH')) exit;

class Pi_Chatbot_Admin {
    private static $instance = null;

    public static function instance() {
        if (self::$instance === null) self::$instance = new self();
        return self::$instance;
    }

    private function __construct() {
        add_action('admin_menu', [$this, 'add_menu']);
        add_action('admin_post_pi_chatbot_reindex', [$this, 'handle_reindex']);
        add_action('admin_post_pi_chatbot_save_settings', [$this, 'handle_save_settings']);
    }

    public function add_menu() {
        add_menu_page(
            __('Pi Chatbot', 'pi-chatbot'),
            __('Pi Chatbot', 'pi-chatbot'),
            'manage_options',
            'pi-chatbot',
            [$this, 'render_page'],
            'dashicons-format-chat',
            58
        );
    }

    public function render_page() {
        if (!current_user_can('manage_options')) return;

        $settings = pi_chatbot_settings();
        $count = (int) get_option('pi_chatbot_index_count', 0);
        $last = get_option('pi_chatbot_last_reindex', '');
        $notice = isset($_GET['reindexed']) ? (int) $_GET['reindexed'] : null;
        $saved = isset($_GET['saved']);
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Pi Chatbot', 'pi-chatbot'); ?></h1>

            <?php if ($notice !== null) : ?>
                <div class="notice notice-success"><p><?php echo esc_html(sprintf(
                    /* translators: %d: number of indexed records */
                    __('Đã reindex xong — %d bản ghi.', 'pi-chatbot'), $notice
                )); ?></p></div>
            <?php endif; ?>
            <?php if (!empty($_GET['reindex_error'])) : ?>
                <div class="notice notice-error"><p><?php echo esc_html(sprintf(
                    /* translators: %s: error message */
                    __('Reindex thất bại: %s', 'pi-chatbot'),
                    sanitize_text_field(wp_unslash($_GET['reindex_error']))
                )); ?></p></div>
            <?php endif; ?>
            <?php if ($saved) : ?>
                <div class="notice notice-success"><p><?php esc_html_e('Đã lưu cấu hình.', 'pi-chatbot'); ?></p></div>
            <?php endif; ?>

            <h2><?php esc_html_e('Chỉ mục dữ liệu', 'pi-chatbot'); ?></h2>
            <p><?php esc_html_e('Số bản ghi đã index:', 'pi-chatbot'); ?> <strong><?php echo esc_html($count); ?></strong>
                <?php if ($last) : ?> — <?php esc_html_e('lần reindex gần nhất:', 'pi-chatbot'); ?> <?php echo esc_html($last); ?><?php endif; ?></p>

            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <?php wp_nonce_field('pi_chatbot_reindex'); ?>
                <input type="hidden" name="action" value="pi_chatbot_reindex">
                <?php submit_button(__('Reindex ngay', 'pi-chatbot'), 'primary'); ?>
            </form>

            <hr>

            <h2><?php esc_html_e('Cấu hình generation (LLM7 API — gọi thẳng, không qua proxy)', 'pi-chatbot'); ?></h2>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <?php wp_nonce_field('pi_chatbot_save_settings'); ?>
                <input type="hidden" name="action" value="pi_chatbot_save_settings">
                <table class="form-table">
                    <tr>
                        <th><label for="llm7_keys"><?php esc_html_e('API keys LLM7', 'pi-chatbot'); ?></label></th>
                        <td><textarea class="large-text code" rows="4" id="llm7_keys" name="llm7_keys"><?php echo esc_textarea(implode("\n", $settings['llm7_keys'])); ?></textarea>
                            <p class="description"><?php esc_html_e('Mỗi dòng 1 key — round-robin, key nào lỗi/hết quota tự chuyển key kế tiếp.', 'pi-chatbot'); ?></p></td>
                    </tr>
                    <tr>
                        <th><label for="model"><?php esc_html_e('Model', 'pi-chatbot'); ?></label></th>
                        <td><input type="text" class="regular-text" id="model" name="model" value="<?php echo esc_attr($settings['model']); ?>"></td>
                    </tr>
                    <tr>
                        <th><label for="top_k"><?php esc_html_e('Top-K context', 'pi-chatbot'); ?></label></th>
                        <td><input type="number" min="1" max="20" id="top_k" name="top_k" value="<?php echo esc_attr($settings['top_k']); ?>"></td>
                    </tr>
                    <tr>
                        <th><label for="rate_limit"><?php esc_html_e('Giới hạn (câu/phút/IP)', 'pi-chatbot'); ?></label></th>
                        <td><input type="number" min="1" max="100" id="rate_limit" name="rate_limit" value="<?php echo esc_attr($settings['rate_limit']); ?>"></td>
                    </tr>
                </table>
                <?php submit_button(__('Lưu cấu hình', 'pi-chatbot')); ?>
            </form>
        </div>
        <?php
    }

    public function handle_reindex() {
        if (!current_user_can('manage_options')) wp_die('Không có quyền.');
        check_admin_referer('pi_chatbot_reindex');
        // Reindex là DB thuần (không còn gọi LLM) nên chỉ mất vài giây cho ~419 bản
        // ghi — set_time_limit chỉ để phòng khi catalog phình to sau này.
        if (function_exists('set_time_limit')) set_time_limit(0);
        $result = Pi_Chatbot_Indexer::reindex_all();
        // Trước đây không phân nhánh — reindex_all() lỗi vẫn hiện thông báo xanh
        // "thành công" (đúng kiểu lỗi đã làm hỏng plugin lần trước: RENAME TABLE
        // fail im lặng, admin không hề biết index không hề được cập nhật).
        $args = is_wp_error($result)
            ? ['page' => 'pi-chatbot', 'reindex_error' => rawurlencode($result->get_error_message())]
            : ['page' => 'pi-chatbot', 'reindexed' => (int) $result];
        wp_safe_redirect(add_query_arg($args, admin_url('admin.php')));
        exit;
    }

    public function handle_save_settings() {
        if (!current_user_can('manage_options')) wp_die('Không có quyền.');
        check_admin_referer('pi_chatbot_save_settings');

        $settings = pi_chatbot_settings();
        if (isset($_POST['llm7_keys'])) {
            $raw = wp_unslash($_POST['llm7_keys']);
            // $_POST['llm7_keys'] LUÔN phải là string (textarea, không phải mảng) —
            // nhưng 1 request thủ công gửi llm7_keys[]=a&llm7_keys[]=b sẽ đưa mảng
            // vào đây, và preg_split() trên mảng là TypeError fatal ở PHP 8 (nonce +
            // capability đã chặn CSRF nên không phải vector tấn công, nhưng vẫn là
            // fatal thật nếu admin tự gõ nhầm URL hoặc dùng công cụ test API).
            $keys = is_string($raw) ? preg_split('/\r\n|\r|\n/', $raw) : [];
            $settings['llm7_keys'] = array_values(array_filter(array_map('trim', $keys)));
        }
        $settings['model']      = sanitize_text_field(wp_unslash($_POST['model'] ?? $settings['model']));
        $settings['top_k']      = max(1, min(20, (int) ($_POST['top_k'] ?? $settings['top_k'])));
        $settings['rate_limit'] = max(1, min(100, (int) ($_POST['rate_limit'] ?? $settings['rate_limit'])));
        // autoload=false — option chứa API key, không cần nạp vào mọi request
        // front-end (chỉ pi_chatbot_settings() mới cần đọc, không phải mọi trang).
        update_option(PI_CHATBOT_OPTION, $settings, false);

        wp_safe_redirect(add_query_arg(['page' => 'pi-chatbot', 'saved' => 1], admin_url('admin.php')));
        exit;
    }
}
