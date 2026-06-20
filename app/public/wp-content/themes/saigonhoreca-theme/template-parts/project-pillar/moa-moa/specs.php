<?php
/**
 * Project Pillar — moa-moa (Moa Moa Pasta Club)
 * Section #5: specs — thiết kế công năng bếp Âu & thiết bị lạnh.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-moa pp-section-moa--alt pp-moa-specs">
  <div class="pp-ambient-glow-moa pp-ambient-glow-moa--bottom-left" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <div class="pp-grid-moa">

      <div class="pp-grid-moa__media moa-specs__side">
        <div class="pp-image-container-shared moa-specs__image-container">
          <div class="moa-specs__image-decor" aria-hidden="true"></div>
          <img src="<?php echo sgh_img('moa-moa/moa-moa-thit-ap-chao-khoai-tay-huong-thao.webp'); ?>" alt="<?php echo esc_attr__('Thịt bò áp chảo ăn kèm khoai tây chiên và hương thảo tại Moa Moa', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
          <div class="pp-image-caption-shared"><?php echo esc_html__('Thịt bò áp chảo cùng khoai tây vàng giòn và hương thảo – dấu ấn món Âu chuẩn vị tại Moa Moa', 'saigonhoreca'); ?></div>
        </div>
      </div>

      <div class="pp-grid-moa__text moa-specs__main">
        <div class="moa-specs__card">
          <header class="moa-specs__header">
            <div class="pp-badge-moa">
              <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2L14.85 9.15L22 12L14.85 14.85L12 22L9.15 14.85L2 12L9.15 9.15L12 2Z"/>
              </svg>
              <?php echo esc_html__('Thiết kế công năng', 'saigonhoreca'); ?>
            </div>
            <h2 class="pp-text-moa__title">
              <?php echo esc_html__('Hệ thống bếp Âu cao cấp – nền tảng cho chất lượng món Ý', 'saigonhoreca'); ?>
            </h2>
            <div class="pp-text-moa__divider" aria-hidden="true"></div>
          </header>

          <div class="moa-specs__body">
            <div class="moa-specs__feature">
              <div class="moa-specs__icon-wrapper">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                  <path d="M4 12h16M4 12a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2M6 8h.01M10 8h.01M14 8h.01M5 12v8m14-8v8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
              <div class="moa-specs__feature-content">
                <h4><?php echo esc_html__('Bếp Âu 6 họng & nồi luộc mì chuyên dụng', 'saigonhoreca'); ?></h4>
                <p><?php echo esc_html__('Trung tâm của gian bếp là bếp Âu 6 họng, cho phép xử lý đồng thời nhiều món pasta với yêu cầu nhiệt độ khác nhau. Nồi luộc mì chuyên dụng được bố trí hợp lý, giúp pasta luôn đạt độ al dente chuẩn mực, không bị gián đoạn trong giờ cao điểm.', 'saigonhoreca'); ?></p>
              </div>
            </div>

            <div class="moa-specs__feature">
              <div class="moa-specs__icon-wrapper">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                  <path d="M3 9h18M3 9a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2M3 9v10a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V9M8 13h.01M8 17h.01" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
              <div class="moa-specs__feature-content">
                <h4><?php echo esc_html__('"Xưởng thủ công thu nhỏ" cho pasta tươi', 'saigonhoreca'); ?></h4>
                <p><?php echo esc_html__('Khu bàn trộn bột và chế biến pasta tươi được thiết kế như một "xưởng thủ công thu nhỏ", nơi đầu bếp có đủ không gian thao tác, kiểm soát độ ẩm, nhiệt độ và kết cấu bột – những yếu tố quyết định linh hồn của pasta tươi.', 'saigonhoreca'); ?></p>
              </div>
            </div>

            <div class="moa-specs__feature">
              <div class="moa-specs__icon-wrapper">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                  <rect x="4" y="2" width="16" height="20" rx="2"/>
                  <path d="M4 12h16M9 6v2M9 16v2" stroke-linecap="round"/>
                </svg>
              </div>
              <div class="moa-specs__feature-content">
                <h4><?php echo esc_html__('Tủ đông – tủ mát dạng ngăn kéo', 'saigonhoreca'); ?></h4>
                <p><?php echo esc_html__('Ẩm thực Ý đề cao nguyên liệu tươi và xử lý nhanh. Moa Moa sử dụng tủ đông – tủ mát dạng ngăn kéo, cho phép đầu bếp lấy nguyên liệu ngay tại vị trí thao tác mà không phá vỡ nhịp làm việc – thể hiện rõ tư duy thiết kế bếp lấy con người và quy trình làm trung tâm.', 'saigonhoreca'); ?></p>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>
