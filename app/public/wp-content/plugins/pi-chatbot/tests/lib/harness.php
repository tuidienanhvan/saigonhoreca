<?php
/**
 * harness.php — shared test scaffolding for every pi-chatbot suite.
 *
 * There is no PHPUnit here on purpose. A WordPress PHPUnit setup needs the
 * wp-tests-lib scaffolding and a second database; this plugin has no build step,
 * no composer.json, and runs on LocalWP where that scaffolding is one more thing
 * to break. `wp eval-file` already gives a fully booted WordPress with the real
 * database, which is what these tests actually need.
 *
 * The one capability that turns "needs a live LLM" into "deterministic in
 * milliseconds" is WordPress's own `pre_http_request` filter: returning anything
 * other than false from it short-circuits wp_remote_post entirely. That lets the
 * router, reranker, generator and the whole REST flow be tested without a
 * network call, without spending free-tier quota, and — because the stub records
 * what it intercepted — lets a test assert on the PROMPT that was built, not
 * just on the value that came back.
 *
 * Every suite must be safe to run repeatedly against the live LocalWP database.
 * Anything that writes gets a unique prefix and is removed in teardown.
 *
 * WRITE SUITES: never track cleanup state with `$x = []` at file top-level plus
 * `global $x` inside a helper function you define in the same file. `wp
 * eval-file` includes the whole file from INSIDE one of ITS OWN functions, so
 * that top-level `$x` is a LOCAL of that wrapping function — `global $x` in
 * your helper binds to $GLOBALS['x'], a different variable entirely, silently.
 * suite-indexer.php did exactly this on its first version: every post it
 * created got tracked into the wrong variable, the `finally` cleanup loop
 * iterated a permanently-empty array, and 28 real posts were left in wp_posts
 * across two runs before a manual before/after ID diff caught it — nothing in
 * the test output looked wrong; every assertion still passed. Track state in a
 * FUNCTION STATIC instead (see pi_check() and pi_http_record() below for the
 * pattern), and have teardown VERIFY its own result with a fresh query rather
 * than trusting a delete call's return value — see suite-indexer.php's
 * `finally` block for the follow-up check that would have caught this
 * immediately. `global $wpdb` is NOT this trap — $wpdb is WordPress's own real
 * global, registered by WP's bootstrap, so referencing it via `global` from
 * anywhere is correct; the trap is only for state a suite invents itself.
 */

if (!defined('ABSPATH')) {
    fwrite(STDERR, "Must be run via `wp eval-file`, not directly with the PHP CLI.\n");
    exit(1);
}

