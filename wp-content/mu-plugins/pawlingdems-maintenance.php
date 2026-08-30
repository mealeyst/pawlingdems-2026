<?php
/**
 * Plugin Name: Pawling Democrats — Maintenance Splash
 * Description: Shows a branded "under construction" page to the public
 * while the site is being redesigned. Logged-in administrators (and
 * editors) still see the real site normally, so the redesign can be
 * checked and finished without an audience.
 *
 * Toggle with PAWLINGDEMS_MAINTENANCE_MODE in wp-config.php:
 *   define( 'PAWLINGDEMS_MAINTENANCE_MODE', true );
 * Defaults to off if the constant isn't set at all.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'template_redirect', function () {
	if ( ! defined( 'PAWLINGDEMS_MAINTENANCE_MODE' ) || ! PAWLINGDEMS_MAINTENANCE_MODE ) {
		return;
	}

	// Anyone logged in (any committee member with an account) still sees
	// the real site, so the redesign can be checked while it's "live".
	if ( is_user_logged_in() ) {
		return;
	}

	// Never block the login screen, admin-ajax, cron, REST, or feeds needed
	// for the site to keep functioning behind the scenes.
	if ( is_admin() || wp_doing_ajax() || wp_doing_cron() ) {
		return;
	}

	status_header( 503 );
	header( 'Retry-After: 3600' );
	nocache_headers();

	$logo = wp_get_attachment_image_src( get_theme_mod( 'custom_logo' ), 'medium' );
	?>
	<!DOCTYPE html>
	<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="robots" content="noindex, nofollow">
		<title><?php bloginfo( 'name' ); ?> &mdash; <?php esc_html_e( 'Site Update in Progress', 'pawling-democrats' ); ?></title>
		<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Roboto+Slab:wght@500;700&display=swap">
		<style>
			:root {
				--color-navy-900: hsl(218, 74%, 15%);
				--color-navy-700: hsl(219, 59%, 24%);
				--color-gold-600: hsl(38, 92%, 45%);
				--color-cream-50: hsl(38, 45%, 96%);
			}
			* { box-sizing: border-box; }
			body {
				margin: 0;
				min-height: 100vh;
				display: flex;
				align-items: center;
				justify-content: center;
				background: var(--color-navy-900);
				background: linear-gradient(160deg, var(--color-navy-900), var(--color-navy-700));
				color: #fff;
				font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
				text-align: center;
				padding: 2rem;
			}
			.wrap { max-width: 560px; }
			img.logo { max-width: 260px; height: auto; margin-bottom: 2rem; }
			h1 {
				font-family: 'Roboto Slab', Georgia, serif;
				text-transform: uppercase;
				letter-spacing: -0.01em;
				font-size: 1.9rem;
				margin: 0 0 1rem;
				border-bottom: 3px solid var(--color-gold-600);
				padding-bottom: 0.75rem;
				display: inline-block;
			}
			p { font-size: 1.05rem; line-height: 1.6; color: var(--color-cream-50); margin: 0 0 0.75rem; }
			a { color: var(--color-gold-600); }
		</style>
	</head>
	<body>
		<div class="wrap">
			<?php if ( $logo ) : ?>
				<img class="logo" src="<?php echo esc_url( $logo[0] ); ?>" alt="<?php bloginfo( 'name' ); ?>">
			<?php endif; ?>
			<h1><?php esc_html_e( "We're Updating Our Website", 'pawling-democrats' ); ?></h1>
			<p><?php esc_html_e( "The Pawling Town Democratic Committee is refreshing this site with a new look. We'll be back online shortly — thanks for your patience.", 'pawling-democrats' ); ?></p>
			<p><?php esc_html_e( 'Questions in the meantime?', 'pawling-democrats' ); ?> <a href="mailto:info@pawlingdems.org">info@pawlingdems.org</a></p>
		</div>
	</body>
	</html>
	<?php
	exit;
} );
