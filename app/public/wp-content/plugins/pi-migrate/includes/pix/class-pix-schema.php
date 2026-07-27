<?php
/**
 * PIX-1 schema: the single source of truth for the canonical import/export
 * standard shared by saigonhoreca and saigonhouse.
 *
 * Every other PIX class reads its maps from here. Nothing else may hardcode a
 * meta key, a taxonomy name, or a post type.
 *
 * Why this exists: before PIX-1 there were three overlapping formats (pi-api's
 * JSON contract, the catalog's 20-column import.csv, and per-brand data.json),
 * and the catalog wrote SEO into `_seo_*`/`_og_image` and attributes into
 * `_attr_*` — keys no template on either site reads, so the data was inert.
 * The maps below are derived from what the themes actually read:
 *   - SEO: themes/saigonhoreca-theme/inc/site/pi-seo-resolver.php
 *   - product render: themes/saigonhoreca-theme/single.php (reads only _sku, _source_url)
 *   - taxonomies/CPT: each theme's inc/engine/cpt-registration.php
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Pi_Pix_Schema {

	const VERSION      = '1';
	const UUID_NS      = '6ba7b811-9dad-11d1-80b4-00c04fd430c8'; // RFC 4122 URL namespace
	const PIX_NS_NAME  = 'https://pix.pitechco.com/1';
	const UID_META_KEY = '_pix_uid';

	/** Attachment meta key holding an asset's content hash, for idempotent reuse. */
	const ASSET_HASH_META = '_pix_asset_sha256';

	/** Gallery attachment ids (featured excluded). No template reads this yet — declared now, rendered later. */
	const GALLERY_META = '_pix_gallery_ids';

	const STATUSES = array( 'publish', 'draft', 'pending', 'private', 'future' );

	const LIFECYCLES = array( 'active', 'discontinued', 'legacy_page_public', 'unknown' );

	const CONTENT_FORMATS = array( 'html', 'blocks' );

	const LANGUAGES = array( 'vi', 'en' );

	/**
	 * Logical entity => post_type, per site.
	 * A null value means the site cannot host that entity: the validator hard-fails
	 * rather than silently coercing to 'post' the way pi-api does.
	 */
	private static $entity_map = array(
		'saigonhoreca' => array(
			'post'       => 'post',
			'page'       => 'page',
			'product'    => 'product',
			'project'    => 'project',
			'attachment' => 'attachment',
		),
		'saigonhouse'  => array(
			'post'       => 'post',
			'page'       => 'page',
			'product'    => null,
			'project'    => 'du_an',
			'attachment' => 'attachment',
		),
	);

	/**
	 * Logical vocabulary => taxonomy, per site, optionally keyed by entity.
	 * '*' is the fallback for entities not listed.
	 *
	 * `product_cat` is deliberately absent: it holds legacy WooCommerce data and
	 * the archive sidebar still falls back to it, so it stays read-only. PIX-1
	 * writes `product_category` only.
	 */
	private static $vocabulary_map = array(
		'saigonhoreca' => array(
			'category' => array( 'product' => 'product_category', 'project' => 'project_category', '*' => 'category' ),
			'brand'    => array( 'product' => 'product_brand' ),
			'tag'      => array( '*' => 'post_tag' ),
		),
		'saigonhouse'  => array(
			'category' => array( '*' => 'category' ),
			'brand'    => array(),
			'tag'      => array( '*' => 'post_tag' ),
		),
	);

	/** PIX `seo.*` field => the meta key the renderer reads. */
	private static $seo_map = array(
		'title'         => '_pi_seo_title',
		'description'   => '_pi_seo_description',
		'og_title'      => '_pi_og_title',
		'og_description'=> '_pi_og_description',
		'og_image_ref'  => '_pi_og_image',
		'og_image_alt'  => '_pi_og_image_alt',
		'focus_keyword' => '_pi_seo_focus_keyword',
		'noindex'       => '_pi_seo_noindex',
		'canonical'     => '_pi_seo_canonical',
		'robots'        => '_pi_seo_robots',
		'nofollow'      => '_pi_seo_nofollow',
		'schema_type'   => '_pi_seo_schema_type',
	);

	/** PIX `fields.*` => meta key, per entity. Only `sku`/`source_url` have readers today. */
	private static $field_map = array(
		'product' => array(
			'sku'               => '_sku',
			'source_url'        => '_source_url',
			'lifecycle'         => '_pi_lifecycle',
			'series'            => '_pi_series',
			'availability_note' => '_pi_availability_note',
		),
	);

	/** PIX `language.*` => meta key. Unprefixed, matching pi-multilang. */
	private static $language_map = array(
		'translation_group' => 'pi_translation_group',
		'code'              => 'pi_language',
	);

	/**
	 * Meta keys that must never appear in a record's `meta` bag.
	 * The `_seo_*`/`_og_image`/`_attr_*` entries are the dead keys PIX-1 exists
	 * to eliminate; the rest are WP-internal or handled by a named field.
	 */
	private static $forbidden_meta_exact = array(
		'_seo_title', '_seo_description', '_seo_desc', '_og_image', '_og_image_url',
		'_og_title', '_og_description',
		'_pi_seo_desc', // read-alias only; writing it is a bug
		'_pi_migrate_brand', '_pi_migrate_gallery_ids',
		'_thumbnail_id', '_edit_lock', '_edit_last',
		'_wp_old_slug', '_wp_old_date', '_wp_desired_post_slug',
		'_wp_trash_meta_status', '_wp_trash_meta_time',
	);

	private static $forbidden_meta_prefix = array( '_attr_', '_attribute_', '_oembed_' );

	/**
	 * Attachment meta prefix pi-api blanket-blocks, which permanently killed alt
	 * text on round-trip. PIX-1 keeps the prefix block but allowlists alt text.
	 */
	private static $blocked_meta_prefix_with_allowlist = array(
		'_wp_attachment_' => array( '_wp_attachment_image_alt' ),
	);

	// ---------------------------------------------------------------
	// Lookups
	// ---------------------------------------------------------------

	public static function sites() {
		return array_keys( self::$entity_map );
	}

	public static function entities() {
		return array_keys( self::$entity_map['saigonhoreca'] );
	}

	/**
	 * @return string|null Post type, or null when the site cannot host this entity.
	 */
	public static function post_type_for( $entity, $site_key ) {
		$map = self::$entity_map[ $site_key ] ?? array();
		return array_key_exists( $entity, $map ) ? $map[ $entity ] : null;
	}

	public static function entity_is_known( $entity ) {
		return in_array( $entity, self::entities(), true );
	}

	/**
	 * @return string|null Taxonomy, or null when unsupported for this site/entity.
	 */
	public static function taxonomy_for( $vocabulary, $site_key, $entity ) {
		$vocabs = self::$vocabulary_map[ $site_key ] ?? array();
		if ( ! isset( $vocabs[ $vocabulary ] ) ) {
			return null;
		}
		$per_entity = $vocabs[ $vocabulary ];
		if ( isset( $per_entity[ $entity ] ) ) {
			return $per_entity[ $entity ];
		}
		return $per_entity['*'] ?? null;
	}

	/**
	 * Reverse of taxonomy_for() — used by exporters walking live WP taxonomies
	 * and needing the logical vocabulary name a record should carry.
	 *
	 * @return string|null
	 */
	public static function vocabulary_for_taxonomy( $taxonomy, $site_key, $entity ) {
		$vocabs = self::$vocabulary_map[ $site_key ] ?? array();
		foreach ( $vocabs as $vocab => $per_entity ) {
			$mapped = $per_entity[ $entity ] ?? ( $per_entity['*'] ?? null );
			if ( $mapped === $taxonomy ) {
				return $vocab;
			}
		}
		return null;
	}

	public static function vocabulary_is_known( $vocabulary ) {
		foreach ( self::$vocabulary_map as $vocabs ) {
			if ( isset( $vocabs[ $vocabulary ] ) ) {
				return true;
			}
		}
		return false;
	}

	public static function seo_map() {
		return self::$seo_map;
	}

	public static function seo_meta_key( $seo_field ) {
		return self::$seo_map[ $seo_field ] ?? null;
	}

	public static function field_map( $entity ) {
		return self::$field_map[ $entity ] ?? array();
	}

	public static function field_meta_key( $entity, $field ) {
		return self::$field_map[ $entity ][ $field ] ?? null;
	}

	public static function language_map() {
		return self::$language_map;
	}

	/**
	 * Every meta key that a named PIX field owns. A record putting one of these
	 * in its `meta` bag is ambiguous and is rejected.
	 */
	public static function reserved_meta_keys( $entity ) {
		return array_values( array_merge(
			array_values( self::$seo_map ),
			array_values( self::field_map( $entity ) ),
			array_values( self::$language_map ),
			array( self::UID_META_KEY, self::GALLERY_META, self::ASSET_HASH_META )
		) );
	}

	/**
	 * @return string|null Reason the key is rejected, or null when allowed.
	 */
	public static function meta_key_rejection( $key, $entity ) {
		if ( ! is_string( $key ) || '' === $key ) {
			return 'meta key must be a non-empty string';
		}
		if ( in_array( $key, self::$forbidden_meta_exact, true ) ) {
			return "forbidden meta key (no template reads it, or it is WP-internal): {$key}";
		}
		foreach ( self::$forbidden_meta_prefix as $prefix ) {
			if ( 0 === strpos( $key, $prefix ) ) {
				return "forbidden meta key prefix '{$prefix}': {$key}";
			}
		}
		foreach ( self::$blocked_meta_prefix_with_allowlist as $prefix => $allow ) {
			if ( 0 === strpos( $key, $prefix ) && ! in_array( $key, $allow, true ) ) {
				return "blocked attachment meta prefix '{$prefix}': {$key}";
			}
		}
		if ( in_array( $key, self::reserved_meta_keys( $entity ), true ) ) {
			return "meta key is owned by a named PIX field, set that field instead: {$key}";
		}
		return null;
	}

	/**
	 * Deterministic UUIDv5 so the same natural key always yields the same uid,
	 * on any machine, without a registry. This is what makes re-import a no-op.
	 */
	public static function mint_uid( $entity, $namespace, $key ) {
		return self::uuid5( self::uuid5( self::UUID_NS, self::PIX_NS_NAME ), "{$entity}:{$namespace}:{$key}" );
	}

	public static function uuid5( $namespace_uuid, $name ) {
		$nhex = str_replace( array( '-', '{', '}' ), '', $namespace_uuid );
		$nstr = '';
		for ( $i = 0; $i < strlen( $nhex ); $i += 2 ) {
			$nstr .= chr( hexdec( $nhex[ $i ] . $nhex[ $i + 1 ] ) );
		}
		$hash = sha1( $nstr . $name );
		return sprintf(
			'%08s-%04s-%04x-%04x-%12s',
			substr( $hash, 0, 8 ),
			substr( $hash, 8, 4 ),
			( hexdec( substr( $hash, 12, 4 ) ) & 0x0fff ) | 0x5000,
			( hexdec( substr( $hash, 16, 4 ) ) & 0x3fff ) | 0x8000,
			substr( $hash, 20, 12 )
		);
	}

	/**
	 * Canonical JSON: sorted keys, no whitespace, unescaped unicode/slashes.
	 * The envelope's records_digest is a sha256 over this, so two builds of the
	 * same data must produce byte-identical output.
	 */
	public static function canonical_json( $data ) {
		return wp_json_encode( self::sort_deep( $data ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
	}

	private static function sort_deep( $data ) {
		if ( ! is_array( $data ) ) {
			return $data;
		}
		$is_list = array_keys( $data ) === range( 0, count( $data ) - 1 );
		$out     = array();
		foreach ( $data as $k => $v ) {
			$out[ $k ] = self::sort_deep( $v );
		}
		if ( ! $is_list ) {
			ksort( $out, SORT_STRING );
		}
		return $out;
	}

	public static function records_digest( array $records ) {
		return hash( 'sha256', self::canonical_json( $records ) );
	}
}
