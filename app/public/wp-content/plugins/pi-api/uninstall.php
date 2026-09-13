<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Revoke any 'Pi_Dashboard' Application Password AuthManager::createAndSyncAppPassword()
// minted (see includes/AuthManager.php) — otherwise it stays valid, granting
// full REST API access as that admin, indefinitely after the plugin is removed.
if (class_exists('WP_Application_Passwords')) {
    foreach (get_users(['role' => 'administrator']) as $admin) {
        $passwords = \WP_Application_Passwords::get_user_application_passwords($admin->ID);
        foreach ($passwords as $p) {
            if (($p['name'] ?? '') === 'Pi_Dashboard' && !empty($p['uuid'])) {
                \WP_Application_Passwords::delete_application_password($admin->ID, $p['uuid']);
            }
        }
    }
}

$key = (string) get_option('pi_api_license_key', '');
if ($key !== '' && defined('PI_API_BACKEND_URL')) {
    wp_remote_post(trailingslashit(PI_API_BACKEND_URL) . 'auth/deactivate', [
        'timeout' => 5,
        'headers' => ['Content-Type' => 'application/json'],
        'body' => wp_json_encode(['license_key' => $key]),
    ]);
}

// Cron: dọn luôn, kể cả khi giữ dữ liệu — hook của plugin đã gỡ chỉ còn là lỗi chờ nổ.
foreach ([
    'pi_api_heartbeat',
    'pi_api_report_usage',
    'pi_backup_run_job',
    'pi_perf_webp_batch_tick',
    'pi_email_campaigns_dispatch',   // Pi_Api_EmailCampaigns::CRON_HOOK
    'pi_system_health_snapshot',     // Pi_Api_System::HEALTH_CRON_HOOK
] as $hook) {
    wp_clear_scheduled_hook($hook);
}

/*
 * 2026-09-13: trước đây file này chỉ xoá 15 option gõ tay và KHÔNG drop bảng nào. Gỡ
 * plugin để lại 21 bảng wp_pi_*, trong đó có dữ liệu cá nhân và bí mật:
 *   pi_api_keys (key_encrypted), pi_webhook_deliveries (request_headers chứa
 *   Authorization), pi_form_submissions (toàn bộ field khách nhập + ip + user_agent),
 *   pi_analytics_* (ip_hash, session_hash, user_id), pi_login_history, pi_audit_log.
 * Cùng hàng chục option còn sót, gồm khoá LLM (sh_ai_openai_key…) và token webhook
 * (pi_webhook_telegram_bot_token…).
 *
 * Site đang chuyển nhà mà cần gỡ tạm plugin thì đặt trong wp-config.php:
 *     define('PI_API_KEEP_DATA', true);
 */
if (defined('PI_API_KEEP_DATA') && PI_API_KEEP_DATA) {
    return;
}

/*
 * Chỉ các bảng pi-api tự CREATE — liệt kê TƯỜNG MINH, không bao giờ drop theo mẫu
 * `wp_pi_%`: tiền tố đó dùng chung với plugin khác (pi-chatbot có 6 bảng
 * wp_pi_chatbot_*). Danh sách đo 13/09/2026 bằng cách giải từng câu CREATE TABLE trong
 * includes/, và grep toàn wp-content xác nhận không file nào NGOÀI pi-api chạm tới chúng.
 *
 * CỐ Ý KHÔNG có: wp_pi_leads, wp_pi_analytics (số ít), wp_sh_chat_logs, wp_sh_analytics —
 * pi-api chỉ ĐỌC các bảng này, không tạo; chủ sở hữu chưa xác định.
 *
 * Thêm bảng mới vào plugin thì PHẢI thêm tên vào đây.
 */
$pi_api_tables = [
    'pi_ai_cloud_transactions',
    'pi_analytics_events',
    'pi_analytics_pageviews',
    'pi_analytics_sessions',
    'pi_api_keys',
    'pi_audit_log',
    'pi_backup_schedules',
    'pi_backups',
    'pi_cron_logs',
    'pi_email_campaigns',
    'pi_email_templates',
    'pi_form_submissions',
    'pi_forms',
    'pi_indexing_queue',
    'pi_lead_activities',
    'pi_login_history',
    'pi_multilang_slug_batches',
    'pi_multilang_slugs',
    'pi_og_templates',
    'pi_saved_queries',
    'pi_webhook_deliveries',
];

/*
 * Xoá option theo TIỀN TỐ chứ không theo danh sách gõ tay: nhiều tên sinh động theo
 * provider (get_option('sh_ai_' . $pid . '_key') ở class-ai-cloud.php), nên danh sách
 * cứng không bao giờ đủ.
 *
 * Mỗi tiền tố dưới đây đã grep toàn bộ plugin + 2 theme ngày 13/09: CHỈ pi-api đọc/ghi.
 * CỐ Ý loại `sgh_`: cả saigonhoreca-theme lẫn saigonhouse-theme đều dùng tiền tố đó
 * (sgh_redirects, sgh_cloudflare_account_id…) — xoá theo mẫu sẽ phá cấu hình theme, dù
 * pi-api cũng ghi vài option sgh_*.
 */
$pi_api_option_prefixes = [
    'pi_api_',
    'pi_license_',
    'pi_ai_',
    'pi_leads_',
    'pi_webhook_',
    'pi_og_',
    'sh_ai_',
];

$pi_api_uninstall_site = static function () use ($pi_api_tables, $pi_api_option_prefixes) {
    global $wpdb;

    foreach ($pi_api_tables as $t) {
        // Tên bảng lấy từ danh sách hằng ở trên, không từ input — nối chuỗi an toàn.
        $wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}{$t}`");
    }

    // Transient của chính plugin nằm trong bảng options với tiền tố _transient_(timeout_).
    $prefixes = $pi_api_option_prefixes;
    foreach (['pi_api_', 'pi_seo_gsc_oauth_state_'] as $p) {
        $prefixes[] = '_transient_' . $p;
        $prefixes[] = '_transient_timeout_' . $p;
    }

    foreach ($prefixes as $prefix) {
        // esc_like escape cả `_`, vốn là ký tự đại diện trong LIKE — không escape thì
        // 'pi_ai_' khớp luôn 'piXaiY…' của plugin khác.
        $wpdb->query($wpdb->prepare(
            "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
            $wpdb->esc_like($prefix) . '%'
        ));
    }
};

if (is_multisite()) {
    foreach (get_sites(['fields' => 'ids', 'number' => 0]) as $blog_id) {
        switch_to_blog($blog_id);
        $pi_api_uninstall_site();
        restore_current_blog();
    }
} else {
    $pi_api_uninstall_site();
}

// usermeta dùng chung một bảng cho cả network nên chỉ chạy một lần.
foreach (['pi_2fa_secret', 'pi_2fa_recovery', 'pi_last_login', 'pi_user_status'] as $meta_key) {
    delete_metadata('user', 0, $meta_key, '', true);
}

wp_cache_flush();
