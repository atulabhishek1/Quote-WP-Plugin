<?php
/**
 * PSR-4 Autoloader.
 *
 * @package AdorableClientPortal\Includes
 */

declare( strict_types=1 );

namespace AdorableClientPortal\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Autoloader
 *
 * Maps namespaces to filesystem paths and loads classes automatically.
 */
class Autoloader {

	/**
	 * Namespace to directory map.
	 *
	 * @var array<string, string>
	 */
	private static array $namespace_map = [
		'AdorableClientPortal\\Includes\\'  => 'includes/',
		'AdorableClientPortal\\Admin\\'     => 'admin/',
		'AdorableClientPortal\\Public_\\'   => 'public/',
		'AdorableClientPortal\\Database\\' => 'database/',
		'AdorableClientPortal\\Modules\\'   => 'modules/',
	];

	/**
	 * Register the autoloader with SPL.
	 */
	public static function register(): void {
		spl_autoload_register( [ static::class, 'load' ] );
	}

	/**
	 * Load a class file based on its fully qualified class name.
	 *
	 * @param string $class Fully qualified class name.
	 */
	public static function load( string $class ): void {
		foreach ( self::$namespace_map as $namespace => $directory ) {
			if ( ! str_starts_with( $class, $namespace ) ) {
				continue;
			}

			$relative_class = substr( $class, strlen( $namespace ) );
			$file           = self::resolve_file_path( $directory, $relative_class );

			if ( file_exists( $file ) ) {
				require_once $file;
				return;
			}
		}
	}

	/**
	 * Convert a class name to a file path following WordPress naming conventions.
	 *
	 * @param string $directory  Base directory.
	 * @param string $class_name Relative class name.
	 * @return string Absolute file path.
	 */
	private static function resolve_file_path( string $directory, string $class_name ): string {
		$parts     = explode( '\\', $class_name );
		$class     = array_pop( $parts );
		$file_name = 'class-' . strtolower( str_replace( '_', '-', $class ) ) . '.php';

		$sub_path = ! empty( $parts )
			? strtolower( implode( DIRECTORY_SEPARATOR, $parts ) ) . DIRECTORY_SEPARATOR
			: '';

		return ACP_PATH . $directory . $sub_path . $file_name;
	}
}
