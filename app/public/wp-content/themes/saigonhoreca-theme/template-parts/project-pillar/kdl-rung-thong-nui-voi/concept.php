<?php
/**
 * Project Pillar — kdl-rung-thong-nui-voi
 * Section #3: concept (Tây Nguyên dining philosophy — nature & flavour)
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-kdl pp-section-kdl--alt pp-concept-kdl">
  <div class="pp-concept-watermark-kdl" aria-hidden="true">02</div>
  <div class="pp-ambient-glow-kdl pp-ambient-glow-kdl--bottom-left" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <div class="pp-grid-12-kdl pp-grid-12-kdl--media-first">

      <div class="pp-grid-12-kdl__media pp-concept-kdl__side">
        <div class="pp-image-container-shared pp-concept-kdl__image-container">
          <div class="pp-image-border-decor-kdl" aria-hidden="true"></div>
          <img src="<?php echo sgh_img('du-an-kdl-rung-thong-nui-voi-cua-saigonhoreca/kdl-rung-thong-nui-voi-khu-bep-cong-nghiep-con-voi-vang.webp'); ?>" alt="<?php echo esc_attr__('Khu bếp công nghiệp nhà hàng Con Voi Vàng', 'saigonhoreca'); ?>" loading="lazy" width="1366" height="768">
          <div class="pp-image-caption-shared"><?php echo esc_html__('Khu bếp công nghiệp nơi chế biến tinh hoa ẩm thực Tây Nguyên tại nhà hàng Con Voi Vàng', 'saigonhoreca'); ?></div>
        </div>
      </div>

      <div class="pp-grid-12-kdl__text pp-concept-kdl__main">
        <div class="pp-glass-card-kdl">
          <span class="pp-corner-leaf-kdl pp-corner-leaf-kdl--tl" aria-hidden="true"></span>
          <span class="pp-corner-leaf-kdl pp-corner-leaf-kdl--br" aria-hidden="true"></span>

          <header class="pp-concept-kdl__header">
            <div class="pp-badge-kdl">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12c4-7 10-7 14 0-4 7-10 7-14 0Z"/><circle cx="12" cy="12" r="2.4"/>
              </svg>
              <?php echo esc_html__('Trải nghiệm ẩm thực', 'saigonhoreca'); ?>
            </div>
            <h2 class="pp-text-kdl__title"><?php echo esc_html__('Hương vị Tây Nguyên giữa đại ngàn', 'saigonhoreca'); ?></h2>
            <div class="pp-text-kdl__divider" aria-hidden="true"></div>
          </header>

          <div class="pp-concept-kdl__body">
            <p><?php echo esc_html__('Mỗi nhà hàng trong khu du lịch mang một phong cách và thực đơn riêng, nhưng đều gặp nhau ở tinh thần tôn vinh ẩm thực bản địa: mộc mạc, đậm đà và đầy nghệ thuật.', 'saigonhoreca'); ?></p>

            <ul class="pp-concept-kdl__list">
              <li class="pp-concept-kdl__list-item">
                <span class="pp-concept-kdl__list-icon" aria-hidden="true">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12c4-7 10-7 14 0-4 7-10 7-14 0Z"/></svg>
                </span>
                <div>
                  <strong><?php echo esc_html__('Cơm lam & món nướng', 'saigonhoreca'); ?></strong>
                  <p><?php echo esc_html__('Đặc sản truyền thống Tây Nguyên chế biến từ nguyên liệu tươi ngon, giữ trọn hương vị núi rừng.', 'saigonhoreca'); ?></p>
                </div>
              </li>
              <li class="pp-concept-kdl__list-item">
                <span class="pp-concept-kdl__list-icon" aria-hidden="true">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M8 21h8M12 21v-4M7 4h10l-1 8a4 4 0 0 1-8 0Z"/></svg>
                </span>
                <div>
                  <strong><?php echo esc_html__('Rượu cần nồng nàn', 'saigonhoreca'); ?></strong>
                  <p><?php echo esc_html__('Nét văn hóa cộng đồng đặc trưng của vùng cao, được phục vụ tinh tế trong không gian sang trọng.', 'saigonhoreca'); ?></p>
                </div>
              </li>
              <li class="pp-concept-kdl__list-item">
                <span class="pp-concept-kdl__list-icon" aria-hidden="true">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 L18 12 H14 L19 20 H5 L10 12 H6 Z"/></svg>
                </span>
                <div>
                  <strong><?php echo esc_html__('Giữa rừng thông nguyên bản', 'saigonhoreca'); ?></strong>
                  <p><?php echo esc_html__('Kiến trúc hòa hợp thiên nhiên, mang đến trải nghiệm ẩm thực tuyệt vời trong không gian xanh mát.', 'saigonhoreca'); ?></p>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>
