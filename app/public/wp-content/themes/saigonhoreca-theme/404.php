<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package SaigonHoreca
 */

get_header();
?>

<?php sh_breadcrumbs(); ?>

<div class="error-page">
    <div class="error-page__container">
<?php
$lang = function_exists('sgh_get_current_lang') ? sgh_get_current_lang() : 'vi';

$err_titles = [
    'vi' => 'Không tìm thấy trang',
    'en' => 'Page Not Found',
    'ja' => 'ページが見つかりません',
];

$err_descs = [
    'vi' => 'Xin lỗi, trang bạn đang tìm kiếm có thể đã bị xóa, đổi tên hoặc tạm thời không truy cập được.',
    'en' => 'Sorry, the page you are looking for might have been removed, had its name changed, or is temporarily unavailable.',
    'ja' => 'お探しのページは削除されたか、名前が変更されたか、一時的に利用できない可能性があります。',
];

$err_search_placeholders = [
    'vi' => 'Tìm kiếm trên website...',
    'en' => 'Search on website...',
    'ja' => 'サイト内を検索...',
];

$err_search_btns = [
    'vi' => 'Tìm',
    'en' => 'Search',
    'ja' => '検索',
];

$err_home_btns = [
    'vi' => 'Về trang chủ',
    'en' => 'Back to Home',
    'ja' => 'ホームへ戻る',
];

$err_contact_btns = [
    'vi' => 'Liên hệ hỗ trợ',
    'en' => 'Contact Support',
    'ja' => 'お問い合わせ',
];

$err_popular_titles = [
    'vi' => 'Bài viết phổ biến',
    'en' => 'Popular Articles',
    'ja' => '人気の記事',
];
?>
        <div class="error-page__content">
            <h1 class="error-page__code">404</h1>
            <div class="error-page__body">
                <h2 class="error-page__title"><?php echo esc_html($err_titles[$lang] ?? $err_titles['vi']); ?></h2>
                <p class="error-page__desc">
                    <?php echo esc_html($err_descs[$lang] ?? $err_descs['vi']); ?>
                </p>

                <!-- Search Box -->
                <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>" class="error-page__search">
                    <div class="error-page__search-row">
                        <input type="search" name="s" placeholder="<?php echo esc_attr($err_search_placeholders[$lang] ?? $err_search_placeholders['vi']); ?>" class="error-page__search-input" />
                        <button type="submit" class="error-page__search-btn"><?php echo esc_html($err_search_btns[$lang] ?? $err_search_btns['vi']); ?></button>
                    </div>
                </form>

                <div class="error-page__actions">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="error-page__btn error-page__btn--primary">
                        <?php echo sh_icon('home', 'error-page__icon'); ?> <?php echo esc_html($err_home_btns[$lang] ?? $err_home_btns['vi']); ?>
                    </a>
                    <a href="<?php echo esc_url(function_exists('sgh_url') ? sgh_url('contact') : home_url('/lien-he/')); ?>" class="error-page__btn error-page__btn--secondary">
                        <?php echo esc_html($err_contact_btns[$lang] ?? $err_contact_btns['vi']); ?>
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
                <div class="error-page__popular">
                    <h3 class="error-page__popular-title"><?php echo esc_html($err_popular_titles[$lang] ?? $err_popular_titles['vi']); ?></h3>
                    <div class="error-page__popular-grid">
                        <?php while ($popular->have_posts()): $popular->the_post();
                            $thumb = saigonhouse_get_post_thumbnail_data(get_the_ID(), 'thumbnail');
                        ?>
                        <a href="<?php the_permalink(); ?>" class="error-page__post-card">
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
