<?php

if( ! class_exists('pdc_field_text') ) :

class pdc_field_text extends pdc_field {
	
	
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
		$this->name = 'text';
		$this->label = __("Text",'pdc');
		$this->defaults = array(
			'default_value'	=> '',
			'maxlength'		=> '',
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
		$keys = array( 'type', 'id', 'class', 'name', 'value', 'placeholder', 'maxlength', 'pattern' );
		$keys2 = array( 'readonly', 'disabled', 'required' );
		$html = '';
		
		
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
	*  @param	$field	- an array holding all the field's data
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
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
		
		
		// maxlength
		pdc_render_field_setting( $field, array(
			'label'			=> __('Character Limit','pdc'),
			'instructions'	=> __('Leave blank for no limit','pdc'),
			'type'			=> 'number',
			'name'			=> 'maxlength',
		));
		
	}
	
}


// initialize
pdc_register_field_type( 'pdc_field_text' );

endif; // class_exists check

?>