<?php
/**
 * @global $args
 */

use Ionos\Assistant\Wizard\Controllers\Abort_Plugin_Selection;

use const Ionos\Assistant\Wizard\VIEWS_DIR_PATH;

load_template( VIEWS_DIR_PATH . '/parts/header-mediumcontent.php', true, $args );
$skip_url = add_query_arg(
	[
		'assistant_wizard_completed' => '1',
	],
	get_admin_url()
);
?>
	<p><?php esc_html_e( 'WordPress Plugins extend WordPress functionality in almost any form imaginable. We picked a few useful plugins from the thousands of available plugins to make starting your website more manageable. If you want to add more plugins, you can do so later.', 'ionos-assistant' ); ?></p>

	<a class="link-btn" href="<?php echo $skip_url; ?>"><?php esc_html_e( 'You can also skip this step', 'ionos-assistant' ); ?></a>
	<h2><?php esc_html_e( 'Select your plugins', 'ionos-assistant' ); ?></h2>
	<div class="abort-plugins">
		<?php foreach ( $args['plugins'] as $key => $info ) : ?>
			<div class="abort-plugin">
				<?php $id = "assistant_wizard_plugin_$key"; ?>
				<input type="checkbox" name="plugins[]" value="<?php echo esc_attr( $key ); ?>" id="<?php echo esc_attr( $id ); ?>" class="plugincheckbox">
				<label for="<?php echo esc_attr( $id ); ?>">
					<div class="abort-plugin-content">
						<span><?php echo Abort_Plugin_Selection::get_plugin_name( $key ); ?></span>
						<div class="abort-plugin-text">
							<span class="title"><?php echo Abort_Plugin_Selection::get_plugin_description( $key ); ?></span>
							<div class="plugin-info">
								<span>Version <?php echo Abort_Plugin_Selection::get_plugin_version( $key ); ?></span>
								<span><?php echo Abort_Plugin_Selection::get_plugin_author( $key ); ?></span>
							</div>
						</div>
					</div>
				</label>
			</div>
		<?php endforeach; ?>
	</div>

	<div class="buttons">
		<button class="btn primary-btn" type="submit"><?php esc_html_e( 'Confirm selection', 'ionos-assistant' ); ?></button>
		<button class="link-btn" type="submit" name="step" value="welcome"><?php esc_html_e( 'Back', 'ionos-assistant' ); ?></button>
	</div>
<?php
load_template( VIEWS_DIR_PATH . '/parts/footer.php', true, $args );
