<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package SaigonHouse
 */

get_header();
?>

<?php sh_breadcrumbs(); ?>

<div class="error-page" data-aos="fade-up">
    <div class="error-page__container" data-aos="fade-up" data-aos-delay="60">
        <div class="error-page__content" data-aos="fade-up" data-aos-delay="100">
            <h1 class="error-page__code">404</h1>
            <div class="error-page__body">
                <h2 class="error-page__title">Không tìm thấy trang</h2>
                <p class="error-page__desc">
                    Xin lỗi, trang bạn đang tìm kiếm có thể đã bị xóa, đổi tên hoặc tạm thời không truy cập được.
                </p>

                <!-- Search Box -->
                <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>" class="error-page__search">
                    <div class="error-page__search-row">
                        <input type="search" name="s" placeholder="Tìm kiếm trên website..." class="error-page__search-input" />
                        <button type="submit" class="error-page__search-btn">Tìm</button>
                    </div>
                </form>

                <div class="error-page__actions" data-aos="fade-up" data-aos-delay="140">
                    <a href="<?php echo home_url(); ?>" class="error-page__btn error-page__btn--primary">
                        <?php echo sh_icon('home', 'error-page__icon'); ?> Về trang chủ
                    </a>
                    <a href="<?php echo home_url('/lien-he'); ?>" class="error-page__btn error-page__btn--secondary">
                        Liên hệ hỗ trợ
                    </a>
                </div>

                <!-- Popular Posts -->
                <?php
                $popular = new WP_Query([
                    'post_type' => 'post', 'posts_per_page' => 4, 'post_status' => 'publish',
                    'meta_key' => '_pi_post_views', 'orderby' => 'meta_value_num', 'order' => 'DESC',
                    'no_found_rows' => true,
                ]);
                if ($popular->have_posts()): ?>
                <div class="error-page__popular" data-aos="fade-up" data-aos-delay="180">
                    <h3 class="error-page__popular-title">Bài viết phổ biến</h3>
                    <div class="error-page__popular-grid">
                        <?php while ($popular->have_posts()): $popular->the_post();
                            $thumb = saigonhouse_get_post_thumbnail_data(get_the_ID(), 'thumbnail');
                        ?>
                        <a href="<?php the_permalink(); ?>" class="error-page__post-card" data-aos="zoom-in-up" data-aos-delay="<?php echo esc_attr(min(420, 100 + ($popular->current_post * 70))); ?>">
                            <img src="<?php echo esc_url($thumb['url']); ?>" alt="<?php the_title_attribute(); ?>" class="error-page__post-thumb" loading="lazy">
                            <div class="error-page__post-info">
                                <div class="error-page__post-title"><?php the_title(); ?></div>
                                <div class="error-page__post-date"><?php echo get_the_date('d/m/Y'); ?></div>
                            </div>
                        </a>
                        <?php endwhile; wp_reset_postdata(); ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
get_footer();
