<?php
/**
 * Dashboard Sync: Data operations (DB→JSON, WebP convert, OG screenshots)
 *
 * Depends on: inc/core/image-optimizer.php (sh_convert_to_webp, sh_can_convert_webp)
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) exit;

/**
 * Sync WordPress DB to theme posts/data.json (schema v2).
 */
function sgh_dashboard_sync_db_to_json() {
    $log = ['--- SYNC DB to data.json (Schema v2) ---'];
    $posts_dir = get_template_directory() . '/posts';

    if (!is_dir($posts_dir)) {
        wp_mkdir_p($posts_dir);
    }

    $posts = get_posts([
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'no_found_rows'  => true,
    ]);

    $updated = 0;
    $created = 0;

    $seo_map = [
        '_pi_seo_title'         => 'seo_title',
        '_pi_seo_description'   => 'seo_description',
        '_pi_seo_focus_keyword' => 'focus_keyword',
        '_pi_og_image'          => 'og_image',
        '_pi_og_title'          => 'og_title',
        '_pi_og_description'    => 'og_description',
        '_pi_custom_author'     => 'custom_author',
        '_pi_custom_date'       => 'custom_date',
    ];

    foreach ($posts as $post) {
        $slug = $post->post_name;
        $dir  = $posts_dir . '/' . $slug;

        if (!is_dir($dir)) {
            wp_mkdir_p($dir);
            wp_mkdir_p($dir . '/images');
            $created++;
        } else {
            $updated++;
        }

        $data = [
            'schema_version' => 2,
            'title'          => $post->post_title,
            'slug'           => $slug,
            'date'           => $post->post_date,
            'excerpt'        => $post->post_excerpt,
            'categories'     => wp_get_post_categories($post->ID, ['fields' => 'slugs']),
            'tags'           => wp_get_post_tags($post->ID, ['fields' => 'names']),
        ];

        $thumb_id = get_post_thumbnail_id($post->ID);
        if ($thumb_id) {
            $thumb_path = get_attached_file($thumb_id);
            if ($thumb_path && file_exists($thumb_path)) {
                $data['thumbnail_url'] = 'images/' . basename($thumb_path);
            }
        }

        foreach ($seo_map as $meta_key => $json_key) {
            $val = get_post_meta($post->ID, $meta_key, true);
            if (!empty($val)) {
                $data[$json_key] = $val;
            }
        }

        if (function_exists('sgh_pi_get_post_seo')) {
            $bundle = sgh_pi_get_post_seo($post->ID);
            foreach (['seo_title', 'seo_description', 'og_title', 'og_description', 'og_image', 'focus_keyword'] as $key) {
                if (!empty($bundle[$key])) {
                    $data[$key] = $bundle[$key];
                }
            }
        }

        if (get_post_meta($post->ID, '_pi_seo_noindex', true) === '1') {
            $data['noindex'] = true;
        }

        $cats = get_the_category($post->ID);
        if (!empty($cats)) {
            $data['article_section'] = $cats[0]->name;
        }

        $image_urls = [];
        if (!empty($data['thumbnail_url'])) {
            $image_urls[] = $data['thumbnail_url'];
        }
        $data['image_urls'] = $image_urls;

        $json_path = $dir . '/data.json';
        file_put_contents($json_path, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    $total = count($posts);
    $log[] = "Tổng: {$total} posts processed";
    $log[] = "Created: {$created} new folders";
    $log[] = "Updated: {$updated} existing folders";

    return ['success' => true, 'message' => "Sync DB -> data.json: {$total} posts.", 'log' => $log];
}

/**
 * Convert all JPG/PNG attachments in Media Library to WebP.
 * Uses shared sh_convert_to_webp() from image-optimizer.php.
 */
function sgh_dashboard_convert_images_webp() {
    $log = ['--- CHUYỂN ẢNH SANG WEBP ---'];
    @set_time_limit(300);
    wp_raise_memory_limit('admin');

    if (!sh_can_convert_webp()) {
        $log[] = 'PHP GD extension hoặc imagewebp() không khả dụng.';
        return ['success' => false, 'message' => 'Server thiếu GD/WebP support.', 'log' => $log];
    }

    $attachments = get_posts([
        'post_type'      => 'attachment',
        'post_mime_type' => ['image/jpeg', 'image/png'],
        'post_status'    => 'inherit',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'no_found_rows'  => true,
    ]);

    $converted = 0;
    $skipped = 0;
    $failed = 0;
    $total = count($attachments);
    $log[] = "Tìm thấy {$total} ảnh JPG/PNG trong Media Library.";

    foreach ($attachments as $att_id) {
        // Reuse per-post converter (converts + deletes original + updates WP)
        if (function_exists('sgh_convert_single_attachment_webp')) {
            $result = sgh_convert_single_attachment_webp($att_id);
        } else {
            // Fallback if per-post handler not loaded
            $file = get_attached_file($att_id);
            if (!$file || !file_exists($file)) { $skipped++; continue; }
            $webp_file = preg_replace('/\.(jpe?g|png)$/i', '.webp', $file);
            if (file_exists($webp_file)) { $skipped++; continue; }
            $result = sh_convert_to_webp($file, $webp_file) ? 'converted' : 'failed';
        }

        if ($result === 'converted') $converted++;
        elseif ($result === 'skipped') $skipped++;
        else $failed++;

        if ($converted % 50 === 0 && $converted > 0) {
            wp_cache_flush();
        }
    }

    $log[] = "Đã convert: {$converted} ảnh (gốc JPG/PNG đã xóa)";
    $log[] = "Bỏ qua (đã là WebP): {$skipped}";
    if ($failed > 0) $log[] = "Lỗi: {$failed} ảnh";

    return [
        'success' => true,
        'message' => "Đã convert {$converted}/{$total} ảnh sang WebP.",
        'log' => $log,
    ];
}

/**
 * Auto-generate OG screenshots for all published posts/pages.
 * Uses Microlink API → converts to WebP → registers in Media Library.
 */
function sgh_dashboard_generate_og_screenshots() {
    $log = ['--- TẠO OG SCREENSHOT TỰ ĐỘNG ---'];
    @set_time_limit(600);

    $posts = get_posts([
        'post_type'      => ['post', 'page'],
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'no_found_rows'  => true,
    ]);

    $total = count($posts);
    $generated = 0;
    $skipped = 0;
    $failed = 0;

    $log[] = "Tìm thấy {$total} bài viết/trang.";

    $upload_dir = wp_upload_dir();
    $og_dir = $upload_dir['basedir'] . '/og-screenshots';
    if (!is_dir($og_dir)) {
        wp_mkdir_p($og_dir);
    }

    foreach ($posts as $pid) {
        $custom_og = get_post_meta($pid, '_pi_og_image', true);
        if (!empty($custom_og)) {
            $skipped++;
            continue;
        }

        $slug = get_post_field('post_name', $pid);
        $og_file = $og_dir . '/' . $slug . '.jpg';
        if (file_exists($og_file)) {
            $og_url = $upload_dir['baseurl'] . '/og-screenshots/' . $slug . '.jpg';
            update_post_meta($pid, '_pi_og_image', $og_url);
            $skipped++;
            continue;
        }

        $permalink = get_permalink($pid);
        if (empty($permalink) || strpos($permalink, 'localhost') !== false || strpos($permalink, '.local') !== false) {
            $skipped++;
            continue;
        }

        $api_url = 'https://api.microlink.io/?url=' . urlencode($permalink)
            . '&screenshot=true&meta=false&embed=screenshot.url'
            . '&viewport.width=1200&viewport.height=630'
            . '&waitForTimeout=3000';

        $response = wp_remote_get($api_url, [
            'timeout'   => 30,
            'sslverify' => (wp_get_environment_type() !== 'local'),
        ]);

        if (is_wp_error($response)) {
            $failed++;
            $log[] = "Lỗi: {$slug} - " . $response->get_error_message();
            continue;
        }

        $code = wp_remote_retrieve_response_code($response);
        $content_type = wp_remote_retrieve_header($response, 'content-type');

        if ($code === 200 && strpos($content_type, 'image') !== false) {
            $image_data = wp_remote_retrieve_body($response);
            $temp_file = $og_dir . '/' . $slug . '-temp.png';
            if (file_put_contents($temp_file, $image_data, LOCK_EX) === false) {
                $failed++;
                continue;
            }

            // Convert to WebP via shared helper
            $webp_file = $og_dir . '/' . $slug . '.webp';
            $converted = sh_convert_to_webp($temp_file, $webp_file);

            if (!$converted) {
                $final_file = $og_dir . '/' . $slug . '.jpg';
                rename($temp_file, $final_file);
                $final_mime = 'image/jpeg';
                $final_ext = 'jpg';
            } else {
                $final_file = $webp_file;
                $final_mime = 'image/webp';
                $final_ext = 'webp';
                if (file_exists($temp_file)) unlink($temp_file);
            }

            $attachment = [
                'post_title'     => 'OG Screenshot: ' . get_the_title($pid),
                'post_mime_type' => $final_mime,
                'post_status'    => 'inherit',
                'post_parent'    => $pid,
            ];
            $attach_id = wp_insert_attachment($attachment, $final_file, $pid);
            if (!is_wp_error($attach_id) && $attach_id > 0) {
                require_once ABSPATH . 'wp-admin/includes/image.php';
                $meta = wp_generate_attachment_metadata($attach_id, $final_file);
                wp_update_attachment_metadata($attach_id, $meta);

                $og_url = wp_get_attachment_url($attach_id);
                update_post_meta($pid, '_pi_og_image', $og_url);
                $generated++;
                $log[] = "Đã tạo: {$slug} ({$final_ext})";
            } else {
                $failed++;
            }
        } else {
            $failed++;
        }

        if ($generated % 10 === 0 && $generated > 0) {
            sleep(2);
        } else {
            usleep(500000);
        }
    }

    $log[] = "Đã tạo: {$generated} ảnh OG";
    $log[] = "Bỏ qua: {$skipped} (đã có OG image)";
    if ($failed > 0) $log[] = "Lỗi: {$failed}";

    return [
        'success' => true,
        'message' => "Đã tạo {$generated}/{$total} OG screenshots.",
        'log' => $log,
    ];
}
