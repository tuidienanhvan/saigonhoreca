<?php
/**
 * Dashboard Sync: Deploy/Undeploy (.htaccess, OG image, robots.txt)
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) exit;

/**
 * Deploy .htaccess production rules to WordPress root.
 */
function sgh_dashboard_deploy_htaccess() {
    $log = ['━━━ DEPLOY .HTACCESS ━━━'];
    $theme_dir = get_template_directory();
    $wp_root = ABSPATH;

    $src = $theme_dir . '/.htaccess.production';
    $dst = $wp_root . '.htaccess';

    if (!file_exists($src)) {
        $log[] = 'File .htaccess.production không tồn tại trong theme.';
        return ['success' => false, 'message' => 'File .htaccess.production không tồn tại.', 'log' => $log];
    }

    // Backup current .htaccess
    if (file_exists($dst)) {
        $backup = $dst . '.backup-' . wp_date('Ymd-His');
        if (is_writable(dirname($backup)) && copy($dst, $backup)) {
            $log[] = "Backup: {$backup}";
        }
    }

    // Always overwrite with production version
    $new_content = file_get_contents($src);
    if (empty($new_content)) {
        $log[] = 'File .htaccess.production trống.';
        return ['success' => false, 'message' => 'File .htaccess.production trống.', 'log' => $log];
    }

    if (is_writable(dirname($dst)) && file_put_contents($dst, $new_content, LOCK_EX) !== false) {
        $log[] = 'Đã deploy .htaccess.production → WordPress root';
        $log[] = 'Security headers, caching, compression, block sensitive files';
        return ['success' => true, 'message' => 'Deploy .htaccess thành công!', 'log' => $log];
    }

    $log[] = 'Không thể ghi file .htaccess — kiểm tra quyền file.';
    return ['success' => false, 'message' => 'Lỗi ghi file .htaccess.', 'log' => $log];
}

/**
 * Deploy OG default image to WordPress root.
 */
function sgh_dashboard_deploy_og_image() {
    $log = ['━━━ DEPLOY OG IMAGE ━━━'];
    $theme_dir = get_template_directory();
    $wp_root = ABSPATH;
    $copied = 0;

    $og_files = ['og-default.png'];
    foreach ($og_files as $file) {
        $src = $theme_dir . '/assets/images/' . $file;
        if (file_exists($src)) {
            $dst = $wp_root . $file;
            if (is_writable(dirname($dst)) && copy($src, $dst)) {
                $log[] = "Copied {$file} -> WordPress root";
                $copied++;
            } else {
                $log[] = "Không thể copy {$file} — kiểm tra quyền thư mục.";
            }
        }
    }

    if ($copied === 0) {
        $log[] = 'Không tìm thấy og-default.png trong theme/assets/images/';
        $log[] = 'Tạo file og-default.png (1200x630px) rồi thử lại.';
        return ['success' => false, 'message' => 'Không tìm thấy OG image trong theme.', 'log' => $log];
    }

    return ['success' => true, 'message' => "Đã deploy {$copied} OG image(s).", 'log' => $log];
}

/**
 * Remove SAIGON HOUSE rules from .htaccess.
 */
function sgh_dashboard_undeploy_htaccess() {
    $log = ['━━━ UNDEPLOY .HTACCESS ━━━'];
    $dst = ABSPATH . '.htaccess';

    if (!file_exists($dst)) {
        $log[] = 'File .htaccess không tồn tại.';
        return ['success' => false, 'message' => 'File .htaccess không tồn tại.', 'log' => $log];
    }

    $content = file_get_contents($dst);

    if (strpos($content, 'SAIGON HOUSE') === false) {
        $log[] = 'Không tìm thấy rules SAIGON HOUSE trong .htaccess.';
        return ['success' => true, 'message' => '.htaccess không có rules SAIGON HOUSE.', 'log' => $log];
    }

    $backup = $dst . '.backup-' . wp_date('Ymd-His');
    if (is_writable(dirname($backup)) && copy($dst, $backup)) {
        $log[] = "Backup: {$backup}";
    }

    $pos = strpos($content, '# BEGIN WordPress');
    if ($pos !== false) {
        $clean = substr($content, $pos);
        if (is_writable($dst) && file_put_contents($dst, $clean, LOCK_EX) !== false) {
            $log[] = 'Đã xoá rules SAIGON HOUSE khỏi .htaccess';
            $log[] = 'Giữ lại block # BEGIN WordPress ... # END WordPress';
            return ['success' => true, 'message' => 'Undeploy .htaccess thành công!', 'log' => $log];
        }
        $log[] = 'Không thể ghi file .htaccess.';
        return ['success' => false, 'message' => 'Lỗi ghi file.', 'log' => $log];
    }

    $log[] = 'Không tìm thấy block # BEGIN WordPress — không dám xoá.';
    return ['success' => false, 'message' => 'Cấu trúc .htaccess không nhận diện được.', 'log' => $log];
}

