<?php
/**
 * Project Pillar — du-an-bep-cang-tin-cong-ty-nhat-nichiyo
 * Section #6: gallery — GOLD editorial grid (uniform 4/3 tiles) + closing đúc kết statement.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-nichiyo pp-gallery-nichiyo-section">
  <div class="pp-nichiyo-glow pp-nichiyo-glow--tr" aria-hidden="true"></div>
  <div class="pp-nichiyo-glow pp-nichiyo-glow--bl" aria-hidden="true"></div>

  <div class="pp-container-shared">

  <!-- Editorial mosaic header — badge + title + tatami divider -->
  <div class="pp-gallery-nichiyo-mosaic__head">
  <span class="pp-gallery-nichiyo-mosaic__badge">
  <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
  <path d="M12 2L14.85 9.15L22 12L14.85 14.85L12 22L9.15 14.85L2 12L9.15 9.15L12 2Z"/>
  </svg>
  <?php echo esc_html__('Thư viện dự án', 'saigonhoreca'); ?>
  </span>
  <span class="pp-text-nichiyo__divider pp-text-nichiyo__divider--center" aria-hidden="true"></span>
  <h2 class="pp-text-nichiyo__title"><?php echo esc_html__('Toàn cảnh không gian bếp & sản phẩm Nichiyo', 'saigonhoreca'); ?></h2>
  <div class="pp-text-nichiyo__body">
  <p><?php echo esc_html__('Một bộ sưu tập trực quan ghi lại sự khéo léo trong bố trí, độ hoàn thiện của thiết bị inox và tinh thần Monozukuri xuyên suốt dự án.', 'saigonhoreca'); ?></p>
  </div>
  </div>

  <!-- GOLD UNIFORM GRID — equal 4/3 tiles, faint numeric watermark -->
  <div class="pp-gallery-nichiyo-mosaic">
  <span class="pp-gallery-nichiyo-mosaic__watermark" aria-hidden="true">06</span>

  <!-- Toàn cảnh bếp hoàn thiện -->
  <div class="pp-image-container-shared pp-gallery-nichiyo-mosaic__item">
  <img src="<?php echo sgh_img('du-an-bep-cang-tin-cong-ty-nhat-nichiyo/nichiyo-tu-luu-tru-inox-cong-nghiep.webp'); ?>" alt="<?php echo esc_attr__('Cận cảnh tủ lưu trữ inox công nghiệp cửa lùa và cửa mở trong bếp căng tin Nichiyo do Saigon Horeca thi công', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
  <div class="pp-image-caption-shared"><?php echo esc_html__('Toàn cảnh bếp căng tin hoàn thiện — minh chứng cho sự tận tâm và chuyên nghiệp của Saigon Horeca.', 'saigonhoreca'); ?></div>
  </div>

  <!-- Sản phẩm thực phẩm Nichiyo -->
  <div class="pp-image-container-shared pp-gallery-nichiyo-mosaic__item">
  <img src="<?php echo sgh_img('du-an-bep-cang-tin-cong-ty-nhat-nichiyo/nichiyo-day-chuyen-che-bien-thuc-pham-cong-nhan.webp'); ?>" alt="<?php echo esc_attr__('Công nhân mặc đồ bảo hộ xếp món chiên vào khay trên dây chuyền băng tải inox trong nhà xưởng chế biến thực phẩm Nichiyo', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="600" height="800">
  <div class="pp-image-caption-shared"><?php echo esc_html__('Mỗi sản phẩm phản ánh tinh thần Monozukuri — chế tác tỉ mỉ và cam kết với chất lượng cuộc sống.', 'saigonhoreca'); ?></div>
  </div>

  <!-- Bố trí khu bếp -->
  <div class="pp-image-container-shared pp-gallery-nichiyo-mosaic__item">
  <img src="<?php echo sgh_img('du-an-bep-cang-tin-cong-ty-nhat-nichiyo/nichiyo-ke-inox-tam-op-tuong-may-hut-mui-bep.webp'); ?>" alt="<?php echo esc_attr__('Kệ inox treo tường nhiều tầng, tấm ốp tường inox và máy hút mùi trong bếp công ty Nichiyo do Saigon Horeca thi công', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
  <div class="pp-image-caption-shared"><?php echo esc_html__('Bố trí khu bếp gọn gàng với tông trắng – xám trung tính, tối ưu luồng thao tác của đầu bếp.', 'saigonhoreca'); ?></div>
  </div>

  <!-- Thiết bị inox & hệ thống thông gió -->
  <div class="pp-image-container-shared pp-gallery-nichiyo-mosaic__item">
  <img src="<?php echo sgh_img('du-an-bep-cang-tin-cong-ty-nhat-nichiyo/nichiyo-bon-rua-inox-goc-bep-cong-nghiep.webp'); ?>" alt="<?php echo esc_attr__('Bồn rửa inox âm bàn tại góc bếp công nghiệp Nichiyo với tấm chắn tường inox đục lỗ và vòi gắn máy lọc nước', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
  <div class="pp-image-caption-shared"><?php echo esc_html__('Thiết bị inox và hệ thống thông gió được thiết kế tỉ mỉ trong không gian bếp hạn chế.', 'saigonhoreca'); ?></div>
  </div>

  <!-- Sản phẩm thực phẩm Nichiyo -->
  <div class="pp-image-container-shared pp-gallery-nichiyo-mosaic__item">
  <img src="<?php echo sgh_img('du-an-bep-cang-tin-cong-ty-nhat-nichiyo/nichiyo-mon-an-che-bien-san-kieu-nhat.webp'); ?>" alt="<?php echo esc_attr__('Các món ăn chế biến sẵn kiểu Nhật: cơm nắm onigiri đóng gói, mì Ý carbonara, thịt nướng sốt teriyaki và cơm cà ri', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="600" height="800">
  <div class="pp-image-caption-shared"><?php echo esc_html__('Danh mục đa dạng từ khắp nơi trên thế giới, mang đến trải nghiệm ẩm thực sáng tạo và độc đáo.', 'saigonhoreca'); ?></div>
  </div>

  <!-- Sản phẩm thực phẩm Nichiyo -->
  <div class="pp-image-container-shared pp-gallery-nichiyo-mosaic__item">
  <img src="<?php echo sgh_img('du-an-bep-cang-tin-cong-ty-nhat-nichiyo/nichiyo-nhan-vien-chien-thuc-pham-bon-chien-inox.webp'); ?>" alt="<?php echo esc_attr__('Nhân viên đeo găng tay hồng chiên miếng tẩm bột tại bồn chiên inox công nghiệp bếp căng tin Nichiyo', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="600" height="800">
  <div class="pp-image-caption-shared"><?php echo esc_html__('Dòng sản phẩm thực phẩm tuyển chọn của Nichiyo, đại diện cho tiêu chuẩn chất lượng và an toàn nghiêm ngặt.', 'saigonhoreca'); ?></div>
  </div>

  </div>

  <!-- Closing editorial statement (kept from previous merged closing content) -->
  <div class="pp-gallery-nichiyo__closing">
  <div class="pp-gallery-nichiyo__closing-body">
  <span class="pp-text-nichiyo__divider" aria-hidden="true"></span>
  <h2 class="pp-text-nichiyo__title"><?php echo esc_html__('Một môi trường làm việc đơn giản, hiệu quả và truyền cảm hứng', 'saigonhoreca'); ?></h2>
  <div class="pp-text-nichiyo__body">
  <p><?php echo esc_html__('Sự khéo léo và tinh tế trong bố trí không chỉ làm cho không gian bếp gọn gàng, tiện nghi mà còn nâng cao hiệu suất và sự thoải mái cho các đầu bếp. Tất cả kết hợp tạo nên một môi trường làm việc đơn giản, hiệu quả và đầy động lực cho đội ngũ Nichiyo.', 'saigonhoreca'); ?></p>
  </div>
  </div>
  </div>

  </div>
</section>
