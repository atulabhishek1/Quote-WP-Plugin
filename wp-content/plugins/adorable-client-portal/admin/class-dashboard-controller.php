<?php
/**
 * Dashboard page controller.
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
 * Class Dashboard_Controller
 *
 * Handles capability checks, data collection, and view rendering
 * for the main dashboard page.
 */
final class Dashboard_Controller {

	/**
	 * Render the dashboard page.
	 * Called by WordPress as the menu page callback.
	 */
	public static function render(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'adorable-client-portal' ) );
		}

		$data = self::collect_data();

		// Make data available to the view via extracted variables.
		extract( $data, EXTR_SKIP ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract

		include ACP_PATH . 'admin/views/dashboard.php';
	}

	/**
	 * Collect all data required by the dashboard view.
	 *
	 * @return array<string, mixed>
	 */
	private static function collect_data(): array {
		$repo         = new Dashboard_Repository();
		$current_user = wp_get_current_user();
		$currency     = get_option( 'acp_currency_symbol', '₹' );

		// Statistics.
		$stats = [
			'total_clients'          => $repo->get_total_clients(),
			'running_projects'       => $repo->get_running_projects(),
			'completed_projects'     => $repo->get_completed_projects(),
			'pending_quotations'     => $repo->get_quotes_by_status( 'draft' ) + $repo->get_quotes_by_status( 'sent' ),
			'approved_quotations'    => $repo->get_quotes_by_status( 'approved' ),
			'total_revenue'          => $repo->get_total_revenue(),
			'pending_payments'       => $repo->get_pending_payments_amount(),
			'upcoming_installations' => $repo->get_upcoming_installations(),
		];

		// Chart data — serialised to JSON for JS consumption.
		$monthly_revenue = $repo->get_monthly_revenue();
		$chart_data      = [
			'monthly_revenue'    => wp_json_encode( $monthly_revenue ),
			'projects_status'    => wp_json_encode( $repo->get_projects_status_distribution() ),
			'quotes_status'      => wp_json_encode( $repo->get_quotes_status_distribution() ),
			'payments_status'    => wp_json_encode( $repo->get_payments_status_distribution() ),
		];

		// Recent activity.
		$recent = [
			'activity' => $repo->get_recent_activity( 8 ),
			'clients'  => $repo->get_recent_clients( 5 ),
			'projects' => $repo->get_recent_projects( 5 ),
		];

		return compact( 'stats', 'chart_data', 'recent', 'current_user', 'currency' );
	}
}
