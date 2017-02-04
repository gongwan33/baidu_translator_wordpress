=== Translator with Baidu Service  ===
Contributors: gongwan33
Donate link: http://www.joybin.cn/wordpress-plugins/translator-with-baidu-service.html
Tags: translator, internationalization, translation, localization, i18n, Cantonese
Requires at least: 3.8
Tested up to: 4.7
Stable tag: 4.7
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Translate your site in many languages with this plugin from JoyBin, Inc. The translating service provider is Baidu.

== Description ==

Translate your site in many languages with this plugin from JoyBin, Inc. (The service provider is Baidu.) Currently we support 25 languages: Chinese, English, Japanese, Korean, French, Spanish, Thai, Arabic, Russian, Portuguese, German, Italian, Greek, Dutch, Polish, Bulgarian, Estonia, Danish, Finnish, Czech, Romanian, Slovenia, Swedish, Hungarian, Vietnamese. And we support 4 kinds of Chinese: Simplified Chinese, Cantonese, Classical Chinese and Traditional Chinese.

== Installation ==
1. Upload the plugin files to the `/wp-content/plugins/baidu-translator` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Use the Settings->BD Translator screen to configure the plugin.
4. You can use shortcode or widget to add Translator with Baidu Service to your page.
    * Shortcode: Add [baidu_translator] to where you want to insert this plugin. Or click the 'Translator' button and choose 'Insert plugin' when editing the posts and pages.
      For Example:
      &lt;html&gt; 
      &lt;body&gt;
      &lt;div&gt;
      &lt;h&gt;Supper Site&lt;/h&gt;
      &lt;ul&gt;
      &lt;li&gt;HOME&lt;/li&gt;
      &lt;li&gt;ABOUT US&lt;/li&gt;
      &lt;li&gt;&lt;?php do_shortcode('[baidu_translator]');?&gt;&lt;/li&gt;
      &lt;/ul&gt;
      &lt;/div&gt;
      &lt;/body&gt;
      &lt;/html&gt;

    * Widget: Config the widget in Appearance->Widget->BD Translator to add the plugin to the specific part of your page.
5. Set 'no translate' area. Please wrap the content you don't want to translate with the wrapper [notranslate][/notranslate] to tell the plugin not to translate the specific content.
    For Example:
	[notranslate]THE CONTENT YOU DON'T WANT TO TRANSLATE[/notranslate]

== Frequently Asked Questions ==

= How can I get the AppId and Key? =

You can register for Baidu developer at http://api.fanyi.baidu.com. After that you can find your AppID and Key in '管理控制台' panel. Or just leave it blank to use default AppID and Key.

= Why does the plugin fail to translate my page sometimes? =

Using the default AppID and Key means you share the same AppID and Key with all the other users of this plugin. The limitation of the number of words to be translated is 2,000,000 per month. So if too many users use this plugin, the interface to Baidu translating service will be unavailable. In this case, you can register for a new developer ID at http://api.fanyi.baidu.com for free and set to Settings->BaiDu Translator->AppID and Key. 

== Screenshots ==

1. The drop down list of Translator with Baidu Service plugin.
2. The language configuration page.

== Changelog ==
= 1.01 =
* Fix readme file issues. 
* Add icon and banner to assets.

= 1.0 =
* First release.

== Upgrade Notice ==

= 1.01 =
* Fix readme file issues. 
* Add icon and banner to assets.

