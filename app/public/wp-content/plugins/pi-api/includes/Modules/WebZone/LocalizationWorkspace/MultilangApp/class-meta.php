<?php
if (!defined('ABSPATH')) exit;

/**
 * Metabox chọn ngôn ngữ cho bài.
 *
 * Bất biến quan trọng, đọc trước khi sửa: bài KHÔNG có meta `pi_language` là
 * nội dung DÙNG CHUNG cho mọi ngôn ngữ — Pi_Multilang_Frontend::filter_query()
 * cho nó hiện ở mọi ngôn ngữ, còn bài đã gắn thẻ thì chỉ hiện ở đúng ngôn ngữ
 * của nó. Nên "chưa gắn thẻ" là một TRẠNG THÁI THẬT, không phải dữ liệu thiếu.
 *
 * Bản cũ mặc định select về 'vi' khi meta rỗng rồi ghi thẳng giá trị đó mỗi
 * lần lưu, nên chỉ cần mở một trang bất kỳ và bấm Cập nhật là nó bị gắn cứng
 * tiếng Việt và biến mất khỏi /en/ và /ja/ — biên tập viên không hề chọn gì.
 * Vì vậy ở đây ô trống là một lựa chọn hiển thị rõ ràng, và lưu ô trống nghĩa
 * là XOÁ meta chứ không phải ghi mặc định.
 */
class Pi_Multilang_Meta {

    public static function init() {
        add_action('add_meta_boxes', [__CLASS__, 'add_meta_boxes']);
        add_action('save_post', [__CLASS__, 'save_post']);
    }

    /**
     * Mọi post type công khai, KHÔNG phải danh sách chép tay.
     *
     * Bản cũ ghi cứng ['post','page','product'] nên các CPT của theme
     * (du-an, san-pham — chính plugin này liệt kê chúng trong
     * Pi_Multilang_Languages_Admin::reserved_prefixes()) không có cách nào gắn
     * ngôn ngữ, dù filter_query vẫn lọc chúng theo pi_language.
     */
    private static function target_post_types() {
        $types = get_post_types(['public' => true], 'names');
        unset($types['attachment']);
        return array_values($types);
    }

    public static function add_meta_boxes() {
        foreach (self::target_post_types() as $screen) {
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

    /** Ngôn ngữ hợp lệ = ngôn ngữ có trong cấu hình, cộng giá trị đang lưu. */
    private static function language_options($current) {
        $config = class_exists('Pi_Multilang_Core') ? Pi_Multilang_Core::get_language_config() : [];

        $out = [];
        foreach ($config as $code => $c) {
            $label = isset($c['label']) && $c['label'] !== '' ? $c['label'] : strtoupper($code);
            $out[$code] = $label . ' (' . $code . ')';
        }

        // Giá trị đang lưu mà không còn trong config (đổi cấu hình, copy DB từ
        // môi trường khác) vẫn phải hiện — nếu không, mở bài ra bấm lưu là mất
        // thẻ ngôn ngữ một cách âm thầm, đúng loại lỗi mà bản cũ mắc.
        if ($current !== '' && !isset($out[$current])) {
            $out[$current] = $current . ' (không còn trong cấu hình)';
        }
        return $out;
    }

    public static function render_meta_box($post) {
        wp_nonce_field('pi_multilang_save', 'pi_multilang_nonce');

        $lang  = (string) get_post_meta($post->ID, 'pi_language', true);
        $group = get_post_meta($post->ID, 'pi_translation_group', true);
        if (!$group) {
            $group = wp_generate_uuid4();
        }

        echo '<p><label for="pi_language"><strong>Ngôn ngữ:</strong></label></p>';
        echo '<select id="pi_language" name="pi_language" style="width:100%;">';
        printf(
            '<option value=""%s>Dùng chung (hiện ở mọi ngôn ngữ)</option>',
            selected($lang, '', false)
        );
        foreach (self::language_options($lang) as $code => $label) {
            printf(
                '<option value="%s"%s>%s</option>',
                esc_attr($code), selected($lang, $code, false), esc_html($label)
            );
        }
        echo '</select>';
        echo '<p class="description">Chọn một ngôn ngữ nghĩa là bài này CHỈ hiện ở ngôn ngữ đó. '
           . 'Để "Dùng chung" nếu đây là nội dung không có bản dịch riêng.</p>';

        echo '<p><label for="pi_translation_group"><strong>Translation Group (UUID):</strong></label></p>';
        echo '<input type="text" id="pi_translation_group" name="pi_translation_group" value="' . esc_attr($group) . '" style="width:100%;" readonly />';

        // Nút dịch chỉ có nghĩa khi bài đã khai báo ngôn ngữ nguồn và KHÔNG
        // phải chính tiếng Anh. Bài "dùng chung" (thẻ rỗng) mà đem dịch sẽ sinh
        // bản EN song song trong khi bản gốc vẫn hiện ở /en/ — hai bài cùng nội
        // dung trên cùng một ngôn ngữ.
        $config = class_exists('Pi_Multilang_Core') ? Pi_Multilang_Core::get_language_config() : [];
        if ($lang !== '' && $lang !== 'en' && isset($config['en'])) {
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
            $lang = sanitize_text_field(wp_unslash($_POST['pi_language']));

            // Ô trống = "dùng chung". Phải XOÁ meta, không được ghi chuỗi rỗng:
            // filter_query dùng compare NOT EXISTS, nên meta rỗng vẫn tính là
            // ĐÃ gắn thẻ và bài sẽ biến mất khỏi mọi ngôn ngữ.
            if ($lang === '') {
                delete_post_meta($post_id, 'pi_language');
            } elseif (array_key_exists($lang, self::language_options($lang))) {
                update_post_meta($post_id, 'pi_language', $lang);
            }
        }
        if (isset($_POST['pi_translation_group'])) {
            update_post_meta($post_id, 'pi_translation_group', sanitize_text_field(wp_unslash($_POST['pi_translation_group'])));
        }
    }
}
