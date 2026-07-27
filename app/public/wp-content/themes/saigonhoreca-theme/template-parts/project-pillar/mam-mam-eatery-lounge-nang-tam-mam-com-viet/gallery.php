<?php
/**
 * Project Pillar — mam-mam-eatery-lounge-nang-tam-mam-com-viet
 * Section #6: gallery slot — GOLD editorial mosaic ("thư viện hình ảnh")
 * followed by the merged closing statement ("lời kết") block.
 * Per project-pillar-standard §6, the closing philosophy is merged into the
 * gallery slot rather than a separate related/cta file.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp-gallery-section-mam">

  <!-- Ambient gold glow + faint editorial numeral watermark -->
  <div class="pp-gallery-section-mam__glow" aria-hidden="true"></div>
  <div class="pp-gallery-section-mam__numeral" aria-hidden="true">04</div>

  <div class="pp-container-shared">

    <!-- Gallery header (badge + title + mâm-tray divider) -->
    <header class="pp-text-mam pp-text-mam--center pp-gallery-section-mam__header scroll-reveal">
      <span class="pp-split-mam__tag pp-gallery-section-mam__tag">
        <?php echo esc_html__('Thư viện dự án', 'saigonhoreca'); ?>
      </span>
      <span class="pp-text-mam__divider" aria-hidden="true"></span>
      <h2 class="pp-text-mam__title">
        <?php echo esc_html__('Khoảnh khắc Mâm Mâm – Không gian & Gian bếp', 'saigonhoreca'); ?>
      </h2>
      <div class="pp-text-mam__body">
        <p><?php echo esc_html__('Từ chiếc mâm tròn sum vầy nơi phòng ăn đến dàn inox sáng bóng trong gian bếp – một bộ sưu tập hình ảnh thực tế Saigon Horeca kiến tạo cho Mâm Mâm.', 'saigonhoreca'); ?></p>
      </div>
    </header>

    <!-- GOLD UNIFORM GRID (1 → 2 → 3 cols · all tiles 4/3, zero voids) -->
    <div class="pp-gallery-mam-mosaic scroll-reveal">

      <!-- Tile 4 -->
      <div class="pp-image-container-shared pp-gallery-mam-mosaic__item">
        <img src="<?php echo sgh_img('mam-mam-eatery-lounge-nang-tam-mam-com-viet/mam-mam-noi-that-lounge-tran-elip-quay-bar-bep-mo.webp'); ?>" alt="<?php echo esc_attr__('Nội thất lounge Mâm Mâm: trần giả elip màu đồng, quầy bar cong và khu bếp mở inox nhìn qua ô pass-through', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
        <div class="pp-image-caption-shared"><?php echo esc_html__('Mảng trần giả elip ốp đồng khổng lồ – biểu tượng chiếc mâm sum vầy vươn trên quầy bar cong và khu bếp mở', 'saigonhoreca'); ?></div>
      </div>

      <!-- Tile 5 -->
      <div class="pp-image-container-shared pp-gallery-mam-mosaic__item">
        <img src="<?php echo sgh_img('mam-mam-eatery-lounge-nang-tam-mam-com-viet/mam-mam-vach-ngan-hoa-bon-canh-hat-sang-phong-an.webp'); ?>" alt="<?php echo esc_attr__('Vách ngăn trang trí họa tiết hoa bốn cánh ốp đá hắt sáng LED ấm trong phòng ăn Mâm Mâm, kết hợp trần dốc trắng và khu ghế bọc xanh', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
        <div class="pp-image-caption-shared"><?php echo esc_html__('Vách ngăn họa tiết hoa bốn cánh ốp đá, hắt sáng LED ấm – nét trang trí mềm mại dẫn lối vào khu phòng ăn', 'saigonhoreca'); ?></div>
      </div>

    </div>
  </div>

  <!-- Closing "lời kết" statement (kept from original gallery slot) -->
  <div class="pp-gallery-section-mam__tray" aria-hidden="true"></div>
  <div class="pp-container-shared">
    <div class="pp-gallery-section-mam__closing scroll-reveal">
      <span class="pp-gallery-section-mam__mark" aria-hidden="true">“ ”</span>
      <blockquote class="pp-gallery-section-mam__quote">
        <?php echo esc_html__('Đưa ẩm thực Việt ra thế giới – bắt đầu từ một mâm cơm, một gian bếp, và cả tấm lòng của người làm bếp.', 'saigonhoreca'); ?>
      </blockquote>
      <p class="pp-gallery-section-mam__sign"><?php echo esc_html__('Mâm Mâm Eatery & Lounge × Saigon Horeca', 'saigonhoreca'); ?></p>
    </div>
  </div>
</section>
