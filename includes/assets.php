<?php 

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('PDC_Assets') ) :

class PDC_Assets {
	
	/** @var array Storage for translations */
	var $text = array();
	
	/** @var array Storage for data */
	var $data = array();
	
	
	/**
	*  __construct
	*
	*  description
	*
	*  @date	10/4/18
	*  @since	5.6.9
	*
	*  @param	void
	*  @return	void
	*/
		
	function __construct() {
		
		// actions
		add_action('init',	array($this, 'register_scripts'));
	}
	
	
	/**
	*  add_text
	*
	*  description
	*
	*  @date	13/4/18
	*  @since	5.6.9
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	function add_text( $text ) {
		foreach( (array) $text as $k => $v ) {
			$this->text[ $k ] = $v;
		}
	}
	
	
	/**
	*  add_data
	*
	*  description
	*
	*  @date	13/4/18
	*  @since	5.6.9
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	function add_data( $data ) {
		foreach( (array) $data as $k => $v ) {
			$this->data[ $k ] = $v;
		}
	}
	
	
	/**
	*  register_scripts
	*
	*  description
	*
	*  @date	13/4/18
	*  @since	5.6.9
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	function register_scripts() {
		
		// vars
		$version = pdc_get_setting('version');
		$min = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
		
		// scripts
		wp_register_script('pdc-input', pdc_get_url("assets/js/pdc-input{$min}.js"), array('jquery', 'jquery-ui-sortable', 'jquery-ui-resizable'), $version );
		wp_register_script('pdc-field-group', pdc_get_url("assets/js/pdc-field-group{$min}.js"), array('pdc-input'), $version );
		
		// styles
		wp_register_style('pdc-global', pdc_get_url('assets/css/pdc-global.css'), array(), $version );
		wp_register_style('pdc-input', pdc_get_url('assets/css/pdc-input.css'), array('pdc-global'), $version );
		wp_register_style('pdc-field-group', pdc_get_url('assets/css/pdc-field-group.css'), array('pdc-input'), $version );
		
		// action
		do_action('pdc/register_scripts', $version, $min);
	}
	
	
	/**
	*  enqueue_scripts
	*
	*  Enqueue scripts for input
	*
	*  @date	13/4/18
	*  @since	5.6.9
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	function enqueue_scripts( $args = array() ) {
		
		// run only once
		if( pdc_has_done('enqueue_scripts') ) {
			return;
		}
		
		// defaults
		$args = wp_parse_args($args, array(
			
			// force tinymce editor to be enqueued
			'uploader'			=> false,
			
			// priority used for action callbacks, defaults to 20 which runs after defaults
			'priority'			=> 20,
			
			// action prefix 
			'context'			=> is_admin() ? 'admin' : 'wp'
		));
		
		// define actions
		$actions = array(
			'admin_enqueue_scripts'			=> $args['context'] . '_enqueue_scripts',
			'admin_print_scripts'			=> $args['context'] . '_print_scripts',
			'admin_head'					=> $args['context'] . '_head',
			'admin_footer'					=> $args['context'] . '_footer',
			'admin_print_footer_scripts'	=> $args['context'] . '_print_footer_scripts',
		);
		
		// fix customizer actions where head and footer are not available
		if( $args['context'] == 'customize_controls' ) {
			$actions['admin_head'] = $actions['admin_print_scripts'];
			$actions['admin_footer'] = $actions['admin_print_footer_scripts'];
		}
		
		// add actions
		foreach( $actions as $function => $action ) {
			pdc_maybe_add_action( $action, array($this, $function), $args['priority'] );
		}
		
		// enqueue uploader
		// WP requires a lot of JS + inline scripes to create the media modal and should be avoioded when possible.
		// - priority must be less than 10 to allow WP to enqueue
		if( $args['uploader'] ) {
			add_action($actions['admin_footer'], 'pdc_enqueue_uploader', 5);
		}
		
		// enqueue
		wp_enqueue_script('pdc-input');
		wp_enqueue_style('pdc-input');
		
		// localize text
		pdc_localize_text(array(
			
			// unload
			'The changes you made will be lost if you navigate away from this page'	=> __('The changes you made will be lost if you navigate away from this page', 'pdc'),
			
			// media
			'Select.verb'			=> _x('Select', 'verb', 'pdc'),
			'Edit.verb'				=> _x('Edit', 'verb', 'pdc'),
			'Update.verb'			=> _x('Update', 'verb', 'pdc'),
			'Uploaded to this post'	=> __('Uploaded to this post', 'pdc'),
			'Expand Details' 		=> __('Expand Details', 'pdc'),
			'Collapse Details' 		=> __('Collapse Details', 'pdc'),
			'Restricted'			=> __('Restricted', 'pdc'),
			'All images'			=> __('All images', 'pdc'),
			
			// validation
			'Validation successful'			=> __('Validation successful', 'pdc'),
			'Validation failed'				=> __('Validation failed', 'pdc'),
			'1 field requires attention'	=> __('1 field requires attention', 'pdc'),
			'%d fields require attention'	=> __('%d fields require attention', 'pdc'),
			
			// tooltip
			'Are you sure?'			=> __('Are you sure?','pdc'),
			'Yes'					=> __('Yes','pdc'),
			'No'					=> __('No','pdc'),
			'Remove'				=> __('Remove','pdc'),
			'Cancel'				=> __('Cancel','pdc'),
			
			// conditions
			'Has any value'				=> __('Has any value', 'pdc'),
			'Has no value'				=> __('Has no value', 'pdc'),
			'Value is equal to'			=> __('Value is equal to', 'pdc'),
			'Value is not equal to'		=> __('Value is not equal to', 'pdc'),
			'Value matches pattern'		=> __('Value matches pattern', 'pdc'),
			'Value contains'			=> __('Value contains', 'pdc'),
			'Value is greater than'		=> __('Value is greater than', 'pdc'),
			'Value is less than'		=> __('Value is less than', 'pdc'),
			'Selection is greater than'	=> __('Selection is greater than', 'pdc'),
			'Selection is less than'	=> __('Selection is less than', 'pdc'),
		));
		
		// action
		do_action('pdc/enqueue_scripts');
	}
	
	
	/**
	*  admin_enqueue_scripts
	*
	*  description
	*
	*  @date	16/4/18
	*  @since	5.6.9
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	function admin_enqueue_scripts() {
		
		// vars
		$text = array();
		
		// actions
		do_action('pdc/admin_enqueue_scripts');
		do_action('pdc/input/admin_enqueue_scripts');
		
		// only include translated strings
		foreach( $this->text as $k => $v ) {
			if( str_replace('.verb', '', $k) !== $v ) {
				$text[ $k ] = $v;
			}
		}
		
		// localize text
		if( $text ) {
			wp_localize_script( 'pdc-input', 'pdcL10n', $text );
		}
	}
	
	
	/**
	*  admin_print_scripts
	*
	*  description
	*
	*  @date	18/4/18
	*  @since	5.6.9
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	function admin_print_scripts() {
		do_action('pdc/admin_print_scripts');
	}
	
	
	/**
	*  admin_head
	*
	*  description
	*
	*  @date	16/4/18
	*  @since	5.6.9
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	function admin_head() {

		// actions
		do_action('pdc/admin_head');
		do_action('pdc/input/admin_head');
	}
	
	
	/**
	*  admin_footer
	*
	*  description
	*
	*  @date	16/4/18
	*  @since	5.6.9
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	function admin_footer() {
		
		// global
		global $wp_version;
		
		// get data
		$data = wp_parse_args($this->data, array(
			'screen'		=> pdc_get_form_data('screen'),
			'post_id'		=> pdc_get_form_data('post_id'),
			'nonce'			=> wp_create_nonce( 'pdc_nonce' ),
			'admin_url'		=> admin_url(),
			'ajaxurl'		=> admin_url( 'admin-ajax.php' ),
			'validation'	=> pdc_get_form_data('validation'),
			'wp_version'	=> $wp_version,
			'pdc_version'	=> pdc_get_setting('version'),
			'browser'		=> pdc_get_browser(),
			'locale'		=> pdc_get_locale(),
			'rtl'			=> is_rtl()
		));
		
		// get l10n (old)
		$l10n = apply_filters( 'pdc/input/admin_l10n', array() );
		
		// todo: force 'pdc-input' script enqueue if not yet included
		// - fixes potential timing issue if pdc_enqueue_assest() was called during body
		
		// localize data
		?>
<script type="text/javascript">
pdc.data = <?php echo wp_json_encode($data); ?>;
pdc.l10n = <?php echo wp_json_encode($l10n); ?>;
</script>
<?php 
		
		// actions
		do_action('pdc/admin_footer');
		do_action('pdc/input/admin_footer');
		
		// trigger prepare
		?>
<script type="text/javascript">
pdc.doAction('prepare');
</script>
<?php
	
	}
	
	
	/**
	*  admin_print_footer_scripts
	*
	*  description
	*
	*  @date	18/4/18
	*  @since	5.6.9
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	function admin_print_footer_scripts() {
		do_action('pdc/admin_print_footer_scripts');
	}
	
	/*
	*  enqueue_uploader
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
	
	function enqueue_uploader() {
		
		// run only once
		if( pdc_has_done('enqueue_uploader') ) {
			return;
		}
		
		// bail early if doing ajax
		if( pdc_is_ajax() ) {
			return;
		}
		
		// enqueue media if user can upload
		if( current_user_can('upload_files') ) {
			wp_enqueue_media();
		}
		
		// create dummy editor
		?>
		<div id="pdc-hidden-wp-editor" class="pdc-hidden">
			<?php wp_editor( '', 'pdc_content' ); ?>
		</div>
		<?php
			
		// action
		do_action('pdc/enqueue_uploader');
	}
}

// instantiate
pdc_new_instance('PDC_Assets');

endif; // class_exists check


/**
*  pdc_localize_text
*
*  description
*
*  @date	13/4/18
*  @since	5.6.9
*
*  @param	type $var Description. Default.
*  @return	type Description.
*/

function pdc_localize_text( $text ) {
	return pdc_get_instance('PDC_Assets')->add_text( $text );
}


/**
*  pdc_localize_data
*
*  description
*
*  @date	13/4/18
*  @since	5.6.9
*
*  @param	type $var Description. Default.
*  @return	type Description.
*/

function pdc_localize_data( $data ) {
	return pdc_get_instance('PDC_Assets')->add_data( $data );
}


/*
*  pdc_enqueue_scripts
*
*  
*
*  @type	function
*  @date	6/10/13
*  @since	5.0.0
*
*  @param	n/a
*  @return	n/a
*/

function pdc_enqueue_scripts( $args = array() ) {
	return pdc_get_instance('PDC_Assets')->enqueue_scripts( $args );
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
	return pdc_get_instance('PDC_Assets')->enqueue_uploader();
}

?>