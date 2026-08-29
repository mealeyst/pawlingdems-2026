<?php

namespace Ionos\Assistant\Wizard;

use Ionos\Assistant\Options;
use Ionos\Assistant\Wizard\Controllers\Abort_Plugin_Selection;
use Ionos\Assistant\Wizard\Controllers\Blueprint_Upload;
use Ionos\Assistant\Wizard\Controllers\Install;
use Ionos\PluginStateHookHandler\PluginState;

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

const FEATURE_MAIN_PLUGIN_FILE_PATH = __DIR__ . '/assistant-feature-wizard.php';
const FEATURE_MAIN_DIR_PATH         = __DIR__;
const VIEWS_DIR_PATH                = FEATURE_MAIN_DIR_PATH . '/inc/views';

Options::set_tenant_and_plugin_name( 'ionos', 'assistant' );

try {
	define( __NAMESPACE__ . '\MAIN_PLUGIN_FILE_PATH', Options::get_main_plugin_file_path( FEATURE_MAIN_PLUGIN_FILE_PATH ) );
	define( __NAMESPACE__ . '\MAIN_PLUGIN_BASENAME', Options::get_main_plugin_file_basename( FEATURE_MAIN_PLUGIN_FILE_PATH ) );
} catch ( \Exception $e ) {
	wp_die( esc_html( $e->getMessage() ) );
}

/**
 * Registers the clean up hooks for different features
 */
function register_clean_up_hooks() {
	$transients = [
		Blueprint_Upload::BLUEPRINT_FILE_TRANSIENT_NAME,
		Manager::BLUEPRINT_ENABLED_TRANSIENT_NAME,
		Abort_Plugin_Selection::ABORT_SCREEN_TRANSIENT_NAME,
	];
	foreach ( Manager::TRANSIENTS as $transient ) {
		$transients[] = $transient['name'];
	}

	( new PluginState( MAIN_PLUGIN_FILE_PATH ) )
		->register_cleanup_hooks()
		->remove_options_on_uninstall(
			[
				Install::INSTALL_COMPONENTS_OPTION_NAME,
				Manager::WIZARD_COMPLETED_OPTION_NAME,
				Rest_Api::TOKEN_OPTION_NAME,
			]
		)
		->remove_transients_on_uninstall( $transients );
}
register_clean_up_hooks();

/**
 * Initializes the manager
 */
function init() {
	Manager::init();
}
add_action( 'init', __NAMESPACE__ . '\init', 9 );

/**
 * Set ups the manager
 */
function setup() {
	Manager::setup();
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\setup' );
