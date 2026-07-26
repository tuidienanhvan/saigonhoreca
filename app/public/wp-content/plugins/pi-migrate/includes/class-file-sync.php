<?php
/**
 * Syncs wp-content/uploads (or any given directory) between two local paths,
 * or packages it into a zip for transfer.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Pi_Migrate_File_Sync {

	public static function zip_uploads( $dest_zip = null ) {
		$upload_dir = wp_upload_dir();
		$source     = $upload_dir['basedir'];
		$dest_zip   = $dest_zip ?: trailingslashit( sys_get_temp_dir() ) . 'pi-migrate-uploads-' . gmdate( 'Ymd-His' ) . '.zip';

		return self::zip_directory( $source, $dest_zip );
	}

	public static function zip_directory( $source_dir, $dest_zip ) {
		if ( ! class_exists( 'ZipArchive' ) ) {
			return new WP_Error( 'pi_migrate_no_zip', 'ZipArchive extension not available.' );
		}
		if ( ! is_dir( $source_dir ) ) {
			return new WP_Error( 'pi_migrate_no_source', "Source directory not found: {$source_dir}" );
		}

		$zip = new ZipArchive();
		if ( true !== $zip->open( $dest_zip, ZipArchive::CREATE | ZipArchive::OVERWRITE ) ) {
			return new WP_Error( 'pi_migrate_zip_open_failed', "Cannot create zip: {$dest_zip}" );
		}

		$source_dir = rtrim( $source_dir, '/\\' );
		$iterator   = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator( $source_dir, RecursiveDirectoryIterator::SKIP_DOTS ),
			RecursiveIteratorIterator::LEAVES_ONLY
		);

		$file_count = 0;
		foreach ( $iterator as $file ) {
			if ( $file->isDir() ) {
				continue;
			}
			$local_path = substr( $file->getPathname(), strlen( $source_dir ) + 1 );
			$zip->addFile( $file->getPathname(), str_replace( '\\', '/', $local_path ) );
			$file_count++;
		}
		$zip->close();

		return array(
			'zip'        => $dest_zip,
			'bytes'      => file_exists( $dest_zip ) ? filesize( $dest_zip ) : 0,
			'file_count' => $file_count,
		);
	}

	/**
	 * Extract a previously exported uploads zip into wp-content/uploads (or a given dir).
	 */
	public static function unzip_uploads( $zip_path, $dest_dir = null ) {
		if ( ! class_exists( 'ZipArchive' ) ) {
			return new WP_Error( 'pi_migrate_no_zip', 'ZipArchive extension not available.' );
		}
		if ( ! file_exists( $zip_path ) ) {
			return new WP_Error( 'pi_migrate_missing_zip', "Zip not found: {$zip_path}" );
		}
		$upload_dir = wp_upload_dir();
		$dest_dir   = $dest_dir ?: $upload_dir['basedir'];

		$zip = new ZipArchive();
		if ( true !== $zip->open( $zip_path ) ) {
			return new WP_Error( 'pi_migrate_zip_open_failed', "Cannot open zip: {$zip_path}" );
		}
		wp_mkdir_p( $dest_dir );
		$zip->extractTo( $dest_dir );
		$count = $zip->numFiles;
		$zip->close();

		return array( 'dest' => $dest_dir, 'file_count' => $count );
	}

	/**
	 * Directory-to-directory sync using PHP copy (Windows-safe, no rsync dependency).
	 * Only overwrites files whose size or mtime differ — cheap incremental sync
	 * for two Local-by-Flywheel sites on the same machine.
	 */
	public static function sync_directory( $source_dir, $dest_dir, $dry_run = false ) {
		if ( ! is_dir( $source_dir ) ) {
			return new WP_Error( 'pi_migrate_no_source', "Source directory not found: {$source_dir}" );
		}
		wp_mkdir_p( $dest_dir );

		$source_dir = rtrim( $source_dir, '/\\' );
		$dest_dir   = rtrim( $dest_dir, '/\\' );
		$iterator   = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator( $source_dir, RecursiveDirectoryIterator::SKIP_DOTS ),
			RecursiveIteratorIterator::LEAVES_ONLY
		);

		$copied = 0;
		$skipped = 0;
		foreach ( $iterator as $file ) {
			if ( $file->isDir() ) {
				continue;
			}
			$relative = substr( $file->getPathname(), strlen( $source_dir ) + 1 );
			$target   = $dest_dir . DIRECTORY_SEPARATOR . $relative;

			$needs_copy = ! file_exists( $target )
				|| filesize( $target ) !== $file->getSize()
				|| filemtime( $target ) < $file->getMTime();

			if ( ! $needs_copy ) {
				$skipped++;
				continue;
			}
			if ( ! $dry_run ) {
				wp_mkdir_p( dirname( $target ) );
				copy( $file->getPathname(), $target );
			}
			$copied++;
		}

		return array( 'copied' => $copied, 'skipped' => $skipped, 'dry_run' => $dry_run );
	}
}
