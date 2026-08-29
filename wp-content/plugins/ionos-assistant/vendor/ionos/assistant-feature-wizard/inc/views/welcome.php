<?php
// Todo: Use class attributes instead of using a global var
/**
 * @global $args
 */

use Ionos\Assistant\Wizard\Manager;

use const Ionos\Assistant\Wizard\VIEWS_DIR_PATH;

load_template( VIEWS_DIR_PATH . '/parts/header.php', true, $args );
?>

	<p><?php esc_html_e( 'Our Assistant will help you to get started with WordPress. You can select a Theme and pick from a list of suitable Plugins for your use case. After completing the following steps, you can start creating content and put some finishing touches to your website!', 'ionos-assistant' ); ?></p>
	<button class="btn primary-btn" type="submit"><?php esc_html_e( 'Let‘s go!', 'ionos-assistant' ); ?></button>
	<div class="btn-row">
		<?php
		// Blueprint is only available for PHP 7.4 and higher.
		if ( ! empty( get_transient( Manager::BLUEPRINT_ENABLED_TRANSIENT_NAME ) ) && PHP_VERSION_ID >= 70400 ) {
			?>
			<button class="link-btn" type="submit" name="step" value="blueprint-upload"><?php esc_html_e( 'Import Blueprint file', 'ionos-assistant' ); ?></button>
			<span class="divider"></span>
		<?php } ?>
		<button class="link-btn" type="submit" name="step" value="abort-plugin-selection"><?php esc_html_e( 'Configure installation myself', 'ionos-assistant' ); ?></button>
	</div>
<?php
load_template( VIEWS_DIR_PATH . '/parts/footer.php', true, $args );
