<?php
/**
 * Bootstraps the Pi REST API gateway from the Pi API plugin.
 *
 * @package Pi\Api
 */

namespace PiApi;

if (!defined('ABSPATH')) {
    exit;
}

class ApiBootstrap {
    public static function init(): void {
        require_once PI_API_DIR . 'includes/api/class-api-controller.php';

        if (class_exists('\Pi_Api_Controller')) {
            \Pi_Api_Controller::get_instance()->init();
        }
    }
}
