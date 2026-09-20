<?php
if (!defined('ABSPATH')) exit;

/**
 * Quản trị CHUỖI GIAO DIỆN (gettext) của theme từ dashboard pi-multilang.
 *
 * Ranh giới thiết kế — đọc trước khi sửa:
 *
 * Plugin này KHÔNG dịch giao diện lúc chạy. Nó chỉ là công cụ soạn thảo file
 * .po và biên dịch ra .mo. Runtime vẫn hoàn toàn là gettext của WordPress:
 * theme gọi load_theme_textdomain('saigonhoreca', .../languages) ở
 * functions.php:76, và filter `locale` ở inc/engine/slug-map.php đổi sang
 * en_US khi host kết thúc bằng .com. Tắt plugin đi thì site .com vẫn dịch
 * bình thường — đó là tiêu chí nghiệm thu chốt hướng này, và cũng là lý do
 * plugin tuyệt đối KHÔNG hook gettext/ngettext.
 *
 * Đổi lại điều gì: hook gettext để tra bảng riêng thì mất Poedit, mất WP-CLI
 * i18n, mất quy trình dịch giả quen .po, và phải tự cache 2000+ chuỗi mỗi
 * request — viết lại thứ tầng C của gettext đã làm sẵn và làm nhanh hơn.
 *
 * Phân công rõ:
 *   - Pi_Multilang_PO_File  : đọc/sửa .po tại chỗ (một dòng đổi = một dòng đổi)
 *   - lớp này              : đường dẫn, lọc/phân trang, biên dịch .mo, sinh .pot
 *   - WordPress pomo/       : biên dịch .po -> .mo (không viết lại)
 */
class Pi_Multilang_UI_Strings {

    const TEXT_DOMAIN   = 'saigonhoreca';
    const DEFAULT_LOCALE = 'en_US';
    const PER_PAGE      = 50;

    /* ------------------------------------------------------------------ *
     * Đường dẫn
     * ------------------------------------------------------------------ */

    public static function theme_dir() {
        return wp_normalize_path(get_template_directory());
    }

    public static function languages_dir() {
        return self::theme_dir() . '/languages';
    }

    public static function po_path($locale = self::DEFAULT_LOCALE) {
        return self::languages_dir() . '/' . $locale . '.po';
    }

    public static function mo_path($locale = self::DEFAULT_LOCALE) {
        return self::languages_dir() . '/' . $locale . '.mo';
    }

    public static function pot_path() {
        return self::languages_dir() . '/' . self::TEXT_DOMAIN . '.pot';
    }

    /** Các locale có file .po trong theme — không hardcode danh sách. */
    public static function available_locales() {
        $out   = [];
        $files = glob(self::languages_dir() . '/*.po');
        if (is_array($files)) {
            foreach ($files as $f) {
                $out[] = basename($f, '.po');
            }
        }
        if (empty($out)) $out[] = self::DEFAULT_LOCALE;
        return $out;
    }

    /* ------------------------------------------------------------------ *
     * Đọc + lọc
     * ------------------------------------------------------------------ */

    public static function load_po($locale = self::DEFAULT_LOCALE) {
        $po = new Pi_Multilang_PO_File(self::po_path($locale));
        return $po->load() ? $po : null;
    }

    /**
     * Phân loại trạng thái dịch của một entry.
     *
     * Vì sao không chỉ so "msgstr === msgid là chưa dịch": scripts/regen-po.php
     * khi thêm chuỗi mới đặt mặc định msgstr = msgid (regen-po.php:42), nên
     * chuỗi tiếng Việt lọt nguyên sang site .com mà .po trông như đã dịch. Đó
     * là ca cần bắt. NHƯNG rất nhiều chuỗi trùng nhau một cách HỢP LỆ —
     * "Hotline:", "Email", "Facebook" giống hệt ở cả hai ngôn ngữ. Gộp chung
     * hai nhóm lại thì danh sách "chưa dịch" đầy báo động giả và mất tác dụng.
     *
     * Phân biệt bằng chính nội dung msgid: chuỗi có dấu tiếng Việt (Latin mở
     * rộng) mà msgstr y hệt thì chắc chắn chưa dịch; chuỗi thuần ASCII trùng
     * nhau thì nhiều khả năng vốn giống nhau. Đây là quy tắc cho cả lớp đầu
     * vào, không phải danh sách ngoại lệ chép tay.
     *
     * @return string 'empty' | 'leaking' | 'same' | 'translated'
     */
    public static function status(array $entry) {
        $s = $entry['msgstr'];
        if ($s === '') return 'empty';
        if ($s !== $entry['msgid']) return 'translated';
        return self::has_vietnamese_marks($entry['msgid']) ? 'leaking' : 'same';
    }

