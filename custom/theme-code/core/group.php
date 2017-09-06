<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class for field group functionality
 */
class PDCTC_Group {

	// field group id
	private $id;

	/**
	 * array of all fields in field group
	 *
	 * if using postmeta table this will be an array of post meta objects
	 * if using posts table this will be and array of Post objects
	 */
	public $fields;

	/**
	 * nesting level
	 *
	 * 0 = not nested inside another field
	 * 1 = nested one level deep inside another field eg. repeater
	 * 2 = nested two levels deep inside other fields etc
	 */
	public $nesting_level;

	// theme code indent for the field group
	public $indent_count;

	// field location (for options panel etc)
	public $location;


	/**
	 * Constructor for field group
	 *
	 * @param $field_group_id	int
	 * @param $nesting_level	int
	 * @param $indent_count		int
	 * @param $location			string
	 */
	function __construct( $field_group_id, $nesting_level = 0, $indent_count = 0, $location = '' ) {

		if ( !empty( $field_group_id ) ) {

			$this->id = $field_group_id;
			$this->fields = $this->get_fields();
			$this->nesting_level = $nesting_level;
			$this->indent_count = $indent_count;
			$this->location = $location;

		}

	}


	/**
	* Get all the fields in the field group.
	*
	* @return array of all fields (post objects) in the field group
	*/
	private function get_fields() {

		if ( 'postmeta' == pdcTC_Core::$db_table ) { // pdc
			return $this->get_fields_from_postmeta_table();
		 } elseif ( 'posts' == pdcTC_Core::$db_table ) { // pdc PRO
			return $this->get_fields_from_posts_table();
		}

	}


	/**
	* Get fields from postmeta table
	*
	* @return array of all fields (post meta objects) in the field group
	*/
	private function get_fields_from_postmeta_table() {

		global $wpdb;

		// get table prefix
		$postmeta_table_name = $wpdb->prefix . 'postmeta';

		// query postmeta table for fields in this field group
		$fields = $wpdb->get_results( "SELECT * FROM " . $postmeta_table_name . " WHERE post_id = " . $this->id . " AND meta_key LIKE 'field_%'" );

		return $fields;

	}


	/**
	* Get fields from posts table
	*
	* @return array of all fields (post objects) in the field group
	*/
	private function get_fields_from_posts_table() {

		// wp query args for all pdc fields for this field group
		$query_args = array(
			'post_type' => 'pdc-field',
			'post_parent' => $this->id,
			'posts_per_page' => '-1',
			'orderby' => 'menu_order',
			'order' => 'ASC',
		);

		$fields_query = new WP_Query( $query_args );

		return $fields_query->posts;

	}


	/**
	 * Render theme PHP for all fields in field group
	 */
	public function render_field_group() {

		// pdc - create, sort and render fields
		if ( 'postmeta' == pdcTC_Core::$db_table ) {

			// create an array of pdcTC_Field objects
			$pdctc_fields = array();

			foreach ( $this->fields as $field ) {

				$pdctc_field = new pdcTC_Field(	$this->nesting_level,
												$this->indent_count,
												$this->location,
												$field
												);

				array_push( $pdctc_fields, $pdctc_field );

			}

			// sort fields
			usort( $pdctc_fields, array( $this, "compare_field_order") );

			// render fields
			foreach ( $pdctc_fields as $pdctc_field ) {
				$pdctc_field->render_field();
			}

		 }

		// pdc PRO - create and render fields (no sorting required)
		elseif ( 'posts' == pdcTC_Core::$db_table ) {

			// create and render pdcTC_Field objects
			foreach ( $this->fields as $field ) {

				$pdctc_field = new pdcTC_Field(	$this->nesting_level,
												$this->indent_count,
												$this->location,
												$field
												);
				$pdctc_field->render_field();

			}

		}

	}

	/**
	 * Field order number comparion, used by usort() in render_field_group()
	 */
	private function compare_field_order( $a, $b ) {

		return $a->settings['order_no'] > $b->settings['order_no'];

	}

}