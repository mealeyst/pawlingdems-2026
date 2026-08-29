<?php

namespace Ionos\Assistant\Banner;

use Ionos\Assistant\Options;

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

Options::set_tenant_and_plugin_name( 'ionos', 'assistant' );

const FEATURE_MAIN_PLUGIN_FILE_PATH = __DIR__ . '/assistant-feature-banner.php';
const FEATURE_MAIN_DIR_PATH = __DIR__;
try {
	define( __NAMESPACE__ . '\MAIN_PLUGIN_FILE_PATH', Options::get_main_plugin_file_path( FEATURE_MAIN_PLUGIN_FILE_PATH ) );
	define( __NAMESPACE__ . '\BASENAME', Options::get_main_plugin_file_basename( FEATURE_MAIN_PLUGIN_FILE_PATH ) );
} catch ( Exception $e ) {
	wp_die( $e->getMessage() );
}

Options::clean_up( MAIN_PLUGIN_FILE_PATH );

function init() {
	$manager = new Manager();
	$manager->init();
}

add_action( 'init', __NAMESPACE__ . '\init' );
