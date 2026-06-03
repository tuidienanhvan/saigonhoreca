<?php
/**
 * Project Pillar â€” du-an-kdl-rung-thong-nui-voi-cua-saigonhoreca
 * Section #6: gallery â€” image grid showcase.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();

$images = [
  ['src' => 'kdl/KDL-Rung-Thong-1.jpg', 'alt' => __('Khu vá»±c phá»¥c vá»¥ KDL Rá»«ng ThÃ´ng', 'saigonhoreca'), 'caption' => __('KhÃ´ng gian phá»¥c vá»¥', 'saigonhoreca'), 'class' => 'pp-gallery-kdl__item--large'],
  ['src' => 'kdl/KDL-Rung-Thong-4.jpg', 'alt' => __('KhÃ´ng gian nghá»‰ dÆ°á»¡ng', 'saigonhoreca'), 'caption' => __('KhÃ´ng gian nghá»‰ dÆ°á»¡ng', 'saigonhoreca'), 'class' => ''],
  ['src' => 'kdl/KDL-Rung-Thong-5.jpg', 'alt' => __('Tiá»‡n Ã­ch khu du lá»‹ch', 'saigonhoreca'), 'caption' => __('Tiá»‡n Ã­ch hiá»‡n Ä‘áº¡i', 'saigonhoreca'), 'class' => ''],
  ['src' => 'kdl/KDL-Rung-Thong-6.jpg', 'alt' => __('View tá»« trÃªn cao', 'saigonhoreca'), 'caption' => __('ToÃ n cáº£nh tá»« trÃªn cao', 'saigonhoreca'), 'class' => 'pp-gallery-kdl__item--wide'],
];
?>
<section class="pp__section scroll-reveal">
  <div class="pp-container-shared">
    <div class="pp-text-kdl__divider pp-text-kdl__divider--center" aria-hidden="true"></div>
    <h2 class="pp-text-kdl__title" style="text-align:center;"><?php echo esc_html__('Project Gallery', 'saigonhoreca'); ?></h2>
    <div class="pp-gallery-kdl" aria-label="<?php echo esc_attr__('KDL Rá»«ng ThÃ´ng gallery', 'saigonhoreca'); ?>">
      <?php foreach ($images as $img): ?>
        <figure class="pp-gallery-kdl__item scroll-reveal <?php echo esc_attr($img['class']); ?>">
          <div class="pp-image-container-shared">
            <img src="<?php echo sgh_img($img['src']); ?>" alt="<?php echo esc_attr($img['alt']); ?>" loading="lazy" decoding="async">
            <div class="pp-image-caption-shared"><?php echo esc_html($img['caption']); ?></div>
          </div>
        </figure>
      <?php endforeach; ?>
    </div>
  </div>
</section>

