<?php

namespace Ionos\Assistant\Wizard\Controllers;

use Ionos\Assistant\Config;
use Ionos\Assistant\Options;
use Ionos\Assistant\Wizard\Manager;

class Use_Case_Selection implements View_Controller {

	public static function render() {
		$use_cases = Config::get( 'features.wizard.usecases' );

		if ( empty( $use_cases ) || ! is_array( $use_cases ) ) {
			return;
		}

		if ( Options::get_installation_mode() === 'woocommerce' ) {
			$heading = esc_html__( 'What would you like to sell?', 'ionos-assistant' );
			$description = esc_html__( 'Tell us what you plan to sell with your new online shop. The Assistant will use this info to suggest fitting Designs and useful Plugins. But don‘t worry, this is not a final decision. You can change every aspect of your site later or pick a custom configuration.', 'ionos-assistant' );
		} else {
			$heading = esc_html__( 'What would you like to build?', 'ionos-assistant' );
			$description = esc_html__( 'Tell us what you plan to do with your new website. The Assistant will use this info to suggest fitting Designs and useful Plugins. But don’t worry, this is not a final decision. You can change every aspect of your site later.', 'ionos-assistant' );
		}

		load_template(
			\Ionos\Assistant\Wizard\VIEWS_DIR_PATH . '/use-case-selection.php',
			true,
			[
				'counter_text' => __( 'Step 1 of 5', 'ionos-assistant' ),
				'heading_text' => $heading,
				'description'  => $description,
				'use_cases'    => $use_cases,
				'next_step'    => Manager::STEP_SLUGS['theme_selection'],
			]
		);
	}

	public static function validate_request_params() {
		return true;
	}

	public static function get_page_title() {
		return __( 'Use case selection', 'ionos-assistant' );
	}

	public static function setup() {
		// TODO: Implement setup() method.
	}
}
