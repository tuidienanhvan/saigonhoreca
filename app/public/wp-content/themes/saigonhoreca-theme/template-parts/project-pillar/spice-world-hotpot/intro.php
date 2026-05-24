<?php
/**
 * Project Pillar — spice-world-hotpot
 * Section #2: intro
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp-swh-intro">
  <div class="pp-watermark-bg-swh" aria-hidden="true">
    <svg viewBox="0 0 100 100" fill="none" stroke="currentColor" stroke-width="1.5">
      <path d="M40 20 H60 M50 20 V80 M40 80 H60" stroke-linecap="round"/>
    </svg>
  </div>

  <div class="pp-ambient-glow-swh pp-ambient-glow-swh--top-right" aria-hidden="true"></div>

  <div class="pp__container">
    <div class="pp-grid-12-swh">
      
      <div class="pp-grid-12-swh__text--cols-5 swh-intro__main">
        <header class="swh-intro__header">
          <div class="pp-badge-swh">
            <svg viewBox="0 0 24 24" fill="currentColor">
              <path d="M12 2L14.85 9.15L22 12L14.85 14.85L12 22L9.15 14.85L2 12L9.15 9.15L12 2Z"/>
            </svg>
            <?php echo esc_html__('Thương hiệu quốc tế', 'saigonhoreca'); ?>
          </div>
          <h2 class="pp-text-swh__title">
            SPICE WORLD HOTPOT
            <span class="pp-text-swh__title-gold"><?php echo esc_html__('Ẩm Thực Lẩu Tứ Xuyên Đích Thực', 'saigonhoreca'); ?></span>
          </h2>
          <div class="pp-text-swh__divider" aria-hidden="true"></div>
          
          <address class="swh-intro__address">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
              <circle cx="12" cy="10" r="3"/>
            </svg>
            <?php echo esc_html__('1061-1063-1065 Trần Hưng Đạo, Phường 5, Quận 5, TPHCM', 'saigonhoreca'); ?>
          </address>
        </header>

        <div class="swh-intro__content">
          <div class="pp-glass-card-swh swh-intro__quote-box">
            <p class="swh-intro__lead"><?php echo esc_html__('Spice World Hot Pot là một thương hiệu lẩu nổi tiếng có nguồn gốc từ Sichuan, một vùng đất với truyền thống ẩm thực lâu đời tại Trung Quốc. Được thành lập vào năm 2003, Spice World Hot Pot là một trong 10 thương hiệu lẩu nổi tiếng nhất được biết đến với hương vị đặc biệt của nước lẩu và các món ăn kèm. Hiện tại, thương hiệu này có mặt tại 10 quốc gia trên thế giới, bao gồm Trung Quốc, Hoa Kỳ, Singapore và Việt Nam.', 'saigonhoreca'); ?></p>
          </div>
          
          <div class="swh-intro__flags-wrapper">
            <h4 class="swh-intro__flags-title"><?php echo esc_html__('MẠNG LƯỚI QUỐC TẾ / INTERNATIONAL BRANCHES', 'saigonhoreca'); ?></h4>
            <div class="swh-intro__flags-list">
              <div class="swh-intro__flag-item">
                <div class="swh-intro__flag-img-box">
                  <img src="<?php echo sgh_img('2021/09/co-singapore.png'); ?>" alt="Singapore Flag" loading="lazy">
                </div>
                <span class="swh-intro__flag-name">Singapore</span>
              </div>
              <div class="swh-intro__flag-item">
                <div class="swh-intro__flag-img-box">
                  <img src="<?php echo sgh_img('2021/09/co-myanmar.png'); ?>" alt="Myanmar Flag" loading="lazy">
                </div>
                <span class="swh-intro__flag-name">Myanmar</span>
              </div>
              <div class="swh-intro__flag-item">
                <div class="swh-intro__flag-img-box">
                  <img src="<?php echo home_url('/wp-content/uploads/2021/09/co-thai-lan.png'); ?>" alt="Thailand Flag" loading="lazy">
                </div>
                <span class="swh-intro__flag-name"><?php echo esc_html__('Thái Lan', 'saigonhoreca'); ?></span>
              </div>
              <div class="swh-intro__flag-item">
                <div class="swh-intro__flag-img-box">
                  <img src="<?php echo sgh_img('2021/09/co-trung-quoc.png'); ?>" alt="China Flag" loading="lazy">
                </div>
                <span class="swh-intro__flag-name"><?php echo esc_html__('Trung Quốc', 'saigonhoreca'); ?></span>
              </div>
              <div class="swh-intro__flag-item">
                <div class="swh-intro__flag-img-box">
                  <img src="<?php echo sgh_img('2021/09/co-my-e1632216750335.png'); ?>" alt="United States Flag" loading="lazy">
                </div>
                <span class="swh-intro__flag-name"><?php echo esc_html__('Hoa Kỳ', 'saigonhoreca'); ?></span>
              </div>
              <div class="swh-intro__flag-item">
                <div class="swh-intro__flag-img-box">
                  <img src="<?php echo sgh_img('2021/09/co-viet-nam.png'); ?>" alt="Vietnam Flag" loading="lazy">
                </div>
                <span class="swh-intro__flag-name"><?php echo esc_html__('Việt Nam', 'saigonhoreca'); ?></span>
              </div>
            </div>
          </div>
          
          <div class="swh-intro__description">
            <p><?php echo esc_html__('Sự độc đáo của nước lẩu đến từ các nguyên liệu tự nhiên được lựa chọn kỹ càng từ nơi xuất xứ của nó, như tiêu rừng được chọn lọc đặc biệt từ Hongya, Sichuan, và ớt từ khu vực núi Quy Châu và Sichuan, cũng như các loại nấm khác nhau. Tất cả các nguyên liệu được ninh trong khoảng 4 đến 6 giờ để tạo ra hương vị đặc trưng của nước lẩu Sichuan Mala trong hình dáng của những chú gấu Teddy dễ thương và mèo Hello Kitty.', 'saigonhoreca'); ?></p>
            <p><?php echo esc_html__('Đỉnh cao ẩm thực không chỉ đến từ hương vị mà còn từ trải nghiệm trực quan, khi Spice World thêm một yếu tố vui nhộn bằng cách kết hợp các nhân vật đồ chơi tan chảy vào lẩu, làm vui mắt cho thực khách trong khi dạ dày của họ mong muốn thêm nhiều hơn.', 'saigonhoreca'); ?></p>
          </div>
          
          <div class="swh-intro__extra-description">
            <p><?php echo esc_html__('Tại Spice World, các lát thịt bò Wagyu tươi ngon được sắp xếp một cách tinh tế trong bộ váy của búp bê Barbie, kết hợp với không gian thiết kế "Old World" đầy hoài niệm với những bức tường đỏ và tranh vẽ dã ngoại rừng Sichuan.', 'saigonhoreca'); ?></p>
          </div>
        </div>
      </div>

      <div class="pp-grid-12-swh__media--cols-7 swh-intro__side">
        <div class="pp-image-container-swh swh-intro__image-container">
          <span class="pp-corner-ornament pp-corner-ornament--top-left" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--top-right" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--bottom-left" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--bottom-right" aria-hidden="true"></span>
          <div class="pp-image-border-decor" aria-hidden="true"></div>
          <div class="pp-image-tag-swh">SICHUAN INTERIOR</div>
          <img src="<?php echo sgh_img('2024/02/spice-world-hotpot-SGH-1.jpeg'); ?>" alt="<?php echo esc_attr__('Spice World Interior', 'saigonhoreca'); ?>" loading="lazy">
          <div class="pp-image-caption-swh"><?php echo esc_html__('Không gian nội thất Sichuan ấm áp kết hợp thép đen hiện đại', 'saigonhoreca'); ?></div>
        </div>
      </div>

    </div>
  </div>
</section>
