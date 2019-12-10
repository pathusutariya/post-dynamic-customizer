<?php

if( ! class_exists('pdc_field_repeater') ) :

class pdc_field_repeater extends pdc_field {
	
	
	/*
	*  __construct
	*
	*  This function will setup the field type data
	*
	*  @type	function
	*  @date	5/03/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function initialize() {
		
		// vars
		$this->name = 'repeater';
		$this->label = __("Repeater",'pdc');
		$this->category = 'layout';
		$this->defaults = array(
			'sub_fields'	=> array(),
			'min'			=> 0,
			'max'			=> 0,
			'layout' 		=> 'table',
			'button_label'	=> '',
			'collapsed'		=> ''
		);
		
		
		// field filters
		$this->add_field_filter('pdc/prepare_field_for_export', array($this, 'prepare_field_for_export'));
		$this->add_field_filter('pdc/prepare_field_for_import', array($this, 'prepare_field_for_import'));
		
		
		// filters
		$this->add_filter('pdc/validate_field',	 array($this, 'validate_any_field'));
		
	}
	
	
	/*
	*  input_admin_enqueue_scripts
	*
	*  description
	*
	*  @type	function
	*  @date	16/12/2015
	*  @since	5.3.2
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function input_admin_enqueue_scripts() {
		
		// localize
		pdc_localize_text(array(
		   	'Minimum rows reached ({min} rows)'	=> __('Minimum rows reached ({min} rows)', 'pdc'),
			'Maximum rows reached ({max} rows)'	=> __('Maximum rows reached ({max} rows)', 'pdc'),
	   	));
	}
	
	
	/*
	*  load_field()
	*
	*  This filter is appied to the $field after it is loaded from the database
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field - the field array holding all the field options
	*
	*  @return	$field - the field array holding all the field options
	*/
	
