<?php
/**
 * Pi API — SEO Health Audit Endpoints
 *
 * Runs site-wide SEO checks, returns structured list of issues with
 * sample affected posts. Some issues are auto-fixable via POST /fix/{id}.
 *
 * Routes:
 *   GET  /pi/v1/seo/health/run
 *   POST /pi/v1/seo/health/fix/{id}
 *   GET  /pi/v1/seo/health/history
 *
 * History storage: option `pi_seo_health_history` (last 20 runs).
 *
 * @package Pi_Api
 */

if (!defined('ABSPATH')) exit;

class Pi_Api_SeoHealth {

    const OPT_HISTORY = 'pi_seo_health_history';
    const HISTORY_MAX = 20;
    const SAMPLE_LIMIT = 5;

    /* List of check IDs in display order. */
    const CHECK_IDS = [
        'missing_meta_description',
        'missing_meta_title',
        'duplicate_titles',
        'missing_alt_text',
        'missing_featured_image',
        'noindex_posts',
        'short_content',
        'missing_categories',
        'large_images',
        'broken_internal_links',
        'orphaned_pages',
        'trailing_spaces_in_title',
    ];

    /* ═══════════════════════════════════════════════════════════════════════
       ROUTE REGISTRATION
       ═══════════════════════════════════════════════════════════════════════ */

