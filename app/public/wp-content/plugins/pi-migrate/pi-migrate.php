<?php
/**
 * Plugin Name: Pi Migrate
 * Description: Full site migration (DB, uploads, search-replace) and generalized product/catalog import-export for Pi projects (saigonhoreca, saigonhouse).
 * Version: 1.0.0
 * Author: Pi
 * Text Domain: pi-migrate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PI_MIGRATE_VERSION', '1.0.0' );
define( 'PI_MIGRATE_DIR', plugin_dir_path( __FILE__ ) );
define( 'PI_MIGRATE_URL', plugin_dir_url( __FILE__ ) );

require_once PI_MIGRATE_DIR . 'includes/pix/class-pix-schema.php';
require_once PI_MIGRATE_DIR . 'includes/pix/class-pix-validator.php';
require_once PI_MIGRATE_DIR . 'includes/pix/class-pix-folder-writer.php';
require_once PI_MIGRATE_DIR . 'includes/pix/class-pix-folder-reader.php';
require_once PI_MIGRATE_DIR . 'includes/pix/class-pix-folder-importer.php';
require_once PI_MIGRATE_DIR . 'includes/class-db-migrator.php';
require_once PI_MIGRATE_DIR . 'includes/class-file-sync.php';
require_once PI_MIGRATE_DIR . 'includes/class-catalog-importer.php';
require_once PI_MIGRATE_DIR . 'includes/class-site-package.php';
require_once PI_MIGRATE_DIR . 'includes/class-rest.php';
require_once PI_MIGRATE_DIR . 'includes/class-admin.php';

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once PI_MIGRATE_DIR . 'includes/class-cli.php';
	WP_CLI::add_command( 'pi-migrate', 'Pi_Migrate_CLI' );
}

add_action( 'rest_api_init', array( 'Pi_Migrate_Rest', 'register_routes' ) );
add_action( 'admin_menu', array( 'Pi_Migrate_Admin', 'register_menu' ) );
add_action( 'admin_enqueue_scripts', array( 'Pi_Migrate_Admin', 'enqueue_assets' ) );
