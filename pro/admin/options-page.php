<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('pdc_admin_options_page') ) :

class pdc_admin_options_page {
	
	var $page;
	
	
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
		add_action('admin_menu', array($this,'admin_menu'), 99, 0);
		
		
		// filters
		add_filter( 'pdc/location/rule_types', 					array($this, 'rule_types'), 10, 1 );
		add_filter( 'pdc/location/rule_values/options_page',	array($this, 'rule_values'), 10, 1 );
		add_filter( 'pdc/location/rule_match/options_page',		array($this, 'rule_match'), 10, 3 );
		
	}
		
	
	/*
	*  pdc_location_rules_types
	*
	*  this function will add "Options Page" to the pdc location rules
	*
	*  @type	function
	*  @date	2/02/13
	*
	*  @param	{array}	$choices
	*  @return	{array}	$choices
	*/
	
	function rule_types( $choices ) {
		
	    $choices[ __("Forms",'pdc') ]['options_page'] = __("Options Page",'pdc');
		
	    return $choices;
	    
	}
	
	
	/*
	*  pdc_location_rules_values_options_page
	*
	*  this function will populate the available pages in the pdc location rules
	*
	*  @type	function
	*  @date	2/02/13
	*
	*  @param	{array}	$choices
	*  @return	{array}	$choices
	*/
	
	function rule_values( $choices ) {
		
		// vars
		$pages = pdc_get_options_pages();
		
		
		// populate
		if( !empty($pages) ) {
		
			foreach( $pages as $page ) {
			
				$choices[ $page['menu_slug'] ] = $page['menu_title'];
				
			}
			
		} else {
			
			$choices[''] = __('No options pages exist', 'pdc');
			
		}
		
		
		// return
	    return $choices;
	}
	
	
	/*
	*  rule_match
	*
	*  description
	*
	*  @type	function
	*  @date	24/02/2014
	*  @since	5.0.0
	*
	*  @param	
	*  @return	
	*/
	
	function rule_match( $match, $rule, $options ) {
		
		// vars
		$options_page = false;
		
		
		// $options does not contain a default for "options_page"
		if( isset($options['options_page']) ) {
		
			$options_page = $options['options_page'];
			
		}
		

		if( !$options_page ) {
		
			global $plugin_page;
			
			$options_page = $plugin_page;
			
		}
		
		
		// match
		if( $rule['operator'] == "==" ) {
		
        	$match = ( $options_page === $rule['value'] );
        	
        } elseif( $rule['operator'] == "!=" ) {
        
        	$match = ( $options_page !== $rule['value'] );
        	
        }
        
        
        // return
        return $match;
        
    }
    
	
	/*
	*  admin_menu
	*
	*  description
	*
	*  @type	function
	*  @date	24/02/2014
	*  @since	5.0.0
	*
	*  @param	
	*  @return	
	*/
	
	function admin_menu() {
		
		// vars
		$pages = pdc_get_options_pages();
		
		
		// create pages
		if( !empty($pages) ) {
		
			foreach( $pages as $page ) {
				
				// vars
				$slug = '';
				
				
				if( empty($page['parent_slug']) ) {
					
					// add page
					$slug = add_menu_page( $page['page_title'], $page['menu_title'], $page['capability'], $page['menu_slug'], array($this, 'html'), $page['icon_url'], $page['position'] );
					
				} else {
					
					// add page
					$slug = add_submenu_page( $page['parent_slug'], $page['page_title'], $page['menu_title'], $page['capability'], $page['menu_slug'], array($this, 'html') );
					
				}
				
				
				// actions
				add_action("load-{$slug}", array($this,'admin_load'));
			}
			
		}
		
	}
	
	
	/*
	*  load
	*
	*  @description: 
	*  @since: 3.6
	*  @created: 2/02/13
	*/
	
	function admin_load() {
		
		// globals
		global $plugin_page;
		
		
		// vars
		$this->page = pdc_get_options_page($plugin_page);
		
		
		// get post_id (allow lang modification)
		$this->page['post_id'] = pdc_get_valid_post_id($this->page['post_id']);
		
		
		// verify and remove nonce
		if( pdc_verify_nonce('options') ) {
		
			// save data
		    if( pdc_validate_save_post(true) ) {
		    	
		    	// set autoload
		    	pdc_update_setting('autoload', $this->page['autoload']);
		    	
		    	
		    	// save
				pdc_save_post( $this->page['post_id'] );
				
				
				// redirect
				wp_redirect( add_query_arg(array('message' => '1')) );
				exit;
				
			}
			
		}
		
		
		// load pdc scripts
		pdc_enqueue_scripts();
		
		
		// actions
		add_action( 'pdc/input/admin_enqueue_scripts',		array($this,'admin_enqueue_scripts') );
		add_action( 'pdc/input/admin_head',					array($this,'admin_head') );
		
		
		// add columns support
		add_screen_option('layout_columns', array('max'	=> 2, 'default' => 2));
		
	}
	
	
	/*
	*  admin_enqueue_scripts
	*
	*  This function will enqueue the 'post.js' script which adds support for 'Screen Options' column toggle
	*
	*  @type	function
	*  @date	23/03/2016
	*  @since	5.3.2
	*
	*  @param	
	*  @return	
	*/
	
	function admin_enqueue_scripts() {
		
		wp_enqueue_script('post');
		
	}
	
	
	/*
	*  admin_head
	*
	*  This action will find and add field groups to the current edit page
	*
	*  @type	action (admin_head)
	*  @date	23/06/12
	*  @since	3.1.8
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function admin_head() {
		
		// get field groups
		$field_groups = pdc_get_field_groups(array(
			'options_page' => $this->page['menu_slug']
		));
		
		
		// notices
		if( !empty($_GET['message']) && $_GET['message'] == '1' ) {
		
			pdc_add_admin_notice( __("Options Updated",'pdc') );
			
		}
		
		
		// add submit div
		add_meta_box('submitdiv', __('Publish','pdc'), array($this, 'postbox_submitdiv'), 'pdc_options_page', 'side', 'high');
		
		
		
		if( empty($field_groups) ) {
		
			pdc_add_admin_notice( sprintf( __('No Custom Field Groups found for this options page. <a href="%s">Create a Custom Field Group</a>', 'pdc'), admin_url() . 'post-new.php?post_type=pdc-field-group' ), 'error');
		
		} else {
			
			foreach( $field_groups as $i => $field_group ) {
			
				// vars
				$id = "pdc-{$field_group['key']}";
				$title = $field_group['title'];
				$context = $field_group['position'];
				$priority = 'high';
				$args = array( 'field_group' => $field_group );
				
				
				// tweaks to vars
				if( $context == 'pdc_after_title' ) {
					
					$context = 'normal';
					
				} elseif( $context == 'side' ) {
				
					$priority = 'core';
					
				}
				
				
				// filter for 3rd party customization
				$priority = apply_filters('pdc/input/meta_box_priority', $priority, $field_group);
				
				
				// add meta box
				add_meta_box( $id, $title, array($this, 'postbox_pdc'), 'pdc_options_page', $context, $priority, $args );
				
				
			}
			// foreach
			
		}
		// if
		
	}
	
	
	/*
	*  postbox_submitdiv
	*
	*  This function will render the submitdiv metabox
	*
	*  @type	function
	*  @date	23/03/2016
	*  @since	5.3.2
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function postbox_submitdiv( $post, $args ) {
		
		?>
		<div id="major-publishing-actions">

			<div id="publishing-action">
				<span class="spinner"></span>
				<input type="submit" accesskey="p" value="<?php echo $this->page['update_button']; ?>" class="button button-primary button-large" id="publish" name="publish">
			</div>
			
			<div class="clear"></div>
		
		</div>
		<?php
		
	}
	
	
	/*
	*  render_meta_box
	*
	*  description
	*
	*  @type	function
	*  @date	24/02/2014
	*  @since	5.0.0
	*
	*  @param	$post (object)
	*  @param	$args (array)
	*  @return	n/a
	*/
	
	function postbox_pdc( $post, $args ) {
		
		// extract args
		extract( $args ); // all variables from the add_meta_box function
		extract( $args ); // all variables from the args argument
		
		
		// vars
		$o = array(
			'id'			=> $id,
			'key'			=> $field_group['key'],
			'style'			=> $field_group['style'],
			'label'			=> $field_group['label_placement'],
			'edit_url'		=> '',
			'edit_title'	=> __('Edit field group', 'pdc'),
			'visibility'	=> true
		);
		
		
		// edit_url
		if( $field_group['ID'] && pdc_current_user_can_admin() ) {
			
			$o['edit_url'] = admin_url('post.php?post=' . $field_group['ID'] . '&action=edit');
				
		}
		
		
		// load fields
		$fields = pdc_get_fields( $field_group );
		
		
		// render
		pdc_render_fields( $this->page['post_id'], $fields, 'div', $field_group['instruction_placement'] );
		
		
		
?>
<script type="text/javascript">
if( typeof pdc !== 'undefined' ) {
		
	pdc.postbox.render(<?php echo json_encode($o); ?>);	

}
</script>
<?php
		
	}
	
	
	/*
	*  html
	*
	*  @description: 
	*  @since: 2.0.4
	*  @created: 5/12/12
	*/
	
	function html() {
		
		// vars
		$view = array(
			'page'	=> $this->page
		);
		
		
		// load view
		pdc_pro_get_view('options-page', $view);
				
	}
	
	
}

// initialize
new pdc_admin_options_page();

endif; // class_exists check

?>