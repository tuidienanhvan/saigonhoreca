<?php
/**
 * Project Pillar â€” du-an-kdl-rung-thong-nui-voi-cua-saigonhoreca
 * Section #1: hero
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-hero-rtn">
    <div class="pp-hero-rtn__media" style="background-image:url('<?php echo sgh_img('2024/08/lienkhuong.png'); ?>'); background-size:cover; background-position:center;"></div>
    <div class="pp-hero-rtn__overlay" aria-hidden="true"></div>
    <div class="pp-hero-rtn__content">
        <h1 class="pp-hero-rtn__title"><?php echo esc_html__('KDL Rá»ªNG THÃ”NG NÃšI VOI', 'saigonhoreca'); ?></h1>
        <p class="pp-hero-rtn__subhead"><?php echo esc_html__('ThiÃªn NhiÃªn HÃ¹ng VÄ© - Tráº£i Nghiá»‡m Tuyá»‡t Vá»i', 'saigonhoreca'); ?></p>
        <p class="pp-hero-rtn__subtitle"><?php echo esc_html__('Saigon Horeca vÃ  hÃ nh trÃ¬nh kiáº¿n táº¡o áº©m thá»±c TÃ¢y NguyÃªn tinh táº¿ táº¡i', 'saigonhoreca'); ?></p>
    </div>
</section>

