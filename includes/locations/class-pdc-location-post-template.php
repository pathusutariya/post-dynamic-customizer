<?php 

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('pdc_location_post_template') ) :

class pdc_location_post_template extends pdc_location {
	
	
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
		$this->name = 'post_template';
		$this->label = __("Post Template",'pdc');
		$this->category = 'post';
		$this->public = pdc_version_compare('wp', '>=', '4.7');
    	
	}
	
	
	/*
	*  get_post_type
	*
	*  This function will return the current post_type
	*
	*  @type	function
	*  @date	25/11/16
	*  @since	5.5.0
	*
	*  @param	$options (int)
	*  @return	(mixed)
	*/
	
	function get_post_type( $screen ) {
		
		// vars
		$post_id = pdc_maybe_get( $screen, 'post_id' );
		$post_type = pdc_maybe_get( $screen, 'post_type' );
		
		
		// post_type
		if( $post_type ) return $post_type;
		
		
		// $post_id
		if( $post_id ) return get_post_type( $post_id );
		
		
		// return
		return false;
		
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
		$templates = array();
		$post_id = pdc_maybe_get( $screen, 'post_id' );
		$page_template = pdc_maybe_get( $screen, 'page_template' );
		$post_type = $this->get_post_type( $screen  );
		
		
		// bail early if no post_type found (not a post)
		if( !$post_type ) return false;
		
		
		// get templates (WP 4.7)
		if( pdc_version_compare('wp', '>=', '4.7') ) {
			
			$templates = pdc_get_post_templates();
			
		}
		
		
		// 'page' is always a valid pt even if no templates exist in the theme
		// allows scenario where page_template = 'default' and no templates exist
		if( !isset($templates['page']) ) {
			
			$templates['page'] = array();
			
		}
		
		
		// bail early if this post type does not allow for templates
		if( !isset($templates[ $post_type ]) ) return false;
		
		
		// get page template
		if( !$page_template ) {
		
			$page_template = get_post_meta( $post_id, '_wp_page_template', true );
			
		}
		
		
		// new post - no page template
		if( !$page_template ) $page_template = "default";
		
		
		// match
		return $this->compare( $page_template, $rule );
		
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
		$choices = array(
			'default' => apply_filters( 'default_page_template_title',  __('Default Template', 'pdc') )
		);
		
		
		// get templates (WP 4.7)
		if( pdc_version_compare('wp', '>=', '4.7') ) {
			
			$templates = pdc_get_post_templates();
			$choices = array_merge($choices, $templates);
			
		}
		
		
		// return choices
		return $choices;
		
	}
	
}

// initialize
pdc_register_location_rule( 'pdc_location_post_template' );

endif; // class_exists check

?>