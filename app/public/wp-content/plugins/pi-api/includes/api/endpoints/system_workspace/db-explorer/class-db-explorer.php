<?php
/**
 * Pi API Database Explorer Endpoints (Pro feature, READ-ONLY)
 *
 * Provides safe, read-only access to the WordPress database for advanced
 * users on Pro/Max tiers. All write operations are rejected at the
 * gateway level via SQL regex sanitisation.
 *
 * Endpoints:
 *   GET    /pi/v1/db/tables                    list tables (rows + size)
 *   GET    /pi/v1/db/table/{table}/schema      column / index / FK info
 *   GET    /pi/v1/db/table/{table}/rows        paginated rows
 *   POST   /pi/v1/db/query                     execute parameterised SELECT
 *   GET    /pi/v1/db/saved-queries             per-user saved queries
 *   POST   /pi/v1/db/saved-queries             save a query
 *   DELETE /pi/v1/db/saved-queries/{id}        remove saved query
 *   POST   /pi/v1/db/export                    export rows as csv|json
 *
 * Security:
 *   - current_user_can('manage_options')
 *   - Pro tier check via option `pi_license_tier`
 *   - SELECT-only regex enforcement (rejects INSERT/UPDATE/DELETE/DROP/etc.)
 *   - Hard cap of 1000 rows per query
 *   - 5-second statement timeout (best-effort via MAX_EXECUTION_TIME hint)
 */

if (!defined('ABSPATH')) exit;

class Pi_Api_DbExplorer {

    /** Hard limit: never return more than this many rows. */
    const MAX_ROWS = 1000;

    /** Soft statement timeout in milliseconds (5s). */
    const TIMEOUT_MS = 5000;

    /**
     * Forbidden SQL keywords — anything that mutates state.
     * NOTE: `\bLOAD\b` alone does NOT match `LOAD_FILE(` — `\b` requires a
     * transition between a word char and a non-word char, and `_` counts as
     * a word char, so "LOAD_FILE" is one unbroken word token as far as the
     * regex engine is concerned. LOAD_FILE() reads arbitrary local files
     * (if the DB user has FILE privilege), defeating the "SELECT-only, safe
     * read-only" guarantee this class documents. Blocked explicitly below.
     */
    const FORBIDDEN_PATTERNS = [
        '/\b(INSERT|UPDATE|DELETE|DROP|ALTER|TRUNCATE|CREATE|RENAME|REPLACE|GRANT|REVOKE|LOCK|UNLOCK|CALL|HANDLER|LOAD|OPTIMIZE|REPAIR|FLUSH)\b/i',
        '/\bLOAD_FILE\s*\(/i',
        '/\bINTO\s+OUTFILE\b/i',
        '/\bINTO\s+DUMPFILE\b/i',
        '/;\s*\S/', // multiple statements
    ];

    public static function register_routes($ns, $admin) {
        // tables list
        register_rest_route($ns, '/db/tables', [
            'methods'  => 'GET',
            'callback' => [self::class, 'list_tables'],
            'permission_callback' => [self::class, 'permission_check'],
        ]);

        // schema for one table
        register_rest_route($ns, '/db/table/(?P<table>[A-Za-z0-9_]+)/schema', [
            'methods'  => 'GET',
            'callback' => [self::class, 'get_schema'],
            'permission_callback' => [self::class, 'permission_check'],
            'args' => ['table' => ['type' => 'string', 'required' => true]],
        ]);

        // paginated rows
        register_rest_route($ns, '/db/table/(?P<table>[A-Za-z0-9_]+)/rows', [
            'methods'  => 'GET',
            'callback' => [self::class, 'get_rows'],
            'permission_callback' => [self::class, 'permission_check'],
            'args' => ['table' => ['type' => 'string', 'required' => true]],
        ]);

        // run a SELECT query
        register_rest_route($ns, '/db/query', [
            'methods'  => 'POST',
            'callback' => [self::class, 'run_query'],
            'permission_callback' => [self::class, 'permission_check'],
        ]);

        // saved queries
        register_rest_route($ns, '/db/saved-queries', [
            ['methods'  => 'GET',  'callback' => [self::class, 'list_saved'], 'permission_callback' => [self::class, 'permission_check']],
            ['methods'  => 'POST', 'callback' => [self::class, 'save_query'], 'permission_callback' => [self::class, 'permission_check']],
        ]);
        register_rest_route($ns, '/db/saved-queries/(?P<id>\d+)', [
            'methods'  => 'DELETE',
            'callback' => [self::class, 'delete_saved'],
            'permission_callback' => [self::class, 'permission_check'],
            'args' => ['id' => ['type' => 'integer', 'required' => true]],
        ]);

        // export
        register_rest_route($ns, '/db/export', [
            'methods'  => 'POST',
            'callback' => [self::class, 'export'],
            'permission_callback' => [self::class, 'permission_check'],
        ]);
    }

