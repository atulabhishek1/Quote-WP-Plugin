<?php
/**
 * Plugin Name:       Adorable Client Portal
 * Plugin URI:        https://adorablecreations.in
 * Description:       Premium Interior Design CRM & Client Portal for WordPress.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      8.0
 * Author:            Adorable Creations
 * Author URI:        https://adorablecreations.in
 * License:           GPL-2.0+
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       adorable-client-portal
 * Domain Path:       /languages
 *
 * @package AdorableClientPortal
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define core constants before autoloader.
define( 'ACP_VERSION', '1.0.0' );
define( 'ACP_FILE', __FILE__ );
define( 'ACP_PATH', plugin_dir_path( __FILE__ ) );
define( 'ACP_URL', plugin_dir_url( __FILE__ ) );
define( 'ACP_BASENAME', plugin_basename( __FILE__ ) );

// Minimum requirements check.
if ( version_compare( PHP_VERSION, '8.0', '<' ) ) {
	add_action(
		'admin_notices',
		function () {
			echo '<div class="notice notice-error"><p>' .
				esc_html__( 'Adorable Client Portal requires PHP 8.0 or higher.', 'adorable-client-portal' ) .
				'</p></div>';
		}
	);
	return;
}

// Load autoloader.
require_once ACP_PATH . 'includes/class-autoloader.php';

\AdorableClientPortal\Includes\Autoloader::register();

// Activation / Deactivation hooks.
register_activation_hook( __FILE__, [ \AdorableClientPortal\Includes\Activator::class, 'activate' ] );
register_deactivation_hook( __FILE__, [ \AdorableClientPortal\Includes\Deactivator::class, 'deactivate' ] );

// Boot the plugin.
add_action(
	'plugins_loaded',
	function () {
		\AdorableClientPortal\Includes\Plugin::get_instance()->run();
	}
);
