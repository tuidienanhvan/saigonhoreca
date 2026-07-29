<?php
/**
 * Plugin Name: Pi Chatbot
 * Description: AI assistant for product consultation for Saigon Horeca — internal full-text retrieval (no embedding/vector DB, data scale doesn't warrant it yet) + generation calling the LLM7 API directly (no intermediate proxy). Attaches to the theme's existing #sh-toggle button.
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
require_once PI_CHATBOT_DIR . 'includes/class-log.php';
require_once PI_CHATBOT_DIR . 'includes/class-feedback.php';
require_once PI_CHATBOT_DIR . 'includes/class-lead.php';
require_once PI_CHATBOT_DIR . 'includes/class-entities.php';
require_once PI_CHATBOT_DIR . 'includes/class-retriever.php';
require_once PI_CHATBOT_DIR . 'includes/class-budget.php';
require_once PI_CHATBOT_DIR . 'includes/class-llm-client.php';
require_once PI_CHATBOT_DIR . 'includes/class-generator.php';
// class-router.php loads AFTER class-entities.php (uses known_brands()) and class-indexer.php
// (uses table_name() for few-shot) — load order is fixed, same as the other files in this plugin.
require_once PI_CHATBOT_DIR . 'includes/class-router.php';
// class-reranker.php uses Pi_Chatbot_LLM_Client — loaded above.
require_once PI_CHATBOT_DIR . 'includes/class-reranker.php';
require_once PI_CHATBOT_DIR . 'includes/class-rest.php';
require_once PI_CHATBOT_DIR . 'includes/class-admin.php';

register_activation_hook(__FILE__, ['Pi_Chatbot_Indexer', 'on_activate']);
register_activation_hook(__FILE__, ['Pi_Chatbot_Log', 'on_activate']);
register_activation_hook(__FILE__, ['Pi_Chatbot_Feedback', 'on_activate']);
register_activation_hook(__FILE__, ['Pi_Chatbot_Lead', 'on_activate']);

add_action('plugins_loaded', 'pi_chatbot_bootstrap');
function pi_chatbot_bootstrap() {
    load_plugin_textdomain('pi-chatbot', false, dirname(plugin_basename(__FILE__)) . '/languages');
    Pi_Chatbot_Indexer::maybe_upgrade();
    Pi_Chatbot_Log::maybe_upgrade();
    Pi_Chatbot_Feedback::maybe_upgrade();
    Pi_Chatbot_Lead::maybe_upgrade();
    Pi_Chatbot_Indexer::instance();
    Pi_Chatbot_Rest::instance();
    Pi_Chatbot_Admin::instance();
}

// Listen for the theme's 'sgh_after_flush_cache' action (the /flush-cache endpoint) — one
// click/call clears both the theme's static cache and the chatbot's answer cache. If the
// active theme isn't saigonhoreca-theme (hook doesn't exist), this add_action() is
// harmless — it simply never gets called.
add_action('sgh_after_flush_cache', ['Pi_Chatbot_Indexer', 'flush_answer_cache']);

/**
 * Effective config = defaults merged with the saved option (changed via the admin page).
 * llm7_keys: LLM7 keys (round-robin if multiple are added via the admin page) — calls
 * https://api.llm7.io DIRECTLY, NOT through Luna-Proxy/Qwen.
 *
 * Don't hardcode a default key here — a secret living in source can't be rotated
 * without a code change. The key lives entirely in the DB option, entered/changed via
 * the Pi Chatbot admin page (the one-time migration from the old version is complete,
 * the migration function has been removed from source — see the change history if you
 * need the details).
 *
 * Cast llm7_keys/router_keys to arrays RIGHT HERE — a single place that simultaneously
 * closes off 3 PHP 8 fatal-error paths if this option was ever saved with the wrong type
 * (scalar instead of array): implode() when rendering the admin page, preg_split() when
 * saving settings, and foreach ($keys as $key) in Pi_Chatbot_LLM_Client::call() on the
 * very request serving a customer — a fatal here is a fatal on the chat response path,
 * far worse than a fatal on the admin page.
 */
