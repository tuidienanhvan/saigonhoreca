<?php
/**
 * WP-CLI commands for pi-api. Provides DB migration, file sync,
 * site packaging, catalog import/export, and PIX-1 folder import/export.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Pi_Api_CLI {

	/**
	 * Export the database to a SQL file.
	 *
	 * ## OPTIONS
	 * [--file=<path>]
	 *
	 * ## EXAMPLES
	 *     wp pi-migrate db-export --file=backup.sql
	 */
	public function db_export( $args, $assoc_args ) {
		$file = $assoc_args['file'] ?? 'pi-migrate-db-' . gmdate( 'Ymd-His' ) . '.sql';
		$result = Pi_Api_DB_Migrator::export( $file );
		$this->finish( $result, "Database exported to {$file}" );
	}

	/**
	 * Import a database SQL file.
	 *
	 * ## OPTIONS
	 * <file>
	 *
	 * ## EXAMPLES
	 *     wp pi-migrate db-import backup.sql
	 */
	public function db_import( $args ) {
		$result = Pi_Api_DB_Migrator::import( $args[0] );
		$this->finish( $result, "Database imported from {$args[0]}" );
	}

	/**
	 * Serialize-safe search-replace across all tables.
	 *
	 * ## OPTIONS
	 * <old>
	 * <new>
	 * [--dry-run]
	 *
	 * ## EXAMPLES
	 *     wp pi-migrate search-replace https://staging.saigonhoreca.com https://saigonhoreca.com
	 */
	public function search_replace( $args, $assoc_args ) {
		list( $old, $new ) = $args;
		$result = Pi_Api_DB_Migrator::search_replace( $old, $new, isset( $assoc_args['dry-run'] ) );
		$this->finish( $result, 'Search-replace complete' );
	}

	/**
	 * Sync a directory (default: uploads) to a destination directory.
	 *
	 * ## OPTIONS
	 * <dest>
	 * [--source=<path>]
	 * [--dry-run]
	 *
	 * ## EXAMPLES
	 *     wp pi-migrate files-sync "C:\Users\Administrator\Local Sites\saigonhouse\app\public\wp-content\uploads"
	 */
	public function files_sync( $args, $assoc_args ) {
		$dest   = $args[0];
		$source = $assoc_args['source'] ?? wp_upload_dir()['basedir'];
		$result = Pi_Api_File_Sync::sync_directory( $source, $dest, isset( $assoc_args['dry-run'] ) );
		$this->finish( $result, "Synced {$source} -> {$dest}" );
	}

	/**
	 * Import a product/catalog data source: a directory of brand folders
	 * (each with data.json, the saigonhoreca layout) or a single JSON/CSV file.
	 *
	 * ## OPTIONS
	 * <path>
	 * [--post-type=<type>]
	 * [--brand=<slugs>]
	 * [--publish]
	 * [--dry-run]
	 * [--conflict-mode=<mode>]
	 *
	 * ## EXAMPLES
	 *     wp pi-migrate catalog-import docs/product-catalog --publish
	 *     wp pi-migrate catalog-import docs/product-catalog --brand=rational,vulcan
	 */
	public function catalog_import( $args, $assoc_args ) {
		$brands = isset( $assoc_args['brand'] ) ? array_map( 'trim', explode( ',', $assoc_args['brand'] ) ) : array();

		$result = Pi_Api_Catalog_Importer::run( $args[0], array(
			'post_type'     => $assoc_args['post-type'] ?? 'product',
			'brands'        => $brands,
			'publish'       => isset( $assoc_args['publish'] ),
			'dry_run'       => isset( $assoc_args['dry-run'] ),
			'conflict_mode' => $assoc_args['conflict-mode'] ?? 'update',
		) );
		$this->finish( $result, 'Catalog import complete' );
	}

	/**
	 * Export a post type back out to JSON (for backup / round-trip).
	 *
	 * ## OPTIONS
	 * <post-type>
	 * [--file=<path>]
	 */
	public function catalog_export( $args, $assoc_args ) {
		$file   = $assoc_args['file'] ?? "pi-migrate-{$args[0]}-" . gmdate( 'Ymd-His' ) . '.json';
		$result = Pi_Api_Catalog_Importer::export( $args[0], $file );
		$this->finish( $result, "Exported to {$file}" );
	}

	/**
	 * Copy SEO values out of the dead meta keys into the ones the theme reads.
	 *
	 * `_seo_title`/`_seo_description`/`_og_image` are never read (the only
	 * resolver, inc/site/pi-seo-resolver.php, reads `_pi_seo_*`/`_pi_og_*`), so
	 * products imported by older tooling hold their SEO text in a key nothing
	 * renders. This copies it across WITHOUT overwriting an existing canonical
	 * value, so it is safe to run before `clean-dead-meta`.
	 *
	 * `_og_image` is deliberately NOT copied when it merely repeats the featured
	 * image (it does on every catalog row) or when it holds a relative path,
	 * since og:image needs an absolute URL and the renderer already falls back
	 * to the featured image.
	 *
	 * ## OPTIONS
	 * [--post-type=<type>]
	 * [--dry-run]
	 *
	 * ## EXAMPLES
	 *     wp pi-migrate migrate-seo-meta --post-type=product --dry-run
	 */
	public function migrate_seo_meta( $args, $assoc_args ) {
		$post_type = $assoc_args['post-type'] ?? 'product';
		$dry_run   = isset( $assoc_args['dry-run'] );

		$map = array(
			'_seo_title'       => '_pi_seo_title',
			'_seo_description' => '_pi_seo_description',
			'_seo_desc'        => '_pi_seo_description',
			'_og_title'        => '_pi_og_title',
			'_og_description'  => '_pi_og_description',
		);

		// 'any' in WP_Query excludes statuses flagged exclude_from_search — i.e.
		// trash and auto-draft — so it silently skips trashed products that still
		// hold SEO text. List the statuses explicitly instead.
		$ids = get_posts( array(
			'post_type'   => $post_type,
			'post_status' => array( 'publish', 'draft', 'pending', 'private', 'future', 'trash', 'inherit' ),
			'numberposts' => -1,
			'fields'      => 'ids',
		) );

		$copied  = array();
		$present = 0;
		$skipped = 0;

		foreach ( $ids as $id ) {
			foreach ( $map as $old => $new ) {
				$old_val = get_post_meta( $id, $old, true );
				if ( '' === $old_val || null === $old_val ) {
					continue;
				}
				$present++;
				if ( '' !== (string) get_post_meta( $id, $new, true ) ) {
					$skipped++;
					continue;
				}
				if ( ! $dry_run ) {
					update_post_meta( $id, $new, $old_val );
				}
				$copied[ $new ] = ( $copied[ $new ] ?? 0 ) + 1;
			}
		}

		$this->finish(
			array(
				'dry_run'            => $dry_run,
				'post_type'          => $post_type,
				'posts_scanned'      => count( $ids ),
				'dead_values_found'  => $present,
				'already_had_target' => $skipped,
				'copied'             => $copied,
			),
			$dry_run ? 'SEO meta migration preview' : 'SEO meta migrated to canonical keys'
		);
	}

	/**
	 * Delete meta keys that no template on either site reads.
	 *
	 * Run `migrate-seo-meta` FIRST — otherwise SEO text that exists only in the
	 * dead keys is lost.
	 *
	 * Earlier catalog imports (this plugin's own older builds, and the theme's
	 * docs/product-catalog/import_via_pi.php) wrote `_seo_*`, `_og_image` and
	 * `_attr_*`/`_attribute_*` meta. The only SEO resolver is
	 * inc/site/pi-seo-resolver.php, which reads the `_pi_seo_*`/`_pi_og_*` prefix
	 * exclusively, and no template reads the attribute keys at all — brand comes
	 * from the product_brand taxonomy. pi-api never deletes stale meta on update,
	 * so those rows survive re-imports. This removes them.
	 *
	 * ## OPTIONS
	 * [--post-type=<type>]
	 * [--dry-run]
	 *
	 * ## EXAMPLES
	 *     wp pi-migrate clean-dead-meta --post-type=product --dry-run
	 */
	public function clean_dead_meta( $args, $assoc_args ) {
		global $wpdb;
		$post_type = $assoc_args['post-type'] ?? 'product';
		$dry_run   = isset( $assoc_args['dry-run'] );

		$exact  = array( '_seo_title', '_seo_description', '_seo_desc', '_og_image', '_og_image_url', '_pi_api_brand' );
		$prefix = array( '_attr_', '_attribute_' );

		$where = array();
		foreach ( $exact as $k ) {
			$where[] = $wpdb->prepare( 'pm.meta_key = %s', $k );
		}
		foreach ( $prefix as $p ) {
			$where[] = $wpdb->prepare( 'pm.meta_key LIKE %s', $wpdb->esc_like( $p ) . '%' );
		}

		$sql = "SELECT pm.meta_id, pm.post_id, pm.meta_key FROM {$wpdb->postmeta} pm
			INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
			WHERE p.post_type = %s AND ( " . implode( ' OR ', $where ) . ' )';
		$rows = $wpdb->get_results( $wpdb->prepare( $sql, $post_type ), ARRAY_A );

		$by_key = array();
		foreach ( $rows as $r ) {
			$by_key[ $r['meta_key'] ] = ( $by_key[ $r['meta_key'] ] ?? 0 ) + 1;
		}

		if ( ! $dry_run ) {
			foreach ( $rows as $r ) {
				delete_metadata_by_mid( 'post', $r['meta_id'] );
			}
		}

		$this->finish(
			array( 'dry_run' => $dry_run, 'post_type' => $post_type, 'rows' => count( $rows ), 'by_key' => $by_key ),
			$dry_run ? count( $rows ) . ' dead meta rows found (dry run)' : count( $rows ) . ' dead meta rows deleted'
		);
	}

	/**
	 * Export a full site package (db + uploads + manifest) to a directory.
	 *
	 * ## OPTIONS
	 * [--output-dir=<path>]
	 */
	public function site_export( $args, $assoc_args ) {
		$output_dir = $assoc_args['output-dir'] ?? 'pi-migrate-site-' . gmdate( 'Ymd-His' );
		$result     = Pi_Api_Site_Package::export( $output_dir );
		$this->finish( $result, "Site exported to {$output_dir}" );
	}

	/**
	 * Import a full site package produced by site-export.
	 *
	 * ## OPTIONS
	 * <source-dir>
	 * [--new-url=<url>]
	 */
	public function site_import( $args, $assoc_args ) {
		$result = Pi_Api_Site_Package::import( $args[0], $assoc_args['new-url'] ?? null );
		$this->finish( $result, "Site imported from {$args[0]}" );
	}

	/**
	 * Export one post as a PIX-1 folder ({slug}/post.json + images/).
	 *
	 * ## OPTIONS
	 * <post-id>
	 * [--out=<dir>]
	 * [--site=<site_key>]
	 *
	 * ## EXAMPLES
	 *     wp pi-migrate pix-export-post 4452 --out=exports/ --site=saigonhoreca
	 */
	public function pix_export_post( $args, $assoc_args ) {
		$out = rtrim( $assoc_args['out'] ?? 'pix-export-' . gmdate( 'Ymd-His' ), '/\\' );
		wp_mkdir_p( $out );
		$result = Pi_Pix_Folder_Writer::export_post( (int) $args[0], $out, $assoc_args['site'] ?? 'saigonhoreca' );
		$this->finish( $result, is_wp_error( $result ) ? '' : "Exported to {$result['dir']}" );
	}

	/**
	 * Export many posts as a PIX-1 folder-of-folders.
	 *
	 * ## OPTIONS
	 * [--post-type=<type>]
	 * [--post-ids=<ids>]
	 * [--out=<dir>]
	 * [--name=<name>]
	 * [--site=<site_key>]
	 *
	 * ## EXAMPLES
	 *     wp pi-migrate pix-export-many --post-type=product --out=exports/ --name=anfim-batch
	 *     wp pi-migrate pix-export-many --post-ids=4452,4453,4454 --out=exports/
	 */
	public function pix_export_many( $args, $assoc_args ) {
		if ( ! empty( $assoc_args['post-ids'] ) ) {
			$post_ids = array_map( 'intval', explode( ',', $assoc_args['post-ids'] ) );
		} else {
			$post_ids = get_posts( array(
				'post_type'      => $assoc_args['post-type'] ?? 'product',
				'post_status'    => 'any',
				'numberposts'    => -1,
				'fields'         => 'ids',
			) );
		}
		$out  = rtrim( $assoc_args['out'] ?? 'pix-export-' . gmdate( 'Ymd-His' ), '/\\' );
		$name = $assoc_args['name'] ?? ( $assoc_args['post-type'] ?? 'export' ) . '-' . gmdate( 'Ymd-His' );
		wp_mkdir_p( $out );
		$result = Pi_Pix_Folder_Writer::export_many( $post_ids, $out, $name, $assoc_args['site'] ?? 'saigonhoreca' );
		$this->finish( $result, is_wp_error( $result ) ? '' : "Exported {$result['post_count']} posts to {$result['dir']}" );
	}

	/**
	 * Validate a PIX-1 folder export without importing anything.
	 *
	 * ## OPTIONS
	 * <dir>
	 * [--site=<site_key>]
	 *
	 * ## EXAMPLES
	 *     wp pi-migrate pix-validate-folder exports/anfim-batch --site=saigonhoreca
	 */
	public function pix_validate_folder( $args, $assoc_args ) {
		$site   = $assoc_args['site'] ?? 'saigonhoreca';
		$result = Pi_Pix_Folder_Reader::validate( $args[0], $site );
		$this->finish( $result, is_wp_error( $result ) ? '' : ( $result['ok'] ? 'Bundle is valid' : ( count( $result['errors'] ) . ' error(s) found' ) ) );
	}

	/**
	 * Import a PIX-1 folder export (single post.json folder, or a batch folder
	 * with a manifest.json). Always validates first; refuses to write anything
	 * if validation fails.
	 *
	 * ## OPTIONS
	 * <dir>
	 * [--site=<site_key>]
	 * [--conflict-mode=<mode>]
	 * [--dry-run]
	 * [--no-suspend-translation]
	 *
	 * ## EXAMPLES
	 *     wp pi-migrate pix-import-folder exports/anfim-batch --site=saigonhoreca --dry-run
	 */
	public function pix_import_folder( $args, $assoc_args ) {
		$result = Pi_Pix_Folder_Importer::import( $args[0], $assoc_args['site'] ?? 'saigonhoreca', array(
			'conflict_mode'       => $assoc_args['conflict-mode'] ?? 'update',
			'dry_run'             => isset( $assoc_args['dry-run'] ),
			'suspend_translation' => ! isset( $assoc_args['no-suspend-translation'] ),
		) );
		$this->finish( $result, is_wp_error( $result ) ? '' : ( $result['ok'] ? 'Import complete' : 'Import finished with errors' ) );
	}

	private function finish( $result, $success_message ) {
		if ( is_wp_error( $result ) ) {
			WP_CLI::error( $result->get_error_message() );
			return;
		}
		WP_CLI::success( $success_message );
		WP_CLI::log( wp_json_encode( $result, JSON_PRETTY_PRINT ) );
	}
}
