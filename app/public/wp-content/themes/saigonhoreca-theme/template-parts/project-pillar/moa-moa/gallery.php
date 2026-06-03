<?php
/**
 * Project Pillar â€” moa-moa
 * Section #6: gallery â€” image grid showcase.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();

$images = [
  ['src' => 'moa/Moa-Moa-1.jpg', 'alt' => __('KhÃ´ng gian nhÃ  hÃ ng Moa Moa', 'saigonhoreca'), 'caption' => __('KhÃ´ng gian phá»¥c vá»¥', 'saigonhoreca'), 'class' => 'pp-gallery-moa__item--large'],
  ['src' => 'moa/Moa-Moa-4.jpg', 'alt' => __('KhÃ´ng gian báº¿p', 'saigonhoreca'), 'caption' => __('KhÃ´ng gian báº¿p má»Ÿ', 'saigonhoreca'), 'class' => ''],
  ['src' => 'moa/Moa-Moa-5.jpg', 'alt' => __('Sáº£n pháº©m cao cáº¥p', 'saigonhoreca'), 'caption' => __('Sáº£n pháº©m cao cáº¥p', 'saigonhoreca'), 'class' => ''],
  ['src' => 'moa/Moa-Moa-6.jpg', 'alt' => __('BÃ n Äƒn Moa Moa', 'saigonhoreca'), 'caption' => __('BÃ n Äƒn áº©m thá»±c Viá»‡t', 'saigonhoreca'), 'class' => 'pp-gallery-moa__item--wide'],
];
?>
<section class="pp__section scroll-reveal">
  <div class="pp-container-shared">
    <div class="pp-text-moa__divider pp-text-moa__divider--center" aria-hidden="true"></div>
    <h2 class="pp-text-moa__title" style="text-align:center;"><?php echo esc_html__('Project Gallery', 'saigonhoreca'); ?></h2>
    <div class="pp-gallery-moa" aria-label="<?php echo esc_attr__('Moa Moa gallery', 'saigonhoreca'); ?>">
      <?php foreach ($images as $img): ?>
        <figure class="pp-gallery-moa__item scroll-reveal <?php echo esc_attr($img['class']); ?>">
          <div class="pp-image-container-shared">
            <img src="<?php echo sgh_img($img['src']); ?>" alt="<?php echo esc_attr($img['alt']); ?>" loading="lazy" decoding="async">
            <div class="pp-image-caption-shared"><?php echo esc_html($img['caption']); ?></div>
          </div>
        </figure>
      <?php endforeach; ?>
    </div>
  </div>
</section>

