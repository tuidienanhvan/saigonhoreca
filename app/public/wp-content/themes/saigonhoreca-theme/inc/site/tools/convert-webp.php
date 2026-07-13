<?php
/**
 * Convert toàn bộ ảnh PNG / JPG / JPEG trong uploads → WebP sibling (cùng tên, .webp).
 *
 * Chạy trên production (admin hoặc &token) để tạo .webp cho cả thư viện media.
 * Theme có sẵn webp-conversion runtime sẽ tự serve .webp khi sibling tồn tại
 * (Accept-aware) → ảnh nhẹ hơn, Lighthouse cao hơn.
 *
 * An toàn: KHÔNG xoá / KHÔNG đổi ảnh gốc — chỉ TẠO thêm file .webp. Bỏ qua file
 * đã có .webp. Chạy theo lô (limit) để tránh timeout; chạy lại đến khi remaining=0.
 *
 * USAGE (admin, hoặc &token=<SGH_REIMPORT_TOKEN>):
 *   /?sgh_convert_webp=1                    → DRY: đếm số ảnh cần convert
 *   /?sgh_convert_webp=1&run=1              → convert 1 lô (mặc định 400 file)
 *   /?sgh_convert_webp=1&run=1&limit=800    → lô 800 file
 *   /?sgh_convert_webp=1&run=1&quality=82   → chất lượng webp (mặc định 82)
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) exit;

add_action('init', function () {
    if (!isset($_GET['sgh_convert_webp'])) return;

    $token_ok = isset($_GET['token']) && defined('SGH_REIMPORT_TOKEN')
        && hash_equals(SGH_REIMPORT_TOKEN, (string) $_GET['token']);
    if (!$token_ok && !current_user_can('manage_options')) {
        wp_die('Admin only.', '', ['response' => 403]);
    }

    @set_time_limit(0);
    @ini_set('memory_limit', '768M');
    header('Content-Type: text/plain; charset=UTF-8');
    while (ob_get_level()) ob_end_flush();

    if (!function_exists('imagewebp')) {
        echo "LỖI: GD imagewebp KHÔNG khả dụng trên server này — không convert được webp.\n";
        exit;
    }

    $is_run  = !empty($_GET['run']);
    $limit   = isset($_GET['limit'])   ? max(1, (int) $_GET['limit'])   : 400;
    $quality = isset($_GET['quality']) ? max(1, min(100, (int) $_GET['quality'])) : 82;

    $base = wp_get_upload_dir()['basedir'];

    echo "=== Convert PNG/JPG/JPEG → WebP — " . ($is_run ? "RUN (lô $limit, q=$quality)" : 'DRY RUN') . " ===\n";
    echo "Thư mục: $base\n\n";

    $skip_dirs = ['/_orphan-backup', '/post/', '/cache'];
    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($base, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    $need = 0; $done = 0; $fail = 0; $scanned = 0;
    foreach ($it as $file) {
        $path = wp_normalize_path($file->getPathname());
        if (!preg_match('/\.(jpe?g|png)$/i', $path)) continue;
        foreach ($skip_dirs as $sd) { if (strpos($path, $sd) !== false) continue 2; }
        $scanned++;

        $webp = preg_replace('/\.(jpe?g|png)$/i', '.webp', $path);
        if (file_exists($webp)) continue; // đã có

        $need++;
        if (!$is_run) {
            if ($need <= 8) echo "  cần convert: " . str_replace($base . '/', '', $path) . "\n";
            continue;
        }
        if ($done >= $limit) continue; // hết lô — đếm tiếp $need cho remaining

        $ok = sgh_convert_one_webp($path, $webp, $quality);
        if ($ok) { $done++; } else { $fail++; }
        if ($done % 50 === 0 && $done > 0) echo "  ... đã convert $done\n";
    }

    if (!$is_run) {
        echo "\nTổng ảnh png/jpg/jpeg quét: $scanned | CẦN convert (chưa có webp): $need\n";
        echo "→ chạy lại với &run=1 để convert.\n";
    } else {
        $remaining = max(0, $need - $done);
        echo "\n--- Convert lô này: thành công $done, lỗi $fail ---\n";
        echo "CÒN LẠI cần convert: $remaining\n";
        echo $remaining > 0 ? "→ chạy LẠI cùng URL để tiếp lô sau.\n" : "✓ XONG — toàn bộ đã có webp.\n";
    }
    echo "\n=== HẾT ===\n";
    exit;
});

/** Convert 1 file → webp, giữ alpha PNG. Trả true nếu OK. */
function sgh_convert_one_webp($src, $dst, $quality = 82) {
    $ext = strtolower(pathinfo($src, PATHINFO_EXTENSION));
    $im = false;
    if ($ext === 'png') {
        $im = @imagecreatefrompng($src);
        if ($im) { imagepalettetotruecolor($im); imagealphablending($im, true); imagesavealpha($im, true); }
    } elseif ($ext === 'jpg' || $ext === 'jpeg') {
        $im = @imagecreatefromjpeg($src);
    }
    if (!$im) return false;
    $ok = @imagewebp($im, $dst, $quality);
    imagedestroy($im);
    if ($ok && (!file_exists($dst) || filesize($dst) === 0)) { @unlink($dst); return false; }
    return (bool) $ok;
}
