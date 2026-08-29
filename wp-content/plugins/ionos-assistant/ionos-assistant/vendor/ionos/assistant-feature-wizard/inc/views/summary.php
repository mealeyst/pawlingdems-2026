<?php
/**
 * @global $args
 */

use Ionos\Assistant\Wizard\Controllers\Summary;
use Ionos\Assistant\Wizard\Manager;
use Ionos\Assistant\Wizard\View_Helper;
use Ionos\Assistant\Wizard\Controllers\Blueprint_Upload;
use const Ionos\Assistant\Wizard\FEATURE_MAIN_PLUGIN_FILE_PATH;
use const Ionos\Assistant\Wizard\VIEWS_DIR_PATH;

load_template( VIEWS_DIR_PATH . '/parts/header.php', true, $args );

if ( empty( Blueprint_Upload::get_transient_content() ) ) {
	$use_case       = $args[ Manager::STATE_INPUT_NAMES['use_case'] ];
	$theme          = $args[ Manager::STATE_INPUT_NAMES['theme'] ];
	$preview_link   = $args[ Manager::STATE_INPUT_NAMES['preview_link'] ];
	$plugins        = $args[ Manager::STATE_INPUT_NAMES['plugins'] ];
	$screenshot_url = $args['info']['screenshot_url'];

	View_Helper::print_hidden_fields(
		[
			Manager::STATE_INPUT_NAMES['use_case'],
			Manager::STATE_INPUT_NAMES['theme'],
			Manager::STATE_INPUT_NAMES['plugins'],
			Manager::STATE_INPUT_NAMES['install_promoted'],
		]
	);
} else {
	$themes       = $args[ Manager::STATE_INPUT_NAMES['themes'] ];
	$active_theme = $args[ Manager::STATE_INPUT_NAMES['active_theme'] ];
	$plugins      = $args[ Manager::STATE_INPUT_NAMES['plugins'] ];

	View_Helper::print_hidden_fields(
		[
			Manager::STATE_INPUT_NAMES['themes'],
			Manager::STATE_INPUT_NAMES['active_theme'],
			Manager::STATE_INPUT_NAMES['plugins'],
		]
	);
}
?>
<?php if ( empty( Blueprint_Upload::get_transient_content() ) && ! empty( $screenshot_url ) ) { ?>
	<img class="summary-img" src="<?php echo( $screenshot_url ); ?>" alt="<?php echo( $theme ); ?>">
<?php } elseif ( Manager::is_blueprint_enabled() ) { ?>
	<img class="summary-img" src="<?php echo Summary::get_active_theme_img( $active_theme ); ?>" alt="blueprint">
<?php } else { ?>
	<img class="summary-img" src="<?php echo plugins_url( '/img/placeholder.svg', FEATURE_MAIN_PLUGIN_FILE_PATH ); ?>" alt="fallback">
<?php } ?>

<div class="preview-text">
	<?php if ( empty( Blueprint_Upload::get_transient_content() ) ) { ?>
		<h2><?php esc_html_e( 'Theme selected', 'ionos-assistant' ); ?></h2>
		<p><?php echo $args['info']['name']; ?></p>
	<?php } ?>

	<?php if ( ! empty( Blueprint_Upload::get_transient_content() ) ) { ?>
		<?php if ( ! empty( $args['active_theme'] ) ) { ?>
			<h2><?php esc_html_e( 'Theme to be installed and activated', 'ionos-assistant' ); ?></h2>
			<ul class="themes-list">
				<li class="text"><?php echo Summary::get_theme_name( $args['active_theme'] ); ?></li>
			</ul>
			<?php
		}
		if ( ! empty( $args['themes'] ) ) {
			?>
			<h2><?php esc_html_e( 'Themes to be installed', 'ionos-assistant' ); ?></h2>
			<ul class="themes-list">
				<?php
				foreach ( $args['themes'] as $key ) {
					if ( $key !== $args['active_theme'] && $key !== Summary::get_theme_name( $key ) ) {
						?>
						<li class="text"><?php echo Summary::get_theme_name( $key ); ?></li>
						<span class="divider"></span>
						<?php
					}
				}
				?>
			</ul>
			<?php
		}
	}

	if ( ! empty( $args['plugins'] ) ) {
		?>
		<h2><?php esc_html_e( 'Plugins to be installed', 'ionos-assistant' ); ?></h2>
		<ul class="plugins-list">
			<?php
			foreach ( $args['plugins'] as $key ) {
				if ( $key !== Summary::get_plugin_name( $key ) ) {
					?>
					<li class="text"><?php echo Summary::get_plugin_name( $key ); ?></li>
					<span class="divider"></span>
					<?php
				}
			}

			if ( empty( Blueprint_Upload::get_transient_content() ) ) {
				foreach ( $args['required_plugins'] as $key => $info ) {
					if ( ! is_array( Summary::get_plugin_name( $key ) ) && $key !== Summary::get_plugin_name( $key ) ) {
						?>
						<div class="text"><?php echo Summary::get_plugin_name( $key ); ?></div>
						<span class="divider"></span>
						<?php
					}
				}
			}
			?>
		</ul>
		<?php
	}

	if ( empty( Blueprint_Upload::get_transient_content() ) ) {
		?>
		<button class="link-btn" type="submit" name="step" value="plugin-selection"><span class="dashicons dashicons-edit"></span><?php esc_html_e( 'Edit plugins selection', 'ionos-assistant' ); ?></button>
	<?php } ?>
</div>

<div class="buttons">
	<button class="btn primary-btn" type="submit"><?php esc_html_e( 'Install', 'ionos-assistant' ); ?></button>
	<a class="link-btn" href="<?php echo add_query_arg( [ 'page=ionos-assistant' => '' ], get_admin_url() . 'admin.php' ); ?>"><?php esc_html_e( 'Reset', 'ionos-assistant' ); ?></a>
</div>
<?php
load_template( VIEWS_DIR_PATH . '/parts/footer.php', true, $args );
