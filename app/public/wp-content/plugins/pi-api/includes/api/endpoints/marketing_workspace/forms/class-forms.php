<?php
/**
 * Pi API Forms Endpoints — Form Builder backend
 *
 * Routes:
 *   GET    /pi/v1/forms                       List all forms
 *   GET    /pi/v1/forms/{id}                  Single form
 *   POST   /pi/v1/forms                       Create form
 *   PUT    /pi/v1/forms/{id}                  Update form
 *   DELETE /pi/v1/forms/{id}                  Delete form
 *   GET    /pi/v1/forms/{id}/submissions      Paginated submissions
 *   POST   /pi/v1/forms/{id}/submit           Public submit (no auth)
 *   GET    /pi/v1/forms/{id}/embed-code       Shortcode + JS embed
 *
 * Tables:
 *   wp_pi_forms            (id, name, slug, fields JSON, settings JSON, created_at, updated_at)
 *   wp_pi_form_submissions (id, form_id, data JSON, ip, user_agent, status, created_at)
 *
 * Field types supported in fields JSON:
 *   text, textarea, email, phone, number, date, select, radio, checkbox, file
 */

if (!defined('ABSPATH')) exit;

class Pi_Api_Forms {

    /* ═══════════════════════════════════════════════════════════════
       SCHEMA — auto-create tables on first request
       ═══════════════════════════════════════════════════════════════ */
    public static function maybe_create_tables() {
        global $wpdb;
        $charset = $wpdb->get_charset_collate();
        $forms_table = $wpdb->prefix . 'pi_forms';
        $subs_table  = $wpdb->prefix . 'pi_form_submissions';

        $existing = get_option('pi_forms_schema_version', 0);
        if ($existing >= 1) return;

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $sql_forms = "CREATE TABLE $forms_table (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(190) NOT NULL DEFAULT '',
            slug VARCHAR(190) NOT NULL DEFAULT '',
            fields LONGTEXT NULL,
            settings LONGTEXT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY slug (slug)
        ) $charset;";

