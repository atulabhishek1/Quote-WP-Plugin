<?php
/**
 * Activation handler.
 *
 * @package AdorableClientPortal\Includes
 */

declare( strict_types=1 );

namespace AdorableClientPortal\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Activator
 */
final class Activator {
	/**
	 * Run on plugin activation.
	 */
	public static function activate(): void {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		update_option( Constants::OPTION_ACTIVATED, 1 );
	}
}
