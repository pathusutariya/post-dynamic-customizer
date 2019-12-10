<?php 

/*
*  pdc_esc_html
*
*  This function will encode <script> tags for safe output
*
*  @type	function
*  @date	25/6/17
*  @since	5.6.0
*
*  @param	string (string)
*  @return	(string)
*/

function pdc_esc_html( $string = '' ) {
	
	// cast
	$string = (string) $string;
	
	
	// replace
	$string = str_replace('<script', htmlspecialchars('<script'), $string);
	$string = str_replace('</script', htmlspecialchars('</script'), $string);
	
	
	// return
	return $string;
	
}


/**
*  pdc_clean_atts
*
*  This function will remove empty attributes
*
*  @date	3/10/17
*  @since	5.6.3
*
*  @param	array $atts
*  @return	array
*/

function pdc_clean_atts( $atts = array() ) {
	
	// loop
	foreach( $atts as $k => $v ) {
		if( $v === '' ) unset( $atts[ $k ] );
	}
	
	
	// return
	return $atts;
}


/**
*  pdc_get_atts
*
*  This function will return an array of HTML attributes
*
*  @date	2/10/17
*  @since	5.6.3
*
*  @param	n/a
*  @return	n/a
*/

/*
function pdc_get_atts( $array, $keys ) {
	
	// vars
	$atts = array();
	
	
	// append attributes
	foreach( $keys as $k ) {
		if( isset($array[ $k ]) ) $atts[ $k ] = $array[ $k ];
	}
	
	
	// modify special attributes
	foreach( array('readonly', 'disabled', 'required') as $k ) {
		$atts[ $k ] = $atts[ $k ] ? $k : '';
	}
	
	
	// clean up blank attributes
	foreach( $atts as $k => $v ) {
		if( $v === '' ) unset( $atts[ $k ] );
	}
	
	
	// return
	return $atts;
	
}
*/


/*
*  pdc_esc_atts
*
*  This function will escape an array of attributes and return as HTML
*
*  @type	function
*  @date	27/6/17
*  @since	5.6.0
*
*  @param	$atts (array)
*  @return	(string)
*/

function pdc_esc_atts( $atts = array() ) {
	
	// vars
	$html = '';
	
	
	// loop
	foreach( $atts as $k => $v ) {
		
		// string
		if( is_string($v) ) {
			
			// don't trim value
			if( $k !== 'value') $v = trim($v);
			
		// boolean	
		} elseif( is_bool($v) ) {
			
			$v = $v ? 1 : 0;
			
		// object
		} elseif( is_array($v) || is_object($v) ) {
			
			$v = json_encode($v);
			
		}
		
		
		// append
		$html .= esc_attr( $k ) . '="' . esc_attr( $v ) . '" ';
		
	}
	
	
	// return
	return trim( $html );
	
}


/*
*  pdc_esc_atts_e
*
*  This function will echo pdc_esc_atts
*
*  @type	function
*  @date	27/6/17
*  @since	5.6.0
*
*  @param	$atts (array)
*  @return	n/a
*/

function pdc_esc_atts_e( $atts = array() ) {
	
	echo pdc_esc_atts( $atts );
	
}


/*
*  pdc_get_text_input
*
*  This function will return HTML for a text input
*
*  @type	function
*  @date	3/02/2014
*  @since	5.0.0
*
*  @param	$atts
*  @return	(string)
*/

function pdc_get_text_input( $atts = array() ) {
	
	$atts['type'] = isset($atts['type']) ? $atts['type'] : 'text';
	return '<input ' . pdc_esc_atts( $atts ) . ' />';
	
}


/*
*  pdc_text_input
*
*  This function will output HTML for a text input
*
*  @type	function
*  @date	3/02/2014
*  @since	5.0.0
*
*  @param	$atts
*  @return	n/a
*/

function pdc_text_input( $atts = array() ) {
	
	echo pdc_get_text_input( $atts );
	
}


