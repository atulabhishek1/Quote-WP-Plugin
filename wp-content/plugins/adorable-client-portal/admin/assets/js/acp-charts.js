/**
 * Adorable Client Portal — Charts Module
 * Reusable Chart.js factory. Called by acp-dashboard.js.
 */
( function ( global ) {
	'use strict';

	const AcpCharts = {};

	const cfg = ( global.acpDashboard && global.acpDashboard.chartColors ) ? global.acpDashboard.chartColors : {
		gold: '#C8A96A', navy: '#2F4858', success: '#10b981',
		warning: '#f59e0b', danger: '#ef4444', info: '#3b82f6',
		purple: '#8b5cf6', muted: '#94a3b8',
	};

	const currency = ( global.acpDashboard && global.acpDashboard.currency ) ? global.acpDashboard.currency : '₹';

	function applyDefaults() {
		if ( typeof Chart === 'undefined' ) return;
		Chart.defaults.font.family  = "'Inter', -apple-system, sans-serif";
		Chart.defaults.font.size    = 12;
		Chart.defaults.color        = '#64748b';
		Chart.defaults.plugins.legend.labels.usePointStyle    = true;
		Chart.defaults.plugins.legend.labels.pointStyleWidth  = 8;
		Chart.defaults.plugins.legend.labels.padding          = 16;
		Chart.defaults.plugins.legend.labels.boxHeight        = 8;
	}

	function hideSkeleton( canvasId ) {
		const skeletonId = canvasId + 'Skeleton';
		const skeleton   = document.getElementById( skeletonId );
		const canvas     = document.getElementById( canvasId );
		if ( skeleton ) skeleton.setAttribute( 'hidden', '' );
		if ( canvas )   canvas.removeAttribute( 'hidden' );
	}

	function parseAttr( canvas, attr ) {
		try {
			return JSON.parse( canvas.dataset[ attr ] || 'null' );
		} catch ( e ) {
			return null;
		}
	}

	/**
	 * Build the monthly revenue line chart.
	 */
	AcpCharts.initRevenue = function ( canvasId ) {
		const canvas = document.getElementById( canvasId );
		if ( ! canvas ) return;

		const raw = parseAttr( canvas, 'chartData' );
		if ( ! raw || ! raw.length ) return;

		const labels   = raw.map( function ( r ) {
			const parts = r.month.split( '-' );
			return new Date( parts[0], parts[1] - 1 ).toLocaleDateString( undefined, { month: 'short', year: '2-digit' } );
		} );
		const revenues = raw.map( function ( r ) { return r.revenue; } );

		hideSkeleton( canvasId );

		new Chart( canvas, {
			type: 'line',
			data: {
				labels: labels,
				datasets: [ {
					label:                'Revenue',
					data:                 revenues,
					borderColor:          cfg.gold,
					backgroundColor:      'rgba(200,169,106,0.08)',
					borderWidth:          2.5,
					pointRadius:          4,
					pointHoverRadius:     6,
					pointBackgroundColor: cfg.gold,
					pointBorderColor:     '#fff',
					pointBorderWidth:     2,
					fill:                 true,
					tension:              0.4,
				} ],
			},
			options: {
				responsive:          true,
				maintainAspectRatio: false,
				interaction:         { mode: 'index', intersect: false },
				plugins: {
					legend: { display: false },
					tooltip: {
						backgroundColor: '#1e293b',
						titleColor:      '#f1f5f9',
						bodyColor:       '#94a3b8',
						borderColor:     '#334155',
						borderWidth:     1,
						padding:         12,
						callbacks: {
							label: function ( ctx ) {
								return ' ' + currency + ctx.parsed.y.toLocaleString();
							},
						},
					},
				},
				scales: {
					x: {
						grid:  { display: false },
						ticks: { color: '#94a3b8' },
					},
					y: {
						beginAtZero: true,
						grid:        { color: 'rgba(0,0,0,0.04)' },
						ticks: {
							color: '#94a3b8',
							callback: function ( val ) {
								if ( val >= 100000 ) return currency + ( val / 100000 ).toFixed( 1 ) + 'L';
								if ( val >= 1000 )   return currency + ( val / 1000 ).toFixed( 0 ) + 'K';
								return currency + val;
							},
						},
					},
				},
			},
		} );
	};

	/**
	 * Build a doughnut chart from a status-distribution object.
	 *
	 * @param {string}   canvasId
	 * @param {string[]} colorList
	 * @param {Object}   labelMap  slug → label
	 */
	AcpCharts.initDoughnut = function ( canvasId, colorList, labelMap ) {
		const canvas = document.getElementById( canvasId );
		if ( ! canvas ) return;

		const raw = parseAttr( canvas, 'chartData' );
		if ( ! raw ) return;

		const labels = Object.keys( raw ).map( function ( k ) { return labelMap[ k ] || k; } );
		const data   = Object.values( raw );

		hideSkeleton( canvasId );

		new Chart( canvas, {
			type: 'doughnut',
			data: {
				labels:   labels,
				datasets: [ {
					data:            data,
					backgroundColor: colorList,
					borderColor:     '#ffffff',
					borderWidth:     3,
					hoverOffset:     6,
				} ],
			},
			options: {
				responsive:          true,
				maintainAspectRatio: false,
				cutout:              '68%',
				plugins: {
					legend: {
						position: 'bottom',
						labels:   { color: '#64748b', padding: 12 },
					},
					tooltip: {
						backgroundColor: '#1e293b',
						titleColor:      '#f1f5f9',
						bodyColor:       '#94a3b8',
						borderColor:     '#334155',
						borderWidth:     1,
						padding:         12,
					},
				},
			},
		} );
	};

	AcpCharts.applyDefaults = applyDefaults;

	global.AcpCharts = AcpCharts;

} )( window );
