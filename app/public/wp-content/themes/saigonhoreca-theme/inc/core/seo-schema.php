<?php
/**
 * Saigon Horeca JSON-LD Structured Data Schema Module
 *
 * Handles Structured Data / JSON-LD schema injection for LocalBusiness, WebSite, and Product.
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) exit;

/* ============================================================================
 * 1. STRUCTURED DATA (JSON-LD SCHEMA)
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

                // Single blog post: Article
                if (is_singular('post')) {
                    $g = self::article(get_post());
                    if ($g) $graphs[] = $g;
                }

                // Contact / About pages: ContactPage / AboutPage (linked to #organization)
                if (self::is_contact_page()) $graphs[] = self::contact_page();
                if (self::is_about_page())   $graphs[] = self::about_page();
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

        protected static function is_contact_page() {
            return is_page_template('page-templates/page-lien-he.php')
                || (function_exists('sgh_slug') && is_page(sgh_slug('contact')));
        }

        protected static function is_about_page() {
            return is_page_template('page-templates/page-gioi-thieu.php')
                || (function_exists('sgh_slug') && is_page(sgh_slug('about')));
        }

        protected static function current_url() {
            $path = isset($_SERVER['REQUEST_URI']) ? strtok($_SERVER['REQUEST_URI'], '?') : '/';
            return home_url($path);
        }

        protected static function lang() {
            return (function_exists('sgh_is_english_site') && sgh_is_english_site()) ? 'en' : 'vi-VN';
        }

        protected static function contact_page() {
            return array_filter([
                '@context'   => 'https://schema.org',
                '@type'      => 'ContactPage',
                '@id'        => self::current_url() . '#contactpage',
                'url'        => self::current_url(),
                'name'       => wp_get_document_title(),
                'about'      => ['@id' => home_url('/') . '#organization'],
                'inLanguage' => self::lang(),
            ]);
        }

        protected static function about_page() {
            return array_filter([
                '@context'   => 'https://schema.org',
                '@type'      => 'AboutPage',
                '@id'        => self::current_url() . '#aboutpage',
                'url'        => self::current_url(),
                'name'       => wp_get_document_title(),
                'about'      => ['@id' => home_url('/') . '#organization'],
                'inLanguage' => self::lang(),
            ]);
        }

        protected static function article($post) {
            if (!$post) return null;
            $img  = get_the_post_thumbnail_url($post, 'full');
            $home = home_url('/');
            $data = [
                '@context'      => 'https://schema.org',
                '@type'         => 'Article',
                '@id'           => get_permalink($post) . '#article',
                'headline'      => get_the_title($post),
                'url'           => get_permalink($post),
                'datePublished' => mysql2date('c', $post->post_date),
                'dateModified'  => mysql2date('c', $post->post_modified),
                'author'        => ['@type' => 'Person', 'name' => get_the_author_meta('display_name', $post->post_author)],
                'publisher'     => ['@id' => $home . '#organization'],
                'image'         => $img ?: null,
                'inLanguage'    => self::lang(),
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

// Khởi chạy ngay
SGH_SEO_Schema::init();
