<?php
if (!defined('ABSPATH')) exit;

class Pi_Multilang_Admin {

    const MENU_SLUG   = 'pi-multilang';
    const DRAFTS_SLUG = 'pi-multilang-drafts';
    const UI_SLUG     = 'pi-multilang-ui-strings';

    /** Ghi vào thư mục theme là thao tác của quản trị viên, không phải biên tập viên. */
    const CAP_WRITE = 'manage_options';

    public static function init() {
        add_action('admin_menu', [__CLASS__, 'add_menu']);
        add_action('admin_post_pi_multilang_save_strings', [__CLASS__, 'handle_save_strings']);
        add_action('admin_post_pi_multilang_regen_pot',    [__CLASS__, 'handle_regen_pot']);
        add_action('admin_post_pi_multilang_compile_mo',   [__CLASS__, 'handle_compile_mo']);
    }

    public static function add_menu() {
        add_menu_page(
            'Pi Multilang',
            'Pi Multilang',
            'edit_posts',
            self::MENU_SLUG,
            [__CLASS__, 'render_drafts_page'],
            'dashicons-translation',
            67
        );

        add_submenu_page(
            self::MENU_SLUG,
            'Bản dịch nội dung',
            'Bản dịch nội dung',
            'edit_posts',
            self::MENU_SLUG,
            [__CLASS__, 'render_drafts_page']
        );

        add_submenu_page(
            self::MENU_SLUG,
            'Chuỗi giao diện',
            'Chuỗi giao diện',
            self::CAP_WRITE,
            self::UI_SLUG,
            [__CLASS__, 'render_ui_strings_page']
        );
    }

    /* ================================================================== *
     * Trang 1 — bản dịch NỘI DUNG (post/page draft do LLM7 sinh)
     * ================================================================== */

    public static function render_drafts_page() {
        echo '<div class="wrap">';
        echo '<h1>Duyệt Bản Dịch (Drafts)</h1>';

        $drafts = get_posts([
            'post_type'      => 'any',
            'post_status'    => 'draft',
            'meta_key'       => 'pi_translation_group',
            'posts_per_page' => -1,
        ]);

        $rows = '';
        foreach ($drafts as $d) {
            $lang = get_post_meta($d->ID, 'pi_language', true);
            if ($lang !== 'en') continue;
            $edit_link = get_edit_post_link($d->ID);
            $rows .= '<tr>';
            $rows .= '<td><strong><a href="' . esc_url($edit_link) . '">' . esc_html($d->post_title) . '</a></strong></td>';
            $rows .= '<td>' . esc_html($d->post_type) . '</td>';
            $rows .= '<td>' . esc_html($lang) . '</td>';
            $rows .= '<td><a href="' . esc_url($edit_link) . '" class="button">Xem / Sửa</a></td>';
            $rows .= '</tr>';
        }

        if ($rows === '') {
            echo '<p>Không có bản dịch nào chờ duyệt.</p>';
        } else {
            echo '<table class="wp-list-table widefat fixed striped">';
            echo '<thead><tr><th>Tiêu đề (Draft)</th><th>Loại (Type)</th><th>Ngôn ngữ</th><th>Hành động</th></tr></thead>';
            echo '<tbody>' . $rows . '</tbody></table>';
        }

        echo '<p style="margin-top:16px;">Chuỗi giao diện (nút, nhãn, menu) nằm ở trang '
           . '<a href="' . esc_url(admin_url('admin.php?page=' . self::UI_SLUG)) . '">Chuỗi giao diện</a>.</p>';
        echo '</div>';
    }

    /* ================================================================== *
     * Trang 2 — chuỗi GIAO DIỆN (gettext .po/.mo)
     * ================================================================== */

    private static function redirect_back($args) {
        $base = admin_url('admin.php?page=' . self::UI_SLUG);
        wp_safe_redirect(add_query_arg($args, $base));
        exit;
    }

