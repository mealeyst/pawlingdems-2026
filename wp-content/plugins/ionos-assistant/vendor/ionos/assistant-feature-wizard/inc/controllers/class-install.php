<?php
// phpcs:disable IonosWordPress.Files.FileName.InvalidClassFileName
// phpcs:disable Squiz.Commenting
// phpcs:disable Squiz.Classes.ValidClassName

namespace Ionos\Assistant\Wizard\Controllers;

use Ionos\Assistant\Wizard\Manager;
use Ionos\Assistant\Config;
use Ionos\Assistant\Wizard\Loop;
use Ionos\Assistant\Wizard\Market_Helper;
use Ionos\Assistant\Wizard\Rest_Api;
use Ionos\Assistant\Wizard\Theme;
use Ionos\Assistant\Wizard\Use_Case;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;

class Install implements View_Controller {

	public static $is_pre_install_view;
	private static $install_data;

	const INSTALL_COMPONENTS_OPTION_NAME = 'assistant_wizard_install_data';

	public static function render() {
		load_template(
			\Ionos\Assistant\Wizard\VIEWS_DIR_PATH . '/install.php',
			true,
			[
				'counter_text'        => ' ',
				'heading_text'        => __( 'Installation in progress', 'ionos-assistant' ),
				'next_step'           => 'loop-consent',
				'install_data'        => self::$install_data,
				'is_pre_install_view' => self::$is_pre_install_view,
			]
		);
	}

	public static function validate_request_params() {
		return true;
	}

	public static function get_page_title() {
		return __( 'Plugin selection', 'ionos-assistant' );
	}

	public static function setup() {
		Manager::prevent_redirect();

		self::$install_data = get_option( self::INSTALL_COMPONENTS_OPTION_NAME, false );
		if ( self::$install_data ) {
			self::process_install_data();
			return;
		}

		self::$is_pre_install_view = true;

		add_action(
			'admin_head',
			function() {
				echo '<meta http-equiv="refresh" content="5">';
			}
		);

		if ( Abort_Plugin_Selection::is_abort_screen() ) {
			self::handle_abort_flow();
			return;
		}

		if ( ! empty( Blueprint_Upload::get_transient_content() && Manager::is_blueprint_enabled() ) ) {
			self::handle_blueprint_flow();
			return;
		}

		$selected_use_case = $_GET[ Manager::STATE_INPUT_NAMES['use_case'] ];
		$selected_theme    = $_GET[ Manager::STATE_INPUT_NAMES['theme'] ];

		$use_case_info = Config::get( 'features.wizard.usecases.' . $selected_use_case );
		$theme_info    = $use_case_info['themes'][ $selected_theme ];

		$use_case = new Use_Case( $use_case_info );
		$theme    = new Theme( $theme_info );

		$required_plugins = array_merge(
			$use_case->get_required_plugins(),
			$theme->get_required_plugins()
		);

		$optional_plugins = array_merge(
			$use_case->get_recommended_plugins(),
			$theme->get_recommended_plugins()
		);

		if ( ! isset( $_GET['plugins'] ) ) {
			$_GET['plugins'] = [];
		}
		$optional_plugins = array_intersect_key( $optional_plugins, array_flip( $_GET['plugins'] ) );

		$plugins = array_merge( $required_plugins, $optional_plugins );

		if ( isset( $_GET['install_promoted'] ) && Plugin_Advertising::validate_promoted_plugin() ) {
			$plugins = array_merge( $plugins, Config::get( 'features.wizard.promotedPlugin' ) );
		}

		if ( isset( $_GET['loop_consent'] ) ) {
			$plugins['ionos-loop']['download_url'] = 'https://s3-de-central.profitbricks.com/web-hosting/ionos/ionos-loop.latest.zip';
			self::$install_data['loop_consent']    = true;
		}

		self::$install_data = [
			'total'       => count( $plugins ) + 1,
			'theme'       => [ $selected_theme => $theme_info ],
			'plugins'     => $plugins,
			'wizard_data' => [
				'use_case' => $selected_use_case ? $selected_use_case : 'none',
				'flow'     => 'wizard',
			],
			'use_case_settings' => isset( $use_case_info['settings'] ) ? $use_case_info['settings'] : [],
		];

		update_option( self::INSTALL_COMPONENTS_OPTION_NAME, self::$install_data );
	}

