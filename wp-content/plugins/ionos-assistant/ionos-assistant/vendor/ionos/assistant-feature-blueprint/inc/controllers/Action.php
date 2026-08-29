<?php
namespace Ionos\Blueprint\Controllers;

use Ionos\Blueprint\Controller\Blueprint;
use Ionos\Blueprint\Model\Activity;
use Ionos\Blueprint\Model\WebApplication;

/**
 * The Controller class for the AJAX action.
 */
class Action {
	/**
	 * Holds the blueprint data.
	 *
	 * @var object
	 */
	protected $blueprint;

	/**
	 * Initializes Blueprint object.
	 */
	public function __construct() {
		$this->blueprint = new Blueprint();
	}

	/**
	 * Generates the Blueprint object.
	 */
	protected function generate() {
		$request_body = file_get_contents( 'php://input' );
		parse_str( $request_body, $param );

		if ( empty( $param['payload'] || ! wp_verify_nonce( $param['nonce'], 'ionos-blueprint-nonce' ) ) ) {
			return false;
		}

		$json = json_decode( $param['payload'] );

		if ( ! $json ) {
			return false;
		}

		$this->set_generator();

		$this->set_themes( $json );
		$this->set_plugins( $json );

		return true;
	}

	/**
	 * Sends the Blueprint to the browser.
	 */
	public function download() {
		if ( ! $this->generate() ) {
			http_response_code( 500 );
			exit; // do not use wp_die, as this sets the status code.
		}

		wp_send_json( $this->blueprint->get_data(), 200, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
	}


	/**
	 * Adds information about where ActivityStream comes from.
	 */
	protected function set_generator() {
		$this->blueprint->get_data()->set_generator(
			new WebApplication(
				[
					'name'    => 'WordPress',
					'version' => get_bloginfo( 'version' ),
				]
			)
		);
	}

	/**
	 * Sets the wanted theme in the Blueprint object.
	 *
	 * @param object $json Hold the json-data from the form.
	 */
	protected function set_themes( $json ) {
		$active_theme = wp_get_theme();
		foreach ( wp_get_themes() as $theme ) {
			if ( empty( $json->all_themes ) && ! in_array( $theme->get_stylesheet(), $json->themes, true ) ) {
				continue;
			}

			$app = [
				'name'    => $theme->get_stylesheet(),
				'version' => $theme->version,
			];
			$app = new WebApplication( $app, 'Theme' );
			$this->blueprint->get_data()->add_item( new Activity( 'Install', $app ) );

			if ( $theme->name === $active_theme->name ) {
				$this->blueprint->get_data()->add_item( new Activity( 'Activate', $app ) );
			}
		}
	}

	/**
	 * Sets the wanted plugins in the Blueprint object.
	 *
	 * @param object $json Hold the json-data from the form.
	 */
	protected function set_plugins( $json ) {
		foreach ( get_plugins() as $slug => $plugin ) {
			if ( empty( $json->all_plugins ) && ! in_array( $slug, $json->plugins, true ) ) {
				continue;
			}

			$basename = plugin_basename( $slug );
			if ( false === strpos( $basename, '/' ) ) {
				$name = basename( $basename, '.php' );
			} else {
				$name = dirname( $basename );
			}

			$app = [
				'name'    => $name,
				'version' => $plugin['Version'],
			];
			$app = new WebApplication( $app, 'Plugin' );
			$this->blueprint->get_data()->add_item( new Activity( 'Install', $app ) );

			if ( is_plugin_active( $slug ) ) {
				$this->blueprint->get_data()->add_item( new Activity( 'Activate', $app ) );
			}
		}
	}

}
