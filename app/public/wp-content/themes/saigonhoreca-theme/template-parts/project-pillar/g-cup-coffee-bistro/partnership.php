<?php
/**
 * Project Pillar — g-cup-coffee-bistro
 * Section #4: partnership (Challenges of Building in High-End Complexes)
 * Được thiết kế lại toàn diện theo phong cách tạp chí kiến trúc bất đối xứng (ArchDaily style)
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-gcb pp-gcb-partnership-section" id="gcb-partnership-anchor">
  <!-- Hoa văn CAD đường ống thông gió và cao độ shophouse mờ nền -->
  <div class="pp-gcb-partnership-cad-decor" aria-hidden="true">
    <svg viewBox="0 0 1200 600" fill="none" xmlns="http://www.w3.org/2000/svg">
      <!-- Hệ trục tọa độ và đường dóng kỹ thuật -->
      <line x1="100" y1="50" x2="1100" y2="50" stroke="var(--gold)" stroke-width="0.3" stroke-dasharray="10 15"/>
      <line x1="100" y1="50" x2="100" y2="550" stroke="var(--gold)" stroke-width="0.3" stroke-dasharray="10 15"/>
      <!-- Sơ đồ đường tròn đục lỗ tiêu âm/la bàn -->
      <circle cx="950" cy="150" r="120" stroke="var(--gold)" stroke-width="0.4" stroke-dasharray="5 5"/>
      <circle cx="950" cy="150" r="80" stroke="var(--gold)" stroke-width="0.4"/>
      <circle cx="950" cy="150" r="40" stroke="var(--gold)" stroke-width="0.6" stroke-dasharray="2 2"/>
      <path d="M950 10v280M800 150h300" stroke="var(--gold)" stroke-width="0.3"/>
    </svg>
  </div>

  <div class="pp-container-shared">
    <!-- Layout mạng lưới bất đối xứng tạp chí kỹ thuật -->
    <div class="pp-gcb-partnership-editorial-grid">
      
      <!-- Cột TRÁI: Hộp thông tin Niken sắc sảo chèn đè chéo, chứa nội dung thách thức shophouse -->
      <div class="pp-gcb-partnership-text-wrapper scroll-reveal">
        <div class="pp-gcb-partnership-glass-card pp-gcb-niken-card">
          <!-- Các nẹp viền kim loại vàng kim mỏng nghệ thuật -->
          <span class="gcb-niken-corner gcb-niken-corner--tl"></span>
          <span class="gcb-niken-corner gcb-niken-corner--tr"></span>
          <span class="gcb-niken-corner gcb-niken-corner--bl"></span>
          <span class="gcb-niken-corner gcb-niken-corner--br"></span>

          <!-- La bàn đo đạc kỹ thuật CAD SVG lơ lửng, chuyển động xoay nhẹ -->
          <div class="gcb-partnership-compass-icon" aria-hidden="true">
            <svg viewBox="0 0 100 100" fill="none" stroke="currentColor" stroke-width="1.2">
              <circle cx="50" cy="50" r="45" stroke-dasharray="3 3"/>
              <circle cx="50" cy="50" r="30"/>
              <path d="M50 10 L50 90 M10 50 L90 50 M30 30 L70 70 M30 70 L70 30" stroke-width="0.8"/>
              <polygon points="50,15 54,45 50,50 46,45" fill="var(--gold)" stroke="none"/>
              <polygon points="50,85 54,55 50,50 46,55" fill="currentColor" stroke="none"/>
            </svg>
          </div>

          <span class="pp-gcb-partnership-tag"><?php echo esc_html__('THÁCH THỨC SHOPHOUSE', 'saigonhoreca'); ?></span>
          
          <h2 class="pp-gcb-card-heading" style="font-size: clamp(1.6rem, 3vw, 2.3rem); margin-top: 0.5rem; margin-bottom: 1.5rem; line-height: 1.25; color: var(--bc); font-family: var(--font-display); font-weight: 700;">
            <?php echo esc_html__('Thách Thức Kỹ Thuật Tại Khu Phức Hợp Cao Cấp', 'saigonhoreca'); ?>
          </h2>

          <div class="pp-gcb-partnership-card-body">
            <div class="gcb-partnership-quote" style="border-left: 2px solid var(--gold); padding-left: 1.25rem; margin-bottom: 1.5rem;">
              <p style="font-size: 1rem; line-height: 1.65; font-weight: 500; font-style: italic; color: var(--bc); margin: 0;">
                <?php echo esc_html__('“Thiết kế bếp và quầy bar cho shophouse tại Metropole Thủ Thiêm đòi hỏi sự tỉ mỉ tối đa trên từng mét vuông diện tích.”', 'saigonhoreca'); ?>
              </p>
            </div>
            
            <p class="pp-gcb-desc-p" style="font-size: 0.95rem; line-height: 1.8; color: var(--bc2); margin: 0;">
              <?php echo esc_html__('Khác với mặt bằng độc lập, shophouse thương mại đi kèm những quy định kỹ thuật vô cùng khắt khe về an toàn PCCC, tải trọng điện và hệ thống hút khói, cấp khí tươi để không gây ảnh hưởng đến cư dân xung quanh.', 'saigonhoreca'); ?>
            </p>
          </div>
        </div>
      </div>

      <!-- Cột PHẢI: Collage ảnh bất đối xứng, có ảnh bo mây đan xen ảnh bo chéo sắc sảo -->
      <div class="pp-gcb-partnership-visual-collage scroll-reveal">
        <!-- Đường dóng đo đạc màu vàng kim -->
        <div class="pp-gcb-partnership-measuring-lines" aria-hidden="true">
          <svg viewBox="0 0 100 100" fill="none" stroke="color-mix(in srgb, var(--gold) 20%, transparent)" stroke-width="0.3">
            <path d="M0 10h100M0 90h100M10 0v100M90 0v100"/>
            <circle cx="50" cy="50" r="40" stroke-dasharray="2 4"/>
          </svg>
        </div>

        <!-- Ảnh chính: Quầy bar beverage counter - Bo mây bay bổng cực kỳ mềm mại -->
        <div class="pp-gcb-partnership-photo pp-gcb-partnership-photo--main pp-gcb-cloud-cloud">
          <div class="pp-image-container-shared">
            <img src="<?php echo sgh_img('g-cup-coffee-bistro/g-cup-coffee-bistro-he-quay-beverage-counter-chuyen-nghiep.webp'); ?>" alt="<?php echo esc_attr__('Hệ quầy beverage counter và hệ lạnh Hoshizaki shophouse', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
            <div class="pp-image-caption-shared">
              <?php echo esc_html__('Hệ quầy beverage counter tích hợp khay đá bảo ôn âm bàn giữ nhiệt chuyên dụng cho barista thao tác nhanh chóng.', 'saigonhoreca'); ?>
            </div>
          </div>
        </div>

        <!-- Ảnh phụ: Blendtec Blenders - Bo chéo góc sắc sảo, đè chồng nghệ thuật -->
        <div class="pp-gcb-partnership-photo pp-gcb-partnership-photo--sub pp-gcb-sharp-diagonal">
          <div class="pp-image-container-shared">
            <img src="<?php echo sgh_img('g-cup-coffee-bistro/g-cup-coffee-bistro-cap-may-xay-blendtec-chong-on.webp'); ?>" alt="<?php echo esc_attr__('Cặp máy xay Blendtec công nghiệp chống ồn', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
            <div class="pp-image-caption-shared">
              <?php echo esc_html__('Cặp máy xay Blendtec công nghiệp tích hợp hộp cách âm chống ồn chuyên dụng cho môi trường yên tĩnh.', 'saigonhoreca'); ?>
            </div>
          </div>
        </div>
      </div>

    </div>

    <!-- Phân chia catalog thành các Card Bản vẽ Blueprint sắc sảo nằm ngang bên dưới -->
    <div class="pp-gcb-partnership-catalog-row scroll-reveal">
      
      <!-- Card phân khu 1 -->
      <div class="pp-gcb-blueprint-card">
        <span class="pp-gcb-blueprint-corner pp-gcb-blueprint-corner--tl"></span>
        <span class="pp-gcb-blueprint-corner pp-gcb-blueprint-corner--br"></span>
        
        <div class="pp-gcb-blueprint-header">
          <span class="pp-gcb-blueprint-num">01</span>
          <h4 class="pp-gcb-blueprint-title"><?php echo esc_html__('Quầy Bar Specialty (Tầng Trệt)', 'saigonhoreca'); ?></h4>
        </div>
        <p class="pp-gcb-blueprint-desc">
          <?php echo esc_html__('Trực diện tiếp đón khách hàng, quầy bar được tính toán chi tiết để lắp đặt máy pha specialty espresso La Marzocco, máy xay Anfim cùng hệ thống vòi rửa ly áp lực âm bàn, hỗ trợ barista thao tác nhanh chóng và sạch sẽ tối đa.', 'saigonhoreca'); ?>
        </p>
      </div>

      <!-- Card phân khu 2 -->
      <div class="pp-gcb-blueprint-card">
        <span class="pp-gcb-blueprint-corner pp-gcb-blueprint-corner--tl"></span>
        <span class="pp-gcb-blueprint-corner pp-gcb-blueprint-corner--br"></span>
        
        <div class="pp-gcb-blueprint-header">
          <span class="pp-gcb-blueprint-num">02</span>
          <h4 class="pp-gcb-blueprint-title"><?php echo esc_html__('Bếp Bánh & Bếp Nóng (Tầng Hai)', 'saigonhoreca'); ?></h4>
        </div>
        <p class="pp-gcb-blueprint-desc">
          <?php echo esc_html__('Saigon Horeca tổ chức quy trình một chiều khép kín, phân tách rõ ràng khu bếp nóng chuẩn bị món ăn nhẹ và khu bếp bánh ngọt chuẩn vị Âu để tránh giao thoa mùi vị và nhiệt độ khi hoạt động với tần suất cao.', 'saigonhoreca'); ?>
        </p>
      </div>

    </div>

    <!-- Hộp Callout kết luận dưới dạng Tag chứng nhận kỹ thuật cao cấp -->
    <div class="pp-gcb-partnership-footer-tag scroll-reveal">
      <div class="pp-gcb-footer-tag-inner">
        <!-- Icon sparkle nẹp góc sáng loáng -->
        <svg class="gcb-callout-sparkle" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
          <path d="M12 2L15 9L22 12L15 15L12 22L9 15L2 12L9 9L12 2Z" fill="var(--gold)" />
        </svg>
        <p class="pp-gcb-footer-tag-text">
          <?php echo esc_html__('Bằng việc tính toán chuẩn xác từ khâu đi ống gió kỹ thuật cho đến định vị chi tiết từng neo thiết bị, Saigon Horeca đã hiện thực hóa không gian bếp mở hoàn mỹ, an toàn tuyệt đối và tôn vinh kiến trúc neutral sang trọng của G-Cup Bistro.', 'saigonhoreca'); ?>
        </p>
      </div>
    </div>

  </div>
</section>
