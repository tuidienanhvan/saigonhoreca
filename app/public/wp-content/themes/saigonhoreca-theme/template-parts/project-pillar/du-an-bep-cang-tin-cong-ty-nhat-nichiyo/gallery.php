<?php
/**
 * Project Pillar — du-an-bep-cang-tin-cong-ty-nhat-nichiyo
 * Section #6: gallery — closing editorial block (split visual + đúc kết statement).
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-nichiyo pp-gallery-nichiyo-section scroll-reveal reveal-fade">
  <div class="pp-nichiyo-glow pp-nichiyo-glow--tr" aria-hidden="true"></div>
  <div class="pp-container-shared">
    <div class="pp-gallery-nichiyo__closing">
      <div class="pp-gallery-nichiyo__closing-media scroll-reveal reveal-left-short">
        <div class="pp-image-container-shared">
          <img src="<?php echo sgh_img('du-an-bep-cang-tin-cong-ty-nhat-nichiyo/nichiyo-du-an-3.jpg'); ?>" alt="<?php echo esc_attr__('Toàn cảnh bếp căng tin công nghiệp Nichiyo do Saigon Horeca thực hiện', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
          <div class="pp-image-caption-shared"><?php echo esc_html__('Toàn cảnh bếp căng tin hoàn thiện — minh chứng cho sự tận tâm và chuyên nghiệp của Saigon Horeca.', 'saigonhoreca'); ?></div>
        </div>
      </div>
      <div class="pp-gallery-nichiyo__closing-body scroll-reveal reveal-right-short">
        <span class="pp-text-nichiyo__divider" aria-hidden="true"></span>
        <h2 class="pp-text-nichiyo__title"><?php echo esc_html__('Một môi trường làm việc đơn giản, hiệu quả và truyền cảm hứng', 'saigonhoreca'); ?></h2>
        <div class="pp-text-nichiyo__body">
          <p><?php echo esc_html__('Sự khéo léo và tinh tế trong bố trí không chỉ làm cho không gian bếp gọn gàng, tiện nghi mà còn nâng cao hiệu suất và sự thoải mái cho các đầu bếp. Tất cả kết hợp tạo nên một môi trường làm việc đơn giản, hiệu quả và đầy động lực cho đội ngũ Nichiyo.', 'saigonhoreca'); ?></p>
        </div>
      </div>
    </div>
  </div>
</section>
