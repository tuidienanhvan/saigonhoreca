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

// 2026-07-29 EMERGENCY GUARD (unrelated to this session's own work — found the
// live site down with a fatal "Cannot redeclare class Pi_Pix_Schema"):
// pi-api/pi-api.php now ALSO requires an identical byte-for-byte copy of
// these same 5 files, with its own comment stating "moved here from the
// pi-migrate plugin 2026-07-29" — i.e. a migration OUT of this plugin was
// apparently started concurrently (a different session on this shared
// machine?) but this side was never updated to stop loading its own copy,
// so both plugins declared the same classes and PHP fataled on whichever
// loaded second. class_exists() guards restore the site immediately without
// deleting anything or guessing which copy is "right" — once whoever is
// doing that migration finishes and removes pi-migrate's ownership of these
// files for real, these guards become inert and can be deleted along with
// the files themselves.
if (!class_exists('Pi_Pix_Schema')) {
    require_once PI_MIGRATE_DIR . 'includes/pix/class-pix-schema.php';
}
if (!class_exists('Pi_Pix_Validator')) {
    require_once PI_MIGRATE_DIR . 'includes/pix/class-pix-validator.php';
}
if (!class_exists('Pi_Pix_Folder_Writer')) {
    require_once PI_MIGRATE_DIR . 'includes/pix/class-pix-folder-writer.php';
}
if (!class_exists('Pi_Pix_Folder_Reader')) {
    require_once PI_MIGRATE_DIR . 'includes/pix/class-pix-folder-reader.php';
}
if (!class_exists('Pi_Pix_Folder_Importer')) {
    require_once PI_MIGRATE_DIR . 'includes/pix/class-pix-folder-importer.php';
}
require_once PI_MIGRATE_DIR . 'includes/class-db-migrator.php';
require_once PI_MIGRATE_DIR . 'includes/class-file-sync.php';
require_once PI_MIGRATE_DIR . 'includes/class-catalog-importer.php';
require_once PI_MIGRATE_DIR . 'includes/class-site-package.php';
require_once PI_MIGRATE_DIR . 'includes/class-rest.php';
require_once PI_MIGRATE_DIR . 'includes/class-admin.php';

// One-off HTTP admin tools (moved from saigonhoreca-theme 2026-07-27 — không
// phải logic theme, thuộc về công cụ migrate/import dữ liệu).
require_once PI_MIGRATE_DIR . 'includes/tools/import-content.php';       // ?sgh_import_content=1
require_once PI_MIGRATE_DIR . 'includes/tools/reimport-products.php';   // ?sgh_reimport_products=1
require_once PI_MIGRATE_DIR . 'includes/tools/recrawl-posts.php';       // ?sgh_recrawl_posts=1
require_once PI_MIGRATE_DIR . 'includes/tools/upload-zip.php';          // ?sgh_upload_zip=1

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once PI_MIGRATE_DIR . 'includes/class-cli.php';
	WP_CLI::add_command( 'pi-migrate', 'Pi_Migrate_CLI' );
}

add_action( 'rest_api_init', array( 'Pi_Migrate_Rest', 'register_routes' ) );
add_action( 'admin_menu', array( 'Pi_Migrate_Admin', 'register_menu' ) );
add_action( 'admin_enqueue_scripts', array( 'Pi_Migrate_Admin', 'enqueue_assets' ) );
