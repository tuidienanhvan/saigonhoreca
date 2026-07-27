<?php
/**
 * Project Pillar — tales-by-chapter
 * Section #6: gallery — closing editorial album of the three spaces.
 * Clean uniform grid: every tile is the same 4/3 frame (zero voids).
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();

/* Uniform album: all tiles share one 4/3 frame. Real images + captions only. */
$tbc_gallery = [
  [
    'src'     => 'tales-by-chapter/tales-by-chapter-chef-tasting-menu-thuan-chay.jpg',
    'alt'     => __('Chef tasting menu thuần chay Tales by Chapter', 'saigonhoreca'),
    'caption' => __('Chef’s tasting menu thuần chay theo mùa', 'saigonhoreca'),
  ],
];
?>
<section class="pp-section-tbc pp-section-tbc--alt pp-gallery-section-tbc">
  <div class="pp-ambient-glow-tbc pp-ambient-glow-tbc--top-right" aria-hidden="true"></div>
  <div class="pp-ambient-glow-tbc pp-ambient-glow-tbc--bottom-left" aria-hidden="true"></div>

  <!-- Số chương chìm kỹ thuật (section #6) -->
  <span class="pp-gallery-tbc__watermark" aria-hidden="true">06</span>

  <!-- Watermark SVG ba tầng (3 không gian) chìm nhẹ -->
  <svg class="pp-gallery-tbc__svg-mark" viewBox="0 0 120 120" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true">
    <path d="M20 84 H100 M28 84 V58 H92 V84 M40 58 V36 H80 V58" stroke-linecap="round" stroke-linejoin="round"/>
  </svg>

  <div class="pp-container-shared">
    <header class="pp-gallery-section-tbc__header">
      <div class="pp-badge-tbc">
        <span class="pp-badge-tbc__num">Album</span>
        <?php echo esc_html__('Toàn cảnh dự án', 'saigonhoreca'); ?>
      </div>
      <h2 class="pp-text-tbc__title"><?php echo esc_html__('Ba không gian, một câu chuyện', 'saigonhoreca'); ?></h2>
      <span class="pp-text-tbc__divider pp-text-tbc__divider--center" aria-hidden="true"></span>
    </header>

    <div class="pp-gallery-tbc" aria-label="<?php echo esc_attr__('Album dự án Tales by Chapter', 'saigonhoreca'); ?>">
      <?php foreach ($tbc_gallery as $img): ?>
        <figure class="pp-gallery-tbc__item pp-image-container-shared">
          <span class="pp-gallery-tbc__corner" aria-hidden="true"></span>
          <img src="<?php echo sgh_img($img['src']); ?>" alt="<?php echo esc_attr($img['alt']); ?>" loading="lazy" decoding="async" width="1366" height="768">
          <figcaption class="pp-image-caption-shared"><?php echo esc_html($img['caption']); ?></figcaption>
        </figure>
      <?php endforeach; ?>
    </div>
  </div>
</section>
