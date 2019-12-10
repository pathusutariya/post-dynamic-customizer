<?php

/*
*  PDC Attachment Form Class
*
*  All the logic for adding fields to attachments
*
*  @class 		pdc_form_attachment
*  @package		PDC
*  @subpackage	Forms
*/

if( ! class_exists('pdc_form_attachment') ) :

class pdc_form_attachment {
	
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
	
	function __construct() {
		
		// actions
		add_action('admin_enqueue_scripts',			array($this, 'admin_enqueue_scripts'));
		
		
		// render
		add_filter('attachment_fields_to_edit', 	array($this, 'edit_attachment'), 10, 2);
		
		
		// save
		add_filter('attachment_fields_to_save', 	array($this, 'save_attachment'), 10, 2);
		
	}
	
	
	/*
	*  admin_enqueue_scripts
	*
	*  This action is run after post query but before any admin script / head actions. 
	*  It is a good place to register all actions.
	*
	*  @type	action (admin_enqueue_scripts)
	*  @date	26/01/13
	*  @since	3.6.0
	*
	*  @param	N/A
	*  @return	N/A
	*/
	
	function admin_enqueue_scripts() {
		
		// bail early if not valid screen
		if( !pdc_is_screen(array('attachment', 'upload')) ) {
			return;
		}
				
		// load pdc scripts
		pdc_enqueue_scripts(array(
			'uploader'	=> true,
		));
		
		// actions
		if( pdc_is_screen('upload') ) {
			add_action('admin_footer', array($this, 'admin_footer'), 0);
		}
	}
	
	
	/*
	*  admin_footer
	*
	*  This function will add pdc_form_data to the WP 4.0 attachment grid
	*
	*  @type	action (admin_footer)
	*  @date	11/09/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function admin_footer() {
		
		// render post data
		pdc_form_data(array( 
			'screen'	=> 'attachment',
			'post_id'	=> 0,
		));
		
?>
<script type="text/javascript">
	
// WP saves attachment on any input change, so unload is not needed
pdc.unload.active = 0;

</script>
<?php
		
	}
	
	
	/*
	*  edit_attachment
	*
	*  description
	*
	*  @type	function
	*  @date	8/10/13
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function edit_attachment( $form_fields, $post ) {
		
		// vars
		$is_page = pdc_is_screen('attachment');
		$post_id = $post->ID;
		$el = 'tr';
		$args = array(
			'attachment' => $post_id
		);
		
		
		// get field groups
		$field_groups = pdc_get_field_groups( $args );
		
		
		// render
		if( !empty($field_groups) ) {
			
			// get pdc_form_data
			ob_start();
			
			
			pdc_form_data(array( 
				'screen'	=> 'attachment',
				'post_id'	=> $post_id,
			));
			
			
			// open
			echo '</td></tr>';
			
			
			// loop
			foreach( $field_groups as $field_group ) {
				
				// load fields
				$fields = pdc_get_fields( $field_group );
				
				
				// override instruction placement for modal
				if( !$is_page ) {
					
					$field_group['instruction_placement'] = 'field';
				}
				
				
				// render			
				pdc_render_fields( $fields, $post_id, $el, $field_group['instruction_placement'] );
				
			}
			
			
			// close
			echo '<tr class="compat-field-pdc-blank"><td>';
			
			
			
			$html = ob_get_contents();
			
			
			ob_end_clean();
			
			
			$form_fields[ 'pdc-form-data' ] = array(
	       		'label' => '',
	   			'input' => 'html',
	   			'html' => $html
			);
						
		}
		
		
		// return
		return $form_fields;
		
	}
	
	
	/*
	*  save_attachment
	*
	*  description
	*
	*  @type	function
	*  @date	8/10/13
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function save_attachment( $post, $attachment ) {
		
		// bail early if not valid nonce
		if( !pdc_verify_nonce('attachment') ) {
			return $post;
		}
		
		// bypass validation for ajax
		if( pdc_is_ajax('save-attachment-compat') ) {
			pdc_save_post( $post['ID'] );
		
		// validate and save
		} elseif( pdc_validate_save_post(true) ) {
			pdc_save_post( $post['ID'] );
		}
		
		// return
		return $post;	
	}
	
			
}

new pdc_form_attachment();

endif;

?>