<?php
/**
 * Project Pillar — du-an-vinh-hiep
 * Section #4: partnership — Khu bếp nhân viên (suất ăn công nghiệp)
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-vhp pp-partnership-vhp">
  <div class="pp-watermark-vhp" aria-hidden="true">03</div>
  <div class="pp-ambient-glow-vhp pp-ambient-glow-vhp--top-right" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <div class="pp-partnership-vhp__grid">

      <!-- Text + dashboard -->
      <div class="pp-partnership-vhp__text">
        <div class="pp-partnership-vhp__card">
          <header>
            <div class="pp-badge-vhp">
              <span class="pp-badge-vhp__diamond" aria-hidden="true"></span>
              <?php echo esc_html__('Bếp nhân viên', 'saigonhoreca'); ?>
            </div>
            <h2 class="pp-partnership-vhp__title">
              <?php echo esc_html__('Hiệu quả vận hành cho suất ăn công nghiệp hiện đại', 'saigonhoreca'); ?>
            </h2>
            <div class="pp-divider-vhp" aria-hidden="true"></div>
          </header>

          <div class="pp-partnership-vhp__body">
            <p class="pp-partnership-vhp__lead"><?php echo esc_html__('Công suất lớn, tốc độ nhanh và tính ổn định cao.', 'saigonhoreca'); ?></p>
            <p><?php echo esc_html__('Bên cạnh khu bếp dành riêng cho ban lãnh đạo, Saigon Horeca cũng tham gia tư vấn và triển khai khu bếp phục vụ nhân viên nhà máy – nơi đòi hỏi công suất lớn, tốc độ nhanh và tính ổn định cao.', 'saigonhoreca'); ?></p>
            <p><?php echo esc_html__('Với đặc thù nhà máy sản xuất, suất ăn nhân viên phải được phục vụ đúng giờ, đúng định lượng và đảm bảo dinh dưỡng. Saigon Horeca đã bố trí hệ thống bếp Á công nghiệp công suất lớn, nồi nấu phở điện tự động, tủ hấp cơm đa năng và hệ thống giữ nóng thức ăn liên tục.', 'saigonhoreca'); ?></p>
            <p><?php echo esc_html__('Toàn bộ quy trình được tổ chức theo luồng một chiều khép kín, giúp hạn chế giao chéo, giảm thời gian chờ và nâng cao năng suất phục vụ.', 'saigonhoreca'); ?></p>
          </div>

          <div class="pp-partnership-vhp__stats">
            <div class="pp-partnership-vhp__stat">
              <span class="pp-partnership-vhp__stat-value"><?php echo esc_html__('Bếp Á', 'saigonhoreca'); ?></span>
              <span class="pp-partnership-vhp__stat-label"><?php echo esc_html__('Công nghiệp công suất lớn', 'saigonhoreca'); ?></span>
            </div>
            <div class="pp-partnership-vhp__stat">
              <span class="pp-partnership-vhp__stat-value"><?php echo esc_html__('1 chiều', 'saigonhoreca'); ?></span>
              <span class="pp-partnership-vhp__stat-label"><?php echo esc_html__('Luồng vận hành khép kín', 'saigonhoreca'); ?></span>
            </div>
          </div>
        </div>
      </div>

      <!-- Staggered media -->
      <div class="pp-partnership-vhp__media">
        <div class="pp-image-container-shared pp-partnership-vhp__image pp-partnership-vhp__image--main">
          <span class="pp-partnership-vhp__tag">KIT—B · STAFF DINING</span>
          <img src="<?php echo sgh_img('du-an-vinh-hiep/vinh-hiep-bep-xao-tu-doi-day-bep-inox.webp'); ?>" alt="<?php echo esc_attr__('Dãy bếp xào từ đôi và bếp từ phẳng inox công suất lớn cho bếp căng tin Vĩnh Hiệp', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
          <div class="pp-image-caption-shared"><?php echo esc_html__('Dãy bếp xào từ công nghiệp – công suất lớn cho nhân viên nhà máy', 'saigonhoreca'); ?></div>
        </div>
        <div class="pp-image-container-shared pp-partnership-vhp__image pp-partnership-vhp__image--float">
          <img src="<?php echo sgh_img('du-an-vinh-hiep/vinh-hiep-khu-chau-rua-inox-goc-chu-l.webp'); ?>" alt="<?php echo esc_attr__('Khu chậu rửa inox góc chữ L một bồn rửa lớn kèm vòi cổ ngỗng tại bếp Vĩnh Hiệp', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="600" height="800">
          <div class="pp-image-caption-shared"><?php echo esc_html__('Khu chậu rửa inox góc chữ L – vệ sinh an toàn thực phẩm', 'saigonhoreca'); ?></div>
        </div>
      </div>

    </div>
  </div>
</section>
