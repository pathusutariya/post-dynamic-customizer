<?php
/*
  Plugin Name: Post Dynamic Customizer
  Plugin URI: https://github.com/pathusutariya/post-dynamic-customizer
  Description: This Plugin is only for Personal use. Do not use for your website  by anykind.
  Version: 1.2.0
  Author: Parth Sutariya
  Author URI: https://www.github.com/pathusutariya
  Copyright: Parth Sutariya
  Text Domain: pdc
  Domain Path: /lang
 */

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('PDC') ) :

class PDC {
	
	/** @var string The plugin version number */
	var $version = '5.7.6';
	
	/** @var array The plugin settings array */
	var $settings = array();
	
	/** @var array The plugin data array */
	var $data = array();
	
	/** @var array Storage for class instances */
	var $instances = array();
	
	
	/*
	*  __construct
	*
	*  A dummy constructor to ensure PDC is only initialized once
	*
	*  @type	function
	*  @date	23/06/12
	*  @since	5.0.0
	*
	*  @param	N/A
	*  @return	N/A
	*/
	
	function __construct() {
		
		/* Do nothing here */
		
	}

	
	/*
	*  initialize
	*
	*  The real constructor to initialize PDC
	*
	*  @type	function
	*  @date	28/09/13
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
		
	function initialize() {
		
		// vars
		$version = $this->version;
		$basename = plugin_basename( __FILE__ );
		$path = plugin_dir_path( __FILE__ );
		$url = plugin_dir_url( __FILE__ );
		$slug = dirname($basename);
		
		
		// settings
		$this->settings = array(
			
			// basic
			'name'				=> __('Post Dynamic Customizer', 'pdc'),
			'version'			=> $version,
						
			// urls
			'file'				=> __FILE__,
			'basename'			=> $basename,
			'path'				=> $path,
			'url'				=> $url,
			'slug'				=> $slug,
			
			// options
			'show_admin'				=> true,
			'show_updates'				=> false,
			'stripslashes'				=> false,
			'local'						=> true,
			'json'						=> true,
			'save_json'					=> '',
			'load_json'					=> array(),
			'default_language'			=> '',
			'current_language'			=> '',
			'capability'				=> 'manage_options',
			'uploader'					=> 'wp',
			'autoload'					=> false,
			'l10n'						=> true,
			'l10n_textdomain'			=> '',
			'google_api_key'			=> '',
			'google_api_client'			=> '',
			'enqueue_google_maps'		=> true,
			'enqueue_select2'			=> true,
			'enqueue_datepicker'		=> true,
			'enqueue_datetimepicker'	=> true,
			'select2_version'			=> 4,
			'row_index_offset'			=> 1,
			'remove_wp_meta_box'		=> true
		);
		
		
		// constants
		$this->define( 'PDC', 			true );
		$this->define( 'PDC_VERSION', 	$version );
		$this->define( 'PDC_PATH', 		$path );
		//$this->define( 'PDC_DEV', 		true );
		
		// api
		include_once( PDC_PATH . 'includes/api/api-helpers.php');
		pdc_include('includes/api/api-input.php');
		pdc_include('includes/api/api-value.php');
		pdc_include('includes/api/api-field.php');
		pdc_include('includes/api/api-field-group.php');
		pdc_include('includes/api/api-template.php');
		pdc_include('includes/api/api-term.php');
		
		// fields
		pdc_include('includes/fields.php');
		pdc_include('includes/fields/class-pdc-field.php');
		
		// locations
		pdc_include('includes/locations.php');
		pdc_include('includes/locations/class-pdc-location.php');
		
		// core
		pdc_include('includes/assets.php');
		pdc_include('includes/cache.php');
		pdc_include('includes/compatibility.php');
		pdc_include('includes/deprecated.php');
		pdc_include('includes/form.php');
		pdc_include('includes/json.php');
		pdc_include('includes/local.php');
		pdc_include('includes/loop.php');
		pdc_include('includes/media.php');
		pdc_include('includes/revisions.php');
		//pdc_include('includes/updates.php');
		pdc_include('includes/upgrades.php');
		pdc_include('includes/validation.php');
		
		// ajax
		pdc_include('includes/ajax/class-pdc-ajax.php');
		pdc_include('includes/ajax/class-pdc-ajax-check-screen.php');
		pdc_include('includes/ajax/class-pdc-ajax-user-setting.php');
		pdc_include('includes/ajax/class-pdc-ajax-upgrade.php');
		pdc_include('includes/ajax/class-pdc-ajax-query.php');
		pdc_include('includes/ajax/class-pdc-ajax-query-terms.php');
		
		// forms
		pdc_include('includes/forms/form-attachment.php');
		pdc_include('includes/forms/form-comment.php');
		pdc_include('includes/forms/form-customizer.php');
		pdc_include('includes/forms/form-front.php');
		pdc_include('includes/forms/form-nav-menu.php');
		pdc_include('includes/forms/form-post.php');
		pdc_include('includes/forms/form-taxonomy.php');
		pdc_include('includes/forms/form-user.php');
		pdc_include('includes/forms/form-widget.php');
		
		
		// admin
		if( is_admin() ) {
			pdc_include('includes/admin/admin.php');
			pdc_include('includes/admin/admin-field-group.php');
			pdc_include('includes/admin/admin-field-groups.php');
			pdc_include('includes/admin/admin-tools.php');
			pdc_include('includes/admin/admin-upgrade.php');
			pdc_include('includes/admin/settings-info.php');
		}
		
		
		// pro
		pdc_include('pro/pdc-pro.php');
		
		
		// actions
		add_action('init',	array($this, 'init'), 5);
		add_action('init',	array($this, 'register_post_types'), 5);
		add_action('init',	array($this, 'register_post_status'), 5);
		
		
		// filters
		add_filter('posts_where',		array($this, 'posts_where'), 10, 2 );
		//add_filter('posts_request',	array($this, 'posts_request'), 10, 1 );
	}
	
	
	/*
	*  init
	*
	*  This function will run after all plugins and theme functions have been included
	*
	*  @type	action (init)
	*  @date	28/09/13
	*  @since	5.0.0
	*
	*  @param	N/A
	*  @return	N/A
	*/
	
