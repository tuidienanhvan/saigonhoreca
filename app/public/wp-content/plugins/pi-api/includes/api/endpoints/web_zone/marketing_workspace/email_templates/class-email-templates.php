<?php
/**
 * Pi API Email Templates Endpoints
 *
 * - GET    /email-templates                    List all templates
 * - GET    /email-templates/{id}               Single template
 * - POST   /email-templates                    Create new template
 * - PUT    /email-templates/{id}               Update template
 * - DELETE /email-templates/{id}               Delete template
 * - POST   /email-templates/{id}/preview       Render with mock vars (HTML)
 * - POST   /email-templates/{id}/test-send     Send a test email
 *
 * Schema: wp_pi_email_templates
 *   id, name, subject, body_html, body_text, variables(JSON), category, created_at, updated_at
 *
 * Variables system:
 *   {{variable}} placeholders. Pre-defined keys:
 *     lead.name | lead.email | lead.phone | lead.company
 *     site.name | site.url
 *     date.today
 *   Custom variables saved per template.
 */

if (!defined('ABSPATH')) exit;

class Pi_Api_EmailTemplates {

    /* ════════════════════════════════════════════════
       SCHEMA
       ════════════════════════════════════════════════ */
    public static function ensure_table() {
        global $wpdb;
        $table   = $wpdb->prefix . 'pi_email_templates';
        $charset = $wpdb->get_charset_collate();

        if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table)) === $table) return;

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        $sql = "CREATE TABLE $table (
            id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            name         VARCHAR(190) NOT NULL DEFAULT '',
            subject      VARCHAR(255) NOT NULL DEFAULT '',
            body_html    LONGTEXT NOT NULL,
            body_text    LONGTEXT NOT NULL,
            variables    LONGTEXT NOT NULL,
            category     VARCHAR(64) NOT NULL DEFAULT 'general',
            created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY idx_category (category),
            KEY idx_updated (updated_at)
        ) $charset;";
        dbDelta($sql);
    }

    /* ════════════════════════════════════════════════
       ROUTE REGISTRATION
       ════════════════════════════════════════════════ */
    public static function register_routes($ns, $admin) {
        self::ensure_table();

        register_rest_route($ns, '/email-templates', [
            ['methods' => 'GET',  'callback' => [self::class, 'list_templates'],  'permission_callback' => $admin],
            ['methods' => 'POST', 'callback' => [self::class, 'create_template'], 'permission_callback' => $admin],
        ]);
        register_rest_route($ns, '/email-templates/(?P<id>\d+)', [
            ['methods' => 'GET',    'callback' => [self::class, 'get_template'],    'permission_callback' => $admin],
            ['methods' => 'PUT',    'callback' => [self::class, 'update_template'], 'permission_callback' => $admin],
            ['methods' => 'POST',   'callback' => [self::class, 'update_template'], 'permission_callback' => $admin], // alias
            ['methods' => 'DELETE', 'callback' => [self::class, 'delete_template'], 'permission_callback' => $admin],
        ]);
        register_rest_route($ns, '/email-templates/(?P<id>\d+)/preview', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'preview_template'],
            'permission_callback' => $admin,
        ]);
        register_rest_route($ns, '/email-templates/(?P<id>\d+)/test-send', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'test_send'],
            'permission_callback' => $admin,
        ]);
    }

    /* ════════════════════════════════════════════════
       LIST
       ════════════════════════════════════════════════ */
    public static function list_templates(\WP_REST_Request $request) {
        global $wpdb;
        $table = $wpdb->prefix . 'pi_email_templates';

        $search   = trim((string) $request->get_param('search'));
        $category = trim((string) $request->get_param('category'));

        $where  = ['1=1'];
        $params = [];
        if ($search !== '') {
            $where[]  = "(name LIKE %s OR subject LIKE %s)";
            $like     = '%' . $wpdb->esc_like($search) . '%';
            $params[] = $like;
            $params[] = $like;
        }
        if ($category !== '' && $category !== 'all') {
            $where[]  = 'category = %s';
            $params[] = $category;
        }

        $sql = "SELECT id, name, subject, category, updated_at, created_at
                FROM $table
                WHERE " . implode(' AND ', $where) . "
                ORDER BY updated_at DESC
                LIMIT 500";
        $sql = $params ? $wpdb->prepare($sql, $params) : $sql;
        $rows = (array) $wpdb->get_results($sql, ARRAY_A);

        return new \WP_REST_Response([
            'templates' => array_map([self::class, 'shape_row_summary'], $rows),
            'count'     => count($rows),
        ], 200);
    }

    /* ════════════════════════════════════════════════
       SINGLE
       ════════════════════════════════════════════════ */
    public static function get_template(\WP_REST_Request $request) {
        $id  = (int) $request->get_param('id');
        $row = self::fetch_row($id);
        if (!$row) {
            return new \WP_REST_Response(['message' => 'Template không tồn tại'], 404);
        }
        return new \WP_REST_Response(self::shape_row_full($row), 200);
    }

    /* ════════════════════════════════════════════════
       CREATE
       ════════════════════════════════════════════════ */
    public static function create_template(\WP_REST_Request $request) {
        global $wpdb;
        $table = $wpdb->prefix . 'pi_email_templates';

        $clean = self::sanitize_input($request->get_json_params());
        if (empty($clean['name'])) {
            return new \WP_REST_Response(['message' => 'Thiếu tên template'], 400);
        }

        $now = current_time('mysql', true);
        $ok  = $wpdb->insert($table, [
            'name'        => $clean['name'],
            'subject'     => $clean['subject'],
            'body_html'   => $clean['body_html'],
            'body_text'   => $clean['body_text'],
            'variables'   => wp_json_encode($clean['variables']),
            'category'    => $clean['category'],
            'created_at'  => $now,
            'updated_at'  => $now,
        ]);
        if ($ok === false) {
            return new \WP_REST_Response(['message' => 'Không thể tạo template'], 500);
        }

        $id  = (int) $wpdb->insert_id;
        $row = self::fetch_row($id);
        return new \WP_REST_Response(['success' => true, 'template' => self::shape_row_full($row)], 200);
    }

    /* ════════════════════════════════════════════════
       UPDATE
       ════════════════════════════════════════════════ */
    public static function update_template(\WP_REST_Request $request) {
        global $wpdb;
        $table = $wpdb->prefix . 'pi_email_templates';
        $id    = (int) $request->get_param('id');
        $row   = self::fetch_row($id);
        if (!$row) {
            return new \WP_REST_Response(['message' => 'Template không tồn tại'], 404);
        }

        $clean = self::sanitize_input($request->get_json_params(), $row);
        $ok = $wpdb->update($table, [
            'name'        => $clean['name'],
            'subject'     => $clean['subject'],
            'body_html'   => $clean['body_html'],
            'body_text'   => $clean['body_text'],
            'variables'   => wp_json_encode($clean['variables']),
            'category'    => $clean['category'],
            'updated_at'  => current_time('mysql', true),
        ], ['id' => $id]);
        if ($ok === false) {
            return new \WP_REST_Response(['message' => 'Không thể cập nhật template'], 500);
        }
        $row = self::fetch_row($id);
        return new \WP_REST_Response(['success' => true, 'template' => self::shape_row_full($row)], 200);
    }

    /* ════════════════════════════════════════════════
       DELETE
       ════════════════════════════════════════════════ */
    public static function delete_template(\WP_REST_Request $request) {
        global $wpdb;
        $table = $wpdb->prefix . 'pi_email_templates';
        $id    = (int) $request->get_param('id');
        $row   = self::fetch_row($id);
        if (!$row) {
            return new \WP_REST_Response(['message' => 'Template không tồn tại'], 404);
        }
        $wpdb->delete($table, ['id' => $id]);
        return new \WP_REST_Response(['success' => true], 200);
    }

    /* ════════════════════════════════════════════════
       PREVIEW
       ════════════════════════════════════════════════ */
    public static function preview_template(\WP_REST_Request $request) {
        $id  = (int) $request->get_param('id');
        $row = self::fetch_row($id);
        if (!$row) {
            return new \WP_REST_Response(['message' => 'Template không tồn tại'], 404);
        }
        $params  = $request->get_json_params();
        $vars_in = isset($params['variables']) && is_array($params['variables']) ? $params['variables'] : [];

        $vars = self::merge_with_mock_vars($row, $vars_in);

        $subject_rendered = self::render_with_vars($row['subject'], $vars);
        $html_rendered    = self::render_with_vars($row['body_html'], $vars);
        $text_rendered    = self::render_with_vars($row['body_text'], $vars);

        return new \WP_REST_Response([
            'success'   => true,
            'subject'   => $subject_rendered,
            'html'      => $html_rendered,
            'text'      => $text_rendered,
            'variables' => $vars,
        ], 200);
    }

    /* ════════════════════════════════════════════════
       TEST SEND
       ════════════════════════════════════════════════ */
    public static function test_send(\WP_REST_Request $request) {
        $id  = (int) $request->get_param('id');
        $row = self::fetch_row($id);
        if (!$row) {
            return new \WP_REST_Response(['message' => 'Template không tồn tại'], 404);
        }
        $params  = $request->get_json_params();
        $to      = sanitize_email((string) ($params['to'] ?? ''));
        $vars_in = isset($params['variables']) && is_array($params['variables']) ? $params['variables'] : [];

        if (!is_email($to)) {
            return new \WP_REST_Response(['message' => 'Địa chỉ email không hợp lệ'], 400);
        }

        $vars    = self::merge_with_mock_vars($row, $vars_in);
        $subject = self::render_with_vars($row['subject'], $vars);
        $html    = self::render_with_vars($row['body_html'], $vars);

        $headers = ['Content-Type: text/html; charset=UTF-8'];
        $sent    = wp_mail($to, $subject, $html, $headers);

        if (!$sent) {
            return new \WP_REST_Response(['message' => 'wp_mail() không gửi được — kiểm tra SMTP / mail logs'], 500);
        }
        return new \WP_REST_Response(['success' => true, 'sent_to' => $to], 200);
    }

    /* ════════════════════════════════════════════════
       PUBLIC HELPERS (used by EmailCampaigns class)
       ════════════════════════════════════════════════ */
    public static function fetch_row($id) {
        global $wpdb;
        $table = $wpdb->prefix . 'pi_email_templates';
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id), ARRAY_A);
        return $row ?: null;
    }

    public static function render_with_vars($template, $vars) {
        if (!is_string($template)) return '';
        $out = $template;
        foreach ((array) $vars as $key => $value) {
            $needle = '{{' . $key . '}}';
            $out    = str_replace($needle, (string) $value, $out);
        }
        // Tolerate {{ name }} (with surrounding spaces)
        $out = preg_replace_callback('/\{\{\s*([a-zA-Z0-9_.]+)\s*\}\}/', function ($m) use ($vars) {
            $k = $m[1];
            return isset($vars[$k]) ? (string) $vars[$k] : '';
        }, $out);
        return $out;
    }

    public static function default_mock_vars() {
        return [
            'lead.name'     => 'Nguyễn Văn A',
            'lead.email'    => 'demo@example.com',
            'lead.phone'    => '0901234567',
            'lead.company'  => 'Công ty TNHH ABC',
            'site.name'     => get_bloginfo('name'),
            'site.url'      => home_url(),
            'date.today'    => date_i18n('d/m/Y'),
        ];
    }

    /* ════════════════════════════════════════════════
       INTERNAL
       ════════════════════════════════════════════════ */
    private static function shape_row_summary($r) {
        return [
            'id'         => (int) $r['id'],
            'name'       => (string) $r['name'],
            'subject'    => (string) $r['subject'],
            'category'   => (string) $r['category'],
            'updated_at' => (string) $r['updated_at'],
            'created_at' => (string) $r['created_at'],
        ];
    }

    private static function shape_row_full($r) {
        $vars = json_decode((string) ($r['variables'] ?? '[]'), true);
        if (!is_array($vars)) $vars = [];
        return [
            'id'         => (int) $r['id'],
            'name'       => (string) $r['name'],
            'subject'    => (string) $r['subject'],
            'body_html'  => (string) $r['body_html'],
            'body_text'  => (string) $r['body_text'],
            'variables'  => $vars,
            'category'   => (string) $r['category'],
            'updated_at' => (string) $r['updated_at'],
            'created_at' => (string) $r['created_at'],
        ];
    }

    private static function sanitize_input($p, $existing = null) {
        if (!is_array($p)) $p = [];
        $variables = isset($p['variables']) && is_array($p['variables']) ? $p['variables'] : [];
        $clean_vars = [];
        foreach ($variables as $v) {
            if (!is_array($v)) continue;
            $key = sanitize_text_field((string) ($v['key'] ?? ''));
            if ($key === '') continue;
            $clean_vars[] = [
                'key'         => $key,
                'label'       => sanitize_text_field((string) ($v['label'] ?? $key)),
                'default'     => sanitize_text_field((string) ($v['default'] ?? '')),
                'description' => sanitize_text_field((string) ($v['description'] ?? '')),
            ];
        }

        return [
            'name'       => sanitize_text_field((string) ($p['name']      ?? ($existing['name']      ?? ''))),
            'subject'    => sanitize_text_field((string) ($p['subject']   ?? ($existing['subject']   ?? ''))),
            'body_html'  => wp_kses_post((string) ($p['body_html']        ?? ($existing['body_html'] ?? ''))),
            'body_text'  => (string) ($p['body_text']                     ?? ($existing['body_text'] ?? '')),
            'variables'  => $clean_vars,
            'category'   => sanitize_key((string) ($p['category']         ?? ($existing['category']  ?? 'general'))),
        ];
    }

    private static function merge_with_mock_vars($row, $custom_in) {
        $vars = self::default_mock_vars();
        // Add custom vars from template definition (defaults)
        $defs = json_decode((string) ($row['variables'] ?? '[]'), true);
        if (is_array($defs)) {
            foreach ($defs as $v) {
                if (!isset($v['key'])) continue;
                $vars[$v['key']] = (string) ($v['default'] ?? '');
            }
        }
        // Overlay caller-provided
        foreach ((array) $custom_in as $k => $v) {
            if (is_string($k)) $vars[$k] = (string) $v;
        }
        return $vars;
    }
}
