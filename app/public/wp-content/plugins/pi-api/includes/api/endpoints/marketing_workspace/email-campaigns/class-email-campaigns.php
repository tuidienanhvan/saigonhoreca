<?php
/**
 * Pi API Email Campaigns Endpoints
 *
 * - GET    /email-campaigns                   List
 * - POST   /email-campaigns                   Create
 * - PUT    /email-campaigns/{id}              Update
 * - DELETE /email-campaigns/{id}              Delete
 * - POST   /email-campaigns/{id}/send         Trigger send (cron-driven)
 * - POST   /email-campaigns/{id}/cancel       Cancel scheduled
 * - GET    /email-campaigns/{id}/stats        Open/click stats
 *
 * Schema: wp_pi_email_campaigns
 *   id, name, template_id, audience_filter(JSON), scheduled_at,
 *   status, sent_count, open_count, click_count, created_at
 *
 * Status: draft | scheduled | sending | sent | cancelled
 *
 * Cron: pi_email_campaigns_dispatch — daily, picks scheduled campaigns due,
 *       batch sends 50/run, updates status.
 */

if (!defined('ABSPATH')) exit;

class Pi_Api_EmailCampaigns {

    const CRON_HOOK     = 'pi_email_campaigns_dispatch';
    const BATCH_SIZE    = 50;

