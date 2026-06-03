<?php
/**
 * Project Pillar — the-royal-all-day-dining
 * Section #2: INTRO — Editorial Magazine Spread Layout
 * Ảnh full-width bleed + text block overlap kiểu tạp chí kiến trúc ArchDaily
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp-intro-editorial-trd">


  <div class="pp-intro-editorial-trd__grid pp-container-shared">
    
    <!-- Cột Trái: Editorial Typography Content -->
    <div class="pp-intro-editorial-trd__content">

      <div class="pp-intro-editorial-trd__body">
        <h2 class="pp-intro-editorial-trd__title">The Royal</h2>
        <div class="pp-intro-editorial-trd__info">
          <p><strong><?php echo esc_html__('Địa chỉ:', 'saigonhoreca'); ?></strong> <?php echo esc_html__('41-47 Dong Du, Ben Nghe Ward, District 1, Ho Chi Minh City.', 'saigonhoreca'); ?></p>
          <p><strong><?php echo esc_html__('Giờ hoạt động:', 'saigonhoreca'); ?></strong> <?php echo esc_html__('11h00 AM – 11h00 PM (Thứ hai – Thứ bảy)', 'saigonhoreca'); ?></p>
        </div>
        <div class="pp-intro-editorial-trd__columns">
          <p><em><?php echo esc_html__('Không gian nơi đây được trau chuốt kỹ lưỡng đến từng chi tiết, phản ánh gu thẩm mỹ thời thượng và sự chỉn chu tuyệt đối trong thiết kế. The Royal không chạy theo xu hướng ngắn hạn, mà kiến tạo một phong cách kiến trúc riêng – hiện đại, thanh lịch và mang đậm dấu ấn của sự tinh mỹ vượt thời gian.', 'saigonhoreca'); ?></em></p>
        </div>
      </div>
    </div>

    <!-- Cột Phải: Creative Royal Gold Image Frame -->
    <div class="pp-intro-editorial-trd__visual-col">
      <div class="pp-intro-editorial-trd__img-frame">
        <!-- Corner Ornaments (Royal gold) -->
        <div class="pp-intro-editorial-trd__ornament pp-intro-editorial-trd__ornament--tl" aria-hidden="true"></div>
        <div class="pp-intro-editorial-trd__ornament pp-intro-editorial-trd__ornament--tr" aria-hidden="true"></div>
        <div class="pp-intro-editorial-trd__ornament pp-intro-editorial-trd__ornament--bl" aria-hidden="true"></div>
        <div class="pp-intro-editorial-trd__ornament pp-intro-editorial-trd__ornament--br" aria-hidden="true"></div>

        <div class="pp-intro-editorial-trd__img-wrap pp-image-container-shared">
          <img src="<?php echo sgh_img('the-royal-all-day-dining/the-royal-all-day-dining-quay-bep-mo-gach-xanh-fujimak.webp'); ?>" alt="<?php echo esc_attr__('Quầy bếp mở ốp gạch xanh lá rêu đặc trưng với lò nướng Fujimak và dãy ghế bar bọc vải tweed nâu ấm cúng tại The Royal All Day Dining', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
          <div class="pp-image-caption-shared"><?php echo esc_html__('Quầy bếp mở ốp gạch xanh Fujimak · The Royal All Day Dining', 'saigonhoreca'); ?></div>
        </div>

        <div class="pp-intro-editorial-trd__spec-footer">
          <span>OPEN_KITCHEN_BAR_TRD</span>
          <span>SCALE: 1:20</span>
        </div>
      </div>
    </div>

  </div>

  <div class="pp-intro-editorial-trd__statement pp-container-shared">
    <div class="pp-intro-editorial-trd__pull-quote">
      <span class="pp-intro-editorial-trd__quote-mark" aria-hidden="true">&ldquo;</span>
      <h2 class="pp-text-trd__title"><?php echo esc_html__('The Royal – Khi vẻ đẹp chỉn chu tạo nên sự khác biệt', 'saigonhoreca'); ?></h2>
    </div>
    <div class="pp-intro-editorial-trd__essay">
      <p><em><?php echo esc_html__('Nằm ngay trung tâm thành phố, THE ROYAL ALL DAY DINING là điểm hẹn lý tưởng dành cho những ai yêu thích sự tinh tế trong ẩm thực. Tại đây, mỗi món ăn là một sự kết hợp khéo léo giữa kỹ thuật ẩm thực hiện đại phương Tây và những giá trị truyền thống sâu sắc của ẩm thực Việt Nam. Mặt tiền được thiết kế với tông màu gỗ sáng, khung cửa kính tối màu, và những chi tiết vuông vức dứt khoát – tất cả gợi lên một sự gần gũi tự nhiên, như lời mời gọi bước vào một không gian không chỉ để ăn uống, mà còn để tạm dừng, tận hưởng, và kết nối.', 'saigonhoreca'); ?></em></p>
    </div>
  </div>
</section>
