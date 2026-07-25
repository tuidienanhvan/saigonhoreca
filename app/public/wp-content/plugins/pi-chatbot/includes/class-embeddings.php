<?php
/**
 * Embeddings — semantic vectors for hybrid retrieval, stored in MySQL.
 *
 * Why a separate table (wp_pi_chatbot_vectors) instead of a column on the
 * index table: reindex_all() rebuilds the index into a SHADOW table then
 * RENAMEs it into place. A vector column would be rebuilt-from-empty on every
 * reindex, meaning a full re-embed (hundreds of paid/rate-limited API calls)
 * every time an admin clicks "Reindex now". Keyed by post_id in its own table,
 * the vectors survive the swap and only rows whose content actually changed
 * get re-embedded.
 *
 * Why the vectors are compared in PHP rather than by the database: MySQL 8.0.35
 * (this site's version) has no VECTOR type and no ANN index. At this catalog's
 * scale (~530 products x 768 dims = ~1.6 MB) a full scan + dot product in PHP
 * measures in tens of milliseconds — far below the cost of a single LLM call
 * elsewhere in the pipeline — so an approximate index would add a dependency
 * and a failure mode to save time that isn't the bottleneck.
 *
 * Provider is pluggable because the free tiers this runs on change terms:
 *   gemini — Google generativelanguage.googleapis.com, gemini-embedding-001
 *   jina   — api.jina.ai, jina-embeddings-v3
 * Both are multilingual (Vietnamese is a first-class language for both), both
 * are reachable with plain wp_remote_post from cPanel shared hosting, which is
 * the hard constraint here — no Python service is possible in production.
 *
 * @package PiChatbot
 */

if (!defined('ABSPATH')) exit;

class Pi_Chatbot_Embeddings {
    /** Table suffix (prefixed with $wpdb->prefix), sibling of PI_CHATBOT_TABLE. */
    const TABLE = 'pi_chatbot_vectors';

    const GEMINI_BASE = 'https://generativelanguage.googleapis.com/v1beta/models/';
    const JINA_URL    = 'https://api.jina.ai/v1/embeddings';

    /** Longer than the router's 8s: a batch of 40 documents legitimately takes
     *  longer than a single short query, and this runs in cron/admin, never on
     *  the customer's request path. embed_query() is the only call on the hot
     *  path and sends exactly one short string. */
    const TIMEOUT_SECONDS = 25;

    /** Max texts per batch request. Gemini's batchEmbedContents caps at 100;
     *  Jina accepts more but 100 keeps one code path for both. */
    const MAX_BATCH = 100;

    /** Characters of content_text fed to the embedder. Product descriptions in
     *  this catalog are short (43.6% under 200 chars); 1500 covers the long
     *  tail without spending tokens on boilerplate footers, and keeps one
     *  vector per product so no chunk-aggregation step is needed. */
    const DOC_TEXT_LIMIT = 1500;

    /** A phrase's embedding never changes (same model + same text = same
     *  vector), so query embeddings cache as long as the router's positive
     *  cache. */
    const CACHE_TTL_QUERY = 30 * DAY_IN_SECONDS;

    /** Circuit breaker — same atomic-bucket pattern as Pi_Chatbot_Router and
     *  Pi_Chatbot_Rest::check_rate_limit(), NOT a get_transient()/set_transient()
     *  read-modify-write (that race was a real bug fixed elsewhere in this
     *  plugin; don't reintroduce it here). */
    const BREAKER_WINDOW_SECONDS = 300;
    const BREAKER_FAIL_THRESHOLD = 5;

    /** Cron hook that walks the backfill in rate-limit-friendly batches. */
    const CRON_HOOK = 'pi_chatbot_embed_backfill';
    const CRON_DELAY_SECONDS = 90;

    public static function table_name() {
        global $wpdb;
        return $wpdb->prefix . self::TABLE;
    }

