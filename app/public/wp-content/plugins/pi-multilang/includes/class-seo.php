<?php
if (!defined('ABSPATH')) exit;

class Pi_Multilang_SEO {
    public static function init() {
        add_action('wp_head', [__CLASS__, 'inject_tags'], 1);
        
        // WP Core Sitemaps
        add_filter('wp_sitemaps_posts_query_args', [__CLASS__, 'filter_sitemap_query'], 10, 2);
        
        // Remove native WP canonical to prevent conflicts, we output our own
        remove_action('wp_head', 'rel_canonical');
        
        // Phân tách cache sitemap thật của theme theo ngôn ngữ
        add_filter('pre_transient_sh_sitemap_xml', [__CLASS__, 'read_sitemap_cache']);
        add_action('set_transient_sh_sitemap_xml', [__CLASS__, 'write_sitemap_cache'], 10, 3);
        add_filter('pre_transient_sh_sitemap_xml_gz', [__CLASS__, 'read_sitemap_cache_gz']);
        add_action('set_transient_sh_sitemap_xml_gz', [__CLASS__, 'write_sitemap_cache_gz'], 10, 3);
    }

    public static function inject_tags() {
        if (is_singular()) {
            global $post;
            
            // Explicitly output Canonical Tag exactly for the current domain
            $canonical_url = get_permalink($post->ID);
            echo '<link rel="canonical" href="' . esc_url($canonical_url) . '">' . "\n";
            
            // Hreflang
            $group = get_post_meta($post->ID, 'pi_translation_group', true);
            if ($group) {
                $posts = get_posts([
                    'post_type' => get_post_type($post),
                    'meta_key' => 'pi_translation_group',
                    'meta_value' => $group,
                    'posts_per_page' => -1,
                    'post_status' => 'publish'
                ]);
                
                foreach ($posts as $p) {
                    $lang = get_post_meta($p->ID, 'pi_language', true) ?: 'vi';
                    $url = get_permalink($p->ID);
                    echo '<link rel="alternate" hreflang="'.esc_attr($lang).'" href="'.esc_url($url).'" />'."\n";
                    if ($lang === 'en') {
                        // Theo yêu cầu: .com/en làm mặc định quốc tế x-default
                        echo '<link rel="alternate" hreflang="x-default" href="'.esc_url($url).'" />'."\n";
                    }
                }
            }
        }
    }

    public static function filter_sitemap_query($args, $post_type = '') {
        if (PI_CURRENT_LANGUAGE === 'vi') {
            $mq = [
                'relation' => 'OR',
                ['key' => 'pi_language', 'value' => 'vi', 'compare' => '='],
                ['key' => 'pi_language', 'compare' => 'NOT EXISTS']
            ];
        } else {
            $mq = [
                ['key' => 'pi_language', 'value' => PI_CURRENT_LANGUAGE, 'compare' => '=']
            ];
        }
        
        if (empty($args['meta_query'])) {
            $args['meta_query'] = $mq;
        } else {
            $args['meta_query'] = [
                'relation' => 'AND',
                $args['meta_query'],
                $mq
            ];
        }
        return $args;
    }

    // --- XỬ LÝ CACHE SITEMAP THEO NGÔN NGỮ ---

    public static function read_sitemap_cache($pre) {
        if (defined('PI_MULTILANG_BYPASS_SITEMAP_CACHE_READ')) return $pre;
        define('PI_MULTILANG_BYPASS_SITEMAP_CACHE_READ', true);
        $val = get_transient('sh_sitemap_xml_' . PI_CURRENT_LANGUAGE);
        return $val === false ? false : $val; // Nếu false, WP sẽ gọi generator
    }

    public static function write_sitemap_cache($value, $expiration, $transient) {
        if (defined('PI_MULTILANG_BYPASS_SITEMAP_CACHE_WRITE')) return;
        define('PI_MULTILANG_BYPASS_SITEMAP_CACHE_WRITE', true);
        
        set_transient('sh_sitemap_xml_' . PI_CURRENT_LANGUAGE, $value, $expiration);
        // Ngay sau khi core set sh_sitemap_xml, ta xoá nó đi để tránh dùng chung
        delete_option('_transient_sh_sitemap_xml');
        delete_option('_transient_timeout_sh_sitemap_xml');
    }

    public static function read_sitemap_cache_gz($pre) {
        if (defined('PI_MULTILANG_BYPASS_SITEMAP_CACHE_READ_GZ')) return $pre;
        define('PI_MULTILANG_BYPASS_SITEMAP_CACHE_READ_GZ', true);
        $val = get_transient('sh_sitemap_xml_gz_' . PI_CURRENT_LANGUAGE);
        return $val === false ? false : $val;
    }

    public static function write_sitemap_cache_gz($value, $expiration, $transient) {
        if (defined('PI_MULTILANG_BYPASS_SITEMAP_CACHE_WRITE_GZ')) return;
        define('PI_MULTILANG_BYPASS_SITEMAP_CACHE_WRITE_GZ', true);
        
        set_transient('sh_sitemap_xml_gz_' . PI_CURRENT_LANGUAGE, $value, $expiration);
        delete_option('_transient_sh_sitemap_xml_gz');
        delete_option('_transient_timeout_sh_sitemap_xml_gz');
    }
}
