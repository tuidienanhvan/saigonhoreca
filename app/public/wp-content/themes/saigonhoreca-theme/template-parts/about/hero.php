<?php
/**
 * About Page Section — Hero
 *
 * Dark luxe hero với BEM class sh-about-hero.
 * Ken-burns bg image + gold accent badge.
 *
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
$bg  = sgh_img('2023/12/bia-bep-cong-nghiep-inox.webp');
?>
<section class="sh-about-hero" aria-label="<?php esc_attr_e('Về Saigon Horeca — hero', 'saigonhoreca'); ?>">
    <div class="sh-about-hero__bg" style="background-image:url('<?php echo esc_url($bg); ?>')"></div>
    <div class="sh-about-hero__overlay"></div>

    <div class="sh-about-hero__content">
        <span class="sh-about-hero__badge"><?php esc_html_e('Về chúng tôi', 'saigonhoreca'); ?></span>
        <h1 class="sh-about-hero__title"><?php esc_html_e('Về Saigon Horeca', 'saigonhoreca'); ?></h1>
        <p class="sh-about-hero__sub">
            <?php esc_html_e('Giải pháp thiết bị bếp công nghiệp và quầy bar chuyên nghiệp hàng đầu tại Việt Nam.', 'saigonhoreca'); ?>
        </p>
        <a class="sh-about-hero__play"
           href="https://www.youtube.com/watch?v=JrvIMIpnddI&list=UUccc8NgtPrRALepBncnvS4A"
target="_blank" rel="noopener"
            aria-label="<?php esc_attr_e('Xem video giới thiệu Saigon Horeca', 'saigonhoreca'); ?>">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg>
        </a>
    </div>
</section>