	function init() {
		
		// bail early if too early
		// ensures all plugins have a chance to add fields, etc
		if( !did_action('plugins_loaded') ) return;
		
		
		// bail early if already init
		if( pdc_has_done('init') ) return;
		
		
		// vars
		$major = intval( pdc_get_setting('version') );
		
		
		// update url
		// - allow another plugin to modify dir (maybe force SSL)
		pdc_update_setting('url', plugin_dir_url( __FILE__ ));
		
		
		// textdomain
		$this->load_plugin_textdomain();
		
		// include 3rd party support
		pdc_include('includes/third-party.php');
		
		// include wpml support
		if( defined('ICL_SITEPRESS_VERSION') ) {
			pdc_include('includes/wpml.php');
		}
		
		// include gutenberg
		if( defined('GUTENBERG_VERSION') ) {
			pdc_include('includes/forms/form-gutenberg.php');
		}
		
		// fields
		pdc_include('includes/fields/class-pdc-field-text.php');
		pdc_include('includes/fields/class-pdc-field-textarea.php');
		pdc_include('includes/fields/class-pdc-field-number.php');
		pdc_include('includes/fields/class-pdc-field-range.php');
		pdc_include('includes/fields/class-pdc-field-email.php');
		pdc_include('includes/fields/class-pdc-field-url.php');
		pdc_include('includes/fields/class-pdc-field-password.php');
		
		pdc_include('includes/fields/class-pdc-field-image.php');
		pdc_include('includes/fields/class-pdc-field-file.php');
		pdc_include('includes/fields/class-pdc-field-wysiwyg.php');
		pdc_include('includes/fields/class-pdc-field-oembed.php');
		
		pdc_include('includes/fields/class-pdc-field-select.php');
		pdc_include('includes/fields/class-pdc-field-checkbox.php');
		pdc_include('includes/fields/class-pdc-field-radio.php');
		pdc_include('includes/fields/class-pdc-field-button-group.php');
		pdc_include('includes/fields/class-pdc-field-true_false.php');
		
		pdc_include('includes/fields/class-pdc-field-link.php');
		pdc_include('includes/fields/class-pdc-field-post_object.php');
		pdc_include('includes/fields/class-pdc-field-page_link.php');
		pdc_include('includes/fields/class-pdc-field-relationship.php');
		pdc_include('includes/fields/class-pdc-field-taxonomy.php');
		pdc_include('includes/fields/class-pdc-field-user.php');
		
		pdc_include('includes/fields/class-pdc-field-google-map.php');
		pdc_include('includes/fields/class-pdc-field-date_picker.php');
		pdc_include('includes/fields/class-pdc-field-date_time_picker.php');
		pdc_include('includes/fields/class-pdc-field-time_picker.php');
		pdc_include('includes/fields/class-pdc-field-color_picker.php');
		
		pdc_include('includes/fields/class-pdc-field-message.php');
		pdc_include('includes/fields/class-pdc-field-accordion.php');
		pdc_include('includes/fields/class-pdc-field-tab.php');
		pdc_include('includes/fields/class-pdc-field-group.php');
		do_action('pdc/include_field_types', $major);
		
		
		// locations
		pdc_include('includes/locations/class-pdc-location-post-type.php');
		pdc_include('includes/locations/class-pdc-location-post-template.php');
		pdc_include('includes/locations/class-pdc-location-post-status.php');
		pdc_include('includes/locations/class-pdc-location-post-format.php');
		pdc_include('includes/locations/class-pdc-location-post-category.php');
		pdc_include('includes/locations/class-pdc-location-post-taxonomy.php');
		pdc_include('includes/locations/class-pdc-location-post.php');
		pdc_include('includes/locations/class-pdc-location-page-template.php');
		pdc_include('includes/locations/class-pdc-location-page-type.php');
		pdc_include('includes/locations/class-pdc-location-page-parent.php');
		pdc_include('includes/locations/class-pdc-location-page.php');
		pdc_include('includes/locations/class-pdc-location-current-user.php');
		pdc_include('includes/locations/class-pdc-location-current-user-role.php');
		pdc_include('includes/locations/class-pdc-location-user-form.php');
		pdc_include('includes/locations/class-pdc-location-user-role.php');
		pdc_include('includes/locations/class-pdc-location-taxonomy.php');
		pdc_include('includes/locations/class-pdc-location-attachment.php');
		pdc_include('includes/locations/class-pdc-location-comment.php');
		pdc_include('includes/locations/class-pdc-location-widget.php');
		pdc_include('includes/locations/class-pdc-location-nav-menu.php');
		pdc_include('includes/locations/class-pdc-location-nav-menu-item.php');
		do_action('pdc/include_location_rules', $major);
		
		
		// local fields
		do_action('pdc/include_fields', $major);
		
		
		// action for 3rd party
		do_action('pdc/init');
			
	}
	
	
	/*
	*  load_plugin_textdomain
	*
	*  This function will load the textdomain file
	*
	*  @type	function
	*  @date	3/5/17
	*  @since	5.5.13
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function load_plugin_textdomain() {
		
		// vars
		$domain = 'pdc';
		$locale = apply_filters( 'plugin_locale', pdc_get_locale(), $domain );
		$mofile = $domain . '-' . $locale . '.mo';
		
		
		// load from the languages directory first
		load_textdomain( $domain, WP_LANG_DIR . '/plugins/' . $mofile );
		
		
		// redirect missing translations
		$mofile = str_replace('fr_CA', 'fr_FR', $mofile);
		
		
		// load from plugin lang folder
		load_textdomain( $domain, pdc_get_path( 'lang/' . $mofile ) );
		
	}
	
	
	/*
	*  register_post_types
	*
	*  This function will register post types and statuses
	*
	*  @type	function
	*  @date	22/10/2015
	*  @since	5.3.2
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function register_post_types() {
		
		// vars
		$cap = pdc_get_setting('capability');
		
		
		// register post type 'pdc-field-group'
		register_post_type('pdc-field-group', array(
			'labels'			=> array(
			    'name'					=> __( 'Field Groups', 'pdc' ),
				'singular_name'			=> __( 'Field Group', 'pdc' ),
			    'add_new'				=> __( 'Add New' , 'pdc' ),
			    'add_new_item'			=> __( 'Add New Field Group' , 'pdc' ),
			    'edit_item'				=> __( 'Edit Field Group' , 'pdc' ),
			    'new_item'				=> __( 'New Field Group' , 'pdc' ),
			    'view_item'				=> __( 'View Field Group', 'pdc' ),
			    'search_items'			=> __( 'Search Field Groups', 'pdc' ),
			    'not_found'				=> __( 'No Field Groups found', 'pdc' ),
			    'not_found_in_trash'	=> __( 'No Field Groups found in Trash', 'pdc' ),
			),
			'public'			=> false,
			'show_ui'			=> true,
			'_builtin'			=> false,
			'capability_type'	=> 'post',
			'capabilities'		=> array(
				'edit_post'			=> $cap,
				'delete_post'		=> $cap,
				'edit_posts'		=> $cap,
				'delete_posts'		=> $cap,
			),
			'hierarchical'		=> true,
			'rewrite'			=> false,
			'query_var'			=> false,
			'supports' 			=> array('title'),
			'show_in_menu'		=> false,
		));
		
		
		// register post type 'pdc-field'
		register_post_type('pdc-field', array(
			'labels'			=> array(
			    'name'					=> __( 'Fields', 'pdc' ),
				'singular_name'			=> __( 'Field', 'pdc' ),
			    'add_new'				=> __( 'Add New' , 'pdc' ),
			    'add_new_item'			=> __( 'Add New Field' , 'pdc' ),
			    'edit_item'				=> __( 'Edit Field' , 'pdc' ),
			    'new_item'				=> __( 'New Field' , 'pdc' ),
			    'view_item'				=> __( 'View Field', 'pdc' ),
			    'search_items'			=> __( 'Search Fields', 'pdc' ),
			    'not_found'				=> __( 'No Fields found', 'pdc' ),
			    'not_found_in_trash'	=> __( 'No Fields found in Trash', 'pdc' ),
			),
			'public'			=> false,
			'show_ui'			=> false,
			'_builtin'			=> false,
			'capability_type'	=> 'post',
			'capabilities'		=> array(
				'edit_post'			=> $cap,
				'delete_post'		=> $cap,
				'edit_posts'		=> $cap,
				'delete_posts'		=> $cap,
			),
			'hierarchical'		=> true,
			'rewrite'			=> false,
			'query_var'			=> false,
			'supports' 			=> array('title'),
			'show_in_menu'		=> false,
		));
		
	}
	
	
	/*
	*  register_post_status
	*
	*  This function will register custom post statuses
	*
	*  @type	function
	*  @date	22/10/2015
	*  @since	5.3.2
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function register_post_status() {
		
		// pdc-disabled
		register_post_status('pdc-disabled', array(
			'label'                     => __( 'Inactive', 'pdc' ),
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Inactive <span class="count">(%s)</span>', 'Inactive <span class="count">(%s)</span>', 'pdc' ),
		));
		
	}
	
	
	/*
	*  posts_where
	*
	*  This function will add in some new parameters to the WP_Query args allowing fields to be found via key / name
	*
	*  @type	filter
	*  @date	5/12/2013
	*  @since	5.0.0
	*
	*  @param	$where (string)
	*  @param	$wp_query (object)
	*  @return	$where (string)
	*/
	
