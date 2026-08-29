<?php
// Todo: Use class attributes instead of using a global var
/**
 * @global $args
 */
use Ionos\Assistant\Wizard\Manager;

use const Ionos\Assistant\Wizard\VIEWS_DIR_PATH;

load_template( VIEWS_DIR_PATH . '/parts/header.php', true, $args );
?>

	<div class="error msg">
		<p><?php esc_html_e( 'Blueprint-file is corrupt.', 'ionos-assistant' ); ?></p>
		<p><?php esc_html_e( 'Please, upload a working Blueprint-file.', 'ionos-assistant' ); ?></p>
	</div>
	<button class="link-btn" type="submit" name="step" value="blueprint-upload"><?php esc_html_e( 'Back', 'ionos-assistant' ); ?></button>
<?php
load_template( VIEWS_DIR_PATH . '/parts/footer.php', true, $args );
