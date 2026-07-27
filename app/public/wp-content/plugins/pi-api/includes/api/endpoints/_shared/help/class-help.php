<?php
/**
 * Pi API Help Center Endpoints
 *
 * Phase 5 task C — markdown-driven help articles, contextual help, what's new,
 * and feedback aggregation. Content lives at:
 *   plugins/pi-api/includes/api/endpoints/_shared/help/content/*.md
 *
 * Each markdown file ships with a YAML-ish front-matter block:
 *   ---
 *   title: ...
 *   category: ...
 *   order: 1
 *   date: 2026-04-25
 *   tags: a, b
 *   ---
 *   # Body...
 *
 * If Parsedown is installed via Composer it's used; otherwise we fall back to
 * a regex renderer covering the subset our docs use (headings, code, lists,
 * bold/italic, links, tables, blockquotes).
 *
 * @package Pi_Api
 */

if (!defined('ABSPATH')) exit;

class Pi_Api_Help {

    const FEEDBACK_OPTION = 'pi_help_feedback';
    const CACHE_KEY_PREFIX = 'pi_help_';
    const CACHE_TTL = 300; // 5 minutes

    /** @var string|null Resolved content directory */
    private static $content_dir = null;

    public static function register_routes($ns, $admin) {
        register_rest_route($ns, '/help/articles', [
            'methods'  => 'GET',
            'callback' => [self::class, 'list_articles'],
            'permission_callback' => $admin,
        ]);
        register_rest_route($ns, '/help/articles/(?P<slug>[a-z0-9\-_]+)', [
            'methods'  => 'GET',
            'callback' => [self::class, 'get_article'],
            'permission_callback' => $admin,
            'args' => ['slug' => ['required' => true]],
        ]);
        register_rest_route($ns, '/help/categories', [
            'methods'  => 'GET',
            'callback' => [self::class, 'list_categories'],
            'permission_callback' => $admin,
        ]);
        register_rest_route($ns, '/help/feedback', [
            'methods'  => 'POST',
            'callback' => [self::class, 'submit_feedback'],
            'permission_callback' => $admin,
        ]);
        register_rest_route($ns, '/help/whats-new', [
            'methods'  => 'GET',
            'callback' => [self::class, 'whats_new'],
            'permission_callback' => $admin,
        ]);
        register_rest_route($ns, '/help/contextual', [
            'methods'  => 'GET',
            'callback' => [self::class, 'contextual'],
            'permission_callback' => $admin,
        ]);
    }

    /** Resolve & cache the content directory location. */
    private static function dir() {
        if (self::$content_dir !== null) return self::$content_dir;
        $candidates = [
            defined('PI_API_DIR') ? PI_API_DIR . 'includes/api/endpoints/_shared/help/content' : __DIR__ . '/content',
        ];
        foreach ($candidates as $c) {
            if (is_dir($c)) {
                self::$content_dir = realpath($c) ?: $c;
                return self::$content_dir;
            }
        }
        self::$content_dir = '';
        return '';
    }

    /** Load all markdown files (with parsed front-matter) — cached. */
    private static function all_articles() {
        $cached = wp_cache_get(self::CACHE_KEY_PREFIX . 'all', 'pi_help');
        if (is_array($cached)) return $cached;

        $dir = self::dir();
        if (!$dir) return [];

        $articles = [];
        $files = glob($dir . '/*.md');
        if (!$files) return [];

        foreach ($files as $file) {
            $slug = basename($file, '.md');
            $raw = @file_get_contents($file);
            if (!$raw) continue;
            $articles[$slug] = self::parse_file($slug, $raw);
        }

        // Sort by `order` ascending (then alpha title fallback)
        uasort($articles, function ($a, $b) {
            $ord = ($a['order'] ?? 999) <=> ($b['order'] ?? 999);
            return $ord !== 0 ? $ord : strcmp($a['title'], $b['title']);
        });

        wp_cache_set(self::CACHE_KEY_PREFIX . 'all', $articles, 'pi_help', self::CACHE_TTL);
        return $articles;
    }

    /** Parse front-matter + body from a single .md string. */
    private static function parse_file($slug, $raw) {
        $meta = [
            'slug'     => $slug,
            'title'    => ucfirst(str_replace('-', ' ', $slug)),
            'category' => 'general',
            'order'    => 999,
            'date'     => '',
            'tags'     => [],
        ];
        $body = $raw;

        if (preg_match('/^---\s*\n(.*?)\n---\s*\n(.*)$/s', $raw, $m)) {
            $front = $m[1];
            $body  = $m[2];
            foreach (preg_split('/\R/', $front) as $line) {
                if (!preg_match('/^([a-zA-Z_]+)\s*:\s*(.*)$/', $line, $kv)) continue;
                $key = strtolower(trim($kv[1]));
                $val = trim($kv[2]);
                if ($key === 'tags') {
                    $meta['tags'] = array_filter(array_map('trim', explode(',', $val)));
                } elseif ($key === 'order') {
                    $meta['order'] = (int) $val;
                } else {
                    $meta[$key] = $val;
                }
            }
        }

        $meta['body_md']   = trim($body);
        $meta['body_html'] = self::render_markdown($body);
        $meta['excerpt']   = wp_trim_words(wp_strip_all_tags($meta['body_html']), 30);

        // Auto-generated TOC from H2/H3
        $meta['toc'] = self::extract_toc($body);

        return $meta;
    }

