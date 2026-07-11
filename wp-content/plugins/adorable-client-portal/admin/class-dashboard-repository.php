<?php
/**
 * Dashboard data repository.
 *
 * @package AdorableClientPortal\Admin
 */

declare( strict_types=1 );

namespace AdorableClientPortal\Admin;

use AdorableClientPortal\Includes\Constants;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Dashboard_Repository
 *
 * Provides all data access methods for the dashboard.
 * Every method uses $wpdb prepared statements and returns
 * safe, typed values — never raw user input.
 */
final class Dashboard_Repository {

	/**
	 * WordPress database instance.
	 *
	 * @var \wpdb
	 */
	private \wpdb $db;

	/**
	 * Constructor.
	 */
	public function __construct() {
		global $wpdb;
		$this->db = $wpdb;
	}

	// -------------------------------------------------------------------------
	// Statistics cards
	// -------------------------------------------------------------------------

	/**
	 * Return total number of clients.
	 *
	 * @return int
	 */
	public function get_total_clients(): int {
		$table = $this->db->prefix . Constants::TABLE_CLIENTS;

		if ( ! $this->table_exists( $table ) ) {
			return 0;
		}

		return (int) $this->db->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$this->db->prepare( 'SELECT COUNT(*) FROM `%i`', $table )
		);
	}

	/**
	 * Return count of projects by status.
	 *
	 * @param string $status Project status slug.
	 * @return int
	 */
	public function get_projects_by_status( string $status ): int {
		$table = $this->db->prefix . Constants::TABLE_PROJECTS;

		if ( ! $this->table_exists( $table ) ) {
			return 0;
		}

		return (int) $this->db->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$this->db->prepare(
				'SELECT COUNT(*) FROM `%i` WHERE `status` = %s',
				$table,
				$status
			)
		);
	}

	/**
	 * Return count of running projects (all non-completed, non-lead statuses).
	 *
	 * @return int
	 */
	public function get_running_projects(): int {
		$table = $this->db->prefix . Constants::TABLE_PROJECTS;

		if ( ! $this->table_exists( $table ) ) {
			return 0;
		}

		$placeholders = implode( ', ', array_fill( 0, 5, '%s' ) );

		return (int) $this->db->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$this->db->prepare(
				"SELECT COUNT(*) FROM `%i` WHERE `status` IN ({$placeholders})", // phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnquotedComplexPlaceholder
				$table,
				'design',
				'approval',
				'production',
				'dispatch',
				'installation'
			)
		);
	}

	/**
	 * Return count of completed projects.
	 *
	 * @return int
	 */
	public function get_completed_projects(): int {
		return $this->get_projects_by_status( 'completed' );
	}

	/**
	 * Return count of quotes by status.
	 *
	 * @param string $status Quote status slug.
	 * @return int
	 */
	public function get_quotes_by_status( string $status ): int {
		$table = $this->db->prefix . Constants::TABLE_QUOTES;

		if ( ! $this->table_exists( $table ) ) {
			return 0;
		}

		return (int) $this->db->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$this->db->prepare(
				'SELECT COUNT(*) FROM `%i` WHERE `status` = %s',
				$table,
				$status
			)
		);
	}

	/**
	 * Return total revenue from paid payments.
	 *
	 * @return float
	 */
	public function get_total_revenue(): float {
		$table = $this->db->prefix . Constants::TABLE_PAYMENTS;

		if ( ! $this->table_exists( $table ) ) {
			return 0.0;
		}

		$result = $this->db->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$this->db->prepare(
				"SELECT COALESCE(SUM(`amount`), 0) FROM `%i` WHERE `status` = 'paid'",
				$table
			)
		);

		return (float) $result;
	}

	/**
	 * Return total pending payment amount.
	 *
	 * @return float
	 */
	public function get_pending_payments_amount(): float {
		$table = $this->db->prefix . Constants::TABLE_PAYMENTS;

		if ( ! $this->table_exists( $table ) ) {
			return 0.0;
		}

		$result = $this->db->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$this->db->prepare(
				"SELECT COALESCE(SUM(`amount`), 0) FROM `%i` WHERE `status` = 'pending'",
				$table
			)
		);

		return (float) $result;
	}

	/**
	 * Return count of projects with upcoming installation (within 30 days).
	 *
	 * @return int
	 */
	public function get_upcoming_installations(): int {
		$table = $this->db->prefix . Constants::TABLE_PROJECTS;

		if ( ! $this->table_exists( $table ) ) {
			return 0;
		}

		return (int) $this->db->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$this->db->prepare(
				"SELECT COUNT(*) FROM `%i`
				WHERE `status` = 'installation'
				AND `installation_date` BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)",
				$table
			)
		);
	}

	// -------------------------------------------------------------------------
	// Chart data
	// -------------------------------------------------------------------------

	/**
	 * Return monthly revenue for the last 12 months.
	 *
	 * @return array<int, array{month: string, revenue: float}>
	 */
	public function get_monthly_revenue(): array {
		$table = $this->db->prefix . Constants::TABLE_PAYMENTS;

		if ( ! $this->table_exists( $table ) ) {
			return $this->empty_monthly_data();
		}

		$rows = $this->db->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$this->db->prepare(
				"SELECT
					DATE_FORMAT(`payment_date`, '%%Y-%%m') AS `month`,
					COALESCE(SUM(`amount`), 0) AS `revenue`
				FROM `%i`
				WHERE `status` = 'paid'
				AND `payment_date` >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
				GROUP BY DATE_FORMAT(`payment_date`, '%%Y-%%m')
				ORDER BY `month` ASC",
				$table
			),
			ARRAY_A
		);

		if ( empty( $rows ) ) {
			return $this->empty_monthly_data();
		}

		return array_map(
			static fn( array $row ) => [
				'month'   => (string) $row['month'],
				'revenue' => (float) $row['revenue'],
			],
			$rows
		);
	}

	/**
	 * Return project counts grouped by status.
	 *
	 * @return array<string, int>
	 */
	public function get_projects_status_distribution(): array {
		$table    = $this->db->prefix . Constants::TABLE_PROJECTS;
		$defaults = array_fill_keys( array_keys( Constants::PROJECT_STATUSES ), 0 );

		if ( ! $this->table_exists( $table ) ) {
			return $defaults;
		}

		$rows = $this->db->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$this->db->prepare(
				'SELECT `status`, COUNT(*) AS `count` FROM `%i` GROUP BY `status`',
				$table
			),
			ARRAY_A
		);

		if ( empty( $rows ) ) {
			return $defaults;
		}

		$result = $defaults;

		foreach ( $rows as $row ) {
			$status = (string) $row['status'];
			if ( array_key_exists( $status, $result ) ) {
				$result[ $status ] = (int) $row['count'];
			}
		}

		return $result;
	}

	/**
	 * Return quote counts grouped by status.
	 *
	 * @return array<string, int>
	 */
	public function get_quotes_status_distribution(): array {
		$table    = $this->db->prefix . Constants::TABLE_QUOTES;
		$defaults = array_fill_keys( array_keys( Constants::QUOTE_STATUSES ), 0 );

		if ( ! $this->table_exists( $table ) ) {
			return $defaults;
		}

		$rows = $this->db->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$this->db->prepare(
				'SELECT `status`, COUNT(*) AS `count` FROM `%i` GROUP BY `status`',
				$table
			),
			ARRAY_A
		);

		if ( empty( $rows ) ) {
			return $defaults;
		}

		$result = $defaults;

		foreach ( $rows as $row ) {
			$status = (string) $row['status'];
			if ( array_key_exists( $status, $result ) ) {
				$result[ $status ] = (int) $row['count'];
			}
		}

		return $result;
	}

	/**
	 * Return payment counts grouped by status.
	 *
	 * @return array<string, int>
	 */
	public function get_payments_status_distribution(): array {
		$table    = $this->db->prefix . Constants::TABLE_PAYMENTS;
		$defaults = array_fill_keys( array_keys( Constants::PAYMENT_STATUSES ), 0 );

		if ( ! $this->table_exists( $table ) ) {
			return $defaults;
		}

		$rows = $this->db->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$this->db->prepare(
				'SELECT `status`, COUNT(*) AS `count` FROM `%i` GROUP BY `status`',
				$table
			),
			ARRAY_A
		);

		if ( empty( $rows ) ) {
			return $defaults;
		}

		$result = $defaults;

		foreach ( $rows as $row ) {
			$status = (string) $row['status'];
			if ( array_key_exists( $status, $result ) ) {
				$result[ $status ] = (int) $row['count'];
			}
		}

		return $result;
	}

	// -------------------------------------------------------------------------
	// Recent activity
	// -------------------------------------------------------------------------

	/**
	 * Return the latest activity log entries.
	 *
	 * @param int $limit Number of entries to return.
	 * @return array<int, array{id: int, action: string, description: string, user_id: int, created_at: string}>
	 */
	public function get_recent_activity( int $limit = 10 ): array {
		$table = $this->db->prefix . Constants::TABLE_ACTIVITY_LOGS;

		if ( ! $this->table_exists( $table ) ) {
			return [];
		}

		$rows = $this->db->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$this->db->prepare(
				'SELECT `id`, `action`, `description`, `user_id`, `created_at`
				FROM `%i`
				ORDER BY `created_at` DESC
				LIMIT %d',
				$table,
				$limit
			),
			ARRAY_A
		);

		if ( empty( $rows ) ) {
			return [];
		}

		return array_map(
			static fn( array $row ) => [
				'id'          => (int) $row['id'],
				'action'      => (string) $row['action'],
				'description' => (string) $row['description'],
				'user_id'     => (int) $row['user_id'],
				'created_at'  => (string) $row['created_at'],
			],
			$rows
		);
	}

	/**
	 * Return the latest clients.
	 *
	 * @param int $limit Number of records.
	 * @return array<int, array{id: int, name: string, email: string, created_at: string}>
	 */
	public function get_recent_clients( int $limit = 5 ): array {
		$table = $this->db->prefix . Constants::TABLE_CLIENTS;

		if ( ! $this->table_exists( $table ) ) {
			return [];
		}

		$rows = $this->db->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$this->db->prepare(
				'SELECT `id`, `name`, `email`, `created_at`
				FROM `%i`
				ORDER BY `created_at` DESC
				LIMIT %d',
				$table,
				$limit
			),
			ARRAY_A
		);

		if ( empty( $rows ) ) {
			return [];
		}

		return array_map(
			static fn( array $row ) => [
				'id'         => (int) $row['id'],
				'name'       => (string) $row['name'],
				'email'      => (string) $row['email'],
				'created_at' => (string) $row['created_at'],
			],
			$rows
		);
	}

	/**
	 * Return the latest projects.
	 *
	 * @param int $limit Number of records.
	 * @return array<int, array{id: int, title: string, status: string, created_at: string}>
	 */
	public function get_recent_projects( int $limit = 5 ): array {
		$table = $this->db->prefix . Constants::TABLE_PROJECTS;

		if ( ! $this->table_exists( $table ) ) {
			return [];
		}

		$rows = $this->db->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$this->db->prepare(
				'SELECT `id`, `title`, `status`, `created_at`
				FROM `%i`
				ORDER BY `created_at` DESC
				LIMIT %d',
				$table,
				$limit
			),
			ARRAY_A
		);

		if ( empty( $rows ) ) {
			return [];
		}

		return array_map(
			static fn( array $row ) => [
				'id'         => (int) $row['id'],
				'title'      => (string) $row['title'],
				'status'     => (string) $row['status'],
				'created_at' => (string) $row['created_at'],
			],
			$rows
		);
	}

	// -------------------------------------------------------------------------
	// Helpers
	// -------------------------------------------------------------------------

	/**
	 * Check whether a table exists in the database.
	 *
	 * @param string $table Full table name including prefix.
	 * @return bool
	 */
	private function table_exists( string $table ): bool {
		$result = $this->db->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$this->db->prepare(
				'SHOW TABLES LIKE %s',
				$this->db->esc_like( $table )
			)
		);

		return ! empty( $result );
	}

	/**
	 * Return an empty 12-month revenue array with zero values.
	 *
	 * @return array<int, array{month: string, revenue: float}>
	 */
	private function empty_monthly_data(): array {
		$data = [];

		for ( $i = 11; $i >= 0; $i-- ) {
			$data[] = [
				'month'   => gmdate( 'Y-m', strtotime( "-{$i} months" ) ),
				'revenue' => 0.0,
			];
		}

		return $data;
	}
}
