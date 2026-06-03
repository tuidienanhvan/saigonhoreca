<?php
/**
 * Project Pillar — heiwa-sushi-omakase
 * Section #2: intro — Option 5: The Gallery Poster style with Fine-line Grids.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-hwa pp-intro-hwa pp-intro-hwa--poster scroll-reveal">
  <!-- Đường chỉ AutoCAD chạy ngầm toàn nền -->
  <div class="pp-hwa-poster-grid" aria-hidden="true">
    <svg viewBox="0 0 100 100" preserveAspectRatio="none" fill="none" stroke="currentColor" stroke-width="0.05" style="position: absolute; inset: 0; width: 100%; height: 100%; pointer-events: none; opacity: 0.08;">
      <!-- Lưới chỉ dọc & ngang -->
      <line x1="20" y1="0" x2="20" y2="100" />
      <line x1="80" y1="0" x2="80" y2="100" />
      <line x1="0" y1="35" x2="100" y2="35" />
      <line x1="0" y1="75" x2="100" y2="75" />
      <!-- Đường nối chéo thiết kế nghệ thuật từ Cột Chữ (dưới trái) sang Cột Ảnh (trên phải) -->
      <path d="M 20,75 L 80,35" stroke="var(--p)" stroke-width="0.1" stroke-dasharray="0.5 0.5" />
      <!-- Điểm định vị tọa độ -->
      <circle cx="20" cy="75" r="0.5" fill="var(--p)" />
      <circle cx="80" cy="35" r="0.5" fill="var(--p)" />
    </svg>
  </div>

  <div class="pp-hwa-ambient-glow pp-hwa-ambient-glow--tr" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <div class="pp-intro-hwa__two-cols">
      <!-- Cột Trái: Grid 4 ảnh món ăn phong cách tạp chí Wabi-Sabi -->
      <div class="pp-intro-hwa__media-col scroll-reveal">
        <div class="pp-intro-hwa__grid-four">
          <div class="pp-image-container-shared pp-frame-hwa pp-frame-hwa--poster-art pp-intro-hwa__grid-item">
            <img src="<?php echo sgh_img('heiwa-sushi-omakase/heiwa-sushi-omakase-dia-sushi-bo-va-luon-nhat.webp'); ?>" alt="<?php echo esc_attr__('Đĩa Sushi bò Wagyu và lươn Nhật nướng sốt Kabayaki tại Heiwa - Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
            <div class="pp-image-caption-shared"><?php echo esc_html__('Đĩa Sushi Wagyu & lươn Nhật.', 'saigonhoreca'); ?></div>
          </div>
          <div class="pp-image-container-shared pp-frame-hwa pp-frame-hwa--poster-art pp-intro-hwa__grid-item">
            <img src="<?php echo sgh_img('heiwa-sushi-omakase/heiwa-sushi-omakase-bat-com-tron-ca-hoi-va-trung-ca.webp'); ?>" alt="<?php echo esc_attr__('Bát cơm trộn cá hồi và trứng cá tại Heiwa - Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
            <div class="pp-image-caption-shared"><?php echo esc_html__('Bát cơm trộn cá hồi & trứng cá.', 'saigonhoreca'); ?></div>
          </div>
          <div class="pp-image-container-shared pp-frame-hwa pp-frame-hwa--poster-art pp-intro-hwa__grid-item">
            <img src="<?php echo sgh_img('heiwa-sushi-omakase/heiwa-sushi-omakase-so-diep-nhat-va-trung-ca-hoi.webp'); ?>" alt="<?php echo esc_attr__('Sò điệp Nhật và trứng cá hồi tại Heiwa - Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
            <div class="pp-image-caption-shared"><?php echo esc_html__('Sò điệp Nhật & trứng cá hồi.', 'saigonhoreca'); ?></div>
          </div>
          <div class="pp-image-container-shared pp-frame-hwa pp-frame-hwa--poster-art pp-intro-hwa__grid-item">
            <img src="<?php echo sgh_img('heiwa-sushi-omakase/heiwa-sushi-omakase-ruou-sake-dassai-va-hau-hyogo.webp'); ?>" alt="<?php echo esc_attr__('Rượu Sake Dassai và hàu Hyogo tại Heiwa - Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
            <div class="pp-image-caption-shared"><?php echo esc_html__('Sake Dassai & hàu Hyogo.', 'saigonhoreca'); ?></div>
          </div>
        </div>
      </div>

      <!-- Cột Phải: Khối chữ phẳng tinh tế, khoảng thở Zen thoáng đãng -->
      <div class="pp-intro-hwa__content-col scroll-reveal">
        <div class="pp-intro-hwa__poster-text">
          <div class="pp-text-hwa__badge"><span class="pp-text-hwa__badge-accent">//</span> <?php echo esc_html__('BỐI CẢNH DỰ ÁN', 'saigonhoreca'); ?></div>
          <h2 class="pp-text-hwa__title"><?php echo esc_html__('Không Gian Nghệ Thuật Omakase Đích Thực', 'saigonhoreca'); ?></h2>
          
          <div class="pp-text-hwa__divider" aria-hidden="true"></div>
          
          <blockquote class="pp-text-hwa__quote"><?php echo esc_html__('“Nơi sự tĩnh lặng của kiến trúc Zen Nhật Bản gặp gỡ sự cầu kỳ của nghệ thuật ẩm thực Omakase đỉnh cao.”', 'saigonhoreca'); ?></blockquote>
          
          <div class="pp-text-hwa__body">
            <p><?php echo esc_html__('Heiwa Sushi Omakase (Số 2 Phan Văn Đáng, Quận 2, HCMC) là điểm đến đỉnh cao cho những tín đồ sành ăn yêu thích ẩm thực Nhật Bản đích thực tại Sài Gòn. Không gian nội thất của Heiwa được thiết kế tỉ mỉ, kiến tạo sự cân bằng hoàn mỹ giữa văn hóa truyền thống Wabi-Sabi và nhịp thở hiện đại đương đại.', 'saigonhoreca'); ?></p>
            <p><?php echo esc_html__('Được chủ đầu tư tin cậy lựa chọn là đơn vị thiết kế và thi công trọn gói hệ thống bếp biểu diễn cao cấp, Saigon Horeca đã quy hoạch một gian bếp mở thông minh. Nơi đây không chỉ tối ưu hóa 100% công năng vận hành khắt khe của Omakase mà còn tạo ra cầu nối tương tác trực tiếp, đánh thức trọn vẹn thị giác và cảm xúc của thực khách khi chiêm ngưỡng đầu bếp chế biến các món sashimi, sushi tinh xảo.', 'saigonhoreca'); ?></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
