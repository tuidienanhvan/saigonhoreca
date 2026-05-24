<?php
/**
 * Front Page Custom Meta Boxes
 * Allows editing Front Page data directly in the Page Editor.
 */

// 0. Enqueue admin CSS
function saigonhouse_front_page_meta_admin_styles($hook) {
    if (!in_array($hook, ['post.php', 'post-new.php'], true)) {
        return;
    }
    global $post;
    if (!$post || $post->ID != get_option('page_on_front')) {
        return;
    }
    wp_enqueue_style(
        'saigonhouse-front-page-meta',
        get_template_directory_uri() . '/inc/admin/front-page-meta.css',
        [],
        filemtime(get_template_directory() . '/inc/admin/front-page-meta.css')
    );
}
add_action('admin_enqueue_scripts', 'saigonhouse_front_page_meta_admin_styles');

// 1. Register Meta Boxes
function saigonhouse_add_front_page_metaboxes() {
    global $post;
    
    // Only show on the Front Page (Page ID set in Reading Settings)
    if ($post->ID == get_option('page_on_front')) {
        add_meta_box(
            'sh_hero_section',
            'Hero / Slider Section Settings',
            'saigonhouse_render_hero_metabox',
            'page',
            'normal',
            'high'
        );

        add_meta_box(
            'sh_sections_titles',
            'Section Titles Settings',
            'saigonhouse_render_sections_metabox',
            'page',
            'normal',
            'high'
        );

        add_meta_box(
            'sh_villa_items',
            'Villa Grid Items (4 Highlighted)',
            'saigonhouse_render_villa_items_metabox',
            'page',
            'normal',
            'high'
        );

        add_meta_box(
            'sh_town_items',
            'Townhouse Grid Items (4 Highlighted)',
            'saigonhouse_render_town_items_metabox',
            'page',
            'normal',
            'high'
        );

        // Testimonial metabox removed — section no longer on front page
    }
}
add_action('add_meta_boxes', 'saigonhouse_add_front_page_metaboxes');

// 2. Render Hero Meta Box (3 Slides)
function saigonhouse_render_hero_metabox($post) {
    wp_nonce_field('saigonhouse_save_front_meta', 'saigonhouse_front_meta_nonce');
    ?>
    <p><strong>Nhập thông tin cho 3 Slides của Hero Carousel:</strong></p>
    
    <?php for ($i = 1; $i <= 3; $i++) : 
        $bg = get_post_meta($post->ID, "_pi_hero_{$i}_bg", true);
        $title = get_post_meta($post->ID, "_pi_hero_{$i}_title", true);
        $subtitle = get_post_meta($post->ID, "_pi_hero_{$i}_subtitle", true);
        $btn_text = get_post_meta($post->ID, "_pi_hero_{$i}_btn_text", true);
        $btn_link = get_post_meta($post->ID, "_pi_hero_{$i}_btn_link", true);
    ?>
    <div class="sh-slide-box">
        <h4>🖼️ Slide #<?php echo $i; ?></h4>
        
        <div class="sh-row">
            <label class="sh-label">Background Image URL:</label>
            <input type="text" name="sh_hero_<?php echo $i; ?>_bg" value="<?php echo esc_attr($bg); ?>" class="sh-input" placeholder="URL ảnh hoặc ID Media Library">
        </div>
        
        <div class="sh-row">
            <label class="sh-label">Tiêu đề chính (Title):</label>
            <input type="text" name="sh_hero_<?php echo $i; ?>_title" value="<?php echo esc_attr($title); ?>" class="sh-input" placeholder="UY TÍN TẠO NIỀM TIN">
        </div>
        
        <div class="sh-row">
            <label class="sh-label">Mô tả phụ (Subtitle):</label>
            <input type="text" name="sh_hero_<?php echo $i; ?>_subtitle" value="<?php echo esc_attr($subtitle); ?>" class="sh-input" placeholder="Hơn 15 năm kinh nghiệm...">
        </div>

        <div class="sh-row" style="border:none;">
            <label class="sh-label">Nút bấm:</label>
            <input type="text" name="sh_hero_<?php echo $i; ?>_btn_text" value="<?php echo esc_attr($btn_text); ?>" class="sh-input-half" placeholder="Text (e.g. Xem Chi Tiết)">
            <input type="text" name="sh_hero_<?php echo $i; ?>_btn_link" value="<?php echo esc_attr($btn_link); ?>" class="sh-input-half" placeholder="Link (e.g. /lien-he)">
        </div>
    </div>
    <?php endfor; ?>
    <?php
}

