<?php
/**
 * Project Pillar — heiwa-sushi-omakase
 * Section #3: concept — "Không gian trải nghiệm": offset-frame quầy bar image
 * + editorial copy, matching the live production layout.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-hwa pp-section-hwa--alt pp-concept-hwa scroll-reveal">
  <div class="pp-hwa-ambient-glow pp-hwa-ambient-glow--bl" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <div class="pp-concept-hwa__grid">

      <!-- Cột trái: ảnh quầy bar khung lệch -->
      <div class="pp-concept-hwa__media scroll-reveal">
        <div class="pp-image-container-shared pp-frame-hwa pp-concept-hwa__figure">
          <img src="<?php echo sgh_img('heiwa-sushi-omakase/heiwa-sushi-omakase-so-diep-nhat-va-trung-ca-hoi.webp'); ?>" alt="<?php echo esc_attr__('Món sò điệp Nhật áp lửa phủ trứng cá hồi trên rong biển nori tại Heiwa Sushi - Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="940" height="788">
          <div class="pp-image-caption-shared"><?php echo esc_html__('Sò điệp Nhật áp lửa điểm trứng cá hồi tươi trên nền rong biển nori — tinh hoa sashimi Omakase.', 'saigonhoreca'); ?></div>
        </div>
      </div>

      <!-- Cột phải: thuyết minh không gian trải nghiệm -->
      <div class="pp-concept-hwa__text scroll-reveal">
        <div class="pp-text-hwa__badge"><span class="pp-text-hwa__badge-accent">//</span> <?php echo esc_html__('KHÔNG GIAN TRẢI NGHIỆM', 'saigonhoreca'); ?></div>
        <h2 class="pp-text-hwa__title"><?php echo esc_html__('Bầu Không Khí Quầy Bar Tinh Tế & Sang Trọng', 'saigonhoreca'); ?></h2>
        <div class="pp-text-hwa__divider" aria-hidden="true"></div>
        <div class="pp-text-hwa__body">
          <p><?php echo esc_html__('Quầy bar của Heiwa Sushi được quy hoạch tinh tế ngay cạnh gian bếp mở biểu diễn, mang đến một không gian ẩm thực sang trọng, thanh tịnh nhưng vô cùng gần gũi. Đây là nơi thực khách có thể hoàn toàn thả lỏng tâm trí, nhâm nhi những ly sake thượng hạng và thong thả thưởng thức từng món sushi tươi ngon.', 'saigonhoreca'); ?></p>
          <p><?php echo esc_html__('Quầy bar được chế tác thủ công hoàn toàn từ các loại gỗ tự nhiên cao cấp với tone màu trầm ấm. Kết hợp cùng mặt kính cường lực siêu trong suốt chạy dọc viền và ánh đèn vàng hắt dịu nhẹ, tạo nên nhịp điệu tĩnh lặng của nghệ thuật Wabi-Sabi.', 'saigonhoreca'); ?></p>
          <p><?php echo esc_html__('Đội ngũ thiết kế nội thất của Saigon Horeca đã khéo léo tuyển chọn vật liệu cao cấp và phối hợp tay nghề thủ công điêu luyện để mang đến một quầy bar không chỉ duy mỹ về mặt nghệ thuật mà còn đạt tiêu chuẩn công thái học tiện nghi tối đa cho thực khách.', 'saigonhoreca'); ?></p>
        </div>
      </div>

    </div>
  </div>
</section>
