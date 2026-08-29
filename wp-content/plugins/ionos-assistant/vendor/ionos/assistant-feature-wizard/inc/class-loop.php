<?php

namespace Ionos\Assistant\Wizard;

use Ionos\Assistant\Wizard\Controllers\Abort_Plugin_Selection;
use Ionos\Assistant\Wizard\Controllers\Blueprint_Upload;

/**
 * Class Loop
 *
 * @package Ionos\Assistant\Wizard
 */
class Loop {
	/**
	 * Inits a custom store and adds the data for assistant wizard
	 *
	 * @param array $data The data to add.
	 */
	public static function add_data( $data ) {
		do_action( 'ionos_loop_init_custom_store', 'wizard' );

		do_action( 'ionos_loop_wizard_update', 'flow', $data['wizard_data']['flow'] );
		do_action( 'ionos_loop_wizard_update', 'use_case', $data['wizard_data']['use_case'] );

		$themes = [];
		foreach ( $data['theme'] as $slug => $theme_data ) {
			$themes[] = [
				'slug' => sanitize_key( $slug ),
			];
		}
		if ( empty( $themes ) ) {
			$themes[] = [
				'slug' => '- none -',
			];
		}
		do_action( 'ionos_loop_wizard_update', 'themes', $themes );

		$plugins = [];
		foreach ( $data['plugins'] as $slug => $plugin_data ) {
			$plugins[] = [
				'slug' => sanitize_key( $slug ),
			];
		}
		// $plugins will never be empty, it will always contain ionos-loop.
		do_action( 'ionos_loop_wizard_update', 'plugins', $plugins );
	}
}
