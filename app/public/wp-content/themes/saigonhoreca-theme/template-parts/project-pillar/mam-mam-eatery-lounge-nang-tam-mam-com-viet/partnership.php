<?php
/**
 * Project Pillar — mam-mam-eatery-lounge-nang-tam-mam-com-viet
 * Section #4: split
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp__section pp__section--alt">
  <div class="pp__container">
    <div class="pp-split-mml pp-split-mml--reverse">
      <div class="pp-split-mml__body">
        <span class="pp-text-mml__divider" aria-hidden="true"></span>
        <h2 class="pp-text-mml__title"><?php echo esc_html__('Điểm nhấn mạnh mẽ trong thiết kế của Saigon Horeca', 'saigonhoreca'); ?></h2>
        <div class="pp-text-mml__body">
      <p><?php echo esc_html__('Bếp MÂM MÂM – Nơi hội tụ của tinh hoa ẩm thực từ Mâm cơm Việt', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Ngay từ ánh nhìn đầu tiên, căn bếp do Saigon Horeca thiết kế tạo sự ấn tượng bởi lối bố trí gọn gàng, đường nét uốn cong mềm mại trong khu vực bar. Mặc dù hai khu vực bếp và bar được phân tách rõ ràng, nhưng mỗi không gian đều có chức năng riêng biệt, tối ưu hóa trải nghiệm cho cả đội ngũ bếp và bartenders.', 'saigonhoreca'); ?></p>
      <p><?php echo esc_html__('Mọi thiết bị trong căn bếp đều được Saigon Horeca lựa chọn kỹ lưỡng, từ chất liệu inox bóng bẩy đến công năng chuẩn công nghiệp, tất cả đều hướng tới mục tiêu mang lại một quy trình vận hành mượt mà và hiệu quả.', 'saigonhoreca'); ?></p>
        </div>
      </div>
      <div class="pp-split-mml__media">
        <img src="<?php echo sgh_img('2025/05/mam-mam-sgh-4.jpg'); ?>" alt="<?php echo esc_attr__('Thiết bị bếp công nghiệp Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
      </div>
    </div>
  </div>
</section>
