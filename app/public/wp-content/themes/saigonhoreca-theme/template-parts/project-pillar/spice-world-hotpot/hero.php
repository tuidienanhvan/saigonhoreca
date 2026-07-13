<?php
/**
 * Project Pillar — spice-world-hotpot
 * Section #1: hero — full-bleed background image with Parallax Clip-Path.
 * Technique: section clip-path:inset(0) confines a position:fixed __bg = GPU parallax (no JS).
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-hero-swh">
  <!-- Thẻ con gánh hiệu ứng Parallax Clip-Path siêu mượt (position:fixed bị clip-path của section giữ lại) -->
  <div class="pp-hero-swh__bg" style="background-image:url('<?php echo esc_url(sgh_img('spice-world-hotpot/spice-world-hotpot-thumbnail-project-cover.webp')); ?>');" aria-hidden="true"></div>
  <div class="pp-hero-swh__overlay" aria-hidden="true"></div>

  <!-- Hơi lẩu bốc lên & ánh lửa Tứ Xuyên (gentle steam + Sichuan fire glow) -->
  <div class="pp-hero-swh__ember pp-hero-swh__ember--left" aria-hidden="true"></div>
  <div class="pp-hero-swh__ember pp-hero-swh__ember--right" aria-hidden="true"></div>
  <div class="pp-hero-swh__steam" aria-hidden="true">
    <span></span><span></span><span></span>
  </div>

  <!-- Removed text content overlay as the background image already contains baked-in titles
<div class="pp-hero-swh__content">
    <div class="pp-hero-swh__icon" aria-hidden="true">
      <svg viewBox="0 0 24 24" fill="currentColor">
        <path d="M12 2L14.85 9.15L22 12L14.85 14.85L12 22L9.15 14.85L2 12L9.15 9.15L12 2Z"/>
      </svg>
    </div>

    <div class="pp-hero-swh__subtitle">Sichuan &bull; Mala Hot Pot &bull; Est. 2003</div>
    <h1 class="pp-hero-swh__title">
      Spice World Hot Pot
      <span class="pp-hero-swh__title-sub"><?php echo esc_html__('Thương Hiệu Lẩu Đỉnh Cao', 'saigonhoreca'); ?></span>
    </h1>
    <div class="pp-hero-swh__divider" aria-hidden="true"></div>
  </div>
-->
</section>
