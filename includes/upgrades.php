<?php

/**
*  pdc_has_upgrade
*
*  Returns true if this site has an upgrade avaialble.
*
*  @date	24/8/18
*  @since	5.7.4
*
*  @param	void
*  @return	bool
*/
function pdc_has_upgrade() {
	
	// vars
	$db_version = pdc_get_db_version();
	
	// return true if DB version is < latest upgrade version
	if( $db_version && pdc_version_compare($db_version, '<', '5.5.0') ) {
		return true;
	}
	
	// update DB version if needed
	if( $db_version !== PDC_VERSION ) {
		pdc_update_db_version( PDC_VERSION );
	}
	
	// return
	return false;
}

/**
*  pdc_upgrade_all
*
*  Returns true if this site has an upgrade avaialble.
*
*  @date	24/8/18
*  @since	5.7.4
*
*  @param	void
*  @return	bool
*/
function pdc_upgrade_all() {
	
	// log
	pdc_dev_log('pdc_upgrade_all');
	
	// vars
	$db_version = pdc_get_db_version();
	
	// 5.0.0
	if( pdc_version_compare($db_version, '<', '5.0.0') ) {
		pdc_upgrade_500();
	}
	
	// 5.5.0
	if( pdc_version_compare($db_version, '<', '5.5.0') ) {
		pdc_upgrade_550();
	}
	
	// upgrade DB version once all updates are complete
	pdc_update_db_version( PDC_VERSION );
}

/**
*  pdc_get_db_version
*
*  Returns the PDC DB version.
*
*  @date	10/09/2016
*  @since	5.4.0
*
*  @param	void
*  @return	string
*/
function pdc_get_db_version() {
	return get_option('pdc_version');
}

/*
*  pdc_update_db_version
*
*  Updates the PDC DB version.
*
*  @date	10/09/2016
*  @since	5.4.0
*
*  @param	string $version The new version.
*  @return	void
*/
function pdc_update_db_version( $version = '' ) {
	update_option('pdc_version', $version );
}

/**
*  pdc_upgrade_500
*
*  Version 5 introduces new post types for field groups and fields.
*
*  @date	23/8/18
*  @since	5.7.4
*
*  @param	void
*  @return	void
*/
function pdc_upgrade_500() {
	
	// log
	pdc_dev_log('pdc_upgrade_500');
	
	// action
	do_action('pdc/upgrade_500');
	
	// do tasks
	pdc_upgrade_500_field_groups();
	
	// update version
	pdc_update_db_version('5.0.0');
}

/**
*  pdc_upgrade_500_field_groups
*
*  Upgrades all PDC4 field groups to PDC5
*
*  @date	23/8/18
*  @since	5.7.4
*
*  @param	void
*  @return	void
*/
function pdc_upgrade_500_field_groups() {
	
	// log
	pdc_dev_log('pdc_upgrade_500_field_groups');
	
	// get old field groups
	$ofgs = get_posts(array(
		'numberposts' 		=> -1,
		'post_type' 		=> 'pdc',
		'orderby' 			=> 'menu_order title',
		'order' 			=> 'asc',
		'suppress_filters'	=> true,
	));
	
	// loop
	if( $ofgs ) {
		foreach( $ofgs as $ofg ){
			pdc_upgrade_500_field_group( $ofg );
		}
	}
}

/**
*  pdc_upgrade_500_field_group
*
*  Upgrades a PDC4 field group to PDC5
*
*  @date	23/8/18
*  @since	5.7.4
*
*  @param	object $ofg	The old field group post object.
*  @return	array $nfg	The new field group array.
*/
function pdc_upgrade_500_field_group( $ofg ) {
	
	// vars
	$nfg = array(
		'ID'			=> 0,
		'title'			=> $ofg->post_title,
		'menu_order'	=> $ofg->menu_order,
	);
	
	// construct the location rules
	$rules = get_post_meta($ofg->ID, 'rule', false);
	$anyorall = get_post_meta($ofg->ID, 'allorany', true);
	if( is_array($rules) ) {
		
		// if field group was duplicated, rules may be a serialized string!
		$rules = array_map('maybe_unserialize', $rules);
		
		// convert rules to groups
		$nfg['location'] = pdc_convert_rules_to_groups( $rules, $anyorall );
	}
	
	// settings
	if( $position = get_post_meta($ofg->ID, 'position', true) ) {
		$nfg['position'] = $position;
	}
	
	if( $layout = get_post_meta($ofg->ID, 'layout', true) ) {
		$nfg['layout'] = $layout;
	}
	
	if( $hide_on_screen = get_post_meta($ofg->ID, 'hide_on_screen', true) ) {
		$nfg['hide_on_screen'] = maybe_unserialize($hide_on_screen);
	}
	
	// save field group
	// pdc_upgrade_field_group will call the pdc_get_valid_field_group function and apply 'compatibility' changes
	$nfg = pdc_update_field_group( $nfg );
	
	// action for 3rd party
	do_action('pdc/upgrade_500_field_group', $nfg, $ofg);
	
	// log
	pdc_dev_log('pdc_upgrade_500_field_group', $ofg, $nfg);
	
	// upgrade fields
	pdc_upgrade_500_fields( $ofg, $nfg );
	
	// trash?
	if( $ofg->post_status == 'trash' ) {
		pdc_trash_field_group( $nfg['ID'] );
	}
	
	// return
	return $nfg;
}

