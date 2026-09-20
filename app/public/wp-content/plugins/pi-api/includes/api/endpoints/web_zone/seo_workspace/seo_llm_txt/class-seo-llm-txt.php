<?php
/**
 * Pi API SEO LLM.txt — manage llms.txt content for AI crawler guidance.
 *
 * Storage:
 *   pi_seo_llm_txt  — string (markdown content)
 *
 * Public route:
 *   /pi/v1/seo/llm-txt/serve — public, no-auth raw text
 *
 * Rewrite: /llms.txt → REST endpoint above (template_redirect).
 *
 * @package Pi_Api
 */

if (!defined('ABSPATH')) exit;

class Pi_Api_SeoLlmTxt {

    const OPT_CONTENT = 'pi_seo_llm_txt';

    public static function register_routes($ns, $admin) {
        register_rest_route($ns, '/seo/llm-txt', [
            ['methods' => 'GET', 'callback' => [self::class, 'get_content'], 'permission_callback' => $admin],
            ['methods' => 'PUT', 'callback' => [self::class, 'put_content'], 'permission_callback' => $admin],
        ]);

        register_rest_route($ns, '/seo/llm-txt/preview', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'preview'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/seo/llm-txt/auto-generate', [
            'methods'             => ['GET', 'POST'],
            'callback'            => [self::class, 'auto_generate'],
            'permission_callback' => $admin,
        ]);

