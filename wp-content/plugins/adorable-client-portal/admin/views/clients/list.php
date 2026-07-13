<?php
/**
 * Clients list view.
 *
 * Variables injected by Clients_Controller::render_list():
 *
 * @var \AdorableClientPortal\Includes\Client_Model[] $clients
 * @var int      $total
 * @var int      $total_pages
 * @var int      $paged
 * @var string[] $cities
 * @var string[] $states
 * @var array    $statuses
 * @var string   $nonce
 * @var array    $args
 * @var \WP_User $current_user
 *
 * @package AdorableClientPortal\Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_page = 'adorable-clients';
$base_url     = admin_url( 'admin.php?page=adorable-clients' );
$search       = $args['search'] ?? '';
$filter_status = $args['status'] ?? '';
$filter_city  = $args['city'] ?? '';
$filter_state = $args['state'] ?? '';
$orderby      = $args['orderby'] ?? 'created_at';
$order        = $args['order'] ?? 'DESC';

/**
 * Build a sortable column header URL.
 *
 * @param string $col Column key.
 * @return string URL.
 */
$sort_url = static function ( string $col ) use ( $base_url, $orderby, $order, $args ): string {
	$new_order = ( $col === $orderby && 'ASC' === $order ) ? 'DESC' : 'ASC';
	return add_query_arg( array_merge( $args, [ 'orderby' => $col, 'order' => $new_order, 'paged' => 1 ] ), $base_url );
};

