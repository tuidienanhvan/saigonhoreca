<?php
/**
 * Pi API Analytics Endpoints
 *
 * Provides:
 *   GET    /analytics/reports                       — legacy page-view summary (wp_pi_analytics)
 *   GET    /analytics/funnels                       — list funnel definitions
 *   POST   /analytics/funnels                       — upsert a funnel
 *   DELETE /analytics/funnels/{id}                  — delete a funnel
 *   GET    /analytics/funnels/{id}/results          — compute funnel conversion counts
 *   GET    /analytics/retention                     — cohort retention matrix
 *   GET    /analytics/events                        — event log / aggregation
 *   POST   /analytics/events/track                  — public event ingest (rate-limited)
 *   GET    /analytics/post-views/top                — top posts by view count
 *
 * Storage:
 *   wp_pi_analytics_events      — event log (per-hit rows)
 *   wp_pi_analytics_sessions    — session tracking (session_hash, first_seen, last_seen)
 *   wp_pi_analytics_pageviews   — (alias table — kept for forward-compat; pageviews also live
 *                                  as rows in wp_pi_analytics_events with event_name='pageview')
 *   option pi_analytics_funnels — array of funnel definitions
 */

if (!defined('ABSPATH')) exit;

class Pi_Api_Analytics {

    const OPT_FUNNELS = 'pi_analytics_funnels';
    const EVENT_RATE_LIMIT_PER_MINUTE = 120;

    /* ═══════════════════════════════════════════════════════════════════════
       ROUTE REGISTRATION
       ═══════════════════════════════════════════════════════════════════════ */

