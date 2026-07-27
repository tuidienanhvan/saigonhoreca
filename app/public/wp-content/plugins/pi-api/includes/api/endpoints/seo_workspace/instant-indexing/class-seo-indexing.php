<?php
/**
 * Pi API SEO Instant Indexing Endpoints
 *
 * Submits URLs to Google's Indexing API. Reuses GSC OAuth credentials
 * (Pi_Api_SeoGsc::get_access_token via reflection-friendly accessor).
 *
 * NOTE: Google's Web Search Indexing API is officially supported only for
 * JobPosting / BroadcastEvent schema. For general URL_UPDATED notifications
 * we still call the same endpoint — Google may ignore non-eligible types
 * but submission itself succeeds and we record response codes.
 *
 * Schema:
 *   wp_pi_indexing_queue (id, url, type, status, response JSON, submitted_at)
 *
 * Routes:
 *   POST /pi/v1/seo/indexing/submit       { urls: [], type: 'URL_UPDATED|URL_DELETED' }
 *   GET  /pi/v1/seo/indexing/queue
 *   GET  /pi/v1/seo/indexing/history?limit=50
 *   POST /pi/v1/seo/indexing/auto-config  { enabled, post_types: [] }
 *   GET  /pi/v1/seo/indexing/auto-config
 *
 * Hooks:
 *   transition_post_status — auto-queue on publish if enabled.
 *
 * @package Pi_Api
 */

if (!defined('ABSPATH')) exit;

class Pi_Api_SeoIndexing {

    const TABLE = 'pi_indexing_queue';
    const OPT_AUTO_ENABLED = 'pi_indexing_auto_enabled';
    const OPT_AUTO_TYPES   = 'pi_indexing_auto_post_types';
    const INDEXING_API     = 'https://indexing.googleapis.com/v3/urlNotifications:publish';

    /* ═══════════════════════════════════════════════════════════════════════
       SCHEMA
       ═══════════════════════════════════════════════════════════════════════ */

