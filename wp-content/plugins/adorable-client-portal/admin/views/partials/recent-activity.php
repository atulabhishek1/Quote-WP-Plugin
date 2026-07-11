<?php
/**
 * Recent activity partial.
 *
 * Variables available:
 *
 * @var array{activity: array, clients: array, projects: array} $recent
 * @var string $currency
 *
 * @package AdorableClientPortal\Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return a human-readable time difference.
 *
 * @param string $datetime MySQL datetime string.
 * @return string
 */
$time_ago = static function ( string $datetime ): string {
	$timestamp = strtotime( $datetime );

	if ( false === $timestamp ) {
		return '';
	}

	return sprintf(
		/* translators: %s: human-readable time difference */
		__( '%s ago', 'adorable-client-portal' ),
		human_time_diff( $timestamp, time() )
	);
};

/**
 * Return a status badge HTML string.
 *
 * @param string $status Status slug.
 * @param array  $map    Map of slug => label.
 * @return string
 */
$status_badge = static function ( string $status, array $map ): string {
	$label = $map[ $status ] ?? ucfirst( $status );
	$class = sanitize_html_class( $status );

	return sprintf(
		'<span class="acp-badge acp-badge--%s">%s</span>',
		esc_attr( $class ),
		esc_html( $label )
	);
};
?>

<div class="acp-activity-grid">

	<!-- Activity Log -->
	<section class="acp-card acp-activity-log" aria-labelledby="acpActivityLogTitle">

		<div class="acp-card__header">
			<h2 class="acp-card__title" id="acpActivityLogTitle">
				<?php esc_html_e( 'Latest Activity', 'adorable-client-portal' ); ?>
			</h2>
		</div>

		<div class="acp-card__body acp-card__body--flush">
			<?php if ( empty( $recent['activity'] ) ) : ?>
				<div class="acp-empty-state">
					<svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
					<p><?php esc_html_e( 'No activity recorded yet.', 'adorable-client-portal' ); ?></p>
				</div>
			<?php else : ?>
				<ul class="acp-activity-list" role="list">
					<?php foreach ( $recent['activity'] as $entry ) : ?>
						<li class="acp-activity-list__item">
							<div class="acp-activity-list__dot" aria-hidden="true"></div>
							<div class="acp-activity-list__content">
								<p class="acp-activity-list__action"><?php echo esc_html( $entry['action'] ); ?></p>
								<p class="acp-activity-list__desc"><?php echo esc_html( $entry['description'] ); ?></p>
							</div>
							<time
								class="acp-activity-list__time"
								datetime="<?php echo esc_attr( $entry['created_at'] ); ?>"
								title="<?php echo esc_attr( $entry['created_at'] ); ?>"
							>
								<?php echo esc_html( $time_ago( $entry['created_at'] ) ); ?>
							</time>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>

	</section>

	<!-- Recent Clients -->
	<section class="acp-card acp-recent-clients" aria-labelledby="acpRecentClientsTitle">

		<div class="acp-card__header">
			<h2 class="acp-card__title" id="acpRecentClientsTitle">
				<?php esc_html_e( 'Recent Clients', 'adorable-client-portal' ); ?>
			</h2>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=adorable-clients' ) ); ?>" class="acp-card__header-link">
				<?php esc_html_e( 'View all', 'adorable-client-portal' ); ?>
			</a>
		</div>

		<div class="acp-card__body acp-card__body--flush">
			<?php if ( empty( $recent['clients'] ) ) : ?>
				<div class="acp-empty-state">
					<svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
					<p><?php esc_html_e( 'No clients added yet.', 'adorable-client-portal' ); ?></p>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=adorable-clients&action=new' ) ); ?>" class="acp-btn acp-btn--sm acp-btn--primary">
						<?php esc_html_e( 'Add First Client', 'adorable-client-portal' ); ?>
					</a>
				</div>
			<?php else : ?>
				<ul class="acp-entity-list" role="list">
					<?php foreach ( $recent['clients'] as $client ) : ?>
						<li class="acp-entity-list__item">
							<div class="acp-entity-list__avatar" aria-hidden="true">
								<?php echo esc_html( mb_strtoupper( mb_substr( $client['name'], 0, 1 ) ) ); ?>
							</div>
							<div class="acp-entity-list__info">
								<p class="acp-entity-list__name"><?php echo esc_html( $client['name'] ); ?></p>
								<p class="acp-entity-list__meta"><?php echo esc_html( $client['email'] ); ?></p>
							</div>
							<time
								class="acp-entity-list__time"
								datetime="<?php echo esc_attr( $client['created_at'] ); ?>"
							>
								<?php echo esc_html( $time_ago( $client['created_at'] ) ); ?>
							</time>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>

	</section>

	<!-- Recent Projects -->
	<section class="acp-card acp-recent-projects" aria-labelledby="acpRecentProjectsTitle">

		<div class="acp-card__header">
			<h2 class="acp-card__title" id="acpRecentProjectsTitle">
				<?php esc_html_e( 'Recent Projects', 'adorable-client-portal' ); ?>
			</h2>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=adorable-projects' ) ); ?>" class="acp-card__header-link">
				<?php esc_html_e( 'View all', 'adorable-client-portal' ); ?>
			</a>
		</div>

		<div class="acp-card__body acp-card__body--flush">
			<?php if ( empty( $recent['projects'] ) ) : ?>
				<div class="acp-empty-state">
					<svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
					<p><?php esc_html_e( 'No projects created yet.', 'adorable-client-portal' ); ?></p>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=adorable-projects&action=new' ) ); ?>" class="acp-btn acp-btn--sm acp-btn--primary">
						<?php esc_html_e( 'Create First Project', 'adorable-client-portal' ); ?>
					</a>
				</div>
			<?php else : ?>
				<ul class="acp-entity-list" role="list">
					<?php
					$project_statuses = \AdorableClientPortal\Includes\Constants::PROJECT_STATUSES;
					foreach ( $recent['projects'] as $project ) :
					?>
						<li class="acp-entity-list__item">
							<div class="acp-entity-list__info">
								<p class="acp-entity-list__name"><?php echo esc_html( $project['title'] ); ?></p>
								<p class="acp-entity-list__meta">
									<?php echo $status_badge( $project['status'], $project_statuses ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — escaped inside function. ?>
								</p>
							</div>
							<time
								class="acp-entity-list__time"
								datetime="<?php echo esc_attr( $project['created_at'] ); ?>"
							>
								<?php echo esc_html( $time_ago( $project['created_at'] ) ); ?>
							</time>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>

	</section>

</div>
