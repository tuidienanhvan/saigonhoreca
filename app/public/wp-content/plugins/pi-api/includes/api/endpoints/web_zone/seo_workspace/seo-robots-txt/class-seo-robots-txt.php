<?php
/**
 * Pi API SEO robots.txt — manage custom robots.txt content.
 *
 * Storage:
 *   pi_seo_robots_txt          — current content (string)
 *   pi_seo_robots_txt_history  — array, max 10 entries
 *
 * Hooks:
 *   robots_txt filter — overrides WP default robots.txt with stored content.
 *
 * @package Pi_Api
 */

if (!defined('ABSPATH')) exit;

class Pi_Api_SeoRobotsTxt {

    const OPT_CONTENT  = 'pi_seo_robots_txt';
    const OPT_HISTORY  = 'pi_seo_robots_txt_history';
    const HISTORY_MAX  = 10;

    public static function register_routes($ns, $admin) {
        register_rest_route($ns, '/seo/robots-txt', [
            ['methods' => 'GET', 'callback' => [self::class, 'get_content'], 'permission_callback' => $admin],
            ['methods' => 'PUT', 'callback' => [self::class, 'put_content'], 'permission_callback' => $admin],
        ]);

        register_rest_route($ns, '/seo/robots-txt/validate', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'validate'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/seo/robots-txt/history', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'history'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/seo/robots-txt/restore/(?P<version_id>\d+)', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'restore'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/seo/robots-txt/test', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'test_url'],
            'permission_callback' => $admin,
        ]);

