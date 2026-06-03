<?php
/**
 * Project Pillar â€” du-an-vinh-hiep
 * Section #1: hero
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-hero-vhp">
    <div class="pp-hero-vhp__media" style="background-image:url('<?php echo sgh_img('2025/01/sheh-fung-5.jpg'); ?>'); background-size:cover; background-position:center;"></div>
    <div class="pp-hero-vhp__overlay" aria-hidden="true"></div>
    <div class="pp-hero-vhp__content">
        <h1 class="pp-hero-vhp__title"><?php echo esc_html__('Dá»± Ã¡n VÄ©nh Hiá»‡p â€“ Coffee Lab', 'saigonhoreca'); ?></h1>
        <p class="pp-hero-vhp__subhead"><?php echo esc_html__('Khi khÃ´ng gian báº¿p vÃ  cÃ  phÃª cÃ¹ng ká»ƒ má»™t cÃ¢u chuyá»‡n vá» chuáº©n má»±c xuáº¥t kháº©u', 'saigonhoreca'); ?></p>
    </div>
</section>