	function posts_where( $where, $wp_query ) {
		
		// global
		global $wpdb;
		
		
		// pdc_field_key
		if( $field_key = $wp_query->get('pdc_field_key') ) {
			$where .= $wpdb->prepare(" AND {$wpdb->posts}.post_name = %s", $field_key );
	    }
	    
	    // pdc_field_name
	    if( $field_name = $wp_query->get('pdc_field_name') ) {
			$where .= $wpdb->prepare(" AND {$wpdb->posts}.post_excerpt = %s", $field_name );
	    }
	    
	    // pdc_group_key
		if( $group_key = $wp_query->get('pdc_group_key') ) {
			$where .= $wpdb->prepare(" AND {$wpdb->posts}.post_name = %s", $group_key );
	    }
	    
	    
	    // return
	    return $where;
	    
	}
	
	
	/*
	*  define
	*
	*  This function will safely define a constant
	*
	*  @type	function
	*  @date	3/5/17
	*  @since	5.5.13
	*
	*  @param	$name (string)
	*  @param	$value (mixed)
	*  @return	n/a
	*/
	
	function define( $name, $value = true ) {
		
		if( !defined($name) ) {
			define( $name, $value );
		}
		
	}
	
	/**
	*  has_setting
	*
	*  Returns true if has setting.
	*
	*  @date	2/2/18
	*  @since	5.6.5
	*
	*  @param	string $name
	*  @return	boolean
	*/
	
