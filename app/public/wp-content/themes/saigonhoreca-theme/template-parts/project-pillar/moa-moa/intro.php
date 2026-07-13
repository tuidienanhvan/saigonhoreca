<?php
/**
 * Project Pillar — moa-moa (Moa Moa Pasta Club)
 * Section #2: intro — editorial asymmetric layout with dropcap + 3D image showcase.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section id="moa-intro" class="pp-section-moa pp-moa-intro">
  <!-- Khai báo các Clip Path hữu cơ (hình dạng bột nhào Pasta) dùng chung cho toàn bộ các section -->
  <svg class="moa-svg-defs" style="position: absolute; width: 0; height: 0; overflow: hidden;" aria-hidden="true">
    <defs>
      <clipPath id="moa-clip-dough-1" clipPathUnits="objectBoundingBox">
        <path d="M 0.08,0.12 C 0.25,0.03 0.6,-0.03 0.85,0.06 C 0.96,0.14 0.99,0.35 0.97,0.68 C 0.95,0.86 0.82,0.97 0.58,0.99 C 0.35,1.01 0.12,0.96 0.04,0.78 C -0.04,0.58 0.01,0.24 0.08,0.12 Z" />
      </clipPath>
      <clipPath id="moa-clip-dough-2" clipPathUnits="objectBoundingBox">
        <path d="M 0.1,0.05 C 0.35,0.01 0.72,-0.02 0.9,0.15 C 1.02,0.32 0.98,0.6 0.92,0.82 C 0.86,0.96 0.58,0.99 0.4,0.97 C 0.2,0.95 0.05,0.85 0.02,0.6 C -0.02,0.38 0.02,0.16 0.1,0.05 Z" />
      </clipPath>
      <clipPath id="moa-clip-dough-3" clipPathUnits="objectBoundingBox">
        <path d="M 0.15,0.06 C 0.42,-0.01 0.78,0.05 0.88,0.18 C 0.98,0.32 0.95,0.65 0.9,0.85 C 0.82,0.98 0.48,0.99 0.3,0.96 C 0.12,0.92 0.03,0.78 0.01,0.5 C -0.01,0.28 0.03,0.12 0.15,0.06 Z" />
      </clipPath>
      <clipPath id="moa-clip-dough-4" clipPathUnits="objectBoundingBox">
        <path d="M 0.12,0.08 C 0.38,0.01 0.7,0.04 0.88,0.12 C 0.98,0.22 0.99,0.5 0.95,0.78 C 0.9,0.92 0.72,0.98 0.48,0.97 C 0.25,0.96 0.08,0.88 0.04,0.65 C -0.02,0.48 0.02,0.22 0.12,0.08 Z" />
      </clipPath>
      <clipPath id="moa-clip-dough-5" clipPathUnits="objectBoundingBox">
        <path d="M 0.06,0.15 C 0.2,0.05 0.65,-0.02 0.85,0.08 C 0.98,0.18 0.96,0.48 0.94,0.72 C 0.92,0.9 0.72,0.99 0.5,0.97 C 0.28,0.95 0.1,0.85 0.04,0.62 C -0.02,0.42 0.01,0.22 0.06,0.15 Z" />
      </clipPath>
      <clipPath id="moa-clip-dough-6" clipPathUnits="objectBoundingBox">
        <path d="M 0.15,0.08 C 0.45,0.02 0.78,0.05 0.9,0.18 C 1,0.32 0.96,0.58 0.92,0.8 C 0.88,0.95 0.62,0.99 0.45,0.96 C 0.28,0.93 0.1,0.82 0.04,0.58 C -0.02,0.38 0.02,0.18 0.15,0.08 Z" />
      </clipPath>
      <clipPath id="moa-clip-dough-7" clipPathUnits="objectBoundingBox">
        <path d="M 0.05,0.1 C 0.3,0.03 0.7,0.05 0.92,0.12 C 0.98,0.22 0.99,0.52 0.97,0.75 C 0.95,0.92 0.75,0.98 0.5,0.97 C 0.25,0.96 0.08,0.88 0.03,0.65 C -0.02,0.45 0.01,0.22 0.05,0.1 Z" />
      </clipPath>
    </defs>
  </svg>

  <!-- Họa tiết sợi pasta / lúa mì uốn lượn chìm nghệ thuật và lưới kỹ thuật -->
  <div class="pp-watermark-wheat-moa" aria-hidden="true">
    <svg viewBox="0 0 200 200" fill="none" stroke="currentColor" stroke-width="0.8">
      <!-- Nhánh lúa mì chính ở giữa -->
      <path d="M100 190 V30" stroke-width="1.2" stroke-linecap="round"/>
      <!-- Hạt lúa mì xếp chéo -->
      <path d="M100 40 C85 35 75 25 80 15 C95 18 100 25 100 40 Z M100 40 C115 35 125 25 120 15 C105 18 100 25 100 40 Z" fill="none" stroke-width="1"/>
      <path d="M100 65 C82 60 72 48 78 35 C94 38 100 48 100 65 Z M100 65 C118 60 128 48 122 35 C106 38 100 48 100 65 Z" fill="none" stroke-width="1"/>
      <path d="M100 95 C82 90 72 75 78 60 C94 63 100 75 100 95 Z M100 95 C118 90 128 75 122 60 C106 63 100 75 100 95 Z" fill="none" stroke-width="1"/>
      <path d="M100 125 C82 120 72 105 78 90 C94 93 100 105 100 125 Z M100 125 C118 120 128 105 122 90 C106 93 100 105 100 125 Z" fill="none" stroke-width="1"/>
      <!-- Các râu lúa mì (wheat awns) vươn dài thanh thoát -->
      <path d="M80 15 C60 5 40 5 30 10" stroke-linecap="round"/>
      <path d="M120 15 C140 5 160 5 170 10" stroke-linecap="round"/>
      <path d="M78 35 C55 20 35 25 25 35" stroke-linecap="round"/>
      <path d="M122 35 C145 20 165 25 175 35" stroke-linecap="round"/>
      <path d="M78 60 C55 45 30 55 20 70" stroke-linecap="round"/>
      <path d="M122 60 C145 45 170 55 180 70" stroke-linecap="round"/>
      <!-- Các sợi mì (pasta line waves) uốn lượn tự do xung quanh tạo nhịp điệu chuyển động -->
      <path d="M20 180 Q 60 120 100 150 T 180 90" stroke-width="0.6"/>
      <path d="M10 120 Q 50 80 120 110 T 190 40" stroke-width="0.5" stroke-dasharray="3 3"/>
      <path d="M40 195 Q 100 160 130 180 T 195 130" stroke-width="0.7"/>
    </svg>
  </div>

  <div class="pp-ambient-glow-moa pp-ambient-glow-moa--top-right" aria-hidden="true"></div>
  <div class="pp-ambient-glow-moa pp-ambient-glow-moa--center-right" aria-hidden="true"></div>
  <div class="pp-ambient-glow-moa pp-ambient-glow-moa--bottom-left" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <div class="pp-grid-moa">

      <div class="pp-grid-moa__text moa-intro__main">
        <header class="moa-intro__header">
          <div class="pp-badge-moa">
            <svg viewBox="0 0 24 24" fill="currentColor">
              <path d="M12 2L14.85 9.15L22 12L14.85 14.85L12 22L9.15 14.85L2 12L9.15 9.15L12 2Z"/>
            </svg>
            Moa Moa Pasta Club
          </div>
          <h2 class="pp-text-moa__title">
            <?php echo esc_html__('Thiết kế layout: Tối ưu vận hành cho pasta thủ công', 'saigonhoreca'); ?>
          </h2>
          <div class="pp-text-moa__divider" aria-hidden="true"></div>
        </header>

        <div class="moa-intro__body">
          <div class="moa-intro__lead-card">
            <!-- Target Crosshairs trang trí kỹ thuật -->
            <span class="moa-intro__target moa-intro__target--tl" aria-hidden="true">+</span>
            <span class="moa-intro__target moa-intro__target--tr" aria-hidden="true">+</span>
            <span class="moa-intro__target moa-intro__target--bl" aria-hidden="true">+</span>
            <span class="moa-intro__target moa-intro__target--br" aria-hidden="true">+</span>
            <span class="moa-intro__coords" aria-hidden="true">LOC: 10.7741° N / 106.7024° E</span>
            
            <p class="moa-intro__lead"><?php echo esc_html__('Giữa trung tâm Quận 1 sôi động, nơi nhịp sống thành phố chưa bao giờ chậm lại, Moa Moa Pasta Club xuất hiện như một khoảng dừng tinh tế dành cho những ai trân trọng ẩm thực Ý nguyên bản. Tọa lạc tại 40E Ngô Đức Kế, phường Bến Nghé, chỉ cách phố đi bộ Nguyễn Huệ vài bước chân, Moa Moa không chỉ là một nhà hàng pasta – mà là nơi thủ công, kỹ thuật và văn hoá ẩm thực giao thoa trong từng chi tiết.', 'saigonhoreca'); ?></p>
          </div>
          <p><?php echo esc_html__('Đồng hành cùng dự án này, Saigon Horeca không tiếp cận Moa Moa như một công trình bếp thông thường, mà như một bài toán không gian – vận hành – trải nghiệm, nơi mỗi quyết định thiết kế đều phải phục vụ cho tinh thần Italiano autentico.', 'saigonhoreca'); ?></p>
          <p style="margin-bottom: 0;"><?php echo esc_html__('Ẩm thực Ý, đặc biệt là pasta thủ công, luôn đặt quy trình chế biến vào vị trí trung tâm. Tại Moa Moa, bếp không bị giấu sau những bức tường kín, mà trở thành trái tim của nhà hàng – nơi thực khách có thể cảm nhận nhịp điệu làm bếp, từ trộn bột, cán mì, luộc pasta cho đến hoàn thiện món ăn.', 'saigonhoreca'); ?></p>
        </div>
      </div>

      <div class="pp-grid-moa__media moa-intro__side">
        <!-- Blueprint CAD Wrapper bao quanh ảnh -->
        <div class="moa-intro__blueprint-wrapper">
          <!-- Lưới nền CAD -->
          <div class="moa-intro__cad-grid" aria-hidden="true"></div>
          
          <!-- Các thước đo kích thước (Dimension lines) -->
          <div class="moa-intro__dim-line moa-intro__dim-line--top" aria-hidden="true">
            <span class="moa-intro__dim-label">W: 768mm</span>
          </div>
          <div class="moa-intro__dim-line moa-intro__dim-line--left" aria-hidden="true">
            <span class="moa-intro__dim-label">H: 1024mm</span>
          </div>

          <!-- Các nhãn kỹ thuật ảo chạy quanh góc -->
          <div class="moa-intro__tech-label moa-intro__tech-label--tl" aria-hidden="true">SYS_COORD_MOA</div>
          <div class="moa-intro__tech-label moa-intro__tech-label--tr" aria-hidden="true">SCALE 1:25</div>
          <div class="moa-intro__tech-label moa-intro__tech-label--bl" aria-hidden="true">M&E_DRAWING_v2.0</div>
          <div class="moa-intro__tech-label moa-intro__tech-label--br" aria-hidden="true">SECTION_B_02</div>

          <!-- Góc tọa độ CAD -->
          <div class="moa-intro__corner-bracket moa-intro__corner-bracket--tl" aria-hidden="true"></div>
          <div class="moa-intro__corner-bracket moa-intro__corner-bracket--tr" aria-hidden="true"></div>
          <div class="moa-intro__corner-bracket moa-intro__corner-bracket--bl" aria-hidden="true"></div>
          <div class="moa-intro__corner-bracket moa-intro__corner-bracket--br" aria-hidden="true"></div>

          <!-- Container ảnh thực tế -->
          <div class="pp-image-container-shared moa-intro__image-container">
            <img src="<?php echo sgh_img('moa-moa/moa-moa-bien-hieu-chao-mung-pasta-club-loi-vao.webp'); ?>" alt="<?php echo esc_attr__('Lối vào Moa Moa Pasta Club với biển hiệu thương hiệu', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
            <!-- Khung viền SVG vẽ nét khớp với clip path (Dough Shape 1) -->
            <svg class="moa-intro__image-border" viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true">
              <path d="M 8,12 C 25,3 60,-3 85,6 C 96,14 99,35 97,68 C 95,86 82,97 58,99 C 35,101 12,96 4,78 C -4,58 1,24 8,12 Z" vector-effect="non-scaling-stroke" />
            </svg>
            <div class="pp-image-caption-shared"><?php echo esc_html__('“Welcome to the Pasta Club” – dấu ấn thương hiệu Moa Moa ngay lối vào', 'saigonhoreca'); ?></div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>
