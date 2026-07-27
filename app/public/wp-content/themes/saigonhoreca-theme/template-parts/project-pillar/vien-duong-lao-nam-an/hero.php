<?php
/**
 * Project Pillar — vien-duong-lao-nam-an
 * Section #1: hero — Viện dưỡng lão Nam An (Nursing Home Kitchen).
 * Full-viewport poster + Parallax Clip-Path (GPU-smooth, no JS) + caring-precision motif.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-hero-nan">
  <!-- YouTube background video with fallback background image -->
  <div class="pp-hero-nan__media" style="background-image:url('<?php echo esc_url(sgh_img('vien-duong-lao-nam-an/nam-an-banner-home.webp')); ?>'); background-size:cover; background-position:center;">
      <iframe data-lazy-src="https://www.youtube.com/embed/_TFKny4seMU?autoplay=1&mute=1&loop=1&playlist=_TFKny4seMU&controls=0&showinfo=0&modestbranding=1&iv_load_policy=3&playsinline=1&disablekb=1&fs=0&rel=0&cc_load_policy=0&color=white&hl=vi" allow="autoplay; encrypted-media; fullscreen" frameborder="0" title="Background video" tabindex="-1" aria-hidden="true"></iframe>
  </div>
  <div class="pp-hero-nan__overlay" aria-hidden="true"></div>
  <div class="pp-ambient-glow-nan pp-ambient-glow-nan--center" aria-hidden="true"></div>

  <!-- Removed text content overlay as the background video already contains baked-in titles -->

  <div class="pp-hero-nan__scroll" aria-hidden="true">
    <svg class="pp-hero-nan__scroll-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="18" height="18">
      <path d="M12 5v14M5 12l7 7 7-7" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
  </div>
</section>
