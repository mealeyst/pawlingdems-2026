<?php
// Todo: Use class attributes instead of using a global var
/**
 * @global $args
 */

use const Ionos\Assistant\Wizard\VIEWS_DIR_PATH;

load_template( VIEWS_DIR_PATH . '/parts/header-noform.php', true, $args );

?>
	<p><?php esc_html_e( 'Import your assets like plugins or theme directly into your website using a generated Blueprint file. You can change every aspect of your site later.', 'ionos-assistant' ); ?></p>
	<a href="<?php echo esc_url( esc_attr__( 'https://www.ionos.com/help?id=5257', 'ionos-assistant' ) ); ?>" target="_blank"><?php esc_html_e( 'How to create and use Blueprints', 'ionos-assistant' ); ?></a>

	<div class="upload-area" ondrop="upload_file(event)" ondragover="return false">
		<div id="drag_upload_file">
			<p class="headline-small"><?php esc_html_e( 'Drop file for upload', 'ionos-assistant' ); ?></p>
			<p><?php esc_html_e( 'or', 'ionos-assistant' ); ?></p>
			<p><button class="btn secondarybtn" type="submit" name="save" value="summary" onclick="file_explorer();" ><?php esc_html_e( 'Select file', 'ionos-assistant' ); ?></button></p>
			<input type="file" name="blueprint_file" id="blueprint_file" accept="application/JSON"/ />
			<span><?php esc_html_e( 'Allowed file type: JSON', 'ionos-assistant' ); ?></span>
		</div>
	</div>

	<a class="link-btn" href="#" onclick="back_btn();return false;"><?php esc_html_e( 'Back', 'ionos-assistant' ); ?></a>
<?php
load_template( VIEWS_DIR_PATH . '/parts/footer.php', true, $args );
