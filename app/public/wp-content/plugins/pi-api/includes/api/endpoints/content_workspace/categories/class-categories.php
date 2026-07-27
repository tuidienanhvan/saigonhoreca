<?php
/**
 * Pi API Categories Endpoints
 */

if (!defined('ABSPATH')) exit;

class Pi_Api_Categories {

    public static function register_routes($ns, $admin) {
        register_rest_route($ns, '/categories', [
            'methods'  => 'GET',
            'callback' => [self::class, 'get_all'],
            'permission_callback' => $admin
        ]);
        register_rest_route($ns, '/categories/audit', [
            'methods'  => 'GET',
            'callback' => [self::class, 'get_audit'],
            'permission_callback' => $admin
        ]);
        register_rest_route($ns, '/categories/sync', [
            'methods'  => 'POST',
            'callback' => [self::class, 'sync'],
            'permission_callback' => $admin
        ]);
        register_rest_route($ns, '/categories/merge/preview', [
            'methods'  => 'POST',
            'callback' => [self::class, 'merge_preview'],
            'permission_callback' => $admin
        ]);
        register_rest_route($ns, '/categories/merge', [
            'methods'  => 'POST',
            'callback' => [self::class, 'merge_execute'],
            'permission_callback' => $admin
        ]);
    }

    public static function get_all() {
        $categories = get_categories(['hide_empty' => false]);
        $formatted = array_map(function($cat) {
            return [
                'id'   => $cat->term_id,
                'name' => $cat->name,
                'slug' => $cat->slug,
                'count' => $cat->count
            ];
        }, $categories);
        return new \WP_REST_Response(['categories' => $formatted], 200);
    }

    public static function get_audit() {
        if (!class_exists('\\PiDashboardV2\\SiteStructure')) {
            return new \WP_REST_Response(['message' => 'SiteStructure not available'], 400);
        }

        $cats_def = \PiDashboardV2\SiteStructure::getCategories();
        $all_cats = get_categories(['hide_empty' => false]);
        $db_slugs = wp_list_pluck($all_cats, 'slug');

        $audit = [];
        foreach ($cats_def as $slug => $info) {
            $audit[] = [
                'slug'   => $slug,
                'name'   => $info['name'],
                'exists' => in_array($slug, $db_slugs, true),
            ];
        }

        return new \WP_REST_Response(['audit' => $audit], 200);
    }

    public static function sync(\WP_REST_Request $request) {
        if (!class_exists('\\PiDashboardV2\\SiteSync')) return new \WP_REST_Response(['message' => 'SiteSync not available'], 400);
        $p = $request->get_json_params();
        return new \WP_REST_Response(\PiDashboardV2\SiteSync::syncCategories(['force' => !empty($p['force'])]), 200);
    }

    /**
     * Validate merge input and normalize ids.
     *
     * @return array|\WP_REST_Response Normalized [source_ids, target_id] or error response.
     */
    private static function validate_merge_input(\WP_REST_Request $request) {
        $p = $request->get_json_params();

        $source_ids = isset($p['source_ids']) && is_array($p['source_ids'])
            ? array_values(array_unique(array_map('intval', $p['source_ids'])))
            : [];
        $target_id  = isset($p['target_id']) ? intval($p['target_id']) : 0;

        $source_ids = array_values(array_filter($source_ids, function ($id) { return $id > 0; }));

        if (empty($source_ids)) {
            return new \WP_REST_Response(['message' => 'Phải chọn ít nhất một chuyên mục nguồn.'], 400);
        }
        if ($target_id <= 0) {
            return new \WP_REST_Response(['message' => 'Thiếu chuyên mục đích.'], 400);
        }
        if (in_array($target_id, $source_ids, true)) {
            return new \WP_REST_Response(['message' => 'Chuyên mục đích không được nằm trong danh sách nguồn.'], 400);
        }

        // Block "Uncategorized" (id 1) as a source. It can still be a target.
        if (in_array(1, $source_ids, true)) {
            return new \WP_REST_Response(['message' => 'Không thể gộp chuyên mục mặc định "Uncategorized".'], 400);
        }

        // Validate existence.
        if (!term_exists($target_id, 'category')) {
            return new \WP_REST_Response(['message' => 'Chuyên mục đích không tồn tại (ID: ' . $target_id . ').'], 400);
        }
        foreach ($source_ids as $sid) {
            if (!term_exists($sid, 'category')) {
                return new \WP_REST_Response(['message' => 'Chuyên mục nguồn không tồn tại (ID: ' . $sid . ').'], 400);
            }
        }

        return ['source_ids' => $source_ids, 'target_id' => $target_id];
    }

