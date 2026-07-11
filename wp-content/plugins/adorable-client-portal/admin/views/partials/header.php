<?php
/**
 * Dashboard top header partial.
 *
 * Variables available from Dashboard_Controller::collect_data():
 *
 * @var \WP_User $current_user
 * @var string   $currency
 *
 * @package AdorableClientPortal\Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$user_display_name = esc_html( $current_user->display_name );
$user_avatar       = get_avatar_url( $current_user->ID, [ 'size' => 40 ] );
$current_date      = wp_date( 'l, F j, Y' );
?>
<header class="acp-topbar" role="banner">

	<div class="acp-topbar__left">

		<button
			class="acp-topbar__sidebar-toggle"
			id="acpSidebarToggle"
			aria-label="<?php esc_attr_e( 'Toggle sidebar', 'adorable-client-portal' ); ?>"
			aria-expanded="true"
			aria-controls="acpSidebar"
			type="button"
		>
			<span class="acp-topbar__sidebar-toggle-bar"></span>
			<span class="acp-topbar__sidebar-toggle-bar"></span>
			<span class="acp-topbar__sidebar-toggle-bar"></span>
		</button>

		<div class="acp-topbar__brand">
			<?php if ( file_exists( ACP_PATH . 'assets/logo.png' ) ) : ?>
				<img
					src="<?php echo esc_url( ACP_URL . 'assets/logo.png' ); ?>"
					alt="<?php esc_attr_e( 'Adorable CRM', 'adorable-client-portal' ); ?>"
					class="acp-topbar__logo"
					width="32"
					height="32"
				/>
			<?php endif; ?>
			<span class="acp-topbar__brand-name"><?php esc_html_e( 'Adorable CRM', 'adorable-client-portal' ); ?></span>
		</div>

		<nav class="acp-breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'adorable-client-portal' ); ?>">
			<ol class="acp-breadcrumb__list">
				<li class="acp-breadcrumb__item">
					<span><?php esc_html_e( 'Home', 'adorable-client-portal' ); ?></span>
				</li>
				<li class="acp-breadcrumb__item acp-breadcrumb__item--active" aria-current="page">
					<span><?php esc_html_e( 'Dashboard', 'adorable-client-portal' ); ?></span>
				</li>
			</ol>
		</nav>

	</div>

	<div class="acp-topbar__center">

		<div class="acp-search" role="search">
			<label for="acpQuickSearch" class="screen-reader-text">
				<?php esc_html_e( 'Quick search', 'adorable-client-portal' ); ?>
			</label>
			<span class="acp-search__icon" aria-hidden="true">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
			</span>
			<input
				type="search"
				id="acpQuickSearch"
				class="acp-search__input"
				placeholder="<?php esc_attr_e( 'Search clients, projects, quotes…', 'adorable-client-portal' ); ?>"
				autocomplete="off"
				aria-label="<?php esc_attr_e( 'Quick search', 'adorable-client-portal' ); ?>"
			/>
			<kbd class="acp-search__shortcut" aria-hidden="true">⌘K</kbd>
		</div>

	</div>

	<div class="acp-topbar__right">

		<span class="acp-topbar__date" aria-label="<?php esc_attr_e( 'Current date', 'adorable-client-portal' ); ?>">
			<?php echo esc_html( $current_date ); ?>
		</span>

		<button
			class="acp-topbar__icon-btn acp-dark-mode-toggle"
			id="acpDarkModeToggle"
			aria-label="<?php esc_attr_e( 'Toggle dark mode', 'adorable-client-portal' ); ?>"
			aria-pressed="false"
			type="button"
		>
			<svg class="acp-dark-mode-toggle__sun" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
			<svg class="acp-dark-mode-toggle__moon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
		</button>

		<div class="acp-notifications" id="acpNotifications">
			<button
				class="acp-topbar__icon-btn acp-notifications__trigger"
				aria-label="<?php esc_attr_e( 'Notifications', 'adorable-client-portal' ); ?>"
				aria-haspopup="true"
				aria-expanded="false"
				type="button"
			>
				<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
				<span class="acp-notifications__badge" id="acpNotificationCount" aria-live="polite" hidden>0</span>
			</button>

			<div class="acp-notifications__dropdown" role="dialog" aria-label="<?php esc_attr_e( 'Notifications', 'adorable-client-portal' ); ?>" hidden>
				<div class="acp-notifications__header">
					<span class="acp-notifications__title"><?php esc_html_e( 'Notifications', 'adorable-client-portal' ); ?></span>
					<button class="acp-notifications__mark-all" type="button">
						<?php esc_html_e( 'Mark all read', 'adorable-client-portal' ); ?>
					</button>
				</div>
				<div class="acp-notifications__body">
					<div class="acp-empty-state acp-empty-state--sm">
						<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
						<p><?php esc_html_e( 'No new notifications', 'adorable-client-portal' ); ?></p>
					</div>
				</div>
			</div>
		</div>

		<div class="acp-user-menu" id="acpUserMenu">
			<button
				class="acp-user-menu__trigger"
				aria-haspopup="true"
				aria-expanded="false"
				type="button"
			>
				<img
					src="<?php echo esc_url( $user_avatar ); ?>"
					alt="<?php echo esc_attr( $user_display_name ); ?>"
					class="acp-user-menu__avatar"
					width="36"
					height="36"
				/>
				<span class="acp-user-menu__name"><?php echo $user_display_name; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — already escaped above. ?></span>
				<svg class="acp-user-menu__chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg>
			</button>

			<div class="acp-user-menu__dropdown" role="menu" hidden>
				<div class="acp-user-menu__info">
					<strong class="acp-user-menu__display-name"><?php echo $user_display_name; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
					<span class="acp-user-menu__email"><?php echo esc_html( $current_user->user_email ); ?></span>
				</div>
				<hr class="acp-user-menu__divider" />
				<a class="acp-user-menu__item" href="<?php echo esc_url( get_edit_profile_url() ); ?>" role="menuitem">
					<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
					<?php esc_html_e( 'Profile', 'adorable-client-portal' ); ?>
				</a>
				<a class="acp-user-menu__item" href="<?php echo esc_url( admin_url( 'admin.php?page=adorable-settings' ) ); ?>" role="menuitem">
					<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/></svg>
					<?php esc_html_e( 'Settings', 'adorable-client-portal' ); ?>
				</a>
				<hr class="acp-user-menu__divider" />
				<a class="acp-user-menu__item acp-user-menu__item--danger" href="<?php echo esc_url( wp_logout_url( admin_url() ) ); ?>" role="menuitem">
					<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
					<?php esc_html_e( 'Sign out', 'adorable-client-portal' ); ?>
				</a>
			</div>
		</div>

	</div>

</header>
