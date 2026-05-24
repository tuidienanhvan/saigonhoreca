<?php
/**
 * Template Part: Premium FAQ Accordion
 *
 * @package SaigonHouse
 */

$items = $args['items'] ?? [];
$title = $args['title'] ?? 'Câu Hỏi Thường Gặp';
$bg_dark = ($args['bg'] ?? '') === 'dark';

if (empty($items)) return;
?>

<section class="sh-faq-premium <?php echo $bg_dark ? 'sh-faq-premium--dark' : ''; ?>" data-aos="fade-up">
    <div class="sh-faq-premium__container">
        
        <div class="sh-faq-premium__header" data-aos="fade-down">
            <span class="sh-faq-premium__badge">FAQ</span>
            <h2 class="sh-faq-premium__title"><?php echo esc_html($title); ?></h2>
        </div>

        <div class="sh-faq-premium__list">
            <?php foreach ($items as $i => $item): ?>
            <div class="sh-faq-p-item" data-aos="fade-up" data-aos-delay="<?php echo $i * 50; ?>">
                <button class="sh-faq-p-trigger" aria-expanded="false">
                    <span class="sh-faq-p-question"><?php echo esc_html($item['q']); ?></span>
                    <span class="sh-faq-p-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                    </span>
                </button>
                <div class="sh-faq-p-content">
                    <div class="sh-faq-p-inner">
                        <?php echo wp_kses_post($item['a']); ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="sh-faq-premium__cta">
            <p>Còn câu hỏi khác? <a href="tel:0961868968">0961 868 968</a></p>
        </div>

    </div>
</section>

<script>
(function() {
    const items = document.querySelectorAll('.sh-faq-p-item');
    
    items.forEach(item => {
        const trigger = item.querySelector('.sh-faq-p-trigger');
        const content = item.querySelector('.sh-faq-p-content');
        
        trigger.addEventListener('click', () => {
            const isActive = item.classList.contains('is-active');
            
            // Close others
            items.forEach(other => {
                if (other !== item) {
                    other.classList.remove('is-active');
                    other.querySelector('.sh-faq-p-trigger').setAttribute('aria-expanded', 'false');
                    other.querySelector('.sh-faq-p-content').style.maxHeight = '0';
                }
            });
            
            // Toggle current
            if (isActive) {
                item.classList.remove('is-active');
                trigger.setAttribute('aria-expanded', 'false');
                content.style.maxHeight = '0';
            } else {
                item.classList.add('is-active');
                trigger.setAttribute('aria-expanded', 'true');
                content.style.maxHeight = content.scrollHeight + 'px';
            }
        });
    });
})();
</script>
