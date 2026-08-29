<?php
//phpcs:disable  Squiz.Commenting, IonosWordPress.Files.FileName.InvalidClassFileName
namespace Ionos\Marketplace;

use Ionos\Marketplace\Config;
use Ionos\Marketplace\WpOrgApi;

class Plugins {

	const FALLBACK_LANG      = 'en';
	const MAX_ITEMS_PER_PAGE = 12;

	private $ui;
	private $plugins;
	private $paginated_plugins = [];
	private $total_pages       = 0;
	private $total_items       = 0;

	public function __construct() {
		$language      = strtolower( explode( '_', get_locale() )[0] );
		$ui            = json_decode( Config::get( 'data.' . $language . '_ui' ) );
		$plugins       = json_decode( Config::get( 'data.' . $language . '_plugins' ) );
		$this->ui      = isset( $ui ) ? $ui : json_decode( Config::get( 'data.' . self::FALLBACK_LANG . '_ui' ) );
		$this->plugins = isset( $plugins ) ? $plugins : json_decode( Config::get( 'data.' . self::FALLBACK_LANG . '_plugins' ) );

		if ( ! empty( $this->plugins ) ) {
			add_filter( 'install_plugins_tabs', [ $this, 'add_ionos_tab' ] );
			add_action( 'install_plugins_ionos', [ $this, 'show_ionos_items' ] );
			add_action( 'install_plugins_pre_ionos', [ $this, 'load_ionos_items' ] );
			add_filter( 'plugins_api', [ $this, 'get_plugin_info' ], 20, 3 );
			add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_style' ] );
		}
	}

	public function enqueue_style() {
		if ( get_current_screen()->id === 'plugin-install' ) {
			wp_enqueue_style(
				'ionos-marketplace-styles',
				Marketplace::get_css_url( 'ionos-marketplace.css' ),
				[],
				filemtime( Marketplace::get_css_path( 'ionos-marketplace.css' ) )
			);
		}
	}

	private function get_data_for_plugins( $custom_plugins = false ) {
		$data = [];
		foreach ( $this->paginated_plugins as $p ) {
			$is_custom = isset( $p->custom ) ? $p->custom : false;
			if ( $custom_plugins === $is_custom ) {
				array_push( $data, $p );
			}
		}

		return $data;
	}

	private function get_custom_plugin_by_slug( $slug ) {
		foreach ( $this->plugins as $p ) {
			if ( $p->slug === $slug && ! empty( $p->custom ) ) {
				return $p;
			}
		}

		return null;
	}

	public function load_ionos_items() {
		global $wp_list_table;
		$wp_list_table->items = [];

		$this->total_pages = ceil( count( $this->plugins ) / self::MAX_ITEMS_PER_PAGE );
		$paged             = isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;
		$page_num          = max( min( $paged, $this->total_pages ), 1 );
		$this->total_items = count( $this->plugins );

		$this->paginated_plugins = array_chunk( $this->plugins, self::MAX_ITEMS_PER_PAGE )[ $page_num - 1 ];

		$plugin_infos       = [];
		$has_custom_plugins = false;
		foreach ( $this->get_data_for_plugins( true ) as $p ) {
			$has_custom_plugins = true;

			$response                 = wp_remote_get( $p->custom_info_url );
			$plugin_infos[ $p->slug ] = json_decode( wp_remote_retrieve_body( $response ), true );

			if ( empty( $plugin_infos[ $p->slug ] ) ) {
				unset( $plugin_infos[ $p->slug ] );
				continue;
			}

			$plugin_infos[ $p->slug ]['slug']              = $p->slug;
			$plugin_infos[ $p->slug ]['name']              = $p->name;
			$plugin_infos[ $p->slug ]['version']           = isset( $plugin_infos[ $p->slug ]['latest_version'] ) ? $plugin_infos[ $p->slug ]['latest_version'] : null;
			$plugin_infos[ $p->slug ]['author']            = '<a href="https://www.ionos.com">IONOS</a>';
			$plugin_infos[ $p->slug ]['short_description'] = $p->description;
			$plugin_infos[ $p->slug ]['rating']            = 0;
			$plugin_infos[ $p->slug ]['num_ratings']       = 0;
			$plugin_infos[ $p->slug ]['requires']          = isset( $plugin_infos[ $p->slug ]['requires_wp'] ) ? $plugin_infos[ $p->slug ]['requires_wp'] : null;
			$plugin_infos[ $p->slug ]['tested']            = isset( $plugin_infos[ $p->slug ]['tested_to'] ) ? $plugin_infos[ $p->slug ]['tested_to'] : null;
			$plugin_infos[ $p->slug ]['download_link']     = isset( $plugin_infos[ $p->slug ]['download_url'] ) ? $plugin_infos[ $p->slug ]['download_url'] : null;
			$plugin_infos[ $p->slug ]['active_installs']   = 0;
			$plugin_infos[ $p->slug ]['icons']             = [ 'svg' => $p->custom_icon_url ];
			$plugin_infos[ $p->slug ]['last_updated']      = isset( $plugin_infos[ $p->slug ]['last_updated'] ) ? $plugin_infos[ $p->slug ]['last_updated'] : '2022-01-01';
		}

		$wp_org_plugins_info = $this->get_data_from_wp_api( $this->get_data_for_plugins() );

		if ( $has_custom_plugins === true && empty( $plugin_infos ) ) {
			$this->admin_notice( 'IONOS' );
		}

		if ( empty( $wp_org_plugins_info ) ) {
			$this->admin_notice( 'WordPress.org' );

			$this->total_pages = 1;
			$this->total_items = count( $plugin_infos );
		}

		$wp_list_table->items = array_merge(
			$plugin_infos,
			$wp_org_plugins_info
		);
	}

