<?php

class BaiDu_Translator_Admin {
	public function load() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_assets' ), 1 );
		add_action( 'sidebar_admin_setup', array( $this, 'enqueue_assets' ) );
		add_action( 'media_buttons', array( $this, 'editor_button' ), 15 );
	}

	public function admin_menu() {
		$settings = new BaiDu_Translator_Settings();

		$screen = new BaiDu_Translator_Admin_Screen_Settings( $settings );
		$screen->load();
	}

    public function register_assets() {
        //Origin css
        //wp_register_style( 'baidu-translator-admin', BAIDU_TRANSLATOR_URL . 'admin/assets/css/admin.css' );
        //Compressed css
		wp_register_style( 'baidu-translator-admin', BAIDU_TRANSLATOR_URL . 'admin/assets/css/admin.min.css' );
		//wp_style_add_data( 'baidu-translator-admin', 'rtl', 'replace' );
        //Origin js
		//wp_register_script( 'baidu-translator-editor', BAIDU_TRANSLATOR_URL . 'admin/assets/js/editor.js', array( 'shortcode' ) );
        //Compressed js
        wp_register_script( 'baidu-translator-editor', BAIDU_TRANSLATOR_URL . 'admin/assets/js/editor.min.js', array( 'shortcode' ) );
	}

	public function enqueue_assets() {
		wp_enqueue_style( 'baidu-translator-admin' );
	}

	public function editor_button( $editor_id ) {
		wp_enqueue_style( 'baidu-translator-admin' );
		wp_enqueue_script( 'baidu-translator-editor' );
		?>
		<div class="baidu-translator-button-group" data-editor="<?php echo esc_attr( $editor_id ); ?>">
			<button class="button" title="<?php esc_attr_e( 'BaiDu Translator', 'baidu-translator' ); ?>">
				<img src="<?php echo esc_url( BAIDU_TRANSLATOR_URL . 'admin/assets/images/baidu-logo-small.png' ); ?>" alt="<?php esc_attr_e( 'BaiDu Translator', 'baidu-translator' ); ?>" width="90" height="26">
			</button>
			<ul class="baidu-translator-dropdown-menu">
				<li><a href="#" data-baidu-translator="insert-plugin"><?php _e( 'Insert plugin', 'baidu-translator' ); ?></a></li>
				<li><a href="#" data-baidu-translator="notranslate"><?php _e( 'Don\'t translate', 'baidu-translator' ); ?></a></li>
			</ul>
		</div>
		<?php
	}
}
