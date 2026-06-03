<?php
/**
 * Project Pillar — the-cheezy-time
 * Section #5: partnership — Nâng cấp layout sáng tạo & SVG Pizza, lò nướng nghệ thuật
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-tct pp-partnership-section-tct pp-section-tct--alt">
  <div class="pp-container-shared">
    
    <!-- BLOCK 1: TIÊU ĐỀ DẪN DẮT & LÒ NƯỚNG PIZZA CỔ ĐIỂN -->
    <div class="pp-part-block pp-part-block--intro">
      
      <!-- Cột trái: Tiêu đề và lời giới thiệu -->
      <div class="pp-part-intro-text">
        <span class="pp-text-tct__divider" aria-hidden="true"></span>
        <h2 class="pp-text-tct__title">
          <?php echo esc_html__('Khi căn bếp trở thành phần không thể tách rời của câu chuyện', 'saigonhoreca'); ?>
        </h2>
        <div class="pp-part-narrative">
          <p class="pp-part-narrative__lead">
            <?php echo esc_html__('The Cheezy Time không chỉ phục vụ món ăn. Nhà hàng mang đến một không gian có chiều sâu văn hoá – nơi truyền thống được kể bằng vật liệu, hình khối và cảm xúc. Và để câu chuyện ấy được kể trọn vẹn, căn bếp phía sau phải đủ mạnh để nâng đỡ mọi trải nghiệm phía trước.', 'saigonhoreca'); ?>
          </p>
          <p class="pp-part-narrative__sub">
            <?php echo esc_html__('Saigon Horeca cùng chủ đầu tư bắt đầu từ việc lên ý tưởng ban đầu cho concept bếp, đặt căn bếp vào đúng vai trò của nó trong tổng thể: một trung tâm điều phối, nơi mọi luồng nguyên liệu, con người và món ăn đều phải di chuyển mạch lạc như chính tinh thần chợ nổi mà The Cheezy Time theo đuổi.', 'saigonhoreca'); ?>
          </p>
        </div>
      </div>

      <!-- Cột phải: Lò nướng Pizza bằng gạch truyền thống (SVG Art) -->
      <div class="pp-part-oven-media">
        <div class="pp-oven-art-container">
          <!-- Ánh lửa phát quang mờ phía sau lò nướng -->
          <div class="pp-oven-fire-glow" aria-hidden="true"></div>
          
          <svg class="pp-pizza-oven-svg" viewBox="0 0 400 350" fill="none" xmlns="http://www.w3.org/2000/svg" aria-label="Hình vẽ lò nướng bánh pizza gạch củi cổ điển truyền thống">
            <title><?php echo esc_html__('Lò nướng pizza củi truyền thống của The Cheezy Time', 'saigonhoreca'); ?></title>
            <!-- Mái lò nướng bằng gạch đá -->
            <path class="oven-dome" d="M60 280 C60 100, 340 100, 340 280" stroke="var(--gold)" stroke-width="6" stroke-linecap="round" opacity="0.2" />
            <!-- Vòng gạch vòm cửa lò -->
            <path class="oven-arch-outer" d="M90 280 C90 140, 310 140, 310 280" stroke="rgba(245,166,35,0.15)" stroke-width="16" stroke-linecap="round" />
            <path class="oven-arch" d="M100 280 C100 160, 300 160, 300 280" stroke="var(--gold)" stroke-width="12" stroke-dasharray="14 6" opacity="0.8" />
            
            <!-- Khoang lò tối mờ ảo phía trong -->
            <path class="oven-interior" d="M120 280 C120 185, 280 185, 280 280 Z" fill="#14110f" stroke="rgba(245,166,35,0.25)" stroke-width="3" />
            
            <!-- Các khối củi đốt lò xếp chéo -->
            <g class="oven-wood">
              <rect x="160" y="258" width="70" height="14" rx="4" fill="#4d3222" stroke="#362217" stroke-width="1" transform="rotate(-12, 195, 265)" />
              <rect x="175" y="260" width="70" height="14" rx="4" fill="#362217" stroke="#24160f" stroke-width="1" transform="rotate(10, 210, 267)" />
            </g>
            
            <!-- Ngọn lửa hồng cháy bập bùng (Được tách thành 3 lớp chuyển động) -->
            <g class="oven-fire">
              <!-- Lửa ngoài (Đỏ) -->
              <path class="flame-outer" d="M140 275 C140 215, 175 190, 185 220 C195 180, 225 200, 235 235 C245 210, 260 230, 255 275 Z" fill="#e63921" opacity="0.8" />
              <!-- Lửa giữa (Cam hồng) -->
              <path class="flame-mid" d="M155 275 C155 225, 180 205, 190 232 C200 195, 218 210, 228 245 C233 228, 245 240, 242 275 Z" fill="#f5a623" opacity="0.9" />
              <!-- Lửa nhân trong cùng (Vàng sáng rực) -->
              <path class="flame-inner" d="M175 275 C175 240, 185 225, 195 245 C205 225, 210 235, 215 275 Z" fill="#fff9eb" opacity="0.95" />
              
              <!-- Tàn lửa/Đốm sáng bay lên -->
              <circle class="spark spark-1" cx="190" cy="205" r="3.5" fill="#f5a623" />
              <circle class="spark spark-2" cx="210" cy="185" r="2" fill="#e63921" />
              <circle class="spark spark-3" cx="175" cy="215" r="1.5" fill="#fff" />
              <circle class="spark spark-4" cx="230" cy="200" r="2.5" fill="#f5a623" />
            </g>
            
            <!-- Khói tỏa ra dịu nhẹ ở nóc vòm -->
            <g class="oven-smoke">
              <path class="smoke-puff smoke-puff-1" d="M170 130 C160 110, 180 90, 185 100 C190 80, 210 90, 205 110 Z" fill="rgba(255,255,255,0.06)" />
              <path class="smoke-puff smoke-puff-2" d="M195 110 C185 90, 205 70, 210 80 C215 60, 235 70, 230 90 Z" fill="rgba(255,255,255,0.04)" opacity="0.8" />
            </g>
          </svg>
        </div>
      </div>

    </div>

    <!-- BLOCK 2: GIẢI PHÁP VẬN HÀNH & NGHỆ THUẬT PIZZA TRÔI NỔI -->
    <div class="pp-part-block pp-part-block--solutions">
      
      <!-- Cột trái: Nghệ thuật Pizza tươi phong cách Ý (SVG Art) -->
      <div class="pp-part-pizza-media">
        <div class="pp-pizza-art-container">
          <svg class="pp-pizza-svg" viewBox="0 0 400 350" fill="none" xmlns="http://www.w3.org/2000/svg" aria-label="Hình vẽ bánh pizza tươi Ý và lát pizza bay lơ lửng">
            <title><?php echo esc_html__('Bánh pizza tươi phong cách Ý truyền thống', 'saigonhoreca'); ?></title>
            
            <!-- Đường nét chuyển động đứt đoạn tạo cảm giác bay bổng -->
            <path class="pizza-motion-line" d="M40 260 C 130 190, 240 290, 340 180" stroke="var(--gold)" stroke-width="1.5" stroke-dasharray="6 8" opacity="0.1" />
            
            <!-- CHIẾC PIZZA CHÍNH (Đã cắt một lát) -->
            <g class="pizza-main">
              <!-- Đế viền bánh nướng phồng vàng -->
              <circle cx="160" cy="190" r="95" fill="#cc823b" stroke="#ab6828" stroke-width="4" />
              <!-- Sốt cà chua đặc trưng Ý -->
              <circle cx="160" cy="190" r="82" fill="#b02617" />
              <!-- Lớp phô mai Mozzarella béo ngậy chảy loang lổ -->
              <path d="M160 114 C 202 114, 238 148, 238 190 C 238 232, 202 266, 160 266 C 118 266, 82 232, 82 190 C 82 148, 118 114, 160 114 Z" fill="#f7c55c" />
              <!-- Phô mai chảy màu vàng sữa nhạt -->
              <path class="cheese-melt" d="M125 155 C 135 150, 145 160, 160 148 C 175 135, 190 162, 202 153 C 215 165, 222 180, 218 200 C 212 218, 195 220, 185 232 C 165 245, 142 232, 132 227 C 115 220, 108 202, 112 182 C 117 165, 115 160, 125 155 Z" fill="#fdf0cc" opacity="0.95" />
              
              <!-- Topping: Pepperoni đỏ lát tròn sậm -->
              <g class="topping-pepperonis">
                <circle cx="120" cy="172" r="13" fill="#91180b" />
                <circle cx="122" cy="170" r="11" fill="#bd220f" />
                <circle cx="124" cy="168" r="3.5" fill="#e34834" opacity="0.7" />

                <circle cx="185" cy="148" r="13" fill="#91180b" />
                <circle cx="187" cy="146" r="11" fill="#bd220f" />
                <circle cx="189" cy="144" r="3.5" fill="#e34834" opacity="0.7" />

                <circle cx="195" cy="208" r="13" fill="#91180b" />
                <circle cx="197" cy="206" r="11" fill="#bd220f" />
                <circle cx="199" cy="204" r="3.5" fill="#e34834" opacity="0.7" />

                <circle cx="132" cy="222" r="13" fill="#91180b" />
                <circle cx="134" cy="220" r="11" fill="#bd220f" />
                <circle cx="136" cy="218" r="3.5" fill="#e34834" opacity="0.7" />
              </g>

              <!-- Topping: Lá Basil tươi mát xanh lá -->
              <g class="topping-basils">
                <!-- Lá 1 -->
                <path d="M142 178 C 132 173, 137 162, 147 168 C 157 174, 152 184, 142 178 Z" fill="#3a7021" />
                <path d="M143 179 C 134 175, 139 164, 148 169 C 156 174, 153 182, 143 179 Z" fill="#4d9430" />
                <!-- Lá 2 -->
                <path d="M168 198 C 162 188, 178 182, 178 192 C 178 202, 162 208, 168 198 Z" fill="#3a7021" />
                <path d="M169 199 C 163 189, 179 183, 179 193 C 179 203, 163 209, 169 199 Z" fill="#4d9430" />
              </g>
              
              <!-- Topping: Lát nấm tươi Champignon -->
              <g class="topping-mushrooms">
                <path d="M162 165 C157 160, 152 165, 157 170 C155 173, 160 175, 164 173 Z" fill="#eddccb" stroke="#bd9873" stroke-width="1" />
                <path d="M145 202 C140 197, 135 202, 140 207 C138 210, 143 212, 147 210 Z" fill="#eddccb" stroke="#bd9873" stroke-width="1" />
              </g>
            </g>

            <!-- LÁT PIZZA PHÔ MAI KÉO SỢI ĐANG BAY LƠ LỬNG -->
            <g class="pizza-slice-flying">
              <!-- Sợi xích phô mai kéo căng nghệ thuật kết nối hai bên -->
              <path class="cheese-bridge" d="M228 178 Q250 160 270 148" stroke="#fdf0cc" stroke-width="4.5" stroke-linecap="round" stroke-dasharray="2 3" opacity="0.9" />
              <path class="cheese-bridge-thin" d="M220 192 Q260 178 285 160" stroke="#f7c55c" stroke-width="2" stroke-linecap="round" opacity="0.8" />
              
              <g class="slice-body">
                <!-- Vỏ bánh tam giác -->
                <path d="M275 125 L352 178 L304 228 Z" fill="#cc823b" stroke="#ab6828" stroke-width="3.5" />
                <!-- Cạnh ngoài dày xốp nổi -->
                <path d="M352 178 C346 192, 322 218, 304 228 L294 213 C312 203, 332 182, 337 168 Z" fill="#a86220" />
                
                <!-- Sốt đỏ phủ -->
                <path d="M283 133 L336 170 L300 207 Z" fill="#b02617" />
                <!-- Phô mai phủ -->
                <path d="M287 137 L330 166 L298 200 Z" fill="#fdf0cc" />
                
                <!-- Pepperoni trên lát cắt -->
                <circle cx="312" cy="172" r="9" fill="#bd220f" />
                <circle cx="298" cy="152" r="8" fill="#bd220f" />
                
                <!-- Lá Basil trên lát cắt -->
                <path d="M308 182 C303 177, 308 172, 313 177 Z" fill="#4d9430" />
              </g>
            </g>

            <!-- CÁC HẠT GIA VỊ VÀ LÁ BAY TỰ DO TRONG KHÔNG GIAN -->
            <g class="pizza-toppings-floating">
              <!-- Lá basil bay tự do 1 -->
              <path class="fly-basil-1" d="M60 120 C54 115, 59 105, 68 110 C77 115, 71 125, 60 120 Z" fill="#4d9430" opacity="0.8" />
              <!-- Lá basil bay tự do 2 -->
              <path class="fly-basil-2" d="M260 80 C254 75, 259 65, 268 70 C277 75, 271 85, 260 80 Z" fill="#3a7021" opacity="0.75" />
              <!-- Lát Pepperoni nhỏ trôi nổi -->
              <circle class="fly-pep" cx="310" cy="270" r="11" fill="#91180b" opacity="0.6" />
              <circle class="fly-pep-inner" cx="311" cy="269" r="9" fill="#bd220f" opacity="0.7" />
              <!-- Hạt tiêu xay đen bay rơi rụng -->
              <circle class="fly-pepper-1" cx="95" cy="100" r="3" fill="#2b231d" opacity="0.6" />
              <circle class="fly-pepper-2" cx="215" cy="90" r="2" fill="#2b231d" opacity="0.5" />
              <circle class="fly-pepper-3" cx="145" cy="300" r="3" fill="#2b231d" opacity="0.7" />
              <circle class="fly-pepper-4" cx="330" cy="100" r="2.5" fill="#2b231d" opacity="0.6" />
            </g>
          </svg>
        </div>
      </div>

      <!-- Cột phải: Các giải pháp thiết kế lắp đặt -->
      <div class="pp-part-solutions">
        <h3 class="pp-part-solutions__heading">
          <?php echo esc_html__('Giải pháp từ Saigon Horeca', 'saigonhoreca'); ?>
        </h3>
        
        <div class="pp-solutions-grid">
          
          <!-- Thẻ giải pháp 1: Layout & Thiết bị -->
          <div class="pp-solution-card">
            <div class="pp-solution-card__header">
              <span class="pp-solution-card__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="3" y="3" width="18" height="18" rx="2"/>
                  <path d="M9 3v18M15 3v18M3 9h18M3 15h18"/>
                </svg>
              </span>
              <h4 class="pp-solution-card__title">
                <?php echo esc_html__('Tư vấn Layout & Thiết bị', 'saigonhoreca'); ?>
              </h4>
            </div>
            <div class="pp-solution-card__body">
              <p>
                <?php echo esc_html__('Từ concept đó, Saigon Horeca tiến hành tư vấn bố trí layout bếp và khu bar, dựa trên nhịp vận hành thực tế của nhà hàng. Hệ thống thiết bị bếp công nghiệp trong dự án được lựa chọn và lắp đặt đồng bộ, hướng đến sự ổn định, độ bền và khả năng đáp ứng cường độ vận hành liên tục.', 'saigonhoreca'); ?>
              </p>
            </div>
          </div>

          <!-- Thẻ giải pháp 2: Chụp hút mùi & Khí tươi -->
          <div class="pp-solution-card">
            <div class="pp-solution-card__header">
              <span class="pp-solution-card__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M9.59 4.59A2 2 0 1 1 11 8H2m10.59 11.41A2 2 0 1 0 14 16H2m15.73-8.27A2.5 2.5 0 1 1 19.5 12H2"/>
                </svg>
              </span>
              <h4 class="pp-solution-card__title">
                <?php echo esc_html__('Hệ thống Thông gió Tinh tế', 'saigonhoreca'); ?>
              </h4>
            </div>
            <div class="pp-solution-card__body">
              <p>
                <?php echo esc_html__('Bên cạnh đó, giải pháp chụp hút mùi kết hợp cấp khí tươi tinh tế giúp giữ gìn không gian nhà hàng luôn trong lành, dễ chịu, gần như "vô hình" đối với thực khách nhưng vô cùng quan trọng đối với trải nghiệm ẩm thực đỉnh cao.', 'saigonhoreca'); ?>
              </p>
            </div>
          </div>

        </div>
      </div>

    </div>

    <!-- BLOCK 3: QUOTE TUYÊN NGÔN -->
    <div class="pp-part-block pp-part-block--gallery">
      
      <!-- Quote Tuyên Ngôn: Viền mạ vàng bóng bẩy -->
      <div class="pp-part-callout">
        <span class="pp-part-callout__quote-icon" aria-hidden="true">“</span>
        <p class="pp-part-callout__text">
          <?php echo esc_html__('Với dự án The Cheezy Time, Saigon Horeca không chỉ hoàn thiện một danh mục hạng mục kỹ thuật. Chúng tôi tự hào đồng hành định hình một hệ thống vận hành bền vững, bảo chứng cho sự thăng hoa của trải nghiệm ẩm thực sông nước đương đại.', 'saigonhoreca'); ?>
        </p>
      </div>

    </div>

  </div>
</section>
