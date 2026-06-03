<?php
/**
 * Project Pillar — roka-fella-tinh-hoa-am-thuc-nhat-ban
 * Section #6: gallery (storage & kitchen execution)
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-rf pp-rf-gallery">
  <div class="pp-watermark-bg-rf" aria-hidden="true">
    <svg viewBox="0 0 100 100" fill="none" stroke="currentColor" stroke-width="1.5">
      <path d="M35 20 L50 80 L65 20" stroke-linecap="round"/>
    </svg>
  </div>

  <div class="pp-ambient-glow-rf pp-ambient-glow-rf--top-right" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <div class="rkf-gallery__layout">
      
      <div class="rkf-gallery__main">
        <div class="pp-glass-card-roka rkf-gallery__glass-card">
          <svg class="card-corner-svg" viewBox="0 0 100 100" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M 0 15 L 0 0 L 15 0" stroke="var(--gold)" stroke-width="1.2" />
            <path d="M 100 15 L 100 0 L 85 0" stroke="var(--gold)" stroke-width="1.2" />
            <path d="M 0 85 L 0 100 L 15 100" stroke="var(--gold)" stroke-width="1.2" />
            <path d="M 100 85 L 100 100 L 85 100" stroke="var(--gold)" stroke-width="1.2" />
          </svg>
          
          <!-- Số chìm kỹ thuật 03 -->
          <div class="pp-gallery-watermark-rf" aria-hidden="true">03</div>
 
          <header class="rkf-gallery__header">
            <div class="pp-badge-rf">
              <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2L14.85 9.15L22 12L14.85 14.85L12 22L9.15 14.85L2 12L9.15 9.15L12 2Z"/>
              </svg>
              <?php echo esc_html__('Bảo quản & Chế biến', 'saigonhoreca'); ?>
            </div>
            <h2 class="pp-text-rf__title">
              <?php echo esc_html__('Trang thiết bị chuyên nghiệp tối tân', 'saigonhoreca'); ?>
            </h2>
            <div class="pp-text-rf__divider" aria-hidden="true"></div>
          </header>
 
          <div class="rkf-gallery__body">
            <p class="rkf-gallery__lead"><?php echo esc_html__('Hệ thống thiết bị nhập khẩu Nhật Bản cao cấp phối hợp hoàn mỹ cùng gia công inox Saigon Horeca.', 'saigonhoreca'); ?></p>
            
            <div class="rkf-gallery__grid">
              <div class="rkf-gallery__item">
                <div class="rkf-gallery__item-header">
                  <strong><?php echo esc_html__('Thiết bị nhiệt nhập khẩu', 'saigonhoreca'); ?></strong>
                  <span class="rkf-gallery__stat-badge rkf-gallery__stat-badge--brand">HOSHIZAKI</span>
                </div>
                <p><?php echo esc_html__('Bếp Á 4 họng, máy chiên nhúng và bếp nướng Salamander cao cấp.', 'saigonhoreca'); ?></p>
              </div>
              <div class="rkf-gallery__item">
                <div class="rkf-gallery__item-header">
                  <strong><?php echo esc_html__('Inox Saigon Horeca', 'saigonhoreca'); ?></strong>
                  <span class="rkf-gallery__stat-badge rkf-gallery__stat-badge--metal">SUS 304</span>
                </div>
                <p><?php echo esc_html__('Hệ thống máy hút khói, bồn rửa và kệ treo tường thiết kế chuyên biệt.', 'saigonhoreca'); ?></p>
              </div>
            </div>
 
            <div class="rkf-gallery__note-box">
              <div class="rkf-gallery__note-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                  <path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
              <p class="rkf-gallery__note"><?php echo esc_html__('Khu vực sơ chế trang bị máy làm đá chuyên dụng và hệ thống tủ lạnh, tủ đông Hoshizaki, cung cấp đá tinh khiết tuyệt đối để giữ lạnh và trình bày các món sashimi nghệ thuật.', 'saigonhoreca'); ?></p>
            </div>
          </div>
        </div>
      </div>
 
      <div class="rkf-gallery__media">
        <div class="pp-image-container-shared rkf-gallery__image-container">
          <svg class="card-corner-svg" viewBox="0 0 100 100" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M 0 15 L 0 0 L 15 0" stroke="var(--gold)" stroke-width="1.2" />
            <path d="M 100 15 L 100 0 L 85 0" stroke="var(--gold)" stroke-width="1.2" />
            <path d="M 0 85 L 0 100 L 15 100" stroke="var(--gold)" stroke-width="1.2" />
            <path d="M 100 85 L 100 100 L 85 100" stroke="var(--gold)" stroke-width="1.2" />
          </svg>
          <div class="pp-image-border-decor" aria-hidden="true"></div>
          <img src="<?php echo sgh_img('roka-fella/roka-fella-bep-nau-chuyen-dung-inox.jpg'); ?>" alt="<?php echo esc_attr__('Phòng bếp nấu ăn Roka Fella', 'saigonhoreca'); ?>" loading="lazy">
          <div class="pp-image-caption-shared"><?php echo esc_html__('Khu bếp sơ chế và chuẩn bị nguyên liệu thực tế được tối ưu hóa bằng thiết bị inox cao cấp', 'saigonhoreca'); ?></div>
        </div>
      </div>
 
    </div>
  </div>
</section>

<section class="pp-section-rf pp-rf-related">
  <!-- Đĩa Vinyl ngầm với hiệu ứng lướt sáng (glow sweep) -->
  <div class="pp-watermark-bg-rf rkf-vinyl-record" aria-hidden="true">
    <svg viewBox="0 0 100 100" fill="none" stroke="currentColor" stroke-width="0.8">
      <circle cx="50" cy="50" r="45" stroke-dasharray="1.5 1.5"/>
      <circle cx="50" cy="50" r="40"/>
      <circle cx="50" cy="50" r="35"/>
      <circle cx="50" cy="50" r="30"/>
      <circle cx="50" cy="50" r="25"/>
      <circle cx="50" cy="50" r="20" stroke-width="1.2"/>
      <circle cx="50" cy="50" r="10" stroke-width="0.8" fill="rgba(212,175,55,0.15)"/>
      <circle cx="50" cy="50" r="3" fill="currentColor"/>
      <path d="M 50 10 L 80 10 L 65 30 L 53 28" stroke="var(--gold)" stroke-width="0.8" opacity="0.4" />
    </svg>
  </div>

  <div class="pp-ambient-glow-rf pp-ambient-glow-rf--bottom-left" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <div class="pp-grid-12-rf">
      
      <div class="pp-grid-12-rf__text--cols-5 rkf-related__main">
        <div class="pp-glass-card-roka rkf-related__glass-card">
          <!-- Khung góc SVG nghệ thuật mạ vàng kiêu sa -->
          <svg class="card-corner-svg" viewBox="0 0 100 100" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M 0 15 L 0 0 L 15 0" stroke="var(--gold)" stroke-width="1.2" />
            <path d="M 100 15 L 100 0 L 85 0" stroke="var(--gold)" stroke-width="1.2" />
            <path d="M 0 85 L 0 100 L 15 100" stroke="var(--gold)" stroke-width="1.2" />
            <path d="M 100 85 L 100 100 L 85 100" stroke="var(--gold)" stroke-width="1.2" />
          </svg>
          
          <header class="rkf-related__header">
            <div class="pp-badge-rf">
              <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2L14.85 9.15L22 12L14.85 14.85L12 22L9.15 14.85L2 12L9.15 9.15L12 2Z"/>
              </svg>
              <?php echo esc_html__('Bàn giao hoàn thiện', 'saigonhoreca'); ?>
            </div>
            <h2 class="pp-text-rf__title" style="font-size: clamp(1.5rem, 3vw, 2.2rem); margin-bottom: 1rem;">
              <?php echo esc_html__('Cam kết dịch vụ đẳng cấp quốc tế', 'saigonhoreca'); ?>
            </h2>
            <div class="pp-text-rf__divider" aria-hidden="true"></div>
          </header>

          <div class="rkf-related__body" style="font-size: 0.98rem; line-height: 1.8; color: var(--bc2); margin-bottom: 1.75rem;">
            <p><?php echo esc_html__('Roka Fella không chỉ thu hút thực khách bởi ẩm thực tuyệt đỉnh và âm nhạc vinyl tinh tế, mà còn bởi không gian sang trọng được chăm chút tỉ mỉ từ những chi tiết nhỏ nhất.', 'saigonhoreca'); ?></p>
            <p><?php echo esc_html__('Sự thành công của dự án đã củng cố niềm tin sâu sắc của HypeAsia dành cho Saigon Horeca, mở đường cho những sự hợp tác tầm cỡ tiếp theo, tiêu biểu là dự án Godmother 2 sau này.', 'saigonhoreca'); ?></p>
          </div>

          <!-- Câu trích dẫn lãng mạn mạ vàng chỉ kép -->
          <div class="rkf-related__quote">
            <span class="rkf-related__quote-line" aria-hidden="true"></span>
            <blockquote>
              "Âm nhạc Vinyl mộc mạc hòa quyện cùng hương vị nồng nàn của rượu Sake, tạo nên những bản giao hương cảm xúc bất tận giữa lòng Sài Gòn phồn hoa."
            </blockquote>
            <span class="rkf-related__quote-line" aria-hidden="true"></span>
          </div>
        </div>
      </div>

      <div class="pp-grid-12-rf__media--cols-7 rkf-related__side">
        <div class="pp-image-container-shared rkf-related__image-container">
          <!-- Khung góc SVG nghệ thuật mạ vàng kiêu sa -->
          <svg class="card-corner-svg" viewBox="0 0 100 100" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M 0 15 L 0 0 L 15 0" stroke="var(--gold)" stroke-width="1.2" />
            <path d="M 100 15 L 100 0 L 85 0" stroke="var(--gold)" stroke-width="1.2" />
            <path d="M 0 85 L 0 100 L 15 100" stroke="var(--gold)" stroke-width="1.2" />
            <path d="M 100 85 L 100 100 L 85 100" stroke="var(--gold)" stroke-width="1.2" />
          </svg>
          <div class="pp-image-border-decor" aria-hidden="true"></div>
          <img src="<?php echo sgh_img('roka-fella/roka-fella-thumbnail-project-cover.webp'); ?>" alt="<?php echo esc_attr__('Roka Fella Final Execution', 'saigonhoreca'); ?>" loading="lazy">
          <div class="pp-image-caption-shared"><?php echo esc_html__('Toàn cảnh không gian quầy sushi bar Omakase sang trọng tích hợp âm nhạc vinyl tại Roka Fella', 'saigonhoreca'); ?></div>
        </div>
      </div>

    </div>
  </div>
</section>

<?php /* T-034: merged from cta.php (cũ section 8) */ ?>
<section class="pp-section-rf pp-rf-cta-taste">
  <div class="pp-watermark-bg-rf" aria-hidden="true">
    <svg viewBox="0 0 100 100" fill="none" stroke="currentColor" stroke-width="1.5">
      <circle cx="50" cy="50" r="40" stroke-linecap="round"/>
      <circle cx="50" cy="50" r="20" stroke-linecap="round"/>
      <path d="M50 10 V90 M10 50 H90" stroke-linecap="round"/>
    </svg>
  </div>

  <div class="pp-ambient-glow-rf pp-ambient-glow-rf--top-right" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <div class="rkf-cta__header-wrapper">
      <div class="pp-badge-rf">
        <svg viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 2L14.85 9.15L22 12L14.85 14.85L12 22L9.15 14.85L2 12L9.15 9.15L12 2Z"/>
        </svg>
        <?php echo esc_html__('Bản giao hương vị giác', 'saigonhoreca'); ?>
      </div>
      <h2 class="pp-text-rf__title">
        <?php echo esc_html__('Kiệt tác ẩm thực Omakase', 'saigonhoreca'); ?>
      </h2>
      <div class="pp-text-rf__divider" aria-hidden="true" style="margin: 1rem 0 2.5rem 0;"></div>
    </div>

    <!-- 9-Image Gastronomy Grid -->
    <div class="pp-gallery-rf pp-gallery-rf--cols-4">
      
      <!-- Dish 1 -->
      <div class="pp-image-container-shared pp-gallery-rf__item">
        <img src="<?php echo sgh_img('roka-fella/roka-fella-mon-sashimi-ca-hoi.jpg'); ?>" alt="<?php echo esc_attr__('Sashimi Otoro Uni Roka Fella', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
        <div class="pp-image-caption-shared"><?php echo esc_html__('Khay nguyên liệu Sashimi thượng hạng với bụng cá ngừ Otoro, cầu gai Uni và bào ngư', 'saigonhoreca'); ?></div>
      </div>

      <!-- Dish 2 -->
      <div class="pp-image-container-shared pp-gallery-rf__item">
        <img src="<?php echo sgh_img('roka-fella/roka-fella-mon-nigiri-ca-ngu.jpg'); ?>" alt="<?php echo esc_attr__('Nigiri Platter Roka Fella', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
        <div class="pp-image-caption-shared"><?php echo esc_html__('Khay sushi nigiri thập cẩm hảo hạng tuyển chọn chuẩn vị Omakase', 'saigonhoreca'); ?></div>
      </div>

      <!-- Dish 3 -->
      <div class="pp-image-container-shared pp-gallery-rf__item">
        <img src="<?php echo sgh_img('roka-fella/roka-fella-mon-nigiri-bach-tuoc.jpg'); ?>" alt="<?php echo esc_attr__('Wagyu Tartare Roka Fella', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
        <div class="pp-image-caption-shared"><?php echo esc_html__('Bò Wagyu Tartare thượng hạng trộn lòng đỏ trứng tươi và hành tây', 'saigonhoreca'); ?></div>
      </div>

      <!-- Dish 4 -->
      <div class="pp-image-container-shared pp-gallery-rf__item">
        <img src="<?php echo sgh_img('roka-fella/roka-fella-mon-sashimi-so-do.jpg'); ?>" alt="<?php echo esc_attr__('Sashimi Platter Roka Fella', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
        <div class="pp-image-caption-shared"><?php echo esc_html__('Set Sashimi thập cẩm hảo hạng với bụng cá ngừ Otoro, cá cam Hamachi và sò điệp', 'saigonhoreca'); ?></div>
      </div>

      <!-- Dish 5 -->
      <div class="pp-image-container-shared pp-gallery-rf__item">
        <img src="<?php echo sgh_img('roka-fella/roka-fella-mon-nigiri-trung-ca-hoi.jpg'); ?>" alt="<?php echo esc_attr__('Hotate Truffle Sashimi Roka Fella', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
        <div class="pp-image-caption-shared"><?php echo esc_html__('Sashimi sò điệp Nhật khè nhẹ kèm nấm Truffle đen đắt đỏ và cánh hoa nghệ thuật', 'saigonhoreca'); ?></div>
      </div>

      <!-- Dish 6 -->
      <div class="pp-image-container-shared pp-gallery-rf__item">
        <img src="<?php echo sgh_img('roka-fella/roka-fella-mon-nigiri-tom-ngot.jpg'); ?>" alt="<?php echo esc_attr__('Madai Nigiri Roka Fella', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
        <div class="pp-image-caption-shared"><?php echo esc_html__('Nigiri cá tráp biển Madai bóng bẩy trang trí lá xanh và trứng cá chuồn vàng', 'saigonhoreca'); ?></div>
      </div>

      <!-- Dish 7 -->
      <div class="pp-image-container-shared pp-gallery-rf__item">
        <img src="<?php echo sgh_img('roka-fella/roka-fella-mon-nigiri-ca-trich-ep-trung.jpg'); ?>" alt="<?php echo esc_attr__('Hamachi Caviar Nigiri Roka Fella', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
        <div class="pp-image-caption-shared"><?php echo esc_html__('Nigiri cá cam Hamachi khè nhẹ mặt phủ trứng cá đen sang trọng', 'saigonhoreca'); ?></div>
      </div>

      <!-- Dish 8 -->
      <div class="pp-image-container-shared pp-gallery-rf__item">
        <img src="<?php echo sgh_img('roka-fella/roka-fella-mon-nigiri-ca-cam.jpg'); ?>" alt="<?php echo esc_attr__('Aburi Hotate Nigiri Roka Fella', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
        <div class="pp-image-caption-shared"><?php echo esc_html__('Nigiri sò điệp Nhật Bản khò lửa nhẹ (Aburi Hotate) rắc bột tiêu đen', 'saigonhoreca'); ?></div>
      </div>

      <!-- Dish 9 -->
      <div class="pp-image-container-shared pp-gallery-rf__item">
        <img src="<?php echo sgh_img('roka-fella/roka-fella-mon-sashimi-bao-ngu.jpg'); ?>" alt="<?php echo esc_attr__('Kinmedai Garlic Nigiri Roka Fella', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
        <div class="pp-image-caption-shared"><?php echo esc_html__('Nigiri cá kim mục Kinmedai khò nhẹ mặt phủ sốt tỏi đen và bột gia vị', 'saigonhoreca'); ?></div>
      </div>

    </div>
  </div>
