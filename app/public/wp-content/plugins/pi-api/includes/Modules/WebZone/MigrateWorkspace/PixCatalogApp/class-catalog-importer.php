<?php
/**
 * Generalized catalog importer — same "idempotent by slug" pattern as
 * saigonhoreca-theme/docs/product-catalog/import_via_pi.php, but not tied
 * to one theme: works from a single JSON/CSV file, or a directory of
 * brand subfolders each containing a data.json (the saigonhoreca layout).
 *
 * Reuses Pi_Api_ImportExport::import_post_list() when the pi-api plugin is
 * active (both saigonhoreca and saigonhouse have it) for posts/meta/taxonomy.
 * Featured-image handling is done by pi-migrate itself (not delegated), since
 * `product_images` in saigonhoreca's import.csv is inconsistent per brand:
 * some brands hold a remote URL, others a relative filesystem path that
 * already points into wp-content/uploads/ (pre-downloaded, no network needed).
 * Falls back to a minimal built-in importer for posts when pi-api is not active.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Pi_Api_Catalog_Importer {

	/**
	 * @param string $path        File (.json/.csv) or directory of brand folders.
	 * @param array  $args {
	 *     @type string $post_type    default 'product'
	 *     @type array  $brands       limit to these brand-folder slugs (directory mode only)
	 *     @type bool   $publish      publish instead of draft
	 *     @type bool   $dry_run      parse + report only, no writes
	 *     @type string $conflict_mode 'update'|'skip' — passed through to pi-api when available
	 * }
	 */
	public static function run( $path, $args = array() ) {
		$args = wp_parse_args( $args, array(
			'post_type'     => 'product',
			'brands'        => array(),
			'publish'       => false,
			'dry_run'       => false,
			'conflict_mode' => 'update',
			'skip_images'   => false,
		) );

		if ( ! file_exists( $path ) ) {
			return new WP_Error( 'pi_api_missing_path', "Path not found: {$path}" );
		}

		$posts   = array();
		$sources = array();

		if ( is_dir( $path ) ) {
			foreach ( glob( rtrim( $path, '/\\' ) . '/*', GLOB_ONLYDIR ) as $brand_dir ) {
				$brand_slug = basename( $brand_dir );
				if ( ! empty( $args['brands'] ) && ! in_array( $brand_slug, $args['brands'], true ) ) {
					continue;
				}
				// Prefer import.csv (already normalized to WP import shape by
				// generate_import_csvs.py) over data.json/data-full.json, which
				// are raw research-crawl artifacts with a free-form per-brand schema.
				$csv_file = $brand_dir . '/import.csv';
				if ( file_exists( $csv_file ) ) {
					$items = self::read_csv( $csv_file );
				} else {
					$data_file = $brand_dir . '/data.json';
					if ( ! file_exists( $data_file ) ) {
						$data_file = $brand_dir . '/data-full.json';
					}
					if ( ! file_exists( $data_file ) ) {
						continue;
					}
					$items = self::read_json( $data_file );
				}
				foreach ( $items as $item ) {
					$posts[] = self::normalize_item( $item, $brand_slug, $args );
				}
				$sources[] = $brand_slug;
			}
		} else {
			$ext   = strtolower( pathinfo( $path, PATHINFO_EXTENSION ) );
			$items = 'csv' === $ext ? self::read_csv( $path ) : self::read_json( $path );
			foreach ( $items as $item ) {
				$posts[] = self::normalize_item( $item, null, $args );
			}
			$sources[] = basename( $path );
		}

		if ( empty( $posts ) ) {
			return new WP_Error( 'pi_api_no_items', 'No importable items found at ' . $path );
		}

		if ( $args['dry_run'] ) {
			return array(
				'dry_run'    => true,
				'sources'    => $sources,
				'item_count' => count( $posts ),
				'sample'     => array_slice( $posts, 0, 3 ),
			);
		}

		$result = self::import_posts( $posts, $args['conflict_mode'] );
		if ( ! $args['skip_images'] ) {
			$result['images'] = self::attach_images( $posts );
		}
		return $result;
	}

	private static function import_posts( array $posts, $conflict_mode ) {
		if ( class_exists( 'Pi_Api_ImportExport' ) && method_exists( 'Pi_Api_ImportExport', 'import_post_list' ) ) {
			return array(
				'engine' => 'pi-api',
				'result' => Pi_Api_ImportExport::import_post_list( $posts, $conflict_mode ),
			);
		}
		return array(
			'engine' => 'pi-migrate-fallback',
			'result' => self::fallback_import( $posts, $conflict_mode ),
		);
	}

	/**
	 * Minimal fallback when pi-api isn't active: match by slug, insert or update.
	 * Does not attempt image sideload or complex taxonomy shapes — pi-api is the
	 * preferred engine; this exists so pi-migrate is usable standalone.
	 */
	private static function fallback_import( array $posts, $conflict_mode ) {
		$created = 0;
		$updated = 0;
		$skipped = 0;
		$errors  = array();

		foreach ( $posts as $item ) {
			$slug     = sanitize_title( $item['slug'] ?? $item['title'] ?? '' );
			$existing = get_page_by_path( $slug, OBJECT, $item['type'] );

			if ( $existing && 'skip' === $conflict_mode ) {
				$skipped++;
				continue;
			}

			$postarr = array(
				'post_title'   => $item['title'] ?? $slug,
				'post_name'    => $slug,
				'post_type'    => $item['type'],
				'post_status'  => $item['status'] ?? 'draft',
				'post_content' => $item['content'] ?? '',
				'post_excerpt' => $item['excerpt'] ?? '',
			);

			if ( $existing ) {
				$postarr['ID'] = $existing->ID;
				$post_id       = wp_update_post( $postarr, true );
				$updated++;
			} else {
				$post_id = wp_insert_post( $postarr, true );
				$created++;
			}

			if ( is_wp_error( $post_id ) ) {
				$errors[] = array( 'slug' => $slug, 'error' => $post_id->get_error_message() );
				continue;
			}

			if ( ! empty( $item['meta'] ) && is_array( $item['meta'] ) ) {
				foreach ( $item['meta'] as $key => $value ) {
					update_post_meta( $post_id, $key, $value );
				}
			}
			// Terms (category + brand). Brand is a product_brand term, never meta —
			// no template reads a brand meta key. Only taxonomies present in the
			// record are written, so a record omitting one cannot clear it.
			if ( ! empty( $item['terms'] ) && is_array( $item['terms'] ) ) {
				$by_tax = array();
				foreach ( $item['terms'] as $t ) {
					if ( ! is_array( $t ) || empty( $t['taxonomy'] ) || ! taxonomy_exists( $t['taxonomy'] ) ) {
						continue;
					}
					$name = (string) ( $t['name'] ?? $t['slug'] ?? '' );
					$slug_t = sanitize_title( (string) ( $t['slug'] ?? $name ) );
					if ( '' === $name && '' === $slug_t ) {
						continue;
					}
					$term = get_term_by( 'slug', $slug_t, $t['taxonomy'] );
					if ( ! $term && '' !== $name ) {
						$term = get_term_by( 'name', $name, $t['taxonomy'] );
					}
					if ( ! $term ) {
						$new = wp_insert_term( $name ?: $slug_t, $t['taxonomy'], array( 'slug' => $slug_t ?: null ) );
						if ( is_wp_error( $new ) ) {
							continue;
						}
						$by_tax[ $t['taxonomy'] ][] = (int) $new['term_id'];
					} else {
						$by_tax[ $t['taxonomy'] ][] = (int) $term->term_id;
					}
				}
				foreach ( $by_tax as $tax => $ids ) {
					wp_set_object_terms( $post_id, $ids, $tax );
				}
			}
		}

		return compact( 'created', 'updated', 'skipped', 'errors' );
	}

	/**
	 * Sets the featured image (and any extra images as a gallery meta) for every
	 * imported post that has a `product_images` value. Handles both shapes found
	 * in saigonhoreca's import.csv: a remote URL (downloaded + sideloaded) and a
	 * relative filesystem path already under wp-content/uploads/ (copied into the
	 * media library directly, no network round-trip). Multiple images can be
	 * pipe-separated ("img1.webp|img2.webp"); the first becomes the featured image.
	 * Safe to re-run: matches existing attachments by a `_pi_migrate_source_path`/
	 * `_pi_migrate_source_url` meta lookup before downloading/copying again.
	 */
	private static function attach_images( array $posts ) {
		$attached = 0;
		$reused   = 0;
		$skipped  = 0;
		$errors   = array();

		foreach ( $posts as $item ) {
			$raw = $item['product_images'] ?? '';
			if ( '' === $raw ) {
				$skipped++;
				continue;
			}
			$slug = $item['slug'] ?? sanitize_title( $item['title'] ?? '' );
			$post = get_page_by_path( $slug, OBJECT, $item['type'] ?? 'product' );
			if ( ! $post ) {
				$errors[] = array( 'slug' => $slug, 'error' => 'post not found after import' );
				continue;
			}

			$refs         = array_filter( array_map( 'trim', explode( '|', $raw ) ) );
			$gallery_ids  = array();
			$had_error    = false;

			foreach ( $refs as $i => $ref ) {
				$attach_id = self::resolve_attachment( $ref, $post->ID, $item['title'] ?? $slug );
				if ( is_wp_error( $attach_id ) ) {
					$errors[] = array( 'slug' => $slug, 'ref' => $ref, 'error' => $attach_id->get_error_message() );
					$had_error = true;
					continue;
				}
				$gallery_ids[] = $attach_id['id'];
				$attach_id['reused'] ? $reused++ : $attached++;
			}

			if ( empty( $gallery_ids ) ) {
				continue;
			}
			set_post_thumbnail( $post->ID, $gallery_ids[0] );
			if ( count( $gallery_ids ) > 1 ) {
				update_post_meta( $post->ID, '_pi_migrate_gallery_ids', array_slice( $gallery_ids, 1 ) );
			}
		}

		return compact( 'attached', 'reused', 'skipped', 'errors' );
	}

	/**
	 * @return array{id:int,reused:bool}|WP_Error
	 */
	private static function resolve_attachment( $ref, $post_id, $title ) {
		$is_remote  = (bool) preg_match( '#^https?://#i', $ref );
		$meta_key   = $is_remote ? '_pi_migrate_source_url' : '_pi_migrate_source_path';
		$normalized = $is_remote ? $ref : ltrim( str_replace( '\\', '/', $ref ), '/' );

		$existing = get_posts( array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			'meta_key'       => $meta_key,
			'meta_value'     => $normalized,
			'fields'         => 'ids',
		) );
		if ( ! empty( $existing ) ) {
			return array( 'id' => $existing[0], 'reused' => true );
		}

		if ( ! function_exists( 'media_handle_sideload' ) ) {
			require_once ABSPATH . 'wp-admin/includes/media.php';
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/image.php';
		}

		if ( $is_remote ) {
			$tmp_file = download_url( $ref );
			if ( is_wp_error( $tmp_file ) ) {
				return $tmp_file;
			}
			$file_array = array(
				'name'     => sanitize_file_name( basename( wp_parse_url( $ref, PHP_URL_PATH ) ) ),
				'tmp_name' => $tmp_file,
			);
			$attach_id = media_handle_sideload( $file_array, $post_id, $title );
			if ( is_wp_error( $attach_id ) ) {
				@unlink( $tmp_file );
				return $attach_id;
			}
		} else {
			$local_path = rtrim( ABSPATH, '/\\' ) . '/' . $normalized;
			if ( ! file_exists( $local_path ) ) {
				return new WP_Error( 'pi_api_image_missing', "Local image not found: {$local_path}" );
			}
			$filetype = wp_check_filetype( basename( $local_path ) );
			if ( empty( $filetype['type'] ) ) {
				return new WP_Error( 'pi_api_bad_filetype', "Unrecognized file type: {$local_path}" );
			}
			$upload_dir = wp_upload_dir();
			$filename   = wp_unique_filename( $upload_dir['path'], basename( $local_path ) );
			$dest_path  = $upload_dir['path'] . '/' . $filename;
			if ( ! copy( $local_path, $dest_path ) ) {
				return new WP_Error( 'pi_api_copy_failed', "Could not copy {$local_path}" );
			}
			$attach_id = wp_insert_attachment( array(
				'post_mime_type' => $filetype['type'],
				'post_title'     => sanitize_text_field( $title ),
				'post_status'    => 'inherit',
			), $dest_path, $post_id );
			if ( is_wp_error( $attach_id ) || ! $attach_id ) {
				return is_wp_error( $attach_id ) ? $attach_id : new WP_Error( 'pi_api_attach_failed', "wp_insert_attachment failed for {$local_path}" );
			}
			$attach_data = wp_generate_attachment_metadata( $attach_id, $dest_path );
			wp_update_attachment_metadata( $attach_id, $attach_data );
		}

		update_post_meta( $attach_id, $meta_key, $normalized );
		return array( 'id' => $attach_id, 'reused' => false );
	}

	/**
	 * CSV `meta:` column name => the meta key the theme actually READS.
	 *
	 * The catalog CSVs (and the theme's own docs/product-catalog/import_via_pi.php)
	 * write `_seo_title` / `_seo_description` / `_og_image`, but the only SEO
	 * resolver on either site is themes/saigonhoreca-theme/inc/site/pi-seo-resolver.php,
	 * which reads the `_pi_seo_*` / `_pi_og_*` prefix exclusively. Passing the CSV
	 * keys through verbatim therefore stored SEO data no template ever renders.
	 * `_pi_seo_desc` is a READ-alias only — never write to it.
	 */
	private static $seo_key_map = array(
		'_seo_title'       => '_pi_seo_title',
		'_seo_description' => '_pi_seo_description',
		'_seo_desc'        => '_pi_seo_description',
		'_og_image'        => '_pi_og_image',
		'_og_title'        => '_pi_og_title',
		'_og_description'  => '_pi_og_description',
	);

	/**
	 * Accepts either the generic {title,content,status,slug,meta} shape, or a
	 * generate_import_csv.py-style flat row (post_title, post_content,
	 * post_status, sku, product_images, meta:*, attribute:* columns) and maps
	 * it into the generic shape pi-api / the fallback importer expect.
	 */
	private static function normalize_item( array $item, $brand_slug, array $args ) {
		if ( isset( $item['post_title'] ) && ! isset( $item['title'] ) ) {
			$item['title']   = $item['post_title'];
			$item['content'] = $item['post_content'] ?? '';
			$meta            = array();
			$terms           = array();

			$product_images = (string) ( $item['product_images'] ?? '' );

			foreach ( $item as $key => $value ) {
				if ( 0 !== strpos( $key, 'meta:' ) ) {
					// `attribute:*` columns are deliberately DROPPED. No template on
					// either site reads _attr_*/_attribute_* meta; brand is rendered
					// from the product_brand taxonomy instead (see below).
					continue;
				}
				$raw_key = substr( $key, 5 );

				// The catalog CSV duplicates product_images verbatim into
				// meta:_og_image on every row. Storing it would (a) be redundant
				// and (b) store a *relative path* where the og:image tag needs an
				// absolute URL. Skip it and let the renderer fall back to the
				// featured image; only keep an og_image that genuinely differs.
				if ( in_array( $raw_key, array( '_og_image', '_og_image_url' ), true )
					&& (string) $value === $product_images ) {
					continue;
				}

				$meta[ self::$seo_key_map[ $raw_key ] ?? $raw_key ] = $value;
			}

			if ( isset( $item['sku'] ) && '' !== $item['sku'] ) {
				$meta['_sku'] = $item['sku'];
			}
			if ( isset( $item['product_images'] ) && '' !== $item['product_images'] ) {
				// Consumed by attach_images(); not a rendered key.
				$meta['_product_images'] = $item['product_images'];
			}

			// pi-api's apply_terms() expects a FLAT array of {taxonomy, slug, name}
			// objects. The previous `terms[taxonomy] = [names]` shape was silently
			// skipped by its `empty($t['taxonomy'])` guard, so no term was ever set.
			// Canonical category taxonomy is product_category; product_cat is
			// legacy WooCommerce data kept read-only and never written here.
			if ( ! empty( $item['product_cat'] ) ) {
				foreach ( array_filter( array_map( 'trim', explode( '|', $item['product_cat'] ) ) ) as $name ) {
					$terms[] = array(
						'taxonomy' => 'product_category',
						'slug'     => sanitize_title( $name ),
						'name'     => $name,
					);
				}
			}
			// Brand belongs in the flat product_brand taxonomy, not in meta.
			$brand_name = $item['product_brand'] ?? ( $item['attribute:brand'] ?? '' );
			if ( '' !== trim( (string) $brand_name ) ) {
				$terms[] = array(
					'taxonomy' => 'product_brand',
					'slug'     => sanitize_title( $brand_name ),
					'name'     => trim( (string) $brand_name ),
				);
			}
			if ( $terms ) {
				$item['terms'] = $terms;
			}

			$item['meta']   = $meta;
			$item['status'] = $item['post_status'] ?? 'draft';
		}

		// Pi_Api_ImportExport::import_post_list() reads the post type from
		// $raw['type']; `post_type` is not consulted. Set only what is read.
		$item['type']   = $item['type'] ?? $item['post_type'] ?? $args['post_type'];
		$item['status'] = $args['publish'] ? 'publish' : ( $item['status'] ?? 'draft' );
		unset( $item['post_type'] );

		if ( $brand_slug ) {
			$item['brand'] = $brand_slug;
		}
		if ( empty( $item['slug'] ) && ! empty( $item['title'] ) ) {
			$item['slug'] = sanitize_title( $item['title'] );
		}
		return $item;
	}

	private static function read_json( $file ) {
		$data = json_decode( file_get_contents( $file ), true );
		if ( ! is_array( $data ) ) {
			return array();
		}
		// support both a bare list and { "items": [...] } / { "products": [...] } wrappers
		foreach ( array( 'items', 'products', 'data' ) as $key ) {
			if ( isset( $data[ $key ] ) && is_array( $data[ $key ] ) ) {
				return $data[ $key ];
			}
		}
		$is_list = array_keys( $data ) === range( 0, count( $data ) - 1 );
		return $is_list ? $data : array( $data );
	}

	private static function read_csv( $file ) {
		$rows   = array();
		$handle = fopen( $file, 'r' );
		if ( ! $handle ) {
			return $rows;
		}
		$header = fgetcsv( $handle );
		if ( $header ) {
			$header[0] = preg_replace( '/^\xEF\xBB\xBF/', '', $header[0] ); // strip UTF-8 BOM
		}
		while ( ( $row = fgetcsv( $handle ) ) !== false ) {
			if ( count( $row ) !== count( $header ) ) {
				continue;
			}
			$rows[] = array_combine( $header, $row );
		}
		fclose( $handle );
		return $rows;
	}

	/**
	 * Export the given post type back out to JSON, in the same shape this
	 * importer reads — for round-tripping / backups.
	 */
	public static function export( $post_type, $file_path ) {
		$posts = get_posts( array(
			'post_type'      => $post_type,
			'post_status'    => 'any',
			'numberposts'    => -1,
		) );

		$items = array();
		foreach ( $posts as $post ) {
			$thumbnail_id = get_post_thumbnail_id( $post->ID );
			$gallery_ids  = get_post_meta( $post->ID, '_pi_migrate_gallery_ids', true ) ?: array();
			$image_urls   = array_filter( array_map( 'wp_get_attachment_url', array_merge(
				$thumbnail_id ? array( $thumbnail_id ) : array(),
				$gallery_ids
			) ) );

			// Terms come from the taxonomies, in the flat {taxonomy,slug,name}
			// shape the importer and pi-api both read.
			$terms = array();
			foreach ( get_object_taxonomies( $post->post_type, 'names' ) as $tax ) {
				foreach ( wp_get_post_terms( $post->ID, $tax ) as $term ) {
					if ( is_wp_error( $term ) ) {
						continue;
					}
					$terms[] = array( 'taxonomy' => $tax, 'slug' => $term->slug, 'name' => $term->name );
				}
			}

			$items[] = array(
				'title'          => $post->post_title,
				'slug'           => $post->post_name,
				'type'           => $post->post_type,
				'status'         => $post->post_status,
				'content'        => $post->post_content,
				'excerpt'        => $post->post_excerpt,
				'meta'           => get_post_meta( $post->ID ),
				'terms'          => $terms,
				'featured_image' => $thumbnail_id ? wp_get_attachment_url( $thumbnail_id ) : null,
				'gallery_images' => $image_urls,
			);
		}

		file_put_contents( $file_path, wp_json_encode( array( 'items' => $items ), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ) );
		return array( 'file' => $file_path, 'item_count' => count( $items ) );
	}
}