    // ─────────────────────────────────────────── permission ────────────────

    /**
     * Permission gate: must be admin AND on Pro/Max tier.
     * Tier is read from option `pi_license_tier` (set by license manager).
     */
    public static function permission_check() {
        if (!current_user_can('manage_options')) return false;
        $tier = strtolower((string) get_option('pi_license_tier', 'free'));
        return in_array($tier, ['pro', 'max', 'enterprise'], true);
    }

    // ─────────────────────────────────────────── safety helpers ────────────

    /**
     * Validate that an SQL string is read-only (SELECT-only).
     *
     * Returns null if safe, or a string error message if rejected.
     * Public so unit tests in JS or PHP can call it directly.
     */
    public static function is_safe_select($sql) {
        $sql = trim((string) $sql);
        if ($sql === '') return 'Truy vấn rỗng.';

        // Strip trailing semicolon for the multi-statement check.
        $body = rtrim($sql, " \t\n\r;");

        // Must start with SELECT or WITH.
        if (!preg_match('/^\s*(SELECT|WITH)\b/i', $body)) {
            return 'Chỉ cho phép câu lệnh SELECT.';
        }

        foreach (self::FORBIDDEN_PATTERNS as $pat) {
            if (preg_match($pat, $body)) {
                return 'Phát hiện từ khóa bị cấm. Chỉ cho phép SELECT thuần.';
            }
        }
        return null;
    }

