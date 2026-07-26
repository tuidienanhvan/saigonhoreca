<?php
/**
 * Reads the PIX-1 folder export format written by Pi_Pix_Folder_Writer, and
 * adapts it into the id-based envelope shape Pi_Pix_Validator expects.
 *
 * On disk, a post's images are inline objects ({file, alt, sha256}) inside its
 * own post.json — deliberately different from the zip container's
 * content-addressed asset pool, since folder export optimizes for a human
 * opening one post's folder and reading it directly, not cross-post dedup.
 * The validator only understands id -> assets{} references, so this class
 * synthesizes ids ("{slug}:featured", "{slug}:gallery-1") purely for
 * validation; the importer resolves those same ids back to real file paths.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Pi_Pix_Folder_Reader {

	/**
	 * @return array{mode:string,root:string,post_dirs:array}|WP_Error
	 */
	public static function discover( $dir ) {
		$dir = rtrim( $dir, '/\\' );
		if ( ! is_dir( $dir ) ) {
			return new WP_Error( 'pi_pix_no_dir', "not a directory: {$dir}" );
		}

		if ( file_exists( $dir . '/manifest.json' ) ) {
			$manifest = json_decode( file_get_contents( $dir . '/manifest.json' ), true );
			if ( ! is_array( $manifest ) ) {
				return new WP_Error( 'pi_pix_bad_manifest', "manifest.json at {$dir} is not valid JSON" );
			}
			$folders = array();
			foreach ( (array) ( $manifest['folders'] ?? array() ) as $slug ) {
				if ( is_dir( $dir . '/' . $slug ) ) {
					$folders[] = $dir . '/' . $slug;
				}
			}
			return array( 'mode' => 'batch', 'root' => $dir, 'manifest' => $manifest, 'post_dirs' => $folders );
		}

		if ( file_exists( $dir . '/post.json' ) ) {
			return array( 'mode' => 'single', 'root' => $dir, 'manifest' => null, 'post_dirs' => array( $dir ) );
		}

		return new WP_Error( 'pi_pix_unrecognized', "{$dir} is neither a single post folder (post.json) nor a batch export (manifest.json)" );
	}

	/**
	 * Builds the validator-shape envelope PLUS a map of asset-id => absolute
	 * file path (with expected sha256), which the importer uses to actually
	 * read image bytes without re-deriving paths from record data.
	 *
	 * @return array{envelope:array,file_map:array,raw_records:array}|WP_Error
	 */
	public static function build_envelope( $dir, $site_key ) {
		$disc = self::discover( $dir );
		if ( is_wp_error( $disc ) ) {
			return $disc;
		}

		$raw_records = array();
		$assets      = array();
		$adapted     = array();
		$file_map    = array();

		foreach ( $disc['post_dirs'] as $post_dir ) {
			$post_json = $post_dir . '/post.json';
			if ( ! file_exists( $post_json ) ) {
				return new WP_Error( 'pi_pix_missing_post_json', "missing post.json in {$post_dir}" );
			}
			$record = json_decode( file_get_contents( $post_json ), true );
			if ( ! is_array( $record ) ) {
				return new WP_Error( 'pi_pix_bad_post_json', "post.json in {$post_dir} is not valid JSON" );
			}
			$raw_records[] = $record;

			$slug = $record['identity']['slug'] ?? basename( $post_dir );
			$adapted_record = $record;

			$featured = $record['images']['featured'] ?? null;
			if ( is_array( $featured ) && ! empty( $featured['file'] ) ) {
				$id = $slug . ':featured';
				self::register_asset( $assets, $file_map, $id, $post_dir, $featured );
				$adapted_record['images']['featured'] = $id;
			} else {
				$adapted_record['images']['featured'] = null;
			}

			$gallery_ids = array();
			foreach ( (array) ( $record['images']['gallery'] ?? array() ) as $i => $img ) {
				if ( ! is_array( $img ) || empty( $img['file'] ) ) {
					continue;
				}
				$id = $slug . ':gallery-' . ( $i + 1 );
				self::register_asset( $assets, $file_map, $id, $post_dir, $img );
				$gallery_ids[] = $id;
			}
			$adapted_record['images']['gallery'] = $gallery_ids;
			$adapted_record['images']['inline']  = array();

			$adapted[] = $adapted_record;
		}

		// The manifest's records_digest was computed over the RAW record shape
		// (inline {file,alt,sha256} image objects), because that is what
		// Pi_Pix_Folder_Writer had on hand at export time. Pi_Pix_Validator's
		// digest check always recomputes from whatever `records` array it is
		// given — which here must be the id-adapted shape, so images can be
		// resolved against `assets`. Those two serializations are never equal,
		// so the real tamper check happens HERE, against the raw shape, before
		// the validator ever runs; the envelope handed to the validator then
		// carries a digest of the adapted records so its own internal
		// recomputation is a trivial (and correct) no-op, not a false failure.
		if ( 'batch' === $disc['mode'] ) {
			$declared_raw_digest = $disc['manifest']['checksum']['records_digest'] ?? null;
			$actual_raw_digest   = Pi_Pix_Schema::records_digest( $raw_records );
			if ( $declared_raw_digest && ! hash_equals( (string) $declared_raw_digest, $actual_raw_digest ) ) {
				return new WP_Error(
					'pi_pix_digest_mismatch',
					"manifest.json records_digest mismatch (declared {$declared_raw_digest}, computed {$actual_raw_digest}) — a post.json was edited after export without regenerating the manifest"
				);
			}
		}

		$envelope = array(
			'pix'         => Pi_Pix_Schema::VERSION,
			'kind'        => 'bundle', // validator only special-cases 'bundle'; folder is a transport detail
			'target_hint' => array( 'site_key' => $site_key ),
			'counts'      => array( 'records' => count( $adapted ) ),
			'checksum'    => array( 'algo' => 'sha256', 'records_digest' => Pi_Pix_Schema::records_digest( $adapted ) ),
			'assets'      => $assets,
			'records'     => $adapted,
		);

		return array( 'envelope' => $envelope, 'file_map' => $file_map, 'raw_records' => $raw_records, 'mode' => $disc['mode'] );
	}

	/**
	 * @return array{ok:bool,errors:array,warnings:array,counts:array}|WP_Error
	 */
	public static function validate( $dir, $site_key ) {
		$built = self::build_envelope( $dir, $site_key );
		if ( is_wp_error( $built ) ) {
			return $built;
		}
		$validator = new Pi_Pix_Validator( $site_key );
		$report    = $validator->validate( $built['envelope'] );

		// The digest check above only proves internal self-consistency for a
		// batch export (declared vs recomputed from the folder's own post.json
		// files) — it does NOT re-derive from anything outside the folder, so a
		// hand-edited post.json with a hand-edited manifest would still pass.
		// That is an accepted limitation of a plain-folder format: real tamper
		// evidence belongs to the checksummed ZIP container, not this one.
		return $report;
	}

	private static function register_asset( array &$assets, array &$file_map, $id, $post_dir, array $img ) {
		$abs_path = $post_dir . '/' . ltrim( str_replace( '\\', '/', $img['file'] ), '/' );
		$assets[ $id ] = array(
			'sha256' => $img['sha256'] ?? '',
			'path'   => 'assets/' . substr( (string) ( $img['sha256'] ?? '' ), 0, 2 ) . '/' . basename( $abs_path ),
			'bytes'  => file_exists( $abs_path ) ? filesize( $abs_path ) : 0,
			'mime'   => file_exists( $abs_path ) ? ( wp_check_filetype( $abs_path )['type'] ?: 'application/octet-stream' ) : 'application/octet-stream',
			'alt'    => $img['alt'] ?? '',
			'origin' => array( 'kind' => 'local', 'ref' => $img['file'] ),
		);
		$file_map[ $id ] = $abs_path;
	}
}
