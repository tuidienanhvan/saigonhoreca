<?php
/**
 * SEO Robots Control (Task 1, Phase 1 SEO)
 *
 * Force `index, follow` for revenue-critical category/term URLs that
 * were accidentally tagged `noindex` in Rank Math (cf. Technical Audit).
 *
 * Strategy:
 *   1. Filter Rank Math `rank_math/frontend/robots` so any setting in
 *      the admin UI is overridden at render time.
 *   2. Filter Yoast `wpseo_robots` for the same effect if Yoast becomes
 *      the active SEO plugin in the future.
 *   3. Filter core `wp_robots` (WP 5.7+) — fallback when no SEO plugin
 *      is installed (local dev / clean prod).
 *   4. Ensure self-referencing `<link rel="canonical">` is present
 *      (SEO plugins emit one; we only inject when none does).
 *   5. One-time DB clean-up: walk Rank Math term meta on admin load
 *      and clear `_rank_math_robots = noindex` for these slugs so the
 *      admin UI reflects the truth.
 *
 * Critical slugs (from technical audit):
 *   - thiet-bi-inox-cong-nghiep   (Inox công nghiệp)
 *   - quay-pha-che-inox-quay-bar  (Quầy pha chế / bar)
 *
 * Add more via `sgh_seo_force_index_slugs` filter.
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) exit;

if (!class_exists('SGH_SEO_Robots')) {

    class SGH_SEO_Robots {

        /** Default term slugs to force `index, follow` on. */
        const CRITICAL_SLUGS = [
            'thiet-bi-inox-cong-nghiep',
            'quay-pha-che-inox-quay-bar',
        ];

        public static function init() {
            // 1. Plugin-level filters (no-op if plugin not present)
            add_filter('rank_math/frontend/robots', [__CLASS__, 'filter_robots_array'], 99);
            add_filter('wpseo_robots',              [__CLASS__, 'filter_robots_string'], 99);

            // 2. Core wp_robots (WP 5.7+) — fallback for no-plugin envs
            add_filter('wp_robots', [__CLASS__, 'filter_wp_robots'], 99);

            // 3. Canonical fallback
            add_action('wp_head', [__CLASS__, 'maybe_inject_canonical'], 5);

            // 4. One-time admin cleanup of term meta
            add_action('admin_init', [__CLASS__, 'maybe_cleanup_term_meta']);
        }

        /**
         * Get the slug list, filterable so devs can add more critical
         * URLs without editing this file.
         */
        public static function critical_slugs() {
            return apply_filters('sgh_seo_force_index_slugs', self::CRITICAL_SLUGS);
        }

        /**
         * True when the currently-queried object is one of the critical
         * terms we want indexed.
         */
        public static function is_forced_index() {
            if (!is_tax() && !is_category() && !is_tag()) return false;
            $term = get_queried_object();
            if (!$term || empty($term->slug)) return false;
            return in_array($term->slug, self::critical_slugs(), true);
        }

        /* ── 1. Rank Math (returns array) ─────────────────────── */
        public static function filter_robots_array($robots) {
            if (self::is_pi_noindex()) {
                $robots = is_array($robots) ? $robots : [];
                unset($robots['index']);
                $robots['noindex'] = 'noindex';
                $robots['follow'] = 'follow';
                return $robots;
            }
            if (!self::is_forced_index()) return $robots;

            $robots = is_array($robots) ? $robots : [];
            // Rank Math expects associative array keyed by directive.
            $robots['index']    = 'index';
            $robots['follow']   = 'follow';
            unset($robots['noindex'], $robots['nofollow']);
            return $robots;
        }

        /* ── 2. Yoast (returns string) ────────────────────────── */
        public static function filter_robots_string($robots) {
            if (self::is_pi_noindex()) return 'noindex, follow';
            if (!self::is_forced_index()) return $robots;
            return 'index, follow';
        }

        /* ── 3. Core wp_robots (associative array) ────────────── */
        public static function filter_wp_robots($robots) {
            if (self::is_pi_noindex()) {
                $robots = is_array($robots) ? $robots : [];
                unset($robots['index'], $robots['none']);
                $robots['noindex'] = true;
                $robots['follow'] = true;
                return $robots;
            }
            if (!self::is_forced_index()) return $robots;
            $robots = is_array($robots) ? $robots : [];
            unset($robots['noindex'], $robots['nofollow'], $robots['none']);
            $robots['index']  = true;
            $robots['follow'] = true;
            // Common best-practice extras
            $robots['max-snippet']      = -1;
            $robots['max-image-preview']= 'large';
            $robots['max-video-preview']= -1;
            return $robots;
        }

        /* ── 4. Canonical fallback ──────────────────────────────
         * Only inject when nobody else does. Skip rules:
         *   - SEO plugin (Yoast/Rank Math) handles its own
         *   - WP core `rel_canonical()` is hooked on wp_head for
         *     singular pages — let it run, don't duplicate
         *   - Taxonomy / archive routes: WP core does NOT emit
         *     canonical → we inject (this is the value-add here) */
        public static function maybe_inject_canonical() {
            if (is_admin() || is_feed()) return;
            if (defined('WPSEO_VERSION') || defined('RANK_MATH_VERSION')) return;

            // WP core emits canonical for is_singular() via rel_canonical().
            // Only inject when WP core won't (taxonomy / archive / 404-safe home).
            if (is_singular() && has_action('wp_head', 'rel_canonical')) return;

            $url = '';
            if (is_tax() || is_category() || is_tag()) {
                $url = get_term_link(get_queried_object());
            } elseif (is_post_type_archive()) {
                $obj = get_queried_object();
                if ($obj && isset($obj->name)) $url = get_post_type_archive_link($obj->name);
            } elseif (is_front_page() || is_home()) {
                $url = home_url('/');
            }

            if ($url && !is_wp_error($url)) {
                echo '<link rel="canonical" href="' . esc_url($url) . '">' . "\n";
            }
        }

        /* ── 5. One-time DB cleanup (Rank Math meta) ──────────── */
        public static function maybe_cleanup_term_meta() {
            // Run at most once per release (option flag).
            $flag = 'sgh_seo_robots_cleanup_v1';
            if (get_option($flag)) return;

            // Only proceed if Rank Math is the active SEO plugin
            // (otherwise nothing to clean).
            if (!defined('RANK_MATH_VERSION')) return;

            foreach (self::critical_slugs() as $slug) {
                $term = self::find_term_by_slug($slug);
                if (!$term) continue;

                // Rank Math stores robots as serialized array in term meta
                $meta_key = 'rank_math_robots';
                $current  = get_term_meta($term->term_id, $meta_key, true);
                if (!is_array($current)) $current = [];

                // Filter out noindex/nofollow, ensure index/follow present
                $cleaned = array_values(array_diff($current, ['noindex', 'nofollow', 'none']));
                if (!in_array('index', $cleaned,  true)) $cleaned[] = 'index';
                if (!in_array('follow', $cleaned, true)) $cleaned[] = 'follow';

                if ($cleaned !== $current) {
                    update_term_meta($term->term_id, $meta_key, $cleaned);
                }
            }

            update_option($flag, time());
        }

        /**
         * Find a term across all taxonomies by slug (since product
         * categories may live under `product_cat`, custom taxonomy,
         * or default `category`).
         */
        protected static function find_term_by_slug($slug) {
            $taxonomies = ['product_cat', 'product_category', 'category', 'product_brand'];
            foreach ($taxonomies as $tax) {
                if (!taxonomy_exists($tax)) continue;
                $term = get_term_by('slug', $slug, $tax);
                if ($term && !is_wp_error($term)) return $term;
            }
            return null;
        }

        protected static function is_pi_noindex() {
            if (!is_singular() || !function_exists('sgh_pi_get_post_seo')) return false;
            $post = get_queried_object();
            if (!$post || empty($post->ID)) return false;
            $seo = sgh_pi_get_post_seo($post->ID);
            return !empty($seo['noindex']);
        }
    }

    SGH_SEO_Robots::init();
}
