<?php
// phpcs:disable IonosWordPress.Files.FileName.InvalidClassFileName
// phpcs:disable Squiz.Commenting
// phpcs:disable Squiz.Classes.ValidClassName

namespace Ionos\Assistant\Wizard\Controllers;

use Ionos\Assistant\Config;
use Ionos\Assistant\Wizard\Loop;
use Ionos\Assistant\Wizard\Market_Helper;
use Ionos\Assistant\Wizard\Wp_Org_Api;

class Abort_Plugin_Selection implements View_Controller {
	private static $filtered_plugins;
	private static $plugin_infos;
	const ABORT_SCREEN_TRANSIENT_NAME = 'abort_screen_active';

	public static function render() {
		set_transient( self::ABORT_SCREEN_TRANSIENT_NAME, true, 3600 );

		$plugins = Config::get( 'features.wizard.usecases.plugins' );
		if ( empty( $plugins ) ) {
			$plugins = Config::get( 'features.wizard.plugins' );
		}
		$plugins = Market_Helper::filter_assets_by_market( $plugins );

		// Remove Woocommerce plugins from Abort Plugin Selection list.
		unset( $plugins['woocommerce'] );
		unset( $plugins['woocommerce-german-market-light'] );

		$next_step          = ( Config::get( 'features.loop.enabled' ) ) ? 'loop-consent' : 'summary';
		self::$plugin_infos = Wp_Org_Api::get_plugin_infos( array_keys( $plugins ) );

		// Filters for Plugins which contain more information ( like name, description, logo ) than a simple slug.
		self::$filtered_plugins = array_filter( array_map( 'array_filter', $plugins ) );
		self::$plugin_infos = array_replace( self::$plugin_infos, self::$filtered_plugins );

		load_template(
			\Ionos\Assistant\Wizard\VIEWS_DIR_PATH . '/abort-plugin-selection.php',
			true,
			[
				'counter_text' => __( 'Step 1 of 1', 'ionos-assistant' ),
				'heading_text' => __( 'Pick some plugins', 'ionos-assistant' ),
				'next_step'    => $next_step,
				'plugins'      => $plugins,
				'plugin_infos' => self::$plugin_infos,
			]
		);
	}

	public static function validate_request_params() {
		return true;
	}


	public static function get_page_title() {
		return __( 'Abort Plugin selection', 'ionos-assistant' );
	}

	public static function setup() {
		if ( ! Config::get( 'features.wizard.abortFlow.enabled' ) ) {
			$skip_url = add_query_arg(
				[
					'assistant_wizard_completed' => '1',
				],
				get_admin_url()
			);
			wp_redirect( $skip_url );
			exit;
		}
	}

	public static function get_plugin_name( $slug ) {
		$paths = [
			"features.wizard.plugins.$slug.name",
		];

		foreach ( $paths as $path ) {
			$name = Config::get( $path );
			if ( $name ) {
				return $name;
			}
		}

		return isset( self::$plugin_infos[ $slug ]['name'] ) ? self::$plugin_infos[ $slug ]['name'] : 'No Plugin name';
	}

	public static function get_plugin_description( $slug ) {
		$paths = [
			"features.wizard.plugins.$slug.description",
		];

		foreach ( $paths as $path ) {
			$description = Config::get( $path );
			if ( $description ) {
				return $description;
			}
		}

		if ( ! empty( self::$plugin_infos[ $slug ]['short_description'] ) ) {
			return self::$plugin_infos[ $slug ]['short_description'];
		}

		if ( ! empty( self::$plugin_infos[ $slug ]['description'] ) ) {
			return self::$plugin_infos[ $slug ]['description'];
		}

		return 'No plugin description is available';
	}

	public static function get_plugin_version( $slug ) {
		$paths = [
			"features.wizard.plugins.$slug.version",
		];

		foreach ( $paths as $path ) {
			$version = Config::get( $path );
			if ( $version ) {
				return $version;
			}
		}

		return isset( self::$plugin_infos[ $slug ]['version'] ) ? self::$plugin_infos[ $slug ]['version'] : 'not available';
	}

	public static function get_plugin_author( $slug ) {
		$paths = [
			"features.wizard.plugins.$slug.author",
		];

		foreach ( $paths as $path ) {
			$author = Config::get( $path );
			if ( $author ) {
				return $author;
			}
		}

		return isset( self::$plugin_infos[ $slug ]['author'] ) ? self::$plugin_infos[ $slug ]['author'] : 'No author available';
	}

	/**
	 * Checks if the abort screen transient is present.
	 *
	 * @return bool
	 */
	public static function is_abort_screen() {
		return (bool) get_transient( self::ABORT_SCREEN_TRANSIENT_NAME );
	}
}
