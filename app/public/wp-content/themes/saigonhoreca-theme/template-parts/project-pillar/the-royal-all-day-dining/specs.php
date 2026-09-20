<?php
/**
 * Project Pillar — the-royal-all-day-dining
 * Section #5: SPECS — Blueprint Technical Drawing Layout
 * Grid 3 cột bất đối xứng + FIG labels + interactive carousel + technical text panel
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();

// Danh sách ảnh specs
$specs_images = [
  [
    'key'  => 'main',
    'fig'  => 'FIG.01',
    'src'  => sgh_img('the-royal-all-day-dining/the-royal-all-day-dining-khu-bep-nong-fujimak.jpg'),
    'alt'  => esc_attr__('Hệ thống thiết bị bếp nóng Fujimak bằng inox cao cấp tại The Royal', 'saigonhoreca'),
    'cap'  => esc_html__('Lò hấp nướng đa năng Fujimak Combi Pro hiện đại phục vụ khu bếp nóng.', 'saigonhoreca'),
  ],
  [
    'key'  => 'side-top',
    'fig'  => 'FIG.02',
    'src'  => sgh_img('the-royal-all-day-dining/the-royal-all-day-dining-khu-so-che-chau-rua.jpg'),
    'alt'  => esc_attr__('Gian bếp sơ chế và chậu rửa inox 304 tại The Royal', 'saigonhoreca'),
    'cap'  => esc_html__('Khu vực sơ chế tích hợp chậu rửa inox 304 sạch sẽ sáng bóng.', 'saigonhoreca'),
  ],
  [
    'key'  => 'side-bot',
    'fig'  => 'FIG.03',
    'src'  => sgh_img('the-royal-all-day-dining/the-royal-all-day-dining-ban-mat-ngan-keo-undercounter.webp'),
    'alt'  => esc_attr__('Bàn mát ngăn kéo undercounter inox tại khu bếp The Royal', 'saigonhoreca'),
    'cap'  => esc_html__('Bàn mát ngăn kéo undercounter inox công nghiệp phục vụ khu bếp nóng.', 'saigonhoreca'),
  ],
];
?>
<section class="pp__section pp-specs-blueprint-trd" id="specs-trd">
  <div class="pp__container pp-container-shared">

    <!-- ==========================================
         Carousel wrapper: ảnh chính + 2 thumbnail
         ========================================== -->
    <div
      class="pp-specs-blueprint-trd__grid js-specs-carousel"
      data-auto-interval="5000"
      data-current="0"
    >
      <!-- ── Ảnh CHÍNH (spotlight) ── -->
      <figure
        class="pp-image-container-shared pp-specs-blueprint-trd__fig pp-specs-blueprint-trd__fig--main"
        id="specs-main-figure"
        aria-label="<?php esc_attr_e('Ảnh thiết bị đang được xem', 'saigonhoreca'); ?>"
      >
        <span class="pp-specs-blueprint-trd__fig-label js-specs-main-label" aria-hidden="true">FIG.01</span>

        <!-- Overlay fade layer giữa các lần chuyển -->
        <span class="pp-specs-blueprint-trd__fig-overlay" aria-hidden="true"></span>

        <img
          id="specs-main-img"
          src="<?php echo $specs_images[0]['src']; ?>"
          alt="<?php echo $specs_images[0]['alt']; ?>"
          loading="lazy"
          decoding="async"
          width="1366"
          height="768"
        >
        <figcaption
          class="pp-image-caption-shared js-specs-main-cap"
          id="specs-main-cap"
        ><?php echo $specs_images[0]['cap']; ?></figcaption>

        <!-- Nút điều hướng trái / phải luôn luôn hiện rõ -->
        <button
          type="button"
          class="pp-specs-blueprint-trd__nav pp-specs-blueprint-trd__nav--prev"
          aria-label="<?php esc_attr_e('Xem ảnh trước', 'saigonhoreca'); ?>"
          data-dir="-1"
        >&#8592;</button>
        <button
          type="button"
          class="pp-specs-blueprint-trd__nav pp-specs-blueprint-trd__nav--next"
          aria-label="<?php esc_attr_e('Xem ảnh tiếp theo', 'saigonhoreca'); ?>"
          data-dir="1"
        >&#8594;</button>

        <!-- Dots indicator -->
        <div class="pp-specs-blueprint-trd__dots" aria-label="<?php esc_attr_e('Chỉ số vị trí ảnh', 'saigonhoreca'); ?>">
          <?php foreach ($specs_images as $i => $img): ?>
          <button
            type="button"
            class="pp-specs-blueprint-trd__dot<?php echo $i === 0 ? ' is-active' : ''; ?>"
            data-index="<?php echo $i; ?>"
            aria-label="<?php printf(esc_attr__('Chuyển sang ảnh %s', 'saigonhoreca'), $img['fig']); ?>"
          ></button>
          <?php endforeach; ?>
        </div>
      </figure>

      <!-- ── Ảnh FIG.02 (thumbnail trái) ── -->
      <figure
        class="pp-image-container-shared pp-specs-blueprint-trd__fig pp-specs-blueprint-trd__fig--side-top js-specs-thumb"
        data-index="1"
        role="button"
        tabindex="0"
        aria-label="<?php esc_attr_e('Xem FIG.02 — Khu sơ chế', 'saigonhoreca'); ?>"
      >
        <span class="pp-specs-blueprint-trd__fig-label js-specs-thumb-label-1" aria-hidden="true">FIG.02</span>
        <img
          id="specs-thumb-img-1"
          src="<?php echo $specs_images[1]['src']; ?>"
          alt="<?php echo $specs_images[1]['alt']; ?>"
          loading="lazy"
          decoding="async"
          width="800"
          height="600"
        >
        <figcaption class="pp-image-caption-shared js-specs-thumb-cap-1"><?php echo $specs_images[1]['cap']; ?></figcaption>
        <span class="pp-specs-blueprint-trd__thumb-cta" aria-hidden="true">Click để phóng to</span>
      </figure>

      <!-- ── Ảnh FIG.03 (thumbnail phải-dưới) ── -->
      <figure
        class="pp-image-container-shared pp-image-container-rf pp-specs-blueprint-trd__fig pp-specs-blueprint-trd__fig--side-bot js-specs-thumb"
        data-index="2"
        role="button"
        tabindex="0"
        aria-label="<?php esc_attr_e('Xem FIG.03 — Chi tiết thiết bị', 'saigonhoreca'); ?>"
      >
        <span class="pp-specs-blueprint-trd__fig-label js-specs-thumb-label-2" aria-hidden="true">FIG.03</span>
        <img
          id="specs-thumb-img-2"
          src="<?php echo $specs_images[2]['src']; ?>"
          alt="<?php echo $specs_images[2]['alt']; ?>"
          loading="lazy"
          decoding="async"
          width="800"
          height="600"
        >
        <figcaption class="pp-image-caption-shared js-specs-thumb-cap-2"><?php echo $specs_images[2]['cap']; ?></figcaption>
        <span class="pp-specs-blueprint-trd__thumb-cta" aria-hidden="true">Click để phóng to</span>
      </figure>
    </div><!-- /.js-specs-carousel -->

    <!-- Dimension line connector -->
    <div class="pp-specs-blueprint-trd__dimension" aria-hidden="true">
      <span class="pp-specs-blueprint-trd__dimension-dot"></span>
      <span class="pp-specs-blueprint-trd__dimension-line"></span>
      <span class="pp-specs-blueprint-trd__dimension-dot"></span>
    </div>

    <!-- Technical text panel -->
    <div class="pp-specs-blueprint-trd__panel">
      <div class="pp-specs-blueprint-trd__panel-header">
        <span class="pp-specs-blueprint-trd__panel-icon" aria-hidden="true">&#9670;</span>
        <h2 class="pp-text-trd__title"><?php echo esc_html__('Giải pháp của Saigon Horeca:', 'saigonhoreca'); ?></h2>
      </div>
      <span class="pp-text-trd__divider" aria-hidden="true"></span>
      <div class="pp-text-trd__body pp-specs-blueprint-trd__body-text">
        <p><?php echo wp_kses_post(sprintf(esc_html__('%1$s ra đời như một tuyên ngôn về sự hoàn mỹ trong từng chi tiết. Và Saigon Horeca, với vai trò thiết kế và thi công hệ thống bếp công nghiệp, đã thể hiện xuất sắc sứ mệnh đó: %2$s', 'saigonhoreca'), '<strong>The Royal</strong>', '<strong>' . esc_html__('biến mỗi mét vuông nhà bếp thành một nền tảng cho sự bứt phá trong nghệ thuật ẩm thực.', 'saigonhoreca') . '</strong>')); ?></p>
        <p><?php echo wp_kses_post(sprintf(esc_html__('Bếp công nghiệp The Royal được xây dựng theo triết lý %1$s', 'saigonhoreca'), '<strong>' . esc_html__('"Bếp là trái tim của nhà hàng – nơi khởi nguồn cho mọi cảm xúc thực"', 'saigonhoreca') . '</strong>')); ?></p>
        <p><?php echo wp_kses_post(sprintf(esc_html__('Mọi khu vực trong bếp – từ sơ chế, chế biến, nấu nướng đến hoàn thiện món – đều được %1$s, tối ưu theo dòng chảy một chiều, giúp tiết kiệm thời gian, giảm thiểu sai sót và tạo nên nhịp làm việc nhịp nhàng, chính xác đến từng thao tác.', 'saigonhoreca'), '<strong>' . esc_html__('vận hành cực kỳ khoa học', 'saigonhoreca') . '</strong>')); ?></p>
        <p><?php echo esc_html__('Tại The Royal, bếp không chỉ là nơi tạo ra món ăn, mà là "ngôi nhà" của các chef thỏa sức sáng tạo và thổi hồn vào từng món ăn – khởi nguồn cho trải nghiệm đẳng cấp đến với thực khách.', 'saigonhoreca'); ?></p>
      </div>
    </div>
  </div><!-- /.pp-container-shared -->
</section>

<script nonce="<?php echo esc_attr($GLOBALS['sgh_csp_nonce'] ?? ''); ?>">
(function () {
  'use strict';

  var specs = [
<?php foreach ($specs_images as $i => $img) : ?>
    {
      fig : '<?php echo esc_js($img['fig']); ?>',
      src : '<?php echo esc_js($img['src']); ?>',
      alt : '<?php echo esc_js($img['alt']); ?>',
      cap : '<?php echo esc_js($img['cap']); ?>'
    }<?php echo $i < count($specs_images) - 1 ? ',' : ''; ?>
<?php endforeach; ?>
  ];

  function initSpecsCarousel () {
    var carousel = document.querySelector('.js-specs-carousel');
    if (!carousel) return;

    var mainImg   = document.getElementById('specs-main-img');
    var mainLabel = carousel.querySelector('.js-specs-main-label');
    var mainCap   = carousel.querySelector('.js-specs-main-cap');
    var overlay   = carousel.querySelector('.pp-specs-blueprint-trd__fig-overlay');
    var dots      = carousel.querySelectorAll('.pp-specs-blueprint-trd__dot');
    var thumbs    = carousel.querySelectorAll('.js-specs-thumb');
    var navBtns   = carousel.querySelectorAll('.pp-specs-blueprint-trd__nav');

    var current   = 0;
    var interval  = 5000;
    var timer     = null;

    // Preload tất cả ảnh
    specs.forEach(function (s) { var im = new Image(); im.src = s.src; });

    function render(index) {
      current = index;
      var item = specs[current];

      if (overlay) {
        overlay.classList.add('is-fading');
        setTimeout(function () {
          overlay.classList.remove('is-fading');
        }, 250);
      }

      if (mainImg) {
        mainImg.src = item.src;
        mainImg.alt = item.alt;
      }
      if (mainLabel) mainLabel.textContent = item.fig;
      if (mainCap) mainCap.textContent = item.cap;

      dots.forEach(function (d, idx) {
        d.classList.toggle('is-active', idx === current);
      });
    }

    function nextSlide() {
      var next = (current + 1) % specs.length;
      render(next);
    }

    function prevSlide() {
      var prev = (current - 1 + specs.length) % specs.length;
      render(prev);
    }

    function startAuto() {
      stopAuto();
      timer = setInterval(nextSlide, interval);
    }

    function stopAuto() {
      if (timer) clearInterval(timer);
    }

    carousel.addEventListener('mouseenter', stopAuto);
    carousel.addEventListener('mouseleave', startAuto);

    navBtns.forEach(function (btn) {
      btn.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        stopAuto();
        var dir = parseInt(this.dataset.dir || '1', 10);
        if (dir === -1) prevSlide(); else nextSlide();
        startAuto();
      });
    });

    dots.forEach(function (dot) {
      dot.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        stopAuto();
        var idx = parseInt(this.dataset.index || '0', 10);
        render(idx);
        startAuto();
      });
    });

    thumbs.forEach(function (thumb) {
      thumb.addEventListener('click', function (e) {
        e.preventDefault();
        stopAuto();
        var idx = parseInt(this.dataset.index || '0', 10);
        render(idx);
        startAuto();
      });
    });

    startAuto();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSpecsCarousel);
  } else {
    initSpecsCarousel();
  }
}());
</script>
