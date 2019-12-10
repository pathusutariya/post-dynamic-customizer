<?php

if( ! class_exists('pdc_field_gallery') ) :

class pdc_field_gallery extends pdc_field {
	
	
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
		$this->name = 'gallery';
		$this->label = __("Gallery",'pdc');
		$this->category = 'content';
		$this->defaults = array(
			'library'		=> 'all',
			'min'			=> 0,
			'max'			=> 0,
			'min_width'		=> 0,
			'min_height'	=> 0,
			'min_size'		=> 0,
			'max_width'		=> 0,
			'max_height'	=> 0,
			'max_size'		=> 0,
			'mime_types'	=> '',
			'insert'		=> 'append'
		);
		
		
		// actions
		add_action('wp_ajax_pdc/fields/gallery/get_attachment',				array($this, 'ajax_get_attachment'));
		add_action('wp_ajax_nopriv_pdc/fields/gallery/get_attachment',		array($this, 'ajax_get_attachment'));
		
		add_action('wp_ajax_pdc/fields/gallery/update_attachment',			array($this, 'ajax_update_attachment'));
		add_action('wp_ajax_nopriv_pdc/fields/gallery/update_attachment',	array($this, 'ajax_update_attachment'));
		
		add_action('wp_ajax_pdc/fields/gallery/get_sort_order',				array($this, 'ajax_get_sort_order'));
		add_action('wp_ajax_nopriv_pdc/fields/gallery/get_sort_order',		array($this, 'ajax_get_sort_order'));
		
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
		   	'Add Image to Gallery'		=> __('Add Image to Gallery', 'pdc'),
			'Maximum selection reached'	=> __('Maximum selection reached', 'pdc'),
	   	));
	}
	
	
	/*
	*  ajax_get_attachment
	*
	*  description
	*
	*  @type	function
	*  @date	13/12/2013
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function ajax_get_attachment() {
	
		// options
   		$options = pdc_parse_args( $_POST, array(
			'post_id'		=> 0,
			'attachment'	=> 0,
			'id'			=> 0,
			'field_key'		=> '',
			'nonce'			=> '',
		));
   		
		
		// validate
		if( !wp_verify_nonce($options['nonce'], 'pdc_nonce') ) die();
		
		
		// bail early if no id
		if( !$options['id'] ) die();
		
		
		// load field
		$field = pdc_get_field( $options['field_key'] );
		
		
		// bali early if no field
		if( !$field ) die();
		
		
		// render
		$this->render_attachment( $options['id'], $field );
		die;
		
	}
	
	
	/*
	*  ajax_update_attachment
	*
	*  description
	*
	*  @type	function
	*  @date	13/12/2013
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function ajax_update_attachment() {
		
		// validate nonce
		if( !wp_verify_nonce($_POST['nonce'], 'pdc_nonce') ) {
		
			wp_send_json_error();
			
		}
		
		
		// bail early if no attachments
		if( empty($_POST['attachments']) ) {
		
			wp_send_json_error();
			
		}
		
		
		// loop over attachments
		foreach( $_POST['attachments'] as $id => $changes ) {
			
			if ( !current_user_can( 'edit_post', $id ) )
				wp_send_json_error();
				
			$post = get_post( $id, ARRAY_A );
		
			if ( 'attachment' != $post['post_type'] )
				wp_send_json_error();
		
			if ( isset( $changes['title'] ) )
				$post['post_title'] = $changes['title'];
		
			if ( isset( $changes['caption'] ) )
				$post['post_excerpt'] = $changes['caption'];
		
			if ( isset( $changes['description'] ) )
				$post['post_content'] = $changes['description'];
		
			if ( isset( $changes['alt'] ) ) {
				$alt = wp_unslash( $changes['alt'] );
				if ( $alt != get_post_meta( $id, '_wp_attachment_image_alt', true ) ) {
					$alt = wp_strip_all_tags( $alt, true );
					update_post_meta( $id, '_wp_attachment_image_alt', wp_slash( $alt ) );
				}
			}
			
			
			// save post
			wp_update_post( $post );
			
			
			/** This filter is documented in wp-admin/includes/media.php */
			// - seems off to run this filter AFTER the update_post function, but there is a reason
			// - when placed BEFORE, an empty post_title will be populated by WP
			// - this filter will still allow 3rd party to save extra image data!
			$post = apply_filters( 'attachment_fields_to_save', $post, $changes );
			
			
			// save meta
			pdc_save_post( $id );
						
		}
		
		
		// return
		wp_send_json_success();
			
	}
	
	
	/*
	*  ajax_get_sort_order
	*
	*  description
	*
	*  @type	function
	*  @date	13/12/2013
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function ajax_get_sort_order() {
		
		// vars
		$r = array();
		$order = 'DESC';
   		$args = pdc_parse_args( $_POST, array(
			'ids'			=> 0,
			'sort'			=> 'date',
			'field_key'		=> '',
			'nonce'			=> '',
		));
		
		
		// validate
		if( ! wp_verify_nonce($args['nonce'], 'pdc_nonce') ) {
		
			wp_send_json_error();
			
		}
		
		
		// reverse
		if( $args['sort'] == 'reverse' ) {
		
			$ids = array_reverse($args['ids']);
			
			wp_send_json_success($ids);
			
		}
		
		
		if( $args['sort'] == 'title' ) {
			
			$order = 'ASC';
			
		}
		
		
		// find attachments (DISTINCT POSTS)
		$ids = get_posts(array(
			'post_type'		=> 'attachment',
			'numberposts'	=> -1,
			'post_status'	=> 'any',
			'post__in'		=> $args['ids'],
			'order'			=> $order,
			'orderby'		=> $args['sort'],
			'fields'		=> 'ids'		
		));
		
		
		// success
		if( !empty($ids) ) {
		
			wp_send_json_success($ids);
			
		}
		
		
		// failure
		wp_send_json_error();
		
	}
	
	
	/*
	*  render_attachment
	*
	*  description
	*
	*  @type	function
	*  @date	13/12/2013
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function render_attachment( $id = 0, $field ) {
		
		// vars
		$attachment = wp_prepare_attachment_for_js( $id );
		$compat = get_compat_media_markup( $id );
		$compat = $compat['item'];
		$prefix = 'attachments[' . $id . ']';
		$thumb = '';
		$dimentions = '';
		
		
		// thumb
		if( isset($attachment['thumb']['src']) ) {
			
			// video
			$thumb = $attachment['thumb']['src'];
			
		} elseif( isset($attachment['sizes']['thumbnail']['url']) ) {
			
			// image
			$thumb = $attachment['sizes']['thumbnail']['url'];
			
		} elseif( $attachment['type'] === 'image' ) {
			
			// svg
			$thumb = $attachment['url'];
			
		} else {
			
			// fallback (perhaps attachment does not exist)
			$thumb = wp_mime_type_icon();
				
		}
		
		
		// dimentions
		if( $attachment['type'] === 'audio' ) {
			
			$dimentions = __('Length', 'pdc') . ': ' . $attachment['fileLength'];
			
		} elseif( !empty($attachment['width']) ) {
			
			$dimentions = $attachment['width'] . ' x ' . $attachment['height'];
			
		}
		
		if( !empty($attachment['filesizeHumanReadable']) ) {
			
			$dimentions .=  ' (' . $attachment['filesizeHumanReadable'] . ')';
			
		}
		
		?>
		<div class="pdc-gallery-side-info">
			<img src="<?php echo $thumb; ?>" alt="<?php echo $attachment['alt']; ?>" />
			<p class="filename"><strong><?php echo $attachment['filename']; ?></strong></p>
			<p class="uploaded"><?php echo $attachment['dateFormatted']; ?></p>
			<p class="dimensions"><?php echo $dimentions; ?></p>
			<p class="actions">
				<a href="#" class="pdc-gallery-edit" data-id="<?php echo $id; ?>"><?php _e('Edit', 'pdc'); ?></a>
				<a href="#" class="pdc-gallery-remove" data-id="<?php echo $id; ?>"><?php _e('Remove', 'pdc'); ?></a>
			</p>
		</div>
		<table class="form-table">
			<tbody>
				<?php 
				
				pdc_render_field_wrap(array(
					//'key'		=> "{$field['key']}-title",
					'name'		=> 'title',
					'prefix'	=> $prefix,
					'type'		=> 'text',
					'label'		=> __('Title', 'pdc'),
					'value'		=> $attachment['title']
				), 'tr');
				
				pdc_render_field_wrap(array(
					//'key'		=> "{$field['key']}-caption",
					'name'		=> 'caption',
					'prefix'	=> $prefix,
					'type'		=> 'textarea',
					'label'		=> __('Caption', 'pdc'),
					'value'		=> $attachment['caption']
				), 'tr');
				
				pdc_render_field_wrap(array(
					//'key'		=> "{$field['key']}-alt",
					'name'		=> 'alt',
					'prefix'	=> $prefix,
					'type'		=> 'text',
					'label'		=> __('Alt Text', 'pdc'),
					'value'		=> $attachment['alt']
				), 'tr');
				
				pdc_render_field_wrap(array(
					//'key'		=> "{$field['key']}-description",
					'name'		=> 'description',
					'prefix'	=> $prefix,
					'type'		=> 'textarea',
					'label'		=> __('Description', 'pdc'),
					'value'		=> $attachment['description']
				), 'tr');
				
				?>
			</tbody>
		</table>
		<?php
		
		echo $compat;
		
	}
	
	
	/*
	*  get_attachments
	*
	*  This function will return an array of attachments for a given field value
	*
	*  @type	function
	*  @date	13/06/2014
	*  @since	5.0.0
	*
	*  @param	$value (array)
	*  @return	$value
	*/
	
	function get_attachments( $value ) {
		
		// bail early if no value
		if( empty($value) ) return false;
		
		
		// force value to array
		$post__in = pdc_get_array( $value );
		
		
		// get posts
		$posts = pdc_get_posts(array(
			'post_type'	=> 'attachment',
			'post__in'	=> $post__in
		));
		
		
		// return
		return $posts;
				
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
		
		// enqueue
		pdc_enqueue_uploader();
		
		
		// vars
		$atts = array(
			'id'				=> $field['id'],
			'class'				=> "pdc-gallery {$field['class']}",
			'data-library'		=> $field['library'],
			'data-min'			=> $field['min'],
			'data-max'			=> $field['max'],
			'data-mime_types'	=> $field['mime_types'],
			'data-insert'		=> $field['insert'],
			'data-columns'		=> 4
		);
		
		
		// set gallery height
		$height = pdc_get_user_setting('gallery_height', 400);
		$height = max( $height, 200 ); // minimum height is 200
		$atts['style'] = "height:{$height}px";
		
		
		// get posts
		$value = $this->get_attachments( $field['value'] );
		
		?>
<div <?php pdc_esc_attr_e($atts); ?>>
	
	<div class="pdc-hidden">
		<?php pdc_hidden_input(array( 'name' => $field['name'], 'value' => '' )); ?>
	</div>
	
	<div class="pdc-gallery-main">
		
		<div class="pdc-gallery-attachments">
			
			<?php if( $value ): ?>
			
				<?php foreach( $value as $i => $v ): 
					
					// bail early if no value
					if( !$v ) continue;
					
					
					// vars
					$a = array(
						'ID' 		=> $v->ID,
						'title'		=> $v->post_title,
						'filename'	=> wp_basename($v->guid),
						'type'		=> pdc_maybe_get(explode('/', $v->post_mime_type), 0),
						'class'		=> 'pdc-gallery-attachment'
					);
					
					
					// thumbnail
					$thumbnail = pdc_get_post_thumbnail($a['ID'], 'medium');
					
					
					// remove filename if is image
					if( $a['type'] == 'image' ) $a['filename'] = '';
					
					
					// class
					$a['class'] .= ' -' . $a['type'];
					
					if( $thumbnail['type'] == 'icon' ) {
						
						$a['class'] .= ' -icon';
						
					}
					
					
					?>
					<div class="<?php echo $a['class']; ?>" data-id="<?php echo $a['ID']; ?>">
						<?php pdc_hidden_input(array( 'name' => $field['name'].'[]', 'value' => $a['ID'] )); ?>
						<div class="margin">
							<div class="thumbnail">
								<img src="<?php echo $thumbnail['url']; ?>" alt="" title="<?php echo $a['title']; ?>"/>
							</div>
							<?php if( $a['filename'] ): ?>
							<div class="filename"><?php echo pdc_get_truncated($a['filename'], 30); ?></div>	
							<?php endif; ?>
						</div>
						<div class="actions">
							<a class="pdc-icon -cancel dark pdc-gallery-remove" href="#" data-id="<?php echo $a['ID']; ?>" title="<?php _e('Remove', 'pdc'); ?>"></a>
						</div>
					</div>
				<?php endforeach; ?>
				
			<?php endif; ?>
			
		</div>
		
		<div class="pdc-gallery-toolbar">
			
			<ul class="pdc-hl">
				<li>
					<a href="#" class="pdc-button button button-primary pdc-gallery-add"><?php _e('Add to gallery', 'pdc'); ?></a>
				</li>
				<li class="pdc-fr">
					<select class="pdc-gallery-sort">
						<option value=""><?php _e('Bulk actions', 'pdc'); ?></option>
						<option value="date"><?php _e('Sort by date uploaded', 'pdc'); ?></option>
						<option value="modified"><?php _e('Sort by date modified', 'pdc'); ?></option>
						<option value="title"><?php _e('Sort by title', 'pdc'); ?></option>
						<option value="reverse"><?php _e('Reverse current order', 'pdc'); ?></option>
					</select>
				</li>
			</ul>
			
		</div>
		
	</div>
	
	<div class="pdc-gallery-side">
	<div class="pdc-gallery-side-inner">
			
		<div class="pdc-gallery-side-data"></div>
						
		<div class="pdc-gallery-toolbar">
			
			<ul class="pdc-hl">
				<li>
					<a href="#" class="pdc-button button pdc-gallery-close"><?php _e('Close', 'pdc'); ?></a>
				</li>
				<li class="pdc-fr">
					<a class="pdc-button button button-primary pdc-gallery-update" href="#"><?php _e('Update', 'pdc'); ?></a>
				</li>
			</ul>
			
		</div>
		
	</div>	
	</div>
	
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
		
		// clear numeric settings
		$clear = array(
			'min',
			'max',
			'min_width',
			'min_height',
			'min_size',
			'max_width',
			'max_height',
			'max_size'
		);
		
		foreach( $clear as $k ) {
			
			if( empty($field[$k]) ) $field[$k] = '';
			
		}
		
		
		// min
		pdc_render_field_setting( $field, array(
			'label'			=> __('Minimum Selection','pdc'),
			'instructions'	=> '',
			'type'			=> 'number',
			'name'			=> 'min'
		));
		
		
		// max
		pdc_render_field_setting( $field, array(
			'label'			=> __('Maximum Selection','pdc'),
			'instructions'	=> '',
			'type'			=> 'number',
			'name'			=> 'max'
		));
		
		
		// insert
		pdc_render_field_setting( $field, array(
			'label'			=> __('Insert','pdc'),
			'instructions'	=> __('Specify where new attachments are added','pdc'),
			'type'			=> 'select',
			'name'			=> 'insert',
			'choices' 		=> array(
				'append'		=> __('Append to the end', 'pdc'),
				'prepend'		=> __('Prepend to the beginning', 'pdc')
			)
		));
		
		
		// library
		pdc_render_field_setting( $field, array(
			'label'			=> __('Library','pdc'),
			'instructions'	=> __('Limit the media library choice','pdc'),
			'type'			=> 'radio',
			'name'			=> 'library',
			'layout'		=> 'horizontal',
			'choices' 		=> array(
				'all'			=> __('All', 'pdc'),
				'uploadedTo'	=> __('Uploaded to post', 'pdc')
			)
		));
		
		
		// min
		pdc_render_field_setting( $field, array(
			'label'			=> __('Minimum','pdc'),
			'instructions'	=> __('Restrict which images can be uploaded','pdc'),
			'type'			=> 'text',
			'name'			=> 'min_width',
			'prepend'		=> __('Width', 'pdc'),
			'append'		=> 'px',
		));
		
		pdc_render_field_setting( $field, array(
			'label'			=> '',
			'type'			=> 'text',
			'name'			=> 'min_height',
			'prepend'		=> __('Height', 'pdc'),
			'append'		=> 'px',
			'_append' 		=> 'min_width'
		));
		
		pdc_render_field_setting( $field, array(
			'label'			=> '',
			'type'			=> 'text',
			'name'			=> 'min_size',
			'prepend'		=> __('File size', 'pdc'),
			'append'		=> 'MB',
			'_append' 		=> 'min_width'
		));	
		
		
		// max
		pdc_render_field_setting( $field, array(
			'label'			=> __('Maximum','pdc'),
			'instructions'	=> __('Restrict which images can be uploaded','pdc'),
			'type'			=> 'text',
			'name'			=> 'max_width',
			'prepend'		=> __('Width', 'pdc'),
			'append'		=> 'px',
		));
		
		pdc_render_field_setting( $field, array(
			'label'			=> '',
			'type'			=> 'text',
			'name'			=> 'max_height',
			'prepend'		=> __('Height', 'pdc'),
			'append'		=> 'px',
			'_append' 		=> 'max_width'
		));
		
		pdc_render_field_setting( $field, array(
			'label'			=> '',
			'type'			=> 'text',
			'name'			=> 'max_size',
			'prepend'		=> __('File size', 'pdc'),
			'append'		=> 'MB',
			'_append' 		=> 'max_width'
		));	
		
		
		// allowed type
		pdc_render_field_setting( $field, array(
			'label'			=> __('Allowed file types','pdc'),
			'instructions'	=> __('Comma separated list. Leave blank for all types','pdc'),
			'type'			=> 'text',
			'name'			=> 'mime_types',
		));
		
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
		
		
		// get posts
		$posts = $this->get_attachments($value);
		
		
		// update value to include $post
		foreach( array_keys($posts) as $i ) {
			
			$posts[ $i ] = pdc_get_attachment( $posts[ $i ] );
			
		}
				
		
		// return
		return $posts;
		
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
		
		if( empty($value) || !is_array($value) ) {
		
			$value = array();
			
		}
		
		
		if( count($value) < $field['min'] ) {
		
			$valid = _n( '%s requires at least %s selection', '%s requires at least %s selections', $field['min'], 'pdc' );
			$valid = sprintf( $valid, $field['label'], $field['min'] );
			
		}
		
				
		return $valid;
		
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
	*  @param	$post_id - the $post_id of which the value will be saved
	*  @param	$field - the field array holding all the field options
	*
	*  @return	$value - the modified value
	*/
	
	function update_value( $value, $post_id, $field ) {
		
		// bail early if no value
		if( empty($value) || !is_array($value) ) return false;
		
		
		// loop
		foreach( $value as $i => $v ) {
			
			$value[ $i ] = $this->update_single_value( $v );
			
		}
				
		
		// return
		return $value;
		
	}
	
	
	/*
	*  update_single_value()
	*
	*  This filter is appied to the $value before it is updated in the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value - the value which will be saved in the database
	*  @param	$post_id - the $post_id of which the value will be saved
	*  @param	$field - the field array holding all the field options
	*
	*  @return	$value - the modified value
	*/
	
	function update_single_value( $value ) {
		
		// numeric
		if( is_numeric($value) ) return $value;
		
		
		// array?
		if( is_array($value) && isset($value['ID']) ) return $value['ID'];
		
		
		// object?
		if( is_object($value) && isset($value->ID) ) return $value->ID;
		
		
		// return
		return $value;
		
	}

	
}


// initialize
pdc_register_field_type( 'pdc_field_gallery' );

endif; // class_exists check

?>