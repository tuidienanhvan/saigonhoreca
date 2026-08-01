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
                // Prior turns of THIS session, sent by the widget so a follow-up
                // ("giá bao nhiêu?", "cái đó dày bao nhiêu?") can be resolved
                // against the product already being discussed. Deliberately not
                // trusted at face value here — client-supplied — real caps and
                // sanitisation happen in sanitize_history(), this schema only
                // rejects the wrong shape outright.
                'history' => [
                    'required' => false,
                    'type'     => 'array',
                    'default'  => [],
                    'items'    => ['type' => 'object'],
                ],
            ],
        ]);

        register_rest_route('pi/v1', '/chatbot/ask-stream', [
            'methods'             => 'POST',
            'callback'            => [$this, 'ask_stream'],
            'permission_callback' => '__return_true',
            'args'                => [
                'message' => [
                    'required'          => true,
                    'type'              => 'string',
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'history' => [
                    'required' => false,
                    'type'     => 'array',
                    'default'  => [],
                    'items'    => ['type' => 'object'],
                ],
            ],
        ]);

        register_rest_route('pi/v1', '/chatbot/feedback', [
            'methods'             => 'POST',
            'callback'            => [$this, 'feedback'],
            'permission_callback' => '__return_true',
            'args'                => [
                'feedback_id' => [
                    'required' => true,
                    'type'     => 'string',
                ],
                'rating' => [
                    'required' => true,
                    'type'     => 'string',
                    'enum'     => ['up', 'down'],
                ],
            ],
        ]);

        register_rest_route('pi/v1', '/chatbot/lead', [
            'methods'             => 'POST',
            'callback'            => [$this, 'lead'],
            'permission_callback' => '__return_true',
            'args'                => [
                'name' => [
                    'required'          => false,
                    'type'              => 'string',
                    'default'           => '',
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'phone' => [
                    'required'          => true,
                    'type'              => 'string',
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                // The customer's own last question, sent by the widget for
                // context (so staff calling back know what was being asked) —
                // same trust level as 'message' on /ask: client-supplied text,
                // capped/sanitized by Pi_Chatbot_Lead::record(), never trusted
                // beyond being a string to store and display in wp-admin.
                'question' => [
                    'required'          => false,
                    'type'              => 'string',
                    'default'           => '',
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
        $history = self::sanitize_history($req->get_param('history'));

        // History folds into the cache key too — the SAME follow-up text
        // ("giá bao nhiêu?") answered after 2 different prior products is 2
        // different answers, and caching on the message alone would serve
        // whichever product asked about it first to every later customer.
        // An EMPTY history produces the exact same key as before this
        // feature existed (no suffix added), so every already-warm cache
        // entry for a plain, no-history question keeps working unchanged.
        $history_suffix = $history ? '|h' . md5(wp_json_encode($history)) : '';
        $cache_key = 'pi_chatbot_ans_' . md5($normalized . '|' . (int) get_option('pi_chatbot_index_version', 0) . $history_suffix);
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

        // Global daily LLM7-call budget (see class-budget.php) — checked ONCE per
        // request, before the router (the FIRST potential LLM call), so a day that
        // hit the cap spends zero further LLM calls rather than degrading only
        // partway through. $budget_exhausted deliberately reuses the EXISTING
        // "router unavailable" fallback below ($enrichment === null -> plain
        // lexical search on the raw message) instead of a new branch: from the
        // request's point of view, "no LLM budget left" and "router is down" need
        // the exact same degrade — a real answer from retrieval, no LLM call.
        $budget_exhausted = Pi_Chatbot_Budget::is_over_limit();

        // Classification is fully LLM-driven: the router runs on EVERY message
        // (rule-based Vietnamese keyword matching — is_price_intent()/
        // is_contact_intent()/... — was removed from class-retriever.php by
        // product decision, trading instant/free classification for a single
        // LLM-first design). intent=price/contact/service/project dispatch to
        // the theme's real static data; intent=product runs a keyword search;
        // a null/failed classification (router disabled, breaker open, error)
        // falls back to a plain product search on the raw message.
        // Per-stage wall-clock, measured not estimated — this is what the
        // frontend's progress card reports, and it is the only way to tell which
        // stage is responsible when the max-accuracy path feels slow.
        $steps = [];
        $started = microtime(true);
        // Independent of $started/$mark's rolling per-STEP clock above — this one
        // measures the WHOLE request, for Log::record_duration() (see class-log.php,
        // powers the admin latency stats). Captured here (post-cache-lookup,
        // post-rate-limit) rather than at the very top of ask(), so it reflects
        // real backend work, not queueing before PHP even started processing.
        $request_started = microtime(true);
        $mark = function ($name) use (&$steps, &$started) {
            $now = microtime(true);
            $steps[] = ['step' => $name, 'ms' => (int) round(($now - $started) * 1000)];
            $started = $now;
        };

        $verified = null;
        $routing = self::resolve_context($message, $history, $settings, $budget_exhausted, $mark);
        $context          = $routing['context'];
        $router_used      = $routing['router_used'];
        $intent           = $routing['intent'];
        $router_keywords  = $routing['router_keywords'];
        $router_brand     = $routing['router_brand'];
        $search_query     = $routing['search_query'];
        $mode             = $routing['mode'];
        $difficulty       = $routing['difficulty'];
        $ambiguity_reason = $routing['ambiguity_reason'];
        $query_variants   = $routing['query_variants'];
        $rerank_used      = $routing['rerank_used'];
        $rerank_confidence = $routing['rerank_confidence'];
        $low_confidence   = $routing['low_confidence'];

        // Log ONLY on the fresh path (not cache hits — a cached question is a
        // repeat of one already logged; logging it again would inflate counts
        // without adding new signal). Logged before calling Generator so we
        // still capture "was context found" even if the LLM call fails —
        // that's an independent signal from whether generation succeeded.
        $log_id = Pi_Chatbot_Log::record($normalized, !empty($context), count($context), $router_used);

        // Budget already exhausted -> no LLM call at all here either (the router
        // was already skipped above). A retrieval-only reply from real catalog
        // data is a genuinely useful degrade, not a failure page.
        if ($budget_exhausted) {
            $result = self::budget_exhausted_result($context);
            $mark('generate');
        } else {
            $result = Pi_Chatbot_Generator::answer($message, $context, $history);
            $mark('generate');

            if (is_wp_error($result)) {
                return new WP_Error('pi_chatbot_generation_failed', 'The assistant is busy right now — please try again in a few minutes or call our hotline.', ['status' => 502]);
            }
        }

        // Faithfulness check. Always on the max path (1 retry, then accept — a
        // second failure means the model cannot express this answer within the
        // reference, and looping further would spend the customer's remaining
        // patience without new information). ALSO on the fast path, but only
        // when the answer actually contains the kind of claim that causes real
        // commercial damage if wrong — a number (price/capacity/dimension), a
        // model-code-looking token, or a URL — and only while there is budget
        // headroom to spend on it: 80% of fast-path traffic (86/107 measured)
        // previously got zero verification no matter what it claimed.
        $needs_fast_verify = $mode === 'fast' && !$budget_exhausted && !empty($context)
            && !Pi_Chatbot_Budget::is_near_limit() && self::answer_needs_fact_check($result['answer']);
        if (($mode === 'max' && !empty($context)) || $needs_fast_verify) {
            $check = Pi_Chatbot_Generator::verify($message, $context, $result['answer']);
            if ($check !== null) {
                $verified = $check['faithful'];
                if (!$check['faithful']) {
                    $retry = Pi_Chatbot_Generator::answer($message, $context, $history, ['correction' => $check['issue']]);
                    if (!is_wp_error($retry)) {
                        $result = $retry;
                        $recheck = Pi_Chatbot_Generator::verify($message, $context, $result['answer']);
                        $verified = $recheck === null ? null : $recheck['faithful'];
                    }
                }
            }
            $mark('verify');
        }

        // "trace" — REAL data from what actually happened in this request (not
        // the answer-generating model's chain-of-thought — LLM7 doesn't expose
        // that field separately for gpt-oss:20b). The frontend uses this to
        // render the "Thinking"/"Searching" progress card. router_keywords/
        // router_brand are only populated when the router actually classified
        // the message as intent=product — an empty array means no reasoning
        // step happened, so the UI never fabricates content for that panel.
        //
        // Everything below 'router_brand' is ADDITIVE — an older cached frontend
        // reading only the original six fields keeps working unchanged.
        $result['trace'] = [
            'intent'          => $intent,
            'search_query'    => $search_query,
            'found_count'     => count($context),
            'router_used'     => $router_used,
            'router_keywords' => $router_keywords,
            'router_brand'    => $router_brand,
            // Adaptive pipeline.
            'mode'              => $mode,
            'difficulty'        => $difficulty,
            'ambiguity_reason'  => $ambiguity_reason,
            'query_variants'    => $query_variants,
            'rerank_used'       => $rerank_used,
            'rerank_confidence' => $rerank_confidence,
            'low_confidence'    => $low_confidence,
            'verified'          => $verified,
            'steps'             => $steps,
            'budget_exhausted'  => $budget_exhausted,
        ];

        // Opaque id the widget echoes back on a 👍/👎 vote (see feedback()) — hash
        // of the normalized question + the exact answer text served, so 2 different
        // answers to the same repeated question (e.g. after a reindex or budget
        // degrade) each get their own id, and the id itself reveals nothing about
        // the content to anyone who only has it (not reversible back to the text).
        $result['feedback_id'] = md5($normalized . '|' . $result['answer']);

        // Fills in duration_ms on the log row record() wrote earlier — only reached
        // for a request that actually produced an answer (any WP_Error return above
        // exits before this line), which is exactly the "real completed answer"
        // population latency stats should reflect (see stats_latency()'s docblock).
        if ($log_id) {
            Pi_Chatbot_Log::record_duration($log_id, (int) round((microtime(true) - $request_started) * 1000));
        }

        // A budget-degraded answer is cached much more briefly than a real one —
        // it's an honest "here's what we found" reply, not a wrong one, but the
        // budget can free up again well within an hour (a key's own quota window
        // resets, or the option is bumped), and a customer retrying a few minutes
        // later should get the real generated answer rather than a stale degrade.
        set_transient($cache_key, $result, $budget_exhausted ? 5 * MINUTE_IN_SECONDS : HOUR_IN_SECONDS);
        return rest_ensure_response($result);
    }

    /**
     * Test hooks for ask_stream() — SSE output (echo/flush/exit) has no
     * return value the test harness can inspect the normal way, and a real
     * `exit` mid-test would kill every suite queued to run after it in
     * run-all.php. Same DI pattern as Pi_Chatbot_LLM_Client::$stream_transport
     * (see its docblock): null uses the real behavior in production; tests
     * substitute a closure that records what would have been sent instead of
     * writing to the actual output buffer.
     *
     * $stream_emit_fn: function(string $type, array $data): void
     * $stream_exit_fn: function(): void — MUST stop execution (e.g. throw) in
     *   a test, the same way the real `exit` does in production, or the
     *   method's cache/response-shaping code after the call would run twice.
     */
    public static $stream_emit_fn = null;
    public static $stream_exit_fn = null;

    private static function stream_emit($type, $data = []) {
        if (self::$stream_emit_fn) {
            (self::$stream_emit_fn)($type, $data);
            return;
        }
        echo 'data: ' . wp_json_encode(array_merge(['type' => $type], $data)) . "\n\n";
        if (function_exists('ob_flush') && ob_get_level() > 0) {
            @ob_flush();
        }
        @flush();
    }

    private static function stream_exit() {
        if (self::$stream_exit_fn) {
            (self::$stream_exit_fn)();
            return;
        }
        exit;
    }

    /**
     * POST /pi/v1/chatbot/ask-stream — token-by-token SSE, ONLY for the
     * narrow set of questions where it is provably safe to show the customer
     * text before the answer is fully known.
     *
     * ELIGIBILITY is the one invariant this whole endpoint exists to protect:
     * Pi_Chatbot_Generator::verify() (the faithfulness check + correction
     * retry) can ONLY ever run when $context is non-empty — see ask()'s own
     * verify block, both the max-path and fast-path conditions there require
     * !empty($context). So streaming is safe to a customer's eyes EXACTLY
     * when resolve_context() returns an empty $context: intent='none' (small
     * talk/greeting — the common case) or intent='product' with zero search
     * results. mode==='max' is excluded too, for a practical (not safety)
     * reason: it always spends 1-2 extra LLM calls (hybrid retrieval's router
     * rewrites + reranking) BEFORE reaching generation, which defeats the
     * entire point of streaming — faster time-to-first-token — even on a
     * question that ends up with empty context.
     *
     * When NOT eligible, this responds with plain JSON `{eligible:false}` —
     * no SSE headers have been sent yet at that point, so the client can
     * transparently fall back to the existing buffered POST /ask and the
     * customer never sees a difference. A cache hit also short-circuits to
     * `{eligible:false}`: /ask already serves a cached answer instantly with
     * zero LLM calls, so there is nothing for streaming to speed up there.
     *
     * looks_degenerate()/the markdown strip still run on the fully-assembled
     * text (see Generator::answer_streaming()'s docblock) but, unlike ask(),
     * cannot reject/retry what the customer already saw live — a deliberate,
     * narrower guarantee than the buffered endpoint's, acceptable because the
     * eligible-to-stream path is exactly the shortest, least claim-heavy
     * answers (no retrieved context to make a false claim from in the first
     * place).
     */
    public function ask_stream($req) {
        $message = trim((string) $req->get_param('message'));
        if ($message === '') {
            return new WP_Error('pi_chatbot_empty_message', 'Message is required.', ['status' => 400]);
        }
        if (mb_strlen($message) > self::MAX_MESSAGE_LENGTH) {
            return new WP_Error('pi_chatbot_too_long', sprintf('Message is too long (max %d characters).', self::MAX_MESSAGE_LENGTH), ['status' => 400]);
        }

        $normalized = trim(preg_replace('/\s+/u', ' ', mb_strtolower($message)));
        $history = self::sanitize_history($req->get_param('history'));
        $history_suffix = $history ? '|h' . md5(wp_json_encode($history)) : '';
        $cache_key = 'pi_chatbot_ans_' . md5($normalized . '|' . (int) get_option('pi_chatbot_index_version', 0) . $history_suffix);

        if (get_transient($cache_key) !== false) {
            return rest_ensure_response(['eligible' => false]);
        }

        $rate_check = $this->check_rate_limit();
        if (is_wp_error($rate_check)) return $rate_check;

        $settings = pi_chatbot_settings();
        $budget_exhausted = Pi_Chatbot_Budget::is_over_limit();

        $steps = [];
        $started = microtime(true);
        $request_started = microtime(true);
        $mark = function ($name) use (&$steps, &$started) {
            $now = microtime(true);
            $steps[] = ['step' => $name, 'ms' => (int) round(($now - $started) * 1000)];
            $started = $now;
        };

        $routing = self::resolve_context($message, $history, $settings, $budget_exhausted, $mark);

        $eligible = !$budget_exhausted && $routing['mode'] !== 'max' && empty($routing['context']);
        if (!$eligible) {
            return rest_ensure_response(['eligible' => false]);
        }

        $log_id = Pi_Chatbot_Log::record($normalized, false, 0, $routing['router_used']);

        // Same shape ask() returns as 'trace' — built and emitted BEFORE
        // generation starts (everything in it is already known once
        // resolve_context() returns) so the widget can render its usual
        // "Analyzing"/"Searching" progress tiers from REAL data while tokens
        // are still arriving, instead of losing that UI entirely on the
        // streaming path (a real regression caught live: the first version
        // of this endpoint sent no trace at all, so the progress card
        // silently disappeared for every streamed answer).
        $trace = [
            'intent'            => $routing['intent'],
            'search_query'      => $routing['search_query'],
            'found_count'       => 0,
            'router_used'       => $routing['router_used'],
            'router_keywords'   => $routing['router_keywords'],
            'router_brand'      => $routing['router_brand'],
            'mode'              => $routing['mode'],
            'difficulty'        => $routing['difficulty'],
            'ambiguity_reason'  => $routing['ambiguity_reason'],
            'query_variants'    => $routing['query_variants'],
            'rerank_used'       => false,
            'rerank_confidence' => null,
            'low_confidence'    => false,
            'verified'          => null,
            'steps'             => $steps,
            'budget_exhausted'  => false,
        ];

        if (!self::$stream_emit_fn) {
            // Real production output — a test never reaches this branch since
            // $stream_emit_fn is set before ask_stream() is called.
            header('Content-Type: text/event-stream');
            header('Cache-Control: no-cache');
            header('X-Accel-Buffering: no'); // nginx: don't buffer the proxy response
            while (function_exists('ob_get_level') && ob_get_level() > 0) {
                @ob_end_flush();
            }
        }

        self::stream_emit('meta', ['trace' => $trace]);

        $result = Pi_Chatbot_Generator::answer_streaming($message, $routing['context'], $history, function ($delta) {
            self::stream_emit('delta', ['text' => $delta]);
        });

        if (is_wp_error($result)) {
            self::stream_emit('error', ['message' => 'The assistant is busy right now — please try again in a few minutes or call our hotline.']);
            self::stream_exit();
            return null;
        }

        $feedback_id = md5($normalized . '|' . $result['answer']);
        if ($log_id) {
            Pi_Chatbot_Log::record_duration($log_id, (int) round((microtime(true) - $request_started) * 1000));
        }

        $cached_result = [
            'answer'      => $result['answer'],
            'sources'     => $result['sources'],
            'trace'       => $trace,
            'feedback_id' => $feedback_id,
        ];
        set_transient($cache_key, $cached_result, HOUR_IN_SECONDS);

        self::stream_emit('done', ['feedback_id' => $feedback_id, 'sources' => $result['sources']]);
        self::stream_exit();
        return null;
    }

    /**
     * POST /pi/v1/chatbot/feedback — 👍/👎 on one answer, identified by the
     * feedback_id ask() attached to its response. No rate limiting shared with
     * ask() (a vote costs zero LLM calls, zero DB writes beyond 1 INSERT — an
     * entirely different resource-cost class from a real question) — a small
     * dedicated per-minute cap instead, generous enough that a real visitor
     * clicking a few 👍/👎 buttons never trips it, tight enough to stop a script
     * from flooding the table.
     */
    const FEEDBACK_RATE_LIMIT = 30; // votes / minute / IP

    public function feedback($req) {
        global $wpdb;
        $ip = $this->get_client_ip();
        $bucket = (int) floor(time() / MINUTE_IN_SECONDS);
        $key = 'pi_chatbot_fbrl_' . md5($ip) . '_' . $bucket;
        $value_option = '_transient_' . $key;
        $timeout_option = '_transient_timeout_' . $key;

        $count = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT option_value FROM {$wpdb->options} WHERE option_name = %s", $value_option
        ));
        if ($count >= self::FEEDBACK_RATE_LIMIT) {
            return new WP_Error('pi_chatbot_rate_limited', 'Too many votes — please wait a minute.', ['status' => 429]);
        }
        $wpdb->query($wpdb->prepare(
            "INSERT INTO {$wpdb->options} (option_name, option_value, autoload)
             VALUES (%s, '1', 'no')
             ON DUPLICATE KEY UPDATE option_value = option_value + 1",
            $value_option
        ));
        add_option($timeout_option, time() + (2 * MINUTE_IN_SECONDS), '', false);

        $rating = $req->get_param('rating') === 'up' ? 1 : -1;
        $ok = Pi_Chatbot_Feedback::record($req->get_param('feedback_id'), $rating);
        if (!$ok) {
            return new WP_Error('pi_chatbot_bad_feedback', 'Invalid feedback id.', ['status' => 400]);
        }
        return rest_ensure_response(['ok' => true]);
    }

    /**
     * POST /pi/v1/chatbot/lead — "leave your phone number, we'll call you
     * back" (lead capture, GLM 5.2 report recommendation C). A genuine
     * customer submits this once or twice per session at most, so the rate
     * limit here is a spam guard, not a real capacity constraint — same
     * pattern/generosity as FEEDBACK_RATE_LIMIT.
     *
     * Notifies the site admin by email on success (fire-and-forget — wp_mail()
     * failing must never turn a captured lead into an error response; the
     * lead is already safely in the DB and visible on the admin page either
     * way). No Zalo push here — see class-lead.php's docblock for why.
     */
    const LEAD_RATE_LIMIT = 5; // submissions / minute / IP

    public function lead($req) {
        global $wpdb;
        $ip = $this->get_client_ip();
        $bucket = (int) floor(time() / MINUTE_IN_SECONDS);
        $key = 'pi_chatbot_leadrl_' . md5($ip) . '_' . $bucket;
        $value_option = '_transient_' . $key;
        $timeout_option = '_transient_timeout_' . $key;

        $count = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT option_value FROM {$wpdb->options} WHERE option_name = %s", $value_option
        ));
        if ($count >= self::LEAD_RATE_LIMIT) {
            return new WP_Error('pi_chatbot_rate_limited', 'Too many requests — please wait a minute.', ['status' => 429]);
        }
        $wpdb->query($wpdb->prepare(
            "INSERT INTO {$wpdb->options} (option_name, option_value, autoload)
             VALUES (%s, '1', 'no')
             ON DUPLICATE KEY UPDATE option_value = option_value + 1",
            $value_option
        ));
        add_option($timeout_option, time() + (2 * MINUTE_IN_SECONDS), '', false);

        $lead_id = Pi_Chatbot_Lead::record(
            $req->get_param('name'),
            $req->get_param('phone'),
            $req->get_param('question')
        );
        if (!$lead_id) {
            return new WP_Error('pi_chatbot_bad_lead', 'Please enter a valid phone number.', ['status' => 400]);
        }

        self::queue_lead_notification($req->get_param('name'), $req->get_param('phone'), $req->get_param('question'));

        return rest_ensure_response(['ok' => true]);
    }

    /**
     * Pending lead notifications, flushed on `shutdown` instead of sent
     * inline during lead(). Test hook: reset to [] between suites the same
     * way Pi_Chatbot_LLM_Client::$stream_transport is (see
     * tests/lib/harness.php's pi_reset_state()) — otherwise notifications
     * queued by one suite's tests accumulate for the whole run and flush as
     * a burst of REAL emails when `wp eval-file` exits.
     */
    public static $pending_lead_notifications = [];

    /**
     * 2026-07-30 fix — measured live: wp_mail() on this install takes
     * ~2000ms and RETURNS FALSE (the local mail transport isn't configured
     * to actually deliver — see notify_admin_of_lead()'s docblock for the
     * separate fix to that silent failure). That was the true cause of the
     * lead-endpoint's real, measured slowness — not something a customer
     * should wait through just so an internal email can be composed.
     *
     * Queues the notification and defers the actual wp_mail() call to the
     * `shutdown` action instead of calling notify_admin_of_lead() inline.
     * Read this carefully before assuming what it buys you — it is NOT the
     * same trade-off streaming makes, and it is easy to overclaim:
     *
     *   - Does NOT free the PHP-FPM worker any sooner. `shutdown` fires
     *     from INSIDE the same PHP process, after the exact same amount of
     *     total work (see flush_lead_notifications()) — worker occupancy,
     *     and therefore this site's real concurrency ceiling
     *     (pm.max_children=2, see the earlier streaming-latency analysis),
     *     is completely unchanged by this.
     *   - DOES let the CUSTOMER stop waiting sooner — but only combined with
     *     fastcgi_finish_request() in flush_lead_notifications(), which
     *     actually closes the HTTP response before running the deferred
     *     work. Without that call, WP REST doesn't send a Content-Length
     *     header, so the browser's fetch() can't resolve until the whole
     *     PHP-FPM request (including every shutdown callback) finishes
     *     anyway — deferring to `shutdown` alone would be pure theatre.
     *   - Only actually does anything on PHP-FPM (the SAPI
     *     fastcgi_finish_request() exists on). On a SAPI without an
     *     equivalent, flush_lead_notifications() below silently falls back
     *     to sending inline at shutdown time — later than lead()'s old
     *     inline call, for no benefit at all on that SAPI.
     *
     * No has_action() de-dupe guard: add_action() with the same static
     * callable + priority is already idempotent (WP_Hook keys callbacks by
     * `_wp_filter_build_unique_id()`, which is deterministic for a
     * ['ClassName','method'] callable) — a guard here would be dead code at
     * best, and `!has_action(...)` specifically is a footgun (has_action()
     * returns the registered priority, which can legitimately be `0`).
     */
    private static function queue_lead_notification($name, $phone, $question) {
        self::$pending_lead_notifications[] = [$name, $phone, $question];
        add_action('shutdown', [__CLASS__, 'flush_lead_notifications']);
    }

    /**
     * Drains the queue queue_lead_notification() built, sending every pending
     * notification. Each item is wrapped independently — a bad recipient or a
     * thrown exception on ONE lead must not swallow the others still waiting
     * in the same batch, and must never propagate out of a `shutdown` hook
     * (WordPress has no caller left to hand an exception to at that point).
     */
    public static function flush_lead_notifications() {
        if (empty(self::$pending_lead_notifications)) return;

        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        } elseif (function_exists('litespeed_finish_request')) {
            litespeed_finish_request();
        }
        // No equivalent on other SAPIs (mod_php, CLI) — the notifications
        // below still send, just without the "customer already has their
        // response" benefit described in queue_lead_notification()'s
        // docblock; there is nothing further to do about that here.

        $pending = self::$pending_lead_notifications;
        self::$pending_lead_notifications = [];

        foreach ($pending as $item) {
            try {
                self::notify_admin_of_lead($item[0], $item[1], $item[2]);
            } catch (\Throwable $e) {
                error_log('[pi-chatbot] lead notification threw during flush: ' . $e->getMessage());
            }
        }
    }

    /**
     * Sends ONE lead notification email — called only from
     * flush_lead_notifications() (see queue_lead_notification()'s docblock
     * for why this is no longer called inline from lead()).
     *
     * Sends to the theme's OWN real contact emails (saigonhouse_get_contact_info()
     * — the exact same source Pi_Chatbot_Retriever::contact_context()/
     * price_context() already read from, see class-retriever.php), NOT WP's
     * `admin_email` option. 2026-07-29 fix — caught live: on this LocalWP
     * install `admin_email` is `dev-email@wpengine.local`, a placeholder that
     * goes nowhere; even on the real production site, `admin_email` is
     * whoever set up WordPress, not necessarily the business inbox staff
     * actually watch for leads. Falls back to `admin_email` only if the
     * theme's contact function isn't available at all (e.g. a different
     * theme is active) — some notification target is better than silently
     * sending nothing.
     */
    private static function notify_admin_of_lead($name, $phone, $question) {
        $to = [];
        if (function_exists('saigonhouse_get_contact_info')) {
            $c = saigonhouse_get_contact_info();
            $to = array_values(array_filter([$c['email_primary'] ?? '', $c['email_secondary'] ?? '']));
        }
        if (!$to) {
            $fallback = get_option('admin_email');
            if (!$fallback) return;
            $to = [$fallback];
        }
        $subject = '[Saigon Horeca] New chat lead: ' . ($phone ?: 'no phone');
        $body = "A visitor left their contact info in the chatbot widget.\n\n"
            . 'Name: ' . ($name ?: '(not given)') . "\n"
            . 'Phone: ' . $phone . "\n"
            . 'Last question: ' . ($question ?: '(none)') . "\n";
        // 2026-07-30 fix — wp_mail() does NOT throw on failure (WP core
        // catches PHPMailer's own exception internally and returns false),
        // so the previous try/catch here caught almost nothing — a real
        // delivery failure went completely unlogged. Check the return value.
        if (!wp_mail($to, $subject, $body)) {
            error_log('[pi-chatbot] lead notification email failed to send (wp_mail() returned false) for phone ' . $phone);
        }
    }

    /**
     * The routing/retrieval half of ask() — everything up to (not including)
     * the Generator::answer() call — factored out (2026-07-29) so ask_stream()
     * can run the EXACT same intent classification + retrieval and decide
     * whether streaming is safe for this question BEFORE spending an LLM call
     * on generation. Keeping one implementation matters here specifically:
     * ask_stream()'s eligibility rule (see its docblock) depends on knowing
     * $mode and $context precisely as ask() would compute them — a second,
     * hand-copied implementation drifting out of sync would silently make
     * streaming unsafe again (see verify()'s "$context non-empty" invariant).
     *
     * @param callable $mark Same per-stage timer closure ask() builds — passed
     *   in rather than created here, so both the router step and the
     *   retrieve/retrieve_hybrid/rerank steps still land in the ONE 'steps'
     *   trace array the frontend's progress card reads.
     * @return array{context:array,router_used:bool,intent:?string,
     *   router_keywords:array,router_brand:?string,search_query:string,
     *   mode:string,difficulty:?string,ambiguity_reason:string,
     *   query_variants:array,rerank_used:bool,rerank_confidence:?float,
     *   low_confidence:bool}
     */
    private static function resolve_context($message, $history, $settings, $budget_exhausted, $mark) {
        $context = [];
        $router_used = false;
        $intent = null;
        $router_keywords = [];
        $router_brand = null;
        $search_query = '';

        // Adaptive dispatch state — see the max-accuracy branch below.
        $mode = 'fast';
        $difficulty = null;
        $ambiguity_reason = '';
        $query_variants = [];
        $rerank_used = false;
        $rerank_confidence = null;
        $low_confidence = false;

        $enrichment = $budget_exhausted ? null : Pi_Chatbot_Router::enrich($message, $history);
        $mark('router');
        if ($enrichment) {
            $router_used = true;
            $intent = $enrichment['intent'];
            $router_keywords = array_merge($enrichment['keywords_vi'], $enrichment['keywords_en']);
            $router_brand = $enrichment['brand'] ?: null;
            $difficulty = $enrichment['difficulty'];
            $ambiguity_reason = $enrichment['ambiguity_reason'];
            $query_variants = $enrichment['query_variants'];

            switch ($intent) {
                case 'none':
                    // Greeting, small talk, or off-topic. The router said so
                    // explicitly, so retrieval is SKIPPED rather than run on the
                    // raw message — searching a product catalogue for "what's
                    // your name" is how four freezers used to end up attached to
                    // that answer as "sources". The generator handles an empty
                    // context conversationally (see its system prompt).
                    $context = [];
                    break;
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
                    // The router returns the SAME information need expressed
                    // twice — once in Vietnamese, once in English. They are
                    // ALTERNATIVES, not extra constraints, so they are searched
                    // as two queries and fused, never concatenated into one.
                    //
                    // Concatenating was measured to be actively harmful: for
                    // "상업용 냉동고 찾고 있어요" the Vietnamese leg alone
                    // ("tủ đông tủ đông công nghiệp") returned 3 correct
                    // freezers, and appending the English leg ("commercial
                    // freezer ice cream freezer") returned ZERO. Adding words to
                    // a query must never be able to empty the result set — but
                    // it can here, because rare words become +mandatory terms
                    // and require_literal_all() then demands a single product
                    // contain all of them, which no product does.
                    //
                    // Vietnamese is the primary leg: 72% of catalog titles are
                    // Vietnamese. The English leg still matters — the other 28%
                    // are English-titled imported lines (Josper, Sammic) that a
                    // Vietnamese query alone can miss — which is exactly why it
                    // stays in the fusion instead of being dropped.
                    $brand_suffix = $enrichment['brand'] ? ' ' . $enrichment['brand'] : '';
                    $query_vi = trim(implode(' ', $enrichment['keywords_vi']) . $brand_suffix);
                    $query_en = trim(implode(' ', $enrichment['keywords_en']) . $brand_suffix);

                    $search_query = $query_vi !== '' ? $query_vi : $query_en;
                    if ($search_query === '') $search_query = $message;
                    // Only a genuine second phrasing goes in — not the primary
                    // leg repeated, and not an empty string.
                    $alt_queries = array_values(array_filter(
                        [$query_vi, $query_en],
                        function ($q) use ($search_query) { return $q !== '' && $q !== $search_query; }
                    ));

                    // MAX-ACCURACY PATH — taken only when the router judged this
                    // question hard for keyword search: vague, comparative,
                    // multi-constraint, use-case-shaped, or mixed-language. Those
                    // are the questions the lexical pipeline measurably gets
                    // wrong (on the golden set its misses are near-miss ORDERING
                    // failures, not misses), and they are rare enough that
                    // spending three extra model calls on them is affordable.
                    // Everything else keeps the fast path unchanged.
                    // Past 80% of the daily LLM budget (Pi_Chatbot_Budget::is_near_limit),
                    // the extra rerank+verify calls the max path spends are exactly the
                    // "deferrable" cost the plan calls for cutting first — every 'hard'
                    // question still gets a real, useful answer via the fast path, just
                    // without the 2-3 extra LLM calls the max path would have spent on it.
                    if (!empty($settings['max_accuracy_enabled']) && $difficulty === 'hard' && !Pi_Chatbot_Budget::is_near_limit()) {
                        $mode = 'max';
                        // Hybrid retrieval + rerank + verify can legitimately run
                        // ~30s end to end; the usual 30s cap on shared hosting
                        // would kill the request mid-flight and show the customer
                        // a generic failure after they had already waited.
                        if (function_exists('set_time_limit')) @set_time_limit(60);

                        // No 'brand' opt here — search_hybrid() never read one
                        // (dead parameter, removed 2026-07-26). $router_brand is
                        // already embedded in $search_query/$alt_queries via
                        // $brand_suffix above, and Entities::extract_brands()
                        // picks it up from that text on every leg the same way
                        // it would for a customer who typed the brand name
                        // themselves — verified identical results with/without
                        // a would-be opt passed through.
                        $candidates = Pi_Chatbot_Retriever::search_hybrid($search_query, Pi_Chatbot_Reranker::MAX_CANDIDATES, [
                            'extra_queries' => $query_variants,
                            'alt_queries'   => $alt_queries,
                        ]) ?: [];
                        $mark('retrieve_hybrid');

                        // Skip reranking when an exact product code already
                        // selected the results — see
                        // Retriever::has_product_code_match(). Measured on the
                        // golden set, this was the reranker's only regression:
                        // it reordered rows an identifier had already pinned
                        // exactly, and every change it could make was a
                        // downgrade.
                        if (Pi_Chatbot_Retriever::has_product_code_match($message)) {
                            $context = array_slice($candidates, 0, (int) $settings['top_k']);
                        } else {
                            $rerank = Pi_Chatbot_Reranker::rerank($message, $candidates, (int) $settings['top_k']);
                            $rerank_used = $rerank['used'];
                            $rerank_confidence = $rerank['confidence'];
                            $context = $rerank['results'];
                        }
                        $mark('rerank');

                        // The reranker read every candidate and marked none of
                        // the top ones as actually answering the question. Handing
                        // that context to the generator anyway is how a confident
                        // wrong answer gets written — pass no context instead, so
                        // the system prompt's honest "I don't have that yet, here
                        // is the hotline" path takes over.
                        if ($rerank_used && $rerank_confidence !== null && $rerank_confidence <= 0.0) {
                            $low_confidence = true;
                            $context = [];
                        }
                    } else {
                        // Fast path also fuses the two language legs. This is
                        // still purely lexical — search_hybrid() with no
                        // extra_queries makes no model calls at all, just one
                        // more SQL pipeline — so the fast path stays fast while
                        // gaining the fix above. With only one leg present it
                        // degenerates to exactly the previous single search().
                        $context = Pi_Chatbot_Retriever::search_hybrid($search_query, (int) $settings['top_k'], [
                            'alt_queries' => $alt_queries,
                        ]) ?: [];
                    }
                    break;
            }
        } else {
            // Router unavailable (disabled/circuit-open/error) — degrade to a
            // plain product search on the raw message rather than leaving the
            // customer with no answer at all. Deliberately NOT the max path: with
            // no difficulty signal there is nothing to justify the extra cost.
            $search_query = $message;
            $context = Pi_Chatbot_Retriever::search($message, (int) $settings['top_k']) ?: [];
        }
        if ($mode !== 'max') $mark('retrieve');

        return [
            'context'           => $context,
            'router_used'       => $router_used,
            'intent'            => $intent,
            'router_keywords'   => $router_keywords,
            'router_brand'      => $router_brand,
            'search_query'      => $search_query,
            'mode'              => $mode,
            'difficulty'        => $difficulty,
            'ambiguity_reason'  => $ambiguity_reason,
            'query_variants'    => $query_variants,
            'rerank_used'       => $rerank_used,
            'rerank_confidence' => $rerank_confidence,
            'low_confidence'    => $low_confidence,
        ];
    }

    /**
     * Retrieval-only fallback used when the daily LLM budget is exhausted (see
     * Pi_Chatbot_Budget) — same {answer, sources} shape Pi_Chatbot_Generator::
     * answer() returns, so every downstream consumer (caching, the trace, the
     * frontend) handles it identically without a special case. Genuinely
     * useful rather than a bare failure page: a customer asking about a
     * product still gets its name/brand/url list, just without LLM-composed
     * prose around it.
     */
    private static function budget_exhausted_result($context) {
        if (empty($context)) {
            return [
                'answer'  => "We're getting a high volume of questions right now, so the assistant can't compose a full answer at the moment — please contact our hotline and our team will help you directly.",
                'sources' => [],
            ];
        }
        $lines = array_map(function ($item) {
            return '- ' . $item['title'] . ($item['brand'] ? " ({$item['brand']})" : '');
        }, $context);
        return [
            'answer'  => "We're getting a high volume of questions right now, so here are the closest matches from our catalog (please contact our hotline for details):\n"
                . implode("\n", $lines),
            'sources' => array_map(function ($item) {
                return ['title' => $item['title'], 'url' => $item['url']];
            }, $context),
        ];
    }

    /**
     * Cheap trigger for the fast path's OPTIONAL fact check (see the 'verify'
     * block in ask()) — the kind of claim that causes real commercial damage
     * if wrong: a number (price/capacity/dimension/model number), a URL, or a
     * model-code-looking token (letters+digits mixed, e.g. "FGTLS1260"). A
     * purely conversational answer with none of these has nothing worth
     * fact-checking at the extra LLM-call cost.
     */
    private static function answer_needs_fact_check($answer) {
        return (bool) preg_match('/https?:\/\/|\d/u', (string) $answer);
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
        $interval_check = $this->check_min_interval();
        if (is_wp_error($interval_check)) return $interval_check;

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

    /** Minimum gap between requests, milliseconds. Tight enough to only catch
     *  a script/double-click hammering the endpoint, loose enough that no real
     *  human typing-then-Enter ever trips it. */
    const MIN_REQUEST_INTERVAL_MS = 1000;

    /**
     * Per-IP minimum gap BETWEEN requests, independent of the per-minute
     * bucket above — the bucket alone allows all `rate_limit` requests to
     * land in the same instant (e.g. a script firing 10 requests in 200ms),
     * each one a full LLM7 round trip and a PHP-FPM worker held for up to
     * 60s; 10 of those landing together can pin 10 workers at once even
     * though the per-minute count is still "within limit". Storing the last-
     * request timestamp (ms) per IP and rejecting anything closer than
     * MIN_REQUEST_INTERVAL_MS afterward closes that gap.
     *
     * A plain read-then-write (not the atomic INSERT...ON DUPLICATE KEY
     * pattern used above) is an accepted, deliberate trade-off here: this is
     * a soft spam guard, not a security boundary, and the same tolerance
     * already applies to the option-based rate-limit bucket this sits next
     * to. The window it could race is under 1ms of the very moment 2
     * requests from the same visitor arrive simultaneously — not a realistic
     * concern for a chat widget that only ever sends one request at a time.
     */
    private function check_min_interval() {
        global $wpdb;
        $key = 'pi_chatbot_lastreq_' . md5($this->get_client_ip());
        $option = '_transient_' . $key;
        $now_ms = (int) round(microtime(true) * 1000);

        $last_ms = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT option_value FROM {$wpdb->options} WHERE option_name = %s", $option
        ));

        if ($last_ms > 0 && ($now_ms - $last_ms) < self::MIN_REQUEST_INTERVAL_MS) {
            return new WP_Error('pi_chatbot_rate_limited', 'You are sending messages too quickly — please wait a moment.', ['status' => 429]);
        }

        $wpdb->query($wpdb->prepare(
            "INSERT INTO {$wpdb->options} (option_name, option_value, autoload)
             VALUES (%s, %s, 'no')
             ON DUPLICATE KEY UPDATE option_value = VALUES(option_value)",
            $option, (string) $now_ms
        ));
        add_option('_transient_timeout_' . $key, time() + (2 * MINUTE_IN_SECONDS), '', false);

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

    /** Max prior exchanges (user+bot pairs) trusted from the client, matching
     *  MAX_HISTORY_TURNS in chatbot.js — enforced again here because the REST
     *  args schema only checks TYPE, not size or content: nothing stops a
     *  direct API call from sending an arbitrarily long or malformed history
     *  array, and that array feeds straight into an LLM prompt. */
    const MAX_HISTORY_TURNS = 2;
    const MAX_HISTORY_MESSAGE_LENGTH = 300;

    /**
     * Turns whatever the client sent for 'history' into a small, well-shaped
     * array the router can trust the SHAPE of (still just customer-authored
     * text content-wise, same as $message) — malformed entries are dropped
     * rather than rejecting the whole request, since a broken history is
     * recoverable (degrade to no context) while a broken message is not.
     *
     * @return array<int,array{role:string,text:string}>
     */
    private static function sanitize_history($raw) {
        if (!is_array($raw)) return [];
        $out = [];
        foreach ($raw as $item) {
            if (!is_array($item)) continue;
            $role = isset($item['role']) ? (string) $item['role'] : '';
            if ($role !== 'user' && $role !== 'bot') continue;
            $text = isset($item['text']) ? trim(sanitize_text_field((string) $item['text'])) : '';
            if ($text === '') continue;
            if (mb_strlen($text) > self::MAX_HISTORY_MESSAGE_LENGTH) {
                $text = mb_substr($text, 0, self::MAX_HISTORY_MESSAGE_LENGTH) . '…';
            }
            $out[] = ['role' => $role, 'text' => $text];
        }
        // Keep the MOST RECENT turns, not the first ones sent — a client that
        // (correctly or not) sends more than the cap should still contribute
        // the part of the conversation actually relevant to a follow-up.
        return array_slice($out, -self::MAX_HISTORY_TURNS * 2);
    }
}
