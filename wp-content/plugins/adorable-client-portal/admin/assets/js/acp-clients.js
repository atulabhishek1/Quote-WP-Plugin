/**
 * Adorable Client Portal — Clients Module JS
 * Handles AJAX form saving, duplicate checking, soft-deletion modal,
 * bulk actions, single client view tabs switching, and inline notes.
 */
jQuery( function ( $ ) {
	'use strict';

	// Inject custom CSS styles for toasts, tabs, and transitions.
	const style = document.createElement( 'style' );
	style.textContent = `
		/* Toast System */
		.acp-toast-container {
			position: fixed;
			bottom: 24px;
			right: 24px;
			display: flex;
			flex-direction: column;
			gap: 12px;
			z-index: 999999;
			max-width: 380px;
			width: calc(100% - 48px);
		}
		.acp-toast {
			background: var(--acp-surface-2);
			border-left: 4px solid var(--acp-primary);
			box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
			padding: 16px;
			border-radius: var(--acp-r-md);
			display: flex;
			align-items: center;
			justify-content: space-between;
			gap: 16px;
			transform: translateY(20px);
			opacity: 0;
			transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.3s ease;
			color: var(--acp-text);
		}
		.acp-toast--show {
			transform: translateY(0);
			opacity: 1;
		}
		.acp-toast--success {
			border-left-color: var(--acp-success, #10b981);
		}
		.acp-toast--error {
			border-left-color: var(--acp-danger, #ef4444);
		}
		.acp-toast__msg {
			font-family: var(--acp-font);
			font-size: 13px;
			font-weight: 500;
			line-height: 1.4;
		}
		.acp-toast__close {
			background: transparent;
			border: none;
			color: var(--acp-text-muted);
			font-size: 18px;
			cursor: pointer;
			padding: 4px;
			line-height: 1;
		}
		.acp-toast__close:hover {
			color: var(--acp-text);
		}

		/* Tabs Styling */
		.acp-tabs-nav-container {
			border-bottom: 1px solid var(--acp-border);
			margin-bottom: var(--acp-sp-6);
		}
		.acp-tabs-nav {
			display: flex;
			gap: var(--acp-sp-4);
		}
		.acp-tabs-nav__btn {
			background: transparent;
			border: none;
			border-bottom: 2px solid transparent;
			padding: var(--acp-sp-3) var(--acp-sp-2);
			font-family: var(--acp-font);
			font-size: var(--acp-fs-sm);
			font-weight: var(--acp-fw-medium);
			color: var(--acp-text-secondary);
			cursor: pointer;
			transition: all var(--acp-t-fast) var(--acp-ease);
			display: flex;
			align-items: center;
			gap: 8px;
			position: relative;
		}
		.acp-tabs-nav__btn:hover {
			color: var(--acp-text);
		}
		.acp-tabs-nav__btn--active {
			color: var(--acp-primary);
			border-bottom-color: var(--acp-primary);
			font-weight: var(--acp-fw-semibold);
		}
		.acp-tabs-nav__badge {
			background: var(--acp-surface-3);
			color: var(--acp-text-secondary);
			font-size: 11px;
			font-weight: 600;
			padding: 1px 6px;
			border-radius: var(--acp-r-full);
		}
		.acp-tabs-nav__btn--active .acp-tabs-nav__badge {
			background: var(--acp-primary-bg);
			color: var(--acp-primary);
		}
		.acp-tab-pane {
			display: none;
		}
		.acp-tab-pane--active {
			display: block;
			animation: acpFadeIn 0.25s ease-out;
		}

		/* Animations & Utilities */
		@keyframes acpFadeIn {
			from { opacity: 0; transform: translateY(4px); }
			to { opacity: 1; transform: translateY(0); }
		}
		.acp-margin-bottom-xs { margin-bottom: 4px; }
		.acp-margin-bottom-sm { margin-bottom: var(--acp-sp-2); }
		.acp-margin-bottom-md { margin-bottom: var(--acp-sp-4); }
		.acp-margin-top-sm { margin-top: var(--acp-sp-2); }
		.acp-margin-top-lg { margin-top: var(--acp-sp-6); }
		.acp-btn--full { width: 100%; justify-content: center; }

		/* Timeline notes history */
		.acp-notes-timeline {
			display: flex;
			flex-direction: column;
			gap: 16px;
			max-height: 480px;
			overflow-y: auto;
			padding-right: 8px;
		}
		.acp-timeline-item {
			background: var(--acp-surface-2);
			border: 1px solid var(--acp-border);
			padding: 14px;
			border-radius: var(--acp-r-lg);
			position: relative;
		}
		.acp-timeline-item__header {
			display: flex;
			justify-content: space-between;
			font-size: 11px;
			margin-bottom: 6px;
		}
		.acp-timeline-item__author {
			font-weight: 600;
			color: var(--acp-text-secondary);
		}
		.acp-timeline-item__date {
			color: var(--acp-text-muted);
		}
		.acp-timeline-item__body {
			font-size: 13px;
			line-height: 1.5;
			color: var(--acp-text);
		}

		/* Grid forms split */
		.acp-grid--form-split {
			display: grid;
			grid-template-columns: 1fr 1.5fr;
			gap: var(--acp-sp-6);
		}
		@media (max-width: 768px) {
			.acp-grid--form-split {
				grid-template-columns: 1fr;
			}
		}

		/* Metadata lists */
		.acp-meta-list {
			list-style: none;
			padding: 0;
			margin: 0;
			display: flex;
			flex-direction: column;
			gap: 12px;
		}
		.acp-meta-item {
			display: flex;
			flex-direction: column;
			gap: 2px;
		}
		.acp-meta-item__label {
			font-size: 11px;
			font-weight: 600;
			text-transform: uppercase;
			letter-spacing: 0.05em;
			color: var(--acp-text-muted);
		}
		.acp-meta-item__value {
			font-size: 13px;
			font-weight: 500;
			color: var(--acp-text-secondary);
		}
		.acp-address-block__title {
			font-size: 12px;
			font-weight: 600;
			color: var(--acp-text-secondary);
			margin: 0 0 4px 0;
		}
		.acp-address-block__text {
			font-size: 13px;
			color: var(--acp-text-secondary);
			margin: 0;
			line-height: 1.5;
		}
		.acp-tags-list {
			display: flex;
			flex-wrap: wrap;
			gap: 6px;
		}
		.acp-badge--tag {
			background: var(--acp-surface-3);
			color: var(--acp-text-secondary);
			font-size: 10px;
			font-weight: 500;
			padding: 2px 8px;
		}
		
		/* Activity logs */
		.acp-activity-timeline {
			display: flex;
			flex-direction: column;
			gap: 16px;
		}
		.acp-activity-item {
			border-left: 2px solid var(--acp-border);
			padding-left: 16px;
			position: relative;
		}
		.acp-activity-item::before {
			content: '';
			position: absolute;
			left: -6px;
			top: 4px;
			width: 10px;
			height: 10px;
			border-radius: 50%;
			background: var(--acp-border);
			border: 2px solid var(--acp-surface);
		}
		.acp-activity-item__header {
			display: flex;
			align-items: center;
			gap: 12px;
			margin-bottom: 4px;
		}
		.acp-activity-item__date {
			font-size: 11px;
			color: var(--acp-text-muted);
		}
		.acp-activity-item__desc {
			font-size: 13px;
			margin: 0 0 6px 0;
			color: var(--acp-text-secondary);
		}
		.acp-activity-item__meta {
			font-size: 11px;
			color: var(--acp-text-muted);
			display: flex;
			align-items: center;
			gap: 4px;
		}
	`;
	document.head.appendChild( style );

	// Helpers
	function toast( message, type = 'success' ) {
		const container = document.getElementById( 'acpToastContainer' );
		if ( ! container ) return;

		const item = document.createElement( 'div' );
		item.className = `acp-toast acp-toast--${type}`;
		item.innerHTML = `
			<span class="acp-toast__msg">${message}</span>
			<button class="acp-toast__close" aria-label="Close">&times;</button>
		`;

		container.appendChild( item );
		setTimeout( () => item.classList.add( 'acp-toast--show' ), 10 );

		const hide = () => {
			item.classList.remove( 'acp-toast--show' );
			setTimeout( () => item.remove(), 300 );
		};

		item.querySelector( '.acp-toast__close' ).addEventListener( 'click', hide );
		setTimeout( hide, 4000 );
	}

	/* ── 1. Client Type Toggle (Individual vs Company) ───────────────────────── */
	const $clientType = $( '#client_type' );
	if ( $clientType.length ) {
		$clientType.on( 'change', function () {
			const type = $( this ).val();
			if ( 'company' === type ) {
				$( '.acp-form-company-field' ).slideDown( 250 );
				$( '#company_name' ).prop( 'required', true );
			} else {
				$( '.acp-form-company-field' ).slideUp( 250 );
				$( '#company_name' ).prop( 'required', false ).val( '' );
				$( '#primary_contact' ).val( '' );
				$( '#secondary_contact' ).val( '' );
				$( '#gst_number' ).val( '' );
			}
		} );
	}

	/* ── 2. Duplicate Checks (Blur Event) ────────────────────────────────────── */
	const $duplicateInputs = $( '.acp-duplicate-check' );
	if ( $duplicateInputs.length ) {
		$duplicateInputs.on( 'blur', function () {
			const $input = $( this );
			const val = $input.val().trim();
			const field = $input.data( 'field' );
			const excludeId = $input.data( 'exclude' ) || 0;
			const $err = $( `#err_${field}` );

			if ( '' === val ) {
				$err.text( '' );
				$input.removeClass( 'acp-form-input--error' );
				return;
			}

			$.post( acpClients.ajaxUrl, {
				action: 'acp_check_duplicate',
				nonce: acpClients.nonce,
				field: field,
				value: val,
				exclude_id: excludeId
			}, function ( res ) {
				if ( res.success && res.data.exists ) {
					$input.addClass( 'acp-form-input--error' );
					if ( 'mobile' === field ) {
						$err.text( 'This mobile number is already registered.' );
					} else if ( 'email' === field ) {
						$err.text( 'This email address is already registered.' );
					} else if ( 'gst_number' === field ) {
						$err.text( 'This GST number is already registered.' );
					}
				} else {
					$input.removeClass( 'acp-form-input--error' );
					$err.text( '' );
				}
			} );
		} );
	}

	/* ── 3. Client AJAX Form Submit ──────────────────────────────────────────── */
	const $form = $( '#acpClientForm' );
	if ( $form.length ) {
		$form.on( 'submit', function ( e ) {
			e.preventDefault();

			// Front-end Validation Check.
			let hasErrors = false;
			$form.find( 'input[required], select[required]' ).each( function () {
				const $el = $( this );
				if ( '' === $el.val().trim() ) {
					$el.addClass( 'acp-form-input--error' );
					hasErrors = true;
				} else {
					$el.removeClass( 'acp-form-input--error' );
				}
			} );

			if ( $form.find( '.acp-form-input--error' ).length > 0 || hasErrors ) {
				toast( 'Please resolve validation errors before saving.', 'error' );
				return;
			}

			const $submitBtn = $( '#acpSaveClient' );
			const originalText = $submitBtn.html();
			const isEdit = $form.find( 'input[name="client_id"]' ).val() > 0;

			$.ajax( {
				url: acpClients.ajaxUrl,
				type: 'POST',
				data: $form.serialize(),
				beforeSend: function () {
					$submitBtn.prop( 'disabled', true ).text( acpClients.i18n.saving );
				},
				success: function ( res ) {
					if ( res.success ) {
						toast( res.data.message || acpClients.i18n.success, 'success' );
						if ( res.data.redirect ) {
							setTimeout( () => {
								window.location.href = res.data.redirect;
							}, 800 );
						}
					} else {
						toast( res.data.message || acpClients.i18n.error, 'error' );
						$submitBtn.prop( 'disabled', false ).html( originalText );
					}
				},
				error: function () {
					toast( acpClients.i18n.error, 'error' );
					$submitBtn.prop( 'disabled', false ).html( originalText );
				}
			} );
		} );
	}

	/* ── 4. Delete Client Handling ───────────────────────────────────────────── */
	let clientToDelete = null;
	const $deleteModal = $( '#acpDeleteModal' );

	$( 'body' ).on( 'click', '.acp-delete-client', function () {
		const $btn = $( this );
		clientToDelete = {
			id: $btn.data( 'id' ),
			name: $btn.data( 'name' ),
			nonce: $btn.data( 'nonce' )
		};

		$( '#acpDeleteModalTitle' ).text( `Delete client: ${clientToDelete.name}` );
		$deleteModal.removeAttr( 'hidden' );
	} );

	$( '#acpDeleteCancel' ).on( 'click', function () {
		$deleteModal.attr( 'hidden', '' );
		clientToDelete = null;
	} );

	$( '#acpDeleteConfirm' ).on( 'click', function () {
		if ( ! clientToDelete ) return;

		const $btn = $( this );
		const originalText = $btn.text();

		$.ajax( {
			url: acpClients.ajaxUrl,
			type: 'POST',
			data: {
				action: 'acp_delete_client',
				nonce: clientToDelete.nonce,
				client_id: clientToDelete.id
			},
			beforeSend: function () {
				$btn.prop( 'disabled', true ).text( acpClients.i18n.deleting );
			},
			success: function ( res ) {
				$btn.prop( 'disabled', false ).text( originalText );
				$deleteModal.attr( 'hidden', '' );

				if ( res.success ) {
					toast( res.data.message || 'Client deleted successfully.', 'success' );
					// Remove the row from table
					$( `tr[data-id="${clientToDelete.id}"]` ).fadeOut( 300, function () {
						$( this ).remove();
						if ( $( '#acpClientsTable tbody tr' ).length === 0 ) {
							window.location.reload();
						}
					} );
				} else {
					toast( res.data.message || acpClients.i18n.error, 'error' );
				}
				clientToDelete = null;
			},
			error: function () {
				$btn.prop( 'disabled', false ).text( originalText );
				$deleteModal.attr( 'hidden', '' );
				toast( acpClients.i18n.error, 'error' );
				clientToDelete = null;
			}
		} );
	} );

	/* ── 5. Bulk Actions Logic ────────────────────────────────────────────────── */
	const $selectAll = $( '#acpSelectAll' );
	const $rowChecks = $( '.acp-row-check' );
	const $bulkBar = $( '#acpBulkBar' );
	const $bulkCount = $( '#acpBulkCount' );

	function updateBulkBar() {
		const selectedCount = $( '.acp-row-check:checked' ).length;
		if ( selectedCount > 0 ) {
			$bulkCount.text( `${selectedCount} selected` );
			$bulkBar.removeAttr( 'hidden' );
		} else {
			$bulkBar.attr( 'hidden', '' );
		}
	}

	if ( $selectAll.length ) {
		$selectAll.on( 'change', function () {
			$rowChecks.prop( 'checked', this.checked );
			updateBulkBar();
		} );

		$rowChecks.on( 'change', function () {
			$selectAll.prop( 'checked', $rowChecks.length === $( '.acp-row-check:checked' ).length );
			updateBulkBar();
		} );

		$( '#acpBulkClear' ).on( 'click', function () {
			$selectAll.prop( 'checked', false );
			$rowChecks.prop( 'checked', false );
			updateBulkBar();
		} );

		$( '#acpBulkApply' ).on( 'click', function () {
			const action = $( '#acpBulkAction' ).val();
			if ( ! action ) {
				toast( 'Please select a bulk action.', 'error' );
				return;
			}

			const ids = [];
			$( '.acp-row-check:checked' ).each( function () {
				ids.push( $( this ).val() );
			} );

			if ( ids.length === 0 ) return;

			const $btn = $( this );
			const originalText = $btn.text();

			$.ajax( {
				url: acpClients.ajaxUrl,
				type: 'POST',
				data: {
					action: 'acp_bulk_clients',
					nonce: acpClients.nonce,
					bulk_action: action,
					ids: ids
				},
				beforeSend: function () {
					$btn.prop( 'disabled', true ).text( 'Applying...' );
				},
				success: function ( res ) {
					$btn.prop( 'disabled', false ).text( originalText );
					if ( res.success ) {
						toast( res.data.message, 'success' );
						setTimeout( () => window.location.reload(), 1000 );
					} else {
						toast( res.data.message || acpClients.i18n.error, 'error' );
					}
				},
				error: function () {
					$btn.prop( 'disabled', false ).text( originalText );
					toast( acpClients.i18n.error, 'error' );
				}
			} );
		} );
	}

	/* ── 6. Single Client View — Tab Switching ────────────────────────────────── */
	const $tabsNav = $( '.acp-tabs-nav__btn' );
	if ( $tabsNav.length ) {
		$tabsNav.on( 'click', function () {
			const $btn = $( this );
			const tabId = $btn.data( 'tab' );

			$tabsNav.removeClass( 'acp-tabs-nav__btn--active' );
			$btn.addClass( 'acp-tabs-nav__btn--active' );

			$( '.acp-tab-pane' ).removeClass( 'acp-tab-pane--active' );
			$( `#tab_${tabId}` ).addClass( 'acp-tab-pane--active' );
		} );
	}

	/* ── 7. Single Client View — Add Note via AJAX ────────────────────────────── */
	const $noteForm = $( '#acpAddNoteForm' );
	if ( $noteForm.length ) {
		$noteForm.on( 'submit', function ( e ) {
			e.preventDefault();

			const $textarea = $( '#acpNoteText' );
			const noteText = $textarea.val().trim();

			if ( '' === noteText ) {
				$textarea.addClass( 'acp-form-input--error' );
				return;
			}
			$textarea.removeClass( 'acp-form-input--error' );

			const $btn = $( '#acpSubmitNote' );
			const originalText = $btn.text();

			$.ajax( {
				url: acpClients.ajaxUrl,
				type: 'POST',
				data: $noteForm.serialize(),
				beforeSend: function () {
					$btn.prop( 'disabled', true ).text( 'Adding...' );
				},
				success: function ( res ) {
					$btn.prop( 'disabled', false ).text( originalText );
					if ( res.success ) {
						toast( res.data.message || 'Note added.', 'success' );
						$textarea.val( '' );

						// Prepend note to the history timeline.
						const $timeline = $( '#acpNotesTimeline' );
						$( '#acpNoNotesMsg' ).remove();

						const noteHtml = `
							<div class="acp-timeline-item" data-id="${res.data.note_id}" style="display:none">
								<div class="acp-timeline-item__header">
									<span class="acp-timeline-item__author">${res.data.author}</span>
									<span class="acp-timeline-item__date">${res.data.date}</span>
								</div>
								<div class="acp-timeline-item__body">
									${res.data.note.replace(/\n/g, '<br>')}
								</div>
							</div>
						`;
						$timeline.prepend( noteHtml );
						$timeline.find( `div[data-id="${res.data.note_id}"]` ).slideDown( 300 );

						// Update badge counter.
						const $badge = $( '#acpNotesCount' );
						if ( $badge.length ) {
							const currentVal = parseInt( $badge.text() ) || 0;
							$badge.text( currentVal + 1 );
						}
					} else {
						toast( res.data.message || acpClients.i18n.error, 'error' );
					}
				},
				error: function () {
					$btn.prop( 'disabled', false ).text( originalText );
					toast( acpClients.i18n.error, 'error' );
				}
			} );
		} );
	}
} );
