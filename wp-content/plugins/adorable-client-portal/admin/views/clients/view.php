<?php
/**
 * Client detail view template.
 *
 * Variables injected by Clients_Controller::render_view():
 *
 * @var \AdorableClientPortal\Includes\Client_Model $client
 * @var array    $notes
 * @var array    $docs
 * @var array    $activity
 * @var array    $statuses
 * @var string   $nonce
 * @var \WP_User $current_user
 *
 * @package AdorableClientPortal\Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_page = 'adorable-clients';
$base_url     = admin_url( 'admin.php?page=adorable-clients' );
?>
<div class="acp-wrap" id="acpApp" data-theme="light">

	<?php include ACP_PATH . 'admin/views/partials/sidebar.php'; ?>

	<div class="acp-main" id="acpMain">

		<?php include ACP_PATH . 'admin/views/partials/header.php'; ?>

		<main class="acp-content" id="acpContent" role="main">

			<!-- Page Header -->
			<div class="acp-page-header">
				<div class="acp-page-header__text">
					<div class="acp-row acp-row--align-center acp-row--gap-md">
						<a href="<?php echo esc_url( $base_url ); ?>" class="acp-btn acp-btn--icon" title="<?php esc_attr_e( 'Back to list', 'adorable-client-portal' ); ?>">
							<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
						</a>
						<div>
							<h1 class="acp-page-header__title"><?php echo esc_html( $client->name ); ?></h1>
							<p class="acp-page-header__subtitle">
								<?php echo esc_html( $client->client_code ); ?> &bull; 
								<span class="acp-badge acp-badge--<?php echo esc_attr( $client->status ); ?>">
									<?php echo esc_html( $client->status_label() ); ?>
								</span>
							</p>
						</div>
					</div>
				</div>
				<div class="acp-page-header__actions">
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=adorable-clients&action=edit&id=' . $client->id ) ); ?>" class="acp-btn acp-btn--primary">
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
						<?php esc_html_e( 'Edit Client', 'adorable-client-portal' ); ?>
					</a>
				</div>
			</div>

			<!-- Tabs Navigation -->
			<div class="acp-tabs-nav-container">
				<nav class="acp-tabs-nav" aria-label="<?php esc_attr_e( 'Client tabs', 'adorable-client-portal' ); ?>">
					<button type="button" class="acp-tabs-nav__btn acp-tabs-nav__btn--active" data-tab="overview">
						<?php esc_html_e( 'Overview', 'adorable-client-portal' ); ?>
					</button>
					<button type="button" class="acp-tabs-nav__btn" data-tab="notes">
						<?php esc_html_e( 'Notes', 'adorable-client-portal' ); ?>
						<span class="acp-tabs-nav__badge" id="acpNotesCount"><?php echo count( $notes ); ?></span>
					</button>
					<button type="button" class="acp-tabs-nav__btn" data-tab="documents">
						<?php esc_html_e( 'Documents', 'adorable-client-portal' ); ?>
						<span class="acp-tabs-nav__badge"><?php echo count( $docs ); ?></span>
					</button>
					<button type="button" class="acp-tabs-nav__btn" data-tab="activity">
						<?php esc_html_e( 'Activity Log', 'adorable-client-portal' ); ?>
					</button>
				</nav>
			</div>

			<!-- Tab Content Areas -->
			<div class="acp-tabs-content">

				<!-- Overview Tab -->
				<div class="acp-tab-pane acp-tab-pane--active" id="tab_overview" role="tabpanel">
					<div class="acp-grid acp-grid--3">

						<!-- Contact Details -->
						<div class="acp-card">
							<div class="acp-card__header">
								<h3 class="acp-card__title"><?php esc_html_e( 'Contact Info', 'adorable-client-portal' ); ?></h3>
							</div>
							<div class="acp-card__body">
								<ul class="acp-meta-list">
									<li class="acp-meta-item">
										<span class="acp-meta-item__label"><?php esc_html_e( 'Mobile', 'adorable-client-portal' ); ?></span>
										<span class="acp-meta-item__value">
											<a href="tel:<?php echo esc_attr( $client->mobile ); ?>" class="acp-link"><?php echo esc_html( $client->mobile ); ?></a>
										</span>
									</li>
									<?php if ( '' !== $client->alternate_mobile ) : ?>
										<li class="acp-meta-item">
											<span class="acp-meta-item__label"><?php esc_html_e( 'Alternate Mobile', 'adorable-client-portal' ); ?></span>
											<span class="acp-meta-item__value"><?php echo esc_html( $client->alternate_mobile ); ?></span>
										</li>
									<?php endif; ?>
									<?php if ( '' !== $client->whatsapp ) : ?>
										<li class="acp-meta-item">
											<span class="acp-meta-item__label"><?php esc_html_e( 'WhatsApp', 'adorable-client-portal' ); ?></span>
											<span class="acp-meta-item__value"><?php echo esc_html( $client->whatsapp ); ?></span>
										</li>
									<?php endif; ?>
									<li class="acp-meta-item">
										<span class="acp-meta-item__label"><?php esc_html_e( 'Email', 'adorable-client-portal' ); ?></span>
										<span class="acp-meta-item__value">
											<?php if ( '' !== $client->email ) : ?>
												<a href="mailto:<?php echo esc_attr( $client->email ); ?>" class="acp-link"><?php echo esc_html( $client->email ); ?></a>
											<?php else : ?>
												<span class="acp-text-muted">&mdash;</span>
											<?php endif; ?>
										</span>
									</li>
									<?php if ( '' !== $client->alternate_email ) : ?>
										<li class="acp-meta-item">
											<span class="acp-meta-item__label"><?php esc_html_e( 'Alternate Email', 'adorable-client-portal' ); ?></span>
											<span class="acp-meta-item__value"><?php echo esc_html( $client->alternate_email ); ?></span>
										</li>
									<?php endif; ?>
								</ul>
							</div>
						</div>

						<!-- Core Properties -->
						<div class="acp-card">
							<div class="acp-card__header">
								<h3 class="acp-card__title"><?php esc_html_e( 'Client Profile', 'adorable-client-portal' ); ?></h3>
							</div>
							<div class="acp-card__body">
								<ul class="acp-meta-list">
									<li class="acp-meta-item">
										<span class="acp-meta-item__label"><?php esc_html_e( 'Client Type', 'adorable-client-portal' ); ?></span>
										<span class="acp-meta-item__value"><?php echo esc_html( ucfirst( $client->client_type ) ); ?></span>
									</li>
									<?php if ( 'company' === $client->client_type && '' !== $client->company_name ) : ?>
										<li class="acp-meta-item">
											<span class="acp-meta-item__label"><?php esc_html_e( 'Company Name', 'adorable-client-portal' ); ?></span>
											<span class="acp-meta-item__value"><?php echo esc_html( $client->company_name ); ?></span>
										</li>
									<?php endif; ?>
									<li class="acp-meta-item">
										<span class="acp-meta-item__label"><?php esc_html_e( 'Lead Source', 'adorable-client-portal' ); ?></span>
										<span class="acp-meta-item__value"><?php echo esc_html( $client->lead_source_label() ); ?></span>
									</li>
									<li class="acp-meta-item">
										<span class="acp-meta-item__label"><?php esc_html_e( 'Salesperson', 'adorable-client-portal' ); ?></span>
										<span class="acp-meta-item__value">
											<?php
											if ( $client->assigned_salesperson > 0 ) {
												$sales = get_userdata( $client->assigned_salesperson );
												echo esc_html( $sales ? $sales->display_name : __( 'Unknown', 'adorable-client-portal' ) );
											} else {
												echo '<span class="acp-text-muted">' . esc_html__( 'Unassigned', 'adorable-client-portal' ) . '</span>';
											}
											?>
										</span>
									</li>
									<li class="acp-meta-item">
										<span class="acp-meta-item__label"><?php esc_html_e( 'Designer', 'adorable-client-portal' ); ?></span>
										<span class="acp-meta-item__value">
											<?php
											if ( $client->assigned_designer > 0 ) {
												$designer = get_userdata( $client->assigned_designer );
												echo esc_html( $designer ? $designer->display_name : __( 'Unknown', 'adorable-client-portal' ) );
											} else {
												echo '<span class="acp-text-muted">' . esc_html__( 'Unassigned', 'adorable-client-portal' ) . '</span>';
											}
											?>
										</span>
									</li>
								</ul>
							</div>
						</div>

						<!-- Tax Details -->
						<div class="acp-card">
							<div class="acp-card__header">
								<h3 class="acp-card__title"><?php esc_html_e( 'Tax Details', 'adorable-client-portal' ); ?></h3>
							</div>
							<div class="acp-card__body">
								<ul class="acp-meta-list">
									<li class="acp-meta-item">
										<span class="acp-meta-item__label"><?php esc_html_e( 'GST Number', 'adorable-client-portal' ); ?></span>
										<span class="acp-meta-item__value"><?php echo '' !== $client->gst_number ? esc_html( $client->gst_number ) : '<span class="acp-text-muted">&mdash;</span>'; ?></span>
									</li>
									<li class="acp-meta-item">
										<span class="acp-meta-item__label"><?php esc_html_e( 'PAN Number', 'adorable-client-portal' ); ?></span>
										<span class="acp-meta-item__value"><?php echo '' !== $client->pan_number ? esc_html( $client->pan_number ) : '<span class="acp-text-muted">&mdash;</span>'; ?></span>
									</li>
									<li class="acp-meta-item">
										<span class="acp-meta-item__label"><?php esc_html_e( 'Registered On', 'adorable-client-portal' ); ?></span>
										<span class="acp-meta-item__value"><?php echo esc_html( $client->formatted_date() ); ?></span>
									</li>
								</ul>
							</div>
						</div>

					</div><!-- /.acp-grid -->

					<div class="acp-grid acp-grid--2 acp-margin-top-lg">
						<!-- Addresses -->
						<div class="acp-card">
							<div class="acp-card__header">
								<h3 class="acp-card__title"><?php esc_html_e( 'Addresses', 'adorable-client-portal' ); ?></h3>
							</div>
							<div class="acp-card__body">
								<div class="acp-address-block acp-margin-bottom-md">
									<h4 class="acp-address-block__title"><?php esc_html_e( 'Billing Address', 'adorable-client-portal' ); ?></h4>
									<p class="acp-address-block__text">
										<?php
										if ( '' !== $client->billing_address ) {
											echo nl2br( esc_html( $client->billing_address ) ) . '<br>';
											echo esc_html( implode( ', ', array_filter( [ $client->city, $client->state, $client->pincode, $client->country ] ) ) );
										} else {
											echo '<span class="acp-text-muted">' . esc_html__( 'No billing address saved.', 'adorable-client-portal' ) . '</span>';
										}
										?>
									</p>
								</div>
								<div class="acp-address-block">
									<h4 class="acp-address-block__title"><?php esc_html_e( 'Site / Project Address', 'adorable-client-portal' ); ?></h4>
									<p class="acp-address-block__text">
										<?php
										if ( '' !== $client->site_address ) {
											echo nl2br( esc_html( $client->site_address ) );
										} else {
											echo '<span class="acp-text-muted">' . esc_html__( 'No site address saved.', 'adorable-client-portal' ) . '</span>';
										}
										?>
									</p>
								</div>
							</div>
						</div>

						<!-- Tags & General Notes -->
						<div class="acp-card">
							<div class="acp-card__header">
								<h3 class="acp-card__title"><?php esc_html_e( 'Tags & Details', 'adorable-client-portal' ); ?></h3>
							</div>
							<div class="acp-card__body">
								<div class="acp-tags-container acp-margin-bottom-md">
									<h4 class="acp-meta-item__label acp-margin-bottom-xs"><?php esc_html_e( 'Tags', 'adorable-client-portal' ); ?></h4>
									<?php if ( ! empty( $client->tags_array() ) ) : ?>
										<div class="acp-tags-list">
											<?php foreach ( $client->tags_array() as $tag ) : ?>
												<span class="acp-badge acp-badge--tag"><?php echo esc_html( $tag ); ?></span>
											<?php endforeach; ?>
										</div>
									<?php else : ?>
										<span class="acp-text-muted"><?php esc_html_e( 'No tags assigned.', 'adorable-client-portal' ); ?></span>
									<?php endif; ?>
								</div>
								<div class="acp-notes-block">
									<h4 class="acp-meta-item__label acp-margin-bottom-xs"><?php esc_html_e( 'Internal Notes Summary', 'adorable-client-portal' ); ?></h4>
									<p class="acp-notes-block__text">
										<?php echo '' !== $client->notes ? nl2br( esc_html( $client->notes ) ) : '<span class="acp-text-muted">' . esc_html__( 'No summary notes.', 'adorable-client-portal' ) . '</span>'; ?>
									</p>
								</div>
							</div>
						</div>
					</div>
				</div><!-- /#tab_overview -->

				<!-- Notes Tab -->
				<div class="acp-tab-pane" id="tab_notes" role="tabpanel">
					<div class="acp-grid acp-grid--form-split">
						<div class="acp-card">
							<div class="acp-card__header">
								<h3 class="acp-card__title"><?php esc_html_e( 'Add Internal Note', 'adorable-client-portal' ); ?></h3>
							</div>
							<div class="acp-card__body">
								<form id="acpAddNoteForm" novalidate>
									<input type="hidden" name="action" value="acp_add_note">
									<input type="hidden" name="nonce" value="<?php echo esc_attr( $nonce ); ?>">
									<input type="hidden" name="client_id" value="<?php echo esc_attr( (string) $client->id ); ?>">
									<div class="acp-form-field">
										<textarea name="note" id="acpNoteText" class="acp-form-textarea" rows="4" placeholder="<?php esc_attr_e( 'Type your internal note here…', 'adorable-client-portal' ); ?>" required></textarea>
									</div>
									<button type="submit" class="acp-btn acp-btn--primary acp-btn--full acp-margin-top-sm" id="acpSubmitNote">
										<?php esc_html_e( 'Add Note', 'adorable-client-portal' ); ?>
									</button>
								</form>
							</div>
						</div>

						<div class="acp-card">
							<div class="acp-card__header">
								<h3 class="acp-card__title"><?php esc_html_e( 'Notes History', 'adorable-client-portal' ); ?></h3>
							</div>
							<div class="acp-card__body">
								<div class="acp-notes-timeline" id="acpNotesTimeline">
									<?php if ( empty( $notes ) ) : ?>
										<p class="acp-text-muted" id="acpNoNotesMsg"><?php esc_html_e( 'No notes saved yet.', 'adorable-client-portal' ); ?></p>
									<?php else : ?>
										<?php foreach ( $notes as $note ) : ?>
											<div class="acp-timeline-item" data-id="<?php echo esc_attr( (string) $note['id'] ); ?>">
												<div class="acp-timeline-item__header">
													<span class="acp-timeline-item__author"><?php echo esc_html( $note['author_name'] ?: __( 'System', 'adorable-client-portal' ) ); ?></span>
													<span class="acp-timeline-item__date"><?php echo esc_html( wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $note['created_at'] ) ) ); ?></span>
												</div>
												<div class="acp-timeline-item__body">
													<?php echo nl2br( esc_html( $note['note'] ) ); ?>
												</div>
											</div>
										<?php endforeach; ?>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</div>
				</div><!-- /#tab_notes -->

				<!-- Documents Tab -->
				<div class="acp-tab-pane" id="tab_documents" role="tabpanel">
					<div class="acp-card">
						<div class="acp-card__header">
							<h3 class="acp-card__title"><?php esc_html_e( 'Client Documents', 'adorable-client-portal' ); ?></h3>
						</div>
						<div class="acp-card__body">
							<?php if ( empty( $docs ) ) : ?>
								<div class="acp-empty">
									<svg class="acp-empty__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
									<p class="acp-empty__title"><?php esc_html_e( 'No documents uploaded', 'adorable-client-portal' ); ?></p>
									<p class="acp-empty__desc"><?php esc_html_e( 'Documents related to this client will appear here.', 'adorable-client-portal' ); ?></p>
								</div>
							<?php else : ?>
								<div class="acp-table-wrapper">
									<table class="acp-table">
										<thead>
											<tr>
												<th class="acp-table__th"><?php esc_html_e( 'Document Name', 'adorable-client-portal' ); ?></th>
												<th class="acp-table__th"><?php esc_html_e( 'Type', 'adorable-client-portal' ); ?></th>
												<th class="acp-table__th"><?php esc_html_e( 'Size', 'adorable-client-portal' ); ?></th>
												<th class="acp-table__th"><?php esc_html_e( 'Uploaded On', 'adorable-client-portal' ); ?></th>
												<th class="acp-table__th acp-table__th--actions"><?php esc_html_e( 'Actions', 'adorable-client-portal' ); ?></th>
											</tr>
										</thead>
										<tbody>
											<?php foreach ( $docs as $doc ) : ?>
												<tr class="acp-table__row">
													<td class="acp-table__td">
														<a href="<?php echo esc_url( $doc['file_url'] ); ?>" class="acp-link" target="_blank" rel="noopener">
															<?php echo esc_html( $doc['document_name'] ); ?>
														</a>
													</td>
													<td class="acp-table__td acp-table__td--muted"><?php echo esc_html( strtoupper( $doc['document_type'] ) ); ?></td>
													<td class="acp-table__td acp-table__td--muted"><?php echo esc_html( size_format( (int) $doc['file_size'] ) ); ?></td>
													<td class="acp-table__td acp-table__td--muted"><?php echo esc_html( wp_date( get_option( 'date_format' ), strtotime( $doc['created_at'] ) ) ); ?></td>
													<td class="acp-table__td acp-table__td--actions">
														<a href="<?php echo esc_url( $doc['file_url'] ); ?>" class="acp-btn acp-btn--icon" target="_blank" rel="noopener" title="<?php esc_attr_e( 'Download', 'adorable-client-portal' ); ?>">
															<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
														</a>
													</td>
												</tr>
											<?php endforeach; ?>
										</tbody>
									</table>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</div><!-- /#tab_documents -->

				<!-- Activity Log Tab -->
				<div class="acp-tab-pane" id="tab_activity" role="tabpanel">
					<div class="acp-card">
						<div class="acp-card__header">
							<h3 class="acp-card__title"><?php esc_html_e( 'Activity Timeline', 'adorable-client-portal' ); ?></h3>
						</div>
						<div class="acp-card__body">
							<?php if ( empty( $activity ) ) : ?>
								<p class="acp-text-muted"><?php esc_html_e( 'No activity logged yet.', 'adorable-client-portal' ); ?></p>
							<?php else : ?>
								<div class="acp-activity-timeline">
									<?php foreach ( $activity as $log ) : ?>
										<div class="acp-activity-item">
											<div class="acp-activity-item__header">
												<span class="acp-activity-item__action acp-badge acp-badge--<?php echo esc_attr( $log['action'] ); ?>">
													<?php echo esc_html( ucfirst( $log['action'] ) ); ?>
												</span>
												<span class="acp-activity-item__date"><?php echo esc_html( wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $log['created_at'] ) ) ); ?></span>
											</div>
											<p class="acp-activity-item__desc"><?php echo esc_html( $log['description'] ); ?></p>
											<div class="acp-activity-item__meta">
												<span class="acp-activity-item__user">
													<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
													<?php echo esc_html( $log['user_name'] ?: __( 'System', 'adorable-client-portal' ) ); ?>
												</span>
												<?php if ( '' !== $log['ip_address'] ) : ?>
													<span class="acp-activity-item__ip">
														&bull; <?php echo esc_html( $log['ip_address'] ); ?>
													</span>
												<?php endif; ?>
											</div>
										</div>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</div><!-- /#tab_activity -->

			</div><!-- /.acp-tabs-content -->

		</main>
	</div>
</div>

<!-- Toast Container -->
<div class="acp-toast-container" id="acpToastContainer" aria-live="polite"></div>
