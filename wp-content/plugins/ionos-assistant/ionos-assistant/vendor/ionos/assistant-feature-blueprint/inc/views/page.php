<?php
/**
 * Templatefile for the admin view.
 *
 * @package Bluerpint
 */

namespace Ionos\Blueprint\Views;

?>
<div class="wrap ionos-blueprint">
	<h1 class="wp-heading-inline"><?php esc_html_e( 'Blueprints', 'ionos-blueprint' ); ?></h1>
	<hr class="wp-header-end">
	<div class="card">
		<?php
		echo '<p>' . preg_replace( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			"/\n+/",
			'</p><p>',
			trim(
				esc_html__(
					'When you click the button below WordPress will create an JSON file for you to save to your computer.
This file contains information about which plugins and themes you have installed and which theme is currently in use.
Once you have saved the download file, you can use the import function of the IONOS Assistant in another WordPress installation.',
					'ionos-blueprint'
				)
			)
		) . '</p>';
		?>
		<form method="post" id="ionos-blueprint-generate">
			<div>
				<input type="checkbox" name="all-themes" value="all-themes" id="ionos-blueprint-all-themes" class="toggle ionos-blueprint-slide-up-down" data-target="#ionos-blueprint-themes" checked>
				<label for="ionos-blueprint-all-themes" class="toggle">
					<?php esc_html_e( 'Export all themes', 'ionos-blueprint' ); ?>
				</label>	

				<ul id="ionos-blueprint-themes" class="ionos-blueprint-list">
				<?php
				foreach ( $args['themes'] as $ionos_theme ) {
					$ionos_parent_attribute = ! empty( $ionos_theme->parent() )
						? sprintf( ' data-parent=%s', esc_attr( $ionos_theme->parent()->get_stylesheet() ) )
						: '';

					$ionos_childtheme_hint = ! empty( $ionos_theme->parent() )
						// translators: %s is the name of the parent theme.
						? '<span class="hint">' . sprintf( esc_html__( 'Child theme of %s', 'ionos-blueprint' ), $ionos_theme->parent()->name ) . '</span>'
						: '';

					printf(
						'<li><input type="checkbox" id="%1$s" name="theme" class="toggle" value="%1$s"%3$s><label for="%1$s" class="toggle">%2$s%4$s</label></li>',
						esc_attr( $ionos_theme->get_stylesheet() ),
						esc_html( $ionos_theme->name ),
						$ionos_parent_attribute, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped above.
						$ionos_childtheme_hint // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped above.
					);
				};
				?>
				</ul>
			</div>
			<div>
				<input type="checkbox" name="all-plugins" value="all-plugins" id="ionos-blueprint-all-plugins" class="toggle ionos-blueprint-slide-up-down" data-target="#ionos-blueprint-plugins" checked>
				<label for="ionos-blueprint-all-plugins" class="toggle">
					<?php esc_html_e( 'Export all plugins', 'ionos-blueprint' ); ?>
				</label>	

				<ul id="ionos-blueprint-plugins" class="ionos-blueprint-list">
				<?php
				foreach ( $args['plugins'] as $ionos_slug => $ionos_plugin ) {
					printf(
						'<li><input type="checkbox" id="%1$s" name="plugin" class="toggle" value="%1$s"><label for="%1$s" class="toggle">%2$s</label></li>',
						esc_attr( $ionos_slug ),
						esc_html( $ionos_plugin['Name'] )
					);
				};
				?>
				</ul>
			</div>

			<div class="margin-top">
				<button class="button button-primary" id="ionos-blueprint-submit" type="submit"><?php esc_html_e( 'Download Blueprint file', 'ionos-blueprint' ); ?></button>
			</div>
			</form>
	</div>
</div>