    public static function handle_save_strings() {
        if (!current_user_can(self::CAP_WRITE)) wp_die('Không đủ quyền.');
        check_admin_referer('pi_multilang_save_strings');

        $locale = isset($_POST['locale']) ? sanitize_text_field(wp_unslash($_POST['locale'])) : Pi_Multilang_UI_Strings::DEFAULT_LOCALE;
        $raw    = isset($_POST['strings']) && is_array($_POST['strings']) ? wp_unslash($_POST['strings']) : [];

        $pairs = [];
        foreach ($raw as $key => $value) {
            // Khoá là md5 do chính plugin sinh ra; lọc để không dùng input thô.
            if (!preg_match('/^[a-f0-9]{32}$/', (string) $key)) continue;
            // KHÔNG sanitize nội dung: bản dịch là văn bản thuần, sanitize sẽ
            // ăn mất dấu nháy, dấu & và ký tự tiếng Việt hợp lệ. Giá trị chỉ
            // được ghi vào .po (đã escape) và escape lại khi in ra HTML.
            $pairs[$key] = (string) $value;
        }

        $result = Pi_Multilang_UI_Strings::save_msgstrs($pairs, $locale);

        $back = self::keep_view_args();
        if (is_wp_error($result)) {
            $back['pi_err'] = $result->get_error_message();
        } else {
            $back['pi_saved'] = (int) $result['changed'];
        }
        self::redirect_back($back);
    }

    public static function handle_regen_pot() {
        if (!current_user_can(self::CAP_WRITE)) wp_die('Không đủ quyền.');
        check_admin_referer('pi_multilang_regen_pot');

        $result = Pi_Multilang_UI_Strings::regen_pot();

        $back = self::keep_view_args();
        if (is_wp_error($result)) {
            $back['pi_err'] = $result->get_error_message();
        } else {
            $back['pi_pot'] = (int) $result['strings'];
        }
        self::redirect_back($back);
    }

    public static function handle_compile_mo() {
        if (!current_user_can(self::CAP_WRITE)) wp_die('Không đủ quyền.');
        check_admin_referer('pi_multilang_compile_mo');

        $locale = isset($_POST['locale']) ? sanitize_text_field(wp_unslash($_POST['locale'])) : Pi_Multilang_UI_Strings::DEFAULT_LOCALE;
        $result = Pi_Multilang_UI_Strings::compile_mo($locale);

        $back = self::keep_view_args();
        if (is_wp_error($result)) {
            $back['pi_err'] = $result->get_error_message();
        } else {
            $back['pi_mo'] = 1;
        }
        self::redirect_back($back);
    }

    /** Giữ nguyên bộ lọc/trang/tìm kiếm khi redirect về. */
    private static function keep_view_args() {
        $out = [];
        foreach (['locale', 'filter', 's', 'paged'] as $k) {
            $src = isset($_POST[$k]) ? $_POST[$k] : (isset($_GET[$k]) ? $_GET[$k] : null);
            if ($src !== null && $src !== '') {
                $out[$k] = sanitize_text_field(wp_unslash($src));
            }
        }
        return $out;
    }

    public static function render_ui_strings_page() {
        if (!current_user_can(self::CAP_WRITE)) wp_die('Không đủ quyền.');

        $locales = Pi_Multilang_UI_Strings::available_locales();
        $locale  = isset($_GET['locale']) ? sanitize_text_field(wp_unslash($_GET['locale'])) : Pi_Multilang_UI_Strings::DEFAULT_LOCALE;
        if (!in_array($locale, $locales, true)) $locale = $locales[0];

        $filter = isset($_GET['filter']) ? sanitize_text_field(wp_unslash($_GET['filter'])) : 'all';
        if (!in_array($filter, ['all', 'untranslated', 'leaking', 'empty', 'translated'], true)) $filter = 'all';
        $search = isset($_GET['s']) ? sanitize_text_field(wp_unslash($_GET['s'])) : '';
        $paged  = isset($_GET['paged']) ? max(1, (int) $_GET['paged']) : 1;

        $po_path = Pi_Multilang_UI_Strings::po_path($locale);
        $mo_path = Pi_Multilang_UI_Strings::mo_path($locale);
        $pot     = Pi_Multilang_UI_Strings::pot_path();

        echo '<div class="wrap">';
        echo '<h1>Chuỗi giao diện (gettext)</h1>';

        self::render_notices();

        $po = Pi_Multilang_UI_Strings::load_po($locale);
        if (!$po) {
            echo '<div class="notice notice-error"><p>Không đọc được file: <code>' . esc_html($po_path) . '</code></p></div></div>';
            return;
        }

        $q = Pi_Multilang_UI_Strings::query($po, [
            'search' => $search,
            'filter' => $filter,
            'page'   => $paged,
        ]);

        self::render_styles();
        self::render_progress($q);
        self::render_toolbar($q, $locale, $filter, $search);
        self::render_table($q, $locale, $filter, $search);
        self::render_pagination($q, $locale, $filter, $search);
        self::render_tech_details($locale, $po_path, $mo_path, $pot, $q);

        echo '</div>';
    }

