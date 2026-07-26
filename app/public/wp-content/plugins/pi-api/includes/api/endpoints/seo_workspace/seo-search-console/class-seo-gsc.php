<?php
/**
 * Pi API SEO Google Search Console Endpoints
 *
 * Bring-your-own-credentials OAuth 2.0 flow for Google Search Console.
 * Admin creates a Google Cloud project, enables:
 *   - Google Search Console API
 *   - Web Search Indexing API (optional, for URL submission)
 * And registers an OAuth 2.0 Web client with the React app URL as a valid
 * redirect URI (e.g. http://localhost:5173/seo-search-console for dev,
 * https://app.example.com/seo-search-console for prod).
 *
 * NOTE on localhost: Google requires the redirect URI to match EXACTLY.
 * For http://localhost:<port>/seo-search-console the URI must be added as
 * an "Authorized redirect URI" in the OAuth consent screen configuration.
 *
 * Options (plain — encrypted-at-rest is overkill for single-site admin
 * plugin; if needed later, migrate to `OAuthClient::obfuscate()` pattern):
 *   pi_seo_gsc_client_id
 *   pi_seo_gsc_client_secret
 *   pi_seo_gsc_refresh_token
 *   pi_seo_gsc_access_token
 *   pi_seo_gsc_access_token_expires   (unix timestamp)
 *   pi_seo_gsc_property               (selected site URL)
 *
 * Routes:
 *   GET  /pi/v1/seo/gsc/status
 *   GET  /pi/v1/seo/gsc/oauth-url?redirect_uri=...
 *   POST /pi/v1/seo/gsc/oauth-callback   { code, redirect_uri }
 *   POST /pi/v1/seo/gsc/disconnect
 *   POST /pi/v1/seo/gsc/credentials      { client_id, client_secret }
 *   GET  /pi/v1/seo/gsc/properties
 *   POST /pi/v1/seo/gsc/select-property  { property }
 *   GET  /pi/v1/seo/gsc/stats
 *   GET  /pi/v1/seo/gsc/coverage
 *   POST /pi/v1/seo/gsc/submit-url       { url }
 *
 * @package Pi_Api
 */

if (!defined('ABSPATH')) exit;

class Pi_Api_SeoGsc {

    const OPT_CLIENT_ID       = 'pi_seo_gsc_client_id';
    const OPT_CLIENT_SECRET   = 'pi_seo_gsc_client_secret';
    const OPT_REFRESH_TOKEN   = 'pi_seo_gsc_refresh_token';
    const OPT_ACCESS_TOKEN    = 'pi_seo_gsc_access_token';
    const OPT_ACCESS_EXPIRES  = 'pi_seo_gsc_access_token_expires';
    const OPT_PROPERTY        = 'pi_seo_gsc_property';

    const SCOPES = [
        'https://www.googleapis.com/auth/webmasters.readonly',
        'https://www.googleapis.com/auth/webmasters',
    ];

    const AUTH_URL     = 'https://accounts.google.com/o/oauth2/v2/auth';
    const TOKEN_URL    = 'https://oauth2.googleapis.com/token';
    const BASE_WEBMASTERS = 'https://searchconsole.googleapis.com/webmasters/v3';
    const BASE_INSPECT    = 'https://searchconsole.googleapis.com/v1';

    /* ═══════════════════════════════════════════════════════════════════════
       ROUTE REGISTRATION
       ═══════════════════════════════════════════════════════════════════════ */

