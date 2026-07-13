<?php
/**
 * Admin menu registration.
 *
 * @package AdorableClientPortal\Admin
 */

declare( strict_types=1 );

namespace AdorableClientPortal\Admin;

use AdorableClientPortal\Includes\Constants;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Admin_Menu
 *
 * Registers the top-level menu and all submenus for Adorable CRM.
 */
final class Admin_Menu {

	/**
	 * Parent menu slug.
	 */
	private const PARENT_SLUG = 'adorable-crm';

	/**
	 * Register all menus with WordPress.
	 */
	public function register_menus(): void {
		if ( ! current_user_can( Constants::CAP_MANAGE ) && ! current_user_can( 'manage_options' ) ) {
			return;
		}

		add_menu_page(
			__( 'Adorable CRM', 'adorable-client-portal' ),
			__( 'Adorable CRM', 'adorable-client-portal' ),
			'manage_options',
			self::PARENT_SLUG,
			[ Dashboard_Controller::class, 'render' ],
			$this->get_menu_icon(),
			3
		);

		$submenus = $this->get_submenus();

		foreach ( $submenus as $submenu ) {
			add_submenu_page(
				self::PARENT_SLUG,
				$submenu['page_title'],
				$submenu['menu_title'],
				$submenu['capability'],
				$submenu['slug'],
				$submenu['callback']
			);
		}
	}

	/**
	 * Return the SVG icon for the menu as a base64 data URI.
	 *
	 * @return string
	 */
	private function get_menu_icon(): string {
		$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>';
		return 'data:image/svg+xml;base64,' . base64_encode( $svg ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
	}

	/**
	 * Return the submenu definitions.
	 *
	 * @return array<int, array{page_title: string, menu_title: string, capability: string, slug: string, callback: callable}>
	 */
	private function get_submenus(): array {
		return [
			[
				'page_title' => __( 'Dashboard', 'adorable-client-portal' ),
				'menu_title' => __( 'Dashboard', 'adorable-client-portal' ),
				'capability' => 'manage_options',
				'slug'       => self::PARENT_SLUG,
				'callback'   => [ Dashboard_Controller::class, 'render' ],
			],
			[
				'page_title' => __( 'Clients', 'adorable-client-portal' ),
				'menu_title' => __( 'Clients', 'adorable-client-portal' ),
				'capability' => 'manage_options',
				'slug'       => 'adorable-clients',
				'callback'   => [ new Clients_Controller(), 'render' ],
			],
			[
				'page_title' => __( 'Projects', 'adorable-client-portal' ),
				'menu_title' => __( 'Projects', 'adorable-client-portal' ),
				'capability' => 'manage_options',
				'slug'       => 'adorable-projects',
				'callback'   => [ Placeholder_Controller::class, 'projects' ],
			],
			[
				'page_title' => __( 'Quotations', 'adorable-client-portal' ),
				'menu_title' => __( 'Quotations', 'adorable-client-portal' ),
				'capability' => 'manage_options',
				'slug'       => 'adorable-quotations',
				'callback'   => [ Placeholder_Controller::class, 'quotations' ],
			],
			[
				'page_title' => __( 'Payments', 'adorable-client-portal' ),
				'menu_title' => __( 'Payments', 'adorable-client-portal' ),
				'capability' => 'manage_options',
				'slug'       => 'adorable-payments',
				'callback'   => [ Placeholder_Controller::class, 'payments' ],
			],
			[
				'page_title' => __( 'Gallery', 'adorable-client-portal' ),
				'menu_title' => __( 'Gallery', 'adorable-client-portal' ),
				'capability' => 'manage_options',
				'slug'       => 'adorable-gallery',
				'callback'   => [ Placeholder_Controller::class, 'gallery' ],
			],
			[
				'page_title' => __( 'Reports', 'adorable-client-portal' ),
				'menu_title' => __( 'Reports', 'adorable-client-portal' ),
				'capability' => 'manage_options',
				'slug'       => 'adorable-reports',
				'callback'   => [ Placeholder_Controller::class, 'reports' ],
			],
			[
				'page_title' => __( 'Settings', 'adorable-client-portal' ),
				'menu_title' => __( 'Settings', 'adorable-client-portal' ),
				'capability' => 'manage_options',
				'slug'       => 'adorable-settings',
				'callback'   => [ Placeholder_Controller::class, 'settings' ],
			],
			[
				'page_title' => __( 'Developer Tools', 'adorable-client-portal' ),
				'menu_title' => __( 'Developer Tools', 'adorable-client-portal' ),
				'capability' => 'manage_options',
				'slug'       => 'adorable-dev-tools',
				'callback'   => [ Placeholder_Controller::class, 'dev_tools' ],
			],
		];
	}
}
