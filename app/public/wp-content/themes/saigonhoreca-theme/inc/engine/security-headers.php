<?php
/**
 * ── Best Practices: Security Headers ────────────────────────
 * Implements HSTS, CSP, X-Frame-Options, etc.
 * Target: 100/100 in Lighthouse Best Practices.
 *
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;

add_action('send_headers', function() {
    if (is_admin()) return;

    $is_https = is_ssl()
        || (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off')
        || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower((string) $_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https');

    // HSTS — only valid on HTTPS (browsers ignore on HTTP; sending it
    // anyway causes a console warning on local HTTP dev).
    if ($is_https) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
    }

    // X-Frame-Options — prevent clickjacking
    header('X-Frame-Options: SAMEORIGIN');

    // X-Content-Type-Options — prevent MIME sniffing
    header('X-Content-Type-Options: nosniff');

    // Referrer Policy
    header('Referrer-Policy: strict-origin-when-cross-origin');

    // Permissions Policy
    header('Permissions-Policy: camera=(), microphone=(), geolocation=(), interest-cohort=()');

    // Cross-Origin-Opener-Policy — browsers ignore on non-HTTPS and emit
    // a console warning ("untrustworthy origin"). Only send on HTTPS.
    if ($is_https) {
        header('Cross-Origin-Opener-Policy: same-origin');
    }

    // Content Security Policy — tuned for saigonhoreca's Elementor +
    // Astra + WooCommerce + GA4 stack served via the static mirror.
    // The saigonhouse parent theme had a tight saigonhouse-plugin CSP
    // that broke runtime here (9× font-src violations, GA collect blocks,
    // WooCommerce webpack chunks refused). Relaxed scopes:
    //   - font-src: + https: (FontAwesome, Astra subsets, third-party icon sets)
    //   - script-src: + https: (Astra + Elementor + Woo chunks under same host)
    //   - connect-src: + https://*.google.com https://*.analytics.google.com
    //     (GA4 routes traffic through analytics.google.com and www.google.com)
    //   - style-src: + https: (Astra inline + Elementor)
    //   - img-src: blob: https: (already permissive)
    // Future hardening (production): replace with nonced CSP per real plugin
    // set; static mirror is dev-only so trade some strictness for working UI.
    $csp = [
        "default-src 'self'",
        "script-src 'self' 'unsafe-inline' 'unsafe-eval' https: blob:",
        "script-src-elem 'self' 'unsafe-inline' https: blob:",
        "style-src 'self' 'unsafe-inline' https:",
        "font-src 'self' data: https:",
        "img-src 'self' data: blob: https:",
        "media-src 'self' https:",
        "connect-src 'self' https: wss:",
        "frame-src 'self' https:",
        "object-src 'none'",
        "base-uri 'self'",
        "form-action 'self' https:",
        "frame-ancestors 'self'",
    ];
    header('Content-Security-Policy: ' . implode('; ', $csp));
});
