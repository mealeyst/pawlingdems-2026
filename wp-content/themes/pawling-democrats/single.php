<?php
/**
 * Single blog post.
 */
get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article <?php post_class( 'single-post' ); ?>>
		<header class="entry-header">
			<h1><?php the_title(); ?></h1>
			<div class="entry-meta">
				<?php
				printf(
					/* translators: 1: post date, 2: post author */
					esc_html__( 'Posted on %1$s by %2$s', 'pawling-democrats' ),
					esc_html( get_the_date() ),
					esc_html( get_the_author() )
				);
				?>
			</div>
		</header>

		<?php if ( has_post_thumbnail() ) : ?>
			<div class="entry-content">
				<?php the_post_thumbnail( 'large' ); ?>
			</div>
		<?php endif; ?>

		<div class="entry-content">
			<?php the_content(); ?>
		</div>
	</article>
	<?php
endwhile;

get_footer();
