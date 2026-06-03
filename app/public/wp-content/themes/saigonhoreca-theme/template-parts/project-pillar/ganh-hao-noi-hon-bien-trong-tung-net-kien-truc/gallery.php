<?php
/**
 * Project Pillar — ganh-hao-noi-hon-bien-trong-tung-net-kien-truc
 * Section #6: gallery + khối đúc kết (lời kết câu chuyện)
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;

$gha_gallery = array(
  array('img' => 'ganh-hao-sgh-8.jpg',     'cap' => __('Giải pháp chống gỉ, tối ưu thoát nhiệt giữa hơi muối biển khơi', 'saigonhoreca')),
  array('img' => 'ganh-hao-sgh-9.jpg',     'cap' => __('Gian bếp thân thiện với nhân viên vận hành – hậu trường sân khấu ẩm thực', 'saigonhoreca')),
  array('img' => 'ganh-hao-sgh-3.jpg',     'cap' => __('Hơn 100 món hải sản tươi sống được phục vụ mỗi ngày', 'saigonhoreca')),
  array('img' => 'du-an-ganh-hao-sgh.jpg', 'cap' => __('Gành Hào – nơi hồn biển hội tụ trong từng nét kiến trúc', 'saigonhoreca')),
);
?>
<section class="pp-gallery-ganh">
  <div class="pp-gallery-ganh__glow" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <div class="pp-gallery-ganh__head scroll-reveal reveal-up">
      <span class="pp-gallery-ganh__divider" aria-hidden="true"></span>
      <h2 class="pp-gallery-ganh__title"><?php echo esc_html__('Bền bỉ giữa nắng gió đại dương', 'saigonhoreca'); ?></h2>
      <p class="pp-gallery-ganh__lead"><?php echo esc_html__('Ở một nơi mà gió biển thổi ngày đêm, độ ẩm và hơi muối luôn là thách thức lớn cho mọi thiết bị kim loại – Saigon Horeca đã nghiên cứu và đưa vào các giải pháp chống gỉ, tối ưu thoát nhiệt và giảm tiếng ồn, đảm bảo gian bếp không chỉ hoạt động hiệu quả, mà còn thân thiện với nhân viên vận hành – những người đứng sau ánh đèn sân khấu ẩm thực.', 'saigonhoreca'); ?></p>
    </div>

    <div class="pp-gallery-ganh__grid">
      <?php foreach ($gha_gallery as $i => $shot) :
        $delay = ($i % 3 + 1) * 100; ?>
      <figure class="pp-gallery-ganh__item pp-image-container-shared scroll-reveal reveal-up delay-<?php echo (int) $delay; ?>">
        <img src="<?php echo sgh_img('ganh-hao-noi-hon-bien-trong-tung-net-kien-truc/' . $shot['img']); ?>" alt="<?php echo esc_attr($shot['cap']); ?>" loading="lazy" decoding="async" width="1366" height="768">
        <figcaption class="pp-image-caption-shared"><?php echo esc_html($shot['cap']); ?></figcaption>
      </figure>
      <?php endforeach; ?>
    </div>

    <div class="pp-gallery-ganh__closing scroll-reveal reveal-up">
      <p><?php echo esc_html__('Với dự án này, Saigon Horeca không chỉ bàn giao một hệ thống bếp công nghiệp, mà còn góp phần duy trì và nâng tầm trải nghiệm ẩm thực tại một địa điểm đặc biệt của Vũng Tàu. Chúng tôi tự hào khi được đồng hành cùng Gành Hào – nơi không chỉ giữ hồn biển qua từng món ăn, mà còn thể hiện sự chỉn chu từ khâu chuẩn bị phía sau.', 'saigonhoreca'); ?></p>
  </div>
  </div>
</section>
