<?php
/**
 * Project Pillar — du-an-vinh-hiep
 * Section #6: gallery — Dấu ấn Saigon Horeca + thư viện thiết bị
 * Image block: GOLD asymmetric editorial mosaic (12-col grid, varied tiles).
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-vhp pp-gallery-vhp">
  <div class="pp-watermark-vhp" aria-hidden="true">05</div>
  <div class="pp-ambient-glow-vhp pp-ambient-glow-vhp--top-right" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <header class="pp-gallery-vhp__header">
      <div class="pp-badge-vhp">
        <span class="pp-badge-vhp__diamond" aria-hidden="true"></span>
        <?php echo esc_html__('Dấu ấn Saigon Horeca', 'saigonhoreca'); ?>
      </div>
      <h2 class="pp-gallery-vhp__title">
        <?php echo esc_html__('Tư duy giải pháp, không chỉ là thiết bị', 'saigonhoreca'); ?>
      </h2>
      <div class="pp-divider-vhp pp-gallery-vhp__divider" aria-hidden="true"></div>
      <div class="pp-gallery-vhp__lead">
        <p><?php echo esc_html__('Ở dự án Vĩnh Hiệp, Saigon Horeca không tiếp cận với vai trò nhà cung cấp đơn thuần. Mỗi hạng mục đều được đặt trong một câu hỏi lớn hơn: Không gian này phục vụ ai? Vận hành như thế nào? Và góp phần gì cho hình ảnh thương hiệu?', 'saigonhoreca'); ?></p>
        <p><?php echo esc_html__('Từ hệ thống bếp dành cho ban lãnh đạo đến Coffee Lab trưng bày, tất cả đều được kết nối bởi một triết lý chung: bếp công nghiệp cao cấp không chỉ để nấu, mà còn để thể hiện chuẩn mực của một doanh nghiệp lớn.', 'saigonhoreca'); ?></p>
      </div>
    </header>

    <!-- ── GOLD ASYMMETRIC MOSAIC ──────────────────────────────────
         12-col editorial collage: 1 feature anchor (8×2 cols),
         2 stacked portraits (4 cols), 2 wide landscapes (6 cols).
         Shared frame + journalistic caption neo dưới mỗi ảnh. -->
    <div class="pp-gallery-vhp__mosaic" aria-label="<?php echo esc_attr__('Thư viện thiết bị bếp dự án Vĩnh Hiệp', 'saigonhoreca'); ?>">
      <svg class="pp-gallery-vhp__numeral" viewBox="0 0 120 120" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.4">
        <circle cx="60" cy="60" r="52" stroke-dasharray="3 5" />
        <path d="M60 14 L60 106 M14 60 L106 60" stroke-dasharray="2 6" opacity="0.6" />
        <circle cx="60" cy="60" r="20" />
      </svg>

      <figure class="pp-image-container-shared pp-gallery-vhp__item">
        <img src="<?php echo sgh_img('du-an-vinh-hiep/vinh-hiep-toan-canh-nha-may-tu-tren-cao.webp'); ?>" alt="<?php echo esc_attr__('Cổng và mặt tiền nhà máy cà phê Vĩnh Hiệp tại Pleiku, Gia Lai (ảnh bìa có logo)', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
        <figcaption class="pp-image-caption-shared"><?php echo esc_html__('Cổng và mặt tiền nhà máy Vĩnh Hiệp tại Pleiku, Gia Lai – nơi Saigon Horeca kiến tạo Coffee Lab và bếp căng tin', 'saigonhoreca'); ?></figcaption>
      </figure>

      <figure class="pp-image-container-shared pp-gallery-vhp__item">
        <img src="<?php echo sgh_img('du-an-vinh-hiep/vinh-hiep-khu-chau-rua-inox-bon-doi.webp'); ?>" alt="<?php echo esc_attr__('Khu chậu rửa inox bồn đôi bố trí góc chữ L tại bếp căng tin Vĩnh Hiệp', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="600" height="800">
        <figcaption class="pp-image-caption-shared"><?php echo esc_html__('Khu chậu rửa inox bồn đôi – sơ chế và vệ sinh chuyên dụng', 'saigonhoreca'); ?></figcaption>
      </figure>

      <figure class="pp-image-container-shared pp-gallery-vhp__item">
        <img src="<?php echo sgh_img('du-an-vinh-hiep/vinh-hiep-cong-nha-may-toan-canh.webp'); ?>" alt="<?php echo esc_attr__('Cổng và mặt tiền nhà máy cà phê Vĩnh Hiệp với xe container đang nhận hàng', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
        <figcaption class="pp-image-caption-shared"><?php echo esc_html__('Cổng nhà máy Vĩnh Hiệp – nhịp xuất hàng container của doanh nghiệp xuất khẩu cà phê', 'saigonhoreca'); ?></figcaption>
      </figure>
    </div>

    <div class="pp-gallery-vhp__quote">
      <span class="pp-gallery-vhp__quote-rule" aria-hidden="true"></span>
      <blockquote>
        <?php echo esc_html__('Không phô trương, không dư thừa, nhưng đủ tinh tế để đối tác quốc tế bước vào và hiểu ngay: đây là một nhà máy xuất khẩu cà phê hàng đầu – nghiêm túc, chuẩn mực và đầy bản sắc.', 'saigonhoreca'); ?>
      </blockquote>
      <p class="pp-gallery-vhp__quote-cite"><?php echo esc_html__('Dự án Vĩnh Hiệp – Coffee Lab × Saigon Horeca', 'saigonhoreca'); ?></p>
    </div>
  </div>
</section>
