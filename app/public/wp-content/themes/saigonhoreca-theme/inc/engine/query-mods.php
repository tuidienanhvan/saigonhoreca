<?php
/**
 * Query modifications — posts with featured images appear first in
 * archives; standard archive page size.
 *
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;

add_filter('posts_join', function ($join, $query) {
    if (is_admin() || !$query->is_main_query()) return $join;
    if ($query->is_home() || $query->is_archive() || $query->is_tag() || $query->is_category()) {
        global $wpdb;
        if (strpos($join, 'AS mt_thumb') === false) {
            $join .= " LEFT JOIN {$wpdb->postmeta} AS mt_thumb ON ({$wpdb->posts}.ID = mt_thumb.post_id AND mt_thumb.meta_key = '_thumbnail_id') ";
        }
    }
    return $join;
}, 10, 2);

add_filter('posts_orderby', function ($orderby, $query) {
    if (is_admin() || !$query->is_main_query()) return $orderby;
    if ($query->is_home() || $query->is_archive() || $query->is_tag() || $query->is_category()) {
        global $wpdb;
        $orderby = "IF(mt_thumb.meta_value IS NOT NULL, 1, 0) DESC, {$wpdb->posts}.post_date DESC";
    }
    return $orderby;
}, 10, 2);

add_action('pre_get_posts', function ($query) {
    if (is_admin() || !$query->is_main_query()) return;
    if ($query->is_archive() || $query->is_category() || $query->is_tag() || $query->is_search() || $query->is_home()) {
        $query->set('posts_per_page', 9);
    }

    // Nâng cấp Search Frontend bằng lõi Pi Chatbot
    if ($query->is_search() && class_exists('Pi_Chatbot_Retriever')) {
        $s = $query->get('s');
        $results = Pi_Chatbot_Retriever::search($s, 50); // Lấy top 50 kết quả tốt nhất
        
        if (!empty($results)) {
            $post_ids = array_column($results, 'id');
            if (!empty($post_ids)) {
                $query->set('post__in', $post_ids);
                $query->set('orderby', 'post__in'); // Giữ đúng thứ tự xếp hạng thông minh của Chatbot
                $query->set('pi_smart_search', true);
            }
        }
    }
});

// Vô hiệu hóa câu lệnh LIKE %s% mặc định của WordPress nếu Pi Chatbot đã có kết quả
add_filter('posts_search', function($search, $query) {
    if (!is_admin() && $query->is_main_query() && $query->is_search() && $query->get('pi_smart_search')) {
        return '';
    }
    return $search;
}, 10, 2);
