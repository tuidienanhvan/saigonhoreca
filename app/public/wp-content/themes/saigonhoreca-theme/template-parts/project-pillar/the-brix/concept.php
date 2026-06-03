<?php
/**
 * Project Pillar — the-brix
 * Section #4: concept
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-concept-brix">
  <!-- Đường lượn sóng nghệ thuật làm ranh giới tách section nhẹ nhàng ở đỉnh -->
  <div class="pp-concept-brix__wave-divider-top" aria-hidden="true">
    <svg viewBox="0 0 1440 60" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M0,30 C240,75 480,75 720,30 C960,-15 1200,-15 1440,30 L1440,0 L0,0 Z" fill="#090c12"/>
      <path d="M0,35 C360,5 720,65 1080,35 C1260,20 1350,27 1440,35" stroke="rgba(16, 185, 129, 0.18)" stroke-width="1"/>
    </svg>
  </div>

  <div class="pp-container-shared">
    <!-- Bố cục lưới nghệ thuật phong cách tạp chí -->
    <div class="pp-concept-brix__grid scroll-reveal">

      <!-- Cột trái: Nội dung thuyết minh tạp chí -->
      <div class="pp-concept-brix__editorial">
        
        <div class="pp-concept-brix__badge">
          <span class="pp-concept-brix__badge-accent">//</span> <?php echo esc_html__('Ý TƯỞNG & VẬT LIỆU', 'saigonhoreca'); ?>
        </div>

        <h2 class="pp-concept-brix__title">
          <?php echo esc_html__('Ốc Đảo Nhiệt Đới & Giải Pháp Thông Gió Tự Nhiên', 'saigonhoreca'); ?>
        </h2>

        <!-- Khung trích dẫn nghệ thuật lớn với nét vẽ dừa ẩn hiện rõ ràng -->
        <div class="pp-concept-brix__quote-card">
          <!-- SVG Cây dừa cách điệu nghệ thuật nét rõ nét hơn -->
          <div class="pp-concept-brix__card-palm-svg" aria-hidden="true">
            <svg viewBox="0 0 100 150" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M20,150 C25,110 35,70 50,30 C53,20 57,10 65,5" stroke="#10b981" stroke-width="2" stroke-linecap="round"/>
              <path d="M65,5 C50,2 35,5 20,15 C30,14 45,12 65,5 Z" fill="rgba(16, 185, 129, 0.1)" stroke="#10b981" stroke-width="1.2"/>
              <path d="M65,5 C60,-5 50,-12 35,-18 C42,-8 52,-2 65,5 Z" fill="rgba(16, 185, 129, 0.1)" stroke="#10b981" stroke-width="1.2"/>
              <path d="M65,5 C72,-4 82,-10 95,-12 C85,-5 75,0 65,5 Z" fill="rgba(16, 185, 129, 0.1)" stroke="#10b981" stroke-width="1.2"/>
              <path d="M65,5 C75,12 85,22 92,35 C82,25 72,15 65,5 Z" fill="rgba(16, 185, 129, 0.1)" stroke="#10b981" stroke-width="1.2"/>
              <circle cx="58" cy="10" r="4.5" fill="#10b981"/>
              <circle cx="68" cy="14" r="3.5" fill="#10b981"/>
            </svg>
          </div>
          
          <span class="pp-concept-brix__quote-mark">“</span>
          <p class="pp-concept-brix__quote-text">
            <?php echo esc_html__('Bảo tồn nét mộc mạc của gạch gốm địa phương và gỗ tự nhiên, kiến tạo một không gian thụ động đón gió trời tự do tựa như ốc đảo nghỉ dưỡng biệt lập.', 'saigonhoreca'); ?>
          </p>
        </div>

        <!-- Catalog thuyết minh 2 cột cụ thể -->
        <div class="pp-concept-brix__details">
          <div class="pp-concept-brix__detail-item">
            <div class="pp-concept-brix__detail-meta">
              <span class="pp-concept-brix__detail-num">01</span>
              <h3 class="pp-concept-brix__detail-title"><?php echo esc_html__('Vật liệu Bản địa mộc mạc', 'saigonhoreca'); ?></h3>
            </div>
            <p class="pp-concept-brix__detail-desc">
              <?php echo esc_html__('Sự kết hợp ngẫu hứng giữa gạch nung đỏ truyền thống, bê tông mài tối giản và các thanh gỗ trần chạy dọc trần nhà tạo nên một nhịp điệu kiến trúc hoài cổ nhưng mang hơi thở hiện đại.', 'saigonhoreca'); ?>
            </p>
          </div>

          <div class="pp-concept-brix__detail-item">
            <div class="pp-concept-brix__detail-meta">
              <span class="pp-concept-brix__detail-num">02</span>
              <h3 class="pp-concept-brix__detail-title"><?php echo esc_html__('Thông gió chéo thụ động', 'saigonhoreca'); ?></h3>
            </div>
            <p class="pp-concept-brix__detail-desc">
              <?php echo esc_html__('Tường gạch thông gió kết hợp hồ bơi trung tâm tạo lực đẩy đưa luồng khí mát lưu thông tự do xuyên suốt không gian, giảm thiểu sự phụ thuộc vào điều hòa nhân tạo.', 'saigonhoreca'); ?>
            </p>
          </div>
        </div>

      </div>

      <!-- Cột phải: Media Showcase với Khung ảnh Sóng biển nghệ thuật -->
      <div class="pp-concept-brix__side">
        <!-- Định nghĩa Clip Path đường sóng biển cho ảnh -->
        <svg width="0" height="0" style="position: absolute;">
          <defs>
            <clipPath id="brix-image-wave-clip" clipPathUnits="objectBoundingBox">
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

        <div class="pp-concept-brix__image-wave-container">
          
          <!-- Lớp 1: Các sóng trang trí nền siêu lớn (Giant decorative waves) ở phía sau để tạo bối cảnh sóng biển cuộn trào rõ rệt -->
          <div class="pp-concept-brix__background-waves" aria-hidden="true">
            <!-- Sóng cuộn trào lớn phía dưới bên phải -->
            <svg class="pp-concept-brix__giant-wave wave-1" viewBox="0 0 300 150" fill="none" xmlns="http://www.w3.org/2000/svg">
              <defs>
                <linearGradient id="brix-wave-grad-giant-1" x1="0" y1="0" x2="0" y2="1">
                  <stop offset="0%" stop-color="rgba(16, 185, 129, 0.25)"/>
                  <stop offset="100%" stop-color="rgba(9, 12, 18, 0)"/>
                </linearGradient>
                <linearGradient id="brix-mask-grad-giant-1" x1="0" y1="0" x2="1" y2="0">
                  <stop offset="0%" stop-color="black" />
                  <stop offset="18%" stop-color="white" />
                  <stop offset="82%" stop-color="white" />
                  <stop offset="100%" stop-color="black" />
                </linearGradient>
                <mask id="brix-wave-mask-giant-1">
                  <rect x="0" y="0" width="300" height="150" fill="url(#brix-mask-grad-giant-1)" />
                </mask>
              </defs>
              <g mask="url(#brix-wave-mask-giant-1)">
                <path d="M0,120 C50,150 100,70 150,110 C200,150 250,80 300,120 L300,150 L0,150 Z" fill="url(#brix-wave-grad-giant-1)" />
                <path d="M0,105 C60,135 120,65 180,105 C240,145 270,95 300,105" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" opacity="0.8"/>
                <path d="M0,90 C80,120 140,50 200,90 C260,130 280,100 300,90" stroke="rgba(255, 255, 255, 0.4)" stroke-width="1.5" stroke-linecap="round" stroke-dasharray="8 6"/>
              </g>
            </svg>

            <!-- Sóng cuộn trào lớn phía trên bên trái -->
            <svg class="pp-concept-brix__giant-wave wave-2" viewBox="0 0 300 120" fill="none" xmlns="http://www.w3.org/2000/svg">
              <defs>
                <linearGradient id="brix-mask-grad-giant-2" x1="0" y1="0" x2="1" y2="0">
                  <stop offset="0%" stop-color="black" />
                  <stop offset="15%" stop-color="white" />
                  <stop offset="85%" stop-color="white" />
                  <stop offset="100%" stop-color="black" />
                </linearGradient>
                <mask id="brix-wave-mask-giant-2">
                  <rect x="0" y="0" width="300" height="120" fill="url(#brix-mask-grad-giant-2)" />
                </mask>
              </defs>
              <g mask="url(#brix-wave-mask-giant-2)">
                <path d="M0,40 C60,10 120,80 180,30 C240,-20 270,40 300,15" stroke="rgba(16, 185, 129, 0.45)" stroke-width="3" stroke-linecap="round" />
                <path d="M0,55 C80,25 140,95 200,45 C260,-5 285,50 300,30" stroke="rgba(255, 255, 255, 0.35)" stroke-width="1.5" stroke-dasharray="6 4"/>
              </g>
            </svg>
          </div>

          <!-- Lớp 2: Khung nền lượn sóng lệch góc tạo khối 3D cho ảnh (Shadow-like wave card background) -->
          <div class="pp-concept-brix__image-wave-bg" aria-hidden="true">
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

          <!-- Lớp 3: Ảnh chính được clip-path theo hình sóng nước -->
          <div class="pp-concept-brix__image-wave-wrap">
            <img src="<?php echo sgh_img('the-brix/the-brix-quay-bar-be-boi-tropical.jpg'); ?>" alt="<?php echo esc_attr__('The Brix Traditional Materials Concept', 'saigonhoreca'); ?>" loading="lazy">
          </div>

          <!-- Lớp 4: KHUNG VIỀN SÓNG BIỂN NGHỆ THUẬT (Creative Wave Outline Border) bọc ngoài khít khao -->
          <div class="pp-concept-brix__wave-outline-container" aria-hidden="true">
            <svg viewBox="0 0 100 100" preserveAspectRatio="none" class="pp-concept-brix__wave-outline-svg">
              <!-- Đường viền nét mềm mại vẽ theo hình dáng sóng của ảnh, tạo viền nổi sáng loáng -->
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
                    stroke="url(#brix-outline-gradient)" 
                    stroke-width="1.8" 
                    stroke-linejoin="round"
                    stroke-linecap="round"/>
              <defs>
                <linearGradient id="brix-outline-gradient" x1="0" y1="0" x2="1" y2="1">
                  <stop offset="0%" stop-color="#10b981"/>
                  <stop offset="30%" stop-color="#34d399"/>
                  <stop offset="70%" stop-color="rgba(255, 255, 255, 0.8)"/>
                  <stop offset="100%" stop-color="#10b981"/>
                </linearGradient>
              </defs>
            </svg>
            
            <!-- Vẽ thêm bọt sóng biển nhỏ (Foam decoration) ở các góc của khung tranh -->
            <svg class="pp-concept-brix__foam foam-top-right" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
              <circle cx="15" cy="15" r="3" fill="#ffffff" opacity="0.8"/>
              <circle cx="25" cy="10" r="2" fill="#ffffff" opacity="0.6"/>
              <circle cx="30" cy="22" r="1.5" fill="#10b981" opacity="0.7"/>
              <path d="M5,25 C12,18 20,30 35,15" stroke="rgba(255,255,255,0.5)" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
 
            <svg class="pp-concept-brix__foam foam-bottom-left" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
              <circle cx="10" cy="20" r="2.5" fill="#ffffff" opacity="0.9"/>
              <circle cx="22" cy="28" r="1.8" fill="#10b981" opacity="0.8"/>
              <circle cx="8" cy="32" r="1.2" fill="#ffffff" opacity="0.6"/>
              <path d="M10,8 C22,12 18,32 32,28" stroke="rgba(16, 185, 129, 0.6)" stroke-width="1.2" stroke-linecap="round"/>
            </svg>
          </div>

          <!-- Lớp 5: Sóng nước gợn nổi trực tiếp bên dưới chân ảnh và phủ lên cạnh viền -->
          <div class="pp-concept-brix__image-wave-overlay" aria-hidden="true">
            <svg viewBox="0 0 500 100" fill="none" xmlns="http://www.w3.org/2000/svg">
              <defs>
                <linearGradient id="brix-wave-overlay-gradient" x1="0" y1="0" x2="0" y2="1">
                  <stop offset="0%" stop-color="rgba(16, 185, 129, 0.65)"/>
                  <stop offset="100%" stop-color="#090c12"/>
                </linearGradient>
                <linearGradient id="brix-wave-overlay-mask-grad" x1="0" y1="0" x2="1" y2="0">
                  <stop offset="0%" stop-color="black" />
                  <stop offset="12%" stop-color="white" />
                  <stop offset="88%" stop-color="white" />
                  <stop offset="100%" stop-color="black" />
                </linearGradient>
                <mask id="brix-wave-overlay-mask">
                  <rect x="0" y="0" width="500" height="100" fill="url(#brix-wave-overlay-mask-grad)" />
                </mask>
              </defs>
              <g mask="url(#brix-wave-overlay-mask)">
                <!-- Sóng chính đè lên viền gầm ảnh, màu sắc tươi sáng -->
                <path d="M0,50 C100,85 200,10 300,75 C370,110 440,80 500,65 L500,100 L0,100 Z" fill="url(#brix-wave-overlay-gradient)" opacity="0.85"/>
                <path d="M0,50 C100,85 200,10 300,75 C370,110 440,80 500,65" stroke="#10b981" stroke-width="4.5" stroke-linecap="round"/>
                <path d="M0,68 C120,25 250,108 380,68 C420,55 460,62 500,65" stroke="#ffffff" stroke-width="2" stroke-linecap="round" opacity="0.9" stroke-dasharray="10 7"/>
                <path d="M0,80 C150,55 300,95 450,75" stroke="rgba(255, 255, 255, 0.4)" stroke-width="1.2" stroke-linecap="round" stroke-dasharray="4 4"/>
              </g>
            </svg>
          </div>
        </div>

        <div class="pp-image-caption-shared" style="margin-top: 1.8rem; text-align: center;">
          <?php echo esc_html__('Hồ bơi vô cực trung tâm ôm trọn không gian thưởng thức ngoài trời phóng khoáng', 'saigonhoreca'); ?>
        </div>
      </div>

    </div>
  </div>

  <!-- Sóng biển cuộn nhẹ ở chân trang tạo ranh giới mềm mại -->
  <div class="pp-concept-brix__wave-divider-bottom" aria-hidden="true">
    <svg viewBox="0 0 1440 100" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M0,50 C360,110 720,-10 1080,70 C1260,110 1350,80 1440,50 L1440,100 L0,100 Z" fill="#06080c" opacity="0.5"/>
      <path d="M0,60 C300,120 600,0 900,90 C1170,135 1305,90 1440,60" stroke="rgba(16, 185, 129, 0.25)" stroke-width="1.5" stroke-dasharray="10 5"/>
    </svg>
  </div>
</section>
