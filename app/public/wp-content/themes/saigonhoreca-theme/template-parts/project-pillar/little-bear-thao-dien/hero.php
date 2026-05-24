<?php
/**
 * Project Pillar — little-bear-thao-dien
 * Section #1: hero
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
$video_id = 'nC3dS_Yvch8';
$origin = rawurlencode(home_url('/'));
$embed_url = sprintf(
    'https://www.youtube-nocookie.com/embed/%1$s?autoplay=1&mute=1&loop=1&playlist=%1$s&controls=0&modestbranding=1&iv_load_policy=3&playsinline=1&disablekb=1&fs=0&rel=0&cc_load_policy=0&color=white&hl=vi&autohide=1&enablejsapi=0&origin=%2$s',
    $video_id,
    $origin
);
?>
<section class="pp-hero-lbt">
    <div class="pp-hero-lbt__media">
        <iframe src="<?php echo esc_url($embed_url); ?>" allow="autoplay; encrypted-media; fullscreen" frameborder="0" title="Background video" tabindex="-1" aria-hidden="true"></iframe>
    </div>
    <div class="pp-hero-lbt__overlay" aria-hidden="true"></div>
    <div class="pp-hero-lbt__content">
        <h1 class="pp-hero-lbt__title"><?php echo esc_html__('LITTLE BEAR', 'saigonhoreca'); ?></h1>
        <p class="pp-hero-lbt__subhead"><?php echo esc_html__('Vibrant vibe - Tasty food - Dynamic wine', 'saigonhoreca'); ?></p>
        <p class="pp-hero-lbt__subtitle"><?php echo esc_html__('Little Bear mời bạn bước vào một thiên đường ẩm thực, nơi những viên gạch ngả nắng, gia vị thơm ngát và tiếng cười hòa quyện thành một bản giao hưởng ấm cúng của hương vị tuyệt vời.', 'saigonhoreca'); ?></p>
    </div>
</section>
