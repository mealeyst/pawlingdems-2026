<?php
/**
 * Template Name: Contact
 */
get_header();
?>

<div class="page-hero">
	<?php while ( have_posts() ) : the_post(); ?>
		<h1><?php the_title(); ?></h1>
	<?php endwhile; ?>
</div>

<div class="contact-columns">
	<div class="contact-address">
		<?php
		rewind_posts();
		while ( have_posts() ) :
			the_post();
			the_content();
		endwhile;
		?>
	</div>
	<div class="contact-form">
		<?php
		if ( shortcode_exists( 'contact-form-7' ) ) {
			echo do_shortcode( '[contact-form-7 id="3" title="Contact form 1"]' );
		} else {
			echo '<p>' . esc_html__( 'Contact form is unavailable right now — please reach us by mail at the address shown.', 'pawling-democrats' ) . '</p>';
		}
		?>
	</div>
</div>

<?php get_footer(); ?>
