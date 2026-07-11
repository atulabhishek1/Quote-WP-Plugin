<?php
/**
 * Client model.
 *
 * Represents a single row from wp_ac_clients.
 * Provides typed getters, fluent setters, validation helpers,
 * and serialization methods.
 *
 * This is the canonical model used by the repository and service layers.
 * The legacy includes/class-client-model.php is kept for backward
 * compatibility with the dashboard repository but new code must use this class.
 *
 * @package AdorableClientPortal\Includes\Models
 */

declare( strict_types=1 );

namespace AdorableClientPortal\Includes\Models;

use AdorableClientPortal\Includes\Constants;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Client
 */
class Client {

	// -------------------------------------------------------------------------
	// Properties — match wp_ac_clients columns exactly
	// -------------------------------------------------------------------------

	private int    $id               = 0;
	private string $client_code      = '';
	private string $client_type      = 'individual';
	private string $company_name     = '';
	private string $client_name      = '';
	private string $primary_contact  = '';
	private string $secondary_contact = '';
	private string $email            = '';
	private string $alternate_email  = '';
	private string $mobile           = '';
	private string $alternate_mobile = '';
	private string $whatsapp_number  = '';
	private string $gst_number       = '';
	private string $pan_number       = '';
	private string $billing_address  = '';
	private string $site_address     = '';
	private string $city             = '';
	private string $state            = '';
	private string $country          = 'India';
	private string $pincode          = '';
	private string $lead_source      = '';
	private int    $assigned_sales   = 0;
	private int    $assigned_designer = 0;
	private string $status           = 'lead';
	private string $notes            = '';
	private int    $created_by       = 0;
	private int    $updated_by       = 0;
	private string $created_at       = '';
	private string $updated_at       = '';
	private ?string $deleted_at      = null;

	// Computed / joined fields populated by repository queries.
	private int   $projects_count = 0;
	private float $total_revenue  = 0.0;
	private float $pending_amount = 0.0;

	// -------------------------------------------------------------------------
	// Construction
	// -------------------------------------------------------------------------

	/**
	 * Hydrate from a raw database row.
	 *
	 * @param array<string,mixed> $row Raw associative array from wpdb.
	 */
	public function __construct( array $row = [] ) {
		if ( ! empty( $row ) ) {
			$this->hydrate( $row );
		}
	}

	/**
	 * Populate all properties from a raw DB row.
	 *
	 * @param array<string,mixed> $row Raw row.
	 * @return void
	 */
	private function hydrate( array $row ): void {
		$this->id                = (int) ( $row['id'] ?? 0 );
		$this->client_code       = (string) ( $row['client_code'] ?? '' );
		$this->client_type       = (string) ( $row['client_type'] ?? 'individual' );
		$this->company_name      = (string) ( $row['company_name'] ?? '' );
		$this->client_name       = (string) ( $row['client_name'] ?? '' );
		$this->primary_contact   = (string) ( $row['primary_contact'] ?? '' );
		$this->secondary_contact = (string) ( $row['secondary_contact'] ?? '' );
		$this->email             = (string) ( $row['email'] ?? '' );
		$this->alternate_email   = (string) ( $row['alternate_email'] ?? '' );
		$this->mobile            = (string) ( $row['mobile'] ?? '' );
		$this->alternate_mobile  = (string) ( $row['alternate_mobile'] ?? '' );
		$this->whatsapp_number   = (string) ( $row['whatsapp_number'] ?? '' );
		$this->gst_number        = (string) ( $row['gst_number'] ?? '' );
		$this->pan_number        = (string) ( $row['pan_number'] ?? '' );
		$this->billing_address   = (string) ( $row['billing_address'] ?? '' );
		$this->site_address      = (string) ( $row['site_address'] ?? '' );
		$this->city              = (string) ( $row['city'] ?? '' );
		$this->state             = (string) ( $row['state'] ?? '' );
		$this->country           = (string) ( $row['country'] ?? 'India' );
		$this->pincode           = (string) ( $row['pincode'] ?? '' );
		$this->lead_source       = (string) ( $row['lead_source'] ?? '' );
		$this->assigned_sales    = (int) ( $row['assigned_sales'] ?? 0 );
		$this->assigned_designer = (int) ( $row['assigned_designer'] ?? 0 );
		$this->status            = (string) ( $row['status'] ?? 'lead' );
		$this->notes             = (string) ( $row['notes'] ?? '' );
		$this->created_by        = (int) ( $row['created_by'] ?? 0 );
		$this->updated_by        = (int) ( $row['updated_by'] ?? 0 );
		$this->created_at        = (string) ( $row['created_at'] ?? '' );
		$this->updated_at        = (string) ( $row['updated_at'] ?? '' );
		$this->deleted_at        = isset( $row['deleted_at'] ) && '' !== $row['deleted_at']
			? (string) $row['deleted_at']
			: null;

		// Computed columns (from JOIN / sub-query).
		$this->projects_count = (int) ( $row['projects_count'] ?? 0 );
		$this->total_revenue  = (float) ( $row['total_revenue'] ?? 0.0 );
		$this->pending_amount = (float) ( $row['pending_amount'] ?? 0.0 );
	}

