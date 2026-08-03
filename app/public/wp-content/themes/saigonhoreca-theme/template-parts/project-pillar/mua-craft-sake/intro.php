<?php
/**
 * Project Pillar — mua-craft-sake
 * Section #2: intro — editorial storytelling + 4-image food gallery.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
?>
<section class="pp-section-mua pp-section-mua--alt pp-intro-mua">
  <div class="pp-glow-mua pp-glow-mua--tr" aria-hidden="true"></div>
  <div class="pp-watermark-mua pp-watermark-mua--num" aria-hidden="true">01</div>
  <div class="pp-container-shared">
  <div class="pp-intro-mua__head">
  <span class="pp-badge-mua">
  <span class="pp-badge-mua__mark" aria-hidden="true">酒</span>
  <?php echo esc_html__('07 Lê Ngô Cát, Quận 3, HCM', 'saigonhoreca'); ?>
  </span>
  <h2 class="pp-text-mua__title pp-intro-mua__title"><?php echo esc_html__('Mùa Craft Sake', 'saigonhoreca'); ?></h2>
  <span class="pp-text-mua__divider" aria-hidden="true"></span>
  </div>

  <div class="pp-intro-mua__lead">
  <p class="pp-intro-mua__addr"><?php echo esc_html__('07 Lê Ngô Cát, Quận 3, HCM, Việt Nam', 'saigonhoreca'); ?></p>
  <div class="pp-text-mua__body">
  <p><?php echo esc_html__('Ghé thăm Mùa Sake, tọa lạc tại vị trí sôi động tại 07 Lê Ngô Cát, Quận 3, TP.HCM, và đắm mình trong thế giới hòa quyện giữa thiết kế hoàn hảo, ẩm thực tinh tế và đồ uống quyến rũ. Mỗi chi tiết tại Mùa Craft Sake, từ bầu không khí đầy phong cách đến những món ăn và thức uống hấp dẫn, đều được tỉ mỉ chăm chút để mang đến cho bạn trải nghiệm ẩm thực khó quên.', 'saigonhoreca'); ?></p>
  <p><?php echo esc_html__('Mùa Craft Sake tự hào với thiết kế nội thất cuốn hút, nơi hòa quyện giữa thiết kế hiện đại và tinh hoa ẩm thực Nhật Bản. Bầu không khí ấm áp, mời gọi của Mùa Sake được tạo nên bởi những bức tường gỗ nâu trầm ấm, những chiếc đèn thả trần tinh tế và những bộ bàn ghế gỗ đơn giản. Từng chi tiết nhỏ trong thiết kế đều được chăm chút tỉ mỉ, mang đến cho thực khách cảm giác thoải mái và thư giãn.', 'saigonhoreca'); ?></p>
  <p><?php echo esc_html__('Mùa Craft Sake không chỉ là một nhà hàng, mà còn là một chuyến du lịch ẩm thực đầy thú vị. Bước vào Mùa, bạn sẽ cảm thấy như đang bước lên một con thuyền sang trọng, sẵn sàng cho một hành trình khám phá những hương vị mới lạ. Tại Mùa, bạn sẽ được thưởng thức những món ăn truyền thống Nhật Bản được chế biến từ những nguyên liệu tươi ngon, kết hợp với những hương vị mới lạ, mang đậm bản sắc Việt Nam. Các món ăn được trình bày đẹp mắt, tinh tế, mang đến cho bạn cảm giác ngon miệng và thích thú. Đến với Mùa Sake, bạn sẽ được trải nghiệm một hành trình ẩm thực đầy thú vị, nơi hương vị và nghệ thuật hòa quyện cùng nhau.', 'saigonhoreca'); ?></p>
  </div>
  </div>

  <div class="pp-gallery-mua pp-gallery-mua--cols-4">
  <figure class="pp-gallery-mua__item pp-image-container-shared">
  <img src="<?php echo sgh_img('mua-craft-sake-lam-ruou-sake-dau-tien-tai-viet-nam/mua-craft-sake-mon-hau-song-long-do-trung-da-muoi.webp'); ?>" alt="<?php echo esc_attr__('Hàu nướng trên nền muối hột tại Mùa Craft Sake', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
  <figcaption class="pp-image-caption-shared"><?php echo esc_html__('Hàu tươi nướng phô mai trên nền muối hột — món khai vị izakaya đậm vị biển của Mùa Sake.', 'saigonhoreca'); ?></figcaption>
  </figure>
  <figure class="pp-gallery-mua__item pp-image-container-shared">
  <img src="<?php echo sgh_img('mua-craft-sake-lam-ruou-sake-dau-tien-tai-viet-nam/mua-craft-sake-sashimi-ca-ngu-beo-kem-sake.webp'); ?>" alt="<?php echo esc_attr__('Sashimi cá ngừ béo và chai Mùa Craft Sake', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
  <figcaption class="pp-image-caption-shared"><?php echo esc_html__('Sashimi cá ngừ béo trên lá tre, kề bên chai Mùa Craft Sake — cặp đôi hoàn hảo của sashimi và sake thủ công.', 'saigonhoreca'); ?></figcaption>
  </figure>
  <figure class="pp-gallery-mua__item pp-image-container-shared">
  <img src="<?php echo sgh_img('mua-craft-sake-lam-ruou-sake-dau-tien-tai-viet-nam/mua-craft-sake-mon-com-kep-onigirazu-thit-bo-trung.webp'); ?>" alt="<?php echo esc_attr__('Burger cơm rong biển (onigirazu) tại Mùa Craft Sake', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
  <figcaption class="pp-image-caption-shared"><?php echo esc_html__('Onigirazu kiểu burger — cơm, rong biển nori và nhân thịt bò mọng, biến tấu sáng tạo từ ẩm thực Nhật.', 'saigonhoreca'); ?></figcaption>
  </figure>
  <figure class="pp-gallery-mua__item pp-image-container-shared">
  <img src="<?php echo sgh_img('mua-craft-sake-lam-ruou-sake-dau-tien-tai-viet-nam/mua-craft-sake-mon-sup-ham-kieu-nhat-tho-su.webp'); ?>" alt="<?php echo esc_attr__('Tô súp Nhật trong tô gốm có nắp tại Mùa Craft Sake', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
  <figcaption class="pp-image-caption-shared"><?php echo esc_html__('Súp Nhật trong tô gốm hoa văn, rắc hành lá thơm — món nóng ấm bụng giữa hành trình izakaya.', 'saigonhoreca'); ?></figcaption>
  </figure>
  </div>
  </div>
</section>
