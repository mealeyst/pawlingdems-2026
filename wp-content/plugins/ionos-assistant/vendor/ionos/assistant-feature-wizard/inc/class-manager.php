<?php
// phpcs:disable IonosWordPress.Files.FileName.InvalidClassFileName
// phpcs:disable Squiz.Commenting
// phpcs:disable Squiz.Classes.ValidClassName

namespace Ionos\Assistant\Wizard;

use Ionos\Assistant\Config;
use Ionos\Assistant\Options;
use Ionos\Assistant\Wizard\Controllers\Blueprint_Upload;
use Ionos\Assistant\Wizard\Controllers\Completed;
use Ionos\Assistant\Wizard\Controllers\Install;
use Ionos\Assistant\Wizard\Controllers\Abort_Plugin_Selection;
use Ionos\Assistant\Wizard\Controllers\Loop_Consent;
use Ionos\Assistant\Wizard\Controllers\Plugin_Advertising;
use Ionos\Assistant\Wizard\Controllers\Plugin_Selection;
use Ionos\Assistant\Wizard\Controllers\Summary;
use Ionos\Assistant\Wizard\Controllers\Theme_Preview;
use Ionos\Assistant\Wizard\Controllers\Use_Case_Selection;
use Ionos\Assistant\Wizard\Controllers\Welcome;
use Ionos\Assistant\Wizard\Controllers\Theme_Selection;
use Ionos\Blueprint\Controller\Blueprint;
use Ionos\LoginRedirect\LoginRedirect;

class Manager {

	/**
	 * Current URL.
	 *
	 * @var string
	 */
	private static $current_url;

	const STEP_SLUGS = [
		'welcome'                => 'welcome',
		'use_case_selection'     => 'use-case-selection',
		'theme_selection'        => 'theme-selection',
		'theme_preview'          => 'theme-preview',
		'plugin_selection'       => 'plugin-selection',
		'plugin_advertising'     => 'plugin-advertising',
		'loop_consent'           => 'loop-consent',
		'summary'                => 'summary',
		'install'                => 'install',
		'completed'              => 'completed',
		'abort_plugin_selection' => 'abort-plugin-selection',
		'blueprint_upload'       => 'blueprint-upload',
	];

	const STATE_INPUT_NAMES = [
		'use_case'         => 'use_case',
		'theme'            => 'theme',
		'themes'           => 'themes',
		'active_theme'     => 'active_theme',
		'plugins'          => 'plugins',
		'install_promoted' => 'install_promoted',
		'preview_link'     => 'preview_link',
		'themeslist'       => 'themeslist',
		'loop_consent'     => 'loop_consent',
	];

	const TRANSIENTS = [
		'theme_infos'  => [
			'name'     => 'assistant_wizard_theme_infos',
			'duration' => 3600,
		],
		'plugin_infos' => [
			'name'     => 'assistant_wizard_plugin_infos',
			'duration' => 3600,
		],
	];


	const WIZARD_COMPLETED_OPTION_NAME     = 'ionos_assistant_completed';
	const BLUEPRINT_ENABLED_TRANSIENT_NAME = 'ionos_assistant_blueprint_enabled';

	/**
	 * Controller Error indicator.
	 *
	 * @var bool
	 */
	private static $is_controller_error;

	/**
	 * Array with available steps.
	 *
	 * @var array
	 */
	private static $steps = [
		self::STEP_SLUGS['welcome']                => Welcome::class,
		self::STEP_SLUGS['use_case_selection']     => Use_Case_Selection::class,
		self::STEP_SLUGS['theme_selection']        => Theme_Selection::class,
		self::STEP_SLUGS['theme_preview']          => Theme_Preview::class,
		self::STEP_SLUGS['plugin_selection']       => Plugin_Selection::class,
		self::STEP_SLUGS['plugin_advertising']     => Plugin_Advertising::class,
		self::STEP_SLUGS['summary']                => Summary::class,
		self::STEP_SLUGS['install']                => Install::class,
		self::STEP_SLUGS['completed']              => Completed::class,
		self::STEP_SLUGS['abort_plugin_selection'] => Abort_Plugin_Selection::class,
		self::STEP_SLUGS['blueprint_upload']       => Blueprint_Upload::class,
		self::STEP_SLUGS['loop_consent']           => Loop_Consent::class,
	];

	/**
	 * Current active controller.
	 *
	 * @var object
	 */
	private static $current_controller;

	/**
	 * This setup-method is being called earlier than init. Once the tariff
	 * is being set, it is not meant to be changed.
	 *
	 * @return void
	 */
	public static function setup() {
		if ( true === (bool) get_option( self::WIZARD_COMPLETED_OPTION_NAME, false ) ) {
			return;
		}

		self::set_tariff();

		if ( self::has_tariff_feature( 'woocommerce' ) ) {
			add_filter(
				'ionos_library_service_url_before_placeholder_replacement',
				[ __CLASS__, 'set_mode_to_woocommerce' ],
				10,
				4
			);
		}

		if ( is_admin() ) {
			add_filter( 'admin_post_ionos_assistant_blueprint_upload', [ __CLASS__, 'blueprint_handler' ], 200, 3 );
		}

	}

