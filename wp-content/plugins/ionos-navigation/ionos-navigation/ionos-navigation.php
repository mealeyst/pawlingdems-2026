<?php
/**
 * Plugin Name:  IONOS Navigation
 * Plugin URI:   https://www.ionos.com
 * Description:  IONOS Navigation allows you to navigate quickly to customer-related control panel pages like invoices, contract data, support … and to swiftly switch between your Managed WordPress instances.
 * Version:      1.0.7
 * License:      GPLv2 or later
 * Author:       IONOS
 * Author URI:   https://www.ionos.com
 * Text Domain:  ionos-navigation
 */

namespace Ionos\Navigation;

use Ionos\Navigation\Config;
use Ionos\Navigation\Options;
use Ionos\Navigation\Updater;
use Ionos\Navigation\Warning;

define( 'IONOS_NAVIGATION_FILE', __FILE__ );
define( 'IONOS_NAVIGATION_DIR', __DIR__ );
define( 'IONOS_NAVIGATION_BASE', plugin_basename( __FILE__ ) );


$autoloader = __DIR__ . '/vendor/autoload.php';
if ( is_readable( $autoloader ) ) {
	require_once $autoloader;
}

Options::set_tenant_and_plugin_name( 'ionos', 'navigation' );

Options::clean_up( IONOS_NAVIGATION_FILE );

/**
 * Initialize plugin.
 */
function init() {
	new Updater();
	new Warning( 'ionos-navigation' );

	if ( current_user_can( 'manage_options' ) && is_admin() && '1' === Config::get( 'features.enabled' ) ) {
		require_once 'inc/class-manager.php';
		require_once 'inc/class-helper.php';

		new Manager();
	}
}

\add_action( 'plugins_loaded', __NAMESPACE__ . '\init' );

/**
 * Plugin translation.
 */
function load_textdomain() {
	\load_plugin_textdomain(
		'ionos-navigation',
		false,
		\dirname( \plugin_basename( __FILE__ ) ) . '/languages/'
	);
}

\add_action( 'init', __NAMESPACE__ . '\load_textdomain' );
