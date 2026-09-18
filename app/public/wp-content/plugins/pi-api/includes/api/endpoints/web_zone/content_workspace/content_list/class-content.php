<?php
/**
 * Pi API Content Endpoints
 */

if (!defined('ABSPATH')) exit;

class Pi_Api_Content {

    public static function register_routes($ns, $admin) {
        register_rest_route($ns, '/content', [
            [
                'methods'  => 'GET',
                'callback' => [self::class, 'get_list'],
                'permission_callback' => $admin
            ],
            [
                'methods'  => 'POST',
                'callback' => [self::class, 'create'],
                'permission_callback' => $admin
            ]
        ]);
        register_rest_route($ns, '/content/(?P<id>\d+)', [
            ['methods' => 'GET',    'callback' => [self::class, 'get_single'], 'permission_callback' => $admin],
            ['methods' => 'POST',   'callback' => [self::class, 'update'],     'permission_callback' => $admin],
            ['methods' => 'DELETE', 'callback' => [self::class, 'delete'],     'permission_callback' => $admin],
        ]);
        register_rest_route($ns, '/content/bulk', [
            'methods'  => 'POST',
            'callback' => [self::class, 'bulk_action'],
            'permission_callback' => $admin
        ]);
        register_rest_route($ns, '/content/bulk-update', [
            'methods'  => 'POST',
            'callback' => [self::class, 'bulk_update'],
            'permission_callback' => $admin
        ]);
        register_rest_route($ns, '/content/seo-table', [
            'methods'  => 'GET',
            'callback' => [self::class, 'get_seo_table'],
            'permission_callback' => $admin
        ]);
        register_rest_route($ns, '/content/(?P<id>\d+)/clone', [
            'methods'  => 'POST',
            'callback' => [self::class, 'clone'],
            'permission_callback' => $admin
        ]);
        register_rest_route($ns, '/content/(?P<id>\d+)/images', [
            'methods'  => 'GET',
            'callback' => [self::class, 'get_images'],
            'permission_callback' => $admin
        ]);
        register_rest_route($ns, '/content/(?P<id>\d+)/images/webp', [
            'methods'  => 'POST',
            'callback' => [self::class, 'convert_webp'],
            'permission_callback' => $admin
        ]);
        register_rest_route($ns, '/content/(?P<id>\d+)/versions', [
            ['methods' => 'GET',  'callback' => [self::class, 'get_versions'],  'permission_callback' => $admin],
            ['methods' => 'POST', 'callback' => [self::class, 'save_version'], 'permission_callback' => $admin],
        ]);
        register_rest_route($ns, '/content/(?P<id>\d+)/versions/restore', [
            'methods'  => 'POST',
            'callback' => [self::class, 'restore_version'],
            'permission_callback' => $admin
        ]);
    }

    /**
     * Map logical meta keys to actual DB keys (supports multi-theme mapping)
     */
    private static function get_meta_key($key) {
        $mapping = apply_filters('pi_api_meta_mapping', [
            'seo_title'      => '_pi_seo_title',
            'seo_desc'       => '_pi_seo_description',
            'seo_keyword'    => '_pi_seo_focus_keyword',
            'seo_noindex'    => '_pi_seo_noindex',
            'custom_author'  => '_pi_custom_author',
            'custom_date'    => '_pi_custom_date',
            'post_versions'  => '_pi_versions',
            'reading_time'   => '_pi_reading_time',
            'word_count'     => '_pi_word_count',
            'char_count'     => '_pi_char_count',
        ]);
        return $mapping[$key] ?? $key;
    }

    private static function get_meta_aliases($key) {
        $aliases = [
            'seo_title'   => ['_pi_seo_title', '_pi_og_title'],
            'seo_desc'    => ['_pi_seo_description', '_pi_seo_desc', '_pi_og_description'],
            'seo_keyword' => ['_pi_seo_focus_keyword'],
            'seo_noindex' => ['_pi_seo_noindex'],
        ];

        return $aliases[$key] ?? [self::get_meta_key($key)];
    }

    private static function get_meta_value($post_id, $key, $default = '') {
        foreach (self::get_meta_aliases($key) as $meta_key) {
            $value = get_post_meta($post_id, $meta_key, true);
            if ($value !== '' && $value !== null) {
                return $value;
            }
        }

        return $default;
    }

    /**
     * Get thumbnail URL with filesystem fallback.
     *
     * WP metadata có thể ghi nhận intermediate sizes (medium, large...)
     * nhưng file thực tế trên disk không tồn tại (do import chỉ copy file gốc,
     * hoặc regenerate bị lỗi). Khi đó browser request → 404 → ảnh trắng.
     *
     * Helper này kiểm tra file trên disk. Nếu thiếu → fallback qua full size,
     * nếu full cũng thiếu → null.
     */
    /**
     * Normalize URL protocol and host for local environment & SSL
     */
    private static function normalize_url($url) {
        if (!$url || !is_string($url)) return null;
        if (is_ssl() || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) {
            $url = str_replace('http://', 'https://', $url);
        }
        return $url;
    }

