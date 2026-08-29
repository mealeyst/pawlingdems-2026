<?php
/**
 * @global $args
 */

use Ionos\Assistant\Wizard\Controllers\Install;

use const Ionos\Assistant\Wizard\VIEWS_DIR_PATH;

$total            = $args['install_data']['total'];
$pending_installs = count( $args['install_data']['plugins'] );

load_template( VIEWS_DIR_PATH . '/parts/header.php', true, $args );

if ( $args['is_pre_install_view'] === true ) { ?>
	<p><?php esc_html_e( 'We’re starting to install your selected components in a moment.', 'ionos-assistant' ); ?></p>
<?php } else { ?>
	<div class="loading"></div>
	<p><?php Install::get_text( $pending_installs ); ?></p>
	<?php
}

load_template( VIEWS_DIR_PATH . '/parts/footer.php', true, $args );
