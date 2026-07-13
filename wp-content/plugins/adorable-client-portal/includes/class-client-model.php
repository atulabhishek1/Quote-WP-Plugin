<?php
/**
 * Client data model.
 *
 * Immutable value object that represents a single client record.
 * Constructed from a raw database row; all properties are typed and safe.
 *
 * @package AdorableClientPortal\Includes
 */

declare( strict_types=1 );

namespace AdorableClientPortal\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Client_Model
 */
final class Client_Model {

	public readonly int    $id;
	public readonly string $client_name;
	public readonly string $company_name;
	public readonly string $email;
	public readonly string $alternate_email;
	public readonly string $mobile;
	public readonly string $alternate_mobile;
	public readonly string $whatsapp;
	public readonly string $gst_number;
	public readonly string $pan_number;
	public readonly string $billing_address;
	public readonly string $site_address;
	public readonly string $city;
	public readonly string $state;
	public readonly string $country;
	public readonly string $pincode;
	public readonly string $lead_source;
	public readonly int    $assigned_salesperson;
	public readonly int    $assigned_designer;
	public readonly string $status;
	public readonly string $tags;
	public readonly string $notes;
	public readonly int    $is_deleted;
	public readonly string $created_at;
	public readonly string $updated_at;

	// Computed / joined fields (optional, populated by repository joins).
	public readonly int    $projects_count;
	public readonly float  $total_revenue;
	public readonly float  $pending_amount;
	
	public readonly string $client_code;
	public readonly string $client_type;

	/**
	 * Construct from a raw DB row (array or object).
	 *
	 * @param array<string,mixed> $row Raw database row.
	 */
	public function __construct( array $row ) {
		$this->id                   = (int) ( $row['id'] ?? 0 );
		$this->client_name          = (string) ( $row['client_name'] ?? '' );
		
		$this->client_code = (string) ( $row['client_code'] ?? '' );
		$this->client_type = (string) ( $row['client_type'] ?? '' );

		$this->company_name         = (string) ( $row['company_name'] ?? '' );
		$this->email                = (string) ( $row['email'] ?? '' );
		$this->alternate_email      = (string) ( $row['alternate_email'] ?? '' );
		$this->mobile               = (string) ( $row['mobile'] ?? '' );
		$this->alternate_mobile     = (string) ( $row['alternate_mobile'] ?? '' );
		$this->whatsapp 			= (string) ( $row['whatsapp_number'] ?? '' );
		$this->gst_number           = (string) ( $row['gst_number'] ?? '' );
		$this->pan_number           = (string) ( $row['pan_number'] ?? '' );
		$this->billing_address      = (string) ( $row['billing_address'] ?? '' );
		$this->site_address         = (string) ( $row['site_address'] ?? '' );
		$this->city                 = (string) ( $row['city'] ?? '' );
		$this->state                = (string) ( $row['state'] ?? '' );
		$this->country              = (string) ( $row['country'] ?? 'India' );
		$this->pincode              = (string) ( $row['pincode'] ?? '' );
		$this->lead_source          = (string) ( $row['lead_source'] ?? '' );
		$this->assigned_salesperson = (int) ( $row['assigned_sales'] ?? 0 );
		$this->assigned_designer    = (int) ( $row['assigned_designer'] ?? 0 );
		$this->status               = (string) ( $row['status'] ?? 'lead' );
		$this->tags                 = (string) ( $row['tags'] ?? '' );
		$this->notes                = (string) ( $row['notes'] ?? '' );
		$this->is_deleted           = (int) ( $row['is_deleted'] ?? 0 );
		$this->created_at           = (string) ( $row['created_at'] ?? '' );
		$this->updated_at           = (string) ( $row['updated_at'] ?? '' );
		$this->projects_count       = (int) ( $row['projects_count'] ?? 0 );
		$this->total_revenue        = (float) ( $row['total_revenue'] ?? 0.0 );
		$this->pending_amount       = (float) ( $row['pending_amount'] ?? 0.0 );
	}

	/**
	 * Return the display label for the client's status.
	 *
	 * @return string
	 */
	public function status_label(): string {
		return Constants::CLIENT_STATUSES[ $this->status ] ?? ucfirst( $this->status );
	}

	/**
	 * Return the display label for the lead source.
	 *
	 * @return string
	 */
	public function lead_source_label(): string {
		return Constants::LEAD_SOURCES[ $this->lead_source ] ?? ucfirst( str_replace( '_', ' ', $this->lead_source ) );
	}

	/**
	 * Return tags as an array.
	 *
	 * @return string[]
	 */
	public function tags_array(): array {
		if ( '' === $this->tags ) {
			return [];
		}

		return array_filter( array_map( 'trim', explode( ',', $this->tags ) ) );
	}

	/**
	 * Return a human-readable formatted created date.
	 *
	 * @return string
	 */
	public function formatted_date(): string {
		if ( '' === $this->created_at ) {
			return '';
		}

		return wp_date( get_option( 'date_format' ), strtotime( $this->created_at ) ) ?: '';
	}

	/**
	 * Return initials for avatar placeholder.
	 *
	 * @return string
	 */

	public function initials(): string {
		$parts = explode( ' ', trim( $this->client_name ) );

		$init = '';

		foreach ( array_slice( $parts, 0, 2 ) as $part ) {
			$init .= strtoupper( mb_substr( $part, 0, 1 ) );
		}

		return $init ?: '?';
	}

}