	function load_field( $field ) {
		
		// min/max
		$field['min'] = (int) $field['min'];
		$field['max'] = (int) $field['max'];
		
		
		// vars
		$sub_fields = pdc_get_fields( $field );
		
		
		// append
		if( $sub_fields ) {
			
			$field['sub_fields'] = $sub_fields;
			
		}
				
		
		// return
		return $field;
		
	}
	
	
	/*
	*  render_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field - an array holding all the field's data
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/
	
	function render_field( $field ) {
		
		// vars
		$sub_fields = $field['sub_fields'];
		$show_order = true;
		$show_add = true;
		$show_remove = true;
		
		
		// bail early if no sub fields
		if( empty($sub_fields) ) return;
		
		
		// value
		$value = is_array($field['value']) ? $field['value'] : array();
		
		
		// div
		$div = array(
			'class' 		=> 'pdc-repeater',
			'data-min' 		=> $field['min'],
			'data-max'		=> $field['max']
		);
		
		
		// empty
		if( empty($value) ) {
			
			$div['class'] .= ' -empty';
			
		}
		
		
		// If there are less values than min, populate the extra values
		if( $field['min'] ) {
			
			$value = array_pad($value, $field['min'], array());
			
		}
		
		
		// If there are more values than man, remove some values
		if( $field['max'] ) {
			
			$value = array_slice($value, 0, $field['max']);
			
			
			// if max 1 row, don't show order
			if( $field['max'] == 1 ) {
			
				$show_order = false;
				
			}
			
			
			// if max == min, don't show add or remove buttons
			if( $field['max'] <= $field['min'] ) {
			
				$show_remove = false;
				$show_add = false;
				
			}
			
		}
		
		
		// setup values for row clone
		$value['pdccloneindex'] = array();
		
		
		// button label
		if( $field['button_label'] === '' ) $field['button_label'] = __('Add Row', 'pdc');
		
		
		// field wrap
		$el = 'td';
		$before_fields = '';
		$after_fields = '';
		
		if( $field['layout'] == 'row' ) {
		
			$el = 'div';
			$before_fields = '<td class="pdc-fields -left">';
			$after_fields = '</td>';
			
		} elseif( $field['layout'] == 'block' ) {
		
			$el = 'div';
			
			$before_fields = '<td class="pdc-fields">';
			$after_fields = '</td>';
			
		}
		
		
		// layout
		$div['class'] .= ' -' . $field['layout'];
		
		
		// collapsed
		if( $field['collapsed'] ) {
			
			// loop
			foreach( $sub_fields as &$sub_field ) {
				
				// add target class
				if( $sub_field['key'] == $field['collapsed'] ) {
					$sub_field['wrapper']['class'] .= ' -collapsed-target';
				}
			}
			unset( $sub_field );
		}
		
?>
<div <?php pdc_esc_attr_e( $div ); ?>>
	<?php pdc_hidden_input(array( 'name' => $field['name'], 'value' => '' )); ?>
<table class="pdc-table">
	
	<?php if( $field['layout'] == 'table' ): ?>
		<thead>
			<tr>
				<?php if( $show_order ): ?>
					<th class="pdc-row-handle"></th>
				<?php endif; ?>
				
				<?php foreach( $sub_fields as $sub_field ): 
					
					// prepare field (allow sub fields to be removed)
					$sub_field = pdc_prepare_field($sub_field);
					
					
					// bail ealry if no field
					if( !$sub_field ) continue;
					
					
					// vars
					$atts = array();
					$atts['class'] = 'pdc-th';
					$atts['data-name'] = $sub_field['_name'];
					$atts['data-type'] = $sub_field['type'];
					$atts['data-key'] = $sub_field['key'];
					
					
					// Add custom width
					if( $sub_field['wrapper']['width'] ) {
					
						$atts['data-width'] = $sub_field['wrapper']['width'];
						$atts['style'] = 'width: ' . $sub_field['wrapper']['width'] . '%;';
						
					}
					
					?>
					<th <?php echo pdc_esc_attr( $atts ); ?>>
						<?php echo pdc_get_field_label( $sub_field ); ?>
						<?php if( $sub_field['instructions'] ): ?>
							<p class="description"><?php echo $sub_field['instructions']; ?></p>
						<?php endif; ?>
					</th>
				<?php endforeach; ?>

				<?php if( $show_remove ): ?>
					<th class="pdc-row-handle"></th>
				<?php endif; ?>
			</tr>
		</thead>
	<?php endif; ?>
	
	<tbody>
		<?php foreach( $value as $i => $row ): ?>
			<tr class="pdc-row<?php if( $i === 'pdccloneindex' ){ echo ' pdc-clone'; } ?>" data-id="<?php echo $i; ?>">
				
				<?php if( $show_order ): ?>
					<td class="pdc-row-handle order" title="<?php _e('Drag to reorder','pdc'); ?>">
						<?php if( $field['collapsed'] ): ?>
						<a class="pdc-icon -collapse small" href="#" data-event="collapse-row" title="<?php _e('Click to toggle','pdc'); ?>"></a>
						<?php endif; ?>
						<span><?php echo intval($i) + 1; ?></span>
					</td>
				<?php endif; ?>
				
				<?php echo $before_fields; ?>
				
				<?php foreach( $sub_fields as $sub_field ): 
					
					// add value
					if( isset($row[ $sub_field['key'] ]) ) {
						
						// this is a normal value
						$sub_field['value'] = $row[ $sub_field['key'] ];
						
					} elseif( isset($sub_field['default_value']) ) {
						
						// no value, but this sub field has a default value
						$sub_field['value'] = $sub_field['default_value'];
						
					}
					
					
					// update prefix to allow for nested values
					$sub_field['prefix'] = $field['name'] . '[' . $i . ']';
					
					
					// render input
					pdc_render_field_wrap( $sub_field, $el ); ?>
					
				<?php endforeach; ?>
				
				<?php echo $after_fields; ?>
				
				<?php if( $show_remove ): ?>
					<td class="pdc-row-handle remove">
						<a class="pdc-icon -plus small pdc-js-tooltip" href="#" data-event="add-row" title="<?php _e('Add row','pdc'); ?>"></a>
						<a class="pdc-icon -minus small pdc-js-tooltip" href="#" data-event="remove-row" title="<?php _e('Remove row','pdc'); ?>"></a>
					</td>
				<?php endif; ?>
				
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php if( $show_add ): ?>
	
	<div class="pdc-actions">
		<a class="pdc-button button button-primary" href="#" data-event="add-row"><?php echo $field['button_label']; ?></a>
	</div>
			
<?php endif; ?>
</div>
<?php
		
	}
	
	
	/*
	*  render_field_settings()
	*
	*  Create extra options for your field. This is rendered when editing a field.
	*  The value of $field['name'] can be used (like bellow) to save extra data to the $field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field	- an array holding all the field's data
	*/
	
