<?php
/**
 * REST surface for triggering pi-migrate actions from the admin UI (no WP-CLI/shell access needed).
 * Namespace: pi-migrate/v1. All routes require manage_options.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Pi_Migrate_Rest {

	const NS = 'pi-migrate/v1';

	public static function register_routes() {
		$permission = function () {
			return current_user_can( 'manage_options' );
		};

		register_rest_route( self::NS, '/db/export', array(
			'methods'             => 'POST',
			'permission_callback' => $permission,
			'callback'            => function ( $req ) {
				$file = $req->get_param( 'file' ) ?: wp_upload_dir()['basedir'] . '/../pi-migrate-db-' . gmdate( 'Ymd-His' ) . '.sql';
				return self::respond( Pi_Migrate_DB_Migrator::export( $file ) );
			},
		) );

		register_rest_route( self::NS, '/search-replace', array(
			'methods'             => 'POST',
			'permission_callback' => $permission,
			'callback'            => function ( $req ) {
				return self::respond( Pi_Migrate_DB_Migrator::search_replace(
					$req->get_param( 'old' ),
					$req->get_param( 'new' ),
					(bool) $req->get_param( 'dry_run' )
				) );
			},
		) );

		register_rest_route( self::NS, '/files/sync', array(
			'methods'             => 'POST',
			'permission_callback' => $permission,
			'callback'            => function ( $req ) {
				return self::respond( Pi_Migrate_File_Sync::sync_directory(
					$req->get_param( 'source' ) ?: wp_upload_dir()['basedir'],
					$req->get_param( 'dest' ),
					(bool) $req->get_param( 'dry_run' )
				) );
			},
		) );

		register_rest_route( self::NS, '/catalog/import', array(
			'methods'             => 'POST',
			'permission_callback' => $permission,
			'callback'            => function ( $req ) {
				return self::respond( Pi_Migrate_Catalog_Importer::run( $req->get_param( 'path' ), array(
					'post_type'     => $req->get_param( 'post_type' ) ?: 'product',
					'brands'        => $req->get_param( 'brands' ) ?: array(),
					'publish'       => (bool) $req->get_param( 'publish' ),
					'dry_run'       => (bool) $req->get_param( 'dry_run' ),
					'conflict_mode' => $req->get_param( 'conflict_mode' ) ?: 'update',
				) ) );
			},
		) );

		register_rest_route( self::NS, '/site/export', array(
			'methods'             => 'POST',
			'permission_callback' => $permission,
			'callback'            => function ( $req ) {
				$dir = $req->get_param( 'output_dir' ) ?: sys_get_temp_dir() . '/pi-migrate-site-' . gmdate( 'Ymd-His' );
				return self::respond( Pi_Migrate_Site_Package::export( $dir ) );
			},
		) );

		register_rest_route( self::NS, '/site/import', array(
			'methods'             => 'POST',
			'permission_callback' => $permission,
			'callback'            => function ( $req ) {
				return self::respond( Pi_Migrate_Site_Package::import(
					$req->get_param( 'source_dir' ),
					$req->get_param( 'new_url' )
				) );
			},
		) );

		register_rest_route( self::NS, '/status', array(
			'methods'             => 'GET',
			'permission_callback' => $permission,
			'callback'            => function () {
				return self::respond( array(
					'version'        => PI_MIGRATE_VERSION,
					'pi_api_active'  => class_exists( 'Pi_Api_ImportExport' ),
					'wp_cli_context' => defined( 'WP_CLI' ) && WP_CLI,
					'site_url'       => site_url(),
				) );
			},
		) );
	}

	private static function respond( $result ) {
		if ( is_wp_error( $result ) ) {
			return new WP_REST_Response( array( 'error' => $result->get_error_message() ), 400 );
		}
		return new WP_REST_Response( $result, 200 );
	}
}
