<?php

namespace Ionos\Navigation;

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Manager class
 */
class Updater {
	const TENANT_UPDATE_DOMAIN = 'https://web-hosting.s3-website-de-central.profitbricks.com';
	const CONFIG_PLUGIN_INI_PATH = '/ionos/live/config/plugins.ini';
	const PACKAGE_PATH = '/ionos/live/%s.%s.zip';

	private $base_name = 'ionos-navigation/ionos-navigation.php';
	private $slug = 'ionos-navigation';
	private $config_selector = 'navigation_plugin';

	/**
	 * Updater constructor.
	 */
	public function __construct() {
		add_filter( 'site_transient_update_plugins', array( $this, 'check_update' ), 10, 1 );
		add_filter( 'auto_update_plugin', array( $this, 'force_auto_update' ), 10, 2 );
		add_filter( 'plugin_row_meta', array( $this, 'plugin_links' ), 10, 2 );
	}

	/**
	 * compares the current version with the latest one and, if necessary, issues the info that an update is pending.
	 *
	 * @param $transient
	 *
	 * @return mixed
	 */
	public function check_update( $transient ) {
		if ( empty( $transient->checked ) || empty( $transient->checked[ $this->base_name ] ) ) {
			return $transient;
		}
		$latest_version = $this->get_latest_version();
		if ( version_compare( $transient->checked[ $this->base_name ], $latest_version ) == - 1
		     && ( $package = $this->get_package_download_link( $latest_version ) )
		) {

			$plugin_data = (object) array(
				'id'            => $this->base_name,
				'slug'          => $this->slug,
				'plugin'        => $this->base_name,
				'new_version'   => $latest_version,
				'url'           => 'https://www.ionos.com',
				'package'       => $package,
				'compatibility' => new \stdClass(),
			);

			$transient->response[ $this->base_name ] = $plugin_data;

			return $transient;
		}

		// cleanup transient
		if ( isset( $transient->response[ $this->base_name ] ) ) {
			unset( $transient->response[ $this->base_name ] );
		}

		return $transient;
	}

	/**
	 * get latest plugin version
	 *
	 * @return false|mixed
	 */
	private function get_latest_version() {
		try {
			$request = wp_remote_get(
				self::TENANT_UPDATE_DOMAIN . self::CONFIG_PLUGIN_INI_PATH,
				array(
					'method' => 'GET',
				)
			);

			if ( wp_remote_retrieve_response_code( $request ) == 200 ) {
				$config_array = parse_ini_string( wp_remote_retrieve_body( $request ) );
				if ( isset( $config_array[ $this->config_selector ] ) ) {
					return $config_array[ $this->config_selector ];
				}
			}
			return false;

		} catch ( \Exception $e ) {
			return false;
		}
	}

	/**
	 * get valid package download link
	 *
	 * @param $version
	 *
	 * @return false|string
	 */
	private function get_package_download_link( $version ) {
		return self::TENANT_UPDATE_DOMAIN .
		       sprintf( self::PACKAGE_PATH, $this->slug, $version );
	}

	/**
	 * Force auto update
	 *
	 * @param $update
	 * @param $item
	 *
	 * @return bool
	 */
	public function force_auto_update( $update, $item ) {
		if ( $item->slug == $this->slug ) {
			return true;
		} else {
			return $update;
		}
	}

	/**
	 * remove plugin notification
	 *
	 * @param $links
	 * @param $plugin_file
	 *
	 * @return mixed
	 */
	public function plugin_links( $links, $plugin_file ) {
		if ( $plugin_file == $this->base_name ) {
			unset( $links[2] );
		}

		return $links;
	}
}