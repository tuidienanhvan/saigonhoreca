<?php
/**
 * Pi API SEO OG Image Generator Endpoints
 *
 * Server-side rendering of OG (Open Graph) preview images for posts/pages.
 * Templates are JSON layouts of stacked elements (text/image/shape) — vars are
 * substituted at render time and a 1200x630 PNG is rasterised via GD when
 * available, otherwise a placeholder URL is returned.
 *
 * Schema:
 *   wp_pi_og_templates (id, name, layout JSON, fonts JSON, colors JSON,
 *                       is_default, created_at)
 *
 * Routes:
 *   GET    /pi/v1/seo/og-image/templates
 *   POST   /pi/v1/seo/og-image/templates              { name, layout, fonts, colors, is_default? }
 *   PUT    /pi/v1/seo/og-image/templates/{id}
 *   DELETE /pi/v1/seo/og-image/templates/{id}
 *   POST   /pi/v1/seo/og-image/render                 { template_id, vars: {...} }
 *   POST   /pi/v1/seo/og-image/generate-bulk          { post_ids: [], template_id }
 *   GET    /pi/v1/seo/og-image/post/{post_id}
 *   GET    /pi/v1/seo/og-image/history                ?limit=50
 *
 * @package Pi_Api
 */

if (!defined('ABSPATH')) exit;

class Pi_Api_SeoOgImage {

    const TABLE = 'pi_og_templates';

    /* ═══════════════════════════════════════════════════════════════════════
       SCHEMA — install on first call
       ═══════════════════════════════════════════════════════════════════════ */

    public static function maybe_install() {
        global $wpdb;
        $table = $wpdb->prefix . self::TABLE;
        $exists = $wpdb->get_var($wpdb->prepare(
            "SHOW TABLES LIKE %s",
            $table
        ));
        if ($exists === $table) return;

        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $table (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(190) NOT NULL,
            layout LONGTEXT NULL,
            fonts LONGTEXT NULL,
            colors LONGTEXT NULL,
            is_default TINYINT(1) NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY is_default (is_default)
        ) $charset_collate;";
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    /* ═══════════════════════════════════════════════════════════════════════
       ROUTE REGISTRATION
       ═══════════════════════════════════════════════════════════════════════ */

    public static function register_routes($ns, $admin) {
        self::maybe_install();

        register_rest_route($ns, '/seo/og-image/templates', [
            ['methods' => 'GET',  'callback' => [self::class, 'list_templates'],   'permission_callback' => $admin],
            ['methods' => 'POST', 'callback' => [self::class, 'create_template'],  'permission_callback' => $admin],
        ]);

        register_rest_route($ns, '/seo/og-image/templates/(?P<id>\d+)', [
            ['methods' => 'PUT',    'callback' => [self::class, 'update_template'], 'permission_callback' => $admin],
            ['methods' => 'DELETE', 'callback' => [self::class, 'delete_template'], 'permission_callback' => $admin],
        ]);

        register_rest_route($ns, '/seo/og-image/render', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'render'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/seo/og-image/generate-bulk', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'generate_bulk'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/seo/og-image/post/(?P<post_id>\d+)', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'get_post_image'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/seo/og-image/history', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'history'],
            'permission_callback' => $admin,
        ]);
    }

    /* ═══════════════════════════════════════════════════════════════════════
       HELPERS
       ═══════════════════════════════════════════════════════════════════════ */

    private static function table() {
        global $wpdb;
        return $wpdb->prefix . self::TABLE;
    }

    private static function decode_row($row) {
        if (!$row) return null;
        $layout = json_decode((string) ($row['layout'] ?? '{}'), true);
        $fonts  = json_decode((string) ($row['fonts']  ?? '{}'), true);
        $colors = json_decode((string) ($row['colors'] ?? '{}'), true);
        return [
            'id'         => (int) $row['id'],
            'name'       => (string) $row['name'],
            'layout'     => is_array($layout) ? $layout : ['width' => 1200, 'height' => 630, 'elements' => []],
            'fonts'      => is_array($fonts) ? $fonts : [],
            'colors'     => is_array($colors) ? $colors : [],
            'is_default' => (bool) (int) $row['is_default'],
            'created_at' => (string) $row['created_at'],
        ];
    }

    private static function uploads_dir() {
        $up = wp_upload_dir();
        $dir = trailingslashit((string) $up['basedir']) . 'og-images';
        if (!is_dir($dir)) wp_mkdir_p($dir);
        return [
            'path' => $dir,
            'url'  => trailingslashit((string) $up['baseurl']) . 'og-images',
        ];
    }

    /**
     * Substitute {{var}} placeholders in a template element's content.
     * Public so unit tests can verify rendering math/substitution.
     */
    public static function apply_vars($content, array $vars) {
        if (!is_string($content)) return '';
        $out = $content;
        foreach ($vars as $key => $val) {
            $out = str_replace('{{' . $key . '}}', (string) $val, $out);
        }
        return $out;
    }

