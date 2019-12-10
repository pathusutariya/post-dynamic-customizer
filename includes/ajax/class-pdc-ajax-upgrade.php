<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('PDC_Ajax_Upgrade') ) :

class PDC_Ajax_Upgrade extends PDC_Ajax {
	
	/** @var string The AJAX action name */
	var $action = 'pdc/ajax/upgrade';
	
	/**
	*  get_response
	*
	*  The actual logic for this AJAX request.
	*
	*  @date	31/7/18
	*  @since	5.7.2
	*
	*  @param	void
	*  @return	mixed The response data to send back or WP_Error.
	*/
	
	function response() {
		
		// switch blog
		if( $this->has('blog_id') ) {
			switch_to_blog( $this->get('blog_id') );
		}
		
		// bail early if no upgrade avaiable
		if( !pdc_has_upgrade() ) {
			return new WP_Error( 'upgrade_error', __('No updates available.', 'pdc') );
		}
		
		// listen for output
		ob_start();
		
		// run upgrades
		pdc_upgrade_all();
		
		// store output
		$error = ob_get_clean();
		
		// return error if output
		if( $error ) {
			return new WP_Error( 'upgrade_error', $error );
		}
		
		// return
		return true;
	}
}

pdc_new_instance('PDC_Ajax_Upgrade');

endif; // class_exists check

?>