<?php
if (!defined('ABSPATH')) exit;

class Pi_Multilang_Frontend {
    public static function init() {
        add_action('pre_get_posts', [__CLASS__, 'filter_query']);
        add_shortcode('pi_lang_switcher', [__CLASS__, 'render_switcher']);
        add_filter('post_link', [__CLASS__, 'rewrite_url_domain'], 10, 2);
        add_filter('page_link', [__CLASS__, 'rewrite_url_domain'], 10, 2);
        add_filter('post_type_link', [__CLASS__, 'rewrite_url_domain'], 10, 2);
    }

    public static function filter_query($query) {
        if (is_admin() || !$query->is_main_query()) return;

        $meta_query = $query->get('meta_query');
        if (!is_array($meta_query)) $meta_query = [];

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

        if (empty($meta_query)) {
            $meta_query = $mq;
        } else {
            $meta_query = [
                'relation' => 'AND',
                $meta_query,
                $mq
            ];
        }

        $query->set('meta_query', $meta_query);
    }

    /**
     * Rewrites a permalink to the correct host/path prefix for the post's
     * OWN language. Delegates entirely to Pi_Multilang_Core — previously
     * this method had its own hardcoded en.saigonhoreca.* string-replace
     * logic, a SEPARATE copy of the domain scheme from class-core.php's,
     * which is exactly how it drifted out of sync across 2 rework rounds
     * (class-core.php got fixed to saigonhoreca.com/.vn, this method didn't,
     * because nothing forced them to share one source of truth). Now there
     * is only one place that knows about hosts/path prefixes.
     */
    public static function rewrite_url_domain($url, $post) {
        if (is_int($post)) $post = get_post($post);
        if (!$post) return $url;

        $lang = get_post_meta($post->ID, 'pi_language', true) ?: 'vi';
        return Pi_Multilang_Core::rewrite_url_for_language($url, $lang);
    }

    public static function render_switcher() {
        if (!is_singular()) return '';
        global $post;

        $group = get_post_meta($post->ID, 'pi_translation_group', true);
        if (!$group) return '';

        $posts = get_posts([
            'post_type' => get_post_type($post),
            'meta_key' => 'pi_translation_group',
            'meta_value' => $group,
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ]);

        $html = '<div class="pi-lang-switcher" style="display:flex;gap:10px;margin:10px 0;">';
        foreach ($posts as $p) {
            $lang = get_post_meta($p->ID, 'pi_language', true) ?: 'vi';
            $url = get_permalink($p->ID);
            $active = ($p->ID === $post->ID) ? ' font-weight:bold;text-decoration:underline;' : '';
            $html .= '<a href="'.esc_url($url).'" class="pi-lang-'.esc_attr($lang).'" style="'.$active.'">'.esc_html(strtoupper($lang)).'</a>';
        }
        $html .= '</div>';
        return $html;
    }
}
