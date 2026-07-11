/**
 * Adorable Client Portal — Sidebar Module
 */
( function () {
	'use strict';

	const STORAGE_KEY = 'acp_sidebar_collapsed';

	const sidebar  = document.getElementById( 'acpSidebar' );
	const toggle   = document.getElementById( 'acpSidebarToggle' );
	const main     = document.getElementById( 'acpMain' );
	const topbar   = document.getElementById( 'acpTopbar' );

	if ( ! sidebar || ! toggle ) return;

	function collapse( save ) {
		sidebar.classList.add( 'acp-sidebar--collapsed' );
		if ( main )   main.classList.add( 'acp-main--expanded' );
		if ( topbar ) topbar.classList.add( 'acp-topbar--expanded' );
		toggle.setAttribute( 'aria-expanded', 'false' );
		if ( save ) localStorage.setItem( STORAGE_KEY, '1' );
	}

	function expand( save ) {
		sidebar.classList.remove( 'acp-sidebar--collapsed' );
		if ( main )   main.classList.remove( 'acp-main--expanded' );
		if ( topbar ) topbar.classList.remove( 'acp-topbar--expanded' );
		toggle.setAttribute( 'aria-expanded', 'true' );
		if ( save ) localStorage.removeItem( STORAGE_KEY );
	}

	function isMobile() {
		return window.innerWidth <= 768;
	}

	function openMobile() {
		sidebar.classList.add( 'acp-sidebar--mobile-open' );
		let overlay = document.getElementById( 'acpSidebarOverlay' );
		if ( ! overlay ) {
			overlay = document.createElement( 'div' );
			overlay.id = 'acpSidebarOverlay';
			overlay.className = 'acp-sidebar-overlay';
			document.body.appendChild( overlay );
			overlay.addEventListener( 'click', closeMobile );
		}
	}

	function closeMobile() {
		sidebar.classList.remove( 'acp-sidebar--mobile-open' );
		const overlay = document.getElementById( 'acpSidebarOverlay' );
		if ( overlay ) overlay.remove();
	}

	// Restore saved state on desktop.
	if ( ! isMobile() && localStorage.getItem( STORAGE_KEY ) === '1' ) {
		collapse( false );
	}

	toggle.addEventListener( 'click', function () {
		if ( isMobile() ) {
			if ( sidebar.classList.contains( 'acp-sidebar--mobile-open' ) ) {
				closeMobile();
			} else {
				openMobile();
			}
			return;
		}

		if ( sidebar.classList.contains( 'acp-sidebar--collapsed' ) ) {
			expand( true );
		} else {
			collapse( true );
		}
	} );

	// Close mobile sidebar on resize to desktop.
	window.addEventListener( 'resize', function () {
		if ( ! isMobile() ) {
			closeMobile();
		}
	} );

} )();
