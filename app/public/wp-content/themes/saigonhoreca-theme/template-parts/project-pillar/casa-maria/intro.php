<?php
/**
 * Project Pillar — casa-maria
 * Section #2: intro (Thiết kế đan xen nghệ thuật Alternating Storytelling)
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-cm pp-section-cm--alt">
  
  <!-- Lưới kỹ thuật AutoCAD Địa Trung Hải mờ chạy ngầm toàn nền -->
  <svg class="pp-cm-bg-grid" width="100%" height="100%" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
    <defs>
      <pattern id="cm-grid-pattern" width="40" height="40" patternUnits="userSpaceOnUse">
        <path d="M 40 0 L 0 0 0 40" fill="none" stroke="rgba(212, 175, 55, 0.015)" stroke-width="1"/>
      </pattern>
    </defs>
    <rect width="100%" height="100%" fill="url(#cm-grid-pattern)" />
  </svg>

  <!-- ĐƯỜNG DẪN KÝ THUẬT KẾT NỐI TOÀN TRANG (Narrative Connecting Line) -->
  <div class="pp-cm-narrative-line"></div>

  <div class="pp-container-shared">

    <!-- TIÊU ĐỀ LỚN MỞ ĐẦU (Intro Header) -->
    <div class="pp-cm-intro__header">
      <span class="pp-text-cm__divider" aria-hidden="true"></span>
      <h2 class="pp-text-cm__title" style="max-width: 46rem; text-align: left; margin-bottom: 2rem;">
        <?php echo esc_html__('Bản giao hưởng của Tapas, Rượu vang và một nhịp điệu vận hành hoàn hảo', 'saigonhoreca'); ?>
      </h2>
      <div class="pp-text-cm__lead">
        <p><?php echo esc_html__('Nơi ẩm thực Tây Ban Nha hiện đại được tôn vinh từ những điều nhỏ nhất.', 'saigonhoreca'); ?></p>
      </div>
    </div>

    <!-- DÒNG CHẢY CÂU CHUYỆN (Alternating Asymmetric Rows) -->
    <div class="pp-cm-intro__story">

      <!-- HÀNG 1: BẢN SẮC ĐỊA TRUNG HẢI (Chữ Trái 38% / Ảnh Phải 62%) -->
      <div class="pp-cm-row pp-cm-row--layout-med pp-cm-row--asymmetric-1">
        
        <!-- Cột chữ -->
        <div class="pp-cm-row__text">
          <div class="pp-intro-card-cm pp-intro-card-cm--med">
            <h3 class="pp-cm-card-heading"><?php echo esc_html__('Bản sắc & Không gian', 'saigonhoreca'); ?></h3>
            <div class="pp-intro-card-cm__body">
              <div class="pp-cm-highlight-quote">
                <p>
                  <span class="pp-cm-dropcap">T</span><?php echo esc_html__('ọa lạc tại 15D Ngô Quang Huy, Thảo Điền, TP.HCM, nhà hàng theo mô hình Vinos – Tapas Spanish Restaurant, nơi ẩm thực Tây Ban Nha được thể hiện qua những phần tapas tinh gọn, wine tasting và cafe.', 'saigonhoreca'); ?>
                </p>
              </div>
              <p class="pp-cm-desc-p">
                <?php echo esc_html__('Tất cả đặt trong một không gian gần gũi, mang đậm tinh thần Địa Trung Hải hiện đại, nơi gỗ ấm, gạch nung và ánh sáng vàng hoàng hôn giao thoa một cách hài hòa.', 'saigonhoreca'); ?>
              </p>
            </div>
          </div>
        </div>

        <!-- Cột ảnh (Ảnh lớn quầy bar Địa Trung Hải uốn lượn có góc và viền AutoCAD mờ) -->
        <div class="pp-cm-row__media pp-cm-media-med">
          <!-- SVG nét vẽ thiết bị CAD kỹ thuật chạy mờ -->
          <svg class="pp-cm-svg-frame-decor" viewBox="0 0 400 400" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <rect class="decor-rect-rotate" x="20" y="20" width="360" height="360" rx="8" stroke="rgba(212, 175, 55, 0.08)" stroke-width="1"/>
            <circle class="decor-circle-scale" cx="200" cy="200" r="160" stroke="rgba(212, 175, 55, 0.04)" stroke-width="1" stroke-dasharray="6 4"/>
          </svg>

          <!-- Khung ảnh sang xịn với 4 góc kim loại và overlay mờ -->
          <div class="pp-cm-luxury-frame pp-cm-luxury-frame--med">
            <span class="pp-cm-corner pp-cm-corner--tl"></span>
            <span class="pp-cm-corner pp-cm-corner--tr"></span>
            <span class="pp-cm-corner pp-cm-corner--bl"></span>
            <span class="pp-cm-corner pp-cm-corner--br"></span>
            <div class="pp-image-container-shared">
              <img src="<?php echo sgh_img('casa-maria/casa-maria-khong-gian-bar-dai-dia-trung-hai.jpg'); ?>" alt="<?php echo esc_attr__('Không gian quầy bar Casa Maria', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
              <div class="pp-image-caption-shared">
                <?php echo esc_html__('Không gian tapas bar ấm cúng mang đậm tinh thần Địa Trung Hải', 'saigonhoreca'); ?>
              </div>
            </div>
          </div>
        </div>

      </div>

      <!-- HÀNG 2: TRIẾT LÝ ĐỐI TÁC (Ảnh Trái 58% / Chữ Phải 42%) -->
      <div class="pp-cm-row pp-cm-row--layout-kitchen pp-cm-row--asymmetric-2">
        
        <!-- Cột ảnh (Toàn cảnh bếp chính panorama) -->
        <div class="pp-cm-row__media pp-cm-media-kitchen">
          <svg class="pp-cm-circuit-decor" viewBox="0 0 500 350" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path d="M 10 10 L 490 10 L 490 340 L 10 340 Z" stroke="rgba(212, 175, 55, 0.08)" stroke-width="1" stroke-dasharray="10 10"/>
            <circle cx="10" cy="10" r="3" fill="var(--gold)"/>
            <circle cx="490" cy="340" r="3" fill="var(--gold)"/>
          </svg>

          <div class="pp-cm-luxury-frame pp-cm-luxury-frame--kitchen pp-cm-luxury-frame--panoramic">
            <span class="pp-cm-corner pp-cm-corner--tl"></span>
            <span class="pp-cm-corner pp-cm-corner--tr"></span>
            <span class="pp-cm-corner pp-cm-corner--bl"></span>
            <span class="pp-cm-corner pp-cm-corner--br"></span>
            <div class="pp-image-container-shared">
              <img src="<?php echo sgh_img('casa-maria/casa-maria-dau-bep-thao-tac-trong-bep.jpg'); ?>" alt="<?php echo esc_attr__('Đầu bếp thao tác trong hệ thống bếp chính Casa Maria', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
              <div class="pp-image-caption-shared">
                <?php echo esc_html__('Đầu bếp vận hành chuyên nghiệp trong không gian bếp tapas mở', 'saigonhoreca'); ?>
              </div>
            </div>
          </div>
        </div>

        <!-- Cột chữ -->
        <div class="pp-cm-row__text">
          <div class="pp-intro-card-cm pp-intro-card-cm--kitchen">
            <div class="pp-cm-narrative-node" aria-hidden="true">01</div>
            <h3 class="pp-cm-card-heading"><?php echo esc_html__('Thiết kế bếp Tapas', 'saigonhoreca'); ?></h3>
            
            <div class="pp-intro-card-cm__body">
              <div class="pp-cm-highlight-quote">
                <p>
                  <span class="pp-cm-dropcap">V</span><?php echo esc_html__('ới Casa Maria, câu hỏi đầu tiên không phải là "dùng thiết bị gì", mà là bếp phải vận hành thế nào để phục vụ đúng tinh thần tapas và wine. Tapas đòi hỏi tốc độ, sự linh hoạt và tính nhất quán cao.', 'saigonhoreca'); ?>
                </p>
              </div>
              <p class="pp-cm-desc-p">
                <?php echo esc_html__('Trong dự án này, Saigon Horeca tham gia sâu từ tư duy concept đến triển khai kỹ thuật, đảm bảo toàn bộ trải nghiệm của khách đều được "hậu thuẫn" bởi một hệ thống bếp – bar vận hành mượt mà phía sau.', 'saigonhoreca'); ?>
              </p>
            </div>
          </div>
        </div>

      </div>

      <!-- HÀNG 3: THIẾT BỊ KHÔI HÀI HÒA (Chữ Trái 38% / Ảnh Phải 62% So Le Nghệ Thuật) -->
      <div class="pp-cm-row pp-cm-row--layout-devices pp-cm-row--asymmetric-3">
        
        <!-- Cột chữ -->
        <div class="pp-cm-row__text">
          <div class="pp-intro-card-cm pp-intro-card-cm--devices">
            <div class="pp-cm-narrative-node" aria-hidden="true">02</div>
            <h3 class="pp-cm-card-heading"><?php echo esc_html__('Thiết bị Âu tinh chỉnh', 'saigonhoreca'); ?></h3>
            
            <div class="pp-intro-card-cm__body">
              <p class="pp-cm-desc-p">
                <?php echo esc_html__('Hệ thống bếp Âu tại Casa Maria được thiết kế để phục vụ nhiều món nhỏ, ra liên tục nhưng không tạo áp lực lên đầu bếp. Không gian bếp được tổ chức gọn, rõ luồng, hạn chế di chuyển thừa và tránh chồng chéo thao tác.', 'saigonhoreca'); ?>
              </p>
              <p class="pp-cm-desc-p">
                <?php echo esc_html__('Thiết bị bếp Âu do Saigon Horeca cung cấp được lựa chọn theo tiêu chí vừa đủ: đủ công suất cho giờ cao điểm, đủ linh hoạt để xử lý đa dạng món tapas, nhưng không làm bếp trở nên nặng nề hay dư thừa.', 'saigonhoreca'); ?>
              </p>
            </div>
          </div>
        </div>

        <!-- Cột ảnh (Grid 2 ảnh so le chồng lệch nghệ thuật cực đỉnh) -->
        <div class="pp-cm-row__media pp-cm-media-devices">
          <!-- Ảnh thiết bị 1 (Bếp nấu chính) -->
          <div class="pp-cm-luxury-frame pp-cm-luxury-frame--device-1">
            <span class="pp-cm-corner pp-cm-corner--tl"></span>
            <span class="pp-cm-corner pp-cm-corner--tr"></span>
            <span class="pp-cm-corner pp-cm-corner--bl"></span>
            <span class="pp-cm-corner pp-cm-corner--br"></span>
            <div class="pp-image-container-shared">
              <img src="<?php echo sgh_img('casa-maria/casa-maria-he-thong-bep-nau-au-inox.jpg'); ?>" alt="<?php echo esc_attr__('Hệ thống bếp Âu Casa Maria', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
              <div class="pp-image-caption-shared">
                <?php echo esc_html__('Hệ thống bếp Âu inox cao cấp bố trí khoa học, tối ưu quy trình', 'saigonhoreca'); ?>
              </div>
            </div>
          </div>

          <!-- Ảnh thiết bị 2 (Chi tiết lắp đặt bếp) -->
          <div class="pp-cm-luxury-frame pp-cm-luxury-frame--device-2">
            <span class="pp-cm-corner pp-cm-corner--tl"></span>
            <span class="pp-cm-corner pp-cm-corner--tr"></span>
            <span class="pp-cm-corner pp-cm-corner--bl"></span>
            <span class="pp-cm-corner pp-cm-corner--br"></span>
            <div class="pp-image-container-shared">
              <img src="<?php echo sgh_img('casa-maria/casa-maria-thiet-bi-bep-au-griddle-tu-lanh.jpg'); ?>" alt="<?php echo esc_attr__('Chi tiết thiết bị bếp nấu Casa Maria', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
              <div class="pp-image-caption-shared">
                <?php echo esc_html__('Bếp từ, griddle và tủ mát công nghiệp tích hợp tiện lợi', 'saigonhoreca'); ?>
              </div>
            </div>
          </div>
        </div>

      </div>

    </div>

    <!-- KHỐI ĐẶC TÍNH SƯƠNG MỜ ĐỊA TRUNG HẢI (Mediterranean Feature Cards) -->
    <div class="pp-cm-features-grid">
      
      <div class="pp-cm-feature-card">
        <div class="pp-cm-feature-card__header">
          <svg class="pp-cm-feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
            <polyline points="20 6 9 17 4 12"/>
          </svg>
          <h4><?php echo esc_html__('Quy trình tinh chỉnh gọn - mở', 'saigonhoreca'); ?></h4>
        </div>
        <p><?php echo esc_html__('Cho phép đầu bếp phối hợp, xử lý nhiều món tapas cùng lúc mà không lo chồng chéo thao tác.', 'saigonhoreca'); ?></p>
      </div>

      <div class="pp-cm-feature-card">
        <div class="pp-cm-feature-card__header">
          <svg class="pp-cm-feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
            <polyline points="20 6 9 17 4 12"/>
          </svg>
          <h4><?php echo esc_html__('Thiết bị Âu chọn lọc kỹ lưỡng', 'saigonhoreca'); ?></h4>
        </div>
        <p><?php echo esc_html__('Đáp ứng tiêu chí: Vừa đủ công suất, vừa đủ linh hoạt, không dư thừa, tối ưu hóa diện tích.', 'saigonhoreca'); ?></p>
      </div>

      <div class="pp-cm-feature-card">
        <div class="pp-cm-feature-card__header">
          <svg class="pp-cm-feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
            <polyline points="20 6 9 17 4 12"/>
          </svg>
          <h4><?php echo esc_html__('Hậu thuẫn nhịp điệu Tapas', 'saigonhoreca'); ?></h4>
        </div>
        <p><?php echo esc_html__('Đảm bảo ra món liên tục, đều tay, giúp thực khách luôn tận hưởng trọn vẹn từng khoảnh khắc.', 'saigonhoreca'); ?></p>
      </div>

    </div>

    <!-- Quote chốt hạ nghệ thuật -->
    <div class="pp-cm-intro-quote-panel">
      <p><strong><?php echo esc_html__('Căn bếp tại Casa Maria không phô trương, mà lặng lẽ tinh chỉnh để trở thành nền tảng vận hành vững chãi cho một phong cách ẩm thực Tây Ban Nha đầy cảm xúc.', 'saigonhoreca'); ?></strong></p>
    </div>

  </div>
</section>
