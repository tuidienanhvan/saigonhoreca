<?php
/**
 * Contact Page Section — Hero
 *
 * Dark luxe compact hero với BEM sh-contact-hero.
 * Giữ cùng pattern với archive-product hero compact.
 *
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri    = get_template_directory_uri();
$bg_url = sgh_img('2023/12/Saigon-Horeca-thiet-bi-bep-cong-nghiep.webp');
?>
<section class="sh-contact-hero" aria-label="<?php esc_attr_e('Liên hệ Saigon Horeca', 'saigonhoreca'); ?>">
    <div class="sh-contact-hero__bg" style="background-image:url('<?php echo esc_url($bg_url); ?>')"></div>
    <div class="sh-contact-hero__overlay"></div>
    <div class="sh-contact-hero__content">
        <span class="sh-contact-hero__badge"><?php esc_html_e('Liên hệ chúng tôi', 'saigonhoreca'); ?></span>
        <h1 class="sh-contact-hero__title"><?php esc_html_e('Liên hệ Saigon Horeca', 'saigonhoreca'); ?></h1>
        <p class="sh-contact-hero__sub">
            Chúng tôi luôn sẵn sàng lắng nghe và tư vấn giải pháp tối ưu nhất cho gian bếp của bạn.
        </p>
    </div>
</section>
