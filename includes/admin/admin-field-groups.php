<?php

/*
*  PDC Admin Field Groups Class
*
*  All the logic for editing a list of field groups
*
*  @class 		pdc_admin_field_groups
*  @package		PDC
*  @subpackage	Admin
*/

if( ! class_exists('pdc_admin_field_groups') ) :

class pdc_admin_field_groups {
	
	// vars
	var $url = 'edit.php?post_type=pdc-field-group',
		$sync = array();
		
	
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
		add_action('current_screen',		array($this, 'current_screen'));
		add_action('trashed_post',			array($this, 'trashed_post'));
		add_action('untrashed_post',		array($this, 'untrashed_post'));
		add_action('deleted_post',			array($this, 'deleted_post'));
		
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
		if( !pdc_is_screen('edit-pdc-field-group') ) {
		
			return;
			
		}
		

		// customize post_status
		global $wp_post_statuses;
		
		
		// modify publish post status
		$wp_post_statuses['publish']->label_count = _n_noop( 'Active <span class="count">(%s)</span>', 'Active <span class="count">(%s)</span>', 'pdc' );
		
		
		// reorder trash to end
		$wp_post_statuses['trash'] = pdc_extract_var( $wp_post_statuses, 'trash' );

		
		// check stuff
		$this->check_duplicate();
		$this->check_sync();
		
		
		// actions
		add_action('admin_enqueue_scripts',							array($this, 'admin_enqueue_scripts'));
		add_action('admin_footer',									array($this, 'admin_footer'));
		
		
		// columns
		add_filter('manage_edit-pdc-field-group_columns',			array($this, 'field_group_columns'), 10, 1);
		add_action('manage_pdc-field-group_posts_custom_column',	array($this, 'field_group_columns_html'), 10, 2);
		
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
		
