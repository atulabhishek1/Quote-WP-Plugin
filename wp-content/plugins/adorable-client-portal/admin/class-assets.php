<?php
/**
 * Admin asset enqueuing.
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
 * Class Assets
 *
 * Enqueues all CSS and JavaScript for the admin area.
 * Assets are only loaded on plugin pages to avoid conflicts.
 */
final class Assets {

	/**
	 * All plugin admin page hook suffixes.
	 *
	 * @var string[]
	 */
	private const PLUGIN_PAGES = [
		'toplevel_page_adorable-crm',
		'adorable-crm_page_adorable-clients',
		'adorable-crm_page_adorable-projects',
		'adorable-crm_page_adorable-quotations',
		'adorable-crm_page_adorable-payments',
		'adorable-crm_page_adorable-gallery',
		'adorable-crm_page_adorable-reports',
		'adorable-crm_page_adorable-settings',
		'adorable-crm_page_adorable-dev-tools',
	];

	/**
	 * Enqueue scripts and styles for the current admin page.
	 *
	 * @param string $hook_suffix Current admin page hook suffix.
	 */
	public function enqueue( string $hook_suffix ): void {
		if ( ! in_array( $hook_suffix, self::PLUGIN_PAGES, true ) ) {
			return;
		}

		$this->enqueue_styles( $hook_suffix );
		$this->enqueue_scripts( $hook_suffix );
	}

	/**
	 * Enqueue all CSS files in dependency order.
	 *
	 * @param string $hook_suffix Current admin page hook suffix.
	 */
	private function enqueue_styles( string $hook_suffix ): void {
		$css = ACP_URL . 'admin/assets/css/';
		$v   = ACP_VERSION;

		// Google Fonts — Inter.
		wp_enqueue_style(
			'acp-google-fonts',
			'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap',
			[],
			null // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
		);

		// 1. Design tokens — must load first.
		wp_enqueue_style( 'acp-variables', $css . 'acp-variables.css', [ 'acp-google-fonts' ], $v );

		// 2. Layout shell.
		wp_enqueue_style( 'acp-layout', $css . 'acp-layout.css', [ 'acp-variables' ], $v );

		// 3. Sidebar.
		wp_enqueue_style( 'acp-sidebar', $css . 'acp-sidebar.css', [ 'acp-layout' ], $v );

		// 4. Topbar.
		wp_enqueue_style( 'acp-topbar', $css . 'acp-topbar.css', [ 'acp-layout' ], $v );

		// 5. Cards.
		wp_enqueue_style( 'acp-cards', $css . 'acp-cards.css', [ 'acp-layout' ], $v );

		// 6. Dashboard page styles.
		wp_enqueue_style( 'acp-dashboard', $css . 'acp-dashboard.css', [ 'acp-cards' ], $v );

		// 7. Responsive — always last.
		wp_enqueue_style( 'acp-responsive', $css . 'acp-responsive.css', [ 'acp-dashboard' ], $v );
	}

	/**
	 * Enqueue all JavaScript files.
	 *
	 * @param string $hook_suffix Current admin page hook suffix.
	 */
	private function enqueue_scripts( string $hook_suffix ): void {
		$js = ACP_URL . 'admin/assets/js/';
		$v  = ACP_VERSION;

		// Chart.js from CDN — only on dashboard.
		if ( 'toplevel_page_adorable-crm' === $hook_suffix ) {
			wp_enqueue_script(
				'chartjs',
				'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js',
				[],
				'4.4.0',
				true
			);
		}

		// Sidebar module.
		wp_enqueue_script( 'acp-sidebar-js', $js . 'acp-sidebar.js', [], $v, true );

		// Notifications module.
		wp_enqueue_script( 'acp-notifications-js', $js . 'acp-notifications.js', [], $v, true );

		// Search module.
		wp_enqueue_script( 'acp-search-js', $js . 'acp-search.js', [], $v, true );

		// Base admin (dark mode, dropdowns).
		wp_enqueue_script( 'acp-admin', $js . 'acp-admin.js', [ 'acp-sidebar-js', 'acp-notifications-js', 'acp-search-js' ], $v, true );

		// Dashboard page scripts.
		if ( 'toplevel_page_adorable-crm' === $hook_suffix ) {
			// Charts module.
			wp_enqueue_script( 'acp-charts-js', $js . 'acp-charts.js', [ 'chartjs' ], $v, true );

			// Dashboard boot.
			wp_enqueue_script( 'acp-dashboard-js', $js . 'acp-dashboard.js', [ 'acp-admin', 'acp-charts-js' ], $v, true );

			wp_localize_script(
				'acp-dashboard-js',
				'acpDashboard',
				[
					'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
					'nonce'       => wp_create_nonce( Constants::NONCE_AJAX ),
					'currency'    => get_option( 'acp_currency_symbol', '₹' ),
					'i18n'        => [
						'loading' => __( 'Loading…', 'adorable-client-portal' ),
						'noData'  => __( 'No data available', 'adorable-client-portal' ),
						'error'   => __( 'Something went wrong. Please try again.', 'adorable-client-portal' ),
					],
					'chartColors' => [
						'gold'    => '#C8A96A',
						'navy'    => '#2F4858',
						'success' => '#10b981',
						'warning' => '#f59e0b',
						'danger'  => '#ef4444',
						'info'    => '#3b82f6',
						'purple'  => '#8b5cf6',
						'muted'   => '#94a3b8',
					],
				]
			);
		}

		// Clients page scripts.
		if ( 'adorable-crm_page_adorable-clients' === $hook_suffix ) {
			wp_enqueue_script( 'acp-clients-js', $js . 'acp-clients.js', [ 'acp-admin', 'jquery' ], $v, true );
			wp_localize_script(
				'acp-clients-js',
				'acpClients',
				[
					'ajaxUrl' => admin_url( 'admin-ajax.php' ),
					'nonce'   => wp_create_nonce( Constants::NONCE_AJAX ),
					'i18n'    => [
						'saving'        => __( 'Saving…', 'adorable-client-portal' ),
						'saveClient'    => __( 'Save Client', 'adorable-client-portal' ),
						'updateClient'  => __( 'Update Client', 'adorable-client-portal' ),
						'confirmDelete' => __( 'Are you sure you want to delete this client? This action can be undone.', 'adorable-client-portal' ),
						'deleting'      => __( 'Deleting…', 'adorable-client-portal' ),
						'success'       => __( 'Operation successful.', 'adorable-client-portal' ),
						'error'         => __( 'Something went wrong.', 'adorable-client-portal' ),
					],
				]
			);
		}
	}
}
