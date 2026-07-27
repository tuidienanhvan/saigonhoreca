<?php
/**
 * Pi API Leads Endpoints
 *
 * Core endpoints:
 * - GET  /leads                       List leads (raw table)
 * - GET  /leads/pipeline              Kanban columns grouped by status
 * - POST /leads/{id}                  Update status of one lead
 *
 * Scoring endpoints (Batch 3.4):
 * - GET    /leads/scoring/rules
 * - POST   /leads/scoring/rules
 * - DELETE /leads/scoring/rules/{id}
 * - GET    /leads/scoring/ai-config
 * - POST   /leads/scoring/ai-config
 * - POST   /leads/scoring/rescore     Body { lead_ids?: [], all?: bool }
 * - GET    /leads/scoring/status
 * - GET    /leads/scoring/preview/{lead_id}
 *
 * Enrichment endpoints (Batch 3.4):
 * - GET    /leads/enrichment/rules
 * - POST   /leads/enrichment/rules
 * - DELETE /leads/enrichment/rules/{id}
 * - POST   /leads/enrichment/enrich   Body { lead_ids: [], source: 'clearbit|hunter|manual' }
 * - GET    /leads/enrichment/status
 */

if (!defined('ABSPATH')) exit;

class Pi_Api_Leads {

    /* ═══════════════════════════════════════════════════════════════
       ROUTE REGISTRATION
       ═══════════════════════════════════════════════════════════════ */
    public static function register_routes($ns, $admin) {
        /* Core */
        register_rest_route($ns, '/leads', [
            'methods'  => 'GET',
            'callback' => [self::class, 'get_leads'],
            'permission_callback' => $admin
        ]);
        register_rest_route($ns, '/leads/pipeline', [
            'methods'  => 'GET',
            'callback' => [self::class, 'get_pipeline'],
            'permission_callback' => $admin
        ]);
        register_rest_route($ns, '/leads/(?P<id>\d+)', [
            'methods'  => 'POST',
            'callback' => [self::class, 'update_lead'],
            'permission_callback' => $admin
        ]);

        /* Scoring — rules */
        register_rest_route($ns, '/leads/scoring/rules', [
            ['methods' => 'GET',  'callback' => [self::class, 'get_scoring_rules'],  'permission_callback' => $admin],
            ['methods' => 'POST', 'callback' => [self::class, 'save_scoring_rules'], 'permission_callback' => $admin],
        ]);
        register_rest_route($ns, '/leads/scoring/rules/(?P<id>[A-Za-z0-9_\-]+)', [
            'methods'  => 'DELETE',
            'callback' => [self::class, 'delete_scoring_rule'],
            'permission_callback' => $admin
        ]);

        /* Scoring — AI config */
        register_rest_route($ns, '/leads/scoring/ai-config', [
            ['methods' => 'GET',  'callback' => [self::class, 'get_scoring_ai_config'],  'permission_callback' => $admin],
            ['methods' => 'POST', 'callback' => [self::class, 'save_scoring_ai_config'], 'permission_callback' => $admin],
        ]);

        /* Scoring — actions + stats */
        register_rest_route($ns, '/leads/scoring/rescore', [
            'methods'  => 'POST',
            'callback' => [self::class, 'rescore_leads'],
            'permission_callback' => $admin
        ]);
        register_rest_route($ns, '/leads/scoring/status', [
            'methods'  => 'GET',
            'callback' => [self::class, 'get_scoring_status'],
            'permission_callback' => $admin
        ]);
        register_rest_route($ns, '/leads/scoring/preview/(?P<id>\d+)', [
            'methods'  => 'GET',
            'callback' => [self::class, 'scoring_preview'],
            'permission_callback' => $admin
        ]);

        /* Enrichment — rules */
        register_rest_route($ns, '/leads/enrichment/rules', [
            ['methods' => 'GET',  'callback' => [self::class, 'get_enrichment_rules'],  'permission_callback' => $admin],
            ['methods' => 'POST', 'callback' => [self::class, 'save_enrichment_rules'], 'permission_callback' => $admin],
        ]);
        register_rest_route($ns, '/leads/enrichment/rules/(?P<id>[A-Za-z0-9_\-]+)', [
            'methods'  => 'DELETE',
            'callback' => [self::class, 'delete_enrichment_rule'],
            'permission_callback' => $admin
        ]);

        /* Enrichment — actions + stats */
        register_rest_route($ns, '/leads/enrichment/enrich', [
            'methods'  => 'POST',
            'callback' => [self::class, 'enrich_leads'],
            'permission_callback' => $admin
        ]);
        register_rest_route($ns, '/leads/enrichment/status', [
            'methods'  => 'GET',
            'callback' => [self::class, 'get_enrichment_status'],
            'permission_callback' => $admin
        ]);

        /* Enrichment — credits + bulk + single (Phase 2 task C) */
        register_rest_route($ns, '/leads/enrichment/credits', [
            'methods'  => 'GET',
            'callback' => [self::class, 'get_enrichment_credits'],
            'permission_callback' => $admin
        ]);
        register_rest_route($ns, '/leads/enrich-bulk', [
            'methods'  => 'POST',
            'callback' => [self::class, 'enrich_bulk'],
            'permission_callback' => $admin
        ]);
        register_rest_route($ns, '/leads/(?P<id>\d+)/enrich-now', [
            'methods'  => 'POST',
            'callback' => [self::class, 'enrich_now'],
            'permission_callback' => $admin
        ]);

        /* Activity timeline / notes / attachments / reminders (Phase 2 task C) */
        register_rest_route($ns, '/leads/(?P<id>\d+)/timeline', [
            'methods'  => 'GET',
            'callback' => [self::class, 'get_timeline'],
            'permission_callback' => $admin
        ]);
        register_rest_route($ns, '/leads/(?P<id>\d+)/notes', [
            'methods'  => 'POST',
            'callback' => [self::class, 'add_note'],
            'permission_callback' => $admin
        ]);
        register_rest_route($ns, '/leads/(?P<id>\d+)/notes/(?P<note_id>\d+)', [
            'methods'  => 'DELETE',
            'callback' => [self::class, 'delete_note'],
            'permission_callback' => $admin
        ]);
        register_rest_route($ns, '/leads/(?P<id>\d+)/attachments', [
            'methods'  => 'POST',
            'callback' => [self::class, 'add_attachment'],
            'permission_callback' => $admin
        ]);
        register_rest_route($ns, '/leads/(?P<id>\d+)/reminders', [
            'methods'  => 'POST',
            'callback' => [self::class, 'add_reminder'],
            'permission_callback' => $admin
        ]);
    }

