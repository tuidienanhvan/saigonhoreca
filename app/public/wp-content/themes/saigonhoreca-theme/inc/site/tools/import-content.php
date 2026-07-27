<?php
/**
 * Import blog posts + products (+ pages) từ file WXR đã export sẵn trong
 * wp-content/uploads/post/ — KHÔNG cần plugin "WordPress Importer".
 *
 * Triết lý "chỉ thay đổi đường dẫn ảnh": tool KHÔNG tải lại media. Nó chỉ
 * tạo lại post/product + rewrite mọi URL ảnh trong nội dung về DẠNG
 * SITE-RELATIVE (/wp-content/uploads/...), nên ảnh hiển thị đúng trên bất
 * kỳ site nào miễn là thư mục uploads đã được upload lên. Featured image &
 * product gallery được map theo TÊN FILE sang attachment đang có trên site
 * đích (nếu có).
 *
 * Nguồn file (tự lấy bản .xml mới nhất theo loại trong uploads/post/):
 *   blog-posts-export-*.xml   (post)
 *   products-export-*.xml     (product)
 *   pages-export-*.xml        (page)
 *
 * USAGE (admin, hoặc &token=<SGH_REIMPORT_TOKEN>):
 *   /?sgh_import_content=1                       → DRY RUN tất cả (post+product), in dự kiến
 *   /?sgh_import_content=1&type=product&run=1    → import product
 *   /?sgh_import_content=1&type=post&run=1        → import blog
 *   /?sgh_import_content=1&type=page&run=1        → import trang
 *   &limit=20                                     → chỉ N item đầu
 *   &host=https://saigonhoreca.com                → rewrite ảnh sang host này thay vì site-relative
 *
 * An toàn: bỏ qua item nếu đã tồn tại post cùng post_name + post_type
 * (chạy lại không tạo trùng). Mặc định DRY RUN.
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) exit;

add_action('init', function () {
    if (!isset($_GET['sgh_import_content'])) return;

    $token_ok = isset($_GET['token']) && defined('SGH_REIMPORT_TOKEN')
        && hash_equals(SGH_REIMPORT_TOKEN, (string) $_GET['token']);
    if (!$token_ok && !current_user_can('manage_options')) {
        wp_die('Admin only.', '', ['response' => 403]);
    }

    @set_time_limit(0);
    @ini_set('memory_limit', '1024M');
    header('Content-Type: text/plain; charset=UTF-8');
    while (ob_get_level()) ob_end_flush();

    $is_run = !empty($_GET['run']);
    $type   = isset($_GET['type']) ? sanitize_key($_GET['type']) : '';   // post|product|page ('' = post+product)
    $limit  = isset($_GET['limit']) ? max(0, (int) $_GET['limit']) : 0;
    $host   = isset($_GET['host']) ? esc_url_raw(trim($_GET['host'])) : ''; // optional explicit host for images

    $base_dir = trailingslashit(wp_get_upload_dir()['basedir']) . 'post';

    // Chọn loại + file tương ứng.
    $type_files = [
        'post'    => 'blog-posts-export-*.xml',
        'product' => 'products-export-*.xml',
        'page'    => 'pages-export-*.xml',
    ];
    $types = $type ? [$type] : ['post', 'product'];

    echo "=== SGH Import Content — " . ($is_run ? 'RUN' : 'DRY RUN') . " ===\n";
    echo "Thư mục nguồn: $base_dir\n";
    echo "Loại: " . implode(', ', $types) . ($limit ? " | limit=$limit" : '') . "\n";
    echo "Ảnh: " . ($host ? "rewrite → $host" : 'rewrite → site-relative (/wp-content/uploads/...)') . "\n\n";

    foreach ($types as $t) {
        if (empty($type_files[$t])) { echo "[$t] bỏ qua — loại không hỗ trợ.\n"; continue; }
        $matches = glob($base_dir . '/' . $type_files[$t]);
        if (!$matches) { echo "[$t] KHÔNG tìm thấy file WXR ({$type_files[$t]}).\n\n"; continue; }
        rsort($matches); // mới nhất trước
        $file = $matches[0];
        echo "[$t] File: " . basename($file) . "\n";
        sgh_import_wxr_file($file, $t, $is_run, $limit, $host);
        echo "\n";
    }

    echo "=== XONG ===\n";
    exit;
});

/**
 * Parse 1 file WXR + import các item theo post_type yêu cầu.
 */
