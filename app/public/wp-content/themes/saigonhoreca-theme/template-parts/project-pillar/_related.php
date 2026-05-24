<?php
/**
 * Related Projects — shared component cho mọi pillar page.
 *
 * Lấy 3 pillar pages khác (slug khác current) từ file system, hiển thị
 * thumbnail từ first hero image trong JSON data. Skip current slug.
 *
 * @package SaigonHoreca
 */
if (!defined('ABSPATH')) exit;

$theme_dir = get_template_directory();
$uploads_dir = function_exists('sgh_uploads_base_dir') ? sgh_uploads_base_dir() : wp_normalize_path(wp_get_upload_dir()['basedir']);

// Current slug — đoán từ REQUEST_URI hoặc page post_name
$current_slug = '';
if (isset($_SERVER['REQUEST_URI'])
    && preg_match('#/du-an/([a-z0-9\-]+)/?#', $_SERVER['REQUEST_URI'], $m)) {
    $current_slug = $m[1];
}

// Walk project-data JSON files để build list
$data_dir = $theme_dir . '/scripts/project-data';
$all = [];
foreach (glob($data_dir . '/*.json') as $f) {
    $slug = basename($f, '.json');
    if ($slug === $current_slug) continue;
    $j = @json_decode(@file_get_contents($f), true);
    if (!is_array($j)) continue;

    // Find first content image that EXISTS on disk (skip missing/logo/flag).
    // Bug observed: scraper may reference images không download được —
    // verify file_exists để tránh broken thumbnails.
    $thumb = '';
    $candidates = [];
    array_walk_recursive($j, function ($v) use (&$candidates) {
        if (!is_string($v)) return;
        if (strpos($v, '__TEMPLATE_URI__/static-mirror/wp-content/uploads/') === 0
            && !preg_match('/(logo|flag|favicon|icon-)/i', $v)) {
            $candidates[] = $v;
        }
    });
    foreach ($candidates as $c) {
        $rel = str_replace('__TEMPLATE_URI__/static-mirror/wp-content/uploads/', '', $c);
        $abs = $uploads_dir . '/' . $rel;
        if (file_exists($abs)) {
            $thumb = $rel;
            break;
        }
    }

    // Skip project entirely if no valid thumb (avoid broken image card).
    if (!$thumb) continue;

    $all[] = [
        'slug'  => $slug,
        'title' => $j['title'] ?? ucwords(str_replace('-', ' ', $slug)),
        'thumb' => sgh_img($thumb),
    ];
}

// Shuffle for variety, take 3
shuffle($all);
$related = array_slice($all, 0, 3);
if (empty($related)) return;
?>
<section class="pp__section">
    <div class="pp__container">
        <div class="pp-text pp-text--center" style="margin-bottom: 3rem;">
            <span class="pp-text__divider pp-text__divider--center" aria-hidden="true"></span>
            <h2 class="pp-text__title">Những dự án thiết kế bếp nổi bật của Saigon Horeca</h2>
        </div>
        <div class="pp-related">
            <?php foreach ($related as $r) : ?>
                <a href="<?php echo esc_url(home_url('/du-an/' . $r['slug'] . '/')); ?>"
                   class="pp-related__card"
                   aria-label="<?php echo esc_attr($r['title']); ?>">
                    <?php if ($r['thumb']) : ?>
                        <img src="<?php echo esc_url($r['thumb']); ?>"
                             alt="<?php echo esc_attr($r['title']); ?>"
                             class="pp-related__img"
                             loading="lazy" decoding="async">
                    <?php endif; ?>
                    <div class="pp-related__overlay" aria-hidden="true"></div>
                    <div class="pp-related__content">
                        <p class="pp-related__tag">Dự án Horeca</p>
                        <h3 class="pp-related__title"><?php echo esc_html($r['title']); ?></h3>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
