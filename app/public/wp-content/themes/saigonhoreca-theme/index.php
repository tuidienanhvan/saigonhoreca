<?php
/**
 * Blog Index (Tin Tức)
 */
get_header();
?>

<main id="primary" class="sh-archive" tabindex="-1">
    <section class="category-hero-bg sh-archive__hero">
        <div class="sh-archive__hero-inner">
            <span class="sh-archive__badge"><?php _e('Khám phá', 'saigonhoreca'); ?></span>
            <h1 class="sh-archive__title">
                <?php
                $page_for_posts = get_option('page_for_posts');
                echo esc_html($page_for_posts ? get_the_title($page_for_posts) : __('Tin Tức & Sự Kiện', 'saigonhoreca'));
                ?>
            </h1>
            <div class="sh-archive__desc"><?php _e('Tin tức, kiến thức và cẩm nang chọn mua thiết bị bếp công nghiệp, quầy bar và giải pháp horeca từ Saigon Horeca.', 'saigonhoreca'); ?></div>
            <p class="sh-archive__count">
                <?php
                global $wp_query;
                printf(_n('Có tổng cộng <strong>%d</strong> bài viết', 'Có tổng cộng <strong>%d</strong> bài viết', $wp_query->found_posts, 'saigonhoreca'), $wp_query->found_posts);
                ?>
            </p>
        </div>
        <?php get_template_part('template-parts/components/wave-divider'); ?>
    </section>

    <div class="sh-archive__content">
        <?php if (have_posts()) : ?>
            <div class="sh-archive__grid">
                <?php while (have_posts()) : the_post(); ?>
                    <?php get_template_part('template-parts/components/post-card'); ?>
                <?php endwhile; ?>
            </div>
            <div class="sgh-pagination">
                <?php echo paginate_links(['mid_size' => 2, 'prev_text' => sh_icon('chevron-left', ''), 'next_text' => sh_icon('chevron-right', ''), 'type' => 'plain']); ?>
            </div>
        <?php else : ?>
            <div class="sh-archive__empty">
                <div class="sh-archive__empty-icon"><?php echo sh_icon('folder-open', ''); ?></div>
                <h2 class="sh-archive__empty-title"><?php _e('Chưa có bài viết', 'saigonhoreca'); ?></h2>
                <p class="sh-archive__empty-desc"><?php _e('Hiện tại chưa có bài viết nào. Vui lòng quay lại sau.', 'saigonhoreca'); ?></p>
                <a href="<?php echo home_url('/'); ?>" class="sh-archive__empty-btn"><?php echo sh_icon('home', ''); ?> <?php _e('Về trang chủ', 'saigonhoreca'); ?></a>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
