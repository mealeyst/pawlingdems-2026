<?php

namespace Ionos\Assistant\Wizard\Controllers;

use Ionos\Assistant\Wizard\Manager;
use Blueprint\Model\Blueprint;

class Welcome implements View_Controller {

	public static function render() {

		load_template(
			\Ionos\Assistant\Wizard\VIEWS_DIR_PATH . '/welcome.php',
			true,
			[
				'counter_text' => ' ',
				'heading_text' => __( 'Welcome to WordPress by IONOS', 'ionos-assistant' ),
				'next_step'    => 'use-case-selection',
			]
		);
	}


	public static function validate_request_params() {
		return true;
	}

	public static function get_page_title() {
		return '';
	}

	public static function setup() {
		// Reset state if welcome step is displayed.
		delete_transient( Blueprint_Upload::BLUEPRINT_FILE_TRANSIENT_NAME );
		delete_transient( 'abort_screen_active' );
		delete_option( Install::INSTALL_COMPONENTS_OPTION_NAME );
	}
}
