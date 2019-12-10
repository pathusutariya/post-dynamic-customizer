<?php

// global
global $field_group;
		
		
// active
pdc_render_field_wrap(array(
	'label'			=> __('Active','pdc'),
	'instructions'	=> '',
	'type'			=> 'true_false',
	'name'			=> 'active',
	'prefix'		=> 'pdc_field_group',
	'value'			=> $field_group['active'],
	'ui'			=> 1,
	//'ui_on_text'	=> __('Active', 'pdc'),
	//'ui_off_text'	=> __('Inactive', 'pdc'),
));


// style
pdc_render_field_wrap(array(
	'label'			=> __('Style','pdc'),
	'instructions'	=> '',
	'type'			=> 'select',
	'name'			=> 'style',
	'prefix'		=> 'pdc_field_group',
	'value'			=> $field_group['style'],
	'choices' 		=> array(
		'default'			=>	__("Standard (WP metabox)",'pdc'),
		'seamless'			=>	__("Seamless (no metabox)",'pdc'),
	)
));


// position
pdc_render_field_wrap(array(
	'label'			=> __('Position','pdc'),
	'instructions'	=> '',
	'type'			=> 'select',
	'name'			=> 'position',
	'prefix'		=> 'pdc_field_group',
	'value'			=> $field_group['position'],
	'choices' 		=> array(
		'pdc_after_title'	=> __("High (after title)",'pdc'),
		'normal'			=> __("Normal (after content)",'pdc'),
		'side' 				=> __("Side",'pdc'),
	),
	'default_value'	=> 'normal'
));


// label_placement
pdc_render_field_wrap(array(
	'label'			=> __('Label placement','pdc'),
	'instructions'	=> '',
	'type'			=> 'select',
	'name'			=> 'label_placement',
	'prefix'		=> 'pdc_field_group',
	'value'			=> $field_group['label_placement'],
	'choices' 		=> array(
		'top'			=>	__("Top aligned",'pdc'),
		'left'			=>	__("Left aligned",'pdc'),
	)
));


// instruction_placement
pdc_render_field_wrap(array(
	'label'			=> __('Instruction placement','pdc'),
	'instructions'	=> '',
	'type'			=> 'select',
	'name'			=> 'instruction_placement',
	'prefix'		=> 'pdc_field_group',
	'value'			=> $field_group['instruction_placement'],
	'choices' 		=> array(
		'label'		=>	__("Below labels",'pdc'),
		'field'		=>	__("Below fields",'pdc'),
	)
));


// menu_order
pdc_render_field_wrap(array(
	'label'			=> __('Order No.','pdc'),
	'instructions'	=> __('Field groups with a lower order will appear first','pdc'),
	'type'			=> 'number',
	'name'			=> 'menu_order',
	'prefix'		=> 'pdc_field_group',
	'value'			=> $field_group['menu_order'],
));


// description
pdc_render_field_wrap(array(
	'label'			=> __('Description','pdc'),
	'instructions'	=> __('Shown in field group list','pdc'),
	'type'			=> 'text',
	'name'			=> 'description',
	'prefix'		=> 'pdc_field_group',
	'value'			=> $field_group['description'],
));


// hide on screen
$choices = array(
	'permalink'			=>	__("Permalink", 'pdc'),
	'the_content'		=>	__("Content Editor",'pdc'),
	'excerpt'			=>	__("Excerpt", 'pdc'),
	'custom_fields'		=>	__("Custom Fields", 'pdc'),
	'discussion'		=>	__("Discussion", 'pdc'),
	'comments'			=>	__("Comments", 'pdc'),
	'revisions'			=>	__("Revisions", 'pdc'),
	'slug'				=>	__("Slug", 'pdc'),
	'author'			=>	__("Author", 'pdc'),
	'format'			=>	__("Format", 'pdc'),
	'page_attributes'	=>	__("Page Attributes", 'pdc'),
	'featured_image'	=>	__("Featured Image", 'pdc'),
	'categories'		=>	__("Categories", 'pdc'),
	'tags'				=>	__("Tags", 'pdc'),
	'send-trackbacks'	=>	__("Send Trackbacks", 'pdc'),
);
if( pdc_get_setting('remove_wp_meta_box') ) {
	unset( $choices['custom_fields'] );	
}

pdc_render_field_wrap(array(
	'label'			=> __('Hide on screen','pdc'),
	'instructions'	=> __('<b>Select</b> items to <b>hide</b> them from the edit screen.','pdc') . '<br /><br />' . __("If multiple field groups appear on an edit screen, the first field group's options will be used (the one with the lowest order number)",'pdc'),
	'type'			=> 'checkbox',
	'name'			=> 'hide_on_screen',
	'prefix'		=> 'pdc_field_group',
	'value'			=> $field_group['hide_on_screen'],
	'toggle'		=> true,
	'choices' 		=> $choices
));


// 3rd party settings
do_action('pdc/render_field_group_settings', $field_group);
		
?>
<div class="pdc-hidden">
	<input type="hidden" name="pdc_field_group[key]" value="<?php echo $field_group['key']; ?>" />
</div>
<script type="text/javascript">
if( typeof pdc !== 'undefined' ) {
		
	pdc.newPostbox({
		'id': 'pdc-field-group-options',
		'label': 'left'
	});	

}
</script>