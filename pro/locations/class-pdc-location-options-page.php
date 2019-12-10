<?php 

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('pdc_location_options_page') ) :

class pdc_location_options_page extends pdc_location {
	
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
		$this->name = 'options_page';
		$this->label = __("Options Page",'pdc');
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
		
		$options_page = pdc_maybe_get( $screen, 'options_page' );
		return $this->compare( $options_page, $rule );
		
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
		
		// vars
		$pages = pdc_get_options_pages();
		
		
		// populate
		if( !empty($pages) ) {
			foreach( $pages as $page ) {
				$choices[ $page['menu_slug'] ] = $page['menu_title'];
			}
		} else {
			$choices[''] = __('No options pages exist', 'pdc');
		}
		
		
		// return
	    return $choices;
		
	}
	
}

// initialize
pdc_register_location_rule( 'pdc_location_options_page' );

endif; // class_exists check

?>