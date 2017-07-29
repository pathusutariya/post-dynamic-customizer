<?php

/*
*  pdc Admin Field Group Class
*
*  All the logic for editing a field group
*
*  @class 		pdc_admin_field_group
*  @package		pdc
*  @subpackage	Admin
*/

if( ! class_exists('pdc_admin_field_group') ) :

class pdc_admin_field_group {
	
	
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
		add_action('current_screen',									array($this, 'current_screen'));
		add_action('save_post',											array($this, 'save_post'), 10, 2);
		
		
		// ajax
		add_action('wp_ajax_pdc/field_group/render_field_settings',		array($this, 'ajax_render_field_settings'));
		add_action('wp_ajax_pdc/field_group/render_location_value',		array($this, 'ajax_render_location_value'));
		add_action('wp_ajax_pdc/field_group/move_field',				array($this, 'ajax_move_field'));
		
		
		// filters
		add_filter('post_updated_messages',								array($this, 'post_updated_messages'));
		
	}
	
	
	/*
	*  post_updated_messages
	*
	*  This function will customize the message shown when editing a field group
	*
	*  @type	action (post_updated_messages)
	*  @date	30/04/2014
	*  @since	5.0.0
	*
	*  @param	$messages (array)
	*  @return	$messages
	*/
	
	function post_updated_messages( $messages ) {
		
		// append to messages
		$messages['pdc-field-group'] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => __('Field group updated.', 'pdc'),
			2 => __('Field group updated.', 'pdc'),
			3 => __('Field group deleted.', 'pdc'),
			4 => __('Field group updated.', 'pdc'),
			5 => false, // field group does not support revisions
			6 => __('Field group published.', 'pdc'),
			7 => __('Field group saved.', 'pdc'),
			8 => __('Field group submitted.', 'pdc'),
			9 => __('Field group scheduled for.', 'pdc'),
			10 => __('Field group draft updated.', 'pdc')
		);
		
		
		// return
		return $messages;
	}
	
	
	/*
	*  current_screen
	*
	*  This function is fired when loading the admin page before HTML has been rendered.
	*
	*  @type	action (current_screen)
	*  @date	21/07/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function current_screen() {
		
		// validate screen
		if( !pdc_is_screen('pdc-field-group') ) return;
		
		
		// disable filters to ensure pdc loads raw data from DB
		pdc_disable_filters();
		
		
		// enqueue scripts
		pdc_enqueue_scripts();
		
		
		// actions
		add_action('pdc/input/admin_enqueue_scripts',		array($this, 'admin_enqueue_scripts'));
		add_action('pdc/input/admin_head', 					array($this, 'admin_head'));
		add_action('pdc/input/form_data', 					array($this, 'form_data'));
		add_action('pdc/input/admin_footer', 				array($this, 'admin_footer'));
		add_action('pdc/input/admin_footer_js',				array($this, 'admin_footer_js'));
		
		
		// filters
		add_filter('pdc/input/admin_l10n',					array($this, 'admin_l10n'));
		
	}
	
	
	/*
	*  admin_enqueue_scripts
	*
	*  This action is run after post query but before any admin script / head actions. 
	*  It is a good place to register all actions.
	*
	*  @type	action (admin_enqueue_scripts)
	*  @date	30/06/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function admin_enqueue_scripts() {
		
		// no autosave
		wp_dequeue_script('autosave');
		
		
		// custom scripts
		wp_enqueue_style('pdc-field-group');
		wp_enqueue_script('pdc-field-group');
		
		
		// 3rd party hook
		do_action('pdc/field_group/admin_enqueue_scripts');
		
	}
	
	
	/*
	*  admin_head
	*
	*  This function will setup all functionality for the field group edit page to work
	*
	*  @type	action (admin_head)
	*  @date	23/06/12
	*  @since	3.1.8
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function admin_head() {
		
		// global
		global $post, $field_group;
		
		
		// set global var
		$field_group = pdc_get_field_group( $post );
		
		
		// metaboxes
		add_meta_box('pdc-field-group-fields', __("Fields",'pdc'), array($this, 'mb_fields'), 'pdc-field-group', 'normal', 'high');
		add_meta_box('pdc-field-group-locations', __("Location",'pdc'), array($this, 'mb_locations'), 'pdc-field-group', 'normal', 'high');
		add_meta_box('pdc-field-group-options', __("Settings",'pdc'), array($this, 'mb_options'), 'pdc-field-group', 'normal', 'high');
		
		
		// actions
		add_action('post_submitbox_misc_actions',	array($this, 'post_submitbox_misc_actions'), 10, 0);
		add_action('edit_form_after_title',			array($this, 'edit_form_after_title'), 10, 0);
		
		
		// filters
		add_filter('screen_settings',				array($this, 'screen_settings'), 10, 1);
		
		
		// 3rd party hook
		do_action('pdc/field_group/admin_head');
		
	}
	
	
	/*
	*  edit_form_after_title
	*
	*  This action will allow pdc to render metaboxes after the title
	*
	*  @type	action
	*  @date	17/08/13
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function edit_form_after_title() {
		
		// globals
		global $post;
		
		
		// render post data
		pdc_form_data(array( 
			'post_id'	=> $post->ID, 
			'nonce'		=> 'field_group',
			'ajax'		=> 0
		));

	}
	
	
	/*
	*  form_data
	*
	*  This function will add extra HTML to the pdc form data element
	*
	*  @type	function
	*  @date	31/05/2016
	*  @since	5.3.8
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function form_data( $args ) {
		
		// add extra inputs
		?>
		<input type="hidden" name="_pdc_delete_fields" value="0" id="input-delete-fields" />
		<?php
		
		
		// do action	
		do_action('pdc/field_group/form_data', $args);
		
	}
	
	
	/*
	*  admin_l10n
	*
	*  This function will append extra l10n strings to the pdc JS object
	*
	*  @type	function
	*  @date	31/05/2016
	*  @since	5.3.8
	*
	*  @param	$l10n (array)
	*  @return	$l10n
	*/
	
	function admin_l10n( $l10n ) {
		
		// merge in new strings
		$l10n = array_merge($l10n, array(
			'move_to_trash'			=> __("Move to trash. Are you sure?",'pdc'),
			'checked'				=> __("checked",'pdc'),
			'no_fields'				=> __("No toggle fields available",'pdc'),
			'title_is_required'		=> __("Field group title is required",'pdc'),
			'copy'					=> __("copy",'pdc'),
			'or'					=> __("or",'pdc'),
			'fields'				=> __("Fields",'pdc'),
			'parent_fields'			=> __("Parent fields",'pdc'),
			'sibling_fields'		=> __("Sibling fields",'pdc'),
			'move_field'			=> __("Move Custom Field",'pdc'),
			'move_field_warning'	=> __("This field cannot be moved until its changes have been saved",'pdc'),
			'null'					=> __("Null",'pdc'),
			'unload'				=> __('The changes you made will be lost if you navigate away from this page','pdc'),
			'field_name_start'		=> __('The string "field_" may not be used at the start of a field name','pdc'),
		));
		
		
		// 3rd party hook
		$l10n = apply_filters('pdc/field_group/admin_l10n', $l10n);
		
		
		// return
		return $l10n;
		
	}
	
	
	
	/*
	*  admin_footer
	*
	*  description
	*
	*  @type	function
	*  @date	11/01/2016
	*  @since	5.3.2
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function admin_footer() {
		
		// 3rd party hook
		do_action('pdc/field_group/admin_footer');
		
	}
	
	
	/*
	*  admin_footer_js
	*
	*  description
	*
	*  @type	function
	*  @date	31/05/2016
	*  @since	5.3.8
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function admin_footer_js() {
		
		// 3rd party hook
		do_action('pdc/field_group/admin_footer_js');
		
	}
	
	
	/*
	*  screen_settings
	*
	*  description
	*
	*  @type	function
	*  @date	26/01/13
	*  @since	3.6.0
	*
	*  @param	$current (string)
	*  @return	$current
	*/
	
	function screen_settings( $html ) {
		
		// vars
		$checked = pdc_get_user_setting('show_field_keys') ? 'checked="checked"' : '';
		
		
		// append
	    $html .= '<div id="pdc-append-show-on-screen" class="pdc-hidden">';
	    $html .= '<label for="pdc-field-key-hide"><input id="pdc-field-key-hide" type="checkbox" value="1" name="show_field_keys" ' . $checked . ' /> ' . __('Field Keys','pdc') . '</label>';
		$html .= '</div>';
	    
	    
	    // return
	    return $html;
	    
	}
	
	
	/*
	*  post_submitbox_misc_actions
	*
	*  This function will customize the publish metabox
	*
	*  @type	function
	*  @date	17/07/2015
	*  @since	5.2.9
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function post_submitbox_misc_actions() {
		
		// global
		global $field_group;
		
		
		// vars
		$status = $field_group['active'] ? __("Active",'pdc') : __("Inactive",'pdc');
		
?>
<script type="text/javascript">
(function($) {
	
	// modify status
	$('#post-status-display').html('<?php echo $status; ?>');
	
	
	// remove edit links
	$('#misc-publishing-actions a').remove();
	
	
	// remove editables (fixes status text changing on submit)
	$('#misc-publishing-actions .hide-if-js').remove();
	
})(jQuery);	
</script>
<?php	
		
	}
	
	
	/*
	*  save_post
	*
	*  This function will save all the field group data
	*
	*  @type	function
	*  @date	23/06/12
	*  @since	1.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function save_post( $post_id, $post ) {
		
		// do not save if this is an auto save routine
		if( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
		
			return $post_id;
			
		}
		
		
		// bail early if not pdc-field-group
		if( $post->post_type !== 'pdc-field-group' ) {
			
			return $post_id;
			
		}
		
		
		// only save once! WordPress save's a revision as well.
		if( wp_is_post_revision($post_id) ) {
		
	    	return $post_id;
	    	
        }
        
        
		// verify nonce
		if( !pdc_verify_nonce('field_group') ) {
		
			return $post_id;
			
		}
        
        
        // disable filters to ensure pdc loads raw data from DB
		pdc_disable_filters();
		
		
        // save fields
		if( !empty($_POST['pdc_fields']) ) {
			
			foreach( $_POST['pdc_fields'] as $field ) {
				
				// vars
				$specific = false;
				$save = pdc_extract_var( $field, 'save' );
				
				
				// only saved field if has changed
				if( $save == 'meta' ) {
				
					$specific = array(
						'menu_order',
						'post_parent',
					);
					
				}
				
				
				// set field parent
				if( empty($field['parent']) ) {
					
					$field['parent'] = $post_id;
					
				}
				
				
				// save field
				pdc_update_field( $field, $specific );
				
			}
			
		}
		
		
		// delete fields
        if( $_POST['_pdc_delete_fields'] ) {
        	
        	// clean
	    	$ids = explode('|', $_POST['_pdc_delete_fields']);
	    	$ids = array_map( 'intval', $ids );
	    	
	    	
	    	// loop
			foreach( $ids as $id ) {
				
				// bai early if no id
				if( !$id ) continue;
				
				
				// delete
				pdc_delete_field( $id );
				
			}
			
        }
		
		
		// add args
        $_POST['pdc_field_group']['ID'] = $post_id;
        $_POST['pdc_field_group']['title'] = $_POST['post_title'];
        
        
		// save field group
        pdc_update_field_group( $_POST['pdc_field_group'] );
		
		
        // return
        return $post_id;
	}
	
	
	/*
	*  mb_fields
	*
	*  This function will render the HTML for the medtabox 'pdc-field-group-fields'
	*
	*  @type	function
	*  @date	28/09/13
	*  @since	5.0.0
	*
	*  @param	N/A
	*  @return	N/A
	*/
	
	function mb_fields() {
		
		// global
		global $field_group;
		
		
		// get fields
		$view = array(
			'fields'	=> pdc_get_fields_by_id( $field_group['ID'] ),
			'parent'	=> 0
		);
		
		
		// load view
		pdc_get_view('field-group-fields', $view);
		
	}
	
	
	/*
	*  mb_options
	*
	*  This function will render the HTML for the medtabox 'pdc-field-group-options'
	*
	*  @type	function
	*  @date	28/09/13
	*  @since	5.0.0
	*
	*  @param	N/A
	*  @return	N/A
	*/
	
	function mb_options() {
		
		// global
		global $field_group;
		
		
		// field key (leave in for compatibility)
		if( !pdc_is_field_group_key( $field_group['key']) ) {
			
			$field_group['key'] = uniqid('group_');
			
		}
		
		
		// don't use view because we need access to $this context
		include( pdc_get_path('admin/views/field-group-options.php') );
		
	}
	
	
	/*
	*  mb_locations
	*
	*  This function will render the HTML for the medtabox 'pdc-field-group-locations'
	*
	*  @type	function
	*  @date	28/09/13
	*  @since	5.0.0
	*
	*  @param	N/A
	*  @return	N/A
	*/
	
	function mb_locations() {
		
		// global
		global $field_group;
		
		
		// UI needs at lease 1 location rule
		if( empty($field_group['location']) ) {
			
			$field_group['location'] = array(
				
				// group 0
				array(
					
					// rule 0
					array(
						'param'		=>	'post_type',
						'operator'	=>	'==',
						'value'		=>	'post',
					)
				)
				
			);
		}
		
		
		// don't use view because we need access to $this context
		include( pdc_get_path('admin/views/field-group-locations.php') );
		
	}
	
	
	/*
	*  render_location_value
	*
	*  This function will render out an input containing location rule values for the given args
	*
	*  @type	function
	*  @date	30/09/13
	*  @since	5.0.0
	*
	*  @param	$options (array)
	*  @return	N/A
	*/
	
	function render_location_value( $options ) {
		
		// vars
		$options = wp_parse_args( $options, array(
			'group_id'	=> 0,
			'rule_id'	=> 0,
			'value'		=> null,
			'param'		=> null,
		));
		
		
		// vars
		$choices = array();
		
		
		// some case's have the same outcome
		if( $options['param'] == "page_parent" ) {
		
			$options['param'] = "page";
			
		}

		
		switch( $options['param'] ) {
			
			
			/*
			*  Post
			*/
			
			case "post_type" :
			
				// get post types
				// - removed show_ui to allow 3rd party code to register a post type using a custom admin edit page
				$post_types = pdc_get_post_types(array(
					//'show_ui'	=> 1, 
					'exclude'	=> array('attachment')
				));
				
				
				// get choices
				$choices = pdc_get_pretty_post_types( $post_types );
				
				
				// end
				break;

				
			case "post" :
				
				// get post types
				$post_types = pdc_get_post_types(array(
					'exclude'	=> array('page', 'attachment')
				));
				
				
				// get posts grouped by post type
				$groups = pdc_get_grouped_posts(array(
					'post_type' => $post_types
				));
				
				
				if( !empty($groups) ) {
			
					foreach( array_keys($groups) as $group_title ) {
						
						// vars
						$posts = pdc_extract_var( $groups, $group_title );
						
						
						// override post data
						foreach( array_keys($posts) as $post_id ) {
							
							// update
							$posts[ $post_id ] = pdc_get_post_title( $posts[ $post_id ] );
							
						};
						
						
						// append to $choices
						$choices[ $group_title ] = $posts;
						
					}
					
				}
				
				break;
			
			case "post_template" :
				
				// vars
				$templates = wp_get_theme()->get_post_templates();
				$default = apply_filters( 'default_page_template_title',  __('Default Template', 'pdc') );
				
				
				// choices
				$choices = array('default' => $default);
				
				
				// templates
				if( !empty($templates) ) {
					
					foreach( $templates as $post_type => $post_type_templates ) {
						
						$choices = array_merge($choices, $post_type_templates);
						
					}
					
				}
				
				
				// break
				break;
				
			case "post_category" :
				
				$terms = pdc_get_taxonomy_terms( 'category' );
				
				if( !empty($terms) ) {
					
					$choices = array_pop($terms);
					
				}
				
				break;
			
			
			case "post_format" :
				
				$choices = get_post_format_strings();
								
				break;
			
			
			case "post_status" :
				
				global $wp_post_statuses;
				
				if( !empty($wp_post_statuses) ) {
					
					foreach( $wp_post_statuses as $status ) {
						
						$choices[ $status->name ] = $status->label;
						
					}
					
				}
								
				break;
			
			
			case "post_taxonomy" :
				
				$choices = pdc_get_taxonomy_terms();
				
				// unset post_format
				if( isset($choices['post_format']) ) {
				
					unset( $choices['post_format']) ;
					
				}
							
				break;
			
			
			/*
			*  Page
			*/
			
			case "page" :
				
				
				// get posts grouped by post type
				$groups = pdc_get_grouped_posts(array(
					'post_type' => 'page'
				));
				
				
				if( !empty($groups) ) {
			
					foreach( array_keys($groups) as $group_title ) {
						
						// vars
						$posts = pdc_extract_var( $groups, $group_title );
						
						
						// override post data
						foreach( array_keys($posts) as $post_id ) {
							
							// update
							$posts[ $post_id ] = pdc_get_post_title( $posts[ $post_id ] );
							
						};
						
						
						// append to $choices
						$choices = $posts;
						
					}
					
				}
				
				
				break;
				
			
			case "page_type" :
				
				$choices = array(
					'front_page'	=>	__("Front Page",'pdc'),
					'posts_page'	=>	__("Posts Page",'pdc'),
					'top_level'		=>	__("Top Level Page (no parent)",'pdc'),
					'parent'		=>	__("Parent Page (has children)",'pdc'),
					'child'			=>	__("Child Page (has parent)",'pdc'),
				);
								
				break;
			
			
			case "page_parent" :
				
				// refer to "page"
				
				break;
			
			
			case "page_template" :
				
				// vars
				$templates = wp_get_theme()->get_page_templates();
				$default = apply_filters( 'default_page_template_title',  __('Default Template', 'pdc') );
				
				
				// merge
				$choices = array_merge(array('default' => $default), $templates);
				
				break;
				
			
			/*
			*  User
			*/
			
			case "current_user" :
				
				// viewing
				$choices = array(
					'logged_in'		=> __('Logged in', 'pdc'),
					'viewing_front'	=> __('Viewing front end', 'pdc'),
					'viewing_back'	=> __('Viewing back end', 'pdc')
				);
								
				break;
			
			case "current_user_role" :
				
				// global
				global $wp_roles;
				
				
				// specific roles
				$choices = $wp_roles->get_names();
				
				
				// multi-site
				if( is_multisite() ) {
					
					$choices = array_merge(array(
						'super_admin' => __('Super Admin', 'pdc')
					), $choices);
					
				}
				
				break;
			
			case "user_role" :
				
				global $wp_roles;
				
				$choices = array_merge( array('all' => __('All', 'pdc')), $wp_roles->get_names() );
			
				break;
				
				
			case "user_form" :
				
				$choices = array(
					'all' 		=> __('All', 'pdc'),
					'edit' 		=> __('Add / Edit', 'pdc'),
					'register' 	=> __('Register', 'pdc')
				);
			
				break;
				
			
			/*
			*  Forms
			*/
			
			case "attachment" :
				
				$choices = array('all' => __('All', 'pdc'));
			
				break;
				
				
			case "taxonomy" :
				
				$choices = array_merge( array('all' => __('All', 'pdc')), pdc_get_taxonomies() );
				
								
				// unset post_format
				if( isset($choices['post_format']) ) {
				
					unset( $choices['post_format']);
					
				}
							
				break;
				
				
			case "comment" :
				
				// vars
				$choices = array(
					'all'	=> __('All', 'pdc')
				);
				
				
				// append post types
				$choices = array_merge( $choices, pdc_get_pretty_post_types() );
				
				
				// end
				break;
			
			
			case "widget" :
				
				global $wp_widget_factory;
				
				$choices = array(
					'all' 		=> __('All', 'pdc'),
				);
				
				
				if( !empty( $wp_widget_factory->widgets ) ) {
					
					foreach( $wp_widget_factory->widgets as $widget ) {
					
						$choices[ $widget->id_base ] = $widget->name;
						
					}
					
				}
								
				break;
		}
		
		
		// allow custom location rules
		$choices = apply_filters( 'pdc/location/rule_values/' . $options['param'], $choices );
							
		
		// create field
		pdc_render_field(array(
			'type'		=> 'select',
			'prefix'	=> "pdc_field_group[location][{$options['group_id']}][{$options['rule_id']}]",
			'name'		=> 'value',
			'value'		=> $options['value'],
			'choices'	=> $choices,
		));
		
	}
	
	
	/*
	*  ajax_render_location_value
	*
	*  This function can be accessed via an AJAX action and will return the result from the render_location_value function
	*
	*  @type	function (ajax)
	*  @date	30/09/13
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function ajax_render_location_value() {
		
		// validate
		if( !pdc_verify_ajax() ) {
		
			die();
			
		}
		
		
		// call function
		$this->render_location_value( $_POST );
		
		
		// die
		die();
								
	}
	
	
	/*
	*  ajax_render_field_settings
	*
	*  This function will return HTML containing the field's settings based on it's new type
	*
	*  @type	function (ajax)
	*  @date	30/09/13
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function ajax_render_field_settings() {
		
		// vars
		$options = array(
			'nonce'			=> '',
			'parent'		=> 0,
			'field_group'	=> 0,
			'prefix'		=> '',
			'type'			=> '',
		);
		
		
		// load post options
		$options = wp_parse_args($_POST, $options);
		
		
		// verify nonce
		if( !wp_verify_nonce($options['nonce'], 'pdc_nonce') ) {
		
			die(0);
			
		}
		
		
		// required
		if( !$options['type'] ) {
		
			die(0);
			
		}
		
				
		// render options
		$field = pdc_get_valid_field(array(
			'type'			=> $options['type'],
			'name'			=> 'temp',
			'prefix'		=> $options['prefix'],
			'parent'		=> $options['parent'],
			'field_group'	=> $options['field_group'],
		));
		
		
		// render
		do_action("pdc/render_field_settings/type={$field['type']}", $field);
		
		
		// die
		die();
								
	}
	
	/*
	*  ajax_move_field
	*
	*  description
	*
	*  @type	function
	*  @date	20/01/2014
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function ajax_move_field() {
		
		// disable filters to ensure pdc loads raw data from DB
		pdc_disable_filters();
		
		
		$args = pdc_parse_args($_POST, array(
			'nonce'				=> '',
			'post_id'			=> 0,
			'field_id'			=> 0,
			'field_group_id'	=> 0
		));
		
		
		// verify nonce
		if( !wp_verify_nonce($args['nonce'], 'pdc_nonce') ) die();
		
		
		// confirm?
		if( $args['field_id'] && $args['field_group_id'] ) {
			
			// vars 
			$field = pdc_get_field($args['field_id']);
			$field_group = pdc_get_field_group($args['field_group_id']);
			
			
			// update parent
			$field['parent'] = $field_group['ID'];
			
			
			// remove conditional logic
			$field['conditional_logic'] = 0;
			
			
			// update field
			pdc_update_field($field);
			
			$v1 = $field['label'];
			$v2 = '<a href="' . admin_url("post.php?post={$field_group['ID']}&action=edit") . '" target="_blank">' . $field_group['title'] . '</a>';
			
			echo '<p><strong>' . __('Move Complete.', 'pdc') . '</strong></p>';
			echo '<p>' . sprintf( __('The %s field can now be found in the %s field group', 'pdc'), $v1, $v2 ). '</p>';
			
			echo '<a href="#" class="button button-primary pdc-close-popup">' . __("Close Window",'pdc') . '</a>';
			
			die();
			
		}
		
		
		// get all field groups
		$field_groups = pdc_get_field_groups();
		$choices = array();
		
		
		// check
		if( !empty($field_groups) ) {
			
			// loop
			foreach( $field_groups as $field_group ) {
				
				// bail early if no ID
				if( !$field_group['ID'] ) continue;
				
				
				// bail ealry if is current
				if( $field_group['ID'] == $args['post_id'] ) continue;
				
				
				// append
				$choices[ $field_group['ID'] ] = $field_group['title'];
				
			}
			
		}
		
		
		// render options
		$field = pdc_get_valid_field(array(
			'type'		=> 'select',
			'name'		=> 'pdc_field_group',
			'choices'	=> $choices
		));
		
		
		echo '<p>' . __('Please select the destination for this field', 'pdc') . '</p>';
		
		echo '<form id="pdc-move-field-form">';
		
			// render
			pdc_render_field_wrap( $field );
			
			echo '<button type="submit" class="button button-primary">' . __("Move Field",'pdc') . '</button>';
			
		echo '</form>';
		
		
		// die
		die();
		
	}
	
}

// initialize
new pdc_admin_field_group();

endif;

?>