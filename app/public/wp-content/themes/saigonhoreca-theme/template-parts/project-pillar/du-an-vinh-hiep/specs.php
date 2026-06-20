<?php
/**
 * Project Pillar — du-an-vinh-hiep
 * Section #5: specs — Showroom Coffee Lab & thiết bị inox trưng bày
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-vhp pp-section-vhp--alt pp-specs-vhp">
  <div class="pp-watermark-vhp pp-watermark-vhp--left" aria-hidden="true">04</div>
  <div class="pp-ambient-glow-vhp pp-ambient-glow-vhp--bottom-left" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <div class="pp-specs-vhp__grid">

      <!-- Media — terminal blueprint -->
      <div class="pp-specs-vhp__media">
        <span class="pp-specs-vhp__coord pp-specs-vhp__coord--tl" aria-hidden="true">SHOWROOM · COFFEE LAB · INOX</span>
        <span class="pp-specs-vhp__coord pp-specs-vhp__coord--br" aria-hidden="true">SUS 304 · MẠ MÀU</span>
        <div class="pp-image-container-shared pp-specs-vhp__image">
          <svg class="pp-specs-vhp__corner-svg" viewBox="0 0 100 100" preserveAspectRatio="none" fill="none" aria-hidden="true">
            <path d="M 0 14 L 0 0 L 14 0" stroke="var(--gold)" stroke-width="1.2"/>
            <path d="M 100 14 L 100 0 L 86 0" stroke="var(--gold)" stroke-width="1.2"/>
            <path d="M 0 86 L 0 100 L 14 100" stroke="var(--gold)" stroke-width="1.2"/>
            <path d="M 100 86 L 100 100 L 86 100" stroke="var(--gold)" stroke-width="1.2"/>
          </svg>
          <img src="<?php echo sgh_img('du-an-vinh-hiep/vinh-hiep-khu-chau-rua-inox-doi.webp'); ?>" alt="<?php echo esc_attr__('Khu chậu rửa inox đôi hai ngăn với vòi cổ cao tại bếp Vĩnh Hiệp', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="600" height="800">
          <div class="pp-image-caption-shared"><?php echo esc_html__('Chậu rửa inox đôi – inox hoàn thiện tinh xảo, đồng bộ', 'saigonhoreca'); ?></div>
        </div>
      </div>

      <!-- Feature terminal card -->
      <div class="pp-specs-vhp__text">
        <div class="pp-specs-vhp__card">
          <svg class="pp-specs-vhp__corner-svg" viewBox="0 0 100 100" preserveAspectRatio="none" fill="none" aria-hidden="true">
            <path d="M 0 12 L 0 0 L 12 0" stroke="var(--gold)" stroke-width="1.2"/>
            <path d="M 100 12 L 100 0 L 88 0" stroke="var(--gold)" stroke-width="1.2"/>
            <path d="M 0 88 L 0 100 L 12 100" stroke="var(--gold)" stroke-width="1.2"/>
            <path d="M 100 88 L 100 100 L 88 100" stroke="var(--gold)" stroke-width="1.2"/>
          </svg>

          <header>
            <div class="pp-badge-vhp">
              <span class="pp-badge-vhp__diamond" aria-hidden="true"></span>
              <?php echo esc_html__('Showroom Coffee Lab', 'saigonhoreca'); ?>
            </div>
            <h2 class="pp-specs-vhp__title">
              <?php echo esc_html__('Khi inox trở thành ngôn ngữ của thương hiệu', 'saigonhoreca'); ?>
            </h2>
            <div class="pp-divider-vhp" aria-hidden="true"></div>
          </header>

          <p class="pp-specs-vhp__intro"><?php echo esc_html__('Điểm nhấn đặc biệt của dự án ở khu vực showroom triển lãm – nơi Vĩnh Hiệp tiếp đón các đoàn khách tham quan và giới thiệu chiều sâu của cà phê Việt Nam.', 'saigonhoreca'); ?></p>

          <div class="pp-specs-vhp__features">
            <div class="pp-specs-vhp__feature">
              <div class="pp-specs-vhp__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                  <path d="M20 7h-9m3 4H5m16 4h-9m6 4H8" stroke-linecap="round"/>
                </svg>
              </div>
              <div class="pp-specs-vhp__feature-content">
                <h4><?php echo esc_html__('Inox cao cấp mang tính trưng bày', 'saigonhoreca'); ?></h4>
                <p><?php echo esc_html__('Toàn bộ không gian thiết bị chế biến và phục vụ cà phê dùng inox cao cấp, được xử lý hoàn thiện tinh xảo, đồng bộ – không mang dáng dấp công nghiệp khô cứng.', 'saigonhoreca'); ?></p>
              </div>
            </div>

            <div class="pp-specs-vhp__feature">
              <div class="pp-specs-vhp__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                  <rect x="3" y="4" width="18" height="16" rx="2"/>
                  <line x1="3" y1="10" x2="21" y2="10" stroke-linecap="round"/>
                </svg>
              </div>
              <div class="pp-specs-vhp__feature-content">
                <h4><?php echo esc_html__('Bàn cupping inox mạ màu', 'saigonhoreca'); ?></h4>
                <p><?php echo esc_html__('Trung tâm không gian là bàn cupping chuyên dụng, thi công hoàn toàn từ inox mạ màu, chọn tông tương đồng với hệ nhận diện thương hiệu Vĩnh Hiệp.', 'saigonhoreca'); ?></p>
              </div>
            </div>

            <div class="pp-specs-vhp__feature">
              <div class="pp-specs-vhp__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                  <path d="M12 3l8 4.5v9L12 21l-8-4.5v-9L12 3z" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
              <div class="pp-specs-vhp__feature-content">
                <h4><?php echo esc_html__('Kỹ thuật – thẩm mỹ – câu chuyện', 'saigonhoreca'); ?></h4>
                <p><?php echo esc_html__('Đây không chỉ là bàn thử nếm, mà là một tuyên ngôn hình ảnh – nơi kỹ thuật, thẩm mỹ và câu chuyện thương hiệu cùng tồn tại hài hòa.', 'saigonhoreca'); ?></p>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>
