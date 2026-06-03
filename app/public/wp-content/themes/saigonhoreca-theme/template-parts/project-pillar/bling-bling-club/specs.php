<?php
/**
 * Project Pillar — bling-bling-club
 * Section #5: split
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-bbc pp-section-bbc--alt">
  <div class="pp-container-shared">
    <div class="pp-split-bbc pp-split-bbc--reverse">
      <div class="pp-split-bbc__body">
        <span class="pp-text-bbc__divider" aria-hidden="true"></span>
        <h2 class="pp-text-bbc__title"><?php echo esc_html__('Sân chơi của các nhà pha chế sáng tạo:', 'saigonhoreca'); ?></h2>
        <div class="pp-text-bbc__body">
      <p><?php echo esc_html__('Tất cả các thiết kế và trang thiết bị được SGH cung cấp cho Bling Bling đều mang tính nghệ thuật cao, làm cho rõ ràng với khách hàng rằng sảnh trung tâm không chỉ là một sân khấu cho các vũ công khoe dáng trên nền nhạc vô cùng hứng khởi và năng động, mà còn là một sân khấu cho các nhà pha chế, những nghệ sĩ tạo ra những loại đồ uống cao cấp, để giọt rượu và dầu thơm nhảy múa trong ly cocktail, làm hài lòng cả vị giác và thị giác của các tín đồ của đêm nơi đây.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Với chất lượng của thiết bị lưu trữ lạnh Fujimak đã được chứng nhận trên thị trường Nhật Bản, nó luôn đảm bảo rằng nguyên liệu luôn tươi mới đến tay của các phù thủy cocktail và trở thành những ly cocktail cực kỳ độc đáo cho những khách hàng tinh tế.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Để luôn có sự kiểm soát đối với các ly và dụng cụ trộn, ngoài việc chúng tôi sản xuất và sắp xếp các bồn rửa bằng thép không gỉ cho khu vực quầy bar, còn có sự giúp đỡ vô cùng đáng chú ý từ Winterholter – một máy rửa ly công nghiệp với công suất 100 ly/giờ (điều kiện tiêu chuẩn).', 'saigonhoreca'); ?></p>
        </div>
      </div>
      <div class="pp-split-bbc__media">
        <div class="pp-image-container-shared">
          <img src="<?php echo sgh_img('bling-bling-club/bling-bling-club-thiet-bi-bep-cong-nghiep.jpg'); ?>" alt="<?php echo esc_attr__('Hệ thống quầy bar pha chế cocktail inox chuyên nghiệp', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="600" height="800">
          <div class="pp-image-caption-shared"><?php echo esc_html__('Hệ thống quầy bar pha chế cocktail inox chuyên nghiệp (Cocktail Station) được bố trí khoa học', 'saigonhoreca'); ?></div>
        </div>
      </div>
    </div>
  </div>
</section>
