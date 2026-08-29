<?php
/**
 * Hoot Theme hooked into the framework
 */

/* === WordPress Blocks === */

/** Add Gutenberg Wide Align support **/
add_theme_support( 'align-wide' );

/** Temporarily remove Gutenberg Widgets Screen **/
if ( apply_filters( 'hoot_disable_widgets_block_editor', true ) ) {
	remove_theme_support( 'widgets-block-editor' );
}

/** Add slightly more opinionated styles for the front end **/
add_theme_support( 'wp-block-styles' );

/** Support custom line heights for paragraphs and headings **/
add_theme_support( 'custom-line-height' );

/** Custom spacing option for blocks like cover and group **/
add_theme_support( 'custom-spacing' );

/** Responsive embedded content **/
add_theme_support( 'responsive-embeds' );


/**
 * Color Pallete
 * Add accent colors to Block Pallete
 */
if ( apply_filters( 'hoot_editor_color_palette', true ) )
	add_action( 'init', 'hoot_wpblock_color_palette' );
function hoot_wpblock_color_palette(){
	$palette = array();
	$defaults = array(
		'#000000' => array( 'black',                 __( 'Black', 'dispatch' ) ),
		'#abb8c3' => array( 'cyan-bluish-gray',      __( 'Cyan bluish gray', 'dispatch' ) ),
		'#ffffff' => array( 'white',                 __( 'White', 'dispatch' ) ),
		'#f78da7' => array( 'pale-pink',             __( 'Pale pink', 'dispatch' ) ),
		'#cf2e2e' => array( 'vivid-red',             __( 'Vivid red', 'dispatch' ) ),
		'#ff6900' => array( 'luminous-vivid-orange', __( 'Luminous vivid orange', 'dispatch' ) ),
		'#fcb900' => array( 'luminous-vivid-amber',  __( 'Luminous vivid amber', 'dispatch' ) ),
		'#7bdcb5' => array( 'light-green-cyan',      __( 'Light green cyan', 'dispatch' ) ),
		'#00d084' => array( 'vivid-green-cyan',      __( 'Vivid green cyan', 'dispatch' ) ),
		'#8ed1fc' => array( 'pale-cyan-blue',        __( 'Pale cyan blue', 'dispatch' ) ),
		'#0693e3' => array( 'vivid-cyan-blue',       __( 'Vivid cyan blue', 'dispatch' ) ),
		'#9b51e0' => array( 'vivid-purple',          __( 'Vivid purple', 'dispatch' ) ),
	);
	if ( apply_filters( 'hoot_editor_accents', true, 'palette' ) ) {
		$palette[] = array(
			'name' => __( 'Theme Accent Color', 'dispatch' ),
			'slug' => 'accent',
			'color' => hoot_get_mod( 'accent_color' ),
		);
		$palette[] = array(
			'name' => __( 'Theme Accent Font Color', 'dispatch' ),
			'slug' => 'accent-font',
			'color' => hoot_get_mod( 'accent_font' ),
		);
	}
	foreach ( $defaults as $key => $value ) {
		$palette[] = array( 'name' => $value[1], 'slug' => $value[0], 'color' => $key );
	}
	add_theme_support( 'editor-color-palette', $palette );
}


/**
 * Block Styles
 */

add_action( 'init', 'hoot_wpblock_styles' );
function hoot_wpblock_styles() {
	register_block_style( 'core/image', array(
		'name'  => 'hoot-image-border',
		'label' => esc_html__( 'Border', 'dispatch' ),
	) );
	register_block_style( 'core/image', array(
		'name'  => 'hoot-image-frame',
		'label' => esc_html__( 'Frame', 'dispatch' ),
	) );
	register_block_style( 'core/list', array(
		'name'  => 'hoot-checklist',
		'label' => __( 'Checkmark', 'dispatch' ),
		'inline_style' =>	'div ul.is-style-hoot-checklist { list-style-type: "\2713"; }' .
							'div ul.is-style-hoot-checklist li { padding-inline-start: 1ch; }',
	) );
	register_block_style( 'core/heading', array(
		'name'  => 'hoot-headblock1',
		'label' => __( 'Style 1', 'dispatch' ),
	) );
	register_block_style( 'core/heading', array(
		'name'  => 'hoot-headblock2',
		'label' => __( 'Style 2', 'dispatch' ),
	) );
	register_block_style( 'core/heading', array(
		'name'  => 'hoot-headblock3',
		'label' => __( 'Style 3', 'dispatch' ),
	) );
}



