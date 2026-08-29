<?php

namespace Ionos\Assistant\LoginRedesign;

use Ionos\Assistant\Options;
use Exception;

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

const FEATURE_MAIN_PLUGIN_FILE_PATH = __DIR__ . '/assistant-feature-login-redesign.php';
const FEATURE_MAIN_DIR_PATH         = __DIR__;

Options::set_tenant_and_plugin_name( 'ionos', 'assistant' );

try {
	define( __NAMESPACE__ . '\MAIN_PLUGIN_FILE_PATH', Options::get_main_plugin_file_path( FEATURE_MAIN_PLUGIN_FILE_PATH ) );
} catch ( Exception $e ) {
	wp_die( $e->getMessage() );
}

Options::clean_up( MAIN_PLUGIN_FILE_PATH );

function init() {
	Manager::init();
}

add_action( 'init', __NAMESPACE__ . '\init' );
