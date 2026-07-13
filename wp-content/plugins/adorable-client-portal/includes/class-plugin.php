<?php
/**
 * Main plugin bootstrap.
 *
 * @package AdorableClientPortal\Includes
 */

declare(strict_types=1);

namespace AdorableClientPortal\Includes;

use AdorableClientPortal\Database\Migration_Runner;
use AdorableClientPortal\Admin\Admin_Menu;
use AdorableClientPortal\Admin\Assets;
use AdorableClientPortal\Admin\Clients_Controller;

if (!defined('ABSPATH')) {
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
	 * Private constructor.
	 */
	private function __construct() {
		$this->loader = new Loader();
	}

	/**
	 * Get singleton instance.
	 */
	public static function get_instance(): self {

		if (self::$instance === null) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Bootstrap plugin.
	 */
	public function run(): void {

		Migration_Runner::run();

		$this->define_admin_hooks();

		$this->loader->run();
	}

	/**
	 * Register admin hooks.
	 */
	private function define_admin_hooks(): void {

		if (!is_admin()) {
			return;
		}

		$admin_menu = new Admin_Menu();
		$assets     = new Assets();
		$clients    = new Clients_Controller();

		/*
		 * Admin
		 */
		$this->loader->add_action(
			'admin_menu',
			[$admin_menu, 'register_menus']
		);

		$this->loader->add_action(
			'admin_enqueue_scripts',
			[$assets, 'enqueue']
		);

		/*
		 * Clients AJAX
		 */

		$this->loader->add_action(
			'wp_ajax_acp_save_client',
			[$clients, 'ajax_save']
		);

		$this->loader->add_action(
			'wp_ajax_acp_delete_client',
			[$clients, 'ajax_delete']
		);

		$this->loader->add_action(
			'wp_ajax_acp_status_change',
			[$clients, 'ajax_status_change']
		);

		$this->loader->add_action(
			'wp_ajax_acp_bulk_clients',
			[$clients, 'ajax_bulk']
		);

		$this->loader->add_action(
			'wp_ajax_acp_check_duplicate',
			[$clients, 'ajax_check_duplicate']
		);

		$this->loader->add_action(
			'wp_ajax_acp_add_note',
			[$clients, 'ajax_add_note']
		);

		$this->loader->add_action(
			'wp_ajax_acp_export_clients',
			[$clients, 'ajax_export_csv']
		);
	}
}