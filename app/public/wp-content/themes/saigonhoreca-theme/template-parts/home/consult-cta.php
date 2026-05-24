<?php
/**
 * Home — Consult CTA (parallax banner).
 *
 * Match production saigonhoreca.vn: nền ảnh bếp công nghiệp dùng
 * `background-attachment: fixed` (parallax) — khi scroll thấy ảnh
 * "ẩn" sau khung cố định. Overlay đen mờ + text trắng centered +
 * 1 nút vàng "Đặt lịch hẹn".
 *
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
$bg  = sgh_img('2025/05/the-royal-sgh-8.jpg');
?>
<section class="sh-consult-cta" style="background-image:url('<?php echo esc_url($bg); ?>');">
    <div class="sh-consult-cta__overlay" aria-hidden="true"></div>
    <div class="sh-consult-cta__inner">
        <span class="sh-consult-cta__divider" aria-hidden="true"></span>
        <h2 class="sh-consult-cta__title">
            <?php esc_html_e('Tư vấn giải pháp hệ thống bếp công nghiệp', 'saigonhoreca'); ?>
        </h2>
        <p class="sh-consult-cta__desc">
            <?php esc_html_e('Chúng tôi tự tin mang đến dịch vụ tư vấn giải pháp tối ưu, cung cấp thiết bị bếp công nghiệp cho các quầy bar và nhà bếp công nghiệp trong ngành công nghiệp dịch vụ lưu trú như khách sạn, khu nghỉ dưỡng và nhà hàng.', 'saigonhoreca'); ?>
        </p>
        <a href="<?php echo esc_url(sgh_url('contact')); ?>" class="sh-consult-cta__btn">
            <?php esc_html_e('Đặt lịch hẹn', 'saigonhoreca'); ?>
        </a>
    </div>
</section>
