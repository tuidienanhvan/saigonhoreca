<?php
/**
 * Pi API Backup & Restore Endpoints — Phase 4 Task C.
 *
 * Endpoints
 * ─────────────────────────────────────────────────────────────
 *   GET    /pi/v1/backup/list                     List backups
 *   POST   /pi/v1/backup/create                   Queue new backup job
 *   GET    /pi/v1/backup/job/{job_id}             Job status
 *   POST   /pi/v1/backup/restore                  Restore (danger!)
 *   DELETE /pi/v1/backup/{id}                     Delete backup file
 *   GET    /pi/v1/backup/download/{id}            Issue secure download URL
 *   GET    /pi/v1/backup/schedules                List schedules
 *   POST   /pi/v1/backup/schedules                Create schedule
 *   DELETE /pi/v1/backup/schedules/{id}           Delete schedule
 *
 * Storage: wp-content/uploads/pi-backups/{filename}.zip
 *
 * Tables (auto-created on first call)
 * ─────────────────────────────────────────────────────────────
 *   wp_pi_backups            (id, filename, type, size_bytes, status,
 *                             file_path, created_at, expires_at)
 *   wp_pi_backup_schedules   (id, frequency, type, retention_count,
 *                             target, target_config JSON, last_run, next_run)
 *
 * Strategy
 * ─────────────────────────────────────────────────────────────
 * - DB backup: prefer mysqldump via shell_exec; fall back to a PHP-based
 *   table-by-table dumper if disabled.
 * - Files backup: ZipArchive of wp-content (excluding cache & optionally
 *   uploads).
 * - Background: jobs are deferred via wp_schedule_single_event so the
 *   request returns immediately with a job_id; status polled via
 *   /backup/job/{id}.
 * - S3/Drive targets are stubbed: log "would upload to ...".
 */

if (!defined('ABSPATH')) exit;

class Pi_Api_Backup {

    /* ───────────────────────────── constants ───────────────────────────── */
    const TABLE_BACKUPS   = 'pi_backups';
    const TABLE_SCHEDULES = 'pi_backup_schedules';
    const DOWNLOAD_TTL    = 600; // seconds for tokenised download URL

