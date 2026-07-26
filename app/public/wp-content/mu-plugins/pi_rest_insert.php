<?php
/*
Plugin Name: PI Local REST Inserter
Description: Custom REST endpoint to insert posts.
*/

add_action('rest_api_init', function () {
    register_rest_route('pi/v1', '/insert-post', array(
        'methods' => 'POST',
        'callback' => 'pi_rest_insert_post_handler',
        'permission_callback' => '__return_true',
    ));
});

function pi_rest_insert_post_handler(WP_REST_Request $request) {
    $params = $request->get_json_params();

    $post_data = array(
        'post_title'   => $params['post_title'],
        'post_content' => $params['post_content'],
        'post_status'  => 'publish',
        'post_author'  => 1,
    );

    if (!empty($params['category'])) {
        require_once(ABSPATH . 'wp-admin/includes/taxonomy.php');
        $cat_id = wp_create_category($params['category']);
        $post_data['post_category'] = array($cat_id);
    }
    
    if (!empty($params['post_tag'])) {
        $post_data['tags_input'] = $params['post_tag'];
    }

    $post_id = wp_insert_post($post_data);

    if (is_wp_error($post_id)) {
        return new WP_REST_Response(array('error' => $post_id->get_error_message()), 500);
    }

    // Function to sideload local image
    $sideload_local_image = function($local_path, $post_id) {
        if (!file_exists($local_path)) return false;
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        
        $image_name = basename($local_path);
        $upload = wp_upload_bits($image_name, null, file_get_contents($local_path));
        if (!$upload['error']) {
            $attachment = array(
                'post_mime_type' => $upload['type'],
                'post_title'     => sanitize_file_name($image_name),
                'post_content'   => '',
                'post_status'    => 'inherit'
            );
            $attach_id = wp_insert_attachment($attachment, $upload['file'], $post_id);
            $attach_data = wp_generate_attachment_metadata($attach_id, $upload['file']);
            wp_update_attachment_metadata($attach_id, $attach_data);
            return $attach_id;
        }
        return false;
    };

    // Function to sideload remote URL
    $sideload_remote_image = function($url, $post_id) {
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        
        $image_name = basename(parse_url($url, PHP_URL_PATH));
        $upload = wp_upload_bits($image_name, null, file_get_contents($url));
        if (!$upload['error']) {
            $attachment = array(
                'post_mime_type' => $upload['type'],
                'post_title'     => sanitize_file_name($image_name),
                'post_content'   => '',
                'post_status'    => 'inherit'
            );
            $attach_id = wp_insert_attachment($attachment, $upload['file'], $post_id);
            $attach_data = wp_generate_attachment_metadata($attach_id, $upload['file']);
            wp_update_attachment_metadata($attach_id, $attach_data);
            return $attach_id;
        }
        return false;
    };

    // Set Feature Image
    if (!empty($params['feature_image_local_path'])) {
        $attach_id = $sideload_local_image($params['feature_image_local_path'], $post_id);
        if ($attach_id) {
            set_post_thumbnail($post_id, $attach_id);
        }
    } elseif (!empty($params['feature_image_url'])) {
        $attach_id = $sideload_remote_image($params['feature_image_url'], $post_id);
        if ($attach_id) {
            set_post_thumbnail($post_id, $attach_id);
        }
    }

    // Set SEO Meta
    if (!empty($params['seo_title'])) {
        update_post_meta($post_id, '_yoast_wpseo_title', $params['seo_title']);
        update_post_meta($post_id, 'rank_math_title', $params['seo_title']);
    }
    if (!empty($params['seo_desc'])) {
        update_post_meta($post_id, '_yoast_wpseo_metadesc', $params['seo_desc']);
        update_post_meta($post_id, 'rank_math_description', $params['seo_desc']);
    }

    return new WP_REST_Response(array('success' => true, 'post_id' => $post_id), 200);
}
