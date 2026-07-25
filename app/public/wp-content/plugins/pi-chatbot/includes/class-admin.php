<?php
/**
 * Admin — simple config page: Reindex button, record count, LLM7 key/model config.
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
                    __('Reindex complete — %d records.', 'pi-chatbot'), $notice
                )); ?></p></div>
            <?php endif; ?>
            <?php if (!empty($_GET['reindex_error'])) : ?>
                <div class="notice notice-error"><p><?php echo esc_html(sprintf(
                    /* translators: %s: error message */
                    __('Reindex failed: %s', 'pi-chatbot'),
                    sanitize_text_field(wp_unslash($_GET['reindex_error']))
                )); ?></p></div>
            <?php endif; ?>
            <?php if ($saved) : ?>
                <div class="notice notice-success"><p><?php esc_html_e('Settings saved.', 'pi-chatbot'); ?></p></div>
            <?php endif; ?>
            <h2><?php esc_html_e('Data index', 'pi-chatbot'); ?></h2>
            <p><?php esc_html_e('Records indexed:', 'pi-chatbot'); ?> <strong><?php echo esc_html($count); ?></strong>
                <?php if ($last) : ?> — <?php esc_html_e('last reindex:', 'pi-chatbot'); ?> <?php echo esc_html($last); ?><?php endif; ?></p>

            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <?php wp_nonce_field('pi_chatbot_reindex'); ?>
                <input type="hidden" name="action" value="pi_chatbot_reindex">
                <?php submit_button(__('Reindex now', 'pi-chatbot'), 'primary'); ?>
            </form>

            <hr>

            <h2><?php esc_html_e('Generation settings (LLM7 API — called directly, no proxy)', 'pi-chatbot'); ?></h2>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <?php wp_nonce_field('pi_chatbot_save_settings'); ?>
                <input type="hidden" name="action" value="pi_chatbot_save_settings">
                <table class="form-table">
                    <tr>
                        <th><label for="llm7_keys"><?php esc_html_e('LLM7 API keys', 'pi-chatbot'); ?></label></th>
                        <td><textarea class="large-text code" rows="4" id="llm7_keys" name="llm7_keys"><?php echo esc_textarea(implode("\n", $settings['llm7_keys'])); ?></textarea>
                            <p class="description"><?php esc_html_e('One key per line — round-robin, any key that errors/runs out of quota automatically moves to the next key.', 'pi-chatbot'); ?></p></td>
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
                        <th><label for="rate_limit"><?php esc_html_e('Rate limit (requests/min/IP)', 'pi-chatbot'); ?></label></th>
                        <td><input type="number" min="1" max="100" id="rate_limit" name="rate_limit" value="<?php echo esc_attr($settings['rate_limit']); ?>"></td>
                    </tr>
                </table>
                <?php submit_button(__('Save settings', 'pi-chatbot')); ?>
            </form>

            <hr>

            <h2><?php esc_html_e('Router (LLM intent classifier)', 'pi-chatbot'); ?></h2>
            <p class="description">
                <?php esc_html_e('Runs on EVERY message — classifies intent (price/contact/service/project/product) and extracts search keywords. Replaced the old rule-based Vietnamese keyword matching by product decision (accepted trade-off: one LLM7 call per message instead of instant/free classification). If disabled, the bot degrades to a plain product search on the raw message with no price/contact/service/project handling. See docs/plans/pi-chatbot-adaptive-router.md.', 'pi-chatbot'); ?>
            </p>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <?php wp_nonce_field('pi_chatbot_save_settings'); ?>
                <input type="hidden" name="action" value="pi_chatbot_save_settings">
                <table class="form-table">
                    <tr>
                        <th><label for="router_enabled"><?php esc_html_e('Enable router', 'pi-chatbot'); ?></label></th>
                        <td><label><input type="checkbox" id="router_enabled" name="router_enabled" value="1" <?php checked(!empty($settings['router_enabled'])); ?>> <?php esc_html_e('Enabled', 'pi-chatbot'); ?></label></td>
                    </tr>
                    <tr>
                        <th><label for="router_model"><?php esc_html_e('Router model', 'pi-chatbot'); ?></label></th>
                        <td><input type="text" class="regular-text" id="router_model" name="router_model" value="<?php echo esc_attr($settings['router_model']); ?>">
                            <p class="description"><?php esc_html_e('Use a turbo model WITHOUT reasoning (e.g. codestral-latest) — reasoning models like gpt-oss:20b spend tokens on hidden reasoning and, measured for real, return empty output when used as a router.', 'pi-chatbot'); ?></p></td>
                    </tr>
                    <tr>
                        <th><label for="router_keys"><?php esc_html_e('Dedicated API keys for the router', 'pi-chatbot'); ?></label></th>
                        <td><textarea class="large-text code" rows="2" id="router_keys" name="router_keys"><?php echo esc_textarea(implode("\n", $settings['router_keys'])); ?></textarea>
                            <p class="description"><?php esc_html_e('Leave empty to share the LLM7 API keys above.', 'pi-chatbot'); ?></p></td>
                    </tr>
                </table>
                <?php submit_button(__('Save router settings', 'pi-chatbot')); ?>
            </form>

            <hr>

            <?php $this->render_accuracy_section($settings); ?>

            <hr>

            <?php $this->render_learning_section(); ?>
        </div>
        <?php
    }

    /**
     * Max-accuracy pipeline — the settings that only affect questions the router
     * labels "hard". Grouped into one section on purpose: enabling semantic
     * search without an API key, or a key without the master switch, does
     * nothing, and splitting them across the page made that easy to get wrong.
     */
    private function render_accuracy_section($settings) {
        ?>
        <h2><?php esc_html_e('Max accuracy (adaptive)', 'pi-chatbot'); ?></h2>
        <p class="description" style="max-width:820px;">
            <?php esc_html_e('The router labels every product question easy or hard. Easy questions keep the existing fast path. Hard ones — vague, comparative, multi-constraint, use-case-shaped, or mixed-language — additionally run semantic search fused with keyword search, an LLM reranking pass, and a fact check of the answer against the retrieved data. Those questions take roughly 20-30 seconds instead of 10, which is the trade this page exists to make explicit.', 'pi-chatbot'); ?>
        </p>

        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <?php wp_nonce_field('pi_chatbot_save_settings'); ?>
            <input type="hidden" name="action" value="pi_chatbot_save_settings">
            <input type="hidden" name="accuracy_form" value="1">
            <table class="form-table">
                <tr>
                    <th><label for="max_accuracy_enabled"><?php esc_html_e('Adaptive max accuracy', 'pi-chatbot'); ?></label></th>
                    <td><label><input type="checkbox" id="max_accuracy_enabled" name="max_accuracy_enabled" value="1" <?php checked(!empty($settings['max_accuracy_enabled'])); ?>> <?php esc_html_e('Enabled', 'pi-chatbot'); ?></label>
                        <p class="description"><?php esc_html_e('Off = every question takes the fast path, exactly as before this feature existed.', 'pi-chatbot'); ?></p></td>
                </tr>
                <tr>
                    <th><label for="rerank_enabled"><?php esc_html_e('LLM reranking', 'pi-chatbot'); ?></label></th>
                    <td><label><input type="checkbox" id="rerank_enabled" name="rerank_enabled" value="1" <?php checked(!empty($settings['rerank_enabled'])); ?>> <?php esc_html_e('Enabled', 'pi-chatbot'); ?></label>
                        <p class="description"><?php esc_html_e('Re-orders the retrieved products by reading them against the question. This is what tells a 2-burner range apart from a 4-burner one when the titles are otherwise identical.', 'pi-chatbot'); ?></p></td>
                </tr>
                <tr>
                    <th><label for="rerank_model"><?php esc_html_e('Reranking / fact-check model', 'pi-chatbot'); ?></label></th>
                    <td><input type="text" class="regular-text" id="rerank_model" name="rerank_model" value="<?php echo esc_attr($settings['rerank_model']); ?>">
                        <p class="description"><?php esc_html_e('Same kind of fast, non-reasoning model as the router.', 'pi-chatbot'); ?></p></td>
                </tr>
                <tr>
                    <th><label for="verify_enabled"><?php esc_html_e('Fact-check answers', 'pi-chatbot'); ?></label></th>
                    <td><label><input type="checkbox" id="verify_enabled" name="verify_enabled" value="1" <?php checked(!empty($settings['verify_enabled'])); ?>> <?php esc_html_e('Enabled', 'pi-chatbot'); ?></label>
                        <p class="description"><?php esc_html_e('A second model checks the written answer against the retrieved data and sends it back once if it finds an invented price, capacity or model number.', 'pi-chatbot'); ?></p></td>
                </tr>
                <tr>
                    <th><label for="multi_query_max"><?php esc_html_e('Extra query rewrites', 'pi-chatbot'); ?></label></th>
                    <td><input type="number" min="0" max="5" id="multi_query_max" name="multi_query_max" value="<?php echo esc_attr($settings['multi_query_max']); ?>">
                        <p class="description"><?php esc_html_e('How many alternative phrasings of a hard question to search alongside the original. 0 disables multi-query.', 'pi-chatbot'); ?></p></td>
                </tr>
            </table>
            <?php submit_button(__('Save accuracy settings', 'pi-chatbot')); ?>
        </form>
        <?php
    }

    /**
     * Learning from conversations — inspects real question logs to find data gaps
     * instead of guessing. Inspired by the "sync_turn + curator" pattern from Hermes
     * Agent (NousResearch, github.com/NousResearch/hermes-agent): log every turn, then
     * aggregate into learned signals. At pi-chatbot's scale, the "curator" is just a
     * single GROUP BY query — no LLM summarization needed like Hermes, because the
     * problem here is much narrower (finding repeated zero-result questions), not
     * building a multi-session user model.
     */
    private function render_learning_section() {
        $stats = Pi_Chatbot_Log::stats();
        $top = Pi_Chatbot_Log::top_unanswered(15, 30);
        ?>
        <h2><?php esc_html_e('Learning from conversations — data gaps', 'pi-chatbot'); ?></h2>
        <p class="description">
            <?php esc_html_e('Logs every real question (excluding ones answered from cache) and whether a matching product/context was found. The table below shows the most-repeated questions in the last 30 days that found NOTHING — a measured, not guessed, signal of missing data or missing keywords.', 'pi-chatbot'); ?>
        </p>
        <p>
            <?php echo esc_html(sprintf(
                /* translators: 1: total logged, 2: unanswered count, 3: distinct unanswered, 4: last 24h */
                __('Total logged questions: %1$d — no context found: %2$d (%3$d distinct questions) — last 24h: %4$d', 'pi-chatbot'),
                $stats['total'], $stats['total_unanswered'], $stats['distinct_unanswered'], $stats['last_24h']
            )); ?>
        </p>
        <?php if (empty($top)) : ?>
            <p><em><?php esc_html_e('No questions missing context in the last 30 days.', 'pi-chatbot'); ?></em></p>
        <?php else : ?>
            <table class="widefat striped" style="max-width: 900px;">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Question', 'pi-chatbot'); ?></th>
                        <th style="width:100px;"><?php esc_html_e('Count', 'pi-chatbot'); ?></th>
                        <th style="width:180px;"><?php esc_html_e('Last seen', 'pi-chatbot'); ?></th>
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
        if (!current_user_can('manage_options')) wp_die('Permission denied.');
        check_admin_referer('pi_chatbot_reindex');
        // Reindex is pure DB work (no more LLM calls) so it only takes a few seconds for
        // ~419 records — set_time_limit is just a safeguard in case the catalog grows
        // much larger later.
        if (function_exists('set_time_limit')) set_time_limit(0);
        $result = Pi_Chatbot_Indexer::reindex_all();
        // Previously there was no branching here — if reindex_all() failed, it still
        // showed a green "success" notice (exactly the kind of bug that broke the
        // plugin last time: RENAME TABLE failed silently and the admin had no idea the
        // index hadn't actually been updated).
        $args = is_wp_error($result)
            ? ['page' => 'pi-chatbot', 'reindex_error' => rawurlencode($result->get_error_message())]
            : ['page' => 'pi-chatbot', 'reindexed' => (int) $result];
        wp_safe_redirect(add_query_arg($args, admin_url('admin.php')));
        exit;
    }

    public function handle_save_settings() {
        if (!current_user_can('manage_options')) wp_die('Permission denied.');
        check_admin_referer('pi_chatbot_save_settings');

        $settings = pi_chatbot_settings();
        if (isset($_POST['llm7_keys'])) {
            $raw = wp_unslash($_POST['llm7_keys']);
            // $_POST['llm7_keys'] should ALWAYS be a string (textarea, not an array) —
            // but a hand-crafted request sending llm7_keys[]=a&llm7_keys[]=b would put an
            // array here, and preg_split() on an array is a fatal TypeError on PHP 8
            // (nonce + capability check already block CSRF so this isn't an attack
            // vector, but it's still a real fatal if an admin mistypes a URL or uses an
            // API testing tool).
            $keys = is_string($raw) ? preg_split('/\r\n|\r|\n/', $raw) : [];
            $settings['llm7_keys'] = array_values(array_filter(array_map('trim', $keys)));
        }
        $settings['model']      = sanitize_text_field(wp_unslash($_POST['model'] ?? $settings['model']));
        $settings['top_k']      = max(1, min(20, (int) ($_POST['top_k'] ?? $settings['top_k'])));
        $settings['rate_limit'] = max(1, min(100, (int) ($_POST['rate_limit'] ?? $settings['rate_limit'])));

        // Router — separate form (2 forms on the same page), the field is only present
        // when that specific form was submitted. The 'router_enabled' checkbox being
        // absent from $_POST means it was unchecked (HTML doesn't send an unchecked
        // checkbox) — ONLY treat it as "disabled" when the router form was actually
        // submitted (detected via the presence of 'router_model'), to avoid the
        // "Save settings" form (which has no router field) accidentally disabling a
        // router that's currently enabled.
        if (isset($_POST['router_model'])) {
            $settings['router_enabled'] = !empty($_POST['router_enabled']);
            $settings['router_model']   = sanitize_text_field(wp_unslash($_POST['router_model']));
            if (isset($_POST['router_keys'])) {
                $raw_router = wp_unslash($_POST['router_keys']);
                $rkeys = is_string($raw_router) ? preg_split('/\r\n|\r|\n/', $raw_router) : [];
                $settings['router_keys'] = array_values(array_filter(array_map('trim', $rkeys)));
            }
        }
        // Max-accuracy — a THIRD form on this page, gated the same way as the
        // router block above and for the same reason: an unchecked checkbox is
        // simply absent from $_POST, so without a marker field proving THIS form
        // was the one submitted, saving the generation settings would silently
        // switch off every accuracy feature. A dedicated hidden field is used
        // rather than piggybacking on a text input, so the guard can't be broken
        // later by renaming or removing whichever field it happened to test.
        if (isset($_POST['accuracy_form'])) {
            $settings['max_accuracy_enabled'] = !empty($_POST['max_accuracy_enabled']);
            $settings['rerank_enabled']       = !empty($_POST['rerank_enabled']);
            $settings['verify_enabled']       = !empty($_POST['verify_enabled']);

            $settings['rerank_model']    = sanitize_text_field(wp_unslash($_POST['rerank_model'] ?? $settings['rerank_model']));
            $settings['multi_query_max'] = max(0, min(5, (int) ($_POST['multi_query_max'] ?? $settings['multi_query_max'])));

        }

        // autoload=false — this option contains an API key, no need to load it on every
        // front-end request (only pi_chatbot_settings() needs to read it, not every page).
        update_option(PI_CHATBOT_OPTION, $settings, false);

        wp_safe_redirect(add_query_arg(['page' => 'pi-chatbot', 'saved' => 1], admin_url('admin.php')));
        exit;
    }
}