    public static function register_routes($ns, $admin) {
        register_rest_route($ns, '/seo/health/run', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'run_checks'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/seo/health/fix/(?P<id>[a-zA-Z0-9_\-]+)', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'fix_issue'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/seo/health/history', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'get_history'],
            'permission_callback' => $admin,
        ]);
    }

    /* ═══════════════════════════════════════════════════════════════════════
       PUBLIC — RUN / FIX / HISTORY
       ═══════════════════════════════════════════════════════════════════════ */

    public static function run_checks(\WP_REST_Request $request) {
        $checks = [];

        foreach (self::CHECK_IDS as $id) {
            $method = 'check_' . $id;
            if (method_exists(self::class, $method)) {
                $checks[] = self::$method();
            }
        }

        // Summary tally for history
        $summary = self::summarize($checks);

        // Save history entry (cap at HISTORY_MAX)
        $history = get_option(self::OPT_HISTORY, []);
        if (!is_array($history)) $history = [];

        array_unshift($history, [
            'timestamp' => current_time('mysql'),
            'summary'   => $summary,
            'checks'    => array_map(function ($c) {
                return [
                    'id'             => $c['id'],
                    'severity'       => $c['severity'],
                    'title'          => $c['title'],
                    'affected_count' => (int) ($c['affected_count'] ?? 0),
                ];
            }, $checks),
        ]);

        if (count($history) > self::HISTORY_MAX) {
            $history = array_slice($history, 0, self::HISTORY_MAX);
        }
        update_option(self::OPT_HISTORY, $history, false);

        return new \WP_REST_Response([
            'timestamp' => current_time('mysql'),
            'summary'   => $summary,
            'checks'    => $checks,
        ], 200);
    }

    public static function fix_issue(\WP_REST_Request $request) {
        $id = (string) $request->get_param('id');

        $fix_map = [
            'trailing_spaces_in_title' => 'fix_trailing_spaces_in_title',
            'missing_alt_text'         => 'fix_missing_alt_text',
        ];

        if (!isset($fix_map[$id])) {
            return new \WP_REST_Response([
                'success' => false,
                'message' => 'Không hỗ trợ sửa tự động cho kiểm tra này. Cần sửa thủ công.',
            ], 400);
        }

        $method = $fix_map[$id];
        $result = self::$method();

        return new \WP_REST_Response(array_merge([
            'success' => true,
            'id'      => $id,
        ], $result), 200);
    }

    public static function get_history(\WP_REST_Request $request) {
        $history = get_option(self::OPT_HISTORY, []);
        if (!is_array($history)) $history = [];

        return new \WP_REST_Response([
            'items' => array_values($history),
            'total' => count($history),
        ], 200);
    }

    /* ═══════════════════════════════════════════════════════════════════════
       INDIVIDUAL CHECKS
       ═══════════════════════════════════════════════════════════════════════ */

    /** 1. Posts/pages missing meta description. */
    private static function check_missing_meta_description() {
        global $wpdb;

        $sql = "
            SELECT p.ID, p.post_title, p.post_type
            FROM {$wpdb->posts} p
            WHERE p.post_status = 'publish'
              AND p.post_type IN ('post', 'page')
              AND NOT EXISTS (
                SELECT 1
                FROM {$wpdb->postmeta} m1
                WHERE m1.post_id = p.ID
                  AND m1.meta_key = '_yoast_wpseo_metadesc'
                  AND m1.meta_value <> ''
              )
              AND NOT EXISTS (
                SELECT 1
                FROM {$wpdb->postmeta} m2
                WHERE m2.post_id = p.ID
                  AND m2.meta_key IN ('_pi_seo_description', '_pi_seo_desc', '_pi_og_description')
                  AND m2.meta_value <> ''
              )
            ORDER BY p.post_date DESC
            LIMIT 2000
        ";
        $rows = $wpdb->get_results($sql);
        $count = count($rows);

        return [
            'id'               => 'missing_meta_description',
            'severity'         => self::severity_by_count($count, 1, 10),
            'title'            => 'Bài viết thiếu meta description',
            'description'      => 'Meta description giúp Google hiển thị đoạn mô tả (snippet) trong kết quả tìm kiếm. Tối ưu 120-160 ký tự.',
            'affected_count'   => $count,
            'affected_samples' => self::map_post_samples($rows),
            'fixable'          => false,
            'fix_note'         => 'Mở từng bài, điền meta description 120-160 ký tự trong khu SEO.',
        ];
    }

    /** 2. Posts/pages missing meta title (custom og:title). */
    private static function check_missing_meta_title() {
        global $wpdb;

        $sql = "
            SELECT p.ID, p.post_title, p.post_type
            FROM {$wpdb->posts} p
            WHERE p.post_status = 'publish'
              AND p.post_type IN ('post', 'page')
              AND NOT EXISTS (
                SELECT 1
                FROM {$wpdb->postmeta} m1
                WHERE m1.post_id = p.ID
                  AND m1.meta_key = '_yoast_wpseo_title'
                  AND m1.meta_value <> ''
              )
              AND NOT EXISTS (
                SELECT 1
                FROM {$wpdb->postmeta} m2
                WHERE m2.post_id = p.ID
                  AND m2.meta_key IN ('_pi_seo_title', '_pi_og_title')
                  AND m2.meta_value <> ''
              )
            ORDER BY p.post_date DESC
            LIMIT 2000
        ";
        $rows = $wpdb->get_results($sql);
        $count = count($rows);

        return [
            'id'               => 'missing_meta_title',
            'severity'         => self::severity_by_count($count, 1, 10),
            'title'            => 'Bài viết thiếu meta title (og:title)',
            'description'      => 'Meta title là tiêu đề hiển thị trên Google SERP và social share. Tối ưu 30-60 ký tự, chứa từ khóa chính.',
            'affected_count'   => $count,
            'affected_samples' => self::map_post_samples($rows),
            'fixable'          => false,
            'fix_note'         => 'Điền meta title trong phần SEO của từng bài.',
        ];
    }

    /** 3. Posts sharing the same post_title (content cannibalization risk). */
    private static function check_duplicate_titles() {
        global $wpdb;

        $sql = "
            SELECT p.ID, p.post_title, p.post_type
            FROM {$wpdb->posts} p
            INNER JOIN (
                SELECT post_title
                FROM {$wpdb->posts}
                WHERE post_status = 'publish'
                  AND post_type IN ('post', 'page')
                  AND post_title != ''
                GROUP BY post_title
                HAVING COUNT(*) > 1
            ) d ON d.post_title = p.post_title
            WHERE p.post_status = 'publish'
              AND p.post_type IN ('post', 'page')
            ORDER BY p.post_title ASC, p.post_date DESC
            LIMIT 500
        ";
        $rows = $wpdb->get_results($sql);
        $count = count($rows);

        return [
            'id'               => 'duplicate_titles',
            'severity'         => self::severity_by_count($count, 1, 4),
            'title'            => 'Tiêu đề bài trùng lặp',
            'description'      => 'Các bài viết có cùng tiêu đề sẽ cạnh tranh xếp hạng (keyword cannibalization), làm loãng tín hiệu SEO.',
            'affected_count'   => $count,
            'affected_samples' => self::map_post_samples($rows),
            'fixable'          => false,
            'fix_note'         => 'Đổi tên mỗi bài để độc nhất; cân nhắc gộp nội dung trùng bằng redirect 301.',
        ];
    }

    /** 4. Image attachments missing alt text. */
    private static function check_missing_alt_text() {
        global $wpdb;

        $sql = "
            SELECT p.ID, p.post_title, p.post_type
            FROM {$wpdb->posts} p
            LEFT JOIN {$wpdb->postmeta} m
                ON m.post_id = p.ID AND m.meta_key = '_wp_attachment_image_alt'
            WHERE p.post_type = 'attachment'
              AND p.post_mime_type LIKE 'image/%'
              AND (m.meta_value IS NULL OR m.meta_value = '')
            ORDER BY p.post_date DESC
            LIMIT 2000
        ";
        $rows = $wpdb->get_results($sql);
        $count = count($rows);

        return [
            'id'               => 'missing_alt_text',
            'severity'         => self::severity_by_count($count, 1, 20),
            'title'            => 'Ảnh thiếu alt text',
            'description'      => 'Alt text giúp trình đọc màn hình và Google Images hiểu ảnh. Bắt buộc cho WCAG A11y và SEO image.',
            'affected_count'   => $count,
            'affected_samples' => self::map_attachment_samples($rows),
            'fixable'          => true,
            'fix_note'         => 'Sửa tự động: điền alt text từ tiêu đề của attachment.',
        ];
    }

    /** 5. Posts (post type 'post') without featured image. */
    private static function check_missing_featured_image() {
        global $wpdb;

        $sql = "
            SELECT p.ID, p.post_title, p.post_type
            FROM {$wpdb->posts} p
            LEFT JOIN {$wpdb->postmeta} m
                ON m.post_id = p.ID AND m.meta_key = '_thumbnail_id'
            WHERE p.post_status = 'publish'
              AND p.post_type IN ('post', 'page')
              AND (m.meta_value IS NULL OR m.meta_value = '' OR m.meta_value = '0')
            ORDER BY p.post_date DESC
            LIMIT 2000
        ";
        $rows = $wpdb->get_results($sql);
        $count = count($rows);

        return [
            'id'               => 'missing_featured_image',
            'severity'         => self::severity_by_count($count, 1, 10),
            'title'            => 'Bài viết thiếu ảnh đại diện',
            'description'      => 'Featured image được dùng làm og:image cho social share (Facebook, Zalo). Ảnh thiếu sẽ giảm CTR trên mạng xã hội.',
            'affected_count'   => $count,
            'affected_samples' => self::map_post_samples($rows),
            'fixable'          => false,
            'fix_note'         => 'Đặt ảnh đại diện cho từng bài, hoặc dùng chức năng Auto OG Images.',
        ];
    }

    /** 6. Published posts marked noindex. */
    private static function check_noindex_posts() {
        global $wpdb;

        $sql = "
            SELECT DISTINCT p.ID, p.post_title, p.post_type
            FROM {$wpdb->posts} p
            INNER JOIN {$wpdb->postmeta} m
                ON m.post_id = p.ID
            WHERE p.post_status = 'publish'
              AND p.post_type IN ('post', 'page')
              AND (
                (m.meta_key = '_yoast_wpseo_meta-robots-noindex' AND m.meta_value = '1')
                OR (m.meta_key = '_pi_seo_noindex' AND m.meta_value = '1')
                OR (m.meta_key = '_pi_seo_noindex' AND m.meta_value = 'true')
              )
            ORDER BY p.post_date DESC
            LIMIT 500
        ";
        $rows = $wpdb->get_results($sql);
        $count = count($rows);

        return [
            'id'               => 'noindex_posts',
            'severity'         => self::severity_by_count($count, 1, 1),
            'title'            => 'Bài đã xuất bản nhưng bị noindex',
            'description'      => 'Các bài được đánh dấu noindex sẽ không xuất hiện trong Google. Kiểm tra kỹ vì có thể là cấu hình nhầm.',
            'affected_count'   => $count,
            'affected_samples' => self::map_post_samples($rows),
            'fixable'          => false,
            'fix_note'         => 'Vào từng bài, tắt tuỳ chọn "Ẩn khỏi kết quả tìm kiếm" nếu thực sự muốn index.',
        ];
    }

    /** 7. Posts with less than 300 words. */
    private static function check_short_content() {
        global $wpdb;

        $sql = "
            SELECT ID, post_title, post_type, post_content
            FROM {$wpdb->posts}
            WHERE post_status = 'publish'
              AND post_type IN ('post', 'page')
            ORDER BY post_date DESC
            LIMIT 3000
        ";
        $rows = $wpdb->get_results($sql);

        $affected = [];
        foreach ($rows as $r) {
            $text = wp_strip_all_tags((string) $r->post_content);
            // Count words: split on any whitespace, filter empty.
            $words = preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
            $wc = is_array($words) ? count($words) : 0;
            if ($wc < 300) {
                $r->word_count = $wc;
                $affected[] = $r;
            }
        }
        $count = count($affected);

        $samples = array_slice($affected, 0, self::SAMPLE_LIMIT);
        $mapped = [];
        foreach ($samples as $p) {
            $mapped[] = [
                'id'        => (int) $p->ID,
                'title'     => (string) $p->post_title,
                'edit_link' => get_edit_post_link($p->ID, 'raw'),
                'view_link' => get_permalink($p->ID),
                'extra'     => isset($p->word_count) ? (int) $p->word_count . ' từ' : '',
            ];
        }

        return [
            'id'               => 'short_content',
            'severity'         => self::severity_by_count($count, 1, 10),
            'title'            => 'Bài viết nội dung ngắn (< 300 từ)',
            'description'      => 'Google ưu tiên nội dung đầy đủ, hữu ích. Bài ngắn hơn 300 từ thường khó xếp hạng cho từ khoá cạnh tranh.',
            'affected_count'   => $count,
            'affected_samples' => $mapped,
            'fixable'          => false,
            'fix_note'         => 'Mở rộng nội dung bằng thông tin chi tiết, ví dụ, hình ảnh, bảng so sánh.',
        ];
    }

    /** 8. Posts with 0 or only "Uncategorized" category. */
    private static function check_missing_categories() {
        global $wpdb;

        // Uncategorized term_id (default category)
        $uncat_id = (int) get_option('default_category', 1);

        $sql = $wpdb->prepare("
            SELECT p.ID, p.post_title, p.post_type,
                   COUNT(tr.term_taxonomy_id) AS tcount,
                   SUM(CASE WHEN tt.term_id = %d THEN 1 ELSE 0 END) AS uncat_hits
            FROM {$wpdb->posts} p
            LEFT JOIN {$wpdb->term_relationships} tr
                ON tr.object_id = p.ID
            LEFT JOIN {$wpdb->term_taxonomy} tt
                ON tt.term_taxonomy_id = tr.term_taxonomy_id AND tt.taxonomy = 'category'
            WHERE p.post_status = 'publish'
              AND p.post_type = 'post'
            GROUP BY p.ID
            HAVING tcount = 0 OR (tcount = 1 AND uncat_hits = 1)
            ORDER BY p.post_date DESC
            LIMIT 2000
        ", $uncat_id);

        $rows = $wpdb->get_results($sql);
        $count = count($rows);

        return [
            'id'               => 'missing_categories',
            'severity'         => self::severity_by_count($count, 1, 5),
            'title'            => 'Bài viết không có chuyên mục phù hợp',
            'description'      => 'Bài thiếu chuyên mục hoặc chỉ gán "Uncategorized" sẽ làm loãng cấu trúc site, giảm internal linking và topical authority.',
            'affected_count'   => $count,
            'affected_samples' => self::map_post_samples($rows),
            'fixable'          => false,
            'fix_note'         => 'Gán chuyên mục phù hợp (khác Uncategorized) cho từng bài, tốt nhất 1-2 chuyên mục.',
        ];
    }

    /** 9. Image attachments > 500KB that aren't WebP. */
    private static function check_large_images() {
        global $wpdb;

        $sql = "
            SELECT p.ID, p.post_title, p.post_mime_type,
                   m.meta_value AS file_rel
            FROM {$wpdb->posts} p
            INNER JOIN {$wpdb->postmeta} m
                ON m.post_id = p.ID AND m.meta_key = '_wp_attached_file'
            WHERE p.post_type = 'attachment'
              AND p.post_mime_type LIKE 'image/%'
              AND p.post_mime_type != 'image/webp'
            LIMIT 2000
        ";
        $rows = $wpdb->get_results($sql);

        $uploads = wp_upload_dir();
        $base    = isset($uploads['basedir']) ? (string) $uploads['basedir'] : '';

        $affected = [];
        foreach ($rows as $r) {
            $rel = (string) $r->file_rel;
            if ($rel === '') continue;
            $path = trailingslashit($base) . $rel;
            if (!is_file($path)) continue;
            $size = @filesize($path);
            if ($size !== false && $size > 500 * 1024) {
                $r->file_size = $size;
                $affected[] = $r;
            }
        }
        // Sort largest first
        usort($affected, function ($a, $b) {
            return ($b->file_size ?? 0) <=> ($a->file_size ?? 0);
        });

        $count = count($affected);
        $samples = array_slice($affected, 0, self::SAMPLE_LIMIT);
        $mapped = [];
        foreach ($samples as $p) {
            $kb = (int) round(($p->file_size ?? 0) / 1024);
            $mapped[] = [
                'id'        => (int) $p->ID,
                'title'     => (string) $p->post_title,
                'edit_link' => get_edit_post_link($p->ID, 'raw'),
                'view_link' => wp_get_attachment_url($p->ID),
                'extra'     => number_format_i18n($kb) . ' KB · ' . (string) $p->post_mime_type,
            ];
        }

        return [
            'id'               => 'large_images',
            'severity'         => self::severity_by_count($count, 1, 5),
            'title'            => 'Ảnh dung lượng lớn không phải WebP',
            'description'      => 'Ảnh JPG/PNG > 500KB làm chậm trang, ảnh hưởng Core Web Vitals. WebP tiết kiệm 25-35% dung lượng với chất lượng tương đương.',
            'affected_count'   => $count,
            'affected_samples' => $mapped,
            'fixable'          => false,
            'fix_note'         => 'Chạy chuyển đổi WebP hàng loạt trong Pi Performance, hoặc nén thủ công bằng công cụ như Squoosh/TinyPNG.',
        ];
    }

    /** 10. Posts containing internal links to non-existent URLs (best-effort, capped at 50 posts). */
    private static function check_broken_internal_links() {
        global $wpdb;

        $sql = "
            SELECT ID, post_title, post_type, post_content
            FROM {$wpdb->posts}
            WHERE post_status = 'publish'
              AND post_type IN ('post', 'page')
              AND post_content LIKE '%href=%'
            ORDER BY post_date DESC
            LIMIT 50
        ";
        $rows = $wpdb->get_results($sql);

        $home = home_url('/');
        $host = parse_url($home, PHP_URL_HOST);
        $affected = [];

        foreach ($rows as $r) {
            $content = (string) $r->post_content;
            if (!preg_match_all('/<a[^>]+href=["\']([^"\']+)["\'][^>]*>/i', $content, $m)) {
                continue;
            }
            $broken = 0;
            $broken_urls = [];
            foreach ($m[1] as $href) {
                $href = trim($href);
                // Skip external, anchors, mailto, tel, javascript, wp-admin, wp-content
                if ($href === '' || $href[0] === '#' || stripos($href, 'mailto:') === 0 || stripos($href, 'tel:') === 0 || stripos($href, 'javascript:') === 0) {
                    continue;
                }
                // If absolute URL, must be same host
                if (preg_match('#^https?://#i', $href)) {
                    $h_host = parse_url($href, PHP_URL_HOST);
                    if ($h_host && $host && strcasecmp($h_host, $host) !== 0) {
                        continue; // external
                    }
                    $path = parse_url($href, PHP_URL_PATH) ?: '/';
                } elseif ($href[0] === '/') {
                    $path = $href;
                } else {
                    continue; // protocol-relative or weird, skip
                }
                // Skip admin/uploads/media
                if (stripos($path, '/wp-admin') === 0 || stripos($path, '/wp-content') === 0 || stripos($path, '/wp-login') === 0 || stripos($path, '/wp-json') === 0) {
                    continue;
                }
                // Strip trailing slash, query, fragment for slug match
                $slug_path = strtok($path, '?#');
                $slug = trim($slug_path, '/');
                if ($slug === '') continue; // homepage, always exists

                // Split — use last segment as slug candidate
                $parts = explode('/', $slug);
                $candidate = end($parts);

                // Query WP for matching post/page by slug
                $exists = $wpdb->get_var($wpdb->prepare(
                    "SELECT ID FROM {$wpdb->posts}
                     WHERE post_name = %s
                       AND post_status IN ('publish','future','draft','pending','private')
                       AND post_type IN ('post','page')
                     LIMIT 1",
                    $candidate
                ));
                if (!$exists) {
                    $broken++;
                    if (count($broken_urls) < 5) $broken_urls[] = $path;
                }
            }
            if ($broken > 0) {
                $r->broken_count = $broken;
                $r->broken_urls  = $broken_urls;
                $affected[] = $r;
            }
        }

        $count = count($affected);
        $samples = array_slice($affected, 0, self::SAMPLE_LIMIT);
        $mapped = [];
        foreach ($samples as $p) {
            $mapped[] = [
                'id'        => (int) $p->ID,
                'title'     => (string) $p->post_title,
                'edit_link' => get_edit_post_link($p->ID, 'raw'),
                'view_link' => get_permalink($p->ID),
                'extra'     => ($p->broken_count ?? 0) . ' liên kết hỏng: ' . implode(', ', $p->broken_urls ?? []),
            ];
        }

        return [
            'id'               => 'broken_internal_links',
            'severity'         => self::severity_by_count($count, 1, 5),
            'title'            => 'Liên kết nội bộ hỏng',
            'description'      => 'Phát hiện link nội bộ trỏ tới trang không tồn tại. Gây 404, hao phí crawl budget, giảm trust. (Quét 50 bài mới nhất có link.)',
            'affected_count'   => $count,
            'affected_samples' => $mapped,
            'fixable'          => false,
            'fix_note'         => 'Sửa link trong post_content hoặc tạo redirect 301 trong tab Redirects.',
        ];
    }

    /** 11. Published pages not linked from anywhere in post_content. */
    private static function check_orphaned_pages() {
        global $wpdb;

        $pages = $wpdb->get_results("
            SELECT ID, post_title, post_name, post_type
            FROM {$wpdb->posts}
            WHERE post_status = 'publish'
              AND post_type = 'page'
            ORDER BY post_date DESC
            LIMIT 500
        ");

        // Also exclude static front/blog page assignments (they're linked via nav/menu)
        $front = (int) get_option('page_on_front', 0);
        $blog  = (int) get_option('page_for_posts', 0);

        $affected = [];
        foreach ($pages as $p) {
            if ((int) $p->ID === $front || (int) $p->ID === $blog) continue;
            $permalink = get_permalink($p->ID);
            $slug = (string) $p->post_name;
            // Search any post_content referencing this page by slug or full URL
            $like_slug = '%/' . $wpdb->esc_like($slug) . '%';
            $like_url  = '%' . $wpdb->esc_like($permalink) . '%';
            $hit = (int) $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->posts}
                 WHERE post_status = 'publish'
                   AND post_type IN ('post', 'page')
                   AND ID != %d
                   AND (post_content LIKE %s OR post_content LIKE %s)",
                (int) $p->ID, $like_slug, $like_url
            ));
            if ($hit === 0) {
                $affected[] = $p;
            }
        }

        $count = count($affected);
        return [
            'id'               => 'orphaned_pages',
            'severity'         => self::severity_by_count($count, 1, 5),
            'title'            => 'Trang không có liên kết nội bộ (orphan)',
            'description'      => 'Pages không được bất kỳ bài/trang nào trỏ tới trong nội dung. Google khó phát hiện, giảm PageRank nội bộ.',
            'affected_count'   => $count,
            'affected_samples' => self::map_post_samples(array_slice($affected, 0, self::SAMPLE_LIMIT * 2)),
            'fixable'          => false,
            'fix_note'         => 'Thêm internal link trỏ tới trang này từ bài/trang có lưu lượng cao.',
        ];
    }

    /** 12. post_title with leading or trailing whitespace. */
    private static function check_trailing_spaces_in_title() {
        global $wpdb;

        $sql = "
            SELECT ID, post_title, post_type
            FROM {$wpdb->posts}
            WHERE post_status IN ('publish', 'draft', 'future', 'pending', 'private')
              AND post_type IN ('post', 'page')
              AND (post_title LIKE ' %' OR post_title LIKE '% ' OR post_title REGEXP '^[[:space:]]|[[:space:]]$')
            LIMIT 500
        ";
        $rows = $wpdb->get_results($sql);
        $count = count($rows);

        return [
            'id'               => 'trailing_spaces_in_title',
            'severity'         => self::severity_by_count($count, 1, 5),
            'title'            => 'Tiêu đề chứa khoảng trắng thừa',
            'description'      => 'Leading/trailing spaces trong post_title gây tiêu đề hiển thị sai trong kết quả tìm kiếm và breadcrumb.',
            'affected_count'   => $count,
            'affected_samples' => self::map_post_samples($rows),
            'fixable'          => true,
            'fix_note'         => 'Sửa tự động: trim tất cả post_title.',
        ];
    }

    /* ═══════════════════════════════════════════════════════════════════════
       AUTO-FIX HANDLERS
       ═══════════════════════════════════════════════════════════════════════ */

    /** Auto-trim all affected post_titles. */
    private static function fix_trailing_spaces_in_title() {
        global $wpdb;

        $sql = "
            SELECT ID, post_title
            FROM {$wpdb->posts}
            WHERE post_status IN ('publish', 'draft', 'future', 'pending', 'private')
              AND post_type IN ('post', 'page')
              AND (post_title LIKE ' %' OR post_title LIKE '% ' OR post_title REGEXP '^[[:space:]]|[[:space:]]$')
            LIMIT 500
        ";
        $rows = $wpdb->get_results($sql);

        $fixed = 0;
        foreach ($rows as $r) {
            $trimmed = trim((string) $r->post_title);
            if ($trimmed !== '' && $trimmed !== $r->post_title) {
                $ok = $wpdb->update(
                    $wpdb->posts,
                    ['post_title' => $trimmed],
                    ['ID' => (int) $r->ID],
                    ['%s'],
                    ['%d']
                );
                if ($ok !== false) {
                    clean_post_cache((int) $r->ID);
                    $fixed++;
                }
            }
        }

        return [
            'fixed'   => $fixed,
            'message' => sprintf('Đã trim %d tiêu đề.', $fixed),
        ];
    }

    /** Auto-populate alt text from attachment post_title. */
    private static function fix_missing_alt_text() {
        global $wpdb;

        $sql = "
            SELECT p.ID, p.post_title
            FROM {$wpdb->posts} p
            LEFT JOIN {$wpdb->postmeta} m
                ON m.post_id = p.ID AND m.meta_key = '_wp_attachment_image_alt'
            WHERE p.post_type = 'attachment'
              AND p.post_mime_type LIKE 'image/%'
              AND (m.meta_value IS NULL OR m.meta_value = '')
            LIMIT 1000
        ";
        $rows = $wpdb->get_results($sql);

        $fixed = 0;
        foreach ($rows as $r) {
            $title = trim((string) $r->post_title);
            if ($title === '') continue;
            // Strip file extension and prettify underscores/hyphens
            $alt = preg_replace('/\.(jpe?g|png|gif|webp|avif|bmp|svg)$/i', '', $title);
            $alt = str_replace(['-', '_'], ' ', $alt);
            $alt = trim(preg_replace('/\s+/u', ' ', $alt));
            if ($alt === '') continue;

            $ok = update_post_meta((int) $r->ID, '_wp_attachment_image_alt', $alt);
            if ($ok) $fixed++;
        }

        return [
            'fixed'   => $fixed,
            'message' => sprintf('Đã điền alt text cho %d ảnh.', $fixed),
        ];
    }

    /* ═══════════════════════════════════════════════════════════════════════
       HELPERS
       ═══════════════════════════════════════════════════════════════════════ */

    /**
     * Compute severity from affected count.
     * 0              -> ok
     * 1..warn_limit  -> warn
     * >warn_limit    -> error
     */
    private static function severity_by_count($count, $warn_min = 1, $error_min = 10) {
        $count = (int) $count;
        if ($count === 0) return 'ok';
        if ($count < $error_min) return 'warn';
        return 'error';
    }

    private static function map_post_samples($rows) {
        $rows = array_slice((array) $rows, 0, self::SAMPLE_LIMIT);
        $out = [];
        foreach ($rows as $r) {
            $id = (int) ($r->ID ?? 0);
            if (!$id) continue;
            $out[] = [
                'id'        => $id,
                'title'     => (string) ($r->post_title ?? '(không tiêu đề)'),
                'edit_link' => get_edit_post_link($id, 'raw'),
                'view_link' => get_permalink($id),
                'type'      => (string) ($r->post_type ?? ''),
            ];
        }
        return $out;
    }

    private static function map_attachment_samples($rows) {
        $rows = array_slice((array) $rows, 0, self::SAMPLE_LIMIT);
        $out = [];
        foreach ($rows as $r) {
            $id = (int) ($r->ID ?? 0);
            if (!$id) continue;
            $out[] = [
                'id'        => $id,
                'title'     => (string) ($r->post_title ?? '(không tiêu đề)'),
                'edit_link' => get_edit_post_link($id, 'raw'),
                'view_link' => wp_get_attachment_url($id),
                'type'      => 'attachment',
            ];
        }
        return $out;
    }

    /** Tally errors/warnings/oks for summary. */
    private static function summarize($checks) {
        $errors = 0; $warns = 0; $oks = 0;
        foreach ($checks as $c) {
            $sev = (string) ($c['severity'] ?? 'ok');
            if ($sev === 'error')      $errors++;
            elseif ($sev === 'warn')   $warns++;
            else                       $oks++;
        }
        return [
            'total'    => count($checks),
            'errors'   => $errors,
            'warnings' => $warns,
            'ok'       => $oks,
        ];
    }
}
