<?php
/**
 * Seed nội dung "mồi" cho site MỚI (deploy lần đầu lên production):
 *   1. Import 30 bài dự án (CPT `project`) từ data đóng gói sẵn trong theme
 *      (inc/tools/data/projects-seed.json) — để /du-an/ + featured-projects
 *      (WP_Query trên CPT) có dữ liệu hiển thị. Dự án render theo slug + ảnh
 *      folder, nên chỉ cần title + slug + excerpt (không kèm nội dung cũ).
 *   2. Đổi template + tắt Elementor cho trang "Về Saigon Horeca" & "Liên hệ"
 *      → dùng template theme (page-gioi-thieu.php / page-lien-he.php), nội dung
 *      nằm trong theme thay vì bản Elementor cũ.
 *
 * An toàn: bỏ qua dự án đã tồn tại (chạy lại không tạo trùng). Mặc định DRY RUN.
 * KHÔNG xoá gì. KHÔNG đụng thư viện ảnh.
 *
 * USAGE (admin, hoặc &token=<SGH_REIMPORT_TOKEN>):
 *   /?sgh_seed=1            → DRY: xem sẽ tạo/sửa gì
 *   /?sgh_seed=1&run=1      → thực thi
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) exit;

add_action('init', function () {
    if (!isset($_GET['sgh_seed'])) return;

    $token_ok = isset($_GET['token']) && defined('SGH_REIMPORT_TOKEN')
        && hash_equals(SGH_REIMPORT_TOKEN, (string) $_GET['token']);
    if (!$token_ok && !current_user_can('manage_options')) {
        wp_die('Admin only.', '', ['response' => 403]);
    }

    @set_time_limit(0);
    header('Content-Type: text/plain; charset=UTF-8');
    while (ob_get_level()) ob_end_flush();

    $is_run = !empty($_GET['run']);
    echo "=== SGH Seed Content — " . ($is_run ? 'RUN' : 'DRY RUN') . " ===\n\n";

    // ── 1. Import projects ──
    $seed_file = get_template_directory() . '/inc/tools/data/projects-seed.json';
    echo "[1] DỰ ÁN (CPT project)\n";
    if (!file_exists($seed_file)) {
        echo "  LỖI: không thấy data/projects-seed.json\n";
    } else {
        $projects = json_decode(file_get_contents($seed_file), true);
        if (!is_array($projects)) {
            echo "  LỖI: data dự án không hợp lệ.\n";
        } else {
            $created = 0; $skip = 0;
            foreach ($projects as $p) {
                $slug = $p['slug'] ?? '';
                if ($slug === '') continue;
                if (get_page_by_path($slug, OBJECT, 'project')) {
                    $skip++;
                    continue;
                }
                if (!$is_run) {
                    echo "  WOULD CREATE: {$p['title']}  ({$slug})\n";
                    $created++;
                    continue;
                }
                $id = wp_insert_post([
                    'post_type'      => 'project',
                    'post_status'    => 'publish',
                    'post_title'     => $p['title'] ?? $slug,
                    'post_name'      => $slug,
                    'post_excerpt'   => $p['excerpt'] ?? '',
                    'post_date'      => $p['date'] ?? '',
                    'comment_status' => 'closed',
                    'ping_status'    => 'closed',
                ], true);
                if (is_wp_error($id)) {
                    echo "  FAIL {$slug}: " . $id->get_error_message() . "\n";
                } else {
                    $created++;
                    echo "  + {$slug} #$id\n";
                }
            }
            echo "  --> " . ($is_run ? "tạo $created" : "dự kiến tạo $created") . ", bỏ qua (đã có) $skip\n";
        }
    }

    // ── 2. Fix trang Về / Liên hệ → template theme ──
    echo "\n[2] TRANG Về / Liên hệ (đổi template theme, tắt Elementor)\n";
    $pages = [
        've-saigon-horeca' => 'page-templates/page-gioi-thieu.php',
        'lien-he'          => 'page-templates/page-lien-he.php',
    ];
    foreach ($pages as $slug => $tpl) {
        $pg = get_page_by_path($slug, OBJECT, 'page');
        if (!$pg) { echo "  (bỏ qua) không thấy trang: $slug\n"; continue; }
        if (!$is_run) {
            echo "  WOULD SET: $slug (#{$pg->ID}) -> $tpl\n";
            continue;
        }
        update_post_meta($pg->ID, '_wp_page_template', $tpl);
        update_post_meta($pg->ID, '_elementor_edit_mode', ''); // ngừng render Elementor (giữ data)
        echo "  $slug (#{$pg->ID}) -> $tpl\n";
    }

    // ── 3. Blog posts: bỏ template Elementor → dùng single.php (có sidebar) ──
    echo "\n[3] BLOG POSTS — bỏ template Elementor (elementor_header_footer/canvas) → single.php\n";
    global $wpdb;
    $ele_posts = $wpdb->get_col(
        "SELECT p.ID FROM {$wpdb->posts} p
         JOIN {$wpdb->postmeta} m ON m.post_id = p.ID
         WHERE p.post_type = 'post' AND p.post_status = 'publish'
           AND m.meta_key = '_wp_page_template' AND m.meta_value LIKE 'elementor%'"
    );
    echo "  Bài blog đang dùng template Elementor: " . count($ele_posts) . "\n";
    if (!$is_run) {
        echo "  WOULD CLEAR " . count($ele_posts) . " bài → 'default' (single.php có sidebar)\n";
    } else {
        $cleared = 0;
        foreach ($ele_posts as $pid) {
            update_post_meta($pid, '_wp_page_template', 'default');
            $cleared++;
        }
        echo "  --> đã đổi $cleared bài về 'default' (single.php)\n";
    }

    // ── 4. Cover responsive — attachment + sizes (BATCHED để tránh timeout) ──
    // Sinh attachment cho cover → wp_get_attachment_image_srcset (hero/grid).
    // CHỈ xử lý dự án CHƯA có featured image, tối đa 8/lần (re-run tới khi hết)
    // — wp_generate_attachment_metadata nặng, làm hết 1 lần dễ 500 (timeout).
    echo "\n[4] COVER RESPONSIVE — attachment + sizes (batch 8/lần)\n";
    @ini_set('memory_limit', '1024M');
    if (function_exists('sgh_project_cover_ensure')) {
        $todo = get_posts([
            'post_type' => 'project', 'post_status' => 'publish', 'numberposts' => -1,
            'fields' => 'ids',
            'meta_query' => [['key' => '_thumbnail_id', 'compare' => 'NOT EXISTS']],
        ]);
        echo "  Cover chưa có attachment: " . count($todo) . "\n";
        if (!$is_run) {
            echo "  WOULD ensure (8/lần) — re-run tới khi = 0\n";
        } else {
            $ens = 0;
            foreach (array_slice($todo, 0, 8) as $pid) {
                try { if (sgh_project_cover_ensure($pid)) $ens++; } catch (\Throwable $e) {}
            }
            $remain = max(0, count($todo) - $ens);
            echo "  --> ensured $ens lô này | CÒN LẠI: $remain" . ($remain ? " (chạy lại ?sgh_seed=1&run=1 để tiếp)\n" : " ✓ xong\n");
        }
    } else {
        echo "  (sgh_project_cover_ensure không khả dụng — bỏ qua)\n";
    }

    if ($is_run) {
        flush_rewrite_rules(false);
        echo "\n✓ XONG. (đã flush rewrite rules)\n";
    } else {
        echo "\n→ chạy lại với &run=1 để thực thi.\n";
    }
    exit;
});