/*
*  pdc_get_hidden_input
*
*  This function will return HTML for a hidden input
*
*  @type	function
*  @date	3/02/2014
*  @since	5.0.0
*
*  @param	$atts
*  @return	(string)
*/

function pdc_get_hidden_input( $atts = array() ) {
	
	$atts['type'] = 'hidden';
	return pdc_get_text_input( $atts );
	
}


/*
*  pdc_hidden_input
*
*  This function will output HTML for a generic input
*
*  @type	function
*  @date	3/02/2014
*  @since	5.0.0
*
*  @param	$atts
*  @return	n/a
*/

function pdc_hidden_input( $atts = array() ) {
	
	echo pdc_get_hidden_input( $atts ) . "\n";
	
}


/*
*  pdc_get_textarea_input
*
*  This function will return HTML for a textarea input
*
*  @type	function
*  @date	3/02/2014
*  @since	5.0.0
*
*  @param	$atts
*  @return	(string)
*/

function pdc_get_textarea_input( $atts = array() ) {
	
	$value = pdc_extract_var( $atts, 'value', '' );
	return '<textarea ' . pdc_esc_atts( $atts ) . '>' . esc_textarea( $value ) . '</textarea>';
		
}


/*
*  pdc_textarea_input
*
*  This function will output HTML for a textarea input
*
*  @type	function
*  @date	3/02/2014
*  @since	5.0.0
*
*  @param	$atts
*  @return	n/a
*/

function pdc_textarea_input( $atts = array() ) {
	
	echo pdc_get_textarea_input( $atts );
	
}


/*
*  pdc_get_checkbox_input
*
*  This function will return HTML for a checkbox input
*
*  @type	function
*  @date	3/02/2014
*  @since	5.0.0
*
*  @param	$atts
*  @return	(string)
*/

function pdc_get_checkbox_input( $atts = array() ) {
	
	$label = pdc_extract_var( $atts, 'label', '' );
	$checked = pdc_maybe_get( $atts, 'checked', '' );
	$atts['type'] = pdc_maybe_get( $atts, 'type', 'checkbox' );
	return '<label' . ($checked ? ' class="selected"' : '') . '><input ' . pdc_esc_attr( $atts ) . '/>' . pdc_esc_html( $label ) . '</label>';
		
}


/*
*  pdc_checkbox_input
*
*  This function will output HTML for a checkbox input
*
*  @type	function
*  @date	3/02/2014
*  @since	5.0.0
*
*  @param	$atts
*  @return	n/a
*/

function pdc_checkbox_input( $atts = array() ) {
	
	echo pdc_get_checkbox_input( $atts );
	
}


/*
*  pdc_get_radio_input
*
*  This function will return HTML for a radio input
*
*  @type	function
*  @date	3/02/2014
*  @since	5.0.0
*
*  @param	$atts
*  @return	(string)
*/

function pdc_get_radio_input( $atts = array() ) {
	
	$atts['type'] = 'radio';
	return pdc_get_checkbox_input( $atts );
		
}


/*
*  pdc_radio_input
*
*  This function will output HTML for a radio input
*
*  @type	function
*  @date	3/02/2014
*  @since	5.0.0
*
*  @param	$atts
*  @return	n/a
*/

function pdc_radio_input( $atts = array() ) {
	
	echo pdc_get_radio_input( $atts );
	
}


/*
*  pdc_get_select_input
*
*  This function will return HTML for a select input
*
*  @type	function
*  @date	3/02/2014
*  @since	5.0.0
*
*  @param	$atts
*  @return	(string)
*/

function pdc_get_select_input( $atts = array() ) {
	
	// vars
	$value = (array) pdc_extract_var( $atts, 'value' );
	$choices = (array) pdc_extract_var( $atts, 'choices' );
	
	
	// html
	$html = '';
	$html .= '<select ' . pdc_esc_atts( $atts ) . '>';
	$html .= pdc_walk_select_input( $choices, $value );
	$html .= '</select>' . "\n";
	
	
	// return
	return $html;
		
}


