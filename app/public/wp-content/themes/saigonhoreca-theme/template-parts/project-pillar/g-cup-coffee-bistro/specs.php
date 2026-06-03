<?php
/**
 * Project Pillar — g-cup-coffee-bistro
 * Section #5: specs — Giải pháp & Thiết bị lắp đặt chuyên sâu
 * Thiết kế theo phong cách tạp chí kiến trúc bất đối xứng (ArchDaily style), giàu tính nghệ thuật
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-gcb pp-specs-section-gcb" id="gcb-specs-anchor">
  <div class="pp-container-shared">
    
    <div class="pp-gcb-specs-grid">
      
      <!-- Cột trái (60%): Collage 3 ảnh thiết bị Bar kỹ thuật xếp chồng bất đối xứng nghệ thuật -->
      <div class="pp-gcb-specs-media-wrapper">
        <!-- Nhãn tọa độ kỹ thuật mờ chạy xung quanh góc ảnh -->
        <div class="pp-gcb-specs-coord pp-gcb-specs-coord--tl" aria-hidden="true">METROPOLE_SH_02 / CAD_v2.0</div>
        <div class="pp-gcb-specs-coord pp-gcb-specs-coord--tr" aria-hidden="true">SCALE: 1:15 @ A3</div>
        <div class="pp-gcb-specs-coord pp-gcb-specs-coord--bl" aria-hidden="true">SYS_COORD: 10.7719° N, 106.7214° E</div>
        <div class="pp-gcb-specs-coord pp-gcb-specs-coord--br" aria-hidden="true">SAIGON HORECA © 2026</div>

        <!-- Họa tiết thước đo và trục CAD phía sau collage -->
        <div class="pp-gcb-specs-cad-lines" aria-hidden="true">
          <svg viewBox="0 0 500 400" fill="none" stroke="color-mix(in srgb, var(--gold) 15%, transparent)" stroke-width="0.4">
            <line x1="10" y1="20" x2="490" y2="20"/>
            <line x1="10" y1="20" x2="10" y2="380"/>
            <line x1="490" y1="20" x2="490" y2="380"/>
            <circle cx="250" cy="200" r="150" stroke-dasharray="2 6"/>
          </svg>
        </div>

        <div class="pp-gcb-specs-collage">
          <!-- Ảnh 1 (Ảnh chính): Máy pha La Marzocco phản chiếu bóng loáng - Bo tròn kèm nẹp vàng kim -->
          <div class="pp-gcb-collage-item pp-gcb-collage-item--main scroll-reveal">
            <div class="pp-gcb-luxury-frame pp-gcb-luxury-frame--specs pp-gcb-specs-sharp-frame">
              <!-- Nẹp góc vàng kim lấp lánh -->
              <span class="pp-gcb-specs-corner pp-gcb-specs-corner--tl"></span>
              <span class="pp-gcb-specs-corner pp-gcb-specs-corner--br"></span>
              
              <div class="pp-image-container-shared">
                <img src="<?php echo sgh_img('g-cup-coffee-bistro/g-cup-coffee-bistro-can-canh-may-pha-ca-phe-la-marzocco.webp'); ?>" alt="<?php echo esc_attr__('Cận cảnh máy pha cà phê La Marzocco bóng bẩy', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
                <div class="pp-image-caption-shared"><?php echo esc_html__('Cận cảnh máy pha cà phê La Marzocco mạ chrome bóng bẩy phản chiếu nghệ thuật kiến trúc độc đáo của quán.', 'saigonhoreca'); ?></div>
              </div>
            </div>
          </div>

          <!-- Ảnh 2 (Ảnh phụ đè góc trên): Máy xay cà phê Anfim - Bo mây bay bổng mượt mà -->
          <div class="pp-gcb-collage-item pp-gcb-collage-item--sub-top scroll-reveal">
            <div class="pp-gcb-luxury-frame pp-gcb-luxury-frame--specs pp-gcb-specs-cloud-frame">
              <div class="pp-image-container-shared">
                <img src="<?php echo sgh_img('g-cup-coffee-bistro/g-cup-coffee-bistro-day-may-xay-ca-phe-anfim.webp'); ?>" alt="<?php echo esc_attr__('Hệ thống máy xay cà phê Anfim G-Cup Bistro', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
                <div class="pp-image-caption-shared"><?php echo esc_html__('Cặp máy xay cà phê Anfim công suất lớn đặt song song, sẵn sàng phục vụ những tách espresso hảo hạng tốc độ cao.', 'saigonhoreca'); ?></div>
              </div>
            </div>
          </div>

          <!-- Ảnh 3 (Ảnh phụ đè góc dưới): Bộ rửa ly âm bàn - Bo vạt chéo sắc sảo hình lục giác chéo -->
          <div class="pp-gcb-collage-item pp-gcb-collage-item--sub-bottom scroll-reveal">
            <div class="pp-gcb-luxury-frame pp-gcb-luxury-frame--specs pp-gcb-specs-diagonal-frame">
              <div class="pp-image-container-shared">
                <img src="<?php echo sgh_img('g-cup-coffee-bistro/g-cup-coffee-bistro-chi-tiet-voi-rua-ly-am-ban.webp'); ?>" alt="<?php echo esc_attr__('Chi tiết bộ rửa ly âm bàn', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
                <div class="pp-image-caption-shared"><?php echo esc_html__('Hệ thống vòi rửa ly xoay âm bàn bằng inox 304 siêu bền giúp tối ưu hóa thao tác vệ sinh tại quầy.', 'saigonhoreca'); ?></div>
              </div>
            </div>
          </div>

          <!-- Ảnh 4 (Ảnh phụ nghiêng áp góc): Drip Filter pha cà phê thủ công -->
          <div class="pp-gcb-collage-item pp-gcb-collage-item--sub-corner scroll-reveal">
            <div class="pp-gcb-luxury-frame pp-gcb-luxury-frame--specs pp-gcb-specs-tilt-frame">
              <div class="pp-image-container-shared">
                <img src="<?php echo sgh_img('g-cup-coffee-bistro/g-cup-coffee-bistro-phin-nhom-den-pha-ca-phe.webp'); ?>" alt="<?php echo esc_attr__('Bộ Drip Filter pha cà phê thủ công tại G-Cup Bistro', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
                <div class="pp-image-caption-shared"><?php echo esc_html__('Nghệ thuật pha chế Drip Filter chậm rãi tinh tế với bộ dụng cụ thủy tinh chịu nhiệt cao cấp.', 'saigonhoreca'); ?></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Cột phải (40%): Thông số kỹ thuật & Giải pháp cơ điện trong hộp Niken bóng bẩy -->
      <div class="pp-gcb-specs-info scroll-reveal">
        <div class="pp-gcb-glass-card-specs pp-gcb-niken-card">
          <!-- Các nẹp viền kim loại mỏng vàng kim -->
          <span class="gcb-niken-corner gcb-niken-corner--tl"></span>
          <span class="gcb-niken-corner gcb-niken-corner--tr"></span>
          <span class="gcb-niken-corner gcb-niken-corner--bl"></span>
          <span class="gcb-niken-corner gcb-niken-corner--br"></span>

          <header class="pp-gcb-specs-header">
            <div class="pp-gcb-badge">
              <svg viewBox="0 0 24 24" fill="currentColor" style="width:14px; height:14px; margin-right:6px;" aria-hidden="true">
                <path d="M12 2L14.85 9.15L22 12L14.85 14.85L12 22L9.15 14.85L2 12L9.15 9.15L12 2Z"/>
              </svg>
              <?php echo esc_html__('THÔNG SỐ & GIẢI PHÁP', 'saigonhoreca'); ?>
            </div>
            <h2 class="pp-text-gcb__title" style="font-size: clamp(1.4rem, 2.4vw, 1.9rem); margin-top: 0.85rem; margin-bottom: 1.4rem; color: var(--bc); font-family: var(--font-display); font-weight: 700; line-height: 1.2;">
              <?php echo esc_html__('Giải Pháp Vận Hành Đồng Bộ', 'saigonhoreca'); ?>
            </h2>
          </header>

          <div class="pp-gcb-specs-body">
            <!-- Tính năng 1 -->
            <div class="pp-gcb-specs-feature">
              <div class="pp-gcb-specs-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:20px; height:20px;" aria-hidden="true">
                  <path d="M20 7h-9m3 4H5m16 4h-9m6 4H8" stroke-linecap="round"/>
                </svg>
              </div>
              <div class="pp-gcb-specs-feat-content">
                <h4><?php echo esc_html__('Hệ thống hút khói & cấp khí tươi', 'saigonhoreca'); ?></h4>
                <p><?php echo esc_html__('Thiết kế chuẩn xác từ khâu tính toán ống gió, bộ lọc mùi, đảm bảo không khí phòng ăn luôn sạch thoáng, không bị bám mùi khói từ khu shophouse.', 'saigonhoreca'); ?></p>
              </div>
            </div>

            <!-- Tính năng 2 -->
            <div class="pp-gcb-specs-feature">
              <div class="pp-gcb-specs-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:20px; height:20px;" aria-hidden="true">
                  <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
                  <line x1="8" y1="21" x2="16" y2="21" stroke-linecap="round"/>
                  <line x1="12" y1="17" x2="12" y2="21" stroke-linecap="round"/>
                </svg>
              </div>
              <div class="pp-gcb-specs-feat-content">
                <h4><?php echo esc_html__('Hệ thống lạnh tích hợp Hoshizaki', 'saigonhoreca'); ?></h4>
                <p><?php echo esc_html__('Hệ thống tủ mát và bàn đông Hoshizaki duy trì lạnh âm sâu đạt chuẩn -16°C, bảo quản kem và đá viên tối ưu ngay tại quầy pha chế.', 'saigonhoreca'); ?></p>
              </div>
            </div>

            <!-- Tính năng 3 -->
            <div class="pp-gcb-specs-feature">
              <div class="pp-gcb-specs-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:20px; height:20px;" aria-hidden="true">
                  <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
              </div>
              <div class="pp-gcb-specs-feat-content">
                <h4><?php echo esc_html__('Đảm bảo quy chuẩn PCCC shophouse', 'saigonhoreca'); ?></h4>
                <p><?php echo esc_html__('Lắp đặt đồng bộ với hệ trần kỹ thuật của ban quản lý Metropole, bảo đảm an toàn PCCC tuyệt đối và vận hành bền bỉ.', 'saigonhoreca'); ?></p>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>

    <!-- Equipment Showcase Catalog phong cách tạp chí cao cấp -->
    <div class="pp-gcb-equip-showcase scroll-reveal">
      <header class="pp-gcb-equip-header">
        <span class="pp-text-section__divider pp-text-section__divider--center" aria-hidden="true"></span>
        <h3 class="pp-text-gcb__title">
          <?php echo esc_html__('Danh Mục Thiết Bị Lắp Đặt Chiến Lược', 'saigonhoreca'); ?>
        </h3>
        <p class="pp-gcb-equip-subtitle">
          <?php echo esc_html__('Tuyển chọn những thương hiệu F&B hàng đầu thế giới được Saigon Horeca tư vấn, cung cấp và lắp đặt đồng bộ tại G-Cup Bistro.', 'saigonhoreca'); ?>
        </p>
      </header>

      <!-- 4 Card Thiết bị thiết kế so le nghệ thuật, icon kỹ thuật 3D và viền sáng lấp lánh -->
      <div class="pp-gcb-equip-grid">
        
        <!-- Card 1: Bếp từ công nghiệp -->
        <div class="pp-gcb-equip-card pp-gcb-niken-card">
          <!-- Các nẹp viền kim loại vàng kim mỏng nghệ thuật -->
          <span class="gcb-niken-corner gcb-niken-corner--tl"></span>
          <span class="gcb-niken-corner gcb-niken-corner--br"></span>
          
          <div class="pp-gcb-equip-icon-wrap">
            <!-- SVG sơ đồ mâm nhiệt cảm ứng 3D lồng vào nhau chi tiết -->
            <svg viewBox="0 0 100 100" fill="none" stroke="currentColor" stroke-width="1.2">
              <circle cx="50" cy="50" r="40" stroke-dasharray="2 3"/>
              <circle cx="50" cy="50" r="28" stroke-width="1.5"/>
              <circle cx="50" cy="50" r="16"/>
              <path d="M10 50h80M50 10v80" stroke-width="0.5" stroke-dasharray="5 5"/>
              <path d="M35 35l30 30M35 65l30-30" stroke-width="0.5"/>
            </svg>
          </div>
          <h4 class="pp-gcb-equip-title"><?php echo esc_html__('Bếp từ công nghiệp Saigon Horeca', 'saigonhoreca'); ?></h4>
          <p class="pp-gcb-equip-desc"><?php echo esc_html__('Hệ thống bếp từ công suất cao được sản xuất riêng, an toàn tuyệt đối, gia nhiệt tức thì và cực kỳ dễ vệ sinh lau chùi.', 'saigonhoreca'); ?></p>
        </div>

        <!-- Card 2: Máy rửa chén Ozti -->
        <div class="pp-gcb-equip-card pp-gcb-niken-card">
          <!-- Các nẹp viền kim loại vàng kim mỏng nghệ thuật -->
          <span class="gcb-niken-corner gcb-niken-corner--tl"></span>
          <span class="gcb-niken-corner gcb-niken-corner--br"></span>
          
          <div class="pp-gcb-equip-icon-wrap">
            <!-- SVG dòng nước áp lực rửa xoáy 3D và các bọt bong bóng siêu vi -->
            <svg viewBox="0 0 100 100" fill="none" stroke="currentColor" stroke-width="1.2">
              <path d="M20 30 Q50 10 80 30 T80 70 Q50 90 20 70Z" stroke-dasharray="2 2"/>
              <path d="M50 20 C60 35 40 50 50 65 C60 80 50 90 50 90" stroke-width="1.5"/>
              <path d="M35 25 C45 40 25 55 35 70" stroke-width="0.8"/>
              <path d="M65 25 C75 40 55 55 65 70" stroke-width="0.8"/>
              <circle cx="25" cy="45" r="4" fill="var(--gold)" stroke="none"/>
              <circle cx="75" cy="55" r="3" fill="var(--gold)" stroke="none"/>
              <circle cx="45" cy="80" r="5" fill="var(--gold)" stroke="none"/>
            </svg>
          </div>
          <h4 class="pp-gcb-equip-title"><?php echo esc_html__('Máy rửa chén Ozti (Thổ Nhĩ Kỳ)', 'saigonhoreca'); ?></h4>
          <p class="pp-gcb-equip-desc"><?php echo esc_html__('Giải pháp rửa sấy khép kín giúp tiết kiệm tối đa thời gian, nhân lực và lượng nước tiêu thụ trong các ca vận hành cao điểm.', 'saigonhoreca'); ?></p>
        </div>

        <!-- Card 3: Lò hấp nướng Giorik -->
        <div class="pp-gcb-equip-card pp-gcb-niken-card">
          <!-- Các nẹp viền kim loại vàng kim mỏng nghệ thuật -->
          <span class="gcb-niken-corner gcb-niken-corner--tl"></span>
          <span class="gcb-niken-corner gcb-niken-corner--br"></span>
          
          <div class="pp-gcb-equip-icon-wrap">
            <!-- SVG luồng nhiệt đối lưu 3D tuần hoàn và hệ thống hơi nước đan chéo -->
            <svg viewBox="0 0 100 100" fill="none" stroke="currentColor" stroke-width="1.2">
              <rect x="15" y="15" width="70" height="70" rx="6" stroke-width="1.5"/>
              <circle cx="50" cy="50" r="20" stroke-dasharray="3 3"/>
              <!-- Cánh quạt đối lưu 3D -->
              <path d="M50 50 L50 35 C55 35 60 40 50 50" fill="var(--gold)" stroke="none"/>
              <path d="M50 50 L65 50 C65 55 60 60 50 50" fill="var(--gold)" stroke="none"/>
              <path d="M50 50 L50 65 C45 65 40 60 50 50" fill="var(--gold)" stroke="none"/>
              <path d="M50 50 L35 50 C35 45 40 40 50 50" fill="var(--gold)" stroke="none"/>
              <!-- Hơi nước đan chéo -->
              <path d="M22 25 Q35 30 22 35 M78 65 Q65 70 78 75" stroke-width="0.8"/>
            </svg>
          </div>
          <h4 class="pp-gcb-equip-title"><?php echo esc_html__('Lò hấp nướng đa năng Giorik (Ý)', 'saigonhoreca'); ?></h4>
          <p class="pp-gcb-equip-desc"><?php echo esc_html__('Thiết bị đa năng hàng đầu châu Âu hỗ trợ chế biến linh hoạt từ các món Á đến Âu, kiểm soát độ ẩm và nhiệt độ nướng đối lưu hoàn hảo.', 'saigonhoreca'); ?></p>
        </div>

        <!-- Card 4: Hệ thống lạnh quầy bar -->
        <div class="pp-gcb-equip-card pp-gcb-niken-card">
          <!-- Các nẹp viền kim loại vàng kim mỏng nghệ thuật -->
          <span class="gcb-niken-corner gcb-niken-corner--tl"></span>
          <span class="gcb-niken-corner gcb-niken-corner--br"></span>
          
          <div class="pp-gcb-equip-icon-wrap">
            <!-- SVG hệ bông tuyết kết tinh 3D và các lá tản nhiệt đồng bộ -->
            <svg viewBox="0 0 100 100" fill="none" stroke="currentColor" stroke-width="1.2">
              <!-- Bông tuyết kết tinh 3D kỹ thuật -->
              <path d="M50 15v70M15 50h70M25 25l50 50M25 75l50-50" stroke-width="1.5"/>
              <path d="M50 30l-5-5M50 30l5-5M50 70l-5 5M50 70l5 5M30 50l-5-5M30 50l-5 5M70 50l5-5M70 50l5 5" stroke-width="1.2"/>
              <circle cx="50" cy="50" r="8" fill="var(--b1)" stroke="currentColor" stroke-width="1"/>
            </svg>
          </div>
          <h4 class="pp-gcb-equip-title"><?php echo esc_html__('Hệ thống lạnh quầy bar & tủ trưng bày', 'saigonhoreca'); ?></h4>
          <p class="pp-gcb-equip-desc"><?php echo esc_html__('Bàn mát barista, tủ đông âm tủ mát trưng bày bánh Âu được đồng bộ hóa toàn diện, vận hành ổn định và giữ nguyên tính thẩm mỹ tinh tế.', 'saigonhoreca'); ?></p>
        </div>

      </div>
    </div>

  </div>
</section>