    /* ════════════════════════════════════════════════
       SCHEMA
       ════════════════════════════════════════════════ */
    public static function ensure_table() {
        global $wpdb;
        $table   = $wpdb->prefix . 'pi_email_campaigns';
        $charset = $wpdb->get_charset_collate();
        if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table)) === $table) return;

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        $sql = "CREATE TABLE $table (
            id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            name            VARCHAR(190) NOT NULL DEFAULT '',
            template_id     BIGINT UNSIGNED NOT NULL DEFAULT 0,
            audience_filter LONGTEXT NOT NULL,
            scheduled_at    DATETIME NULL DEFAULT NULL,
            status          VARCHAR(32) NOT NULL DEFAULT 'draft',
            sent_count      INT UNSIGNED NOT NULL DEFAULT 0,
            open_count      INT UNSIGNED NOT NULL DEFAULT 0,
            click_count     INT UNSIGNED NOT NULL DEFAULT 0,
            created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY idx_status (status),
            KEY idx_scheduled (scheduled_at)
        ) $charset;";
        dbDelta($sql);
    }

    /* ════════════════════════════════════════════════
       ROUTES + CRON
       ════════════════════════════════════════════════ */
    public static function register_routes($ns, $admin) {
        self::ensure_table();
        self::ensure_cron();

        register_rest_route($ns, '/email-campaigns', [
            ['methods' => 'GET',  'callback' => [self::class, 'list_campaigns'],  'permission_callback' => $admin],
            ['methods' => 'POST', 'callback' => [self::class, 'create_campaign'], 'permission_callback' => $admin],
        ]);
        register_rest_route($ns, '/email-campaigns/(?P<id>\d+)', [
            ['methods' => 'GET',    'callback' => [self::class, 'get_campaign'],    'permission_callback' => $admin],
            ['methods' => 'PUT',    'callback' => [self::class, 'update_campaign'], 'permission_callback' => $admin],
            ['methods' => 'POST',   'callback' => [self::class, 'update_campaign'], 'permission_callback' => $admin], // alias
            ['methods' => 'DELETE', 'callback' => [self::class, 'delete_campaign'], 'permission_callback' => $admin],
        ]);
        register_rest_route($ns, '/email-campaigns/(?P<id>\d+)/send', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'trigger_send'],
            'permission_callback' => $admin,
        ]);
        register_rest_route($ns, '/email-campaigns/(?P<id>\d+)/cancel', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'cancel_campaign'],
            'permission_callback' => $admin,
        ]);
        register_rest_route($ns, '/email-campaigns/(?P<id>\d+)/stats', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'get_stats'],
            'permission_callback' => $admin,
        ]);
    }

    public static function ensure_cron() {
        if (!wp_next_scheduled(self::CRON_HOOK)) {
            wp_schedule_event(time() + 60, 'daily', self::CRON_HOOK);
        }
        add_action(self::CRON_HOOK, [self::class, 'cron_dispatch']);
    }

    /* ════════════════════════════════════════════════
       LIST
       ════════════════════════════════════════════════ */
    public static function list_campaigns(\WP_REST_Request $request) {
        global $wpdb;
        $table = $wpdb->prefix . 'pi_email_campaigns';

        $status = trim((string) $request->get_param('status'));
        $from   = trim((string) $request->get_param('from'));
        $to     = trim((string) $request->get_param('to'));

        $where  = ['1=1'];
        $params = [];
        if ($status !== '' && $status !== 'all') {
            $where[]  = 'status = %s';
            $params[] = $status;
        }
        if ($from !== '') {
            $where[]  = 'created_at >= %s';
            $params[] = $from . ' 00:00:00';
        }
        if ($to !== '') {
            $where[]  = 'created_at <= %s';
            $params[] = $to . ' 23:59:59';
        }

        $sql = "SELECT * FROM $table WHERE " . implode(' AND ', $where) . " ORDER BY created_at DESC LIMIT 500";
        $sql = $params ? $wpdb->prepare($sql, $params) : $sql;
        $rows = (array) $wpdb->get_results($sql, ARRAY_A);

        // Map template names
        $templates = [];
        if ($rows) {
            $tids = array_unique(array_filter(array_map(function ($r) { return (int) $r['template_id']; }, $rows)));
            if ($tids) {
                $t_table = $wpdb->prefix . 'pi_email_templates';
                $in      = implode(',', array_map('intval', $tids));
                if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $t_table)) === $t_table) {
                    $tplrows = $wpdb->get_results("SELECT id, name FROM $t_table WHERE id IN ($in)", ARRAY_A);
                    foreach ((array) $tplrows as $t) $templates[(int) $t['id']] = (string) $t['name'];
                }
            }
        }

        return new \WP_REST_Response([
            'campaigns' => array_map(function ($r) use ($templates) {
                return self::shape_row($r, $templates);
            }, $rows),
            'count' => count($rows),
        ], 200);
    }

    /* ════════════════════════════════════════════════
       SINGLE
       ════════════════════════════════════════════════ */
    public static function get_campaign(\WP_REST_Request $request) {
        $id  = (int) $request->get_param('id');
        $row = self::fetch_row($id);
        if (!$row) {
            return new \WP_REST_Response(['message' => 'Campaign không tồn tại'], 404);
        }
        return new \WP_REST_Response(self::shape_row($row), 200);
    }

    /* ════════════════════════════════════════════════
       CREATE
       ════════════════════════════════════════════════ */
    public static function create_campaign(\WP_REST_Request $request) {
        global $wpdb;
        $table = $wpdb->prefix . 'pi_email_campaigns';

        $clean = self::sanitize_input($request->get_json_params());
        if ($clean['name'] === '' || $clean['template_id'] <= 0) {
            return new \WP_REST_Response(['message' => 'Thiếu tên hoặc template'], 400);
        }

        $now = current_time('mysql', true);
        $ok  = $wpdb->insert($table, [
            'name'            => $clean['name'],
            'template_id'     => $clean['template_id'],
            'audience_filter' => wp_json_encode($clean['audience_filter']),
            'scheduled_at'    => $clean['scheduled_at'],
            'status'          => $clean['status'],
            'sent_count'      => 0,
            'open_count'      => 0,
            'click_count'     => 0,
            'created_at'      => $now,
            'updated_at'      => $now,
        ]);
        if ($ok === false) {
            return new \WP_REST_Response(['message' => 'Không tạo được chiến dịch'], 500);
        }
        $id = (int) $wpdb->insert_id;
        return new \WP_REST_Response(['success' => true, 'campaign' => self::shape_row(self::fetch_row($id))], 200);
    }

    /* ════════════════════════════════════════════════
       UPDATE
       ════════════════════════════════════════════════ */
    public static function update_campaign(\WP_REST_Request $request) {
        global $wpdb;
        $table = $wpdb->prefix . 'pi_email_campaigns';
        $id    = (int) $request->get_param('id');
        $row   = self::fetch_row($id);
        if (!$row) {
            return new \WP_REST_Response(['message' => 'Campaign không tồn tại'], 404);
        }

        $clean = self::sanitize_input($request->get_json_params(), $row);
        $wpdb->update($table, [
            'name'            => $clean['name'],
            'template_id'     => $clean['template_id'],
            'audience_filter' => wp_json_encode($clean['audience_filter']),
            'scheduled_at'    => $clean['scheduled_at'],
            'status'          => $clean['status'],
            'updated_at'      => current_time('mysql', true),
        ], ['id' => $id]);
        return new \WP_REST_Response(['success' => true, 'campaign' => self::shape_row(self::fetch_row($id))], 200);
    }

    /* ════════════════════════════════════════════════
       DELETE
       ════════════════════════════════════════════════ */
    public static function delete_campaign(\WP_REST_Request $request) {
        global $wpdb;
        $table = $wpdb->prefix . 'pi_email_campaigns';
        $id    = (int) $request->get_param('id');
        if (!self::fetch_row($id)) {
            return new \WP_REST_Response(['message' => 'Campaign không tồn tại'], 404);
        }
        $wpdb->delete($table, ['id' => $id]);
        return new \WP_REST_Response(['success' => true], 200);
    }

    /* ════════════════════════════════════════════════
       TRIGGER SEND (manual, cron also dispatches)
       ════════════════════════════════════════════════ */
    public static function trigger_send(\WP_REST_Request $request) {
        $id  = (int) $request->get_param('id');
        $row = self::fetch_row($id);
        if (!$row) {
            return new \WP_REST_Response(['message' => 'Campaign không tồn tại'], 404);
        }
        if (in_array($row['status'], ['sent', 'sending', 'cancelled'], true)) {
            return new \WP_REST_Response(['message' => 'Chiến dịch không ở trạng thái có thể gửi'], 409);
        }

        $result = self::dispatch_campaign((int) $row['id']);
        return new \WP_REST_Response($result, 200);
    }

    /* ════════════════════════════════════════════════
       CANCEL
       ════════════════════════════════════════════════ */
    public static function cancel_campaign(\WP_REST_Request $request) {
        global $wpdb;
        $table = $wpdb->prefix . 'pi_email_campaigns';
        $id    = (int) $request->get_param('id');
        $row   = self::fetch_row($id);
        if (!$row) {
            return new \WP_REST_Response(['message' => 'Campaign không tồn tại'], 404);
        }
        if (!in_array($row['status'], ['draft', 'scheduled'], true)) {
            return new \WP_REST_Response(['message' => 'Chỉ huỷ được khi nháp hoặc đã lên lịch'], 409);
        }
        $wpdb->update($table, [
            'status'     => 'cancelled',
            'updated_at' => current_time('mysql', true),
        ], ['id' => $id]);
        return new \WP_REST_Response(['success' => true], 200);
    }

    /* ════════════════════════════════════════════════
       STATS
       ════════════════════════════════════════════════ */
    public static function get_stats(\WP_REST_Request $request) {
        $id  = (int) $request->get_param('id');
        $row = self::fetch_row($id);
        if (!$row) {
            return new \WP_REST_Response(['message' => 'Campaign không tồn tại'], 404);
        }
        $sent  = (int) $row['sent_count'];
        $open  = (int) $row['open_count'];
        $click = (int) $row['click_count'];
        return new \WP_REST_Response([
            'id'            => (int) $row['id'],
            'name'          => (string) $row['name'],
            'sent_count'    => $sent,
            'open_count'    => $open,
            'click_count'   => $click,
            'open_rate'     => $sent > 0 ? round(($open / $sent) * 100, 2)  : 0,
            'click_rate'    => $sent > 0 ? round(($click / $sent) * 100, 2) : 0,
            'status'        => (string) $row['status'],
        ], 200);
    }

    /* ════════════════════════════════════════════════
       CRON DISPATCH
       ════════════════════════════════════════════════ */
    public static function cron_dispatch() {
        global $wpdb;
        $table = $wpdb->prefix . 'pi_email_campaigns';
        if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table)) !== $table) return;

        $now = current_time('mysql', true);
        $rows = (array) $wpdb->get_results($wpdb->prepare(
            "SELECT id FROM $table
             WHERE status IN ('scheduled', 'draft')
               AND scheduled_at IS NOT NULL
               AND scheduled_at <= %s
             ORDER BY scheduled_at ASC
             LIMIT 20",
            $now
        ), ARRAY_A);

        foreach ($rows as $r) {
            self::dispatch_campaign((int) $r['id']);
        }
    }

    /* ════════════════════════════════════════════════
       INTERNAL — single-batch dispatch
       ════════════════════════════════════════════════ */
    private static function dispatch_campaign($campaign_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'pi_email_campaigns';
        $row   = self::fetch_row($campaign_id);
        if (!$row) return ['success' => false, 'message' => 'Không tìm thấy chiến dịch'];

        // Mark sending
        $wpdb->update($table, ['status' => 'sending', 'updated_at' => current_time('mysql', true)], ['id' => $campaign_id]);

        // Resolve template
        $tpl = null;
        if (class_exists('Pi_Api_EmailTemplates')) {
            $tpl = Pi_Api_EmailTemplates::fetch_row((int) $row['template_id']);
        }
        if (!$tpl) {
            $wpdb->update($table, ['status' => 'draft'], ['id' => $campaign_id]);
            return ['success' => false, 'message' => 'Template không tồn tại'];
        }

        $audience = json_decode((string) $row['audience_filter'], true);
        $audience = is_array($audience) ? $audience : [];

        $recipients = self::resolve_audience($audience);
        if (empty($recipients)) {
            $wpdb->update($table, [
                'status'     => 'sent',
                'sent_count' => 0,
                'updated_at' => current_time('mysql', true),
            ], ['id' => $campaign_id]);
            return ['success' => true, 'sent' => 0, 'message' => 'Không có người nhận khớp bộ lọc'];
        }

        $sent_total = (int) $row['sent_count'];
        $batch      = array_slice($recipients, $sent_total, self::BATCH_SIZE);
        $sent_now   = 0;

        foreach ($batch as $r) {
            $vars = array_merge(Pi_Api_EmailTemplates::default_mock_vars(), [
                'lead.name'    => $r['name']    ?? '',
                'lead.email'   => $r['email']   ?? '',
                'lead.phone'   => $r['phone']   ?? '',
                'lead.company' => $r['company'] ?? '',
            ]);
            $subject = Pi_Api_EmailTemplates::render_with_vars($tpl['subject'], $vars);
            $html    = Pi_Api_EmailTemplates::render_with_vars($tpl['body_html'], $vars);
            if (!is_email($r['email'])) continue;
            if (wp_mail($r['email'], $subject, $html, ['Content-Type: text/html; charset=UTF-8'])) {
                $sent_now++;
            }
        }

        $sent_total += $sent_now;
        $finished    = $sent_total >= count($recipients);
        $wpdb->update($table, [
            'sent_count' => $sent_total,
            'status'     => $finished ? 'sent' : 'sending',
            'updated_at' => current_time('mysql', true),
        ], ['id' => $campaign_id]);

        return [
            'success'        => true,
            'sent_in_batch'  => $sent_now,
            'sent_total'     => $sent_total,
            'recipients'     => count($recipients),
            'status'         => $finished ? 'sent' : 'sending',
        ];
    }

    private static function resolve_audience($filter) {
        $segment = (string) ($filter['segment'] ?? 'all');
        $args = [
            'post_type'      => 'sh_lead',
            'post_status'    => 'publish',
            'posts_per_page' => 2000,
            'fields'         => 'ids',
        ];

        if ($segment === 'by-status' && !empty($filter['status'])) {
            $args['meta_query'] = [[
                'key'   => '_pi_lead_status',
                'value' => sanitize_key((string) $filter['status']),
            ]];
        } elseif ($segment === 'by-source' && !empty($filter['source'])) {
            $args['meta_query'] = [[
                'key'   => '_pi_lead_source',
                'value' => sanitize_text_field((string) $filter['source']),
            ]];
        } elseif ($segment === 'by-tag' && !empty($filter['tag'])) {
            $args['meta_query'] = [[
                'key'     => '_pi_lead_tags',
                'value'   => sanitize_text_field((string) $filter['tag']),
                'compare' => 'LIKE',
            ]];
        }

        $ids = get_posts($args);
        $out = [];
        foreach ((array) $ids as $id) {
            $email = (string) get_post_meta($id, '_pi_lead_email', true);
            if (!is_email($email)) continue;
            $out[] = [
                'id'      => (int) $id,
                'email'   => $email,
                'name'    => get_post_meta($id, '_pi_lead_name', true) ?: get_the_title($id),
                'phone'   => (string) get_post_meta($id, '_pi_lead_phone', true),
                'company' => (string) get_post_meta($id, '_pi_lead_company_name', true),
            ];
        }
        return $out;
    }

    /* ════════════════════════════════════════════════
       SHAPING / SANITIZE
       ════════════════════════════════════════════════ */
    public static function fetch_row($id) {
        global $wpdb;
        $table = $wpdb->prefix . 'pi_email_campaigns';
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id), ARRAY_A);
        return $row ?: null;
    }

    private static function shape_row($r, $templates_map = []) {
        if (!$r) return null;
        $audience = json_decode((string) ($r['audience_filter'] ?? '{}'), true);
        $sent  = (int) $r['sent_count'];
        $open  = (int) $r['open_count'];
        $click = (int) $r['click_count'];
        $tpl_id = (int) $r['template_id'];
        return [
            'id'              => (int) $r['id'],
            'name'            => (string) $r['name'],
            'template_id'     => $tpl_id,
            'template_name'   => isset($templates_map[$tpl_id]) ? $templates_map[$tpl_id] : '',
            'audience_filter' => is_array($audience) ? $audience : [],
            'scheduled_at'    => $r['scheduled_at'],
            'status'          => (string) $r['status'],
            'sent_count'      => $sent,
            'open_count'      => $open,
            'click_count'     => $click,
            'open_rate'       => $sent > 0 ? round(($open / $sent) * 100, 2)  : 0,
            'click_rate'      => $sent > 0 ? round(($click / $sent) * 100, 2) : 0,
            'created_at'      => (string) $r['created_at'],
            'updated_at'      => (string) $r['updated_at'],
        ];
    }

    private static function sanitize_input($p, $existing = null) {
        if (!is_array($p)) $p = [];
        $allowed = ['draft', 'scheduled', 'sending', 'sent', 'cancelled'];
        $status  = sanitize_key((string) ($p['status'] ?? ($existing['status'] ?? 'draft')));
        if (!in_array($status, $allowed, true)) $status = 'draft';

        $audience = isset($p['audience_filter']) && is_array($p['audience_filter']) ? $p['audience_filter'] : [];
        $segment  = sanitize_key((string) ($audience['segment'] ?? 'all'));
        $clean_aud = ['segment' => $segment];
        foreach (['status', 'source', 'tag'] as $k) {
            if (isset($audience[$k])) $clean_aud[$k] = sanitize_text_field((string) $audience[$k]);
        }

        $sched = isset($p['scheduled_at']) ? trim((string) $p['scheduled_at']) : ($existing['scheduled_at'] ?? null);
        if ($sched === '' || $sched === null) {
            $sched = null;
        } else {
            // Accept ISO-8601 / "YYYY-MM-DD HH:MM:SS"
            $ts = strtotime($sched);
            $sched = $ts ? gmdate('Y-m-d H:i:s', $ts) : null;
        }

        return [
            'name'            => sanitize_text_field((string) ($p['name'] ?? ($existing['name'] ?? ''))),
            'template_id'     => (int) ($p['template_id'] ?? ($existing['template_id'] ?? 0)),
            'audience_filter' => $clean_aud,
            'scheduled_at'    => $sched,
            'status'          => $status,
        ];
    }
}
