<?php 

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('pdc_validation') ) :

class pdc_validation {
	
	
	/*
	*  __construct
	*
	*  This function will setup the class functionality
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
		$this->errors = array();
		
		
		// ajax
		add_action('wp_ajax_pdc/validate_save_post',			array($this, 'ajax_validate_save_post'));
		add_action('wp_ajax_nopriv_pdc/validate_save_post',		array($this, 'ajax_validate_save_post'));
		
		
		// filters
		add_filter('pdc/validate_save_post',					array($this, 'pdc_validate_save_post'), 5);
		
	}
	
	
	/*
	*  add_error
	*
	*  This function will add an error message for a field
	*
	*  @type	function
	*  @date	25/11/2013
	*  @since	5.0.0
	*
	*  @param	$input (string) name attribute of DOM elmenet
	*  @param	$message (string) error message
	*  @return	$post_id (int)
	*/
	
	function add_error( $input, $message ) {
		
		// add to array
		$this->errors[] = array(
			'input'		=> $input,
			'message'	=> $message
		);
		
	}
	
	
	/*
	*  get_error
	*
	*  This function will return an error for a given input
	*
	*  @type	function
	*  @date	5/03/2016
	*  @since	5.3.2
	*
	*  @param	$input (string) name attribute of DOM elmenet
	*  @return	(mixed)
	*/
	
	function get_error( $input ) {
		
		// bail early if no errors
		if( empty($this->errors) ) return false;
		
		
		// loop
		foreach( $this->errors as $error ) {
			
			if( $error['input'] === $input ) return $error;
			
		}
		
		
		// return
		return false;
				
	}
	
	
	/*
	*  get_errors
	*
	*  This function will return validation errors
	*
	*  @type	function
	*  @date	25/11/2013
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	(array|boolean)
	*/
	
	function get_errors() {
		
		// bail early if no errors
		if( empty($this->errors) ) return false;
		
		
		// return
		return $this->errors;
		
	}
	
	
	/*
	*  reset_errors
	*
	*  This function will remove all errors
	*
	*  @type	function
	*  @date	4/03/2016
	*  @since	5.3.2
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function reset_errors() {
		
		$this->errors = array();
		
	}
	
	
	/*
	*  ajax_validate_save_post
	*
	*  This function will validate the $_POST data via AJAX
	*
	*  @type	function
	*  @date	27/10/2014
	*  @since	5.0.9
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function ajax_validate_save_post() {
		
		// validate
		if( !pdc_verify_ajax() ) die();
		
		
		// vars
		$json = array(
			'valid'		=> 1,
			'errors'	=> 0
		);
		
		
		// success
		if( pdc_validate_save_post() ) {
			
			wp_send_json_success($json);
			
		}
		
		
		// update vars
		$json['valid'] = 0;
		$json['errors'] = pdc_get_validation_errors();
		
		
		// return
		wp_send_json_success($json);
		
	}
	
	
	/*
	*  validate_value
	*
	*  This function will validate a field's value
	*
	*  @type	function
	*  @date	27/10/2014
	*  @since	5.0.9
	*
	*  @param	$value (mixed)
	*  @param	$field (array)
	*  @param	$input (string) name attribute of DOM elmenet
	*  @return	(boolean)
	*/
	
	function validate_value( $value, $field, $input ) {
		
		// vars
		$valid = true;
		$message = sprintf( __( '%s value is required', 'pdc' ), $field['label'] );
		
		
		// valid
		if( $field['required'] ) {
			
			// valid is set to false if the value is empty, but allow 0 as a valid value
			if( empty($value) && !is_numeric($value) ) {
				
				$valid = false;
				
			}
			
		}
		
		
		// filter for 3rd party customization
		$valid = apply_filters( "pdc/validate_value", $valid, $value, $field, $input );
		$valid = apply_filters( "pdc/validate_value/type={$field['type']}", $valid, $value, $field, $input );
		$valid = apply_filters( "pdc/validate_value/name={$field['name']}", $valid, $value, $field, $input );
		$valid = apply_filters( "pdc/validate_value/key={$field['key']}", $valid, $value, $field, $input );
		
		
		// allow $valid to be a custom error message
		if( !empty($valid) && is_string($valid) ) {
			
			$message = $valid;
			$valid = false;
			
		}
		
		
		if( !$valid ) {
			
			pdc_add_validation_error( $input, $message );
			return false;
			
		}
		
		
		// return
		return true;
		
	}
	

