<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('PDC_WPML_Compatibility') ) :

class PDC_WPML_Compatibility {
	
	/**
	*  __construct
	*
	*  Sets up the class functionality.
	*
	*  @date	23/06/12
	*  @since	3.1.8
	*
	*  @param	void
	*  @return	void
	*/
	function __construct() {
		
		// global
		global $sitepress;
		
		// update settings
		pdc_update_setting('default_language', $sitepress->get_default_language());
		pdc_update_setting('current_language', $sitepress->get_current_language());
		
		// localize data
		pdc_localize_data(array(
		   	'language' => $sitepress->get_current_language()
	   	));
		
		// switch lang during AJAX action
		add_action('pdc/verify_ajax', array($this, 'verify_ajax'));
		
		// prevent 'pdc-field' from being translated
		add_filter('get_translatable_documents', array($this, 'get_translatable_documents'));
		
		// check if 'pdc-field-group' is translatable
		if( $this->is_translatable() ) {
			
			// actions
			add_action('pdc/upgrade_500_field_group',		array($this, 'upgrade_500_field_group'), 10, 2);
			add_action('icl_make_duplicate',				array($this, 'icl_make_duplicate'), 10, 4);
			
			// filters
			add_filter('pdc/settings/save_json',			array($this, 'settings_save_json'));
			add_filter('pdc/settings/load_json',			array($this, 'settings_load_json'));
		}
	}
	
	/**
	*  is_translatable
	*
	*  Returns true if the pdc-field-group post type is translatable.
	*  Also adds compatibility with PDC4 settings
	*
	*  @date	10/04/2015
	*  @since	5.2.3
	*
	*  @param	void
	*  @return	bool
	*/
	function is_translatable() {
		
		// global
		global $sitepress;
		
		// vars
		$post_types = $sitepress->get_setting('custom_posts_sync_option');
		
		// return false if no post types
		if( !pdc_is_array($post_types) ) {
			return false;
		}
		
		// prevent 'pdc-field' from being translated
		if( !empty($post_types['pdc-field']) ) {
			$post_types['pdc-field'] = 0;
			$sitepress->set_setting('custom_posts_sync_option', $post_types);
		}
		
		// when upgrading to version 5, review 'pdc' setting
		// update 'pdc-field-group' if 'pdc' is translatable, and 'pdc-field-group' does not yet exist
		if( !empty($post_types['pdc']) && !isset($post_types['pdc-field-group']) ) {
			$post_types['pdc-field-group'] = 1;
			$sitepress->set_setting('custom_posts_sync_option', $post_types);
		}
		
		// return true if pdc-field-group is translatable
		if( !empty($post_types['pdc-field-group']) ) {
			return true;
		}
		
		// return
		return false;
	}
	
	/**
	*  upgrade_500_field_group
	*
	*  Update the icl_translations table data when creating the field groups.
	*
	*  @date	10/04/2015
	*  @since	5.2.3
	*
	*  @param	array $field_group The new field group array.
	*  @param	object $ofg The old field group WP_Post object.
	*  @return	void
	*/
	function upgrade_500_field_group($field_group, $ofg) {
		
		// global
		global $wpdb;
		
		// get translation rows (old pdc4 and new pdc5)
		$old_row = $wpdb->get_row($wpdb->prepare(
			"SELECT * FROM {$wpdb->prefix}icl_translations WHERE element_type=%s AND element_id=%d", 
			'post_pdc', $ofg->ID
		), ARRAY_A);
		
		$new_row = $wpdb->get_row($wpdb->prepare(
			"SELECT * FROM {$wpdb->prefix}icl_translations WHERE element_type=%s AND element_id=%d", 
			'post_pdc-field-group', $field_group['ID']
		), ARRAY_A);
		
		// bail ealry if no rows
		if( !$old_row || !$new_row ) {
			return;
		}
		
		// create reference of old trid to new trid
		// trid is a simple int used to find associated objects
		if( empty($this->trid_ref) ) {
			$this->trid_ref = array();
		}
		
		// update trid
		if( isset($this->trid_ref[ $old_row['trid'] ]) ) {
			
			// this field group is a translation of another, update it's trid to match the previously inserted group
			$new_row['trid'] = $this->trid_ref[ $old_row['trid'] ];
		} else {
			
			// this field group is the first of it's translations, update the reference for future groups
			$this->trid_ref[ $old_row['trid'] ] = $new_row['trid'];
		}
		
		// update icl_translations
		// Row is created by WPML, and much easier to tweak it here due to the very complicated and nonsensical WPML logic
		$table = "{$wpdb->prefix}icl_translations";
		$data = array( 'trid' => $new_row['trid'], 'language_code' => $old_row['language_code'] );
		$where = array( 'translation_id' => $new_row['translation_id'] );
		$data_format = array( '%d', '%s' );
		$where_format = array( '%d' );
		
		// allow source_language_code to equal NULL
		if( $old_row['source_language_code'] ) {
			
			$data['source_language_code'] = $old_row['source_language_code'];
			$data_format[] = '%s';
		}
		
		// update wpdb
		$result = $wpdb->update( $table, $data, $where, $data_format, $where_format );
	}
	
