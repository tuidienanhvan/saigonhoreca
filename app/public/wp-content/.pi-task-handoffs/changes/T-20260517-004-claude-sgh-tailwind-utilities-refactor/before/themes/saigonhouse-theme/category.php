<?php
/**
 * Category Archive Template
 */
get_header();
?>

<?php sh_breadcrumbs(); ?>

<main id="primary" class="sh-archive">
    <section class="category-hero-bg sh-archive__hero" data-aos="fade-in">
        <div class="sh-archive__hero-inner" data-aos="fade-up" data-aos-delay="80">
            <span class="sh-archive__badge">Chuyên mục</span>
            <h1 class="sh-archive__title"><?php single_cat_title(); ?></h1>
            <?php if (category_description()) : ?>
                <div class="sh-archive__desc"><?php echo wp_kses_post(category_description()); ?></div>
            <?php endif; ?>
            <p class="sh-archive__count">
                <?php
                global $wp_query;
                printf(_n('Có tổng cộng <strong>%d</strong> bài viết', 'Có tổng cộng <strong>%d</strong> bài viết', $wp_query->found_posts, 'saigonhouse'), $wp_query->found_posts);
                ?>
            </p>
        </div>
        <?php get_template_part('template-parts/components/wave-divider'); ?>
    </section>

    <div class="sh-archive__content" data-aos="fade-up" data-aos-delay="120">
        <?php if (have_posts()) : ?>
            <div class="sh-archive__grid">
                <?php while (have_posts()) : the_post(); ?>
                    <?php get_template_part('template-parts/components/post-card'); ?>
                <?php endwhile; ?>
            </div>
            <div class="sgh-pagination" data-aos="fade-up" data-aos-delay="180">
                <?php echo paginate_links(['mid_size' => 2, 'prev_text' => sh_icon('chevron-left', ''), 'next_text' => sh_icon('chevron-right', ''), 'type' => 'plain']); ?>
            </div>
        <?php else : ?>
            <div class="sh-archive__empty" data-aos="zoom-in" data-aos-delay="140">
                <div class="sh-archive__empty-icon"><?php echo sh_icon('folder-open', ''); ?></div>
                <h2 class="sh-archive__empty-title"><?php _e('Thư mục trống', 'saigonhouse'); ?></h2>
                <p class="sh-archive__empty-desc"><?php _e('Hiện tại chưa có bài viết nào trong chuyên mục này.', 'saigonhouse'); ?></p>
                <a href="<?php echo home_url('/'); ?>" class="sh-archive__empty-btn"><?php echo sh_icon('home', ''); ?> <?php _e('Về trang chủ', 'saigonhouse'); ?></a>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
