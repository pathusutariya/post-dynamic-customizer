<?php 

// vars
$field = false;
$i = 0;


// extract args
extract( $args );


// add prefix
$field['prefix'] = "pdc_fields[{$field['ID']}]";


// vars
$atts = array(
	'class' => "pdc-field-object pdc-field-object-{$field['type']}",
	'data-id'	=> $field['ID'],
	'data-key'	=> $field['key'],
	'data-type'	=> $field['type'],
);

$meta = array(
	'ID'			=> $field['ID'],
	'key'			=> $field['key'],
	'parent'		=> $field['parent'],
	'menu_order'	=> $field['menu_order'],
	'save'			=> '',
);


// replace
$atts['class'] = str_replace('_', '-', $atts['class']);

?>
<div <?php echo pdc_esc_attr( $atts ); ?>>
	
	<div class="meta">
		<?php foreach( $meta as $k => $v ):
			
			pdc_hidden_input(array( 'class' => "input-{$k}", 'name' => "{$field['prefix']}[{$k}]", 'value' => $v ));
				
		endforeach; ?>
	</div>
	
	<div class="handle">
		<ul class="pdc-hl pdc-tbody">
			<li class="li-field-order">
				<span class="pdc-icon pdc-sortable-handle" title="<?php _e('Drag to reorder','pdc'); ?>"><?php echo ($i + 1); ?></span>
				<pre class="pre-field-key"><?php echo $field['key']; ?></pre>
			</li>
			<li class="li-field-label">
				<strong>
					<a class="edit-field" title="<?php _e("Edit field",'pdc'); ?>" href="#"><?php echo pdc_get_field_label($field); ?></a>
				</strong>
				<div class="row-options">
					<a class="edit-field" title="<?php _e("Edit field",'pdc'); ?>" href="#"><?php _e("Edit",'pdc'); ?></a>
					<a class="duplicate-field" title="<?php _e("Duplicate field",'pdc'); ?>" href="#"><?php _e("Duplicate",'pdc'); ?></a>
					<a class="move-field" title="<?php _e("Move field to another group",'pdc'); ?>" href="#"><?php _e("Move",'pdc'); ?></a>
					<a class="delete-field" title="<?php _e("Delete field",'pdc'); ?>" href="#"><?php _e("Delete",'pdc'); ?></a>
				</div>
			</li>
			<li class="li-field-name"><?php echo $field['name']; ?></li>
			<li class="li-field-type">
				<?php if( pdc_field_type_exists($field['type']) ): ?>
					<?php echo pdc_get_field_type_label($field['type']); ?>
				<?php else: ?>
					<b><?php _e('Error', 'pdc'); ?></b> <?php _e('Field type does not exist', 'pdc'); ?>
				<?php endif; ?>
			</li>	
		</ul>
	</div>
	
	<div class="settings">			
		<table class="pdc-table">
			<tbody>
				<?php 
				
				// label
				pdc_render_field_setting($field, array(
					'label'			=> __('Field Label','pdc'),
					'instructions'	=> __('This is the name which will appear on the EDIT page','pdc'),
					'name'			=> 'label',
					'type'			=> 'text',
					'required'		=> 1,
					'class'			=> 'field-label'
				), true);
				
				
				// name
				pdc_render_field_setting($field, array(
					'label'			=> __('Field Name','pdc'),
					'instructions'	=> __('Single word, no spaces. Underscores and dashes allowed','pdc'),
					'name'			=> 'name',
					'type'			=> 'text',
					'required'		=> 1,
					'class'			=> 'field-name'
				), true);
				
				
				// type
				pdc_render_field_setting($field, array(
					'label'			=> __('Field Type','pdc'),
					'instructions'	=> '',
					'required'		=> 1,
					'type'			=> 'select',
					'name'			=> 'type',
					'choices' 		=> pdc_get_grouped_field_types(),
					'class'			=> 'field-type'
				), true);
				
				
				// instructions
				pdc_render_field_setting($field, array(
					'label'			=> __('Instructions','pdc'),
					'instructions'	=> __('Instructions for authors. Shown when submitting data','pdc'),
					'type'			=> 'textarea',
					'name'			=> 'instructions',
					'rows'			=> 5
				), true);
				
				
				// required
				pdc_render_field_setting($field, array(
					'label'			=> __('Required?','pdc'),
					'instructions'	=> '',
					'type'			=> 'true_false',
					'name'			=> 'required',
					'ui'			=> 1,
					'class'			=> 'field-required'
				), true);
				
				
				// 3rd party settings
				do_action('pdc/render_field_settings', $field);
				
				
				// type specific settings
				do_action("pdc/render_field_settings/type={$field['type']}", $field);
				
				
				// conditional logic
				pdc_get_view('field-group-field-conditional-logic', array( 'field' => $field ));
				
				
				// wrapper
				pdc_render_field_wrap(array(
					'label'			=> __('Wrapper Attributes','pdc'),
					'instructions'	=> '',
					'type'			=> 'text',
					'name'			=> 'width',
					'prefix'		=> $field['prefix'] . '[wrapper]',
					'value'			=> $field['wrapper']['width'],
					'prepend'		=> __('width', 'pdc'),
					'append'		=> '%',
					'wrapper'		=> array(
						'data-name' => 'wrapper',
						'class' => 'pdc-field-setting-wrapper'
					)
				), 'tr');
				
				pdc_render_field_wrap(array(
					'label'			=> '',
					'instructions'	=> '',
					'type'			=> 'text',
					'name'			=> 'class',
					'prefix'		=> $field['prefix'] . '[wrapper]',
					'value'			=> $field['wrapper']['class'],
					'prepend'		=> __('class', 'pdc'),
					'wrapper'		=> array(
						'data-append' => 'wrapper'
					)
				), 'tr');
				
				pdc_render_field_wrap(array(
					'label'			=> '',
					'instructions'	=> '',
					'type'			=> 'text',
					'name'			=> 'id',
					'prefix'		=> $field['prefix'] . '[wrapper]',
					'value'			=> $field['wrapper']['id'],
					'prepend'		=> __('id', 'pdc'),
					'wrapper'		=> array(
						'data-append' => 'wrapper'
					)
				), 'tr');
				
				?>
				<tr class="pdc-field pdc-field-save">
					<td class="pdc-label"></td>
					<td class="pdc-input">
						<ul class="pdc-hl">
							<li>
								<a class="button edit-field" title="<?php _e("Close Field",'pdc'); ?>" href="#"><?php _e("Close Field",'pdc'); ?></a>
							</li>
						</ul>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	
</div>