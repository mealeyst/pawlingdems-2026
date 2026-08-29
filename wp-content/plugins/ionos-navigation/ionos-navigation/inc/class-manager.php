<?php

namespace Ionos\Navigation;

// Do not allow direct access!

use Ionos\Navigation\Config;

if ( ! \defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Manager class
 */
class Manager {
	public function __construct() {
		\add_action( 'admin_init', array( $this, 'init' ) );
	}

	/**
	 * Adds menu and needed resources.
	 */
	public function init() {
		\add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_navigation_resources' ) );
		\add_action( 'admin_bar_menu', array( $this, 'add_ionos_menu' ), 999 );
	}

	/**
	 * Implements a navigation based on the config.json provided by the WMS.
	 *
	 * @param  \WP_Admin_Bar  $wp_admin_bar  The WordPress Admin bar object.
	 *
	 * @return \WP_Admin_Bar
	 */
	public function add_ionos_menu( $wp_admin_bar ) {
		$list = $this->get_elements();
		if ( empty( $list ) ) {
			return $wp_admin_bar;
		}


		//specify background color
		//the background color changes between two shades of gray.
		$child_count = 0;
		foreach ( $wp_admin_bar->get_nodes() as $object ) {
			if ( 'my-account' === $object->parent ) {
				$child_count++;
			}
		}

		//create container basic structure with color specification
		$wp_admin_bar->add_group(
			array(
				'id'     => 'ionos-global-navigation-group',
				'parent' => 'my-account',
				'meta'   => array(
					'class' => ( 0 === ( $child_count % 2 ) ) ?: 'ab-sub-secondary',
				),
			)
		);

		//Add submenu
		$wp_admin_bar->add_node(
			array(
				'id'     => 'ionos-global-navigation',
				'title'  => '<span class="ab-sub-menu menupop">'
							. \__( 'IONOS Navigation', 'ionos-navigation' )
							. '</span>',
				'parent' => 'ionos-global-navigation-group',
			)
		);

		//add submenu items
		foreach ( $list as $key => $value ) {
			$args = array(
				'id'     => 'ionos-global-navigation-' . $key,
				'title'  => \__( $value['name'], 'ionos-navigation' ),
				'parent' => 'ionos-global-navigation',
				'href'   => $value['href'],
				'meta'   => array(
					'target' => '_blank',
				),
			);
			$wp_admin_bar->add_node( $args );
		}

		return $wp_admin_bar;
	}

	/**
	 * Load internal resources
	 */
	public function enqueue_navigation_resources() {
		// Load styles
		\wp_enqueue_style(
			'ionos-navigation-css',
			Helper::get_css_url( 'ionos-navigation.css' ),
			array(),
			filemtime( Helper::get_css_path( 'ionos-navigation.css' ) )
		);
	}

	/**
	 * Gets the WMS feature config and prepares it for navigation.
	 *
	 * @return array|null
	 */
	private function get_elements() {
		$config_array = \json_decode( Config::get( 'features.menu' ), true );
		if ( ! \is_array( $config_array ) || ! \is_array( $config_array['menu'] ) ) {
			return array();
		}

		$market = false;
		if ( isset( $config_array['market'] ) && isset( $config_array['market'][ \get_option( 'ionos_market' ) ] ) ) {
			$market = $config_array['market'][ \get_option( 'ionos_market' ) ];
		}

		$list = array();
		foreach ( $config_array['menu'] as $value ) {
			if ( isset( $value['name'] ) && isset( $value['path'] ) ) {
				if ( false !== $market && Config::get( 'features.use_market' ) ) {
					$domain = $market;
				} elseif ( isset( $value['domain'] ) ) {
					$domain = $value['domain'];
				} else {
					continue;
				}

				$list[] = array(
					'name' => $value['name'],
					'href' => 'https://' . $domain . $value['path'],
				);
			}
		}

		return $list;
	}
}
