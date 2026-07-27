<?php
/**
 * PIX-1 validator.
 *
 * Pure: no DB reads, no writes, no WP functions beyond wp_json_encode/sanitize.
 * Runs over the WHOLE bundle before any import writes anything, so a bad bundle
 * can never half-import — the failure mode pi-api has today, where a mid-batch
 * error leaves everything before it committed.
 *
 * Two severities:
 *   ERROR — abort the entire bundle, write nothing.
 *   WARN  — proceed, but record it; these encode the real data-quality problems
 *           found in the catalog audit (thin content, degenerate SEO, duplicate
 *           titles) which are fixed upstream by the crawl/content pass, not here.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Pi_Pix_Validator {

	/** Bounds are advisory (WARN only) — chosen from the audit's observed failures. */
	const SEO_TITLE_MIN = 15;
	const SEO_TITLE_MAX = 60;
	const SEO_DESC_MIN  = 50;
	const SEO_DESC_MAX  = 160;
	const CONTENT_MIN   = 200;

	private $errors   = array();
	private $warnings = array();
	private $site_key;

	public function __construct( $site_key ) {
		$this->site_key = $site_key;
	}

	/**
	 * @param array $envelope Decoded pix.json (envelope + assets + records).
	 * @return array{ok:bool,errors:array,warnings:array,counts:array}
	 */
	public function validate( array $envelope ) {
		$this->errors   = array();
		$this->warnings = array();

		if ( ! in_array( $this->site_key, Pi_Pix_Schema::sites(), true ) ) {
			$this->error( 'E000', "unknown target site '{$this->site_key}'" );
			return $this->result( 0, 0 );
		}

		$this->validate_envelope( $envelope );

		$assets  = isset( $envelope['assets'] ) && is_array( $envelope['assets'] ) ? $envelope['assets'] : array();
		$records = isset( $envelope['records'] ) && is_array( $envelope['records'] ) ? $envelope['records'] : array();

		if ( empty( $records ) ) {
			$this->error( 'E003', 'bundle contains no records' );
			return $this->result( 0, count( $assets ) );
		}

		$this->validate_digest( $envelope, $records );

		$seen_uid       = array();
		$seen_sku       = array();
		$seen_title     = array();   // "brand|title" => count
		$referenced     = array();
		$seen_lang_pair = array();

		foreach ( $records as $i => $record ) {
			if ( ! is_array( $record ) ) {
				$this->error( 'E003', "record #{$i} is not an object" );
				continue;
			}
			$this->validate_record( $record, $i, $assets, $seen_uid, $seen_sku, $seen_title, $referenced, $seen_lang_pair );
		}

		foreach ( $seen_title as $key => $count ) {
			if ( $count > 1 ) {
				$this->warn( 'W101', "duplicate title within the same brand ({$count}x): {$key}" );
			}
		}

		$this->validate_assets( $assets, $referenced );

		return $this->result( count( $records ), count( $assets ) );
	}

	// ---------------------------------------------------------------

	private function validate_envelope( array $e ) {
		if ( ( $e['pix'] ?? null ) !== Pi_Pix_Schema::VERSION ) {
			$this->error( 'E001', "unsupported pix version: " . var_export( $e['pix'] ?? null, true ) . ' (expected ' . Pi_Pix_Schema::VERSION . ')' );
		}
		if ( ! in_array( $e['kind'] ?? '', array( 'bundle' ), true ) ) {
			$this->error( 'E001', "unknown bundle kind: " . var_export( $e['kind'] ?? null, true ) );
		}
		$hint = $e['target_hint']['site_key'] ?? null;
		if ( $hint && $hint !== $this->site_key ) {
			$this->warn( 'W120', "bundle was built for site '{$hint}' but is being validated against '{$this->site_key}'" );
		}
		$declared = $e['counts']['records'] ?? null;
		$actual   = is_array( $e['records'] ?? null ) ? count( $e['records'] ) : 0;
		if ( null !== $declared && (int) $declared !== $actual ) {
			$this->error( 'E015', "counts.records={$declared} but bundle holds {$actual} records" );
		}
	}

	private function validate_digest( array $e, array $records ) {
		$declared = $e['checksum']['records_digest'] ?? null;
		if ( ! $declared ) {
			$this->error( 'E002', 'envelope is missing checksum.records_digest' );
			return;
		}
		$actual = Pi_Pix_Schema::records_digest( $records );
		if ( ! hash_equals( (string) $declared, $actual ) ) {
			$this->error( 'E002', "records_digest mismatch (declared {$declared}, computed {$actual}) — bundle is corrupt or was edited after build" );
		}
	}

	private function validate_record( array $r, $i, array $assets, array &$seen_uid, array &$seen_sku, array &$seen_title, array &$referenced, array &$seen_lang_pair ) {
		$label = "record #{$i}";

		// --- identity ---
		$uid = $r['uid'] ?? '';
		if ( ! is_string( $uid ) || '' === $uid ) {
			$this->error( 'E003', "{$label}: missing uid" );
		} elseif ( isset( $seen_uid[ $uid ] ) ) {
			$this->error( 'E004', "{$label}: duplicate uid {$uid} (first seen at record #{$seen_uid[$uid]})" );
		} else {
			$seen_uid[ $uid ] = $i;
		}

		$title = trim( (string) ( $r['title'] ?? '' ) );
		if ( '' === $title ) {
			$this->error( 'E003', "{$label}: missing title" );
		}
		if ( $title !== '' ) {
			$label .= " ('" . self::cut( $title, 50 ) . "')";
		}

		// --- entity ---
		$entity = $r['entity'] ?? '';
		if ( ! Pi_Pix_Schema::entity_is_known( $entity ) ) {
			$this->error( 'E005', "{$label}: unknown entity '{$entity}'" );
			return; // everything downstream depends on entity
		}
		if ( null === Pi_Pix_Schema::post_type_for( $entity, $this->site_key ) ) {
			$this->error( 'E005', "{$label}: entity '{$entity}' has no post type on site '{$this->site_key}'" );
		}

		// --- status ---
		$status = $r['status'] ?? '';
		if ( ! in_array( $status, Pi_Pix_Schema::STATUSES, true ) ) {
			$this->error( 'E011', "{$label}: invalid status '{$status}'" );
		}

		// --- content ---
		$format = $r['content']['format'] ?? 'html';
		if ( ! in_array( $format, Pi_Pix_Schema::CONTENT_FORMATS, true ) ) {
			$this->error( 'E017', "{$label}: invalid content.format '{$format}'" );
		}
		$content = (string) ( $r['content']['value'] ?? '' );
		$plain   = trim( html_entity_decode( wp_strip_all_tags( $content ), ENT_QUOTES, 'UTF-8' ) );
		if ( self::len( $plain ) < self::CONTENT_MIN ) {
			$this->warn( 'W105', "{$label}: thin content (" . self::len( $plain ) . ' chars)' );
		}
		if ( preg_match( '/&#\d+;|&[a-z]+;/i', $content ) ) {
			$this->warn( 'W106', "{$label}: content still holds raw HTML entities" );
		}

		// --- title shape ---
		if ( preg_match( '/\|\s*([^|]+?)\s*\|\s*\1\s*$/u', $title ) ) {
			$this->warn( 'W102', "{$label}: title repeats the brand suffix twice" );
		}

		// --- terms ---
		$brand_slug = '';
		foreach ( (array) ( $r['terms'] ?? array() ) as $t ) {
			if ( ! is_array( $t ) || empty( $t['vocabulary'] ) ) {
				$this->error( 'E006', "{$label}: term entry missing vocabulary" );
				continue;
			}
			$vocab = $t['vocabulary'];
			if ( ! Pi_Pix_Schema::vocabulary_is_known( $vocab ) ) {
				$this->error( 'E006', "{$label}: unknown vocabulary '{$vocab}'" );
				continue;
			}
			if ( null === Pi_Pix_Schema::taxonomy_for( $vocab, $this->site_key, $entity ) ) {
				$this->error( 'E006', "{$label}: vocabulary '{$vocab}' is unsupported for entity '{$entity}' on site '{$this->site_key}'" );
				continue;
			}
			$slug = (string) ( $t['slug'] ?? '' );
			$path = $t['path'] ?? null;
			if ( is_array( $path ) && $path ) {
				$last = (string) end( $path );
				if ( $slug !== '' && $last !== $slug ) {
					$this->error( 'E014', "{$label}: terms.path last element '{$last}' does not match slug '{$slug}'" );
				}
			}
			if ( 'brand' === $vocab ) {
				$brand_slug = $slug;
			}
		}

		// --- images ---
		$featured = $r['images']['featured'] ?? null;
		foreach ( array( 'featured', 'gallery', 'inline' ) as $slot ) {
			$refs = $r['images'][ $slot ] ?? null;
			if ( null === $refs || '' === $refs ) {
				continue;
			}
			foreach ( (array) $refs as $ref ) {
				if ( ! isset( $assets[ $ref ] ) ) {
					$this->error( 'E007', "{$label}: images.{$slot} references unknown asset '{$ref}'" );
					continue;
				}
				$referenced[ $ref ] = true;
			}
		}
		if ( empty( $featured ) ) {
			$this->warn( 'W112', "{$label}: no featured image" );
		}

		// --- seo ---
		$seo = is_array( $r['seo'] ?? null ) ? $r['seo'] : array();
		foreach ( array_keys( $seo ) as $seo_field ) {
			if ( null === Pi_Pix_Schema::seo_meta_key( $seo_field ) ) {
				$this->error( 'E018', "{$label}: unknown seo field '{$seo_field}'" );
			}
		}
		$seo_title = trim( (string) ( $seo['title'] ?? '' ) );
		if ( '' !== $seo_title ) {
			$len = self::len( $seo_title );
			if ( $len < self::SEO_TITLE_MIN || $len > self::SEO_TITLE_MAX ) {
				$this->warn( 'W103', "{$label}: seo.title length {$len} outside " . self::SEO_TITLE_MIN . '-' . self::SEO_TITLE_MAX );
			}
		}
		$seo_desc = trim( (string) ( $seo['description'] ?? '' ) );
		if ( '' !== $seo_desc ) {
			$len = self::len( $seo_desc );
			if ( $len < self::SEO_DESC_MIN || $len > self::SEO_DESC_MAX ) {
				$this->warn( 'W104', "{$label}: seo.description length {$len} outside " . self::SEO_DESC_MIN . '-' . self::SEO_DESC_MAX );
			}
			$note = trim( (string) ( $r['fields']['availability_note'] ?? '' ) );
			if ( '' !== $note && 0 === strcasecmp( $seo_desc, $note ) ) {
				$this->warn( 'W104', "{$label}: seo.description merely repeats availability_note" );
			}
		}
		if ( ! empty( $seo['og_image_ref'] ) && $featured && $seo['og_image_ref'] === $featured ) {
			$this->warn( 'W110', "{$label}: seo.og_image_ref duplicates the featured image — omit it and let the renderer derive it" );
		}

		// --- fields ---
		$fields = is_array( $r['fields'] ?? null ) ? $r['fields'] : array();
		$known  = Pi_Pix_Schema::field_map( $entity );
		foreach ( array_keys( $fields ) as $fname ) {
			if ( ! isset( $known[ $fname ] ) ) {
				$this->error( 'E019', "{$label}: unknown field '{$fname}' for entity '{$entity}'" );
			}
		}
		if ( isset( $fields['lifecycle'] ) ) {
			if ( ! in_array( $fields['lifecycle'], Pi_Pix_Schema::LIFECYCLES, true ) ) {
				$this->error( 'E020', "{$label}: invalid lifecycle '{$fields['lifecycle']}'" );
			} elseif ( 'unknown' === $fields['lifecycle'] ) {
				$this->warn( 'W111', "{$label}: lifecycle is 'unknown'" );
			}
		}
		if ( 'product' === $entity ) {
			$sku = trim( (string) ( $fields['sku'] ?? '' ) );
			if ( '' === $sku ) {
				$this->error( 'E016', "{$label}: product requires fields.sku" );
			} else {
				$sku_key = $brand_slug . '|' . $sku;
				if ( isset( $seen_sku[ $sku_key ] ) ) {
					$this->error( 'E016', "{$label}: duplicate sku '{$sku}' within brand '{$brand_slug}' (first at record #{$seen_sku[$sku_key]})" );
				} else {
					$seen_sku[ $sku_key ] = $i;
				}
			}
		}

		if ( '' !== $title ) {
			$tkey = $brand_slug . '|' . $title;
			$seen_title[ $tkey ] = ( $seen_title[ $tkey ] ?? 0 ) + 1;
		}

		// --- language ---
		$lang = is_array( $r['language'] ?? null ) ? $r['language'] : array();
		$code = $lang['code'] ?? null;
		if ( null !== $code ) {
			if ( ! in_array( $code, Pi_Pix_Schema::LANGUAGES, true ) ) {
				$this->error( 'E013', "{$label}: unknown language.code '{$code}'" );
			} elseif ( ! empty( $lang['translation_group'] ) ) {
				$pair = $lang['translation_group'] . '|' . $code;
				if ( isset( $seen_lang_pair[ $pair ] ) ) {
					$this->error( 'E013', "{$label}: two records share translation_group + language '{$pair}' (first at record #{$seen_lang_pair[$pair]})" );
				} else {
					$seen_lang_pair[ $pair ] = $i;
				}
			}
		}

		// --- meta bag ---
		foreach ( (array) ( $r['meta'] ?? array() ) as $entry ) {
			if ( ! is_array( $entry ) || ! isset( $entry['key'] ) ) {
				$this->error( 'E009', "{$label}: meta entry must be an object with a 'key'" );
				continue;
			}
			$reason = Pi_Pix_Schema::meta_key_rejection( $entry['key'], $entity );
			if ( $reason ) {
				$this->error( 'E009', "{$label}: {$reason}" );
			}
			if ( ! array_key_exists( 'values', $entry ) || ! is_array( $entry['values'] ) ) {
				$this->error( 'E021', "{$label}: meta '{$entry['key']}' must carry a values array" );
			} elseif ( ! empty( $entry['single'] ) && count( $entry['values'] ) > 1 ) {
				$this->error( 'E021', "{$label}: meta '{$entry['key']}' is marked single but holds " . count( $entry['values'] ) . ' values' );
			}
		}

		// --- parent ---
		$parent = is_array( $r['parent'] ?? null ) ? $r['parent'] : array();
		if ( ! empty( $parent['uid'] ) && ! empty( $parent['slug'] ) && $parent['uid'] === $uid ) {
			$this->error( 'E012', "{$label}: record is its own parent" );
		}
	}

	private function validate_assets( array $assets, array $referenced ) {
		foreach ( $assets as $id => $a ) {
			if ( ! is_array( $a ) ) {
				$this->error( 'E008', "asset '{$id}' is not an object" );
				continue;
			}
			foreach ( array( 'sha256', 'path', 'bytes', 'mime' ) as $req ) {
				if ( empty( $a[ $req ] ) ) {
					$this->error( 'E008', "asset '{$id}' is missing {$req}" );
				}
			}
			if ( ! empty( $a['sha256'] ) && ! preg_match( '/^[a-f0-9]{64}$/', (string) $a['sha256'] ) ) {
				$this->error( 'E008', "asset '{$id}' has a malformed sha256" );
			}
			if ( ! empty( $a['path'] ) && 0 !== strpos( (string) $a['path'], 'assets/' ) ) {
				$this->error( 'E008', "asset '{$id}' path must live under assets/ (got '{$a['path']}')" );
			}
			$kind = $a['origin']['kind'] ?? null;
			if ( 'remote' === $kind ) {
				$this->warn( 'W108', "asset '{$id}' is still a remote reference — images must be materialised at build time, never hotlinked at import" );
			}
			if ( ! isset( $referenced[ $id ] ) ) {
				$this->warn( 'W109', "asset '{$id}' is not referenced by any record" );
			}
			if ( empty( $a['alt'] ) ) {
				$this->warn( 'W113', "asset '{$id}' has no alt text" );
			}
		}
	}

	// ---------------------------------------------------------------

	/**
	 * mbstring is not guaranteed on every host (and is absent from Local's CLI
	 * php.ini), so length/substring checks fall back to a UTF-8-aware regex
	 * count rather than assuming the extension is loaded.
	 */
	private static function len( $s ) {
		$s = (string) $s;
		if ( function_exists( 'mb_strlen' ) ) {
			return mb_strlen( $s, 'UTF-8' );
		}
		$n = preg_match_all( '/./us', $s );
		return false === $n ? strlen( $s ) : $n;
	}

	private static function cut( $s, $length ) {
		$s = (string) $s;
		if ( function_exists( 'mb_substr' ) ) {
			return mb_substr( $s, 0, $length, 'UTF-8' );
		}
		if ( preg_match( '/^.{0,' . (int) $length . '}/us', $s, $m ) ) {
			return $m[0];
		}
		return substr( $s, 0, $length );
	}

	private function error( $code, $message ) {
		$this->errors[] = array( 'code' => $code, 'message' => $message );
	}

	private function warn( $code, $message ) {
		$this->warnings[] = array( 'code' => $code, 'message' => $message );
	}

	private function result( $record_count, $asset_count ) {
		$by_code = array();
		foreach ( $this->warnings as $w ) {
			$by_code[ $w['code'] ] = ( $by_code[ $w['code'] ] ?? 0 ) + 1;
		}
		return array(
			'ok'       => empty( $this->errors ),
			'site'     => $this->site_key,
			'errors'   => $this->errors,
			'warnings' => $this->warnings,
			'counts'   => array(
				'records'          => $record_count,
				'assets'          => $asset_count,
				'errors'          => count( $this->errors ),
				'warnings'        => count( $this->warnings ),
				'warnings_by_code'=> $by_code,
			),
		);
	}
}
