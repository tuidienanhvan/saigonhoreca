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
            $args['cat'] = $category;
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

        $query = new \WP_Query($args);
        $items = [];

        // Collect all IDs to pre-fetch meta and terms in bulk
        $post_ids = wp_list_pluck($query->posts, 'ID');
        if ($post_ids) {
            update_post_thumbnail_cache($query);
            update_object_term_cache($post_ids, $type);
        }

        foreach ($query->posts as $p) {
            // Ưu tiên lấy từ meta đã được lưu sẵn
            $word_count = (int) get_post_meta($p->ID, self::get_meta_key('word_count'), true);
            if (!$word_count) {
                $clean_content = strip_tags($p->post_content);
                $word_count = !empty($clean_content) ? count(preg_split('/\s+/u', $clean_content, -1, PREG_SPLIT_NO_EMPTY)) : 0;
            }
            
            // Get categories
            $cats = get_the_category($p->ID);
            $formatted_cats = [];
            if (!empty($cats)) {
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
                'thumbnail'      => get_the_post_thumbnail_url($p->ID, 'medium') ?: null,
                'categories'     => $formatted_cats,
                'word_count'     => $word_count,
                'comment_count'  => (int) $p->comment_count,
            ];
        }

        return new \WP_REST_Response([
            'items' => $items,
            'total' => (int) $query->found_posts,
            'pages' => (int) $query->max_num_pages,
        ], 200);
    }

    public static function create(\WP_REST_Request $request) {
        $params = $request->get_json_params();
        $type   = $request->get_param('type') ?: 'post';

        $post_data = [
            'post_title'   => $params['title'] ?? '',
            'post_content' => $params['content'] ?? '',
            'post_status'  => $params['status'] ?? 'draft',
            'post_type'    => $type,
            'post_author'  => get_current_user_id(),
        ];

        if (isset($params['post_password']))  $post_data['post_password']  = $params['post_password'];
        if (isset($params['comment_status'])) $post_data['comment_status'] = $params['comment_status'];
        if (isset($params['ping_status']))    $post_data['ping_status']    = $params['ping_status'];

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
            'thumbnail'   => get_the_post_thumbnail_url($id, 'large'),
            'thumbnail_id' => get_post_thumbnail_id($id),
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
        if (isset($params['content']))        $post_data['post_content']   = $params['content'];
        if (isset($params['status']))         $post_data['post_status']    = $params['status'];
        if (isset($params['post_password']))  $post_data['post_password']  = $params['post_password'];
        if (isset($params['comment_status'])) $post_data['comment_status'] = $params['comment_status'];
        if (isset($params['ping_status']))    $post_data['ping_status']    = $params['ping_status'];

        wp_update_post($post_data);

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
