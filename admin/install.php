<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('pdc_admin_install') ) :

class pdc_admin_install {
	
	// vars
	var $db_updates = array(
		'5.0.0' => 'pdc_update_500',
		'5.5.0' => 'pdc_update_550'
	);
	
	
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
		add_action('admin_menu', array($this,'admin_menu'), 20);
		add_action('wp_upgrade', array($this,'wp_upgrade'), 10, 2);
		
		
		// ajax
		add_action('wp_ajax_pdc/admin/db_update', array($this, 'ajax_db_update'));
		
	}
	
	
	/*
	*  admin_menu
	*
	*  This function will chck for available updates and add actions if needed
	*
	*  @type	function
	*  @date	19/02/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function admin_menu() {
		
		// vars
		$updates = pdc_get_db_updates();
		
		
		// bail early if no updates available
		if( !$updates ) return;
		
		
		// actions
		add_action('admin_notices', array($this, 'admin_notices'), 1);
		
		
		// add page
		$page = add_submenu_page('index.php', __('Upgrade Database','pdc'), __('Upgrade Database','pdc'), pdc_get_setting('capability'), 'pdc-upgrade', array($this,'html') );
		
		
		// actions
		add_action('load-' . $page, array($this,'load'));
		
	}
	
	
	/*
	*  load
	*
	*  This function will look at the $_POST data and run any functions if needed
	*
	*  @type	function
	*  @date	7/01/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function load() {
		
		// hide upgrade 
		remove_action('admin_notices', array($this, 'admin_notices'), 1);
		
		
		// load pdc scripts
		pdc_enqueue_scripts();
		
	}
	
	
	/*
	*  admin_notices
	*
	*  This function will render the DB Upgrade notice
	*
	*  @type	function
	*  @date	17/10/13
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function admin_notices() {
		
		// view
		$view = array(
			'button_text'	=> __("Upgrade Database", 'pdc'),
			'button_url'	=> admin_url('index.php?page=pdc-upgrade')
		);
		
		
		// load view
		pdc_get_view('install-notice', $view);
		
	}
	
	
	/*
	*  html
	*
	*  description
	*
	*  @type	function
	*  @date	19/02/2014
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function html() {
		
		// view
		$view = array(
			'updates'			=> pdc_get_db_updates(),
			'plugin_version'	=> pdc_get_setting('version')
		);
		
		
		// load view
		pdc_get_view('install', $view);
		
	}
	
	
	/*
	*  ajax_db_update
	*
	*  description
	*
	*  @type	function
	*  @date	24/10/13
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function ajax_db_update() {
		
   		// options
   		$options = wp_parse_args( $_POST, array(
			'nonce'		=> '',
			'blog_id'	=> '',
		));
		
		
		// validate
		if( !wp_verify_nonce($options['nonce'], 'pdc_db_update') ) {
		
			wp_send_json_error(array(
				'message' => __('Error validating request', 'pdc')
			));	
			
		}
		
		
		// switch blog
		if( $options['blog_id'] ) { 
			
			switch_to_blog( $options['blog_id'] );
			
		}
		
		
		// vars
		$updates = pdc_get_db_updates();
		$message = '';
		
		
		// bail early if no updates
		if( empty($updates) ) {
			
			wp_send_json_error(array(
				'message' => __('No updates available.', 'pdc')
			));	
			
		}
		
		
		// install updates
		foreach( $updates as $version => $callback ) {
			
			$message .= $this->run_update( $callback );
		
		}
		
		
		// updates complete
		pdc_update_db_version();
		
		
		// return
		wp_send_json_success(array(
			'message' => $message
		));
		
	}
	
	
	/*
	*  run_db_update
	*
	*  This function will perform a db upgrade
	*
	*  @type	function
	*  @date	10/09/2016
	*  @since	5.4.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function run_update( $callback = '' ) {
		
		// include update functions
		pdc_include('admin/install-updates.php');
	
		
		// bail early if not found
		if( !function_exists($callback) ) return false;
			
			
		// load any errors / feedback from update
		ob_start();
		
		
		// include
		call_user_func($callback);
		
		
		// get feedback
		$message = ob_get_clean();
		
		
		// return
		return $message;
		
	}
	
	
	/*
	*  wp_upgrade
	*
	*  This function will run when the WP database is updated
	*
	*  @type	function
	*  @date	10/09/2016
	*  @since	5.4.0
	*
	*  @param	$wp_db_version (string) The new $wp_db_version
	*  @return	$wp_current_db_version (string) The old (current) $wp_db_version
	*/
	
	function wp_upgrade( $wp_db_version, $wp_current_db_version ) {
		
		// vars
		$pdc_db_version = pdc_get_db_version();
		
		
		// termmeta was added in WP 4.4 (34370)
		// if website has already updated to pdc 5.5.0, termmeta will not have yet been migrated
		if( $wp_db_version >= 34370 && $wp_current_db_version < 34370 && pdc_version_compare($pdc_db_version, '>=', '5.5.0') ) {
			
			$this->run_update('pdc_update_550_termmeta');
							
		}
		
	}
		
}


// initialize
pdc()->admin->install = new pdc_admin_install();

endif; // class_exists check


/*
*  pdc_get_db_version
*
*  This function will return the current pdc DB version
*
*  @type	function
*  @date	10/09/2016
*  @since	5.4.0
*
*  @param	$post_id (int)
*  @return	$post_id (int)
*/

function pdc_get_db_version() {
	
	return get_option('pdc_version');
	
}


/*
*  pdc_update_db_version
*
*  This function will update the current pdc DB version
*
*  @type	function
*  @date	10/09/2016
*  @since	5.4.0
*
*  @param	$post_id (int)
*  @return	$post_id (int)
*/

function pdc_update_db_version( $version = '' ) {
	
	// default to latest
	if( !$version ) {
		
		$version = pdc_get_setting('version');
		
	}
	
	
	// update
	update_option('pdc_version', $version );
	
}


/*
*  pdc_get_db_updates
*
*  This function will return available db updates
*
*  @type	function
*  @date	12/05/2014
*  @since	5.0.0
*
*  @param	$post_id (int)
*  @return	$post_id (int)
*/

function pdc_get_db_updates() {
	
	// vars
	$available = array();
	$db_updates = pdc()->admin->install->db_updates;
	$pdc_version = pdc_get_setting('version');
	$db_version = pdc_get_db_version();
	
	
	// bail early if is fresh install
	if( !$db_version ) {
	
		pdc_update_db_version($pdc_version);
		return false;
		
	}
	
	
	// bail early if is up to date
	if( pdc_version_compare($db_version, '>=', $pdc_version)) return false;
	
	
	// loop
	foreach( $db_updates as $version => $callback ) {
		
		// ignore if update is for a future version (may exist for testing)
		if( pdc_version_compare( $version, '>', $pdc_version ) ) continue;
		
		
		// ignore if update has already been run
		if( pdc_version_compare( $version, '<=', $db_version ) ) continue;
		
		
    	// append
        $available[ $version ] = $callback;
		
	}
	    
    
	// bail early if no updates available
	// - also update DB to current version
    if( empty($available) ) {
	
		pdc_update_db_version($pdc_version);
		return false;
		
	}
    
    
    // return
    return $available;
	
}

?>