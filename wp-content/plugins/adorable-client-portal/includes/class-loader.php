<?php
/**
 * Plugin loader — wires all hooks and modules.
 *
 * @package AdorableClientPortal\Includes
 */

declare( strict_types=1 );

namespace AdorableClientPortal\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Loader
 *
 * Bootstraps every module by registering WordPress hooks.
 */
final class Loader {

	/**
	 * Registered action hooks.
	 *
	 * @var array<int, array{hook: string, callback: callable, priority: int, args: int}>
	 */
	private array $actions = [];

	/**
	 * Registered filter hooks.
	 *
	 * @var array<int, array{hook: string, callback: callable, priority: int, args: int}>
	 */
	private array $filters = [];

	/**
	 * Add an action hook.
	 *
	 * @param string   $hook     WordPress hook name.
	 * @param callable $callback Callback to run.
	 * @param int      $priority Hook priority.
	 * @param int      $args     Number of accepted arguments.
	 */
	public function add_action( string $hook, callable $callback, int $priority = 10, int $args = 1 ): void {
		$this->actions[] = compact( 'hook', 'callback', 'priority', 'args' );
	}

	/**
	 * Add a filter hook.
	 *
	 * @param string   $hook     WordPress hook name.
	 * @param callable $callback Callback to run.
	 * @param int      $priority Hook priority.
	 * @param int      $args     Number of accepted arguments.
	 */
	public function add_filter( string $hook, callable $callback, int $priority = 10, int $args = 1 ): void {
		$this->filters[] = compact( 'hook', 'callback', 'priority', 'args' );
	}

	/**
	 * Register all collected hooks with WordPress.
	 */
	public function run(): void {
		foreach ( $this->filters as $filter ) {
			add_filter( $filter['hook'], $filter['callback'], $filter['priority'], $filter['args'] );
		}

		foreach ( $this->actions as $action ) {
			add_action( $action['hook'], $action['callback'], $action['priority'], $action['args'] );
		}
	}
}