/**
*  pdc_upgrade_500_fields
*
*  Upgrades all PDC4 fields to PDC5 from a specific field group 
*
*  @date	23/8/18
*  @since	5.7.4
*
*  @param	object $ofg	The old field group post object.
*  @param	array $nfg	The new field group array.
*  @return	void
*/
function pdc_upgrade_500_fields( $ofg, $nfg ) {
	
	// global
	global $wpdb;
	
	// get field from postmeta
	$rows = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->postmeta WHERE post_id = %d AND meta_key LIKE %s", $ofg->ID, 'field_%'), ARRAY_A);
	
	// check
	if( $rows ) {
		
		// vars
		$checked = array();
		
		// loop
		foreach( $rows as $row ) {
			
			// vars
			$field = $row['meta_value'];
			$field = maybe_unserialize( $field );
			$field = maybe_unserialize( $field ); // run again for WPML
			
			// bail early if key already migrated (potential duplicates in DB)
			if( isset($checked[ $field['key'] ]) ) continue;
			$checked[ $field['key'] ] = 1;
			
			// add parent
			$field['parent'] = $nfg['ID'];
			
			// migrate field
			$field = pdc_upgrade_500_field( $field );
		}
 	}
}

/**
*  pdc_upgrade_500_field
*
*  Upgrades a PDC4 field to PDC5
*
*  @date	23/8/18
*  @since	5.7.4
*
*  @param	array $field The old field.
*  @return	array $field The new field.
*/
function pdc_upgrade_500_field( $field ) {
	
	// order_no is now menu_order
	$field['menu_order'] = pdc_extract_var( $field, 'order_no', 0 );
	
	// correct very old field keys (field2 => field_2)
	if( substr($field['key'], 0, 6) !== 'field_' ) {
		$field['key'] = 'field_' . str_replace('field', '', $field['key']);
	}
	
	// extract sub fields
	$sub_fields = array();
	if( $field['type'] == 'repeater' ) {
		
		// loop over sub fields
		if( !empty($field['sub_fields']) ) {
			foreach( $field['sub_fields'] as $sub_field ) {
				$sub_fields[] = $sub_field;
			}
		}
		
		// remove sub fields from field
		unset( $field['sub_fields'] );
	
	} elseif( $field['type'] == 'flexible_content' ) {
		
		// loop over layouts
		if( is_array($field['layouts']) ) {
			foreach( $field['layouts'] as $i => $layout ) {
				
				// generate key
				$layout['key'] = uniqid('layout_');
				
				// loop over sub fields
				if( !empty($layout['sub_fields']) ) {
					foreach( $layout['sub_fields'] as $sub_field ) {
						$sub_field['parent_layout'] = $layout['key'];
						$sub_fields[] = $sub_field;
					}
				}
				
				// remove sub fields from layout
				unset( $layout['sub_fields'] );
				
				// update
				$field['layouts'][ $i ] = $layout;
				
			}
		}
	}
	
	// save field
	$field = pdc_update_field( $field );
	
	// log
	pdc_dev_log('pdc_upgrade_500_field', $field);
	
	// sub fields
	if( $sub_fields ) {
		foreach( $sub_fields as $sub_field ) {
			$sub_field['parent'] = $field['ID'];
			pdc_upgrade_500_field($sub_field);
		}
	}
	
	// action for 3rd party
	do_action('pdc/update_500_field', $field);
	
	// return
	return $field;
}

