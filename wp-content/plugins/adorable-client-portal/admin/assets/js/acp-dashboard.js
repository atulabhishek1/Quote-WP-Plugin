/**
 * Adorable Client Portal — Dashboard Boot
 * Initialises all charts and dashboard interactions.
 */
( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {
		if ( typeof Chart === 'undefined' || typeof AcpCharts === 'undefined' ) return;

		AcpCharts.applyDefaults();

		const c = ( window.acpDashboard && window.acpDashboard.chartColors ) ? window.acpDashboard.chartColors : {};

		// Monthly Revenue — line chart.
		AcpCharts.initRevenue( 'acpRevenueChart' );

		// Projects by Status — doughnut.
		AcpCharts.initDoughnut(
			'acpProjectsChart',
			[ c.gold, c.warning, c.purple, c.info, c.success, c.muted, c.danger ],
			{ lead: 'Lead', design: 'Design', approval: 'Approval', production: 'Production', dispatch: 'Dispatch', installation: 'Installation', completed: 'Completed' }
		);

		// Quotes by Status — doughnut.
		AcpCharts.initDoughnut(
			'acpQuotesChart',
			[ c.muted, c.info, c.success, c.danger, c.warning ],
			{ draft: 'Draft', sent: 'Sent', approved: 'Approved', rejected: 'Rejected', revised: 'Revised' }
		);

		// Payments by Status — doughnut.
		AcpCharts.initDoughnut(
			'acpPaymentsChart',
			[ c.warning, c.info, c.success, c.danger ],
			{ pending: 'Pending', partial: 'Partial', paid: 'Paid', refunded: 'Refunded' }
		);
	} );

} )();
