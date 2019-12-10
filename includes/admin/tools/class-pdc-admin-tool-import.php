<?php 

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('PDC_Admin_Tool_Import') ) :

class PDC_Admin_Tool_Import extends PDC_Admin_Tool {
	
	
	/**
	*  initialize
	*
	*  This function will initialize the admin tool
	*
	*  @date	10/10/17
	*  @since	5.6.3
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function initialize() {
		
		// vars
		$this->name = 'import';
		$this->title = __("Import Field Groups", 'pdc');
    	$this->icon = 'dashicons-upload';
    	
	}
	
	
	/**
	*  html
	*
	*  This function will output the metabox HTML
	*
	*  @date	10/10/17
	*  @since	5.6.3
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function html() {
		
		// vars
		$choices = array();
		$field_groups = pdc_get_field_groups();
		
		
		// loop
		if( $field_groups ) {
			foreach( $field_groups as $field_group ) {
				$choices[ $field_group['key'] ] = esc_html( $field_group['title'] );
			}	
		}
		
		
		// html
		?>
		<p><?php _e('Select the Post Dynamic Customizer JSON file you would like to import. When you click the import button below, PDC will import the field groups.', 'pdc'); ?></p>
		<div class="pdc-fields">
			<?php 
			
			pdc_render_field_wrap(array(
				'label'		=> __('Select File', 'pdc'),
				'type'		=> 'file',
				'name'		=> 'pdc_import_file',
				'value'		=> false,
				'uploader'	=> 'basic',
			));
			
			?>
		</div>
		<p class="pdc-submit">
			<input type="submit" class="button button-primary" value="<?php _e('Import File', 'pdc'); ?>" />
		</p>
		<?php
		
	}
	
	
	/**
	*  submit
	*
	*  This function will run when the tool's form has been submit
	*
	*  @date	10/10/17
	*  @since	5.6.3
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function submit() {
		
		// validate
		if( empty($_FILES['pdc_import_file']['size']) ) {
			
			pdc_add_admin_notice( __("No file selected", 'pdc') , 'error');
			return;
		
		}
		
		
		// vars
		$file = $_FILES['pdc_import_file'];
		
		
		// validate error
		if( $file['error'] ) {
			
			pdc_add_admin_notice(__('Error uploading file. Please try again', 'pdc'), 'error');
			return;
		
		}
		
		
		// validate type
		if( pathinfo($file['name'], PATHINFO_EXTENSION) !== 'json' ) {
		
			pdc_add_admin_notice(__('Incorrect file type', 'pdc'), 'error');
			return;
			
		}
		
		
		// read file
		$json = file_get_contents( $file['tmp_name'] );
		
		
		// decode json
		$json = json_decode($json, true);
		
		
		// validate json
    	if( empty($json) ) {
    	
    		pdc_add_admin_notice(__('Import file empty', 'pdc'), 'error');
	    	return;
    	
    	}
    	
    	
    	// if importing an auto-json, wrap field group in array
    	if( isset($json['key']) ) {
	    	
	    	$json = array( $json );
	    	
    	}
    	
    	
    	// vars
    	$ids = array();
    	$keys = array();
    	$imported = array();
    	
    	
    	// populate keys
    	foreach( $json as $field_group ) {
	    	
	    	// append key
	    	$keys[] = $field_group['key'];
	    	
	    }
	    
	    
    	// look for existing ids
    	foreach( $keys as $key ) {
	    	
	    	// attempt find ID
	    	$field_group = _pdc_get_field_group_by_key( $key );
	    	
	    	
	    	// bail early if no field group
	    	if( !$field_group ) continue;
	    	
	    	
	    	// append
	    	$ids[ $key ] = $field_group['ID'];
	    	
	    }
	    
    	
    	// enable local
		pdc_enable_local();
		
		
		// reset local (JSON class has already included .json field groups which may conflict)
		pdc_reset_local();
		
    	
    	// add local field groups
    	foreach( $json as $field_group ) {
	    	
	    	// add field group
	    	pdc_add_local_field_group( $field_group );
	    	
	    }
	    
	    
    	// loop over keys
    	foreach( $keys as $key ) {
	    	
	    	// vars
	    	$field_group = pdc_get_local_field_group( $key );
	    	
	    	
	    	// attempt get id
	    	$id = pdc_maybe_get( $ids, $key );
	    	
	    	if( $id ) {
		    	
		    	$field_group['ID'] = $id;
		    	
	    	}
	    	
	    	
	    	// append fields
			if( pdc_have_local_fields($key) ) {
				
				$field_group['fields'] = pdc_get_local_fields( $key );
				
			}
			
			
			// import
			$field_group = pdc_import_field_group( $field_group );
			
			
			// append message
			$imported[] = array(
				'ID'		=> $field_group['ID'],
				'title'		=> $field_group['title'],
				'updated'	=> $id ? 1 : 0
			);
			
    	}
    	
    	
    	// messages
    	if( !empty($imported) ) {
    		
    		// vars
    		$links = array();
    		$count = count($imported);
    		$message = sprintf(_n( 'Imported 1 field group', 'Imported %s field groups', $count, 'pdc' ), $count) . '.';
    		
    		
    		// populate links
    		foreach( $imported as $import ) {
	    		
	    		$links[] = '<a href="' . admin_url("post.php?post={$import['ID']}&action=edit") . '" target="_blank">' . $import['title'] . '</a>';
	    			
	    	}
	    	
	    	
	    	// append links
	    	$message .= ' ' . implode(', ', $links);
	    	
	    	
	    	// add notice
	    	pdc_add_admin_notice( $message );
    	
    	}
		
	}
	
	
}

// initialize
pdc_register_admin_tool( 'PDC_Admin_Tool_Import' );

endif; // class_exists check

?>