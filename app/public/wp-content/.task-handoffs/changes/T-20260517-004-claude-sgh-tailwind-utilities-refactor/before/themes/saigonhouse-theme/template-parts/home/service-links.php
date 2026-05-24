<?php
/**
 * Template Part: Service Links — Floating overlapping cards
 * @package SaigonHouse
 */

$fp_id = (int) get_option('page_on_front');

$services = [
    ['number' => '01', 'title' => get_post_meta($fp_id, '_sgh_svc1_title', true) ?: 'Thiết Kế Kiến Trúc', 'subtitle' => get_post_meta($fp_id, '_sgh_svc1_subtitle', true) ?: 'Sáng tạo không gian sống đẳng cấp', 'link' => get_post_meta($fp_id, '_sgh_svc1_url', true) ?: '/bao-gia-thiet-ke-kien-truc-xay-dung-cong-trinh-1/'],
    ['number' => '02', 'title' => get_post_meta($fp_id, '_sgh_svc2_title', true) ?: 'Xây Dựng Phần Thô', 'subtitle' => get_post_meta($fp_id, '_sgh_svc2_subtitle', true) ?: 'Vững chắc nền móng tương lai', 'link' => get_post_meta($fp_id, '_sgh_svc2_url', true) ?: '/bang-bao-gia-thi-cong-xay-dung-nha-phan-tho-1/'],
    ['number' => '03', 'title' => get_post_meta($fp_id, '_sgh_svc3_title', true) ?: 'Xây Nhà Trọn Gói', 'subtitle' => get_post_meta($fp_id, '_sgh_svc3_subtitle', true) ?: 'Chìa khóa trao tay', 'link' => get_post_meta($fp_id, '_sgh_svc3_url', true) ?: '/bang-bao-gia-xay-dung-nha-tron-goi-1/'],
];
?>
<section class="sh-svc-links">
    <div class="sh-svc-links__positioner">
        <div class="sh-svc-links__grid">
            <?php foreach ($services as $index => $item): ?>
            <a href="<?php echo esc_url($item['link']); ?>" class="sh-svc-card sgh-card-hover" data-aos="flip-up" data-aos-delay="<?php echo $index * 100; ?>">
                <div class="sh-svc-card__bg"></div>
                <div class="sh-svc-card__shine"></div>
                <div class="sh-svc-card__badge"><?php echo esc_html($item['number']); ?></div>
                <div class="sh-svc-card__body">
                    <div class="sh-svc-card__text">
                        <h3 class="sh-svc-card__title"><?php echo esc_html($item['title']); ?></h3>
                        <div class="sh-svc-card__divider"></div>
                        <p class="sh-svc-card__subtitle"><?php echo esc_html($item['subtitle']); ?></p>
                    </div>
                    <div class="sh-svc-card__arrow">
                        <?php echo sh_icon('arrow-right', 'sh-svc-card__arrow-icon'); ?>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
