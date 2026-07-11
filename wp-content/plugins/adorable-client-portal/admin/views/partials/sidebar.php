<?php
/**
 * Dashboard sidebar partial.
 *
 * @package AdorableClientPortal\Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : 'adorable-crm'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

/**
 * Return 'acp-sidebar__link--active' if the given slug matches the current page.
 *
 * @param string $slug Menu slug.
 * @return string
 */
$is_active = static function ( string $slug ) use ( $current_page ): string {
	return $slug === $current_page ? 'acp-sidebar__link--active' : '';
};

$nav_items = [
	[
		'slug'  => 'adorable-crm',
		'label' => __( 'Dashboard', 'adorable-client-portal' ),
		'icon'  => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>',
	],
	[
		'slug'  => 'adorable-clients',
		'label' => __( 'Clients', 'adorable-client-portal' ),
		'icon'  => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
	],
	[
		'slug'  => 'adorable-projects',
		'label' => __( 'Projects', 'adorable-client-portal' ),
		'icon'  => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>',
	],
	[
		'slug'  => 'adorable-quotations',
		'label' => __( 'Quotations', 'adorable-client-portal' ),
		'icon'  => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>',
	],
	[
		'slug'  => 'adorable-payments',
		'label' => __( 'Payments', 'adorable-client-portal' ),
		'icon'  => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>',
	],
	[
		'slug'  => 'adorable-gallery',
		'label' => __( 'Gallery', 'adorable-client-portal' ),
		'icon'  => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>',
	],
	[
		'slug'  => 'adorable-reports',
		'label' => __( 'Reports', 'adorable-client-portal' ),
		'icon'  => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>',
	],
	[
		'slug'  => 'adorable-settings',
		'label' => __( 'Settings', 'adorable-client-portal' ),
		'icon'  => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/></svg>',
	],
	[
		'slug'  => 'adorable-dev-tools',
		'label' => __( 'Developer Tools', 'adorable-client-portal' ),
		'icon'  => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>',
	],
];
?>
<aside class="acp-sidebar" id="acpSidebar" role="navigation" aria-label="<?php esc_attr_e( 'Main navigation', 'adorable-client-portal' ); ?>">

	<div class="acp-sidebar__inner">

		<div class="acp-sidebar__brand">
			<?php if ( file_exists( ACP_PATH . 'assets/logo.png' ) ) : ?>
				<img
					src="<?php echo esc_url( ACP_URL . 'assets/logo.png' ); ?>"
					alt="<?php esc_attr_e( 'Adorable CRM', 'adorable-client-portal' ); ?>"
					class="acp-sidebar__logo"
					width="36"
					height="36"
				/>
			<?php endif; ?>
			<span class="acp-sidebar__brand-name"><?php esc_html_e( 'Adorable CRM', 'adorable-client-portal' ); ?></span>
		</div>

		<nav class="acp-sidebar__nav">
			<ul class="acp-sidebar__menu" role="list">
				<?php foreach ( $nav_items as $item ) : ?>
					<li class="acp-sidebar__item">
						<a
							href="<?php echo esc_url( admin_url( 'admin.php?page=' . $item['slug'] ) ); ?>"
							class="acp-sidebar__link <?php echo esc_attr( $is_active( $item['slug'] ) ); ?>"
							<?php if ( $is_active( $item['slug'] ) ) : ?>
								aria-current="page"
							<?php endif; ?>
						>
							<span class="acp-sidebar__icon"><?php echo $item['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — static SVG. ?></span>
							<span class="acp-sidebar__label"><?php echo esc_html( $item['label'] ); ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</nav>

		<div class="acp-sidebar__footer">
			<div class="acp-sidebar__version">
				<span class="acp-sidebar__version-label"><?php esc_html_e( 'Version', 'adorable-client-portal' ); ?></span>
				<span class="acp-sidebar__version-number"><?php echo esc_html( ACP_VERSION ); ?></span>
			</div>
		</div>

	</div>

</aside>
