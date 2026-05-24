<?php
/**
 * SGH_Dashboard Class — Core singleton, stats, system status, and action dispatcher.
 *
 * @package SaigonHoreca
 */

if (!defined('ABSPATH')) exit;

class SGH_Dashboard {

    /**
     * Singleton instance
     */
    private static $instance = null;

    /**
     * Get singleton instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor - Setup hooks
     */
    private function __construct() {
        // Clear stats cache when posts change
        add_action('save_post', function() { delete_transient('sgh_dashboard_stats'); });
        add_action('delete_post', function() { delete_transient('sgh_dashboard_stats'); });
        add_action('transition_post_status', function() { delete_transient('sgh_dashboard_stats'); });
    }

    // ========================================
    // STATISTICS METHODS
    // ========================================

    /**
     * Get overview statistics
     */
    public function get_stats() {
        // Cache stats 5 phút để không query DB mỗi lần load dashboard
        $cached = get_transient('sgh_dashboard_stats');
        if ($cached !== false) return $cached;
        $pages_def = sgh_get_pages_definition();
        $cats_def  = sgh_get_categories_definition();
        $menu_obj  = wp_get_nav_menu_object('Primary Menu');
        $menu_items = $menu_obj ? wp_get_nav_menu_items($menu_obj->term_id) : [];

        $all_pages = get_posts([
            'post_type'      => 'page',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'no_found_rows'  => true,
        ]);
        $page_slug_map = [];
        foreach ($all_pages as $p) {
            $page_slug_map[$p->post_name] = $p->ID;
        }

        $pages_exist = 0;
        foreach ($pages_def as $slug => $info) {
            if (isset($page_slug_map[$slug])) $pages_exist++;
        }

        // Batch fetch all categories (1 query instead of N)
        $all_cats = get_categories(['hide_empty' => false]);
        $cat_slug_set = [];
        foreach ($all_cats as $cat) {
            $cat_slug_set[$cat->slug] = true;
        }

        // Build valid slugs list and count categories
        $valid_slugs = [];
        $cats_total = 0;
        $cats_exist = 0;
        foreach ($cats_def as $parent_slug => $parent_info) {
            $cats_total++;
            $valid_slugs[] = $parent_slug;
            if (isset($cat_slug_set[$parent_slug])) $cats_exist++;
            if (!empty($parent_info['children'])) {
                foreach ($parent_info['children'] as $slug => $name) {
                    $cats_total++;
                    $valid_slugs[] = $slug;
                    if (isset($cat_slug_set[$slug])) $cats_exist++;
                }
            }
        }

        // Count stale categories (reuse $all_cats from batch fetch above)
        $valid_set = array_flip($valid_slugs);
        $stale_count = 0;
        foreach ($all_cats as $cat) {
            if ($cat->slug !== 'uncategorized' && !isset($valid_set[$cat->slug])) {
                $stale_count++;
            }
        }

        // WordPress stats
        $counts = wp_count_posts();
        $wp_post_count = (int) ($counts->publish ?? 0);

        $pages_counts = wp_count_posts('page');
        $wp_page_count = (int) ($pages_counts->publish ?? 0);

        $comments_counts = wp_count_comments();
        $wp_comment_count = (int) ($comments_counts->approved ?? 0);

        $att_counts = (array) wp_count_attachments();
        unset($att_counts['trash']);
        $wp_media_count = (int) array_sum($att_counts);

        // Delta stats for Hero banner
        global $wpdb;
        $posts_this_week = (int) $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->posts}
             WHERE post_type = 'post' AND post_status = 'publish'
             AND post_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)"
        );
        $media_today = (int) $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->posts}
             WHERE post_type = 'attachment' AND post_status = 'inherit'
             AND post_date >= CURDATE()"
        );

        $stats = [
            'pages' => [
                'exist' => $pages_exist,
                'total' => count($pages_def),
                'percent' => count($pages_def) > 0 ? round(($pages_exist / count($pages_def)) * 100) : 0
            ],
            'categories' => [
                'exist' => $cats_exist,
                'total' => $cats_total,
                'percent' => $cats_total > 0 ? round(($cats_exist / $cats_total) * 100) : 0
            ],
            'menu_items' => count($menu_items),
            'stale_categories' => $stale_count,
            'wp_posts' => $wp_post_count,
            'wp_pages' => $wp_page_count,
            'wp_comments' => $wp_comment_count,
            'wp_media' => $wp_media_count,
            'posts_this_week' => $posts_this_week,
            'media_today' => $media_today,
        ];
        set_transient('sgh_dashboard_stats', $stats, 300); // Cache 5 phút
        return $stats;
    }

    /**
     * Get system status information
     */
    public function get_system_status() {
        global $wpdb;

        // WordPress version
        $wp_version = get_bloginfo('version');

        // PHP version
        $php_version = PHP_VERSION;

        // Memory limit
        $memory_limit = ini_get('memory_limit');
        $memory_usage = round(memory_get_usage() / 1024 / 1024, 2);

        // Max execution time
        $max_execution_time = ini_get('max_execution_time');

        // Database info
        $db_version = $wpdb->db_version();

        // Upload max size
        $upload_max = size_format(wp_max_upload_size());

        // Check debug mode
        $debug_mode = (defined('WP_DEBUG') && WP_DEBUG) ? true : false;

        // Check cache
        $cache_type = 'No Cache';
        if (defined('WP_CACHE') && WP_CACHE) {
            $cache_type = 'WP_CACHE';
        }
        if (function_exists('wp_using_ext_object_cache')) {
            $using_cache = wp_using_ext_object_cache();
            if ($using_cache) {
                $cache_type = 'Object Cache (Redis/Memcached)';
            }
        }

        // Theme info
        $theme = wp_get_theme();

        return [
            'wordpress' => [
                'version' => $wp_version,
                'debug' => $debug_mode,
                'language' => get_bloginfo('language')
            ],
            'php' => [
                'version' => $php_version,
                'memory_limit' => $memory_limit,
                'memory_usage' => $memory_usage,
                'max_execution' => $max_execution_time
            ],
            'database' => [
                'version' => $db_version,
                'type' => 'MySQL/MariaDB'
            ],
            'server' => [
                'upload_max' => $upload_max,
                'cache' => $cache_type
            ],
            'theme' => [
                'name' => $theme->get('Name'),
                'version' => $theme->get('Version')
            ]
        ];
    }

    // ========================================
    // ACTION DISPATCHER
    // ========================================

    /**
     * Process sync action and return result
     */
    public function process_action($action) {
        // Load deferred modules (cache, reset, export, sync) on demand
        do_action('sgh_before_action_dispatch');

        // Invalidate stats cache khi bất kỳ action nào chạy
        delete_transient('sgh_dashboard_stats');

        $result = [
            'success' => true,
            'message' => '',
            'log' => [],
            'data' => []
        ];

        switch ($action) {
            case 'save-ai-settings':
                $providers = function_exists('sh_api_get_providers') ? sh_api_get_providers() : [];
                $posted_pools = isset($_POST['sgh_api_keys']) && is_array($_POST['sgh_api_keys'])
                    ? (array) wp_unslash($_POST['sgh_api_keys'])
                    : [];
                $saved_keys = 0;
                $providers_with_keys = 0;
                $log = [];
                $primary_by_key_option = [];
                $seen_key_options = [];

                foreach ($providers as $p) {
                    if (empty($p['key_option'])) continue;

                    $provider_id = sanitize_key((string) ($p['id'] ?? ''));
                    if ($provider_id === '') continue;

                    $pool_option = function_exists('sh_api_get_provider_pool_option')
                        ? sh_api_get_provider_pool_option($p)
                        : ('sgh_api_keys_' . $provider_id);

                    $raw_keys = [];
                    if (isset($posted_pools[$provider_id])) {
                        $raw_keys = (array) $posted_pools[$provider_id];
                    } elseif (isset($_POST[$p['key_option']])) {
                        // Backward compatibility with old single-input form
                        $raw_keys = [wp_unslash($_POST[$p['key_option']])];
                    }

                    $clean_keys = [];
                    foreach ($raw_keys as $raw_key) {
                        $key = trim(sanitize_text_field((string) $raw_key));
                        if ($key !== '') {
                            $clean_keys[] = $key;
                        }
                    }
                    $clean_keys = array_values(array_unique($clean_keys));

                    update_option($pool_option, $clean_keys);

                    if (!empty($clean_keys)) {
                        $saved_keys += count($clean_keys);
                        $providers_with_keys++;
                        if (!isset($primary_by_key_option[$p['key_option']])) {
                            $primary_by_key_option[$p['key_option']] = $clean_keys[0];
                        }
                    }

                    $log[] = '- ' . ($p['name'] ?? $provider_id) . ': ' . count($clean_keys) . ' key(s)';
                    $seen_key_options[$p['key_option']] = true;
                }

                // Keep legacy single-key options in sync for backward compatibility.
                foreach (array_keys($seen_key_options) as $key_option) {
                    $legacy_primary = $primary_by_key_option[$key_option] ?? '';
                    update_option($key_option, $legacy_primary);
                }

                // Save Cloudflare account id
                $cloudflare_account_id = isset($_POST['sgh_cloudflare_account_id'])
                    ? sanitize_text_field(wp_unslash($_POST['sgh_cloudflare_account_id']))
                    : '';
                update_option('sgh_cloudflare_account_id', $cloudflare_account_id);
                if ($cloudflare_account_id !== '') {
                    $log[] = '- cloudflare_account_id: đã cấu hình';
                }

                // Lưu model đã chọn cho từng provider
                foreach ($providers as $p) {
                    $pid = sanitize_key((string) ($p['id'] ?? ''));
                    $model_field = 'sgh_model_' . $pid;
                    if (isset($_POST[$model_field])) {
                        update_option($model_field, sanitize_text_field($_POST[$model_field]));
                    }
                }

                $active = function_exists('sh_api_get_active_providers') ? count(sh_api_get_active_providers()) : $providers_with_keys;
                $result['log'] = array_merge(
                    ["Đã lưu {$saved_keys} API keys ({$providers_with_keys} provider có key)."],
                    $log,
                    ["", "{$active} providers đang hoạt động."]
                );
                $result['success'] = true;
                $result['message'] = "Đã cập nhật {$active} AI providers!";
                break;

            case 'sync-pages':
                $res = sgh_sync_pages();
                $result['log'] = $res['log'];
                $result['data'] = $res;
                $result['message'] = sprintf('Pages: %d created, %d updated, %d skipped',
                    $res['created'], $res['updated'], $res['skipped']);
                break;

            case 'process-import-zip':
                $res = sgh_dashboard_process_import_zip();
                $result['log'] = $res['log'];
                $result['data'] = $res;
                $result['success'] = $res['success'];
                $result['message'] = $res['message'];
                break;

            case 'import-from-posts-dir':
                $res = sgh_dashboard_import_from_posts_dir();
                $result['log'] = $res['log'];
                $result['data'] = $res;
                $result['success'] = $res['success'];
                $result['message'] = $res['message'];
                break;

            case 'sync-categories':
                $res = sgh_sync_categories();
                $result['log'] = $res['log'];
                $result['data'] = $res;
                $result['message'] = sprintf('Categories: %d created, %d skipped',
                    $res['created'], $res['skipped']);
                break;

            case 'cleanup-categories':
                $res = sgh_cleanup_stale_categories();
                $result['log'] = $res['log'];
                $result['data'] = $res;
                $result['message'] = sprintf('Cleanup: %d categories deleted', $res['deleted']);
                break;

            case 'clear-nano-cache':
                if (class_exists('SGH_Nano_Cache')) {
                    $nano = new \SGH_Nano_Cache();
                    $nano->flush_all_nano_cache();
                }
                $res = ['log' => ['Database transient cache cleared.'], 'deleted' => 1];
                $result['log'] = $res['log'];
                $result['data'] = $res;
                $result['message'] = 'Nano Cache: Đã xóa toàn bộ cache.';
                break;

            case 'flush_all_cache':
                $log = [];
                wp_cache_flush();
                $log[] = 'WP Object Cache cleared.';
                delete_transient('sgh_dashboard_stats');
                delete_transient('sgh_new_leads_count');
                $log[] = 'Dashboard transients cleared.';
                if (class_exists('SGH_Nano_Cache')) {
                    $nano = new \SGH_Nano_Cache();
                    $nano->flush_all_nano_cache();
                    $log[] = 'Nano Cache: Đã xóa sạch.';
                }
                if (function_exists('opcache_reset')) {
                    @opcache_reset();
                    $log[] = 'OPcache cleared.';
                }
                $result['log'] = $log;
                $result['message'] = 'All cache cleared.';
                break;

            case 'delete-all-posts':
                $res = sgh_dashboard_delete_all_posts();
                $result['log'] = $res['log'];
                $result['data'] = $res;
                $result['message'] = sprintf('Xóa Bài Viết: Đã xóa vĩnh viễn %d bài.', $res['deleted']);
                break;

            case 'sync-favicon':
                $res = sgh_dashboard_sync_favicons();
                $result['log'] = $res['log'];
                $result['data'] = $res;
                $result['success'] = $res['success'];
                $result['message'] = $res['message'];
                break;

            case 'sync-menu':
                $res = sgh_dashboard_sync_menu();
                $result['log'] = $res['log'];
                $result['data'] = $res;
                $result['success'] = $res['errors'] === 0;
                $result['message'] = sprintf('Menu: %d items created, %d errors',
                    $res['created'], $res['errors']);
                break;

            case 'sync-all':
                $log = ['━━━ SYNC ALL ━━━'];

                $p = sgh_sync_pages();
                $log[] = ''; $log[] = '📄 PAGES:';
                $log = array_merge($log, $p['log']);

                $c = sgh_sync_categories();
                $log[] = ''; $log[] = '🏷️ CATEGORIES:';
                $log = array_merge($log, $c['log']);

                $cl = sgh_cleanup_stale_categories();
                $log[] = ''; $log[] = '🧹 CLEANUP:';
                $log = array_merge($log, $cl['log']);

                $m = sgh_dashboard_sync_menu();
                $log[] = ''; $log[] = '🔄 MENU:';
                $log = array_merge($log, $m['log']);

                $log[] = ''; $log[] = '━━━ HOÀN TẤT ━━━';

                $result['log'] = $log;
                $result['data'] = ['pages' => $p, 'categories' => $c, 'cleanup' => $cl, 'menu' => $m];
                $result['success'] = $m['errors'] === 0;
                $result['message'] = 'Sync All completed!';
                break;

            case 'reset-db':
                $res = sgh_reset_all();
                $result['log'] = $res['log'];
                $result['data'] = $res;
                $result['message'] = sprintf('Reset: %d items deleted', $res['total_deleted']);
                break;

            case 'nuclear-reset':
                $res = sgh_dashboard_nuclear_reset();
                $result['log'] = $res['log'];
                $result['data'] = $res;
                $result['message'] = $res['message'];
                break;

            case 'deploy-htaccess':
                $res = sgh_dashboard_deploy_htaccess();
                $result['log'] = $res['log'];
                $result['success'] = $res['success'];
                $result['message'] = $res['message'];
                break;

            case 'deploy-og-image':
                $res = sgh_dashboard_deploy_og_image();
                $result['log'] = $res['log'];
                $result['success'] = $res['success'];
                $result['message'] = $res['message'];
                break;

            case 'sync-db-to-json':
                $res = sgh_dashboard_sync_db_to_json();
                $result['log'] = $res['log'];
                $result['success'] = $res['success'];
                $result['message'] = $res['message'];
                break;

            case 'undeploy-htaccess':
                $res = sgh_dashboard_undeploy_htaccess();
                $result['log'] = $res['log'];
                $result['success'] = $res['success'];
                $result['message'] = $res['message'];
                break;

            case 'undeploy-og-image':
                $res = sgh_dashboard_undeploy_og_image();
                $result['log'] = $res['log'];
                $result['success'] = $res['success'];
                $result['message'] = $res['message'];
                break;

            case 'deploy-robots-txt':
                $res = sgh_dashboard_deploy_robots_txt();
                $result['log'] = $res['log'];
                $result['success'] = $res['success'];
                $result['message'] = $res['message'];
                break;

            case 'undeploy-robots-txt':
                $res = sgh_dashboard_undeploy_robots_txt();
                $result['log'] = $res['log'];
                $result['success'] = $res['success'];
                $result['message'] = $res['message'];
                break;

            case 'generate-og-screenshots':
                $res = sgh_dashboard_generate_og_screenshots();
                $result['log'] = $res['log'];
                $result['success'] = $res['success'];
                $result['message'] = $res['message'];
                break;

            case 'convert-images-webp':
                $res = sgh_dashboard_convert_images_webp();
                $result['log'] = $res['log'];
                $result['success'] = $res['success'];
                $result['message'] = $res['message'];
                break;

            case 'delete-page':
                $page_id = (int) ($_GET['item_id'] ?? 0);
                if ($page_id && $page_id !== (int) get_option('page_on_front')) {
                    wp_delete_post($page_id, true);
                    wp_cache_flush();
                    $result['message'] = "Đã xóa page ID {$page_id}.";
                } else {
                    $result['success'] = false;
                    $result['message'] = 'Không thể xóa trang chủ hoặc ID không hợp lệ.';
                }
                break;

            case 'delete-category':
                $term_id = (int) ($_GET['item_id'] ?? 0);
                $default_cat = (int) get_option('default_category');
                if ($term_id && $term_id !== $default_cat) {
                    wp_delete_term($term_id, 'category');
                    clean_term_cache($term_id, 'category');
                    wp_cache_flush();
                    $result['message'] = "Đã xóa category ID {$term_id}.";
                } else {
                    $result['success'] = false;
                    $result['message'] = 'Không thể xóa danh mục mặc định.';
                }
                break;

            case 'delete-post':
                $post_id = (int) ($_GET['item_id'] ?? 0);
                if ($post_id) {
                    wp_delete_post($post_id, true);
                    $result['message'] = "Đã xóa post ID {$post_id}.";
                } else {
                    $result['success'] = false;
                    $result['message'] = 'ID không hợp lệ.';
                }
                break;

            default:
                $result['success'] = false;
                $result['message'] = 'Unknown action';
        }

        return $result;
    }
}

// Initialize dashboard
function sgh_dashboard() {
    return SGH_Dashboard::get_instance();
}