	/**
	*  settings_save_json
	*
	*  Modifies the json path.
	*
	*  @date	19/05/2014
	*  @since	5.0.0
	*
	*  @param	string $path The json save path.
	*  @return	string
	*/
	function settings_save_json( $path ) {	

		// bail early if dir does not exist
		if( !is_writable($path) ) {
			return $path;
		}
		
		// ammend
		$path = untrailingslashit($path) . '/' . pdc_get_setting('current_language');
		
		// make dir if does not exist
		if( !file_exists($path) ) {
			mkdir($path, 0777, true);
		}
		
		// return
		return $path;
	}
	
	/**
	*  settings_load_json
	*
	*  Modifies the json path.
	*
	*  @date	19/05/2014
	*  @since	5.0.0
	*
	*  @param	string $path The json save path.
	*  @return	string
	*/	
	function settings_load_json( $paths ) {
		
		// loop
		if( $paths ) {
		foreach( $paths as $i => $path ) {
			$paths[ $i ] = untrailingslashit($path) . '/' . pdc_get_setting('current_language');
		}}
		
		// return
		return $paths;
	}
	
	/**
	*  icl_make_duplicate
	*
	*  description
	*
	*  @date	26/02/2014
	*  @since	5.0.0
	*
	*  @param	void
	*  @return	void
	*/
	function icl_make_duplicate( $master_post_id, $lang, $postarr, $id ) {
		
		// bail early if not pdc-field-group
		if( $postarr['post_type'] != 'pdc-field-group' ) {
			return;
		}
		
		// update the lang
		pdc_update_setting('current_language', $lang);
		
		// duplicate field group specifying the $post_id
		pdc_duplicate_field_group( $master_post_id, $id );
		
		// always translate independately to avoid many many bugs!
		// - translation post gets a new key (post_name) when origional post is saved
		// - local json creates new files due to changed key
		global $iclTranslationManagement;
		$iclTranslationManagement->reset_duplicate_flag( $id );
	}
	
	
	/**
	*  verify_ajax
	*
	*  Sets the correct language during AJAX requests.
	*
	*  @type	function
	*  @date	7/08/2015
	*  @since	5.2.3
	*
	*  @param	void
	*  @return	void
	*/
	function verify_ajax() {
		
		// set the language for this AJAX request
		// this will allow get_posts to work as expected (load posts from the correct language)
		if( isset($_REQUEST['lang']) ) {
			global $sitepress;
			$sitepress->switch_lang( $_REQUEST['lang'] );
		}
	}
	
	/**
	*  get_translatable_documents
	*
	*  Removes 'pdc-field' from the available post types for translation.
	*
	*  @type	function
	*  @date	17/8/17
	*  @since	5.6.0
	*
	*  @param	array $icl_post_types The array of post types.
	*  @return	array
	*/
	function get_translatable_documents( $icl_post_types ) {
		
		// unset
		unset( $icl_post_types['pdc-field'] );
		
		// return
		return $icl_post_types;
	}
}

pdc_new_instance('PDC_WPML_Compatibility');

endif; // class_exists check

?>