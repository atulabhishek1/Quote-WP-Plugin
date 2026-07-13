<?php
/**
 * Client service.
 *
 * Business logic for the Clients module.
 * No SQL in this layer; all persistence is delegated to the repository.
 *
 * @package AdorableClientPortal\Includes\Services
 */

declare( strict_types=1 );

namespace AdorableClientPortal\Includes\Services;

use AdorableClientPortal\Includes\Client_Model;
use AdorableClientPortal\Includes\Constants;
use AdorableClientPortal\Includes\Interfaces\Client_Repository_Interface;
use AdorableClientPortal\Includes\Repositories\Client_Repository;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Client_Service
 */
final class Client_Service {

	private Client_Repository_Interface $repository;

	/**
	 * Client_Service constructor.
	 *
	 * @param Client_Repository_Interface|null $repository Optional repository implementation.
	 */
	public function __construct( ?Client_Repository_Interface $repository = null ) {
		$this->repository = $repository ?? new Client_Repository();
	}

	/**
	 * Create a new client record.
	 *
	 * @param array<string,mixed> $data Validated, sanitised field map.
	 * @return int|false New client ID or false on failure.
	 */
	public function createClient( array $data ): int|false {
		return $this->repository->create( $data );
	}

	/**
	 * Update an existing client record.
	 *
	 * @param int                 $id   Client ID.
	 * @param array<string,mixed> $data Validated, sanitised field map.
	 * @return bool
	 */
	public function updateClient( int $id, array $data ): bool {
		return $this->repository->update( $id, $data );
	}

	/**
	 * Soft-delete a client.
	 *
	 * @param int $id Client ID.
	 * @return bool
	 */
	public function deleteClient( int $id ): bool {
		return $this->repository->delete( $id );
	}

	/**
	 * Restore a soft-deleted client.
	 *
	 * @param int $id Client ID.
	 * @return bool
	 */
	public function restoreClient( int $id ): bool {
		return $this->repository->restore( $id );
	}

	/**
	 * Get a client by ID.
	 *
	 * @param int  $id              Client ID.
	 * @param bool $includeDeleted  Include soft-deleted records.
	 * @return Client_Model|null
	 */
	public function getClient( int $id, bool $includeDeleted = false ): ?Client_Model {
		return $this->repository->find( $id, $includeDeleted );
	}

	/**
	 * Find a client by email.
	 *
	 * @param string $email Email address.
	 * @return Client_Model|null
	 */
	public function findClientByEmail( string $email ): ?Client_Model {
		return $this->repository->findByEmail( $email );
	}

	/**
	 * Find a client by mobile number.
	 *
	 * @param string $mobile Mobile number.
	 * @return Client_Model|null
	 */
	public function findClientByMobile( string $mobile ): ?Client_Model {
		return $this->repository->findByMobile( $mobile );
	}

	/**
	 * Find a client by client code.
	 *
	 * @param string $code Client code.
	 * @return Client_Model|null
	 */
	public function findClientByCode( string $code ): ?Client_Model {
		return $this->repository->findByCode( $code );
	}

	/**
	 * Check whether a client already exists for a unique field.
	 *
	 * @param string $field      Column name.
	 * @param string $value      Field value.
	 * @param int    $excludeId  Exclude this client ID.
	 * @return bool
	 */
	public function clientExists( string $field, string $value, int $excludeId = 0 ): bool {
		return $this->repository->exists( $field, $value, $excludeId );
	}

	/**
	 * Return a paginated, filtered list of clients.
	 *
	 * @param array<string,mixed> $filters Query arguments.
	 * @return Client_Model[]
	 */
	public function listClients( array $filters = [] ): array {
		return $this->repository->getAll( $filters );
	}

	/**
	 * Search clients by text.
	 *
	 * @param string $term  Search term.
	 * @param int    $limit Max results.
	 * @return Client_Model[]
	 */
	public function searchClients( string $term, int $limit = 20 ): array {
		return $this->repository->search( $term, $limit );
	}

