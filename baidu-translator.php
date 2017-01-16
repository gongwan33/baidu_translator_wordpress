<?php
/**
 * Plugin Name: BaiDu Translator
 * Plugin URI: http://www.joybin.cn/wordpress-plugins/baidu-translator
 * Description: Translate your website in many languages with the Bai Du Translator plugin from JoyBin, Inc. (28 languages supported)
 * Version: 1.0
 * Author: JoyBin, Inc.
 * Author URI: http://www.joybin.cn/
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Domain Path: /languages
 * Text Domain: baidu-translator
 */

/*  Copyright 2010 - 2016 Wagner Wang  (email : wagner@joybin.cn)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    The structure of this plugin refers to Bing Translator by Microsoft Open Technologies Inc. (https://wordpress.org/plugins/bing-translator/)
*/

global $baidu_translator;

if ( ! defined( 'BAIDU_TRANSLATOR_PATH' ) ) {
	define( 'BAIDU_TRANSLATOR_PATH', dirname( __FILE__ ) . '/' );
}

if ( ! defined( 'BAIDU_TRANSLATOR_URL' ) ) {
	define( 'BAIDU_TRANSLATOR_URL', plugin_dir_url( __FILE__ ) );
}

include( BAIDU_TRANSLATOR_PATH . 'includes/functions.php' );

function baidu_translator_autoloader( $class ) {
	if ( 0 !== strpos( $class, 'BaiDu_Translator' ) ) {
		return;
	}

	$file  = dirname( __FILE__ );
	$file .= ( false === strpos( $class, 'Admin' ) ) ? '/includes/' : '/admin/includes/';
	$file .= 'class-' . strtolower( str_replace( '_', '-', $class ) ) . '.php';

	if ( file_exists( $file ) ) {
		require_once( $file );
	}
}
spl_autoload_register( 'baidu_translator_autoloader' );

$baidu_translator = new BaiDu_Translator();

add_action( 'plugins_loaded', array( $baidu_translator, 'load_plugin' ) );
