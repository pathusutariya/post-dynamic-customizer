<?php 

if( !class_exists('pdc_pro') ):

class pdc_pro {
	
	/*
	*  __construct
	*
	*  
	*
	*  @type	function
	*  @date	23/06/12
	*  @since	5.0.0
	*
	*  @param	N/A
	*  @return	N/A
	*/
	
	function __construct() {
		
		// update setting
		pdc_update_setting( 'pro', true );
		pdc_update_setting( 'name', __('Post Dynamic Customizer', 'pdc') );
		

		// api
		pdc_include('pro/api/api-pro.php');
		pdc_include('pro/api/api-options-page.php');
		
		
		// updates
		pdc_include('pro/core/updates.php');
			
			
		// admin
		if( is_admin() ) {
			
			// options page
			pdc_include('pro/admin/options-page.php');
			
			// settings
			pdc_include('pro/admin/settings-updates.php');
			
		}
		
		
		// actions
		add_action('init',										array($this, 'register_assets'));
		add_action('pdc/include_field_types',					array($this, 'include_field_types'), 5);
		add_action('pdc/input/admin_enqueue_scripts',			array($this, 'input_admin_enqueue_scripts'));
		add_action('pdc/field_group/admin_enqueue_scripts',		array($this, 'field_group_admin_enqueue_scripts'));
		
	}
	
	
	/*
	*  include_field_types
	*
	*  description
	*
	*  @type	function
	*  @date	21/10/2015
	*  @since	5.2.3
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function include_field_types() {
		
		pdc_include('pro/fields/repeater.php');
		pdc_include('pro/fields/flexible-content.php');
		pdc_include('pro/fields/gallery.php');
		pdc_include('pro/fields/clone.php');
		
	}
	
	
	/*
	*  register_assets
	*
	*  description
	*
	*  @type	function
	*  @date	4/11/2013
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function register_assets() {
		
		// vars
		$version = pdc_get_setting('version');
		$min = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
		
		
		// register scripts
		wp_register_script( 'pdc-pro-input', pdc_get_dir( "pro/assets/js/pdc-pro-input{$min}.js" ), array('pdc-input'), $version );
		wp_register_script( 'pdc-pro-field-group', pdc_get_dir( "pro/assets/js/pdc-pro-field-group{$min}.js" ), array('pdc-field-group'), $version );
		
		
		// register styles
		wp_register_style( 'pdc-pro-input', pdc_get_dir( 'pro/assets/css/pdc-pro-input.css' ), array('pdc-input'), $version ); 
		wp_register_style( 'pdc-pro-field-group', pdc_get_dir( 'pro/assets/css/pdc-pro-field-group.css' ), array('pdc-input'), $version ); 
		
	}
	
	
	/*
	*  input_admin_enqueue_scripts
	*
	*  description
	*
	*  @type	function
	*  @date	4/11/2013
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function input_admin_enqueue_scripts() {
		
		// scripts
		wp_enqueue_script('pdc-pro-input');
	
	
		// styles
		wp_enqueue_style('pdc-pro-input');
		
	}
	
	
	/*
	*  field_group_admin_enqueue_scripts
	*
	*  description
	*
	*  @type	function
	*  @date	4/11/2013
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function field_group_admin_enqueue_scripts() {
		
		// scripts
		wp_enqueue_script('pdc-pro-field-group');
	
	
		// styles
		wp_enqueue_style('pdc-pro-field-group');
		
	}
	 
}


// instantiate
new pdc_pro();


// end class
endif;

?>