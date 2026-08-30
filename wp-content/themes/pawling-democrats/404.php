<?php
/**
 * 404 template.
 */
get_header();
?>

<div class="error-404">
	<h1><?php esc_html_e( 'Page Not Found', 'pawling-democrats' ); ?></h1>
	<p><?php esc_html_e( "The page you're looking for doesn't exist or has moved.", 'pawling-democrats' ); ?></p>
	<a class="button" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to Home', 'pawling-democrats' ); ?></a>
</div>

<?php get_footer(); ?>
