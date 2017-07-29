<?php 

// vars
$field = array(
	'label'		=> __('Select Field Groups', 'pdc'),
	'type'		=> 'checkbox',
	'name'		=> 'pdc_export_keys',
	'prefix'	=> false,
	'value'		=> false,
	'toggle'	=> true,
	'choices'	=> array(),
);

$field_groups = pdc_get_field_groups();


// populate choices
if( $field_groups ) {
	
	foreach( $field_groups as $field_group ) {
		
		$field['choices'][ $field_group['key'] ] = $field_group['title'];
		
	}
	
}

?>
<div class="wrap pdc-settings-wrap">
	
	<h1><?php _e('Tools', 'pdc'); ?></h1>
	
	<div class="pdc-box" id="pdc-export-field-groups">
		<div class="title">
			<h3><?php _e('Export Field Groups', 'pdc'); ?></h3>
		</div>
		<div class="inner">
			<p><?php _e('Select the field groups you would like to export and then select your export method. Use the download button to export to a .json file which you can then import to another pdc installation. Use the generate button to export to PHP code which you can place in your theme.', 'pdc'); ?></p>
			
			<form method="post" action="">
			<div class="pdc-hidden">
				<input type="hidden" name="_pdcnonce" value="<?php echo wp_create_nonce( 'export' ); ?>" />
			</div>
			<table class="form-table">
                <tbody>
	                <?php pdc_render_field_wrap( $field, 'tr' ); ?>
					<tr>
						<th></th>
						<td>
							<input type="submit" name="download" class="button button-primary" value="<?php _e('Download export file', 'pdc'); ?>" />
							<input type="submit" name="generate" class="button button-primary" value="<?php _e('Generate export code', 'pdc'); ?>" />
						</td>
					</tr>
				</tbody>
			</table>
			</form>
            
		</div>
	</div>

	
	<div class="pdc-box">
		<div class="title">
			<h3><?php _e('Import Field Groups', 'pdc'); ?></h3>
		</div>
		<div class="inner">
			<p><?php _e('Select the Advanced Custom Fields JSON file you would like to import. When you click the import button below, pdc will import the field groups.', 'pdc'); ?></p>
			
			<form method="post" action="" enctype="multipart/form-data">
			<div class="pdc-hidden">
				<input type="hidden" name="_pdcnonce" value="<?php echo wp_create_nonce( 'import' ); ?>" />
			</div>
			<table class="form-table">
                <tbody>
                	<tr>
                    	<th>
                    		<label><?php _e('Select File', 'pdc'); ?></label>
                    	</th>
						<td>
							<input type="file" name="pdc_import_file">
						</td>
					</tr>
					<tr>
						<th></th>
						<td>
							<input type="submit" class="button button-primary" value="<?php _e('Import', 'pdc'); ?>" />
						</td>
					</tr>
				</tbody>
			</table>
			</form>
			
		</div>
		
		
	</div>
	
</div>