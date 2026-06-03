<?php
/**
 * Project Pillar — the-cheezy-time
 * Section #2: intro (Thiết kế đan xen nghệ thuật Alternating Storytelling)
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-tct pp-section-tct--alt">
  <!-- Gradient Grids chạy mờ dưới nền -->
  <svg class="pp-tct-bg-grid" width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
    <defs>
      <pattern id="grid-pattern" width="40" height="40" patternUnits="userSpaceOnUse">
        <path d="M 40 0 L 0 0 0 40" fill="none" stroke="rgba(255, 255, 255, 0.015)" stroke-width="1"/>
      </pattern>
    </defs>
    <rect width="100%" height="100%" fill="url(#grid-pattern)" />
  </svg>

  <!-- ĐƯỜNG DẪN NGHỆ THUẬT KẾT NỐI TOÀN BỘ TRANG (Narrative Connecting Line) -->
  <div class="pp-tct-narrative-line"></div>

  <div class="pp-container-shared">
    
    <!-- Tiêu đề lớn (Intro Header) -->
    <div class="pp-tct-intro__header">
      <span class="pp-text-tct__divider" aria-hidden="true"></span>
      <h2 class="pp-text-tct__title">
        <?php echo esc_html__('Một khoảnh khắc ngắn trên bàn ăn, một guồng quay dài phía sau cánh cửa bếp', 'saigonhoreca'); ?>
      </h2>
      <div class="pp-text-tct__lead">
        <p>
          <strong><?php echo esc_html__('Ở đó, người ta không cần bảng hiệu. Chỉ một cây bẹo, một ánh mắt, một tiếng gọi – thế là đủ để hiểu nhau.', 'saigonhoreca'); ?></strong>
        </p>
      </div>
    </div>

    <!-- Dòng chảy câu chuyện (Alternating Rows) -->
    <div class="pp-tct-intro__story">
      
      <!-- HÀNG 1: BẢN SẮC SÔNG NƯỚC (Asymmetric Layout - Chữ 38% / Ảnh 62%) -->
      <div class="pp-tct-row pp-tct-row--layout-river pp-tct-row--asymmetric-1">
        
        <!-- Cột chữ -->
        <div class="pp-tct-row__text">
          <div class="pp-intro-card-tct pp-intro-card-tct--river">
            <h3 class="pp-tct-card-heading"><?php echo esc_html__('Hồn sông nước & Vật liệu', 'saigonhoreca'); ?></h3>
            
            <div class="pp-intro-card-tct__body">
              <div class="pp-tct-highlight-quote">
                <p>
                  <span class="pp-tct-dropcap">C</span><?php echo esc_html__('hợ nổi không ồn ào khoe sắc. Nó lặng lẽ giữ lại cái hồn của miền sông nước: sự hài hòa với thiên nhiên, sự chân thành trong cách con người đối đãi với nhau.', 'saigonhoreca'); ?>
                </p>
              </div>
              <p class="pp-tct-desc-p">
                <?php echo esc_html__('Và chính tinh thần ấy đã trở thành nền tảng cho toàn bộ concept của The Cheezy Time – từ không gian kiến trúc, vật liệu, cho đến trải nghiệm ẩm thực.', 'saigonhoreca'); ?>
              </p>
              <p class="pp-tct-desc-p">
                <?php echo esc_html__('The Cheezy Time chọn kể câu chuyện miền Tây qua vật liệu: ánh inox copper vàng đồng mang sắc thái thời gian, gợi nhắc những giá trị truyền đời; chất liệu gỗ đậm màu đất phù sa trầm lắng; và sỏi polymer tựa dấu vết của mặt đất, của từng bước chân bồi tụ phù sa.', 'saigonhoreca'); ?>
              </p>
            </div>
          </div>
        </div>

        <!-- Cột ảnh (Đa dạng các khung hình chữ nhật ngang uốn lượn mây nước) -->
        <div class="pp-tct-row__media pp-tct-media-river">
          <!-- SVG trang trí nét vẽ kỹ thuật -->
          <svg class="pp-tct-svg-frame-decor" viewBox="0 0 400 400" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect class="decor-rect-rotate" x="20" y="20" width="360" height="360" rx="12" stroke="rgba(245, 166, 35, 0.08)" stroke-width="1"/>
            <circle class="decor-circle-scale" cx="200" cy="200" r="160" stroke="rgba(245, 166, 35, 0.04)" stroke-width="1" stroke-dasharray="6 6"/>
          </svg>

          <!-- Ảnh ngang lớn với viền mây uốn lượn -->
          <div class="pp-tct-luxury-frame pp-tct-luxury-frame--landscape-main">
            <span class="pp-tct-corner pp-tct-corner--tl"></span>
            <span class="pp-tct-corner pp-tct-corner--tr"></span>
            <span class="pp-tct-corner pp-tct-corner--bl"></span>
            <span class="pp-tct-corner pp-tct-corner--br"></span>
            <div class="pp-image-container-shared">
              <img src="<?php echo sgh_img('the-cheezy-time/the-cheezy-time-interior.jpg'); ?>" alt="<?php echo esc_attr__('Không gian nhà hàng The Cheezy Time Cần Thơ', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
              <div class="pp-image-caption-shared">
                <?php echo esc_html__('Một góc bài trí tinh tế bên trong nhà hàng, nơi các chất liệu gỗ, kim loại và ánh sáng vàng ấm hòa quyện tạo nên không khí lãng mạn đậm chất Ý.', 'saigonhoreca'); ?>
              </div>
            </div>
          </div>

          <!-- Ảnh ngang nhỏ chồng đè với viền mây uốn lượn -->
          <div class="pp-tct-luxury-frame pp-tct-luxury-frame--landscape-sub">
            <span class="pp-tct-corner pp-tct-corner--tl"></span>
            <span class="pp-tct-corner pp-tct-corner--tr"></span>
            <span class="pp-tct-corner pp-tct-corner--bl"></span>
            <span class="pp-tct-corner pp-tct-corner--br"></span>
            <div class="pp-image-container-shared">
              <img src="<?php echo sgh_img('the-cheezy-time/the-cheezy-time-dining-space.jpg'); ?>" alt="<?php echo esc_attr__('Bàn ghế gỗ trầm và quầy bar trung tâm tại The Cheezy Time', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
              <div class="pp-image-caption-shared">
                <?php echo esc_html__('Không gian ẩm thực ấm cúng tại The Cheezy Time với bàn ghế gỗ sồi tự nhiên và hệ trần thép uốn cong tạo hình mui thuyền độc bản.', 'saigonhoreca'); ?>
              </div>
            </div>
          </div>
        </div>

      </div>

      <!-- HÀNG 2: DÒNG CHẢY HỆ THỐNG (Asymmetric Layout - Ảnh 60% / Chữ 40%) -->
      <div class="pp-tct-row pp-tct-row--layout-blueprint pp-tct-row--asymmetric-2">
        
        <!-- Cột ảnh (Ảnh ngang uốn lượn lớn) -->
        <div class="pp-tct-row__media pp-tct-media-blueprint">
          <!-- SVG vẽ viền mạch điện kỹ thuật chạy xung quanh ảnh -->
          <svg class="pp-tct-circuit-decor" viewBox="0 0 500 350" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path class="circuit-path-1" d="M 10 10 L 490 10 L 490 340 L 10 340 Z" stroke="rgba(245, 166, 35, 0.1)" stroke-width="1" stroke-dasharray="15 15"/>
            <circle class="circuit-node-1" cx="10" cy="10" r="3" fill="var(--gold)"/>
            <circle class="circuit-node-2" cx="490" cy="340" r="3" fill="var(--gold)"/>
          </svg>

          <div class="pp-tct-luxury-frame pp-tct-luxury-frame--blueprint">
            <span class="pp-tct-corner pp-tct-corner--tl"></span>
            <span class="pp-tct-corner pp-tct-corner--tr"></span>
            <span class="pp-tct-corner pp-tct-corner--bl"></span>
            <span class="pp-tct-corner pp-tct-corner--br"></span>
            
            <div class="pp-image-container-shared">
              <img src="<?php echo sgh_img('the-cheezy-time/the-cheezy-time-interior-curves.jpg'); ?>" alt="<?php echo esc_attr__('Hệ trần cong nghệ thuật tại The Cheezy Time', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
              <div class="pp-image-caption-shared">
                <?php echo esc_html__('Hệ vòm tròn khoét rỗng nghệ thuật và trần thép lượn sóng tạo nên chiều sâu thị giác ấn tượng cho không gian ẩm thực miền Tây sông nước kết hợp văn hóa Ý.', 'saigonhoreca'); ?>
              </div>
            </div>
          </div>
        </div>

        <!-- Cột chữ -->
        <div class="pp-tct-row__text">

          <div class="pp-intro-card-tct pp-intro-card-tct--blueprint">
            <div class="pp-tct-timeline-indicator"></div>
            
            <div class="pp-intro-card-tct__header">
              <svg class="pp-tct-header-icon-anim" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg>
              <h3 class="pp-intro-card-tct__title"><?php echo esc_html__('Dòng chảy vận hành', 'saigonhoreca'); ?></h3>
            </div>
            
            <div class="pp-intro-card-tct__body">
              <div class="pp-tct-timeline-item">
                <div class="pp-tct-timeline-node-wrapper">
                  <span class="pp-tct-timeline-node"></span>
                  <span class="pp-tct-timeline-pulse"></span>
                </div>
                <p class="pp-tct-desc-p">
                  <?php echo esc_html__('Mỗi chất liệu tạo nên một cuộc đối thoại giữa văn hoá, không gian và cảm xúc. Tinh thần "dòng chảy" này kéo dài đến cả những không gian phía sau – nơi quyết định nhịp sống thực sự của toàn bộ nhà hàng, tựa như cách chợ nổi đã tồn tại qua bao thế hệ.', 'saigonhoreca'); ?>
                </p>
              </div>
              <div class="pp-tct-timeline-item">
                <div class="pp-tct-timeline-node-wrapper">
                  <span class="pp-tct-timeline-node"></span>
                  <span class="pp-tct-timeline-pulse"></span>
                </div>
                <p class="pp-tct-desc-p">
                  <?php echo esc_html__('Một concept lấy cảm hứng từ chợ nổi đòi hỏi sự linh hoạt tuyệt đối. Nó yêu cầu căn bếp phía sau phải đủ kỷ luật để vận hành hiệu quả, nhưng cũng đủ "mềm" để thích nghi với nhịp sống liên tục của nhà hàng.', 'saigonhoreca'); ?>
                </p>
              </div>
            </div>
          </div>
        </div>

      </div>

      <!-- HÀNG 3: TRÁI TIM ĐỎ LỬA (Asymmetric Layout - Chữ 38% / Ảnh 62% Panoramic) -->
      <div class="pp-tct-row pp-tct-row--layout-furnace pp-tct-row--asymmetric-3">
        
        <!-- Cột chữ -->
        <div class="pp-tct-row__text">
          <div class="pp-intro-card-tct pp-intro-card-tct--furnace">
            <div class="pp-card-fire-glow"></div>
            <h3 class="pp-tct-card-heading"><?php echo esc_html__('Trái tim căn bếp', 'saigonhoreca'); ?></h3>
            
            <div class="pp-intro-card-tct__body">
              <div class="pp-tct-callout-panel">
                <span class="pp-tct-callout-quote-icon">“</span>
                <p>
                  <strong><?php echo esc_html__('Một món ăn được đặt xuống bàn chỉ trong vài phút. Nhưng để khoảnh khắc ấy trọn vẹn, phía sau là cả một hệ thống vận hành không được phép sai nhịp.', 'saigonhoreca'); ?></strong>
                </p>
              </div>
              <p class="pp-tct-desc-p">
                <?php echo esc_html__('Đó là căn bếp luôn đỏ lửa, nơi từng chiếc bánh pizza được canh đúng nhiệt, từng phần mỳ đút lò ra đúng thời điểm. Đó là sự phối hợp thầm lặng, chính xác, không một nhịp thừa giữa bếp và đội ngũ phục vụ, kéo dài liên tục từ trưa đến tối muộn.', 'saigonhoreca'); ?>
              </p>
            </div>
          </div>
        </div>

        <!-- Cột ảnh (Panoramic ngang siêu rộng với viền mây) -->
        <div class="pp-tct-row__media pp-tct-media-furnace">
          <svg class="pp-tct-fire-particles" viewBox="0 0 400 300" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle class="fire-p-1" cx="80" cy="280" r="3" fill="#d27d56" opacity="0.6"/>
            <circle class="fire-p-2" cx="200" cy="260" r="4" fill="#f5a623" opacity="0.8"/>
            <circle class="fire-p-3" cx="320" cy="290" r="2" fill="#d27d56" opacity="0.5"/>
            <circle class="fire-p-4" cx="150" cy="270" r="3.5" fill="#f5a623" opacity="0.7"/>
          </svg>

          <div class="pp-tct-luxury-frame pp-tct-luxury-frame--furnace pp-tct-luxury-frame--panoramic">
            <span class="pp-tct-corner pp-tct-corner--tl"></span>
            <span class="pp-tct-corner pp-tct-corner--tr"></span>
            <span class="pp-tct-corner pp-tct-corner--bl"></span>
            <span class="pp-tct-corner pp-tct-corner--br"></span>
            
            <div class="pp-image-container-shared">
              <img src="<?php echo sgh_img('the-cheezy-time/the-cheezy-time-pizza-oven.jpg'); ?>" alt="<?php echo esc_attr__('Lò nướng Pizza lò gạch truyền thống tại The Cheezy Time', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
              <div class="pp-image-caption-shared">
                <?php echo esc_html__('Lò nướng Pizza bằng đất sét nung đỏ bóng đặt tại quầy bếp mở, nơi thực khách có thể trực tiếp chiêm ngưỡng nghệ thuật làm pizza thủ công.', 'saigonhoreca'); ?>
              </div>
            </div>
          </div>
        </div>

      </div>

    </div>
  </div>
</section>