    /**
     * POST /categories/merge/preview — preview merge impact.
     */
    public static function merge_preview(\WP_REST_Request $request) {
        $validated = self::validate_merge_input($request);
        if ($validated instanceof \WP_REST_Response) return $validated;

        $source_ids = $validated['source_ids'];
        $target_id  = $validated['target_id'];

        $target_term = get_term($target_id, 'category');
        $target = [
            'id'   => (int) $target_term->term_id,
            'name' => $target_term->name,
            'slug' => $target_term->slug,
            'post_count' => (int) $target_term->count,
        ];

        $sources = [];
        $all_post_ids = [];
        $total_posts_affected = 0;
        $warnings = [];
        $has_children_to_reparent = false;
        $has_large_source = false;

        foreach ($source_ids as $sid) {
            $term = get_term($sid, 'category');
            $post_ids = get_objects_in_term($sid, 'category');
            if (is_wp_error($post_ids)) $post_ids = [];
            $post_ids = array_map('intval', $post_ids);

            $children = get_terms([
                'taxonomy'   => 'category',
                'parent'     => $sid,
                'hide_empty' => false,
            ]);
            $child_count = is_array($children) ? count($children) : 0;
            if ($child_count > 0) $has_children_to_reparent = true;

            $pc = count($post_ids);
            if ($pc > 500) $has_large_source = true;

            $sources[] = [
                'id'           => (int) $term->term_id,
                'name'         => $term->name,
                'slug'         => $term->slug,
                'post_count'   => $pc,
                'child_count'  => $child_count,
            ];

            $all_post_ids = array_merge($all_post_ids, $post_ids);
            $total_posts_affected += $pc;
        }

        $unique_posts_affected = count(array_unique($all_post_ids));

        if ($has_children_to_reparent) {
            $warnings[] = 'Một số chuyên mục nguồn có chuyên mục con — con sẽ được chuyển về chuyên mục đích.';
        }
        if ($has_large_source) {
            $warnings[] = 'Có chuyên mục nguồn chứa hơn 500 bài viết — thao tác có thể mất thời gian.';
        }
        if ($total_posts_affected !== $unique_posts_affected) {
            $warnings[] = 'Một số bài viết xuất hiện trong nhiều chuyên mục nguồn — chỉ tính một lần trong kết quả cuối.';
        }

        $target_has_after_merge = (int) $target['post_count'] + $unique_posts_affected;

        return new \WP_REST_Response([
            'target'                 => $target,
            'sources'                => $sources,
            'total_posts_affected'   => $total_posts_affected,
            'unique_posts_affected'  => $unique_posts_affected,
            'target_has_after_merge' => $target_has_after_merge,
            'warnings'               => $warnings,
        ], 200);
    }

    /**
     * POST /categories/merge — execute merge. Requires confirm=true.
     */
    public static function merge_execute(\WP_REST_Request $request) {
        $p = $request->get_json_params();
        if (empty($p['confirm']) || $p['confirm'] !== true) {
            return new \WP_REST_Response(['message' => 'Thiếu cờ xác nhận (confirm: true).'], 400);
        }

        $validated = self::validate_merge_input($request);
        if ($validated instanceof \WP_REST_Response) return $validated;

        $source_ids = $validated['source_ids'];
        $target_id  = $validated['target_id'];

        $target_term = get_term($target_id, 'category');
        $log = [];
        $posts_moved = 0;
        $children_reparented = 0;
        $sources_deleted = [];

        $log[] = sprintf('Bắt đầu gộp vào "%s" (ID %d).', $target_term->name, $target_id);

        foreach ($source_ids as $sid) {
            $src_term = get_term($sid, 'category');
            if (!$src_term || is_wp_error($src_term)) {
                $log[] = sprintf('Bỏ qua ID %d — không tồn tại.', $sid);
                continue;
            }
            $log[] = sprintf('Xử lý nguồn "%s" (ID %d).', $src_term->name, $sid);

            // 1. Move posts (append target category).
            $post_ids = get_objects_in_term($sid, 'category');
            if (is_wp_error($post_ids)) $post_ids = [];
            $post_ids = array_map('intval', $post_ids);
            $moved_this_source = 0;
            foreach ($post_ids as $post_id) {
                $result = wp_set_post_terms($post_id, [$target_id], 'category', true);
                if (!is_wp_error($result)) {
                    $posts_moved++;
                    $moved_this_source++;
                }
            }
            $log[] = sprintf('  ↳ Đã chuyển %d bài viết.', $moved_this_source);

            // 2. Re-parent children to target.
            $children = get_terms([
                'taxonomy'   => 'category',
                'parent'     => $sid,
                'hide_empty' => false,
            ]);
            if (is_array($children) && !empty($children)) {
                foreach ($children as $child) {
                    $updated = wp_update_term($child->term_id, 'category', ['parent' => $target_id]);
                    if (!is_wp_error($updated)) {
                        $children_reparented++;
                        $log[] = sprintf('  ↳ Chuyển con "%s" (ID %d) sang đích.', $child->name, $child->term_id);
                    } else {
                        $log[] = sprintf('  ↳ Lỗi khi chuyển con ID %d: %s', $child->term_id, $updated->get_error_message());
                    }
                }
            }

            // 3. Delete the source category.
            $deleted = wp_delete_term($sid, 'category');
            if ($deleted === true || (is_bool($deleted) && $deleted)) {
                $sources_deleted[] = $sid;
                $log[] = sprintf('  ↳ Đã xóa nguồn ID %d.', $sid);
            } else {
                $msg = is_wp_error($deleted) ? $deleted->get_error_message() : 'unknown';
                $log[] = sprintf('  ↳ Không thể xóa nguồn ID %d (%s).', $sid, $msg);
            }
        }

        // Clear term cache so subsequent queries see fresh counts.
        clean_term_cache([$target_id], 'category');

        $log[] = sprintf('Hoàn tất: %d bài chuyển, %d con chuyển, %d nguồn xóa.',
            $posts_moved, $children_reparented, count($sources_deleted));

        return new \WP_REST_Response([
            'success'              => true,
            'posts_moved'          => $posts_moved,
            'children_reparented'  => $children_reparented,
            'sources_deleted'      => $sources_deleted,
            'log'                  => $log,
        ], 200);
    }
}
