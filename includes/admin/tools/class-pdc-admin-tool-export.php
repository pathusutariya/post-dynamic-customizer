<?php 

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('PDC_Admin_Tool_Export') ) :

class PDC_Admin_Tool_Export extends PDC_Admin_Tool {
	
	/** @var string View context */
	var $view = '';
	
	
	/** @var array Export data */
	var $json = '';
	
	
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
		$this->name = 'export';
		$this->title = __("Export Field Groups", 'pdc');
    	
    	
    	// active
    	if( $this->is_active() ) {
			$this->title .= ' - ' . __('Generate PHP', 'pdc');
		}
		
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
		
		// vars
		$action = pdc_maybe_get_POST('action');
		
		
		// download
		if( $action === 'download' ) {
			
			$this->submit_download();
		
		// generate	
		} elseif( $action === 'generate' ) {
			
			$this->submit_generate();
			
		}
		
	}
	
	
	/**
	*  submit_download
	*
	*  description
	*
	*  @date	17/10/17
	*  @since	5.6.3
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function submit_download() {
		
		// vars
		$json = $this->get_selected();
		
		
		// validate
		if( $json === false ) {
			return pdc_add_admin_notice( __("No field groups selected", 'pdc') , 'error');
		}
		
		
		// headers
		$file_name = 'pdc-export-' . date('Y-m-d') . '.json';
		header( "Content-Description: File Transfer" );
		header( "Content-Disposition: attachment; filename={$file_name}" );
		header( "Content-Type: application/json; charset=utf-8" );
		
		
		// return
		echo pdc_json_encode( $json );
		die;
		
	}
	
	
	/**
	*  submit_generate
	*
	*  description
	*
	*  @date	17/10/17
	*  @since	5.6.3
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function submit_generate() {
		
		// vars
		$keys = $this->get_selected_keys();
		
		
		// validate
		if( !$keys ) {
			return pdc_add_admin_notice( __("No field groups selected", 'pdc') , 'error');
		}
		
		
		// url
		$url = add_query_arg( 'keys', implode('+', $keys), $this->get_url() );
		
		
		// redirect
		wp_redirect( $url );
		exit;
		
	}
	
	
	/**
	*  load
	*
	*  description
	*
	*  @date	21/10/17
	*  @since	5.6.3
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function load() {
		
		// active
    	if( $this->is_active() ) {
	    	
	    	// get selected keys
	    	$selected = $this->get_selected_keys();
	    	
	    	
	    	// add notice
	    	if( $selected ) {
		    	$count = count($selected);
		    	$message = sprintf( _n( 'Exported 1 field group.', 'Exported %s field groups.', $count, 'pdc' ), $count );
		    	pdc_add_admin_notice( $message );
	    	}
		}

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
		
		// single (generate PHP)
		if( $this->is_active() ) {
			
			$this->html_single();
		
		// archive	
		} else {
			
			$this->html_archive();
			
		}
		
	}
	
	
	/**
	*  html_field_selection
	*
	*  description
	*
	*  @date	24/10/17
	*  @since	5.6.3
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function html_field_selection() {
		
		// vars
		$choices = array();
		$selected = $this->get_selected_keys();
		$field_groups = pdc_get_field_groups();
		
		
		// loop
		if( $field_groups ) {
			foreach( $field_groups as $field_group ) {
				$choices[ $field_group['key'] ] = esc_html( $field_group['title'] );
			}	
		}
		
		
		// render
		pdc_render_field_wrap(array(
			'label'		=> __('Select Field Groups', 'pdc'),
			'type'		=> 'checkbox',
			'name'		=> 'keys',
			'prefix'	=> false,
			'value'		=> $selected,
			'toggle'	=> true,
			'choices'	=> $choices,
		));
		
	}
	
	
	/**
	*  html_panel_selection
	*
	*  description
	*
	*  @date	21/10/17
	*  @since	5.6.3
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function html_panel_selection() {
		
		?>
		<div class="pdc-panel pdc-panel-selection">
			<h3 class="pdc-panel-title"><?php _e('Select Field Groups', 'pdc') ?> <i class="dashicons dashicons-arrow-right"></i></h3>
			<div class="pdc-panel-inside">
				<?php $this->html_field_selection(); ?>
			</div>
		</div>
		<?php
		
	}
	
	
	/**
	*  html_panel_settings
	*
	*  description
	*
	*  @date	21/10/17
	*  @since	5.6.3
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function html_panel_settings() {
		
		?>
		<div class="pdc-panel pdc-panel-settings">
			<h3 class="pdc-panel-title"><?php _e('Settings', 'pdc') ?> <i class="dashicons dashicons-arrow-right"></i></h3>
			<div class="pdc-panel-inside">
				<?php 
			
/*
				pdc_render_field_wrap(array(
					'label'		=> __('Empty settings', 'pdc'),
					'type'		=> 'select',
					'name'		=> 'minimal',
					'prefix'	=> false,
					'value'		=> '',
					'choices'	=> array(
						'all'		=> 'Include all settings',
						'minimal'	=> 'Ignore empty settings'
					)
				));
*/
				
				?>
			</div>
		</div>
		<?php
			
	}
	
	
	/**
	*  html_archive
	*
	*  description
	*
	*  @date	20/10/17
	*  @since	5.6.3
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function html_archive() {
		
		?>
		<p><?php _e('Select the field groups you would like to export and then select your export method. Use the download button to export to a .json file which you can then import to another PDC installation. Use the generate button to export to PHP code which you can place in your theme.', 'pdc'); ?></p>
		<div class="pdc-fields">
			<?php $this->html_field_selection(); ?>
		</div>
		<p class="pdc-submit">
			<button type="submit" name="action" class="button button-primary" value="download"><?php _e('Export File', 'pdc'); ?></button>
			<button type="submit" name="action" class="button" value="generate"><?php _e('Generate PHP', 'pdc'); ?></button>
		</p>
		<?php
		
	}
	
	
	/**
	*  html_single
	*
	*  description
	*
	*  @date	20/10/17
	*  @since	5.6.3
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function html_single() {
		
		?>
		<div class="pdc-postbox-columns">
			<div class="pdc-postbox-main">
				<?php $this->html_generate(); ?>
			</div>
			<div class="pdc-postbox-side">
				<?php $this->html_panel_selection(); ?>
				<p class="pdc-submit">
					<button type="submit" name="action" class="button button-primary" value="generate"><?php _e('Generate PHP', 'pdc'); ?></button>
				</p>
			</div>
		</div>
		<?php
		
	}
	
	
	/**
	*  html_generate
	*
	*  description
	*
	*  @date	17/10/17
	*  @since	5.6.3
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function html_generate() {
		
		// prevent default translation and fake __() within string
		pdc_update_setting('l10n_var_export', true);
		
		
		// vars
		$json = $this->get_selected();
		$str_replace = array(
			"  "			=> "\t",
			"'!!__(!!\'"	=> "__('",
			"!!\', !!\'"	=> "', '",
			"!!\')!!'"		=> "')",
			"array ("		=> "array("
		);
		$preg_replace = array(
			'/([\t\r\n]+?)array/'	=> 'array',
			'/[0-9]+ => array/'		=> 'array'
		);


		?>
		<p><?php _e("The following code can be used to register a local version of the selected field group(s). A local field group can provide many benefits such as faster load times, version control & dynamic fields/settings. Simply copy and paste the following code to your theme's functions.php file or include it within an external file.", 'pdc'); ?></p>
		<textarea id="pdc-export-textarea" readonly="true"><?php
		
		echo "if( function_exists('pdc_add_local_field_group') ):" . "\r\n" . "\r\n";
		
		foreach( $json as $field_group ) {
					
			// code
			$code = var_export($field_group, true);
			
			
			// change double spaces to tabs
			$code = str_replace( array_keys($str_replace), array_values($str_replace), $code );
			
			
			// correctly formats "=> array("
			$code = preg_replace( array_keys($preg_replace), array_values($preg_replace), $code );
			
			
			// esc_textarea
			$code = esc_textarea( $code );
			
			
			// echo
			echo "pdc_add_local_field_group({$code});" . "\r\n" . "\r\n";
		
		}
		
		echo "endif;";
		
		?></textarea>
		<p class="pdc-submit">
			<a class="button" id="pdc-export-copy"><?php _e( 'Copy to clipboard', 'pdc' ); ?></a>
		</p>
		<script type="text/javascript">
		(function($){
			
			// vars
			var $a = $('#pdc-export-copy');
			var $textarea = $('#pdc-export-textarea');
			
			
			// remove $a if 'copy' is not supported
			if( !document.queryCommandSupported('copy') ) {
				return $a.remove();
			}
			
			
			// event
			$a.on('click', function( e ){
				
				// prevent default
				e.preventDefault();
				
				
				// select
				$textarea.get(0).select();
				
				
				// try
				try {
					
					// copy
					var copy = document.execCommand('copy');
					if( !copy ) return;
					
					
					// tooltip
					pdc.newTooltip({
						text: 		"<?php _e('Copied', 'pdc' ); ?>",
						timeout:	250,
						target: 	$(this),
					});
					
				} catch (err) {
					
					// do nothing
					
				}
						
			});
		
		})(jQuery);
		</script>
		<?php
		
	}
	
	
	
	/**
	*  get_selected_keys
	*
	*  This function will return an array of field group keys that have been selected
	*
	*  @date	20/10/17
	*  @since	5.6.3
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function get_selected_keys() {
		
		// check $_POST
		if( $keys = pdc_maybe_get_POST('keys') ) {
			return (array) $keys;
		}
		
		
		// check $_GET
		if( $keys = pdc_maybe_get_GET('keys') ) {
			$keys = str_replace(' ', '+', $keys);
			return explode('+', $keys);
		}
		
		
		// return
		return false;
		
	}
	
	
	/**
	*  get_selected
	*
	*  This function will return the JSON data for given $_POST args
	*
	*  @date	17/10/17
	*  @since	5.6.3
	*
	*  @param	n/a
	*  @return	array
	*/
	
	function get_selected() {
		
		// vars
		$selected = $this->get_selected_keys();
		$json = array();
		
		
		// bail early if no keys
		if( !$selected ) return false;
		
		
		// construct JSON
		foreach( $selected as $key ) {
			
			// load field group
			$field_group = pdc_get_field_group( $key );
			
			
			// validate field group
			if( empty($field_group) ) continue;
			
			
			// load fields
			$field_group['fields'] = pdc_get_fields( $field_group );
	
	
			// prepare for export
			$field_group = pdc_prepare_field_group_for_export( $field_group );
			
			
			// add to json array
			$json[] = $field_group;
			
		}
		
		
		// return
		return $json;
		
	}
}

// initialize
pdc_register_admin_tool( 'PDC_Admin_Tool_Export' );

endif; // class_exists check

?>