    /**
     * Compute clamped element bounds inside the canvas. Returns array of
     * (x, y, w, h) integers, never negative, never exceeding canvas size.
     */
    public static function clamp_bounds($el, $canvas_w, $canvas_h) {
        $x = max(0, (int) ($el['x'] ?? 0));
        $y = max(0, (int) ($el['y'] ?? 0));
        $w = max(1, min((int) ($el['w'] ?? 100), $canvas_w - $x));
        $h = max(1, min((int) ($el['h'] ?? 30),  $canvas_h - $y));
        return ['x' => $x, 'y' => $y, 'w' => $w, 'h' => $h];
    }

    /* ═══════════════════════════════════════════════════════════════════════
       CRUD — TEMPLATES
       ═══════════════════════════════════════════════════════════════════════ */

    public static function list_templates() {
        global $wpdb;
        $rows = $wpdb->get_results(
            "SELECT * FROM " . self::table() . " ORDER BY is_default DESC, id DESC",
            ARRAY_A
        );
        $rows = is_array($rows) ? $rows : [];
        $out = [];
        foreach ($rows as $r) {
            $out[] = self::decode_row($r);
        }
        return new \WP_REST_Response(['success' => true, 'templates' => $out], 200);
    }

    public static function create_template(\WP_REST_Request $request) {
        global $wpdb;
        $body = $request->get_json_params();
        if (!is_array($body)) {
            return new \WP_REST_Response(['success' => false, 'message' => 'Body phải là JSON'], 400);
        }
        $name = sanitize_text_field((string) ($body['name'] ?? ''));
        if ($name === '') {
            return new \WP_REST_Response(['success' => false, 'message' => 'Thiếu tên mẫu.'], 400);
        }

        $layout = isset($body['layout']) ? wp_json_encode($body['layout']) : wp_json_encode(['width' => 1200, 'height' => 630, 'elements' => []]);
        $fonts  = isset($body['fonts'])  ? wp_json_encode($body['fonts'])  : wp_json_encode([]);
        $colors = isset($body['colors']) ? wp_json_encode($body['colors']) : wp_json_encode([]);
        $is_default = !empty($body['is_default']) ? 1 : 0;

        if ($is_default) {
            $wpdb->query("UPDATE " . self::table() . " SET is_default=0");
        }

        $wpdb->insert(self::table(), [
            'name'       => $name,
            'layout'     => $layout,
            'fonts'      => $fonts,
            'colors'     => $colors,
            'is_default' => $is_default,
            'created_at' => current_time('mysql'),
        ]);
        $id = (int) $wpdb->insert_id;
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . self::table() . " WHERE id=%d", $id), ARRAY_A);
        return new \WP_REST_Response([
            'success'  => true,
            'message'  => 'Đã tạo mẫu.',
            'template' => self::decode_row($row),
        ], 200);
    }

    public static function update_template(\WP_REST_Request $request) {
        global $wpdb;
        $id   = (int) $request->get_param('id');
        $body = $request->get_json_params();
        if (!is_array($body)) {
            return new \WP_REST_Response(['success' => false, 'message' => 'Body phải là JSON'], 400);
        }

        $exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM " . self::table() . " WHERE id=%d", $id));
        if (!$exists) {
            return new \WP_REST_Response(['success' => false, 'message' => 'Không tìm thấy mẫu.'], 404);
        }

        $update = [];
        if (isset($body['name']))       $update['name']       = sanitize_text_field((string) $body['name']);
        if (isset($body['layout']))     $update['layout']     = wp_json_encode($body['layout']);
        if (isset($body['fonts']))      $update['fonts']      = wp_json_encode($body['fonts']);
        if (isset($body['colors']))     $update['colors']     = wp_json_encode($body['colors']);
        if (isset($body['is_default'])) {
            $is_default = !empty($body['is_default']) ? 1 : 0;
            if ($is_default) {
                $wpdb->query("UPDATE " . self::table() . " SET is_default=0");
            }
            $update['is_default'] = $is_default;
        }

        if (!empty($update)) {
            $wpdb->update(self::table(), $update, ['id' => $id]);
        }

        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . self::table() . " WHERE id=%d", $id), ARRAY_A);
        return new \WP_REST_Response([
            'success'  => true,
            'message'  => 'Đã cập nhật.',
            'template' => self::decode_row($row),
        ], 200);
    }

    public static function delete_template(\WP_REST_Request $request) {
        global $wpdb;
        $id = (int) $request->get_param('id');
        $wpdb->delete(self::table(), ['id' => $id]);
        return new \WP_REST_Response(['success' => true, 'message' => 'Đã xoá.'], 200);
    }

    /* ═══════════════════════════════════════════════════════════════════════
       RENDER
       ═══════════════════════════════════════════════════════════════════════ */

    public static function render(\WP_REST_Request $request) {
        $body = $request->get_json_params();
        if (!is_array($body)) {
            return new \WP_REST_Response(['success' => false, 'message' => 'Body phải là JSON'], 400);
        }

        $template_id = (int) ($body['template_id'] ?? 0);
        $vars        = is_array($body['vars'] ?? null) ? $body['vars'] : [];
        $post_id     = isset($body['post_id']) ? (int) $body['post_id'] : 0;

        global $wpdb;
        $row = null;
        if ($template_id > 0) {
            $row = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM " . self::table() . " WHERE id=%d", $template_id
            ), ARRAY_A);
        }
        if (!$row) {
            // Fallback to default
            $row = $wpdb->get_row("SELECT * FROM " . self::table() . " WHERE is_default=1 LIMIT 1", ARRAY_A);
        }
        $template = self::decode_row($row);

        $url = self::rasterise($template, $vars, $post_id);

        // Save reference to history option
        $history = (array) get_option('pi_og_history', []);
        $history[] = [
            'post_id'     => $post_id,
            'template_id' => $template ? $template['id'] : 0,
            'template'    => $template ? $template['name'] : '',
            'url'         => $url,
            'vars'        => $vars,
            'status'      => 'success',
            'generated_at' => current_time('mysql'),
        ];
        if (count($history) > 200) $history = array_slice($history, -200);
        update_option('pi_og_history', $history, false);

        return new \WP_REST_Response([
            'success' => true,
            'url'     => $url,
        ], 200);
    }

    /**
     * Rasterise a template into a PNG file. Uses GD if available, otherwise
     * returns a data URL placeholder.
     *
     * @return string Public URL of the rendered image.
     */
    private static function rasterise($template, array $vars, $post_id = 0) {
        $up = self::uploads_dir();
        $w = (int) ($template['layout']['width']  ?? 1200);
        $h = (int) ($template['layout']['height'] ?? 630);
        $w = max(100, min(1600, $w));
        $h = max(100, min(900,  $h));

        $filename = 'og-' . ($post_id > 0 ? $post_id . '-' : '') . substr(md5(wp_json_encode([$template, $vars]) . microtime()), 0, 8) . '.png';
        $filepath = trailingslashit($up['path']) . $filename;
        $fileurl  = trailingslashit($up['url'])  . $filename;

        if (!function_exists('imagecreatetruecolor')) {
            // No GD — return a placeholder via picsum-style URL with title
            $title = (string) ($vars['title'] ?? 'OG Image');
            return 'data:image/svg+xml;base64,' . base64_encode(self::svg_placeholder($w, $h, $title));
        }

        $img = imagecreatetruecolor($w, $h);
        $bg = self::hex_to_rgb($template['colors']['bg'] ?? '#0f172a');
        $bgcol = imagecolorallocate($img, $bg[0], $bg[1], $bg[2]);
        imagefill($img, 0, 0, $bgcol);

        // Draw elements
        foreach ((array) ($template['layout']['elements'] ?? []) as $el) {
            $bounds = self::clamp_bounds($el, $w, $h);
            $type = (string) ($el['type'] ?? 'text');

            if ($type === 'shape') {
                $color = self::hex_to_rgb((string) ($el['color'] ?? '#1e293b'));
                $col = imagecolorallocate($img, $color[0], $color[1], $color[2]);
                imagefilledrectangle($img, $bounds['x'], $bounds['y'], $bounds['x'] + $bounds['w'], $bounds['y'] + $bounds['h'], $col);
            } elseif ($type === 'text') {
                $color = self::hex_to_rgb((string) ($el['color'] ?? '#ffffff'));
                $col = imagecolorallocate($img, $color[0], $color[1], $color[2]);
                $content = self::apply_vars((string) ($el['content'] ?? ''), $vars);
                $size = max(1, min(5, (int) round(((int) ($el['fontSize'] ?? 28)) / 12)));
                imagestring($img, $size, $bounds['x'], $bounds['y'], $content, $col);
            } elseif ($type === 'image' && !empty($el['src'])) {
                $src = (string) self::apply_vars((string) $el['src'], $vars);
                $img_data = false;

                if (wp_http_validate_url($src)) {
                    $response = wp_safe_remote_get($src, [
                        'timeout'            => 5,
                        'reject_unsafe_urls' => true,
                        'redirection'        => 2,
                    ]);
                    if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
                        $img_data = wp_remote_retrieve_body($response);
                    }
                } else {
                    $upload_dir = wp_upload_dir();
                    $real_base_uploads = realpath($upload_dir['basedir']);
                    $real_base_theme   = realpath(get_template_directory());
                    $real_file         = realpath($src);

                    if ($real_file && ($real_base_uploads || $real_base_theme)) {
                        $in_uploads = $real_base_uploads && str_starts_with($real_file, $real_base_uploads);
                        $in_theme   = $real_base_theme && str_starts_with($real_file, $real_base_theme);
                        if ($in_uploads || $in_theme) {
                            $img_data = @file_get_contents($real_file);
                        }
                    }
                }

                if ($img_data) {
                    $tmp = @imagecreatefromstring($img_data);
                    if ($tmp) {
                        imagecopyresampled(
                            $img, $tmp,
                            $bounds['x'], $bounds['y'], 0, 0,
                            $bounds['w'], $bounds['h'],
                            imagesx($tmp), imagesy($tmp)
                        );
                        imagedestroy($tmp);
                    }
                }
            }
        }

        imagepng($img, $filepath);
        imagedestroy($img);
        return $fileurl;
    }

    private static function hex_to_rgb($hex) {
        $hex = ltrim((string) $hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        if (strlen($hex) !== 6) return [15, 23, 42];
        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];
    }

    private static function svg_placeholder($w, $h, $title) {
        $title_escaped = htmlspecialchars((string) $title, ENT_QUOTES);
        return '<?xml version="1.0"?><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' . $w . ' ' . $h . '" width="' . $w . '" height="' . $h . '">'
            . '<rect width="100%" height="100%" fill="#0f172a"/>'
            . '<text x="50%" y="50%" fill="#fff" font-size="48" text-anchor="middle" dominant-baseline="middle" font-family="Arial">'
            . $title_escaped . '</text></svg>';
    }

    /* ═══════════════════════════════════════════════════════════════════════
       BULK + POST LOOKUP
       ═══════════════════════════════════════════════════════════════════════ */

    public static function generate_bulk(\WP_REST_Request $request) {
        $body = $request->get_json_params();
        $post_ids    = (array) ($body['post_ids']    ?? []);
        $template_id = (int)   ($body['template_id'] ?? 0);

        $results = [];
        foreach ($post_ids as $pid) {
            $pid = (int) $pid;
            $post = get_post($pid);
            if (!$post) {
                $results[] = ['post_id' => $pid, 'success' => false, 'message' => 'Không tìm thấy bài viết.'];
                continue;
            }
            $thumb = (string) get_the_post_thumbnail_url($pid, 'large');
            $vars = [
                'title'       => get_the_title($post),
                'description' => (string) (get_post_meta($pid, '_yoast_wpseo_metadesc', true) ?: wp_trim_words((string) $post->post_excerpt ?: (string) $post->post_content, 25)),
                'author'      => (string) get_the_author_meta('display_name', (int) $post->post_author),
                'date'        => mysql2date('d/m/Y', (string) $post->post_date),
                'image'       => $thumb,
            ];

            // Inline render rather than calling render() to avoid overhead.
            global $wpdb;
            $row = $template_id > 0
                ? $wpdb->get_row($wpdb->prepare("SELECT * FROM " . self::table() . " WHERE id=%d", $template_id), ARRAY_A)
                : $wpdb->get_row("SELECT * FROM " . self::table() . " WHERE is_default=1 LIMIT 1", ARRAY_A);
            $template = self::decode_row($row);
            $url = self::rasterise($template, $vars, $pid);

            update_post_meta($pid, '_pi_og_image', $url);
            update_post_meta($pid, '_pi_og_image_url', $url);
            update_post_meta($pid, '_pi_og_image_template', $template ? $template['id'] : 0);

            $results[] = [
                'post_id' => $pid,
                'success' => true,
                'url'     => $url,
            ];
        }

        return new \WP_REST_Response([
            'success' => true,
            'results' => $results,
            'count'   => count($results),
        ], 200);
    }

    public static function get_post_image(\WP_REST_Request $request) {
        $pid  = (int) $request->get_param('post_id');
        $url  = (string) (get_post_meta($pid, '_pi_og_image', true) ?: get_post_meta($pid, '_pi_og_image_url', true));
        $tpl  = (int)    get_post_meta($pid, '_pi_og_image_template', true);
        return new \WP_REST_Response([
            'success'     => true,
            'post_id'     => $pid,
            'url'         => $url,
            'template_id' => $tpl,
        ], 200);
    }

    public static function history(\WP_REST_Request $request) {
        $limit = max(1, min(200, (int) ($request->get_param('limit') ?: 50)));
        $history = (array) get_option('pi_og_history', []);
        $history = array_reverse($history);
        $history = array_slice($history, 0, $limit);
        // Enrich with post titles
        foreach ($history as &$h) {
            if (!empty($h['post_id'])) {
                $h['post_title'] = (string) get_the_title((int) $h['post_id']);
            }
        }
        return new \WP_REST_Response([
            'success' => true,
            'history' => $history,
        ], 200);
    }
}
