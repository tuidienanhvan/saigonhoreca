<?php
if (!defined('ABSPATH')) exit;

class Pi_Multilang_Admin {
    public static function init() {
        add_action('admin_menu', [__CLASS__, 'add_menu']);
    }

    public static function add_menu() {
        add_submenu_page(
            'edit.php',
            'Duyệt Bản Dịch',
            'Duyệt Bản Dịch',
            'edit_posts',
            'pi-multilang-drafts',
            [__CLASS__, 'render_page']
        );
    }

    public static function render_page() {
        echo '<div class="wrap">';
        echo '<h1>Duyệt Bản Dịch (Drafts)</h1>';
        
        $drafts = get_posts([
            'post_type' => 'any',
            'post_status' => 'draft',
            'meta_key' => 'pi_translation_group',
            'posts_per_page' => -1
        ]);
        
        if (empty($drafts)) {
            echo '<p>Không có bản dịch nào chờ duyệt.</p>';
        } else {
            echo '<table class="wp-list-table widefat fixed striped">';
            echo '<thead><tr><th>Tiêu đề (Draft)</th><th>Loại (Type)</th><th>Ngôn ngữ</th><th>Hành động</th></tr></thead>';
            echo '<tbody>';
            foreach ($drafts as $d) {
                $lang = get_post_meta($d->ID, 'pi_language', true);
                // Lọc những bài dịch sang EN
                if ($lang !== 'en') continue;

                $edit_link = get_edit_post_link($d->ID);
                echo '<tr>';
                echo '<td><strong><a href="'.esc_url($edit_link).'">'.esc_html($d->post_title).'</a></strong></td>';
                echo '<td>'.esc_html($d->post_type).'</td>';
                echo '<td>'.esc_html($lang).'</td>';
                echo '<td><a href="'.esc_url($edit_link).'" class="button">Xem / Sửa</a></td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
        }
        
        echo '</div>';
    }
}
