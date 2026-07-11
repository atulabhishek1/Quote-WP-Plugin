/**
 * Adorable Client Portal — Base Admin Module
 * Dark mode, user menu dropdown.
 */
( function () {
	'use strict';

	/* ── Dark Mode ─────────────────────────────────────────────────────────── */
	const DARK_KEY   = 'acp_dark_mode';
	const app        = document.getElementById( 'acpApp' );
	const darkToggle = document.getElementById( 'acpDarkToggle' );

	if ( app && darkToggle ) {
		if ( localStorage.getItem( DARK_KEY ) === 'dark' ) {
			app.setAttribute( 'data-theme', 'dark' );
			darkToggle.setAttribute( 'aria-pressed', 'true' );
		}

		darkToggle.addEventListener( 'click', function () {
			const isDark = app.getAttribute( 'data-theme' ) === 'dark';
			app.setAttribute( 'data-theme', isDark ? 'light' : 'dark' );
			darkToggle.setAttribute( 'aria-pressed', String( ! isDark ) );
			localStorage.setItem( DARK_KEY, isDark ? 'light' : 'dark' );
		} );
	}

	/* ── User Menu ─────────────────────────────────────────────────────────── */
	const userMenu    = document.getElementById( 'acpUserMenu' );
	const userTrigger = userMenu ? userMenu.querySelector( '.acp-user-menu__trigger' ) : null;

	if ( userMenu && userTrigger ) {
		userTrigger.addEventListener( 'click', function ( e ) {
			e.stopPropagation();
			userMenu.classList.toggle( 'acp-user-menu--open' );
			const expanded = userMenu.classList.contains( 'acp-user-menu--open' );
			userTrigger.setAttribute( 'aria-expanded', String( expanded ) );
		} );
	}

	/* ── Global close on outside click / Escape ────────────────────────────── */
	document.addEventListener( 'click', function () {
		if ( userMenu ) userMenu.classList.remove( 'acp-user-menu--open' );
	} );

	document.addEventListener( 'keydown', function ( e ) {
		if ( e.key === 'Escape' ) {
			if ( userMenu ) userMenu.classList.remove( 'acp-user-menu--open' );
		}
	} );

} )();
