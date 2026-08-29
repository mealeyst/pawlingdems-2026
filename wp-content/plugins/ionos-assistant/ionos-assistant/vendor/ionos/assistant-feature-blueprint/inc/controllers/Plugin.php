<?php
namespace Ionos\Blueprint\Controllers;

/**
 * The Controller class for the whole plugin.
 */
class Plugin {
	/**
	 * Inits the plugin.
	 *
	 * @param string $dir Direcotry of the plugin.
	 * @param string $file Main file of the plugin.
	 */
	public static function init( $dir, $file ) {
		define( __NAMESPACE__ . '\\PLUGIN_DIR', $dir );
		define( __NAMESPACE__ . '\\PLUGIN_FILE', $file );

		self::load_textdomain();
	}

	/**
	 * Inits the plugin translation.
	 */
	public static function load_textdomain() {
		$plugin_rel_path = substr( PLUGIN_DIR, strlen( WP_PLUGIN_DIR ) + 1 );

		load_plugin_textdomain(
			'ionos-blueprint',
			false,
			$plugin_rel_path . '/languages'
		);
	}
}