    /** Generate ToC entries from H2/H3 headings. */
    private static function extract_toc($body) {
        $toc = [];
        if (preg_match_all('/^(#{2,3})\s+(.+)$/m', $body, $m, PREG_SET_ORDER)) {
            foreach ($m as $h) {
                $level = strlen($h[1]);
                $text  = trim($h[2]);
                $toc[] = [
                    'level' => $level,
                    'text'  => $text,
                    'id'    => sanitize_title($text),
                ];
            }
        }
        return $toc;
    }

    /** Render markdown — Parsedown if available, else regex fallback. */
    private static function render_markdown($md) {
        if (class_exists('Parsedown')) {
            try {
                $pd = new \Parsedown();
                if (method_exists($pd, 'setSafeMode')) $pd->setSafeMode(true);
                return $pd->text($md);
            } catch (\Throwable $e) {
                // fall through to regex fallback
            }
        }
        return self::render_markdown_simple($md);
    }

    /**
     * Minimal markdown renderer covering: H1-H4, bold, italic, code (inline + fenced),
     * links, lists (- and 1.), tables (| header |), blockquotes, hr, paragraphs.
     */
    private static function render_markdown_simple($md) {
        $md = str_replace(["\r\n", "\r"], "\n", $md);
        $lines = explode("\n", $md);
        $out = [];
        $in_code = false;
        $code_lang = '';
        $code_buf = [];
        $list_stack = []; // entries: 'ul' | 'ol'
        $in_table = false;
        $table_buf = [];

        $flush_list = function () use (&$list_stack, &$out) {
            while (!empty($list_stack)) {
                $tag = array_pop($list_stack);
                $out[] = "</$tag>";
            }
        };
        $flush_table = function () use (&$in_table, &$table_buf, &$out) {
            if (!$in_table) return;
            // table_buf rows: [['header'|'sep'|'row'], cells[]]
            $rows = $table_buf;
            $head_cells = [];
            $body_rows = [];
            $skip_next = false;
            foreach ($rows as $i => $r) {
                if ($i === 0) { $head_cells = $r; continue; }
                if ($i === 1 && self::is_table_separator_row($r)) { continue; }
                $body_rows[] = $r;
            }
            $html = '<table class="pi-help-table"><thead><tr>';
            foreach ($head_cells as $c) $html .= '<th>' . self::inline($c) . '</th>';
            $html .= '</tr></thead><tbody>';
            foreach ($body_rows as $r) {
                $html .= '<tr>';
                foreach ($r as $c) $html .= '<td>' . self::inline($c) . '</td>';
                $html .= '</tr>';
            }
            $html .= '</tbody></table>';
            $out[] = $html;
            $in_table = false;
            $table_buf = [];
        };

        foreach ($lines as $line) {
            // Fenced code blocks
            if (preg_match('/^```(\w*)\s*$/', $line, $m)) {
                if ($in_code) {
                    $out[] = '<pre class="pi-code"><code class="lang-' . esc_attr($code_lang) . '">' .
                        esc_html(implode("\n", $code_buf)) .
                        '</code></pre>';
                    $in_code = false;
                    $code_buf = [];
                    $code_lang = '';
                } else {
                    $flush_list(); $flush_table();
                    $in_code = true;
                    $code_lang = $m[1];
                }
                continue;
            }
            if ($in_code) { $code_buf[] = $line; continue; }

            // Tables
            if (strpos($line, '|') !== false && preg_match('/\|/', $line)) {
                $cells = self::parse_table_row($line);
                if ($cells !== null) {
                    if (!$in_table) { $flush_list(); $in_table = true; $table_buf = []; }
                    $table_buf[] = $cells;
                    continue;
                }
            } elseif ($in_table) {
                $flush_table();
            }

            // Headings
            if (preg_match('/^(#{1,6})\s+(.+?)\s*$/', $line, $m)) {
                $flush_list(); $flush_table();
                $lvl = strlen($m[1]);
                $txt = trim($m[2]);
                $id  = sanitize_title($txt);
                $out[] = "<h{$lvl} id=\"{$id}\">" . self::inline($txt) . "</h{$lvl}>";
                continue;
            }

            // Horizontal rule
            if (preg_match('/^[\-\*_]{3,}\s*$/', $line)) {
                $flush_list(); $flush_table();
                $out[] = '<hr/>';
                continue;
            }

            // Blockquote
            if (preg_match('/^>\s?(.*)$/', $line, $m)) {
                $flush_list();
                $out[] = '<blockquote>' . self::inline($m[1]) . '</blockquote>';
                continue;
            }

            // Lists (unordered)
            if (preg_match('/^(\s*)[\-\*]\s+(.+)$/', $line, $m)) {
                if (empty($list_stack) || end($list_stack) !== 'ul') {
                    $flush_list(); $list_stack[] = 'ul'; $out[] = '<ul>';
                }
                $out[] = '<li>' . self::inline($m[2]) . '</li>';
                continue;
            }
            // Ordered
            if (preg_match('/^\s*\d+\.\s+(.+)$/', $line, $m)) {
                if (empty($list_stack) || end($list_stack) !== 'ol') {
                    $flush_list(); $list_stack[] = 'ol'; $out[] = '<ol>';
                }
                $out[] = '<li>' . self::inline($m[1]) . '</li>';
                continue;
            }

            // Blank line
            if (trim($line) === '') {
                $flush_list();
                continue;
            }

            // Paragraph
            $flush_list();
            $out[] = '<p>' . self::inline($line) . '</p>';
        }

        if ($in_code) {
            $out[] = '<pre class="pi-code"><code>' . esc_html(implode("\n", $code_buf)) . '</code></pre>';
        }
        $flush_list();
        $flush_table();

        return implode("\n", $out);
    }

