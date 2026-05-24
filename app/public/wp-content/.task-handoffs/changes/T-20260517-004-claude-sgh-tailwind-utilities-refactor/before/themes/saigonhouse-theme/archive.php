<?php
/**
 * The template for displaying archive pages
 *
 * @package SaigonHouse
 */

get_header();
?>

<?php sh_breadcrumbs(); ?>

<main id="primary" class="sh-archive">
    <!-- Hero Section -->
    <section class="sh-archive__hero category-hero-bg" data-aos="fade-in">
        <div class="sh-archive__hero-inner" data-aos="fade-up" data-aos-delay="80">
            <span class="sh-archive__badge">
                <?php
                if (is_author()) echo 'Tác giả';
                elseif (is_tag()) echo 'Thẻ';
                elseif (is_year() || is_month() || is_day()) echo 'Lưu trữ';
                else echo 'Chuyên mục';
                ?>
            </span>

            <h1 class="sh-archive__title"><?php the_archive_title(); ?></h1>

            <?php if (get_the_archive_description()) : ?>
                <div class="sh-archive__desc"><?php echo wp_kses_post(get_the_archive_description()); ?></div>
            <?php endif; ?>

            <p class="sh-archive__count">
                <?php
                global $wp_query;
                $total = $wp_query->found_posts;
                printf(_n('Có tổng cộng <strong>%d</strong> bài viết', 'Có tổng cộng <strong>%d</strong> bài viết', $total, 'saigonhouse'), $total);
                ?>
            </p>
        </div>

        <?php get_template_part('template-parts/components/wave-divider'); ?>
    </section>

    <div class="sh-archive__content sh-with-sidebar" data-aos="fade-up" data-aos-delay="120">
        <div class="sh-with-sidebar__main">
            <?php if (have_posts()) : ?>
                <div class="sh-archive__grid">
                    <?php while (have_posts()) : the_post(); ?>
                        <?php get_template_part('template-parts/components/post-card'); ?>
                    <?php endwhile; ?>
                </div>

                <div class="sgh-pagination" data-aos="fade-up" data-aos-delay="180">
                    <?php
                    echo paginate_links([
                        'mid_size'  => 2,
                        'prev_text' => sh_icon('chevron-left', 'sh-archive__page-icon'),
                        'next_text' => sh_icon('chevron-right', 'sh-archive__page-icon'),
                        'type'      => 'plain'
                    ]);
                    ?>
                </div>

            <?php else : ?>
                <div class="sh-archive__empty" data-aos="zoom-in" data-aos-delay="140">
                    <div class="sh-archive__empty-icon">
                        <?php echo sh_icon('folder-open', 'sh-archive__empty-svg'); ?>
                    </div>
                    <h3 class="sh-archive__empty-title">Chưa có bài viết</h3>
                    <p class="sh-archive__empty-desc">Chuyên mục này hiện tại chưa có bài viết nào.</p>
                    <a href="<?php echo home_url(); ?>" class="sh-archive__empty-btn">Về trang chủ</a>
                </div>
            <?php endif; ?>
        </div>
        <aside class="sh-with-sidebar__aside" data-aos="fade-left" data-aos-delay="160">
            <?php get_sidebar(); ?>
        </aside>
    </div>
</main>

<?php
get_footer();
