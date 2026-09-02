<?php
if (!defined('ABSPATH')) exit;

/**
 * Lô việc nền của phân hệ đa ngôn ngữ (EP-16 — UC-14).
 *
 * Chỉ nhận những việc CHẠY ĐƯỢC TRONG WORDPRESS: biên dịch lại .mo, quét mã
 * nguồn sinh lại .pot, xoá cache theo ngôn ngữ, dò URL 404 sau khi đổi slug.
 *
 * Việc "dịch lô còn thiếu" mà hồ sơ có nhắc thì KHÔNG nhận ở đây: ADR của chính
 * dự án (07-architecture.md) chốt dashboard là nơi duy nhất gọi mô hình, để
 * WordPress không phải giữ API key của nhà cung cấp. Nhận rồi trả lỗi rõ ràng
 * còn hơn im lặng bỏ qua khiến người dùng tưởng đã xếp hàng.
 *
 * Việc chạy NGAY trong request, không qua wp-cron. Lý do: cả bốn việc đều đo
 * bằng giây trên site này (catalog .po ~1.2K chuỗi), trong khi wp-cron trên
 * WordPress chỉ chạy khi có người truy cập — xếp hàng vào đó thì một site vắng
 * khách sẽ không bao giờ biên dịch xong .mo. Trả 202 kèm mã việc để giao diện
 * vẫn theo dõi được, và để sau này chuyển sang hàng đợi thật không phải đổi
 * hợp đồng API.
 */
class Pi_Multilang_Jobs {

    const OPTION           = 'pi_multilang_jobs';
    const IDEMPOTENCY_OPT  = 'pi_multilang_jobs_idempotency';
    const MAX_JOBS         = 50;

    /** Việc chạy được, kèm mô tả để giao diện khỏi hard-code. */
    public static function kinds() {
        return [
            'compile_mo'   => 'Biên dịch lại tệp .mo từ .po',
            'regen_pot'    => 'Quét mã nguồn, sinh lại tệp mẫu .pot',
            'flush_cache'  => 'Xoá cache trang theo ngôn ngữ',
            'scan_404'     => 'Dò URL 404 sau khi đổi slug',
            'init_locale'  => 'Tạo tệp .po cho một ngôn ngữ chưa có',
        ];
    }

    /**
     * Chạy một việc. Cùng idempotency_key thì trả lại kết quả cũ, không chạy lại.
     *
     * @return array|WP_Error
     */
    public static function run($kind, array $args = [], $idempotency_key = '') {
        $kind = (string) $kind;
        if (!isset(self::kinds()[$kind])) {
            if ($kind === 'translate_missing') {
                return new WP_Error(
                    'job_not_here',
                    'Dịch bằng AI chạy ở dashboard (AI Copilot), không chạy trong WordPress. '
                        . 'Dùng nút "AI điền chuỗi" ở màn Bản dịch.',
                    ['status' => 422]
                );
            }
            return new WP_Error('unknown_job', 'Không có loại việc "' . $kind . '".', ['status' => 422]);
        }

        $key = trim((string) $idempotency_key);
        if ($key === '') {
            return new WP_Error('missing_idempotency_key', 'Thiếu idempotency_key.', ['status' => 422]);
        }

        $seen = (array) get_option(self::IDEMPOTENCY_OPT, []);
        if (isset($seen[$key])) {
            $old = self::get($seen[$key]);
            if ($old) return $old + ['replayed' => true];
        }

        $id  = 'job_' . wp_generate_password(12, false, false);
        $job = [
            'id'         => $id,
            'kind'       => $kind,
            'status'     => 'running',
            'started_at' => current_time('mysql', true),
            'ended_at'   => null,
            'args'       => $args,
            'result'     => null,
            'error'      => null,
        ];
        self::store($job);

        try {
            $job['result'] = self::execute($kind, $args);
            $job['status'] = 'done';
        } catch (Throwable $e) {
            // Việc nền hỏng KHÔNG được ném ra ngoài thành 500: giao diện cần đọc
            // được lý do ở đúng bản ghi việc, chứ không phải ở log server.
            $job['status'] = 'failed';
            $job['error']  = $e->getMessage();
        }

        $job['ended_at'] = current_time('mysql', true);
        self::store($job);

        $seen[$key] = $id;
        if (count($seen) > self::MAX_JOBS) $seen = array_slice($seen, -self::MAX_JOBS, null, true);
        update_option(self::IDEMPOTENCY_OPT, $seen, false);

        return $job + ['replayed' => false];
    }

    private static function execute($kind, array $args) {
        switch ($kind) {
            case 'compile_mo':
                $locale = isset($args['locale']) ? (string) $args['locale'] : '';
                $locales = $locale !== '' ? [$locale] : (array) Pi_Multilang_UI_Strings::available_locales();
                $out = [];
                foreach ($locales as $l) {
                    $res = Pi_Multilang_UI_Strings::compile_mo($l);
                    $out[$l] = is_wp_error($res) ? $res->get_error_message() : $res;
                }
                return ['compiled' => $out];

            case 'regen_pot':
                $res = Pi_Multilang_UI_Strings::regen_pot();
                if (is_wp_error($res)) throw new RuntimeException($res->get_error_message());
                return is_array($res) ? $res : ['ok' => true];

            case 'flush_cache':
                Pi_Multilang_Slug_Batch::flush_site_caches();
                return ['flushed' => true];

            case 'init_locale':
                return self::init_locale($args);

            case 'scan_404':
                return self::scan_404($args);
        }
        throw new RuntimeException('Không có nhánh thực thi cho "' . $kind . '".');
    }