function pi_chatbot_settings() {
    $defaults = [
        'llm7_keys'  => [],
        'model'      => 'gpt-oss:20b',
        'top_k'      => 6,
        'rate_limit' => 10, // questions / minute / IP
        // Global (site-wide, not per-IP) cap on real LLM7 HTTP attempts per calendar
        // day — see class-budget.php. 300/day is sized for LLM7's free tier: even the
        // max-accuracy path's worst case (router + generate + retry + rerank + verify
        // + retry-verify = 6 calls) leaves room for 50 such requests/day, and the
        // common fast-path question (router + generate = 2 calls) leaves room for 150.
        // Raise this if paid/higher-quota keys are configured.
        'llm_daily_budget' => 300,
        // Router — ON BY DEFAULT. It is now the ONLY intent classifier (the old
        // rule-based Vietnamese keyword matching in class-retriever.php was
        // removed by product decision, trading instant/free classification
        // for a single LLM-first design). Disabling it does not just remove a
        // fallback — it removes price/contact/service/project handling
        // entirely, degrading every message to a plain product search on the
        // raw text. See docs/plans/pi-chatbot-adaptive-router.md.
        'router_enabled' => true,
        'router_model'   => 'codestral-latest', // turbo model, no reasoning — measured for real
        // 1.8s average, very different from the generator model (gpt-oss:20b takes 2.9s +
        // returns empty when used as router, since it's a reasoning model that eats all of
        // max_tokens into a hidden field).
        'router_keys'    => [], // empty = share llm7_keys — split out if you want a different key

        // ── Max-accuracy pipeline ───────────────────────────────────────────
        // The router labels each question easy/hard; only 'hard' ones take the
        // expensive path (multi-query retrieval -> LLM rerank -> faithfulness
        // check). Latency there is deliberately generous (~30s worst case)
        // because the questions that reach it are exactly the ones the fast
        // lexical pipeline measurably gets wrong. Turning this off restores the
        // previous behaviour exactly — every question takes the fast path.
        //
        // There is deliberately NO embedding/vector provider here. A semantic
        // leg was built and measured, then removed: it needed a SECOND external
        // API (on top of LLM7) with its own key, quota and failure mode, while
        // the capability it was meant to add — understanding a question phrased
        // differently from the catalog — is already supplied by the router's
        // query rewrites and the reranker, both on the LLM7 dependency that
        // already exists. Measurement also showed the real retrieval gap was
        // not semantic at all: it was customers typing Vietnamese without
        // diacritics, which costs hit@3 0.838 -> 0.324 and is fixed for free by
        // Retriever::search_unaccented().
        'max_accuracy_enabled' => true,

        // Reranking + verification, both on the router model (fast, non-reasoning).
        'rerank_enabled' => true,
        'rerank_model'   => 'codestral-latest',
        'verify_enabled' => true,

        // Extra router-generated query rewrites fused into the same result pool.
        'multi_query_max' => 2,
        // Reciprocal Rank Fusion constant. 60 is the value from the original RRF
        // paper (Cormack et al. 2009) and is deliberately NOT tuned here — the
        // whole point of choosing RRF over weighted score blending is that it
        // needs no tuning against incomparable score scales.
        'rrf_k' => 60,
    ];
    $saved = get_option(PI_CHATBOT_OPTION, []);
    if (!is_array($saved)) $saved = [];
    $settings = array_merge($defaults, $saved);
    if (!is_array($settings['llm7_keys'])) {
        $settings['llm7_keys'] = array_filter([(string) $settings['llm7_keys']]);
    }
    if (!is_array($settings['router_keys'])) {
        $settings['router_keys'] = array_filter([(string) $settings['router_keys']]);
    }
    // Same defensive casting rationale as the key arrays above: these are read on
    // the customer request path, where a wrong type saved into the option would
    // be a fatal on the chat response, not just on the admin page.
    $settings['multi_query_max'] = max(0, min(5, (int) $settings['multi_query_max']));
    $settings['rrf_k']           = max(1, min(500, (int) $settings['rrf_k']));
    return $settings;
}

