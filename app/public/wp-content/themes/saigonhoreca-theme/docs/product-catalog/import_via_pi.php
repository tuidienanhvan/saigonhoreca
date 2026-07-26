<?php
/**
 * import_via_pi.php — Nạp product từ data.json qua ĐÚNG engine import của pi-api
 * (Pi_Api_ImportExport::import_post_list), thay vì tự viết importer rời rạc — theo
 * yêu cầu "nó phải như plugin của Pi".
 *
 * Chạy: wp eval-file docs/product-catalog/import_via_pi.php [brand-slug ...] [--publish]
 *   - Không truyền brand: xử lý MỌI thư mục có data.json.
 *   - Mặc định post_status = draft (duyệt trước). --publish để đăng luôn.
 *
 * Idempotent: import_post_list(conflict_mode='update') khớp bài cũ theo SLUG và
 * cập nhật tại chỗ — crawl lại lần sau (khách đổi) chạy lại script này KHÔNG nhân
 * đôi, chỉ cập nhật. Slug sinh xác định từ brand + model nên bền qua các lần recrawl.
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) { fwrite(STDERR, "Chạy qua: wp eval-file\n"); exit(1); }

// Nạp engine của pi-api trực tiếp từ file (không phụ thuộc plugin active) — class chỉ
// dùng hàm WP core + method static của chính nó, không cần bootstrap pi-api.
$pi_engine = WP_PLUGIN_DIR . '/pi-api/includes/api/endpoints/system/class-import-export.php';
if (!class_exists('Pi_Api_ImportExport')) {
    if (!file_exists($pi_engine)) {
        fwrite(STDERR, "Không tìm thấy engine pi-api: $pi_engine\n");
        exit(1);
    }
    require_once $pi_engine;
}

$catalog_dir = __DIR__;

// ── Parse args ──
// Nhận token trần (không dùng --flag vì wp-cli nuốt các tham số dạng --xxx).
// Vd: wp eval-file import_via_pi.php josper halton dry-run
//     wp eval-file import_via_pi.php publish        (đăng tất cả)
// WP-CLI eval-file đưa tham số vị trí qua biến $args (KHÔNG phải $argv) — hỗ trợ cả
// 2 để chạy được qua eval-file lẫn PHP CLI trực tiếp.
if (isset($args) && is_array($args)) {
    // $args do wp-cli cung cấp — dùng nguyên.
} elseif (isset($argv) && is_array($argv)) {
    $args = array_slice($argv, 1);
} else {
    $args = [];
}
$publish = false;
$dry_run = false;
$brand_filter = [];
foreach ($args as $a) {
    $t = ltrim($a, '-');
    if ($t === 'publish') { $publish = true; continue; }
    if ($t === 'dry-run' || $t === 'dryrun' || $t === 'dry') { $dry_run = true; continue; }
    $brand_filter[] = $a;
}
$status = $publish ? 'publish' : 'draft';

// ── Chọn brand ──
$brands = [];
if ($brand_filter) {
    $brands = $brand_filter;
} else {
    foreach (glob($catalog_dir . '/*', GLOB_ONLYDIR) as $dir) {
        if (file_exists($dir . '/data.json')) $brands[] = basename($dir);
    }
}

/** Lấy phần base của tên brand (trước dấu gạch xuất xứ) để làm tiền tố slug ổn định. */
function pi_import_brand_slug_prefix($brand_full) {
    $base = preg_split('/[\-\x{2013}\x{2014}]/u', $brand_full)[0];
    return sanitize_title(trim($base));
}

/** Gom mọi item sản phẩm trong 1 group (hỗ trợ models / series[].models / variants / sample_models). */
function pi_import_group_items($group) {
    $items = [];
    foreach (['models', 'variants', 'sample_models'] as $key) {
        if (!empty($group[$key]) && is_array($group[$key])) {
            foreach ($group[$key] as $it) $items[] = $it;
        }
    }
    if (!empty($group['series']) && is_array($group['series'])) {
        foreach ($group['series'] as $series) {
            $sname = $series['series_name'] ?? ($series['name'] ?? '');
            foreach (['models', 'variants', 'sample_models'] as $key) {
                if (!empty($series[$key]) && is_array($series[$key])) {
                    foreach ($series[$key] as $it) {
                        if ($sname && empty($it['series'])) $it['series'] = $sname;
                        $items[] = $it;
                    }
                }
            }
        }
    }
    return $items;
}

/** True nếu đã có product (theo slug) VÀ nó đã có featured image — để bỏ qua tải lại ảnh. */
function pi_import_existing_has_thumb($slug) {
    $existing = get_page_by_path($slug, OBJECT, 'product');
    return $existing && has_post_thumbnail($existing->ID);
}