    public static function register_routes($ns, $admin) {
        // Existing (legacy) — keep
        register_rest_route($ns, '/analytics/reports', [
            'methods'  => 'GET',
            'callback' => [self::class, 'get_reports'],
            'permission_callback' => $admin,
        ]);

        // Funnels
        register_rest_route($ns, '/analytics/funnels', [
            ['methods' => 'GET',  'callback' => [self::class, 'list_funnels'], 'permission_callback' => $admin],
            ['methods' => 'POST', 'callback' => [self::class, 'save_funnel'],  'permission_callback' => $admin],
        ]);
        register_rest_route($ns, '/analytics/funnels/(?P<id>[A-Za-z0-9_-]+)', [
            ['methods' => 'GET',    'callback' => [self::class, 'get_funnel'],    'permission_callback' => $admin],
            ['methods' => 'PUT',    'callback' => [self::class, 'update_funnel'], 'permission_callback' => $admin],
            ['methods' => 'DELETE', 'callback' => [self::class, 'delete_funnel'], 'permission_callback' => $admin],
        ]);
        register_rest_route($ns, '/analytics/funnels/(?P<id>[A-Za-z0-9_-]+)/results', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'funnel_results'],
            'permission_callback' => $admin,
        ]);
        register_rest_route($ns, '/analytics/funnels/(?P<id>[A-Za-z0-9_-]+)/duplicate', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'duplicate_funnel'],
            'permission_callback' => $admin,
        ]);

        // Retention
        register_rest_route($ns, '/analytics/retention', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'retention_matrix'],
            'permission_callback' => $admin,
        ]);

        // Events (admin — listing / aggregation)
        register_rest_route($ns, '/analytics/events', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'list_events'],
            'permission_callback' => $admin,
        ]);

        // Events tracking (PUBLIC — no auth, rate-limited)
        register_rest_route($ns, '/analytics/events/track', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'track_event'],
            'permission_callback' => '__return_true',
        ]);

        // Post views — top
        register_rest_route($ns, '/analytics/post-views/top', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'top_post_views'],
            'permission_callback' => $admin,
        ]);

        // Ensure tables exist on first route registration.
        self::maybe_create_tables();
    }

    /* ═══════════════════════════════════════════════════════════════════════
       SCHEMA BOOTSTRAP
       ═══════════════════════════════════════════════════════════════════════ */

    public static function maybe_create_tables() {
        global $wpdb;
        $db_ver  = (string) get_option('pi_analytics_api_db_ver', '0');
        if ($db_ver === '1.1') return;

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        $charset = $wpdb->get_charset_collate();

        $events    = $wpdb->prefix . 'pi_analytics_events';
        $sessions  = $wpdb->prefix . 'pi_analytics_sessions';
        $pageviews = $wpdb->prefix . 'pi_analytics_pageviews';

        dbDelta("CREATE TABLE {$events} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            event_name varchar(64) NOT NULL,
            user_id bigint(20) unsigned NOT NULL DEFAULT 0,
            session_hash varchar(64) NOT NULL DEFAULT '',
            page_url varchar(500) NOT NULL DEFAULT '',
            page_id bigint(20) unsigned NOT NULL DEFAULT 0,
            referrer varchar(500) NOT NULL DEFAULT '',
            ip_hash varchar(64) NOT NULL DEFAULT '',
            device varchar(16) NOT NULL DEFAULT '',
            country char(2) NOT NULL DEFAULT '',
            meta longtext,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY event_created (event_name, created_at),
            KEY page_created (page_id, created_at),
            KEY session (session_hash),
            KEY user_event (user_id, event_name)
        ) {$charset};");

        dbDelta("CREATE TABLE {$sessions} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            session_hash varchar(64) NOT NULL,
            user_id bigint(20) unsigned NOT NULL DEFAULT 0,
            first_seen datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            last_seen datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            pageviews int(11) NOT NULL DEFAULT 0,
            events int(11) NOT NULL DEFAULT 0,
            device varchar(16) NOT NULL DEFAULT '',
            country char(2) NOT NULL DEFAULT '',
            PRIMARY KEY (id),
            UNIQUE KEY session_hash (session_hash),
            KEY first_seen (first_seen),
            KEY user_id (user_id)
        ) {$charset};");

        dbDelta("CREATE TABLE {$pageviews} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            session_hash varchar(64) NOT NULL DEFAULT '',
            user_id bigint(20) unsigned NOT NULL DEFAULT 0,
            page_url varchar(500) NOT NULL DEFAULT '',
            page_id bigint(20) unsigned NOT NULL DEFAULT 0,
            referrer varchar(500) NOT NULL DEFAULT '',
            device varchar(16) NOT NULL DEFAULT '',
            time_on_page int(11) NOT NULL DEFAULT 0,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY session (session_hash),
            KEY page_created (page_id, created_at),
            KEY created_at (created_at)
        ) {$charset};");

        update_option('pi_analytics_api_db_ver', '1.1', false);
    }

    private static function events_table(): string {
        global $wpdb;
        return $wpdb->prefix . 'pi_analytics_events';
    }

    private static function sessions_table(): string {
        global $wpdb;
        return $wpdb->prefix . 'pi_analytics_sessions';
    }

    private static function pageviews_table(): string {
        global $wpdb;
        return $wpdb->prefix . 'pi_analytics_pageviews';
    }

    private static function event_table_exists(): bool {
        global $wpdb;
        $t = self::events_table();
        return $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $t)) === $t;
    }

    /* ═══════════════════════════════════════════════════════════════════════
       LEGACY REPORT (wp_pi_analytics)
       ═══════════════════════════════════════════════════════════════════════ */

    public static function get_reports(\WP_REST_Request $request) {
        $days = (int) ($request->get_param('days') ?: 30);
        $days = max(1, min(180, $days));
        
        $cache_key = "pi_analytics_reports_{$days}";
        $cached = get_transient($cache_key);
        if ($cached !== false) {
            return new \WP_REST_Response($cached, 200);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'pi_analytics';

        if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table)) !== $table) {
            return new \WP_REST_Response([
                'daily_visits' => [], 'top_pages' => [], 'devices' => [], 'referrers' => [],
                'total_visits' => 0, 'total_uniques' => 0,
                'today' => ['views'=>0, 'uniques'=>0], 'yesterday' => ['views'=>0, 'uniques'=>0],
                'bounce_rate' => 0, 'avg_time_on_site' => 0, 'traffic_sources' => [], 'browsers' => [], 'previous_period' => [], 'live_visitors' => 0
            ], 200);
        }

        $from = date('Y-m-d', strtotime("-{$days} days"));
        $to   = date('Y-m-d');

        $today_dt = date('Y-m-d');
        $yesterday_dt = date('Y-m-d', strtotime('-1 day'));

        $today = $wpdb->get_row($wpdb->prepare("SELECT COUNT(*) as views, SUM(is_unique) as uniques FROM $table WHERE visit_date = %s", $today_dt));
        $yesterday = $wpdb->get_row($wpdb->prepare("SELECT COUNT(*) as views, SUM(is_unique) as uniques FROM $table WHERE visit_date = %s", $yesterday_dt));

        $daily_visits = $wpdb->get_results($wpdb->prepare(
            "SELECT visit_date AS date, COUNT(*) AS views, SUM(is_unique) AS uniques FROM $table WHERE visit_date BETWEEN %s AND %s GROUP BY visit_date ORDER BY visit_date ASC",
            $from, $to
        ));

        $top_pages = $wpdb->get_results($wpdb->prepare(
            "SELECT page_url AS path, COUNT(*) AS views, SUM(is_unique) AS uniques FROM $table WHERE visit_date BETWEEN %s AND %s GROUP BY page_url ORDER BY views DESC LIMIT 15",
            $from, $to
        ));

        $devices = $wpdb->get_results($wpdb->prepare(
            "SELECT device, COUNT(*) AS count FROM $table WHERE visit_date BETWEEN %s AND %s GROUP BY device ORDER BY count DESC",
            $from, $to
        ));

        $referrers = $wpdb->get_results($wpdb->prepare(
            "SELECT CASE WHEN referrer = '' OR referrer IS NULL THEN 'Direct' ELSE SUBSTRING_INDEX(SUBSTRING_INDEX(REPLACE(REPLACE(referrer,'https://',''),'http://',''),'/',1),'?',1) END as source, COUNT(*) AS count FROM $table WHERE visit_date BETWEEN %s AND %s GROUP BY source ORDER BY count DESC LIMIT 15",
            $from, $to
        ));

        $totals = $wpdb->get_row($wpdb->prepare("SELECT COUNT(*) as total_visits, SUM(is_unique) as total_uniques FROM $table WHERE visit_date BETWEEN %s AND %s", $from, $to));

        // --- NEW METRICS FOR H-030 ---
        $bounce_rate = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT ROUND(100 * SUM(CASE WHEN page_count = 1 THEN 1 ELSE 0 END) / COUNT(*))
            FROM (
                SELECT session_id, COUNT(*) as page_count
                FROM {$table}
                WHERE visit_date >= %s AND session_id != ''
                GROUP BY session_id
            ) AS sessions",
            $from
        ));

        $avg_time = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT AVG(session_duration)
             FROM (
                 SELECT session_id, MAX(session_duration) as session_duration
                 FROM {$table}
                 WHERE visit_date >= %s AND session_id != ''
                 GROUP BY session_id
             ) AS s",
            $from
        ));

        // Categorize referrers into traffic_sources
        $sources_map = ['direct' => 0, 'search' => 0, 'social' => 0, 'referral' => 0];
        $referrers_raw = $wpdb->get_results($wpdb->prepare("SELECT referrer, COUNT(*) as count FROM $table WHERE visit_date BETWEEN %s AND %s GROUP BY referrer", $from, $to));
        foreach ($referrers_raw as $row) {
            $ref = strtolower(parse_url($row->referrer, PHP_URL_HOST) ?: '');
            if (empty($ref)) { $cat = 'direct'; }
            elseif (preg_match('/google|bing|yahoo|duckduckgo|baidu/', $ref)) { $cat = 'search'; }
            elseif (preg_match('/facebook|twitter|instagram|tiktok|linkedin|youtube|zalo/', $ref)) { $cat = 'social'; }
            else { $cat = 'referral'; }
            $sources_map[$cat] += (int) $row->count;
        }
        $total_sources = array_sum($sources_map) ?: 1;
        $traffic_sources = [];
        foreach ($sources_map as $source => $count) {
            if ($count === 0) continue;
            $traffic_sources[] = [
                'source'  => ucfirst($source),
                'count'   => $count,
                'percent' => round($count * 100 / $total_sources, 1),
            ];
        }

        // Browsers
        $ua_rows = $wpdb->get_results($wpdb->prepare("SELECT user_agent, COUNT(*) as count FROM $table WHERE visit_date BETWEEN %s AND %s GROUP BY user_agent", $from, $to));
        $browser_map = [];
        foreach ($ua_rows as $row) {
            $ua = $row->user_agent;
            if (preg_match('/Edg\//i', $ua)) $b = 'Edge';
            elseif (preg_match('/Chrome\//i', $ua)) $b = 'Chrome';
            elseif (preg_match('/Firefox\//i', $ua)) $b = 'Firefox';
            elseif (preg_match('/Safari\//i', $ua) && !preg_match('/Chrome/i', $ua)) $b = 'Safari';
            elseif (preg_match('/Opera|OPR\//i', $ua)) $b = 'Opera';
            else $b = 'Other';
            $browser_map[$b] = ($browser_map[$b] ?? 0) + (int) $row->count;
        }
        arsort($browser_map);
        $total_ua = array_sum($browser_map) ?: 1;
        $browsers = [];
        foreach ($browser_map as $b => $count) {
            $browsers[] = [
                'browser' => $b,
                'count'   => $count,
                'percent' => round($count * 100 / $total_ua, 1)
            ];
        }

        // Previous period measurements
        $prev_start = date('Y-m-d', strtotime("-{$days} days", strtotime($from)));
        $prev_end   = date('Y-m-d', strtotime("-1 day", strtotime($from)));
        $prev_totals = $wpdb->get_row($wpdb->prepare("SELECT COUNT(*) as total_visits, SUM(is_unique) as total_uniques FROM $table WHERE visit_date BETWEEN %s AND %s", $prev_start, $prev_end));
        $prev_bounce_rate = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT ROUND(100 * SUM(CASE WHEN page_count = 1 THEN 1 ELSE 0 END) / COUNT(*))
            FROM (SELECT session_id, COUNT(*) as page_count FROM {$table} WHERE visit_date BETWEEN %s AND %s AND session_id != '' GROUP BY session_id) AS s",
            $prev_start, $prev_end
        ));
        $prev_avg_time = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT AVG(session_duration) FROM (SELECT session_id, MAX(session_duration) as session_duration FROM {$table} WHERE visit_date BETWEEN %s AND %s AND session_id != '' GROUP BY session_id) AS s",
            $prev_start, $prev_end
        ));
        $previous_period = [
            'total_visits'  => (int) ($prev_totals->total_visits ?? 0),
            'total_uniques' => (int) ($prev_totals->total_uniques ?? 0),
            'bounce_rate'   => $prev_bounce_rate,
            'avg_time_on_site' => $prev_avg_time
        ];

        // Live visitors polling (active sessions in last 5 minutes)
        $live = (int) $wpdb->get_var("SELECT COUNT(DISTINCT session_id) FROM {$table} WHERE session_id != '' AND created_at >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)");

        $data = [
            'daily_visits'  => $daily_visits,
            'top_pages'     => $top_pages,
            'devices'       => $devices,
            'referrers'     => $referrers,
            'total_visits'  => (int) ($totals->total_visits ?? 0),
            'total_uniques' => (int) ($totals->total_uniques ?? 0),
            'today'         => ['views' => (int) ($today->views ?? 0), 'uniques' => (int) ($today->uniques ?? 0)],
            'yesterday'     => ['views' => (int) ($yesterday->views ?? 0), 'uniques' => (int) ($yesterday->uniques ?? 0)],
            // H-030 Extensions
            'bounce_rate'      => $bounce_rate,
            'avg_time_on_site' => $avg_time,
            'traffic_sources'  => $traffic_sources,
            'browsers'         => $browsers,
            'previous_period'  => $previous_period,
            'live_visitors'    => $live,
            'last_update'      => current_time('c'),
        ];

        // Cache the entire payload for 5 minutes (300 TTL)
        set_transient($cache_key, $data, 5 * MINUTE_IN_SECONDS);

        return new \WP_REST_Response($data, 200);
    }

    /* ═══════════════════════════════════════════════════════════════════════
       FUNNELS
       ═══════════════════════════════════════════════════════════════════════ */

    private static function read_funnels(): array {
        $raw = get_option(self::OPT_FUNNELS, []);
        return is_array($raw) ? array_values($raw) : [];
    }

    private static function write_funnels(array $list): void {
        update_option(self::OPT_FUNNELS, array_values($list), false);
    }

    private static function sanitize_funnel(array $in): array {
        $id = isset($in['id']) && is_string($in['id']) ? preg_replace('/[^A-Za-z0-9_-]/', '', $in['id']) : '';
        $is_new = $id === '';
        if ($is_new) {
            $id = 'fn_' . substr(md5(uniqid('', true)), 0, 10);
        }
        $name        = isset($in['name']) ? sanitize_text_field((string) $in['name']) : 'Phễu mới';
        $description = isset($in['description']) ? sanitize_textarea_field((string) $in['description']) : '';
        $window      = isset($in['window_hours']) ? max(1, min(720, (int) $in['window_hours'])) : 24;
        $allowed_event_types = ['page_view', 'click', 'form_submit', 'custom'];
        $steps   = [];
        if (!empty($in['steps']) && is_array($in['steps'])) {
            foreach ($in['steps'] as $order => $s) {
                if (!is_array($s)) continue;
                $event_type = isset($s['event_type']) && in_array($s['event_type'], $allowed_event_types, true)
                    ? $s['event_type']
                    : 'custom';
                $event = isset($s['event_name']) ? sanitize_text_field((string) $s['event_name']) : '';
                // Backward-compat: if event_name missing but event_type is page_view, use 'pageview'.
                if ($event === '') {
                    if ($event_type === 'page_view')   $event = 'pageview';
                    elseif ($event_type === 'click')   $event = 'click';
                    elseif ($event_type === 'form_submit') $event = 'form_submit';
                    else continue; // require event_name for custom
                }
                $match_rules = [];
                if (!empty($s['match_rules']) && is_array($s['match_rules'])) {
                    foreach ($s['match_rules'] as $k => $v) {
                        $sk = sanitize_key((string) $k);
                        if ($sk === '') continue;
                        $match_rules[$sk] = is_scalar($v) ? sanitize_text_field((string) $v) : '';
                    }
                }
                // Conditions (additional filters on user/session properties).
                $conditions = [];
                if (!empty($s['conditions']) && is_array($s['conditions'])) {
                    foreach ($s['conditions'] as $c) {
                        if (!is_array($c)) continue;
                        $field = isset($c['field']) ? sanitize_key((string) $c['field']) : '';
                        $op    = isset($c['op']) ? sanitize_key((string) $c['op']) : 'eq';
                        $val   = isset($c['value']) ? sanitize_text_field((string) $c['value']) : '';
                        if ($field === '') continue;
                        $conditions[] = ['field' => $field, 'op' => $op, 'value' => $val];
                    }
                }
                $steps[] = [
                    'label'       => isset($s['label']) ? sanitize_text_field((string) $s['label']) : $event,
                    'event_type'  => $event_type,
                    'event_name'  => $event,
                    'match_rules' => $match_rules,
                    'conditions'  => $conditions,
                    'order'       => isset($s['order']) ? (int) $s['order'] : (int) $order,
                ];
            }
        }
        // Sort by order to keep stable.
        usort($steps, fn($a, $b) => ($a['order'] ?? 0) <=> ($b['order'] ?? 0));

        // A/B variants (optional).
        $ab_variants = [];
        if (!empty($in['ab_variants']) && is_array($in['ab_variants'])) {
            foreach ($in['ab_variants'] as $v) {
                if (!is_array($v)) continue;
                $vname = isset($v['name']) ? sanitize_text_field((string) $v['name']) : '';
                if ($vname === '') continue;
                $traffic = isset($v['traffic']) ? max(0, min(100, (int) $v['traffic'])) : 50;
                $mods    = [];
                if (!empty($v['modifications']) && is_array($v['modifications'])) {
                    foreach ($v['modifications'] as $mk => $mv) {
                        $mks = sanitize_key((string) $mk);
                        if ($mks === '') continue;
                        $mods[$mks] = is_scalar($mv) ? sanitize_text_field((string) $mv) : '';
                    }
                }
                $ab_variants[] = [
                    'name'          => $vname,
                    'traffic'       => $traffic,
                    'modifications' => $mods,
                ];
            }
        }

        return [
            'id'           => $id,
            'name'         => $name,
            'description'  => $description,
            'window_hours' => $window,
            'steps'        => $steps,
            'ab_variants'  => $ab_variants,
            'created_at'   => isset($in['created_at']) ? (string) $in['created_at'] : current_time('mysql'),
            'updated_at'   => current_time('mysql'),
        ];
    }

    public static function get_funnel(\WP_REST_Request $request) {
        $id = (string) $request['id'];
        foreach (self::read_funnels() as $f) {
            if (($f['id'] ?? '') === $id) {
                return new \WP_REST_Response(['funnel' => $f], 200);
            }
        }
        return new \WP_Error('not_found', 'Không tìm thấy phễu.', ['status' => 404]);
    }

    public static function update_funnel(\WP_REST_Request $request) {
        $id = (string) $request['id'];
        $body = $request->get_json_params();
        if (!is_array($body)) {
            return new \WP_Error('invalid_body', 'Body phải là JSON object.', ['status' => 400]);
        }
        $body['id'] = $id;
        $sanitized = self::sanitize_funnel($body);
        if (empty($sanitized['steps'])) {
            return new \WP_Error('no_steps', 'Phễu cần ít nhất một bước.', ['status' => 400]);
        }
        $list = self::read_funnels();
        $found = false;
        foreach ($list as $i => $f) {
            if (($f['id'] ?? '') === $id) {
                // Preserve created_at.
                $sanitized['created_at'] = $f['created_at'] ?? $sanitized['created_at'];
                $list[$i] = $sanitized;
                $found = true;
                break;
            }
        }
        if (!$found) {
            return new \WP_Error('not_found', 'Không tìm thấy phễu.', ['status' => 404]);
        }
        self::write_funnels($list);
        return new \WP_REST_Response(['funnel' => $sanitized], 200);
    }

    public static function duplicate_funnel(\WP_REST_Request $request) {
        $id = (string) $request['id'];
        $src = null;
        foreach (self::read_funnels() as $f) {
            if (($f['id'] ?? '') === $id) { $src = $f; break; }
        }
        if (!$src) {
            return new \WP_Error('not_found', 'Không tìm thấy phễu.', ['status' => 404]);
        }
        $copy = $src;
        unset($copy['id']);
        $copy['name'] = ($src['name'] ?? 'Phễu') . ' (bản sao)';
        $copy['created_at'] = current_time('mysql');
        $sanitized = self::sanitize_funnel($copy);
        $list = self::read_funnels();
        $list[] = $sanitized;
        self::write_funnels($list);
        return new \WP_REST_Response(['funnel' => $sanitized], 200);
    }

    public static function list_funnels(\WP_REST_Request $request) {
        return new \WP_REST_Response([
            'funnels' => self::read_funnels(),
        ], 200);
    }

    public static function save_funnel(\WP_REST_Request $request) {
        $body = $request->get_json_params();
        if (!is_array($body)) {
            return new \WP_Error('invalid_body', 'Body phải là JSON object.', ['status' => 400]);
        }
        $sanitized = self::sanitize_funnel($body);
        if (empty($sanitized['steps'])) {
            return new \WP_Error('no_steps', 'Phễu cần ít nhất một bước.', ['status' => 400]);
        }
        $list = self::read_funnels();
        $replaced = false;
        foreach ($list as $i => $f) {
            if (isset($f['id']) && $f['id'] === $sanitized['id']) {
                $list[$i] = $sanitized;
                $replaced = true;
                break;
            }
        }
        if (!$replaced) $list[] = $sanitized;
        self::write_funnels($list);
        return new \WP_REST_Response(['funnel' => $sanitized], 200);
    }

    public static function delete_funnel(\WP_REST_Request $request) {
        $id = (string) $request['id'];
        $list = self::read_funnels();
        $new  = array_values(array_filter($list, fn($f) => ($f['id'] ?? '') !== $id));
        self::write_funnels($new);
        return new \WP_REST_Response(['deleted' => true, 'id' => $id], 200);
    }

    public static function funnel_results(\WP_REST_Request $request) {
        global $wpdb;
        $id   = (string) $request['id'];
        // Accept either `days` or `period` (e.g. '7d', '30d', '90d').
        $period = (string) ($request->get_param('period') ?: '');
        if ($period !== '' && preg_match('/^(\d+)d$/', $period, $m)) {
            $days = (int) $m[1];
        } else {
            $days = (int) ($request->get_param('days') ?: 30);
        }
        $days = max(1, min(180, $days));

        $funnel = null;
        foreach (self::read_funnels() as $f) {
            if (($f['id'] ?? '') === $id) { $funnel = $f; break; }
        }
        if (!$funnel) {
            return new \WP_Error('not_found', 'Không tìm thấy phễu.', ['status' => 404]);
        }

        $steps = $funnel['steps'] ?? [];
        if (empty($steps)) {
            return new \WP_REST_Response(['funnel' => $funnel, 'total_entries' => 0, 'steps' => []], 200);
        }

        if (!self::event_table_exists()) {
            // No data: return empty results.
            return new \WP_REST_Response([
                'funnel'        => $funnel,
                'total_entries' => 0,
                'steps'         => array_map(fn($s) => [
                    'label' => $s['label'] ?? $s['event_name'],
                    'event_name' => $s['event_name'],
                    'count' => 0,
                    'conversion_rate' => 0,
                    'drop_off' => 0,
                ], $steps),
            ], 200);
        }

        $events_table = self::events_table();
        $from  = gmdate('Y-m-d H:i:s', time() - ($days * DAY_IN_SECONDS));
        $window_seconds = (int) ($funnel['window_hours'] ?? 24) * HOUR_IN_SECONDS;

        // STEP 1: find entries — sessions that fired the first step's event in window.
        $first = $steps[0];
        $first_rows = $wpdb->get_results($wpdb->prepare(
            "SELECT session_hash, MIN(created_at) AS ts
             FROM {$events_table}
             WHERE event_name = %s
               AND created_at >= %s
               AND session_hash != ''
             GROUP BY session_hash",
            $first['event_name'], $from
        ));

        $entries = [];
        foreach ($first_rows as $r) {
            if (self::row_matches_rules($r, $first, $events_table)) {
                $entries[$r->session_hash] = $r->ts;
            }
        }
        $total_entries = count($entries);
        $counts = [$total_entries];

        // Subsequent steps: for each session in entries, check if the step event
        // fired after the previous step's timestamp AND within the window.
        $prev_timestamps = $entries;
        for ($i = 1; $i < count($steps); $i++) {
            $step = $steps[$i];
            $matched = [];
            foreach ($prev_timestamps as $session => $prev_ts) {
                $window_end = gmdate('Y-m-d H:i:s', strtotime($entries[$session]) + $window_seconds);
                $row = $wpdb->get_row($wpdb->prepare(
                    "SELECT session_hash, MIN(created_at) AS ts
                     FROM {$events_table}
                     WHERE session_hash = %s
                       AND event_name = %s
                       AND created_at >= %s
                       AND created_at <= %s
                     GROUP BY session_hash",
                    $session, $step['event_name'], $prev_ts, $window_end
                ));
                if ($row && self::row_matches_rules($row, $step, $events_table)) {
                    $matched[$session] = $row->ts;
                }
            }
            $counts[] = count($matched);
            $prev_timestamps = $matched;
        }

        // Build response steps.
        $out_steps = [];
        foreach ($steps as $i => $s) {
            $count = (int) ($counts[$i] ?? 0);
            $conv  = $total_entries > 0 ? round(($count / $total_entries) * 100, 2) : 0;
            $drop  = 0;
            $drop_count = 0;
            if ($i > 0) {
                $prev = (int) ($counts[$i - 1] ?? 0);
                $drop_count = max(0, $prev - $count);
                $drop = $prev > 0 ? round((($prev - $count) / $prev) * 100, 2) : 0;
            }
            $out_steps[] = [
                'label'           => $s['label'] ?? $s['event_name'],
                'event_name'      => $s['event_name'],
                'event_type'      => $s['event_type'] ?? 'custom',
                'count'           => $count,
                'conversion_rate' => $conv,
                'drop_off'        => $drop,
                'drop_count'      => $drop_count,
            ];
        }

        // Avg time-to-convert: difference between first step's ts and last step's ts
        // for sessions that completed all steps.
        $avg_seconds = 0;
        $completed_sessions = $prev_timestamps; // sessions that reached the last step
        if (!empty($completed_sessions)) {
            $deltas = [];
            foreach ($completed_sessions as $session => $last_ts) {
                $start = isset($entries[$session]) ? strtotime($entries[$session]) : 0;
                $end   = strtotime($last_ts);
                if ($start > 0 && $end > $start) {
                    $deltas[] = $end - $start;
                }
            }
            if (!empty($deltas)) {
                $avg_seconds = (int) round(array_sum($deltas) / count($deltas));
            }
        }

        // Time-series of conversions per day (sessions that completed the full funnel).
        $time_series = [];
        if (!empty($completed_sessions)) {
            $by_day = [];
            foreach ($completed_sessions as $session => $last_ts) {
                $day = substr($last_ts, 0, 10);
                $by_day[$day] = ($by_day[$day] ?? 0) + 1;
            }
            // Fill missing days with 0.
            for ($d = $days - 1; $d >= 0; $d--) {
                $day = gmdate('Y-m-d', time() - ($d * DAY_IN_SECONDS));
                $time_series[] = [
                    'date'        => $day,
                    'conversions' => (int) ($by_day[$day] ?? 0),
                ];
            }
        }

        $final_count = (int) ($counts[count($counts) - 1] ?? 0);
        $overall_conv = $total_entries > 0 ? round(($final_count / $total_entries) * 100, 2) : 0;

        return new \WP_REST_Response([
            'funnel'              => $funnel,
            'total_entries'       => $total_entries,
            'total_converted'     => $final_count,
            'conversion_rate'     => $overall_conv,
            'avg_time_seconds'    => $avg_seconds,
            'days'                => $days,
            'period'              => $days . 'd',
            'steps'               => $out_steps,
            'time_series'         => $time_series,
        ], 200);
    }

    /**
     * Check that a single event row satisfies a step's match_rules.
     * match_rules = { url_contains?: string, meta_key?: value }
     */
    private static function row_matches_rules($row, array $step, string $events_table): bool {
        $rules = $step['match_rules'] ?? [];
        if (empty($rules)) return true;

        // We may need to fetch the row's page_url / meta — the GROUP BY query in
        // the caller only returns session_hash + ts. Fetch one matching row.
        global $wpdb;
        $full = $wpdb->get_row($wpdb->prepare(
            "SELECT page_url, meta FROM {$events_table}
             WHERE session_hash = %s AND event_name = %s AND created_at = %s
             LIMIT 1",
            $row->session_hash, $step['event_name'], $row->ts
        ));
        if (!$full) return false;

        if (!empty($rules['url_contains'])) {
            if (stripos((string) $full->page_url, $rules['url_contains']) === false) return false;
        }
        $meta = [];
        if (!empty($full->meta)) {
            $decoded = json_decode((string) $full->meta, true);
            if (is_array($decoded)) $meta = $decoded;
        }
        foreach ($rules as $k => $v) {
            if ($k === 'url_contains') continue;
            $actual = $meta[$k] ?? null;
            if ((string) $actual !== (string) $v) return false;
        }
        return true;
    }

    /* ═══════════════════════════════════════════════════════════════════════
       RETENTION MATRIX
       ═══════════════════════════════════════════════════════════════════════ */

    public static function retention_matrix(\WP_REST_Request $request) {
        global $wpdb;
        $period       = (string) ($request->get_param('period') ?: 'week'); // 'week' | 'month'
        $cohort_count = (int)    ($request->get_param('cohort_count') ?: 8);
        $cohort_count = max(2, min(24, $cohort_count));
        $period       = in_array($period, ['week', 'month'], true) ? $period : 'week';

        if (!self::event_table_exists()) {
            return new \WP_REST_Response([
                'period'       => $period,
                'cohort_count' => $cohort_count,
                'cohorts'      => [],
                'columns'      => [],
            ], 200);
        }

        $events = self::events_table();
        $unit_seconds = $period === 'month' ? 30 * DAY_IN_SECONDS : 7 * DAY_IN_SECONDS;

        // Determine the cohort window: the last `cohort_count` periods ending today.
        $now = time();
        $cohort_ranges = [];
        for ($i = $cohort_count - 1; $i >= 0; $i--) {
            $start = $now - (($i + 1) * $unit_seconds);
            $end   = $now - ($i * $unit_seconds);
            $cohort_ranges[] = [
                'label' => gmdate('Y-m-d', $start),
                'start' => gmdate('Y-m-d H:i:s', $start),
                'end'   => gmdate('Y-m-d H:i:s', $end),
            ];
        }

        // Pull all sessions and their first seen within the entire window.
        $earliest = $cohort_ranges[0]['start'];
        $rows = $wpdb->get_results($wpdb->prepare(
            "SELECT session_hash, MIN(created_at) AS first_seen, MAX(created_at) AS last_seen
             FROM {$events}
             WHERE created_at >= %s AND session_hash != ''
             GROUP BY session_hash",
            $earliest
        ));

        // Map session -> cohort index; then group sessions by cohort.
        $cohort_sessions = array_fill(0, $cohort_count, []);
        foreach ($rows as $r) {
            $ft = strtotime($r->first_seen);
            $idx = (int) floor(($ft - strtotime($earliest)) / $unit_seconds);
            if ($idx < 0 || $idx >= $cohort_count) continue;
            $cohort_sessions[$idx][$r->session_hash] = $r->last_seen;
        }

        // For each cohort, compute retention at week/month 0..(cohort_count-1-idx).
        $cohorts = [];
        foreach ($cohort_ranges as $idx => $range) {
            $sessions   = $cohort_sessions[$idx];
            $size       = count($sessions);
            $remaining  = $cohort_count - $idx;
            $values     = array_fill(0, $remaining, null);

            if ($size > 0) {
                $cohort_start = strtotime($range['start']);
                foreach ($sessions as $hash => $last_seen) {
                    $lt  = strtotime($last_seen);
                    $max = (int) floor(($lt - $cohort_start) / $unit_seconds);
                    for ($n = 0; $n <= $max && $n < $remaining; $n++) {
                        if ($values[$n] === null) $values[$n] = 0;
                        $values[$n]++;
                    }
                }
                // Convert to percentages (relative to cohort size).
                for ($n = 0; $n < $remaining; $n++) {
                    $count = (int) ($values[$n] ?? 0);
                    $values[$n] = [
                        'count' => $count,
                        'pct'   => $size > 0 ? round(($count / $size) * 100, 1) : 0,
                    ];
                }
            } else {
                for ($n = 0; $n < $remaining; $n++) {
                    $values[$n] = ['count' => 0, 'pct' => 0];
                }
            }

            $cohorts[] = [
                'cohort'    => $range['label'],
                'size'      => $size,
                'retention' => $values,
            ];
        }

        $columns = [];
        $label = $period === 'month' ? 'Tháng' : 'Tuần';
        for ($i = 0; $i < $cohort_count; $i++) $columns[] = "$label $i";

        return new \WP_REST_Response([
            'period'       => $period,
            'cohort_count' => $cohort_count,
            'columns'      => $columns,
            'cohorts'      => $cohorts,
        ], 200);
    }

    /* ═══════════════════════════════════════════════════════════════════════
       EVENTS — listing / aggregation
       ═══════════════════════════════════════════════════════════════════════ */

    public static function list_events(\WP_REST_Request $request) {
        global $wpdb;
        $event_name = (string) ($request->get_param('event_name') ?: '');
        $days       = (int)    ($request->get_param('days') ?: 30);
        $days       = max(1, min(365, $days));
        $group_by   = (string) ($request->get_param('group_by') ?: '');
        $group_by   = in_array($group_by, ['day', 'url', 'user', 'event'], true) ? $group_by : '';
        $page       = max(1, (int) ($request->get_param('page') ?: 1));
        $per_page   = min(200, max(10, (int) ($request->get_param('per_page') ?: 50)));

        if (!self::event_table_exists()) {
            return new \WP_REST_Response([
                'total' => 0, 'page' => $page, 'per_page' => $per_page,
                'group_by' => $group_by, 'rows' => [], 'distinct_events' => [],
            ], 200);
        }

        $events = self::events_table();
        $from   = gmdate('Y-m-d H:i:s', time() - ($days * DAY_IN_SECONDS));

        $where = 'created_at >= %s';
        $args  = [$from];
        if ($event_name !== '') {
            $where .= ' AND event_name = %s';
            $args[] = $event_name;
        }

        // Distinct event names for filter dropdown.
        $distinct = $wpdb->get_col($wpdb->prepare(
            "SELECT DISTINCT event_name FROM {$events} WHERE created_at >= %s ORDER BY event_name ASC LIMIT 50",
            $from
        ));

        if ($group_by !== '') {
            // Aggregation mode
            $group_map = [
                'day'   => 'DATE(created_at)',
                'url'   => 'page_url',
                'user'  => 'user_id',
                'event' => 'event_name',
            ];
            $label_map = [
                'day'   => 'DATE(created_at) AS label',
                'url'   => 'page_url AS label',
                'user'  => 'CAST(user_id AS CHAR) AS label',
                'event' => 'event_name AS label',
            ];
            $group_sql = $group_map[$group_by];
            $label_sql = $label_map[$group_by];
            $sql = "SELECT {$label_sql}, COUNT(*) AS count, COUNT(DISTINCT session_hash) AS uniques
                    FROM {$events} WHERE {$where}
                    GROUP BY {$group_sql}
                    ORDER BY count DESC
                    LIMIT 200";
            $rows = $wpdb->get_results($wpdb->prepare($sql, ...$args));

            return new \WP_REST_Response([
                'group_by'        => $group_by,
                'days'            => $days,
                'event_name'      => $event_name,
                'distinct_events' => array_values($distinct ?: []),
                'rows'            => $rows ?: [],
            ], 200);
        }

        // List mode (paginated)
        $offset = ($page - 1) * $per_page;
        $total = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$events} WHERE {$where}",
            ...$args
        ));

        $sql  = "SELECT id, event_name, user_id, session_hash, page_url, referrer,
                        device, meta, created_at
                 FROM {$events} WHERE {$where}
                 ORDER BY created_at DESC
                 LIMIT %d OFFSET %d";
        $args_page = array_merge($args, [$per_page, $offset]);
        $rows = $wpdb->get_results($wpdb->prepare($sql, ...$args_page));

        // Decode meta
        foreach ($rows as &$r) {
            if (!empty($r->meta)) {
                $decoded = json_decode((string) $r->meta, true);
                $r->meta = is_array($decoded) ? $decoded : [];
            } else {
                $r->meta = [];
            }
        }
        unset($r);

        return new \WP_REST_Response([
            'total'           => $total,
            'page'            => $page,
            'per_page'        => $per_page,
            'days'            => $days,
            'event_name'      => $event_name,
            'distinct_events' => array_values($distinct ?: []),
            'rows'            => $rows ?: [],
        ], 200);
    }

    /* ═══════════════════════════════════════════════════════════════════════
       EVENT INGEST (public)
       ═══════════════════════════════════════════════════════════════════════ */

    public static function track_event(\WP_REST_Request $request) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

        // Rate limit — per IP, per minute.
        $key = 'pi_anl_rl_' . md5($ip);
        $hits = (int) get_transient($key);
        if ($hits >= self::EVENT_RATE_LIMIT_PER_MINUTE) {
            return new \WP_Error('rate_limited', 'Too many events.', ['status' => 429]);
        }
        set_transient($key, $hits + 1, MINUTE_IN_SECONDS);

        $body = $request->get_json_params();
        if (!is_array($body)) {
            // Allow form-encoded fallback.
            $body = [
                'event_name' => $request->get_param('event_name'),
                'meta'       => $request->get_param('meta'),
                'url'        => $request->get_param('url'),
                'referrer'   => $request->get_param('referrer'),
            ];
        }

        $event_name = isset($body['event_name']) ? sanitize_key((string) $body['event_name']) : '';
        if ($event_name === '' || strlen($event_name) > 64) {
            return new \WP_Error('invalid_event_name', 'event_name required (<=64 chars, slug).', ['status' => 400]);
        }

        // Bot filter
        $ua = (string) ($_SERVER['HTTP_USER_AGENT'] ?? '');
        if (preg_match('/bot|crawl|spider|slurp|facebookexternalhit|Googlebot/i', $ua)) {
            return new \WP_REST_Response(['ok' => true, 'skipped' => 'bot'], 200);
        }

        $meta = $body['meta'] ?? [];
        if (!is_array($meta)) $meta = [];
        // Drop any super-large meta.
        $meta_json = wp_json_encode($meta);
        if ($meta_json === false || strlen($meta_json) > 4096) {
            $meta = [];
        }

        // Endpoint công khai: cắt url/referrer để không ghi chuỗi dài tuỳ ý vào DB.
        $url      = mb_substr(esc_url_raw((string) ($body['url'] ?? '')), 0, 2048);
        $referrer = mb_substr(esc_url_raw((string) ($body['referrer'] ?? '')), 0, 2048);

        $user_id = get_current_user_id();
        pi_analytics_log_event($event_name, [
            'url'      => $url,
            'referrer' => $referrer,
            'meta'     => $meta,
        ], $user_id ?: null);

        return new \WP_REST_Response(['ok' => true], 200);
    }

    /* ═══════════════════════════════════════════════════════════════════════
       TOP POSTS BY VIEWS
       ═══════════════════════════════════════════════════════════════════════ */

    public static function top_post_views(\WP_REST_Request $request) {
        global $wpdb;
        $days  = max(1, min(365, (int) ($request->get_param('days') ?: 30)));
        $limit = max(1, min(100, (int) ($request->get_param('limit') ?: 20)));

        $items = [];

        // Prefer event-based data if the events table has rows.
        if (self::event_table_exists()) {
            $events = self::events_table();
            $from   = gmdate('Y-m-d H:i:s', time() - ($days * DAY_IN_SECONDS));
            $items  = $wpdb->get_results($wpdb->prepare(
                "SELECT page_id, page_url,
                        COUNT(*)              AS views,
                        COUNT(DISTINCT session_hash) AS uniques
                 FROM {$events}
                 WHERE (event_name = %s OR event_name = %s)
                   AND created_at >= %s
                   AND page_id > 0
                 GROUP BY page_id
                 ORDER BY views DESC
                 LIMIT %d",
                'pageview', 'page_view', $from, $limit
            ));
        }

        // Fallback to legacy sh_analytics (uses URL).
        if (empty($items)) {
            $legacy = $wpdb->prefix . 'sh_analytics';
            if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $legacy)) === $legacy) {
                $from = gmdate('Y-m-d', time() - ($days * DAY_IN_SECONDS));
                $items = $wpdb->get_results($wpdb->prepare(
                    "SELECT post_id AS page_id, page_url,
                            COUNT(*)        AS views,
                            SUM(is_unique)  AS uniques
                     FROM {$legacy}
                     WHERE visit_date >= %s AND post_id > 0
                     GROUP BY post_id
                     ORDER BY views DESC
                     LIMIT %d",
                    $from, $limit
                ));
            }
        }

        // Enrich with post title + permalink.
        $out = [];
        foreach ((array) $items as $it) {
            $pid = (int) ($it->page_id ?? 0);
            $post = $pid > 0 ? get_post($pid) : null;
            $out[] = [
                'post_id'   => $pid,
                'title'     => $post ? get_the_title($post) : (string) ($it->page_url ?? '(unknown)'),
                'url'       => $post ? get_permalink($post) : (string) ($it->page_url ?? ''),
                'views'     => (int) ($it->views ?? 0),
                'uniques'   => (int) ($it->uniques ?? 0),
                'post_type' => $post ? $post->post_type : '',
                'status'    => $post ? $post->post_status : '',
            ];
        }

        return new \WP_REST_Response([
            'days'  => $days,
            'limit' => $limit,
            'items' => $out,
        ], 200);
    }
}

