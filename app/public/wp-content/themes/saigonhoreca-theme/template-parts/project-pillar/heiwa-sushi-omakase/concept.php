<?php
/**
 * Project Pillar — heiwa-sushi-omakase
 * Section #3: split
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="sgh-hwa-concept-section">
  <div class="sgh-hwa-concept-container">
    <div class="sgh-hwa-concept-grid">
      
      <!-- Cột trái: Offset Frame Media Showcase (Quầy Bar) -->
      <div class="sgh-hwa-concept-media">
        <div class="sgh-hwa-concept-media__frame" aria-hidden="true"></div>
        <div class="sgh-hwa-concept-media__wrapper">
          <img src="<?php echo sgh_img('2024/02/heiwasushi3-1.jpg'); ?>" alt="<?php echo esc_attr__('Bầu không khí quầy bar tinh tế và sang trọng tại Heiwa Sushi - Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="940" height="788">
        </div>
      </div>

      <!-- Cột phải: Thuyết minh & Typography -->
      <div class="sgh-hwa-concept-text">
        <div class="sgh-hwa-concept-badge">
          <span class="sgh-hwa-concept-badge__accent">//</span> <?php echo esc_html__('KHÔNG GIAN TRẢI NGHIỆM', 'saigonhoreca'); ?>
        </div>
        
        <h3 class="sgh-hwa-concept-title">
          <?php echo esc_html__('Bầu Không Khí Quầy Bar Tinh Tế & Sang Trọng', 'saigonhoreca'); ?>
        </h3>
        
        <div class="sgh-hwa-concept-body">
          <p class="sgh-hwa-concept-paragraph">
            <?php echo esc_html__('Quầy bar của Heiwa Sushi được quy hoạch tinh tế ngay cạnh gian bếp mở biểu diễn, mang đến một không gian ẩm thực sang trọng, thanh tịnh nhưng vô cùng gần gũi. Đây là nơi thực khách có thể hoàn toàn thả lỏng tâm trí, nhâm nhi những ly sake thượng hạng và thong thả thưởng thức từng món sushi tươi ngon.', 'saigonhoreca'); ?>
          </p>
          <p class="sgh-hwa-concept-paragraph">
            <?php echo esc_html__('Quầy bar được chế tác thủ công hoàn toàn từ các loại gỗ tự nhiên cao cấp với tone màu trầm ấm. Kết hợp cùng mặt kính cường lực siêu trong suốt chạy dọc viền và ánh đèn vàng hắt dịu nhẹ, tạo nên nhịp điệu tĩnh lặng của nghệ thuật Wabi-Sabi.', 'saigonhoreca'); ?>
          </p>
          <p class="sgh-hwa-concept-paragraph">
            <?php echo esc_html__('Đội ngũ thiết kế nội thất của Saigon Horeca đã khéo léo tuyển chọn vật liệu cao cấp và phối hợp tay nghề thủ công điêu luyện để mang đến một quầy bar không chỉ duy mỹ về mặt nghệ thuật mà còn đạt tiêu chuẩn công thái học tiện nghi tối đa cho thực khách.', 'saigonhoreca'); ?>
          </p>
        </div>
      </div>

    </div>
  </div>
</section>

