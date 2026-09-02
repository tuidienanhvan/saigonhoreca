<?php
/**
 * Reads a single .csv file into a PIX-1 envelope. Header row + data rows;
 * BOM on the first header cell is stripped. Rows whose column count does
 * not match the header are skipped (mirrors the catalog importer's reader).
 *
 * The item-to-record mapping, asset synthesis and envelope assembly live in
 * the Pi_Pix_Item_Converter trait (shared with the JSON reader) — this class
 * only implements the CSV-specific parsing.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Pi_Pix_Csv_Reader {

	use Pi_Pix_Item_Converter;

	/**
	 * @return array{envelope:array,file_map:array,raw_records:array}|WP_Error
	 */
	public static function read( $file_path, $site_key = 'saigonhoreca' ) {
		return self::build_envelope( $file_path, $site_key );
	}

	protected static function parse_file( $file_path ) {
		if ( ! file_exists( $file_path ) ) {
			return new WP_Error( 'pi_pix_csv_missing', "CSV file not found: {$file_path}" );
		}
		$handle = fopen( $file_path, 'r' );
		if ( ! $handle ) {
			return new WP_Error( 'pi_pix_csv_open_failed', "Cannot open CSV file: {$file_path}" );
		}
		$header = fgetcsv( $handle );
		if ( ! $header ) {
			fclose( $handle );
			return new WP_Error( 'pi_pix_csv_empty', "CSV file is empty: {$file_path}" );
		}
		$header[0] = preg_replace( '/^\xEF\xBB\xBF/', '', $header[0] ); // strip UTF-8 BOM

		$rows = array();
		while ( ( $row = fgetcsv( $handle ) ) !== false ) {
			if ( count( $row ) !== count( $header ) ) {
				continue;
			}
			$rows[] = array_combine( $header, $row );
		}
		fclose( $handle );

		if ( empty( $rows ) ) {
			return new WP_Error( 'pi_pix_csv_no_data', "No data rows found in CSV: {$file_path}" );
		}
		return $rows;
	}
}