    private static function parse_table_row($line) {
        $line = trim($line);
        if ($line === '' || $line[0] !== '|' || substr($line, -1) !== '|') {
            // Allow rows that don't bracket with | only if it has multiple |
            if (substr_count($line, '|') < 2) return null;
        }
        $line = trim($line, '|');
        $cells = array_map('trim', explode('|', $line));
        return $cells;
    }

    private static function is_table_separator_row($cells) {
        foreach ($cells as $c) {
            if (!preg_match('/^:?-{3,}:?$/', trim($c))) return false;
        }
        return true;
    }

    /** Inline markdown: bold, italic, inline code, links. */
    private static function inline($text) {
        // Escape HTML first
        $t = esc_html($text);
        // Inline code `x` (do this first to avoid conflicts)
        $t = preg_replace_callback('/`([^`]+)`/', function ($m) {
            return '<code>' . $m[1] . '</code>';
        }, $t);
        // Bold **x**
        $t = preg_replace('/\*\*([^*]+)\*\*/', '<strong>$1</strong>', $t);
        // Italic *x* (avoid matching inside bold)
        $t = preg_replace('/(?<![*])\*([^*\s][^*]*)\*(?![*])/', '<em>$1</em>', $t);
        // Links [text](url)
        $t = preg_replace_callback('/\[([^\]]+)\]\(([^)]+)\)/', function ($m) {
            $url = esc_url($m[2]);
            return '<a href="' . $url . '">' . $m[1] . '</a>';
        }, $t);
        return $t;
    }

    // ─── Endpoints ────────────────────────────────────────────────────────

    public static function list_articles($req) {
        $cat = sanitize_text_field($req->get_param('category') ?: '');
        $search = sanitize_text_field($req->get_param('search') ?: '');
        $articles = self::all_articles();
        $items = [];

        foreach ($articles as $a) {
            if ($cat && $a['category'] !== $cat) continue;
            if ($search) {
                $hay = mb_strtolower($a['title'] . ' ' . $a['body_md'] . ' ' . implode(' ', $a['tags']));
                if (mb_strpos($hay, mb_strtolower($search)) === false) continue;
            }
            // Strip body for list view
            $items[] = [
                'slug'     => $a['slug'],
                'title'    => $a['title'],
                'category' => $a['category'],
                'order'    => $a['order'],
                'date'     => $a['date'],
                'tags'     => $a['tags'],
                'excerpt'  => $a['excerpt'],
            ];
        }

        return rest_ensure_response([
            'items' => array_values($items),
            'total' => count($items),
        ]);
    }

    public static function get_article($req) {
        $slug = sanitize_title($req->get_param('slug'));
        $articles = self::all_articles();
        if (!isset($articles[$slug])) {
            return new WP_Error('not_found', 'Bài viết không tồn tại.', ['status' => 404]);
        }
        $article = $articles[$slug];

        // Build "related" — same category, top 3 (excluding self)
        $related = [];
        foreach ($articles as $a) {
            if ($a['slug'] === $slug) continue;
            if ($a['category'] !== $article['category']) continue;
            $related[] = [
                'slug'  => $a['slug'],
                'title' => $a['title'],
                'excerpt' => $a['excerpt'],
            ];
            if (count($related) >= 3) break;
        }

        $article['related'] = $related;
        return rest_ensure_response($article);
    }