	function has_setting( $name ) {
		return isset($this->settings[ $name ]);
	}
	
	/**
	*  get_setting
	*
	*  Returns a setting.
	*
	*  @date	28/09/13
	*  @since	5.0.0
	*
	*  @param	string $name
	*  @return	mixed
	*/
	
	function get_setting( $name ) {
		return isset($this->settings[ $name ]) ? $this->settings[ $name ] : null;
	}
	
	/**
	*  update_setting
	*
	*  Updates a setting.
	*
	*  @date	28/09/13
	*  @since	5.0.0
	*
	*  @param	string $name
	*  @param	mixed $value
	*  @return	n/a
	*/
	
	function update_setting( $name, $value ) {
		$this->settings[ $name ] = $value;
		//return true;
	}
	
	/**
	*  get_data
	*
	*  Returns data.
	*
	*  @date	28/09/13
	*  @since	5.0.0
	*
	*  @param	string $name
	*  @return	mixed
	*/
	
	function get_data( $name ) {
		return isset($this->data[ $name ]) ? $this->data[ $name ] : null;
	}
	
	
	/**
	*  set_data
	*
	*  Sets data.
	*
	*  @date	28/09/13
	*  @since	5.0.0
	*
	*  @param	string $name
	*  @param	mixed $value
	*  @return	n/a
	*/
	
