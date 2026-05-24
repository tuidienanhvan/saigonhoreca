<?php
/**
 * Template Part: Customer Reviews
 * @package SaigonHouse
 */

$reviews = [
    ['name' => 'Anh Minh Tuấn', 'location' => 'Khu Sala, Quận 2', 'badge' => 'Biệt Thự Hiện Đại', 'title' => '"Chuyên nghiệp & Tận tâm"', 'text' => '"Saigon House làm việc rất chuyên nghiệp. Từ khâu thiết kế đến thi công đều rất chi tiết. Tôi hoàn toàn yên tâm khi giao nhà cho các bạn."'],
    ['name' => 'Chị Thanh Thảo', 'location' => 'Biệt thự Đồng Nai', 'badge' => 'Nội Thất Sang Trọng', 'title' => '"Không gian ưng ý"', 'text' => '"Thiết kế của Saigon House toát lên vẻ sang trọng nhưng vẫn rất ấm cúng. Tôi đặc biệt thích cách bố trí ánh sáng và không gian xanh."'],
    ['name' => 'Anh Hoàng', 'location' => 'Nhà Phố Thủ Đức', 'badge' => 'Xây Nhà Trọn Gói', 'title' => '"Đúng tiến độ cam kết"', 'text' => '"Tôi rất hài lòng vì tiến độ thi công nhanh chóng, đúng như cam kết trong hợp đồng. Đội ngũ giám sát rất kỹ lưỡng và nhiệt tình."'],
];

$review_imgs = sh_section_images('biet thu', 3);
?>
<section class="sh-reviews">
    <div class="sh-reviews__bg-pattern"></div>

    <div class="sh-reviews__container">
        <!-- Header -->
        <div class="sh-reviews__header" data-aos="blur-in">
            <div class="sh-reviews__watermark">REVIEWS</div>
            <div class="sh-reviews__header-inner">
                <div class="sh-reviews__bracket sh-reviews__bracket--tl"></div>
                <div class="sh-reviews__bracket sh-reviews__bracket--tr"></div>
                <div class="sh-reviews__bracket sh-reviews__bracket--bl"></div>
                <div class="sh-reviews__bracket sh-reviews__bracket--br"></div>

                <span class="sh-reviews__label">
                    <span class="sh-reviews__label-line"></span>
                    UY TÍN & CHẤT LƯỢNG
                    <span class="sh-reviews__label-line sh-reviews__label-line--right"></span>
                </span>
                <h2 class="sh-reviews__title">CẢM NHẬN <span class="sh-reviews__title-accent">KHÁCH HÀNG</span></h2>
                <div class="sh-reviews__dots">
                    <span class="sh-reviews__dot sh-reviews__dot--sm"></span>
                    <span class="sh-reviews__dot sh-reviews__dot--md"></span>
                    <span class="sh-reviews__dot sh-reviews__dot--lg"></span>
                    <span class="sh-reviews__dot sh-reviews__dot--md"></span>
                    <span class="sh-reviews__dot sh-reviews__dot--sm"></span>
                </div>
            </div>
        </div>

        <!-- Cards -->
        <div class="sh-reviews__grid sh-hscroll">
            <?php foreach ($reviews as $i => $review): ?>
            <div class="sh-reviews__card" data-aos="bounce-up" data-aos-delay="<?php echo $i * 100; ?>">
                <div class="sh-reviews__card-img">
                    <img src="<?php echo esc_url($review_imgs[$i] ?? ''); ?>" alt="<?php echo esc_attr($review['name']); ?>" loading="lazy" decoding="async" class="sh-reviews__card-photo">
                    <span class="sh-reviews__card-badge"><?php echo esc_html($review['badge']); ?></span>
                </div>
                <div class="sh-reviews__card-body">
                    <div class="sh-reviews__stars">
                        <?php for ($s = 0; $s < 5; $s++): ?>
                        <svg class="sh-reviews__star" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <?php endfor; ?>
                    </div>
                    <h3 class="sh-reviews__card-title"><?php echo esc_html($review['title']); ?></h3>
                    <p class="sh-reviews__card-text"><?php echo esc_html($review['text']); ?></p>
                    <div class="sh-reviews__card-author">
                        <img src="<?php echo esc_url($review_imgs[$i] ?? ''); ?>" alt="<?php echo esc_attr($review['name']); ?>" loading="lazy" decoding="async" class="sh-reviews__avatar">
                        <div>
                            <div class="sh-reviews__author-name">
                                <?php echo esc_html($review['name']); ?>
                                <?php echo sh_icon('check-circle', 'sh-reviews__verified'); ?>
                            </div>
                            <div class="sh-reviews__author-location"><?php echo esc_html($review['location']); ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
