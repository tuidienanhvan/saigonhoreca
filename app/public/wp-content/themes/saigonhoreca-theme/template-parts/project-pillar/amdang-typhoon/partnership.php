<?php
/**
 * Project Pillar — amdang-typhoon
 * Section #4: partnership (Menu & Gastronomy Section)
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
?>
<section class="pp-section-adt-menu">
  <div class="pp-container-shared">
    <div class="pp-grid-adt-menu">
      <!-- Cột trái: Thuyết minh ẩm thực & bảng món ăn đặc trưng -->
      <div class="pp-text-adt-menu">
        <div class="pp-badge-adt-menu">
          <span class="pp-badge-accent-adt-menu">//</span> <?php echo esc_html('ẨM THỰC TINH HOA'); ?>
        </div>

        <h3 class="pp-title-adt-menu">
          <?php echo esc_html('Hương Vị Thái - Trung Đặc Trưng Từ Cua Biển'); ?>
        </h3>

        <div class="pp-body-adt-menu">
          <p class="pp-paragraph-adt-menu">
            <?php echo esc_html('Bản sắc ẩm thực của Amdang Typhoon là sự giao thoa hoàn hảo giữa hương vị truyền thống Thái Lan và nghệ thuật chế biến Trung Hoa. Nguồn cảm hứng lớn nhất của các đầu bếp nơi đây đến từ các món ăn trứ danh làm từ cua biển tươi ngon hảo hạng và hải sản tươi sống.'); ?>
          </p>
          <p class="pp-paragraph-adt-menu">
            <?php echo esc_html('Để hiện thực hóa thực đơn Michelin đỉnh cao đòi hỏi một hệ thống bếp vận hành trơn tru và mạnh mẽ. Saigon Horeca đã đồng hành thiết kế, cung cấp giải pháp thiết bị bếp chuyên sâu, giúp các đầu bếp tối ưu hóa thời gian chuẩn bị và chế biến món ăn mà vẫn giữ trọn tinh túy hương vị nguyên bản.'); ?>
          </p>
        </div>

        <div class="pp-menu-specs-card">
          <h4 class="pp-menu-specs-title"><?php echo esc_html('SIGNATURE MENU EXPERIENCE'); ?></h4>
          <ul class="pp-menu-specs-list">
            <li>
              <span class="spec-name"><?php echo esc_html('Cua Sốt Ớt Cay Amdang (Signature Crab)'); ?></span>
              <span class="spec-value"><?php echo esc_html('Vị cay nồng đặc trưng kiểu Bangkok'); ?></span>
            </li>
            <li>
              <span class="spec-name"><?php echo esc_html('Miến Cua Tay Cầm (Crab Glass Noodles)'); ?></span>
              <span class="spec-value"><?php echo esc_html('Nước dùng hầm xương tủy đậm đà'); ?></span>
            </li>
            <li>
              <span class="spec-name"><?php echo esc_html('Trạm Chế Mì Tươi Di Động (Noodle Station)'); ?></span>
              <span class="spec-value"><?php echo esc_html('Chế biến mì thủ công ngay tại chỗ'); ?></span>
            </li>
          </ul>
        </div>
      </div>

      <!-- Cột phải: Mosaic 4-Image Grid của Menu -->
      <div class="pp-media-adt-menu">
        <div class="pp-menu-mosaic">
          <div class="pp-mosaic-item pp-mosaic-item--1">
            <div class="pp-mosaic-image-wrapper pp-image-container-shared">
              <img src="<?php echo sgh_img('amdang-typhoon/amdang-typhoon-mon-an-thai-hoa-hai-san.jpeg'); ?>" alt="<?php echo esc_attr('Set ẩm thực Thái Lan cao cấp với canh Tom Kha và hải sản chiên tỏi ớt'); ?>" loading="lazy" decoding="async">
              <div class="pp-image-caption-shared"><?php echo esc_html__('Set ẩm thực Thái Lan cao cấp với canh Tom Kha và hải sản chiên tỏi ớt', 'saigonhoreca'); ?></div>
            </div>
          </div>

          <div class="pp-mosaic-item pp-mosaic-item--2">
            <div class="pp-mosaic-image-wrapper pp-image-container-shared">
              <img src="<?php echo sgh_img('amdang-typhoon/amdang-typhoon-ban-tiec-hai-san.jpeg'); ?>" alt="<?php echo esc_attr('Bàn tiệc hải sản Thái Lan thịnh soạn với canh Tom Yum Goong tôm càng và cá vược chiên giòn'); ?>" loading="lazy" decoding="async">
              <div class="pp-image-caption-shared"><?php echo esc_html__('Bàn tiệc hải sản Thái Lan thịnh soạn với canh Tom Yum Goong tôm càng và cá vược chiên giòn', 'saigonhoreca'); ?></div>
            </div>
          </div>

          <div class="pp-mosaic-item pp-mosaic-item--3">
            <div class="pp-mosaic-image-wrapper pp-image-container-shared">
              <img src="<?php echo sgh_img('amdang-typhoon/amdang-typhoon-mon-goi-cay-bangkok.jpeg'); ?>" alt="<?php echo esc_attr('Gỏi đu đủ ba khía Som Tum chua cay ăn kèm gà nướng xiên satay và chả tôm chiên donut'); ?>" loading="lazy" decoding="async">
              <div class="pp-image-caption-shared"><?php echo esc_html__('Gỏi đu đủ ba khía Som Tum chua cay ăn kèm gà nướng xiên satay và chả tôm chiên donut', 'saigonhoreca'); ?></div>
            </div>
          </div>

          <div class="pp-mosaic-item pp-mosaic-item--4">
            <div class="pp-mosaic-image-wrapper pp-image-container-shared">
              <img src="<?php echo sgh_img('amdang-typhoon/amdang-typhoon-mon-an-mi-xao-muc-chien.jpeg'); ?>" alt="<?php echo esc_attr('Bộ sưu tập các món khai vị chiên giòn truyền thống Thái Lan với chả tôm donut và cánh gà chiên sả'); ?>" loading="lazy" decoding="async">
              <div class="pp-image-caption-shared"><?php echo esc_html__('Bộ sưu tập các món khai vị chiên giòn truyền thống Thái Lan với chả tôm donut và cánh gà chiên sả', 'saigonhoreca'); ?></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

