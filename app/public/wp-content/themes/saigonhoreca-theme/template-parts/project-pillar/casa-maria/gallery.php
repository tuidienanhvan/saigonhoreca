<?php
/**
 * Project Pillar — casa-maria
 * Section #6: gallery — Không gian & Trải nghiệm thực tế
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-cm pp-gallery-section-cm">
  <div class="pp-container-shared">

    <!-- TIÊU ĐỀ LỚN MỞ ĐẦU (Intro Header) -->
    <div class="pp-cm-intro__header" style="margin-bottom: 4.5rem;">
      <span class="pp-text-cm__divider" aria-hidden="true"></span>
      <h2 class="pp-text-cm__title" style="max-width: 44rem;">
        <?php echo esc_html__('Không gian & Trải nghiệm thực tế', 'saigonhoreca'); ?>
      </h2>
      <div class="pp-text-cm__lead">
        <p><?php echo esc_html__('Sự hài hòa tuyệt đối giữa nét thô mộc Địa Trung Hải và hơi thở ẩm thực đương đại.', 'saigonhoreca'); ?></p>
      </div>
    </div>

    <!-- DÒNG CHẢY 1: NGHỆ THUẬT & KHÔNG GIAN (Ảnh Trái 38% / Chữ Phải 62%) -->
    <div class="pp-cm-row pp-cm-row--layout-gallery-decor pp-cm-row--asymmetric-1" style="margin-bottom: 6rem;">
      
      <!-- Cột ảnh (Bàn Brunch Địa Trung Hải) -->
      <div class="pp-cm-row__media pp-cm-media-gallery-decor">
        <!-- SVG CAD mờ trang trí -->
        <svg class="pp-cm-svg-frame-decor" viewBox="0 0 400 400" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
          <rect x="20" y="20" width="360" height="360" rx="8" stroke="rgba(212, 175, 55, 0.08)" stroke-width="1"/>
          <circle cx="200" cy="200" r="140" stroke="rgba(212, 175, 55, 0.03)" stroke-width="1" stroke-dasharray="4 4"/>
        </svg>

        <!-- Khung ảnh 4 góc vàng kim lộng lẫy -->
        <div class="pp-cm-luxury-frame pp-cm-luxury-frame--gallery-decor">
          <span class="pp-cm-corner pp-cm-corner--tl"></span>
          <span class="pp-cm-corner pp-cm-corner--tr"></span>
          <span class="pp-cm-corner pp-cm-corner--bl"></span>
          <span class="pp-cm-corner pp-cm-corner--br"></span>
          <div class="pp-image-container-shared">
            <img src="<?php echo sgh_img('casa-maria/casa-maria-banh-mi-tapas-brunch-cafe.jpg'); ?>" alt="<?php echo esc_attr__('Bàn Brunch Địa Trung Hải tại Casa Maria', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
            <div class="pp-image-caption-shared">
              <?php echo esc_html__('Bữa tiệc Brunch Địa Trung Hải ngập tràn năng lượng và sắc màu tươi mát', 'saigonhoreca'); ?>
            </div>
          </div>
        </div>
      </div>

      <!-- Cột chữ -->
      <div class="pp-cm-row__text">
        <div class="pp-intro-card-cm pp-intro-card-cm--gallery-decor">
          <h3 class="pp-cm-card-heading"><?php echo esc_html__('Nghệ Thuật Trực Quan & Trải Nghiệm', 'saigonhoreca'); ?></h3>
          <div class="pp-intro-card-cm__body">
            <div class="pp-cm-highlight-quote">
              <p>
                <span class="pp-cm-dropcap">M</span><?php echo esc_html__('ột bữa tiệc Brunch Địa Trung Hải ngập tràn màu sắc với trứng ốp rực rỡ, bánh mì giòn rụm và ly nước cam mát lành – mang đến khởi đầu hoàn hảo.', 'saigonhoreca'); ?>
              </p>
            </div>
            <p class="pp-cm-desc-p">
              <?php echo esc_html__('Mỗi hương vị, ánh sáng và chi tiết decor gỗ mộc tại Casa Maria đều được Saigon Horeca nâng niu, tạo nên một hành trình thưởng thức ẩm thực trọn vẹn cả vị giác lẫn thị giác.', 'saigonhoreca'); ?>
            </p>
          </div>
        </div>
      </div>

    </div>

    <!-- DÒNG CHẢY 2: PANORAMIC SHOWCASE (Ảnh tapas lớn nằm riêng biệt tràn màn hình có góc gold) -->
    <div class="pp-cm-panoramic-showcase" style="margin-bottom: 6rem;">
      <div class="pp-cm-luxury-frame pp-cm-luxury-frame--panoramic">
        <span class="pp-cm-corner pp-cm-corner--tl"></span>
        <span class="pp-cm-corner pp-cm-corner--tr"></span>
        <span class="pp-cm-corner pp-cm-corner--bl"></span>
        <span class="pp-cm-corner pp-cm-corner--br"></span>
        <div class="pp-image-container-shared">
          <img src="<?php echo sgh_img('casa-maria/casa-maria-xien-tapas-pintxos.jpg'); ?>" alt="<?php echo esc_attr__('Các xiên tapas pintxos tại Casa Maria', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
          <div class="pp-image-caption-shared">
            <?php echo esc_html__('Các xiên tapas pintxos trên nền bánh mì, phục vụ trong không khí ấm cúng của Casa Maria', 'saigonhoreca'); ?>
          </div>
        </div>
      </div>
      <div class="pp-cm-panoramic-quote">
        <p><strong><?php echo esc_html__('Khi thiết bị bếp hoạt động với kỷ luật và nhịp độ hoàn hảo ở phía sau, đầu bếp có thể thăng hoa tuyệt đối để sáng tạo ra những đĩa tapas tinh tế nhất.', 'saigonhoreca'); ?></strong></p>
      </div>
    </div>

    <!-- DÒNG CHẢY 3: HẬU TRƯỜNG KỸ THUẬT SO LE (Ảnh Trái So Le 58% / Chữ Phải 42%) -->
    <div class="pp-cm-row pp-cm-row--layout-gallery-backstage pp-cm-row--asymmetric-2">
      
      <!-- Cột ảnh so le (Vẹm sốt + Nghêu hấp) -->
      <div class="pp-cm-row__media pp-cm-media-gallery-backstage">
        <!-- Ảnh thiết bị 5 (Vẹm sốt) -->
        <div class="pp-cm-luxury-frame pp-cm-luxury-frame--backstage-1">
          <span class="pp-cm-corner pp-cm-corner--tl"></span>
          <span class="pp-cm-corner pp-cm-corner--tr"></span>
          <span class="pp-cm-corner pp-cm-corner--bl"></span>
          <span class="pp-cm-corner pp-cm-corner--br"></span>
          <div class="pp-image-container-shared">
            <img src="<?php echo sgh_img('casa-maria/casa-maria-mon-an-vem-sot-tay-ban-nha.jpg'); ?>" alt="<?php echo esc_attr__('Món vẹm sốt Tây Ban Nha', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
            <div class="pp-image-caption-shared">
              <?php echo esc_html__('Vẹm xanh sốt Tây Ban Nha đậm vị biển nồng nàn', 'saigonhoreca'); ?>
            </div>
          </div>
        </div>

        <!-- Ảnh thiết bị 6 (Nghêu hấp) -->
        <div class="pp-cm-luxury-frame pp-cm-luxury-frame--backstage-2">
          <span class="pp-cm-corner pp-cm-corner--tl"></span>
          <span class="pp-cm-corner pp-cm-corner--tr"></span>
          <span class="pp-cm-corner pp-cm-corner--bl"></span>
          <span class="pp-cm-corner pp-cm-corner--br"></span>
          <div class="pp-image-container-shared">
            <img src="<?php echo sgh_img('casa-maria/casa-maria-mon-an-ngheu-hap-tay-ban-nha.jpg'); ?>" alt="<?php echo esc_attr__('Món nghêu hấp Tây Ban Nha', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
            <div class="pp-image-caption-shared">
              <?php echo esc_html__('Đĩa nghêu hấp Almejas a la Marinera nóng hổi đầy kích thích vị giác', 'saigonhoreca'); ?>
            </div>
          </div>
        </div>
      </div>

      <!-- Cột chữ -->
      <div class="pp-cm-row__text">
        <div class="pp-intro-card-cm pp-intro-card-cm--gallery-backstage">
          <h3 class="pp-cm-card-heading"><?php echo esc_html__('Hương Vị Tapas Đích Thực', 'saigonhoreca'); ?></h3>
          <div class="pp-intro-card-cm__body">
            <p class="pp-cm-desc-p">
              <?php echo esc_html__('Từ những phần vẹm xanh sốt Tây Ban Nha đậm đà đến đĩa nghêu hấp Almejas mọng nước, từng đĩa tapas đều mang hương vị nồng ấm của biển cả Địa Trung Hải, được chế biến chuẩn xác từ căn bếp mở chuyên nghiệp do Saigon Horeca trang bị.', 'saigonhoreca'); ?>
            </p>
          </div>
        </div>
      </div>

    </div>

  </div>
</section>

<?php /* T-034: merged from related.php (cũ section 7) */ ?>
<section class="pp-section-cm pp-section-cm-related">
  <div class="pp-container-shared">
    
    <!-- Dàn trang bất đối xứng (Chữ Trái 42% / Ảnh Phải 58%) -->
    <div class="pp-cm-row pp-cm-row--layout-related pp-cm-row--asymmetric-2">
      
      <!-- Cột chữ -->
      <div class="pp-cm-row__text">
        <div class="pp-intro-card-cm pp-intro-card-cm--related">
          <span class="pp-text-cm__divider" aria-hidden="true" style="margin-bottom: 1.5rem;"></span>
          <h3 class="pp-cm-card-heading"><?php echo esc_html__('Hoàn thiện & Bàn giao', 'saigonhoreca'); ?></h3>
          
          <div class="pp-intro-card-cm__body">
            <div class="pp-cm-highlight-quote">
              <p>
                <span class="pp-cm-dropcap">S</span><?php echo esc_html__('ự hoàn mỹ đến từ những chi tiết nhỏ nhất. Saigon Horeca bàn giao hệ thống bếp và bar Casa Maria đáp ứng trọn vẹn cả tiêu chuẩn công nghệ lẫn giá trị thẩm mỹ nghệ thuật.', 'saigonhoreca'); ?>
              </p>
            </div>
            <p class="pp-cm-desc-p">
              <?php echo esc_html__('Mỗi góc nhỏ, từ những bức tranh decor nghệ thuật đến hệ thống inox sáng bóng của thiết bị bếp, đều được đội ngũ kỹ sư Saigon Horeca chăm chút tỉ mỉ. Sự hài lòng tuyệt đối của đối tác Casa Maria chính là minh chứng cho năng lực triển khai dự án F&B chuẩn quốc tế của chúng tôi.', 'saigonhoreca'); ?>
            </p>
            
            <p class="pp-cm-desc-p" style="font-weight: 500; font-style: italic; color: var(--gold) !important; margin-top: 1rem;">
              <?php echo esc_html__('Kiến tạo không gian ẩm thực không chỉ là lắp đặt thiết bị, mà là thổi hồn vào từng trải nghiệm vận hành bền vững.', 'saigonhoreca'); ?>
            </p>
          </div>
        </div>
      </div>

      <!-- Cột ảnh (Bức tranh nghệ thuật decor hoàn thiện) -->
      <div class="pp-cm-row__media pp-cm-media-related">
        <!-- SVG viền AutoCAD mờ -->
        <svg class="pp-cm-svg-frame-decor" viewBox="0 0 400 400" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
          <rect x="20" y="20" width="360" height="360" rx="8" stroke="rgba(212, 175, 55, 0.08)" stroke-width="1"/>
          <line x1="20" y1="20" x2="380" y2="380" stroke="rgba(212, 175, 55, 0.03)" stroke-width="1" stroke-dasharray="8 8"/>
        </svg>

        <!-- Khung ảnh 4 góc vàng kim lộng lẫy -->
        <div class="pp-cm-luxury-frame pp-cm-luxury-frame--related">
          <span class="pp-cm-corner pp-cm-corner--tl"></span>
          <span class="pp-cm-corner pp-cm-corner--tr"></span>
          <span class="pp-cm-corner pp-cm-corner--bl"></span>
          <span class="pp-cm-corner pp-cm-corner--br"></span>
          <div class="pp-image-container-shared">
            <img src="<?php echo sgh_img('casa-maria/casa-maria-tranh-nghe-thuat-decor-treo-tuong.jpg'); ?>" alt="<?php echo esc_attr__('Chi tiết tranh nghệ thuật decor treo tường tại Casa Maria', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
            <div class="pp-image-caption-shared">
              <?php echo esc_html__('Tranh nghệ thuật decor – Điểm xuyết không gian Tây Ban Nha ấm cúng', 'saigonhoreca'); ?>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<?php /* T-034: merged from cta.php (cũ section 8) */ ?>