	function render_field_settings( $field ) {
		
		// vars
		$args = array(
			'fields'	=> $field['sub_fields'],
			'parent'	=> $field['ID']
		);
		
		
		?><tr class="pdc-field pdc-field-setting-sub_fields" data-setting="repeater" data-name="sub_fields">
			<td class="pdc-label">
				<label><?php _e("Sub Fields",'pdc'); ?></label>
				<p class="description"></p>		
			</td>
			<td class="pdc-input">
				<?php 
				
				pdc_get_view('field-group-fields', $args);
				
				?>
			</td>
		</tr>
		<?php
		
		
		// rows
		$field['min'] = empty($field['min']) ? '' : $field['min'];
		$field['max'] = empty($field['max']) ? '' : $field['max'];
		
		
		// collapsed
		$choices = array();
		if( $field['collapsed'] ) {
			
			// load sub field
			$sub_field = pdc_get_field($field['collapsed']);
			
			// append choice
			if( $sub_field ) {
				$choices[ $sub_field['key'] ] = $sub_field['label'];
			}
		}
		
		pdc_render_field_setting( $field, array(
			'label'			=> __('Collapsed','pdc'),
			'instructions'	=> __('Select a sub field to show when row is collapsed','pdc'),
			'type'			=> 'select',
			'name'			=> 'collapsed',
			'allow_null'	=> 1,
			'choices'		=> $choices
		));
		
		
		// min
		pdc_render_field_setting( $field, array(
			'label'			=> __('Minimum Rows','pdc'),
			'instructions'	=> '',
			'type'			=> 'number',
			'name'			=> 'min',
			'placeholder'	=> '0',
		));
		
		
		// max
		pdc_render_field_setting( $field, array(
			'label'			=> __('Maximum Rows','pdc'),
			'instructions'	=> '',
			'type'			=> 'number',
			'name'			=> 'max',
			'placeholder'	=> '0',
		));
		
		
		// layout
		pdc_render_field_setting( $field, array(
			'label'			=> __('Layout','pdc'),
			'instructions'	=> '',
			'class'			=> 'pdc-repeater-layout',
			'type'			=> 'radio',
			'name'			=> 'layout',
			'layout'		=> 'horizontal',
			'choices'		=> array(
				'table'			=> __('Table','pdc'),
				'block'			=> __('Block','pdc'),
				'row'			=> __('Row','pdc')
			)
		));
		
		
		// button_label
		pdc_render_field_setting( $field, array(
			'label'			=> __('Button Label','pdc'),
			'instructions'	=> '',
			'type'			=> 'text',
			'name'			=> 'button_label',
			'placeholder'	=> __('Add Row','pdc')
		));
		
	}
	
	
	/*
	*  load_value()
	*
	*  This filter is applied to the $value after it is loaded from the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value (mixed) the value found in the database
	*  @param	$post_id (mixed) the $post_id from which the value was loaded
	*  @param	$field (array) the field array holding all the field options
	*  @return	$value
	*/
	
