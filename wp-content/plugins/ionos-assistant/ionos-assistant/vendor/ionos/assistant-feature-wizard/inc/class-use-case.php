<?php

namespace Ionos\Assistant\Wizard;

class Use_Case {
	protected $config;

	/**
	 * Contains a cached version of the info for themes from WP.org and custom sources.
	 *
	 * @var null
	 */
	protected $theme_infos = null;

	/**
	 * Contains the theme slugs coming from the WMS config.
	 *
	 * @var array|mixed
	 */
	protected $wp_org_themes = [];

	/**
	 * Contains the theme slugs and info coming from the WMS config.
	 *
	 * @var array
	 */
	protected $custom_themes = [];

	/**
	 * Takes a config part for a use case, get all themes from that and split them into WP.org and custom themes.
	 *
	 * @param $config_part
	 */
	public function __construct( $config_part ) {
		$this->config = $config_part;

		$themes = $this->get_themes();
		foreach ( $themes as $key => $theme ) {
			if ( ! empty( $theme['download_url'] ) ) {
				$this->custom_themes[ $key ]         = $theme;
				$this->custom_themes[ $key ]['slug'] = $key;
				unset( $themes[ $key ] );
			}
		}
		$this->wp_org_themes = $themes;
	}

	/**
	 * Get the themes slugs for WP.org themes and the slugs and info for custom themes.
	 *
	 * @deprecated To get the theme info for (non)-custom themes use retrieve_theme_infos().
	 *
	 * @return array|mixed
	 */
	public function get_themes() {
		if ( ! isset( $this->config['themes'] ) ) {
			return [];
		}

		return Market_Helper::filter_assets_by_market( $this->config['themes'] );
	}

	/**
	 * Retrieves theme infos for WP.org themes and merges them with custom theme infos.
	 * Returns a cached result if possible.
	 *
	 * @return array
	 */
	public function retrieve_theme_infos() {
		if ( $this->theme_infos !== null ) {
			return $this->theme_infos;
		}

		$wp_org_theme_slugs = array_keys( $this->wp_org_themes );
		$this->theme_infos  = array_merge(
			Wp_Org_Api::get_theme_infos( $wp_org_theme_slugs ),
			$this->custom_themes
		);
		ksort( $this->theme_infos );

		return $this->theme_infos;
	}

	/**
	 * Checks if a custom theme with a given slug exists.
	 *
	 * @return bool
	 */
	public function has_custom_theme( $slug ) {
		return array_key_exists( $slug, $this->custom_themes );
	}

	public function get_required_plugins() {
		if ( ! isset( $this->config['plugins']['required'] ) ) {
			return [];
		}

		return Market_Helper::filter_assets_by_market( $this->config['plugins']['required'] );
	}

	public function get_recommended_plugins() {
		if ( ! isset( $this->config['plugins']['recommended'] ) ) {
			return [];
		}

		return Market_Helper::filter_assets_by_market( $this->config['plugins']['recommended'] );
	}
}
