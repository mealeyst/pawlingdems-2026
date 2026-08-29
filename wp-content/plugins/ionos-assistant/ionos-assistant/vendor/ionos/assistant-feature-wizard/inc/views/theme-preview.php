<?php
/**
 * @global $args
 */

use Ionos\Assistant\Wizard\Manager;
use Ionos\Assistant\Config;
use Ionos\Assistant\Wizard\View_Helper;

use const Ionos\Assistant\Wizard\VIEWS_DIR_PATH;

$use_case       = $args[ Manager::STATE_INPUT_NAMES['use_case'] ];
$theme          = $args[ Manager::STATE_INPUT_NAMES['theme'] ];
$preview_link   = $args[ Manager::STATE_INPUT_NAMES['preview_link'] ];
$screenshot_url = $args['info']['screenshot_url'];
$description    = Config::get( "features.wizard.usecases.$use_case.themes.$theme.description" );

if ( ! $description ) {
	if ( isset( $args['info']['sections'] ) && isset( $args['info']['sections']['description'] ) ) {
		$description = $args['info']['sections']['description'];
	}
}

load_template( VIEWS_DIR_PATH . '/parts/header.php', true, $args );

View_Helper::print_hidden_fields(
	[
		Manager::STATE_INPUT_NAMES['use_case'],
		Manager::STATE_INPUT_NAMES['theme'],
	]
);

?>
	<img class="preview-img" src="<?php echo $screenshot_url; ?>" alt="<?php echo $theme; ?>">
	<div class="preview-text">
		<?php
		if ( ! empty( $preview_link ) ) {
			printf(
				'<a class="link-btn" target="_blank" href="%s"><span class="dashicons dashicons-search"></span>%s</a>',
				$preview_link,
				esc_html( 'Theme Preview', 'ionos-assistant' )
			);
		}
		?>
		<?php
		if ( $description ) {
			echo "<p class='theme-description'>$description</p>";
		}
		?>
	</div>

	<div class="buttons">
		<button class="btn primary-btn" type="submit"><?php esc_html_e( 'Next Step', 'ionos-assistant' ); ?></button>
		<button class="link-btn" type="submit" name="step" value="theme-selection"><?php esc_html_e( 'Back', 'ionos-assistant' ); ?></button>
	</div>
<?php
load_template( VIEWS_DIR_PATH . '/parts/footer.php', true, $args );
