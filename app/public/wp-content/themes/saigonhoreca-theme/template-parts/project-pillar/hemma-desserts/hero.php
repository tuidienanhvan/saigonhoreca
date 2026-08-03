<?php
/**
 * Project Pillar — hemma-desserts
 * Section #1: hero
 * European patisserie nook — cozy, refined, warm pastel-gold mood.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-hero-hmd">
  <!-- Lớp nền gánh hiệu ứng parallax siêu mượt qua background-attachment:fixed (không dùng clip-path) -->
  <div class="pp-hero-hmd__bg" style="background-image:url('<?php echo sgh_img('hemma-desserts-mot-goc-nho-chau-au-giua-thao-dien/hemma-desserts-mot-goc-nho-chau-au-giua-thao-dien-thumbnail-project-cover.webp'); ?>');" aria-hidden="true"></div>
  <div class="pp-hero-hmd__overlay" aria-hidden="true"></div>
  <div class="pp-hero-hmd__glow" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <!-- Removed text content overlay as the background image already contains baked-in titles
<div class="pp-hero-hmd__content">
      <span class="pp-hero-hmd__eyebrow"><?php echo esc_html__('Patisserie Européenne · Thảo Điền', 'saigonhoreca'); ?></span>
      <h1 class="pp-hero-hmd__title">
        <?php echo esc_html__('Hemma Desserts', 'saigonhoreca'); ?>
        <span class="pp-hero-hmd__title-sub"><?php echo esc_html__('Một góc nhỏ châu Âu giữa Thảo Điền', 'saigonhoreca'); ?></span>
      </h1>
      <div class="pp-hero-hmd__divider" aria-hidden="true"></div>
      <p class="pp-hero-hmd__subtitle"><?php echo esc_html__('Ẩn mình ở cuối con đường Nguyễn Bá Huân, Hemma Desserts là một tiệm tráng miệng kiểu Âu nhỏ xinh được vận hành bởi hai founder trẻ người nước ngoài, cũng chính là đầu bếp đứng bếp bánh mỗi ngày.', 'saigonhoreca'); ?></p>
    </div>
-->
  </div>

  <div class="pp-hero-hmd__scroll" aria-hidden="true">
    <svg class="pp-hero-hmd__scroll-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="18" height="18">
      <path d="M12 5v14M5 12l7 7 7-7" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
  </div>
</section>
