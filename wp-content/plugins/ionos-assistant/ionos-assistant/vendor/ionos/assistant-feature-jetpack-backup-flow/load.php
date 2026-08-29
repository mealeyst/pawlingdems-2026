<?php

namespace Ionos\Assistant\JetpackBackupFlow;

use Ionos\Assistant\Options;
use Exception;
use Ionos\PluginStateHookHandler\PluginState;

const FEATURE_MAIN_PLUGIN_FILE_PATH = __DIR__ . '/assistant-feature-jetpack-backup-flow.php';
const FEATURE_MAIN_DIR_PATH         = __DIR__;
const VIEWS_DIR_PATH                = __DIR__ . '/inc/views';
const INSTALL_JETPACK_OPTION_NAME   = 'assistant_jetpack_backup_flow_pending';

Options::set_tenant_and_plugin_name( 'ionos', 'assistant' );

try {
	define( __NAMESPACE__ . '\MAIN_PLUGIN_FILE_PATH', Options::get_main_plugin_file_path( FEATURE_MAIN_PLUGIN_FILE_PATH ) );
} catch ( Exception $e ) {
	wp_die( $e->getMessage() );
}

Options::clean_up( MAIN_PLUGIN_FILE_PATH );

( new PluginState( MAIN_PLUGIN_FILE_PATH ) )
	->register_cleanup_hooks()
	->remove_options_on_uninstall( [ INSTALL_JETPACK_OPTION_NAME ] );

function init() {
	Manager::init();
}

add_action( 'init', __NAMESPACE__ . '\init' );
