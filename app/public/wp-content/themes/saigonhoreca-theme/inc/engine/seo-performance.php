<?php
/**
 * Saigon Horeca SEO Performance & Security Module
 *
 * Handles performance optimizations (loading/decoding attributes, resource hints, stripping bloat),
 * HTTPS rewrites, fallbacks for 404 local media resources, and security headers.
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) exit;

/* ============================================================================
 * 1. PERFORMANCE, RESOURCE HINTS, HTTPS REWRITE, AND 404 IMAGE FALLBACKS
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

            // 6. Setup output buffering rewrite (HTTP to HTTPS, 404 images)
            add_action('template_redirect',           [__CLASS__, 'buffer_output_setup'], 999);

            // 7. Fallback 404 attachment images
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
            if (empty($attr['loading'])) {
                // The featured image of a singular post is the likely LCP — never lazy it.
                $is_featured = is_singular() && $attachment
                    && (int) $attachment->ID === (int) get_post_thumbnail_id();
                if ($is_featured) {
                    $attr['loading'] = 'eager';
                    if (empty($attr['fetchpriority'])) $attr['fetchpriority'] = 'high';
                } else {
                    $attr['loading'] = 'lazy';
                }
            }

            // Auto alt: prefer the attachment's OWN alt, then its title, then the post title.
            if (empty($attr['alt'])) {
                $alt = $attachment ? (string) get_post_meta($attachment->ID, '_wp_attachment_image_alt', true) : '';
                if ($alt === '' && $attachment) $alt = (string) get_the_title($attachment->ID);
                if ($alt === '') $alt = (string) get_the_title();
                $attr['alt'] = wp_strip_all_tags($alt);
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

            // Ảnh trong nội dung thiếu file local → nối về ẢNH THẬT trên production.
            // Giữ nguyên ảnh nào CÓ sẵn local (vd ảnh project mới); chỉ thay ảnh THIẾU.
            $base_dir = wp_get_upload_dir()['basedir'];
            $buffer = preg_replace_callback(
                '#https?://saigonhoreca\.local/wp-content/uploads/([^\s\'"<>)]+?\.(?:jpe?g|png|webp|gif))#i',
                static function ($m) use ($base_dir) {
                    $rel = $m[1];
                    if (file_exists(wp_normalize_path($base_dir . '/' . $rel))) return $m[0];
                    $orig = preg_match('/^(.+)-\d+x\d+\.(\w+)$/', $rel, $mm) ? $mm[1] . '.' . $mm[2] : $rel;
                    $orig = preg_replace('/\.(jpe?g|png)$/i', '.webp', $orig);
                    return SGH_PROD_UPLOADS_BASE . '/' . $orig;
                },
                $buffer
            );

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
                        $orig = preg_match('/^(.+)-\d+x\d+\.(jpg|jpeg|png|webp|gif)$/i', $relative_path, $mm)
                            ? $mm[1] . '.' . $mm[2] : $relative_path;
                        $orig = preg_replace('/\.(jpe?g|png)$/i', '.webp', $orig);
                        $fallback_url = esc_url(SGH_PROD_UPLOADS_BASE . '/' . ltrim($orig, '/'));

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
                    $rel_webp = preg_replace('/\.(jpe?g|png)$/i', '.webp', ltrim($image_meta['file'], '/'));
                    $fallback_url = esc_url(SGH_PROD_UPLOADS_BASE . '/' . $rel_webp);
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
                    // Nối về ẢNH THẬT trên production, ưu tiên bản .webp (nhẹ).
                    $rel_webp = preg_replace('/\.(jpe?g|png)$/i', '.webp', ltrim($original_relative, '/'));
                    $image[0] = esc_url(SGH_PROD_UPLOADS_BASE . '/' . $rel_webp);
                }
            }
            return $image;
        }

        protected static function enabled($feature) {
            return (bool) apply_filters("sgh_perf_{$feature}_enabled", true);
        }
    }
}

// Khởi chạy ngay
SGH_SEO_Performance::init();
