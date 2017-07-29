<?php

/*
*  ACF File Field Class
*
*  All the logic for this field type
*
*  @class 		pdc_field_file
*  @extends		pdc_field
*  @package		ACF
*  @subpackage	Fields
*/

if( ! class_exists('pdc_field_file') ) :

class pdc_field_file extends pdc_field {
	
	
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
	
	function __construct() {
		
		// vars
		$this->name = 'file';
		$this->label = __("File",'pdc');
		$this->category = 'content';
		$this->defaults = array(
			'return_format'	=> 'array',
			'library' 		=> 'all',
			'min_size'		=> 0,
			'max_size'		=> 0,
			'mime_types'	=> ''
		);
		$this->l10n = array(
			'select'		=> __("Select File",'pdc'),
			'edit'			=> __("Edit File",'pdc'),
			'update'		=> __("Update File",'pdc'),
			'uploadedTo'	=> __("Uploaded to this post",'pdc'),
		);
		
		
		// filters
		add_filter('get_media_item_args',			array($this, 'get_media_item_args'));
		
		
		// do not delete!
    	parent::__construct();
    	
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
		$uploader = pdc_get_setting('uploader');
		
		
		// enqueue
		if( $uploader == 'wp' ) {
			
			pdc_enqueue_uploader();
			
		}
		
		
		// vars
		$o = array(
			'icon'		=> '',
			'title'		=> '',
			'url'		=> '',
			'filesize'	=> '',
			'filename'	=> '',
		);
		
		$div = array(
			'class'				=> 'pdc-file-uploader pdc-cf',
			'data-library' 		=> $field['library'],
			'data-mime_types'	=> $field['mime_types'],
			'data-uploader'		=> $uploader
		);
		
		
		// has value?
		if( $field['value'] ) {
			
			$file = get_post( $field['value'] );
			
			if( $file ) {
				
				$o['icon'] = wp_mime_type_icon( $file->ID );
				$o['title']	= $file->post_title;
				$o['filesize'] = @size_format(filesize( get_attached_file( $file->ID ) ));
				$o['url'] = wp_get_attachment_url( $file->ID );
				
				$explode = explode('/', $o['url']);
				$o['filename'] = end( $explode );	
							
			}
			
			
			// url exists
			if( $o['url'] ) {
				
				$div['class'] .= ' has-value';
			
			}
						
		}
				
?>
<div <?php pdc_esc_attr_e($div); ?>>
	<?php pdc_hidden_input(array( 'name' => $field['name'], 'value' => $field['value'], 'data-name' => 'id' )); ?>
	<div class="show-if-value file-wrap pdc-soh">
		<div class="file-icon">
			<img data-name="icon" src="<?php echo $o['icon']; ?>" alt=""/>
		</div>
		<div class="file-info">
			<p>
				<strong data-name="title"><?php echo $o['title']; ?></strong>
			</p>
			<p>
				<strong><?php _e('File name', 'pdc'); ?>:</strong>
				<a data-name="filename" href="<?php echo $o['url']; ?>" target="_blank"><?php echo $o['filename']; ?></a>
			</p>
			<p>
				<strong><?php _e('File size', 'pdc'); ?>:</strong>
				<span data-name="filesize"><?php echo $o['filesize']; ?></span>
			</p>
			
			<ul class="pdc-hl pdc-soh-target">
				<?php if( $uploader != 'basic' ): ?>
					<li><a class="pdc-icon -pencil dark" data-name="edit" href="#"></a></li>
				<?php endif; ?>
				<li><a class="pdc-icon -cancel dark" data-name="remove" href="#"></a></li>
			</ul>
		</div>
	</div>
	<div class="hide-if-value">
		<?php if( $uploader == 'basic' ): ?>
			
			<?php if( $field['value'] && !is_numeric($field['value']) ): ?>
				<div class="pdc-error-message"><p><?php echo $field['value']; ?></p></div>
			<?php endif; ?>
			
			<label class="pdc-basic-uploader">
				<input type="file" name="<?php echo $field['name']; ?>" id="<?php echo $field['id']; ?>" />
			</label>
			
		<?php else: ?>
			
			<p style="margin:0;"><?php _e('No file selected','pdc'); ?> <a data-name="add" class="pdc-button button" href="#"><?php _e('Add File','pdc'); ?></a></p>
			
		<?php endif; ?>
		
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
			'min_size',
			'max_size'
		);
		