/**
 * Dựng nội dung HTML từ mô tả + specs — khớp độ đầy đủ với sản phẩm cũ trên site
 * (vd "Máy hút chân không SU-420 – Sammic", ID 1588: legend + "Specifications Sheet"
 * + danh sách bullet riêng cho "Default equipment"/"Optional equipment", KHÔNG phải
 * 1 <ul> phẳng dồn hết key:value). Bản đầu của hàm này (1 <ul> phẳng) đã lặp lại đúng
 * lỗi mà `reimport-products.php` từng phải vá cho 381 sản phẩm cũ: "import gốc chỉ lấy
 * excerpt ngắn, thiếu body Specifications Sheet/Default equipment/Optional equipment" —
 * sửa tận gốc ở đây thay vì phải viết tool backfill riêng lần 2.
 *
 * Heuristic tách "spec dài kiểu danh sách" (Key Features, Options, Default/Optional
 * equipment...) khỏi "spec ngắn kiểu 1 giá trị" (Width, Temperature...): value chứa
 * "; " VÀ dài hơn 60 ký tự → coi là danh sách, tách theo "; " thành <ul><li> riêng
 * dưới <h4> mang chính tên field đó — không cần biết trước tên field theo từng hãng.
 */
function pi_import_build_content($item, $title = '') {
    if ($title === '') $title = (string) ($item['title'] ?? $item['model'] ?? '');
    $desc  = $item['description_en'] ?? ($item['description_vi'] ?? ($item['description'] ?? ''));

    $html = '<div class="ast-accordion-wrap">';
    if ($title) $html .= '<h2>' . esc_html('Mô tả thông số kỹ thuật của ' . $title) . '</h2>';
    if ($desc)  $html .= '<p class="legend">' . esc_html($desc) . '</p>';

    $simple_lines = [];
    $list_sections = [];
    if (!empty($item['specs']) && is_array($item['specs'])) {
        foreach ($item['specs'] as $k => $v) {
            if ($v === '' || $v === null) continue;
            // KHÔNG đăng giá niêm yết của hãng (82 ô `price_eur` trong dữ liệu Josper,
            // vd dao €187.55). Đây là giá bán lẻ châu Âu: sai tiền tệ cho thị trường
            // Việt Nam, lộ giá gốc, và phá cấu trúc giá riêng của Saigon Horeca. Dữ
            // liệu vẫn giữ trong data-full.json để tham chiếu nội bộ, chỉ chặn ở khâu
            // render ra trang công khai.
            if (preg_match('/price|eur|gbp|usd|giá/i', (string) $k)) continue;
            $label = ucwords(str_replace('_', ' ', (string) $k));
            $val = is_array($v) ? implode('; ', array_map('strval', $v)) : (string) $v;
            // "; " (Precision) hoặc " | " (Kolb/Halton) — 2 kiểu phân tách list đã gặp
            // trong dữ liệu 5 hãng crawl-full; ưu tiên "; " nếu có cả hai.
            $sep = strpos($val, '; ') !== false ? '; ' : (strpos($val, ' | ') !== false ? ' | ' : '');
            if ($sep !== '' && mb_strlen($val) > 60) {
                $list_sections[$label] = array_values(array_filter(array_map('trim', explode($sep, $val))));
            } else {
                $simple_lines[] = '<strong>' . esc_html($label) . ':</strong> ' . esc_html($val);
            }
        }
    }

    if ($simple_lines) {
        $html .= '<h4>Specifications Sheet</h4><p>' . implode('<br> ', $simple_lines) . '</p>';
    }
    foreach ($list_sections as $label => $items) {
        $html .= '<h4>' . esc_html($label) . '</h4><ul>';
        foreach ($items as $li) $html .= '<li>' . esc_html($li) . '</li>';
        $html .= '</ul>';
    }

    $html .= '</div>';
    return $html;
}

$grand_total = 0; $grand_created = 0; $grand_updated = 0; $grand_errors = 0;

