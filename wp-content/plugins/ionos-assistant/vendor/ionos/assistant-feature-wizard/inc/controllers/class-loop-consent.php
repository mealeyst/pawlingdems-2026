<?php
// phpcs:disable IonosWordPress.Files.FileName.InvalidClassFileName
// phpcs:disable Squiz.Commenting 
// phpcs:disable Squiz.Classes.ValidClassName

namespace Ionos\Assistant\Wizard\Controllers;

use Ionos\Assistant\Wizard\Manager;
use Ionos\Assistant\Wizard\Request_Validator;

class Loop_Consent implements View_Controller {

	public static function render() {
		load_template(
			\Ionos\Assistant\Wizard\VIEWS_DIR_PATH . '/loop-consent.php',
			true,
			[
				'counter_text' => ' ',
				'heading_text' => '',
				'next_step'    => 'summary',
			]
		);
	}

	public static function validate_request_params() {
		if ( Abort_Plugin_Selection::is_abort_screen() ) {
			return true;
		}

		return Request_Validator::validate( [ 'use_case', 'theme' ] );
	}

	public static function get_page_title() {
		return __( 'Loop', 'ionos-assistant' );
	}

	public static function setup() {}
}
