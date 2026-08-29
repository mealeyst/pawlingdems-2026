<?php
/**
 * Plugin Name:  IONOS Help
 * Plugin URI:   https://www.ionos.com
 * Description:  This plugin links provides you with information and guidance to help you navigate through WordPress administration and find support available at IONOS. The complete set of information is available with activated IONOS login to fetch information form IONOS Help Center.
 * Version:      2.2.1
 * License:      GPLv2 or later
 * Author:       IONOS
 * Author URI:   https://www.ionos.com
 * Text Domain:  ionos-help-center
 * Domain Path:  /languages
 */

namespace Ionos\HelpCenter;

use Ionos\HelpCenter\Options;
use Ionos\HelpCenter\Config;
use Ionos\HelpCenter\Warning;
use Ionos\HelpCenter\Updater;


$autoloader = __DIR__ . '/vendor/autoload.php';
if ( is_readable( $autoloader ) ) {
	require_once $autoloader;
}

Options::set_tenant_and_plugin_name('ionos', 'help-center');

Options::clean_up( __FILE__ );

function init() {

	new Manager();
	new Updater();
	new Warning( 'ionos-help-center' );
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\init' );

function load_textdomain() {
	if ( strpos( plugin_dir_path( __FILE__ ), 'mu-plugins' ) !== false ) {
		load_muplugin_textdomain(
			'ionos-help-center',
			basename( dirname( __FILE__ ) ) . '/languages'
		);
	} else {
		load_plugin_textdomain(
			'ionos-help-center',
			false,
			dirname( plugin_basename( __FILE__ ) ) . '/languages/'
		);
	}
}
add_action( 'init', __NAMESPACE__ . '\load_textdomain' );
