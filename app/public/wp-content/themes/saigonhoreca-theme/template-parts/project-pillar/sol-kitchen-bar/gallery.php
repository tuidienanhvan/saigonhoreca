<?php
/**
 * Project Pillar — sol-kitchen-bar
 * Section #6: gallery — metro image grid + closing remarks (đúc kết).
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
?>
<section class="pp-section-sol pp-gallery-sol scroll-reveal">
  <div class="pp-sol-ambient pp-sol-ambient--tr" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <div class="pp-gallery-sol__head">
      <span class="pp-text-sol__eyebrow"><?php echo esc_html__('Thi công & Bàn giao', 'saigonhoreca'); ?></span>
      <span class="pp-text-sol__divider pp-text-sol__divider--center" aria-hidden="true"></span>
      <h2 class="pp-text-sol__title"><?php echo esc_html__('Quy trình thi công nghiêm ngặt', 'saigonhoreca'); ?></h2>
      <div class="pp-text-sol__body pp-text-sol--center">
        <p><?php echo esc_html__('Quy trình thi công được thực hiện một cách nghiêm ngặt và cẩn thận. Tất cả các công đoạn đều được giám sát chặt chẽ để đảm bảo an toàn lao động và hạn chế tối đa các sự cố có thể xảy ra.', 'saigonhoreca'); ?></p>
      </div>
    </div>

    <div class="pp-gallery-sol__grid">
      <figure class="pp-gallery-sol__item pp-gallery-sol__item--wide scroll-reveal">
        <div class="pp-image-container-shared">
          <img src="<?php echo sgh_img('sol-kitchen-bar/thi-cong-03.jpg'); ?>" alt="<?php echo esc_attr__('Thi công lắp đặt hệ thống bếp Sol Kitchen & Bar quận 7', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
          <div class="pp-image-caption-shared"><?php echo esc_html__('Lắp đặt và đấu nối thiết bị tại công trường, giám sát chặt chẽ từng công đoạn.', 'saigonhoreca'); ?></div>
        </div>
      </figure>
      <figure class="pp-gallery-sol__item scroll-reveal">
        <div class="pp-image-container-shared">
          <img src="<?php echo sgh_img('sol-kitchen-bar/thi-cong-04.jpg'); ?>" alt="<?php echo esc_attr__('Hoàn thiện hệ thống bàn inox và thiết bị bếp Sol Kitchen & Bar', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
          <div class="pp-image-caption-shared"><?php echo esc_html__('Hệ thống bàn inox và thiết bị được hoàn thiện theo đúng bố trí công năng.', 'saigonhoreca'); ?></div>
        </div>
      </figure>
      <figure class="pp-gallery-sol__item scroll-reveal">
        <div class="pp-image-container-shared">
          <img src="<?php echo sgh_img('sol-kitchen-bar/chup-hut-8m-2-1.jpg'); ?>" alt="<?php echo esc_attr__('Chi tiết hệ thống chụp hút khói khu bếp Sol Kitchen & Bar', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
          <div class="pp-image-caption-shared"><?php echo esc_html__('Chi tiết hệ chụp hút khói khu nấu, đảm bảo luồng khí lưu thông liên tục.', 'saigonhoreca'); ?></div>
        </div>
      </figure>
    </div>
  </div>
</section>

<section class="pp-section-sol pp-section-sol--alt pp-outro-sol scroll-reveal">
  <div class="pp-sol-ambient pp-sol-ambient--bl" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <div class="pp-outro-sol__layout">
      <div class="pp-outro-sol__media scroll-reveal">
        <div class="pp-image-container-shared pp-frame-sol">
          <img src="<?php echo sgh_img('sol-kitchen-bar/sol-sgh.jpg'); ?>" alt="<?php echo esc_attr__('Khu bếp Sol Kitchen & Bar quận 7 hoàn thiện bởi Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
          <div class="pp-image-caption-shared"><?php echo esc_html__('Khu bếp Sol quận 7 hoàn thiện — kết tinh của khảo sát kỹ lưỡng và thi công chuẩn mực.', 'saigonhoreca'); ?></div>
        </div>
      </div>
      <div class="pp-outro-sol__body pp-story-card-sol">
        <div class="pp-sol-ornament pp-sol-ornament--right" aria-hidden="true"></div>
        <span class="pp-text-sol__divider" aria-hidden="true"></span>
        <h2 class="pp-text-sol__title"><?php echo esc_html__('Đối tác đáng tin cậy', 'saigonhoreca'); ?></h2>
        <div class="pp-text-sol__body">
          <p><?php echo esc_html__('Saigon Horeca cam kết mang đến dịch vụ chất lượng cao, sự tin cậy trong kinh doanh và tính thẩm mỹ trong hệ thống bếp. Chúng tôi luôn đặt lợi ích của khách hàng lên hàng đầu và không ngừng cải tiến để đáp ứng nhu cầu ngày càng cao của thị trường.', 'saigonhoreca'); ?></p>
          <p><?php echo esc_html__('Với kinh nghiệm và uy tín đã được khẳng định 10 năm nay, Saigon Horeca tự hào là đối tác đáng tin cậy của nhiều nhà hàng và quán Bar nổi tiếng như Bambino, Loco Complex, Yuzu Omakase, Unic Bar, …', 'saigonhoreca'); ?></p>
          <p class="pp-outro-sol__sign"><?php echo esc_html__('Saigon Horeca | Kitchen Equipment and Bar Solutions.', 'saigonhoreca'); ?></p>
        </div>
      </div>
    </div>
  </div>
</section>
