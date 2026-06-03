<?php
/**
 * Project Pillar — little-bear-thao-dien
 * Section #6: split gallery
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp__section--alt pp-gallery-lb-sec scroll-reveal">
  <!-- Họa tiết blueprint chạy ngầm dưới nền -->
  <div class="pp-gallery-lb__blueprint-bg" aria-hidden="true">
    <svg viewBox="0 0 800 600" fill="none" stroke="rgba(212, 175, 55, 0.05)" stroke-width="1">
      <circle cx="400" cy="300" r="250" stroke-dasharray="5 5" />
      <circle cx="400" cy="300" r="150" />
      <line x1="100" y1="300" x2="700" y2="300" />
      <line x1="400" y1="50" x2="400" y2="550" />
      <path d="M 200,150 L 600,150 L 600,450 L 200,450 Z" />
      <path d="M 250,200 L 550,200 L 550,400 L 250,400 Z" stroke-dasharray="10 5" />
      
      <!-- Chỉ số tọa độ và ruler -->
      <text x="405" y="60" font-family="monospace" font-size="12" fill="rgba(212, 175, 55, 0.15)">Y: 550</text>
      <text x="705" y="295" font-family="monospace" font-size="12" fill="rgba(212, 175, 55, 0.15)">X: 700</text>
      <text x="405" y="315" font-family="monospace" font-size="12" fill="rgba(212, 175, 55, 0.15)">(0,0)</text>
      <!-- Thước đo vạch chia -->
      <path d="M 100 295 L 100 305 M 200 295 L 200 305 M 300 295 L 300 305" />
      <path d="M 395 100 L 405 100 M 395 200 L 405 200 M 395 400 L 405 400 M 395 500 L 405 500" />
    </svg>
  </div>

  <div class="pp-container-shared">
    <div class="pp-gallery-lb__grid">
      
      <!-- Cột text: DNA của Saigon Horeca -->
      <div class="pp-gallery-lb__text-col">
        <span class="pp-text-lb__divider" aria-hidden="true"></span>
        <h2 class="pp-text-lb__title"><?php echo esc_html__('DNA nghệ thuật trong thiết kế quầy bar', 'saigonhoreca'); ?></h2>
        
        <div class="pp-gallery-lb__body">
          <p><?php echo esc_html__('Quầy bar mở nằm đối diện khu vực nấu nướng, cho phép thực khách chứng kiến nghệ thuật ẩm thực của các đầu bếp. Mang đến trải nghiệm ăn uống độc đáo và thú vị.', 'saigonhoreca'); ?></p>
          
          <ul class="pp-gallery-lb__list">
            <li>
              <strong><?php echo esc_html__('Lưu trữ tối ưu', 'saigonhoreca'); ?></strong>
              <p><?php echo esc_html__('Tủ lạnh và tủ đông lớn ở cánh trái bếp đảm bảo độ tươi ngon của nguyên liệu. Sử dụng các thương hiệu nổi tiếng như Fujimak và Hoshizaki để đảm bảo độ tin cậy và hiệu suất.', 'saigonhoreca'); ?></p>
            </li>
            <li>
              <strong><?php echo esc_html__('Gia công inox riêng theo mặt bằng', 'saigonhoreca'); ?></strong>
              <p><?php echo esc_html__('Mặt bàn làm việc và kệ treo tường bằng thép không gỉ được thiết kế riêng cho Little Bear để tối đa hóa không gian và chức năng.', 'saigonhoreca'); ?></p>
            </li>
          </ul>
        </div>
      </div>

      <!-- Cột media: Lưới 2 ảnh so le bất đối xứng không chồng đè -->
      <div class="pp-gallery-lb__media-col">
        <div class="pp-gallery-lb__media-grid">
          
          <!-- Ảnh 1: Quầy bar thực tế -->
          <div class="pp-image-container-shared pp-gallery-lb__img-1">
            <img src="<?php echo sgh_img('little-bear-thao-dien/little-bear-thao-dien-quay-bar-phuc-vu.jpg'); ?>" alt="<?php echo esc_attr__('Khu vực hậu cần quầy bar inox và hệ tủ trữ lạnh rượu vang tại Little Bear', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="600" height="800">
            <div class="pp-image-caption-shared"><?php echo esc_html__('Không gian hậu cần quầy bar inox 304 kết hợp tủ trữ rượu vang mặt kính tối tân cùng hệ thống bàn mát undercounter, giúp tối ưu hóa dung tích trữ đông nguyên liệu và bảo quan nhiệt độ chuẩn xác.', 'saigonhoreca'); ?></div>
          </div>

          <!-- Ảnh 2: Không gian ăn uống nhìn vào bếp -->
          <div class="pp-image-container-shared pp-gallery-lb__img-2">
            <img src="<?php echo sgh_img('little-bear-thao-dien/little-bear-thao-dien-khong-gian-bep-bar.jpg'); ?>" alt="<?php echo esc_attr__('Không gian quầy bar mở và line bếp hiện đại tại Little Bear Thảo Điền', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="600" height="800">
            <div class="pp-image-caption-shared"><?php echo esc_html__('Không gian quầy bar mở đối diện line bếp nóng, nơi thực khách có thể trực tiếp tương tác và chiêm ngưỡng nghệ thuật ẩm thực tinh tế.', 'saigonhoreca'); ?></div>
          </div>

        </div>
      </div>

    </div>

    <!-- Khối đúc kết/Lời kết khép lại câu chuyện (Section 6 Footer) -->
    <div class="pp-gallery-lb__footer">
      <div class="pp-gallery-lb__footer-card">
        <!-- SVG Họa tiết AutoCAD bốn góc tạo nét kỹ sư sang xịn mịn -->
        <div class="pp-gallery-lb__footer-corner pp-gallery-lb__footer-corner--top-left">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--gold-dim)" stroke-width="1.5">
            <line x1="0" y1="0" x2="24" y2="0" />
            <line x1="0" y1="0" x2="0" y2="24" />
            <circle cx="0" cy="0" r="4" />
          </svg>
        </div>
        <div class="pp-gallery-lb__footer-corner pp-gallery-lb__footer-corner--top-right">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--gold-dim)" stroke-width="1.5">
            <line x1="0" y1="0" x2="24" y2="0" />
            <line x1="24" y1="0" x2="24" y2="24" />
            <circle cx="24" cy="0" r="4" />
          </svg>
        </div>
        <div class="pp-gallery-lb__footer-corner pp-gallery-lb__footer-corner--bottom-left">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--gold-dim)" stroke-width="1.5">
            <line x1="0" y1="24" x2="24" y2="24" />
            <line x1="0" y1="0" x2="0" y2="24" />
            <circle cx="0" cy="24" r="4" />
          </svg>
        </div>
        <div class="pp-gallery-lb__footer-corner pp-gallery-lb__footer-corner--bottom-right">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--gold-dim)" stroke-width="1.5">
            <line x1="0" y1="24" x2="24" y2="24" />
            <line x1="24" y1="0" x2="24" y2="24" />
            <circle cx="24" cy="24" r="4" />
          </svg>
        </div>

        <!-- Bear SVG Icon decoration -->
        <div class="pp-gallery-lb__footer-bear-decor" style="width: 80px; height: 80px; margin: 0 auto 1.5rem; filter: drop-shadow(0 4px 12px rgba(245,166,35,0.2));">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" width="100%" height="100%">
            <defs>
              <linearGradient id="bearBody" x1="0" y1="0" x2="1" y2="1">
                <stop offset="0%" stop-color="#f5a623" />
                <stop offset="100%" stop-color="#d4731c" />
              </linearGradient>
              <radialGradient id="cheekGlow" cx="50%" cy="50%" r="50%">
                <stop offset="0%" stop-color="#f1785b" stop-opacity="0.6" />
                <stop offset="100%" stop-color="#f1785b" stop-opacity="0" />
              </radialGradient>
              <linearGradient id="innerEar" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="#fcd8a5" />
                <stop offset="100%" stop-color="#f19b6c" />
              </linearGradient>
            </defs>
            <circle cx="54" cy="54" r="24" fill="url(#bearBody)" stroke="#4a2206" stroke-width="3" />
            <circle cx="146" cy="54" r="24" fill="url(#bearBody)" stroke="#4a2206" stroke-width="3" />
            <circle cx="56" cy="56" r="14" fill="url(#innerEar)" />
            <circle cx="144" cy="56" r="14" fill="url(#innerEar)" />
            <path d="M 60,130 C 45,130 35,160 55,176 C 70,188 130,188 145,176 C 165,160 155,130 140,130 Z" fill="url(#bearBody)" stroke="#4a2206" stroke-width="3.5" />
            <path d="M 50,132 C 32,138 28,158 46,160 C 58,162 64,142 62,134" fill="url(#bearBody)" stroke="#4a2206" stroke-width="3" />
            <path d="M 150,132 C 168,138 172,158 154,160 C 142,162 136,142 138,134" fill="url(#bearBody)" stroke="#4a2206" stroke-width="3" />
            <circle cx="100" cy="96" r="54" fill="url(#bearBody)" stroke="#4a2206" stroke-width="3.5" />
            <circle cx="68" cy="106" r="12" fill="url(#cheekGlow)" />
            <circle cx="132" cy="106" r="12" fill="url(#cheekGlow)" />
            <ellipse cx="100" cy="110" rx="22" ry="15" fill="#fcd8a5" stroke="#4a2206" stroke-width="2.5" />
            <path d="M 94,103 C 94,99 106,99 106,103 C 106,108 100,113 100,113 C 100,113 94,108 94,103 Z" fill="#2d1200" />
            <path d="M 94,111 C 97,114 100,114 100,114 C 100,114 103,114 106,111" fill="none" stroke="#4a2206" stroke-width="2" stroke-linecap="round" />
            <path d="M 100,113 L 100,118" stroke="#4a2206" stroke-width="2" />
            <path d="M 92,118 C 96,122 100,121 100,121 C 100,121 104,121 108,118" fill="none" stroke="#4a2206" stroke-width="2" stroke-linecap="round" />
            <circle cx="76" cy="88" r="8" fill="#2d1200" />
            <circle cx="74" cy="85" r="2.5" fill="#ffffff" />
            <circle cx="78" cy="90" r="1" fill="#ffffff" />
            <circle cx="124" cy="88" r="8" fill="#2d1200" />
            <circle cx="122" cy="85" r="2.5" fill="#ffffff" />
            <circle cx="126" cy="90" r="1" fill="#ffffff" />
            <path d="M 68,76 Q 76,73 82,78" fill="none" stroke="#4a2206" stroke-width="2" stroke-linecap="round" />
            <path d="M 132,76 Q 124,73 118,78" fill="none" stroke="#4a2206" stroke-width="2" stroke-linecap="round" />
          </svg>
        </div>

        <p><?php echo esc_html__('Sự chú ý đến từng chi tiết và cam kết về sự xuất sắc của Saigon Horeca đã mang đến một căn bếp không chỉ đáp ứng yêu cầu của Little Bear mà còn vượt quá mong đợi của họ.', 'saigonhoreca'); ?></p>
        <div class="pp-gallery-lb__footer-link">
          <a href="<?php echo esc_url(home_url('/tin-tuc/')); ?>" class="pp-lb-btn-link">
            <span><?php echo esc_html__('Khám phá ngay: Thiết kế bếp công nghiệp SGH góp phần đưa 2 nhà hàng lên top danh sách Travel & Leisure 2024', 'saigonhoreca'); ?></span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
              <line x1="5" y1="12" x2="19" y2="12"/>
              <polyline points="12 5 19 12 12 5"/>
            </svg>
          </a>
        </div>
      </div>
    </div>

  </div>
</section>
