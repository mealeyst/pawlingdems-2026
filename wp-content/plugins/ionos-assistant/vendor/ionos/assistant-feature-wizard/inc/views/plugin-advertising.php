<?php
/**
 * @global $args
 */

use Ionos\Assistant\Wizard\Manager;
use Ionos\Assistant\Wizard\View_Helper;

use const Ionos\Assistant\Wizard\VIEWS_DIR_PATH;

load_template( VIEWS_DIR_PATH . '/parts/header.php', true, $args );
View_Helper::print_hidden_fields(
	[
		Manager::STATE_INPUT_NAMES['use_case'],
		Manager::STATE_INPUT_NAMES['theme'],
		Manager::STATE_INPUT_NAMES['plugins'],
	]
);
?>
<h1 class="screen-reader-text"><?php _ex( 'Install Jetpack', 'plugin-advertising-heading', 'ionos-assistant' ); ?></h1>
<img src="<?php echo plugins_url( '/img/jetpack-logo.svg', \Ionos\Assistant\Wizard\FEATURE_MAIN_PLUGIN_FILE_PATH ); ?>" alt="" class="jetpack-logo">
<p class="large-description-teaser"><?php esc_html_e( 'Get essential WordPress security and performance tools by setting up Jetpack', 'ionos-assistant' ); ?></p>
<p class="description"><?php esc_html_e( 'You can install Jetpack immediately with this WordPress installation. Jetpack gives you more options and tools to do even more with your WordPress installation. In order to use Jetpack to its full extent, a separate registration is required.', 'ionos-assistant' ); ?></p>
<div class="buttons">
	<button class="btn primary-btn" type="submit" name="install_promoted"><?php esc_html_e( 'Install Jetpack', 'ionos-assistant' ); ?></button>
	<button class="link-btn" type="submit"><?php esc_html_e( 'Not now, thank you', 'ionos-assistant' ); ?></button>
</div>
<?php
load_template( VIEWS_DIR_PATH . '/parts/footer.php', true, $args );
