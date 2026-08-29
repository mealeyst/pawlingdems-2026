<?php

use const Ionos\Assistant\JetpackBackupFlow\VIEWS_DIR_PATH;

load_template( VIEWS_DIR_PATH . '/parts/header.php', true );
?>
<h1 class="screen-reader-text"><?php _e( 'Installing Jetpack Backup', 'ionos-assistant' ); ?></h1>
<img src="<?php echo plugins_url( '/img/jetpack-logo.svg', \Ionos\Assistant\JetpackBackupFlow\FEATURE_MAIN_PLUGIN_FILE_PATH ); ?>" alt="" class="jetpack-logo">
<p><?php _e( 'Please wait a moment while we are installing Jetpack Backup for you.', 'ionos-assistant' ); ?></p>
<?php
load_template( VIEWS_DIR_PATH . '/parts/footer.php', true );