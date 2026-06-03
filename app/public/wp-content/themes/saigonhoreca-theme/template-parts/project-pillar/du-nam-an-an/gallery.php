<?php
/**
 * Project Pillar — du-nam-an-an
 * Section #6: gallery — image grid showcase.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();

$images = [
  ['src' => 'dnaa/Du-Nam-An-An-1.jpg', 'alt' => __('Không gian bếp Nam An An', 'saigonhoreca'), 'caption' => __('Không gian phục vụ', 'saigonhoreca'), 'class' => 'pp-gallery-nan__item--large'],
  ['src' => 'dnaa/Du-Nam-An-An-4.jpg', 'alt' => __('Thiết bị chuyên dụng', 'saigonhoreca'), 'caption' => __('Thiết bị chuyên dụng', 'saigonhoreca'), 'class' => ''],
  ['src' => 'dnaa/Du-Nam-An-An-5.jpg', 'alt' => __('Sản phẩm cao cấp', 'saigonhoreca'), 'caption' => __('Sản phẩm cao cấp', 'saigonhoreca'), 'class' => ''],
  ['src' => 'dnaa/Du-Nam-An-An-6.jpg', 'alt' => __('Bàn ăn Nam An An', 'saigonhoreca'), 'caption' => __('Bàn ăn chăm sóc', 'saigonhoreca'), 'class' => 'pp-gallery-nan__item--wide'],
];
?>
<section class="pp__section scroll-reveal">
  <div class="pp-container-shared">
    <div class="pp-text-nan__divider pp-text-nan__divider--center" aria-hidden="true"></div>
    <h2 class="pp-text-nan__title" style="text-align:center;"><?php echo esc_html__('Project Gallery', 'saigonhoreca'); ?></h2>
    <div class="pp-gallery-nan" aria-label="<?php echo esc_attr__('Du Nam An An gallery', 'saigonhoreca'); ?>">
      <?php foreach ($images as $img): ?>
        <figure class="pp-gallery-nan__item scroll-reveal <?php echo esc_attr($img['class']); ?>">
          <div class="pp-image-container-shared">
            <img src="<?php echo sgh_img($img['src']); ?>" alt="<?php echo esc_attr($img['alt']); ?>" loading="lazy" decoding="async">
            <div class="pp-image-caption-shared"><?php echo esc_html($img['caption']); ?></div>
          </div>
        </figure>
      <?php endforeach; ?>
    </div>
  </div>
</section>
