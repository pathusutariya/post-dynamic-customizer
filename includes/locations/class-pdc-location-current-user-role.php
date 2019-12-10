<?php 

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('pdc_location_current_user_role') ) :

class pdc_location_current_user_role extends pdc_location {
	
	
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
		$this->name = 'current_user_role';
		$this->label = __("Current User Role",'pdc');
		$this->category = 'user';
    	
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
		
		// bail early if not logged in
		if( !is_user_logged_in() ) return false;
		
		
		// vars
		$user = wp_get_current_user();
		
		
		// super_admin
		if( $rule['value'] == 'super_admin' ) {
			
			$result = is_super_admin( $user->ID );
			
		// role
		} else {
			
			$result = in_array( $rule['value'], $user->roles );
			
		}
		
		
		// reverse if 'not equal to'
        if( $rule['operator'] == '!=' ) {
	        	
        	$result = !$result;
        
        }
		
		
        // return
        return $result;
        
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
		
		// global
		global $wp_roles;
		
		
		// specific roles
		$choices = $wp_roles->get_names();
		
		
		// multi-site
		if( is_multisite() ) {
			
			$prepend = array( 'super_admin' => __('Super Admin', 'pdc') );
			$choices = array_merge( $prepend, $choices );
			
		}
		
		
		// return
		return $choices;
		
	}
	
}

// initialize
pdc_register_location_rule( 'pdc_location_current_user_role' );

endif; // class_exists check

?>