    /**
     * Get thumbnail URL with filesystem fallback + meta/attachment fallbacks.
     */
    private static function get_safe_thumbnail_url($post_id, $size = 'medium') {
        $thumb_id = get_post_thumbnail_id($post_id);

        // Fallback 1: Check ACF / Custom theme post meta keys
        if (!$thumb_id) {
            $alt_keys = ['_thumbnail_id', 'project_image', 'project_banner', 'hero_image', '_pi_og_image', '_thumbnail_url'];
            foreach ($alt_keys as $key) {
                $meta_val = get_post_meta($post_id, $key, true);
                if (is_numeric($meta_val) && (int)$meta_val > 0) {
                    $thumb_id = (int)$meta_val;
                    break;
                } elseif (is_string($meta_val) && str_starts_with($meta_val, 'http')) {
                    return self::normalize_url($meta_val);
                }
            }
        }

        // Fallback 2: Check first attached image in post gallery
        if (!$thumb_id) {
            $images = get_attached_media('image', $post_id);
            if (!empty($images)) {
                $first = reset($images);
                $thumb_id = $first->ID;
            }
        }

        if (!$thumb_id) return null;

        // Auto-sanitize _wp_attached_file in DB if it contains an absolute Windows/Unix path
        $raw_attached = get_post_meta($thumb_id, '_wp_attached_file', true);
        if ($raw_attached && preg_match('#wp-content/uploads/(.+)$#i', $raw_attached, $match)) {
            $clean_rel = $match[1];
            if ($clean_rel !== $raw_attached) {
                update_post_meta($thumb_id, '_wp_attached_file', $clean_rel);
            }
        }

        // 1. Kiểm tra xem file kích thước yêu cầu (vd: medium) có tồn tại thực sự trên ổ đĩa không
        $meta = wp_get_attachment_metadata($thumb_id);
        if (!empty($meta['sizes'][$size]['file']) && !empty($meta['file'])) {
            $basedir = wp_get_upload_dir()['basedir'];
            $dir = dirname($meta['file']);
            $size_path = $basedir . ($dir !== '.' ? '/' . $dir : '') . '/' . $meta['sizes'][$size]['file'];
            if (file_exists($size_path)) {
                $url = get_the_post_thumbnail_url($post_id, $size) ?: wp_get_attachment_image_url($thumb_id, $size);
                return self::normalize_url($url);
            }
        }

        // 2. Fallback: kiểm tra file ảnh gốc (attached file)
        $attached_file = get_attached_file($thumb_id);
        if ($attached_file && file_exists($attached_file)) {
            $url = wp_get_attachment_url($thumb_id);
            return self::normalize_url($url);
        }

        return null;
    }

    /**
     * Detect the actual PHP template file WordPress uses for a given page or CPT item.
     */
    private static function detect_page_template($page_id, $post_type = 'page') {
        // Trong MỘT request kết quả không đổi, mà get_list gọi hàm này tới 2 lần cho mỗi trang
        // (một lần ở nhánh đếm từ, một lần khi dựng `template`), mỗi lần tới ~5 file_exists.
        static $memo = [];
        $k = $post_type . ':' . $page_id;
        if (!array_key_exists($k, $memo)) {
            $memo[$k] = self::detect_page_template_uncached($page_id, $post_type);
        }
        return $memo[$k];
    }

