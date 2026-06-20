<?php
/**
 * Project Pillar — heiwa-sushi-omakase
 * Section #6: gallery —
 *   (a) "Kỹ thuật vận hành": framed equipment image + Hoshizaki/inox feature list
 *   (b) "Hoàn thiện thực tế": closing copy + zen quote + 3-image construction collage
 * Matches the live production layout; tokens only, real images only.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-hwa pp-gallery-hwa scroll-reveal">
  <div class="pp-hwa-ambient-glow pp-hwa-ambient-glow--bl" aria-hidden="true"></div>

  <div class="pp-container-shared">
    <div class="pp-gallery-hwa__grid">

      <!-- Cột trái: ảnh hệ thống thiết bị -->
      <div class="pp-gallery-hwa__media scroll-reveal">
        <div class="pp-image-container-shared pp-frame-hwa pp-gallery-hwa__figure">
          <img src="<?php echo sgh_img('heiwa-sushi-omakase/heiwa-sushi-omakase-chi-tiet-co-khi.webp'); ?>" alt="<?php echo esc_attr__('Hệ thống thiết bị tủ đông Hoshizaki và bàn inox cao cấp tại Heiwa Sushi - Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
          <div class="pp-image-caption-shared"><?php echo esc_html__('Chi tiết cơ khí inox 304 gia công riêng đạt chuẩn Omakase Fresh.', 'saigonhoreca'); ?></div>
        </div>
      </div>

      <!-- Cột phải: thuyết minh + lưới tính năng thiết bị -->
      <div class="pp-gallery-hwa__text scroll-reveal">
        <div class="pp-text-hwa__badge"><span class="pp-text-hwa__badge-accent">//</span> <?php echo esc_html__('KỸ THUẬT VẬN HÀNH', 'saigonhoreca'); ?></div>
        <h2 class="pp-text-hwa__title"><?php echo esc_html__('Hệ Thống Thiết Bị Hoshizaki & Cơ Khí Inox Cao Cấp', 'saigonhoreca'); ?></h2>
        <div class="pp-text-hwa__divider" aria-hidden="true"></div>
        <div class="pp-text-hwa__body">
          <p><?php echo esc_html__('Trái tim vận hành của bếp Heiwa Sushi nằm ở các giải pháp thiết bị lạnh chất lượng cao từ thương hiệu Hoshizaki Nhật Bản. Tủ đông và tủ mát bảo quản nguyên liệu tươi sống được tính toán vị trí lắp đặt chính xác, tối ưu hóa tối đa lối đi và tầm với của đầu bếp trong giờ cao điểm.', 'saigonhoreca'); ?></p>
          <p><?php echo esc_html__('Hệ thống giá treo tường, chậu rửa, bàn inox 304 cao cấp do Saigon Horeca gia công sản xuất riêng cho dự án đều đạt tiêu chuẩn cơ khí hoàn mỹ. Mỗi góc chấn ép, vết hàn khí trơ đều được xử lý mài bóng mịn, triệt tiêu khe hở tích tụ vi khuẩn để đảm bảo tiêu chí Omakase Fresh khắt khe.', 'saigonhoreca'); ?></p>
        </div>

        <div class="pp-gallery-hwa__features">
          <div class="pp-gallery-hwa__feature">
            <span class="pp-gallery-hwa__feature-icon" aria-hidden="true">
              <svg viewBox="0 0 40 40" fill="none" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round">
                <rect x="9" y="4" width="22" height="32" rx="1.5" />
                <line x1="20" y1="4" x2="20" y2="36" />
                <line x1="9" y1="20" x2="31" y2="20" />
                <line x1="18.5" y1="8" x2="18.5" y2="16" />
                <line x1="21.5" y1="8" x2="21.5" y2="16" />
                <line x1="18.5" y1="24" x2="18.5" y2="32" />
                <line x1="21.5" y1="24" x2="21.5" y2="32" />
                <line x1="13" y1="36" x2="13" y2="38.5" stroke-width="1.75" />
                <line x1="27" y1="36" x2="27" y2="38.5" stroke-width="1.75" />
              </svg>
            </span>
            <span class="pp-gallery-hwa__feature-text">
              <span class="pp-gallery-hwa__feature-title"><?php echo esc_html__('Tủ Lạnh Hoshizaki', 'saigonhoreca'); ?></span>
              <span class="pp-gallery-hwa__feature-desc"><?php echo esc_html__('Bảo quản hải sản sashimi ở nhiệt độ tối ưu nhất.', 'saigonhoreca'); ?></span>
            </span>
          </div>

          <div class="pp-gallery-hwa__feature">
            <span class="pp-gallery-hwa__feature-icon" aria-hidden="true">
              <svg viewBox="0 0 40 40" fill="none" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round">
                <polygon points="6,15 22,9 34,15 18,21" />
                <line x1="6" y1="15" x2="6" y2="17.5" />
                <line x1="18" y1="21" x2="18" y2="23.5" />
                <line x1="34" y1="15" x2="34" y2="17.5" />
                <line x1="8" y1="17.5" x2="8" y2="32.5" />
                <line x1="18" y1="23.5" x2="18" y2="34.5" />
                <line x1="32" y1="17.5" x2="32" y2="31.5" />
              </svg>
            </span>
            <span class="pp-gallery-hwa__feature-text">
              <span class="pp-gallery-hwa__feature-title"><?php echo esc_html__('Inox 304 Chuẩn Sạch', 'saigonhoreca'); ?></span>
              <span class="pp-gallery-hwa__feature-desc"><?php echo esc_html__('Kháng khuẩn tuyệt đối, chống gỉ sét và dễ vệ sinh.', 'saigonhoreca'); ?></span>
            </span>
          </div>

          <div class="pp-gallery-hwa__feature">
            <span class="pp-gallery-hwa__feature-icon" aria-hidden="true">
              <svg viewBox="0 0 40 40" fill="none" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round">
                <rect x="6" y="11.5" width="28" height="2.5" rx="0.5" />
                <line x1="8" y1="18.5" x2="32" y2="18.5" stroke-dasharray="1.5 1.5" />
                <path d="M 13,18.5 C 13,20.5 12,21.5 11,21.5 C 10,21.5 10,19.5 11,18.5" />
                <path d="M 20,18.5 C 20,20.5 19,21.5 18,21.5 C 17,21.5 17,19.5 18,18.5" />
                <rect x="15" y="25" width="6" height="5" rx="1" />
                <path d="M 27,18.5 C 27,20.5 26,21.5 25,21.5 C 24,21.5 24,19.5 25,18.5" />
              </svg>
            </span>
            <span class="pp-gallery-hwa__feature-text">
              <span class="pp-gallery-hwa__feature-title"><?php echo esc_html__('Giá Treo Tiện Lợi', 'saigonhoreca'); ?></span>
              <span class="pp-gallery-hwa__feature-desc"><?php echo esc_html__('Tối ưu hóa không gian, tăng diện tích thao tác thực tế.', 'saigonhoreca'); ?></span>
            </span>
          </div>

          <div class="pp-gallery-hwa__feature">
            <span class="pp-gallery-hwa__feature-icon" aria-hidden="true">
              <svg viewBox="0 0 40 40" fill="none" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round">
                <rect x="6" y="15" width="28" height="19" rx="1.5" />
                <rect x="9" y="18" width="22" height="13" rx="0.5" />
                <ellipse cx="20" cy="24.5" rx="3" ry="1.5" />
                <path d="M 13,18 L 13,9 C 13,5 18,5 18,8.5 L 18,10.5" stroke-width="1.75" />
              </svg>
            </span>
            <span class="pp-gallery-hwa__feature-text">
              <span class="pp-gallery-hwa__feature-title"><?php echo esc_html__('Hệ Chậu Rửa Ergonomics', 'saigonhoreca'); ?></span>
              <span class="pp-gallery-hwa__feature-desc"><?php echo esc_html__('Thiết kế sâu lòng thông minh ngăn văng nước bẩn ra ngoài.', 'saigonhoreca'); ?></span>
            </span>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<?php /* Khối đúc kết / hoàn thiện thực tế — header editorial + MOSAIC bất đối xứng 3 ảnh thi công + quote (KHÔNG tạo route riêng). */ ?>
