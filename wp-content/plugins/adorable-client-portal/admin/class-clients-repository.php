<?php
/**
 * Clients repository.
 *
 * All database access for the Clients module.
 * No SQL outside this class. Every query uses prepared statements.
 *
 * @package AdorableClientPortal\Admin
 */

declare( strict_types=1 );

namespace AdorableClientPortal\Admin;

use AdorableClientPortal\Includes\Client_Model;
use AdorableClientPortal\Includes\Constants;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Clients_Repository
 */
final class Clients_Repository {

	/** @var \wpdb */
	private \wpdb $db;

	/** @var string Clients table (with prefix). */
	private string $table;

	/** @var string Activity log table (with prefix). */
	private string $log_table;

	/** @var string Client notes table (with prefix). */
	private string $notes_table;

	/** @var string Client documents table (with prefix). */
	private string $docs_table;

	/** @var string Client contacts table (with prefix). */
	private string $contacts_table;

	/** @var string Client addresses table (with prefix). */
	private string $addresses_table;

	public function __construct() {
		global $wpdb;
		$this->db              = $wpdb;
		$this->table           = $wpdb->prefix . Constants::TABLE_CLIENTS;
		$this->log_table       = $wpdb->prefix . Constants::TABLE_ACTIVITY_LOGS;
		$this->notes_table     = $wpdb->prefix . Constants::TABLE_CLIENT_NOTES;
		$this->docs_table      = $wpdb->prefix . Constants::TABLE_CLIENT_DOCUMENTS;
		$this->contacts_table  = $wpdb->prefix . Constants::TABLE_CLIENT_CONTACTS;
		$this->addresses_table = $wpdb->prefix . Constants::TABLE_CLIENT_ADDRESSES;
	}

	// -------------------------------------------------------------------------
	// Read
	// -------------------------------------------------------------------------