</section>

<!-- Section 8B: Vinyl Mixology & Music Lounge -->
<section class="pp-section-rf pp-section-rf--alt pp-rf-cta-music">
  <div class="pp-ambient-glow-rf pp-ambient-glow-rf--bottom-left" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <div class="rkf-music__split">
      
      <!-- Vinyl spinning record dynamic illustration -->
      <div class="rkf-music__visual">
        <div class="rkf-music__vinyl-record-wrap rkf-turntable">
          <!-- Bảng điều khiển cơ khí chải xước mạ vàng kim -->
          <div class="rkf-turntable__dashboard">
            <div class="rkf-turntable__brand">ROKA FELLA</div>
            <div class="rkf-btn-group">
              <button class="rkf-btn-metal rkf-btn-start" aria-label="Start/Stop Turntable">
                <span class="rkf-btn-dot"></span>
              </button>
              <div class="rkf-speed-switch">
                <span class="rkf-btn-speed rkf-btn-speed--33 active">33</span>
                <span class="rkf-btn-speed rkf-btn-speed--45">45</span>
              </div>
            </div>
            <!-- Núm vặn Pitch mạ đồng thau sang trọng -->
            <div class="rkf-pitch-control">
              <div class="rkf-knob-metal rkf-knob-pitch"></div>
              <div class="rkf-pitch-labels">PITCH</div>
            </div>
          </div>

          <!-- Mâm đĩa & Đĩa xoay (Platter & Record) -->
          <div class="rkf-turntable__platter">
            <div class="rkf-music__vinyl-record">
              <!-- Rãnh đĩa cơ học chìm nổi -->
              <div class="rkf-vinyl-grooves"></div>
              <!-- Nhãn đĩa Retro thiết kế xoay tròn kỹ thuật -->
              <div class="rkf-music__vinyl-center">
                <div class="rkf-vinyl-label-inner">
                  <span class="rkf-vinyl-label-text">ROKA FELLA VINYL LOUNGE</span>
                </div>
              </div>
              <!-- Cục chặn đĩa than bằng đồng thau nặng ở tâm trục -->
              <div class="rkf-vinyl-stabilizer"></div>
            </div>
          </div>

          <!-- Hệ cần Tonearm cơ khí gimbal đối trọng kép tinh xảo -->
          <div class="rkf-turntable__tonearm-system">
            <div class="rkf-tonearm-base"></div> <!-- Trục gimbal -->
            <div class="rkf-music__vinyl-arm">
              <div class="rkf-tonearm-counterweight"></div> <!-- Cân đối trọng -->
              <div class="rkf-tonearm-cartridge"></div> <!-- Kim đọc phát sáng -->
            </div>
          </div>
        </div>
      </div>

      <!-- Editorial text card -->
      <div class="rkf-music__text-column">
        <div class="pp-badge-rf">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v-1.07zM17.9 13.9c-.38-.9-1.12-1.9-2.9-1.9h-1v-3c0-.55-.45-1-1-1H8v-2h2c1.1 0 2-.9 2-2h1.9c3.95.49 7 3.85 7 7.93 0 1.55-.47 3-1.9 3.97z"/>
          </svg>
          <?php echo esc_html__('Vinyl Rooftop Bar', 'saigonhoreca'); ?>
        </div>
        <h3 class="rkf-music__subtitle"><?php echo esc_html__('Ẩm thực Omakase hòa quyện cùng nốt nhạc Vinyl', 'saigonhoreca'); ?></h3>
        
        <div class="rkf-music__glass-card">
          <span class="pp-corner-ornament pp-corner-ornament--top-left" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--top-right" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--bottom-left" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--bottom-right" aria-hidden="true"></span>
          
          <div class="rkf-music__body-text">
            <p><?php echo esc_html__('Tại Roka Fella, ngoài trải nghiệm hấp dẫn với ẩm thực Omakase, còn có một không gian âm nhạc cổ điển sang trọng trên tầng thượng nhà hàng được gọi là quầy bar vinyl. Tại đây, bậc thầy pha chế Aris Sanjaya sẽ chiêu đãi bạn những ly cocktail độc đáo, thưởng thức cùng giai điệu soul, jazz, và R&B cổ điển.', 'saigonhoreca'); ?></p>
            <p style="margin-bottom: 0; font-weight: 600; color: var(--gold);"><?php echo esc_html__('Sự giao thoa đầy say đắm giữa âm thanh mộc mạc của đĩa than và ly cocktail tinh tế tạo nên một dấu ấn khó phai.', 'saigonhoreca'); ?></p>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- Section 8C: Grand Saigon Horeca Corporate Call-to-Action -->