    /**
     * Có ký tự Latin mang dấu hay không (Latin-1 Supplement, Latin Extended-A/B,
     * Latin Extended Additional). Bộ này phủ trọn dấu tiếng Việt, gồm cả đ/Đ.
     * Không dò từ vựng — chỉ dò dấu, nên không cần từ điển và không đoán bừa.
     */
    public static function has_vietnamese_marks($s) {
        return (bool) preg_match('/[\x{00C0}-\x{024F}\x{1E00}-\x{1EFF}]/u', (string) $s);
    }

    /**
     * "Chưa dịch" = rỗng, hoặc đang lọt tiếng Việt. Chuỗi ASCII trùng nhau
     * KHÔNG tính — xem status() để biết vì sao.
     */
    public static function is_untranslated(array $entry) {
        $st = self::status($entry);
        return ($st === 'empty' || $st === 'leaking');
    }

    /**
     * @return array{items:array,total:int,total_all:int,untranslated:int,pages:int,page:int}
     */
    public static function query(Pi_Multilang_PO_File $po, $args = []) {
        $args = wp_parse_args($args, [
            'search'   => '',
            'filter'   => 'all',   // all | untranslated | translated
            'page'     => 1,
            'per_page' => self::PER_PAGE,
        ]);

        $all    = $po->entries();
        $counts = ['empty' => 0, 'leaking' => 0, 'same' => 0, 'translated' => 0];
        foreach ($all as $i => $e) {
            $st = self::status($e);
            $all[$i]['status'] = $st;
            $counts[$st]++;
        }
        $untranslated = $counts['empty'] + $counts['leaking'];

        $items  = [];
        $needle = trim((string) $args['search']);
        foreach ($all as $e) {
            if ($args['filter'] === 'untranslated' && !self::is_untranslated($e)) continue;
            if ($args['filter'] === 'translated'   &&  self::is_untranslated($e)) continue;
            if ($args['filter'] === 'leaking'      &&  $e['status'] !== 'leaking') continue;
            if ($args['filter'] === 'empty'        &&  $e['status'] !== 'empty') continue;
            if ($needle !== '') {
                $hay = $e['msgid'] . "\n" . $e['msgstr'];
                if (stripos($hay, $needle) === false) continue;
            }
            $items[] = $e;
        }

        $per_page = max(1, min(200, (int) $args['per_page']));
        $total    = count($items);
        $pages    = max(1, (int) ceil($total / $per_page));
        $page     = max(1, min($pages, (int) $args['page']));
        $items    = array_slice($items, ($page - 1) * $per_page, $per_page);

        return [
            'items'        => $items,
            'total'        => $total,
            'total_all'    => count($all),
            'untranslated' => $untranslated,
            'counts'       => $counts,
            'pages'        => $pages,
            'page'         => $page,
            'per_page'     => $per_page,
        ];
    }

    /* ------------------------------------------------------------------ *
     * Ghi
     * ------------------------------------------------------------------ */

    /**
     * Lưu các msgstr đã đổi. Chỉ ghi file khi thực sự có thay đổi, và chỉ
     * biên dịch lại .mo khi .po đã đổi — tránh đụng mtime vô cớ.
     *
     * @param array $pairs [key => msgstr mới]
     * @return array|WP_Error
     */
    public static function save_msgstrs(array $pairs, $locale = self::DEFAULT_LOCALE) {
        $po_path = self::po_path($locale);
        if (!is_writable($po_path)) {
            return new WP_Error('po_not_writable', 'Không ghi được file: ' . $po_path);
        }

        $po = self::load_po($locale);
        if (!$po) {
            return new WP_Error('po_unreadable', 'Không đọc được file .po: ' . $po_path);
        }

        $normalized = [];
        foreach ($pairs as $key => $value) {
            // Chuẩn hoá xuống dòng để không tạo diff giả do CRLF từ trình duyệt.
            $normalized[$key] = str_replace("\r\n", "\n", (string) $value);
        }

        $res = $po->set_msgstrs_batch($normalized);
        if (is_wp_error($res)) return $res;
        $changed = (int) $res;

        if ($changed === 0) {
            return ['changed' => 0, 'mo' => false];
        }
        if (!$po->save()) {
            return new WP_Error('po_write_failed', 'Ghi .po thất bại.');
        }

        $mo = self::compile_mo($locale);
        if (is_wp_error($mo)) return $mo;

        self::flush_static_cache();

        return ['changed' => $changed, 'mo' => true];
    }

