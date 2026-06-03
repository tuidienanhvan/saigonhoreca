<?php
/**
 * Project Pillar — the-royal-all-day-dining
 * Section #3: CONCEPT — Cinematic Storytelling Scroll Layout
 * Timeline dọc + xen kẽ text-image + full-width overlay + asymmetric gallery
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp-concept-cinema-trd">
  <!-- Watermark số thứ tự 03 chìm cực chất nghệ thuật -->
  <div class="pp-concept-watermark-trd" aria-hidden="true">03</div>

  <!-- SVG trang trí tinh tế chìm ở nền -->
  <div class="pp-concept-cinema-trd__svg-backdrop" aria-hidden="true">
    <svg viewBox="0 0 400 400" fill="none" xmlns="http://www.w3.org/2000/svg">
      <line x1="200" y1="0" x2="200" y2="400" stroke="color-mix(in srgb, var(--gold) 6%, transparent)" stroke-width="0.5" stroke-dasharray="3 3" />
      <line x1="0" y1="200" x2="400" y2="200" stroke="color-mix(in srgb, var(--gold) 6%, transparent)" stroke-width="0.5" stroke-dasharray="3 3" />
      <circle cx="200" cy="200" r="140" stroke="color-mix(in srgb, var(--gold) 4%, transparent)" stroke-width="0.75" />
      <circle cx="200" cy="200" r="170" stroke="color-mix(in srgb, var(--gold) 2%, transparent)" stroke-dasharray="2 4" stroke-width="0.5" />
    </svg>
  </div>

  <div class="pp__container pp-container-shared">
    <!-- Chapter 1: Offset Split (Text 40% trái, Image 55% phải) -->
    <div class="pp-concept-cinema-trd__chapter">

      <div class="pp-concept-cinema-trd__split pp-concept-cinema-trd__split--offset">
        <div class="pp-concept-cinema-trd__text">
          <!-- Huy hiệu/Badge phong cách Luxury -->
          <div class="pp-badge-trd">
            <svg class="pp-badge-trd__icon" viewBox="0 0 24 24" fill="currentColor">
              <path d="M12 2L14.85 9.15L22 12L14.85 14.85L12 22L9.15 14.85L2 12L9.15 9.15L12 2Z"/>
            </svg>
            <?php echo esc_html__('Concept Thiết Kế', 'saigonhoreca'); ?>
          </div>

          <h2 class="pp-text-trd__title"><?php echo esc_html__('Thiết kế của The Royal All Day Dining', 'saigonhoreca'); ?></h2>
          <span class="pp-text-trd__divider" aria-hidden="true"></span>
          <div class="pp-text-trd__body pp-text-trd__body--dropcap">
            <p><?php echo esc_html__('Tọa lạc tại 41–47 Đông Du, Bến Nghé, Quận 1, TP. Hồ Chí Minh, The Royal All Day Dining mang dáng dấp của một nhà hàng đẳng cấp – nơi mỗi trải nghiệm ẩm thực đều được chăm chút tỉ mỉ. Không phô trương hình ảnh, The Royal lựa chọn cách chinh phục khách hàng bằng chất lượng, sự chỉn chu và phong cách vận hành chuyên nghiệp.', 'saigonhoreca'); ?></p>
            <p><?php echo esc_html__('Không gian và thiết kế không cần lời quảng bá rầm rộ – chính cảm nhận của thực khách sẽ là minh chứng rõ ràng nhất cho sự khác biệt mang tên The Royal.', 'saigonhoreca'); ?></p>
          </div>
        </div>
        <div class="pp-concept-cinema-trd__media">
          <figure class="pp-image-container-shared pp-image-container-rf">
            <!-- Khung góc SVG nghệ thuật mạ vàng kiêu sa -->
            <svg class="card-corner-svg" viewBox="0 0 100 100" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M 0 15 L 0 0 L 15 0" stroke="var(--gold)" stroke-width="1.2" />
              <path d="M 100 15 L 100 0 L 85 0" stroke="var(--gold)" stroke-width="1.2" />
              <path d="M 0 85 L 0 100 L 15 100" stroke="var(--gold)" stroke-width="1.2" />
              <path d="M 100 85 L 100 100 L 85 100" stroke="var(--gold)" stroke-width="1.2" />
            </svg>
            <img src="<?php echo sgh_img('the-royal-all-day-dining/the-royal-all-day-dining-vach-kinh-phan-chieu-logo.webp'); ?>" alt="<?php echo esc_attr__('Hệ gương ghép trám ô quả trám sang trọng phản chiếu không gian quầy bar và logo nổi The Royal All Day Dining', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
            <figcaption class="pp-image-caption-shared"><?php echo esc_html__('Hệ thống gương ốp tường phản chiếu kết hợp logo nổi The Royal All Day Dining sang trọng.', 'saigonhoreca'); ?></figcaption>
          </figure>
        </div>
      </div>
    </div>

    <!-- Chapter 2: Full-width image + overlay text card -->
    <div class="pp-concept-cinema-trd__chapter">

      <div class="pp-concept-cinema-trd__fullwidth">
        <figure class="pp-image-container-shared pp-image-container-rf pp-concept-cinema-trd__fullwidth-fig">
          <!-- Khung góc SVG nghệ thuật mạ vàng kiêu sa -->
          <svg class="card-corner-svg" viewBox="0 0 100 100" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M 0 15 L 0 0 L 15 0" stroke="var(--gold)" stroke-width="1.2" />
            <path d="M 100 15 L 100 0 L 85 0" stroke="var(--gold)" stroke-width="1.2" />
            <path d="M 0 85 L 0 100 L 15 100" stroke="var(--gold)" stroke-width="1.2" />
            <path d="M 100 85 L 100 100 L 85 100" stroke="var(--gold)" stroke-width="1.2" />
          </svg>
          <img src="<?php echo sgh_img('the-royal-all-day-dining/the-royal-all-day-dining-ghe-sofa-cong-da-bo.webp'); ?>" alt="<?php echo esc_attr__('Dãy ghế sofa da nâu ấm cúng dưới ánh đèn chùm cổ điển tại The Royal', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
          <figcaption class="pp-image-caption-shared"><?php echo esc_html__('Không gian sảnh ngồi với dãy sofa da bò cong cổ điển tinh tế.', 'saigonhoreca'); ?></figcaption>
        </figure>
        <div class="pp-concept-cinema-trd__overlay-card">
          <p><?php echo esc_html__('Bên trong, không gian mang hơi hướng một phòng trà châu Âu thế kỷ trước, với ghế bọc da nâu cổ điển, tường ốp gỗ tự nhiên và hệ thống đèn chùm ánh vàng dịu.', 'saigonhoreca'); ?></p>
        </div>
      </div>
    </div>

    <!-- Chapter 3: Body text + Asymmetric Gallery -->
    <div class="pp-concept-cinema-trd__chapter">

      <div class="pp-concept-cinema-trd__narrative">
        <p><?php echo esc_html__('Cách bố trí ghế sofa cong mềm mại, đan cài cùng những vách ngăn thấp bằng gỗ và kính, tạo nên từng "khoảng trời riêng" cho thực khách mà không làm mất đi sự liên kết tổng thể. Một chi tiết nhỏ nhưng tinh tế chính là sự lặp lại nhẹ nhàng của họa tiết đường cong — từ cửa vòm, đến lưng ghế, đến khung kính tường — như những giai điệu cổ xưa vang lên trong không gian hiện đại.', 'saigonhoreca'); ?></p>
      </div>

      <div class="pp-concept-cinema-trd__asymmetric-gallery">
        <figure class="pp-image-container-shared pp-image-container-rf pp-concept-cinema-trd__gallery-main">
          <!-- Khung góc SVG nghệ thuật mạ vàng kiêu sa -->
          <svg class="card-corner-svg" viewBox="0 0 100 100" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M 0 15 L 0 0 L 15 0" stroke="var(--gold)" stroke-width="1.2" />
            <path d="M 100 15 L 100 0 L 85 0" stroke="var(--gold)" stroke-width="1.2" />
            <path d="M 0 85 L 0 100 L 15 100" stroke="var(--gold)" stroke-width="1.2" />
            <path d="M 100 85 L 100 100 L 85 100" stroke="var(--gold)" stroke-width="1.2" />
          </svg>
          <img src="<?php echo sgh_img('the-royal-all-day-dining/the-royal-all-day-dining-can-canh-setup-ban-an.jpg'); ?>" alt="<?php echo esc_attr__('Bàn ăn gỗ tối màu kết hợp ghế bọc da ấm cúng phong cách phòng trà Châu Âu', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
          <figcaption class="pp-image-caption-shared"><?php echo esc_html__('Chi tiết sắp đặt bàn ăn phong cách Fine Dining cao cấp.', 'saigonhoreca'); ?></figcaption>
        </figure>
        <figure class="pp-image-container-shared pp-image-container-rf pp-concept-cinema-trd__gallery-side">
          <!-- Khung góc SVG nghệ thuật mạ vàng kiêu sa -->
          <svg class="card-corner-svg" viewBox="0 0 100 100" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M 0 15 L 0 0 L 15 0" stroke="var(--gold)" stroke-width="1.2" />
            <path d="M 100 15 L 100 0 L 85 0" stroke="var(--gold)" stroke-width="1.2" />
            <path d="M 0 85 L 0 100 L 15 100" stroke="var(--gold)" stroke-width="1.2" />
            <path d="M 100 85 L 100 100 L 85 100" stroke="var(--gold)" stroke-width="1.2" />
          </svg>
          <img src="<?php echo sgh_img('the-royal-all-day-dining/the-royal-all-day-dining-ghe-don-nhung-xanh-reu.jpg'); ?>" alt="<?php echo esc_attr__('Chi tiết ghế bọc nệm nhung xanh lá cao cấp và bàn gỗ tại The Royal', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
          <figcaption class="pp-image-caption-shared"><?php echo esc_html__('Dãy ghế ăn bọc nhung xanh rêu đan xen da kem sang trọng.', 'saigonhoreca'); ?></figcaption>
        </figure>
      </div>

      <!-- Closing text -->
      <div class="pp-concept-cinema-trd__closing">
        <p><?php echo esc_html__('Ánh sáng ở The Royal được tiết chế kỹ lưỡng. Không gian ấm cúng và sang trọng, vừa đủ để mỗi bữa ăn trở thành một khoảnh khắc tận hưởng trọn vẹn. Đèn chùm kiểu dáng tối giản, sử dụng lớp thủy tinh mờ để ánh sáng không chói lóa, lại làm nổi bật được độ óng ả của gỗ và da trong phòng.', 'saigonhoreca'); ?></p>
        <p><?php echo esc_html__('The Royal không cố gắng làm mình khác biệt một cách ồn ào, mà chọn cách lặng lẽ chinh phục lòng người — giống như một bản nhạc trữ tình ngân vang giữa những bộn bề náo động. Đó chính là "chất" Royal: tinh tế, trầm lắng và bền bỉ theo thời gian.', 'saigonhoreca'); ?></p>
      </div>
    </div>
  </div>
</section>
