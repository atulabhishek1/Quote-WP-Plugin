<?php
/**
 * Clients repository.
 *
 * All database access for the Clients module.
 * No SQL outside this class.
 *
 * @package AdorableClientPortal\Includes\Repositories
 */

declare( strict_types=1 );

namespace AdorableClientPortal\Includes\Repositories;

use AdorableClientPortal\Includes\Constants;
use AdorableClientPortal\Includes\Client_Model;
use AdorableClientPortal\Includes\Interfaces\Client_Repository_Interface;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Client_Repository
 */
final class Client_Repository implements Client_Repository_Interface {

	/** @var \wpdb */
	private \wpdb $db;

	/** @var string Clients table with prefix. */
	private string $table;

	/** @var string Projects table with prefix. */
	private string $projects_table;

	/** @var string Payments table with prefix. */
	private string $payments_table;

	/**
	 * Client_Repository constructor.
	 */
	public function __construct() {
		global $wpdb;

		$this->db             = $wpdb;
		$this->table          = $wpdb->prefix . Constants::TABLE_CLIENTS;
		$this->projects_table = $wpdb->prefix . Constants::TABLE_PROJECTS;
		$this->payments_table = $wpdb->prefix . Constants::TABLE_PAYMENTS;
	}

	/**
	 * {@inheritDoc}
	 */
	public function create( array $data ): int|false {
		return $this->insert( $data );
	}

	/**
	 * {@inheritDoc}
	 */
	public function update( int $id, array $data ): bool {
		return $this->updateRow( $id, $data );
	}

	/**
	 * {@inheritDoc}
	 */
	public function delete( int $id ): bool {
		return $this->softDelete( $id );
	}

	/**
	 * {@inheritDoc}
	 */
	public function restore( int $id ): bool {
		return $this->restoreRow( $id );
	}

	/**
	 * {@inheritDoc}
	 */
	public function find( int $id, bool $include_deleted = false ): ?Client_Model {
		return $this->getClientById( $id, $include_deleted );
	}

	/**
	 * {@inheritDoc}
	 */
	public function findById( int $id ): ?Client_Model {
		return $this->getClientById( $id );
	}

	/**
	 * {@inheritDoc}
	 */
	public function findByEmail( string $email ): ?Client_Model {
		return $this->findByField( 'email', $email );
	}

	/**
	 * {@inheritDoc}
	 */
	public function findByMobile( string $mobile ): ?Client_Model {
		return $this->findByField( 'mobile', $mobile );
	}

	/**
	 * {@inheritDoc}
	 */
	public function findByCode( string $code ): ?Client_Model {
		return $this->findByField( 'client_code', $code );
	}

