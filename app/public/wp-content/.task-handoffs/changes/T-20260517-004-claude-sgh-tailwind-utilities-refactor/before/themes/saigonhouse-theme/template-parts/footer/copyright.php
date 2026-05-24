<?php
$contact = $args['contact'] ?? (function_exists('saigonhouse_get_contact_info') ? saigonhouse_get_contact_info() : []);
$company_name = $args['company_name'] ?? ($contact['company_name'] ?? get_bloginfo('name'));
$privacy_url = $args['privacy_url'] ?? home_url('/chinh-sach-bao-mat');
$terms_url = $args['terms_url'] ?? home_url('/dieu-khoan-su-dung');
?>
<div class="sh-footer__copyright" data-aos="fade-up" data-aos-delay="260">
    <p>&copy; <?php echo date('Y'); ?> <?php echo esc_html($company_name); ?>. All rights reserved.</p>
    <div class="sh-footer__copyright-links">
        <a href="<?php echo esc_url($privacy_url); ?>" class="sh-footer__copyright-link">Chính sách bảo mật</a>
        <a href="<?php echo esc_url($terms_url); ?>" class="sh-footer__copyright-link">Điều khoản sử dụng</a>
    </div>
</div>
