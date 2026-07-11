/**
 * Adorable Client Portal — Notifications Module
 */
( function () {
	'use strict';

	const wrapper = document.getElementById( 'acpNotifications' );
	if ( ! wrapper ) return;

	const trigger = wrapper.querySelector( '.acp-notif__trigger' );
	const panel   = wrapper.querySelector( '.acp-notif__panel' );
	const markAll = wrapper.querySelector( '.acp-notif__mark-all' );

	if ( ! trigger || ! panel ) return;

	function open() {
		wrapper.classList.add( 'acp-notif--open' );
		trigger.setAttribute( 'aria-expanded', 'true' );
		panel.removeAttribute( 'hidden' );
	}

	function close() {
		wrapper.classList.remove( 'acp-notif--open' );
		trigger.setAttribute( 'aria-expanded', 'false' );
		panel.setAttribute( 'hidden', '' );
	}

	trigger.addEventListener( 'click', function ( e ) {
		e.stopPropagation();
		wrapper.classList.contains( 'acp-notif--open' ) ? close() : open();
	} );

	document.addEventListener( 'click', function ( e ) {
		if ( ! wrapper.contains( e.target ) ) close();
	} );

	document.addEventListener( 'keydown', function ( e ) {
		if ( e.key === 'Escape' ) close();
	} );

	if ( markAll ) {
		markAll.addEventListener( 'click', function () {
			// Architecture prepared for AJAX mark-all-read.
			const badge = document.getElementById( 'acpNotifBadge' );
			if ( badge ) badge.setAttribute( 'hidden', '' );
		} );
	}

} )();
