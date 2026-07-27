<?php
/**
 * Project Pillar — tales-by-chapter
 * Section #1: hero — editorial full-viewport opener with chapter motif.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-hero-tbc">
  <!-- Thẻ con gánh hiệu ứng Parallax Clip-Path siêu mượt (không JS, GPU) -->
  <div class="pp-hero-tbc__bg" style="background-image:url('<?php echo sgh_img('tales-by-chapter/tales-by-chapter-thumbnail-project-cover.webp'); ?>');" aria-hidden="true"></div>
  <div class="pp-hero-tbc__overlay" aria-hidden="true"></div>
  <div class="pp-hero-tbc__grain" aria-hidden="true"></div>

  <div class="pp-container-shared pp-hero-tbc__inner">
    <!-- Removed text content overlay as the background image already contains baked-in titles
<div class="pp-hero-tbc__content">
      <div class="pp-hero-tbc__mark" aria-hidden="true">
        <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
          <path d="M24 11c-4-3-9-3.5-14-2.5v26c5-1 10-.5 14 2.5 4-3 9-3.5 14-2.5v-26c-5-1-10-.5-14 2.5Z"/>
          <path d="M24 11v26"/>
        </svg>
      </div>

      <span class="pp-hero-tbc__eyebrow"><?php echo esc_html__('Saigon Horeca × Tales by Chapter', 'saigonhoreca'); ?></span>

      <h1 class="pp-hero-tbc__title">
        Tales by Chapter
        <span class="pp-hero-tbc__title-sub"><?php echo esc_html__('Câu chuyện phía sau một gian bếp thuần chay', 'saigonhoreca'); ?></span>
      </h1>

      <p class="pp-hero-tbc__tagline"><?php echo esc_html__('The first zero waste plant-based dining in Vietnam', 'saigonhoreca'); ?></p>

      <div class="pp-hero-tbc__divider" aria-hidden="true"></div>
    </div>
-->

    <div class="pp-hero-tbc__scroll" aria-hidden="true">
      <span class="pp-hero-tbc__scroll-text"><?php echo esc_html__('Lật trang đầu tiên', 'saigonhoreca'); ?></span>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
        <path d="M12 5v14M5 12l7 7 7-7"/>
      </svg>
    </div>
  </div>
</section>
