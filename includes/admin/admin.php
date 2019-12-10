<?php 

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('pdc_admin') ) :

class pdc_admin {
	
	// vars
	var $notices = array();
	
	
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
		add_action('admin_menu', 			array($this, 'admin_menu'));
		add_action('admin_enqueue_scripts',	array($this, 'admin_enqueue_scripts'), 0);
		add_action('admin_notices', 		array($this, 'admin_notices'));
        add_filter('plugin_action_links_' . pdc_get_setting('basename'), array($this, 'admin_plugin_action_link'));
	}

    function admin_plugin_action_link($links) {
        $mylinks = array(
            '<a href="' . admin_url('edit.php?post_type=pdc-field-group') . '">FieldsGroup</a>',
            '<a href="' . admin_url('edit.php?post_type=pdc-field-group&page=pdc-tools') . '">Export Import</a>',
        );
        return array_merge($links, $mylinks);
    }

	/*
	*  add_notice
	*
	*  This function will add the notice data to a setting in the pdc object for the admin_notices action to use
	*
	*  @type	function
	*  @date	17/10/13
	*  @since	5.0.0
	*
	*  @param	$text (string)
	*  @param	$class (string)
	*  @param	wrap (string)
	*  @return	n/a
	*/
	
	function add_notice( $text = '', $class = '', $wrap = 'p' ) {
		
		// append
		$this->notices[] = array(
			'text'	=> $text,
			'class'	=> 'updated ' . $class,
			'wrap'	=> $wrap
		);
		
	}
	
	
	/*
	*  get_notices
	*
	*  This function will return an array of admin notices
	*
	*  @type	function
	*  @date	17/10/13
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	(array)
	*/
	
	function get_notices() {
		
		// bail early if no notices
		if( empty($this->notices) ) return false;
		
		
		// return
		return $this->notices;
		
	}
	
	
	/*
	*  admin_menu
	*
	*  This function will add the PDC menu item to the WP admin
	*
	*  @type	action (admin_menu)
	*  @date	28/09/13
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function admin_menu() {
		
		// bail early if no show_admin
		if( !pdc_get_setting('show_admin') ) return;
		
		
		// vars
		$slug = 'edit.php?post_type=pdc-field-group';
		$cap = pdc_get_setting('capability');
		
		
		// add parent
		//add_menu_page(__("Custom Fields",'pdc'), __("PDC Fields",'pdc'), $cap, $slug, false, 'dashicons-welcome-widgets-menus', '80.025');
		
		
		// add children
		add_submenu_page($slug, __('Field Groups','pdc'), __('Field Groups','pdc'), $cap, $slug );
		add_submenu_page($slug, __('Add New','pdc'), __('Add New','pdc'), $cap, 'post-new.php?post_type=pdc-field-group' );
		
	}
	
	
	/*
	*  admin_enqueue_scripts
	*
	*  This function will add the already registered css
	*
	*  @type	function
	*  @date	28/09/13
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function admin_enqueue_scripts() {
		
		wp_enqueue_style( 'pdc-global' );
		
	}
	
	
	/*
	*  admin_notices
	*
	*  This function will render any admin notices
	*
	*  @type	function
	*  @date	17/10/13
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function admin_notices() {
		
		// vars
		$notices = $this->get_notices();
		
		
		// bail early if no notices
		if( !$notices ) return;
		
		
		// loop
		foreach( $notices as $notice ) {
			
			$open = '';
			$close = '';
				
			if( $notice['wrap'] ) {
				
				$open = "<{$notice['wrap']}>";
				$close = "</{$notice['wrap']}>";
				
			}
				
			?>
			<div class="pdc-admin-notice notice is-dismissible <?php echo esc_attr($notice['class']); ?>"><?php echo $open . $notice['text'] . $close; ?></div>
			<?php
				
		}
		
	}
	
}

// initialize
pdc()->admin = new pdc_admin();

endif; // class_exists check


/*
*  pdc_add_admin_notice
*
*  This function will add the notice data to a setting in the pdc object for the admin_notices action to use
*
*  @type	function
*  @date	17/10/13
*  @since	5.0.0
*
*  @param	$text (string)
*  @param	$class (string)
*  @return	(int) message ID (array position)
*/

function pdc_add_admin_notice( $text, $class = '', $wrap = 'p' ) {
	
	return pdc()->admin->add_notice($text, $class, $wrap);
	
}


/*
*  pdc_get_admin_notices
*
*  This function will return an array containing any admin notices
*
*  @type	function
*  @date	17/10/13
*  @since	5.0.0
*
*  @param	n/a
*  @return	(array)
*/

function pdc_get_admin_notices() {
	
	return pdc()->admin->get_notices();
	
}

?>