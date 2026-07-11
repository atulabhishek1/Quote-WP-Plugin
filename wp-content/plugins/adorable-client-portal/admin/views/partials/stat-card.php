<?php
/**
 * Reusable statistics card partial.
 *
 * Expected $card array:
 *   string  title       Card title.
 *   string  value       Formatted display value.
 *   string  icon        Inline SVG string.
 *   string  color       CSS modifier: primary|success|warning|danger|info|purple.
 *   string  trend       Optional trend text e.g. "+12% this month".
 *   string  trend_dir   Optional: 'up' | 'down' | 'neutral'.
 *   string  link        Optional admin URL for "View all" link.
 *   string  link_label  Optional link label text.
 *
 * @var array{title: string, value: string, icon: string, color: string, trend?: string, trend_dir?: string, link?: string, link_label?: string} $card
 *
 * @package AdorableClientPortal\Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$color      = isset( $card['color'] ) ? sanitize_html_class( $card['color'] ) : 'primary';
$trend_dir  = isset( $card['trend_dir'] ) ? sanitize_html_class( $card['trend_dir'] ) : 'neutral';
$has_trend  = ! empty( $card['trend'] );
$has_link   = ! empty( $card['link'] );
?>
<div class="acp-stat-card acp-stat-card--<?php echo esc_attr( $color ); ?>" role="article">

	<div class="acp-stat-card__body">

		<div class="acp-stat-card__content">
			<p class="acp-stat-card__title"><?php echo esc_html( $card['title'] ); ?></p>
			<p class="acp-stat-card__value" aria-live="polite"><?php echo esc_html( $card['value'] ); ?></p>

			<?php if ( $has_trend ) : ?>
				<p class="acp-stat-card__trend acp-stat-card__trend--<?php echo esc_attr( $trend_dir ); ?>">
					<?php if ( 'up' === $trend_dir ) : ?>
						<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="18 15 12 9 6 15"/></svg>
					<?php elseif ( 'down' === $trend_dir ) : ?>
						<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg>
					<?php endif; ?>
					<span><?php echo esc_html( $card['trend'] ); ?></span>
				</p>
			<?php endif; ?>
		</div>

		<div class="acp-stat-card__icon-wrap" aria-hidden="true">
			<?php echo $card['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — static SVG passed from controller. ?>
		</div>

	</div>

	<?php if ( $has_link ) : ?>
		<div class="acp-stat-card__footer">
			<a href="<?php echo esc_url( $card['link'] ); ?>" class="acp-stat-card__link">
				<?php echo esc_html( $card['link_label'] ?? __( 'View all', 'adorable-client-portal' ) ); ?>
				<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"/></svg>
			</a>
		</div>
	<?php endif; ?>

</div>