/**
*  pdc_upgrade_550
*
*  Version 5.5 adds support for the wp_termmeta table added in WP 4.4.
*
*  @date	23/8/18
*  @since	5.7.4
*
*  @param	void
*  @return	void
*/
function pdc_upgrade_550() {
	
	// log
	pdc_dev_log('pdc_upgrade_550');
	
	// action
	do_action('pdc/upgrade_550');
	
	// do tasks
	pdc_upgrade_550_termmeta();
	
	// update version
	pdc_update_db_version('5.5.0');
}

/**
*  pdc_upgrade_550_termmeta
*
*  Upgrades all PDC4 termmeta saved in wp_options to the wp_termmeta table.
*
*  @date	23/8/18
*  @since	5.7.4
*
*  @param	void
*  @return	void
*/
function pdc_upgrade_550_termmeta() {
	
	// log
	pdc_dev_log('pdc_upgrade_550_termmeta');
	
	// bail early if no wp_termmeta table
	if( get_option('db_version') < 34370 ) {
		return;
	}
	
	// get all taxonomies
	$taxonomies = get_taxonomies(false, 'objects');
	
	// loop
	if( $taxonomies ) {
	foreach( $taxonomies as $taxonomy ) {
		pdc_upgrade_550_taxonomy( $taxonomy->name );
	}}
	
	// action for 3rd party
	do_action('pdc/upgrade_550_termmeta');
}

/*
*  pdc_wp_upgrade_550_termmeta
*
*  When the database is updated to support term meta, migrate PDC term meta data across.
*
*  @date	23/8/18
*  @since	5.7.4
*
*  @param	string $wp_db_version The new $wp_db_version.
*  @param	string $wp_current_db_version The old (current) $wp_db_version.
*  @return	void
*/
function pdc_wp_upgrade_550_termmeta( $wp_db_version, $wp_current_db_version ) {
	if( $wp_db_version >= 34370 && $wp_current_db_version < 34370 ) {
		if( pdc_version_compare(pdc_get_db_version(), '>', '5.5.0') ) {
			pdc_upgrade_550_termmeta();
		}				
	}
}
add_action( 'wp_upgrade', 'pdc_wp_upgrade_550_termmeta', 10, 2 );

/**
*  pdc_upgrade_550_taxonomy
*
*  Upgrades all PDC4 termmeta for a specific taxonomy.
*
*  @date	24/8/18
*  @since	5.7.4
*
*  @param	string $taxonomy The taxonomy name.
*  @return	void
*/
function pdc_upgrade_550_taxonomy( $taxonomy ) {
	
	// log
	pdc_dev_log('pdc_upgrade_550_taxonomy', $taxonomy);
	
	// global
	global $wpdb;
	
	// vars
	$search = $taxonomy . '_%';
	$_search = '_' . $search;
	
	// escape '_'
	// http://stackoverflow.com/questions/2300285/how-do-i-escape-in-sql-server
	$search = str_replace('_', '\_', $search);
	$_search = str_replace('_', '\_', $_search);
	
	// search
	// results show faster query times using 2 LIKE vs 2 wildcards
	$rows = $wpdb->get_results($wpdb->prepare(
		"SELECT * 
		FROM $wpdb->options 
		WHERE option_name LIKE %s 
		OR option_name LIKE %s",
		$search,
		$_search 
	), ARRAY_A);
	
	// loop
	if( $rows ) {
	foreach( $rows as $row ) {
		
		/*
		Use regex to find "(_)taxonomy_(term_id)_(field_name)" and populate $matches:
		Array
		(
		    [0] => _category_3_color
		    [1] => _
		    [2] => 3
		    [3] => color
		)
		*/
		if( !preg_match("/^(_?){$taxonomy}_(\d+)_(.+)/", $row['option_name'], $matches) ) {
			continue;
		}
		
		// vars
		$term_id = $matches[2];
		$meta_name = $matches[1] . $matches[3];
		$meta_value = $row['option_value'];
		
		// log
		pdc_dev_log('pdc_upgrade_550_term', $term_id, $meta_name, $meta_value);
		
		// update
		update_metadata( 'term', $term_id, $meta_name, $meta_value );
	}}
	
	// action for 3rd party
	do_action('pdc/upgrade_550_taxonomy', $taxonomy);
}


?>