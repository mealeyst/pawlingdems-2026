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

/**
 * Class for installation logic.
 */
class Installer {
	/**
	 * Function to install next component in array.
	 *
	 * @return bool|mixed|null
	 */
	public static function install_next_component() {
		$install_data = get_option( Install::INSTALL_COMPONENTS_OPTION_NAME, false );
		if ( empty( $install_data ) ) {
			return null;
		}

		// Loop specific flow - install & activate loop.
		if ( isset( $install_data['plugins']['ionos-loop'] ) && is_array( $install_data['plugins']['ionos-loop'] ) ) {
			if ( ! is_plugin_active( 'ionos-loop/ionos-loop.php' ) ) {
				// Install and activate Loop plugin. And give consent.
				if ( file_exists( WP_PLUGIN_DIR . '/ionos-loop/ionos-loop.php' ) ) {
					// Loop is already installed. Just activate it.
					activate_plugin( 'ionos-loop/ionos-loop.php' );
				} else {
					// Install the Loop plugin. And reload. The next steps will need the plugin to be active.
					self::install_plugin( 'ionos-loop', $install_data['plugins']['ionos-loop'] );
				}

				if ( method_exists( \Ionos\Loop\Plugin::class, 'register_at_data_collector' ) ) {
					\Ionos\Loop\Plugin::register_at_data_collector();
				} else {
					// Fallback: Just set the consent to true and trigger a warning.
					wp_trigger_error( 'Loop plugin is not working as expected. Please contact the provider.', E_USER_WARNING );
					add_option( 'ionos_loop_consent', true );
				}
				return true;
			}

			// Add data, make sure not to go into this again and return.
			Loop::add_data( $install_data );

			unset( $install_data['plugins']['ionos-loop'] );
			update_option( Install::INSTALL_COMPONENTS_OPTION_NAME, $install_data );
			return true;
		}

		// Classic wizard flow - install & activate theme.
		if ( isset( $install_data['theme'] ) ) {
			$installed = self::install_theme( $install_data['theme'] );

			if ( true === $installed ) {
				self::activate_theme( $install_data['theme'] );
			}

			unset( $install_data['theme'] );
			update_option( Install::INSTALL_COMPONENTS_OPTION_NAME, $install_data );

			return $installed;
		}

		// Install plugins.
		if ( isset( $install_data['plugins'] ) && count( $install_data['plugins'] ) > 0 ) {
			$plugin_name = array_key_first( $install_data['plugins'] );
			$plugin_data = $install_data['plugins'][ $plugin_name ];
			unset( $install_data['plugins'][ $plugin_name ] );

			// First store the data, to prevent an infinite loop, in case the installation fails with a fatal error.
			update_option( Install::INSTALL_COMPONENTS_OPTION_NAME, $install_data );

			return self::install_plugin( $plugin_name, $plugin_data );
		}

		// Blueprint Wizard flow - install all themes & activate specific theme.
		if ( isset( $install_data['themes'] ) && count( $install_data['themes'] ) > 0 ) {
			$theme     = array_splice( $install_data['themes'], 0, 1 );
			$installed = self::install_theme( $theme );

			if ( true === $installed ) {
				self::activate_theme( $theme );
			}

			update_option( Install::INSTALL_COMPONENTS_OPTION_NAME, $install_data );

			return $installed;
		}

		update_option( Manager::WIZARD_COMPLETED_OPTION_NAME, true );
		// TODO: Post install configure steps.

		delete_option( Install::INSTALL_COMPONENTS_OPTION_NAME );
		return null;
	}

	/**
	 * Installs a theme by given data.
	 *
	 * @param array $theme Theme data.
	 *
	 * @return bool
	 */
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

	/**
	 * Activates a theme by given data.
	 *
	 * @param array $theme Theme data.
	 *
	 * @return bool
	 */
	private static function activate_theme( $theme ) {
		$theme = wp_get_theme( array_keys( $theme )[0] );
		if ( wp_get_theme() === $theme ) {
			// already current theme.
			return true;
		}

		switch_theme( $theme->get_stylesheet() );

		return wp_get_theme() === $theme;
	}

	/**
	 * Installs a plugin by given data.
	 *
	 * @param string $plugin_name Plugin slug.
	 * @param array  $plugin_data Plugin data.
	 *
	 * @return bool|mixed
	 */
	private static function install_plugin( $plugin_name, $plugin_data ) {
		// Install custom plugins.
		if ( isset( $plugin_data['download_url'] ) ) {
			$result = self::execute_install( $plugin_data['download_url'] );

			return $result;
		}

		// Install from repo.
		$api = plugins_api(
			'plugin_information',
			[
				'slug'   => $plugin_name,
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

	/**
	 * Executes a plugin install by download_url.
	 *
	 * @param string $download_url URL for Plugin Zip.
	 *
	 * @return bool
	 */
	private static function execute_install( $download_url ) {
		// Ignore failures on accessing SSL "https://api.wordpress.org/plugins/update-check/1.1/" in `wp_update_plugins()` which seem to occur intermittently.
		set_error_handler( null, E_USER_WARNING | E_USER_NOTICE );

		$plugin_upgrader = new Plugin_Upgrader( new Automatic_Upgrader_Skin() );
		$installed       = $plugin_upgrader->install( $download_url );
		activate_plugin( $plugin_upgrader->plugin_info() );

		return true === $installed;
	}
}
