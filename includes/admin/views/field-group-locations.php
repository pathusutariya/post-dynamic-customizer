<?php

// global
global $field_group;

?>
<div class="pdc-field">
	<div class="pdc-label">
		<label><?php _e("Rules",'pdc'); ?></label>
		<p class="description"><?php _e("Create a set of rules to determine which edit screens will use these advanced custom fields",'pdc'); ?></p>
	</div>
	<div class="pdc-input">
		<div class="rule-groups">
			
			<?php foreach( $field_group['location'] as $i => $group ): 
				
				// bail ealry if no group
				if( empty($group) ) return;
				
				
				// view
				pdc_get_view('html-location-group', array(
					'group'		=> $group,
					'group_id'	=> "group_{$i}"
				));
			
			endforeach;	?>
			
			<h4><?php _e("or",'pdc'); ?></h4>
			
			<a href="#" class="button add-location-group"><?php _e("Add rule group",'pdc'); ?></a>
			
		</div>
	</div>
</div>
<script type="text/javascript">
if( typeof pdc !== 'undefined' ) {
		
	pdc.newPostbox({
		'id': 'pdc-field-group-locations',
		'label': 'left'
	});	

}
</script>