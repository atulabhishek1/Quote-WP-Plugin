<?php
/**
 * Dashboard charts partial.
 *
 * Variables available:
 *
 * @var array{monthly_revenue: string, projects_status: string, quotes_status: string, payments_status: string} $chart_data
 *
 * @package AdorableClientPortal\Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="acp-charts-grid">

	<!-- Monthly Revenue Chart -->
	<section class="acp-card acp-chart-card acp-chart-card--wide" aria-labelledby="acpRevenueChartTitle">

		<div class="acp-card__header">
			<h2 class="acp-card__title" id="acpRevenueChartTitle">
				<?php esc_html_e( 'Monthly Revenue', 'adorable-client-portal' ); ?>
			</h2>
			<div class="acp-chart-card__legend" aria-hidden="true">
				<span class="acp-chart-card__legend-dot acp-chart-card__legend-dot--primary"></span>
				<span class="acp-chart-card__legend-label"><?php esc_html_e( 'Revenue', 'adorable-client-portal' ); ?></span>
			</div>
		</div>

		<div class="acp-card__body">
			<div class="acp-chart-card__wrap">
				<div class="acp-skeleton acp-skeleton--chart" id="acpRevenueChartSkeleton" aria-hidden="true"></div>
				<canvas
					id="acpRevenueChart"
					class="acp-chart"
					data-chart-type="line"
					data-chart-data="<?php echo esc_attr( $chart_data['monthly_revenue'] ); ?>"
					aria-label="<?php esc_attr_e( 'Monthly revenue chart', 'adorable-client-portal' ); ?>"
					role="img"
					hidden
				></canvas>
			</div>
		</div>

	</section>

	<!-- Projects by Status -->
	<section class="acp-card acp-chart-card" aria-labelledby="acpProjectsChartTitle">

		<div class="acp-card__header">
			<h2 class="acp-card__title" id="acpProjectsChartTitle">
				<?php esc_html_e( 'Projects by Status', 'adorable-client-portal' ); ?>
			</h2>
		</div>

		<div class="acp-card__body">
			<div class="acp-chart-card__wrap acp-chart-card__wrap--doughnut">
				<div class="acp-skeleton acp-skeleton--chart-sm" id="acpProjectsChartSkeleton" aria-hidden="true"></div>
				<canvas
					id="acpProjectsChart"
					class="acp-chart"
					data-chart-type="doughnut"
					data-chart-data="<?php echo esc_attr( $chart_data['projects_status'] ); ?>"
					aria-label="<?php esc_attr_e( 'Projects by status chart', 'adorable-client-portal' ); ?>"
					role="img"
					hidden
				></canvas>
			</div>
		</div>

	</section>

	<!-- Quotation Status -->
	<section class="acp-card acp-chart-card" aria-labelledby="acpQuotesChartTitle">

		<div class="acp-card__header">
			<h2 class="acp-card__title" id="acpQuotesChartTitle">
				<?php esc_html_e( 'Quotation Status', 'adorable-client-portal' ); ?>
			</h2>
		</div>

		<div class="acp-card__body">
			<div class="acp-chart-card__wrap acp-chart-card__wrap--doughnut">
				<div class="acp-skeleton acp-skeleton--chart-sm" id="acpQuotesChartSkeleton" aria-hidden="true"></div>
				<canvas
					id="acpQuotesChart"
					class="acp-chart"
					data-chart-type="doughnut"
					data-chart-data="<?php echo esc_attr( $chart_data['quotes_status'] ); ?>"
					aria-label="<?php esc_attr_e( 'Quotation status chart', 'adorable-client-portal' ); ?>"
					role="img"
					hidden
				></canvas>
			</div>
		</div>

	</section>

	<!-- Payment Status -->
	<section class="acp-card acp-chart-card" aria-labelledby="acpPaymentsChartTitle">

		<div class="acp-card__header">
			<h2 class="acp-card__title" id="acpPaymentsChartTitle">
				<?php esc_html_e( 'Payment Status', 'adorable-client-portal' ); ?>
			</h2>
		</div>

		<div class="acp-card__body">
			<div class="acp-chart-card__wrap acp-chart-card__wrap--doughnut">
				<div class="acp-skeleton acp-skeleton--chart-sm" id="acpPaymentsChartSkeleton" aria-hidden="true"></div>
				<canvas
					id="acpPaymentsChart"
					class="acp-chart"
					data-chart-type="doughnut"
					data-chart-data="<?php echo esc_attr( $chart_data['payments_status'] ); ?>"
					aria-label="<?php esc_attr_e( 'Payment status chart', 'adorable-client-portal' ); ?>"
					role="img"
					hidden
				></canvas>
			</div>
		</div>

	</section>

</div>
