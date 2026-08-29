<?php

Ionos_Assistant_View::load_template( 'card/header-default' );
foreach ( $usecase_data['plugins'] as $plugin ) {
	echo sprintf( '<input name="plugins[]" value="%s" hidden />', $plugin );
}

wp_nonce_field( 'activate' );
echo sprintf( '<input name="redirect_to" value="%s" hidden />', $usecase_data['redirect_to'] );
echo sprintf( '<input name="usecase" value="%s" hidden />', $usecase_name );

?>

<div class="card-content">
	<div class="card-content-inner">
		<h2><?php echo sprintf(esc_html__( '%s Installation', 'ionos-assistant' ), $usecase_data['title']); ?></h2>
		<p><?php echo sprintf(esc_html__( 'We are going to install %s plugin now.', 'ionos-assistant' ), $usecase_data['title']); ?></p>
	</div>
</div>

<?php
Ionos_Assistant_View::load_template( 'card/footer', array(
	'card_actions' => array(
		'left'  => array(),
		'right' => array(
			'install-hidden-usecase' => array(
				'label' => esc_html__( 'Proceed', 'ionos-assistant' ),
				'class' => 'button button-primary',
				'data' => array(
					'action' => 'install-hidden-usecase'
				)
			),
			'cancel' => array(
				'label' => esc_html__( 'Cancel', 'ionos-assistant' ),
				'class' => 'button',
				'href'  => esc_url( admin_url() )
			)
		)
	)
) );
?>
