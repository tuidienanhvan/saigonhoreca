<?php
/**
 * Pi API Chatbot Endpoints
 *
 * Knowledge Base + Intent Detection + Skills + RAG management for the
 * Pi Chatbot plugins (v1 + v2). Backs the React ChatbotKB page in
 * pi-dashboard-webapp. Logic is ported from:
 *   - plugins/pi-chatbot/includes/IntentDetector.php      (similar_text / keyword scoring)
 *   - plugins/pi-chatbot/includes/Chatbot.php             (sanitize reply, system prompt)
 *   - plugins/pi-chatbot-v2/includes/Modules/IntentEngine.php (pattern matching)
 *   - plugins/pi-chatbot-v2/includes/Modules/AiReply.php       (buildSystemPrompt)
 *   - plugins/pi-chatbot-v2/includes/Skills/SkillRegistry.php  (detectIntent)
 *
 * Options store:
 *   pi_chatbot_settings   - runtime settings (greeting, model, prompt, …)
 *   pi_chatbot_kb         - array of KB entries
 *   pi_chatbot_intents    - array of intent rules
 *   pi_chatbot_skills     - array of skill (tool) definitions
 *   pi_chatbot_rag_config - RAG config (embedding, chunk, overlap, …)
 *   pi_chatbot_rag_status - RAG index status (for polling)
 *
 * @package Pi_Api
 */

if (!defined('ABSPATH')) exit;

class Pi_Api_Chatbot {

    const OPT_SETTINGS  = 'pi_chatbot_settings';
    const OPT_KB        = 'pi_chatbot_kb';
    const OPT_INTENTS   = 'pi_chatbot_intents';
    const OPT_SKILLS    = 'pi_chatbot_skills';
    const OPT_RAG       = 'pi_chatbot_rag_config';
    const OPT_RAG_STAT  = 'pi_chatbot_rag_status';

