<?php 

// vars
$rule_types = apply_filters('pdc/location/rule_types', array(
	__("Post",'pdc') => array(
		'post_type'		=>	__("Post Type",'pdc'),
		'post_template'	=>	__("Post Template",'pdc'),
		'post_status'	=>	__("Post Status",'pdc'),
		'post_format'	=>	__("Post Format",'pdc'),
		'post_category'	=>	__("Post Category",'pdc'),
		'post_taxonomy'	=>	__("Post Taxonomy",'pdc'),
		'post'			=>	__("Post",'pdc')
	),
	__("Page",'pdc') => array(
		'page_template'	=>	__("Page Template",'pdc'),
		'page_type'		=>	__("Page Type",'pdc'),
		'page_parent'	=>	__("Page Parent",'pdc'),
		'page'			=>	__("Page",'pdc')
	),
	__("User",'pdc') => array(
		'current_user'		=>	__("Current User",'pdc'),
		'current_user_role'	=>	__("Current User Role",'pdc'),
		'user_form'			=>	__("User Form",'pdc'),
		'user_role'			=>	__("User Role",'pdc')
	),
	__("Forms",'pdc') => array(
		'attachment'	=>	__("Attachment",'pdc'),
		'taxonomy'		=>	__("Taxonomy Term",'pdc'),
		'comment'		=>	__("Comment",'pdc'),
		'widget'		=>	__("Widget",'pdc')
	)
));


// WP < 4.7
if( pdc_version_compare('wp', '<', '4.7') ) {
	
	unset( $rule_types[ __("Post",'pdc') ]['post_template'] );
	
}

$rule_operators = apply_filters( 'pdc/location/rule_operators', array(
	'=='	=>	__("is equal to",'pdc'),
	'!='	=>	__("is not equal to",'pdc'),
));
						
?>
<div class="pdc-field">
	<div class="pdc-label">
		<label><?php _e("Rules",'pdc'); ?></label>
		<p class="description"><?php _e("Create a set of rules to determine which edit screens will use these advanced custom fields",'pdc'); ?></p>
	</div>
	<div class="pdc-input">
		<div class="rule-groups">
			
			<?php foreach( $field_group['location'] as $group_id => $group ): 
				
				// validate
				if( empty($group) ) {
				
					continue;
					
				}
				
				
				// $group_id must be completely different to $rule_id to avoid JS issues
				$group_id = "group_{$group_id}";
				$h4 = ($group_id == "group_0") ? __("Show this field group if",'pdc') : __("or",'pdc');
				
				?>
			
				<div class="rule-group" data-id="<?php echo $group_id; ?>">
				
					<h4><?php echo $h4; ?></h4>
					
					<table class="pdc-table -clear">
						<tbody>
							<?php foreach( $group as $rule_id => $rule ): 
								
								// valid rule
								$rule = wp_parse_args( $rule, array(
									'field'		=>	'',
									'operator'	=>	'==',
									'value'		=>	'',
								));
								
															
								// $group_id must be completely different to $rule_id to avoid JS issues
								$rule_id = "rule_{$rule_id}";
								
								?>
								<tr data-id="<?php echo $rule_id; ?>">
								<td class="param"><?php 
									
									// create field
									pdc_render_field(array(
										'type'		=> 'select',
										'prefix'	=> "pdc_field_group[location][{$group_id}][{$rule_id}]",
										'name'		=> 'param',
										'value'		=> $rule['param'],
										'choices'	=> $rule_types,
										'class'		=> 'location-rule-param'
									));
		
								?></td>
								<td class="operator"><?php 	
									
									// create field
									pdc_render_field(array(
										'type'		=> 'select',
										'prefix'	=> "pdc_field_group[location][{$group_id}][{$rule_id}]",
										'name'		=> 'operator',
										'value'		=> $rule['operator'],
										'choices' 	=> $rule_operators,
										'class'		=> 'location-rule-operator'
									)); 	
									
								?></td>
								<td class="value"><?php 
									
									$this->render_location_value(array(
										'group_id'	=> $group_id,
										'rule_id'	=> $rule_id,
										'value'		=> $rule['value'],
										'param'		=> $rule['param'],
										'class'		=> 'location-rule-value'
									)); 
									
								?></td>
								<td class="add">
									<a href="#" class="button add-location-rule"><?php _e("and",'pdc'); ?></a>
								</td>
								<td class="remove">
									<a href="#" class="pdc-icon -minus remove-location-rule"></a>
								</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
					
				</div>
			<?php endforeach; ?>
			
			<h4><?php _e("or",'pdc'); ?></h4>
			
			<a href="#" class="button add-location-group"><?php _e("Add rule group",'pdc'); ?></a>
			
		</div>
	</div>
</div>
<script type="text/javascript">
if( typeof pdc !== 'undefined' ) {
		
	pdc.postbox.render({
		'id': 'pdc-field-group-locations',
		'label': 'left'
	});	

}
</script>