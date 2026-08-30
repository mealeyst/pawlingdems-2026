<?php
/**
 * Generic page template.
 */
get_header();
?>

<div class="page-hero">
	<?php while ( have_posts() ) : the_post(); ?>
		<h1><?php the_title(); ?></h1>
	<?php endwhile; ?>
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
