<?php
/**
 * Admin — simple config page: Reindex button, record count, LLM7 key/model config.
 *
 * Visual layer only (assets/admin.css + assets/admin.js, both scoped to this
 * one page) is styled to the saigonhoreca brand tokens (black + gold — see
 * D:\Documents\pi-design-system and the theme's own _brand.css, values copied
 * literally since this page has no build step and must not depend on the
 * theme being active). Every form field's name/id and every hidden guard
 * field is UNCHANGED from before — handle_save_settings()/handle_reindex()
 * were not touched, only render_page() and its helpers.
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
        add_action('admin_post_pi_chatbot_faq_action', [$this, 'handle_faq_action']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
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

    /** Loaded ONLY on this plugin's own admin page — checked by hook suffix,
     *  not by $_GET['page'], so a URL query string can't trick another screen
     *  into loading this CSS/JS. filemtime versioning matches the pattern
     *  already used for the public-facing widget assets in pi-chatbot.php. */
    public function enqueue_assets($hook) {
        if ($hook !== 'toplevel_page_pi-chatbot') return;
        wp_enqueue_style('dashicons');
        $css_file = PI_CHATBOT_DIR . 'assets/admin.css';
        $js_file  = PI_CHATBOT_DIR . 'assets/admin.js';
        wp_enqueue_style('pi-chatbot-admin', PI_CHATBOT_URL . 'assets/admin.css', ['dashicons'], file_exists($css_file) ? filemtime($css_file) : PI_CHATBOT_VERSION);
        wp_enqueue_script('pi-chatbot-admin', PI_CHATBOT_URL . 'assets/admin.js', [], file_exists($js_file) ? filemtime($js_file) : PI_CHATBOT_VERSION, true);
    }

    public function render_page() {
        if (!current_user_can('manage_options')) return;

        $settings = pi_chatbot_settings();
        $count = (int) get_option('pi_chatbot_index_count', 0);
        $last = get_option('pi_chatbot_last_reindex', '');
        $notice = isset($_GET['reindexed']) ? (int) $_GET['reindexed'] : null;
        $saved = isset($_GET['saved']);
        ?>
        <div class="wrap pi-chatbot-admin">
            <div class="pi-cba-head">
                <span class="dashicons dashicons-format-chat" aria-hidden="true"></span>
                <div>
                    <h1><?php esc_html_e('Pi Chatbot', 'pi-chatbot'); ?></h1>
                    <p class="pi-cba-sub"><?php esc_html_e('Retrieval + generation settings for the Saigon Horeca sales assistant.', 'pi-chatbot'); ?></p>
                </div>
            </div>

            <?php if ($notice !== null) : ?>
                <div class="pi-alert pi-alert--success"><span class="dashicons dashicons-yes-alt"></span><p><?php echo esc_html(sprintf(
                    /* translators: %d: number of indexed records */
                    __('Reindex complete — %d records.', 'pi-chatbot'), $notice
                )); ?></p></div>
            <?php endif; ?>
            <?php if (!empty($_GET['reindex_error'])) : ?>
                <div class="pi-alert pi-alert--danger"><span class="dashicons dashicons-warning"></span><p><?php echo esc_html(sprintf(
                    /* translators: %s: error message */
                    __('Reindex failed: %s', 'pi-chatbot'),
                    sanitize_text_field(wp_unslash($_GET['reindex_error']))
                )); ?></p></div>
            <?php endif; ?>
            <?php if ($saved) : ?>
                <div class="pi-alert pi-alert--success"><span class="dashicons dashicons-yes-alt"></span><p><?php esc_html_e('Settings saved.', 'pi-chatbot'); ?></p></div>
            <?php endif; ?>

            <div class="pi-card">
                <div class="pi-card__header">
                    <span class="pi-card__eyebrow">01 — Index</span>
                    <h2 class="pi-card__title"><?php esc_html_e('Data index', 'pi-chatbot'); ?></h2>
                </div>
                <div class="pi-card__body">
                <div class="pi-grid--stats">
                    <div>
                        <div class="pi-stat__value"><?php echo esc_html($count); ?></div>
                        <div class="pi-stat__label"><?php esc_html_e('Records indexed', 'pi-chatbot'); ?></div>
                    </div>
                    <?php if ($last) : ?>
                    <div>
                        <div class="pi-stat__value pi-stat__value--sm"><?php echo esc_html($last); ?></div>
                        <div class="pi-stat__label"><?php esc_html_e('Last reindex', 'pi-chatbot'); ?></div>
                    </div>
                    <?php endif; ?>
                </div>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <?php wp_nonce_field('pi_chatbot_reindex'); ?>
                    <input type="hidden" name="action" value="pi_chatbot_reindex">
                    <?php submit_button(__('Reindex now', 'pi-chatbot'), 'primary', 'submit', false); ?>
                </form>
                </div>
            </div>

            <div class="pi-card">
                <div class="pi-card__header">
                    <span class="pi-card__eyebrow">02 — Generation</span>
                    <h2 class="pi-card__title"><?php esc_html_e('Generation settings', 'pi-chatbot'); ?></h2>
                    <p class="pi-card__desc"><?php esc_html_e('LLM7 API — called directly, no proxy.', 'pi-chatbot'); ?></p>
                </div>
                <div class="pi-card__body pi-card__body--tight">
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <?php wp_nonce_field('pi_chatbot_save_settings'); ?>
                    <input type="hidden" name="action" value="pi_chatbot_save_settings">
                    <div class="pi-cba-fields">
                        <div class="pi-cba-field">
                            <label class="pi-cba-label" for="llm7_keys"><?php esc_html_e('LLM7 API keys', 'pi-chatbot'); ?></label>
                            <div>
                                <textarea class="pi-key-list-source" rows="4" id="llm7_keys" name="llm7_keys"><?php echo esc_textarea(implode("\n", $settings['llm7_keys'])); ?></textarea>
                                <p class="pi-cba-desc"><?php esc_html_e('Round-robin — any key that errors or runs out of quota automatically moves to the next one. Add as many as you have; more keys means more headroom under load.', 'pi-chatbot'); ?></p>
                            </div>
                        </div>
                        <div class="pi-cba-field">
                            <label class="pi-cba-label" for="model"><?php esc_html_e('Model', 'pi-chatbot'); ?></label>
                            <input type="text" id="model" name="model" value="<?php echo esc_attr($settings['model']); ?>">
                        </div>
                        <div class="pi-cba-field">
                            <label class="pi-cba-label" for="top_k"><?php esc_html_e('Top-K context', 'pi-chatbot'); ?></label>
                            <input type="number" min="1" max="20" id="top_k" name="top_k" value="<?php echo esc_attr($settings['top_k']); ?>">
                        </div>
                        <div class="pi-cba-field">
                            <label class="pi-cba-label" for="rate_limit"><?php esc_html_e('Rate limit', 'pi-chatbot'); ?></label>
                            <div>
                                <input type="number" min="1" max="100" id="rate_limit" name="rate_limit" value="<?php echo esc_attr($settings['rate_limit']); ?>">
                                <p class="pi-cba-desc"><?php esc_html_e('Requests per minute, per visitor IP.', 'pi-chatbot'); ?></p>
                            </div>
                        </div>
                        <div class="pi-cba-field">
                            <label class="pi-cba-label" for="llm_daily_budget"><?php esc_html_e('Daily LLM budget', 'pi-chatbot'); ?></label>
                            <div>
                                <input type="number" min="10" max="100000" id="llm_daily_budget" name="llm_daily_budget" value="<?php echo esc_attr($settings['llm_daily_budget']); ?>">
                                <p class="pi-cba-desc"><?php esc_html_e('Site-wide cap on real LLM7 calls per day (not per visitor). Past 80% of this, the assistant stops doing the extra reranking/fact-check passes. Past 100%, it answers from retrieval only (still useful, no LLM call) instead of failing outright.', 'pi-chatbot'); ?></p>
                            </div>
                        </div>
                    </div>
                    <?php submit_button(__('Save settings', 'pi-chatbot'), 'primary', 'submit', false, ['style' => 'margin-top:18px;']); ?>
                </form>
                </div>
            </div>

            <div class="pi-card">
                <div class="pi-card__header">
                    <span class="pi-card__eyebrow">03 — Router</span>
                    <h2 class="pi-card__title"><?php esc_html_e('Router (LLM intent classifier)', 'pi-chatbot'); ?></h2>
                    <p class="pi-card__desc">
                        <?php esc_html_e('Runs on every message — classifies intent (price/contact/service/project/product) and extracts search keywords. If disabled, the bot degrades to a plain product search on the raw message with no price/contact/service/project handling.', 'pi-chatbot'); ?>
                    </p>
                </div>
                <div class="pi-card__body pi-card__body--tight">
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <?php wp_nonce_field('pi_chatbot_save_settings'); ?>
                    <input type="hidden" name="action" value="pi_chatbot_save_settings">
                    <div class="pi-cba-fields">
                        <div class="pi-cba-field">
                            <span class="pi-cba-label"><?php esc_html_e('Enable router', 'pi-chatbot'); ?></span>
                            <div class="pi-cba-checkbox-row">
                                <input type="checkbox" id="router_enabled" name="router_enabled" value="1" <?php checked(!empty($settings['router_enabled'])); ?>>
                                <label for="router_enabled"><?php esc_html_e('Enabled', 'pi-chatbot'); ?></label>
                            </div>
                        </div>
                        <div class="pi-cba-field">
                            <label class="pi-cba-label" for="router_model"><?php esc_html_e('Router model', 'pi-chatbot'); ?></label>
                            <div>
                                <input type="text" id="router_model" name="router_model" value="<?php echo esc_attr($settings['router_model']); ?>">
                                <p class="pi-cba-desc"><?php esc_html_e('A fast turbo model WITHOUT reasoning (e.g. codestral-latest) — reasoning models spend tokens on hidden reasoning and can return empty output when used as a router.', 'pi-chatbot'); ?></p>
                            </div>
                        </div>
                        <div class="pi-cba-field">
                            <label class="pi-cba-label" for="router_keys"><?php esc_html_e('Dedicated router keys', 'pi-chatbot'); ?></label>
                            <div>
                                <textarea class="pi-key-list-source" rows="2" id="router_keys" name="router_keys"><?php echo esc_textarea(implode("\n", $settings['router_keys'])); ?></textarea>
                                <p class="pi-cba-desc"><?php esc_html_e('Leave empty to share the LLM7 API keys above.', 'pi-chatbot'); ?></p>
                            </div>
                        </div>
                    </div>
                    <?php submit_button(__('Save router settings', 'pi-chatbot'), 'primary', 'submit', false, ['style' => 'margin-top:18px;']); ?>
                </form>
                </div>
            </div>

            <?php $this->render_accuracy_section($settings); ?>

            <?php $this->render_learning_section(); ?>

            <?php $this->render_leads_section(); ?>

            <?php $this->render_faq_section(); ?>
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
        <div class="pi-card">
            <div class="pi-card__header">
                <span class="pi-card__eyebrow">04 — Adaptive pipeline</span>
                <h2 class="pi-card__title"><?php esc_html_e('Max accuracy', 'pi-chatbot'); ?></h2>
                <p class="pi-card__desc">
                    <?php esc_html_e('The router labels every product question easy or hard. Easy questions keep the fast path. Hard ones — vague, comparative, multi-constraint, use-case-shaped, or mixed-language — additionally run an LLM reranking pass and a fact check of the answer against the retrieved data, taking roughly 20-30 seconds instead of 10.', 'pi-chatbot'); ?>
                </p>
            </div>
            <div class="pi-card__body pi-card__body--tight">
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <?php wp_nonce_field('pi_chatbot_save_settings'); ?>
                <input type="hidden" name="action" value="pi_chatbot_save_settings">
                <input type="hidden" name="accuracy_form" value="1">
                <div class="pi-cba-fields">
                    <div class="pi-cba-field">
                        <span class="pi-cba-label"><?php esc_html_e('Adaptive max accuracy', 'pi-chatbot'); ?></span>
                        <div>
                            <div class="pi-cba-checkbox-row">
                                <input type="checkbox" id="max_accuracy_enabled" name="max_accuracy_enabled" value="1" <?php checked(!empty($settings['max_accuracy_enabled'])); ?>>
                                <label for="max_accuracy_enabled"><?php esc_html_e('Enabled', 'pi-chatbot'); ?></label>
                            </div>
                            <p class="pi-cba-desc"><?php esc_html_e('Off = every question takes the fast path, exactly as before this feature existed.', 'pi-chatbot'); ?></p>
                        </div>
                    </div>
                    <div class="pi-cba-field">
                        <span class="pi-cba-label"><?php esc_html_e('LLM reranking', 'pi-chatbot'); ?></span>
                        <div>
                            <div class="pi-cba-checkbox-row">
                                <input type="checkbox" id="rerank_enabled" name="rerank_enabled" value="1" <?php checked(!empty($settings['rerank_enabled'])); ?>>
                                <label for="rerank_enabled"><?php esc_html_e('Enabled', 'pi-chatbot'); ?></label>
                            </div>
                            <p class="pi-cba-desc"><?php esc_html_e('Re-orders retrieved products by reading them against the question — tells a 2-burner range apart from a 4-burner one when the titles are otherwise identical.', 'pi-chatbot'); ?></p>
                        </div>
                    </div>
                    <div class="pi-cba-field">
                        <label class="pi-cba-label" for="rerank_model"><?php esc_html_e('Reranking / fact-check model', 'pi-chatbot'); ?></label>
                        <div>
                            <input type="text" id="rerank_model" name="rerank_model" value="<?php echo esc_attr($settings['rerank_model']); ?>">
                            <p class="pi-cba-desc"><?php esc_html_e('Same kind of fast, non-reasoning model as the router.', 'pi-chatbot'); ?></p>
                        </div>
                    </div>
                    <div class="pi-cba-field">
                        <span class="pi-cba-label"><?php esc_html_e('Fact-check answers', 'pi-chatbot'); ?></span>
                        <div>
                            <div class="pi-cba-checkbox-row">
                                <input type="checkbox" id="verify_enabled" name="verify_enabled" value="1" <?php checked(!empty($settings['verify_enabled'])); ?>>
                                <label for="verify_enabled"><?php esc_html_e('Enabled', 'pi-chatbot'); ?></label>
                            </div>
                            <p class="pi-cba-desc"><?php esc_html_e('A second model checks the answer against the retrieved data and rewrites it once if it finds an invented price, capacity or model number.', 'pi-chatbot'); ?></p>
                        </div>
                    </div>
                    <div class="pi-cba-field">
                        <label class="pi-cba-label" for="multi_query_max"><?php esc_html_e('Extra query rewrites', 'pi-chatbot'); ?></label>
                        <div>
                            <input type="number" min="0" max="5" id="multi_query_max" name="multi_query_max" value="<?php echo esc_attr($settings['multi_query_max']); ?>">
                            <p class="pi-cba-desc"><?php esc_html_e('How many alternative phrasings of a hard question to search alongside the original. 0 disables multi-query.', 'pi-chatbot'); ?></p>
                        </div>
                    </div>
                </div>
                <?php submit_button(__('Save accuracy settings', 'pi-chatbot'), 'primary', 'submit', false, ['style' => 'margin-top:18px;']); ?>
            </form>
            </div>
        </div>
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
        $feedback = Pi_Chatbot_Feedback::stats(30);
        $latency = Pi_Chatbot_Log::stats_latency(7);
        ?>
        <div class="pi-card">
            <div class="pi-card__header">
                <span class="pi-card__eyebrow">05 — Data gaps</span>
                <h2 class="pi-card__title"><?php esc_html_e('Learning from conversations', 'pi-chatbot'); ?></h2>
                <p class="pi-card__desc">
                    <?php esc_html_e('Logs every real question (excluding cache hits) and whether a matching product/context was found. The table shows the most-repeated questions in the last 30 days that found NOTHING — a measured signal of missing data or missing keywords.', 'pi-chatbot'); ?>
                </p>
            </div>
            <div class="pi-card__body pi-card__body--tight">
            <div class="pi-grid--stats">
                <div>
                    <div class="pi-stat__value"><?php echo esc_html($stats['total']); ?></div>
                    <div class="pi-stat__label"><?php esc_html_e('Logged questions', 'pi-chatbot'); ?></div>
                </div>
                <div>
                    <div class="pi-stat__value"><?php echo esc_html($stats['total_unanswered']); ?></div>
                    <div class="pi-stat__label"><?php echo esc_html(sprintf(
                        /* translators: %d: distinct unanswered question count */
                        __('No context found (%d distinct)', 'pi-chatbot'), $stats['distinct_unanswered']
                    )); ?></div>
                </div>
                <div>
                    <div class="pi-stat__value"><?php echo esc_html($stats['last_24h']); ?></div>
                    <div class="pi-stat__label"><?php esc_html_e('Last 24h', 'pi-chatbot'); ?></div>
                </div>
                <div>
                    <div class="pi-stat__value">
                        <?php echo $feedback['total'] > 0
                            ? esc_html(round(100 * $feedback['up'] / $feedback['total'])) . '%'
                            : esc_html__('—', 'pi-chatbot'); ?>
                    </div>
                    <div class="pi-stat__label">
                        <?php echo esc_html(sprintf(
                            /* translators: 1: 👍 count, 2: 👎 count, over the last 30 days */
                            __('Helpful (👍 %1$d / 👎 %2$d, 30d)', 'pi-chatbot'), $feedback['up'], $feedback['down']
                        )); ?>
                    </div>
                </div>
            </div>
            <div class="pi-grid--stats" style="margin-top:10px;">
                <div>
                    <div class="pi-stat__value">
                        <?php echo $latency['p50'] !== null ? esc_html(round($latency['p50'] / 1000, 1)) . 's' : esc_html__('—', 'pi-chatbot'); ?>
                    </div>
                    <div class="pi-stat__label"><?php esc_html_e('Latency p50 (7d)', 'pi-chatbot'); ?></div>
                </div>
                <div>
                    <div class="pi-stat__value">
                        <?php echo $latency['p90'] !== null ? esc_html(round($latency['p90'] / 1000, 1)) . 's' : esc_html__('—', 'pi-chatbot'); ?>
                    </div>
                    <div class="pi-stat__label"><?php esc_html_e('Latency p90 (7d)', 'pi-chatbot'); ?></div>
                </div>
                <div>
                    <div class="pi-stat__value">
                        <?php echo $latency['max'] !== null ? esc_html(round($latency['max'] / 1000, 1)) . 's' : esc_html__('—', 'pi-chatbot'); ?>
                    </div>
                    <div class="pi-stat__label"><?php echo esc_html(sprintf(
                        /* translators: %d: number of completed requests the latency stats are based on */
                        __('Latency max (7d, n=%d)', 'pi-chatbot'), $latency['n']
                    )); ?></div>
                </div>
            </div>
            <?php if (empty($top)) : ?>
                <p><em><?php esc_html_e('No questions missing context in the last 30 days.', 'pi-chatbot'); ?></em></p>
            <?php else : ?>
                <div class="pi-table-wrap">
                    <table class="pi-table">
                        <thead>
                            <tr>
                                <th><?php esc_html_e('Question', 'pi-chatbot'); ?></th>
                                <th style="width:90px;"><?php esc_html_e('Count', 'pi-chatbot'); ?></th>
                                <th style="width:170px;"><?php esc_html_e('Last seen', 'pi-chatbot'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($top as $row) : ?>
                            <tr>
                                <td><?php echo esc_html($row->question_norm); ?></td>
                                <td class="num"><?php echo esc_html($row->cnt); ?></td>
                                <td><?php echo esc_html($row->last_seen); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Leads left in chat via the "leave your phone number" form (GLM 5.2
     * report recommendation C — lead capture). Contains PII (name/phone), so
     * unlike top_unanswered() this is NOT rendered as an inline table on a
     * page other roles might see over a shoulder — it's still just the
     * existing wp-admin page though, gated by the same manage_options
     * capability as the rest of this screen.
     */
    private function render_leads_section() {
        $leads = Pi_Chatbot_Lead::recent(20);
        $total = Pi_Chatbot_Lead::total_count();
        ?>
        <div class="pi-card">
            <div class="pi-card__header">
                <span class="pi-card__eyebrow">06 — Leads</span>
                <h2 class="pi-card__title"><?php esc_html_e('Callback requests', 'pi-chatbot'); ?></h2>
                <p class="pi-card__desc">
                    <?php esc_html_e('Name/phone a visitor left in the chat widget for a callback. No Zalo handoff yet — call or message these numbers directly.', 'pi-chatbot'); ?>
                </p>
            </div>
            <div class="pi-card__body pi-card__body--tight">
            <div class="pi-grid--stats">
                <div>
                    <div class="pi-stat__value"><?php echo esc_html($total); ?></div>
                    <div class="pi-stat__label"><?php esc_html_e('Total leads', 'pi-chatbot'); ?></div>
                </div>
            </div>
            <?php if (empty($leads)) : ?>
                <p><em><?php esc_html_e('No callback requests yet.', 'pi-chatbot'); ?></em></p>
            <?php else : ?>
                <div class="pi-table-wrap">
                    <table class="pi-table">
                        <thead>
                            <tr>
                                <th><?php esc_html_e('Name', 'pi-chatbot'); ?></th>
                                <th><?php esc_html_e('Phone', 'pi-chatbot'); ?></th>
                                <th><?php esc_html_e('Last question', 'pi-chatbot'); ?></th>
                                <th style="width:170px;"><?php esc_html_e('Received', 'pi-chatbot'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($leads as $row) : ?>
                            <tr>
                                <td><?php echo esc_html($row->name !== '' ? $row->name : '—'); ?></td>
                                <td><?php echo esc_html($row->phone); ?></td>
                                <td><?php echo esc_html($row->question !== '' ? $row->question : '—'); ?></td>
                                <td><?php echo esc_html($row->created_at); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
            </div>
        </div>
        <?php
    }

    /**
     * FAQ approval queue.
     *
     * Pi_Chatbot_FAQ_Engine::assetize() has been recording every answered
     * question since it shipped, but nothing ever read those rows back:
     * find_faq() filters on is_approved = 1 and there was no way to approve
     * anything — no UI, no cron, no caller. 59 rows in, 0 out. This screen is
     * the missing half; find_faq() is now wired into Pi_Chatbot_Rest::ask()
     * (2026-08-08), so approving a row here means the next customer asking
     * that exact question gets served instantly with ZERO LLM calls.
     *
     * That is precisely why approval is manual and one-at-a-time. An approved
     * answer bypasses the model AND the faithfulness check — whatever is in
     * that row is what the customer reads, verbatim, until someone deletes it.
     */
    private function render_faq_section() {
        $pending = Pi_Chatbot_FAQ_Engine::get_pending(30);
        $approved_count = Pi_Chatbot_FAQ_Engine::count_approved();
        $pending_count = Pi_Chatbot_FAQ_Engine::count_pending();
        ?>
        <div class="pi-card">
            <div class="pi-card__header">
                <span class="pi-card__eyebrow">07 — FAQ</span>
                <h2 class="pi-card__title"><?php esc_html_e('Approve reusable answers', 'pi-chatbot'); ?></h2>
                <p class="pi-card__desc">
                    <?php esc_html_e('Questions the bot has already answered. Approving one makes it served instantly for that exact question — no AI call, and no fact check. Read it before you approve it.', 'pi-chatbot'); ?>
                </p>
            </div>
            <div class="pi-card__body pi-card__body--tight">
            <div class="pi-grid--stats">
                <div>
                    <div class="pi-stat__value"><?php echo esc_html($approved_count); ?></div>
                    <div class="pi-stat__label"><?php esc_html_e('Approved (served instantly)', 'pi-chatbot'); ?></div>
                </div>
                <div>
                    <div class="pi-stat__value"><?php echo esc_html($pending_count); ?></div>
                    <div class="pi-stat__label"><?php esc_html_e('Awaiting review', 'pi-chatbot'); ?></div>
                </div>
            </div>

            <p style="margin:10px 0;padding:10px;border-left:3px solid #d63638;background:#fcf0f1;">
                <strong><?php esc_html_e('These answers were written by the AI, not by a person.', 'pi-chatbot'); ?></strong><br>
                <?php esc_html_e('The repeat count only shows how often the bot produced the same answer — it is not confirmation that the answer is correct. Check the facts yourself before approving.', 'pi-chatbot'); ?>
            </p>

            <?php if (empty($pending)) : ?>
                <p><em><?php esc_html_e('Nothing waiting for review.', 'pi-chatbot'); ?></em></p>
            <?php else : ?>
                <div class="pi-table-wrap">
                    <table class="pi-table">
                        <thead>
                            <tr>
                                <th><?php esc_html_e('Question', 'pi-chatbot'); ?></th>
                                <th><?php esc_html_e('Answer the customer would get', 'pi-chatbot'); ?></th>
                                <th style="width:70px;"><?php esc_html_e('Repeats', 'pi-chatbot'); ?></th>
                                <th style="width:150px;"><?php esc_html_e('Action', 'pi-chatbot'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($pending as $row) : ?>
                            <tr>
                                <td><?php echo esc_html($row->question); ?></td>
                                <td style="max-width:420px;"><?php echo esc_html(mb_substr($row->answer, 0, 400) . (mb_strlen($row->answer) > 400 ? '…' : '')); ?></td>
                                <td class="num"><?php echo esc_html($row->verified_count); ?></td>
                                <td>
                                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline;">
                                        <?php wp_nonce_field('pi_chatbot_faq_action'); ?>
                                        <input type="hidden" name="action" value="pi_chatbot_faq_action">
                                        <input type="hidden" name="faq_id" value="<?php echo esc_attr($row->id); ?>">
                                        <button type="submit" name="faq_op" value="approve" class="button button-primary"><?php esc_html_e('Approve', 'pi-chatbot'); ?></button>
                                        <button type="submit" name="faq_op" value="delete" class="button"><?php esc_html_e('Delete', 'pi-chatbot'); ?></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Approve or delete one FAQ candidate. Same guard pattern as
     * handle_reindex()/handle_save_settings(): capability check first, then
     * nonce, then act — an approved row is served verbatim to customers, so
     * this is not a place to be lax.
     */
    public function handle_faq_action() {
        if (!current_user_can('manage_options')) wp_die('Permission denied.');
        check_admin_referer('pi_chatbot_faq_action');

        $id = isset($_POST['faq_id']) ? (int) $_POST['faq_id'] : 0;
        $op = isset($_POST['faq_op']) ? sanitize_text_field(wp_unslash($_POST['faq_op'])) : '';

        $done = false;
        if ($id > 0 && $op === 'approve') {
            $done = Pi_Chatbot_FAQ_Engine::approve($id);
        } elseif ($id > 0 && $op === 'delete') {
            $done = Pi_Chatbot_FAQ_Engine::delete_faq($id);
        }

        wp_safe_redirect(add_query_arg(
            ['page' => 'pi-chatbot', 'faq_done' => $done ? $op : 'error'],
            admin_url('admin.php')
        ));
        exit;
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
        $settings['llm_daily_budget'] = max(10, min(100000, (int) ($_POST['llm_daily_budget'] ?? $settings['llm_daily_budget'])));

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