/**
 * Add Stylesheets
 */

// Load after main stylesheet (and hootkit if available), but before child theme's stylesheet (and child hootkit)
add_action( 'wp_enqueue_scripts', 'hoot_wpblock_assets', 16 );
function hoot_wpblock_assets(){
	$style_uri = hoot_locate_style( 'hoot-theme/blocks/wpblocks' );
	wp_enqueue_style( 'hoot-wpblocks', $style_uri, array(), THEME_VERSION );
}

// Set dynamic css handle to hoot-wpblocks
add_filter( 'hoot_style_builder_inline_style_handle', 'hoot_dynamic_css_wpblock_handle', 4 );
function hoot_dynamic_css_wpblock_handle(){ return 'hoot-wpblocks'; }

// Editor stylesheet (HBS loads @10)
add_action( 'enqueue_block_editor_assets', 'hoot_wpblock_editor_assets', 12 );
function hoot_wpblock_editor_assets(){
	// This is loaded in only Backend...
	$style_uri = hoot_locate_style( 'hoot-theme/blocks/wpblocks-editor' );
	wp_enqueue_style( 'hoot-wpblocks-editor', $style_uri, array(), THEME_VERSION );

	$settings = array();
	$settings['accent_color']         = hoot_get_mod( 'accent_color' );
	$settings['accent_font']          = hoot_get_mod( 'accent_font' );
	extract( apply_filters( 'hoot_custom_css_settings', $settings, 'blocks-editor' ) );
	$dynamic_css = '';
	$dynamic_css .= ':root .has-accent-color' . ',' . '.is-style-outline>.wp-block-button__link:not(.has-text-color), .wp-block-button__link.is-style-outline:not(.has-text-color)'
					. '{ color: ' . $accent_color . '; } ';
	$dynamic_css .= ':root .has-accent-background-color' . ',' . '.wp-block-button__link' . ',' . '.wp-block-search__button, .wp-block-file__button'
					. '{ background: ' . $accent_color . '; } ';
	$dynamic_css .= ':root .has-accent-font-color' . ',' . '.wp-block-button__link' . ',' . '.wp-block-search__button, .wp-block-file__button'
					. '{ color: ' . $accent_font . '; } ';
	$dynamic_css .= ':root .has-accent-font-background-color'
					. '{ background: ' . $accent_font . '; } ';
	wp_add_inline_style( 'hoot-wpblocks-editor', $dynamic_css );
}


/** Add Dynamic CSS **/

add_action( 'hoot_dynamic_cssrules', 'hoot_dynamic_wpblockcss', 8 );
function hoot_dynamic_wpblockcss() {
	$settings = array();
	$settings['accent_color']         = hoot_get_mod( 'accent_color' );
	$settings['accent_font']          = hoot_get_mod( 'accent_font' );
	extract( apply_filters( 'hoot_custom_css_settings', $settings, 'blocks' ) );

	hoot_add_css_rule( array(
						'selector'  => ':root .has-accent-color' . ',' . '.is-style-outline>.wp-block-button__link:not(.has-text-color), .wp-block-button__link.is-style-outline:not(.has-text-color)',
						'property'  => 'color',
						'value'     => $accent_color,
						'idtag'     => 'accent_color',
					) );
	hoot_add_css_rule( array(
						'selector'  => ':root .has-accent-background-color' . ',' . '.wp-block-button__link,.wp-block-button__link:hover' . ',' . '.wp-block-search__button,.wp-block-search__button:hover, .wp-block-file__button,.wp-block-file__button:hover',
						'property'  => 'background',
						'value'     => $accent_color,
						'idtag'     => 'accent_color',
					) );
	hoot_add_css_rule( array(
						'selector'  => ':root .has-accent-font-color' . ',' . '.wp-block-button__link,.wp-block-button__link:hover' . ',' . '.wp-block-search__button,.wp-block-search__button:hover, .wp-block-file__button,.wp-block-file__button:hover',
						'property'  => 'color',
						'value'     => $accent_font,
						'idtag'     => 'accent_font',
					) );
	hoot_add_css_rule( array(
						'selector'  => ':root .has-accent-font-background-color',
						'property'  => 'background',
						'value'     => $accent_font,
						'idtag'     => 'accent_font',
					) );

}