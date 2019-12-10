<?php 

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('pdc_pro_updates') ) :

class pdc_pro_updates {
	

	/*
	*  __construct
	*
	*  Initialize filters, action, variables and includes
	*
	*  @type	function
	*  @date	23/06/12
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function __construct() {
		
		// actions
		add_action('init',	array($this, 'init'), 20);
		
	}
	
	
	/*
	*  init
	*
	*  description
	*
	*  @type	function
	*  @date	10/4/17
	*  @since	5.5.10
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function init() {
		
		// bail early if no show_updates
		if( !pdc_get_setting('show_updates') ) return;
		
		
		// bail early if not a plugin (included in theme)
		if( !pdc_is_plugin_active() ) return;
		
		
		// register update
		pdc_register_plugin_update(array(
			'id'		=> 'pro',
			'key'		=> pdc_pro_get_license_key(),
			'slug'		=> pdc_get_setting('slug'),
			'basename'	=> pdc_get_setting('basename'),
			'version'	=> pdc_get_setting('version'),
		));
		
		
		// admin
		if( is_admin() ) {
			
			add_action('in_plugin_update_message-' . pdc_get_setting('basename'), array($this, 'modify_plugin_update_message'), 10, 2 );
			
		}
		
		
	}
	
	
	/*
	*  modify_plugin_update_message
	*
	*  Displays an update message for plugin list screens.
	*
	*  @type	function
	*  @date	14/06/2016
	*  @since	5.3.8
	*
	*  @param	$message (string)
	*  @param	$plugin_data (array)
	*  @param	$r (object)
	*  @return	$message
	*/
	
	function modify_plugin_update_message( $plugin_data, $response ) {
		
		// bail ealry if has key
		if( pdc_pro_get_license_key() ) return;
		
		
		// display message
		echo '<br />' . sprintf( __('To enable updates, please enter your license key on the <a href="%s">Updates</a> page. If you don\'t have a licence key, please see <a href="%s">details & pricing</a>.', 'pdc'), admin_url('edit.php?post_type=pdc-field-group&page=pdc-settings-updates'), 'https://www.advancedcustomfields.com/pro' );
		
	}
	
}


// initialize
new pdc_pro_updates();

endif; // class_exists check


/*
*  pdc_pro_get_license
*
*  This function will return the license
*
*  @type	function
*  @date	20/09/2016
*  @since	5.4.0
*
*  @param	n/a
*  @return	n/a
*/

function pdc_pro_get_license() {
	
	// get option
	$license = get_option('pdc_pro_license');
	
	
	// bail early if no value
	if( !$license ) return false;
	
	
	// decode
	$license = maybe_unserialize(base64_decode($license));
	
	
	// bail early if corrupt
	if( !is_array($license) ) return false;
	
	
	// return
	return $license;
	
}


/*
*  pdc_pro_get_license_key
*
*  This function will return the license key
*
*  @type	function
*  @date	20/09/2016
*  @since	5.4.0
*
*  @param	n/a
*  @return	n/a
*/

function pdc_pro_get_license_key() {
	
	// vars
	$license = pdc_pro_get_license();
	$home_url = home_url();
	
	
	// bail early if empty
	if( !$license || !$license['key'] ) return false;
	
	
	// bail early if url has changed
	if( pdc_strip_protocol($license['url']) !== pdc_strip_protocol($home_url) ) return false;
	
	
	// return
	return $license['key'];
	
}


/*
*  pdc_pro_update_license
*
*  This function will update the DB license
*
*  @type	function
*  @date	20/09/2016
*  @since	5.4.0
*
*  @param	$key (string)
*  @return	n/a
*/

function pdc_pro_update_license( $key = '' ) {
	
	// vars
	$value = '';
	
	
	// key
	if( $key ) {
		
		// vars
		$data = array(
			'key'	=> $key,
			'url'	=> home_url()
		);
		
		
		// encode
		$value = base64_encode(maybe_serialize($data));
		
	}
	
	
	// re-register update (key has changed)
	pdc_register_plugin_update(array(
		'id'		=> 'pro',
		'key'		=> $key,
		'slug'		=> pdc_get_setting('slug'),
		'basename'	=> pdc_get_setting('basename'),
		'version'	=> pdc_get_setting('version'),
	));
	
	
	// update
	return update_option('pdc_pro_license', $value);
	
}

?>