/*
*  pdc_walk_select_input
*
*  This function will return the HTML for a select input's choices
*
*  @type	function
*  @date	27/6/17
*  @since	5.6.0
*
*  @param	$post_id (int)
*  @return	$post_id (int)
*/

function pdc_walk_select_input( $choices = array(), $values = array(), $depth = 0 ) {
	
	// bail ealry if no choices
	if( empty($choices) ) return '';
	
	
	// vars
	$html = '';
	
	
	// sanitize values for 'selected' matching
	if( $depth == 0 ) {
		$values = array_map('esc_attr', $values);
	}
	
	
	// loop
	foreach( $choices as $value => $label ) {
		
		// optgroup
		if( is_array($label) ){
			
			$html .= '<optgroup label="' . esc_attr($value) . '">';
			$html .= pdc_walk_select_input( $label, $values, $depth+1 );
			$html .= '</optgroup>';
		
		// option	
		} else {
			
			// vars
			$atts = array( 'value' => $value );
			$pos = array_search( esc_attr($value), $values );
		
		
			// selected
			if( $pos !== false ) {
				$atts['selected'] = 'selected';
				$atts['data-i'] = $pos;
			}
			
			
			// append
			$html .= '<option ' . pdc_esc_attr($atts) . '>' . esc_html($label) . '</option>';
			
		}
		
	}
	
	
	// return
	return $html;
	
}


/*
*  pdc_select_input
*
*  This function will output HTML for a select input
*
*  @type	function
*  @date	3/02/2014
*  @since	5.0.0
*
*  @param	$atts
*  @return	n/a
*/

function pdc_select_input( $atts = array() ) {
	
	echo pdc_get_select_input( $atts );
	
}



/*
function pdc_test_esc_html( $string = '' ) {
	
	$s = '';
	
	
	$time_start = microtime(true);
	$s .= wp_kses_post( $string );
	$s .= ' = ('. (microtime(true) - $time_start) .')';
	
	$s .= '-----';

	
	$time_start = microtime(true);
	$s .= str_replace(array('<script', '</script'), array(htmlspecialchars('<script'), htmlspecialchars('</script')), $string);
	$s .= ' = ('. (microtime(true) - $time_start) .')';
	

	$time_start = microtime(true);
	if( strpos($string, '<script') ) {
		$s .= str_replace(array('<script', '</script'), array(htmlspecialchars('<script'), htmlspecialchars('</script')), $string);
	}
	$s .= ' = ('. (microtime(true) - $time_start) .')';
	
	return $s;
	
}
*/


/*
*  pdc_get_file_input
*
*  This function will return HTML for a file input
*
*  @type	function
*  @date	3/02/2014
*  @since	5.0.0
*
*  @param	$atts
*  @return	(string)
*/

function pdc_get_file_input( $atts = array() ) {
	
	$atts['type'] = 'file';
	return pdc_get_text_input( $atts );
	
}


/*
*  pdc_file_input
*
*  This function will output HTML for a file input
*
*  @type	function
*  @date	3/02/2014
*  @since	5.0.0
*
*  @param	$atts
*  @return	n/a
*/

function pdc_file_input( $atts = array() ) {
	
	echo pdc_get_file_input( $atts );
	
}


/*
*  pdc_esc_attr
*
*  Deprecated since 5.6.0
*
*  @type	function
*  @date	1/10/13
*  @since	5.0.0
*
*  @param	$atts (array)
*  @return	n/a
*/

function pdc_esc_attr( $atts ) {
	
	return pdc_esc_atts( $atts );
	
}


/*
*  pdc_esc_attr_e
*
*  Deprecated since 5.6.0
*
*  @type	function
*  @date	1/10/13
*  @since	5.0.0
*
*  @param	$atts (array)
*  @return	n/a
*/

function pdc_esc_attr_e( $atts ) {
	
	pdc_esc_atts_e( $atts );
	
}


 ?>