<?php

/*
*  ACF Text Field Class
*
*  All the logic for this field type
*
*  @class 		pdc_field_text
*  @extends		pdc_field
*  @package		ACF
*  @subpackage	Fields
*/

if( ! class_exists('pdc_field_text') ) :

class pdc_field_text extends pdc_field {
	
	
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
	
	function __construct() {
		
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
		
		
		// do not delete!
    	parent::__construct();
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
		$o = array( 'type', 'id', 'class', 'name', 'value', 'placeholder' );
		$s = array( 'readonly', 'disabled' );
		$e = '';
		
		
		// maxlength
		if( $field['maxlength'] ) {
		
			$o[] = 'maxlength';
			
		}
		
		
		// prepend
		if( $field['prepend'] !== '' ) {
		
			$field['class'] .= ' pdc-is-prepended';
			$e .= '<div class="pdc-input-prepend">' . $field['prepend'] . '</div>';
			
		}
		
		
		// append
		if( $field['append'] !== '' ) {
		
			$field['class'] .= ' pdc-is-appended';
			$e .= '<div class="pdc-input-append">' . $field['append'] . '</div>';
			
		}
		
		
		// append atts
		foreach( $o as $k ) {
		
			$atts[ $k ] = $field[ $k ];	
			
		}
		
		
		// append special atts
		foreach( $s as $k ) {
		
			if( !empty($field[ $k ]) ) $atts[ $k ] = $k;
			
		}
		
		
		// render
		$e .= '<div class="pdc-input-wrap">';
		$e .= '<input ' . pdc_esc_attr( $atts ) . ' />';
		$e .= '</div>';
		
		
		// return
		echo $e;
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
pdc_register_field_type( new pdc_field_text() );

endif; // class_exists check

?>