	private static function process_install_data() {
		$token = bin2hex( random_bytes( 16 ) ); // Works with PHP 5.6 due to WP polyfill.
		update_option( Rest_Api::TOKEN_OPTION_NAME, $token );
		$url      = esc_url_raw( rest_url( Rest_Api::API_NAMESPACE . '/process-install-data' ) );
		$url      = preg_replace( '/(.*)localhost:(?:[0-9]+)(.*)/', '$1localhost:80$2', $url );
		$response = wp_remote_post(
			$url,
			[
				'headers' => [
					Rest_Api::TOKEN_HEADER_NAME => $token,
				],
				'timeout' => 300,
			]
		);

		if ( is_wp_error( $response ) ) {
			// TODO: What to do if there was an error, show maybe an error page?
			return;
		}

		$body = json_decode( wp_remote_retrieve_body( $response ) );

		delete_option( Rest_Api::TOKEN_OPTION_NAME );

		if ( isset( $body->message->status ) && ( $body->message->status < 200 || $body->message->status >= 300 ) ) {
			error_log( 'API not available, see class-install.php' );
			wp_die( esc_html( 'API not available.', 'ionos-assistant' ) );
		}

		if ( isset( $body->processing_completed ) ) {
			Manager::redirect_to_step( Manager::STEP_SLUGS['completed'] );
		}

		add_action(
			'admin_head',
			function() {
				echo '<meta http-equiv="refresh" content="0">';
			}
		);
	}

	public static function get_text( $string ) {
		return "Only $string components have to be installed.";
	}

	/**
	 * Handle logic of the abort flow.
	 */
	private static function handle_abort_flow() {
		$plugins = [];

		$plugins_info = Config::get( 'features.wizard.usecases.plugins' );
		if ( empty( $plugins_info ) ) {
			$plugins_info = Config::get( 'features.wizard.plugins' );
		}
		$plugins_info = Market_Helper::filter_assets_by_market( $plugins_info );

		if ( ! empty( $_GET[ Manager::STATE_INPUT_NAMES['plugins'] ] ) ) {
			$plugins = array_flip( $_GET[ Manager::STATE_INPUT_NAMES['plugins'] ] );
		}

		foreach ( $plugins as $key => $value ) {
			if ( ! empty( $plugins_info[ $key ] ) ) {
				unset( $plugins[ $key ] );
				$plugins[ $key ]['download_url'] = $plugins_info[ $key ]['download_url'];
			}
		}

		if ( isset( $_GET['install_promoted'] ) ) {
			if ( Plugin_Advertising::validate_promoted_plugin() ) {
				$plugins = array_merge( $plugins, Config::get( 'features.wizard.promotedPlugin' ) );
			}
		}

		if ( isset( $_GET['loop_consent'] ) ) {
			$plugins['ionos-loop']['download_url'] = 'https://s3-de-central.profitbricks.com/web-hosting/ionos/ionos-loop.latest.zip';
			self::$install_data['loop_consent']    = true;
		}

		self::$install_data = [
			'total'   => count( $plugins ),
			'plugins' => $plugins,
			'wizard_data'       => [
				'use_case' => 'none',
				'flow'     => 'abort',
			],
		];

		delete_transient( Abort_Plugin_Selection::ABORT_SCREEN_TRANSIENT_NAME );
		update_option( self::INSTALL_COMPONENTS_OPTION_NAME, self::$install_data );
	}

	/**
	 * Handle logic of the blueprint flow.
	 */
	public static function handle_blueprint_flow() {
		self::$install_data = [
			'total'        => count( $_GET[ Manager::STATE_INPUT_NAMES['plugins'] ] ),
			'plugins'      => array_flip( $_GET[ Manager::STATE_INPUT_NAMES['plugins'] ] ),
			'themes'       => array_flip( $_GET[ Manager::STATE_INPUT_NAMES['themes'] ] ),
			'active_theme' => $_GET[ Manager::STATE_INPUT_NAMES['active_theme'] ],
			'wizard_data'       => [
				'use_case' => 'none',
				'flow'     => 'blueprint',
			],
		];

		update_option( self::INSTALL_COMPONENTS_OPTION_NAME, self::$install_data );
	}
}
