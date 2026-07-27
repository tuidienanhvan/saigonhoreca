<?php
/**
 * Project Pillar — ganh-hao-noi-hon-bien-trong-tung-net-kien-truc
 * Section #6: gallery (Split into Scenery and Kitchen) + khối đúc kết
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;

/* Mảng 3 ảnh phong cảnh & không gian (Scenery) */
$scenery_gallery = array(
  array(
    'img'   => 'ganh-hao-toan-canh-flycam-nha-hang-ven-bien-vung-tau.webp',
    'cap'   => __('Toàn cảnh Flycam nhà hàng ven biển Vũng Tàu', 'saigonhoreca'),
    'class' => 'span-2-col span-2-row pp-gallery-ganh__hero-shot',
  ),
  array(
    'img'   => 'ganh-hao-khu-ban-an-ngoai-troi-nhin-ra-ben-thuyen.webp',
    'cap'   => __('Khu vực bàn ăn ngoài trời nhìn ra bến thuyền', 'saigonhoreca'),
    'class' => 'span-1-col span-1-row',
  ),
  array(
    'img'   => 'ganh-hao-sgh-5.webp',
    'cap'   => __('Khu vực Buffet hải sản ven biển', 'saigonhoreca'),
    'class' => 'span-1-col span-1-row',
  ),
);

/* Mảng 2 ảnh bếp (Kitchen) */
$kitchen_gallery = array(
  array(
    'img'   => 'ganh-hao-bep-cong-nghiep-ban-ke-inox.webp',
    'cap'   => __('Bàn kệ inox chống gỉ sét', 'saigonhoreca'),
  ),
  array(
    'img'   => 'ganh-hao-khu-bep-inox-bep-a-lap-dat-view-bien.webp',
    'cap'   => __('Gian bếp hướng biển tối ưu thoát nhiệt', 'saigonhoreca'),
  ),
);
?>
<section class="pp-gallery-ganh">
  <div class="pp-gallery-ganh__glow" aria-hidden="true"></div>
  <div class="pp-gallery-ganh__watermark" aria-hidden="true">06</div>

  <div class="pp-container-shared">
    
  <!-- PHẦN 1: KHÔNG GIAN & PHONG CẢNH -->
  <div class="pp-gallery-ganh__section pp-gallery-ganh__section--scenery">
  <div class="pp-gallery-ganh__head">
  <div class="pp-gallery-ganh__badge">
  <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false">
  <path d="M12 2L14.85 9.15L22 12L14.85 14.85L12 22L9.15 14.85L2 12L9.15 9.15L12 2Z"/>
  </svg>
  <?php echo esc_html__('Không gian thực tế', 'saigonhoreca'); ?>
  </div>
  <h2 class="pp-gallery-ganh__title"><?php echo esc_html__('Dấu ấn giữa biển trời Vũng Tàu', 'saigonhoreca'); ?></h2>
  <span class="pp-gallery-ganh__divider" aria-hidden="true"></span>
  </div>

  <div class="pp-gallery-ganh__scenery-grid">
  <?php foreach ($scenery_gallery as $i => $shot) : ?>
  <figure class="pp-image-container-shared pp-gallery-ganh__item <?php echo esc_attr($shot['class']); ?> delay-<?php echo ($i+1)*100; ?>">
  <img src="<?php echo sgh_img('ganh-hao-noi-hon-bien-trong-tung-net-kien-truc/' . $shot['img']); ?>" alt="<?php echo esc_attr($shot['cap']); ?>" loading="lazy" decoding="async">
  <figcaption class="pp-image-caption-shared"><?php echo esc_html($shot['cap']); ?></figcaption>
  </figure>
  <?php endforeach; ?>
  </div>
  </div>

  <!-- PHẦN 2: BẾP & VẬN HÀNH -->
  <div class="pp-gallery-ganh__section pp-gallery-ganh__section--kitchen">
  <div class="pp-gallery-ganh__head">
  <div class="pp-gallery-ganh__badge">
  <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false">
  <path d="M12 2L14.85 9.15L22 12L14.85 14.85L12 22L9.15 14.85L2 12L9.15 9.15L12 2Z"/>
  </svg>
  <?php echo esc_html__('Hệ thống vận hành', 'saigonhoreca'); ?>
  </div>
  <h2 class="pp-gallery-ganh__title"><?php echo esc_html__('Bền bỉ giữa nắng gió đại dương', 'saigonhoreca'); ?></h2>
  <span class="pp-gallery-ganh__divider" aria-hidden="true"></span>
  <p class="pp-gallery-ganh__lead"><?php echo esc_html__('Ở nơi gió biển thổi ngày đêm, hơi muối là thách thức lớn. Saigon Horeca đã nghiên cứu giải pháp chống gỉ, tối ưu thoát nhiệt, đảm bảo gian bếp hoạt động hiệu quả và thân thiện với nhân viên vận hành.', 'saigonhoreca'); ?></p>
  </div>

  <div class="pp-gallery-ganh__kitchen-grid">
  <?php foreach ($kitchen_gallery as $i => $shot) : ?>
  <figure class="pp-image-container-shared pp-gallery-ganh__item delay-<?php echo ($i+1)*100; ?>">
  <img src="<?php echo sgh_img('ganh-hao-noi-hon-bien-trong-tung-net-kien-truc/' . $shot['img']); ?>" alt="<?php echo esc_attr($shot['cap']); ?>" loading="lazy" decoding="async">
  <figcaption class="pp-image-caption-shared"><?php echo esc_html($shot['cap']); ?></figcaption>
  </figure>
  <?php endforeach; ?>
  </div>
  </div>

  <div class="pp-gallery-ganh__closing">
  <p><?php echo esc_html__('Với dự án này, Saigon Horeca tự hào góp phần duy trì và nâng tầm trải nghiệm ẩm thực tại một địa điểm đặc biệt của Vũng Tàu. Nơi không chỉ giữ hồn biển qua từng món ăn, mà còn thể hiện sự chỉn chu từ khâu chuẩn bị phía sau.', 'saigonhoreca'); ?></p>
  </div>
  </div>
</section>
