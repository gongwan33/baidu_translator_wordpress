<?php
class BaiDu_Translator_Embed {
	protected $settings;

	public function __construct( BaiDu_Translator_Settings $settings ) {
		$this->settings = $settings;
	}

	public function render() {
        $settings = get_option('baidu_translator');
		$snippet = $this->get_snippet($settings);

		echo $snippet;
	}

    protected function get_snippet($settings) {
        $snippet = '<script src="http://apps.bdimg.com/libs/jquery/1.9.1/jquery.min.js"></script>'."\n";
        //Origin script
        //$snippet .= '<script src="'.BAIDU_TRANSLATOR_URL.'includes/js/md5.js"></script>'."\n";
        //compressed script
        $snippet .= '<script src="'.BAIDU_TRANSLATOR_URL.'includes/js/md5.min.js"></script>'."\n";
        
        //Origin script
        //$snippet .= '<script id="baidu-translate" src="'.BAIDU_TRANSLATOR_URL.'includes/js/baidu-translate.js" data="'.BAIDU_TRANSLATOR_URL.'" defer></script>'."\n";
        //compressed script
        $snippet .= '<script id="baidu-translate" src="'.BAIDU_TRANSLATOR_URL.'includes/js/baidu-translate.min.js" data="'.BAIDU_TRANSLATOR_URL.'" defer></script>'."\n";
        $snippet .= <<<EOT
<style>
#drop_title{border:2px solid #000000;padding-left:2px;font-size:12px;background:white;height:28px;line-height:23px}
#drop_title #down_array {float:right;height:0px;width:0px;padding:0px;border-style:solid;border-color:#000000 transparent;border-width:8px 6px 0px 6px;margin:8px 3px;}
#baidu_translate_drop_list {float:right;cursor:pointer;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none}
#baidu_translate_drop_list ul{border:2px solid #000000;background:#fbfbfb;margin-top:-2px;display:none;margin-left:0px;text-align:left;padding:0px 0px}
#baidu_translate_drop_list li{margin-left:2px;margin-right:2px;list-style-type:none;font-size:12px;text-align:left;text-indent:0px;height:28px;padding-top:2px}
#baidu_translate_drop_list li:hover{background:aqua}
</style>
<div id="baidu_translate_drop_list">
<div id="drop_title">
EOT;
        $snippet .= '<span>Select language</span><span id="down_array"></span>';
        $snippet .= <<<EOT
</div>
<ul>
EOT;

        $lang_map = $this->settings->get_lang_map();
        foreach ($settings['support_lang'] as $lang) {
            $snippet .= '<li id="'.$lang.'"><img src="'.BAIDU_TRANSLATOR_URL.'includes/img/'.$lang.'.png" /><span>'.$lang_map[$lang].'</span></li>';
        }
        $snippet .= <<<EOT
</ul>
</div>
EOT;
        return $snippet;
    }
}
