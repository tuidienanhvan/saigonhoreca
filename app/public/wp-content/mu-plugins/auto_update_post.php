<?php
/*
Plugin Name: Auto Update Post
Description: Automatically updates a specific post with clean HTML.
*/

add_action('init', 'auto_update_bep_mo_post_once');

function auto_update_bep_mo_post_once() {
    if (get_option('_has_updated_bep_mo_33988_v2')) {
        return;
    }

    $file_path = 'C:/Users/Administrator/Local Sites/saigonhoreca/clean_article.html';
    if (!file_exists($file_path)) {
        return;
    }

    $content = file_get_contents($file_path);

    $post_data = array(
        'ID'           => 33988,
        'post_content' => $content,
    );

    $post_id = wp_update_post($post_data);

    if (!is_wp_error($post_id) && $post_id != 0) {
        update_option('_has_updated_bep_mo_33988_v2', true);
        error_log("SUCCESS: Auto-updated post ID " . $post_id);
    }
}
