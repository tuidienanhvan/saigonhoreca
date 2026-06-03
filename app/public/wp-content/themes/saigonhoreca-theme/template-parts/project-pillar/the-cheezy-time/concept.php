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
  <!-- SVG nền trang trí hình học mờ -->
  <svg class="pp-concept-bg-svg" viewBox="0 0 1200 800" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
    <circle class="concept-orbit-1" cx="900" cy="200" r="280" stroke="rgba(245,166,35,0.03)" stroke-width="1" stroke-dasharray="8 8"/>
    <circle class="concept-orbit-2" cx="900" cy="200" r="200" stroke="rgba(245,166,35,0.025)" stroke-width="1"/>
    <line class="concept-diag-1" x1="0" y1="600" x2="400" y2="0" stroke="rgba(245,166,35,0.02)" stroke-width="1"/>
    <line class="concept-diag-2" x1="800" y1="800" x2="1200" y2="200" stroke="rgba(245,166,35,0.02)" stroke-width="1"/>
  </svg>

  <div class="pp-container-shared">
    <!-- Tiêu đề concept -->
    <div class="pp-text-tct">
      <span class="pp-text-tct__divider" aria-hidden="true"></span>
      <h2 class="pp-text-tct__title">
        <?php echo esc_html__('Khi concept có chiều sâu, căn bếp không thể là phần phụ', 'saigonhoreca'); ?>
      </h2>
      
      <div class="pp-text-tct__body">
        <p>
          <?php echo esc_html__('The Cheezy Time là mô hình đặc biệt đầu tiên tại Cần Thơ kết hợp bếp Âu hiện đại và pizza tươi phong cách Ý trong cùng một không gian vận hành. Điều này đòi hỏi căn bếp không chỉ đủ công suất, mà còn phải được tổ chức sao cho hai "nhịp nấu" khác nhau có thể song song tồn tại, không chồng chéo, không triệt tiêu lẫn nhau.', 'saigonhoreca'); ?>
        </p>
        <p>
          <?php echo esc_html__('Kết quả là một hệ thống vận hành êm, ổn định và gần như "vô hình" trong trải nghiệm của thực khách – đúng với tinh thần của The Cheezy Time, nơi kỹ thuật luôn đứng phía sau để nâng đỡ cảm xúc phía trước.', 'saigonhoreca'); ?>
        </p>
        <p>
          <?php echo esc_html__('Trong cấu trúc bếp, khu bếp Âu và khu bếp pizza được tổ chức để vừa tách biệt về nhiệt và công năng, vừa kết nối mạch lạc trong luồng vận hành chung. Cấu hình thiết bị được lựa chọn nhằm đảm bảo sự ổn định và tính linh hoạt, bao gồm:', 'saigonhoreca'); ?>
        </p>
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
              <?php echo esc_html__('Thiết kế phối cảnh 3D quầy pha chế trung tâm với dải đèn LED hắt sáng mềm mại, tủ trưng bày bánh ngọt và logo nhận diện thương hiệu độc đáo.', 'saigonhoreca'); ?>
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
