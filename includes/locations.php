<?php 

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('pdc_locations') ) :

class pdc_locations {
	
	
	/** @var array Contains an array of location rule instances */
	var $locations = array();
	
	
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
		
		/* do nothing */
		
	}
	
	
	/*
	*  register_location
	*
	*  This function will store a location rule class
	*
	*  @type	function
	*  @date	6/07/2016
	*  @since	5.4.0
	*
	*  @param	$instance (object)
	*  @return	n/a
	*/
	
	function register_location( $class ) {
		
		$instance = new $class();
		$this->locations[ $instance->name ] = $instance;
		
	}
	
	
	/*
	*  get_rule
	*
	*  This function will return a location rule class
	*
	*  @type	function
	*  @date	6/07/2016
	*  @since	5.4.0
	*
	*  @param	$name (string)
	*  @return	(mixed)
	*/
	
	function get_location( $name ) {
		
		return isset( $this->locations[$name] ) ? $this->locations[$name] : null;
		
	}
	
		
	/*
	*  get_rules
	*
	*  This function will return a grouped array of location rules (category => name => label)
	*
	*  @type	function
	*  @date	6/07/2016
	*  @since	5.4.0
	*
	*  @param	n/a
	*  @return	(array)
	*/
	
	function get_locations() {
		
		// vars
		$groups = array();
		$l10n = array(
			'post'		=> __('Post', 'pdc'),
			'page'		=> __('Page', 'pdc'),
			'user'		=> __('User', 'pdc'),
			'forms'		=> __('Forms', 'pdc'),
		);
		
			
		// loop
		foreach( $this->locations as $location ) {
			
			// bail ealry if not public
			if( !$location->public ) continue;
			
			
			// translate
			$cat = $location->category;
			$cat = isset( $l10n[$cat] ) ? $l10n[$cat] : $cat;
			
			
			// append
			$groups[ $cat ][ $location->name ] = $location->label;
			
		}
		
		
		// filter
		$groups = apply_filters('pdc/location/rule_types', $groups);
		
		
		// return
		return $groups;
		
	}
	
}

// initialize
pdc()->locations = new pdc_locations();

endif; // class_exists check


/*
*  pdc_register_location_rule
*
*  alias of pdc()->locations->register_location()
*
*  @type	function
*  @date	31/5/17
*  @since	5.6.0
*
*  @param	n/a
*  @return	n/a
*/

function pdc_register_location_rule( $class ) {
	
	return pdc()->locations->register_location( $class );
	
}


/*
*  pdc_get_location_rule
*
*  alias of pdc()->locations->get_location()
*
*  @type	function
*  @date	31/5/17
*  @since	5.6.0
*
*  @param	n/a
*  @return	n/a
*/

function pdc_get_location_rule( $name ) {
	
	return pdc()->locations->get_location( $name );
	
}


/*
*  pdc_get_location_rule_types
*
*  alias of pdc()->locations->get_locations()
*
*  @type	function
*  @date	31/5/17
*  @since	5.6.0
*
*  @param	n/a
*  @return	n/a
*/

function pdc_get_location_rule_types() {
	
	return pdc()->locations->get_locations();
	
}


/**
*  pdc_validate_location_rule
*
*  Returns a valid location rule array.
*
*  @date	28/8/18
*  @since	5.7.4
*
*  @param	$rule array The rule array.
*  @return	array
*/

function pdc_validate_location_rule( $rule = false ) {
	
	// defaults
	$rule = wp_parse_args( $rule, array(
		'id'		=> '',
		'group'		=> '',
		'param'		=> '',
		'operator'	=> '==',
		'value'		=> '',
	));
	
	// filter
	$rule = apply_filters( "pdc/location/validate_rule/type={$rule['param']}", $rule );
	$rule = apply_filters( "pdc/location/validate_rule", $rule);
	
	// return
	return $rule;
}

/*
*  pdc_get_location_rule_operators
*
*  This function will return the operators for a given rule
*
*  @type	function
*  @date	30/5/17
*  @since	5.6.0
*
*  @param	$rule (array)
*  @return	(array)
*/

function pdc_get_location_rule_operators( $rule ) {
	
	// vars
	$operators = array(
		'=='	=> __("is equal to",'pdc'),
		'!='	=> __("is not equal to",'pdc'),
	);
	
	
	// filter
	$operators = apply_filters( "pdc/location/rule_operators/type={$rule['param']}", $operators, $rule );
	$operators = apply_filters( "pdc/location/rule_operators/{$rule['param']}", $operators, $rule );
	$operators = apply_filters( "pdc/location/rule_operators", $operators, $rule );
	
	
	// return
	return $operators;
	
}


/*
*  pdc_get_location_rule_values
*
*  This function will return the values for a given rule 
*
*  @type	function
*  @date	30/5/17
*  @since	5.6.0
*
*  @param	$rule (array)
*  @return	(array)
*/

function pdc_get_location_rule_values( $rule ) {
	
	// vars
	$values = array();
	
	
	// filter
	$values = apply_filters( "pdc/location/rule_values/type={$rule['param']}", $values, $rule );
	$values = apply_filters( "pdc/location/rule_values/{$rule['param']}", $values, $rule );
	$values = apply_filters( "pdc/location/rule_values", $values, $rule );
	
	
	// return
	return $values;
	
}


/*
*  pdc_match_location_rule
*
*  This function will match a given rule to the $screen
*
*  @type	function
*  @date	30/5/17
*  @since	5.6.0
*
*  @param	$rule (array)
*  @param	$screen (array)
*  @return	(boolean)
*/

function pdc_match_location_rule( $rule, $screen ) {
	
	// vars
	$result = false;
	
	
	// filter
	$result = apply_filters( "pdc/location/match_rule/type={$rule['param']}", $result, $rule, $screen );
	$result = apply_filters( "pdc/location/match_rule", $result, $rule, $screen );
	$result = apply_filters( "pdc/location/rule_match/{$rule['param']}", $result, $rule, $screen );
	$result = apply_filters( "pdc/location/rule_match", $result, $rule, $screen );
	
	
	// return
	return $result;
	
}


/*
*  pdc_get_location_screen
*
*  This function will return a valid location screen array
*
*  @type	function
*  @date	30/5/17
*  @since	5.6.0
*
*  @param	$screen (array)
*  @param	$field_group (array)
*  @return	(array)
*/

function pdc_get_location_screen( $screen, $field_group ) {
	
	// vars
	$screen = wp_parse_args($screen, array(
		'lang'	=> pdc_get_setting('current_language'),
		'ajax'	=> false
	));
	
	
	// filter for 3rd party customization
	$screen = apply_filters('pdc/location/screen', $screen, $field_group);
	
	
	// return
	return $screen;
	
}

/**
*  pdc_get_valid_location_rule
*
*  Deprecated in 5.7.4. Use pdc_validate_location_rule() instead.
*
*  @date	30/5/17
*  @since	5.6.0
*
*  @param	$rule array The rule array.
*  @return	array
*/

function pdc_get_valid_location_rule( $rule ) {
	return pdc_validate_location_rule( $rule );
}

?>