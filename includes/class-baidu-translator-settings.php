<?php
class BaiDu_Translator_Settings {
    protected $lang_map = array (
        'zh' => '中文',
        'wyw' => '文言文',
        'yue' => '粤语',
        'en' => 'English',
        'jp' => '日本語',
        'kor' => '한국어',
        'fra' => 'Français',
        'spa' => 'El español',
        'ara' => ' عربي',
        'th' => 'ภาษาไทย',
        'ru' => 'русский',
        'pt' => 'Português',
        'de' => 'Deutsch',
        'it' => 'In Italiano',
        'el' => 'Ελληνική γλώσσα',
        'nl' => 'De Nederlandse',
        'pl' => 'w języku polskim',
        'bul' => 'на български език',
        'est' => 'eesti',
        'dan' => 'dansk',
        'fin' => 'suomen',
        'cs' => 'česky',
        'rom' => 'în limba română',
        'slo' => 'slovenski jezik',
        'swe' => 'svenska',
        'hu' => 'magyar',
        'cht' => '繁體中文',
        'vie' => 'Tiếng Việt'
    );

    protected $translate_parts = array (
        'body',
        'head',
        'all', 
    );

    protected $lang_list = array (
        'zh',
        'wyw',
        'yue',
        'en',
        'jp',
        'kor',
        'fra',
        'spa',
        'ara',
        'th',
        'ru',
        'pt',
        'de',
        'it',
        'el',
        'nl',
        'pl',
        'bul',
        'est',
        'dan',
        'fin',
        'cs',
        'rom',
        'slo',
        'swe',
        'hu',
        'cht',
        'vie'
    );

    protected $appid;

    protected $key;

	protected $default_lang;

	protected $translate_range;

	protected $support_lang = array ();

	protected $auto_lang;

    protected $bypassid_list = array ();

    public function get_lang_map () {
        return $this->lang_map;
    }

    public function get_translate_parts() {
        return $this->translate_parts;
    }

    public function get_lang_list() {
        return $this->lang_list;
    }

	public function __construct( $args = array() ) {
		$defaults = $this->get_defaults();

		// Initialize with defaults.
		$this->merge_settings( $defaults );

		// Update with any saved settings.
        $options = get_option('baidu_translator');
        if ( $options === false) {
            add_option('baidu_translator', $defaults);
        } else {
		    $this->merge_settings( (array) $options );
        }
		// Update with passed args.
		$this->merge_settings( $args );
	}

	public function get_defaults() {
		return array(
            'appid' => 'joybin',
            'key' => 'joybin',
			'default_lang' => 'zh',
			'translate_range' => 'body',
            'support_lang' => array (
                'zh',
                'en',
                'jp'
            ),
            'auto_lang'  => false,
            'bypassid_list' => array (
                'baidu_translate_drop_list'
            )
		);
	}

	public function get_setting( $key = 'all' ) {
		$defaults = $this->get_defaults();
		$settings = wp_parse_args( get_object_vars( $this ), $defaults );
		$settings = array_intersect_key( $settings, $defaults );

		if ( 'all' != $key ) {
			return isset( $settings[ $key ] ) ? $settings[ $key ] : false;
		}

		return $settings;
	}

	public function set_setting( $key, $value ) {
		$defaults = $this->get_defaults();
        switch ( $key ) {
        case 'appid':
            if (empty ( $value )) {
                $this->appid = 'joybin';
            } else {
                $this->appid = $value;
            }
            break;
        case 'key':
            if (empty ( $value )) {
                $this->key = 'joybin';
            } else {
                $this->key = $value;
            }
            break;
        case 'default_lang' :
            $value = strtolower( $value );
            $this->default_lang = $value;
            break;
        case 'translate_range' :
            $value = strtolower( $value );
            if ( in_array( $value, $this->translate_parts) ) {
                $this->translate_range = $value;
            }
            break;

        case 'support_lang' :
            if ( is_array( $value ) ) {
                $this->support_lang = $value;
            }
            break;

        case 'auto_lang' :
            $this->auto_lang = empty( $value ) ? false : true;
            break;

        case 'bypassid_list' :
            if (is_array ( $value )) {
                $this->bypassid_list =  $value;
            }
            break;
        }
	}

	public function merge_settings( $settings, $diff = '' ) {
		if ( is_array( $settings ) ) {
			foreach ( $settings as $key => $value ) {
				$this->set_setting( $key, $value );
			}
		}

		$settings = wp_parse_args( $this->get_setting(), $settings );

		if ( 'intersect' == $diff ) {
			$settings = array_intersect_key( $settings, $this->get_defaults() );
		}

		return $settings;
	}
}
