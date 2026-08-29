<?php

namespace Ionos\Blueprint;

use Ionos\Assistant\Config;
use Ionos\Assistant\Options;
use Ionos\Assistant\Menu;

Options::set_tenant_and_plugin_name( 'ionos', 'assistant' );
Options::get_main_plugin_file_path( __DIR__ . '/assistant-feature-blueprint.php' );

if ( Config::get( 'features.blueprint.enabled' ) !== false ) {
	Controllers\Plugin::init( __DIR__, __FILE__ );

	add_action(
		'admin_menu',
		function () {
			Menu::add_submenu_page( 'Blueprint', 'Blueprint', 'export', 'blueprint', [
				'Ionos\Blueprint\Controllers\Page',
				'render'
			], 10 );
		}
	);

	add_action( 'wp_ajax_ionos-blueprint-generate', [ new Controllers\Action(), 'download' ] );
}
