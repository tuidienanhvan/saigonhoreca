<?php
/**
 * Project Pillar — grand-marble-thuong-hieu-banh-cao-cap-nhat-ban
 * Section #5: specs — technical drawing collage + equipment list.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-gmarb pp-section-gmarb--alt pp-specs-gmarb scroll-reveal">
  <div class="pp-gmarb-glow pp-gmarb-glow--tr" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <div class="pp-split-gmarb pp-specs-gmarb__layout">
      <div class="pp-split-gmarb__body scroll-reveal reveal-left">
        <div class="pp-gmarb-ornament" aria-hidden="true"></div>
        <span class="pp-text-gmarb__eyebrow"><?php echo esc_html__('Bản vẽ kỹ thuật', 'saigonhoreca'); ?></span>
        <div class="pp-text-gmarb__divider pp-text-gmarb__divider--dots" aria-hidden="true"></div>
        <h2 class="pp-text-gmarb__title"><?php echo esc_html__('Bố Trí Thiết Bị & Luồng Vận Hành', 'saigonhoreca'); ?></h2>
        <div class="pp-text-gmarb__body">
          <p><?php echo esc_html__('Khu vực nhận nguyên liệu được trang bị cân chính xác, bồn rửa tay, đèn đuổi côn trùng và phòng lạnh 10 mét khối duy trì 2°C để bảo quản dâu, cam, xoài trong điều kiện tốt nhất. Tiếp đến, khu Trộn và Cán Bột là trái tim của quy trình.', 'saigonhoreca'); ?></p>
          <ul class="pp-specs-gmarb__list">
            <li><?php echo esc_html__('Máy trộn bột và máy lăn bột Chanmag (Đài Loan) cho khu trộn – cán bột.', 'saigonhoreca'); ?></li>
            <li><?php echo esc_html__('Tủ lạnh và tủ đông Hoshizaki (Nhật Bản) cùng bồn rửa, kệ treo tường inox.', 'saigonhoreca'); ?></li>
            <li><?php echo esc_html__('Phòng lạnh 10m³ giữ 2°C cho khu tiếp nhận và bảo quản nguyên liệu tươi.', 'saigonhoreca'); ?></li>
            <li><?php echo esc_html__('Bố trí sáu khu vực chức năng theo luồng một chiều, tối ưu vệ sinh và năng suất.', 'saigonhoreca'); ?></li>
          </ul>
        </div>
      </div>
      <div class="pp-split-gmarb__media pp-specs-gmarb__media scroll-reveal reveal-right delay-150">
        <div class="pp-specs-gmarb__coord pp-specs-gmarb__coord--tl" aria-hidden="true">SYS_COORD / BAKERY</div>
        <div class="pp-specs-gmarb__coord pp-specs-gmarb__coord--br" aria-hidden="true">CAD_v2.0 / SCALE 1:50</div>

        <div class="pp-specs-gmarb__collage">
          <div class="pp-image-container-shared pp-specs-gmarb__frame pp-specs-gmarb__frame--main">
            <img src="<?php echo sgh_img('grand-marble-thuong-hieu-banh-cao-cap-nhat-ban/grand-marble-ban-ve-bo-tri-thiet-bi.png'); ?>" alt="<?php echo esc_attr__('Bản vẽ bố trí thiết bị tổng thể phòng làm bánh Grand Marble', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1322" height="852">
            <div class="pp-image-caption-shared"><?php echo esc_html__('Bản vẽ bố trí thiết bị tổng thể, phân chia sáu khu vực chức năng theo luồng vận hành một chiều của phòng làm bánh.', 'saigonhoreca'); ?></div>
          </div>
          <div class="pp-image-container-shared pp-specs-gmarb__frame pp-specs-gmarb__frame--float">
            <img src="<?php echo sgh_img('grand-marble-thuong-hieu-banh-cao-cap-nhat-ban/grand-marble-ban-ve-khu-vuc-1.png'); ?>" alt="<?php echo esc_attr__('Bản vẽ chi tiết khu vực tiếp nhận nguyên liệu Grand Marble', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="975" height="595">
            <div class="pp-image-caption-shared"><?php echo esc_html__('Bản vẽ chi tiết một khu vực chức năng, thể hiện vị trí cân, bồn rửa, phòng lạnh và hệ kệ inox.', 'saigonhoreca'); ?></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
