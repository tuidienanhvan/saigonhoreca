<?php
/**
 * Full-site package: bundles a DB export + uploads zip + manifest.json
 * into one folder (or zip), and can restore from one. This is the
 * "migrate whole site between environments" half of pi-migrate.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Pi_Api_Site_Package {

	public static function export( $output_dir ) {
		wp_mkdir_p( $output_dir );
		$output_dir = rtrim( $output_dir, '/\\' );

		$db_result = Pi_Api_DB_Migrator::export( $output_dir . '/database.sql' );
		if ( is_wp_error( $db_result ) ) {
			return $db_result;
		}

		$uploads_result = Pi_Api_File_Sync::zip_uploads( $output_dir . '/uploads.zip' );
		if ( is_wp_error( $uploads_result ) ) {
			return $uploads_result;
		}

		$manifest = array(
			'site_url'      => site_url(),
			'home_url'      => home_url(),
			'wp_version'    => get_bloginfo( 'version' ),
			'exported_at'   => gmdate( 'c' ),
			'pi_api'    => pi_api_VERSION,
			'db'            => $db_result,
			'uploads'       => $uploads_result,
			'active_plugins'=> get_option( 'active_plugins', array() ),
			'active_theme'  => get_stylesheet(),
		);
		file_put_contents( $output_dir . '/manifest.json', wp_json_encode( $manifest, JSON_PRETTY_PRINT ) );

		return array( 'output_dir' => $output_dir, 'manifest' => $manifest );
	}

	/**
	 * @param string $source_dir     Folder produced by export(), containing database.sql + uploads.zip + manifest.json
	 * @param string $new_site_url   If set, runs search-replace old site_url -> new_site_url after DB import.
	 */
	public static function import( $source_dir, $new_site_url = null ) {
		$source_dir = rtrim( $source_dir, '/\\' );
		$manifest_path = $source_dir . '/manifest.json';
		if ( ! file_exists( $manifest_path ) ) {
			return new WP_Error( 'pi_api_no_manifest', "manifest.json not found in {$source_dir}" );
		}
		$manifest = json_decode( file_get_contents( $manifest_path ), true );

		$db_result = Pi_Api_DB_Migrator::import( $source_dir . '/database.sql' );
		if ( is_wp_error( $db_result ) ) {
			return $db_result;
		}

		$uploads_result = null;
		if ( file_exists( $source_dir . '/uploads.zip' ) ) {
			$uploads_result = Pi_Api_File_Sync::unzip_uploads( $source_dir . '/uploads.zip' );
			if ( is_wp_error( $uploads_result ) ) {
				return $uploads_result;
			}
		}

		$search_replace_result = null;
		if ( $new_site_url && ! empty( $manifest['site_url'] ) && $manifest['site_url'] !== $new_site_url ) {
			$search_replace_result = Pi_Api_DB_Migrator::search_replace( $manifest['site_url'], $new_site_url );
		}

		return array(
			'db'             => $db_result,
			'uploads'        => $uploads_result,
			'search_replace' => $search_replace_result,
			'source_manifest'=> $manifest,
		);
	}
}