		foreach( $clear as $k ) {
			
			if( empty($field[$k]) ) {
				
				$field[$k] = '';
				
			}
			
		}
		
		
		// return_format
		pdc_render_field_setting( $field, array(
			'label'			=> __('Return Value','pdc'),
			'instructions'	=> __('Specify the returned value on front end','pdc'),
			'type'			=> 'radio',
			'name'			=> 'return_format',
			'layout'		=> 'horizontal',
			'choices'		=> array(
				'array'			=> __("File Array",'pdc'),
				'url'			=> __("File URL",'pdc'),
				'id'			=> __("File ID",'pdc')
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
			'instructions'	=> __('Restrict which files can be uploaded','pdc'),
			'type'			=> 'text',
			'name'			=> 'min_size',
			'prepend'		=> __('File size', 'pdc'),
			'append'		=> 'MB',
		));
		
		
		// max
		pdc_render_field_setting( $field, array(
			'label'			=> __('Maximum','pdc'),
			'instructions'	=> __('Restrict which files can be uploaded','pdc'),
			'type'			=> 'text',
			'name'			=> 'max_size',
			'prepend'		=> __('File size', 'pdc'),
			'append'		=> 'MB',
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
		
		
		// bail early if not numeric (error message)
		if( !is_numeric($value) ) return false;
		
		
		// convert to int
		$value = intval($value);
		
		
		// format
		if( $field['return_format'] == 'url' ) {
		
			return wp_get_attachment_url($value);
			
		} elseif( $field['return_format'] == 'array' ) {
			
			return pdc_get_attachment( $value );
		}
		
		
		// return
		return $value;
	}
	
	
	/*
	*  get_media_item_args
	*
	*  description
	*
	*  @type	function
	*  @date	27/01/13
	*  @since	3.6.0
	*
	*  @param	$vars (array)
	*  @return	$vars
	*/
	
	function get_media_item_args( $vars ) {
	
	    $vars['send'] = true;
	    return($vars);
	    
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
		
		// bail early if is empty
		if( empty($value) ) return false;
		
		
		// validate
		if( is_array($value) && isset($value['ID']) ) { 
			
			$value = $value['ID'];
			
		} elseif( is_object($value) && isset($value->ID) ) { 
			
			$value = $value->ID;
			
		}
		
		
		// bail early if not attachment ID
		if( !$value || !is_numeric($value) ) return false;
		
		
		// confirm type
		$value = (int) $value;
		
		
		// maybe connect attacment to post 
		pdc_connect_attachment_to_post( $value, $post_id );
		
		
		// return
		return $value;
		
	}
		
	
	
	/*
	*  validate_value
	*
	*  This function will validate a basic file input
	*
	*  @type	function
	*  @date	11/02/2014
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function validate_value( $valid, $value, $field, $input ){
		
		// bail early if empty		
		if( empty($value) ) return $valid;
		
		
		// bail ealry if is numeric
		if( is_numeric($value) ) return $valid;
		
		
		// bail ealry if not basic string
		if( !is_string($value) ) return $valid;
		
		
		// decode value
		$file = null;
		parse_str($value, $file);
		
		
		// bail early if no attachment
		if( empty($file) ) return $valid;
		
		
		// get errors
		$errors = pdc_validate_attachment( $file, $field, 'basic_upload' );
		
		
		// append error
		if( !empty($errors) ) {
			
			$valid = implode("\n", $errors);
			
		}
		
		
		// return		
		return $valid;
		
	}
	
}


// initialize
pdc_register_field_type( new pdc_field_file() );

endif; // class_exists check

?>