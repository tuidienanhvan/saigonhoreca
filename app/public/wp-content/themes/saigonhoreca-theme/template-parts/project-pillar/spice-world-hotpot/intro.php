<?php
/**
 * Project Pillar — spice-world-hotpot
 * Section #2: intro
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-swh-intro">
  <div class="pp-swh-intro__glow pp-swh-intro__glow--top-right" aria-hidden="true"></div>
  <div class="pp-swh-intro__num-watermark" aria-hidden="true">01</div>

  <div class="pp-container-shared">
    <div class="pp-swh-intro__grid">

      <div class="pp-swh-intro__main">
        <header class="pp-swh-intro__header">
          <div class="pp-swh-badge">
            <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
              <path d="M12 2L14.85 9.15L22 12L14.85 14.85L12 22L9.15 14.85L2 12L9.15 9.15L12 2Z"/>
            </svg>
            <?php echo esc_html__('Thương hiệu quốc tế', 'saigonhoreca'); ?>
          </div>
          <h2 class="pp-swh-intro__title">
            Spice World Hot Pot
            <span class="pp-swh-intro__title-gold"><?php echo esc_html__('Ẩm Thực Lẩu Tứ Xuyên Đích Thực', 'saigonhoreca'); ?></span>
          </h2>
          <div class="pp-swh-divider" aria-hidden="true"></div>

          <address class="pp-swh-intro__address">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
              <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" stroke-linecap="round" stroke-linejoin="round"/>
              <circle cx="12" cy="10" r="3"/>
            </svg>
            <?php echo esc_html__('1061-1063-1065 Trần Hưng Đạo, Phường 5, Quận 5, TPHCM', 'saigonhoreca'); ?>
          </address>
        </header>

        <div class="pp-swh-glass-card pp-swh-intro__lead-card">
          <p class="pp-swh-intro__lead"><?php echo esc_html__('Spice World Hot Pot là một thương hiệu lẩu nổi tiếng có nguồn gốc từ Sichuan, một vùng đất với truyền thống ẩm thực lâu đời tại Trung Quốc. Được thành lập vào năm 2003, Spice World Hot Pot là một trong 10 thương hiệu lẩu nổi tiếng nhất được biết đến với hương vị đặc biệt của nước lẩu và các món ăn kèm. Hiện tại, thương hiệu này có mặt tại 10 quốc gia trên thế giới, bao gồm Trung Quốc, Hoa Kỳ, Singapore và Việt Nam.', 'saigonhoreca'); ?></p>
        </div>

        <div class="pp-swh-intro__flags">
          <h3 class="pp-swh-intro__flags-title"><?php echo esc_html__('MẠNG LƯỚI QUỐC TẾ / INTERNATIONAL BRANCHES', 'saigonhoreca'); ?></h3>
          <ul class="pp-swh-intro__flags-list">
            <li class="pp-swh-intro__flag">
              <span class="pp-swh-intro__flag-img"><img src="<?php echo esc_url(sgh_img('spice-world-hotpot/co-singapore.png')); ?>" alt="<?php echo esc_attr__('Cờ Singapore', 'saigonhoreca'); ?>" width="40" height="27" loading="lazy" decoding="async"></span>
              <span class="pp-swh-intro__flag-name">Singapore</span>
            </li>
            <li class="pp-swh-intro__flag">
              <span class="pp-swh-intro__flag-img"><img src="<?php echo esc_url(sgh_img('spice-world-hotpot/co-myanmar.png')); ?>" alt="<?php echo esc_attr__('Cờ Myanmar', 'saigonhoreca'); ?>" width="40" height="27" loading="lazy" decoding="async"></span>
              <span class="pp-swh-intro__flag-name">Myanmar</span>
            </li>
            <li class="pp-swh-intro__flag">
              <span class="pp-swh-intro__flag-img"><img src="<?php echo esc_url(sgh_img('spice-world-hotpot/co-thai-lan.png')); ?>" alt="<?php echo esc_attr__('Cờ Thái Lan', 'saigonhoreca'); ?>" width="40" height="27" loading="lazy" decoding="async"></span>
              <span class="pp-swh-intro__flag-name"><?php echo esc_html__('Thái Lan', 'saigonhoreca'); ?></span>
            </li>
            <li class="pp-swh-intro__flag">
              <span class="pp-swh-intro__flag-img"><img src="<?php echo esc_url(sgh_img('spice-world-hotpot/co-trung-quoc.png')); ?>" alt="<?php echo esc_attr__('Cờ Trung Quốc', 'saigonhoreca'); ?>" width="40" height="27" loading="lazy" decoding="async"></span>
              <span class="pp-swh-intro__flag-name"><?php echo esc_html__('Trung Quốc', 'saigonhoreca'); ?></span>
            </li>
            <li class="pp-swh-intro__flag">
              <span class="pp-swh-intro__flag-img"><img src="<?php echo esc_url(sgh_img('spice-world-hotpot/co-my-e1632216750335.png')); ?>" alt="<?php echo esc_attr__('Cờ Hoa Kỳ', 'saigonhoreca'); ?>" width="40" height="27" loading="lazy" decoding="async"></span>
              <span class="pp-swh-intro__flag-name"><?php echo esc_html__('Hoa Kỳ', 'saigonhoreca'); ?></span>
            </li>
            <li class="pp-swh-intro__flag">
              <span class="pp-swh-intro__flag-img"><img src="<?php echo esc_url(sgh_img('spice-world-hotpot/co-viet-nam.png')); ?>" alt="<?php echo esc_attr__('Cờ Việt Nam', 'saigonhoreca'); ?>" width="40" height="27" loading="lazy" decoding="async"></span>
              <span class="pp-swh-intro__flag-name"><?php echo esc_html__('Việt Nam', 'saigonhoreca'); ?></span>
            </li>
          </ul>
        </div>

        <div class="pp-swh-intro__body">
          <p><?php echo esc_html__('Sự độc đáo của nước lẩu đến từ các nguyên liệu tự nhiên được lựa chọn kỹ càng từ nơi xuất xứ của nó, như tiêu rừng được chọn lọc đặc biệt từ Hongya, Sichuan, và ớt từ khu vực núi Quy Châu và Sichuan, cũng như các loại nấm khác nhau. Tất cả các nguyên liệu được ninh trong khoảng 4 đến 6 giờ để tạo ra hương vị đặc trưng của nước lẩu Sichuan Mala trong hình dáng của những chú gấu Teddy dễ thương và mèo Hello Kitty.', 'saigonhoreca'); ?></p>
          <p><?php echo esc_html__('Đỉnh cao ẩm thực không chỉ đến từ hương vị mà còn từ trải nghiệm trực quan, khi Spice World thêm một yếu tố vui nhộn bằng cách kết hợp các nhân vật đồ chơi tan chảy vào lẩu, làm vui mắt cho thực khách trong khi dạ dày của họ mong muốn thêm nhiều hơn.', 'saigonhoreca'); ?></p>
          <p><?php echo esc_html__('Tại Spice World, các lát thịt bò Wagyu tươi ngon được sắp xếp một cách tinh tế trong bộ váy của búp bê Barbie, kết hợp với không gian thiết kế "Old World" đầy hoài niệm với những bức tường đỏ và tranh vẽ dã ngoại rừng Sichuan.', 'saigonhoreca'); ?></p>
        </div>
      </div>

      <div class="pp-swh-intro__side">
        <figure class="pp-swh-media pp-image-container-shared pp-swh-intro__figure">
          <span class="pp-swh-tag">SICHUAN INTERIOR</span>
          <img src="<?php echo esc_url(sgh_img('spice-world-hotpot/spice-world-hotpot-ban-lau-nguyen-lieu-noi-doi-vi.jpeg')); ?>" alt="<?php echo esc_attr__('Mâm lẩu Tứ Xuyên với nồi inox hai ngăn cùng thịt bò, hải sản và rau nấm', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
          <figcaption class="pp-image-caption-shared"><?php echo esc_html__('Mâm lẩu Tứ Xuyên bài trí trên bàn đá đen: nồi inox hai ngăn mala – nước trắng cùng thịt bò, hải sản và rau nấm tươi', 'saigonhoreca'); ?></figcaption>
        </figure>
      </div>

    </div>
  </div>
</section>