	/**
	 * Return a paginated, filtered list of clients with computed columns.
	 *
	 * @param array<string,mixed> $args Query arguments.
	 * @return Client_Model[]
	 */
	public function get_clients( array $args = [] ): array {
		$defaults = [
			'search'      => '',
			'status'      => '',
			'salesperson' => 0,
			'designer'    => 0,
			'city'        => '',
			'state'       => '',
			'date_from'   => '',
			'date_to'     => '',
			'orderby'     => 'created_at',
			'order'       => 'DESC',
			'per_page'    => 20,
			'paged'       => 1,
			'include_deleted' => false,
		];

		$args     = wp_parse_args( $args, $defaults );
		$where    = [ '1=1' ];
		$params   = [];

		if ( ! $args['include_deleted'] ) {
			$where[] = '`c`.`is_deleted` = 0';
		}

		if ( ! empty( $args['search'] ) ) {
			$like      = '%' . $this->db->esc_like( $args['search'] ) . '%';
			$where[]   = '(`c`.`name` LIKE %s OR `c`.`mobile` LIKE %s OR `c`.`email` LIKE %s OR `c`.`company_name` LIKE %s OR `c`.`gst_number` LIKE %s OR `c`.`pan_number` LIKE %s)';
			$params[]  = $like;
			$params[]  = $like;
			$params[]  = $like;
			$params[]  = $like;
			$params[]  = $like;
			$params[]  = $like;
		}

		if ( ! empty( $args['status'] ) ) {
			$where[]  = '`c`.`status` = %s';
			$params[] = $args['status'];
		}

		if ( ! empty( $args['salesperson'] ) ) {
			$where[]  = '`c`.`assigned_salesperson` = %d';
			$params[] = (int) $args['salesperson'];
		}

		if ( ! empty( $args['designer'] ) ) {
			$where[]  = '`c`.`assigned_designer` = %d';
			$params[] = (int) $args['designer'];
		}

		if ( ! empty( $args['city'] ) ) {
			$where[]  = '`c`.`city` = %s';
			$params[] = $args['city'];
		}

		if ( ! empty( $args['state'] ) ) {
			$where[]  = '`c`.`state` = %s';
			$params[] = $args['state'];
		}

		if ( ! empty( $args['date_from'] ) ) {
			$where[]  = 'DATE(`c`.`created_at`) >= %s';
			$params[] = $args['date_from'];
		}

		if ( ! empty( $args['date_to'] ) ) {
			$where[]  = 'DATE(`c`.`created_at`) <= %s';
			$params[] = $args['date_to'];
		}

		$allowed_order  = [ 'name', 'company_name', 'mobile', 'email', 'status', 'created_at', 'updated_at', 'projects_count', 'total_revenue' ];
		$orderby        = in_array( $args['orderby'], $allowed_order, true ) ? $args['orderby'] : 'created_at';
		$order          = 'ASC' === strtoupper( $args['order'] ) ? 'ASC' : 'DESC';
		$per_page       = max( 1, (int) $args['per_page'] );
		$offset         = ( max( 1, (int) $args['paged'] ) - 1 ) * $per_page;

		$projects_table = $this->db->prefix . Constants::TABLE_PROJECTS;
		$payments_table = $this->db->prefix . Constants::TABLE_PAYMENTS;

		$where_sql = implode( ' AND ', $where );

		// Build the SQL. Computed columns use sub-queries so they work even when
		// the projects / payments tables do not yet exist.
		$sql = "
			SELECT
				`c`.*,
				COALESCE((
					SELECT COUNT(*) FROM `{$projects_table}` `p`
					WHERE `p`.`client_id` = `c`.`id`
				), 0) AS `projects_count`,
				COALESCE((
					SELECT SUM(`py`.`amount`) FROM `{$payments_table}` `py`
					INNER JOIN `{$projects_table}` `pj` ON `pj`.`id` = `py`.`project_id`
					WHERE `pj`.`client_id` = `c`.`id` AND `py`.`status` = 'paid'
				), 0) AS `total_revenue`,
				COALESCE((
					SELECT SUM(`py`.`amount`) FROM `{$payments_table}` `py`
					INNER JOIN `{$projects_table}` `pj` ON `pj`.`id` = `py`.`project_id`
					WHERE `pj`.`client_id` = `c`.`id` AND `py`.`status` = 'pending'
				), 0) AS `pending_amount`
			FROM `{$this->table}` `c`
			WHERE {$where_sql}
			ORDER BY `{$orderby}` {$order}
			LIMIT %d OFFSET %d
		";

		$params[] = $per_page;
		$params[] = $offset;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.NotPrepared
		$rows = $this->db->get_results( $this->db->prepare( $sql, ...$params ), ARRAY_A );

		if ( empty( $rows ) ) {
			return [];
		}

		return array_map( static fn( array $row ) => new Client_Model( $row ), $rows );
	}