/**
 * Remove OG default images from WordPress root.
 */
function sgh_dashboard_undeploy_og_image() {
    $log = ['━━━ UNDEPLOY OG IMAGE ━━━'];
    $wp_root = ABSPATH;
    $deleted = 0;

    foreach (['og-default.png'] as $file) {
        $path = $wp_root . $file;
        if (file_exists($path)) {
            if (is_writable($path) && unlink($path)) {
                $log[] = "Đã xoá {$file}";
                $deleted++;
            } else {
                $log[] = "Không thể xoá {$file} — kiểm tra quyền.";
            }
        }
    }

    if ($deleted === 0) {
        $log[] = 'Không có OG image nào ở root để xoá.';
        return ['success' => true, 'message' => 'Không có OG image nào để xoá.', 'log' => $log];
    }

    return ['success' => true, 'message' => "Đã xoá {$deleted} OG image(s).", 'log' => $log];
}

/**
 * Deploy robots.txt to WordPress root.
 */
function sgh_dashboard_deploy_robots_txt() {
    $log = ['--- DEPLOY ROBOTS.TXT ---'];
    $dst = ABSPATH . 'robots.txt';
    $site_url = get_site_url();
    $host = wp_parse_url($site_url, PHP_URL_HOST);
    $sitemap_lines = ["Sitemap: {$site_url}/wp-sitemap.xml"];
    if (defined('RANK_MATH_VERSION')) {
        array_unshift($sitemap_lines, "Sitemap: {$site_url}/sitemap_index.xml");
    }

    $content = "# Robots.txt for {$host}\n"
        . "# Generated by Saigon Horeca Theme\n\n"
        . "User-agent: *\n"
        . "Allow: /\n"
        . "Disallow: /wp-admin/\n"
        . "Allow: /wp-admin/admin-ajax.php\n"
        . "Disallow: /wp-includes/\n"
        . "Disallow: /trackback/\n"
        . "Disallow: /comments/feed/\n"
        . "Disallow: */trackback/\n"
        . "Disallow: /*?feed=rss\n"
        . "Disallow: /cgi-bin/\n"
        . "Disallow: /tmp/\n"
        . "Disallow: /*?s=\n"
        . "Disallow: /*&s=\n\n"
        . "# Social Media Crawlers\n"
        . "User-agent: facebookexternalhit\nAllow: /\n\n"
        . "User-agent: Facebot\nAllow: /\n\n"
        . "User-agent: Twitterbot\nAllow: /\n\n"
        . "User-agent: LinkedInBot\nAllow: /\n\n"
        . "User-agent: TelegramBot\nAllow: /\n\n"
        . implode("\n", array_unique($sitemap_lines)) . "\n\n"
        . "# Host: {$host}\n";

    if (file_exists($dst)) {
        $backup = $dst . '.backup-' . wp_date('Ymd-His');
        if (is_writable(dirname($backup))) {
            copy($dst, $backup);
            $log[] = "Backup: {$backup}";
        }
    }

    if (is_writable(dirname($dst)) && file_put_contents($dst, $content, LOCK_EX) !== false) {
        $log[] = "Đã tạo robots.txt tại WordPress root.";
        $log[] = "Bao gồm: Social bot allow, sitemap URLs.";
        return ['success' => true, 'message' => 'Deploy robots.txt thành công!', 'log' => $log];
    }

    $log[] = 'Không thể ghi file robots.txt.';
    return ['success' => false, 'message' => 'Lỗi ghi robots.txt.', 'log' => $log];
}

/**
 * Remove robots.txt from WordPress root.
 */
function sgh_dashboard_undeploy_robots_txt() {
    $log = ['--- UNDEPLOY ROBOTS.TXT ---'];
    $path = ABSPATH . 'robots.txt';

    if (!file_exists($path)) {
        $log[] = 'Không có robots.txt ở root.';
        return ['success' => true, 'message' => 'Không có robots.txt để xoá.', 'log' => $log];
    }

    if (is_writable($path) && unlink($path)) {
        $log[] = 'Đã xoá robots.txt. WordPress sẽ dùng dynamic robots.txt filter.';
        return ['success' => true, 'message' => 'Đã xoá robots.txt.', 'log' => $log];
    }

    $log[] = 'Không thể xoá robots.txt.';
    return ['success' => false, 'message' => 'Lỗi xoá robots.txt.', 'log' => $log];
}
