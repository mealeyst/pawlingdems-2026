<?php
// Do not allow direct access!
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Forbidden' );
}

use Ionos\Assistant\Config;
use Ionos\Assistant\Options;

class Ionos_Assistant_Dashboard {

	/**
	 * Precondition: Assistant has already been completed or not
	 *
	 * @var boolean
	 */
	private $is_assistant_completed;

	/**
	 * Precondition: can the user use the Assistant & edit things
	 *
	 * @var boolean
	 */
	private $current_user_can;

	/**
	 * Precondition: on which WP Admin page we are (WP screen ID)
	 *
	 * @var string
	 */
	private $wp_current_screen;

	/**
	 * Precondition: plugins from user (with their active status)
	 *
	 * @var boolean[string]
	 */
	private $wp_plugins = array();

	/**
	 * Precondition: Custom blog feeds
	 *
	 * @var string
	 */
	private $custom_blog_feed;

	/**
	 * Ionos_Assistant_Dashboard constructor.
	 */
	public function __construct() {

		// Configure AJAX hook for the Dashboard actions
		add_action( 'wp_ajax_ajaxgmarket', array( 'Ionos_Assistant_Dashboard', 'setup_german_market' ) );
		add_action( 'wp_ajax_ajaxceditor', array( 'Ionos_Assistant_Dashboard', 'setup_classic_editor' ) );

		// Enqueue scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Enqueue styles
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );

		// Init params on loading page
		add_action( 'load-index.php', array( $this, 'configure' ) );
		add_action( 'load-plugins.php', array( $this, 'configure' ) );

		// Remove WP standard elements
		add_action( 'load-index.php', array( $this, 'hide_welcome_panel' ) );
		add_action( 'admin_head', array( $this, 'hide_welcome_panel_option' ) );

		// Add Dashboard custom elements
		add_action( 'admin_notices', array( $this, 'render_dashboard_panels' ) );

		// Add Dashboard custom WP widgets
		add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widgets' ) );

