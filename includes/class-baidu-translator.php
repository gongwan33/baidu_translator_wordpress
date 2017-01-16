<?php
class BaiDu_Translator {

	public function load_plugin() {
		self::load_textdomain();
		self::register_shortcodes();

		if ( is_admin() ) {
			self::load_admin();
		}

        require_once('baidu-translate-proxy.php');
		add_action( 'widgets_init', array( $this, 'register_widgets' ) );
        add_action( 'wp_ajax_option-params', 'get_params_from_server');
        add_action( 'wp_ajax_nopriv_option-params', 'get_params_from_server');
	}

	public function load_textdomain() {
		load_plugin_textdomain( 'baidu-translator', false, dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages' );
	}

	public function load_admin() {
		$admin = new BaiDu_Translator_Admin();
		$admin->load();
	}

	public function register_widgets() {
		register_widget( 'BaiDu_Translator_Widget' );
	}

	public function register_shortcodes() {
		add_shortcode( 'baidu_translator', array( $this, 'baidu_translator_shortcode' ) );
		add_shortcode( 'notranslate', array( $this, 'notranslate_shortcode' ) );
	}

	public function baidu_translator_shortcode( $atts = array() ) {
		ob_start();
		baidu_translator( $atts );
		return ob_get_clean();
	}

	public function notranslate_shortcode( $atts = array(), $content = '' ) {
		$is_inline = false === strpos( $content, "\n" );

		if ( ! $is_inline ) {
			$content = preg_replace( '#^</p>#', '', $content );
			$content = preg_replace( '#<p>$#', '', $content );
			$content = wpautop( $content );
		}

		$tag = $is_inline ? 'span' : 'div';
		return sprintf( '<%1$s class="notranslate">%2$s</%1$s>', $tag, $content );
	}

	public static function shortcode_bool( $var ) {
		$falsey = array( 'false', '0', 'no', 'n' );
		return ( ! $var || in_array( strtolower( $var ), $falsey ) ) ? false : true;
	}
}
