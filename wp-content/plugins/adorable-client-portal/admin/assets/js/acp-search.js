/**
 * Adorable Client Portal — Search Module
 */
( function () {
	'use strict';

	const input = document.getElementById( 'acpSearchInput' );
	if ( ! input ) return;

	// Keyboard shortcut: Ctrl+K / Cmd+K.
	document.addEventListener( 'keydown', function ( e ) {
		if ( ( e.ctrlKey || e.metaKey ) && e.key === 'k' ) {
			e.preventDefault();
			input.focus();
			input.select();
		}
	} );

	// Architecture: AJAX search will be wired here.
	// input.addEventListener( 'input', debounce( doSearch, 300 ) );

} )();
