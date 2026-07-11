<?php
/**
 * Migration runner.
 *
 * Discovers all migration classes, compares them against the installed
 * database version stored in wp_options, and runs any pending migrations
 * in ascending version order.
 *
 * Usage:
 *   Migration_Runner::run();   // on activation and plugin upgrade
 *   Migration_Runner::rollback( '1.0.0' );  // on full uninstall
 *
 * @package AdorableClientPortal\Database
 */

declare( strict_types=1 );

namespace AdorableClientPortal\Database;

use AdorableClientPortal\Includes\Constants;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Migration_Runner
 */
final class Migration_Runner {

	/**
	 * Absolute path to the migrations directory.
	 */
	private const MIGRATIONS_DIR = ACP_PATH . 'database/migrations/';

	/**
	 * Namespace prefix for all migration classes.
	 */
	private const MIGRATIONS_NS = 'AdorableClientPortal\\Database\\Migrations\\';

	/**
	 * Run all pending migrations.
	 *
	 * Compares the installed DB version (from wp_options) against every
	 * migration file found in the migrations directory. Runs each pending
	 * migration in ascending version order, then stores the new version.
	 *
	 * @return void
	 */
	public static function run(): void {
		$installed_version = (string) get_option( Constants::OPTION_DB_VERSION, '0.0.0' );
		$pending           = self::get_pending_migrations( $installed_version );

		if ( empty( $pending ) ) {
			return;
		}

		foreach ( $pending as $version => $class_name ) {
			self::load_migration_file( $version );

			if ( ! class_exists( $class_name ) ) {
				continue;
			}

			$migration = new $class_name();
			$migration->up();

			// Store the version after each successful migration so a partial
			// failure leaves the DB at the last successfully applied version.
			update_option( Constants::OPTION_DB_VERSION, $version, false );
		}
	}

	/**
	 * Roll back a specific migration version.
	 *
	 * Called only during full plugin uninstall — never on deactivation.
	 *
	 * @param string $version The version to roll back (e.g. '1.0.0').
	 * @return void
	 */
	public static function rollback( string $version ): void {
		$class_name = self::version_to_class_name( $version );

		self::load_migration_file( $version );

		if ( ! class_exists( $class_name ) ) {
			return;
		}

		$migration = new $class_name();
		$migration->down();

		delete_option( Constants::OPTION_DB_VERSION );
	}

	// -------------------------------------------------------------------------
	// Private helpers
	// -------------------------------------------------------------------------

	/**
	 * Return an ordered map of version => class_name for all migrations
	 * that are newer than the installed version.
	 *
	 * @param string $installed_version Currently installed DB version.
	 * @return array<string, string>  version => fully-qualified class name
	 */
	private static function get_pending_migrations( string $installed_version ): array {
		$all     = self::discover_migrations();
		$pending = [];

		foreach ( $all as $version => $class_name ) {
			if ( version_compare( $version, $installed_version, '>' ) ) {
				$pending[ $version ] = $class_name;
			}
		}

		return $pending;
	}

	/**
	 * Scan the migrations directory and return all discovered migrations
	 * sorted by version ascending.
	 *
	 * File naming convention: class-migration-v{major}-{minor}-{patch}.php
	 * Example: class-migration-v1-0-0.php  →  version 1.0.0
	 *
	 * @return array<string, string>  version => fully-qualified class name
	 */
	private static function discover_migrations(): array {
		if ( ! is_dir( self::MIGRATIONS_DIR ) ) {
			return [];
		}

		$files    = glob( self::MIGRATIONS_DIR . 'class-migration-v*.php' );
		$map      = [];

		if ( false === $files || empty( $files ) ) {
			return [];
		}

		foreach ( $files as $file ) {
			$basename = basename( $file, '.php' );

			// Extract version from filename: class-migration-v1-0-0 → 1.0.0
			if ( ! preg_match( '/^class-migration-v(\d+)-(\d+)-(\d+)$/', $basename, $m ) ) {
				continue;
			}

			$version              = "{$m[1]}.{$m[2]}.{$m[3]}";
			$map[ $version ]      = self::version_to_class_name( $version );
		}

		uksort( $map, 'version_compare' );

		return $map;
	}

	/**
	 * Convert a version string to a fully-qualified migration class name.
	 *
	 * 1.0.0  →  AdorableClientPortal\Database\Migrations\Migration_V1_0_0
	 *
	 * @param string $version Semantic version string.
	 * @return string
	 */
	private static function version_to_class_name( string $version ): string {
		$suffix = 'Migration_V' . str_replace( '.', '_', $version );
		return self::MIGRATIONS_NS . $suffix;
	}

	/**
	 * Require the migration file for a given version.
	 *
	 * @param string $version Semantic version string.
	 * @return void
	 */
	private static function load_migration_file( string $version ): void {
		$dashes = str_replace( '.', '-', $version );
		$file   = self::MIGRATIONS_DIR . "class-migration-v{$dashes}.php";

		if ( file_exists( $file ) ) {
			require_once $file;
		}
	}
}
