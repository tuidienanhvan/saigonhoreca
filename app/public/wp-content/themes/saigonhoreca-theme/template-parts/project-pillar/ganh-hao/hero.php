<?php
/**
 * Project Pillar — ganh-hao
 * Section #1: hero
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
?>
<section class="pp-hero-ganh">
  <!-- Thẻ con gánh hiệu ứng Parallax Clip-Path siêu mượt (bg position:fixed bị clip-path của section "neo" lại) -->
  <div class="pp-hero-ganh__bg" style="background-image:url('<?php echo sgh_img('ganh-hao-noi-hon-bien-trong-tung-net-kien-truc/ganh-hao-mui-da-toan-canh-tu-tren-cao-mai-cong-luon-song.webp'); ?>');" aria-hidden="true"></div>
  <div class="pp-hero-ganh__overlay" aria-hidden="true"></div>

  <div class="pp-hero-ganh__glow pp-hero-ganh__glow--sea" aria-hidden="true"></div>
  <div class="pp-hero-ganh__glow pp-hero-ganh__glow--sand" aria-hidden="true"></div>

  <!-- Removed text content overlay as the background image already contains baked-in titles
<div class="pp-hero-ganh__content">
    <div class="pp-hero-ganh__eyebrow" aria-hidden="true"></div>
    <h1 class="pp-hero-ganh__title"><?php echo esc_html__('Gành Hào', 'saigonhoreca'); ?></h1>
    <p class="pp-hero-ganh__subtitle"><?php echo esc_html__('– nơi hồn biển hội tụ trong từng nét kiến trúc', 'saigonhoreca'); ?></p>
    <span class="pp-hero-ganh__divider" aria-hidden="true"></span>
  </div>
-->
</section>
