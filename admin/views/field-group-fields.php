<?php 

// vars
$fields = false;
$parent = 0;


// use fields if passed in
extract( $args );

?>
<div class="pdc-field-list-wrap">
	
	<ul class="pdc-hl pdc-thead">
		<li class="li-field-order"><?php _e('Order','pdc'); ?></li>
		<li class="li-field-label"><?php _e('Label','pdc'); ?></li>
		<li class="li-field-name"><?php _e('Name','pdc'); ?></li>
		<li class="li-field-type"><?php _e('Type','pdc'); ?></li>
	</ul>
	
	<div class="pdc-field-list">
		
		<div class="no-fields-message" <?php if( $fields ){ echo 'style="display:none;"'; } ?>>
			<?php _e("No fields. Click the <strong>+ Add Field</strong> button to create your first field.",'pdc'); ?>
		</div>
		
		<?php if( $fields ):
			
			foreach( $fields as $i => $field ):
				
				pdc_get_view('field-group-field', array( 'field' => $field, 'i' => $i ));
				
			endforeach;
		
		endif; ?>
		
	</div>
	
	<ul class="pdc-hl pdc-tfoot">
		<li class="pdc-fr">
			<a href="#" class="button button-primary button-large add-field"><?php _e('+ Add Field','pdc'); ?></a>
		</li>
	</ul>
	
<?php if( !$parent ):
	
	// get clone
	$clone = pdc_get_valid_field(array(
		'ID'		=> 'pdccloneindex',
		'key'		=> 'pdccloneindex',
		'label'		=> __('New Field','pdc'),
		'name'		=> 'new_field',
		'type'		=> 'text'
	));
	
	?>
	<script type="text/html" id="tmpl-pdc-field">
	<?php pdc_get_view('field-group-field', array( 'field' => $clone )); ?>
	</script>
<?php endif;?>
	
</div>