	// -------------------------------------------------------------------------
	// Getters
	// -------------------------------------------------------------------------

	public function getId(): int { return $this->id; } // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public function getClientCode(): string { return $this->client_code; } // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public function getClientType(): string { return $this->client_type; } // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public function getCompanyName(): string { return $this->company_name; } // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public function getClientName(): string { return $this->client_name; } // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public function getPrimaryContact(): string { return $this->primary_contact; } // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public function getSecondaryContact(): string { return $this->secondary_contact; } // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public function getEmail(): string { return $this->email; } // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public function getAlternateEmail(): string { return $this->alternate_email; } // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public function getMobile(): string { return $this->mobile; } // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public function getAlternateMobile(): string { return $this->alternate_mobile; } // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public function getWhatsappNumber(): string { return $this->whatsapp_number; } // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public function getGstNumber(): string { return $this->gst_number; } // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public function getPanNumber(): string { return $this->pan_number; } // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public function getBillingAddress(): string { return $this->billing_address; } // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public function getSiteAddress(): string { return $this->site_address; } // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public function getCity(): string { return $this->city; } // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public function getState(): string { return $this->state; } // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public function getCountry(): string { return $this->country; } // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public function getPincode(): string { return $this->pincode; } // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public function getLeadSource(): string { return $this->lead_source; } // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public function getAssignedSales(): int { return $this->assigned_sales; } // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public function getAssignedDesigner(): int { return $this->assigned_designer; } // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public function getStatus(): string { return $this->status; } // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public function getNotes(): string { return $this->notes; } // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public function getCreatedBy(): int { return $this->created_by; } // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public function getUpdatedBy(): int { return $this->updated_by; } // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public function getCreatedAt(): string { return $this->created_at; } // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public function getUpdatedAt(): string { return $this->updated_at; } // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public function getDeletedAt(): ?string { return $this->deleted_at; } // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public function getProjectsCount(): int { return $this->projects_count; } // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public function getTotalRevenue(): float { return $this->total_revenue; } // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public function getPendingAmount(): float { return $this->pending_amount; } // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid

	// -------------------------------------------------------------------------
	// Setters (fluent)
	// -------------------------------------------------------------------------

	public function setClientCode( string $v ): static { $this->client_code = $v; return $this; } // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public function setClientType( string $v ): static { $this->client_type = $v; return $this; } // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public function setCompanyName( string $v ): static { $this->company_name = $v; return $this; } // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public function setClientName( string $v ): static { $this->client_name = $v; return $this; } // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public function setPrimaryContact( string $v ): static { $this->primary_contact = $v; return $this; } // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public function setSecondaryContact( string $v ): static { $this->secondary_contact = $v; return $this; } // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public function setEmail( string $v ): static { $this->email = $v; return $this; } // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public function setAlternateEmail( string $v ): static { $this->alternate_email = $v; return $this; } // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public function setMobile( string $v ): static { $this->mobile = $v; return $this; } // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public function setAlternateMobile( string $v ): static { $this->alternate_mobile = $v; return $this; } // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public function setWhatsappNumber( string $v ): static { $this->whatsapp_number = $v; return $this; } // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public function setGstNumber( string $v ): static { $this->gst_number = $v; return $this; } // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public function setPanNumber( string $v ): static { $this->pan_number = $v; return $this; } // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public function setBillingAddress( string $v ): static { $this->billing_address = $v; return $this; } // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public function setSiteAddress( string $v ): static { $this->site_address = $v; return $this; } // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public function setCity( string $v ): static { $this->city = $v; return $this; } // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public function setState( string $v ): static { $this->state = $v; return $this; } // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public function setCountry( string $v ): static { $this->country = $v; return $this; } // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public function setPincode( string $v ): static { $this->pincode = $v; return $this; } // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public function setLeadSource( string $v ): static { $this->lead_source = $v; return $this; } // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public function setAssignedSales( int $v ): static { $this->assigned_sales = $v; return $this; } // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public function setAssignedDesigner( int $v ): static { $this->assigned_designer = $v; return $this; } // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public function setStatus( string $v ): static { $this->status = $v; return $this; } // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public function setNotes( string $v ): static { $this->notes = $v; return $this; } // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public function setCreatedBy( int $v ): static { $this->created_by = $v; return $this; } // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
	public function setUpdatedBy( int $v ): static { $this->updated_by = $v; return $this; } // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid

