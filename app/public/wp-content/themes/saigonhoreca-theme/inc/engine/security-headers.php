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

    // ── CSP nonce ──
    // Generate a unique nonce per request. Stored in a global so header.php
    // can read it and attach to inline <script> tags.
    $nonce = base64_encode(random_bytes(16));
    $GLOBALS['sgh_csp_nonce'] = $nonce;

    // Content Security Policy — nonce replaces unsafe-inline for scripts.
    // Inline scripts that MUST stay inline (dark-mode flash-prevention) get
    // the nonce attribute. All other scripts are external (enqueued).
    $csp = [
        "default-src 'self'",
        "script-src 'self' 'unsafe-eval' https: blob: 'nonce-{$nonce}'",
        "script-src-elem 'self' https: blob: 'nonce-{$nonce}'",
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
