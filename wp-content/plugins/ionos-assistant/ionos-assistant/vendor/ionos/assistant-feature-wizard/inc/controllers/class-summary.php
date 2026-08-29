<?php

namespace Ionos\Assistant\Wizard\Controllers;

use Ionos\Assistant\Config;
use Ionos\Assistant\Wizard\Manager;
use Ionos\Assistant\Wizard\Request_Validator;
use Ionos\Assistant\Wizard\Theme;
use Ionos\Assistant\Wizard\Use_Case;
use Ionos\Assistant\Wizard\Wp_Org_Api;
use const Ionos\Assistant\Wizard\FEATURE_MAIN_PLUGIN_FILE_PATH;

use const Ionos\Assistant\Wizard\VIEWS_DIR_PATH;

class Summary implements View_Controller {
	private static $plugin_infos;
	private static $themes_infos;
	private static $selected_use_case;
	private static $selected_theme;

	public static function render() {
		$optional_plugins = [];

		if ( ! empty( $_GET[ Manager::STATE_INPUT_NAMES['plugins'] ] ) ) {
			$optional_plugins = $_GET[ Manager::STATE_INPUT_NAMES['plugins'] ];
		}

		if ( isset( $_GET['install_promoted'] ) && Plugin_Advertising::validate_promoted_plugin() ) {
			$promoted_plugin  = Config::get( 'features.wizard.promotedPlugin' );
			$optional_plugins = array_merge( $optional_plugins, (array) array_key_first( $promoted_plugin ) ); // Works since PHP 7.3 or with polyfill since WP 5.9
		}

		$template_array = [
			'counter_text' => __( 'Step 2 of 2', 'ionos-assistant' ),
			'heading_text' => __( 'Abort Summary', 'ionos-assistant' ),
			'next_step'    => 'install',
			'plugins'      => $optional_plugins,
		];

		$plugin_slugs  = $optional_plugins;
		$template_view = VIEWS_DIR_PATH . '/abort-summary.php';

		if ( ! Abort_Plugin_Selection::is_abort_screen() && empty( Blueprint_Upload::get_transient_content() ) ) {
			self::$selected_use_case = $_GET[ Manager::STATE_INPUT_NAMES['use_case'] ];
			self::$selected_theme    = $_GET[ Manager::STATE_INPUT_NAMES['theme'] ];

			$use_case_info = Config::get( 'features.wizard.usecases.' . self::$selected_use_case );
			$use_case      = new Use_Case( $use_case_info );
			$infos         = $use_case->retrieve_theme_infos();
			if ( empty( $infos ) ) {
				return;
			}

			$theme_info = $infos[ self::$selected_theme ];
			$theme      = new Theme( $theme_info );

			$required_plugins = array_merge(
				$use_case->get_required_plugins(),
				$theme->get_required_plugins()
			);
			$plugin_slugs     = array_merge( array_keys( $required_plugins ), $optional_plugins );
			$template_array   = [
				'counter_text'     => __( 'Step 5 of 5', 'ionos-assistant' ),
				'heading_text'     => __( 'Summary', 'ionos-assistant' ),
				'use_case'         => self::$selected_use_case,
				'theme'            => self::$selected_theme,
				'info'             => $theme_info,
				'plugins'          => $optional_plugins,
				'required_plugins' => $required_plugins,
				'next_step'        => 'install',
				'preview_link'     => 'https://wp-themes.com/' . self::$selected_theme,
			];

			$template_view      = VIEWS_DIR_PATH . '/summary.php';
			self::$themes_infos = Wp_Org_Api::get_theme_infos( $theme );

		} elseif ( Manager::is_blueprint_enabled() && ! empty( Blueprint_Upload::get_transient_content() ) ) {
			// Transient is empty due to corrupt blueprint file;
			if ( Blueprint_Upload::get_transient_content() == 'error' ) {
				$template_array['heading_text'] = __( 'Error occured', 'ionos-assistant' );
				$template_array['counter_text'] = ' ';
				$template_view                  = VIEWS_DIR_PATH . '/error-occured.php';
				load_template(
					$template_view,
					true,
					$template_array
				);
				return;
			}

			$file                = json_decode( Blueprint_Upload::get_transient_content() );
			$plugins_list        = [];
			$install_themes_list = [];
			$active_theme        = [];

			foreach ( $file->items as $item ) {
				if ( $item->object->applicationCategory == 'Theme' ) {
					switch ( $item->type ) {
						case 'Install':
							$install_themes_list[] = $item->object->name;
							break;
						case 'Activate':
							$active_theme = $item->object->name;
							break;
					}
				} elseif ( $item->object->applicationCategory == 'Plugin' ) {
					switch ( $item->type ) {
						case 'Install':
							$plugins_list[] = $item->object->name;
							break;
						case 'Activate':
							break;
					}
				}
			}

			$themes_slugs = $install_themes_list;
			$plugin_slugs = $plugins_list;

			$_GET['plugins']      = $plugin_slugs;
			$_GET['themes']       = $themes_slugs;
			$_GET['active_theme'] = $active_theme;

			$template_array['heading_text'] = __( 'Blueprint Summary', 'ionos-assistant' );
			$template_array['themes']       = $themes_slugs;
			$template_array['active_theme'] = $active_theme;
			$template_array['plugins']      = $plugin_slugs;

			$template_view = VIEWS_DIR_PATH . '/summary.php';

			self::$themes_infos = Wp_Org_Api::get_theme_infos( $themes_slugs );
		}

		self::$plugin_infos = Wp_Org_Api::get_plugin_infos( $plugin_slugs );

		load_template(
			$template_view,
			true,
			$template_array
		);
	}