    /**
     * Created from Pi_Chatbot_Indexer::create_table() (schema version gate), NOT
     * from create_table_at() — that one also builds the shadow table, and there
     * must be exactly one vectors table, not one per reindex.
     */
    public static function create_table() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table = self::table_name();

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        // model is part of the PRIMARY KEY, not just a column: switching provider
        // or dimension count produces vectors that are meaningless to compare
        // against the old ones. Keying on it lets both live side by side during a
        // migration instead of one silently overwriting the other.
        $sql = "CREATE TABLE {$table} (
            post_id BIGINT UNSIGNED NOT NULL,
            model VARCHAR(64) NOT NULL,
            content_hash CHAR(32) NOT NULL,
            dims SMALLINT UNSIGNED NOT NULL,
            vector MEDIUMBLOB NOT NULL,
            updated_at DATETIME NOT NULL,
            PRIMARY KEY (post_id, model),
            KEY content_hash (content_hash)
        ) {$charset_collate} ENGINE=InnoDB;";
        dbDelta($sql);
    }

    // ── Configuration ──────────────────────────────────────────────────────

    /**
     * Identity stamp written into the `model` column and the query-cache key.
     * Includes the dimension count on purpose: gemini-embedding-001 at 768 dims
     * is a TRUNCATED (Matryoshka) version of the same model at 3072, and the two
     * are NOT comparable — a stamp of just the model name would let a dimension
     * change silently corrupt every similarity score.
     */
    public static function model_id($settings = null) {
        $s = $settings ?: pi_chatbot_settings();
        return sprintf('%s:%s:%d', $s['embedding_provider'], $s['embedding_model'], (int) $s['embedding_dims']);
    }

    private static function api_key($settings) {
        return $settings['embedding_provider'] === 'jina'
            ? trim((string) $settings['jina_api_key'])
            : trim((string) $settings['gemini_api_key']);
    }

    /** Configured with a key and the breaker is closed — does NOT guarantee any
     *  document has been embedded yet (see has_vectors() for that). */
    public static function is_configured($settings = null) {
        $s = $settings ?: pi_chatbot_settings();
        if (empty($s['hybrid_enabled'])) return false;
        if (self::api_key($s) === '') return false;
        return !self::breaker_open();
    }

    /**
     * True when at least one document vector exists for the CURRENT model stamp.
     * Guards the hot path: with hybrid enabled but the backfill not yet run,
     * embedding the query would spend an API call and ~0.5s to then match
     * against nothing. Cached briefly rather than per-request — a backfill
     * finishing is not urgent enough to justify a COUNT on every message.
     */
    public static function has_vectors($settings = null) {
        $s = $settings ?: pi_chatbot_settings();
        $model = self::model_id($s);
        $cache_key = 'pi_chatbot_emb_has_' . md5($model);
        $cached = get_transient($cache_key);
        if ($cached !== false) return $cached === '1';

        global $wpdb;
        $table = self::table_name();
        $has = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT EXISTS(SELECT 1 FROM {$table} WHERE model = %s)", $model
        ));
        set_transient($cache_key, $has ? '1' : '0', $has ? HOUR_IN_SECONDS : 5 * MINUTE_IN_SECONDS);
        return (bool) $has;
    }

    /** Everything needed for semantic search to actually contribute. */
    public static function is_available($settings = null) {
        $s = $settings ?: pi_chatbot_settings();
        return self::is_configured($s) && self::has_vectors($s);
    }

    // ── Vector math / storage codec ────────────────────────────────────────

    /**
     * float32 little-endian ('g'), 4 bytes per dimension — 3,072 bytes at 768
     * dims. float64 ('e') would double the storage for precision that is far
     * below the noise floor of the similarity ranking itself.
     */
    public static function pack_vector(array $v) {
        return pack('g*', ...$v);
    }

    public static function unpack_vector($blob) {
        $out = unpack('g*', (string) $blob);
        return $out === false ? [] : array_values($out); // unpack() is 1-indexed
    }

    /**
     * Plain dot product, NOT a full cosine formula: every vector that reaches
     * storage or the query cache has already been L2-normalized by normalize(),
     * so the magnitudes are 1 and the denominator would be a wasted
     * sqrt-per-comparison over hundreds of candidates.
     */
    public static function dot(array $a, array $b) {
        $n = min(count($a), count($b));
        $sum = 0.0;
        for ($i = 0; $i < $n; $i++) {
            $sum += $a[$i] * $b[$i];
        }
        return $sum;
    }

    /**
     * MANDATORY, not optional: Gemini only returns a pre-normalized vector at
     * the model's full 3072 dimensions. Any truncated outputDimensionality
     * (768 here) comes back UN-normalized, so skipping this step would let a
     * document's vector length — an artifact of truncation, not of meaning —
     * dominate the ranking.
     */
    private static function normalize(array $v) {
        $sum = 0.0;
        foreach ($v as $x) $sum += $x * $x;
        if ($sum <= 0.0) return $v;
        $inv = 1.0 / sqrt($sum);
        foreach ($v as $i => $x) $v[$i] = $x * $inv;
        return $v;
    }

    /** The exact text that gets embedded for a document — one definition shared
     *  by the backfill and the content-changed check, so they can never drift. */
    public static function document_text($row) {
        $parts = array_filter([
            (string) $row->title,
            (string) $row->brand,
            (string) $row->category,
            mb_substr((string) $row->content_text, 0, self::DOC_TEXT_LIMIT),
        ], function ($p) { return trim($p) !== ''; });
        return trim(preg_replace('/\s+/u', ' ', implode('. ', $parts)));
    }

    public static function content_hash($text) {
        return md5($text);
    }

    // ── API calls ──────────────────────────────────────────────────────────

    /**
     * Embed one customer question. Cached for 30 days keyed on the model stamp
     * + normalized text, so a repeated question costs nothing.
     *
     * @return float[]|null null on any failure — callers MUST treat null as
     *   "no semantic leg available" and fall back to lexical-only retrieval.
     */
    public static function embed_query($text) {
        $settings = pi_chatbot_settings();
        if (!self::is_configured($settings)) return null;

        $normalized = trim(preg_replace('/\s+/u', ' ', mb_strtolower((string) $text)));
        if ($normalized === '') return null;

        $cache_key = 'pi_chatbot_emb_' . md5(self::model_id($settings) . '|' . $normalized);
        $cached = get_transient($cache_key);
        if (is_array($cached)) return $cached;
        if ($cached === 'NEGATIVE') return null;

        $vectors = self::call_provider([$normalized], 'query', $settings);
        if (!$vectors || empty($vectors[0])) {
            set_transient($cache_key, 'NEGATIVE', 10 * MINUTE_IN_SECONDS);
            return null;
        }

        set_transient($cache_key, $vectors[0], self::CACHE_TTL_QUERY);
        return $vectors[0];
    }

    /**
     * @param string[] $texts
     * @return array<int,float[]|null>|null Same indexing as $texts; null (the
     *   whole return) means the request failed outright.
     */
    public static function embed_documents(array $texts) {
        if (!$texts) return [];
        $settings = pi_chatbot_settings();
        if (!self::is_configured($settings)) return null;
        return self::call_provider(array_values($texts), 'document', $settings);
    }

    /**
     * @param string $role 'query'|'document' — providers rank noticeably better
     *   when told which side of the retrieval pair a text is (asymmetric task
     *   embeddings); both APIs expose this and both are used here.
     * @return array<int,float[]|null>|null
     */
    private static function call_provider(array $texts, $role, $settings) {
        $provider = $settings['embedding_provider'];
        $result = $provider === 'jina'
            ? self::call_jina($texts, $role, $settings)
            : self::call_gemini($texts, $role, $settings);

        if ($result === null) {
            self::bump_breaker_failure();
            return null;
        }
        return $result;
    }

    private static function call_gemini(array $texts, $role, $settings) {
        $model = $settings['embedding_model'];
        $dims  = (int) $settings['embedding_dims'];
        $key   = self::api_key($settings);
        $task  = $role === 'query' ? 'RETRIEVAL_QUERY' : 'RETRIEVAL_DOCUMENT';

        // taskType/outputDimensionality are TOP-LEVEL fields on the v1beta REST
        // endpoint (the nested "config" object seen in the docs is the language
        // SDK's shape, which the REST API rejects as an unknown field).
        $requests = [];
        foreach ($texts as $t) {
            $requests[] = [
                'model'               => 'models/' . $model,
                'content'             => ['parts' => [['text' => $t]]],
                'taskType'            => $task,
                'outputDimensionality' => $dims,
            ];
        }

        $url = self::GEMINI_BASE . rawurlencode($model) . ':batchEmbedContents?key=' . rawurlencode($key);
        $res = wp_remote_post($url, [
            'timeout' => self::TIMEOUT_SECONDS,
            'headers' => ['Content-Type' => 'application/json'],
            'body'    => wp_json_encode(['requests' => $requests]),
        ]);

        if (is_wp_error($res)) {
            error_log('[pi-chatbot-embeddings] Gemini request failed: ' . $res->get_error_message());
            return null;
        }
        $code = wp_remote_retrieve_response_code($res);
        $data = json_decode(wp_remote_retrieve_body($res), true);
        if ($code < 200 || $code >= 300 || !is_array($data) || !isset($data['embeddings'])) {
            error_log("[pi-chatbot-embeddings] Gemini HTTP {$code}: " . substr((string) wp_remote_retrieve_body($res), 0, 500));
            return null;
        }

        $out = [];
        foreach ($texts as $i => $_) {
            $values = $data['embeddings'][$i]['values'] ?? null;
            $out[$i] = is_array($values) && $values ? self::normalize(array_map('floatval', $values)) : null;
        }
        return $out;
    }

    private static function call_jina(array $texts, $role, $settings) {
        $key = self::api_key($settings);
        $res = wp_remote_post(self::JINA_URL, [
            'timeout' => self::TIMEOUT_SECONDS,
            'headers' => [
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . $key,
            ],
            'body' => wp_json_encode([
                'model'      => $settings['embedding_model'],
                'task'       => $role === 'query' ? 'retrieval.query' : 'retrieval.passage',
                'dimensions' => (int) $settings['embedding_dims'],
                'input'      => $texts,
            ]),
        ]);

        if (is_wp_error($res)) {
            error_log('[pi-chatbot-embeddings] Jina request failed: ' . $res->get_error_message());
            return null;
        }
        $code = wp_remote_retrieve_response_code($res);
        $data = json_decode(wp_remote_retrieve_body($res), true);
        if ($code < 200 || $code >= 300 || !is_array($data) || empty($data['data'])) {
            error_log("[pi-chatbot-embeddings] Jina HTTP {$code}: " . substr((string) wp_remote_retrieve_body($res), 0, 500));
            return null;
        }

        // Jina echoes an explicit "index" per item — order is NOT guaranteed to
        // match the input array, so map by that field rather than by position.
        $out = array_fill(0, count($texts), null);
        foreach ($data['data'] as $item) {
            $i = isset($item['index']) ? (int) $item['index'] : null;
            if ($i === null || !isset($out[$i]) && $i >= count($texts)) continue;
            $values = $item['embedding'] ?? null;
            if (is_array($values) && $values) $out[$i] = self::normalize(array_map('floatval', $values));
        }
        return $out;
    }

    // ── Storage ────────────────────────────────────────────────────────────

    public static function store($post_id, array $vector, $content_hash, $settings = null) {
        global $wpdb;
        $s = $settings ?: pi_chatbot_settings();
        $wpdb->replace(self::table_name(), [
            'post_id'      => (int) $post_id,
            'model'        => self::model_id($s),
            'content_hash' => $content_hash,
            'dims'         => count($vector),
            'vector'       => self::pack_vector($vector),
            'updated_at'   => current_time('mysql'),
        ], ['%d', '%s', '%s', '%d', '%s', '%s']);
        delete_transient('pi_chatbot_emb_has_' . md5(self::model_id($s)));
    }

    public static function delete_post($post_id) {
        global $wpdb;
        $wpdb->delete(self::table_name(), ['post_id' => (int) $post_id], ['%d']);
    }

    /**
     * Loads document vectors for the current model.
     *
     * @param int[]|null $post_ids Restrict to these ids (used when the router
     *   recognized a brand, so only that brand's products are scanned) — null
     *   loads every vector.
     * @return array<int,float[]> post_id => vector
     */
    public static function load_vectors($post_ids = null, $settings = null) {
        global $wpdb;
        $s = $settings ?: pi_chatbot_settings();
        $table = self::table_name();
        $model = self::model_id($s);
        $dims  = (int) $s['embedding_dims'];

        if (is_array($post_ids)) {
            $post_ids = array_values(array_unique(array_map('intval', $post_ids)));
            if (!$post_ids) return [];
            $placeholders = implode(',', array_fill(0, count($post_ids), '%d'));
            $rows = $wpdb->get_results($wpdb->prepare(
                "SELECT post_id, vector FROM {$table}
                 WHERE model = %s AND dims = %d AND post_id IN ({$placeholders})",
                array_merge([$model, $dims], $post_ids)
            ));
        } else {
            $rows = $wpdb->get_results($wpdb->prepare(
                "SELECT post_id, vector FROM {$table} WHERE model = %s AND dims = %d",
                $model, $dims
            ));
        }

        $out = [];
        foreach ((array) $rows as $r) {
            $v = self::unpack_vector($r->vector);
            if ($v) $out[(int) $r->post_id] = $v;
        }
        return $out;
    }

    // ── Backfill ───────────────────────────────────────────────────────────

    /** @return array{embedded:int,total:int,pending:int} */
    public static function stats($settings = null) {
        global $wpdb;
        $s = $settings ?: pi_chatbot_settings();
        $index = Pi_Chatbot_Indexer::table_name();
        $vec   = self::table_name();
        $model = self::model_id($s);

        $total = (int) $wpdb->get_var(
            "SELECT COUNT(*) FROM {$index} WHERE post_type IN ('product','project')"
        );
        $embedded = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$vec} v
             INNER JOIN {$index} i ON i.post_id = v.post_id
             WHERE v.model = %s AND i.post_type IN ('product','project')",
            $model
        ));
        return ['embedded' => $embedded, 'total' => $total, 'pending' => max(0, $total - $embedded)];
    }

    /**
     * Embeds one batch of documents that are missing or stale.
     *
     * Staleness is decided by comparing the index row's updated_at against the
     * vector row's — no MD5 of the composed text in SQL, which would have to
     * reproduce document_text()'s exact truncation/whitespace rules in a second
     * language and would silently re-embed everything the moment they drifted.
     * The stored content_hash is then compared in PHP so a post re-saved with no
     * real change costs a timestamp touch instead of an API call.
     *
     * @return array{done:bool,embedded:int,pending:int,error:?string}
     */
    public static function backfill_batch($limit = 40) {
        global $wpdb;
        $settings = pi_chatbot_settings();
        if (!self::is_configured($settings)) {
            return ['done' => true, 'embedded' => 0, 'pending' => 0, 'error' => 'Embeddings are not configured (missing API key, hybrid disabled, or circuit breaker open).'];
        }

        $index = Pi_Chatbot_Indexer::table_name();
        $vec   = self::table_name();
        $model = self::model_id($settings);
        $limit = max(1, min(self::MAX_BATCH, (int) $limit));

        $rows = $wpdb->get_results($wpdb->prepare(
            "SELECT i.post_id, i.title, i.brand, i.category, i.content_text
             FROM {$index} i
             LEFT JOIN {$vec} v ON v.post_id = i.post_id AND v.model = %s
             WHERE i.post_type IN ('product','project')
               AND (v.post_id IS NULL OR v.updated_at < i.updated_at)
             ORDER BY i.post_id ASC
             LIMIT %d",
            $model, $limit
        ));

        if (!$rows) {
            return ['done' => true, 'embedded' => 0, 'pending' => 0, 'error' => null];
        }

        $texts = [];
        $hashes = [];
        $unchanged = [];
        $existing = self::existing_hashes(wp_list_pluck($rows, 'post_id'), $model);

        foreach ($rows as $r) {
            $text = self::document_text($r);
            $hash = self::content_hash($text);
            $pid  = (int) $r->post_id;
            if (isset($existing[$pid]) && $existing[$pid] === $hash) {
                // Re-saved with no substantive change — refresh the timestamp so
                // this row stops being selected, without paying for an API call.
                $unchanged[] = $pid;
                continue;
            }
            $texts[$pid]  = $text;
            $hashes[$pid] = $hash;
        }

        if ($unchanged) {
            $wpdb->query($wpdb->prepare(
                "UPDATE {$vec} SET updated_at = %s
                 WHERE model = %s AND post_id IN (" . implode(',', array_fill(0, count($unchanged), '%d')) . ")",
                array_merge([current_time('mysql'), $model], $unchanged)
            ));
        }

        $embedded = 0;
        if ($texts) {
            $ids = array_keys($texts);
            $vectors = self::embed_documents(array_values($texts));
            if ($vectors === null) {
                return ['done' => false, 'embedded' => 0, 'pending' => self::stats($settings)['pending'], 'error' => 'The embedding API call failed — see the error log.'];
            }
            foreach ($ids as $i => $pid) {
                if (empty($vectors[$i])) continue;
                self::store($pid, $vectors[$i], $hashes[$pid], $settings);
                $embedded++;
            }
        }

        $pending = self::stats($settings)['pending'];
        return ['done' => $pending === 0, 'embedded' => $embedded, 'pending' => $pending, 'error' => null];
    }

    private static function existing_hashes($post_ids, $model) {
        global $wpdb;
        $post_ids = array_values(array_unique(array_map('intval', (array) $post_ids)));
        if (!$post_ids) return [];
        $placeholders = implode(',', array_fill(0, count($post_ids), '%d'));
        $rows = $wpdb->get_results($wpdb->prepare(
            "SELECT post_id, content_hash FROM " . self::table_name() . "
             WHERE model = %s AND post_id IN ({$placeholders})",
            array_merge([$model], $post_ids)
        ));
        $out = [];
        foreach ((array) $rows as $r) $out[(int) $r->post_id] = $r->content_hash;
        return $out;
    }

    /**
     * Queues the backfill as a self-rescheduling single cron event rather than
     * a loop: free embedding tiers are rate-limited per minute, and a
     * synchronous loop over hundreds of products would either trip that limit or
     * hold a PHP worker for minutes. Scheduling is a no-op if a run is already
     * pending, so save_post firing repeatedly during a bulk edit can't stack up
     * a queue of duplicate jobs.
     */
    public static function schedule_backfill($delay = 5) {
        if (!self::is_configured()) return;
        if (wp_next_scheduled(self::CRON_HOOK)) return;
        wp_schedule_single_event(time() + max(1, (int) $delay), self::CRON_HOOK);
    }

    public static function run_backfill_cron() {
        $result = self::backfill_batch(40);
        // Reschedule while work remains AND this batch made progress. Without
        // the progress check, a permanently-failing provider (revoked key) would
        // re-arm the job every 90 seconds forever; the breaker eventually opens
        // and is_configured() then stops the chain from being re-armed at all.
        if (!$result['done'] && $result['embedded'] > 0) {
            wp_schedule_single_event(time() + self::CRON_DELAY_SECONDS, self::CRON_HOOK);
        }
    }

    // ── Circuit breaker ────────────────────────────────────────────────────

    private static function breaker_bucket() {
        return (int) floor(time() / self::BREAKER_WINDOW_SECONDS);
    }

    public static function breaker_open() {
        global $wpdb;
        $key = '_transient_pi_chatbot_emb_fail_' . self::breaker_bucket();
        $count = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT option_value FROM {$wpdb->options} WHERE option_name = %s", $key
        ));
        return $count >= self::BREAKER_FAIL_THRESHOLD;
    }

    private static function bump_breaker_failure() {
        global $wpdb;
        $bucket = self::breaker_bucket();
        $key = '_transient_pi_chatbot_emb_fail_' . $bucket;
        $wpdb->query($wpdb->prepare(
            "INSERT INTO {$wpdb->options} (option_name, option_value, autoload)
             VALUES (%s, '1', 'no')
             ON DUPLICATE KEY UPDATE option_value = option_value + 1",
            $key
        ));
        add_option('_transient_timeout_pi_chatbot_emb_fail_' . $bucket, time() + self::BREAKER_WINDOW_SECONDS, '', false);
    }
}