    /* ═══════════════════════════════════════════════════════════════
       CORE — list / pipeline / update
       ═══════════════════════════════════════════════════════════════ */
    public static function get_leads() {
        global $wpdb;
        $table = $wpdb->prefix . 'pi_leads';
        $db_rows = [];
        if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table)) === $table) {
            $db_rows = (array) $wpdb->get_results("SELECT * FROM $table ORDER BY created_at DESC LIMIT 500");
        }

        // Attach score from the matching CPT (if a sh_lead post exists for the same email/phone).
        // Fallback: expose meta columns directly if already present.
        foreach ($db_rows as $i => $row) {
            // Row is stdClass; copy to array for easier handling
            $arr = is_object($row) ? (array) $row : (array) $row;
            if (!isset($arr['score']) || $arr['score'] === null || $arr['score'] === '') {
                $arr['score'] = self::lookup_score_for_contact($arr['email'] ?? '', $arr['phone'] ?? '');
            }
            $db_rows[$i] = (object) $arr;
        }

        // If the pi_leads table is empty, also expose sh_lead CPT posts so the
        // UI has something to show. This keeps existing behavior backward-compatible.
        if (empty($db_rows)) {
            $cpt = get_posts([
                'post_type'      => 'sh_lead',
                'post_status'    => 'publish',
                'posts_per_page' => 200,
                'orderby'        => 'date',
                'order'          => 'DESC',
            ]);
            foreach ($cpt as $p) {
                $db_rows[] = (object) [
                    'id'         => $p->ID,
                    'name'       => get_post_meta($p->ID, '_pi_lead_name', true) ?: $p->post_title,
                    'phone'      => get_post_meta($p->ID, '_pi_lead_phone', true),
                    'email'      => get_post_meta($p->ID, '_pi_lead_email', true),
                    'message'    => $p->post_content,
                    'source'     => get_post_meta($p->ID, '_pi_lead_source', true) ?: 'Direct',
                    'created_at' => $p->post_date,
                    'score'      => (int) get_post_meta($p->ID, '_pi_lead_score', true),
                ];
            }
        }

        return new \WP_REST_Response($db_rows, 200);
    }

    public static function get_pipeline(\WP_REST_Request $request) {
        $statuses  = ['new' => 'Mới', 'contacted' => 'Đã liên hệ', 'quoted' => 'Đã báo giá', 'won' => 'Thành công', 'lost' => 'Thất bại'];
        $filter    = $request->get_param('status') ?: 'all';
        $page      = max(1, (int) ($request->get_param('page') ?: 1));
        $per_page  = min(200, max(1, (int) ($request->get_param('per_page') ?: 50)));

        $columns = [];
        $total = 0;

        foreach ($statuses as $s => $label) {
            if ($filter !== 'all' && $filter !== $s) continue;

            $query_args = [
                'post_type'      => 'sh_lead',
                'post_status'    => 'publish',
                'posts_per_page' => $per_page,
                'paged'          => $page,
                'orderby'        => 'date',
                'order'          => 'DESC',
            ];

            if ($s === 'new') {
                $query_args['meta_query'] = [
                    'relation' => 'OR',
                    ['key' => '_pi_lead_status', 'value' => $s],
                    ['key' => '_pi_lead_status', 'compare' => 'NOT EXISTS'],
                    ['key' => '_pi_lead_status', 'value' => '', 'compare' => '=']
                ];
            } else {
                $query_args['meta_query'] = [
                    ['key' => '_pi_lead_status', 'value' => $s]
                ];
            }

            $query = new \WP_Query($query_args);
            $count = $query->found_posts;
            $total += $count;

            $leads = [];
            foreach ($query->posts as $p) {
                $leads[] = [
                    'id'         => $p->ID,
                    'name'       => get_post_meta($p->ID, '_pi_lead_name', true) ?: $p->post_title,
                    'phone'      => get_post_meta($p->ID, '_pi_lead_phone', true),
                    'service'    => get_post_meta($p->ID, '_pi_lead_service', true),
                    'score'      => (int) get_post_meta($p->ID, '_pi_lead_score', true),
                    'created_at' => $p->post_date,
                    'status'     => $s,
                ];
            }

            $columns[] = [
                'status' => $s,
                'label'  => $label,
                'count'  => $count,
                'leads'  => $leads,
            ];
        }

        return new \WP_REST_Response([
            'columns' => $columns,
            'total'   => $total,
        ], 200);
    }

    public static function update_lead(\WP_REST_Request $request) {
        $id     = (int) $request->get_param('id');
        $params = $request->get_json_params();

        if (get_post_type($id) !== 'sh_lead') {
            return new \WP_REST_Response(['message' => 'Lead not found'], 404);
        }

        if (isset($params['status'])) {
            $allowed = ['new', 'contacted', 'quoted', 'won', 'lost'];
            if (in_array($params['status'], $allowed, true)) {
                update_post_meta($id, '_pi_lead_status', $params['status']);
            }
        }

        return new \WP_REST_Response(['success' => true], 200);
    }

    /* ═══════════════════════════════════════════════════════════════
       SCORING — rules CRUD
       ═══════════════════════════════════════════════════════════════ */
    public static function get_scoring_rules() {
        $rules = self::read_rules_option('pi_leads_scoring_rules');
        return new \WP_REST_Response(['rules' => $rules], 200);
    }

    public static function save_scoring_rules(\WP_REST_Request $request) {
        $params = $request->get_json_params();
        $incoming = isset($params['rule']) && is_array($params['rule']) ? [$params['rule']] :
                    (isset($params['rules']) && is_array($params['rules']) ? $params['rules'] : []);

        if (empty($incoming)) {
            return new \WP_REST_Response(['success' => false, 'message' => 'No rule provided'], 400);
        }

        $rules = self::read_rules_option('pi_leads_scoring_rules');
        foreach ($incoming as $raw) {
            $clean = self::sanitize_scoring_rule($raw);
            if (!$clean) continue;
            $idx = self::find_rule_index($rules, $clean['id']);
            if ($idx === -1) {
                $rules[] = $clean;
            } else {
                $rules[$idx] = $clean;
            }
        }
        update_option('pi_leads_scoring_rules', $rules, false);
        return new \WP_REST_Response(['success' => true, 'rules' => $rules], 200);
    }

    public static function delete_scoring_rule(\WP_REST_Request $request) {
        $id = (string) $request->get_param('id');
        $rules = self::read_rules_option('pi_leads_scoring_rules');
        $rules = array_values(array_filter($rules, function ($r) use ($id) {
            return (string) ($r['id'] ?? '') !== $id;
        }));
        update_option('pi_leads_scoring_rules', $rules, false);
        return new \WP_REST_Response(['success' => true, 'rules' => $rules], 200);
    }

    /* ═══════════════════════════════════════════════════════════════
       SCORING — AI config
       ═══════════════════════════════════════════════════════════════ */
    public static function get_scoring_ai_config() {
        $cfg = self::read_ai_config();
        return new \WP_REST_Response($cfg, 200);
    }

    public static function save_scoring_ai_config(\WP_REST_Request $request) {
        $p = $request->get_json_params();
        if (!is_array($p)) $p = [];
        $cfg = [
            'enabled'         => !empty($p['enabled']),
            'provider'        => sanitize_key((string) ($p['provider'] ?? 'openai')),
            'model'           => sanitize_text_field((string) ($p['model'] ?? 'gpt-4o-mini')),
            'prompt_template' => (string) ($p['prompt_template'] ?? self::default_prompt_template()),
            'score_weight'    => max(0.0, min(1.0, (float) ($p['score_weight'] ?? 0.5))),
        ];
        update_option('pi_leads_scoring_ai_config', $cfg, false);
        return new \WP_REST_Response(['success' => true, 'config' => $cfg], 200);
    }

    /* ═══════════════════════════════════════════════════════════════
       SCORING — rescore action
       ═══════════════════════════════════════════════════════════════ */
    public static function rescore_leads(\WP_REST_Request $request) {
        $p = $request->get_json_params();
        $all = !empty($p['all']);
        $ids = isset($p['lead_ids']) && is_array($p['lead_ids']) ? array_map('intval', $p['lead_ids']) : [];

        if ($all) {
            $ids = get_posts([
                'post_type'      => 'sh_lead',
                'post_status'    => 'publish',
                'posts_per_page' => -1,
                'fields'         => 'ids',
            ]);
        }
        $ids = array_values(array_filter(array_map('intval', $ids)));
        if (empty($ids)) {
            return new \WP_REST_Response(['success' => false, 'message' => 'Không có lead nào để chấm điểm'], 400);
        }

        $rules = self::read_rules_option('pi_leads_scoring_rules');
        $ai_cfg = self::read_ai_config();

        $scored = 0;
        $sum = 0;
        $errors = [];
        foreach ($ids as $id) {
            if (get_post_type($id) !== 'sh_lead') { $errors[] = $id; continue; }
            $result = self::score_single_lead($id, $rules, $ai_cfg);
            if (!empty($result['ok'])) {
                $scored++;
                $sum += (int) $result['score'];
            } else {
                $errors[] = $id;
            }
        }

        update_option('pi_leads_scoring_last_rescore', gmdate('c'), false);

        $avg = $scored > 0 ? (int) round($sum / $scored) : 0;
        return new \WP_REST_Response([
            'success'    => true,
            'total'      => count($ids),
            'scored'     => $scored,
            'avg_score'  => $avg,
            'errors'     => $errors,
        ], 200);
    }

    public static function get_scoring_status() {
        global $wpdb;
        $row = $wpdb->get_row("
            SELECT COUNT(pm.post_id) AS total_scored,
                   AVG(CAST(pm.meta_value AS UNSIGNED)) AS avg_score
            FROM {$wpdb->postmeta} pm
            INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
            WHERE pm.meta_key = '_pi_lead_score'
              AND p.post_type = 'sh_lead'
              AND p.post_status = 'publish'
              AND pm.meta_value != ''
              AND CAST(pm.meta_value AS UNSIGNED) > 0
        ");
        $total_scored = $row ? (int) $row->total_scored : 0;
        $avg_score    = $row ? (int) round((float) $row->avg_score) : 0;

        // Distribution buckets (0-40, 41-70, 71-100)
        $dist = [
            'low'    => 0, // 0-40
            'medium' => 0, // 41-70
            'high'   => 0, // 71-100
        ];
        $all_scores = $wpdb->get_col("
            SELECT CAST(pm.meta_value AS UNSIGNED) AS score
            FROM {$wpdb->postmeta} pm
            INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
            WHERE pm.meta_key = '_pi_lead_score'
              AND p.post_type = 'sh_lead'
              AND p.post_status = 'publish'
              AND pm.meta_value != ''
        ");
        foreach ((array) $all_scores as $s) {
            $s = (int) $s;
            if ($s <= 40) $dist['low']++;
            elseif ($s <= 70) $dist['medium']++;
            else $dist['high']++;
        }

        $total_leads = (int) wp_count_posts('sh_lead')->publish;

        return new \WP_REST_Response([
            'total_leads'    => $total_leads,
            'total_scored'   => $total_scored,
            'avg_score'      => $avg_score,
            'distribution'   => $dist,
            'last_rescore'   => get_option('pi_leads_scoring_last_rescore', ''),
            'queue_status'   => 'idle',
            'ai_enabled'     => !empty(self::read_ai_config()['enabled']),
            'rule_count'     => count(self::read_rules_option('pi_leads_scoring_rules')),
        ], 200);
    }

    public static function scoring_preview(\WP_REST_Request $request) {
        $id = (int) $request->get_param('id');
        if (get_post_type($id) !== 'sh_lead') {
            return new \WP_REST_Response(['message' => 'Lead không tồn tại'], 404);
        }

        $rules = self::read_rules_option('pi_leads_scoring_rules');
        $ai_cfg = self::read_ai_config();
        $lead = self::collect_lead_data($id);
        $breakdown = self::evaluate_rules($lead, $rules);

        $ai_out = null;
        if (!empty($ai_cfg['enabled'])) {
            $ai_out = self::call_ai_scorer($lead, $ai_cfg);
        }

        $final = self::blend_scores($breakdown['score'], $ai_out, $ai_cfg);

        return new \WP_REST_Response([
            'lead'      => [
                'id'      => $id,
                'name'    => $lead['name'],
                'email'   => $lead['email'],
                'phone'   => $lead['phone'],
                'service' => $lead['service'],
            ],
            'rules_score'      => $breakdown['score'],
            'rules_matches'    => $breakdown['matches'],
            'ai'               => $ai_out,
            'final_score'      => $final,
            'stored_score'     => (int) get_post_meta($id, '_pi_lead_score', true),
            'scored_at'        => (string) get_post_meta($id, '_pi_lead_scored_at', true),
            'breakdown_stored' => json_decode((string) get_post_meta($id, '_pi_lead_score_breakdown', true), true),
        ], 200);
    }

    /* ═══════════════════════════════════════════════════════════════
       ENRICHMENT — rules CRUD
       ═══════════════════════════════════════════════════════════════ */
    public static function get_enrichment_rules() {
        $rules = self::read_rules_option('pi_leads_enrichment_rules');
        // Redact api_key for safety
        $redacted = array_map(function ($r) {
            if (!empty($r['api_key'])) {
                $k = (string) $r['api_key'];
                $r['api_key_masked'] = strlen($k) > 8 ? substr($k, 0, 4) . '...' . substr($k, -4) : '***';
                $r['has_api_key']    = true;
                $r['api_key']        = ''; // never send back
            } else {
                $r['has_api_key'] = false;
            }
            return $r;
        }, $rules);
        return new \WP_REST_Response(['rules' => $redacted], 200);
    }

    public static function save_enrichment_rules(\WP_REST_Request $request) {
        $params = $request->get_json_params();
        $incoming = isset($params['rule']) && is_array($params['rule']) ? [$params['rule']] :
                    (isset($params['rules']) && is_array($params['rules']) ? $params['rules'] : []);

        if (empty($incoming)) {
            return new \WP_REST_Response(['success' => false, 'message' => 'No rule provided'], 400);
        }
        $rules = self::read_rules_option('pi_leads_enrichment_rules');
        foreach ($incoming as $raw) {
            $clean = self::sanitize_enrichment_rule($raw, $rules);
            if (!$clean) continue;
            $idx = self::find_rule_index($rules, $clean['id']);
            if ($idx === -1) {
                $rules[] = $clean;
            } else {
                $rules[$idx] = $clean;
            }
        }
        update_option('pi_leads_enrichment_rules', $rules, false);
        return new \WP_REST_Response(['success' => true, 'rules' => array_map(function ($r) {
            $r['has_api_key'] = !empty($r['api_key']);
            $r['api_key'] = '';
            return $r;
        }, $rules)], 200);
    }

    public static function delete_enrichment_rule(\WP_REST_Request $request) {
        $id = (string) $request->get_param('id');
        $rules = self::read_rules_option('pi_leads_enrichment_rules');
        $rules = array_values(array_filter($rules, function ($r) use ($id) {
            return (string) ($r['id'] ?? '') !== $id;
        }));
        update_option('pi_leads_enrichment_rules', $rules, false);
        return new \WP_REST_Response(['success' => true], 200);
    }

    /* ═══════════════════════════════════════════════════════════════
       ENRICHMENT — action + stats
       ═══════════════════════════════════════════════════════════════ */
    public static function enrich_leads(\WP_REST_Request $request) {
        $p = $request->get_json_params();
        $ids = isset($p['lead_ids']) && is_array($p['lead_ids']) ? array_map('intval', $p['lead_ids']) : [];
        $source = sanitize_key((string) ($p['source'] ?? 'manual'));
        if (!in_array($source, ['clearbit', 'hunter', 'manual'], true)) {
            $source = 'manual';
        }
        $ids = array_values(array_filter($ids));
        if (empty($ids)) {
            return new \WP_REST_Response(['success' => false, 'message' => 'Thiếu lead_ids'], 400);
        }

        // Find a matching rule for this source to grab API key
        $rules = self::read_rules_option('pi_leads_enrichment_rules');
        $rule  = null;
        foreach ($rules as $r) {
            if (($r['source'] ?? '') === $source) { $rule = $r; break; }
        }
        $api_key = $rule['api_key'] ?? '';

        $enriched = 0;
        $results = [];
        $errors = [];

        foreach ($ids as $id) {
            if (get_post_type($id) !== 'sh_lead') { $errors[] = $id; continue; }
            $email = (string) get_post_meta($id, '_pi_lead_email', true);
            $name  = get_the_title($id) ?: (string) get_post_meta($id, '_pi_lead_name', true);

            $out = ['id' => $id, 'email' => $email, 'source' => $source, 'success' => false];

            if ($source === 'manual') {
                update_post_meta($id, '_pi_lead_enrichment_status', 'pending_review');
                update_post_meta($id, '_pi_lead_enriched_at', gmdate('c'));
                $out['success'] = true;
                $out['note']    = 'Đã đánh dấu chờ xem xét thủ công';
                $enriched++;
            } elseif ($source === 'clearbit') {
                if (empty($email) || !is_email($email)) {
                    $out['message'] = 'Lead không có email hợp lệ';
                } elseif (empty($api_key)) {
                    $out['message'] = 'Chưa cấu hình Clearbit API key';
                } else {
                    $data = self::call_clearbit($email, $api_key);
                    if (!empty($data['success'])) {
                        self::apply_enrichment_mapping($id, $data['payload'], $rule['field_mapping'] ?? self::default_clearbit_mapping());
                        update_post_meta($id, '_pi_lead_enrichment_source', 'clearbit');
                        update_post_meta($id, '_pi_lead_enriched_at', gmdate('c'));
                        $out['success'] = true;
                        $out['fields']  = array_keys($data['payload']);
                        $enriched++;
                    } else {
                        $out['message'] = $data['message'] ?? 'Clearbit error';
                    }
                }
            } elseif ($source === 'hunter') {
                if (empty($email)) {
                    // Hunter email-finder can work with domain + name only
                    $out['message'] = 'Thiếu email để chạy Hunter';
                } elseif (empty($api_key)) {
                    $out['message'] = 'Chưa cấu hình Hunter API key';
                } else {
                    $data = self::call_hunter($email, $name, $api_key);
                    if (!empty($data['success'])) {
                        self::apply_enrichment_mapping($id, $data['payload'], $rule['field_mapping'] ?? self::default_hunter_mapping());
                        update_post_meta($id, '_pi_lead_enrichment_source', 'hunter');
                        update_post_meta($id, '_pi_lead_enriched_at', gmdate('c'));
                        $out['success'] = true;
                        $out['fields']  = array_keys($data['payload']);
                        $enriched++;
                    } else {
                        $out['message'] = $data['message'] ?? 'Hunter error';
                    }
                }
            }

            $results[] = $out;
        }

        update_option('pi_leads_enrichment_last_run', gmdate('c'), false);
        return new \WP_REST_Response([
            'success'  => true,
            'source'   => $source,
            'total'    => count($ids),
            'enriched' => $enriched,
            'results'  => $results,
        ], 200);
    }

    public static function get_enrichment_status() {
        global $wpdb;
        $total_enriched = (int) $wpdb->get_var("
            SELECT COUNT(DISTINCT post_id)
            FROM {$wpdb->postmeta}
            WHERE meta_key = '_pi_lead_enriched_at'
              AND meta_value != ''
        ");
        $by_source = $wpdb->get_results("
            SELECT meta_value AS source, COUNT(*) AS cnt
            FROM {$wpdb->postmeta}
            WHERE meta_key = '_pi_lead_enrichment_source'
              AND meta_value != ''
            GROUP BY meta_value
        ");
        $src_map = [];
        foreach ((array) $by_source as $r) $src_map[$r->source] = (int) $r->cnt;

        return new \WP_REST_Response([
            'total_enriched' => $total_enriched,
            'by_source'      => $src_map,
            'last_run'       => get_option('pi_leads_enrichment_last_run', ''),
            'rule_count'     => count(self::read_rules_option('pi_leads_enrichment_rules')),
        ], 200);
    }

    /* ═══════════════════════════════════════════════════════════════
       INTERNAL HELPERS — rules
       ═══════════════════════════════════════════════════════════════ */
    private static function read_rules_option($key) {
        $raw = get_option($key, []);
        if (!is_array($raw)) return [];
        return array_values($raw);
    }

    private static function find_rule_index($rules, $id) {
        foreach ($rules as $i => $r) {
            if ((string) ($r['id'] ?? '') === (string) $id) return $i;
        }
        return -1;
    }

    private static function sanitize_scoring_rule($raw) {
        if (!is_array($raw)) return null;
        $id = sanitize_key((string) ($raw['id'] ?? ''));
        if ($id === '') $id = 'rule_' . wp_generate_password(8, false);

        $cond = isset($raw['condition']) && is_array($raw['condition']) ? $raw['condition'] : [];
        return [
            'id'        => $id,
            'name'      => sanitize_text_field((string) ($raw['name'] ?? 'Rule')),
            'enabled'   => !isset($raw['enabled']) ? true : (bool) $raw['enabled'],
            'condition' => [
                'field'    => sanitize_key((string) ($cond['field'] ?? 'email')),
                'operator' => sanitize_key((string) ($cond['operator'] ?? 'contains')),
                'value'    => sanitize_text_field((string) ($cond['value'] ?? '')),
            ],
            'points'    => max(-100, min(100, (int) ($raw['points'] ?? 0))),
        ];
    }

    private static function sanitize_enrichment_rule($raw, $existing) {
        if (!is_array($raw)) return null;
        $id = sanitize_key((string) ($raw['id'] ?? ''));
        if ($id === '') $id = 'enrich_' . wp_generate_password(8, false);
        $source = sanitize_key((string) ($raw['source'] ?? 'manual'));
        if (!in_array($source, ['clearbit', 'hunter', 'manual'], true)) $source = 'manual';

        // Preserve existing api_key when caller submits empty string (means "unchanged")
        $existing_idx = self::find_rule_index($existing, $id);
        $prev_key = $existing_idx >= 0 ? ($existing[$existing_idx]['api_key'] ?? '') : '';
        $api_key = (string) ($raw['api_key'] ?? '');
        if ($api_key === '' && $prev_key !== '') {
            $api_key = $prev_key;
        }

        $mapping = isset($raw['field_mapping']) && is_array($raw['field_mapping']) ? $raw['field_mapping'] : [];
        $clean_mapping = [];
        foreach ($mapping as $src_key => $dest_key) {
            $clean_mapping[sanitize_key((string) $src_key)] = sanitize_key((string) $dest_key);
        }
        if (empty($clean_mapping)) {
            $clean_mapping = $source === 'clearbit' ? self::default_clearbit_mapping() :
                             ($source === 'hunter' ? self::default_hunter_mapping() : []);
        }

        $trigger = sanitize_key((string) ($raw['trigger'] ?? 'on_demand'));
        if (!in_array($trigger, ['on_create', 'on_demand'], true)) $trigger = 'on_demand';

        return [
            'id'            => $id,
            'name'          => sanitize_text_field((string) ($raw['name'] ?? ucfirst($source))),
            'source'        => $source,
            'field_mapping' => $clean_mapping,
            'trigger'       => $trigger,
            'api_key'       => sanitize_text_field($api_key),
            'enabled'       => !isset($raw['enabled']) ? true : (bool) $raw['enabled'],
        ];
    }

    private static function default_clearbit_mapping() {
        return [
            'name'         => 'company_name',
            'company_name' => 'company_name',
            'domain'       => 'company_website',
            'industry'     => 'company_industry',
            'size'         => 'company_size',
            'title'        => 'title',
            'linkedin'     => 'linkedin_url',
        ];
    }
    private static function default_hunter_mapping() {
        return [
            'email'        => 'email',
            'company'      => 'company_name',
            'position'     => 'title',
            'linkedin_url' => 'linkedin_url',
            'twitter'      => 'twitter_url',
            'score'        => 'hunter_confidence',
        ];
    }

    /* ═══════════════════════════════════════════════════════════════
       INTERNAL HELPERS — scoring engine
       ═══════════════════════════════════════════════════════════════ */
    private static function read_ai_config() {
        $cfg = get_option('pi_leads_scoring_ai_config', []);
        if (!is_array($cfg)) $cfg = [];
        return array_merge([
            'enabled'         => false,
            'provider'        => 'openai',
            'model'           => 'gpt-4o-mini',
            'prompt_template' => self::default_prompt_template(),
            'score_weight'    => 0.5,
        ], $cfg);
    }

    private static function default_prompt_template() {
        return "Bạn là trợ lý đánh giá chất lượng lead B2B. Cho lead sau:\n\n{{lead}}\n\nHãy đánh giá khả năng lead này chuyển đổi thành khách hàng, trả về DUY NHẤT một số nguyên từ 0 đến 100 (không giải thích).";
    }

    private static function collect_lead_data($id) {
        $post = get_post($id);
        return [
            'id'         => $id,
            'name'       => get_post_meta($id, '_pi_lead_name', true) ?: ($post ? $post->post_title : ''),
            'email'      => (string) get_post_meta($id, '_pi_lead_email', true),
            'phone'      => (string) get_post_meta($id, '_pi_lead_phone', true),
            'service'    => (string) get_post_meta($id, '_pi_lead_service', true),
            'source'     => (string) get_post_meta($id, '_pi_lead_source', true),
            'status'     => (string) get_post_meta($id, '_pi_lead_status', true),
            'message'    => $post ? $post->post_content : '',
            'created_at' => $post ? $post->post_date : '',
            'company_name'    => (string) get_post_meta($id, '_pi_lead_company_name', true),
            'company_size'    => (string) get_post_meta($id, '_pi_lead_company_size', true),
            'company_website' => (string) get_post_meta($id, '_pi_lead_company_website', true),
            'title'           => (string) get_post_meta($id, '_pi_lead_title', true),
        ];
    }

    private static function compare_field($lead_value, $operator, $value) {
        $lv = is_string($lead_value) ? $lead_value : (string) $lead_value;
        $op = $operator;
        switch ($op) {
            case 'equals':
                return strcasecmp($lv, (string) $value) === 0;
            case 'contains':
                return $value === '' ? false : (stripos($lv, (string) $value) !== false);
            case 'regex':
                if (!is_string($value) || $value === '') return false;
                set_error_handler(function () { /* suppress preg warnings */ });
                $res = @preg_match('/' . str_replace('/', '\\/', $value) . '/i', $lv);
                restore_error_handler();
                return $res === 1;
            case 'gt':
                return (float) $lv > (float) $value;
            case 'lt':
                return (float) $lv < (float) $value;
            case 'is_empty':
                return trim($lv) === '';
            case 'is_not_empty':
                return trim($lv) !== '';
            default:
                return false;
        }
    }

    private static function evaluate_rules(array $lead, array $rules) {
        $matches = [];
        $score = 0;
        foreach ($rules as $r) {
            if (isset($r['enabled']) && !$r['enabled']) continue;
            $field = $r['condition']['field']    ?? '';
            $op    = $r['condition']['operator'] ?? 'equals';
            $val   = $r['condition']['value']    ?? '';
            $lead_val = $lead[$field] ?? '';
            $hit = self::compare_field($lead_val, $op, $val);
            if ($hit) {
                $score += (int) ($r['points'] ?? 0);
                $matches[] = [
                    'id'       => $r['id']   ?? '',
                    'name'     => $r['name'] ?? '',
                    'points'   => (int) ($r['points'] ?? 0),
                    'field'    => $field,
                    'operator' => $op,
                    'value'    => $val,
                    'lead_value' => is_scalar($lead_val) ? (string) $lead_val : '',
                ];
            }
        }
        return ['score' => $score, 'matches' => $matches];
    }

    private static function call_ai_scorer(array $lead, array $cfg) {
        $provider = $cfg['provider'] ?? 'openai';
        $model    = $cfg['model']    ?? 'gpt-4o-mini';
        $template = (string) ($cfg['prompt_template'] ?? self::default_prompt_template());

        // Serialize the lead as readable text for the prompt
        $lead_lines = [];
        foreach ($lead as $k => $v) {
            if (is_scalar($v) && $v !== '' && $v !== null) {
                $lead_lines[] = $k . ': ' . $v;
            }
        }
        $lead_text = implode("\n", $lead_lines);
        $prompt = str_replace('{{lead}}', $lead_text, $template);

        $meta = [
            'openai' => [
                'key_option' => 'sh_ai_openai_key',
                'url'        => 'https://api.openai.com/v1/chat/completions',
            ],
            'anthropic' => [
                'key_option' => 'sh_ai_anthropic_key',
                'url'        => 'https://api.anthropic.com/v1/messages',
            ],
            'gemini' => [
                'key_option' => 'sh_ai_gemini_key',
            ],
            'groq' => [
                'key_option' => 'sh_ai_groq_key',
                'url'        => 'https://api.groq.com/openai/v1/chat/completions',
            ],
        ];
        if (!isset($meta[$provider])) {
            return ['ok' => false, 'score' => 0, 'message' => 'Provider không hỗ trợ'];
        }
        $key = get_option($meta[$provider]['key_option'], '');
        if (!$key) {
            return ['ok' => false, 'score' => 0, 'message' => 'Provider chưa có API key'];
        }

        $args = [
            'timeout'   => 25,
            'sslverify' => (wp_get_environment_type() !== 'local'),
            'headers'   => ['Content-Type' => 'application/json'],
        ];

        $raw_text = '';
        if ($provider === 'openai' || $provider === 'groq') {
            $args['headers']['Authorization'] = 'Bearer ' . $key;
            $args['body'] = wp_json_encode([
                'model'    => $model,
                'messages' => [['role' => 'user', 'content' => $prompt]],
                'max_tokens' => 20,
                'temperature' => 0,
            ]);
            $resp = wp_remote_post($meta[$provider]['url'], $args);
            if (is_wp_error($resp)) return ['ok' => false, 'score' => 0, 'message' => $resp->get_error_message()];
            $d = json_decode(wp_remote_retrieve_body($resp), true);
            $raw_text = (string) ($d['choices'][0]['message']['content'] ?? '');
        } elseif ($provider === 'anthropic') {
            $args['headers']['x-api-key'] = $key;
            $args['headers']['anthropic-version'] = '2023-06-01';
            $args['body'] = wp_json_encode([
                'model'      => $model,
                'max_tokens' => 20,
                'messages'   => [['role' => 'user', 'content' => $prompt]],
            ]);
            $resp = wp_remote_post($meta[$provider]['url'], $args);
            if (is_wp_error($resp)) return ['ok' => false, 'score' => 0, 'message' => $resp->get_error_message()];
            $d = json_decode(wp_remote_retrieve_body($resp), true);
            $raw_text = (string) ($d['content'][0]['text'] ?? '');
        } elseif ($provider === 'gemini') {
            $url = 'https://generativelanguage.googleapis.com/v1beta/models/' . rawurlencode($model) . ':generateContent';
            $url = add_query_arg('key', $key, $url);
            $args['body'] = wp_json_encode([
                'contents' => [['role' => 'user', 'parts' => [['text' => $prompt]]]],
                'generationConfig' => ['maxOutputTokens' => 20, 'temperature' => 0],
            ]);
            $resp = wp_remote_post($url, $args);
            if (is_wp_error($resp)) return ['ok' => false, 'score' => 0, 'message' => $resp->get_error_message()];
            $d = json_decode(wp_remote_retrieve_body($resp), true);
            $raw_text = (string) ($d['candidates'][0]['content']['parts'][0]['text'] ?? '');
        }

        // Parse first integer out of response
        $score = 0;
        if (preg_match('/\b(\d{1,3})\b/', $raw_text, $m)) {
            $score = max(0, min(100, (int) $m[1]));
            return ['ok' => true, 'score' => $score, 'raw' => $raw_text];
        }
        return ['ok' => false, 'score' => 0, 'message' => 'Không đọc được điểm từ AI', 'raw' => $raw_text];
    }

    private static function blend_scores($rules_score, $ai_out, $cfg) {
        $rules = max(0, min(100, (int) $rules_score));
        if (empty($cfg['enabled']) || empty($ai_out) || empty($ai_out['ok'])) {
            return $rules;
        }
        $w = max(0.0, min(1.0, (float) ($cfg['score_weight'] ?? 0.5)));
        $ai = max(0, min(100, (int) $ai_out['score']));
        $blended = ($w * $ai) + ((1.0 - $w) * $rules);
        return max(0, min(100, (int) round($blended)));
    }

    private static function score_single_lead($id, $rules, $ai_cfg) {
        $lead = self::collect_lead_data($id);
        $breakdown = self::evaluate_rules($lead, $rules);
        $ai_out = null;
        if (!empty($ai_cfg['enabled'])) {
            $ai_out = self::call_ai_scorer($lead, $ai_cfg);
        }
        $final = self::blend_scores($breakdown['score'], $ai_out, $ai_cfg);
        update_post_meta($id, '_pi_lead_score', $final);
        update_post_meta($id, '_pi_lead_score_breakdown', wp_json_encode([
            'rules_score'   => $breakdown['score'],
            'rules_matches' => $breakdown['matches'],
            'ai'            => $ai_out,
            'blended'       => $final,
        ]));
        update_post_meta($id, '_pi_lead_scored_at', gmdate('c'));
        return ['ok' => true, 'score' => $final];
    }

    private static function lookup_score_for_contact($email, $phone) {
        $meta_query = ['relation' => 'OR'];
        if (!empty($email)) $meta_query[] = ['key' => '_pi_lead_email', 'value' => $email];
        if (!empty($phone)) $meta_query[] = ['key' => '_pi_lead_phone', 'value' => $phone];
        if (count($meta_query) === 1) return null;

        $q = get_posts([
            'post_type'      => 'sh_lead',
            'post_status'    => 'publish',
            'posts_per_page' => 1,
            'fields'         => 'ids',
            'meta_query'     => $meta_query,
        ]);
        if (empty($q)) return null;
        $v = get_post_meta((int) $q[0], '_pi_lead_score', true);
        return $v === '' ? null : (int) $v;
    }

    /* ═══════════════════════════════════════════════════════════════
       INTERNAL HELPERS — enrichment providers
       ═══════════════════════════════════════════════════════════════ */
    private static function call_clearbit($email, $api_key) {
        $url = add_query_arg('email', rawurlencode($email), 'https://person.clearbit.com/v2/people/find');
        $resp = wp_remote_get($url, [
            'timeout'   => 20,
            'sslverify' => (wp_get_environment_type() !== 'local'),
            'headers'   => [
                'Authorization' => 'Bearer ' . $api_key,
                'Accept'        => 'application/json',
            ],
        ]);
        if (is_wp_error($resp)) return ['success' => false, 'message' => $resp->get_error_message()];
        $code = (int) wp_remote_retrieve_response_code($resp);
        if ($code === 202) return ['success' => false, 'message' => 'Clearbit đang xử lý (202 Accepted), thử lại sau'];
        if ($code < 200 || $code >= 300) return ['success' => false, 'message' => 'Clearbit HTTP ' . $code];

        $d = json_decode(wp_remote_retrieve_body($resp), true);
        if (!is_array($d)) return ['success' => false, 'message' => 'Clearbit trả dữ liệu không hợp lệ'];

        $payload = [
            'name'         => $d['name']['fullName']   ?? '',
            'title'        => $d['employment']['title'] ?? '',
            'company_name' => $d['employment']['name']  ?? '',
            'domain'       => $d['employment']['domain'] ?? '',
            'linkedin'     => $d['linkedin']['handle']  ?? '',
        ];
        return ['success' => true, 'payload' => $payload];
    }

    private static function call_hunter($email, $name, $api_key) {
        $parts = explode('@', $email);
        $domain = $parts[1] ?? '';
        if ($domain === '') return ['success' => false, 'message' => 'Email không có tên miền'];

        $first = '';
        $last  = '';
        $nm = trim($name);
        if ($nm !== '') {
            $bits = preg_split('/\s+/', $nm);
            $first = $bits[0] ?? '';
            $last  = count($bits) > 1 ? end($bits) : '';
        }

        $args = [
            'domain' => $domain,
            'api_key' => $api_key,
        ];
        if ($first !== '') $args['first_name'] = $first;
        if ($last !== '')  $args['last_name']  = $last;

        $url = add_query_arg($args, 'https://api.hunter.io/v2/email-finder');
        $resp = wp_remote_get($url, [
            'timeout'   => 20,
            'sslverify' => (wp_get_environment_type() !== 'local'),
        ]);
        if (is_wp_error($resp)) return ['success' => false, 'message' => $resp->get_error_message()];
        $code = (int) wp_remote_retrieve_response_code($resp);
        $body = wp_remote_retrieve_body($resp);
        $d    = json_decode($body, true);
        if ($code < 200 || $code >= 300) {
            $msg = isset($d['errors'][0]['details']) ? $d['errors'][0]['details'] : ('Hunter HTTP ' . $code);
            return ['success' => false, 'message' => $msg];
        }
        $data = $d['data'] ?? [];
        $payload = [
            'email'        => $data['email']        ?? $email,
            'score'        => $data['score']        ?? '',
            'company'      => $data['company']      ?? '',
            'position'     => $data['position']     ?? '',
            'linkedin_url' => $data['linkedin_url'] ?? '',
            'twitter'      => $data['twitter']      ?? '',
        ];
        return ['success' => true, 'payload' => $payload];
    }

    private static function apply_enrichment_mapping($post_id, $payload, $mapping) {
        if (!is_array($mapping) || empty($mapping)) return;
        foreach ($mapping as $src => $dest) {
            if (!isset($payload[$src]) || $payload[$src] === '') continue;
            $meta_key = '_pi_lead_' . sanitize_key($dest);
            update_post_meta($post_id, $meta_key, sanitize_text_field((string) $payload[$src]));
        }
    }

    /* ═══════════════════════════════════════════════════════════════
       ENRICHMENT — single + bulk + credits (Phase 2 task C)
       ═══════════════════════════════════════════════════════════════ */
    public static function enrich_now(\WP_REST_Request $request) {
        $id = (int) $request->get_param('id');
        $p = $request->get_json_params();
        if (!is_array($p)) $p = [];
        $source = sanitize_key((string) ($p['source'] ?? 'clearbit'));
        if (!in_array($source, ['clearbit', 'hunter', 'manual'], true)) $source = 'clearbit';

        // Reuse the existing enrich_leads pipeline by faking a sub-request
        $req = new \WP_REST_Request('POST', '/');
        $req->set_body(wp_json_encode(['lead_ids' => [$id], 'source' => $source]));
        $req->set_header('Content-Type', 'application/json');

        $result = self::enrich_leads($req);
        $data = $result instanceof \WP_REST_Response ? $result->get_data() : null;

        // Log activity
        self::log_activity($id, 'enriched', [
            'source'  => $source,
            'success' => !empty($data['enriched']),
        ]);

        return $result;
    }

    public static function enrich_bulk(\WP_REST_Request $request) {
        $p = $request->get_json_params();
        if (!is_array($p)) $p = [];
        $ids    = isset($p['lead_ids']) && is_array($p['lead_ids']) ? array_map('intval', $p['lead_ids']) : [];
        $source = sanitize_key((string) ($p['source'] ?? 'clearbit'));
        if (!in_array($source, ['clearbit', 'hunter', 'manual'], true)) $source = 'clearbit';
        if (empty($ids)) {
            return new \WP_REST_Response(['success' => false, 'message' => 'Thiếu lead_ids'], 400);
        }
        $req = new \WP_REST_Request('POST', '/');
        $req->set_body(wp_json_encode(['lead_ids' => $ids, 'source' => $source]));
        $req->set_header('Content-Type', 'application/json');
        return self::enrich_leads($req);
    }

    public static function get_enrichment_credits() {
        $clearbit_key = get_option('pi_clearbit_api_key', '');
        $hunter_key   = get_option('pi_hunter_api_key', '');
        $clearbit_rule = self::find_rule_by_source('clearbit');
        $hunter_rule   = self::find_rule_by_source('hunter');

        // Fallbacks: pick the rule's api_key when global option not set
        if (!$clearbit_key && $clearbit_rule) $clearbit_key = $clearbit_rule['api_key'] ?? '';
        if (!$hunter_key   && $hunter_rule)   $hunter_key   = $hunter_rule['api_key']   ?? '';

        $hunter_credits = ['available' => null, 'used' => null, 'reset' => null];
        if ($hunter_key) {
            $resp = wp_remote_get('https://api.hunter.io/v2/account?api_key=' . rawurlencode($hunter_key), [
                'timeout' => 10,
                'sslverify' => (wp_get_environment_type() !== 'local'),
            ]);
            if (!is_wp_error($resp) && (int) wp_remote_retrieve_response_code($resp) === 200) {
                $d = json_decode(wp_remote_retrieve_body($resp), true);
                $req = $d['data']['requests']['searches'] ?? [];
                $hunter_credits['used']      = isset($req['used']) ? (int) $req['used'] : null;
                $hunter_credits['available'] = isset($req['available']) ? (int) $req['available'] : null;
                $hunter_credits['reset']     = $d['data']['reset_date'] ?? null;
            }
        }

        return new \WP_REST_Response([
            'sources' => [
                [
                    'id'          => 'clearbit',
                    'label'       => 'Clearbit',
                    'configured'  => !empty($clearbit_key),
                    'credits'     => null, // Clearbit không expose API kiểm tra số dư
                    'note'        => empty($clearbit_key) ? 'Chưa cấu hình API key' : 'Đã cấu hình — Clearbit không cung cấp endpoint kiểm tra số dư',
                ],
                [
                    'id'          => 'hunter',
                    'label'       => 'Hunter.io',
                    'configured'  => !empty($hunter_key),
                    'credits'     => $hunter_credits,
                    'note'        => empty($hunter_key) ? 'Chưa cấu hình API key' : 'Đã cấu hình',
                ],
                [
                    'id'          => 'manual',
                    'label'       => 'Thủ công',
                    'configured'  => true,
                    'credits'     => null,
                    'note'        => 'Đánh dấu lead chờ con người xem',
                ],
            ],
        ], 200);
    }

    private static function find_rule_by_source($source) {
        $rules = self::read_rules_option('pi_leads_enrichment_rules');
        foreach ($rules as $r) {
            if (($r['source'] ?? '') === $source) return $r;
        }
        return null;
    }

    /* ═══════════════════════════════════════════════════════════════
       ACTIVITY TIMELINE — notes / attachments / reminders
       ═══════════════════════════════════════════════════════════════ */
    public static function get_timeline(\WP_REST_Request $request) {
        global $wpdb;
        $id = (int) $request->get_param('id');
        if (get_post_type($id) !== 'sh_lead') {
            return new \WP_REST_Response(['message' => 'Lead không tồn tại'], 404);
        }
        self::ensure_activity_table();
        $table = $wpdb->prefix . 'pi_lead_activities';
        $rows = $wpdb->get_results(
            $wpdb->prepare("SELECT id, lead_id, event_type, actor_id, data, created_at
                            FROM {$table} WHERE lead_id = %d ORDER BY id DESC LIMIT 200", $id),
            ARRAY_A
        );
        $out = [];
        foreach ((array) $rows as $r) {
            $out[] = [
                'id'         => (int) $r['id'],
                'event_type' => $r['event_type'],
                'actor_id'   => (int) $r['actor_id'],
                'actor_name' => $r['actor_id'] ? (get_userdata((int) $r['actor_id'])->display_name ?? '') : 'Hệ thống',
                'data'       => json_decode((string) $r['data'], true),
                'created_at' => $r['created_at'],
            ];
        }
        // Synthesize a "created" event from post itself if no rows
        if (empty($out)) {
            $post = get_post($id);
            if ($post) {
                $out[] = [
                    'id'         => 0,
                    'event_type' => 'created',
                    'actor_id'   => 0,
                    'actor_name' => 'Hệ thống',
                    'data'       => ['source' => get_post_meta($id, '_pi_lead_source', true) ?: 'unknown'],
                    'created_at' => $post->post_date_gmt ?: $post->post_date,
                ];
            }
        }
        return new \WP_REST_Response(['events' => $out], 200);
    }

    public static function add_note(\WP_REST_Request $request) {
        $id = (int) $request->get_param('id');
        if (get_post_type($id) !== 'sh_lead') {
            return new \WP_REST_Response(['message' => 'Lead không tồn tại'], 404);
        }
        $p = $request->get_json_params();
        $content = sanitize_textarea_field((string) ($p['content'] ?? ''));
        if ($content === '') {
            return new \WP_REST_Response(['success' => false, 'message' => 'Nội dung rỗng'], 400);
        }
        $note_id = self::log_activity($id, 'note_added', ['content' => $content]);
        return new \WP_REST_Response(['success' => true, 'note_id' => $note_id], 200);
    }

    public static function delete_note(\WP_REST_Request $request) {
        global $wpdb;
        $id   = (int) $request->get_param('id');
        $note = (int) $request->get_param('note_id');
        self::ensure_activity_table();
        $table = $wpdb->prefix . 'pi_lead_activities';
        $deleted = $wpdb->delete($table, ['id' => $note, 'lead_id' => $id, 'event_type' => 'note_added'], ['%d', '%d', '%s']);
        return new \WP_REST_Response(['success' => $deleted !== false], 200);
    }

    public static function add_attachment(\WP_REST_Request $request) {
        $id = (int) $request->get_param('id');
        if (get_post_type($id) !== 'sh_lead') {
            return new \WP_REST_Response(['message' => 'Lead không tồn tại'], 404);
        }
        $files = $request->get_file_params();
        if (empty($files['file'])) {
            // Allow JSON URL stub for testing
            $p = $request->get_json_params();
            $url  = isset($p['url'])  ? esc_url_raw((string) $p['url'])  : '';
            $name = isset($p['name']) ? sanitize_text_field((string) $p['name']) : 'file';
            if ($url === '') return new \WP_REST_Response(['success' => false, 'message' => 'Thiếu file'], 400);
            $aid = self::log_activity($id, 'attachment_added', ['name' => $name, 'url' => $url]);
            return new \WP_REST_Response(['success' => true, 'attachment_id' => $aid, 'url' => $url], 200);
        }

        require_once ABSPATH . 'wp-admin/includes/file.php';
        $upload = wp_handle_upload($files['file'], ['test_form' => false]);
        if (!empty($upload['error'])) {
            return new \WP_REST_Response(['success' => false, 'message' => $upload['error']], 400);
        }
        $aid = self::log_activity($id, 'attachment_added', [
            'name' => basename($upload['file']),
            'url'  => $upload['url'],
            'type' => $upload['type'] ?? '',
        ]);
        return new \WP_REST_Response(['success' => true, 'attachment_id' => $aid, 'url' => $upload['url']], 200);
    }

    public static function add_reminder(\WP_REST_Request $request) {
        $id = (int) $request->get_param('id');
        if (get_post_type($id) !== 'sh_lead') {
            return new \WP_REST_Response(['message' => 'Lead không tồn tại'], 404);
        }
        $p = $request->get_json_params();
        $when    = sanitize_text_field((string) ($p['when']    ?? ''));
        $message = sanitize_text_field((string) ($p['message'] ?? ''));
        if ($when === '' || $message === '') {
            return new \WP_REST_Response(['success' => false, 'message' => 'Thiếu thời gian hoặc nội dung nhắc'], 400);
        }
        $rid = self::log_activity($id, 'reminder_set', ['when' => $when, 'message' => $message, 'done' => false]);
        return new \WP_REST_Response(['success' => true, 'reminder_id' => $rid], 200);
    }

    /* ═══════════════════════════════════════════════════════════════
       INTERNAL — activity logging
       ═══════════════════════════════════════════════════════════════ */
    private static function ensure_activity_table() {
        global $wpdb;
        $table = $wpdb->prefix . 'pi_lead_activities';
        if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table)) === $table) return;
        $charset = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE {$table} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            lead_id BIGINT UNSIGNED NOT NULL,
            event_type VARCHAR(48) NOT NULL,
            actor_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
            data LONGTEXT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY lead_id (lead_id),
            KEY event_type (event_type)
        ) {$charset};";
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    private static function log_activity($lead_id, $event_type, $data = []) {
        global $wpdb;
        self::ensure_activity_table();
        $table = $wpdb->prefix . 'pi_lead_activities';
        $wpdb->insert($table, [
            'lead_id'    => (int) $lead_id,
            'event_type' => sanitize_key($event_type),
            'actor_id'   => (int) get_current_user_id(),
            'data'       => wp_json_encode($data ?: new \stdClass()),
        ], ['%d', '%s', '%d', '%s']);
        return (int) $wpdb->insert_id;
    }
}
