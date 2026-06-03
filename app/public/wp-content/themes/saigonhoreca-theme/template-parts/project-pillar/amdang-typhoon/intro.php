<?php
/**
 * Project Pillar — amdang-typhoon
 * Section #2: intro (Thiết kế sáng tạo Asymmetric Editorial)
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
?>
<section class="pp-section-adt-intro">
  <div class="pp-container-shared">
    <div class="pp-grid-adt-intro">
      
      <!-- Cột trái: Văn bản Michelin và dropcap chữ A -->
      <div class="pp-text-adt-intro">
        <div class="pp-badge-adt-intro">
          <span class="pp-badge-accent-adt-intro">//</span> <?php echo esc_html('MICHELIN GUIDE DI SẢN'); ?>
        </div>
        
        <h3 class="pp-title-adt-intro">
          <?php echo esc_html('Tinh Hoa Ẩm Thực Thái - Hoa Giao Thoa'); ?>
        </h3>
        
        <div class="pp-body-adt-intro">
          <p class="pp-paragraph-adt-intro pp-paragraph-adt-intro--dropcap">
            <?php echo esc_html('Amdang Typhoon tự hào là một trong những nhà hàng danh giá được giới thiệu trang trọng trong cẩm nang ẩm thực toàn cầu Michelin Guide 2021. Mang đậm bản sắc di sản ẩm thực Trung Hoa và Thái Lan độc bản, nhà hàng đã khéo léo tạo dựng nên những dấu ấn ẩm thực sâu đậm trong lòng thực khách qua các món ăn đặc trưng chế biến từ hải sản và cua biển tươi ngon hảo hạng.'); ?>
          </p>
          <p class="pp-paragraph-adt-intro">
            <?php echo esc_html('Sự tỉ mỉ và tôn trọng nguyên tác hương vị truyền thống chính là kim chỉ nam giúp Amdang Typhoon khẳng định vị thế đỉnh cao của mình trong bản đồ ẩm thực cao cấp tại Sài Gòn.'); ?>
          </p>
        </div>
      </div>

      <!-- Cột phải: 3D Safari Mockup trưng bày ảnh bếp chính Michelin -->
      <div class="pp-media-adt-intro">
        <div class="pp-safari-mockup">
          <div class="pp-safari-header">
            <span class="pp-safari-dot pp-safari-dot--red"></span>
            <span class="pp-safari-dot pp-safari-dot--yellow"></span>
            <span class="pp-safari-dot pp-safari-dot--green"></span>
            <div class="pp-safari-address-bar">https://saigonhoreca.vn/amdang-typhoon/</div>
          </div>
          <div class="pp-safari-content pp-image-container-shared">
            <img src="<?php echo sgh_img('amdang-typhoon/amdang-typhoon-loi-vao-nhahang.jpg'); ?>" alt="<?php echo esc_attr('Thiết kế lối vào nhà hàng Amdang Typhoon - Saigon Horeca'); ?>" loading="lazy" decoding="async" width="900" height="600">
            <div class="pp-image-caption-shared"><?php echo esc_html__('Phối cảnh lối hành lang mộc mạc và bảng chỉ dẫn tinh tế dẫn vào nhà hàng Amdang Typhoon', 'saigonhoreca'); ?></div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

