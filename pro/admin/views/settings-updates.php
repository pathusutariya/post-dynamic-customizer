<?php 

// extract
extract($args);


// vars
$active = $license ? true : false;
$nonce = $active ? 'deactivate_pro_licence' : 'activate_pro_licence';
$input = $active ? 'password' : 'text';
$button = $active ? __('Deactivate License', 'pdc') : __('Activate License', 'pdc');
$readonly = $active ? 1 : 0;

?>
<div class="wrap pdc-settings-wrap">
	
	<h1><?php _e('Updates', 'pdc'); ?></h1>
	
	<div class="pdc-box" id="pdc-license-information">
		<div class="title">
			<h3><?php _e('License Information', 'pdc'); ?></h3>
		</div>
		<div class="inner">
			<p><?php printf(__('To unlock updates, please enter your license key below. If you don\'t have a licence key, please see <a href="%s" target="_blank">details & pricing</a>.','pdc'), esc_url('https://www.advancedcustomfields.com/pro')); ?></p>
			<form action="" method="post">
			<div class="pdc-hidden">
				<input type="hidden" name="_pdcnonce" value="<?php echo wp_create_nonce( $nonce ); ?>" />
			</div>
			<table class="form-table">
                <tbody>
                	<tr>
                    	<th>
                    		<label for="pdc-field-pdc_pro_licence"><?php _e('License Key', 'pdc'); ?></label>
                    	</th>
						<td>
							<?php 
							
							// render field
							pdc_render_field(array(
								'type'		=> $input,
								'name'		=> 'pdc_pro_licence',
								'value'		=> str_repeat('*', strlen($license)),
								'readonly'	=> $readonly
							));
							
							?>
						</td>
					</tr>
					<tr>
						<th></th>
						<td>
							<input type="submit" value="<?php echo $button; ?>" class="button button-primary">
						</td>
					</tr>
				</tbody>
			</table>
			</form>
            
		</div>
		
	</div>
	
	<div class="pdc-box" id="pdc-update-information">
		<div class="title">
			<h3><?php _e('Update Information', 'pdc'); ?></h3>
		</div>
		<div class="inner">
			<table class="form-table">
                <tbody>
                	<tr>
                    	<th>
                    		<label><?php _e('Current Version', 'pdc'); ?></label>
                    	</th>
						<td>
							<?php echo $current_version; ?>
						</td>
					</tr>
					<tr>
                    	<th>
                    		<label><?php _e('Latest Version', 'pdc'); ?></label>
                    	</th>
						<td>
							<?php echo $remote_version; ?>
						</td>
					</tr>
					<tr>
                    	<th>
                    		<label><?php _e('Update Available', 'pdc'); ?></label>
                    	</th>
						<td>
							<?php if( $update_available ): ?>
								
								<span style="margin-right: 5px;"><?php _e('Yes', 'pdc'); ?></span>
								
								<?php if( $active ): ?>
									<a class="button button-primary" href="<?php echo admin_url('plugins.php?s=Advanced+Custom+Fields+Pro'); ?>"><?php _e('Update Plugin', 'pdc'); ?></a>
								<?php else: ?>
									<a class="button" disabled="disabled" href="#"><?php _e('Please enter your license key above to unlock updates', 'pdc'); ?></a>
								<?php endif; ?>
								
							<?php else: ?>
								
								<span style="margin-right: 5px;"><?php _e('No', 'pdc'); ?></span>
								<a class="button" href="<?php echo add_query_arg('force-check', 1); ?>"><?php _e('Check Again', 'pdc'); ?></a>
							<?php endif; ?>
						</td>
					</tr>
					<?php if( $changelog ): ?>
					<tr>
                    	<th>
                    		<label><?php _e('Changelog', 'pdc'); ?></label>
                    	</th>
						<td>
							<?php echo $changelog; ?>
						</td>
					</tr>
					<?php endif; ?>
					<?php if( $upgrade_notice ): ?>
					<tr>
                    	<th>
                    		<label><?php _e('Upgrade Notice', 'pdc'); ?></label>
                    	</th>
						<td>
							<?php echo $upgrade_notice; ?>
						</td>
					</tr>
					<?php endif; ?>
				</tbody>
			</table>
			</form>
            
		</div>
		
		
	</div>
	
</div>
<style type="text/css">
	#pdc_pro_licence {
		width: 75%;
	}
	
	#pdc-update-information td h4 {
		display: none;
	}
</style>