	function set_data( $name, $value ) {
		$this->data[ $name ] = $value;
	}
	
	
	/**
	*  get_instance
	*
	*  Returns an instance.
	*
	*  @date	13/2/18
	*  @since	5.6.9
	*
	*  @param	string $class The instance class name.
	*  @return	object
	*/
	
	function get_instance( $class ) {
		$name = strtolower($class);
		return isset($this->instances[ $name ]) ? $this->instances[ $name ] : null;
	}
	
	/**
	*  new_instance
	*
	*  Creates and stores an instance.
	*
	*  @date	13/2/18
	*  @since	5.6.9
	*
	*  @param	string $class The instance class name.
	*  @return	object
	*/
	
	function new_instance( $class ) {
		$instance = new $class();
		$name = strtolower($class);
		$this->instances[ $name ] = $instance;
		return $instance;
	}
	
}


/*
*  pdc
*
*  The main function responsible for returning the one true pdc Instance to functions everywhere.
*  Use this function like you would a global variable, except without needing to declare the global.
*
*  Example: <?php $pdc = pdc(); ?>
*
*  @type	function
*  @date	4/09/13
*  @since	4.3.0
*
*  @param	N/A
*  @return	(object)
*/


function pdc() {
	
	// globals
	global $pdc;
	
	
	// initialize
	if( !isset($pdc) ) {
		$pdc = new PDC();
		$pdc->initialize();
	}


	// return
	return $pdc;
	
}


// initialize
pdc();


endif; // class_exists check

//Adding Option Page
include 'custom/theme-code/pdc_theme_code.php';
include_once 'custom/option_page_creator.php';
?>
