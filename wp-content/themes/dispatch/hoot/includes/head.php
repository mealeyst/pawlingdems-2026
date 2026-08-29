<?php
/**
 * Functions for outputting common site data in the `<head>` area of a site.
 *
 * @package hoot
 * @subpackage framework
 * @since hoot 1.0.0
 */

/* Adds common theme items to <head>. */
add_action( 'wp_head', 'hoot_meta_charset',  0 );
add_action( 'wp_head', 'hoot_meta_compatible',  0 );
add_action( 'wp_head', 'hoot_meta_responsive', 1 );
add_action( 'wp_head', 'hoot_meta_template', 1 );
add_action( 'wp_head', 'hoot_link_pingback', 3 );
add_action( 'wp_head', 'hoot_link_profile', 3 );

/**
 * Adds the meta charset to the header.
 *
 * @since 1.0.0
 * @access public
 * @return void
 */
function hoot_meta_charset() {
	printf( '<meta charset="%s" />' . "\n", get_bloginfo( 'charset' ) );
}

/**
 * Adds the meta http-equiv to the header.
 *
 * @since 1.0.0
 * @access public
 * @return void
 */
function hoot_meta_compatible() {
	echo '<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> <!-- Enable IE Highest available mode (compatibility mode); users with GCF will have page rendered using Google Chrome Frame -->' . "\n";
}

/**
 * Adds the meta for responsive theme.
 *
 * @since 1.0.0
 * @access public
 */
function hoot_meta_responsive() {
	echo '<meta name="HandheldFriendly" content="True">' . "\n";
	echo '<meta name="MobileOptimized" content="767">' . "\n";
	echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">' . "\n";
}

/**
 * Generates the relevant template info.  Adds template meta with theme version.  Uses the theme 
 * name and version from style.css.
 * filter hook.
 *
 * @since 1.0.0
 * @access public
 * @return void
 */
function hoot_meta_template() {
	$theme    = wp_get_theme( get_template() );
	$template = sprintf( '<meta name="template" content="%s %s" />' . "\n", esc_attr( $theme->get( 'Name' ) ), esc_attr( $theme->get( 'Version' ) ) );

	echo apply_filters( 'hoot_meta_template', $template );
}

/**
 * Adds the pingback link to the header.
 *
 * @since 1.0.0
 * @access public
 * @return void
 */
function hoot_link_pingback() {
	if ( 'open' === get_option( 'default_ping_status' ) )
		printf( '<link rel="pingback" href="%s" />' . "\n", get_bloginfo( 'pingback_url' ) );
}

/**
 * Adds the profile link to the header.
 *
 * @since 1.0.0
 * @access public
 * @return void
 */
function hoot_link_profile() {
	echo '<link rel="profile" href="http://gmpg.org/xfn/11" />' . "\n";
}