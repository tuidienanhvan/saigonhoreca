<?php
/**
 * Project Pillar — grand-marble-thuong-hieu-banh-cao-cap-nhat-ban
 * Section #6: gallery — metro image grid + signature-flavour split + closing block.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-gmarb pp-gallery-section-gmarb">
  <div class="pp-gmarb-glow pp-gmarb-glow--bl" aria-hidden="true"></div>
  <div class="pp-gmarb-glow pp-gmarb-glow--tr" aria-hidden="true"></div>

  <div class="pp-container-shared">
  <div class="pp-gallery-gmarb__head">
  <span class="pp-text-gmarb__eyebrow"><?php echo esc_html__('Thư viện dự án', 'saigonhoreca'); ?></span>
  <div class="pp-text-gmarb__divider pp-text-gmarb__divider--dots pp-text-gmarb__divider--center" aria-hidden="true"></div>
  <h2 class="pp-text-gmarb__title"><?php echo esc_html__('Hành Trình Của Một Chiếc Bánh', 'saigonhoreca'); ?></h2>
  </div>

  <div class="pp-gallery-gmarb">
  <span class="pp-gallery-gmarb__watermark" aria-hidden="true">04</span>

  <!-- Tile 01 — vân đá marble -->
  <figure class="pp-gallery-gmarb__item">
  <div class="pp-image-container-shared pp-gallery-gmarb__frame">
  <span class="pp-gallery-gmarb__index" aria-hidden="true">01</span>
  <img src="<?php echo sgh_img('grand-marble-thuong-hieu-banh-cao-cap-nhat-ban/grand-marble-hoa-van-marble.jpg'); ?>" alt="<?php echo esc_attr__('Hai phụ nữ mặc kimono trước cửa hàng Grand Marble phong cách Kyoto truyền thống', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
  <figcaption class="pp-image-caption-shared"><?php echo esc_html__('Mặt tiền cửa hàng Grand Marble giữa lòng cố đô Kyoto, nơi rèm noren và đèn lồng đỏ gìn giữ nét truyền thống Nhật Bản trong từng chiếc bánh.', 'saigonhoreca'); ?></figcaption>
  </div>
  </figure>

  <!-- Tile 02 — hộp quà & các vị Danish Marble -->
  <figure class="pp-gallery-gmarb__item">
  <div class="pp-image-container-shared pp-gallery-gmarb__frame">
  <span class="pp-gallery-gmarb__index" aria-hidden="true">02</span>
  <img src="<?php echo sgh_img('grand-marble-thuong-hieu-banh-cao-cap-nhat-ban/grand-marble-original-taste.webp'); ?>" alt="<?php echo esc_attr__('Các lát Danish Marble nhiều vị bên hai hộp quà cam và champagne Grand Marble', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
  <figcaption class="pp-image-caption-shared"><?php echo esc_html__('Bộ sưu tập Danish Marble nhiều hương vị khoe vân ruột óng ả bên hai hộp quà sắc cam và champagne – thành phẩm hoàn thiện sẵn sàng trao tay thực khách.', 'saigonhoreca'); ?></figcaption>
  </div>
  </figure>

  <!-- Tile 03 — bàn thao tác inox -->
  <figure class="pp-gallery-gmarb__item">
  <div class="pp-image-container-shared pp-gallery-gmarb__frame">
  <span class="pp-gallery-gmarb__index" aria-hidden="true">03</span>
  <img src="<?php echo sgh_img('grand-marble-thuong-hieu-banh-cao-cap-nhat-ban/grand-marble-ban-thao-tac.jpg'); ?>" alt="<?php echo esc_attr__('Bàn thao tác inox khu kiểm tra đóng gói Grand Marble', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
  <figcaption class="pp-image-caption-shared"><?php echo esc_html__('Bàn thao tác inox mặt trắng cùng kệ chứa và xe giá nhiều tầng – không gian sạch sẽ, vững chãi cho công đoạn hoàn thiện và đóng gói bánh.', 'saigonhoreca'); ?></figcaption>
  </div>
  </figure>

  <!-- Tile 04 — bản vẽ kỹ thuật -->
  <figure class="pp-gallery-gmarb__item">
  <div class="pp-image-container-shared pp-gallery-gmarb__frame">
  <span class="pp-gallery-gmarb__index" aria-hidden="true">04</span>
  <img src="<?php echo sgh_img('grand-marble-thuong-hieu-banh-cao-cap-nhat-ban/grand-marble-ban-ve-mat-bang-khu-lo-banh-phong-lam-mat.webp'); ?>" alt="<?php echo esc_attr__('Bản vẽ bố trí khu vực chức năng phòng làm bánh Grand Marble', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
  <figcaption class="pp-image-caption-shared"><?php echo esc_html__('Bản vẽ kỹ thuật khu vực chức năng, cơ sở để thi công và lắp đặt thiết bị đúng chuẩn vận hành.', 'saigonhoreca'); ?></figcaption>
  </div>
  </figure>
  </div>
  </div>
</section>

<section class="pp-section-gmarb pp-section-gmarb--alt">
  <div class="pp-gmarb-glow pp-gmarb-glow--tr" aria-hidden="true"></div>
  <div class="pp-container-shared">
  <div class="pp-split-gmarb pp-split-gmarb--reverse">
  <div class="pp-split-gmarb__media">
  <div class="pp-image-container-shared pp-frame-gmarb">
  <img src="<?php echo sgh_img('grand-marble-thuong-hieu-banh-cao-cap-nhat-ban/grand-marble-chocolate-marou.jpg'); ?>" alt="<?php echo esc_attr__('Hai ổ Danish Marble cắt đôi vị chocolate và caramel cùng hạnh nhân, caramel của Grand Marble', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="800" height="600">
  <figcaption class="pp-image-caption-shared"><?php echo esc_html__('Hai ổ Danish Marble cắt đôi khoe vân ruột óng ả – một vị chocolate điểm hạnh nhân, một vị caramel ngọt ngào, tinh tuý hương vị Grand Marble.', 'saigonhoreca'); ?></figcaption>
  </div>
  </div>
  <div class="pp-split-gmarb__body">
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
<section class="pp-section-gmarb pp-gallery-gmarb__closing">
  <div class="pp-gmarb-glow pp-gmarb-glow--bl" aria-hidden="true"></div>
  <div class="pp-container-shared">
  <div class="pp-gallery-gmarb__closing-inner">
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
