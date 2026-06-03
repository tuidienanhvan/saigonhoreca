<?php
/**
 * Project Pillar — heiwa-sushi-omakase
 * Section #5: partnership — Bespoke Wabi-Sabi Parallax Editorial
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-bg-hwa scroll-reveal">
  <!-- Thẻ con gánh hiệu ứng Parallax Clip-Path siêu mượt -->
  <div class="pp-section-bg-hwa__bg" style="background-image:url('<?php echo sgh_img('heiwa-sushi-omakase/heiwa-sushi-omakase-khu-bon-rua-va-so-che-ban-mat-inox.webp'); ?>');"></div>
  <div class="pp-section-bg-hwa__overlay" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <div class="pp-section-bg-hwa__content scroll-reveal reveal-up">
      
      <!-- Tiêu đề phong cách tạp chí -->
      <div class="pp-section-bg-hwa__header">
        <div class="pp-text-hwa__badge">
          <span class="pp-text-hwa__badge-accent">//</span> <?php echo esc_html__('TRẢI NGHIỆM TƯƠNG TÁC', 'saigonhoreca'); ?>
        </div>
        <h2 class="pp-text-hwa__title"><?php echo esc_html__('Trải Nghiệm Ẩm Thực Tương Tác', 'saigonhoreca'); ?></h2>
      </div>

      <!-- Bố cục 2 cột thanh thoát -->
      <div class="pp-hwa-partnership-row">
        <!-- Cột trái: Trích dẫn Wabi-Sabi -->
        <div class="pp-hwa-partnership-col-left">
          <div class="pp-hwa-partnership-quote">
            <span class="pp-hwa-quote-icon" aria-hidden="true">“</span>
            <p class="pp-hwa-quote-text">
              <?php echo esc_html__('Tại Heiwa Sushi, sự kết hợp giữa quầy bếp và quầy bar tạo nên một trải nghiệm ẩm thực hài hòa, nơi hương vị và nghệ thuật thẩm mỹ hòa quyện vào nhau.', 'saigonhoreca'); ?>
            </p>
          </div>
        </div>

        <!-- Cột phải: Các đoạn mô tả kỹ thuật -->
        <div class="pp-hwa-partnership-col-right">
          <p class="pp-hwa-partnership-lead">
            <?php echo esc_html__('Trái tim của Heiwa Sushi, chính là căn bếp hiện đại và tiện nghi được Saigon Horeca chăm chút tỉ mỉ. Những thiết bị cao cấp như tủ đông Hoshizaki, quầy lạnh bảo quản thực phẩm, máy làm đá, hệ thống giá treo tường và các dụng cụ bằng thép không gỉ đóng vai trò quan trọng trong thành công của nhà hàng.', 'saigonhoreca'); ?>
          </p>
          <p class="pp-hwa-partnership-body">
            <?php echo esc_html__('Bên cạnh đó, Heiwa Sushi còn chú trọng đến việc duy trì nhiệt độ và độ ẩm thích hợp trong bếp, nhằm đảm bảo nguyên liệu luôn trong trạng thái tốt nhất. Sự đầu tư bài bản vào hệ thống bếp không chỉ góp phần vào chất lượng món ăn mà còn tạo nên môi trường làm việc an toàn, thoải mái cho các đầu bếp, gián tiếp nâng tầm trải nghiệm thực khách.', 'saigonhoreca'); ?>
          </p>
        </div>
      </div>

      <!-- Đỉnh cao sự hợp tác - Callout kết luận Wabi-Sabi -->
      <div class="pp-hwa-partnership-footer-callout">
        <div class="pp-hwa-callout-inner">
          <svg class="pp-hwa-callout-sparkle" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 2L14.85 9.15L22 12L14.85 14.85L12 22L9.15 14.85L2 12L9.15 9.15L12 2Z" fill="var(--gold)" />
          </svg>
          <p class="pp-hwa-callout-text">
            <?php echo esc_html__('Sự đồng điệu giữa thiết kế công thái học và nghệ thuật bài trí mang đến không gian vận hành hoàn hảo cho từng đêm tiệc Omakase thượng hạng.', 'saigonhoreca'); ?>
          </p>
        </div>
      </div>

    </div>
  </div>
</section>
