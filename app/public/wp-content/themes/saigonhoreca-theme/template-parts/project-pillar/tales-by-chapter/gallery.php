<?php
/**
 * Project Pillar — tales-by-chapter
 * Section #6: gallery — image grid showcase.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();

$images = [
  ['src' => 'tales/Tales-by-Chapter-1.jpg', 'alt' => __('Không gian Tales by Chapter', 'saigonhoreca'), 'caption' => __('Không gian phục vụ', 'saigonhoreca'), 'class' => 'pp-gallery-tbc__item--large'],
  ['src' => 'tales/Tales-by-Chapter-4.jpg', 'alt' => __('Thiết bị chuyên dụng', 'saigonhoreca'), 'caption' => __('Thiết bị chuyên dụng', 'saigonhoreca'), 'class' => ''],
  ['src' => 'tales/Tales-by-Chapter-5.jpg', 'alt' => __('Sản phẩm cao cấp', 'saigonhoreca'), 'caption' => __('Sản phẩm cao cấp', 'saigonhoreca'), 'class' => ''],
  ['src' => 'tales/Tales-by-Chapter-6.jpg', 'alt' => __('Bàn ăn Tales by Chapter', 'saigonhoreca'), 'caption' => __('Bàn ăn plant-based', 'saigonhoreca'), 'class' => 'pp-gallery-tbc__item--wide'],
];
?>
<section class="pp__section scroll-reveal">
  <div class="pp-container-shared">
    <div class="pp-text-tbc__divider pp-text-tbc__divider--center" aria-hidden="true"></div>
    <h2 class="pp-text-tbc__title" style="text-align:center;"><?php echo esc_html__('Project Gallery', 'saigonhoreca'); ?></h2>
    <div class="pp-gallery-tbc" aria-label="<?php echo esc_attr__('Tales by Chapter gallery', 'saigonhoreca'); ?>">
      <?php foreach ($images as $img): ?>
        <figure class="pp-gallery-tbc__item scroll-reveal <?php echo esc_attr($img['class']); ?>">
          <div class="pp-image-container-shared">
            <img src="<?php echo sgh_img($img['src']); ?>" alt="<?php echo esc_attr($img['alt']); ?>" loading="lazy" decoding="async">
            <div class="pp-image-caption-shared"><?php echo esc_html($img['caption']); ?></div>
          </div>
        </figure>
      <?php endforeach; ?>
    </div>
  </div>
</section>