        $sql_subs = "CREATE TABLE $subs_table (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            form_id BIGINT(20) UNSIGNED NOT NULL,
            data LONGTEXT NULL,
            ip VARCHAR(64) NULL,
            user_agent VARCHAR(255) NULL,
            status VARCHAR(32) NOT NULL DEFAULT 'new',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY form_id (form_id),
            KEY status (status),
            KEY created_at (created_at)
        ) $charset;";

        dbDelta($sql_forms);
        dbDelta($sql_subs);
        update_option('pi_forms_schema_version', 1, false);
    }

    /* ═══════════════════════════════════════════════════════════════
       ROUTE REGISTRATION
       ═══════════════════════════════════════════════════════════════ */
    public static function register_routes($ns, $admin) {
        // Public callback for the submit endpoint — anyone can post.
        $public = '__return_true';

        register_rest_route($ns, '/forms', [
            ['methods' => 'GET',  'callback' => [self::class, 'list_forms'],   'permission_callback' => $admin],
            ['methods' => 'POST', 'callback' => [self::class, 'create_form'],  'permission_callback' => $admin],
        ]);

        register_rest_route($ns, '/forms/(?P<id>\d+)', [
            ['methods' => 'GET',    'callback' => [self::class, 'get_form'],    'permission_callback' => $admin],
            ['methods' => 'PUT',    'callback' => [self::class, 'update_form'], 'permission_callback' => $admin],
            ['methods' => 'DELETE', 'callback' => [self::class, 'delete_form'], 'permission_callback' => $admin],
        ]);

        register_rest_route($ns, '/forms/(?P<id>\d+)/submissions', [
            'methods'  => 'GET',
            'callback' => [self::class, 'list_submissions'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/forms/(?P<id>\d+)/submissions/(?P<sub_id>\d+)', [
            'methods'  => 'DELETE',
            'callback' => [self::class, 'delete_submission'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/forms/(?P<id>\d+)/submit', [
            'methods'  => 'POST',
            'callback' => [self::class, 'submit_form'],
            'permission_callback' => $public,
        ]);

        register_rest_route($ns, '/forms/(?P<id>\d+)/embed-code', [
            'methods'  => 'GET',
            'callback' => [self::class, 'get_embed_code'],
            'permission_callback' => $admin,
        ]);
    }

    /* ═══════════════════════════════════════════════════════════════
       LIST / GET / CREATE / UPDATE / DELETE
       ═══════════════════════════════════════════════════════════════ */
    public static function list_forms() {
        self::maybe_create_tables();
        global $wpdb;
        $forms_table = $wpdb->prefix . 'pi_forms';
        $subs_table  = $wpdb->prefix . 'pi_form_submissions';

        $rows = $wpdb->get_results("SELECT * FROM $forms_table ORDER BY updated_at DESC", ARRAY_A);
        if (!is_array($rows)) $rows = [];

        $out = [];
        foreach ($rows as $r) {
            $form = self::format_form_row($r);
            $form_id = (int) $r['id'];
            $sub_count = (int) $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $subs_table WHERE form_id = %d", $form_id
            ));
            $last = $wpdb->get_var($wpdb->prepare(
                "SELECT created_at FROM $subs_table WHERE form_id = %d ORDER BY created_at DESC LIMIT 1", $form_id
            ));
            $form['submissions_count'] = $sub_count;
            $form['last_submission_at'] = $last ? (string) $last : null;
            $form['fields_count'] = is_array($form['fields']) ? count($form['fields']) : 0;
            $out[] = $form;
        }
        return new \WP_REST_Response($out, 200);
    }

    public static function get_form(\WP_REST_Request $request) {
        self::maybe_create_tables();
        global $wpdb;
        $id = (int) $request->get_param('id');
        $row = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}pi_forms WHERE id = %d", $id
        ), ARRAY_A);
        if (!$row) return new \WP_REST_Response(['message' => 'Form không tồn tại'], 404);
        return new \WP_REST_Response(self::format_form_row($row), 200);
    }

    public static function create_form(\WP_REST_Request $request) {
        self::maybe_create_tables();
        global $wpdb;
        $params = $request->get_json_params();
        if (!is_array($params)) $params = [];

        $name = sanitize_text_field((string) ($params['name'] ?? 'Form mới'));
        $slug = self::unique_slug(sanitize_title($params['slug'] ?? $name));
        $fields   = self::sanitize_fields($params['fields']   ?? []);
        $settings = self::sanitize_settings($params['settings'] ?? []);

        $ok = $wpdb->insert($wpdb->prefix . 'pi_forms', [
            'name'     => $name,
            'slug'     => $slug,
            'fields'   => wp_json_encode($fields),
            'settings' => wp_json_encode($settings),
        ]);

        if (!$ok) {
            return new \WP_REST_Response(['message' => 'Không tạo được form'], 500);
        }

        $id = (int) $wpdb->insert_id;
        $row = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}pi_forms WHERE id = %d", $id
        ), ARRAY_A);
        return new \WP_REST_Response(self::format_form_row($row), 201);
    }

    public static function update_form(\WP_REST_Request $request) {
        self::maybe_create_tables();
        global $wpdb;
        $id = (int) $request->get_param('id');
        $params = $request->get_json_params();
        if (!is_array($params)) $params = [];

        $existing = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}pi_forms WHERE id = %d", $id
        ), ARRAY_A);
        if (!$existing) return new \WP_REST_Response(['message' => 'Form không tồn tại'], 404);

        $update = [];
        if (isset($params['name'])) $update['name'] = sanitize_text_field((string) $params['name']);
        if (isset($params['slug'])) {
            $clean_slug = sanitize_title($params['slug']);
            if ($clean_slug !== $existing['slug']) {
                $update['slug'] = self::unique_slug($clean_slug, $id);
            }
        }
        if (array_key_exists('fields', $params)) {
            $update['fields'] = wp_json_encode(self::sanitize_fields($params['fields']));
        }
        if (array_key_exists('settings', $params)) {
            $update['settings'] = wp_json_encode(self::sanitize_settings($params['settings']));
        }

        if (!empty($update)) {
            $wpdb->update($wpdb->prefix . 'pi_forms', $update, ['id' => $id]);
        }

        $row = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}pi_forms WHERE id = %d", $id
        ), ARRAY_A);
        return new \WP_REST_Response(self::format_form_row($row), 200);
    }

    public static function delete_form(\WP_REST_Request $request) {
        self::maybe_create_tables();
        global $wpdb;
        $id = (int) $request->get_param('id');
        $wpdb->delete($wpdb->prefix . 'pi_form_submissions', ['form_id' => $id]);
        $ok = $wpdb->delete($wpdb->prefix . 'pi_forms', ['id' => $id]);
        return new \WP_REST_Response(['success' => (bool) $ok], 200);
    }

    /* ═══════════════════════════════════════════════════════════════
       SUBMISSIONS — list / delete / public submit
       ═══════════════════════════════════════════════════════════════ */
    public static function list_submissions(\WP_REST_Request $request) {
        self::maybe_create_tables();
        global $wpdb;
        $id = (int) $request->get_param('id');
        $page = max(1, (int) ($request->get_param('page') ?: 1));
        $per_page = min(100, max(1, (int) ($request->get_param('per_page') ?: 20)));
        $offset = ($page - 1) * $per_page;

        $subs_table = $wpdb->prefix . 'pi_form_submissions';
        $total = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $subs_table WHERE form_id = %d", $id
        ));

        $rows = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $subs_table WHERE form_id = %d ORDER BY created_at DESC LIMIT %d OFFSET %d",
            $id, $per_page, $offset
        ), ARRAY_A);
        if (!is_array($rows)) $rows = [];

        $items = array_map([self::class, 'format_submission_row'], $rows);
        return new \WP_REST_Response([
            'items'      => $items,
            'total'      => $total,
            'page'       => $page,
            'per_page'   => $per_page,
            'total_pages' => (int) ceil($total / $per_page),
        ], 200);
    }

    public static function delete_submission(\WP_REST_Request $request) {
        self::maybe_create_tables();
        global $wpdb;
        $sub_id = (int) $request->get_param('sub_id');
        $form_id = (int) $request->get_param('id');
        $ok = $wpdb->delete($wpdb->prefix . 'pi_form_submissions', [
            'id' => $sub_id, 'form_id' => $form_id,
        ]);
        return new \WP_REST_Response(['success' => (bool) $ok], 200);
    }

    /** Requests per IP per minute — mirrors analytics/track_event's rate limit pattern. */
    const SUBMIT_RATE_LIMIT_PER_MINUTE = 5;

    public static function submit_form(\WP_REST_Request $request) {
        // Rate limit — per IP, per minute. This endpoint has no auth (public
        // submit) and triggers wp_insert_post + wp_mail + wp_remote_post per
        // request, so without a limit it's a trivial CRM-flood / mail-bomb /
        // webhook-amplification vector.
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $rl_key  = 'pi_forms_rl_' . md5($ip);
        $rl_hits = (int) get_transient($rl_key);
        if ($rl_hits >= self::SUBMIT_RATE_LIMIT_PER_MINUTE) {
            return new \WP_Error('rate_limited', 'Quá nhiều yêu cầu, thử lại sau.', ['status' => 429]);
        }
        set_transient($rl_key, $rl_hits + 1, MINUTE_IN_SECONDS);

        self::maybe_create_tables();
        global $wpdb;
        $id = (int) $request->get_param('id');

        $form = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}pi_forms WHERE id = %d", $id
        ), ARRAY_A);
        if (!$form) return new \WP_REST_Response(['message' => 'Form không tồn tại'], 404);

        $payload = $request->get_json_params();
        if (!is_array($payload)) {
            $payload = $request->get_body_params();
        }
        if (!is_array($payload)) $payload = [];

        // Honeypot: a hidden field ('_pi_hp') a human never sees or fills,
        // but naive bots that auto-fill every input do. Fake a normal
        // success response so the bot doesn't learn to avoid the field.
        if (!empty($payload['_pi_hp'])) {
            return new \WP_REST_Response([
                'success'         => true,
                'submission_id'   => 0,
                'success_message' => 'Cảm ơn bạn đã gửi thông tin.',
                'redirect_url'    => '',
            ], 200);
        }

        $fields_def = json_decode((string) $form['fields'], true);
        if (!is_array($fields_def)) $fields_def = [];
        $settings = json_decode((string) $form['settings'], true);
        if (!is_array($settings)) $settings = [];

        // Validate + sanitize against field definition
        $clean = [];
        $errors = [];
        foreach ($fields_def as $f) {
            $name = (string) ($f['name'] ?? '');
            if ($name === '') continue;
            $type = (string) ($f['type'] ?? 'text');
            $required = !empty($f['required']);
            $val = $payload[$name] ?? '';
            if (is_array($val)) {
                $val = array_map(function ($v) { return is_scalar($v) ? sanitize_text_field((string) $v) : ''; }, $val);
            } elseif (is_scalar($val)) {
                if ($type === 'textarea') $val = sanitize_textarea_field((string) $val);
                elseif ($type === 'email') $val = sanitize_email((string) $val);
                else $val = sanitize_text_field((string) $val);
            } else {
                $val = '';
            }
            if ($required) {
                $is_empty = (is_array($val) && empty($val)) || (is_string($val) && trim($val) === '');
                if ($is_empty) $errors[$name] = 'Trường bắt buộc';
            }
            if ($type === 'email' && !empty($val) && is_string($val) && !is_email($val)) {
                $errors[$name] = 'Email không hợp lệ';
            }
            $clean[$name] = $val;
        }

        if (!empty($errors)) {
            return new \WP_REST_Response(['success' => false, 'errors' => $errors], 422);
        }

        $ip = isset($_SERVER['REMOTE_ADDR']) ? substr((string) $_SERVER['REMOTE_ADDR'], 0, 64) : '';
        $ua = isset($_SERVER['HTTP_USER_AGENT']) ? substr((string) $_SERVER['HTTP_USER_AGENT'], 0, 255) : '';

        $wpdb->insert($wpdb->prefix . 'pi_form_submissions', [
            'form_id'    => $id,
            'data'       => wp_json_encode($clean),
            'ip'         => $ip,
            'user_agent' => $ua,
            'status'     => 'new',
        ]);
        $sub_id = (int) $wpdb->insert_id;

        // Optional lead capture
        if (!empty($settings['lead_capture'])) {
            self::capture_lead_from_submission($clean, $form);
        }

        // Optional webhook
        if (!empty($settings['webhook_url'])) {
            self::dispatch_webhook($settings['webhook_url'], [
                'form_id'    => $id,
                'form_name'  => $form['name'],
                'submission_id' => $sub_id,
                'data'       => $clean,
                'created_at' => gmdate('c'),
            ]);
        }

        // Optional email notification
        if (!empty($settings['email_to'])) {
            self::send_notification_email($settings['email_to'], $form, $clean);
        }

        do_action('pi_form_submitted', $id, $sub_id, $clean, $form);

        return new \WP_REST_Response([
            'success'         => true,
            'submission_id'   => $sub_id,
            'success_message' => $settings['success_message'] ?? 'Cảm ơn bạn đã gửi thông tin.',
            'redirect_url'    => $settings['redirect_url'] ?? '',
        ], 200);
    }

    /* ═══════════════════════════════════════════════════════════════
       EMBED CODE
       ═══════════════════════════════════════════════════════════════ */
    public static function get_embed_code(\WP_REST_Request $request) {
        self::maybe_create_tables();
        global $wpdb;
        $id = (int) $request->get_param('id');
        $form = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}pi_forms WHERE id = %d", $id
        ), ARRAY_A);
        if (!$form) return new \WP_REST_Response(['message' => 'Form không tồn tại'], 404);

        $shortcode = sprintf('[pi_form id="%d"]', $id);
        $endpoint = esc_url_raw(rest_url('pi/v1/forms/' . $id . '/submit'));
        $js = "<div id=\"pi-form-{$id}\"></div>\n";
        $js .= "<script>(function(){\n";
        $js .= "  var endpoint = " . wp_json_encode($endpoint) . ";\n";
        $js .= "  var formId = {$id};\n";
        $js .= "  fetch(" . wp_json_encode(esc_url_raw(rest_url('pi/v1/forms/' . $id))) . ")\n";
        $js .= "    .then(function(r){return r.json();})\n";
        $js .= "    .then(function(form){\n";
        $js .= "      // render fields and POST to `endpoint` on submit.\n";
        $js .= "      console.log('[pi-form] loaded', form);\n";
        $js .= "    });\n";
        $js .= "})();</script>";

        return new \WP_REST_Response([
            'shortcode' => $shortcode,
            'js_embed'  => $js,
            'endpoint'  => $endpoint,
            'form_url'  => esc_url_raw(rest_url('pi/v1/forms/' . $id)),
        ], 200);
    }

    /* ═══════════════════════════════════════════════════════════════
       INTERNAL HELPERS
       ═══════════════════════════════════════════════════════════════ */
    private static function format_form_row($row) {
        if (!is_array($row)) return null;
        $fields   = json_decode((string) ($row['fields']   ?? ''), true);
        $settings = json_decode((string) ($row['settings'] ?? ''), true);
        return [
            'id'         => (int) $row['id'],
            'name'       => (string) $row['name'],
            'slug'       => (string) $row['slug'],
            'fields'     => is_array($fields)   ? $fields   : [],
            'settings'   => is_array($settings) ? $settings : [],
            'created_at' => (string) $row['created_at'],
            'updated_at' => (string) $row['updated_at'],
        ];
    }

    private static function format_submission_row($row) {
        $data = json_decode((string) ($row['data'] ?? ''), true);
        return [
            'id'         => (int) $row['id'],
            'form_id'    => (int) $row['form_id'],
            'data'       => is_array($data) ? $data : [],
            'ip'         => (string) $row['ip'],
            'user_agent' => (string) $row['user_agent'],
            'status'     => (string) $row['status'],
            'created_at' => (string) $row['created_at'],
        ];
    }

    private static function unique_slug($slug, $exclude_id = 0) {
        global $wpdb;
        $base = $slug !== '' ? $slug : 'form-' . wp_generate_password(6, false);
        $base = strtolower($base);
        $candidate = $base;
        $i = 2;
        while (true) {
            $exists = (int) $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}pi_forms WHERE slug = %s AND id <> %d",
                $candidate, (int) $exclude_id
            ));
            if (!$exists) break;
            $candidate = $base . '-' . $i;
            $i++;
            if ($i > 200) { $candidate = $base . '-' . wp_generate_password(4, false); break; }
        }
        return $candidate;
    }

    private static function sanitize_fields($fields) {
        if (!is_array($fields)) return [];
        $allowed_types = ['text', 'textarea', 'email', 'phone', 'number', 'date', 'select', 'radio', 'checkbox', 'file'];
        $out = [];
        foreach ($fields as $f) {
            if (!is_array($f)) continue;
            $type = sanitize_key((string) ($f['type'] ?? 'text'));
            if (!in_array($type, $allowed_types, true)) $type = 'text';
            $clean = [
                'id'          => sanitize_key((string) ($f['id']   ?? 'field_' . wp_generate_password(8, false))),
                'type'        => $type,
                'name'        => sanitize_key((string) ($f['name'] ?? ($f['id'] ?? 'field'))),
                'label'       => sanitize_text_field((string) ($f['label'] ?? '')),
                'placeholder' => sanitize_text_field((string) ($f['placeholder'] ?? '')),
                'required'    => !empty($f['required']),
            ];
            if (isset($f['options']) && is_array($f['options'])) {
                $clean['options'] = array_values(array_map(function ($o) {
                    if (is_array($o)) {
                        return [
                            'value' => sanitize_text_field((string) ($o['value'] ?? '')),
                            'label' => sanitize_text_field((string) ($o['label'] ?? '')),
                        ];
                    }
                    $s = sanitize_text_field((string) $o);
                    return ['value' => $s, 'label' => $s];
                }, $f['options']));
            }
            if (isset($f['conditional']) && is_array($f['conditional'])) {
                $c = $f['conditional'];
                $clean['conditional'] = [
                    'field' => sanitize_key((string) ($c['field'] ?? '')),
                    'op'    => sanitize_key((string) ($c['op']    ?? 'equals')),
                    'value' => sanitize_text_field((string) ($c['value'] ?? '')),
                ];
            }
            $out[] = $clean;
        }
        return $out;
    }

    private static function sanitize_settings($settings) {
        if (!is_array($settings)) $settings = [];
        return [
            'submit_button_label' => sanitize_text_field((string) ($settings['submit_button_label'] ?? 'Gửi')),
            'success_message'     => sanitize_text_field((string) ($settings['success_message']    ?? 'Cảm ơn bạn đã gửi thông tin.')),
            'redirect_url'        => esc_url_raw((string) ($settings['redirect_url'] ?? '')),
            'email_to'            => sanitize_text_field((string) ($settings['email_to']           ?? '')),
            'email_template_id'   => sanitize_text_field((string) ($settings['email_template_id']  ?? '')),
            'webhook_url'         => esc_url_raw((string) ($settings['webhook_url'] ?? '')),
            'lead_capture'        => !empty($settings['lead_capture']),
        ];
    }

    private static function capture_lead_from_submission(array $data, array $form) {
        $name  = (string) ($data['name']  ?? $data['full_name'] ?? '');
        $email = (string) ($data['email'] ?? '');
        $phone = (string) ($data['phone'] ?? '');
        if ($name === '' && $email === '' && $phone === '') return;

        $post_id = wp_insert_post([
            'post_type'   => 'sh_lead',
            'post_status' => 'publish',
            'post_title'  => $name !== '' ? $name : ($email !== '' ? $email : 'Lead từ form ' . $form['name']),
            'post_content' => isset($data['message']) ? (string) $data['message'] : '',
        ]);
        if ($post_id && !is_wp_error($post_id)) {
            update_post_meta($post_id, '_pi_lead_name',   $name);
            update_post_meta($post_id, '_pi_lead_email',  $email);
            update_post_meta($post_id, '_pi_lead_phone',  $phone);
            update_post_meta($post_id, '_pi_lead_source', 'Form: ' . $form['name']);
            update_post_meta($post_id, '_pi_lead_status', 'new');
        }
    }

    private static function dispatch_webhook($url, $payload) {
        if (empty($url)) return;
        wp_remote_post($url, [
            'timeout'   => 10,
            'blocking'  => false,
            'headers'   => ['Content-Type' => 'application/json'],
            'body'      => wp_json_encode($payload),
            'sslverify' => (wp_get_environment_type() !== 'local'),
        ]);
    }

    private static function send_notification_email($to, $form, $data) {
        if (!is_email($to)) return;
        $subject = '[' . get_bloginfo('name') . '] Phản hồi mới: ' . $form['name'];
        $lines = ['Form: ' . $form['name'], '', 'Dữ liệu gửi:'];
        foreach ($data as $k => $v) {
            $lines[] = '- ' . $k . ': ' . (is_array($v) ? implode(', ', $v) : (string) $v);
        }
        wp_mail($to, $subject, implode("\n", $lines));
    }
}