    /**
     * Tạo tệp `.po` rỗng cho một locale từ tệp mẫu `.pot`.
     *
     * available_locales() liệt kê locale bằng cách quét `*.po` trong thư mục
     * theme — chưa có tệp thì ngôn ngữ đó KHÔNG xuất hiện trong ô chọn, và
     * người dùng không có chỗ nào để bắt đầu dịch. Đây là bước mồi.
     *
     * Không ghi đè tệp đã có: đè lên là xoá sạch công dịch.
     */
    private static function init_locale(array $args) {
        $locale = isset($args['locale']) ? trim((string) $args['locale']) : '';
        if ($locale === '' || !preg_match('/^[A-Za-z_@\-]{2,20}$/', $locale)) {
            throw new RuntimeException('Thiếu hoặc sai mã locale (ví dụ: ja, en_US).');
        }

        $po  = Pi_Multilang_UI_Strings::po_path($locale);
        $pot = Pi_Multilang_UI_Strings::pot_path();

        if (file_exists($po)) {
            return ['created' => false, 'reason' => 'Tệp đã có, không ghi đè.', 'path' => $po];
        }
        if (!file_exists($pot)) {
            throw new RuntimeException('Chưa có tệp mẫu .pot — chạy việc "regen_pot" trước.');
        }

        $noi_dung = file_get_contents($pot);
        if ($noi_dung === false) throw new RuntimeException('Không đọc được tệp .pot.');

        // Đặt đúng mã ngôn ngữ trong phần đầu, nếu không WordPress vẫn nạp
        // nhưng công cụ dịch bên ngoài sẽ đọc sai locale.
        $noi_dung = preg_replace(
            '/"Language: [^"]*\\\\n"/',
            '"Language: ' . $locale . '\\\\n"',
            $noi_dung,
            1
        );

        // Tiếng Nhật, Hàn, Trung và Việt KHÔNG chia số nhiều. Bê nguyên
        // `nplurals=2` từ tệp mẫu (vốn viết cho tiếng Anh) sẽ khiến mọi chuỗi
        // _n() nạp sai nhánh, và công cụ dịch bên ngoài bắt người dịch điền hai
        // ô cho một từ không hề có dạng số nhiều.
        $plural = [
            'ja' => 'nplurals=1; plural=0;',
            'ko' => 'nplurals=1; plural=0;',
            'zh' => 'nplurals=1; plural=0;',
            'vi' => 'nplurals=1; plural=0;',
        ];
        $goc = strtolower(substr($locale, 0, 2));
        if (isset($plural[$goc])) {
            $noi_dung = preg_replace(
                '/"Plural-Forms: [^"]*"/',
                '"Plural-Forms: ' . $plural[$goc] . '\\\\n"',
                $noi_dung,
                1
            );
        }

        if (!is_dir(dirname($po))) throw new RuntimeException('Không có thư mục languages của theme.');
        if (file_put_contents($po, $noi_dung) === false) {
            throw new RuntimeException('Không ghi được tệp .po — kiểm tra quyền thư mục.');
        }

        $so_chuoi = preg_match_all('/^msgid /m', $noi_dung);
        return ['created' => true, 'path' => $po, 'strings' => (int) $so_chuoi];
    }

    /**
     * Dò URL 404 sau khi đổi slug.
     *
     * Đi từ CHÍNH bảng alias chứ không bò cả site: thứ vừa đổi mới là thứ có thể
     * gãy, còn quét toàn site trên 3.492 bài thì vừa lâu vừa ồn. Gọi nội bộ qua
     * loopback để đi đúng đường định tuyến thật (filter `request`, alias, 301).
     */
    private static function scan_404(array $args) {
        $limit = isset($args['limit']) ? max(1, min(200, (int) $args['limit'])) : 50;

        global $wpdb;
        $rows = $wpdb->get_results($wpdb->prepare(
            "SELECT lang, alias FROM " . Pi_Multilang_Slugs::table() . "
              WHERE status = 'active' AND TRIM(COALESCE(alias,'')) <> ''
              ORDER BY id DESC LIMIT %d",
            $limit
        )) ?: [];

        $urls = [];
        foreach ($rows as $r) {
            // Tiền tố lấy từ cấu hình chứ không lấy mã ngôn ngữ: hai thứ này khác
            // nhau (ngôn ngữ gốc của host có tiền tố RỖNG), lấy nhầm là dò ra 404
            // giả hàng loạt.
            $prefix = trim((string) Pi_Multilang_Core::prefix_for($r->lang), '/');
            $urls[] = home_url('/' . ($prefix !== '' ? $prefix . '/' : '') . trim($r->alias, '/') . '/');
        }
        $urls = array_values(array_unique($urls));

        $broken = [];
        foreach ($urls as $u) {
            $res  = wp_remote_head($u, ['timeout' => 8, 'redirection' => 3, 'sslverify' => false]);
            $code = is_wp_error($res) ? 0 : (int) wp_remote_retrieve_response_code($res);
            if ($code === 0 || $code >= 400) {
                $broken[] = ['url' => $u, 'status' => $code];
            }
        }

        return ['checked' => count($urls), 'broken' => $broken, 'broken_count' => count($broken)];
    }

    public static function get($id) {
        $all = (array) get_option(self::OPTION, []);
        return $all[$id] ?? null;
    }

    public static function listing($limit = 20) {
        $all = array_values((array) get_option(self::OPTION, []));
        usort($all, static function ($a, $b) {
            return strcmp((string) ($b['started_at'] ?? ''), (string) ($a['started_at'] ?? ''));
        });
        return array_slice($all, 0, max(1, (int) $limit));
    }

    private static function store(array $job) {
        $all = (array) get_option(self::OPTION, []);
        $all[$job['id']] = $job;
        if (count($all) > self::MAX_JOBS) {
            $all = array_slice($all, -self::MAX_JOBS, null, true);
        }
        update_option(self::OPTION, $all, false);
    }
}
