<?php
/**
 * Functions for handling theme main stylesheets in the frontend.
 *
 * @package hoot
 * @subpackage framework
 * @since hoot 1.0.0
 */

/* Register Main styles. */
add_action( 'wp_enqueue_scripts', 'hoot_register_styles', 0 );

/**
 * Load Main styles. It's a good practice to load any other stylesheet before the main style
 * Recommended priorities for various styles:
 * wp_enqueue_scripts @10 :                        Theme's third party styles, Plugins
 * wp_enqueue_scripts @12 : [hoot-style]           Main theme stylesheet
 * wp_enqueue_scripts @14 : [theme-hootkit]        Main theme hkit stylesheet
 * wp_enqueue_scripts @16 : [hoot-wpblocks]        Main theme wpblocks stylesheet
 * wp_enqueue_scripts @18 : [hoot-child-style]     Child theme stylesheet
 * wp_enqueue_scripts @20 : [theme-child-hootkit]  Child theme hkit stylesheet
 * wp_enqueue_scripts @99 : Dynamic inline stylesheet attached to handle:
 *                          Default      : [hoot-style]
 *                          Filtered @2  : [theme-hootkit]
 *                          Filtered @4  : [hoot-wpblocks]
 *                          Filtered @8  : [hoot-child-style] Filtered in hoot child themes only : Not recommended in custom child themes
 *                          Filtered @10 : [theme-child-hootkit]
 */
add_action( 'wp_enqueue_scripts', 'hoot_enqueue_styles', 12 );
add_action( 'wp_enqueue_scripts', 'hoot_enqueue_child_styles', 18 );

/* Load the development stylsheet (unminified) in script debug mode. */
add_filter( 'stylesheet_uri', 'hoot_min_stylesheet_uri', 5, 2 );

/**
 * Registers stylesheets for the framework. This function merely registers styles with WordPress using
 * the wp_register_style() function. It does not load any stylesheets on the site. If a theme wants to 
 * register its own custom styles, it should do so on the 'wp_enqueue_scripts' hook.
 *
 * @since 1.0.0
 * @access public
 * @return void
 */
function hoot_register_styles() {

	/* Get styles. */
	$styles = hoot_get_styles();

	/* Loop through each style and register it. */
	foreach ( $styles as $style => $args ) {

		$defaults = array( 
			'handle'  => 'hoot' . $style, 
			'src'     => '',
			'deps'    => null,
			'version' => false,
			'media'   => 'all'
		);

		$args = wp_parse_args( $args, $defaults );

		if ( !empty( $args['src'] ) ) {
			wp_register_style(
				sanitize_key( $args['handle'] ),
				esc_url( $args['src'] ),
				is_array( $args['deps'] ) ? $args['deps'] : null,
				preg_replace( '/[^a-z0-9_\-.]/', '', strtolower( $args['version'] ) ),
				esc_attr( $args['media'] )
			);
		}

	}

}

/**
 * Tells WordPress to load the styles using the wp_enqueue_style() function.
 *
 * @since 1.0.0
 * @access public
 * @return void
 */
function hoot_enqueue_styles() {
	$style = hoot_get_styles( 'parent' );
	if ( !empty( $style['handle'] ) && !empty( $style['src'] ) )
		wp_enqueue_style( sanitize_key( $style['handle'] ) );
}
function hoot_enqueue_child_styles() {
	if ( is_child_theme() ) :
	$style = hoot_get_styles( 'child' );
	if ( !empty( $style['handle'] ) && !empty( $style['src'] ) )
		wp_enqueue_style( sanitize_key( $style['handle'] ) );
	endif;
}

/**
 * Returns an array of the available styles for use in themes.
 *
 * @since 1.0.0
 * @access public
 * @return array
 */
function hoot_get_styles( $return = '' ) {

	static $styles;
	if ( empty( $styles ) ) :

	/* Initialize */
	$styles = array();

	/* If a child theme is active, add the parent theme's style. */
	// Cannot use 'hoot_locate_style()' as the function will always return child
	// theme stylesheet. Hence we have to manually locate and add parent stylesheet.
	if ( is_child_theme() ) {

		$loadminified = ( defined( 'HOOT_DEBUG' ) ) ?
						( ( HOOT_DEBUG ) ? false : true ) :
						hoot_get_mod( 'load_minified', 0 );

		/* Get the parent theme stylesheet (if a '.min' version of the stylesheet exists, use it) */
		if ( $loadminified && file_exists( trailingslashit( THEME_DIR ) . "style.min.css" ) )
			$src = trailingslashit( THEME_URI ) . "style.min.css";
		else
			// We can skip file_exists for src as parent style.css will always be there.
			$src = trailingslashit( THEME_URI ) . "style.css";

		$styles['parent'] = array(
			'handle' => 'hoot-style',
			'src' => $src,
			'version' => THEME_VERSION,
			);
		$styles['child'] = array(
			'handle' => 'hoot-child-style',
			'src' => get_stylesheet_uri(),
			'version' => CHILD_THEME_VERSION,
			);
	}

	else {
		$styles[ 'parent' ] = array(
			'handle' => 'hoot-style',
			'src' => get_stylesheet_uri(),
			'version' => THEME_VERSION,
			);
	}

	endif;

	/* Return the array of styles. */
	return ( $return && !empty( $styles[ $return ] ) ) ? $styles[ $return ] : $styles;
}

/**
 * Filters the 'stylesheet_uri' returned by get_stylesheet_uri() to allow theme developers to offer a
 * minimized version of their main 'style.css' file. It will detect if a 'style.min.css' file is available
 * and use it if HOOT_DEBUG is disabled.
 *
 * @since 1.0.0
 * @access public
 * @param string  $stylesheet_uri      The URI of the active theme's stylesheet.
 * @param string  $stylesheet_dir_uri  The directory URI of the active theme's stylesheet.
 * @return string $stylesheet_uri
 */
function hoot_min_stylesheet_uri( $stylesheet_uri, $stylesheet_dir_uri ) {

	if ( defined( 'HOOT_DEBUG' ) )
		$loadminified = ( HOOT_DEBUG ) ? false : true;
	else
		$loadminified = hoot_get_mod( 'load_minified', 0 );

	/* Use the .min stylesheet if available. */
	if ( $loadminified ) {

		/* Remove the stylesheet directory URI from the file name. */
		$stylesheet = str_replace( trailingslashit( $stylesheet_dir_uri ), '', $stylesheet_uri );

		/* Change the stylesheet name to 'style.min.css'. */
		$stylesheet = str_replace( '.css', ".min.css", $stylesheet );

		/* If the stylesheet exists in the stylesheet directory, set the stylesheet URI to the dev stylesheet. */
		if ( file_exists( trailingslashit( get_stylesheet_directory() ) . $stylesheet ) )
			$stylesheet_uri = esc_url( trailingslashit( $stylesheet_dir_uri ) . $stylesheet );

	}

	/* Return the theme stylesheet. */
	return $stylesheet_uri;

}