foreach ($brands as $brand_slug) {
    $data_path = $catalog_dir . '/' . $brand_slug . '/data.json';
    if (!file_exists($data_path)) {
        echo "⏭  $brand_slug: không có data.json — bỏ qua\n";
        continue;
    }
    $data = json_decode(file_get_contents($data_path), true);
    if (!is_array($data) || empty($data['groups'])) {
        echo "⏭  $brand_slug: data.json rỗng/không có groups — bỏ qua\n";
        continue;
    }

    $brand_full = $data['brand'] ?? $brand_slug;
    $brand_prefix = pi_import_brand_slug_prefix($brand_full);
    $research_date = $data['research_date'] ?? gmdate('Y-m-d');

    $posts = [];
    foreach ($data['groups'] as $group) {
        $group_en = $group['group_name'] ?? '';
        $group_vi = $group['group_name_vi'] ?? ($group_en ?: 'Sản phẩm');
        $group_src = !empty($group['source_urls'][0]) ? $group['source_urls'][0] : '';

        foreach (pi_import_group_items($group) as $item) {
            $model = $item['model'] ?? ($item['variant_name'] ?? ($item['model_code'] ?? ''));
            $title = $item['title'] ?? '';
            if ($title === '') {
                $title = trim($group_en . ' ' . $model);
                if ($title === '') $title = $group_vi;
            }

            // Slug ổn định (bền qua recrawl): brand + model, fallback brand + title.
            $slug_seed = $model !== '' ? ($brand_prefix . '-' . $model) : ($brand_prefix . '-' . $title);
            $slug = sanitize_title($slug_seed);

            $img = $item['image_url'] ?? ($item['og_image'] ?? ($item['image'] ?? ''));
            $src = $item['source_url'] ?? $group_src;

            $meta = [
                '_attr_brand'       => $brand_full,
                '_attr_series'      => $item['series'] ?? $group_en,
                '_attr_model'       => $model,
                '_attr_origin'      => $item['origin'] ?? '',
                '_attr_heat_source' => $item['heat_source'] ?? '',
                '_source_url'       => $src,
                '_research_date'    => $research_date,
                '_seo_title'        => $item['seo_title'] ?? '',
                '_seo_description'  => $item['seo_description'] ?? '',
                '_og_image'         => $img,
            ];
            // Bỏ meta rỗng cho gọn.
            $meta = array_filter($meta, function ($v) { return $v !== '' && $v !== null; });

            $post = [
                'title'        => $title,
                'slug'         => $slug,
                'type'         => 'product',
                'status'       => $status,
                'content_html' => pi_import_build_content($item, $title),
                'terms'        => [
                    ['taxonomy' => 'product_brand',    'name' => $brand_full, 'slug' => sanitize_title($brand_full)],
                    ['taxonomy' => 'product_category', 'name' => $group_vi,   'slug' => sanitize_title($group_vi)],
                ],
                'meta'         => $meta,
            ];
            // Chỉ đính featured_image_url khi sản phẩm CHƯA có ảnh — engine pi-api luôn
            // tải lại ảnh nếu field này có mặt (kể cả khi update), gây tải trùng + tạo
            // attachment trùng ở mỗi lần recrawl. Bỏ field này khi đã có ảnh → chạy lại
            // nhanh (không tải lại) và không nhân đôi media.
            if ($img && !pi_import_existing_has_thumb($slug)) {
                $post['featured_image_url'] = $img;
            }

            $posts[] = $post;
        }
    }

    if (!$posts) {
        echo "⚠  $brand_slug: 0 sản phẩm trích được từ data.json\n";
        continue;
    }

    if ($dry_run) {
        $sample = $posts[0];
        echo sprintf("🔍 %-24s %d sản phẩm (dry-run, KHÔNG ghi DB)\n", $brand_slug, count($posts));
        echo "    mẫu[0]: title=\"{$sample['title']}\" slug={$sample['slug']} cat=\""
           . $sample['terms'][1]['name'] . "\" img=" . (empty($sample['featured_image_url']) ? 'KHÔNG' : 'có') . "\n";
        $grand_total += count($posts);
        continue;
    }

    // ĐÚNG engine pi-api — conflict_mode='update' để idempotent theo slug.
    $stats = Pi_Api_ImportExport::import_post_list($posts, 'update');

    $c = (int) ($stats['imported'] ?? 0);
    $u = (int) ($stats['updated'] ?? 0);
    $e = (int) ($stats['errors'] ?? 0);
    $grand_total += count($posts); $grand_created += $c; $grand_updated += $u; $grand_errors += $e;
    echo sprintf("✅ %-24s %d sản phẩm → tạo %d, cập nhật %d, lỗi %d [%s]\n",
        $brand_slug, count($posts), $c, $u, $e, $status);
}

echo str_repeat('─', 60) . "\n";
if ($dry_run) {
    echo sprintf("DRY-RUN: %d sản phẩm sẽ được nạp (chưa ghi gì).\n", $grand_total);
    return;
}
echo sprintf("TỔNG: %d sản phẩm · tạo %d · cập nhật %d · lỗi %d\n",
    $grand_total, $grand_created, $grand_updated, $grand_errors);

// Reindex chatbot để sản phẩm mới xuất hiện trong tư vấn.
if (class_exists('Pi_Chatbot_Indexer')) {
    $n = Pi_Chatbot_Indexer::reindex_all();
    echo "🔄 Chatbot reindex: $n bản ghi\n";
}
