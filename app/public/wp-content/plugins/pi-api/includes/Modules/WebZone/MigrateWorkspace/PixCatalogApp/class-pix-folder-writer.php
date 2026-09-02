<?php
/**
 * PIX-1 folder export: one post = one folder, so a person can open a single
 * export and read `post.json` + `images/` directly, no zip tool required.
 *
 *   {slug}/
 *     post.json          — the full PIX-1 record; images referenced by relative filename
 *     images/
 *       featured.{ext}
 *       gallery-1.{ext}
 *       gallery-2.{ext}
 *
 * Exporting many posts wraps N of these same per-post folders inside one
 * parent folder plus a manifest.json — the per-post folder shape never
 * changes, so a single-post export and one item out of a batch are byte-for-
 * byte the same thing.
 *
 *   {export-name}/
 *     manifest.json      — pix version, site, generated_at, counts, records_digest
 *     {slug-1}/...
 *     {slug-2}/...
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Pi_Pix_Folder_Writer {

	/**
	 * @param int    $post_id
	 * @param string $dest_dir Parent directory the {slug}/ folder is created inside.
	 * @param string $site_key Which site's entity/vocabulary map to export against.
	 * @return array{dir:string,record:array}|WP_Error
	 */
	public static function export_post( $post_id, $dest_dir, $site_key = 'saigonhoreca' ) {
		$record = self::build_record( $post_id, $site_key );
		if ( is_wp_error( $record ) ) {
			return $record;
		}

		$slug     = $record['identity']['slug'] ?: (string) $post_id;
		$post_dir = rtrim( $dest_dir, '/\\' ) . '/' . $slug;
		wp_mkdir_p( $post_dir );
		wp_mkdir_p( $post_dir . '/images' );

		$record = self::materialize_images( $post_id, $record, $post_dir );

		file_put_contents( $post_dir . '/post.json', Pi_Pix_Schema::canonical_json( $record ) . "\n" );

		return array( 'dir' => $post_dir, 'slug' => $slug, 'record' => $record );
	}

	/**
	 * @param int[]  $post_ids
	 * @param string $dest_dir Where the {export-name} parent folder is created.
	 * @param string $export_name
	 * @return array|WP_Error
	 */
	public static function export_many( array $post_ids, $dest_dir, $export_name, $site_key = 'saigonhoreca' ) {
		$root = rtrim( $dest_dir, '/\\' ) . '/' . $export_name;
		wp_mkdir_p( $root );

		$records = array();
		$slugs   = array();
		$errors  = array();

		foreach ( $post_ids as $post_id ) {
			$result = self::export_post( $post_id, $root, $site_key );
			if ( is_wp_error( $result ) ) {
				$errors[] = array( 'post_id' => $post_id, 'error' => $result->get_error_message() );
				continue;
			}
			$records[] = $result['record'];
			$slugs[]   = $result['slug'];
		}

		if ( empty( $records ) ) {
			return new WP_Error( 'pi_pix_export_empty', 'No posts were exported', array( 'errors' => $errors ) );
		}

		$manifest = array(
			'pix'          => Pi_Pix_Schema::VERSION,
			'kind'         => 'folder-bundle',
			'generated_at' => gmdate( 'c' ),
			'source'       => array( 'site_url' => site_url() ),
			'counts'       => array( 'records' => count( $records ), 'errors' => count( $errors ) ),
			'folders'      => $slugs,
			'checksum'     => array( 'algo' => 'sha256', 'records_digest' => Pi_Pix_Schema::records_digest( $records ) ),
		);
		file_put_contents( $root . '/manifest.json', Pi_Pix_Schema::canonical_json( $manifest ) . "\n" );

		return array( 'dir' => $root, 'post_count' => count( $records ), 'errors' => $errors );
	}

	/**
	 * Same as export_many() but writes a single .zip instead of leaving a
	 * folder on disk. The ZIP is the transport; the per-post folder shape
	 * inside it is byte-for-byte identical to a folder-bundle export, so
	 * read_zip() can hand the extracted tree straight to the existing
	 * Pi_Pix_Folder_Reader::build_envelope().
	 *
	 * The manifest's checksum gains a `zip_sha256` (hash of the final .zip
	 * bytes) on top of records_digest, so a tampered bundle is detectable
	 * before any image byte is read.
	 *
	 * @return array{zip:string,bytes:int,post_count:int,zip_sha256:string,errors:array}|WP_Error
	 */
	public static function export_zip( array $post_ids, $dest_dir, $export_name, $site_key = 'saigonhoreca' ) {
		if ( ! class_exists( 'ZipArchive' ) ) {
			return new WP_Error( 'pi_pix_no_zip', 'ZipArchive extension not available' );
		}
		$dest_dir = rtrim( $dest_dir, '/\\' );
		wp_mkdir_p( $dest_dir );

		// Step 1: build the folder-bundle (reuses export_many verbatim).
		$bundle = self::export_many( $post_ids, $dest_dir, $export_name, $site_key );
		if ( is_wp_error( $bundle ) ) {
			return $bundle;
		}
		$root = $bundle['dir'];

		// Step 2: zip the bundle tree into dest_dir/{export_name}.zip. The
		// per-post folder + manifest.json become top-level entries inside the
		// archive (we add the CONTENTS of $root, not $root itself).
		$zip_path = $dest_dir . '/' . $export_name . '.zip';
		$zip      = new ZipArchive();
		if ( true !== $zip->open( $zip_path, ZipArchive::CREATE | ZipArchive::OVERWRITE ) ) {
			return new WP_Error( 'pi_pix_zip_open', "cannot create zip: {$zip_path}" );
		}

		$iterator = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator( $root, RecursiveDirectoryIterator::SKIP_DOTS ),
			RecursiveIteratorIterator::LEAVES_ONLY
		);
		$file_count = 0;
		foreach ( $iterator as $file ) {
			if ( $file->isDir() ) {
				continue;
			}
			$local = substr( $file->getPathname(), strlen( $root ) + 1 );
			$zip->addFile( $file->getPathname(), str_replace( '\\', '/', $local ) );
			$file_count++;
		}
		$zip->close();

		// Step 3: patch manifest.json inside the zip to add zip_sha256. The
		// manifest was written by export_many() with records_digest only; we
		// reopen the closed zip, rewrite that one entry, close again.
		$zip_sha = hash_file( 'sha256', $zip_path );
		$manifest_json = file_get_contents( $root . '/manifest.json' );
		$manifest      = json_decode( $manifest_json, true );
		$manifest['kind'] = 'zip-bundle';
		$manifest['checksum']['zip_sha256'] = $zip_sha;
		$reopened = new ZipArchive();
		if ( true === $reopened->open( $zip_path ) ) {
			$reopened->addFromString( 'manifest.json', Pi_Pix_Schema::canonical_json( $manifest ) . "\n" );
			$reopened->close();
			// zip_sha256 changes once we rewrite the entry — recompute so the
			// returned value matches the bytes actually on disk.
			$zip_sha = hash_file( 'sha256', $zip_path );
		}

		// Step 4: best-effort cleanup of the intermediate folder-bundle; the
		// ZIP is the deliverable now.
		self::rrmdir( $root );

		return array(
			'zip'        => $zip_path,
			'bytes'      => filesize( $zip_path ) ?: 0,
			'file_count' => $file_count,
			'post_count' => $bundle['post_count'],
			'zip_sha256' => $zip_sha,
			'errors'     => $bundle['errors'],
		);
	}

	private static function rrmdir( $dir ) {
		if ( ! is_dir( $dir ) ) {
			return;
		}
		foreach ( scandir( $dir ) ?: array() as $f ) {
			if ( '.' === $f || '..' === $f ) {
				continue;
			}
			$p = $dir . '/' . $f;
			is_dir( $p ) ? self::rrmdir( $p ) : @unlink( $p );
		}
		@rmdir( $dir );
	}

	// ---------------------------------------------------------------

	private static function build_record( $post_id, $site_key ) {
		$post = get_post( $post_id );
		if ( ! $post ) {
			return new WP_Error( 'pi_pix_no_post', "post {$post_id} not found" );
		}

		$entity = self::entity_for_post_type( $post->post_type, $site_key );
		if ( null === $entity ) {
			return new WP_Error( 'pi_pix_unmapped_type', "post_type '{$post->post_type}' has no PIX entity mapping on site '{$site_key}'" );
		}

		$uid = get_post_meta( $post_id, Pi_Pix_Schema::UID_META_KEY, true );
		if ( '' === $uid ) {
			$uid = Pi_Pix_Schema::mint_uid( $entity, 'post-id', (string) $post_id );
		}

		$terms = array();
		foreach ( get_object_taxonomies( $post->post_type, 'names' ) as $tax ) {
			$vocab = Pi_Pix_Schema::vocabulary_for_taxonomy( $tax, $site_key, $entity );
			if ( null === $vocab ) {
				continue; // taxonomy exists on the site but isn't part of the PIX-1 vocabulary map
			}
			foreach ( wp_get_post_terms( $post_id, $tax ) as $term ) {
				if ( is_wp_error( $term ) ) {
					continue;
				}
				$ancestors = array_reverse( get_ancestors( $term->term_id, $tax ) );
				$path      = array_map( function ( $tid ) use ( $tax ) {
					$t = get_term( $tid, $tax );
					return $t && ! is_wp_error( $t ) ? $t->slug : null;
				}, $ancestors );
				$path   = array_values( array_filter( $path ) );
				$path[] = $term->slug;

				$terms[] = array(
					'vocabulary' => $vocab,
					'slug'       => $term->slug,
					'name'       => $term->name,
					'path'       => $path,
				);
			}
		}

		$meta_raw = get_post_meta( $post_id );
		$reserved = Pi_Pix_Schema::reserved_meta_keys( $entity );
		$seo      = array();
		foreach ( Pi_Pix_Schema::seo_map() as $field => $key ) {
			$val = $meta_raw[ $key ][0] ?? '';
			if ( '' !== $val ) {
				$seo[ $field ] = maybe_unserialize( $val );
			}
		}
		$fields = array();
		foreach ( Pi_Pix_Schema::field_map( $entity ) as $field => $key ) {
			$val = $meta_raw[ $key ][0] ?? '';
			if ( '' !== $val ) {
				$fields[ $field ] = maybe_unserialize( $val );
			}
		}
		if ( 'product' === $entity && empty( $fields['sku'] ) ) {
			$fields['sku'] = get_post_meta( $post_id, '_sku', true ) ?: ( $post->post_name ?: 'SKU-' . $post_id );
		}
		$meta = array();
		foreach ( $meta_raw as $key => $values ) {
			// Keys owned by a named field/SEO/language slot, or forbidden/internal
			// keys, never round-trip into the generic escape-hatch meta bag.
			if ( in_array( $key, $reserved, true ) || null !== Pi_Pix_Schema::meta_key_rejection( $key, $entity ) ) {
				continue;
			}
			$vals   = array_map( 'maybe_unserialize', $values );
			$meta[] = array( 'key' => $key, 'values' => array_values( $vals ), 'single' => 1 === count( $vals ) );
		}

		$lang_map = Pi_Pix_Schema::language_map();
		$language = array();
		$group    = get_post_meta( $post_id, $lang_map['translation_group'], true );
		$code     = get_post_meta( $post_id, $lang_map['code'], true );
		if ( $group || $code ) {
			$language = array(
				'code'              => $code ?: 'vi',
				'translation_group' => $group ?: null,
				'is_source'         => ( $code ?: 'vi' ) === 'vi',
			);
		}

		$parent = array( 'uid' => null, 'slug' => null );
		if ( $post->post_parent ) {
			$parent_uid = get_post_meta( $post->post_parent, Pi_Pix_Schema::UID_META_KEY, true );
			$parent_post = get_post( $post->post_parent );
			$parent = array(
				'uid'  => $parent_uid ?: null,
				'slug' => $parent_post ? $parent_post->post_name : null,
			);
		}

		return array(
			'pix'      => Pi_Pix_Schema::VERSION,
			'uid'      => $uid,
			'entity'   => $entity,
			'identity' => array(
				'natural_key' => self::natural_key( $entity, $post_id, $fields ),
				'slug'        => $post->post_name,
			),
			'title'    => $post->post_title,
			'content'  => array( 'format' => 'html', 'value' => $post->post_content ),
			'excerpt'  => $post->post_excerpt,
			'status'   => $post->post_status,
			'dates'    => array(
				'published' => mysql2date( 'c', $post->post_date, false ),
				'modified'  => mysql2date( 'c', $post->post_modified, false ),
			),
			'author'   => array( 'login' => get_userdata( $post->post_author )->user_login ?? '' ),
			'parent'   => $parent,
			'language' => $language,
			'terms'    => $terms,
			// images{} is filled in by materialize_images() once files are on disk.
			'images'   => array( 'featured' => null, 'gallery' => array(), 'inline' => array() ),
			'seo'      => $seo,
			'fields'   => $fields,
			'meta'     => $meta,
		);
	}

	private static function natural_key( $entity, $post_id, array $fields ) {
		if ( 'product' === $entity && ! empty( $fields['sku'] ) ) {
			$brand_terms = wp_get_post_terms( $post_id, 'product_brand' );
			$brand       = ! empty( $brand_terms ) && ! is_wp_error( $brand_terms ) ? $brand_terms[0]->slug : 'unknown';
			return array( 'namespace' => 'saigonhoreca.catalog', 'key' => $brand . '|' . $fields['sku'] );
		}
		return null;
	}

	/**
	 * Copies the featured/gallery/attached/inline attachments into {post_dir}/images/ under
	 * role-based names and fills in record.images with those relative paths
	 * (relative to the post folder, so the folder is portable on its own).
	 */
	private static function materialize_images( $post_id, array $record, $post_dir ) {
		$featured_id = get_post_thumbnail_id( $post_id );
		$copied_paths = array();
		$copied_ids   = array();

		// 1. Featured image
		$featured_ref = null;
		if ( $featured_id ) {
			$featured_ref = self::copy_attachment( $featured_id, $post_dir, 'featured' );
			if ( $featured_ref ) {
				$copied_ids[] = (int) $featured_id;
				$path = get_attached_file( $featured_id );
				if ( $path ) {
					$copied_paths[ realpath( $path ) ?: $path ] = $featured_ref;
				}
			}
		}

		// 2. Gallery & custom field images
		$gallery_id_candidates = array();
		
		// 2a. Standard WooCommerce / PIX gallery
		$woo_gallery = get_post_meta( $post_id, Pi_Pix_Schema::GALLERY_META, true );
		if ( is_string( $woo_gallery ) && '' !== trim( $woo_gallery ) ) {
			$gallery_id_candidates = array_merge( $gallery_id_candidates, array_filter( array_map( 'intval', explode( ',', $woo_gallery ) ) ) );
		} elseif ( is_array( $woo_gallery ) ) {
			$gallery_id_candidates = array_merge( $gallery_id_candidates, array_filter( array_map( 'intval', $woo_gallery ) ) );
		}

		// 2b. Other custom fields (gallery, du_an_gallery, images, hinh_anh, photos)
		foreach ( array( 'gallery', 'du_an_gallery', 'images', 'hinh_anh', 'photos', 'project_gallery', 'slide_images' ) as $g_key ) {
			$g_val = get_post_meta( $post_id, $g_key, true );
			if ( is_string( $g_val ) && '' !== trim( $g_val ) ) {
				$gallery_id_candidates = array_merge( $gallery_id_candidates, array_filter( array_map( 'intval', explode( ',', $g_val ) ) ) );
			} elseif ( is_array( $g_val ) ) {
				foreach ( $g_val as $item ) {
					if ( is_numeric( $item ) ) {
						$gallery_id_candidates[] = (int) $item;
					} elseif ( is_array( $item ) && ! empty( $item['ID'] ) ) {
						$gallery_id_candidates[] = (int) $item['ID'];
					} elseif ( is_array( $item ) && ! empty( $item['id'] ) ) {
						$gallery_id_candidates[] = (int) $item['id'];
					}
				}
			}
		}

		// 2c. Attached media where post_parent == $post_id
		$attachments = get_posts( array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'post_parent'    => $post_id,
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'post_mime_type' => 'image',
		) );
		if ( ! empty( $attachments ) ) {
			$gallery_id_candidates = array_merge( $gallery_id_candidates, array_map( 'intval', $attachments ) );
		}

		$gallery_id_candidates = array_values( array_unique( $gallery_id_candidates ) );
		$gallery_refs = array();
		$g_idx = 1;
		foreach ( $gallery_id_candidates as $attach_id ) {
			if ( in_array( $attach_id, $copied_ids, true ) ) {
				continue;
			}
			$ref = self::copy_attachment( (int) $attach_id, $post_dir, 'gallery-' . $g_idx );
			if ( $ref ) {
				$gallery_refs[] = $ref;
				$copied_ids[] = (int) $attach_id;
				$path = get_attached_file( $attach_id );
				if ( $path ) {
					$copied_paths[ realpath( $path ) ?: $path ] = $ref;
				}
				$g_idx++;
			}
		}

		// 3. Inline images in HTML content
		$inline_refs = array();
		$post_content = $record['content']['value'] ?? '';
		if ( ! empty( $post_content ) ) {
			preg_match_all( '/<img[^>]+src=[\'"]([^\'"]+)[\'"][^>]*>/i', $post_content, $img_matches, PREG_SET_ORDER );
			$uploads = wp_upload_dir();
			$upload_base_url = untrailingslashit( $uploads['baseurl'] );
			$upload_base_dir = untrailingslashit( $uploads['basedir'] );

			$inline_idx = 1;
			foreach ( $img_matches as $m ) {
				$src = $m[1];
				if ( empty( $src ) || strpos( $src, 'data:' ) === 0 ) {
					continue;
				}

				$alt = '';
				if ( preg_match( '/alt=[\'"]([^\'"]*)[\'"]/i', $m[0], $alt_match ) ) {
					$alt = $alt_match[1];
				}

				$file_on_disk = null;
				if ( 0 === strpos( $src, $upload_base_url ) ) {
					$rel_url = substr( $src, strlen( $upload_base_url ) );
					$candidate_path = $upload_base_dir . $rel_url;
					if ( file_exists( $candidate_path ) ) {
						$file_on_disk = $candidate_path;
					}
				}

				if ( ! $file_on_disk ) {
					$att_id = attachment_url_to_postid( $src );
					if ( $att_id ) {
						$p = get_attached_file( $att_id );
						if ( $p && file_exists( $p ) ) {
							$file_on_disk = $p;
							if ( '' === $alt ) {
								$alt = get_post_meta( $att_id, '_wp_attachment_image_alt', true );
							}
						}
					}
				}

				if ( $file_on_disk && file_exists( $file_on_disk ) ) {
					$real_p = realpath( $file_on_disk ) ?: $file_on_disk;
					if ( isset( $copied_paths[ $real_p ] ) ) {
						$existing_ref = $copied_paths[ $real_p ];
						$inline_refs[] = array(
							'file'   => $existing_ref['file'],
							'alt'    => $alt ?: ( $existing_ref['alt'] ?? '' ),
							'sha256' => $existing_ref['sha256'] ?? '',
							'src'    => $src,
						);
					} else {
						$ext = pathinfo( $file_on_disk, PATHINFO_EXTENSION ) ?: 'jpg';
						$filename = 'inline-' . $inline_idx . '.' . $ext;
						wp_mkdir_p( $post_dir . '/images' );
						copy( $file_on_disk, $post_dir . '/images/' . $filename );
						$sha = hash_file( 'sha256', $file_on_disk );
						$ref = array(
							'file'   => 'images/' . $filename,
							'alt'    => $alt,
							'sha256' => $sha,
							'src'    => $src,
						);
						$inline_refs[] = $ref;
						$copied_paths[ $real_p ] = $ref;
						$inline_idx++;
					}
				}
			}
		}

		$record['images'] = array(
			'featured' => $featured_ref,
			'gallery'  => $gallery_refs,
			'inline'   => $inline_refs,
		);
		return $record;
	}

	/**
	 * @return array{file:string,alt:string,sha256:string}|null
	 */
	private static function copy_attachment( $attach_id, $post_dir, $role ) {
		$path = get_attached_file( $attach_id );
		if ( ! $path || ! file_exists( $path ) ) {
			return null;
		}
		$ext      = pathinfo( $path, PATHINFO_EXTENSION ) ?: 'jpg';
		$filename = $role . '.' . $ext;
		wp_mkdir_p( $post_dir . '/images' );
		copy( $path, $post_dir . '/images/' . $filename );

		return array(
			'file'   => 'images/' . $filename,
			'alt'    => get_post_meta( $attach_id, '_wp_attachment_image_alt', true ),
			'sha256' => hash_file( 'sha256', $path ),
		);
	}

	private static function entity_for_post_type( $post_type, $site_key ) {
		foreach ( Pi_Pix_Schema::entities() as $entity ) {
			if ( Pi_Pix_Schema::post_type_for( $entity, $site_key ) === $post_type ) {
				return $entity;
			}
		}
		return null;
	}
}
