<?php
/**
 * Breadcrumbs — Reusable component with Schema.org BreadcrumbList
 *
 * Usage: sh_breadcrumbs() — auto-detects page type
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) exit;

/**
 * Output breadcrumbs HTML with JSON-LD schema
 */
function sh_breadcrumbs() {
    if (is_front_page()) return;

    $items = [['name' => 'Trang chủ', 'url' => home_url('/')]];

    if (is_singular('post')) {
        $cats = get_the_category();
        if (!empty($cats)) {
            $cat = $cats[0];
            // Parent category first
            if ($cat->parent) {
                $parent = get_category($cat->parent);
                $items[] = ['name' => $parent->name, 'url' => get_category_link($parent->term_id)];
            }
            $items[] = ['name' => $cat->name, 'url' => get_category_link($cat->term_id)];
        } else {
            $items[] = ['name' => 'Tin Tức', 'url' => sgh_url('news')];
        }
        $items[] = ['name' => get_the_title()];

    } elseif (is_singular('product')) {
        $items[] = ['name' => 'Sản phẩm', 'url' => get_post_type_archive_link('product') ?: sgh_url('products_index')];
        // Deepest product_category — walk parent chain from leaf to root.
        $terms = get_the_terms(get_the_ID(), 'product_category');
        if (!is_wp_error($terms) && !empty($terms)) {
            $leaf = $terms[0];
            // Pick the deepest (most-nested) term if multiple
            foreach ($terms as $t) {
                if ((int) $t->parent !== 0) { $leaf = $t; break; }
            }
            $chain = [];
            $cur = $leaf;
            while ($cur) {
                array_unshift($chain, $cur);
                $cur = $cur->parent ? get_term($cur->parent, 'product_category') : null;
                if (is_wp_error($cur)) $cur = null;
            }
            foreach ($chain as $t) {
                $items[] = ['name' => $t->name, 'url' => get_term_link($t)];
            }
        }
        $items[] = ['name' => get_the_title()];

    } elseif (is_singular('project')) {
        $items[] = ['name' => 'Dự án', 'url' => get_post_type_archive_link('project') ?: sgh_url('projects_index')];
        $items[] = ['name' => get_the_title()];

    } elseif (is_tax('product_category')) {
        $items[] = ['name' => 'Sản phẩm', 'url' => get_post_type_archive_link('product') ?: sgh_url('products_index')];
        $term = get_queried_object();
        // Walk parent chain
        $chain = [];
        $cur = $term;
        while ($cur) {
            array_unshift($chain, $cur);
            $cur = $cur->parent ? get_term($cur->parent, 'product_category') : null;
            if (is_wp_error($cur)) $cur = null;
        }
        $last_idx = count($chain) - 1;
        foreach ($chain as $idx => $t) {
            $entry = ['name' => $t->name];
            if ($idx !== $last_idx) $entry['url'] = get_term_link($t);
            $items[] = $entry;
        }

    } elseif (is_tax('product_brand')) {
        $items[] = ['name' => 'Sản phẩm', 'url' => get_post_type_archive_link('product') ?: sgh_url('products_index')];
        $items[] = ['name' => 'Thương hiệu']; // label-only, no listing page
        $term = get_queried_object();
        $items[] = ['name' => $term->name];

    } elseif (is_post_type_archive('product')) {
        $items[] = ['name' => 'Sản phẩm'];

    } elseif (is_post_type_archive('project')) {
        $items[] = ['name' => 'Dự án'];

    } elseif (is_category()) {
        $cat = get_queried_object();
        if ($cat->parent) {
            $parent = get_category($cat->parent);
            $items[] = ['name' => $parent->name, 'url' => get_category_link($parent->term_id)];
        }
        $items[] = ['name' => $cat->name];

    } elseif (is_tag()) {
        $items[] = ['name' => 'Tag: ' . single_tag_title('', false)];

    } elseif (is_search()) {
        $items[] = ['name' => 'Tìm kiếm: ' . get_search_query()];

    } elseif (is_archive()) {
        $items[] = ['name' => get_the_archive_title()];

    } elseif (is_page()) {
        $post = get_queried_object();
        // Parent page
        if ($post->post_parent) {
            $parent = get_post($post->post_parent);
            $items[] = ['name' => $parent->post_title, 'url' => get_permalink($parent->ID)];
        }
        $items[] = ['name' => get_the_title()];

    } elseif (is_404()) {
        $items[] = ['name' => 'Không tìm thấy trang'];
    }

    // HTML output
    $chevron = function_exists('sh_icon') ? sh_icon('chevron-right', 'sh-bc__chevron', 'aria-hidden="true"') : '<span class="sh-bc__sep">/</span>';
    ?>
    <nav class="sh-bc" aria-label="Breadcrumb">
        <div class="sh-bc__container">
            <ol class="sh-bc__list" itemscope itemtype="https://schema.org/BreadcrumbList">
                <?php foreach ($items as $i => $item):
                    $is_last = ($i === count($items) - 1);
                    if ($i > 0) echo $chevron;
                ?>
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <?php if (!$is_last && isset($item['url'])): ?>
                        <a href="<?php echo esc_url($item['url']); ?>" class="sh-bc__link" itemprop="item">
                            <?php if ($i === 0 && function_exists('sh_icon')) echo sh_icon('home', 'sh-bc__home-icon', 'aria-hidden="true"'); ?>
                            <span itemprop="name"><?php echo esc_html($item['name']); ?></span>
                        </a>
                    <?php else: ?>
                        <span class="sh-bc__current" itemprop="name"><?php echo esc_html($item['name']); ?></span>
                    <?php endif; ?>
                    <meta itemprop="position" content="<?php echo $i + 1; ?>">
                </li>
                <?php endforeach; ?>
            </ol>
        </div>
    </nav>
    <?php

    // JSON-LD Schema
    $schema_items = [];
    foreach ($items as $i => $item) {
        $entry = [
            '@type'    => 'ListItem',
            'position' => $i + 1,
            'name'     => $item['name'],
        ];
        if (isset($item['url'])) {
            $entry['item'] = $item['url'];
        } elseif ($i === count($items) - 1) {
            // Last item: include current page URL per Google recommendation
            $entry['item'] = is_singular() ? get_permalink() : (is_category() || is_tag() ? get_term_link(get_queried_object()) : home_url($_SERVER['REQUEST_URI'] ?? '/'));
        }
        $schema_items[] = $entry;
    }
    echo '<script type="application/ld+json">' . wp_json_encode([
        '@context'        => 'https://schema.org',
        '@type'           => 'BreadcrumbList',
        'itemListElement' => $schema_items,
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
}
