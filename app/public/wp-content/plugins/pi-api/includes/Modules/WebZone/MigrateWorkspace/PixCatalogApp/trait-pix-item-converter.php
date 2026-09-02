<?php
/**
 * Shared item → PIX record + envelope builder for the in-memory readers
 * (JSON, CSV). Folder-based imports go through Pi_Pix_Folder_Reader which
 * resolves images from disk; these readers only ever see remote image URLs,
 * so the asset pool + file_map are synthesized here.
 *
 * Centralizing this fixes two defects that the original copy-pasted
 * json/csv readers shared:
 *   1. The record was pushed into $records BEFORE its image refs were
 *      rewritten to asset ids, so records_digest was computed over a
 *      different shape than the validator/importer received → E002 mismatch.
 *   2. The two readers were 90% identical code.
 *
 * A reader using this trait implements parse_file() to return a flat list
 * of source items; everything else (record mapping, asset synthesis,
 * envelope assembly, digest) happens once, here, correctly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

trait Pi_Pix_Item_Converter {

	/**
	 * Subclass hook: parse a source file into a list of raw item arrays.
	 * Each item may use either the generic shape (title, content, meta, ...)
	 * or the catalog CSV shape (post_title, post_content, meta:*, ...).
	 *
	 * @return array[]|WP_Error
	 */
	abstract protected static function parse_file( $file_path );

	/**
	 * Build a PIX envelope (validator/importer shape) from a list of raw
	 * items. Image URLs (featured_image_url / product_images) become entries
	 * in the assets pool + file_map; the record's images.* slots hold the
	 * synthesized asset ids so the digest matches what the importer reads.
	 *
	 * @return array{envelope:array,file_map:array,raw_records:array}|WP_Error
	 */
	protected static function build_envelope( $file_path, $site_key ) {
		$items = static::parse_file( $file_path );
		if ( is_wp_error( $items ) ) {
			return $items;
		}

		$records  = array();
		$assets   = array();
		$file_map = array();

		foreach ( $items as $item ) {
			$record = self::item_to_record( $item, $site_key );
			if ( is_wp_error( $record ) ) {
				continue;
			}
			$slug = $record['identity']['slug'];

			// Rewrite image slots from raw URLs → synthesized asset ids,
			// and register the asset. Done on the SAME record we push, so
			// the digest reflects the id-ref shape the importer expects.
			$featured_url = $record['images']['featured'];
			$gallery_urls = $record['images']['gallery'];

			$featured_ref = null;
			if ( $featured_url ) {
				$featured_ref = "{$slug}:featured";
				self::register_remote_asset( $assets, $file_map, $featured_ref, $featured_url );
			}

			$gallery_refs = array();
			foreach ( $gallery_urls as $i => $url ) {
				$id = "{$slug}:gallery-" . ( $i + 1 );
				self::register_remote_asset( $assets, $file_map, $id, $url );
				$gallery_refs[] = $id;
			}

			$record['images'] = array(
				'featured' => $featured_ref,
				'gallery'  => $gallery_refs,
				'inline'   => array(),
			);

			$records[] = $record;
		}

		$envelope = array(
			'pix'         => Pi_Pix_Schema::VERSION,
			'kind'        => 'bundle',
			'target_hint' => array( 'site_key' => $site_key ),
			'counts'      => array( 'records' => count( $records ) ),
			'checksum'    => array(
				'algo'           => 'sha256',
				'records_digest' => Pi_Pix_Schema::records_digest( $records ),
			),
			'assets'      => $assets,
			'records'     => $records,
		);

		return array(
			'envelope'    => $envelope,
			'file_map'    => $file_map,
			'raw_records' => $records,
		);
	}

	private static function register_remote_asset( array &$assets, array &$file_map, $id, $url ) {
		$assets[ $id ] = array(
			'sha256' => '',
			'path'   => '',
			'bytes'  => 0,
			'mime'   => 'image/jpeg',
			'alt'    => '',
			'origin' => array( 'kind' => 'remote', 'ref' => $url ),
		);
		$file_map[ $id ] = $url;
	}

	/**
	 * Map one raw item → PIX record. Accepts both the generic shape and the
	 * catalog CSV shape. Images stay as raw URLs here; build_envelope()
	 * converts them to asset ids so the same digest/shape flows everywhere.
	 */
	private static function item_to_record( array $item, $site_key ) {
		$title     = (string) ( $item['title'] ?? $item['post_title'] ?? '' );
		$slug      = (string) ( $item['slug'] ?? sanitize_title( $title ) );
		$post_type = (string) ( $item['type'] ?? $item['post_type'] ?? 'post' );
		$entity    = self::entity_for_post_type( $post_type, $site_key );

		if ( null === $entity ) {
			return new WP_Error( 'pi_pix_unmapped_type', "post_type '{$post_type}' has no PIX entity mapping on site '{$site_key}'" );
		}

		$uid    = Pi_Pix_Schema::mint_uid( $entity, 'slug', $slug );
		$content = (string) ( $item['content_html'] ?? $item['content'] ?? $item['post_content'] ?? '' );
		$status = (string) ( $item['status'] ?? $item['post_status'] ?? 'draft' );
		if ( ! in_array( $status, Pi_Pix_Schema::STATUSES, true ) ) {
			$status = 'draft';
		}

		// terms → vocabulary.
		$terms = array();
		foreach ( (array) ( $item['terms'] ?? array() ) as $t ) {
			if ( ! is_array( $t ) || empty( $t['taxonomy'] ) ) {
				continue;
			}
			$vocab = Pi_Pix_Schema::vocabulary_for_taxonomy( $t['taxonomy'], $site_key, $entity );
			if ( null === $vocab ) {
				continue;
			}
			$slug_t = (string) ( $t['slug'] ?? '' );
			$terms[] = array(
				'vocabulary' => $vocab,
				'slug'       => $slug_t,
				'name'       => (string) ( $t['name'] ?? $slug_t ),
				'path'       => array( $slug_t ),
			);
		}

		// SEO + named fields pulled out of the meta bag.
		$raw_meta = (array) ( $item['meta'] ?? array() );
		$seo      = array();
		$fields   = array();
		foreach ( Pi_Pix_Schema::seo_map() as $field => $key ) {
			if ( array_key_exists( $key, $raw_meta ) && '' !== (string) $raw_meta[ $key ] ) {
				$seo[ $field ] = $raw_meta[ $key ];
			}
		}
		foreach ( Pi_Pix_Schema::field_map( $entity ) as $field => $key ) {
			if ( array_key_exists( $key, $raw_meta ) && '' !== (string) $raw_meta[ $key ] ) {
				$fields[ $field ] = $raw_meta[ $key ];
			}
		}

		// Remaining meta → generic bag (skip keys PIX forbids/reserves).
		$meta_bag = array();
		foreach ( $raw_meta as $key => $value ) {
			if ( null !== Pi_Pix_Schema::meta_key_rejection( $key, $entity ) ) {
				continue;
			}
			$meta_bag[] = array( 'key' => $key, 'values' => array( (string) $value ), 'single' => true );
		}

		// Images: prefer explicit featured_image_url, else first of product_images.
		$featured_url = '';
		if ( ! empty( $item['featured_image_url'] ) ) {
			$featured_url = (string) $item['featured_image_url'];
		} elseif ( ! empty( $item['product_images'] ) ) {
			$refs = array_filter( array_map( 'trim', explode( '|', (string) $item['product_images'] ) ) );
			if ( ! empty( $refs ) ) {
				$featured_url = $refs[0];
			}
		}
		$gallery_urls = array();
		if ( ! empty( $item['product_images'] ) ) {
			$refs = array_filter( array_map( 'trim', explode( '|', (string) $item['product_images'] ) ) );
			if ( count( $refs ) > 1 ) {
				$gallery_urls = array_slice( $refs, 1 );
			}
		}

		// Language.
		$language = array();
		$group    = $item['translation_group'] ?? $item['pi_translation_group'] ?? null;
		$code     = $item['pi_language'] ?? null;
		if ( $group || $code ) {
			$language = array(
				'code'              => $code ?: 'vi',
				'translation_group' => $group ?: null,
				'is_source'         => ( $code ?: 'vi' ) === 'vi',
			);
		}

		$parent = array( 'uid' => null, 'slug' => null );
		if ( ! empty( $item['parent'] ) && is_array( $item['parent'] ) ) {
			$parent = $item['parent'];
		}

		return array(
			'pix'      => Pi_Pix_Schema::VERSION,
			'uid'      => $uid,
			'entity'   => $entity,
			'identity' => array( 'natural_key' => null, 'slug' => $slug ),
			'title'    => $title,
			'content'  => array( 'format' => 'html', 'value' => $content ),
			'excerpt'  => (string) ( $item['excerpt'] ?? '' ),
			'status'   => $status,
			'dates'    => array( 'published' => null, 'modified' => null ),
			'author'   => array( 'login' => '' ),
			'parent'   => $parent,
			'language' => $language,
			'terms'    => $terms,
			'images'   => array(
				'featured' => $featured_url ?: null,
				'gallery'  => $gallery_urls,
				'inline'   => array(),
			),
			'seo'      => $seo,
			'fields'   => $fields,
			'meta'     => $meta_bag,
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
