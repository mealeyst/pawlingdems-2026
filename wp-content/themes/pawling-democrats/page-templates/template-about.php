<?php
/**
 * Template Name: About Us
 */
get_header();
$photos_uri = get_template_directory_uri() . '/assets/images/photos';
?>

<div class="page-hero has-photo" style="background-image:url('<?php echo esc_url( $photos_uri . '/town-hall.jpg' ); ?>');">
	<span class="eyebrow"><?php esc_html_e( 'Who We Are', 'pawling-democrats' ); ?></span>
	<?php while ( have_posts() ) : the_post(); ?>
		<h1><?php the_title(); ?></h1>
	<?php endwhile; ?>
	<?php pawlingdems_photo_credit_overlay( 'Daniel Case', 'https://commons.wikimedia.org/wiki/File:Pawling,_NY,_town_hall.jpg' ); ?>
</div>

<?php
rewind_posts();
while ( have_posts() ) :
	the_post();
	?>
	<div class="entry-content page-content">
		<?php the_content(); ?>
	</div>
	<?php
endwhile;
?>

<?php get_footer(); ?>