		// Enqueue actions requested by URI parameter(s)
		add_action( 'admin_head', array( $this, 'handle_dashboard_params' ) );
	}

	/**
	 * Configure the Dashboard params in the WP admin area
	 */
	public function configure() {

		// Check user permissions
		$this->current_user_can = current_user_can( 'manage_options' );

		// Check the current WP admin page
		$this->wp_current_screen = get_current_screen()->id;

		// Check the current Assistant status
		$this->is_assistant_completed = ( get_option( 'ionos_assistant_completed' ) == true );

		// Check active plugins
		$wp_plugins = get_plugins();

		foreach ( $wp_plugins as $plugin_path => $plugin_data ) {
			$plugin_filename = explode( '/', $plugin_path );
			$plugin_slug = str_replace( '.php', '', $plugin_filename[ 0 ] );

			$this->wp_plugins[ $plugin_slug ] = is_plugin_active( $plugin_path );
		}

		// Check if we have some custom blog feed
		$blog_feed = Config::get( 'links.blog_feed_' . Options::get_market() ) ?? Config::get( 'links.blog_feed_US' );

		if ( $blog_feed ) {
			$this->custom_blog_feed = $blog_feed;
		}
	}

	/**
	 * Add "Welcome" option in the Screen Options
	 */
	public function hide_welcome_panel_option() {
		echo '<style>[for="wp_welcome_panel-hide"] {display: none !important;}</style>';
	}

	/**
	 * Hide standard WP Welcome panel
	 * because we show our own
	 */
	public function hide_welcome_panel() {
		$user_id = get_current_user_id();

		if ( 1 == get_user_meta( $user_id, 'show_welcome_panel', true ) ) {
			update_user_meta( $user_id, 'show_welcome_panel', 0 );
		}
	}

	/**
	 * Render the HTML output of the WP Dashboard custom welcome panel(s)
	 */
	public function render_dashboard_panels() {

		if ( ! $this->current_user_can ) {
			return;
		}

		echo '<div class="updated assistant-dashboard dashboard-row">';

		// Welcome panel with some useful links
		switch ( $this->wp_current_screen ) {
			case 'dashboard':
				$this->render_welcome_panel();
				break;

			case 'plugins':
				$this->render_plugins_panel();
				break;

			default:
				return;
		}

		echo '</div>';
	}

	/**
	 * Render the HTML output for the general Welcome panel
	 */
	public function render_welcome_panel() {
		if ( $this->is_assistant_completed ) {
			Ionos_Assistant_View::load_template(
				'dashboard/welcome-panel-second-run',
				array(
					'is_product_domain' => $this->is_product_domain()
				)
			);
		} else {
			Ionos_Assistant_View::load_template( 'dashboard/welcome-panel-first-run' );
		}
	}

	/**
	 * Render the HTML output for the general Welcome panel in Plugins page
	 */
	public function render_plugins_panel() {

		Ionos_Assistant_View::load_template(
			'dashboard/welcome-panel-plugins',
			array( 'is_assistant_completed' => $this->is_assistant_completed )
		);
	}

	/**
	 * Configure the custom WP Dashboard widgets
	 * (replaces the standard WP Feed)
	 */
	public function add_dashboard_widgets() {

		// Register a custom Blog Feed Widget
		if ( $this->custom_blog_feed ) {
			wp_add_dashboard_widget(
				'ionos_assistant_blog_feed_widget',
				__( 'Community News', 'ionos-assistant' ),
				array( $this, 'render_dashboard_blog_feed_widget' )
			);

			// Remove the custom widget from its current position on the Dashboard
			global $wp_meta_boxes;

			$normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];
			$widget_backup = array( 'ionos_assistant_blog_feed_widget' => $normal_dashboard['ionos_assistant_blog_feed_widget'] );
			unset( $normal_dashboard['ionos_assistant_blog_feed_widget'] );

			// Insert it back to the top of the Dashboard
			$sorted_dashboard = array_merge( $widget_backup, $normal_dashboard );
			$wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;

			// Remove standard WP Blog Feed Widget
			remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
		}
	}

	/**
	 * Render the HTML output of the Blog Feed Widget
	 */
	public function render_dashboard_blog_feed_widget() {
		$feed = fetch_feed( $this->custom_blog_feed );

		if ( ! is_wp_error( $feed ) ) {
			$feed_items = $feed->get_items( 0, 3 );
		}

		$more_url = Config::get( 'links.blog_' . Options::get_market() ) ?? Config::get( 'links.blog_US' );

		Ionos_Assistant_View::load_template(
			'dashboard/widget-blog-feed',
			array(
				'feed_items' => $feed_items,
				'more_url'   => $more_url
			)
		);
	}

	/**
	 * Install and activate German Market (AJAX)
	 */
	public static function setup_german_market() {
		include_once( Ionos_Assistant::get_inc_dir_path() .'assets-manager.php' );

		// Setup German Market
		$assets_manager = new Ionos_Assistant_Assets_Manager( 'eshop' );
		$success = $assets_manager->setup_plugin( 'woocommerce-german-market-light' );

		// Avoid showing that panel again if the user deletes/deactivates the plugin afterwards
		update_option( 'ionos_assistant_gmarket_panel_dismissed', true );

		if ( $success ) {
			wp_send_json_success(
				array(
					'referer' => esc_url( admin_url( 'plugins.php' ) )
				)
			);
		} else {
			wp_send_json_error();
		}
	}

	/**
	 * Install and activate the Classic Editor plugin (AJAX)
	 */
	public static function setup_classic_editor() {
		include_once( Ionos_Assistant::get_inc_dir_path() .'assets-manager.php' );

		// Setup the Classic Editor plugin
		$assets_manager = new Ionos_Assistant_Assets_Manager();
		$success = $assets_manager->setup_plugin( 'classic-editor' );

		// Avoid showing that panel again if the user deletes/deactivates the plugin afterwards
		update_option( 'ionos_assistant_editor_panel_dismissed', true );

		if ( $success ) {
			wp_send_json_success(
				array(
					'referer' => esc_url( admin_url( 'plugins.php' ) )
				)
			);
		} else {
			wp_send_json_error();
		}
	}

	/**
	 * Handle actions from the dashboard anywhere in the admin area (via GET parameters)
	 */
	public function handle_dashboard_params() {

		// Click on "Dismiss" in German Market panel
		if ( isset( $_GET[ 'close_german_market_panel' ] ) ) {
			update_option( 'ionos_assistant_gmarket_panel_dismissed', true );
		}

		// Click on "Dismiss" in Editor panel
		if ( isset( $_GET[ 'close_editor_panel' ] ) ) {
			update_option( 'ionos_assistant_editor_panel_dismissed', true );
		}
	}

	/**
	 * Register the CSS and fonts for the Dashboard
	 */
	public function enqueue_styles() {

		// Add the Dasg
		wp_enqueue_style(
			'ionos-assistant-wp-dashboard',
			Ionos_Assistant::get_css_url( 'dashboard.css' ),
			array(),
			filemtime( Ionos_Assistant::get_css_path( 'dashboard.css' ) )
		);
	}

	/**
	 * Register JS scripts for the Dashboard
	 */
	public function enqueue_scripts() {

		// Add global script
		wp_enqueue_script(
			'ionos-assistant-wp-dashboard',
			Ionos_Assistant::get_js_url( 'dashboard.js' ),
			array( 'jquery' ),
			filemtime( Ionos_Assistant::get_js_path( 'dashboard.js' ) ),
			true
		);

		// Configure the AJAX object for the dashboard script
		wp_localize_script( 'ionos-assistant-wp-dashboard', 'ajax_dashboard_object', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' )
		) );
	}

	/**
	 * Check if the current WP Domain is a product domain
	 * (If yes a link will be shown to redirect the user to the Control Panel, where a new domain can be assigned)
	 *
	 * @return boolean
	 */
	private function is_product_domain() {

		$product_domains = array(
			'.apps-1and1.net',
			'.apps-1and1.com',
			'.online.de',
			'.live-website.com'
		);
		$domain = get_site_url();

		foreach ( $product_domains as $product_domain ) {
			if ( stripos( $domain, $product_domain ) !== false ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Return link to email managemen for IONOS.
	 *
	 * @return null|string
	 */
	public static function get_manage_email_link() {
		$market = Options::get_market();
		$market_email_datas = array(
			'DE' => array(
				'host' => 'mein.ionos.de',
				'action_code' => 'OM.BW.BW263K422193T7073a',
			),
			'UK' => array(
				'host' => 'my.ionos.co.uk',
				'action_code' => 'OM.CE.CE263K422194T7073a',
			),
			'FR' => array(
				'host' => 'my.ionos.fr',
				'action_code' => 'OM.CF.CF263K422195T7073a',
			),
			'ES' => array(
				'host' => 'my.ionos.es',
				'action_code' => 'OM.CS.CS263K422196T7073a',
			),
			'IT' => array(
				'host' => 'my.ionos.it',
				'action_code' => 'OM.CI.CI263K422197T7073a',
			),
			'US' => array(
				'host' => 'my.ionos.com',
				'action_code' => 'OM.CU.CU263K422198T7073a',
			),
			'MX' => array(
				'host' => 'my.ionos.mx',
				'action_code' => 'OM.CM.CM263K422199T7073a',
			),
			'CA' => array(
				'host' => 'my.ionos.ca',
				'action_code' => 'OM.CC.CC263K422200T7073a',
			),
		);

		$market_email_data = isset( $market_email_datas[ $market ] ) ? $market_email_datas[ $market ] : null;
		if ( null === $market_email_data ) {
			return null;
		}

		return sprintf(
			'https://%1$s/select-contract-silent?contract-selection-target=email-package-selection&contract-selection-domain=%3$s&contract-selection-nodata=&domain=%3$s&mail-business-ac=%2$s&mail-basic-ac=%2$s&utm_source=wordpress&utm_campaign=wp-email-create',
			$market_email_data['host'],
			$market_email_data['action_code'],
			parse_url( get_site_url(), PHP_URL_HOST )
		);
	}
}

new Ionos_Assistant_Dashboard();