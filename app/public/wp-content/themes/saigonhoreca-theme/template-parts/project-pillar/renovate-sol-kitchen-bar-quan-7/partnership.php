<?php
/**
 * Project Pillar — renovate-sol-kitchen-bar-quan-7
 * Section #4: with_gallery (Ventilation System)
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="sgh-ventilation-section">
  <div class="sgh-ventilation-container">
    <div class="sgh-ventilation-grid">
      
      <!-- Cột trái: Text & Spec kĩ thuật sang trọng -->
      <div class="sgh-ventilation-text">
        <div class="sgh-ventilation-badge">
          <span class="sgh-ventilation-badge__accent">//</span> DỊCH VỤ & KỸ THUẬT
        </div>
        
        <h3 class="sgh-ventilation-title">
          <?php echo esc_html__('Hệ Thống Chụp Hút Khói Khử Mùi & Cấp Gió Tươi', 'saigonhoreca'); ?>
        </h3>
        
        <div class="sgh-ventilation-body">
          <p class="sgh-ventilation-paragraph">
            <?php echo esc_html__('Hệ thống chụp hút khói dài 8m được thiết kế để hút khói và cấp gió tươi song song trong khu vực nấu. Hệ thống này giúp luồng khí lưu thông liên tục, tạo không gian thoải mái cho các đầu bếp làm việc và hạn chế tình trạng bí khí, đảm bảo môi trường làm việc trong lành và thoáng đãng.', 'saigonhoreca'); ?>
          </p>
          <p class="sgh-ventilation-paragraph">
            <?php echo esc_html__('Trong khu vực rửa, một hệ thống chụp hút khói dài 2m cũng được lắp đặt để hút khói và cấp gió tươi độc lập, đảm bảo khu vực vệ sinh luôn sạch sẽ, khô ráo và không bị ẩm mốc trong suốt quá trình vận hành liên tục của nhà hàng.', 'saigonhoreca'); ?>
          </p>
        </div>

        <!-- Technical Specs Card cao cấp -->
        <div class="sgh-ventilation-specs">
          <h4 class="sgh-ventilation-specs__title"><?php echo esc_html__('Thông số kỹ thuật lắp đặt', 'saigonhoreca'); ?></h4>
          <div class="sgh-ventilation-specs__grid">
            <div class="sgh-ventilation-specs__item">
              <span class="sgh-ventilation-specs__label"><?php echo esc_html__('Chụp hút nấu chính', 'saigonhoreca'); ?></span>
              <span class="sgh-ventilation-specs__value"><?php echo esc_html__('Dài 8.0 mét', 'saigonhoreca'); ?></span>
            </div>
            <div class="sgh-ventilation-specs__item">
              <span class="sgh-ventilation-specs__label"><?php echo esc_html__('Chụp hút khu rửa', 'saigonhoreca'); ?></span>
              <span class="sgh-ventilation-specs__value"><?php echo esc_html__('Dài 2.0 mét', 'saigonhoreca'); ?></span>
            </div>
            <div class="sgh-ventilation-specs__item">
              <span class="sgh-ventilation-specs__label"><?php echo esc_html__('Cơ chế vận hành', 'saigonhoreca'); ?></span>
              <span class="sgh-ventilation-specs__value"><?php echo esc_html__('Hút & Cấp gió song song', 'saigonhoreca'); ?></span>
            </div>
            <div class="sgh-ventilation-specs__item">
              <span class="sgh-ventilation-specs__label"><?php echo esc_html__('Tiêu chuẩn kỹ thuật', 'saigonhoreca'); ?></span>
              <span class="sgh-ventilation-specs__value"><?php echo esc_html__('Michelin-Grade Standard', 'saigonhoreca'); ?></span>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Cột phải: Lưới Mosaic lồng ghép 3 ảnh có Depth & Overlap -->
      <div class="sgh-ventilation-media">
        <div class="sgh-ventilation-mosaic">
          
          <!-- Ảnh 1 (Main): Chụp hút 8m khu nấu chính -->
          <div class="sgh-ventilation-mosaic__item sgh-ventilation-mosaic__item--main">
            <img src="<?php echo sgh_img('2024/06/chup-hut-8m-1-1.jpg'); ?>" alt="<?php echo esc_attr__('Hệ thống chụp hút khói khu nấu chính 8m - Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
          </div>
          
          <!-- Ảnh 2 (Sub 1): Góc cận chụp hút 8m -->
          <div class="sgh-ventilation-mosaic__item sgh-ventilation-mosaic__item--sub1">
            <img src="<?php echo sgh_img('2024/06/chup-hut-8m-2-1.jpg'); ?>" alt="<?php echo esc_attr__('Cận cảnh thi công lắp đặt chụp hút khói 8m - Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
          </div>
          
          <!-- Ảnh 3 (Sub 2): Chụp hút 2m khu rửa đang lắp đặt -->
          <div class="sgh-ventilation-mosaic__item sgh-ventilation-mosaic__item--sub2">
            <img src="<?php echo sgh_img('2024/06/chup-hut-2m-1.jpg'); ?>" alt="<?php echo esc_attr__('Lắp đặt chụp hút khói khu rửa 2m - Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
          </div>
          
        </div>
      </div>

    </div>
  </div>
</section>

