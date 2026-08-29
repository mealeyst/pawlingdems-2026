<?php

namespace Ionos\Assistant\JetpackBackupFlow\Controllers;

use const Ionos\Assistant\JetpackBackupFlow\VIEWS_DIR_PATH;

class Confirm implements ViewController {
	public static function setup() {}

	public static function render() {
		load_template( VIEWS_DIR_PATH . '/confirm.php', true );
	}

	public static function get_page_title() {
		return __( 'Confirm Jetpack installation', 'ionos-assistant' );
	}
}