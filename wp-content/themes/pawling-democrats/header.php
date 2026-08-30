<?php
/**
 * Site header.
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="screen-reader-text" href="#main-content"><?php esc_html_e( 'Skip to content', 'pawling-democrats' ); ?></a>

<header class="site-header">
	<div class="site-header__inner">
		<?php if ( has_custom_logo() ) : ?>
			<?php the_custom_logo(); ?>
		<?php else : ?>
			<a class="site-branding" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/PawlingDem.svg' ); ?>" alt="" width="48" height="48">
				<span class="site-branding__text"><?php bloginfo( 'name' ); ?></span>
			</a>
		<?php endif; ?>

		<button class="menu-toggle" aria-controls="primary-navigation" aria-expanded="false">
			<?php esc_html_e( 'Menu', 'pawling-democrats' ); ?>
		</button>

		<nav class="primary-navigation" id="primary-navigation" aria-label="<?php esc_attr_e( 'Primary', 'pawling-democrats' ); ?>">
			<?php
			wp_nav_menu( array(
				'theme_location' => 'primary',
				'container'      => false,
				'fallback_cb'    => 'pawlingdems_fallback_menu',
			) );
			?>
		</nav>
	</div>
</header>

<main id="main-content">