    public static function register_routes($ns, $admin) {
        register_rest_route($ns, '/seo/gsc/status', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'status'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/seo/gsc/oauth-url', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'oauth_url'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/seo/gsc/oauth-callback', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'oauth_callback'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/seo/gsc/disconnect', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'disconnect'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/seo/gsc/credentials', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'save_credentials'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/seo/gsc/properties', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'properties'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/seo/gsc/select-property', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'select_property'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/seo/gsc/stats', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'stats'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/seo/gsc/coverage', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'coverage'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/seo/gsc/submit-url', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'submit_url'],
            'permission_callback' => $admin,
        ]);
    }

    /* ═══════════════════════════════════════════════════════════════════════
       STATUS
       ═══════════════════════════════════════════════════════════════════════ */

    public static function status() {
        $client_id = (string) get_option(self::OPT_CLIENT_ID, '');
        $refresh   = (string) get_option(self::OPT_REFRESH_TOKEN, '');
        $property  = (string) get_option(self::OPT_PROPERTY, '');

        return new \WP_REST_Response([
            'connected'      => $refresh !== '' && $client_id !== '',
            'property'       => $property !== '' ? $property : null,
            'client_id_set'  => $client_id !== '',
            'client_secret_set' => get_option(self::OPT_CLIENT_SECRET, '') !== '',
            'scopes'         => self::SCOPES,
        ], 200);
    }

    /* ═══════════════════════════════════════════════════════════════════════
       CREDENTIALS
       ═══════════════════════════════════════════════════════════════════════ */

    public static function save_credentials(\WP_REST_Request $request) {
        $body = $request->get_json_params();
        if (!is_array($body)) {
            return new \WP_REST_Response(['success' => false, 'message' => 'Body phải là JSON'], 400);
        }

        $client_id     = isset($body['client_id'])     ? trim((string) $body['client_id'])     : '';
        $client_secret = isset($body['client_secret']) ? trim((string) $body['client_secret']) : '';

        if ($client_id === '' || $client_secret === '') {
            return new \WP_REST_Response([
                'success' => false,
                'message' => 'Thiếu Client ID hoặc Client Secret.',
            ], 400);
        }

        update_option(self::OPT_CLIENT_ID, sanitize_text_field($client_id), false);
        update_option(self::OPT_CLIENT_SECRET, sanitize_text_field($client_secret), false);

        return new \WP_REST_Response([
            'success' => true,
            'message' => 'Đã lưu thông tin OAuth.',
        ], 200);
    }

    /* ═══════════════════════════════════════════════════════════════════════
       OAUTH URL
       ═══════════════════════════════════════════════════════════════════════ */

    public static function oauth_url(\WP_REST_Request $request) {
        $client_id = (string) get_option(self::OPT_CLIENT_ID, '');
        if ($client_id === '') {
            return new \WP_REST_Response([
                'success' => false,
                'message' => 'Chưa cấu hình Client ID. Vui lòng lưu credentials trước.',
            ], 400);
        }

        $redirect_uri = (string) $request->get_param('redirect_uri');
        if ($redirect_uri === '') {
            return new \WP_REST_Response([
                'success' => false,
                'message' => 'Thiếu redirect_uri.',
            ], 400);
        }

        // CSRF state token (10 min transient)
        $state = wp_generate_password(24, false, false);
        set_transient('pi_seo_gsc_oauth_state_' . $state, 1, 600);

        $url = add_query_arg([
            'client_id'     => $client_id,
            'redirect_uri'  => $redirect_uri,
            'response_type' => 'code',
            'scope'         => implode(' ', self::SCOPES),
            'access_type'   => 'offline',
            'prompt'        => 'consent',
            'state'         => $state,
        ], self::AUTH_URL);

        return new \WP_REST_Response([
            'success' => true,
            'url'     => $url,
            'state'   => $state,
        ], 200);
    }

    /* ═══════════════════════════════════════════════════════════════════════
       OAUTH CALLBACK
       ═══════════════════════════════════════════════════════════════════════ */

    public static function oauth_callback(\WP_REST_Request $request) {
        $body = $request->get_json_params();
        if (!is_array($body)) {
            return new \WP_REST_Response(['success' => false, 'message' => 'Body phải là JSON'], 400);
        }

        $code         = isset($body['code'])         ? (string) $body['code']         : '';
        $redirect_uri = isset($body['redirect_uri']) ? (string) $body['redirect_uri'] : '';
        $state        = isset($body['state'])        ? (string) $body['state']        : '';

        if ($code === '' || $redirect_uri === '') {
            return new \WP_REST_Response([
                'success' => false,
                'message' => 'Thiếu code hoặc redirect_uri.',
            ], 400);
        }

        // Validate state if present (not strictly required because the SPA
        // controls both ends of the flow, but adds CSRF safety)
        if ($state !== '') {
            $valid = (bool) get_transient('pi_seo_gsc_oauth_state_' . $state);
            if (!$valid) {
                return new \WP_REST_Response([
                    'success' => false,
                    'message' => 'State token không khớp (CSRF).',
                ], 400);
            }
            delete_transient('pi_seo_gsc_oauth_state_' . $state);
        }

        $client_id     = (string) get_option(self::OPT_CLIENT_ID, '');
        $client_secret = (string) get_option(self::OPT_CLIENT_SECRET, '');
        if ($client_id === '' || $client_secret === '') {
            return new \WP_REST_Response([
                'success' => false,
                'message' => 'Chưa cấu hình Client ID / Secret.',
            ], 400);
        }

        $resp = wp_remote_post(self::TOKEN_URL, [
            'timeout' => 20,
            'body'    => [
                'code'          => $code,
                'client_id'     => $client_id,
                'client_secret' => $client_secret,
                'redirect_uri'  => $redirect_uri,
                'grant_type'    => 'authorization_code',
            ],
        ]);

        if (is_wp_error($resp)) {
            return new \WP_REST_Response([
                'success' => false,
                'message' => 'Lỗi kết nối Google: ' . $resp->get_error_message(),
            ], 500);
        }

        $data = json_decode((string) wp_remote_retrieve_body($resp), true);
        if (!is_array($data) || empty($data['access_token'])) {
            $err = is_array($data)
                ? ($data['error_description'] ?? $data['error'] ?? 'Phản hồi không hợp lệ')
                : 'JSON parse fail';
            return new \WP_REST_Response([
                'success' => false,
                'message' => 'OAuth lỗi: ' . $err,
            ], 400);
        }

        // Store tokens
        update_option(self::OPT_ACCESS_TOKEN, (string) $data['access_token'], false);
        update_option(self::OPT_ACCESS_EXPIRES, time() + (int) ($data['expires_in'] ?? 3600), false);
        if (!empty($data['refresh_token'])) {
            update_option(self::OPT_REFRESH_TOKEN, (string) $data['refresh_token'], false);
        }

        return new \WP_REST_Response([
            'success' => true,
            'message' => 'Đã kết nối Google Search Console.',
        ], 200);
    }

    /* ═══════════════════════════════════════════════════════════════════════
       DISCONNECT
       ═══════════════════════════════════════════════════════════════════════ */

    public static function disconnect() {
        delete_option(self::OPT_ACCESS_TOKEN);
        delete_option(self::OPT_ACCESS_EXPIRES);
        delete_option(self::OPT_REFRESH_TOKEN);
        delete_option(self::OPT_PROPERTY);
        return new \WP_REST_Response([
            'success' => true,
            'message' => 'Đã ngắt kết nối.',
        ], 200);
    }

    /* ═══════════════════════════════════════════════════════════════════════
       PROPERTIES
       ═══════════════════════════════════════════════════════════════════════ */

    public static function properties() {
        $resp = self::gsc_request(self::BASE_WEBMASTERS . '/sites', 'GET');
        if (empty($resp['success'])) {
            return new \WP_REST_Response($resp, 400);
        }

        $sites = [];
        foreach ((array) ($resp['data']['siteEntry'] ?? []) as $entry) {
            $sites[] = [
                'siteUrl'         => (string) ($entry['siteUrl'] ?? ''),
                'permissionLevel' => (string) ($entry['permissionLevel'] ?? ''),
            ];
        }

        return new \WP_REST_Response([
            'success'    => true,
            'properties' => $sites,
            'selected'   => (string) get_option(self::OPT_PROPERTY, ''),
        ], 200);
    }

    public static function select_property(\WP_REST_Request $request) {
        $body = $request->get_json_params();
        if (!is_array($body)) {
            return new \WP_REST_Response(['success' => false, 'message' => 'Body phải là JSON'], 400);
        }

        $property = isset($body['property']) ? trim((string) $body['property']) : '';
        if ($property === '') {
            return new \WP_REST_Response(['success' => false, 'message' => 'Thiếu property.'], 400);
        }

        update_option(self::OPT_PROPERTY, esc_url_raw($property), false);

        return new \WP_REST_Response([
            'success'  => true,
            'property' => $property,
            'message'  => 'Đã chọn property: ' . $property,
        ], 200);
    }

    /* ═══════════════════════════════════════════════════════════════════════
       STATS (Last 28 days search analytics)
       ═══════════════════════════════════════════════════════════════════════ */

    public static function stats(\WP_REST_Request $request) {
        $property = (string) get_option(self::OPT_PROPERTY, '');
        if ($property === '') {
            return new \WP_REST_Response([
                'success' => false,
                'message' => 'Chưa chọn property.',
            ], 400);
        }

        $days = max(7, min(90, (int) ($request->get_param('days') ?: 28)));

        $start_date = gmdate('Y-m-d', strtotime('-' . ($days + 3) . ' days'));
        $end_date   = gmdate('Y-m-d', strtotime('-3 days'));

        // 1. Totals (no dimensions)
        $totals_resp = self::gsc_request(
            self::BASE_WEBMASTERS . '/sites/' . rawurlencode($property) . '/searchAnalytics/query',
            'POST',
            [
                'startDate'  => $start_date,
                'endDate'    => $end_date,
                'dimensions' => [],
                'rowLimit'   => 1,
            ]
        );

        $totals = [
            'clicks'      => 0,
            'impressions' => 0,
            'ctr'         => 0,
            'position'    => 0,
        ];
        if (!empty($totals_resp['success'])) {
            $row = $totals_resp['data']['rows'][0] ?? null;
            if (is_array($row)) {
                $totals = [
                    'clicks'      => (int)   ($row['clicks']      ?? 0),
                    'impressions' => (int)   ($row['impressions'] ?? 0),
                    'ctr'         => (float) ($row['ctr']         ?? 0),
                    'position'    => (float) ($row['position']    ?? 0),
                ];
            }
        }

        // 2. Per-day series
        $by_date_resp = self::gsc_request(
            self::BASE_WEBMASTERS . '/sites/' . rawurlencode($property) . '/searchAnalytics/query',
            'POST',
            [
                'startDate'  => $start_date,
                'endDate'    => $end_date,
                'dimensions' => ['date'],
                'rowLimit'   => $days + 5,
            ]
        );
        $by_date = [];
        if (!empty($by_date_resp['success'])) {
            foreach ((array) ($by_date_resp['data']['rows'] ?? []) as $row) {
                $by_date[] = [
                    'date'        => (string) ($row['keys'][0] ?? ''),
                    'clicks'      => (int)   ($row['clicks']      ?? 0),
                    'impressions' => (int)   ($row['impressions'] ?? 0),
                    'ctr'         => (float) ($row['ctr']         ?? 0),
                    'position'    => (float) ($row['position']    ?? 0),
                ];
            }
        }

        // 3. Top queries
        $top_queries_resp = self::gsc_request(
            self::BASE_WEBMASTERS . '/sites/' . rawurlencode($property) . '/searchAnalytics/query',
            'POST',
            [
                'startDate'  => $start_date,
                'endDate'    => $end_date,
                'dimensions' => ['query'],
                'rowLimit'   => 25,
            ]
        );
        $top_queries = [];
        if (!empty($top_queries_resp['success'])) {
            foreach ((array) ($top_queries_resp['data']['rows'] ?? []) as $row) {
                $top_queries[] = [
                    'query'       => (string) ($row['keys'][0] ?? ''),
                    'clicks'      => (int)   ($row['clicks']      ?? 0),
                    'impressions' => (int)   ($row['impressions'] ?? 0),
                    'ctr'         => (float) ($row['ctr']         ?? 0),
                    'position'    => (float) ($row['position']    ?? 0),
                ];
            }
        }

        // 4. Top pages
        $top_pages_resp = self::gsc_request(
            self::BASE_WEBMASTERS . '/sites/' . rawurlencode($property) . '/searchAnalytics/query',
            'POST',
            [
                'startDate'  => $start_date,
                'endDate'    => $end_date,
                'dimensions' => ['page'],
                'rowLimit'   => 25,
            ]
        );
        $top_pages = [];
        if (!empty($top_pages_resp['success'])) {
            foreach ((array) ($top_pages_resp['data']['rows'] ?? []) as $row) {
                $top_pages[] = [
                    'page'        => (string) ($row['keys'][0] ?? ''),
                    'clicks'      => (int)   ($row['clicks']      ?? 0),
                    'impressions' => (int)   ($row['impressions'] ?? 0),
                    'ctr'         => (float) ($row['ctr']         ?? 0),
                    'position'    => (float) ($row['position']    ?? 0),
                ];
            }
        }

        return new \WP_REST_Response([
            'success'     => true,
            'totals'      => $totals,
            'by_date'     => $by_date,
            'top_queries' => $top_queries,
            'top_pages'   => $top_pages,
            'range'       => ['start' => $start_date, 'end' => $end_date, 'days' => $days],
        ], 200);
    }

    /* ═══════════════════════════════════════════════════════════════════════
       COVERAGE (submitted sitemaps + approximate status counts)
       ═══════════════════════════════════════════════════════════════════════ */

    public static function coverage() {
        $property = (string) get_option(self::OPT_PROPERTY, '');
        if ($property === '') {
            return new \WP_REST_Response([
                'success' => false,
                'message' => 'Chưa chọn property.',
            ], 400);
        }

        // GSC v3 API does not expose coverage categorically via public API —
        // the closest available data is sitemap submission status. We tally
        // warnings and errors from submitted sitemaps to approximate a
        // coverage breakdown.
        $resp = self::gsc_request(
            self::BASE_WEBMASTERS . '/sites/' . rawurlencode($property) . '/sitemaps',
            'GET'
        );

        if (empty($resp['success'])) {
            return new \WP_REST_Response($resp, 400);
        }

        $sitemaps = (array) ($resp['data']['sitemap'] ?? []);
        $valid = 0;
        $warning = 0;
        $error = 0;
        $excluded = 0;
        $issues = [];

        foreach ($sitemaps as $sm) {
            $warnings = (int) ($sm['warnings'] ?? 0);
            $errors   = (int) ($sm['errors']   ?? 0);
            $submitted_count = 0;
            $indexed_count   = 0;
            foreach ((array) ($sm['contents'] ?? []) as $c) {
                $submitted_count += (int) ($c['submitted'] ?? 0);
                $indexed_count   += (int) ($c['indexed']   ?? 0);
            }

            $valid    += $indexed_count;
            $excluded += max(0, $submitted_count - $indexed_count);
            $warning  += $warnings;
            $error    += $errors;

            if ($warnings > 0 || $errors > 0) {
                $issues[] = [
                    'path'      => (string) ($sm['path'] ?? ''),
                    'warnings'  => $warnings,
                    'errors'    => $errors,
                    'submitted' => $submitted_count,
                    'indexed'   => $indexed_count,
                    'last_submitted' => (string) ($sm['lastSubmitted'] ?? ''),
                    'is_pending'     => (bool)   ($sm['isPending']    ?? false),
                ];
            }
        }

        return new \WP_REST_Response([
            'success'  => true,
            'valid'    => $valid,
            'warning'  => $warning,
            'error'    => $error,
            'excluded' => $excluded,
            'issues'   => $issues,
            'sitemaps' => $sitemaps,
        ], 200);
    }

    /* ═══════════════════════════════════════════════════════════════════════
       SUBMIT URL (URL Inspection API)
       ═══════════════════════════════════════════════════════════════════════ */

    public static function submit_url(\WP_REST_Request $request) {
        $property = (string) get_option(self::OPT_PROPERTY, '');
        if ($property === '') {
            return new \WP_REST_Response([
                'success' => false,
                'message' => 'Chưa chọn property.',
            ], 400);
        }

        $body = $request->get_json_params();
        if (!is_array($body)) {
            return new \WP_REST_Response(['success' => false, 'message' => 'Body phải là JSON'], 400);
        }

        $url = isset($body['url']) ? trim((string) $body['url']) : '';
        if ($url === '' || !filter_var($url, FILTER_VALIDATE_URL)) {
            return new \WP_REST_Response(['success' => false, 'message' => 'URL không hợp lệ.'], 400);
        }

        // URL Inspection returns index status. (Instant-Indexing PUSH is a
        // different API and requires a service account — out of scope here.)
        $resp = self::gsc_request(
            self::BASE_INSPECT . '/urlInspection/index:inspect',
            'POST',
            [
                'inspectionUrl' => $url,
                'siteUrl'       => $property,
            ]
        );

        if (empty($resp['success'])) {
            return new \WP_REST_Response($resp, 400);
        }

        $inspection = $resp['data']['inspectionResult'] ?? [];
        $index_status = $inspection['indexStatusResult'] ?? [];

        return new \WP_REST_Response([
            'success'    => true,
            'message'    => 'Đã gửi yêu cầu kiểm tra URL.',
            'inspection' => [
                'verdict'        => (string) ($index_status['verdict']        ?? ''),
                'coverage_state' => (string) ($index_status['coverageState']  ?? ''),
                'robots_state'   => (string) ($index_status['robotsTxtState'] ?? ''),
                'indexing_state' => (string) ($index_status['indexingState']  ?? ''),
                'last_crawl'     => (string) ($index_status['lastCrawlTime']  ?? ''),
                'sitemap'        => (array)  ($index_status['sitemap']        ?? []),
                'referring_urls' => (array)  ($index_status['referringUrls']  ?? []),
            ],
        ], 200);
    }

    /* ═══════════════════════════════════════════════════════════════════════
       INTERNAL — TOKEN MANAGEMENT
       ═══════════════════════════════════════════════════════════════════════ */

    /**
     * Get a valid access token. Auto-refreshes if expired (or within 60s of
     * expiry). Returns empty string if no refresh token or refresh fails.
     */
    private static function get_access_token() {
        $access  = (string) get_option(self::OPT_ACCESS_TOKEN, '');
        $expires = (int)    get_option(self::OPT_ACCESS_EXPIRES, 0);
        $refresh = (string) get_option(self::OPT_REFRESH_TOKEN, '');

        if ($access !== '' && $expires > (time() + 60)) {
            return $access;
        }

        if ($refresh === '') return '';

        // Refresh token
        $client_id     = (string) get_option(self::OPT_CLIENT_ID, '');
        $client_secret = (string) get_option(self::OPT_CLIENT_SECRET, '');
        if ($client_id === '' || $client_secret === '') return '';

        $resp = wp_remote_post(self::TOKEN_URL, [
            'timeout' => 20,
            'body'    => [
                'client_id'     => $client_id,
                'client_secret' => $client_secret,
                'refresh_token' => $refresh,
                'grant_type'    => 'refresh_token',
            ],
        ]);

        if (is_wp_error($resp)) return '';

        $data = json_decode((string) wp_remote_retrieve_body($resp), true);
        if (!is_array($data) || empty($data['access_token'])) return '';

        update_option(self::OPT_ACCESS_TOKEN, (string) $data['access_token'], false);
        update_option(self::OPT_ACCESS_EXPIRES, time() + (int) ($data['expires_in'] ?? 3600), false);
        if (!empty($data['refresh_token'])) {
            update_option(self::OPT_REFRESH_TOKEN, (string) $data['refresh_token'], false);
        }

        return (string) $data['access_token'];
    }

    /**
     * Perform an authenticated request against the GSC / URL Inspection API.
     * On 401, attempts a single token refresh + retry.
     *
     * @return array {success: bool, data?: array, message?: string, code?: int}
     */
    private static function gsc_request($url, $method = 'GET', $body = null) {
        $token = self::get_access_token();
        if ($token === '') {
            return [
                'success' => false,
                'message' => 'Chưa kết nối Google hoặc refresh_token đã hết hạn. Vui lòng kết nối lại.',
            ];
        }

        $do_call = static function ($token) use ($url, $method, $body) {
            $args = [
                'method'  => $method,
                'timeout' => 30,
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept'        => 'application/json',
                ],
            ];
            if ($body !== null) {
                $args['headers']['Content-Type'] = 'application/json';
                $args['body'] = wp_json_encode($body);
            }
            return wp_remote_request($url, $args);
        };

        $resp = $do_call($token);
        if (is_wp_error($resp)) {
            return ['success' => false, 'message' => 'HTTP error: ' . $resp->get_error_message()];
        }

        $code = (int) wp_remote_retrieve_response_code($resp);

        // 401 — token expired mid-flight; force refresh and retry once
        if ($code === 401) {
            delete_option(self::OPT_ACCESS_TOKEN);
            delete_option(self::OPT_ACCESS_EXPIRES);
            $new_token = self::get_access_token();
            if ($new_token === '') {
                return [
                    'success' => false,
                    'message' => 'Unauthorized — không thể refresh token. Vui lòng kết nối lại.',
                ];
            }
            $resp = $do_call($new_token);
            if (is_wp_error($resp)) {
                return ['success' => false, 'message' => 'HTTP error: ' . $resp->get_error_message()];
            }
            $code = (int) wp_remote_retrieve_response_code($resp);
        }

        $raw  = (string) wp_remote_retrieve_body($resp);
        $data = json_decode($raw, true);

        if ($code >= 400) {
            $msg = is_array($data) && isset($data['error']['message'])
                ? $data['error']['message']
                : ('HTTP ' . $code);
            return ['success' => false, 'message' => 'GSC API error: ' . $msg, 'code' => $code];
        }

        return ['success' => true, 'data' => is_array($data) ? $data : [], 'code' => $code];
    }
}
