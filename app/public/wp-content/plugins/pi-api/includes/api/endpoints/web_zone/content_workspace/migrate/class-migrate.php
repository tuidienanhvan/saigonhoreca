<?php
/**
 * Pi API Import/Export Endpoints — PIX-1.
 *
 * Generic post import/export for every entity pi-api knows about (post, page,
 * product, project, attachment). Backs the dashboard/web-app's "migrate
 * content" feature and is the home of the long-referenced-but-never-defined
 * `Pi_Api_ImportExport::import_post_list()` static method that the theme
 * (docs/product-catalog/import_via_pi.php) and the legacy pi-migrate catalog
 * importer both call.
 *
 * Transport
 * ─────────────────────────────────────────────────────────────
 *   GET    /pi/v1/migrate/schema              PIX-1 schema doc (for rendering forms)
 *   POST   /pi/v1/migrate/posts/export        Export posts → ZIP bundle (Phase 3)
 *   POST   /pi/v1/migrate/posts/import        Import from ZIP / JSON / CSV (Phase 3-4)
 *   POST   /pi/v1/migrate/posts/validate      Validate-only (dry-run, no writes)
 *   POST   /pi/v1/migrate/posts/import-list   Direct in-body import (the import_post_list
 *                                             surface, accepts the legacy catalog shape)
 *
 * This file lives at endpoints/content_workspace/migrate/ so pi-api's glob
 * loader auto-requires it; the route registration still needs the
 * 'migrate' => 'Pi_Api_Migrate' entry in class-api-controller.php's $groups
 * array — without that line these routes 404.
 */

if (!defined('ABSPATH')) exit;

class Pi_Api_Migrate {

    /**
     * Detect the site_key from the current site URL. PIX-1 maps entities and
     * vocabularies per-site, so every endpoint needs to know which site it is
     * operating on. Defaults to the first known site when nothing matches.
     */
    private static function site_key() {
        $host = wp_parse_url(site_url(), PHP_URL_HOST);
        foreach (Pi_Pix_Schema::sites() as $site_key) {
            if (false !== strpos($site_key, $host) || false !== strpos($host, $site_key)) {
                return $site_key;
            }
        }
        $sites = Pi_Pix_Schema::sites();
        return $sites[0] ?? 'saigonhoreca';
    }

    /* ─────────────────────────── route registration ────────────────────── */
    public static function register_routes($ns, $admin) {
        register_rest_route($ns, '/migrate/schema', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'get_schema'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/migrate/posts/validate', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'validate_bundle'],
            'permission_callback' => $admin,
        ]);

