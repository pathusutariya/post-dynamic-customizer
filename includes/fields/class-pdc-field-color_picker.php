<?php

if( ! class_exists('pdc_field_color_picker') ) :

class pdc_field_color_picker extends pdc_field {
	
	
	/*
	*  __construct
	*
	*  This function will setup the field type data
	*
	*  @type	function
	*  @date	5/03/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function initialize() {
		
		// vars
		$this->name = 'color_picker';
		$this->label = __("Color Picker",'pdc');
		$this->category = 'jquery';
		$this->defaults = array(
			'default_value'	=> '',
		);
		
	}
	
	
	/*
	*  input_admin_enqueue_scripts
	*
	*  description
	*
	*  @type	function
	*  @date	16/12/2015
	*  @since	5.3.2
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function input_admin_enqueue_scripts() {
		
		// globals
		global $wp_scripts;
		
		
		// register if not already (on front end)
		// http://wordpress.stackexchange.com/questions/82718/how-do-i-implement-the-wordpress-iris-picker-into-my-plugin-on-the-front-end
		if( !isset($wp_scripts->registered['iris']) ) {
			
			// styles
			wp_register_style('wp-color-picker', admin_url('css/color-picker.css'), array(), '', true);
			
			
			// scripts
			wp_register_script('iris', admin_url('js/iris.min.js'), array('jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch'), '1.0.7', true);
			wp_register_script('wp-color-picker', admin_url('js/color-picker.min.js'), array('iris'), '', true);
			
			
			// localize
		    wp_localize_script('wp-color-picker', 'wpColorPickerL10n', array(
		        'clear'			=> __('Clear', 'pdc' ),
		        'defaultString'	=> __('Default', 'pdc' ),
		        'pick'			=> __('Select Color', 'pdc' ),
		        'current'		=> __('Current Color', 'pdc' )
		    )); 
			
		}
		
		
		// enqueue
		wp_enqueue_style('wp-color-picker');
	    wp_enqueue_script('wp-color-picker');
	    
	    			
	}
	
	
	/*
	*  render_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field - an array holding all the field's data
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/
	
	function render_field( $field ) {
		
		// vars
		$text_input = pdc_get_sub_array( $field, array('id', 'class', 'name', 'value') );
		$hidden_input = pdc_get_sub_array( $field, array('name', 'value') );
		
		
		// html
		?>
		<div class="pdc-color-picker">
			<?php pdc_hidden_input( $hidden_input ); ?>
			<?php pdc_text_input( $text_input ); ?>
		</div>
		<?php
	}
	
	
	/*
	*  render_field_settings()
	*
	*  Create extra options for your field. This is rendered when editing a field.
	*  The value of $field['name'] can be used (like bellow) to save extra data to the $field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field	- an array holding all the field's data
	*/
	
	function render_field_settings( $field ) {
		
		// display_format
		pdc_render_field_setting( $field, array(
			'label'			=> __('Default Value','pdc'),
			'instructions'	=> '',
			'type'			=> 'text',
			'name'			=> 'default_value',
			'placeholder'	=> '#FFFFFF'
		));
		
	}
	
}


// initialize
pdc_register_field_type( 'pdc_field_color_picker' );

endif; // class_exists check

?>