	/**
	 * Return total count matching the same filters (for pagination).
	 *
	 * @param array<string,mixed> $args Same args as get_clients() minus pagination.
	 * @return int
	 */
	public function count_clients( array $args = [] ): int {
		$defaults = [
			'search'      => '',
			'status'      => '',
			'salesperson' => 0,
			'designer'    => 0,
			'city'        => '',
			'state'       => '',
			'date_from'   => '',
			'date_to'     => '',
			'include_deleted' => false,
		];

		$args   = wp_parse_args( $args, $defaults );
		$where  = [ '1=1' ];
		$params = [];

		if ( ! $args['include_deleted'] ) {
			$where[] = '`is_deleted` = 0';
		}

		if ( ! empty( $args['search'] ) ) {
			$like     = '%' . $this->db->esc_like( $args['search'] ) . '%';
			$where[]  = '(`name` LIKE %s OR `mobile` LIKE %s OR `email` LIKE %s OR `company_name` LIKE %s OR `gst_number` LIKE %s OR `pan_number` LIKE %s)';
			$params[] = $like;
			$params[] = $like;
			$params[] = $like;
			$params[] = $like;
			$params[] = $like;
			$params[] = $like;
		}

		if ( ! empty( $args['status'] ) ) {
			$where[]  = '`status` = %s';
			$params[] = $args['status'];
		}

		if ( ! empty( $args['salesperson'] ) ) {
			$where[]  = '`assigned_salesperson` = %d';
			$params[] = (int) $args['salesperson'];
		}

		if ( ! empty( $args['designer'] ) ) {
			$where[]  = '`assigned_designer` = %d';
			$params[] = (int) $args['designer'];
		}

		if ( ! empty( $args['city'] ) ) {
			$where[]  = '`city` = %s';
			$params[] = $args['city'];
		}

		if ( ! empty( $args['state'] ) ) {
			$where[]  = '`state` = %s';
			$params[] = $args['state'];
		}

		if ( ! empty( $args['date_from'] ) ) {
			$where[]  = 'DATE(`created_at`) >= %s';
			$params[] = $args['date_from'];
		}

		if ( ! empty( $args['date_to'] ) ) {
			$where[]  = 'DATE(`created_at`) <= %s';
			$params[] = $args['date_to'];
		}

		$where_sql = implode( ' AND ', $where );
		$sql       = "SELECT COUNT(*) FROM `{$this->table}` WHERE {$where_sql}";

		if ( ! empty( $params ) ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.NotPrepared
			return (int) $this->db->get_var( $this->db->prepare( $sql, ...$params ) );
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.NotPrepared
		return (int) $this->db->get_var( $sql );
	}

	/**
	 * Return a single client by ID.
	 *
	 * @param int  $id             Client ID.
	 * @param bool $include_deleted Include soft-deleted records.
	 * @return Client_Model|null
	 */
	public function get_client( int $id, bool $include_deleted = false ): ?Client_Model {
		$deleted_clause = $include_deleted ? '' : 'AND `is_deleted` = 0';

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$row = $this->db->get_row(
			$this->db->prepare(
				"SELECT * FROM `{$this->table}` WHERE `id` = %d {$deleted_clause} LIMIT 1",
				$id
			),
			ARRAY_A
		);

		return $row ? new Client_Model( $row ) : null;
	}

	/**
	 * Insert a new client row.
	 *
	 * @param array<string,mixed> $data Sanitised column => value pairs.
	 * @return int|false New client ID or false on failure.
	 */
	public function insert( array $data ): int|false {
		$data['created_at'] = current_time( 'mysql' );
		$data['updated_at'] = current_time( 'mysql' );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$result = $this->db->insert( $this->table, $data );

		return false !== $result ? (int) $this->db->insert_id : false;
	}

	/**
	 * Update an existing client row.
	 *
	 * @param int                 $id   Client ID.
	 * @param array<string,mixed> $data Sanitised column => value pairs.
	 * @return bool
	 */
	public function update( int $id, array $data ): bool {
		$data['updated_at'] = current_time( 'mysql' );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$result = $this->db->update( $this->table, $data, [ 'id' => $id ] );

		return false !== $result;
	}

	/**
	 * Soft-delete a client.
	 *
	 * @param int $id Client ID.
	 * @return bool
	 */
	public function soft_delete( int $id ): bool {
		return $this->update( $id, [ 'is_deleted' => 1, 'status' => 'archived' ] );
	}

	/**
	 * Restore a soft-deleted client.
	 *
	 * @param int $id Client ID.
	 * @return bool
	 */
	public function restore( int $id ): bool {
		return $this->update( $id, [ 'is_deleted' => 0 ] );
	}

	/**
	 * Permanently delete a client and all related sub-records.
	 *
	 * @param int $id Client ID.
	 * @return bool
	 */
	public function permanent_delete( int $id ): bool {
		// Delete sub-records first.
		foreach ( [ $this->notes_table, $this->docs_table, $this->contacts_table, $this->addresses_table ] as $sub ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$this->db->delete( $sub, [ 'client_id' => $id ] );
		}

		// Delete activity logs for this client.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$this->db->delete( $this->log_table, [ 'object_id' => $id, 'object_type' => 'client' ] );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$result = $this->db->delete( $this->table, [ 'id' => $id ] );

		return false !== $result;
	}

	/**
	 * Bulk update status for multiple clients.
	 *
	 * @param int[]  $ids    Client IDs.
	 * @param string $status New status.
	 * @return bool
	 */
	public function bulk_update_status( array $ids, string $status ): bool {
		if ( empty( $ids ) ) {
			return false;
		}

		$placeholders = implode( ', ', array_fill( 0, count( $ids ), '%d' ) );
		$params       = array_merge( [ $status ], $ids );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$result = $this->db->query(
			$this->db->prepare(
				"UPDATE `{$this->table}` SET `status` = %s, `updated_at` = NOW() WHERE `id` IN ({$placeholders})",
				...$params
			)
		);

		return false !== $result;
	}

	/**
	 * Bulk soft-delete multiple clients.
	 *
	 * @param int[] $ids Client IDs.
	 * @return bool
	 */
	public function bulk_delete( array $ids ): bool {
		if ( empty( $ids ) ) {
			return false;
		}

		$placeholders = implode( ', ', array_fill( 0, count( $ids ), '%d' ) );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$result = $this->db->query(
			$this->db->prepare(
				"UPDATE `{$this->table}` SET `is_deleted` = 1, `status` = 'archived', `updated_at` = NOW() WHERE `id` IN ({$placeholders})",
				...$ids
			)
		);

		return false !== $result;
	}

	// -------------------------------------------------------------------------
	// Duplicate detection
	// -------------------------------------------------------------------------

	/**
	 * Check if a mobile number already exists (excluding a given ID).
	 *
	 * @param string $mobile    Mobile number.
	 * @param int    $exclude_id Client ID to exclude (for edit).
	 * @return bool
	 */
	public function mobile_exists( string $mobile, int $exclude_id = 0 ): bool {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$count = (int) $this->db->get_var(
			$this->db->prepare(
				"SELECT COUNT(*) FROM `{$this->table}` WHERE `mobile` = %s AND `id` != %d AND `is_deleted` = 0",
				$mobile,
				$exclude_id
			)
		);

		return $count > 0;
	}

	/**
	 * Check if an email already exists (excluding a given ID).
	 *
	 * @param string $email      Email address.
	 * @param int    $exclude_id Client ID to exclude.
	 * @return bool
	 */
	public function email_exists( string $email, int $exclude_id = 0 ): bool {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$count = (int) $this->db->get_var(
			$this->db->prepare(
				"SELECT COUNT(*) FROM `{$this->table}` WHERE `email` = %s AND `id` != %d AND `is_deleted` = 0",
				$email,
				$exclude_id
			)
		);

		return $count > 0;
	}

	/**
	 * Check if a GST number already exists (excluding a given ID).
	 *
	 * @param string $gst        GST number.
	 * @param int    $exclude_id Client ID to exclude.
	 * @return bool
	 */
	public function gst_exists( string $gst, int $exclude_id = 0 ): bool {
		if ( '' === $gst ) {
			return false;
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$count = (int) $this->db->get_var(
			$this->db->prepare(
				"SELECT COUNT(*) FROM `{$this->table}` WHERE `gst_number` = %s AND `id` != %d AND `is_deleted` = 0",
				$gst,
				$exclude_id
			)
		);

		return $count > 0;
	}

	// -------------------------------------------------------------------------
	// Notes
	// -------------------------------------------------------------------------

	/**
	 * Return all notes for a client.
	 *
	 * @param int $client_id Client ID.
	 * @return array<int, array<string,mixed>>
	 */
	public function get_notes( int $client_id ): array {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$rows = $this->db->get_results(
			$this->db->prepare(
				"SELECT `n`.*, `u`.`display_name` AS `author_name`
				FROM `{$this->notes_table}` `n`
				LEFT JOIN `{$this->db->users}` `u` ON `u`.`ID` = `n`.`user_id`
				WHERE `n`.`client_id` = %d
				ORDER BY `n`.`created_at` DESC",
				$client_id
			),
			ARRAY_A
		);

		return $rows ?: [];
	}

	/**
	 * Insert a note.
	 *
	 * @param int    $client_id Client ID.
	 * @param string $note      Note text.
	 * @param int    $user_id   Author user ID.
	 * @return int|false
	 */
	public function insert_note( int $client_id, string $note, int $user_id ): int|false {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$result = $this->db->insert(
			$this->notes_table,
			[
				'client_id'  => $client_id,
				'note'       => $note,
				'user_id'    => $user_id,
				'created_at' => current_time( 'mysql' ),
			]
		);

		return false !== $result ? (int) $this->db->insert_id : false;
	}

	/**
	 * Delete a note.
	 *
	 * @param int $note_id Note ID.
	 * @param int $user_id User requesting deletion (for ownership check).
	 * @return bool
	 */
	public function delete_note( int $note_id, int $user_id ): bool {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$result = $this->db->delete(
			$this->notes_table,
			[ 'id' => $note_id, 'user_id' => $user_id ]
		);

		return false !== $result && $result > 0;
	}

	// -------------------------------------------------------------------------
	// Documents
	// -------------------------------------------------------------------------

	/**
	 * Return all documents for a client.
	 *
	 * @param int $client_id Client ID.
	 * @return array<int, array<string,mixed>>
	 */
	public function get_documents( int $client_id ): array {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$rows = $this->db->get_results(
			$this->db->prepare(
				"SELECT * FROM `{$this->docs_table}` WHERE `client_id` = %d ORDER BY `created_at` DESC",
				$client_id
			),
			ARRAY_A
		);

		return $rows ?: [];
	}

	/**
	 * Insert a document record.
	 *
	 * @param array<string,mixed> $data Document data.
	 * @return int|false
	 */
	public function insert_document( array $data ): int|false {
		$data['created_at'] = current_time( 'mysql' );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$result = $this->db->insert( $this->docs_table, $data );

		return false !== $result ? (int) $this->db->insert_id : false;
	}

	/**
	 * Delete a document record.
	 *
	 * @param int $doc_id    Document ID.
	 * @param int $client_id Client ID (ownership check).
	 * @return bool
	 */
	public function delete_document( int $doc_id, int $client_id ): bool {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$result = $this->db->delete(
			$this->docs_table,
			[ 'id' => $doc_id, 'client_id' => $client_id ]
		);

		return false !== $result && $result > 0;
	}

	// -------------------------------------------------------------------------
	// Activity log
	// -------------------------------------------------------------------------

	/**
	 * Insert an activity log entry.
	 *
	 * @param array<string,mixed> $data Log data.
	 * @return bool
	 */
	public function log_activity( array $data ): bool {
		$data['created_at'] = current_time( 'mysql' );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$result = $this->db->insert( $this->log_table, $data );

		return false !== $result;
	}

	/**
	 * Return activity log entries for a client.
	 *
	 * @param int $client_id Client ID.
	 * @param int $limit     Max entries.
	 * @return array<int, array<string,mixed>>
	 */
	public function get_activity_log( int $client_id, int $limit = 50 ): array {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$rows = $this->db->get_results(
			$this->db->prepare(
				"SELECT `l`.*, `u`.`display_name` AS `user_name`
				FROM `{$this->log_table}` `l`
				LEFT JOIN `{$this->db->users}` `u` ON `u`.`ID` = `l`.`user_id`
				WHERE `l`.`object_id` = %d AND `l`.`object_type` = 'client'
				ORDER BY `l`.`created_at` DESC
				LIMIT %d",
				$client_id,
				$limit
			),
			ARRAY_A
		);

		return $rows ?: [];
	}

	// -------------------------------------------------------------------------
	// Export
	// -------------------------------------------------------------------------

	/**
	 * Return all clients for CSV export (no pagination).
	 *
	 * @param array<string,mixed> $args Filter args (same as get_clients minus pagination).
	 * @return Client_Model[]
	 */
	public function get_all_for_export( array $args = [] ): array {
		$args['per_page'] = 99999;
		$args['paged']    = 1;

		return $this->get_clients( $args );
	}

	// -------------------------------------------------------------------------
	// Filter helpers
	// -------------------------------------------------------------------------

	/**
	 * Return distinct cities for the filter dropdown.
	 *
	 * @return string[]
	 */
	public function get_distinct_cities(): array {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$rows = $this->db->get_col(
			"SELECT DISTINCT `city` FROM `{$this->table}` WHERE `city` != '' AND `is_deleted` = 0 ORDER BY `city` ASC"
		);

		return $rows ?: [];
	}

	/**
	 * Return distinct states for the filter dropdown.
	 *
	 * @return string[]
	 */
	public function get_distinct_states(): array {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$rows = $this->db->get_col(
			"SELECT DISTINCT `state` FROM `{$this->table}` WHERE `state` != '' AND `is_deleted` = 0 ORDER BY `state` ASC"
		);

		return $rows ?: [];
	}
}
