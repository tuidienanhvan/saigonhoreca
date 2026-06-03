<?php
/**
 * Saigon Horeca SEO & Performance Manager
 *
 * Centralized hub for all SEO, robots, schema, performance, and HTML rewrite rules.
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) exit;

/* ============================================================================
 * 1. AUTO META DESCRIPTION & TITLE FILTERS
 * ============================================================================ */

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
}

/* ============================================================================
 * 2. SEO ROBOTS TAG CONTROL
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
 * 3. STRUCTURED DATA (JSON-LD SCHEMA)
 * ============================================================================ */

if (!class_exists('SGH_SEO_Schema')) {

    class SGH_SEO_Schema {

        /**
         * Registry for dynamic schemas collected during template rendering.
         * @var array
         */
        private static $registered_schemas = [];

        public static function init() {
            add_action('wp_head', [__CLASS__, 'render'], 30);
            add_action('wp_footer', [__CLASS__, 'render_registered_schemas'], 99);
        }

        /**
         * Register a dynamic JSON-LD schema to be printed in the footer.
         */
        public static function register($key, array $schema) {
            self::$registered_schemas[$key] = $schema;
        }

        /**
         * Render all dynamically registered schemas in the footer.
         */
        public static function render_registered_schemas() {
            if (is_admin() || is_feed() || empty(self::$registered_schemas)) return;

            foreach (self::$registered_schemas as $key => $schema) {
                echo '<!-- SGH SEO Schema: ' . esc_html($key) . ' -->' . "\n";
                echo '<script type="application/ld+json">'
                    . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                    . '</script>' . "\n";
            }
        }

        public static function render() {
            if (is_admin() || is_feed()) return;

            $graphs = self::pi_rule_graphs();

            if (empty($graphs)) {
                $graphs = [];

                // Always-on: LocalBusiness
                $g = self::local_business();
                if ($g) $graphs[] = $g;

                // Homepage: WebSite with SearchAction
                if (is_front_page() || is_home()) {
                    $g = self::website_search();
                    if ($g) $graphs[] = $g;
                }

                // Single product: Product schema
                if (is_singular('product')) {
                    $g = self::product(get_post());
                    if ($g) $graphs[] = $g;
                }
            }

            if (empty($graphs)) return;

            foreach ($graphs as $g) {
                echo '<script type="application/ld+json">'
                    . wp_json_encode($g, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                    . '</script>' . "\n";
            }
        }

        protected static function local_business() {
            $c = self::contact();
            if (!$c) return null;

            $home = home_url('/');
            $logo = self::asset_url('assets/images/logo.png');

            $data = [
                '@context'   => 'https://schema.org',
                '@type'      => 'LocalBusiness',
                '@id'        => $home . '#organization',
                'name'       => $c['company_name'] ?? get_bloginfo('name'),
                'legalName'  => $c['company_full'] ?? null,
                'url'        => $home,
                'description'=> $c['description'] ?? get_bloginfo('description'),
                'telephone'  => $c['hotline'] ?? null,
                'email'      => $c['email_primary'] ?? null,
            ];

            if (!empty($c['address'])) {
                $data['address'] = [
                    '@type'           => 'PostalAddress',
                    'streetAddress'   => $c['address'],
                    'addressLocality' => 'Hồ Chí Minh',
                    'addressRegion'   => 'Hồ Chí Minh',
                    'addressCountry'  => 'VN',
                ];
            }

            if ($logo) {
                $data['logo'] = [
                    '@type' => 'ImageObject',
                    'url'   => $logo,
                ];
                $data['image'] = $logo;
            }

            $sameAs = array_filter([
                $c['facebook']  ?? null,
                $c['youtube']   ?? null,
                $c['zalo']      ?? null,
                $c['instagram'] ?? null,
                $c['tiktok']    ?? null,
            ]);
            if ($sameAs) $data['sameAs'] = array_values($sameAs);

            $contactPoints = [];
            if (!empty($c['hotline'])) {
                $contactPoints[] = [
                    '@type'             => 'ContactPoint',
                    'telephone'         => $c['hotline'],
                    'contactType'       => 'sales',
                    'areaServed'        => 'VN',
                    'availableLanguage' => ['Vietnamese', 'English'],
                ];
            }
            if (!empty($c['hotline_alt'])) {
                $contactPoints[] = [
                    '@type'       => 'ContactPoint',
                    'telephone'   => $c['hotline_alt'],
                    'contactType' => 'customer support',
                    'areaServed'  => 'VN',
                ];
            }
            if ($contactPoints) $data['contactPoint'] = $contactPoints;

            return array_filter($data, static function($v) { return $v !== null && $v !== ''; });
        }

        protected static function website_search() {
            $home = home_url('/');
            return [
                '@context'      => 'https://schema.org',
                '@type'         => 'WebSite',
                '@id'           => $home . '#website',
                'url'           => $home,
                'name'          => get_bloginfo('name'),
                'description'   => get_bloginfo('description'),
                'publisher'     => ['@id' => $home . '#organization'],
                'potentialAction' => [
                    '@type'       => 'SearchAction',
                    'target'      => [
                        '@type'       => 'EntryPoint',
                        'urlTemplate' => $home . '?s={search_term_string}',
                    ],
                    'query-input' => 'required name=search_term_string',
                ],
                'inLanguage' => 'vi-VN',
            ];
        }

        protected static function product($post) {
            if (!$post) return null;

            $sku    = get_post_meta($post->ID, '_sku', true);
            $brands = wp_get_post_terms($post->ID, 'product_brand');
            $cats   = wp_get_post_terms($post->ID, 'product_category');

            $brand_name = (!is_wp_error($brands) && !empty($brands)) ? $brands[0]->name : null;
            $cat_name   = (!is_wp_error($cats)   && !empty($cats))   ? $cats[0]->name   : null;

            $img = get_the_post_thumbnail_url($post, 'large');
            $desc = '';
            if (has_excerpt($post)) {
                $desc = wp_strip_all_tags(get_the_excerpt($post));
            } else {
                $desc = wp_strip_all_tags($post->post_content);
                $desc = mb_substr($desc, 0, 200);
            }
            $desc = preg_replace('/\s+/u', ' ', trim((string) $desc));

            $url = get_permalink($post);

            $data = [
                '@context'    => 'https://schema.org',
                '@type'       => 'Product',
                '@id'         => $url . '#product',
                'name'        => get_the_title($post),
                'url'         => $url,
                'description' => $desc ?: null,
                'sku'         => $sku ?: null,
                'mpn'         => $sku ?: null,
                'image'       => $img ?: null,
                'category'    => $cat_name,
                'brand'       => $brand_name ? [
                    '@type' => 'Brand',
                    'name'  => $brand_name,
                ] : null,
            ];

            return array_filter($data, static function($v) { return $v !== null && $v !== ''; });
        }

        protected static function contact() {
            if (function_exists('saigonhouse_get_contact_info')) {
                $c = saigonhouse_get_contact_info();
                return is_array($c) ? $c : null;
            }
            return null;
        }

        protected static function asset_url($rel) {
            $path = get_template_directory() . '/' . ltrim($rel, '/');
            if (!file_exists($path)) return null;
            return get_template_directory_uri() . '/' . ltrim($rel, '/');
        }

        protected static function pi_rule_graphs() {
            if (!function_exists('sgh_pi_has_schema_rules') || !sgh_pi_has_schema_rules()) {
                return [];
            }

            $rules = function_exists('sgh_pi_get_schema_rules') ? sgh_pi_get_schema_rules() : [];
            $post = is_singular() ? get_post() : null;
            $graphs = [];

            foreach ($rules as $rule) {
                if (!is_array($rule) || empty($rule['json_ld']) || empty($rule['enabled'])) {
                    continue;
                }
                if (!self::pi_rule_applies($rule, $post)) {
                    continue;
                }
                $graphs[] = self::pi_rule_resolve($rule['json_ld'], $post);
            }

            return $graphs;
        }

        protected static function pi_rule_applies(array $rule, $post) {
            $scope = $rule['scope'] ?? 'site';
            if ($scope === 'site') return true;
            if (!$post) return false;

            $conditions = is_array($rule['conditions'] ?? null) ? $rule['conditions'] : [];
            if ($scope === 'post_type') {
                $target = (string) ($conditions['post_type'] ?? '');
                return $target === '' || $target === $post->post_type;
            }
            if ($scope === 'single') {
                $target = (int) ($conditions['post_id'] ?? 0);
                return $target === 0 || $target === (int) $post->ID;
            }

            return false;
        }

        protected static function pi_rule_resolve($schema, $post) {
            if (!$post) return $schema;

            $replacements = [
                '%%post_title%%'     => get_the_title($post),
                '%%post_url%%'       => get_permalink($post),
                '%%post_excerpt%%'   => wp_strip_all_tags(get_the_excerpt($post)),
                '%%post_author%%'    => get_the_author_meta('display_name', $post->post_author),
                '%%post_date%%'      => mysql2date('c', $post->post_date),
                '%%post_modified%%'  => mysql2date('c', $post->post_modified),
                '%%featured_image%%' => get_the_post_thumbnail_url($post, 'full') ?: '',
                '%%site_name%%'      => get_bloginfo('name'),
                '%%site_url%%'       => home_url('/'),
            ];

            if (is_string($schema)) {
                return strtr($schema, $replacements);
            }
            if (is_array($schema)) {
                $resolved = [];
                foreach ($schema as $key => $value) {
                    $resolved[$key] = self::pi_rule_resolve($value, $post);
                }
                return $resolved;
            }

            return $schema;
        }
    }
}

/* ============================================================================
 * 4. ROBOTS.TXT & PAGINATION ROBOTS
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

/* ============================================================================
 * 5. PERFORMANCE, RESOURCE HINTS, HTTPS REWRITE, AND 404 IMAGE FALLBACKS
 * ============================================================================ */

if (!class_exists('SGH_SEO_Performance')) {

    class SGH_SEO_Performance {

        public static function init() {
            // 1+2. Image attributes
            if (self::enabled('img_attrs')) {
                add_filter('wp_get_attachment_image_attributes', [__CLASS__, 'img_attrs'], 20, 3);
                add_filter('the_content',                         [__CLASS__, 'content_img_attrs'], 999);
            }

            // 3. Resource hints
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

            // 5. Force HTTPS URL rewriting filters (Lighthouse performance/best practices)
            add_filter('option_siteurl',              [__CLASS__, 'force_https_url']);
            add_filter('option_home',                 [__CLASS__, 'force_https_url']);
            add_filter('wp_get_attachment_url',       [__CLASS__, 'force_https_url']);
            add_filter('wp_get_attachment_image_src', [__CLASS__, 'force_https_image_src']);
            add_filter('wp_calculate_image_srcset',   [__CLASS__, 'force_https_image_srcset']);

            // 6. Setup output buffering rewrite (HTTP to HTTPS, 404 images, manual Gzip)
            add_action('template_redirect',           [__CLASS__, 'buffer_output_setup'], 999);

            // 7. Fallback 404 attachment images (Moved from functions.php)
            add_filter('post_thumbnail_html',         [__CLASS__, 'fallback_404_thumbnail_html'], 99, 5);
            add_filter('wp_calculate_image_srcset',   [__CLASS__, 'fallback_404_image_srcset'], 99, 5);
            add_filter('wp_get_attachment_image_src', [__CLASS__, 'fallback_404_image_src'], 99, 4);

            // 8. Security Headers
            add_action('send_headers',                [__CLASS__, 'send_security_headers']);
        }

        public static function send_security_headers() {
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

            // Cross-Origin-Opener-Policy — only send on HTTPS
            if ($is_https) {
                header('Cross-Origin-Opener-Policy: same-origin');
            }

            // Content Security Policy
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
        }

        public static function img_attrs($attr, $attachment, $size) {
            if (empty($attr['decoding'])) $attr['decoding'] = 'async';
            if (empty($attr['loading'])) $attr['loading'] = 'lazy';
            
            // Auto alt for attachment images
            if (empty($attr['alt'])) {
                $attr['alt'] = get_the_title() ? wp_strip_all_tags(get_the_title()) : get_the_title($attachment->ID);
            }
            return $attr;
        }

        public static function content_img_attrs($html) {
            if (is_admin() || empty($html)) return $html;
            if (strpos($html, '<img') === false) return $html;

            $count = 0;
            $post_title = is_singular() ? esc_attr(wp_strip_all_tags(get_the_title())) : '';

            return preg_replace_callback(
                '/<img\b[^>]*>/i',
                static function($m) use (&$count, $post_title) {
                    $tag = $m[0];
                    $count++;

                    if (stripos($tag, 'decoding=') === false) {
                        $tag = preg_replace('/<img\b/i', '<img decoding="async"', $tag, 1);
                    }

                    if ($count > 1) {
                        if (stripos($tag, 'loading=') === false) {
                            $tag = preg_replace('/<img\b/i', '<img loading="lazy"', $tag, 1);
                        }
                    } else {
                        $tag = preg_replace('/\s+loading=[\'"]lazy[\'"]/i', '', $tag);
                    }

                    if ($post_title) {
                        if (stripos($tag, 'alt=') === false) {
                            $tag = preg_replace('/<img\b/i', '<img alt="' . $post_title . '"', $tag, 1);
                        } else {
                            $tag = preg_replace('/\s+alt=[\'"]\s*[\'"]/i', ' alt="' . $post_title . '"', $tag);
                        }
                    }

                    return $tag;
                },
                $html
            );
        }

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
            return $urls;
        }

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
            add_filter('wp_resource_hints', static function($urls, $relation_type) {
                if ('dns-prefetch' === $relation_type) {
                    $emoji_svg_url = apply_filters('emoji_svg_url', 'https://s.w.org/images/core/emoji/14.0.0/svg/');
                    $urls = array_diff($urls, [$emoji_svg_url, 'https://s.w.org']);
                }
                return $urls;
            }, 10, 2);
        }

        public static function strip_oembed() {
            remove_action('wp_head', 'wp_oembed_add_discovery_links');
            remove_action('wp_head', 'wp_oembed_add_host_js');
        }

        public static function force_https_url($url) {
            if (is_string($url) && strpos($url, 'http://') === 0) {
                return 'https://' . substr($url, 7);
            }
            return $url;
        }

        public static function force_https_image_src($image) {
            if (is_array($image) && !empty($image[0])) {
                $image[0] = self::force_https_url($image[0]);
            }
            return $image;
        }

        public static function force_https_image_srcset($sources) {
            if (is_array($sources)) {
                foreach ($sources as &$s) {
                    if (isset($s['url'])) {
                        $s['url'] = self::force_https_url($s['url']);
                    }
                }
            }
            return $sources;
        }

        public static function buffer_output_setup() {
            if (is_admin() || wp_doing_ajax() || defined('REST_REQUEST')) {
                return;
            }
            ob_start([__CLASS__, 'buffer_output_callback']);
        }

        public static function buffer_output_callback($buffer) {
            $buffer = str_replace('http://saigonhoreca.local', 'https://saigonhoreca.local', (string) $buffer);
            
            $fallback_url = 'https://saigonhoreca.local/wp-content/uploads/saigonhoreca/the-royal-sgh-10.webp';
            
            $buffer = preg_replace(
                '/https?:\/\/[^\s\'",]+tai-sao-cac-nha-hang-cao-cap-nen-dau-tu-josper[^\s\'",]*\.(jpg|png|jpeg|webp)/i',
                $fallback_url,
                $buffer
            );
            $buffer = preg_replace(
                '/\/wp-content\/uploads\/[^\s\'",]+tai-sao-cac-nha-hang-cao-cap-nen-dau-tu-josper[^\s\'",]*\.(jpg|png|jpeg|webp)/i',
                $fallback_url,
                $buffer
            );
            
            if (!headers_sent() && isset($_SERVER['HTTP_ACCEPT_ENCODING']) && strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false) {
                header('Content-Encoding: gzip');
                $buffer = gzencode($buffer, 9);
            }
            
            return $buffer;
        }

        /**
         * Tự động chặn các ảnh thumbnail bị lỗi 404 do thiếu tệp vật lý local,
         * thay thế bằng ảnh có sẵn chất lượng cao the-royal-sgh-10.webp.
         */
        public static function fallback_404_thumbnail_html($html, $post_id, $post_thumbnail_id, $size, $attr) {
            if (empty($html)) return $html;

            if (preg_match('/src=[\'"]([^\'"]+)[\'"]/i', $html, $matches)) {
                $img_url = $matches[1];
                
                $uploads_info = wp_get_upload_dir();
                $base_url = $uploads_info['baseurl'];
                $base_dir = $uploads_info['basedir'];
                
                if (strpos($img_url, $base_url) !== false) {
                    $relative_path = str_replace($base_url, '', $img_url);
                    $physical_file = wp_normalize_path($base_dir . $relative_path);
                    
                    if (!file_exists($physical_file)) {
                        $fallback_url = esc_url($base_url . '/saigonhoreca/the-royal-sgh-10.webp');
                        
                        $html = preg_replace('/src=[\'"]([^\'"]+)[\'"]/i', 'src="' . $fallback_url . '"', $html);
                        $html = preg_replace('/srcset=[\'"]([^\'"]+)[\'"]/i', 'srcset=""', $html);
                        $html = preg_replace('/sizes=[\'"]([^\'"]+)[\'"]/i', 'sizes=""', $html);
                    }
                }
            }
            return $html;
        }

        /**
         * Triệt tiêu lỗi 404 srcset của WordPress đối với các ảnh không tồn tại.
         */
        public static function fallback_404_image_srcset($sources, $size_array, $image_src, $image_meta, $attachment_id) {
            if (empty($sources) || !is_array($sources)) return $sources;

            $uploads_info = wp_get_upload_dir();
            $base_dir = $uploads_info['basedir'];
            $base_url = $uploads_info['baseurl'];

            if (!empty($image_meta['file'])) {
                $physical_file = wp_normalize_path($base_dir . '/' . $image_meta['file']);
                if (!file_exists($physical_file)) {
                    $fallback_url = rtrim($base_url, '/') . '/saigonhoreca/the-royal-sgh-10.webp';
                    $fallback_url = str_replace('http://', 'https://', $fallback_url);
                    $fallback_url = esc_url($fallback_url);
                    return [
                        450 => [
                            'url'        => $fallback_url,
                            'descriptor' => 'w',
                            'value'      => 450
                        ]
                    ];
                }
            }

            $first_source = reset($sources);
            if ($first_source && isset($first_source['url'])) {
                $url = $first_source['url'];
                if (strpos($url, $base_url) !== false) {
                    $relative_path = str_replace($base_url, '', $url);
                    
                    if (preg_match('/^(.+)-\d+x\d+\.(jpg|jpeg|png|webp|gif)$/i', $relative_path, $matches)) {
                        $original_relative = $matches[1] . '.' . $matches[2];
                    } else {
                        $original_relative = $relative_path;
                    }
                    
                    $physical_file = wp_normalize_path($base_dir . $original_relative);
                    if (!file_exists($physical_file)) {
                        return array();
                    }
                }
            }
            return $sources;
        }

        /**
         * Triệt tiêu lỗi 404 ở mức thấp hơn cho mọi attachment image src.
         */
        public static function fallback_404_image_src($image, $attachment_id, $size, $icon) {
            if (empty($image) || !is_array($image)) return $image;

            $img_url = $image[0];
            $uploads_info = wp_get_upload_dir();
            $base_url = $uploads_info['baseurl'];
            $base_dir = $uploads_info['basedir'];

            $normalized_url = str_replace(['http://', 'https://'], '//', $img_url);
            $normalized_base = str_replace(['http://', 'https://'], '//', $base_url);

            if (strpos($normalized_url, $normalized_base) !== false) {
                $relative_path = str_replace($normalized_base, '', $normalized_url);
                
                if (preg_match('/^(.+)-\d+x\d+\.(jpg|jpeg|png|webp|gif)$/i', $relative_path, $matches)) {
                    $original_relative = $matches[1] . '.' . $matches[2];
                } else {
                    $original_relative = $relative_path;
                }
                
                $physical_file = wp_normalize_path($base_dir . '/' . ltrim($original_relative, '/'));
                
                if (!file_exists($physical_file)) {
                    $fallback_url = rtrim($base_url, '/') . '/saigonhoreca/the-royal-sgh-10.webp';
                    $fallback_url = str_replace('http://', 'https://', $fallback_url);
                    $image[0] = esc_url($fallback_url);
                }
            }
            return $image;
        }

        protected static function enabled($feature) {
            return (bool) apply_filters("sgh_perf_{$feature}_enabled", true);
        }
    }
}

