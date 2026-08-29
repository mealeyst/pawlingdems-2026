<?php

namespace Ionos\HelpCenter;

// Do not allow direct access!
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

class Helper {

	public static function get_plugin_dir_path() {
		return plugin_dir_path( __DIR__ );
	}

	/**
	 * Get CSS URL from plugin
	 *
	 * @param  string  $file
	 *
	 * @return string
	 */
	public static function get_css_url( $file = '' ) {
		return plugins_url( 'css/' . $file, __DIR__ );
	}

	public static function get_css_path( $file = '' ) {
		return self::get_plugin_dir_path() . 'css/' . $file;
	}

	/**
	 * Get JavaScript URL from plugin
	 *
	 * @param  string  $file
	 *
	 * @return string
	 */
	public static function get_js_url( $file = '' ) {
		return plugins_url( 'js/' . $file, __DIR__ );
	}

	public static function get_js_path( $file = '' ) {
		return self::get_plugin_dir_path() . 'js/' . $file;
	}
}
