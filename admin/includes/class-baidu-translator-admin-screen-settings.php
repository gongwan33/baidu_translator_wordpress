<?php

class BaiDu_Translator_Admin_Screen_Settings {
	protected $settings;
	public function __construct( BaiDu_Translator_Settings $settings ) {
		$this->settings = $settings;
	}

	public function load() {
		self::add_menu_item();
		self::register_settings();
		self::add_sections();
		self::add_settings();
	}

	public function add_menu_item() {
		add_options_page(
			__( 'BD Translator', 'baidu-translator' ),
			__( 'BD Translator', 'baidu-translator' ),
			'manage_options',
			'baidu-translator',
			array( $this, 'render_screen' )
		);

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	public function register_settings() {
		register_setting( 'baidu-translator', 'baidu_translator', array( $this, 'sanitize_settings' ) );
	}

	public function add_sections() {
		add_settings_section(
			'translation-settings',
			__( 'Translator Settings', 'baidu-translator' ),
			'__return_null',
			'baidu-translator'
		);
	}

    public function add_settings() {
        add_settings_field(
			'appid',
			__( 'AppID', 'baidu-translator' ),
			array( $this, 'render_field_appid' ),
			'baidu-translator',
			'translation-settings'
        );

        add_settings_field(
			'key',
			__( 'Key', 'baidu-translator' ),
			array( $this, 'render_field_key' ),
			'baidu-translator',
			'translation-settings'
        );

		add_settings_field(
			'default_lang',
			__( 'Default language', 'baidu-translator' ),
			array( $this, 'render_field_default_lang' ),
			'baidu-translator',
			'translation-settings'
        );

        add_settings_field(
			'translate_range',
			__( 'Translate range', 'baidu-translator' ),
			array( $this, 'render_field_translate_range' ),
			'baidu-translator',
			'translation-settings'
        );

        add_settings_field(
			'support_lang',
			__( 'Support languages', 'baidu-translator' ),
			array( $this, 'render_field_support_lang' ),
			'baidu-translator',
			'translation-settings'
		);

        add_settings_field(
			'auto_lang',
			__( 'Auto-Detect', 'baidu-translator' ),
			array( $this, 'render_field_auto_lang' ),
			'baidu-translator',
			'translation-settings'
		);

        add_settings_field(
			'bypassid_list',
            __( 'Bypass ID（the IDs of the elements）', 'baidu-translator' ),
			array( $this, 'render_field_bypassid_list' ),
			'baidu-translator',
			'translation-settings'
		);
	}

	public function sanitize_settings( $value ) {
		if ( ! isset( $value['auto_lang'] ) ) {
			$value['auto_lang'] = false;
        } else {
            $value['auto_lang'] = true;
        }

        if ( isset( $value['bypassid_list'] ) ) {
            $list = explode(';', $value['bypassid_list']);
            $listVal = array ();

            foreach ( $list as $item ) {
                $item = esc_attr( trim( $item ) );
                if ( !empty ( $item ) ) {
                    $listVal[] = $item; 
                }
            }

            $value['bypassid_list'] = $listVal;
        }

        if ( isset( $value['appid'] ) ) {
            if( empty ( $value['appid'] ) ) {
                $value['appid'] = 'joybin';
            } else {
                $value['appid'] = esc_attr( trim( $value['appid'] ) );
            }
        }

        if ( isset( $value['key'] ) ) {
            if( empty ( $value['key'] ) ) {
                $value['key'] = 'joybin';
            } else {
                $value['key'] = esc_attr( trim($value['key'] ) );
            }
        }

		// Sanitize the settings.
		$settings = new Baidu_Translator_Settings();
		$value = $settings->merge_settings( $value, 'intersect' );
 
		return $value;
	}

	public function enqueue_assets( $hook_suffix ) {
		if ( 'settings_page_baidu-translator' !== $hook_suffix ) {
			return;
		}

		wp_enqueue_style( 'baidu-translator-admin' );
	}

	public function render_screen() {
		include( BAIDU_TRANSLATOR_PATH . 'admin/views/screen-settings.php' );
	}

    public function render_field_appid() {
		?>
            <span class="description"><?php _e( 'Baidu developer AppID. To apply, please click ', 'baidu-translator' ); ?><a target="_blank" href="http://api.fanyi.baidu.com"><?php _e('here.', 'baidu-translator'); ?></a></span>
        <p class="baidu-translator-togglable-field">
        <?php $appid = $this->settings->get_setting('appid');
        if ( $appid === 'joybin' ) {
            $text = '';
        } else {
            $text = $appid;
        }
        echo '<input type="text" name="baidu_translator[appid]" id="baidu-translator-appid" value="'.$text.'"></input>';
             ?>
		</p>
		<span class="description-footer"><?php _e( 'If you don\'t have this ID, please leave it blank to use the default ID (2,000,000 words/month limited for all the users without valid developer ID).', 'baidu-translator' ); ?></span>
		<?php
	}

    public function render_field_key() {
		?>
		<span class="description"><?php _e( 'Baidu developer Key. To apply, please click ', 'baidu-translator' ); ?><a target="_blank" href="http://api.fanyi.baidu.com"><?php _e('here.', 'baidu-translator'); ?></a></span>
        <p class="baidu-translator-togglable-field">
        <?php $key = $this->settings->get_setting('key');
        if ( $key === 'joybin' ) {
            $text = '';
        } else {
            $text = $key;
        }
        echo '<input type="text" name="baidu_translator[key]" id="baidu-translator-key" value="'.$text.'"></input>';
             ?>
		</p>
		<span class="description-footer"><?php _e( 'If you don\'t have this ID, please leave it blank to use the default Key (2,000,000 words/month limited for all the users without valid developer Key).', 'baidu-translator' ); ?></span>
		<?php
	}

	public function render_field_default_lang() {
		$default_lang = $this->settings->get_setting( 'default_lang' );
		?>
		<span class="description"><?php _e( 'Please choose the language you website is in.', 'baidu-translator' ); ?></span>
		<p class="baidu-translator-togglable-field">
			<select name="baidu_translator[default_lang]" id="baidu-translator-default-lang">
            <?php $support_langs = $this->settings->get_setting('support_lang');
        foreach ($support_langs as $lang) {
                if ($lang === $default_lang) {
                    echo '<option value="'.$lang.'" selected>'.$this->settings->get_lang_map()[$lang]."</option>";
                } else {
                    echo '<option value="'.$lang.'">'.$this->settings->get_lang_map()[$lang]."</option>";
                }
        }
             ?>
			</select><br>
		</p>
		<?php
    }

    public function render_field_translate_range() {
		?>
		<span class="description"><?php _e( 'Please choose the part you want to translate.', 'baidu-translator' ); ?></span>
        <p class="baidu-translator-togglable-field">
        <?php $translate_range = $this->settings->get_setting('translate_range');
        foreach ($this->settings->get_translate_parts() as $part) {
                if ($part === $translate_range) {
                    echo '<input type="radio" name="baidu_translator[translate_range]" value="'.$part.'" checked>'.$part.'</input></br>';
                } else {
                    echo '<input type="radio" name="baidu_translator[translate_range]" value="'.$part.'">'.$part.'</input></br>';
                }
        }
             ?>
		</p>
		<?php
    }

    public function render_field_support_lang() {
		?>
		<span class="description"><?php _e( 'Please choose the languages you want to support.', 'baidu-translator' ); ?></span>
        <p class="baidu-translator-togglable-field">
        <?php $support_lang = $this->settings->get_setting('support_lang');
        foreach ($this->settings->get_lang_list() as $lang) {
                if ( in_array( $lang, $support_lang ) ) {
                    echo '<input type="checkbox" name="baidu_translator[support_lang][]" value="'.$lang.'" checked>'.$this->settings->get_lang_map()[$lang].'</input></br>';
                } else {
                    echo '<input type="checkbox" name="baidu_translator[support_lang][]" value="'.$lang.'">'.$this->settings->get_lang_map()[$lang].'</input></br>';
                }
        }
             ?>
		</p>
		<?php
	}

    public function render_field_auto_lang() {
		?>
		<span class="description"><?php _e( 'If enable this option, the plugin will detect the source language automatically.', 'baidu-translator' ); ?></span>
        <p class="baidu-translator-togglable-field">
        <?php $auto_lang = $this->settings->get_setting('auto_lang');
        $instruction = __('Detect the language automatically.', 'baidu-translator');
        if ( $auto_lang ) {
                    echo '<input type="checkbox" name="baidu_translator[auto_lang]" value="auto" checked>'.$instruction.'</input></br>';
         } else {
                    echo '<input type="checkbox" name="baidu_translator[auto_lang]" value="manual">'.$instruction.'</input></br>';
        }
             ?>
		</p>
		<?php
	}

    public function render_field_bypassid_list() {
		?>
		<span class="description"><?php _e( 'The IDs of the elements listed here will not be translated. (separated by ;)', 'baidu-translator' ); ?></span>
        <p class="baidu-translator-togglable-field">
        <?php $bypassid_list = $this->settings->get_setting('bypassid_list');
        $text = '';
        foreach ( $bypassid_list as $item) {
            $text .= $item.';';          
        }
        echo '<textarea name="baidu_translator[bypassid_list]" id="baidu-translator-bypassid-list" value="'.$text.'">'.$text.'</textarea>';
             ?>
		</p>
		<span class="description-footer"><?php _e( 'For example: if you don\'t want to translate the plugin itself, please input baidu_translate_drop_list; here.', 'baidu-translator' ); ?></span>
		<?php
	}
}