/* ============================================================================
 * 5.5. OPEN GRAPH & TWITTER CARD SOCIAL META
 * ============================================================================ */

if (!class_exists('SGH_SEO_Social')) {

    class SGH_SEO_Social {

        public static function init() {
            // Đăng ký hook wp_head để in social meta tự động
            add_action('wp_head', [__CLASS__, 'render_social_meta'], 10);
        }

        public static function render_social_meta() {
            if (is_admin() || is_feed()) return;

            $has_seo_plugin = class_exists('WPSEO_Frontend') || class_exists('RankMath');
            
            // Đọc slug cho singular template (project)
            $post_slug = '';
            if (is_singular('project')) {
                global $post;
                $post_slug = isset($post->post_name) ? $post->post_name : '';
            }

            $pi_seo = is_singular() && function_exists('sgh_pi_get_post_seo') ? sgh_pi_get_post_seo(get_queried_object_id()) : [];
            $has_pi_social = !empty($pi_seo['og_title']) || !empty($pi_seo['og_description']) || !empty($pi_seo['og_image'])
                || !empty($pi_seo['seo_title']) || !empty($pi_seo['seo_description']);

            if (!$has_seo_plugin || $has_pi_social) :
                // 1. OG Title
                $seo_title = '';
                if ($post_slug && function_exists('sgh_get_project_meta')) {
                    $seo_title = sgh_get_project_meta($post_slug, 'Title');
                }
                if (!$seo_title) {
                    $seo_title = !empty($pi_seo['og_title']) ? $pi_seo['og_title']
                        : (!empty($pi_seo['seo_title']) ? $pi_seo['seo_title']
                        : (is_singular() ? get_the_title() : get_bloginfo('name')));
                }

                // 2. OG Description
                $seo_desc = '';
                if ($post_slug && function_exists('sgh_get_project_meta')) {
                    $seo_desc = sgh_get_project_meta($post_slug, 'Description');
                }
                if (!$seo_desc) {
                    $seo_desc = !empty($pi_seo['og_description']) ? $pi_seo['og_description']
                        : (!empty($pi_seo['seo_description']) ? $pi_seo['seo_description']
                        : (class_exists('SGH_SEO_Meta_Description')
                        ? SGH_SEO_Meta_Description::build_for_current()
                        : (is_singular() && has_excerpt() ? strip_tags(get_the_excerpt()) : get_bloginfo('description'))));
                }

                $seo_url   = is_singular() ? get_permalink() : home_url('/');
                $seo_type  = is_singular('post') ? 'article' : 'website';

                // 3. OG Image
                $seo_img = '';
                if ($post_slug && function_exists('sgh_get_project_thumbnail')) {
                    $seo_img = sgh_get_project_thumbnail($post_slug);
                }
                if (!$seo_img) {
                    $seo_img = !empty($pi_seo['og_image']) ? $pi_seo['og_image']
                        : (is_singular() && has_post_thumbnail() ? get_the_post_thumbnail_url(null, 'large') : esc_url(get_template_directory_uri() . '/assets/images/og-default.jpg'));
                }
            ?>
                <meta property="og:title" content="<?php echo esc_attr($seo_title); ?>">
                <meta property="og:description" content="<?php echo esc_attr($seo_desc); ?>">
                <meta property="og:url" content="<?php echo esc_url($seo_url); ?>">
                <meta property="og:type" content="<?php echo esc_attr($seo_type); ?>">
                <meta property="og:image" content="<?php echo esc_url($seo_img); ?>">
                <meta property="og:site_name" content="<?php echo esc_attr(get_bloginfo('name')); ?>">
                
                <!-- Twitter Cards -->
                <meta name="twitter:card" content="summary_large_image">
                <meta name="twitter:title" content="<?php echo esc_attr($seo_title); ?>">
                <meta name="twitter:description" content="<?php echo esc_attr($seo_desc); ?>">
                <meta name="twitter:image" content="<?php echo esc_url($seo_img); ?>">
            <?php
            endif;
        }
    }
}

/* ============================================================================
 * 6. INITIALIZE SUB-SYSTEMS
 * ============================================================================ */

class SGH_SEO_Manager {
    public static function init() {
        SGH_SEO_Meta_Description::init();
        SGH_SEO_Robots::init();
        SGH_SEO_Schema::init();
        SGH_SEO_Robots_Txt::init();
        SGH_SEO_Performance::init();
        SGH_SEO_Social::init();
    }
}

SGH_SEO_Manager::init();