	/**
	 * {@inheritDoc}
	 */
	public function exists( string $field, string $value, int $exclude_id = 0 ): bool {
		$allowed = [ 'email', 'mobile', 'gst_number', 'client_code' ];

		if ( ! in_array( $field, $allowed, true ) ) {
			return false;
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$count = (int) $this->db->get_var(
			$this->db->prepare(
				"SELECT COUNT(*) FROM `{$this->table}` WHERE `{$field}` = %s AND `id` != %d AND `is_deleted` = 0",
				$value,
				$exclude_id
			)
		);

		return $count > 0;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getAll( array $args = [] ): array {
		return $this->getClients( $args );
	}

	/**
	 * {@inheritDoc}
	 */
	public function search( string $term, int $per_page = 20 ): array {
		return $this->getClients([
			'search'   => $term,
			'per_page' => $per_page,
			'paged'    => 1,
		]);
	}

	/**
	 * {@inheritDoc}
	 */
	public function count( array $args = [] ): int {
		return $this->countClients( $args );
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDistinctCities(): array {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$rows = $this->db->get_col(
			"SELECT DISTINCT `city` FROM `{$this->table}` WHERE `city` != '' AND `is_deleted` = 0 ORDER BY `city` ASC"
		);

		return $rows ?: [];
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDistinctStates(): array {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$rows = $this->db->get_col(
			"SELECT DISTINCT `state` FROM `{$this->table}` WHERE `state` != '' AND `is_deleted` = 0 ORDER BY `state` ASC"
		);

		return $rows ?: [];
	}

	/**
	 * Return a paginated, filtered list of clients with computed columns.
	 *
	 * @param array<string,mixed> $args Query arguments.
	 * @return Client_Model[]
	 */
	private function getClients( array $args = [] ): array {
		$defaults = [
			'search'          => '',
			'status'          => '',
			'salesperson'     => 0,
			'designer'        => 0,
			'city'            => '',
			'state'           => '',
			'date_from'       => '',
			'date_to'         => '',
			'orderby'         => 'created_at',
			'order'           => 'DESC',
			'per_page'        => 20,
			'paged'           => 1,
			'include_deleted' => false,
		];

		$args   = wp_parse_args( $args, $defaults );
		$where  = [ '1=1' ];
		$params = [];

		if ( ! $args['include_deleted'] ) {
			$where[] = '`c`.`is_deleted` = 0';
		}

		if ( '' !== trim( $args['search'] ) ) {
			$like = '%' . $this->db->esc_like( $args['search'] ) . '%';
			$where[] = '(`c`.`client_name` LIKE %s OR `c`.`mobile` LIKE %s OR `c`.`email` LIKE %s OR `c`.`company_name` LIKE %s OR `c`.`gst_number` LIKE %s OR `c`.`pan_number` LIKE %s OR `c`.`client_code` LIKE %s)';
			$params[] = $like;
			$params[] = $like;
			$params[] = $like;
			$params[] = $like;
			$params[] = $like;
			$params[] = $like;
			$params[] = $like;
		}

		if ( '' !== trim( $args['status'] ) ) {
			$where[] = '`c`.`status` = %s';
			$params[] = $args['status'];
		}

		if ( ! empty( $args['salesperson'] ) ) {
			$where[] = '`c`.`assigned_sales` = %d';
			$params[] = (int) $args['salesperson'];
		}

		if ( ! empty( $args['designer'] ) ) {
			$where[] = '`c`.`assigned_designer` = %d';
			$params[] = (int) $args['designer'];
		}

		if ( '' !== trim( $args['city'] ) ) {
			$where[] = '`c`.`city` = %s';
			$params[] = $args['city'];
		}

		if ( '' !== trim( $args['state'] ) ) {
			$where[] = '`c`.`state` = %s';
			$params[] = $args['state'];
		}

		if ( '' !== trim( $args['date_from'] ) ) {
			$where[] = 'DATE(`c`.`created_at`) >= %s';
			$params[] = $args['date_from'];
		}

		if ( '' !== trim( $args['date_to'] ) ) {
			$where[] = 'DATE(`c`.`created_at`) <= %s';
			$params[] = $args['date_to'];
		}

		$allowed_order = [ 'client_name', 'company_name', 'mobile', 'email', 'status', 'created_at', 'updated_at', 'projects_count', 'total_revenue' ];
		$orderby = in_array( $args['orderby'], $allowed_order, true ) ? $args['orderby'] : 'created_at';
		$order   = 'ASC' === strtoupper( $args['order'] ) ? 'ASC' : 'DESC';
		$per_page = max( 1, (int) $args['per_page'] );
		$offset = ( max( 1, (int) $args['paged'] ) - 1 ) * $per_page;

		$where_sql = implode( ' AND ', $where );

		$sql = "
			SELECT
				`c`.`id`,
				`c`.`client_code`,
				`c`.`client_type`,
				`c`.`company_name`,
				`c`.`client_name` AS `name`,
				`c`.`primary_contact`,
				`c`.`secondary_contact`,
				`c`.`email`,
				`c`.`alternate_email`,
				`c`.`mobile`,
				`c`.`alternate_mobile`,
				`c`.`whatsapp_number` AS `whatsapp`,
				`c`.`gst_number`,
				`c`.`pan_number`,
				`c`.`billing_address`,
				`c`.`site_address`,
				`c`.`city`,
				`c`.`state`,
				`c`.`country`,
				`c`.`pincode`,
				`c`.`lead_source`,
				`c`.`assigned_sales` AS `assigned_salesperson`,
				`c`.`assigned_designer`,
				`c`.`status`,
				`c`.`notes`,
				`c`.`is_deleted`,
				`c`.`created_at`,
				`c`.`updated_at`,
				COALESCE((
					SELECT COUNT(*) FROM `{$this->projects_table}` `p`
					WHERE `p`.`client_id` = `c`.`id`
				), 0) AS `projects_count`,
				COALESCE((
					SELECT SUM(`py`.`amount`) FROM `{$this->payments_table}` `py`
					INNER JOIN `{$this->projects_table}` `pj` ON `pj`.`id` = `py`.`project_id`
					WHERE `pj`.`client_id` = `c`.`id` AND `py`.`status` = 'paid'
				), 0) AS `total_revenue`,
				COALESCE((
					SELECT SUM(`py`.`amount`) FROM `{$this->payments_table}` `py`
					INNER JOIN `{$this->projects_table}` `pj` ON `pj`.`id` = `py`.`project_id`
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
	 * Return total count matching the given filters.
	 *
	 * @param array<string,mixed> $args Query arguments.
	 * @return int
	 */
	private function countClients( array $args = [] ): int {
		$defaults = [
			'search'          => '',
			'status'          => '',
			'salesperson'     => 0,
			'designer'        => 0,
			'city'            => '',
			'state'           => '',
			'date_from'       => '',
			'date_to'         => '',
			'include_deleted' => false,
		];

		$args   = wp_parse_args( $args, $defaults );
		$where  = [ '1=1' ];
		$params = [];

		if ( ! $args['include_deleted'] ) {
			$where[] = '`is_deleted` = 0';
		}

		if ( '' !== trim( $args['search'] ) ) {
			$like = '%' . $this->db->esc_like( $args['search'] ) . '%';
			$where[] = '(`client_name` LIKE %s OR `mobile` LIKE %s OR `email` LIKE %s OR `company_name` LIKE %s OR `gst_number` LIKE %s OR `pan_number` LIKE %s OR `client_code` LIKE %s)';
			$params[] = $like;
			$params[] = $like;
			$params[] = $like;
			$params[] = $like;
			$params[] = $like;
			$params[] = $like;
			$params[] = $like;
		}

		if ( '' !== trim( $args['status'] ) ) {
			$where[] = '`status` = %s';
			$params[] = $args['status'];
		}

		if ( ! empty( $args['salesperson'] ) ) {
			$where[] = '`assigned_sales` = %d';
			$params[] = (int) $args['salesperson'];
		}

		if ( ! empty( $args['designer'] ) ) {
			$where[] = '`assigned_designer` = %d';
			$params[] = (int) $args['designer'];
		}

		if ( '' !== trim( $args['city'] ) ) {
			$where[] = '`city` = %s';
			$params[] = $args['city'];
		}

		if ( '' !== trim( $args['state'] ) ) {
			$where[] = '`state` = %s';
			$params[] = $args['state'];
		}

		if ( '' !== trim( $args['date_from'] ) ) {
			$where[] = 'DATE(`created_at`) >= %s';
			$params[] = $args['date_from'];
		}

		if ( '' !== trim( $args['date_to'] ) ) {
			$where[] = 'DATE(`created_at`) <= %s';
			$params[] = $args['date_to'];
		}

		$where_sql = implode( ' AND ', $where );
		$sql = "SELECT COUNT(*) FROM `{$this->table}` WHERE {$where_sql}";

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
	 * @param int  $id Client ID.
	 * @param bool $include_deleted Include soft-deleted records.
	 * @return Client_Model|null
	 */
	private function getClientById( int $id, bool $include_deleted = false ): ?Client_Model {
		$deleted_clause = $include_deleted ? '' : 'AND `is_deleted` = 0';

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$row = $this->db->get_row(
			$this->db->prepare(
				"SELECT * FROM `{$this->table}` WHERE `id` = %d {$deleted_clause} LIMIT 1",
				$id
			),
			ARRAY_A
		);

		return $row ? new Client_Model( $this->normalizeRow( $row ) ) : null;
	}

	/**
	 * Find a client by a unique field.
	 *
	 * @param string $field Column name.
	 * @param string $value Field value.
	 * @return Client_Model|null
	 */
	private function findByField( string $field, string $value ): ?Client_Model {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.NotPrepared
		$row = $this->db->get_row(
			$this->db->prepare(
				"SELECT * FROM `{$this->table}` WHERE `{$field}` = %s AND `is_deleted` = 0 LIMIT 1",
				$value
			),
			ARRAY_A
		);

		return $row ? new Client_Model( $this->normalizeRow( $row ) ) : null;
	}

	/**
	 * Normalize raw database rows for Client_Model compatibility.
	 *
	 * @param array<string,mixed> $row Raw database row.
	 * @return array<string,mixed>
	 */
	private function normalizeRow( array $row ): array {
		if ( isset( $row['client_name'] ) && ! isset( $row['name'] ) ) {
			$row['name'] = $row['client_name'];
		}

		if ( isset( $row['whatsapp_number'] ) && ! isset( $row['whatsapp'] ) ) {
			$row['whatsapp'] = $row['whatsapp_number'];
		}

		if ( isset( $row['assigned_sales'] ) && ! isset( $row['assigned_salesperson'] ) ) {
			$row['assigned_salesperson'] = $row['assigned_sales'];
		}

		return $row;
	}

	/**
	 * Insert a new client row.
	 *
	 * @param array<string,mixed> $data Sanitised column => value pairs.
	 * @return int|false New client ID or false on failure.
	 */
	private function insert( array $data ): int|false {
		$data['created_at'] = current_time( 'mysql' );
		$data['updated_at'] = current_time( 'mysql' );

		if ( ! isset( $data['is_deleted'] ) ) {
			$data['is_deleted'] = 0;
		}

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
	private function updateRow( int $id, array $data ): bool {
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
	private function softDelete( int $id ): bool {
		return $this->updateRow( $id, [
			'is_deleted' => 1,
			'deleted_at' => current_time( 'mysql' ),
			'status'     => 'archived',
		] );
	}

	/**
	 * Restore a soft-deleted client.
	 *
	 * @param int $id Client ID.
	 * @return bool
	 */
	private function restoreRow( int $id ): bool {
		return $this->updateRow( $id, [
			'is_deleted' => 0,
			'deleted_at' => null,
		] );
	}
}