		wp_enqueue_script('pdc-input');
		
	}
	
	
	/*
	*  check_duplicate
	*
	*  This function will check for any $_GET data to duplicate
	*
	*  @type	function
	*  @date	17/10/13
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function check_duplicate() {
		
		// message
		if( $ids = pdc_maybe_get_GET('pdcduplicatecomplete') ) {
			
			// explode
			$ids = explode(',', $ids);
			$total = count($ids);
			
			if( $total == 1 ) {
				
				pdc_add_admin_notice( sprintf(__('Field group duplicated. %s', 'pdc'), '<a href="' . get_edit_post_link($ids[0]) . '">' . get_the_title($ids[0]) . '</a>') );
				
			} else {
				
				pdc_add_admin_notice( sprintf(_n( '%s field group duplicated.', '%s field groups duplicated.', $total, 'pdc' ), $total) );
				
			}
			
		}
		
		
		// vars
		$ids = array();
		
		
		// check single
		if( $id = pdc_maybe_get_GET('pdcduplicate') ) {
			
			$ids[] = $id;
		
		// check multiple
		} elseif( pdc_maybe_get_GET('action2') === 'pdcduplicate' ) {
			
			$ids = pdc_maybe_get_GET('post');
			
		}
		
		
		// sync
		if( !empty($ids) ) {
			
			// validate
			check_admin_referer('bulk-posts');
			
			
			// vars
			$new_ids = array();
			
			
			// loop
			foreach( $ids as $id ) {
				
				// duplicate
				$field_group = pdc_duplicate_field_group( $id );
				
				
				// increase counter
				$new_ids[] = $field_group['ID'];
				
			}
			
			
			// redirect
			wp_redirect( admin_url( $this->url . '&pdcduplicatecomplete=' . implode(',', $new_ids)) );
			exit;
				
		}
		
	}
	
	
	/*
	*  check_sync
	*
	*  This function will check for any $_GET data to sync
	*
	*  @type	function
	*  @date	9/12/2014
	*  @since	5.1.5
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function check_sync() {
		
		// message
		if( $ids = pdc_maybe_get_GET('pdcsynccomplete') ) {
			
			// explode
			$ids = explode(',', $ids);
			$total = count($ids);
			
			if( $total == 1 ) {
				
				pdc_add_admin_notice( sprintf(__('Field group synchronised. %s', 'pdc'), '<a href="' . get_edit_post_link($ids[0]) . '">' . get_the_title($ids[0]) . '</a>') );
				
			} else {
				
				pdc_add_admin_notice( sprintf(_n( '%s field group synchronised.', '%s field groups synchronised.', $total, 'pdc' ), $total) );
				
			}
			
		}
		
		
		// vars
		$groups = pdc_get_field_groups();
		
		
		// bail early if no field groups
		if( empty($groups) ) return;
		
		
		// find JSON field groups which have not yet been imported
		foreach( $groups as $group ) {
			
			// vars
			$local = pdc_maybe_get($group, 'local', false);
			$modified = pdc_maybe_get($group, 'modified', 0);
			$private = pdc_maybe_get($group, 'private', false);
			
			
			// ignore DB / PHP / private field groups
			if( $local !== 'json' || $private ) {
				
				// do nothing
				
			} elseif( !$group['ID'] ) {
				
				$this->sync[ $group['key'] ] = $group;
				
			} elseif( $modified && $modified > get_post_modified_time('U', true, $group['ID'], true) ) {
				
				$this->sync[ $group['key'] ]  = $group;
				
			}
						
		}
		
		
		// bail if no sync needed
		if( empty($this->sync) ) return;
		
		
		// maybe sync
		$sync_keys = array();
		
		
		// check single
		if( $key = pdc_maybe_get_GET('pdcsync') ) {
			
			$sync_keys[] = $key;
		
		// check multiple
		} elseif( pdc_maybe_get_GET('action2') === 'pdcsync' ) {
			
			$sync_keys = pdc_maybe_get_GET('post');
			
		}
		
		
		// sync
		if( !empty($sync_keys) ) {
			
			// validate
			check_admin_referer('bulk-posts');
			
			
			// disable filters to ensure PDC loads raw data from DB
			pdc_disable_filters();
			pdc_enable_filter('local');
			
			
			// disable JSON
			// - this prevents a new JSON file being created and causing a 'change' to theme files - solves git anoyance
			pdc_update_setting('json', false);
			
			
			// vars
			$new_ids = array();
				
			
			// loop
			foreach( $sync_keys as $key ) {
				
				// append fields
				if( pdc_have_local_fields($key) ) {
					
					$this->sync[ $key ]['fields'] = pdc_get_local_fields( $key );
					
				}
				
				
				// import
				$field_group = pdc_import_field_group( $this->sync[ $key ] );
									
				
				// append
				$new_ids[] = $field_group['ID'];
				
			}
			
			
			// redirect
			wp_redirect( admin_url( $this->url . '&pdcsynccomplete=' . implode(',', $new_ids)) );
			exit;
			
		}
		
		
		// filters
		add_filter('views_edit-pdc-field-group', array($this, 'list_table_views'));
		
	}
	
	
	/*
	*  list_table_views
	*
	*  This function will add an extra link for JSON in the field group list table
	*
	*  @type	function
	*  @date	3/12/2014
	*  @since	5.1.5
	*
	*  @param	$views (array)
	*  @return	$views
	*/
	
	function list_table_views( $views ) {
		
		// vars
		$class = '';
		$total = count($this->sync);
		
		// active
		if( pdc_maybe_get_GET('post_status') === 'sync' ) {
			
			// actions
			add_action('admin_footer', array($this, 'sync_admin_footer'), 5);
			
			
			// set active class
			$class = ' class="current"';
			
			
			// global
			global $wp_list_table;
			
			
			// update pagination
			$wp_list_table->set_pagination_args( array(
				'total_items' => $total,
				'total_pages' => 1,
				'per_page' => $total
			));
			
		}
		
		
		// add view
		$views['json'] = '<a' . $class . ' href="' . admin_url($this->url . '&post_status=sync') . '">' . __('Sync available', 'pdc') . ' <span class="count">(' . $total . ')</span></a>';
		
		
		// return
		return $views;
		
	}
	
	
	/*
	*  trashed_post
	*
	*  This function is run when a post object is sent to the trash
	*
	*  @type	action (trashed_post)
	*  @date	8/01/2014
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	n/a
	*/
	
	function trashed_post( $post_id ) {
		
		// validate post type
		if( get_post_type($post_id) != 'pdc-field-group' ) {
		
			return;
		
		}
		
		
		// trash field group
		pdc_trash_field_group( $post_id );
		
	}
	
	
	/*
	*  untrashed_post
	*
	*  This function is run when a post object is restored from the trash
	*
	*  @type	action (untrashed_post)
	*  @date	8/01/2014
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	n/a
	*/
	
	function untrashed_post( $post_id ) {
		
		// validate post type
		if( get_post_type($post_id) != 'pdc-field-group' ) {
		
			return;
			
		}
		
		
		// trash field group
		pdc_untrash_field_group( $post_id );
		
	}
	
	
	/*
	*  deleted_post
	*
	*  This function is run when a post object is deleted from the trash
	*
	*  @type	action (deleted_post)
	*  @date	8/01/2014
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	n/a
	*/
	
	function deleted_post( $post_id ) {
		
		// validate post type
		if( get_post_type($post_id) != 'pdc-field-group' ) {
		
			return;
			
		}
		
		
		// trash field group
		pdc_delete_field_group( $post_id );
		
	}
	
	
	/*
	*  field_group_columns
	*
	*  This function will customize the columns for the field group table
	*
	*  @type	filter (manage_edit-pdc-field-group_columns)
	*  @date	28/09/13
	*  @since	5.0.0
	*
	*  @param	$columns (array)
	*  @return	$columns (array)
	*/
	
	function field_group_columns( $columns ) {
		
		return array(
			'cb'	 				=> '<input type="checkbox" />',
			'title' 				=> __('Title', 'pdc'),
			'pdc-fg-description'	=> __('Description', 'pdc'),
			'pdc-fg-status' 		=> '<i class="pdc-icon -dot-3 small pdc-js-tooltip" title="' . esc_attr__('Status', 'pdc') . '"></i>',
			'pdc-fg-count' 			=> __('Fields', 'pdc'),
		);
		
	}
	
	
	/*
	*  field_group_columns_html
	*
	*  This function will render the HTML for each table cell
	*
	*  @type	action (manage_pdc-field-group_posts_custom_column)
	*  @date	28/09/13
	*  @since	5.0.0
	*
	*  @param	$column (string)
	*  @param	$post_id (int)
	*  @return	n/a
	*/
	
	function field_group_columns_html( $column, $post_id ) {
		
		// vars
		$field_group = pdc_get_field_group( $post_id );
		
		
		// render
		$this->render_column( $column, $field_group );
	    
	}
	
	function render_column( $column, $field_group ) {
		
		// description
		if( $column == 'pdc-fg-description' ) {
			
			if( $field_group['description'] ) {
				
				echo '<span class="pdc-description">' . pdc_esc_html($field_group['description']) . '</span>';
				
			}
        
        // status
	    } elseif( $column == 'pdc-fg-status' ) {
			
			if( isset($this->sync[ $field_group['key'] ]) ) {
				
				echo '<i class="pdc-icon -sync grey small pdc-js-tooltip" title="' . esc_attr__('Sync available', 'pdc') .'"></i> ';
				
			}
			
			if( $field_group['active'] ) {
				
				//echo '<i class="pdc-icon -check small pdc-js-tooltip" title="' . esc_attr__('Active', 'pdc') .'"></i> ';
				
			} else {
				
				echo '<i class="pdc-icon -minus yellow small pdc-js-tooltip" title="' . esc_attr__('Inactive', 'pdc') . '"></i> ';
				
			}
	    
        // fields
	    } elseif( $column == 'pdc-fg-count' ) {
			
			echo esc_html( pdc_get_field_count( $field_group ) );
        
        }
		
	}
	
	
	/*
	*  admin_footer
	*
	*  This function will render extra HTML onto the page
	*
	*  @type	action (admin_footer)
	*  @date	23/06/12
	*  @since	3.1.8
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function admin_footer() {
		
		// vars
		$url_home = 'https://www.advancedcustomfields.com';
		$url_support = 'https://support.advancedcustomfields.com';
		$icon = '<i aria-hidden="true" class="dashicons dashicons-external"></i>';
		
?>
<script type="text/javascript">
(function($){
	
	// wrap
	$('#wpbody .wrap').attr('id', 'pdc-field-group-wrap');
	
	
	// wrap form
	$('#posts-filter').wrap('<div class="pdc-columns-2" />');
	
	
	// add column main
	$('#posts-filter').addClass('pdc-column-1');
	
	
	// add column side
	$('#posts-filter').after( $('#tmpl-pdc-column-2').html() );
	
	
	// modify row actions
	$('#the-list tr').each(function(){
		
		// vars
		var $tr = $(this),
			id = $tr.attr('id'),
			description = $tr.find('.column-pdc-fg-description').html();
		
		
		// replace Quick Edit with Duplicate (sync page has no id attribute)
		if( id ) {
			
			// vars
			var post_id	= id.replace('post-', '');
			var url = '<?php echo esc_url( admin_url( $this->url . '&pdcduplicate=__post_id__&_wpnonce=' . wp_create_nonce('bulk-posts') ) ); ?>';
			var $span = $('<span class="pdc-duplicate-field-group"><a title="<?php _e('Duplicate this item', 'pdc'); ?>" href="' + url.replace('__post_id__', post_id) + '"><?php _e('Duplicate', 'pdc'); ?></a> | </span>');
			
			
			// replace
			$tr.find('.column-title .row-actions .inline').replaceWith( $span );
			
		}
		
		
		// add description to title
		$tr.find('.column-title .row-title').after( description );
		
	});
	
	
	// modify bulk actions
	$('#bulk-action-selector-bottom option[value="edit"]').attr('value','pdcduplicate').text('<?php _e( 'Duplicate', 'pdc' ); ?>');
	
	
	// clean up table
	$('#adv-settings label[for="pdc-fg-description-hide"]').remove();
	
	
	// mobile compatibility
	var status = $('.pdc-icon.-dot-3').first().attr('title');
	$('td.column-pdc-fg-status').attr('data-colname', status);
	
	
	// no field groups found
	$('#the-list tr.no-items td').attr('colspan', 4);
	
	
	// search
	$('.subsubsub').append(' | <li><a href="#" class="pdc-toggle-search"><?php _e('Search', 'pdc'); ?></a></li>');
	
	
	// events
	$(document).on('click', '.pdc-toggle-search', function( e ){
		
		// prevent default
		e.preventDefault();
		
		
		// toggle
		$('.search-box').slideToggle();
		
	});
	
})(jQuery);
</script>
<?php
		
	}
	
	
	/*
	*  sync_admin_footer
	*
	*  This function will render extra HTML onto the page
	*
	*  @type	action (admin_footer)
	*  @date	23/06/12
	*  @since	3.1.8
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function sync_admin_footer() {
		
		// vars
		$i = -1;
		$columns = array(
			'pdc-fg-description',
			'pdc-fg-status',
			'pdc-fg-count'
		);
		$nonce = wp_create_nonce('bulk-posts');
		
?>
<script type="text/html" id="tmpl-pdc-json-tbody">
<?php foreach( $this->sync as $field_group ): 
	
	// vars
	$i++; 
	$key = $field_group['key'];
	$title = $field_group['title'];
	$url = admin_url( $this->url . '&post_status=sync&pdcsync=' . $key . '&_wpnonce=' . $nonce );
	
	?>
	<tr <?php if($i%2 == 0): ?>class="alternate"<?php endif; ?>>
		<th class="check-column" scope="row">
			<label for="cb-select-<?php echo esc_attr($key); ?>" class="screen-reader-text"><?php echo esc_html(sprintf(__('Select %s', 'pdc'), $title)); ?></label>
			<input type="checkbox" value="<?php echo esc_attr($key); ?>" name="post[]" id="cb-select-<?php echo esc_attr($key); ?>">
		</th>
		<td class="post-title page-title column-title">
			<strong>
				<span class="row-title"><?php echo esc_html($title); ?></span><span class="pdc-description"><?php echo esc_html($key); ?>.json</span>
			</strong>
			<div class="row-actions">
				<span class="import"><a title="<?php echo esc_attr( __('Synchronise field group', 'pdc') ); ?>" href="<?php echo esc_url($url); ?>"><?php _e( 'Sync', 'pdc' ); ?></a></span>
			</div>
		</td>
		<?php foreach( $columns as $column ): ?>
			<td class="column-<?php echo esc_attr($column); ?>"><?php $this->render_column( $column, $field_group ); ?></td>
		<?php endforeach; ?>
	</tr>
<?php endforeach; ?>
</script>
<script type="text/html" id="tmpl-pdc-bulk-actions">
	<?php // source: bulk_actions() wp-admin/includes/class-wp-list-table.php ?>
	<select name="action2" id="bulk-action-selector-bottom"></select>
	<?php submit_button( __( 'Apply' ), 'action', '', false, array( 'id' => "doaction2" ) ); ?>
</script>
<script type="text/javascript">
(function($){
	
	// update table HTML
	$('#the-list').html( $('#tmpl-pdc-json-tbody').html() );
	
	
	// bulk may not exist if no field groups in DB
	if( !$('#bulk-action-selector-bottom').exists() ) {
		
		$('.tablenav.bottom .actions.alignleft').html( $('#tmpl-pdc-bulk-actions').html() );
		
	}
	
	
	// set only options
	$('#bulk-action-selector-bottom').html('<option value="-1"><?php _e('Bulk Actions'); ?></option><option value="pdcsync"><?php _e('Sync', 'pdc'); ?></option>');
		
})(jQuery);
</script>
<?php
		
	}
			
}

new pdc_admin_field_groups();

endif;

?>