<?php
/**
 * Project Pillar — bambino-saigonhoreca
 * Section #2: intro — Mosaic 4 ảnh + Editorial Text
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-bb pp-intro-bam">
  <div class="pp-container-shared">
    <div class="pp-intro-bam__grid">

      <!-- Cột trái: Văn bản editorial -->
      <div class="pp-intro-bam__text-col">
        <div class="pp-intro-bam__badge">
          <span class="pp-intro-bam__badge-dot"></span>
          Superclub Experience
        </div>

        <h2 class="pp-intro-bam__title">
          Bambino<br>
          <span class="pp-intro-bam__title-sub">Superclub</span>
        </h2>

        <div class="pp-intro-bam__body">
          <p class="pp-intro-bam__lead"><?php echo esc_html__('Bước vào thế giới của Bambino Superclub — nơi điều bình thường trở nên phi thường, và trời đêm hóa thân thành dòng năng lượng sống động nhịp nhàng.', 'saigonhoreca'); ?></p>
          <p><?php echo esc_html__('Nằm yên bên trong lòng sôi động của Thành phố Hồ Chí Minh, đây không chỉ là điểm đến giải trí; đó là trải nghiệm đắm mình vào thế giới vượt lên trên bình thường — những đêm khó quên và khoảnh khắc sôi động không thể nào quên.', 'saigonhoreca'); ?></p>
        </div>

        <div class="pp-intro-bam__quote-card">
          <p><?php echo esc_html__('"SAIGON HORECA tự hào tham gia thiết kế và phân phối thiết bị nhà bếp bằng thép không gỉ cao cấp. Sự hợp tác không chỉ dừng lại ở chức năng — nó hài hòa sáng tạo với tính thực tế."', 'saigonhoreca'); ?></p>
          <span class="pp-intro-bam__quote-author">— Saigon Horeca Editorial</span>
        </div>

        <div class="pp-intro-bam__info-chips">
          <span class="pp-intro-bam__chip">📍 29-31 Tôn Thất Thiệp, Quận 1</span>
          <span class="pp-intro-bam__chip">⏰ Bar Bino: 22:00 – 02:00</span>
          <span class="pp-intro-bam__chip">⚡ Fri &amp; Sat (22:00 – 02:00)</span>
        </div>
      </div>

      <!-- Cột phải: 2-Column Asymmetric Staggered Grid (Đẹp không chồng chéo, không cắt ảnh) -->
      <div class="pp-intro-bam__media-col">
        <!-- SVG chìm phía sau nền -->
        <div class="pp-intro-bam__svg-backdrop">
          <svg viewBox="0 0 400 400" fill="none" xmlns="http://www.w3.org/2000/svg">
            <line x1="200" y1="0" x2="200" y2="400" stroke="rgba(212, 175, 55, 0.08)" stroke-width="0.75" stroke-dasharray="3 3" />
            <line x1="0" y1="200" x2="400" y2="200" stroke="rgba(212, 175, 55, 0.08)" stroke-width="0.75" stroke-dasharray="3 3" />
            <circle cx="200" cy="200" r="160" stroke="rgba(212, 175, 55, 0.05)" stroke-width="0.75" />
            <path d="M 40 200 A 160 160 0 0 1 200 40" stroke="var(--gold)" stroke-width="1" stroke-opacity="0.15" />
          </svg>
        </div>

        <div class="pp-intro-bam__mosaic">
          
          <!-- Cột Trái (Dịch chuyển nhẹ xuống dưới để tạo nhịp điệu bất đối xứng sang trọng) -->
          <div class="pp-intro-bam__mosaic-column pp-intro-bam__mosaic-column--left">
            
            <!-- Card 1: Khai vị Mortadella Ham -->
            <div class="pp-intro-bam__card pp-image-container-shared">
              <!-- Ngôi sao lấp lánh kiêu sa -->
              <svg class="pp-sparkle-star pp-sparkle-star--1" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0L14.6 9.4L24 12L14.6 14.6L12 24L9.4 14.6L0 12L9.4 9.4L12 0Z"/></svg>
              <svg class="pp-sparkle-star pp-sparkle-star--2" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0L14.6 9.4L24 12L14.6 14.6L12 24L9.4 14.6L0 12L9.4 9.4L12 0Z"/></svg>
              
              <svg class="card-corner-svg" viewBox="0 0 100 100" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M 0 12 L 0 0 L 12 0" stroke="rgba(212, 175, 55, 0.4)" stroke-width="1" />
                <path d="M 100 12 L 100 0 L 88 0" stroke="rgba(212, 175, 55, 0.4)" stroke-width="1" />
                <path d="M 0 88 L 0 100 L 12 100" stroke="rgba(212, 175, 55, 0.4)" stroke-width="1" />
                <path d="M 100 88 L 100 100 L 88 100" stroke="rgba(212, 175, 55, 0.4)" stroke-width="1" />
              </svg>
              <img src="<?php echo sgh_img('bambino/bambino-mon-an-khai-vi-mortadella-ham.png'); ?>" alt="<?php echo esc_attr__('Món khai vị Mortadella Ham và cocktail tại Bambino', 'saigonhoreca'); ?>" loading="eager" decoding="async">
              <div class="pp-image-caption-shared"><?php echo esc_html__('Món khai vị thịt nguội Mortadella cắt mỏng tinh tế và ly cocktail', 'saigonhoreca'); ?></div>
            </div>

            <!-- Card 2: Salad tươi & Pasta -->
            <div class="pp-intro-bam__card pp-image-container-shared">
              <!-- Ngôi sao lấp lánh kiêu sa -->
              <svg class="pp-sparkle-star pp-sparkle-star--3" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0L14.6 9.4L24 12L14.6 14.6L12 24L9.4 14.6L0 12L9.4 9.4L12 0Z"/></svg>
              <svg class="pp-sparkle-star pp-sparkle-star--4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0L14.6 9.4L24 12L14.6 14.6L12 24L9.4 14.6L0 12L9.4 9.4L12 0Z"/></svg>
              
              <svg class="card-corner-svg" viewBox="0 0 100 100" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M 0 12 L 0 0 L 12 0" stroke="rgba(212, 175, 55, 0.4)" stroke-width="1" />
                <path d="M 100 12 L 100 0 L 88 0" stroke="rgba(212, 175, 55, 0.4)" stroke-width="1" />
                <path d="M 0 88 L 0 100 L 12 100" stroke="rgba(212, 175, 55, 0.4)" stroke-width="1" />
                <path d="M 100 88 L 100 100 L 88 100" stroke="rgba(212, 175, 55, 0.4)" stroke-width="1" />
              </svg>
              <img src="<?php echo sgh_img('bambino/bambino-mon-an-salad-rau-kem-pasta.png'); ?>" alt="<?php echo esc_attr__('Salad rau xà lách tươi ăn kèm Pasta sốt kem cà chua tại Bambino', 'saigonhoreca'); ?>" loading="eager" decoding="async">
              <div class="pp-image-caption-shared"><?php echo esc_html__('Tô xà lách xanh tươi giòn kết hợp mì Ý Conchiglie sốt kem cà chua', 'saigonhoreca'); ?></div>
            </div>

          </div>

          <!-- Cột Phải -->
          <div class="pp-intro-bam__mosaic-column pp-intro-bam__mosaic-column--right">
            
            <!-- Card 3: Mì Ý Mafaldine -->
            <div class="pp-intro-bam__card pp-image-container-shared">
              <!-- Ngôi sao lấp lánh kiêu sa -->
              <svg class="pp-sparkle-star pp-sparkle-star--1" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0L14.6 9.4L24 12L14.6 14.6L12 24L9.4 14.6L0 12L9.4 9.4L12 0Z"/></svg>
              <svg class="pp-sparkle-star pp-sparkle-star--2" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0L14.6 9.4L24 12L14.6 14.6L12 24L9.4 14.6L0 12L9.4 9.4L12 0Z"/></svg>
              
              <svg class="card-corner-svg" viewBox="0 0 100 100" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M 0 12 L 0 0 L 12 0" stroke="rgba(212, 175, 55, 0.4)" stroke-width="1" />
                <path d="M 100 12 L 100 0 L 88 0" stroke="rgba(212, 175, 55, 0.4)" stroke-width="1" />
                <path d="M 0 88 L 0 100 L 12 100" stroke="rgba(212, 175, 55, 0.4)" stroke-width="1" />
                <path d="M 100 88 L 100 100 L 88 100" stroke="rgba(212, 175, 55, 0.4)" stroke-width="1" />
              </svg>
              <img src="<?php echo sgh_img('bambino/bambino-mon-an-mi-y-mafaldine-bolognese.png'); ?>" alt="<?php echo esc_attr__('Mì Ý Mafaldine Bolognese rắc phô mai tại Bambino', 'saigonhoreca'); ?>" loading="eager" decoding="async">
              <div class="pp-image-caption-shared"><?php echo esc_html__('Mì Ý sợi dẹt Mafaldine quyện sốt thịt băm Bolognese rắc tuyết phô mai', 'saigonhoreca'); ?></div>
            </div>

            <!-- Card 4: Phô mai Burrata -->
            <div class="pp-intro-bam__card pp-image-container-shared">
              <!-- Ngôi sao lấp lánh kiêu sa -->
              <svg class="pp-sparkle-star pp-sparkle-star--3" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0L14.6 9.4L24 12L14.6 14.6L12 24L9.4 14.6L0 12L9.4 9.4L12 0Z"/></svg>
              <svg class="pp-sparkle-star pp-sparkle-star--4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0L14.6 9.4L24 12L14.6 14.6L12 24L9.4 14.6L0 12L9.4 9.4L12 0Z"/></svg>
              
              <svg class="card-corner-svg" viewBox="0 0 100 100" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M 0 12 L 0 0 L 12 0" stroke="rgba(212, 175, 55, 0.4)" stroke-width="1" />
                <path d="M 100 12 L 100 0 L 88 0" stroke="rgba(212, 175, 55, 0.4)" stroke-width="1" />
                <path d="M 0 88 L 0 100 L 12 100" stroke="rgba(212, 175, 55, 0.4)" stroke-width="1" />
                <path d="M 100 88 L 100 100 L 88 100" stroke="rgba(212, 175, 55, 0.4)" stroke-width="1" />
              </svg>
              <img src="<?php echo sgh_img('bambino/bambino-mon-an-salad-pho-mai-burrata.png'); ?>" alt="<?php echo esc_attr__('Salad phô mai Burrata tươi ăn kèm thịt nguội Prosciutto tại Bambino', 'saigonhoreca'); ?>" loading="eager" decoding="async">
              <div class="pp-image-caption-shared"><?php echo esc_html__('Salad phô mai tươi Burrata kèm sốt cà chua bi confit và thịt muối Prosciutto', 'saigonhoreca'); ?></div>
            </div>

          </div>

        </div>
      </div>

    </div>
  </div>
</section>
