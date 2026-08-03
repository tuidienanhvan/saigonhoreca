<?php
/**
 * Auto-route project-index pillar pages → page-templates/page-project-<slug>.php
 * (slug thật lấy từ inc/config/routes.php `sgh_bilingual_slug_map()`).
 *
 * Cho phép test 12 pillar pages NGAY mà không cần tạo Page trong wp-admin.
 * Pattern y hệt static-mirror.php nhưng serve NEW BEM templates.
 *
 * Khi user tạo Page thực sự với template "Project Pillar — X" và parent
 * đúng section slug, WP routing native sẽ xử lý — pillar-routes.php tự bypass.
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) exit;

if (!function_exists('sgh_pillar_route_serve')) {

    function sgh_pillar_route_serve() {
        if (is_admin() || (defined('DOING_AJAX') && DOING_AJAX) || (defined('REST_REQUEST') && REST_REQUEST)) {
            return;
        }

        $uri = isset($_SERVER['REQUEST_URI']) ? strtok($_SERVER['REQUEST_URI'], '?') : '';
        if (!$uri) return;

        // ── Cache Flush Route ──
        if ($uri === '/flush-cache' || isset($_GET['flush-theme-cache'])) {
            $cache_dir = wp_normalize_path(WP_CONTENT_DIR . '/cache/sgh-static');
            $files_deleted = 0;
            if (is_dir($cache_dir)) {
                try {
                    $it = new RecursiveDirectoryIterator($cache_dir, RecursiveDirectoryIterator::SKIP_DOTS);
                    $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
                    foreach($files as $file) {
                        if ($file->isDir()){
                            @rmdir($file->getPathname());
                        } else {
                            if (@unlink($file->getPathname())) {
                                $files_deleted++;
                            }
                        }
                    }
                    @rmdir($cache_dir);
                } catch (Exception $e) {
                    // Ignore errors during directory traversal
                }
            }
            
            // Also flush home cache file if exists
            $home_cache_file = wp_normalize_path(WP_CONTENT_DIR . '/cache/sgh-home-cache.html');
            if (file_exists($home_cache_file)) {
                if (@unlink($home_cache_file)) {
                    $files_deleted++;
                }
            }

            // Cho phép plugin khác (vd pi-chatbot) nghe cùng 1 điểm bấm này để dọn
            // cache riêng của họ — không cần route này biết chi tiết plugin nào
            // đang lắng nghe (loose coupling qua action hook, không hardcode tên
            // plugin/class cụ thể vào file theme).
            do_action('sgh_after_flush_cache');

            wp_die(
                "<h3>Theme Static Cache Flushed Successfully!</h3><p>Deleted $files_deleted cached files/folders.</p><p><a href='" . esc_url(home_url('/')) . "'>&larr; Back to Home</a></p>",
                "Cache Flushed",
                array('response' => 200)
            );
            exit;
        }

        // Match canonical VI/EN project-index prefix (see routes.php slug map)
        // AND legacy/root alias /<slug>/.
        $proj_map  = function_exists('sgh_bilingual_slug_map') ? sgh_bilingual_slug_map() : [];
        $proj_vi   = $proj_map['projects_index'][0] ?? 'project';
        $proj_en   = $proj_map['projects_index'][1] ?? 'projects';
        $is_canonical_path = preg_match('#^/(?:' . preg_quote($proj_vi, '#') . '|' . preg_quote($proj_en, '#') . ')/([a-z0-9\-]+)/?$#i', $uri, $m);
        $is_root_alias     = false;

        if (!$is_canonical_path) {
            $is_root_alias = preg_match('#^/([a-z0-9\-]+)/?$#i', $uri, $m);
        }

        if (!$is_canonical_path && !$is_root_alias) return;
        $slug = strtolower($m[1]);
        // Language-aware project prefix (see routes.php slug map, cùng nguồn với match ở trên).
        $proj = function_exists('sgh_slug') ? sgh_slug('projects_index') : $proj_vi;

        // [GỠ 2026-08-03] Nhánh 301 slug cũ -> slug chuẩn.
        // Chủ site chốt: không giữ fallback, mỗi dự án đúng một URL. Slug cũ
        // trả 404 thật. Bảng ánh xạ ở inc/config/routes.php cũng đã gỡ.

        // Bypass định tuyến ảo nếu đã có Post thật thuộc Custom Post Type project
        $existing_project = get_posts([
            'name'             => $slug,
            'post_type'        => 'project',
            'post_status'      => 'publish',
            'numberposts'      => 1,
            'no_found_rows'    => true,
            'suppress_filters' => true,
        ]);
        if (!empty($existing_project)) {
            // Post thật đã tồn tại, nhưng CPT 'project' chỉ có rewrite rule
            // cho project-index/<slug>/ (xem cpt-registration.php). Nếu request là
            // root alias (/<slug>/, không có prefix project-index), WP native routing sẽ
            // KHÔNG khớp rule nào và có thể rơi vào post/page cũ còn sót lại
            // từ site cũ (import XML) — phải tự 301 sang permalink chuẩn.
            if ($is_root_alias) {
                wp_safe_redirect(get_permalink($existing_project[0]), 301);
                exit;
            }
            return;
        }

        // The Brix native pillar is not rebuilt to production parity yet.
        // Let static-mirror.php serve the freshly crawled production snapshot.
        if ($slug === 'the-brix') return;

        // Resolve template: prefer new single-project/<slug>.php (post-migration),
        // fall back to legacy page-templates/page-project-<slug>.php for any
        // project not yet migrated. Projects moved out of page-templates/ on
        // 2026-05-29 — virtual route must follow them to single-project/.
        $single_path = get_template_directory() . '/single-project/' . $slug . '.php';
        $legacy_path = get_template_directory() . '/page-templates/page-project-' . $slug . '.php';
        if (file_exists($single_path)) {
            $tpl_path = $single_path;
        } elseif (file_exists($legacy_path)) {
            $tpl_path = $legacy_path;
        } else {
            return;
        }

        $canonical_url = home_url('/' . $proj . '/' . $slug . '/');

        // If a real WP Page exists under the project-index prefix, let WP render the
        // canonical path natively, but still support root alias redirects.
        $existing = get_page_by_path($proj . '/' . $slug);
        if ($existing) {
            if ($is_root_alias) {
                wp_safe_redirect(get_permalink($existing), 301);
                exit;
            }
            return;
        }

        $request_host  = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : parse_url(home_url('/'), PHP_URL_HOST);
        $request_url   = (is_ssl() ? 'https://' : 'http://') . $request_host . $uri;
        if (untrailingslashit($request_url) !== untrailingslashit($canonical_url)) {
            wp_safe_redirect($canonical_url, 301);
            exit;
        }

        // Fake the queried object so functions like get_the_title() work.
        // CRITICAL: ID must be non-zero, else WP core `get_metadata_raw()`
        // returns false BEFORE firing the `get_post_metadata` filter — meaning
        // our template-slug filter would never run. Use a deterministic, high
        // virtual ID per slug (>= 9_000_000) guaranteed not to collide with
        // real posts.
        global $wp_query, $post;
        $fake_id = 9000000 + (crc32($slug) % 1000000);
        $fake_post = (object) [
            'ID'             => $fake_id,
            'post_title'     => sgh_pillar_label_from_slug($slug),
            'post_name'      => $slug,
            'post_type'      => 'page',
            'post_status'    => 'publish',
            'post_content'   => '',
            'post_date'      => current_time('mysql'),
            'post_date_gmt' => current_time('mysql', 1),
            'post_modified' => current_time('mysql'),
            'post_modified_gmt' => current_time('mysql', 1),
            'post_author'    => 0,
            'post_parent'    => 0,
            'menu_order'     => 0,
            'comment_status' => 'closed',
            'ping_status'    => 'closed',
            'comment_count'  => 0,
            'post_excerpt'   => '',
            'post_password'  => '',
            'to_ping'        => '',
            'pinged'         => '',
            'post_content_filtered' => '',
            'guid'           => $canonical_url,
            'post_mime_type' => '',
            'filter'         => 'raw',
        ];
        $post = new WP_Post($fake_post);
        setup_postdata($post);

        $wp_query->is_page     = true;
        $wp_query->is_singular = true;
        $wp_query->is_single   = false;
        $wp_query->is_404      = false;
        $wp_query->is_home     = false;
        $wp_query->is_archive  = false;
        $wp_query->is_search   = false;
        $wp_query->queried_object    = $post;
        $wp_query->queried_object_id = $post->ID;
        $wp_query->post              = $post;
        $wp_query->posts             = [ $post ];
        $wp_query->post_count        = 1;
        $wp_query->found_posts       = 1;
        $wp_query->query             = [ 'pagename' => $proj . '/' . $slug ];
        $wp_query->query_vars['pagename'] = $proj . '/' . $slug;
        $wp_query->query_vars['page_id']  = $post->ID;
        status_header(200);

        // Root fix: short-circuit get_post_meta(0, '_wp_page_template') so that
        // WP core `get_page_template_slug()` returns our slug natively. This
        // makes is_page_template(), body_class, and template hierarchy all work
        // without any custom detection.
        $tpl_slug = 'page-templates/page-project-' . $slug . '.php';
        add_filter('get_post_metadata', static function($value, $object_id, $meta_key) use ($tpl_slug, $fake_id) {
            if ((int) $object_id === $fake_id && $meta_key === '_wp_page_template') {
                return $tpl_slug;
            }
            return $value;
        }, 10, 3);

        // WP core shortlink generation calls get_post($fake_id). Because this
        // route intentionally has no database row, preempt it with the real URL.
        add_filter('pre_get_shortlink', static function($return, $id, $context) use ($canonical_url) {
            if ($context === 'query') {
                return $canonical_url;
            }
            return $return;
        }, 10, 3);

        // Keep all URL builders canonical. `get_permalink()` for a fake top-level
        // page would otherwise become /<slug>/ instead of the canonical project-index path.
        add_filter('page_link', static function($link, $post_id) use ($fake_id, $canonical_url) {
            return ((int) $post_id === $fake_id) ? $canonical_url : $link;
        }, 10, 2);

        add_filter('post_type_link', static function($link, $post) use ($fake_id, $canonical_url) {
            return ($post instanceof WP_Post && (int) $post->ID === $fake_id) ? $canonical_url : $link;
        }, 10, 2);

        // Render
        require $tpl_path;
        exit;
    }

    // Priority -10: chạy TRƯỚC static-mirror (priority 0) để pillar template
    // win race nếu trùng project-index/<slug>/ slug.
    add_action('template_redirect', 'sgh_pillar_route_serve', -10);
}

if (!function_exists('sgh_pillar_label_from_slug')) {
    function sgh_pillar_label_from_slug($slug) {
        // Map known slugs → display labels (from project-data JSON if available)
        $data_file = get_template_directory() . '/scripts/project-data/' . $slug . '.json';
        if (is_readable($data_file)) {
            $j = @json_decode(@file_get_contents($data_file), true);
            if (is_array($j) && !empty($j['title'])) return $j['title'];
        }
        return ucwords(str_replace('-', ' ', $slug));
    }
}

// Filter single template để WordPress tự động load từ thư mục single-project/ hoặc page-templates/
add_filter('single_template', static function($template) {
    $post = get_queried_object();

    if ($post instanceof WP_Post && $post->post_type === 'project') {
        $slug = $post->post_name;
        
        // 1. Kiểm tra dự án mới ở single-project/
        $custom_template = get_template_directory() . '/single-project/' . $slug . '.php';
        if (file_exists($custom_template)) {
            return $custom_template;
        }
        
        // 2. Tương thích ngược: Kiểm tra dự án cũ ở page-templates/
        $legacy_template = get_template_directory() . '/page-templates/page-project-' . $slug . '.php';
        if (file_exists($legacy_template)) {
            return $legacy_template;
        }
    }
    return $template;
});

// Thêm class body tương thích ngược cho Custom Post Type project single pages
add_filter('body_class', static function($classes) {
    global $post;
    if (is_singular('project')) {
        $slug = $post->post_name;
        $classes[] = 'page-template-page-project-' . $slug;
        $classes[] = 'page-template-page-project';
    }
    return $classes;
});
