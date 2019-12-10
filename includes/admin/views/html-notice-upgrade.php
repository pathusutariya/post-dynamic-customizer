<?php 

// calculate add-ons (non pro only)
$plugins = array();

if( !pdc_get_setting('pro') ) {
	
	if( is_plugin_active('pdc-repeater/pdc-repeater.php') ) $plugins[] = __("Repeater",'pdc');
	if( is_plugin_active('pdc-flexible-content/pdc-flexible-content.php') ) $plugins[] = __("Flexible Content",'pdc');
	if( is_plugin_active('pdc-gallery/pdc-gallery.php') ) $plugins[] = __("Gallery",'pdc');
	if( is_plugin_active('pdc-options-page/pdc-options-page.php') ) $plugins[] = __("Options Page",'pdc');
	
}
?>