<?php
/**
 * Migration v1.0.0 — Clients module tables.
 *
 * Creates:
 *   wp_ac_clients
 *   wp_ac_client_notes
 *   wp_ac_client_documents
 *   wp_ac_client_contacts
 *   wp_ac_client_addresses
 *   wp_ac_activity_logs
 *
 * Uses dbDelta() — safe to run on every activation and upgrade.
 * Never executes raw CREATE TABLE directly.
 *
 * @package AdorableClientPortal\Database\Migrations
 */

declare( strict_types=1 );

namespace AdorableClientPortal\Database\Migrations;

use AdorableClientPortal\Includes\Constants;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Migration_V1_0_0
 *
 * Implements the database schema for plugin version 1.0.0.
 * Each public method is idempotent — safe to call multiple times.
 */
final class Migration_V1_0_0 {

	/**
	 * The schema version this migration represents.
	 */
	public const VERSION = '1.0.0';

	/** @var \wpdb */
	private \wpdb $db;

	/** @var string MySQL charset collate string. */
	private string $charset_collate;

	public function __construct() {
		global $wpdb;
		$this->db              = $wpdb;
		$this->charset_collate = $wpdb->get_charset_collate();
	}

	/**
	 * Run all table creation / alteration statements for this version.
	 *
	 * @return void
	 */
	public function up(): void {
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$this->create_clients_table();
		$this->create_client_notes_table();
		$this->create_client_documents_table();
		$this->create_client_contacts_table();
		$this->create_client_addresses_table();
		$this->create_activity_logs_table();
	}

	/**
	 * Rollback — drops all tables created by this migration.
	 * Only called during full uninstall, never on deactivation.
	 *
	 * @return void
	 */
	public function down(): void {
		$tables = [
			$this->db->prefix . Constants::TABLE_CLIENT_ADDRESSES,
			$this->db->prefix . Constants::TABLE_CLIENT_CONTACTS,
			$this->db->prefix . Constants::TABLE_CLIENT_DOCUMENTS,
			$this->db->prefix . Constants::TABLE_CLIENT_NOTES,
			$this->db->prefix . Constants::TABLE_ACTIVITY_LOGS,
			$this->db->prefix . Constants::TABLE_CLIENTS,
		];

		foreach ( $tables as $table ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$this->db->query( "DROP TABLE IF EXISTS `{$table}`" );
		}
	}

	// -------------------------------------------------------------------------
	// Table definitions
	// -------------------------------------------------------------------------

	/**
	 * Create or update wp_ac_clients.
	 *
	 * Columns follow the full specification:
	 *   client_code      — auto-generated, e.g. AC000001
	 *   client_type      — individual | company
	 *   deleted_at       — soft-delete timestamp (NULL = active)
	 *
	 * Unique keys: client_code, mobile, email, gst_number
	 * Indexes:     status, city, assigned_sales, assigned_designer,
	 *              created_at, deleted_at
	 *
	 * @return void
	 */
	private function create_clients_table(): void {
		$table = $this->db->prefix . Constants::TABLE_CLIENTS;

		/*
		 * dbDelta() is strict about formatting:
		 *   - Two spaces before column definitions.
		 *   - PRIMARY KEY must be on its own line.
		 *   - No trailing comma on the last column.
		 *   - KEY definitions after all columns.
		 */
		$sql = "CREATE TABLE {$table} (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  client_code VARCHAR(30) NOT NULL DEFAULT '',
  client_type ENUM('individual','company') NOT NULL DEFAULT 'individual',
  company_name VARCHAR(200) NOT NULL DEFAULT '',
  client_name VARCHAR(200) NOT NULL DEFAULT '',
  primary_contact VARCHAR(200) NOT NULL DEFAULT '',
  secondary_contact VARCHAR(200) NOT NULL DEFAULT '',
  email VARCHAR(200) NOT NULL DEFAULT '',
  alternate_email VARCHAR(200) NOT NULL DEFAULT '',
  mobile VARCHAR(20) NOT NULL DEFAULT '',
  alternate_mobile VARCHAR(20) NOT NULL DEFAULT '',
  whatsapp_number VARCHAR(20) NOT NULL DEFAULT '',
  gst_number VARCHAR(20) NOT NULL DEFAULT '',
  pan_number VARCHAR(20) NOT NULL DEFAULT '',
  billing_address TEXT NOT NULL,
  site_address TEXT NOT NULL,
  city VARCHAR(100) NOT NULL DEFAULT '',
  state VARCHAR(100) NOT NULL DEFAULT '',
  country VARCHAR(100) NOT NULL DEFAULT 'India',
  pincode VARCHAR(10) NOT NULL DEFAULT '',
  lead_source VARCHAR(50) NOT NULL DEFAULT '',
  assigned_sales BIGINT UNSIGNED NOT NULL DEFAULT 0,
  assigned_designer BIGINT UNSIGNED NOT NULL DEFAULT 0,
  status VARCHAR(20) NOT NULL DEFAULT 'lead',
  notes LONGTEXT NOT NULL,
  created_by BIGINT UNSIGNED NOT NULL DEFAULT 0,
  updated_by BIGINT UNSIGNED NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  updated_at DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  deleted_at DATETIME DEFAULT NULL,
  PRIMARY KEY  (id),
  UNIQUE KEY client_code (client_code),
  UNIQUE KEY mobile (mobile),
  UNIQUE KEY email (email),
  UNIQUE KEY gst_number (gst_number),
  KEY status (status),
  KEY city (city),
  KEY assigned_sales (assigned_sales),
  KEY assigned_designer (assigned_designer),
  KEY created_at (created_at),
  KEY deleted_at (deleted_at)
) {$this->charset_collate};";

