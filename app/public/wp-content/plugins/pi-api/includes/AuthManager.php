<?php
namespace PiApi;

if (!defined('ABSPATH')) {
    exit;
}

class AuthManager {
    const APP_PASS_NAME = 'Pi_Dashboard';

    public static function init(): void {
        // Chỉ chạy trong admin để tránh ảnh hưởng hiệu năng frontend
        if (is_admin()) {
            add_action('admin_init', [self::class, 'ensureAppPasswordSynced']);
        }
    }

    public static function ensureAppPasswordSynced(): void {
        // Tránh chạy quá nhiều lần
        if (get_transient('pi_api_auth_sync_check')) {
            return;
        }
        set_transient('pi_api_auth_sync_check', 1, HOUR_IN_SECONDS);

        // Chỉ tự tạo + gửi credential SAU KHI site owner đã chủ động nhập
        // license key (hành động rõ ràng, coi như đồng ý kết nối Pi Dashboard).
        // TRƯỚC ĐÂY: chạy vô điều kiện cho BẤT KỲ admin nào mở wp-admin, kể cả
        // khi chưa từng cấu hình license — tự động cấp 1 credential toàn quyền
        // REST API và gửi cleartext ra backend mà không có bước xác nhận nào.
        if (!class_exists(Settings::class) || Settings::getLicenseKey() === '') {
            return;
        }

        $user_id = self::getAdminUserId();
        if (!$user_id) {
            return;
        }

        // Kiểm tra xem đã có App Pass của Pi chưa
        $passwords = \WP_Application_Passwords::get_user_application_passwords($user_id);
        $exists = false;
        foreach ($passwords as $p) {
            if ($p['name'] === self::APP_PASS_NAME) {
                $exists = true;
                break;
            }
        }

        if (!$exists) {
            self::createAndSyncAppPassword($user_id);
        }
    }

    private static function getAdminUserId(): int {
        $admins = get_users(['role' => 'administrator', 'number' => 1]);
        return !empty($admins) ? $admins[0]->ID : 0;
    }

    private static function createAndSyncAppPassword(int $user_id): void {
        $user = get_userdata($user_id);
        if (!$user) return;

        // ⚠️ 2026-08-03 — SỬA FATAL: "Call to undefined function
        // wp_create_application_password()".
        //
        // `wp_create_application_password` KHÔNG phải hàm của WordPress — nó là
        // tên ACTION HOOK (do_action ở wp-includes/class-wp-application-passwords.php:150).
        // Bản cũ nhầm tên hook thành tên hàm, rồi require wp-admin/includes/user.php
        // để "nạp" nó — file đó cũng không chứa hàm nào tên vậy, nên require xong
        // vẫn undefined và fatal ngay tại đây. Log ghi nhận lỗi này 3 lần
        // (03/08 05:42, 03/08 11:47, 04/08 02:16), đều từ admin-ajax.php ->
        // admin_init, tức mỗi lần vào wp-admin mà chưa có App Password.
        //
        // API đúng là phương thức tĩnh của cùng class mà hàm ensureAppPasswordSynced()
        // phía trên ĐANG dùng (::get_user_application_passwords). Trả về đúng
        // shape [$cleartext_password, $new_item] mà đoạn dưới mong đợi.
        if (!class_exists('\\WP_Application_Passwords')) {
            require_once ABSPATH . WPINC . '/class-wp-application-passwords.php';
        }

        $created = \WP_Application_Passwords::create_new_application_password($user_id, [
            'name' => self::APP_PASS_NAME,
        ]);

        if (is_wp_error($created)) {
            error_log('[pi-api] Không tạo được Application Password: ' . $created->get_error_message());
            return;
        }

        list($new_password, $new_item) = $created;

        if ($new_password) {
            // Gửi về Backend ngay lập tức
            $email = Settings::getLicenseEmail() ?: $user->user_email;
            BackendClient::registerSiteCredentials($email, $new_password);
            
            // Lưu vết để không gửi lại trừ khi cần thiết
            update_option('pi_api_app_pass_synced_at', current_time('mysql'));
        }
    }
}
