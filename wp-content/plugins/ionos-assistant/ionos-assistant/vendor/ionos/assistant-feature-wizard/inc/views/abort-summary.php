<?php
/**
 * @global $args
 */

use Ionos\Assistant\Wizard\Controllers\Summary;
use Ionos\Assistant\Wizard\Manager;
use Ionos\Assistant\Wizard\View_Helper;

use const Ionos\Assistant\Wizard\VIEWS_DIR_PATH;

load_template( VIEWS_DIR_PATH . '/parts/header.php', true, $args );

$plugins = $args[ Manager::STATE_INPUT_NAMES['plugins'] ];

View_Helper::print_hidden_fields(
	[
		Manager::STATE_INPUT_NAMES['plugins'],
	]
);
?>
	<img class="abort-summary-img" src="https://s.w.org/style/images/about/WordPress-logotype-simplified.png" alt="WordPress Logo">
	<div class="preview-text">
		<h4><?php esc_html_e( 'WordPress Vanilla', 'ionos-assistant' ); ?></h4>
		<h4><?php esc_html_e( 'Plugins to be installed', 'ionos-assistant' ); ?></h4>

		<ul class="plugins-list">
			<?php
			if ( ! empty( $args['plugins'] ) ) {
				foreach ( $args['plugins'] as $key ) {
					?>
					<li class="text"><?php echo Summary::get_plugin_name( $key ); ?></li>
					<?php
				}
			}
			?>
		</ul>
		<button class="link-btn" type="submit" name="step" value="abort-plugin-selection"><span class="dashicons dashicons-edit"></span><?php esc_html_e( 'Edit plugins selection', 'ionos-assistant' ); ?></button>
	</div>
	<div class="buttons">
		<button class="btn primary-btn" type="submit"><?php esc_html_e( 'Install', 'ionos-assistant' ); ?></button>
		<a class="link-btn" href="<?php echo add_query_arg( [ 'page=ionos-assistant' => '' ], get_admin_url() . 'admin.php' ); ?>"><?php esc_html_e( 'Reset', 'ionos-assistant' ); ?></a>
	</div>
<?php
load_template( VIEWS_DIR_PATH . '/parts/footer.php', true, $args );