<section class="pp-section-rf pp-rf-cta-corporate">
  <!-- Dynamic grid pattern and technical lines -->
  <div class="pp-cta-grid-pattern-rf" aria-hidden="true"></div>
  <div class="pp-ambient-glow-rf pp-ambient-glow-rf--center" aria-hidden="true" style="opacity: 0.7;"></div>

  <div class="pp-container-shared">
    <div class="rkf-cta-corp__box">
      
      <!-- Top badge -->
      <div class="pp-badge-rf" style="margin: 0 auto 1.5rem auto;">
        <span style="color: var(--gold); margin-right: 0.5rem;">//</span> <?php echo esc_html__('ĐỒNG HÀNH THIẾT KẾ & SẢN XUẤT', 'saigonhoreca'); ?>
      </div>

      <!-- Main Headline -->
      <h2 class="rkf-cta-corp__title">
        <?php echo __('Kiến Tạo Không Gian Bếp Omakase <span class="pp-highlight-gold-rf">Chuẩn Nhật Bản</span>', 'saigonhoreca'); ?>
      </h2>

      <!-- Description -->
      <div class="rkf-cta-corp__desc">
        <p><?php echo esc_html__('Toàn bộ hệ thống quầy Sushi bảo quản lạnh chuyên nghiệp và thiết bị bếp công nghiệp của Roka Fella đều được Saigon Horeca tư vấn, thiết kế bản vẽ và gia công sản xuất trực tiếp.', 'saigonhoreca'); ?></p>
        <p><?php echo esc_html__('Bạn đang tìm kiếm một đối tác tin cậy để thiết kế hệ thống bếp nhà hàng sang trọng, quầy bar hiện đại hoặc dự án F&B tiêu chuẩn quốc tế? Hãy để Saigon Horeca biến ý tưởng của bạn thành kiệt tác.', 'saigonhoreca'); ?></p>
      </div>

      <!-- Grid of Services/Value propositions -->
      <div class="rkf-cta-corp__specs-grid">
        <div class="rkf-cta-corp__spec-item">
          <span class="rkf-cta-corp__spec-num">01</span>
          <div class="rkf-cta-corp__spec-content">
            <strong><?php echo esc_html__('QUY HOẠCH TỐI ƯU', 'saigonhoreca'); ?></strong>
            <span><?php echo esc_html__('Bản vẽ mặt bằng 2D/3D chi tiết, tối ưu luồng di chuyển một chiều chuẩn F&B.', 'saigonhoreca'); ?></span>
          </div>
        </div>

        <div class="rkf-cta-corp__spec-item">
          <span class="rkf-cta-corp__spec-num">02</span>
          <div class="rkf-cta-corp__spec-content">
            <strong><?php echo esc_html__('SẢN XUẤT ĐỘC BẢN', 'saigonhoreca'); ?></strong>
            <span><?php echo esc_html__('Sản xuất trực tiếp thiết bị inox SUS304 cao cấp theo module thực đơn chuyên biệt.', 'saigonhoreca'); ?></span>
          </div>
        </div>

        <div class="rkf-cta-corp__spec-item">
          <span class="rkf-cta-corp__spec-num">03</span>
          <div class="rkf-cta-corp__spec-content">
            <strong>HỆ THỐNG COLD CHAIN</strong>
            <span><?php echo esc_html__('Hệ thống thiết bị lạnh nhập khẩu Hoshizaki, bảo quản nguyên liệu tươi sống hoàn hảo.', 'saigonhoreca'); ?></span>
          </div>
        </div>
      </div>

      <!-- Action Button -->
      <div class="rkf-cta-corp__btn-wrapper">
        <a href="<?php echo esc_url(home_url('/lien-he/')); ?>" class="rkf-cta-corp__btn">
          <span><?php echo esc_html__('LIÊN HỆ TƯ VẤN NGAY', 'saigonhoreca'); ?></span>
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <line x1="5" y1="12" x2="19" y2="12"></line>
            <polyline points="12 5 19 12 12 19"></polyline>
          </svg>
        </a>
      </div>

    </div>
  </div>
</section>
