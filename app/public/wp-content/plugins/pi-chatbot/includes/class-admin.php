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

            <?php $this->render_websearch_section($settings); ?>

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
     * External web search — the site-owner half of a two-gate switch. The other
     * half is the customer's own toggle in the chat widget; a search only runs
     * when BOTH are on, so turning this off disables the feature outright no
     * matter what any client sends.
     */
    private function render_websearch_section($settings) {
        ?>
        <div class="pi-card">
            <div class="pi-card__header">
                <span class="pi-card__eyebrow">05 — Web search</span>
                <h2 class="pi-card__title"><?php esc_html_e('External web search', 'pi-chatbot'); ?></h2>
                <p class="pi-card__desc">
                    <?php esc_html_e('Adds a toggle to the chat widget that lets a customer ask the bot to also look things up on the public internet. The catalog always stays the source of truth: web results are passed to the model in a separate, clearly-labelled block and are never presented as products we sell, never merged into the sources list, and never quoted as our prices. If a product only appears on the web, the bot still answers that we do not list it and gives the hotline.', 'pi-chatbot'); ?>
                </p>
                <p class="pi-card__desc">
                    <?php esc_html_e('A key-free public search API no longer exists — that was tested, not assumed. DuckDuckGo\'s HTML endpoint answers with an anti-bot challenge, and its official keyless JSON API returns nothing at all for product queries. Checked 2026-07-26:', 'pi-chatbot'); ?>
                </p>
                <ul class="pi-card__desc" style="list-style:disc;padding-left:20px;">
                    <li><strong>SearXNG</strong> — <?php esc_html_e('self-hosted, open source. Genuinely free, no quota, no card, because you run it yourself. Needs a container you maintain, and json must be added to search.formats in its settings.yml (off by default). Point it at YOUR OWN instance, not a public one.', 'pi-chatbot'); ?></li>
                    <li><strong>Serper.dev</strong> — <?php esc_html_e('2,500 free credits, no card needed. One-time, not monthly — after that it is paid.', 'pi-chatbot'); ?></li>
                    <li><strong>Brave</strong> — <?php esc_html_e('WARNING: Brave removed its free tier in February 2026. The card taken at signup, previously described as an anti-fraud check that would never be charged, is now billed once the small monthly credit runs out. Only pick this if you accept paying.', 'pi-chatbot'); ?></li>
                </ul>
                <p>
                    <?php if (Pi_Chatbot_WebSearch::is_configured()) : ?>
                        <span class="pi-badge pi-badge--success"><?php esc_html_e('Active', 'pi-chatbot'); ?></span>
                    <?php elseif (!empty($settings['web_search_enabled'])) : ?>
                        <span class="pi-badge pi-badge--warning"><?php
                            echo esc_html($settings['web_search_provider'] === 'searxng'
                                ? __('Enabled, but no SearXNG URL — feature is dormant', 'pi-chatbot')
                                : __('Enabled, but no API key — feature is dormant', 'pi-chatbot'));
                        ?></span>
                    <?php else : ?>
                        <span class="pi-badge pi-badge--muted"><?php esc_html_e('Off', 'pi-chatbot'); ?></span>
                    <?php endif; ?>
                </p>
            </div>
            <div class="pi-card__body pi-card__body--tight">
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <?php wp_nonce_field('pi_chatbot_save_settings'); ?>
                    <input type="hidden" name="action" value="pi_chatbot_save_settings">
                    <input type="hidden" name="websearch_form" value="1">
                    <div class="pi-cba-fields">
                        <div class="pi-cba-field">
                            <span class="pi-cba-label"><?php esc_html_e('Allow web search', 'pi-chatbot'); ?></span>
                            <div>
                                <div class="pi-cba-checkbox-row">
                                    <input type="checkbox" id="web_search_enabled" name="web_search_enabled" value="1" <?php checked(!empty($settings['web_search_enabled'])); ?>>
                                    <label for="web_search_enabled"><?php esc_html_e('Enabled', 'pi-chatbot'); ?></label>
                                </div>
                                <p class="pi-cba-desc"><?php esc_html_e('Off = the toggle does not appear in the widget and no external request is ever made, whatever a client sends. Adds roughly 1-3 seconds to a question when a customer switches it on.', 'pi-chatbot'); ?></p>
                            </div>
                        </div>
                        <div class="pi-cba-field">
                            <label class="pi-cba-label" for="web_search_provider"><?php esc_html_e('Provider', 'pi-chatbot'); ?></label>
                            <div>
                                <select id="web_search_provider" name="web_search_provider">
                                    <option value="searxng" <?php selected($settings['web_search_provider'], 'searxng'); ?>><?php esc_html_e('SearXNG — self-hosted, free, no quota', 'pi-chatbot'); ?></option>
                                    <option value="serper" <?php selected($settings['web_search_provider'], 'serper'); ?>><?php esc_html_e('Serper.dev — 2,500 free credits, then paid', 'pi-chatbot'); ?></option>
                                    <option value="brave" <?php selected($settings['web_search_provider'], 'brave'); ?>><?php esc_html_e('Brave — PAID since Feb 2026', 'pi-chatbot'); ?></option>
                                </select>
                                <p class="pi-cba-desc"><?php esc_html_e('Fill in the field matching your choice: SearXNG uses the instance URL below and ignores the key; Serper and Brave use the key and ignore the URL.', 'pi-chatbot'); ?></p>
                            </div>
                        </div>
                        <div class="pi-cba-field">
                            <label class="pi-cba-label" for="web_search_url"><?php esc_html_e('SearXNG instance URL', 'pi-chatbot'); ?></label>
                            <div>
                                <input type="text" id="web_search_url" name="web_search_url" placeholder="https://searx.example.com" value="<?php echo esc_attr($settings['web_search_url']); ?>">
                                <p class="pi-cba-desc"><?php esc_html_e('Your own instance only. Must have json enabled in search.formats (settings.yml) — without it SearXNG replies 403 and no results come back.', 'pi-chatbot'); ?></p>
                            </div>
                        </div>
                        <div class="pi-cba-field">
                            <label class="pi-cba-label" for="web_search_key"><?php esc_html_e('Provider API key', 'pi-chatbot'); ?></label>
                            <div>
                                <input type="password" id="web_search_key" name="web_search_key" autocomplete="off" value="<?php echo esc_attr($settings['web_search_key']); ?>">
                                <p class="pi-cba-desc"><?php esc_html_e('Serper or Brave only. Stored with autoload off, alongside the LLM7 keys.', 'pi-chatbot'); ?></p>
                            </div>
                        </div>
                    </div>
                    <?php submit_button(__('Save web search settings', 'pi-chatbot'), 'primary', 'submit', false, ['style' => 'margin-top:18px;']); ?>
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
        // Web search — a FOURTH form, gated by its own marker field for exactly
        // the same reason as the two above: an unchecked checkbox is simply
        // absent from $_POST, so without proof that THIS form was submitted,
        // saving any other section would silently switch the feature off.
        if (isset($_POST['websearch_form'])) {
            $settings['web_search_enabled'] = !empty($_POST['web_search_enabled']);

            // Whitelist, not sanitize_text_field: an unknown provider string
            // would silently fall through to Brave inside WebSearch::search()
            // while the admin page kept displaying whatever was typed.
            $provider = sanitize_text_field(wp_unslash($_POST['web_search_provider'] ?? ''));
            if (in_array($provider, ['searxng', 'serper', 'brave'], true)) {
                $settings['web_search_provider'] = $provider;
            }
            if (isset($_POST['web_search_key'])) {
                $settings['web_search_key'] = trim(sanitize_text_field(wp_unslash($_POST['web_search_key'])));
            }
            if (isset($_POST['web_search_url'])) {
                // esc_url_raw, not sanitize_text_field: this value is fetched
                // server-side, so it must be a real URL with an allowed
                // protocol, not merely free of tags.
                $settings['web_search_url'] = esc_url_raw(trim(wp_unslash($_POST['web_search_url'])));
            }
        }
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