<section class="pp-section-cm pp-cta-section-cm pp-section-cm--alt">
  <div class="pp-container-shared">

    <!-- Lưới AutoCAD mờ chạy ngầm toàn nền -->
    <svg class="pp-cm-bg-grid" width="100%" height="100%" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; opacity: 0.02; z-index: 1;">
      <rect width="100%" height="100%" fill="url(#cm-grid-pattern)" />
    </svg>

    <!-- Bố cục 3 cột nghệ thuật (Architectural Monolith Grid) -->
    <div class="pp-cta-triptych-grid">
      
      <!-- CỘT 1: Đồ họa chữ dọc (Editorial Column Axis) -->
      <div class="pp-cta-col-axis" aria-hidden="true">
        <div class="pp-cta-vertical-text">SAIGON HORECA</div>
        <span class="pp-cta-axis-line"></span>
      </div>

      <!-- CỘT 2: Nội dung Triết lý & Checklist kính mờ -->
      <div class="pp-cta-col-content">
        <div class="pp-cta-glass-card">
          <span class="pp-text-cm__divider" aria-hidden="true" style="margin-bottom: 1.5rem; display: block; width: 40px; height: 2px; background: var(--gold);"></span>
          <h2 class="pp-text-cm__title" style="font-size: clamp(1.8rem, 3.2vw, 2.5rem); margin-bottom: 2rem; text-align: left; line-height: 1.25; color: var(--bc);">
            <?php echo esc_html__('Triết lý đối tác bền vững từ Saigon Horeca', 'saigonhoreca'); ?>
          </h2>
          
          <div class="pp-cta-card-body">
            <p class="pp-cm-desc-p" style="font-size: 1.02rem; line-height: 1.75; color: var(--bc2); margin-bottom: 2rem;">
              <?php echo esc_html__('Casa Maria là ví dụ điển hình cho cách Saigon Horeca tiếp cận dự án F&B cao cấp: từ bếp tapas, quầy bar wine – cafe đến hệ thống bảo quản và hút mùi, mỗi giải pháp đều được thiết kế để phục vụ đúng tinh thần Tây Ban Nha hiện đại.', 'saigonhoreca'); ?>
            </p>
            
            <ul class="pp-cta-philosophy-list-cm">
              <li>
                <svg class="pp-cta-list-icon-cm" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2" width="18" height="18" aria-hidden="true">
                  <polyline points="20 6 9 17 4 12"/>
                </svg>
                <span><?php echo esc_html__('Không chỉ bán thiết bị đơn lẻ.', 'saigonhoreca'); ?></span>
              </li>
              <li>
                <svg class="pp-cta-list-icon-cm" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2" width="18" height="18" aria-hidden="true">
                  <polyline points="20 6 9 17 4 12"/>
                </svg>
                <span><?php echo esc_html__('Không áp công thức thiết kế rập khuôn.', 'saigonhoreca'); ?></span>
              </li>
              <li>
                <svg class="pp-cta-list-icon-cm" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2" width="18" height="18" aria-hidden="true">
                  <polyline points="20 6 9 17 4 12"/>
                </svg>
                <span><strong><?php echo esc_html__('Luôn kiến tạo từ concept vận hành & cảm xúc khách hàng.', 'saigonhoreca'); ?></strong></span>
              </li>
            </ul>

            <!-- Biểu đồ quy trình SVG chạy mờ ở chân cột -->
            <svg class="pp-cta-flow-svg" viewBox="0 0 300 30" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" style="width: 100%; max-width: 280px; height: auto; opacity: 0.12; margin: 2.5rem 0 1.5rem 0;">
              <circle cx="15" cy="15" r="4" fill="var(--gold)"/>
              <line x1="19" y1="15" x2="135" y2="15" stroke="var(--gold)" stroke-width="1" stroke-dasharray="3 3"/>
              <circle cx="150" cy="15" r="4" fill="var(--gold)"/>
              <line x1="154" y1="15" x2="281" y2="15" stroke="var(--gold)" stroke-width="1" stroke-dasharray="3 3"/>
              <circle cx="285" cy="15" r="4" fill="var(--gold)"/>
            </svg>

            <p class="pp-cm-desc-p" style="font-weight: 500; font-style: italic; color: var(--gold) !important; margin: 0; font-size: 1rem; border-top: 1px solid var(--bd); padding-top: 1.5rem;">
              <?php echo esc_html__('Một căn bếp tốt không chỉ giúp nấu ăn ngon hơn – mà còn giúp hồn cốt của concept nhà hàng được kể một cách bền vững nhất.', 'saigonhoreca'); ?>
            </p>
          </div>
        </div>
      </div>

      <!-- CỘT 3: Hình ảnh mặt tiền lớn hoành tráng -->
      <div class="pp-cta-col-media">
        <!-- SVG viền mảnh kiến trúc mờ ảo -->
        <svg class="pp-cta-media-decor" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" style="position: absolute; top: -1.5rem; left: -1.5rem; width: calc(100% + 3rem); height: calc(100% + 3rem); pointer-events: none; opacity: 0.12; z-index: 1;">
          <rect x="5" y="5" width="90" height="90" stroke="var(--gold)" stroke-width="0.5"/>
          <line x1="50" y1="5" x2="50" y2="95" stroke="var(--gold)" stroke-width="0.2" stroke-dasharray="2 2"/>
        </svg>

        <div class="pp-cta-photo-wrapper">
          <div class="pp-image-container-shared">
            <img src="<?php echo sgh_img('casa-maria/casa-maria-mat-tien-cua-do-tay-ban-nha.jpg'); ?>" alt="<?php echo esc_attr__('Tổng thể dự án Casa Maria do Saigon Horeca triển khai', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
            <div class="pp-image-caption-shared">
              <?php echo esc_html__('Mặt tiền cửa đỏ Tây Ban Nha – Sự khởi đầu đầy cảm hứng của Casa Maria', 'saigonhoreca'); ?>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>