    private static function render_notices() {
        if (isset($_GET['pi_err'])) {
            echo '<div class="notice notice-error is-dismissible"><p>' . esc_html(wp_unslash($_GET['pi_err'])) . '</p></div>';
        }
        if (isset($_GET['pi_saved'])) {
            $n = (int) $_GET['pi_saved'];
            if ($n > 0) {
                echo '<div class="notice notice-success is-dismissible"><p><strong>Đã lưu ' . $n . ' chuỗi.</strong> '
                   . 'Bản dịch đã được biên dịch và cache đã xoá — mở lại trang ngoài site là thấy ngay.</p></div>';
            } else {
                echo '<div class="notice notice-info is-dismissible"><p>Bạn chưa sửa chuỗi nào, nên không có gì để lưu.</p></div>';
            }
        }
        if (isset($_GET['pi_pot'])) {
            echo '<div class="notice notice-success is-dismissible"><p>Đã quét lại mã nguồn: tìm thấy '
               . (int) $_GET['pi_pot'] . ' chuỗi.</p></div>';
        }
        if (isset($_GET['pi_mo'])) {
            echo '<div class="notice notice-success is-dismissible"><p>Đã biên dịch lại bản dịch.</p></div>';
        }
    }

    /* ------------------------------------------------------------------ *
     * Hiển thị
     * ------------------------------------------------------------------ */

