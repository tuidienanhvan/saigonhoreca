<?php
/**
 * Robots.txt + Crawl Budget Optimisation — Phase 3 SEO
 *
 * Hooks the dynamic /robots.txt that WP serves (`do_robots()`) to:
 *   - Disallow URL-noise that wastes crawl budget:
 *       ?orderby=*, ?filter_*, ?utm_*, ?fbclid=*, /cart/, /checkout/,
 *       /my-account/, /wp-admin/ (already), search results, feed/comments
 *   - Allow critical assets (CSS/JS/images) for Mobile-Friendly test
 *   - Surface the active sitemap URL in the response
 *
 * Also: noindex for paginated archive pages (`/page/2/+`) to avoid
 * crawl-budget waste on thin paginated lists while keeping page 1
 * indexable.
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) exit;

if (!class_exists('SGH_SEO_Robots_Txt')) {

    class SGH_SEO_Robots_Txt {

        public static function init() {
            add_filter('robots_txt',           [__CLASS__, 'filter_robots_txt'], 20, 2);
            add_filter('wp_robots',            [__CLASS__, 'filter_pagination_robots'], 50);
            add_filter('rank_math/frontend/robots', [__CLASS__, 'filter_pagination_rank_math'], 50);
        }

        /**
         * Build a hardened robots.txt — replaces WP's tiny default
         * (Disallow: /wp-admin/, Allow: /wp-admin/admin-ajax.php).
         */
        public static function filter_robots_txt($output, $public) {
            if (!$public) return $output;
            if (function_exists('sgh_pi_has_custom_robots_txt') && sgh_pi_has_custom_robots_txt()) {
                return $output;
            }

            $sitemap_lines = array_map(
                static fn($url) => 'Sitemap: ' . $url,
                self::sitemap_urls()
            );

            $rules = [
                'User-agent: *',
                '',
                '# Core WP admin',
                'Disallow: /wp-admin/',
                'Allow:    /wp-admin/admin-ajax.php',
                '',
                '# Cart / account (no-Woo today, future-safe)',
                'Disallow: /cart/',
                'Disallow: /checkout/',
                'Disallow: /my-account/',
                '',
                '# Search results',
                'Disallow: /?s=',
                'Disallow: /search/',
                '',
                '# URL noise that creates infinite duplicate variants',
                'Disallow: /*?orderby=',
                'Disallow: /*?filter_',
                'Disallow: /*?utm_',
                'Disallow: /*?fbclid=',
                'Disallow: /*?gclid=',
                'Disallow: /*?msclkid=',
                'Disallow: /*?_=',
                'Disallow: /*?cb=',
                '',
                '# Feed / comments — low SEO value, high crawl cost',
                'Disallow: /feed/',
                'Disallow: /comments/feed/',
                'Disallow: /trackback/',
                'Disallow: /*/feed/',
                'Disallow: /*/trackback/',
                'Disallow: /*?replytocom=',
                '',
                '# Allow CSS / JS / image so Mobile-Friendly test can render',
                'Allow: /wp-content/themes/*.css',
                'Allow: /wp-content/themes/*.js',
                'Allow: /wp-content/uploads/',
                'Allow: /wp-content/plugins/*.css',
                'Allow: /wp-content/plugins/*.js',
                '',
                '# Sitemap',
                ...$sitemap_lines,
                '',
            ];

            return implode("\n", $rules);
        }

        protected static function sitemap_urls() {
            $urls = [home_url('/wp-sitemap.xml')];

            if (defined('RANK_MATH_VERSION')) {
                array_unshift($urls, home_url('/sitemap_index.xml'));
            }

            return array_values(array_unique($urls));
        }

        /**
         * Add `noindex, follow` for paginated archive pages (page 2+)
         * so thin paginated lists don't crowd the index. Page 1 stays
         * indexable. Applies via core wp_robots filter.
         */
        public static function filter_pagination_robots($robots) {
            if (!self::is_paginated_archive_page()) return $robots;
            $robots = is_array($robots) ? $robots : [];
            unset($robots['index']);
            $robots['noindex'] = true;
            $robots['follow']  = true; // still follow links
            return $robots;
        }

        /**
         * Same for Rank Math (returns assoc array of directives).
         */
        public static function filter_pagination_rank_math($robots) {
            if (!self::is_paginated_archive_page()) return $robots;
            $robots = is_array($robots) ? $robots : [];
            unset($robots['index']);
            $robots['noindex'] = 'noindex';
            $robots['follow']  = 'follow';
            return $robots;
        }

        protected static function is_paginated_archive_page() {
            // page 2+ of any archive (category/tag/cpt-archive/date/author)
            $paged = max((int) get_query_var('paged'), (int) get_query_var('page'));
            if ($paged < 2) return false;
            return is_archive() || is_home() || is_post_type_archive() || is_tax() || is_category() || is_tag();
        }
    }

    SGH_SEO_Robots_Txt::init();
}
