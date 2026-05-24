<?php
/**
 * About Page Section — Testimonials
 *
 * Dark luxe: 3-col grid — left info block + 2 review cards.
 * BEM: sh-about-testimonials
 *
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;

$testimonials = [
    [
        'quote'   => 'Tôi rất hài lòng với dịch vụ của Saigon Horeca trong việc cung cấp <strong>thiết bị bếp công nghiệp</strong> chất lượng cao. Nhân viên tư vấn rất nhiệt tình và chuyên nghiệp, giúp tôi chọn lựa được những sản phẩm phù hợp nhất.',
        'author'  => 'Tiffany',
        'company' => 'Heiwa Sushi',
    ],
    [
        'quote'   => 'Saigon Horeca là đối tác đáng tin cậy của chúng tôi trong việc cung cấp <strong>thiết bị quầy bar</strong>. Sản phẩm đa dạng và chất lượng, giúp tạo ra không gian phục vụ thú vị và chuyên nghiệp.',
        'author'  => 'Madam Phuong',
        'company' => 'The Brix Eatery & Lounge',
    ],
];
?>
<section class="sh-about-testimonials" aria-label="<?php esc_attr_e('Nhận xét từ khách hàng', 'saigonhoreca'); ?>">
    <div class="sh-about-testimonials__inner">

        <!-- Left info -->
        <div class="sh-about-testimonials__info">
            <span class="sh-about-testimonials__eyebrow"><?php esc_html_e('Khách hàng nói gì', 'saigonhoreca'); ?></span>
            <h2 class="sh-about-testimonials__heading"><?php esc_html_e('Nhận xét từ khách hàng', 'saigonhoreca'); ?></h2>
            <p class="sh-about-testimonials__body">
                Chúng tôi chia sẻ những đánh giá tích cực từ khách hàng về dịch vụ và sản phẩm của Saigon Horeca — đối tác đáng tin cậy trong giải pháp bếp công nghiệp và quầy bar chất lượng cao tại Việt Nam.
            </p>
        </div>

        <!-- Cards -->
        <?php foreach ($testimonials as $t): ?>
            <div class="sh-about-testimonials__card">
                <div class="sh-about-testimonials__stars" role="img" aria-label="5 sao">
                    <?php for ($i = 0; $i < 5; $i++): ?>
                        <svg viewBox="0 0 20 20" aria-hidden="true">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    <?php endfor; ?>
                </div>
                <blockquote class="sh-about-testimonials__quote">
                    <?php echo $t['quote']; ?>
                </blockquote>
                <footer class="sh-about-testimonials__footer" data-initial="<?php echo esc_attr(mb_substr($t['author'], 0, 1)); ?>">
                    <div class="sh-about-testimonials__footer-text">
                        <cite class="sh-about-testimonials__author"><?php echo esc_html($t['author']); ?></cite>
                        <span class="sh-about-testimonials__company"><?php echo esc_html($t['company']); ?></span>
                    </div>
                </footer>
            </div>
        <?php endforeach; ?>

    </div>
</section>
