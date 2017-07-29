<?php

/*
*  ACF Checkbox Field Class
*
*  All the logic for this field type
*
*  @class 		pdc_field_checkbox
*  @extends		pdc_field
*  @package		ACF
*  @subpackage	Fields
*/

if( ! class_exists('pdc_field_checkbox') ) :

class pdc_field_checkbox extends pdc_field {
	
	
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
		$this->name = 'checkbox';
		$this->label = __("Checkbox",'pdc');
		$this->category = 'choice';
		$this->defaults = array(
			'layout'			=> 'vertical',
			'choices'			=> array(),
			'default_value'		=> '',
			'allow_custom'		=> 0,
			'save_custom'		=> 0,
			'toggle'			=> 0,
			'return_format'		=> 'value'
		);
		
		
		// do not delete!
    	parent::__construct();
	}
		
	
	/*
	*  render_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field (array) the $field being rendered
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field (array) the $field being edited
	*  @return	n/a
	*/
	
	function render_field( $field ) {
		
		// ensure array
		$field['value'] = pdc_get_array($field['value'], false);
		$field['choices'] = pdc_get_array($field['choices']);
		
		
		// hiden input
		pdc_hidden_input( array('name' => $field['name']) );
		
		
		// vars
		$i = 0;
		$li = '';
		$all_checked = true;
		
		
		// checkbox saves an array
		$field['name'] .= '[]';
		
		
		// foreach choices
		if( !empty($field['choices']) ) {
			
			foreach( $field['choices'] as $value => $label ) {
				
				// increase counter
				$i++;
				
				
				// vars
				$atts = array(
					'type'	=> 'checkbox',
					'id'	=> $field['id'], 
					'name'	=> $field['name'],
					'value'	=> $value,
				);
				
				
				// is choice selected?
				if( in_array($value, $field['value']) ) {
					
					$atts['checked'] = 'checked';
					
				} else {
					
					$all_checked = false;
					
				}
				
				
				if( isset($field['disabled']) && pdc_in_array($value, $field['disabled']) ) {
				
					$atts['disabled'] = 'disabled';
					
				}
				
				
				// each input ID is generated with the $key, however, the first input must not use $key so that it matches the field's label for attribute
				if( $i > 1 ) {
				
					$atts['id'] .= '-' . $value;
					
				}
				
				
				// append HTML
				$li .= '<li><label><input ' . pdc_esc_attr( $atts ) . '/>' . $label . '</label></li>';
				
			}
			
			
			// toggle all
			if( $field['toggle'] ) {
				
				// vars
				$label = __("Toggle All", 'pdc');
				$atts = array(
					'type'	=> 'checkbox',
					'class'	=> 'pdc-checkbox-toggle'
				);
				
				
				// custom label
				if( is_string($field['toggle']) ) {
					
					$label = $field['toggle'];
					
				}
				
				
				// checked
				if( $all_checked ) {
					
					$atts['checked'] = 'checked';
					
				}
				
				
				// append HTML
				$li = '<li><label><input ' . pdc_esc_attr( $atts ) . '/>' . $label . '</label></li>' . $li;
					
			}
		
		}
		
		
		// allow_custom
		if( $field['allow_custom'] ) {
			
			
			// loop
			foreach( $field['value'] as $value ) {
				
				// ignore if already eixsts
				if( isset($field['choices'][ $value ]) ) continue;
				
				
				// vars
				$atts = array(
					'type'	=> 'text',
					'name'	=> $field['name'],
					'value'	=> $value,
				);
				
				
				// append
				$li .= '<li><input class="pdc-checkbox-custom" type="checkbox" checked="checked" /><input ' . pdc_esc_attr( $atts ) . '/></li>';
				
			}
			
			
			// append button
			$li .= '<li><a href="#" class="button pdc-add-checkbox">' . __('Add new choice', 'pdc') . '</a></li>';
			
		}
		
		
		// class
		$field['class'] .= ' pdc-checkbox-list';
		$field['class'] .= ($field['layout'] == 'horizontal') ? ' pdc-hl' : ' pdc-bl';

		
		// return
		echo '<ul ' . pdc_esc_attr(array( 'class' => $field['class'] )) . '>' . $li . '</ul>';
		
		
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
		
		// encode choices (convert from array)
		$field['choices'] = pdc_encode_choices($field['choices']);
		$field['default_value'] = pdc_encode_choices($field['default_value'], false);
				
		
		// choices
		pdc_render_field_setting( $field, array(
			'label'			=> __('Choices','pdc'),
			'instructions'	=> __('Enter each choice on a new line.','pdc') . '<br /><br />' . __('For more control, you may specify both a value and label like this:','pdc'). '<br /><br />' . __('red : Red','pdc'),
			'type'			=> 'textarea',
			'name'			=> 'choices',
		));	
		
		
		// other_choice
		pdc_render_field_setting( $field, array(
			'label'			=> __('Allow Custom','pdc'),
			'instructions'	=> '',
			'name'			=> 'allow_custom',
			'type'			=> 'true_false',
			'ui'			=> 1,
			'message'		=> __("Allow 'custom' values to be added", 'pdc'),
		));
		
		
		// save_other_choice
		pdc_render_field_setting( $field, array(
			'label'			=> __('Save Custom','pdc'),
			'instructions'	=> '',
			'name'			=> 'save_custom',
			'type'			=> 'true_false',
			'ui'			=> 1,
			'message'		=> __("Save 'custom' values to the field's choices", 'pdc')
		));
		
		
		// default_value
		pdc_render_field_setting( $field, array(
			'label'			=> __('Default Value','pdc'),
			'instructions'	=> __('Enter each default value on a new line','pdc'),
			'type'			=> 'textarea',
			'name'			=> 'default_value',
		));
		
		
		// layout
		pdc_render_field_setting( $field, array(
			'label'			=> __('Layout','pdc'),
			'instructions'	=> '',
			'type'			=> 'radio',
			'name'			=> 'layout',
			'layout'		=> 'horizontal', 
			'choices'		=> array(
				'vertical'		=> __("Vertical",'pdc'), 
				'horizontal'	=> __("Horizontal",'pdc')
			)
		));
		
		
		// layout
		pdc_render_field_setting( $field, array(
			'label'			=> __('Toggle','pdc'),
			'instructions'	=> __('Prepend an extra checkbox to toggle all choices','pdc'),
			'name'			=> 'toggle',
			'type'			=> 'true_false',
			'ui'			=> 1,
		));
		
		
		// return_format
		pdc_render_field_setting( $field, array(
			'label'			=> __('Return Value','pdc'),
			'instructions'	=> __('Specify the returned value on front end','pdc'),
			'type'			=> 'radio',
			'name'			=> 'return_format',
			'layout'		=> 'horizontal',
			'choices'		=> array(
				'value'			=> __('Value','pdc'),
				'label'			=> __('Label','pdc'),
				'array'			=> __('Both (Array)','pdc')
			)
		));		
		
	}
	
	
	/*
	*  update_field()
	*
	*  This filter is appied to the $field before it is saved to the database
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field - the field array holding all the field options
	*  @param	$post_id - the field group ID (post_type = pdc)
	*
	*  @return	$field - the modified field
	*/

	function update_field( $field ) {
		
		return pdc_get_field_type('select')->update_field( $field );
		
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
	*  @param	$post_id - the $post_id of which the value will be saved
	*  @param	$field - the field array holding all the field options
	*
	*  @return	$value - the modified value
	*/
	
	function update_value( $value, $post_id, $field ) {
		
		// bail early if is empty
		if( empty($value) ) return $value;
		
		
		// select -> update_value()
		$value = pdc_get_field_type('select')->update_value( $value, $post_id, $field );
		
		
		// save_other_choice
		if( $field['save_custom'] ) {
			
			// get raw $field (may have been changed via repeater field)
			// if field is local, it won't have an ID
			$selector = $field['ID'] ? $field['ID'] : $field['key'];
			$field = pdc_get_field( $selector, true );
			
			
			// bail early if no ID (JSON only)
			if( !$field['ID'] ) return $value;
			
			
			// loop
			foreach( $value as $v ) {
				
				// ignore if already eixsts
				if( isset($field['choices'][ $v ]) ) continue;
				
				
				// unslash (fixes serialize single quote issue)
				$v = wp_unslash($v);
				
				
				// append
				$field['choices'][ $v ] = $v;
				
			}
			
			
			// save
			pdc_update_field( $field );
			
		}		
		
		
		// return
		return $value;
		
	}
	
	
	/*
	*  translate_field
	*
	*  This function will translate field settings
	*
	*  @type	function
	*  @date	8/03/2016
	*  @since	5.3.2
	*
	*  @param	$field (array)
	*  @return	$field
	*/
	
	function translate_field( $field ) {
		
		return pdc_get_field_type('select')->translate_field( $field );
		
	}
	
	
	/*
	*  format_value()
	*
	*  This filter is appied to the $value after it is loaded from the db and before it is returned to the template
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value (mixed) the value which was loaded from the database
	*  @param	$post_id (mixed) the $post_id from which the value was loaded
	*  @param	$field (array) the field array holding all the field options
	*
	*  @return	$value (mixed) the modified value
	*/
	
	function format_value( $value, $post_id, $field ) {
		
		return pdc_get_field_type('select')->format_value( $value, $post_id, $field );
		
	}
	
}


// initialize
pdc_register_field_type( new pdc_field_checkbox() );

endif; // class_exists check

?>