// 3. Render Sections Meta Box
function saigonhouse_render_sections_metabox($post) {
    $villa_title = get_post_meta($post->ID, '_pi_sec_villa_title', true);
    $villa_desc = get_post_meta($post->ID, '_pi_sec_villa_desc', true);
    $town_title = get_post_meta($post->ID, '_pi_sec_town_title', true);
    ?>
    <div class="sh-row">
        <label class="sh-label">Villa Section Title:</label>
        <input type="text" name="sh_sec_villa_title" value="<?php echo esc_attr($villa_title); ?>" class="sh-input">
        <label class="sh-label" style="margin-top:10px;">Villa Section Description:</label>
        <textarea name="sh_sec_villa_desc" class="sh-input" rows="3"><?php echo esc_textarea($villa_desc); ?></textarea>
    </div>

    <div class="sh-row" style="border:none;">
        <label class="sh-label">Townhouse Section Title:</label>
        <input type="text" name="sh_sec_town_title" value="<?php echo esc_attr($town_title); ?>" class="sh-input">
    </div>
    <?php
}

// 3.1 Render Villa Items
function saigonhouse_render_villa_items_metabox($post) {
    echo '<p>Enter details for the 4 highlighted villas.</p>';
    for ($i = 1; $i <= 4; $i++) {
        $name = get_post_meta($post->ID, "_sh_villa_{$i}_name", true);
        $img = get_post_meta($post->ID, "_sh_villa_{$i}_img", true);
        $link = get_post_meta($post->ID, "_sh_villa_{$i}_link", true);
        echo "<div class='sh-row'>
            <strong>Villa #$i</strong><br>
            <label class='sh-label'>Name:</label>
            <input type='text' name='sh_villa_{$i}_name' value='" . esc_attr($name) . "' class='sh-input' placeholder='e.g. BT-MODERN'>
            <label class='sh-label'>Image URL:</label>
            <input type='text' name='sh_villa_{$i}_img' value='" . esc_attr($img) . "' class='sh-input' placeholder='https://...'>
            <label class='sh-label'>Post Link (URL):</label>
            <input type='text' name='sh_villa_{$i}_link' value='" . esc_attr($link) . "' class='sh-input' placeholder='Copy link bài viết dán vào đây...'>
        </div>";
    }
}

// 3.2 Render Townhouse Items
function saigonhouse_render_town_items_metabox($post) {
    echo '<p>Enter details for the 4 highlighted townhouses.</p>';
    for ($i = 1; $i <= 4; $i++) {
        $name = get_post_meta($post->ID, "_sh_town_{$i}_name", true);
        $img = get_post_meta($post->ID, "_sh_town_{$i}_img", true);
        $link = get_post_meta($post->ID, "_sh_town_{$i}_link", true);
        echo "<div class='sh-row'>
            <strong>Townhouse #$i</strong><br>
            <label class='sh-label'>Name:</label>
            <input type='text' name='sh_town_{$i}_name' value='" . esc_attr($name) . "' class='sh-input' placeholder='e.g. NP-LUX'>
            <label class='sh-label'>Image URL:</label>
            <input type='text' name='sh_town_{$i}_img' value='" . esc_attr($img) . "' class='sh-input' placeholder='https://...'>
            <label class='sh-label'>Post Link (URL):</label>
            <input type='text' name='sh_town_{$i}_link' value='" . esc_attr($link) . "' class='sh-input' placeholder='Copy link bài viết dán vào đây...'>
        </div>";
    }
}

// 3.4 Render Testimonial Media
function saigonhouse_render_testi_metabox($post) {
    $video = get_post_meta($post->ID, '_pi_testi_video', true);
    ?>
    <div class="sh-row" style="border:none;">
        <label class="sh-label">Youtube Embed URL:</label>
        <input type="text" name="sh_testi_video" value="<?php echo esc_attr($video); ?>" class="sh-input" placeholder="https://www.youtube.com/embed/...">
    </div>
    <?php
}

// 4. Save Data
function saigonhouse_save_front_meta($post_id) {
    if (!isset($_POST['saigonhouse_front_meta_nonce']) || !wp_verify_nonce($_POST['saigonhouse_front_meta_nonce'], 'saigonhouse_save_front_meta')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_page', $post_id)) return;

    // Base Fields
    $fields = [
        'sh_sec_villa_title', 'sh_sec_villa_desc', 'sh_sec_town_title', 'sh_sec_testi_title',
        'sh_testi_video'
    ];

    // Add Hero Slide Fields (1-3)
    for ($i = 1; $i <= 3; $i++) {
        $fields[] = "sh_hero_{$i}_bg";
        $fields[] = "sh_hero_{$i}_title";
        $fields[] = "sh_hero_{$i}_subtitle";
        $fields[] = "sh_hero_{$i}_btn_text";
        $fields[] = "sh_hero_{$i}_btn_link";
    }

    // Add Dynamic Fields (1-4)
    for ($i = 1; $i <= 4; $i++) {
        $fields[] = "sh_villa_{$i}_name";
        $fields[] = "sh_villa_{$i}_img";
        $fields[] = "sh_villa_{$i}_link";
        $fields[] = "sh_town_{$i}_name";
        $fields[] = "sh_town_{$i}_img";
        $fields[] = "sh_town_{$i}_link";
    }

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
        }
    }
}
add_action('save_post', 'saigonhouse_save_front_meta');
