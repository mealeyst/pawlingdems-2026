<?php

namespace Ionos\Assistant\Wizard\Controllers;

use Ionos\Assistant\Wizard\Manager;

class Blueprint_Upload implements View_Controller {
	const BLUEPRINT_FILE_TRANSIENT_NAME = 'ionos_assistant_blueprint_file';

	public static function render() {
		load_template(
			\Ionos\Assistant\Wizard\VIEWS_DIR_PATH . '/blueprint-upload.php',
			true,
			[
				'counter_text' => __( 'Step 1 of 2', 'ionos-assistant' ),
				'heading_text' => __( 'Upload your Blueprint file', 'ionos-assistant' ),
				'next_step'    => 'summary',
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
		// TODO: Implement setup() method.
	}

	/**
	 * Gets the transient content.
	 *
	 * @return string
	 */
	public static function get_transient_content() {
		return (string) get_transient( self::BLUEPRINT_FILE_TRANSIENT_NAME );
	}
}
