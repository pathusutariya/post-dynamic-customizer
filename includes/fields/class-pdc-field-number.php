<?php

if( ! class_exists('pdc_field_number') ) :

class pdc_field_number extends pdc_field {
	
	
	/*
	*  initialize
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
		$this->name = 'number';
		$this->label = __("Number",'pdc');
		$this->defaults = array(
			'default_value'	=> '',
			'min'			=> '',
			'max'			=> '',
			'step'			=> '',
			'placeholder'	=> '',
			'prepend'		=> '',
			'append'		=> ''
		);
		
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
		$atts = array();
		$keys = array( 'type', 'id', 'class', 'name', 'value', 'min', 'max', 'step', 'placeholder', 'pattern' );
		$keys2 = array( 'readonly', 'disabled', 'required' );
		$html = '';
		
		
		// step
		if( !$field['step'] ) {
			$field['step'] = 'any';
		}
		
		
		// prepend
		if( $field['prepend'] !== '' ) {
		
			$field['class'] .= ' pdc-is-prepended';
			$html .= '<div class="pdc-input-prepend">' . pdc_esc_html($field['prepend']) . '</div>';
			
		}
		
		
		// append
		if( $field['append'] !== '' ) {
		
			$field['class'] .= ' pdc-is-appended';
			$html .= '<div class="pdc-input-append">' . pdc_esc_html($field['append']) . '</div>';
			
		}
		
		
		// atts (value="123")
		foreach( $keys as $k ) {
			if( isset($field[ $k ]) ) $atts[ $k ] = $field[ $k ];
		}
		
		
		// atts2 (disabled="disabled")
		foreach( $keys2 as $k ) {
			if( !empty($field[ $k ]) ) $atts[ $k ] = $k;
		}
		
		
		// remove empty atts
		$atts = pdc_clean_atts( $atts );
		
		
		// render
		$html .= '<div class="pdc-input-wrap">' . pdc_get_text_input( $atts ) . '</div>';
		
		
		// return
		echo $html;
		
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
		
		// default_value
		pdc_render_field_setting( $field, array(
			'label'			=> __('Default Value','pdc'),
			'instructions'	=> __('Appears when creating a new post','pdc'),
			'type'			=> 'text',
			'name'			=> 'default_value',
		));
		
		
		// placeholder
		pdc_render_field_setting( $field, array(
			'label'			=> __('Placeholder Text','pdc'),
			'instructions'	=> __('Appears within the input','pdc'),
			'type'			=> 'text',
			'name'			=> 'placeholder',
		));
		
		
		// prepend
		pdc_render_field_setting( $field, array(
			'label'			=> __('Prepend','pdc'),
			'instructions'	=> __('Appears before the input','pdc'),
			'type'			=> 'text',
			'name'			=> 'prepend',
		));
		
		
		// append
		pdc_render_field_setting( $field, array(
			'label'			=> __('Append','pdc'),
			'instructions'	=> __('Appears after the input','pdc'),
			'type'			=> 'text',
			'name'			=> 'append',
		));
		
		
		// min
		pdc_render_field_setting( $field, array(
			'label'			=> __('Minimum Value','pdc'),
			'instructions'	=> '',
			'type'			=> 'number',
			'name'			=> 'min',
		));
		
		
		// max
		pdc_render_field_setting( $field, array(
			'label'			=> __('Maximum Value','pdc'),
			'instructions'	=> '',
			'type'			=> 'number',
			'name'			=> 'max',
		));
		
		
		// max
		pdc_render_field_setting( $field, array(
			'label'			=> __('Step Size','pdc'),
			'instructions'	=> '',
			'type'			=> 'number',
			'name'			=> 'step',
		));
		
	}
	
	
	/*
	*  validate_value
	*
	*  description
	*
	*  @type	function
	*  @date	11/02/2014
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function validate_value( $valid, $value, $field, $input ){
		
		// remove ','
		if( pdc_str_exists(',', $value) ) {
			
			$value = str_replace(',', '', $value);
			
		}
				
		
		// if value is not numeric...
		if( !is_numeric($value) ) {
			
			// allow blank to be saved
			if( !empty($value) ) {
				
				$valid = __('Value must be a number', 'pdc');
				
			}
			
			
			// return early
			return $valid;
			
		}
		
		
		// convert
		$value = floatval($value);
		
		
		// min
		if( is_numeric($field['min']) && $value < floatval($field['min'])) {
			
			$valid = sprintf(__('Value must be equal to or higher than %d', 'pdc'), $field['min'] );
			
		}
		
		
		// max
		if( is_numeric($field['max']) && $value > floatval($field['max']) ) {
			
			$valid = sprintf(__('Value must be equal to or lower than %d', 'pdc'), $field['max'] );
			
		}
		
		
		// return		
		return $valid;
		
	}
	
	
	/*
	*  update_value()
	*
	*  This filter is appied to the $value before it is updated in the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value - the value which will be saved in the database
	*  @param	$field - the field array holding all the field options
	*  @param	$post_id - the $post_id of which the value will be saved
	*
	*  @return	$value - the modified value
	*/
	
	function update_value( $value, $post_id, $field ) {
		
		// no formatting needed for empty value
		if( empty($value) ) {
			
			return $value;
			
		}
		
		
		// remove ','
		if( pdc_str_exists(',', $value) ) {
			
			$value = str_replace(',', '', $value);
			
		}
		
		
		// return
		return $value;
		
	}
	
	
}


// initialize
pdc_register_field_type( 'pdc_field_number' );

endif; // class_exists check

?>