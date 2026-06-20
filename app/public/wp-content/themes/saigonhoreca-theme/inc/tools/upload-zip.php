<?php
/**
 * Upload 1 file .zip → giải nén vào wp-content/uploads/ (MERGE — KHÔNG xoá gì).
 *
 * Dùng để đẩy nhanh các FOLDER ẢNH PROJECT + folder `saigonhoreca/` lên site đích
 * mà không phải SFTP từng file. Giữ nguyên cấu trúc thư mục bên trong zip.
 *
 * AN TOÀN:
 *   - Admin-only (hoặc &token=<SGH_REIMPORT_TOKEN>).
 *   - Chống Zip-Slip: từ chối entry có `..`, đường dẫn tuyệt đối, ổ đĩa, hay
 *     thoát ra ngoài uploads.
 *   - Mặc định CHỈ nhận file ảnh (jpg/jpeg/png/webp/gif/svg/avif/bmp/ico).
 *     Tích "cho phép mọi loại" nếu cần (vẫn chặn .php/.phtml/.htaccess…).
 *   - KHÔNG xoá file cũ — chỉ tạo mới / ghi đè (tuỳ chọn). Đây là MERGE đúng
 *     với yêu cầu deploy "đừng sync-delete".
 *   - Có "Chỉ liệt kê (dry run)" để xem nội dung trước khi giải nén.
 *
 * USAGE: đăng nhập admin → mở  /?sgh_upload_zip=1  → chọn .zip → Upload.
 *   (hoặc /?sgh_upload_zip=1&token=<SGH_REIMPORT_TOKEN>)
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) exit;

add_action('init', function () {
    if (!isset($_GET['sgh_upload_zip'])) return;

    $token_ok = isset($_GET['token']) && defined('SGH_REIMPORT_TOKEN')
        && hash_equals(SGH_REIMPORT_TOKEN, (string) $_GET['token']);
    if (!$token_ok && !current_user_can('manage_options')) {
        wp_die('Admin only.', '', ['response' => 403]);
    }

    @set_time_limit(0);
    @ini_set('memory_limit', '1024M');

    $base     = wp_normalize_path(wp_get_upload_dir()['basedir']);
    $is_post  = ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';

    // ---- POST: xử lý file zip ----
    if ($is_post) {
        // CSRF nonce (chỉ bắt buộc khi đi đường admin, không dùng token).
        if (!$token_ok && (empty($_POST['sgh_zip_nonce'])
            || !wp_verify_nonce($_POST['sgh_zip_nonce'], 'sgh_upload_zip'))) {
            wp_die('Nonce sai / hết hạn — tải lại trang.', '', ['response' => 403]);
        }

        header('Content-Type: text/plain; charset=UTF-8');
        while (ob_get_level()) ob_end_flush();

        if (!class_exists('ZipArchive')) {
            echo "LỖI: PHP ZipArchive không khả dụng trên server này.\n"; exit;
        }
        if (empty($_FILES['sgh_zip']) || ($_FILES['sgh_zip']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            $err = $_FILES['sgh_zip']['error'] ?? UPLOAD_ERR_NO_FILE;
            $msgs = [
                UPLOAD_ERR_INI_SIZE   => 'file lớn hơn upload_max_filesize (php.ini)',
                UPLOAD_ERR_FORM_SIZE  => 'file lớn hơn MAX_FILE_SIZE form',
                UPLOAD_ERR_PARTIAL    => 'upload dở dang',
                UPLOAD_ERR_NO_FILE    => 'chưa chọn file',
                UPLOAD_ERR_NO_TMP_DIR => 'thiếu thư mục tạm',
                UPLOAD_ERR_CANT_WRITE => 'không ghi được đĩa',
            ];
            echo "LỖI upload: " . ($msgs[$err] ?? "mã $err") . "\n";
            echo "Giới hạn hiện tại: upload_max_filesize=" . ini_get('upload_max_filesize')
                . " | post_max_size=" . ini_get('post_max_size') . "\n";
            exit;
        }

        $tmp       = $_FILES['sgh_zip']['tmp_name'];
        $orig_name = $_FILES['sgh_zip']['name'];
        $dry       = !empty($_POST['dry']);
        $overwrite = !empty($_POST['overwrite']);
        $allow_all = !empty($_POST['allow_all']);

        // Thư mục con đích (tuỳ chọn) — sanitize, chặn traversal.
        $subdir = isset($_POST['subdir']) ? trim((string) $_POST['subdir']) : '';
        $subdir = str_replace('\\', '/', $subdir);
        $subdir = preg_replace('#\.\.+#', '', $subdir);
        $subdir = trim(preg_replace('#/+#', '/', $subdir), '/');
        $dest_root = $base . ($subdir !== '' ? '/' . $subdir : '');

        echo "=== Giải nén ZIP → uploads — " . ($dry ? 'DRY RUN (chỉ liệt kê)' : 'GIẢI NÉN') . " ===\n";
        echo "File: $orig_name (" . size_format((int) $_FILES['sgh_zip']['size']) . ")\n";
        echo "Đích: " . str_replace($base, '[uploads]', $dest_root) . "\n";
        echo "Lọc: " . ($allow_all ? 'mọi loại (trừ file thực thi)' : 'CHỈ ảnh')
            . " | trùng tên: " . ($overwrite ? 'GHI ĐÈ' : 'BỎ QUA') . "\n\n";

        $zip = new ZipArchive();
        if ($zip->open($tmp) !== true) { echo "LỖI: không mở được zip (hỏng?).\n"; exit; }

        $allow_ext = ['jpg','jpeg','png','webp','gif','svg','avif','bmp','ico'];
        $deny_ext  = ['php','phtml','php3','php4','php5','php7','phps','pht','phar',
                      'cgi','pl','py','sh','exe','htaccess','htm','html','js'];

        $created = 0; $overwritten = 0; $skip_exist = 0; $skip_filtered = 0; $skip_unsafe = 0; $dirs = 0;
        $base_len = strlen($base);

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if ($name === false || $name === '') continue;

            $rel = str_replace('\\', '/', $name);
            if (substr($rel, -1) === '/') { $dirs++; continue; } // thư mục

            // Rác hệ thống.
            $bn = basename($rel);
            if (strpos($rel, '__MACOSX/') === 0 || $bn === '.DS_Store' || $bn === 'Thumbs.db') continue;

            // Chống Zip-Slip.
            if ($rel[0] === '/' || strpos($rel, '..') !== false || preg_match('#^[a-zA-Z]:#', $rel)) {
                echo "  ⚠ BỎ (không an toàn): $rel\n"; $skip_unsafe++; continue;
            }

            // Lọc đuôi.
            $ext = strtolower(pathinfo($rel, PATHINFO_EXTENSION));
            if ($ext === '' || in_array($ext, $deny_ext, true)) { $skip_filtered++; continue; }
            if (!$allow_all && !in_array($ext, $allow_ext, true)) { $skip_filtered++; continue; }

            $target = wp_normalize_path($dest_root . '/' . $rel);
            // Đảm bảo nằm TRONG uploads.
            if (strncmp($target, $base, $base_len) !== 0) { echo "  ⚠ BỎ (ngoài uploads): $rel\n"; $skip_unsafe++; continue; }

            $exists = file_exists($target);

            if ($dry) {
                if ($created + $overwritten + $skip_exist < 60) {
                    echo "  " . ($exists ? ($overwrite ? '~ ghi đè ' : '· bỏ (đã có) ') : '+ tạo    ')
                        . str_replace($base, '[uploads]', $target) . "\n";
                }
                if ($exists && !$overwrite) $skip_exist++; elseif ($exists) $overwritten++; else $created++;
                continue;
            }

            if ($exists && !$overwrite) { $skip_exist++; continue; }

            wp_mkdir_p(dirname($target));
            $in = $zip->getStream($name);
            if (!$in) { echo "  LỖI đọc: $rel\n"; continue; }
            $out = @fopen($target, 'wb');
            if (!$out) { fclose($in); echo "  LỖI ghi: $rel\n"; continue; }
            stream_copy_to_stream($in, $out);
            fclose($in); fclose($out);
            if ($exists) $overwritten++; else $created++;
        }

        $zip->close();

        echo "\n--- KẾT QUẢ" . ($dry ? ' (dry)' : '') . " ---\n";
        echo "Tạo mới:      $created\n";
        echo "Ghi đè:       $overwritten\n";
        echo "Bỏ (đã có):   $skip_exist\n";
        echo "Bỏ (lọc đuôi):$skip_filtered\n";
        echo "Bỏ (k an toàn):$skip_unsafe\n";
        echo "Thư mục:      $dirs\n";
        echo $dry ? "\n→ Bỏ tích 'dry run' rồi Upload lại để giải nén thật.\n" : "\n✓ XONG.\n";
        exit;
    }

    // ---- GET: form upload ----
    $nonce = wp_create_nonce('sgh_upload_zip');
    $url   = esc_url(add_query_arg('sgh_upload_zip', '1', home_url('/')));
    header('Content-Type: text/html; charset=UTF-8');
    ?><!doctype html><html lang="vi"><head><meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>SGH · Upload ZIP → uploads</title>
    <style>
      body{font:15px/1.5 system-ui,Segoe UI,Roboto,sans-serif;background:#0e0f12;color:#e7e3d8;margin:0;padding:2.5rem 1rem;}
      .card{max-width:640px;margin:0 auto;background:#16181d;border:1px solid #2a2d34;border-radius:16px;padding:2rem;}
      h1{font-size:1.35rem;margin:0 0 .35rem;color:#C8A24A;}
      p.sub{margin:0 0 1.4rem;color:#9aa0ab;font-size:.92rem;}
      label.row{display:flex;gap:.6rem;align-items:flex-start;margin:.7rem 0;cursor:pointer;}
      label.row input[type=checkbox]{margin-top:.25rem;}
      .f{display:block;margin:1.1rem 0;}
      .f b{display:block;margin-bottom:.35rem;font-size:.9rem;color:#cdd2da;}
      input[type=file],input[type=text]{width:100%;box-sizing:border-box;background:#0e0f12;border:1px solid #343843;color:#e7e3d8;border-radius:10px;padding:.7rem .8rem;font:inherit;}
      button{margin-top:1.3rem;width:100%;background:#C8A24A;color:#16130b;border:0;border-radius:10px;padding:.85rem;font-weight:700;font-size:1rem;cursor:pointer;}
      button:hover{filter:brightness(1.08);}
      .note{margin-top:1.2rem;font-size:.82rem;color:#8b909b;border-top:1px solid #2a2d34;padding-top:1rem;}
      code{background:#0e0f12;padding:.08rem .35rem;border-radius:5px;color:#C8A24A;}
    </style></head><body>
    <form class="card" method="post" action="<?php echo $url; ?>" enctype="multipart/form-data">
      <h1>Upload ZIP → <code>uploads/</code></h1>
      <p class="sub">Giải nén giữ nguyên cấu trúc thư mục. <b>MERGE</b> — không xoá gì. Zip nên chứa sẵn folder gốc (vd <code>saigonhoreca/…</code>, <code>roka-fella/…</code>).</p>
      <input type="hidden" name="sgh_zip_nonce" value="<?php echo esc_attr($nonce); ?>">

      <label class="f"><b>File .zip</b>
        <input type="file" name="sgh_zip" accept=".zip,application/zip" required>
      </label>

      <label class="f"><b>Thư mục con đích (tuỳ chọn — để trống = thẳng vào uploads/)</b>
        <input type="text" name="subdir" placeholder="vd: 2026/06  hoặc để trống" value="">
      </label>

      <label class="row"><input type="checkbox" name="dry" value="1" checked>
        <span><b>Chỉ liệt kê (dry run)</b> — xem trước nội dung, chưa ghi đĩa. Bỏ tích để giải nén thật.</span></label>
      <label class="row"><input type="checkbox" name="overwrite" value="1">
        <span>Ghi đè file trùng tên (mặc định: bỏ qua file đã có).</span></label>
      <label class="row"><input type="checkbox" name="allow_all" value="1">
        <span>Cho phép mọi loại file (mặc định chỉ ảnh). Vẫn luôn chặn <code>.php/.js/.html…</code></span></label>

      <button type="submit">Upload &amp; xử lý</button>

      <p class="note">Giới hạn server: upload_max_filesize=<code><?php echo esc_html(ini_get('upload_max_filesize')); ?></code> ·
      post_max_size=<code><?php echo esc_html(ini_get('post_max_size')); ?></code>.
      Zip lớn hơn mức này phải chia nhỏ hoặc tăng giới hạn trong <code>php.ini</code>.</p>
    </form></body></html><?php
    exit;
});
