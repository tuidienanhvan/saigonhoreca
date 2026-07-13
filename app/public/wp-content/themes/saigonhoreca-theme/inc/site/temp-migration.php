<?php
/**
 * Temporary Migration Helper for Viện dưỡng lão Nam An (former du-nam-an-an)
 * This will automatically update the database post name and title on production.
 *
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;

add_action('init', function() {
    global $wpdb;
    
    // 1. Rename du-nam-an-an project title and slug
    $post_id = $wpdb->get_var($wpdb->prepare(
        "SELECT ID FROM {$wpdb->posts} WHERE (post_name = %s OR post_title = %s) AND post_type = %s LIMIT 1",
        'du-nam-an-an', 'Dự Nam An An', 'project'
    ));
    if ($post_id) {
        $wpdb->update(
            $wpdb->posts,
            [
                'post_title' => 'Viện dưỡng lão Nam An',
                'post_name'  => 'vien-duong-lao-nam-an',
                'guid'       => home_url('/du-an/vien-duong-lao-nam-an/')
            ],
            ['ID' => $post_id]
        );
        delete_transient('sgh_theme_project_list');
        delete_transient('sgh_project_thumb_' . $post_id);
    }

    // 2. Automate Grand Marble cover image alignment
    $grand_marble_id = $wpdb->get_var($wpdb->prepare(
        "SELECT ID FROM {$wpdb->posts} WHERE post_name = %s AND post_type = %s LIMIT 1",
        'grand-marble', 'project'
    ));
    if ($grand_marble_id) {
        $cover_att_id = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_wp_attached_file' AND meta_value LIKE %s LIMIT 1",
            '%grand-marble-thuong-hieu-banh-cao-cap-nhat-ban-thumbnail-project-cover%'
        ));
        if ($cover_att_id > 0) {
            $current_thumb = (int) get_post_meta($grand_marble_id, '_thumbnail_id', true);
            if ($current_thumb !== $cover_att_id) {
                update_post_meta($grand_marble_id, '_thumbnail_id', $cover_att_id);
                delete_transient('sgh_project_thumb_' . $grand_marble_id);
            }
        }
    }
});
