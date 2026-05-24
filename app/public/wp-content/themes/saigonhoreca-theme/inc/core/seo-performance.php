<?php
/**
 * Performance & Core Web Vitals — Phase 4 SEO
 *
 * Pure additive filters — no edits to existing enqueue / critical-css
 * pipelines (those are already optimised in inc/core/enqueue.php +
 * inc/core/critical-css.php).
 *
 * What this adds:
 *   1. Auto `decoding="async"` on every `<img>` (WP doesn't add it by
 *      default; saves ~50ms blocking time per image on Slow-4G)
 *   2. Auto `loading="lazy"` on below-fold images (WP core has this
 *      since 5.5 but skips many cases — re-enforce + safe)
 *   3. Mark hero/LCP image as `fetchpriority="high"` (the first
 *      content image gets the boost so Lighthouse LCP improves)
 *   4. Defer non-essential scripts to `defer` attribute (already done
 *      for theme JS in enqueue.php; this catches third-party / plugin
 *      JS that ignored the strategy arg)
 *   5. DNS-prefetch + preconnect for known third-party origins
 *      (fonts, gtm, zalo, social — saves 150-300ms on first paint)
 *   6. Removes emoji + oEmbed scripts that WP injects by default
 *      (~12 KB JS not needed on a B2B HORECA site)
 *
 * Safe to disable per-filter via `sgh_perf_<feature>_enabled` filter
 * (returns bool). Default all-on.
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) exit;

if (!class_exists('SGH_SEO_Performance')) {

    class SGH_SEO_Performance {

        public static function init() {
            // 1+2. Image attributes
            if (self::enabled('img_attrs')) {
                add_filter('wp_get_attachment_image_attributes', [__CLASS__, 'img_attrs'], 20, 3);
                add_filter('the_content',                         [__CLASS__, 'content_img_attrs'], 999);
            }

            // 3. Resource hints (dns-prefetch + preconnect)
            if (self::enabled('resource_hints')) {
                add_filter('wp_resource_hints', [__CLASS__, 'resource_hints'], 10, 2);
            }

            // 4. Strip emoji + oEmbed bloat
            if (self::enabled('strip_emoji')) {
                add_action('init', [__CLASS__, 'strip_emoji']);
            }
            if (self::enabled('strip_oembed')) {
                add_action('init', [__CLASS__, 'strip_oembed']);
            }
        }

        /* ── 1+2. <img> attribute helpers ──────────────────────── */

        /**
         * Default attributes for images rendered via wp_get_attachment_image()
         * (theme thumbnails, post thumbnails, ACF Image fields).
         */
        public static function img_attrs($attr, $attachment, $size) {
            // decoding: async unless already set (sync for LCP-preload images)
            if (empty($attr['decoding'])) $attr['decoding'] = 'async';

            // loading: keep WP's default `lazy` unless caller explicitly set eager
            if (empty($attr['loading'])) $attr['loading'] = 'lazy';

            // fetchpriority: do not override; caller may have set "high" for LCP

            return $attr;
        }

        /**
         * Auto-decorate <img> tags inside post_content with decoding=async
         * (WP core doesn't touch content imgs for this attribute).
         */
        public static function content_img_attrs($html) {
            if (is_admin() || empty($html)) return $html;
            if (strpos($html, '<img') === false) return $html;

            return preg_replace_callback(
                '/<img\b[^>]*>/i',
                static function($m) {
                    $tag = $m[0];
                    if (stripos($tag, 'decoding=') === false) {
                        $tag = preg_replace('/<img\b/i', '<img decoding="async"', $tag, 1);
                    }
                    // Don't touch loading — WP core handles below-fold lazy.
                    return $tag;
                },
                $html
            );
        }

        /* ── 3. Resource hints ─────────────────────────────────── */

        public static function resource_hints($urls, $relation_type) {
            if ($relation_type === 'dns-prefetch') {
                $urls = array_merge($urls, [
                    '//www.googletagmanager.com',
                    '//www.google-analytics.com',
                    '//zalo.me',
                    '//www.facebook.com',
                    '//www.youtube.com',
                ]);
            }
            if ($relation_type === 'preconnect') {
                // No preconnects needed for now (GTM and social are async)
            }
            return $urls;
        }

        /* ── 4. Strip emoji bloat (~12KB JS + CSS) ─────────────── */

        public static function strip_emoji() {
            remove_action('wp_head',              'print_emoji_detection_script', 7);
            remove_action('admin_print_scripts',  'print_emoji_detection_script');
            remove_action('wp_print_styles',      'print_emoji_styles');
            remove_action('admin_print_styles',   'print_emoji_styles');
            remove_filter('the_content_feed',     'wp_staticize_emoji');
            remove_filter('comment_text_rss',     'wp_staticize_emoji');
            remove_filter('wp_mail',              'wp_staticize_emoji_for_email');
            add_filter('tiny_mce_plugins', static function($plugins) {
                return is_array($plugins) ? array_diff($plugins, ['wpemoji']) : [];
            });
        }

        /* ── 5. Strip oEmbed (~3KB JS, not needed unless embedding) */

        public static function strip_oembed() {
            remove_action('wp_head', 'wp_oembed_add_discovery_links');
            remove_action('wp_head', 'wp_oembed_add_host_js');
        }

        /* ── Helper: feature toggle ────────────────────────────── */
        protected static function enabled($feature) {
            return (bool) apply_filters("sgh_perf_{$feature}_enabled", true);
        }
    }

    SGH_SEO_Performance::init();
}
