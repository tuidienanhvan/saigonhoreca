<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Pi_Migrate_Admin {

	public static function register_menu() {
		add_management_page(
			'Pi Migrate',
			'Pi Migrate',
			'manage_options',
			'pi-migrate',
			array( __CLASS__, 'render_page' )
		);
	}

	public static function enqueue_assets( $hook ) {
		if ( 'tools_page_pi-migrate' !== $hook ) {
			return;
		}
		wp_enqueue_script(
			'pi-migrate-admin',
			PI_MIGRATE_URL . 'admin/js/admin.js',
			array(),
			PI_MIGRATE_VERSION,
			true
		);
		wp_localize_script( 'pi-migrate-admin', 'PiMigrate', array(
			'root'  => esc_url_raw( rest_url( 'pi-migrate/v1' ) ),
			'nonce' => wp_create_nonce( 'wp_rest' ),
		) );
	}

	public static function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap">
			<h1>Pi Migrate</h1>
			<p>Full-site migration and catalog import/export. Actions run on this site (<code><?php echo esc_html( site_url() ); ?></code>).</p>

			<h2>Site package</h2>
			<p>
				<label>Output dir <input type="text" id="pm-site-output" style="width:420px" placeholder="C:\path\to\export-folder"></label>
				<button class="button button-primary" id="pm-site-export">Export site</button>
			</p>
			<p>
				<label>Source dir <input type="text" id="pm-site-source" style="width:420px" placeholder="C:\path\to\exported-folder"></label>
				<label>New URL <input type="text" id="pm-site-newurl" style="width:260px" placeholder="https://new-site.com"></label>
				<button class="button" id="pm-site-import">Import site</button>
			</p>

			<h2>Search &amp; replace</h2>
			<p>
				<input type="text" id="pm-sr-old" placeholder="old URL" style="width:300px">
				<input type="text" id="pm-sr-new" placeholder="new URL" style="width:300px">
				<label><input type="checkbox" id="pm-sr-dry" checked> dry run</label>
				<button class="button" id="pm-sr-run">Run</button>
			</p>

			<h2>Catalog import</h2>
			<p>
				<label>Path <input type="text" id="pm-cat-path" style="width:420px" placeholder="docs/product-catalog or a JSON/CSV file"></label>
				<label>Post type <input type="text" id="pm-cat-posttype" style="width:120px" value="product"></label>
			</p>
			<p>
				<label><input type="checkbox" id="pm-cat-publish"> publish</label>
				<label><input type="checkbox" id="pm-cat-dry" checked> dry run</label>
				<button class="button button-primary" id="pm-cat-run">Import catalog</button>
			</p>

			<h2>Files sync</h2>
			<p>
				<label>Destination dir <input type="text" id="pm-files-dest" style="width:420px" placeholder="C:\...\uploads"></label>
				<label><input type="checkbox" id="pm-files-dry" checked> dry run</label>
				<button class="button" id="pm-files-run">Sync uploads</button>
			</p>

			<h2>Result</h2>
			<pre id="pm-output" style="background:#fff;border:1px solid #ccd0d4;padding:12px;max-height:400px;overflow:auto;">(no action run yet)</pre>
		</div>
		<?php
	}
}
