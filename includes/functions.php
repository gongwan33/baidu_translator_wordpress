<?php
function baidu_translator( $args = array() ) {
	static $instance = 0;
	$instance++;

	if ( $instance > 1 ) {
		return;
	}

	$settings = new BaiDu_Translator_Settings( $args );

	$embed = new BaiDu_Translator_Embed( $settings );
	$embed->render();
}
