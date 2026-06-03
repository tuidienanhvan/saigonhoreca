<?php
namespace PiApi;

if (!defined('ABSPATH')) {
    exit;
}

class Settings {
    public static function init(): void {
        if (defined('PI_API_DEV_MODE') && PI_API_DEV_MODE) {
            add_action('admin_menu', [self::class, 'registerMenu']);
        }
        add_action('admin_post_pi_api_action', [self::class, 'handleAction']);
    }

    public static function registerMenu(): void {
        add_menu_page(
            __('Pi API', 'pi-api'),
            __('Pi API', 'pi-api'),
            'manage_options',
            'pi-api-license',
            [self::class, 'renderLicensePage'],
            'dashicons-admin-network',
            54
        );
    }

    public static function renderLicensePage(): void {
        include PI_API_DIR . 'views/license-page.php';
    }

    public static function getLicenseKey(): string {
        return (string) get_option('pi_api_license_key', '');
    }

    public static function getLicenseEmail(): string {
        return (string) get_option('pi_api_license_email', '');
    }

    public static function getTier(): string {
        return self::normalizeTier((string) get_option('pi_api_tier', 'free'));
    }

    public static function getFeatures(): array {
        $features = get_option('pi_api_features', []);
        return is_array($features) ? array_values(array_map('sanitize_key', $features)) : [];
    }

    public static function getJwt(): string {
        return (string) get_option('pi_api_jwt', '');
    }

    /**
     * Return a usable JWT, refreshing from the backend if it's missing or
     * within 60s of expiry. Single source of truth used by IframeRenderer
     * (page render) and JwtAjax (postMessage refresh) — keeps both paths
     * in sync.
     *
     * @return array{jwt: string, expires_in: int, error: ?string}
     *   error codes: no_license | jwt_empty | jwt_issue_failed | <backend msg>
     */
    public static function ensureValidJwt(): array {
        $jwt = self::getJwt();
        $expires_at = (int) get_option('pi_api_jwt_expires_at', 0);
        $now = time();

        // Reuse cached JWT when it still has > 60s of life
        if ($jwt !== '' && $expires_at > $now + 60) {
            return ['jwt' => $jwt, 'expires_in' => $expires_at - $now, 'error' => null];
        }

        $key = self::getLicenseKey();
        if ($key === '') {
            return ['jwt' => '', 'expires_in' => 0, 'error' => 'no_license'];
        }

        $resp = BackendClient::issueJwt($key);
        if (empty($resp['success'])) {
            return [
                'jwt'        => '',
                'expires_in' => 0,
                'error'      => (string) ($resp['error'] ?? 'jwt_issue_failed'),
            ];
        }

        $data = (array) ($resp['data'] ?? []);
        $jwt = (string) ($data['jwt'] ?? '');
        $expires_in = max(60, (int) ($data['expires_in'] ?? 900));

        if ($jwt === '') {
            return ['jwt' => '', 'expires_in' => 0, 'error' => 'jwt_empty'];
        }

        update_option('pi_api_jwt', $jwt, false);
        update_option('pi_api_jwt_expires_at', $now + $expires_in, false);

        return ['jwt' => $jwt, 'expires_in' => $expires_in, 'error' => null];
    }

    public static function getTokenQuota(): int {
        $map = [
            'free'       => 50000,
            'pro'        => 1000000,
            'max'        => 3000000,
            'enterprise' => -1,
        ];
        return $map[self::getTier()] ?? 50000;
    }

    public static function isActive(): bool {
        return self::getLicenseKey() !== '' && self::getJwt() !== '';
    }

    public static function handleAction(): void {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('No permission', 'pi-api'));
        }

        $action = sanitize_key((string) ($_POST['pi_api_action'] ?? ''));
        if ($action === 'activate') {
            check_admin_referer('pi_api_activate');
            $key = sanitize_text_field((string) ($_POST['license_key'] ?? ''));
            $resp = BackendClient::activate($key, (string) wp_parse_url(home_url(), PHP_URL_HOST));

            if (!empty($resp['success'])) {
                self::storeActivation($key, (array) ($resp['data'] ?? []));
                wp_safe_redirect(admin_url('admin.php?page=pi-api-dashboard&pi_api_msg=activated'));
                exit;
            }

            wp_safe_redirect(admin_url('admin.php?page=pi-api-dashboard&pi_api_type=error&pi_api_msg=' . rawurlencode((string) ($resp['error'] ?? 'Activation failed'))));
            exit;
        }

        if ($action === 'deactivate') {
            check_admin_referer('pi_api_deactivate');
            $key = self::getLicenseKey();
            if ($key !== '') {
                BackendClient::deactivate($key);
            }
            self::clearActivation();
            wp_safe_redirect(admin_url('admin.php?page=pi-api-dashboard&pi_api_msg=deactivated'));
            exit;
        }

        wp_safe_redirect(admin_url('admin.php?page=pi-api-dashboard'));
        exit;
    }

    public static function storeActivation(string $key, array $data): void {
        $tier = self::normalizeTier((string) ($data['tier'] ?? 'free'));
        $features = isset($data['features']) && is_array($data['features']) ? $data['features'] : [];
        $jwt = (string) ($data['jwt'] ?? '');
        $expiresIn = max(60, (int) ($data['expires_in'] ?? 900));

        update_option('pi_api_license_key', $key, false);
        update_option('pi_api_license_email', (string) ($data['email'] ?? ''), false);
        update_option('pi_api_tier', $tier, false);
        update_option('pi_api_features', array_values(array_map('sanitize_key', $features)), false);
        update_option('pi_api_jwt', $jwt, false);
        update_option('pi_api_jwt_expires_at', time() + $expiresIn, false);
        update_option('pi_api_status', (string) ($data['status'] ?? 'active'), false);
        update_option('pi_api_activated_at', time(), false);
        update_option('pi_api_heartbeat_fails', 0, false);

        self::mirrorLegacyLicense($key, $tier);
    }

    public static function clearActivation(): void {
        foreach ([
            'pi_api_license_key',
            'pi_api_tier',
            'pi_api_features',
            'pi_api_jwt',
            'pi_api_jwt_expires_at',
            'pi_api_status',
        ] as $option) {
            delete_option($option);
        }
        update_option('pi_license_tier', 'free', false);
        update_option('pi_license_data', ['key' => '', 'tier' => 'free', 'activated_at' => null, 'expires_at' => null, 'sites_used' => 0], false);
    }

    public static function maskKey(string $key): string {
        if ($key === '') {
            return '';
        }
        if (strlen($key) <= 12) {
            return str_repeat('*', max(0, strlen($key) - 4)) . substr($key, -4);
        }
        return substr($key, 0, 7) . str_repeat('*', 10) . substr($key, -5);
    }

    public static function normalizeTier(string $tier): string {
        $tier = strtolower(trim($tier));
        return in_array($tier, ['free', 'pro', 'max', 'enterprise'], true) ? $tier : 'free';
    }

    private static function mirrorLegacyLicense(string $key, string $tier): void {
        update_option('pi_license_tier', $tier, false);
        update_option('pi_license_data', [
            'key'          => $key,
            'tier'         => $tier,
            'activated_at' => gmdate('c'),
            'expires_at'   => null,
            'sites_used'   => 1,
        ], false);
    }
}