	function load_value( $value, $post_id, $field ) {
		
		// bail early if no value
		if( empty($value) ) return false;
		
		
		// bail ealry if not numeric
		if( !is_numeric($value) ) return false;
		
		
		// bail early if no sub fields
		if( empty($field['sub_fields']) ) return false;
		
		
		// vars
		$value = intval($value);
		$rows = array();
		
		
		// loop
		for( $i = 0; $i < $value; $i++ ) {
			
			// create empty array
			$rows[ $i ] = array();
			
			
			// loop through sub fields
			foreach( array_keys($field['sub_fields']) as $j ) {
				
				// get sub field
				$sub_field = $field['sub_fields'][ $j ];
				
				
				// bail ealry if no name (tab)
				if( pdc_is_empty($sub_field['name']) ) continue;
				
				
				// update $sub_field name
				$sub_field['name'] = "{$field['name']}_{$i}_{$sub_field['name']}";
				
				
				// get value
				$sub_value = pdc_get_value( $post_id, $sub_field );
			
			
				// add value
				$rows[ $i ][ $sub_field['key'] ] = $sub_value;
				
			}
			
		}
		
		
		// return
		return $rows;
		
	}
	
	
	/*
	*  format_value()
	*
	*  This filter is appied to the $value after it is loaded from the db and before it is returned to the template
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value (mixed) the value which was loaded from the database
	*  @param	$post_id (mixed) the $post_id from which the value was loaded
	*  @param	$field (array) the field array holding all the field options
	*
	*  @return	$value (mixed) the modified value
	*/
	
	function format_value( $value, $post_id, $field ) {
		
		// bail early if no value
		if( empty($value) ) return false;
		
		
		// bail ealry if not array
		if( !is_array($value) ) return false;
		
		
		// bail early if no sub fields
		if( empty($field['sub_fields']) ) return false;
		
		
		// loop over rows
		foreach( array_keys($value) as $i ) {
			
			// loop through sub fields
			foreach( array_keys($field['sub_fields']) as $j ) {
				
				// get sub field
				$sub_field = $field['sub_fields'][ $j ];
				
				
				// bail ealry if no name (tab)
				if( pdc_is_empty($sub_field['name']) ) continue;
				
				
				// extract value
				$sub_value = pdc_extract_var( $value[ $i ], $sub_field['key'] );
				
				
				// update $sub_field name
				$sub_field['name'] = "{$field['name']}_{$i}_{$sub_field['name']}";
				
				
				// format value
				$sub_value = pdc_format_value( $sub_value, $post_id, $sub_field );
				
				
				// append to $row
				$value[ $i ][ $sub_field['_name'] ] = $sub_value;
				
			}
			
		}
		
		
		// return
		return $value;
		
	}
	
	
	/*
	*  validate_value
	*
	*  description
	*
	*  @type	function
	*  @date	11/02/2014
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function validate_value( $valid, $value, $field, $input ){
		
		// vars
		$count = 0;
		
		
		// check if is value (may be empty string)
		if( is_array($value) ) {
			
			// remove pdccloneindex
			if( isset($value['pdccloneindex']) ) {
				unset($value['pdccloneindex']);
			}
			
			// count
			$count = count($value);
		}
		
		
		// validate required
		if( $field['required'] && !$count ) {
			$valid = false;
		}
		
		
		// min
		$min = (int) $field['min'];
		if( $min && $count < $min ) {
			
			// create error
			$error = __('Minimum rows reached ({min} rows)', 'pdc');
 			$error = str_replace('{min}', $min, $error);
 			
 			// return
			return $error;
		}
		
		
		// validate value
		if( $count ) {
			
			// bail early if no sub fields
			if( !$field['sub_fields'] ) {
				return $valid;
			}
			
			// loop rows
			foreach( $value as $i => $row ) {
				
				// loop sub fields
				foreach( $field['sub_fields'] as $sub_field ) {
					
					// vars
					$k = $sub_field['key'];
					
					// test sub field exists
					if( !isset($row[ $k ]) ) {
						continue;
					}
					
					// validate
					pdc_validate_value( $row[ $k ], $sub_field, "{$input}[{$i}][{$k}]" );
				}
				// end loop sub fields
			}
			// end loop rows
		}
		
		
		// return
		return $valid;
	}
	
	
	/*
	*  update_row
	*
	*  This function will update a value row
	*
	*  @type	function
	*  @date	15/2/17
	*  @since	5.5.8
	*
	*  @param	$i (int)
	*  @param	$field (array)
	*  @param	$post_id (mixed)
	*  @return	(boolean)
	*/
	