	public static function blueprint_handler() {
		delete_transient( Blueprint_Upload::BLUEPRINT_FILE_TRANSIENT_NAME );

		if ( $_GET['action'] != 'ionos_assistant_blueprint_upload' ) {
			return;
		}

		if ( ! isset( $_FILES['file']['tmp_name'] ) ) {
			return;
		}

		if ( isset( $_FILES['file']['tmp_name'] ) && self::is_blueprint_enabled() === true ) {
			set_transient( Blueprint_Upload::BLUEPRINT_FILE_TRANSIENT_NAME, 'error', 3600 );

			$json_string = $_FILES['file']['tmp_name'];
			$blueprint   = new Blueprint( [ 'debug' => false ] );
			$validation  = $blueprint->decode( file_get_contents( $json_string ) );

			if ( $validation ) {
				set_transient( Blueprint_Upload::BLUEPRINT_FILE_TRANSIENT_NAME, file_get_contents( $_FILES['file']['tmp_name'] ), 3600 );
			}
		}
	}

	public static function set_mode_to_woocommerce( $url, $service, $tenant, $plugin ) {
		if ( 'config' !== $service || 'assistant' !== $plugin ) {
			return $url;
		}
		$url = str_replace( '{mode}', 'woocommerce', $url );

		return $url;
	}

	public static function init() {
		if ( defined( 'WP_CLI' ) && true === WP_CLI ) {
			return;
		}

		self::$current_url = "{$_SERVER['REQUEST_SCHEME']}://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";

		Rest_Api::init();

		if ( ! Config::get( 'features.wizard.enabled' ) ) {
			return;
		}

		delete_transient( self::BLUEPRINT_ENABLED_TRANSIENT_NAME );

		if ( Config::get( 'features.blueprint.enabled' ) ) {
			set_transient( self::BLUEPRINT_ENABLED_TRANSIENT_NAME, true, 3600 );
		}

		if ( isset( $_GET['coupon'] ) ) {
			return;
		}

		LoginRedirect::register_redirect();

		add_action( 'admin_menu', [ __CLASS__, 'add_assistant_page' ] );
		if ( false === (bool) get_option( self::WIZARD_COMPLETED_OPTION_NAME, false ) ) {
			add_filter( 'ionos_login_redirect_to', [ __CLASS__, 'start_wizard' ], 200, 3 );
		}

		add_action(
			'admin_init',
			function() {
				if ( ! current_user_can( 'manage_options' ) ) {
					return;
				}

				if ( ! isset( $_GET['assistant_wizard_completed'] ) ) {
					return;
				}

				update_option( self::WIZARD_COMPLETED_OPTION_NAME, true );
			},
			5
		);

		if ( ! self::is_wizard_page() ) {
			return;
		}

		add_filter(
			'admin_title',
			function ( $admin_title ) {
				if ( ! self::is_wizard_page() || self::$is_controller_error ) {
					return $admin_title;
				}

				$title = array_filter(
					[
						self::$current_controller::get_page_title(),
						__( 'IONOS Assistant', 'ionos-assistant' ),
					]
				);

				return implode( ' – ', $title );
			},
			10
		);

		add_action(
			'admin_init',
			function() {
				if ( ! current_user_can( 'manage_options' ) ) {
					return;
				}

				self::$is_controller_error = ! self::setup_controller();
			},
			5
		);

		add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_wizard_resources' ] );
		add_action( 'admin_enqueue_scripts', [ Custom_CSS::class, 'init' ], 12 );
	}

	/**
	 * The tariff is detected by the presence of a plugin.
	 * A tariff may have multiple tariff features.
	 * Defaults to standard.
	 *
	 * @return void
	 */
	private static function set_tariff() {
		$tariff = (string) get_option( 'ionos_tariff', '' );
		if ( '' !== $tariff ) {
			return;
		}

		if ( true === class_exists( 'WooCommerce', false ) ) {
			update_option( 'ionos_tariff', 'woocommerce' );
			self::add_tariff_feature( 'woocommerce' );
			delete_transient( 'ionos_assistant_config' );
			return;
		}

		update_option( 'ionos_tariff', 'standard' );
	}

	/**
	 * Gets the current step slug.
	 *
	 * @return string
	 */
	public static function get_step_slug() {
		$step = sanitize_key( filter_input( INPUT_GET, 'step', FILTER_SANITIZE_STRING ) );

		if ( in_array( $step, self::STEP_SLUGS, true ) ) {
			return array_search( $step, self::STEP_SLUGS, true );
		}

		return self::STEP_SLUGS['welcome'];
	}

	/**
	 * Get the tariff of the WordPress instance.
	 *
	 * @return string
	 */
	private static function get_tariff() {
		return (string) get_option( 'ionos_tariff', 'standard' );
	}

