<?php
/**
 * Client add / edit form view.
 *
 * Variables injected by Clients_Controller::render_form():
 *
 * @var \AdorableClientPortal\Includes\Client_Model|null $client  null for new client.
 * @var array    $statuses
 * @var array    $sources
 * @var string   $nonce
 * @var array    $users
 * @var \WP_User $current_user
 *
 * @package AdorableClientPortal\Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$is_edit      = null !== $client && $client->id > 0;
$page_title   = $is_edit ? __( 'Edit Client', 'adorable-client-portal' ) : __( 'Add New Client', 'adorable-client-portal' );
$client_id    = $is_edit ? $client->id : 0;
$current_page = 'adorable-clients';

/**
 * Helper: return field value from client or empty string.
 *
 * @param string $field Field name.
 * @return string
 */
$val = static function ( string $field ) use ( $client ): string {
	if ( null === $client ) {
		return '';
	}
	return (string) ( $client->$field ?? '' );
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
					<h1 class="acp-page-header__title"><?php echo esc_html( $page_title ); ?></h1>
					<?php if ( $is_edit ) : ?>
						<p class="acp-page-header__subtitle">
							<?php echo esc_html( $client->name ); ?>
							<?php if ( '' !== $client->company_name ) : ?>
								&mdash; <?php echo esc_html( $client->company_name ); ?>
							<?php endif; ?>
						</p>
					<?php endif; ?>
				</div>
				<div class="acp-page-header__actions">
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=adorable-clients' ) ); ?>" class="acp-btn acp-btn--secondary">
						<?php esc_html_e( '← Back to Clients', 'adorable-client-portal' ); ?>
					</a>
				</div>
			</div>

			<!-- Form -->
			<form id="acpClientForm" class="acp-client-form" novalidate>
				<input type="hidden" name="action" value="acp_save_client">
				<input type="hidden" name="nonce" value="<?php echo esc_attr( $nonce ); ?>">
				<input type="hidden" name="client_id" value="<?php echo esc_attr( (string) $client_id ); ?>">

				<div class="acp-form-layout">

					<!-- Left Column: Main Sections -->
					<div class="acp-form-main">

						<!-- Section 1: Basic Details -->
						<div class="acp-card acp-form-section">
							<div class="acp-card__header">
								<h2 class="acp-card__title">
									<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
									<?php esc_html_e( 'Basic Details', 'adorable-client-portal' ); ?>
								</h2>
							</div>
							<div class="acp-card__body">
								<div class="acp-form-grid acp-form-grid--2">
									<div class="acp-form-field acp-form-field--required">
										<label class="acp-form-label" for="client_type"><?php esc_html_e( 'Client Type', 'adorable-client-portal' ); ?></label>
										<select id="client_type" name="client_type" class="acp-form-select" required>
											<option value="individual" <?php selected( $val( 'client_type' ) ?: 'individual', 'individual' ); ?>><?php esc_html_e( 'Individual', 'adorable-client-portal' ); ?></option>
											<option value="company" <?php selected( $val( 'client_type' ), 'company' ); ?>><?php esc_html_e( 'Company', 'adorable-client-portal' ); ?></option>
										</select>
									</div>
									<div class="acp-form-field acp-form-field--required">
										<label class="acp-form-label" for="client_name"><?php esc_html_e( 'Client Name', 'adorable-client-portal' ); ?></label>
										<input type="text" id="client_name" name="client_name" class="acp-form-input" value="<?php echo esc_attr( $val( 'name' ) ); ?>" required autocomplete="name">
										<span class="acp-form-error" id="err_client_name"></span>
									</div>
									<div class="acp-form-field acp-form-company-field" id="acpCompanyField" <?php echo $val( 'client_type' ) === 'company' ? '' : 'style="display:none"'; ?>>
										<label class="acp-form-label" for="company_name"><?php esc_html_e( 'Company Name', 'adorable-client-portal' ); ?></label>
										<input type="text" id="company_name" name="company_name" class="acp-form-input" value="<?php echo esc_attr( $val( 'company_name' ) ); ?>" autocomplete="organization">
									</div>
									<div class="acp-form-field acp-form-company-field" id="acpPrimaryContactField" <?php echo $val( 'client_type' ) === 'company' ? '' : 'style="display:none"'; ?>>
										<label class="acp-form-label" for="primary_contact"><?php esc_html_e( 'Primary Contact Person', 'adorable-client-portal' ); ?></label>
										<input type="text" id="primary_contact" name="primary_contact" class="acp-form-input" value="<?php echo esc_attr( $val( 'primary_contact' ) ); ?>">
									</div>
									<div class="acp-form-field acp-form-company-field" id="acpSecondaryContactField" <?php echo $val( 'client_type' ) === 'company' ? '' : 'style="display:none"'; ?>>
										<label class="acp-form-label" for="secondary_contact"><?php esc_html_e( 'Secondary Contact Person', 'adorable-client-portal' ); ?></label>
										<input type="text" id="secondary_contact" name="secondary_contact" class="acp-form-input" value="<?php echo esc_attr( $val( 'secondary_contact' ) ); ?>">
									</div>
									<div class="acp-form-field">
										<label class="acp-form-label" for="lead_source"><?php esc_html_e( 'Lead Source', 'adorable-client-portal' ); ?></label>
										<select id="lead_source" name="lead_source" class="acp-form-select">
											<option value=""><?php esc_html_e( '— Select Source —', 'adorable-client-portal' ); ?></option>
											<?php foreach ( $sources as $key => $label ) : ?>
												<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $val( 'lead_source' ), $key ); ?>>
													<?php echo esc_html( $label ); ?>
												</option>
											<?php endforeach; ?>
										</select>
									</div>
									<div class="acp-form-field acp-form-field--required">
										<label class="acp-form-label" for="status"><?php esc_html_e( 'Status', 'adorable-client-portal' ); ?></label>
										<select id="status" name="status" class="acp-form-select" required>
											<?php foreach ( $statuses as $key => $label ) : ?>
												<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $val( 'status' ) ?: 'lead', $key ); ?>>
													<?php echo esc_html( $label ); ?>
												</option>
											<?php endforeach; ?>
										</select>
									</div>
								</div>
							</div>
						</div>

						<!-- Section 2: Contact Information -->
						<div class="acp-card acp-form-section">
							<div class="acp-card__header">
								<h2 class="acp-card__title">
									<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.18 2 2 0 0 1 3.6 1h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.6a16 16 0 0 0 6.29 6.29l.96-.96a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
									<?php esc_html_e( 'Contact Information', 'adorable-client-portal' ); ?>
								</h2>
							</div>
							<div class="acp-card__body">
								<div class="acp-form-grid acp-form-grid--2">
									<div class="acp-form-field acp-form-field--required">
										<label class="acp-form-label" for="mobile"><?php esc_html_e( 'Mobile', 'adorable-client-portal' ); ?></label>
										<input type="tel" id="mobile" name="mobile" class="acp-form-input acp-duplicate-check" data-field="mobile" data-exclude="<?php echo esc_attr( (string) $client_id ); ?>" value="<?php echo esc_attr( $val( 'mobile' ) ); ?>" required autocomplete="tel" maxlength="15">
										<span class="acp-form-error" id="err_mobile"></span>
									</div>
									<div class="acp-form-field">
										<label class="acp-form-label" for="alternate_mobile"><?php esc_html_e( 'Alternate Mobile', 'adorable-client-portal' ); ?></label>
										<input type="tel" id="alternate_mobile" name="alternate_mobile" class="acp-form-input" value="<?php echo esc_attr( $val( 'alternate_mobile' ) ); ?>" autocomplete="tel" maxlength="15">
									</div>
									<div class="acp-form-field">
										<label class="acp-form-label" for="whatsapp"><?php esc_html_e( 'WhatsApp Number', 'adorable-client-portal' ); ?></label>
										<input type="tel" id="whatsapp" name="whatsapp" class="acp-form-input" value="<?php echo esc_attr( $val( 'whatsapp' ) ); ?>" maxlength="15">
									</div>
									<div class="acp-form-field">
										<label class="acp-form-label" for="email"><?php esc_html_e( 'Email', 'adorable-client-portal' ); ?></label>
										<input type="email" id="email" name="email" class="acp-form-input acp-duplicate-check" data-field="email" data-exclude="<?php echo esc_attr( (string) $client_id ); ?>" value="<?php echo esc_attr( $val( 'email' ) ); ?>" autocomplete="email">
										<span class="acp-form-error" id="err_email"></span>
									</div>
									<div class="acp-form-field">
										<label class="acp-form-label" for="alternate_email"><?php esc_html_e( 'Alternate Email', 'adorable-client-portal' ); ?></label>
										<input type="email" id="alternate_email" name="alternate_email" class="acp-form-input" value="<?php echo esc_attr( $val( 'alternate_email' ) ); ?>" autocomplete="email">
									</div>
								</div>
							</div>
						</div>

						<!-- Section 3: Billing Address -->
						<div class="acp-card acp-form-section">
							<div class="acp-card__header">
								<h2 class="acp-card__title">
									<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
									<?php esc_html_e( 'Billing Address', 'adorable-client-portal' ); ?>
								</h2>
							</div>
							<div class="acp-card__body">
								<div class="acp-form-grid acp-form-grid--1">
									<div class="acp-form-field">
										<label class="acp-form-label" for="billing_address"><?php esc_html_e( 'Billing Address', 'adorable-client-portal' ); ?></label>
										<textarea id="billing_address" name="billing_address" class="acp-form-textarea" rows="3"><?php echo esc_textarea( $val( 'billing_address' ) ); ?></textarea>
									</div>
								</div>
								<div class="acp-form-grid acp-form-grid--3">
									<div class="acp-form-field">
										<label class="acp-form-label" for="city"><?php esc_html_e( 'City', 'adorable-client-portal' ); ?></label>
										<input type="text" id="city" name="city" class="acp-form-input" value="<?php echo esc_attr( $val( 'city' ) ); ?>">
									</div>
									<div class="acp-form-field">
										<label class="acp-form-label" for="state"><?php esc_html_e( 'State', 'adorable-client-portal' ); ?></label>
										<input type="text" id="state" name="state" class="acp-form-input" value="<?php echo esc_attr( $val( 'state' ) ); ?>">
									</div>
									<div class="acp-form-field">
										<label class="acp-form-label" for="pincode"><?php esc_html_e( 'Pincode', 'adorable-client-portal' ); ?></label>
										<input type="text" id="pincode" name="pincode" class="acp-form-input" value="<?php echo esc_attr( $val( 'pincode' ) ); ?>" maxlength="10">
									</div>
									<div class="acp-form-field">
										<label class="acp-form-label" for="country"><?php esc_html_e( 'Country', 'adorable-client-portal' ); ?></label>
										<input type="text" id="country" name="country" class="acp-form-input" value="<?php echo esc_attr( $val( 'country' ) ?: 'India' ); ?>">
									</div>
								</div>
							</div>
						</div>

						<!-- Section 4: Site Address -->
						<div class="acp-card acp-form-section">
							<div class="acp-card__header">
								<h2 class="acp-card__title">
									<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
									<?php esc_html_e( 'Site Address', 'adorable-client-portal' ); ?>
								</h2>
							</div>
							<div class="acp-card__body">
								<div class="acp-form-grid acp-form-grid--1">
									<div class="acp-form-field">
										<label class="acp-form-label" for="site_address"><?php esc_html_e( 'Site / Project Address', 'adorable-client-portal' ); ?></label>
										<textarea id="site_address" name="site_address" class="acp-form-textarea" rows="3"><?php echo esc_textarea( $val( 'site_address' ) ); ?></textarea>
									</div>
								</div>
							</div>
						</div>

						<!-- Section 5: Tax Information -->
						<div class="acp-card acp-form-section">
							<div class="acp-card__header">
								<h2 class="acp-card__title">
									<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
									<?php esc_html_e( 'Tax Information', 'adorable-client-portal' ); ?>
								</h2>
							</div>
							<div class="acp-card__body">
								<div class="acp-form-grid acp-form-grid--2">
									<div class="acp-form-field acp-form-company-field" id="acpGstField" <?php echo $val( 'client_type' ) === 'company' ? '' : 'style="display:none"'; ?>>
										<label class="acp-form-label" for="gst_number"><?php esc_html_e( 'GST Number', 'adorable-client-portal' ); ?></label>
										<input type="text" id="gst_number" name="gst_number" class="acp-form-input acp-duplicate-check" data-field="gst_number" data-exclude="<?php echo esc_attr( (string) $client_id ); ?>" value="<?php echo esc_attr( $val( 'gst_number' ) ); ?>" maxlength="15" style="text-transform:uppercase" placeholder="22AAAAA0000A1Z5">
										<span class="acp-form-hint"><?php esc_html_e( '15-character GST number', 'adorable-client-portal' ); ?></span>
										<span class="acp-form-error" id="err_gst_number"></span>
									</div>
									<div class="acp-form-field">
										<label class="acp-form-label" for="pan_number"><?php esc_html_e( 'PAN Number', 'adorable-client-portal' ); ?></label>
										<input type="text" id="pan_number" name="pan_number" class="acp-form-input" value="<?php echo esc_attr( $val( 'pan_number' ) ); ?>" maxlength="10" style="text-transform:uppercase" placeholder="AAAAA9999A">
										<span class="acp-form-hint"><?php esc_html_e( '10-character PAN number', 'adorable-client-portal' ); ?></span>
									</div>
								</div>
							</div>
						</div>

						<!-- Section 6: Notes -->
						<div class="acp-card acp-form-section">
							<div class="acp-card__header">
								<h2 class="acp-card__title">
									<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
									<?php esc_html_e( 'Notes', 'adorable-client-portal' ); ?>
								</h2>
							</div>
							<div class="acp-card__body">
								<div class="acp-form-field">
									<label class="acp-form-label" for="notes"><?php esc_html_e( 'Internal Notes', 'adorable-client-portal' ); ?></label>
									<textarea id="notes" name="notes" class="acp-form-textarea" rows="4" placeholder="<?php esc_attr_e( 'Add any internal notes about this client…', 'adorable-client-portal' ); ?>"><?php echo esc_textarea( $val( 'notes' ) ); ?></textarea>
								</div>
							</div>
						</div>

					</div><!-- /.acp-form-main -->

					<!-- Right Column: Sidebar Sections -->
					<div class="acp-form-sidebar">

						<!-- Assignment -->
						<div class="acp-card acp-form-section">
							<div class="acp-card__header">
								<h2 class="acp-card__title"><?php esc_html_e( 'Assignment', 'adorable-client-portal' ); ?></h2>
							</div>
							<div class="acp-card__body">
								<div class="acp-form-field">
									<label class="acp-form-label" for="assigned_salesperson"><?php esc_html_e( 'Sales Person', 'adorable-client-portal' ); ?></label>
									<select id="assigned_salesperson" name="assigned_salesperson" class="acp-form-select">
										<option value="0"><?php esc_html_e( '— Unassigned —', 'adorable-client-portal' ); ?></option>
										<?php foreach ( $users as $user ) : ?>
											<option value="<?php echo esc_attr( (string) $user->ID ); ?>" <?php selected( (int) $val( 'assigned_salesperson' ), (int) $user->ID ); ?>>
												<?php echo esc_html( $user->display_name ); ?>
											</option>
										<?php endforeach; ?>
									</select>
								</div>
								<div class="acp-form-field">
									<label class="acp-form-label" for="assigned_designer"><?php esc_html_e( 'Designer', 'adorable-client-portal' ); ?></label>
									<select id="assigned_designer" name="assigned_designer" class="acp-form-select">
										<option value="0"><?php esc_html_e( '— Unassigned —', 'adorable-client-portal' ); ?></option>
										<?php foreach ( $users as $user ) : ?>
											<option value="<?php echo esc_attr( (string) $user->ID ); ?>" <?php selected( (int) $val( 'assigned_designer' ), (int) $user->ID ); ?>>
												<?php echo esc_html( $user->display_name ); ?>
											</option>
										<?php endforeach; ?>
									</select>
								</div>
							</div>
						</div>

						<!-- Tags -->
						<div class="acp-card acp-form-section">
							<div class="acp-card__header">
								<h2 class="acp-card__title"><?php esc_html_e( 'Tags', 'adorable-client-portal' ); ?></h2>
							</div>
							<div class="acp-card__body">
								<div class="acp-form-field">
									<label class="acp-form-label" for="tags"><?php esc_html_e( 'Tags', 'adorable-client-portal' ); ?></label>
									<input type="text" id="tags" name="tags" class="acp-form-input" value="<?php echo esc_attr( $val( 'tags' ) ); ?>" placeholder="<?php esc_attr_e( 'vip, premium, referral…', 'adorable-client-portal' ); ?>">
									<span class="acp-form-hint"><?php esc_html_e( 'Comma-separated tags', 'adorable-client-portal' ); ?></span>
								</div>
							</div>
						</div>

						<!-- Save Actions -->
						<div class="acp-card acp-form-section">
							<div class="acp-card__body">
								<div class="acp-form-actions">
									<button type="submit" class="acp-btn acp-btn--primary acp-btn--full" id="acpSaveClient">
										<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
										<?php echo $is_edit ? esc_html__( 'Update Client', 'adorable-client-portal' ) : esc_html__( 'Save Client', 'adorable-client-portal' ); ?>
									</button>
									<a href="<?php echo esc_url( admin_url( 'admin.php?page=adorable-clients' ) ); ?>" class="acp-btn acp-btn--secondary acp-btn--full">
										<?php esc_html_e( 'Cancel', 'adorable-client-portal' ); ?>
									</a>
								</div>
							</div>
						</div>

					</div><!-- /.acp-form-sidebar -->

				</div><!-- /.acp-form-layout -->
			</form>

		</main>
	</div>
</div>

<!-- Toast Container -->
<div class="acp-toast-container" id="acpToastContainer" aria-live="polite"></div>
