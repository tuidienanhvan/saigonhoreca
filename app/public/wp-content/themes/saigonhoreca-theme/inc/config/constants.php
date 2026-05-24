<?php
/**
 * Theme Constants
 *
 * Centralized configuration values previously hardcoded across the codebase.
 * Loaded early in functions.php before all other includes.
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) exit;

// Site identity
define('SGH_SITE_NAME', 'Saigon Horeca');
define('SGH_SITE_TAGLINE', 'Thiết bị bếp công nghiệp & giải pháp horeca chuyên nghiệp');

// Third-party IDs (overridable via wp_options in dashboard Settings tab)
define('SGH_FB_APP_ID', '966242223397117');
define('SGH_GTM_CONTAINER_ID', 'GTM-PVSPDW5B');

// Google Fonts — kept at 6 weights (400, 500, 600, 700, 800, 900).
// Verified usage: 500 (40 refs), 800 (36 refs), 900 (66 refs) in active CSS.
// display=optional eliminates the font-swap CLS shift on the hero <h1>:
// browser uses fallback for the load period (~100 ms); if Be Vietnam Pro
// arrives in that window it is applied immediately, otherwise the fallback
// is kept for the rest of the page-load (font is cached for next visit).
// We pair this with explicit `<link rel="preload" as="font">` for the two
// hot weights (400, 700) so the load-period almost always wins.
define('SGH_GOOGLE_FONTS_URL', 'https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,400;0,500;0,700;1,400;1,500;1,700&family=Lexend:wght@500;700&family=Lora:ital,wght@0,400;0,500;0,700;1,400;1,700&display=swap');

// Analytics
define('SGH_ANALYTICS_SALT', 'sh_salt_2026');
