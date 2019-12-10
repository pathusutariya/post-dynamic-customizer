<?php

/*
*  PDC Widget Form Class
*
*  All the logic for adding fields to widgets
*
*  @class 		pdc_form_widget
*  @package		PDC
*  @subpackage	Forms
*/

if( ! class_exists('pdc_form_widget') ) :

class pdc_form_widget {
	
	
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
		$this->preview_values = array();
		$this->preview_reference = array();
		$this->preview_errors = array();
		
		
		// actions
		add_action('admin_enqueue_scripts',		array($this, 'admin_enqueue_scripts'));
		add_action('in_widget_form', 			array($this, 'edit_widget'), 10, 3);
		add_action('pdc/validate_save_post',	array($this, 'pdc_validate_save_post'), 5);
		
		
		// filters
		add_filter('widget_update_callback', 	array($this, 'save_widget'), 10, 4);
		
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
		
		// validate screen
		if( pdc_is_screen('widgets') || pdc_is_screen('customize') ) {
		
			// valid
			
		} else {
			
			return;
			
		}
		
		
		// load pdc scripts
		pdc_enqueue_scripts();
		
		
		// actions
		add_action('pdc/input/admin_footer', array($this, 'admin_footer'), 1);

	}
	
	
	/*
	*  pdc_validate_save_post
	*
	*  This function will loop over $_POST data and validate
	*
	*  @type	action 'pdc/validate_save_post' 5
	*  @date	7/09/2016
	*  @since	5.4.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function pdc_validate_save_post() {
		
		// bail ealry if not widget
		if( !isset($_POST['_pdc_widget_id']) ) return;
		
		
		// vars
		$id = $_POST['_pdc_widget_id'];
		$number = $_POST['_pdc_widget_number'];
		$prefix = $_POST['_pdc_widget_prefix'];
		
		
		// validate
		pdc_validate_values( $_POST[ $id ][ $number ]['pdc'], $prefix );
				
	}
	
	
	/*
	*  edit_widget
	*
	*  This function will render the fields for a widget form
	*
	*  @type	function
	*  @date	11/06/2014
	*  @since	5.0.0
	*
	*  @param	$widget (object)
	*  @param	$return (null)
	*  @param	$instance (object)
	*  @return	$post_id (int)
	*/
	
	function edit_widget( $widget, $return, $instance ) {
		
		// vars
		$post_id = 0;
		$prefix = 'widget-' . $widget->id_base . '[' . $widget->number . '][pdc]';
		
		
		// get id
		if( $widget->number !== '__i__' ) {
		
			$post_id = "widget_{$widget->id}";
			
		}
		
		
		// get field groups
		$field_groups = pdc_get_field_groups(array(
			'widget' => $widget->id_base
		));
		
		
		// render
		if( !empty($field_groups) ) {
			
			// render post data
			pdc_form_data(array( 
				'screen'		=> 'widget',
				'post_id'		=> $post_id,
				'widget_id'		=> 'widget-' . $widget->id_base,
				'widget_number'	=> $widget->number,
				'widget_prefix'	=> $prefix
			));
			
			
			// wrap
			echo '<div class="pdc-widget-fields pdc-fields -clear">';
			
			// loop
			foreach( $field_groups as $field_group ) {
				
				// load fields
				$fields = pdc_get_fields( $field_group );
				
				
				// bail if not fields
				if( empty($fields) ) continue;
				
				
				// change prefix
				pdc_prefix_fields( $fields, $prefix );
				
				
				// render
				pdc_render_fields( $fields, $post_id, 'div', $field_group['instruction_placement'] );
				
			}
			
			//wrap
			echo '</div>';
			
			
			// jQuery selector looks odd, but is necessary due to WP adding an incremental number into the ID
			// - not possible to find number via PHP parameters
			if( $widget->updated ): ?>
			<script type="text/javascript">
			(function($) {
				
				pdc.doAction('append', $('[id^="widget"][id$="<?php echo $widget->id; ?>"]') );
				
			})(jQuery);	
			</script>
			<?php endif;
				
		}
		
	}
	
	
	/*
	*  save_widget
	*
	*  This function will hook into the widget update filter and save PDC data
	*
	*  @type	function
	*  @date	27/05/2015
	*  @since	5.2.3
	*
	*  @param	$instance (array) widget settings
	*  @param	$new_instance (array) widget settings
	*  @param	$old_instance (array) widget settings
	*  @param	$widget (object) widget info
	*  @return	$instance
	*/
	
	function save_widget( $instance, $new_instance, $old_instance, $widget ) {
		
		// bail ealry if not valid (!customize + pdc values + nonce)
		if( isset($_POST['wp_customize']) || !isset($new_instance['pdc']) || !pdc_verify_nonce('widget') ) return $instance;
		
		
		// save
		pdc_save_post( "widget_{$widget->id}", $new_instance['pdc'] );
		
		
		// return
		return $instance;
		
	}
	
	
	/*
	*  admin_footer
	*
	*  This function will add some custom HTML to the footer of the edit page
	*
	*  @type	function
	*  @date	11/06/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function admin_footer() {
?>
<script type="text/javascript">
(function($) {
	
	// vars
	pdc.set('post_id', 'widgets');
	
	// Only initialize visible fields.
	pdc.addFilter('find_fields', function( $fields ){
		
		// not templates
		$fields = $fields.not('#available-widgets .pdc-field');
		
		// not widget dragging in
		$fields = $fields.not('.widget.ui-draggable-dragging .pdc-field');
		
		// return
		return $fields;
	});
	
	// on publish
	$('#widgets-right').on('click', '.widget-control-save', function( e ){
		
		// vars
		var $button = $(this);
		var $form = $button.closest('form');
		
		// validate
		var valid = pdc.validateForm({
			form: $form,
			event: e,
			reset: true
		});
		
		// if not valid, stop event and allow validation to continue
		if( !valid ) {
			e.preventDefault();
			e.stopImmediatePropagation();
		}
	});
	
	// show
	$('#widgets-right').on('click', '.widget-top', function(){
		var $widget = $(this).parent();
		if( $widget.hasClass('open') ) {
			pdc.doAction('hide', $widget);
		} else {
			pdc.doAction('show', $widget);
		}
	});
	
	$(document).on('widget-added', function( e, $widget ){
		
		// - use delay to avoid rendering issues with customizer (ensures div is visible)
		setTimeout(function(){
			pdc.doAction('append', $widget );
		}, 100);
	});
	
})(jQuery);	
</script>
<?php
		
	}
}

new pdc_form_widget();

endif;

?>
