<?php
/**
 * /du-an/ — Hero featured projects carousel.
 *
 * Match production saigonhoreca.vn:
 *   - 6 slides rotating qua các dự án nổi bật
 *   - Mỗi slide: BG image fade + LEFT text col (title, desc, CTA) +
 *     RIGHT thumbnails 2-3 dự án kế tiếp (peek into next slides)
 *   - Auto-advance 6s, fade transition, dots navigation
 *   - Inline ~40 lines JS để standalone (không cần Swiper lib)
 *
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();

$slides = [
    [
        'title' => 'Sol Kitchen & Bar D7',
        'desc'  => 'Tiếp nối sự thành công của nhà hàng Sol quận 1 khi lọt vào Michelin Guide 2023, Saigon Horeca vinh hạnh được chủ đầu tư Sol Kitchen & Bar tin tưởng lựa chọn là đơn vị uy tín triển khai hệ thống bếp cao cấp cho nhà hàng tại quận 7, HCMC.',
        'img'   => '2024/06/Sol0D7-05.webp',
        'slug'  => 'sol-kitchen-bar-d7',
    ],
    [
        'title' => 'Little Bear',
        'desc'  => 'Little Bear Thảo Điền — không gian ẩm thực ấm cúng đậm chất Âu, được Saigon Horeca thi công trọn gói hệ thống bếp công nghiệp và quầy bar chuyên nghiệp.',
        'img'   => '2024/01/Little-bear-00.webp',
        'slug'  => 'little-bear',
    ],
    [
        'title' => 'Bambino',
        'desc'  => 'Bambino — nhà hàng Ý đạt giải B.A.R Award 2023, được Saigon Horeca tin tưởng triển khai hệ thống bếp cao cấp và quầy bar đẳng cấp Michelin.',
        'img'   => '2023/12/bambino-saigon-horeca-2-1.webp',
        'slug'  => 'bambino-saigonhoreca',
    ],
    [
        'title' => 'Yuzu Omakase',
        'desc'  => 'Yuzu Omakase — trải nghiệm fine-dining Nhật Bản tinh tế, với bếp Teppanyaki và sushi bar được Saigon Horeca thiết kế trọn gói theo tiêu chuẩn quốc tế.',
        'img'   => '2024/01/Yuzu-Omakase-1.webp',
        'slug'  => 'yuzu-omakase',
    ],
    [
        'title' => 'Godmother Friendship',
        'desc'  => 'Godmother Friendship — không gian Á-Âu đương đại, hệ thống bếp công nghiệp đầy đủ tiện nghi được Saigon Horeca thi công đúng tiến độ và chuẩn vệ sinh ATTP.',
        'img'   => '2023/12/Godmother-friendship-1.webp',
        'slug'  => 'godmother-friendship',
    ],
    [
        'title' => 'Sol Kitchen & Bar Q1',
        'desc'  => 'Nhà hàng Sol Kitchen & Bar quận 1 — vinh dự được lọt Michelin Guide 2023. Saigon Horeca tự hào là đơn vị thiết kế và thi công hệ thống bếp công nghiệp cao cấp.',
        'img'   => '2023/12/Sol-kitchen-bar-2-1.webp',
        'slug'  => 'sol-kitchen-bar-q1',
    ],
];

$detail_label = 'Chi Tiết Dự Án';
?>
<section class="sh-archive-hero" aria-label="Dự án nổi bật Saigon Horeca">

    <!-- Background slideshow layer (fade qua các ảnh) -->
    <div class="sh-archive-hero__bg-stack" aria-hidden="true">
        <?php foreach ($slides as $i => $s) : ?>
            <div class="sh-archive-hero__bg <?php echo $i === 0 ? 'is-active' : ''; ?>"
                 data-index="<?php echo (int) $i; ?>"
                 style="background-image: url('<?php echo esc_url(sgh_img("{$s['img']}")); ?>');"></div>
        <?php endforeach; ?>
    </div>

    <!-- Dark overlay gradient -->
    <div class="sh-archive-hero__overlay" aria-hidden="true"></div>

    <!-- Content (text left + thumbnails right) -->
    <div class="sh-archive-hero__inner">

        <!-- LEFT: Text + CTA (rotating sync với slide hiện tại) -->
        <div class="sh-archive-hero__text-stack">
            <?php foreach ($slides as $i => $s) : ?>
                <div class="sh-archive-hero__text <?php echo $i === 0 ? 'is-active' : ''; ?>"
                     data-index="<?php echo (int) $i; ?>">
                    <h1 class="sh-archive-hero__title"><?php echo esc_html($s['title']); ?></h1>
                    <p class="sh-archive-hero__desc"><?php echo esc_html($s['desc']); ?></p>
                    <a href="<?php echo esc_url(home_url('/du-an/' . $s['slug'] . '/')); ?>" class="sh-archive-hero__cta">
                        <span><?php echo esc_html($detail_label); ?></span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                            <path d="M5 12h14M13 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- RIGHT: Project preview cards (clickable navigation) -->
        <div class="sh-archive-hero__cards">
            <?php foreach ($slides as $i => $s) : ?>
                <button class="sh-archive-hero__card <?php echo $i === 0 ? 'is-active' : ''; ?>"
                        data-index="<?php echo (int) $i; ?>"
                        type="button"
                        aria-label="<?php echo esc_attr($s['title']); ?>">
                    <img src="<?php echo esc_url(sgh_img("{$s['img']}")); ?>"
                         alt=""
                         loading="lazy" decoding="async">
                    <span class="sh-archive-hero__card-name"><?php echo esc_html($s['title']); ?></span>
                </button>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Dots — group role since each child is a navigation button (not a tab panel). -->
    <div class="sh-archive-hero__dots" role="group" aria-label="Carousel navigation">
        <?php foreach ($slides as $i => $s) : ?>
            <button class="sh-archive-hero__dot <?php echo $i === 0 ? 'is-active' : ''; ?>"
                    data-index="<?php echo (int) $i; ?>"
                    type="button"
                    aria-label="Slide <?php echo $i + 1; ?>"></button>
        <?php endforeach; ?>
    </div>
</section>

<script>
/* Auto-rotate slider /du-an/ hero — pure JS, no deps. */
(function () {
    var sec = document.querySelector('.sh-archive-hero');
    if (!sec) return;
    var bgs   = sec.querySelectorAll('.sh-archive-hero__bg');
    var texts = sec.querySelectorAll('.sh-archive-hero__text');
    var cards = sec.querySelectorAll('.sh-archive-hero__card');
    var dots  = sec.querySelectorAll('.sh-archive-hero__dot');
    var total = bgs.length;
    var current = 0;
    var timer;

    function goTo(i) {
        i = ((i % total) + total) % total;
        [bgs, texts, cards, dots].forEach(function (group) {
            group.forEach(function (el, idx) {
                el.classList.toggle('is-active', idx === i);
            });
        });
        // Scroll active card vào view BÊN TRONG cards container (horizontal),
        // KHÔNG dùng scrollIntoView vì sẽ scroll cả page vertical.
        var cardsWrap = sec.querySelector('.sh-archive-hero__cards');
        if (cardsWrap && cards[i]) {
            var wrapRect = cardsWrap.getBoundingClientRect();
            var cardRect = cards[i].getBoundingClientRect();
            var delta = cardRect.left - wrapRect.left + cardsWrap.scrollLeft;
            // Center the card inside the wrap horizontally.
            var center = delta - (cardsWrap.clientWidth - cards[i].clientWidth) / 2;
            cardsWrap.scrollTo({ left: Math.max(0, center), behavior: 'smooth' });
        }
        current = i;
    }
    function next() { goTo(current + 1); }
    function start() { timer = setInterval(next, 6000); }
    function stop()  { clearInterval(timer); }

    [cards, dots].forEach(function (group) {
        group.forEach(function (el, idx) {
            el.addEventListener('click', function () {
                goTo(idx);
                stop(); start(); // reset auto-rotate
            });
        });
    });

    sec.addEventListener('mouseenter', stop);
    sec.addEventListener('mouseleave', start);
    start();
})();
</script>
