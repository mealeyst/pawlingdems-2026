<?php // phpcs:disable IonosWordPress.Files.NamespaceIsRequired.NoNamespace

/**
 * This file outputs the header, logo, counter, headline, and some hidden fields like next_step.
 *
 * @global $args
 */

use Ionos\Assistant\Config;

$ionos_wizard_counter_text = ! empty( $args['counter_text'] )
	? $args['counter_text']
	: __( 'Counter missing', 'ionos-assistant' );

?>
<div class="wrapper">
	<div class="header">
		<img class="logo" src="<?php echo esc_attr( Config::get( 'branding.logo_variant1' ) ); ?>" />
	</div>
	<div class="container">
		<p class="counter"><?php echo esc_html( $ionos_wizard_counter_text ); ?></p>
		<?php
		if ( ! empty( $args['heading_text'] ) ) :
			?>
			<h1 class="headline"><?php echo esc_html( $args['heading_text'] ); ?></h1>
			<?php
		endif;
		?>
		<form method="get">
			<input type="hidden" name="page" value="ionos-assistant">
			<input type="hidden" name="step" value="<?php echo ! empty( $args['next_step'] ) ? esc_attr( $args['next_step'] ) : ''; ?>">
			<div class="content">
