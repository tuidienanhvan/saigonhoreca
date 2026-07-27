<?php
/**
 * Pi API SEO Redirects + 404 Monitor Endpoints
 *
 * Storage: wp_options
 *   sgh_redirects  — array of rules: [{id, source, target, code, regex, hits, last_hit, created_at}]
 *   sgh_404_log    — ring buffer (max 500): [{url, referrer, user_agent, ip, timestamp, count}]
 *
 * Runtime hooks (bootstrapped on WP load, not only REST):
 *   template_redirect  →  match+redirect incoming REQUEST_URI against rules,
 *                         otherwise log 404 when is_404().
 *
 * @package Pi_Api
 */

if (!defined('ABSPATH')) exit;

class Pi_Api_SeoRedirects {

    const OPT_RULES   = 'sgh_redirects';
    const OPT_LOG     = 'sgh_404_log';
    const LOG_MAX     = 500;
    const ALLOWED_CODES = [301, 302, 307, 410];

    /* ═══════════════════════════════════════════════════════════════════════
       ROUTE REGISTRATION
       ═══════════════════════════════════════════════════════════════════════ */

    public static function register_routes($ns, $admin) {
        // Rules — list + create/update
        register_rest_route($ns, '/seo/redirects', [
            ['methods' => 'GET',  'callback' => [self::class, 'list_rules'], 'permission_callback' => $admin],
            ['methods' => 'POST', 'callback' => [self::class, 'save_rule'],  'permission_callback' => $admin],
        ]);

        // Rules — delete one by id
        register_rest_route($ns, '/seo/redirects/(?P<id>\d+)', [
            'methods'             => 'DELETE',
            'callback'            => [self::class, 'delete_rule'],
            'permission_callback' => $admin,
        ]);

        // CSV I/O
        register_rest_route($ns, '/seo/redirects/import-csv', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'import_csv'],
            'permission_callback' => $admin,
        ]);
        register_rest_route($ns, '/seo/redirects/export-csv', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'export_csv'],
            'permission_callback' => $admin,
        ]);

        // 404 log — list + clear
        register_rest_route($ns, '/seo/404-log', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'list_log'],
            'permission_callback' => $admin,
        ]);
        register_rest_route($ns, '/seo/404-log/clear', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'clear_log'],
            'permission_callback' => $admin,
        ]);

        // Bootstrap runtime hook once — auto-redirect + 404 logger on public hits
        self::maybe_bootstrap_runtime();
    }

    /* ═══════════════════════════════════════════════════════════════════════
       STORAGE HELPERS
       ═══════════════════════════════════════════════════════════════════════ */

    private static function read_rules() {
        $rules = get_option(self::OPT_RULES, []);
        return is_array($rules) ? array_values($rules) : [];
    }

    private static function write_rules(array $rules) {
        update_option(self::OPT_RULES, array_values($rules), false);
    }

    private static function read_log() {
        $log = get_option(self::OPT_LOG, []);
        return is_array($log) ? array_values($log) : [];
    }

    private static function write_log(array $log) {
        // Keep ring buffer size within LOG_MAX
        if (count($log) > self::LOG_MAX) {
            $log = array_slice($log, -self::LOG_MAX);
        }
        update_option(self::OPT_LOG, array_values($log), false);
    }

    private static function next_id(array $rules) {
        $max = 0;
        foreach ($rules as $r) {
            if (isset($r['id']) && (int) $r['id'] > $max) $max = (int) $r['id'];
        }
        return $max + 1;
    }

    private static function normalise_rule($row) {
        $code  = (int) ($row['code'] ?? 301);
        if (!in_array($code, self::ALLOWED_CODES, true)) $code = 301;

        return [
            'id'         => (int) ($row['id'] ?? 0),
            'source'     => trim((string) ($row['source'] ?? '')),
            'target'     => trim((string) ($row['target'] ?? '')),
            'code'       => $code,
            'regex'      => !empty($row['regex']) && $row['regex'] !== 'false' && $row['regex'] !== '0',
            'hits'       => (int) ($row['hits'] ?? 0),
            'last_hit'   => (string) ($row['last_hit'] ?? ''),
            'created_at' => (string) ($row['created_at'] ?? ''),
        ];
    }

    /* ═══════════════════════════════════════════════════════════════════════
       RULES — LIST / SAVE / DELETE
       ═══════════════════════════════════════════════════════════════════════ */

    public static function list_rules(\WP_REST_Request $request) {
        $page     = max(1, (int) ($request->get_param('page') ?: 1));
        $per_page = min(500, max(1, (int) ($request->get_param('per_page') ?: 25)));
        $search   = trim((string) ($request->get_param('search') ?: ''));

        $all = self::read_rules();

        if ($search !== '') {
            $needle = mb_strtolower($search);
            $all = array_values(array_filter($all, function ($r) use ($needle) {
                $hay = mb_strtolower(($r['source'] ?? '') . ' ' . ($r['target'] ?? ''));
                return strpos($hay, $needle) !== false;
            }));
        }

        // Sort: newest first (by id desc)
        usort($all, function ($a, $b) {
            return (int) ($b['id'] ?? 0) - (int) ($a['id'] ?? 0);
        });

        $total  = count($all);
        $offset = ($page - 1) * $per_page;
        $items  = array_slice($all, $offset, $per_page);

        return new \WP_REST_Response([
            'items'    => $items,
            'total'    => $total,
            'page'     => $page,
            'per_page' => $per_page,
            'pages'    => max(1, (int) ceil($total / $per_page)),
        ], 200);
    }

    public static function save_rule(\WP_REST_Request $request) {
        $p = $request->get_json_params();
        if (!is_array($p)) $p = $request->get_params();

        $source = trim((string) ($p['source'] ?? ''));
        $target = trim((string) ($p['target'] ?? ''));
        if ($source === '' || $target === '') {
            return new \WP_REST_Response(['message' => 'Source và Target không được trống.'], 400);
        }

        $code = (int) ($p['code'] ?? 301);
        if (!in_array($code, self::ALLOWED_CODES, true)) {
            return new \WP_REST_Response(['message' => 'Mã redirect không hợp lệ.'], 400);
        }

        $is_regex = !empty($p['regex']);
        if ($is_regex) {
            // Validate regex compiles (wrap in # delimiters, anchoring left to dev)
            $pattern = '#' . str_replace('#', '\\#', $source) . '#i';
            if (@preg_match($pattern, '') === false) {
                return new \WP_REST_Response(['message' => 'Regex không hợp lệ.'], 400);
            }
        }

        $rules = self::read_rules();
        $id    = (int) ($p['id'] ?? 0);
        $now   = current_time('mysql');

        if ($id > 0) {
            // Update existing
            $found = false;
            foreach ($rules as $i => $r) {
                if ((int) ($r['id'] ?? 0) === $id) {
                    $rules[$i] = self::normalise_rule(array_merge($r, [
                        'source' => $source,
                        'target' => $target,
                        'code'   => $code,
                        'regex'  => $is_regex,
                    ]));
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                return new \WP_REST_Response(['message' => 'Không tìm thấy rule ID=' . $id], 404);
            }
        } else {
            // Create
            $rules[] = self::normalise_rule([
                'id'         => self::next_id($rules),
                'source'     => $source,
                'target'     => $target,
                'code'       => $code,
                'regex'      => $is_regex,
                'hits'       => 0,
                'last_hit'   => '',
                'created_at' => $now,
            ]);
        }

        self::write_rules($rules);
        return new \WP_REST_Response(['success' => true, 'rules' => self::read_rules()], 200);
    }

    public static function delete_rule(\WP_REST_Request $request) {
        $id    = (int) $request->get_param('id');
        $rules = self::read_rules();

        $filtered = array_values(array_filter($rules, function ($r) use ($id) {
            return (int) ($r['id'] ?? 0) !== $id;
        }));

        if (count($filtered) === count($rules)) {
            return new \WP_REST_Response(['message' => 'Không tìm thấy rule ID=' . $id], 404);
        }

        self::write_rules($filtered);
        return new \WP_REST_Response(['success' => true], 200);
    }

    /* ═══════════════════════════════════════════════════════════════════════
       CSV I/O
       ═══════════════════════════════════════════════════════════════════════ */

    public static function import_csv(\WP_REST_Request $request) {
        $files = $request->get_file_params();
        if (empty($files['file']) && empty($files['csv'])) {
            return new \WP_REST_Response(['message' => 'Chưa chọn file CSV.'], 400);
        }

        $upload = !empty($files['file']) ? $files['file'] : $files['csv'];
        if (!empty($upload['error']) || empty($upload['tmp_name']) || !is_readable($upload['tmp_name'])) {
            return new \WP_REST_Response(['message' => 'File upload lỗi.'], 400);
        }

        $fh = @fopen($upload['tmp_name'], 'r');
        if (!$fh) {
            return new \WP_REST_Response(['message' => 'Không mở được file.'], 500);
        }

        // Strip BOM if present on first read
        $first = fgets($fh);
        if ($first !== false) {
            $first = preg_replace('/^\xEF\xBB\xBF/', '', $first);
            // Reset + write back via seek
            rewind($fh);
            // Just keep going with fgetcsv; manual BOM strip on header row below
        }

        $rules    = self::read_rules();
        $imported = 0;
        $skipped  = 0;
        $errors   = 0;
        $header   = null;
        $next_id  = self::next_id($rules);

        while (($row = fgetcsv($fh, 0, ',')) !== false) {
            if (!$row || (count($row) === 1 && trim((string) $row[0]) === '')) continue;

            // Strip BOM from first cell on first row
            if ($header === null) {
                $row[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $row[0]);
                $head_lc = array_map(function ($c) { return strtolower(trim((string) $c)); }, $row);

                if (in_array('source', $head_lc, true) && in_array('target', $head_lc, true)) {
                    $header = $head_lc;
                    continue;
                }
                // No header row → default column order
                $header = ['source', 'target', 'code', 'regex'];
                // Fall through and process this row as data
            }

            $assoc = [];
            foreach ($header as $i => $key) {
                $assoc[$key] = isset($row[$i]) ? trim((string) $row[$i]) : '';
            }

            $source = $assoc['source'] ?? '';
            $target = $assoc['target'] ?? '';
            if ($source === '' || $target === '') { $skipped++; continue; }

            $code = (int) ($assoc['code'] ?? 301);
            if (!in_array($code, self::ALLOWED_CODES, true)) $code = 301;

            $regex_raw = strtolower((string) ($assoc['regex'] ?? ''));
            $is_regex  = in_array($regex_raw, ['1', 'true', 'yes', 'y', 'on'], true);

            if ($is_regex && @preg_match('#' . str_replace('#', '\\#', $source) . '#i', '') === false) {
                $errors++;
                continue;
            }

            // De-dup by exact source match
            $dup = false;
            foreach ($rules as $r) {
                if (($r['source'] ?? '') === $source) { $dup = true; break; }
            }
            if ($dup) { $skipped++; continue; }

            $rules[] = self::normalise_rule([
                'id'         => $next_id++,
                'source'     => $source,
                'target'     => $target,
                'code'       => $code,
                'regex'      => $is_regex,
                'hits'       => 0,
                'last_hit'   => '',
                'created_at' => current_time('mysql'),
            ]);
            $imported++;
        }
        fclose($fh);

        self::write_rules($rules);

        return new \WP_REST_Response([
            'success'  => true,
            'imported' => $imported,
            'skipped'  => $skipped,
            'errors'   => $errors,
            'total'    => count($rules),
        ], 200);
    }

    public static function export_csv(\WP_REST_Request $request) {
        $rules = self::read_rules();

        // Build CSV body manually
        $lines = [];
        $lines[] = 'source,target,code,regex,hits,last_hit,created_at';
        foreach ($rules as $r) {
            $lines[] = implode(',', [
                self::csv_cell($r['source'] ?? ''),
                self::csv_cell($r['target'] ?? ''),
                (int) ($r['code'] ?? 301),
                !empty($r['regex']) ? '1' : '0',
                (int) ($r['hits'] ?? 0),
                self::csv_cell($r['last_hit'] ?? ''),
                self::csv_cell($r['created_at'] ?? ''),
            ]);
        }
        $csv = "\xEF\xBB\xBF" . implode("\r\n", $lines) . "\r\n";

        $filename = 'pi-redirects-' . gmdate('Y-m-d') . '.csv';

        // Emit binary CSV with download headers.
        // Using nocache_headers + direct echo + die mirrors how other REST
        // "download" endpoints in this codebase emit non-JSON payloads.
        nocache_headers();
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($csv));
        echo $csv;
        exit;
    }

    private static function csv_cell($val) {
        $val = (string) $val;
        if (preg_match('/[",\r\n]/', $val)) {
            return '"' . str_replace('"', '""', $val) . '"';
        }
        return $val;
    }

    /* ═══════════════════════════════════════════════════════════════════════
       404 LOG — LIST / CLEAR
       ═══════════════════════════════════════════════════════════════════════ */

    public static function list_log(\WP_REST_Request $request) {
        $page     = max(1, (int) ($request->get_param('page') ?: 1));
        $per_page = min(500, max(1, (int) ($request->get_param('per_page') ?: 50)));

        $log = self::read_log();

        // Sort: most recent first (by timestamp desc)
        usort($log, function ($a, $b) {
            return strcmp((string) ($b['timestamp'] ?? ''), (string) ($a['timestamp'] ?? ''));
        });

        $total       = count($log);
        $unique_urls = count(array_unique(array_map(function ($l) { return $l['url'] ?? ''; }, $log)));
        $hits_sum    = 0;
        foreach ($log as $l) $hits_sum += (int) ($l['count'] ?? 1);

        $offset = ($page - 1) * $per_page;
        $items  = array_slice($log, $offset, $per_page);

        return new \WP_REST_Response([
            'items'        => $items,
            'total'        => $total,
            'page'         => $page,
            'per_page'     => $per_page,
            'pages'        => max(1, (int) ceil($total / $per_page)),
            'stats'        => [
                'total_404s'  => $hits_sum,
                'unique_urls' => $unique_urls,
                'entries'     => $total,
            ],
        ], 200);
    }

    public static function clear_log(\WP_REST_Request $request) {
        update_option(self::OPT_LOG, [], false);
        return new \WP_REST_Response(['success' => true], 200);
    }

    /* ═══════════════════════════════════════════════════════════════════════
       RUNTIME — auto-redirect + 404 logger
       ═══════════════════════════════════════════════════════════════════════ */

    private static $booted = false;

    public static function maybe_bootstrap_runtime() {
        if (self::$booted) return;
        self::$booted = true;

        // Hook late enough that WP has resolved is_404(), early enough to avoid output
        add_action('template_redirect', [self::class, 'handle_request'], 1);
    }

    public static function handle_request() {
        if (is_admin() || (defined('DOING_AJAX') && DOING_AJAX) || (defined('DOING_CRON') && DOING_CRON)) return;
        if (defined('REST_REQUEST') && REST_REQUEST) return;

        $uri = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '';
        if ($uri === '') return;

        $path = self::canonical_path($uri);

        // 1) Auto-redirect on matching rule
        $rule = self::find_match($path);
        if ($rule) {
            self::perform_redirect($rule, $path);
            return;
        }

        // 2) 404 logger
        if (function_exists('is_404') && is_404()) {
            self::log_404($path);
        }
    }

    private static function canonical_path($uri) {
        // Strip home-path prefix on sub-directory installs
        $home_path = parse_url(home_url('/'), PHP_URL_PATH) ?: '/';
        if ($home_path !== '/' && strpos($uri, $home_path) === 0) {
            $uri = substr($uri, strlen($home_path) - 1);
        }
        return $uri;
    }

    private static function find_match($path) {
        $rules = self::read_rules();
        foreach ($rules as $rule) {
            $src = (string) ($rule['source'] ?? '');
            if ($src === '') continue;

            if (!empty($rule['regex'])) {
                $pattern = '#' . str_replace('#', '\\#', $src) . '#i';
                if (@preg_match($pattern, $path) === 1) return $rule;
            } else {
                if ($path === $src) return $rule;
            }
        }
        return null;
    }

    private static function perform_redirect(array $rule, $current_path) {
        $code   = (int) ($rule['code'] ?? 301);
        $target = (string) ($rule['target'] ?? '');

        // Guard against loop: don't redirect to same path
        if ($target === '' || $target === $current_path) return;

        // Regex target supports $1 backrefs
        if (!empty($rule['regex'])) {
            $pattern = '#' . str_replace('#', '\\#', (string) $rule['source']) . '#i';
            $target  = @preg_replace($pattern, $target, $current_path);
            if (!is_string($target) || $target === '') return;
        }

        // Bump hit counter
        self::bump_hit((int) ($rule['id'] ?? 0));

        if ($code === 410) {
            status_header(410);
            nocache_headers();
            exit;
        }

        $url = self::normalise_target($target);
        nocache_headers();
        wp_redirect($url, in_array($code, [301, 302, 307], true) ? $code : 301);
        exit;
    }

    private static function normalise_target($target) {
        if (preg_match('~^https?://~i', $target)) return $target;
        return home_url($target);
    }

    private static function bump_hit($id) {
        if ($id <= 0) return;
        $rules = self::read_rules();
        foreach ($rules as $i => $r) {
            if ((int) ($r['id'] ?? 0) === $id) {
                $rules[$i]['hits']     = (int) ($r['hits'] ?? 0) + 1;
                $rules[$i]['last_hit'] = current_time('mysql');
                break;
            }
        }
        self::write_rules($rules);
    }

    private static function log_404($path) {
        $log     = self::read_log();
        $now     = current_time('mysql');
        $referer = isset($_SERVER['HTTP_REFERER']) ? substr((string) $_SERVER['HTTP_REFERER'], 0, 500) : '';
        $ua      = isset($_SERVER['HTTP_USER_AGENT']) ? substr((string) $_SERVER['HTTP_USER_AGENT'], 0, 300) : '';
        $ip      = self::masked_ip((string) ($_SERVER['REMOTE_ADDR'] ?? ''));

        // Upsert by url
        $found = false;
        foreach ($log as $i => $entry) {
            if (($entry['url'] ?? '') === $path) {
                $log[$i]['count']      = (int) ($entry['count'] ?? 1) + 1;
                $log[$i]['timestamp']  = $now;
                $log[$i]['referrer']   = $referer;
                $log[$i]['user_agent'] = $ua;
                $log[$i]['ip']         = $ip;
                $found = true;
                break;
            }
        }

        if (!$found) {
            $log[] = [
                'url'        => $path,
                'referrer'   => $referer,
                'user_agent' => $ua,
                'ip'         => $ip,
                'timestamp'  => $now,
                'count'      => 1,
            ];
        }

        self::write_log($log);
    }

    private static function masked_ip($ip) {
        if ($ip === '') return '';
        // IPv4: strip last octet
        if (strpos($ip, '.') !== false) {
            $parts = explode('.', $ip);
            if (count($parts) === 4) { $parts[3] = '0'; return implode('.', $parts); }
        }
        // IPv6: strip last group
        if (strpos($ip, ':') !== false) {
            $parts = explode(':', $ip);
            $parts[count($parts) - 1] = '0';
            return implode(':', $parts);
        }
        return substr($ip, 0, 64);
    }
}

/* ─────────────────────────────────────────────────────────────────────────
   Bootstrap runtime even when REST isn't being hit. The Controller only
   calls register_routes() during rest_api_init; template_redirect must fire
   on every public hit, so we also wire it up on plugins_loaded.
   ───────────────────────────────────────────────────────────────────────── */
add_action('plugins_loaded', function () {
    if (class_exists('Pi_Api_SeoRedirects')) {
        Pi_Api_SeoRedirects::maybe_bootstrap_runtime();
    }
}, 20);
