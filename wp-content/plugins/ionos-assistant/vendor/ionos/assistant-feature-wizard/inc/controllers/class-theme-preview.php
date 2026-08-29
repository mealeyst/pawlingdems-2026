<?php
// phpcs:disable IonosWordPress.Files.FileName.InvalidClassFileName
// phpcs:disable Squiz.Classes.ValidClassName

namespace Ionos\Assistant\Wizard\Controllers;

use Ionos\Assistant\Config;
use Ionos\Assistant\Wizard\Manager;
use Ionos\Assistant\Wizard\Request_Validator;
use Ionos\Assistant\Wizard\Use_Case;

/**
 * Controller for the theme preview step.
 */
class Theme_Preview implements View_Controller {

	/**
	 * Renders the view.
	 */
	public static function render() {
		$selected_use_case = $_GET[ Manager::STATE_INPUT_NAMES['use_case'] ];
		$selected_theme    = $_GET[ Manager::STATE_INPUT_NAMES['theme'] ];

		$use_case = new Use_Case( Config::get( "features.wizard.usecases.$selected_use_case" ) );
		$infos    = $use_case->retrieve_theme_infos( $selected_theme );
		if ( empty( $infos ) || ! is_array( $infos ) ) {
			return;
		}

		$preview_link = $use_case->has_custom_theme( $selected_theme )
			? $infos[ $selected_theme ]['preview_url']
			: 'https://wp-themes.com/' . $selected_theme;

		$theme_name = $infos[ $selected_theme ]['name'];

		load_template(
			\Ionos\Assistant\Wizard\VIEWS_DIR_PATH . '/theme-preview.php',
			true,
			[
				'counter_text' => __( 'Step 3 of 5', 'ionos-assistant' ),
				'heading_text' => sprintf( /* translators: s=theme name */
					__( 'Theme: %s', 'ionos-assistant' ),
					$theme_name
				),
				'next_step'    => 'plugin-selection',
				'info'         => $infos[ $selected_theme ],
				'theme'        => $selected_theme,
				'use_case'     => $selected_use_case,
				'preview_link' => $preview_link,
			]
		);
	}

	/**
	 * Validates the request parameters.
	 */
	public static function validate_request_params() {
		return Request_Validator::validate( [ 'use_case', 'theme' ] );
	}

	/**
	 * Provides the page title.
	 */
	public static function get_page_title() {
		return __( 'Theme preview', 'ionos-assistant' );
	}

	/**
	 * Initializes the controller. Not used yet.
	 */
	public static function setup() {
		// TODO: Implement setup() method.
	}
}
