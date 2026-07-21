<?php
/**
 * REST — pi/v1/chatbot/ask, public (IP-rate-limited via a transient bucket).
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
            return new WP_Error('pi_chatbot_empty_message', 'Message is required.', ['status' => 400]);
        }
        if (mb_strlen($message) > self::MAX_MESSAGE_LENGTH) {
            return new WP_Error('pi_chatbot_too_long', sprintf('Message is too long (max %d characters).', self::MAX_MESSAGE_LENGTH), ['status' => 400]);
        }

        // Cache the answer keyed on hash(normalized question + index version) —
        // a repeated question (greetings, popular brands, the 4 quick-reply
        // buttons) returns instantly with no retrieval/LLM call. Whitespace is
        // normalized first (previously "tủ  đông" and "tủ đông" were 2 separate
        // cache entries — 2 LLM calls at ~8.7s each for the same question).
        // pi_chatbot_index_version is bumped on BOTH full reindex and single
        // post save/delete (see class-indexer.php) — previously only reindex
        // bumped last_reindex, so editing one product's price/specs still
        // served the old answer + sources for up to an hour.
        $normalized = trim(preg_replace('/\s+/u', ' ', mb_strtolower($message)));
        $cache_key = 'pi_chatbot_ans_' . md5($normalized . '|' . (int) get_option('pi_chatbot_index_version', 0));
        $cached = get_transient($cache_key);
        if ($cached !== false) {
            return rest_ensure_response($cached);
        }

        // Rate limit AFTER the cache lookup: a cached answer costs nothing
        // (no retrieval, no LLM) so it shouldn't count against quota —
        // previously checked first, so free cache hits still ate into the
        // per-minute limit.
        $rate_check = $this->check_rate_limit();
        if (is_wp_error($rate_check)) return $rate_check;

        $settings = pi_chatbot_settings();

        // Classification is fully LLM-driven: the router runs on EVERY message
        // (rule-based Vietnamese keyword matching — is_price_intent()/
        // is_contact_intent()/... — was removed from class-retriever.php by
        // product decision, trading instant/free classification for a single
        // LLM-first design). intent=price/contact/service/project dispatch to
        // the theme's real static data; intent=product runs a keyword search;
        // a null/failed classification (router disabled, breaker open, error)
        // falls back to a plain product search on the raw message.
        $context = [];
        $router_used = false;
        $intent = null;
        $router_keywords = [];
        $router_brand = null;
        $search_query = '';

        $enrichment = Pi_Chatbot_Router::enrich($message);
        if ($enrichment) {
            $router_used = true;
            $intent = $enrichment['intent'];
            $router_keywords = array_merge($enrichment['keywords_vi'], $enrichment['keywords_en']);
            $router_brand = $enrichment['brand'] ?: null;

            switch ($intent) {
                case 'contact':
                    $context = Pi_Chatbot_Retriever::contact_context();
                    break;
                case 'price':
                    $context = Pi_Chatbot_Retriever::price_context();
                    break;
                case 'service':
                    $context = Pi_Chatbot_Retriever::service_context();
                    break;
                case 'project':
                    $context = Pi_Chatbot_Retriever::search_projects($message, (int) $settings['top_k']) ?: [];
                    break;
                default: // 'product'
                    $search_query = trim(implode(' ', array_merge(
                        $enrichment['keywords_en'],
                        $enrichment['keywords_vi'],
                        $enrichment['brand'] ? [$enrichment['brand']] : []
                    )));
                    if ($search_query === '') $search_query = $message;
                    $context = Pi_Chatbot_Retriever::search($search_query, (int) $settings['top_k']) ?: [];
                    break;
            }
        } else {
            // Router unavailable (disabled/circuit-open/error) — degrade to a
            // plain product search on the raw message rather than leaving the
            // customer with no answer at all.
            $search_query = $message;
            $context = Pi_Chatbot_Retriever::search($message, (int) $settings['top_k']) ?: [];
        }

        // Log ONLY on the fresh path (not cache hits — a cached question is a
        // repeat of one already logged; logging it again would inflate counts
        // without adding new signal). Logged before calling Generator so we
        // still capture "was context found" even if the LLM call fails —
        // that's an independent signal from whether generation succeeded.
        Pi_Chatbot_Log::record($normalized, !empty($context), count($context), $router_used);

        $result = Pi_Chatbot_Generator::answer($message, $context);

        if (is_wp_error($result)) {
            return new WP_Error('pi_chatbot_generation_failed', 'The assistant is busy right now — please try again in a few minutes or call our hotline.', ['status' => 502]);
        }

        // "trace" — REAL data from what actually happened in this request (not
        // the answer-generating model's chain-of-thought — LLM7 doesn't expose
        // that field separately for gpt-oss:20b). The frontend uses this to
        // render the "Thinking"/"Searching" progress card. router_keywords/
        // router_brand are only populated when the router actually classified
        // the message as intent=product — an empty array means no reasoning
        // step happened, so the UI never fabricates content for that panel.
        $result['trace'] = [
            'intent'          => $intent,
            'search_query'    => $search_query,
            'found_count'     => count($context),
            'router_used'     => $router_used,
            'router_keywords' => $router_keywords,
            'router_brand'    => $router_brand,
        ];

        set_transient($cache_key, $result, HOUR_IN_SECONDS);
        return rest_ensure_response($result);
    }

    /**
     * Per-IP limit via the 'rate_limit' option (default 10/minute) — simple
     * spam guard.
     *
     * Keyed by MINUTE BUCKET (`floor(time()/60)`) instead of one fixed key +
     * manual reset. The old version reset the window with get->delete->update
     * — a non-atomic 3-step read-modify-write sitting right next to a line
     * that WAS made atomic on purpose — so 2 requests arriving right as the
     * window expired could both see "the old window is over," one delete()
     * landing right after the other's INSERT, wiping out the increment that
     * was just written. A new key every minute means there's no reset step
     * left to race — the first request in a new minute naturally starts at 0
     * because that key never existed before.
     *
     * ONLY INCREMENT WHEN STILL UNDER THE LIMIT: previously incremented first
     * then compared, so a user hitting 429 who retried would push their OWN
     * count even higher, pushing the reopen time further out — the exact
     * opposite of the "wait a minute" the message promised.
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
            return new WP_Error('pi_chatbot_rate_limited', 'You are sending messages too quickly — please wait a minute.', ['status' => 429]);
        }

        $wpdb->query($wpdb->prepare(
            "INSERT INTO {$wpdb->options} (option_name, option_value, autoload)
             VALUES (%s, '1', 'no')
             ON DUPLICATE KEY UPDATE option_value = option_value + 1",
            $value_option
        ));
        // add_option() is a no-op if the row already exists — no reset step
        // needed, WP cleans it up itself once the transient timeout passes
        // (uninstall.php also sweeps by this pattern).
        add_option($timeout_option, time() + (2 * MINUTE_IN_SECONDS), '', false);

        return true;
    }

    /**
     * REMOTE_ADDR is the rate-limit key — NOT X-Forwarded-For/X-Client-IP,
     * since those are entirely client-controlled headers; sending a random
     * value on every request would neutralize rate limiting (a real bug found
     * during audit). XFF is only trusted when the site genuinely sits behind
     * a trusted reverse proxy (define the PI_CHATBOT_TRUST_PROXY constant in
     * wp-config.php to enable — off by default; LocalWP/simple hosting
     * doesn't need it).
     */
    private function get_client_ip() {
        if (defined('PI_CHATBOT_TRUST_PROXY') && PI_CHATBOT_TRUST_PROXY && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
            return trim($ip);
        }
        return !empty($_SERVER['REMOTE_ADDR']) ? trim($_SERVER['REMOTE_ADDR']) : '0.0.0.0';
    }
}
