<?php
/**
 * Template Name: Trang Tin Tức (Custom)
 * @package SaigonHouse
 */
get_header(); ?>

<main id="primary" class="sh-archive">
    <section class="sh-archive__hero category-hero-bg" data-aos="fade-in">
        <div class="sh-archive__hero-inner" data-aos="fade-up" data-aos-delay="80">
            <span class="sh-archive__badge"><?php _e('Khám phá', 'saigonhouse'); ?></span>
            <h1 class="sh-archive__title">Tin Tức & Sự Kiện</h1>
            <div class="sh-archive__desc"><?php _e('Cập nhật những thông tin mới nhất về kiến trúc, phong thủy và cẩm nang xây dựng từ Saigon House.', 'saigonhouse'); ?></div>
            <p class="sh-archive__count">
                <?php
                $paged = (get_query_var('paged')) ? get_query_var('paged') : ((get_query_var('page')) ? get_query_var('page') : 1);
                $news_query = new WP_Query(['post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => 9, 'paged' => $paged]);
                $total = $news_query->found_posts;
                printf(_n('Có tổng cộng <strong>%d</strong> bài viết', 'Có tổng cộng <strong>%d</strong> bài viết', $total, 'saigonhouse'), $total);
                ?>
            </p>
        </div>
        <?php get_template_part('template-parts/components/wave-divider', null, ['fill_attr' => 'var(--color-surface-alt)']); ?>
    </section>

    <div class="sh-archive__content" data-aos="fade-up" data-aos-delay="120">
        <?php if ($news_query->have_posts()) : ?>
            <div class="sh-archive__grid">
                <?php while ($news_query->have_posts()) : $news_query->the_post(); ?>
                    <?php get_template_part('template-parts/components/post-card'); ?>
                <?php endwhile; ?>
            </div>

            <div class="sgh-pagination" data-aos="fade-up" data-aos-delay="180">
                <?php
                $big = 999999999;
                echo paginate_links([
                    'base'      => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                    'format'    => '?paged=%#%',
                    'current'   => max(1, $paged),
                    'total'     => $news_query->max_num_pages,
                    'mid_size'  => 2,
                    'prev_text' => sh_icon('chevron-left', 'sh-archive__page-icon'),
                    'next_text' => sh_icon('chevron-right', 'sh-archive__page-icon'),
                    'type'      => 'plain'
                ]);
                wp_reset_postdata();
                ?>
            </div>

        <?php else : ?>
            <div class="sh-archive__empty" data-aos="zoom-in" data-aos-delay="140">
                <div class="sh-archive__empty-icon">
                    <?php echo sh_icon('folder-open', 'sh-archive__empty-svg'); ?>
                </div>
                <h3 class="sh-archive__empty-title"><?php _e('Chưa có bài viết', 'saigonhouse'); ?></h3>
                <p class="sh-archive__empty-desc"><?php _e('Xin lỗi, hiện tại chưa có bài viết nào được đăng. Vui lòng quay lại sau.', 'saigonhouse'); ?></p>
                <a href="<?php echo home_url('/'); ?>" class="sh-archive__empty-btn"><?php _e('Về trang chủ', 'saigonhouse'); ?></a>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
