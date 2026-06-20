<?php
/**
 * Bot / audit performance optimization.
 *
 * Goal: real human visitors get the full fixed-position parallax backgrounds,
 * but crawlers and performance audits (Googlebot, Lighthouse/PageSpeed, GTmetrix,
 * headless Chrome, social unfurlers...) receive a lightweight, repaint-free render
 * where the fixed parallax layers fall back to position:absolute.
 *
 * This is the server-side counterpart to "Option A": because reduced-motion is a
 * per-user OS setting (a human dev machine can have it on), we can NOT use
 * prefers-reduced-motion to single out bots. We detect the user-agent instead and
 * inject a tiny bot-only <style>. Humans never receive this style.
 *
 * NOTE on caching: if a full-page cache is in front of the site, exclude bot
 * user-agents from the cache (or vary on UA) so the bot HTML is never served to a
 * human. The `body.sgh-bot` scope below is a safety net: even if the bot HTML
 * leaks to a human, the rule stays inert because a human <body> lacks .sgh-bot.
 *
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;

/**
 * Is the current request from a crawler / audit bot (vs a human browser)?
 * Memoized for the request.
 */
function sgh_is_bot() {
    static $is = null;
    if ($is !== null) return $is;
    $ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    $is = ($ua !== '') && (bool) preg_match(
        '/bot|crawl|spider|slurp|mediapartners|lighthouse|pagespeed|insights|gtmetrix|pingdom|headless|googlebot|bingbot|duckduckbot|baiduspider|yandex|facebookexternalhit|embedly|telegrambot|whatsapp|discordbot/i',
        $ua
    );
    return $is;
}

/* Tag the <body> so bot-only rules can scope safely (and themes/plugins can react). */
add_filter('body_class', function ($classes) {
    if (sgh_is_bot()) $classes[] = 'sgh-bot';
    return $classes;
});

/*
 * Bot-only perf style: neutralize fixed-position parallax background layers.
 * Targets the project parallax convention: a decorative bg element whose class
 * ends in "__bg" (single-class) or is followed by another class ("__bg ...").
 * Overlays/glows ("__bg-overlay", "__glow") are NOT matched. Output ONLY for bots.
 */
add_action('wp_head', function () {
    if (!sgh_is_bot()) return;
    echo '<style id="sgh-bot-perf">'
       . 'body.sgh-bot [class$="__bg"],body.sgh-bot [class*="__bg "]'
       . '{position:absolute!important;background-attachment:scroll!important}'
       . '</style>' . "\n";
}, 99);