<section class="pp-section-hwa pp-section-hwa--alt pp-gallery-hwa__closing scroll-reveal">
  <div class="pp-hwa-ambient-glow pp-hwa-ambient-glow--tr" aria-hidden="true"></div>
  <div class="pp-hwa-ambient-glow pp-hwa-ambient-glow--bl" aria-hidden="true"></div>

  <!-- Số chìm kỹ thuật watermark (06) cho gallery hoàn thiện -->
  <span class="pp-gallery-hwa__watermark" aria-hidden="true">06</span>

  <div class="pp-container-shared">

    <!-- Header editorial: badge + tiêu đề + divider + đúc kết + quote thiền -->
    <div class="pp-gallery-hwa__closing-head scroll-reveal">
      <div class="pp-text-hwa__badge"><span class="pp-text-hwa__badge-accent">//</span> <?php echo esc_html__('HOÀN THIỆN THỰC TẾ', 'saigonhoreca'); ?></div>
      <h2 class="pp-text-hwa__title"><?php echo esc_html__('Kiến Tạo Chuẩn Xác Từng Chi Tiết Cơ Khí', 'saigonhoreca'); ?></h2>
      <div class="pp-text-hwa__divider" aria-hidden="true"></div>
      <div class="pp-text-hwa__body">
        <p><?php echo esc_html__('Tại Heiwa Sushi, sự hoàn mỹ không chỉ xuất hiện trên các đĩa thức ăn tuyệt hảo của người nghệ sĩ Omakase, mà được đảm bảo xuyên suốt từ cốt lõi hạ tầng kỹ thuật. Saigon Horeca cam kết thi công thực tế bám sát 100% bản vẽ kỹ thuật kỹ lưỡng nhất.', 'saigonhoreca'); ?></p>
        <p><?php echo esc_html__('Quy trình lắp ráp thiết bị, căn chỉnh độ phẳng mặt bàn inox, tối ưu hóa các đường đi ống thoát sàn và hệ thống hút mùi đều được giám sát khắt khe bởi các kỹ sư dày dặn kinh nghiệm, mang đến một gian bếp an toàn, hiệu năng vượt trội.', 'saigonhoreca'); ?></p>
      </div>
      <blockquote class="pp-gallery-hwa__quote">
        <p><?php echo esc_html__('“Sự tinh tế không nằm ở những gì hào nhoáng lộ thiên, mà ẩn sâu trong sự hoàn hảo của những chi tiết cơ khí âm thầm nâng bước người đầu bếp sáng tạo nghệ thuật.”', 'saigonhoreca'); ?></p>
        <cite class="pp-gallery-hwa__quote-author"><?php echo esc_html__('Đội Ngũ Kỹ Sư Saigon Horeca', 'saigonhoreca'); ?></cite>
      </blockquote>
    </div>

    <!-- MOSAIC → lưới đồng nhất 3 ảnh thi công (mọi tile cùng tỉ lệ 4/3, không khe đen) -->
    <div class="pp-gallery-hwa__mosaic scroll-reveal">

      <!-- Tile 1 -->
      <div class="pp-image-container-shared pp-frame-hwa pp-gallery-hwa__item">
        <img src="<?php echo sgh_img('heiwa-sushi-omakase/heiwa-sushi-omakase-bat-com-tron-ca-hoi-va-trung-ca.webp'); ?>" alt="<?php echo esc_attr__('Bát cơm trộn cá hồi và trứng cá tại Heiwa Sushi - Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="900" height="650">
        <div class="pp-image-caption-shared"><?php echo esc_html__('Bát cơm trộn cá hồi & trứng cá tươi.', 'saigonhoreca'); ?></div>
      </div>

      <!-- Tile 2 -->
      <div class="pp-image-container-shared pp-frame-hwa pp-gallery-hwa__item">
        <img src="<?php echo sgh_img('heiwa-sushi-omakase/heiwa-sushi-omakase-dia-sushi-bo-va-luon-nhat.webp'); ?>" alt="<?php echo esc_attr__('Đĩa sushi cuốn phủ bơ quả áp lửa tại Heiwa Sushi - Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="800" height="600">
        <div class="pp-image-caption-shared"><?php echo esc_html__('Sushi cuốn phủ bơ quả áp lửa, xếp vây tinh tế trên khay gỗ.', 'saigonhoreca'); ?></div>
      </div>

      <!-- Tile 3 -->
      <div class="pp-image-container-shared pp-frame-hwa pp-gallery-hwa__item">
        <img src="<?php echo sgh_img('heiwa-sushi-omakase/heiwa-sushi-omakase-ruou-sake-dassai-va-hau-hyogo.webp'); ?>" alt="<?php echo esc_attr__('Rượu Sake Dassai và hàu Hyogo tại Heiwa Sushi - Saigon Horeca', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="800" height="600">
        <div class="pp-image-caption-shared"><?php echo esc_html__('Sake Dassai 23 & hàu Hyogo thượng hạng.', 'saigonhoreca'); ?></div>
      </div>

    </div>
  </div>
</section>
