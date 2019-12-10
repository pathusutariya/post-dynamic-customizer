<?php 

// vars
$prefix = 'pdc_field_group[location]['.$rule['group'].']['.$rule['id'].']';

?>
<tr data-id="<?php echo $rule['id']; ?>">
	<td class="param">
		<?php 
		
		// vars
		$choices = pdc_get_location_rule_types();
		
		
		// array
		if( is_array($choices) ) {
			
			pdc_render_field(array(
				'type'		=> 'select',
				'name'		=> 'param',
				'prefix'	=> $prefix,
				'value'		=> $rule['param'],
				'choices'	=> $choices,
				'class'		=> 'refresh-location-rule'
			));
		
		}
		
		?>
	</td>
	<td class="operator">
		<?php 
		
		// vars
		$choices = pdc_get_location_rule_operators( $rule );
		
		
		// array
		if( is_array($choices) ) {
			
			pdc_render_field(array(
				'type'		=> 'select',
				'name'		=> 'operator',
				'prefix'	=> $prefix,
				'value'		=> $rule['operator'],
				'choices'	=> $choices
			));
		
		// custom	
		} else {
			
			echo $choices;
			
		}
	
		?>
	</td>
	<td class="value">
		<?php
		
		// vars
		$choices = pdc_get_location_rule_values( $rule );
		
		
		// array
		if( is_array($choices) ) {
			
			pdc_render_field(array(
				'type'		=> 'select',
				'name'		=> 'value',
				'prefix'	=> $prefix,
				'value'		=> $rule['value'],
				'choices'	=> $choices
			));
		
		// custom	
		} else {
			
			echo $choices;
			
		}
		
		?>
	</td>
	<td class="add">
		<a href="#" class="button add-location-rule"><?php _e("and",'pdc'); ?></a>
	</td>
	<td class="remove">
		<a href="#" class="pdc-icon -minus remove-location-rule"></a>
	</td>
</tr>