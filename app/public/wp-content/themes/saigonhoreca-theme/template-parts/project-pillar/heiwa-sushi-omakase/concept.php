<?php
/**
 * Project Pillar — heiwa-sushi-omakase
 * Section #3: concept (Bespoke Wabi-Sabi Parallax Editorial)
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="sgh-hwa-concept-section">
  <!-- Thẻ con gánh hiệu ứng Parallax Clip-Path siêu mượt -->
  <div class="sgh-hwa-concept-bg" style="background-image:url('<?php echo sgh_img('heiwa-sushi-omakase/heiwa-sushi-omakase-quay-bar-bieu-dien-omakase.webp'); ?>');"></div>
  <div class="sgh-hwa-concept-overlay" aria-hidden="true"></div>
  
  <div class="pp-container-shared">
    <div class="sgh-hwa-concept-editorial">
      
      <!-- Tiêu đề phong cách tạp chí -->
      <div class="sgh-hwa-concept-header">
        <div class="sgh-hwa-concept-badge">
          <span class="sgh-hwa-concept-badge__accent">//</span> <?php echo esc_html__('KHÔNG GIAN TRẢI NGHIỆM', 'saigonhoreca'); ?>
        </div>
        <h2 class="sgh-hwa-concept-title">
          <?php echo esc_html__('Bầu Không Khí Quầy Bar Tinh Tế & Sang Trọng', 'saigonhoreca'); ?>
        </h2>
      </div>

      <!-- PHẦN 1: Tầm Nhìn & Ý Tưởng (2 Cột thoáng đãng) -->
      <div class="sgh-hwa-concept-intro-row">
        <div class="sgh-hwa-concept-quote-col">
          <div class="sgh-hwa-concept-quote-card">
            <span class="sgh-hwa-concept-quote-mark">“</span>
            <p class="sgh-hwa-concept-quote-text">
              <?php echo esc_html__('Sự tĩnh lặng của nghệ thuật Wabi-Sabi hòa nhịp cùng sự tinh xảo trong chế tác thủ công.', 'saigonhoreca'); ?>
            </p>
          </div>
        </div>
        <div class="sgh-hwa-concept-lead-col">
          <p class="sgh-hwa-concept-lead-text">
            <?php echo esc_html__('Quầy bar của Heiwa Sushi được quy hoạch tinh tế ngay cạnh gian bếp mở biểu diễn, mang đến một không gian ẩm thực sang trọng, thanh tịnh nhưng vô cùng gần gũi. Đây là nơi thực khách có thể hoàn toàn thả lỏng tâm trí, nhâm nhi những ly sake thượng hạng và thong thả thưởng thức từng món sushi tươi ngon.', 'saigonhoreca'); ?>
          </p>
        </div>
      </div>

      <!-- PHẦN 2: Chi tiết vật liệu & thiết kế (Catalog 2 cột) -->
      <div class="sgh-hwa-concept-details-row">
        <div class="sgh-hwa-concept-detail-card">
          <div class="sgh-hwa-concept-detail-meta">
            <span class="sgh-hwa-concept-detail-num">01</span>
            <span class="sgh-hwa-concept-detail-tag"><?php echo esc_html__('Chế Tác Thủ Công', 'saigonhoreca'); ?></span>
          </div>
          <p class="sgh-hwa-concept-detail-desc">
            <?php echo esc_html__('Quầy bar được chế tác thủ công hoàn toàn từ các loại gỗ tự nhiên cao cấp với tone màu trầm ấm. Kết hợp cùng mặt kính cường lực siêu trong suốt chạy dọc viền và ánh đèn vàng hắt dịu nhẹ, tạo nên nhịp điệu tĩnh lặng của nghệ thuật Wabi-Sabi.', 'saigonhoreca'); ?>
          </p>
        </div>

        <div class="sgh-hwa-concept-detail-card">
          <div class="sgh-hwa-concept-detail-meta">
            <span class="sgh-hwa-concept-detail-num">02</span>
            <span class="sgh-hwa-concept-detail-tag"><?php echo esc_html__('Tiêu Chuẩn Công Thái Học', 'saigonhoreca'); ?></span>
          </div>
          <p class="sgh-hwa-concept-detail-desc">
            <?php echo esc_html__('Đội ngũ thiết kế nội thất của Saigon Horeca đã khéo léo tuyển chọn vật liệu cao cấp và phối hợp tay nghề thủ công điêu luyện để mang đến một quầy bar không chỉ duy mỹ về mặt nghệ thuật mà còn đạt tiêu chuẩn công thái học tiện ghi tối đa cho thực khách.', 'saigonhoreca'); ?>
          </p>
        </div>
      </div>

    </div>
  </div>
</section>
