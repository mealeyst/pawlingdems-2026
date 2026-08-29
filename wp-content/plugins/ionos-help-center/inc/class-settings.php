<?php

namespace Ionos\Help_Center;

// Do not allow direct access!
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class Help_Center_Settings
 */
class Settings {

	public function __construct() {
		if ( is_admin() ) {
			add_action( 'ionos_assistant_register_settings', array( $this, 'register_settings' ), 10, 2 );
		}
	}

	/**
	 * creates settings option for IONOS Assistant Settings Page
	 *
	 * @param $options_group_id
	 * @param $branding_data
	 *
	 * @return null
	 */
	public function register_settings( $options_group_id, $branding_data ) {
		if ( ! isset( $branding_data['name'] ) ) {
			return null;
		}

		register_setting(
			$options_group_id,
			'ionos_help_center_main_enabled',
			array( 'default' => Config::get( 'main.enabled' ) )
		);

		add_settings_section(
			'ionos_help_center_integration_settings',
			'',
			'',
			'ionos_assistant_settings_plugin'
		);

		add_settings_field(
			'ionos_help_center_main_enabled',
			sprintf(
				__( '%s - Help', 'ionos-help-center' ),
				$branding_data['name']
			),
			array( $this, 'help_center_settings' ),
			'ionos_assistant_settings_plugin',
			'ionos_help_center_integration_settings',
			array(
				'branding_data' => $branding_data,
			)
		);
	}

	/**
	 * return setings html
	 *
	 * @param $args
	 */
	public function help_center_settings( $args ) {
		$option = Config::get('main.enabled');

		echo '<label for="ionos_help_center_main_enabled">';
		echo '<input  id="ionos_help_center_main_enabled" name="ionos_help_center_main_enabled" type="checkbox" value="1" ' . checked( '1', $option, false ) . ' />';
		echo sprintf( __( 'Use %s help', 'ionos-help-center' ), $args['branding_data']['name'] . '</label>' );
	}
}
