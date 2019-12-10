<?php 

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('pdc_location_nav_menu_item') ) :

class pdc_location_nav_menu_item extends pdc_location {
	
	
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
	
	function initialize() {
		
		// vars
		$this->name = 'nav_menu_item';
		$this->label = __("Menu Item",'pdc');
		$this->category = 'forms';
    	
	}
	

	/*
	*  rule_match
	*
	*  This function is used to match this location $rule to the current $screen
	*
	*  @type	function
	*  @date	3/01/13
	*  @since	3.5.7
	*
	*  @param	$match (boolean) 
	*  @param	$rule (array)
	*  @return	$options (array)
	*/
	
	function rule_match( $result, $rule, $screen ) {
		
		// vars
		$nav_menu_item = pdc_maybe_get( $screen, 'nav_menu_item' );
		
		
		// bail early if not nav_menu_item
		if( !$nav_menu_item ) return false;
		
		
		// append nav_menu data
		if( !isset($screen['nav_menu']) ) {
			$screen['nav_menu'] = pdc_get_data('nav_menu_id');
		}
		
		
        // return
        return pdc_get_location_rule('nav_menu')->rule_match( $result, $rule, $screen );
		
	}
	
	
	/*
	*  rule_operators
	*
	*  This function returns the available values for this rule type
	*
	*  @type	function
	*  @date	30/5/17
	*  @since	5.6.0
	*
	*  @param	n/a
	*  @return	(array)
	*/
	
	function rule_values( $choices, $rule ) {
		
		// get menu choices
		$choices = pdc_get_location_rule('nav_menu')->rule_values( $choices, $rule );
		
		
		// append item types?
		// dificult to get these details
			
		
		// return
		return $choices;
		
	}
	
}

// initialize
pdc_register_location_rule( 'pdc_location_nav_menu_item' );

endif; // class_exists check

?>