<?php
/**
 * Project Pillar — amdang-typhoon
 * Section #7: related (Quy hoạch bếp chính và quầy bar)
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
?>
<section class="pp-related-at">
  <div class="pp-container-at-related">
    
    <!-- Phần trên: Header & Text Split (Text trái 55%, Quote phải 45% trên Desktop) -->
    <div class="pp-related-at__header-grid">
      <div class="pp-text-at-related">
        <div class="pp-badge-at-related">
          <span class="pp-badge-accent-at-related">//</span> <?php echo esc_html('THI CÔNG & GIÁM SÁT'); ?>
        </div>
        
        <h3 class="pp-title-at-related">
          <?php echo esc_html('Quy Hoạch Bếp Chính & Quầy Bar Chuyên Nghiệp'); ?>
        </h3>
        
        <div class="pp-body-at-related">
          <p class="pp-paragraph-at-related">
            <?php echo esc_html('Khu vực bếp chính đóng vai trò cốt lõi vì đây là nơi kiến tạo các tác phẩm ẩm thực độc đáo làm nên uy tín của nhà hàng. Không gian được quy hoạch tỉ mỉ từ khâu lưu trữ, sơ chế, chế biến đến vệ sinh nhằm tối ưu hóa sự thoải mái cho đầu bếp và tuân thủ nghiêm ngặt các quy định về an toàn thực phẩm.'); ?>
          </p>
          <p class="pp-paragraph-at-related">
            <?php echo esc_html('Song song đó, khu vực quầy bar lớn được bố trí dụng cụ pha chế một cách khoa học và gọn gàng, giúp các bartender dễ dàng thao tác nhanh chóng và phô diễn tài năng pha chế nghệ thuật, mang lại không gian trình diễn đẹp mắt và đẳng cấp chuyên nghiệp.'); ?>
          </p>
        </div>
      </div>

      <!-- Khối Quote sang trọng bên phải (Đã sửa lỗi chữ dấu tổ hợp diacritics rải rác) -->
      <div class="pp-related-quote-wrapper">
        <blockquote class="pp-related-quote">
          <p>
            <?php echo esc_html('“Sự khoa học trong sắp đặt thiết bị không chỉ nâng cao hiệu suất làm việc, mà còn khơi nguồn cảm hứng sáng tạo tối đa cho người nghệ sĩ F&B.”'); ?>
          </p>
          <cite class="pp-related-quote-author"><?php echo esc_html('KỸ SƯ QUY HOẠCH SAIGON HORECA'); ?></cite>
        </blockquote>
      </div>
    </div>

    <!-- Phần dưới: Lưới hình ảnh cực lớn (Full Width Grid) giúp xem rõ chi tiết bản vẽ -->
    <div class="pp-media-at-related-full">
      <div class="pp-related-mosaic-full">
        
        <!-- Ảnh 1: Bản vẽ bếp chính chiếm 100% hàng trên (Cực to rõ) -->
        <div class="pp-related-mosaic-item-full pp-related-mosaic-item-full--1">
          <div class="pp-related-image-wrapper">
            <img src="<?php echo esc_url(sgh_img('2020/12/Amdang-Typhoon-bep-chinh-e1631096489893.jpg')); ?>" alt="<?php echo esc_attr('Bản vẽ bố trí thiết bị khu bếp chính Amdang Typhoon - Saigon Horeca'); ?>" loading="lazy" decoding="async">
          </div>
          <span class="pp-related-label"><?php echo esc_html('BẢN VẼ BẾP CHÍNH (QUY HOẠCH CHI TIẾT)'); ?></span>
        </div>

        <!-- Hàng dưới: 2 ảnh chia đôi song song -->
        <div class="pp-related-mosaic-row-sub">
          <!-- Ảnh 2: Bản vẽ quầy bar -->
          <div class="pp-related-mosaic-item-full pp-related-mosaic-item-full--2">
            <div class="pp-related-image-wrapper">
              <img src="<?php echo esc_url(sgh_img('2020/12/am-bv-bar.JPG-e1631162242378.png')); ?>" alt="<?php echo esc_attr('Bản vẽ bố trí thiết bị quầy bar Amdang Typhoon - Saigon Horeca'); ?>" loading="lazy" decoding="async">
            </div>
            <span class="pp-related-label"><?php echo esc_html('BẢN VẼ CHI TIẾT QUẦY BAR'); ?></span>
          </div>

          <!-- Ảnh 3: Thực tế quầy bar -->
          <div class="pp-related-mosaic-item-full pp-related-mosaic-item-full--3">
            <div class="pp-related-image-wrapper">
              <img src="<?php echo esc_url(sgh_img('elementor/thumbs/sgh-AmDang-Bar-1-qkj06csy5c0ubt68v6tp7bqvzi6obm2rovt33a7o20.jpeg')); ?>" alt="<?php echo esc_attr('Thực tế quầy bar lớn nhà hàng Amdang Typhoon - Saigon Horeca'); ?>" loading="lazy" decoding="async">
            </div>
            <span class="pp-related-label"><?php echo esc_html('HOÀN THIỆN THỰC TẾ QUẦY BAR'); ?></span>
          </div>
        </div>

      </div>
    </div>

  </div>
</section>