if (!function_exists('pi_check')) {

    /**
     * Records one assertion.
     *
     * State lives in a function static, NOT in file-scope variables reached via
     * `global`. `wp eval-file` includes these files from INSIDE a function, so
     * their top-level variables are locals of that scope — a `global $failures`
     * in a helper would write to a completely different variable than the one
     * the summary reads. The first version of the original unit-checks file did
     * exactly that and always printed success, because failures accumulated into
     * a global nobody looked at. A test harness that cannot report failure is
     * worse than no harness, so the state lives somewhere no scoping rule can
     * split it.
     *
     * Call with no arguments to read the running totals.
     *
     * @return array{passed:int,failed:string[]}|null
     */
    function pi_check($name = null, $condition = null, $detail = '') {
        static $passed = 0;
        static $failed = [];

        if ($name === null) return ['passed' => $passed, 'failed' => $failed];

        if ($condition) {
            $passed++;
            echo "  ok   {$name}\n";
        } else {
            $failed[] = $name . ($detail !== '' ? " ({$detail})" : '');
            echo "  FAIL {$name}" . ($detail !== '' ? " — {$detail}" : '') . "\n";
        }
        return null;
    }

    /** Section heading, so a long run stays readable. */
    function pi_section($title) {
        echo "\n[{$title}]\n";
    }

    /**
     * Invokes a private/protected static method. These are private for good
     * reason — they are not API — but their behaviour is exactly what needs
     * pinning down, and testing them only through the public surface would mean
     * a failure points at the wrong layer.
     */
    function pi_call_private($class, $method, array $args) {
        $ref = new ReflectionMethod($class, $method);
        $ref->setAccessible(true);
        return $ref->invokeArgs(null, $args);
    }

    /** Reads a private/protected static property. */
    function pi_get_private_prop($class, $prop) {
        $ref = new ReflectionProperty($class, $prop);
        $ref->setAccessible(true);
        return $ref->getValue();
    }

    function pi_set_private_prop($class, $prop, $value) {
        $ref = new ReflectionProperty($class, $prop);
        $ref->setAccessible(true);
        $ref->setValue(null, $value);
    }

    // ── HTTP stubbing ──────────────────────────────────────────────────────

    /**
     * Intercepts every outbound HTTP request for the duration of a test.
     *
     * @param callable $responder function(array $args, string $url, int $callIndex)
     *   returning either a WP HTTP response array, or a WP_Error to simulate a
     *   transport failure. Returning null falls through to a generic 200 so a
     *   test only has to describe the calls it cares about.
     */
    function pi_http_stub(callable $responder) {
        pi_http_reset_calls();
        pi_http_unstub();

        $filter = function ($pre, $args, $url) use ($responder) {
            $index = count(pi_http_calls());
            // Record BEFORE responding: a test asserting on prompt content needs
            // the request even when the stub answers with an error.
            pi_http_record([
                'url'  => $url,
                'body' => isset($args['body']) ? json_decode($args['body'], true) : null,
                'raw'  => $args['body'] ?? null,
                'args' => $args,
            ]);
            $response = $responder($args, $url, $index);
            if ($response === null) return pi_http_response(['choices' => [['message' => ['content' => 'ok']]]]);
            return $response;
        };
        pi_http_current_filter($filter);
        add_filter('pre_http_request', $filter, 10, 3);
    }

    function pi_http_unstub() {
        $existing = pi_http_current_filter();
        if ($existing) {
            remove_filter('pre_http_request', $existing, 10);
            pi_http_current_filter(false);
        }
    }

    /** Holder for the active filter closure so it can be removed by identity. */
    function pi_http_current_filter($set = null) {
        static $filter = null;
        if ($set === false) { $filter = null; return null; }
        if ($set !== null) { $filter = $set; return $filter; }
        return $filter;
    }

    function pi_http_record($call = null) {
        static $calls = [];
        if ($call === 'RESET') { $calls = []; return []; }
        if ($call !== null) { $calls[] = $call; }
        return $calls;
    }

    function pi_http_calls() { return pi_http_record(); }
    function pi_http_reset_calls() { pi_http_record('RESET'); }

    /** Builds a well-formed LLM7-shaped 200 response. */
    function pi_http_response(array $payload, $code = 200) {
        return [
            'headers'  => [],
            'body'     => wp_json_encode($payload),
            'response' => ['code' => $code, 'message' => $code === 200 ? 'OK' : 'Error'],
            'cookies'  => [],
            'filename' => null,
        ];
    }

    /** Shorthand: a chat-completion reply carrying exactly this content string. */
    function pi_http_content($content) {
        return pi_http_response(['choices' => [['message' => ['content' => $content]]]]);
    }

    // ── Rate-limit bucket seeding ──────────────────────────────────────────

    /**
     * Directly seeds a per-minute rate-limit bucket to $count — for a test
     * that needs to prove a limit/increment guard WITHOUT making N sequential
     * real calls (which was the actual cause of a real, repeatedly-observed
     * flake: Pi_Chatbot_Rest::lead()/feedback()/check_rate_limit() all key a
     * bucket on `floor(time() / MINUTE_IN_SECONDS)`, computed FRESH on every
     * call — a test hammering 6 real calls in a row has a real, measured
     * ~1/12 chance one of them lands after a wall-clock minute rolled over
     * mid-loop, silently resetting the counter and failing the assertion for
     * a reason that has nothing to do with the code under test).
     *
     * Writes via the IDENTICAL `INSERT ... ON DUPLICATE KEY UPDATE` shape
     * production uses (see Pi_Chatbot_Rest::lead()) — same table, same
     * columns, same 'no' autoload, same key format — so the row this seeds
     * IS the row production's own increment touches next, not a look-alike
     * the real code would silently ignore. Also seeds the companion
     * `_transient_timeout_` row production writes via add_option(), so a
     * seeded bucket is indistinguishable from one a real request built.
     *
     * Seeds BOTH the current bucket and the next one (bucket+1) — this is
     * what makes a test built on this helper immune to the same
     * minute-boundary race, not merely less likely to hit it: regardless of
     * which bucket a call actually lands in (current, or the very next one
     * if a rollover happens between seeding and the call), it was already
     * seeded.
     *
     * @param string $key_prefix One of 'pi_chatbot_leadrl_', 'pi_chatbot_fbrl_',
     *   'pi_chatbot_rl_' — the exact prefix the REST method under test uses.
     * @param string $ip
     * @param int    $count
     */
    function pi_seed_minute_bucket($key_prefix, $ip, $count) {
        global $wpdb;
        $bucket = (int) floor(time() / MINUTE_IN_SECONDS);
        foreach ([$bucket, $bucket + 1] as $b) {
            $key = $key_prefix . md5($ip) . '_' . $b;
            $wpdb->query($wpdb->prepare(
                "INSERT INTO {$wpdb->options} (option_name, option_value, autoload)
                 VALUES (%s, %s, 'no')
                 ON DUPLICATE KEY UPDATE option_value = VALUES(option_value)",
                '_transient_' . $key, (string) $count
            ));
            $wpdb->query($wpdb->prepare(
                "INSERT INTO {$wpdb->options} (option_name, option_value, autoload)
                 VALUES (%s, %s, 'no')
                 ON DUPLICATE KEY UPDATE option_value = VALUES(option_value)",
                '_transient_timeout_' . $key, (string) (time() + 2 * MINUTE_IN_SECONDS)
            ));
        }
    }

    /**
     * Reads the SAME two-bucket window pi_seed_minute_bucket() seeds (current
     * + next), summed — so "did the real call I just made actually increment
     * the bucket" can be asserted without caring which of the two buckets it
     * landed in.
     */
    function pi_read_minute_bucket($key_prefix, $ip) {
        global $wpdb;
        $bucket = (int) floor(time() / MINUTE_IN_SECONDS);
        $total = 0;
        foreach ([$bucket, $bucket + 1] as $b) {
            $key = $key_prefix . md5($ip) . '_' . $b;
            $total += (int) $wpdb->get_var($wpdb->prepare(
                "SELECT option_value FROM {$wpdb->options} WHERE option_name = %s",
                '_transient_' . $key
            ));
        }
        return $total;
    }

    // ── Mail stubbing ───────────────────────────────────────────────────────

    /**
     * Intercepts every wp_mail() call for the duration of a test — same role
     * as pi_http_stub() above, and for the same reason: a test suite must run
     * deterministically, offline, and without side effects a real run
     * shouldn't have (here: not spamming a real inbox every time the lead
     * rate-limit test's loop calls Pi_Chatbot_Rest::notify_admin_of_lead()).
     * Uses the `pre_wp_mail` filter (WP 5.7+) — returning non-null
     * short-circuits wp_mail() entirely and becomes its return value.
     *
     * @param callable|null $responder function(array $atts): bool — $atts has
     *   to/subject/message/headers/attachments. Return true/false to simulate
     *   success/failure. A responder returning null (or omitted entirely)
     *   defaults to success — a forgetful test can never leak a real send,
     *   since null is coerced to true here rather than passed through (unlike
     *   pi_http_stub(), where null falls through to a canned response — mail
     *   has no safe "canned response" other than "don't actually send").
     */
    function pi_mail_stub(callable $responder = null) {
        pi_mail_reset_calls();
        pi_mail_unstub();
        if ($responder === null) $responder = function ($atts) { return true; };

        $filter = function ($pre, $atts) use ($responder) {
            pi_mail_record($atts);
            $result = $responder($atts);
            return $result === null ? true : $result;
        };
        pi_mail_current_filter($filter);
        add_filter('pre_wp_mail', $filter, 10, 2);
    }

    function pi_mail_unstub() {
        $existing = pi_mail_current_filter();
        if ($existing) {
            remove_filter('pre_wp_mail', $existing, 10);
            pi_mail_current_filter(false);
        }
    }

    /** Holder for the active filter closure so it can be removed by identity. */
    function pi_mail_current_filter($set = null) {
        static $filter = null;
        if ($set === false) { $filter = null; return null; }
        if ($set !== null) { $filter = $set; return $filter; }
        return $filter;
    }

    function pi_mail_record($call = null) {
        static $calls = [];
        if ($call === 'RESET') { $calls = []; return []; }
        if ($call !== null) { $calls[] = $call; }
        return $calls;
    }

    function pi_mail_calls() { return pi_mail_record(); }
    function pi_mail_reset_calls() { pi_mail_record('RESET'); }

    // ── State isolation ────────────────────────────────────────────────────

    /**
     * Clears everything that survives between calls inside one PHP process and
     * would otherwise let one test decide another's outcome.
     *
     * Retriever::$product_count_cache is the sharp edge: it memoizes a COUNT for
     * the whole request, so a suite that writes rows and then measures IDF would
     * silently use the pre-write total.
     */
    function pi_reset_state() {
        global $wpdb;

        pi_set_private_prop('Pi_Chatbot_Retriever', 'product_count_cache', null);
        // Same sharp edge as above, added 2026-07-26 alongside compute_idf()'s
        // new caching: this one memoizes DF per WORD for the whole request, so
        // a suite that changes catalog content and re-measures IDF for a word
        // it already asked about earlier would silently reuse the stale value.
        pi_set_private_prop('Pi_Chatbot_Retriever', 'idf_request_cache', []);
        // Same reason, added 2026-07-26 alongside search_by_product_code()'s
        // new memoization: a suite that writes/renames a product mid-run and
        // re-asks about the same code would otherwise see the pre-write match.
        pi_set_private_prop('Pi_Chatbot_Retriever', 'product_code_match_cache', []);

        foreach ([
            'pi_chatbot_known_brands',
            'pi_chatbot_taxonomy_brands',
            'pi_chatbot_rtr_fewshot',
        ] as $key) {
            delete_transient($key);
        }

        // Router caches, answer caches, rate-limit buckets and breaker counters
        // are all keyed by a hash, so they can only be cleared by pattern.
        // pi_chatbot_llmbudget_ (2026-07-27, class-budget.php) is included here
        // too even though it is deliberately NOT a transient (it must survive
        // transient GC in production) — a suite that pushes the daily budget
        // over its limit must not leave that state for the NEXT suite's tests.
        $wpdb->query(
            "DELETE FROM {$wpdb->options}
             WHERE option_name LIKE '\_transient\_pi\_chatbot\_%'
                OR option_name LIKE '\_transient\_timeout\_pi\_chatbot\_%'
                OR option_name LIKE 'pi\_chatbot\_llmbudget\_%'"
        );

        // The raw DELETE above only touches the DATABASE row — WordPress's
        // object cache (in-memory for the lifetime of this one PHP process,
        // which is the entire test run) still holds whatever get_transient()
        // last read. Without flushing it, a later ask() in the SAME suite run
        // can silently reuse a cached answer/router-classification from an
        // earlier test block even though the underlying option row is gone —
        // this was caught live: a "low confidence" test received the cached
        // answer text from an unrelated "confident rerank" test that ran two
        // pi_with_settings() blocks earlier, because both blocks searched the
        // same brand name and the in-memory cache outlived the DB delete.
        wp_cache_flush();
    }

    /**
     * Runs $fn with the plugin settings temporarily overridden, then restores
     * the saved option EXACTLY as it was — including restoring "no option at
     * all", which is not the same as an empty array. The restore runs in a
     * finally block so a failing assertion cannot leave the site reconfigured.
     */
    function pi_with_settings(array $overrides, callable $fn) {
        $had = get_option(PI_CHATBOT_OPTION, null);
        $base = is_array($had) ? $had : [];
        update_option(PI_CHATBOT_OPTION, array_merge($base, $overrides), false);
        try {
            return $fn();
        } finally {
            if ($had === null) {
                delete_option(PI_CHATBOT_OPTION);
            } else {
                update_option(PI_CHATBOT_OPTION, $had, false);
            }
        }
    }

    // ── Fixtures ───────────────────────────────────────────────────────────

    /**
     * Creates a throwaway table with the index schema. Built by calling the
     * plugin's own create_table_at() rather than a copy of the CREATE TABLE —
     * a duplicated schema here would drift the moment the real one changed, and
     * the tests would then be validating a table shape that no longer ships.
     */
    function pi_temp_index_table() {
        global $wpdb;
        $name = $wpdb->prefix . 'pi_chatbot_index_test_' . wp_rand(100000, 999999);
        pi_call_private('Pi_Chatbot_Indexer', 'create_table_at', [$name]);
        if ((string) $wpdb->get_var("SHOW TABLES LIKE '{$name}'") !== $name) {
            return null;
        }
        return $name;
    }

    function pi_drop_temp_table($name) {
        global $wpdb;
        if (!$name) return;
        // Only ever drop something that carries the test marker, so a bug in a
        // caller can never take out the live index table.
        if (strpos($name, 'pi_chatbot_index_test_') === false) return;
        $wpdb->query("DROP TABLE IF EXISTS {$name}");
    }

    /** Leaves no orphans behind if a suite crashed before its own teardown. */
    function pi_drop_all_temp_tables() {
        global $wpdb;
        $like = $wpdb->esc_like($wpdb->prefix . 'pi_chatbot_index_test_') . '%';
        foreach ((array) $wpdb->get_col($wpdb->prepare('SHOW TABLES LIKE %s', $like)) as $t) {
            pi_drop_temp_table($t);
        }
    }

    /** Minimal stdClass row matching what the retriever's SQL returns. */
    function pi_row($fields = []) {
        return (object) array_merge([
            'post_id'      => 1,
            'post_type'    => 'product',
            'title'        => 'Test product',
            'brand'        => '',
            'category'     => '',
            'url'          => 'https://example.test/p/1',
            'content_text' => 'Test content',
            'score'        => 1.0,
        ], $fields);
    }

    /** Formatted row, i.e. what search() returns and the reranker consumes. */
    function pi_result($fields = []) {
        return array_merge([
            'post_id'  => 1,
            'title'    => 'Test product',
            'brand'    => '',
            'category' => '',
            'url'      => 'https://example.test/p/1',
            'excerpt'  => 'Test excerpt',
            'score'    => 1.0,
        ], $fields);
    }

    // ── Reporting ──────────────────────────────────────────────────────────

    /**
     * Prints the summary and returns the exit code.
     *
     * $expected_failures lets a suite register assertions it MEANT to fail —
     * used by the harness self-test, which proves this reporter can actually go
     * red. Without that proof a scoping or counter bug makes every run print
     * success no matter what the assertions did, which is the exact failure this
     * file's docblock describes.
     */
    function pi_report($expected_failures = []) {
        $summary = pi_check();
        $real = array_values(array_filter($summary['failed'], function ($f) use ($expected_failures) {
            foreach ($expected_failures as $prefix) {
                if (strpos($f, $prefix) === 0) return false;
            }
            return true;
        }));

        echo "\n" . str_repeat('=', 66) . "\n";
        if ($real) {
            printf("FAILED — %d passed, %d failed:\n", $summary['passed'], count($real));
            foreach ($real as $f) echo "  - {$f}\n";
            echo "\n";
            return 1;
        }
        $note = $expected_failures ? ' (plus deliberate failures proving the reporter goes red)' : '';
        printf("All %d checks passed%s.\n\n", $summary['passed'], $note);
        return 0;
    }
}