	/**
	 * Return the total count of clients matching filters.
	 *
	 * @param array<string,mixed> $filters Query arguments.
	 * @return int
	 */
	public function countClients( array $filters = [] ): int {
		return $this->repository->count( $filters );
	}

	/**
	 * Return distinct client cities for list filters.
	 *
	 * @return string[]
	 */
	public function getDistinctCities(): array {
		return $this->repository->getDistinctCities();
	}

	/**
	 * Return distinct client states for list filters.
	 *
	 * @return string[]
	 */
	public function getDistinctStates(): array {
		return $this->repository->getDistinctStates();
	}

	/**
	 * Validate client data and return error messages.
	 *
	 * @param array<string,mixed> $data      Sanitised data.
	 * @param int                 $client_id Existing client ID for edit checks.
	 * @return string[]
	 */
	public function validateClientData( array $data, int $client_id = 0 ): array {
		$errors = [];

		$clientName = trim( (string) ( $data['client_name'] ?? $data['name'] ?? '' ) );
		$mobile = trim( (string) ( $data['mobile'] ?? '' ) );
		$email = trim( (string) ( $data['email'] ?? '' ) );
		$alternateEmail = trim( (string) ( $data['alternate_email'] ?? '' ) );
		$gstNumber = strtoupper( trim( (string) ( $data['gst_number'] ?? '' ) ) );
		$panNumber = strtoupper( trim( (string) ( $data['pan_number'] ?? '' ) ) );
		$status = trim( (string) ( $data['status'] ?? 'lead' ) );
		$clientType = trim( (string) ( $data['client_type'] ?? 'individual' ) );

		if ( '' === $clientName ) {
			$errors[] = __( 'Client name is required.', 'adorable-client-portal' );
		}

		if ( '' === $mobile ) {
			$errors[] = __( 'Mobile number is required.', 'adorable-client-portal' );
		} elseif ( ! preg_match( '/^[6-9]\d{9}$/', preg_replace( '/[\s\-\+]/', '', $mobile ) ) ) {
			$errors[] = __( 'Mobile number must be a valid 10-digit Indian number.', 'adorable-client-portal' );
		} elseif ( $this->repository->exists( 'mobile', $mobile, $client_id ) ) {
			$errors[] = __( 'This mobile number is already registered.', 'adorable-client-portal' );
		}

		if ( '' !== $email ) {
			if ( ! is_email( $email ) ) {
				$errors[] = __( 'Email address format is invalid.', 'adorable-client-portal' );
			} elseif ( $this->repository->exists( 'email', $email, $client_id ) ) {
				$errors[] = __( 'This email address is already registered.', 'adorable-client-portal' );
			}
		}

		if ( '' !== $alternateEmail && ! is_email( $alternateEmail ) ) {
			$errors[] = __( 'Alternate email address format is invalid.', 'adorable-client-portal' );
		}

		if ( '' !== $gstNumber ) {
			if ( ! preg_match( '/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/', $gstNumber ) ) {
				$errors[] = __( 'GST number format is invalid.', 'adorable-client-portal' );
			} elseif ( $this->repository->exists( 'gst_number', $gstNumber, $client_id ) ) {
				$errors[] = __( 'This GST number is already registered.', 'adorable-client-portal' );
			}
		}

		if ( '' !== $panNumber && ! preg_match( '/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/', $panNumber ) ) {
			$errors[] = __( 'PAN number format is invalid.', 'adorable-client-portal' );
		}

		if ( ! array_key_exists( $status, Constants::CLIENT_STATUSES ) ) {
			$errors[] = __( 'Invalid status selected.', 'adorable-client-portal' );
		}

		if ( ! array_key_exists( $clientType, Constants::CLIENT_TYPES ) ) {
			$errors[] = __( 'Invalid client type selected.', 'adorable-client-portal' );
		}

		if ( isset( $data['client_code'] ) && '' !== trim( (string) $data['client_code'] ) ) {
			$clientCode = trim( (string) $data['client_code'] );
			if ( $this->repository->exists( 'client_code', $clientCode, $client_id ) ) {
				$errors[] = __( 'This client code is already in use.', 'adorable-client-portal' );
			}
		}

		return $errors;
	}
}
