<?php
/**
 * The template for displaying author archive pages
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) exit;

get_header();

// Lấy thông tin tác giả hiện tại
$author = get_user_by('slug', get_query_var('author_name'));
if (!$author) {
    $author = get_userdata(get_query_var('author'));
}

$author_id = $author ? $author->ID : 1;
$author_name = $author ? $author->display_name : get_the_author();
$author_desc = get_the_author_meta('description', $author_id) ?: 'Đội ngũ chuyên gia Saigon Horeca — chuyên tư vấn thiết bị bếp công nghiệp, quầy bar và giải pháp horeca cho nhà hàng, khách sạn.';
$author_email = get_the_author_meta('user_email', $author_id);
$avatar_url = get_avatar_url($author_id, ['size' => 140]);
?>

<?php sh_breadcrumbs(); ?>

<main id="primary" class="sh-archive sh-author-archive" tabindex="-1">
    <!-- Hero Section tác giả cực kỳ sang trọng -->
    <section class="sh-archive__hero category-hero-bg sh-author-hero">
        <div class="sh-archive__hero-inner sh-author-hero__inner">
            <div class="sh-author-profile">
                <div class="sh-author-profile__avatar">
                    <img src="<?php echo esc_url($avatar_url); ?>" alt="<?php echo esc_attr($author_name); ?>" class="sh-author-profile__img" width="140" height="140">
                </div>
                <div class="sh-author-profile__info">
                    <span class="sh-archive__badge"><?php _e('Tác giả bài viết', 'saigonhoreca'); ?></span>
                    <h1 class="sh-archive__title sh-author-profile__name"><?php echo esc_html($author_name); ?></h1>
                    
                    <?php if ($author_desc) : ?>
                        <div class="sh-archive__desc sh-author-profile__desc"><?php echo wp_kses_post($author_desc); ?></div>
                    <?php endif; ?>

                    <div class="sh-author-profile__meta">
                        <span class="sh-author-profile__count">
                            <?php
                            global $wp_query;
                            $total = $wp_query->found_posts;
                            printf(_n('Đã chia sẻ <strong>%d</strong> bài viết hữu ích', 'Đã chia sẻ <strong>%d</strong> bài viết hữu ích', $total, 'saigonhoreca'), $total);
                            ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <?php get_template_part('template-parts/components/wave-divider'); ?>
    </section>

    <div class="sh-archive__content sh-with-sidebar">
        <div class="sh-with-sidebar__main">
            <h2 class="sh-author-archive__list-title"><?php printf(__('Tất cả bài viết từ %s', 'saigonhoreca'), esc_html($author_name)); ?></h2>
            
            <?php if (have_posts()) : ?>
                <div class="sh-archive__grid">
                    <?php while (have_posts()) : the_post(); ?>
                        <?php get_template_part('template-parts/components/post-card'); ?>
                    <?php endwhile; ?>
                </div>

                <div class="sgh-pagination">
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
                <div class="sh-archive__empty">
                    <div class="sh-archive__empty-icon">
                        <?php echo sh_icon('folder-open', 'sh-archive__empty-svg'); ?>
                    </div>
                    <h3 class="sh-archive__empty-title">Chưa có bài viết</h3>
                    <p class="sh-archive__empty-desc">Tác giả này hiện tại chưa xuất bản bài viết nào.</p>
                    <a href="<?php echo home_url(); ?>" class="sh-archive__empty-btn">Về trang chủ</a>
                </div>
            <?php endif; ?>
        </div>
        <aside class="sh-with-sidebar__aside">
            <?php get_sidebar(); ?>
        </aside>
    </div>
</main>

<?php
get_footer();
