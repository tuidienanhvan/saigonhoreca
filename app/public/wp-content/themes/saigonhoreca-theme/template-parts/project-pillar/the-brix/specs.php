<?php
/**
 * Project Pillar — the-brix
 * Section #8: specs
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-specs-brix">
  <div class="pp-container-shared">
    <div class="pp-specs-brix__grid scroll-reveal">

      <div class="pp-specs-brix__body">
        
        <div class="pp-specs-brix__badge">
          <span class="pp-specs-brix__badge-accent">//</span> <?php echo esc_html__('ẨM THỰC & TRẢI NGHIỆM', 'saigonhoreca'); ?>
        </div>

        <h2 class="pp-specs-brix__title">
          <?php echo esc_html__('Tinh Hoa Ẩm Thực Âu Giữa Lòng Ốc Đảo', 'saigonhoreca'); ?>
        </h2>

        <!-- Khung trích dẫn nghệ thuật lớn với nét vẽ cocktail chìm tinh tế -->
        <div class="pp-specs-brix__quote-card">
          <!-- SVG Ly Cocktail cách điệu nghệ thuật mảnh mai lồng chìm -->
          <div class="pp-specs-brix__card-cocktail-svg" aria-hidden="true">
            <svg viewBox="0 0 100 150" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M20,25 L80,25 L50,80 Z" fill="none" stroke="#10b981" stroke-width="1.5" stroke-linejoin="round" opacity="0.65"/>
              <path d="M50,80 L50,130" stroke="#10b981" stroke-width="2" stroke-linecap="round" opacity="0.65"/>
              <path d="M35,130 L65,130" stroke="#10b981" stroke-width="2" stroke-linecap="round" opacity="0.65"/>
              <!-- Lát chanh cài miệng ly -->
              <circle cx="28" cy="20" r="10" fill="none" stroke="#34d399" stroke-width="1.2" opacity="0.75"/>
              <path d="M28,10 L28,30" stroke="#34d399" stroke-width="0.8" opacity="0.75"/>
              <path d="M18,20 L38,20" stroke="#34d399" stroke-width="0.8" opacity="0.75"/>
              <!-- Nhánh mint/trang trí -->
              <path d="M45,60 L78,10" stroke="#5eead4" stroke-width="1.5" stroke-linecap="round" opacity="0.85"/>
              <!-- Bong bóng ga của nước soda mát lạnh -->
              <circle cx="48" cy="40" r="1.5" fill="#5eead4" opacity="0.6"/>
              <circle cx="54" cy="50" r="2" fill="#5eead4" opacity="0.4"/>
              <circle cx="42" cy="32" r="1.2" fill="#ffffff" opacity="0.5"/>
            </svg>
          </div>
          
          <span class="pp-specs-brix__quote-mark">“</span>
          <p class="pp-specs-brix__quote-text">
            <?php echo esc_html__('Sự giao thoa đầy say mê giữa kỹ nghệ ẩm thực Âu cổ điển với nguồn nguyên liệu tươi rói bản địa, được thưởng thức bên cạnh hồ bơi trong vắt rực rỡ nắng vàng.', 'saigonhoreca'); ?>
          </p>
        </div>

        <!-- Catalog thuyết minh chi tiết ẩm thực 2 cột -->
        <div class="pp-specs-brix__details">
          <div class="pp-specs-brix__detail-item">
            <div class="pp-specs-brix__detail-meta">
              <span class="pp-specs-brix__detail-num">01</span>
              <h3 class="pp-specs-brix__detail-title"><?php echo esc_html__('Lửa hồng Open-Fire & Hải sản tươi', 'saigonhoreca'); ?></h3>
            </div>
            <p class="pp-specs-brix__detail-desc">
              <?php echo esc_html__('Điểm nhấn ẩm thực là các món hải sản và steak hảo hạng nướng trên lò than hoa mở mộc mạc, giữ trọn vẹn nước ngọt tự nhiên cùng hương thơm hun khói nồng nàn quyến rũ.', 'saigonhoreca'); ?>
            </p>
          </div>

          <div class="pp-specs-brix__detail-item">
            <div class="pp-specs-brix__detail-meta">
              <span class="pp-specs-brix__detail-num">02</span>
              <h3 class="pp-specs-brix__detail-title"><?php echo esc_html__('Mixology Nhiệt đới rực rỡ', 'saigonhoreca'); ?></h3>
            </div>
            <p class="pp-specs-brix__detail-desc">
              <?php echo esc_html__('Thực đơn đồ uống được chế tác công phu bởi những nhà mixologist tài năng, kết hợp trái cây nhiệt đới tươi mới của Việt Nam và các loại rượu hảo hạng dưới bóng cọ.', 'saigonhoreca'); ?>
            </p>
          </div>
        </div>

      </div>

      <div class="pp-specs-brix__side">
        <span class="pp-specs-brix__coord pp-specs-brix__coord--tl" aria-hidden="true">10.78°N</span>
        <span class="pp-specs-brix__coord pp-specs-brix__coord--tr" aria-hidden="true">106.74°E</span>
        <span class="pp-specs-brix__coord pp-specs-brix__coord--bl" aria-hidden="true">SEC.08</span>
        <span class="pp-specs-brix__coord pp-specs-brix__coord--br" aria-hidden="true">D2-HCM</span>

        <!-- Định nghĩa Clip Path đường sóng biển cho ảnh của Specs -->
        <svg width="0" height="0" style="position: absolute;">
          <defs>
            <clipPath id="brix-specs-image-wave-clip" clipPathUnits="objectBoundingBox">
              <path d="M 0.05,0.06 
                       C 0.28,0.01 0.52,0.1 0.76,0.04 
                       C 0.88,0.01 0.94,0.06 0.94,0.12 
                       C 0.97,0.35 0.91,0.58 0.95,0.82 
                       C 0.94,0.89 0.86,0.87 0.72,0.92 
                       C 0.52,0.98 0.36,0.88 0.16,0.93 
                       C 0.06,0.95 0.04,0.89 0.05,0.82 
                       C 0.02,0.58 0.07,0.35 0.05,0.12 
                       C 0.05,0.06 0.05,0.06 0.05,0.06 Z" />
            </clipPath>
          </defs>
        </svg>

        <div class="pp-specs-brix__image-wave-container">
          
          <!-- Lớp 1: Các sóng trang trí nền siêu lớn ở phía sau -->
          <div class="pp-specs-brix__background-waves" aria-hidden="true">
            <!-- Sóng cuộn trào lớn phía dưới bên phải -->
            <svg class="pp-specs-brix__giant-wave wave-1" viewBox="0 0 300 150" fill="none" xmlns="http://www.w3.org/2000/svg">
              <defs>
                <linearGradient id="brix-specs-wave-grad-giant-1" x1="0" y1="0" x2="0" y2="1">
                  <stop offset="0%" stop-color="rgba(16, 185, 129, 0.25)"/>
                  <stop offset="100%" stop-color="rgba(9, 12, 18, 0)"/>
                </linearGradient>
                <linearGradient id="brix-specs-mask-grad-giant-1" x1="0" y1="0" x2="1" y2="0">
                  <stop offset="0%" stop-color="black" />
                  <stop offset="18%" stop-color="white" />
                  <stop offset="82%" stop-color="white" />
                  <stop offset="100%" stop-color="black" />
                </linearGradient>
                <mask id="brix-specs-wave-mask-giant-1">
                  <rect x="0" y="0" width="300" height="150" fill="url(#brix-specs-mask-grad-giant-1)" />
                </mask>
              </defs>
              <g mask="url(#brix-specs-wave-mask-giant-1)">
                <path d="M0,120 C50,150 100,70 150,110 C200,150 250,80 300,120 L300,150 L0,150 Z" fill="url(#brix-specs-wave-grad-giant-1)" />
                <path d="M0,105 C60,135 120,65 180,105 C240,145 270,95 300,105" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" opacity="0.8"/>
                <path d="M0,90 C80,120 140,50 200,90 C260,130 280,100 300,90" stroke="rgba(255, 255, 255, 0.4)" stroke-width="1.5" stroke-linecap="round" stroke-dasharray="8 6"/>
              </g>
            </svg>

            <!-- Sóng cuộn trào lớn phía trên bên trái -->
            <svg class="pp-specs-brix__giant-wave wave-2" viewBox="0 0 300 120" fill="none" xmlns="http://www.w3.org/2000/svg">
              <defs>
                <linearGradient id="brix-specs-mask-grad-giant-2" x1="0" y1="0" x2="1" y2="0">
                  <stop offset="0%" stop-color="black" />
                  <stop offset="15%" stop-color="white" />
                  <stop offset="85%" stop-color="white" />
                  <stop offset="100%" stop-color="black" />
                </linearGradient>
                <mask id="brix-specs-wave-mask-giant-2">
                  <rect x="0" y="0" width="300" height="120" fill="url(#brix-specs-mask-grad-giant-2)" />
                </mask>
              </defs>
              <g mask="url(#brix-specs-wave-mask-giant-2)">
                <path d="M0,40 C60,10 120,80 180,30 C240,-20 270,40 300,15" stroke="rgba(16, 185, 129, 0.45)" stroke-width="3" stroke-linecap="round" />
                <path d="M0,55 C80,25 140,95 200,45 C260,-5 285,50 300,30" stroke="rgba(255, 255, 255, 0.35)" stroke-width="1.5" stroke-dasharray="6 4"/>
              </g>
            </svg>
          </div>

          <!-- Lớp 2: Khung nền lượn sóng lệch góc tạo khối 3D cho ảnh -->
          <div class="pp-specs-brix__image-wave-bg" aria-hidden="true">
            <svg viewBox="0 0 100 100" preserveAspectRatio="none">
              <path d="M 5,6 
                       C 28,1 52,10 76,4 
                       C 88,1 94,6 94,12 
                       C 97,35 91,58 95,82 
                       C 94,89 86,87 72,92 
                       C 52,98 36,88 16,93 
                       C 6,95 04,89 5,82 
                       C 2,58 7,35 5,12 
                       C 5,6 5,6 5,6 Z" fill="rgba(16, 185, 129, 0.25)" filter="drop-shadow(0px 10px 15px rgba(16, 185, 129, 0.3))" />
            </svg>
          </div>

          <!-- Lớp 3: Khung bọc ảnh chính được clip-path theo hình sóng nước -->
          <div class="pp-specs-brix__image-wave-wrap">
            <div class="pp-specs-brix__image-tag">European Cuisine</div>
            <img src="<?php echo sgh_img('the-brix/the-brix-khong-gian-am-thuc-len-den-dem.jpg'); ?>" alt="<?php echo esc_attr__('The Brix European Cuisine', 'saigonhoreca'); ?>" loading="lazy">
          </div>

          <!-- Lớp 4: KHUNG VIỀN SÓNG BIỂN NGHỆ THUẬT bọc ngoài khít khao -->
          <div class="pp-specs-brix__wave-outline-container" aria-hidden="true">
            <svg viewBox="0 0 100 100" preserveAspectRatio="none" class="pp-specs-brix__wave-outline-svg">
              <path d="M 5,6 
                       C 28,1 52,10 76,4 
                       C 88,1 94,6 94,12 
                       C 97,35 91,58 95,82 
                       C 94,89 86,87 72,92 
                       C 52,98 36,88 16,93 
                       C 6,95 04,89 5,82 
                       C 2,58 7,35 5,12 
                       C 5,6 5,6 5,6 Z" 
                    fill="none" 
                    stroke="url(#brix-specs-outline-gradient)" 
                    stroke-width="1.8" 
                    stroke-linejoin="round"
                    stroke-linecap="round"/>
              <defs>
                <linearGradient id="brix-specs-outline-gradient" x1="0" y1="0" x2="1" y2="1">
                  <stop offset="0%" stop-color="#10b981"/>
                  <stop offset="30%" stop-color="#34d399"/>
                  <stop offset="70%" stop-color="rgba(255, 255, 255, 0.8)"/>
                  <stop offset="100%" stop-color="#10b981"/>
                </linearGradient>
              </defs>
            </svg>
            
            <!-- Vẽ thêm bọt sóng biển nhỏ (Foam) ở các góc -->
            <svg class="pp-specs-brix__foam foam-top-right" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
              <circle cx="15" cy="15" r="3" fill="#ffffff" opacity="0.8"/>
              <circle cx="25" cy="10" r="2" fill="#ffffff" opacity="0.6"/>
              <circle cx="30" cy="22" r="1.5" fill="#10b981" opacity="0.7"/>
              <path d="M5,25 C12,18 20,30 35,15" stroke="rgba(255,255,255,0.5)" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
 
            <svg class="pp-specs-brix__foam foam-bottom-left" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
              <circle cx="10" cy="20" r="2.5" fill="#ffffff" opacity="0.9"/>
              <circle cx="22" cy="28" r="1.8" fill="#10b981" opacity="0.8"/>
              <circle cx="8" cy="32" r="1.2" fill="#ffffff" opacity="0.6"/>
              <path d="M10,8 C22,12 18,32 32,28" stroke="rgba(16, 185, 129, 0.6)" stroke-width="1.2" stroke-linecap="round"/>
            </svg>
          </div>

          <!-- Lớp 5: Sóng nước gợn nổi trực tiếp bên dưới chân ảnh -->
          <div class="pp-specs-brix__image-wave-overlay" aria-hidden="true">
            <svg viewBox="0 0 500 100" fill="none" xmlns="http://www.w3.org/2000/svg">
              <defs>
                <linearGradient id="brix-specs-wave-overlay-gradient" x1="0" y1="0" x2="0" y2="1">
                  <stop offset="0%" stop-color="rgba(16, 185, 129, 0.65)"/>
                  <stop offset="100%" stop-color="#0c1628"/>
                </linearGradient>
                <linearGradient id="brix-specs-wave-overlay-mask-grad" x1="0" y1="0" x2="1" y2="0">
                  <stop offset="0%" stop-color="black" />
                  <stop offset="12%" stop-color="white" />
                  <stop offset="88%" stop-color="white" />
                  <stop offset="100%" stop-color="black" />
                </linearGradient>
                <mask id="brix-specs-wave-overlay-mask">
                  <rect x="0" y="0" width="500" height="100" fill="url(#brix-specs-wave-overlay-mask-grad)" />
                </mask>
              </defs>
              <g mask="url(#brix-specs-wave-overlay-mask)">
                <path d="M0,50 C100,85 200,10 300,75 C370,110 440,80 500,65 L500,100 L0,100 Z" fill="url(#brix-specs-wave-overlay-gradient)" opacity="0.85"/>
                <path d="M0,50 C100,85 200,10 300,75 C370,110 440,80 500,65" stroke="#10b981" stroke-width="4.5" stroke-linecap="round"/>
                <path d="M0,68 C120,25 250,108 380,68 C420,55 460,62 500,65" stroke="#ffffff" stroke-width="2" stroke-linecap="round" opacity="0.9" stroke-dasharray="10 7"/>
                <path d="M0,80 C150,55 300,95 450,75" stroke="rgba(255, 255, 255, 0.4)" stroke-width="1.2" stroke-linecap="round" stroke-dasharray="4 4"/>
              </g>
            </svg>
          </div>
        </div>

        <div class="pp-image-caption-shared" style="margin-top: 1.8rem; text-align: center;">
          <?php echo esc_html__('Vẻ đẹp lung linh huyền ảo của The Brix về đêm với những dải đèn ấm áp phản chiếu trên mặt nước hồ bơi xanh ngọc bích trữ tình', 'saigonhoreca'); ?>
        </div>
      </div>

    </div>
  </div>
</section>