    private static function detect_page_template_uncached($page_id, $post_type = 'page') {
        $custom = get_page_template_slug($page_id);
        if ($custom) {
            return $custom;
        }

        $theme_dir = get_template_directory();
        $slug = get_post_field('post_name', $page_id);

        if ($post_type === 'project') {
            if ($slug) {
                if (file_exists($theme_dir . "/single-project/{$slug}.php")) {
                    return "single-project/{$slug}.php";
                }
                if (file_exists($theme_dir . "/single-project/{$slug}/index.php")) {
                    return "single-project/{$slug}/index.php";
                }
                if (file_exists($theme_dir . "/single-project-{$slug}.php")) {
                    return "single-project-{$slug}.php";
                }
                if (file_exists($theme_dir . "/project-{$slug}.php")) {
                    return "project-{$slug}.php";
                }
            }
            if (file_exists($theme_dir . '/single-project.php')) {
                return 'single-project.php';
            }
            return 'single-project.php';
        }

        // 2. Static front page (Settings > Reading)
        $front_page_id = (int) get_option('page_on_front');
        if ($front_page_id && $front_page_id === $page_id) {
            if (file_exists($theme_dir . '/front-page.php')) {
                return 'front-page.php';
            }
        }

        // 3. Blog posts page (Settings > Reading)
        $posts_page_id = (int) get_option('page_for_posts');
        if ($posts_page_id && $posts_page_id === $page_id) {
            if (file_exists($theme_dir . '/home.php')) {
                return 'home.php';
            }
            return 'archive.php';
        }

        // 4. CPT archive override — page slug matches a CPT's rewrite slug
        static $cpts = null;
        if ($cpts === null) $cpts = get_post_types(['public' => true, '_builtin' => false], 'objects');
        foreach ($cpts as $cpt) {
            $archive_slug = $cpt->has_archive;
            if ($archive_slug === true) {
                $archive_slug = $cpt->rewrite['slug'] ?? $cpt->name;
            }
            if ($archive_slug && $slug === $archive_slug) {
                $tpl = "archive-{$cpt->name}.php";
                if (file_exists($theme_dir . '/' . $tpl)) {
                    return $tpl;
                }
                return 'archive.php';
            }
        }

        // 5. Check page-{slug}.php
        if ($slug && file_exists($theme_dir . "/page-{$slug}.php")) {
            return "page-{$slug}.php";
        }

        return 'page.php';
    }

