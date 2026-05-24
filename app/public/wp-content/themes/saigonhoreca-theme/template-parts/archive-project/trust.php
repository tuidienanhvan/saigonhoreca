<?php
/**
 * /du-an/ — Uy Tín và Chất Lượng (Đối Tác carousel).
 *
 * Match production saigonhoreca.vn:
 *   - WHITE background (break dark luxe ở đây để logo brand hiện rõ)
 *   - Eyebrow vàng "Uy Tín và Chất Lượng"
 *   - Title đen "Đối Tác của Sài Gòn Horeca"
 *   - Carousel 5-up logos scroll-snap với dots navigation
 *   - Auto-advance 4s
 *
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;
$uri = get_template_directory_uri();

// 12 partners production (theo data /du-an/ scrape).
$partners = [
    ['name' => 'Fujimak',      'file' => 'SGH-Fujimak_logo.jpeg'],
    ['name' => 'Sammic',       'file' => 'SGH-sammic.jpeg'],
    ['name' => 'Giorik',       'file' => 'SGH-Giorik.jpeg'],
    ['name' => 'Hoshizaki',    'file' => 'SGH-hoshizaki.png'],
    ['name' => 'Berjaya',      'file' => 'logo-Berjaya-min.png'],
    ['name' => 'Hobart',       'file' => 'SGH-Hobart.jpg'],
    ['name' => 'Rational',     'file' => 'SGH-rational.png'],
    ['name' => 'Mahlkonig',    'file' => 'SGH-mhalkonik.jpg'],
    ['name' => 'Anfim',        'file' => 'SGH-Anfim.jpg'],
    ['name' => 'Fiorenzato',   'file' => 'SGH-fiorengato.webp'],
    ['name' => 'Winterhalter', 'file' => 'SGH-Winterhalter.png'],
    ['name' => 'BFC',          'file' => 'SGH-BFC-machine-coffee.jpeg'],
];
// Match production: 1 dot per logo, advance 1-at-a-time (infinite loop).
$total_dots = count($partners);
?>
<section class="sh-archive-trust" aria-label="Đối tác Saigon Horeca">
    <!-- Decorative SVG corner ornaments (top-left, top-right) -->
    <svg class="sh-archive-trust__ornament sh-archive-trust__ornament--tl" viewBox="0 0 120 120" fill="none" aria-hidden="true">
        <circle cx="20" cy="20" r="3" fill="currentColor" opacity=".55"/>
        <circle cx="40" cy="20" r="2" fill="currentColor" opacity=".35"/>
        <circle cx="20" cy="40" r="2" fill="currentColor" opacity=".35"/>
        <path d="M0 60 Q 30 30 60 0" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" opacity=".4"/>
    </svg>
    <svg class="sh-archive-trust__ornament sh-archive-trust__ornament--tr" viewBox="0 0 120 120" fill="none" aria-hidden="true">
        <circle cx="100" cy="20" r="3" fill="currentColor" opacity=".55"/>
        <circle cx="80" cy="20" r="2" fill="currentColor" opacity=".35"/>
        <circle cx="100" cy="40" r="2" fill="currentColor" opacity=".35"/>
        <path d="M120 60 Q 90 30 60 0" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" opacity=".4"/>
    </svg>

    <div class="sh-archive-trust__inner">
        <header class="sh-archive-trust__header">
            <!-- Eyebrow flanked by sparkle SVG -->
            <span class="sh-archive-trust__eyebrow">
                <svg class="sh-archive-trust__sparkle" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M12 2 L13.5 9 L20 10.5 L13.5 12 L12 19 L10.5 12 L4 10.5 L10.5 9 Z"/>
                </svg>
                <span>Uy Tín và Chất Lượng</span>
                <svg class="sh-archive-trust__sparkle" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M12 2 L13.5 9 L20 10.5 L13.5 12 L12 19 L10.5 12 L4 10.5 L10.5 9 Z"/>
                </svg>
            </span>
            <h2 class="sh-archive-trust__title">Đối Tác của Sài Gòn Horeca</h2>
            <!-- Divider: gold line + diamond center + line -->
            <span class="sh-archive-trust__divider" aria-hidden="true">
                <svg viewBox="0 0 200 12" fill="none" preserveAspectRatio="none">
                    <line x1="0" y1="6" x2="84" y2="6" stroke="currentColor" stroke-width="1.5" opacity=".5"/>
                    <path d="M100 1 L107 6 L100 11 L93 6 Z" fill="currentColor"/>
                    <line x1="116" y1="6" x2="200" y2="6" stroke="currentColor" stroke-width="1.5" opacity=".5"/>
                </svg>
            </span>
        </header>

        <div class="sh-archive-trust__carousel">
            <div class="sh-archive-trust__track">
                <?php foreach ($partners as $p) : ?>
                    <div class="sh-archive-trust__cell">
                        <img src="<?php echo esc_url(sgh_img('2024/07/') . $p['file']); ?>"
                             alt="<?php echo esc_attr($p['name']); ?>"
                             width="175" height="100"
                             loading="lazy" decoding="async">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="sh-archive-trust__dots" role="group" aria-label="Carousel navigation">
            <?php for ($i = 0; $i < $total_dots; $i++) : ?>
                <button class="sh-archive-trust__dot <?php echo $i === 0 ? 'is-active' : ''; ?>"
                        data-page="<?php echo (int) $i; ?>"
                        type="button"
                        aria-label="Trang <?php echo $i + 1; ?>"></button>
            <?php endfor; ?>
        </div>
    </div>
</section>

<script>
/* Trust carousel — match production: advance từng 1 logo (1 dot/logo).
   Scroll bằng logo-width (kèm gap) thay vì cả page-width. */
(function () {
    var sec = document.querySelector('.sh-archive-trust');
    if (!sec) return;
    var track = sec.querySelector('.sh-archive-trust__track');
    var dots  = sec.querySelectorAll('.sh-archive-trust__dot');
    var cells = sec.querySelectorAll('.sh-archive-trust__cell');
    if (!track || !dots.length || !cells.length) return;
    var current = 0;
    var timer;

    function goTo(idx) {
        idx = ((idx % dots.length) + dots.length) % dots.length;
        var target = cells[idx];
        if (target) {
            // Scroll target cell vào start của track (relative scroll).
            var trackRect  = track.getBoundingClientRect();
            var targetRect = target.getBoundingClientRect();
            var delta = targetRect.left - trackRect.left + track.scrollLeft;
            track.scrollTo({ left: delta, behavior: 'smooth' });
        }
        dots.forEach(function (d, i) { d.classList.toggle('is-active', i === idx); });
        current = idx;
    }
    dots.forEach(function (d, i) {
        d.addEventListener('click', function () { goTo(i); reset(); });
    });
    function next() { goTo(current + 1); }
    function start() { timer = setInterval(next, 3000); }
    function stop()  { clearInterval(timer); }
    function reset() { stop(); start(); }

    sec.addEventListener('mouseenter', stop);
    sec.addEventListener('mouseleave', start);
    start();
})();
</script>
