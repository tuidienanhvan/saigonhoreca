<?php
/**
 * Project Pillar — the-brix
 * Section #9: gallery + kitchen workflow showcase
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-gallery-brix">
  <div class="pp-container-shared">

    <div class="pp-gallery-brix__header scroll-reveal">
      <h2 class="pp-gallery-brix__title"><?php echo esc_html__('Không gian & Bản vẽ kỹ thuật', 'saigonhoreca'); ?></h2>
      <div class="pp-gallery-brix__divider" aria-hidden="true"></div>
      <p class="pp-gallery-brix__lead"><?php echo esc_html__('Góp phần tạo nên tâm trạng sảng khoái và thư thái của thực khách tại The Brix có sự tham gia của Saigon Horeca, đơn vị tư vấn và cung cấp toàn bộ thiết bị, giải pháp vận hành cho khu vực Bếp và Bếp Bánh.', 'saigonhoreca'); ?></p>
    </div>

    <!-- Bento Grid 5 Ảnh Nghệ Thuật: Thực Tế Thi Công & Bản Vẽ CAD Kỹ Thuật -->
    <div class="pp-gallery-brix__grid scroll-reveal">
      
      <!-- Item 1: Bếp chính (Tiêu điểm lớn 2 cột) -->
      <div class="pp-image-container-shared pp-gallery-brix__item pp-gallery-brix__item--featured">
        <div class="pp-image-tag-brix"><?php echo esc_html__('Bếp chính chuyên nghiệp', 'saigonhoreca'); ?></div>
        <img src="<?php echo sgh_img('the-brix/the-brix-he-thong-bep-nau-chinh-inox.jpg'); ?>" alt="<?php echo esc_attr__('Hệ thống bếp nấu chính inox tại The Brix', 'saigonhoreca'); ?>" loading="lazy">
        <div class="pp-image-caption-shared"><?php echo esc_html__('Hệ bếp nóng inox 304 liên hoàn chuyên nghiệp kết hợp tum hút mùi tiêu âm công suất lớn, giữ cho gian bếp luôn thông thoáng và sạch sẽ.', 'saigonhoreca'); ?></div>
      </div>

      <!-- Item 2: Khu soạn chia -->
      <div class="pp-image-container-shared pp-gallery-brix__item">
        <div class="pp-image-tag-brix"><?php echo esc_html__('Khu soạn chia trung tâm', 'saigonhoreca'); ?></div>
        <img src="<?php echo sgh_img('the-brix/the-brix-ban-trung-tam-quay-soan-chia.jpg'); ?>" alt="<?php echo esc_attr__('Bàn trung tâm soạn chia inox tại The Brix', 'saigonhoreca'); ?>" loading="lazy">
        <div class="pp-image-caption-shared"><?php echo esc_html__('Bàn soạn chia inox trung tâm tích hợp ngăn mát under-counter, trang bị hệ đèn giữ nóng chao đồng cổ điển bảo toàn hương vị.', 'saigonhoreca'); ?></div>
      </div>

      <!-- Item 3: Khu sơ chế & bồn rửa (MỚI) -->
      <div class="pp-image-container-shared pp-gallery-brix__item">
        <div class="pp-image-tag-brix"><?php echo esc_html__('Khu sơ chế & bồn rửa', 'saigonhoreca'); ?></div>
        <img src="<?php echo sgh_img('the-brix/the-brix-ban-so-che-rua-inox.jpg'); ?>" alt="<?php echo esc_attr__('Bàn sơ chế và bồn rửa inox tại The Brix', 'saigonhoreca'); ?>" loading="lazy">
        <div class="pp-image-caption-shared"><?php echo esc_html__('Bàn sơ chế và bồn rửa inox 304 tiêu chuẩn F&B, thiết kế bo góc mềm mại hạn chế bám bẩn và dễ dàng vệ sinh định kỳ.', 'saigonhoreca'); ?></div>
      </div>

      <!-- Item 4: Bản vẽ mặt bằng bếp chính (MỚI) -->
      <div class="pp-image-container-shared pp-gallery-brix__item">
        <div class="pp-image-tag-brix"><?php echo esc_html__('Bản vẽ mặt bằng bếp chính', 'saigonhoreca'); ?></div>
        <img src="<?php echo sgh_img('the-brix/the-brix-ban-ve-mat-bang-bep-chinh.png'); ?>" alt="<?php echo esc_attr__('Bản vẽ mặt bằng bếp chính The Brix', 'saigonhoreca'); ?>" loading="lazy">
        <div class="pp-image-caption-shared"><?php echo esc_html__('Bản vẽ thiết kế mặt bằng bếp chính với hệ thống thiết bị phân khu logic, tuân thủ nghiêm ngặt quy trình bếp một chiều chuẩn quốc tế.', 'saigonhoreca'); ?></div>
      </div>

      <!-- Item 5: Bản vẽ phòng bánh -->
      <div class="pp-image-container-shared pp-gallery-brix__item">
        <div class="pp-image-tag-brix"><?php echo esc_html__('Bản vẽ kỹ thuật phòng bánh', 'saigonhoreca'); ?></div>
        <img src="<?php echo sgh_img('the-brix/the-brix-ban-ve-mat-bang-phong-banh.png'); ?>" alt="<?php echo esc_attr__('Bản vẽ mặt bằng phòng bánh The Brix', 'saigonhoreca'); ?>" loading="lazy">
        <div class="pp-image-caption-shared"><?php echo esc_html__('Bản vẽ quy hoạch không gian phòng bánh 18m² tối ưu hóa luồng di chuyển, giúp thợ làm bánh thao tác chuẩn xác từ nhào bột đến nướng thành phẩm.', 'saigonhoreca'); ?></div>
      </div>

    </div>

  </div>
</section>

<!-- Kitchen Workflow Section: Layout 2 Cột Đối Xứng Nghệ Thuật -->
<section class="pp-kitchen-flow-brix">
  <div class="pp-container-shared">
    <div class="pp-kitchen-flow-brix__grid scroll-reveal">
      
      <!-- Cột trái: Timeline 5 bước vận hành -->
      <div class="pp-kitchen-flow-brix__timeline">
        <div class="pp-kitchen-flow-brix__header-sub">
          <span class="pp-kitchen-flow-brix__accent-label"><?php echo esc_html__('LOGISTICS & WORKFLOW', 'saigonhoreca'); ?></span>
          <h3 class="pp-kitchen-flow-brix__section-title"><?php echo esc_html__('Quy trình vận hành chuẩn mực', 'saigonhoreca'); ?></h3>
        </div>

        <div class="pp-kitchen-flow-brix__inner">
          <div class="pp-kitchen-flow-brix__step">
            <span class="pp-kitchen-flow-brix__marker" aria-hidden="true">01</span>
            <p><?php echo esc_html__('Từ khu vực nhận hàng, sơ chế đến lưu trữ, nấu nướng, trình bày món ăn và rửa chén, chúng tôi sắp xếp từng khu vực theo tiêu chuẩn an toàn thực phẩm, thuận tiện cho nhân viên trong quá trình vận hành.', 'saigonhoreca'); ?></p>
          </div>

          <div class="pp-kitchen-flow-brix__step">
            <span class="pp-kitchen-flow-brix__marker" aria-hidden="true">02</span>
            <p><?php echo esc_html__('Khu vực chuẩn bị và lưu trữ thực phẩm bao gồm bồn rửa, tủ lạnh, tủ đông và kệ treo tường.', 'saigonhoreca'); ?></p>
          </div>

          <div class="pp-kitchen-flow-brix__step">
            <span class="pp-kitchen-flow-brix__marker" aria-hidden="true">03</span>
            <p><?php echo esc_html__('Khu vực bếp chính được trang bị bếp chiên nhúng, bếp 6 họng, bếp phẳng, lò nướng và tum hút mùi.', 'saigonhoreca'); ?></p>
          </div>

          <div class="pp-kitchen-flow-brix__step">
            <span class="pp-kitchen-flow-brix__marker" aria-hidden="true">04</span>
            <p><?php echo esc_html__('Khu vực rửa chén được thiết kế theo đúng quy trình một chiều từ bàn thu gom có lỗ xả rác, vòi phun tráng, máy rửa bát đến bàn sạch và kệ úp chén đĩa...', 'saigonhoreca'); ?></p>
          </div>

          <div class="pp-kitchen-flow-brix__step">
            <span class="pp-kitchen-flow-brix__marker" aria-hidden="true">05</span>
            <p><?php echo esc_html__('Thiết kế tận dụng tối đa ánh sáng tự nhiên thông qua hệ mái kính kín, mang lại bầu không khí dễ chịu và tránh sự bí bách của không gian kín.', 'saigonhoreca'); ?></p>
          </div>
        </div>
      </div>

      <!-- Cột phải: Khối hình ảnh giải pháp vệ sinh công nghiệp (MỚI) -->
      <div class="pp-kitchen-flow-brix__visual-wrapper">
        <div class="pp-kitchen-flow-brix__visual-card">
          <div class="pp-kitchen-flow-brix__visual-tag"><?php echo esc_html__('Giải pháp vệ sinh công nghiệp', 'saigonhoreca'); ?></div>
          <div class="pp-kitchen-flow-brix__image-frame">
            <img src="<?php echo sgh_img('the-brix/the-brix-may-rua-bat-cong-nghiep.jpg'); ?>" alt="<?php echo esc_attr__('Hệ thống máy rửa bát công nghiệp tại The Brix', 'saigonhoreca'); ?>" loading="lazy">
          </div>
          <div class="pp-kitchen-flow-brix__visual-info">
            <h4 class="pp-kitchen-flow-brix__visual-title"><?php echo esc_html__('Hệ thống máy rửa bát công nghiệp', 'saigonhoreca'); ?></h4>
            <p class="pp-kitchen-flow-brix__visual-desc"><?php echo esc_html__('Saigon Horeca lắp đặt hệ thống máy rửa bát công nghiệp đạt hiệu suất cao, khử khuẩn bằng nhiệt độ tiêu chuẩn, khép kín quy trình một chiều hoàn hảo, bảo vệ sức khỏe tối đa cho thực khách.', 'saigonhoreca'); ?></p>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- Bakery Section: Layout 2 Cột Sang Trọng, Đột Phá Visual -->
<section class="pp-bakery-brix">
  <div class="pp-container-shared">
    <div class="pp-bakery-brix__card scroll-reveal">
      <div class="pp-bakery-brix__grid">
        
        <!-- Cột trái: Thông tin chuyên môn, bento list thiết bị -->
        <div class="pp-bakery-brix__info">
          <div class="pp-bakery-brix__header">
            <span class="pp-bakery-brix__icon" aria-hidden="true">🍞</span>
            <h3 class="pp-bakery-brix__title"><?php echo esc_html__('Phân khu Bếp Bánh nghệ thuật', 'saigonhoreca'); ?></h3>
          </div>
          <div class="pp-bakery-brix__body">
            <p class="pp-bakery-brix__highlight"><?php echo esc_html__('Trong không gian rộng 18 mét vuông, chúng tôi đã lập kế hoạch cẩn thận và tối ưu hóa cách bố trí các thiết bị làm bánh, đảm bảo quy trình diễn ra trơn tru từ khâu nhào, định hình bột đến ủ bột, bảo quản và nướng.', 'saigonhoreca'); ?></p>
            <p><?php echo esc_html__('Nhờ đó, những chiếc bánh thơm ngon được tạo ra dưới bàn tay khéo léo của các đầu bếp với sự hỗ trợ đắc lực từ các thiết bị chuyên dụng cao cấp do Saigon Horeca cung cấp và trực tiếp lắp đặt.', 'saigonhoreca'); ?></p>
          </div>
          
          <!-- Bento list mini thiết bị -->
          <div class="pp-bakery-brix__equipments">
            <h4 class="pp-bakery-brix__equipments-title"><?php echo esc_html__('Thiết bị cốt lõi phân khu bánh:', 'saigonhoreca'); ?></h4>
            <div class="pp-bakery-brix__equipments-grid">
              <div class="pp-bakery-brix__equipment-item">
                <span class="pp-bakery-brix__eq-dot"></span>
                <span><?php echo esc_html__('Lò nướng bánh UNOX thông minh', 'saigonhoreca'); ?></span>
              </div>
              <div class="pp-bakery-brix__equipment-item">
                <span class="pp-bakery-brix__eq-dot"></span>
                <span><?php echo esc_html__('Tủ mát trưng bày bánh cao cấp', 'saigonhoreca'); ?></span>
              </div>
              <div class="pp-bakery-brix__equipment-item">
                <span class="pp-bakery-brix__eq-dot"></span>
                <span><?php echo esc_html__('Máy cán bột chuyên dụng', 'saigonhoreca'); ?></span>
              </div>
              <div class="pp-bakery-brix__equipment-item">
                <span class="pp-bakery-brix__eq-dot"></span>
                <span><?php echo esc_html__('Bàn thao tác inox 304 bo góc', 'saigonhoreca'); ?></span>
              </div>
            </div>
          </div>
        </div>

        <!-- Cột phải: Visual lò nướng đa năng chuyên dụng (MỚI) -->
        <div class="pp-bakery-brix__visual">
          <div class="pp-bakery-brix__visual-card-inner">
            <div class="pp-bakery-brix__visual-tag"><?php echo esc_html__('Thiết bị tiêu điểm', 'saigonhoreca'); ?></div>
            <div class="pp-bakery-brix__visual-image">
              <img src="<?php echo sgh_img('the-brix/the-brix-lo-nuong-da-nang-unox-cheftop.jpg'); ?>" alt="<?php echo esc_attr__('Lò nướng đa năng UNOX Cheftop tại The Brix', 'saigonhoreca'); ?>" loading="lazy">
            </div>
            <div class="pp-bakery-brix__visual-caption">
              <h5><?php echo esc_html__('Lò nướng đa năng UNOX Cheftop', 'saigonhoreca'); ?></h5>
              <p><?php echo esc_html__('Trái tim của phòng bánh, kiểm soát nhiệt độ và độ ẩm chính xác, đảm bảo độ nở hoàn hảo và hương vị tinh tế nhất cho từng mẻ bánh.', 'saigonhoreca'); ?></p>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</section>