	// -------------------------------------------------------------------------
	// State helpers
	// -------------------------------------------------------------------------

	/**
	 * Whether this client has been soft-deleted.
	 *
	 * @return bool
	 */
	public function isDeleted(): bool { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		return null !== $this->deleted_at;
	}

	/**
	 * Whether this is a new (unsaved) client.
	 *
	 * @return bool
	 */
	public function isNew(): bool { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		return 0 === $this->id;
	}

	// -------------------------------------------------------------------------
	// Validation helpers
	// -------------------------------------------------------------------------

	/**
	 * Validate the email field format.
	 *
	 * @return bool
	 */
	public function isValidEmail(): bool { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		return '' === $this->email || false !== filter_var( $this->email, FILTER_VALIDATE_EMAIL );
	}

	/**
	 * Validate the alternate email field format.
	 *
	 * @return bool
	 */
	public function isValidAlternateEmail(): bool { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		return '' === $this->alternate_email || false !== filter_var( $this->alternate_email, FILTER_VALIDATE_EMAIL );
	}

	/**
	 * Validate Indian mobile number format (10 digits, starts with 6-9).
	 *
	 * @param string $number Mobile number to validate.
	 * @return bool
	 */
	public function isValidMobile( string $number = '' ): bool { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		$n = '' === $number ? $this->mobile : $number;
		return '' === $n || (bool) preg_match( '/^[6-9]\d{9}$/', preg_replace( '/[\s\-\+]/', '', $n ) );
	}

	/**
	 * Validate Indian GST number format (15 alphanumeric characters).
	 *
	 * @return bool
	 */
	public function isValidGst(): bool { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		return '' === $this->gst_number
			|| (bool) preg_match( '/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/', strtoupper( $this->gst_number ) );
	}

	/**
	 * Validate Indian PAN number format (AAAAA9999A).
	 *
	 * @return bool
	 */
	public function isValidPan(): bool { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		return '' === $this->pan_number
			|| (bool) preg_match( '/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/', strtoupper( $this->pan_number ) );
	}

	/**
	 * Validate that the status is one of the allowed values.
	 *
	 * @return bool
	 */
	public function isValidStatus(): bool { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		return array_key_exists( $this->status, Constants::CLIENT_STATUSES );
	}

	/**
	 * Validate that the client type is one of the allowed values.
	 *
	 * @return bool
	 */
	public function isValidClientType(): bool { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		return array_key_exists( $this->client_type, Constants::CLIENT_TYPES );
	}

	/**
	 * Run all validation rules and return an array of error messages.
	 * An empty array means the model is valid.
	 *
	 * @return string[]
	 */
	public function validate(): array {
		$errors = [];

		if ( '' === trim( $this->client_name ) ) {
			$errors[] = __( 'Client name is required.', 'adorable-client-portal' );
		}

		if ( '' === trim( $this->mobile ) ) {
			$errors[] = __( 'Mobile number is required.', 'adorable-client-portal' );
		} elseif ( ! $this->isValidMobile() ) {
			$errors[] = __( 'Mobile number format is invalid.', 'adorable-client-portal' );
		}

		if ( '' !== $this->email && ! $this->isValidEmail() ) {
			$errors[] = __( 'Email address format is invalid.', 'adorable-client-portal' );
		}

		if ( '' !== $this->alternate_email && ! $this->isValidAlternateEmail() ) {
			$errors[] = __( 'Alternate email address format is invalid.', 'adorable-client-portal' );
		}

		if ( '' !== $this->gst_number && ! $this->isValidGst() ) {
			$errors[] = __( 'GST number format is invalid.', 'adorable-client-portal' );
		}

		if ( '' !== $this->pan_number && ! $this->isValidPan() ) {
			$errors[] = __( 'PAN number format is invalid.', 'adorable-client-portal' );
		}

		if ( ! $this->isValidStatus() ) {
			$errors[] = __( 'Invalid client status.', 'adorable-client-portal' );
		}

		if ( ! $this->isValidClientType() ) {
			$errors[] = __( 'Invalid client type.', 'adorable-client-portal' );
		}

		return $errors;
	}