    /* ─────────────────────────── route registration ────────────────────── */
    public static function register_routes($ns, $admin) {
        register_rest_route($ns, '/backup/list', [
            'methods'  => 'GET',
            'callback' => [self::class, 'list_backups'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/backup/create', [
            'methods'  => 'POST',
            'callback' => [self::class, 'create_backup'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/backup/job/(?P<job_id>[A-Za-z0-9_\-]+)', [
            'methods'  => 'GET',
            'callback' => [self::class, 'get_job_status'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/backup/restore', [
            'methods'  => 'POST',
            'callback' => [self::class, 'restore_backup'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/backup/(?P<id>\d+)', [
            'methods'  => 'DELETE',
            'callback' => [self::class, 'delete_backup'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/backup/download/(?P<id>\d+)', [
            'methods'  => 'GET',
            'callback' => [self::class, 'download_backup'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/backup/schedules', [
            ['methods' => 'GET',  'callback' => [self::class, 'list_schedules'],  'permission_callback' => $admin],
            ['methods' => 'POST', 'callback' => [self::class, 'create_schedule'], 'permission_callback' => $admin],
        ]);

        register_rest_route($ns, '/backup/schedules/(?P<id>\d+)', [
            'methods'  => 'DELETE',
            'callback' => [self::class, 'delete_schedule'],
            'permission_callback' => $admin,
        ]);

        // Cron callback: the action that wp_schedule_single_event fires.
        add_action('pi_backup_run_job', [self::class, 'run_job_async'], 10, 1);
    }

    /* ─────────────────────────── DB schema ─────────────────────────── */
    private static function ensure_tables() {
        global $wpdb;
        $charset = $wpdb->get_charset_collate();
        $t1 = $wpdb->prefix . self::TABLE_BACKUPS;
        $t2 = $wpdb->prefix . self::TABLE_SCHEDULES;

        $exists = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $t1));
        if ($exists !== $t1) {
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            dbDelta("CREATE TABLE $t1 (
                id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                filename VARCHAR(255) NOT NULL,
                type VARCHAR(20) NOT NULL DEFAULT 'full',
                size_bytes BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
                status VARCHAR(20) NOT NULL DEFAULT 'queued',
                file_path TEXT NOT NULL,
                progress TINYINT UNSIGNED NOT NULL DEFAULT 0,
                error_message TEXT NULL,
                job_id VARCHAR(40) NULL,
                created_at DATETIME NOT NULL,
                expires_at DATETIME NULL,
                PRIMARY KEY  (id),
                KEY job_id (job_id),
                KEY created_at (created_at)
            ) $charset;");

            dbDelta("CREATE TABLE $t2 (
                id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                frequency VARCHAR(20) NOT NULL DEFAULT 'daily',
                type VARCHAR(20) NOT NULL DEFAULT 'full',
                retention_count INT UNSIGNED NOT NULL DEFAULT 7,
                target VARCHAR(20) NOT NULL DEFAULT 'local',
                target_config LONGTEXT NULL,
                paused TINYINT(1) NOT NULL DEFAULT 0,
                last_run DATETIME NULL,
                next_run DATETIME NULL,
                created_at DATETIME NOT NULL,
                PRIMARY KEY  (id)
            ) $charset;");
        }
    }

    private static function backup_dir() {
        $up = wp_upload_dir();
        $dir = trailingslashit($up['basedir']) . 'pi-backups';
        if (!file_exists($dir)) wp_mkdir_p($dir);
        // Drop a .htaccess so the directory cannot be listed (Apache).
        $ht = $dir . '/.htaccess';
        if (!file_exists($ht)) @file_put_contents($ht, "Order deny,allow\nDeny from all\n");
        return $dir;
    }

    /* ─────────────────────────── list / delete / download ─────────────────────────── */
    public static function list_backups(\WP_REST_Request $req) {
        self::ensure_tables();
        global $wpdb;
        $t = $wpdb->prefix . self::TABLE_BACKUPS;
        $rows = $wpdb->get_results("SELECT * FROM $t ORDER BY created_at DESC LIMIT 200", ARRAY_A) ?: [];

        // Aggregate stats
        $total_size = 0; $oldest = null;
        foreach ($rows as $r) {
            $total_size += (int)$r['size_bytes'];
            if (!$oldest || $r['created_at'] < $oldest) $oldest = $r['created_at'];
        }

        return new \WP_REST_Response([
            'backups' => array_map([self::class, 'shape_backup'], $rows),
            'stats'   => [
                'total'      => count($rows),
                'total_size' => $total_size,
                'oldest'     => $oldest,
            ],
        ], 200);
    }

    private static function shape_backup($row) {
        return [
            'id'         => (int)$row['id'],
            'filename'   => $row['filename'],
            'type'       => $row['type'],
            'size_bytes' => (int)$row['size_bytes'],
            'status'     => $row['status'],
            'progress'   => (int)$row['progress'],
            'error'      => $row['error_message'] ?? null,
            'job_id'     => $row['job_id'] ?? null,
            'created_at' => $row['created_at'],
            'expires_at' => $row['expires_at'],
        ];
    }

    public static function delete_backup(\WP_REST_Request $req) {
        self::ensure_tables();
        global $wpdb;
        $id = (int)$req['id'];
        $t = $wpdb->prefix . self::TABLE_BACKUPS;
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $t WHERE id = %d", $id), ARRAY_A);
        if (!$row) return new \WP_REST_Response(['error' => 'Không tìm thấy backup'], 404);

        if (!empty($row['file_path']) && file_exists($row['file_path'])) {
            @unlink($row['file_path']);
        }
        $wpdb->delete($t, ['id' => $id]);
        return new \WP_REST_Response(['success' => true], 200);
    }

    public static function download_backup(\WP_REST_Request $req) {
        self::ensure_tables();
        global $wpdb;
        $id = (int)$req['id'];
        $t = $wpdb->prefix . self::TABLE_BACKUPS;
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $t WHERE id = %d", $id), ARRAY_A);
        if (!$row) return new \WP_REST_Response(['error' => 'Không tìm thấy backup'], 404);

        // Mint a short-lived token bound to id + user + expiry.
        $expires = time() + self::DOWNLOAD_TTL;
        $payload = $id . '|' . get_current_user_id() . '|' . $expires;
        $secret = wp_salt('auth');
        $sig = hash_hmac('sha256', $payload, $secret);
        $token = rtrim(strtr(base64_encode($payload . '|' . $sig), '+/', '-_'), '=');

        $url = add_query_arg([
            'action' => 'pi_backup_download',
            'token'  => $token,
        ], admin_url('admin-ajax.php'));

        return new \WP_REST_Response([
            'url'        => $url,
            'expires_at' => date('c', $expires),
            'filename'   => $row['filename'],
        ], 200);
    }

    /* ─────────────────────────── create / job status ─────────────────────────── */
    public static function create_backup(\WP_REST_Request $req) {
        self::ensure_tables();
        $params = $req->get_json_params();
        $type = in_array($params['type'] ?? 'full', ['db', 'files', 'full'], true)
            ? $params['type'] : 'full';
        $exclude_uploads = !empty($params['exclude_uploads']);

        global $wpdb;
        $t = $wpdb->prefix . self::TABLE_BACKUPS;
        $job_id = 'job_' . wp_generate_password(16, false, false);
        $now = current_time('mysql');
        $stamp = date('Ymd-His');
        $filename = "pi-backup-{$type}-{$stamp}.zip";
        $path = trailingslashit(self::backup_dir()) . $filename;

        $wpdb->insert($t, [
            'filename'   => $filename,
            'type'       => $type,
            'size_bytes' => 0,
            'status'     => 'queued',
            'file_path'  => $path,
            'progress'   => 0,
            'job_id'     => $job_id,
            'created_at' => $now,
        ]);
        $backup_id = (int)$wpdb->insert_id;

        // Defer execution to WP-Cron so the request returns fast.
        wp_schedule_single_event(time() + 1, 'pi_backup_run_job', [[
            'backup_id'       => $backup_id,
            'job_id'          => $job_id,
            'type'            => $type,
            'exclude_uploads' => $exclude_uploads,
        ]]);

        return new \WP_REST_Response([
            'job_id'    => $job_id,
            'backup_id' => $backup_id,
            'status'    => 'queued',
        ], 202);
    }

    public static function get_job_status(\WP_REST_Request $req) {
        self::ensure_tables();
        global $wpdb;
        $job_id = sanitize_text_field($req['job_id']);
        $t = $wpdb->prefix . self::TABLE_BACKUPS;
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $t WHERE job_id = %s", $job_id), ARRAY_A);
        if (!$row) return new \WP_REST_Response(['error' => 'Không tìm thấy job'], 404);

        $resp = [
            'status'   => $row['status'],
            'progress' => (int)$row['progress'],
            'job_id'   => $row['job_id'],
            'backup_id'=> (int)$row['id'],
        ];
        if ($row['status'] === 'failed') {
            $resp['error'] = $row['error_message'] ?: 'Lỗi không xác định';
        }
        if ($row['status'] === 'complete') {
            $up = wp_upload_dir();
            $resp['file_url'] = trailingslashit($up['baseurl']) . 'pi-backups/' . $row['filename'];
            $resp['size_bytes'] = (int)$row['size_bytes'];
        }
        return new \WP_REST_Response($resp, 200);
    }

    /* ─────────────────────────── async runner (WP-Cron) ─────────────────────────── */
    public static function run_job_async($args) {
        if (!is_array($args)) return;
        self::ensure_tables();
        global $wpdb;
        $t = $wpdb->prefix . self::TABLE_BACKUPS;
        $backup_id = (int)($args['backup_id'] ?? 0);
        if (!$backup_id) return;

        $wpdb->update($t, ['status' => 'running', 'progress' => 5], ['id' => $backup_id]);

        try {
            $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $t WHERE id = %d", $backup_id), ARRAY_A);
            if (!$row) return;
            $path = $row['file_path'];

            switch ($args['type']) {
                case 'db':
                    self::do_db_backup($path, $backup_id);
                    break;
                case 'files':
                    self::do_files_backup($path, $backup_id, !empty($args['exclude_uploads']));
                    break;
                case 'full':
                default:
                    self::do_full_backup($path, $backup_id, !empty($args['exclude_uploads']));
                    break;
            }

            $size = file_exists($path) ? filesize($path) : 0;
            $wpdb->update($t, [
                'status'     => 'complete',
                'progress'   => 100,
                'size_bytes' => $size,
            ], ['id' => $backup_id]);
        } catch (\Throwable $e) {
            $wpdb->update($t, [
                'status'        => 'failed',
                'error_message' => substr($e->getMessage(), 0, 500),
            ], ['id' => $backup_id]);
        }
    }

    /* ─────────────────────────── DB backup ─────────────────────────── */
    private static function do_db_backup($zip_path, $backup_id) {
        global $wpdb;
        $t = $wpdb->prefix . self::TABLE_BACKUPS;
        $tmp_sql = $zip_path . '.sql';

        // Try mysqldump first.
        $dumped = false;
        if (function_exists('shell_exec') && self::is_shell_exec_safe()) {
            $cmd = sprintf(
                'mysqldump --user=%s --password=%s --host=%s %s > %s 2>&1',
                escapeshellarg(DB_USER),
                escapeshellarg(DB_PASSWORD),
                escapeshellarg(DB_HOST),
                escapeshellarg(DB_NAME),
                escapeshellarg($tmp_sql)
            );
            @shell_exec($cmd);
            if (file_exists($tmp_sql) && filesize($tmp_sql) > 0) {
                $dumped = true;
            }
        }

        // Fallback: PHP-based dumper, table-by-table.
        if (!$dumped) {
            self::php_db_dump($tmp_sql, $backup_id);
        }

        $wpdb->update($t, ['progress' => 70], ['id' => $backup_id]);

        // Wrap into ZIP for consistency.
        $zip = new \ZipArchive();
        if ($zip->open($zip_path, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Không thể mở file zip');
        }
        $zip->addFile($tmp_sql, 'database.sql');
        $zip->close();
        @unlink($tmp_sql);

        $wpdb->update($t, ['progress' => 95], ['id' => $backup_id]);
    }

    private static function is_shell_exec_safe() {
        $disabled = explode(',', (string) ini_get('disable_functions'));
        $disabled = array_map('trim', $disabled);
        return !in_array('shell_exec', $disabled, true);
    }

    private static function php_db_dump($out_path, $backup_id) {
        global $wpdb;
        $tracker = $wpdb->prefix . self::TABLE_BACKUPS;
        $tables = $wpdb->get_col("SHOW TABLES");
        $fp = fopen($out_path, 'w');
        if (!$fp) throw new \RuntimeException('Không mở được file SQL tạm');

        fwrite($fp, "-- Pi Backup PHP-dump ".date('c')."\n\n");
        fwrite($fp, "SET FOREIGN_KEY_CHECKS=0;\n\n");

        $count = count($tables); $i = 0;
        foreach ($tables as $tbl) {
            $i++;
            fwrite($fp, "DROP TABLE IF EXISTS `$tbl`;\n");
            $row = $wpdb->get_row("SHOW CREATE TABLE `$tbl`", ARRAY_N);
            if ($row && !empty($row[1])) {
                fwrite($fp, $row[1] . ";\n\n");
            }
            $rows = $wpdb->get_results("SELECT * FROM `$tbl`", ARRAY_N);
            foreach ($rows as $r) {
                $vals = array_map(function($v) use ($wpdb) {
                    if (is_null($v)) return 'NULL';
                    return "'" . esc_sql($v) . "'";
                }, $r);
                fwrite($fp, "INSERT INTO `$tbl` VALUES (" . implode(',', $vals) . ");\n");
            }
            fwrite($fp, "\n");
            // Update progress 10..65
            $pct = 10 + (int) (55 * ($i / max(1, $count)));
            $wpdb->update($tracker, ['progress' => $pct], ['id' => $backup_id]);
        }
        fwrite($fp, "SET FOREIGN_KEY_CHECKS=1;\n");
        fclose($fp);
    }

    /* ─────────────────────────── files backup ─────────────────────────── */
    private static function do_files_backup($zip_path, $backup_id, $exclude_uploads) {
        global $wpdb;
        $t = $wpdb->prefix . self::TABLE_BACKUPS;
        $zip = new \ZipArchive();
        if ($zip->open($zip_path, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Không tạo được zip');
        }
        $base = WP_CONTENT_DIR;
        $up   = wp_upload_dir();
        $excludes = ['cache', 'pi-backups'];

        $iter = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($base, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );
        $count = 0;
        foreach ($iter as $file) {
            $rel = ltrim(str_replace('\\', '/', substr($file->getPathname(), strlen($base))), '/');
            if (!$rel) continue;
            // Skip excluded paths
            $skip = false;
            foreach ($excludes as $ex) {
                if (strpos($rel, $ex . '/') === 0) { $skip = true; break; }
            }
            if (!$skip && $exclude_uploads && strpos($rel, 'uploads/') === 0) {
                $skip = true;
            }
            if ($skip) continue;
            if ($file->isFile()) {
                $zip->addFile($file->getPathname(), 'wp-content/' . $rel);
                $count++;
                if ($count % 200 === 0) {
                    $wpdb->update($t, ['progress' => min(90, 10 + (int)($count / 50))], ['id' => $backup_id]);
                }
            }
        }
        $zip->close();
        $wpdb->update($t, ['progress' => 95], ['id' => $backup_id]);
    }

    private static function do_full_backup($zip_path, $backup_id, $exclude_uploads) {
        $tmp_db = $zip_path . '.dbtmp.zip';
        self::do_db_backup($tmp_db, $backup_id);

        // Now create the full zip combining DB + files.
        global $wpdb;
        $t = $wpdb->prefix . self::TABLE_BACKUPS;
        $zip = new \ZipArchive();
        if ($zip->open($zip_path, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Không tạo được zip cuối');
        }

        // Add DB sql from the temporary db zip.
        $tmp = new \ZipArchive();
        if ($tmp->open($tmp_db) === true) {
            $sql = $tmp->getFromName('database.sql');
            if ($sql !== false) {
                $zip->addFromString('database.sql', $sql);
            }
            $tmp->close();
        }
        @unlink($tmp_db);

        $base = WP_CONTENT_DIR;
        $excludes = ['cache', 'pi-backups'];
        $iter = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($base, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );
        $count = 0;
        foreach ($iter as $file) {
            $rel = ltrim(str_replace('\\', '/', substr($file->getPathname(), strlen($base))), '/');
            if (!$rel) continue;
            $skip = false;
            foreach ($excludes as $ex) {
                if (strpos($rel, $ex . '/') === 0) { $skip = true; break; }
            }
            if (!$skip && $exclude_uploads && strpos($rel, 'uploads/') === 0) $skip = true;
            if ($skip) continue;
            if ($file->isFile()) {
                $zip->addFile($file->getPathname(), 'wp-content/' . $rel);
                $count++;
                if ($count % 200 === 0) {
                    $wpdb->update($t, ['progress' => min(95, 70 + (int)($count / 200))], ['id' => $backup_id]);
                }
            }
        }
        $zip->close();
    }

    /* ─────────────────────────── restore ─────────────────────────── */
    public static function restore_backup(\WP_REST_Request $req) {
        self::ensure_tables();
        $params = $req->get_json_params();
        $backup_id   = (int)($params['backup_id'] ?? 0);
        $restore_db  = !empty($params['restore_db']);
        $restore_fs  = !empty($params['restore_files']);
        $confirmed   = ($params['confirm'] ?? '') === 'KHÔI PHỤC';

        if (!$confirmed) {
            return new \WP_REST_Response([
                'error' => 'Cần xác nhận: gửi field "confirm" với giá trị "KHÔI PHỤC"',
            ], 400);
        }
        if (!$backup_id || (!$restore_db && !$restore_fs)) {
            return new \WP_REST_Response(['error' => 'Tham số không hợp lệ'], 400);
        }

        global $wpdb;
        $t = $wpdb->prefix . self::TABLE_BACKUPS;
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $t WHERE id = %d", $backup_id), ARRAY_A);
        if (!$row || !file_exists($row['file_path'])) {
            return new \WP_REST_Response(['error' => 'Không tìm thấy file backup'], 404);
        }

        // Open zip and stream-restore.
        $zip = new \ZipArchive();
        if ($zip->open($row['file_path']) !== true) {
            return new \WP_REST_Response(['error' => 'Không mở được file backup'], 500);
        }

        $log = [];
        if ($restore_db) {
            $sql = $zip->getFromName('database.sql');
            if ($sql !== false) {
                self::execute_sql_dump($sql);
                $log[] = 'Đã phục hồi cơ sở dữ liệu';
            } else {
                $log[] = 'Không tìm thấy database.sql trong file';
            }
        }
        if ($restore_fs) {
            $extracted = 0;
            $real_wp_content = realpath(WP_CONTENT_DIR);
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $stat = $zip->statIndex($i);
                if (!$stat) continue;
                $name = $stat['name'];
                if (strpos($name, 'wp-content/') !== 0) continue;
                if (strpos($name, '..') !== false || strpos($name, '\\') !== false) continue;
                $dest = ABSPATH . $name;
                $dir = dirname($dest);
                if (!file_exists($dir)) wp_mkdir_p($dir);
                $real_dest_dir = realpath($dir);
                if ($real_dest_dir === false || ($real_dest_dir !== $real_wp_content && strpos($real_dest_dir, $real_wp_content . DIRECTORY_SEPARATOR) !== 0)) {
                    continue;
                }
                $stream = $zip->getFromIndex($i);
                if ($stream !== false) {
                    file_put_contents($dest, $stream);
                    $extracted++;
                }
            }
            $log[] = "Đã phục hồi $extracted file";
        }
        $zip->close();

        return new \WP_REST_Response([
            'success' => true,
            'log'     => $log,
        ], 200);
    }

    private static function execute_sql_dump($sql) {
        global $wpdb;
        // Split on lines that end with `;` at end of statement.
        $stmts = preg_split('/;[\r\n]+/', $sql);
        foreach ($stmts as $s) {
            $s = trim($s);
            if ($s === '' || strpos($s, '--') === 0) continue;
            $wpdb->query($s);
        }
    }

    /* ─────────────────────────── schedules ─────────────────────────── */
    public static function list_schedules() {
        self::ensure_tables();
        global $wpdb;
        $t = $wpdb->prefix . self::TABLE_SCHEDULES;
        $rows = $wpdb->get_results("SELECT * FROM $t ORDER BY id DESC", ARRAY_A) ?: [];
        $out = [];
        foreach ($rows as $r) {
            $r['target_config'] = $r['target_config'] ? json_decode($r['target_config'], true) : null;
            $r['paused'] = (int)$r['paused'] === 1;
            $r['retention_count'] = (int)$r['retention_count'];
            $r['id'] = (int)$r['id'];
            $out[] = $r;
        }
        return new \WP_REST_Response(['schedules' => $out], 200);
    }

    public static function create_schedule(\WP_REST_Request $req) {
        self::ensure_tables();
        $p = $req->get_json_params();
        $frequency = in_array($p['frequency'] ?? 'daily', ['daily','weekly','monthly'], true)
            ? $p['frequency'] : 'daily';
        $type = in_array($p['type'] ?? 'full', ['db','files','full'], true) ? $p['type'] : 'full';
        $retention = max(1, min(60, (int)($p['retention_count'] ?? 7)));
        $target = in_array($p['target'] ?? 'local', ['local','s3','drive'], true) ? $p['target'] : 'local';
        $cfg = $p['target_config'] ?? null;

        global $wpdb;
        $t = $wpdb->prefix . self::TABLE_SCHEDULES;
        $now = current_time('mysql');
        $next = self::next_run_for($frequency);

        $wpdb->insert($t, [
            'frequency'        => $frequency,
            'type'             => $type,
            'retention_count'  => $retention,
            'target'           => $target,
            'target_config'    => $cfg ? wp_json_encode($cfg) : null,
            'paused'           => 0,
            'last_run'         => null,
            'next_run'         => $next,
            'created_at'       => $now,
        ]);

        if ($target !== 'local') {
            error_log("[pi-backup] would upload future backups to: $target");
        }

        return new \WP_REST_Response(['id' => (int)$wpdb->insert_id], 201);
    }

    public static function delete_schedule(\WP_REST_Request $req) {
        self::ensure_tables();
        global $wpdb;
        $id = (int)$req['id'];
        $t = $wpdb->prefix . self::TABLE_SCHEDULES;
        $wpdb->delete($t, ['id' => $id]);
        return new \WP_REST_Response(['success' => true], 200);
    }

    private static function next_run_for($frequency) {
        $secs = $frequency === 'weekly' ? 7 * 86400 :
                ($frequency === 'monthly' ? 30 * 86400 : 86400);
        return date('Y-m-d H:i:s', time() + $secs);
    }
}
