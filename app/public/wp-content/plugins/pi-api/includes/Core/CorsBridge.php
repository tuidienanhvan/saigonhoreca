<?php
namespace PiApi\Core;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * CorsBridge — allow the iframe origin (PI_API_DASHBOARD_URL) to call this
 * site's WP REST API with credentials so a user-supplied Application
 * Password works cross-origin.
 */
class CorsBridge {
    public static function register(): void {
        // Handle early pre-flight OPTIONS request before REST API routing
        add_action('init', function () {
            if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
                self::sendCorsHeaders(null);
            }
        }, 1);

        add_action('rest_api_init', function () {
            remove_filter('rest_pre_serve_request', 'rest_send_cors_headers');
            add_filter('rest_pre_serve_request', [self::class, 'sendCorsHeaders'], 0);
        }, 15);
    }

    public static function sendCorsHeaders($value) {
        $origin = isset($_SERVER['HTTP_ORIGIN']) ? esc_url_raw(wp_unslash($_SERVER['HTTP_ORIGIN'])) : '';
        if ($origin === '') {
            return $value;
        }

        $expected_origin = defined('PI_API_DASHBOARD_URL') ? self::originOf((string) PI_API_DASHBOARD_URL) : '';
        $allowed_origins = [
            $expected_origin,
            'http://localhost:8000',
            'http://127.0.0.1:8000',
            'http://localhost:3000',
            'http://127.0.0.1:3000',
        ];

        // Add range of potential Vite ports (5173-5185)
        for ($port = 5170; $port <= 5185; $port++) {
            $allowed_origins[] = "http://localhost:$port";
            $allowed_origins[] = "http://127.0.0.1:$port";
        }

        $allowed_origins = (array) apply_filters('pi_api_cors_allowed_origins', $allowed_origins, $origin);

        if (!in_array($origin, $allowed_origins, true)) {
            return $value;
        }

        header('Access-Control-Allow-Origin: ' . $origin);
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Authorization, Content-Type, X-WP-Nonce, X-Pi-Site, X-Pi-Site-Id, X-Impersonate-Tenant-ID, Content-Disposition, Content-MD5, X-Requested-With, Cache-Control, Pragma');
        header('Access-Control-Expose-Headers: X-WP-Total, X-WP-TotalPages, Link, Content-Disposition');
        header('Vary: Origin');

        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
            status_header(204);
            exit;
        }

        return $value;
    }

    private static function originOf(string $url): string {
        $parts = wp_parse_url($url);
        if (empty($parts['host'])) return '';
        $scheme = $parts['scheme'] ?? 'https';
        $port   = isset($parts['port']) ? ':' . (int) $parts['port'] : '';
        return $scheme . '://' . $parts['host'] . $port;
    }
}
