<?php
/**
 * Blog index (used for the "Blog" posts page).
 */
get_header();

$blog_page = get_option( 'page_for_posts' );
$blog_title = $blog_page ? get_the_title( $blog_page ) : __( 'Blog', 'pawling-democrats' );
?>

<div class="page-hero">
	<h1><?php echo esc_html( $blog_title ); ?></h1>
</div>

<div class="blog-grid">
	<?php if ( have_posts() ) : ?>
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article class="post-card">
				<?php if ( has_post_thumbnail() ) : ?>
					<a class="post-card__thumb" href="<?php the_permalink(); ?>">
						<?php the_post_thumbnail( 'medium_large' ); ?>
					</a>
				<?php endif; ?>
				<div class="post-card__body">
					<div class="post-card__meta"><?php echo esc_html( get_the_date() ); ?></div>
					<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24 ) ); ?></p>
					<a href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read more', 'pawling-democrats' ); ?> &rarr;</a>
				</div>
			</article>
			<?php
		endwhile;
		?>
	<?php else : ?>
		<p><?php esc_html_e( 'No posts yet — check back soon.', 'pawling-democrats' ); ?></p>
	<?php endif; ?>
</div>

<div class="container">
	<?php the_posts_pagination(); ?>
</div>

<?php get_footer(); ?>
