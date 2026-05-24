<?php
/**
 * Admin Notifications
 *
 * Hiện thông báo quan trọng trên WP Admin:
 * - Lead mới chưa xử lý
 * - DB cần optimize (revisions > 50)
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) exit;

add_action('admin_notices', function () {
    if (!current_user_can('manage_options')) return;

    // 1. New leads notification
    if (function_exists('sh_get_lead_counts')) {
        $counts = sh_get_lead_counts();
        if ($counts['new'] > 0) {
            $url = admin_url('admin.php?page=pi-leads&lead_status=new');
            echo '<div class="notice notice-info is-dismissible"><p>';
            echo '<strong>SaigonHoreca:</strong> Có <strong>' . $counts['new'] . '</strong> khách hàng mới chưa liên hệ. ';
            echo '<a href="' . esc_url($url) . '">Xem ngay &rarr;</a>';
            echo '</p></div>';
        }
    }

    // 2. DB optimization reminder (check once per day)
    $last_check = get_transient('sh_db_check_notice');
    if ($last_check === false) {
        global $wpdb;
        $revisions = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type='revision'");
        $trash = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_status='trash'");

        if ($revisions > 50 || $trash > 20) {
            $url = admin_url('admin.php?page=sgh-dashboard&tab=system');
            echo '<div class="notice notice-warning is-dismissible"><p>';
            echo '<strong>SaigonHoreca:</strong> Database có ' . $revisions . ' revisions, ' . $trash . ' posts rác. ';
            echo '<a href="' . esc_url($url) . '">Tối ưu ngay &rarr;</a>';
            echo '</p></div>';
        }

        set_transient('sh_db_check_notice', '1', DAY_IN_SECONDS);
    }
});
