<?php
/**
 * Project Pillar — spice-world-hotpot
 * Section #5: specs — thiết kế công năng & hệ thống bếp
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-swh-specs">
  <div class="pp-swh-specs__glow" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <div class="pp-swh-specs__grid">

      <div class="pp-swh-specs__side">
        <div class="pp-swh-specs__blueprint">
          <div class="pp-swh-specs__coord pp-swh-specs__coord--tl" aria-hidden="true">SYS_COORD: 10.7546&deg; N, 106.6710&deg; E</div>
          <div class="pp-swh-specs__coord pp-swh-specs__coord--tr" aria-hidden="true">SPICE_WORLD / CAD_v1.0</div>
          <div class="pp-swh-specs__coord pp-swh-specs__coord--bl" aria-hidden="true">SCALE: 1:50 @ A3</div>
          <div class="pp-swh-specs__coord pp-swh-specs__coord--br" aria-hidden="true">SAIGON HORECA &copy; 2026</div>

          <figure class="pp-swh-media pp-image-container-shared pp-swh-specs__figure">
            <span class="pp-swh-tag">LAYOUT PLAN</span>
            <img src="<?php echo esc_url(sgh_img('spice-world-hotpot/swh-ban-ve-tong-the-khu-bep.png')); ?>" alt="<?php echo esc_attr__('Bản vẽ tổng thể khu bếp Spice World Hot Pot', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
            <figcaption class="pp-image-caption-shared"><?php echo esc_html__('Bản vẽ tổng thể bố trí khu bếp do Saigon Horeca thiết kế', 'saigonhoreca'); ?></figcaption>
          </figure>
        </div>
      </div>

      <div class="pp-swh-specs__main">
        <header class="pp-swh-specs__header">
          <div class="pp-swh-badge">
            <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
              <path d="M12 2L14.85 9.15L22 12L14.85 14.85L12 22L9.15 14.85L2 12L9.15 9.15L12 2Z"/>
            </svg>
            <?php echo esc_html__('Thiết kế công năng', 'saigonhoreca'); ?>
          </div>
          <h2 class="pp-swh-specs__title">
            <?php echo esc_html__('Hệ thống bếp công nghiệp', 'saigonhoreca'); ?>
            <span class="pp-swh-specs__title-gold"><?php echo esc_html__('Tiêu chuẩn nhà hàng quốc tế', 'saigonhoreca'); ?></span>
          </h2>
          <div class="pp-swh-divider" aria-hidden="true"></div>
          <p class="pp-swh-specs__intro"><?php echo esc_html__('Với diện tích nhà bếp lên đến 120 mét vuông, kết hợp với các điều kiện hiện có như thang máy và bố trí công trình, Saigon Horeca đã tính toán và phân bổ các khu vực chức năng theo tiêu chuẩn của một nhà hàng tiêu chuẩn quốc tế.', 'saigonhoreca'); ?></p>
        </header>

        <div class="pp-swh-specs__features">
          <article class="pp-swh-specs__feature">
            <span class="pp-swh-specs__icon" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <rect x="3" y="3" width="18" height="18" rx="2"/>
                <path d="M3 9h18M9 21V9" stroke-linecap="round"/>
              </svg>
            </span>
            <div class="pp-swh-specs__feature-text">
              <h3><?php echo esc_html__('Khu bếp 120m² phân vùng chức năng', 'saigonhoreca'); ?></h3>
              <p><?php echo esc_html__('Các khu vực chức năng được tính toán và phân bổ khoa học dựa trên thang máy, bố trí công trình hiện có, đảm bảo luồng vận hành mượt mà theo tiêu chuẩn quốc tế.', 'saigonhoreca'); ?></p>
            </div>
          </article>

          <article class="pp-swh-specs__feature">
            <span class="pp-swh-specs__icon" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M3 21V8l9-5 9 5v13" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M9 21v-6h6v6M7 11h.01M17 11h.01" stroke-linecap="round"/>
              </svg>
            </span>
            <div class="pp-swh-specs__feature-text">
              <h3><?php echo esc_html__('Khu rửa & sơ chế 100% inox', 'saigonhoreca'); ?></h3>
              <p><?php echo esc_html__('Trang bị 2 bồn rửa kép, một bàn rửa, kệ gắn tường và tủ lớn bằng thép không gỉ. Toàn bộ do Saigon Horeca thiết kế và sản xuất riêng, kèm khu chuẩn bị dự phòng đảm bảo vệ sinh an toàn thực phẩm.', 'saigonhoreca'); ?></p>
            </div>
          </article>

          <article class="pp-swh-specs__feature">
            <span class="pp-swh-specs__icon" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M12 2v6m0 0c-3 0-5 2-5 5v7h10v-7c0-3-2-5-5-5z" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </span>
            <div class="pp-swh-specs__feature-text">
              <h3><?php echo esc_html__('Hệ thống thông gió tối ưu', 'saigonhoreca'); ?></h3>
              <p><?php echo esc_html__('Do món ăn kèm lẩu là nguyên liệu tươi dùng trực tiếp tại bàn, khu bếp rộng rãi chỉ cần một máy hút khói để thoát khói, đảm bảo thông gió tiêu chuẩn mà tiết kiệm đáng kể chi phí đầu tư.', 'saigonhoreca'); ?></p>
            </div>
          </article>

          <article class="pp-swh-specs__feature">
            <span class="pp-swh-specs__icon" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <rect x="5" y="2" width="14" height="20" rx="2"/>
                <path d="M9 6v4M9 14v4" stroke-linecap="round"/>
              </svg>
            </span>
            <div class="pp-swh-specs__feature-text">
              <h3><?php echo esc_html__('Kho lạnh gần 3000 lít', 'saigonhoreca'); ?></h3>
              <p><?php echo esc_html__('Khu phục vụ trang bị 3 tủ lạnh cánh kính đứng Berjaya-Malaysia, 2 tủ lạnh nằm Hoshizaki, cùng 6 tủ lạnh trượt inox và 2 bàn rửa inox, giữ trọn độ tươi cho nguyên liệu.', 'saigonhoreca'); ?></p>
            </div>
          </article>
        </div>
      </div>

    </div>
  </div>
</section>