    /**
     * Helper to compute total aggregate word count for CPT archive shell pages.
     */
    private static function get_aggregate_cpt_words($cpt) {
        global $wpdb;
        static $cache = [];
        if (isset($cache[$cpt])) return $cache[$cpt];

        // Kéo toàn bộ post_content của cả CPT về PHP chỉ để đếm từ — nặng và chạy lại ở mọi
        // request có trang archive shell. Cache transient 1 giờ, khoá theo (số bài, lần sửa gần
        // nhất) nên thêm/sửa/xoá bài tự làm khoá đổi. Hai số đó lấy bằng MỘT truy vấn nhẹ, không
        // đọc thân bài. Tiền tố pi_api_ để uninstall.php dọn được.
        $sig = $wpdb->get_row($wpdb->prepare(
            "SELECT COUNT(*) AS n, MAX(post_modified_gmt) AS m FROM {$wpdb->posts} WHERE post_type = %s AND post_status = 'publish'",
            $cpt
        ));
        $khoa = 'pi_api_cpt_words_' . $cpt . '_' . md5(($sig->n ?? 0) . '|' . ($sig->m ?? ''));
        $da_luu = get_transient($khoa);
        if ($da_luu !== false) {
            return $cache[$cpt] = (int) $da_luu;
        }

        $posts = $wpdb->get_results($wpdb->prepare("
            SELECT post_content 
            FROM {$wpdb->posts} 
            WHERE post_type = %s AND post_status = 'publish'
        ", $cpt));

        $total_words = 0;
        foreach ($posts as $p) {
            $clean = strip_tags($p->post_content);
            if (!empty($clean)) {
                $total_words += count(preg_split('/\s+/u', $clean, -1, PREG_SPLIT_NO_EMPTY));
            }
        }
        $cache[$cpt] = $total_words;
        set_transient($khoa, $total_words, HOUR_IN_SECONDS);
        return $total_words;
    }

    public static function get_list(\WP_REST_Request $request) {
        $type     = $request->get_param('type') ?: 'post';
        $status   = $request->get_param('status') ?: 'any';
        $search   = $request->get_param('search') ?: '';
        $page     = max(1, (int) ($request->get_param('page') ?: 1));
        $per_page = min(200, max(1, (int) ($request->get_param('per_page') ?: 25)));
        $orderby  = $request->get_param('orderby') ?: 'date';
        $order    = strtoupper($request->get_param('order') ?: 'DESC') === 'ASC' ? 'ASC' : 'DESC';

        $args = [
            'post_type'      => $type,
            'post_status'    => $status,
            'posts_per_page' => $per_page,
            'paged'          => $page,
            'orderby'        => in_array($orderby, ['date', 'title', 'modified'], true) ? $orderby : 'date',
            'order'          => $order,
        ];

        $category = (int) $request->get_param('category');
        if ($category > 0) {
            // Dùng đúng taxonomy theo post type — 'cat' chỉ hoạt động cho taxonomy 'category'
            $tax_map = [
                'project' => 'project_category',
                'product' => 'product_category',
            ];
            $filter_taxonomy = $tax_map[$type] ?? 'category';

            if ($filter_taxonomy === 'category') {
                $args['cat'] = $category;
            } else {
                $args['tax_query'] = [[
                    'taxonomy' => $filter_taxonomy,
                    'field'    => 'term_id',
                    'terms'    => [$category],
                ]];
            }
        }

        $date_after = $request->get_param('after');
        $date_before = $request->get_param('before');
        if ($date_after || $date_before) {
            $args['date_query'] = [
                'inclusive' => true,
            ];
            if ($date_after)  $args['date_query']['after']  = $date_after;
            if ($date_before) $args['date_query']['before'] = $date_before;
        }

        // Advanced Search: Search by ID (#123)
        if (preg_match('/^#(\d+)$/', $search, $m)) {
            $args['p'] = (int) $m[1];
        } elseif (ctype_digit($search) && strlen($search) <= 8) {
            $args['p'] = (int) $search;
        } elseif (!empty($search)) {
            $args['s'] = $search;
        }

        // Parent filter (for hierarchical post types like page)
        $parent_filter = $request->get_param('parent');
        if ($parent_filter === 'root') {
            $args['post_parent'] = 0;
        } elseif ($parent_filter === 'child') {
            $args['post_parent__not_in'] = [0];
        } elseif (is_numeric($parent_filter) && (int)$parent_filter > 0) {
            $args['post_parent'] = (int) $parent_filter;
        }

        $query = new \WP_Query($args);
        $items = [];

        // Collect all IDs to pre-fetch meta and terms in bulk
        $post_ids = wp_list_pluck($query->posts, 'ID');
        $children_counts = [];
        if ($post_ids) {
            update_post_thumbnail_cache($query);
            update_object_term_cache($post_ids, $type);

            // Nạp sẵn cả tác giả và bài cha — trước đây mỗi dòng tự gọi get_the_author_meta /
            // get_the_title($parent_id) nên mỗi tác giả / bài cha mới là một truy vấn riêng.
            cache_users(array_values(array_unique(array_filter(wp_list_pluck($query->posts, 'post_author')))));
            $cha = array_values(array_diff(array_unique(array_filter(array_map('intval', wp_list_pluck($query->posts, 'post_parent')))), $post_ids));
            if ($cha && function_exists('_prime_post_caches')) _prime_post_caches($cha, false, false);

            // Đếm con của MỌI trang bằng một truy vấn GROUP BY thay vì một COUNT(*) mỗi dòng.
            if ($type === 'page') {
                global $wpdb;
                $in = implode(',', array_map('intval', $post_ids));   // toàn số nguyên -> an toàn để nối
                foreach ((array) $wpdb->get_results($wpdb->prepare(
                    "SELECT post_parent, COUNT(*) AS c FROM {$wpdb->posts} WHERE post_type = %s AND post_status != 'trash' AND post_parent IN ($in) GROUP BY post_parent",
                    $type
                )) as $row) {
                    $children_counts[(int) $row->post_parent] = (int) $row->c;
                }
            }
        }

        foreach ($query->posts as $p) {
            // Ưu tiên lấy từ meta đã được lưu sẵn
            $word_count = (int) get_post_meta($p->ID, self::get_meta_key('word_count'), true);
            if (!$word_count) {
                $clean_content = strip_tags($p->post_content);
                $word_count = !empty($clean_content) ? count(preg_split('/\s+/u', $clean_content, -1, PREG_SPLIT_NO_EMPTY)) : 0;

                // Fallback cho các Trang Archive Shell (Dự Án, Sản Phẩm, Tin Tức): tính tổng quy mô từ các mục con
                if ($word_count === 0 && $type === 'page') {
                    $slug = $p->post_name;
                    $tpl  = self::detect_page_template($p->ID, 'page');
                    if ($slug === 'du-an' || str_contains($tpl, 'archive-project')) {
                        $word_count = self::get_aggregate_cpt_words('project');
                    } elseif ($slug === 'san-pham' || str_contains($tpl, 'archive-product')) {
                        $word_count = self::get_aggregate_cpt_words('product');
                    } elseif ($slug === 'tin-tuc' || $p->ID === (int) get_option('page_for_posts')) {
                        $word_count = self::get_aggregate_cpt_words('post');
                    }
                }
            }

            // Parent/Child info
            $parent_id = (int) $p->post_parent;
            $parent_title = $parent_id > 0 ? html_entity_decode(get_the_title($parent_id), ENT_QUOTES, 'UTF-8') : null;
            
            $children_count = $children_counts[(int) $p->ID] ?? 0;
            
            // Get categories — đúng taxonomy theo post type
            // get_the_category() chỉ query taxonomy 'category' (post mặc định).
            // CPT project dùng 'project_category', product dùng 'product_category'.
            $tax_map = [
                'project' => 'project_category',
                'product' => 'product_category',
            ];
            $taxonomy = $tax_map[$type] ?? 'category';
            $cats = get_the_terms($p->ID, $taxonomy);
            $formatted_cats = [];
            if (!empty($cats) && !is_wp_error($cats)) {
                foreach ($cats as $cat) {
                    $formatted_cats[] = [
                        'id'   => $cat->term_id,
                        'name' => $cat->name,
                        'slug' => $cat->slug
                    ];
                }
            }

            $items[] = [
                'id'             => $p->ID,
                'title'          => $p->post_title,
                'slug'           => $p->post_name,
                'url'            => get_permalink($p->ID),
                'status'         => $p->post_status,
                'date'           => get_the_date('Y-m-d H:i', $p),
                'modified'       => get_the_modified_date('Y-m-d H:i', $p),
                'author'         => get_the_author_meta('display_name', $p->post_author),
                'thumbnail'      => self::get_safe_thumbnail_url($p->ID, 'medium'),
                'categories'     => $formatted_cats,
                'word_count'     => $word_count,
                'comment_count'  => (int) $p->comment_count,
                'parent_id'      => $parent_id,
                'parent_title'   => $parent_title,
                'children_count' => $children_count,
                'menu_order'     => (int) $p->menu_order,
                'template'       => in_array($type, ['page', 'project'], true) ? self::detect_page_template($p->ID, $type) : null,
            ];
        }

        return new \WP_REST_Response([
            'items' => $items,
            'total' => (int) $query->found_posts,
            'pages' => (int) $query->max_num_pages,
        ], 200);
    }


    /**
     * SEO: post_content không được chứa H1 (H1 = post_title).
     * Chuyển mọi <h1> thành <h2> để đảm bảo chỉ có 1 H1 trên trang.
     */
    private static function sanitize_content_headings($html) {
        if (empty($html)) return $html;
        $html = preg_replace('/<h1(\s|>)/i', '<h2$1', $html);
        $html = preg_replace('/<\/h1>/i', '</h2>', $html);
        return $html;
    }

    public static function create(\WP_REST_Request $request) {
        $params = $request->get_json_params();
        $type   = $request->get_param('type') ?: 'post';

        $post_data = [
            'post_title'   => $params['title'] ?? '',
            'post_content' => self::sanitize_content_headings($params['content'] ?? ''),
            'post_status'  => $params['status'] ?? 'draft',
            'post_type'    => $type,
            'post_author'  => get_current_user_id(),
        ];

        if (isset($params['post_password']))  $post_data['post_password']  = $params['post_password'];
        if (isset($params['comment_status'])) $post_data['comment_status'] = $params['comment_status'];
        if (isset($params['ping_status']))    $post_data['ping_status']    = $params['ping_status'];
        if (isset($params['parent_id']))      $post_data['post_parent']    = (int) $params['parent_id'];
        if (isset($params['menu_order']))     $post_data['menu_order']     = (int) $params['menu_order'];

        $id = wp_insert_post($post_data);

        if (is_wp_error($id)) {
            return new \WP_REST_Response(['message' => $id->get_error_message()], 500);
        }

        if (isset($params['category_id'])) {
            wp_set_post_categories($id, [(int) $params['category_id']]);
        }

        if (isset($params['thumbnail_id'])) {
            set_post_thumbnail($id, (int) $params['thumbnail_id']);
        }

        // Meta
        if (isset($params['seo'])) {
            $seo = $params['seo'];
            if (isset($seo['title'])) update_post_meta($id, self::get_meta_key('seo_title'), sanitize_text_field($seo['title']));
            if (isset($seo['desc']))  update_post_meta($id, self::get_meta_key('seo_desc'), sanitize_textarea_field($seo['desc']));
        }

        // Reading Time & Stats
        if (isset($params['content'])) {
            $content = $params['content'];
            update_post_meta($id, self::get_meta_key('reading_time'), self::calculate_reading_time($content));
            
            $clean_content = strip_tags($content);
            $word_count = !empty($clean_content) ? count(preg_split('/\s+/u', $clean_content, -1, PREG_SPLIT_NO_EMPTY)) : 0;
            $char_count = mb_strlen($clean_content);
            
            update_post_meta($id, self::get_meta_key('word_count'), $word_count);
            update_post_meta($id, self::get_meta_key('char_count'), $char_count);
        }

        return new \WP_REST_Response(['id' => $id, 'success' => true], 200);
    }

    public static function get_single(\WP_REST_Request $request) {
        $id = (int) $request->get_param('id');
        $p  = get_post($id);
        if (!$p) return new \WP_REST_Response(['message' => 'Not found'], 404);

        $categories = wp_get_post_categories($id);

        return new \WP_REST_Response([
            'id'             => $p->ID,
            'title'          => $p->post_title,
            'content'        => $p->post_content,
            'excerpt'        => $p->post_excerpt,
            'slug'           => $p->post_name,
            'status'         => $p->post_status,
            'date'           => get_the_date('Y-m-d H:i', $p),
            'modified'       => get_the_modified_date('Y-m-d H:i', $p),
            'post_password'  => $p->post_password,
            'comment_status' => $p->comment_status,
            'ping_status'    => $p->ping_status,
            'category_id'    => !empty($categories) ? $categories[0] : '',
            'seo' => [
                'title'    => self::get_meta_value($id, 'seo_title', ''),
                'desc'     => self::get_meta_value($id, 'seo_desc', ''),
                'keywords' => self::get_meta_value($id, 'seo_keyword', ''),
            ],
            'seo_title'   => self::get_meta_value($id, 'seo_title', ''),
            'seo_desc'    => self::get_meta_value($id, 'seo_desc', ''),
            'seo_keyword' => self::get_meta_value($id, 'seo_keyword', ''),
            'thumbnail'   => get_the_post_thumbnail_url($id, 'large'),
            'thumbnail_id' => get_post_thumbnail_id($id),
            'parent_id'   => (int) $p->post_parent,
            'menu_order'  => (int) $p->menu_order,
            'url'         => get_permalink($id),
            'edit_url'    => get_edit_post_link($id, 'raw'),
            'media_count' => count(get_attached_media('image', $id)),
        ], 200);
    }

    public static function update(\WP_REST_Request $request) {
        $id     = (int) $request->get_param('id');
        $params = $request->get_json_params();

        $post_data = ['ID' => $id];
        if (isset($params['title']))          $post_data['post_title']     = $params['title'];
        if (isset($params['content']))        $post_data['post_content']   = self::sanitize_content_headings($params['content']);
        if (isset($params['status']))         $post_data['post_status']    = $params['status'];
        if (isset($params['post_password']))  $post_data['post_password']  = $params['post_password'];
        if (isset($params['comment_status'])) $post_data['comment_status'] = $params['comment_status'];
        if (isset($params['ping_status']))    $post_data['ping_status']    = $params['ping_status'];
        if (isset($params['parent_id']))      $post_data['post_parent']    = (int) $params['parent_id'];
        if (isset($params['menu_order']))     $post_data['menu_order']     = (int) $params['menu_order'];

        wp_update_post($post_data);

        if (isset($params['category_id'])) {
            wp_set_post_categories($id, [(int) $params['category_id']]);
        }

        if (isset($params['thumbnail_id'])) {
            set_post_thumbnail($id, (int) $params['thumbnail_id']);
        }

        // Meta (Supports both root-level and nested 'seo' object)
        $seo_title   = $params['seo_title'] ?? $params['seo']['title'] ?? null;
        $seo_desc    = $params['seo_desc'] ?? $params['seo']['desc'] ?? null;
        $seo_keyword = $params['seo_keyword'] ?? $params['seo']['keywords'] ?? $params['seo']['keyword'] ?? null;

        if ($seo_title !== null)   update_post_meta($id, self::get_meta_key('seo_title'), sanitize_text_field($seo_title));
        if ($seo_desc !== null)    update_post_meta($id, self::get_meta_key('seo_desc'), sanitize_textarea_field($seo_desc));
        if ($seo_keyword !== null) update_post_meta($id, self::get_meta_key('seo_keyword'), sanitize_text_field($seo_keyword));

        // Reading Time & Stats
        if (isset($params['content'])) {
            $content = $params['content'];
            update_post_meta($id, self::get_meta_key('reading_time'), self::calculate_reading_time($content));
            
            $clean_content = strip_tags($content);
            $word_count = !empty($clean_content) ? count(preg_split('/\s+/u', $clean_content, -1, PREG_SPLIT_NO_EMPTY)) : 0;
            $char_count = mb_strlen($clean_content);
            
            update_post_meta($id, self::get_meta_key('word_count'), $word_count);
            update_post_meta($id, self::get_meta_key('char_count'), $char_count);
        }

        return new \WP_REST_Response(['success' => true], 200);
    }

    public static function delete(\WP_REST_Request $request) {
        $id    = (int) $request->get_param('id');
        $force = !empty($request->get_param('force'));
        $force ? wp_delete_post($id, true) : wp_trash_post($id);
        return new \WP_REST_Response(['success' => true], 200);
    }

    public static function bulk_action(\WP_REST_Request $request) {
        $p   = $request->get_json_params();
        $ids = array_map('intval', (array) ($p['ids'] ?? []));
        $act = $p['action'] ?? '';
        $results = [];
        
        foreach ($ids as $id) {
            // Permission check per item — dùng đúng cap của hành động
            // (trash/delete cần delete_post, không phải edit_post — với
            // post_author=0 thì edit_post map sang edit_others_pages, dễ
            // false-negative dù route đã yêu cầu manage_options ở tầng trên).
            $required_cap = in_array($act, ['trash', 'delete'], true) ? 'delete_post' : 'edit_post';
            if (!current_user_can($required_cap, $id)) {
                $results[] = ['id' => $id, 'success' => false, 'message' => 'No permission'];
                continue;
            }

            $success = false;
            if ($act === 'trash') {
                $success = (bool) wp_trash_post($id);
            } elseif ($act === 'delete') {
                $success = (bool) wp_delete_post($id, true);
            } elseif ($act === 'clone') {
                $p = get_post($id);
                if ($p) {
                    $new_id = wp_insert_post([
                        'post_title'   => $p->post_title . ' (Copy)',
                        'post_content' => $p->post_content,
                        'post_excerpt' => $p->post_excerpt,
                        'post_status'  => 'draft',
                        'post_type'    => $p->post_type,
                        'post_author'  => get_current_user_id(),
                    ]);
                    if ($new_id && !is_wp_error($new_id)) {
                        // Clone taxonomies & thumbnail
                        $taxonomies = get_object_taxonomies($p->post_type);
                        foreach ($taxonomies as $tax) {
                            $terms = wp_get_object_terms($id, $tax, ['fields' => 'ids']);
                            wp_set_object_terms($new_id, $terms, $tax);
                        }
                        if (has_post_thumbnail($id)) {
                            set_post_thumbnail($new_id, get_post_thumbnail_id($id));
                        }
                        $success = true;
                    }
                }
            } elseif (!empty($act)) {
                $updated_id = wp_update_post([
                    'ID'          => $id,
                    'post_status' => $act
                ]);
                $success = ($updated_id !== 0 && !is_wp_error($updated_id));
            }

            $results[] = [
                'id'      => $id,
                'success' => $success
            ];
        }

        // success tổng phải phản ánh đúng kết quả từng item — trước đây
        // luôn hard-code true, khiến frontend luôn báo "đã xóa" dù item lỗi.
        $all_ok = !in_array(false, array_column($results, 'success'), true);
        $any_ok = in_array(true, array_column($results, 'success'), true);
        $status = $all_ok ? 200 : ($any_ok ? 207 : 500);

        return new \WP_REST_Response([
            'success' => $all_ok,
            'results' => $results
        ], $status);
    }

    public static function clone(\WP_REST_Request $request) {
        $id = (int) $request->get_param('id');
        $p  = get_post($id);
        if (!$p) return new \WP_REST_Response(['message' => 'Not found'], 404);

        $new_id = wp_insert_post([
            'post_title'   => $p->post_title . ' (Copy)',
            'post_content' => $p->post_content,
            'post_excerpt' => $p->post_excerpt,
            'post_status'  => 'draft',
            'post_type'    => $p->post_type,
            'post_author'  => get_current_user_id(),
        ]);

        if ($new_id && !is_wp_error($new_id)) {
            // Clone Categories & Taxonomies
            $taxonomies = get_object_taxonomies($p->post_type);
            foreach ($taxonomies as $tax) {
                $terms = wp_get_object_terms($id, $tax, ['fields' => 'ids']);
                wp_set_object_terms($new_id, $terms, $tax);
            }

            // Clone Meta (excluding internal ones)
            $meta = get_post_custom($id);
            foreach ($meta as $key => $values) {
                if (strpos($key, '_edit_') === 0 || $key === '_encloseme') continue;
                foreach ($values as $val) {
                    add_post_meta($new_id, $key, maybe_unserialize($val));
                }
            }
            return new \WP_REST_Response(['success' => true, 'new_id' => $new_id], 200);
        }

        return new \WP_REST_Response(['message' => 'Clone failed'], 500);
    }

    public static function get_versions(\WP_REST_Request $request) {
        $id = (int) $request->get_param('id');
        $vs = get_post_meta($id, self::get_meta_key('post_versions'), true) ?: [];
        return new \WP_REST_Response(['versions' => array_reverse($vs)], 200);
    }

    public static function save_version(\WP_REST_Request $request) {
        $id = (int) $request->get_param('id');
        $p  = get_post($id);
        $vs = get_post_meta($id, self::get_meta_key('post_versions'), true) ?: [];
        
        $vs[] = [
            'time'    => current_time('mysql'),
            'content' => $p->post_content,
            'user'    => wp_get_current_user()->display_name,
        ];
        if (count($vs) > 20) $vs = array_slice($vs, -20);
        update_post_meta($id, self::get_meta_key('post_versions'), $vs);
        
        return new \WP_REST_Response(['success' => true], 200);
    }

    public static function restore_version(\WP_REST_Request $request) {
        $id  = (int) $request->get_param('id');
        $p   = $request->get_json_params();
        $idx = (int) $p['idx'];

        $vs = get_post_meta($id, self::get_meta_key('post_versions'), true);
        if (isset($vs[$idx])) {
            wp_update_post(['ID' => $id, 'post_content' => $vs[$idx]['content']]);
            return new \WP_REST_Response(['success' => true], 200);
        }
        return new \WP_REST_Response(['message' => 'Version not found'], 404);
    }

    /**
     * Bulk-update SEO meta + slugs for a list of posts.
     * Body: { updates: [ { id, fields: { meta_title, meta_description, focus_keyword, slug } } ] }
     * Returns: { results: [ { id, ok, message? } ] }
     */
    public static function bulk_update(\WP_REST_Request $request) {
        $params  = $request->get_json_params();
        $updates = (array) ($params['updates'] ?? []);
        $results = [];

        $field_to_meta = [
            'meta_title'       => 'seo_title',
            'meta_description' => 'seo_desc',
            'focus_keyword'    => 'seo_keyword',
        ];

        foreach ($updates as $u) {
            $id = (int) ($u['id'] ?? 0);
            $fields = (array) ($u['fields'] ?? []);
            if ($id <= 0 || !get_post($id)) {
                $results[] = ['id' => $id, 'ok' => false, 'message' => 'post not found'];
                continue;
            }

            // Slug update via wp_update_post (post_name).
            if (array_key_exists('slug', $fields)) {
                wp_update_post([
                    'ID'        => $id,
                    'post_name' => sanitize_title($fields['slug']),
                ]);
            }

            foreach ($field_to_meta as $field => $meta_key) {
                if (!array_key_exists($field, $fields)) continue;
                $val = $fields[$field];
                $meta = self::get_meta_key($meta_key);
                if ($val === '' || $val === null) {
                    delete_post_meta($id, $meta);
                } else {
                    $sanitized = $field === 'meta_description'
                        ? sanitize_textarea_field($val)
                        : sanitize_text_field($val);
                    update_post_meta($id, $meta, $sanitized);
                }
            }

            $results[] = ['id' => $id, 'ok' => true];
        }

        return new \WP_REST_Response(['results' => $results], 200);
    }

    /**
     * Returns posts with full SEO meta payload — used by the bulk-edit table.
     * Supports filters: type, status, search, category, page, per_page.
     */
    public static function get_seo_table(\WP_REST_Request $request) {
        $type     = $request->get_param('type') ?: 'post';
        $status   = $request->get_param('status') ?: 'any';
        $search   = $request->get_param('search') ?: '';
        $category = (int) ($request->get_param('category') ?: 0);
        $page     = max(1, (int) ($request->get_param('page') ?: 1));
        $per_page = min(200, max(1, (int) ($request->get_param('per_page') ?: 50)));

        $args = [
            'post_type'      => $type,
            'post_status'    => $status,
            'posts_per_page' => $per_page,
            'paged'          => $page,
            'orderby'        => 'date',
            'order'          => 'DESC',
            's'              => $search,
        ];
        if ($category > 0 && $type === 'post') {
            $args['cat'] = $category;
        }

        $query = new \WP_Query($args);
        $items = [];
        foreach ($query->posts as $p) {
            $thumb = get_the_post_thumbnail_url($p->ID, 'thumbnail') ?: null;
            $og_image = (string) (get_post_meta($p->ID, '_pi_og_image', true) ?: get_post_meta($p->ID, '_pi_og_image_url', true));
            $items[] = [
                'id'               => $p->ID,
                'title'            => $p->post_title,
                'slug'             => $p->post_name,
                'status'           => $p->post_status,
                'thumbnail'        => $thumb,
                'meta_title'       => (string) self::get_meta_value($p->ID, 'seo_title', ''),
                'meta_description' => (string) self::get_meta_value($p->ID, 'seo_desc', ''),
                'focus_keyword'    => (string) self::get_meta_value($p->ID, 'seo_keyword', ''),
                'word_count'       => str_word_count(strip_tags($p->post_content)),
                'has_og_image'     => !empty($og_image) || !empty($thumb),
                'updated_at'       => get_the_modified_date('Y-m-d H:i', $p),
            ];
        }

        return new \WP_REST_Response([
            'items' => $items,
            'total' => (int) $query->found_posts,
            'pages' => (int) $query->max_num_pages,
        ], 200);
    }

    /**
     * Helper to calculate reading time in minutes (assumes 200 wpm)
     */
    private static function calculate_reading_time($content) {
        $clean_content = strip_tags($content);
        $word_count = !empty($clean_content) ? count(preg_split('/\s+/u', $clean_content, -1, PREG_SPLIT_NO_EMPTY)) : 0;
        return ceil($word_count / 200);
    }
}
