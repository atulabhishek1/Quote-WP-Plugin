<?php
/**
 * Client repository interface.
 *
 * Defines the contract that every concrete Client repository must satisfy.
 * Depend on this interface, not on the concrete class, to keep modules
 * decoupled and testable.
 *
 * @package AdorableClientPortal\Includes\Interfaces
 */

declare( strict_types=1 );

namespace AdorableClientPortal\Includes\Interfaces;

use AdorableClientPortal\Includes\Models\Client;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Interface Client_Repository_Interface
 */
interface Client_Repository_Interface {

	/**
	 * Persist a new client and return its generated ID.
	 *
	 * @param array<string,mixed> $data Validated, sanitised field map.
	 * @return int|false  New client ID, or false on failure.
	 */
	public function create( array $data ): int|false;

	/**
	 * Update an existing client record.
	 *
	 * @param int                 $id   Client ID.
	 * @param array<string,mixed> $data Validated, sanitised field map.
	 * @return bool
	 */
	public function update( int $id, array $data ): bool;

	/**
	 * Soft-delete a client by setting deleted_at.
	 *
	 * @param int $id Client ID.
	 * @return bool
	 */
	public function delete( int $id ): bool;

	/**
	 * Restore a soft-deleted client (clear deleted_at).
	 *
	 * @param int $id Client ID.
	 * @return bool
	 */
	public function restore( int $id ): bool;

	/**
	 * Find a client by primary key.
	 *
	 * @param int  $id             Client ID.
	 * @param bool $include_deleted Return soft-deleted records too.
	 * @return Client|null
	 */
	public function find( int $id, bool $include_deleted = false ): ?Client;

	/**
	 * Alias of find() — explicit name for clarity in service layer.
	 *
	 * @param int $id Client ID.
	 * @return Client|null
	 */
	public function findById( int $id ): ?Client; // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid

	/**
	 * Find a client by email address.
	 *
	 * @param string $email Email address.
	 * @return Client|null
	 */
	public function findByEmail( string $email ): ?Client; // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid

	/**
	 * Find a client by mobile number.
	 *
	 * @param string $mobile Mobile number.
	 * @return Client|null
	 */
	public function findByMobile( string $mobile ): ?Client; // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid

	/**
	 * Find a client by their unique client code (e.g. AC000001).
	 *
	 * @param string $code Client code.
	 * @return Client|null
	 */
	public function findByCode( string $code ): ?Client; // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid

	/**
	 * Check whether a client with the given field value already exists.
	 *
	 * @param string $field      Column name: 'email' | 'mobile' | 'gst_number' | 'client_code'.
	 * @param string $value      Value to check.
	 * @param int    $exclude_id Exclude this ID (for edit operations).
	 * @return bool
	 */
	public function exists( string $field, string $value, int $exclude_id = 0 ): bool;

	/**
	 * Return a paginated, filtered list of clients.
	 *
	 * @param array<string,mixed> $args Query arguments.
	 * @return Client[]
	 */
	public function getAll( array $args = [] ): array; // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid

	/**
	 * Full-text search across name, mobile, email, company, GST, PAN.
	 *
	 * @param string $term     Search term.
	 * @param int    $per_page Max results.
	 * @return Client[]
	 */
	public function search( string $term, int $per_page = 20 ): array;

	/**
	 * Return the total count matching the given filter args.
	 *
	 * @param array<string,mixed> $args Same filter args as getAll(), minus pagination.
	 * @return int
	 */
	public function count( array $args = [] ): int;
}
