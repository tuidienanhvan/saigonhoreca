<?php
/**
 * Project Pillar — truong-mam-non-trinh-vuong
 * Section #6: gallery (Gold asymmetric editorial mosaic · shared frames · result note)
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();

/* Uniform editorial grid — every tile is an equal 4/3 box (no per-tile size modifiers).
   Same real images + Vietnamese captions. */
$tvg_gallery = array(
  array('img' => 'bep-an-truong-mam-non-tu-thuc-trinh-vuong/du-an-truong-mn-trinh-vuong.jpg',   'cap' => __('Tủ nấu cơm – hấp công nghiệp inox 2 cánh, công suất lớn', 'saigonhoreca')),
  array('img' => 'bep-an-truong-mam-non-tu-thuc-trinh-vuong/du-an-truong-mn-trinh-vuong-2.jpg', 'cap' => __('Tủ nấu cơm inox và dàn nồi nghiêng dưới hệ chụp hút khổ lớn', 'saigonhoreca')),
  array('img' => 'bep-an-truong-mam-non-tu-thuc-trinh-vuong/du-an-truong-mn-trinh-vuong-6.jpg', 'cap' => __('Cận cảnh họng đốt gas gang trên mặt bếp inox công suất lớn', 'saigonhoreca')),
);
?>
<section class="pp-gallery-tvg">
  <div class="pp-gallery-tvg__glow" aria-hidden="true"></div>
  <div class="pp-gallery-tvg__glow pp-gallery-tvg__glow--bl" aria-hidden="true"></div>
  <div class="pp-gallery-tvg__watermark" aria-hidden="true">06</div>

  <div class="pp-container-shared">
    <header class="pp-gallery-tvg__head">
      <div class="pp-gallery-tvg__badge">
        <span class="pp-gallery-tvg__badge-dot" aria-hidden="true"></span>
        <?php echo esc_html__('Hình ảnh thực tế', 'saigonhoreca'); ?>
      </div>
      <h2 class="pp-gallery-tvg__title"><?php echo esc_html__('Bếp Trinh Vương — sạch sẽ, sáng bóng, vận hành nhịp nhàng', 'saigonhoreca'); ?></h2>
      <div class="pp-gallery-tvg__divider" aria-hidden="true"></div>
      <p class="pp-gallery-tvg__lead"><?php echo esc_html__('Saigon Horeca không thiết kế để "đẹp trên bản vẽ", mà để bếp vận hành tốt ngoài đời thật — từng khu vực, từng thiết bị đều làm đúng vai trò của mình.', 'saigonhoreca'); ?></p>
    </header>

    <div class="pp-gallery-tvg__mosaic">
      <?php foreach ($tvg_gallery as $g) : ?>
      <div class="pp-image-container-shared pp-gallery-tvg__item">
        <img src="<?php echo sgh_img($g['img']); ?>" alt="<?php echo esc_attr($g['cap']); ?>" loading="lazy" decoding="async" width="1366" height="768">
        <div class="pp-image-caption-shared"><?php echo esc_html($g['cap']); ?></div>
      </div>
      <?php endforeach; ?>
    </div>

    <div class="pp-gallery-tvg__result">
      <div class="pp-gallery-tvg__result-icon" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
          <path d="M20 6 9 17l-5-5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
      <div class="pp-gallery-tvg__result-text">
        <p><?php echo esc_html__('Tại Trường Mầm Non Trinh Vương, kết quả được thể hiện rõ: 500 suất ăn được chuẩn bị đầy đủ, nóng hổi, đúng giờ — mỗi ngày.', 'saigonhoreca'); ?></p>
        <blockquote><?php echo esc_html__('"Hiệu quả bắt đầu từ chi tiết — và chi tiết bắt đầu từ sự thấu hiểu."', 'saigonhoreca'); ?></blockquote>
      </div>
    </div>
  </div>
</section>
