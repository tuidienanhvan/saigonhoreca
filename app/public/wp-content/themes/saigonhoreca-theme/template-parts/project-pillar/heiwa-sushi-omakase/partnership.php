<?php
/**
 * Project Pillar — heiwa-sushi-omakase
 * Section #5: partnership — "Trải nghiệm ẩm thực tương tác": full-bleed
 * background using the Parallax Clip-Path technique (clip-path inset on the
 * section confines a position:fixed bg = a GPU-smooth, JS-free parallax) with
 * centered editorial copy over a LIGHT token overlay.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-bg-hwa scroll-reveal">
  <!-- Thẻ con gánh hiệu ứng Parallax Clip-Path siêu mượt (position:fixed bị section clip-path giới hạn) -->
  <div class="pp-section-bg-hwa__bg" style="background-image:url('<?php echo sgh_img('heiwa-sushi-omakase/heiwa-sushi-omakase-khu-bon-rua-va-so-che-ban-mat-inox.webp'); ?>');" aria-hidden="true"></div>
  <div class="pp-section-bg-hwa__scrim" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <div class="pp-section-bg-hwa__content scroll-reveal">
      <div class="pp-text-hwa__badge pp-section-bg-hwa__badge"><span class="pp-text-hwa__badge-accent">//</span> <?php echo esc_html__('TRẢI NGHIỆM TƯƠNG TÁC', 'saigonhoreca'); ?></div>
      <h2 class="pp-text-hwa__title"><?php echo esc_html__('Trải Nghiệm Ẩm Thực Tương Tác', 'saigonhoreca'); ?></h2>
      <div class="pp-text-hwa__divider pp-text-hwa__divider--center" aria-hidden="true"></div>

      <div class="pp-section-bg-hwa__body">
        <p><?php echo esc_html__('Tại Heiwa Sushi, sự kết hợp giữa quầy bếp và quầy bar tạo nên một trải nghiệm ẩm thực hài hòa, nơi hương vị và nghệ thuật thẩm mỹ hòa quyện vào nhau.', 'saigonhoreca'); ?></p>
        <p><?php echo esc_html__('Trái tim của Heiwa Sushi, chính là căn bếp hiện đại và tiện nghi được Saigon Horeca chăm chút tỉ mỉ. Những thiết bị cao cấp như tủ đông Hoshizaki, quầy lạnh bảo quản thực phẩm, máy làm đá, hệ thống giá treo tường và các dụng cụ bằng thép không gỉ đóng vai trò quan trọng trong thành công của nhà hàng. Bên cạnh đó, Heiwa Sushi còn chú trọng đến việc duy trì nhiệt độ và độ ẩm thích hợp trong bếp, nhằm đảm bảo nguyên liệu luôn trong trạng thái tốt nhất.', 'saigonhoreca'); ?></p>
        <p><?php echo esc_html__('Sự đầu tư bài bản vào hệ thống bếp không chỉ góp phần vào chất lượng món ăn mà còn tạo nên môi trường làm việc an toàn, thoải mái cho các đầu bếp. Điều này gián tiếp mang đến trải nghiệm ẩm thực tuyệt vời cho thực khách.', 'saigonhoreca'); ?></p>
      </div>

      <div class="pp-section-bg-hwa__callout">
        <svg class="pp-section-bg-hwa__sparkle" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
          <path d="M12 2L14.85 9.15L22 12L14.85 14.85L12 22L9.15 14.85L2 12L9.15 9.15L12 2Z" />
        </svg>
        <p class="pp-section-bg-hwa__callout-text"><?php echo esc_html__('Sự đồng điệu giữa thiết kế công thái học và nghệ thuật bài trí mang đến không gian vận hành hoàn hảo cho từng đêm tiệc Omakase thượng hạng.', 'saigonhoreca'); ?></p>
      </div>
    </div>
  </div>
</section>
