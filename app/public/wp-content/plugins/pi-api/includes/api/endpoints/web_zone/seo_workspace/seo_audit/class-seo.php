<?php
/**
 * Pi API SEO Endpoints
 */

if (!defined('ABSPATH')) exit;

class Pi_Api_Seo {

    public static function register_routes($ns, $admin) {
        register_rest_route($ns, '/seo/audit', [
            'methods'  => 'GET',
            'callback' => [self::class, 'get_audit'],
            'permission_callback' => $admin
        ]);
        register_rest_route($ns, '/seo/audit/update', [
            'methods'  => 'POST',
            'callback' => [self::class, 'update_post_seo'],
            'permission_callback' => $admin
        ]);
        register_rest_route($ns, '/seo/settings', [
            ['methods' => 'GET',  'callback' => [self::class, 'get_settings'],    'permission_callback' => $admin],
            ['methods' => 'POST', 'callback' => [self::class, 'update_settings'], 'permission_callback' => $admin],
        ]);
    }

    public static function get_audit(\WP_REST_Request $request) {
        $type = $request->get_param('type') ?: 'post';
        $page = max(1, (int) ($request->get_param('page') ?: 1));
        $per_page = max(1, (int) ($request->get_param('per_page') ?: 25));
        $search = $request->get_param('search');
        $status = $request->get_param('status');
        
        $query_args = [
            'post_type'      => $type,
            'post_status'    => 'publish',
            'posts_per_page' => $per_page,
            'paged'          => $page,
        ];
        if (!empty($search)) {
            $query_args['s'] = $search;
        }

        $query = new \WP_Query($query_args);

        $items = [];
        $total_score_sum = 0;

        $home_url = home_url();
        $domain = parse_url($home_url, PHP_URL_HOST);

        foreach ($query->posts as $p) {
            // Read SEO Title
            $seo_title = get_post_meta($p->ID, '_pi_seo_title', true)
                ?: get_post_meta($p->ID, '_rank_math_title', true)
                ?: get_post_meta($p->ID, '_yoast_wpseo_title', true)
                ?: get_post_meta($p->ID, '_pi_og_title', true)
                ?: '';

            // Read SEO Desc
            $seo_desc = get_post_meta($p->ID, '_pi_seo_description', true)
                ?: get_post_meta($p->ID, '_pi_seo_desc', true)
                ?: get_post_meta($p->ID, '_rank_math_description', true)
                ?: get_post_meta($p->ID, '_yoast_wpseo_metadesc', true)
                ?: get_post_meta($p->ID, '_pi_og_description', true)
                ?: '';

            $has_thumb = has_post_thumbnail($p->ID);
            $url = get_permalink($p->ID);

            // Internal links calculation
            $internal_links = 0;
            if (!empty($p->post_content)) {
                if (preg_match_all('/href=["\']([^"\']+)["\']/i', $p->post_content, $matches)) {
                    foreach ($matches[1] as $link) {
                        if (($domain && strpos($link, $domain) !== false) || (strpos($link, '/') === 0 && strpos($link, '//') !== 0)) {
                            $internal_links++;
                        }
                    }
                }
            }

            // Calculate Score
            $saved_score = (int) get_post_meta($p->ID, '_pi_seo_score', true);
            if ($saved_score > 0) {
                $score = $saved_score;
            } else {
                $score = 0;
                // Title score: max 30
                if (!empty($seo_title)) {
                    $score += 30;
                } elseif (!empty($p->post_title) && mb_strlen($p->post_title) >= 10) {
                    $score += 20;
                }

                // Desc score: max 30
                if (!empty($seo_desc)) {
                    $score += 30;
                } elseif (!empty($p->post_excerpt)) {
                    $score += 15;
                }

                // Thumb score: max 20
                if ($has_thumb) {
                    $score += 20;
                }

                // Internal links score: max 20
                if ($internal_links >= 2) {
                    $score += 20;
                } elseif ($internal_links == 1) {
                    $score += 10;
                }
            }

            $item = [
                'id'             => $p->ID,
                'title'          => $p->post_title,
                'url'            => $url ?: '',
                'seo_title'      => $seo_title,
                'seo_desc'       => $seo_desc,
                'has_thumb'      => (bool) $has_thumb,
                'internal_links' => (int) $internal_links,
                'score'          => (int) min(100, $score),
            ];

            // Filter by status if set
            if ($status === 'issues' && $score >= 60 && !empty($seo_title) && !empty($seo_desc)) {
                continue;
            }
            if ($status === 'good' && ($score < 60 || empty($seo_title) || empty($seo_desc))) {
                continue;
            }

            $total_score_sum += $item['score'];
            $items[] = $item;
        }

        $avg_score = count($items) > 0 ? (int) round($total_score_sum / count($items)) : 0;

        return new \WP_REST_Response([
            'items'     => $items,
            'total'     => (int) $query->found_posts,
            'avg_score' => $avg_score,
        ], 200);
    }

    public static function get_settings() {
        return new \WP_REST_Response([
            'ga4_id' => get_option('sgh_ga4_id', ''),
            'gtm_id' => get_option('sgh_gtm_id', ''),
            'fb_app_id' => get_option('sgh_fb_app_id', ''),
        ], 200);
    }

    public static function update_settings(\WP_REST_Request $request) {
        $p = $request->get_json_params();
        if (isset($p['ga4_id'])) update_option('sgh_ga4_id', sanitize_text_field($p['ga4_id']));
        if (isset($p['gtm_id'])) update_option('sgh_gtm_id', sanitize_text_field($p['gtm_id']));
        
        return new \WP_REST_Response(['success' => true], 200);
    }

    public static function update_post_seo(\WP_REST_Request $request) {
        $p = $request->get_json_params();
        $post_id = (int) ($p['post_id'] ?? 0);
        if (!$post_id || !get_post($post_id)) {
            return new \WP_REST_Response(['error' => 'Invalid post ID'], 400);
        }

        if (isset($p['seo_title'])) {
            update_post_meta($post_id, '_pi_seo_title', sanitize_text_field($p['seo_title']));
        }
        if (isset($p['seo_desc'])) {
            update_post_meta($post_id, '_pi_seo_description', sanitize_textarea_field($p['seo_desc']));
        }

        return new \WP_REST_Response(['success' => true], 200);
    }
}
