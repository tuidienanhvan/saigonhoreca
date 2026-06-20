<?php
/**
 * Project Pillar — mua-craft-sake-lam-ruou-sake-dau-tien-tai-viet-nam
 * Section #6: gallery — tasting-room split + GOLD ASYMMETRIC MOSAIC + lời kết.
 * (T-034: gộp nội dung related/cta cũ vào cuối gallery.)
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
?>
<section class="pp-section-mua pp-section-mua--alt pp-gallery-sec-mua">
  <div class="pp-glow-mua pp-glow-mua--bl" aria-hidden="true"></div>
  <div class="pp-watermark-mua pp-watermark-mua--kanji" aria-hidden="true">味</div>
  <div class="pp-container-shared">
    <div class="pp-split-mua">
      <div class="pp-split-mua__body scroll-reveal reveal-right-short duration-1000">
        <div class="pp-card-mua">
          <span class="pp-badge-mua">
            <span class="pp-badge-mua__mark" aria-hidden="true">酒</span>
            <?php echo esc_html__('Phòng nếm rượu sake', 'saigonhoreca'); ?>
          </span>
          <h2 class="pp-text-mua__title"><?php echo esc_html__('Trải nghiệm nếm rượu không thể quên', 'saigonhoreca'); ?></h2>
          <span class="pp-text-mua__divider" aria-hidden="true"></span>
          <div class="pp-text-mua__body">
            <p><?php echo esc_html__('Trải nghiệm nếm rượu không thể quên: Tại Mùa Sake, bạn sẽ được đắm mình trong không gian phòng nếm rượu tĩnh lặng, nơi bầu không khí lý tưởng để khám phá thế giới phức tạp của Sake. Hãy lắng nghe bản hoà tấu của hương thơm, vị ngọt, và kết cấu khi thưởng thức những loại sake hảo hạng của chúng tôi. Menu nếm rượu theo set của chúng tôi mang đến cơ hội so sánh và khám phá các phong cách sake khác nhau. Đội ngũ nhân viên thân thiện và am hiểu sake luôn sẵn sàng tư vấn, đưa bạn lên hành trình khám phá sake khó quên.', 'saigonhoreca'); ?></p>
            <p><?php echo esc_html__('Mùa Sake nâng tầm trải nghiệm ẩm thực của bạn với sự kết hợp giữa rượu Sake và món ăn được trau chuốt tỉ mỉ. Thực đơn của nhà hàng mang đến nhiều lựa chọn món ngon được chế biến khéo léo để tôn lên từng hương vị của các loại Sake. Từ sushi và sashimi tươi ngon đến yakitori thơm nức và tempura giòn tan, mỗi sáng tạo ẩm thực đều hòa quyện tinh tế với các loại Sake thủ công của chúng tôi, tạo nên một bản hòa tấu hương vị khó cưỡng.', 'saigonhoreca'); ?></p>
          </div>
        </div>
      </div>
      <div class="pp-split-mua__media scroll-reveal reveal-left-short duration-1000 delay-150">
        <figure class="pp-image-container-shared pp-frame-mua">
          <img src="<?php echo sgh_img('mua-craft-sake-lam-ruou-sake-dau-tien-tai-viet-nam/mua-craft-sake-people.jpg'); ?>" alt="<?php echo esc_attr__('Đội ngũ ủ rượu Mùa Craft Sake bên bồn lên men inox', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="600" height="800">
          <figcaption class="pp-image-caption-shared"><?php echo esc_html__('Đội ngũ ủ rượu Mùa Craft Sake bên dàn bồn lên men inox — những bàn tay làm nên từng mẻ sake thủ công.', 'saigonhoreca'); ?></figcaption>
        </figure>
      </div>
    </div>
  </div>
</section>

<section class="pp-section-mua pp-gallery-sec-mua">
  <!-- Ambient sake-glow + số chìm kỹ thuật 04 -->
  <div class="pp-glow-mua pp-glow-mua--tr" aria-hidden="true"></div>
  <div class="pp-watermark-mua pp-watermark-mua--num" aria-hidden="true">04</div>

  <!-- Đại tự SVG mạ vàng chìm trang trí (sake = 酒) -->
  <svg class="pp-mosaic-mua__sigil" viewBox="0 0 120 120" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
    <circle cx="60" cy="60" r="54" stroke="var(--mua-gold)" stroke-width="0.6" stroke-dasharray="2 4"/>
    <circle cx="60" cy="60" r="40" stroke="var(--mua-gold)" stroke-width="0.6"/>
    <path d="M60 18 L60 102 M30 42 L90 42 M36 70 L84 70" stroke="var(--mua-gold)" stroke-width="0.8" stroke-linecap="round"/>
  </svg>

  <div class="pp-container-shared">
    <header class="pp-mosaic-mua__head scroll-reveal reveal-up-short duration-800">
      <span class="pp-badge-mua">
        <span class="pp-badge-mua__mark" aria-hidden="true">酒</span>
        <?php echo esc_html__('Thiết bị bếp & gian bếp hoàn thiện', 'saigonhoreca'); ?>
      </span>
      <h2 class="pp-text-mua__title"><?php echo esc_html__('Gian bếp izakaya chuẩn vận hành chuyên nghiệp', 'saigonhoreca'); ?></h2>
      <span class="pp-text-mua__divider pp-text-mua__divider--center" aria-hidden="true"></span>
    </header>

    <!-- UNIFORM GRID — 6 ảnh thật trên khung & chú thích chia sẻ, mỗi ô 4/3 -->
    <div class="pp-mosaic-mua">

      <!-- Tile 1 — hệ thống thiết bị -->
      <figure class="pp-image-container-shared pp-mosaic-mua__item scroll-reveal reveal-up duration-1000">
        <img src="<?php echo sgh_img('mua-craft-sake-lam-ruou-sake-dau-tien-tai-viet-nam/mua-craft-sake-quay-bar-voi-rot-sake-bon-u-inox.webp'); ?>" alt="<?php echo esc_attr__('Quầy bar rót sake với bồn inox và dàn vòi rót tại Mùa Craft Sake', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
        <figcaption class="pp-image-caption-shared"><?php echo esc_html__('Quầy bar sake với dàn vòi rót và bồn inox — nơi từng mẻ sake thủ công được phục vụ tươi tận ly.', 'saigonhoreca'); ?></figcaption>
      </figure>

      <!-- Tile 2 — bàn inox tuyển chọn -->
      <figure class="pp-image-container-shared pp-mosaic-mua__item scroll-reveal reveal-up duration-1000 delay-100">
        <img src="<?php echo sgh_img('mua-craft-sake-lam-ruou-sake-dau-tien-tai-viet-nam/mua-craft-sake-day-bep-nuong-than-ke-pass-chup-hut.webp'); ?>" alt="<?php echo esc_attr__('Thiết bị bếp inox Saigon Horeca tại Mùa Sake', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
        <figcaption class="pp-image-caption-shared"><?php echo esc_html__('Thiết bị inox bền bỉ do Saigon Horeca tuyển chọn cho gian bếp izakaya.', 'saigonhoreca'); ?></figcaption>
      </figure>

      <!-- Tile 3 — tổng thể gian bếp -->
      <figure class="pp-image-container-shared pp-mosaic-mua__item scroll-reveal reveal-up duration-1000 delay-150">
        <img src="<?php echo sgh_img('mua-craft-sake-lam-ruou-sake-dau-tien-tai-viet-nam/mua-craft-sake-day-bep-nau-gas-chup-hut-cap-khi-tuoi.webp'); ?>" alt="<?php echo esc_attr__('Gian bếp Mùa Craft Sake 01', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
        <figcaption class="pp-image-caption-shared"><?php echo esc_html__('Tổng thể gian bếp hoàn thiện bởi đội ngũ kỹ thuật Saigon Horeca.', 'saigonhoreca'); ?></figcaption>
      </figure>

      <!-- Tile 4 — khu chế biến -->
      <figure class="pp-image-container-shared pp-mosaic-mua__item scroll-reveal reveal-up duration-1000 delay-200">
        <img src="<?php echo sgh_img('mua-craft-sake-lam-ruou-sake-dau-tien-tai-viet-nam/mua-craft-sake-bep-mo-chup-hut-dao-ban-soan-bep-gas.webp'); ?>" alt="<?php echo esc_attr__('Gian bếp Mùa Craft Sake 02', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
        <figcaption class="pp-image-caption-shared"><?php echo esc_html__('Khu vực chế biến gọn gàng, tiện cho từng thao tác của đầu bếp.', 'saigonhoreca'); ?></figcaption>
      </figure>

      <!-- Tile 5 — bàn inox sáng bóng -->
      <figure class="pp-image-container-shared pp-mosaic-mua__item scroll-reveal reveal-up duration-1000 delay-250">
        <img src="<?php echo sgh_img('mua-craft-sake-lam-ruou-sake-dau-tien-tai-viet-nam/mua-craft-sake-he-thong-chup-hut-khoi-ong-cap-khi-tuoi.webp'); ?>" alt="<?php echo esc_attr__('Gian bếp Mùa Craft Sake 03', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
        <figcaption class="pp-image-caption-shared"><?php echo esc_html__('Hệ thống chụp hút khói inox khổ lớn trên tường gạch men — bảo đảm gian bếp izakaya luôn thông thoáng.', 'saigonhoreca'); ?></figcaption>
      </figure>

      <!-- Tile 6 — bố trí khoa học -->
      <figure class="pp-image-container-shared pp-mosaic-mua__item scroll-reveal reveal-up duration-1000 delay-300">
        <img src="<?php echo sgh_img('mua-craft-sake-lam-ruou-sake-dau-tien-tai-viet-nam/mua-craft-sake-bep-mo-nhin-tu-quay-bar-trang-tri-sake-nhat.webp'); ?>" alt="<?php echo esc_attr__('Gian bếp Mùa Craft Sake 04', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
        <figcaption class="pp-image-caption-shared"><?php echo esc_html__('Gian bếp được bố trí khoa học, tối ưu cho từng công đoạn chế biến.', 'saigonhoreca'); ?></figcaption>
      </figure>

    </div>

    <div class="pp-text-mua pp-text-mua--center scroll-reveal reveal-up-short duration-1000">
      <div class="pp-text-mua__body">
        <p><?php echo esc_html__('Từ nguyên liệu tươi mát ngon lành, bàn bếp inox sáng bóng cho đến các thiết bị nấu nướng và pha chế hiện đại, mọi thứ ở Mùa Sake đều thể hiện Saigon Horeca luôn tâm huyết và tận tâm trong việc tạo nên không gian bếp và quầy bar hoàn hảo.', 'saigonhoreca'); ?></p>
        <p><?php echo esc_html__('Với sự chuyên môn của Saigon Horeca trong xây dựng nhà bếp và quầy bar, Mùa Sake không chỉ xuất sắc trong việc cung cấp ẩm thực mà còn thiết lập một tiêu chuẩn mới về tính chức năng và hiệu quả. Sự hòa quyện giữa tầm nhìn của Mùa Sake và khả năng kỹ thuật của Saigon Horeca đã tạo ra một thiên đường ẩm thực kết hợp sự chân thực của văn hóa izakaya với những tiến bộ mới nhất trong thiết bị.', 'saigonhoreca'); ?></p>
        <p><?php echo esc_html__('Nằm tại số 07 Lê Ngô Cát, Quận 3, TP.HCM, Mùa Sake không chỉ là một điểm đến izakaya hấp dẫn mà còn là minh chứng cho tài nghệ tinh xảo và trang thiết bị tiên tiến của Saigon Horeca.', 'saigonhoreca'); ?></p>
      </div>
    </div>
  </div>
</section>
