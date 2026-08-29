<?php

namespace Ionos\HelpCenter;

use Ionos\HelpCenter\Config;
use Ionos\HelpCenter\Options;

// Do not allow direct access!
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class Ionos_Help_Center_Manager
 */
class Manager {

	public function __construct() {
		// Construct JS to dynamically add help center links
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_help_center_resources' ) );
		add_action( 'admin_head', array( $this, 'add_external_libraries' ) );

		// Add help center static links via hooks
		$this->create_help_center_links();
	}

	/**
	 * Build static HTML links via configured hooks
	 */
	public function create_help_center_links() {
		if ( Config::get('main.enabled') === '1' ) {
			$article_list = Config::get('articles.hooks');
			foreach ( $article_list as $key => $article ) {
				$domain = Config::get('domains.' . $article['source'] );
				add_filter( $article['hook'], function ( $html ) use ( $article, $domain ) {
					return (
						$html . sprintf(
							'<a href="%s?id=%s" class="help-center-article-element exos-icon exos-icon-help-18 oao-pi-open-in-%s"></a>',
							__( $domain, 'ionos-help-center' ),
							$article['article_id'],
							$article['behavior']
						)
					);
				} );
			}
		}
	}

	/**
	 * Load external resources from IONOS library
	 */
	public function add_external_libraries() {
		echo '<script src="https://ce1.uicdn.net/exos/framework/1.1/ionos.min.js" async="async"></script>';
		echo '<script src="https://frontend-services.ionos.com/t/tag/IONOS/wp-admin.js" async="async" defer="defer" id="oaotag" type="text/javascript"></script>';
	}

	/**
	 * Load internal resources
	 */
	public function enqueue_help_center_resources() {
		// Load scripts
		wp_enqueue_script(
			'ionos-help-center-js',
			Helper::get_js_url( 'ionos-help-center.js' ),
			array(),
			filemtime( Helper::get_js_path( 'ionos-help-center.js' ) ),
			true
		);
		wp_localize_script( 'ionos-help-center-js', 'ionos',
			array( 'market' => ( string ) strtolower( Options::get_market() ) )
		);

		$this->enqueue_help_center_page_script( 'ionos-help-center-js' );

		// Load styles
		wp_enqueue_style(
			'ionos-help-center-css',
			Helper::get_css_url( 'ionos-help-center.css' ),
			array(),
			filemtime( Helper::get_css_path( 'ionos-help-center.css' ) )
		);

	}

	/**
	 * Add help article script
	 */
	public function enqueue_help_center_page_script( $handle ) {
		if ( Config::get('main.enabled') === '1' ) {
			$article_list = $this->get_current_page_help_center_articles();

			if ( count( $article_list ) ) {
				wp_add_inline_script( $handle, $this->get_article_integration_js_string( $article_list ) );
			}
		}
	}

	/**
	 * Collect help information of current page
	 *
	 * @return array
	 */
	public function get_current_page_help_center_articles() {
		global $current_screen;
		$result_array = array();

		if ( ! isset( $current_screen ) ) {
			return array();
		}

		$all_articles = Config::get( 'articles.pages' );
		if ( isset( $all_articles[ $current_screen->id ] ) && is_array( $all_articles[ $current_screen->id ] ) ) {
			foreach ( $all_articles[ $current_screen->id ] as $article_key => $article_value ) {
				$result_array[ $article_key ] = $article_value;
			}
		}

		return $result_array;
	}

	/**
	 * Build help article script
	 *
	 * @param  array  $article_list
	 *
	 * @return string
	 */
	public function get_article_integration_js_string( array $article_list ) {
		$script = '';
		foreach ( $article_list as $article ) {
			$domain = Config::get( 'domains.' . $article['source'] );

			$script .= sprintf(
				'create_help_center_link("%s","%s","%s","%s");',
				$article['article_id'],
				$article['behavior'],
				$article['html_selector'],
				__( $domain, 'ionos-help-center' )
			);
		}

		return $script;
	}
}
