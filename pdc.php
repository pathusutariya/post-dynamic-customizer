<?php

/*
  Plugin Name: Post Dynamic Customizer
  Plugin URI: https://github.com/pathusutariya/post-dynamic-customizer
  Description: This Plugin is only for Personal use.   Do not use for your website  by anykind.
  Version: 1.0.0
  Author: Parth Sutariya
  Author URI: https://www.github.com/pathusutariya
  Copyright: Parth Sutariya
  Text Domain: pdc
  Domain Path: /lang
 */

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

if (!class_exists('pdc')) :

    class pdc {
        /*
         *  __construct
         *
         *  A dummy constructor to ensure ACF is only initialized once
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
         *  The real constructor to initialize ACF
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
            $this->settings = array(
              // basic
              'name'                   => __('Post Dynamic Customizer', 'pdc'),
              'version'                => '1.0.0',
              // urls
              'basename'               => plugin_basename(__FILE__),
              'path'                   => plugin_dir_path(__FILE__),
              'dir'                    => plugin_dir_url(__FILE__),
              // options
              'show_admin'             => true,
              'show_updates'           => true,
              'stripslashes'           => false,
              'local'                  => true,
              'json'                   => true,
              'save_json'              => '',
              'load_json'              => array(),
              'default_language'       => '',
              'current_language'       => '',
              'capability'             => 'manage_options',
              'uploader'               => 'wp',
              'autoload'               => false,
              'l10n'                   => true,
              'l10n_textdomain'        => '',
              'google_api_key'         => '',
              'google_api_client'      => '',
              'enqueue_google_maps'    => true,
              'enqueue_select2'        => true,
              'enqueue_datepicker'     => true,
              'enqueue_datetimepicker' => true,
              'select2_version'        => 3,
              'row_index_offset'       => 1
            );


            // include helpers
            include_once( $this->settings['path'] . 'api/api-helpers.php');


            // api
            pdc_include('api/api-value.php');
            pdc_include('api/api-field.php');
            pdc_include('api/api-field-group.php');
            pdc_include('api/api-template.php');


            // core
            pdc_include('core/ajax.php');
            pdc_include('core/cache.php');
            pdc_include('core/compatibility.php');
            pdc_include('core/deprecated.php');
            pdc_include('core/field.php');
            pdc_include('core/fields.php');
            pdc_include('core/form.php');
            pdc_include('core/input.php');
            pdc_include('core/validation.php');
            pdc_include('core/json.php');
            pdc_include('core/local.php');
            pdc_include('core/location.php');
            pdc_include('core/loop.php');
            pdc_include('core/media.php');
            pdc_include('core/revisions.php');
            pdc_include('core/third_party.php');
            pdc_include('core/updates.php');


            // forms
            pdc_include('forms/attachment.php');
            pdc_include('forms/comment.php');
            pdc_include('forms/post.php');
            pdc_include('forms/taxonomy.php');
            pdc_include('forms/user.php');
            pdc_include('forms/widget.php');


            // admin
            if (is_admin()) {

                pdc_include('admin/admin.php');
                pdc_include('admin/field-group.php');
                pdc_include('admin/field-groups.php');
                pdc_include('admin/install.php');
                pdc_include('admin/settings-tools.php');
                pdc_include('admin/settings-info.php');


                // network
                if (is_network_admin()) {

                    pdc_include('admin/install-network.php');
                }
            }


            // pro
            pdc_include('pro/pdc-pro.php');


            // actions
            add_action('init', array($this, 'init'), 5);
            add_action('init', array($this, 'register_post_types'), 5);
            add_action('init', array($this, 'register_post_status'), 5);
            add_action('init', array($this, 'register_assets'), 5);


            // filters
            add_filter('posts_where', array($this, 'posts_where'), 10, 2);
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
            if (!did_action('plugins_loaded'))
                return;


            // bail early if already init
            if (pdc_has_done('init'))
                return;


            // vars
            $major = intval(pdc_get_setting('version'));


            // redeclare dir
            // - allow another plugin to modify dir (maybe force SSL)
            pdc_update_setting('dir', plugin_dir_url(__FILE__));


            // set text domain
            load_textdomain('pdc', pdc_get_path('lang/pdc-' . pdc_get_locale() . '.mo'));


            // include wpml support
            if (defined('ICL_SITEPRESS_VERSION')) {

                pdc_include('core/wpml.php');
            }


            // field types
            pdc_include('fields/text.php');
            pdc_include('fields/textarea.php');
            pdc_include('fields/number.php');
            pdc_include('fields/email.php');
            pdc_include('fields/url.php');
            pdc_include('fields/password.php');
            pdc_include('fields/wysiwyg.php');
            pdc_include('fields/oembed.php');
            //pdc_include('fields/output.php');
            pdc_include('fields/image.php');
            pdc_include('fields/file.php');
            pdc_include('fields/select.php');
            pdc_include('fields/checkbox.php');
            pdc_include('fields/radio.php');
            pdc_include('fields/true_false.php');
            pdc_include('fields/post_object.php');
            pdc_include('fields/page_link.php');
            pdc_include('fields/relationship.php');
            pdc_include('fields/taxonomy.php');
            pdc_include('fields/user.php');
            pdc_include('fields/google-map.php');
            pdc_include('fields/date_picker.php');
            pdc_include('fields/date_time_picker.php');
            pdc_include('fields/time_picker.php');
            pdc_include('fields/color_picker.php');
            pdc_include('fields/message.php');
            pdc_include('fields/tab.php');


            // 3rd party field types
            do_action('pdc/include_field_types', $major);


            // local fields
            do_action('pdc/include_fields', $major);


            // action for 3rd party
            do_action('pdc/init');
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
              'labels'          => array(
                'name'               => __('Field Groups', 'pdc'),
                'singular_name'      => __('Field Group', 'pdc'),
                'add_new'            => __('Add New', 'pdc'),
                'add_new_item'       => __('Add New Field Group', 'pdc'),
                'edit_item'          => __('Edit Field Group', 'pdc'),
                'new_item'           => __('New Field Group', 'pdc'),
                'view_item'          => __('View Field Group', 'pdc'),
                'search_items'       => __('Search Field Groups', 'pdc'),
                'not_found'          => __('No Field Groups found', 'pdc'),
                'not_found_in_trash' => __('No Field Groups found in Trash', 'pdc'),
              ),
              'public'          => false,
              'show_ui'         => true,
              '_builtin'        => false,
              'capability_type' => 'post',
              'capabilities'    => array(
                'edit_post'    => $cap,
                'delete_post'  => $cap,
                'edit_posts'   => $cap,
                'delete_posts' => $cap,
              ),
              'hierarchical'    => true,
              'rewrite'         => false,
              'query_var'       => false,
              'supports'        => array('title'),
              'show_in_menu'    => false,
            ));


            // register post type 'pdc-field'
            register_post_type('pdc-field', array(
              'labels'          => array(
                'name'               => __('Fields', 'pdc'),
                'singular_name'      => __('Field', 'pdc'),
                'add_new'            => __('Add New', 'pdc'),
                'add_new_item'       => __('Add New Field', 'pdc'),
                'edit_item'          => __('Edit Field', 'pdc'),
                'new_item'           => __('New Field', 'pdc'),
                'view_item'          => __('View Field', 'pdc'),
                'search_items'       => __('Search Fields', 'pdc'),
                'not_found'          => __('No Fields found', 'pdc'),
                'not_found_in_trash' => __('No Fields found in Trash', 'pdc'),
              ),
              'public'          => false,
              'show_ui'         => false,
              '_builtin'        => false,
              'capability_type' => 'post',
              'capabilities'    => array(
                'edit_post'    => $cap,
                'delete_post'  => $cap,
                'edit_posts'   => $cap,
                'delete_posts' => $cap,
              ),
              'hierarchical'    => true,
              'rewrite'         => false,
              'query_var'       => false,
              'supports'        => array('title'),
              'show_in_menu'    => false,
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
              'label'                     => __('Inactive', 'pdc'),
              'public'                    => true,
              'exclude_from_search'       => false,
              'show_in_admin_all_list'    => true,
              'show_in_admin_status_list' => true,
              'label_count'               => _n_noop('Inactive <span class="count">(%s)</span>', 'Inactive <span class="count">(%s)</span>', 'pdc'),
            ));
        }

        /*
         *  register_assets
         *
         *  This function will register scripts and styles
         *
         *  @type	function
         *  @date	22/10/2015
         *  @since	5.3.2
         *
         *  @param	n/a
         *  @return	n/a
         */

        function register_assets() {

            // vars
            $version = pdc_get_setting('version');
            $min     = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';


            // scripts
            wp_register_script('pdc-input', pdc_get_dir("assets/js/pdc-input{$min}.js"), array('jquery', 'jquery-ui-core', 'jquery-ui-sortable', 'jquery-ui-resizable'), $version);
            wp_register_script('pdc-field-group', pdc_get_dir("assets/js/pdc-field-group{$min}.js"), array('pdc-input'), $version);


            // styles
            wp_register_style('pdc-global', pdc_get_dir('assets/css/pdc-global.css'), array(), $version);
            wp_register_style('pdc-input', pdc_get_dir('assets/css/pdc-input.css'), array('pdc-global'), $version);
            wp_register_style('pdc-field-group', pdc_get_dir('assets/css/pdc-field-group.css'), array('pdc-input'), $version);
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

        function posts_where($where, $wp_query) {

            // global
            global $wpdb;


            // pdc_field_key
            if ($field_key = $wp_query->get('pdc_field_key')) {

                $where .= $wpdb->prepare(" AND {$wpdb->posts}.post_name = %s", $field_key);
            }


            // pdc_field_name
            if ($field_name = $wp_query->get('pdc_field_name')) {

                $where .= $wpdb->prepare(" AND {$wpdb->posts}.post_excerpt = %s", $field_name);
            }


            // pdc_group_key
            if ($group_key = $wp_query->get('pdc_group_key')) {

                $where .= $wpdb->prepare(" AND {$wpdb->posts}.post_name = %s", $group_key);
            }


            // return
            return $where;
        }

        /*
         *  get_setting
         *
         *  This function will return a value from the settings array found in the pdc object
         *
         *  @type	function
         *  @date	28/09/13
         *  @since	5.0.0
         *
         *  @param	$name (string) the setting name to return
         *  @param	$value (mixed) default value
         *  @return	$value
         */

        function get_setting($name, $value = null) {

            // check settings
            if (isset($this->settings[$name])) {

                $value = $this->settings[$name];
            }


            // filter for 3rd party customization
            if (substr($name, 0, 1) !== '_') {

                $value = apply_filters("pdc/settings/{$name}", $value);
            }


            // return
            return $value;
        }

        /*
         *  update_setting
         *
         *  This function will update a value into the settings array found in the pdc object
         *
         *  @type	function
         *  @date	28/09/13
         *  @since	5.0.0
         *
         *  @param	$name (string)
         *  @param	$value (mixed)
         *  @return	n/a
         */

        function update_setting($name, $value) {

            $this->settings[$name] = $value;

            return true;
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
        global $pdc;
        if (!isset($pdc)) {
            $pdc = new pdc();
            $pdc->initialize();
        }
        return $pdc;
    }

// initialize
    pdc();

endif; // class_exists check
//Adding Option Page

include_once 'custom/option_page_creator.php';
?>