add_action('save_post', ['Pi_Chatbot_Indexer', 'on_save_post'], 20, 3);
add_action('before_delete_post', ['Pi_Chatbot_Indexer', 'on_delete_post']);

add_action('wp_enqueue_scripts', 'pi_chatbot_enqueue_frontend');
function pi_chatbot_enqueue_frontend() {
    if (!function_exists('is_plugin_active')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    // The theme only renders the #sh-toggle button when this plugin is active — safe to double-check here too.
    if (!is_plugin_active('pi-chatbot/pi-chatbot.php')) return;

    // Bust cache based on file mtime instead of a static version — editing CSS/JS means
    // the browser loads the new version immediately, no need to bump a version by hand
    // or clear cache manually.
    $mtime = function ($rel_path) {
        $abs = PI_CHATBOT_DIR . $rel_path;
        return file_exists($abs) ? filemtime($abs) : PI_CHATBOT_VERSION;
    };

    // CSS split into 1 file per component (2026-07-26) instead of 1 monolithic
    // chatbot.css — enqueued as a dependency CHAIN (each handle depends on the one
    // before) purely so WordPress prints them in this exact order; there is no actual
    // cross-file selector dependency, tokens.css just needs to load first so its --cb-*
    // custom properties exist before anything else references them.
    $css_handles = [
        'pi-chatbot-tokens'        => 'assets/css/chatbot-tokens.css',
        'pi-chatbot-panel'         => 'assets/css/chatbot-panel.css',
        'pi-chatbot-messages'      => 'assets/css/chatbot-messages.css',
        'pi-chatbot-progress'      => 'assets/css/chatbot-progress.css',
        'pi-chatbot-quick-replies' => 'assets/css/chatbot-quick-replies.css',
        'pi-chatbot-footer'        => 'assets/css/chatbot-footer.css',
        'pi-chatbot-responsive'    => 'assets/css/chatbot-responsive.css',
    ];
    $prev_handle = null;
    foreach ($css_handles as $handle => $rel_path) {
        wp_enqueue_style($handle, PI_CHATBOT_URL . $rel_path, $prev_handle ? [$prev_handle] : [], $mtime($rel_path));
        $prev_handle = $handle;
    }

    // JS split into 2 files: chatbot-ui.js (pure DOM builders + trace->text copy, no
    // shared state — the "components") loads FIRST as a real dependency (chatbot.js
    // calls window.PiChatbotUI.* at parse time in a few places), then chatbot.js (state,
    // network, event wiring) which owns everything that has to persist across a
    // request: chat history, timers, the request sequence counter.
    wp_enqueue_script('pi-chatbot-ui', PI_CHATBOT_URL . 'assets/chatbot-ui.js', [], $mtime('assets/chatbot-ui.js'), true);
    wp_enqueue_script('pi-chatbot', PI_CHATBOT_URL . 'assets/chatbot.js', ['pi-chatbot-ui'], $mtime('assets/chatbot.js'), true);
    wp_localize_script('pi-chatbot', 'PiChatbotConfig', [
        'restUrl'     => esc_url_raw(rest_url('pi/v1/chatbot/ask')),
        'streamUrl'   => esc_url_raw(rest_url('pi/v1/chatbot/ask-stream')),
        'feedbackUrl' => esc_url_raw(rest_url('pi/v1/chatbot/feedback')),
        'leadUrl'     => esc_url_raw(rest_url('pi/v1/chatbot/lead')),
        'nonce'     => wp_create_nonce('wp_rest'),
        // Same source as Pi_Chatbot_Rest::MAX_MESSAGE_LENGTH — previously "500" was a
        // hardcoded number in both PHP and the maxlength attribute in chatbot.js,
        // easy to drift out of sync if one gets changed later and the other is forgotten.
        'maxLength' => Pi_Chatbot_Rest::MAX_MESSAGE_LENGTH,
    ]);
}
