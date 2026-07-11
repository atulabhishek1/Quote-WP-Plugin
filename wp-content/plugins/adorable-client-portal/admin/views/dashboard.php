<?php
/**
 * Dashboard page view.
 *
 * Variables injected by Dashboard_Controller::collect_data():
 *
 * @var array{
 *   total_clients: int,
 *   running_projects: int,
 *   completed_projects: int,
 *   pending_quotations: int,
 *   approved_quotations: int,
 *   total_revenue: float,
 *   pending_payments: float,
 *   upcoming_installations: int
 * } $stats
 * @var array{monthly_revenue: string, projects_status: string, quotes_status: string, payments_status: string} $chart_data
 * @var array{activity: array, clients: array, projects: array} $recent
 * @var \WP_User $current_user
 * @var string   $currency
 *
 * @package AdorableClientPortal\Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Format a monetary value.
 *
 * @param float  $amount   Amount.
 * @param string $symbol   Currency symbol.
 * @return string
 */
$format_currency = static function ( float $amount, string $symbol ): string {
	if ( $amount >= 10000000 ) {
		return $symbol . number_format( $amount / 10000000, 2 ) . 'Cr';
	}

	if ( $amount >= 100000 ) {
		return $symbol . number_format( $amount / 100000, 2 ) . 'L';
	}

	if ( $amount >= 1000 ) {
		return $symbol . number_format( $amount / 1000, 1 ) . 'K';
	}

	return $symbol . number_format( $amount, 0 );
};

// Build stat cards data.
$stat_cards = [
	[
		'title' => __( 'Total Clients', 'adorable-client-portal' ),
		'value' => number_format_i18n( $stats['total_clients'] ),
		'color' => 'primary',
		'link'  => admin_url( 'admin.php?page=adorable-clients' ),
		'icon'  => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
	],
	[
		'title' => __( 'Running Projects', 'adorable-client-portal' ),
		'value' => number_format_i18n( $stats['running_projects'] ),
		'color' => 'purple',
		'link'  => admin_url( 'admin.php?page=adorable-projects' ),
		'icon'  => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>',
	],
	[
		'title' => __( 'Completed Projects', 'adorable-client-portal' ),
		'value' => number_format_i18n( $stats['completed_projects'] ),
		'color' => 'success',
		'link'  => admin_url( 'admin.php?page=adorable-projects&status=completed' ),
		'icon'  => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>',
	],
	[
		'title' => __( 'Pending Quotations', 'adorable-client-portal' ),
		'value' => number_format_i18n( $stats['pending_quotations'] ),
		'color' => 'warning',
		'link'  => admin_url( 'admin.php?page=adorable-quotations&status=draft' ),
		'icon'  => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>',
	],
	[
		'title' => __( 'Approved Quotations', 'adorable-client-portal' ),
		'value' => number_format_i18n( $stats['approved_quotations'] ),
		'color' => 'info',
		'link'  => admin_url( 'admin.php?page=adorable-quotations&status=approved' ),
		'icon'  => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>',
	],
	[
		'title' => __( 'Total Revenue', 'adorable-client-portal' ),
		'value' => $format_currency( $stats['total_revenue'], $currency ),
		'color' => 'success',
		'link'  => admin_url( 'admin.php?page=adorable-payments' ),
		'icon'  => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>',
	],
	[
		'title' => __( 'Pending Payments', 'adorable-client-portal' ),
		'value' => $format_currency( $stats['pending_payments'], $currency ),
		'color' => 'danger',
		'link'  => admin_url( 'admin.php?page=adorable-payments&status=pending' ),
		'icon'  => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>',
	],
	[
		'title' => __( 'Upcoming Installations', 'adorable-client-portal' ),
		'value' => number_format_i18n( $stats['upcoming_installations'] ),
		'color' => 'info',
		'link'  => admin_url( 'admin.php?page=adorable-projects&status=installation' ),
		'icon'  => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>',
	],
];
?>
<div class="acp-wrap" id="acpApp" data-theme="light">

	<?php include ACP_PATH . 'admin/views/partials/sidebar.php'; ?>

	<div class="acp-main" id="acpMain">

		<?php include ACP_PATH . 'admin/views/partials/header.php'; ?>

		<main class="acp-content" id="acpContent" role="main">

			<!-- Page Title -->
			<div class="acp-page-header">
				<div class="acp-page-header__text">
					<h1 class="acp-page-header__title">
						<?php
						printf(
							/* translators: %s: user display name */
							esc_html__( 'Welcome back, %s 👋', 'adorable-client-portal' ),
							esc_html( $current_user->display_name )
						);
						?>
					</h1>
					<p class="acp-page-header__subtitle">
						<?php esc_html_e( "Here's what's happening with your projects today.", 'adorable-client-portal' ); ?>
					</p>
				</div>
				<div class="acp-page-header__actions">
					<a
						href="<?php echo esc_url( admin_url( 'admin.php?page=adorable-clients&action=new' ) ); ?>"
						class="acp-btn acp-btn--primary"
					>
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
						<?php esc_html_e( 'New Client', 'adorable-client-portal' ); ?>
					</a>
				</div>
			</div>

			<!-- Statistics Cards -->
			<section class="acp-stats-grid" aria-label="<?php esc_attr_e( 'Statistics overview', 'adorable-client-portal' ); ?>">
				<?php foreach ( $stat_cards as $card ) : ?>
					<?php include ACP_PATH . 'admin/views/partials/stat-card.php'; ?>
				<?php endforeach; ?>
			</section>

			<!-- Charts -->
			<?php include ACP_PATH . 'admin/views/partials/charts.php'; ?>

			<!-- Quick Actions + Recent Activity -->
			<div class="acp-bottom-grid">

				<?php include ACP_PATH . 'admin/views/partials/quick-actions.php'; ?>

				<div class="acp-bottom-grid__activity">
					<?php include ACP_PATH . 'admin/views/partials/recent-activity.php'; ?>
				</div>

			</div>

		</main>

	</div>

</div>