    /**
     * Biên dịch .po -> .mo bằng chính lớp PO/MO của WordPress
     * (wp-includes/pomo/) — cùng cách scripts/compile-mo.php đang làm.
     */
    public static function compile_mo($locale = self::DEFAULT_LOCALE) {
        require_once ABSPATH . 'wp-includes/pomo/po.php';
        require_once ABSPATH . 'wp-includes/pomo/mo.php';

        $po_path = self::po_path($locale);
        $mo_path = self::mo_path($locale);

        $po = new PO();
        if (!$po->import_from_file($po_path)) {
            return new WP_Error('po_import_failed', 'Không parse được .po: ' . $po_path);
        }
        $mo = new MO();
        $mo->merge_with($po);
        if (!$mo->export_to_file($mo_path)) {
            return new WP_Error('mo_export_failed', 'Không ghi được .mo: ' . $mo_path);
        }
        clearstatcache(true, $mo_path);
        return true;
    }

    /**
     * Cache tĩnh khoá theo locale (wp-content/cache/sgh-static/<locale>/...),
     * nên sửa bản dịch xong mà không xoá thì trang cũ vẫn được phục vụ. Gọi
     * hàm sẵn có của theme thay vì tự đi xoá file — không đụng inc/engine/.
     */
    private static function flush_static_cache() {
        if (function_exists('sgh_static_cache_flush')) {
            sgh_static_cache_flush();
            return true;
        }
        return false;
    }

    /* ------------------------------------------------------------------ *
     * Trích chuỗi từ mã nguồn -> .pot
     * ------------------------------------------------------------------ */

    /**
     * Chữ ký các hàm gettext: vị trí (0-based) của từng tham số.
     * Đây là bảng dữ liệu, không phải chuỗi if — thêm hàm mới = thêm 1 dòng.
     */
    private static function gettext_signatures() {
        return [
            '__'         => ['text' => 0, 'domain' => 1],
            '_e'         => ['text' => 0, 'domain' => 1],
            'esc_html__' => ['text' => 0, 'domain' => 1],
            'esc_html_e' => ['text' => 0, 'domain' => 1],
            'esc_attr__' => ['text' => 0, 'domain' => 1],
            'esc_attr_e' => ['text' => 0, 'domain' => 1],
            '_x'         => ['text' => 0, 'context' => 1, 'domain' => 2],
            '_ex'        => ['text' => 0, 'context' => 1, 'domain' => 2],
            'esc_html_x' => ['text' => 0, 'context' => 1, 'domain' => 2],
            'esc_attr_x' => ['text' => 0, 'context' => 1, 'domain' => 2],
            '_n'         => ['text' => 0, 'plural' => 1, 'domain' => 3],
            '_nx'        => ['text' => 0, 'plural' => 1, 'context' => 3, 'domain' => 4],
        ];
    }

    /** Thư mục bỏ qua khi quét: không phải mã chạy của theme. */
    private static function skip_dirs() {
        return ['node_modules', 'vendor', 'languages', 'scripts', 'dist', 'cache', '.git'];
    }

    public static function scan_php_files($root) {
        $out  = [];
        $skip = self::skip_dirs();
        if (!is_dir($root)) return $out;

        $it = new RecursiveIteratorIterator(
            new RecursiveCallbackFilterIterator(
                new RecursiveDirectoryIterator($root, RecursiveDirectoryIterator::SKIP_DOTS),
                function ($current) use ($skip) {
                    if ($current->isDir()) return !in_array($current->getFilename(), $skip, true);
                    return strtolower($current->getExtension()) === 'php';
                }
            )
        );
        foreach ($it as $f) {
            if ($f->isFile()) $out[] = wp_normalize_path($f->getPathname());
        }
        sort($out);
        return $out;
    }

