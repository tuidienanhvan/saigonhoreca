<?php
namespace PiApi;

if (!defined('ABSPATH')) {
    exit;
}

class Heartbeat {
    public static function init(): void {
        add_action('pi_api_heartbeat', [self::class, 'run']);
        if (!wp_next_scheduled('pi_api_heartbeat')) {
            wp_schedule_event(time() + HOUR_IN_SECONDS, 'daily', 'pi_api_heartbeat');
        }
    }

    public static function run(): void {
        $key = Settings::getLicenseKey();
        if ($key === '') {
            return;
        }

        $resp = BackendClient::heartbeat($key);
        if (!empty($resp['success'])) {
            $data = (array) ($resp['data'] ?? []);
            $tier = Settings::normalizeTier((string) ($data['tier'] ?? Settings::getTier()));
            $features = isset($data['features']) && is_array($data['features']) ? $data['features'] : Settings::getFeatures();
            update_option('pi_api_tier', $tier, false);
            update_option('pi_api_features', array_values(array_map('sanitize_key', $features)), false);
            update_option('pi_api_status', (string) ($data['status'] ?? 'active'), false);
            update_option('pi_api_last_heartbeat', time(), false);
            update_option('pi_api_heartbeat_fails', 0, false);
            update_option('pi_license_tier', $tier, false);
            return;
        }

        $fails = (int) get_option('pi_api_heartbeat_fails', 0) + 1;
        update_option('pi_api_heartbeat_fails', $fails, false);
        if ($fails > 7) {
            update_option('pi_api_status', 'unreachable', false);
        }
    }
}
