<?php
/**
 * Main plugin bootstrap.
 *
 * @package AdorableClientPortal\Includes
 */

declare( strict_types=1 );

namespace AdorableClientPortal\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Plugin
 *
 * Singleton that wires every module into WordPress via the Loader.
 */
final class Plugin {

	/**
	 * Singleton instance.
	 *
	 * @var self|null
	 */
	private static ?self $instance = null;

	/**
	 * Hook loader.
	 *
	 * @var Loader
	 */
	private Loader $loader;

	/**
	 * Private constructor — use get_instance().
	 */
	private function __construct() {
		$this->loader = new Loader();
	}

	/**
	 * Return the singleton instance.
	 */
	public static function get_instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Bootstrap all modules and run the loader.
	 */
	public function run(): void {
		$this->define_admin_hooks();
		$this->loader->run();
	}

	/**
	 * Register all admin-side hooks.
	 */
	private function define_admin_hooks(): void {
		if ( ! is_admin() ) {
			return;
		}

		$admin_menu = new \AdorableClientPortal\Admin\Admin_Menu();
		$assets     = new \AdorableClientPortal\Admin\Assets();

		$this->loader->add_action( 'admin_menu', [ $admin_menu, 'register_menus' ] );
		$this->loader->add_action( 'admin_enqueue_scripts', [ $assets, 'enqueue' ] );
	}
}