	function update_row( $row, $i = 0, $field, $post_id ) {
		
		// bail early if no layout reference
		if( !is_array($row) ) return false;
		
		
		// bail early if no layout
		if( empty($field['sub_fields']) ) return false;
		
		
		// loop
		foreach( $field['sub_fields'] as $sub_field ) {
			
			// value
			$value = null;
			
			
			// find value (key)
			if( isset($row[ $sub_field['key'] ]) ) {
				
				$value = $row[ $sub_field['key'] ];
			
			// find value (name)	
			} elseif( isset($row[ $sub_field['name'] ]) ) {
				
				$value = $row[ $sub_field['name'] ];
				
			// value does not exist	
			} else {
				
				continue;
				
			}
			
			
			// modify name for save
			$sub_field['name'] = "{$field['name']}_{$i}_{$sub_field['name']}";
						
			
			// update field
			pdc_update_value( $value, $post_id, $sub_field );
				
		}
		
		
		// return
		return true;
		
	}
	
	
	/*
	*  delete_row
	*
	*  This function will delete a value row
	*
	*  @type	function
	*  @date	15/2/17
	*  @since	5.5.8
	*
	*  @param	$i (int)
	*  @param	$field (array)
	*  @param	$post_id (mixed)
	*  @return	(boolean)
	*/
	
	function delete_row( $i = 0, $field, $post_id ) {
		
		// bail early if no sub fields
		if( empty($field['sub_fields']) ) return false;
		
		
		// loop
		foreach( $field['sub_fields'] as $sub_field ) {
			
			// modify name for delete
			$sub_field['name'] = "{$field['name']}_{$i}_{$sub_field['name']}";
			
			
			// delete value
			pdc_delete_value( $post_id, $sub_field );
			
		}
		
		
		// return
		return true;
		
	}
	
	
	/*
	*  update_value()
	*
	*  This filter is appied to the $value before it is updated in the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value - the value which will be saved in the database
	*  @param	$field - the field array holding all the field options
	*  @param	$post_id - the $post_id of which the value will be saved
	*
	*  @return	$value - the modified value
	*/
	
	function update_value( $value, $post_id, $field ) {
		
		// bail early if no sub fields
		if( empty($field['sub_fields']) ) return $value;
		
		
		// vars
		$new_value = 0;
		$old_value = (int) pdc_get_metadata( $post_id, $field['name'] );
		
		
		// update sub fields
		if( !empty($value) ) { $i = -1;
			
			// remove pdccloneindex
			if( isset($value['pdccloneindex']) ) {
			
				unset($value['pdccloneindex']);
				
			}
			
			// loop through rows
			foreach( $value as $row ) {	$i++;
				
				// bail early if no row
				if( !is_array($row) ) continue;
				
				
				// update row
				$this->update_row( $row, $i, $field, $post_id );
				
				
				// append
				$new_value++;
				
			}
			
		}
		
		
		// remove old rows
		if( $old_value > $new_value ) {
			
			// loop
			for( $i = $new_value; $i < $old_value; $i++ ) {
				
				$this->delete_row( $i, $field, $post_id );
				
			}
			
		}
		
		
		// save false for empty value
		if( empty($new_value) ) $new_value = '';
		
		
		// return
		return $new_value;
	}
	
	
	/*
	*  delete_value
	*
	*  description
	*
	*  @type	function
	*  @date	1/07/2015
	*  @since	5.2.3
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function delete_value( $post_id, $key, $field ) {
		
		// get old value (db only)
		$old_value = (int) pdc_get_metadata( $post_id, $field['name'] );
		
		
		// bail early if no rows or no sub fields
		if( !$old_value || empty($field['sub_fields']) ) return;
		
		
		// loop
		for( $i = 0; $i < $old_value; $i++ ) {
			
			$this->delete_row( $i, $field, $post_id );
			
		}
			
	}
	
	
	/*
	*  delete_field
	*
	*  description
	*
	*  @type	function
	*  @date	4/04/2014
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function delete_field( $field ) {
		
		// bail early if no sub fields
		if( empty($field['sub_fields']) ) return;
		
		
		// loop through sub fields
		foreach( $field['sub_fields'] as $sub_field ) {
		
			pdc_delete_field( $sub_field['ID'] );
			
		}
		
	}
	
	
	/*
	*  update_field()
	*
	*  This filter is appied to the $field before it is saved to the database
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field - the field array holding all the field options
	*  @param	$post_id - the field group ID (post_type = pdc)
	*
	*  @return	$field - the modified field
	*/

