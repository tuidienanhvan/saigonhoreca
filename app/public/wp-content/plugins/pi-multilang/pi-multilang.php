<?php
/**
 * Plugin Name: Pi Multilang
 * Description: Hỗ trợ đa ngôn ngữ config-driven theo domain, tự động dịch LLM7 lưu draft, SEO tags và lọc query.
 * Version: 1.0.0
 * Author: Pi
 */

if (!defined('ABSPATH')) exit;

define('PI_MULTILANG_DIR', plugin_dir_path(__FILE__));
define('PI_MULTILANG_URL', plugin_dir_url(__FILE__));

require_once PI_MULTILANG_DIR . 'includes/class-core.php';
require_once PI_MULTILANG_DIR . 'includes/class-meta.php';
require_once PI_MULTILANG_DIR . 'includes/class-frontend.php';
require_once PI_MULTILANG_DIR . 'includes/class-seo.php';
require_once PI_MULTILANG_DIR . 'includes/class-admin.php';
require_once PI_MULTILANG_DIR . 'includes/class-translation.php';

Pi_Multilang_Core::init();

add_action('plugins_loaded', function() {
    Pi_Multilang_Meta::init();
    Pi_Multilang_Frontend::init();
    Pi_Multilang_SEO::init();
    Pi_Multilang_Admin::init();
    Pi_Multilang_Translation::init();
});
