<?php
namespace PiApi;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * admin-ajax handler for refreshing the iframe's JWT from inside the parent
 * WordPress page. The embedded webapp posts `pi-api/refresh-jwt` to its
 * parent window; `iframe-bridge.js` translates that into a fetch on this
 * action, then forwards the new JWT back via postMessage.
 *
 * Why through the parent and not directly from the webapp?
 *   The webapp lives at a different origin (PI_API_BACKEND_URL) and does not
 *   hold the WordPress license key. Round-tripping the refresh through the
 *   parent keeps the key server-side and reuses Settings::ensureValidJwt(),
 *   which is the same code path the initial page render uses.
 */
class JwtAjax {
    public const ACTION = 'pi_api_refresh_jwt';
    public const NONCE = 'pi_api_iframe';

    public static function init(): void {
        // Logged-in users only. Capability + nonce checked in handle().
        add_action('wp_ajax_' . self::ACTION, [self::class, 'handle']);
    }

    public static function handle(): void {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['error' => 'forbidden'], 403);
        }

        // wp_verify_nonce / check_ajax_referer — accept token via _wpnonce
        // query arg (bridge appends it that way to keep the request a GET)
        check_ajax_referer(self::NONCE, '_wpnonce');

        $result = Settings::ensureValidJwt();
        if (!empty($result['error'])) {
            wp_send_json_error(
                ['error' => (string) $result['error']],
                $result['error'] === 'no_license' ? 403 : 401
            );
        }

        wp_send_json_success([
            'jwt'        => (string) $result['jwt'],
            'expires_in' => (int) $result['expires_in'],
        ]);
    }
}
