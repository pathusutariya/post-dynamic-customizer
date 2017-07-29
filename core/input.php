<?php 

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('pdc_input') ) :

class pdc_input {
	
	
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
		
		// vars
		$this->admin_enqueue_scripts = 'admin_enqueue_scripts';
		$this->admin_head = 'admin_head';
		$this->admin_footer = 'admin_footer';
		$this->enqueued = false;
		$this->data = array();
		
		
		// actions
		add_action('pdc/save_post', array($this, 'save_post'), 10, 1);
		
	}
	
	
	/*
	*  get_data
	*
	*  This function will return form data
	*
	*  @type	function
	*  @date	4/03/2016
	*  @since	5.3.2
	*
	*  @param	$key (mixed)
	*  @return	(mixed)
	*/
	
	function get_data( $key = false ) {
		
		// vars
		$data = $this->data;
		
		
		// key
		if( $key && isset($data[ $key ]) ) {
			
			$data = $data[ $key ];
			
		}
		
		
		// return
		return $data;
		
	}
	
	
	/*
	*  set_data
	*
	*  This function will se the form data
	*
	*  @type	function
	*  @date	4/03/2016
	*  @since	5.3.2
	*
	*  @param	$data (array)
	*  @return	(array)
	*/
	
	function set_data( $data ) {
		
		// defaults
		$data = pdc_parse_args($data, array(
			'post_id'		=> 0,		// ID of current post
			'nonce'			=> 'post',	// nonce used for $_POST validation
			'validation'	=> 1,		// runs AJAX validation
			'ajax'			=> 0,		// fetches new field groups via AJAX
		));
		
		
		// update
		$this->data = $data;
		
		
		// enqueue uploader if page allows AJAX fields to appear
		if( $data['ajax'] ) {
			
			add_action($this->admin_footer, 'pdc_enqueue_uploader', 1);
			
		}
		
		
		// return 
		return $data;
		
	}
	
	
	/*
	*  enqueue
	*
	*  This function will determin the actions to use for different pages
	*
	*  @type	function
	*  @date	13/01/2016
	*  @since	5.3.2
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function enqueue() {
		
		// bail ealry if already enqueued
		if( $this->enqueued ) return;
		
		
		// update setting
		$this->enqueued = true;
		
		
		// global
		global $pagenow;
		
		
		// determine action hooks
		if( $pagenow == 'customize.php' ) {
			
			$this->admin_head = 'customize_controls_print_scripts';
			$this->admin_footer = 'customize_controls_print_footer_scripts';
			
		} elseif( $pagenow == 'wp-login.php' ) { 
			
			$this->admin_enqueue_scripts = 'login_enqueue_scripts';
			$this->admin_head = 'login_head';
			$this->admin_footer = 'login_footer';
			
		} elseif( !is_admin() ) {
			
			$this->admin_enqueue_scripts = 'wp_enqueue_scripts';
			$this->admin_head = 'wp_head';
			$this->admin_footer = 'wp_footer';
			
		}
		
		
		// actions
		pdc_maybe_add_action($this->admin_enqueue_scripts, 	array($this, 'admin_enqueue_scripts'), 20 );
		pdc_maybe_add_action($this->admin_head, 			array($this, 'admin_head'), 20 );
		pdc_maybe_add_action($this->admin_footer, 			array($this, 'admin_footer'), 20 );
				
	}
	
	
	/*
	*  admin_enqueue_scripts
	*
	*  The pdc input screen admin_enqueue_scripts
	*
	*  @type	function
	*  @date	4/03/2016
	*  @since	5.3.2
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function admin_enqueue_scripts() {
		
		// scripts
		wp_enqueue_script('pdc-input');
		
		
		// styles
		wp_enqueue_style('pdc-input');
		
		
		// do action
		do_action('pdc/input/admin_enqueue_scripts');
		
	}
	
	
	/*
	*  admin_head
	*
	*  The pdc input screen admin_head
	*
	*  @type	function
	*  @date	4/03/2016
	*  @since	5.3.2
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function admin_head() {
		
		// do action
		do_action('pdc/input/admin_head');
		
	}
	
	
	/*
	*  admin_footer
	*
	*  The pdc input screen admin_footer
	*
	*  @type	function
	*  @date	4/03/2016
	*  @since	5.3.2
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function admin_footer() {
		
		// global
		global $wp_version;
		
		
		// options
		$o = array(
			'post_id'		=> pdc_get_form_data('post_id'),
			'nonce'			=> wp_create_nonce( 'pdc_nonce' ),
			'admin_url'		=> admin_url(),
			'ajaxurl'		=> admin_url( 'admin-ajax.php' ),
			'ajax'			=> pdc_get_form_data('ajax'),
			'validation'	=> pdc_get_form_data('validation'),
			'wp_version'	=> $wp_version,
			'pdc_version'	=> pdc_get_setting('version'),
			'browser'		=> pdc_get_browser(),
			'locale'		=> get_locale(),
			'rtl'			=> is_rtl()
		);
		
		
		// l10n
		$l10n = apply_filters( 'pdc/input/admin_l10n', array(
			'unload'				=> __('The changes you made will be lost if you navigate away from this page','pdc'),
			'expand_details' 		=> __('Expand Details','pdc'),
			'collapse_details' 		=> __('Collapse Details','pdc'),
			'validation_successful'	=> __('Validation successful', 'pdc'),
			'validation_failed'		=> __('Validation failed', 'pdc'),
			'validation_failed_1'	=> __('1 field requires attention', 'pdc'),
			'validation_failed_2'	=> __('%d fields require attention', 'pdc'),
			'restricted'			=> __('Restricted','pdc')
		));
		
		
?>
<script type="text/javascript">
var pdc = pdc || null;
if( pdc ) {
	
	pdc.o = <?php echo json_encode($o); ?>;
	pdc.l10n = <?php echo json_encode($l10n); ?>;
	<?php do_action('pdc/input/admin_footer_js'); ?>

}
</script>
<?php

do_action('pdc/input/admin_footer');
	
?>
<script type="text/javascript">
	if( pdc ) pdc.do_action('prepare');
</script>
<?php
		
	}
	
	
	/*
	*  save_post
	*
	*  This function will save the $_POST data
	*
	*  @type	function
	*  @date	24/10/2014
	*  @since	5.0.9
	*
	*  @param	$post_id (int)
	*  @return	n/a
	*/
	
	function save_post( $post_id ) {
		
		// bail early if empty
		if( empty($_POST['pdc']) ) return;
		
		
		// save $_POST data
		foreach( $_POST['pdc'] as $k => $v ) {
			
			// get field
			$field = pdc_get_field( $k );
			
			
			// continue if no field
			if( !$field ) continue;
			
			
			// update
			pdc_update_value( $v, $post_id, $field );
			
		}
	
	}
	
}

