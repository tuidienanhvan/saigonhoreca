<?php
/**
 * Writes a validated PIX-1 envelope (built by Pi_Pix_Folder_Reader) into WP.
 *
 * Two passes, which is the fix for defect #6 in pi-api's importer (parent
 * resolution keyed on a slug that may have just been deduped): pass 1 upserts
 * every record and records uid => post_id; pass 2 resolves post_parent once
 * every post in the batch is guaranteed to exist.
 *
 * Identity resolution order (the fix for defect #1 — slug-only identity):
 *   1. _pix_uid meta lookup
 *   2. _pix_natural_key meta lookup (JSON-encoded natural_key, when present)
 *   3. slug + post_type (get_page_by_path) — legacy fallback, back-writes
 *      _pix_uid so this path is never needed again for the same content.
 *
 * Always validates first and refuses to write anything if the envelope has
 * ERROR-level findings — a bad bundle never half-imports.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Pi_Pix_Folder_Importer {

	const NATURAL_KEY_META = '_pix_natural_key';

	/**
	 * @param string $dir
	 * @param string $site_key
	 * @param array  $args { conflict_mode: skip|update|create, dry_run: bool, suspend_translation: bool }
	 * @return array|WP_Error
	 */
	public static function import( $dir, $site_key, array $args = array() ) {
		$args = wp_parse_args( $args, array(
			'conflict_mode'       => 'update',
			'dry_run'             => false,
			'suspend_translation' => true,
		) );

		$built = Pi_Pix_Folder_Reader::build_envelope( $dir, $site_key );
		if ( is_wp_error( $built ) ) {
			return $built;
		}

		$validator = new Pi_Pix_Validator( $site_key );
		$report    = $validator->validate( $built['envelope'] );
		if ( ! $report['ok'] ) {
			return array(
				'ok'          => false,
				'reason'      => 'validation failed — nothing was written',
				'validation'  => $report,
			);
		}

		if ( $args['dry_run'] ) {
			return array( 'ok' => true, 'dry_run' => true, 'validation' => $report, 'records' => count( $built['envelope']['records'] ) );
		}

		$suspend_flag_set = false;
		if ( $args['suspend_translation'] && ! defined( 'PI_MULTILANG_SUSPEND' ) ) {
			define( 'PI_MULTILANG_SUSPEND', true );
			$suspend_flag_set = true;
		}
		// Belt-and-braces even without a cooperating pi-multilang change: drop its
		// save_post hook for the duration of the batch if the class is loaded.
		$removed_hook = false;
		if ( class_exists( 'Pi_Multilang\\Translation' ) ) {
			$removed_hook = remove_action( 'save_post', array( 'Pi_Multilang\\Translation', 'on_save_post' ), 10 );
		}

		$created  = 0;
		$updated  = 0;
		$skipped  = 0;
		$errors   = array();
		$uid_to_id = array();
		$pending_parents = array();
		$assets_result = array( 'created' => 0, 'reused' => 0, 'failed' => array() );

		foreach ( $built['envelope']['records'] as $record ) {
			$outcome = self::upsert_record( $record, $site_key, $args['conflict_mode'], $built['file_map'], $built['envelope']['assets'] );
			if ( is_wp_error( $outcome ) ) {
				$errors[] = array( 'uid' => $record['uid'], 'title' => $record['title'], 'error' => $outcome->get_error_message() );
				continue;
			}
			switch ( $outcome['action'] ) {
				case 'created': $created++; break;
				case 'updated': $updated++; break;
				case 'skipped': $skipped++; break;
			}
			if ( $outcome['post_id'] ) {
				$uid_to_id[ $record['uid'] ] = $outcome['post_id'];
				if ( ! empty( $record['parent']['uid'] ) || ! empty( $record['parent']['slug'] ) ) {
					$pending_parents[ $outcome['post_id'] ] = $record['parent'];
				}
			}
			foreach ( $outcome['assets'] as $a ) {
				if ( $a['ok'] ) {
					$a['reused'] ? $assets_result['reused']++ : $assets_result['created']++;
				} else {
					$assets_result['failed'][] = $a;
				}
			}
		}

		foreach ( $pending_parents as $child_id => $parent_ref ) {
			$parent_id = null;
			if ( ! empty( $parent_ref['uid'] ) && isset( $uid_to_id[ $parent_ref['uid'] ] ) ) {
				$parent_id = $uid_to_id[ $parent_ref['uid'] ];
			} elseif ( ! empty( $parent_ref['uid'] ) ) {
				$parent_id = self::find_by_uid( $parent_ref['uid'] );
			}
			if ( ! $parent_id && ! empty( $parent_ref['slug'] ) ) {
				$p = get_page_by_path( $parent_ref['slug'], OBJECT, get_post_type( $child_id ) );
				$parent_id = $p ? $p->ID : null;
			}
			if ( $parent_id && (int) $parent_id !== (int) $child_id ) {
				wp_update_post( array( 'ID' => $child_id, 'post_parent' => (int) $parent_id ) );
			} else {
				$errors[] = array( 'post_id' => $child_id, 'error' => 'could not resolve parent ' . wp_json_encode( $parent_ref ) );
			}
		}

		if ( $removed_hook ) {
			add_action( 'save_post', array( 'Pi_Multilang\\Translation', 'on_save_post' ), 10, 3 );
		}

		$ok = empty( $assets_result['failed'] ) && empty( $errors );

		return array(
			'ok'       => $ok,
			'created'  => $created,
			'updated'  => $updated,
			'skipped'  => $skipped,
			'errors'   => $errors,
			'assets'   => $assets_result,
			'validation_warnings' => count( $report['warnings'] ),
		);
	}

	/**
	 * Import a chunk/slice of records from a pre-extracted PIX bundle.
	 *
	 * @param string $dir
	 * @param string $site_key
	 * @param int    $offset
	 * @param int    $limit
	 * @param array  $args
	 * @return array|WP_Error
	 */
	public static function import_chunk( $dir, $site_key, $offset = 0, $limit = 10, array $args = array() ) {
		$args = wp_parse_args( $args, array(
			'conflict_mode'       => 'update',
			'suspend_translation' => true,
		) );

		$built = Pi_Pix_Folder_Reader::build_envelope( $dir, $site_key );
		if ( is_wp_error( $built ) ) {
			return $built;
		}

		$all_records   = $built['envelope']['records'] ?? array();
		$total_records = count( $all_records );
		$offset        = max( 0, (int) $offset );
		$limit         = max( 1, (int) $limit );

		$slice = array_slice( $all_records, $offset, $limit );

		if ( $args['suspend_translation'] && ! defined( 'PI_MULTILANG_SUSPEND' ) ) {
			define( 'PI_MULTILANG_SUSPEND', true );
		}
		$removed_hook = false;
		if ( class_exists( 'Pi_Multilang\\Translation' ) ) {
			$removed_hook = remove_action( 'save_post', array( 'Pi_Multilang\\Translation', 'on_save_post' ), 10 );
		}

		$chunk_created = 0;
		$chunk_updated = 0;
		$chunk_skipped = 0;
		$chunk_errors  = array();
		$chunk_items   = array();
		$assets_result = array( 'created' => 0, 'reused' => 0, 'failed' => array() );

		foreach ( $slice as $record ) {
			$item_title = $record['title'] ?? ( $record['identity']['slug'] ?? 'Item' );
			$outcome = self::upsert_record( $record, $site_key, $args['conflict_mode'], $built['file_map'], $built['envelope']['assets'] );
			if ( is_wp_error( $outcome ) ) {
				$chunk_errors[] = array( 'uid' => $record['uid'], 'title' => $item_title, 'error' => $outcome->get_error_message() );
				$chunk_items[] = array( 'title' => $item_title, 'action' => 'error', 'error' => $outcome->get_error_message() );
				continue;
			}
			switch ( $outcome['action'] ) {
				case 'created': $chunk_created++; break;
				case 'updated': $chunk_updated++; break;
				case 'skipped': $chunk_skipped++; break;
			}
			$chunk_items[] = array(
				'title'   => $item_title,
				'post_id' => $outcome['post_id'],
				'action'  => $outcome['action'],
			);
			foreach ( $outcome['assets'] as $a ) {
				if ( $a['ok'] ) {
					$a['reused'] ? $assets_result['reused']++ : $assets_result['created']++;
				} else {
					$assets_result['failed'][] = $a;
				}
			}
		}

		if ( $removed_hook ) {
			add_action( 'save_post', array( 'Pi_Multilang\\Translation', 'on_save_post' ), 10, 3 );
		}

		$next_offset = $offset + count( $slice );
		$is_done     = $next_offset >= $total_records;

		// On final chunk, resolve parents across the batch
		if ( $is_done ) {
			self::resolve_all_parents( $all_records );
		}

		return array(
			'ok'            => empty( $chunk_errors ),
			'done'          => $is_done,
			'offset'        => $next_offset,
			'total_records' => $total_records,
			'current_items' => $chunk_items,
			'chunk'         => array(
				'processed' => count( $slice ),
				'created'   => $chunk_created,
				'updated'   => $chunk_updated,
				'skipped'   => $chunk_skipped,
				'assets'    => $assets_result,
				'errors'    => $chunk_errors,
			),
		);
	}

	private static function resolve_all_parents( array $all_records ) {
		foreach ( $all_records as $record ) {
			if ( empty( $record['parent']['uid'] ) && empty( $record['parent']['slug'] ) ) {
				continue;
			}
			$child_id = self::find_by_uid( $record['uid'] );
			if ( ! $child_id ) {
				continue;
			}
			$parent_id = null;
			if ( ! empty( $record['parent']['uid'] ) ) {
				$parent_id = self::find_by_uid( $record['parent']['uid'] );
			}
			if ( ! $parent_id && ! empty( $record['parent']['slug'] ) ) {
				$p = get_page_by_path( $record['parent']['slug'], OBJECT, get_post_type( $child_id ) );
				$parent_id = $p ? $p->ID : null;
			}
			if ( $parent_id && (int) $parent_id !== (int) $child_id ) {
				wp_update_post( array( 'ID' => $child_id, 'post_parent' => (int) $parent_id ) );
			}
		}
	}

	// ---------------------------------------------------------------

	private static function upsert_record( array $record, $site_key, $conflict_mode, array $file_map, array $assets ) {
		$entity    = $record['entity'];
		$post_type = Pi_Pix_Schema::post_type_for( $entity, $site_key );
		if ( null === $post_type ) {
			return new WP_Error( 'pi_pix_unmapped_entity', "entity '{$entity}' has no post_type on '{$site_key}'" );
		}

		$natural_key_json = ! empty( $record['identity']['natural_key'] )
			? wp_json_encode( $record['identity']['natural_key'] )
			: null;

		$post_id = self::find_by_uid( $record['uid'] );
		$matched_by = $post_id ? 'uid' : null;

		if ( ! $post_id && $natural_key_json ) {
			$post_id = self::find_by_natural_key( $natural_key_json );
			$matched_by = $post_id ? 'natural_key' : null;
		}

		if ( ! $post_id ) {
			$slug = $record['identity']['slug'] ?? sanitize_title( $record['title'] );
			$existing = $slug ? get_page_by_path( $slug, OBJECT, $post_type ) : null;
			if ( $existing ) {
				$post_id = $existing->ID;
				$matched_by = 'slug';
			}
		}

		if ( $post_id && 'skip' === $conflict_mode ) {
			return array( 'action' => 'skipped', 'post_id' => $post_id, 'assets' => array() );
		}

		$postarr = array(
			'post_title'   => wp_kses_post( $record['title'] ),
			'post_content' => (string) ( $record['content']['value'] ?? '' ),
			'post_excerpt' => wp_kses_post( (string) ( $record['excerpt'] ?? '' ) ),
			'post_status'  => $record['status'],
			'post_type'    => $post_type,
		);
		if ( ! empty( $record['identity']['slug'] ) ) {
			$postarr['post_name'] = $record['identity']['slug'];
		}
		if ( ! empty( $record['author']['login'] ) ) {
			$user = get_user_by( 'login', $record['author']['login'] );
			if ( $user ) {
				$postarr['post_author'] = $user->ID;
			}
		}

		$action = $post_id ? 'updated' : 'created';
		if ( $post_id ) {
			$postarr['ID'] = $post_id;
			$result = wp_update_post( $postarr, true );
		} else {
			$result = wp_insert_post( $postarr, true );
		}
		if ( is_wp_error( $result ) ) {
			return $result;
		}
		$post_id = (int) $result;

		// Back-write identity so a slug-matched or natural-key-matched post never
		// needs that fallback path again.
		update_post_meta( $post_id, Pi_Pix_Schema::UID_META_KEY, $record['uid'] );
		if ( $natural_key_json ) {
			update_post_meta( $post_id, self::NATURAL_KEY_META, $natural_key_json );
		}

		self::apply_terms( $post_id, $record, $site_key, $entity );
		self::apply_named_fields( $post_id, $record, $entity );
		self::apply_language( $post_id, $record );
		self::apply_meta_bag( $post_id, $record );
		$asset_results = self::apply_images( $post_id, $record, $file_map, $assets );

		return array( 'action' => $action, 'post_id' => $post_id, 'matched_by' => $matched_by, 'assets' => $asset_results );
	}

	private static function find_by_uid( $uid ) {
		$rows = get_posts( array(
			'post_type'      => 'any',
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'meta_key'       => Pi_Pix_Schema::UID_META_KEY,
			'meta_value'     => $uid,
			'fields'         => 'ids',
		) );
		return $rows ? (int) $rows[0] : null;
	}

	private static function find_by_natural_key( $natural_key_json ) {
		$rows = get_posts( array(
			'post_type'      => 'any',
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'meta_key'       => self::NATURAL_KEY_META,
			'meta_value'     => $natural_key_json,
			'fields'         => 'ids',
		) );
		return $rows ? (int) $rows[0] : null;
	}

	/**
	 * Only taxonomies PRESENT in the record are touched — a record that omits
	 * `brand` must not clear an existing brand assignment. `path` is walked
	 * top-down, creating ancestor terms as needed, so term order in the record
	 * never matters (fixes defect #5: pi-api's order-dependent parent=0 bug).
	 */
	private static function apply_terms( $post_id, array $record, $site_key, $entity ) {
		$by_tax = array();
		foreach ( (array) ( $record['terms'] ?? array() ) as $t ) {
			$tax = Pi_Pix_Schema::taxonomy_for( $t['vocabulary'], $site_key, $entity );
			if ( ! $tax || ! taxonomy_exists( $tax ) ) {
				continue;
			}
			$path = ! empty( $t['path'] ) && is_array( $t['path'] ) ? $t['path'] : array( $t['slug'] ?? $t['name'] ?? '' );
			$parent_id = 0;
			$leaf_id   = 0;
			foreach ( $path as $slug ) {
				$slug = sanitize_title( $slug );
				if ( '' === $slug ) {
					continue;
				}
				$term = get_term_by( 'slug', $slug, $tax );
				if ( ! $term ) {
					$name = ( $slug === sanitize_title( $t['name'] ?? '' ) ) ? $t['name'] : $slug;
					$new  = wp_insert_term( $name, $tax, array( 'parent' => $parent_id ) );
					$leaf_id = is_wp_error( $new ) ? 0 : (int) $new['term_id'];
				} else {
					$leaf_id = (int) $term->term_id;
				}
				$parent_id = $leaf_id;
			}
			if ( $leaf_id ) {
				$by_tax[ $tax ][] = $leaf_id;
			}
		}
		foreach ( $by_tax as $tax => $ids ) {
			wp_set_object_terms( $post_id, $ids, $tax );
		}
	}

	private static function apply_named_fields( $post_id, array $record, $entity ) {
		foreach ( (array) ( $record['seo'] ?? array() ) as $field => $value ) {
			$key = Pi_Pix_Schema::seo_meta_key( $field );
			if ( $key && '' !== $value && null !== $value ) {
				update_post_meta( $post_id, $key, $value );
			}
		}
		foreach ( (array) ( $record['fields'] ?? array() ) as $field => $value ) {
			$key = Pi_Pix_Schema::field_meta_key( $entity, $field );
			if ( $key && '' !== $value && null !== $value ) {
				update_post_meta( $post_id, $key, $value );
			}
		}
	}

	private static function apply_language( $post_id, array $record ) {
		$lang = (array) ( $record['language'] ?? array() );
		if ( empty( $lang ) ) {
			return;
		}
		$map = Pi_Pix_Schema::language_map();
		if ( ! empty( $lang['code'] ) ) {
			update_post_meta( $post_id, $map['code'], $lang['code'] );
		}
		if ( ! empty( $lang['translation_group'] ) ) {
			update_post_meta( $post_id, $map['translation_group'], $lang['translation_group'] );
		}
	}

	/**
	 * Escape-hatch meta: delete-then-add per key so multi-value meta round-trips
	 * (fix for defect #2), but skips the delete when incoming values already
	 * match, narrowing the destructive delete/add window.
	 */
	private static function apply_meta_bag( $post_id, array $record ) {
		foreach ( (array) ( $record['meta'] ?? array() ) as $entry ) {
			$key    = $entry['key'];
			$values = array_values( (array) $entry['values'] );
			$existing = get_post_meta( $post_id, $key, false );
			sort( $existing );
			$incoming = $values;
			sort( $incoming );
			if ( $existing === $incoming ) {
				continue;
			}
			delete_post_meta( $post_id, $key );
			foreach ( $values as $v ) {
				add_post_meta( $post_id, $key, $v );
			}
		}
	}

	/**
	 * Idempotent by content hash first (works across re-exports even if the
	 * file path changes), falling back to the source path/URL the older
	 * catalog importer used, so previously-imported attachments are recognized
	 * rather than duplicated.
	 */
	private static function apply_images( $post_id, array $record, array $file_map, array $assets ) {
		$results = array();
		$slug    = $record['identity']['slug'] ?? (string) $post_id;

		// 1. Featured image
		$featured_entry = $record['images']['featured'] ?? null;
		$featured_ref   = null;
		if ( is_string( $featured_entry ) && '' !== $featured_entry ) {
			$featured_ref = $featured_entry;
		} elseif ( is_array( $featured_entry ) && ! empty( $featured_entry['id'] ) ) {
			$featured_ref = $featured_entry['id'];
		} elseif ( is_array( $featured_entry ) && ! empty( $featured_entry['file'] ) ) {
			$featured_ref = $slug . ':featured';
		}

		if ( $featured_ref && isset( $file_map[ $featured_ref ] ) ) {
			$alt = is_array( $featured_entry ) ? ( $featured_entry['alt'] ?? '' ) : ( $assets[ $featured_ref ]['alt'] ?? '' );
			$att = self::attach_from_file( $file_map[ $featured_ref ], $post_id, $alt );
			$results[] = $att;
			if ( $att['ok'] ) {
				set_post_thumbnail( $post_id, $att['attachment_id'] );
			}
		}

		// 2. Gallery images
		$gallery_ids = array();
		foreach ( (array) ( $record['images']['gallery'] ?? array() ) as $i => $g_entry ) {
			$g_ref = null;
			if ( is_string( $g_entry ) && '' !== $g_entry ) {
				$g_ref = $g_entry;
			} elseif ( is_array( $g_entry ) && ! empty( $g_entry['id'] ) ) {
				$g_ref = $g_entry['id'];
			} elseif ( is_array( $g_entry ) && ! empty( $g_entry['file'] ) ) {
				$g_ref = $slug . ':gallery-' . ( $i + 1 );
			}

			if ( ! $g_ref || ! isset( $file_map[ $g_ref ] ) ) {
				continue;
			}
			$alt = is_array( $g_entry ) ? ( $g_entry['alt'] ?? '' ) : ( $assets[ $g_ref ]['alt'] ?? '' );
			$att = self::attach_from_file( $file_map[ $g_ref ], $post_id, $alt );
			$results[] = $att;
			if ( $att['ok'] ) {
				$gallery_ids[] = $att['attachment_id'];
			}
		}
		if ( $gallery_ids ) {
			update_post_meta( $post_id, Pi_Pix_Schema::GALLERY_META, $gallery_ids );
			update_post_meta( $post_id, 'gallery', $gallery_ids );
			update_post_meta( $post_id, 'du_an_gallery', $gallery_ids );
		}

		// 3. Inline images in content
		$inline_url_map = array();
		foreach ( (array) ( $record['images']['inline'] ?? array() ) as $i => $item ) {
			$ref = null;
			$old_src = '';
			if ( is_string( $item ) && '' !== $item ) {
				$ref = $item;
			} elseif ( is_array( $item ) ) {
				$ref = $item['id'] ?? ( ! empty( $item['file'] ) ? $slug . ':inline-' . ( $i + 1 ) : null );
				$old_src = $item['src'] ?? '';
			}

			if ( ! $ref || ! isset( $file_map[ $ref ] ) ) {
				continue;
			}
			$alt = is_array( $item ) ? ( $item['alt'] ?? '' ) : ( $assets[ $ref ]['alt'] ?? '' );
			$att = self::attach_from_file( $file_map[ $ref ], $post_id, $alt );
			$results[] = $att;
			if ( $att['ok'] && $old_src ) {
				$new_url = wp_get_attachment_url( $att['attachment_id'] );
				if ( $new_url && $new_url !== $old_src ) {
					$inline_url_map[ $old_src ] = $new_url;
				}
			}
		}

		// Rewrite URLs in post_content if inline images were imported
		if ( ! empty( $inline_url_map ) ) {
			$post_obj = get_post( $post_id );
			if ( $post_obj && ! empty( $post_obj->post_content ) ) {
				$updated_content = str_replace( array_keys( $inline_url_map ), array_values( $inline_url_map ), $post_obj->post_content );
				if ( $updated_content !== $post_obj->post_content ) {
					wp_update_post( array(
						'ID'           => $post_id,
						'post_content' => $updated_content,
					) );
				}
			}
		}

		return $results;
	}

	private static function attach_from_file( $abs_path, $post_id, $alt = '' ) {
		if ( ! file_exists( $abs_path ) ) {
			return array( 'ok' => false, 'reused' => false, 'file' => $abs_path, 'error' => 'file not found' );
		}
		$hash = hash_file( 'sha256', $abs_path );

		$existing = get_posts( array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			'meta_key'       => Pi_Pix_Schema::ASSET_HASH_META,
			'meta_value'     => $hash,
			'fields'         => 'ids',
		) );
		if ( $existing ) {
			return array( 'ok' => true, 'reused' => true, 'attachment_id' => (int) $existing[0] );
		}

		if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
			require_once ABSPATH . 'wp-admin/includes/image.php';
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/media.php';
		}

		$filetype = wp_check_filetype( basename( $abs_path ) );
		if ( empty( $filetype['type'] ) ) {
			return array( 'ok' => false, 'reused' => false, 'file' => $abs_path, 'error' => 'unrecognized file type' );
		}
		$upload_dir = wp_upload_dir();
		$filename   = wp_unique_filename( $upload_dir['path'], basename( $abs_path ) );
		$dest       = $upload_dir['path'] . '/' . $filename;
		if ( ! copy( $abs_path, $dest ) ) {
			return array( 'ok' => false, 'reused' => false, 'file' => $abs_path, 'error' => 'copy failed' );
		}
		$attach_id = wp_insert_attachment( array(
			'post_mime_type' => $filetype['type'],
			'post_title'     => sanitize_file_name( pathinfo( $filename, PATHINFO_FILENAME ) ),
			'post_status'    => 'inherit',
		), $dest, $post_id );
		if ( is_wp_error( $attach_id ) || ! $attach_id ) {
			return array( 'ok' => false, 'reused' => false, 'file' => $abs_path, 'error' => is_wp_error( $attach_id ) ? $attach_id->get_error_message() : 'wp_insert_attachment failed' );
		}
		wp_update_attachment_metadata( $attach_id, wp_generate_attachment_metadata( $attach_id, $dest ) );
		update_post_meta( $attach_id, Pi_Pix_Schema::ASSET_HASH_META, $hash );
		if ( '' !== $alt ) {
			update_post_meta( $attach_id, '_wp_attachment_image_alt', sanitize_text_field( $alt ) );
		}

		return array( 'ok' => true, 'reused' => false, 'attachment_id' => (int) $attach_id );
	}
}