    public static function register_routes($ns, $admin) {
        // Settings
        register_rest_route($ns, '/chatbot/settings', [
            ['methods' => 'GET',  'callback' => [self::class, 'get_settings'],  'permission_callback' => $admin],
            ['methods' => 'POST', 'callback' => [self::class, 'save_settings'], 'permission_callback' => $admin],
        ]);

        // Knowledge base
        register_rest_route($ns, '/chatbot/kb', [
            ['methods' => 'GET',  'callback' => [self::class, 'list_kb'],  'permission_callback' => $admin],
            ['methods' => 'POST', 'callback' => [self::class, 'save_kb'],  'permission_callback' => $admin],
        ]);
        register_rest_route($ns, '/chatbot/kb/(?P<id>[a-zA-Z0-9_\-]+)', [
            'methods'             => 'DELETE',
            'callback'            => [self::class, 'delete_kb'],
            'permission_callback' => $admin,
        ]);
        register_rest_route($ns, '/chatbot/kb/import-csv', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'import_csv'],
            'permission_callback' => $admin,
        ]);
        register_rest_route($ns, '/chatbot/kb/export-csv', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'export_csv'],
            'permission_callback' => $admin,
        ]);

        // Intents
        register_rest_route($ns, '/chatbot/intents', [
            ['methods' => 'GET',  'callback' => [self::class, 'list_intents'], 'permission_callback' => $admin],
            ['methods' => 'POST', 'callback' => [self::class, 'save_intent'],  'permission_callback' => $admin],
        ]);
        register_rest_route($ns, '/chatbot/intents/(?P<id>[a-zA-Z0-9_\-]+)', [
            'methods'             => 'DELETE',
            'callback'            => [self::class, 'delete_intent'],
            'permission_callback' => $admin,
        ]);

        // Skills
        register_rest_route($ns, '/chatbot/skills', [
            ['methods' => 'GET',  'callback' => [self::class, 'list_skills'], 'permission_callback' => $admin],
            ['methods' => 'POST', 'callback' => [self::class, 'save_skill'],  'permission_callback' => $admin],
        ]);
        register_rest_route($ns, '/chatbot/skills/(?P<id>[a-zA-Z0-9_\-]+)', [
            'methods'             => 'DELETE',
            'callback'            => [self::class, 'delete_skill'],
            'permission_callback' => $admin,
        ]);

        // RAG
        register_rest_route($ns, '/chatbot/rag/config', [
            ['methods' => 'GET',  'callback' => [self::class, 'get_rag_config'],  'permission_callback' => $admin],
            ['methods' => 'POST', 'callback' => [self::class, 'save_rag_config'], 'permission_callback' => $admin],
        ]);
        register_rest_route($ns, '/chatbot/rag/reindex', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'rag_reindex'],
            'permission_callback' => $admin,
        ]);
        register_rest_route($ns, '/chatbot/rag/status', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'rag_status'],
            'permission_callback' => $admin,
        ]);

        // Test
        register_rest_route($ns, '/chatbot/test-message', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'test_message'],
            'permission_callback' => $admin,
        ]);
    }

    /* ═════════════════════════════════════════════════════════════
       SETTINGS
       ═════════════════════════════════════════════════════════════ */
    public static function get_settings() {
        $opt = (array) get_option(self::OPT_SETTINGS, []);
        return new \WP_REST_Response(self::merge_defaults_settings($opt), 200);
    }

    public static function save_settings(\WP_REST_Request $request) {
        $body = (array) $request->get_json_params();
        $clean = [
            'enabled'           => !empty($body['enabled']),
            'greeting'          => sanitize_text_field((string) ($body['greeting']  ?? 'Xin chào! Mình có thể giúp gì?')),
            'fallback'          => sanitize_text_field((string) ($body['fallback']  ?? 'Câu hỏi thú vị! Để mình chuyển cho team bạn nhé.')),
            'model'             => sanitize_text_field((string) ($body['model']     ?? 'gpt-4o-mini')),
            'temperature'       => max(0.0, min(2.0, (float) ($body['temperature'] ?? 0.6))),
            'max_tokens'        => max(32,  min(8192, (int) ($body['max_tokens']   ?? 400))),
            'system_prompt'     => self::sanitize_textarea_preserve((string) ($body['system_prompt'] ?? '')),
            'handoff_threshold' => max(0.0, min(1.0, (float) ($body['handoff_threshold'] ?? 0.3))),
        ];
        update_option(self::OPT_SETTINGS, $clean, false);
        return new \WP_REST_Response(['success' => true, 'settings' => $clean], 200);
    }

    protected static function merge_defaults_settings(array $opt): array {
        return array_merge([
            'enabled'           => true,
            'greeting'          => 'Xin chào! Mình có thể giúp gì?',
            'fallback'          => 'Câu hỏi thú vị! Để mình chuyển cho team bạn nhé.',
            'model'             => 'gpt-4o-mini',
            'temperature'       => 0.6,
            'max_tokens'        => 400,
            'system_prompt'     => '',
            'handoff_threshold' => 0.3,
        ], $opt);
    }

    /* ═════════════════════════════════════════════════════════════
       KNOWLEDGE BASE
       ═════════════════════════════════════════════════════════════ */
    public static function list_kb(\WP_REST_Request $request) {
        $kb     = self::get_kb();
        $search = trim((string) $request->get_param('search'));
        $tag    = trim((string) $request->get_param('tag'));

        if ($search !== '') {
            $needle = mb_strtolower($search, 'UTF-8');
            $kb = array_values(array_filter($kb, function ($e) use ($needle) {
                $hay = mb_strtolower(($e['question'] ?? '') . ' ' . ($e['answer'] ?? ''), 'UTF-8');
                return str_contains($hay, $needle);
            }));
        }
        if ($tag !== '') {
            $kb = array_values(array_filter($kb, function ($e) use ($tag) {
                return in_array($tag, (array) ($e['tags'] ?? []), true);
            }));
        }

        // Aggregate tag list for filter UI
        $all_tags = [];
        foreach (self::get_kb() as $row) {
            foreach ((array) ($row['tags'] ?? []) as $t) {
                $t = (string) $t;
                if ($t === '') continue;
                $all_tags[$t] = ($all_tags[$t] ?? 0) + 1;
            }
        }
        ksort($all_tags);

        return new \WP_REST_Response([
            'items'    => $kb,
            'total'    => count($kb),
            'all_tags' => $all_tags,
        ], 200);
    }

    public static function save_kb(\WP_REST_Request $request) {
        $body = (array) $request->get_json_params();
        $entry = self::sanitize_kb_entry($body);
        if ($entry === null) {
            return new \WP_Error('invalid', 'Question và answer là bắt buộc.', ['status' => 400]);
        }

        $kb = self::get_kb();
        $found = false;
        foreach ($kb as $i => $row) {
            if (($row['id'] ?? '') === $entry['id']) {
                $entry['created_at'] = $row['created_at'] ?? current_time('mysql');
                $entry['updated_at'] = current_time('mysql');
                $kb[$i] = $entry;
                $found = true;
                break;
            }
        }
        if (!$found) {
            $entry['created_at'] = current_time('mysql');
            $entry['updated_at'] = current_time('mysql');
            $kb[] = $entry;
        }
        update_option(self::OPT_KB, $kb, false);

        return new \WP_REST_Response(['success' => true, 'entry' => $entry], 200);
    }

    public static function delete_kb(\WP_REST_Request $request) {
        $id = (string) $request['id'];
        $kb = self::get_kb();
        $kb = array_values(array_filter($kb, fn($r) => ($r['id'] ?? '') !== $id));
        update_option(self::OPT_KB, $kb, false);
        return new \WP_REST_Response(['success' => true], 200);
    }

    public static function import_csv(\WP_REST_Request $request) {
        // Accept either multipart ($_FILES['file']) or JSON body with csv_text.
        $csv_text = '';

        if (!empty($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
            $csv_text = (string) file_get_contents($_FILES['file']['tmp_name']);
        } else {
            $body = $request->get_json_params();
            if (is_array($body) && isset($body['csv_text'])) {
                $csv_text = (string) $body['csv_text'];
            }
        }

        if ($csv_text === '') {
            return new \WP_Error('no_data', 'Không có dữ liệu CSV.', ['status' => 400]);
        }

        // Strip BOM
        if (substr($csv_text, 0, 3) === "\xEF\xBB\xBF") {
            $csv_text = substr($csv_text, 3);
        }

        $lines = preg_split("/\r\n|\n|\r/", $csv_text);
        $kb    = self::get_kb();
        $added = 0; $updated = 0; $skipped = 0; $errors = 0;
        $header_seen = false;

        foreach ($lines as $line) {
            if (trim($line) === '') continue;
            $fields = str_getcsv($line);

            // Detect + skip header row on first non-empty line.
            if (!$header_seen) {
                $header_seen = true;
                $lower = array_map('strtolower', array_map('trim', $fields));
                if (in_array('question', $lower, true) || in_array('câu hỏi', $lower, true)) {
                    continue; // this was the header
                }
            }

            $question = isset($fields[0]) ? trim((string) $fields[0]) : '';
            $answer   = isset($fields[1]) ? trim((string) $fields[1]) : '';
            $tags_raw = isset($fields[2]) ? trim((string) $fields[2]) : '';
            $priority = isset($fields[3]) ? (int) $fields[3]          : 10;

            if ($question === '' || $answer === '') { $errors++; continue; }

            $tags = array_values(array_filter(array_map('trim', explode('|', $tags_raw))));
            if (empty($tags) && $tags_raw !== '') {
                $tags = array_values(array_filter(array_map('trim', explode(',', $tags_raw))));
            }

            // Upsert by question match.
            $target = null;
            foreach ($kb as $i => $row) {
                if (($row['question'] ?? '') === $question) { $target = $i; break; }
            }

            $entry = self::sanitize_kb_entry([
                'id'       => $target !== null ? ($kb[$target]['id'] ?? self::new_id('kb')) : self::new_id('kb'),
                'question' => $question,
                'answer'   => $answer,
                'tags'     => $tags,
                'priority' => $priority,
            ]);
            if ($entry === null) { $errors++; continue; }

            if ($target !== null) {
                $entry['created_at'] = $kb[$target]['created_at'] ?? current_time('mysql');
                $entry['updated_at'] = current_time('mysql');
                $kb[$target] = $entry;
                $updated++;
            } else {
                $entry['created_at'] = current_time('mysql');
                $entry['updated_at'] = current_time('mysql');
                $kb[] = $entry;
                $added++;
            }
        }

        update_option(self::OPT_KB, $kb, false);

        return new \WP_REST_Response([
            'success' => true,
            'summary' => [
                'added'   => $added,
                'updated' => $updated,
                'skipped' => $skipped,
                'errors'  => $errors,
                'total'   => count($kb),
            ],
        ], 200);
    }

    public static function export_csv(\WP_REST_Request $request) {
        $kb = self::get_kb();

        // Build CSV in memory — str_putcsv helper.
        $lines   = [];
        $lines[] = self::csv_row(['question', 'answer', 'tags', 'priority']);
        foreach ($kb as $row) {
            $tags = implode('|', (array) ($row['tags'] ?? []));
            $lines[] = self::csv_row([
                (string) ($row['question'] ?? ''),
                (string) ($row['answer']   ?? ''),
                $tags,
                (string) (int) ($row['priority'] ?? 10),
            ]);
        }
        $csv = "\xEF\xBB\xBF" . implode("\n", $lines) . "\n";

        // Stream directly as text/csv (avoid WP_REST_Response JSON wrapping).
        nocache_headers();
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="pi-kb.csv"');
        header('Content-Length: ' . strlen($csv));
        echo $csv;
        exit;
    }

    /* ═════════════════════════════════════════════════════════════
       INTENTS
       ═════════════════════════════════════════════════════════════ */
    public static function list_intents() {
        $intents = self::get_intents();
        $skills  = self::get_skills();
        return new \WP_REST_Response([
            'items'    => $intents,
            'total'    => count($intents),
            'skill_ids' => array_values(array_map(fn($s) => ['id' => $s['id'] ?? '', 'name' => $s['name'] ?? ''], $skills)),
        ], 200);
    }

    public static function save_intent(\WP_REST_Request $request) {
        $body = (array) $request->get_json_params();
        $entry = self::sanitize_intent($body);
        if ($entry === null) {
            return new \WP_Error('invalid', 'Name và patterns là bắt buộc.', ['status' => 400]);
        }

        $intents = self::get_intents();
        $found = false;
        foreach ($intents as $i => $row) {
            if (($row['id'] ?? '') === $entry['id']) {
                $intents[$i] = $entry;
                $found = true;
                break;
            }
        }
        if (!$found) {
            $intents[] = $entry;
        }
        update_option(self::OPT_INTENTS, $intents, false);

        return new \WP_REST_Response(['success' => true, 'entry' => $entry], 200);
    }

    public static function delete_intent(\WP_REST_Request $request) {
        $id = (string) $request['id'];
        $intents = self::get_intents();
        $intents = array_values(array_filter($intents, fn($r) => ($r['id'] ?? '') !== $id));
        update_option(self::OPT_INTENTS, $intents, false);
        return new \WP_REST_Response(['success' => true], 200);
    }

    /* ═════════════════════════════════════════════════════════════
       SKILLS
       ═════════════════════════════════════════════════════════════ */
    public static function list_skills() {
        return new \WP_REST_Response([
            'items' => self::get_skills(),
            'total' => count(self::get_skills()),
        ], 200);
    }

    public static function save_skill(\WP_REST_Request $request) {
        $body = (array) $request->get_json_params();
        $entry = self::sanitize_skill($body);
        if ($entry === null) {
            return new \WP_Error('invalid', 'Name và trigger_phrases là bắt buộc.', ['status' => 400]);
        }

        $skills = self::get_skills();
        $found = false;
        foreach ($skills as $i => $row) {
            if (($row['id'] ?? '') === $entry['id']) {
                $skills[$i] = $entry;
                $found = true;
                break;
            }
        }
        if (!$found) {
            $skills[] = $entry;
        }
        update_option(self::OPT_SKILLS, $skills, false);

        return new \WP_REST_Response(['success' => true, 'entry' => $entry], 200);
    }

    public static function delete_skill(\WP_REST_Request $request) {
        $id = (string) $request['id'];
        $skills = self::get_skills();
        $skills = array_values(array_filter($skills, fn($r) => ($r['id'] ?? '') !== $id));
        update_option(self::OPT_SKILLS, $skills, false);
        return new \WP_REST_Response(['success' => true], 200);
    }

    /* ═════════════════════════════════════════════════════════════
       RAG
       ═════════════════════════════════════════════════════════════ */
    public static function get_rag_config() {
        $opt = (array) get_option(self::OPT_RAG, []);
        return new \WP_REST_Response(self::merge_defaults_rag($opt), 200);
    }

    public static function save_rag_config(\WP_REST_Request $request) {
        $body = (array) $request->get_json_params();
        $clean = [
            'enabled'         => !empty($body['enabled']),
            'embedding_model' => sanitize_text_field((string) ($body['embedding_model'] ?? 'text-embedding-3-small')),
            'chunk_size'      => max(50,  min(4000, (int) ($body['chunk_size'] ?? 500))),
            'overlap'         => max(0,   min(500,  (int) ($body['overlap']    ?? 50))),
            'index_status'    => sanitize_text_field((string) ($body['index_status'] ?? 'idle')),
            'last_reindex'    => sanitize_text_field((string) ($body['last_reindex'] ?? '')),
        ];
        update_option(self::OPT_RAG, $clean, false);
        return new \WP_REST_Response(['success' => true, 'config' => $clean], 200);
    }

    protected static function merge_defaults_rag(array $opt): array {
        return array_merge([
            'enabled'         => false,
            'embedding_model' => 'text-embedding-3-small',
            'chunk_size'      => 500,
            'overlap'         => 50,
            'index_status'    => 'idle',
            'last_reindex'    => '',
        ], $opt);
    }

    public static function rag_reindex(\WP_REST_Request $request) {
        // Simple in-process indexer: chunk the KB entries in batches of 10,
        // updating the status option between batches. A real deployment would
        // fan this out via Action Scheduler / WP-Cron; keeping it sync-friendly
        // so the React page can show progress without extra infra.
        $kb       = self::get_kb();
        $total    = count($kb);
        $chunk    = 10;
        $progress = 0.0;

        update_option(self::OPT_RAG_STAT, [
            'state'      => 'running',
            'progress'   => 0.0,
            'total'      => $total,
            'processed'  => 0,
            'started_at' => current_time('mysql'),
        ], false);

        if ($total === 0) {
            $done = [
                'state'       => 'done',
                'progress'    => 1.0,
                'total'       => 0,
                'processed'   => 0,
                'started_at'  => current_time('mysql'),
                'finished_at' => current_time('mysql'),
            ];
            update_option(self::OPT_RAG_STAT, $done, false);

            $cfg = (array) get_option(self::OPT_RAG, []);
            $cfg['index_status'] = 'done';
            $cfg['last_reindex'] = current_time('mysql');
            update_option(self::OPT_RAG, $cfg, false);

            return new \WP_REST_Response(['success' => true, 'status' => $done], 200);
        }

        // Process in chunks — each chunk recomputes progress.
        // (Actual embedding work would be delegated to Pi Backend; here we just
        // "touch" each entry so the wiring is present and the UI can animate.)
        $processed = 0;
        for ($offset = 0; $offset < $total; $offset += $chunk) {
            $slice = array_slice($kb, $offset, $chunk);
            foreach ($slice as $row) {
                // Hook for backend embedding — plugins/theme can subscribe.
                do_action('pi_chatbot_rag_index_entry', $row);
                $processed++;
            }
            $progress = $total > 0 ? min(1.0, $processed / $total) : 1.0;
            update_option(self::OPT_RAG_STAT, [
                'state'      => $processed >= $total ? 'done' : 'running',
                'progress'   => $progress,
                'total'      => $total,
                'processed'  => $processed,
                'started_at' => current_time('mysql'),
            ], false);
        }

        $done = [
            'state'       => 'done',
            'progress'    => 1.0,
            'total'       => $total,
            'processed'   => $processed,
            'started_at'  => current_time('mysql'),
            'finished_at' => current_time('mysql'),
        ];
        update_option(self::OPT_RAG_STAT, $done, false);

        $cfg = (array) get_option(self::OPT_RAG, []);
        $cfg['index_status'] = 'done';
        $cfg['last_reindex'] = current_time('mysql');
        update_option(self::OPT_RAG, $cfg, false);

        return new \WP_REST_Response(['success' => true, 'status' => $done], 200);
    }

    public static function rag_status() {
        $stat = (array) get_option(self::OPT_RAG_STAT, []);
        if (empty($stat)) {
            $stat = ['state' => 'idle', 'progress' => 0.0];
        }
        return new \WP_REST_Response($stat, 200);
    }

    /* ═════════════════════════════════════════════════════════════
       TEST MESSAGE — probe rules without live chat
       ═════════════════════════════════════════════════════════════ */
    public static function test_message(\WP_REST_Request $request) {
        $body         = (array) $request->get_json_params();
        $message      = trim((string) ($body['message'] ?? ''));
        $test_intents = !isset($body['test_intents']) || !empty($body['test_intents']);
        $test_rag     = !isset($body['test_rag'])     || !empty($body['test_rag']);

        if ($message === '') {
            return new \WP_Error('empty', 'Tin nhắn không được để trống.', ['status' => 400]);
        }

        $result = [
            'message'         => $message,
            'detected_intent' => null,
            'intent_scores'   => [],
            'kb_matches'      => [],
            'skill'           => null,
            'response'        => null,
            'source'          => 'fallback',
        ];

        // 1) Intent match — score each rule's patterns via similar_text.
        if ($test_intents) {
            $intents  = self::get_intents();
            $settings = self::merge_defaults_settings((array) get_option(self::OPT_SETTINGS, []));
            $scores   = [];
            $best     = null;
            $best_score = 0.0;

            foreach ($intents as $intent) {
                $patterns  = (array) ($intent['patterns'] ?? []);
                $threshold = (float) ($intent['confidence_threshold'] ?? $settings['handoff_threshold']);
                $peak      = 0.0;

                foreach ($patterns as $p) {
                    $p = (string) $p;
                    if ($p === '') continue;
                    similar_text(
                        mb_strtolower($message, 'UTF-8'),
                        mb_strtolower($p, 'UTF-8'),
                        $percent
                    );
                    $score = $percent / 100.0;
                    if ($score > $peak) $peak = $score;
                }

                $scores[] = [
                    'id'         => (string) ($intent['id'] ?? ''),
                    'name'       => (string) ($intent['name'] ?? ''),
                    'confidence' => round($peak, 3),
                    'threshold'  => round($threshold, 3),
                    'matched'    => $peak >= $threshold,
                ];

                if ($peak > $best_score && $peak >= $threshold) {
                    $best_score = $peak;
                    $best       = $intent;
                }
            }

            usort($scores, fn($a, $b) => $b['confidence'] <=> $a['confidence']);
            $result['intent_scores']   = $scores;
            $result['detected_intent'] = $best ? [
                'id'         => (string) ($best['id'] ?? ''),
                'name'       => (string) ($best['name'] ?? ''),
                'confidence' => round($best_score, 3),
                'response'   => (string) ($best['response_template'] ?? ''),
                'skill'      => (string) ($best['skill'] ?? ''),
            ] : null;

            if ($best) {
                $result['response'] = (string) ($best['response_template'] ?? '');
                $result['source']   = 'intent';

                if (!empty($best['skill'])) {
                    $skills = self::get_skills();
                    foreach ($skills as $s) {
                        if (($s['id'] ?? '') === $best['skill']) {
                            $result['skill'] = [
                                'id'          => (string) ($s['id'] ?? ''),
                                'name'        => (string) ($s['name'] ?? ''),
                                'handler'     => (string) ($s['handler'] ?? 'builtin'),
                                'config'      => (array)  ($s['config'] ?? []),
                            ];
                            break;
                        }
                    }
                }
            }
        }

        // 2) KB match — simple case-insensitive search across question+answer.
        if ($test_rag) {
            $kb      = self::get_kb();
            $needle  = mb_strtolower($message, 'UTF-8');
            $matches = [];
            foreach ($kb as $row) {
                $q = mb_strtolower((string) ($row['question'] ?? ''), 'UTF-8');
                $a = mb_strtolower((string) ($row['answer']   ?? ''), 'UTF-8');
                similar_text($needle, $q, $pq);
                similar_text($needle, $a, $pa);
                $score = max($pq, $pa) / 100.0;

                // Also accept direct substring hits to catch short queries.
                $hit = ($pq >= 30.0 || $pa >= 30.0)
                    || str_contains($q . ' ' . $a, $needle);
                if ($hit) {
                    $matches[] = [
                        'id'       => (string) ($row['id']       ?? ''),
                        'question' => (string) ($row['question'] ?? ''),
                        'answer'   => (string) ($row['answer']   ?? ''),
                        'tags'     => (array)  ($row['tags']     ?? []),
                        'priority' => (int)    ($row['priority'] ?? 10),
                        'score'    => round($score, 3),
                    ];
                }
            }
            usort($matches, function ($a, $b) {
                if ($a['priority'] !== $b['priority']) return $b['priority'] <=> $a['priority'];
                return $b['score'] <=> $a['score'];
            });
            $result['kb_matches'] = array_slice($matches, 0, 5);

            // If no intent fired, promote the top KB hit as the fallback reply.
            if ($result['response'] === null && !empty($result['kb_matches'])) {
                $result['response'] = $result['kb_matches'][0]['answer'];
                $result['source']   = 'kb';
            }
        }

        if ($result['response'] === null) {
            $settings = self::merge_defaults_settings((array) get_option(self::OPT_SETTINGS, []));
            $result['response'] = $settings['fallback'];
            $result['source']   = 'fallback';
        }

        return new \WP_REST_Response($result, 200);
    }

    /* ═════════════════════════════════════════════════════════════
       HELPERS
       ═════════════════════════════════════════════════════════════ */
    protected static function get_kb(): array {
        $raw = get_option(self::OPT_KB, []);
        return is_array($raw) ? array_values($raw) : [];
    }
    protected static function get_intents(): array {
        $raw = get_option(self::OPT_INTENTS, []);
        return is_array($raw) ? array_values($raw) : [];
    }
    protected static function get_skills(): array {
        $raw = get_option(self::OPT_SKILLS, []);
        return is_array($raw) ? array_values($raw) : [];
    }

    protected static function new_id(string $prefix): string {
        return $prefix . '_' . substr(md5(uniqid('', true) . wp_rand()), 0, 10);
    }

    protected static function sanitize_kb_entry(array $body): ?array {
        $question = trim((string) ($body['question'] ?? ''));
        $answer   = trim((string) ($body['answer']   ?? ''));
        if ($question === '' || $answer === '') return null;

        $tags_raw = (array) ($body['tags'] ?? []);
        $tags     = [];
        foreach ($tags_raw as $t) {
            $t = sanitize_text_field((string) $t);
            if ($t !== '') $tags[] = $t;
        }

        return [
            'id'       => sanitize_key((string) ($body['id'] ?? '')) ?: self::new_id('kb'),
            'question' => sanitize_text_field($question),
            'answer'   => self::sanitize_textarea_preserve($answer),
            'tags'     => array_values(array_unique($tags)),
            'priority' => max(0, min(100, (int) ($body['priority'] ?? 10))),
        ];
    }

    protected static function sanitize_intent(array $body): ?array {
        $name = trim((string) ($body['name'] ?? ''));
        $patterns_raw = (array) ($body['patterns'] ?? []);
        $patterns = [];
        foreach ($patterns_raw as $p) {
            $p = trim(sanitize_text_field((string) $p));
            if ($p !== '') $patterns[] = $p;
        }
        if ($name === '' || empty($patterns)) return null;

        $skill = sanitize_key((string) ($body['skill'] ?? ''));

        return [
            'id'                   => sanitize_key((string) ($body['id'] ?? '')) ?: self::new_id('in'),
            'name'                 => $name,
            'patterns'             => $patterns,
            'response_template'    => self::sanitize_textarea_preserve((string) ($body['response_template'] ?? '')),
            'confidence_threshold' => max(0.0, min(1.0, (float) ($body['confidence_threshold'] ?? 0.3))),
            'skill'                => $skill,
        ];
    }

    protected static function sanitize_skill(array $body): ?array {
        $name = trim((string) ($body['name'] ?? ''));
        $triggers_raw = (array) ($body['trigger_phrases'] ?? []);
        $triggers = [];
        foreach ($triggers_raw as $t) {
            $t = trim(sanitize_text_field((string) $t));
            if ($t !== '') $triggers[] = $t;
        }
        if ($name === '' || empty($triggers)) return null;

        $handler = (string) ($body['handler'] ?? 'builtin');
        if (!in_array($handler, ['builtin', 'webhook'], true)) $handler = 'builtin';

        $config_raw = $body['config'] ?? [];
        if (is_string($config_raw)) {
            $decoded = json_decode($config_raw, true);
            $config_raw = is_array($decoded) ? $decoded : [];
        }
        $config = is_array($config_raw) ? $config_raw : [];

        $clean_config = [];
        if ($handler === 'webhook') {
            $url = isset($config['url']) ? esc_url_raw((string) $config['url']) : '';
            $clean_config['url'] = $url;
            $params = $config['params'] ?? [];
            if (is_string($params)) {
                $decoded = json_decode($params, true);
                $params = is_array($decoded) ? $decoded : [];
            }
            $clean_config['params'] = is_array($params) ? $params : [];
        } else {
            // Keep arbitrary JSON for builtin handlers so admins can stash
            // per-skill knobs without schema changes.
            $clean_config = $config;
        }

        return [
            'id'              => sanitize_key((string) ($body['id'] ?? '')) ?: self::new_id('sk'),
            'name'            => $name,
            'description'     => sanitize_text_field((string) ($body['description'] ?? '')),
            'trigger_phrases' => $triggers,
            'handler'         => $handler,
            'config'          => $clean_config,
        ];
    }

    protected static function sanitize_textarea_preserve(string $s): string {
        // Keep newlines and basic formatting, strip tags. wp_check_invalid_utf8
        // avoids corrupt bytes poisoning the option blob.
        $s = wp_check_invalid_utf8($s);
        $s = wp_kses_post($s);
        $s = str_replace(["\r\n", "\r"], "\n", $s);
        return trim($s);
    }

    protected static function csv_row(array $cells): string {
        $out = [];
        foreach ($cells as $c) {
            $c = (string) $c;
            $needs_quote = (strpbrk($c, ",\"\n\r") !== false);
            $c = str_replace('"', '""', $c);
            $out[] = $needs_quote ? '"' . $c . '"' : $c;
        }
        return implode(',', $out);
    }
}
