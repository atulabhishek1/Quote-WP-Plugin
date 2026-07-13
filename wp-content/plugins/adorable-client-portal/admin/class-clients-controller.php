<?php
/**
 * Clients module controller.
 *
 * Handles page routing (list / add / edit / view) and every AJAX action
 * for the Clients module. No SQL here — delegates to Clients_Repository.
 *
 * @package AdorableClientPortal\Admin
 */

declare( strict_types=1 );

namespace AdorableClientPortal\Admin;

use AdorableClientPortal\Includes\Constants;
use AdorableClientPortal\Includes\Services\Client_Service;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Clients_Controller
 */
final class Clients_Controller {

	private Clients_Repository $repo;
	private Client_Service $service;

	public function __construct() {
		$this->repo    = new Clients_Repository();
		$this->service = new Client_Service();
	}

	// -------------------------------------------------------------------------
	// Page render — called by WordPress menu callback
	// -------------------------------------------------------------------------

	/**
	 * Main entry point. Routes to the correct sub-view based on ?action=.
	 */
	public function render(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'adorable-client-portal' ) );
		}

		$action = isset( $_GET['action'] ) ? sanitize_key( wp_unslash( $_GET['action'] ) ) : 'list'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		match ( $action ) {
			'new'    => $this->render_form( 0 ),
			'edit'   => $this->render_form( (int) ( $_GET['id'] ?? 0 ) ), // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			'view'   => $this->render_view( (int) ( $_GET['id'] ?? 0 ) ), // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			default  => $this->render_list(),
		};
	}

	// -------------------------------------------------------------------------
	// Sub-views
	// -------------------------------------------------------------------------

	private function render_list(): void {
		$per_page = 20;
		$paged    = max( 1, (int) ( $_GET['paged'] ?? 1 ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		$args = [
			'search'    => sanitize_text_field( wp_unslash( $_GET['s'] ?? '' ) ), // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			'status'    => sanitize_key( wp_unslash( $_GET['status'] ?? '' ) ), // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			'city'      => sanitize_text_field( wp_unslash( $_GET['city'] ?? '' ) ), // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			'state'     => sanitize_text_field( wp_unslash( $_GET['state'] ?? '' ) ), // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			'orderby'   => sanitize_key( wp_unslash( $_GET['orderby'] ?? 'created_at' ) ), // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			'order'     => sanitize_key( wp_unslash( $_GET['order'] ?? 'DESC' ) ), // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			'per_page'  => $per_page,
			'paged'     => $paged,
		];

		$clients     = $this->service->listClients( $args );
		$total       = $this->service->countClients( $args );
		$total_pages = (int) ceil( $total / $per_page );
		$cities      = $this->service->getDistinctCities();
		$states      = $this->service->getDistinctStates();
		$statuses    = Constants::CLIENT_STATUSES;
		$nonce       = wp_create_nonce( Constants::NONCE_AJAX );
		$current_user = wp_get_current_user();

		include ACP_PATH . 'admin/views/clients/list.php';
	}

	private function render_form( int $client_id ): void {
		$client   = $client_id > 0 ? $this->service->getClient( $client_id ) : null;
		$statuses = Constants::CLIENT_STATUSES;
		$sources  = Constants::LEAD_SOURCES;
		$nonce    = wp_create_nonce( Constants::NONCE_CLIENT_SAVE );
		$users    = get_users( [ 'fields' => [ 'ID', 'display_name' ] ] );
		$current_user = wp_get_current_user();

		include ACP_PATH . 'admin/views/clients/form.php';
	}

	private function render_view( int $client_id ): void {
		$client = $this->service->getClient( $client_id );

		if ( ! $client ) {
			wp_die( esc_html__( 'Client not found.', 'adorable-client-portal' ) );
		}

		$notes    = $this->repo->get_notes( $client_id );
		$docs     = $this->repo->get_documents( $client_id );
		$activity = $this->repo->get_activity_log( $client_id, 30 );
		$statuses = Constants::CLIENT_STATUSES;
		$nonce    = wp_create_nonce( Constants::NONCE_AJAX );
		$current_user = wp_get_current_user();

		include ACP_PATH . 'admin/views/clients/view.php';
	}

	// -------------------------------------------------------------------------
	// AJAX — Save (add / edit)
	// -------------------------------------------------------------------------

	public function ajax_save(): void {
		check_ajax_referer( Constants::NONCE_CLIENT_SAVE, 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => __( 'Permission denied.', 'adorable-client-portal' ) ], 403 );
		}

		$client_id = (int) ( $_POST['client_id'] ?? 0 );
		$data      = $this->sanitize_form_data( $_POST );
		$errors    = $this->validate( $data, $client_id );

		if ( ! empty( $errors ) ) {
			wp_send_json_error( [ 'message' => implode( ' ', $errors ) ] );
		}

		$user_id = get_current_user_id();

		if ( $client_id > 0 ) {
			$data['updated_by'] = $user_id;
			$ok = $this->repo->update( $client_id, $data );

			if ( $ok ) {
				$this->repo->log_activity( [
					'object_type' => 'client',
					'object_id'   => $client_id,
					'action'      => 'updated',
					'description' => sprintf( 'Client "%s" was updated.', $data['client_name'] ),
					'user_id'     => $user_id,
					'ip_address'  => $this->get_ip(),
				] );
				wp_send_json_success( [
					'message'   => __( 'Client updated successfully.', 'adorable-client-portal' ),
					'client_id' => $client_id,
					'redirect'  => admin_url( 'admin.php?page=adorable-clients&action=view&id=' . $client_id ),
				] );
			}

			wp_send_json_error( [ 'message' => __( 'Failed to update client.', 'adorable-client-portal' ) ] );
		}

		// New client — generate code.
		$data['client_code'] = $this->generate_client_code();
		$data['created_by']  = $user_id;
		$data['updated_by']  = $user_id;

		$new_id = $this->repo->insert( $data );

		if ( $new_id ) {
			$this->repo->log_activity( [
				'object_type' => 'client',
				'object_id'   => $new_id,
				'action'      => 'created',
				'description' => sprintf( 'Client "%s" was created.', $data['client_name'] ),
				'user_id'     => $user_id,
				'ip_address'  => $this->get_ip(),
			] );
			wp_send_json_success( [
				'message'   => __( 'Client created successfully.', 'adorable-client-portal' ),
				'client_id' => $new_id,
				'redirect'  => admin_url( 'admin.php?page=adorable-clients&action=view&id=' . $new_id ),
			] );
		}

		wp_send_json_error( [ 'message' => __( 'Failed to create client.', 'adorable-client-portal' ) ] );
	}

	// -------------------------------------------------------------------------
	// AJAX — Delete
	// -------------------------------------------------------------------------

	public function ajax_delete(): void {
		check_ajax_referer( Constants::NONCE_AJAX, 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => __( 'Permission denied.', 'adorable-client-portal' ) ], 403 );
		}

		$client_id = (int) ( $_POST['client_id'] ?? 0 );

		if ( $client_id < 1 ) {
			wp_send_json_error( [ 'message' => __( 'Invalid client ID.', 'adorable-client-portal' ) ] );
		}

		$ok = $this->repo->soft_delete( $client_id );

		if ( $ok ) {
			$this->repo->log_activity( [
				'object_type' => 'client',
				'object_id'   => $client_id,
				'action'      => 'deleted',
				'description' => 'Client was soft-deleted.',
				'user_id'     => get_current_user_id(),
				'ip_address'  => $this->get_ip(),
			] );
			wp_send_json_success( [ 'message' => __( 'Client deleted.', 'adorable-client-portal' ) ] );
		}

		wp_send_json_error( [ 'message' => __( 'Failed to delete client.', 'adorable-client-portal' ) ] );
	}

	// -------------------------------------------------------------------------
	// AJAX — Status change
	// -------------------------------------------------------------------------

	public function ajax_status_change(): void {
		check_ajax_referer( Constants::NONCE_AJAX, 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => __( 'Permission denied.', 'adorable-client-portal' ) ], 403 );
		}

		$client_id = (int) ( $_POST['client_id'] ?? 0 );
		$status    = sanitize_key( wp_unslash( $_POST['status'] ?? '' ) );

		if ( $client_id < 1 || ! array_key_exists( $status, Constants::CLIENT_STATUSES ) ) {
			wp_send_json_error( [ 'message' => __( 'Invalid data.', 'adorable-client-portal' ) ] );
		}

		$ok = $this->repo->update( $client_id, [ 'status' => $status ] );

		if ( $ok ) {
			wp_send_json_success( [
				'message'      => __( 'Status updated.', 'adorable-client-portal' ),
				'status'       => $status,
				'status_label' => Constants::CLIENT_STATUSES[ $status ],
			] );
		}

		wp_send_json_error( [ 'message' => __( 'Failed to update status.', 'adorable-client-portal' ) ] );
	}

	// -------------------------------------------------------------------------
	// AJAX — Bulk actions
	// -------------------------------------------------------------------------

	public function ajax_bulk(): void {
		check_ajax_referer( Constants::NONCE_AJAX, 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => __( 'Permission denied.', 'adorable-client-portal' ) ], 403 );
		}

		$action = sanitize_key( wp_unslash( $_POST['bulk_action'] ?? '' ) );
		$ids    = array_map( 'intval', (array) ( $_POST['ids'] ?? [] ) );
		$ids    = array_filter( $ids, static fn( int $id ) => $id > 0 );

		if ( empty( $ids ) ) {
			wp_send_json_error( [ 'message' => __( 'No clients selected.', 'adorable-client-portal' ) ] );
		}

		if ( 'delete' === $action ) {
			$ok = $this->repo->bulk_delete( $ids );
			$ok
				? wp_send_json_success( [ 'message' => sprintf( _n( '%d client deleted.', '%d clients deleted.', count( $ids ), 'adorable-client-portal' ), count( $ids ) ) ] )
				: wp_send_json_error( [ 'message' => __( 'Bulk delete failed.', 'adorable-client-portal' ) ] );
			return;
		}

		if ( array_key_exists( $action, Constants::CLIENT_STATUSES ) ) {
			$ok = $this->repo->bulk_update_status( $ids, $action );
			$ok
				? wp_send_json_success( [ 'message' => sprintf( _n( '%d client updated.', '%d clients updated.', count( $ids ), 'adorable-client-portal' ), count( $ids ) ) ] )
				: wp_send_json_error( [ 'message' => __( 'Bulk status update failed.', 'adorable-client-portal' ) ] );
			return;
		}

		wp_send_json_error( [ 'message' => __( 'Unknown bulk action.', 'adorable-client-portal' ) ] );
	}

	// -------------------------------------------------------------------------
	// AJAX — Duplicate check
	// -------------------------------------------------------------------------

	public function ajax_check_duplicate(): void {
		check_ajax_referer( Constants::NONCE_AJAX, 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [], 403 );
		}

		$field      = sanitize_key( wp_unslash( $_POST['field'] ?? '' ) );
		$value      = sanitize_text_field( wp_unslash( $_POST['value'] ?? '' ) );
		$exclude_id = (int) ( $_POST['exclude_id'] ?? 0 );
		$exists     = false;

		if ( 'mobile' === $field ) {
			$exists = $this->repo->mobile_exists( $value, $exclude_id );
		} elseif ( 'email' === $field ) {
			$exists = $this->repo->email_exists( $value, $exclude_id );
		} elseif ( 'gst_number' === $field ) {
			$exists = $this->repo->gst_exists( $value, $exclude_id );
		}

		wp_send_json_success( [ 'exists' => $exists ] );
	}

	// -------------------------------------------------------------------------
	// AJAX — Add note
	// -------------------------------------------------------------------------

	public function ajax_add_note(): void {
		check_ajax_referer( Constants::NONCE_AJAX, 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => __( 'Permission denied.', 'adorable-client-portal' ) ], 403 );
		}

		$client_id = (int) ( $_POST['client_id'] ?? 0 );
		$note      = sanitize_textarea_field( wp_unslash( $_POST['note'] ?? '' ) );

		if ( $client_id < 1 || '' === trim( $note ) ) {
			wp_send_json_error( [ 'message' => __( 'Invalid data.', 'adorable-client-portal' ) ] );
		}

		$note_id = $this->repo->insert_note( $client_id, $note, get_current_user_id() );

		if ( $note_id ) {
			wp_send_json_success( [
				'message' => __( 'Note added.', 'adorable-client-portal' ),
				'note_id' => $note_id,
				'note'    => esc_html( $note ),
				'author'  => esc_html( wp_get_current_user()->display_name ),
				'date'    => esc_html( wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) ) ),
			] );
		}

		wp_send_json_error( [ 'message' => __( 'Failed to add note.', 'adorable-client-portal' ) ] );
	}

	// -------------------------------------------------------------------------
	// AJAX — Export CSV
	// -------------------------------------------------------------------------

	public function ajax_export_csv(): void {
		check_ajax_referer( Constants::NONCE_AJAX, 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Permission denied.', 'adorable-client-portal' ) );
		}

		$args = [
			'search' => sanitize_text_field( wp_unslash( $_GET['s'] ?? '' ) ), // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			'status' => sanitize_key( wp_unslash( $_GET['status'] ?? '' ) ), // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			'city'   => sanitize_text_field( wp_unslash( $_GET['city'] ?? '' ) ), // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			'state'  => sanitize_text_field( wp_unslash( $_GET['state'] ?? '' ) ), // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		];

		$clients = $this->repo->get_all_for_export( $args );

		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="clients-' . gmdate( 'Y-m-d' ) . '.csv"' );
		header( 'Pragma: no-cache' );
		header( 'Expires: 0' );

		$out = fopen( 'php://output', 'w' );

		// BOM for Excel UTF-8.
		fwrite( $out, "\xEF\xBB\xBF" ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite

		fputcsv( $out, [
			'ID', 'Client Name', 'Company', 'Email', 'Mobile', 'WhatsApp',
			'City', 'State', 'Country', 'Pincode', 'GST', 'PAN',
			'Lead Source', 'Status', 'Created At',
		] );

		foreach ( $clients as $c ) {
			fputcsv( $out, [
				$c->id,
				$c->name,
				$c->company_name,
				$c->email,
				$c->mobile,
				$c->whatsapp,
				$c->city,
				$c->state,
				$c->country,
				$c->pincode,
				$c->gst_number,
				$c->pan_number,
				$c->lead_source_label(),
				$c->status_label(),
				$c->created_at,
			] );
		}

		fclose( $out ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
		exit;
	}

	// -------------------------------------------------------------------------
	// Helpers
	// -------------------------------------------------------------------------

	/**
	 * Sanitize all POST fields from the client form.
	 *
	 * @param array<string,mixed> $post Raw $_POST.
	 * @return array<string,mixed>
	 */
	private function sanitize_form_data( array $post ): array {
		return [
			'client_type'       => sanitize_key( wp_unslash( $post['client_type'] ?? 'individual' ) ),
			'client_name'       => sanitize_text_field( wp_unslash( $post['client_name'] ?? '' ) ),
			'company_name'      => sanitize_text_field( wp_unslash( $post['company_name'] ?? '' ) ),
			'primary_contact'   => sanitize_text_field( wp_unslash( $post['primary_contact'] ?? '' ) ),
			'secondary_contact' => sanitize_text_field( wp_unslash( $post['secondary_contact'] ?? '' ) ),
			'email'             => sanitize_email( wp_unslash( $post['email'] ?? '' ) ),
			'alternate_email'   => sanitize_email( wp_unslash( $post['alternate_email'] ?? '' ) ),
			'mobile'            => sanitize_text_field( wp_unslash( $post['mobile'] ?? '' ) ),
			'alternate_mobile'  => sanitize_text_field( wp_unslash( $post['alternate_mobile'] ?? '' ) ),
			'whatsapp_number'   => sanitize_text_field( wp_unslash( $post['whatsapp'] ?? '' ) ),
			'gst_number'        => strtoupper( sanitize_text_field( wp_unslash( $post['gst_number'] ?? '' ) ) ),
			'pan_number'        => strtoupper( sanitize_text_field( wp_unslash( $post['pan_number'] ?? '' ) ) ),
			'billing_address'   => sanitize_textarea_field( wp_unslash( $post['billing_address'] ?? '' ) ),
			'site_address'      => sanitize_textarea_field( wp_unslash( $post['site_address'] ?? '' ) ),
			'city'              => sanitize_text_field( wp_unslash( $post['city'] ?? '' ) ),
			'state'             => sanitize_text_field( wp_unslash( $post['state'] ?? '' ) ),
			'country'           => sanitize_text_field( wp_unslash( $post['country'] ?? 'India' ) ),
			'pincode'           => sanitize_text_field( wp_unslash( $post['pincode'] ?? '' ) ),
			'lead_source'       => sanitize_key( wp_unslash( $post['lead_source'] ?? '' ) ),
			'assigned_sales'    => (int) ( $post['assigned_salesperson'] ?? 0 ),
			'assigned_designer' => (int) ( $post['assigned_designer'] ?? 0 ),
			'status'            => sanitize_key( wp_unslash( $post['status'] ?? 'lead' ) ),
			'tags'              => sanitize_text_field( wp_unslash( $post['tags'] ?? '' ) ),
			'notes'             => sanitize_textarea_field( wp_unslash( $post['notes'] ?? '' ) ),
		];
	}

	/**
	 * Validate sanitised form data. Returns array of error strings.
	 *
	 * @param array<string,mixed> $data      Sanitised data.
	 * @param int                 $client_id 0 for new, >0 for edit.
	 * @return string[]
	 */
	private function validate( array $data, int $client_id ): array {
		$errors = [];

		if ( '' === trim( $data['client_name'] ) ) {
			$errors[] = __( 'Client name is required.', 'adorable-client-portal' );
		}

		if ( '' === trim( $data['mobile'] ) ) {
			$errors[] = __( 'Mobile number is required.', 'adorable-client-portal' );
		} elseif ( ! preg_match( '/^[6-9]\d{9}$/', preg_replace( '/[\s\-\+]/', '', $data['mobile'] ) ) ) {
			$errors[] = __( 'Mobile number must be a valid 10-digit Indian number.', 'adorable-client-portal' );
		} elseif ( $this->repo->mobile_exists( $data['mobile'], $client_id ) ) {
			$errors[] = __( 'This mobile number is already registered.', 'adorable-client-portal' );
		}

		if ( '' !== $data['email'] ) {
			if ( ! is_email( $data['email'] ) ) {
				$errors[] = __( 'Email address format is invalid.', 'adorable-client-portal' );
			} elseif ( $this->repo->email_exists( $data['email'], $client_id ) ) {
				$errors[] = __( 'This email address is already registered.', 'adorable-client-portal' );
			}
		}

		if ( '' !== $data['gst_number'] ) {
			if ( ! preg_match( '/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/', $data['gst_number'] ) ) {
				$errors[] = __( 'GST number format is invalid.', 'adorable-client-portal' );
			} elseif ( $this->repo->gst_exists( $data['gst_number'], $client_id ) ) {
				$errors[] = __( 'This GST number is already registered.', 'adorable-client-portal' );
			}
		}

		if ( '' !== $data['pan_number'] && ! preg_match( '/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/', $data['pan_number'] ) ) {
			$errors[] = __( 'PAN number format is invalid.', 'adorable-client-portal' );
		}

		if ( ! array_key_exists( $data['status'], Constants::CLIENT_STATUSES ) ) {
			$errors[] = __( 'Invalid status selected.', 'adorable-client-portal' );
		}

		return $errors;
	}

	/**
	 * Generate the next sequential client code (e.g. AC000001).
	 *
	 * @return string
	 */
	private function generate_client_code(): string {
		global $wpdb;
		$table = $wpdb->prefix . Constants::TABLE_CLIENTS;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$last = (int) $wpdb->get_var( "SELECT MAX(id) FROM `{$table}`" );

		return 'AC' . str_pad( (string) ( $last + 1 ), 6, '0', STR_PAD_LEFT );
	}

	/**
	 * Return the visitor's IP address.
	 *
	 * @return string
	 */
	private function get_ip(): string {
		$ip = $_SERVER['REMOTE_ADDR'] ?? '';
		return sanitize_text_field( wp_unslash( $ip ) );
	}
}
