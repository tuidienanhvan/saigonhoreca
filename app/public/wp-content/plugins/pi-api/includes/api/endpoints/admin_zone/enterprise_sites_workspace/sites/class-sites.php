<?php
/**
 * Pi API Sites — Multi-Site Switcher (Phase 5 task D, Max tier).
 *
 * Endpoints
 * ─────────────────────────────────────────────────────────────
 *   GET    /pi/v1/sites                    List managed sites
 *   POST   /pi/v1/sites/connect            Connect a remote Pi site
 *                                          Body: { site_url, license_key, name? }
 *   DELETE /pi/v1/sites/{id}               Disconnect (remove) a managed site
 *   POST   /pi/v1/sites/{id}/sync          Sync metadata (post/lead counts...)
 *   GET    /pi/v1/sites/{id}/health        Health snapshot
 *
 * Stub storage:
 *   Persists in WP option `pi_managed_sites` as a JSON array of:
 *     [{ id, name, url, license_key, last_synced, status, metadata }]
 *
 *   Real cross-site auth (federated REST → remote /pi/v1/system) is deferred —
 *   Phase 5 only needs add/list/remove/sync-stub.
 *
 *   Future schema (declared here for a CREATE TABLE migration step):
 *     wp_pi_managed_sites (
 *        id BIGINT AUTO_INCREMENT PK,
 *        site_url VARCHAR(255) NOT NULL,
 *        name VARCHAR(255),
 *        license_key_encrypted TEXT,
 *        status VARCHAR(20) DEFAULT 'pending',
 *        last_synced_at DATETIME NULL,
 *        metadata LONGTEXT,
 *        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
 *     )
 */

if (!defined('ABSPATH')) exit;

class Pi_Api_Sites {

    const OPTION_KEY = 'pi_managed_sites';

    /* ─────────────────────────── routes ─────────────────────────── */
    public static function register_routes($ns, $admin) {
        register_rest_route($ns, '/sites', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'list_sites'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/sites/connect', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'connect_site'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/sites/(?P<id>[a-zA-Z0-9_-]+)', [
            'methods'             => 'DELETE',
            'callback'            => [self::class, 'disconnect_site'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/sites/(?P<id>[a-zA-Z0-9_-]+)/sync', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'sync_site'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/sites/(?P<id>[a-zA-Z0-9_-]+)/health', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'site_health'],
            'permission_callback' => $admin,
        ]);
    }

    /* ─────────────────────────── helpers ─────────────────────────── */
    private static function load_sites() {
        $raw = get_option(self::OPTION_KEY, []);
        if (!is_array($raw)) $raw = [];
        // sanitize each entry
        $out = [];
        foreach ($raw as $row) {
            if (!is_array($row) || empty($row['id'])) continue;
            $out[] = [
                'id'           => (string) $row['id'],
                'name'         => (string) ($row['name'] ?? $row['url'] ?? ''),
                'url'          => (string) ($row['url'] ?? ''),
                'license_key'  => (string) ($row['license_key'] ?? ''),
                'last_synced'  => $row['last_synced'] ?? null,
                'status'       => (string) ($row['status'] ?? 'pending'),
                'metadata'     => is_array($row['metadata'] ?? null) ? $row['metadata'] : [],
                'created_at'   => $row['created_at'] ?? null,
            ];
        }
        return $out;
    }

    private static function save_sites($sites) {
        update_option(self::OPTION_KEY, array_values($sites), false);
    }

    private static function fingerprint($site_url, $license_key) {
        // Short non-secret fingerprint to help users verify connection legitimacy.
        $hash = substr(hash('sha256', strtolower(trim($site_url)) . '|' . $license_key), 0, 12);
        return strtoupper(substr($hash, 0, 4) . '-' . substr($hash, 4, 4) . '-' . substr($hash, 8, 4));
    }

    private static function tier_check() {
        // Multi-site is Max-tier only.
        $tier = strtolower((string) get_option('pi_license_tier', 'free'));
        if (!in_array($tier, ['max'], true)) {
            return new \WP_Error(
                'pi_sites_tier_locked',
                'Multi-site quản lý chỉ khả dụng trên gói Max.',
                ['status' => 403]
            );
        }
        return true;
    }

    /* ─────────────────────────── handlers ─────────────────────────── */
    public static function list_sites(\WP_REST_Request $req) {
        // Tier soft-check — return list anyway but flag locked, so UI can show banner.
        $tier_err = self::tier_check();
        $sites = self::load_sites();
        $payload = [
            'sites' => $sites,
            'tier'  => strtolower((string) get_option('pi_license_tier', 'free')),
            'locked' => is_wp_error($tier_err),
        ];
        return new \WP_REST_Response($payload, 200);
    }