/**
 * Plugin-scope helper — insert a row into wp_pi_analytics_events.
 * Safe to call from any plugin/module once the table exists.
 *
 * @param string $event_name
 * @param array  $meta     Optional payload (url, referrer, meta, page_id, device…)
 * @param int|null $user_id Override current user id.
 * @return int Inserted row id or 0 on failure.
 */
if (!function_exists('pi_analytics_log_event')) {
    function pi_analytics_log_event(string $event_name, array $meta = [], ?int $user_id = null): int {
        global $wpdb;
        if (class_exists('Pi_Api_Analytics')) {
            \Pi_Api_Analytics::maybe_create_tables();
        }
        $table = $wpdb->prefix . 'pi_analytics_events';
        if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table)) !== $table) {
            return 0;
        }

        $event_name = substr(sanitize_key($event_name), 0, 64);
        if ($event_name === '') return 0;

        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $ua = (string) ($_SERVER['HTTP_USER_AGENT'] ?? '');

        $device = 'desktop';
        $lc = strtolower($ua);
        if (str_contains($lc, 'mobi')) $device = 'mobile';
        elseif (str_contains($lc, 'tablet') || str_contains($lc, 'ipad')) $device = 'tablet';

        $salt         = defined('SGH_ANALYTICS_SALT') ? SGH_ANALYTICS_SALT : wp_salt();
        $ip_hash      = substr(hash('sha256', $ip . $salt), 0, 64);
        $session_hash = substr(hash('sha256', $ip . $ua . $salt), 0, 64);

        $url      = isset($meta['url']) ? esc_url_raw((string) $meta['url']) : '';
        $referrer = isset($meta['referrer']) ? esc_url_raw((string) $meta['referrer']) : '';
        $page_id  = isset($meta['page_id']) ? (int) $meta['page_id'] : 0;
        $payload  = isset($meta['meta']) && is_array($meta['meta']) ? $meta['meta'] : [];
        $meta_json = wp_json_encode($payload);
        if ($meta_json === false) $meta_json = '{}';

        $uid = is_int($user_id) ? $user_id : (int) get_current_user_id();

        $ok = $wpdb->insert($table, [
            'event_name'   => $event_name,
            'user_id'      => $uid,
            'session_hash' => $session_hash,
            'page_url'     => mb_substr($url, 0, 500),
            'page_id'      => $page_id,
            'referrer'     => mb_substr($referrer, 0, 500),
            'ip_hash'      => $ip_hash,
            'device'       => $device,
            'meta'         => $meta_json,
        ]);
        if (!$ok) return 0;

        // Touch session row.
        $sessions = $wpdb->prefix . 'pi_analytics_sessions';
        if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $sessions)) === $sessions) {
            $existing = $wpdb->get_var($wpdb->prepare(
                "SELECT id FROM {$sessions} WHERE session_hash = %s LIMIT 1",
                $session_hash
            ));
            if ($existing) {
                $wpdb->query($wpdb->prepare(
                    "UPDATE {$sessions}
                     SET last_seen = CURRENT_TIMESTAMP,
                         events = events + 1,
                         pageviews = pageviews + %d
                     WHERE session_hash = %s",
                    $event_name === 'pageview' ? 1 : 0,
                    $session_hash
                ));
            } else {
                $wpdb->insert($sessions, [
                    'session_hash' => $session_hash,
                    'user_id'      => $uid,
                    'device'       => $device,
                    'pageviews'    => $event_name === 'pageview' ? 1 : 0,
                    'events'       => 1,
                ]);
            }
        }

        return (int) $wpdb->insert_id;
    }
}
