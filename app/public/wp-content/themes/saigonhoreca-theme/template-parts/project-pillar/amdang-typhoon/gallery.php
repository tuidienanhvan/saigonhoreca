<?php
/**
 * Project Pillar — amdang-typhoon
 * Section #6: gallery (Khu vực bếp lò than & quầy mì di động)
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
?>
<section class="pp-gallery-adt-cooking">
  <div class="pp-container-shared">
    <div class="pp-grid-adt-cooking">
      
      <!-- Cột trái: Media - Khung Trưng Bày Chuyển Dịch Tiêu Điểm 3D (3D Depth Focal Shifting Showcase) -->
      <div class="pp-media-adt-cooking">
        <!-- Thước đo kỹ thuật trang trí chìm ngầm (Blueprint Technical Ruler) -->
        <div class="pp-cooking-tech-ruler" aria-hidden="true"></div>
        
        <!-- Ảnh 1: Bản vẽ thiết bị bếp nướng lò than -->
        <div class="pp-cooking-img-box pp-cooking-img-box--main pp-image-container-shared">
          <span class="pp-corner-ornament pp-corner-ornament--top-left" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--top-right" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--bottom-left" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--bottom-right" aria-hidden="true"></span>
          <img src="<?php echo sgh_img('amdang-typhoon/amdang-typhoon-ban-ve-khu-nuong.jpg'); ?>" alt="<?php echo esc_attr('Bản vẽ thiết bị bếp lò than và quầy mì Amdang Typhoon - Saigon Horeca'); ?>" loading="lazy" decoding="async">
          <div class="pp-image-caption-shared"><?php echo esc_html__('Bản vẽ thiết kế mặt bằng khu bếp nướng và quầy trần mì của Saigon Horeca', 'saigonhoreca'); ?></div>
        </div>
        
        <!-- Ảnh 2: Ảnh thực tế khu trần mì di động chồng lấn lệch tầng -->
        <div class="pp-cooking-img-box pp-cooking-img-box--sub pp-image-container-shared">
          <span class="pp-corner-ornament pp-corner-ornament--top-left" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--top-right" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--bottom-left" aria-hidden="true"></span>
          <span class="pp-corner-ornament pp-corner-ornament--bottom-right" aria-hidden="true"></span>
          <img src="<?php echo sgh_img('amdang-typhoon/amdang-typhoon-thuc-te-khu-tran-mi.jpg'); ?>" alt="<?php echo esc_attr('Thực tế khu trần mì di động tại nhà hàng Amdang Typhoon - Saigon Horeca'); ?>" loading="lazy" decoding="async">
          <div class="pp-image-caption-shared"><?php echo esc_html__('Thực tế quầy trần mì di động làm bằng inox 304 cao cấp tại hành lang', 'saigonhoreca'); ?></div>
        </div>
      </div>

      <!-- Cột phải: Thuyết minh giải pháp & Bảng thiết bị kỹ thuật -->
      <div class="pp-text-adt-cooking">
        <div class="pp-badge-adt-cooking">
          <span class="pp-badge-accent-adt-cooking">//</span> <?php echo esc_html('THIẾT KẾ ĐỘC BẢN'); ?>
        </div>
        
        <h3 class="pp-title-adt-cooking">
          <?php echo esc_html('Khu Bếp Lò Than & Quầy Mì Di Động Băng Cốc'); ?>
        </h3>
        
        <div class="pp-body-adt-cooking">
          <p class="pp-paragraph-adt-cooking">
            <?php echo esc_html('Điểm đặc trưng của bếp Amdang Typhoon là khu bếp nướng sử dụng hoàn toàn bằng lò than truyền thống cùng quầy mì tươi di động. Khói nghi ngút và nhiệt lượng tỏa ra từ bếp lò than cực kỳ lớn, đặt ra bài toán khó về lưu chuyển không khí.'); ?>
          </p>
          <p class="pp-paragraph-adt-cooking">
            <?php echo esc_html('Saigon Horeca đã tính toán cẩn thận hệ thống thông hút khói và cấp khí tươi chuyên biệt, giúp loại bỏ triệt để khói và khí nóng, mang lại bầu không khí thông thoáng, dễ chịu cho các đầu bếp thao tác, đồng thời tuân thủ nghiêm ngặt các quy chuẩn an toàn thực phẩm.'); ?>
          </p>
        </div>

        <!-- Bảng thông số thiết bị kỹ thuật -->
        <div class="pp-cooking-specs-card">
          <h4 class="pp-cooking-specs-title"><?php echo esc_html('DANH MỤC THIẾT BỊ KHU BẾP NƯỚNG'); ?></h4>
          <ul class="pp-cooking-specs-list">
            <li>
              <span class="spec-name"><?php echo esc_html('Hệ thống hút khói bếp & Lò than'); ?></span>
              <span class="spec-value"><?php echo esc_html('Saigon Horeca sản xuất (Custom)'); ?></span>
            </li>
            <li>
              <span class="spec-name"><?php echo esc_html('Bàn làm việc, bồn rửa inox'); ?></span>
              <span class="spec-value"><?php echo esc_html('Inox 304 tiêu chuẩn F&B'); ?></span>
            </li>
            <li>
              <span class="spec-name"><?php echo esc_html('Tủ lạnh bảo quản thực phẩm'); ?></span>
              <span class="spec-value"><?php echo esc_html('Hoshizaki (Nhật) / Berjaya (Mã)'); ?></span>
            </li>
          </ul>
        </div>
      </div>

    </div>
  </div>
</section>

<?php /* T-034: merged from related.php (cũ section 7) */ ?>
<section class="pp-related-adt">
  <div class="pp-container-shared">
    
    <!-- Phần trên: Header & Text Split (Text trái 55%, Quote phải 45% trên Desktop) -->
    <div class="pp-related-adt__header-grid">
      <div class="pp-text-adt-related">
        <div class="pp-badge-adt-related">
          <span class="pp-badge-accent-adt-related">//</span> <?php echo esc_html('THI CÔNG & GIÁM SÁT'); ?>
        </div>
        
        <h3 class="pp-title-adt-related">
          <?php echo esc_html('Quy Hoạch Bếp Chính & Quầy Bar Chuyên Nghiệp'); ?>
        </h3>
        
        <div class="pp-body-adt-related">
          <p class="pp-paragraph-adt-related">
            <?php echo esc_html('Khu vực bếp chính đóng vai trò cốt lõi vì đây là nơi kiến tạo các tác phẩm ẩm thực độc đáo làm nên uy tín của nhà hàng. Không gian được quy hoạch tỉ mỉ từ khâu lưu trữ, sơ chế, chế biến đến vệ sinh nhằm tối ưu hóa sự thoải mái cho đầu bếp và tuân thủ nghiêm ngặt các quy định về an toàn thực phẩm.'); ?>
          </p>
          <p class="pp-paragraph-adt-related">
            <?php echo esc_html('Song song đó, khu vực quầy bar lớn được bố trí dụng cụ pha chế một cách khoa học và gọn gàng, giúp các bartender dễ dàng thao tác nhanh chóng và phô diễn tài năng pha chế nghệ thuật, mang lại không gian trình diễn đẹp mắt và đẳng cấp chuyên nghiệp.'); ?>
          </p>
        </div>
      </div>

      <!-- Khối Quote sang trọng bên phải (Đã sửa lỗi chữ dấu tổ hợp diacritics rải rác) -->
      <div class="pp-related-quote-wrapper">
        <blockquote class="pp-related-quote">
          <p>
            <?php echo esc_html('“Sự khoa học trong sắp đặt thiết bị không chỉ nâng cao hiệu suất làm việc, mà còn khơi nguồn cảm hứng sáng tạo tối đa cho người nghệ sĩ F&B.”'); ?>
          </p>
          <cite class="pp-related-quote-author"><?php echo esc_html('KỸ SƯ QUY HOẠCH SAIGON HORECA'); ?></cite>
        </blockquote>
      </div>
    </div>

    <!-- Phần dưới: Lưới hình ảnh cực lớn (Full Width Grid) giúp xem rõ chi tiết bản vẽ -->
    <div class="pp-media-adt-related-full">
      <div class="pp-related-mosaic-full">
        
        <!-- Ảnh 1: Bản vẽ bếp chính chiếm 100% hàng trên (Cực to rõ) -->
        <div class="pp-related-mosaic-item-full pp-related-mosaic-item-full--1">
          <div class="pp-related-image-wrapper pp-image-container-shared">
            <img src="<?php echo esc_url(sgh_img('amdang-typhoon/amdang-typhoon-ban-ve-bep-chinh.jpg')); ?>" alt="<?php echo esc_attr('Bản vẽ bố trí thiết bị khu bếp chính Amdang Typhoon - Saigon Horeca'); ?>" loading="lazy" decoding="async">
            <div class="pp-image-caption-shared"><?php echo esc_html__('Bản vẽ thiết kế quy hoạch tổng thể mặt bằng công nghệ bếp chính một chiều', 'saigonhoreca'); ?></div>
          </div>
        </div>

        <!-- Hàng dưới: 2 ảnh chia đôi song song -->
        <div class="pp-related-mosaic-row-sub">
          <!-- Ảnh 2: Bản vẽ quầy bar -->
          <div class="pp-related-mosaic-item-full pp-related-mosaic-item-full--2">
            <div class="pp-related-image-wrapper pp-image-container-shared">
              <img src="<?php echo esc_url(sgh_img('amdang-typhoon/amdang-typhoon-ban-ve-quay-bar.png')); ?>" alt="<?php echo esc_attr('Bản vẽ chi tiết bố trí thiết bị quầy bar lớn', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
              <div class="pp-image-caption-shared"><?php echo esc_html__('Bản vẽ chi tiết công nghệ bố trí thiết bị quầy bar pha chế đồ uống', 'saigonhoreca'); ?></div>
            </div>
          </div>

          <!-- Ảnh 3: Thực tế quầy bar -->
          <div class="pp-related-mosaic-item-full pp-related-mosaic-item-full--3">
            <div class="pp-related-image-wrapper pp-image-container-shared">
              <img src="<?php echo esc_url(sgh_img('amdang-typhoon/amdang-typhoon-thuc-te-quay-bar.jpeg')); ?>" alt="<?php echo esc_attr('Thực tế quầy bar lớn nhà hàng Amdang Typhoon - Saigon Horeca'); ?>" loading="lazy" decoding="async">
              <div class="pp-image-caption-shared"><?php echo esc_html__('Hoàn thiện thực tế không gian quầy bar đậm chất nghệ thuật Thái Lan với hình vẽ thần hộ pháp Yaksha', 'saigonhoreca'); ?></div>
            </div>
          </div>
        </div>

      </div>
    </div>

  </div>