    public static function list_categories() {
        $articles = self::all_articles();
        $cats = [];
        foreach ($articles as $a) {
            $key = $a['category'];
            if (!isset($cats[$key])) {
                $cats[$key] = [
                    'slug'  => $key,
                    'label' => self::category_label($key),
                    'count' => 0,
                ];
            }
            $cats[$key]['count']++;
        }
        return rest_ensure_response(['items' => array_values($cats)]);
    }

    private static function category_label($slug) {
        $map = [
            'getting-started' => 'Bắt đầu',
            'content'         => 'Nội dung',
            'marketing'       => 'Marketing',
            'system'          => 'Hệ thống',
            'developer'       => 'Lập trình viên',
            'announcements'   => 'Thông báo',
            'general'         => 'Chung',
        ];
        return $map[$slug] ?? ucfirst(str_replace('-', ' ', $slug));
    }

    public static function submit_feedback($req) {
        $slug    = sanitize_title($req->get_param('article_slug') ?: '');
        $helpful = (bool) $req->get_param('helpful');
        $comment = sanitize_textarea_field($req->get_param('comment') ?: '');

        if (!$slug) {
            return new WP_Error('bad_request', 'Thiếu article_slug.', ['status' => 400]);
        }

        $store = get_option(self::FEEDBACK_OPTION, []);
        if (!is_array($store)) $store = [];
        $store[] = [
            'slug'    => $slug,
            'helpful' => $helpful,
            'comment' => $comment,
            'user_id' => get_current_user_id(),
            'time'    => time(),
        ];
        // Cap to 1000 entries
        if (count($store) > 1000) $store = array_slice($store, -1000);
        update_option(self::FEEDBACK_OPTION, $store, false);

        return rest_ensure_response(['ok' => true]);
    }

    public static function whats_new() {
        $articles = self::all_articles();
        $whats_new = $articles['whats-new'] ?? null;
        if (!$whats_new) {
            return rest_ensure_response(['items' => []]);
        }

        // Parse the markdown body into release entries by H2.
        $body = $whats_new['body_md'];
        $items = [];
        if (preg_match_all('/^##\s+(.+?)\n(.*?)(?=^##\s+|\z)/sm', $body, $m, PREG_SET_ORDER)) {
            foreach ($m as $rel) {
                $heading = trim($rel[1]);
                $content = trim($rel[2]);
                // Try to split heading: "v2.5.0 — 25/04/2026 — Highlight"
                $version = $date = $title = '';
                $parts = array_map('trim', explode('—', $heading));
                $version = $parts[0] ?? $heading;
                $date    = $parts[1] ?? '';
                $title   = $parts[2] ?? '';

                $items[] = [
                    'version' => $version,
                    'date'    => $date,
                    'title'   => $title,
                    'body_html' => self::render_markdown($content),
                    'body_md'   => $content,
                ];
            }
        }

        return rest_ensure_response(['items' => $items]);
    }

    public static function contextual($req) {
        $page = sanitize_text_field($req->get_param('page') ?: '');
        $articles = self::all_articles();
        if (!$page) {
            return rest_ensure_response(['items' => []]);
        }

        // Map page slugs to article tags / categories
        $hints = [
            '/'                 => ['getting-started'],
            '/posts'            => ['posts-pages'],
            '/pages'            => ['posts-pages'],
            '/seo-audit'        => ['seo-overview'],
            '/seo-schema'       => ['seo-overview'],
            '/seo-health'       => ['seo-overview'],
            '/leads'            => ['leads-management'],
            '/leads-pipeline'   => ['leads-management'],
            '/chat-logs'        => ['chatbot-setup'],
            '/chatbot-kb'       => ['chatbot-setup'],
            '/email-campaigns'  => ['email-campaigns'],
            '/email-templates'  => ['email-campaigns'],
            '/backup'           => ['backup-restore'],
            '/system'           => ['troubleshooting'],
            '/system-health'    => ['troubleshooting'],
        ];

        $relevant = $hints[$page] ?? [];
        $items = [];
        foreach ($relevant as $slug) {
            if (isset($articles[$slug])) {
                $a = $articles[$slug];
                $items[] = [
                    'slug'    => $a['slug'],
                    'title'   => $a['title'],
                    'excerpt' => $a['excerpt'],
                ];
            }
        }

        // Always include getting-started as fallback
        if (empty($items) && isset($articles['getting-started'])) {
            $a = $articles['getting-started'];
            $items[] = [
                'slug'    => $a['slug'],
                'title'   => $a['title'],
                'excerpt' => $a['excerpt'],
            ];
        }

        return rest_ensure_response([
            'page' => $page,
            'items' => array_slice($items, 0, 3),
        ]);
    }
}
