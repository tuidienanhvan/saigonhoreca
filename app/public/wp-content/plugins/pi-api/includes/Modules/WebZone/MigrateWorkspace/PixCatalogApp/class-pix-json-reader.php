<?php
/**
 * Reads a single .json file into a PIX-1 envelope.
 *
 * Accepts a bare array of items, or a wrapper object under one of the keys
 * items / products / data. The item-to-record mapping, asset synthesis and
 * envelope assembly live in the Pi_Pix_Item_Converter trait (shared with
 * the CSV reader) — this class only implements the JSON-specific parsing.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Pi_Pix_Json_Reader {

	use Pi_Pix_Item_Converter;

	/**
	 * @return array{envelope:array,file_map:array,raw_records:array}|WP_Error
	 */
	public static function read( $file_path, $site_key = 'saigonhoreca' ) {
		return self::build_envelope( $file_path, $site_key );
	}

	protected static function parse_file( $file_path ) {
		if ( ! file_exists( $file_path ) ) {
			return new WP_Error( 'pi_pix_json_missing', "JSON file not found: {$file_path}" );
		}
		$data = json_decode( file_get_contents( $file_path ), true );
		if ( ! is_array( $data ) ) {
			return new WP_Error( 'pi_pix_json_invalid', "Invalid JSON file: {$file_path}" );
		}
		foreach ( array( 'items', 'products', 'data' ) as $key ) {
			if ( isset( $data[ $key ] ) && is_array( $data[ $key ] ) ) {
				return $data[ $key ];
			}
		}
		$is_list = array_keys( $data ) === range( 0, count( $data ) - 1 );
		return $is_list ? $data : array( $data );
	}
}
