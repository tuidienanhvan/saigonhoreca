<?php
/**
 * Project Pillar — the-brix
 * Section #1: hero
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-hero-brix">
    <div class="pp-hero-brix__media" style="background-image:url('<?php echo sgh_img('the-brix/the-brix-khong-gian-be-boi-ngoai-troi.jpg'); ?>');"></div>
    <div class="pp-hero-brix__overlay" aria-hidden="true"></div>

    <div class="pp-ambient-glow-brix pp-ambient-glow-brix--top-left" aria-hidden="true"></div>
    <div class="pp-ambient-glow-brix pp-ambient-glow-brix--bottom-right" aria-hidden="true"></div>

    <div class="pp-container-shared">
      <div class="pp-hero-brix__content">
        <div class="pp-hero-brix__badge">
          <?php echo esc_html__('Bistro nhiệt đới bên hồ bơi · Quận 2', 'saigonhoreca'); ?>
        </div>
        <h1 class="pp-hero-brix__title">
          <?php echo esc_html__('The Brix', 'saigonhoreca'); ?>
          <span class="pp-hero-brix__title-sub"><?php echo esc_html__('Ốc đảo nghỉ dưỡng giữa lòng thành phố', 'saigonhoreca'); ?></span>
        </h1>
        <p class="pp-hero-brix__subtitle"><?php echo esc_html__('Nơi thiên nhiên nhiệt đới hòa quyện cùng ẩm thực Âu tinh tế - một ốc đảo xanh mát bên hồ bơi, tách biệt khỏi nhịp sống đô thị ồn ào.', 'saigonhoreca'); ?></p>
        <div class="pp-hero-brix__divider" aria-hidden="true"></div>
      </div>
    </div>

    <div class="pp-hero-brix__scroll-indicator">
      <span class="pp-hero-brix__scroll-text"><?php echo esc_html__('Khám phá', 'saigonhoreca'); ?></span>
      <svg class="pp-hero-brix__scroll-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="18" height="18">
        <path d="M12 5v14M5 12l7 7 7-7" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </div>
</section>
