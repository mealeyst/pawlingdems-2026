<?php
/**
 * Pawling Democrats theme setup.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PAWLINGDEMS_VERSION', '1.4.2' );

function pawlingdems_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'custom-logo', array(
		'height'      => 96,
		'width'       => 288,
		'flex-height' => true,
		'flex-width'  => true,
	) );
	add_theme_support( 'align-wide' );
	add_theme_support( 'responsive-embeds' );

	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'pawling-democrats' ),
		'footer'  => __( 'Footer Menu', 'pawling-democrats' ),
	) );

	add_theme_support( 'editor-color-palette', array(
		array(
			'name'  => __( 'Navy (darkest)', 'pawling-democrats' ),
			'slug'  => 'navy-900',
			'color' => 'hsl(218, 74%, 15%)',
		),
		array(
			'name'  => __( 'Navy', 'pawling-democrats' ),
			'slug'  => 'navy-700',
			'color' => 'hsl(219, 59%, 24%)',
		),
		array(
			'name'  => __( 'Navy (light)', 'pawling-democrats' ),
			'slug'  => 'navy-500',
			'color' => 'hsl(218, 47%, 34%)',
		),
		array(
			'name'  => __( 'Accent Blue', 'pawling-democrats' ),
			'slug'  => 'accent-500',
			'color' => 'rgba(0, 113, 255, 1)',
		),
		array(
			'name'  => __( 'Gold', 'pawling-democrats' ),
			'slug'  => 'gold-600',
			'color' => 'hsl(38, 92%, 45%)',
		),
		array(
			'name'  => __( 'Cream', 'pawling-democrats' ),
			'slug'  => 'cream-50',
			'color' => 'hsl(38, 45%, 96%)',
		),
		array(
			'name'  => __( 'Off White', 'pawling-democrats' ),
			'slug'  => 'white-50',
			'color' => 'hsl(216, 33%, 97%)',
		),
		array(
			'name'  => __( 'Charcoal', 'pawling-democrats' ),
			'slug'  => 'black-400',
			'color' => 'hsl(228, 8%, 12%)',
		),
	) );
}
add_action( 'after_setup_theme', 'pawlingdems_setup' );

function pawlingdems_assets() {
	wp_enqueue_style(
		'pawlingdems-fonts',
		'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Roboto+Slab:wght@500;700&display=swap',
		array(),
		null
	);
	wp_enqueue_style( 'pawlingdems-style', get_stylesheet_uri(), array(), PAWLINGDEMS_VERSION );
	wp_enqueue_script( 'pawlingdems-main', get_template_directory_uri() . '/assets/js/main.js', array(), PAWLINGDEMS_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'pawlingdems_assets' );

function pawlingdems_register_footer_widgets() {
	register_sidebar( array(
		'name'          => __( 'Footer', 'pawling-democrats' ),
		'id'            => 'footer-1',
		'description'   => __( 'Widgets shown in the site footer.', 'pawling-democrats' ),
		'before_widget' => '<div class="footer-widget">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3>',
		'after_title'   => '</h3>',
	) );
}
add_action( 'widgets_init', 'pawlingdems_register_footer_widgets' );

/**
 * Fallback menu markup for the `primary` location when no menu has been
 * assigned yet in Appearance > Menus.
 */
function pawlingdems_fallback_menu() {
	echo '<ul>';
	wp_list_pages( array( 'title_li' => '' ) );
	echo '</ul>';
}

/**
 * Render a small attribution line for a theme-supplied Wikimedia Commons
 * photo. All of the /assets/images/photos/ files are CC BY-SA 3.0 and
 * require attribution — see credits.txt in that folder for source links.
 */
function pawlingdems_photo_credit( $photographer, $commons_url ) {
	printf(
		'<p class="photo-credit">%1$s: <a href="%2$s">%3$s</a>, %4$s</p>',
		esc_html__( 'Photo', 'pawling-democrats' ),
		esc_url( $commons_url ),
		esc_html( $photographer ),
		esc_html__( 'CC BY-SA 3.0 / Wikimedia Commons', 'pawling-democrats' )
	);
}

/**
 * Same attribution, but as a small badge overlaid on the bottom-right
 * corner of the photo it credits. Use inside a `position: relative`
 * container (a hero section, a .photo-band figure) that wraps the image.
 */
function pawlingdems_photo_credit_overlay( $photographer, $commons_url ) {
	printf(
		'<span class="photo-credit-overlay">%1$s: <a href="%2$s">%3$s</a>, %4$s</span>',
		esc_html__( 'Photo', 'pawling-democrats' ),
		esc_url( $commons_url ),
		esc_html( $photographer ),
		esc_html__( 'CC BY-SA 3.0 / Wikimedia Commons', 'pawling-democrats' )
	);
}

/**
 * A small inline SVG icon, used on the pillar cards. Keeping these as
 * inline markup (rather than an icon font or image library) avoids an
 * extra dependency for half a dozen simple glyphs.
 */
function pawlingdems_icon( $name ) {
	$icons = array(
		'ballot'  => '<svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16v16H4z"/><path d="M8 10l3 3 5-5" /></svg>',
		'people'  => '<svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="8" r="3"/><path d="M2 20c0-3.3 3.1-6 7-6s7 2.7 7 6"/><circle cx="17" cy="8" r="2.5"/><path d="M16 14.2c2.9.6 5 2.7 5 5.8"/></svg>',
		'megaphone' => '<svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 9v6h5l7 4V5l-7 4H4z"/><path d="M7 15v4"/><path d="M19 9a4 4 0 0 1 0 6"/></svg>',
		'gavel'   => '<svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="2"><rect x="9.5" y="4.75" width="9" height="4.5" rx="2" transform="rotate(45 14 7)"/><path d="M11 10l-5 5"/><rect x="4" y="17" width="13" height="3" rx="1.5"/></svg>',
		'calendar' => '<svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/></svg>',
		'clipboard' => '<svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="2"><rect x="5" y="4" width="14" height="17" rx="2"/><rect x="9" y="2" width="6" height="4" rx="1"/><path d="M8.5 13l2.5 2.5 5-5"/></svg>',
	);

	echo $icons[ $name ] ?? '';
}
