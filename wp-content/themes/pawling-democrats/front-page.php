<?php
/**
 * Front page (Home).
 */
get_header();
$photos_uri = get_template_directory_uri() . '/assets/images/photos';
?>

<section class="hero-photo" style="background-image:url('<?php echo esc_url( $photos_uri . '/welcome-sign.jpg' ); ?>');">
	<span class="eyebrow"><?php esc_html_e( 'Pawling, New York', 'pawling-democrats' ); ?></span>
	<h1><?php bloginfo( 'name' ); ?></h1>
	<p><?php bloginfo( 'description' ); ?></p>
	<div class="hero-photo__actions">
		<a class="button button--gold" href="<?php echo esc_url( home_url( '/get-involved/' ) ); ?>"><?php esc_html_e( 'Get Involved', 'pawling-democrats' ); ?></a>
		<a class="button button--outline" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact Us', 'pawling-democrats' ); ?></a>
	</div>
	<?php pawlingdems_photo_credit_overlay( 'Daniel Case', 'https://commons.wikimedia.org/wiki/File:Pawling,_NY,_welcome_sign.jpg' ); ?>
</section>

<?php
while ( have_posts() ) :
	the_post();
	?>
	<div class="section section--cream">
		<div class="section__inner page-content">
			<?php the_content(); ?>
		</div>
	</div>
	<?php
endwhile;
?>

<section class="section">
	<div class="section__inner">
		<div class="section__header">
			<span class="eyebrow"><?php esc_html_e( 'Local Government, Explained', 'pawling-democrats' ); ?></span>
			<h2><?php esc_html_e( 'What Does a Town Democratic Committee Do?', 'pawling-democrats' ); ?></h2>
			<p><?php esc_html_e( "Town committees are the foundation of the Democratic Party — the volunteers who do the on-the-ground work that state and national campaigns depend on.", 'pawling-democrats' ); ?></p>
		</div>
		<div class="pillar-grid">
			<div class="pillar-card pillar-card--brand">
				<div class="pillar-card__header">
					<div class="pillar-card__icon"><?php pawlingdems_icon( 'ballot' ); ?></div>
					<h3><?php esc_html_e( 'Recruit Candidates', 'pawling-democrats' ); ?></h3>
				</div>
				<p><?php esc_html_e( 'We find and support Democrats willing to run for town board, town justice, and other local offices.', 'pawling-democrats' ); ?></p>
			</div>
			<div class="pillar-card pillar-card--accent">
				<div class="pillar-card__header">
					<div class="pillar-card__icon"><?php pawlingdems_icon( 'megaphone' ); ?></div>
					<h3><?php esc_html_e( 'Register Voters', 'pawling-democrats' ); ?></h3>
				</div>
				<p><?php esc_html_e( 'We help neighbors get registered, confirm their polling place, and make a plan to vote in every election.', 'pawling-democrats' ); ?></p>
			</div>
			<div class="pillar-card pillar-card--brand">
				<div class="pillar-card__header">
					<div class="pillar-card__icon"><?php pawlingdems_icon( 'people' ); ?></div>
					<h3><?php esc_html_e( 'Represent Our Districts', 'pawling-democrats' ); ?></h3>
				</div>
				<p><?php esc_html_e( 'Each committee member represents an election district, carrying neighbors\' concerns to the county party.', 'pawling-democrats' ); ?></p>
			</div>
			<div class="pillar-card pillar-card--accent">
				<div class="pillar-card__header">
					<div class="pillar-card__icon"><?php pawlingdems_icon( 'calendar' ); ?></div>
					<h3><?php esc_html_e( 'Host Public Forums', 'pawling-democrats' ); ?></h3>
				</div>
				<p><?php esc_html_e( 'We organize town halls and issue forums so residents can hear directly from candidates and officials.', 'pawling-democrats' ); ?></p>
			</div>
			<div class="pillar-card pillar-card--brand">
				<div class="pillar-card__header">
					<div class="pillar-card__icon"><?php pawlingdems_icon( 'clipboard' ); ?></div>
					<h3><?php esc_html_e( 'Coordinate Volunteers', 'pawling-democrats' ); ?></h3>
				</div>
				<p><?php esc_html_e( 'From canvassing to phone banking to staffing a table at Pawling events, we organize the volunteers who power local campaigns.', 'pawling-democrats' ); ?></p>
			</div>
			<div class="pillar-card pillar-card--accent">
				<div class="pillar-card__header">
					<div class="pillar-card__icon"><?php pawlingdems_icon( 'gavel' ); ?></div>
					<h3><?php esc_html_e( 'Advocate Locally', 'pawling-democrats' ); ?></h3>
				</div>
				<p><?php esc_html_e( 'We show up at town board meetings and speak up for Democratic values on the issues that affect Pawling directly.', 'pawling-democrats' ); ?></p>
			</div>
		</div>
	</div>