function sgh_import_wxr_file($file, $want_type, $is_run, $limit, $host) {
    $prev = libxml_use_internal_errors(true);
    $xml  = simplexml_load_file($file);
    if (!$xml) { echo "  LỖI: không parse được XML.\n"; libxml_use_internal_errors($prev); return; }

    $WP = 'http://wordpress.org/export/1.2/';
    $CT = 'http://purl.org/rss/1.0/modules/content/';
    $EX = 'http://wordpress.org/export/1.2/excerpt/';

    // Pass 1: map attachment nguồn id -> tên file (nếu WXR có item attachment).
    $att_src = []; // src_id => basename
    foreach ($xml->channel->item as $it) {
        $w = $it->children($WP);
        if ((string) $w->post_type === 'attachment') {
            $url = (string) $w->attachment_url;
            if ($url) $att_src[(int) $w->post_id] = basename(parse_url($url, PHP_URL_PATH));
        }
    }
    if ($att_src) echo "  (WXR có " . count($att_src) . " attachment để map featured/gallery)\n";

    $created = 0; $skip = 0; $done = 0;
    foreach ($xml->channel->item as $it) {
        $w  = $it->children($WP);
        $pt = (string) $w->post_type;
        if ($pt !== $want_type) continue;
        if ($limit && $done >= $limit) break;
        $done++;

        $slug  = (string) $w->post_name;
        $title = (string) $it->title;

        // Dedup: đã có post cùng slug + type?
        $existing = $slug ? get_page_by_path($slug, OBJECT, $pt) : null;
        if ($existing) { $skip++; echo "  SKIP (đã có): [$pt] $title\n"; continue; }

        // Nội dung + rewrite URL ảnh.
        $content = (string) $it->children($CT)->encoded;
        $excerpt = (string) $it->children($EX)->encoded;
        $content = sgh_import_rewrite_img_urls($content, $host);

        if (!$is_run) {
            $imgs = preg_match_all('#<img\b#i', $content);
            echo "  WOULD CREATE: [$pt] $title  (slug=$slug, imgs=$imgs)\n";
            $created++;
            continue;
        }

        // Terms theo domain.
        $tax_map = []; // taxonomy => [slugs]
        foreach ($it->category as $cat) {
            $domain = (string) $cat['domain'];
            $nice   = (string) $cat['nicename'];
            $name   = (string) $cat;
            if (!$domain || !taxonomy_exists($domain)) continue;
            if (!term_exists($nice ?: $name, $domain)) {
                wp_insert_term($name, $domain, ['slug' => $nice]);
            }
            $tax_map[$domain][] = $nice ?: sanitize_title($name);
        }

        $postarr = [
            'post_title'   => $title,
            'post_name'    => $slug,
            'post_content' => wp_slash($content),
            'post_excerpt' => wp_slash($excerpt),
            'post_status'  => (string) $w->status ?: 'publish',
            'post_type'    => $pt,
            'post_date'    => (string) $w->post_date ?: '',
            'menu_order'   => (int) $w->menu_order,
            'comment_status' => (string) $w->comment_status ?: 'closed',
        ];
        $new_id = wp_insert_post($postarr, true);
        if (is_wp_error($new_id)) { echo "  FAIL: $title — " . $new_id->get_error_message() . "\n"; continue; }

        // Gán terms.
        foreach ($tax_map as $tax => $slugs) {
            wp_set_object_terms($new_id, array_values(array_unique($slugs)), $tax, false);
        }

        // Meta (bỏ qua nội bộ; remap featured + gallery theo tên file).
        $skip_meta = ['_edit_lock', '_edit_last', '_wp_old_slug', '_wp_old_date'];
        foreach ($w->postmeta as $m) {
            $key = (string) $m->meta_key;
            $val = (string) $m->meta_value;
            if (in_array($key, $skip_meta, true)) continue;

            if ($key === '_thumbnail_id') {
                $tid = sgh_import_map_attachment((int) $val, $att_src);
                if ($tid) update_post_meta($new_id, '_thumbnail_id', $tid);
                continue;
            }
            if ($key === '_product_image_gallery') {
                $ids = array_filter(array_map('intval', explode(',', $val)));
                $mapped = [];
                foreach ($ids as $oid) { $nid = sgh_import_map_attachment($oid, $att_src); if ($nid) $mapped[] = $nid; }
                if ($mapped) update_post_meta($new_id, '_product_image_gallery', implode(',', $mapped));
                continue;
            }
            // meta thường (gồm WooCommerce _price, _sku, _stock... giữ nguyên)
            update_post_meta($new_id, $key, maybe_unserialize($val));
        }

        $created++;
        echo "  + [$pt] $title  → #$new_id\n";
    }

    echo "  --- $want_type: " . ($is_run ? "tạo $created" : "dự kiến tạo $created") . ", bỏ qua $skip ---\n";
    libxml_use_internal_errors($prev);
}

/** Rewrite mọi URL ảnh uploads về site-relative (hoặc sang $host nếu có). */
function sgh_import_rewrite_img_urls($content, $host = '') {
    $repl = $host ? rtrim($host, '/') . '/wp-content/uploads/' : '/wp-content/uploads/';
    // bất kỳ host nào + /wp-content/uploads/  → repl
    $content = preg_replace('#https?://[^/"\']+/wp-content/uploads/#i', $repl, $content);
    return $content;
}

/** Map 1 source attachment id → target attachment id theo TÊN FILE. */
function sgh_import_map_attachment($src_id, $att_src) {
    if (!$src_id) return 0;
    $fname = isset($att_src[$src_id]) ? $att_src[$src_id] : '';
    if (!$fname) return 0;
    global $wpdb;
    $stem = preg_replace('/-\d+x\d+(\.[a-z0-9]+)$/i', '$1', $fname); // bỏ hậu tố -WxH nếu có
    $row = $wpdb->get_var($wpdb->prepare(
        "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key='_wp_attached_file' AND meta_value LIKE %s LIMIT 1",
        '%/' . $wpdb->esc_like($stem)
    ));
    return $row ? (int) $row : 0;
}
