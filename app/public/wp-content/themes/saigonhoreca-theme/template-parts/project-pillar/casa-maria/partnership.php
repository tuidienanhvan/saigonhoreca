<?php
/**
 * Project Pillar — casa-maria
 * Section #4: partnership — Giải pháp bảo quản & Trữ đông nguyên liệu
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-cm pp-partnership-section-cm pp-section-cm--alt">
  <div class="pp-container-shared">

    <!-- TIÊU ĐỀ LỚN MỞ ĐẦU (Intro Header) -->
    <div class="pp-cm-intro__header" style="margin-bottom: 4.5rem;">
      <span class="pp-text-cm__divider" aria-hidden="true"></span>
      <h2 class="pp-text-cm__title" style="max-width: 44rem;">
        <?php echo esc_html__('Giải pháp bảo quản – Trữ đông – Chuẩn mực cho nguyên liệu Tây Ban Nha', 'saigonhoreca'); ?>
      </h2>
      <div class="pp-text-cm__lead">
        <p><?php echo esc_html__('Lớp kỹ thuật thầm lặng nhưng quyết định trực tiếp đến độ tươi ngon của từng phần tapas.', 'saigonhoreca'); ?></p>
      </div>
    </div>

    <!-- Dàn trang bất đối xứng (Ảnh Tủ Đông Trái 38% / Ô Lưới Giải Pháp Phải 62%) -->
    <div class="pp-cm-row pp-cm-row--layout-partnership pp-cm-row--asymmetric-1" style="margin-bottom: 6rem;">
      
      <!-- Cột ảnh (Tủ đông công nghiệp 6 cánh) -->
      <div class="pp-cm-row__media pp-cm-media-partnership">
        <!-- SVG AutoCAD CAD kỹ thuật mờ -->
        <svg class="pp-cm-svg-frame-decor" viewBox="0 0 400 500" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
          <rect x="15" y="15" width="370" height="470" rx="8" stroke="rgba(212, 175, 55, 0.08)" stroke-width="1"/>
          <line x1="15" y1="120" x2="385" y2="120" stroke="rgba(212, 175, 55, 0.04)" stroke-width="1" stroke-dasharray="4 4"/>
          <line x1="15" y1="380" x2="385" y2="380" stroke="rgba(212, 175, 55, 0.04)" stroke-width="1" stroke-dasharray="4 4"/>
        </svg>

        <!-- Khung ảnh 4 góc vàng kim lộng lẫy -->
        <div class="pp-cm-luxury-frame pp-cm-luxury-frame--partnership">
          <span class="pp-cm-corner pp-cm-corner--tl"></span>
          <span class="pp-cm-corner pp-cm-corner--tr"></span>
          <span class="pp-cm-corner pp-cm-corner--bl"></span>
          <span class="pp-cm-corner pp-cm-corner--br"></span>
          <div class="pp-image-container-shared">
            <img src="<?php echo sgh_img('casa-maria/casa-maria-goc-quay-bar-decor-go.jpg'); ?>" alt="<?php echo esc_attr__('Hệ thống quầy bar gỗ Casa Maria', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
            <div class="pp-image-caption-shared">
              <?php echo esc_html__('Hệ thống quầy bar gỗ mộc mạc và thiết bị lạnh bảo quản chuyên dụng', 'saigonhoreca'); ?>
            </div>
          </div>
        </div>
      </div>

      <!-- Cột chữ (Grid 3 thẻ Solution Glassmorphism) -->
      <div class="pp-cm-row__text">
        <div class="pp-solutions-grid-cm">
          
          <div class="pp-solution-card-cm">
            <div class="pp-solution-card-header-cm">
              <span class="pp-solution-card-icon-cm" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
                  <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
                  <line x1="8" y1="21" x2="16" y2="21"/>
                  <line x1="12" y1="17" x2="12" y2="21"/>
                </svg>
              </span>
              <h4 class="pp-solution-card-title-cm">
                <?php echo esc_html__('Bảo quản mát & trữ đông chuyên sâu', 'saigonhoreca'); ?>
              </h4>
            </div>
            <div class="pp-solution-card-body-cm">
              <p>
                <?php echo esc_html__('Thiết bị lưu trữ phù hợp hoàn hảo với nhịp nhập nguyên liệu Tây Ban Nha tươi ngon hàng ngày như phô mai Manchego, Jamón Ibérico và hải sản cao cấp.', 'saigonhoreca'); ?>
              </p>
            </div>
          </div>

          <div class="pp-solution-card-cm">
            <div class="pp-solution-card-header-cm">
              <span class="pp-solution-card-icon-cm" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
                  <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                </svg>
              </span>
              <h4 class="pp-solution-card-title-cm">
                <?php echo esc_html__('Kỹ thuật sơ chế Sous-vide tinh tế', 'saigonhoreca'); ?>
              </h4>
            </div>
            <div class="pp-solution-card-body-cm">
              <p>
                <?php echo esc_html__('Sử dụng máy sous-vide chuyên nghiệp từ Sammic giúp kiểm soát nhiệt độ chính xác tuyệt đối, giữ trọn hương vị tự nhiên và kết cấu hoàn mỹ cho các món tapas đút lò.', 'saigonhoreca'); ?>
              </p>
            </div>
          </div>

          <div class="pp-solution-card-cm">
            <div class="pp-solution-card-header-cm">
              <span class="pp-solution-card-icon-cm" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
                  <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                  <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
                  <line x1="12" y1="22.08" x2="12" y2="12"/>
                </svg>
              </span>
              <h4 class="pp-solution-card-title-cm">
                <?php echo esc_html__('Giải pháp đóng gói chân không chuyên nghiệp', 'saigonhoreca'); ?>
              </h4>
            </div>
            <div class="pp-solution-card-body-cm">
              <p>
                <?php echo esc_html__('Hút chân không hỗ trợ khâu chuẩn bị bán thành phẩm tối ưu, bảo toàn nguyên liệu trong điều kiện yếm khí lý tưởng, rút ngắn thời gian chuẩn bị cho giờ cao điểm.', 'saigonhoreca'); ?>
              </p>
            </div>
          </div>

        </div>
      </div>

    </div>

    <!-- HÀNG KHỐI TIẾP THEO: 2 ảnh so le máy sous-vide + tủ Fujimak bên trái, Card quote chốt bên phải -->
    <div class="pp-cm-row pp-cm-row--layout-sub-partnership pp-cm-row--asymmetric-2">
      
      <!-- Cột ảnh so le -->
      <div class="pp-cm-row__media pp-cm-media-sub-part">
        <!-- Ảnh thiết bị 3 (Sous-vide Sammic) -->
        <div class="pp-cm-luxury-frame pp-cm-luxury-frame--sub-part-1">
          <span class="pp-cm-corner pp-cm-corner--tl"></span>
          <span class="pp-cm-corner pp-cm-corner--tr"></span>
          <span class="pp-cm-corner pp-cm-corner--bl"></span>
          <span class="pp-cm-corner pp-cm-corner--br"></span>
          <div class="pp-image-container-shared">
            <img src="<?php echo sgh_img('casa-maria/casa-maria-may-sous-vide-sammic.jpg'); ?>" alt="<?php echo esc_attr__('Máy sous-vide Sammic tại Casa Maria', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
            <div class="pp-image-caption-shared">
              <?php echo esc_html__('Kỹ thuật sơ chế Sous-vide bằng máy Sammic chuyên dụng', 'saigonhoreca'); ?>
            </div>
          </div>
        </div>

        <!-- Ảnh thiết bị 4 (Tủ mát Fujimak) -->
        <div class="pp-cm-luxury-frame pp-cm-luxury-frame--sub-part-2">
          <span class="pp-cm-corner pp-cm-corner--tl"></span>
          <span class="pp-cm-corner pp-cm-corner--tr"></span>
          <span class="pp-cm-corner pp-cm-corner--bl"></span>
          <span class="pp-cm-corner pp-cm-corner--br"></span>
          <div class="pp-image-container-shared">
            <img src="<?php echo sgh_img('casa-maria/casa-maria-chau-rua-inox-ban-thao-tac.jpg'); ?>" alt="<?php echo esc_attr__('Khu vực chậu rửa và bàn thao tác sơ chế', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
            <div class="pp-image-caption-shared">
              <?php echo esc_html__('Khu vực chậu rửa và bàn sơ chế inox y tế chuẩn vệ sinh an toàn thực phẩm', 'saigonhoreca'); ?>
            </div>
          </div>
        </div>
      </div>

      <!-- Cột chữ Quote panel -->
      <div class="pp-cm-row__text">
        <div class="pp-intro-card-cm pp-intro-card-cm--partnership-quote" style="border-right: 2px solid rgba(212, 175, 55, 0.2); border-left: none;">
          <h3 class="pp-cm-card-heading"><?php echo esc_html__('Kỹ Thuật Thầm Lặng', 'saigonhoreca'); ?></h3>
          <div class="pp-intro-card-cm__body">
            <p class="pp-cm-desc-p">
              <?php echo esc_html__('Từng thớ thịt đùi heo muối Iberico béo ngậy, từng con tôm nướng đỏ rực hay đĩa bạch tuộc Pulpo a la Gallega mọng nước – tất cả đều cần một chế độ bảo quản chuẩn xác đến từng độ C để giữ trọn vẹn kết cấu nguyên liệu.', 'saigonhoreca'); ?>
            </p>
            <p class="pp-cm-desc-p" style="font-weight: 500; color: var(--gold) !important; font-style: italic;">
              <?php echo esc_html__('Bảo quản mát và trữ đông không chỉ là giải pháp cất trữ, mà là sự tôn trọng cốt lõi của Saigon Horeca dành cho hương vị ẩm thực nguyên bản.', 'saigonhoreca'); ?>
            </p>
          </div>
        </div>
      </div>

    </div>

  </div>
</section>
