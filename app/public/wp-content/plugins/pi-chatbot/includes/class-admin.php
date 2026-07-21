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

            <hr>

            <h2><?php esc_html_e('Router (Adaptive RAG) — lưới an toàn cho câu hỏi tiếng Việt không nhắc brand', 'pi-chatbot'); ?></h2>
            <p class="description">
                <?php esc_html_e('CHỈ chạy khi tìm kiếm thường (rule-based) đã trả rỗng — không thay thế, không chạy cho mọi câu. Mặc định TẮT, chỉ bật sau khi đã xem qua tests/eval-retrieval.php. Xem docs/plans/pi-chatbot-adaptive-router.md.', 'pi-chatbot'); ?>
            </p>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <?php wp_nonce_field('pi_chatbot_save_settings'); ?>
                <input type="hidden" name="action" value="pi_chatbot_save_settings">
                <table class="form-table">
                    <tr>
                        <th><label for="router_enabled"><?php esc_html_e('Bật router', 'pi-chatbot'); ?></label></th>
                        <td><label><input type="checkbox" id="router_enabled" name="router_enabled" value="1" <?php checked(!empty($settings['router_enabled'])); ?>> <?php esc_html_e('Bật', 'pi-chatbot'); ?></label></td>
                    </tr>
                    <tr>
                        <th><label for="router_model"><?php esc_html_e('Model router', 'pi-chatbot'); ?></label></th>
                        <td><input type="text" class="regular-text" id="router_model" name="router_model" value="<?php echo esc_attr($settings['router_model']); ?>">
                            <p class="description"><?php esc_html_e('Nên dùng model turbo KHÔNG reasoning (vd codestral-latest) — model reasoning như gpt-oss:20b tốn token vào suy luận ẩn, đo thật trả về rỗng khi dùng làm router.', 'pi-chatbot'); ?></p></td>
                    </tr>
                    <tr>
                        <th><label for="router_keys"><?php esc_html_e('API keys riêng cho router', 'pi-chatbot'); ?></label></th>
                        <td><textarea class="large-text code" rows="2" id="router_keys" name="router_keys"><?php echo esc_textarea(implode("\n", $settings['router_keys'])); ?></textarea>
                            <p class="description"><?php esc_html_e('Để trống = dùng chung API keys LLM7 ở trên.', 'pi-chatbot'); ?></p></td>
                    </tr>
                </table>
                <?php submit_button(__('Lưu cấu hình router', 'pi-chatbot')); ?>
            </form>

            <hr>

            <?php $this->render_learning_section(); ?>
        </div>
        <?php
    }

    /**
     * Học từ hội thoại — soi log câu hỏi thật để tìm khoảng trống dữ liệu, thay vì
     * đoán. Cảm hứng từ pattern "sync_turn + curator" của Hermes Agent (NousResearch,
     * github.com/NousResearch/hermes-agent): ghi mỗi lượt hỏi, sau đó gom lại thành
     * tín hiệu học được. Ở quy mô pi-chatbot, "curator" chỉ là 1 câu GROUP BY —
     * không cần LLM tóm tắt hội thoại như Hermes vì bài toán ở đây hẹp hơn nhiều
     * (tìm câu hỏi lặp lại 0 kết quả), không phải xây mô hình người dùng đa phiên.
     */
    private function render_learning_section() {
        $stats = Pi_Chatbot_Log::stats();
        $top = Pi_Chatbot_Log::top_unanswered(15, 30);
        ?>
        <h2><?php esc_html_e('Học từ hội thoại — khoảng trống dữ liệu', 'pi-chatbot'); ?></h2>
        <p class="description">
            <?php esc_html_e('Ghi lại mỗi câu hỏi thật (không tính câu đã trả lời từ cache) và việc có tìm thấy sản phẩm/context hay không. Bảng dưới là câu hỏi lặp lại nhiều lần nhất trong 30 ngày mà KHÔNG tìm thấy gì — dấu hiệu thiếu dữ liệu hoặc thiếu từ khoá, đo được thay vì phán đoán.', 'pi-chatbot'); ?>
        </p>
        <p>
            <?php echo esc_html(sprintf(
                /* translators: 1: total logged, 2: unanswered count, 3: distinct unanswered, 4: last 24h */
                __('Tổng câu đã log: %1$d — không tìm thấy context: %2$d (%3$d câu hỏi khác nhau) — 24h gần nhất: %4$d', 'pi-chatbot'),
                $stats['total'], $stats['total_unanswered'], $stats['distinct_unanswered'], $stats['last_24h']
            )); ?>
        </p>
        <?php if (empty($top)) : ?>
            <p><em><?php esc_html_e('Chưa có câu hỏi nào bị thiếu context trong 30 ngày qua.', 'pi-chatbot'); ?></em></p>
        <?php else : ?>
            <table class="widefat striped" style="max-width: 900px;">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Câu hỏi', 'pi-chatbot'); ?></th>
                        <th style="width:100px;"><?php esc_html_e('Số lần', 'pi-chatbot'); ?></th>
                        <th style="width:180px;"><?php esc_html_e('Gần nhất', 'pi-chatbot'); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($top as $row) : ?>
                    <tr>
                        <td><?php echo esc_html($row->question_norm); ?></td>
                        <td><?php echo esc_html($row->cnt); ?></td>
                        <td><?php echo esc_html($row->last_seen); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
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

        // Router — form riêng (2 form trên cùng trang), field chỉ có mặt khi đúng form
        // đó được submit. checkbox 'router_enabled' vắng mặt trong $_POST nghĩa là bỏ
        // tick (HTML không gửi checkbox chưa check) — CHỈ coi là "tắt" khi form router
        // thực sự được gửi (nhận diện qua sự có mặt của 'router_model'), tránh form
        // "Lưu cấu hình generation" (không có field router) vô tình tắt router đang bật.
        if (isset($_POST['router_model'])) {
            $settings['router_enabled'] = !empty($_POST['router_enabled']);
            $settings['router_model']   = sanitize_text_field(wp_unslash($_POST['router_model']));
            if (isset($_POST['router_keys'])) {
                $raw_router = wp_unslash($_POST['router_keys']);
                $rkeys = is_string($raw_router) ? preg_split('/\r\n|\r|\n/', $raw_router) : [];
                $settings['router_keys'] = array_values(array_filter(array_map('trim', $rkeys)));
            }
        }
        // autoload=false — option chứa API key, không cần nạp vào mọi request
        // front-end (chỉ pi_chatbot_settings() mới cần đọc, không phải mọi trang).
        update_option(PI_CHATBOT_OPTION, $settings, false);

        wp_safe_redirect(add_query_arg(['page' => 'pi-chatbot', 'saved' => 1], admin_url('admin.php')));
        exit;
    }
}