    private static function render_styles() {
        ?>
        <style>
            .pi-bar{background:#dcdcde;border-radius:9px;height:18px;overflow:hidden;max-width:760px;margin:4px 0 6px}
            .pi-bar span{display:block;height:100%;background:#00a32a;transition:width .3s}
            .pi-stats{display:flex;gap:12px;flex-wrap:wrap;margin:16px 0 20px}
            .pi-stat{background:#fff;border:1px solid #dcdcde;border-left-width:4px;border-radius:3px;
                     padding:10px 16px;min-width:150px}
            .pi-stat b{display:block;font-size:22px;line-height:1.2}
            .pi-stat span{color:#646970;font-size:12px}
            .pi-stat.is-warn{border-left-color:#b32d2e}
            .pi-stat.is-empty{border-left-color:#dba617}
            .pi-stat.is-same{border-left-color:#996800}
            .pi-stat.is-ok{border-left-color:#00a32a}
            .pi-badge{display:inline-block;padding:2px 8px;border-radius:9px;font-size:11px;font-weight:600;white-space:nowrap}
            .pi-badge.leaking{background:#fcf0f1;color:#b32d2e}
            .pi-badge.empty{background:#fcf9e8;color:#8a6116}
            .pi-badge.same{background:#f6f7f7;color:#646970}
            .pi-badge.translated{background:#edfaef;color:#007017}
            .pi-src{background:#f6f7f7;border-left:3px solid #c3c4c7;padding:8px 10px;border-radius:2px;
                    line-height:1.5;word-break:break-word}
            .pi-row-warn .pi-src{border-left-color:#b32d2e}
            .pi-strings td{vertical-align:top;padding:12px 10px}
            .pi-strings textarea{width:100%;min-height:62px;font-size:13px;line-height:1.5}
            .pi-actions{display:flex;gap:8px;align-items:center;flex-wrap:wrap}
            .pi-tech{margin-top:28px;max-width:900px}
            .pi-tech summary{cursor:pointer;color:#646970;font-size:13px}
            .pi-tech table{margin-top:10px}
            .pi-tech code{font-size:11px;word-break:break-all}
        </style>
        <?php
    }

    /** Thanh tiến độ + 4 ô số. Đây là thứ người dịch cần thấy đầu tiên. */
    private static function render_progress($q) {
        $c     = $q['counts'];
        $total = max(1, (int) $q['total_all']);
        $done  = (int) $c['translated'] + (int) $c['same'];
        $pct   = (int) round($done / $total * 100);

        printf(
            '<p style="margin:14px 0 2px;font-size:14px;"><strong>%d / %d</strong> chuỗi đã có bản dịch tiếng Anh <strong>(%d%%)</strong></p>',
            $done, (int) $q['total_all'], $pct
        );
        printf('<div class="pi-bar"><span style="width:%d%%"></span></div>', $pct);

        echo '<div class="pi-stats">';
        self::stat('is-warn',  $c['leaking'],    'Đang hiện tiếng Việt', 'Có bản dịch nhưng vẫn là tiếng Việt');
        self::stat('is-empty', $c['empty'],      'Chưa điền',            'Ô bản dịch bỏ trống');
        self::stat('is-same',  $c['same'],       'Giữ nguyên',           'Hai ngôn ngữ viết giống nhau');
        self::stat('is-ok',    $c['translated'], 'Đã dịch',              'Không cần làm gì');
        echo '</div>';
    }

    private static function stat($class, $num, $label, $hint) {
        printf(
            '<div class="pi-stat %s" title="%s"><b>%d</b><span>%s</span></div>',
            esc_attr($class), esc_attr($hint), (int) $num, esc_html($label)
        );
    }

    private static function view_url($locale, $filter, $search, $paged = 1) {
        $args = ['page' => self::UI_SLUG, 'locale' => $locale];
        if ($filter !== 'all') $args['filter'] = $filter;
        if ($search !== '')    $args['s']      = $search;
        if ($paged > 1)        $args['paged']  = $paged;
        return add_query_arg($args, admin_url('admin.php'));
    }

    /**
     * Bộ lọc dạng link (kiểu chuẩn của WP), KHÔNG phải dropdown + nút "Lọc".
     * Dropdown cũ hiện lựa chọn mới ngay khi vừa đổi, trong khi bảng bên dưới
     * vẫn là kết quả cũ cho tới lúc bấm nút — nhìn như đã lọc mà thực ra chưa.
     */
    private static function render_toolbar($q, $locale, $filter, $search) {
        $c = $q['counts'];
        // Nhãn phải khớp ĐÚNG số dòng bộ lọc trả về. Bộ lọc 'translated' loại
        // mọi thứ "chưa dịch", nghĩa là nó gồm cả nhóm 'same' (hai ngôn ngữ
        // viết giống nhau) — nên số phải là translated + same, và đó cũng
        // chính là con số trên thanh tiến độ. Trước đó nhãn ghi 729 nhưng trả
        // về cả dòng 'same', tức nhãn nói một đằng nội dung một nẻo.
        $views = [
            'all'          => ['Tất cả',               (int) $q['total_all']],
            'leaking'      => ['Đang hiện tiếng Việt', (int) $c['leaking']],
            'empty'        => ['Chưa điền',            (int) $c['empty']],
            'untranslated' => ['Cần xử lý',            (int) $q['untranslated']],
            'translated'   => ['Không cần sửa',        (int) $c['translated'] + (int) $c['same']],
        ];

        echo '<ul class="subsubsub" style="margin-bottom:8px;">';
        $i = 0;
        foreach ($views as $key => $v) {
            $i++;
            printf(
                '<li>%s<a href="%s"%s>%s <span class="count">(%d)</span></a>%s</li>',
                '',
                esc_url(self::view_url($locale, $key, $search)),
                $filter === $key ? ' class="current" aria-current="page"' : '',
                esc_html($v[0]),
                $v[1],
                $i < count($views) ? ' |' : ''
            );
        }
        echo '</ul>';

        echo '<div class="pi-actions" style="clear:both;padding-top:6px;">';

        echo '<form method="get" action="' . esc_url(admin_url('admin.php')) . '" class="pi-actions">';
        echo '<input type="hidden" name="page" value="' . esc_attr(self::UI_SLUG) . '">';
        echo '<input type="hidden" name="locale" value="' . esc_attr($locale) . '">';
        if ($filter !== 'all') echo '<input type="hidden" name="filter" value="' . esc_attr($filter) . '">';
        echo '<input type="search" name="s" value="' . esc_attr($search) . '" placeholder="Tìm trong chuỗi gốc hoặc bản dịch" style="width:280px;">';
        echo '<button class="button">Tìm</button>';
        if ($search !== '') {
            echo '<a class="button-link" href="' . esc_url(self::view_url($locale, $filter, '')) . '">Xoá tìm kiếm</a>';
        }
        echo '</form>';

        echo '<span style="flex:1"></span>';

        echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
        wp_nonce_field('pi_multilang_regen_pot');
        echo '<input type="hidden" name="action" value="pi_multilang_regen_pot">';
        echo '<input type="hidden" name="locale" value="' . esc_attr($locale) . '">';
        echo '<button class="button" title="Quét lại toàn bộ mã nguồn theme để tìm chuỗi mới cần dịch">Quét chuỗi mới</button>';
        echo '</form>';

        echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
        wp_nonce_field('pi_multilang_compile_mo');
        echo '<input type="hidden" name="action" value="pi_multilang_compile_mo">';
        echo '<input type="hidden" name="locale" value="' . esc_attr($locale) . '">';
        echo '<button class="button" title="Dựng lại file bản dịch từ dữ liệu hiện có">Biên dịch lại</button>';
        echo '</form>';

        echo '</div>';
    }

    private static function render_table($q, $locale, $filter, $search) {
        $labels = [
            'empty'      => ['Chưa điền',            'empty'],
            'leaking'    => ['Đang hiện tiếng Việt', 'leaking'],
            'same'       => ['Giữ nguyên',           'same'],
            'translated' => ['Đã dịch',              'translated'],
        ];

        echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
        wp_nonce_field('pi_multilang_save_strings');
        echo '<input type="hidden" name="action" value="pi_multilang_save_strings">';
        echo '<input type="hidden" name="locale" value="' . esc_attr($locale) . '">';
        echo '<input type="hidden" name="filter" value="' . esc_attr($filter) . '">';
        echo '<input type="hidden" name="s" value="' . esc_attr($search) . '">';
        echo '<input type="hidden" name="paged" value="' . (int) $q['page'] . '">';

        echo '<table class="wp-list-table widefat striped pi-strings"><thead><tr>'
           . '<th style="width:42%;">Chữ trên website (tiếng Việt)</th>'
           . '<th style="width:42%;">Dịch sang tiếng Anh</th>'
           . '<th style="width:16%;">Trạng thái</th>'
           . '</tr></thead><tbody>';

        if (empty($q['items'])) {
            echo '<tr><td colspan="3" style="padding:24px;text-align:center;color:#646970;">'
               . 'Không có chuỗi nào khớp. Thử bỏ bớt bộ lọc hoặc từ khoá tìm kiếm.</td></tr>';
        }

        foreach ($q['items'] as $e) {
            $st  = isset($e['status']) ? $e['status'] : Pi_Multilang_UI_Strings::status($e);
            $lbl = isset($labels[$st]) ? $labels[$st] : ['?', 'same'];
            $warn = ($st === 'leaking' || $st === 'empty');

            echo '<tr' . ($warn ? ' class="pi-row-warn"' : '') . '>';
            echo '<td><div class="pi-src">' . esc_html($e['msgid']) . '</div>';
            if ($e['msgctxt'] !== null) {
                echo '<p class="description" style="margin:4px 0 0;">Ngữ cảnh: ' . esc_html($e['msgctxt']) . '</p>';
            }
            echo '</td>';

            echo '<td><textarea name="strings[' . esc_attr($e['key']) . ']" rows="2" '
               . 'placeholder="Nhập bản dịch tiếng Anh...">' . esc_textarea($e['msgstr']) . '</textarea></td>';

            printf('<td><span class="pi-badge %s">%s</span></td>', esc_attr($lbl[1]), esc_html($lbl[0]));
            echo '</tr>';
        }

        echo '</tbody></table>';
        echo '<p style="margin-top:14px;"><button class="button button-primary button-large">Lưu trang này</button> '
           . '<span class="description" style="margin-left:8px;">Chỉ những ô bạn thực sự sửa mới được ghi lại.</span></p>';
        echo '</form>';
    }

    private static function render_pagination($q, $locale, $filter, $search) {
        if ($q['pages'] <= 1) return;
        echo '<div class="tablenav bottom"><div class="tablenav-pages">';
        printf('<span class="displaying-num">%d chuỗi</span> ', (int) $q['total']);
        echo paginate_links([
            'base'      => str_replace('%23%23', '%#%', self::view_url($locale, $filter, $search, 0) . '&paged=%#%'),
            'format'    => '',
            'prev_text' => '&laquo;',
            'next_text' => '&raquo;',
            'total'     => (int) $q['pages'],
            'current'   => (int) $q['page'],
        ]);
        echo '</div></div>';
    }

    /**
     * Chi tiết kỹ thuật — gấp lại, vì người dịch không cần và nó chiếm hết
     * phần đầu trang. Vẫn giữ đủ số liệu đối chiếu để verify khi cần.
     */
    private static function render_tech_details($locale, $po_path, $mo_path, $pot, $q) {
        $mo_time  = file_exists($mo_path) ? date_i18n('d/m/Y H:i', filemtime($mo_path)) : 'chưa có';
        $pot_n    = file_exists($pot) ? self::count_pot_msgids($pot) : ['entries' => 0, 'grep' => 0];
        $writable = is_writable($po_path);

        echo '<details class="pi-tech"><summary>Chi tiết kỹ thuật (đường dẫn file, cách hoạt động)</summary>';

        if (!$writable) {
            echo '<div class="notice notice-error inline"><p><strong>Không ghi được file bản dịch.</strong> '
               . 'Mọi thay đổi sẽ không lưu được cho tới khi sửa quyền ghi.</p></div>';
        }

        echo '<table class="widefat striped"><tbody>';
        printf('<tr><td style="width:190px;"><strong>Ngôn ngữ</strong></td><td><code>%s</code></td></tr>', esc_html($locale));
        printf('<tr><td><strong>Số chuỗi</strong></td><td>%d (thêm 1 dòng header = %d, khớp <code>grep -c \'^msgid\'</code>)</td></tr>',
            (int) $q['total_all'], (int) $q['total_all'] + 1);
        printf('<tr><td><strong>File soạn thảo</strong></td><td><code>%s</code></td></tr>', esc_html($po_path));
        printf('<tr><td><strong>File biên dịch</strong></td><td><code>%s</code><br>cập nhật %s</td></tr>',
            esc_html($mo_path), esc_html($mo_time));
        printf('<tr><td><strong>File mẫu (.pot)</strong></td><td><code>%s</code><br>%d chuỗi (<code>grep -c \'^msgid\'</code> = %d, gồm header và %d dòng <code>msgid_plural</code>)</td></tr>',
            esc_html($pot), (int) $pot_n['entries'], (int) $pot_n['grep'],
            max(0, (int) $pot_n['grep'] - (int) $pot_n['entries'] - 1));
        echo '</tbody></table>';

        echo '<p class="description">Bản dịch chạy bằng gettext gốc của WordPress: theme nạp <code>.mo</code> qua '
           . '<code>load_theme_textdomain()</code>. Trang này chỉ soạn <code>.po</code> rồi biên dịch <code>.mo</code> — '
           . 'tắt plugin thì site vẫn dịch bình thường.</p>';
        echo '</details>';
    }

    private static function count_pot_msgids($pot) {
        $entries = 0;
        $grep    = 0;
        $fh = @fopen($pot, 'r');
        if (!$fh) return ['entries' => 0, 'grep' => 0];
        while (($line = fgets($fh)) !== false) {
            if (strpos($line, 'msgid') !== 0) continue;
            $grep++;
            if (strpos($line, 'msgid ') === 0) $entries++;
        }
        fclose($fh);
        return ['entries' => max(0, $entries - 1), 'grep' => $grep];
    }
}
