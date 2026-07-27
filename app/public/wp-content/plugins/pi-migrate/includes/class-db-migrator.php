<?php
/**
 * Database export/import and serialize-safe search-replace.
 * Delegates to WP-CLI (`wp db export/import`, `wp search-replace`) when available,
 * since WP-CLI's implementation is already mysqldump-based and serialize-safe.
 * Falls back to a pure-PHP dumper/replacer for non-CLI (REST/admin) triggers.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Pi_Migrate_DB_Migrator {

	public static function export( $file_path ) {
		if ( self::has_cli() ) {
			WP_CLI::runcommand( 'db export ' . escapeshellarg( $file_path ), array( 'launch' => false ) );
			return array(
				'file'  => $file_path,
				'bytes' => file_exists( $file_path ) ? filesize( $file_path ) : 0,
			);
		}
		return self::php_export( $file_path );
	}

	public static function import( $file_path ) {
		if ( ! file_exists( $file_path ) ) {
			return new WP_Error( 'pi_migrate_missing_file', "SQL file not found: {$file_path}" );
		}
		if ( self::has_cli() ) {
			WP_CLI::runcommand( 'db import ' . escapeshellarg( $file_path ), array( 'launch' => false ) );
			return array( 'file' => $file_path );
		}
		return self::php_import( $file_path );
	}

	/**
	 * @param string $old
	 * @param string $new
	 * @param bool   $dry_run
	 * @return array{tables_scanned:int,cells_changed:int,report?:array}
	 */
	public static function search_replace( $old, $new, $dry_run = false ) {
		if ( self::has_cli() ) {
			$args = 'search-replace ' . escapeshellarg( $old ) . ' ' . escapeshellarg( $new ) . ' --all-tables --report-changed-only';
			if ( $dry_run ) {
				$args .= ' --dry-run';
			}
			$result = WP_CLI::runcommand( $args, array( 'launch' => false, 'return' => 'all' ) );
			return array(
				'method' => 'wp-cli',
				'output' => is_object( $result ) ? $result->stdout : (string) $result,
			);
		}
		return self::php_search_replace( $old, $new, $dry_run );
	}

	private static function has_cli() {
		return defined( 'WP_CLI' ) && WP_CLI && class_exists( 'WP_CLI' );
	}

	// -------------------------------------------------------------------
	// Pure-PHP fallback (used when invoked via REST/admin, no shell access)
	// -------------------------------------------------------------------

	private static function php_export( $file_path ) {
		global $wpdb;
		$tables = $wpdb->get_col( 'SHOW TABLES' );
		$fh     = fopen( $file_path, 'w' );
		if ( ! $fh ) {
			return new WP_Error( 'pi_migrate_write_failed', "Cannot write to {$file_path}" );
		}
		fwrite( $fh, "-- pi-migrate PHP export — " . gmdate( 'c' ) . "\nSET FOREIGN_KEY_CHECKS=0;\n\n" );

		foreach ( $tables as $table ) {
			$create = $wpdb->get_row( "SHOW CREATE TABLE `{$table}`", ARRAY_N );
			fwrite( $fh, "DROP TABLE IF EXISTS `{$table}`;\n" . $create[1] . ";\n\n" );

			$rows = $wpdb->get_results( "SELECT * FROM `{$table}`", ARRAY_A );
			foreach ( $rows as $row ) {
				$cols = array_map( function ( $c ) {
					return '`' . $c . '`';
				}, array_keys( $row ) );
				$vals = array_map( function ( $v ) use ( $wpdb ) {
					return null === $v ? 'NULL' : "'" . $wpdb->_real_escape( $v ) . "'";
				}, array_values( $row ) );
				fwrite( $fh, "INSERT INTO `{$table}` (" . implode( ',', $cols ) . ') VALUES (' . implode( ',', $vals ) . ");\n" );
			}
			fwrite( $fh, "\n" );
		}
		fwrite( $fh, "SET FOREIGN_KEY_CHECKS=1;\n" );
		fclose( $fh );

		return array( 'file' => $file_path, 'bytes' => filesize( $file_path ), 'tables' => count( $tables ) );
	}

	private static function php_import( $file_path ) {
		global $wpdb;
		$sql        = file_get_contents( $file_path );
		$statements = array_filter( array_map( 'trim', explode( ";\n", $sql ) ) );
		$executed   = 0;
		foreach ( $statements as $statement ) {
			if ( '' === $statement || 0 === strpos( $statement, '--' ) ) {
				continue;
			}
			$result = $wpdb->query( $statement );
			if ( false === $result ) {
				return new WP_Error( 'pi_migrate_sql_error', $wpdb->last_error, array( 'statement' => substr( $statement, 0, 200 ) ) );
			}
			$executed++;
		}
		return array( 'statements_executed' => $executed );
	}

	/**
	 * Serialize-safe recursive replace, scanned across every text-like column
	 * of every table. Mirrors the approach used by wp-cli/search-replace-command.
	 */
	private static function php_search_replace( $old, $new, $dry_run ) {
		global $wpdb;
		$tables        = $wpdb->get_col( 'SHOW TABLES' );
		$cells_changed = 0;
		$report        = array();

		foreach ( $tables as $table ) {
			$primary_key = self::get_primary_key( $table );
			if ( ! $primary_key ) {
				continue;
			}
			$columns = $wpdb->get_results( "SHOW COLUMNS FROM `{$table}`", ARRAY_A );
			$text_columns = array_filter( $columns, function ( $c ) {
				return (bool) preg_match( '/char|text|blob/i', $c['Type'] );
			} );
			if ( empty( $text_columns ) ) {
				continue;
			}

			foreach ( $text_columns as $col ) {
				$column = $col['Field'];
				if ( $column === $primary_key ) {
					continue;
				}
				$rows = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT `{$primary_key}` AS pk, `{$column}` AS val FROM `{$table}` WHERE `{$column}` LIKE %s",
						'%' . $wpdb->esc_like( $old ) . '%'
					),
					ARRAY_A
				);
				foreach ( $rows as $row ) {
					$new_val = self::recursive_unserialize_replace( $old, $new, $row['val'] );
					if ( $new_val === $row['val'] ) {
						continue;
					}
					$cells_changed++;
					$report[] = array( 'table' => $table, 'column' => $column, 'pk' => $row['pk'] );
					if ( ! $dry_run ) {
						$wpdb->update( $table, array( $column => $new_val ), array( $primary_key => $row['pk'] ) );
					}
				}
			}
		}

		return array(
			'method'        => 'php-fallback',
			'dry_run'       => $dry_run,
			'tables_scanned'=> count( $tables ),
			'cells_changed' => $cells_changed,
			'report'        => array_slice( $report, 0, 200 ),
		);
	}

	private static function get_primary_key( $table ) {
		global $wpdb;
		$row = $wpdb->get_row( "SHOW KEYS FROM `{$table}` WHERE Key_name = 'PRIMARY'", ARRAY_A );
		return $row ? $row['Column_name'] : null;
	}

	private static function recursive_unserialize_replace( $from, $to, $data ) {
		if ( is_string( $data ) && ( $unserialized = @unserialize( $data ) ) !== false ) {
			return serialize( self::recursive_unserialize_replace( $from, $to, $unserialized ) );
		}
		if ( is_array( $data ) ) {
			$out = array();
			foreach ( $data as $key => $value ) {
				$out[ $key ] = self::recursive_unserialize_replace( $from, $to, $value );
			}
			return $out;
		}
		if ( is_object( $data ) ) {
			foreach ( $data as $key => $value ) {
				$data->$key = self::recursive_unserialize_replace( $from, $to, $value );
			}
			return $data;
		}
		if ( is_string( $data ) ) {
			return str_replace( $from, $to, $data );
		}
		return $data;
	}
}
