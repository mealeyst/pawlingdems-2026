<?php

namespace Ionos\Assistant\Wizard;

use Ionos\Assistant\Wizard\Controllers\Install;
use Plugin_Upgrader;
use Theme_Upgrader;
use Automatic_Upgrader_Skin;

require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/theme.php';
require_once ABSPATH . 'wp-admin/includes/plugin.php';
require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
require_once ABSPATH . 'wp-admin/includes/class-plugin-upgrader.php';

class Installer {

	private static $install_data;

	public static function install_next_component() {
		self::$install_data = get_option( Install::INSTALL_COMPONENTS_OPTION_NAME, false );
		if ( ! self::$install_data ) {
			return null;
		}

		// Classic wizard flow - install & activate theme.
		if ( isset( self::$install_data['theme'] ) ) {
			$installed = self::install_theme( self::$install_data['theme'] );

			if ( true === $installed ) {
				self::activate_theme( self::$install_data['theme'] );
			}

			unset( self::$install_data['theme'] );
			update_option( Install::INSTALL_COMPONENTS_OPTION_NAME, self::$install_data );

			return $installed;
		}

		// Install plugins.
		if ( isset( self::$install_data['plugins'] ) && count( self::$install_data['plugins'] ) > 0 ) {
			$installed = self::install_plugin();
			update_option( Install::INSTALL_COMPONENTS_OPTION_NAME, self::$install_data );

			return $installed;
		}

		// Blueprint Wizard flow - install all themes & activate specific theme.
		if ( isset( self::$install_data['themes'] ) && count( self::$install_data['themes'] ) > 0 ) {
			$theme     = array_splice( self::$install_data['themes'], 0, 1 );
			$installed = self::install_theme( $theme );

			if ( true === $installed ) {
				self::activate_theme( $theme );
			}

			update_option( Install::INSTALL_COMPONENTS_OPTION_NAME, self::$install_data );

			return $installed;
		}

		update_option( Manager::WIZARD_COMPLETED_OPTION_NAME, true );
		// TODO: Post install configure steps

		delete_option( Install::INSTALL_COMPONENTS_OPTION_NAME );
		return null;
	}

	private static function install_theme( $theme ) {
		$slug = array_keys( $theme )[0];

		if ( isset( $theme[ $slug ]['download_url'] ) ) {
			$installed = ( new Theme_Upgrader( new Automatic_Upgrader_Skin() ) )->install( $theme[ $slug ]['download_url'] );
			return true === $installed;
		}

		$api = themes_api( 'theme_information', [ 'slug' => $slug ] );

		if ( is_wp_error( $api ) ) {
			return false;
		}

		$theme = wp_get_theme( $slug );
		if ( $theme->exists() ) {
			return true;
		}

		// Clear cache so WP_Theme doesn't create a "missing theme" object.
		$cache_hash = md5( $theme->theme_root . '/' . $theme->stylesheet );
		foreach ( [ 'theme', 'screenshot', 'headers', 'page_templates' ] as $key ) {
			wp_cache_delete( $key . '-' . $cache_hash, 'themes' );
		}

		// Ignore failures on accessing SSL "https://api.wordpress.org/themes/update-check/1.1/" in `wp_update_themes()` which seem to occur intermittently.
		set_error_handler( null, E_USER_WARNING | E_USER_NOTICE );

		$result = ( new Theme_Upgrader( new Automatic_Upgrader_Skin() ) )->install( $api->download_link );

		restore_error_handler();

		return true === $result;
	}

	private static function activate_theme( $theme ) {
		$theme = wp_get_theme( array_keys( $theme )[0] );
		if ( wp_get_theme() === $theme ) {
			// already current theme.
			return true;
		}

		switch_theme( $theme->get_stylesheet() );

		return wp_get_theme() === $theme;
	}

	private static function install_plugin() {
		$plugin      = array_splice( self::$install_data['plugins'], 0, 1 );
		$plugin_data = current( $plugin );
		if ( isset( $plugin_data['download_url'] ) ) {
			return self::execute_install( $plugin_data['download_url'] );
		}

		// Install from repo
		$api = plugins_api(
			'plugin_information',
			[
				'slug'   => array_keys( $plugin )[0],
				'fields' => [ 'downloadlink' => true ],
			]
		);

		if ( is_wp_error( $api ) ) {
			return false;
		}

		$status = install_plugin_install_status( $api );

		if ( 'install' !== $status['status'] ) {
			return true;
		}

		return self::execute_install( $api->download_link );
	}

	private static function execute_install( $download_url ) {
		// Ignore failures on accessing SSL "https://api.wordpress.org/plugins/update-check/1.1/" in `wp_update_plugins()` which seem to occur intermittently.
		set_error_handler( null, E_USER_WARNING | E_USER_NOTICE );

		$plugin_upgrader = new Plugin_Upgrader( new Automatic_Upgrader_Skin() );
		$installed       = $plugin_upgrader->install( $download_url );
		activate_plugin( $plugin_upgrader->plugin_info() );

		return true === $installed;
	}
}
