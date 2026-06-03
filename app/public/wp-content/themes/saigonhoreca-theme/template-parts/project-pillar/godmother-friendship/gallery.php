<?php
/**
 * Project Pillar — godmother-friendship
 * Section #6: gallery
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-gmf pp-gallery-gmf-section scroll-reveal">
  <div class="pp-container-shared">
    <div class="pp-text-gmf--center" style="margin-bottom: 2.5rem;">
      <div class="pp-badge-gmf" style="margin: 0 auto 1rem;">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2L14.85 9.15L22 12L14.85 14.85L12 22L9.15 14.85L2 12L9.15 9.15L12 2Z"/></svg>
        Thư viện ảnh thực tế
      </div>
      <h2 class="pp-text-gmf__title" style="text-align: center;">Thiết Bị Bếp Vận Hành Thực Tế</h2>
      <div class="pp-text-gmf__divider pp-text-gmf__divider--center" aria-hidden="true"></div>
      <div class="pp-text-gmf__body" style="text-align: center;">
        <p>Hình ảnh thực tế hệ thống thiết bị bếp nóng inox công nghiệp do Saigon Horeca thiết kế, sản xuất và thi công lắp đặt trọn gói tại Godmother Friendship.</p>
      </div>
    </div>

    <!-- Nhóm 3 hình thiết bị bếp thực tế xếp ngang hàng -->
    <div class="pp-gallery-equipment-grid-gmf">
      <!-- Item 1: Prep area double sink -->
      <div class="pp-gallery-grid-gmf__item">
        <div class="pp-image-container-shared pp-gallery-img-container-gmf">
          <img src="<?php echo sgh_img('godmother-friendship/godmother-friendship-khu-bep-cong-nghiep-inox.webp'); ?>" alt="<?php echo esc_attr__('Hệ thống khu rửa và sơ chế bếp công nghiệp bằng inox', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
          <div class="pp-image-caption-shared"><?php echo esc_html__('Bồn rửa đôi inox kết hợp bàn sơ chế chắc chắn, kết nối đường cấp thoát nước tối ưu cho khu sơ chế và vệ sinh.', 'saigonhoreca'); ?></div>
        </div>
      </div>

      <!-- Item 2: Line bếp Cobra -->
      <div class="pp-gallery-grid-gmf__item">
        <div class="pp-image-container-shared pp-gallery-img-container-gmf">
          <img src="<?php echo sgh_img('godmother-friendship/godmother-friendship-line-bep-au-6-hong-cobra.webp'); ?>" alt="<?php echo esc_attr__('Hệ line bếp Âu công nghiệp Cobra', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
          <div class="pp-image-caption-shared"><?php echo esc_html__('Line bếp Âu 6 họng Cobra tích hợp điều chỉnh nhiệt độ chính xác, phục vụ nhanh chóng các món Brunch chất lượng cao.', 'saigonhoreca'); ?></div>
        </div>
      </div>

      <!-- Item 3: Lò Salamander -->
      <div class="pp-gallery-grid-gmf__item">
        <div class="pp-image-container-shared pp-gallery-img-container-gmf">
          <img src="<?php echo sgh_img('godmother-friendship/godmother-friendship-lo-nuong-salamander-treo-tuong.webp'); ?>" alt="<?php echo esc_attr__('Lò nướng Salamander treo tường công nghiệp', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
          <div class="pp-image-caption-shared"><?php echo esc_html__('Lò nướng Salamander điện treo tường công suất lớn, tối ưu hóa không gian thao tác phía trên line bếp nóng.', 'saigonhoreca'); ?></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Đối tác chiến lược - Block đúc kết khép lại câu chuyện (gộp vào cuối Section 6) -->
<section class="pp-section-gmf pp-section-gmf--alt scroll-reveal">
  <div class="pp-container-shared">
    <div class="pp-glass-card-gmf pp-partner-card-gmf" style="max-width: 900px; margin: 0 auto; text-align: center;">
      <!-- Corner ornaments -->
      <div class="pp-corner-ornament-gmf--tl" aria-hidden="true"></div>
      <div class="pp-corner-ornament-gmf--tr" aria-hidden="true"></div>
      <div class="pp-corner-ornament-gmf--bl" aria-hidden="true"></div>
      <div class="pp-corner-ornament-gmf--br" aria-hidden="true"></div>

      <!-- Bounding box SVG decoration -->
      <svg class="pp-cad-bbox-card-gmf" viewBox="0 0 100 100" fill="none" stroke="currentColor" stroke-width="0.3" preserveAspectRatio="none" aria-hidden="true">
        <rect x="1" y="1" width="98" height="98" stroke-dasharray="2 2"/>
      </svg>

      <div class="pp-badge-gmf" style="margin: 0 auto 1.5rem;">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2L14.85 9.15L22 12L14.85 14.85L12 22L9.15 14.85L2 12L9.15 9.15L12 2Z"/></svg>
        Đối tác chiến lược
      </div>

      <!-- Logo đối tác ở trên -->
      <div class="pp-partner-logo-gmf">
        <img src="<?php echo sgh_img('godmother-friendship/godmother-friendship-logo-ngang-bake-brunch.webp'); ?>" alt="<?php echo esc_attr__('Godmother Friendship Logo Bake & Brunch', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
      </div>
      
      <h2 class="pp-text-gmf__title" style="text-align: center; margin-bottom: 1rem;">Saigon Horeca — Đối Tác Uy Tín Của GodMother Friendship</h2>
      <div class="pp-text-gmf__divider pp-text-gmf__divider--center" aria-hidden="true"></div>
      
      <div class="pp-text-gmf__body" style="max-width: 800px; margin: 1.5rem auto 0 auto;">
        <p>Đối với dự án GodMother Friendship, nhà phát triển dự án HypeAsia đã lựa chọn Saigon Horeca là đơn vị tư vấn, thiết kế và cung cấp trọn gói thiết bị cho Nhà bếp, Quầy bar và Quầy Kiosk của nhà hàng. Với kinh nghiệm và chuyên môn trong thiết kế, Saigon Horeca luôn tối ưu hóa sản phẩm của mình, sản xuất trực tiếp theo thiết kế riêng. Nhờ đó, có thể đưa ra mức giá tốt nhất cho khách hàng.</p>
      </div>

      <!-- Ảnh nội thất quầy bar gỗ và bàn đá terrazzo ở dưới -->
      <div class="pp-partner-image-gmf">
        <div class="pp-image-container-shared pp-gallery-img-container-gmf">
          <img src="<?php echo sgh_img('godmother-friendship/godmother-friendship-noi-that-quay-bar-go-va-ban-terrazzo.webp'); ?>" alt="<?php echo esc_attr__('Không gian quầy bar gỗ và bàn đá terrazzo thực tế tại Godmother Friendship', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
          <div class="pp-image-caption-shared"><?php echo esc_html__('Không gian quầy bar kết hợp hài hòa giữa chất liệu gỗ ấm áp và mặt bàn đá terrazzo hiện đại tại Godmother Friendship.', 'saigonhoreca'); ?></div>
        </div>
      </div>
    </div>
  </div>
</section>

