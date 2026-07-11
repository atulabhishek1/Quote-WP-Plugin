<?php
/**
 * Placeholder controller for future modules.
 *
 * @package AdorableClientPortal\Admin
 */

declare( strict_types=1 );

namespace AdorableClientPortal\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Placeholder_Controller
 *
 * Renders a "coming soon" screen for modules not yet built.
 * Replace each method with the real controller when the module is ready.
 */
final class Placeholder_Controller {

	/**
	 * Render a coming-soon page for a given module.
	 *
	 * @param string $title Module title.
	 * @param string $icon  Dashicon class.
	 */
	private static function render_placeholder( string $title, string $icon ): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'adorable-client-portal' ) );
		}
		?>
		<div class="acp-wrap">
			<div class="acp-placeholder-page">
				<div class="acp-placeholder-page__inner">
					<span class="dashicons <?php echo esc_attr( $icon ); ?> acp-placeholder-page__icon"></span>
					<h1 class="acp-placeholder-page__title"><?php echo esc_html( $title ); ?></h1>
					<p class="acp-placeholder-page__desc">
						<?php
						printf(
							/* translators: %s: module name */
							esc_html__( 'The %s module is coming in the next phase. Stay tuned!', 'adorable-client-portal' ),
							'<strong>' . esc_html( $title ) . '</strong>'
						);
						?>
					</p>
				</div>
			</div>
		</div>
		<?php
	}

	/** Render Clients placeholder. */
	public static function clients(): void {
		self::render_placeholder( __( 'Clients', 'adorable-client-portal' ), 'dashicons-groups' );
	}

	/** Render Projects placeholder. */
	public static function projects(): void {
		self::render_placeholder( __( 'Projects', 'adorable-client-portal' ), 'dashicons-portfolio' );
	}

	/** Render Quotations placeholder. */
	public static function quotations(): void {
		self::render_placeholder( __( 'Quotations', 'adorable-client-portal' ), 'dashicons-media-document' );
	}

	/** Render Payments placeholder. */
	public static function payments(): void {
		self::render_placeholder( __( 'Payments', 'adorable-client-portal' ), 'dashicons-money-alt' );
	}

	/** Render Gallery placeholder. */
	public static function gallery(): void {
		self::render_placeholder( __( 'Gallery', 'adorable-client-portal' ), 'dashicons-format-gallery' );
	}

	/** Render Reports placeholder. */
	public static function reports(): void {
		self::render_placeholder( __( 'Reports', 'adorable-client-portal' ), 'dashicons-chart-bar' );
	}

	/** Render Settings placeholder. */
	public static function settings(): void {
		self::render_placeholder( __( 'Settings', 'adorable-client-portal' ), 'dashicons-admin-settings' );
	}

	/** Render Developer Tools placeholder. */
	public static function dev_tools(): void {
		self::render_placeholder( __( 'Developer Tools', 'adorable-client-portal' ), 'dashicons-code-standards' );
	}
}