	// -------------------------------------------------------------------------
	// Display helpers
	// -------------------------------------------------------------------------

	/**
	 * Return the human-readable status label.
	 *
	 * @return string
	 */
	public function statusLabel(): string { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		return Constants::CLIENT_STATUSES[ $this->status ] ?? ucfirst( $this->status );
	}

	/**
	 * Return the human-readable client type label.
	 *
	 * @return string
	 */
	public function clientTypeLabel(): string { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		return Constants::CLIENT_TYPES[ $this->client_type ] ?? ucfirst( $this->client_type );
	}

	/**
	 * Return the human-readable lead source label.
	 *
	 * @return string
	 */
	public function leadSourceLabel(): string { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		return Constants::LEAD_SOURCES[ $this->lead_source ] ?? ucfirst( str_replace( '_', ' ', $this->lead_source ) );
	}

	/**
	 * Return two-letter initials for avatar placeholder.
	 *
	 * @return string
	 */
	public function initials(): string {
		$parts = explode( ' ', trim( $this->client_name ) );
		$init  = '';

		foreach ( array_slice( $parts, 0, 2 ) as $part ) {
			$init .= strtoupper( mb_substr( $part, 0, 1 ) );
		}

		return $init ?: '?';
	}

	/**
	 * Return the display name — company name if company type, else client name.
	 *
	 * @return string
	 */
	public function displayName(): string { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		if ( 'company' === $this->client_type && '' !== $this->company_name ) {
			return $this->company_name;
		}

		return $this->client_name;
	}

	/**
	 * Return a formatted created_at date using the site's date format.
	 *
	 * @return string
	 */
	public function formattedCreatedAt(): string { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		if ( '' === $this->created_at || '0000-00-00 00:00:00' === $this->created_at ) {
			return '';
		}

		return wp_date( get_option( 'date_format' ), strtotime( $this->created_at ) ) ?: '';
	}

	// -------------------------------------------------------------------------
	// Serialization
	// -------------------------------------------------------------------------

	/**
	 * Return a plain array suitable for wpdb insert/update.
	 * Excludes computed columns and the primary key.
	 *
	 * @return array<string,mixed>
	 */
	public function toArray(): array { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		return [
			'client_code'       => $this->client_code,
			'client_type'       => $this->client_type,
			'company_name'      => $this->company_name,
			'client_name'       => $this->client_name,
			'primary_contact'   => $this->primary_contact,
			'secondary_contact' => $this->secondary_contact,
			'email'             => $this->email,
			'alternate_email'   => $this->alternate_email,
			'mobile'            => $this->mobile,
			'alternate_mobile'  => $this->alternate_mobile,
			'whatsapp_number'   => $this->whatsapp_number,
			'gst_number'        => $this->gst_number,
			'pan_number'        => $this->pan_number,
			'billing_address'   => $this->billing_address,
			'site_address'      => $this->site_address,
			'city'              => $this->city,
			'state'             => $this->state,
			'country'           => $this->country,
			'pincode'           => $this->pincode,
			'lead_source'       => $this->lead_source,
			'assigned_sales'    => $this->assigned_sales,
			'assigned_designer' => $this->assigned_designer,
			'status'            => $this->status,
			'notes'             => $this->notes,
			'created_by'        => $this->created_by,
			'updated_by'        => $this->updated_by,
		];
	}

	/**
	 * Return a JSON-serialisable array (includes id and computed fields).
	 *
	 * @return array<string,mixed>
	 */
	public function toJson(): array { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		return array_merge(
			[ 'id' => $this->id ],
			$this->toArray(),
			[
				'created_at'     => $this->created_at,
				'updated_at'     => $this->updated_at,
				'deleted_at'     => $this->deleted_at,
				'projects_count' => $this->projects_count,
				'total_revenue'  => $this->total_revenue,
				'pending_amount' => $this->pending_amount,
				'status_label'   => $this->statusLabel(),
				'display_name'   => $this->displayName(),
				'initials'       => $this->initials(),
			]
		);
	}
}
