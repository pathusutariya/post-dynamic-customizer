<?php 

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('PDC_Media') ) :

class PDC_Media {
	
	
	/*
	*  __construct
	*
	*  Initialize filters, action, variables and includes
	*
	*  @type	function
	*  @date	23/06/12
	*  @since	5.0.0
	*
	*  @param	N/A
	*  @return	N/A
	*/
	
	function __construct() {
		
		// actions
		add_action('pdc/enqueue_scripts',			array($this, 'enqueue_scripts'));
		add_action('pdc/save_post', 				array($this, 'save_files'), 5, 1);
		
		
		// filters
		add_filter('wp_handle_upload_prefilter', 	array($this, 'handle_upload_prefilter'), 10, 1);
		
		
		// ajax
		add_action('wp_ajax_query-attachments',		array($this, 'wp_ajax_query_attachments'), -1);
	}
	
	
	/**
	*  enqueue_scripts
	*
	*  Localizes data
	*
	*  @date	27/4/18
	*  @since	5.6.9
	*
	*  @param	void
	*  @return	void
	*/
	
	function enqueue_scripts(){
		
		// localize
		pdc_localize_data(array(
			'mimeTypeIcon'	=> wp_mime_type_icon(),
			'mimeTypes'		=> get_allowed_mime_types()
		));
	}
		
		
	/*
	*  handle_upload_prefilter
	*
	*  description
	*
	*  @type	function
	*  @date	16/02/2015
	*  @since	5.1.5
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function handle_upload_prefilter( $file ) {
		
		// bail early if no pdc field
		if( empty($_POST['_pdcuploader']) ) {
			return $file;
		}
		
		
		// load field
		$field = pdc_get_field( $_POST['_pdcuploader'] );
		if( !$field ) {
			return $file;
		}
		
		
		// get errors
		$errors = pdc_validate_attachment( $file, $field, 'upload' );
		
		
		/**
		*  Filters the errors for a file before it is uploaded to WordPress.
		*
		*  @date	16/02/2015
		*  @since	5.1.5
		*
		*  @param	array $errors An array of errors.
		*  @param	array $file An array of data for a single file.
		*  @param	array $field The field array.
		*/
		$errors = apply_filters( "pdc/upload_prefilter/type={$field['type']}",	$errors, $file, $field );
		$errors = apply_filters( "pdc/upload_prefilter/name={$field['_name']}",	$errors, $file, $field );
		$errors = apply_filters( "pdc/upload_prefilter/key={$field['key']}", 	$errors, $file, $field );
		$errors = apply_filters( "pdc/upload_prefilter", 						$errors, $file, $field );
		
		
		// append error
		if( !empty($errors) ) {
			$file['error'] = implode("\n", $errors);
		}
		
		
		// return
		return $file;
	}

	
	/*
	*  save_files
	*
	*  This function will save the $_FILES data
	*
	*  @type	function
	*  @date	24/10/2014
	*  @since	5.0.9
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function save_files( $post_id = 0 ) {
		
		// bail early if no $_FILES data
		if( empty($_FILES['pdc']['name']) ) {
			return;
		}
		
		
		// upload files
		pdc_upload_files();
	}
	
	
	/*
	*  wp_ajax_query_attachments
	*
	*  description
	*
	*  @type	function
	*  @date	26/06/2015
	*  @since	5.2.3
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function wp_ajax_query_attachments() {
		
		add_filter('wp_prepare_attachment_for_js', 	array($this, 'wp_prepare_attachment_for_js'), 10, 3);
		
	}
	
	function wp_prepare_attachment_for_js( $response, $attachment, $meta ) {
		
		// append attribute
		$response['pdc_errors'] = false;
		
		
		// bail early if no pdc field
		if( empty($_POST['query']['_pdcuploader']) ) {
			return $response;
		}
		
		
		// load field
		$field = pdc_get_field( $_POST['query']['_pdcuploader'] );
		if( !$field ) {
			return $response;
		}
		
		
		// get errors
		$errors = pdc_validate_attachment( $response, $field, 'prepare' );
		
		
		// append errors
		if( !empty($errors) ) {
			$response['pdc_errors'] = implode('<br />', $errors);
		}
		
		
		// return
		return $response;
	}
}

// instantiate
pdc_new_instance('PDC_Media');

endif; // class_exists check

?>