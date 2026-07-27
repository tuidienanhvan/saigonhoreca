<?php
/**
 * Project Pillar — bep-canteen-nha-may-sheh-fung
 * Section #6: gallery — editorial cold-chain grid + corporate call-to-action
 * Image-gallery block built as a GOLD uniform editorial grid
 * (equal 4/3 tiles · clean tiling · shared frame + caption).
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();
?>
<section class="pp-section-shf pp-section-shf--alt pp-gallery2-shf">
  <div class="pp-ambient-glow-shf pp-ambient-glow-shf--left" aria-hidden="true"></div>
  <div class="pp-section-num-shf" aria-hidden="true">03</div>

  <div class="pp-container-shared">

    <!-- Editorial header -->
    <header class="pp-mosaic-shf__header">
      <span class="pp-badge-shf">
        <span class="pp-badge-shf__dot" aria-hidden="true"></span>
        <?php echo esc_html__('Bảo quản & Vận hành', 'saigonhoreca'); ?>
      </span>
      <h2 class="pp-text-shf__title"><?php echo esc_html__('Một không gian bếp tối ưu cho cả nhà máy', 'saigonhoreca'); ?></h2>
      <span class="pp-text-shf__divider" aria-hidden="true"></span>
      <p class="pp-mosaic-shf__lead"><?php echo esc_html__('Hệ thống bếp canteen được trang bị thiết bị tối tân và bố trí khoa học, đảm bảo sự linh hoạt trong vận hành và phục vụ được số lượng lớn nhân viên cùng lúc. Các vật liệu dễ vệ sinh như thép không gỉ và bề mặt chống bám bẩn giữ cho căn bếp luôn sạch sẽ, thoáng đãng và an toàn vệ sinh thực phẩm.', 'saigonhoreca'); ?></p>
    </header>

    <!-- Single feature image + editorial highlights card (cân bằng layout sau dedup) -->
    <div class="pp-mosaic-shf" role="list" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:clamp(1rem,2.5vw,1.75rem);align-items:stretch;">
      <div class="pp-mosaic-shf__watermark" aria-hidden="true">06</div>

      <!-- cold-chain hero -->
      <div class="pp-image-container-shared pp-mosaic-shf__item" role="listitem">
        <img src="<?php echo sgh_img('bep-canteen-nha-may-sheh-fung/bep-canteen-nha-may-sheh-fung-tu-lanh-cong-nghiep-va-chau-rua-hanh-lang.webp'); ?>" alt="<?php echo esc_attr__('Tủ lạnh công nghiệp bảo quản nguyên liệu tươi sống tại Sheh Fung', 'saigonhoreca'); ?>" loading="lazy" decoding="async" width="1366" height="768">
        <div class="pp-image-caption-shared"><?php echo esc_html__('Tủ lạnh công nghiệp bảo quản nguyên liệu tươi sống', 'saigonhoreca'); ?></div>
      </div>

      <!-- Editorial highlights (component chữ trang trí, reuse token gold) -->
      <div class="pp-shf-highlights" role="listitem" style="position:relative;display:flex;flex-direction:column;justify-content:center;gap:1.1rem;padding:clamp(1.5rem,3vw,2.5rem);border:1px solid color-mix(in srgb, var(--gold, #C8A24A) 28%, transparent);border-radius:18px;background:linear-gradient(155deg, color-mix(in srgb, var(--gold,#C8A24A) 7%, transparent), transparent 60%);overflow:hidden;">
        <span class="pp-badge-shf" style="align-self:flex-start;"><span class="pp-badge-shf__dot" aria-hidden="true"></span><?php echo esc_html__('Tiêu chí vận hành', 'saigonhoreca'); ?></span>
        <h3 class="pp-text-shf__title" style="font-size:clamp(1.25rem,2.4vw,1.7rem);margin:0;"><?php echo esc_html__('Bếp canteen chuẩn nhà máy', 'saigonhoreca'); ?></h3>
        <span class="pp-text-shf__divider" aria-hidden="true"></span>
        <ul style="list-style:none;margin:0;padding:0;display:grid;gap:0.85rem;">
          <?php
          $shf_points = [
            ['k' => __('Số lượng lớn', 'saigonhoreca'),     'v' => __('Bố trí một chiều, phục vụ hàng trăm suất ăn cùng lúc cho cả nhà máy.', 'saigonhoreca')],
            ['k' => __('Inox SUS 304', 'saigonhoreca'),      'v' => __('Bề mặt chống bám bẩn, dễ vệ sinh, bền bỉ trước cường độ vận hành liên tục.', 'saigonhoreca')],
            ['k' => __('Cold-chain', 'saigonhoreca'),        'v' => __('Hệ tủ lạnh – tủ đông công nghiệp giữ nguyên liệu tươi sạch, an toàn.', 'saigonhoreca')],
            ['k' => __('Chuẩn ATTP', 'saigonhoreca'),        'v' => __('Phân khu rạch ròi, thoát khí – nước – rác hợp lý, sạch sẽ và thoáng đãng.', 'saigonhoreca')],
          ];
          foreach ($shf_points as $pt) : ?>
            <li style="display:flex;gap:0.75rem;align-items:flex-start;">
              <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="var(--gold,#C8A24A)" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="flex:0 0 auto;margin-top:2px;"><path d="M20 6 9 17l-5-5"/></svg>
              <span><strong style="color:var(--gold,#C8A24A);"><?php echo esc_html($pt['k']); ?></strong> — <?php echo esc_html($pt['v']); ?></span>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>

    </div>

    <div class="pp-cta-shf">
      <div class="pp-cta-shf__grid-pattern" aria-hidden="true"></div>

      <div class="pp-cta-shf__inner">
        <span class="pp-badge-shf pp-cta-shf__badge">
          <span class="pp-badge-shf__dot" aria-hidden="true"></span>
          <?php echo esc_html__('Đồng hành thiết kế & thi công', 'saigonhoreca'); ?>
        </span>
        <h2 class="pp-cta-shf__title"><?php echo esc_html__('Kiến tạo hệ thống bếp công nghiệp đẳng cấp cùng Saigon Horeca', 'saigonhoreca'); ?></h2>
        <p class="pp-cta-shf__desc"><?php echo esc_html__('Từ bếp nhà hàng, canteen cho đến quầy bar và coffee shop, Saigon Horeca luôn là sự lựa chọn hàng đầu của các chủ đầu tư, nhờ vào sự uy tín, chuyên nghiệp và tận tâm trong từng dự án.', 'saigonhoreca'); ?></p>
        <a class="pp-cta-shf__btn" href="<?php echo esc_url(home_url('/lien-he/')); ?>">
          <span><?php echo esc_html__('Liên hệ tư vấn ngay', 'saigonhoreca'); ?></span>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <line x1="5" y1="12" x2="19" y2="12"></line>
            <polyline points="12 5 19 12 12 19"></polyline>
          </svg>
        </a>
      </div>
    </div>

  </div>
</section>
