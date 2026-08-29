<?php
namespace Ionos\Blueprint;

Controllers\Plugin::init( __DIR__, __FILE__ );

add_action(
	'admin_menu',
	function () {
		add_menu_page(
			'Blueprints',
			'Blueprints',
			'export',
			'blueprints',
			[ 'Ionos\Blueprint\Controllers\Page', 'render' ]
		);
	}
);

add_action( 'wp_ajax_ionos-blueprint-generate', [ new Controllers\Action(), 'download' ] );
