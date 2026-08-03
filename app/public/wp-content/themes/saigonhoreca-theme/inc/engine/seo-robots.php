<?php
/**
 * Saigon Horeca SEO Robots & Canonical Module
 *
 * Controls robots directives (index/noindex), canonical link generation, and robots.txt outputs.
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) exit;

/* ============================================================================
 * 1. SEO ROBOTS TAG CONTROL
 * ============================================================================ */

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

        public static function critical_slugs() {
            return apply_filters('pi_seo_force_index_slugs', self::CRITICAL_SLUGS);
        }

        public static function is_forced_index() {
            if (!is_tax() && !is_category() && !is_tag()) return false;
            $term = get_queried_object();
            if (!$term || empty($term->slug)) return false;
            return in_array($term->slug, self::critical_slugs(), true);
        }

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
            $robots['index']    = 'index';
            $robots['follow']   = 'follow';
            unset($robots['noindex'], $robots['nofollow']);
            return $robots;
        }

        public static function filter_robots_string($robots) {
            if (self::is_pi_noindex()) return 'noindex, follow';
            if (!self::is_forced_index()) return $robots;
            return 'index, follow';
        }

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
            $robots['max-snippet']      = -1;
            $robots['max-image-preview']= 'large';
            $robots['max-video-preview']= -1;
            return $robots;
        }

        public static function maybe_inject_canonical() {
            if (is_admin() || is_feed()) return;
            if (defined('WPSEO_VERSION') || defined('RANK_MATH_VERSION')) return;

            // Core WP vẫn đang phát canonical cho singular -> nhường, tránh 2 thẻ.
            if (is_singular() && has_action('wp_head', 'rel_canonical')) return;

            // ⚠️ 2026-08-03 — thứ tự nhánh có Ý NGHĨA.
            //
            // Trang chủ khi show_on_front='page' vừa là is_front_page() VỪA là
            // is_singular(). Bản cũ đặt nhánh front-page NGANG HÀNG với các
            // nhánh archive nhưng KHÔNG có nhánh singular, nên:
            //   - core rel_canonical bị pi-multilang gỡ (class-seo.php:25)
            //   - guard ở trên vì thế không kích hoạt
            //   - theme phát 1 thẻ (nhánh front-page) + plugin phát 1 thẻ
            //     (nhánh is_singular) => TRANG CHỦ CÓ 2 CANONICAL.
            // Google gặp nhiều canonical có thể bỏ qua hết và tự chọn URL.
            //
            // Nay theme là NGUỒN DUY NHẤT: xử lý cả singular, và plugin đã bỏ
            // phần canonical của nó (chỉ còn hreflang). Nhánh singular phải
            // đứng TRƯỚC front-page vì trang chủ dạng page rơi vào cả hai —
            // get_permalink() cho đúng URL hơn home_url('/') khi site chạy
            // trong thư mục con.
            $url = '';
            if (is_singular()) {
                $obj = get_queried_object();
                if ($obj instanceof WP_Post) $url = get_permalink($obj->ID);
            } elseif (is_tax() || is_category() || is_tag()) {
                $url = get_term_link(get_queried_object());
            } elseif (is_post_type_archive()) {
                $obj = get_queried_object();
                if ($obj && isset($obj->name)) $url = get_post_type_archive_link($obj->name);
            } elseif (is_front_page() || is_home()) {
                // Chỉ còn dùng khi show_on_front='posts' (trang chủ KHÔNG singular).
                $url = home_url('/');
            }

            if ($url && !is_wp_error($url)) {
                echo '<link rel="canonical" href="' . esc_url($url) . '">' . "\n";
            }
        }

        public static function maybe_cleanup_term_meta() {
            $flag = 'pi_seo_robots_cleanup_v1';
            if (get_option($flag)) return;
            if (!defined('RANK_MATH_VERSION')) return;

            foreach (self::critical_slugs() as $slug) {
                $term = self::find_term_by_slug($slug);
                if (!$term) continue;

                $meta_key = 'rank_math_robots';
                $current  = get_term_meta($term->term_id, $meta_key, true);
                if (!is_array($current)) $current = [];

                $cleaned = array_values(array_diff($current, ['noindex', 'nofollow', 'none']));
                if (!in_array('index', $cleaned,  true)) $cleaned[] = 'index';
                if (!in_array('follow', $cleaned, true)) $cleaned[] = 'follow';

                if ($cleaned !== $current) {
                    update_term_meta($term->term_id, $meta_key, $cleaned);
                }
            }

            update_option($flag, time());
        }

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
}

/* ============================================================================
 * 2. ROBOTS.TXT & PAGINATION ROBOTS
 * ============================================================================ */

if (!class_exists('SGH_SEO_Robots_Txt')) {

    class SGH_SEO_Robots_Txt {

        public static function init() {
            add_filter('robots_txt',           [__CLASS__, 'filter_robots_txt'], 20, 2);
            add_filter('wp_robots',            [__CLASS__, 'filter_pagination_robots'], 50);
            add_filter('rank_math/frontend/robots', [__CLASS__, 'filter_pagination_rank_math'], 50);
        }

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

        public static function filter_pagination_robots($robots) {
            if (!self::is_paginated_archive_page()) return $robots;
            $robots = is_array($robots) ? $robots : [];
            unset($robots['index']);
            $robots['noindex'] = true;
            $robots['follow']  = true;
            return $robots;
        }

        public static function filter_pagination_rank_math($robots) {
            if (!self::is_paginated_archive_page()) return $robots;
            $robots = is_array($robots) ? $robots : [];
            unset($robots['index']);
            $robots['noindex'] = 'noindex';
            $robots['follow']  = 'follow';
            return $robots;
        }

        protected static function is_paginated_archive_page() {
            $paged = max((int) get_query_var('paged'), (int) get_query_var('page'));
            if ($paged < 2) return false;
            return is_archive() || is_home() || is_post_type_archive() || is_tax() || is_category() || is_tag();
        }
    }
}

// Khởi chạy ngay
SGH_SEO_Robots::init();
SGH_SEO_Robots_Txt::init();
