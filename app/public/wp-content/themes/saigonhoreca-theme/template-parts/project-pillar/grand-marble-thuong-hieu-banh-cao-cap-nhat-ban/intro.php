<?php
/**
 * Project Pillar — grand-marble-thuong-hieu-banh-cao-cap-nhat-ban
 * Section #2: intro — alternating storytelling, Kyoto origin + artisan craft.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-gmarb pp-intro-gmarb scroll-reveal">
  <div class="pp-gmarb-flow-line" aria-hidden="true"></div>
  <div class="pp-gmarb-glow pp-gmarb-glow--tr" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <div class="pp-split-gmarb pp-intro-gmarb__layout">
      <div class="pp-split-gmarb__body pp-story-gmarb scroll-reveal reveal-left">
        <div class="pp-gmarb-ornament" aria-hidden="true"></div>
        <span class="pp-text-gmarb__eyebrow"><?php echo esc_html__('Di sản từ cố đô', 'saigonhoreca'); ?></span>
        <div class="pp-text-gmarb__divider pp-text-gmarb__divider--dots" aria-hidden="true"></div>
        <h2 class="pp-text-gmarb__title"><?php echo esc_html__('Grand Marble và Nét Đẹp của Bàn Tay Nghệ Nhân', 'saigonhoreca'); ?></h2>
        <div class="pp-text-gmarb__body pp-text-gmarb__body--dropcap">
          <p><?php echo esc_html__('Thương hiệu bánh cao cấp Grand Marble của Nhật Bản xuất hiện từ năm 1996 tại Kyoto – thành phố hơn 1000 năm lịch sử về ngành thủ công mỹ nghệ. Sự giao thương tấp nập và không khí sôi động của các ngành nghề xưa đã hòa quyện, tạo nên ảnh hưởng văn hóa từ nhiều quốc gia khác nhau, góp phần hình thành nên phong cách độc đáo của thương hiệu Grand Marble.', 'saigonhoreca'); ?></p>
        </div>
      </div>
      <div class="pp-split-gmarb__media scroll-reveal reveal-right delay-150">
        <div class="pp-image-container-shared pp-frame-gmarb">
          <img src="<?php echo sgh_img('grand-marble-thuong-hieu-banh-cao-cap-nhat-ban/grand-marble-nghe-nhan-banh.jpg'); ?>" alt="<?php echo esc_attr__('Nghệ nhân Grand Marble chế tác bánh Danish Marble thủ công', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="800" height="600">
          <div class="pp-image-caption-shared"><?php echo esc_html__('Đôi bàn tay tài hoa của nghệ nhân Kyoto tạo nên những lớp vân marble đặc trưng, dấu ấn riêng của thương hiệu Grand Marble.', 'saigonhoreca'); ?></div>
        </div>
      </div>
    </div>
  </div>
</section>