    public static function connect_site(\WP_REST_Request $req) {
        $tier_err = self::tier_check();
        if (is_wp_error($tier_err)) return $tier_err;

        $body = $req->get_json_params();
        $url  = isset($body['site_url']) ? esc_url_raw(trim($body['site_url'])) : '';
        $key  = isset($body['license_key']) ? sanitize_text_field((string) $body['license_key']) : '';
        $name = isset($body['name']) ? sanitize_text_field((string) $body['name']) : '';

        if (empty($url)) {
            return new \WP_Error('pi_sites_invalid_url', 'URL site không hợp lệ.', ['status' => 400]);
        }
        if (empty($key)) {
            return new \WP_Error('pi_sites_invalid_key', 'License key bắt buộc.', ['status' => 400]);
        }

        $sites = self::load_sites();
        // Dedupe by URL
        foreach ($sites as $existing) {
            if (strtolower($existing['url']) === strtolower($url)) {
                return new \WP_Error('pi_sites_duplicate', 'Site này đã được kết nối.', ['status' => 409]);
            }
        }

        $id = 'site_' . wp_generate_uuid4();
        if (function_exists('wp_parse_url')) {
            $host = wp_parse_url($url, PHP_URL_HOST);
        } else {
            $host = parse_url($url, PHP_URL_HOST);
        }
        $entry = [
            'id'          => $id,
            'name'        => $name ?: ($host ?: $url),
            'url'         => $url,
            'license_key' => $key,
            'last_synced' => null,
            'status'      => 'connected',
            'metadata'    => [
                'fingerprint' => self::fingerprint($url, $key),
                'posts'       => null,
                'leads'       => null,
            ],
            'created_at'  => current_time('mysql'),
        ];

        $sites[] = $entry;
        self::save_sites($sites);
        return new \WP_REST_Response($entry, 201);
    }

    public static function disconnect_site(\WP_REST_Request $req) {
        $tier_err = self::tier_check();
        if (is_wp_error($tier_err)) return $tier_err;

        $id = (string) $req->get_param('id');
        $sites = self::load_sites();
        $kept = [];
        $removed = false;
        foreach ($sites as $row) {
            if ($row['id'] === $id) { $removed = true; continue; }
            $kept[] = $row;
        }
        if (!$removed) {
            return new \WP_Error('pi_sites_not_found', 'Không tìm thấy site.', ['status' => 404]);
        }
        self::save_sites($kept);
        return new \WP_REST_Response(['ok' => true, 'id' => $id], 200);
    }

    public static function sync_site(\WP_REST_Request $req) {
        $tier_err = self::tier_check();
        if (is_wp_error($tier_err)) return $tier_err;

        $id = (string) $req->get_param('id');
        $sites = self::load_sites();
        $found = null;
        foreach ($sites as $idx => $row) {
            if ($row['id'] === $id) { $found = $idx; break; }
        }
        if ($found === null) {
            return new \WP_Error('pi_sites_not_found', 'Không tìm thấy site.', ['status' => 404]);
        }

        // Stub: simulate sync — real impl would HTTP-fetch /pi/v1/overview from remote.
        $sites[$found]['last_synced'] = current_time('mysql');
        $sites[$found]['status']      = 'connected';
        $sites[$found]['metadata']    = array_merge(
            $sites[$found]['metadata'] ?? [],
            [
                'posts' => rand(20, 500),
                'leads' => rand(0, 120),
                'pages' => rand(5, 40),
                'media' => rand(50, 800),
                'last_activity' => current_time('mysql'),
            ]
        );
        self::save_sites($sites);
        return new \WP_REST_Response($sites[$found], 200);
    }

    public static function site_health(\WP_REST_Request $req) {
        $id = (string) $req->get_param('id');
        $sites = self::load_sites();
        $site = null;
        foreach ($sites as $row) {
            if ($row['id'] === $id) { $site = $row; break; }
        }
        if (!$site) {
            return new \WP_Error('pi_sites_not_found', 'Không tìm thấy site.', ['status' => 404]);
        }

        // Stub health snapshot — replace with real cross-site /pi/v1/system call later.
        $age_hours = $site['last_synced']
            ? max(0, floor((time() - strtotime($site['last_synced'])) / 3600))
            : 999;

        $level = $age_hours < 24 ? 'healthy' : ($age_hours < 168 ? 'stale' : 'error');
        return new \WP_REST_Response([
            'id'        => $id,
            'level'     => $level,
            'age_hours' => (int) $age_hours,
            'checks'    => [
                'reachable'  => true,
                'license_ok' => true,
                'plugins_ok' => true,
            ],
            'fingerprint' => $site['metadata']['fingerprint'] ?? null,
        ], 200);
    }
}
