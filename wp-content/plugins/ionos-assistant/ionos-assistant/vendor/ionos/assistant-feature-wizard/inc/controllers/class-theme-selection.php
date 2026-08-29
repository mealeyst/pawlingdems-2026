<?php

namespace Ionos\Assistant\Wizard\Controllers;

use Ionos\Assistant\Wizard\Manager;
use Ionos\Assistant\Config;
use Ionos\Assistant\Wizard\Request_Validator;
use Ionos\Assistant\Wizard\Use_Case;

class Theme_Selection implements View_Controller {

	public static function render() {
		$selected_use_case = isset( $_GET['use_case'] ) ? strtolower( $_GET['use_case'] ) : null;
		if ( empty( $selected_use_case ) ) {
			return;
		}

		$use_case = new Use_Case( Config::get( "features.wizard.usecases.$selected_use_case" ) );
		$infos    = $use_case->retrieve_theme_infos();
		if ( empty( $infos ) || ! is_array( $infos ) ) {
			return;
		}

		load_template(
			\Ionos\Assistant\Wizard\VIEWS_DIR_PATH . '/theme-selection.php',
			true,
			[
				'counter_text' => __( 'Step 2 of 5', 'ionos-assistant' ),
				'heading_text' => __( 'Choose your theme', 'ionos-assistant' ),
				'themes'       => $infos,
				'next_step'    => Manager::STEP_SLUGS['theme_preview'],
			]
		);
	}

	public static function validate_request_params() {
		return Request_Validator::validate( [ 'use_case' ] );
	}

	public static function get_page_title() {
		return __( 'Theme selection', 'ionos-assistant' );
	}

	public static function setup() {
		// TODO: Implement setup() method.
	}
}
