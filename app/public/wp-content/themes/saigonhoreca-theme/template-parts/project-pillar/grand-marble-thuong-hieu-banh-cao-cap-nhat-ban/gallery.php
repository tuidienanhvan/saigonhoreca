<?php
/**
 * Project Pillar — grand-marble-thuong-hieu-banh-cao-cap-nhat-ban
 * Section #6: gallery — metro image grid + signature-flavour split + closing block.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-gmarb pp-gallery-section-gmarb scroll-reveal">
  <div class="pp-gmarb-glow pp-gmarb-glow--bl" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <div class="pp-gallery-gmarb__head scroll-reveal reveal-up">
      <span class="pp-text-gmarb__eyebrow"><?php echo esc_html__('Thư viện dự án', 'saigonhoreca'); ?></span>
      <div class="pp-text-gmarb__divider pp-text-gmarb__divider--dots pp-text-gmarb__divider--center" aria-hidden="true"></div>
      <h2 class="pp-text-gmarb__title"><?php echo esc_html__('Hành Trình Của Một Chiếc Bánh', 'saigonhoreca'); ?></h2>
    </div>

    <div class="pp-gallery-gmarb">
      <figure class="pp-gallery-gmarb__item pp-gallery-gmarb__item--wide scroll-reveal reveal-up">
        <div class="pp-image-container-shared">
          <img src="<?php echo sgh_img('grand-marble-thuong-hieu-banh-cao-cap-nhat-ban/grand-marble-hoa-van-marble.jpg'); ?>" alt="<?php echo esc_attr__('Hoa văn vân đá marble trên bánh Grand Marble', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
          <figcaption class="pp-image-caption-shared"><?php echo esc_html__('Các họa tiết vân đá marble đặc trưng – kết quả của tài năng và sự tận tụy của những nghệ nhân làm bánh.', 'saigonhoreca'); ?></figcaption>
        </div>
      </figure>
      <figure class="pp-gallery-gmarb__item pp-gallery-gmarb__item--tall scroll-reveal reveal-up delay-100">
        <div class="pp-image-container-shared">
          <img src="<?php echo sgh_img('grand-marble-thuong-hieu-banh-cao-cap-nhat-ban/grand-marble-lo-nuong-chanmag.jpg'); ?>" alt="<?php echo esc_attr__('Lò nướng Chanmag tại khu làm bánh Grand Marble', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
          <figcaption class="pp-image-caption-shared"><?php echo esc_html__('Khu Làm Bánh với tủ ủ bột, lò nướng Chanmag và bàn inox mặt đá hoa cương cho các thao tác tạo hình.', 'saigonhoreca'); ?></figcaption>
        </div>
      </figure>
      <figure class="pp-gallery-gmarb__item scroll-reveal reveal-up delay-150">
        <div class="pp-image-container-shared">
          <img src="<?php echo sgh_img('grand-marble-thuong-hieu-banh-cao-cap-nhat-ban/grand-marble-ban-thao-tac.jpg'); ?>" alt="<?php echo esc_attr__('Bàn thao tác inox khu kiểm tra đóng gói Grand Marble', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
          <figcaption class="pp-image-caption-shared"><?php echo esc_html__('Khu Kiểm Tra và Đóng Gói thủ công với bồn rửa, máy cắt bánh và bàn inox, hoàn thiện từng hộp bánh.', 'saigonhoreca'); ?></figcaption>
        </div>
      </figure>
      <figure class="pp-gallery-gmarb__item scroll-reveal reveal-up delay-200">
        <div class="pp-image-container-shared">
          <img src="<?php echo sgh_img('grand-marble-thuong-hieu-banh-cao-cap-nhat-ban/grand-marble-ban-ve-khu-vuc-3.png'); ?>" alt="<?php echo esc_attr__('Bản vẽ bố trí khu vực chức năng phòng làm bánh Grand Marble', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
          <figcaption class="pp-image-caption-shared"><?php echo esc_html__('Bản vẽ kỹ thuật khu vực chức năng, cơ sở để thi công và lắp đặt thiết bị đúng chuẩn vận hành.', 'saigonhoreca'); ?></figcaption>
        </div>
      </figure>
    </div>
  </div>
</section>

<section class="pp-section-gmarb pp-section-gmarb--alt scroll-reveal">
  <div class="pp-gmarb-glow pp-gmarb-glow--tr" aria-hidden="true"></div>
  <div class="pp-container-shared">
    <div class="pp-split-gmarb pp-split-gmarb--reverse">
      <div class="pp-split-gmarb__media scroll-reveal reveal-right">
        <div class="pp-image-container-shared pp-frame-gmarb">
          <img src="<?php echo sgh_img('grand-marble-thuong-hieu-banh-cao-cap-nhat-ban/grand-marble-chocolate-marou.jpg'); ?>" alt="<?php echo esc_attr__('Bộ ba hương vị chocolate Marou độc quyền của Grand Marble Việt Nam', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="800" height="600">
          <figcaption class="pp-image-caption-shared"><?php echo esc_html__('Bộ ba hương vị độc quyền sử dụng chocolate Marou thượng hạng – dấu ấn riêng của Grand Marble tại Việt Nam.', 'saigonhoreca'); ?></figcaption>
        </div>
      </div>
      <div class="pp-split-gmarb__body scroll-reveal reveal-left delay-150">
        <div class="pp-gmarb-ornament pp-gmarb-ornament--right" aria-hidden="true"></div>
        <span class="pp-text-gmarb__eyebrow"><?php echo esc_html__('Hương vị Việt Nam', 'saigonhoreca'); ?></span>
        <div class="pp-text-gmarb__divider pp-text-gmarb__divider--dots" aria-hidden="true"></div>
        <h2 class="pp-text-gmarb__title"><?php echo esc_html__('Dấu Ấn Marou Trên Bánh Danish Marble', 'saigonhoreca'); ?></h2>
        <div class="pp-text-gmarb__body">
          <p><?php echo esc_html__('Bên cạnh những hương vị truyền thống, Grand Marble mang đến các hương vị riêng biệt cho từng quốc gia. Tại Việt Nam, thương hiệu thu hút thực khách với bộ ba hương vị độc quyền sử dụng chocolate Marou thượng hạng: Chocolate-Chocolate, Green Tea Chocolate và Orange Mango Chocolate – thực sự làm ấn tượng vị giác.', 'saigonhoreca'); ?></p>
        </div>
        <div class="pp-concept-gmarb__signals" aria-label="<?php echo esc_attr__('Bộ ba hương vị chocolate Marou', 'saigonhoreca'); ?>">
          <span><?php echo esc_html__('Chocolate-Chocolate', 'saigonhoreca'); ?></span>
          <span><?php echo esc_html__('Green Tea Chocolate', 'saigonhoreca'); ?></span>
          <span><?php echo esc_html__('Orange Mango', 'saigonhoreca'); ?></span>
        </div>
      </div>
    </div>
  </div>
</section>

<?php /* Closing block — đúc kết câu chuyện dự án (chuẩn T-034: gộp vào cuối gallery). */ ?>
<section class="pp-section-gmarb pp-gallery-gmarb__closing scroll-reveal">
  <div class="pp-gmarb-glow pp-gmarb-glow--bl" aria-hidden="true"></div>
  <div class="pp-container-shared">
    <div class="pp-gallery-gmarb__closing-inner scroll-reveal reveal-up">
      <span class="pp-text-gmarb__eyebrow"><?php echo esc_html__('Lời kết', 'saigonhoreca'); ?></span>
      <div class="pp-text-gmarb__divider pp-text-gmarb__divider--dots pp-text-gmarb__divider--center" aria-hidden="true"></div>
      <h2 class="pp-text-gmarb__title"><?php echo esc_html__('Tận Tâm Trong Từng Chiếc Bánh', 'saigonhoreca'); ?></h2>
      <div class="pp-text-gmarb__body pp-text-gmarb__body--center">
        <p><?php echo esc_html__('Với hơn 7 năm kinh nghiệm trong ngành F&B, Saigon Horeca tự hào đồng hành cùng nhiều dự án thiết kế và xây dựng quầy bar, phòng làm bánh, bếp công nghiệp và bếp nhà hàng. Thiết bị chúng tôi cung cấp đến từ các thương hiệu uy tín thế giới, cùng các thiết bị inox chất lượng cao do chính Saigon Horeca thiết kế và sản xuất.', 'saigonhoreca'); ?></p>
        <p><?php echo esc_html__('Uy tín và chất lượng sản phẩm luôn là kim chỉ nam hàng đầu của chúng tôi – mỗi thiết kế đều thể hiện sự tận tâm trong việc phục vụ khách hàng, tối ưu hóa quy trình vận hành và giảm thiểu chi phí đầu tư.', 'saigonhoreca'); ?></p>
      </div>
    </div>
  </div>
</section>