    public static function maybe_install() {
        global $wpdb;
        $table = $wpdb->prefix . self::TABLE;
        $exists = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table));
        if ($exists === $table) return;

        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $table (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            url VARCHAR(2083) NOT NULL,
            type VARCHAR(32) NOT NULL DEFAULT 'URL_UPDATED',
            status VARCHAR(16) NOT NULL DEFAULT 'pending',
            response LONGTEXT NULL,
            response_code SMALLINT UNSIGNED NULL,
            submitted_at DATETIME NULL,
            queued_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY status (status),
            KEY queued_at (queued_at)
        ) $charset_collate;";
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    /* ═══════════════════════════════════════════════════════════════════════
       ROUTES
       ═══════════════════════════════════════════════════════════════════════ */

    public static function register_routes($ns, $admin) {
        self::maybe_install();

        register_rest_route($ns, '/seo/indexing/submit', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'submit'],
            'permission_callback' => $admin,
        ]);
        register_rest_route($ns, '/seo/indexing/queue', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'queue'],
            'permission_callback' => $admin,
        ]);
        register_rest_route($ns, '/seo/indexing/history', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'history'],
            'permission_callback' => $admin,
        ]);
        register_rest_route($ns, '/seo/indexing/auto-config', [
            ['methods' => 'GET',  'callback' => [self::class, 'get_auto_config'],  'permission_callback' => $admin],
            ['methods' => 'POST', 'callback' => [self::class, 'set_auto_config'],  'permission_callback' => $admin],
        ]);
        register_rest_route($ns, '/seo/indexing/process', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'process_queue'],
            'permission_callback' => $admin,
        ]);
        register_rest_route($ns, '/seo/indexing/stats', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'stats'],
            'permission_callback' => $admin,
        ]);

        // Bootstrap auto-submit hook
        add_action('transition_post_status', [self::class, 'on_post_publish'], 10, 3);
    }

    /* ═══════════════════════════════════════════════════════════════════════
       HOOK — auto queue on publish
       ═══════════════════════════════════════════════════════════════════════ */

    public static function on_post_publish($new_status, $old_status, $post) {
        if ($new_status !== 'publish' || $old_status === 'publish') return;
        if (!get_option(self::OPT_AUTO_ENABLED, false)) return;

        $allowed = (array) get_option(self::OPT_AUTO_TYPES, ['post', 'page']);
        if (!in_array((string) $post->post_type, $allowed, true)) return;

        $url = (string) get_permalink((int) $post->ID);
        if ($url === '') return;

        self::enqueue($url, 'URL_UPDATED');
    }

    private static function enqueue($url, $type) {
        global $wpdb;
        $wpdb->insert($wpdb->prefix . self::TABLE, [
            'url'       => esc_url_raw($url),
            'type'      => in_array($type, ['URL_UPDATED', 'URL_DELETED'], true) ? $type : 'URL_UPDATED',
            'status'    => 'pending',
            'queued_at' => current_time('mysql'),
        ]);
    }

    /* ═══════════════════════════════════════════════════════════════════════
       SUBMIT (manual)
       ═══════════════════════════════════════════════════════════════════════ */

    public static function submit(\WP_REST_Request $request) {
        $body = $request->get_json_params();
        if (!is_array($body)) {
            return new \WP_REST_Response(['success' => false, 'message' => 'Body phải là JSON'], 400);
        }
        $urls = array_filter(array_map('trim', (array) ($body['urls'] ?? [])));
        $type = (string) ($body['type'] ?? 'URL_UPDATED');

        if (empty($urls)) {
            return new \WP_REST_Response(['success' => false, 'message' => 'Thiếu danh sách URL.'], 400);
        }

        $enqueued = 0;
        foreach ($urls as $u) {
            if (!filter_var($u, FILTER_VALIDATE_URL)) continue;
            self::enqueue($u, $type);
            $enqueued++;
        }

        // Process immediately if user requested
        $processed = self::process_pending(50);

        return new \WP_REST_Response([
            'success'   => true,
            'enqueued'  => $enqueued,
            'processed' => $processed,
            'message'   => "Đã thêm $enqueued URL vào hàng đợi.",
        ], 200);
    }

    /* ═══════════════════════════════════════════════════════════════════════
       PROCESS PENDING
       ═══════════════════════════════════════════════════════════════════════ */

    public static function process_queue(\WP_REST_Request $request) {
        $body = $request->get_json_params();
        $limit = max(1, min(200, (int) ($body['limit'] ?? 50)));
        $count = self::process_pending($limit);
        return new \WP_REST_Response([
            'success' => true,
            'processed' => $count,
            'message'   => "Đã xử lý $count URL.",
        ], 200);
    }

    /**
     * Process up to $limit pending rows. Returns processed count.
     */
    private static function process_pending($limit = 50) {
        global $wpdb;
        $table = $wpdb->prefix . self::TABLE;

        $rows = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM $table WHERE status='pending' ORDER BY queued_at ASC LIMIT %d", $limit),
            ARRAY_A
        );
        if (!$rows) return 0;

        $token = self::access_token();
        $count = 0;

        foreach ($rows as $row) {
            $resp_data = null;
            $code = 0;
            $status = 'failed';

            if (!$token) {
                $resp_data = ['error' => 'No OAuth token. Connect Google first.'];
            } else {
                $payload = wp_json_encode([
                    'url'  => (string) $row['url'],
                    'type' => (string) $row['type'],
                ]);
                $resp = wp_remote_post(self::INDEXING_API, [
                    'timeout' => 30,
                    'headers' => [
                        'Authorization' => 'Bearer ' . $token,
                        'Content-Type'  => 'application/json',
                    ],
                    'body' => $payload,
                ]);
                if (is_wp_error($resp)) {
                    $resp_data = ['error' => $resp->get_error_message()];
                } else {
                    $code = (int) wp_remote_retrieve_response_code($resp);
                    $body = (string) wp_remote_retrieve_body($resp);
                    $resp_data = json_decode($body, true) ?: ['raw' => $body];
                    $status = ($code >= 200 && $code < 300) ? 'success' : 'failed';
                }
            }

            $wpdb->update($table, [
                'status'        => $status,
                'response'      => wp_json_encode($resp_data),
                'response_code' => $code,
                'submitted_at'  => current_time('mysql'),
            ], ['id' => (int) $row['id']]);
            $count++;
        }
        return $count;
    }

    /**
     * Pull access token from GSC integration. The method is private over there
     * so we replicate the refresh logic minimally — only need a valid Bearer.
     */
    private static function access_token() {
        if (!class_exists('Pi_Api_SeoGsc')) return '';
        $access  = (string) get_option(\Pi_Api_SeoGsc::OPT_ACCESS_TOKEN, '');
        $expires = (int)    get_option(\Pi_Api_SeoGsc::OPT_ACCESS_EXPIRES, 0);
        $refresh = (string) get_option(\Pi_Api_SeoGsc::OPT_REFRESH_TOKEN, '');

        if ($access !== '' && $expires > (time() + 60)) return $access;
        if ($refresh === '') return '';

        $client_id     = (string) get_option(\Pi_Api_SeoGsc::OPT_CLIENT_ID, '');
        $client_secret = (string) get_option(\Pi_Api_SeoGsc::OPT_CLIENT_SECRET, '');
        if ($client_id === '' || $client_secret === '') return '';

        $resp = wp_remote_post(\Pi_Api_SeoGsc::TOKEN_URL, [
            'timeout' => 20,
            'body'    => [
                'client_id'     => $client_id,
                'client_secret' => $client_secret,
                'refresh_token' => $refresh,
                'grant_type'    => 'refresh_token',
            ],
        ]);
        if (is_wp_error($resp)) return '';
        $data = json_decode((string) wp_remote_retrieve_body($resp), true);
        if (!is_array($data) || empty($data['access_token'])) return '';

        update_option(\Pi_Api_SeoGsc::OPT_ACCESS_TOKEN, (string) $data['access_token'], false);
        update_option(\Pi_Api_SeoGsc::OPT_ACCESS_EXPIRES, time() + (int) ($data['expires_in'] ?? 3600), false);
        return (string) $data['access_token'];
    }

    /* ═══════════════════════════════════════════════════════════════════════
       READ ENDPOINTS
       ═══════════════════════════════════════════════════════════════════════ */

    public static function queue() {
        global $wpdb;
        $rows = $wpdb->get_results(
            "SELECT * FROM " . $wpdb->prefix . self::TABLE . " WHERE status='pending' ORDER BY queued_at ASC LIMIT 200",
            ARRAY_A
        );
        return new \WP_REST_Response([
            'success' => true,
            'queue'   => is_array($rows) ? $rows : [],
        ], 200);
    }

    public static function history(\WP_REST_Request $request) {
        global $wpdb;
        $limit = max(1, min(500, (int) ($request->get_param('limit') ?: 50)));
        $rows = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM " . $wpdb->prefix . self::TABLE . " WHERE status<>'pending' ORDER BY submitted_at DESC LIMIT %d",
                $limit
            ),
            ARRAY_A
        );
        $rows = is_array($rows) ? $rows : [];
        foreach ($rows as &$r) {
            if (!empty($r['response'])) {
                $r['response'] = json_decode((string) $r['response'], true);
            }
        }
        return new \WP_REST_Response([
            'success' => true,
            'history' => $rows,
        ], 200);
    }

    public static function get_auto_config() {
        return new \WP_REST_Response([
            'success'    => true,
            'enabled'    => (bool) get_option(self::OPT_AUTO_ENABLED, false),
            'post_types' => (array) get_option(self::OPT_AUTO_TYPES, ['post', 'page']),
        ], 200);
    }

    public static function set_auto_config(\WP_REST_Request $request) {
        $body = $request->get_json_params();
        if (!is_array($body)) {
            return new \WP_REST_Response(['success' => false, 'message' => 'Body phải là JSON'], 400);
        }
        update_option(self::OPT_AUTO_ENABLED, !empty($body['enabled']), false);
        $types = array_filter(array_map('sanitize_key', (array) ($body['post_types'] ?? [])));
        update_option(self::OPT_AUTO_TYPES, array_values($types), false);
        return new \WP_REST_Response([
            'success' => true,
            'message' => 'Đã lưu cấu hình tự động.',
        ], 200);
    }

    public static function stats() {
        global $wpdb;
        $table = $wpdb->prefix . self::TABLE;
        $thirty = gmdate('Y-m-d H:i:s', time() - 30 * 86400);
        $total = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table WHERE submitted_at >= %s", $thirty
        ));
        $success = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table WHERE submitted_at >= %s AND status='success'", $thirty
        ));
        $pending = (int) $wpdb->get_var(
            "SELECT COUNT(*) FROM $table WHERE status='pending'"
        );
        return new \WP_REST_Response([
            'success'      => true,
            'last_30_days' => $total,
            'success_count' => $success,
            'success_rate' => $total ? round(($success / $total) * 100, 1) : 0,
            'pending'      => $pending,
        ], 200);
    }
}
