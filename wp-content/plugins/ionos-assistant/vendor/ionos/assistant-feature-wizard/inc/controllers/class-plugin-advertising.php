<?php
// phpcs:disable IonosWordPress.Files.FileName.InvalidClassFileName
// phpcs:disable Squiz.Commenting 
// phpcs:disable Squiz.Classes.ValidClassName

namespace Ionos\Assistant\Wizard\Controllers;

use Ionos\Assistant\Config;
use Ionos\Assistant\Wizard\Manager;
use Ionos\Assistant\Wizard\Request_Validator;
use Ionos\Assistant\Wizard\Wp_Org_Api;

class Plugin_Advertising implements View_Controller {

	private static $is_valid_promoted_plugin;

	public static function render() {
		if ( ! self::$is_valid_promoted_plugin ) {
			// Todo: Load error template.
			exit;
		}

		$promoted_plugin = Config::get( 'features.wizard.promotedPlugin' );
		$promoted_plugin = $promoted_plugin[ array_key_first( $promoted_plugin ) ]; // Works since PHP 7.3 or with polyfill since WP 5.9
		$next_step       = 'summary';
		if ( Abort_Plugin_Selection::is_abort_screen() ) {
			$next_step = 'install';
		}

		load_template(
			\Ionos\Assistant\Wizard\VIEWS_DIR_PATH . '/plugin-advertising.php',
			true,
			[
				'counter_text' => ' ',
				'heading_text' => '',
				'next_step'    => $next_step,
				'plugin'       => $promoted_plugin,
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
		return __( 'Jetpack', 'ionos-assistant' );
	}

	public static function validate_promoted_plugin() {
		$promoted_plugin = Config::get( 'features.wizard.promotedPlugin' );

		if ( empty( $promoted_plugin ) || ! is_array( $promoted_plugin ) ) {
			return false;
		}

		$slug            = array_key_first( $promoted_plugin ); // Works since PHP 7.3 or with polyfill since WP 5.9
		$promoted_plugin = $promoted_plugin[ $slug ];

		$required_array_keys = [
			'headline',
			'name',
			'description',
			'image',
		];

		foreach ( $required_array_keys as $key ) {
			if ( ! array_key_exists( $key, $promoted_plugin ) ) {
				return false;
			}
		}

		if ( array_key_exists( 'download_url', $promoted_plugin ) ) {
			$response = wp_remote_head( $promoted_plugin[ $promoted_plugin['download_url'] ] );
			if ( is_wp_error( $response ) ) {
				return false;
			}

			if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
				return false;
			}

			return true;
		}

		return ! empty( Wp_Org_Api::get_plugin_infos( [ $slug ] ) );
	}

	public static function setup() {
		self::$is_valid_promoted_plugin = self::validate_promoted_plugin();
	}
}
