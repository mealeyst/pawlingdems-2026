<?php
// phpcs:ignoreFile

use Ionos\Assistant\Options;
use Ionos\Assistant\Config;
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
$market = Options::get_market();
$privacy_url = Config::get( 'features.loop.privacy_policy.' . $market );
?>
<div class="loop-card__header">
    <h1 class="headline"><?php esc_html_e( 'Ready to enter the loop?', 'ionos-assistant' ); ?></h1>
    <p class="subline"><?php esc_html_e( 'Join Loop, our customer feedback program', 'ionos-assistant' ); ?></p>
</div>
<p class="description">
	<ul class="loop-argument-list">
		<li class="dashicons-before dashicons-yes"><?php esc_html_e( 'Be the first in line to learn about upcoming features and improvements', 'ionos-assistant' ); ?></li>
		<li class="dashicons-before dashicons-yes"><?php esc_html_e( 'Help us to improve your experience by answering short surveys', 'ionos-assistant' ); ?><span class="sub"><?php esc_html_e( 'Everything will be 100% anonymised and cannot be associated with you', 'ionos-assistant' ); ?></span></li>
		<li class="dashicons-before dashicons-yes"><?php esc_html_e( 'Provide anonymous usage data about how you are using WordPress', 'ionos-assistant' ); ?><span class="sub"><?php esc_html_e( 'No personal or website data will be transferred', 'ionos-assistant' ); ?></span></li>
	</ul>
<div class="buttons">
	<button class="btn primary-btn" type="submit" name="loop_consent"><?php esc_html_e( 'Join feedback program', 'ionos-assistant' ); ?></button>
    <div class="loop-card__policy">
        <p><?php esc_html_e( 'You can leave at any time.', 'ionos-assistant' ); ?></p>
        <a href="<?php echo esc_url( $privacy_url ); ?>" class="link-btn" target="_blank"><?php esc_html_e( 'Privacy Policy', 'ionos-assistant' ); ?></a>
    </div>
	<button class="link-btn" type="submit"><?php esc_html_e( 'Maybe later', 'ionos-assistant' ); ?></button>
</div>
<?php
load_template( VIEWS_DIR_PATH . '/parts/footer.php', true, $args );
