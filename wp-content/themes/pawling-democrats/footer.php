</main><!-- #main-content -->

<footer class="site-footer">
	<div class="site-footer__inner">
		<div class="footer-about">
			<?php if ( has_custom_logo() ) : ?>
				<div class="footer-logo"><?php the_custom_logo(); ?></div>
			<?php else : ?>
				<h3><?php bloginfo( 'name' ); ?></h3>
			<?php endif; ?>
		</div>

		<div class="footer-nav">
			<h3><?php esc_html_e( 'Menu', 'pawling-democrats' ); ?></h3>
			<?php
			wp_nav_menu( array(
				'theme_location' => 'footer',
				'container'      => false,
				'menu_class'     => '',
				'fallback_cb'    => false,
			) );
			?>
		</div>

		<?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
			<div class="footer-widgets">
				<?php dynamic_sidebar( 'footer-1' ); ?>
			</div>
		<?php endif; ?>
	</div>
	<div class="site-footer__bottom">
		&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'Paid for by the Pawling Town Democratic Committee.', 'pawling-democrats' ); ?>
		<p class="site-footer__credits">
			<?php
			printf(
				/* translators: %s: link to Wikimedia Commons */
				esc_html__( 'Town photos courtesy of Wikimedia Commons contributors, licensed %s.', 'pawling-democrats' ),
				'<a href="https://creativecommons.org/licenses/by-sa/3.0/">CC BY-SA 3.0</a>'
			);
			?>
		</p>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
