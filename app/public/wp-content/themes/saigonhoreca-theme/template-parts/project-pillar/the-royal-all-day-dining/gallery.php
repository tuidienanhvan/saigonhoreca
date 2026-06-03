<?php
/**
 * Project Pillar — the-royal-all-day-dining
 * Section #5 & #6: Bento Mosaic DNA & Asymmetric Closing Statement
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>

<!-- ==========================================
     SECTION 5: BENTO MOSAIC GRID (DNA & OPERATIONS)
     ========================================== -->
<section class="pp__section pp-gallery-bento-section-trd">
  <div class="pp__container pp-container-shared">
    
    <!-- Tiêu đề lớn dẫn nhập cho hệ Bento -->
    <div class="pp-bento-intro">
      <span class="pp-bento-tag"><?php echo esc_html__('DNA & VẬN HÀNH', 'saigonhoreca'); ?></span>
      <h2 class="pp-bento-title"><?php echo esc_html__('Sự Đồng Bộ Tuyệt Đối & Tư Duy Vận Hành Chi Tiết', 'saigonhoreca'); ?></h2>
      <p class="pp-bento-desc"><?php echo esc_html__('Không chỉ lắp đặt thiết bị bếp, Saigon Horeca cùng chủ đầu tư thiết lập một hệ sinh thái vận hành đẳng cấp, đan cài khoa học trong từng khoảng không.', 'saigonhoreca'); ?></p>
    </div>

    <!-- Bento Grid Container -->
    <div class="pp-bento-grid">
      
      <!-- Card 1: Equipment Specs Highlight (Cột 1, Hàng 1 - Chiếm 2 dòng trên Desktop) -->
      <div class="pp-bento-card pp-bento-card--equipment">
        <div class="pp-bento-card__inner">
          <span class="pp-bento-card__num">01</span>
          <h3 class="pp-bento-card__title"><?php echo esc_html__('Hệ thống thiết bị đồng bộ', 'saigonhoreca'); ?></h3>
          <div class="pp-bento-card__divider"></div>
          <ul class="pp-bento-list">
            <li>
              <strong><?php echo esc_html__('Hệ lò nướng hấp combi & lò Salamander', 'saigonhoreca'); ?></strong>
              <span><?php echo esc_html__('Tối ưu hóa nhiệt lượng, giữ trọn hương vị ẩm thực vương giả.', 'saigonhoreca'); ?></span>
            </li>
            <li>
              <strong><?php echo esc_html__('Thiết bị lạnh công nghiệp inox chuyên dụng', 'saigonhoreca'); ?></strong>
              <span><?php echo esc_html__('Bảo quản nguyên liệu tươi sống tiêu chuẩn khắt khe.', 'saigonhoreca'); ?></span>
            </li>
            <li>
              <strong><?php echo esc_html__('Hệ thống bếp gas Fujimak công suất lớn', 'saigonhoreca'); ?></strong>
              <span><?php echo esc_html__('Nhập khẩu chính hãng, độ bền bỉ vượt thời gian.', 'saigonhoreca'); ?></span>
            </li>
          </ul>
        </div>
        <!-- Blueprint Decor Compass -->
        <div class="pp-bento-blueprint-decor pp-bento-blueprint-decor--compass" aria-hidden="true">
          <svg viewBox="0 0 100 100" fill="none" stroke="currentColor">
            <circle cx="50" cy="50" r="45" stroke-dasharray="1 3" stroke-width="0.5"/>
            <circle cx="50" cy="50" r="35" stroke-width="0.5"/>
            <circle cx="50" cy="50" r="2" fill="currentColor"/>
            <line x1="50" y1="5" x2="50" y2="95" stroke-width="0.5" stroke-dasharray="4 4"/>
            <line x1="5" y1="50" x2="95" y2="50" stroke-width="0.5" stroke-dasharray="4 4"/>
            <path d="M50 15 L53 35 L50 40 L47 35 Z" fill="currentColor" opacity="0.3"/>
            <path d="M50 85 L53 65 L50 60 L47 65 Z" fill="currentColor" opacity="0.1"/>
            <path d="M15 50 L35 53 L40 50 L35 47 Z" fill="currentColor" opacity="0.2"/>
            <path d="M85 50 L65 53 L60 50 L65 47 Z" fill="currentColor" opacity="0.2"/>
          </svg>
        </div>
      </div>

      <!-- Card 2: Collage Image (Cột 2, Hàng 1 - Chiếm 2 cột ngang trên Desktop) -->
      <div class="pp-bento-card pp-bento-card--image-collage">
        <figure class="pp-image-container-shared">
          <img src="<?php echo sgh_img('the-royal-all-day-dining/the-royal-all-day-dining-collage-chi-tiet-thiet-bi.jpg'); ?>" alt="<?php echo esc_attr__('Hệ thống lò nướng hấp combi và thiết bị giữ nhiệt inox công nghiệp tại The Royal', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="800" height="600">
          <figcaption class="pp-image-caption-shared"><?php echo esc_html__('Cận cảnh hệ thống thiết bị inox 304 tích hợp chuẩn chỉ từng micromet.', 'saigonhoreca'); ?></figcaption>
        </figure>
      </div>

      <!-- Card 3: Operations Highlight Text (Cột 4, Hàng 1) -->
      <div class="pp-bento-card pp-bento-card--ops">
        <div class="pp-bento-card__inner">
          <span class="pp-bento-card__num">02</span>
          <h3 class="pp-bento-card__title"><?php echo esc_html__('Tư duy một chiều', 'saigonhoreca'); ?></h3>
          <div class="pp-bento-card__divider"></div>
          <p><?php echo wp_kses_post(sprintf(esc_html__('Từng chi tiết nhỏ từ %1$s, %2$s đến %3$s đều được tính toán khoa học dựa trên thói quen của đầu bếp chuyên nghiệp.', 'saigonhoreca'), '<strong>' . esc_html__('chiều cao quầy sơ chế', 'saigonhoreca') . '</strong>', '<strong>' . esc_html__('vị trí máy rửa', 'saigonhoreca') . '</strong>', '<strong>' . esc_html__('khoảng cách bếp nấu', 'saigonhoreca') . '</strong>')); ?></p>
        </div>
        <!-- Blueprint Decor Technical Grid -->
        <div class="pp-bento-blueprint-decor pp-bento-blueprint-decor--grid" aria-hidden="true">
          <svg viewBox="0 0 100 100" fill="none" stroke="currentColor">
            <rect x="5" y="5" width="90" height="90" stroke-width="0.5" stroke-dasharray="2 2" rx="3"/>
            <line x1="5" y1="25" x2="95" y2="25" stroke-width="0.3" stroke-dasharray="1 3"/>
            <line x1="5" y1="50" x2="95" y2="50" stroke-width="0.5" stroke-dasharray="1 1"/>
            <line x1="5" y1="75" x2="95" y2="75" stroke-width="0.3" stroke-dasharray="1 3"/>
            <line x1="25" y1="5" x2="25" y2="95" stroke-width="0.3" stroke-dasharray="1 3"/>
            <line x1="50" y1="5" x2="50" y2="95" stroke-width="0.5" stroke-dasharray="1 1"/>
            <line x1="75" y1="5" x2="75" y2="95" stroke-width="0.3" stroke-dasharray="1 3"/>
            <text x="10" y="18" font-family="monospace" font-size="3" fill="currentColor" opacity="0.3">SYS_REF_A</text>
            <text x="10" y="90" font-family="monospace" font-size="3" fill="currentColor" opacity="0.3">SCALE: 1:20</text>
          </svg>
        </div>
      </div>

      <!-- Card 4: Stats 1 - Dự án (Cột 2, Hàng 2) -->
      <div class="pp-bento-card pp-bento-card--stat">
        <div class="pp-bento-card__inner">
          <span class="pp-bento-stat__val">300+</span>
          <span class="pp-bento-stat__label"><?php echo esc_html__('Dự Án Đã Hoàn Thành', 'saigonhoreca'); ?></span>
        </div>
      </div>

      <!-- Card 5: Stats 2 - Năm kinh nghiệm (Cột 3, Hàng 2) -->
      <div class="pp-bento-card pp-bento-card--stat">
        <div class="pp-bento-card__inner">
          <span class="pp-bento-stat__val">15+</span>
          <span class="pp-bento-stat__label"><?php echo esc_html__('Năm Đồng Hành F&B Việt', 'saigonhoreca'); ?></span>
        </div>
      </div>

      <!-- Card 6: Prep Area Image (Cột 4, Hàng 2 - Chiếm 2 dòng dọc/ngang tùy responsive) -->
      <div class="pp-bento-card pp-bento-card--image-prep">
        <figure class="pp-image-container-shared">
          <img src="<?php echo sgh_img('the-royal-all-day-dining/the-royal-all-day-dining-khu-so-che-chau-rua.jpg'); ?>" alt="<?php echo esc_attr__('Hệ thống quầy sơ chế chậu rửa và lưu trữ lạnh bằng inox 304 chuẩn công nghiệp', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="800" height="600">
          <figcaption class="pp-image-caption-shared"><?php echo esc_html__('Khu vực sơ chế tích hợp chậu rửa công nghiệp hiện đại.', 'saigonhoreca'); ?></figcaption>
        </figure>
      </div>

      <!-- Card 7: Core Value Text (Cột 1, Hàng 3 - Chiếm 2 cột ngang trên Desktop) -->
      <div class="pp-bento-card pp-bento-card--core-val">
        <div class="pp-bento-card__inner">
          <span class="pp-bento-card__num">03</span>
          <h3 class="pp-bento-card__title"><?php echo esc_html__('Linh hồn vận hành chuẩn chỉnh', 'saigonhoreca'); ?></h3>
          <div class="pp-bento-card__divider"></div>
          <p><?php echo wp_kses_post(sprintf(esc_html__('Bếp công nghiệp chuyên nghiệp không đơn thuần là tập hợp thiết bị đắt tiền, đó là sự kết tinh giữa %1$s, %2$s và %3$s.', 'saigonhoreca'), '<strong>' . esc_html__('tư duy vận hành tối ưu một chiều', 'saigonhoreca') . '</strong>', '<strong>' . esc_html__('sự am hiểu sâu sắc thói quen đầu bếp', 'saigonhoreca') . '</strong>', '<strong>' . esc_html__('tiêu chuẩn vệ sinh an toàn nghiêm ngặt', 'saigonhoreca') . '</strong>')); ?></p>
        </div>
        <!-- Blueprint Decor Measuring -->
        <div class="pp-bento-blueprint-decor pp-bento-blueprint-decor--measuring" aria-hidden="true">
          <svg viewBox="0 0 100 100" fill="none" stroke="currentColor">
            <g stroke-width="0.5">
              <line x1="10" y1="80" x2="90" y2="80"/>
              <line x1="10" y1="73" x2="10" y2="87"/>
              <line x1="90" y1="73" x2="90" y2="87"/>
              <polygon points="10,80 16,77 16,83" fill="currentColor"/>
              <polygon points="90,80 84,77 84,83" fill="currentColor"/>
            </g>
            <text x="32" y="72" font-family="monospace" font-size="4.5" fill="currentColor" opacity="0.4">W: 1200mm</text>
            <path d="M20 20 A30 30 0 0 1 80 20" stroke-width="0.5" stroke-dasharray="3 3"/>
            <text x="44" y="14" font-family="monospace" font-size="4.5" fill="currentColor" opacity="0.4">R: 35.5</text>
          </svg>
        </div>
      </div>

      <!-- Card 8: Strategic Partnership Panel (Cột 3, Hàng 3 - Chiếm 2 cột ngang trên Desktop) -->
      <div class="pp-bento-card pp-bento-card--partnership">
        <div class="pp-bento-card__inner">
          <span class="pp-bento-card__num">04</span>
          <h3 class="pp-bento-card__title"><?php echo esc_html__('Đối tác chiến lược tin cậy', 'saigonhoreca'); ?></h3>
          <div class="pp-bento-card__divider"></div>
          <p><?php echo wp_kses_post(sprintf(esc_html__('Với dự án The Royal, Saigon Horeca một lần nữa khẳng định vị thế trong việc %1$s không chỉ đẹp thẩm mỹ, mà còn tối ưu hóa công năng và bền vững theo năm tháng.', 'saigonhoreca'), '<strong>' . esc_html__('kiến tạo những không gian bếp đẳng cấp', 'saigonhoreca') . '</strong>')); ?></p>
        </div>
        <!-- Blueprint Decor Flow Diagram -->
        <div class="pp-bento-blueprint-decor pp-bento-blueprint-decor--flow" aria-hidden="true">
          <svg viewBox="0 0 100 100" fill="none" stroke="currentColor">
            <path d="M10 50 C 30 20, 70 80, 90 50" stroke-width="0.5" stroke-dasharray="3 3"/>
            <circle cx="10" cy="50" r="2" fill="currentColor"/>
            <circle cx="90" cy="50" r="2" fill="currentColor"/>
            <g fill="currentColor" opacity="0.6">
              <polygon points="50,45 55,42 53,49"/>
              <polygon points="30,40 35,37 33,44"/>
              <polygon points="70,55 75,52 73,59"/>
            </g>
            <text x="25" y="65" font-family="monospace" font-size="3" fill="currentColor" opacity="0.3">ONE-WAY SYSTEM</text>
          </svg>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ==========================================
     SECTION 6: ASYMMETRIC CLOSING STATEMENT (CTA)
     ========================================== -->
<section class="pp__section pp-cta-asymmetric-trd">
  <!-- Nền ảnh phủ mờ sang trọng phía sau -->
  <div class="pp-cta-bg-bleed" style="background-image: url('<?php echo sgh_img('the-royal-all-day-dining/the-royal-all-day-dining-vach-kinh-phan-chieu-logo.webp'); ?>');"></div>
  
  <div class="pp-container-shared">
    <div class="pp-cta-asymmetric-grid">
      
      <!-- Panel chứa nội dung chữ Asymmetric (Lệch trái 60%) -->
      <div class="pp-cta-card-floating">
        <span class="pp-cta-badge"><?php echo esc_html__('TRIẾT LÝ SAIGON HORECA', 'saigonhoreca'); ?></span>
        
        <h2 class="pp-cta-title"><?php echo esc_html__('Kiến tạo hệ sinh thái bếp đồng bộ & cảm xúc', 'saigonhoreca'); ?></h2>
        <span class="pp-cta-line" aria-hidden="true"></span>
        
        <div class="pp-cta-body">
          <p><?php echo esc_html__('The Royal là minh chứng sắc nét cho cách Saigon Horeca tiếp cận các dự án F&B đỉnh cao: không bán thiết bị đơn lẻ, chúng tôi kiến tạo giải pháp toàn diện phục vụ đúng linh hồn ẩm thực và thăng hoa trải nghiệm khách hàng.', 'saigonhoreca'); ?></p>
        </div>

        <blockquote class="pp-cta-quote">
          <span class="pp-cta-quote__icon">“</span>
          <p class="pp-cta-quote__text"><?php echo esc_html__('Một căn bếp tốt không chỉ giúp nấu ăn ngon hơn — mà còn giúp hồn cốt của nhà hàng được kể một cách bền vững nhất.', 'saigonhoreca'); ?></p>
          <cite class="pp-cta-quote__author">— Saigon Horeca</cite>
        </blockquote>

        <!-- Taglist thể hiện triết lý -->
        <div class="pp-cta-tags">
          <span class="pp-cta-tag-item"><?php echo esc_html__('Không áp dụng rập khuôn', 'saigonhoreca'); ?></span>
          <span class="pp-cta-tag-item"><?php echo esc_html__('Tối ưu hóa một chiều', 'saigonhoreca'); ?></span>
          <span class="pp-cta-tag-item"><?php echo esc_html__('Đồng hành trọn đời', 'saigonhoreca'); ?></span>
        </div>
      </div>
      
    </div>
  </div>
</section>
