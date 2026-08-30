<?php
/**
 * Template Name: Get Involved
 */
get_header();
$photos_uri = get_template_directory_uri() . '/assets/images/photos';
?>

<div class="page-hero has-photo" style="background-image:url('<?php echo esc_url( $photos_uri . '/harlem-valley.jpg' ); ?>');">
	<span class="eyebrow"><?php esc_html_e( 'Join Us', 'pawling-democrats' ); ?></span>
	<?php while ( have_posts() ) : the_post(); ?>
		<h1><?php the_title(); ?></h1>
	<?php endwhile; ?>
	<?php pawlingdems_photo_credit_overlay( 'Daniel Case', 'https://commons.wikimedia.org/wiki/File:Harlem_Valley_view_from_Appalachian_Trail,_Pawling,_NY.jpg' ); ?>
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
