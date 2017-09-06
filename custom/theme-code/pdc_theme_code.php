<?php
// Exit if accessed directly
//if (!defined('ABSPATH'))    exit;

// Define the version number
define('PDCTC_VERSION', '1.3.2');

// Check for dashboard or admin panel
if (is_admin()) {

    /**
     * Classes
     */
    include('core/core.php');
    include('core/group.php');
    include('core/field.php');

    /**
     * Single function for accessing plugin core instance
     *
     * @return pdcTC_Core
     */
    function pdctc() {
        static $instance;
        if (!$instance)
            $instance = new pdcTC_Core(plugin_dir_path(__FILE__), plugin_dir_url(__FILE__), plugin_basename(__FILE__), PDCTC_VERSION);
        return $instance;
    }
    //dump(plugin_dir_path(__FILE__));
    pdctc(); // kickoff
}