		dbDelta( $sql );
	}

	/**
	 * Create or update wp_ac_client_notes.
	 *
	 * @return void
	 */
	private function create_client_notes_table(): void {
		$table = $this->db->prefix . Constants::TABLE_CLIENT_NOTES;

		$sql = "CREATE TABLE {$table} (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  client_id BIGINT UNSIGNED NOT NULL,
  note LONGTEXT NOT NULL,
  user_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY  (id),
  KEY client_id (client_id),
  KEY user_id (user_id)
) {$this->charset_collate};";

		dbDelta( $sql );
	}

	/**
	 * Create or update wp_ac_client_documents.
	 *
	 * Future relationship: client_id → wp_ac_clients.id
	 *
	 * @return void
	 */
	private function create_client_documents_table(): void {
		$table = $this->db->prefix . Constants::TABLE_CLIENT_DOCUMENTS;

		$sql = "CREATE TABLE {$table} (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  client_id BIGINT UNSIGNED NOT NULL,
  document_name VARCHAR(255) NOT NULL DEFAULT '',
  document_type VARCHAR(50) NOT NULL DEFAULT '',
  file_path VARCHAR(500) NOT NULL DEFAULT '',
  file_url VARCHAR(500) NOT NULL DEFAULT '',
  file_size BIGINT UNSIGNED NOT NULL DEFAULT 0,
  mime_type VARCHAR(100) NOT NULL DEFAULT '',
  uploaded_by BIGINT UNSIGNED NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY  (id),
  KEY client_id (client_id),
  KEY uploaded_by (uploaded_by)
) {$this->charset_collate};";

		dbDelta( $sql );
	}

	/**
	 * Create or update wp_ac_client_contacts.
	 *
	 * Stores multiple contact persons per client.
	 * Future relationship: client_id → wp_ac_clients.id
	 *
	 * @return void
	 */
	private function create_client_contacts_table(): void {
		$table = $this->db->prefix . Constants::TABLE_CLIENT_CONTACTS;

		$sql = "CREATE TABLE {$table} (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  client_id BIGINT UNSIGNED NOT NULL,
  contact_name VARCHAR(200) NOT NULL DEFAULT '',
  designation VARCHAR(100) NOT NULL DEFAULT '',
  email VARCHAR(200) NOT NULL DEFAULT '',
  mobile VARCHAR(20) NOT NULL DEFAULT '',
  whatsapp VARCHAR(20) NOT NULL DEFAULT '',
  is_primary TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY  (id),
  KEY client_id (client_id)
) {$this->charset_collate};";

		dbDelta( $sql );
	}

	/**
	 * Create or update wp_ac_client_addresses.
	 *
	 * Stores multiple addresses per client (billing, site, etc.).
	 * Future relationship: client_id → wp_ac_clients.id
	 *
	 * @return void
	 */
	private function create_client_addresses_table(): void {
		$table = $this->db->prefix . Constants::TABLE_CLIENT_ADDRESSES;

		$sql = "CREATE TABLE {$table} (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  client_id BIGINT UNSIGNED NOT NULL,
  address_type VARCHAR(50) NOT NULL DEFAULT 'billing',
  address_line1 VARCHAR(255) NOT NULL DEFAULT '',
  address_line2 VARCHAR(255) NOT NULL DEFAULT '',
  city VARCHAR(100) NOT NULL DEFAULT '',
  state VARCHAR(100) NOT NULL DEFAULT '',
  country VARCHAR(100) NOT NULL DEFAULT 'India',
  pincode VARCHAR(10) NOT NULL DEFAULT '',
  is_default TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY  (id),
  KEY client_id (client_id),
  KEY address_type (address_type)
) {$this->charset_collate};";

		dbDelta( $sql );
	}

	/**
	 * Create or update wp_ac_activity_logs.
	 *
	 * Shared log table for all modules.
	 * object_type: 'client' | 'project' | 'quote' | 'payment'
	 * Future relationships: object_id → respective module tables.
	 *
	 * @return void
	 */
	private function create_activity_logs_table(): void {
		$table = $this->db->prefix . Constants::TABLE_ACTIVITY_LOGS;

		$sql = "CREATE TABLE {$table} (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  object_type VARCHAR(50) NOT NULL DEFAULT '',
  object_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
  action VARCHAR(100) NOT NULL DEFAULT '',
  description TEXT NOT NULL,
  old_value LONGTEXT DEFAULT NULL,
  new_value LONGTEXT DEFAULT NULL,
  user_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
  ip_address VARCHAR(45) NOT NULL DEFAULT '',
  created_at DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY  (id),
  KEY object_type_id (object_type,object_id),
  KEY user_id (user_id),
  KEY created_at (created_at)
) {$this->charset_collate};";

		dbDelta( $sql );
	}
}
