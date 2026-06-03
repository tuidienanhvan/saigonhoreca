<?php
/**
 * Project Pillar — ganh-hao-noi-hon-bien-trong-tung-net-kien-truc
 * Section #5: specs (split — tinh gọn, hiệu quả, dấu ấn vùng biển + thiết bị)
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;

$gha_equipment = array(
  __('Bếp gas công nghiệp & chảo xào liền bệ', 'saigonhoreca'),
  __('Bếp hấp 3 tầng & bếp nướng mặt đá', 'saigonhoreca'),
  __('Tủ lạnh công nghiệp bảo quản hải sản', 'saigonhoreca'),
  __('Khu rửa đa khoang bố trí khoa học', 'saigonhoreca'),
);
?>
<section class="pp-specs-ganh">
  <div class="pp-specs-ganh__glow" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <div class="pp-specs-ganh__layout">
      <div class="pp-specs-ganh__media pp-image-container-shared scroll-reveal reveal-left">
        <span class="pp-specs-ganh__coord pp-specs-ganh__coord--tl" aria-hidden="true">SYS_COORD</span>
        <span class="pp-specs-ganh__coord pp-specs-ganh__coord--br" aria-hidden="true">SCALE 1:1</span>
        <img src="<?php echo sgh_img('ganh-hao-noi-hon-bien-trong-tung-net-kien-truc/ganh-hao-sgh-7.jpg'); ?>" alt="<?php echo esc_attr__('Hệ thống bếp công nghiệp Saigon Horeca tại Gành Hào', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
        <figcaption class="pp-image-caption-shared"><?php echo esc_html__('Luồng vận hành xoay quanh nguyên liệu tươi sống – tốc độ nhanh, hương vị nguyên bản', 'saigonhoreca'); ?></figcaption>
      </div>

      <div class="pp-specs-ganh__body scroll-reveal reveal-right">
        <span class="pp-specs-ganh__divider" aria-hidden="true"></span>
        <h2 class="pp-specs-ganh__title"><?php echo esc_html__('Tinh gọn – hiệu quả – mang dấu ấn vùng biển', 'saigonhoreca'); ?></h2>
        <p class="pp-specs-ganh__lead"><?php echo esc_html__('Điểm đặc biệt của dự án lần này là sự kết hợp giữa kỹ thuật hiện đại và cảm hứng địa phương. Không gian bếp vẫn giữ được chất “biển” – với luồng vận hành xoay quanh nguyên liệu tươi sống, tốc độ xử lý nhanh nhưng vẫn đảm bảo hương vị nguyên bản.', 'saigonhoreca'); ?></p>
        <ul class="pp-specs-ganh__list">
          <?php foreach ($gha_equipment as $i => $item) : ?>
          <li class="pp-specs-ganh__feature">
            <span class="pp-specs-ganh__num" aria-hidden="true"><?php echo esc_html(sprintf('%02d', $i + 1)); ?></span>
            <span class="pp-specs-ganh__feature-text"><?php echo esc_html($item); ?></span>
          </li>
          <?php endforeach; ?>
        </ul>
        <p class="pp-specs-ganh__note"><?php echo esc_html__('Tất cả đều bố trí khoa học, tiết kiệm diện tích mà vẫn đảm bảo công năng.', 'saigonhoreca'); ?></p>
      </div>
    </div>
  </div>
</section>
