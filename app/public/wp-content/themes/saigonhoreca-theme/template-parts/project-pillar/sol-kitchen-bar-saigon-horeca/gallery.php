<?php
/**
 * Project Pillar — sol-kitchen-bar-saigon-horeca
 * Section #6: gallery — metro image grid + closing remarks (đúc kết).
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
?>
<section class="pp-section-solshr pp-gallery-solshr scroll-reveal">
  <div class="pp-solshr-ambient pp-solshr-ambient--tr" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <div class="pp-gallery-solshr__head">
      <span class="pp-text-solshr__eyebrow"><?php echo esc_html__('Không gian & Trải nghiệm', 'saigonhoreca'); ?></span>
      <span class="pp-text-solshr__divider pp-text-solshr__divider--center" aria-hidden="true"></span>
      <h2 class="pp-text-solshr__title"><?php echo esc_html__('Thư viện SOL Kitchen & Bar', 'saigonhoreca'); ?></h2>
      <div class="pp-text-solshr__body pp-text-solshr--center">
        <p><?php echo esc_html__('Một không gian hacienda ngập tràn màu sắc và cảm hứng Mỹ Latin, nơi mỗi góc nhìn đều kể một câu chuyện ẩm thực riêng.', 'saigonhoreca'); ?></p>
      </div>
    </div>

    <div class="pp-gallery-solshr__grid">
      <figure class="pp-gallery-solshr__item pp-gallery-solshr__item--wide scroll-reveal">
        <div class="pp-image-container-shared">
          <img src="<?php echo sgh_img('sol-kitchen-bar-saigon-horeca/Sol-kitchen-00003.jpg'); ?>" alt="<?php echo esc_attr__('Không gian ăn uống SOL Kitchen & Bar', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
          <div class="pp-image-caption-shared"><?php echo esc_html__('Khu vực phục vụ rộng rãi với ánh sáng ấm áp đặc trưng phong cách hacienda.', 'saigonhoreca'); ?></div>
        </div>
      </figure>
      <figure class="pp-gallery-solshr__item scroll-reveal">
        <div class="pp-image-container-shared">
          <img src="<?php echo sgh_img('sol-kitchen-bar-saigon-horeca/Sol-kitchen-00004.jpg'); ?>" alt="<?php echo esc_attr__('Chi tiết trang trí SOL Kitchen & Bar', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
          <div class="pp-image-caption-shared"><?php echo esc_html__('Chi tiết trang trí gợi nhắc bầu không khí Mỹ Latin đầy màu sắc.', 'saigonhoreca'); ?></div>
        </div>
      </figure>
      <figure class="pp-gallery-solshr__item scroll-reveal">
        <div class="pp-image-container-shared">
          <img src="<?php echo sgh_img('sol-kitchen-bar-saigon-horeca/Sol-kitchen-00005.jpg'); ?>" alt="<?php echo esc_attr__('Quầy bar SOL Kitchen & Bar', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
          <div class="pp-image-caption-shared"><?php echo esc_html__('Quầy bar sang trọng — trung tâm của những ly pisco sour và cocktail signature.', 'saigonhoreca'); ?></div>
        </div>
      </figure>
    </div>
  </div>
</section>

<section class="pp-section-solshr pp-section-solshr--alt pp-outro-solshr scroll-reveal">
  <div class="pp-solshr-ambient pp-solshr-ambient--bl" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <div class="pp-outro-solshr__layout">
      <div class="pp-outro-solshr__media scroll-reveal">
        <div class="pp-image-container-shared pp-frame-solshr">
          <img src="<?php echo sgh_img('sol-kitchen-bar-saigon-horeca/Sol-kitchen-00006.jpg'); ?>" alt="<?php echo esc_attr__('Trải nghiệm ẩm thực tại SOL Kitchen & Bar', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
          <div class="pp-image-caption-shared"><?php echo esc_html__('Trải nghiệm ẩm thực Latin American trọn vẹn trong không gian được Saigon Horeca chăm chút.', 'saigonhoreca'); ?></div>
        </div>
      </div>
      <div class="pp-outro-solshr__body pp-story-card-solshr">
        <div class="pp-solshr-ornament pp-solshr-ornament--right" aria-hidden="true"></div>
        <span class="pp-text-solshr__divider" aria-hidden="true"></span>
        <h2 class="pp-text-solshr__title"><?php echo esc_html__('Môi trường làm việc đáng tin cậy', 'saigonhoreca'); ?></h2>
        <div class="pp-text-solshr__body">
          <p><?php echo esc_html__('Sự hợp tác chặt chẽ và chuyên nghiệp giữa SOL Kitchen & Bar và Saigon Horeca đã tạo ra một môi trường làm việc hiệu quả và đáng tin cậy cho việc chuẩn bị và phục vụ các món ăn tinh tế đến khách hàng.', 'saigonhoreca'); ?></p>
          <p class="pp-outro-solshr__sign"><?php echo esc_html__('Saigon Horeca | Kitchen Equipment and Bar Solutions.', 'saigonhoreca'); ?></p>
        </div>
      </div>
    </div>
  </div>
</section>