	/**
	 * Add tariff features to flow.
	 *
	 * @param string $feature Feature name.
	 *
	 * @return void
	 */
	private static function add_tariff_feature( $feature ) {
		$features = (array) get_option( 'ionos_tariff_features', [] );
		array_push( $features, $feature );
		$features = array_unique( $features );
		update_option( 'ionos_tariff_features', $features );
	}

	/**
	 * Check if feature is already added.
	 *
	 * @param string $feature Feature name.
	 *
	 * @return bool
	 */
	private static function has_tariff_feature( $feature ) {
		return in_array( $feature, (array) get_option( 'ionos_tariff_features', [] ), true );
	}

	/**
	 * Check if we are currently on a wizard page.
	 *
	 * @return bool
	 */
	private static function is_wizard_page() {
		return false !== strpos( self::$current_url, 'page=ionos-assistant' );
	}

	/**
	 * Checks if the blueprint transient is enabled.
	 *
	 * @return bool
	 */
	public static function is_blueprint_enabled() {
		return (bool) get_transient( self::BLUEPRINT_ENABLED_TRANSIENT_NAME );
	}

	/**
	 * Determines if a valid step exists and the corresponding controller for that, validates the request params
	 * Returns true if no problem appeared
	 *
	 * @return bool
	 */
	private static function setup_controller() {
		$step = isset( $_GET['step'] ) ? $_GET['step'] : self::STEP_SLUGS['welcome'];

		if ( get_option( self::WIZARD_COMPLETED_OPTION_NAME, false ) ) {
			$step = self::STEP_SLUGS['completed'];
		}

		self::$current_controller = isset( self::$steps[ $step ] ) ? self::$steps[ $step ] : '';
		if ( empty( self::$current_controller ) ) {
			return false;
		}

		if ( ! self::$current_controller::validate_request_params() ) {
			return false;
		}

		self::$current_controller::setup();

		return true;
	}

	/**
	 * Start wizard flow and maybe redirect.
	 *
	 * @param string   $redirect_to Redirect at the end of the wizard flow.
	 * @param string   $origin_redirect_to Redirect params for redirect_to.
	 * @param \WP_User $user Current active user.
	 *
	 * @return string
	 */
	public static function start_wizard( $redirect_to, $origin_redirect_to, $user ) {
		if ( is_wp_error( $user ) ) {
			return $redirect_to;
		}

		if ( ! $user->has_cap( 'manage_options' ) ) {
			return $redirect_to;
		}

		$params = self::get_params_from_url( $origin_redirect_to );
		if ( isset( $params['coupon'] ) ) {
			return $redirect_to;
		}

		return get_admin_url( null, 'admin.php?page=ionos-assistant' );
	}

	/**
	 * Add assistant menu page.
	 */
	public static function add_assistant_page() {
		add_menu_page(
			'Ionos Assistant',
			'Ionos Assistant',
			'administrator',
			'ionos-assistant',
			[
				__CLASS__,
				'show_assistant_page',
			]
		);
		remove_menu_page( 'ionos-assistant' );
	}

	/**
	 * Redirect to different wizard step.
	 *
	 * @param string $step Current wizard step.
	 */
	public static function redirect_to_step( $step ) {
		wp_redirect( get_admin_url( null, "admin.php?page=ionos-assistant&step=$step" ) );
		exit;
	}

	/**
	 * Shows the current active assistant page.
	 */
	public static function show_assistant_page() {
		if ( ! self::is_wizard_page() || self::$is_controller_error ) {
			return;
		}

		self::$current_controller::render();
	}

	/**
	 * Prevent redirects out of wizard.
	 */
	public static function prevent_redirect() {
		add_filter(
			'wp_redirect',
			function( $location ) {
				if ( false !== strpos( $location, 'page=ionos-assistant&step=completed' ) ) {
					return $location;
				}
				return self::$current_url;
			},
			PHP_INT_MAX
		);
	}

	/**
	 * Enqueue all needed resources.
	 *
	 * @param string $hook_suffix Hook suffix.
	 */
	public static function enqueue_wizard_resources( $hook_suffix ) {
		if ( 'toplevel_page_ionos-assistant' !== $hook_suffix ) {
			return;
		}

		wp_enqueue_style(
			'ionos-assistant-wizard',
			plugins_url( 'css/wizard.css', __DIR__ ),
			[],
			filemtime( FEATURE_MAIN_DIR_PATH . '/css/wizard.css' )
		);

		if ( self::BLUEPRINT_ENABLED_TRANSIENT_NAME ) {
			wp_enqueue_script(
				'ionos-assistant-wizard',
				plugins_url( 'js/drag-drop.js', __DIR__ ),
				null,
				filemtime( FEATURE_MAIN_DIR_PATH . '/js/drag-drop.js' ),
				true
			);
		}
	}

	/**
	 * Parse params from url.
	 *
	 * @param string $url URL to parse params from.
	 *
	 * @return array|null
	 */
	private static function get_params_from_url( $url ) {
		$url_query_string = wp_parse_url( $url, PHP_URL_QUERY );
		if ( ! is_string( $url_query_string ) ) {
			return null;
		}

		$params = null;
		wp_parse_str( $url_query_string, $params );

		return $params;
	}
}
