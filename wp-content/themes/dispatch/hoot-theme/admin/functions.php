<?php
/**
 * Helper Functions
 */

/**
 * Set Theme About Page Tags
 * @access public
 * @return mixed
 */
function hoot_abouttag( $index = 'slug' ) {
	static $tags;
	if ( empty( $tags ) ) {
		$child = defined( 'CHILD_THEME_NAME' ) ? CHILD_THEME_NAME : '';
		$is_official_child = false;
		if ( $child ) {
			$checks = apply_filters( 'hoot_theme_config_childtheme_array', array() );
			foreach ( $checks as $check ) {
				if ( stripos( $child, $check ) !== false ) {
					$is_official_child = true;
					break;
				}
			}
		}
		$tags = $is_official_child ? array() : array(
			'slug' => 'dispatch',
			'name' => __( 'Dispatch', 'dispatch' ),
			'label' => __( 'Dispatch Dashboard', 'dispatch' ),
			'vers' => THEME_VERSION,
			'shot' => ( file_exists( trailingslashit( THEME_DIR ) . 'screenshot.jpg' ) ) ? trailingslashit( THEME_URI ) . 'screenshot.jpg' : (
						( file_exists( trailingslashit( THEME_DIR ) . 'screenshot.png' ) ) ? trailingslashit( THEME_URI ) . 'screenshot.png' : ''
						),
			'fullshot' => ( file_exists( trailingslashit( HOOT_THEMEDIR ) . 'admin/images/screenshot.jpg' ) ) ? trailingslashit( HOOT_THEMEURI ) . 'admin/images/screenshot.jpg' : (
				( file_exists( trailingslashit( HOOT_THEMEDIR ) . 'admin/images/screenshot.png' ) ) ? trailingslashit( HOOT_THEMEURI ) . 'admin/images/screenshot.png' : ''
				)
		);
		$tags = apply_filters( 'hoot_abouttags', $tags );
		if ( ! is_array( $tags ) ) $tags = array();
		if ( !empty( $tags['name'] ) ) $tags['name'] = esc_html( $tags['name'] );
		if ( !empty( $tags['slug'] ) ) $tags['slug'] = sanitize_html_class( $tags['slug'] );
		if ( !empty( $tags['vers'] ) ) $tags['vers'] = sanitize_text_field( $tags['vers'] );
		if ( !empty( $tags['shot'] ) ) $tags['shot'] = esc_url( $tags['shot'] );
		if ( !empty( $tags['fullshot'] ) ) $tags['fullshot'] = esc_url( $tags['fullshot'] );
	}
	return ( $index === true ? $tags : ( ( isset( $tags[ $index ] ) ) ? $tags[ $index ] : '' ) );
}