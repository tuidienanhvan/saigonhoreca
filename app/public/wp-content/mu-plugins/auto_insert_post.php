<?php
/*
Plugin Name: Auto Insert Post
Description: Automatically inserts a markdown file as a post.
*/

add_action('init', 'auto_insert_markdown_post_once');

function auto_insert_markdown_post_once() {
    if (get_option('_has_inserted_bep_mo')) {
        return;
    }

    $file_path = 'C:/Users/Administrator/Local Sites/saigonhoreca/bai_viet_bep_mo.md';
    if (!file_exists($file_path)) {
        return;
    }

    $content = file_get_contents($file_path);
    $content = nl2br($content);

    $post_data = array(
        'post_title'    => 'Bếp mở (Open Kitchen) - Xu hướng thiết kế nhà hàng 2026',
        'post_content'  => $content,
        'post_status'   => 'publish',
        'post_author'   => 1,
        'post_type'     => 'post'
    );

    $post_id = wp_insert_post($post_data);

    if (!is_wp_error($post_id)) {
        update_option('_has_inserted_bep_mo', true);
        error_log("SUCCESS: Auto-inserted post ID " . $post_id);
    }
}