    /**
     * Validate a table identifier and quote it for SQL.
     * Only A-Z, a-z, 0-9, underscore allowed. Otherwise null.
     */
    private static function safe_table($name) {
        if (!is_string($name) || $name === '') return null;
        if (!preg_match('/^[A-Za-z0-9_]+$/', $name)) return null;
        global $wpdb;
        // Confirm the table actually exists in this DB.
        $exists = $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $name));
        return ($exists === $name) ? '`' . $name . '`' : null;
    }

    /**
     * Inject a hard LIMIT/OFFSET if missing, capped at MAX_ROWS.
     */
    private static function clamp_limit($sql, $limit = null) {
        $limit = $limit ? min((int) $limit, self::MAX_ROWS) : self::MAX_ROWS;
        if (preg_match('/\bLIMIT\b/i', $sql)) {
            // Existing LIMIT — let it through (we still cap rows on output).
            return $sql;
        }
        return rtrim($sql, " \t\n\r;") . " LIMIT {$limit}";
    }

    // ─────────────────────────────────────────── tables list ───────────────

    public static function list_tables() {
        global $wpdb;
        $rows = $wpdb->get_results(
            "SHOW TABLE STATUS FROM `" . DB_NAME . "`",
            ARRAY_A
        );
        $out = [];
        foreach ((array) $rows as $r) {
            $size = (int) ($r['Data_length'] ?? 0) + (int) ($r['Index_length'] ?? 0);
            $out[] = [
                'name'      => $r['Name'],
                'engine'    => $r['Engine'] ?? null,
                'rows'      => (int) ($r['Rows'] ?? 0),
                'size'      => $size,
                'size_fmt'  => size_format($size),
                'collation' => $r['Collation'] ?? null,
                'comment'   => $r['Comment'] ?? '',
            ];
        }
        return rest_ensure_response($out);
    }

    // ─────────────────────────────────────────── schema ────────────────────

    public static function get_schema($req) {
        $tbl = self::safe_table($req->get_param('table'));
        if (!$tbl) return new WP_Error('pi_db_invalid_table', 'Tên bảng không hợp lệ.', ['status' => 400]);
        global $wpdb;

        // Columns
        $cols = $wpdb->get_results("SHOW FULL COLUMNS FROM {$tbl}", ARRAY_A) ?: [];
        $columns = array_map(function ($c) {
            return [
                'name'     => $c['Field'],
                'type'     => $c['Type'],
                'nullable' => strtoupper($c['Null'] ?? '') === 'YES',
                'default'  => $c['Default'],
                'key'      => $c['Key'] ?? '',
                'extra'    => $c['Extra'] ?? '',
                'comment'  => $c['Comment'] ?? '',
                'pk'       => ($c['Key'] ?? '') === 'PRI',
            ];
        }, $cols);

        // Indexes
        $idx = $wpdb->get_results("SHOW INDEX FROM {$tbl}", ARRAY_A) ?: [];
        $indexes = array_map(function ($i) {
            return [
                'name'    => $i['Key_name'],
                'column'  => $i['Column_name'],
                'unique'  => (int) ($i['Non_unique'] ?? 1) === 0,
                'type'    => $i['Index_type'] ?? '',
                'seq'     => (int) ($i['Seq_in_index'] ?? 0),
            ];
        }, $idx);

        // Foreign keys (best-effort via information_schema)
        $tname = trim($tbl, '`');
        $fks = $wpdb->get_results($wpdb->prepare(
            "SELECT COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME, CONSTRAINT_NAME
             FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND REFERENCED_TABLE_NAME IS NOT NULL",
            DB_NAME,
            $tname
        ), ARRAY_A) ?: [];
        $foreign_keys = array_map(function ($f) {
            return [
                'name'             => $f['CONSTRAINT_NAME'],
                'column'           => $f['COLUMN_NAME'],
                'referenced_table' => $f['REFERENCED_TABLE_NAME'],
                'referenced_col'   => $f['REFERENCED_COLUMN_NAME'],
            ];
        }, $fks);

        // CREATE TABLE DDL
        $ddl_row = $wpdb->get_row("SHOW CREATE TABLE {$tbl}", ARRAY_N);
        $ddl = $ddl_row[1] ?? '';

        return rest_ensure_response([
            'table'        => $tname,
            'columns'      => $columns,
            'indexes'      => $indexes,
            'foreign_keys' => $foreign_keys,
            'ddl'          => $ddl,
        ]);
    }

    // ─────────────────────────────────────────── rows ──────────────────────

    public static function get_rows($req) {
        $tbl = self::safe_table($req->get_param('table'));
        if (!$tbl) return new WP_Error('pi_db_invalid_table', 'Tên bảng không hợp lệ.', ['status' => 400]);
        global $wpdb;

        $page     = max(1, (int) $req->get_param('page'));
        $per_page = max(1, min(self::MAX_ROWS, (int) ($req->get_param('per_page') ?: 50)));
        $offset   = ($page - 1) * $per_page;

        // Order by — must be a valid column name (alpha + _ + .)
        $order_by = $req->get_param('order_by');
        $order_sql = '';
        if ($order_by && preg_match('/^[A-Za-z0-9_]+(\s+(ASC|DESC))?$/i', $order_by)) {
            $order_sql = ' ORDER BY ' . $order_by;
        }

        // Count
        $total = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$tbl}");

        // Rows
        $sql = "SELECT * FROM {$tbl}{$order_sql} LIMIT %d OFFSET %d";
        $rows = $wpdb->get_results($wpdb->prepare($sql, $per_page, $offset), ARRAY_A) ?: [];

        return rest_ensure_response([
            'rows'     => $rows,
            'total'    => $total,
            'page'     => $page,
            'per_page' => $per_page,
            'pages'    => (int) ceil($total / max(1, $per_page)),
        ]);
    }

    // ─────────────────────────────────────────── query ─────────────────────

    public static function run_query($req) {
        $body = $req->get_json_params();
        $sql  = isset($body['sql']) ? (string) $body['sql'] : '';
        $args = isset($body['params']) && is_array($body['params']) ? $body['params'] : [];

        $err = self::is_safe_select($sql);
        if ($err) return new WP_Error('pi_db_unsafe_sql', $err, ['status' => 400]);

        global $wpdb;
        $sql_clamped = self::clamp_limit($sql);

        // Hint at a max execution time. Best-effort, and NOT interchangeable
        // between engines: MySQL's `/*+ MAX_EXECUTION_TIME(n) */` optimizer
        // hint is silently ignored (treated as an inert comment) on MariaDB
        // — which is what most local/shared hosting actually runs — so a
        // SELECT SLEEP() or an expensive join would run unbounded there
        // despite this class documenting a "5-second query timeout". Detect
        // the engine and use MariaDB's real per-statement equivalent
        // (`SET STATEMENT max_statement_time=n FOR <stmt>`, 10.1.1+) instead.
        $is_mariadb = false;
        if (!empty($wpdb->use_mysqli) && $wpdb->dbh instanceof \mysqli) {
            $server_info = @mysqli_get_server_info($wpdb->dbh);
            $is_mariadb  = $server_info && stripos($server_info, 'mariadb') !== false;
        }

        if ($is_mariadb) {
            // MariaDB's max_statement_time is in SECONDS (fractional allowed) — TIMEOUT_MS is milliseconds.
            if (!preg_match('/max_statement_time/i', $sql_clamped)) {
                $seconds = self::TIMEOUT_MS / 1000;
                $sql_clamped = 'SET STATEMENT max_statement_time=' . $seconds . ' FOR ' . $sql_clamped;
            }
        } elseif (!preg_match('/MAX_EXECUTION_TIME/i', $sql_clamped) && stripos($sql_clamped, 'select') !== false) {
            $sql_clamped = preg_replace(
                '/^\s*SELECT\b/i',
                'SELECT /*+ MAX_EXECUTION_TIME(' . self::TIMEOUT_MS . ') */',
                $sql_clamped,
                1
            );
        }

        $start = microtime(true);
        // Run prepared if params present, else raw (already validated read-only).
        if (count($args)) {
            $prepared = $wpdb->prepare($sql_clamped, $args);
            if ($prepared === false || $prepared === null) {
                return new WP_Error('pi_db_prepare_failed', 'Không thể chuẩn bị truy vấn (kiểm tra placeholder).', ['status' => 400]);
            }
            $rows = $wpdb->get_results($prepared, ARRAY_A);
        } else {
            $rows = $wpdb->get_results($sql_clamped, ARRAY_A);
        }
        $elapsed = round((microtime(true) - $start) * 1000, 2);

        if ($wpdb->last_error) {
            return new WP_Error('pi_db_query_error', $wpdb->last_error, ['status' => 400]);
        }

        $rows = $rows ?: [];
        $columns = $rows ? array_keys($rows[0]) : [];

        // Cap rows defensively even though LIMIT was injected.
        if (count($rows) > self::MAX_ROWS) {
            $rows = array_slice($rows, 0, self::MAX_ROWS);
        }

        return rest_ensure_response([
            'columns'     => $columns,
            'rows'        => $rows,
            'row_count'   => count($rows),
            'elapsed_ms'  => $elapsed,
            'truncated'   => count($rows) >= self::MAX_ROWS,
        ]);
    }

    // ─────────────────────────────────────────── saved queries ─────────────

    /**
     * Ensure the saved-queries table exists (lazy create on first use).
     */
    private static function ensure_saved_table() {
        global $wpdb;
        $table = $wpdb->prefix . 'pi_saved_queries';
        $charset = $wpdb->get_charset_collate();

        $exists = $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $table));
        if ($exists !== $table) {
            $sql = "CREATE TABLE IF NOT EXISTS {$table} (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                user_id BIGINT UNSIGNED NOT NULL,
                name VARCHAR(190) NOT NULL,
                sql_text LONGTEXT NOT NULL,
                created_at DATETIME NOT NULL,
                last_run_at DATETIME NULL,
                run_count INT UNSIGNED NOT NULL DEFAULT 0,
                PRIMARY KEY  (id),
                KEY user_id (user_id)
            ) {$charset};";
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            dbDelta($sql);
        }
        return $table;
    }

    public static function list_saved() {
        global $wpdb;
        $table = self::ensure_saved_table();
        $uid = get_current_user_id();
        $rows = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT id, name, sql_text, created_at, last_run_at, run_count
                 FROM {$table} WHERE user_id = %d
                 ORDER BY id DESC",
                $uid
            ),
            ARRAY_A
        ) ?: [];
        return rest_ensure_response($rows);
    }

    public static function save_query($req) {
        global $wpdb;
        $table = self::ensure_saved_table();
        $body  = $req->get_json_params();
        $name  = trim((string) ($body['name'] ?? ''));
        $sql   = trim((string) ($body['sql'] ?? ''));

        if ($name === '' || $sql === '') {
            return new WP_Error('pi_db_missing', 'Cần có cả tên và SQL.', ['status' => 400]);
        }
        $err = self::is_safe_select($sql);
        if ($err) return new WP_Error('pi_db_unsafe_sql', $err, ['status' => 400]);

        $uid = get_current_user_id();
        $wpdb->insert($table, [
            'user_id'    => $uid,
            'name'       => $name,
            'sql_text'   => $sql,
            'created_at' => current_time('mysql'),
            'run_count'  => 0,
        ], ['%d', '%s', '%s', '%s', '%d']);

        return rest_ensure_response([
            'id'         => (int) $wpdb->insert_id,
            'name'       => $name,
            'sql_text'   => $sql,
            'created_at' => current_time('mysql'),
            'run_count'  => 0,
        ]);
    }

    public static function delete_saved($req) {
        global $wpdb;
        $table = self::ensure_saved_table();
        $id = (int) $req->get_param('id');
        $uid = get_current_user_id();
        $rows = $wpdb->delete($table, ['id' => $id, 'user_id' => $uid], ['%d', '%d']);
        if (!$rows) {
            return new WP_Error('pi_db_not_found', 'Không tìm thấy truy vấn đã lưu.', ['status' => 404]);
        }
        return rest_ensure_response(['deleted' => true, 'id' => $id]);
    }

    // ─────────────────────────────────────────── export ────────────────────

    public static function export($req) {
        $body   = $req->get_json_params();
        $table  = isset($body['table']) ? (string) $body['table'] : '';
        $sql    = isset($body['query']) ? (string) $body['query'] : '';
        $format = strtolower((string) ($body['format'] ?? 'csv'));
        if (!in_array($format, ['csv', 'json'], true)) {
            return new WP_Error('pi_db_format', 'Định dạng phải là csv hoặc json.', ['status' => 400]);
        }

        global $wpdb;
        $rows = [];
        if ($sql) {
            $err = self::is_safe_select($sql);
            if ($err) return new WP_Error('pi_db_unsafe_sql', $err, ['status' => 400]);
            $rows = $wpdb->get_results(self::clamp_limit($sql), ARRAY_A) ?: [];
        } elseif ($table) {
            $tbl = self::safe_table($table);
            if (!$tbl) return new WP_Error('pi_db_invalid_table', 'Tên bảng không hợp lệ.', ['status' => 400]);
            $rows = $wpdb->get_results("SELECT * FROM {$tbl} LIMIT " . self::MAX_ROWS, ARRAY_A) ?: [];
        } else {
            return new WP_Error('pi_db_missing', 'Cần cung cấp table hoặc query.', ['status' => 400]);
        }

        if ($format === 'json') {
            return rest_ensure_response([
                'format' => 'json',
                'rows'   => $rows,
                'count'  => count($rows),
            ]);
        }

        // CSV — build content as a string
        $out = fopen('php://temp', 'r+');
        if ($rows) {
            fputcsv($out, array_keys($rows[0]));
            foreach ($rows as $r) fputcsv($out, $r);
        }
        rewind($out);
        $csv = stream_get_contents($out);
        fclose($out);

        return rest_ensure_response([
            'format'   => 'csv',
            'content'  => $csv,
            'count'    => count($rows),
        ]);
    }
}
