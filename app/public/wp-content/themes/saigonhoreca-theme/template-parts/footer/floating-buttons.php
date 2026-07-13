<?php
/**
 * Template Part: Footer Floating Buttons (Back to Top, Phone, Zalo)
 *
 * Draggable floating action buttons with edge snap and persistent position.
 *
 * @var array $args Optional. May include a `contact` array.
 *
 * @package SaigonHoreca
 */

if (! defined('ABSPATH')) {
    exit;
}

$contact = $args['contact'] ?? (function_exists('saigonhouse_get_contact_info') ? saigonhouse_get_contact_info() : []);
$hotline_raw = $contact['hotline_raw'] ?? str_replace([' ', '.', ','], '', $contact['hotline'] ?? '');

// Nút bật/tắt AI Chatbot — CHỈ hiện khi plugin Pi Chatbot thật sự active.
// Panel chat + toàn bộ logic (đọc ngữ cảnh bài viết đang xem, gắn sự kiện click
// vào #sh-toggle) do plugin tự render qua wp_footer (xem footer.php) — theme chỉ
// cung cấp đúng markup theo "hợp đồng" ID mà CSS floating-buttons.css đã định
// sẵn từ trước (#sh-toggle / #sh-ic-chat / #sh-ic-x / #sh-badge).
if (!function_exists('is_plugin_active')) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
}
$sh_chatbot_active = is_plugin_active('pi-chatbot/pi-chatbot.php');
?>
<div id="sh-floating-buttons">
    <a href="#" id="back-to-top" aria-label="<?php esc_attr_e('Cuộn lên đầu trang', 'saigonhoreca'); ?>" role="button">
        <svg class="sh-scroll-ring" viewBox="0 0 36 36">
            <path class="sh-scroll-track" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
            <path id="scroll-progress-circle" class="sh-scroll-progress" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
        </svg>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary" style="position:relative; z-index:1;"><path d="m18 15-6-6-6 6"/></svg>
    </a>

    <?php if (! empty($contact['hotline'])) : ?>
    <a href="tel:<?php echo esc_attr($hotline_raw); ?>" class="sh-phone-float" aria-label="<?php echo esc_attr(sprintf(__('Gọi hotline %s', 'saigonhoreca'), $contact['hotline'])); ?>" data-gtm-click="click_phone" data-gtm-location="floating_button">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
    </a>
    <?php endif; ?>

    <?php if (! empty($contact['zalo'])) : ?>
    <a href="<?php echo esc_url($contact['zalo']); ?>" target="_blank" rel="noopener noreferrer" class="sh-zalo-btn" aria-label="<?php esc_attr_e('Chat qua Zalo', 'saigonhoreca'); ?>" data-gtm-click="click_zalo" data-gtm-location="floating_button">
        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/zalo-icon.webp" alt="<?php esc_attr_e('Chat Zalo', 'saigonhoreca'); ?>" class="sh-zalo-btn__img" loading="lazy" decoding="async" width="56" height="56">
    </a>
    <?php endif; ?>

    <?php if (! empty($contact['facebook'])) : ?>
    <a href="<?php echo esc_url($contact['facebook']); ?>" target="_blank" rel="noopener noreferrer" class="sh-fb-btn" aria-label="<?php esc_attr_e('Theo dõi Facebook', 'saigonhoreca'); ?>" data-gtm-click="click_facebook" data-gtm-location="floating_button">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
        </svg>
    </a>
    <?php endif; ?>

    <?php if ($sh_chatbot_active) : ?>
    <button type="button" id="sh-toggle" aria-label="<?php esc_attr_e('Mở trợ lý AI tư vấn', 'saigonhoreca'); ?>" data-gtm-click="click_chatbot" data-gtm-location="floating_button">
        <svg id="sh-ic-chat" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        <svg id="sh-ic-x" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
        <span id="sh-badge" aria-hidden="true"></span>
    </button>
    <?php endif; ?>
</div>
<?php // JS (back-to-top + kéo-thả) enqueue có điều kiện qua inc/engine/enqueue.php — assets/js/floating-buttons.js ?>
