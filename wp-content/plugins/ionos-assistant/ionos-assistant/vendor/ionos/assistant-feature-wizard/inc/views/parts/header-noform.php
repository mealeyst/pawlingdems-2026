<?php
/**
 * @global $args
 */

use Ionos\Assistant\Config;

?>
<div class="wrapper">
	<div class="header">
		<img class="logo" src="<?php echo Config::get( 'branding.logo_variant1' ); ?>" />
	</div>
	<div class="container">
		<p class="counter"><?php echo ! empty( $args['counter_text'] ) ? $args['counter_text'] : __( 'Counter missing', 'ionos-assistant' ); ?></p>
		<?php
		if ( ! empty( $args['heading_text'] ) ) :
			?>
			<h1 class="headline"><?php echo $args['heading_text']; ?></h1>
			<?php
		endif;
		?>
		<div>
			<div class="content">
