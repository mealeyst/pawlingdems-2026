<?php
/**
 * View that lets the user choose if he wants to install Jetpack.
 */

use Ionos\Assistant\JetpackBackupFlow\Manager;

use const Ionos\Assistant\JetpackBackupFlow\VIEWS_DIR_PATH;

load_template( VIEWS_DIR_PATH . '/parts/header.php', true );
?>
<form>
	<input type="hidden" name="coupon" value="<?php echo $_GET['coupon']; ?>">
	<input type="hidden" name="page" value="<?php echo Manager::HIDDEN_PAGE_SLUG; ?>">
	<input type="hidden" name="step" value="install">

	<h1 class="screen-reader-text"><?php _e( 'Jetpack Backup', 'ionos-assistant' ); ?></h1>
	<img src="<?php echo plugins_url( '/img/jetpack-logo.svg', \Ionos\Assistant\JetpackBackupFlow\FEATURE_MAIN_PLUGIN_FILE_PATH ); ?>" class="jetpack-logo" alt="">
	<p><?php _e( 'We are going to install Jetpack Backup now.', 'ionos-assistant' ); ?></p>
	<div class="buttons">
		<button class="btn primarybtn" type="submit"><?php _e( 'Ok', 'ionos-assistant' ); ?></button>
		<a class="linkbtn" href="<?php echo admin_url(); ?>"><?php _e( 'No thanks', 'ionos-assistant' ); ?></a>
	</div>
</form>
<?php
load_template( VIEWS_DIR_PATH . '/parts/footer.php', true );