	function update_field( $field ) {
		
		// remove sub fields
		unset($field['sub_fields']);
		
				
		// return		
		return $field;
	}
	
	
	/*
	*  duplicate_field()
	*
	*  This filter is appied to the $field before it is duplicated and saved to the database
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field - the field array holding all the field options
	*
	*  @return	$field - the modified field
	*/

	function duplicate_field( $field ) {
		
		// get sub fields
		$sub_fields = pdc_extract_var( $field, 'sub_fields' );
		
		
		// save field to get ID
		$field = pdc_update_field( $field );
		
		
		// duplicate sub fields
		pdc_duplicate_fields( $sub_fields, $field['ID'] );
		
						
		// return		
		return $field;
	}
	
	
	/*
	*  translate_field
	*
	*  This function will translate field settings
	*
	*  @type	function
	*  @date	8/03/2016
	*  @since	5.3.2
	*
	*  @param	$field (array)
	*  @return	$field
	*/
	
	function translate_field( $field ) {
		
		// translate
		$field['button_label'] = pdc_translate( $field['button_label'] );
		
		
		// return
		return $field;
		
	}
	
	
	/*
	*  validate_any_field
	*
	*  This function will add compatibility for the 'column_width' setting
	*
	*  @type	function
	*  @date	30/1/17
	*  @since	5.5.6
	*
	*  @param	$field (array)
	*  @return	$field
	*/
	
	function validate_any_field( $field ) {
		
		// width has changed
		if( isset($field['column_width']) ) {
			
			$field['wrapper']['width'] = pdc_extract_var($field, 'column_width');
			
		}
		
		
		// return
		return $field;
		
	}
	
	
	/*
	*  prepare_field_for_export
	*
	*  description
	*
	*  @type	function
	*  @date	11/03/2014
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function prepare_field_for_export( $field ) {
		
		// bail early if no sub fields
		if( empty($field['sub_fields']) ) return $field;
		
		
		// prepare
		$field['sub_fields'] = pdc_prepare_fields_for_export( $field['sub_fields'] );
		
		
		// return
		return $field;
		
	}
	
	
	/*
	*  prepare_field_for_import
	*
	*  description
	*
	*  @type	function
	*  @date	11/03/2014
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function prepare_field_for_import( $field ) {
		
		// bail early if no sub fields
		if( empty($field['sub_fields']) ) return $field;
		
		
		// vars
		$sub_fields = $field['sub_fields'];
		
		
		// reset field setting
		$field['sub_fields'] = array();
		
		
		// loop
		foreach( $sub_fields as &$sub_field ) {
			
			$sub_field['parent'] = $field['key'];
			
		}
		
		
		// merge
		array_unshift($sub_fields, $field);
		
		
		// return
		return $sub_fields;
		
	}

}


// initialize
pdc_register_field_type( 'pdc_field_repeater' );

endif; // class_exists check

?>
