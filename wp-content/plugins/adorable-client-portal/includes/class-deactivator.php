<?php
/**
 * Deactivation handler.
 *
 * @package AdorableClientPortal\Includes
 */

declare( strict_types=1 );

namespace AdorableClientPortal\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Deactivator
 */
final class Deactivator {
	/**
	 * Run on plugin deactivation.
	 */
	public static function deactivate(): void {
		update_option( Constants::OPTION_ACTIVATED, 0 );
	}
}
