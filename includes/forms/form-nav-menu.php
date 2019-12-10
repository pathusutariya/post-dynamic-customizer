<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('pdc_form_nav_menu') ) :

class pdc_form_nav_menu {
	
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
		add_action('admin_enqueue_scripts',		array($this, 'admin_enqueue_scripts'));
		add_action('wp_update_nav_menu',		array($this, 'update_nav_menu'));
		add_action('pdc/validate_save_post',	array($this, 'pdc_validate_save_post'), 5);
		add_action('wp_nav_menu_item_custom_fields',	array($this, 'wp_nav_menu_item_custom_fields'), 10, 5);
		
		// filters
		add_filter('wp_get_nav_menu_items',		array($this, 'wp_get_nav_menu_items'), 10, 3);
		add_filter('wp_edit_nav_menu_walker',	array($this, 'wp_edit_nav_menu_walker'), 10, 2);
		
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
		if( !pdc_is_screen('nav-menus') ) return;
		
		
		// load pdc scripts
		pdc_enqueue_scripts();
		
		
		// actions
		add_action('admin_footer', array($this, 'admin_footer'), 1);

	}
	
	
	/**
	*  wp_nav_menu_item_custom_fields
	*
	*  description
	*
	*  @date	30/7/18
	*  @since	5.6.9
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	function wp_nav_menu_item_custom_fields( $item_id, $item, $depth, $args, $id = '' ) {
		
		// vars
		$prefix = "menu-item-pdc[$item_id]";
		
		// get field groups
		$field_groups = pdc_get_field_groups(array(
			'nav_menu_item' 		=> $item->type,
			'nav_menu_item_id'		=> $item_id,
			'nav_menu_item_depth'	=> $depth
		));
		
		// render
		if( !empty($field_groups) ) {
			
			// open
			echo '<div class="pdc-menu-item-fields pdc-fields -clear">';
			
			// loop
			foreach( $field_groups as $field_group ) {
				
				// load fields
				$fields = pdc_get_fields( $field_group );
				
				// bail if not fields
				if( empty($fields) ) continue;
				
				// change prefix
				pdc_prefix_fields( $fields, $prefix );
				
				// render
				pdc_render_fields( $fields, $item_id, 'div', $field_group['instruction_placement'] );
			}
			
			// close
			echo '</div>';
			
			// Trigger append for newly created menu item (via AJAX)
			if( pdc_is_ajax('add-menu-item') ): ?>
			<script type="text/javascript">
			(function($) {
				pdc.doAction('append', $('#menu-item-settings-<?php echo $item_id; ?>') );
			})(jQuery);
			</script>
			<?php endif;
		}
	}
	
	
	/*
	*  update_nav_menu
	*
	*  description
	*
	*  @type	function
	*  @date	26/5/17
	*  @since	5.6.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function update_nav_menu( $menu_id ) {
		
		// vars
		$post_id = pdc_get_term_post_id( 'nav_menu', $menu_id );
		
		
		// verify and remove nonce
		if( !pdc_verify_nonce('nav_menu') ) return $menu_id;
		
			   
	    // validate and show errors
		pdc_validate_save_post( true );
		
		
	    // save
		pdc_save_post( $post_id );
		
		
		// save nav menu items
		$this->update_nav_menu_items( $menu_id );
		
	}
	
	
	/*
	*  update_nav_menu_items
	*
	*  description
	*
	*  @type	function
	*  @date	26/5/17
	*  @since	5.6.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function update_nav_menu_items( $menu_id ) {
			
		// bail ealry if not set
		if( empty($_POST['menu-item-pdc']) ) return;
		
		
		// loop
		foreach( $_POST['menu-item-pdc'] as $post_id => $values ) {
			
			pdc_save_post( $post_id, $values );
				
		}
			
	}
	
	
	/**
	*  wp_get_nav_menu_items
	*
	*  WordPress does not provide an easy way to find the current menu being edited.
	*  This function listens to when a menu's items are loaded and stores the menu.
	*  Needed on nav-menus.php page for new menu with no items
	*
	*  @date	23/2/18
	*  @since	5.6.9
	*
	*  @param	type $var Description. Default.
	*  @return	type Description.
	*/
	
	function wp_get_nav_menu_items( $items, $menu, $args ) {
		pdc_set_data('nav_menu_id', $menu->term_id);
		return $items;
	}
	
	
	/*
	*  wp_edit_nav_menu_walker
	*
	*  description
	*
	*  @type	function
	*  @date	26/5/17
	*  @since	5.6.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function wp_edit_nav_menu_walker( $class, $menu_id = 0 ) {
		
		// update data (needed for ajax location rules to work)
		pdc_set_data('nav_menu_id', $menu_id);
		
		// include walker
		if( class_exists('Walker_Nav_Menu_Edit') ) {
			pdc_include('includes/walkers/class-pdc-walker-nav-menu-edit.php');
		}
		
		// return
		return 'PDC_Walker_Nav_Menu_Edit';
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
		
		// bail ealry if not set
		if( empty($_POST['menu-item-pdc']) ) return;
		
		
		// loop
		foreach( $_POST['menu-item-pdc'] as $post_id => $values ) {
			
			// vars
			$prefix = 'menu-item-pdc['.$post_id.']';
			
			
			// validate
			pdc_validate_values( $values, $prefix );
				
		}
				
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
		
		// vars
		$nav_menu_id = pdc_get_data('nav_menu_id');
		$post_id = pdc_get_term_post_id( 'nav_menu', $nav_menu_id );
		
		
		// get field groups
		$field_groups = pdc_get_field_groups(array(
			'nav_menu' => $nav_menu_id
		));
		
?>
<div id="tmpl-pdc-menu-settings" style="display: none;">
	<?php
	
	// data (always needed to save nav menu items)
	pdc_form_data(array( 
		'screen'	=> 'nav_menu',
		'post_id'	=> $post_id,
		'ajax'		=> 1
	));
	
	
	// render
	if( !empty($field_groups) ) {
		
		// loop
		foreach( $field_groups as $field_group ) {
			
			$fields = pdc_get_fields( $field_group );
			
			echo '<div class="pdc-menu-settings -'.$field_group['style'].'">';
			
				echo '<h2>' . $field_group['title'] . '</h2>';
			
				echo '<div class="pdc-fields -left -clear">';
			
					pdc_render_fields( $fields, $post_id, 'div', $field_group['instruction_placement'] );
			
				echo '</div>';
			
			echo '</div>';
			
		}
		
	}
	
	?>
</div>
<script type="text/javascript">
(function($) {
	
	// append html
	$('#post-body-content').append( $('#tmpl-pdc-menu-settings').html() );
	
	
	// avoid WP over-writing $_POST data
	// - https://core.trac.wordpress.org/ticket/41502#ticket
	$(document).on('submit', '#update-nav-menu', function() {

		// vars
		var $form = $(this);
		var $input = $('input[name="nav-menu-data"]');
		
		
		// decode json
		var json = $form.serializeArray();
		var json2 = [];
		
		
		// loop
		$.each( json, function( i, pair ) {
			
			// avoid nesting (unlike WP)
			if( pair.name === 'nav-menu-data' ) return;
			
			
			// bail early if is 'pdc[' input
			if( pair.name.indexOf('pdc[') > -1 ) return;
						
			
			// append
			json2.push( pair );
			
		});
		
		
		// update
		$input.val( JSON.stringify(json2) );
		
	});
		
		
})(jQuery);	
</script>
<?php
		
	}
	
}

pdc_new_instance('pdc_form_nav_menu');

endif;

?>