        register_rest_route($ns, '/migrate/posts/import-list', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'import_list_route'],
            'permission_callback' => $admin,
        ]);

        // Phase 3+: ZIP/JSON/CSV export/import handlers.
        register_rest_route($ns, '/migrate/posts/export', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'export_posts'],
            'permission_callback' => $admin,
        ]);
        register_rest_route($ns, '/migrate/posts/download', [
            'methods'             => 'GET',
            'callback'            => [self::class, 'download_export'],
            'permission_callback' => $admin,
        ]);
        register_rest_route($ns, '/migrate/posts/upload', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'upload_bundle'],
            'permission_callback' => $admin,
        ]);
        register_rest_route($ns, '/migrate/posts/import', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'import_posts'],
            'permission_callback' => $admin,
        ]);
        register_rest_route($ns, '/migrate/posts/import-chunk', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'import_chunk'],
            'permission_callback' => $admin,
        ]);

        // Phase 5: DB migration routes.
        register_rest_route($ns, '/db/export', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'db_export'],
            'permission_callback' => $admin,
        ]);
        register_rest_route($ns, '/db/import', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'db_import'],
            'permission_callback' => $admin,
        ]);
        register_rest_route($ns, '/db/search-replace', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'db_search_replace'],
            'permission_callback' => $admin,
        ]);

        // Phase 5: File sync routes.
        register_rest_route($ns, '/files/sync', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'files_sync'],
            'permission_callback' => $admin,
        ]);
        register_rest_route($ns, '/files/zip-uploads', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'files_zip_uploads'],
            'permission_callback' => $admin,
        ]);
        register_rest_route($ns, '/files/unzip-uploads', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'files_unzip_uploads'],
            'permission_callback' => $admin,
        ]);

        // Phase 5: Site package routes.
        register_rest_route($ns, '/site/export', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'site_export'],
            'permission_callback' => $admin,
        ]);
        register_rest_route($ns, '/site/import', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'site_import'],
            'permission_callback' => $admin,
        ]);

        // Phase 5: Catalog import route.
        register_rest_route($ns, '/catalog/import', [
            'methods'             => 'POST',
            'callback'            => [self::class, 'catalog_import'],
            'permission_callback' => $admin,
        ]);
    }

    /* ─────────────────────────────── schema ────────────────────────────── */

    /**
     * UC-10: return the PIX-1 schema so the dashboard can render its import
     * form without hardcoding field names. Pure data — no DB.
     */
    public static function get_schema(\WP_REST_Request $req) {
        $site_key = self::site_key();
        $entities = [];
        foreach (Pi_Pix_Schema::entities() as $entity) {
            $pt = Pi_Pix_Schema::post_type_for($entity, $site_key);
            $entities[$entity] = [
                'post_type'     => $pt,
                'fields'        => Pi_Pix_Schema::field_map($entity),
                'vocabularies'  => [],
            ];
        }
        // Resolve which vocabularies are available for each entity on this site.
        foreach ($entities as $entity => &$info) {
            foreach (['category', 'brand', 'tag'] as $vocab) {
                $tax = Pi_Pix_Schema::taxonomy_for($vocab, $site_key, $entity);
                if (null !== $tax) {
                    $info['vocabularies'][$vocab] = $tax;
                }
            }
        }
        unset($info);

        return new \WP_REST_Response([
            'pix'        => Pi_Pix_Schema::VERSION,
            'site_key'   => $site_key,
            'site_url'   => site_url(),
            'entities'   => $entities,
            'seo_fields' => Pi_Pix_Schema::seo_map(),
            'statuses'   => Pi_Pix_Schema::STATUSES,
            'languages'  => Pi_Pix_Schema::LANGUAGES,
            'lifecycles' => Pi_Pix_Schema::LIFECYCLES,
        ], 200);
    }

    /* ───────────────────────── validate (UC-06) ────────────────────────── */

    /**
     * Accepts an already-built PIX envelope in the request body (the shape
     * Pi_Pix_Folder_Reader::build_envelope produces) and runs the validator
     * without writing anything. For validating a ZIP/folder/CSV on disk, the
     * caller resolves it to an envelope first via the Phase 3/4 readers.
     */
    public static function validate_bundle(\WP_REST_Request $req) {
        $envelope = $req->get_json_params();
        if (!is_array($envelope) || !isset($envelope['records'])) {
            return new \WP_REST_Response(['error' => 'request body must be a PIX envelope { pix, records, assets, checksum }'], 400);
        }
        $site_key = $envelope['target_hint']['site_key'] ?? self::site_key();

        $validator = new Pi_Pix_Validator($site_key);
        $report    = $validator->validate($envelope);
        return new \WP_REST_Response($report, $report['ok'] ? 200 : 422);
    }

    /* ─────────────── import_post_list (the legacy contract) ────────────── */

    /**
     * Adapter that turns the legacy pi-migrate/catalog importer's flat item
     * shape into a PIX envelope and runs it through the PIX importer, so the
     * theme's docs/product-catalog/import_via_pi.php and the old catalog
     * importer both "just work" against pi-api now.
     *
     * Input item shape (from normalize_item / import_via_pi.php):
     *   title, slug, type, status, content|content_html, excerpt,
     *   meta { key => scalar }, terms [ {taxonomy, slug, name} ],
     *   featured_image_url (optional, remote URL).
     *
     * Returns the contract the theme expects:
     *   ['imported' => int, 'updated' => int, 'errors' => int, ...]
     */
    public static function import_post_list(array $posts, $conflict_mode = 'update') {
        if (empty($posts)) {
            return ['imported' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => 0, 'engine' => 'pix-1'];
        }

        $site_key = self::site_key();
        $envelope = self::build_envelope_from_items($posts, $site_key);

        $validator = new Pi_Pix_Validator($site_key);
        $report    = $validator->validate($envelope);
        if (!$report['ok']) {
            // Don't hard-fail the legacy caller on warnings (it has none today);
            // surface ERROR findings as a count so import_via_pi.php's
            // error tally stays meaningful without crashing.
            return [
                'imported' => 0,
                'updated'  => 0,
                'skipped'  => 0,
                'errors'   => count($envelope['records']),
                'engine'   => 'pix-1',
                'validation' => $report,
            ];
        }

        // The PIX importer wants a folder-style envelope with a file_map for
        // assets. Legacy items carry only remote URLs (no local files), so we
        // sideload those on demand via a synthetic file_map that the importer
        // treats as missing-paths (it already tolerates that for remote refs).
        // To keep behavior aligned with the legacy importer (which downloaded
        // featured_image_url), we pre-download remote images into a temp dir
        // and point file_map at them.
        [$envelope, $file_map] = self::materialize_remote_assets($envelope);

        $result = self::run_pix_import($envelope, $site_key, [
            'conflict_mode'       => in_array($conflict_mode, ['update', 'skip', 'create'], true) ? $conflict_mode : 'update',
            'dry_run'             => false,
            'suspend_translation' => true,
        ], $file_map);

        return [
            'imported' => $result['created'] ?? 0,
            'updated'  => $result['updated'] ?? 0,
            'skipped'  => $result['skipped'] ?? 0,
            'errors'   => count($result['errors'] ?? []),
            'engine'   => 'pix-1',
            'assets'   => $result['assets'] ?? null,
            'warnings' => $result['validation_warnings'] ?? 0,
        ];
    }

    /**
     * REST wrapper around import_post_list for callers that want JSON in/out
     * instead of the static-method interface (e.g. the dashboard).
     *
     * Body: { "posts": [ {title, slug, type, ...} ], "conflict_mode": "update" }
     */
    public static function import_list_route(\WP_REST_Request $req) {
        $body           = $req->get_json_params();
        $posts          = $body['posts'] ?? [];
        $conflict_mode  = $body['conflict_mode'] ?? 'update';
        $dry_run        = !empty($body['dry_run']);

        if (!is_array($posts) || empty($posts)) {
            return new \WP_REST_Response(['error' => 'no posts provided (expected { posts: [...] })'], 400);
        }

        if ($dry_run) {
            // Dry-run: validate only, return the report + sample envelope.
            $site_key = self::site_key();
            $envelope = self::build_envelope_from_items($posts, $site_key);
            $validator = new Pi_Pix_Validator($site_key);
            return new \WP_REST_Response([
                'dry_run'  => true,
                'engine'   => 'pix-1',
                'count'    => count($posts),
                'validation' => $validator->validate($envelope),
                'sample'   => $envelope['records'][0] ?? null,
            ], 200);
        }

        $result = self::import_post_list($posts, $conflict_mode);
        return new \WP_REST_Response($result, 200);
    }

    /* ─────────────────────── Phase 5: DB migration ────────────────── */

    public static function db_export(\WP_REST_Request $req) {
        $file = $req->get_param('file') ?: wp_upload_dir()['basedir'] . '/../pi-api-db-' . gmdate('Ymd-His') . '.sql';
        $result = Pi_Api_DB_Migrator::export($file);
        if (is_wp_error($result)) {
            return new \WP_REST_Response(['error' => $result->get_error_message()], 400);
        }
        return new \WP_REST_Response($result, 200);
    }

    public static function db_import(\WP_REST_Request $req) {
        $file = $req->get_param('file');
        if (!$file || !file_exists($file)) {
            return new \WP_REST_Response(['error' => 'SQL file not found'], 400);
        }
        $result = Pi_Api_DB_Migrator::import($file);
        if (is_wp_error($result)) {
            return new \WP_REST_Response(['error' => $result->get_error_message()], 400);
        }
        return new \WP_REST_Response($result, 200);
    }

    public static function db_search_replace(\WP_REST_Request $req) {
        $old = $req->get_param('old');
        $new = $req->get_param('new');
        $dry_run = (bool) $req->get_param('dry_run');
        if (empty($old) || empty($new)) {
            return new \WP_REST_Response(['error' => 'both old and new parameters are required'], 400);
        }
        $result = Pi_Api_DB_Migrator::search_replace($old, $new, $dry_run);
        if (is_wp_error($result)) {
            return new \WP_REST_Response(['error' => $result->get_error_message()], 400);
        }
        return new \WP_REST_Response($result, 200);
    }

    /* ─────────────────────── Phase 5: File sync ────────────────── */

    public static function files_sync(\WP_REST_Request $req) {
        $source = $req->get_param('source') ?: wp_upload_dir()['basedir'];
        $dest = $req->get_param('dest');
        $dry_run = (bool) $req->get_param('dry_run');
        if (empty($dest)) {
            return new \WP_REST_Response(['error' => 'dest parameter is required'], 400);
        }
        $result = Pi_Api_File_Sync::sync_directory($source, $dest, $dry_run);
        if (is_wp_error($result)) {
            return new \WP_REST_Response(['error' => $result->get_error_message()], 400);
        }
        return new \WP_REST_Response($result, 200);
    }

    public static function files_zip_uploads(\WP_REST_Request $req) {
        $dest_zip = $req->get_param('dest_zip');
        $result = Pi_Api_File_Sync::zip_uploads($dest_zip);
        if (is_wp_error($result)) {
            return new \WP_REST_Response(['error' => $result->get_error_message()], 400);
        }
        return new \WP_REST_Response($result, 200);
    }

    public static function files_unzip_uploads(\WP_REST_Request $req) {
        $zip_path = $req->get_param('zip_path');
        $dest_dir = $req->get_param('dest_dir');
        if (empty($zip_path) || !file_exists($zip_path)) {
            return new \WP_REST_Response(['error' => 'zip_path not found'], 400);
        }
        $result = Pi_Api_File_Sync::unzip_uploads($zip_path, $dest_dir);
        if (is_wp_error($result)) {
            return new \WP_REST_Response(['error' => $result->get_error_message()], 400);
        }
        return new \WP_REST_Response($result, 200);
    }

    /* ─────────────────────── Phase 5: Site package ────────────── */

    public static function site_export(\WP_REST_Request $req) {
        $output_dir = $req->get_param('output_dir') ?: sys_get_temp_dir() . '/pi-api-site-' . gmdate('Ymd-His');
        $result = Pi_Api_Site_Package::export($output_dir);
        if (is_wp_error($result)) {
            return new \WP_REST_Response(['error' => $result->get_error_message()], 400);
        }
        return new \WP_REST_Response($result, 200);
    }

    public static function site_import(\WP_REST_Request $req) {
        $source_dir = $req->get_param('source_dir');
        $new_url = $req->get_param('new_url');
        if (empty($source_dir) || !file_exists($source_dir)) {
            return new \WP_REST_Response(['error' => 'source_dir not found'], 400);
        }
        $result = Pi_Api_Site_Package::import($source_dir, $new_url);
        if (is_wp_error($result)) {
            return new \WP_REST_Response(['error' => $result->get_error_message()], 400);
        }
        return new \WP_REST_Response($result, 200);
    }

    /* ─────────────────────── Phase 5: Catalog import ──────────── */

    public static function catalog_import(\WP_REST_Request $req) {
        $path = $req->get_param('path');
        if (empty($path) || !file_exists($path)) {
            return new \WP_REST_Response(['error' => 'path not found'], 400);
        }
        $args = [
            'post_type' => $req->get_param('post_type') ?: 'product',
            'brands' => $req->get_param('brands') ?: [],
            'publish' => (bool) $req->get_param('publish'),
            'dry_run' => (bool) $req->get_param('dry_run'),
            'conflict_mode' => $req->get_param('conflict_mode') ?: 'update',
        ];
        $result = Pi_Api_Catalog_Importer::run($path, $args);
        if (is_wp_error($result)) {
            return new \WP_REST_Response(['error' => $result->get_error_message()], 400);
        }
        return new \WP_REST_Response($result, 200);
    }

    public static function export_posts(\WP_REST_Request $req) {
		$post_ids = (array) ( $req->get_param( 'post_ids' ) ?? array() );
		$single_id = $req->get_param( 'single_id' );
		if ( ! empty( $single_id ) ) {
			$post_ids = array( (int) $single_id );
		}

		$post_type = $req->get_param( 'post_type' ) ?: 'product';
		
		if ( ! empty( $single_id ) ) {
			$post_obj = get_post( (int) $single_id );
			$slug = ( $post_obj && ! empty( $post_obj->post_name ) ) ? $post_obj->post_name : 'item-' . $single_id;
			$export_name = 'pix1-single-' . sanitize_file_name( $slug ) . '-' . gmdate( 'Ymd-His' );
		} else {
			$export_name = $req->get_param( 'export_name' ) ?: 'pix1-' . sanitize_file_name( $post_type ) . '-' . gmdate( 'Ymd-His' );
		}

		$dry_run = (bool) $req->get_param( 'dry_run' );

		if ( empty( $post_ids ) ) {
			$post_ids = get_posts( array(
				'post_type'      => $post_type,
				'post_status'    => 'any',
				'numberposts'    => -1,
				'fields'         => 'ids',
			) );
		}

		if ( empty( $post_ids ) ) {
			return new \WP_REST_Response( array( 'error' => 'no posts found to export' ), 400 );
		}

		$uploads = wp_upload_dir();
		$export_dir = trailingslashit( $uploads['basedir'] ) . 'pi-exports/';
		wp_mkdir_p( $export_dir );
		$site_key = self::site_key();

		if ( $dry_run ) {
			return new \WP_REST_Response( array(
				'dry_run' => true,
				'post_count' => count( $post_ids ),
				'post_type' => $post_type,
				'export_name' => $export_name,
			), 200 );
		}

		$result = Pi_Pix_Folder_Writer::export_zip( $post_ids, $export_dir, $export_name, $site_key );
		if ( is_wp_error( $result ) ) {
			return new \WP_REST_Response( array( 'error' => $result->get_error_message() ), 400 );
		}

		$zip_filename = basename( $result['zip'] );
		$download_url = trailingslashit( $uploads['baseurl'] ) . 'pi-exports/' . $zip_filename;
		$stream_url = rest_url( 'pi/v1/migrate/posts/download?file=' . rawurlencode( $zip_filename ) );

		return new \WP_REST_Response( array(
			'zip' => $result['zip'],
			'filename' => $zip_filename,
			'download_url' => $download_url,
			'stream_url' => $stream_url,
			'bytes' => $result['bytes'],
			'file_count' => $result['file_count'] ?? count( $post_ids ),
			'post_count' => $result['post_count'],
			'zip_sha256' => $result['zip_sha256'],
			'errors' => $result['errors'],
		), 200 );
	}

	public static function download_export(\WP_REST_Request $req) {
		$file_param = $req->get_param( 'file' );
		if ( empty( $file_param ) ) {
			return new \WP_REST_Response( array( 'error' => 'file parameter is required' ), 400 );
		}

		$filename = basename( $file_param );
		$uploads = wp_upload_dir();
		$export_path = trailingslashit( $uploads['basedir'] ) . 'pi-exports/' . $filename;
		$temp_path = trailingslashit( sys_get_temp_dir() ) . $filename;

		$target = null;
		if ( file_exists( $export_path ) ) {
			$target = $export_path;
		} elseif ( file_exists( $temp_path ) ) {
			$target = $temp_path;
		}

		if ( ! $target || ! file_exists( $target ) ) {
			return new \WP_REST_Response( array( 'error' => 'Export file not found' ), 404 );
		}

		header( 'Content-Description: File Transfer' );
		header( 'Content-Type: application/zip' );
		header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
		header( 'Content-Transfer-Encoding: binary' );
		header( 'Expires: 0' );
		header( 'Cache-Control: must-revalidate' );
		header( 'Pragma: public' );
		header( 'Content-Length: ' . filesize( $target ) );
		readfile( $target );
		exit;
	}

	public static function upload_bundle(\WP_REST_Request $req) {
		if ( empty( $_FILES['file'] ) ) {
			return new \WP_REST_Response( array( 'error' => 'No file uploaded. Please send a ZIP file.' ), 400 );
		}

		$file = $_FILES['file'];
		if ( ! empty( $file['error'] ) ) {
			return new \WP_REST_Response( array( 'error' => 'Upload error code: ' . $file['error'] ), 400 );
		}

		$ext = strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) );
		if ( 'zip' !== $ext && 'json' !== $ext ) {
			return new \WP_REST_Response( array( 'error' => 'Only .zip bundles are supported' ), 400 );
		}

		$uploads = wp_upload_dir();
		$import_dir = trailingslashit( $uploads['basedir'] ) . 'pi-imports/';
		wp_mkdir_p( $import_dir );
		$target_file = $import_dir . 'pix-upload-' . gmdate( 'YmdHis' ) . '-' . wp_generate_password( 6, false ) . '.' . $ext;

		if ( ! move_uploaded_file( $file['tmp_name'], $target_file ) ) {
			if ( ! copy( $file['tmp_name'], $target_file ) ) {
				return new \WP_REST_Response( array( 'error' => 'Failed to save uploaded file' ), 500 );
			}
		}

		$site_key = self::site_key();
		$sha256 = hash_file( 'sha256', $target_file );

		if ( 'zip' === $ext ) {
			if ( ! class_exists( 'ZipArchive' ) ) {
				return new \WP_REST_Response( array( 'error' => 'ZipArchive extension not available on server' ), 400 );
			}
			$built = Pi_Pix_Folder_Reader::read_zip( $target_file, $site_key );
			if ( is_wp_error( $built ) ) {
				@unlink( $target_file );
				return new \WP_REST_Response( array( 'error' => $built->get_error_message() ), 400 );
			}

			$validator = new Pi_Pix_Validator( $site_key );
			$report = $validator->validate( $built['envelope'] );

			$records = $built['envelope']['records'] ?? array();
			$media_count = count( $built['envelope']['assets'] ?? array() );
			$post_type = ! empty( $records[0]['entity'] ) ? $records[0]['entity'] : ( ! empty( $records[0]['identity']['post_type'] ) ? $records[0]['identity']['post_type'] : 'post' );

			$sample_items = array();
			foreach ( array_slice( $records, 0, 10 ) as $rec ) {
				$cat_name = 'Chưa phân loại';
				$categories = array();
				$other_terms = array();
				if ( ! empty( $rec['terms'] ) && is_array( $rec['terms'] ) ) {
					foreach ( $rec['terms'] as $t ) {
						if ( ! empty( $t['name'] ) ) {
							if ( ( $t['vocabulary'] ?? '' ) === 'category' ) {
								$categories[] = $t['name'];
							} else {
								$other_terms[] = $t['name'];
							}
						}
					}
				} elseif ( ! empty( $rec['taxonomies'] ) ) {
					foreach ( $rec['taxonomies'] as $tax_name => $tax_terms ) {
						foreach ( (array) $tax_terms as $t ) {
							if ( ! empty( $t['name'] ) ) {
								if ( strpos( $tax_name, 'cat' ) !== false ) {
									$categories[] = $t['name'];
								} else {
									$other_terms[] = $t['name'];
								}
							}
						}
					}
				}
				if ( ! empty( $categories ) ) {
					$cat_name = implode( ', ', array_slice( $categories, 0, 2 ) );
				} elseif ( ! empty( $other_terms ) ) {
					$cat_name = implode( ', ', array_slice( $other_terms, 0, 2 ) );
				}

				$sample_items[] = array(
					'id' => $rec['identity']['id'] ?? null,
					'uuid' => $rec['uid'] ?? ( $rec['identity']['uuid'] ?? ( 'pix-' . substr( md5( $rec['title'] ?? '' ), 0, 8 ) ) ),
					'title' => $rec['title'] ?? '(Không có tiêu đề)',
					'slug' => $rec['identity']['slug'] ?? '',
					'category' => $cat_name,
					'entity' => $rec['entity'] ?? 'post',
					'status' => 'ready',
				);
			}

			// Clean up the extraction directory from read_zip inspection
			if ( ! empty( $built['zip_extract_dir'] ) ) {
				self::rrmdir( $built['zip_extract_dir'] );
			}

			return new \WP_REST_Response( array(
				'valid' => $report['ok'] ?? true,
				'file_path' => $target_file,
				'file_name' => $file['name'],
				'file_size' => filesize( $target_file ),
				'sha256' => $sha256,
				'manifest' => array(
					'version' => Pi_Pix_Schema::VERSION,
					'post_type' => $post_type,
					'item_count' => count( $records ),
					'media_count' => $media_count,
					'exported_at' => $built['envelope']['generated_at'] ?? gmdate( 'Y-m-d' ),
					'source_site' => $built['envelope']['source']['site_url'] ?? 'External PIX-1 Site',
					'sha256' => $sha256,
				),
				'items' => $sample_items,
				'validation' => $report,
			), 200 );
		}

		return new \WP_REST_Response( array( 'error' => 'Unsupported format' ), 400 );
	}

	public static function import_posts(\WP_REST_Request $req) {
		$file_path = $req->get_param( 'file_path' );
		$site_key = $req->get_param( 'site_key' ) ?: self::site_key();
		$conflict_mode = $req->get_param( 'conflict_mode' ) ?: 'update';
		$dry_run = (bool) $req->get_param( 'dry_run' );
		$suspend_translation = $req->get_param( 'suspend_translation' ) !== false;

		// Path-traversal guard: only allow files physically inside the uploads
		// dir or the system temp dir. Without this an admin could point
		// file_path at any file on the host (wp-config.php, /etc/hosts, ...).
		$unsafe = self::assert_safe_path( $file_path );
		if ( null !== $unsafe ) {
			return $unsafe;
		}

		$ext = strtolower( pathinfo( $file_path, PATHINFO_EXTENSION ) );

		if ( 'zip' === $ext ) {
			return self::handle_zip_import( $file_path, $site_key, $conflict_mode, $dry_run, $suspend_translation );
		}

		if ( 'json' === $ext ) {
			return self::handle_json_import( $file_path, $site_key, $conflict_mode, $dry_run, $suspend_translation );
		}

		if ( 'csv' === $ext ) {
			return self::handle_csv_import( $file_path, $site_key, $conflict_mode, $dry_run, $suspend_translation );
		}

		return new \WP_REST_Response( array( 'error' => 'Unsupported file type: .' . $ext . '. Supported: .zip, .json, .csv' ), 400 );
	}

	public static function import_chunk(\WP_REST_Request $req) {
		$file_path           = $req->get_param( 'file_path' );
		$site_key            = $req->get_param( 'site_key' ) ?: self::site_key();
		$conflict_mode       = $req->get_param( 'conflict_mode' ) ?: 'update';
		$offset              = (int) ( $req->get_param( 'offset' ) ?: 0 );
		$limit               = (int) ( $req->get_param( 'limit' ) ?: 10 );
		$suspend_translation = $req->get_param( 'suspend_translation' ) !== false;

		$unsafe = self::assert_safe_path( $file_path );
		if ( null !== $unsafe ) {
			return $unsafe;
		}

		$ext = strtolower( pathinfo( $file_path, PATHINFO_EXTENSION ) );
		if ( 'zip' !== $ext ) {
			return new \WP_REST_Response( array( 'error' => 'Chunked import only supports .zip bundles' ), 400 );
		}

		if ( ! class_exists( 'ZipArchive' ) ) {
			return new \WP_REST_Response( array( 'error' => 'ZipArchive extension not available' ), 400 );
		}

		$cache_dir = sys_get_temp_dir() . '/pix-chunk-' . md5( $file_path );
		if ( ! is_dir( $cache_dir ) || $offset === 0 ) {
			if ( is_dir( $cache_dir ) ) {
				self::rrmdir( $cache_dir );
			}
			wp_mkdir_p( $cache_dir );
			$zip = new \ZipArchive();
			if ( true !== $zip->open( $file_path ) ) {
				return new \WP_REST_Response( array( 'error' => 'Cannot open ZIP archive' ), 400 );
			}
			$zip->extractTo( $cache_dir );
			$zip->close();
		}

		$result = Pi_Pix_Folder_Importer::import_chunk(
			$cache_dir,
			$site_key,
			$offset,
			$limit,
			array(
				'conflict_mode'       => in_array( $conflict_mode, array( 'skip', 'update', 'create' ), true ) ? $conflict_mode : 'update',
				'suspend_translation' => $suspend_translation,
			)
		);

		if ( is_wp_error( $result ) ) {
			return new \WP_REST_Response( array( 'error' => $result->get_error_message() ), 400 );
		}

		// If finished all records, clean up the chunk cache folder
		if ( ! empty( $result['done'] ) ) {
			self::rrmdir( $cache_dir );
		}

		return new \WP_REST_Response( $result, 200 );
	}

	/**
	 * Reject paths that escape the uploads dir or system temp dir. Returns null
	 * when the path is safe (caller proceeds), or a 400 WP_REST_Response to
	 * return directly when it is not. Resolves ../, symlinks, and mixed
	 * slashes via realpath() before comparing, so traversal tricks don't slip
	 * through a prefix check.
	 */
	private static function assert_safe_path( $file_path ) {
		if ( ! $file_path || '' === trim( $file_path ) ) {
			return new \WP_REST_Response( array( 'error' => 'file_path is required' ), 400 );
		}
		$real = realpath( $file_path );
		if ( false === $real || ! file_exists( $real ) ) {
			return new \WP_REST_Response( array( 'error' => 'File not found or not readable' ), 400 );
		}
		$uploads   = wp_upload_dir();
		$allowed   = array(
			rtrim( str_replace( '\\', '/', $uploads['basedir'] ), '/' ),
			rtrim( str_replace( '\\', '/', sys_get_temp_dir() ), '/' ),
		);
		$real_norm = rtrim( str_replace( '\\', '/', $real ), '/' );
		foreach ( $allowed as $root ) {
			if ( $real_norm === $root || 0 === strpos( $real_norm, $root . '/' ) ) {
				return null; // safe
			}
		}
		return new \WP_REST_Response( array(
			'error' => 'file_path must live inside wp-content/uploads or the system temp dir (path traversal blocked)',
		), 400 );
	}

	private static function handle_zip_import( $zip_path, $site_key, $conflict_mode, $dry_run, $suspend_translation ) {
		if ( ! class_exists( 'ZipArchive' ) ) {
			return new \WP_REST_Response( array( 'error' => 'ZipArchive extension not available' ), 400 );
		}

		if ( $dry_run ) {
			$built = Pi_Pix_Folder_Reader::read_zip( $zip_path, $site_key );
			if ( is_wp_error( $built ) ) {
				return new \WP_REST_Response( array( 'error' => $built->get_error_message() ), 400 );
			}
			$validator = new Pi_Pix_Validator( $site_key );
			$report = $validator->validate( $built['envelope'] );
			return new \WP_REST_Response( array(
				'dry_run' => true,
				'format' => 'zip',
				'valid' => $report['ok'],
				'record_count' => count( $built['envelope']['records'] ),
				'validation' => $report,
			), 200 );
		}

		$built = Pi_Pix_Folder_Reader::read_zip( $zip_path, $site_key );
		if ( is_wp_error( $built ) ) {
			return new \WP_REST_Response( array( 'error' => $built->get_error_message() ), 400 );
		}

		$validator = new Pi_Pix_Validator( $site_key );
		$report = $validator->validate( $built['envelope'] );
		if ( ! $report['ok'] ) {
			return new \WP_REST_Response( array(
				'error' => 'validation failed',
				'validation' => $report,
			), 422 );
		}

		$args = array(
			'conflict_mode' => in_array( $conflict_mode, array( 'skip', 'update', 'create' ), true ) ? $conflict_mode : 'update',
			'dry_run' => false,
			'suspend_translation' => $suspend_translation,
		);

		$result = Pi_Pix_Folder_Importer::import( $built['zip_extract_dir'], $site_key, $args );
		self::rrmdir( $built['zip_extract_dir'] );

		return new \WP_REST_Response( $result, 200 );
	}

	private static function handle_json_import( $json_path, $site_key, $conflict_mode, $dry_run, $suspend_translation ) {
		$built = Pi_Pix_Json_Reader::read( $json_path, $site_key );
		if ( is_wp_error( $built ) ) {
			return new \WP_REST_Response( array( 'error' => $built->get_error_message() ), 400 );
		}

		$validator = new Pi_Pix_Validator( $site_key );
		$report = $validator->validate( $built['envelope'] );
		if ( ! $report['ok'] ) {
			return new \WP_REST_Response( array(
				'error' => 'validation failed',
				'validation' => $report,
			), 422 );
		}

		if ( $dry_run ) {
			return new \WP_REST_Response( array(
				'dry_run' => true,
				'format' => 'json',
				'valid' => true,
				'record_count' => count( $built['envelope']['records'] ),
				'validation' => $report,
			), 200 );
		}

		$args = array(
			'conflict_mode' => in_array( $conflict_mode, array( 'skip', 'update', 'create' ), true ) ? $conflict_mode : 'update',
			'dry_run' => false,
			'suspend_translation' => $suspend_translation,
		);

		$result = self::run_pix_import_from_envelope( $built['envelope'], $site_key, $args, $built['file_map'] );
		return new \WP_REST_Response( $result, 200 );
	}

	private static function handle_csv_import( $csv_path, $site_key, $conflict_mode, $dry_run, $suspend_translation ) {
		$built = Pi_Pix_Csv_Reader::read( $csv_path, $site_key );
		if ( is_wp_error( $built ) ) {
			return new \WP_REST_Response( array( 'error' => $built->get_error_message() ), 400 );
		}

		$validator = new Pi_Pix_Validator( $site_key );
		$report = $validator->validate( $built['envelope'] );
		if ( ! $report['ok'] ) {
			return new \WP_REST_Response( array(
				'error' => 'validation failed',
				'validation' => $report,
			), 422 );
		}

		if ( $dry_run ) {
			return new \WP_REST_Response( array(
				'dry_run' => true,
				'format' => 'csv',
				'valid' => true,
				'record_count' => count( $built['envelope']['records'] ),
				'validation' => $report,
			), 200 );
		}

		$args = array(
			'conflict_mode' => in_array( $conflict_mode, array( 'skip', 'update', 'create' ), true ) ? $conflict_mode : 'update',
			'dry_run' => false,
			'suspend_translation' => $suspend_translation,
		);

		$result = self::run_pix_import_from_envelope( $built['envelope'], $site_key, $args, $built['file_map'] );
		return new \WP_REST_Response( $result, 200 );
	}

	private static function run_pix_import_from_envelope( array $envelope, $site_key, array $args, array $file_map ) {
		$validator = new Pi_Pix_Validator( $site_key );
		$report = $validator->validate( $envelope );
		if ( ! $report['ok'] ) {
			return array(
				'ok' => false,
				'errors' => array( array( 'error' => 'validation failed', 'validation' => $report ) ),
				'validation_warnings' => count( $report['warnings'] ),
			);
		}

		$tmp = trailingslashit( sys_get_temp_dir() ) . 'pix-import-' . gmdate( 'Ymd-His' ) . '-' . wp_rand( 1000, 9999 );
		wp_mkdir_p( $tmp );
		file_put_contents( $tmp . '/manifest.json', wp_json_encode( array(
			'pix' => Pi_Pix_Schema::VERSION,
			'kind' => 'folder-bundle',
			'folders' => array_map( function ( $r ) { return $r['identity']['slug']; }, $envelope['records'] ),
			'checksum' => array( 'records_digest' => $envelope['checksum']['records_digest'] ),
		) ) );
		foreach ( $envelope['records'] as $record ) {
			$slug = $record['identity']['slug'];
			$post_dir = $tmp . '/' . $slug;
			wp_mkdir_p( $post_dir . '/images' );
			$out = $record;
			if ( ! empty( $out['images']['featured'] ) && isset( $file_map[ $out['images']['featured'] ] ) ) {
				$id = $out['images']['featured'];
				$out['images']['featured'] = array(
					'file' => 'images/featured.' . pathinfo( $file_map[$id], PATHINFO_EXTENSION ),
					'alt' => $envelope['assets'][$id]['alt'] ?? '',
					'sha256' => $envelope['assets'][$id]['sha256'] ?? '',
				);
				copy( $file_map[$id], $post_dir . '/' . $out['images']['featured']['file'] );
			} else {
				$out['images']['featured'] = null;
			}
			$g = array();
			foreach ( (array) ( $out['images']['gallery'] ?? array() ) as $i => $ref ) {
				if ( ! isset( $file_map[$ref] ) ) {
					continue;
				}
				$file = 'images/gallery-' . ( $i + 1 ) . '.' . pathinfo( $file_map[$ref], PATHINFO_EXTENSION );
				copy( $file_map[$ref], $post_dir . '/' . $file );
				$g[] = array( 'file' => $file, 'alt' => $envelope['assets'][$ref]['alt'] ?? '', 'sha256' => $envelope['assets'][$ref]['sha256'] ?? '' );
			}
			$out['images']['gallery'] = $g;
			file_put_contents( $post_dir . '/post.json', Pi_Pix_Schema::canonical_json( $out ) . "\n" );
		}

		$result = Pi_Pix_Folder_Importer::import( $tmp, $site_key, $args );
		self::rrmdir( $tmp );
		return $result;
	}

    /* ══════════════════════════ INTERNALS ════════════════════════════════ */

    /**
     * Convert legacy item shape → PIX-1 envelope. Terms' flat
     * {taxonomy, slug, name} are reverse-mapped to the logical vocabulary
     * name the schema expects.
     */
    private static function build_envelope_from_items(array $items, $site_key) {
        $records = [];
        foreach ($items as $item) {
            $records[] = self::item_to_record($item, $site_key);
        }

        return [
            'pix'         => Pi_Pix_Schema::VERSION,
            'kind'        => 'bundle',
            'target_hint' => ['site_key' => $site_key],
            'counts'      => ['records' => count($records)],
            'checksum'    => [
                'algo'           => 'sha256',
                'records_digest' => Pi_Pix_Schema::records_digest($records),
            ],
            'assets'      => [],   // filled by materialize_remote_assets
            'records'     => $records,
        ];
    }

    /**
     * Map one legacy item to a PIX record. Reads both the catalog-importer's
     * shape (content, meta, terms[{taxonomy,slug,name}], product_images) and
     * the theme script's shape (content_html, featured_image_url).
     */
    private static function item_to_record(array $item, $site_key) {
        $title     = (string) ($item['title'] ?? '');
        $slug      = (string) ($item['slug'] ?? sanitize_title($title));
        $post_type = (string) ($item['type'] ?? $item['post_type'] ?? 'post');
        $entity    = self::entity_for_post_type($post_type, $site_key) ?? 'post';

        // Content: accept both `content` (catalog importer) and `content_html` (theme).
        $content = (string) ($item['content_html'] ?? $item['content'] ?? '');

        $status = (string) ($item['status'] ?? 'draft');
        if (!in_array($status, Pi_Pix_Schema::STATUSES, true)) {
            $status = 'draft';
        }

        // uid: deterministic from entity+slug so re-imports update in place.
        $uid = Pi_Pix_Schema::mint_uid($entity, 'slug', $slug);

        // terms → vocabulary.
        $terms = [];
        foreach ((array) ($item['terms'] ?? []) as $t) {
            if (!is_array($t) || empty($t['taxonomy'])) {
                continue;
            }
            $vocab = Pi_Pix_Schema::vocabulary_for_taxonomy($t['taxonomy'], $site_key, $entity);
            if (null === $vocab) {
                continue;
            }
            $slug_t = (string) ($t['slug'] ?? '');
            $terms[] = [
                'vocabulary' => $vocab,
                'slug'       => $slug_t,
                'name'       => (string) ($t['name'] ?? $slug_t),
                'path'       => [$slug_t],
            ];
        }

        // SEO + named fields pulled out of the meta bag so they land in the
        // right PIX slot instead of the generic meta escape-hatch.
        $seo    = [];
        $fields = [];
        $raw_meta = (array) ($item['meta'] ?? []);
        foreach (Pi_Pix_Schema::seo_map() as $field => $key) {
            if (array_key_exists($key, $raw_meta) && '' !== (string) $raw_meta[$key]) {
                $seo[$field] = $raw_meta[$key];
            }
        }
        foreach (Pi_Pix_Schema::field_map($entity) as $field => $key) {
            if (array_key_exists($key, $raw_meta) && '' !== (string) $raw_meta[$key]) {
                $fields[$field] = $raw_meta[$key];
            }
        }

        // Remaining meta → generic bag (skip keys PIX forbids/reserves).
        $meta_bag = [];
        foreach ($raw_meta as $key => $value) {
            if (null !== Pi_Pix_Schema::meta_key_rejection($key, $entity)) {
                continue;
            }
            $meta_bag[] = ['key' => $key, 'values' => [(string) $value], 'single' => true];
        }

        // Featured image: theme's featured_image_url, else catalog's product_images first ref.
        $featured_url = '';
        if (!empty($item['featured_image_url'])) {
            $featured_url = (string) $item['featured_image_url'];
        } elseif (!empty($item['product_images'])) {
            $refs = array_filter(array_map('trim', explode('|', (string) $item['product_images'])));
            if (!empty($refs)) {
                $featured_url = $refs[0];
            }
        }
        $gallery_urls = [];
        if (!empty($item['product_images'])) {
            $refs = array_filter(array_map('trim', explode('|', (string) $item['product_images'])));
            if (count($refs) > 1) {
                $gallery_urls = array_slice($refs, 1);
            }
        }

        // Images: remote URLs are recorded as id refs into the assets pool; the
        // materialize step downloads them and fills the file_map. Placeholder
        // ids use {slug}:featured / {slug}:gallery-N, matching Pi_Pix_Folder_Reader.
        $featured_ref = null;
        $gallery_refs = [];
        $assets_local = [];
        if ('' !== $featured_url) {
            $id            = "{$slug}:featured";
            $featured_ref  = $id;
            $assets_local[$id] = ['origin' => ['kind' => 'remote', 'ref' => $featured_url]];
        }
        foreach ($gallery_urls as $i => $url) {
            $id = "{$slug}:gallery-" . ($i + 1);
            $gallery_refs[] = $id;
            $assets_local[$id] = ['origin' => ['kind' => 'remote', 'ref' => $url]];
        }

        return [
            'pix'      => Pi_Pix_Schema::VERSION,
            'uid'      => $uid,
            'entity'   => $entity,
            'identity' => ['natural_key' => null, 'slug' => $slug],
            'title'    => $title,
            'content'  => ['format' => 'html', 'value' => $content],
            'excerpt'  => (string) ($item['excerpt'] ?? ''),
            'status'   => $status,
            'dates'    => ['published' => null, 'modified' => null],
            'author'   => ['login' => ''],
            'parent'   => ['uid' => null, 'slug' => null],
            'language' => [],
            'terms'    => $terms,
            'images'   => ['featured' => $featured_ref, 'gallery' => $gallery_refs, 'inline' => []],
            'seo'      => $seo,
            'fields'   => $fields,
            'meta'     => $meta_bag,
            // Stashed for materialize_remote_assets; stripped before validation.
            '_pending_assets' => $assets_local,
        ];
    }

    /**
     * Download each remote asset ref into a temp dir and wire up the
     * file_map + assets pool the PIX importer reads. Returns the cleaned
     * envelope (no _pending_assets) and the file_map.
     */
    private static function materialize_remote_assets(array $envelope) {
        if (!function_exists('download_url')) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/media.php';
            require_once ABSPATH . 'wp-admin/includes/image.php';
        }

        $assets   = [];
        $file_map = [];
        $tmp_root = trailingslashit(sys_get_temp_dir()) . 'pix-remote-' . gmdate('Ymd-His') . '-' . wp_rand(1000, 9999);
        wp_mkdir_p($tmp_root);

        foreach ($envelope['records'] as &$record) {
            $pending = $record['_pending_assets'] ?? [];
            unset($record['_pending_assets']);
            foreach ($pending as $id => $info) {
                $ref = $info['origin']['ref'] ?? '';
                if ('' === $ref) {
                    continue;
                }
                $tmp = download_url($ref);
                if (is_wp_error($tmp)) {
                    continue;
                }
                $name     = sanitize_file_name(basename(wp_parse_url($ref, PHP_URL_PATH)) ?: "asset-{$id}");
                $abs      = $tmp_root . '/' . wp_unique_filename($tmp_root, $name);
                rename($tmp, $abs);
                $hash     = hash_file('sha256', $abs);
                $file_map[$id] = $abs;
                $assets[$id] = [
                    'sha256' => $hash,
                    'path'   => 'assets/' . substr($hash, 0, 2) . '/' . basename($abs),
                    'bytes'  => filesize($abs),
                    'mime'   => wp_check_filetype($abs)['type'] ?: 'application/octet-stream',
                    'alt'    => '',
                    'origin' => ['kind' => 'local', 'ref' => $ref],
                ];
            }
        }
        unset($record);

        $envelope['assets'] = $assets;
        return [$envelope, $file_map];
    }

    /**
     * Thin wrapper around the PIX folder importer's import() that accepts a
     * ready envelope + file_map instead of a folder on disk. Mirrors the
     * private logic in Pi_Pix_Folder_Importer::import() without re-reading
     * the folder, so import_post_list can feed it an in-memory envelope.
     */
    private static function run_pix_import(array $envelope, $site_key, array $args, array $file_map) {
        // Reuse the validator the importer runs internally so behavior matches.
        $validator = new Pi_Pix_Validator($site_key);
        $report    = $validator->validate($envelope);
        if (!$report['ok']) {
            return [
                'ok'     => false,
                'errors' => [['error' => 'validation failed', 'validation' => $report]],
                'validation_warnings' => count($report['warnings']),
            ];
        }

        // Defer to the importer by materializing a temp folder it can read.
        // The folder importer already handles file_map-style asset resolution,
        // so we hand it the envelope directly via its public import() by
        // writing a minimal folder structure. Simpler & reuses all its
        // upsert/parent/image logic than re-implementing it here.
        $tmp = trailingslashit(sys_get_temp_dir()) . 'pix-import-' . gmdate('Ymd-His') . '-' . wp_rand(1000, 9999);
        wp_mkdir_p($tmp);
        file_put_contents($tmp . '/manifest.json', wp_json_encode([
            'pix'       => Pi_Pix_Schema::VERSION,
            'kind'      => 'folder-bundle',
            'folders'   => array_map(function ($r) { return $r['identity']['slug']; }, $envelope['records']),
            'checksum'  => ['records_digest' => $envelope['checksum']['records_digest']],
        ]));
        foreach ($envelope['records'] as $record) {
            $slug     = $record['identity']['slug'];
            $post_dir = $tmp . '/' . $slug;
            wp_mkdir_p($post_dir . '/images');
            // Strip the asset-id refs the importer expects to resolve against
            // file_map; the reader writes {file,alt,sha256} inline objects.
            $out = $record;
            if (!empty($out['images']['featured']) && isset($file_map[$out['images']['featured']])) {
                $id = $out['images']['featured'];
                $out['images']['featured'] = [
                    'file'   => 'images/featured.' . pathinfo($file_map[$id], PATHINFO_EXTENSION),
                    'alt'    => $envelope['assets'][$id]['alt'] ?? '',
                    'sha256' => $envelope['assets'][$id]['sha256'] ?? '',
                ];
                copy($file_map[$id], $post_dir . '/' . $out['images']['featured']['file']);
            } else {
                $out['images']['featured'] = null;
            }
            $g = [];
            foreach ((array) ($out['images']['gallery'] ?? []) as $i => $ref) {
                if (!isset($file_map[$ref])) {
                    continue;
                }
                $file = 'images/gallery-' . ($i + 1) . '.' . pathinfo($file_map[$ref], PATHINFO_EXTENSION);
                copy($file_map[$ref], $post_dir . '/' . $file);
                $g[] = ['file' => $file, 'alt' => $envelope['assets'][$ref]['alt'] ?? '', 'sha256' => $envelope['assets'][$ref]['sha256'] ?? ''];
            }
            $out['images']['gallery'] = $g;
            file_put_contents($post_dir . '/post.json', Pi_Pix_Schema::canonical_json($out) . "\n");
        }

        $result = Pi_Pix_Folder_Importer::import($tmp, $site_key, $args);
        // Best-effort cleanup; importer has finished all writes by now.
        self::rrmdir($tmp);
        return $result;
    }

    private static function rrmdir($dir) {
        if (!is_dir($dir)) {
            return;
        }
        foreach (scandir($dir) ?: [] as $f) {
            if ('.' === $f || '..' === $f) {
                continue;
            }
            $p = $dir . '/' . $f;
            is_dir($p) ? self::rrmdir($p) : @unlink($p);
        }
        @rmdir($dir);
    }

    private static function entity_for_post_type($post_type, $site_key) {
        foreach (Pi_Pix_Schema::entities() as $entity) {
            if (Pi_Pix_Schema::post_type_for($entity, $site_key) === $post_type) {
                return $entity;
            }
        }
        return null;
    }
}

// Backward-compat alias: the theme (docs/product-catalog/import_via_pi.php)
// and the legacy pi-migrate catalog importer both hardcode
// `Pi_Api_ImportExport::import_post_list()`. The class was renamed to
// Pi_Api_Migrate when the migrate endpoints were folded in, but the static
// contract name is still in the wild — alias it so those callers keep
// working without a code change on their side. Safe to remove once every
// caller is migrated to the new name.
if ( ! class_exists( 'Pi_Api_ImportExport' ) ) {
    class_alias( 'Pi_Api_Migrate', 'Pi_Api_ImportExport' );
}
