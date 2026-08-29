<?php

namespace Ionos\Navigation;

// Do not allow direct access!
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Helper class
 */
class Helper {
	/**
	 * Get the url of the css folder
	 *
	 * @param  string  $file  // css file name.
	 *
	 * @return string
	 */
	public static function get_css_url( $file = '' ) {
		return plugins_url( 'css/' . $file, __DIR__ );
	}

	/**
	 * Get the path of the css folder
	 *
	 * @param  string  $file  // css file name.
	 *
	 * @return string
	 */
	public static function get_css_path( $file = '' ) {
		return plugin_dir_path( __DIR__ ) . 'css/';
	}
}
