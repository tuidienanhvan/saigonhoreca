<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Revoke any 'Pi_Dashboard' Application Password AuthManager::createAndSyncAppPassword()
// minted (see includes/AuthManager.php) — otherwise it stays valid, granting
// full REST API access as that admin, indefinitely after the plugin is removed.
if (class_exists('WP_Application_Passwords')) {
    foreach (get_users(['role' => 'administrator']) as $admin) {
        $passwords = \WP_Application_Passwords::get_user_application_passwords($admin->ID);
        foreach ($passwords as $p) {
            if (($p['name'] ?? '') === 'Pi_Dashboard' && !empty($p['uuid'])) {
                \WP_Application_Passwords::delete_application_password($admin->ID, $p['uuid']);
            }
        }
    }
}

$key = (string) get_option('pi_api_license_key', '');
if ($key !== '' && defined('PI_API_BACKEND_URL')) {
    wp_remote_post(trailingslashit(PI_API_BACKEND_URL) . 'auth/deactivate', [
        'timeout' => 5,
        'headers' => ['Content-Type' => 'application/json'],
        'body' => wp_json_encode(['license_key' => $key]),
    ]);
}

$options = [
    'pi_api_license_key',
    'pi_api_tenant_id',
    'pi_api_tier',
    'pi_api_features',
    'pi_api_jwt',
    'pi_api_jwt_expires_at',
    'pi_api_status',
    'pi_api_activated_at',
    'pi_api_last_heartbeat',
    'pi_api_last_error',
    'pi_api_heartbeat_fails',
    'pi_license_tier',
    'pi_license_data',
    'pi_ai_cloud_tokens_used_month',
    'pi_api_app_pass_synced_at',
];

foreach ($options as $option) {
    delete_option($option);
}

wp_clear_scheduled_hook('pi_api_heartbeat');
delete_transient('pi_api_hb_fails');