	/*
	*  pdc_validate_save_post
	*
	*  This function will loop over $_POST data and validate
	*
	*  @type	action 'pdc/validate_save_post' 5
	*  @date	7/09/2016
	*  @since	5.4.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function pdc_validate_save_post() {
		
		// bail early if no $_POST
		if( empty($_POST['pdc']) ) return;
		
		
		// loop
		foreach( $_POST['pdc'] as $field_key => $value ) {
			
			// get field
			$field = pdc_get_field( $field_key );
			$input = 'pdc[' . $field_key . ']';
			
			
			// bail early if not found
			if( !$field ) continue;
			
			
			// validate
			pdc_validate_value( $value, $field, $input );
			
		}
		
	}
	
	
	/*
	*  validate_save_post
	*
	*  This function will validate $_POST data and add errors
	*
	*  @type	function
	*  @date	25/11/2013
	*  @since	5.0.0
	*
	*  @param	$show_errors (boolean) if true, errors will be shown via a wp_die screen
	*  @return	(boolean)
	*/
	
	function validate_save_post( $show_errors = false ) {
		
		// action
		do_action('pdc/validate_save_post');
		
		
		// vars
		$errors = pdc_get_validation_errors();
		
		
		// bail ealry if no errors
		if( !$errors ) return true;
		
		
		// show errors
		if( $show_errors ) {
				
			$message = '<h2>' . __('Validation failed', 'pdc') . '</h2>';
			$message .= '<ul>';
			foreach( $errors as $error ) {
				
				$message .= '<li>' . $error['message'] . '</li>';
				
			}
			$message .= '</ul>';
			
			
			// die
			wp_die( $message, 'Validation failed' );
			
		}
		
		
		// return
		return false;
		
	}
	
}

// initialize
pdc()->validation = new pdc_validation();

endif; // class_exists check




/*
*  pdc_add_validation_error
*
*  alias of pdc()->validation->add_error()
*
*  @type	function
*  @date	6/10/13
*  @since	5.0.0
*
*  @param	n/a
*  @return	n/a
*/

function pdc_add_validation_error( $input, $message = '' ) {
	
	return pdc()->validation->add_error( $input, $message );
	
}


/*
*  pdc_get_validation_errors
*
*  alias of pdc()->validation->get_errors()
*
*  @type	function
*  @date	6/10/13
*  @since	5.0.0
*
*  @param	n/a
*  @return	n/a
*/

function pdc_get_validation_errors() {
	
	return pdc()->validation->get_errors();
	
}


/*
*  pdc_reset_validation_errors
*
*  alias of pdc()->validation->reset_errors()
*
*  @type	function
*  @date	6/10/13
*  @since	5.0.0
*
*  @param	n/a
*  @return	n/a
*/

function pdc_reset_validation_errors() {
	
	return pdc()->validation->reset_errors();
	
}


/*
*  pdc_validate_save_post
*
*  alias of pdc()->validation->validate_save_post()
*
*  @type	function
*  @date	6/10/13
*  @since	5.0.0
*
*  @param	n/a
*  @return	n/a
*/

function pdc_validate_save_post( $show_errors = false ) {
	
	return pdc()->validation->validate_save_post( $show_errors );
	
}


/*
*  pdc_validate_value
*
*  alias of pdc()->validation->validate_value()
*
*  @type	function
*  @date	6/10/13
*  @since	5.0.0
*
*  @param	n/a
*  @return	n/a
*/

function pdc_validate_value( $value, $field, $input ) {
	
	return pdc()->validation->validate_value( $value, $field, $input );
	
}