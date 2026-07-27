<?php
/**
 * Project Pillar — the-cheezy-time
 * Section #3: concept — Nâng cấp sáng tạo layout chữ & ảnh
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-tct pp-concept-section-tct">
  <!-- SVG nền bản vẽ kỹ thuật Blueprint mờ ảo -->
  <svg class="pp-concept-bg-svg pp-concept-blueprint-svg" viewBox="0 0 1200 800" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
    <defs>
      <!-- Định nghĩa lưới ô vuông kiểu CAD -->
      <pattern id="blueprint-grid-tct" width="40" height="40" patternUnits="userSpaceOnUse">
        <path d="M 40 0 L 0 0 0 40" fill="none" stroke="rgba(245, 166, 35, 0.012)" stroke-width="0.75"/>
      </pattern>
      <!-- Đầu mũi tên đo kích thước -->
      <marker id="arrow-start-tct" viewBox="0 0 10 10" refX="5" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
        <path d="M 0 0 L 10 5 L 0 10 z" fill="rgba(245, 166, 35, 0.12)"/>
      </marker>
      <marker id="arrow-end-tct" viewBox="0 0 10 10" refX="5" refY="5" markerWidth="6" markerHeight="6" orient="auto">
        <path d="M 0 0 L 10 5 L 0 10 z" fill="rgba(245, 166, 35, 0.12)"/>
      </marker>
    </defs>
    <!-- Lưới blueprint toàn vùng -->
    <rect width="100%" height="100%" fill="url(#blueprint-grid-tct)" />
    
    <!-- Các đường nét CAD giả lập thiết kế mặt bằng bếp -->
    <g opacity="0.8">
      <!-- Chu vi tổng thể -->
      <rect x="50" y="60" width="1100" height="680" stroke="rgba(245, 166, 35, 0.025)" stroke-width="1.5" stroke-dasharray="10 5"/>
      
      <!-- Đường đo kích thước ngang phía trên -->
      <line x1="50" y1="35" x2="1150" y2="35" stroke="rgba(245, 166, 35, 0.08)" stroke-width="1" marker-start="url(#arrow-start-tct)" marker-end="url(#arrow-end-tct)"/>
      <line x1="50" y1="25" x2="50" y2="45" stroke="rgba(245, 166, 35, 0.08)" stroke-width="1"/>
      <line x1="1150" y1="25" x2="1150" y2="45" stroke="rgba(245, 166, 35, 0.08)" stroke-width="1"/>
      <text x="600" y="30" fill="var(--gold)" font-size="9" font-family="monospace" letter-spacing="0.15em" opacity="0.4" text-anchor="middle">SECTION 03 - OPERATIONAL FLOW LENGTH: 24,800 mm</text>
      
      <!-- Sơ đồ phân khu bếp dạng hình học mờ -->
      <!-- Khu bếp Âu (Trái) -->
      <rect x="120" y="140" width="380" height="240" stroke="rgba(245, 166, 35, 0.015)" stroke-width="1"/>
      <line x1="120" y1="140" x2="500" y2="380" stroke="rgba(245, 166, 35, 0.008)" stroke-width="0.5"/>
      <line x1="500" y1="140" x2="120" y2="380" stroke="rgba(245, 166, 35, 0.008)" stroke-width="0.5"/>
      <text x="310" y="265" fill="var(--gold)" font-size="10" font-family="monospace" letter-spacing="0.08em" opacity="0.25" text-anchor="middle">HOT KITCHEN ZONE (WEST)</text>
      
      <!-- Khu Bếp Pizza (Phải) -->
      <rect x="700" y="140" width="380" height="240" stroke="rgba(245, 166, 35, 0.015)" stroke-width="1"/>
      <circle cx="890" cy="260" r="80" stroke="rgba(245, 166, 35, 0.02)" stroke-width="1" stroke-dasharray="4 4"/>
      <text x="890" y="265" fill="var(--gold)" font-size="10" font-family="monospace" letter-spacing="0.08em" opacity="0.25" text-anchor="middle">PIZZA OVEN ZONE (EAST)</text>
      
      <!-- Luồng chuyển tiếp ở giữa -->
      <path d="M 520 260 L 680 260" stroke="rgba(245, 166, 35, 0.08)" stroke-width="1.5" stroke-dasharray="6 4" marker-end="url(#arrow-end-tct)"/>
      <text x="600" y="250" fill="var(--gold)" font-size="8" font-family="monospace" letter-spacing="0.1em" opacity="0.3" text-anchor="middle">DUAL PASSAGE</text>
    </g>

    <!-- Quỹ đạo vòng xoay kỹ thuật (giữ lại từ code cũ nhưng đổi màu mờ hơn) -->
    <circle class="concept-orbit-1" cx="950" cy="220" r="240" stroke="rgba(245,166,35,0.02)" stroke-width="1" stroke-dasharray="8 8"/>
    <circle class="concept-orbit-2" cx="950" cy="220" r="160" stroke="rgba(245,166,35,0.015)" stroke-width="1"/>
  </svg>

  <div class="pp-container-shared">
    <!-- Tiêu đề concept và mô tả dạng Tạp chí 2 cột phi đối xứng (Editorial) -->
    <div class="pp-text-tct-editorial">
      <div class="pp-text-tct-editorial__left">
        <div class="pp-text-tct-editorial__section-tag">
          <span class="pp-text-tct-editorial__num">03</span>
          <span class="pp-text-tct-editorial__divider-v" aria-hidden="true"></span>
          <span class="pp-text-tct-editorial__badge">CONCEPT PHILOSOPHY</span>
        </div>
        <h2 class="pp-text-tct-editorial__title">
          Khi <span class="pp-editorial-highlight-italic">concept</span> có chiều sâu,<br>
          căn bếp không thể là <span class="pp-editorial-highlight-serif">phần phụ</span>
        </h2>
        <div class="pp-text-tct-editorial__title-line" aria-hidden="true"></div>
        
        <!-- Bảng thông số nhanh phong cách bản vẽ kỹ thuật -->
        <div class="pp-concept-quick-specs">
          <div class="pp-concept-quick-specs__item">
            <span class="pp-concept-quick-specs__label"><?php echo esc_html__('Setup phân khu', 'saigonhoreca'); ?></span>
            <span class="pp-concept-quick-specs__value"><?php echo esc_html__('02 (Bếp Âu & Pizza tươi)', 'saigonhoreca'); ?></span>
          </div>
          <div class="pp-concept-quick-specs__item">
            <span class="pp-concept-quick-specs__label"><?php echo esc_html__('Luồng vận hành', 'saigonhoreca'); ?></span>
            <span class="pp-concept-quick-specs__value"><?php echo esc_html__('Song song - Không chồng chéo', 'saigonhoreca'); ?></span>
          </div>
          <div class="pp-concept-quick-specs__item">
            <span class="pp-concept-quick-specs__label"><?php echo esc_html__('Đặc tính thiết bị', 'saigonhoreca'); ?></span>
            <span class="pp-concept-quick-specs__value"><?php echo esc_html__('Cách biệt nhiệt & Công năng', 'saigonhoreca'); ?></span>
          </div>
        </div>
      </div>
      
      <div class="pp-text-tct-editorial__right">
        <div class="pp-text-tct-editorial__lead-card">
          <span class="pp-text-tct-editorial__quote-icon" aria-hidden="true">“</span>
          <p class="pp-text-tct-editorial__lead">
            <?php echo esc_html__('The Cheezy Time là mô hình đặc biệt đầu tiên tại Cần Thơ kết hợp bếp Âu hiện đại và pizza tươi phong cách Ý trong cùng một không gian vận hành. Điều này đòi hỏi căn bếp không chỉ đủ công suất, mà còn phải được tổ chức sao cho hai "nhịp nấu" khác nhau có thể song song tồn tại, không chồng chéo, không triệt tiêu lẫn nhau.', 'saigonhoreca'); ?>
          </p>
        </div>
        
        <div class="pp-text-tct-editorial__body">
          <div class="pp-editorial-body-para">
            <span class="pp-editorial-para-num">01 / VẬN HÀNH</span>
            <p>
              <?php echo esc_html__('Kết quả là một hệ thống vận hành êm, ổn định và gần như "vô hình" trong trải nghiệm của thực khách – đúng với tinh thần của The Cheezy Time, nơi kỹ thuật luôn đứng phía sau để nâng đỡ cảm xúc phía trước.', 'saigonhoreca'); ?>
            </p>
          </div>
          <div class="pp-editorial-body-para">
            <span class="pp-editorial-para-num">02 / CẤU TRÚC</span>
            <p>
              <?php echo esc_html__('Trong cấu trúc bếp, khu bếp Âu và khu bếp pizza được tổ chức để vừa tách biệt về nhiệt và công năng, vừa kết nối mạch lạc trong luồng vận hành chung. Cấu hình thiết bị được lựa chọn nhằm đảm bảo sự ổn định và tính linh hoạt, bao gồm:', 'saigonhoreca'); ?>
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════
         HÀNG 1: LÒ GẠCH — Text trái / Ảnh phải
         ═══════════════════════════════════════════════════════ -->
    <div class="pp-concept-grid-tct pp-concept-grid-tct--row1">
      <!-- Cột chữ: Accent Card với border-left & callout -->
      <div class="pp-concept-text-tct pp-concept-text-tct--bar">
        <div class="pp-concept-accent-bar" aria-hidden="true"></div>
        <h3 class="pp-concept-heading-tct">
          <?php echo esc_html__('Quầy pha chế trung tâm – Giao điểm của Thẩm mỹ & Vận hành', 'saigonhoreca'); ?>
        </h3>
        <div class="pp-concept-body-tct">
          <div class="pp-concept-callout-tct">
            <span class="pp-concept-callout-icon" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 2L14.85 9.15L22 12L14.85 14.85L12 22L9.15 14.85L2 12L9.15 9.15L12 2Z"/>
              </svg>
            </span>
            <p>
              <strong><?php echo esc_html__('Quầy pha chế trung tâm không chỉ là nơi phục vụ đồ uống. Tại The Cheezy Time, đây là điểm chạm thị giác đầu tiên, kết nối mượt mà giữa không gian nhà hàng ấm cúng và nhịp sống năng động.', 'saigonhoreca'); ?></strong>
            </p>
          </div>
          <p class="pp-concept-desc-tct">
            <?php echo esc_html__('Thiết kế phối cảnh 3D quầy pha chế được Saigon Horeca tư vấn bố trí tỉ mỉ: từ hệ mặt đá cao cấp dễ vệ sinh, tủ kính trưng bày bánh ngọt tích hợp đèn LED ấm áp, cho đến logo thương hiệu phát sáng nhẹ dịu – tất cả tạo nên ấn tượng chuyên nghiệp ngay từ cái nhìn đầu tiên.', 'saigonhoreca'); ?>
          </p>
        </div>
      </div>
      
      <!-- Cột ảnh: Quầy pha chế 3D -->
      <div class="pp-concept-media-tct pp-concept-media-tct--bar">
        <!-- SVG trang trí quỹ đạo quay -->
        <svg class="pp-concept-orbit-decor" viewBox="0 0 400 300" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
          <ellipse class="orbit-ring-1" cx="200" cy="150" rx="190" ry="140" stroke="rgba(245,166,35,0.06)" stroke-width="1" stroke-dasharray="4 8"/>
          <circle class="orbit-dot-1" cx="390" cy="150" r="3" fill="var(--gold)" opacity="0.4"/>
        </svg>
        <div class="pp-tct-luxury-frame pp-tct-luxury-frame--concept">
          <span class="pp-tct-corner pp-tct-corner--tl"></span>
          <span class="pp-tct-corner pp-tct-corner--tr"></span>
          <span class="pp-tct-corner pp-tct-corner--bl"></span>
          <span class="pp-tct-corner pp-tct-corner--br"></span>
          <div class="pp-image-container-shared" style="aspect-ratio: 16 / 10;">
            <img src="<?php echo sgh_img('the-cheezy-time/the-cheezy-time-bar-counter-3d.jpg'); ?>" alt="<?php echo esc_attr__('Thiết kế phối cảnh 3D quầy pha chế trung tâm The Cheezy Time', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
            <div class="pp-image-caption-shared">
              <?php echo esc_html__('Phối cảnh 3D quầy pha chế trung tâm: dải đèn LED hắt sáng ấm dưới mặt quầy, kệ rượu cùng cụm vòi bia, tủ kính trưng bày và logo thương hiệu nổi bật trên nền tường gạch.', 'saigonhoreca'); ?>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════
         HÀNG 2: BẾP BÁNH & BẾP NẤU — Ảnh trái / Text phải (đảo bên)
         ═══════════════════════════════════════════════════════ -->
    <div class="pp-concept-grid-tct pp-concept-grid-tct--row2 pp-concept-grid-tct--reverse">
      <!-- Cột ảnh: Bếp bánh & bếp nấu -->
      <div class="pp-concept-media-tct pp-concept-media-tct--kitchen">
        <!-- SVG trang trí mạch kỹ thuật -->
        <svg class="pp-concept-tech-decor" viewBox="0 0 400 300" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
          <rect class="tech-rect-pulse" x="10" y="10" width="380" height="280" rx="8" stroke="rgba(245,166,35,0.05)" stroke-width="1" stroke-dasharray="12 8"/>
          <circle class="tech-node-tl" cx="10" cy="10" r="2.5" fill="var(--gold)" opacity="0.5"/>
          <circle class="tech-node-br" cx="390" cy="290" r="2.5" fill="var(--gold)" opacity="0.5"/>
        </svg>
        <div class="pp-tct-luxury-frame pp-tct-luxury-frame--concept">
          <span class="pp-tct-corner pp-tct-corner--tl"></span>
          <span class="pp-tct-corner pp-tct-corner--tr"></span>
          <span class="pp-tct-corner pp-tct-corner--bl"></span>
          <span class="pp-tct-corner pp-tct-corner--br"></span>
          <div class="pp-image-container-shared" style="aspect-ratio: 16 / 10;">
            <img src="<?php echo sgh_img('the-cheezy-time/the-cheezy-time-gas-burner.jpg'); ?>" alt="<?php echo esc_attr__('Hệ thống bếp gas công nghiệp bằng thép không gỉ cao cấp', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
            <div class="pp-image-caption-shared">
              <?php echo esc_html__('Cận cảnh hệ thống bếp gas công nghiệp bằng thép không gỉ cao cấp được lắp đặt chuẩn chỉnh cho khu vực bếp nóng.', 'saigonhoreca'); ?>
            </div>
          </div>
        </div>
      </div>

      <!-- Cột chữ: Timeline-style card -->
      <div class="pp-concept-text-tct pp-concept-text-tct--kitchen">
        <div class="pp-concept-accent-bar pp-concept-accent-bar--alt" aria-hidden="true"></div>
        <h3 class="pp-concept-heading-tct">
          <?php echo esc_html__('Phân chia bếp bánh và bếp nấu khoa học', 'saigonhoreca'); ?>
        </h3>
        <div class="pp-concept-body-tct">
          <div class="pp-concept-timeline-tct">
            <div class="pp-concept-timeline-item-tct">
              <span class="pp-concept-timeline-dot-tct" aria-hidden="true"></span>
              <p class="pp-concept-desc-tct">
                <?php echo esc_html__('Bếp bánh và bếp nấu được bố trí thành hai khu vực riêng biệt nhưng nằm sát nhau, thuận tiện cho việc vận hành. Nhờ cách chia tách này, mùi vị giữa các món ăn và bánh ngọt không bị pha lẫn, giúp giữ được chất lượng thành phẩm tốt nhất.', 'saigonhoreca'); ?>
              </p>
            </div>
            <div class="pp-concept-timeline-item-tct">
              <span class="pp-concept-timeline-dot-tct" aria-hidden="true"></span>
              <p class="pp-concept-desc-tct">
                <?php echo esc_html__('Đồng thời, chức năng của từng khu được phân chia rõ ràng: khu bếp nấu tập trung vào chế biến món mặn, còn khu bếp bánh được trang bị thiết bị chuyên dụng để làm các loại bánh ngọt và tráng miệng.', 'saigonhoreca'); ?>
              </p>
            </div>
          </div>
          <div class="pp-concept-closing-tct">
            <p>
              <em><?php echo esc_html__('Cách bố trí này không chỉ hợp lý mà còn giúp đầu bếp làm việc hiệu quả, tránh va chạm và giảm áp lực trong giờ cao điểm – tất cả cho thấy một triết lý: đằng sau một trải nghiệm đẹp là một cấu trúc vận hành thông minh và tôn trọng thiết kế.', 'saigonhoreca'); ?></em>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
