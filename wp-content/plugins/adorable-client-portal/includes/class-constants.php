<?php
/**
 * Plugin-wide constants.
 *
 * @package AdorableClientPortal\Includes
 */

declare( strict_types=1 );

namespace AdorableClientPortal\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Constants
 *
 * Central registry for all plugin constants.
 */
final class Constants {

	// Database table names (without prefix).
	public const TABLE_CLIENTS       = 'ac_clients';
	public const TABLE_PROJECTS      = 'ac_projects';
	public const TABLE_QUOTES        = 'ac_quotes';
	public const TABLE_QUOTE_ITEMS   = 'ac_quote_items';
	public const TABLE_PAYMENTS      = 'ac_payments';
	public const TABLE_GALLERY       = 'ac_gallery';
	public const TABLE_PROGRESS      = 'ac_progress';
	public const TABLE_DOCUMENTS     = 'ac_documents';
	public const TABLE_NOTES         = 'ac_notes';
	public const TABLE_ACTIVITY_LOGS = 'ac_activity_logs';

	// Option keys.
	public const OPTION_DB_VERSION      = 'acp_db_version';
	public const OPTION_SETTINGS        = 'acp_settings';
	public const OPTION_ACTIVATED       = 'acp_activated';

	// DB version.
	public const DB_VERSION = '1.0.0';

	// Capability slugs.
	public const CAP_MANAGE = 'manage_acp';
	public const CAP_VIEW   = 'view_acp';

	// Nonce actions.
	public const NONCE_ADMIN  = 'acp_admin_nonce';
	public const NONCE_AJAX   = 'acp_ajax_nonce';
	public const NONCE_CLIENT = 'acp_client_nonce';

	// Upload sub-directory.
	public const UPLOAD_DIR = 'adorable-client-portal';

	// Project statuses.
	public const PROJECT_STATUSES = [
		'lead'         => 'Lead',
		'design'       => 'Design',
		'approval'     => 'Approval',
		'production'   => 'Production',
		'dispatch'     => 'Dispatch',
		'installation' => 'Installation',
		'completed'    => 'Completed',
	];

	// Quote statuses.
	public const QUOTE_STATUSES = [
		'draft'    => 'Draft',
		'sent'     => 'Sent',
		'approved' => 'Approved',
		'rejected' => 'Rejected',
		'revised'  => 'Revised',
	];

	// Payment statuses.
	public const PAYMENT_STATUSES = [
		'pending'   => 'Pending',
		'partial'   => 'Partial',
		'paid'      => 'Paid',
		'refunded'  => 'Refunded',
	];

	// Prevent instantiation.
	private function __construct() {}
}
