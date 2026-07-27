( function () {
	const out = document.getElementById( 'pm-output' );

	async function call( path, body ) {
		out.textContent = 'Running…';
		try {
			const res = await fetch( PiMigrate.root + path, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-WP-Nonce': PiMigrate.nonce,
				},
				body: JSON.stringify( body ),
			} );
			const json = await res.json();
			out.textContent = JSON.stringify( json, null, 2 );
		} catch ( err ) {
			out.textContent = 'Error: ' + err.message;
		}
	}

	document.getElementById( 'pm-site-export' ).addEventListener( 'click', () => {
		call( '/site/export', { output_dir: document.getElementById( 'pm-site-output' ).value } );
	} );

	document.getElementById( 'pm-site-import' ).addEventListener( 'click', () => {
		if ( ! confirm( 'This overwrites the current database and uploads. Continue?' ) ) {
			return;
		}
		call( '/site/import', {
			source_dir: document.getElementById( 'pm-site-source' ).value,
			new_url: document.getElementById( 'pm-site-newurl' ).value || null,
		} );
	} );

	document.getElementById( 'pm-sr-run' ).addEventListener( 'click', () => {
		call( '/search-replace', {
			old: document.getElementById( 'pm-sr-old' ).value,
			new: document.getElementById( 'pm-sr-new' ).value,
			dry_run: document.getElementById( 'pm-sr-dry' ).checked,
		} );
	} );

	document.getElementById( 'pm-cat-run' ).addEventListener( 'click', () => {
		call( '/catalog/import', {
			path: document.getElementById( 'pm-cat-path' ).value,
			post_type: document.getElementById( 'pm-cat-posttype' ).value,
			publish: document.getElementById( 'pm-cat-publish' ).checked,
			dry_run: document.getElementById( 'pm-cat-dry' ).checked,
		} );
	} );

	document.getElementById( 'pm-files-run' ).addEventListener( 'click', () => {
		call( '/files/sync', {
			dest: document.getElementById( 'pm-files-dest' ).value,
			dry_run: document.getElementById( 'pm-files-dry' ).checked,
		} );
	} );
} )();
