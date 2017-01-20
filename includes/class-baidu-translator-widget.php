<?php

class BaiDu_Translator_Widget extends WP_Widget {
	public function __construct( $id_base = false, $name = false, $widget_options = array(), $control_options = array() ) {
		$id_base = ( $id_base ) ? $id_base : 'baidu-translator';
		$name = ( $name ) ? $name : __( 'BD Translator', 'baidu-translator' );

		$widget_options = wp_parse_args( $widget_options, array(
			'classname'   => 'widget_baidu_translator',
			'description' => __( 'Embed Translator with Baidu Service widget.', 'baidu-translator' ),
		) );

		$control_options = wp_parse_args( $control_options, array() );

		parent::__construct( $id_base, $name, $widget_options, $control_options );
	}

	public function widget( $args, $instance ) {
		extract( $args );

		$instance['title'] = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

		echo $before_widget;

			echo empty( $instance['title'] ) ? '' : $before_title . $instance['title'] . $after_title;

			if ( ! $output = apply_filters( 'baidu_translator_widget_output', '', $instance, $args ) ) {
                baidu_translator( $instance );
			}

		echo $after_widget;
	}

	public function update( $new_instance, $old_instance ) {
		$new_instance['title']      = wp_strip_all_tags( $new_instance['title'] );

		$settings = new BaiDu_Translator_Settings();
		$instance = $settings->merge_settings( $new_instance );

		return $instance;
	}

	public function form( $instance ) {
		$instance['title'] = isset( $instance['title'] ) ? $instance['title'] : '';

		// Merge sanitized settings into the instance settings.
		$settings = new BaiDu_Translator_Settings();
		$instance = $settings->merge_settings( $instance );

		$title = wp_strip_all_tags( $instance['title'] );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'baidu-translator' ); ?></label>
			<input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>" id="<?php echo $this->get_field_id( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>" class="widefat">
		</p>

		<h4><?php _e( 'Widget Settings', 'baidu-translator' ); ?></h4>
        <p>
            <?php _e( 'Please config this plugin from Settings->BaiDu Translator.', 'baidu-translator')?>
        </p>
				<?php
	}
}