    /**
     * Trích chuỗi bằng token_get_all() của PHP, KHÔNG dùng regex.
     *
     * Lý do: regex trên mã PHP sai một cách âm thầm — nó không phân biệt được
     * lời gọi thật với chuỗi nằm trong comment hay trong một string literal
     * khác, không xử lý đúng escape, và không biết `$obj->__()` không phải
     * gettext. Tokenizer là thứ PHP dùng để đọc chính nó, nên đúng với mọi
     * cách viết hợp lệ chứ không chỉ những cách đã gặp.
     *
     * Tham số không phải hằng chuỗi (biến, nối chuỗi) thì BỎ QUA — không đoán
     * giá trị. Chuỗi không trích được thì để thiếu còn hơn bịa vào .pot.
     *
     * @return array key => ['msgid','msgctxt','plural','refs'=>string[]]
     */
    public static function extract_source_strings($root = null) {
        $root  = $root ?: self::theme_dir();
        $found = [];
        foreach (self::scan_php_files($root) as $file) {
            $code = file_get_contents($file);
            if ($code === false) continue;
            self::extract_from_code($code, $file, $root, $found);
        }
        return $found;
    }

    private static function extract_from_code($code, $file, $root, array &$found) {
        $tokens = @token_get_all($code);
        if (!is_array($tokens)) return;

        $sig     = self::gettext_signatures();
        $count   = count($tokens);
        $rel     = ltrim(str_replace($root, '', $file), '/');
        $barrier = [T_OBJECT_OPERATOR, T_DOUBLE_COLON, T_FUNCTION, T_NEW];
        if (defined('T_NULLSAFE_OBJECT_OPERATOR')) $barrier[] = T_NULLSAFE_OBJECT_OPERATOR;

        for ($i = 0; $i < $count; $i++) {
            $tok = $tokens[$i];
            if (!is_array($tok) || $tok[0] !== T_STRING) continue;
            if (!isset($sig[$tok[1]])) continue;

            // Loại method call ($o->__()), static call (C::__()) và định nghĩa hàm.
            $p = self::prev_significant($tokens, $i);
            if ($p !== null && is_array($tokens[$p]) && in_array($tokens[$p][0], $barrier, true)) continue;

            $j = self::next_significant($tokens, $i);
            if ($j === null || $tokens[$j] !== '(') continue;

            $args = self::collect_args($tokens, $j);
            if ($args === null) continue;

            self::record($sig[$tok[1]], $args, $rel, $tok[2], $found);
        }
    }

