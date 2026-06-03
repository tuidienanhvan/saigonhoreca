<?php
/**
 * T-20260519-003 Phase D — wp_insert_post for every parsed entity.
 *
 * Reads:
 *   out/products.json   → CPT `product`
 *   out/projects.json   → CPT `project`
 *   out/pages.json      → post_type `page`
 *   out/posts.json      → post_type `post`
 *   out/terms.json      → seeds for `product_category` (only ones that ACTUALLY
 *                         have at least one product referencing them are kept)
 *   out/attachment-map.json — src_url -> attachment_id (from Phase C)
 *
 * For each entity:
 *   - Look up existing post by post_name + post_type (re-runnable)
 *   - wp_insert_post / wp_update_post with rewritten content
 *   - Set featured image (via attachment-map lookup on featured_image_src)
 *   - Rewrite scraped image URLs in content -> local /wp-content/uploads/... URLs
 *   - Rewrite production saigonhoreca.vn page URLs -> local home_url
 *   - Set categories/tags via wp_set_object_terms
 *
 * Run via:
 *   wp --path=<site> eval-file scripts/import-posts.php [--type=product]
 *
 * Output:
 *   out/post-map.json: { slug => post_id }
 */

if (!defined('ABSPATH')) {
    fwrite(STDERR, "FATAL: must be run via wp-cli `wp eval-file`\n");
    exit(1);
}

$root = dirname(__DIR__);
$out_dir = $root . '/out';

// Allow filtering by post_type via env var (since wp eval-file doesn't pass args).
$only_type = getenv('SGH_IMPORT_TYPE') ?: '';

function shr_load_json($path) {
    if (!is_readable($path)) {
        fwrite(STDERR, "  missing: $path\n");
        return [];
    }
    return json_decode(file_get_contents($path), true) ?: [];
}

$products = shr_load_json("$out_dir/products.json");
$projects = shr_load_json("$out_dir/projects.json");
$pages    = shr_load_json("$out_dir/pages.json");
$posts    = shr_load_json("$out_dir/posts.json");
$terms    = shr_load_json("$out_dir/terms.json");
$att_map  = shr_load_json("$out_dir/attachment-map.json");

echo "[import-posts] products=" . count($products)
   . " projects=" . count($projects)
   . " pages=" . count($pages)
   . " posts=" . count($posts)
   . " terms=" . count($terms)
   . " attachments=" . count($att_map) . "\n";

$wp_upload = wp_upload_dir();
$local_uploads_url = trailingslashit($wp_upload['baseurl']);  // e.g. https://saigonhoreca.local/wp-content/uploads/
$home_url = trailingslashit(home_url('/'));

/**
 * Rewrite production URLs in content HTML so all references stay local.
 *   wp-content/uploads/... → local same path (host swap)
 *   any other saigonhoreca.vn URL → local home_url
 */
function shr_rewrite_content($html, $local_uploads_url, $home_url) {
    if (!$html) return $html;
    $html = preg_replace(
        '#https?://(?:www\\.)?saigonhoreca\\.vn/wp-content/uploads/#',
        $local_uploads_url,
        $html
    );
    $html = preg_replace(
        '#https?://(?:www\\.)?saigonhoreca\\.vn/#',
        $home_url,
        $html
    );
    return $html;
}

/**
 * Resolve a category slug to a term_id, creating the term if it doesn't exist.
 * Falls back to a sanitized slug for term creation.
 */
function shr_get_or_create_term($slug, $taxonomy, $term_seeds_by_slug) {
    if (!$slug) return null;
    $term = get_term_by('slug', $slug, $taxonomy);
    if ($term && !is_wp_error($term)) {
        return (int) $term->term_id;
    }
    // Look up display name from term seeds if available
    $name = $term_seeds_by_slug[$slug]['name'] ?? str_replace('-', ' ', ucfirst($slug));
    $description = $term_seeds_by_slug[$slug]['description'] ?? '';
    $r = wp_insert_term($name, $taxonomy, [
        'slug'        => $slug,
        'description' => $description,
    ]);
    if (is_wp_error($r)) {
        // Maybe race with simultaneous insert — re-fetch.
        $term = get_term_by('slug', $slug, $taxonomy);
        return $term ? (int) $term->term_id : null;
    }
    return (int) $r['term_id'];
}

// Index term seeds by slug for fast lookup
$term_seeds_by_slug = [];
foreach ($terms as $t) {
    if (!isset($t['slug'])) continue;
    $term_seeds_by_slug[$t['slug']] = $t;
}

$post_map = [];
$stats = ['created' => 0, 'updated' => 0, 'failed' => 0];

/**
 * Import a single entity. Returns the resulting post_id (int) or null on fail.
 */
