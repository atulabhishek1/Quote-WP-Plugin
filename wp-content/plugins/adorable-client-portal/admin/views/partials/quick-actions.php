<?php
/**
 * Quick actions partial.
 *
 * @package AdorableClientPortal\Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$actions = [
	[
		'label' => __( 'New Client', 'adorable-client-portal' ),
		'href'  => admin_url( 'admin.php?page=adorable-clients&action=new' ),
		'color' => 'primary',
		'icon'  => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>',
	],
	[
		'label' => __( 'New Project', 'adorable-client-portal' ),
		'href'  => admin_url( 'admin.php?page=adorable-projects&action=new' ),
		'color' => 'purple',
		'icon'  => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>',
	],
	[
		'label' => __( 'Generate Quote', 'adorable-client-portal' ),
		'href'  => admin_url( 'admin.php?page=adorable-quotations&action=new' ),
		'color' => 'warning',
		'icon'  => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>',
	],
	[
		'label' => __( 'Add Payment', 'adorable-client-portal' ),
		'href'  => admin_url( 'admin.php?page=adorable-payments&action=new' ),
		'color' => 'success',
		'icon'  => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>',
	],
	[
		'label' => __( 'Upload Render', 'adorable-client-portal' ),
		'href'  => admin_url( 'admin.php?page=adorable-gallery&action=upload' ),
		'color' => 'info',
		'icon'  => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>',
	],
	[
		'label' => __( 'Settings', 'adorable-client-portal' ),
		'href'  => admin_url( 'admin.php?page=adorable-settings' ),
		'color' => 'muted',
		'icon'  => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/></svg>',
	],
];
?>
<section class="acp-card acp-quick-actions" aria-labelledby="acpQuickActionsTitle">

	<div class="acp-card__header">
		<h2 class="acp-card__title" id="acpQuickActionsTitle">
			<?php esc_html_e( 'Quick Actions', 'adorable-client-portal' ); ?>
		</h2>
	</div>

	<div class="acp-card__body">
		<div class="acp-quick-actions__grid">
			<?php foreach ( $actions as $action ) : ?>
				<a
					href="<?php echo esc_url( $action['href'] ); ?>"
					class="acp-quick-action acp-quick-action--<?php echo esc_attr( $action['color'] ); ?>"
				>
					<span class="acp-quick-action__icon"><?php echo $action['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — static SVG. ?></span>
					<span class="acp-quick-action__label"><?php echo esc_html( $action['label'] ); ?></span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>

</section>
