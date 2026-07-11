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

	// DB version — increment this when a new migration is added.
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

	// Client statuses.
	public const CLIENT_STATUSES = [
		'lead'      => 'Lead',
		'prospect'  => 'Prospect',
		'active'    => 'Active',
		'on_hold'   => 'On Hold',
		'completed' => 'Completed',
		'inactive'  => 'Inactive',
		'archived'  => 'Archived',
	];

	// Lead sources.
	public const LEAD_SOURCES = [
		'referral'       => 'Referral',
		'instagram'      => 'Instagram',
		'facebook'       => 'Facebook',
		'google'         => 'Google',
		'website'        => 'Website',
		'walk_in'        => 'Walk In',
		'exhibition'     => 'Exhibition',
		'houzz'          => 'Houzz',
		'justdial'       => 'JustDial',
		'other'          => 'Other',
	];

	// Additional table names.
	public const TABLE_CLIENT_NOTES     = 'ac_client_notes';
	public const TABLE_CLIENT_DOCUMENTS = 'ac_client_documents';
	public const TABLE_CLIENT_CONTACTS  = 'ac_client_contacts';
	public const TABLE_CLIENT_ADDRESSES = 'ac_client_addresses';

	// Nonce actions for clients.
	public const NONCE_CLIENT_SAVE   = 'acp_client_save';
	public const NONCE_CLIENT_DELETE = 'acp_client_delete';
	public const NONCE_CLIENT_BULK   = 'acp_client_bulk';
	public const NONCE_CLIENT_IMPORT = 'acp_client_import';

	// Client type values.
	public const CLIENT_TYPES = [
		'individual' => 'Individual',
		'company'    => 'Company',
	];

	// Client code prefix.
	public const CLIENT_CODE_PREFIX = 'AC';
	public const CLIENT_CODE_PAD    = 6;

	// Prevent instantiation.
	private function __construct() {}
}