function shr_import_entity(
    $entity,
    $att_map,
    $local_uploads_url,
    $home_url,
    $term_seeds_by_slug,
    &$stats
) {
    $slug      = $entity['slug'] ?? '';
    $post_type = $entity['post_type'] ?? 'post';
    if (!$slug) {
        $stats['failed']++;
        return null;
    }

    $title   = wp_strip_all_tags($entity['title'] ?? $slug);
    $excerpt = $entity['excerpt'] ?? '';
    $content = shr_rewrite_content($entity['content_html'] ?? '', $local_uploads_url, $home_url);
    $excerpt = wp_strip_all_tags(shr_rewrite_content($excerpt, $local_uploads_url, $home_url));

    $date = $entity['post_date'] ?? '';
    if ($date) {
        try {
            $dt = new DateTime($date);
            $date = $dt->format('Y-m-d H:i:s');
        } catch (Exception $e) {
            $date = '';
        }
    }

    // Look up existing.
    $existing = get_posts([
        'name'        => $slug,
        'post_type'   => $post_type,
        'post_status' => 'any',
        'numberposts' => 1,
        'fields'      => 'ids',
        'suppress_filters' => true,
    ]);

    $postdata = [
        'post_title'   => $title,
        'post_name'    => $slug,
        'post_content' => $content,
        'post_excerpt' => $excerpt,
        'post_status'  => $entity['post_status'] ?? 'publish',
        'post_type'    => $post_type,
    ];
    if ($date) {
        $postdata['post_date']     = $date;
        $postdata['post_date_gmt'] = get_gmt_from_date($date);
    }

    if (!empty($existing)) {
        $postdata['ID'] = (int) $existing[0];
        $post_id = wp_update_post($postdata, true);
        $action  = 'updated';
    } else {
        $post_id = wp_insert_post($postdata, true);
        $action  = 'created';
    }

    if (is_wp_error($post_id) || !$post_id) {
        fwrite(STDERR, "  FAIL $post_type/$slug: "
            . (is_wp_error($post_id) ? $post_id->get_error_message() : '?') . "\n");
        $stats['failed']++;
        return null;
    }
    $stats[$action]++;

    // Featured image
    $fi_url = $entity['featured_image_src'] ?? '';
    if ($fi_url && isset($att_map[$fi_url])) {
        set_post_thumbnail($post_id, (int) $att_map[$fi_url]);
    }

    // Categories / tags
    if ($post_type === 'product') {
        $cat_ids = [];
        foreach (($entity['categories'] ?? []) as $cslug) {
            $tid = shr_get_or_create_term($cslug, 'product_category', $term_seeds_by_slug);
            if ($tid) $cat_ids[] = $tid;
        }
        if ($cat_ids) wp_set_object_terms($post_id, $cat_ids, 'product_category');

        $tag_ids = [];
        foreach (($entity['tags'] ?? []) as $tslug) {
            // product_tag taxonomy doesn't exist (only product_brand) — skip silently
        }

        // Brand string -> single term in product_brand
        $brand = $entity['brand'] ?? '';
        if ($brand) {
            $bslug = sanitize_title($brand);
            $bid = shr_get_or_create_term($bslug, 'product_brand', [
                $bslug => ['name' => $brand, 'description' => ''],
            ]);
            if ($bid) wp_set_object_terms($post_id, [$bid], 'product_brand');
        }

        // SKU as post meta
        if (!empty($entity['sku'])) {
            update_post_meta($post_id, '_sku', $entity['sku']);
        }
    } elseif ($post_type === 'project') {
        // No category mapping yet; tags reserved for future
    } elseif ($post_type === 'post') {
        $cat_ids = [];
        foreach (($entity['categories'] ?? []) as $cslug) {
            $tid = shr_get_or_create_term($cslug, 'category', $term_seeds_by_slug);
            if ($tid) $cat_ids[] = $tid;
        }
        if ($cat_ids) wp_set_object_terms($post_id, $cat_ids, 'category');

        $tag_ids = [];
        foreach (($entity['tags'] ?? []) as $tslug) {
            $tid = shr_get_or_create_term($tslug, 'post_tag', []);
            if ($tid) $tag_ids[] = $tid;
        }
        if ($tag_ids) wp_set_object_terms($post_id, $tag_ids, 'post_tag');
    }

    return (int) $post_id;
}

$type_to_bucket = [
    'product' => $products,
    'project' => $projects,
    'post'    => $posts,
    'page'    => $pages,
];

foreach ($type_to_bucket as $type => $bucket) {
    if ($only_type && $only_type !== $type) continue;
    echo "\n[import-posts] === $type (" . count($bucket) . ") ===\n";
    foreach ($bucket as $i => $entity) {
        $pid = shr_import_entity($entity, $att_map, $local_uploads_url, $home_url,
                                  $term_seeds_by_slug, $stats);
        if ($pid) {
            $post_map[$type . '/' . $entity['slug']] = $pid;
        }
        if (($i + 1) % 25 === 0 || $i + 1 === count($bucket)) {
            printf("  [%4d/%4d] $type: created=%4d updated=%4d failed=%4d\n",
                $i + 1, count($bucket),
                $stats['created'], $stats['updated'], $stats['failed']);
        }
    }
}

$map_path = $out_dir . '/post-map.json';
file_put_contents($map_path, json_encode($post_map, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

echo "\n[import-posts] DONE — created={$stats['created']} updated={$stats['updated']} failed={$stats['failed']}\n";
echo "[import-posts] map written -> $map_path\n";
