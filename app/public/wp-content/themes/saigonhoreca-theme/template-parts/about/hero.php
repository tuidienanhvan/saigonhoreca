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
$bg  = sgh_img('saigonhoreca/bia-bep-cong-nghiep-inox.webp');
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
            <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
              <defs>
                <linearGradient id="grad-play-gold" x1="0%" y1="0%" x2="100%" y2="100%">
                  <stop offset="0%" stop-color="#F5A623" />
                  <stop offset="100%" stop-color="#ffd166" />
                </linearGradient>
                <filter id="play-glow" x="-50%" y="-50%" width="200%" height="200%">
                  <feGaussianBlur stdDeviation="3" result="blur" />
                  <feComposite in="SourceGraphic" in2="blur" operator="over" />
                </filter>
              </defs>
              <circle class="play-ring-outer" cx="50" cy="50" r="45" stroke="url(#grad-play-gold)" stroke-width="1.2" stroke-dasharray="4 6" stroke-opacity="0.4"/>
              <circle cx="50" cy="50" r="39" stroke="url(#grad-play-gold)" stroke-width="1.2" stroke-opacity="0.2"/>
              <path d="M42 35.5V64.5C42 66.2 43.8 67.2 45.3 66.2L68.5 51.7C69.8 50.9 69.8 49.1 68.5 48.3L45.3 33.8C43.8 32.8 42 33.8 42 35.5Z" fill="url(#grad-play-gold)" filter="url(#play-glow)"/>
            </svg>
        </a>
    </div>
</section>
