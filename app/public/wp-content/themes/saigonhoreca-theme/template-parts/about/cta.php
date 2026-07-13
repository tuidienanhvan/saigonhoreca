<?php
/**
 * About Page Section — CTA Final + Extra Testimonials
 *
 * Dark luxe: 3-col testimonial grid + full-width CTA banner with bg image.
 * BEM: sh-about-cta
 *
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri    = get_template_directory_uri();
$bg_url = sgh_img('saigonhoreca/Saigon-Horeca-thiet-bi-bep-cong-nghiep.jpg');
?>
<section class="sh-about-cta" aria-label="<?php esc_attr_e('CTA và đánh giá bổ sung', 'saigonhoreca'); ?>">
    <div class="sh-about-cta__inner">
        <!-- CTA Banner -->
        <div class="sh-about-cta__banner" style="background-image:url('<?php echo esc_url($bg_url); ?>')">
            <div class="sh-about-cta__banner-overlay"></div>
            <div class="sh-about-cta__banner-content">
                <h2 class="sh-about-cta__banner-title"><?php esc_html_e('Đồng hành cùng Saigon Horeca', 'saigonhoreca'); ?></h2>
                <p class="sh-about-cta__banner-sub">
                    Kiến tạo không gian ẩm thực, bếp ăn công nghiệp và quầy bar chuyên nghiệp — tối ưu công năng hàng đầu.
                </p>
                <div class="sh-about-cta__banner-actions">
                    <a class="sh-about-cta__btn sh-about-cta__btn--primary"
                       href="<?php echo esc_url(sgh_url('contact')); ?>">
                        <?php esc_html_e('Liên hệ tư vấn', 'saigonhoreca'); ?>
                    </a>
                    <a class="sh-about-cta__btn sh-about-cta__btn--outline"
                       href="tel:+84901304365">
                        Hotline: 0901.304.365
                    </a>
                </div>
            </div>
        </div>

    </div>
</section>
