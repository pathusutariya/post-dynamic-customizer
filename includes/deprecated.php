<?php 

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('pdc_deprecated') ) :

class pdc_deprecated {
	
	/*
	*  __construct
	*
	*  This function will setup the class functionality
	*
	*  @type	function
	*  @date	30/1/17
	*  @since	5.5.6
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function __construct() {
		
		// settings
		add_filter('pdc/settings/show_admin',			array($this, 'pdc_settings_show_admin'), 5, 1);				// 5.0.0
		add_filter('pdc/settings/l10n_textdomain',		array($this, 'pdc_settings_l10n_textdomain'), 5, 1);		// 5.3.3
		add_filter('pdc/settings/l10n_field',			array($this, 'pdc_settings_l10n_field'), 5, 1);				// 5.3.3
		add_filter('pdc/settings/l10n_field_group',		array($this, 'pdc_settings_l10n_field'), 5, 1);				// 5.3.3
		add_filter('pdc/settings/url',					array($this, 'pdc_settings_url'), 5, 1);					// 5.6.8
		add_filter('pdc/validate_setting',				array($this, 'pdc_validate_setting'), 5, 1);				// 5.6.8
		

		// filters
		add_filter('pdc/validate_field', 				array($this, 'pdc_validate_field'), 10, 1); 				// 5.5.6
		add_filter('pdc/validate_field_group', 			array($this, 'pdc_validate_field_group'), 10, 1); 			// 5.5.6
		add_filter('pdc/validate_post_id', 				array($this, 'pdc_validate_post_id'), 10, 2); 			// 5.5.6
		
	}
	
	
	/*
	*  pdc_settings_show_admin
	*
	*  This function will add compatibility for previously named hooks
	*
	*  @type	function
	*  @date	19/05/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function pdc_settings_show_admin( $setting ) {
		
		// 5.0.0 - removed PDC_LITE
		return ( defined('PDC_LITE') && PDC_LITE ) ? false : $setting;
		
	}
	
	
	/*
	*  pdc_settings_l10n_textdomain
	*
	*  This function will add compatibility for previously named hooks
	*
	*  @type	function
	*  @date	19/05/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function pdc_settings_l10n_textdomain( $setting ) {
		
		// 5.3.3 - changed filter name
		return pdc_get_setting( 'export_textdomain', $setting );
		
	}
	
	
	/*
	*  pdc_settings_l10n_field
	*
	*  This function will add compatibility for previously named hooks
	*
	*  @type	function
	*  @date	19/05/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function pdc_settings_l10n_field( $setting ) {
		
		// 5.3.3 - changed filter name
		return pdc_get_setting( 'export_translate', $setting );
		
	}
	
	
	/**
	*  pdc_settings_url
	*
	*  This function will add compatibility for previously named hooks
	*
	*  @date	12/12/17
	*  @since	5.6.8
	*
	*  @param	n/a
	*  @return	n/a
	*/
		
	function pdc_settings_url( $value ) {
		return apply_filters( "pdc/settings/dir", $value );
	}
	
	/**
	*  pdc_validate_setting
	*
	*  description
	*
	*  @date	2/2/18
	*  @since	5.6.5
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	function pdc_validate_setting( $name ) {
		
		// vars
		$changed = array(
			'dir' => 'url'	// 5.6.8
		);
		
		// check
		if( isset($changed[ $name ]) ) {
			return $changed[ $name ];
		}
		
		//return
		return $name;
	}
	
	
	/*
	*  pdc_validate_field
	*
	*  This function will add compatibility for previously named hooks
	*
	*  @type	function
	*  @date	30/1/17
	*  @since	5.5.6
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function pdc_validate_field( $field ) {
		
		// 5.5.6 - changed filter name
		$field = apply_filters( "pdc/get_valid_field/type={$field['type']}", $field );
		$field = apply_filters( "pdc/get_valid_field", $field );
		
		
		// return
		return $field;
		
	}
	
	
	/*
	*  pdc_validate_field_group
	*
	*  This function will add compatibility for previously named hooks
	*
	*  @type	function
	*  @date	30/1/17
	*  @since	5.5.6
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function pdc_validate_field_group( $field_group ) {
		
		// 5.5.6 - changed filter name
		$field_group = apply_filters('pdc/get_valid_field_group', $field_group);
		
		
		// return
		return $field_group;
		
	}
	
	
	/*
	*  pdc_validate_post_id
	*
	*  This function will add compatibility for previously named hooks
	*
	*  @type	function
	*  @date	6/2/17
	*  @since	5.5.6
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function pdc_validate_post_id( $post_id, $_post_id ) {
		
		// 5.5.6 - changed filter name
		$post_id = apply_filters('pdc/get_valid_post_id', $post_id, $_post_id);
		
		
		// return
		return $post_id;
		
	}
	
}


// initialize
pdc()->deprecated = new pdc_deprecated();

endif; // class_exists check

?>