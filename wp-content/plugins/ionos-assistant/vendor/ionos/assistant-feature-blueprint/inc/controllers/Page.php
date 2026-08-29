<?php
namespace Ionos\Blueprint\Controllers;

/**
 * The Controller class for the admin page.
 */
class Page {
	/**
	 * Renders the page in the backend.
	 */
	public static function render() {
		wp_enqueue_style( 'admin-styles', plugins_url( 'dist/css/style.css', PLUGIN_FILE ), null, filemtime( PLUGIN_DIR . '/dist/css/style.css' ) );
		wp_enqueue_script( 'ionos-blueprint', plugins_url( 'dist/js/index.js', PLUGIN_FILE ), [], filemtime( PLUGIN_DIR . '/dist/js/index.js' ), true );

		$vars = [
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'ionos-blueprint-nonce' ),
			'messages' => [
				'error'        => __( 'An error occurred.', 'ionos-blueprint' ),
				'parent_theme' => __( 'Parent theme', 'ionos-blueprint' ),
			],
		];
		wp_localize_script( 'ionos-blueprint', 'ionosBlueprint', $vars );

		load_template(
			PLUGIN_DIR . '/inc/views/page.php',
			true,
			[
				'themes'  => wp_get_themes(),
				'plugins' => get_plugins(),
			]
		);
	}
}
