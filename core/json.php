<?php 

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('pdc_json') ) :

class pdc_json {
	
	function __construct() {
		
		// update setting
		pdc_update_setting('save_json', get_stylesheet_directory() . '/pdc-json');
		pdc_append_setting('load_json', get_stylesheet_directory() . '/pdc-json');
		
		
		// actions
		add_action('pdc/update_field_group',		array($this, 'update_field_group'), 10, 1);
		add_action('pdc/duplicate_field_group',		array($this, 'update_field_group'), 10, 1);
		add_action('pdc/untrash_field_group',		array($this, 'update_field_group'), 10, 1);
		add_action('pdc/trash_field_group',			array($this, 'delete_field_group'), 10, 1);
		add_action('pdc/delete_field_group',		array($this, 'delete_field_group'), 10, 1);
		add_action('pdc/include_fields', 			array($this, 'include_fields'), 10, 0);
		
	}
	
	
	/*
	*  update_field_group
	*
	*  This function is hooked into the pdc/update_field_group action and will save all field group data to a .json file 
	*
	*  @type	function
	*  @date	10/03/2014
	*  @since	5.0.0
	*
	*  @param	$field_group (array)
	*  @return	n/a
	*/
	
	function update_field_group( $field_group ) {
		
		// validate
		if( !pdc_get_setting('json') ) return;
		
		
		// get fields
		$field_group['fields'] = pdc_get_fields( $field_group );
		
		
		// save file
		pdc_write_json_field_group( $field_group );
			
	}
	
	
	/*
	*  delete_field_group
	*
	*  This function will remove the field group .json file
	*
	*  @type	function
	*  @date	10/03/2014
	*  @since	5.0.0
	*
	*  @param	$field_group (array)
	*  @return	n/a
	*/
	
	function delete_field_group( $field_group ) {
		
		// validate
		if( !pdc_get_setting('json') ) return;
		
		
		// WP appends '__trashed' to end of 'key' (post_name) 
		$field_group['key'] = str_replace('__trashed', '', $field_group['key']);
		
		
		// delete
		pdc_delete_json_field_group( $field_group['key'] );
		
	}
		
	
	/*
	*  include_fields
	*
	*  This function will include any JSON files found in the active theme
	*
	*  @type	function
	*  @date	10/03/2014
	*  @since	5.0.0
	*
	*  @param	$version (int)
	*  @return	n/a
	*/
	
	function include_fields() {
		
		// validate
		if( !pdc_get_setting('json') ) return;
		
		
		// vars
		$paths = pdc_get_setting('load_json');
		
		
		// loop through and add to cache
		foreach( $paths as $path ) {
			
			// remove trailing slash
			$path = untrailingslashit( $path );
		
		
			// check that path exists
			if( !file_exists( $path ) ) {
			
				continue;
				
			}
			
			
			$dir = opendir( $path );
	    
		    while(false !== ( $file = readdir($dir)) ) {
		    	
		    	// validate type
				if( pathinfo($file, PATHINFO_EXTENSION) !== 'json' ) continue;
		    	
		    	
		    	// read json
		    	$json = file_get_contents("{$path}/{$file}");
		    	
		    	
		    	// validate json
		    	if( empty($json) ) continue;
		    	
		    	
		    	// decode
		    	$json = json_decode($json, true);
		    	
		    	
		    	// add local
		    	$json['local'] = 'json';
		    	
		    	
		    	// add field group
		    	pdc_add_local_field_group( $json );
		        
		    }
		    
		}
		
	}
	
}


// initialize
pdc()->json = new pdc_json();

endif; // class_exists check


/*
*  pdc_write_json_field_group
*
*  This function will save a field group to a json file within the current theme
*
*  @type	function
*  @date	5/12/2014
*  @since	5.1.5
*
*  @param	$field_group (array)
*  @return	(boolean)
*/

function pdc_write_json_field_group( $field_group ) {
	
	// vars
	$path = pdc_get_setting('save_json');
	$file = $field_group['key'] . '.json';
	
	
	// remove trailing slash
	$path = untrailingslashit( $path );
	
	
	// bail early if dir does not exist
	if( !is_writable($path) ) return false;
	
	
	// prepare for export
	$id = pdc_extract_var( $field_group, 'ID' );
	$field_group = pdc_prepare_field_group_for_export( $field_group );
	

	// add modified time
	$field_group['modified'] = get_post_modified_time('U', true, $id, true);
	
	
	// write file
	$f = fopen("{$path}/{$file}", 'w');
	fwrite($f, pdc_json_encode( $field_group ));
	fclose($f);
	
	
	// return
	return true;
	
}


/*
*  pdc_delete_json_field_group
*
*  This function will delete a json field group file
*
*  @type	function
*  @date	5/12/2014
*  @since	5.1.5
*
*  @param	$key (string)
*  @return	(boolean)
*/

function pdc_delete_json_field_group( $key ) {
	
	// vars
	$path = pdc_get_setting('save_json');
	$file = $key . '.json';
	
	
	// remove trailing slash
	$path = untrailingslashit( $path );
	
	
	// bail early if file does not exist
	if( !is_readable("{$path}/{$file}") ) {
	
		return false;
		
	}
	
		
	// remove file
	unlink("{$path}/{$file}");
	
	
	// return
	return true;
	
}


?>