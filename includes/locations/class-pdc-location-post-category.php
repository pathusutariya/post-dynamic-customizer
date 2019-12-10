<?php 

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('pdc_location_post_category') ) :

class pdc_location_post_category extends pdc_location {
	
	
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
		$this->name = 'post_category';
		$this->label = __("Post Category",'pdc');
		$this->category = 'post';
    	
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
		
		return pdc_get_location_rule('post_taxonomy')->rule_match( $result, $rule, $screen );
		
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
		
		$terms = pdc_get_taxonomy_terms( 'category' );
				
		if( !empty($terms) ) {
			
			$choices = array_pop($terms);
			
		}
		
		return $choices;
		
	}
	
}

// initialize
pdc_register_location_rule( 'pdc_location_post_category' );

endif; // class_exists check

?>