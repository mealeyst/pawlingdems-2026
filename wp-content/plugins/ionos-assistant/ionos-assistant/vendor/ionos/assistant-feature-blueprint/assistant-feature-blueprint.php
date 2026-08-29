<?php
/**
 * Blueprint
 *
 * @package           Blueprints
 * @author            Ionos
 * @copyright         2022 Ionos
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Blueprints
 * Description:       Export blueprint files with information about plugins and themes to import them elsewhere.
 * Version:           1.0.1
 * Requires at least: 5.0
 * Requires PHP:      7.4
 * Author:            IONOS
 * Author URI:        https://ionos.com
 * Text Domain:       ionos-blueprint
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

namespace Ionos\Blueprint;

use Exception;

try {
	if ( ! file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
		throw new Exception( 'Please run "composer install" in the plugin directory.' );
	}
} catch ( Exception $e ) {
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	wp_die( $e->getMessage() );
}

require_once __DIR__ . '/vendor/autoload.php';

if ( ! defined( 'ABSPATH' ) ) {
	return;
}
