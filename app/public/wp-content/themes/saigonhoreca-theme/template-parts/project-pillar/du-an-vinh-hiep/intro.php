<?php
/**
 * Project Pillar — du-an-vinh-hiep
 * Section #2: intro — Coffee Lab triết lý thương hiệu
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-vhp pp-intro-vhp">
  <div class="pp-watermark-vhp" aria-hidden="true">01</div>
  <div class="pp-ambient-glow-vhp pp-ambient-glow-vhp--top-right" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <div class="pp-intro-vhp__grid">

      <!-- Cột chữ -->
      <div class="pp-intro-vhp__text">
        <header class="pp-intro-vhp__header">
          <div class="pp-badge-vhp">
            <span class="pp-badge-vhp__diamond" aria-hidden="true"></span>
            <?php echo esc_html__('Câu chuyện thương hiệu', 'saigonhoreca'); ?>
          </div>
          <h2 class="pp-intro-vhp__title">
            <?php echo esc_html__('Khi mỗi không gian là một phần của câu chuyện chất lượng', 'saigonhoreca'); ?>
          </h2>
          <div class="pp-intro-vhp__divider" aria-hidden="true"></div>
        </header>

        <div class="pp-intro-vhp__body">
          <p><?php echo esc_html__('Trong ngành cà phê, chất lượng không chỉ nằm ở hạt cà phê – mà còn ở cách người ta tôn trọng và tôn vinh giá trị của nó. Với Vĩnh Hiệp, doanh nghiệp xuất khẩu cà phê nhân số 1 Việt Nam niên vụ 2023–2024, mỗi không gian trong nhà máy không đơn thuần là nơi vận hành, mà còn là một phần của câu chuyện thương hiệu khi tiếp đón đối tác toàn cầu.', 'saigonhoreca'); ?></p>
          <p><?php echo esc_html__('Dự án Coffee Lab – Nhà máy chế biến cà phê xuất khẩu Vĩnh Hiệp là một trong những dự án tiêu biểu thể hiện rõ triết lý đó. Và Saigon Horeca được lựa chọn không phải để "lắp thiết bị", mà để tư vấn và kiến tạo một hệ thống bếp – showroom tương xứng với vị thế thương hiệu.', 'saigonhoreca'); ?></p>
          <p><?php echo esc_html__('Coffee Lab tại Vĩnh Hiệp không chỉ phục vụ mục đích nội bộ. Đây là nơi đón tiếp các đoàn khách quốc tế, đối tác thương mại, chuyên gia cà phê, đồng thời là không gian để trình diễn quy trình, gu thẩm mỹ và chuẩn mực vận hành của một doanh nghiệp xuất khẩu hàng đầu.', 'saigonhoreca'); ?></p>
          <p><?php echo esc_html__('Điều đó đặt ra một yêu cầu rất cao: Không gian phải chính xác về công năng, chuẩn mực về kỹ thuật, nhưng đồng thời tinh tế về hình ảnh và cảm xúc. Mọi chi tiết – từ bếp, khu chế biến, đến bàn cupping – đều phải góp phần kể câu chuyện về chất lượng và sự chỉn chu.', 'saigonhoreca'); ?></p>
        </div>
      </div>

      <!-- Cột ảnh xếp tầng -->
      <div class="pp-intro-vhp__media">
        <div class="pp-image-container-shared pp-intro-vhp__image pp-intro-vhp__image--lead">
          <span class="pp-intro-vhp__tag">VHP—01 · EXPORT GRADE</span>
          <img src="<?php echo sgh_img('du-an-vinh-hiep/vinh-hiep-tu-inox-am-hai-canh-chan-inox.webp'); ?>" alt="<?php echo esc_attr__('Tủ inox âm hai cánh chân inox lưu trữ tại bếp Vĩnh Hiệp do Saigon Horeca thi công', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
          <div class="pp-image-caption-shared"><?php echo esc_html__('Tủ inox âm – giải pháp lưu trữ kín đáo, dễ vệ sinh', 'saigonhoreca'); ?></div>
        </div>
        <div class="pp-image-container-shared pp-intro-vhp__image pp-intro-vhp__image--float">
          <img src="<?php echo sgh_img('du-an-vinh-hiep/vinh-hiep-bon-rua-inox-ban-chu-l-tu-be.webp'); ?>" alt="<?php echo esc_attr__('Bồn rửa inox mặt bàn chữ L với hệ tủ bệ tại khu sơ chế bếp Vĩnh Hiệp', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="600" height="800">
          <div class="pp-image-caption-shared"><?php echo esc_html__('Khu bồn rửa inox – sơ chế và vệ sinh chuẩn quốc tế', 'saigonhoreca'); ?></div>
        </div>
      </div>

    </div>
  </div>
</section>
