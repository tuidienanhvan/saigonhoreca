<?php
/**
 * Project Pillar — sol-kitchen-bar-saigon-horeca
 * Section #6: gallery — metro image grid (space & experience) + closing remarks
 * split block, numbered watermark accent.
 * Copy: hacienda colour & Latin American inspiration; closing collaboration note.
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
?>
<section class="pp-section-solshr pp-gallery-solshr">
  <div class="pp-solshr-ambient pp-solshr-ambient--tr" aria-hidden="true"></div>
  <div class="pp-gallery-solshr__watermark" aria-hidden="true">04</div>

  <!-- Vầng sáng radial gold ấm áp khuếch tán nền khảm (ambient glow) -->
  <div class="pp-gallery-solshr__glow" aria-hidden="true"></div>

  <!-- Vân chìm SVG mặt trời hacienda nghệ thuật -->
  <svg class="pp-gallery-solshr__sun" viewBox="0 0 100 100" fill="none" aria-hidden="true">
  <circle cx="50" cy="50" r="22" stroke="currentColor" stroke-width="1.2"/>
  <g stroke="currentColor" stroke-width="1.2" stroke-linecap="round">
  <path d="M50 6 V20"/><path d="M50 80 V94"/><path d="M6 50 H20"/><path d="M80 50 H94"/>
  <path d="M19 19 L29 29"/><path d="M71 71 L81 81"/><path d="M81 19 L71 29"/><path d="M29 71 L19 81"/>
  </g>
  </svg>

  <div class="pp-container-shared">
  <div class="pp-gallery-solshr__head">
  <span class="pp-text-solshr__eyebrow"><?php echo esc_html__('Không gian & Trải nghiệm', 'saigonhoreca'); ?></span>
  <span class="pp-text-solshr__divider pp-text-solshr__divider--center" aria-hidden="true"></span>
  <h2 class="pp-text-solshr__title"><?php echo esc_html__('Thư viện SOL Kitchen & Bar', 'saigonhoreca'); ?></h2>
  <div class="pp-text-solshr__body pp-text-solshr--center">
  <p><?php echo esc_html__('Một không gian hacienda ngập tràn màu sắc và cảm hứng Mỹ Latin, nơi mỗi góc nhìn đều kể một câu chuyện ẩm thực riêng.', 'saigonhoreca'); ?></p>
  </div>
  </div>

  <!-- LƯỚI ĐỀU (Uniform grid) — 1 → 2 → 3 cột, mọi tile cùng tỉ lệ 4/3, xếp khít không khoảng trống. -->
  <div class="pp-gallery-solshr__mosaic">
  <figure class="pp-gallery-solshr__item">
  <div class="pp-image-container-shared pp-gallery-solshr__frame">
  <img src="<?php echo esc_url(sgh_img('sol-kitchen-bar-saigon-horeca/sol-kitchen-bar-dia-hai-san-hau-tuoi-phong-cach-latin.webp')); ?>" alt="<?php echo esc_attr__('Nhân viên SOL bưng đĩa hải sản hàu tươi trên vỏ với hành tím, rau thơm và sốt salsa, kèm chanh và sốt cay, phong cách Mỹ Latin', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
  <div class="pp-image-caption-shared"><?php echo esc_html__('Nhân viên SOL bưng mâm hàu tươi trên đá lạnh, điểm hành tím, rau thơm và sốt cay — tinh thần hải sản tươi sống đậm chất Mỹ Latin trong từng phần phục vụ.', 'saigonhoreca'); ?></div>
  </div>
  </figure>
  <figure class="pp-gallery-solshr__item">
  <div class="pp-image-container-shared pp-gallery-solshr__frame">
  <img src="<?php echo esc_url(sgh_img('sol-kitchen-bar-saigon-horeca/sol-kitchen-bar-mon-thit-bo-nuong-com-do-sot-chimichurri.webp')); ?>" alt="<?php echo esc_attr__('Món thịt bò nướng thái lát trên cơm đỏ kiểu Latin, ăn kèm ớt nướng, hành tây và sốt chimichurri tại Sol Kitchen & Bar', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
  <div class="pp-image-caption-shared"><?php echo esc_html__('Thịt bò nướng thái lát chín tái trên nền cơm đỏ Latin, kèm ớt và hành tây nướng cùng chén chimichurri xanh — món chủ lực thể hiện bí quyết chế biến thịt đỏ của SOL.', 'saigonhoreca'); ?></div>
  </div>
  </figure>
  <figure class="pp-gallery-solshr__item">
  <div class="pp-image-container-shared pp-gallery-solshr__frame">
  <img src="<?php echo esc_url(sgh_img('sol-kitchen-bar-saigon-horeca/sol-kitchen-bar-thit-nuong-sot-kem-bap.webp')); ?>" alt="<?php echo esc_attr__('Món thịt nướng thái lát ăn kèm sốt kem bắp và bắp nướng, rưới sốt nâu, bày trong tô gốm tại Sol Kitchen & Bar', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
  <div class="pp-image-caption-shared"><?php echo esc_html__('Thịt nướng thái lát mềm mọng trên nền sốt kem bắp vàng óng, rưới sốt nâu đậm và điểm rau thơm — sự hòa quyện béo ngậy, ngọt thơm rất Mỹ Latin trong tô gốm thủ công.', 'saigonhoreca'); ?></div>
  </div>
  </figure>
  </div>
  </div>
</section>

<section class="pp-section-solshr pp-section-solshr--alt pp-outro-solshr">
  <div class="pp-solshr-ambient pp-solshr-ambient--bl" aria-hidden="true"></div>

  <div class="pp-container-shared">
  <div class="pp-outro-solshr__layout">
  <div class="pp-outro-solshr__media">
  <div class="pp-image-container-shared pp-frame-solshr">
  <img src="<?php echo esc_url(sgh_img('sol-kitchen-bar-saigon-horeca/sol-kitchen-bar-suon-heo-nuong-bbq-sot-couscous.webp')); ?>" alt="<?php echo esc_attr__('Đĩa sườn heo nướng BBQ sốt nâu đỏ ăn kèm couscous trộn rau củ tại Sol Kitchen & Bar', 'saigonhoreca'); ?>" loading="lazy" decoding="async">
  <div class="pp-image-caption-shared"><?php echo esc_html__('Trải nghiệm ẩm thực Latin American trọn vẹn trong không gian được Saigon Horeca chăm chút.', 'saigonhoreca'); ?></div>
  </div>
  </div>
  <div class="pp-outro-solshr__body pp-story-card-solshr">
  <div class="pp-solshr-ornament pp-solshr-ornament--right" aria-hidden="true"></div>
  <span class="pp-text-solshr__divider" aria-hidden="true"></span>
  <h2 class="pp-text-solshr__title"><?php echo esc_html__('Môi trường làm việc đáng tin cậy', 'saigonhoreca'); ?></h2>
  <div class="pp-text-solshr__body">
  <p><?php echo esc_html__('Sự hợp tác chặt chẽ và chuyên nghiệp giữa SOL Kitchen & Bar và Saigon Horeca đã tạo ra một môi trường làm việc hiệu quả và đáng tin cậy cho việc chuẩn bị và phục vụ các món ăn tinh tế đến khách hàng.', 'saigonhoreca'); ?></p>
  <p class="pp-outro-solshr__sign"><?php echo esc_html__('Saigon Horeca | Kitchen Equipment and Bar Solutions.', 'saigonhoreca'); ?></p>
  </div>
  </div>
  </div>
  </div>
</section>