// initialize
pdc()->input = new pdc_input();

endif; // class_exists check



/*
*  pdc_enqueue_scripts
*
*  alias of pdc()->form->enqueue()
*
*  @type	function
*  @date	6/10/13
*  @since	5.0.0
*
*  @param	n/a
*  @return	n/a
*/

function pdc_enqueue_scripts() {
	
	return pdc()->input->enqueue();
	
}


/*
*  pdc_get_form_data
*
*  alias of pdc()->form->get_data()
*
*  @type	function
*  @date	6/10/13
*  @since	5.0.0
*
*  @param	n/a
*  @return	n/a
*/

function pdc_get_form_data( $key = false ) {
	
	return pdc()->input->get_data( $key );

}


/*
*  pdc_set_form_data
*
*  alias of pdc()->form->set_data()
*
*  @type	function
*  @date	6/10/13
*  @since	5.0.0
*
*  @param	n/a
*  @return	n/a
*/

function pdc_set_form_data( $data = array() ) {
	
	return pdc()->input->set_data( $data );

}


/*
*  pdc_enqueue_uploader
*
*  This function will render a WP WYSIWYG and enqueue media
*
*  @type	function
*  @date	27/10/2014
*  @since	5.0.9
*
*  @param	n/a
*  @return	n/a
*/

function pdc_enqueue_uploader() {
	
	// bail early if doing ajax
	if( pdc_is_ajax() ) return;
	
	
	// bail ealry if already run
	if( pdc_has_done('enqueue_uploader') ) return;
	
	
	// enqueue media if user can upload
	if( current_user_can('upload_files') ) {
		
		wp_enqueue_media();
		
	}
	
	
	// create dummy editor
	?><div id="pdc-hidden-wp-editor" class="pdc-hidden"><?php wp_editor( '', 'pdc_content' ); ?></div><?php
	
}


/*
*  pdc_form_data
*
*  description
*
*  @type	function
*  @date	15/10/13
*  @since	5.0.0
*
*  @param	$post_id (int)
*  @return	$post_id (int)
*/

function pdc_form_data( $args = array() ) {
	
	// make sure scripts and styles have been included
	// case: front end bbPress edit user
	pdc_enqueue_scripts();
	
	
	// set form data
	$args = pdc_set_form_data( $args );
	
	
	// hidden inputs
	$inputs = array(
		'_pdcnonce'		=> wp_create_nonce($args['nonce']),
		'_pdcchanged'	=> 0
	);
	
	
	// append custom
	foreach( $args as $k => $v ) {
		
		if( substr($k, 0, 4) === '_pdc' ) $inputs[ $k ] = $v;
		
	}
	
	
	?>
	<div id="pdc-form-data" class="pdc-hidden">
		<?php foreach( $inputs as $k => $v ): ?>
		<input type="hidden" name="<?php echo esc_attr($k); ?>" value="<?php echo esc_attr($v); ?>" />
		<?php endforeach; ?>
		<?php do_action('pdc/input/form_data', $args); ?>
	</div>
	<?php
	
}


/*
*  pdc_save_post
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

function pdc_save_post( $post_id = 0 ) {
	
	// bail early if no pdc values
	if( empty($_POST['pdc']) ) return false;
	
	
	// set form data
	pdc_set_form_data(array(
		'post_id'	=> $post_id
	));
	
	
	// hook for 3rd party customization
	do_action('pdc/save_post', $post_id);
	
	
	// return
	return true;

}
