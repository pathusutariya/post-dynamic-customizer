<?php 

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('pdc_location_widget') ) :

class pdc_location_widget extends pdc_location {
	
	
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
		$this->name = 'widget';
		$this->label = __("Widget",'pdc');
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
		$widget = pdc_maybe_get( $screen, 'widget' );
		
		
		// bail early if not widget
		if( !$widget ) return false;
				
		
        // return
        return $this->compare( $widget, $rule );
		
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
		global $wp_widget_factory;
		
		
		// vars
		$choices = array( 'all' => __('All', 'pdc') );
		
		
		// loop
		if( !empty( $wp_widget_factory->widgets ) ) {
					
			foreach( $wp_widget_factory->widgets as $widget ) {
			
				$choices[ $widget->id_base ] = $widget->name;
				
			}
			
		}
				
		
		// return
		return $choices;
		
	}
	
}

// initialize
pdc_register_location_rule( 'pdc_location_widget' );

endif; // class_exists check

?>