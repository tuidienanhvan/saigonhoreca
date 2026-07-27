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
	 * Copies the featured/gallery attachments into {post_dir}/images/ under
	 * role-based names and fills in record.images with those relative paths
	 * (relative to the post folder, so the folder is portable on its own).
	 */
	private static function materialize_images( $post_id, array $record, $post_dir ) {
		$featured_id = get_post_thumbnail_id( $post_id );
		$gallery_ids = get_post_meta( $post_id, Pi_Pix_Schema::GALLERY_META, true ) ?: array();

		$featured_ref = null;
		if ( $featured_id ) {
			$featured_ref = self::copy_attachment( $featured_id, $post_dir, 'featured' );
		}
		$gallery_refs = array();
		foreach ( (array) $gallery_ids as $i => $attach_id ) {
			$ref = self::copy_attachment( (int) $attach_id, $post_dir, 'gallery-' . ( $i + 1 ) );
			if ( $ref ) {
				$gallery_refs[] = $ref;
			}
		}

		$record['images'] = array(
			'featured' => $featured_ref,
			'gallery'  => $gallery_refs,
			'inline'   => array(),
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
