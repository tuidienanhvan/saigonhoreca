<?php
/**
 * Auto Meta Description (Task 3, Phase 1 SEO)
 *
 * Build a clean, SERP-friendly meta description in 120-155 chars
 * for every product/project/single post + core taxonomy pages.
 *
 * Strategy:
 *   - If a manually-written description exists AND is in [120, 160]
 *     range → keep it (respect editorial intent).
 *   - Else → auto-compose:
 *       [Title] – [Brand] – [USP/excerpt 60 chars] – Hotline: 0901 304 365
 *     then clamp to 155 chars (ellipsis-safe word boundary).
 *
 * Hooks both SEO plugins so it Just Works regardless of whether
 * Yoast / Rank Math is the active SEO plugin on prod:
 *   - `wpseo_metadesc`               (Yoast)
 *   - `rank_math/frontend/description` (Rank Math)
 *   - `wp_head` (fallback): inject <meta name="description"> when
 *     no SEO plugin is present (e.g. on local dev).
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) exit;

if (!class_exists('SGH_SEO_Meta_Description')) {

    class SGH_SEO_Meta_Description {

        /** Target range for a "good" meta description. */
        const MIN_LEN = 120;
        const MAX_LEN = 155;

        /** Cache resolved description per request to avoid recomputation. */
        protected static $cache = [];

        public static function init() {
            // SEO plugin filters (no-ops if plugin not installed)
            add_filter('wpseo_metadesc',                [__CLASS__, 'filter_existing'], 20, 1);
            add_filter('rank_math/frontend/description',[__CLASS__, 'filter_existing'], 20, 1);
            add_filter('wpseo_title',                   [__CLASS__, 'filter_title'], 20, 1);
            add_filter('rank_math/frontend/title',      [__CLASS__, 'filter_title'], 20, 1);
            add_filter('pre_get_document_title',        [__CLASS__, 'filter_title'], 20, 1);

            // Fallback: inject <meta name="description"> when no SEO plugin
            // present. Hook at priority 1 so it lands above plugin/theme noise.
            add_action('wp_head', [__CLASS__, 'maybe_inject_meta'], 1);
        }

        /**
         * Filter callback for SEO plugins. If their existing description
         * is empty or out-of-range, replace with our auto-built one.
         * Otherwise pass-through (respect editor's choice).
         */
        public static function filter_existing($desc) {
            $pi_desc = self::pi_description_for_current();
            if ($pi_desc !== '') {
                return $pi_desc;
            }

            $desc = is_string($desc) ? trim($desc) : '';
            $len  = mb_strlen($desc);

            if ($desc !== '' && $len >= self::MIN_LEN && $len <= 160) {
                return $desc; // already good
            }

            $auto = self::build_for_current();
            return $auto !== '' ? $auto : $desc;
        }

        public static function filter_title($title) {
            $pi_title = self::pi_title_for_current();
            if ($pi_title !== '') {
                return $pi_title;
            }
            return $title;
        }

        /**
         * Inject fallback <meta name="description"> when no SEO plugin
         * has output one (Yoast/Rank Math both echo their tag in wp_head
         * at priority 1; we run also at priority 1 but check first).
         */
        public static function maybe_inject_meta() {
            if (is_admin() || is_feed()) return;

            // Skip if Yoast / Rank Math already emit their own tag.
            if (defined('WPSEO_VERSION') || defined('RANK_MATH_VERSION')) return;

            $desc = self::build_for_current();
            if ($desc === '') return;

            echo '<meta name="description" content="' . esc_attr($desc) . '">' . "\n";
        }

        /**
         * Build optimised description for the currently queried object.
         * Returns '' if no suitable context (e.g. admin / search / 404).
         */
        public static function build_for_current() {
            $key = self::cache_key();
            if ($key === '') return '';
            if (isset(self::$cache[$key])) return self::$cache[$key];

            $desc = '';

            if (is_singular(['product', 'project', 'post', 'page'])) {
                $desc = self::build_for_singular(get_queried_object());
            } elseif (is_tax() || is_category() || is_tag()) {
                $desc = self::build_for_term(get_queried_object());
            } elseif (is_post_type_archive(['product', 'project'])) {
                $desc = self::build_for_archive(get_queried_object());
            } elseif (is_front_page() || is_home()) {
                $desc = self::build_for_home();
            }

            return self::$cache[$key] = $desc;
        }

        /* ── Builders per context ──────────────────────────────── */

        protected static function build_for_singular($post) {
            if (!$post) return '';
            $bundle = function_exists('sgh_pi_get_post_seo') ? sgh_pi_get_post_seo($post->ID) : [];
            if (!empty($bundle['seo_description'])) {
                return self::clean($bundle['seo_description']);
            }

            $title = self::clean($bundle['seo_title'] ?? get_the_title($post));
            $brand = self::resolve_brand($post);
            $usp   = self::resolve_usp($post);

            return self::compose($title, $brand, $usp);
        }

        protected static function build_for_term($term) {
            if (!$term) return '';
            $title = self::clean($term->name);
            $usp   = self::clean(wp_strip_all_tags((string) $term->description));
            return self::compose($title, '', $usp ?: 'Sản phẩm chính hãng, giá tốt, bảo hành dài hạn');
        }

        protected static function build_for_archive($obj) {
            $label = '';
            if ($obj && isset($obj->labels->name)) {
                $label = self::clean($obj->labels->name);
            } elseif ($obj && isset($obj->name)) {
                $label = self::clean(ucfirst($obj->name));
            }
            $usp = 'Bếp công nghiệp, quầy bar chuẩn HORECA — thiết kế trọn gói, lắp đặt nhanh';
            return self::compose($label, '', $usp);
        }

        protected static function build_for_home() {
            $site = function_exists('get_bloginfo') ? get_bloginfo('name') : 'Saigon Horeca';
            $usp  = 'Thiết bị bếp công nghiệp, inox, quầy bar — thiết kế thi công trọn gói chuẩn HORECA cho nhà hàng, khách sạn, café';
            return self::compose($site, '', $usp);
        }

        /* ── Helpers ──────────────────────────────────────────── */

        /**
         * Compose: "[Title] – [Brand] – [USP] – Hotline: <num>"
         * Then clamp to MAX_LEN at a word boundary.
         */
        protected static function compose($title, $brand, $usp) {
            $title = self::clean($title);
            $brand = self::clean($brand);
            $usp   = self::clean($usp);

            $hotline = self::hotline();
            $tail    = $hotline ? ' – Hotline: ' . $hotline : '';

            // Try full version first.
            $parts = array_filter([$title, $brand, $usp]);
            $core  = implode(' – ', $parts);
            $full  = $core . $tail;

            if (mb_strlen($full) <= self::MAX_LEN) {
                return self::pad_min($full, $title, $hotline);
            }

            // Trim USP first.
            $room = self::MAX_LEN - mb_strlen($title) - ($brand !== '' ? mb_strlen($brand) + 3 : 0) - mb_strlen($tail) - 3;
            if ($room > 20) {
                $usp_trim = self::word_clip($usp, $room - 1) . '…';
                $core = trim(implode(' – ', array_filter([$title, $brand, $usp_trim])));
                $full = $core . $tail;
                if (mb_strlen($full) <= self::MAX_LEN) {
                    return $full;
                }
            }

            // Last resort: clip title.
            $room = self::MAX_LEN - mb_strlen($tail) - 1;
            return self::word_clip($title, $room) . '…' . $tail;
        }

        /**
         * Pad description up to MIN_LEN with boilerplate snippets.
         * Tries each snippet in order; keeps the longest version that
         * still fits under MAX_LEN. Always reaches MIN_LEN when room
         * allows because snippets are sized to fill the gap.
         */
        protected static function pad_min($desc, $title, $hotline) {
            $len = mb_strlen($desc);
            if ($len >= self::MIN_LEN) return $desc;

            // Boilerplate snippets ordered by relevance / length.
            // Avoid `&` (HTML-encodes to `&amp;` → inflates byte count
            // while SERP only sees 1 char). Use "+" or omit conjunction.
            $snippets = [
                'Bếp công nghiệp, quầy bar, inox HORECA cho nhà hàng, khách sạn, café cao cấp — giá tốt',
                'Tư vấn miễn phí, thiết kế trọn gói, lắp đặt tận nơi toàn quốc — bảo hành chính hãng',
                'Giá tốt, bảo hành chính hãng, đội ngũ kỹ sư chuyên nghiệp, lắp đặt nhanh',
                'Giá tốt, bảo hành chính hãng',
            ];

            $needle = ' – Hotline:';
            $has_hotline_tail = $hotline && strpos($desc, $needle) !== false;
            [$head, $tail] = $has_hotline_tail
                ? explode($needle, $desc, 2)
                : [$desc, ''];
            $tail_full = $has_hotline_tail ? ($needle . $tail) : '';

            // Try snippets in order; pick longest one that fits MAX_LEN.
            $best = $desc;
            foreach ($snippets as $snip) {
                $candidate = $head . ' – ' . $snip . $tail_full;
                $clen = mb_strlen($candidate);
                if ($clen <= self::MAX_LEN && $clen >= mb_strlen($best)) {
                    $best = $candidate;
                    if ($clen >= self::MIN_LEN) break; // good enough
                }
            }
            return $best;
        }

        protected static function resolve_brand($post) {
            // Try common brand taxonomies in order.
            foreach (['product_brand', 'brand', 'thuong-hieu'] as $tax) {
                if (taxonomy_exists($tax)) {
                    $terms = get_the_terms($post, $tax);
                    if (!is_wp_error($terms) && !empty($terms)) {
                        return self::clean($terms[0]->name);
                    }
                }
            }
            // Fallback: ACF field 'brand' if present.
            if (function_exists('get_field')) {
                $b = get_field('brand', $post->ID);
                if (is_string($b) && $b !== '') return self::clean($b);
            }
            return '';
        }

        protected static function resolve_usp($post) {
            // Priority: explicit excerpt → ACF 'usp' → first 60 chars of content
            $ex = has_excerpt($post) ? get_the_excerpt($post) : '';
            $ex = self::clean(wp_strip_all_tags((string) $ex));
            if ($ex !== '') return self::word_clip($ex, 70);

            if (function_exists('get_field')) {
                $u = get_field('usp', $post->ID);
                if (is_string($u) && $u !== '') return self::word_clip(self::clean($u), 70);
            }

            $content = self::clean(wp_strip_all_tags((string) $post->post_content));
            if ($content !== '') return self::word_clip($content, 70);

            return '';
        }

        protected static function hotline() {
            if (function_exists('saigonhouse_get_contact_info')) {
                $c = saigonhouse_get_contact_info();
                if (!empty($c['hotline'])) return $c['hotline'];
            }
            return '';
        }

        protected static function clean($s) {
            $s = (string) $s;
            $s = preg_replace('/\s+/u', ' ', $s);
            $s = str_replace(['"', "\xc2\xa0"], ['', ' '], $s);
            return trim($s);
        }

        /** Clip to N chars at a word boundary (no trailing partial word). */
        protected static function word_clip($s, $n) {
            $s = self::clean($s);
            if (mb_strlen($s) <= $n) return $s;
            $clip = mb_substr($s, 0, $n);
            $pos  = mb_strrpos($clip, ' ');
            if ($pos !== false && $pos > $n * 0.6) {
                $clip = mb_substr($clip, 0, $pos);
            }
            return rtrim($clip, " .,;:–-");
        }

        protected static function cache_key() {
            $obj = get_queried_object();
            if (is_singular()) return 's:' . ($obj->ID ?? '');
            if (is_tax() || is_category() || is_tag()) return 't:' . ($obj->term_id ?? '');
            if (is_post_type_archive()) return 'a:' . ($obj->name ?? '');
            if (is_front_page() || is_home()) return 'home';
            return '';
        }

        protected static function pi_description_for_current() {
            if (!is_singular()) return '';
            $post = get_queried_object();
            if (!$post || empty($post->ID) || !function_exists('sgh_pi_get_post_seo')) return '';

            return self::clean((string) (sgh_pi_get_post_seo($post->ID)['seo_description'] ?? ''));
        }

        protected static function pi_title_for_current() {
            if (!is_singular()) return '';
            $post = get_queried_object();
            if (!$post || empty($post->ID) || !function_exists('sgh_pi_get_post_seo')) return '';

            return self::clean((string) (sgh_pi_get_post_seo($post->ID)['seo_title'] ?? ''));
        }
    }

    SGH_SEO_Meta_Description::init();
}