    private static function prev_significant($tokens, $i) {
        for ($k = $i - 1; $k >= 0; $k--) {
            if (is_array($tokens[$k]) && in_array($tokens[$k][0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)) continue;
            return $k;
        }
        return null;
    }

    private static function next_significant($tokens, $i) {
        $n = count($tokens);
        for ($k = $i + 1; $k < $n; $k++) {
            if (is_array($tokens[$k]) && in_array($tokens[$k][0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)) continue;
            return $k;
        }
        return null;
    }

    /**
     * Gom tham số của lời gọi bắt đầu tại dấu '(' ở $open.
     * Trả về mảng các tham số, mỗi tham số là mảng token. null nếu ngoặc lệch.
     */
    private static function collect_args($tokens, $open) {
        $depth = 0;
        $args  = [];
        $cur   = [];
        $n     = count($tokens);

        for ($k = $open; $k < $n; $k++) {
            $t = $tokens[$k];

            if (!is_array($t)) {
                if ($t === '(' || $t === '[' || $t === '{') {
                    $depth++;
                    if ($depth === 1) continue;    // chính dấu '(' mở lời gọi
                } elseif ($t === ')' || $t === ']' || $t === '}') {
                    $depth--;
                    if ($depth === 0) {            // đóng lời gọi
                        $args[] = $cur;
                        return $args;
                    }
                } elseif ($t === ',' && $depth === 1) {
                    $args[] = $cur;
                    $cur    = [];
                    continue;
                }
            }
            if ($depth >= 1) $cur[] = $t;
        }
        return null; // ngoặc không đóng -> bỏ qua, không đoán
    }

    /** Tham số là MỘT hằng chuỗi thì trả giá trị, ngược lại null. */
    private static function literal_arg($arg) {
        $sig = [];
        foreach ($arg as $t) {
            if (is_array($t) && in_array($t[0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)) continue;
            $sig[] = $t;
        }
        if (count($sig) !== 1) return null;
        $t = $sig[0];
        if (!is_array($t) || $t[0] !== T_CONSTANT_ENCAPSED_STRING) return null;

        $raw  = $t[1];
        $body = substr($raw, 1, -1);
        if ($raw[0] === "'") {
            return str_replace(['\\\\', "\\'"], ['\\', "'"], $body);
        }
        return stripcslashes($body);
    }

    private static function record(array $spec, array $args, $rel, $line, array &$found) {
        $get = function ($idx) use ($args) {
            return isset($args[$idx]) ? self::literal_arg($args[$idx]) : null;
        };

        $domain = $get($spec['domain']);
        if ($domain !== self::TEXT_DOMAIN) return;   // chuỗi của domain khác

        $text = $get($spec['text']);
        if ($text === null || $text === '') return;  // không phải hằng -> không đoán

        $context = isset($spec['context']) ? $get($spec['context']) : null;
        $plural  = isset($spec['plural'])  ? $get($spec['plural'])  : null;

        $key = Pi_Multilang_PO_File::make_key($text, $context);
        if (!isset($found[$key])) {
            $found[$key] = [
                'msgid'   => $text,
                'msgctxt' => $context,
                'plural'  => $plural,
                'refs'    => [],
            ];
        }
        if ($plural !== null && $found[$key]['plural'] === null) {
            $found[$key]['plural'] = $plural;
        }
        $ref = $rel . ':' . $line;
        if (!in_array($ref, $found[$key]['refs'], true)) {
            $found[$key]['refs'][] = $ref;
        }
    }

    /**
     * Sinh lại saigonhoreca.pot từ mã nguồn.
     *
     * .pot là TEMPLATE: msgstr luôn rỗng. Không chép bản dịch từ .po sang, và
     * cũng không đụng tới .po — regen .pot là thao tác chỉ-ghi-một-file.
     *
     * @return array|WP_Error
     */
    public static function regen_pot() {
        $dir = self::languages_dir();
        if (!is_writable($dir)) {
            return new WP_Error('dir_not_writable', 'Không ghi được thư mục: ' . $dir);
        }

        $found = self::extract_source_strings();

        // Sắp theo tham chiếu đầu tiên -> file .pot ổn định giữa các lần chạy,
        // diff chỉ phản ánh chuỗi thật sự thêm/bớt.
        uasort($found, function ($a, $b) {
            $ra = isset($a['refs'][0]) ? $a['refs'][0] : '';
            $rb = isset($b['refs'][0]) ? $b['refs'][0] : '';
            $c  = strcmp($ra, $rb);
            return $c !== 0 ? $c : strcmp($a['msgid'], $b['msgid']);
        });

        $now = gmdate('Y-m-d H:iO');
        $out = 'msgid ""' . "\n" . 'msgstr ""' . "\n"
             . '"Project-Id-Version: Saigon Horeca\n"' . "\n"
             . '"Report-Msgid-Bugs-To: https://saigonhoreca.vn\n"' . "\n"
             . '"POT-Creation-Date: ' . $now . '\n"' . "\n"
             . '"MIME-Version: 1.0\n"' . "\n"
             . '"Content-Type: text/plain; charset=UTF-8\n"' . "\n"
             . '"Content-Transfer-Encoding: 8bit\n"' . "\n"
             . '"Language: \n"' . "\n"
             . '"Plural-Forms: nplurals=2; plural=(n != 1);\n"' . "\n"
             . '"X-Generator: pi-multilang\n"' . "\n";

        foreach ($found as $e) {
            $out .= "\n";
            foreach ($e['refs'] as $ref) {
                $out .= '#: ' . $ref . "\n";
            }
            if ($e['msgctxt'] !== null) {
                $out .= 'msgctxt "' . Pi_Multilang_PO_File::encode($e['msgctxt']) . '"' . "\n";
            }
            $out .= 'msgid "' . Pi_Multilang_PO_File::encode($e['msgid']) . '"' . "\n";
            if ($e['plural'] !== null) {
                $out .= 'msgid_plural "' . Pi_Multilang_PO_File::encode($e['plural']) . '"' . "\n";
                $out .= 'msgstr[0] ""' . "\n";
                $out .= 'msgstr[1] ""' . "\n";
            } else {
                $out .= 'msgstr ""' . "\n";
            }
        }

        $path = self::pot_path();
        if (file_put_contents($path, $out, LOCK_EX) === false) {
            return new WP_Error('pot_write_failed', 'Ghi .pot thất bại: ' . $path);
        }
        clearstatcache(true, $path);

        return ['strings' => count($found), 'path' => $path];
    }
}
