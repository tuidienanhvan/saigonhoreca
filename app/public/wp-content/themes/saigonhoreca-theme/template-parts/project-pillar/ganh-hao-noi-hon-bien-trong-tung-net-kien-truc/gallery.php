<?php
/**
 * Project Pillar — ganh-hao-noi-hon-bien-trong-tung-net-kien-truc
 * Section #6: gallery (GOLD editorial asymmetric mosaic) + khối đúc kết (lời kết câu chuyện)
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;

/* Lưới đồng nhất — mọi tile cùng aspect-ratio 4/3 (lát kín, 0 khoảng trống đen) */
$gha_gallery = array(
  array(
    'img'   => 'ganh-hao-mui-da-toan-canh-tu-tren-cao-mai-cong-luon-song.webp',
    'cap'   => __('Gành Hào – nơi hồn biển hội tụ trong từng nét kiến trúc', 'saigonhoreca'),
  ),
  array(
    'img'   => 'ganh-hao-khu-ban-an-ngoai-troi-nhin-ra-ben-thuyen.webp',
    'cap'   => __('Khu vực bàn ăn ngoài trời có mái che nhìn ra bến thuyền cá Vũng Tàu', 'saigonhoreca'),
  ),
  array(
    'img'   => 'ganh-hao-bep-cong-nghiep-ban-ke-inox.webp',
    'cap'   => __('Giải pháp chống gỉ, tối ưu thoát nhiệt giữa hơi muối biển khơi', 'saigonhoreca'),
  ),
  array(
    'img'   => 'ganh-hao-khu-bep-inox-bep-a-lap-dat-view-bien.webp',
    'cap'   => __('Gian bếp thân thiện với nhân viên vận hành – hậu trường sân khấu ẩm thực', 'saigonhoreca'),
  ),
);
?>
<section class="pp-gallery-ganh">
  <div class="pp-gallery-ganh__glow" aria-hidden="true"></div>
  <div class="pp-gallery-ganh__watermark" aria-hidden="true">06</div>

  <div class="pp-container-shared">
    <div class="pp-gallery-ganh__head scroll-reveal reveal-up">
      <div class="pp-gallery-ganh__badge">
        <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false">
          <path d="M12 2L14.85 9.15L22 12L14.85 14.85L12 22L9.15 14.85L2 12L9.15 9.15L12 2Z"/>
        </svg>
        <?php echo esc_html__('Không gian & Vận hành thực tế', 'saigonhoreca'); ?>
      </div>
      <h2 class="pp-gallery-ganh__title"><?php echo esc_html__('Bền bỉ giữa nắng gió đại dương', 'saigonhoreca'); ?></h2>
      <span class="pp-gallery-ganh__divider" aria-hidden="true"></span>
      <p class="pp-gallery-ganh__lead"><?php echo esc_html__('Ở một nơi mà gió biển thổi ngày đêm, độ ẩm và hơi muối luôn là thách thức lớn cho mọi thiết bị kim loại – Saigon Horeca đã nghiên cứu và đưa vào các giải pháp chống gỉ, tối ưu thoát nhiệt và giảm tiếng ồn, đảm bảo gian bếp không chỉ hoạt động hiệu quả, mà còn thân thiện với nhân viên vận hành – những người đứng sau ánh đèn sân khấu ẩm thực.', 'saigonhoreca'); ?></p>
    </div>

    <div class="pp-gallery-ganh__mosaic">
      <?php foreach ($gha_gallery as $i => $shot) :
        $delay = ($i % 3 + 1) * 100; ?>
      <div class="pp-image-container-shared pp-gallery-ganh__item scroll-reveal reveal-up delay-<?php echo (int) $delay; ?>">
        <img src="<?php echo sgh_img('ganh-hao-noi-hon-bien-trong-tung-net-kien-truc/' . $shot['img']); ?>" alt="<?php echo esc_attr($shot['cap']); ?>" loading="lazy" decoding="async" width="1366" height="768">
        <div class="pp-image-caption-shared"><?php echo esc_html($shot['cap']); ?></div>
      </div>
      <?php endforeach; ?>
    </div>

    <div class="pp-gallery-ganh__closing scroll-reveal reveal-up">
      <p><?php echo esc_html__('Với dự án này, Saigon Horeca không chỉ bàn giao một hệ thống bếp công nghiệp, mà còn góp phần duy trì và nâng tầm trải nghiệm ẩm thực tại một địa điểm đặc biệt của Vũng Tàu. Chúng tôi tự hào khi được đồng hành cùng Gành Hào – nơi không chỉ giữ hồn biển qua từng món ăn, mà còn thể hiện sự chỉn chu từ khâu chuẩn bị phía sau.', 'saigonhoreca'); ?></p>
    </div>
  </div>
</section>
