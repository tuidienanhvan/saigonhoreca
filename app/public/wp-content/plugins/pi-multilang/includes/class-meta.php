<?php
if (!defined('ABSPATH')) exit;

class Pi_Multilang_Meta {
    public static function init() {
        add_action('add_meta_boxes', [__CLASS__, 'add_meta_boxes']);
        add_action('save_post', [__CLASS__, 'save_post']);
    }

    public static function add_meta_boxes() {
        $screens = ['post', 'page', 'product'];
        foreach ($screens as $screen) {
            add_meta_box(
                'pi_multilang_meta',
                'Pi Multilang',
                [__CLASS__, 'render_meta_box'],
                $screen,
                'side',
                'high'
            );
        }
    }

    public static function render_meta_box($post) {
        wp_nonce_field('pi_multilang_save', 'pi_multilang_nonce');
        $lang = get_post_meta($post->ID, 'pi_language', true) ?: 'vi';
        $group = get_post_meta($post->ID, 'pi_translation_group', true);
        if (!$group) {
            $group = wp_generate_uuid4();
        }
        
        echo '<p><label for="pi_language"><strong>Ngôn ngữ:</strong></label></p>';
        echo '<select id="pi_language" name="pi_language" style="width:100%;">';
        echo '<option value="vi" ' . selected($lang, 'vi', false) . '>Tiếng Việt (vi)</option>';
        echo '<option value="en" ' . selected($lang, 'en', false) . '>English (en)</option>';
        echo '</select>';

        echo '<p><label for="pi_translation_group"><strong>Translation Group (UUID):</strong></label></p>';
        echo '<input type="text" id="pi_translation_group" name="pi_translation_group" value="' . esc_attr($group) . '" style="width:100%;" readonly />';
        
        if ($lang === 'vi') {
            echo '<p><button type="button" id="pi-multilang-translate-btn" class="button button-primary" data-post-id="'.$post->ID.'">Dịch tự động sang EN (LLM7)</button></p>';
            echo '<div id="pi-multilang-translate-status" style="margin-top: 10px; font-weight: bold;"></div>';
            
            // Add script inline for simplicity of this implementation
            echo '<script>
            jQuery(document).ready(function($) {
                $("#pi-multilang-translate-btn").click(function(e) {
                    e.preventDefault();
                    var btn = $(this);
                    btn.prop("disabled", true).text("Đang dịch...");
                    $("#pi-multilang-translate-status").text("");
                    
                    $.ajax({
                        url: ajaxurl,
                        type: "POST",
                        data: {
                            action: "pi_multilang_translate",
                            post_id: btn.data("post-id"),
                            _ajax_nonce: "'.wp_create_nonce('pi_translate').'"
                        },
                        success: function(response) {
                            if (response.success) {
                                $("#pi-multilang-translate-status").css("color", "green").text("Đã dịch xong, bản Draft EN vừa được tạo!");
                            } else {
                                $("#pi-multilang-translate-status").css("color", "red").text("Lỗi: " + response.data);
                            }
                        },
                        error: function() {
                            $("#pi-multilang-translate-status").css("color", "red").text("Lỗi kết nối.");
                        },
                        complete: function() {
                            btn.prop("disabled", false).text("Dịch tự động sang EN (LLM7)");
                        }
                    });
                });
            });
            </script>';
        }
    }

    public static function save_post($post_id) {
        if (!isset($_POST['pi_multilang_nonce']) || !wp_verify_nonce($_POST['pi_multilang_nonce'], 'pi_multilang_save')) {
            return;
        }
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!current_user_can('edit_post', $post_id)) return;

        if (isset($_POST['pi_language'])) {
            update_post_meta($post_id, 'pi_language', sanitize_text_field($_POST['pi_language']));
        }
        if (isset($_POST['pi_translation_group'])) {
            update_post_meta($post_id, 'pi_translation_group', sanitize_text_field($_POST['pi_translation_group']));
        }
    }
}