	public static function validate_request_params() {
		if ( Abort_Plugin_Selection::is_abort_screen() ) {
			return true;
		}

		if ( Manager::is_blueprint_enabled() ) {
			return true;
		}

		return Request_Validator::validate( [ 'use_case', 'theme' ] );
	}

	public static function get_page_title() {
		return __( 'Summary', 'ionos-assistant' );
	}

	public static function get_plugin_name( $slug ) {
		if ( ! is_array( $slug ) ) {
			if ( empty( Blueprint_Upload::get_transient_content() ) ) {
				if ( ! Abort_Plugin_Selection::is_abort_screen() ) {
					$use_case = self::$selected_use_case;
					$theme    = self::$selected_theme;
					$paths    = [
						"features.wizard.usecases.$use_case.plugins.recommended.$slug.name",
						"features.wizard.usecases.$use_case.plugins.required.$slug.name",
						"features.wizard.usecases.$use_case.themes.$theme.plugins.recommended.$slug.name",
						"features.wizard.usecases.$use_case.themes.$theme.plugins.required.$slug.name",
					];
				} else {
					$paths = [
						"features.wizard.plugins.$slug.name",
					];
				}

				foreach ( $paths as $path ) {
					$name = Config::get( $path );

					if ( $name ) {
						return $name;
					}
				}
			}

			return isset( self::$plugin_infos[ $slug ]['name'] ) ? self::$plugin_infos[ $slug ]['name'] : $slug;
		}

		return $slug['name'];
	}

	public static function get_theme_name( $slug ) {
		if ( ! is_array( $slug ) ) {
			return isset( self::$themes_infos[ $slug ]['name'] ) ? self::$themes_infos[ $slug ]['name'] : $slug;
		}

		return Manager::is_blueprint_enabled() ? $slug : $slug['name'];
	}

	public static function get_active_theme_img( $active_theme ) {
		if ( empty( $active_theme ) ) {
			return plugins_url( '/img/placeholder.svg', FEATURE_MAIN_PLUGIN_FILE_PATH );
		}

		return 'https:' . self::$themes_infos[ $active_theme ]['screenshot_url'];
	}

	public static function setup() {

	}
}
