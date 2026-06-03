<?php
/**
 * FAQ Accordion Component — reusable, JSON-LD schema
 *
 * Usage:
 *   get_template_part('template-parts/components/faq-accordion', null, [
 *       'title'  => 'Câu Hỏi Thường Gặp',
 *       'items'  => [['q' => 'Câu hỏi?', 'a' => 'Trả lời...']],
 *       'schema' => true,
 *       'bg'     => 'gray', // 'gray' | 'white' | 'green'
 *   ]);
 *
 * @package SaigonHouse
 */

$items  = $args['items']  ?? [];
$title  = $args['title']  ?? 'Câu Hỏi Thường Gặp';
$schema = $args['schema'] ?? true;
$bg     = $args['bg']     ?? 'gray';

if (empty($items)) return;

$bg_mod = '';
if ($bg === 'white') $bg_mod = 'sh-faq--white';
elseif ($bg === 'green') $bg_mod = 'sh-faq--green';
?>

<?php if ($schema): ?>
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [
        <?php foreach ($items as $i => $item):
            $comma = ($i < count($items) - 1) ? ',' : ''; ?>
        {
            "@type": "Question",
            "name": <?php echo wp_json_encode($item['q']); ?>,
            "acceptedAnswer": {
                "@type": "Answer",
                "text": <?php echo wp_json_encode(wp_strip_all_tags($item['a'])); ?>
            }
        }<?php echo $comma; ?>
        <?php endforeach; ?>
    ]
}
</script>
<?php endif; ?>

<section class="sh-faq <?php echo $bg_mod; ?>" data-aos="fade-up">

    <div class="sh-faq__container">
        <div class="sh-faq__header" data-aos="fade-up" data-aos-delay="80">
            <span class="sh-faq__badge">FAQ</span>
            <h2 class="sh-faq__title"><?php echo esc_html($title); ?></h2>
        </div>

        <div class="sh-faq__list">
            <?php foreach ($items as $i => $item): ?>
            <div class="sh-faq-item" data-aos="fade-up" data-aos-delay="<?php echo esc_attr(min(420, 120 + ($i * 70))); ?>">
                <button class="sh-faq-question"
                        aria-expanded="false"
                        aria-controls="faq-body-<?php echo esc_attr($i); ?>"
                        id="faq-btn-<?php echo esc_attr($i); ?>">
                    <span><?php echo esc_html($item['q']); ?></span>
                    <span class="sh-faq-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path class="sh-faq-plus-v" d="M12 5v14"/>
                            <path d="M5 12h14"/>
                        </svg>
                    </span>
                </button>
                <div class="sh-faq-body"
                     id="faq-body-<?php echo esc_attr($i); ?>"
                     role="region"
                     aria-labelledby="faq-btn-<?php echo esc_attr($i); ?>">
                    <div class="sh-faq-answer">
                        <?php echo wp_kses_post($item['a']); ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <p class="sh-faq__cta">
            Còn câu hỏi khác? <?php
                $phone = function_exists('saigonhouse_contact') ? saigonhouse_contact('hotline') : '0961 868 968';
            ?>
            <a href="<?php echo function_exists('saigonhouse_phone_link') ? saigonhouse_phone_link() : 'tel:0961868968'; ?>"><?php echo esc_html($phone); ?></a>
        </p>
    </div>
</section>

<script>
(function () {
    var items = document.querySelectorAll('.sh-faq-item');
    if (!items.length) return;

    items.forEach(function (item) {
        var btn  = item.querySelector('.sh-faq-question');
        var body = item.querySelector('.sh-faq-body');
        var icon = item.querySelector('.sh-faq-icon');
        var plusV = item.querySelector('.sh-faq-plus-v');
        if (!btn || !body) return;

        btn.addEventListener('click', function () {
            var isOpen = btn.getAttribute('aria-expanded') === 'true';

            items.forEach(function (other) {
                var otherBtn  = other.querySelector('.sh-faq-question');
                var otherBody = other.querySelector('.sh-faq-body');
                var otherIcon = other.querySelector('.sh-faq-icon');
                var otherPlusV = other.querySelector('.sh-faq-plus-v');
                if (otherBtn && otherBody && otherBtn !== btn) {
                    otherBtn.setAttribute('aria-expanded', 'false');
                    otherBody.style.maxHeight = '0';
                    if (otherIcon) otherIcon.style.backgroundColor = '';
                    if (otherPlusV) otherPlusV.style.opacity = '1';
                    otherIcon && (otherIcon.querySelector('svg').style.transform = '');
                }
            });

            if (isOpen) {
                btn.setAttribute('aria-expanded', 'false');
                body.style.maxHeight = '0';
                if (icon) { icon.style.backgroundColor = ''; icon.style.color = ''; }
                if (plusV) plusV.style.opacity = '1';
                icon && (icon.querySelector('svg').style.transform = '');
            } else {
                btn.setAttribute('aria-expanded', 'true');
                body.style.maxHeight = body.scrollHeight + 'px';
                if (icon) { icon.style.backgroundColor = 'var(--brand, #007d3d)'; icon.style.color = '#fff'; icon.querySelector('svg').style.stroke = '#fff'; }
                if (plusV) plusV.style.opacity = '0';
                icon && (icon.querySelector('svg').style.transform = 'rotate(45deg)');
            }
        });

        btn.addEventListener('keydown', function (e) {
            if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                e.preventDefault();
                var allBtns = Array.from(document.querySelectorAll('.sh-faq-question'));
                var idx = allBtns.indexOf(btn);
                var next = e.key === 'ArrowDown' ? allBtns[idx + 1] : allBtns[idx - 1];
                if (next) next.focus();
            }
        });
    });
})();
</script>
