<?php
/**
 * Project Pillar — yuzu-omakase
 * Section #2: intro — stats bar + asymmetric grid.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;

$stats = [
  ['label' => esc_html__('Xếp hạng', 'saigonhoreca'), 'value' => esc_html__('Top 50 Thái Lan', 'saigonhoreca')],
  ['label' => esc_html__('Mô hình', 'saigonhoreca'), 'value' => esc_html__('Fine Dining / Omakase', 'saigonhoreca')],
  ['label' => esc_html__('Vai trò SGH', 'saigonhoreca'), 'value' => esc_html__('Thiết kế & Thiết bị bếp', 'saigonhoreca')],
];
?>
<section class="pp-intro-yzo scroll-reveal">
  <!-- SVG Trang trí dưới nền -->
  <div class="pp-intro-yzo__bg-svg" aria-hidden="true">
    <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
      <circle cx="50" cy="50" r="40" stroke="var(--yzo-gold-border)" stroke-width="0.25" stroke-dasharray="1 3"/>
      <path d="M10 50H90M50 10V90" stroke="var(--yzo-gold-border)" stroke-width="0.1"/>
    </svg>
  </div>

  <div class="pp-container-shared">
    <div class="pp-intro-yzo__grid">
      <!-- Cột chữ (38%) -->
      <div class="pp-intro-yzo__content">
        <span class="pp-intro-yzo__eyebrow"><?php echo esc_html__('Restaurant Profile', 'saigonhoreca'); ?></span>
        <h2 class="pp-intro-yzo__title">
          <span>Yuzu Omakase</span>
          <span class="pp-intro-yzo__divider" aria-hidden="true"></span>
        </h2>
        
        <div class="pp-intro-yzo__description">
          <p class="pp-intro-yzo__lead">
            <span class="pp-intro-yzo__dropcap">Y</span><?php echo esc_html__('uzu Omakase Vietnam tại 34 Thủ Khoa Huân là điểm đến ẩm thực đẳng cấp mang tinh thần Nhật Bản đương đại, thuộc hệ thống YUZU GROUP nổi danh châu Á.', 'saigonhoreca'); ?>
          </p>
          <p><?php echo esc_html__('Để hiện thực hóa trải nghiệm fine-dining hoàn mỹ, Saigon Horeca hân hạnh đồng hành trong vai trò tư vấn giải pháp, tổ chức không gian bếp mở và thiết lập luồng vận hành tối tân nhất cho các đầu bếp chế tác.', 'saigonhoreca'); ?></p>
        </div>

        <blockquote class="pp-intro-yzo__quote">
          <p><?php echo esc_html__('“Nơi tinh hoa ẩm thực Omakase đòi hỏi một hệ bếp vận hành chính xác, sạch sẽ và bền bỉ trong từng giây phút phục vụ.”', 'saigonhoreca'); ?></p>
        </blockquote>

        <!-- Stats Bar ngay bên dưới cột chữ -->
        <div class="pp-intro-yzo__stats" aria-label="<?php echo esc_attr__('Thông tin nổi bật', 'saigonhoreca'); ?>">
          <?php foreach ($stats as $item) : ?>
            <div class="pp-intro-yzo__stat">
              <span class="pp-intro-yzo__stat-label"><?php echo esc_html($item['label']); ?></span>
              <strong class="pp-intro-yzo__stat-value"><?php echo esc_html($item['value']); ?></strong>
            </div>
          <?php endforeach; ?>
        </div>

        <!-- SVG Lát cá hồi sushi (Salmon Nigiri) tinh tế và nghệ thuật -->
        <div class="pp-intro-yzo__cad-diagram scroll-reveal" aria-hidden="true">
          <svg viewBox="0 0 320 180" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Định nghĩa Gradients cho cá và cơm -->
            <defs>
              <linearGradient id="salmon-grad" x1="70" y1="60" x2="250" y2="120" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#ff3b0f"/>
                <stop offset="50%" stop-color="#ff5e36"/>
                <stop offset="100%" stop-color="#ff8c69"/>
              </linearGradient>
              <linearGradient id="rice-grad" x1="100" y1="80" x2="220" y2="130" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#ffffff"/>
                <stop offset="100%" stop-color="#e8e8db"/>
              </linearGradient>
              <radialGradient id="shadow-grad" cx="160" cy="132" r="100" fx="160" fy="132" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="rgba(0,0,0,0.5)"/>
                <stop offset="100%" stop-color="rgba(0,0,0,0)"/>
              </radialGradient>
              <linearGradient id="gloss-grad" x1="120" y1="65" x2="200" y2="85" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="rgba(255,255,255,0.45)"/>
                <stop offset="100%" stop-color="rgba(255,255,255,0)"/>
              </linearGradient>
            </defs>

            <!-- Bóng đổ bên dưới -->
            <ellipse cx="160" cy="134" rx="85" ry="11" fill="url(#shadow-grad)"/>

            <!-- Cơm sushi (Shari) -->
            <g>
              <path d="M 100,112 Q 160,86 220,112 Q 215,131 160,133 Q 105,131 100,112 Z" fill="url(#rice-grad)" stroke="#d2d2be" stroke-width="0.5"/>
              <ellipse cx="106" cy="114" rx="5" ry="3" fill="#ffffff" transform="rotate(-15 106 114)"/>
              <ellipse cx="121" cy="123" rx="6" ry="3.5" fill="#fefef5" transform="rotate(10 121 123)"/>
              <ellipse cx="141" cy="129" rx="5.5" ry="3" fill="#ffffff" transform="rotate(-5 141 129)"/>
              <ellipse cx="166" cy="128" rx="6" ry="3.5" fill="#fefef5" transform="rotate(15 166 128)"/>
              <ellipse cx="191" cy="123" rx="5" ry="3" fill="#ffffff" transform="rotate(-20 191 123)"/>
              <ellipse cx="211" cy="116" rx="5.5" ry="3" fill="#fefef5" transform="rotate(25 211 116)"/>
              <ellipse cx="161" cy="111" rx="6" ry="3.2" fill="#ffffff" transform="rotate(-30 161 111)"/>
            </g>

            <!-- Lát cá hồi (Neta) -->
            <g>
              <path d="M 74,110 C 74,110 94,80 149,70 C 204,60 244,82 249,94 C 254,102 239,114 214,118 C 184,122 149,112 114,116 C 94,118 79,116 74,110 Z" fill="url(#salmon-grad)" stroke="#e6350d" stroke-width="0.5"/>
              <!-- Vân mỡ cá hồi màu trắng -->
              <path d="M 89,104 Q 109,85 139,80" stroke="#ffffff" stroke-width="2" stroke-linecap="round" fill="none" opacity="0.85"/>
              <path d="M 114,110 Q 134,90 164,84" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round" fill="none" opacity="0.85"/>
              <path d="M 139,113 Q 159,95 189,89" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round" fill="none" opacity="0.85"/>
              <path d="M 164,115 Q 184,98 214,94" stroke="#ffffff" stroke-width="2" stroke-linecap="round" fill="none" opacity="0.85"/>
              <path d="M 189,116 Q 209,102 234,100" stroke="#ffffff" stroke-width="1.8" stroke-linecap="round" fill="none" opacity="0.8"/>
              <!-- Độ bóng ẩm ướt của lát cá tươi -->
              <path d="M 94,87 Q 144,70 204,74 C 179,74 129,77 94,87 Z" fill="url(#gloss-grad)"/>
            </g>

            <!-- Nhũ vàng & Hành lá trang trí -->
            <g>
              <circle cx="151" cy="74" r="2.5" fill="#ffdf00" opacity="0.95"/>
              <polygon points="145,76 147,78 144,79 143,77" fill="#ffd700" opacity="0.85"/>
              <polygon points="157,72 160,73 159,75 156,74" fill="#ffd700" opacity="0.85"/>
              <!-- Sợi hành lá -->
              <path d="M 137,80 Q 147,70 157,76" stroke="#2ecc71" stroke-width="1.5" stroke-linecap="round" fill="none"/>
              <path d="M 141,77 Q 151,72 161,79" stroke="#27ae60" stroke-width="1.2" stroke-linecap="round" fill="none"/>
            </g>
          </svg>
        </div>
      </div>

      <!-- Cột ảnh bất đối xứng gồm 4 hình ảnh chuẩn Metro Grid (Không lặp lại ảnh) -->
      <div class="pp-intro-yzo__visual">
        <div class="pp-intro-yzo__asymmetric-grid-four">
          
          <!-- Ảnh 1: Thuyền cầu gai Uni (Wide) -->
          <figure class="pp-image-container-shared pp-intro-yzo__fig--wide-top scroll-reveal">
            <img src="<?php echo esc_url(sgh_img('yuzu-omakase/yuzu-omakase-thuyen-cau-gai-uni-thuong-hang.webp')); ?>" alt="<?php echo esc_attr__('Thuyền gỗ bày biện cồi cầu gai Nhật Bản Uni tươi rói', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
            <div class="pp-image-caption-shared"><?php echo esc_html__('Thuyền gỗ bày biện cồi cầu gai Nhật Bản (Uni) tươi rói bên cạnh cặp tượng vàng chiêu tài, nâng tầm trải nghiệm ẩm thực thượng hạng.', 'saigonhoreca'); ?></div>
          </figure>

          <!-- Ảnh 2: Sashimi tôm hùm tre (Normal) -->
          <figure class="pp-image-container-shared pp-intro-yzo__fig--normal-left scroll-reveal">
            <img src="<?php echo esc_url(sgh_img('yuzu-omakase/yuzu-omakase-sashimi-tom-hum-tre-nguyen-ban.webp')); ?>" alt="<?php echo esc_attr__('Tuyệt tác Sashimi tôm hùm tre nguyên bản tươi rói', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
            <div class="pp-image-caption-shared"><?php echo esc_html__('Tuyệt tác Sashimi tôm hùm tre nguyên con tươi rói, kết hợp hoa tươi trang trí tinh tế làm nổi bật chiều sâu ẩm thực đương đại.', 'saigonhoreca'); ?></div>
          </figure>

          <!-- Ảnh 3: Nigiri sushi tôm ngọt Amaebi bình phong (Normal) -->
          <figure class="pp-image-container-shared pp-intro-yzo__fig--normal-right scroll-reveal">
            <img src="<?php echo esc_url(sgh_img('yuzu-omakase/yuzu-omakase-nigiri-sushi-tom-ngot-phong-man.webp')); ?>" alt="<?php echo esc_attr__('Nigiri sushi tôm ngọt Amaebi phủ nhũ vàng và Caviar đặt trước bình phong vẽ phong cảnh Nhật Bản cổ kính', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
            <div class="pp-image-caption-shared"><?php echo esc_html__('Món Nigiri tôm ngọt phủ nhũ vàng và Caviar đặt trước bình phong vẽ phong cảnh Nhật Bản cổ kính, một sự tôn vinh nghệ thuật ẩm thực hoàng gia.', 'saigonhoreca'); ?></div>
          </figure>

          <!-- Ảnh 4: Không gian quầy trà và bonsai mạ vàng (Wide) -->
          <figure class="pp-image-container-shared pp-intro-yzo__fig--wide-bot scroll-reveal">
            <img src="<?php echo esc_url(sgh_img('yuzu-omakase/yuzu-omakase-quay-tra-chieu-cay-canh-bonsai-ma-vang.webp')); ?>" alt="<?php echo esc_attr__('Không gian thưởng trà thiền tịnh tĩnh lặng với điểm nhấn là cây bonsai mạ vàng', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
            <div class="pp-image-caption-shared"><?php echo esc_html__('Không gian thưởng trà thiền tịnh tĩnh lặng với điểm nhấn là cây bonsai mạ vàng lấp lánh bên cạnh bộ ấm trà thủy tinh thanh khiết.', 'saigonhoreca'); ?></div>
          </figure>

        </div>
      </div>
    </div>
  </div>
</section>
