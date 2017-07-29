<?php 

// vars
$button_url = '';
$button_text = '';
$confirm = true;


// extract
extract($args);


// calculate add-ons (non pro only)
$plugins = array();

if( !pdc_get_setting('pro') ) {
	
	if( is_plugin_active('pdc-repeater/pdc-repeater.php') ) $plugins[] = __("Repeater",'pdc');
	if( is_plugin_active('pdc-flexible-content/pdc-flexible-content.php') ) $plugins[] = __("Flexible Content",'pdc');
	if( is_plugin_active('pdc-gallery/pdc-gallery.php') ) $plugins[] = __("Gallery",'pdc');
	if( is_plugin_active('pdc-options-page/pdc-options-page.php') ) $plugins[] = __("Options Page",'pdc');
	
}

?>
<div id="pdc-upgrade-notice" class="pdc-cf">
	
	<div class="inner">
		
		<div class="pdc-icon logo">
			<i class="pdc-sprite-logo"></i>
		</div>
		
		<div class="content">
			
			<h2><?php _e("Database Upgrade Required",'pdc'); ?></h2>
			
			<p><?php printf(__("Thank you for updating to %s v%s!", 'pdc'), pdc_get_setting('name'), pdc_get_setting('version') ); ?><br /><?php _e("Before you start using the new awesome features, please update your database to the newest version.", 'pdc'); ?></p>
			
			<?php if( !empty($plugins) ): ?>
				<p><?php printf(__("Please also ensure any premium add-ons (%s) have first been updated to the latest version.", 'pdc'), implode(', ', $plugins) ); ?></p>
			<?php endif; ?>
			
			<p><a id="pdc-notice-action" href="<?php echo $button_url; ?>" class="button button-primary"><?php echo $button_text; ?></a></p>
			
		<?php if( $confirm ): ?>
			<script type="text/javascript">
			(function($) {
				
				$("#pdc-notice-action").on("click", function(){
			
					var answer = confirm("<?php _e( 'It is strongly recommended that you backup your database before proceeding. Are you sure you wish to run the updater now?', 'pdc' ); ?>");
					return answer;
			
				});
				
			})(jQuery);
			</script>
		<?php endif; ?>
		
		</div>
		
	</div>
	
</div>