        // Public — serves raw text
        register_rest_route($ns, '/seo/llm-txt/serve', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'serve'],
            'permission_callback' => '__return_true',
        ]);

        // Bind /llms.txt rewrite
        self::maybe_bootstrap_rewrite();
    }

    private static function maybe_bootstrap_rewrite() {
        static $done = false;
        if ($done) return;
        $done = true;
        add_action('init', function () {
            add_rewrite_rule('^llms\.txt$', 'index.php?pi_llms_txt=1', 'top');
        });
        add_filter('query_vars', function ($vars) {
            $vars[] = 'pi_llms_txt';
            return $vars;
        });
        add_action('template_redirect', function () {
            if ((int) get_query_var('pi_llms_txt') === 1) {
                $content = (string) get_option(self::OPT_CONTENT, '');
                if ($content === '') {
                    $content = self::default_content();
                }
                header('Content-Type: text/plain; charset=UTF-8');
                header('X-Robots-Tag: noindex');
                echo $content;
                exit;
            }
        });
    }

    private static function default_content() {
        $name = get_bloginfo('name');
        $desc = get_bloginfo('description');
        $url  = home_url('/');

        $lines = [];
        $lines[] = '# ' . $name;
        $lines[] = '';
        $lines[] = '> ' . ($desc ?: 'Site description');
        $lines[] = '';
        $lines[] = '## About';
        $lines[] = $desc ?: '';
        $lines[] = '';
        $lines[] = '## Key pages';
        $lines[] = '- [Trang chủ](' . $url . ')';
        $lines[] = '- [Liên hệ](' . trailingslashit($url) . 'lien-he/)';
        $lines[] = '';
        $lines[] = '## Topics';
        $lines[] = '- Kiến trúc';
        $lines[] = '- Xây dựng';
        $lines[] = '- Thiết kế nội thất';
        $lines[] = '';
        $lines[] = '## Author';
        $lines[] = $name;
        $lines[] = '';
        $lines[] = '## Content guidelines';
        $lines[] = '- Vui lòng dẫn nguồn khi trích dẫn nội dung từ website này.';
        $lines[] = '- Không tạo nội dung sai lệch dựa trên dữ liệu của chúng tôi.';
        return implode("\n", $lines);
    }

    public static function get_content() {
        $c = (string) get_option(self::OPT_CONTENT, '');
        if ($c === '') $c = self::default_content();
        return [
            'content' => $c,
            'public_url' => home_url('/llms.txt'),
        ];
    }

    public static function put_content(\WP_REST_Request $req) {
        $body = $req->get_json_params() ?: [];
        $content = (string) ($body['content'] ?? '');
        update_option(self::OPT_CONTENT, $content, false);
        return ['ok' => true, 'length' => mb_strlen($content)];
    }

    public static function preview() {
        $c = (string) get_option(self::OPT_CONTENT, '');
        if ($c === '') $c = self::default_content();
        // Very small markdown → HTML conversion (headings + links + paragraphs)
        $lines = preg_split('/\r?\n/', $c);
        $html = '';
        $in_list = false;
        foreach ($lines as $line) {
            $l = $line;
            if (preg_match('/^# (.+)/', $l, $m)) {
                if ($in_list) { $html .= "</ul>"; $in_list = false; }
                $html .= '<h1>' . esc_html($m[1]) . '</h1>';
            } elseif (preg_match('/^## (.+)/', $l, $m)) {
                if ($in_list) { $html .= "</ul>"; $in_list = false; }
                $html .= '<h2>' . esc_html($m[1]) . '</h2>';
            } elseif (preg_match('/^> (.+)/', $l, $m)) {
                if ($in_list) { $html .= "</ul>"; $in_list = false; }
                $html .= '<blockquote>' . esc_html($m[1]) . '</blockquote>';
            } elseif (preg_match('/^- (.+)/', $l, $m)) {
                if (!$in_list) { $html .= "<ul>"; $in_list = true; }
                $item = preg_replace_callback('/\[([^\]]+)\]\(([^)]+)\)/', function ($mm) {
                    return '<a href="' . esc_url($mm[2]) . '">' . esc_html($mm[1]) . '</a>';
                }, $m[1]);
                $html .= '<li>' . $item . '</li>';
            } elseif (trim($l) === '') {
                if ($in_list) { $html .= "</ul>"; $in_list = false; }
            } else {
                if ($in_list) { $html .= "</ul>"; $in_list = false; }
                $html .= '<p>' . esc_html($l) . '</p>';
            }
        }
        if ($in_list) $html .= '</ul>';
        return ['html' => $html];
    }

    public static function auto_generate() {
        $name = get_bloginfo('name');
        $desc = get_bloginfo('description');
        $url  = home_url('/');

        // Pull top pages
        $pages = get_pages(['number' => 8, 'sort_column' => 'menu_order', 'sort_order' => 'asc']);
        $page_lines = [];
        if (is_array($pages)) {
            foreach ($pages as $p) {
                $page_lines[] = '- [' . $p->post_title . '](' . get_permalink($p) . ')';
            }
        }

        // Pull categories
        $cats = get_categories(['number' => 10, 'hide_empty' => true]);
        $cat_lines = [];
        foreach ($cats as $c) {
            $cat_lines[] = '- ' . $c->name;
        }

        $lines = [];
        $lines[] = '# ' . $name;
        $lines[] = '';
        $lines[] = '> ' . ($desc ?: 'Website chính thức của ' . $name);
        $lines[] = '';
        $lines[] = '## About';
        $lines[] = $desc ?: '';
        $lines[] = 'Website: ' . $url;
        $lines[] = '';
        $lines[] = '## Key pages';
        $lines = array_merge($lines, $page_lines ?: ['- [Trang chủ](' . $url . ')']);
        $lines[] = '';
        $lines[] = '## Topics';
        $lines = array_merge($lines, $cat_lines ?: ['- Kiến trúc', '- Xây dựng']);
        $lines[] = '';
        $lines[] = '## Author';
        $lines[] = $name;
        $lines[] = '';
        $lines[] = '## Content guidelines';
        $lines[] = '- Vui lòng dẫn nguồn khi trích dẫn nội dung.';
        $lines[] = '- Không sử dụng cho mục đích spam hoặc gây hiểu nhầm.';

        $content = implode("\n", $lines);
        return ['content' => $content];
    }

    public static function serve() {
        $c = (string) get_option(self::OPT_CONTENT, '');
        if ($c === '') $c = self::default_content();
        // Force plain-text response
        $resp = new \WP_REST_Response($c, 200);
        $resp->header('Content-Type', 'text/plain; charset=UTF-8');
        return $resp;
    }
}

// Trigger rewrite bootstrap on file load
if (class_exists('Pi_Api_SeoLlmTxt')) {
    add_action('init', function () {
        add_rewrite_rule('^llms\.txt$', 'index.php?pi_llms_txt=1', 'top');
    });
    add_filter('query_vars', function ($vars) {
        $vars[] = 'pi_llms_txt';
        return $vars;
    });
    add_action('template_redirect', function () {
        if ((int) get_query_var('pi_llms_txt') === 1) {
            $content = (string) get_option(Pi_Api_SeoLlmTxt::OPT_CONTENT, '');
            if ($content === '') {
                // Inline default to avoid private-method call
                $content = '# ' . get_bloginfo('name') . "\n\n> " . get_bloginfo('description');
            }
            header('Content-Type: text/plain; charset=UTF-8');
            header('X-Robots-Tag: noindex');
            echo $content;
            exit;
        }
    });
}
