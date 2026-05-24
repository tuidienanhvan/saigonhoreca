<?php
/**
 * Archive product — filter sidebar (categories + brands).
 *
 * Renders product_category HIERARCHICALLY (parent + indented children),
 * matching production saigonhoreca.vn structure. Brands flat.
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) exit;

$all_cats = get_terms([
    'taxonomy'   => 'product_category',
    'hide_empty' => true,
    'orderby'    => 'name',
]);
$brands = get_terms([
    'taxonomy'   => 'product_brand',
    'hide_empty' => true,
    'orderby'    => 'name',
]);

if (empty($all_cats) && empty($brands)) return;

$current_term = is_tax(['product_category', 'product_brand']) ? get_queried_object() : null;

// Build parent → children map
$by_parent = [];
$by_id = [];
if (!is_wp_error($all_cats)) {
    foreach ($all_cats as $c) {
        $by_id[$c->term_id] = $c;
        $by_parent[(int) $c->parent][] = $c;
    }
}

/**
 * Recursive renderer — outputs <li> for one term + its children as nested <ul>.
 */
$render_cat = function ($term, $depth = 0) use (&$render_cat, &$by_parent, $current_term) {
    $is_current = $current_term && $current_term->taxonomy === 'product_category'
        && (int) $current_term->term_id === (int) $term->term_id;
    $children = $by_parent[$term->term_id] ?? [];
    ?>
    <li class="sh-filter-block__item<?php echo $is_current ? ' is-current' : ''; ?> sh-filter-block__item--depth-<?php echo (int) $depth; ?>">
        <a href="<?php echo esc_url(get_term_link($term)); ?>">
            <span><?php echo esc_html($term->name); ?></span>
            <span class="sh-filter-block__count">(<?php echo (int) $term->count; ?>)</span>
        </a>
        <?php if (!empty($children)) : ?>
            <ul class="sh-filter-block__sublist">
                <?php foreach ($children as $child) : $render_cat($child, $depth + 1); endforeach; ?>
            </ul>
        <?php endif; ?>
    </li>
    <?php
};

$top_level = $by_parent[0] ?? [];
?>

<aside class="sh-archive__sidebar" aria-label="<?php esc_attr_e('Lọc sản phẩm', 'saigonhoreca'); ?>">
    <?php if (!empty($top_level)) : ?>
        <div class="sh-filter-block">
            <h2 class="sh-filter-block__title"><?php _e('Danh mục sản phẩm', 'saigonhoreca'); ?></h2>
            <ul class="sh-filter-block__list">
                <?php foreach ($top_level as $cat) { $render_cat($cat, 0); } ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (!is_wp_error($brands) && !empty($brands)) : ?>
        <div class="sh-filter-block">
            <h2 class="sh-filter-block__title"><?php _e('Thương hiệu', 'saigonhoreca'); ?></h2>
            <ul class="sh-filter-block__list">
                <?php foreach ($brands as $b) :
                    $is_current = $current_term && $current_term->taxonomy === 'product_brand' && (int) $current_term->term_id === (int) $b->term_id; ?>
                    <li class="sh-filter-block__item<?php echo $is_current ? ' is-current' : ''; ?>">
                        <a href="<?php echo esc_url(get_term_link($b)); ?>">
                            <span><?php echo esc_html($b->name); ?></span>
                            <span class="sh-filter-block__count">(<?php echo (int) $b->count; ?>)</span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
</aside>
