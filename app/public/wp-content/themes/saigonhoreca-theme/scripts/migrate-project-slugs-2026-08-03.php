<?php
/**
 * Migration slug dự án — 2026-08-03
 *
 * CHẠY MỘT LẦN trên production, NGAY SAU khi deploy theme.
 *
 * ⚠️ VÌ SAO BẮT BUỘC: slug nằm trong DB (wp_posts.post_name), không nằm trong
 * theme. Deploy file mà không chạy cái này thì production giữ slug cũ, trong khi
 * theme mới chỉ còn file single-project/<slug-mới>.php. Filter `single_template`
 * (inc/engine/pillar-routes.php) không tìm được template riêng -> rơi về
 * single.php -> MẤT TOÀN BỘ THIẾT KẾ PILLAR của 10 dự án.
 *
 * CHẠY:
 *   wp eval-file wp-content/themes/saigonhoreca-theme/scripts/migrate-project-slugs-2026-08-03.php
 * hoặc
 *   php -r "define('WP_USE_THEMES',false); require 'wp-load.php';
 *           require 'wp-content/themes/saigonhoreca-theme/scripts/migrate-project-slugs-2026-08-03.php';"
 *
 * XEM TRƯỚC (không ghi gì): thêm biến môi trường SGH_DRY=1
 *   SGH_DRY=1 wp eval-file .../migrate-project-slugs-2026-08-03.php
 *
 * KHÔNG GIỮ FALLBACK — quyết định của chủ site (2026-08-03): slug cũ trả 404
 * thật, chấp nhận mất thứ hạng. Script vì thế XOÁ LUÔN `_wp_old_slug` mà
 * WordPress tự sinh, nếu không WP sẽ âm thầm 301 slug cũ -> slug mới.
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) {
    exit('Chạy qua wp-cli hoặc sau khi require wp-load.php.');
}

$sgh_slug_migration = [
    // Rút gọn slug dài (36–50 ký tự) -> tên thương hiệu
    'mua-craft-sake-lam-ruou-sake-dau-tien-tai-viet-nam' => 'mua-craft-sake',
    'hemma-desserts-mot-goc-nho-chau-au-giua-thao-dien'  => 'hemma-desserts',
    'ganh-hao-noi-hon-bien-trong-tung-net-kien-truc'     => 'ganh-hao',
    'du-an-kdl-rung-thong-nui-voi-cua-saigonhoreca'      => 'kdl-rung-thong-nui-voi',
    'mam-mam-eatery-lounge-nang-tam-mam-com-viet'        => 'mam-mam-eatery',
    'bep-an-truong-mam-non-tu-thuc-trinh-vuong'          => 'truong-mam-non-trinh-vuong',
    'du-an-bep-cang-tin-cong-ty-nhat-nichiyo'            => 'nichiyo-canteen',
    'roka-fella-tinh-hoa-am-thuc-nhat-ban'               => 'roka-fella',

    // 2 cơ sở SOL — đặt tên theo QUẬN cho phân biệt được
    'sol-kitchen-bar-saigon-horeca'                      => 'sol-kitchen-bar-d1',
    'renovate-sol-kitchen-bar-quan-7'                    => 'renovate-sol-kitchen-bar-d7',
];

$sgh_dry = (getenv('SGH_DRY') === '1');
echo $sgh_dry ? "== CHẾ ĐỘ XEM TRƯỚC (không ghi gì) ==\n" : "== CHẠY THẬT ==\n";

$done = 0; $skip = 0; $miss = 0;

foreach ($sgh_slug_migration as $old => $new) {
    $posts = get_posts([
        'name'        => $old,
        'post_type'   => 'project',
        'post_status' => 'any',
        'numberposts' => 1,
    ]);

    if (!$posts) {
        // Đã đổi rồi thì không coi là lỗi — script chạy lại được.
        $already = get_posts([
            'name' => $new, 'post_type' => 'project',
            'post_status' => 'any', 'numberposts' => 1,
        ]);
        if ($already) { printf("  BỎ QUA (đã là %s)\n", $new); $skip++; }
        else          { printf("  KHÔNG THẤY: %s\n", $old); $miss++; }
        continue;
    }

    // Slug đích đã có post KHÁC chiếm chỗ -> dừng, không ghi đè.
    $taken = get_posts([
        'name' => $new, 'post_type' => 'project',
        'post_status' => 'any', 'numberposts' => 1,
        'exclude' => [$posts[0]->ID],
    ]);
    if ($taken) {
        printf("  ✗ TRÙNG, KHÔNG ĐỔI: %s đã thuộc post #%d\n", $new, $taken[0]->ID);
        $miss++;
        continue;
    }

    if ($sgh_dry) {
        printf("  sẽ đổi #%d: %s -> %s\n", $posts[0]->ID, $old, $new);
        $done++;
        continue;
    }

    wp_update_post(['ID' => $posts[0]->ID, 'post_name' => $new]);
    $after = get_post($posts[0]->ID)->post_name;
    printf("  %s #%d: %s -> %s\n", ($after === $new ? '✓' : '✗ LỆCH'), $posts[0]->ID, $old, $after);
    if ($after === $new) $done++; else $miss++;
}

if (!$sgh_dry) {
    // Không giữ fallback: gỡ _wp_old_slug do wp_update_post() vừa sinh ra.
    global $wpdb;
    $rows = $wpdb->get_results(
        "SELECT pm.meta_id, pm.meta_value FROM {$wpdb->postmeta} pm
         JOIN {$wpdb->posts} p ON p.ID = pm.post_id
         WHERE pm.meta_key = '_wp_old_slug' AND p.post_type = 'project'"
    );
    foreach ($rows as $r) {
        $wpdb->delete($wpdb->postmeta, ['meta_id' => $r->meta_id]);
        printf("  gỡ _wp_old_slug: %s\n", $r->meta_value);
    }

    flush_rewrite_rules(true);
    if (function_exists('sgh_static_cache_flush')) {
        sgh_static_cache_flush();
        echo "  đã xoá cache tĩnh\n";
    }
}

printf("\nKết quả: %d đổi, %d bỏ qua, %d lỗi/không thấy\n", $done, $skip, $miss);

echo "\nKIỂM SAU KHI CHẠY (thay <domain> cho đúng):\n";
foreach ($sgh_slug_migration as $old => $new) {
    printf("  https://<domain>/du-an/%s/   -> kỳ vọng 200\n", $new);
}
echo "  Và 10 slug cũ -> kỳ vọng 404 (đúng chủ ý, không phải lỗi).\n";
echo "  Trang phải render <main class=\"pp ...\">, KHÔNG phải sh-single__main.\n";
echo "  Nếu thấy sh-single__main tức template riêng không được tìm thấy ->\n";
echo "  kiểm theme đã deploy đủ single-project/ và template-parts/project-pillar/ chưa.\n";
