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

// Google Fonts — Be Vietnam Pro (400/500/700 + italics), Lexend (500/700),
// Lora (400/500/700 + italics), `display=swap`.
// Loaded async via style_loader_tag filter (preload-as-style + onload swap)
// with preconnect hints in header.php. Deliberately NO <link rel="preload"
// as="font">: font preloads were measured to steal bandwidth from the hero
// LCP image and were reverted — see docs/PERF-DECISIONS.md §3.
define('SGH_GOOGLE_FONTS_URL', 'https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,400;0,500;0,700;1,400;1,500;1,700&family=Lexend:wght@500;700&family=Lora:ital,wght@0,400;0,500;0,700;1,400;1,700&display=swap');

// Analytics
define('SGH_ANALYTICS_SALT', 'sh_salt_2026');
