<?php

if( ! class_exists('pdc_field_button_group') ) :

class pdc_field_button_group extends pdc_field {
	
	
	/**
	*  initialize()
	*
	*  This function will setup the field type data
	*
	*  @date	18/9/17
	*  @since	5.6.3
	*
	*  @param	n/a
	*  @return	n/a
	*/
	 
	function initialize() {
		
		// vars
		$this->name = 'button_group';
		$this->label = __("Button Group",'pdc');
		$this->category = 'choice';
		$this->defaults = array(
			'choices'			=> array(),
			'default_value'		=> '',
			'allow_null' 		=> 0,
			'return_format'		=> 'value',
			'layout'			=> 'horizontal',
		);
		
	}
	
	
	/**
	*  render_field()
	*
	*  Creates the field's input HTML
	*
	*  @date	18/9/17
	*  @since	5.6.3
	*
	*  @param	array $field The field settings array
	*  @return	n/a
	*/
	
	function render_field( $field ) {
		
		// vars
		$html = '';
		$selected = null;
		$buttons = array();
		$value = esc_attr( $field['value'] );
		
		
		// bail ealrly if no choices
		if( empty($field['choices']) ) return;
		
		
		// buttons
		foreach( $field['choices'] as $_value => $_label ) {
			
			// checked
			$checked = ( $value === esc_attr($_value) );
			if( $checked ) $selected = true;
			
			
			// append
			$buttons[] = array(
				'name'		=> $field['name'],
				'value'		=> $_value,
				'label'		=> $_label,
				'checked'	=> $checked
			);
							
		}
		
		
		// maybe select initial value
		if( !$field['allow_null'] && $selected === null ) {
			$buttons[0]['checked'] = true;
		}
		
		
		// div
		$div = array( 'class' => 'pdc-button-group' );
		
		if( $field['layout'] == 'vertical' )	{ $div['class'] .= ' -vertical'; }
		if( $field['class'] )					{ $div['class'] .= ' ' . $field['class']; }
		if( $field['allow_null'] )				{ $div['data-allow_null'] = 1; }
		
		
		// hdden input
		$html .= pdc_get_hidden_input( array('name' => $field['name']) );
			
			
		// open
		$html .= '<div ' . pdc_esc_attr($div) . '>';
			
			// loop
			foreach( $buttons as $button ) {
				
				// checked
				if( $button['checked'] ) {
					$button['checked'] = 'checked';
				} else {
					unset($button['checked']);
				}
				
				
				// append
				$html .= pdc_get_radio_input( $button );
				
			}
			
					
		// close
		$html .= '</div>';
		
		
		// return
		echo $html;
		
	}
	
	
	/**
	*  render_field_settings()
	*
	*  Creates the field's settings HTML
	*
	*  @date	18/9/17
	*  @since	5.6.3
	*
	*  @param	array $field The field settings array
	*  @return	n/a
	*/
	
	function render_field_settings( $field ) {
		
		// encode choices (convert from array)
		$field['choices'] = pdc_encode_choices($field['choices']);
		
		
		// choices
		pdc_render_field_setting( $field, array(
			'label'			=> __('Choices','pdc'),
			'instructions'	=> __('Enter each choice on a new line.','pdc') . '<br /><br />' . __('For more control, you may specify both a value and label like this:','pdc'). '<br /><br />' . __('red : Red','pdc'),
			'type'			=> 'textarea',
			'name'			=> 'choices',
		));
		
		
		// allow_null
		pdc_render_field_setting( $field, array(
			'label'			=> __('Allow Null?','pdc'),
			'instructions'	=> '',
			'name'			=> 'allow_null',
			'type'			=> 'true_false',
			'ui'			=> 1,
		));
		
		
		// default_value
		pdc_render_field_setting( $field, array(
			'label'			=> __('Default Value','pdc'),
			'instructions'	=> __('Appears when creating a new post','pdc'),
			'type'			=> 'text',
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
				'horizontal'	=> __("Horizontal",'pdc'),
				'vertical'		=> __("Vertical",'pdc'), 
			)
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
	*  @date	18/9/17
	*  @since	5.6.3
	*
	*  @param	array $field The field array holding all the field options
	*  @return	$field
	*/

	function update_field( $field ) {
		
		return pdc_get_field_type('radio')->update_field( $field );
	}
	
	
	/*
	*  load_value()
	*
	*  This filter is appied to the $value after it is loaded from the db
	*
	*  @date	18/9/17
	*  @since	5.6.3
	*
	*  @param	mixed	$value		The value found in the database
	*  @param	mixed	$post_id	The post ID from which the value was loaded from
	*  @param	array	$field		The field array holding all the field options
	*  @return	$value
	*/
	
	function load_value( $value, $post_id, $field ) {
		
		return pdc_get_field_type('radio')->load_value( $value, $post_id, $field );
		
	}
	
	
	/*
	*  translate_field
	*
	*  This function will translate field settings
	*
	*  @date	18/9/17
	*  @since	5.6.3
	*
	*  @param	array $field The field array holding all the field options
	*  @return	$field
	*/
	
	function translate_field( $field ) {
		
		return pdc_get_field_type('radio')->translate_field( $field );
		
	}
	
	
	/*
	*  format_value()
	*
	*  This filter is appied to the $value after it is loaded from the db and before it is returned to the template
	*
	*  @date	18/9/17
	*  @since	5.6.3
	*
	*  @param	mixed	$value		The value found in the database
	*  @param	mixed	$post_id	The post ID from which the value was loaded from
	*  @param	array	$field		The field array holding all the field options
	*  @return	$value
	*/
	
	function format_value( $value, $post_id, $field ) {
		
		return pdc_get_field_type('radio')->format_value( $value, $post_id, $field );
		
	}
	
}


// initialize
pdc_register_field_type( 'pdc_field_button_group' );

endif; // class_exists check

?>