$sort_icon = static function ( string $col ) use ( $orderby, $order ): string {
	if ( $col !== $orderby ) {
		return '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="12" y1="5" x2="12" y2="19"/><polyline points="19 12 12 19 5 12"/></svg>';
	}
	return 'ASC' === $order
		? '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="18 15 12 9 6 15"/></svg>'
		: '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg>';
};
?>
<div class="acp-wrap" id="acpApp" data-theme="light">

	<?php include ACP_PATH . 'admin/views/partials/sidebar.php'; ?>

	<div class="acp-main" id="acpMain">

		<?php include ACP_PATH . 'admin/views/partials/header.php'; ?>

		<main class="acp-content" id="acpContent" role="main">

			<!-- Page Header -->
			<div class="acp-page-header">
				<div class="acp-page-header__text">
					<h1 class="acp-page-header__title"><?php esc_html_e( 'Clients', 'adorable-client-portal' ); ?></h1>
					<p class="acp-page-header__subtitle">
						<?php
						printf(
							/* translators: %d: total client count */
							esc_html__( '%d total clients', 'adorable-client-portal' ),
							(int) $total
						);
						?>
					</p>
				</div>
				<div class="acp-page-header__actions">
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=adorable-clients&action=new' ) ); ?>" class="acp-btn acp-btn--primary">
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
						<?php esc_html_e( 'Add Client', 'adorable-client-portal' ); ?>
					</a>
					<a href="<?php echo esc_url( wp_nonce_url( add_query_arg( array_filter( [ 's' => $search, 'status' => $filter_status, 'city' => $filter_city, 'state' => $filter_state ] ), admin_url( 'admin-ajax.php?action=acp_export_clients' ) ), \AdorableClientPortal\Includes\Constants::NONCE_AJAX, 'nonce' ) ); ?>" class="acp-btn acp-btn--secondary" id="acpExportBtn">
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
						<?php esc_html_e( 'Export CSV', 'adorable-client-portal' ); ?>
					</a>
				</div>
			</div>

			<!-- Filters Bar -->
			<div class="acp-card acp-clients-filters">
				<div class="acp-card__body acp-card__body--sm">
					<form method="get" action="<?php echo esc_url( $base_url ); ?>" class="acp-filters-form" id="acpFiltersForm">
						<input type="hidden" name="page" value="adorable-clients">

						<div class="acp-filters-row">
							<!-- Live Search -->
							<div class="acp-filter-search">
								<svg class="acp-filter-search__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
								<input
									type="search"
									name="s"
									id="acpClientSearch"
									class="acp-filter-search__input"
									value="<?php echo esc_attr( $search ); ?>"
									placeholder="<?php esc_attr_e( 'Search by name, mobile, email, GST, PAN…', 'adorable-client-portal' ); ?>"
									autocomplete="off"
								>
							</div>

							<!-- Status Filter -->
							<select name="status" class="acp-filter-select" id="acpStatusFilter">
								<option value=""><?php esc_html_e( 'All Statuses', 'adorable-client-portal' ); ?></option>
								<?php foreach ( $statuses as $key => $label ) : ?>
									<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $filter_status, $key ); ?>>
										<?php echo esc_html( $label ); ?>
									</option>
								<?php endforeach; ?>
							</select>

							<!-- City Filter -->
							<?php if ( ! empty( $cities ) ) : ?>
								<select name="city" class="acp-filter-select">
									<option value=""><?php esc_html_e( 'All Cities', 'adorable-client-portal' ); ?></option>
									<?php foreach ( $cities as $city ) : ?>
										<option value="<?php echo esc_attr( $city ); ?>" <?php selected( $filter_city, $city ); ?>>
											<?php echo esc_html( $city ); ?>
										</option>
									<?php endforeach; ?>
								</select>
							<?php endif; ?>

							<!-- State Filter -->
							<?php if ( ! empty( $states ) ) : ?>
								<select name="state" class="acp-filter-select">
									<option value=""><?php esc_html_e( 'All States', 'adorable-client-portal' ); ?></option>
									<?php foreach ( $states as $state ) : ?>
										<option value="<?php echo esc_attr( $state ); ?>" <?php selected( $filter_state, $state ); ?>>
											<?php echo esc_html( $state ); ?>
										</option>
									<?php endforeach; ?>
								</select>
							<?php endif; ?>

							<button type="submit" class="acp-btn acp-btn--secondary acp-btn--sm">
								<?php esc_html_e( 'Filter', 'adorable-client-portal' ); ?>
							</button>

							<?php if ( $search || $filter_status || $filter_city || $filter_state ) : ?>
								<a href="<?php echo esc_url( $base_url ); ?>" class="acp-btn acp-btn--ghost acp-btn--sm">
									<?php esc_html_e( 'Clear', 'adorable-client-portal' ); ?>
								</a>
							<?php endif; ?>
						</div>
					</form>
				</div>
			</div>

			<!-- Bulk Actions Bar -->
			<div class="acp-bulk-bar" id="acpBulkBar" hidden>
				<span class="acp-bulk-bar__count" id="acpBulkCount">0 <?php esc_html_e( 'selected', 'adorable-client-portal' ); ?></span>
				<div class="acp-bulk-bar__actions">
					<select id="acpBulkAction" class="acp-filter-select">
						<option value=""><?php esc_html_e( 'Bulk Action', 'adorable-client-portal' ); ?></option>
						<option value="delete"><?php esc_html_e( 'Delete', 'adorable-client-portal' ); ?></option>
						<?php foreach ( $statuses as $key => $label ) : ?>
							<option value="<?php echo esc_attr( $key ); ?>">
								<?php
								printf(
									/* translators: %s: status label */
									esc_html__( 'Mark as %s', 'adorable-client-portal' ),
									esc_html( $label )
								);
								?>
							</option>
						<?php endforeach; ?>
					</select>
					<button type="button" class="acp-btn acp-btn--secondary acp-btn--sm" id="acpBulkApply">
						<?php esc_html_e( 'Apply', 'adorable-client-portal' ); ?>
					</button>
					<button type="button" class="acp-btn acp-btn--ghost acp-btn--sm" id="acpBulkClear">
						<?php esc_html_e( 'Clear selection', 'adorable-client-portal' ); ?>
					</button>
				</div>
			</div>

			<!-- Clients Table -->
			<div class="acp-card acp-clients-table-card">
				<?php if ( empty( $clients ) ) : ?>
					<div class="acp-empty">
						<svg class="acp-empty__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
						<p class="acp-empty__title"><?php esc_html_e( 'No clients found', 'adorable-client-portal' ); ?></p>
						<p class="acp-empty__desc">
							<?php if ( $search || $filter_status ) : ?>
								<?php esc_html_e( 'Try adjusting your search or filters.', 'adorable-client-portal' ); ?>
							<?php else : ?>
								<?php esc_html_e( 'Add your first client to get started.', 'adorable-client-portal' ); ?>
							<?php endif; ?>
						</p>
						<?php if ( ! $search && ! $filter_status ) : ?>
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=adorable-clients&action=new' ) ); ?>" class="acp-btn acp-btn--primary">
								<?php esc_html_e( 'Add First Client', 'adorable-client-portal' ); ?>
							</a>
						<?php endif; ?>
					</div>
				<?php else : ?>
					<div class="acp-table-wrapper">
						<table class="acp-table" id="acpClientsTable">
							<thead>
								<tr>
									<th class="acp-table__th acp-table__th--check">
										<input type="checkbox" id="acpSelectAll" class="acp-checkbox" aria-label="<?php esc_attr_e( 'Select all', 'adorable-client-portal' ); ?>">
									</th>
									<th class="acp-table__th">
										<a href="<?php echo esc_url( $sort_url( 'client_name' ) ); ?>" class="acp-table__sort-link">
											<?php esc_html_e( 'Client', 'adorable-client-portal' ); ?>
											<?php echo $sort_icon( 'client_name' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
										</a>
									</th>
									<th class="acp-table__th">
										<a href="<?php echo esc_url( $sort_url( 'company_name' ) ); ?>" class="acp-table__sort-link">
											<?php esc_html_e( 'Company', 'adorable-client-portal' ); ?>
											<?php echo $sort_icon( 'company_name' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
										</a>
									</th>
									<th class="acp-table__th"><?php esc_html_e( 'Mobile', 'adorable-client-portal' ); ?></th>
									<th class="acp-table__th"><?php esc_html_e( 'Email', 'adorable-client-portal' ); ?></th>
									<th class="acp-table__th acp-table__th--center">
										<a href="<?php echo esc_url( $sort_url( 'projects_count' ) ); ?>" class="acp-table__sort-link">
											<?php esc_html_e( 'Projects', 'adorable-client-portal' ); ?>
											<?php echo $sort_icon( 'projects_count' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
										</a>
									</th>
									<th class="acp-table__th">
										<a href="<?php echo esc_url( $sort_url( 'status' ) ); ?>" class="acp-table__sort-link">
											<?php esc_html_e( 'Status', 'adorable-client-portal' ); ?>
											<?php echo $sort_icon( 'status' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
										</a>
									</th>
									<th class="acp-table__th">
										<a href="<?php echo esc_url( $sort_url( 'updated_at' ) ); ?>" class="acp-table__sort-link">
											<?php esc_html_e( 'Last Updated', 'adorable-client-portal' ); ?>
											<?php echo $sort_icon( 'updated_at' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
										</a>
									</th>
									<th class="acp-table__th acp-table__th--actions"><?php esc_html_e( 'Actions', 'adorable-client-portal' ); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $clients as $client ) : ?>
									<tr class="acp-table__row" data-id="<?php echo esc_attr( (string) $client->id ); ?>">
										<td class="acp-table__td acp-table__td--check">
											<input
												type="checkbox"
												class="acp-checkbox acp-row-check"
												value="<?php echo esc_attr( (string) $client->id ); ?>"
												aria-label="<?php echo esc_attr( sprintf( __( 'Select %s', 'adorable-client-portal' ), $client->client_name ) ); ?>"
											>
										</td>
										<td class="acp-table__td">
											<div class="acp-client-cell">
												<div class="acp-client-avatar" aria-hidden="true">
													<?php echo esc_html( $client->initials() ); ?>
												</div>
												<div class="acp-client-info">
													<a href="<?php echo esc_url( admin_url( 'admin.php?page=adorable-clients&action=view&id=' . $client->id ) ); ?>" class="acp-client-name">
														<?php echo esc_html( $client->client_name ); ?>
													</a>
													<?php if ( '' !== $client->city ) : ?>
														<span class="acp-client-location">
															<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
															<?php echo esc_html( $client->city ); ?>
														</span>
													<?php endif; ?>
												</div>
											</div>
										</td>
										<td class="acp-table__td acp-table__td--muted">
											<?php echo '' !== $client->company_name ? esc_html( $client->company_name ) : '<span class="acp-text-muted">—</span>'; ?>
										</td>
										<td class="acp-table__td">
											<a href="tel:<?php echo esc_attr( $client->mobile ); ?>" class="acp-link">
												<?php echo esc_html( $client->mobile ); ?>
											</a>
										</td>
										<td class="acp-table__td acp-table__td--muted">
											<?php if ( '' !== $client->email ) : ?>
												<a href="mailto:<?php echo esc_attr( $client->email ); ?>" class="acp-link">
													<?php echo esc_html( $client->email ); ?>
												</a>
											<?php else : ?>
												<span class="acp-text-muted">—</span>
											<?php endif; ?>
										</td>
										<td class="acp-table__td acp-table__td--center">
											<span class="acp-count-badge"><?php echo esc_html( (string) $client->projects_count ); ?></span>
										</td>
										<td class="acp-table__td">
											<span class="acp-badge acp-badge--<?php echo esc_attr( $client->status ); ?>">
												<?php echo esc_html( $client->status_label() ); ?>
											</span>
										</td>
										<td class="acp-table__td acp-table__td--muted">
											<?php
											if ( '' !== $client->updated_at && '0000-00-00 00:00:00' !== $client->updated_at ) {
												echo esc_html( wp_date( get_option( 'date_format' ), strtotime( $client->updated_at ) ) );
											} else {
												echo '<span class="acp-text-muted">—</span>';
											}
											?>
										</td>
										<td class="acp-table__td acp-table__td--actions">
											<div class="acp-row-actions">
												<a href="<?php echo esc_url( admin_url( 'admin.php?page=adorable-clients&action=view&id=' . $client->id ) ); ?>" class="acp-btn acp-btn--icon" title="<?php esc_attr_e( 'View', 'adorable-client-portal' ); ?>">
													<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
												</a>
												<a href="<?php echo esc_url( admin_url( 'admin.php?page=adorable-clients&action=edit&id=' . $client->id ) ); ?>" class="acp-btn acp-btn--icon" title="<?php esc_attr_e( 'Edit', 'adorable-client-portal' ); ?>">
													<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
												</a>
												<button
													type="button"
													class="acp-btn acp-btn--icon acp-btn--danger-hover acp-delete-client"
													data-id="<?php echo esc_attr( (string) $client->id ); ?>"
													data-name="<?php echo esc_attr( $client->client_name ); ?>"
													data-nonce="<?php echo esc_attr( $nonce ); ?>"
													title="<?php esc_attr_e( 'Delete', 'adorable-client-portal' ); ?>"
												>
													<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
												</button>
											</div>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>

						<!-- Pagination -->
						<?php if ( $total_pages > 1 ) : ?>
							<div class="acp-pagination">
								<span class="acp-pagination__info">
									<?php
									printf(
										/* translators: 1: current page, 2: total pages */
										esc_html__( 'Page %1$d of %2$d', 'adorable-client-portal' ),
										(int) $paged,
										(int) $total_pages
									);
									?>
								</span>
								<div class="acp-pagination__links">
									<?php if ( $paged > 1 ) : ?>
										<a href="<?php echo esc_url( add_query_arg( array_merge( $args, [ 'paged' => $paged - 1 ] ), $base_url ) ); ?>" class="acp-btn acp-btn--secondary acp-btn--sm">
											<?php esc_html_e( '← Prev', 'adorable-client-portal' ); ?>
										</a>
									<?php endif; ?>

									<?php
									$start = max( 1, $paged - 2 );
									$end   = min( $total_pages, $paged + 2 );
									for ( $p = $start; $p <= $end; $p++ ) :
										?>
										<a
											href="<?php echo esc_url( add_query_arg( array_merge( $args, [ 'paged' => $p ] ), $base_url ) ); ?>"
											class="acp-btn acp-btn--sm <?php echo $p === $paged ? 'acp-btn--primary' : 'acp-btn--secondary'; ?>"
										>
											<?php echo esc_html( (string) $p ); ?>
										</a>
									<?php endfor; ?>

									<?php if ( $paged < $total_pages ) : ?>
										<a href="<?php echo esc_url( add_query_arg( array_merge( $args, [ 'paged' => $paged + 1 ] ), $base_url ) ); ?>" class="acp-btn acp-btn--secondary acp-btn--sm">
											<?php esc_html_e( 'Next →', 'adorable-client-portal' ); ?>
										</a>
									<?php endif; ?>
								</div>
							</div>
						<?php endif; ?>
				<?php endif; ?>
			</div>

		</main>
	</div>
</div>

<!-- Delete Confirmation Modal -->
<div class="acp-modal-overlay" id="acpDeleteModal" hidden role="dialog" aria-modal="true" aria-labelledby="acpDeleteModalTitle">
	<div class="acp-modal">
		<div class="acp-modal__header">
			<h2 class="acp-modal__title" id="acpDeleteModalTitle"><?php esc_html_e( 'Delete Client', 'adorable-client-portal' ); ?></h2>
		</div>
		<div class="acp-modal__body">
			<p id="acpDeleteModalMsg"><?php esc_html_e( 'Are you sure you want to delete this client? This action can be undone.', 'adorable-client-portal' ); ?></p>
		</div>
		<div class="acp-modal__footer">
			<button type="button" class="acp-btn acp-btn--secondary" id="acpDeleteCancel"><?php esc_html_e( 'Cancel', 'adorable-client-portal' ); ?></button>
			<button type="button" class="acp-btn acp-btn--danger" id="acpDeleteConfirm"><?php esc_html_e( 'Delete', 'adorable-client-portal' ); ?></button>
		</div>
	</div>
</div>

<!-- Toast Container -->
<div class="acp-toast-container" id="acpToastContainer" aria-live="polite"></div>