	private function get_data_from_wp_api( $plugins ) {
		$slugs = [];
		foreach ( $plugins as $p ) {
			$slugs[] = $p->slug;
		}

		try {
			$info = WpOrgApi::get_info(
				'plugin',
				$slugs,
				[
					'short_description' => true,
					'icons'             => true,
				]
			);
		} catch ( \Exception $e ) {
			return [];
		}

		return $info;
	}

	public function add_ionos_tab( $tabs ) {
		unset( $tabs['featured'] );

		$tab_name = isset( $this->ui->tab_name ) ? $this->ui->tab_name : 'Marketplace recommends';
		return array_merge(
			[ 'ionos' => $tab_name ],
			$tabs
		);
	}

	public function show_ionos_items() {
		global $wp_list_table;

		$wp_list_table->set_pagination_args(
			[
				'total_items' => $this->total_items,
				'total_pages' => $this->total_pages,
				'per_page'    => self::MAX_ITEMS_PER_PAGE,
			]
		);

		display_plugins_table();
	}

	public function get_plugin_info( $result, $action, $args ) {
		if ( $action !== 'plugin_information' ) {
			return $result;
		}

		$p = $this->get_custom_plugin_by_slug( $args->slug );
		if ( ! is_null( $p ) ) {
			$response          = wp_remote_get( $p->custom_info_url );
			$pi                = json_decode( wp_remote_retrieve_body( $response ) );
			$pi->name          = $p->name;
			$pi->slug          = $args->slug;
			$pi->download_link = $pi->download_url;
			$pi->version       = $pi->latest_version;
			$pi->requires      = $pi->requires_wp;
			$pi->tested        = $pi->tested_to;
			$pi->sections      = [
				_x( 'Description', 'Plugin installer section title' ) => $p->description,
				_x( 'Changelog', 'Plugin installer section title' )   => $this->render_changelog( $pi->changelog ),
			];

			return $pi;
		}

		return $result;
	}

	private function render_changelog( $changelog ) {
		$response = '';

		foreach ( $changelog as $item ) {
			$response .= '<h4>' . $item->version . '</h4><ul>';

			foreach ( $item->changes as $c ) {
				$response .= '<li>' . $c . '</li>';
			}

			$response .= '</ul>';
		}

		return $response;
	}

	/**
	 * Adds an admin notice.
	 *
	 * @param string $message Message to display.
	 * @param string $type    Type of message.
	 */
	private function admin_notice( $message, $type = 'error' ) {
		add_action(
			'admin_notices',
			function () use ( $message, $type ) {
				?>
				<div class="notice notice-<?php echo esc_attr( $type ); ?>">
					<?php /* translators: %s is replaced with a plugin source, likely WordPress.org or a tenant name */ ?>
					<p><?php echo esc_html( sprintf( __( 'Could not load plugins from %s. Please try again later.', 'ionos-marketplace' ), $message ) ); ?></p>
				</div>
				<?php
			}
		);
	}
}