</section>

<section class="section section--cream">
	<div class="section__inner">
		<div class="section__header">
			<span class="eyebrow"><?php esc_html_e( 'Our Town', 'pawling-democrats' ); ?></span>
			<h2><?php esc_html_e( 'Proud to Call Pawling Home', 'pawling-democrats' ); ?></h2>
		</div>
		<div class="photo-band">
			<figure>
				<img src="<?php echo esc_url( $photos_uri . '/downtown-pawling.jpg' ); ?>" alt="<?php esc_attr_e( 'Downtown Pawling, New York', 'pawling-democrats' ); ?>" loading="lazy">
				<figcaption><?php esc_html_e( 'Downtown Pawling', 'pawling-democrats' ); ?></figcaption>
				<?php pawlingdems_photo_credit_overlay( 'English836', 'https://commons.wikimedia.org/wiki/File:Downtown_Pawling.JPG' ); ?>
			</figure>
			<figure>
				<img src="<?php echo esc_url( $photos_uri . '/town-hall.jpg' ); ?>" alt="<?php esc_attr_e( 'Pawling Town Hall', 'pawling-democrats' ); ?>" loading="lazy">
				<figcaption><?php esc_html_e( 'Pawling Town Hall', 'pawling-democrats' ); ?></figcaption>
				<?php pawlingdems_photo_credit_overlay( 'Daniel Case', 'https://commons.wikimedia.org/wiki/File:Pawling,_NY,_town_hall.jpg' ); ?>
			</figure>
			<figure>
				<img src="<?php echo esc_url( $photos_uri . '/harlem-valley.jpg' ); ?>" alt="<?php esc_attr_e( 'Harlem Valley view from the Appalachian Trail', 'pawling-democrats' ); ?>" loading="lazy">
				<figcaption><?php esc_html_e( 'View from the Appalachian Trail', 'pawling-democrats' ); ?></figcaption>
				<?php pawlingdems_photo_credit_overlay( 'Daniel Case', 'https://commons.wikimedia.org/wiki/File:Harlem_Valley_view_from_Appalachian_Trail,_Pawling,_NY.jpg' ); ?>
			</figure>
		</div>
	</div>
</section>

<section class="section">
	<div class="section__inner">
		<div class="section__header">
			<span class="eyebrow"><?php esc_html_e( "What's Happening", 'pawling-democrats' ); ?></span>
			<h2><?php esc_html_e( 'Recent Activity', 'pawling-democrats' ); ?></h2>
			<p><?php esc_html_e( 'Updates from committee members on the current political landscape, locally and beyond.', 'pawling-democrats' ); ?></p>
		</div>
		<div class="activity-feed">
			<?php
			$recent = new WP_Query( array(
				'post_type'      => 'post',
				'posts_per_page' => 4,
				'no_found_rows'  => true,
			) );
			while ( $recent->have_posts() ) :
				$recent->the_post();
				?>
				<article class="activity-item">
					<div class="activity-item__date">
						<span class="day"><?php echo esc_html( get_the_date( 'j' ) ); ?></span>
						<span class="month"><?php echo esc_html( get_the_date( 'M' ) ); ?></span>
					</div>
					<div class="activity-item__body">
						<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
						<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 26 ) ); ?></p>
						<a href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read more', 'pawling-democrats' ); ?> &rarr;</a>
					</div>
				</article>
				<?php
			endwhile;
			wp_reset_postdata();
			?>
		</div>
		<p style="text-align:center; margin-top:2rem;">
			<a class="button" href="<?php echo esc_url( home_url( '/blog/' ) ); ?>"><?php esc_html_e( 'Visit the Blog', 'pawling-democrats' ); ?></a>
		</p>
	</div>
</section>

<section class="cta-band">
	<h2><?php esc_html_e( 'Ready to Show Up for Pawling?', 'pawling-democrats' ); ?></h2>
	<p><?php esc_html_e( 'Whether you have an hour or a whole afternoon, there\'s a role for you on the committee.', 'pawling-democrats' ); ?></p>
	<a class="button" href="<?php echo esc_url( home_url( '/get-involved/' ) ); ?>"><?php esc_html_e( 'Get Involved', 'pawling-democrats' ); ?></a>
</section>

<?php get_footer(); ?>
