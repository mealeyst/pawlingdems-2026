<?php

use Ionos\Assistant\Wizard\Manager;

use const Ionos\Assistant\Wizard\VIEWS_DIR_PATH;

/**
 * @global $args
 */
load_template( VIEWS_DIR_PATH . '/parts/header.php', true, $args );
?>
	<p><?php echo $args['description']; ?></p>

	<div class="usecases">
<?php
foreach ( $args['use_cases'] as $key => $value ) {
	if ( ! isset( $value['headline'] ) || ! isset( $value['icon'] ) ) {
		continue;
	}

	$icon = "<img src=\"data:image/svg+xml;base64,{$value['icon']}\">";
	if ( strpos( $value['icon'], 'dashicons' ) !== false ) {
		$icon = "<span class='dashicons {$value['icon']}'></span>";
	}

	$use_case_headline = $value['headline'];
	printf(
		'<div class="usecase"><input class="casebtn" type="submit" name="%5$s" value="%1$s" id="%2$s" required><label class="case-label" for="%2$s">%4$s%3$s</label></div>',
		$key,
		"ionos_assistant_wizard_use_case_$key",
		__( $use_case_headline, 'ionos-assistant' ),
		$icon,
		Manager::STATE_INPUT_NAMES['use_case']
	);
}
?>
	</div>

<?php
load_template( VIEWS_DIR_PATH . '/parts/footer.php', true, $args );
