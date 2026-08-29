<?php

namespace Ionos\Assistant\Wizard;

// phpcs:disable Squiz.Classes.ValidClassName.NotCamelCaps
/**
 * Class Wp_Org_Api
 *
 * @package Ionos\Assistant\Wizard
 *
 * @codeCoverageIgnore
 */
class Wp_Org_Api {

	const INFO_REQUEST_TYPE_THEME  = 'theme';
	const INFO_REQUEST_TYPE_PLUGIN = 'plugin';

	/**
	 * Get the theme information for multiple themes/plugins at once
	 *
	 * @param array  $slugs List of slugs to get infos for.
	 * @param string $type Type of api call. Either theme or plugin.
	 *
	 * @return array
	 */
	private static function get_infos( $slugs, $type ) {
		if ( ! is_array( $slugs ) || empty( $slugs ) ) {
			return [];
		}

		if ( ! in_array( $type, [ self::INFO_REQUEST_TYPE_THEME, self::INFO_REQUEST_TYPE_PLUGIN ], true ) ) {
			return [];
		}

		$transient_name = Manager::TRANSIENTS[ "{$type}_infos" ]['name'];
		$infos          = get_transient( $transient_name );
		if ( empty( $infos ) || ! is_array( $infos ) ) {
			$infos = [];
		}

		$missing_slugs = array_diff( $slugs, array_keys( $infos ) );
		$missing_infos = self::remote_info_request( $missing_slugs, $type );
		$infos         = array_merge( $infos, $missing_infos );
		set_transient( $transient_name, $infos, 60 );
		$infos = array_intersect_key( $infos, array_flip( $slugs ) );
		ksort( $infos );

		return null !== $infos ? $infos : [];
	}

	/**
	 * Get theme infos from wp.org
	 *
	 * @param array $theme_slugs List of slugs to get infos for.
	 */
	public static function get_theme_infos( $theme_slugs ) {
		if ( empty( $theme_slugs ) ) {
			return [];
		}

		$requests = [];
		foreach ( $theme_slugs as $theme_slug ) {
			$requests[] = [
				'url'  => 'https://api.wordpress.org/themes/info/1.2/?action=theme_information&slug=' . $theme_slug,
				'type' => 'GET',
				'data' => [
					'locale' => get_user_locale(),
				],
			];
		}

		if ( class_exists( '\WpOrg\Requests\Requests' ) === true ) {
			$responses = \WpOrg\Requests\Requests::request_multiple( $requests );
		} else {
			// Legacy Fallback for WordPress Versions prior 6.2 .
			$responses = Requests::request_multiple( $requests );
		}

		$theme_infos = [];
		foreach ( $responses as $response ) {
			if ( $response->status_code !== 200 ) {
				continue;
			}

			$data = self::normalize_data( (array) json_decode( $response->body, true ) );
			if ( empty( $data ) ) {
				continue;
			}

			$theme_infos[ $data['slug'] ] = $data;
		}

		return $theme_infos;
	}

	/**
	 * Removes unnecessary data from API response.
	 *
	 * @param array $data List of provided data from WP.org API. Returns empty array if necessary data is missing.
	 */
	private static function normalize_data( $data ) {
		$filtered_data = [];
		$schema = [
			'name',
			'slug',
			'preview_url',
			'screenshot_url',
			'download_link',
			'sections',
		];

		foreach ( $schema as $key ) {
			if ( ! array_key_exists( $key, $data ) ) {
				return [];
			}

			$filtered_data[ $key ] = $data[ $key ];
		}

		return $filtered_data;
	}

	/**
	 * Get plugin infos from wp.org. Wraps get_infos.
	 *
	 * @param array $slugs List of slugs to get infos for.
	 */
	public static function get_plugin_infos( $slugs ) {
		return self::get_infos( $slugs, self::INFO_REQUEST_TYPE_PLUGIN );
	}

	/**
	 * Performs API-Call.
	 *
	 * @param array  $slugs List of slugs to get infos for.
	 * @param string $type Type of api call. Either theme or plugin. Theme does not work anymore.
	 */
	private static function remote_info_request( $slugs, $type ) {
		// Todo: Use plugins_api call.
		$locale = get_user_locale();
		$url    = "https://api.wordpress.org/{$type}s/info/1.2/?action={$type}_information&request[fields]=short_description,icons&request[locale]=$locale";

		if ( empty( $slugs ) || ! is_array( $slugs ) ) {
			return [];
		}

		foreach ( $slugs as $slug ) {
			$url .= "&request[slugs][]=$slug";
		}

		$response = wp_remote_get( $url );
		if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
			return [];
		}

		$body = wp_remote_retrieve_body( $response );
		if ( empty( $body ) ) {
			return [];
		}

		$infos = json_decode( $body, true );

		return null !== $infos ? $infos : [];
	}
}