        // Bind override filter
        self::bootstrap_override();
    }

    /* ───────────────────────────── HELPERS ───────────────────────────── */

    private static function bootstrap_override() {
        static $bound = false;
        if ($bound) return;
        $bound = true;
        add_filter('robots_txt', function ($output, $public) {
            if (!$public) return $output;
            $custom = (string) get_option(self::OPT_CONTENT, '');
            if ($custom !== '') {
                return $custom;
            }
            return $output;
        }, 10, 2);
    }

    private static function default_content() {
        $home = home_url('/');
        $sitemap = home_url('/sitemap.xml');
        $lines = [
            'User-agent: *',
            'Disallow: /wp-admin/',
            'Allow: /wp-admin/admin-ajax.php',
            '',
            'Sitemap: ' . $sitemap,
        ];
        return implode("\n", $lines);
    }

    private static function read_history() {
        $h = get_option(self::OPT_HISTORY, []);
        return is_array($h) ? $h : [];
    }

    private static function write_history(array $h) {
        if (count($h) > self::HISTORY_MAX) {
            $h = array_slice($h, -self::HISTORY_MAX);
        }
        update_option(self::OPT_HISTORY, $h, false);
    }

    /* ───────────────────────────── ENDPOINTS ───────────────────────────── */

    public static function get_content() {
        $c = (string) get_option(self::OPT_CONTENT, '');
        if ($c === '') $c = self::default_content();
        return [
            'content' => $c,
            'history' => self::read_history(),
        ];
    }

    public static function put_content(\WP_REST_Request $req) {
        $body = $req->get_json_params() ?: [];
        $content = (string) ($body['content'] ?? '');

        // Save current to history before overwrite
        $current = (string) get_option(self::OPT_CONTENT, '');
        if ($current !== '') {
            $h = self::read_history();
            $h[] = [
                'id'      => time(),
                'content' => $current,
                'saved_at' => current_time('mysql'),
            ];
            self::write_history($h);
        }

        update_option(self::OPT_CONTENT, $content, false);
        return ['ok' => true, 'length' => mb_strlen($content)];
    }

    public static function validate() {
        $c = (string) get_option(self::OPT_CONTENT, '');
        if ($c === '') $c = self::default_content();
        $errors = [];
        $warnings = [];

        $lines = preg_split('/\r?\n/', $c);
        $has_user_agent = false;
        $has_sitemap = false;
        foreach ($lines as $i => $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) continue;
            if (!preg_match('/^(User-agent|Allow|Disallow|Crawl-delay|Sitemap|Host):\s*.+/i', $line)) {
                $errors[] = ['line' => $i + 1, 'message' => 'Cú pháp không hợp lệ: ' . $line];
                continue;
            }
            if (stripos($line, 'User-agent:') === 0) $has_user_agent = true;
            if (stripos($line, 'Sitemap:') === 0)    $has_sitemap = true;
        }
        if (!$has_user_agent) {
            $warnings[] = ['line' => 0, 'message' => 'Chưa có chỉ thị User-agent'];
        }
        if (!$has_sitemap) {
            $warnings[] = ['line' => 0, 'message' => 'Khuyến nghị thêm Sitemap'];
        }

        return [
            'valid'    => empty($errors),
            'errors'   => $errors,
            'warnings' => $warnings,
        ];
    }

    public static function history() {
        return ['items' => self::read_history()];
    }

    public static function restore(\WP_REST_Request $req) {
        $vid = (int) $req['version_id'];
        $h = self::read_history();
        $found = null;
        foreach ($h as $entry) {
            if ((int) ($entry['id'] ?? 0) === $vid) {
                $found = $entry;
                break;
            }
        }
        if (!$found) {
            return new \WP_Error('not_found', 'Version not found', ['status' => 404]);
        }
        // Backup current first
        $current = (string) get_option(self::OPT_CONTENT, '');
        if ($current !== '') {
            $h[] = [
                'id'       => time(),
                'content'  => $current,
                'saved_at' => current_time('mysql'),
            ];
            self::write_history($h);
        }
        update_option(self::OPT_CONTENT, $found['content'], false);
        return ['ok' => true, 'restored' => $vid];
    }

    public static function test_url(\WP_REST_Request $req) {
        $url = (string) $req->get_param('url');
        $ua  = (string) ($req->get_param('user-agent') ?: $req->get_param('ua') ?: '*');
        if ($url === '') {
            return new \WP_Error('bad_request', 'url is required', ['status' => 400]);
        }

        $content = (string) get_option(self::OPT_CONTENT, '');
        if ($content === '') $content = self::default_content();

        $rules = self::parse_rules($content);
        $result = self::evaluate($rules, $ua, $url);

        return [
            'url'        => $url,
            'user_agent' => $ua,
            'allowed'    => $result['allowed'],
            'reason'     => $result['reason'],
            'matched_rule' => $result['rule'],
        ];
    }

    private static function parse_rules($content) {
        $blocks = [];
        $current = null;
        foreach (preg_split('/\r?\n/', $content) as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                if ($current) { $blocks[] = $current; $current = null; }
                continue;
            }
            if (stripos($line, 'User-agent:') === 0) {
                if (!$current) $current = ['agents' => [], 'allow' => [], 'disallow' => []];
                $current['agents'][] = trim(substr($line, 11));
            } elseif (stripos($line, 'Allow:') === 0 && $current) {
                $current['allow'][] = trim(substr($line, 6));
            } elseif (stripos($line, 'Disallow:') === 0 && $current) {
                $current['disallow'][] = trim(substr($line, 9));
            }
        }
        if ($current) $blocks[] = $current;
        return $blocks;
    }

    private static function evaluate($rules, $ua, $url) {
        $path = parse_url($url, PHP_URL_PATH) ?: $url;
        $matchBlock = null;
        $uaLow = strtolower($ua);

        foreach ($rules as $b) {
            foreach ($b['agents'] as $a) {
                $a = trim($a);
                if ($a === '*' || stripos($uaLow, strtolower($a)) !== false) {
                    $matchBlock = $b;
                    break 2;
                }
            }
        }
        // Fallback: wildcard
        if (!$matchBlock) {
            foreach ($rules as $b) {
                if (in_array('*', array_map('trim', $b['agents']), true)) { $matchBlock = $b; break; }
            }
        }
        if (!$matchBlock) {
            return ['allowed' => true, 'reason' => 'No matching block — allowed by default', 'rule' => null];
        }

        // Disallow rules
        foreach ($matchBlock['disallow'] as $d) {
            if ($d === '') continue;
            if (str_starts_with($path, $d)) {
                return [
                    'allowed' => false,
                    'reason'  => 'Bị chặn bởi rule: Disallow: ' . $d,
                    'rule'    => 'Disallow: ' . $d,
                ];
            }
        }
        return ['allowed' => true, 'reason' => 'Cho phép truy cập', 'rule' => null];
    }
}