</section>

<?php /* T-034: merged from cta.php (cũ section 8) */ ?>
<section class="pp-section-adt-cta">
  <!-- High-end technical background elements -->
  <div class="pp-cta-grid-pattern" aria-hidden="true"></div>
  <div class="pp-cta-ambient-glow" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <div class="pp-cta-split pp-cta-center-layout">
      
      <!-- Center Column: Copy & Interactive Specs Showcase -->
      <div class="pp-cta-copy">
        <div class="pp-badge-adt-cta">
          <span class="pp-badge-accent-adt-cta">//</span> <?php echo esc_html('ĐỒNG HÀNH THIẾT KẾ'); ?>
        </div>

        <h2 class="pp-title-adt-cta">
          <?php echo 'Kiến Tạo Không Gian <span class="pp-highlight-gold">Bếp Chuẩn Michelin</span> Cho Dự Án F&B Của Bạn'; ?>
        </h2>

        <div class="pp-desc-adt-cta">
          <p class="pp-desc-paragraph">
            <?php echo esc_html('Hơn 3/4 hệ thống thiết bị bếp nướng lò than đặc thù và quầy mì di động của Amdang Typhoon đều được Saigon Horeca thiết kế và gia công sản xuất trực tiếp theo tiêu chuẩn cơ khí inox cao cấp nhất.'); ?>
          </p>
          <p class="pp-desc-paragraph">
            <?php echo esc_html('Bạn đang có ý tưởng cho một dự án nhà hàng sang trọng hay mô hình F&B độc đáo đạt tiêu chuẩn quốc tế? Hãy để Saigon Horeca đồng hành cùng bạn trên con đường kiến tạo.'); ?>
          </p>
        </div>

        <!-- Interactive Specs Horizontal Grid -->
        <div class="pp-cta-specs-list pp-cta-specs-grid">
          <div class="pp-cta-spec-item">
            <span class="pp-cta-spec-num">01</span>
            <div class="pp-cta-spec-content">
              <strong><?php echo esc_html('QUY HOẠCH TỐI ƯU'); ?></strong>
              <span><?php echo esc_html('Bản vẽ mặt bằng 2D/3D chi tiết, tối ưu luồng di chuyển một chiều chuẩn F&B.'); ?></span>
            </div>
          </div>
          
          <div class="pp-cta-spec-item">
            <span class="pp-cta-spec-num">02</span>
            <div class="pp-cta-spec-content">
              <strong><?php echo esc_html('SẢN XUẤT ĐỘC BẢN'); ?></strong>
              <span><?php echo esc_html('Sản xuất trực tiếp thiết bị inox SUS304 cao cấp theo module thực đơn chuyên biệt.'); ?></span>
            </div>
          </div>

          <div class="pp-cta-spec-item">
            <span class="pp-cta-spec-num">03</span>
            <div class="pp-cta-spec-content">
              <strong>HỆ THỐNG AIRFLOW</strong>
              <span><?php echo esc_html('Tính toán thông gió, hút khói bếp lò than áp lực cao, khử mùi triệt để.'); ?></span>
            </div>
          </div>
        </div>

        <div class="pp-btn-wrapper-adt-cta">
          <a href="<?php echo esc_url(home_url('/lien-he/')); ?>" class="pp-btn-adt-cta">
            <span class="pp-btn-adt-cta-text"><?php echo esc_html('LIÊN HỆ TƯ VẤN NGAY'); ?></span>
            <svg class="pp-btn-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <line x1="5" y1="12" x2="19" y2="12"></line>
              <polyline points="12 5 19 12 12 19"></polyline>
            </svg>
          </a>
        </div>
      </div>

    </div>
  </div>
</section>

