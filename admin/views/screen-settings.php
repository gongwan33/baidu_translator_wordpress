<div class="wrap">
	<h2 class="baidu-translator-screen-title">
		<img src="<?php echo esc_url( BAIDU_TRANSLATOR_URL . 'admin/assets/images/baidu-logo.png' ); ?>" alt="<?php esc_attr_e( 'Baidu', 'baidu-translator' ); ?>" width="121" height="46">
		<?php _e( 'Translator Settings', 'baidu-translator' ); ?>
	</h2>

	<form action="options.php" method="post">
		<?php settings_fields( 'baidu-translator' ); ?>
		<?php do_settings_sections( 'baidu-translator' ); ?>
		<?php submit_button(); ?>
	</form>
</div>
