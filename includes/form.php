<?php 

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('PDC_Form') ) :

class PDC_Form {
	
	/** @var array Storage for data */
	var $data = array();
	
	
	/*
	*  __construct
	*
	*  This function will setup the class functionality.
	*
	*  @type	function
	*  @date	5/03/2014
	*  @since	5.0.0
	*
	*  @param	void
	*  @return	void
	*/
	
	function __construct() {
		
		// actions
		add_action('pdc/save_post', array($this, '_save_post'), 10, 1);
	}
	
	
	/*
	*  set_data
	*
	*  Sets data.
	*
	*  @type	function
	*  @date	4/03/2016
	*  @since	5.3.2
	*
	*  @param	array $data An array of data.
	*  @return	array
	*/
	
	function set_data( $data = array() ) {
		
		// defaults
		$data = wp_parse_args($data, array(
			'screen'		=> 'post',	// Current screen loaded (post, user, taxonomy, etc)
			'post_id'		=> 0,		// ID of current post being edited
			'nonce'			=> '',		// nonce used for $_POST validation (defaults to screen)
			'validation'	=> 1,		// enables form validation
			'changed'		=> 0,		// used by revisions and unload to detect change
		));
		
		// crete nonce
		$data['nonce'] = wp_create_nonce($data['screen']);
		
		// update
		$this->data = $data;
		
		// return 
		return $data;
	}
	
	
	/*
	*  get_data
	*
	*  Returns data.
	*
	*  @type	function
	*  @date	4/03/2016
	*  @since	5.3.2
	*
	*  @param	string $name The data anme.
	*  @return	mixed The data.
	*/
	
	function get_data( $name = false ) {
		return isset($this->data[ $name ]) ? $this->data[ $name ] : null;
	}
	
	/**
	*  render_data
	*
	*  Renders the <div id="pdc-form-data"> element with hidden "form data" inputs
	*
	*  @date	17/4/18
	*  @since	5.6.9
	*
	*  @param	array $data An array of data.
	*  @return	void
	*/
	
	function render_data( $data = array() ) {
		
		// set form data
		$data = $this->set_data( $data );
		
		?>
		<div id="pdc-form-data" class="pdc-hidden">
			<?php 
			
			// loop
			foreach( $data as $name => $value ) {
				
				// input
				pdc_hidden_input(array(
					'id'	=> '_pdc_' . $name,
					'name'	=> '_pdc_' . $name,
					'value'	=> $value
				));
			}
			
			// actions
			do_action('pdc/form_data', $data);
			do_action('pdc/input/form_data', $data);
			
			?>
		</div>
		<?php
	}
	
	
	/**
	*  save_post
	*
	*  Calls the 'pdc/save_post' action allowing $_POST data to be saved
	*
	*  @date	17/4/18
	*  @since	5.6.9
	*
	*  @param	mixed $post_id The $post_id used to save data to the DB
	*  @param	array $values Optional. An optional array of data to be saved (modifies $_POST['pdc'])
	*  @return	boolean Returns true on success.
	*/
	
	
	function save_post( $post_id = 0, $values = null ) {
		
		// override $_POST
		if( $values !== null ) {
			$_POST['pdc'] = $values;
		}
		
		// bail early if no values
		if( empty($_POST['pdc']) ) {
			return false;
		}
		
		// set form data
		$this->set_data(array(
			'post_id' => $post_id
		));
		
		// action
		do_action('pdc/save_post', $post_id);
		
		// return
		return true;
	}

	
	/**
	*  _save_post
	*
	*  Saves the actual $_POST['pdc'] data.
	*  Performing this logic within an action allows developers to hook in before and after data is saved.
	*
	*  @date	24/10/2014
	*  @since	5.0.9
	*
	*  @param	mixed $post_id The $post_id used to save data to the DB
	*  @return	void.
	*/
	
	function _save_post( $post_id ) {
		
		// bail early if empty
		// - post data may have be modified
		if( empty($_POST['pdc']) ) {
			return;
		}
		
		// loop
		foreach( $_POST['pdc'] as $key => $value ) {
			
			// get field
			$field = pdc_get_field( $key );
			
			// update
			if( $field ) {
				pdc_update_value( $value, $post_id, $field );
			}
		}
	}
}

// instantiate
pdc_new_instance('PDC_Form');

endif; // class_exists check


/*
*  pdc_get_form_data
*
*  alias of pdc()->form->get_data()
*
*  @type	function
*  @date	6/10/13
*  @since	5.0.0
*
*  @param	n/a
*  @return	n/a
*/

function pdc_get_form_data( $name = '' ) {
	return pdc_get_instance('PDC_Form')->get_data( $name );
}


/*
*  pdc_set_form_data
*
*  alias of pdc()->form->set_data()
*
*  @type	function
*  @date	6/10/13
*  @since	5.0.0
*
*  @param	n/a
*  @return	n/a
*/

function pdc_set_form_data( $data = array() ) {
	return pdc_get_instance('PDC_Form')->set_data( $data );
}


/*
*  pdc_form_data
*
*  description
*
*  @type	function
*  @date	15/10/13
*  @since	5.0.0
*
*  @param	$post_id (int)
*  @return	$post_id (int)
*/

function pdc_form_data( $data = array() ) {
	return pdc_get_instance('PDC_Form')->render_data( $data );
}

/*
*  pdc_save_post
*
*  description
*
*  @type	function
*  @date	15/10/13
*  @since	5.0.0
*
*  @param	$post_id (int)
*  @return	$post_id (int)
*/

function pdc_save_post( $post_id = 0, $values = null ) {
	return pdc_get_instance('PDC_Form')->save_post( $post_id, $values );
}

?>