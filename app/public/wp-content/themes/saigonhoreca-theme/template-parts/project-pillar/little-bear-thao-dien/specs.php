<?php
/**
 * Project Pillar — little-bear-thao-dien
 * Section #5: split-reverse
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp__section--alt pp-specs-lb scroll-reveal">
  <!-- Glow mờ chạy ngầm -->
  <div class="pp-specs-lb__glow" aria-hidden="true"></div>

  <!-- SVG Họa tiết AutoCAD kích thước đo M&E chạy ngầm sắc nét -->
  <svg class="pp-lb-specs-blueprint" viewBox="0 0 400 300" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
    <g stroke="rgba(245, 166, 35, 0.018)" stroke-width="0.5">
      <!-- Lưới chấm tọa độ -->
      <path d="M 0 50 L 400 50 M 0 100 L 400 100 M 0 150 L 400 150 M 0 200 L 400 200 M 0 250 L 400 250" stroke-dasharray="2 4"/>
      <path d="M 50 0 L 50 300 M 100 0 L 100 300 M 150 0 L 150 300 M 200 0 L 200 300 M 250 0 L 250 300 M 300 0 L 300 300 M 350 0 L 350 300" stroke-dasharray="2 4"/>
      <!-- Đường dải kích thước đo AutoCAD -->
      <line x1="40" y1="280" x2="360" y2="280" stroke="rgba(245, 166, 35, 0.04)" stroke-width="0.75"/>
      <line x1="40" y1="275" x2="40" y2="285"/>
      <line x1="360" y1="275" x2="360" y2="285"/>
      <line x1="40" y1="280" x2="50" y2="277"/>
      <line x1="40" y1="280" x2="50" y2="283"/>
      <line x1="360" y1="280" x2="350" y2="277"/>
      <line x1="360" y1="280" x2="350" y2="283"/>
    </g>
    <text x="200" y="276" font-family="monospace" font-size="6" fill="rgba(245, 166, 35, 0.08)" text-anchor="middle">LBT_SECTION_SPAN: 1200mm</text>
  </svg>

  <div class="pp-container-shared">
    <div class="pp-specs-lb__grid">
      
      <!-- Cụm collage 2 ảnh xếp chồng lệch góc nghệ thuật và nhãn tọa độ M&E -->
      <div class="pp-specs-lb__media-col">
        <div class="pp-specs-lb__stamp pp-specs-lb__stamp--tl" aria-hidden="true">SYS_COORD: 10.7933° N, 106.7025° E</div>
        <div class="pp-specs-lb__stamp pp-specs-lb__stamp--tr" aria-hidden="true">LITTLE_BEAR_04 / CAD_v2.0</div>
        <div class="pp-specs-lb__stamp pp-specs-lb__stamp--bl" aria-hidden="true">SCALE: 1:35 @ A3</div>
        <div class="pp-specs-lb__stamp pp-specs-lb__stamp--br" aria-hidden="true">SAIGON HORECA © 2026</div>

        <div class="pp-specs-lb__collage">
          <!-- Ảnh nền chính (Bản vẽ thực tế line bếp) -->
          <div class="pp-image-container-shared pp-specs-lb__img-under">
            <img src="<?php echo sgh_img('little-bear-thao-dien/little-bear-thao-dien-ban-ngan-keo-tumat.jpg'); ?>" alt="<?php echo esc_attr__('Hệ thống bếp nướng than hoa, lò hấp nướng đa năng Rational và bếp phẳng hồng ngoại tại Little Bear', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="600" height="800">
            <div class="pp-image-caption-shared"><?php echo esc_html__('Bố cục bếp M&E đỉnh cao tích hợp lò hấp nướng đa năng Rational Combi hiện đại cùng bếp hồng ngoại phẳng inox 304, mang đến giải pháp vận hành an toàn, vệ sinh và tiết kiệm năng lượng tối đa.', 'saigonhoreca'); ?></div>
          </div>

          <!-- Ảnh chồng đè góc (Cận cảnh bếp phẳng) -->
          <div class="pp-image-container-shared pp-specs-lb__img-over">
            <div class="pp-specs-lb__img-over-inner">
              <img src="<?php echo sgh_img('little-bear-thao-dien/little-bear-thao-dien-chi-tiet-bep-inox.jpg'); ?>" alt="<?php echo esc_attr__('Cận cảnh mặt bếp hồng ngoại phẳng cao cấp và chảo thép đen carbon tại khu vực bếp nóng Little Bear', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="600" height="800">
            </div>
            <div class="pp-image-caption-shared"><?php echo esc_html__('Chi tiết gia công cạnh tủ inox 304 phẳng mịn không tì vết, tối ưu hóa vệ sinh và độ bền bỉ trong bếp nóng.', 'saigonhoreca'); ?></div>
          </div>
        </div>
      </div>

      <!-- Cột mô tả thông số kỹ thuật M&E -->
      <div class="pp-specs-lb__text-col">
        <div class="pp-specs-card-lb">
          <!-- Focus Bracket SVG trang trí 4 góc -->
          <svg class="pp-lb-focus-bracket pp-lb-focus-bracket--tl" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2"><path d="M 8 2 L 2 2 L 2 8"/></svg>
          <svg class="pp-lb-focus-bracket pp-lb-focus-bracket--tr" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2"><path d="M 16 2 L 22 2 L 22 8"/></svg>
          <svg class="pp-lb-focus-bracket pp-lb-focus-bracket--bl" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2"><path d="M 8 22 L 2 22 L 2 16"/></svg>
          <svg class="pp-lb-focus-bracket pp-lb-focus-bracket--br" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2"><path d="M 16 22 L 22 22 L 22 16"/></svg>

          <header class="pp-specs-card-lb__header">
            <span class="pp-specs-card-tag"><?php echo esc_html__('Quy Chuẩn Kỹ Thuật', 'saigonhoreca'); ?></span>
            <h2 class="pp-specs-card-lb__title"><?php echo esc_html__('Giải pháp của Saigon Horeca:', 'saigonhoreca'); ?></h2>
            <div class="pp-specs-card-lb__divider" aria-hidden="true"></div>
          </header>

          <div class="pp-specs-card-lb__body">
            <p class="pp-specs-card-lb__intro-text"><?php echo esc_html__('Biến mỗi mét vuông nhà bếp thành một nền tảng cho sự bứt phá trong nghệ thuật ẩm thực. Bếp của Little Bear được xây dựng theo quy trình một chiều nghiêm ngặt và tối ưu hóa không gian.', 'saigonhoreca'); ?></p>

            <div class="pp-specs-features">
              
              <div class="pp-specs-feature-item">
                <div class="pp-specs-feature-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                    <line x1="9" y1="3" x2="9" y2="21"/>
                    <line x1="15" y1="3" x2="15" y2="21"/>
                  </svg>
                </div>
                <div class="pp-specs-feature-text">
                  <h4><?php echo esc_html__('Khu chế biến trung tâm', 'saigonhoreca'); ?></h4>
                  <p><?php echo esc_html__('Hệ thống hai bàn mát ngăn kéo inox 304 được đặt lưng đối lưng, tối đa hóa việc tận dụng diện tích trống và lưu trữ thực phẩm tươi sạch trong tầm tay thao tác.', 'saigonhoreca'); ?></p>
                </div>
              </div>

              <div class="pp-specs-feature-item">
                <div class="pp-specs-feature-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                  </svg>
                </div>
                <div class="pp-specs-feature-text">
                  <h4><?php echo esc_html__('Phân khu bếp nóng khép kín', 'saigonhoreca'); ?></h4>
                  <p><?php echo esc_html__('Được bố trí gọn gàng phía bên trái line vận hành, trang bị lò hấp nướng đa năng cùng bếp phẳng hồng ngoại công suất cao, tạo dòng nhiệt tức thì hỗ trợ tối đa cho đầu bếp.', 'saigonhoreca'); ?></p>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>
