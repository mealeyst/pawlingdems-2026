<?php

namespace Ionos\Assistant\Wizard\Controllers;

use Ionos\Assistant\Wizard\Manager;

class Completed implements View_Controller {

	public static function render() {
		load_template( \Ionos\Assistant\Wizard\VIEWS_DIR_PATH . '/completed.php', true );
	}

	public static function validate_request_params() {
		return true;
	}

	public static function get_page_title() {
		return __( 'Setup completed', 'ionos-assistant' );
	}

	public static function setup() {
		delete_transient( Blueprint_Upload::BLUEPRINT_FILE_TRANSIENT_NAME );
		delete_transient( Manager::BLUEPRINT_ENABLED_TRANSIENT_NAME );

		wp_redirect( admin_url() );
		exit;
	}
}
