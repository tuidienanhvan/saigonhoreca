<?php
/**
 * Project Pillar — yuzu-omakase
 * Section #6: gallery — project conclusion card.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
?>
<section class="pp-gallery-yzo-sec scroll-reveal">
  <div class="pp-container-shared">
    
    <!-- Khối đúc kết/lời kết (Conclusion block) chốt lại dự án -->
    <div class="pp-gallery-yzo-sec__conclusion scroll-reveal">
      <div class="pp-gallery-yzo-sec__conclusion-card">
        
        <!-- SVG Sushi Bạch Tuộc (Tako Nigiri) nghệ thuật - Góc dưới bên trái -->
        <div class="pp-gallery-yzo-sec__decor-pattern pp-gallery-yzo-sec__decor-pattern--left" aria-hidden="true" title="Tako Nigiri Sushi">
          <svg viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Shari (Cơm sushi bên dưới) -->
            <ellipse cx="60" cy="80" rx="35" ry="18" fill="url(#yzo-tako-shari-grad)" opacity="0.9"/>
            <ellipse cx="50" cy="76" rx="6" ry="3" fill="#ffffff" opacity="0.3"/>
            <ellipse cx="70" cy="82" rx="5" ry="2.5" fill="#ffffff" opacity="0.25"/>
            
            <!-- Neta - Lát bạch tuộc tươi (Tako) uốn lượn uốn cong quyến rũ -->
            <path d="M22 65 C32 50, 48 40, 64 45 C78 50, 88 62, 98 56 C102 54, 105 48, 106 44 C104 52, 98 68, 86 72 C72 76, 58 66, 44 64 C32 62, 24 66, 22 65 Z" fill="url(#yzo-tako-skin-grad)"/>
            
            <!-- Giác hút bạch tuộc đặc trưng (Suckers) chuyển sắc hồng cam -->
            <circle cx="34" cy="54" r="5" fill="url(#yzo-tako-sucker)" stroke="#ffffff" stroke-width="0.75"/>
            <circle cx="48" cy="48" r="5.5" fill="url(#yzo-tako-sucker)" stroke="#ffffff" stroke-width="0.75"/>
            <circle cx="64" cy="49" r="6" fill="url(#yzo-tako-sucker)" stroke="#ffffff" stroke-width="0.75"/>
            <circle cx="80" cy="55" r="5" fill="url(#yzo-tako-sucker)" stroke="#ffffff" stroke-width="0.75"/>
            
            <!-- Lá rong biển bọc ngang (Nori belt) đen bóng kim loại sang trọng -->
            <path d="M50 63 C52 75, 54 88, 56 97 C62 97, 68 96, 70 95 C68 86, 66 73, 64 64 Z" fill="url(#yzo-tako-nori-grad)" stroke="color-mix(in srgb, var(--p) 30%, transparent)" stroke-width="0.5"/>
            
            <!-- Điểm khói vàng kim sáng rực rỡ xung quanh -->
            <circle cx="30" cy="35" r="2" fill="var(--p)" opacity="0.4"/>
            <circle cx="95" cy="85" r="1.5" fill="var(--p)" opacity="0.3"/>
          </svg>
        </div>

        <!-- SVG Sushi Cá Trích Ép Trứng (Nishin Nigiri) nghệ thuật - Góc trên bên phải -->
        <div class="pp-gallery-yzo-sec__decor-pattern pp-gallery-yzo-sec__decor-pattern--right" aria-hidden="true" title="Kazan/Nishin Nigiri Sushi">
          <svg viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Shari (Cơm sushi bên dưới) -->
            <ellipse cx="60" cy="76" rx="36" ry="17" fill="url(#yzo-tako-shari-grad)" opacity="0.9"/>
            
            <!-- Lớp trứng cá màu vàng óng ánh (Kazan/Tobiko base) giòn sần sật -->
            <path d="M22 64 C22 64, 40 76, 64 74 C88 72, 98 62, 98 62 C98 62, 86 86, 60 86 C34 86, 22 64, 22 64 Z" fill="url(#yzo-nishin-egg-grad)"/>
            
            <!-- Các hạt trứng li ti vàng óng (Stippling texture) tỏa sáng -->
            <circle cx="45" cy="78" r="1.2" fill="#fff" opacity="0.8"/>
            <circle cx="52" cy="81" r="1" fill="#ffe066" opacity="0.9"/>
            <circle cx="62" cy="79" r="1.5" fill="#fff" opacity="0.75"/>
            <circle cx="70" cy="81" r="1.2" fill="#ffe066" opacity="0.9"/>
            <circle cx="78" cy="76" r="1" fill="#fff" opacity="0.8"/>
            
            <!-- Lát cá trích phi lê bạc bóng bẩy (Nishin fillet) ôm gọn cơm -->
            <path d="M20 58 C32 44, 52 38, 70 42 C86 46, 96 56, 100 52 C94 44, 78 34, 60 34 C42 34, 26 44, 20 58 Z" fill="url(#yzo-nishin-fish-grad)"/>
            
            <!-- Đường cắt trang trí đẹp mắt dọc lưng cá trích ẩm mượt -->
            <path d="M42 38 L48 46" stroke="color-mix(in srgb, var(--p) 60%, #fff)" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M56 39 L62 48" stroke="color-mix(in srgb, var(--p) 60%, #fff)" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M70 42 L76 50" stroke="color-mix(in srgb, var(--p) 60%, #fff)" stroke-width="1.5" stroke-linecap="round"/>
            
            <!-- Sợi hành lá xanh uốn lượn & nhũ vàng hoàng gia bên trên -->
            <path d="M50 32 C54 26, 62 26, 66 31 C68 33, 72 32, 74 29" stroke="#22c55e" stroke-width="1.2" stroke-linecap="round" fill="none"/>
            <circle cx="58" cy="27" r="2" fill="var(--p)"/>
          </svg>
        </div>

        <!-- Định nghĩa hệ màu Gradients cực đỉnh dành riêng cho tác phẩm Sushi -->
        <svg width="0" height="0" style="position: absolute;">
          <defs>
            <!-- Shari (Cơm) Gradient -->
            <radialGradient id="yzo-tako-shari-grad" cx="50%" cy="50%" r="50%">
              <stop offset="0%" stop-color="#ffffff"/>
              <stop offset="70%" stop-color="#eaeaea"/>
              <stop offset="100%" stop-color="#b5b5b5" stop-opacity="0.8"/>
            </radialGradient>
            <!-- Da bạch tuộc Tako đỏ sẫm sang trọng -->
            <linearGradient id="yzo-tako-skin-grad" x1="0%" y1="0%" x2="100%" y2="100%">
              <stop offset="0%" stop-color="#991b1b"/>
              <stop offset="45%" stop-color="#7f1d1d"/>
              <stop offset="100%" stop-color="#450a0a"/>
            </linearGradient>
            <!-- Giác hút màu hồng phấn -->
            <radialGradient id="yzo-tako-sucker" cx="35%" cy="35%" r="65%">
              <stop offset="0%" stop-color="#fecdd3"/>
              <stop offset="60%" stop-color="#fda4af"/>
              <stop offset="100%" stop-color="#e11d48"/>
            </radialGradient>
            <!-- Nori lá rong biển đen ánh kim -->
            <linearGradient id="yzo-tako-nori-grad" x1="0%" y1="0%" x2="100%" y2="0%">
              <stop offset="0%" stop-color="#1e293b"/>
              <stop offset="50%" stop-color="#0f172a"/>
              <stop offset="100%" stop-color="#020617"/>
            </linearGradient>
            <!-- Màu Trứng cá trích vàng óng giòn tan -->
            <linearGradient id="yzo-nishin-egg-grad" x1="0%" y1="0%" x2="0%" y2="100%">
              <stop offset="0%" stop-color="#facc15"/>
              <stop offset="50%" stop-color="#eab308"/>
              <stop offset="100%" stop-color="#ca8a04"/>
            </linearGradient>
            <!-- Màu lưng cá trích ánh bạc bóng mượt -->
            <linearGradient id="yzo-nishin-fish-grad" x1="0%" y1="0%" x2="100%" y2="100%">
              <stop offset="0%" stop-color="#94a3b8"/>
              <stop offset="35%" stop-color="#475569"/>
              <stop offset="70%" stop-color="#334155"/>
              <stop offset="100%" stop-color="#1e293b"/>
            </linearGradient>
          </defs>
        </svg>

        <!-- Bố cục phần chữ được căn giữa hoàn hảo -->
        <span class="pp-gallery-yzo-sec__conclusion-kicker"><?php echo esc_html__('Kết luận dự án', 'saigonhoreca'); ?></span>
        
        <h3 class="pp-gallery-yzo-sec__conclusion-title"><?php echo esc_html__('Sự Khác Biệt Đến Từ Sự Hoàn Mỹ', 'saigonhoreca'); ?></h3>
        
        <!-- Divider thanh lịch cân đối nằm ngay trong card -->
        <div class="pp-gallery-yzo-sec__conclusion-divider" aria-hidden="true">
          <span class="pp-gallery-yzo-sec__divider-dot"></span>
          <span class="pp-gallery-yzo-sec__divider-line"></span>
          <span class="pp-gallery-yzo-sec__divider-dot"></span>
        </div>

        <p class="pp-gallery-yzo-sec__conclusion-text">
          <?php echo esc_html__('Không chỉ đơn thuần là việc lắp đặt các máy móc thiết bị, Saigon Horeca đã truyền tải trọn vẹn tinh thần tỉ mỉ, trân quý nguyên liệu của văn hóa ẩm thực Omakase vào cấu trúc hệ bếp. Sự kết hợp giữa kỹ nghệ gia công inox hàng đầu và giải pháp thiết kế tối ưu M&E tạo ra bệ phóng vững chắc để Yuzu Omakase thăng hoa nghệ thuật ẩm thực.', 'saigonhoreca'); ?>
        </p>
      </div>
    </div>

  </div>
</section>
